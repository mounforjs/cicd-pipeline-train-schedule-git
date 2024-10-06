<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once 'application/libraries/htmlpurifier/library/HTMLPurifier.auto.php';

if (!function_exists('addScheme()')) {
    function addScheme($url, $scheme = 'http://')
	{
	  return parse_url($url, PHP_URL_SCHEME) === null ?
		$scheme . $url : $url;
	}
}

if (!function_exists('asset_url()')) {
    function asset_url($name='') {
        return base_url() . $name;
    }
}

if (!function_exists('js_url()')) {
    function js_url($name='') {
        return base_url() .'assets/js/' .$name;
    }
}

if (!function_exists('round_to_2dc()')) {
    function round_to_2dc($value='') {
        return number_format((float)$value, 2, '.', ',');
    }
}

if (!function_exists('get_user_session()')) {
    function get_user_session() {
        $CI = &get_instance();
        $CI->load->model('home_model');
        return $CI->home_model->get_user_session($CI->session->userdata("user_id"));
    }
}

if (!function_exists('update_session_activity()')) {
    function update_session_activity() {
        $CI = &get_instance();
        $CI->load->model('home_model');
        return $CI->home_model->update_user_session($CI->session->userdata("user_id"));
    }
}

if (!function_exists('getprofile()')) {
    function getprofile($user_id=null) {
        $CI = &get_instance();
        if (!isset($user_id) && $CI->session->userdata('user_id')) {
			$CI->load->model('user_model');
			$usr_profile = $CI->user_model->get_user_profile();

			return $usr_profile;
        } else if (isset($user_id)) {
            $CI->load->model('user_model');
			$usr_profile = $CI->user_model->get_user_profile($user_id);

			return $usr_profile;
        }
    }
}

if (!function_exists('get_noti_preferences()')) {
    function get_noti_preferences($user_id=null) {
        $CI = &get_instance();
        $CI->load->model('user_model');
        if (!isset($user_id)) {
            $user_id = $CI->session->userdata('user_id');
        }

        return $CI->user_model->get_user_noti_preferences($user_id);
    }
}

if (!function_exists('get_email_preferences()')) {
    function get_email_preferences($user_id=null) {
        $CI = &get_instance();
        $CI->load->model('user_model');
        if (!isset($user_id)) {
            $user_id = $CI->session->userdata('user_id');
        }

        return $CI->user_model->get_user_email_preferences($user_id);
    }
}

if (!function_exists('get_game_user()')) {
    function get_game_user($user_id='') {
        $CI = &get_instance();
        $CI->load->model('user_model');
		$usr_profile = $CI->user_model->get_user_profile($user_id);

		return $usr_profile;

    }
}

if (!function_exists('get_fundraise_name()')) {
    function get_fundraise_name($fund_id='') {
        $CI = &get_instance();
        $CI->load->model('user_model');
		$fund_detail = $CI->user_model->get_fundraise_name($fund_id);

		return $fund_detail;

    }
}

if (!function_exists('get_default_fundraise()')) {
    function get_default_fundraise($user_id='') {
        $CI = &get_instance();
		if ($CI->session->userdata('user_id')) {
			$CI->load->model('user_model');
			$user_id = $CI->session->userdata('user_id');
			$default_fundraise = $CI->user_model->get_user_field($user_id, 'default_fundraise');
			if ($default_fundraise) {
				return $default_fundraise->default_fundraise;
			}
        }
    }
}

if (!function_exists('get_supported_fundraise()')) {
    function get_supported_fundraise($fund_id) {
        $CI = &get_instance();
        $CI->load->model('user_model');
		$supported_fundraise = $CI->user_model->get_supported_fundraise($fund_id);

		return $supported_fundraise;
    }
}

if (!function_exists('getBalance()')) {
    function getBalance() {
        $CI = &get_instance();
        if ($CI->session->userdata('user_id')) {
			$CI->load->model('transaction_model');
			$balance = $CI->transaction_model->getUserLastBalance();

			return '$'.$balance;
        } else {
        }
    }
}

if (!function_exists('getBalanceAsFloat()')) {
    function getBalanceAsFloat() {
        $CI = &get_instance();
        if ($CI->session->userdata('user_id')) {
      $CI->load->model('transaction_model');
      $balance = $CI->transaction_model->getUserLastBalance();

      return $balance;
        } else {
        }
    }
}

