<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Charity_model extends CI_Model {

	public function get_user_default_fundraise($approved=false) {
		$this->db->select('C.slug, C.name, C.fundraise_type, C.charity_url, C.Description, C.Image, C.approved, 1 as `def`');
		$this->db->join("tbl_users U", 'C.slug = U.default_fundraise');
		$this->db->where('U.user_id',$this->session->userdata('user_id'));

		if ($approved) {
			$this->db->where('C.approved', 'Yes');
		}

		$result = $this->db->get('charity C')->row();
		if (isset($result)) {
			return $result;
		} else {
			$this->db->select('C.slug, C.name, C.fundraise_type, C.charity_url, C.Description, C.Image, C.approved, 0 as `def`');
			$this->db->from("charity C");
			$this->db->where(["approved" => "Yes", "order_no IS NOT NULL" => NULL]);
			$this->db->order_by('C.order_no', "ASC");
			$this->db->limit(1, 0);
	
			return $this->db->get()->row();
		}
    }

	public function get_fundraise($slug) {
		$this->db->select('C.slug, C.name, C.fundraise_type, C.charity_url, C.Description, C.Image, C.approved, if(U.default_fundraise=C.slug, 1, 0) as `def`');
		if ($this->session->userdata("user_id")) {
			$this->db->join("tbl_users U", 'U.user_id=' . $this->session->userdata("user_id"), 'left');
		}
		
		$this->db->where('C.slug', $slug);

		$query = $this->db->get('charity C');
		return $query->row();
    }

	public function get_game_fundraiser($id) {
		$this->db->select('C.id, C.slug, C.name, C.fundraise_type, C.charity_url, C.Description, C.Image, C.approved, if(U.default_fundraise=C.slug, 1, 0) as `def`');

		$this->db->from('game as g');
		$this->db->join("user_game_charity as ugc", 'ugc.game_id=g.id');
		$this->db->join("charity as C", 'ugc.charity_id=C.id');
		$this->db->join("tbl_users U", 'U.user_id=ugc.user_id', 'left');

		$this->db->where('g.id', $id);

		return $this->db->get()->row();
    }

	public function get_fundraise_created_byuser($sub_type='', $slug="", $offset=0, $limit=8) {
		$this->db->select('C.name, C.fundraise_type, C.charity_url, C.slug, C.Description, C.Image, C.approved, R.status as fundraiserPendingStatus');
		$this->db->where('C.user_ID',$this->session->userdata('user_id'));

		if ($sub_type != '' && $sub_type != 'all')
			$this->db->where('C.fundraise_type' ,$sub_type);

		if (!empty($slug))
			$this->db->where('C.slug' ,$slug);

		if (empty($slug))
			$this->db->limit($limit, $offset);

		$this->db->join("tbl_users U", 'C.user_id = U.user_id', 'left');
		$this->db->join("tblFundraiserEditReason R", 'R.fundraiser_id = C.id and R.user_id = C.user_ID and R.status = 0', 'left');
		$this->db->order_by("(CASE WHEN U.default_fundraise IN (C.slug) THEN -1 ELSE U.default_fundraise END) ASC, C.id DESC");
		$result = $this->db->get('charity C')->result_array();

		foreach ($result as $key => $value) {
			$fundraise_data = $this->get_total_raised($value['slug']);

			if (is_object($fundraise_data)) {
				$result[$key]['raised'] = number_format($fundraise_data->raised, 2);
			} else {
				$result[$key]['raised'] = 0;
			}
		}

		return $result;
	}

	public function get_fundraise_supported_byuser($sub_type='', $slug="", $offset=0) {
		$this->db->select('charity.name, charity.fundraise_type, charity.slug, charity.charity_url, charity.Description, charity.Image, charity.approved');
		$this->db->join("tbl_users","find_in_set(charity.slug, tbl_users.supported_fundraise)");
		$this->db->group_by('charity.id');
		$this->db->where('charity.user_ID',$this->session->userdata('user_id'));
		$this->db->or_where('tbl_users.user_id',$this->session->userdata('user_id'));
		if ($sub_type != '' && $sub_type != 'all')
			$this->db->where('charity.fundraise_type', $sub_type);
		if (!empty($slug))
			$this->db->where('charity.slug' ,$slug);
		if (empty($slug))
			$this->db->limit(8, $offset);
		$result = $this->db->get('charity')->result_array();

		foreach ($result as $key => $value) {
			$fundraise_data = $this->get_total_raised($value['slug']);

			if (is_object($fundraise_data)) {
				$result[$key]['raised'] = number_format($fundraise_data->raised, 2);
			} else {
				$result[$key]['raised'] = 0;
			}
		}

		return $result;
	}

	public function get_all_fundraiser_list($sub_type='', $slug="", $approved=false, $limit=0, $offset=0) {
		$user_id = $this->session->userdata('user_id');
		$user = $this->db->where('user_id', $user_id)->get('tbl_users')->row();
		$this->db->select('C.user_id, C.name, C.fundraise_type, C.slug, C.charity_url, C.Description, C.Image, C.approved, R.status as fundraiserPendingStatus');
		
		if (!$user_id) {
			$this->db->where('C.approved','Yes'); 
		} else {
			if (isset($user) && $user->usertype != '2') {
				if ($approved) {
					$this->db->where('C.approved','Yes'); 
				} else {
					$this->db->group_start(); 
					$this->db->where('C.approved','Yes'); 
					$this->db->or_where('C.user_id', $user_id); 
					$this->db->group_end(); 
				}
			} else {
				if ($approved) {
					$this->db->where('C.approved','Yes'); 
				}
			}
		}

		if ($sub_type != '' && $sub_type != 'all')
			$this->db->where('C.fundraise_type' ,$sub_type);

		if (!empty($slug))
			$this->db->where('C.slug' ,$slug);
		
		$this->db->join("tblFundraiserEditReason R", 'R.fundraiser_id = C.id and R.user_id = C.user_ID and R.status = 0', 'left');

		if (empty($slug)) {
			if ($limit > 0) {
				$this->db->limit($limit, $offset);
			}
		}

		$this->db->order_by('order_no');
		$result = $this->db->get('charity C')->result_array();

		foreach ($result as $key => $value) {
			$fundraise_data = $this->get_total_raised($value['slug']);

			if (is_object($fundraise_data)) {
				$result[$key]['raised'] = number_format($fundraise_data->raised, 2);
			} else {
				$result[$key]['raised'] = 0;
			}
		}

		return $result;
	}

	public function get_total_raised($slug) {
		$this->db->select('coalesce(sum(raised),0) as raised');
		$this->db->from('charity as C');
		$this->db->join('user_game_charity as ugc', 'C.id=ugc.charity_id', 'left');
		$this->db->join('(select id, sum(credits) as raised from game left join payments on game.id=payments.game_id group by id) as gp', 'gp.id=ugc.game_id', 'left');
		
		$this->db->where('C.slug', $slug);
		$this->db->group_by('C.id', 'ASC');
		return $this->db->get()->row();
	}
	
	public function updateDefaultFundraiser($slug) {
		$new_default = array('default_fundraise'=> $slug);
	    $this->db->where('user_id', $this->session->userdata('user_id'));
		return $this->db->update('tbl_users', $new_default);
	}

	public function deleteCreatedFundraiser($slug) {
		$this->db->where('slug', $slug);
		return $this->db->delete('charity');
	}

	public function removeSupportedFundraiser($slug) {
		$fundraiser = $this->getFundraiserDetailBySlug($slug);

		$sql = "UPDATE tbl_users SET supported_fundraise = TRIM(BOTH ',' FROM
					REPLACE(
						REPLACE(
							CONCAT(',', supported_fundraise, ',')
						, '".$fundraiser->slug.",', ''),
					',,', ',')
					)
				WHERE user_id = ".$this->session->userdata('user_id');

	 	return $this->db->query($sql);
	}

	public function getEditedFundraiserDetails($slug) {
		$user = $this->db->where('user_id', $this->session->userdata('user_id'))->get('tbl_users')->row();

		$this->db->select('C.name, C.slug, C.fundraise_type, C.charity_url, C.Description, C.Image, C.Address, C.Contact_personnel, C.Phonenumber, C.Tax_ID, IFNULL(U.default_fundraise, 0) as isDefault');
		$this->db->where('C.slug', $slug);
		
		if ($user->usertype != '2') {
			$this->db->where('C.user_id',$this->session->userdata('user_id'));
		}

		$this->db->join("tbl_users U", "U.user_id=C.user_ID", 'LEFT');

		$query=$this->db->get('charity C');
		if ($query->num_rows()>0) {
			return $query->row();
		} else { 
			return false; 
		}
	}

	public function getPendingEditRequestForFundraiser($slug) {
		$fundraiser = $this->getFundraiserIdBySlug($slug);
		return $this->db->select('*')->where('status', 0)->where('fundraiser_id', $fundraiser->id)->get('tblFundraiserEditReason')->row();
	}

	public function insertFundraiserEditReason($slug, $reason) {
		$fundraiser = $this->getFundraiserIdBySlug($slug);
		$data= array(
			'fundraiser_id' => $fundraiser->id,
			'user_id' => $this->session->userdata('user_id'),
			'reason' => $reason,
			'status' => 0
		);

		return $this->db->insert('tblFundraiserEditReason',$data);
	}

	public function getAllBeneficiaryDetails($slug) {
		return $this->db->where('slug', $slug)->get('charity')->row();
	}

	public function getBeneficiaryNameBySlug($slug){
		return $this->db->select('slug, name')->where('slug', $slug)->get('charity')->row();
	}

	public function getFundraiserDetailById($id) {
		return $this->db->select('name, slug, fundraise_type, Description, charity_url, Image')->where('id', $id)->get('charity')->row();
	}

	public function getFundraiserDetailBySlug($slug) {
		return $this->db->select('name, slug, fundraise_type, Description, charity_url, Image')->where('slug', $slug)->get('charity')->row();
	}

	public function getFundraiserIdBySlug($slug) {
		return $this->db->select('id, name, fundraise_type, approved')->where('slug', $slug)->get('charity')->row();
	}

	public function get_charity_all() {
		$this->db->select("*");
		$this->db->from('charity_under_user');
		$this->db->join(' tbl_users', 'charity_under_user.User_ID = tbl_users.user_id');
		$query = $this->db->get();

		return $result=$query->result_array();
	}

	public function create_fundraiser($user_id, $slug, $name, $isUpdate=false) {
		if ($isUpdate)	{
			$charity=$this->db->where('name', $name)->where('slug !=', $slug)->get('charity');
		} else {
			$charity=$this->db->get_where('charity',['name' => $name]);
		}

		$file = sanitizeInput($this->input->post('fundraise_img_path'), FILTER_VALIDATE_URL);
		if ($charity->num_rows()>0) {
			return '2';  //already exists
		} else {
			if(sanitizeInput($this->input->post('is_non'), FILTER_VALIDATE_BOOLEAN)){ $profit = 'Yes'; } else { $profit = 'No'; }
			$data = array(
				'user_id' => $user_id,
				'user_type' => (getprofile()->usertype == "2") ?'admin' :'user' ,
				'fundraise_type' => sanitizeInput($this->input->post('fundraise_type'), FILTER_SANITIZE_STRING),
				'name' => $name,
				'Address' => sanitizeInput($this->input->post('address', true), FILTER_SANITIZE_STRING),
				'charity_url' => sanitizeInput($this->input->post('charity_url', true), FILTER_VALIDATE_URL),
				'Contact_personnel' => sanitizeInput($this->input->post('contact', true), FILTER_SANITIZE_STRING),
				'Phonenumber' => sanitizeInput($this->input->post('phone', true), FILTER_SANITIZE_STRING),
				'Tax_ID' => sanitizeInput($this->input->post('form_charity_tax', true), FILTER_SANITIZE_STRING),
				'non_profit_501c3' => $profit,
				'featured' => (getprofile()->usertype == "2") ?'Yes' :'No' ,
				'order_no' => sanitizeInput($this->input->post('order'), FILTER_VALIDATE_INT),
				'featured' => 'No' ,
				'approved' => (getprofile()->usertype == "2") ?'Yes' :'No' ,
				'Default' =>  'No',
				'Description' => sanitizeInput($this->input->post('form_charity_desc', true), FILTER_SANITIZE_STRING)
			);

			if ($file != '') {
				$data['Image'] = $file;
			}

			$title = $name;
			if ($isUpdate)	{
        		$exist = $this->db->where('name', $title)->where('slug !=', '')->where('slug !=', $slug)->from('charity')->count_all_results();
        	} else {
        		$exist = $this->db->where('name', $title)->where('slug !=', '')->from('charity')->count_all_results();
        	}

            if (isset($exist) and $exist > 0)
                $title = (str_replace(' ', '-', strtolower(clean_special_char(trim($title))))).'-'.$exist;
            else
                $title = (str_replace(' ', '-', strtolower(clean_special_char(trim($title)))));
				
        	$data['slug'] = $title;

			if ($isUpdate == true) {
				unset($data['user_id']);
				unset($data['user_type']);

				$reasonTbl = $this->db->where('fundraiser_id', $charity->id)->where('status', 0)->get('tblFundraiserEditReason')->row();
				if (isset($reasonTbl->id)) {
					$this->db->where('id', $reasonTbl->id);
					$this->db->update('tblFundraiserEditReason',['status' => 1]);
				}

				$this->db->where('slug', $slug);
				$this->db->update('charity',$data);

				return ($this->db->affected_rows() > 0) ? $this->db->insert_id() : FALSE;
			} else {
				$this->db->insert('charity',$data);
				return ($this->db->affected_rows() > 0) ? $this->db->insert_id() : FALSE;
			}
		}
	}


	public function getadmincharityname($cname, $slug = '') {
		$this->db->where('name',$cname);
		if (!empty($fund_id)) {
			$this->db->where('slug!=',$slug);
		}

		$query = $this->db->get('charity');
		if ($query->num_rows()>0) {
			echo "false";
		} else {
			echo "true";
		}
	}

	public function get_charity_all_default() {
		$this->db->get('order_no', 'asc');

		$query=$this->db->get('charity');
		return $result=$query->result_array();
	}

	public function get_all_fundraise() {
		$this->db->order_by('fundraise_type', 'asc');
		$query=$this->db->get('charity');
		return $result=$query->result();
	}

	public function get_approved_charity_list($user_id="", $type='') {
		$this->db->select('slug, name, fundraise_type, Description, Image');

		$this->db->where(['Approved' => 'Yes']);
		if ($user_id != '' && $user_id != 'all') {
			$this->db->select('if(U.default_fundraise=slug, 1, 0) as `def`');
			$this->db->join("tbl_users U", 'slug = U.default_fundraise');
			$this->db->where('U.user_id', $user_id);
		}

		if ($type!='') {
			$this->db->where(['fundraise_type' => $type]);
		}

        return $this->db->get('charity')->result();
    }

	public function get_beneficiary_list($type='all', $sub_type=null, $all=false) {
		$user_id = $this->session->userdata('user_id');
		$user = $this->db->where('user_id', $user_id)->get('tbl_users')->row();

		$this->db->select('c.slug, c.name');
		$this->db->from('charity as c');

		if (!$user_id) {
			$this->db->where('c.approved','Yes'); 
		} else {
			// created, supported
			if (isset($type) && $type != 'all') {
				if ($type == "created") {
					$this->db->join('tbl_users as u', 'c.user_id=u.user_id');
					$this->db->where('c.user_id', $user_id); 
				} else {
					$this->db->join('tbl_users as u', 'c.slug = u.supported_fundraise');
					$this->db->where('c.user_id', $user_id); 
				}
			} else { // all
				// $all - show admins what they would see as a normal user unless specified
				if (isset($user) && (($user->usertype != '2') || ($user->usertype == '2' && !$all))) {
					$this->db->join('tbl_users as u', 'c.user_id=u.user_id');
					$this->db->group_start(); 
					$this->db->where('c.approved', 'Yes'); 
					$this->db->or_where('c.user_id', $user_id); 
					$this->db->group_end(); 
				}
			}
		}

		//charity, project, cause, education
		if (isset($sub_type) && !empty($sub_type) && $sub_type != 'all') {
			$this->db->where('c.fundraise_type', $sub_type);
		}

		$this->db->order_by('name', 'asc');

		return $this->db->get()->result_array();
    }

	public function updateexist_admincharity() {
		$user_id = $this->session->userdata('user_id');
		if( sanitizeInput($this->input->post('is_non'), FILTER_VALIDATE_BOOLEAN)){
			$profit = 'Yes';
		} else {
			$profit = 'No';
		}
		$data = array(
			'user_id' => $user_id,
			'user_type' => (getprofile()->usertype == "2") ?'admin' :'user' ,
			'fundraise_type' => sanitizeInput($this->input->post('fundraise_type'), FILTER_SANITIZE_STRING),
			'name' => sanitizeInput($this->input->post('name', true), FILTER_SANITIZE_STRING),
			'Address' => sanitizeInput($this->input->post('address', true), FILTER_SANITIZE_STRING),
			'charity_url' => sanitizeInput($this->input->post('charity_url', true), FILTER_VALIDATE_URL),
			'Contact_personnel' => sanitizeInput($this->input->post('contact', true), FILTER_SANITIZE_STRING),
			'Phonenumber' => sanitizeInput($this->input->post('phone', true), FILTER_SANITIZE_STRING),
			'Tax_ID' => sanitizeInput($this->input->post('tax_id', true), FILTER_SANITIZE_STRING),
			'non_profit_501c3' => $profit,
			'featured' => (getprofile()->usertype == "2") ?'Yes' :'No' ,
			'order_no' => sanitizeInput($this->input->post('order'), FILTER_VALIDATE_INT),
			'approved' => (getprofile()->usertype == "2") ?'Yes' :'No' ,
			'Default' =>  'No',
			'Description' => sanitizeInput($this->input->post('description', true), FILTER_SANITIZE_STRING)
		);
		
		$this->load->library('aws_s3');
		if ($_FILES['img1']['name']!="") {
			$file = $this->aws_s3->upload($_FILES['img1']['name'], AWS_Bucket_GameImage);
			if (is_array($file)) {
				$data['Image'] = $file['path'];
			}
		}

		$this->db->where(['id' => sanitizeInput($this->input->post('update_id'), FILTER_VALIDATE_INT)]);
		$this->db->update('charity',$data);
		return ($this->db->affected_rows() > 0) ? true : false;
	}
}
