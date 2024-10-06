<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Short_model extends CI_Model {

    function getUrl($url) {
        $this->db->select("su.url");
        $this->db->from("shortened_urls as su");
        $this->db->where("su.short_url", $url);
        $url = $this->db->get()->row()->url;

        if (!isset($url)) {
            $url = "error_404";
        }

        return $url;
    }

    function doesURLExist($url) {
        $this->db->select("su.url");
        $this->db->from("shortened_urls as su");
        $this->db->where("su.short_url", $url);
        $exist = $this->db->get()->num_rows();

        return $exist;
    }

    function getTotalShortUrls() {
        $this->db->from("shortened_urls as su");
        $this->db->join("tbl_users as tu", "tu.user_id = su.user_id");
        return $this->db->get()->num_rows();
    }

    function getShortUrls($search=null, $order=null, $limit=null, $offset=null) {
        $this->db->select("su.id, tu.user_id, tu.username, su.url, su.short_url, su.created_at");
        $this->db->from("shortened_urls as su");
        $this->db->join("tbl_users as tu", "tu.user_id = su.user_id");

        if (isset($search) && $search != "") {
            $this->db->group_start();
            $this->db->like("su.url", $search);
            $this->db->or_like(array("su.short_url" => $search, "tu.user_id" => $search, "tu.username" => $search));
            $this->db->group_end();
        }

        if (isset($order)) {
            $this->db->order_by($order["by"], $order["arrange"]);
        }

        if (isset($limit) && isset($offset)) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->get()->result();
    }

    function createShortURL($user_id, $url_slug, $shorturl_slug) {
        $this->db->from("shortened_urls");
        $this->db->where("short_url", $shorturl_slug);

        $count = $this->db->get()->num_rows();
        if ($count <= 0) {
            $data = array(
                "user_id" => $user_id,
                "url" => $url_slug,
                "short_url" => $shorturl_slug
            );
            $this->db->insert("shortened_urls", $data);

            if ($this->db->affected_rows() > 0) {
                return true;
            }
        }

        return false;
    }

    function editShortURL($user_id, $id, $url_slug, $shorturl_slug) {
        $this->db->where("id", $id);
        $this->db->update("shortened_urls", ["url" => $url_slug, "short_url" => $shorturl_slug]);

        if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
    }

    function deleteShortURL($id) {
        $this->db->where("id", $id);
        $delete = $this->db->delete("shortened_urls");

        if ($this->db->affected_rows() > 0) {
			return true;
		} else {
			return false;
		}
    }
}