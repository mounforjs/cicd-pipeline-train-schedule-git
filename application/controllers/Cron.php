<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once __DIR__ . '/Email.php';

class Cron extends CI_Controller {

	public function __construct () {
		parent::__construct();

        $this->load->model('user_model');
        $this->load->model('cron_model');
        $this->load->model('notification_model');

		check_login(); //for testing, should never be accessible
	}

	public function makeGamesLive($startpoint=null) {
		$numUpdated = 0;

		//get all games that need to be published
		$games = $this->cron_model->getPublishGames($startpoint); 

		//update each game to be live
		foreach ($games as $game) {
			$this->db->trans_start();

			$publishDate = DateTime::createFromFormat('Y-m-d H:i:s', $game["Publish_Date"]);
			$publish = isDatePassed($publishDate);
			if ($publish) {
				$this->cron_model->makeGameStatusLive($game["id"]); 
			
				if ($this->db->affected_rows() > 0) { //if game is successfully made live, notify
					$numUpdated++;
					
					$emailData = $this->cron_model->getEmailInfo($game["user_id"], $game["id"]);

					$notes = "Your game, " . $game["name"] . ", is now live!";
					$body = $this->load->view('emails/game-live-email', $emailData, true);

					sendNotificationAndEmail("game_live", $game["user_id"], $game["user_id"], $notes, "game", "publish", $game["id"], $game["charity_id"], $notes, $body, $publishDate->format("Y-m-d H:i:s"));
				}
			}

			$this->db->trans_complete();
		}

		echo json_encode($numUpdated);
	}

	public function makeGamesCompleted($startpoint=null) {  
		$numUpdated = 0;

		//get all live games
		$games = $this->cron_model->getLiveGames($startpoint); 

		//update each game to be completed
		foreach ($games as $game) {
			$emailData = $this->cron_model->getEmailInfo($game["user_id"], $game["id"]);

			$goalMet = false;

			$gameCredit = $this->cron_model->getGameCredit($game["id"]); //percentages - creator, winwin, fundraiser, etc.
			$gameEarnings = $this->cron_model->getTotalRaised($game["id"])->credits;

			if ($game['credit_type'] === 'free') { //free games need only be ended
				$completed = $this->isGameOver($game);
				$expired = $this->isGameExpired($game);

				$goalMet = ($completed) ? $completed : $expired;
			} else {				
				if ($game["winner_option"] == 2) { //game ends if raised enough
					$goalMet = ($gameEarnings >= $gameCredit->fundraise_value);
				} else if ($game["winner_option"] == 3 || $game["winner_option"] == 4) { //game ends if earned enough AND time is up/date reached
					$completed = $this->isGameOver($game);
					$goalMet = ($completed && ($gameEarnings >= $gameCredit->fundraise_value));
				}
			}

			if ($goalMet) { //mark completed, notify
				$this->db->trans_start();

				$this->cron_model->makeGameStatusCompleted($game["id"]); 

				if ($this->db->affected_rows() > 0) { //if game is successfully made completed, notify
					$numUpdated++;

					if ($game["gameType"] === "challenge") {
						$isReview = $this->cron_model->isReviewGame($game["id"])->isQuesReview;
						if ($isReview === "yes") {
							$emailData->review = true;
							$notes = "Your game, " . $game["name"] . ", requires review!";
						} else {
							$notes = "Your game, " . $game["name"] . ", has ended!";
						}
					} else {
						$notes = "Your game, " . $game["name"] . ", has ended!";
					}

					$body = $this->load->view('emails/game-end-email', $emailData, true);
					sendNotificationAndEmail("game_complete", $game["user_id"], $game["user_id"], $notes, "game", "end", $game["id"], $game["charity_id"], $notes, $body, gmdate("Y-m-d H:i:s"));					
				}

				$this->db->trans_complete();
			}
		}

		echo json_encode($numUpdated);
	}

