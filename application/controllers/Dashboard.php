<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	public function __construct () {
		parent::__construct();
		$this->load->model('charity_model');
		$this->load->model('dashboard_model');
		$this->load->library('user_agent');
		$this->load->library('template');
        $this->load->library('session');
	}

	public function index() {
		$user_id = $this->session->userdata('user_id');
		if (!$user_id) {
			redirect('login');
		}
		$tab = sanitizeInput($this->input->get("tab"), FILTER_SANITIZE_STRING);
		switch ($tab) {
			case "prizes":
				$data["tab"] = 0;
				break;
			case "player":
				$data["tab"] = 2;
				break;
			default:
				$data["tab"] = 1;
				break;
		}

		$data["search"] = sanitizeInput($this->input->get("search"), FILTER_SANITIZE_STRING);

		//get total raised by both user's fundraisers and games
		$both =  $this->dashboard_model->get_total_raised_by_fundraise_type($user_id, "overall");
		$data["overall"] = $this->getTotal($both);
		
		//get total raised by only user's fundraisers
		$fundraiser =  $this->dashboard_model->get_total_raised_by_fundraise_type($user_id, "fundraiser");
		$data['fundraiser'] = $this->getTotal($fundraiser);

		//get total raised by only user's games
		$game =  $this->dashboard_model->get_total_raised_by_fundraise_type($user_id, "game");
		$data['game'] = $this->getTotal($game);


		//get all user's games - for measuring engagement
		$data['games'] = $this->dashboard_model->getGames($user_id);

		$data['fundraiserEngagement'] =  $this->charity_model->get_all_fundraiser_list('project');
		$data['fundraiserView'] =  $this->charity_model->get_all_fundraiser_list('project');

		$all = $this->dashboard_model->getGamesSupportingFundraisers($user_id, 'all');
		$data['allSupportingGames'] = $this->getTotal($all);

		$active = $this->dashboard_model->getGamesSupportingFundraisers($user_id, 'active');
		$data['allActiveSupportingGames'] = $this->getTotal($active);

		$fundraisers = $this->dashboard_model->getFundraisers($user_id);
		$other = $this->dashboard_model->getGamesSupportingFundraisers($user_id, 'other', $fundraisers[0]['id']);
		$data['allOtherSupportingGames'] = $this->getTotal($other);

		$data['totalActivity'] = $this->dashboard_model->getTotalEngagement($user_id);
		$data['userActivity'] = $this->dashboard_model->getUserActivity($user_id);
		$data['supporterActivity'] = $this->dashboard_model->getSupporterActivity($user_id);

		$data['userFundraisers'] = $this->dashboard_model->getFundraisers($user_id);

		$createdGames = $this->dashboard_model->getGamesWhere($user_id, 'created');
		$data['totalCreated'] = $this->getTotal($createdGames);

		$liveGames = $this->dashboard_model->getGamesWhere($user_id, 'live');
		$data['totalLive'] = $this->getTotal($liveGames);

		$completedGames = $this->dashboard_model->getGamesWhere($user_id, 'completed');
		$data['totalCompleted'] = $this->getTotal($completedGames);

		$donations = $this->dashboard_model->getTotalDonated($user_id, "overall");
		$data['totalDonations'] = $this->getTotal($donations);
		
		$otherDonations = $this->dashboard_model->getTotalDonated($user_id, "other");
		$data['totalOtherDonations'] = $this->getTotal($otherDonations);

		$donationsWon = $this->dashboard_model->getTotalDonated($user_id, "won");
		$data['totalWon'] = $this->getTotal($donationsWon);

		$gamesPlayed = $this->dashboard_model->getTotalGamesPlayed($user_id, 'played');
		$data['totalGamesPlayed'] = $this->getCount($gamesPlayed);

		$fundraiserSupported = $this->dashboard_model->getTotalGamesPlayed($user_id, 'fundraiser');
		$data['totalFundraisersSupported'] = $this->getCount($fundraiserSupported);

		$gametypesPlayed = $this->dashboard_model->getTotalGamesPlayed($user_id, 'gametype');
		$data['totalGametypesPlayed'] = $this->getCount($gametypesPlayed);

		$highestScores = $this->dashboard_model->getPlayerScores($user_id, 'highest');
		$data['totalHighestScores'] = $this->getCount($highestScores);

		$lowestScores = $this->dashboard_model->getPlayerScores($user_id, 'lowest');
		$data['totalLowestScores'] = $this->getCount($lowestScores);

		$winningScores = $this->dashboard_model->getPlayerScores($user_id, 'winning');
		$data['totalWinningScores'] = $this->getCount($winningScores);

		$total = $this->dashboard_model->getTotalClaimablePrizes($user_id);
		$data["claimablePrizes"]->prizes = $this->dashboard_model->getClaimablePrizes($user_id, null, null, 10, 0);
		$data["claimablePrizes"]->deferLoading = array("filtered" => $total, "total" => $total);

		$total = $this->dashboard_model->getTotalUserClaimedPrizes($user_id);
		$data["claimedPrizes"]->prizes = $this->dashboard_model->getUserClaimedPrizes($user_id, null, null, 10, 0);
		$data["claimedPrizes"]->deferLoading = array("filtered" => $total, "total" => $total);
		
		$data['default_fundraiser']        = $this->charity_model->get_user_default_fundraise();
        $data['default_fundraiser']->Image = getImagePathSize($data['default_fundraiser']->Image,'beneficiary_info_logo');
        $data['default_fundraiser']->icon  = getFundraiseIcon($data['default_fundraiser']->fundraise_type);
        $data['default_fundraiser']->totalRaised = (is_object($this->charity_model->get_total_raised($data['default_fundraiser']->id)) ? 
                        $this->charity_model->get_total_raised($data['default_fundraiser']->id)->raised : 0);
		$data["search"] = $this->charity_model->get_beneficiary_list();

        $data['my_created_fundraiser_list'] = $this->charity_model->get_fundraise_created_byuser();
        $data['my_supported_fundraiser_list'] = $this->charity_model->get_fundraise_supported_byuser();
        
        foreach ($data['my_supported_fundraiser_list'] as $key => $value) {
            $data['my_supported_fundraiser_list'][$key]['Image'] = getImagePathSize($value['Image'], 'beneficiary_card');
            $data['my_supported_fundraiser_list'][$key]['icon']  = getFundraiseIcon($value['fundraise_type']);
        }

        foreach ($data['my_created_fundraiser_list'] as $key => $value) {
            $data['my_created_fundraiser_list'][$key]['Image'] = getImagePathSize($value['Image'], 'beneficiary_card');
            $data['my_created_fundraiser_list'][$key]['icon']  = getFundraiseIcon($value['fundraise_type']);
        }

		$this->template->set_layout(DEFAULT_LAYOUT)->build('dashboard/index', $data);
	}

	private function getTotal($data) {
		$total = 0;

		foreach ($data as $key => $value) {
			$keys = array_keys($value); 
			$num = $value[$keys[count($keys)-1]];

			if (is_numeric($num)) {
				$total += $num;
			}
		}

		return $total;
	}

	private function getCount($data) {
		$count = count($data) -1;

		return $count;
	}

	public function getDashboardCharts() {
		$user_id = $this->session->userdata('user_id');

		$dataType = sanitizeInput($this->input->get("datatype"), FILTER_SANITIZE_STRING);
		$filter = sanitizeInput($this->input->get("filter"), FILTER_SANITIZE_STRING);

		switch ($dataType) {
			case 'funds':
				$data = $this->dashboard_model->getTotalDonated($user_id, $filter);
				break;
			case 'raised':
				$data = $this->dashboard_model->get_total_raised_by_fundraise_type($user_id, $filter);
				break;
			case 'engagement':
				$game_id = sanitizeInput($this->input->get("game_id"), FILTER_VALIDATE_INT);
				$charity_id = sanitizeInput($this->input->get("charity_id"), FILTER_VALIDATE_INT);

				$data = $this->dashboard_model->getGameEngagement($user_id, $game_id);
				break;
			case 'supporters':
				$fundraiser = sanitizeInput($this->input->get("fundraiser"), FILTER_VALIDATE_INT);

				$data = $this->dashboard_model->getGamesSupportingFundraisers($user_id, $filter, $fundraiser);
				break;
			case 'games':
				$data = $this->dashboard_model->getGamesWhere($user_id, $filter);
				break;
			case 'playerGames':
				$data = $this->dashboard_model->getTotalGamesPlayed($user_id, $filter);
				break;
			case 'score':
				$data = $this->dashboard_model->getPlayerScores($user_id, $filter);
				break;
		}

		echo json_encode($data);
	}

	public function getUserActivity() {
		$limit = sanitizeInput($this->input->get("limit"), FILTER_VALIDATE_INT);

		$noti = $this->dashboard_model->getUserActivity($this->session->userdata('user_id'), $limit);

		echo json_encode($noti);
	}

	public function getSupporterActivity() {
		$limit = sanitizeInput($this->input->get("limit"), FILTER_VALIDATE_INT);

		$noti = $this->dashboard_model->getSupporterActivity($this->session->userdata('user_id'), $limit);

		echo json_encode($noti);
	}

	public function getClaimablePrizes() {
		$user_id = $this->session->userdata('user_id');

		$status = sanitizeInput($this->input->get("statusfilter"), FILTER_VALIDATE_INT);
		$status_filtered = array();

		$draw = sanitizeInput($this->input->get("draw"), FILTER_VALIDATE_INT);

        $limit = sanitizeInput($this->input->get("length"), FILTER_VALIDATE_INT);
		$offset = sanitizeInput($this->input->get("start"), FILTER_VALIDATE_INT);
		$search = sanitizeInput($this->input->get("search[value]", true), FILTER_SANITIZE_STRING);

		$by = sanitizeInput($this->input->get("order[0][column]"), FILTER_VALIDATE_INT);
		$order = array("by" => sanitizeInput($this->input->get("columns[" . $by . "][data]"), FILTER_SANITIZE_STRING), "arrange" => sanitizeInput($this->input->get("order[0][dir]"), FILTER_SANITIZE_STRING));
		
		$total = $this->dashboard_model->getTotalClaimablePrizes($user_id, $status);
		$prizes = $this->dashboard_model->getClaimablePrizes($user_id, $search, $order, $status);

		foreach ($prizes as $key=>$prize) {
			$prizeStatus = $this->getPrizeStatus($prize);
			if ($status != 0 && $status != $prizeStatus) { //blacklist nonmatching statuses
				$status_filtered[$key] = $prize;
				continue;
			}

			$prize->prizestatus = $prizeStatus;
			$prize->image_proof = $this->dashboard_model->getShippingImageProof($prize->id);

			if ($user_id == $prize->shippee_id) {
				if (!isset($prize->received)) {
					$prize->allowReceived = true;
				}
			}

			unset($prize->status);
			unset($prize->shippee_id);
		}

		$prizes = array_diff_key($prizes, $status_filtered); //keep only nonblacklisted

		$data = array(
			"draw" => $draw,
			"recordsTotal" => $total,
			"recordsFiltered" => count($prizes),
			"data" => array_slice($prizes, $offset, $limit)
		);

		echo json_encode($data);
	}

	public function getClaimedPrizes() {
		$user_id = $this->session->userdata('user_id');

		$status = sanitizeInput($this->input->get("statusfilter"), FILTER_VALIDATE_INT);;
		$status_filtered = array();

		$draw = sanitizeInput($this->input->get("draw"), FILTER_VALIDATE_INT);

        $limit = sanitizeInput($this->input->get("length"), FILTER_VALIDATE_INT);
		$offset = sanitizeInput($this->input->get("start"), FILTER_VALIDATE_INT);
		$search = sanitizeInput($this->input->get("search[value]", true), FILTER_SANITIZE_STRING);

		$by = sanitizeInput($this->input->get("order[0][column]"), FILTER_VALIDATE_INT);
		$order = array("by" => sanitizeInput($this->input->get("columns[" . $by . "][data]"), FILTER_SANITIZE_STRING), "arrange" => sanitizeInput($this->input->get("order[0][dir]"), FILTER_SANITIZE_STRING));
		
		$total = $this->dashboard_model->getTotalUserClaimedPrizes($user_id, $status);
		$prizes = $this->dashboard_model->getUserClaimedPrizes($user_id, $search, $order, $status);

		foreach ($prizes as $key=>$prize) {
			$prizeStatus = $this->getPrizeStatus($prize);
			if ($status != 0 && $status != $prizeStatus) { //blacklist nonmatching statuses
				$status_filtered[$key] = $prize;
				continue;
			}

			$prize->prizestatus = $prizeStatus;
			$prize->image_proof = $this->dashboard_model->getShippingImageProof($prize->id);

			if ($user_id == $prize->creator_id) {
				if ($prize->received != 1) {
					$prize->allowUpdate = true;
				}
			}

			unset($prize->status);
			unset($prize->shipper_id);
		}

		$prizes = array_diff_key($prizes, $status_filtered); //keep only nonblacklisted

		$data = array(
			"draw" => $draw,
			"recordsTotal" => $total,
			"recordsFiltered" => count($prizes),
			"data" => array_slice($prizes, $offset, $limit)
		);

		echo json_encode($data);
	}

	private function getPrizeStatus($prize) {
		if (!isset($prize->confirmed)) { //pending
			return 1;
		} else if ($prize->confirmed == 1 && !isset($prize->processed)) { //claimed - claimable
			return 2;
		} else if ($prize->confirmed == 1 && $prize->processed == 1 && !isset($prize->received)) { //processed
			return 3;
		} else if ($prize->confirmed == 1 && $prize->processed == 1 && $prize->received == 1) { //received
			return 4;
		} else if ($prize->confirmed == 1 && $prize->processed == 1 && $prize->received == 0) { //not received
			return 5;
		} else if ($prize->review == 1 && !isset($prize->approved)) { //under review
			return 6;
		} else if ($prize->status == 2) { //completed
			return 7;
		} else if ($prize->status == 3) { //failed
			return 8;
		} else {
			return 0;
		}
	}

	public function getShippingInfo() {
		$user_id = $this->session->userdata('user_id');

		$id = sanitizeInput($this->input->get("id"), FILTER_VALIDATE_INT);
		$shippingInfo = $this->dashboard_model->getShippingInfo($id, $user_id);

		if (isset($shippingInfo)) {
			$shippingInfo->image_proof = $this->dashboard_model->getShippingImageProof($id);

			if ($user_id == $shippingInfo->shipper_id) {
				if ($shippingInfo->received != 1) {
					$shippingInfo->allowUpdate = true;
				}
			} else if ($user_id == $shippingInfo->shippee_id) {
				if ($shippingInfo->processed == 1 && $shippingInfo->received != 1) {
					$processedAt = DateTime::createFromFormat('Y-m-d H:i:s', $shippingInfo->processed_at);
					$processedAt->modify("+ 3 days 0 hours 0 minutes");
					if (isDatePassed($processedAt) && !isset($shippingInfo->received)) {
						$shippingInfo->allowReceived = true;
					}
				}
			}
			
			unset($shippingInfo->shipper_id);
			unset($shippingInfo->shippee_id);

			echo json_encode(array("status" => "success", "info" => $shippingInfo));
		} else {
			echo json_encode(array("status" => "failed", "message" => "could not get info"));
		}
		
	}

	public function updateTracking() {
		$user_id = $this->session->userdata('user_id');

		$id = sanitizeInput($this->input->post("id"), FILTER_VALIDATE_INT);
		$proof = sanitizeInput($this->input->post("proof"), FILTER_VALIDATE_INT);

		switch ($proof) {
			case 0: //tracking
				$provider = sanitizeInput($this->input->post("provider", true), FILTER_SANITIZE_STRING);
				$num = sanitizeInput($this->input->post("num"), FILTER_SANITIZE_STRING);

				$update = $this->dashboard_model->updateTracking($id, $user_id, $provider, $num);
				break;
			case 1: //images
				$images = sanitizeInputArray(json_decode($this->input->post("images", true)), FILTER_SANITIZE_URL);

				$update = $this->dashboard_model->updateImageProof($id, $user_id, $images);
				break;
			default:
				break;
		}

		if ($update) {
			echo json_encode(array("status" => "success", "message" => "update successful"));
		} else {
			echo json_encode(array("status" => "failed", "message" => "update failed"));
		}
	}

	public function updateReceived() {
		$user_id = $this->session->userdata('user_id');

		$id = sanitizeInput($this->input->post("id"), FILTER_VALIDATE_INT);
		$received = sanitizeInput($this->input->post("received"), FILTER_VALIDATE_INT) ? 1 : 0;

		$update = $this->dashboard_model->updateReceived($id, $user_id, $received);
		if ($update) {
			echo json_encode(array("status" => "success", "message" => "update successful"));
		} else {
			echo json_encode(array("status" => "failed", "message" => "update failed"));
		}
	}
}