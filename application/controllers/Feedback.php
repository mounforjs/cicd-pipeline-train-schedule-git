<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once __DIR__ . '/Email.php';

class Feedback extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('feedback_model');
    }

    public function user_feedback() {
        $subcat = sanitizeInput($this->input->post('subcat'), FILTER_VALIDATE_INT);
        $rating = sanitizeInput($this->input->post('rating'), FILTER_VALIDATE_INT);
        $winwinrating = sanitizeInput($this->input->post('winwinrating'), FILTER_VALIDATE_INT);

        $inputs = array(
            'user_id' => ($this->session->userdata('user_id') ? $this->session->userdata('user_id') : 0),
			'category_id' => (isset($subcat) ? $subcat : 0),
			'rating' => (isset($rating) ? $rating : 0),
			'winwinrating' => isset($winwinrating) ? $winwinrating : 0,
			'feedback_description' => sanitizeInput($this->input->post('feedback_desc_editor', true), FILTER_SANITIZE_STRING),
			'page_url' => sanitizeInput($this->input->post('current_url'), FILTER_VALIDATE_URL),
        );

        $images = $this->input->post('feedback_images_url');

        $id = $this->feedback_model->insert_user_feedback($inputs, $images);
        if (!isset($id)) {
            echo 'failed';
            exit();
        } else {
            $emailData = $this->feedback_model->get_feedback($id);

            $body = $this->load->view('emails/feedback', $emailData, true);
			$subject = "A user has submitted feedback!";
			$email = "support@winwinlabs.org";
			Email::index($email, $subject, $body);

            echo 'success';
        }
    }

    public function manage_feedback() {
        $data['breadcrumb'] = array(asset_url('dashboard') => 'Dashboard', asset_url('account/manage_admin') => 'Admin Panel', '#' => 'Manage Feedback');

        $data['feedback_list'] = $this->feedback_model->get_all_feedbacks();
        $this->load->account_template('account/manage_feedback/index', $data);
    }
    public function manage_gal() {
        $data['feedback_list'] = $this->feedback_model->get_all_feedbacks();
        $this->load->account_template('account/manage_feedback/index_gal', $data);
    }

    public function manage_page_links() {
        $data['page_list'] = $this->feedback_model->get_all_page_links();
        $this->load->account_template('account/manage_feedback/index', $data);
    }

    public function resize_image($sourcePath, $desPath, $width = '500', $height = '500') {
        $this->load->library('image_lib');

        $this->image_lib->clear();
        $config['image_library'] = 'gd2';
        $config['source_image'] = $sourcePath;
        $config['new_image'] = $desPath;
        $config['quality'] = '100%';
        $config['create_thumb'] = TRUE;
        $config['maintain_ratio'] = false;
        $config['thumb_marker'] = '';
        $config['width'] = $width;
        $config['height'] = $height;
        $this->image_lib->initialize($config);

        if ($this->image_lib->resize()) {
            return true;
        } else {
            return false;
        }
    }
}
