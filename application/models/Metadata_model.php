<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Metadata_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_metadata($page_name) {
        $this->db->like('url', $page_name);
        $query = $this->db->get('tbl_sitemap');
        return $query->row_array();
    }
}