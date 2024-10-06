<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require('application/libraries/Payouts-PHP-SDK/payment.php');
require('application/libraries/stripe/stripe/lib/Stripe.php');
require('application/libraries/stripe-php-master/init.php');
require_once __DIR__ . '/Email.php';

class Transactions extends CI_Controller {

	public function __construct () {
		parent::__construct();
		check_login();

		$this->load->model('transaction_model');
		$this->load->model('buy_credit_model');
		$this->load->model('charity_model');
		$this->load->model('notification_model');

		$this->load->library('template');
		$this->load->library('session');
		
		$this->template->set_breadcrumb('Home', asset_url());
	}

	public function index() {
		$this->updateCashoutPayPalTransactionStatus();
		
		if(count($this->getPendingStipeTransactions()) > 0) {
			$pendignTxns = $this->getPendingStipeTransactions();
			$this->updateStripeTransactionStatus($pendignTxns);
		}

		if (!empty(getprofile()->user_id) && (getprofile()->usertype!='2')) {
			$user_id = getprofile()->user_id;
			$data['user_id'] = $user_id;

			$total = $this->transaction_model->getTotalTransactions($user_id);
			$data['sum'] = $this->transaction_model->get_sum($user_id);
			$data['transactions'] = $this->transaction_model->get_all_transactions($user_id, null, null, 10, 0);
			$data['loginuserdetails'] = $this->user_model->get_loginuser();

			$successArray = array('SUCCESS', 'Completed', 'Complete', 'succeeded');
			$pendingArray = array('pending', 'PENDING');

			foreach ($data['transactions'] as $key => $txn) { 
				if (in_array($txn['payment_status'], $successArray)) { 
					$data['transactions'][$key]['payment_status'] = 'success';
				}
			}

			foreach ($data['transactions'] as $key => $txn) { 
				if (in_array($txn['payment_status'], $pendingArray)) { 
					$data['transactions'][$key]['payment_status'] = 'pending';
				}
			}

			foreach ($data['transactions'] as $key => $txn) { 
				if ($txn['payment_status'] == 'success') {
					$data['transactions'][$key]['badge_color'] = 'badge-success';
				} elseif ($txn['payment_status'] == 'pending') {
					$data['transactions'][$key]['badge_color'] = 'badge-warning';
				} elseif ($txn['payment_status'] == 'failed') {
					$data['transactions'][$key]['badge_color'] = 'badge-danger';
				}
			}
		}
		$data['total_credits'] = $this->transaction_model->get_sum($this->session->userdata('user_id'));
        $data['withdrawable_credits'] = $this->transaction_model->get_withdrawable_sum($this->session->userdata('user_id'));
		$data['donated_credits'] = $this->transaction_model->get_donated_sum($this->session->userdata('user_id'));

		$data['deferLoading'] = array("filtered" => $total, "total" => $total);

		$this->template->set_breadcrumb('Transactions', asset_url('transactions'));
		$this->template->set_layout(DEFAULT_LAYOUT)->build('transactions/index', $data);
	}

	public function getStripePaymentKeys() {
        $stripePaymentKey = $this->buy_credit_model->getPaymentMethodList('stripe', $this->getEnv())[0];

        $stripe = array(
            'secret_key' => $stripePaymentKey['secret_key'],
            'publishable_key' => $stripePaymentKey['publishable_key'],
        );

        return $stripe;
    }

	
    public function getEnv() {
        return (asset_url() === 'https://winwinlabs.org/') ? 'prod' : 'dev';
    }

	public function getPendingStipeTransactions() {
		$this->db->select('*');
		$this->db->where('is_cashout', null);
		$this->db->where('payment_mode', 3);
		$this->db->where('is_processed', 0); // 0-pending, 1-succeeded, 2-failed
		$this->db->where_not_in('payment_status', ['SUCCESS', 'Completed', 'Complete', 'succeeded', 'failed']);
		$this->db->where('User_ID', $this->session->userdata('user_id'));
		$this->db->from('payments'); 
		return $this->db->get()->result();
	}