	public function declareReviewGameWinner() {
		$game_id = filter_var($this->input->post("game_id"), FILTER_VALIDATE_INT);

		$game = (array)$this->cron_model->getGameInfo($game_id);
		$declared = $this->cron_model->isDeclared($game["id"]);

		// get all remaining winners that should be selected - if reselecting, amount may be different that winner count
		$winners = $this->cron_model->getReviewGameWinners($game_id, $declared["remaining"]);

		if (isset($declared["declared"]) && !$declared["declared"] && count($winners) > 0) {
			$gameCredit = $this->cron_model->getGameCredit($game_id);
			$emailData = $this->cron_model->getEmailInfo($game["user_id"], $game["id"]); 
			$emailData->review = true;
	
			$this->db->trans_start();
	
			$this->cron_model->createDistribution($game, $gameCredit);
			if ($this->db->affected_rows() > 0) {
				$game["winners"] = array_map(function($winner) { return $winner->user_id; }, $winners);
				
				//notify game creator by notification and email
				$notes = "Winners have been selected from your game, " . $game['name'] . "!";
				$body = $this->load->view('emails/game-winner-creator-email', $emailData, true);
	
				sendNotificationAndEmail("winners_selected", $game["user_id"], $game["user_id"], $notes, "game", "review", $game["id"], null, $notes, $body);
			
				$this->notifyWinners($game, $game["winners"]);
				$this->notifyPlayers($game, $game["winners"]);
		
				$this->cron_model->makeGameProcessed($game["id"]); //mark game processed
	
				echo json_encode(array('status' => 'success', 'msg' => 'Winners declared successfully!'));
			} else {
				echo json_encode(array('status' => 'failed', 'msg' => 'Failed to declare winners!'));
			}
	
			$this->db->trans_complete();
		} else {
			if ($declared["declared"]) {
				echo json_encode(array('status' => 'failed', 'msg' => 'Winners already declared!'));
			} else {
				echo json_encode(array('status' => 'failed', 'msg' => 'No winners selected!'));
			}
		}
    }

	public function createDistributions($startpoint=null) {  //first step for completed games
		$this->db->trans_start();

		$reviewGames = $this->cron_model->getCompletedReviewGames(); //get all completed reviewable games
		$completedGames = $this->cron_model->getCompletedGames($startpoint); //get all completed games

		$games = array_udiff($completedGames, $reviewGames, function($a, $b) { //return list of complete games excluding all challenge games which require review
			if ($a["id"] == $b["game_id"]) {
				return 0;
			} else {
				return -1;
			}
		});

		for ($i = 0; $i < count($games); $i++) {
			$game = $games[$i];

			$emailData = $this->cron_model->getEmailInfo($game["user_id"], $game["id"]); 
			
			$gameCredit = $this->cron_model->getGameCredit($game["id"]); //percentages - creator, winwin, fundraiser, etc.
			if (($game["credit_type"] == "credit" || $game["credit_type"] == "prize")) { 
				$winners = $this->cron_model->createDistribution($game, $gameCredit); //get all winners and create distribution
				if (count($winners) > 0 && $this->db->affected_rows() > 0) {
					$games[$i]["winners"] = array_map(function($winner) { return $winner->user_id; }, $winners);
					
					//notify game creator by notification and email
					$notes = "Winners have been selected from your game, " . $game['name'] . "!";
					$body = $this->load->view('emails/game-winner-creator-email', $emailData, true);

					sendNotificationAndEmail("winners_selected", $game["user_id"], $game["user_id"], $notes, "game", "win", $game["id"], null, $notes, $body);
				}
			}
		}

		//notify players after creator has been notified
		for ($i = 0; $i < count($games); $i++) {
			$game = $games[$i];

			$this->notifyWinners($game, $game["winners"]);
			$this->notifyPlayers($game, $game["winners"]);

			$this->cron_model->makeGameProcessed($game["id"]); //mark game processed
		}

		$this->db->trans_complete();
	}

