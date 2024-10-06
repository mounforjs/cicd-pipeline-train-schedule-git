<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Faq extends CI_Controller {

	public function __construct () {
		parent::__construct();		
		$this->load->model('faq_model');
		$this->load->library('template');
	}
	 
	public function index() {
		$data['faq'] = $this->faq_model->getFAQ();
		$this->template->setSeo(2);
		$this->template->set_layout(DEFAULT_LAYOUT)->build('faq/index', $data);
	}
     
	public function category($id) {
        $data['faq'] = $this->faq_model->getSingleCateogoryFAQ($id);
        $data['noSCategory'] = $this->faq_model->getSingleCateogoryFAQ($id, true);
        
        if ($data['faq'] === null) {
            redirect('faq');
        } else {
            $this->template->setSeo(2);
		    $this->template->set_layout(DEFAULT_LAYOUT)->build('faq/category', $data);
        }
	}

	public function question($id) {
		$data['faq'] = $this->faq_model->getQuestion($id);
        
        if ($data['faq'] === null) {
            redirect('faq');
        } else {
            $this->template->setSeo(2);
		    $this->template->set_layout(DEFAULT_LAYOUT)->build('faq/question', $data);
		}
	}

    public function getAllQuestions() {
		$search =  sanitizeInput($this->input->get('search'), FILTER_SANITIZE_STRING);

		$data['faq'] = $this->faq_model->getAllQuestions($search);

		echo json_encode($data);
    }

	public function getCategories() {
		echo json_encode($this->faq_model->getCategories());
	}

    public function getSubCategories() {
		$categoryID = sanitizeInput($this->input->get("catID"), FILTER_VALIDATE_INT);

		echo json_encode($this->faq_model->getSubCategories($categoryID));
	}

	public function getQuestions() {
		$categoryID = sanitizeInput($this->input->get("catID"), FILTER_VALIDATE_INT);
		$subCategoryID = sanitizeInput($this->input->get("subCatID"), FILTER_VALIDATE_INT);

		echo json_encode($this->faq_model->getQuestions($categoryID, $subCategoryID));
	}

	public function addQuestion() {
		$response = "";

		$id = sanitizeInput($this->input->post("id"), FILTER_VALIDATE_INT);
		$question = sanitizeInput($this->input->post("question", true), FILTER_SANITIZE_STRING);
		$answer = sanitizeInput($this->input->post("answer", true), FILTER_SANITIZE_STRING);
		$category = sanitizeInput($this->input->post("category"), FILTER_VALIDATE_INT);
		$subcategory = sanitizeInput($this->input->post("subcategory"), FILTER_VALIDATE_INT);

		$result = $this->faq_model->addQuestion($id, $question, $answer, $category, $subcategory);
		if ($result) {
			$response = array("status" => "success", "message" => "added question");
		} else {
			$response = array("status" => "fail", "message" => "could not add question");
		}
		
		echo json_encode($response);
	}

	public function addCategory() {
		$response = "";

		$id = sanitizeInput($this->input->post("id"), FILTER_VALIDATE_INT);
		$category = sanitizeInput($this->input->post("category"), FILTER_VALIDATE_STRING);

		$result = $this->faq_model->addCategory($id, $category);
		if ($result) {
			$response = array("status" => "success", "message" => "added category");
		} else {
			$this->output->set_status_header('406');
			$response = array("status" => "fail", "message" => "could not add category");
		}
		
		echo json_encode($response);
	}

	public function addSubCategory() {
		$response = "";

		$id = sanitizeInput($this->input->post("id"), FILTER_VALIDATE_INT);
		$category = sanitizeInput($this->input->post("category"), FILTER_VALIDATE_STRING);
		$type = sanitizeInput($this->input->post("type", true), FILTER_VALIDATE_STRING);

		$result = $this->faq_model->addSubCategory($id, $category, $type);
		if ($result) {
			$response = array("status" => "success", "message" => "added subcategory");
		} else {
			$this->output->set_status_header('406');
			$response = array("status" => "fail", "message" => "could not add subcategory");
		}
		
		echo json_encode($response);
	}

	public function updateQuestion() {
		$response = "";

		$oldID = sanitizeInput($this->input->post("oldID"), FILTER_VALIDATE_INT);
		$id = sanitizeInput($this->input->post("id"), FILTER_VALIDATE_INT);
		$question = $this->input->post("question");
		$answer = $this->input->post("answer");
		$category = $this->input->post("category");
		$subcategory = sanitizeInput($this->input->post("subcategory"), FILTER_VALIDATE_STRING);

		$result = $this->faq_model->updateQuestion($oldID, $id, $question, $answer, $category, $subcategory);
		if ($result) {
			$response = array("status" => "success", "message" => "updated question");
		} else {
			$this->output->set_status_header('406');
			$response = array("status" => "fail", "message" => "could not update question");
		}

		echo json_encode($response);
	}

	public function updateCategory() {
		$response = "";

		$oldID = sanitizeInput($this->input->post("oldID"), FILTER_VALIDATE_INT);
		$id = sanitizeInput($this->input->post("id"), FILTER_VALIDATE_INT);
		$category = sanitizeInput($this->input->post("category"), FILTER_VALIDATE_INT);

		$result = $this->faq_model->updateCategory($oldID, $id, $category);
		if ($result) {
			$response = array("status" => "success", "message" => "updated category");
		} else {
			$this->output->set_status_header('406');
			$response = array("status" => "fail", "message" => "could not update category");
		}
		
		echo json_encode($response);
	}

	public function updateSubCategory() {
		$response = "";

		$oldID = sanitizeInput($this->input->post("oldID"), FILTER_VALIDATE_INT);
		$id = sanitizeInput($this->input->post("id"), FILTER_VALIDATE_INT);
		$category = sanitizeInput($this->input->post("category"), FILTER_VALIDATE_INT);
		$type = sanitizeInput($this->input->post("type", true), FILTER_SANITIZE_STRING);

		$result = $this->faq_model->updateSubCategory($oldID, $id, $category, $type);
		if ($result) {
			$response = array("status" => "success", "message" => "updated subcategory");
		} else {
			$this->output->set_status_header('406');
			$response = array("status" => "fail", "message" => "could not update subcategory");
		}
		
		echo json_encode($response);
	}

	public function deleteQuestion() {
		$response = "";

		$id = sanitizeInput($this->input->post("id"), FILTER_VALIDATE_INT);

		$result = $this->faq_model->deleteQuestion($id);
		if ($result) {
			$response = array("status" => "success", "message" => "deleted question");
		} else {
			$this->output->set_status_header('406');
			$response = array("status" => "fail", "message" => "could not delete question");
		}
		
		echo json_encode($response);
	}

	public function deleteCategory() {
		$response = "";

		$id = sanitizeInput($this->input->post("id"), FILTER_VALIDATE_INT);

		$result = $this->faq_model->deleteCategory($id);
		if ($result) {
			$response = array("status" => "success", "message" => "deleted category");
		} else {
			$this->output->set_status_header('406');
			$response = array("status" => "fail", "message" => "could not delete category");
		}
		
		echo json_encode($response);
	}

	public function deleteSubCategory() {
		$response = "";

		$id = sanitizeInput($this->input->post("id"), FILTER_VALIDATE_INT);

		$result = $this->faq_model->deleteSubCategory($id);
		if ($result) {
			$response = array("status" => "success", "message" => "deleted subcategory");
		} else {
			$this->output->set_status_header('406');
			$response = array("status" => "fail", "message" => "could not delete subcategory");
		}

		echo json_encode($response);
	}

	public function getIncrementedID() {
		$response = "";
		$type = sanitizeInput($this->input->post("type", true), FILTER_SANITIZE_STRING);

		$result = $this->faq_model->getIncrementedID($type) + 1;

		echo json_encode($result);
	}
}