	public function updateStripeTransactionStatus($pendignTxns) {
		foreach ($pendignTxns as $txn) {
			$stripe = $this->getStripePaymentKeys();
			\Stripe\Stripe::setApiKey($stripe['secret_key']);

			// Retrieve the charge object
			$getChargeId = \Stripe\BalanceTransaction::retrieve($txn->txn_id)->source;

			// Get the status of the charge
			$chargeStatus = \Stripe\Charge::retrieve($getChargeId)->status;

			if($chargeStatus == 'succeeded') {
				$user_id = $this->session->userdata('user_id');
				$user =  getprofile($user_id);
				$payment_gross = $txn->payment_gross;
				$total_charges = $txn->total_charge;
				$item_number = '';
				$txn_id = $txn->txn_id;
				$method =  $txn->Notes;
				$currency_code = $txn->currency_code;
				$totalcredits = $txn->payment_gross;

				if($chargeStatus  == 'pending' || $chargeStatus  == 'failed'){
					$creditToAdd = 0;
				} else {
					$creditToAdd = $totalcredits; 
				}

				$totalsumofcredits = $this->getTotalSumOfCredits($user_id, $creditToAdd, 1);
				
				$ref_no = $this->buy_credit_model->getReferenceNumber();
				$date = date('Y-m-d H:i:s');

				$this->buy_credit_model->insertPayment($user_id, $totalcredits, $item_number, $txn_id, $payment_gross, 
					$total_charges, $currency_code, 1, 0, $chargeStatus, $date, $method, $totalsumofcredits, $ref_no, 3);

				$this->db->where('payment_id', $txn->payment_id);
				$this->db->update('payments', array('is_processed' => 1));

				$this->sendReceiptForStripeStatusUpdate($user, $date, $ref_no, $payment_gross, $total_charges, $method, 'Stripe', $chargeStatus);
			}
		}
	}

	public function getTotalSumOfCredits($user_id, $totalcredits, $method) {
        $this->db->select('sum(Credits) as credits_cr');
		$this->db->where_not_in('payment_status', array('pending', 'failed', 'PENDING'));
        $total_credit_cr = $this->db->get_where('payments', ['status' => 1, 'User_ID' => $user_id ])->row()->credits_cr;

        $this->db->select('sum(Credits) as credits_db');
		$this->db->where_not_in('payment_status', array('pending', 'failed', 'PENDING'));
        $total_credit_db = $this->db->get_where('payments', ['status' => 2, 'User_ID' => $user_id ])->row()->credits_db;

        $mycredits = $total_credit_cr - $total_credit_db;

		// 1 - method Stripe , 2 - method PayPal
		if ($method == 1) {
            $sumOfCredit = round($mycredits + $totalcredits, 2);
        } else {
            $sumOfCredit = round($mycredits - $totalcredits, 2);
        }
		
        return $sumOfCredit;
    }

	public function updateCashoutPayPalTransactionStatus() {
		$this->db->select('*');
		$this->db->where('is_cashout', 1);
		$this->db->where('payment_mode', 2);
		$this->db->where('is_processed', 0); // 0-pending, 1-succeeded, 2-failed
		$this->db->where_not_in('payment_status', ['SUCCESS', 'Completed', 'Complete', 'succeeded', 'failed']);
		$this->db->where('User_ID', $this->session->userdata('user_id'));

		$this->db->from('payments'); 
		$txnArray = $this->db->get()->result();

		$paypal = new Payment;
		foreach ($txnArray as $txn) {
			$response = $paypal->getBatchIdStatus($txn->txn_id);
			$result = $response->result;

			if ($response->statusCode == 200 && @$response->result->batch_header->batch_status === 'SUCCESS') {

				$user_id = $this->session->userdata('user_id');
				$payment_gross = $txn->payment_gross;
				$total_charges = $txn->total_charge;
				$item_number = '';
				$txn_id = $txn->txn_id;
				$method =  $txn->Notes;
				$currency_code = $txn->currency_code;
				$totalcredits = $txn->payment_gross;
				$payment_status = $result->batch_header->batch_status;

				if($payment_status  == 'PENDING' || $payment_status == 'FAILED'){
					$creditToAdd = 0;
				} else {
					$creditToAdd = $totalcredits; 
				}

				$totalsumofcredits = $this->getTotalSumOfCredits($user_id, $creditToAdd, 2);
				$ref_no = $this->buy_credit_model->getReferenceNumber();
				$date = date('Y-m-d H:i:s');

				$this->buy_credit_model->insertPayment($user_id, $totalcredits, $item_number, $txn_id, $payment_gross, 
					$total_charges, $currency_code, 2, 0, $payment_status, $date, $method, $totalsumofcredits, $ref_no, 2);

				$this->db->where('payment_id', $txn->payment_id);
				$this->db->update('payments', array('is_processed' => 1));
	
				$this->sendNotificationAndEmail($txn->txn_id, $txn->ref_num, $total_charges, 
					$totalsumofcredits, 'PayPal', $payment_status);
			} else {
				// we need to come up with some data to show user
			}
		}
	}

