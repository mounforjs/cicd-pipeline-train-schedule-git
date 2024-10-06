<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Buy_credit_model extends CI_Model {

    public function saved_card_list($user_id = '') {
        $stripe_details = "select * from user_stripe_details where payment_type = 'card' and user_id='".$user_id."'";
        $savedcardlist = $this->db->query($stripe_details)->result_array();

        return 	$savedcardlist;
    }

    public function saved_bank_list($user_id = '') {
        $stripe_details = "select * from user_stripe_details where payment_type = 'bank' and user_id='".$user_id."'";
        $savedbanklist = $this->db->query($stripe_details)->result_array();
            
        return  $savedbanklist;
    }

    public function delete_card($id) {      
        $this->db->where('id', $id );
        $del = $this->db->delete('user_stripe_details');  

        if ($del == '1') {
            return true;
        } else {
            return false;
        }
    }

    public function getPaymentMethodList($methodName, $server) {
        $prodKeyActiveOnDevdbQuery = "select activeStatus from payment_method_data where name='".$methodName."' and server ='".$server."'";
        $prodKeyActiveOnDev =  $this->db->query($prodKeyActiveOnDevdbQuery)->row()->activeStatus;

        if ($server == 'dev') {
            $dbServer = ($prodKeyActiveOnDev == '0') ? 'prod' : 'dev';
        }

        elseif ($server === 'prod') {
            $dbServer = ($prodKeyActiveOnDev == '0') ? 'dev' : 'prod';
        }

        $payment_details = "select * from payment_method_data where name='".$methodName."' and server ='".$dbServer."'";

        return $this->db->query($payment_details)->result_array();
    }

    public function getAllPaymentMethods($method, $dbServer) {
        $q =  "select * from payment_method_data where name='".$method."' and server ='".$dbServer."'";
        $keyList =  $this->db->query($q)->result_array();

        return $keyList;
    }

    public function updatePaymentMethodKey($keyVal, $keyId) {
        $pValArr = ["pDevId", "pDevUrl", "pProdId", "pProdUrl"]; 
        $sValArr =  ["sDevSectKey", "pDevPubsKey", "sProdSectKey", "sProdPubsKey"]; 

        if (in_array($keyId, $pValArr)) {
            $method = 'paypal';
        }

        if (in_array($keyId, $sValArr)) {
            $method = 'stripe';
        }

        switch ($keyId) {
            case "pDevId":
                $valueToBeUpdated = 'method_id';
                $server = 'dev';
                break;
            case "pDevUrl":
                $valueToBeUpdated = 'method_url';
                $server = 'dev';
                break;
            case "pProdId":
                $valueToBeUpdated = 'method_id';
                $server = 'prod';
                break;
            case "pProdUrl":
                $valueToBeUpdated = 'method_url';
                $server = 'prod';
                break;
            case "sDevSectKey":
                $valueToBeUpdated = 'secret_key';
                $server = 'dev';
                break;
            case "sDevPubsKey":
                $valueToBeUpdated = 'publishable_key';
                $server = 'dev';
                break;
            case "sProdSectKey":
                $valueToBeUpdated = 'secret_key';
                $server = 'prod';
                break;
            case "sProdPubsKey":
                $valueToBeUpdated = 'publishable_key';
                $server = 'prod';
                break;
        }

        $data = array($valueToBeUpdated => $keyVal);  

        $this->db->where('server',$server);
        $this->db->where('name',$method);
        $del = $this->db->update('payment_method_data',$data);  
        if ($del == '1') {
            return true;
        } else {
            return false;
        }
    }

    public function switchPaymentMethodKey($keyValSwitch, $server, $method) {

        if ($server == 'dev' && $keyValSwitch == 'p') {
            $keyVal = '0';
        } elseif ($server == 'dev' && $keyValSwitch == 'd') {
            $keyVal = '1';
        }

        if ($server== 'prod' && $keyValSwitch == 'd') {
            $keyVal = '0';
        } elseif ($server == 'prod' && $keyValSwitch == 'p') {
            $keyVal = '1';
        }

        $data = array('activeStatus' => $keyVal);  

        $this->db->where('server', $server);
        $this->db->where('name', $method);
        $del = $this->db->update('payment_method_data',$data);  
        if ($del == '1') {
            return true;
        } else {
            return false;
        }
    }

    public function getReferenceNumber() {
        $this->db->select('ref_num');
        $this->db->from('payments');
        $this->db->where('ref_num IS NOT NULL');
        $this->db->order_by('payment_id', 'desc');
        $result = $this->db->get()->row();

        if (isset($result->ref_num) and $result->ref_num > 0) {
            return $result->ref_num + 1;
        } else {
            return 10001;
        }
    }

    public function insertPayment($user_id, $totalcredits, $item_number, $txn_id, $payment_gross, $total_charges, $currency_code, $status, $game_id, $payment_status, $date, $notes, $totalsumofcredits, $ref_no, $mode, $is_deductible=null, $donated_to_fundraiser=null, $is_referral=null) {
        $data = array(
            "Credits" => $totalcredits,
            "User_ID" => $user_id,
            "item_number" => $item_number,
            "txn_id" => $txn_id,
            "payment_gross" => $payment_gross,
            "total_charge" => $total_charges,
            "currency_code" => $currency_code,
            "payment_status" => $payment_status,
            "Date" => $date,
            "Notes" => $notes,
            "Status" => $status,
            "game_id" => $game_id,
            "total_credits" => $totalsumofcredits,
            "ref_num" => $ref_no,
            "payment_mode" => $mode,
            "is_deductible" => $is_deductible,
            "donated_to_fundraiser_name" => $donated_to_fundraiser,
            "is_referral" => $is_referral
        );

        $insert = $this->db->insert('payments', $data);
        
        return $insert;
    }

    public function updateDefaultPaymentMethod($user_id, $method) {
        $this->db->where(array('user_id' => $user_id, 'payment_type' => $method));
        $this->db->set("default_payment_method", 0);
        $this->db->update('user_stripe_details');
    }

    public function insertStripeDetails($user_id, $payment_method, $zip, $payment_method_type, $default_payment, $save) {
        $data = array(
            'user_id' => $user_id ,
            'payment_method_id' => $payment_method->id,
            'card_last_four' => ($payment_method->object == 'card') ? $payment_method->last4 : null,
            'card_brand' => $payment_method->brand,
            'card_fingerprint' => (isset($payment_method->fingerprint)) ? $payment_method->fingerprint : null,
            'bank_brand' => $payment_method->bank_name,
            'bank_last_four' => ($payment_method->object == 'bank_account') ? $payment_method->last4 : null,
            'payment_type' => $payment_method_type,
            'payment_user_name' => '',
            'payment_zipcode' => $zip,
            'default_payment_method' => $default_payment,
            'save_payment_method' => $save
        );
        return $this->db->insert('user_stripe_details', $data);
    }

    public function setCustomerID($user_id, $customer_id) {
        $this->db->where('user_id', $user_id);
        $this->db->set('customer_id', $customer_id);
        $this->db->update('tbl_users');
    }

    public function getStripeDetails($id) {
        $this->db->where('id', $id);
        $this->db->from('user_stripe_details');

        return $this->db->get()->row();
    }

    public function stripeDetailsBankVerification($id, $status) {
        $this->db->where('id', $id);
        $this->db->set('bank_verification_status', $status);

        return $this->db->update('user_stripe_details');
    }

    public function updateUserDefaultPaymentMethod($user_id, $pType) {
		switch ($pType) {
			case "bankRadio":
				$paymentMethodType = 1;
				break;
			case "creditRadio":
				$paymentMethodType = 2;
				break;
			case "paypalRadio":
				$paymentMethodType = 3;
				break;
		}
		$data = array('default_payment_method' => $paymentMethodType);  

		$this->db->where('user_id', $user_id);
		return $this->db->update('tbl_users', $data);
	}

    public function set_default_card($id, $ptype){   
        $data = array('default_payment_method' => 0);
        $this->db->where('user_id', $this->session->userdata('user_id'));
        $this->db->where('payment_type', $ptype);
        $del = $this->db->update('user_stripe_details',$data);

        $data = array('default_payment_method' => 1);  
        $this->db->where('id', $id);
        $del = $this->db->update('user_stripe_details',$data);  

        if($del == '1') {
            return true;
        } else {
            return false;
        }
    }

    public function getTotalSumOfCredits($user_id, $totalcredits) {
        $this->db->select('sum(Credits) as credits_cr');
        $total_credit_cr = $this->db->get_where('payments', ['status' => 1, 'User_ID' => $user_id ])->row()->credits_cr;

        $this->db->select('sum(Credits) as credits_db');
        $total_credit_db = $this->db->get_where('payments', ['status' => 2, 'User_ID' => $user_id ])->row()->credits_db;

        $mycredits = $total_credit_cr - $total_credit_db;

        return round($totalcredits + $mycredits, 2);
    }
}