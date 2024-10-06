<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Gamedata {
	public function __construct() {
		$this->CI = &get_instance();
		
		$this->CI->load->model('game_play_model');
		$this->CI->load->model('user_model');
		$this->CI->load->model('wishlist_model');
		$this->CI->load->model('home_model');
		$this->CI->load->model('flags_model');
		$this->CI->load->model('charity_model');
	}

	public function getGamedata($filters=array(), $show='', $offset=0, $limit=6){
    	$data=array();
		
		$filters = $this->createCardFilters($show, $filters);
		$data["game_data"] = $this->CI->home_model->fetch_games($filters, $show, $offset, $limit);

		$data["filters"] = $filters;
	
		foreach ($data["game_data"] as $key => $value) {
	 		$data["game_data"][$key]->game_wishlist_status = $this->CI->wishlist_model->get_wishlist_status($value->id);
		}

		foreach ($data["game_data"] as $key => $value) {
			$data["game_data"][$key]->finance_details =  $this->CI->home_model->fetch_credit_detail($value->id);
		}

		foreach ($data["game_data"] as $key => $value) {
			$data["game_data"][$key]->game_type =  $this->CI->home_model->get_game_type($value->Type);
			$data["game_data"][$key]->GameImage = getImagePathSize($value->Game_Image,'game_card_thumbnails');
			$data["game_data"][$key]->game_type_image = getGameTypeImage($value->game_type[0]->name);
			$data["game_data"][$key]->credit_type_image = getCreditTypeImage($value->credit_type);
		}

		foreach ($data["game_data"] as $key => $value) {
			$data["game_data"][$key]->supported_fundraise =  $this->CI->home_model->get_supported_fundraise($value->id);
			$data["game_data"][$key]->supported_fundraise_image = getImagePathSize($value->supported_fundraise[0]->Image, 'game_card_beneficiary_icon');
		}

		return $data;
    }

	public function getCardfilterGamedata($filters=array(), $show='', $offset=0, $limit=6){
    	$data=array();

		$filters = $this->createCardFilters($show, $filters);	
		$data["game_data"] = $this->CI->home_model->fetch_games($filters, $show, $offset, $limit);

		$data["filters"] = $filters;

		foreach ($data["game_data"] as $key => $value) {
			$data["game_data"][$key]->finance_details =  $this->CI->home_model->fetch_credit_detail($value->id);
		}

		foreach ($data["game_data"] as $key => $value) {
			$data["game_data"][$key]->game_type =  $this->CI->home_model->get_game_type($value->Type);
			$data["game_data"][$key]->GameImage = getImagePathSize($value->Game_Image,'game_card_thumbnails');
			$data["game_data"][$key]->game_type_image = getGameTypeImage($value->game_type[0]->name);
			$data["game_data"][$key]->credit_type_image = getCreditTypeImage($value->credit_type);
		}

		foreach ($data["game_data"] as $key => $value) {
			$data["game_data"][$key]->supported_fundraise =  $this->CI->home_model->get_supported_fundraise($value->id);
			$data["game_data"][$key]->supported_fundraise_image = getImagePathSize($value->supported_fundraise[0]->Image,'game_card_beneficiary_icon');
		}

		foreach ($data["game_data"] as $key => $value) {
	 		$data["game_data"][$key]->game_wishlist_status = $this->CI->wishlist_model->get_wishlist_status($value->id);
		}

		$data['min_value_of_the_game'] = $this->CI->game_play_model->get_game_value('min');
		$data['max_value_of_the_game'] = $this->CI->game_play_model->get_game_value('max');
		$data['min_credit_cost'] = $this->CI->game_play_model->get_game_credit_cost('min');
		$data['max_credit_cost'] = $this->CI->game_play_model->get_game_credit_cost('max');
		
		// Define a safe step value
		$step = 5000;

		// Ensure min and max values are set and valid
		$min_value = isset($data['min_value_of_the_game']) ? round($data['min_value_of_the_game'], -1) : 0;
		$max_value = isset($data['max_value_of_the_game']) ? $data['max_value_of_the_game'] : 0;
		$min_cost = isset($data['min_credit_cost']) ? round($data['min_credit_cost'], -1) : 0;
		$max_cost = isset($data['max_credit_cost']) ? $data['max_credit_cost'] : 0;

		// Check if the step is valid for value range
		if ($step < ($max_value - $min_value)) {
			$value_range = range($min_value, $max_value, $step);
			$data['value_groups'] = array_chunk($value_range, 2);
		} else {
			$data['value_groups'] = []; // Handle invalid range
		}

		// Check if the step is valid for cost range
		if ($step < ($max_cost - $min_cost)) {
			$cost_range = range($min_cost, $max_cost, $step);
			$data['cost_groups'] = array_chunk($cost_range, 2);
		} else {
			$data['cost_groups'] = []; // Handle invalid range
		}

		$data['game_values'] = gameValue();
		$data['game_costs'] = gameCost();

		$data['game_types'] = $this->CI->game_play_model->get_types();
		$data['charity_data'] = $this->CI->game_play_model->get_charity();
		$data['fundraise_list'] = $this->CI->charity_model->get_all_fundraise();
		
		return $data;
    }

	private function createCardFilters($show, $filters) {
		$filters["show"] = $show;

		if (!empty($filters)) {
			$filters = array_filter($filters, function($value) {
				return isset($value) && ($value === "0" || $value);
			});

			if (isset($filters["user"])) {
				$users = implode(" ", array_map(function($val) {
					return "user:{$val}";
				}, explode(",", $filters["user"])) );

				if (isset($filters["search"])) {
					$filters["search"] = $filters["search"] . " " . $users;
				} else {
					$filters["search"] = $users;
				}

				unset($filters["user"]);
			}

			if (isset($filters["tags"])) {
				$tags = implode(" ", array_map(function($val) {
					return "tag:{$val}";
				}, explode(",", $filters["tags"])) );

				if (isset($filters["search"])) {
					$filters["search"] = $filters["search"] . " " . $tags;
				} else {
					$filters["search"] = $tags;
				}

				unset($filters["tags"]);
			}

			foreach ($filters as $key => $val) {
				if ($key == "beneficiary") {
					$b_val = sanitizeInput($val, FILTER_SANITIZE_STRING | FILTER_FLAG_ENCODE_HIGH );
					$beneficiary = $this->CI->charity_model->getBeneficiaryNameBySlug($b_val);
					$filters[$key] = ["slug" => $beneficiary->slug, "name" => $beneficiary->name];
				} else if ($key == "game_values" || $key == "game_costs") {
					$_val = explode(",", sanitizeInput($val, FILTER_SANITIZE_STRING));
					$filters[$key] = create_range($_val);
				} else if ($key == "limit") {
					$filters[$key] = sanitizeInput($val, FILTER_VALIDATE_INT);
				} else if ($key == "search") {
					$s_val = sanitizeInput($val, FILTER_SANITIZE_STRING | FILTER_FLAG_NO_ENCODE_QUOTES);
					$filters[$key] = $this->createKeywords($s_val);
				} else {
					$_val = sanitizeInput($val, FILTER_SANITIZE_STRING);

					#possible to sort by multple sort list which should be impossible
					$filters[$key] = implode(",", array_map('trim', explode(",", $_val)));
				}
			}
		}

		return array_filter($filters, function($f) { return isset($f); });;
	}

	private function createKeywords($search) {
		$regex_match_keyword = "/(\w*):(?!$)(('(.*?)'|(\"(.*?)\")|((?<![\"'])\w*))(,?))*/i";

		# seperate keywords by - *type*:*value* , *type*:"*value*" , *type*:'*value*'
		$tags = array();
		$result = preg_match_all($regex_match_keyword, $search, $tags, PREG_PATTERN_ORDER);
		
		# remove tags from original
		$search_no_tags = trim(preg_replace($regex_match_keyword, "", $search));

		#search for any strings inside quotes
		$keywords = array();
		$quoted_kwrds = preg_match_all("/[^\s\"']+|\"([^\"]*)\"|'([^']*)'/", $search_no_tags, $keywords, PREG_PATTERN_ORDER);

		return array("input" => $search, "keywords" => $keywords[0], "tags" => $tags[0]);
	}
}

?>