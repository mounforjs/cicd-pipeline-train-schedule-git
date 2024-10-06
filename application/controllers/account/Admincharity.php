<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admincharity extends CI_Controller {

	public function __construct () 	{
		parent::__construct();		
		check_login();

		$this->load->model('charity_model');
	}

	public function index() { 
		$data['breadcrumb'] = array(asset_url('account/dashboard') => 'Dashboard', asset_url('account/manage_admin') => 'Admin Panel', '#' => 'Manage Admin Fundraise');

	    $data['loginuserdetails'] = $this->user_model->get_loginuser(); 
		$data['allcharitydetails'] = $this->charity_model->get_charity_all_default();

		$this->load->account_template('account/admincharity/index',$data); 
	}
	
	public function admincharityname() { 
		$fund_id = '';
		if ($this->input->get('fund_id')) {
			$fund_id = $this->input->get('fund_id');
		}
		$cname = $this->input->Post('charity_name');
		
		$data = $this->charity_model->getadmincharityname($cname, $fund_id);
		echo $data;
	}
	
	public function addadmin_charity() {
		$this->form_validation->set_rules('name','Charity Name','trim|required');
		$this->form_validation->set_rules('address','Charity Address','trim|required');
		$this->form_validation->set_rules('charity_url','Charity URL','trim|required');
		$this->form_validation->set_rules('contact','Charity Contact Personnel','trim|required');
		$this->form_validation->set_rules('phone','Charity Phone','trim|required');
		$this->form_validation->set_rules('description','Charity Description','trim|required');
		$this->form_validation->set_rules('fundraise_authorize', 'Authorization for Non-Profit', 'trim|required');

		if ($this->input->post('fundraise_type')=='charity') {
			$this->form_validation->set_rules('tax_id', 'Fundraise Tax ID', 'trim|required');
		}

		if ($this->form_validation->run() == FALSE) {
			redirect($this->uri->uri_string());
		} else {
			$data = $this->charity_model->create_fundraiser();
			echo $data; exit;
		}
	}

	public function update_admin_charity() {
		$this->form_validation->set_rules('name','Charity Name','trim|required');
		$this->form_validation->set_rules('address','Charity Address','trim|required');
		$this->form_validation->set_rules('charity_url','Charity URL','trim|required');
		$this->form_validation->set_rules('contact','Charity Contact Personnel','trim|required');
		$this->form_validation->set_rules('phone','Charity Phone','trim|required');
		$this->form_validation->set_rules('order','Charity Order','trim|required');
		$this->form_validation->set_rules('description','Charity Description','trim|required');
		$this->form_validation->set_rules('fundraise_authorize', 'Authorization for Non-Profit', 'trim|required');

		if ($this->input->post('fundraise_type')=='charity') {
			$this->form_validation->set_rules('tax_id', 'Fundraise Tax ID', 'trim|required');
		}
		
		if ($this->form_validation->run() == FALSE) {
			echo '5';exit;
		} else {
			$data = $this->charity_model->updateexist_admincharity();
			echo $data; exit;
		}
	}
}