	public function confirmDistributions() { //second step for completed games
		$this->db->trans_start();

		$unconfirmed = $this->cron_model->getUnconfirmedDistributions(); 

		$gameDistributions = array();
		foreach ($unconfirmed as $key => $distribution) { //sort distributions by game
			$gameDistributions[$distribution->game_id][$key] = $distribution;
		}

		foreach ($gameDistributions as $game_id => $distributions) {
			$reselected = array();
			$game = get_object_vars($this->cron_model->getGameInfo($game_id));

			foreach ($distributions as $key => $distribution) {
				$emailData = $this->cron_model->getEmailInfo($distribution->winner_id, $distribution->game_id);

				$confirmBy = DateTime::createFromFormat('Y-m-d H:i:s', $distribution->created_at);
				$confirmBy->modify("+ 7 days 0 hours 0 minutes"); //users should confirm within 1 week

				$reselect = isDatePassed($confirmBy);
				if (!$reselect) { //user has not claimed prize, but it HAS NOT been 7 days since - resend notifications to user
					$currentDate = new DateTime(gmdate("Y-m-d H:i:s"));
					$timeLeft = $currentDate->diff($confirmBy);
					$timeLeft = (int)$timeLeft->format("%H") + ((int)$timeLeft->format("%d") * 24);

					$pastNotifications = $this->notification_model->getNotification($distribution->winner_id, null, "prize", "confirm", $game_id);
					$lastNotification = DateTime::createFromFormat('Y-m-d H:i:s', $pastNotifications{0}->Date);
					if ($timeLeft <= 24) { //if less than 24 hours, notify more frequently
						$lastNotification->modify("+ " . (floor($timeLeft/3)) . " hours 0 minutes");
					} else { //notify once a day
						$lastNotification->modify("+ 1 days 0 hours 0 minutes");
					}

					$reNotify = isDatePassed($lastNotification);
					if ($reNotify) { //has been x hours/days since last notification
						if ($timeLeft >= 24) {
							$remainingTime = (int)($timeLeft / 24) . " days";
						} else {
							$remainingTime = $timeLeft . " hours";
						}

						$emailData->remainingTime = $remainingTime;
						$notes = "You have " . $remainingTime . " left to claim a prize you won! Click here to confirm your details!";
						$body = $this->load->view('emails/prize-confirm-details', $emailData, true);
						$subject = "You have " . $remainingTime . " left to claim a prize you won!";

						sendNotificationAndEmail("claim_prize_reminder", $distribution->winner_id, $distribution->winner_id, $notes, "prize", "confirm", $game["id"], null, $subject, $body);

						continue;
					}
				} else { //user has not claimed prize and it HAS been 7 days since - marked failed and reselect
					$this->cron_model->markDistributionFailed($distribution);
					if ($this->db->affected_rows() > 0) {
						array_push($reselected, $distribution);
					}
				}
			}

			$reselected = array_slice($reselected, 0, $game["winner_count"]); //limit reselection count to winner count
			if (count($reselected) > 0) { //reselect players if available
				$this->reselectWinners($game, $reselected);
				
				foreach ($reselected as $key => $distribution) {
					$emailData = $this->cron_model->getEmailInfo($distribution->winner_id, $distribution->game_id);

					$notes = "We've been trying to confirm some details about a prize you won, but unfortunately we've had to select another winner.";
					$body = $this->load->view('emails/game-winner-reselection', $emailData, true);
					$subject = "We've had to select another winner..";

					sendNotificationAndEmail("claim_prize_failed", $distribution->winner_id, $distribution->winner_id, $notes, "prize", "reselect", $distribution->game_id, null, $subject, $body);
				}
			}
		}

		$this->db->trans_complete();
	}