if (!function_exists('getTotalDrafted()')) {
    function getTotalDrafted() {
        $CI = &get_instance();
        $user_id = $CI->session->userdata('user_id');
        if ($user_id) {
			$CI->load->model('home_model');
            $drafted = $CI->home_model->getTotalGame($user_id, 'drafted');
            
            $total = $drafted[0]['total'];

			return $total;
        } else {
        }
    }
}

if (!function_exists('getTotalPublished()')) {
    function getTotalPublished() {
        $CI = &get_instance();
        $user_id = $CI->session->userdata('user_id');
        if ($user_id) {
			$CI->load->model('home_model');
            $published = $CI->home_model->getTotalGame($user_id, 'published');
            
            $total = $published[0]['total'];

			return $total;
        } else {
        }
    }
}

if (!function_exists('getTotalLive()')) {
    function getTotalLive() {
        $CI = &get_instance();
        $user_id = $CI->session->userdata('user_id');
        if ($user_id) {
			$CI->load->model('home_model');
            $live = $CI->home_model->getTotalGame($user_id, 'live');
            
            $total = $live[0]['total'];

			return $total;
        } else {
        }
    }
}

if (!function_exists('getTotalReview()')) {
    function getTotalReview() {
        $CI = &get_instance();
        $user_id = $CI->session->userdata('user_id');
        if ($user_id) {
			$CI->load->model('manage_games_model');
            $reviewGameCount = $CI->manage_games_model->reviewGamesCount($user_id);

            return $reviewGameCount;
        } else {
        }
    }
}

if (!function_exists('getTotalCompleted()')) {
    function getTotalCompleted() {
        $CI = &get_instance();
        $user_id = $CI->session->userdata('user_id');
        if ($user_id) {
			$CI->load->model('home_model');
            $review = $CI->home_model->getTotalGame($user_id, 'completed');
            
            $total = $review[0]['total'];

			return $total;
        } else {
        }
    }
}

if (!function_exists('getTotalWishlisted()')) {
    function getTotalWishlisted() {
        $CI = &get_instance();
        $user_id = $CI->session->userdata('user_id');
        if ($user_id) {
			$CI->load->model('wishlist_model');
            $wishlisted = $CI->wishlist_model->getTotalWishlisted($user_id);
            
            $total = $wishlisted[0]['total'];

			return $total;
        } else {
        }
    }
}

if (!function_exists('getTotalPlayed()')) {
    function getTotalPlayed() {
        $CI = &get_instance();
        $user_id = $CI->session->userdata('user_id');
        if ($user_id) {
			$CI->load->model('home_model');
            $played = $CI->home_model->getTotalGame($user_id, 'played');
            
            $total = count($played);

			return $total;
        } else {
        }
    }
}

if (!function_exists('check_logout()')) {
    function check_logout() {
        $CI = &get_instance();
        if ($CI->session->userdata('user_id')) {
            redirect('', 'refresh');
        }
    }
}

if (!function_exists('get_feedback_category()')) {
    function get_feedback_category($feedback_id=0) {
        $CI = &get_instance();
        $CI->load->model('feedback_model');
		$feedback_cateogry_list = $CI->feedback_model->feedback_category_list($feedback_id);

		return $feedback_cateogry_list;
    }
}

if (!function_exists('quotes_list()')) {
    function quotes_list() {
        $CI = &get_instance();
        $CI->load->model('quote_model');
		$quote_list = $CI->quote_model->get_all_featured_quotes();

		return $quote_list;
    }
}

if (!function_exists('game_visits_count()')) {
    function game_visits_count($game_id) {
        $CI = &get_instance();
        $CI->load->model('game_play_model');
		$game_visits_count = $CI->game_play_model->game_visits_count($game_id);

		return $game_visits_count;
    }
}

if (!function_exists('game_playes_count()')) {
    function game_playes_count($game_id) {
        $CI = &get_instance();
        $CI->load->model('game_play_model');
		$game_playes_count = $CI->game_play_model->game_playes_count($game_id);

		return $game_playes_count;
    }
}

if (!function_exists('get_page_links()')) {
    function get_page_links() {
        $CI = &get_instance();
        $CI->load->model('feedback_model');
		$page_links = $CI->feedback_model->get_all_page_links();

		return $page_links;
    }
}

