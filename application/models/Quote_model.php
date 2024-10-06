<?php


class Quote_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }
    
    public function insert_quotes($data) {
		$result = $this->db->insert('quotes', $data);
        return $result;
	}

    public function update_quotes($id, $data) {
		$this->db->where(['id' => $id]);
		$this->db->update('quotes', $data);
		return ($this->db->affected_rows() > 0) ? true : false; 
    }
	
	public function get_quote_detail($id) { 		
		$this->db->where('id',$id);

		$query = $this->db->get('quotes'); 
		$result = $query->row(); 
        return $result;
	}

    public function get_all_quotes($search=null, $order=null, $limit=null, $offset=null) {
        $this->db->from('quotes');

        if (isset($search) && $search != "") {
            $this->db->group_start();
            $this->db->like("description", $search);
            $this->db->or_like(array("source" => $search, "category" => $search));
            $this->db->group_end();
        }

        if (!empty($order["by"]) && !empty($order["arrange"])) {
            $this->db->order_by($order["by"], $order["arrange"]);
        }

        if (isset($limit) && isset($offset)) {
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get();
        return $query->result();
    }

    public function get_all_featured_quotes() {
        $this->db->order_by('order_no','ASC');
		$this->db->where('featured','Yes');
		$this->db->from('quotes');
        
 		$query = $this->db->get();
  		return $query->result();
    }

    public function getTotalQuotes() {
        $this->db->from('quotes');
        return $this->db->get()->num_rows();
    }
	 
	public function delete_quote($id) {   	
		$this->db->where('id', $id );
        
		$del = $this->db->delete('quotes');  
        return ($del == '1') ? true : false;
	}

    public function update_featured_quote($id,$status) {
        $data = array( 'featured' => $status );

        $this->db->where('id', $id);
        $result=$this->db->update('quotes', $data); 
        return $result;
    }
}