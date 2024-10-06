<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once __DIR__ . '/Email.php';

class Donation extends CI_Controller {

	public function __construct() {
		parent::__construct();

		$this->load->model('buy_credit_model');
		$this->load->model('charity_model');
		$this->load->library('session');
	}

	public function index() {
		if ($_POST) {
			$user_id = $this->session->userdata('user_id');
        	$user = getprofile($user_id);

			$payment_gross = sanitizeInput($this->input->post('amount'), FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_THOUSAND); 
			if($payment_gross * 1.01 > getBalanceAsFloat() ) {
				echo json_encode(['status' => 'error', 'message' => 'Donation total charges cannot exceed available credits. Please Buy Credits to donate more.']);
				exit;
			}

			$fundraiser_slug = sanitizeInput($this->input->post('fundraiser_slug'), FILTER_VALIDATE_URL);
			$fundraiser = $this->charity_model->getFundraiserIdBySlug($fundraiser_slug);

			$item_number = '';
			$date = date('Y-m-d H:i:s');

			$txn_id = uniqid();
			$currency_code = 'usd';
			$payment_status = "succeeded";
			$is_deductible = $fundraiser->fundraise_type == 'charity' ? 1 : 0;

			//get the exact credit
			$totalcredits = $payment_gross;
			$total_charges = $payment_gross * 1.01;
			$totalsumofcredits = $this->getTotalSumOfCredits($user_id, $total_charges);

			$ref_no = $this->buy_credit_model->getReferenceNumber();
			$method = "WinWinLabs";

			$this->buy_credit_model->insertPayment($user_id, $totalcredits, $item_number, $txn_id, $payment_gross, $total_charges, $currency_code, 2, 0, $payment_status, $date, '$' . round_to_2dc($totalcredits) . ' Donated to ' . $fundraiser->name, $totalsumofcredits, $ref_no, 6, 1, $fundraiser->name);
			if ($this->db->affected_rows() > 0) {
				$donation = [
					'user_id' => $this->session->userdata('user_id'),
					'fundraiser_id' => $fundraiser->id,
					'amount' => $totalcredits,
					'status' => 'completed',
					'date' => $date
				];
				$this->db->insert('donation', $donation);

				if ($this->db->affected_rows() > 0) {
					$emailData = array (
						"username" => $user->username,
						"charity" => $fundraiser->name,
						"amount" => $totalcredits
					);
					$subject = "Thanks for donating!";
        			$view = $this->load->view('emails/donation-email', $emailData, true);
					

					Email::index($user->email, $subject, $view);
				}

				echo json_encode(['status' => 'success', 'message' => 'Thanks for your donation, we appreciate it!']);
			} else {
				echo json_encode(['status' => 'error']);
			}
		}
	}

	private function getTotalSumOfCredits($user_id, $total_charges) {
        $this->db->select('sum(Credits) as credits_cr');
        $total_credit_cr = $this->db->get_where('payments', ['status' => 1, 'User_ID' => $user_id ])->row()->credits_cr;

        $this->db->select('sum(Credits) as credits_db');
        $total_credit_db = $this->db->get_where('payments', ['status' => 2, 'User_ID' => $user_id ])->row()->credits_db;

        $mycredits = $total_credit_cr - $total_credit_db;

        return round($mycredits - $total_charges, 2);
    }
}
