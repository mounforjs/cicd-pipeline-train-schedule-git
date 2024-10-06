<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Games extends CI_Controller {

    public function __construct() {
        parent::__construct();
        ob_start(); # add this

        $this->load->model('home_model');
        $this->load->model('charity_model');
        $this->load->model('user_model');
        $this->load->model('wishlist_model');
        $this->load->model('Game_play_model');
        $this->load->model('Create_game_model');
        $this->load->model('manage_games_model');
        $this->load->model('Quiz_model');
        $this->load->model('Question_model');
        $this->load->model('Notification_model');
        $this->load->model('address_model');
        $this->load->model('cron_model');
        $this->load->model('quiz_model');

        $this->load->library('template');
        $this->load->library('session');
        $this->load->library('Gamedata');

        $this->user_id = $this->session->userdata("user_id");

        $segments = explode('/', trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/'));
        if ($segments[1] != 'create' && $segments[1] != 'edit') {
            $this->template->set_breadcrumb('Home', asset_url());
        }
    }

    public function show() {
        $show = $this->uri->segment('3') != null ? $this->uri->segment('3') : "play";
        $gameSlug = $this->uri->segment('4') != null ? $this->uri->segment('4') : "";

        if ($show == 'drafted' && !$this->user_id) {
            redirect('login');
        }
        if ($show == 'live' && !$this->user_id) {
            redirect('login');
        }
        if ($show == 'published' && !$this->user_id) {
            redirect('login');
        }
        $notIn = array(
            'play',
            'drafted',
            'published',
            'live',
            'completed',
            'wishlist',
            'played'
        );

        if (!in_array($show, $notIn)) {
            redirect("error_404");
            exit;
        }

        if ($gameSlug != '') {
            $data['game'] = $this->home_model->fetch_game_detail($gameSlug);
            if (!isset($data['game'])) {
                redirect("games/show/" . $show);
                exit;
            } 

            $data['game']->selected_fundraiser = $this->charity_model->get_game_fundraiser($data['game']->id);
            $data['game']->selected_fundraiser->Image = getImagePathSize($data['game']->selected_fundraiser->Image, "beneficiary_info_logo");

            $status = getGameState($data['game']);
            $access = $this->home_model->getAccessPrivilege($data['game']->id, $this->user_id);

            if($show !== $status && !$access){ //if game status & url status differ AND creator and user are the same
                echo $this->user_id . " " . $data['game']->user_id . " - " . (isAdmin() ? "true" : "false");
                redirect("games/show/" . $status . "/" . $gameSlug);
                exit;
            }

            $data["account_link"] = $this->checkAccountLink($data['game']->gameType);

            if ($show == 'completed' && $data['game']->user_id != $this->user_id) {
                $confirmed = $this->home_model->getConfirmingStatus($data['game']->id, $this->user_id);
                if (!isset($confirmed->confirmed)) {
                    redirect("error_404");
                    exit;
                } else {
                    if ($confirmed->confirmed == 1) {
                        redirect("dashboard?tab=prizes");
                    } else {
                        $profile = getprofile();
                        $addresses = $this->address_model->getUserAddresses($this->user_id);
                        $defaultAddress = $this->address_model->getDefaultAddress($this->user_id);

                        $data["confirmPrize"] = array(
                            "claim" => true,
                            "profile" => array("firstname" => $profile->firstname, "lastname" => $profile->lastname),
                            "addresses" => (empty($addresses) ? NULL : $addresses),
                            "defaultAddress" => (empty($defaultAddress) ? NULL : $addresses[0])
                        );
                    }
                }
            }
            
            $data['game']->profile_img_path     = getImagePathSize($data['game']->profile_img_path, 'prize_creator_icon');
            $data['game']->GameImage            = getImagePathSize($data['game']->Game_Image, 'game_card_thumbnails');
            $data['game']->game_type            = $this->home_model->get_game_type($data['game']->Type);
            $data['game']->game_type_image      = getGameTypeImage($data['game']->game_type[0]->name);
            $data['game']->credit_type_image    = getCreditTypeImage($data['game']->credit_type);
            $data['game']->game_wishlist_status = $this->wishlist_model->get_wishlist_status($data['game']->id);
            $data['game']->rating = get_game_rating($data['game']->id);

            if ($data['game']->winner_option == 3) {
                $data['game']->game_end_rule = "Time Elapsed + Goal Reached";
            } else if ($data['game']->winner_option == 2) {
                $data['game']->game_end_rule = "Fundraising Goal Reached";
            } else if ($data['game']->winner_option == 4) {
                $data['game']->game_end_rule = "Date Reached: " . (isset($data['game']->End_Date) ? $data['game']->End_Date . " GMT/UTC" : "?");
            }

            $data['game']->game_status = ucfirst(getGameState($data['game']));

            if ($data['game']->credit_type != "free") {             
                $data['game']->calculated_finances = (object) calculate_game_finances($data['game']);
                $data['game']->goalRaised =  round_to_2dc($this->cron_model->getTotalRaised($data['game']->id)->credits);
            }

            if($data['game']->credit_type == 'credit') {
                $data['game']->minAmountNeededForGoal = $data['game']->fundraise_value;
            }
            else {
                $data['game']->minAmountNeededForGoal = $data['game']->fundraise_value + ($data['game']->value_of_the_game * $data['game']->winner_count);
            }

            // Ensure $data['game'] is an object
            if (isset($data['game']) && is_object($data['game'])) {
                // Check if the property exists and is not null
                if (isset($data['game']->fundraise_image)) {
                    $data['game']->supported_fundraise_image = getImagePathSize($data['game']->fundraise_image, 'beneficiary_info_logo');
                } else {
                    // Handle the case where fundraise_image is not set
                    $data['game']->supported_fundraise_image = 'default-image-path'; // Provide a default path or handle accordingly
                }
            } else {
                // Handle the case where $data['game'] is not set or not an object
                // Optionally initialize $data['game'] or log an error
                $data['game'] = new stdClass(); // Initialize as an empty object if needed
                $data['game']->supported_fundraise_image = 'default-image-path'; // Provide a default path or handle accordingly
            }

            $this->db->select('main_image, prize_image');
            $this->db->from('prize');
            $this->db->where('game_id', $data['game']->id);
            $this->db->order_by("main_image", "desc");
            $data['game']->prize_image_data = $this->db->get()->result();

            foreach ($data['game']->prize_image_data as $key => $value) {
                $data['game']->prize_image_data[$key]->prize_image = getImagePathSize($value->prize_image, "game_details_slideshow_main_image");
            }

            $data['user_default_fundraiser']  = $this->charity_model->get_user_default_fundraise(true);
            $data['all_approved_charity_list']  = $this->charity_model->get_approved_charity_list();
            $data['user_approved_charity_list'] = $this->charity_model->get_approved_charity_list($this->session->userdata('user_id'));
            
            if ($show === 'play') {
                $this->Game_play_model->insert_visit($data['game']->id);
            }

            $this->session->set_userdata('page', 'game_detail');
            $filters = array("user" => $data['game']->username, "exclude_games" => [$data['game']->id]);
            $data['game_list'] = $this->gamedata->getGamedata($filters, $show, 0, 3);

            $this->template->set_layout(DEFAULT_LAYOUT)->build('games/game_detail', $data);
        } else {
            $data = $this->gamedata->getCardfilterGamedata($this->input->get(), $show);

            $this->template->set_breadcrumb($show . ' Games', asset_url('games/show/' . $show));
            $this->template->set_layout(DEFAULT_LAYOUT)->build('games/game_list', $data);
        }
    }

    function showMore() {
        $show = sanitizeInput($this->input->get('show'), FILTER_SANITIZE_STRING);
        $offset = sanitizeInput($this->input->get('offset'), FILTER_VALIDATE_INT);

        $gamedata = $this->gamedata->getGamedata($this->input->get(), $show, $offset);
        echo json_encode($gamedata);
    }

    public function add_wishlist() {
        $game_id = sanitizeInput($this->input->post('id'), FILTER_VALIDATE_INT);
        
        $result  = $this->wishlist_model->add_wishlist($game_id);
        echo $result;
    }

    public function remove_wishlist() {
        $game_id = sanitizeInput($this->input->post('id'), FILTER_VALIDATE_INT);

        $result  = $this->wishlist_model->remove_wishlist($game_id);
        echo $result;
    }

    public function playing($slug = '', $custom_amount = 0) {
        check_login();
        if ($slug == '') {
            $this->session->set_flashdata('icon', 'error');
            $this->session->set_flashdata('prompt_title', 'Whoops!');
            $this->session->set_flashdata('message', 'This game does not exist.');
            redirect(asset_url('games/show/play'));
            exit;
        }

        $custom_amount = sanitizeInput($this->input->get('custom_amount'), FILTER_VALIDATE_FLOAT);
        $selected_beneficiary = sanitizeInput($this->input->get('selected_beneficiary'), FILTER_SANITIZE_STRING);

        $game_details = $this->home_model->fetch_game_detail($slug);
        if ($game_details->donationOption == 2 && !empty($selected_beneficiary)) {
            //verify selected beneficiary is approved
            $selected = $this->charity_model->getFundraiserIdBySlug($selected_beneficiary);
            if (isset($selected) && $selected->approved == "Yes") {
                $game_details->selected_fundraiser = $selected;
            } else {
                $game_details->selected_fundraiser = $this->charity_model->get_game_fundraiser($game_details->id);
            }
        } else {
            $game_details->selected_fundraiser = $this->charity_model->get_game_fundraiser($game_details->id);
        }

        // do not allow game creator to play game
        if ($this->user_id == $game_details->user_id) {
            $this->session->set_flashdata('icon', 'warning');
            $this->session->set_flashdata('prompt_title', 'Whoops!');
            $this->session->set_flashdata('message', 'You cannot play your own game!');
            redirect( asset_url('games/show/play') );
            exit();
        }

        // only games that are live should be playable
        $status = getGameState($game_details);
        if ($status != 'play') {
            $this->session->set_flashdata('icon', 'warning');
            $this->session->set_flashdata('prompt_title', 'Whoops!');
            $this->session->set_flashdata('message', 'This game is not currently available to play!');
            redirect( asset_url('games/show/play') );
            exit();
        } else {
            // ensure there is enough time left
        }

        if (isset($game_details->id)) {
            // if an account link is required, ensure the account is linked
            $accountLink = $this->checkAccountLink($game_details->gameType);
            if ($accountLink["required"] && !$accountLink["linked"]) {
                $this->session->set_flashdata('icon', 'warning');
                $this->session->set_flashdata('prompt_title', 'Whoops!');
                $this->session->set_flashdata('message', 'You need to link your account to play this game! Link your account in your profile.');
                redirect($_SERVER['HTTP_REFERER']);
                exit();
            }

            // game session id to start/resume
            $game_session_id = $this->game_play_model->createGameSessionId($this->user_id, $game_details->id);
            $resume_game_session = false;

            if (!$accountLink["third_party"]) {
                // get existing game session and ensure it has either not started or has finished
                if (isset($_SESSION["currently_playing"]) && isset($_SESSION["currently_playing"][$game_details->id])) {
                    $game_session_id = $_SESSION["currently_playing"][$game_details->id];

                    if ((isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0')) {
                        // page refresh
                        unset($_SESSION["currently_playing"][$game_details->id]);
                        unset($_SESSION["active_sessions"][$game_session_id]);
                        $this->redirectIfPlaying($status, $slug, 'You have forfeit your attempt!');
                    } else {
                        // page exit by some other means
                        if ($this->isGameSessionActive($game_session_id)) {
                            // game session is still in progress
                            $this->redirectIfPlaying($status, $slug, 'Looks like you are currently playing that game!');
                        } else {
                            unset($_SESSION["currently_playing"][$game_details->id]);
                            unset($_SESSION["active_sessions"][$game_session_id]);
                        }
                    }
                }
                
                // validate whether last game session has started and/or finished
                $existing_game_session = $this->game_play_model->getLastGameSession($this->user_id, $game_details->id);
                if (isset($existing_game_session)) {
                    if (!isset($existing_game_session->created_at) || (isset($existing_game_session->created_at) && !isset($existing_game_session->start_time))) {
                        // existing game session has not started, resume
                        $game_session_id = $existing_game_session->game_session_id;
                        $resume_game_session = true;
                    }
                }

                if (isset($_SESSION["currently_playing"])) {
                    $_SESSION["currently_playing"][$game_details->id] = $game_session_id;
                    $_SESSION["active_sessions"][$game_session_id] = array("type" => "playing", "last_check_in" => null);
                } else {
                    $_SESSION["currently_playing"] = array($game_details->id => $game_session_id);
                    $_SESSION["active_sessions"] = array($game_session_id => array("type" => "playing", "last_check_in" => null));
                }
            }

            $this->db->trans_begin();

            $deduct = $this->Game_play_model->deductCreditsAndInitAttempt($this->user_id, $game_session_id, $game_details, $custom_amount, $resume_game_session);
            if ($deduct["status"]) {
                $gameplay_data = $this->constructGameData($game_details, $game_session_id);

                if ($accountLink["third_party"]) {
                    $award = $this->Game_play_model->awardMinecraftTokens($this->user_id, $game_details);
                    if ($award) {
                        $this->db->trans_commit();

                        $notes = "You've been awarded " . $game_details->attempt_count . " Minecraft tokens for " . $game_details->name . "!";
                        sendNotification("tokens_awarded", $this->user_id, $this->user_id, $notes, "game", "buy", $game_details->id, null);

                        $this->session->set_flashdata('confirm', true);
                        $this->session->set_flashdata('icon', 'success');
                        $this->session->set_flashdata('prompt_title', 'Tokens added!');
                        $this->session->set_flashdata('message', 'Connect to minecraft.winwinlabs.org to start playing!');
                    }
                    
                    redirect("games/show/play/" . $game_details->slug);
                } else {
                    $this->db->trans_commit();

                    $this->template->set_layout(GAMEPLAY_LAYOUT)->build('games/gamePlayScreen', $gameplay_data);
                }
            } else {
                $this->db->trans_rollback();

                unset($_SESSION["currently_playing"][$game_details->id]);
                unset($_SESSION["active_sessions"][$game_session_id]);

                if ($deduct["error"] == "insufficient") {
                    $_SESSION['redirectTo'] = $this->agent->referrer();
                    $_SESSION['playGameError'] = "insufficient";

                    redirect(asset_url('buycredits'));
                } else if ($deduct["error"] == "backend") {
                    $this->session->set_flashdata('icon', 'error');
                    $this->session->set_flashdata('prompt_title', 'Whoops!');
                    $this->session->set_flashdata('message', 'We ran into an error!');

                    redirect("games/show/play/" . $game_details->slug);
                }
            }
        } else {
            $this->session->set_flashdata('icon', 'warning');
            $this->session->set_flashdata('prompt_title', 'Whoops!');
            $this->session->set_flashdata('message', 'This game does not exist.');
            redirect(asset_url('games/show/play'));
        }
    }

    private function constructGameData($game_details, $game_session_id="") {
        $game_data = array(
            "game_session_id" => $game_session_id,
            "slug" => $game_details->slug,
            "type" => $game_details->gameType,
            "credit_type" => $game_details->credit_type
        );
        
        // set game relevant data
        switch ($game_details->gameType) {
            case "2048":
                $game_data['tile'] = $game_details->game_tile_goal;
                break;
            case "challenge":
                $game_data['name']      = $game_details->name;
                $game_data['quiz_id']   = $game_details->quiz_id;
                $game_data['quiz_det']  = $this->Quiz_model->getQuizById($game_details->quiz_id);
                break;
            case "minecraft":
                break;
            case "puzzle":
                $game_data['puzzleGrid'] = $game_details->Level;
                $game_data['puzzleImage'] = $game_details->Image;
                $game_data["puzzleContentType"] = pathinfo($game_data['puzzleImage'], PATHINFO_EXTENSION);
                break;
            default:
                break;
        }

        // set game rule
        switch ($game_details->gameType) {
            case "challenge":
                $game_data['rule'] = $game_details->Quiz_rules;
                break;
            default:
                $game_data['rule'] = $game_details->time_limit;
                break;
        }

        // set game goal
        switch ($game_details->gameType) {
            case "2048":
                $game_data['goal'] = str_replace("%", $game_details->game_tile_goal, $game_details->gameTypeGoal);
                break;
            default:
                $game_data['goal'] = $game_details->gameTypeGoal;
                break;
        }

        $game_data['gameRule'] = $this->gameRule($game_data['type'], $game_data['rule']); 

        return $game_data;
    }

    private function redirectIfPlaying($status, $slug, $msg) {
        $this->session->set_flashdata('icon', 'error');
        $this->session->set_flashdata('prompt_title', 'Whoops!');
        $this->session->set_flashdata('message', $msg);

        $this->session->keep_flashdata('icon');
        $this->session->keep_flashdata('prompt_title');
        $this->session->keep_flashdata('message');

        redirect("games/show/{$status}/{$slug}");
        exit();
    }

    private function isGameSessionActive($game_session_id) {
        // ensure game is regularly checking in - unset if hasnt - update if has
        if (isset($_SESSION["active_sessions"][$game_session_id])) {
            if (isset($_SESSION["active_sessions"][$game_session_id]["last_check_in"])) {
                $time_passed = time() - ($_SESSION["active_sessions"][$game_session_id]["last_check_in"]);
                if ($time_passed <= 10) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return true;
            }
        } else {
            return false;
        }
    }

    public function check_in() {
        $response = array();

        $_POST =  json_decode(file_get_contents('php://input'), true);
        $game_session_id = sanitizeInput($_POST["game_session_id"], FILTER_SANITIZE_STRING);

        $user_session = get_user_session();

        // ensure user is logged in, session valid, and game is regularly checking in - unset if hasnt - update if has
        if ($this->user_id && isset($user_session) && $_SESSION["session_id"] === $user_session->session_id) {
            if (isset($_SESSION["active_sessions"][$game_session_id])) {
                $current_time = time();
                $_SESSION['last_activity'] = $current_time; 

                if (!isset($_SESSION["active_sessions"][$game_session_id]["last_check_in"])) {
                    if ($_SESSION["active_sessions"][$game_session_id]["type"] == "playing") {
                        $start = $this->game_play_model->startGameSession($game_session_id, $current_time);
                        $response["status"] = ($start ? "success" : "failed");
                    } else {
                        $response["status"] = "success";
                    }

                    $_SESSION["active_sessions"][$game_session_id]["last_check_in"] = $current_time;
                } else {
                    if ($current_time - ($_SESSION["active_sessions"][$game_session_id]["last_check_in"]) > 10) {
                        unset($_SESSION["active_sessions"][$game_session_id]);
                        $response["status"] = "failed";
                    } else {
                        $_SESSION["active_sessions"][$game_session_id]["last_check_in"] = $current_time;
                    }
                }
            } else {
                $response["status"] = "failed";
            }
        } else {
            $response["status"] = "failed";
        }

        echo json_encode($response);
    }

    public function preview($slug = '') {
        check_login();
        if ($slug == '') {
            $this->session->set_flashdata('icon', 'warning');
            $this->session->set_flashdata('prompt_title', 'Whoops!');
            $this->session->set_flashdata('message', 'This game does not exist.');
            redirect(asset_url('games/show/play'));
            exit();
        }

        $game_details = $this->home_model->fetch_game_detail($slug);
        $status = getGameState($game_details);

        if (($this->user_id != $game_details->user_id) && !isAdmin()) {
            $this->session->set_flashdata('icon', 'warning');
            $this->session->set_flashdata('prompt_title', 'Whoops!');
            $this->session->set_flashdata('message', 'You don\'t have access to this game.');
            redirect(asset_url('games/show/play'));
        }

        if (isset($game_details->id)) {
            $accountLink = $this->checkAccountLink($game_details->gameType);
            if ($accountLink["required"] && !$accountLink["linked"]) {
                $this->session->set_flashdata('icon', 'warning');
                $this->session->set_flashdata('prompt_title', 'Whoops!');
                $this->session->set_flashdata('message', 'You need to link your account to play this game! Link your account in your profile.');
                redirect($_SERVER['HTTP_REFERER']);
            }

            $game_session_id = $this->game_play_model->createGameSessionId($this->user_id, $game_details->id);
            $gameplay_data = $this->constructGameData($game_details, $game_session_id);

            if (isset($_SESSION["currently_playing"])) {
                if (!isset($_SESSION["currently_playing"][$game_details->id])) {
                    $_SESSION["currently_playing"][$game_details->id] = $game_session_id;
                    $_SESSION["active_sessions"][$game_session_id] = array("type" => "preview", "last_check_in" => null, "slug" => $game_details->slug);
                } else {
                    if ((isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] === 'max-age=0')) {
                        // page refresh
                        unset($_SESSION["active_sessions"][$_SESSION["active_sessions"][$_SESSION["currently_playing"][$game_details->id]]]);
                        unset($_SESSION["currently_playing"][$game_details->id]);
                        $this->redirectIfPlaying($status, $slug, 'You have forfeit your attempt!');
                    } else {
                        // page exit by some other means
                        if ($this->isGameSessionActive($_SESSION["currently_playing"][$game_details->id])) {
                            // game session is still in progress
                            $this->redirectIfPlaying($status, $slug, 'Looks like you are currently previewing that game!');
                        } else {
                            unset($_SESSION["currently_playing"][$game_details->id]);
                            unset($_SESSION["active_sessions"][$_SESSION["currently_playing"][$game_details->id]]);
                        }
                    }
                }
            } else {
                $_SESSION["currently_playing"][$game_details->id] = $game_session_id;
                $_SESSION["active_sessions"] = array($game_session_id => array("type" => "preview", "last_check_in" => null, "slug" => $game_details->slug));
            }

            $isThirdParty = $this->Game_play_model->isThirdParty($game_details->gameType);
            if ($isThirdParty->third_party) {
                $this->session->set_flashdata('confirm', true);
                $this->session->set_flashdata('icon', 'success');
                $this->session->set_flashdata('prompt_title', 'This is a third party game!');
                $this->session->set_flashdata('message', $isThirdParty->goal);

                redirect(asset_url("games/show/{$status}/" . $game_details->slug));
            } else {
                $this->template->set_layout(GAMEPLAY_LAYOUT)->build('games/gamePlayScreen', $gameplay_data);
            }
        } else {
            $this->session->set_flashdata('icon', 'warning');
            $this->session->set_flashdata('prompt_title', 'Whoops!');
            $this->session->set_flashdata('message', 'This game does not exist.');
            redirect(asset_url("games/show/{$status}"));
        }
    }

    private function gameRule($gType, $gRule) {
        switch ($gType) {
            case "2048":
                return ($gRule == 1) ? 'Fastest to Winning Tile' : 'Highest Score';
              break;
            case "puzzle":
                return ($gRule == 1) ? 'Fastest to solve wins' : 'Fewest moves wins';
              break;
            default:
                if ($gRule == 1) {
                    return 'Fastest 100% correct wins';
                } elseif ($gRule == 2) {
                    return 'Most right answers wins';
                } else {
                    return 'The fastest + most right answers wins';
                }
          }
    }

    public function submit() {
        $end_time = time();

        $user_session = get_user_session();
        $game_session_id = sanitizeInput($this->input->post("game_session_id"), FILTER_SANITIZE_STRING);

        $response = array();
        if ($this->user_id && isset($user_session) && $_SESSION["session_id"] === $user_session->session_id && !empty($game_session_id)) {
            if (isset($_SESSION["active_sessions"][$game_session_id])) {
                if ($_SESSION["active_sessions"][$game_session_id]["type"] == "playing") {
                    $game_details = $this->game_play_model->get_attempt_game_detail($game_session_id);

                    $submit_data = $this->construct_submit_data($game_details->gameType, $end_time);
                    $submit = $this->submit_game_data($game_details, $game_session_id, $submit_data);
                    if ($submit) {
                        $response["status"] = "success";
                        $response["redirect"] = "games/stats/{$game_details->slug}/{$game_session_id}";
                    } else {
                        $response["status"] = "failed";
                        $response["msg"] = "Error submitting score!";
                        $response["redirect"] = "games/show/play/{$game_details->slug}";
                    }

                    //remove game from currently playing and active_sessions
                    unset($_SESSION["currently_playing"][$game_details->id]);
                    unset($_SESSION["active_sessions"][$game_session_id]);
                } else if ($_SESSION["active_sessions"][$game_session_id]["type"] == "preview") {
                    $slug = $_SESSION["active_sessions"][$game_session_id]["slug"];
                    $game_details = $this->home_model->fetch_game_detail($slug);

                    $_SESSION["active_sessions"][$game_session_id]["game_score"] = $this->construct_submit_data($game_details->gameType, $end_time);

                    $response["status"] = "success";
                    $response["redirect"] = "games/stats_preview/{$slug}/{$game_session_id}";
                } else if ($_SESSION["active_sessions"][$game_session_id]["type"] == "development") {
                    unset($_SESSION["active_sessions"][$game_session_id]);

                    $response["status"] = "success";
                    $response["redirect"] = "games/create";
                }
            } else {
                $response["status"] = "failed";
                $response["msg"] = "Invalid session!";
                $response["redirect"] = "games/show/play";
            }
        } else {
            $response["status"] = "failed";
            $response["msg"] = "Invalid session!";
            $response["redirect"] = "games/show/play";
        }

        echo json_encode($response);
    }

    private function construct_submit_data($gametype, $end_time) {
        $data = array();

        // no client is trustworthy, but there are a plethora of issues (any score/measure/time should be calculated by server, not sent by client)

        switch ($gametype) {
            case "2048":
                $data["score"] = sanitizeInput($this->input->post("score"), FILTER_VALIDATE_INT);
                $data["completed_in"] = sanitizeInput($this->input->post("elapsed"), FILTER_VALIDATE_FLOAT) / 1000;
                $data["won"] = sanitizeInput($this->input->post("won"), FILTER_VALIDATE_INT);
                break;
            case "challenge":
                $data["completed_in"] = sanitizeInput($this->input->post("elapsed"), FILTER_VALIDATE_FLOAT) / 1000;
                $data["answers"] = json_decode($this->input->post("answer"));
                break;
            case "minecraft":
                break;
            case "puzzle":
                $data["steps"] = sanitizeInput($this->input->post("steps"), FILTER_VALIDATE_INT);
                $data["completed_in"] = sanitizeInput($this->input->post("elapsed"), FILTER_VALIDATE_FLOAT) / 1000;
                $data["won"] = 1;
                break;
            default:
                break;
        }

        $data["end_time"] = gmdate("Y-m-d H:i:s", $end_time);

        return $data;
    }

    private function submit_game_data($game_details, $game_session_id, $submit_data) {
        $result = null;

        switch ($game_details->gameType) {
            case 'challenge':
                $result = $this->quiz_model->submit($this->user_id, $game_details, $game_session_id, $submit_data);
                break;
            default:
                $result = $this->game_play_model->submit_game_data($game_session_id, $submit_data);
                break;
        }
        
        return $result;
    }

    function stats($slug="", $game_session_id="") {
        $this->load->driver('cache');
        $this->cache->clean();
        ob_clean();

        if (!empty($slug) && !empty($game_session_id)) {
            $game_details = $this->game_play_model->get_attempt_game_detail($game_session_id);
            $status = getGameState($game_details);
            
            //get game_details from valid session id - session should belong to corresponding user and game
            if (isset($game_details) && $this->user_id == $game_details->player_id && $slug == $game_details->slug) { // && $status == "live") {

                //only show stats if game is not currently in progress
                if (!isset($_SESSION["currently_playing"][$game_session_id])) {
                    $game_details->game_fundraiser = $this->charity_model->get_game_fundraiser($game_details->id);
                    $rating = $this->game_play_model->getUserRating($game_details->id, $this->user_id);

                    $history = $this->get_game_stats($game_session_id, $game_details);
                    if (isset($history)) { //Marking score seen
                        if ($game_details->donationOption == 2) {
                            $game_details->selected_fundraiser = $this->charity_model->getFundraiserDetailById($history->selected_beneficiary);
                        }

                        $this->game_play_model->setScoreSeen($game_session_id);
                        $this->template->set_layout(GAMEPLAY_LAYOUT)->build('games/gameWinScoreScreen', array(
                            'user' => getprofile(),
                            'game_details' => $game_details,
                            'game_score' => $history,
                            'rating' => $rating,
                        ));
                    } else {
                        redirect("games/show/play/{$game_details->slug}/");
                    }
                } else {
                    $this->session->set_flashdata('icon', 'warning');
                    $this->session->set_flashdata('prompt_title', 'Whoops!');
                    $this->session->set_flashdata('message', 'Game session is in progress!');
                    redirect("games/show/play/{$game_details->slug}/");
                }
            } else {
                if (isset($game_details)) {
                    if ($status == "live") {
                        redirect("games/show/play/{$slug}/");
                    } else {
                        redirect('games/show/play/');
                    }
                } else {
                    redirect('games/show/play/');
                }
            }
        } else {
            redirect('games/show/play/');
        }
    }

    public function stats_preview($slug="", $game_session_id="") {
        if (!empty($slug) && !empty($game_session_id) && isset($_SESSION["active_sessions"][$game_session_id]) && $_SESSION["active_sessions"][$game_session_id]["type"] == "preview") {
            $game_details = $this->home_model->fetch_game_detail($slug);
            if ($this->user_id == $game_details->user_id) { 
                $game_details->game_fundraiser = $this->charity_model->get_game_fundraiser($game_details->id);
                $rating = $this->game_play_model->getUserRating($game_details->id, $this->user_id);
                $game_score = (object)$_SESSION["active_sessions"][$game_session_id]["game_score"];

                //remove game from preview
                unset($_SESSION["currently_playing"][$game_details->id]);
                unset($_SESSION["active_sessions"][$game_session_id]);
                
                $this->template->set_layout(GAMEPLAY_LAYOUT)->build('games/gameWinScoreScreen', array(
                    'user' => getprofile(),
                    'game_details' => $game_details,
                    'game_score' => $game_score,
                    'rating' => $rating,
                    'preview' => true
                ));
            } else {
                redirect("games/show/drafted/{$slug}");
            }
        } else {
            redirect("games/show/drafted/{$slug}");
        }
    }

    private function get_game_stats($game_session_id, $game_details) {
        switch ($game_details->gameType) {
            default:
                $history = $this->Game_play_model->getGameHistoryForUserByGameId($this->user_id, $game_details->id, $game_session_id);
                break;
        }

        return $history;
    }

    public function deleteDraftedGame() {
        $slug = sanitizeInput($this->input->post('slug'), FILTER_VALIDATE_URL);

        $result = $this->Game_play_model->deleteGame($slug);
        echo ($result == true) ? 1 : 0;
    }

    public function publishGame() {
        $slug = sanitizeInput($this->input->post('slug'), FILTER_VALIDATE_URL);
        $date = sanitizeInput($this->input->post('pTime'), FILTER_SANITIZE_STRING);

        if ($this->validateDate($date)) {
            $result = $this->Game_play_model->updateGamePublishOrLiveDate($slug, $date, 'Yes');
        } else {
            $result = array("status" => "failed", "msg" => "Date must be in the future.");
        }

        echo json_encode($result);
    }

    public function liveGame() {
        $slug = sanitizeInput($this->input->post('slug'), FILTER_VALIDATE_URL);
        $date = gmdate('Y-m-d H:i:s', date('m/d/Y H:i:s'));

        $result = $this->Game_play_model->updateGamePublishOrLiveDate($slug, $date, 'Live');
        echo json_encode($result);
    }

    public function validateDate($date) {
        if (new DateTime($date) < new DateTime("now")) {
            return false;
        }

        return true;
    }

    public function create($type = '') {
        check_login();

        if (getprofile()->creator_status == 'Yes') {
            $user_id = $this->user_id;
            $gametypes = array("2048", "puzzle", "challenge", "minecraft");

            if (in_array($type, $gametypes)) {
                $data['type'] = $type;
                $data['default_fundraiser']  = $this->charity_model->get_user_default_fundraise();
                $data['default_fundraiser']->Image = getImagePathSize($data['default_fundraiser']->Image,'beneficiary_info_logo');
                $data['default_fundraiser']->icon  = getFundraiseIcon($data['default_fundraiser']->fundraise_type);
                $data['default_fundraiser']->totalRaised = $this->charity_model->get_total_raised($data['default_fundraiser']->slug)->raised;
                $data["search"] = $this->charity_model->get_beneficiary_list();

                $data['category'] = $this->Question_model->getCategoryList();

                switch ($type) {
                    case "2048":
                    case "puzzle":
                    case "challenge":
                        $total = $this->Quiz_model->getTotalApprovedQuizzes();
                        $data['quiz'] = $this->Quiz_model->getApprovedQuizList(null, null, 10, 0);

                        $data['quizConfig'] = array("createGame" => true, "quizName" => "createGameQuizzes");
                        $data['deferLoading'] = array("filtered" => $total, "total" => $total);
                        break;
                    case "minecraft":
                        $data["gamemodes"] = $this->Create_game_model->getGamemodes($type);
                        $data["gameconfigs"] = $this->Create_game_model->getGameConfigs($user_id, $data["gamemodes"][0]->id);

                        foreach ($data["gameconfigs"] as $key => $gameconfig) {
                            $gameconfig->kits = $this->Create_game_model->getKits($user_id, $gameconfig->id);
                            $gameconfig->rules = $this->Create_game_model->getRules($user_id, $gameconfig->id);
                        }

                        $data["selectedGamemode"] = $data["gameconfigs"][0]->gametype;
                        $data["selectedConfig"] = $data["gameconfigs"][0]->id;
                        $data["selectedConfigIdx"] = 0;

                        //create config form
                        $data["arenas"] = $this->Create_game_model->getArenas();
                        $data["ruletypes"] = $this->Create_game_model->getRuletypes();

                        $data["minecraftPlayer"] = $this->Create_game_model->getMinecraftPlayer()->username;
                        $data["minecraftColors"] = $this->Create_game_model->getMinecraftColors();
                        $data["minecraftItems"] = $this->Create_game_model->getMinecraftItems();
                        $data["minecraftArmor"] = $this->Create_game_model->getMinecraftItems(2);
                        $data["minecraftMobs"] = $this->Create_game_model->getMinecraftMobs();
                        break;
                }

                $this->template->set_layout(DEFAULT_LAYOUT)->build('games/gameCreateForm', $data);
            } else {
                $data = array();
                if (isset($type) && $type != '') {
                    $data['error'] = 'Please choose between given option!';
                }
                
                $this->template->set_layout(DEFAULT_LAYOUT)->build('games/gameCreateChoice', $data);
            }
        } else {
             redirect(asset_url());
        }
    }

    public function edit($slug) {
        check_login();
        $user_id = $this->user_id;

        $data['game'] = $this->home_model->fetch_game_detail($slug);
        if (isset($data['game']->gameType)) {

            $type = ($data['game']->gameTypeParent == 0) ? $data['game']->gameType : $this->home_model->get_game_type($data['game']->gameTypeParent)[0]->name;
            $data['type'] = $type;

            if ($data['game']->winner_option == 3) {
                $data['game']->game_end_rule = "Time Elapsed + Goal Reached";
            } else if ($data['game']->winner_option == 2) {
                $data['game']->game_end_rule = "Fundraising Goal Reached";
            } else if ($data['game']->winner_option == 4) {
                $data['game']->game_end_rule = "Date Reached: " . (isset($data['game']->End_Date) ? $data['game']->End_Date . " GMT/UTC" : "?");
            }

            if ($data['game']->Publish == 'No') {
                $data['game']->game_status = "Draft Game";
            } else if ($data['game']->Publish == 'Yes') {
                $data['game']->game_status = "Publish Game";
            } else if ($data['game']->Publish == 'Live') {
                $data['game']->game_status = "Live";
            }

            if ($data['game']->credit_type != "free") {
                $data['game']->fundraiseGoal = $data['game']->fundraise_value;
                $data['game']->charityPercentage = $data['game']->beneficiary_percentage;
                $data['game']->winner_credit    = ($data['game']->fundraise_value * $data['game']->winner_percentage) / 100;
                $data['game']->creator_credit   = (($data['game']->fundraise_value) * ($data['game']->creator_percentage / 100));
                $data['game']->creator_credit += ($data['game']->credit_type == "credit") ? 0 : ($data['game']->winner_count * $data['game']->value_of_the_game);
                $data['game']->winwin_credit    = ($data['game']->fundraise_value) * $data['game']->wwl_percentage / 100;
            }

            switch ($type) {
                case "puzzle": case "2048": case "challenge":
                    $data['quiz'] = $this->Quiz_model->getApprovedQuizList();
                    $data['game_quiz'] = $this->Quiz_model->getQuizByIdForGame($data['game']->quiz_id);
                    $data['fundraise_list'] = $this->charity_model->get_all_fundraise();
                    $data['category'] = $this->Question_model->getCategoryList();
                    break;
                case "minecraft":
                    $selectedConfig = $this->Create_game_model->getGameConfig($data['game']->id)[0];
                    $data["selectedGamemode"] = $selectedConfig->gametype;
                    $data["selectedConfig"] = $selectedConfig->id;

                    $gamemodes = $this->Create_game_model->getGamemodes($type);
                    $gameconfigs = $this->Create_game_model->getGameConfigs($user_id, $selectedConfig->gametype);

                    $data["gamemodes"] = $gamemodes;
                    $data["gameconfigs"] = $gameconfigs;
                    foreach ($gameconfigs as $key => $config) {
                        $config->kits = $this->Create_game_model->getKits($user_id, $config->id);
                        $config->rules = $this->Create_game_model->getRules($user_id, $config->id);

                        if ($config->id == $selectedConfig->id) {
                            $data["selectedConfigIdx"] = $key;
                        }
                    }

                    //create config form
                    $data["arenas"] = $this->Create_game_model->getArenas();
                    $data["ruletypes"] = $this->Create_game_model->getRuletypes();

                    $data["minecraftPlayer"] = $this->Create_game_model->getMinecraftPlayer()->username;
                    $data["minecraftColors"] = $this->Create_game_model->getMinecraftColors();
                    $data["minecraftItems"] = $this->Create_game_model->getMinecraftItems();
                    $data["minecraftArmor"] = $this->Create_game_model->getMinecraftItems(2);
                    $data["minecraftMobs"] = $this->Create_game_model->getMinecraftMobs();
                    break;
            }

            $total = $this->Quiz_model->getTotalApprovedQuizzes();
            $data['quiz'] = $this->Quiz_model->getApprovedQuizList(null, null, 10, 0);
            $data['game_quiz'] = $this->Quiz_model->getQuizByIdForGame($data['game']->quiz_id);

            $data['quizConfig'] = array("createGame" => true, "quizName" => "createGameQuizzes");
            $data['deferLoading'] = array("filtered" => $total, "total" => $total);

            $data['fundraise_list'] = $this->charity_model->get_all_fundraise();

            $fundraiser_id = $this->Create_game_model->getGameFundraiser($data['game']->id)->charity_id;
            $game_fundraiser = $this->charity_model->getFundraiserDetailById($fundraiser_id);

            #load data on page load, not on ajax
            $selected_fundraiser = $this->charity_model->get_fundraise($game_fundraiser->slug);
            $selected_fundraiser->Image = getImagePathSize($selected_fundraiser->Image,'beneficiary_info_logo');
            $selected_fundraiser->icon  = getFundraiseIcon($selected_fundraiser->fundraise_type);
            $selected_fundraiser->totalRaised = $this->charity_model->get_total_raised($selected_fundraiser->slug)->raised;
            $data["search"] = $this->charity_model->get_beneficiary_list();

            $data['game']->selected_fundraiser = $selected_fundraiser;

            $this->db->select('main_image, prize_image');
            $this->db->from('prize');
            $this->db->where('game_id', $data['game']->id);
            $this->db->order_by("main_image", "desc");
            $data['game']->prize_image_data = $this->db->get()->result();

            foreach ($data['game']->prize_image_data as $key => $value) {
                $data['game']->prize_image_data[$key]->prize_image = getImagePathSize($value->prize_image, 'game_details_slideshow_main_image');
            }

            $this->template->set_layout(DEFAULT_LAYOUT)->build('games/gameCreateForm', $data);
        } else {
            $this->session->set_flashdata('icon', 'error');
            $this->session->set_flashdata('prompt_title', 'Whoops!');
            $this->session->set_flashdata('message', 'That game doesn\'t exist!');
            redirect("games/show/drafted");
        }
    }

    public function development() {
        $type = $this->uri->segment('3');

        $dev_gametypes = $this->home_model->get_gametypes(true);
        $dev_names = array_keys($dev_gametypes);

        if (isset($type) && in_array($type, $dev_names)) {
            $accountLink = $this->checkAccountLink($type);
            if ($accountLink["required"] && !$accountLink["linked"]) {
                $this->session->set_flashdata('icon', 'error');
                $this->session->set_flashdata('prompt_title', 'Account Link Required!');
                $this->session->set_flashdata('message', 'Link your account before trying it out!');
                redirect("games/create");
                exit();
            }

            $game_session_id = $this->game_play_model->createGameSessionId($this->user_id, uniqid());
            $gameplay_data = array(
                'game_session_id' => $game_session_id,
                'type' => $type,
                'credit_type' => 'free',
                'goal' => $dev_gametypes[$type]["goal"],
                'attempt_count' => 3
            );

            if (isset($_SESSION["active_sessions"])) {
                $_SESSION["active_sessions"][$game_session_id] = array("type" => "development", "last_check_in" => null);
            } else {
                $_SESSION["active_sessions"] = array($game_session_id => array("type" => "development", "last_check_in" => null));
            }

            $this->template->set_layout(GAMEPLAY_LAYOUT)->build('games/gamePlayScreen', $gameplay_data);
        } else {
            $this->session->set_flashdata('icon', 'warning');
            $this->session->set_flashdata('prompt_title', 'Whoops!');
            $this->session->set_flashdata('message', 'This gametype does not exist or is ineligible.');
            redirect(asset_url($_SERVER['HTTP_REFERER']));
        }
    }

    public function add_game() {
        check_login();
        $result = $this->Create_game_model->create_game();

        echo json_encode($result);
    }

    private function checkAccountLink($gametype) {
        $isRequired = $this->Game_play_model->requireAccountLink($gametype);
        $isLinked = ($isRequired->account_link) ? $this->Game_play_model->isAccountLink($this->user_id, $isRequired->gametype) : 0;
        $isThirdParty = $this->Game_play_model->isThirdParty($gametype)->third_party;

        return array("required" => $isRequired->account_link, "linked" => $isLinked, "third_party" => $isThirdParty);
    }

    public function getGamemodeConfigs() {
        $user_id = $this->user_id;
        $gamemode = sanitizeInput($this->input->get("gamemode"), FILTER_VALIDATE_INT);

        $gameconfigs = $this->Create_game_model->getGameConfigs($user_id, $gamemode);
        foreach ($gameconfigs as $key => $gameconfig) {
            $gameconfig->kits = $this->Create_game_model->getKits($user_id, $gameconfig->id);
            $gameconfig->rules = $this->Create_game_model->getRules($user_id, $gameconfig->id);
        }

        echo json_encode($gameconfigs);
    }

    public function addNewGamemodeConfig() {
        $user_id = $this->user_id;

        $info = (object)$this->input->post("info");
        $kits = (object)array_slice($this->input->post("kits"), 0, 9);
        $rules = (object)array_slice($this->input->post("rules"), 0, 5);

        $config = $this->Create_game_model->createNewGameConfig($user_id, $info, $kits, $rules);

        echo json_encode($config);
    }

    public function duplicateGame($id='') {
        check_login();
    	$result = $this->Create_game_model->duplicateGame($id);

    	echo $result;
    }

    public function review($gameSlug = "") {
        check_login();
        
        $reviewerId = $this->user_id;
        if (empty($gameSlug)) { 
            $data['games_list'] = $this->manage_games_model->get_review_games($reviewerId, 10, 0);
            $total = sizeof($data['games_list']);

            $data['deferLoading'] = array("filtered" => $total, "total" => $total);

            $this->template->set_layout(DEFAULT_LAYOUT)->build('games/review', $data);
        } else {
            $game = $this->manage_games_model->get_review_details($gameSlug);
            if (!isset($game) || empty($game)) { //only get user's games
                redirect("games/review");
            } else {
                $data['game'] = $game;

                $attempts = $this->manage_games_model->getUserAttemptInfo($game->quiz_id, $game->id, null, null, 10, 0);
                $data["attempt_data"] = $attempts["attempts"];

                $selectedUsers = $this->manage_games_model->getSelectedUsers($game->quiz_id, $game->id, null, null, 3, 0);
                $data["selectedUsers"] = $selectedUsers["users"];
                $data["totalSelectedUsers"] = $this->manage_games_model->getTotalSelectedUsers($game->quiz_id, $game->id);

                if ($game->Publish != "No" && $game->Status != "Completed") {
                    $game->declarable = false;
                } else {
                    if ($game->processed == 0 || ($game->processed == 1 && $game->review_status == 1)) {
                        $game->declarable = true;
                    } else {
                        $game->declarable = false; $game->declared = true;
                    }
                }

                $game->selectable = ($selectedUsers["total"] <= $game->winner_count && (!isset($game->declared) || !$game->declared)) ? true : false;
                
                $data['deferLoading1'] = array("filtered" => $data["totalSelectedUsers"], "total" => $data["totalSelectedUsers"]);
                $data['deferLoading2'] = array("filtered" => $attempts["total"], "total" => $attempts["total"]);
                $data['deferLoading3'] = array("filtered" => 0, "total" => 0);
                
                $this->template->set_layout(DEFAULT_LAYOUT)->build('games/review_game_detail', $data);
            }
        }
    }

    public function getAllReviewGames() {
        check_login();

        $limit = (int)$this->input->get("length");
		$offset = (int)$this->input->get("start");
		$search = $this->input->get("search[value]");

		$by = $this->input->get("order[0][column]");
		$order = array("by" => $this->input->get("columns[" . $by . "][data]"), "arrange" => $this->input->get("order[0][dir]"));
        $reviewer_id = $this->session->userdata('user_id');

        $total = $this->manage_games_model->reviewGamesCount($reviewer_id);
        $games = $this->manage_games_model->get_review_games($reviewer_id, $limit, $offset, $search, $order);

        foreach ($games as $key => $game) {
			$game->Game_Image = getImagePathSize($game->Game_Image,'admin_games');
		}

        $data = array(
			"draw" => (int)$this->input->get("draw"),
			"recordsTotal" => $total,
			"recordsFiltered" => count($games),
			"data" => array_slice($games, $offset, $limit)
		);

		echo json_encode($data);
    }

    public function getReviewGameSelectedUsers() {
        check_login();

        $draw = filter_var($this->input->get('draw'), FILTER_VALIDATE_INT);

        $slug = filter_var($this->input->get('slug', true), FILTER_SANITIZE_URL);

        $limit = filter_var($this->input->get('length'), FILTER_VALIDATE_INT);
		$offset = filter_var($this->input->get('start'), FILTER_VALIDATE_INT);
        $search = $this->input->get("search[value]");

        $by = $this->input->get("order[0][column]");
		$order = array("by" => $this->input->get("columns[" . $by . "][data]"), "arrange" => $this->input->get("order[0][dir]"));

        $game = $this->manage_games_model->get_review_details($slug);

        $total = $this->manage_games_model->getTotalSelectedUsers($game->quiz_id, $game->id);
        $selectedUsers = $this->manage_games_model->getSelectedUsers($game->quiz_id, $game->id, $search, $order, $limit, $offset);
        $data = array(
			"draw" => $draw,
			"recordsTotal" => $total,
			"recordsFiltered" => $total,
			"data" => $selectedUsers["users"]
		);
        
        echo json_encode($data);
    }

    public function getReviewGameAttempts() {
        check_login();

        $draw = filter_var($this->input->get('draw'), FILTER_VALIDATE_INT);

        $slug = filter_var($this->input->get('slug', true), FILTER_SANITIZE_URL);

        $limit = filter_var($this->input->get('length'), FILTER_VALIDATE_INT);
		$offset = filter_var($this->input->get('offset'), FILTER_VALIDATE_INT);
        $search = $this->input->get("search[value]");

        $by = $this->input->get("order[0][column]");
		$order = array("by" => $this->input->get("columns[" . $by . "][data]"), "arrange" => $this->input->get("order[0][dir]"));

        $game = $this->manage_games_model->get_review_details($slug);

        $total = $this->manage_games_model->getTotalUserAttemptInfo($game->quiz_id, $game->id);
        $attempts = $this->manage_games_model->getUserAttemptInfo($game->quiz_id, $game->id, $search, $order, $limit, $offset);

        foreach ($attempts["attempts"] as $key => $attempt) {
            $attempt->editable = ((isset($game->selectable) && $game->selectable) && !$attempt->reselected);
        }

        $data = array(
			"draw" => $draw,
			"recordsTotal" => $total,
			"recordsFiltered" => $attempts["total"],
			"data" => array_slice($attempts["attempts"], $offset, $limit)
		);
        
        echo json_encode($data);
    }

    public function getReviewGameUserAttempt() {
        check_login();

        $draw = filter_var($this->input->get('draw'), FILTER_VALIDATE_INT);

        $user_id = filter_var($this->input->get('user_id'), FILTER_VALIDATE_INT);
        $quiz_id = filter_var($this->input->get('quiz_id'), FILTER_VALIDATE_INT);
        $game_id = filter_var($this->input->get('game_id'), FILTER_VALIDATE_INT);
        $attempt_num = filter_var($this->input->get('attempt_num'), FILTER_VALIDATE_INT);

        $limit = filter_var($this->input->get('length'), FILTER_VALIDATE_INT);
		$offset = filter_var($this->input->get('offset'), FILTER_VALIDATE_INT);
        $search = $this->input->get("search[value]");

        $by = $this->input->get("order[0][column]");
		$order = array("by" => $this->input->get("columns[" . $by . "][data]"), "arrange" => $this->input->get("order[0][dir]"));

        $quiz = $this->Quiz_model->getQuizById($quiz_id);

        $attempt = $this->manage_games_model->getUserAttempt($quiz->questions, $quiz_id, $game_id, $user_id, $search, $order, $limit, $offset, $attempt_num);

        $data = array(
			"draw" => $draw,
			"recordsTotal" => $attempt["total"],
			"recordsFiltered" => $attempt["total"],
			"data" => array_slice($attempt["questions"], $offset, $limit)
		);
        
        echo json_encode($data);
    }

    public function updateUserAttempt() {
        $userId = filter_var($this->input->post('rUserId', true), FILTER_VALIDATE_INT);
        $gameId = filter_var($this->input->post('rGameId', true), FILTER_VALIDATE_INT);
        $name = filter_var($this->input->post('rName', true), FILTER_SANITIZE_STRING);
        $value = $this->input->post('rValue', true);
        switch ($name) {
            case "final_rank":
                $value = filter_var($value, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                break;
            case "grade":
            case "notes":
                $value = preg_replace('/[^A-Za-z0-9\-]/', '', $value);
                break;
        }

        if (in_array($name, array("final_rank", "grade", "notes"))) {
            $data = $this->manage_games_model->updateReviewStatus($value, $userId, $name, $gameId);
            echo json_encode($data); 
        }
	}

    public function addrating() {
		$slug = $this->input->post('slug');
        $rating = $this->input->post('rating');
        
        $data = $this->game_play_model->addrating($slug, $rating);
        echo json_encode(array("status" => $data));
    }
}
