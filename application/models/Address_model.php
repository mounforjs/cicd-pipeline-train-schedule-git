<?php

class Address_model extends CI_Model {
    public function __construct() {
        parent::__construct();
    }

	public function getUserAddresses($user_id) {
		$this->db->from('user_address');
		$this->db->where("user_id", $user_id);
		$this->db->order_by("def", "DESC");

		return $this->db->get()->result();
	}

    public function getAddress($user_id, $id) {
		$this->db->from('user_address');
		$this->db->where(array("user_id" => $user_id, "id" => $id));

		return $this->db->get()->row();
	}

    public function getDefaultAddress($user_id) {
		$this->db->from('user_address');
		$this->db->where(array("user_id" => $user_id, "def" => 1));

		return $this->db->get()->row();
	}

    public function makeAddressDefault($user_id, $id) {
		$this->db->trans_start();

		$this->db->where(array("user_id" => $user_id, "def" => 1));
		$removeDefault = $this->db->update('user_address', array("def" => 0));

		$this->db->where(array("user_id" => $user_id, "id" => $id));
		$newDefault = $this->db->update('user_address', array("def" => 1));

		$this->db->trans_complete();

		return ($removeDefault && $newDefault);
	}

	public function insertAddress($data) {
		$exists = $this->getDefaultAddress($this->session->userdata("user_id"));

		if (isset($data->address_1) && isset($data->city) && isset($data->state) && isset($data->zip)) {
			$data = array(
				"user_id" => $this->session->userdata("user_id"),
				"name" => $data->name,
				"fullname" => $data->fullname,
				"address_1" => $data->address_1,
				"address_2" => $data->address_2,
				"city" => $data->city,
				"state" => $data->state,
				"zip" => $data->zip,
				"def" => (isset($exists)) ? 0 : 1
			);
            
			$this->db->insert('user_address', $data);
			return $this->db->insert_id();
		}
	}

	public function updateAddress($id, $data) {
		$exists = $this->getAddress($this->session->userdata("user_id"), $id);

		if (isset($exists) && isset($data->address_1) && isset($data->city) && isset($data->state) && isset($data->zip)) {
			$data = array(
				"user_id" => $this->session->userdata("user_id"),
				"name" => $data->name,
				"fullname" => $data->fullname,
				"address_1" => $data->address_1,
				"address_2" => $data->address_2,
				"city" => $data->city,
				"state" => $data->state,
				"zip" => $data->zip
			);
            
			$this->db->where(array('user_id' => $this->session->userdata("user_id"), "id" => $id));
			$this->db->update('user_address', $data);
			return ($this->db->affected_rows() > 0) ? $exists->id : 0;
		}
	}

	public function removeAddress($user_id, $id) {
		$this->db->where(array('user_id' => $user_id, "id" => $id));
		$this->db->delete('user_address');

		if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function reselectDefault($user_id) {
		$this->db->from("user_address");
		$this->db->where('user_id', $user_id);
		$this->db->order_by('id', "ASC");
		$newDefault = $this->db->get()->result();

		$this->db->where('id', $newDefault{0}->id);
		$this->db->update('user_address', array("def" => 1));

		return $newDefault{0}->id;
	}

	public function getGameCreator($game_id) {
		$this->db->from("game");
		$this->db->where("id", $game_id);
		$result = $this->db->get()->row();

		return $result->user_id;
	}
}