	public function reselectWinners($game, $reselected) {
		$reselectCount = count($reselected);
		$emailData = $this->cron_model->getEmailInfo($game["user_id"], $game["id"]);
		$isReview = $this->cron_model->isReviewGame($game["id"])->isQuesReview;

		if (!isset($isReview) || $isReview !== "yes") { 
			$winners = $this->cron_model->reselectWinners($game, $reselected, $reselectCount);
			if (!empty($winners)) { //winners will not be empty if successful
				$this->notifyWinners($game, $winners);

				if (count($winners) == $reselectCount) { //all winners reselected
					$notes = "Winners have been reselected for your game, " . $game['name'] . "!";
					$body = $this->load->view('emails/game-winner-reselection-creator-email', $emailData, true);
					$subject = $notes;

					sendNotificationAndEmail("winners_reselected", $game["user_id"], $game["user_id"], $notes, "game", "reselect", $game["id"], null, $subject, $body);
				} else { //portion of winners reselected
					$notReselected = array_slice($reselected, count($winners));
					foreach ($notReselected as $key => $distribution) {
						$reason = "Only able to reselect a portion of winners.";
						$this->cron_model->markDistributionUnderReview($distribution->id, $reason);
					}

					$notes = "We were only able to reselect a portion of the needed winners.";
					$body = $this->load->view('emails/game-winner-reselection-portion-email', $emailData, true);
					$subject = "Only able to reselect a portion of your game's winners!";

					sendNotificationAndEmail("winners_reselect_portion", $game["user_id"], $game["user_id"], $notes, "game", "reselect", $game["id"], null, $subject, $body);
				}
			} else { //no winners to reselect, send reselected distributions to admin review
				foreach ($reselected as $key => $distribution) {
					$reason = "Unable to reselect winners.";
					$this->cron_model->markDistributionUnderReview($distribution->id, $reason);
				}

				$notes = "Your initial winners did not claim their prize within the specified time period, but we were unable to select additional winners.";
				$body = $this->load->view('emails/game-winner-reselection-fail-email', $emailData, true);
				$subject = "Unable to reselect winners!";

				sendNotificationAndEmail("winners_reselect_failed", $game["user_id"], $game["user_id"], $notes, "game", "reselect", $game["id"], null, $subject, $body);
			}
		} else {
			#allow creator to select new winners, excluding previous winners that didnt respond
			$this->cron_model->markReselectedReviewGameWinners($reselected);
			$this->cron_model->markGameForReview($game["id"]);
			
			$emailData->reselectCount = $reselectCount;
			$notes = $reselectCount . " of the winners you selected " . (($reselectCount > 1) ? "haven't" : "hasn't") . " claimed their prize within 7 days.";
			$body = $this->load->view('emails/game-winner-review-reselection', $emailData, true);
			$subject = "You need to select more winners.";

			sendNotificationAndEmail("winners_select_more", $game["user_id"], $game["user_id"], $notes, "game", "review", $game["id"], null, $subject, $body);
		}
	}

	public function notifyWinners($game, $winners) {
		foreach ($winners as $winner) { //notify winners by notification and email
			$emailData = $this->cron_model->getEmailInfo($winner, $game["id"]);

			$notes = "Congratulations, you're a winner! You won " . (($game["credit_type"] == "prize") ? "a prize from " : "credits from ") . $game['name'] . "!";
			$body = $this->load->view('emails/game-winner-email', $emailData, true);
			$subject = "Congratulations, you're a winner!";

			sendNotificationAndEmail("game_winner", $winner, $winner, $notes, "prize", "confirm", $game["id"], null, $subject, $body);
		}
	}

	public function notifyPlayers($game, $winners) { //those who have not won
		$players = $this->cron_model->getGamePlayers($game, $winners);

		foreach ($players as $player) { //notify winners by notification and email
			$emailData = $this->cron_model->getEmailInfo($player->user_id, $game["id"]);

			$notes = $game["name"] . " has ended. Unfortunately, you weren't selected as a winner.";
			$body = $this->load->view('emails/game-nonwinner-email', $emailData, true);
			$subject = "Thanks for playing!";

			sendNotificationAndEmail("game_participant", $player->user_id, $player->user_id, $notes, "game", "end", $game["id"], null, $subject, $body);
		}
	}

