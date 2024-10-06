<?php

class Question_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function add($data) {
        $this->db->insert('question', $data);
        $insert = $this->db->insert_id();

        if ($insert > 0) {
            return array("status" => "success", "message" => "added question");
        } else {
            return array("status" => "failed", "message" => "could not add question");
        }
    }

    public function update($data, $id) {
        $this->db->where('id', $id);
        $this->db->update('question', $data);

        if ($this->db->affected_rows() > 0) {
            return array("status" => "success", "message" => "updated question");
        } else {
            return array("status" => "failed", "message" => "could not update question");
        }
    }

    public function getTotalQuestions($user_id=null) {
        $this->db->from('question as Q');
		$this->db->join('category as C', 'C.id = Q.category', 'left');

        if (isset($user_id)) {
            $this->db->where('Q.created_by', $user_id);
        }
        
        return $this->db->get()->num_rows();
    }

    public function getQuestions($search=null, $order=null, $limit=null, $offset=null, $user_id=null) {
        $user_type = getprofile()->usertype == '2';

        $this->db->select('Q.*, C.name as category_name');
        $this->db->from('question as Q');
		$this->db->join('category as C', 'C.id = Q.category', 'left');

        if (isset($user_id)) {
            $this->db->where('Q.created_by', $user_id);
        }
        
        if (isset($search) && $search != "") {
            $this->db->group_start();
            $this->db->like("Q.question", $search);
            $this->db->or_like(array("C.name" => $search, "Q.difficulty" => $search));
            $this->db->group_end();
        }

        if (!empty($order["by"]) && !empty($order["arrange"])) {
            $this->db->order_by($order["by"], $order["arrange"]);
        }

        if (isset($limit) && isset($offset)) {
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get()->result();
        foreach ($query as $key => $ques) {
            $ques->editable = ((($this->session->userdata("user_id") == $ques->created_by && $ques->status != 1) || $user_type) ? true : false);
        }

        return $query;
    }

    public function getTotalApprovedQuestions($user_id=null) {
        $this->db->from('question as Q');
		$this->db->join('category as C', 'C.id = Q.category', 'left');

        if (isset($user_id)) {
            $this->db->where('Q.created_by', $user_id);
        }
        
		$this->db->where('Q.status', 1);
        
        return $this->db->get()->num_rows();
    }

    public function getApprovedQuestions($search=null, $order=null, $limit=null, $offset=null, $user_id=null) {
        $this->db->select('Q.*, C.name as category_name');
        $this->db->from('question as Q');
		$this->db->join('category as C', 'C.id = Q.category', 'left');
        
        if (isset($search) && $search != "") {
            $this->db->group_start();
            $this->db->like("Q.question", $search);
            $this->db->or_like(array("C.name" => $search, "Q.difficulty" => $search));
            $this->db->group_end();
        }

        if (isset($user_id)) {
            $this->db->where('Q.created_by', $user_id);
        }

        $this->db->where('Q.status', 1);

        if (!empty($order["by"]) && !empty($order["arrange"])) {
            $this->db->order_by($order["by"], $order["arrange"]);
        }

        if (isset($limit) && isset($offset)) {
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get()->result();

        return $query;
    }

    public function getRandomApprovedQuestions($limit, $where=null, $notwhere=null, $user_id=null) {
        $this->db->select('Q.*, C.name as category_name');
        $this->db->from('question as Q');
		$this->db->join('category as C', 'C.id = Q.category', 'left');

        if (isset($user_id)) {
            $this->db->where('Q.created_by', $user_id);
        }
        
        if (isset($where)) {
            if (isset($where["category"]) && count($where["category"]) > 0) {
                $this->db->where_in('C.id', $where["category"]);
            }

            if (isset($where["difficulty"]) && count($where["difficulty"]) > 0) {
                $this->db->where_in('Q.difficulty', $where["difficulty"]);
            }

            if (isset($where["type"]) && count($where["type"]) > 0) {
                $this->db->where_in('Q.type', $where["type"]);
            }
        }

        if (isset($notwhere) && $notwhere) {
            $this->db->where_not_in('Q.id', $notwhere);
        }

        $this->db->where('Q.status', 1);

        $this->db->order_by('rand()');
        $this->db->limit($limit);

        $query = $this->db->get()->result();

        return $query;
    }

    public function getCategoryList() {
        return $this->db->get('category')->result();
    }

    public function getCategoryByName($value) {
        return $this->db->where('name', $value)->get('category')->row();
    }

    public function getQuestionById($id) {
        return $this->db->where('id', $id)->get('question')->row();
    }

    public function getQuestionByQuestionText($value) {
        return $this->db->where('question', $value)->get('question')->row();
    }
}

?>
