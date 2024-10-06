<?php


class About_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_content() {
		$this->db->select('description');
        $this->db->where('page', 'about');
        $this->db->from('tbl_content');

        $query = $this->db->get();
        return $query->result();
    }

    public function update_about_content($id,$status) {            
        $data = array( 'description' => $status );
        $this->db->where('page_id', (int)$id);

        $result=$this->db->update('tbl_content', $data); 
        return $result;
    }
}