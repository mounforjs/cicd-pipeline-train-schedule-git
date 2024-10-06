<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Aws_keys_model extends CI_Model {

    public function get_aws_keys() {
        $query = $this->db->get('aws_keys');
        return $query->row();
    }
}