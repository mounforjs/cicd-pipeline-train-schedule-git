<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Short extends CI_Controller {

	public function __construct () {
		parent::__construct();

		$this->load->library('template');
        $this->load->model('short_model'); 
	}

    public function index() {
        redirect($this->short_model->getUrl($this->uri->uri_string()));
    }

    public function error_404() {
        $this->template->set_layout(DEFAULT_LAYOUT)->build('layouts/error_404');
    }

    public function check_short_url() {
		checkAdminLogin();

		$short = sanitizeInput($this->input->post("short", true), FILTER_SANITIZE_STRING);

		$exists = $this->short_model->doesURLExist($short);
		if ($exists) {
			$response = array("status" => "fail", "message" => "shortened url not available");
		} else {
			$response = array("status" => "success", "message" => "shortened url available");
		}

		echo json_encode($response);
	}
}