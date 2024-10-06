<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Blog extends CI_Controller {

	public function __construct () 	{
		parent::__construct();		
		$this->load->model('blog_model');
		$this->load->model('news_model');
		$this->load->library('template');
	}

	 public function index() {  
		if ($this->session->userdata('user_id') == 112) {
			$data['blog_posts'] = $this->blog_model->getEntries(false);
			$data['admin'] = true;
		} else {
			$data['blog_posts'] = $this->blog_model->getEntries();
			$data['admin'] = false;
		}

		$this->template->setSeo(2);
		$this->template->set_layout(DEFAULT_LAYOUT)->build('blog/index.php', $data);
	 }
	 
	 public function post($slug = "") {
		if ($slug == "") {
			redirect('blog');
		}

		if ($this->session->userdata('user_id') == 112) {
			$data['blog_post'] = $this->blog_model->getEntry($slug, false);
			$data['admin'] = true;
		} else {
			$data['blog_post'] = $this->blog_model->getEntry($slug);
			$data['admin'] = false;
		}

		if (count($data['blog_post']) == 0) {
			redirect('blog');
		}

		$this->template->setSeo(2);
		$this->template->set_layout(DEFAULT_LAYOUT)->build('blog/blog.php', $data);
	}

	public function add() {
		$data["user"] = $this->news_model->getUser($this->session->userdata('user_id'));

		if ($this->session->userdata('user_id') == 112) {
			$this->template->setSeo(2);
			$this->template->set_layout(DEFAULT_LAYOUT)->build('blog/add.php', $data);
		} else {
			redirect("blog");
		}
	}

	public function addBlogPost() {
		if ($this->session->userdata('user_id') != 112) {
			redirect("blog");
			exit();
		}

		$response = array("status" => "fail");

		$user = $this->session->userdata('user_id');

		$validate = $this->validateInputs();
		if (!$validate["valid"]) {
			$response["message"] = $validate["failed"];
		} else {
			$blog = $validate["data"];

			$result = $this->blog_model->addBlogPost($user, $blog->title, $blog->shorturl, $blog->published, $blog->excerpt, $blog->featured_image, $blog->content);
			if ($result[0] == true) {
				$response = array("status" => "success", "message" => "added blog post", "slug" => $result[1]);
			} else {
				$this->output->set_status_header('406');
				$response["message"] = "could not add blog post";
			}
		}

		echo json_encode($response);
	}

	public function updateBlogPost() {
		if ($this->session->userdata('user_id') != 112) {
			redirect("blog");
			exit();
		}

		$response = array("status" => "fail");

		$id = sanitizeInput($this->input->post("id"), FILTER_VALIDATE_INT);
		$validate = $this->validateInputs();
		if (!$id || !$validate["valid"]) {
			$result["message"] = $validate["failed"];
		} else {
			$blog = $validate["data"];

			$result = $this->blog_model->updateBlogPost($id, $blog->title, $blog->shorturl, $blog->published, $blog->excerpt, $blog->featured_image, $blog->content);
			if ($result[0] == true) {
				$response = array("status" => "success", "message" => "updated blog post", "slug" => $result[1]);
			} else {
				$this->output->set_status_header('406');
				$response["message"] = "could not update blog post";
			}
		}

		echo json_encode($response);
	}

	public function deleteBlogPost() {
		if ($this->session->userdata('user_id') != 112) {
			redirect("blog");
			exit();
		}

		$response = array("status" => "fail");

		$id = sanitizeInput($this->input->post("id"), FILTER_VALIDATE_INT);

		$result = $this->blog_model->deleteBlogPost($id);
		if ($result) {
			$response = array("status" => "success", "message" => "deleted blog post");
		} else {
			$this->output->set_status_header('406');
			$response["message"] = "could not delete blog post";
		}

		echo json_encode($response);
	}

	private function validateInputs() {
		$blog = (object) array(
			"title" => sanitizeInput($this->input->post("title", true), FILTER_SANITIZE_STRING),
			"shorturl" => sanitizeInput($this->input->post("shorturl", true), FILTER_SANITIZE_STRING),
			"published" => sanitizeInput($this->input->post("published"), FILTER_VALIDATE_INT),
			"excerpt" => sanitizeInput($this->input->post("excerpt", true), FILTER_SANITIZE_FULL_SPECIAL_CHARS),
			"featured_image" => sanitizeInput($this->input->post("featured_image", true), FILTER_VALIDATE_URL),
			"content" => sanitizeInput($this->input->post("content", true), FILTER_SANITIZE_FULL_SPECIAL_CHARS)
		);

		return validateInputs($blog);
	}
}