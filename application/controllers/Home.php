<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once __DIR__.'/Email.php';

class Home extends CI_Controller {

	public function __construct () {
		parent::__construct();

		$this->load->model('home_model');
		$this->load->model('charity_model');
		$this->load->model('address_model');
		$this->load->model('user_model');

		$this->load->library('template');
        $this->load->library('session');
        $this->load->library('email');
		$this->load->library('user_agent');

        $this->load->helper('cookie');

		$this->template->set_breadcrumb('Home', asset_url());
	}

	public function index() {


		$this->load->library('Gamedata');
		// set referral code to both session and cookie
		$referral = sanitizeInput($_GET['referral'], FILTER_SANITIZE_STRING);

		$key = getenv('ENCRYPTED_AWS_ACCESS_KEY_ID');
		die($key);
		
		$_SESSION["referral"] = $referral;
		set_cookie(array(
			'name'   => 'referral',
			'value'  => $referral,
			'expire' => 3600,
		));

		if($_SESSION["referral"]) {
			redirect('register');
		}

		$data['charity'] = $this->charity_model->get_all_fundraiser_list('charity', "", true, 4, 0);
		$data['project'] = $this->charity_model->get_all_fundraiser_list('project', "", true, 4, 0);
		$data['education'] = $this->charity_model->get_all_fundraiser_list('education', "", true, 4, 0);

        foreach ($data['charity'] as $key => $value) {
			$data['charity'][$key]['Image'] = getImagePathSize($value['Image'], 'beneficiary_card');
            $data['charity'][$key]['icon']  = getFundraiseIcon($value['fundraiser_type']);
        }

		foreach ($data['project'] as $key => $value) {
			$data['project'][$key]['Image'] = getImagePathSize($value['Image'], 'beneficiary_card');
            $data['project'][$key]['icon']  = getFundraiseIcon($value['fundraiser_type']);
        }

		foreach ($data['education'] as $key => $value) {
			$data['education'][$key]['Image'] = getImagePathSize($value['Image'], 'beneficiary_card');
            $data['education'][$key]['icon']  = getFundraiseIcon($value['fundraiser_type']);
        }
		
		$this->template->set_layout(DEFAULT_LAYOUT)->build('home/index', $data);
	}

	public function profile() {
	   	if (!$this->session->userdata('user_id')) {
			redirect('login');
		}

		$tab = sanitizeInput($this->input->get("tab"), FILTER_SANITIZE_STRING);
		switch ($tab) {
			case "beneficiary":
				$data["tab"] = 1;
				break;
			case "addresses":
				$data["tab"] = 2;
				break;
			case "accounts":
				$data["tab"] = 3;
				break;
			case "preferences":
				$data["tab"] = 4;
				break;
			default:
				$data["tab"] = 0;
				break;
		}

		$data['userData'] = $this->user_model->get_user_profile();
		$data['userData']->addresses = $this->address_model->getUserAddresses($this->session->userdata('user_id'));
		
		$data['default_fundraise'] = $this->charity_model->get_user_default_fundraise();
		$data['default_fundraise']->Image = getImagePathSize($data['default_fundraise']->Image,'beneficiary_info_logo');
		$data['default_fundraise']->icon = getFundraiseIcon($data['default_fundraise']->fundraiser_type);
		$data['default_fundraise']->totalRaised = $this->charity_model->get_total_raised($data['default_fundraise']->slug)->raised;
		$data["search"] = $this->charity_model->get_beneficiary_list();

		$data['fundraise_list'] = $this->charity_model->get_all_fundraise();
		$data['wishlist_count'] = $this->user_model->get_wishlist_count();
		$data['created_count'] = $this->user_model->get_created_count();
		$data['linked_accounts'] = $this->home_model->getAllLinkedStatus($this->session->userdata('user_id'));
	
		$data['w9_data'] = $this->user_model->w9_form_get_data();		

		$data["noti_preferences"] = get_noti_preferences();
		$data["email_preferences"] = get_email_preferences();

		$this->template->set_breadcrumb('My Profile', asset_url('profile'));
		$this->template->set_layout(DEFAULT_LAYOUT)->build('home/profile', $data);
    }

