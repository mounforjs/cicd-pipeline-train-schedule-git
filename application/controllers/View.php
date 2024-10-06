<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class View extends CI_Controller {
	 public function __construct() {
        parent::__construct();
        ob_start(); # add this
        
        $this->load->model('notification_model');
        $this->load->model('home_model');
        $this->load->library('user_agent');
        $this->load->library('template');
        $this->load->library('session');
        
    }

    public function fetch() {
        $user_id = $this->session->userdata('user_id');

        $dismissed = sanitizeInputArray(json_decode($this->input->raw_input_stream, true)["dismissed"], FILTER_VALIDATE_INT);

        //dismiss those marked
        if (count($dismissed) > 0) {
            $this->notification_model->dismissNotifications($user_id, $dismissed);
        }

        //get new results after dismissed
        $count = $this->notification_model->getUnseenCount($user_id);
        $result = $this->notification_model->getNotifications($user_id);

        $output = array();
        if (count($result) > 0) {
            foreach ($result as $row) {
                if ($row["status"]==0) {
                    $class='unread';
                    $unreadIcon = 'fa fa-envelope'; 
                } else {
                    $class = '';
                    $unreadIcon = 'fa fa-envelope-open-o';
                }

                $output[$row["id"]] = '<div class="notification-box ' . $class . '"><a class="notification-msg"  href="' . $this->getLink($row) . '"><strong><i class="'.$unreadIcon.'"></i> '.$row["Notes"].'</strong></a><div class="dismiss-notification"><a class="dismiss-notification" data-id="'.$row["id"].'" stat-id="'.$row["status"].'"><i class="fa fa-times" aria-hidden="true"></i></a></div></div>';
            }
        } else {
            $output = array('<p style="font-style:italic">No Notification Found</p>');
        }

        $data = array(
            'notification' => $output,
            'unseen_notification'  => $count
        );

        echo json_encode($data);
    }

    public function getLink($notification) {
        $link = "";

        if ($notification["type"] == "game") {
            $game = $this->notification_model->getNotificationGame($notification["game_id"]);
            $status = getGameState($game);

            if (isset($game)) {
                switch ($notification["action"]) {
                    case 'create':
                    case 'edit':
                    case 'publish':
                    case 'win':
                    case 'end':
                        $link = asset_url(). 'games/show/' . $status . "/" . $game->slug;
                        break;
                    case 'buy':
                        $link = asset_url().'transactions/';
                        break;
                    case 'review':
                        $link = asset_url(). 'games/review/' . $game->slug;
                        break;
                    default:
                        $link = "";
                        break;
                }
            }
        } else if ($notification["type"] == "prize") { 
            switch ($notification["action"]) {
                case 'win':
                case 'confirm':
                    $game = $this->notification_model->getNotificationGame($notification["game_id"]);
                    $status = getGameState($game);
                    
                    $link = asset_url(). 'games/show/' . $status . "/" . $game->slug;
                    break;
                case 'claimed':
                case 'process':
                case 'review':
                    $link = asset_url() .'dashboard?tab=prizes';
                    break;
                default:
                    $link = "";
                    break;
            }
        } else { //fundraiser
            $charity = $this->notification_model->getNotificationCharity($notification["charity_id"]);
            
            if (isset($charity)) {
                switch ($notification["action"]) {
                    case 'create':
                    case 'add':
                    case 'approved':
                        $link = asset_url(). 'fundraisers/show/all/' . $charity->slug;
                        break;
                }
            }
        }
        
        return $link;
    }
}
