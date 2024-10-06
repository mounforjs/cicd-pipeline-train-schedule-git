<?php

class Feedback_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function feedback_category_list($parent_id=0) {
    	$this->db->where('parent_id',$parent_id);
		$query=$this->db->get('feedback_category'); 
		$result = $query->result_array(); 
		return $result;
    }
    
    public function insert_user_feedback($data, $images) {
        $this->db->insert('user_feedback', $data);

        $feedback_id = $this->db->insert_id();
        if ($feedback_id) {
            if (count($images) > 0) {
				for ($i = 0; $i < sizeof($images); $i++) {
					$feedback_info = array(
						'feedback_image' => sanitizeInput($images[$i], FILTER_VALIDATE_URL),
						'feedback_id' => $feedback_id,                  
					);
					
					$this->db->insert('feedback_images', $feedback_info);
				}
			}
		}
		
        return isset($feedback_id) ? $feedback_id : null;
    }

    public function get_feedback($id) { 
		$this->db->select("user_feedback.*, feedback_category.category_name as category, tbl_users.username");
		$this->db->from('user_feedback');
		$this->db->join('feedback_category', 'user_feedback.category_id = feedback_category.id', 'left');
		$this->db->join('tbl_users', 'user_feedback.user_id = tbl_users.user_id', 'left');
		$this->db->where('user_feedback.id', $id);

		$result = $this->db->get()->result_array()[0];
        $result["images"] = $this->get_feedback_images($id);

        return $result;
	}

    private function get_feedback_images($id) { 
		$this->db->select("feedback_image");
		$this->db->from("feedback_images");
		$this->db->where('feedback_id', $id);
		$this->db->like('feedback_image', "https", "after");

		return $this->db->get()->result_array();
	}
	
	public function get_all_feedbacks($search=null, $order=null, $limit=null, $offset=null) { 
		$this->db->select("user_feedback.*, feedback_category.category_name, tbl_users.firstname, tbl_users.lastname, tbl_users.email");
		$this->db->from('user_feedback');
		$this->db->join('feedback_category', 'user_feedback.category_id = feedback_category.id', 'left');
		$this->db->join('tbl_users', 'user_feedback.user_id = tbl_users.user_id', 'left');
		
        if (isset($search) && $search != "") {
            $this->db->group_start();
            $this->db->like("feedback_description", $search);
            $this->db->or_like(array("feedback_category.category_name" => $search, "tbl_users.firstname" => $search, "tbl_users.lastname" => $search, "rating" => $search, "winwinrating" => $search));
            $this->db->group_end();
        }

        if (!empty($order["by"]) && !empty($order["arrange"])) {
            $this->db->order_by($order["by"], $order["arrange"]);
        } else {
            $this->db->order_by('user_feedback.date_created', 'desc'); 
        }

        if (isset($limit) && isset($offset)) {
            $this->db->limit($limit, $offset);
        }

		$result = $this->db->get()->result();
		return $result;
	}

    public function getTotalFeedback() {
		$this->db->from('user_feedback');
		$this->db->join('feedback_category', 'user_feedback.category_id = feedback_category.id', 'left');
		$this->db->join('tbl_users', 'user_feedback.user_id = tbl_users.user_id', 'left');
		
		return $this->db->get()->num_rows();
    }

    public function get_all_page_links() { 
        $this->db->select("*");
        $this->db->where('status','1');
        $this->db->from('page_links');
        
        $query = $this->db->get();
        return $result = $query->result_array();
    }
}