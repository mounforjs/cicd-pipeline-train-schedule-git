<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Game_play_model extends CI_Model {

	public function deductCreditsAndInitAttempt($user_id, $game_session_id, $game_row, $custom_amount, $resume) {
		if ($resume) {
			return array("status" => true, "game_session_id" => $game_session_id);
		}

		$userCurrentBalance = getBalanceAsFloat();
		if ($custom_amount > 0 && $custom_amount > $game_row->credit_cost) { //verify custom amount is greater than default credit cost
			$game_row->credit_cost = $custom_amount;
		}
		
		$totalcredits = $userCurrentBalance - $game_row->credit_cost;
		$date = date('Y-m-d H:i:s');

		$game_id = $game_row->id;
		$selected_beneficiary = null;
		$payment_id = null;

		if ($game_row->credit_cost == 0 ) {
			// game is free
			$selected_beneficiary = $payment_id = 0;
		} else {
			// game is not free - check if user is eligible to play and deduct if eligibile
			if ($userCurrentBalance >= $game_row->credit_cost) {
				// user has enough credits to play

				$selected_beneficiary = $game_row->selected_fundraiser->id;

				$ref_no = $this->db->select('ref_num')->order_by('payment_id','desc')->get('payments')->row();
				if (isset($ref_no->ref_num) and $ref_no->ref_num > 0) {
					$ref_no = $ref_no->ref_num + 1;
				} else {
					$ref_no = 10001;
				}


	
				$data = array(
					'Credits' => $game_row->credit_cost,
					'User_ID' => $user_id,
					'user_type' => 7,
					'Date' => $date, 
					'Notes' => '$ '.round_to_2dc($game_row->credit_cost)." deducted for game ".$game_row->name,
					'Status' => '2',
					'game_id' => $game_id,
					'item_number' => '',
					'txn_id' => '',
					'payment_gross' => '',
					'currency_code' => '0',
					'payment_status' => '',
					'total_credits' =>$totalcredits,
					'payment_mode' => 4,
					'ref_num'	=> $ref_no,
					'payment_status' => 'Completed'
				);
	
				if (!$this->db->insert('payments',$data)) {
					return array("status" => false, "error" => "backend");
				} else {
					$insert_id = $this->db->insert_id();
					$payment_id = $insert_id;
	
					$this->db->set('txn_id', $insert_id.uniqid());
					$this->db->where('payment_id', $insert_id);
					if ($this->db->update('payments')) {
						$payment_id = $insert_id;
					} else {
						return array("status" => false, "error" => "backend");
					}
				}
			} else {
				return array("status" => false, "error" => "insufficient");
			}
		}	

		if (isset($payment_id)) {
			//start user's attempt if payment processed

			$data = array(
				'user_id' => $user_id,
				'game_id' => $game_id,
				'selected_beneficiary' => $selected_beneficiary,
				'payment_id' => $payment_id,
				'game_session_id' => $game_session_id,
				'created_at' => $date,					
			);

			$insert_user_game = $this->db->insert('user_game_attempts', $data);
			if ($insert_user_game) {
				return array("status" => true, "game_session_id" => $game_session_id);
			} else {
				return array("status" => false, "error" => "backend");
			}
		}
	}

	public function createGameSessionId($user_id, $game_id) {
		return md5(uniqid($user_id . $game_id . date("now"), true));
	}

	public function getGameHistoryForUserByGameId($user_id, $game_id, $game_session_id) {
		$this->db->select('uga.selected_beneficiary, p.Credits as custom_amount, gh.*');
		$this->db->from('user_game_attempts as uga');
		$this->db->join('payments as p', "uga.payment_id=p.payment_id", "left");
		$this->db->join('game_history as gh', "uga.game_session_id=gh.game_session_id");
		$this->db->where(array('uga.user_id' => $user_id, 'uga.game_id' => $game_id, "uga.game_session_id" => $game_session_id, 'end_time is not NULL' => null));
		$this->db->order_by('gh.id', 'DESC');
		return $this->db->get()->row();
	}

	public function setScoreSeen($game_session_id) {
		$this->db->where('game_session_id', $game_session_id)->update('game_history', array( 'score_screen_seen' => 1));
	}

	public function getLastGameSession($user_id, $game_id) {
		//returns latest user_game_attempt row with corresponding game_history row (null if not created)
		$this->db->select("uga.user_id, uga.game_id, uga.game_session_id, gh.created_at, gh.start_time, gh.end_time");
		$this->db->from("user_game_attempts as uga");
		$this->db->join("game_history as gh", "uga.game_session_id = gh.game_session_id", "left");
		$this->db->where(array("uga.user_id" => $user_id, "uga.game_id" => $game_id));
		$this->db->order_by("uga.id", "DESC");
		$this->db->limit(1);

		return $this->db->get()->row();
	}

	public function startGameSession($game_session_id, $time) {
		$attempt_data = array(
			'game_session_id' => $game_session_id,
			'start_time' => date("Y-m-d H:i:s", $time)
		);

		return $this->db->insert('game_history', $attempt_data);
	}

	public function get_attempt_game_detail($game_session_id) {
		$this->db->select('gt.id, (case when gt.parent != 0 then gt2.name else gt.name end) as name');
		$this->db->from('gametype as gt');
		$this->db->join('gametype as gt2', 'gt.parent = gt2.id', 'left');
		$subquery = $this->db->get_compiled_select();

        $this->db->select('game.*, uga.user_id as player_id, gt.name as gameType');
        $this->db->from('user_game_attempts as uga');
        $this->db->join('game_history as gh', "uga.game_session_id=gh.game_session_id");
        $this->db->join('game', "game.id=uga.game_id");
        $this->db->join("({$subquery}) as gt", "game.Type=gt.id");
		$this->db->where('uga.game_session_id', $game_session_id);

        return $this->db->get()->row();
	}

	public function submit_game_data($game_session_id, $data) {
		$this->db->where("game_session_id", $game_session_id);
		return $this->db->update("game_history", $data);
	}

	public function getUserRating($game_id, $user_id) {
		$this->db->from('user_game_rate');
		$this->db->where(array('game_id =' => $game_id, "user_id" => $user_id));
 		$rating = $this->db->get()->row();

  		return ($rating) ? $rating->rating : 5;
	}

	public function addRating($slug, $rating) {
		$user_id = $this->session->userdata('user_id');

		$this->db->from("game");
		$this->db->where("slug", $slug);

		//if game exists
		$game_id = $this->db->get()->row()->id;
		if (!isset($game_id)) {
			return "game doesn't exist";
		}

		//if rating from player already exists
		$this->db->from("user_game_rate");
		$this->db->where(array("user_id" => $user_id, "game_id" => $game_id));
		if (count($this->db->get()->row()) > 0) {
			$this->db->set('rating', $rating);
			$this->db->where(array('user_id' =>$user_id ,'game_id' => $game_id));
			$update = $this->db->update('user_game_rate');

			return "updated";
		}

		$data = array('user_id' =>$user_id ,'game_id' => $game_id,'rating' => $rating);
		$insert = $this->db->insert('user_game_rate',$data);

		if ($this->db->affected_rows() > 0) {
			return 'success';
		}
	}

    public function updateGamePublishOrLiveDate($slug, $date, $publish) {
    	$game = $this->db->where('slug', $slug)->get('game')->row();
    	if (isset($game)) {
			$ugc = $this->db->get_where('user_game_charity', ['game_id' => $game->id])->row();
			$fundraiser = $this->db->get_where('charity', ['id' => $ugc->charity_id])->row();

			if ($fundraiser->approved === "Yes") {
				$this->db->where('id', $game->id);
				$this->db->update('game', array('Publish_Date' => $date, 'Publish' =>$publish));
				return array("status" => "success", "msg" => ($publish == "Live") ? "Game has been made live!" : "Game scheduled to go live on, $date.");
			} else {
				return array("status" => "failed", "msg" => "Beneficiary is not approved.");
			}
    	} else {
    		return array("status" => "failed", "msg" => "Game does not exist.");
    	}
    }  

	public function get_types($dev=0) {
		//get types no marked for development
		$this->db->where(array("parent" => 0, "dev" => $dev));
		$root_types = $this->db->get('gametype')->result();

		foreach($root_types as $key => $root_type) {
			$this->db->where(array("parent" => $root_type->id, "dev" => $dev));
			$root_type->child_types = $this->db->get('gametype')->result();
		}

		return $root_types;
	}

	public function get_charity() {
		$data =array(
			'game.Publish' => 'Yes',
			'user_charity.user_id !=' => $this->session->userdata('user_id'),
			'game.Status' => 'Running',
		);	

		$this->db->select('user_charity.*')->from('user_charity')->join('game', 'user_charity.game_id = game.id');
		$this->db->where($data);
		$this->db->where_not_in('game.id','( select game_id from user_game_attempts where user_id='.$this->session->userdata('user_id').')');
		$this->db->where($data);
		$this->db->group_by('user_charity.name');

		return $this->db->get()->result();
	}

	public function deleteGame($gid) {
		$this->db->where('slug', $gid);
		$this->db->where('user_id', $this->session->userdata('user_id'));
		$this->db->delete('game'); 

		if ($this->db->affected_rows() > 0)
			return true;
		else
			return false;
	}
	
	public function isRight($Answer, $Question) {
        $Answer = strtolower($Answer);
		$right_sql =  $this->db->query("SELECT * FROM answers WHERE ( Answer_id = '".$Answer."' AND Answer_Question_id = '".$Question."' ) OR (find_in_set('".$Answer."', Answer_text ) AND Answer_Question_id = '".$Question."')");
        $isright = $right_sql->result_array();

        $right = $isright[0]["Answer_isRight"];
        $correct = $isright[0]["Answer_text"];
        if ($right == 1 || $Answer == $correct) {
            return true;
        } else {
            return false;
        }
    }

	public function insert_visit($game_id) {
		$user_id = '0';
		if ($this->session->userdata('user_id')) {
			$user_id = $this->session->userdata('user_id');
		}
		
		$data=array(
			'user_id' => $user_id,
			'game_id' =>  $game_id,
		);

		$this->db->insert('game_visits',$data);
	}

	public function isThirdParty($gametype) {
		$this->db->select("parent, third_party, name as gametype, goal");
		$this->db->from("gametype");
		$this->db->where("name", $gametype);
		$parent = $this->db->get()->result()[0];

		if ($parent->parent == 0) {
			return $parent;
		} else {
			$this->db->select("gt.third_party, gt.name as gametype, gt.goal");
			$this->db->from("gametype");
			$this->db->join("gametype as gt", "gametype.parent = gt.id");
			$this->db->where("gametype.name = '" . $gametype . "'");
		}
		
		return $this->db->get()->row();
	}

	public function requireAccountLink($gametype) {
		$this->db->select("parent, account_link, name as gametype");
		$this->db->from("gametype");
		$this->db->where("name", $gametype);
		$parent = $this->db->get()->result()[0];

		if ($parent->parent == 0) {
			return $parent;
		} else {
			$this->db->select("gt.account_link, gt.name as gametype");
			$this->db->from("gametype");
			$this->db->join("gametype as gt", "gametype.parent = gt.id");
			$this->db->where("gametype.name = '" . $gametype . "'");
		}
		
		return $this->db->get()->result()[0];
	}
	
	public function isAccountLink($user_id, $gametype) {
		$this->db->from("linked_accounts as la");
		$this->db->where(array("la.user_id" => $user_id, "la.type" => $gametype));
		
		return $this->db->get()->num_rows();
	}
	
	public function awardMinecraftTokens($user_id, $game_details) {
		$this->db->where(array("user_id" => $user_id, "game_id" => $game_details->id));
		$existing = $this->db->get("minecraft_data")->result()[0];

		if ($existing->credits > 0) {
			$data = array("credits" => $existing->credits + $game_details->attempt_count);
			$this->db->where(array("user_id" => $user_id, "game_id" => $game_details->id));
			return $this->db->update("minecraft_data", $data);
		} else {
			$data = array(
				'user_id' => $user_id,
				'game_id' => $game_details->id,
				'credits' => $game_details->attempt_count
			);

			return $this->db->insert("minecraft_data", $data);
		}
	}

	public function get_game_value($value='min') {
		$this->db->select("$value(value_of_the_game) as value");
		$res = $this->db->get('game')->row();
		return $res->value;
	}

	public function get_game_credit_cost($value='min') {
		$this->db->select("$value(credit_cost) as value");
		$res = $this->db->get('game')->row();
		return $res->value;
	}

	public function game_visits_count($game_id) {
		$this->db->from('game_visits');
		$this->db->where('game_visits.game_id ='.$game_id);
 		$count = $this->db->count_all_results();
  		return $count;
	}

	public function game_playes_count($game_id) {
		$this->db->from('user_game_attempts');
		$this->db->where('user_game_attempts.game_id ='.$game_id);
		$this->db->group_by('user_game_attempts.user_id');
 		$count = $this->db->count_all_results();
  		return $count;
	}

	public function charity_game_count($charity_id) {
		$this->db->join('game','game.id = user_game_charity.game_id');
		$this->db->from('user_game_charity');
		$this->db->where('user_game_charity.charity_id ='.$charity_id);
		$this->db->where("game.Publish = 'Yes'");

 		$count = $this->db->count_all_results();
  		return $count;
	}

	public function get_game_rating($game_id) {
		$this->db->select_avg('user_game_rate.rating');
		$this->db->from('user_game_rate');
		$this->db->where('user_game_rate.game_id ='.$game_id);
 		$query = $this->db->get();

 		$game_rating = $query->row()->rating;
		if (!$game_rating) {
			$game_rating = 5;
		}
		
  		return $game_rating;
	}
}

