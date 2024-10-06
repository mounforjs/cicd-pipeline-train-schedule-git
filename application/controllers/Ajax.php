<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends CI_Controller {

	public function __construct () {
		parent::__construct();	

		$this->load->library('Aws_s3');
	}


	public function uploadImage() {
		$response = $this->aws_s3->upload($_FILES['file'], AWS_Bucket_GameImage);
		if (is_array($response)) {
			echo $response['path'];
		} else {
			echo 'error';
		}
	}
}
?>