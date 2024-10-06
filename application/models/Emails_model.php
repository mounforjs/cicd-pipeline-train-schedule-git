<?php

class Emails_model extends CI_Model 
{
    public function __construct() {
        parent::__construct();
    }

    public function get_content() {
		$this->db->select('description,email_subject');
        $this->db->from('tbl_content');

        $query = $this->db->get();
        return $query->result();
    }

    public function update_email_content($id,$subject,$description) {
        $data = array( 'description' => $description, 'email_subject' => $subject );
        $this->db->where('page_id', (int)$id);

        $result=$this->db->update('tbl_content', $data);
        return $result;
    }
}
