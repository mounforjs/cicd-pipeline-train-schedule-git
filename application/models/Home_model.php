<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home_model extends CI_Model {

	function getTagName($id) {
		return $this->db->get_where('gametags', ['id'=>$id])->row();
	}

	public function login($email, $password) {
		$data = array();

		$this->db->where(['email' => $email]);
		$result1 = $this->db->get('tbl_users');
		
		if ($result1->num_rows() != 0) {
			if ($this->session->tempdata('login_penalty')) {
				$data = array ('errors' => 'Too many login attempts; please try again later.');
			} else {
				$db_password = $result1->row()->password;

				if (password_verify($password, $db_password)) {
					if ($result1->row(0)->user_status == 'No') {
						$data = array ('errors' => 'Account suspended; contact support.');
					} else {
						$data['id'] = $result1->row(0)->user_id;	
					}
				} else {
					$attempt = $this->session->userdata("login_attempt");
					$attempt++;
					$this->session->set_userdata("login_attempt", $attempt);
	
					if ($attempt == 5) {
						$this->session->set_tempdata('login_penalty', true, 600); //10 min login penalty
	
						$data = array ('errors' => 'Too many login attempts; please try again later.');
					} else { //wrong password
						$data = array ('errors' => 'Login failed; invalid email or password.');
					}
				}
			}
		} else { //email does not exist
			$data = array ('errors' => 'Login failed; invalid email or password.');
		}

		return $data;
	}
	

	public function track($id) {
		$geo = unserialize(file_get_contents("http://www.geoplugin.net/php.gp?ip=50.134.219.49"));
		$country = $geo["geoplugin_countryName"];
		$city = $geo["geoplugin_city"];

		$data = array(
			'user_id' => $id,
			'ip' => $this->get_client_ip()  ,
			'location' =>  $country ,
			'timezone' =>  date('P'), 
		);

		$this->db->insert('tracking_user',$data);
	}

	private function get_client_ip() {
		$ipaddress = '';
		if (isset($_SERVER['HTTP_CLIENT_IP']))
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else if (isset($_SERVER['HTTP_X_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		else if (isset($_SERVER['HTTP_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		else if (isset($_SERVER['REMOTE_ADDR']))
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		else
			$ipaddress = 'UNKNOWN';
		return $ipaddress;
	}

	public function get_user_session($user_id) {
		$this->db->select("us.*, (CURRENT_TIMESTAMP() >= expires_at) as expired");
		$this->db->from("tbl_users as tu");
		$this->db->join("user_sessions as us", "us.user_id=tu.user_id");
		$this->db->where("tu.user_id", $user_id);

		return $this->db->get()->row();
	}

	public function set_user_session($user_id) {
		$session_id = md5(uniqid("session-" . $user_id, true));
		$expiration = $this->config->item("sess_expiration");

		$last_acccess = date("Y-m-d H:i:s", strtotime("now"));
		$expires_at = date("Y-m-d H:i:s", strtotime("+ {$expiration} seconds"));

		$data = array(
			"user_id" => $user_id, 
			"session_id" => $session_id,
			"last_access" => $last_acccess,
			"expires_at" => $expires_at
		);

		$this->db->delete("user_sessions", array("user_id" => $user_id));

		$this->db->where("user_id", $user_id);
		$insert = $this->db->insert("user_sessions", $data);
		if ($insert) {
			return array("status" => "success", "session_id" => $session_id);
		} else {
			return array("status" => "failed");
		}
	}

	public function update_user_session($user_id) {
		$expiration = $this->config->item("sess_expiration");

		$last_acccess = date("Y-m-d H:i:s", strtotime("now"));
		$expires_at = date("Y-m-d H:i:s", strtotime("+ {$expiration} seconds"));

		$data = array(
			"last_access" => $last_acccess,
			"expires_at" => $expires_at
		);

		$this->db->where("user_id", $user_id);
		return $this->db->update("user_sessions", $data);
	}

	public function remove_user_session($user_id) {
		$this->db->where("user_id", $user_id);
		return $this->db->delete("user_sessions");
	}
	
	public function getTotalGame($user_id, $filter) {
		if ($filter != 'played') {
			$this->db->select("count(id) as total");
			$this->db->from("game");
		} else {
			$this->db->select("count(gh.id) as total");
			$this->db->from('user_game_attempts as uga');
			$this->db->join('game_history as gh', "uga.game_session_id=gh.game_session_id");
		}

		switch ($filter) {
			case 'drafted':
				$this->db->where(['user_id'=> $user_id, 'Publish' => 'No', 'Publish_Date is NULL' => NULL]);
				break;
			case 'published':
				$this->db->where(['user_id'=> $user_id, 'Publish' => 'Yes', 'STR_TO_DATE(Publish_Date, "%Y-%m-%d %H:%i:%s") IS NOT NULL' => NULL]);
				break;
			case 'live':
				$this->db->where(['user_id'=> $user_id, 'Publish' => 'Live', 'STR_TO_DATE(Publish_Date, "%Y-%m-%d %H:%i:%s") IS NOT NULL' => NULL]);
				break;
			case 'completed':
				$this->db->where(['user_id'=> $user_id, 'Status =' => 'Completed']);
				break;
			case 'played':
				$this->db->join('game', "game.id=uga.game_id");
				$this->db->where('uga.user_id', $user_id);
				$this->db->group_by("uga.game_id");
				break;
		}

		$query = $this->db->get()->result_array();
		
		return $query;
	}
	
	public function fetch_games(&$filters=array(), $show, $offset=0, $limit=6) {
		return $this->search('all', $filters, $show, $offset, $limit);
	}
	
	public function search($result_type='count', &$filters, $show, $offset, $limit) {
		$user_id = $this->session->userdata('user_id') ? $this->session->userdata('user_id') : 0; #logged in user

		# game tag subquery - query builder does not play nice with subqueries
		$game_tags = $this->get_game_tags();

		$this->db->from('game');

		$this->db->join("({$game_tags}) as gts", 'game.id = gts.id', 'left');

		# filter by game state
		if ($show == 'drafted') {
			$this->db->where(['game.user_id'=> $user_id, 'game.Publish' => 'No', 'game.Publish_Date is NULL' => NULL]);
		} elseif ($show == 'published') {
			$this->db->where(['game.user_id'=> $user_id, 'game.Publish' => 'Yes', 'STR_TO_DATE(Publish_Date, "%Y-%m-%d %H:%i:%s") IS NOT NULL' => NULL]);
		} elseif ($show == 'live') {
			$this->db->where(['game.user_id'=> $user_id, 'game.Publish' => 'Live', 'STR_TO_DATE(Publish_Date, "%Y-%m-%d %H:%i:%s") IS NOT NULL' => NULL]);
		} elseif ($show == 'wishlist') {
			$this->db->join('user_wishlist', 'game.id = user_wishlist.game_id');		
			$this->db->where(['user_wishlist.user_id' => $user_id , 'game.Publish' => 'Live']);
		} elseif ($show == 'played') {
        	$this->db->join('user_game_attempts as uga', "uga.game_id=game.id");
        	$this->db->join('game_history as gh', "uga.game_session_id=gh.game_session_id");	
			$this->db->where('uga.user_id', $user_id);
		} elseif ($show == 'completed') {	
			$this->db->where(['game.user_id'=> $user_id, 'game.Status =' => 'Completed']);
		} else {
			$this->db->where(' game.id NOT IN (SELECT game_id FROM tbl_flag WHERE user_id = '.$user_id.')', NULL, FALSE);

			$g_status = array('Live','Yes');	
			$this->db->where_in('game.Publish', $g_status);
			$this->db->where(['game.active' => 'Yes']);	
		}

		# exclude games - i.e. showing games on the game detail page created by x user, excluding the current
		if (isset($filters["exclude_games"]) && !empty($filters["exclude_games"])) {
			$this->db->where_not_in('game.id', $filters["exclude_games"]);
		}
		
		# game value range - creates range based on input i.e. 1-5, 5-10 => 1-10
		if (isset($filters["game_value"]) && !empty($filters["game_value"])) {
			list($min, $max) = explode("-", $filters["game_value"]);

			$this->db->group_start();

			if ((isset($min) && $min != "") && (isset($max) && $max != "")) {
				if ($min == $max) {
					$this->db->where("game.value_of_the_game", $max);
				} else {
					$this->db->where("game.value_of_the_game >=", $min);
					$this->db->where("game.value_of_the_game <=", $max);
				}
				
			} else {
				if (isset($min) && $min != "") {
					$this->db->where("game.value_of_the_game >=", $min);
				} else {
					$this->db->where("game.value_of_the_game <=", $max);
				}
			}

			$this->db->group_end();
		}

		# game cost range - creates range based on input i.e. 1-5, 5-10 => 1-10
		if (isset($filters["game_cost"]) && !empty($filters["game_cost"])) {
			list($min, $max) = explode("-", $filters["game_cost"]);

			$this->db->group_start();

			if ((isset($min) && $min != "") && (isset($max) && $max != "")) {
				if ($min == $max) {
					$this->db->where("game.credit_cost", $max);
				} else {
					$this->db->where("game.credit_cost >=", $min);
					$this->db->where("game.credit_cost <=", $max);
				}
			} else {
				if (isset($min) && $min != "") {
					$this->db->where("game.credit_cost >=", $min);
				} else {
					$this->db->where("game.credit_cost <=", $max);
				}
			}

			$this->db->group_end();
		}
		
		# gametype filter - filter by type (and subtype if possible) - i.e. 'skeleton shootout' has a parent 'minecraft'
		if (isset($filters["game_type"]) && !empty($filters["game_type"])) {
			$game_types = explode(",", $filters["game_type"]);
			if (in_array("all", $game_types)) {
				;
			} else {
				$this->db->group_start();

				$this->db->join('gametype', 'game.Type = gametype.id');
				$this->db->join('gametype as gt2', 'gametype.parent = gt2.id', 'left');
				$this->db->where_in('gametype.name', $game_types);
				$this->db->or_where_in('gt2.name', $game_types);

				$this->db->group_end();
			}
		}
		
		# game reward type 
		if (isset($filters["credit_type"]) && !empty($filters["credit_type"])) {
			$credit_types = explode(",", $filters["credit_type"]);
			if (isset($game_type_filter) && in_array("all", $credit_types)) {
				;
			} else {
				$this->db->where_in('game.credit_type', $credit_types);
			}
		}


		if ((isset($filters["fundraise_type"]) && !empty($filters["fundraise_type"])) || (isset($filters["beneficiary"]) && !empty($filters["beneficiary"]))) {
			if (empty($keyword)) {
				$this->db->join('user_game_charity', 'game.id = user_game_charity.game_id', 'left');
				$this->db->join('charity', 'charity.id = user_game_charity.charity_id', 'left');
			}
		
			if (!empty($filters["beneficiary"])) {
				$this->db->where('charity.slug', $filters["beneficiary"]["slug"]);
			}
		}

		# search bar filtering - supports tagging/keywords - i.e. "an entire phrase", tag:xbox title:test, title:"test test", etc.
		if (isset($filters["search"]) && !empty($filters["search"])) {
			$acceptable_tags = ["title", "descr", "user", "tag"];

			# split tags by type-value
			$tags = array_map(function($val) {
				return explode(":", strtolower($val));
			}, $filters["search"]["tags"]);
			
			# remove tag if it is not acceptable
			$tags = array_filter($tags, function($tag) use($acceptable_tags) {
				return in_array($tag[0], $acceptable_tags);
			});

			# create map/dict/assoc array of tags - array[type] = value
			$tag_map = array_reduce($tags, function($carry, $item) {
				if (isset($carry[$item[0]])) {
					# create array if key already exists
					array_push($carry[$item[0]], $item[1]);
				} else {
					$carry[$item[0]] = [$item[1]]; 
				}
				
				return $carry;
			});

			# create filtering for keywords - i.e. "a search phrase", a search phrase, etc.
			if (!empty($filters["search"]["keywords"])) {
				// Escape special regex characters in the keywords
				$escaped_keywords = array_map(function($keyword) {
					return preg_quote($keyword, '/'); // Escape special characters for regex
				}, $filters["search"]["keywords"]);
			
				// Create a regex pattern from the escaped keywords
				// Match either individual keywords or entire quoted phrases
				$pattern = implode("|", $escaped_keywords);
			
				// Build regex pattern to match whole phrases
				$where = "({$pattern})";
			
				// Apply the regex pattern to the query
				$this->db->where("LOWER(game.name) REGEXP '{$where}'", NULL, false);
			}			

			# create filtering for tags - i.e. tag:test, title:"test", etc.
			if (isset($tag_map) && !empty($tag_map)) {
				$this->db->group_start();

				$i = 0;
				foreach ($tag_map as $type => $kwrd) {
					$join = null;

					switch ($type) {
						case "title":
							$column_name = "game.name";
							break;
						case "descr":
							$column_name = "game.game_desc";
							break;
						case "user":
							$column_name = "tbl_users.username";
							$this->db->select("gts.game_tags");
							$this->db->join('tbl_users', 'game.user_id = tbl_users.user_id', 'left');
							break;
						case "tag":
							$column_name = "game_tags";
							break;
						default:
							continue 2;
							break;
					}

					if (is_array($kwrd)) {
						// Join keywords using regex - AND for comma separated, OR for each item in array
						foreach ($kwrd as $k => $v) {
							$this->db->or_group_start();
					
							// Split each keyword set by comma
							$and = explode(",", $v);
					
							foreach ($and as $k1 => $v1) {
								// Remove quotes from around the keyword
								$_v1 = trim($v1, "\"'");
					
								// Escape special characters for regex
								$_v1 = preg_quote($_v1, '/');
					
								// Construct regex pattern to match the keyword
								$where = "\\b{$_v1}\\b";
					
								// Add where clause to the query
								$this->db->where("LOWER({$column_name}) REGEXP '{$where}'", NULL, false);
							}
					
							$this->db->group_end();
						}
					} else {
						// Check if the string contains comma-separated values
						if (strpos($kwrd, ',') !== false) {
							// Keywords matching *type*:*value*,*value*,etc. operate as AND in regex
							$this->db->group_start();
					
							// Split the keywords by comma
							$and = explode(",", $kwrd);
					
							foreach ($and as $k => $v) {
								// Escape special characters for regex
								$v = trim($v);
								$v = preg_quote($v, '/');
					
								// Construct regex pattern to match the keyword
								$where = "\\b{$v}\\b";
					
								// Add where clause to the query
								$this->db->where("LOWER({$column_name}) REGEXP '{$where}'", NULL, false);
							}
					
							$this->db->group_end();
						} else {
							// If not comma-separated, perform a LIKE search
							$this->db->like("LOWER({$column_name})", strtolower($kwrd));
						}
					}

					$i++;
				}

				$this->db->group_end(); 
			}

			# exclusion filter of games by user *unless specifically searching for a username
			if ($user_id > 0 && !isset($tag_map["user"])) {
				$this->db->where('game.user_id !=', $user_id);
			}
		}

		# sort order filter
		if (isset($filters["sort_list"]) && !empty($filters["sort_list"])) {
			switch ($filters["sort_list"]) {
				case 'price_high':
					$this->db->order_by('game.value_of_the_game','desc');
					break;
				case 'price_low':
					$this->db->order_by('game.value_of_the_game','asc');
					break;
				case 'newest':
					$this->db->order_by('game.id','desc');
					break;
				case 'oldest':
					$this->db->order_by('game.id','asc');
					break;
				case 'fund_high':
					$this->db->join('game_credit', 'game.id = game_credit.game_id', 'left');
					$this->db->order_by('game_credit.fundraise_value','desc');
					break;
				case 'soon':
					$this->db->select('DATE_add( DATE_Add( DATE_ADD( Publish_Date , INTERVAL End_Day DAY), INTERVAL End_Hour HOUR), INTERVAL End_Minute MINUTE) as end_date');
					$this->db->having('end_date >= DATE_ADD(NOW(), INTERVAL 3 MINUTE)');
					$this->db->order_by('end_date','asc');
					break;
				case 'rate_high_low':
					$this->db->order_by('rating','desc');
					$this->db->order_by('rating_count','desc');
					break;
				case 'cost_low':
					$this->db->order_by('game.credit_cost','asc');
					break;
				case 'cost_high':
					$this->db->order_by('game.credit_cost','desc');
					break;
				default:
					break;
			}
		} else {
			$this->db->order_by('game.value_of_the_game','desc');
		}

		$this->db->join('user_game_rate', 'game.id = user_game_rate.game_id', 'left');
		$this->db->group_by('game.id');

		$this->db->limit($limit, $offset);
	
		$this->db->select('game.id, game.name, game.slug, gts.game_tags, game.credit_cost, game.value_of_the_game, game.game_desc, game.user_id, game.Type, game.Game_Image, game.Publish, game.Publish_Date, game.credit_type, COALESCE(AVG(user_game_rate.rating), 5) as rating, COUNT(user_game_rate.id) as rating_count');

		$query = $this->db->get();
		$result = $query->result();
		
		if($result_type == 'count'){
			return $query->num_rows();
		}

		return $result;
	}

	private function get_game_tags($slug=null) {
		$this->db->select("game.id, group_concat(gt1.tag_name) as game_tags");
		$this->db->from("game");
		$this->db->join("game_tags as gt", "game.id = gt.game_id", "left");
		$this->db->join("gametags as gt1", "gt.tag_id = gt1.id");
		$this->db->group_by("game.id");

		if (isset($slug)) {
			$this->db->where("game.slug", $slug);
		}

		return $this->db->get_compiled_select();
	}

	public function fetch_game_detail($slug) {
		$game_tags = $this->get_game_tags($slug);

		$this->db->select('game.*' );
		$this->db->select('game_credit.fundraise_value, game_credit.beneficiary_percentage, game_credit.wwl_percentage, game_credit.creator_percentage, game_credit.winner_percentage' );
		$this->db->select('tbl_users.username, tbl_users.profile_img_path, tbl_users.user_description, gametype.name as gameType, gametype.parent as gameTypeParent, gametype.img as gameTypeImg, gametype.descr as gameTypeDesc, gametype.instructions as gameTypeInstructions, gametype.instructionImage as gameTypeInstrImage, gametype.goal gameTypeGoal' );
		$this->db->select('gts.game_tags' );
		$this->db->select('gametype.name as gameType, gametype.parent as gameTypeParent, gametype.img as gameTypeImg, gametype.descr as gameTypeDesc, gametype.instructions as gameTypeInstructions, gametype.instructionImage as gameTypeInstrImage, gametype.goal gameTypeGoal' );
		
		$this->db->where('game.slug',$slug);

		$this->db->from('game');

		$this->db->join('game_credit', 'game.id = game_credit.game_id', 'left');
		$this->db->join('tbl_users', 'tbl_users.user_id  = game.user_id','left');
		$this->db->join('gametype', 'gametype.id  = game.Type','left');

		$this->db->join("({$game_tags}) as gts", 'game.id = gts.id', 'left');

		$result = $this->db->get()->row();
		return $result;
	}

	public function fetch_credit_detail($id =0 ) {
		$this->db->where('game_credit.id',$id);
		return $this->db->get('game_credit')->row();
	}

	public function getAccessPrivilege($game_id, $user_id) {
		$this->db->from('tbl_credits_distribution');
		$this->db->where("game_id", $game_id);
		$this->db->where("creator_id", $user_id);
		$this->db->or_where("winner_id", $user_id);

		$result = $this->db->get()->num_rows();
		return ($result > 0) ? true : false;
	}

	public function getConfirmingStatus($game_id, $user_id) {
		$this->db->from('tbl_credits_distribution');
		$this->db->where(array("game_id" => $game_id, "winner_id" => $user_id));

		return $this->db->get()->row();
	}

	public function get_game_type($type) {
		$this->db->select('name');
        $this->db->where('id', $type);
        $this->db->from('gametype');
        $query = $this->db->get();
        return $query->result();
	}

	public function get_gametypes($dev = false, $third_party = false, $account_link = false) {
		$this->db->select('gt.id, gt.name, gt2.name as parent, gt.goal');
		$this->db->from('gametype as gt');
		$this->db->join('gametype as gt2', 'gt.parent = gt2.id', 'left');

		if ($dev) {
			$this->db->where(array('gt.dev' => 1, 'gt2.parent is null' => null));
			$this->db->or_where(array('gt2.dev' => 1));
		}

		if ($third_party) {
			$this->db->where(array('gt.third_party' => 1, 'gt2.parent is null' => null));
			$this->db->or_where(array('gt2.third_party' => 1));
		}
		
		if ($account_link) {
			$this->db->where(array('gt.account_link' => 1, 'gt2.parent is null' => null));
			$this->db->or_where(array('gt2.account_link' => 1));
		}
		

        $result = $this->db->get()->result_array();
		
		$assoc_result = array();
		foreach ($result as $key => $val) {
            $assoc_result[$val["name"]] = $val;
        }

		return $assoc_result;
	}

	public function get_supported_fundraise($id) {
		$this->db->select('charity.name, charity.Approved,charity.fundraise_type, charity.Image, charity.Description' )->from('user_game_charity') ->join('charity', 'user_game_charity.charity_id = charity.id');
        $this->db->where('user_game_charity.game_id = '.$id);
        $query = $this->db->get();
        return $query->result();
	}

	public function getUserEmail($user_id) {
		$this->db->select("email");
		$this->db->from("tbl_users");
		$this->db->where("user_id", $user_id);

		return $this->db->get()->result();
	}

	public function getAllLinkedStatus($user_id) {
		$this->db->distinct();
		$this->db->select("gt.*, la.id as linked, lac.id as code");
		$this->db->from("gametype");
		$this->db->join("gametype as gt", "gametype.parent = gt.id");
		$this->db->join("linked_accounts as la", "la.type = gt.name and la.user_id = " . $user_id, "left");
		$this->db->join("link_account_codes as lac", "lac.gametype = gt.name and lac.user_id = " . $user_id, "left");
		$this->db->where("gt.account_link", 1);
		$this->db->group_by("gt.parent, la.user_id");

		return $this->db->get()->result();
	}

	public function hasUserLinked($user_id, $gametype) {
		$this->db->from("linked_accounts");
		$this->db->where(array("user_id" => $user_id, "type" => $gametype));
		$exist = $this->db->get()->num_rows();

		return ($exist > 0) ? true: false;
	}

	public function hasUserCreatedCode($user_id, $gametype) {
		$this->db->select("count(id) as count");
		$this->db->from("link_account_codes");
		$this->db->where(array("user_id" => $user_id, "gametype" => $gametype));
		$exist = $this->db->get()->row();

		return ($exist->count > 0) ? true: false;
	}

	public function doesCodeExist($code) {
		$this->db->from("link_account_codes");
		$this->db->where("code", $code);
		$exist = $this->db->get()->row();

		return ($exist->count > 0) ? true: false;
	}

	public function removeLinkedAccount($user_id, $gametype) {
		$this->db->where(array('user_id' => $user_id, 'type' => $gametype));
		$this->db->delete('linked_accounts');

		return $this->db->affected_rows();
	}

	public function createLinkCode($user_id, $gametype) {
		$code = bin2hex(random_bytes(6));

		$exist = $this->doesCodeExist($code);
		while ($exist) {
			$code = bin2hex(random_bytes(6));
			$exist = $this->doesCodeExist($code);
		}

		$data = array(
			"user_id" => $user_id,
			"gametype" => $gametype,
			"code" => $code
		);
	
		$this->db->insert('link_account_codes', $data);

		return array("created" => $this->db->affected_rows(), "code" => $code);
	}

	public function deleteLinkCode($id) {
		$this->db->where("id", $id);
		$this->db->delete('link_account_codes');
	}
}