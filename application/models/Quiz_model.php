<?php

class Quiz_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function add($data) {
        $this->db->insert('quiz', $data);
        $insert = $this->db->insert_id();

        if ($insert > 0) {
            return array("status" => "success", "message" => "added quiz");
        } else {
            return array("status" => "failed", "message" => "could not add quiz");
        }
    }

    public function update($data, $id) {
        $this->db->where('id', $id);
        $this->db->update('quiz', $data);

        if ($this->db->affected_rows() > 0) {
            return array("status" => "success", "message" => "updated quiz");
        } else {
            return array("status" => "failed", "message" => "could not update quiz");
        }
    }

    public function getTotalQuizzes($user_id=null) {
        $this->db->from('quiz as Q');
		$this->db->join('category as C', 'C.id = Q.category', 'left');

        if (isset($user_id)) {
            $this->db->where('Q.created_by', $user_id);
        }
        
        return $this->db->get()->num_rows();
    }

    public function getQuizzes($search=null, $order=null, $limit=null, $offset=null, $user_id=null) {
        $user_type = getprofile()->usertype == '2';
        
        $this->db->select('Q.*, C.name as category_name');
        $this->db->from('quiz as Q');
		$this->db->join('category as C', 'C.id = Q.category', 'left');

        if (isset($user_id)) {
            $this->db->where('Q.created_by', $user_id);
        }
        
        if (isset($search) && $search != "") {
            $this->db->group_start();
            $this->db->like("Q.name", $search);
            $this->db->or_like(array("C.name" => $search, "Q.difficulty" => $search));
            $this->db->group_end();
        }

        if (!empty($order["by"]) && !empty($order["arrange"])) {
            $this->db->order_by($order["by"], $order["arrange"]);
        }

        if (isset($limit) && isset($offset)) {
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get()->result();
        foreach ($query as $key => $quiz) {
            $quiz->editable = ((($this->session->userdata("user_id") == $quiz->created_by && $quiz->status != 1) || $user_type) ? true : false);
        }

        return $query;
    }

    public function getTotalApprovedQuizzes($user_id=null) {
        $this->db->from('quiz as Q');
		$this->db->join('category as C', 'C.id = Q.category', 'left');

        $this->db->where(array('Q.status' => 1, 'Q.is_publish' => 1));

        if (isset($user_id)) {
            $this->db->where('Q.created_by', $user_id);
        }
        
        return $this->db->get()->num_rows();
    }

    public function getApprovedQuizList($search=null, $order=null, $limit=null, $offset=null, $user_id=null) {
        $user_type = getprofile()->usertype == '2';
        
        $this->db->select('Q.*, C.name as category_name');
        $this->db->from('quiz as Q');
		$this->db->join('category as C', 'C.id = Q.category', 'left');

        $this->db->where(array('Q.status' => 1, 'Q.is_publish' => 1));

        if (isset($user_id)) {
            $this->db->where('Q.created_by', $user_id);
        }
        
        if (isset($search) && $search != "") {
            $this->db->group_start();
            $this->db->like("Q.name", $search);
            $this->db->or_like(array("C.name" => $search, "Q.difficulty" => $search));
            $this->db->group_end();
        }

        if (!empty($order["by"]) && !empty($order["arrange"])) {
            $this->db->order_by($order["by"], $order["arrange"]);
        }

        if (isset($limit) && isset($offset)) {
            $this->db->limit($limit, $offset);
        }

        $query = $this->db->get()->result();
        return $query;
    }

    public function getQuizByGameSession($game_session_id) {
        $this->db->select('quiz.*');
        $this->db->from('user_game_attempts as uga');
        $this->db->join('game', "game.id=uga.game_id");
        $this->db->join('quiz', "quiz.id=game.quiz_id");
        $this->db->where('uga.game_session_id', $game_session_id);

        return $this->db->get()->row();
    }

    public function getQuizByGameSlug($slug) {
        $this->db->select('quiz.*');
        $this->db->from('game');
        $this->db->join('quiz', "quiz.id=game.quiz_id");
        $this->db->where(array("game.slug" => $slug));

        return $this->db->get()->row();
    }

    public function getQuizById($id) {
        return $this->db->where('id', $id)->get('quiz')->row();
    }

    public function getQuizByIdForEdit($id) {
        $this->db->from('quiz as Q');
        $this->db->where('id', $id);

        $quizzes = $this->db->get()->row();

        $questionsData = array();
        $questions = json_decode($quizzes->questions);
        foreach($questions as $key => $_id) {
            $question = $this->getQuizQuestion($_id)[0];
            $questionsData[] = $question;
        }
        
        $quizzes->questions = $questionsData;

        return $quizzes;
    }

    public function getQuizByIdForGame($id) {
        return $this->db->select("Q.*, C.name as category_name")
        ->join('category C', 'C.id = Q.category', 'left')
        ->where('Q.id', $id)
        ->where('Q.is_publish', 0)
        ->get('quiz Q')->row();
    }

    public function getQuizQuestion($id) {
        $ids = json_decode($id);
        if (gettype($ids) == "array") {
            $order = sprintf('FIELD(Q.id, %s)', implode(', ', $ids));
        }

        $this->db->select('Q.id, Q.question, Q.correct_answer, Q.incorrect_answer, Q.boolean_answer, Q.type, Q.difficulty, C.name as category_name');
        $this->db->join('category C', 'C.id = Q.category', 'left');
        $this->db->where_in('Q.id', $ids);
        if (!empty($order["by"]) && !empty($order["arrange"])) {
            $this->db->order_by($order, NULL, FALSE);
        }

        return $this->db->get('question Q')->result_array();
    }

    public function getQuizQuestionForPlaying($id) {
        $ids = json_decode($id);
        if (gettype($ids) == "array") {
            $order = sprintf('FIELD(Q.id, %s)', implode(', ', $ids));
        }

        $this->db->select('Q.id, Q.question as text, Q.correct_answer, Q.incorrect_answer, Q.boolean_answer, Q.type, Q.difficulty, C.name as category');
        $this->db->join('category C', 'C.id = Q.category', 'left');
        $this->db->where_in('Q.id', json_decode($id));
        if (isset($order)) {
            $this->db->order_by($order, NULL, FALSE);
        }
        
        $data = $this->db->get('question Q')->result_array();
        foreach($data as $index=>$d) {
            $text = [];
            $in_correct_answer = json_decode($d['incorrect_answer']);
            if ($in_correct_answer != '' and count($in_correct_answer) > 0) {
                $allOptoin = $this->shuffle_assoc(array_merge(json_decode($d['correct_answer']), json_decode($d['incorrect_answer'])));
                foreach($allOptoin as $option) {
                    $text[] = ['text' => $option];
                }
            }

            if ($d['type'] == 'boolean') {
                if (json_decode($d['correct_answer'])[0] == 'True') {
                    $text[]['text'] = "False";
                    $text[]['text'] = "True";
                } else {
                    $text[]['text'] = "True";
                    $text[]['text'] = "False";
                }
            }
            $data[$index]['responses'] = $text;
            unset($data[$index]['incorrect_answers']);
            unset($data[$index]['correct_answer']);
            unset($data[$index]['incorrect_answer']);
            unset($data[$index]['boolean_answer']);
            unset($data[$index]['boolean_answer']);
        }
        return $data;
    }

    private function shuffle_assoc($list) { 
        if (!is_array($list)) return $list; 
        $keys = array_keys($list); 
        shuffle($keys); 
        $random = array(); 
        foreach ($keys as $key) { 
          $random[$key] = $list[$key]; 
        }
        return $random; 
    } 

    public function get_quiz_result($id, $user_id) {
        $result = $this->db->where('game_id', $id)
        ->where('user_id', $user_id)
        ->order_by('id', 'desc')->get('user_quiz_answer')->row();
        return $result;
    }

    public function submit($user_id, $game_details, $game_session_id, $data) {
        $game_id = $game_details->id;
        
        $quiz = $this->getQuizByGameSlug($game_details->slug);
        $questions = $this->getQuizQuestion( $quiz->questions );
        $answers = $data["answers"];

        $right = 0; $qCount = 0;
        foreach($answers as $index=>$d) {
            $id = sanitizeInput($d->id, FILTER_VALIDATE_INT);
            $value = sanitizeInput($d->value, FILTER_SANITIZE_STRING);

            $answers[$index]->is_right = NULL;
            foreach ($questions as $key => $question) {
                if ($id == $question["id"]) {
                    $qCount++;
                    if ($question['type'] == 'boolean') {
                        $answers[$index]->is_right = 'no';
                        $correct_answer = (json_decode($question["correct_answer"])[0] == '0' || json_decode($question['correct_answer'])[0] == 'False') ? 'False' : 'True';
                        if ($correct_answer == $value) {
                            $right++;
                            $answers[$index]->is_right = 'yes';
                        }
                    } elseif ($question['type'] == 'one') {
                        $answers[$index]->is_right = 'no';
                        if ( in_array( strtolower($value), array_map('strtolower', explode(",", json_decode($question['correct_answer'])[0]))) ) {
                            $right++;
                            $answers[$index]->is_right = 'yes';
                        }
                    } elseif ($question['type'] == 'multiple') {
                        $answers[$index]->is_right = 'no';
                        if ( in_array( $value, json_decode($question['correct_answer'])) ) {
                            $right++;
                            $answers[$index]->is_right = 'yes';
                        }
                    } elseif($question['type'] == 'review'){ // to be discussed about this time 
                        continue;
                    }
                }
            }
        }

        $time_taken = $data["completed_in"];
        $ms = DateTime::createFromFormat('U.u', $time_taken);
        $formatted_time = gmdate("H:i:s.", $time_taken) . rtrim($ms->format("u"), "0");;
        $score = (($right / $qCount) * 100);

        $this->db->trans_start();

        $quiz_data = array(
            'user_id' => $user_id,
            'game_id' => $game_id,
            'game_session_id' => $game_session_id,
            'quiz_id' => $game_details->quiz_id,
            'time_taken' => $time_taken,
            'formatted_time' => $formatted_time,
            'right_answer' => $right,
            'score' => number_format((float)$score, 2, '.', ''),
            'answer' => json_encode($answers),
            'created_at' => date('Y-m-d H:i:s'),
        );

        $quiz_history = $this->db->insert("user_quiz_answer", $quiz_data);

        $attempt_data = array(
            'completed_in' => $time_taken,
            'quiz_percentage' => number_format((float)$score, 2, '.', '')/100,
            'score' => number_format((float)$score, 2, '.', ''),
            "end_time" => $data["end_time"]
        );

        $this->db->where("game_session_id", $game_session_id);
        $history = $this->db->update("game_history", $attempt_data);

        $this->db->trans_complete();

        return ($quiz_history && $history);
    }
}

?>