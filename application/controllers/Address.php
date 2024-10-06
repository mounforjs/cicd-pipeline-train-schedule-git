<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once __DIR__ . '/Email.php';

class Address extends CI_Controller {

	public function __construct () {
		parent::__construct();
        $this->load->model('address_model');
        $this->load->model('cron_model');

		check_login();
		$this->user_id = $this->session->userdata("user_id");
	}

	public function getAddress() {
		$result = array();

		$id = sanitizeInput($this->input->get("id"), FILTER_VALIDATE_INT);
		if (!$id) {
			$result = array("status" => "failed", "message" => "invalid input");
		} else {
			$address = $this->address_model->getAddress($this->user_id, $id);
			if (isset($address)) {
				$result = array("status" => "success", "address" => $address);
			} else {
				$result = array("status" => "failed");
			}
		}

		echo json_encode($result);
	}

	public function getDefaultAddress() {
		$result = array();

		$address = $this->address_model->getDefaultAddress($this->user_id);
		if (isset($address)) {
			$result = array("status" => "success", "address" => $address);
		} else {
			$result = array("status" => "failed");
		}

		echo json_encode($result);
	}

	public function makeAddressDefault() {
		$result = array();

		$id = sanitizeInput($this->input->post("id"), FILTER_VALIDATE_INT);

		$default = $this->address_model->makeAddressDefault($this->user_id, $id);
		$result["status"] = ($default) ? "success" : "failed";
		
		echo json_encode($result);
	}

    public function newAddress() {
		$result = array("status" => "failed");

		$claim = sanitizeInput($this->input->post("claim", true), FILTER_VALIDATE_BOOLEAN) ? 1 : 0;
		$validate = $this->validateAddressInput();
		if (!$validate["valid"]) {
			$result["message"] = $validate["failed"];
		} else {
			$address = $validate["data"];

			$insert = $this->address_model->insertAddress($address);
			if (!$insert) {
				$result["message"] = "Unable to add address.";
			} else {
				$total = count($this->address_model->getUserAddresses($this->session->userdata("user_id")));
				$address = $this->address_model->getAddress($this->session->userdata("user_id"), $insert);
				$view = $this->load->view("address/address-partial", array("claim" => $claim, "key" => $total-1, "address" => $address), TRUE);

				$result = array("status" => "success", "message" => $view, "address" => $address);
			}
		}

		echo json_encode($result);
	}	

    public function editAddress() {
		$result = array("status" => "failed");

		$id = sanitizeInput($this->input->post("id"), FILTER_VALIDATE_INT);
		$validate = $this->validateAddressInput();
		if (!$id || !$validate["valid"]) {
			$result["message"] = $validate["failed"];
		} else {
			$address = $validate["data"];
			
			$update = $this->address_model->updateAddress($id, $address);
			if (!$update) {
				$result["message"] = "Unable to update address.";
			} else {
				$address = $this->address_model->getAddress($this->session->userdata("user_id"), $update);

				$result = array("status" => "success", "message" => "Updated address.");

			}	
		}

		echo json_encode($result);
	}	

	public function removeAddress() {
		$result = array("status" => "failed");

		$id = sanitizeInput($this->input->post("id"), FILTER_VALIDATE_INT);
		if (!$id) {
			$result["message"] = "invalid input: id";
		} else {
			$this->db->trans_start();

			$remove = $this->address_model->removeAddress($this->user_id, $id);
			$default = $this->address_model->getDefaultAddress($this->user_id);
			if (!isset($default)) {
				$reselected = $this->address_model->reselectDefault($this->user_id);
			}

			$this->db->trans_complete();

			if ($remove) {
				$result = array("status" => "success", "message" => "Removed address.");
				if ($reselected > 0) {
					$result["default"] = $reselected;
				}
			} else {
				$result["message"] = "Unable to remove address.";
			}
		}

		echo json_encode($result);
	}
    
