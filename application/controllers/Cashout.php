<?php
defined('BASEPATH') or exit('No direct script access allowed');
require('application/libraries/Payouts-PHP-SDK/payment.php');
require_once('application/libraries/stripe-php-master/init.php');

require_once __DIR__ . '/Email.php';

class Cashout extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        ob_start(); # add this
        $this->load->model('transaction_model');
        $this->load->model('user_model');
        $this->load->model('notification_model');
        $this->load->model('buy_credit_model');
    }

    function paypal_cashout() {
        $amount = $this->input->post("amountWithdrawn");
        $email = $this->input->post("email"); 

        $paypal = new Payment;
        $response = $paypal->payout($email, $amount);

        if ($response->statusCode = 201) {
            $result = $response->result;
        
            $date = date('Y-m-d H:i:s');

            $mycredits = $this->transaction_model->get_sum();

            $payment_status = $result->batch_header->batch_status;

            $cashout_amount = $amount;

            if($payment_status == 'PENDING' || $payment_status == 'FAILED'){
                $amount = 0;
            }

            $totalcredits = $mycredits - $amount;

            $notes = "$".$cashout_amount." withdrawn to Paypal Account ".$email;

            $ref_no = $this->db->select('ref_num')->where('ref_num IS NOT NULL')->order_by('payment_id','desc')->get('payments')->row();
            
            if(isset($ref_no->ref_num) and $ref_no->ref_num > 0){
                $ref_no = $ref_no->ref_num + 1;
            }else{
                $ref_no = 10001;
            }

            $data = array(
                'Credits' => $cashout_amount,
                'User_ID' => $this->session->userdata('user_id'),
                'Date' => $date,
                'Notes' => $notes,
                'Status' => '2',
                'game_id' => '',
                'item_number' => '',
                'txn_id' => $result->batch_header->payout_batch_id,
                'ref_num' => $ref_no,
                'payment_gross' => $cashout_amount,
                'total_charge' => $cashout_amount,
                'currency_code' => 'USD',
                'payment_status' => $payment_status,
                'payment_mode' => '2',
                'total_credits' => $totalcredits,
                'is_cashout' => '1'
            );

            $txnData = $this->transaction_model->insert_payout_data($data, 'paypal');
            $method = 'PayPal';

            if ($txnData) {
                $this->sendNotificationAndEmail($txnData->txnId, $ref_no, $cashout_amount, $totalcredits, 
                        $method, $txnData->status);

                $data = array('status'=>$txnData->Complete, 'id'=> $txnData->txnId, 'userId'=> $txnData->userId) ;
                echo json_encode($data);
            }

        } else {
            $data = array('status'=> false, 'id'=> '', 'userId'=> '') ;
            echo json_encode($data);
        }
    }

    function getBatchIdStatus(){
        $paypal = new Payment;
        $response = $paypal->getBatchIdStatus($this->input->post("batchId"));
        if ($response->statusCode = 201) {
            $result = $response->result;
            
            $data = array('status' => true);

            if(isset($result->items[0]->errors)){
                $data['errorMsg'] = wordwrap(@$result->items[0]->errors->message, 130) ;
            }

            echo json_encode($data);
        } else {
            var_dump($response);
        }
    }

    public function stripeConnectKey() {
        $keys = array(
            'prod' => 'ca_FUrgwoj8A0yjXgIp1iz332Cq7gxm3Sbn',
            'dev' => 'ca_FUrgKm8WjAaSDfWkCm8ikxd7vgvz06vA'
        );

        return $keys;
    }

    public function getStripeConnectUrl() {
        $keys = $this->stripeConnectKey();
        $cliendId = (asset_url() === 'https://winwinlabs.org/') ? $keys['prod'] : $keys['dev'];
        $connectUrl = 'https://connect.stripe.com/oauth/authorize?response_type=code&client_id='.$cliendId.'&scope=read_write';

        // Redirect the user to the generated URL
        header("Location: $connectUrl");
        exit;
    }

    public function connectStripeAccount() {
        // Check if the 'code' parameter exists in the query string
        if (isset($_GET['code'])) {
            // Retrieve the value of the 'code' parameter
            $code = $_GET['code'];

            // Now you can use the $code variable for further processing
            $this->getStripeConnectUserAccount($code);
        } else {
            // If the 'code' parameter is not present in the URL, return null or handle the error as per your application logic
            return null;
        }
    }

    public function getStripeConnectUserAccount($code) {
        $stripe = $this->getStripePaymentKeys();
        \Stripe\Stripe::setApiKey($stripe['secret_key']);

        $response = \Stripe\OAuth::token([
            'grant_type' => 'authorization_code',
            'code' => $code,
        ]);

        // Access the connected account id in the response
        $connected_account_id = $response->stripe_user_id;

        $response = $this->user_model->insert_stripe_connect_account($connected_account_id);

        if($response['status'] === 'success') {
            $data = "Congratulations, your Stripe Account was successfully connected to WinWinLabs";
        }

        // Redirect to the desired view passing along the response message
        $this->session->set_flashdata('message', $data);

        redirect('cashout/stripe');
    }

    public function stripe_cashout() {
        $mycredits = $this->transaction_model->get_sum();
        $amount = $this->input->post('amountWithdrawn');
        $status = "$".$amount." withdrawn to linked Stripe Account";

        $user_connect_stripe_account= $this->db->where('user_id', $this->session->userdata('user_id'))->get('tbl_users')->row();
        $account = $user_connect_stripe_account->payout_stripe_connect_account_id;

        if (!$account) {
            $this->session->set_flashdata('message', 'error');
            redirect('cashout/stripe');
        } else {
            $stripe = $this->getStripePaymentKeys();
            \Stripe\Stripe::setApiKey($stripe['secret_key']);

            $mycredits = $this->transaction_model->get_sum();
            $amount = $this->input->post('amountWithdrawn');
            $status = "$".$amount." withdrawn to linked Stripe Account";

            try {
                $payout = \Stripe\Transfer::create([
                    'amount' => $amount*100,
                    'currency' => 'usd',
                    'destination' => $account,
                    "source_type" => "bank_account"
                ]);    
                // Handle the successful payout
            } catch(\Stripe\Exception\CardException $e) {
                // Since it's a decline, \Stripe\Exception\CardException will be caught
                $data = array('status' => false) ;
                echo json_encode($data);
            } catch (Exception $e) {
                $data = array('status' => false) ;
                echo json_encode($data);
            }

            $transactionId = $payout['balance_transaction'];

            if ($transactionId!="") {
                $date = date('Y-m-d H:i:s');
                $totalcredits=$mycredits-$amount;
                $ref_no = $this->db->select('ref_num')->where('ref_num IS NOT NULL')->order_by('payment_id','desc')->get('payments')->row();
                if (isset($ref_no->ref_num) and $ref_no->ref_num > 0) {
                    $ref_no = $ref_no->ref_num + 1;
                } else {
                    $ref_no = 10001;
                }
        
                $data = array(
                    'Credits' => $amount,
                    'User_ID' => $this->session->userdata('user_id'),
                    'Date' => $date,
                    'Notes' => $status,
                    'Status' => '2',
                    'game_id' => '',
                    'item_number' => '',
                    'txn_id' => $transactionId,
                    'ref_num' => $ref_no,
                    'payment_gross' => $amount,
                    'total_charge' => $amount,
                    'currency_code' => 'USD',
                    'payment_status' => "SUCCESS",
                    'total_credits' => $totalcredits,
                    'payment_mode' => '3',
                    'is_cashout' => '1'
                );

                $insert = $this->transaction_model->insert_payout_data($data, 'stripe');
                $method = 'Stripe';
                
                if ($insert) {
                    $this->sendNotificationAndEmail($transactionId, $ref_no, $amount, $totalcredits, 
                                $method, "SUCCESS");
                    $data = array('status' => true) ;
                    echo json_encode($data);
                }
            }
        }
    }

    public function getEnv() {
        return (asset_url() === 'https://winwinlabs.org/') ? 'prod' : 'dev';
    }

    private function getStripePaymentKeys() {
        $stripePaymentKey = $this->buy_credit_model->getPaymentMethodList('stripe', $this->getEnv())[0];

        $stripe = array(
            'secret_key' => $stripePaymentKey['secret_key'],
            'publishable_key' => $stripePaymentKey['publishable_key'],
        );

        return $stripe;
    }

    public function sendNotificationAndEmail($txn_id, $ref_no, $cashout_amount, $total_charges, $method, $status) {
        $user_id = $this->session->userdata('user_id');
        $user = getprofile($user_id);
        $emailData = array (
            "username" => $user->username,
            "transaction_date" => date('Y-m-d H:i:s'),
            "transaction_id" => $txn_id,
            "wwl_ref_id" => $ref_no,
            "transaction_amount" => $cashout_amount,
            "credit_balance" => $total_charges,
            "transaction_method" => $method,
            "transaction_status" => strtoupper($status)
        );
        $subject = "Your credit withdraw status";
        $view = $this->load->view('emails/withdraw-credits-email', $emailData, true);
        Email::index($user->email, $subject, $view);

        $notes = "Your " .$method. " withdraw request for $" .$cashout_amount. " - " .strtoupper($status);
        $this->notification_model->insertNotification($user_id, $user_id, $notes, 'withdraw', 'transaction', '','');
    }
}
