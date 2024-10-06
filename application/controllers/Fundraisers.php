<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once __DIR__ . '/Email.php';

class Fundraisers extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->model('charity_model');
        $this->load->library('template');
        $this->load->library('session');
        $this->load->library('Gamedata');

        $this->template->set_breadcrumb('Home', asset_url());

         // condition for Supporter and Creator
         if(checkSupporterLogin() || checkContentCreatorLogin()) { 
            redirect(base_url('admin'));
        }
    }

    public function index() {
        check_login();
        $data['default_fundraiser']        = $this->charity_model->get_user_default_fundraise();
        $data['default_fundraiser']->Image = getImagePathSize($data['default_fundraiser']->Image,'beneficiary_info_logo');
        $data['default_fundraiser']->icon  = getFundraiseIcon($data['default_fundraiser']->fundraise_type);
        $data['default_fundraiser']->totalRaised = $this->charity_model->get_total_raised($data['default_fundraiser']->slug)->raised;
        $data["search"] = $this->charity_model->get_beneficiary_list();

        $data['my_created_fundraiser_list'] = $this->charity_model->get_fundraise_created_byuser("" , "", 0, 4);
        $data['my_supported_fundraiser_list'] = $this->charity_model->get_fundraise_supported_byuser("" , "", 0, 4);
        
        foreach ($data['my_supported_fundraiser_list'] as $key => $value) {
            $data['my_supported_fundraiser_list'][$key]['Image'] = getImagePathSize($value['Image'], 'beneficiary_card');
            $data['my_supported_fundraiser_list'][$key]['icon']  = getFundraiseIcon($value['fundraise_type']);
        }

        foreach ($data['my_created_fundraiser_list'] as $key => $value) {
            $data['my_created_fundraiser_list'][$key]['Image'] = getImagePathSize($value['Image'], 'beneficiary_card');
            $data['my_created_fundraiser_list'][$key]['icon']  = getFundraiseIcon($value['fundraise_type']);
        }


        $this->template->set_breadcrumb('Manage Beneficiary', asset_url('fundraisers/manage'));
        $this->template->set_layout(DEFAULT_LAYOUT)->build('fundraisers/index', $data);
    }

    public function show() {
        $type = $this->uri->segment('3') != null ? $this->uri->segment('3') : "all";
        $fundraiserSlug = $this->uri->segment('4') != null ? $this->uri->segment('4') : "";

        if (!in_array($type, ['all', 'created', 'supported'])) {
            $type = 'all';
        }

        if (!empty($fundraiserSlug)) {
            $data              = $this->charity_model->getFundraiserDetailBySlug($fundraiserSlug);
            $data->Image       = getImagePathSize($data->Image, 'beneficiary_info_logo');
            $data->totalRaised = $this->charity_model->get_total_raised($data->slug);
            
            $filters = array("beneficiary" => $data->slug);
            $data->game_list = $this->gamedata->getGamedata($filters, 'play', 0, 3);

            $this->template->set_breadcrumb('Beneficiaries', asset_url('fundraisers/show'));
            $this->template->set_breadcrumb($data->name, '');

            $this->template->set_layout(DEFAULT_LAYOUT)->build('fundraisers/fundraiser_detail', $data);
        } else {
            $userprofile = getprofile();

            $data = array();
            $data['usertype'] = $userprofile->usertype;
            $data['default_fundraiser'] = $userprofile->default_fundraise;
            $data['type'] = $type;

            if ($type == 'created') {
                $data["fundraisers"] = $this->charity_model->get_fundraise_created_byuser();
            } else if ($type == 'supported') {
                $data["fundraisers"] = $this->charity_model->get_fundraise_supported_byuser();
            } else if ($type == 'all') {
                $data["fundraisers"] = $this->charity_model->get_all_fundraiser_list('all', '', false, 8, 0);
            }

            $data["search"] = $this->charity_model->get_beneficiary_list($type, null, true);

            foreach ($data["fundraisers"] as $key => $value) {
                $data["fundraisers"][$key]['Image'] = getImagePathSize($value['Image'], 'fundraisers_card');
                $data["fundraisers"][$key]['icon']  = getFundraiseIcon($value['fundraise_type']);
            }
            
            if ($this->session->userdata("user_id") && getprofile()->usertype == 2 and $this->session->userdata('adminfundraisersEditId')) {
                $data['openfundraisers'] = $this->session->userdata('adminfundraisersEditId');
                $this->session->unset_userdata('adminfundraisersEditId');
            }

            $this->template->set_breadcrumb('fundraisers', asset_url('fundraisers/show'));
            $this->template->set_layout(DEFAULT_LAYOUT)->build('fundraisers/fundraisers_list', $data);
        }
    }

    public function showForEdit($slug) {
        $exist = $this->db->where('slug', $slug)->get('charity')->row();
        if (isset($exist->id)) {
            $this->session->set_userdata('adminfundraisersEditId', $exist->id);
            redirect('fundraisers/show/all');
        }
    }

    public function getDefaultFundraiseDetailsOnChange() {
        $slug            = sanitizeInput($this->input->post('slug'), FILTER_VALIDATE_URL);
        if (empty($slug)) {
            $result = $this->charity_model->get_user_default_fundraise();
        } else {
            $result = $this->charity_model->get_fundraise($slug);
        }
        
        $result->Image = getImagePathSize($result->Image, 'beneficiary_info_logo');
        $result->icon  = getFundraiseIcon($result->fundraise_type);
        if ($result->slug != $slug) {
            $result->raised = $this->charity_model->get_total_raised($result->slug)->raised;
        } else {
            $result->raised = $this->charity_model->get_total_raised($slug)->raised;
        }

        echo json_encode($result);
    }

    public function getBeneficiaries() {
        $posted_type = sanitizeInput($this->input->post('type'), FILTER_SANITIZE_STRING);

        $type          = (!empty($posted_type)) ? $posted_type : 'all';
        $sub_type      = sanitizeInput($this->input->post('sub_type'), FILTER_SANITIZE_STRING);
        $slug = sanitizeInput($this->input->post('slug'), FILTER_VALIDATE_URL);

        $offset = sanitizeInput($this->input->post('offset'), FILTER_VALIDATE_INT);

        if ($type == 'created') {
            $data = $this->charity_model->get_fundraise_created_byuser($sub_type, $slug, $offset);
        } else if ($type == 'supported') {
            $data = $this->charity_model->get_fundraise_supported_byuser($sub_type, $slug, $offset);
        } else {
            $data = $this->charity_model->get_all_fundraiser_list($sub_type, $slug, false, 8, $offset);
        }

        $search = $this->charity_model->get_beneficiary_list($type, $sub_type, true);

        foreach ($data as $key => $value) {
            $data[$key]['Image'] = getImagePathSize($value['Image'], 'beneficiary_card');
            $data[$key]['icon']  = getFundraiseIcon($value['fundraise_type']);
        }

        $userprofile = getprofile();
        echo json_encode(array('usertype' => $userprofile->usertype, 'default_fundraiser' => $userprofile->default_fundraise, 'data' => $data, 'search' => $search));
    }

    public function makeDefaultFundraiser() {
        $slug   = sanitizeInput($this->input->post('slug'), FILTER_VALIDATE_URL);

        $data = $this->charity_model->updateDefaultFundraiser($slug);
        echo $data;
    }

    public function deleteCreatedFundraiser() {
        $slug  = sanitizeInput($this->input->post('slug'), FILTER_VALIDATE_URL);

        $del = $this->charity_model->deleteCreatedFundraiser($slug);
        echo $del;
    }

    public function removeSupportedFundraiser() {
        $slug  = sanitizeInput($this->input->post('slug'), FILTER_VALIDATE_URL);

        $del = $this->charity_model->removeSupportedFundraiser($slug);
        echo $del;
    }

    public function getEditedFundraiserDetails() {
        $slug     = sanitizeInput($this->input->post('slug'), FILTER_VALIDATE_URL);
        $result = $this->charity_model->getEditedFundraiserDetails($slug);
        $result->Image = getImagePathSize($result->Image, 'beneficiary_info_logo');
        $result->icon  = getFundraiseIcon($result->fundraise_type);

        if (getprofile()->usertype == 2) {
            $result->isAdmin = 1;
            $pendingRequest  = $this->charity_model->getPendingEditRequestForFundraiser($slug);
            if (isset($pendingRequest->reason)) {
                $result->reason = $pendingRequest->reason;
            } else {
                $result->reason = '';
            }
        } else {
            $result->isAdmin = 0;

            $isPendingRequestExist = $this->charity_model->getPendingEditRequestForFundraiser($slug);
            if (isset($isPendingRequestExist->reason)) {
                echo json_encode(array('status' => 'failed', 'msg' => 'pending request'));
                return;
            }

        }

        echo json_encode($result);
    }

    public function add_edit_fundraiser() {
        $this->form_validation->set_error_delimiters('', '');
        $this->form_validation->set_rules('form_charity_name', 'Charity Name', 'trim|required', array());
        $this->form_validation->set_rules('form_charity_desc', 'Charity Description', 'trim|required', array());

        $fundraise_type = sanitizeInput($this->input->post('fundraise_type'), FILTER_SANITIZE_STRING);
        if ($fundraise_type == 'charity') {
            $this->form_validation->set_rules('form_charity_tax', 'Fundraise Tax ID', 'trim|required', array());
        }

        $admin = getprofile(286);
        $user = getprofile();

        if ($this->form_validation->run() == false) {
            echo json_encode(array("status" => "failed", "msg" => validation_errors()));
        } else {
            /**When Admin Submit form**/
            $slug = sanitizeInput($this->input->post('slug'), FILTER_VALIDATE_URL);
            $name = sanitizeInput($this->input->post('form_charity_name', true), FILTER_SANITIZE_STRING);
            $reasonDescription = sanitizeInput($this->input->post('reasonDescription', true), FILTER_SANITIZE_STRING);

            $isUpdate = !empty($slug) ? true : false;

            if (getprofile()->usertype == 2) {
                if (empty($slug)) {
                    $insert_id = $this->charity_model->create_fundraiser($user->id, $slug, $name, $isUpdate);
                    if ($insert_id !== FALSE) {
                        $emailData = array("username" => $user->username, "beneficiary" => $name);
                        $subject = "Beneficiary Created!";
                        $body = $this->load->view('emails/new-beneficiary-email', $emailData, true);

                        sendEmail("beneficiary_approved", $user->user_id, $subject, $body);

                        echo json_encode(array("status" => 'success', "msg" => "Beneficiary created!"));
                    } else {
                        echo json_encode(array("status" => 'failed', "msg" => "Beneficiary creation failed!"));
                    }
                } else {
                    $beneficiary = $this->charity_model->getAllBeneficiaryDetails($slug);
                    $creator = getprofile($beneficiary->user_id);

                    $insert_id = $this->charity_model->create_fundraiser($creator->user_id, $slug, $name, $isUpdate);
                    if ($insert_id !== FALSE) {
                        $emailData = array("username" => $creator->username, "beneficiary" => $beneficiary);
                        $subject = "Beneficiary Approved!";
                        $body = $this->load->view('emails/beneficiary-approved-email', $emailData, true);

                        sendNotificationAndEmail("beneficiary_approved", $creator->user_id, $admin->user_id, "Your beneficiary, ${name}, has been approved!", "fundraiser", "approved", null, $beneficiary->id, $subject, $body);

                        echo json_encode(array("status" => 'success', "msg" => "Beneficiary approved!"));
                    } else {
                        echo json_encode(array("status" => 'failed', "msg" => "Beneficiary approval failed!"));
                    }
                }

                return;
            }
            /**When Admin Submit form**/

            $exists = $this->charity_model->getFundraiserDetailBySlug($slug);
            if ($exists) {
                if (empty($reasonDescription)) {
                    echo json_encode(array("status" => "failed", "msg" => "Reason for edit not provided."));
                    return;
                }

                $beneficiary = $exists;
                $result = $this->charity_model->insertFundraiserEditReason($slug, $reasonDescription);

                if ($result) {
                    // For admin email -----------------------------------------------
                    $dataAdmin['adminUsername'] = $admin->username;
                    $dataAdmin['username'] = $user->username;
                    $dataAdmin['beneficiary'] = $beneficiary;
                    $bodyAdmin = $this->load->view('emails/edit-fundraiser-admin-email', $dataAdmin, true);
                    Email::index('support@winwinlabs.org', 'Fundraiser Edit Requested', $bodyAdmin);
                    // For admin email -----------------------------------------------

                    // For logged in user email -----------------------------------------------
                    $data['username'] = $user->username;
                    $data['beneficiary'] = $beneficiary;
                    $body = $this->load->view('emails/edit-fundraiser-email', $data, true);
                    sendEmail("beneficiary_edit_request", $user->user_id, 'Fundraiser Edit Request', $body);
                    // For logged in user email -----------------------------------------------
                }

                $msg = ($result) ? 'Thanks for your sending your request, we will review and contact you soon!' : 'We ran into an error creating your request.';
                $status = ($result) ? 'success' : 'failed';

                ob_clean();
                echo json_encode(array("status" => $status, "msg" => $msg));
            } else {
                $newBeneficiaryId = $this->charity_model->create_fundraiser($user->user_id, $slug, $name, $isUpdate);
                $beneficiary = $this->charity_model->getFundraiserDetailById($newBeneficiaryId);
                
                if ($beneficiary) {
                    // send email to admin that user has created a new beneficiary ----------------------
                    $dataAdmin['beneficiary'] = $beneficiary;
                    $dataAdmin['email'] = $user->email;
                    $dataAdmin['adminUsername'] = $admin->username;
                    $dataAdmin['username'] = $user->username;
                    $bodyAdmin = $this->load->view('emails/new-beneficiary-admin-email', $dataAdmin, true);
                    Email::index('newbeneficiary@winwinlabs.org', 'New Beneficiary Info', $bodyAdmin);
                    // ----------------------------------------------

                    $emailData = array("username" => $user->username, "beneficiary" => $beneficiary);
                    $subject = "Beneficiary Created!";
                    $body = $this->load->view('emails/new-beneficiary-email', $emailData, true);

                    sendEmail("beneficiary_created", $user->user_id, $subject, $body);

                    echo json_encode(array("status" => 'success', "msg" => "Beneficiary created!"));
                } else {
                    echo json_encode(array("status" => 'failed', "msg" => "We ran into an issue creating beneficiary." . $beneficiary));
                }
            }
        }
    }

    public function getBeneficiaryList() {
        $sub_type = sanitizeInput($this->input->get('sub_type'), FILTER_SANITIZE_STRING);
        $result = $this->charity_model->get_beneficiary_list('all', $sub_type);
        echo json_encode($result);
    }

    public function getCharityDetail() {
        check_login();

        $slug = sanitizeInput($this->input->get('slug'), FILTER_VALIDATE_URL);

        $result = $this->charity_model->get_fundraise($slug);  
        $result->Image = getImagePathSize($result->Image, 'beneficiary_info_logo');
        $result->icon  = getFundraiseIcon($result->fundraise_type);
        if ($result->slug != $slug) {
            $result->raised = $this->charity_model->get_total_raised($result->slug)->raised;
        } else {
            $result->raised = $this->charity_model->get_total_raised($slug)->raised;
        }

        $data = array("play_game" => true, "fundraisers" => array((array)$result));
        $view = $this->load->view("fundraisers/partials/fundraiserCard", $data, true);

        echo $view;
    }
}