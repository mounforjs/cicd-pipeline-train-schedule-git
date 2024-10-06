<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Email extends CI_Controller {

	public function __construct() {
		parent::__construct();
	}

	static public function index($email, $subject, $body) {
		$ci = &get_instance();

		$config = array();
		$config['useragent']    = "CodeIgniter";
		$config['mailpath']     = "/usr/bin/sendmail";
		$config['protocol']     = "smtp";
		$config['smtp_host']    = "ssl://smtp.gmail.com";
		$config['smtp_port']    = "465";
		$config['smtp_timeout'] = '7';

		$config['smtp_user']    = "support@winwinlabs.org";
		$config['smtp_pass']    = "Support2024!!!";
		$config['mailtype']     = 'html';
		$config['charset']      = 'utf-8';
		$config['newline']      = "\r\n";
		$config['wordwrap']     = TRUE;

		$ci->load->library('email');
		$ci->email->initialize($config);
		$ci->email->from('support@winwinlabs.org', 'WinWinLabs');
		$ci->email->to($email);
		$ci->email->subject($subject);
		$ci->email->set_mailtype("html");
		$ci->email->message($body);
		$ci->email->send();
	}
}
