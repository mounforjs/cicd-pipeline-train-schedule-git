<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Challenge extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->model('Question_model');
        $this->load->model('Quiz_model');
        $this->load->model('Game_play_model');

        $this->load->helper('url');
        $this->load->library('user_agent');
        $this->load->library('template');
        $this->load->library('session');

        if (!$this->session->userdata('user_id')) {
			redirect('login');
		}

        $this->template->set_breadcrumb('Home', asset_url());
        $this->user_id = $this->session->userdata('user_id');

        // Get the method name from the URL
		$method_name = $this->router->fetch_method();

		// Define the role-based arrays for allowed methods
		$hideForSupporterAndCreator = array('question','quiz');

        // condition for Supporter and Creator
        if(in_array($method_name, $hideForSupporterAndCreator) && (checkSupporterLogin() || checkContentCreatorLogin())) { 
            redirect(base_url('admin'));
        }
    }

    public function question($action=null) {
		if ($action == "add" && $this->input->post()) {
            $data = $this->getQuestionData($this->input->post());
            $data['created_by'] = $this->user_id;
            $data['created_at'] = date('Y-m-d H:i:s');

            $result = $this->Question_model->add($data);
            echo json_encode($result);
		} else if ($action == "edit" && $this->input->post()) {
            $id = sanitizeInput($this->input->post("id"), FILTER_VALIDATE_INT);

			$question = $this->Question_model->getQuestionById($id);
            if (!isset($question)) {
                echo json_encode(array("status" => "failed", "message" => "No such question."));
                return;
            } else if ($question->status == 1 && getprofile()->usertype != '2') {
                echo json_encode(array("status" => "failed", "message" => "Can't edit already approved question!"));
                return;
			} else if ($question->created_by != $this->user_id && getprofile()->usertype != '2') {
                echo json_encode(array("status" => "failed", "message" => "You can't edit someone else's question."));
                return;
            }

            $data = $this->getQuestionData($this->input->post());
            $data['updated_by'] = $this->user_id;
            $data['updated_at'] = date('Y-m-d H:i:s');

            $result = $this->Question_model->update($data, $id);
            echo json_encode($result);
		} else {
            if (!isset($action)) {
                $data["category"] = $this->Question_model->getCategoryList();

                $total = $this->Question_model->getTotalQuestions();
                $data['question'] = $this->Question_model->getQuestions(null, null, 10, 0);

                $data['deferLoading'] = array("filtered" => $total, "total" => $total);

                $this->template->set_breadcrumb('Questions', asset_url('challenge/question'));
                $this->template->set_layout(DEFAULT_LAYOUT)->build('challenge/ques_list', $data);
            } else {
                redirect("challenge/question");
            }
		}
	}

    private function getQuestionData($data) {
        $data['id'] = sanitizeInput($this->input->post('id'), FILTER_VALIDATE_INT);
        $data['category'] = sanitizeInput($this->input->post('category', true), FILTER_VALIDATE_INT);
        $data['type'] = sanitizeInput($this->input->post('type', true), FILTER_SANITIZE_STRING);
        $data['difficulty'] = sanitizeInput($this->input->post('difficulty', true), FILTER_SANITIZE_STRING);
        $data['question'] = $this->input->post('question', true);

        if ($data['type'] == 'multiple') {
            $data['incorrect_answer'] = json_encode(sanitizeInputArray(explode(",",$this->input->post('incorrect_answer', true), 4), FILTER_SANITIZE_STRING));
            $data['correct_answer'] = json_encode(sanitizeInputArray(explode(",",$this->input->post('correct_answer', true), 2), FILTER_SANITIZE_STRING));
            unset($data['boolean_answer']);
        } else if ($data['type'] == 'boolean') {
            $data['boolean_answer'] = sanitizeInput($this->input->post('boolean_answer', true), FILTER_SANITIZE_STRING);
            $data['correct_answer'] = json_encode([($data['boolean_answer'] == 0 ? "False" : "True")]);
            unset($data['incorrect_answer']);
        } else if ($data['type'] == 'one') {
            $data['correct_answer'] = json_encode(sanitizeInputArray($this->input->post('correct_answer', true), FILTER_SANITIZE_STRING));
            unset($data['boolean_answer']);
            unset($data['incorrect_answer']);
        }

        $data['status'] = (getprofile()->usertype == '2' ? sanitizeInput($this->input->post("status"), FILTER_VALIDATE_INT) : 0);

        return $data;
    }

    public function getQuestions() {
		$userfilter = sanitizeInput($this->input->get("userfilter"), FILTER_VALIDATE_BOOLEAN) ? $this->user_id : null;

        $draw = sanitizeInput($this->input->get("draw"), FILTER_VALIDATE_INT);

		$limit = sanitizeInput($this->input->get("length"), FILTER_VALIDATE_INT);
		$offset = sanitizeInput($this->input->get("start"), FILTER_VALIDATE_INT);
		$search = sanitizeInput($this->input->get("search[value]"), FILTER_SANITIZE_STRING);

		$by = sanitizeInput($this->input->get("order[0][column]"), FILTER_VALIDATE_INT);
		$order = array("by" => sanitizeInput($this->input->get("columns[" . $by . "][data]"), FILTER_SANITIZE_STRING), "arrange" => sanitizeInput($this->input->get("order[0][dir]"), FILTER_SANITIZE_STRING));
		
		$total = $this->Question_model->getTotalQuestions($userfilter);
		$question = $this->Question_model->getQuestions($search, $order, null, null, $userfilter);

		$data = array(
			"draw" => $draw,
			"recordsTotal" => $total,
			"recordsFiltered" => count($question),
			"data" => array_slice($question, $offset, $limit)
		);

		echo json_encode($data);
	}

    public function getApprovedQuestions() {
        $userfilter = sanitizeInput($this->input->get("userfilter"), FILTER_VALIDATE_BOOLEAN) ? $this->user_id : null;

        $draw = sanitizeInput($this->input->get("draw"), FILTER_VALIDATE_INT);

        $limit = sanitizeInput($this->input->get("length"), FILTER_VALIDATE_INT);
		$offset = sanitizeInput($this->input->get("start"), FILTER_VALIDATE_INT);
		$search = sanitizeInput($this->input->get("search[value]", true), FILTER_SANITIZE_STRING);

		$by = sanitizeInput($this->input->get("order[0][column]"), FILTER_VALIDATE_INT);
		$order = array("by" => sanitizeInput($this->input->get("columns[" . $by . "][data]"), FILTER_SANITIZE_STRING), "arrange" => sanitizeInput($this->input->get("order[0][dir]"), FILTER_SANITIZE_STRING));
		
		$total = $this->Question_model->getTotalApprovedQuestions($userfilter);
		$question = $this->Question_model->getApprovedQuestions($search, $order, null, null, $userfilter);

		$data = array(
			"draw" => $draw,
			"recordsTotal" => $total,
			"recordsFiltered" => count($question),
			"data" => array_slice($question, $offset, $limit)
		);

		echo json_encode($data);
    }

    public function getRandomQuestions() {
        $userfilter = sanitizeInput($this->input->get("userfilter"), FILTER_VALIDATE_BOOLEAN) ? $this->user_id : null;

        $limit = sanitizeInput($this->input->get("count"), FILTER_VALIDATE_INT);
        $notwhere = sanitizeInput($this->input->get("added"), FILTER_VALIDATE_INT);
        $where = array(
            "category" => sanitizeInputArray($this->input->get("category[]"), FILTER_VALIDATE_INT),
            "difficulty" => sanitizeInputArray($this->input->get("difficulty[]"), FILTER_SANITIZE_STRING),
            "type" => sanitizeInputArray($this->input->get("type[]"), FILTER_SANITIZE_STRING)
        );

        $data = $this->Question_model->getRandomApprovedQuestions($limit, $where, $notwhere, $userfilter);
        
        echo json_encode($data);
    }

    public function getQuestion() {
        $id = sanitizeInput($this->input->get("id"), FILTER_VALIDATE_INT);
        $data = $this->Question_model->getQuestionById($id);

        echo json_encode($data);
    }

	public function quiz($action=null) {
		if ($action == "add" && $this->input->post()) {
            $data = $this->getQuizData($this->input->post());
            $data['created_by'] = $this->user_id;
            $data['created_at'] = date('Y-m-d H:i:s');

            $result = $this->Quiz_model->add($data);
            echo json_encode($result);
		} else if ($action == "edit" && $this->input->post()) {
            $id = sanitizeInput($this->input->post("id"), FILTER_VALIDATE_INT);

			$quiz = $this->Quiz_model->getQuizById($id);
            if (!isset($quiz)) {
                echo json_encode(array("status" => "failed", "message" => "No such quiz."));
                return;
            } else if ($quiz->is_publish == 1 && getprofile()->usertype != '2') {
                echo json_encode(array("status" => "failed", "message" => "Can\'t edit already published quiz."));
                return;
            } else if ($quiz->created_by != $this->user_id && getprofile()->usertype != '2') {
                echo json_encode(array("status" => "failed", "message" => "You can't edit someone else's quiz."));
                return;
            } else if ($quiz->is_publish == 0 and $quiz->status == 3) {
                echo json_encode(array("status" => "failed", "message" => "Can\'t edit this quiz."));
                return;
            }

            $data = $this->getQuizData($this->input->post());
            $data['updated_by'] =  $this->user_id;;
            $data['updated_at'] = date('Y-m-d H:i:s');

            $result = $this->Quiz_model->update($data, $id);
            echo json_encode($result);
		} else {
            if (!isset($action)) {
                $data["category"] = $this->Question_model->getCategoryList();

                $total = $this->Quiz_model->getTotalQuizzes();
                $data['quiz'] = $this->Quiz_model->getQuizzes(null, null, 10, 0);
    
                $data['quizConfig'] = array("createGame" => false, "quizName" => "quizzes");
                $data['deferLoading'] = array("filtered" => $total, "total" => $total);
        
                $this->template->set_breadcrumb('Quizzes', asset_url('challenge/quiz'));
                $this->template->set_layout(DEFAULT_LAYOUT)->build('challenge/quiz_list', $data);
            } else {
                redirect("challenge/quiz");
            }
		}
	}

    private function getQuizData($data) {
        $data['id'] = sanitizeInput($this->input->post('id'), FILTER_VALIDATE_INT);
        $data['category'] = sanitizeInput($this->input->post('category'), FILTER_VALIDATE_INT);
        $data['difficulty'] = sanitizeInput($this->input->post('difficulty'), FILTER_SANITIZE_STRING);
        $data['name'] = sanitizeInput($this->input->post('name', true), FILTER_SANITIZE_STRING);
        $data['questions'] = json_encode(sanitizeInputArray(explode(",", $this->input->post('questions', true)), FILTER_VALIDATE_INT));
        $data['is_publish'] = sanitizeInput($this->input->post('is_publish'), FILTER_VALIDATE_BOOLEAN) ? 1 : 0;

        $data['status'] = (getprofile()->usertype == '2' ? sanitizeInput($this->input->post("status"), FILTER_VALIDATE_INT) : 0);

        return $data;
    }

    public function getQuizQuestions() {
        $quiz_id = sanitizeInput($this->input->get('quiz'), FILTER_VALIDATE_INT);
        $quiz = $this->Quiz_model->getQuizById($quiz_id);
        $questions = $this->Quiz_model->getQuizQuestion($quiz->questions);
        
        echo json_encode($questions);
    }

	public function getQuizzes() {
        $userfilter = sanitizeInput($this->input->get("userfilter"), FILTER_VALIDATE_BOOLEAN) ? $this->user_id : null;

        $draw = sanitizeInput($this->input->get("draw"), FILTER_VALIDATE_INT);

		$limit = sanitizeInput($this->input->get("length"), FILTER_VALIDATE_INT);
		$offset = sanitizeInput($this->input->get("start"), FILTER_VALIDATE_INT);
		$search = sanitizeInput($this->input->get("search[value]"), FILTER_SANITIZE_STRING);

		$by = sanitizeInput($this->input->get("order[0][column]"), FILTER_VALIDATE_INT);
		$order = array("by" => sanitizeInput($this->input->get("columns[" . $by . "][data]"), FILTER_SANITIZE_STRING), "arrange" => sanitizeInput($this->input->get("order[0][dir]"), FILTER_SANITIZE_STRING));
		
		$total = $this->Quiz_model->getTotalQuizzes($userfilter);
		$quizzes = $this->Quiz_model->getQuizzes($search, $order, null, null, $userfilter);

		$data = array(
			"draw" => $draw,
			"recordsTotal" => $total,
			"recordsFiltered" => count($quizzes),
			"data" => array_slice($quizzes, $offset, $limit)
		);

		echo json_encode($data);
	}

    public function getApprovedQuizzes() {
        $userfilter = sanitizeInput($this->input->get("userfilter"), FILTER_VALIDATE_BOOLEAN) ? $this->user_id : null;

        $draw = sanitizeInput($this->input->get("draw"), FILTER_VALIDATE_INT);

		$limit = sanitizeInput($this->input->get("length"), FILTER_VALIDATE_INT);
		$offset = sanitizeInput($this->input->get("start"), FILTER_VALIDATE_INT);
		$search = sanitizeInput($this->input->get("search[value]"), FILTER_SANITIZE_STRING);

		$by = sanitizeInput($this->input->get("order[0][column]"), FILTER_VALIDATE_INT);
		$order = array("by" => sanitizeInput($this->input->get("columns[" . $by . "][data]"), FILTER_SANITIZE_STRING), "arrange" => sanitizeInput($this->input->get("order[0][dir]"), FILTER_SANITIZE_STRING));
		
		$total = $this->Quiz_model->getTotalApprovedQuizzes($userfilter);
		$quizzes = $this->Quiz_model->getApprovedQuizList($search, $order, null, null, $userfilter);

		$data = array(
			"draw" => $draw,
			"recordsTotal" => $total,
			"recordsFiltered" => count($quizzes),
			"data" => array_slice($quizzes, $offset, $limit)
		);

		echo json_encode($data);
	}

    public function getQuiz() {
        $id = sanitizeInput($this->input->get("id"), FILTER_VALIDATE_INT);
        $data = $this->Quiz_model->getQuizByIdForEdit($id);

        echo json_encode($data);
    }

    /* For Playing Game Code */

    public function getQuizQuestionForPlaying() {
        $game_session_id = sanitizeInput($this->input->get("game_session_id"), FILTER_SANITIZE_STRING);

        $response = array("status" => "failed");

        //only get questions if in session
        if (!empty($game_session_id) && (isset($_SESSION["active_sessions"][$game_session_id]))) {
            $active_session = $_SESSION["active_sessions"][$game_session_id];
            $play_state = $active_session["type"];
            
            // only retrieve questions one time
            if (!isset($active_session["started"])) {
                if ($play_state != "preview") {
                    $quiz = $this->Quiz_model->getQuizByGameSession($game_session_id);
                } else {
                    $slug = $active_session["slug"];
                    $quiz = $this->Quiz_model->getQuizByGameSlug($slug);
                }
            }

            if (!empty($quiz) && $quiz->questions) {
                $questions = $this->Quiz_model->getQuizQuestionForPlaying($quiz->questions);
                if (!empty($questions)) {
                    $_SESSION["active_sessions"][$game_session_id]["started"] = true;
                    $response = array("status" => "success", "questions" => $questions);
                }
            }
        }

        echo json_encode($response);
    }
}
