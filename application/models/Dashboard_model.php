<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_model extends CI_Model {
	//requires optimization

	public function getGames($user_id) {
		$this->db->select("game_id");
		$this->db->from('user_game_charity');
		$this->db->where('user_id', $user_id);
		$this->db->order_by('game_id', "DESC");

		return $this->db->get()->result_array();
	}

	public function getFundraisers($user_id) {
		$this->db->select("charity.id, charity.name");
		$this->db->from('charity');
		$this->db->where('user_id', $user_id);
		$this->db->order_by('charity.name', "asc");

		$query = $this->db->get()->result_array();

		return $query;
	}

	public function getGamesWhere($user_id, $filter) {
		$union = "'Donated', '#' from dual UNION (";

		$this->db->select($union . " select name, count(id) as '#'");
		$this->db->from('game');

		switch ($filter) {
			case 'created':
				$this->db->where('user_id', $user_id);
				break;
			case 'live':
				$this->db->where(['user_id'=> $user_id, 'Publish' => "Live", "Status !=" => "Completed"]);
				break;
			case 'completed':
				$this->db->where(['user_id'=> $user_id, "Status" => "Completed"]);
				break;
		}

		$this->db->group_by('name');
		$this->db->order_by('name', "asc");

		$query = $this->db->get_compiled_select() . ")";

		return $this->db->query($query)->result_array();
	}

	public function getTotalGamesPlayed($user_id, $filter) {
		switch ($filter) {
			case 'played':
				$union = "'Game', '#' from dual UNION (";
				$this->db->select($union . " select game.name, count(game.name) as total");
				break;
			case 'fundraiser':
				$union = "'Fundraiser', '#' from dual UNION (";
				$this->db->select($union . " select charity.name, count(charity.name) as total");
				break;
			case 'gametype':
				$union = "'Gametype', '#' from dual UNION (";
				$this->db->select($union . " select gametype.name, count(gametype.name) as total");
				break;
		}

		if ($filter == 'fundraiser') {
			$this->db->from('user_game_attempts as uga');
			$this->db->join('game_history as gh', 'uga.game_session_id=gh.game_session_id');
			$this->db->join('game', 'uga.game_id=game.id');
			$this->db->join('gametype', 'gametype.id=game.Type');

			$this->db->join('user_game_charity as ugc', 'ugc.game_id=game.id', 'left');
			$this->db->join('charity', 'ugc.charity_id=charity.id');

			$this->db->group_by('gh.id');
		} else {
			$this->db->from('user_game_attempts as uga');
			$this->db->join('game_history as gh', 'uga.game_session_id=gh.game_session_id');

			$this->db->join('game', 'uga.game_id=game.id', 'left');
			$this->db->join('gametype', 'gametype.id=game.Type', 'left');
	
			$this->db->where('uga.user_id', $user_id);
	
			$this->db->group_by('gh.id');
		}

		$query = $this->db->get_compiled_select() . ")";
		
		return $this->db->query($query)->result_array();
	}

	public function getTotalDonated($user_id, $filter) {
		if ($filter == 'overall') {
			$union = "'Donated', '#' from dual UNION (";
			$this->db->select($union . " select IFNULL(charity.name, '---') as charity, CONVERT(sum(credits), char(255)) as count");
		} else if ($filter == 'other') {
			$union = "'Donated', '#' from dual UNION (";
			$this->db->select($union . " select IFNULL(charity.name, '---') as charity, credits as count");
		} else if ($filter == 'won') {
			$union = "'Won', '#' from dual UNION (";
			$this->db->select($union . " select IFNULL(game.name, '---') as charity, credits as count");
		}
		
		$this->db->from('payments');
		$this->db->join('user_game_charity as ugc', 'ugc.game_id=payments.game_id', 'left');
		$this->db->join('charity', 'charity.id=ugc.charity_id', 'left');
		$this->db->join('game', 'game.id=ugc.game_id', 'left');


		if ($filter == 'overall') {
			$this->db->where(['payments.user_id' => $user_id, 'payments.credits >' => 0]);
			$this->db->group_by("charity.name");
		} else if ($filter == 'other') {
			$this->db->where(['payments.user_id' => $user_id, 'payments.credits >' => 0, 'game.user_id !=' => $user_id, 'charity.user_id !=' => $user_id]);
		} if ($filter == 'won') {
			$this->db->where(['payments.user_id' => $user_id, 'payments.credits >' => 0, 'payments.user_type' => 1]);
			
		}

		$query = $this->db->get_compiled_select() . ")";

		return $this->db->query($query)->result_array();
	}

	public function getTotalEngagement($user_id, $interval="") {
		$this->db->select("COALESCE(sum(count), 0) as traffic from (SELECT `uga`.`user_id`, count(uga.game_id) as count, date(gh.created_at) 
		FROM `user_game_attempts` as uga join `game_history` as gh on `uga`.`game_session_id`=`gh`.`game_session_id`
		WHERE `uga`.`user_id` = " . $user_id . " and gh.created_at < now() and gh.created_at > (now() - INTERVAL 7 Day) GROUP BY date(gh.created_at) order by gh.created_at desc) as traffic");

		$query = $this->db->get()->result_array();

		return $query;
	}

	public function getPlayerScores($user_id, $filter) {
		$union = "'Game', 'score' from dual UNION (";

		$this->db->select($union . " select game.name, score");
		$this->db->from('user_game_attempts as uga');
		$this->db->join('game_history as gh', 'uga.game_session_id=gh.game_session_id');

		$this->db->join('game', "game.id=uga.game_id");

		switch ($filter) {
			case 'highest':
				$this->db->where('uga.user_id', $user_id);
				$this->db->order_by('score', "desc");
				break;
			case 'lowest':
				$this->db->where('uga.user_id', $user_id);
				$this->db->order_by('score', "asc");
				break;
			case 'winning':
				$this->db->where(['uga.user_id'=> $user_id, "won" => 1]);
				$this->db->order_by('score', "desc");
				break;
		}

		$this->db->limit(10);

		$query = $this->db->get_compiled_select() . ")";

		return $this->db->query($query)->result_array();
	}

	public function getGameEngagement($user_id, $game_id="all", $charity_id="all") {
		$union = "'Date', '#' from dual UNION (";
		$this->db->select($union . "select date(created_at) as date, count(uga.game_id) as count");

		$this->db->from('user_game_attempts as uga');
		$this->db->join('game_history as gh', 'uga.game_session_id=gh.game_session_id');
		$this->db->join('user_game_charity as ugc', 'uga.game_id=ugc.game_id');
		$this->db->join('charity', 'charity.id=ugc.charity_id');

		if (($game_id != "all" && $game_id != "none") && ($charity_id != "all" && $charity_id != "none")) {
			$this->db->where(['ugc.user_id'=> $user_id, 'charity.user_id' => $user_id]);
		} else {
			if ($game_id == "all" && $charity_id == "all") {
				$this->db->where('ugc.user_id', $user_id);
			} else if ($game_id == "all" && $charity_id == "none") {
				$this->db->where(['ugc.user_id' => $user_id, 'charity.user_id !=' => $user_id]);
			} else {
				$this->db->where(['ugc.user_id !=' => $user_id, 'charity.user_id' => $user_id]);
			}
		}
		
		$this->db->group_by('date(created_at)');
		$this->db->order_by('created_at', "DESC");
		$this->db->limit(7, 0);

		$query = $this->db->get_compiled_select() . ")";

		return $this->db->query($query)->result_array();
	}

	public function getGamesSupportingFundraisers($user_id, $filter="all", $fundraiser=null) {
		$union = "";

		if ($filter == "other") {
			$union = "'Game', '#' from dual UNION (";
			$this->db->select($union . " select game.name as game, COALESCE(count(ugc.charity_id),0) as count");
		} else {
			$union = "'Charity', '#' from dual UNION (";
			$this->db->select($union . " select charity.name as fundraiser, COALESCE(count(ugc.charity_id),0) as count");
		}
		

		$this->db->from('user_game_charity as ugc');
		$this->db->join('charity', 'ugc.charity_id=charity.id');
		$this->db->join('game', 'game.id=ugc.game_id');

		if ($filter == "all") {
			$this->db->where(["charity.user_id" => $user_id]);
			$this->db->group_by('charity.name');
		} else if ($filter == "active") {
			$this->db->where(["charity.user_id" => $user_id, "game.Publish" => "Live", "game.Status" => "Running"]);
			$this->db->group_by('charity.name');
		} else {
			if (isset($fundraiser)) {
				$this->db->where(["charity.id" => $fundraiser]);
			} else {
				$this->db->where(["charity.user_id" => $user_id, "game.user_id !="=> $user_id]);
			}
			
			$this->db->group_by('game.name');
		}

		$query = $this->db->get_compiled_select() . ")";

		return $this->db->query($query)->result_array();
	}

	public function getTotalClaimablePrizes($user_id) {
		$this->db->from('tbl_credits_distribution');
		$this->db->join('game', "tbl_credits_distribution.game_id=game.id");
		$this->db->where("winner_id", $user_id);
		$this->db->where("confirmed !=", 2);
		$this->db->where("confirmed is NOT NULL", NULL, false);

		return $this->db->get()->num_rows();
	}

	public function getClaimablePrizes($user_id, $search=null, $order=null, $limit=null, $offset=null) {
		$this->db->select("esi.id, tcd.winner_id, game.name, game.prize_title, game.slug, tcd.status, tcd.confirmed, esi.fullname, esi.address_1, esi.address_2, esi.city, esi.state, esi.zip, esi.shipping_provider, esi.tracking_num, esi.processed, esi.received");
        $this->db->from('tbl_credits_distribution as tcd');
        $this->db->join('game', "tcd.game_id=game.id");
        $this->db->join('escrow_shipping_info as esi', "esi.game_id=tcd.game_id and esi.shippee_id=tcd.winner_id", "left");
		$this->db->where("tcd.winner_id", $user_id);
		$this->db->where("confirmed !=", 2);
		$this->db->where("confirmed is NOT NULL", NULL, false);
        
        if (isset($search) && $search != "") {
            $this->db->group_start();
            $this->db->like("game.name", $search);
            $this->db->or_like(array("prize_title" => $search, "prize_description" => $search, "prize_type" => $search, "prize_specification" => $search));
            $this->db->group_end();
        }

        if (isset($order)) {
			if ($order["by"] == "confirmed") {
				$this->db->order_by("esi.received", $order["arrange"]);
				$this->db->order_by("esi.processed", $order["arrange"]);
				$this->db->order_by("tcd.confirmed", $order["arrange"]);
			} else {
				$this->db->order_by($order["by"], $order["arrange"]);
			}
        } else {
			$this->db->order_by("tcd.confirmed", "ASC");
			$this->db->order_by("esi.processed", "ASC");
			$this->db->order_by("esi.received", "ASC");
		}

		$this->db->order_by("esi.processed", "ASC");

        if (isset($limit) && isset($offset)) {
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get();
        return $query->result();
	}

	public function getTotalUserClaimedPrizes($user_id) {
		$this->db->from('tbl_credits_distribution');
		$this->db->where("creator_id", $user_id);
		$this->db->join('game', "tbl_credits_distribution.game_id=game.id");
		
		$this->db->group_start();
			$this->db->group_start();
			$this->db->where("confirmed !=", 2);
			$this->db->where("confirmed is NOT NULL", NULL, false);
			$this->db->group_end();

			$this->db->or_group_start();
			$this->db->where("confirmed", 2);
			$this->db->where("review", 1);
			$this->db->group_end();
		$this->db->group_end();

		return $this->db->get()->num_rows();
	}

	public function getUserClaimedPrizes($user_id, $search=null, $order=null, $limit=null, $offset=null) {
		$this->db->select("esi.id, tcd.creator_id, game.name, game.prize_title, game.slug, tcd.status, tcd.confirmed, tcd.review, tcd.approved, esi.fullname, esi.address_1, esi.address_2, esi.city, esi.state, esi.zip, esi.shipping_provider, esi.tracking_num, esi.processed, esi.received");
        $this->db->from('tbl_credits_distribution as tcd');
        $this->db->join('game', "tcd.game_id=game.id");
		$this->db->join('escrow_shipping_info as esi', "esi.game_id=tcd.game_id and esi.shippee_id=tcd.winner_id", "left");
		$this->db->where("tcd.creator_id", $user_id);

		$this->db->group_start();
			$this->db->group_start();
			$this->db->where("confirmed !=", 2);
			$this->db->where("confirmed is NOT NULL", NULL, false);
			$this->db->group_end();

			$this->db->or_group_start();
			$this->db->where("confirmed", 2);
			$this->db->where("review", 1);
			$this->db->group_end();
		$this->db->group_end();
		
        if (isset($search) && $search != "") {
            $this->db->group_start();
            $this->db->like("game.name", $search);
            $this->db->or_like(array("prize_title" => $search, "prize_description" => $search, "prize_type" => $search, "prize_specification" => $search));
            $this->db->group_end();
        }

        if (isset($order)) {
			if ($order["by"] == "confirmed") {
				$this->db->order_by("esi.received", $order["arrange"]);
				$this->db->order_by("esi.processed", $order["arrange"]);
				$this->db->order_by("tcd.confirmed", $order["arrange"]);
			} else {
				$this->db->order_by($order["by"], $order["arrange"]);
			}
        } else {
			$this->db->order_by("tcd.confirmed", "ASC");
			$this->db->order_by("esi.processed", "ASC");
			$this->db->order_by("esi.received", "ASC");
		}

        if (isset($limit) && isset($offset)) {
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get();
        return $query->result();
	}

	public function getShippingInfo($id, $user_id) {
		$this->db->select("esi.shipper_id, esi.shippee_id, esi.fullname, esi.address_1, esi.address_2, esi.city, esi.state, esi.zip, esi.shipping_provider, esi.tracking_num, esi.processed, esi.processed_at, esi.received");
        $this->db->from('escrow_shipping_info as esi');
		$this->db->where("esi.id", $id);
		$this->db->group_start();
			$this->db->where("esi.shipper_id", $user_id);
			$this->db->or_where("esi.shippee_id", $user_id);
		$this->db->group_end();

        $query = $this->db->get();
        return $query->row();
	}

	public function getShippingImageProof($id) {
		$this->db->select('image');
		$this->db->from('escrow_shipping_image_proof');
		$this->db->where('escrow_id', $id);

		$query = $this->db->get();
        return $query->result_array();
	}

	public function updateTracking($id, $user_id, $provider, $num) {
		$data = array(
			"shipping_provider" => $provider,
			"tracking_num" => $num,
			"processed" => 1,
			"processed_at" => gmdate("Y-m-d H:i:s")
		);

		$this->db->where(array('shipper_id' => $user_id, 'id' => $id));
		$this->db->update('escrow_shipping_info', $data);

		if ($this->db->affected_rows() > 0) {
			return true;
		}

		return false;
	}

	public function updateImageProof($id, $user_id, $images) {
		$this->db->where("escrow_id", $id);
		$exist = $this->db->get("escrow_shipping_image_proof")->num_rows();

		$data = array();
		foreach($images as $image) {
			array_push($data, array("escrow_id" => $id, "image" => $image));
		}

		if ($exist > 0) {
			$this->db->where("escrow_id", $id);
			$this->db->delete("escrow_shipping_image_proof");
		}
		
		$this->db->insert_batch('escrow_shipping_image_proof', $data);
		if ($this->db->affected_rows() > 0) {
			$data = array(
				"processed" => 1,
				"processed_at" => gmdate("Y-m-d H:i:s")
			);

			$this->db->where(array('shipper_id' => $user_id, 'id' => $id));
			$this->db->update('escrow_shipping_info', $data);
			if ($this->db->affected_rows() > 0) {
				return true;
			}
		}

		return false;
	}

	public function updateReceived($id, $user_id, $received) {
		$data = array(
			"received" => $received
		);

		if ($received) {
			$data["received_at"] = gmdate("Y-m-d H:i:s");
		}
		$this->db->where(array('shippee_id' => $user_id, 'id' => $id));
		$this->db->update('escrow_shipping_info', $data);

		if ($this->db->affected_rows() > 0) {
			return true;
		}

		return false;
	}

	public function get_total_raised_by_fundraise_type($user_id=NULL, $filter="overall") {
		$union = "";
		$select = "";
		$where = [];
		$groupBy = "";
		
		if (isset($user_id) && isset($filter)) {
			if ($filter == "overall") {
				$union = "'Fundraise_Type', '#' from dual UNION (";
				$select = "select fundraise_type";
				$where = ['charity.user_id = ' . $user_id . ' or ugc.user_id= ' . $user_id];
				$groupBy = 'fundraise_type';
			} else if ($filter == "fundraiser") {
				$union = "'Charity', '#' from dual UNION (";
				$select = "select charity.name as fundraiser";
				$where = ['charity.user_id'=>$user_id];
				$groupBy = "charity.name";
			} else if ($filter == "game") {
				$union = "'Game', '#' from dual UNION (";
				$select = "select game.name as game";
				$where = ['ugc.user_id'=>$user_id];
				$groupBy = "game.name";
			}
		}

		$this->db->select( $union . $select . ', coalesce(sum(payments.Credits),0) as count');
		$this->db->from('charity');
		$this->db->join('user_game_charity as ugc', 'ugc.charity_id=charity.id', 'left');
		$this->db->join('game', 'ugc.game_id=game.id');
		$this->db->join('payments', 'payments.game_id=ugc.game_id', 'left');
		
		$this->db->where($where);
		$this->db->group_by($groupBy);

		$query = $this->db->get_compiled_select() . ")";

		return $this->db->query($query)->result_array();
	}

	public function getUserActivity($user_id, $limit=0) {
		$this->db->select("notification.*, game.user_id as gameUser, tbl_users.username, game.name as g_name, game.slug as gameSlug, gametype.name as gameType, charity.user_id as charityUser, charity.name as c_name, charity.slug as charitySlug, fundraise_type");
		$this->db->from("notification");

		$this->db->group_start();
		$this->db->where(["notification.for_user" => $user_id, "notification.action_user =" => $user_id, "game.user_id" => $user_id ]);
			$this->db->group_start();
			$this->db->where("notification.action = 'create' or notification.action = 'publish' or notification.action = 'end' or notification.action = 'win' or notification.action = 'confirm'");
			$this->db->group_end();
		$this->db->group_end();
		$this->db->or_group_start();
		$this->db->where(["notification.for_user" => $user_id, "notification.action_user =" => $user_id, "game.user_id !=" => $user_id]);
			$this->db->group_start();
			$this->db->where("notification.action = 'win' or notification.action = 'play' or notification.action = 'confirm'");
			$this->db->group_end();
		$this->db->group_end();

		$this->db->join("game", "notification.game_id=game.id", "left");
		$this->db->join("charity", "notification.charity_id=charity.id", "left");
		$this->db->join("gametype", "game.Type=gametype.id");
		$this->db->join("tbl_users", "notification.action_user=tbl_users.user_id", "left");
		$this->db->order_by("Date", 'desc');

		if ($limit == 0) {
			$this->db->limit(7, 0);
		} else {
			$this->db->limit(7, $limit);
		}

		$query = $this->db->get()->result_array();
		return $query;
	}

	public function getSupporterActivity($user_id, $limit=0) {
		$this->db->select("notification.*, game.user_id as gameUser, tbl_users.username, game.name as g_name, game.slug as gameSlug, gametype.name as gameType, charity.user_id as charityUser, charity.name as c_name, charity.slug as charitySlug, fundraise_type");
		$this->db->from("notification");

		$this->db->group_start();
		$this->db->where(["notification.for_user =" => $user_id, "notification.action_user !=" => $user_id, "game.user_id !=" => $user_id ]);
			$this->db->group_start();
			$this->db->where("notification.action = 'create' or notification.action = 'end' or notification.action = 'win' or notification.action = 'claimed'");
			$this->db->group_end();
		$this->db->group_end();
		$this->db->or_group_start();
		$this->db->where(["notification.for_user =" => $user_id, "notification.action_user !=" => $user_id, "game.user_id" => $user_id]);
			$this->db->group_start();
			$this->db->where("notification.action = 'win' or notification.action = 'play' or notification.action = 'claimed'");
			$this->db->group_end();
		$this->db->group_end();

		$this->db->join("game", "notification.game_id=game.id", "left");
		$this->db->join("charity", "notification.charity_id=charity.id", "left");
		$this->db->join("gametype", "game.Type=gametype.id");
		$this->db->join("tbl_users", "notification.action_user=tbl_users.user_id", "left");
		$this->db->order_by("Date", 'desc');

		if ($limit == 0) {
			$this->db->limit(7, 0);
		} else {
			$this->db->limit(7, $limit);
		}

		$query = $this->db->get()->result_array();

		return $query;
	}
}

