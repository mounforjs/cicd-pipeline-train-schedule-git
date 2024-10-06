<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Wishlist_model extends CI_Model {

	public function get_wishlist_status($game_id) {
		$user_id = $this->session->userdata('user_id');

		$this->db->where('user_id', $user_id);
		$this->db->where('game_id', $game_id);
		$result = $this->db->get('user_wishlist')->row();
		if (empty($result)) { 
			return 0;
		}
		else {
			return 1;
		}
	}
	
	public function getTotalWishlisted($user_id) {
		$this->db->select("count(user_id) as total");
		$this->db->from("user_wishlist");
		$this->db->where("user_id", $user_id);

		$query = $this->db->get()->result_array();

		return $query;
	}
	
	public function remove_wishlist($game_id) {
		$user_id = $this->session->userdata('user_id');
		
		$this->db->where('user_id', $user_id);
		$this->db->where('game_id', $game_id);
		$this->db->delete('user_wishlist');
	}
	
	public function add_wishlist($game_id) {
		$user_id = $this->session->userdata('user_id');
		
		$this->db->where('user_id', $user_id);
		$this->db->where('game_id', $game_id);

		$query = $this->db->get('user_wishlist'); 
		$result = $query->row(); 
		if (empty($result)) {			
			$wishlist_data = array(
				'user_id' => $this->session->userdata('user_id'),
				'game_id' => $game_id,           
			);
			$result = $this->db->insert('user_wishlist', $wishlist_data);
			return ($this->db->affected_rows() > 0) ? TRUE : FALSE;
		} else {
			return 'already';
		}
	}
}