<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Checksession extends CI_Controller{

	public function index() {
		date_default_timezone_set("UTC");

		$response = array();

		if (!$this->session->userdata("user_id")) {
			$this->end_session();
			$response["redirect"] = true;
		} else {
			$existing_session = get_user_session();
			if (!isset($existing_session)) {
				$response["redirect"] = true;
				$this->end_session();
			} else if (isset($existing_session) && $existing_session->expired) {
				$response["invalid"] = true;
				$this->end_session();
			} else {
				//ajax/xhr triggers session refresh and time of last activity
				if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
					$_SESSION['last_activity'] = time();
					unset($_SESSION['force_expire_by']);
					update_session_activity();
				} else {
					$time = time();

					$expiration = config_item('sess_expiration'); //default 7200s - 2hrs
					$warning = config_item('sess_warning'); //default 300s - 5min

					$last_activity = $_SESSION['last_activity'];
					$expires_at = isset($_SESSION['force_expire_by']) ? $_SESSION['force_expire_by'] : $last_activity + $expiration;

					$time_until = round(($expires_at - $time) / 10) * 10; // round to nearest ten

					$warning =  $time_until <= $warning;
					$expired = $time_until <= 0;

					if ($expired || $time_until > $expiration) {
						$response["redirect"] = true;
						$this->end_session();
					} else {
						if ($warning) {
							$response["warning"] = true;

							if (!isset($_SESSION["force_expire_by"])) {
								$_SESSION["force_expire_by"] = $expires_at;
							} else {
								if ($_SESSION["force_expire_by"] - $time < 0) {
									unset($response["warning"]);
									$response["redirect"] = true;
								}
							}
						}
					}
				}
			}
		}

		echo json_encode($response);
	}

	private function end_session() {
		unset($_SESSION["sess_timeout_warning"]);
		session_unset();
	}
}
