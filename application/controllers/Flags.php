<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Flags extends CI_Controller {

	public function __construct () 	{
		parent::__construct();		

		$this->load->model('flags_model');

		check_login();
	}

	public function addFlag() {
		$data = $this->flags_model->insert_flag();
		echo json_encode(array("done"=>$data)); 
	}
}