if (!function_exists('charity_game_count()')) {
    function charity_game_count($charity_id) {
        $CI = &get_instance();
        $CI->load->model('game_play_model');
		$charity_game_count = $CI->game_play_model->charity_game_count($charity_id);

		return $charity_game_count;
    }
}

if (!function_exists('get_game_rating()')) {
    function get_game_rating($game_id) {
        $CI = &get_instance();
        $CI->load->model('game_play_model');
		$game_rating = $CI->game_play_model->get_game_rating($game_id);

		return $game_rating;


    }
}

if (!function_exists('check_login()')) {
    function check_login() {
        $CI = &get_instance();
        if ($CI->session->userdata('user_id')) {
        } else {
            redirect('login', 'refresh');
        }
    }
}

if (!function_exists('checkAdminLogin()')) {
    function checkAdminLogin() {
        $CI = &get_instance();
        if ($CI->session->userdata('user_id') && getprofile()->usertype=='2') {
            return true;
        } else {
           return false;
        }
    }
}

if (!function_exists('checkSysAdminLogin()')) {
    function checkSysAdminLogin() {
        $CI = &get_instance();
        if ($CI->session->userdata('user_id') && getprofile()->usertype=='3') {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('checkSupporterLogin()')) {
    function checkSupporterLogin() {
        $CI = &get_instance();
        if ($CI->session->userdata('user_id') && getprofile()->usertype=='4') {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('checkContentCreatorLogin()')) {
    function checkContentCreatorLogin() {
        $CI = &get_instance();
        if ($CI->session->userdata('user_id') && getprofile()->usertype=='5') {
            return true;
        } else {
            return false;
        }
    }
}

if (!function_exists('isAdmin()')) {
    function isAdmin() {
        $CI = &get_instance();
        if ($CI->session->userdata('user_id') && getprofile()->usertype=='2') {
            return true;
        } else {
           return false;
        }
    }
}

if (!function_exists('isRegularUser()')) {
    function isRegularUser() {
        $CI = &get_instance();
        if ($CI->session->userdata('user_id') && getprofile()->usertype=='0') {
            return true;
        } else {
           return false;
        }
    }
}

if (!function_exists('showAdminNav()')) {
    function showAdminNav() {
        if (isAdmin() ||
            checkSysAdminLogin() || 
            checkSupporterLogin() || 
            checkContentCreatorLogin()
        ) {
            return true;
        } else {
           return false;
        }
    }
}

if (!function_exists('getGameTypeImage()')) {
    function getGameTypeImage($type) {
        $minecraft_types = ['minecraft', 'zombie_blitz', 'skeleton_shootout', 'parkour_escape', 'custom', 'stone_collector', 'warden_fight', 'duels'];
        
        if (in_array($type, $minecraft_types)) {
            $image = "https://dg7ltaqbp10ai.cloudfront.net/fit-in/200x200/minecraft-logo.png";
        } 
        else {
            switch ($type) {
                case "puzzle":
                    $image = "https://dg7ltaqbp10ai.cloudfront.net/fit-in/41x41/1591893582-categoryiconpuzzle.png";
                    break;
                case "2048":
                    $image = "https://dg7ltaqbp10ai.cloudfront.net/fit-in/41x41/2048-logo.png";
                    break;
                case "challenge":
                    $image = "https://dg7ltaqbp10ai.cloudfront.net/fit-in/41x41/1591893396-categoryiconchallenge.png";
                    break;
                default:
                    $image = "https://dg7ltaqbp10ai.cloudfront.net/fit-in/41x41/gameicon.png";
                    break;
            }
        }
        return $image;
    }
}

if (!function_exists('getCreditTypeImage()')) {
    function getCreditTypeImage($type) {
        switch ($type) {
            case "credit":
                $image = "https://dg7ltaqbp10ai.cloudfront.net/fit-in/200x200/fa-cash-bill-icon.png";
                break;
            case "prize":
                $image = "https://dg7ltaqbp10ai.cloudfront.net/fit-in/200x200/fa-red-prize-icon-01.png";
                break;
            default:
                $image = "https://dg7ltaqbp10ai.cloudfront.net/fit-in/200x200/freegamesicon.png";
                break;
        }

        return $image;
    }
}

if (!function_exists('getImagePathSize()')) {
    function getImagePathSize($image, $size=null, $fallback=null) {
        $validation = "/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i";
        $filterType = 'fit-in';

        switch ($size) {
            case 'fundraiser':
                $size = '302x200';
                $fallback = 'https://dg7ltaqbp10ai.cloudfront.net/game.jpeg';
                break;
            case 'game_card_thumbnails':
                $fallback = 'https://dg7ltaqbp10ai.cloudfront.net/game.jpeg';
                $size = '370x240';
                $fallback = 'https://dg7ltaqbp10ai.cloudfront.net/game.jpeg';
                break;
            case 'game_card_beneficiary_icon':
                $size = '125x125';
                $fallback = 'https://dg7ltaqbp10ai.cloudfront.net/game.jpeg';
                break;
            case 'prize_creator_icon':
                $size = '125x125';
                $fallback = 'https://dg7ltaqbp10ai.cloudfront.net/16527364686282c1d478892_icon.png';
                break;
            case 'profile_image_icon':
                $size = '35x35';
                $fallback = 'https://dg7ltaqbp10ai.cloudfront.net/16527364686282c1d478892_icon.png';
                break;
            case 'profile_image':
                $size = '200x200';
                $fallback = 'https://dg7ltaqbp10ai.cloudfront.net/16527364686282c1d478892_icon.png';
                break;
            case 'puzzle_image_create_game':
                $size = '200x200';
                $fallback = 'https://dg7ltaqbp10ai.cloudfront.net/165775718162cf5dfddc915_icon.png';
                break;
            case 'game_details_slideshow_main_image':
                $size = '740x440';
                $fallback = 'https://dg7ltaqbp10ai.cloudfront.net/165775718162cf5dfddc915_icon.png';
                break;
            case 'game_details_slideshow_images_thumbnail':
                $size = '112x70';
                $fallback = 'https://dg7ltaqbp10ai.cloudfront.net/game.jpeg';
                break;
            case 'beneficiary_info_logo':
                $size = '314x200';
                $fallback = 'https://dg7ltaqbp10ai.cloudfront.net/game.jpeg';
                break;
            case 'create_a_game_upload_thumbnails_for_game_and_prize':
                $size = '244x151';
                $fallback = 'https://dg7ltaqbp10ai.cloudfront.net/165775718162cf5dfddc915_icon.png';
                break;
            case 'prize_image_thumbnail':
                $size = '70x70';
                $fallback = 'https://dg7ltaqbp10ai.cloudfront.net/game.jpeg';
                break;
            case 'image_upload_placeholder':
                $size = '70x70';
                $fallback = 'https://dg7ltaqbp10ai.cloudfront.net/165775718162cf5dfddc915_icon.png';
                break;
            case 'puzzle_game_image':
                $size = '1000x670';
                $fallback = 'https://dg7ltaqbp10ai.cloudfront.net/game.jpeg';
                break;
            case 'add_new_beneficiary_upload_thumbnail':
                $size = '200x150';
                $fallback = 'https://dg7ltaqbp10ai.cloudfront.net/game.jpeg';
                break;
            case 'beneficiary_card':
                $size = '330x210';
                $fallback = 'https://dg7ltaqbp10ai.cloudfront.net/game.jpeg';
                break;
            case 'admin_games':
                $size = '50x50';
                $fallback = 'https://dg7ltaqbp10ai.cloudfront.net/game.jpeg';
                break;
            default:
                $size = '302x200';
                $fallback = 'https://dg7ltaqbp10ai.cloudfront.net/game.jpeg';
                break;
        }

        if ((preg_match($validation, $image) == 1)) {
            if (!strpos($image, $filterType)) {
                $split = explode('/', $image);
                $filename = end($split);

                $new_size = $filterType.'/'.$size.'/'.$filename;
                $image = str_replace($filename, $new_size, $image);
            } else {
                $image = preg_replace("/[0-9]+x[0-9]+/i", $size, $image);
            }

            if (!strpos($fallback, $filterType)) {
                $_split = explode('/', $fallback);
                $_filename = end($_split);

                $_new_size = $filterType.'/'.$size.'/'.$_filename;
                $fallback = str_replace($_filename, $_new_size, $fallback);
            } else {
                $fallback = preg_replace("/[0-9]+x[0-9]+/i", $size, $fallback);
            }

        }

        return array("image" => $image, "fallback" => $fallback);
    }
}

if (!function_exists('isDatePassed')) {
    function isDatePassed($date) {
        date_default_timezone_set('UTC');

		$currentDate = DateTime::createFromFormat('Y-m-d H:i:s', date("Y-m-d H:i:s"));
		if ($currentDate >= $date) {
			return true;
		} else {
			return false;
		}
    }
}

if (!function_exists('getDefaultPaymentMethodType()')) {
    function getDefaultPaymentMethodType() {
        $pType = 'paypal';
        // switch (getprofile()->default_payment_method) {
        //     case "1":
        //         $pType = 'bank';
        //         break;
        //     case "2":
        //         $pType = 'credit';
        //         break;
        //     case "3":
        //         $pType = 'paypal';
        //         break;
        // }
        return $pType;
    }
}

if (!function_exists('getGameState()')) {
    function getGameState($game) {
        $CI = &get_instance();
        $user_id = $CI->session->userdata("user_id");

        if ($game->Publish == "No" && $game->Publish_Date == NULL) {
            return "drafted";
        } else if ($game->Publish == "Yes" && $game->Publish_Date != NULL) {
            if ($game->user_id === $user_id) {
                return "published";
            } else {
                return 'play';
            }
        } else if ($game->Publish == "Live" && $game->Publish_Date != NULL) {
            if ($game->user_id === $user_id) {
                return "live";
            } else {
                return 'play';
            }
        } else if ($game->Status == 'Completed') {
            return 'completed';
        } else {
			return 'play';
		}
    }
}

if (!function_exists('validateDateString()')) {
    function validateDateString($date) {
        $dateObj = new DateTime($date);
        if ($dateObj) {
            return $date;
        } else {
            return "";
        }
    }
}

if (!function_exists('validateInputs()')) {
    function validateInputs($inputs) { 
        $failedInputs = array();

        //associative array/object - [name of input => filter_var()] e.g. "id" : sanitizeInput($this->input->post("id"), FILTER_SANITIZE_NUMBER_INT)
        foreach($inputs as $key => $property) {
			if (!$property && $property === false) {
                array_push($failedInputs, $key);
			}
		}

        if (count($failedInputs) > 0) {
            $message = "invalid inputs: ";
            foreach($failedInputs as $failed) {
                $message .= ($failed . ", ");
            }

            return array("valid" => false, "failed" => $message);
        } else {
            return array("valid" => true, "data" => $inputs);
        }
    }
}

// https://www.php.net/manual/en/filter.filters.validate.php
// https://www.php.net/manual/en/filter.filters.sanitize.php
if (!function_exists('sanitizeInput()')) {
    function sanitizeInput($input, $filter, $flags=null, $options=null) { 
        switch ($filter) {
            case FILTER_VALIDATE_INT:
                $input = filter_var($input, FILTER_VALIDATE_INT, isset($options) ? $options : ["options" => ["min_range" => 0], "flags" => FILTER_NULL_ON_FAILURE]);
                break;
            case FILTER_SANITIZE_NUMBER_FLOAT:
                $input = filter_var($input, FILTER_SANITIZE_NUMBER_FLOAT, $flags);
                break;
            case FILTER_VALIDATE_FLOAT:
                $input = filter_var($input, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE | $flags);
                break;
            // supports "yes", "no", etc.
            case FILTER_VALIDATE_BOOLEAN:
                $input = filter_var($input, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                break;
            // likely need to change as its not a catchall
            case FILTER_VALIDATE_URL:
                // validate url host
                $accepted_hosts = [$_SERVER['HTTP_HOST'], "dg7ltaqbp10ai.cloudfront.net", "winwinlabs.s3.us-east-2.amazonaws.com"];
                
                $url = parse_url($input);
                if ($url && isset($url["scheme"]) && $url["scheme"] == "https" && isset($url["host"]) && in_array($url["host"], $accepted_hosts)) {
                    $input = filter_var(urldecode($input), FILTER_SANITIZE_SPECIAL_CHARS, FILTER_NULL_ON_FAILURE | $flags);
                } else {
                    // if input does not have schema or host, likely a slug
                    if (isset($url) && isset($url["path"])) {
                        $input = filter_var(urldecode($input), FILTER_SANITIZE_SPECIAL_CHARS, FILTER_NULL_ON_FAILURE | $flags);
                    } else {
                        $input = false;
                    } 
                }
                break;
            // likely need to change to account for more than RFC 822 - will do for now
            case FILTER_VALIDATE_EMAIL:
                $input = filter_var($input, FILTER_VALIDATE_EMAIL);
                break;
            // filter is deprecated, but htmlspecialchars encodes html instead of removing
            case FILTER_SANITIZE_STRING:
                if ($input === "0" || $input) {
                    $input = filter_var($input, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | $flags);
                } else {
                    $input = "";
                }
                
                break;
            case FILTER_SANITIZE_FULL_SPECIAL_CHARS:
                $config = HTMLPurifier_Config::createDefault();
                $config->set("Attr.AllowedFrameTargets", ["_self", "_blank"]);
                
                $purifier = new HTMLPurifier($config);
                $input = $purifier->purify(html_entity_decode($input, ENT_QUOTES | ENT_XML1, 'UTF-8'));
                break;
        }

        return $input;
    }
}

if (!function_exists('sanitizeInputArray()')) {
    function sanitizeInputArray($array, $filter, $flags=null) { 
        if (!isset($array)) return null;
        
        foreach ($array as $key => $val) {
            switch ($filter) {
                case FILTER_VALIDATE_FLOAT:
                case FILTER_VALIDATE_INT:
                case FILTER_VALIDATE_BOOLEAN:
                // likely need to change as its not a catchall - verifies against RFC 822
                case FILTER_VALIDATE_EMAIL:
                    $result = sanitizeInput($val, $filter);
                    break;
                // likely need to change as its not a catchall
                case FILTER_VALIDATE_URL:
                // filter is deprecated, but htmlspecialchars encodes html instead of removing
                case FILTER_SANITIZE_STRING:
                    $result = sanitizeInput($val, $filter, $flags);
                    break;
            }

            if (!isset($result) || !$result) {
                unset($array[$key]);
            } else {
                $array[$key] = $result;
            }
        }

        return $array;
    }
}

if (!function_exists('sendNotification()')) {
    function sendNotification($noti_pref, $for_user, $action_user, $notes, $type, $action, $game_id, $charity_id, $date=null) {
        $CI = &get_instance();
        $CI->load->model('notification_model');

        $preferences = get_noti_preferences($for_user);
        if ($preferences->{$noti_pref}) {
            $CI->notification_model->insertNotification($for_user, $action_user, $notes, $type, $action, $game_id, $charity_id, $date);
        }
	}
}

if (!function_exists('sendEmail()')) {
    function sendEmail($email_pref, $user_id, $subject, $body) {
        $CI = &get_instance();
        
        $preferences = get_email_preferences($user_id);
        if ($preferences->{$email_pref}) {
            $email = getprofile($user_id)->email;
            Email::index($email, $subject, $body);
        }
	}
}

if (!function_exists('sendNotificationAndEmail()')) {
    function sendNotificationAndEmail($pref, $for_user, $action_user, $notes, $type, $action, $game_id, $charity_id, $subject, $body, $date=null) {
        sendNotification($pref, $for_user, $action_user, $notes, $type, $action, $game_id, $charity_id, $date);
        sendEmail($pref, $for_user, $subject, $body);
	}
}

if (!function_exists('getFundraiseIcon()')) {
    function getFundraiseIcon($value = '') {
        switch ($value) {
            case "charity":
                return "fas fa-hand-holding-heart";
                break;
            case "project":
                return "fas fa-lightbulb";
                break;
            default:
                return "fa fa-globe";
                break;
        }
    }
}

if (!function_exists('getNotificationUnseenCount()')) {
    function getNotificationUnseenCount() {
		$CI = &get_instance();
        $CI->load->model('notification_model');

		return $CI->notification_model->getUnseenCount($CI->session->userdata("user_id"));;
	}
}

if (!function_exists('clean_special_char()')) {
    function clean_special_char($string) {
		$string = str_replace(' ', '-', $string);
		return preg_replace('/[^A-Za-z0-9\-]/', '', $string);
	}
}

if (!function_exists('clamp()')) {
    function clamp($num, $min, $max) {
        return min(max($num, $min), $max);
    }
}

// check for valid referral
if (!function_exists('isReferral()')) {
    function isReferral() {
        $CI = &get_instance();
        $CI->load->model('referral_model');

        $isExistReferral = $CI->referral_model->checkIfReferralExists($_SESSION["referral"]);
        if (isset($_SESSION["referral"]) && !empty($CI->input->cookie('referral', TRUE)) && $_SESSION["referral"] == $CI->input->cookie('referral', TRUE)) {
            if ($isExistReferral) {
                return true;
            } else {
                return false;
            }
        } else {
            return null;
        }
    }
}

if (!function_exists('getReferralForUserTransaction()')) {
    function getReferralForUserTransaction() {
        $CI = &get_instance();
        $CI->load->model('referral_model');

        $referralId = $CI->referral_model->getReferralIdForUserTransaction($_SESSION["referral"]);
        if ($_SESSION["referral"] == $CI->input->cookie('referral', TRUE) &&  $referralId) {
            return $referralId;
        }
    }
}

if (!function_exists('create_range()')) {
    function create_range($values, $decimal=2) {
		# returns range given an array of values in the format [1-5, 5-10, 10-15]

		$min = null; $max = null;
		foreach ($values as $value_filter) {
			$range = array_map(function($val) {
				return (isset($val) && $val != "") ? floatval(number_format(rtrim((float)$val), 2, '.', '')) : "";
			}, explode('-', $value_filter));

			if (count($range) > 1) {
				if (isset($range[0])) {
					$min = (!isset($min) || $range[0] < $min) ? $range[0] : $min;
				}

				if (isset($range[1])) {
					$max = (!isset($max) || $range[1] > $max) ? $range[1] : $max;
				}
			} else {
				$max = (!isset($max) || $value_filter > $max) ? $value_filter : $max;
			}
		}

        if (!isset($min)) {
            $min = "";
        }

        if (!isset($max)) {
            $max = "";
        }

        return implode("-", array_filter([$min, $max], function($v) {
            return isset($v);
        }));
	}
}

if (!function_exists('calculate_game_finances()')) {
    function calculate_game_finances($game) {
        # distributes earnings for each party as evenly as possible
        function distribute($total, $parties) {
            # distributes a total between x number of parties as evenly as possible
            # e.g. 21 /=> 7, 7, 7 - 10 / 3 => 3.33, 3.33, 3.34
            $divisions = array_reduce($parties, function($carry, $item) {
                return $carry += ($item ? 1 : 0);
            }, 0);
    
            $m = $total * 100;
            $n = $m % $divisions;
            $v = floor( $m / $divisions ) / 100;
            $w = floor( $m / $divisions + 1 ) / 100;
    
            $index = 0; $earnings = array();
            foreach ($parties as $party => $include) {
                $earning = ($include) ? ($index < $n ? $w: $v) : 0;
                $earnings[$party] = $earning;
                $index++;
            }
    
            return $earnings;
        }
        
        $calculated_finances = array();

        $beneficiaries = array(
            "creator_beneficiary" => true,
            "winner_beneficiary" => $game->donationOption != 1
        );

        $parties = array(
            "creator" => true,
            "winner" => $game->credit_type != "prize",
            "wwl" => $game->selected_fundraiser->slug != "winwinlabs-fundraising-system"
        );

        $beneficiary_percentage = $game->beneficiary_percentage;

        # calculate earnings for each beneficiary
        $beneficiary_total = $game->fundraise_value * ($beneficiary_percentage / 100);
        $beneficiary_earnings = distribute($beneficiary_total, $beneficiaries);

        foreach ($beneficiaries as $beneficiary => $include) {
            if ($include) {
                $calculated_finances[$beneficiary] = $beneficiary_earnings[$beneficiary];
            }
        }

        # calculate earnings for each remaining party (based on remainder)
        $beneficiary_sum = array_reduce($beneficiary_earnings, function($carry, $item) {
            return $carry += $item;
        });
        $remaining_total = round(($game->fundraise_value - $beneficiary_sum), 2);
        $party_earnings = distribute($remaining_total, $parties);

        foreach ($parties as $party => $include) {
            if ($include) {
                $calculated_finances[$party] = $party_earnings[$party];
            }
        }

        $calculated_finances["winner_count"] = $game->winner_count;
        $calculated_finances["prize_value"] = $game->value_of_the_game;

        return $calculated_finances;
	}
}

if (!function_exists('getLogoImage()')) {
    function getLogoImage() {
        $image = "https://dg7ltaqbp10ai.cloudfront.net/fit-in/200x35/WinWinLabs_Logo.png";

        return $image;
    }
}