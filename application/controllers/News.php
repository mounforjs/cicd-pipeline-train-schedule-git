<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class News extends CI_Controller {

	public function __construct () 	{
		parent::__construct();		
		$this->load->model('news_model');
		$this->load->library('template');
	}

	 public function index() {
		if ($this->session->userdata('user_id') == 112) {
			$data['articles'] = $this->news_model->getEntries(false);
			$data['admin'] = true;
		} else {
			$data['articles'] = $this->news_model->getEntries();
			$data['admin'] = false;
		}

		$this->template->setSeo(2);
		$this->template->set_layout(DEFAULT_LAYOUT)->build('news/index.php', $data);
	 }

	public function article($slug = "") {
		if ($slug == "") {
			redirect('news');
		}

		if ($this->session->userdata('user_id') == 112) {
			$data['article'] = $data['article'] = $this->news_model->getEntry($slug, false);;
			$data['admin'] = true;
		} else {
			$data['article'] = $data['article'] = $this->news_model->getEntry($slug);
			$data['admin'] = false;
		}

		if (count($data['article']) == 0) {
			redirect('news');
		}

		$this->template->setSeo(2);
		$this->template->set_layout(DEFAULT_LAYOUT)->build('news/article.php', $data);
	}

	public function add() {
		$data["user"] = $this->news_model->getUser($this->session->userdata('user_id'));

		if ($this->session->userdata('user_id') == 112) {
			$this->template->setSeo(2);
			$this->template->set_layout(DEFAULT_LAYOUT)->build('news/add.php', $data);
		} else {
			redirect("news");
		}
	}

	public function addArticle() {
		if ($this->session->userdata('user_id') != 112) {
			redirect("blog");
			exit();
		}

		$response = array("status" => "fail");

		$user = $this->session->userdata('user_id');

		$validate = $this->validateInputs();
		if (!$validate["valid"]) {
			$result["message"] = $validate["failed"];
		} else {
			$news = $validate["data"];

			$result = $this->news_model->addArticle($user, $news->title, $news->shorturl, $news->published, $news->excerpt, $news->featured_image, $news->content);
			if ($result[0] == true) {
				$response = array("status" => "success", "message" => "added article", "slug" => $result[1]);
			} else {
				$this->output->set_status_header('406');
				$response["message"] = "could not add article";
			}
		}

		echo json_encode($response);
	}

	public function updateArticle() {
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
			$news = $validate["data"];

			$result = $this->news_model->updateArticle($id, $news->title, $news->shorturl, $news->published, $news->excerpt, $news->featured_image, $news->content);
			if ($result[0] == true) {
				$response = array("status" => "success", "message" => "updated article", "slug" => $result[1]);
			} else {
				$this->output->set_status_header('406');
				$response["message"] = "could not article";
			}
		}

		echo json_encode($response);
	}

	public function deleteArticle() {
		if ($this->session->userdata('user_id') != 112) {
			redirect("blog");
			exit();
		}

		$response = array("status" => "fail");
		$id = sanitizeInput($this->input->post("id"), FILTER_VALIDATE_INT);

		$result = $this->news_model->deleteArticle($id);
		if ($result) {
			$response = array("status" => "success", "message" => "deleted article");
		} else {
			$this->output->set_status_header('406');
			$response["message"] = "could not delete article";
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