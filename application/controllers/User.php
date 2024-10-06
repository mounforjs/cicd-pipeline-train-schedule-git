<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once __DIR__ . '/Email.php';

class User extends CI_Controller {
	/**
	 * User constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->load->model('user_model');
		$this->load->model('home_model');
		$this->load->model('emails_model');
		$this->load->model('referral_model');
		$this->load->model('buy_credit_model');

		$this->load->helper('cookie');

		$this->load->library('template');
		$this->load->helper('security');
	}

	public function sessions() {
		$sId = $this->session->userdata('user_id');
		if (isset($sId)) {
			echo true;
		}
	}

	public function register_insert_check() {
		$this->form_validation->set_rules('firstname', 'First Name', 'trim|required');
		$this->form_validation->set_rules('lastname', 'Last Name', 'trim|required');
		$this->form_validation->set_rules('username', 'User Name', 'trim|required');
		$this->form_validation->set_rules('password', 'Password', 'trim|required');
		$this->form_validation->set_rules('unEmail', 'Email Address', 'trim|required');
		$this->form_validation->set_rules('country', 'Country', 'trim|required');

		$response = array();
		if ($this->form_validation->run() == FALSE) {
			echo json_encode(validation_errors());
		} else {
            $firstname = sanitizeInput($this->input->post('firstname', true), FILTER_SANITIZE_STRING);
            $lastname = sanitizeInput($this->input->post('lastname', true), FILTER_SANITIZE_STRING);
            $email = sanitizeInput($this->input->post('unEmail', true), FILTER_VALIDATE_EMAIL);

			$query = $this->db->get_where('tbl_users', ['email' => $email]);
			if ($query->num_rows() == 0) {
				$user_id = $this->user_model->insert_user();
				if ($user_id) {
					$user = getprofile($user_id);

					// send email to new user ----------------------
					$data['page_description'] = $this->emails_model->get_content();
					$body = $this->load->view('emails/welcome-email', $data, TRUE);
					$subject = $data['page_description'][1]->email_subject;
					Email::index($user->email, $subject, $body);
					// ----------------------------------------------

					// send email to admin about new user ----------------------
					$data['user'] = $user;
					$body = $this->load->view('emails/new-user-admin-email', $data, TRUE);
					$email = 'new-user@winwinlabs.org';
					$subject = 'New User Registration Info';
					Email::index($email, $subject, $body);
					// ----------------------------------------------

					unset($_SESSION["login_attempt"]);
					$this->session->unset_tempdata("login_penalty");

					// create new session and store in db
					$set_session = $this->home_model->set_user_session($user_id);
					$response = array("status" => $set_session["status"]);

					if ($set_session["status"] == "success") {
						$this->start_user_session($user_id, $set_session["session_id"]);
						$this->set_redirect($response);
					} else {
						$response["msg"] = $result['errors'];
					}

					$_SESSION["nu"]= "1";

					$isReferral = isReferral();
					if(isset($isReferral)) {
						$response["isReferred"] = $isReferral;
						$referrralData = $this->referral_model->get_referral_data_for_transaction(getReferralForUserTransaction());
						$paymentInsertedStatusReferree = $this->insertTransactionForReferralRedemption($user_id, number_format($referrralData[0]['value'],2), 1);
						
						if($referrralData[0]['referrer_value'] > 0) {
							$paymentInsertedStatusReferrer = $this->insertTransactionForReferralRedemption($referrralData[0]['referrer_id'], 
								number_format($referrralData[0]['referrer_value'],2));
						}

						if($referrralData[0]['redemption_total'] <  $referrralData[0]['cap_number']) {
							$this->referral_model->updateReferralRedemptionCount($referrralData[0]['id']);
						}
					}

					if (isset($paymentInsertedStatusReferree) && $paymentInsertedStatusReferree) {
						$response["referralRedeemed"] = true;
						$response["referralValue"] = '$'.number_format($referrralData[0]['value'],2);
					}
				}
			} else {
				$response = array("status" => "failed", "msg" => "Unable to use this email.");
			}

			echo json_encode($response);
		}
	}

	public function insertTransactionForReferralRedemption($id, $referralValue, $refUserType='') {
		$payment_gross =  round($referralValue,2);
        $currency_code = "usd";
        $payment_status = "Completed";
		$item_number = '';
        $date = date('Y-m-d H:i:s');
        $totalcredits = $payment_gross;
        $total_charges = round($referralValue, 2);
        $totalsumofcredits = $this->buy_credit_model->getTotalSumOfCredits($id, $totalcredits);
        $ref_no = $this->buy_credit_model->getReferenceNumber();
        $txn_id = '';
        $method = '';

		return $this->buy_credit_model->insertPayment(
			$id, 
			$totalcredits, 
			$item_number, 
			$txn_id, 
			$payment_gross, 
			$total_charges, 
			$currency_code,
			1, 
			0, 
			$payment_status, 
			$date, 
			($refUserType == 1) ? "Congratulations, you have been awarded $" .$totalcredits. " referral credits." :
				"Congratulations, you earned $" .$totalcredits. " for referring a new user.",
			$totalsumofcredits, 
			$ref_no, 
			5,
			'',
			'',
			1
		);
	}

	public function check_username() {
		$query = $this->db->get_where('tbl_users', ['username' => sanitizeInput($this->input->post('username', true), FILTER_SANITIZE_STRING)]);
		if ($query->num_rows() > 0 && $query->row()->user_id != $this->session->userdata("user_id")) {
			echo json_encode("Username already exists");
		} else {
			echo json_encode("true");
		}
	}

	public function checkteam() {
		$team_number = 'frc' . trim(sanitizeInput($this->input->get('team'), FILTER_SANITIZE_STRING), '+ ');
		$result = $this->user_model->api_blue_alliance($team_number);
		if (empty($result) || !isset($result->nickname)) {
			echo "error";
		} else {
			echo $result->nickname;
		}
	}
	
	public function reset() {
		check_logout();
		$this->form_validation->set_rules('resetEmail', 'Email Address', 'trim|required');
		$data = '';
		if ($this->form_validation->run() !== FALSE) {
			$this->user_model->reset_user();
		}

		$this->load->home_template('reset_user');
	}

	public function reset_password() {
		$email = sanitizeInput($this->input->post('email', true), FILTER_SANITIZE_STRING);
		$result = $this->db->get_where('tbl_users', ['email' => $email]);

		if ($result->num_rows() > 0) {
			$user_id = $result->row()->user_id;
			$email = $result->row()->email;

			$upass = rand();
			$key = $this->my_simple_crypt($user_id, 'e');
			$token = bin2hex(openssl_random_pseudo_bytes(64));
			$this->session->set_userdata('token', $token);

			if($token) {
				$this->db->where('email', $email);
				$this->db->update('tbl_users', ['token' => $token]);
			}

			$data = array("token" => $token, "upass" => $upass, "key" => $key);
			$subject = 'Password Reset Request';
			$body = $this->load->view('emails/password-reset', $data, true);

			Email::index($email, $subject, $body);
		}
	}

	public function reset_verify() {
		check_logout();
		$token = sanitizeInput($this->input->get('token'), FILTER_SANITIZE_STRING);
		$upass = sanitizeInput($this->input->get('key'), FILTER_SANITIZE_STRING);
		$user_id = sanitizeInput($this->input->get('sess'), FILTER_VALIDATE_INT); //double check this is int

		$data['token'] = $token;
		$data['upass'] = $upass;
		$data['user_id'] = $user_id;

		if (!$token || !$upass || !$user_id) {
			redirect('login', 'refresh');
		} else {

			$this->template->set_layout(DEFAULT_LAYOUT)->build('home/verify', $data);
		}
	}

	public function reset_api() {
		$val = array(); parse_str($this->input->post('pDa'), $val);

		$id = $this->my_simple_crypt(sanitizeInput(xss_clean($val['id']), FILTER_SANITIZE_STRING), 'd');
		$existingToken = $this->db->get_where('tbl_users', ['user_id' => $id])->row()->token;
		if ($existingToken === $val['token']) {
			$pswd = sanitizeInput(xss_clean($val['pswd']), FILTER_SANITIZE_STRING);
			$pswrdVerify = sanitizeInput(xss_clean($val['pswrdVerify']), FILTER_SANITIZE_STRING);

			if ($pswd === $pswrdVerify) {
				$hashed_password = password_hash($pswd, PASSWORD_DEFAULT);
				$this->db->where('user_id', $id);
				$this->db->update('tbl_users', ['password' => $hashed_password]);
				echo '1';
			} else {
				echo '3';
			}
		} else {
			echo "4";
			$this->session->sess_destroy('userdata');
		}
	}

	public function login_check() {
		$email = sanitizeInput($this->input->post('email', true), FILTER_VALIDATE_EMAIL);
		$password = sanitizeInput($this->input->post('password', true), FILTER_SANITIZE_STRING);

		if ($this->session->userdata("user_id") == null) {
			$result = $this->home_model->login($email, $password);

			if (isset($result['errors'])) {
				echo json_encode(["status" => "failed", "msg" => $result['errors']]);
			} else {
				echo json_encode(["status" => "success"]);
			}
		} else {
			echo json_encode(["status" => "failed", "msg" => "Already logged in."]);
		}
	}

	private function start_user_session($user_id, $session_id) {
		$this->home_model->track($user_id);
	
		$session_data = array('user_id' => $user_id, "session_id" => $session_id);
		$this->session->set_userdata($session_data);
	}

	private function set_redirect(&$response) {
		if (isset($_COOKIE["redirect_to"])) {
			$response["redirect"] = $_COOKIE["redirect_to"];
			delete_cookie("redirect_to");
			unset($_COOKIE['redirect_to']);
		} else {
			$response["redirect"] = "/";
		}
	}
	public function edit_profile() {
		check_login();
		$data = [
			"firstname" => sanitizeInput($this->input->post("firstname", true), FILTER_SANITIZE_STRING),
			"lastname" => sanitizeInput($this->input->post("lastname", true), FILTER_SANITIZE_STRING),
			"country" => sanitizeInput($this->input->post("country", true), FILTER_SANITIZE_STRING),
			"username" => sanitizeInput($this->input->post("username", true), FILTER_SANITIZE_STRING),
			"updateEmail" => sanitizeInput($this->input->post("updateEmail", true), FILTER_SANITIZE_STRING),
			"password" => sanitizeInput($this->input->post("password", true), FILTER_SANITIZE_STRING),
			"pathway" => sanitizeInput($this->input->post("pathway", true), FILTER_SANITIZE_STRING),
			"interests" => sanitizeInput($this->input->post("interests", true), FILTER_SANITIZE_STRING),
			"strengths" => sanitizeInput($this->input->post("strengths", true), FILTER_SANITIZE_STRING),
			"learn_areas" => sanitizeInput($this->input->post("learn_areas", true), FILTER_SANITIZE_STRING),
			"graduation" => sanitizeInput($this->input->post("graduation", true), FILTER_VALIDATE_INT),
			"internship" => sanitizeInput($this->input->post("internship"), FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
			"intern_time" => sanitizeInput($this->input->post("intern_time", true), FILTER_SANITIZE_STRING),
			"is_first" => sanitizeInput($this->input->post("is_first"), FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
			"challenge" => sanitizeInput($this->input->post("challenge", true), FILTER_SANITIZE_STRING),
			"teamname" => sanitizeInput($this->input->post("teamname", true), FILTER_SANITIZE_STRING),
			"teamnameftc" => sanitizeInput($this->input->post("teamnameftc", true), FILTER_SANITIZE_STRING),
			"teamnamefll" => sanitizeInput($this->input->post("teamnamefll", true), FILTER_SANITIZE_STRING),
			"teamnamejrfll" => sanitizeInput($this->input->post("teamnamejrfll", true), FILTER_SANITIZE_STRING),
			"lifetime_goals" => sanitizeInput($this->input->post("lifetime_goals", true), FILTER_SANITIZE_STRING),
			"webapp_feedback" => sanitizeInput($this->input->post("webapp_feedback", true), FILTER_SANITIZE_STRING),
			"user_description" => sanitizeInput($this->input->post("user_description", true), FILTER_SANITIZE_STRING),
			"profile_img_path" => sanitizeInput($this->input->post("profile_img_path", true), FILTER_VALIDATE_URL)
		];

		$result = $this->user_model->update_user($data, 'user');
		echo json_encode($result);
	}

	function my_simple_crypt($string, $action = 'e') {
		// you may change these values to your own
		$secret_key = 'asd87as8d7a8sd7a8s7d89as';
		$secret_iv = 'as9d8as9fasfas89fas89hfgas';

		$output = false;
		$encrypt_method = "AES-256-CBC";
		$key = hash('sha256', $secret_key);
		$iv = substr(hash('sha256', $secret_iv), 0, 16);

		if ($action == 'e') {
			$output = base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv));
		} else if ($action == 'd') {
			$output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
		}

		return $output;
	}

	public function w9form() {
		check_login();
    	$w9data = $this->input->post();

    	$result = $this->user_model->w9_form_add_update($w9data);
    	echo $result;
	}
}