    public function confirmAddress() {
		$address_id = sanitizeInput($this->input->get("address_id"), FILTER_VALIDATE_INT);
		$game_id = sanitizeInput($this->input->get("game_id"), FILTER_VALIDATE_INT);

		$address = $this->address_model->getAddress($this->user_id, $address_id);
		if (!$address_id || !$game_id || !isset($address)) {
			echo json_encode(array("status" => "failed", "error" => "Address not found."));
			exit();
		}

		//process confirmation of address
		$this->db->trans_start();

		$this->cron_model->markDistributionConfirmed($game_id, $this->user_id); //confirm distribution
		if ($this->db->affected_rows() == 0) {
			echo json_encode(array("status" => "failed", "error" => "could not confirm distribution"));
			exit();
		} else {
			$shipper_id = $this->address_model->getGameCreator($game_id);
			$this->cron_model->createShippingInfo($shipper_id, $this->user_id, $address_id, $game_id); //create shipping info for prize game
			if ($this->db->affected_rows() == 0) {
				echo json_encode(array("status" => "failed", "error" => "could not create shipping details"));
				exit();
			}

			$notes = "A player that has won one of your fundraisers has claimed their prize!";

			$emailData = $this->cron_model->getEmailInfo($shipper_id, $game_id);
			$body = $this->load->view('emails/prize-claimed-email', $emailData, true);
			$subject = "A player has claimed their prize!";

			sendNotificationAndEmail("winner_claimed_prize", $shipper_id, $this->user_id, $notes, "prize", "claimed", $game_id, null, $subject, $body);

			echo json_encode(array("status" => "success", "msg" => "confirmed your address", "redirect" => site_url("dashboard?tab=prizes")));
		}

		$this->db->trans_complete();
	}

	public function validateAddress() {
		$result = array();

		$validate = $this->validateAddressInput();
		if (!$validate["valid"]) {
			$result  = array("status" => "failed", "message" => $validate["failed"]);
		} else {
			$address = $validate["data"];
			$result = $this->validateAddressUSPS($address);
		}

		echo json_encode($result);
	}

	private function validateAddressUSPS($data) {
		$user_id = "491WINWI4777";
		$url = 'https://secure.shippingapis.com/ShippingAPI.dll?API=Verify';
			
		$address_1 = '<Address1>' . $data->address_1 . '</Address1>';
		$address_2 = '<Address2>' . $data->address_2 . '</Address2>';
		$city = '<City>' . $data->city . '</City>';
		$state = '<State>' . $data->state . '</State>';
		$zip = '<Zip5>' . $data->zip . '</Zip5><Zip4></Zip4>';

		$address = '<AddressValidateRequest USERID="' . $user_id . '"><Address ID="0">' . $address_1 . $address_2 . $city . $state . $zip . '</Address></AddressValidateRequest>';

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_POSTFIELDS, 'XML=' . $address);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 300);

		$result = curl_exec($curl);
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		curl_close($curl);

		if ($code === 200) {
			$result = json_decode(json_encode(simplexml_load_string($result)), true);

			if (!isset($result["Address"]["Error"])) {
				return array("status" => "success", "Address" => $result["Address"]);
			} else {
				return array("status" => "failed", "Error" => $result["Address"]["Error"]["Description"]);
			}
		} else {
			return array("status" => "failed");
		}
	}

	private function validateAddressInput() {
		$address = (object) array(
			"name" => sanitizeInput($this->input->post("address_name", true), FILTER_SANITIZE_STRING),
			"fullname" => sanitizeInput($this->input->post("fullname", true), FILTER_SANITIZE_STRING),
			"address_1" => sanitizeInput($this->input->post("address_1", true), FILTER_SANITIZE_STRING),
			"address_2" => sanitizeInput($this->input->post("address_2", true), FILTER_SANITIZE_STRING),
			"city" => sanitizeInput($this->input->post("city", true), FILTER_SANITIZE_STRING),
			"state" => sanitizeInput($this->input->post("state", true), FILTER_SANITIZE_STRING),
			"zip" => str_pad(sanitizeInput((int)$this->input->post("zip", true), FILTER_VALIDATE_INT), 5, "0", STR_PAD_LEFT)
		);

		return validateInputs($address);
	}
}