	public function distributeCredits() {
		$numDistributed = 0;
		$distributions = $this->cron_model->getDistributions(); 

		//update each game to be completed
		foreach ($distributions as $distribution) {
			if (isset($distribution->confirmed)) { //distribution is a prize
				if ($distribution->confirmed == 0) { //skip unconfirmed
					continue;
				} else {
					if ($distribution->review == 1 && $distribution->approved != 1) { //skip those under review
						continue;
					}

					//check shipping status, then distribute
					$status = $this->checkShippingStatus($distribution);
					switch($status) {
						case "unprocessed":
						case "unreceived":
						case "review":
							continue 2;
						default:
							break;
					}
				}
			}

			$this->db->trans_start();

			if ($distribution->winner_type == 2) {
				$this->cron_model->distributeCredits($distribution->winner_id, $distribution->winner_credits, $distribution->game_id, 1, 5); 
			} else {
				$this->cron_model->distributeCredits(112, $distribution->goedu_credits, $distribution->game_id, 3, 6); 
				$this->cron_model->distributeCredits(112, $distribution->creator_fundraise_credits, $distribution->game_id, 4, 2); 
				$this->cron_model->distributeCredits(112, $distribution->winner_fundraise_credits, $distribution->game_id, 4, 4); 

				$this->cron_model->distributeCredits($distribution->creator_id, $distribution->creator_credits, $distribution->game_id, 2, 1); 
				$this->cron_model->distributeCredits($distribution->winner_id, $distribution->winner_credits, $distribution->game_id, 1, 3); 
			}


			$this->cron_model->markDistributionComplete($distribution->id);
			if ($this->db->affected_rows() > 0) {
				$numDistributed++;
			}

			$this->db->trans_complete();
		}

		echo json_encode($numDistributed);
	}

