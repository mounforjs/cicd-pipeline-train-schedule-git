<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron_model extends CI_Model {
	
    public function __construct() {
        parent::__construct();
    }

	public function getLiveGames($startpoint) {
		$this->db->select("game.*, gt.name as gameType, ugc.user_id, ugc.charity_id");
		$this->db->from("game");
		$this->db->join("user_game_charity as ugc", "ugc.game_id = game.id");
		$this->db->join("gametype as gt", "game.Type = gt.id");
		$this->db->where(array('game.Publish'=> "Live", "game.Status" => "Running"));

		if (isset($startpoint) && !empty($startpoint)) {
			$this->db->where("game.id >=", $startpoint);
		}

		$games = $this->db->get()->result_array();

		return $games;
	}

	public function getPublishGames($startpoint) {
		$this->db->select("game.*, ugc.user_id, ugc.charity_id");
		$this->db->from("game");
		$this->db->join("user_game_charity as ugc", "ugc.game_id = game.id");
		$this->db->where(array('game.Publish'=> "Yes", "game.Status !=" => "Completed"));

		if (isset($startpoint) && !empty($startpoint)) {
			$this->db->where("game.id >=", $startpoint);
		}

		$games = $this->db->get()->result_array();

		return $games;
	}

	public function getCompletedGames($startpoint) {
		$this->db->select("game.*, ugc.user_id, ugc.charity_id");
		$this->db->from("game");
		$this->db->join("user_game_charity as ugc", "ugc.game_id = game.id");
		$this->db->where(array('game.Publish'=> "No", "game.Status" => "Completed", "game.processed" => 0));
		
		if (isset($startpoint) && !empty($startpoint)) {
			$this->db->where("game.id >=", $startpoint);
		}

		$games = $this->db->get()->result_array();

		return $games;
	}

	public function isDeclared($game_id) {
		$this->db->select("game_id, count(game_id) as count, num_of_winners");
		$this->db->from("tbl_credits_distribution");
		$this->db->where(array('game_id'=> $game_id, "status !=" => 3));

		$declared = $this->db->get()->row();
		if (isset($declared) && $declared->count == $declared->num_of_winners) {
			return ["declared" => true];
		} else {
			return ["declared" => false, "remaining" => $declared->num_of_winners - $declared->count];
		}
	}

	public function isReviewGame($game_id) {
		$sql = "
			select * FROM (
				SELECT `t1`.`id` as game_id,
			
				CASE    
					WHEN (
						LOCATE('review', (
							SELECT GROUP_CONCAT(TYPE) FROM question
								WHERE FIND_IN_SET(id, (
									SELECT
										REPLACE(REPLACE(REPLACE(questions, ']', ''), '[', ''),'\"','')
									FROM
										quiz
									WHERE id = t5.id)
								)
							))
						) > 0 THEN 'yes'
				ELSE 'no'
				END AS isQuesReview
				
				FROM `game` AS `t1`
				JOIN `gametype` AS `t4` ON `t1`.`Type` = `t4`.`id`
				JOIN `quiz` AS `t5` ON `t5`.`id` = `t1`.`quiz_id`

				WHERE `t1`.`id` = " . $game_id . "
				
				GROUP BY `t1`.`id`
				ORDER BY `t1`.`Publish_Date` DESC
			) AS MT"
		;

		$q = $this->db->query($sql);
        return $q->row();
	}

	public function getCompletedReviewGames() {
		$sql = "
			select * FROM (
				SELECT `t1`.`id` as game_id,
			
				CASE    
					WHEN (
						LOCATE('review', (
							SELECT GROUP_CONCAT(TYPE) FROM question
								WHERE FIND_IN_SET(id, (
									SELECT
										REPLACE(REPLACE(REPLACE(questions, ']', ''), '[', ''),'\"','')
									FROM
										quiz
									WHERE id = t5.id)
								)
							))
						) > 0 THEN 'yes'
				ELSE 'no'
				END AS isQuesReview
				
				FROM `game` AS `t1`
				JOIN `gametype` AS `t4` ON `t1`.`Type` = `t4`.`id`
				JOIN `quiz` AS `t5` ON `t5`.`id` = `t1`.`quiz_id`

				WHERE `t1`.`Status` = 'Completed' AND `t1`.`processed` = 0 AND `t4`.`name` = 'challenge' AND `t1`.`review_status` = 0
				
				GROUP BY `t1`.`id`
				ORDER BY `t1`.`Publish_Date` DESC
			) AS MT
			
			WHERE MT.isQuesReview = 'yes'"
		;

        $q = $this->db->query($sql);
        $result = $q->result_array();

		return $result;
	}

	public function getGameCredit($game_id) {
		$this->db->select('fundraise_value,beneficiary_percentage ,wwl_percentage ,creator_percentage,winner_percentage');
		$this->db->where('game_id', $game_id);
		$this->db->from('game_credit');

		return $this->db->get()->row();
	}

	public function getTotalRaised($game_id) {
		$this->db->select('COALESCE(sum(credits), 0) as credits');
		$this->db->from('payments');
		$this->db->where(array('game_id' => $game_id, 'Status' => '2'));

		return $this->db->get()->row();
	}

	public function getGameInfo($game_id) {
		$this->db->select("game.*, ugc.user_id, ugc.charity_id");
		$this->db->from("game");
		$this->db->join("user_game_charity as ugc", "ugc.game_id = game.id");
		$this->db->where('game.id', $game_id);

		return $this->db->get()->row();
	}

	public function getCharityInfo($game_id, $user_id) {
		$this->db->from("user_game_charity as ugc");
		$this->db->where(array('game_id' => $game_id, 'ugc.user_id' => $user_id));
		$this->db->join('charity', 'charity.id = ugc.charity_id', 'left');

		return $this->db->get()->row();
	}

	public function getDistribution($id) {
		$this->db->from("tbl_credits_distribution as tcd");
		$this->db->where('id', $id);

		return $this->db->get()->row();
	}

	public function getGameDistributions($game_id) {
		$this->db->from("tbl_credits_distribution as tcd");
		$this->db->where('game_id', $game_id);

		$this->db->order_by('status', "ASC");

		return $this->db->get()->result();
	}

	public function getDistributions() {
		$this->db->from("tbl_credits_distribution as tcd");
		$this->db->group_start();
		$this->db->where(array('status' => 1, 'review' => 0, "approved is NULL" => NULL));
		
		$this->db->group_start();
		$this->db->or_where(array("confirmed !=" => 2, "confirmed is NULL" => NULL));
		$this->db->group_end();

		$this->db->group_end();

		$this->db->or_group_start();
			$this->db->where(array('status' => 1, 'review' => 1, "approved" => 1, "confirmed" => 1));
		$this->db->group_end();

		$distributions = $this->db->get()->result();

		return $distributions;
	}

	public function getUnconfirmedDistributions() {
		$this->db->from("tbl_credits_distribution as tcd");
		$this->db->where(array('status' => 1, 'review' => 0, 'confirmed' => 0));

		$this->db->where("game_id", 1061);
		$this->db->order_by("game_id", "ASC");
		$this->db->order_by("winner_type", "ASC");

		$distributions = $this->db->get()->result();

		return $distributions;
	}

	public function getReselectedUsers($game_id) {
		$this->db->from("tbl_credits_distribution as tcd");
		$this->db->where(array('confirmed' => 2, 'game_id' => $game_id));

		$this->db->order_by("winner_type", "ASC");

		$result = $this->db->get()->result_array();

		return array_map(function($value) { return $value["winner_id"]; }, $result);
	}

	public function getConfirmedUsers($game_id) {
		$this->db->from("tbl_credits_distribution as tcd");
		$this->db->where(array('confirmed' => 1, 'game_id' => $game_id));

		$this->db->order_by("winner_type", "ASC");

		$result = $this->db->get()->result_array();

		return array_map(function($value) { return $value["winner_id"]; }, $result);
	}

	public function getShippingInfo($shipper_id, $shippee_id, $game_id) {
		$this->db->from("escrow_shipping_info");
		$this->db->where(array('shipper_id' => $shipper_id, 'shippee_id' =>  $shippee_id, 'game_id' => $game_id));

		return $this->db->get()->row();
	}

	public function getPlayersInProgress($game_id) {
		$this->db->select("count(id) as total");
		$this->db->from("players_in_progress");
		$this->db->where("game_id", $game_id);

		return $this->db->get()->row();
	}

	public function getSummedPayments($game_id, $player=null) {
		$this->db->select("User_ID, sum(credits) as total, currency_code");
		$this->db->from("payments");
		$this->db->where(array('game_id' => $game_id, 'Status' => '2'));
		if (isset($player)) {
			$this->db->where("User_ID", $player);
		}

		$this->db->group_by('User_ID');

		return $this->db->get()->result_array();
	}

	public function getUserBalance($user_id) {
		$this->db->select('total_credits');
		$this->db->from("payments");
		$this->db->where('User_ID', $user_id);
		$this->db->order_by('Date',"DESC");
		$this->db->limit(1);

		return $this->db->get()->row();
	}

	public function getEmailInfo($user_id, $game_id) {
		$this->db->select('username');
		$this->db->from("tbl_users");
		$this->db->where("user_id", $user_id);
		$winner = $this->db->get()->row()->username;

		$this->db->select('game.name as name, gametype.name as type, charity.id as charity_id, charity.slug as charity_slug, charity.name as charity, game.slug');
		$this->db->from("user_game_charity as ugc");
		$this->db->join("game", "game.id=ugc.game_id");
		$this->db->join("charity", "charity.id=ugc.charity_id");
		$this->db->join('gametype', 'gametype.id  = game.Type','left');
		$this->db->where("ugc.game_id", $game_id);

		$result = $this->db->get()->row();
		$result->username = $winner;

		return $result;
	}

	public function getGameTypeParent($id) {
		$this->db->select('id, parent'); 
		$this->db->from('gametype'); 
		$this->db->where('id', $id); 
		
		return $this->db->get()->row();
	}

	public function getMinecraftScoreMeasure($game_id) {
		$this->db->select('mgi.*'); 
		$this->db->from('minecraft_games as mg'); 
		$this->db->where('mg.game_id', $game_id); 
		$this->db->join('minecraft_gameinfo as mgi', "mg.info_id=mgi.id"); 
		
		return $this->db->get()->row();
	}

	public function makeGameStatusLive($game_id) {
		$this->db->where('id', $game_id); 
		$this->db->update('game', array('game.Publish' => "Live")); 
	}

	public function makeGameStatusCompleted($game_id) {
		$this->db->where('id', $game_id); 
		$this->db->update('game', array('game.Publish' => 'No', 'game.Status' => "Completed")); 
	}

	public function makeGameProcessed($game_id) {
		$this->db->where('id', $game_id); 
		$this->db->update('game', array('game.processed' => 1)); 
	}

	public function markDistributionComplete($id) {
		$this->db->set('status', 2);
		$this->db->where('id', $id);
		$this->db->update('tbl_credits_distribution');
	}

	public function markDistributionConfirmed($game_id, $user_id) {
		$this->db->where(array('game_id' => $game_id, 'winner_id' => $user_id, 'confirmed' => 1));
		$exist = $this->db->get('tbl_credits_distribution')->row();

		if (!isset($exist)) {
			$data = array(
				"confirmed" => 1,
				"confirmed_at" => gmdate("Y-m-d H:i:s")
			);
	
			$this->db->where(array('game_id' => $game_id, 'winner_id' => $user_id, 'confirmed' => 0));
			$this->db->update('tbl_credits_distribution', $data);
		}
	}

	public function markDistributionUnderReview($id, $notes=null) {
		$this->db->set('review', 1);
		if (isset($notes)) {
			$this->db->set('notes', "CONCAT(notes, ' " . $notes . "')", false);
		}
		
		$this->db->where('id', $id);
		$this->db->update('tbl_credits_distribution');
	}

	public function markReselectedReviewGameWinners($reselected) {
		foreach($reselected as $key => $distribution) { 
			$this->db->set('reselected', 1);
			$this->db->set('final_rank', null);
			$this->db->where(array('game_id' => $distribution->game_id, 'user_id' => $distribution->winner_id));
			$this->db->update('tbl_user_review');
		}
	}

	public function markGameForReview($game_id) {
		$this->db->set('review_status', 1);
		$this->db->where('id', $game_id);
		$this->db->update('game');
	}

	public function approveDistribution($distribution, $note=null) {
		$this->db->set('approved', 1);
		if (isset($notes)) {
			$this->db->set('notes', "CONCAT(notes, ' " . $note . "')", false);
		}
		
		$this->db->where(array('game_id' => $distribution->game_id, 'winner_id' => $distribution->winner_id, 'confirmed' => 0));
		$this->db->update('tbl_credits_distribution');
	}

	public function disapproveDistribution($distribution, $note=null) {
		$this->db->set('status', 3);
		$this->db->set('approved', 0);
		if (isset($notes)) {
			$this->db->set('notes', "CONCAT(notes, ' " . $note . "')", false);
		}
		
		$this->db->where(array('game_id' => $distribution->game_id, 'winner_id' => $distribution->winner_id, 'confirmed' => 0));
		$this->db->update('tbl_credits_distribution');
	}

	public function markDistributionFailed($distribution) {
		$data = array(
			"status" => 3,
			"confirmed" => 2,
		);
		
		$this->db->where(array('game_id' => $distribution->game_id, 'winner_id' => $distribution->winner_id, 'confirmed' => 0));
		$this->db->update('tbl_credits_distribution', $data);
	}

	public function markShippingFailed($id) {
		$this->db->set('processed', 2);
		$this->db->where('id', $id);
		$this->db->update('escrow_shipping_info');
	}

	public function markShippingReceived($id) {
		$this->db->set('received', 1);
		$this->db->where('id', $id);
		$this->db->update('escrow_shipping_info');
	}

	public function selectGameWinners($game, $limit=null, $not=null) {
		$typeParent = $this->getGameTypeParent($game["Type"]);
		$type = (int)($typeParent->parent > 0) ? $typeParent->parent : $game["Type"];

		$order = '';
		$select = '';

		switch ($type) {
			case 1: //puzzle
				if ($game["time_limit"] == 1) {
					$select = 'completed_in';
					$order = 'completed_in ASC';
				} else {
					if ($game["Steps"] == 1) {
						$select = 'steps, completed_in';
						$order = 'steps ASC, completed_in ASC';
					} else {
						$select = 'score';
						$order = 'score DESC';
					}
				}

				break;
			case 2: //2048
				if ($game["time_limit"] == 1) {
					$select = 'won, completed_in';
					$order = 'won DESC, completed_in ASC';
				} else {
					$select = 'won, steps';
					$order = 'won DESC, steps ASC';
				}

				break;
			case 3: //challenge
				if ($game["Quiz_rules"] == 1) {
					$select = 'completed_in, quiz_percentage';
					$order = 'completed_in ASC, quiz_percentage DESC';
				}  else if ($game["Quiz_rules"] == 2) {
					$select = 'quiz_percentage';
					$order = 'quiz_percentage DESC';
				} else {
					$select = 'quiz_percentage, completed_in';
					$order = 'quiz_percentage DESC, completed_in ASC';
				}

				break;
			case 7: //minecraft
				$game_base = $this->getMinecraftScoreMeasure($game["id"])->game_base;

				if ($game_base == "point" || $game_base == "judge") {
					$select = 'score';
					$order = 'score DESC';
				} else if ($game_base == "time") {
					$select = 'completed_in, score';
					$order = 'completed_in ASC, score DESC';
				}

				break;
			// future games go here
		}

		// select every attempt ordered by best to worst
		$this->db->select("uga.user_id, uga.selected_beneficiary, ${$select}, created_at");
		$this->db->from('user_game_attempts as uga');
		$this->db->join('game_history as gh', "uga.game_session_id=gh.game_session_id");
		$this->db->where('game_id', $game["id"]);

		// exclude attempts where user is being reselected
		if (isset($not)) {
			$this->db->where_not_in("user_id", $not);
		}

		$this->db->order_by($order);
		$this->db->order_by('created_at', "DESC");
		$subquery = $this->db->get_compiled_select();

		// select best attempt by user
		$this->db->from("(${subquery}) as winners");
		$this->db->group_by("user_id");
		$this->db->order_by($order);
		$this->db->order_by('created_at', "DESC");

		if (isset($limit)) {
			$this->db->limit($limit);
		} else {
			$this->db->limit($game["winner_count"]);
		}

		return $this->db->get()->result();
	}

	public function getGamePlayers($game, $winners) {
		$this->db->select("user_id");

		$this->db->from('user_game_attempts');
		$this->db->where('game_id', $game["id"]);
		$this->db->where_not_in("user_id", $winners);

		$this->db->group_by("user_id");

		return $this->db->get()->result();
	}

	public function createDistribution($game, $gameCredit, $winners=null) {
		$fundraise_value = $this->getTotalRaised($game["id"])->credits; //total of all payments

		$fundraise_percentage = $gameCredit->beneficiary_percentage/100;
		$wwl_percentage = $gameCredit->wwl_percentage/100;
		$creator_percentage = $gameCredit->creator_percentage/100;
		$winner_percentage = $gameCredit->winner_percentage/100;

		if (!isset($winners)) {
			$winners = $this->selectGameWinners($game);
		}

		$winnerCount = count($winners);
		if ($winnerCount >= 1) {
			//credit awards dependent on # of winners
			$fundraise_credits_value = ($fundraise_percentage * $fundraise_value) / $winnerCount;
			$winwin_credits = ($wwl_percentage * $fundraise_value) / $winnerCount;
			$creator_credits = ($creator_percentage * $fundraise_value) / $winnerCount;
			$winner_credits = ($winner_percentage * $fundraise_value) / $winnerCount;	

			$charity_info = $this->getCharityInfo($game["id"], $game["user_id"]);
			$creator_fundraise_id = $charity_info->charity_id;

			foreach ($winners as $index => $row) {	
				$winnerType = 1;
				$notes = "Primary Winner: " . $winner_credits . " credits.";

				if ($game["credit_type"] == 'prize') {
					$prize_value = $game["value_of_the_game"];
					$notes = "Primary Winner: '" . $game["prize_title"] . "' worth $" . $prize_value . ".";
				
					if ($index > 0) {
						$winnerType = 2;
						$notes = "Non-Primary Winner: '" . $game["prize_title"] . "' worth $" . $prize_value . ".";
					}
				}

				if ($game["donationOption"] == 1) {
					$winner_fundraise_id = $creator_fundraise_id;
					$winner_fundraise_credits_value = $fundraise_credits_value/2;
				} else {
					if ($winnerType == 1) { //first place winner - portion of goal goes to beneficiary of their choice
						$winner_fundraise_id = $row->selected_beneficiary;
					} else {
						$winner_fundraise_id = null;
					}

					$winner_fundraise_credits_value = ($winnerType == 1) ? $fundraise_credits_value/2 : null;
				}

				$data_credits = array(
					'ref_number' => time().uniqid(),
					'game_id' => $game["id"],
					'status' => 1,

					'creator_id' => $game["user_id"],
					'creator_credits' => $creator_credits,

					'creator_fundraise_id' => $creator_fundraise_id,
					'creator_fundraise_credits' => $fundraise_credits_value/2,

					'winner_id' => $row->user_id,
					'winner_credits' => $winner_credits,

					'winner_fundraise_id' => $winner_fundraise_id,
					'winner_fundraise_credits' => $winner_fundraise_credits_value,
					'winner_type' => $winnerType,
					'notes' => isset($notes) ? $notes : '',
					'num_of_winners' => $game["winner_count"],

					'goedu_credits' => $winwin_credits,
					'confirmed' => (($game["credit_type"] == "prize") ? 0 : NULL),

					'created_at' => date('Y-m-d H:i:s')
				);
				$this->db->insert('tbl_credits_distribution', $data_credits);
			}
		}

		return $winners;
	}

	public function reselectWinners($game, $reselected, $num_of_reselects) {
		$currentConfirmedUsers = $this->getConfirmedUsers($game["id"]); //do not reselect from already confirmed users
		$pastReselectedUsers = $this->getReselectedUsers($game["id"]); //past users that were reselected, which are not included in $reselected
		$currentReselectedUsers = array_map(function($value) { return $value->winner_id; }, $reselected); //current users being reselected
		
		$allReselectedUsers = array_merge($currentConfirmedUsers, $currentReselectedUsers, $pastReselectedUsers);

		$winners = $this->selectGameWinners($game, $num_of_reselects, $allReselectedUsers);
		if (!empty($winners)) { //can reselect winners
			foreach ($winners as $index => $row) {
				$data = $reselected[$index];
	
				unset($data->id);
				$data->winner_id = $row->user_id;
				$data->ref_number = time().uniqid();
				$data->notes = $data->notes . "\n\nWinner reselected.\n\n";
				$data->confirmed = 0;
				$data->created_at = date('Y-m-d H:i:s');
	
				if ($game["donationOption"] == 1) {
					$data->winner_fundraise_id = $this->getCharityInfo($game["id"], $row->user_id);
				} else {
					if ($data->winner_type == 1) {
						$data->winner_fundraise_id = $row->selected_beneficiary;
					}
				}
	
				$this->db->insert('tbl_credits_distribution', $data);
			}
		}
		
		return $winners;
	}

	public function getCurrentCreditTotal($user_id) {
		$this->db->select("total_credits");
		$this->db->from("payments");
		$this->db->where("User_ID", $user_id);
		$this->db->order_by("payment_id", "DESC");

		return $this->db->get()->row();
	}

	public function distributeCredits($id, $credits, $game_id, $type, $user_type) {
		$gameInfo = $this->getGameInfo($game_id);
		$charityInfo = $this->getCharityInfo($game_id, $id);

		$is_company = 0;
		$donated_to_fundraiser = 0;
		$is_deductible = 0;

		switch($type) {
			case 1: //winner
				$notes = "Game: " . $game_id . " : " . $gameInfo->name . " - Winner cut: $" . $credits . ".";
				break;
			case 2: //creator
				$notes = "Game: " . $game_id . " : " . $gameInfo->name . " - Creator cut: $" . $credits . ".";
				break;
			case 3: //winwin
				$notes = "Game " . $game_id . " : " . $gameInfo->name . " - WinWinLabs cut: $" . $credits . ".";
				$is_company = 1;
				break;
			case 4: //benficiary
				$notes = "Game " . $game_id . " : " . $gameInfo->name . " - Beneficiary: " . $charityInfo["name"] . " - Amount owed: $" . $credits . ".";
				$is_company = 1;
				$donated_to_fundraiser = 1;
				$is_deductible = 1;
				break;
		}

		$currentTotalCredits = $this->getCurrentCreditTotal($id)->total_credits;

		$data = array(
			"game_id" => $game_id,
			"User_ID" => $id,
			"txn_id" => '',
			"payment_gross" => $credits,
			"total_charge" => $credits,
			"currency_code" => 0,
			"payment_status" => "Completed",
			"Date" => date("Y-m-d H:i:s"),
			"Credits" => $credits,
			"Notes" => $notes,
			"Status" => 1,
			"is_paid" => 3,
			"payment_mode" => 1,
			"total_credits" => $currentTotalCredits + $credits,
			"user_type" => $user_type,
			"is_company" => $is_company,
			"donated_to_fundraiser_name" => $donated_to_fundraiser,
			"is_deductible" => $is_deductible = 0
		);
		$this->db->insert("payments", $data);

		if ($this->db->affected_rows() > 0) {
			$insert_id = $this->db->insert_id();
			$transaction = $insert_id.uniqid();
	
			$this->db->set('txn_id', $transaction);
			$this->db->where('payment_id', $insert_id);
			$this->db->update('payments');
		}
	}

	public function createShippingInfo($shipper_id, $shippee_id, $address_id, $game_id) {
		$this->db->from('user_address');
		$this->db->where(array('id' => $address_id, "user_id" => $shippee_id));
		$address = $this->db->get()->row();

		$data = array(
			"game_id"  => $game_id,
			"shipper_id" => $shipper_id,
			"shippee_id" => $shippee_id,
			"fullname"  => $address->fullname,
			"address_1"  => $address->address_1,
			"address_2"  => $address->address_2,
			"city"  => $address->city,
			"state"  => $address->state,
			"zip"  => $address->zip
		);

		$this->db->insert('escrow_shipping_info', $data);
	}

	public function nullifyWinner($id) {
		$distribution = $this->getDistribution($id);
		$creator_fundraise_credits = $distribution->creator_fundraise_credits;
		$winner_credit = $distribution->winner_credits;

		$this->db->set('winner_credits', 0);
		$this->db->set('creator_fundraise_credits', $creator_fundraise_credits + $winner_credit);
		
		$this->db->where('id', $id);
		$this->db->update('tbl_credits_distribution');
	}

	public function removeExpiredCodes() {
		$this->db->where('TIMESTAMPDIFF(MINUTE, link_account_codes.timestamp, NOW()) >=', 10);
		$this->db->delete('link_account_codes');

		return $this->db->affected_rows();
	}
	
	public function compensateWinner($id, $compensated_credit) {
		$distribution = $this->getDistribution($id);
		$creator_credits = $distribution->creator_credits;
		$winner_credits = $distribution->winner_credits;

		$this->db->set('winner_credits', $winner_credits + $compensated_credit);
		$this->db->set('creator_credits', $creator_credits - $compensated_credit);
		
		$this->db->where('id', $id);
		$this->db->update('tbl_credits_distribution');
	}

	public function refundPlayerPayments($game, $player=null) {
		$summedPayments = $this->getSummedPayments($game["id"], $player);

		if (!empty($summedPayments)) {
			$processedPayments = 0;
			foreach($summedPayments as $payment) {
				$user_id = $payment["User_ID"];
				$refund_amount = $payment["total"];
				$currency_code = $payment["currency_code"];

				$userBalance = $this->getUserBalance($user_id)->total_credits;
				$totalCredits = round($userBalance + $refund_amount, 2);
				
				$ref_no = $this->db->select('max(ref_num) as ref_num')->get('payments')->row()->ref_num + 1;
				$notes = '$' . $refund_amount . ' refunded for ' . $game['name'];

				$data = array(
					'txn_id' => uniqid(),
					'game_id' => $game["id"],
					'User_ID' => $user_id,
					'ref_num' => $ref_no,
					'payment_gross' => 0.00,
					'total_charge' => 0.00,
					'currency_code' => $currency_code,
					'payment_status' => 'Completed',
					'Date' => gmdate('Y-m-d H:i:s'),
					'Credits' => $refund_amount,
					'Notes' => $notes,
					'Status' => 1,
					'is_paid' => 3,
					'payment_mode' => 4,
					'total_credits' => $totalCredits,
					'user_type' => 7
				);
				$this->db->insert('payments', $data);

				if ($this->db->affected_rows() > 0) {
					$processedPayments++;
				}
			}
		}

		if (count($summedPayments) == $processedPayments) {
			return true;
		}
	}

	//testing

	public function addPlayerData($game_id, $user_id, $insertdata) {
		$num = 0;

		$this->db->from('game');
		$this->db->where('id', $game_id);
		$game = $this->db->get()->row();

		$this->db->trans_start();

		$paid = $this->payToPlay($user_id, $game);
		if ($paid) {
			$played = $this->playGame($user_id, $game, $insertdata);
			if ($played) {
				$num++;
			}
		}

		$this->db->trans_complete();

		return $num;
	}

	public function addRandomPlayerData($game_id, $count) {
		$this->db->from('game');
		$this->db->where('id', $game_id);
		$game = $this->db->get()->row();

		$num = 0;
		for ($i = 0; $i < $count; $i++) {
			$this->db->trans_start();

			$user_id = rand(980, 985);

			$paid = $this->payToPlay($user_id, $game);
			if ($paid) {
				$played = $this->playGame($user_id, $game);
				if ($played) {
					$num++;
				}
			}

			$this->db->trans_complete();
		}

		return $num;
	}

	private function getTotalCredits($user_id) {
		$this->db->from('payments');
		$this->db->where('User_ID', $user_id);
		$this->db->order_by('payment_id', "DESC");
		$this->db->order_by('Date', "DESC");
		$last_payment = $this->db->get()->row();

		return (!empty($last_payment) ? $last_payment->total_credits : 0);
	}

	private function payToPlay($user_id, $game) {
		$total_credits = $this->getTotalCredits($user_id);

		$data = array(
			'txn_id' => uniqid(),
			'game_id' => 0,
			'User_ID' => $user_id,
			'ref_num' => 'this_is_a_test_'.uniqid(),
			'payment_gross' => $game->credit_cost,
			'total_charge' => 00.00,
			'currency_code' => 0,
			'payment_status' => 'Completed',
			'Date' => gmdate('Y-m-d H:i:s'),
			'Credits' => $game->credit_cost,
			'Notes' => "Test - You have been awarded $" . $game->credit_cost . " from WinWinLabs",
			'Status' => 1,
			'is_paid' => 3,
			'payment_mode' => 4,
			'total_credits' => ($total_credits + $game->credit_cost),
		);
		$this->db->insert('payments', $data);

		$total_credits += $game->credit_cost;

		$data = array(
			'txn_id' => uniqid(),
			'game_id' => $game->id,
			'User_ID' => $user_id,
			'ref_num' => 'this_is_a_test_'.uniqid(),
			'payment_gross' => $game->credit_cost,
			'total_charge' => $game->credit_cost,
			'currency_code' => 0,
			'payment_status' => 'Completed',
			'Date' => gmdate('Y-m-d H:i:s'),
			'Credits' => $game->credit_cost,
			'Notes' => "Test - $ " . $game->credit_cost . " deducted for game " . $game->name,
			'Status' => 2,
			'is_paid' => 0,
			'payment_mode' => 4,
			'total_credits' => ($total_credits - $game->credit_cost),
		);
		$this->db->insert('payments', $data);

		return (($this->db->affected_rows() > 0) ? true : false);
	}

	private function playGame($user_id, $game, $insertdata=NULL) {
		$data = array(
			'user_id' => $user_id,
			'game_type' => $game->Type,
			'created_at' => gmdate('Y-m-d H:i:s'),
			'game_id' => $game->id,
		);

		if (isset($insertdata) && !empty($insertdata)) {
			$merged = array_merge($data, $insertdata);
			$this->db->insert('game_history', $merged);
		} else {
			switch ($game->Type) {
				case 1: //puzzle
				case 2: //2048
					$steps = rand(10, 1000);
	
					$data["completed_in"] = rand(2, 600);
					$data["steps"] = $steps;
					$data["won"] = rand() % 2;
					$data["score"] = $steps * rand(0, 3);
					
					break;
				case 3: //challenge
					$this->db->from("quiz");
					$this->db->where("id", $game->quiz_id);
					$ques_count = count(json_decode($this->db->get()->row()->questions));
	
					$correct = round((rand(0, $ques_count) / $ques_count), 2);
	
					$data["completed_in"] = ($ques_count * rand(1, 30)) + rand(0, 10);
					$data["quiz_percentage"] = $correct;
					$data["score"] = (int)($correct * 100);
					break;
				case 8: //blitz
					$data["completed_in"] = 120.00;
					$data["score"] = rand(0, 2000);
	
					break;
			}

			$this->db->insert('game_history', $data);
		}

		return (($this->db->affected_rows() > 0) ? true : false);
	}

	public function getReviewGameWinners($game_id, $limit) {
		$this->db->distinct();
		$this->db->select('user_id, final_rank');
		$this->db->from('tbl_user_review');
		$this->db->where('game_id', $game_id);
		$this->db->where('reselected !=', 1);
		$this->db->where('final_rank IS NOT NULL', NULL, false);
		$this->db->order_by('final_rank', 'ASC');
		$this->db->limit($limit);

		return $this->db->get()->result();
	}
}