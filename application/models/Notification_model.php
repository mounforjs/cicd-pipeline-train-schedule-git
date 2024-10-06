<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Notification_model extends CI_Model {

    public function insertNotification($for_user, $action_user, $notes, $type, $action, $game_id=null,  $charity_id=null, $date=null) {
        date_default_timezone_set('UTC');

        $data = array(
            'for_user' => $for_user,
            'action_user' => $action_user,
            'Notes'   => $notes,
            'type' => $type,
            'action' => $action,
            'game_id' => $game_id,
            'charity_id' => $charity_id,
            'Date' => isset($date) ? $date : date("Y-m-d H:i:s")
        );

        $result = $this->db->insert('notification', $data);
        return $result;
    }

    public function getNotificationCharity($charity_id) {
        $this->db->from("charity");
        $this->db->where("id", $charity_id);

        return $this->db->get()->row();
    }

    public function getNotificationGame($game_id) {
        $this->db->from("game");
        $this->db->where("id", $game_id);

        return $this->db->get()->row();
    }

    public function getNotification($for_user, $action_user=null, $type=null, $action=null, $game_id=null) {
        $this->db->from("notification");
        $this->db->where("for_user", $for_user);

        if (isset($action_user)) {
            $this->db->where("action_user", $action_user);
        }

        if (isset($type)) {
            $this->db->where("type", $type);
        }

        if (isset($action)) {
            $this->db->where("action", $action);
        }

        if (isset($game_id)) {
            $this->db->where("game_id", $game_id);
        }

        $this->db->order_by("id", "DESC");

        return $this->db->get()->result();
    }
    
    public function dismissNotifications($user_id, $dismissed) {
        $this->db->set('status', 1);
        $this->db->where('for_user', $user_id);
        $this->db->where_in('id', $dismissed);
        $this->db->update('notification');
    }

    public function getUnseenCount($user_id) {
        $this->db->from("notification");
        $this->db->where(array("for_user" => $user_id, "status" => 0));

        return $this->db->get()->num_rows();
    }

    public function getNotifications($user_id) {
        $this->db->from("notification");
        $this->db->where(array("for_user" => $user_id, "status" => 0));
        $this->db->order_by("status ASC, Date DESC");

        $this->db->limit(20);

        return $this->db->get()->result_array();
    }
}