	public function checkShippingStatus($distribution) {
		$currentDate = new DateTime(gmdate("Y-m-d H:i:s"));

		$shippingInfo = $this->cron_model->getShippingInfo($distribution->creator_id, $distribution->winner_id, $distribution->game_id);
		if ($shippingInfo->processed != 1) { //shipper has not processed
			$emailData = $this->cron_model->getEmailInfo($shippingInfo->shipper_id, $shippingInfo->game_id);
			
			$confirmedAt = DateTime::createFromFormat('Y-m-d H:i:s', $distribution->confirmed_at);

			$timeSinceClaimed = $currentDate->diff($confirmedAt);
			$timeSinceClaimed = (int)$timeSinceClaimed->format("%H") + ((int)$timeSinceClaimed->format("%d") * 24);
			if ($timeSinceClaimed > 240) { //shipper has 10 days to process
				//marked failed - refund
				$reason = "Creator failed to process and ship prize.";
				$this->cron_model->markDistributionUnderReview($distribution->id, $reason);

				$notes = "You have failed to ship a winner their prize within 10 days. Your credits are being witheld until staff can manually review the status.";
				$body = $this->load->view('emails/prize-process-fail-email', $emailData, true);

				sendNotificationAndEmail("process_prize_failed", $shippingInfo->shipper_id, $shippingInfo->shipper_id, $notes, "prize", "review", $shippingInfo->game_id, null, $notes, $body);

				return "review";
			} else {
				//notify shipper
				$pastNotifications = $this->notification_model->getNotification($shippingInfo->shipper_id, null, "prize", "process", $shippingInfo->game_id);
				if (count($pastNotifications) <= 0) {
					$pastNotifications = $this->notification_model->getNotification($shippingInfo->shipper_id, null, "prize", "claimed", $shippingInfo->game_id);
				}
				
				$lastNotification = DateTime::createFromFormat('Y-m-d H:i:s', $pastNotifications{0}->Date);
				if ($timeSinceClaimed <= 24) { //if less than 24 hours, notify more frequently
					$lastNotification->modify("+ " . (floor($timeSinceClaimed/3)) . " hours 0 minutes");
				} else { //notify once every 1 days
					$lastNotification->modify("+ 1 days 0 hours 0 minutes");
				}

				$reNotify = isDatePassed($lastNotification);
				if ($reNotify) {
					$notes = "A winner is waiting for you to process their prize!";
					$body = $this->load->view('emails/prize-process-email', $emailData, true);

					sendNotificationAndEmail("process_prize_reminder", $shippingInfo->shipper_id, $shippingInfo->shipper_id, $notes, "prize", "process", $shippingInfo->game_id, null, $notes, $body);
				}

				return "unprocessed";
			}
		} else { //shipper has processed
			if (!isset($shippingInfo->received)) { //shippee has not noted it has not been received
				$processedAt = DateTime::createFromFormat('Y-m-d H:i:s', $shippingInfo->processed_at);

				$timeSinceProcess = $currentDate->diff($processedAt);
				$timeSinceProcess = (int)$timeSinceProcess->format("%H") + ((int)$timeSinceProcess->format("%d") * 24);
				if ($timeSinceProcess > 720) { //shippee has not marked it received within a month
					$emailData = $this->cron_model->getEmailInfo($shippingInfo->shipper_id, $shippingInfo->game_id);

					$reason = "Prize winner has not marked prize received within 30 days.";
					$this->cron_model->markDistributionUnderReview($distribution->id, $reason); //distribution should be under review for refund/reselection

					$notes = "A prize winner has failed to acknowledge that they received their prize. Your credits are being witheld until staff can manually review the status.";
					$body = $this->load->view('emails/prize-acknowledge-fail-email', $emailData, true);

					sendNotificationAndEmail("winner_acknowledge_prize_failed", $shippingInfo->shipper_id, $shippingInfo->shipper_id, $notes, "prize", "review", $shippingInfo->game_id, null, $notes, $body);

					return 'review';
				} else {
					$emailData = $this->cron_model->getEmailInfo($shippingInfo->shippee_id, $shippingInfo->game_id);

					//notify shippee
					$pastNotifications = $this->notification_model->getNotification($shippingInfo->shippee_id, null, "prize", "receive", $shippingInfo->game_id);
					$lastNotification = DateTime::createFromFormat('Y-m-d H:i:s', $pastNotifications{0}->Date);
					if ($timeSinceProcess <= 24) { //if less than 24 hours, notify more frequently
						$lastNotification->modify("+ " . (floor($timeSinceProcess/3)) . " hours 0 minutes");
					} else { //notify once every 5 days
						$lastNotification->modify("+ 5 days 0 hours 0 minutes");
					}

					$reNotify = isDatePassed($lastNotification);
					if ($reNotify) {
						$notes = "Have you received your prize?";
						$body = $this->load->view('emails/prize-receive-query-email', $emailData, true);

						sendNotificationAndEmail("prize_acknowledge_reminder", $shippingInfo->shippee_id, $shippingInfo->shippee_id, $notes, "prize", "receive", $shippingInfo->game_id, null, $notes, $body);
					}

					return 'unreceived';
				}
			} else if ($shippingInfo->received == 0) { //shippee has noted it has not been received 
				$emailData = $this->cron_model->getEmailInfo($shippingInfo->shipper_id, $shippingInfo->game_id);

				$reason = "Prize winner has marked the prize unreceived.";
				$this->cron_model->markDistributionUnderReview($distribution->id, $reason); //distribution should be under review for refund/reselection

				$notes = "A prize winner has acknowledged that they have not received their prize. Your credits are being witheld until staff can manually review the status.";
				$body = $this->load->view('emails/prize-receive-fail-email', $emailData, true);

				sendNotificationAndEmail("winner_acknowledge_prize", $shippingInfo->shipper_id, $shippingInfo->shipper_id, $notes, "prize", "review", $shippingInfo->game_id, null, $notes, $body);

				return 'review';
			} else { //shippee has received
				$emailData = $this->cron_model->getEmailInfo($shippingInfo->shipper_id, $shippingInfo->game_id);

				$notes = "A prize winner has acknowledged that they received their prize!";
				$body = $this->load->view('emails/prize-receive-email', $emailData, true);

				sendNotificationAndEmail("winner_acknowledge_prize", $shippingInfo->shipper_id, $shippingInfo->shipper_id, $notes, "prize", "receive", $shippingInfo->game_id, null, $notes, $body);

				return 'received';
			}
		}
	}