	public function covid() {
	  	$this->template->set_layout(DEFAULT_LAYOUT)->build('home/covid19', 	 $data);
	}

	public function board() {
		$data['imgForNeil'] = 'https://dg7ltaqbp10ai.cloudfront.net/fit-in/200x200/neil.png';
		$data['imgForEllie'] = 'https://dg7ltaqbp10ai.cloudfront.net/fit-in/200x200/ellie.png';
		$data['imgForScott'] = 'https://dg7ltaqbp10ai.cloudfront.net/fit-in/200x200/scott.png';
		$data['imgForClodomir'] = 'https://dg7ltaqbp10ai.cloudfront.net/ClodomirGoncalves.jpg';
		$data['defaultImgPlaceholder'] = 'https://dg7ltaqbp10ai.cloudfront.net/fit-in/200x200/personPlaceholder.png';

		$this->template->set_layout(DEFAULT_LAYOUT)->build('home/board', $data);
	}

	public function history() {
		$data = '';
	  	$this->template->set_layout(DEFAULT_LAYOUT)->build('home/history', 	 $data);
	}

	public function mission() {
		$data = '';
	  	$this->template->set_layout(DEFAULT_LAYOUT)->build('home/mission', 	 $data);
	}
	
	public function disclosure() {
		$data = '';
	  	$this->template->set_layout(DEFAULT_LAYOUT)->build('home/disclosure', 	 $data);
	}

	public function governance() {
		$data = '';
	  	$this->template->set_layout(DEFAULT_LAYOUT)->build('home/governance', 	 $data);
	}

	public function register() {
	  	check_logout();

		$this->setCookieRedirect();

		$checkReferralStatus = isReferral();
		if($checkReferralStatus === true) {
			$data['refStatus'] = 1;
		} elseif($checkReferralStatus === false) {
			$data['refStatus'] = 2;
			unset($_SESSION['referral']);
			delete_cookie('referral'); 
		} else {
			$data['refStatus'] = 3;
		}
		  
	  	$this->template->set_layout(DEFAULT_LAYOUT)->build('home/register', $data);
	}

	public function getReferralStatus() {
		unset($_SESSION['referral']);

		$cookieVal = sanitizeInput($this->input->post('id'),FILTER_SANITIZE_EMAIL);
		$_SESSION["referral"]= $cookieVal;
		$status = isReferral();
		if($status === false) {
			unset($_SESSION['referral']);
		}
	
		echo json_encode($status);
	}

	public function login() {
		check_logout();

		$this->setCookieRedirect();

        $this->template->set_layout(DEFAULT_LAYOUT)->build('home/login', '');
    }

    public function login_redirect() {
        check_logout();

		$this->form_validation->set_rules('email','Email','trim|required');
		$this->form_validation->set_rules('password','Password','trim|required');
		if ($this->form_validation->run() == FALSE) {
			$data = array (
				'errors' => validation_errors()
			);
			$this->session->set_flashdata($data);
			redirect("login");
		} else {
			$email = sanitizeInput($this->input->post('email'), FILTER_SANITIZE_EMAIL);
			$password = sanitizeInput($this->input->post('password'), FILTER_SANITIZE_STRING);

			$result = $this->user_model->login($email, $password);
			if (!isset($result['errors'])) {
				$user_id = $result['id'];

				$existing_session = $this->home_model->get_user_session($user_id);
				if (isset($existing_session) && !$existing_session->expired) { // if already logged in elsewhere, use same session
					$this->start_user_session($user_id, $existing_session->session_id);
				} else {
					// create new session and store in db
					$set_session = $this->home_model->set_user_session($user_id);
					if ($set_session["status"] == "success") {
						$this->start_user_session($user_id, $set_session["session_id"]);
					} else {
						redirect("login");
					}
				}
			} else {
				redirect("login");
			}
		}
    }

	private function setCookieRedirect() {
		$url = parse_url($this->agent->referrer());
		if ($url["host"] == $_SERVER['SERVER_NAME']) {
			// can set more conditions in the future

			// set redirect if coming from a game_detail page - matching a url /games/show/play/*any-slug-of-any-length*
			if (strpos($url["path"], "/games/show/play/") !== false && preg_match("/games\/show\/play\/(.*+)/i", $url["path"])) {
				set_cookie(array(
					'name'   => 'redirect_to',
					'value'  =>  $url["path"],                            
					'expire' => 0,
				));
			}
		}
	}

