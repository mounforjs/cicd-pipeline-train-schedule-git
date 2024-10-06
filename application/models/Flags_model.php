<?php

class Flags_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function insert_flag() {
        $input1 = $this->input->post();
        $data = array(
            'game_id' => $input1['gid'],
            'user_id' => $input1['uid'],
            'flag_description' => $input1['desc'],
        );
    
        $result = ($this->db->insert('tbl_flag', $data)) ? '1' : '0';
        return $result;
    }

    public function get_flag_status($game_id) {
        $user_id = $this->session->userdata('user_id');
        $this->db->select('*');
        $this->db->from('tbl_flag');
        $this->db->where('game_id',$game_id);
        $this->db->where('user_id',$user_id);

        $query = $this->db->get()->result();
        if (!empty($query)) {
            return 1;
        } else {
            return 0;
        }
    }

    public function fetch_flag_detail($slug, $search=null, $order=null, $limit=null, $offset=null) {
        $this->db->select('game.name, game.user_id as creator_id, tbl_flag.created_at, tbl_flag.flag_description, tbl_users.firstname, tbl_users.lastname');
        $this->db->from('game');
        $this->db->where('slug',$slug);
        $this->db->join('tbl_flag', 'tbl_flag.game_id = game.id');
        $this->db->join('tbl_users', 'tbl_flag.user_id = tbl_users.user_id', 'left');

        if (isset($search) && $search != "") {
            $this->db->group_start();
            $this->db->like("tbl_flag.flag_description", $search);
            $this->db->or_like(array("tbl_users.firstname" => $search, "tbl_users.firstname" => $search));
            $this->db->group_end();
        }

        if (!empty($order["by"]) && !empty($order["arrange"])) {
            $this->db->order_by($order["by"], $order["arrange"]);
        }

        if (isset($limit) && isset($offset)) {
            $this->db->limit($limit, $offset);
        }
       
        $result = $this->db->get()->result();

        return $result;
    }

    public function getTotalFlags($slug) {
        $this->db->from('game');
        $this->db->where('slug', $slug);
        $this->db->join('tbl_flag', 'tbl_flag.game_id = game.id');
        $this->db->join('tbl_users', 'tbl_flag.user_id = tbl_users.user_id', 'left');

        return $this->db->get()->num_rows();
    }
}