	public function removeExpiredAccountLinkCodes() {
		$removed = $this->cron_model->removeExpiredCodes();

		echo $removed;
	}
	
	//refunding users should change other distribution totals if there are multiple winners because by refunding payments you are changing the total raised

	public function nullifyWinner() { //winner receives no reward, creator's fundraiser receives share
		$id = sanitizeInput($this->input->post("id"), FILTER_VALIDATE_INT);
		$note = sanitizeInput($this->input->post("note", true), FILTER_SANITIZE_STRING);

		$this->db->trans_start();

		$distribution = $this->cron_model->getDistribution($id);
		$this->cron_model->approveDistribution($distribution, $note);

		$success = $this->cron_model->nullifyWinner($distribution->id);

		$this->db->trans_complete();

		echo json_encode(array("status" => $success));
	}

	public function compensateWinner() { //compensate winner with credits from creator's share
		$id = sanitizeInput($this->input->post("id"), FILTER_VALIDATE_INT);
		$note = sanitizeInput($this->input->post("note", true), FILTER_SANITIZE_STRING);

		$compensated_credit = sanitizeInput($this->input->post("amount"), FILTER_VALIDATE_FLOAT);

		$this->db->trans_start();

		$distribution = $this->cron_model->getDistribution($id);
		$this->cron_model->approveDistribution($distribution, $note);

		$success = $this->cron_model->compensateWinner($distribution->id, $compensated_credit);

		$this->db->trans_complete();

		echo json_encode(array("status" => $success));
	}

	public function partialRefund() { //refund all winner payments
		$id = sanitizeInput($this->input->post("id"), FILTER_VALIDATE_INT);
		$note = sanitizeInput($this->input->post("note", true), FILTER_SANITIZE_STRING);

		$this->db->trans_start();

		$distribution = $this->cron_model->getDistribution($id);
		$this->cron_model->disapproveDistribution($distribution, $note);

		$success = $this->cron_model->refundPlayerPayments($distribution->game_id, $distribution->winner_id);

		$this->db->trans_complete();

		echo json_encode(array("status" => $success));
	}

	public function completeRefund() { //refund all player payments
        $game_id = sanitizeInput($this->input->post("game_id"), FILTER_VALIDATE_INT);
		$note = sanitizeInput($this->input->post("note", true), FILTER_SANITIZE_STRING);

		$this->db->trans_start();

		$distributions = $this->cron_model->getGameDistributions($game_id);
		foreach($distributions as $key => $distribution) {
			$this->cron_model->disapproveDistribution($distribution, $note);
		} 

		$success = $this->cron_model->refundPlayerPayments($game_id);

		$this->db->trans_complete();

		echo json_encode(array("status" => $success));
	}

	//refunds - not tested fully 

	public function isGameOver($game) {
		$endDate = DateTime::createFromFormat('Y-m-d H:i:s', $game["Publish_Date"]);
		$endDate->modify("+ {$game["End_Day"]} days {$game["End_Hour"]} hours {$game["End_Minute"]} minutes");
		
		return isDatePassed($endDate);
	}

	public function isGameExpired($game) { //free games should not last forever
		$expireDate = DateTime::createFromFormat('Y-m-d H:i:s', $game["Publish_Date"])->add(new DateInterval('P2Y'));
		
		return isDatePassed($expireDate);
	}

	//testing

	public function addPlayerData($game_id, $user_id, $completed_in, $steps, $won, $score) {
		$data = array(
			'completed_in' => $completed_in,
			'steps' => $steps,
			'won' => $won,
			'score' => $score,
		);

		$added = $this->cron_model->addPlayerData($game_id, $user_id, $data);

		echo "inserted " . $added;
	}

	public function addRandomPlayerData($game_id, $count) {
		$added = $this->cron_model->addRandomPlayerData($game_id, $count);

		echo "inserted " . $added;
	}
}