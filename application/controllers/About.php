<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class About extends CI_Controller {

	public function __construct () 	{
		parent::__construct();		
		 $this->load->model('about_model');
		 $this->load->model('content_model');
		 $this->load->model('news_model');
		$this->load->library('template');
	}

	 
	public function index() {
		$data['page_description'] = $this->about_model->get_content();
		$data['content'] = $this->content_model->get_content(1);
		$data['article'] = $this->news_model->getEntries(true, 1);
		$this->template->set_layout(DEFAULT_LAYOUT)->build('home/about', $data);
	}
}