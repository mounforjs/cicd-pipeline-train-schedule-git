<?php
class Referral_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_referrals() {
        $this->db->select('*');
        $this->db->from('tbl_referral');

        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_referral_data_for_transaction($id) {
        $this->db->select('id, referrer_id, value, referrer_value, cap_number, redemption_total');
        $this->db->where('id',$id);
        $this->db->from('tbl_referral');

        $query = $this->db->get();
        return $query->result_array();
    }

    function save_referral(){
        sanitizeInput($_POST, FILTER_SANITIZE_STRING);
        
        $data = "";
        $id = $_POST['id'];

        $_POST['referrer_id'] = $_POST['referrer_name'];
        unset($_POST['referrer_name']);

        $formatedTime = explode('to', $_POST['referralDateTimeRange']);
        $_POST['start_date'] = $formatedTime[0];
        $_POST['end_date'] = $formatedTime[1];

        unset($_POST['referralDateTimeRange']);

        foreach($_POST as $k => $v){
            // excluding id 
            if(!in_array($k,array('id'))){
                // add comma if data variable is not empty
                if(!empty($data)) $data .= ", ";
                $data .= " `{$k}` = '{$v}' ";
            }
        }

        if(empty($id)){
            // Insert New Referral
            $sql = "INSERT INTO tbl_referral set {$data}";
        }else{
            // Update Referral Details            
            $sql = "UPDATE tbl_referral set {$data} where id = {$id}";
        }
        $save = $this->db->query($sql);
        if($save && !$this->db->error){
            $resp['status'] = 'success';
            if(empty($id))
                $resp['msg'] = 'New Referral successfully added';
            else
            $resp['msg'] = 'Referral\'s Details successfully updated';

        }else{
            $resp['status'] = 'failed';
            $resp['msg'] = 'There\'s an error occured while saving the data';
            $resp['error'] = $this->db->error;
        }
        return json_encode($resp);
    }

    function delete_referral(){
        $id = sanitizeInput($_POST['id'], FILTER_SANITIZE_STRING);
        $delete = $this->db->query("DELETE FROM `tbl_referral` where id = {$id}");
        if($delete){
            $resp['status'] = 'success';
        }else{
            $resp['status'] = 'failed';
            $resp['msg'] = 'There\'s an error occured while deleting the data';
            $resp['error'] = $this->db->error;
        }
        return json_encode($resp);
    }

    public function checkIfReferralExists($referral) {
		$query = $this->db->where('name', $referral)->get('tbl_referral');
        $checkData = $query->row();

		if ($query->num_rows() > 0) {
            $redemptionAmount = $checkData->redemption_total;
            $capAmount = $checkData->cap_number;
            $activeStatus = $checkData->status;

            $dateNow = new DateTime("now");
            $dateEnd = new DateTime($checkData->end_date);

            if(($redemptionAmount < $capAmount) && ($activeStatus == 1) && ($dateNow < $dateEnd)) {
			    return true;
            } else {
                return false;
            }
		} else {
			return false;
		}
	}

    public function getReferralIdForUserTransaction($referral) {
        $query = $this->db->select('id')->where('name', $referral)->get('tbl_referral')->row()->id;
        return $query;
    }

    public function updateReferralRedemptionCount($id) {
        $query = $this->db->select('redemption_total')->where('id', $id)->get('tbl_referral')->row()->redemption_total + 1;
        $data = array('redemption_total' => $query);
        $this->db->where('id', $id);
        $this->db->update('tbl_referral', $data);
    }

    public function checkReferralNameDuplicate($referral) {
        $this->db->where('name', $referral);
        $query = $this->db->get('tbl_referral');
        if ($query->num_rows() > 0){
            return true;
        } else {
            return false;
        }
    }
}
