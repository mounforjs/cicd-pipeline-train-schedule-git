<?php

class Content_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }

    public function get_content($main_id) {
        $this->db->select('*');
        $this->db->where('page_id',$main_id);
        $this->db->from('tbl_content_pages');
        $query = $this->db->get();

        return $query->result();
    }
}
