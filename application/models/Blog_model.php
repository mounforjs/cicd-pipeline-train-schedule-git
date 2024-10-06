<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Blog_model extends CI_Model {
    function __construct() {
        parent::__construct();
    }

    public function getEntries($published=true, $limit=5) {
        $this->db->select("be.id, be.title, be.slug, be.excerpt, be.featured_image, be.text, be.published, tu.firstname, tu.lastname, be.DATE_CREATED, be.DATE_EDITED");
        $this->db->from("blog_entries as be");
        $this->db->join("tbl_users as tu", "be.author = tu.user_id", "left");
        if ($published == true) {
            $this->db->where("published", 1);
        }
        $this->db->order_by("DATE_CREATED", "DESC");
        if ($limit != -1) {
            $this->db->limit($limit);
        }

        $result = $this->db->get()->result_array();

        return $result;
    }

    public function getEntry($slug, $published=true) {
        $this->db->select("be.id, be.title, be.slug, su.short_url, be.excerpt, be.featured_image, be.text, be.published, tu.firstname, tu.lastname, be.DATE_CREATED, be.DATE_EDITED");
        $this->db->from("blog_entries as be");
        $this->db->join("tbl_users as tu", "be.author = tu.user_id", "left");
        $this->db->join("shortened_urls as su", "su.url = CONCAT('blog/post/', be.slug)", "left");

        if ($published == true) {
            $this->db->where(array("published" => 1, "slug" => $slug));
        } else {
            $this->db->where(array("slug" => $slug));
        }

        $result = $this->db->get()->result_array();

        return $result;
    }

    public function addBlogPost($user_id, $title, $shorturl, $published, $excerpt, $featured_image, $content) {
        $originalSlug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
        $date = date("Y-m-d H:i:s");

        $slug = $this->findNewSlug($originalSlug);

        $data = array("title" => trim($title, " "), "published" => $published, "author" => $user_id, "slug" => $slug, "featured_image" => $featured_image, "excerpt" => $excerpt, "text" => $content, "DATE_EDITED" => $date, "DATE_EDITED" => NULL);
        $this->db->trans_start();
        $insert = $this->db->insert("blog_entries", $data);

        if (isset($shorturl) && !empty($shorturl)) {
            $this->db->from("shortened_urls");
            $this->db->where("short_url", $shorturl);
            $count = $this->db->get()->num_rows();
    
            if ($count <= 0) { 
                $data = array(
                    "user_id" => $user_id,
                    "url" => "blog/post/" . $slug,
                    "short_url" => $shorturl
                );
                $this->db->insert("shortened_urls", $data);
            }
        }

        $this->db->trans_complete();

        return [$insert, $slug];
    }

    public function updateBlogPost($id, $title, $shorturl, $published, $excerpt, $featured_image, $content) {
        $this->db->where("id", $id);
        $oldData = $this->db->get("blog_entries")->row();

        $date = date("Y-m-d H:i:s");

        if ($title != $oldData->title) {
            $newSlug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
            $slug = $this->findNewSlug($newSlug);
        } else {
            $slug = $oldData->slug;
        }

        $data = array(
            "title" => trim($title, " "),
            "published" => $published,
            "slug" => $slug,
            "featured_image" => $featured_image,
            "excerpt" => $excerpt,
            "text" => $content,
            "DATE_EDITED" => $date
        );
        $this->db->trans_start();
        $this->db->where("id", $id);
        $update = $this->db->update("blog_entries", $data);

        if (!empty($shorturl)) {
            $this->db->from("shortened_urls");
            $this->db->where("url", ("blog/post/" . $oldData->slug));
            $this->db->or_where("short_url", $shorturl);
            $result = $this->db->get()->row();
    
            $data = array(
                "user_id" => $this->session->userdata("user_id"),
                "url" => "blog/post/" . $slug,
                "short_url" => $shorturl
            );

            if (!isset($result)) { 
                $this->db->insert("shortened_urls", $data);
            } else {
                $oldSlug = ("blog/post/" . $oldData->slug);
                $this->db->where("url", $oldSlug);
                $this->db->update("shortened_urls", $data);
            }
        } else {
            $this->db->where("url", ("blog/post/" . $oldData->slug));
            $this->db->delete("shortened_urls");
        }

        $this->db->trans_complete();

        return [$update, $slug];
    }

    public function deleteBlogPost($id) {
        $this->db->from("blog_entries as be");
        $this->db->where("id", $id);
        $blog = $this->db->get()->row();

        $this->db->trans_start();
        $this->db->where("id", $id);
        $delete = $this->db->delete("blog_entries");

        $this->db->where("url", ("blog/post/" . $blog->slug));
        $this->db->delete("shortened_urls");

        $this->db->trans_complete();

        return $delete;
    }

    public function findNewSlug($original) {
        $slug = $original;
        $count = 1;
        while(true) {
            $this->db->select("count(id) as count");
            $this->db->from("blog_entries");
            $this->db->where("slug =", $slug);

            $result = $this->db->get()->result_array();

            if ($result[0]["count"] > 0) {
                $slug = increment_string($original, "-", $count);
                $count++;
            } else {
                break;
            }
        }

        return $slug;
    }
}