	private function start_user_session($user_id, $session_id) {
		$this->home_model->track($user_id);
	
		$session_data = array('user_id' => $user_id, "session_id" => $session_id);
		$this->session->set_userdata($session_data);

		if (isset($_COOKIE["redirect_to"])) {
			$redirect_to = $_COOKIE["redirect_to"];
			delete_cookie("redirect_to");
			unset($_COOKIE['redirect_to']);

			redirect($redirect_to);
		} else {
			redirect();
		}
	}

    public function reset() {
        check_logout();
        $this->form_validation->set_rules('reset_email', 'Email Address', 'trim|required');
        $data = '';
        if ($this->form_validation->run() !== FALSE) {
            $this->user_model->reset_user();
        }

		$this->template->set_layout(DEFAULT_LAYOUT)->build('home/reset', $data);
    }

	public function logout() {
	   $this->session->sess_destroy('userdata');
	   $this->home_model->remove_user_session($this->session->userdata("user_id"));

	   redirect('login', 'refresh');
	}

	public function linkAccount() {
		$user_id = $this->session->userdata("user_id");
		$gametype = $this->input->post("type");

		$linked = $this->home_model->hasUserLinked($user_id, $gametype);
		$created = $this->home_model->hasUserCreatedCode($user_id, $gametype);
		if (!$linked) {
			if (!$created) {
				$email = $this->home_model->getUserEmail($user_id)[0];
				$result = $this->home_model->createLinkCode($user_id, $gametype);
				if ($result["created"] > 0) {
					$subject = "Linking Your " . ucfirst(str_replace("_", " ", $gametype)) . " Account";
					$data = array(
						"subject" => 'Your code is: '. $result["code"],
						"message" => "<ol style='padding: 0;'><li>Log into your Minecraft Account.</li><li>Connect to our server: minecraft.winwinlabs.org</li><li>Use the in-game command '/wwl link *code*' to connect your account.</li></ol>"
					);
			
					Email::index($email->email, $subject, $this->load->view("emails/link-account-email", $data, TRUE));
		
					echo json_encode(array("status" => "success", "message" => "code created, check email"));
				} else {
					echo json_encode(array("status" => "failed", "reason" => "2", "message" => "error, could not create code"));
				}
			} else {
				echo json_encode(array("status" => "failed", "reason" => "4", "message" => "code already created"));
			}
		} else {
			echo json_encode(array("status" => "failed", "reason" => "3", "message" => "already linked"));
		}
	}

	public function unlinkAccount() {
		$user_id = $this->session->userdata("user_id");
		$gametype = $this->input->post("type");

		$removed = $this->home_model->removeLinkedAccount($user_id, $gametype);
		if ($removed > 0) {
			echo json_encode(array("status" => "success", "message" => "account unlinked"));
		} else {
			echo json_encode(array("status" => "failed", "message" => "server error or no account to unlink"));
		}
	}

	public function jsError() {
		$this->load->view("errors/html/error_js_disabled");
	}

	public function updatePreferences() {
		$user_id = $this->session->userdata("user_id");

		$form = sanitizeInput($this->input->post("form"), FILTER_SANITIZE_STRING);
		if (!in_array($form, ["notification-prefs", "email-prefs"])) { //validate form id
			echo json_encode(array("status" => "failed", "msg" => "target not specified"));
		} else {
			$data = $this->input->post();
			unset($data["form"]);

			foreach ($data as $preference) { //validate values are either 1 or 0
				$pref = sanitizeInput($preference, FILTER_VALIDATE_INT);
				if (!isset($pref) || ($pref != 0 && $pref != 1)) {
					echo json_encode(array("status" => "failed", "msg" => "invalid preference value"));
					return;
				}
			}

			if ($form == "notification-prefs") {
				$update = $this->user_model->update_user_noti_preferences($user_id, $data);
			} else {
				$update = $this->user_model->update_user_email_preferences($user_id, $data);
			}

			echo json_encode($update);
		}
	}
}