	public function getTransactions() {
		$user_id = (int)$this->session->userdata("user_id");

		$draw = sanitizeInput($this->input->get("draw"), FILTER_VALIDATE_INT);

        $limit = sanitizeInput($this->input->get("length"), FILTER_VALIDATE_INT);
		$offset = sanitizeInput($this->input->get("start"), FILTER_VALIDATE_INT);
		$search = sanitizeInput($this->input->get("search[value]", true), FILTER_SANITIZE_STRING);

		$by = sanitizeInput($this->input->get("order[0][column]"), FILTER_VALIDATE_INT);
		$order = array("by" => sanitizeInput($this->input->get("columns[" . $by . "][data]"), FILTER_SANITIZE_STRING), "arrange" => sanitizeInput($this->input->get("order[0][dir]"), FILTER_SANITIZE_STRING));
		
		$total = $this->transaction_model->getTotalTransactions($user_id);
		$transactions = $this->transaction_model->get_all_transactions($user_id, $search, $order);

		$successArray = array('SUCCESS', 'Completed', 'Complete', 'succeeded');
		$pendingArray = array('pending', 'PENDING');

		foreach ($transactions as $key => $txn) { 
			if (in_array($txn['payment_status'], $successArray)) { 
				$transactions[$key]['payment_status'] = 'success';
			}
		}

		foreach ($transactions as $key => $txn) { 
			if (in_array($txn['payment_status'], $pendingArray)) { 
				$transactions[$key]['payment_status'] = 'pending';
			}
		}

		foreach ($transactions as $key => $txn) { 
			if ($txn['payment_status'] == 'success') {
				$transactions[$key]['badge_color'] = 'badge-success';
			} elseif ($txn['payment_status'] == 'pending') {
				$transactions[$key]['badge_color'] = 'badge-warning';
			} elseif ($txn['payment_status'] == 'failed') {
				$transactions[$key]['badge_color'] = 'badge-danger';
			}
		}
		
		$data = array(
			"draw" => $draw,
			"recordsTotal" => $total,
			"recordsFiltered" => count($transactions),
			"data" => array_slice($transactions, $offset, $limit)
		);

		echo json_encode($data);
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

	private function sendReceiptForStripeStatusUpdate($user, $date, $ref_no, $payment_gross, $total_charges, $method, $vendor, $status='') {
        $emailData = array (
            "username" => $user->username,
            "transaction_date" => $date,
            "transaction_id" => $ref_no,
            "transaction_subtotal" => $payment_gross,
            "transaction_tax" => $total_charges - $payment_gross,
            "transaction_total" => $total_charges,
            "transaction_method" => $method,
            "transaction_vendor" => $vendor,
			"transaction_status" => $status
        );
        $subject = "Your WinWinLabs receipt";
        $view = $this->load->view('emails/receipt-email', $emailData, true);

        Email::index($user->email, $subject, $view);

		$notes = "Your " .$method. " for $" .$payment_gross. " - " .strtoupper($status);
		$user_id = $user->user_id;

		$this->notification_model->insertNotification($user_id, $user_id, $notes, '', 'transaction', '','');
    }	
}
