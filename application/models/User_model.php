<?php


class User_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

	public function get_all_users($search=null, $order=null, $limit=null, $offset=null) {
		$this->db->select('tbl_users.`user_id`, `firstname`, `lastname`, `email`, `username`, `default_fundraise`, tbl_users.usertype, tbl_users.user_status, tbl_users.country, tbl_users.btester_status, tbl_users.creator_status, tbl_users.credit_withdraw_status, tbl_users.created_at, tbl_users.decision_maker, charity.name');
        $this->db->from('tbl_users');
		$this->db->join('charity', 'charity.id = tbl_users.default_fundraise', 'left');
        
        if (isset($search) && $search != "") {
            $this->db->group_start();
            $this->db->like("firstname", $search);
            $this->db->or_like(array("lastname" => $search, "email" => $search));
            $this->db->group_end();
        }

        if (isset($order)) {
            $this->db->order_by($order["by"], $order["arrange"]);
        }

        if (isset($limit) && isset($offset)) {
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get();
        return $query->result();
    }

    public function getTotalUsers() {
        $this->db->select('tbl_users.`user_id`, `firstname`, `lastname`, `email`, `username`, `default_fundraise`, tbl_users.user_status, tbl_users.country, tbl_users.btester_status, tbl_users.creator_status, tbl_users.credit_withdraw_status, tbl_users.created_at, tbl_users.decision_maker, charity.name');
        $this->db->from('tbl_users');
		$this->db->join('charity', 'charity.id = tbl_users.default_fundraise', 'left');
        
        return $this->db->get()->num_rows();
    }

    public function insert_user() {
        $inputs = $this->input->post();
		$options = array (
			'cost' => 12
		);

		$encrypted = password_hash($this->input->post('password') ,PASSWORD_BCRYPT,$options);
        $data = array(
            'firstname' => $inputs['firstname'],
            'lastname' => $inputs['lastname'],
            'country' => $inputs['country'],
            'username' => $inputs['username'],
            'password' => $encrypted,
            'email' => $inputs['unEmail'],
            'profile_img_path' => '',
            'created_at' => date('Y-m-d H:i:s'),
            'decision_maker' => !empty($inputs['cv']) ?  $inputs['cv']: 'No',
        );

        $config['upload_path'] = './assets/uploads/profile_pic/';
        $config['allowed_types'] = 'gif|jpg|png';
        $config['remove_spaces'] = true;
        $config['encrypt_name'] = true;

        $this->load->library('upload', $config);

        if ($this->upload->do_upload("profile_pic")) {
            $logo = $this->upload->data();
            $image1 = $logo['file_name'];
			$path = "./assets/uploads/profile_pic/";
            $data['profile_img_path'] = $path.$logo['file_name'];
        }

		if(isReferral()) {
			$data['is_referred'] = 1;
			$data['redeemed_referral_id'] = getReferralForUserTransaction();
		}

        $result = $this->db->insert('tbl_users', $data);
        if ($result) {
            $last_inserted_id = $this->db->insert_id();

			$this->create_user_noti_preferences($last_inserted_id);
			$this->create_user_email_preferences($last_inserted_id);

			$teams = $this->input->post('teamname');



			if ($this->input->post('teamname')) {
				$teams_arr = explode(',', $teams );
				$ins_cnt = 0;
				$fund_ids = array();
				foreach($teams_arr as $team) {
					$frc_team = strtolower('frc'.trim($team,'+ '));
					$team_info = $this->api_blue_alliance($frc_team);
					if (!isset($team_info->Errors)) {
						$charity = $this->db->get_where('charity',['name' => $team_info->nickname])->row();
						if (!empty($charity)) {
							$fund_ids[] = $charity->id;
						} else {
							$address = $team_info->city.', '.$team_info->state_prov.', '.$team_info->country;

							$data = array(
								'user_id' => $last_inserted_id,
								'user_type' => 'user' ,
								'team_number' => $team_info->team_number,
								'name' => $team_info->nickname,
								'Address' => $address,
								'featured' => 'No' ,
								'approved' => 'Yes' ,
								'Default' => 'No',
								'Description' => $team_info->name,
								'non_profit_501c3' => 'Yes'
							);

							$this->db->insert('charity',$data);
							$fund_ids[] = $this->db->insert_id();
						}

						$ins_cnt++;
					}
				}

				$this->db->where(['user_id' => $last_inserted_id]);
				# change to slugs, though not sure if needed (teamname of profile section removed for now) 
				$data = array('default_fundraise' => $fund_ids[0], 'supported_fundraise' => implode(',', $fund_ids));
				$this->db->update('tbl_users',$data);
			}

			return $last_inserted_id;
        } else {
            return false;
        }
    }

	public function update_user($data,$table) {
		if ($table == 'user') {
			if (isset($data['updateEmail'])  && !empty($data['updateEmail'])) {
				$result = $this->db->get_where("tbl_users", array("email" => $data['updateEmail'], "user_id !=" => $this->session->userdata("user_id")))->num_rows();
				if ($result <= 0) {
					$data['email'] = $data['updateEmail'];
					unset($data['updateEmail']);
				} else {
					return array("status" => "failed", "msg" => "Unable to use this email.");
				}
			}

			if (isset($data['password'])  && !empty($data['password'])) {
				$options = array (
					'cost' => 12
				);
				$encrypted = password_hash($data['password'] ,PASSWORD_BCRYPT,$options);
				$data['password'] = $encrypted;
			} else {
				unset($data['password']);
			}

			if (isset($data['profile_img_path']) && (!$data['profile_img_path'] || empty($data['profile_img_path']))) {
				unset($data['profile_img_path']);
			}

			if (isset($data['pathway'])  && !empty($data['pathway'])) {
				$data['pathway'] = implode(',',$data['pathway']);
			}

			if (isset($data['challenge'])  && !empty($data['challenge'])) {
				$data['challenge'] = implode(',',$data['challenge']);
			}

			if (isset($data['teamname'])) {
				$data['frc_team_number'] = $data['teamname'];
				unset($data['teamname']);
			}

			if (isset($data['teamnameftc'])) {
				$data['ftc_team_number'] = $data['teamnameftc'];
				unset($data['teamnameftc']);
			}

			if (isset($data['teamnamefll'])) {
				$data['fll_team_number'] = $data['teamnamefll'];
				unset($data['teamnamefll']);
			}

			if (isset($data['teamnamejrfll'])) {
				$data['jrfll_team_number'] = $data['teamnamejrfll'];
				unset($data['teamnamejrfll']);
			}

			unset($data['finish']);
			unset($data['profile_password']);

			$this->db->where( 'user_id', $this->session->userdata('user_id'));
			if ($this->db->update('tbl_users',$data)) {
				return array("status" => "success");
			} else {
				return array("status" => "failed", "msg" => "Something went wrong; please try again later.");
			}
		}
	}

	public function get_loginuser() {
		$this->db->where('user_ID',$this->session->userdata('user_id'));

		$query=$this->db->get(' tbl_users');
		return $result=$query->result_array();
	}

	public function get_stripe_payout_id_status() {
		$this->db->select('payout_stripe_connect_account_id');
		$this->db->where('user_ID',$this->session->userdata('user_id'));
		
		if (empty($this->db->get('tbl_users')->row()->payout_stripe_connect_account_id)) {
			return 0;
		}
		else {
			return 1;
		}
	}

	public function get_user_profile($user_id = '') {
		if ($user_id == '') {
			$user_id = $this->session->userdata('user_id');
		}
		$this->db->where('user_id',$user_id);
		return $this->db->get('tbl_users')->row();
	}

	public function get_wishlist_count($user_id = '') {
		if ($user_id == '') {
			$user_id = $this->session->userdata('user_id');
		}
		$this->db->where('user_id',$user_id);
		return count($this->db->get('user_wishlist')->result());
	}

	public function get_created_count($user_id = '') {
		if ($user_id == '') {
			$user_id = $this->session->userdata('user_id');
		}
		$this->db->where('user_id',$user_id);
		return count($this->db->get('game')->result());
	}

	public function get_user_field($id,$field) {
        $query = $this->db->select($field)
            ->where('user_id', $id)
            ->get('tbl_users');
        return $query->row();
    }

    public function login($email, $password) {
		$this->db->where(['email' => $email]);
		$result1=$this->db->get('tbl_users');

		if ($result1->num_rows() != 0) {

			$db_password = $result1->row()->password;

			if (password_verify($password, $db_password)) {
				if ($result1->row(0)->user_status == 'No') { //account suspended
					$data= array ('errors' => 'Account has been suspended. Please contact an admin at suuport@winwinlabs.org.');
				} else {
					$data['id'] = $result1->row(0)->user_id;
				}
			} else { //password incorrect
				$data= array ('errors' => 'That password was incorrect. Please try again.');
			}
		} else { //email doesnt exist
			$data= array ('errors' => 'Email not associated with any account.');
		}

		return $data;
	}

    public function reset_user() {
        $inputs = $this->input->post();

        $data = array(
            'reset_email' => $inputs['reset_email'],
        );

        $config['remove_spaces'] = true;
        $config['encrypt_name'] = true;

        $result = $this->db->insert($this->table, $data);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

	public function get_fundraise_name($id) {
		$this->db->select('name');
		$this->db->where('id',$id);

		$result = $this->db->get('charity')->row()->name;
		return $result;
	}

	public function get_supported_fundraise($fund_id) {
		$this->db->select('tbl_users.supported_fundraise');
		$this->db->where('tbl_users.user_id',$this->session->userdata('user_id'));
		$this->db->where("find_in_set($fund_id,tbl_users.supported_fundraise)!=",0);
		$result = $this->db->get('tbl_users')->row()->supported_fundraise;
		return $result;
	}

	public function api_blue_alliance($team_name='') {
		if (!empty($team_name)) {
			$tname = $team_name;
			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => "https://www.thebluealliance.com/api/v3/team/".$tname."/simple",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_HTTPHEADER => array(
					"cache-control: no-cache",
					"postman-token: 1b92bfad-840a-82cc-e26d-f259351a4d71",
					"x-tba-auth-key: MRIpTp81zHNJr7WFv4ghCOiuJFWYPgJgKRGpgD1NsnJLuwGZB3jFGnF6NUZM731v"
				),
			));

			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);

			if ($err) {
				return "error";
			} else {
				return json_decode($response);
			}
		}
	}

	public function create_new_user($username, $email, $password, $role) {
		$now = date('Y-m-d H:i:s');
		$data = array(
			'username' => $username,
			'email' => $email,
			'password' => $password,
			'usertype' => $role,
			'created_at' => $now
		);

		$this->db->insert('tbl_users', $data);
	}

  	public function update_admin_created_user($id,$content,$name) {
         switch ($name) {
           case "firstname":
               $data = array( 'firstname' => $content );
               break;
           case "lastname":
               $data = array( 'lastname' => $content );
               break;
           case "username":
               $data = array( 'username' => $content );
               break;
           case "email":
                $data = array( 'email' => $content );
               break;
       }

       $this->db->where('user_id', (int)$id);

       $result=$this->db->update('tbl_users', $data);
       return $result;
	}

	public function update_user_password($id,$status) {
        $data = array( 'password' => $status );
        $this->db->where('user_id', $id);

        $result=$this->db->update('tbl_users', $data);
        return $result;
    }

	public function delete_user($id) {
        $this->db->where('user_id', $id );
        $del = $this->db->delete('tbl_users');
        if ($del == '1') {
            return true;
        } else {
            return false;
        }
    }

	public function get_subscription_list() {
		$this->db->from('subscribe_list')->order_by('id','desc');

		return $this->db->get()->result();
	}

	public function w9_form_add_update() {
		$data = array(
			'user_id' => $this->session->userdata('user_id'),
			'business_name' => sanitizeInput($this->input->post('businessname', true), FILTER_SANITIZE_STRING),
			'taxpayer_id'=> sanitizeInput(preg_replace("/[^0-9]/", "", $this->input->post('taxpayerid', true)), FILTER_SANITIZE_STRING),
			'ssn_or_ein'=> sanitizeInput($this->input->post('taxpayidtype'), FILTER_VALIDATE_BOOLEAN),
			'tax_classification'=> sanitizeInput($this->input->post('taxclass', true), FILTER_SANITIZE_STRING),
			'exempt_payee_code'=> sanitizeInput($this->input->post('exemptpayee', true), FILTER_SANITIZE_STRING),
			'fatca_report_code'=> sanitizeInput($this->input->post('fatcareporting', true), FILTER_SANITIZE_STRING),
			'address'=> sanitizeInput($this->input->post('address', true), FILTER_SANITIZE_STRING),
			'city'=> sanitizeInput($this->input->post('city', true), FILTER_SANITIZE_STRING),
			'state'=> sanitizeInput($this->input->post('state', true), FILTER_SANITIZE_STRING),
			'zipcode'=> str_pad(sanitizeInput((int)$this->input->post("zip", true), FILTER_VALIDATE_INT), 5, "0", STR_PAD_LEFT),
			'signature'=> sanitizeInput($this->input->post('signature', true), FILTER_SANITIZE_STRING),
			'electronic_copy'=> sanitizeInput($this->input->post('ecopy', true), FILTER_VALIDATE_BOOLEAN),
			'created_at'=> date('Y-m-d H:i:s'),
		);

		return $this->db->insert('tbl_w9', $data);
	}

	public function w9_form_get_data($user_id = '') {
		if ($user_id == '') {
			$user_id = $this->session->userdata('user_id');
		}
		
		$this->db->where('user_id',$user_id);
		$this->db->order_by("created_at", "desc");
		$this->db->limit(1);

		return $this->db->get('tbl_w9')->result();
	}

	public function update_user_status($id,$status) {
        $data = array( 'user_status' => $status );
        $this->db->where('user_id', $id);

        $result=$this->db->update('tbl_users', $data);
        return $result;
    }

     public function update_tester_status($id,$status) {
        $data = array( 'btester_status' => $status );
        $this->db->where('user_id', $id);

        $result=$this->db->update('tbl_users', $data);
        return $result;
    }

      public function update_creator_status($id,$status) {
        $data = array( 'creator_status' => $status );
        $this->db->where('user_id', $id);

        $result=$this->db->update('tbl_users', $data);
        return $result;
    }

	public function update_credit_withdraw_status($id,$status) {
        $data = array( 'credit_withdraw_status' => $status );
        $this->db->where('user_id', $id);
		
        $result=$this->db->update('tbl_users', $data);
        return $result;
    }

	public function get_user_noti_preferences($user_id) {
		$this->db->where('user_id', $user_id);
		
        return $this->db->get('notification_preferences')->row();
    }

	public function get_user_email_preferences($user_id) {
        $this->db->where('user_id', $user_id);
		
        return $this->db->get('email_preferences')->row();
    }

	public function create_user_noti_preferences($user_id) {
        return $this->db->insert('notification_preferences', array("user_id" => $user_id));
    }

	public function create_user_email_preferences($user_id) {
		return $this->db->insert('email_preferences', array("user_id" => $user_id));
    }

	public function update_user_noti_preferences($user_id, $data) {
		$this->db->where('user_id', $user_id);
		
        $update = $this->db->update('notification_preferences', $data);
		return array("status" => (($update) ? "success" : "failed"));
    }

	public function update_user_email_preferences($user_id, $data) {
        $this->db->where('user_id', $user_id);
		
        $update = $this->db->update('email_preferences', $data);
		return array("status" => (($update) ? "success" : "failed"));
    }

	public function get_user_id_name() {
		$this->db->select('`user_id`, `firstname`, `lastname`');
		$this->db->from('tbl_users');
		$users = $this->db->get()->result_array();

		foreach($users as $key => &$val){
			$val['fullname'] = $val['firstname']. ' ' .$val['lastname'];
		}
		return $users;
	}

	public function insert_stripe_connect_account($accountId) {
		$data = array(
			'payout_stripe_connect_account_id' => $accountId
		);
		$this->db->where('user_id', $this->session->userdata('user_id'));
		$update = $this->db->update('tbl_users', $data);
		return array("status" => (($update) ? "success" : "failed"));
	}

	public function get_admin_roles() {
		$this->db->select('role, permission');
        $this->db->from('tbl_admin_permissions');
        return $this->db->get()->result_array();
	}

	public function get_user_permission_string($type) {
		$this->db->select('role');
		$this->db->where('permission', $type);
        $this->db->from('tbl_admin_permissions');
        return $this->db->get()->row();
	}

	public function update_user_role($id, $permission) {
        $data = array( 'usertype' => $permission );
        $this->db->where('user_id', $id);

        $result=$this->db->update('tbl_users', $data);
        return $result;
    }
}
