<?php


class Coupon_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_coupon($id) {
        $this->db->select('description, amount');
        $this->db->where('id', $id);
        $this->db->from('tbl_coupons');
        
        return $this->db->get()->row();
    }

    public function get_coupons() {
        $this->db->select('*');
        $this->db->where('active', 'Yes');
        $this->db->from('tbl_coupons');

        $query = $this->db->get();
        return $query->result();
    }

    public function get_all_coupons($search=null, $order=null, $limit=null, $offset=null) {
        $this->db->from('tbl_coupons');

        if (isset($search) && $search != "") {
            $this->db->group_start();
            $this->db->like("description", $search);
            $this->db->or_like("amount", $search);
            $this->db->group_end();
        }

        if (!empty($order["by"]) && !empty($order["arrange"])) {
            $this->db->order_by($order["by"], $order["arrange"]);
        }

        if (isset($limit) && isset($offset)) {
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get()->result();

        return $query;
    }

    public function getTotalCoupons() {
        $this->db->from('tbl_coupons');

        return $this->db->get()->num_rows();
    }

    public function update_coupon_content($id,$content,$name) {
        switch ($name) {
            case "description":
                $data = array( 'description' => $content );
                break;
            case "amount":
                 $data = array( 'amount' => $content );
                break;

        }

        $this->db->where('id', (int)$id);
        $result=$this->db->update('tbl_coupons', $data);
        return $result;
    }

    public function add_coupon($text,$type) {
        $data = array( 'description' => $text, 'amount' => $type, 'active' => 'No');

        $this->db->insert('tbl_coupons', $data);
    }

    public function give_user_coupon($id, $coupon) {
        $this->db->select('description, amount');
        $this->db->where('id', $coupon);
        $this->db->from('tbl_coupons');
        $query = $this->db->get();
        $data =  $query->result_array();

        $this->db->select('sum(Credits) as credits_cr');
        $total_credit_cr = $this->db->get_where('payments', ['status' => 1, 'User_ID' => $id])->row()->credits_cr;

        $this->db->select('sum(Credits) as credits_cr');
        $total_credit_db = $this->db->get_where('payments', ['status' => 2, 'User_ID' => $id])->row()->credits_cr;

        $mycredits = $total_credit_cr - $total_credit_db;
        $total_charges = $data[0]['amount'];
        $description = $data[0]['description'];
        $totalsumofcredits = round( $total_charges + $mycredits, 2);

        $ref_no = $this->db->select('ref_num')->order_by('payment_id','desc')->get('payments')->row();
        if (isset($ref_no->ref_num) and $ref_no->ref_num > 0) {
            $ref_no = $ref_no->ref_num;
        } else {
            $ref_no = 10000;
        }

        $insert = array(
            'game_id' => '',
            'User_ID' => $id,
            'txn_id' => sha1(time()),
            'ref_num' => ($ref_no + 1),
            'Credits' => $total_charges,
            'currency_code' => 'USD',
            'Date' => date('Y-m-d H:i:s'),
            'payment_status' => 'Complete',
            'payment_gross' =>$total_charges,
            'Status' => 1,
            'total_credits' => $totalsumofcredits,
            'payment_mode' => 5,
            'is_paid'=>3,
            'Notes' => 'You have been awarded $'.  $total_charges. ' from WinWinLabs for ' . $description,
            'user_type' =>NULL, //1 creator, 2 creator fundraise, 3 winner , 4 winner fundraise, 5 sub winner , 6 goedu
        );

        $result =  $this->db->insert('payments', $insert);
        return $result;
    }

    public function delete_coupon_content($id) {
        $this->db->where('id', $id );
        $del = $this->db->delete('tbl_coupons');
        if ($del == '1') {
            return true;
        } else {
            return false;
        }
    }

    public function update_coupon_status($id,$status) {
        $data = array( 'active' => $status );

        $this->db->where('id', $id);
        $result=$this->db->update('tbl_coupons', $data);
        return $result;
    }
}
