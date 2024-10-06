<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Create_game_model extends CI_Model {

    // common modal for all games
    public function create_game() {
        $user_id          = $this->session->userdata('user_id');
        $is_admin = isAdmin();

        $game_id = sanitizeInput($this->input->post('game_id'), FILTER_VALIDATE_INT);

        if ($game_id > 0) {
            $game = $this->get_game($game_id);

            // must be creator or be an admin to edit
            if ($user_id != $game->user_id && !$is_admin) {
                return array("status" => "failed", "msg" => "Only creators can edit their games.");
            }

            // game must be drafted to edit, unless admin is editing
            $status = getGameState($game);
            if ($status != "drafted" && !$is_admin) {
                return array("status" => "failed", "msg" => "Only drafted games can be edited.");
            }
        }

        $reward_type = sanitizeInput($this->input->post('credit_prize', true), FILTER_SANITIZE_STRING);
        $cost_to_play = sanitizeInput($this->input->post('costtoplay', true), FILTER_VALIDATE_FLOAT);
        $winner_count = sanitizeInput($this->input->post('winner_count', true), FILTER_VALIDATE_INT);
        $reward_value = sanitizeInput($this->input->post('prize_value', true), FILTER_VALIDATE_FLOAT);
        $fundaiser_slug = sanitizeInput($this->input->post('selected_fundraiser'), FILTER_VALIDATE_URL);

        $game_state = array(
            "reward_type" => $reward_type,
            "is_winwinlabs" => $fundaiser_slug === "winwinlabs-fundraising-system",
            "is_prize" => ($reward_type === "prize") ? true : false,
            "donation_option" => sanitizeInput($this->input->post('gameDonationOption'), FILTER_VALIDATE_INT)
        );

        $input_finances = array(
            "winner_count" => $winner_count,
            "cost_to_play" => $cost_to_play,
            "fundraise_goal" => sanitizeInput($this->input->post('gameFundraiserGoal'), FILTER_VALIDATE_FLOAT),
            "beneficiary_percent" => sanitizeInput($this->input->post('gameFundraiserPercent'), FILTER_VALIDATE_FLOAT),
            "reward_value" => $reward_value
        );

        $game_finances = $this->calculate_finances($game_state, $input_finances);

        $type = sanitizeInput($this->input->post('type', true), FILTER_SANITIZE_STRING);
        $image1 = sanitizeInput($this->input->post('gameInfoImage', true), FILTER_VALIDATE_URL);
        $image2 = sanitizeInput($this->input->post('puzzleImage', true), FILTER_VALIDATE_URL);

        $optionsCheckbox = sanitizeInput($this->input->post('optionsCheckboxes', true), FILTER_SANITIZE_STRING);
        if ($optionsCheckbox == "timelimit") {
            $time_limit = 1;
            $steps      = 0;
        } else{
            $time_limit = 0;
            $steps      = 1;
        }
        
        $min_credit = sanitizeInput($this->input->post('min_credit'), FILTER_VALIDATE_FLOAT);

        $days = sanitizeInput($this->input->post('days'), FILTER_VALIDATE_INT);
        $hour = sanitizeInput($this->input->post('hours'), FILTER_VALIDATE_INT);
        $min = sanitizeInput($this->input->post('min'), FILTER_VALIDATE_INT);

        $gamestage = sanitizeInput($this->input->post('gamestage', true), FILTER_SANITIZE_STRING);
        $gamedepends = sanitizeInput($this->input->post('gamedepends'), FILTER_VALIDATE_INT);
        if ($gamedepends == 1) {
            $min_credit = 0;

            $days = (isset($days)) ? $days : 0;
            $hour = (isset($hour)) ? $hour : 0;
            $min = (isset($min)) ? $min : 0;
        } else if ($gamedepends == 2) {
            $min_credit = (isset($min_credit)) ? $min_credit : 0;

            $days = 0;
            $hour = 0;
            $min  = 0;
        } else if ($gamedepends == 3) {
            $min_credit = (isset($min_credit)) ? $min_credit : 0;

            $days = (isset($days)) ? $days : 0;
            $hour = (isset($hour)) ? $hour : 0;
            $min = (isset($min)) ? $min : 0;
        } else if ($gamedepends == 4) {
            $min_credit = (isset($min_credit)) ? $min_credit : 0;

            $end = strtotime(sanitizeInput($this->input->post('endDate', true), FILTER_SANITIZE_STRING));
            $endDate = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', $end));

            if ($gamestage == "Publish Game") {
                $pub = strtotime(sanitizeInput($this->input->post('utc_publish_date', true), FILTER_SANITIZE_STRING));
                if ($end < $pub || $end < strtotime('now') || $pub < strtotime('now')) { //dates spaced correctly in time - end date after pub date, etc.
                    if ($end < $pub) {
                        $msg = "Invalid End Date, must be after Publish Date.";
                    } else if ($end < strtotime('now')) { 
                        $msg = "Invalid End Date, must be in the future.";
                    } else {
                        $msg = "Invalid Publish Date, must be in the future.";
                    }
                    return array("status" => "failed", "msg" => $msg);
                }
                $publishDate = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', $pub));
            } else {
                $publishDate = DateTime::createFromFormat('Y-m-d H:i:s', gmdate("Y-m-d H:i:s"));
            }

            $diff = $publishDate->diff($endDate);
            
            $days = (int)$diff->format("%a");
            $hour = (int)$diff->format("%h");
            $min = (int)$diff->format("%i");
        }

        if ($type == 'quiz' || $type == 'challenge') {
            $g_type         = 3;
            $Quiz_rules     = $optionsCheckbox;
            $time_limit     = 0;
            $steps          = 0;
            $Level          = 0;
            $game_tile_goal = 0;
        } else if ($type == '2048') {
            $g_type      = 2;
            $Level          = 0;
            $game_tile_goal = sanitizeInput($this->input->post('game_tile_goal'), FILTER_VALIDATE_INT);
        } else if ($type == 'minecraft') {
            $g_type = sanitizeInput($this->input->post('gamemode'), FILTER_VALIDATE_INT);
            $g_config = sanitizeInput($this->input->post('gameconfig'), FILTER_VALIDATE_INT);

            $this->db->from("minecraft_gameinfo");
            $this->db->where(array("id" => $g_config, "gametype" => $g_type, "approved" => 1));

            $this->db->group_start();
                $this->db->where("user_id", $user_id);
                $this->db->or_where("user_id IS NULL", NULL);
            $this->db->group_end();
            $gameconfig = $this->db->get()->row();

            if (count($gameconfig) <= 0) {
                return array("status" => "failed", "msg" => "Invalid game configuration.");
            }
        } else {
            $g_type = 1;
            $Quiz_rules     = '';
            $Level          = sanitizeInput($this->input->post('gamedifficulty'), FILTER_VALIDATE_INT);
            $game_tile_goal = 0;
        }
        
        $game_tags = sanitizeInput($this->input->post('gametags', true), FILTER_SANITIZE_STRING);

        $title = sanitizeInput($this->input->post('gametitle', true), FILTER_SANITIZE_STRING);
        $exist = $this->db->where('name2', $title)->get('game')->num_rows();
        $no    = '';
        if ($exist > 0) {
            $no = ' - ' . ($exist < 10) ? 0 . ($exist + 1) : ($exist + 1);
        }

        //  1. is base url equal to demo url of database
        // 2. else set isProd = 1
        $site_config = $this->db->where('id', 1)->get('tbl_siteconfiguration')->row();
        if ( $site_config->demo_url == 'https://archive.winwinlabs.com/') {
            $isProd = 0;
        } else {
            $isProd = 1;
        }

        $slug = str_replace(' ', '-', ucwords(clean_special_char(trim($title))));
        if ($game_id > 0) {
            $exist = $this->db->where('name', $title)->where('slug !=', '')->where('id !=', $game_id)->from('game')->count_all_results();
        } else {
            $exist = $this->db->where('name', $title)->or_where('slug LIKE', $slug)->where('slug !=', '')->from('game')->count_all_results();
        }

        if (isset($exist) and $exist > 0) {
            $slug = (str_replace(' ', '-', ucwords(clean_special_char(trim($title))))).'-'.$exist;
        }

        $quiz_id = sanitizeInput($this->input->post('quiz'), FILTER_VALIDATE_INT);
        if ($g_type == 3) {
            $approved = $this->getQuizById($quiz_id)->status == 1;
            if (!$approved && $gamestage != "Draft Game") {
                return array("status" => "failed", "msg" => "Quiz not eligible for use in non-drafted games.");
            }
        }

        $publish_status = "";
        $utc_publish_date = validateDateString(sanitizeInput($this->input->post('utc_publish_date', true), FILTER_SANITIZE_STRING));
        if ($gamestage == "Publish Game") {
            if (!empty($utc_publish_date)) {
                $utc_publish_date = date_create($utc_publish_date);
                $utc_publish_date = date_format($utc_publish_date, 'Y-m-d H:i:s');
                $publish_status = 'Yes';
            } else {
                return array("status" => "failed", "msg" => "Invalid publish date.");
            }
        } else {
            if ($gamestage == 'Live') {
                $utc_publish_date   = gmdate("Y-m-d H:i:s");
                $publish_status = 'Live';
            } else {
                $utc_publish_date   = null;
                $publish_status = 'No';
            }
        }

        # if beneficiary is not approved, override publish status to draft
        if ($fundaiser_slug) {
            $user_charity = $this->db->get_where('charity', ['slug' => $fundaiser_slug])->row();
            if (isset($user_charity) && !empty($user_charity)) {
                if ($user_charity->approved !== 'Yes') {
                    $utc_publish_date = null;
                    $publish_status = 'No';
                }
            }
        }

        $gamedescription = sanitizeInput($this->input->post('gamedescription'), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $prizetitle = sanitizeInput($this->input->post('prizetitle', true), FILTER_SANITIZE_STRING);
        $prizedescription = sanitizeInput($this->input->post('prizedescription'), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $prize_type = sanitizeInput($this->input->post('prize_type', true), FILTER_SANITIZE_STRING);
        $prize_specification = sanitizeInput($this->input->post('prize_specification'), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $gameEndInfoDescription = sanitizeInput($this->input->post('gameEndInfoDescription'), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $data = array(
            'isProd' => $isProd,
            'name'                => $title,
            'slug'                => $slug,
            'name2'               => $title,

            'credit_cost'         => ($reward_type == "free") ? 0 : $cost_to_play,
            'winner_count'        => $winner_count,
            'value_of_the_game'   => ($reward_type == "free") ? 0 : round($reward_value, 2),

            'time_limit'          => $time_limit,
            'game_desc'           => $gamedescription,
            'user_id'             => $this->session->userdata('user_id'),

            'Type'                => $g_type,
            'Steps'               => $steps,
            'Level'               => isset($Level) ? $Level : 0,
            'game_tile_goal'      => isset($game_tile_goal) ? $game_tile_goal : 0,
            'winner_option'       => $gamedepends,

            'Image'               => $image2,
            'Game_Image'          => $image1,

            'Publish'             => $publish_status,
            'Publish_Date'        => $utc_publish_date,
            
            'End_Date'            => (isset($endDate)) ? $endDate->format("Y-m-d H:i:s") : null,
            'End_Day'             => (int)$days,
            'End_Hour'            => (int)$hour,
            'End_Minute'          => (int)$min,

            'Min_credits'         => $min_credit,

            'Quiz_rules'          => isset($Quiz_rules) ? $Quiz_rules : null,
            'quiz_id'             => $quiz_id,
            
            'credit_type'         => $reward_type,
            'prize_title'         => $prizetitle,
            'prize_description'   => $prizedescription,
            'prize_type'          => $prize_type,
            'prize_specification' => $prize_specification,
            
            'gameEndDescription' => ($gameEndInfoDescription != '') ? $gameEndInfoDescription : '',
            'donationOption'     => ($game_state["donation_option"] != '') ? $game_state["donation_option"] : 1,
        );

        if ($game_id > 0) {
            if ($is_admin) {
                unset($data['user_id']);
            }

            $this->db->where('id', $game_id);
            $gameChanges = $this->db->update('game', $data);
            $edit = true;
        } else {
            $gameChanges = $this->db->insert('game', $data);
            $game_id = $this->db->insert_id();
            $edit = false;
        }

        if ($gameChanges) {
            $this->create_game_tags($game, $game_tags);
            
            if ($fundaiser_slug) {
                $user_charity = $this->db->get_where('charity', ['slug' => $fundaiser_slug])->row();
                $charity_data = array(
                    'charity_id' => $user_charity->id,
                    'user_id'    => $user_id,
                    'game_id'    => $game_id,
                );

                $isFundraiserExists = $this->getGameFundraiser($game_id);

                if (!empty($isFundraiserExists)) {
                    $this->db->where('game_id', $game_id);
                    $this->db->where('user_id', $user_id);
                    $this->db->update('user_game_charity', $charity_data);
                } else {
                    $this->db->insert('user_game_charity', $charity_data);
                }
            }

            //minecraft game config association
            if (isset($g_config)) {
                $mcdata = array(
                    "game_id" => $game_id,
                    "info_id" => $g_config
                );

                if ($edit) {
                    $this->db->where('game_id', $game_id);
                    $this->db->update('minecraft_games', $mcdata);
                } else {
                    $this->db->insert('minecraft_games', $mcdata);
                }
            }

            $notification_data = array(
                'for_user' => $this->session->userdata('user_id'),
                'action_user' => $this->session->userdata('user_id'),
                'Notes'   => (($edit) ? "You edited your game: " : "You created a new game: ") . $title . ".",
                'type' => "game",
                'action' => ($edit) ? "edit" : "create",
                'game_id' => $game_id,
                'charity_id' => $user_charity->id,
                'Date' => date("Y-m-d H:i:s")
            );

            if ($edit) {
                $notification_data["Notes"] = "You edited a game: " . $title . ".";
                $this->db->insert('notification', $notification_data);
            } else {
                $this->db->insert('notification', $notification_data);
            }

            if (!$edit) {
                //default rating by creator
                $rating_data = array(
                    'user_id' => $this->session->userdata('user_id'),
                    'game_id' => $game_id,
                    'rating'  => '5',
                );
                $this->db->insert('user_game_rate', $rating_data);
            }

            //Store puzzle Img
            $prize_images = sanitizeInputArray($this->input->post('gamePrizeOtherImages',true), FILTER_VALIDATE_URL);

            $this->db->where("main_image", 0)->where("game_id", $game_id)->delete("prize");
            if (!empty($prize_images)) {
                for ($i = 0; $i < sizeof($prize_images); $i++) {
                    $prize = array(
                        'prize_image' => $prize_images[$i],
                        'game_id'     => $game_id,
                        'main_image'  => 0,
                    );

                    $this->db->insert('prize', $prize);
                }
            }

            $db_prize_main_image = sanitizeInput($this->input->post('gamePrizeImage', true), FILTER_VALIDATE_URL);
            if (!empty($db_prize_main_image)) {
                $isExistPrizeMainImage = $this->db->where("main_image", 1)->where("game_id", $game_id)->get('prize')->row();
                if (!empty($isExistPrizeMainImage)) {
                    $this->db->where("main_image", 1)
                    ->where("game_id", $game_id)
                    ->update('prize', ['prize_image' => $db_prize_main_image]);
                } else {
                    $prize = array(
                        'prize_image' => $db_prize_main_image,
                        'game_id'     => $game_id,
                        'main_image'  => 1,
                    );
                    $this->db->insert('prize', $prize);
                }
            }

            $game_finances["game_id"] = $game_id;
            $game_credit = $this->db->where('game_id', $game_id)->get('game_credit')->row();
            if (isset($game_credit) && $game_credit->id) {
                $this->db->where('id', $game_credit->id)->update('game_credit', $game_finances);
            } else {
                $this->db->insert('game_credit', $game_finances);
            }

            if ($publish_status != 'Live') {
                $gamestage = "published";
                if ($publish_status == "No") {
                    $gamestage = "drafted";
                }
            } else {
                $gamestage = 'live';
            }

            return array('status' => 'success', 'gamestage' => $gamestage, 'game_id' => $game_id, 'slug' => $data['slug']);
        } else {
            return array("status" => "failed", "msg" => (($edit) ? "We ran into an error updating your game." : "We ran into an error creating your game."));
        }
    }

    private function calculate_finances($game_state, $finances) {
        $is_free = $game_state["reward_type"] == "free";
        $winner_count = (!$game_state["is_prize"]) ? 1 : $finances["winner_count"];
        $fundraise_goal = $finances["fundraise_goal"];
        $beneficiary_percent = clamp($finances["beneficiary_percent"], 10, 100);
        $reward_value = max($finances["reward_value"], 1);
        $cost_to_play = max($finances["cost_to_play"], .10);

        $remaining_percent = 100 - $beneficiary_percent;
        $parties = 3;
        if ($game_state["is_winwinlabs"]) {
            $parties -= 1;
            if ($game_state["is_prize"]) {
                $parties -= 1;
            }
        }

        // percents are approximate
        $percent_per_party = round(($remaining_percent / $parties), 3);
        $creator_percent = $percent_per_party;
        $winner_percent = $game_state["is_prize"] ? 0 : $percent_per_party;
        $wwl_percent = $game_state["is_winwinlabs"] ? 0 : $percent_per_party;

        return array(
            'beneficiary_percentage' => !$is_free ? $beneficiary_percent : 0,
            'wwl_percentage' => !$is_free ? $wwl_percent : 0,
            'creator_percentage' => !$is_free ? $creator_percent : 0,
            'winner_percentage' => !$is_free ? $winner_percent : 0,
            'fundraise_value' => !$is_free ? $fundraise_goal : 0,
        );
    }

    private function create_game_tags($game, $game_tags) {
        // creates new game tags or updates associated game tags (add/remove)

        $game_id = $game->id;

        if (!empty($game_tags)) {
            // get game's associated tags (id, name) and create assoc array of data [name => [id, name], ..]
            $associated_game_tags = $this->get_game_tags($game->slug);
            $associated_game_tags = array_reduce($associated_game_tags, function ($result, $tag) {
                $result[$tag["tag_name"]] = $tag;
                return $result;
            }, array());

            // create array of game's associated tag names
            $associated_game_tags_names = array_keys($associated_game_tags);

            // get difference between existing and new tags
            $new_gametags = explode(',', $game_tags);
            $game_tags_diff = array_merge( array_diff($new_gametags, $associated_game_tags_names), array_diff($associated_game_tags_names, $new_gametags));

            // get tags that already exist in db
            $existing_game_tags = $this->get_existing_game_tags($new_gametags);
            $existing_game_tags = array_reduce($existing_game_tags, function ($result, $tag) {
                $result[$tag["tag_name"]] = $tag["id"];
                return $result;
            }, array());

            // for the tag difference, determine if they need to be added or removed
            $add_tags = array(); $remove_tags = array();
            foreach ($game_tags_diff as $tag) {
                if (!isset($associated_game_tags[$tag])) {
                    // add tag if it is not already associated with game

                    if (!isset($existing_game_tags[$tag])) {
                        // create new gametag if does not exist

                        $tag_data = array(
                            'tag_name' => $tag,
                            'status'   => 1,
                        );
                        $this->db->insert('gametags', $tag_data);
                        $tag_id = $this->db->insert_id();
                    } else {
                        $tag_id = $existing_game_tags[$tag];
                    }
                    
                    array_push($add_tags, array("game_id" => $game_id, "tag_id" => $tag_id));
                } else {
                    if (!isset($new_gametags[$tag])) {
                        // remove tag if it does not exist in new game tag list
                        array_push($remove_tags, $associated_game_tags[$tag]["tag_id"]);
                    }
                }
            }

            // delete tags
            if ($remove_tags) {
                $this->db->where("game_id", $game_id);
                $this->db->where_in("tag_id", $remove_tags);
                $delete = $this->db->delete("game_tags");
            } else {
                $delete = false;
            }

            // insert new tags
            if ($add_tags) {
                $insert = $this->db->insert_batch("game_tags", $add_tags);
            } else {
                $insert = false;
            }

            return $insert;
        }
    }

    private function get_game_tags($slug) {
		$this->db->select("gt.tag_id, gt1.tag_name");
		$this->db->from("game");
		$this->db->join("game_tags as gt", "game.id = gt.game_id", "left");
		$this->db->join("gametags as gt1", "gt.tag_id = gt1.id");
		$this->db->group_by("gt1.id");

        $this->db->where("game.slug", $slug);

		return $this->db->get()->result_array();
	}

    private function get_existing_game_tags($game_tags) {
        $this->db->select("id, tag_name");
        $this->db->from("gametags");
        $this->db->where_in("tag_name", $game_tags);
        return $this->db->get()->result_array();
    }

    // duplicate game function
    public function duplicateGame($id) {
        $id = $this->input->post('id');

		$this->db->query('
		INSERT INTO `game`(`isProd`, `name`, `slug`, `name2`, `gametags`, `credit_cost`, `winner_count`, 
        `value_of_the_game`, `time_limit`, `game_desc`, `game_option`, `user_id`, `Type`, `Steps`, `Level`, 
        `game_tile_goal`, `winner_option`, `Image`, `Game_Image`, `Publish`, `Publish_Date`, `Status`, `processed`, 
        `End_Date`, `End_Day`, `End_Hour`, `End_Minute`, `Min_credits`, `Quiz_rules`, `quiz_id`, `credit_type`, 
        `prize_title`, `prize_description`, `prize_type`, `prize_specification`, `created_at`, `updated_at`, `active`, 
        `review_status`, `gameEndDescription`, `donationOption`) 

        SELECT `isProd`, `name`, `slug`, `name2`, `gametags`, `credit_cost`, `winner_count`, `value_of_the_game`, 
        `time_limit`, `game_desc`, `game_option`, `user_id`, `Type`, `Steps`, `Level`, `game_tile_goal`, `winner_option`, 
        `Image`, `Game_Image`, `Publish`, `Publish_Date` , `Status`, `processed`, `End_Date`, `End_Day`, `End_Hour`, `End_Minute`, 
        `Min_credits`, `Quiz_rules`, `quiz_id`, `credit_type`, `prize_title`, `prize_description`, `prize_type`, 
        `prize_specification`, `created_at`, `updated_at`, `active`, `review_status`, `gameEndDescription`, 
        `donationOption` FROM `game` 
		WHERE id='.$id);

		$insert_id = $this->db->insert_id();

		$this->db->query('INSERT INTO `prize`(`game_id`, `prize_image`, `main_image`) 
		SELECT '.$insert_id.', `prize_image`, `main_image` FROM `prize` WHERE game_id = '.$id);

		$this->db->query('INSERT INTO `game_credit`(`game_id`, `fundraise_value`, `beneficiary_percentage`, `wwl_percentage`, `creator_percentage`, `winner_percentage`) 
		SELECT '.$insert_id.', `fundraise_value`, `beneficiary_percentage`, `wwl_percentage`, `creator_percentage`, `winner_percentage` FROM `game_credit` 
		WHERE game_id = '.$id);

        $this->db->query('INSERT INTO `user_game_charity`(`charity_id`, `user_id`, `game_id`) 
		SELECT `charity_id`, `user_id`, '.$insert_id.' FROM `user_game_charity` WHERE game_id = '.$id);

		$title = $this->db->select('name')->where('id', $insert_id)->get('game')->row()->name;
        if ($insert_id > 0) {
            $exist = $this->db->where('name', $title)->where('slug !=', '')->where('id !=', $insert_id)->from('game')->count_all_results();
        } else {
			$exist = $this->db->where('name', $title)->where('slug !=', '')->from('game')->count_all_results();
        }
		
        if (isset($exist) and $exist > 0) {
			$title = (str_replace(' ', '-', ucwords(clean_special_char(trim($title))))).'-'.$exist;
        } else {
			$title = (str_replace(' ', '-', ucwords(clean_special_char(trim($title)))));
        }
		
		$this->db->where('id', $insert_id)->update('game', ['slug' => $title]);
	
        $slug = $this->db->select('slug')->where('id', $insert_id)->get('game')->row()->slug;
    
        return $slug;
	}

    public function getQuizById($id) {
        return $this->db->where('id', $id)->get('quiz')->row();
    }

    public function getGameFundraiser($game_id) {
        return $this->db->where('game_id', $game_id)->order_by('id', 'DESC')->limit(1)->get('user_game_charity')->row();
    }

    public function getGamemodes($type) {
        $this->db->select("gametype.id, gametype.name");
        $this->db->from("gametype");
        $this->db->join("gametype as gt", "gametype.parent = gt.id");
        $this->db->where("gt.name", $type);

        return $this->db->get()->result();
    }

    public function getGameConfig($game_id=null) {
        $this->db->select("mgi.*");
        $this->db->from("minecraft_games as mg");
        $this->db->join("minecraft_gameinfo as mgi", "mg.info_id = mgi.id");
        $this->db->where("mg.game_id", $game_id);

        return $this->db->get()->result();
    }

    public function getGameConfigs($user_id, $type) {
        $this->db->select("mgi.id, mgi.user_id, mgi.name, mgi.gametype, mgi.arena as arena_id, ma.arena_name as arena, ma.min_x, ma.max_x, ma.min_y, ma.max_y, ma.min_z, ma.max_z, mgi.play_type, mgi.game_base, mgi.timelimit, mgi.point_value, mgi.hunger, mgi.regen, mgi.looting, mgi.trial, mgi.approved");
        $this->db->from("minecraft_gameinfo as mgi");
        $this->db->join("minecraft_arenas as ma", "mgi.arena=ma.id");
        $this->db->where(array("mgi.gametype" => $type, "mgi.approved" => 1));

        $this->db->group_start();
            $this->db->where("mgi.user_id", $user_id);
            $this->db->or_where("mgi.user_id IS NULL", NULL);
        $this->db->group_end();

        return $this->db->get()->result();
    }

    public function getKits($user_id, $info_id, $kit_id=null) {
        $this->db->select("mgik.info_id, mk.*");
        $this->db->from("minecraft_gameinfo_kits as mgik");
        $this->db->join("minecraft_kits as mk", "mk.id=mgik.kit_id");
        $this->db->join("minecraft_gameinfo as mgi", "mgi.id = mgik.info_id");
        $this->db->where("mgik.info_id", $info_id);

        if (isset($kit_id)) {
            $this->db->where("mk.id", $kit_id);
        }

        $this->db->group_start();
            $this->db->where("mk.user_id", $user_id);
            $this->db->or_where("mk.user_id IS NULL", NULL);
        $this->db->group_end();

        $result = $this->db->get()->result();

        foreach ($result as $key => $kit) {
            $amounts = $this->getMinecraftItemAmount($kit->id);

            foreach ($kit as $key1 => $slot) {
                if (isset($slot) && (strpos($key1, "hotbar") !== false || strpos($key1, "offhand") !== false || strpos($key1, "armor") !== false || strpos($key1, "key") !== false)) {
                    $item = $this->getMinecraftItems(null, $slot);
                    if (strpos($key1, "key") === false) {
                        if (strpos($key1, "armor") === false && isset($amounts)) {
                            if (isset($item)) {
                                $item->amount = $amounts->{((str_replace("_", "", $key1)) . "_amount")};
                            }
                        } else {
                            if (!isset($item)) { $item = new stdClass(); }
                            $item->amount = 1;
                        }
                    }

                    $kit->{$key1} = $item;
                } else {
                    $kit->{$key1} = $slot;
                }
            }
        }

        return $result;
    }

    public function getRules($user_id, $info_id, $rule_id=null) {
        $this->db->select("mgir.info_id, mr.id as rule_id, mr.user_id, mr.ruletype_id, mrt.rule as rule_type, mr.name as rule_name, mr.value, mr.wave_D, mr.wave_Int, mr.location_x, mr.location_y, mr.location_z, mr.checkpoint, mr.judge_type, la.username as judge_player, mr.judge_description, mr.item_type, mo1.name as item_name, mr.mob_type, mo.name as mob_name, mr.perimeter, mr.starting, ");
        $this->db->from("minecraft_gameinfo_rules as mgir");
        $this->db->join("minecraft_rules as mr", "mgir.rule_id=mr.id");
        $this->db->join("minecraft_ruletypes as mrt", "mr.ruletype_id=mrt.id");
        $this->db->join("linked_accounts as la", "mr.judge_player=la.user_id", "left");

        $this->db->join("minecraft_objects as mo", "mo.id=mr.mob_type", "left");
        $this->db->join("minecraft_objects as mo1", "mo1.id=mr.item_type", "left");

        $this->db->where("mgir.info_id", $info_id);

        if (isset($rule_id)) {
            $this->db->where("mr.id", $rule_id);
        }

        $this->db->group_start();
            $this->db->where("mr.user_id", $user_id);
            $this->db->or_where("mr.user_id IS NULL", NULL);
        $this->db->group_end();

        $this->db->order_by("mr.ruletype_id", "asc");

        $result = $this->db->get();

        return (isset($rule_id)) ? $result->row() : $result->result();
    }

    public function getRuletypes() {
        $this->db->from("minecraft_ruletypes");

        return $this->db->get()->result();
    }

    public function getMinecraftPlayer() {
        $this->db->select("username");
        $this->db->from("linked_accounts as la");
        $this->db->where("user_id", $this->session->userdata("user_id"));

        return $this->db->get()->row();
    }

    public function getArenas($id=null) {
        $this->db->from("minecraft_arenas as ma");

        if (isset($id)) {
            $this->db->where("id", $id);
            return $this->db->get()->row();
        } else {
            return $this->db->get()->result();
        }
    }

    public function getMinecraftColors() {
        $this->db->from("minecraft_colors as mc");

        return $this->db->get()->result();
    }

    public function getMinecraftItems($type=null, $item_id=null) {
        $this->db->select("mi.id, mi.name, mi.name_color as name_color_id, mc.color as name_color, mi.lore, mi.lore_color as lore_color_id, mc1.color as lore_color, mi.item_type, mo.name as item_name, mi.show_enchants, mi.unbreakable, mi.custom");
        $this->db->from("minecraft_items as mi");
        $this->db->join("minecraft_objects as mo", "mi.item_type=mo.id");

        $this->db->join("minecraft_colors as mc", "mi.name_color=mc.id", "left");
        $this->db->join("minecraft_colors as mc1", "mi.lore_color=mc1.id", "left");
        
        if (isset($type) && $type != "") {
            $this->db->where("mo.type", $type);
        } else {
            $this->db->where("mo.type <", 3); //not needed if there cannot be custom mobs in minecraft_items
        }

        if (isset($item_id) && $item_id != "") {
            $this->db->where("mi.id", $item_id);
        }
        
        $this->db->where("mi.name IS NOT NULL", NULL);

        $this->db->order_by("mi.name asc, mo.type asc");

        $result = $this->db->get();
        
        return (isset($item_id)) ? $result->row() : $result->result();
    }

    public function getMinecraftMobs() {
        $this->db->from("minecraft_objects as mo");
        $this->db->where("mo.type", 3);
        $this->db->order_by("mo.name asc, mo.type asc");

        return $this->db->get()->result();
    }

    public function getMinecraftItemAmount($kit_id) {
        $this->db->from("minecraft_amounts as ma");
        $this->db->where("ma.kit_id", $kit_id);

        return $this->db->get()->row();
    }

    public function createNewGameConfig($user_id, $info, $kits, $rules) {
        $game_bases = array("point", "time", "judge");
        $play_types = array("solo", "cooperative", "competitive");
        $judge_type = array("manual", "mob_farm");

        $arena = null;
        $locationXMin = $locationYMin = $locationZMin = -10; $locationXMax = $locationYMax = $locationZMax = 10; 
        if (isset($info->new_arena)) {
            $arenas = $this->getArenas(sanitizeInput($info->new_arena, FILTER_VALIDATE_INT));
            $arena = $arenas->id;

            if (isset($arena)) {
                $locationXMin = $arenas->min_x; $locationXMax = $arenas->max_x;
                $locationYMin = $arenas->min_y; $locationYMax = $arenas->max_y;
                $locationZMin = $arenas->min_z; $locationZMax = $arenas->max_z;
            }
        }

        $timelimitMin = 0;
        $timelimitMax = 0; 
        $point_value = (sanitizeInput($info->new_point_value, FILTER_VALIDATE_INT) ? sanitizeInput($info->new_point_value, FILTER_VALIDATE_INT) : null);
        if (isset($info->new_game_base) && in_array($info->new_game_base, $game_bases)) {
            switch ($info->new_game_base) {
                case "point":
                    $timelimitMin = 10;
                    $timelimitMax = 600;

                    $point_value = null;
                    break;
                case "time":
                    $timelimitMin = 1;
                    $timelimitMax = 10000;
                    break;
            }
        }

        $infodata = array(
            'user_id'            => $user_id,
            'name'               => sanitizeInput($this->security->xss_clean($info->new_gameconfig), FILTER_SANITIZE_STRING),
            'gametype'           => sanitizeInput($info->gamemode, FILTER_VALIDATE_INT) ? sanitizeInput($info->gamemode, FILTER_VALIDATE_INT) : 11,
            'arena'              => sanitizeInput($info->new_arena, FILTER_VALIDATE_INT) ? sanitizeInput($info->new_arena, FILTER_VALIDATE_INT) : null,
            'play_type'          => (isset($info->new_play_type) && in_array($info->new_play_type, $play_types)) ? $info->new_play_type : null,
            'game_base'          => (isset($info->new_game_base) && in_array($info->new_game_base, $game_bases)) ? $info->new_game_base : null,
            'timelimit'          => sanitizeInput($info->new_timelimit, FILTER_VALIDATE_INT) ? sanitizeInput($info->new_timelimit, FILTER_VALIDATE_INT) : null,
            'point_value'        => $point_value,
            'hunger'             => sanitizeInput($info->new_hunger, FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
            'regen'              => sanitizeInput($info->new_regen, FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
            'looting'            => sanitizeInput($info->new_looting, FILTER_VALIDATE_BOOLEAN) ? 1 : 0,
            'trial'              => 1,
            'approved'           => 0
        );

        $this->db->insert('minecraft_gameinfo', $infodata);
        $info_id = $this->db->insert_id();

        $countKit = 0; $countRule = 0;
        if ($this->db->affected_rows()) {
            foreach($kits as $key => $kit) {
                $kit = (object)$kit;

                if (isset($kit->existing_kit_id)) {
                    $existing = sanitizeInput($kit->existing_kit_id, FILTER_VALIDATE_INT);
                    $this->db->from("minecraft_kits");
                    $this->db->where("id", $existing);
                    $exist = $this->db->get()->row();

                    if (count($exist) > 0) {
                        $this->db->insert('minecraft_gameinfo_kits', array("info_id" => $info_id, "kit_id" => $kit->id));
                    }
                } else {
                    $num = "_" . (int)substr($key, -1);
                    $kitdata = array(
                        'name'               => isset($kit->{"new_kit_name" . $num}) ? $kit->{"new_kit_name" . $num} : null,
                        'name_color'         => ((isset($kit->{"new_kit_name_color" . $num}) && $kit->{"new_kit_name_color" . $num} != "0") ? (int)$kit->{"new_kit_name_color" . $num} : null),
                        'user_id'            => $user_id,
                        'key'                => ((isset($kit->{"new_kit_key" . $num}) && $kit->{"new_kit_key" . $num} != "0") ? (int)$kit->{"new_kit_key" . $num} : null),
                        'hotbar_1'           => ((isset($kit->{"new_hotbar_1" . $num}) && $kit->{"new_hotbar_1" . $num} != "0") ? (int)$kit->{"new_hotbar_1" . $num} : null),
                        'hotbar_2'           => ((isset($kit->{"new_hotbar_2" . $num}) && $kit->{"new_hotbar_2" . $num} != "0") ? (int)$kit->{"new_hotbar_2" . $num} : null),
                        'hotbar_3'           => ((isset($kit->{"new_hotbar_3" . $num}) && $kit->{"new_hotbar_3" . $num} != "0") ? (int)$kit->{"new_hotbar_3" . $num} : null),
                        'hotbar_4'           => ((isset($kit->{"new_hotbar_4" . $num}) && $kit->{"new_hotbar_4" . $num} != "0") ? (int)$kit->{"new_hotbar_4" . $num} : null),
                        'hotbar_5'           => ((isset($kit->{"new_hotbar_5" . $num}) && $kit->{"new_hotbar_5" . $num} != "0") ? (int)$kit->{"new_hotbar_5" . $num} : null),
                        'hotbar_6'           => ((isset($kit->{"new_hotbar_6" . $num}) && $kit->{"new_hotbar_6" . $num} != "0") ? (int)$kit->{"new_hotbar_6" . $num} : null),
                        'hotbar_7'           => ((isset($kit->{"new_hotbar_7" . $num}) && $kit->{"new_hotbar_7" . $num} != "0") ? (int)$kit->{"new_hotbar_7" . $num} : null),
                        'hotbar_8'           => ((isset($kit->{"new_hotbar_8" . $num}) && $kit->{"new_hotbar_8" . $num} != "0") ? (int)$kit->{"new_hotbar_8" . $num} : null),
                        'hotbar_9'           => ((isset($kit->{"new_hotbar_9" . $num}) && $kit->{"new_hotbar_9" . $num} != "0") ? (int)$kit->{"new_hotbar_9" . $num} : null),
                        'offhand'            => ((isset($kit->{"new_offhand" . $num}) && $kit->{"new_offhand" . $num} != "0") ? (int)$kit->{"new_offhand" . $num} : null),
                        'armor_head'         => ((isset($kit->{"new_armor_head" . $num}) && $kit->{"new_armor_head" . $num} != "0") ? (int)$kit->{"new_armor_head" . $num} : null),
                        'armor_chest'        => ((isset($kit->{"new_armor_chest" . $num}) && $kit->{"new_armor_chest" . $num} != "0") ? (int)$kit->{"new_armor_chest" . $num} : null),
                        'armor_pants'        => ((isset($kit->{"new_armor_pants" . $num}) && $kit->{"new_armor_pants" . $num} != "0") ? (int)$kit->{"new_armor_pants" . $num} : null),
                        'armor_boots'        => ((isset($kit->{"new_armor_boots" . $num}) && $kit->{"new_armor_boots" . $num} != "0") ? (int)$kit->{"new_armor_boots" . $num} : null),
                    );
    
                    $this->db->insert('minecraft_kits', $kitdata);
                    $kit_id = $this->db->insert_id();
    
                    if ($this->db->affected_rows()) {
                        $this->db->insert('minecraft_gameinfo_kits', array("info_id" => $info_id, "kit_id" => $kit_id));
                    } else {
                        $countKit++;
                    }
                }
            }
    
            $judge_player = $user_id;
            foreach($rules as $key => $rule) {
                $rule = (object)$rule;

                if (isset($kit->existing_rule_id)) {
                    $existing = sanitizeInput($rule->existing_rule_id, FILTER_VALIDATE_INT);
                    $this->db->from("minecraft_rules");
                    $this->db->where("id", $existing);
                    $exist = $this->db->get();

                    if (count($exist) > 0) {
                        $this->db->insert('minecraft_gameinfo_rules', array("info_id" => $info_id, "rule_id" => $rule->id));
                    }
                } else {
                    $num = "_" . (int)substr($key, -1);
                    $ruledata = array(
                        'user_id'            => $user_id,
                        'ruletype_id'        => ((isset($rule->{"new_rule_type" . $num}) && $rule->{"new_rule_type" . $num} != "0") ? (int)$rule->{"new_rule_type" . $num} : null),
                        'name'               => (isset($rule->{"new_rule_name" . $num}) ? $rule->{"new_rule_name" . $num} : null),
                        'value'              => (isset($rule->{"new_value" . $num}) ? clamp((int)$rule->{"new_value" . $num}, -10000, 10000) : null),
                        'wave_D'             => (isset($rule->{"new_wave_D" . $num}) ? clamp((int)$rule->{"new_wave_D" . $num}, 1, 25) : null),
                        'wave_Int'           => (isset($rule->{"new_wave_Int" . $num}) ? clamp((int)$rule->{"new_wave_Int" . $num}, 1, 10) : null),
                        'location_x'         => (isset($rule->{"new_location_x" . $num}) ? clamp((int)$rule->{"new_location_x" . $num}, $locationXMin, $locationXMax) : null),
                        'location_y'         => (isset($rule->{"new_location_y" . $num}) ? clamp((int)$rule->{"new_location_y" . $num}, $locationYMin, $locationYMax) : null),
                        'location_z'         => (isset($rule->{"new_location_z" . $num}) ? clamp((int)$rule->{"new_location_z" . $num}, $locationZMin, $locationZMax) : null),
                        'checkpoint'         => (isset($rule->{"new_checkpoint" . $num}) ? (int)$rule->{"new_checkpoint" . $num} : null),
                        'item_type'          => ((isset($rule->{"new_item_type" . $num}) && $rule->{"new_item_type" . $num} != "0") ? (int)$rule->{"new_item_type" . $num} : null),
                        'mob_type'           => ((isset($rule->{"new_mob_type" . $num}) && $rule->{"new_mob_type" . $num} != "0") ? (int)$rule->{"new_mob_type" . $num} : null),
                        'judge_type'         => ((isset($rule->{"new_judge_type" . $num}) && in_array($rule->{"new_judge_type" . $num}, $judge_type)) ? $rule->{"new_judge_type" . $num} : null),
                        'judge_description'  => ((isset($rule->{"new_judge_descr" . $num}) && $rule->{"new_judge_descr" . $num} != "") ? $rule->{"new_judge_descr" . $num} : null),
                        'judge_player'       => ((isset($rule->{"new_rule_type" . $num}) && $rule->{"new_rule_type" . $num} == "6") ? $judge_player : null),
                        'perimeter'          => (isset($rule->{"new_perimeter" . $num}) ? (int)$rule->{"new_perimeter" . $num} : null),
                        'starting'           => ((isset($rule->{"new_starting" . $num}) && $rule->{"new_starting" . $num} != "0") ? (int)$rule->{"new_starting" . $num} : null)
                    );

                    $this->db->insert('minecraft_rules', $ruledata);
                    $rule_id = $this->db->insert_id();
    
                    if ($this->db->affected_rows()) {
                        $this->db->insert('minecraft_gameinfo_rules', array("info_id" => $info_id, "rule_id" => $rule_id));
                    } else {
                        $countRule++;
                    }
                }
            }

            if ($countKit > 0 || $countRule > 0) {
                return array("status" => "failed", "message" => "could not insert " . $countKit . " kits - could not insert " . $countRule . " rules");
            } else {
                return array("status" => "success", "message" => "created new config");
            }
        } else {
            return array("status" => "failed", "message" => "error creating new config");
        }
    }

    public function get_game($game_id) {
        $this->db->from("game");
        $this->db->where("id", $game_id);

        return $this->db->get()->row();
    }
}
