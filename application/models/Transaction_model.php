<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transaction_model extends CI_Model {

	public  function get_sum($user_id = '') {
		if (empty($user_id)) {
			$user_id = $this->session->userdata('user_id');
		}
		
		$this->db->select('sum(Credits) as credits_cr');
		$this->db->from('payments');
		$this->db->where(['status' => '1' , 'user_ID' => $user_id]);	
		$this->db->where_not_in('payment_status', array('pending', 'failed'));
		$totalcredits_cr = $this->db->get()->row()->credits_cr;
		
		$this->db->select('sum(Credits) as credits_cr');
		$this->db->from('payments');
		$this->db->where(['status' => '2' , 'user_ID' => $user_id]);	
		$this->db->where_not_in('payment_status', array('pending', 'failed'));
		$totalcredits_db = $this->db->get()->row()->credits_cr;

		return round($totalcredits_cr-$totalcredits_db,2);
	}

	public function get_withdrawable_sum($user_id = '') {
		if (empty($user_id)) {
			$user_id = $this->session->userdata('user_id');
		}
		
		$this->db->select('sum(Credits) as credits_cr');
		$this->db->from('payments');
		$this->db->where(['status' => '1' , 'user_ID' => $user_id, 'payment_type' => '1']);	
		$this->db->where_not_in('payment_status', array('pending', 'failed'));
		$totalcredits_cr = $this->db->get()->row()->credits_cr;
		
		$this->db->select('sum(Credits) as credits_cr');
		$this->db->from('payments');
		$this->db->where(['status' => '2' , 'user_ID' => $user_id, 'payment_type' => '1']);	
		$this->db->where_not_in('payment_status', array('pending', 'failed'));
		$totalcredits_db = $this->db->get()->row()->credits_cr;

		return round($totalcredits_cr-$totalcredits_db,2);
	}

	public function get_donated_sum($user_id = '') {
		if (empty($user_id)) {
			$user_id = $this->session->userdata('user_id');
		}
		
		$this->db->select('sum(Credits) as credits_cr');
		$this->db->from('payments');
		$this->db->where(['status' => '1' , 'user_ID' => $user_id, 'payment_type' => '2']);	
		$this->db->where_not_in('payment_status', array('pending', 'failed'));
		$totalcredits_cr = $this->db->get()->row()->credits_cr;
		
		$this->db->select('sum(Credits) as credits_cr');
		$this->db->from('payments');
		$this->db->where(['status' => '2' , 'user_ID' => $user_id, 'payment_type' => '2']);	
		$this->db->where_not_in('payment_status', array('pending', 'failed'));
		$totalcredits_db = $this->db->get()->row()->credits_cr;

		return round($totalcredits_cr-$totalcredits_db,2);
	}

	public function getUserLastBalance() {
		if (empty($user_id)) {
			$user_id = $this->session->userdata('user_id');
		}

		$this->db->select('total_credits');
		$this->db->where('User_ID', $user_id);
		$this->db->order_by('payment_id',"desc");
		$this->db->limit(1);
		$userBalance = $this->db->get('payments')->row()->total_credits;

		return $userBalance;
	}

	public function get_all_transactions($user_id=null, $search=null, $order=null, $limit=null, $offset=null) {
		$this->db->select('t1.Date, t1.ref_num, t1.User_ID as user_id, t2.firstname, t2.lastname, t2.email, t1.payment_status, t1.is_paid, t1.payment_mode, t1.game_id, t3.name as game_name, t1.user_type, t1.Notes, t1.Status, t1.Credits, t1.total_charge, t1.total_credits, t1.is_deductible');
		$this->db->from('payments as t1');
			
		$this->db->join('tbl_users as t2', "t1.User_ID=t2.user_id", "left");
		$this->db->join('game as t3', "t1.game_id=t3.id", "left");

		if (isset($user_id)) {
			$this->db->where('t1.User_ID', $user_id);
		}

		if (isset($search) && $search != "") {
			$this->db->group_start();
			$this->db->like("t1.ref_num", $search);
			$this->db->or_like(array("t1.Notes" => $search, "t1.User_ID" => $search, "t2.firstname" => $search, "t2.lastname" => $search, "t2.email" => $search));
			$this->db->group_end();
		}

		if (!empty($order["by"]) && !empty($order["arrange"])) {
            $this->db->order_by($order["by"], $order["arrange"]);
        } else {
			$this->db->order_by('Date','desc');
		}
		
		if (isset($limit) && isset($offset)) {
			$this->db->limit($limit, $offset);
		}


		$payments = $this->db->get()->result_array();

		foreach ($payments as $key => &$payment) {

			switch ($payment['user_type']) {
				case 1:
					$this->db->select('t5.name as creator_fundraise_name, t4.creator_fundraise_id');
					$this->db->from('tbl_credits_distribution as t4');

					$this->db->join('charity as t5','t5.id=t4.creator_fundraise_id');
					$this->db->where(array('t4.creator_id' => (int)$payment["user_id"], 't4.game_id' => (int)$payment["game_id"]));

					$result = $this->db->get()->row();

					$payment["creator_fundraise_id"] = $result->creator_fundraise_id;
					$payment["creator_fundraise_name"] = $result->creator_fundraise_name;
					break;
				case 2:
				case 4:
					$this->db->select('t4.winner_type, t4.creator_fundraise_id, t5.name as creator_fundraise_name, t4.winner_fundraise_id, t6.name as winner_fundraise_name, t7.final_rank');
					$this->db->from('tbl_credits_distribution as t4');
					
					$this->db->join('charity as t5','t5.id=t4.creator_fundraise_id','left');
					$this->db->join('charity as t6','t6.id=t4.winner_fundraise_id','left');
					$this->db->join('tbl_user_review as t7','t7.user_id=t4.winner_id and t7.game_id = t4.game_id','left');

					$this->db->where(array('t4.game_id' => (int)$payment["game_id"]));

					$result = $this->db->get()->row();

					$payment["creator_fundraise_id"] = $result->creator_fundraise_id;
					$payment["creator_fundraise_name"] = $result->creator_fundraise_name;
					$payment["winner_fundraise_id"] = $result->winner_fundraise_id;
					$payment["winner_fundraise_name"] = $result->winner_fundraise_name;

					$payment["winner_type"] = $result->winner_type;
					$payment["final_rank"] = $result->final_rank;
					break;
				case 5:
					$this->db->select('t4.final_rank');
					$this->db->from('tbl_user_review as t4');
					
					$this->db->join('','=t1.User_ID and t4.game_id = t1.game_id','left');

					$this->db->where(array('t4.user_id' => (int)$payment['user_id'], 't4.game_id' => (int)$payment["game_id"]));

					$result = $this->db->get()->row();

					$payment["final_rank"] = $result->final_rank;
					break;
			}
		}

		return $payments;
	}

	public function getTotalTransactions($user_id=null) {
		$this->db->distinct();
		$this->db->from('payments as t1');
			
		$this->db->join('tbl_users as t2', "t1.User_ID=t2.user_id", "left");
		$this->db->join('game as t3', "t1.game_id=t3.id", "left");

		if (isset($user_id)) {
            $this->db->where('t1.user_id', $user_id);
        }

		return $this->db->get()->num_rows();
	}

	public function getAllDistributions($where, $search=null, $order=null, $limit=null, $offset=null) {
		$this->db->select('tcd.*, t0.name as game_name, t1.username as creator_name, t2.username as winner_name, t3.name as creator_charity_name, t4.name as winner_charity_name');
		$this->db->from('tbl_credits_distribution as tcd');
			
		$this->db->join('game as t0', "tcd.game_id=t0.id", "left");
		$this->db->join('tbl_users as t1', "tcd.creator_id=t1.user_id", "left");
		$this->db->join('tbl_users as t2', "tcd.winner_id=t2.user_id", "left");
		$this->db->join('charity as t3', "tcd.creator_fundraise_id=t3.id", "left");
		$this->db->join('charity as t4', "tcd.winner_fundraise_id=t4.id", "left");

		switch ($where) {
			case 1: //processed
				$this->db->where('tcd.status', "2");
				break;
			case 2: //reviewable
				$this->db->where(array('tcd.review' => "1", "tcd.approved is NULL" => null));
				break;
			case 3: //approved
				$this->db->where(array('tcd.review' => "1", "tcd.approved" => 1));
				break;
			case 3: //not approved
				$this->db->where(array('tcd.review' => "1", "tcd.approved" => 0));
				break;
			default: //all
				break;
		}
		
		if (isset($search) && $search != "") {
            $this->db->group_start();
            $this->db->like("tcd.game_id", $search);
            $this->db->or_like(
				array(
					"t0.game_name" => $search,
					"tcd.creator_id" => $search,
					"t1.username" => $search,
					"tcd.winner_id" => $search,
					"t2.username" => $search,
					"tcd.creator_fundraise_id" => $search,
					"t3.name" => $search,
					"tcd.winner_fundraise_id" => $search,
					"t4.name" => $search,
					"tcd.status" => $search,
					"tcd.review" => $search
				)
			);
            $this->db->group_end();
        }

        if (isset($order)) {
            $this->db->order_by($order["by"], $order["arrange"]);
        } else {
            $this->db->order_by('tcd.review', 'DESC'); 
            $this->db->order_by('tcd.approved', 'ASC'); 
            $this->db->order_by('tcd.status', 'ASC'); 
        }

        if (isset($limit) && isset($offset)) {
            $this->db->limit($limit, $offset);
        }

		$result = $this->db->get()->result();
		return $result;
	}

	public function getTotalDistributions() {
		$this->db->from('tbl_credits_distribution as tcd');
			
		$this->db->join('game as t0', "tcd.game_id=t0.id", "left");
		$this->db->join('tbl_users as t1', "tcd.creator_id=t1.user_id", "left");
		$this->db->join('tbl_users as t2', "tcd.winner_id=t2.user_id", "left");
		$this->db->join('charity as t3', "tcd.creator_fundraise_id=t3.id", "left");
		$this->db->join('charity as t4', "tcd.winner_fundraise_id=t4.id", "left");

		return $this->db->get()->num_rows();
	}

	public function getDistributionDetails($id) {
		$this->db->from('tbl_credits_distribution as tcd');
		$this->db->where("tcd.id", $id);
		$distribution = $this->db->get()->row();

		$this->db->from('game as t0');
		$this->db->where("t0.id", $distribution->game_id);
		$game = $this->db->get()->row();

		$this->db->from('tbl_users as t1');
		$this->db->where("t1.user_id", $distribution->creator_id);
		$creator = $this->db->get()->row();

		$this->db->from('tbl_users as t2');
		$this->db->where("t2.user_id", $distribution->winner_id);
		$winner = $this->db->get()->row();

		$this->db->from('charity as t3');
		$this->db->where("t3.id", $distribution->creator_fundraise_id);
		$creator_fundraiser = $this->db->get()->row();

		$this->db->from('charity as t4');
		$this->db->where("t4.id", $distribution->winner_fundraise_id);
		$winner_fundraiser = $this->db->get()->row();

		$this->db->from('escrow_shipping_info as esi');
		$this->db->where("esi.shippee_id", $distribution->winner_id);
		$shipping = $this->db->get()->row();


		$data = array(
			"distribution" => $distribution,
			"game" => $game,
			"creator" => $creator,
			"winner" => $winner,
			"creator_fundraiser" => $creator_fundraiser,
			"winner_fundraiser" => $winner_fundraiser,
			"shipping" => $shipping,
			"refund" => $this->getDistributionRefundOptions($distribution->game_id)
		);

		return $data;
	}

	public function getDistributionRefundOptions($game_id) {
		$this->db->select("game_id, sum(case when tcd.status = 2 then 1 else 0 end) as processed");
		$this->db->from("tbl_credits_distribution as tcd");
		$this->db->group_by("game_id");

		$result = $this->db->get()->row();

		$prize = ($result->winner_credits == 0) ? true : false;
		$processed = $result->processed;

		$complete = true;
		if ($processed > 0) {
			$complete = false;
		}

		return (object) array("Complete" => $complete, "Partial" => true, "Comp" => $prize, "Nullify" => true);
	}

	public function insert_payout_data($data, $cashoutMethod) {
		$this->db->insert('payments', $data);
		if ($cashoutMethod == 'paypal') {
			$q = $this->db->get_where('payments', array('payment_id' => $this->db->insert_id()));
			return (object) array("Complete" => true, "status" =>  $q->row()->payment_status, 
								"txnId" => $q->row()->txn_id, "userId" => $q->row()->User_ID);
		} else {
			return true;
		}
	}
}