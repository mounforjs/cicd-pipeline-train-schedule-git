<?php


class Manage_games_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function get_all_games($search = null, $order = null, $limit = null, $offset = null) {
        $this->db->select('t1.id, t1.slug, t1.name, t1.user_id, t5.firstname, t5.lastname, t1.value_of_the_game, t1.credit_cost, t1.Publish, t1.active, t1.created_at, t1.type,t1.Game_Image, t2.charity_id, t3.name as charityname, t4.name as gametype, t1.credit_type, t1.isProd');
        $this->db->from('game as t1');
        $this->db->join('user_game_charity as t2', 't1.id = t2.game_id', 'left');
        $this->db->join('charity as t3', 't3.id = t2.charity_id', 'left');
        $this->db->join('gametype as t4', 't1.Type = t4.id');
        $this->db->join('tbl_users as t5', 't5.user_id = t1.user_id');

        if (isset($search) && $search != "") {
            $this->db->group_start();
            $this->db->like("t1.name", $search);
            $this->db->or_like(array("t3.name" => $search, "t5.firstname" => $search, "t5.lastname" => $search));
            $this->db->group_end();
        }

        if (!empty($order["by"]) && !empty($order["arrange"])) {
            $this->db->order_by($order["by"], $order["arrange"]);
        } else {
            $this->db->order_by('t1.id', 'desc');
            $this->db->order_by('t1.Publish_Date', 'desc');
        }

        if (isset($limit) && isset($offset)) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->get()->result();
    }

    public function get_user_games($user_id = '', $search = null, $order = null, $limit = null, $offset = null) {
        if (empty($user_id)) {
            $user_id = $this->session->userdata('user_id');
        }

        $this->db->select('t1.id, t1.name, t1.slug, t1.user_id, t5.firstname, t5.lastname, t1.value_of_the_game, t1.credit_cost, t1.Publish, t1.active, t1.created_at, t1.type,t1.Game_Image, t2.charity_id, t3.name as charityname, t4.name as gametype, t1.credit_type, t1.isProd');
        $this->db->from('game as t1');
        $this->db->join('user_game_charity as t2', 't1.id = t2.game_id', 'left');
        $this->db->join('charity as t3', 't3.id = t2.charity_id', 'left');
        $this->db->join('gametype as t4', 't1.Type = t4.id');
        $this->db->join('tbl_users as t5', 't5.user_id = t1.user_id');
        $this->db->where('t1.user_id', $user_id);

        if (isset($search) && $search != "") {
            $this->db->group_start();
            $this->db->like("t1.name", $search);
            $this->db->or_like(array("t3.name" => $search, "t5.firstname" => $search, "t5.lastname" => $search));
            $this->db->group_end();
        }

        if (!empty($order["by"]) && !empty($order["arrange"])) {
            $this->db->order_by($order["by"], $order["arrange"]);
        } else {
            $this->db->order_by('t1.id', 'desc');
            $this->db->order_by('t1.Publish_Date', 'desc');
        }

        if (isset($limit) && isset($offset)) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->get()->result();
    }

    public function getTotalGames($user_id = null) {
        $this->db->from('game as t1');
        $this->db->join('user_game_charity as t2', 't1.id = t2.game_id', 'left');
        $this->db->join('charity as t3', 't3.id = t2.charity_id', 'left');
        $this->db->join('gametype as t4', 't1.Type = t4.id');
        $this->db->join('tbl_users as t5', 't5.user_id = t1.user_id');

        if (isset($user_id)) {
            $this->db->where('t1.user_id', $user_id);
        }

        return $this->db->get()->num_rows();
    }

    public function get_review_games($reviewer_id, $limit=null, $offset=null, $search=null, $order=null) {
        $sql = "
        SELECT * FROM (
            SELECT
              `t1`.`id`,
              `t1`.`name`,
              `t1`.`user_id`,
              `t1`.`slug`,
              `t1`.`value_of_the_game`,
              `t1`.`credit_cost`,
              `t1`.`processed`,
              `t1`.`review_status`,
              `t1`.`Publish`,
              `t1`.`active`,
              `t1`.`created_at`,
              `t1`.`Status`,
              `t1`.`type`,
              `t1`.`Game_Image`,
              `t1`.`credit_type`,
              `t2`.`charity_id`,
              `t3`.`name` AS `charityname`,
              `t4`.`name` AS `gametype`,

              (select count(plays.user_id) as total from (select user_game_attempts.game_id, user_game_attempts.user_id from user_game_attempts 
              join game on game.id=user_game_attempts.game_id group by user_game_attempts.game_id, user_game_attempts.user_id) as plays where plays.game_id = t1.id 
              group by plays.game_id) as player_count,
              
              CASE    
              WHEN (LOCATE('review', (SELECT
                GROUP_CONCAT(TYPE)
              FROM
                question
              WHERE FIND_IN_SET(
                  id,
                  (SELECT
                    REPLACE(
                      REPLACE(REPLACE(questions, ']', ''), '[', ''),
                      '\"',''
                    )
                  FROM
                    quiz
                  WHERE id = t5.id)
                )))) > 0 THEN 'yes'
              ELSE 'no'
              END
               AS isQuesReview
               
            FROM
              `game` AS `t1`
              JOIN `user_game_charity` AS `t2`
                ON `t1`.`id` = `t2`.`game_id`
              JOIN `charity` AS `t3`
                ON `t3`.`id` = `t2`.`charity_id`
              JOIN `gametype` AS `t4`
                ON `t1`.`Type` = `t4`.`id`
              JOIN `quiz` AS `t5`
                ON `t5`.`id` = `t1`.`quiz_id`
            WHERE (`t1`.`Publish` = 'Live'
              OR (`t1`.`Publish` = 'No'
              AND `t1`.`Status` = 'Completed'))
              AND (`t1`.`processed` = 0 
              OR (`t1`.`processed` = 1
              OR `t1`.`review_status` = 1))
              AND `t1`.`user_id` = " . $reviewer_id . "
              AND `t4`.`name` = 'challenge'
            
            GROUP BY `t1`.`id`
            ORDER BY `t1`.`Publish_Date` DESC

            ) AS MT
            WHERE MT.isQuesReview = 'yes'
            ";


            if (isset($search) && $search != "") {
                $sql .= " and (`MT`.`name` like '%" . $search . "%' or `MT`.`gametype` like '%" . $search . "%' or `MT`.`charityname` like '%" . $search . "%')";
            }
    
            if (!empty($order["by"]) && !empty($order["arrange"])) {
                $sql .= " order by " . $order["by"] . " " . $order["arrange"];
            }
    
            if (isset($limit) && isset($offset) ) {
                $sql .= " limit " . $offset . ", " . $limit;
            }
    

        $q = $this->db->query($sql);
       
        return $q->result();
    }

    public function reviewGamesCount($user_id) {
        return count($this->get_review_games($user_id));
    }

    public function getUserTotalAttemptsForReviewGame($quizId, $gameId, $userId) {
        $this->db->select('id');
        $this->db->from('user_quiz_answer');
        $this->db->where('quiz_id', $quizId);
        $this->db->where('game_id', $gameId);
        $this->db->where('user_id', $userId);
        return $this->db->get()->num_rows();
    }

    public function get_review_details($slug = "") {
        $this->db->select('t1.id, t1.name,t1.prize_title, t1.credit_type, t1.value_of_the_game, t1.quiz_id, t1.winner_option, t4.name as Quiz_name,t1.winner_count, 
        t1.winner_option as Quiz_rules,t6.fundraise_value,t6.beneficiary_percentage,t6.wwl_percentage, t6.creator_percentage, t1.Publish, t1.Status, t1.processed, t1.review_status');
        $this->db->from('game as t1');
        $this->db->where('t1.slug', $slug);
        $this->db->where('t1.user_id', $this->session->userdata("user_id")); //only get games by requesting user

        $this->db->group_start();
            $this->db->where('t1.Publish', 'Live');
            $this->db->or_group_start();
            $this->db->where(array('t1.Publish' => 'No', 't1.Status' => 'Completed'));
            $this->db->group_end();
        $this->db->group_end();

        $this->db->group_start();
            $this->db->where('t1.processed', 0);
            $this->db->or_group_start();
            $this->db->or_where(array('t1.processed' => 1, 't1.review_status' => 1));
            $this->db->group_end();
        $this->db->group_end();
        
        $this->db->join('tbl_users as t3', 't1.user_id = t3.user_id');
        $this->db->join('quiz as t4', ' t4.id = t1.quiz_id');
        $this->db->join('game_credit as t6', 't6.game_id = t1.id');

        $this->db->group_by('t1.id', $slug);
        $this->db->order_by('t1.Publish_Date', 'desc');
        
        return $this->db->get()->row();
    }

    public function getTotalSelectedUsers($quiz_id, $game_id) {
        return $this->getSelectedUsers($quiz_id, $game_id)["total"];
    }

    public function getSelectedUsers($quiz_id, $game_id, $search=null, $order=null, $limit=null, $offset=null) {
        $this->db->select('UQA.id, UQA.user_id, UQA.game_id, U.username, t5.notes, t5.final_rank, t5.grade');
        $this->db->from('user_quiz_answer UQA');

        $this->db->join('tbl_users U', 'U.user_id = UQA.user_id');
        $this->db->join('tbl_user_review as t5', 't5.user_id = U.user_id and t5.game_id = UQA.game_id', 'left');

        $this->db->where(array('UQA.quiz_id' => $quiz_id, 'UQA.game_id' => $game_id));
        $this->db->where("t5.final_rank is NOT NULL", NULL, false);
        $this->db->where("t5.reselected !=", 1);

        if (isset($search) && $search != "") {
            $this->db->group_start();
            $this->db->like("U.username", $search);
            $this->db->or_like(array("t5.notes" => $search, "t5.final_rank" => $search, "t5.grade" => $search));
            $this->db->group_end();
        }

        $this->db->group_by('UQA.user_id');

        $this->db->order_by('t5.final_rank', 'ASC');

        if (!empty($order["by"]) && !empty($order["arrange"])) {
            $this->db->order_by($order["by"], $order["arrange"]);
        }

        if (isset($limit) && isset($offset)) {
            $this->db->limit($limit, $offset);
        }

        $users = $this->db->get()->result();

        return ["total" => count($users), "users" => $users];
    }

    public function getTotalUserAttemptInfo($quiz_id, $game_id) {
        return count($this->getUserAttemptInfo($quiz_id, $game_id)["attempts"]);
    }

    public function getUserAttemptInfo($quiz_id, $game_id, $search=null, $order=null, $limit=null, $offset=null) {
        $this->db->select('UQA.id, UQA.user_id, UQA.game_id, U.username, t5.notes, t5.final_rank, t5.grade, t5.reselected, count(UQA.id) as userTotalAttempts');
        $this->db->from('user_quiz_answer UQA');

        $this->db->join('tbl_users U', 'U.user_id = UQA.user_id');
        $this->db->join('tbl_user_review as t5', 't5.user_id = U.user_id and t5.game_id = UQA.game_id', 'left');

        $this->db->where('UQA.quiz_id', $quiz_id);
        $this->db->where('UQA.game_id', $game_id);

        if (isset($search) && $search != "") {
            $this->db->group_start();
            $this->db->like("U.username", $search);
            $this->db->or_like(array("t5.notes" => $search, "t5.final_rank" => $search, "t5.grade" => $search));
            $this->db->group_end();
        }

        $this->db->group_by('UQA.user_id');

        $this->db->order_by('UQA.created_at', 'asc');
        $this->db->order_by('UQA.score', 'DESC');

        if (!empty($order["by"]) && !empty($order["arrange"])) {
            $this->db->order_by($order["by"], $order["arrange"]);
        }

        if (isset($limit) && isset($offset)) {
            $this->db->limit($limit, $offset);
        }

        $answers = $this->db->get()->result();

        return ["total" => count($answers), "attempts" => $answers];
    }

    public function getUserAttempt($questions, $quiz_id, $game_id, $user_id, $search=null, $order=null, $limit=null, $offset=null, $attempt_num=null) {
        //get quiz questions and order by quiz question order
        $ids = json_decode($questions);
        if (gettype($ids) == "array") {
            $questionOrder = sprintf('FIELD(Q.id, %s)', implode(', ', $ids));
        }

        $this->db->select('Q.id, Q.question, Q.type, Q.difficulty, C.name as category_name');
        $this->db->join('category C', 'C.id = Q.category', 'left');
        $this->db->where_in('Q.id', $ids);
        if (isset($questionOrder)) {
            $this->db->order_by($questionOrder, NULL, FALSE);
        }

        $questionsResult = $this->db->get('question Q')->result();
        $questions = array_values($questionsResult);

        //get attempts
        $this->db->select('UQA.*');
        $this->db->from('user_quiz_answer UQA');

        $this->db->join('tbl_users U', 'U.user_id = UQA.user_id');
        $this->db->join('tbl_user_review as t5', 't5.user_id = U.user_id and t5.game_id = UQA.game_id', 'left');

        $this->db->where(array('UQA.quiz_id' => $quiz_id, 'UQA.game_id' => $game_id, 'UQA.user_id' => $user_id));

        $this->db->order_by('UQA.created_at', 'asc');
        $this->db->order_by('UQA.score', 'DESC');
        $this->db->order_by('UQA.id', 'DESC');

        //limit results to single attempt (x amount of questions in one quiz) or if quiz has more questions than needed, limit by n needed
        $this->db->limit(1, $attempt_num-1);

        $attempt = $this->db->get()->result()[0];
        $attempt->answer = json_decode($attempt->answer);

        foreach ($questions as $index => &$question) {      
            $question->id = $index + 1;
            $question->answer = $attempt->answer[$index]->value;
            $question->time = $attempt->answer[$index]->time;
        }

        //filter questions, answer, time by user search input
        if (isset($search) && $search != "") {
            $questions = array_filter($questions, function($question) use ($search) {
                $searchQuestion = strpos(strtolower($question->question), strtolower($search)) !== false;
                $searchAnswer = strpos(strtolower($question->value), strtolower($search)) !== false;
                $searchTimeTaken = strpos(strtolower($question->time), strtolower($search)) !== false;

                return $searchQuestion || $searchAnswer || $searchTimeTaken;
            });
        }

        return ["total" => count($questions), "questions" => $questions];
    }
    
    public function updateReviewStatus($value, $userId, $name, $gameId) {
        $data = array(
            $name => $value,
            "user_id" => (isset($userId) ? $userId : null),
            "game_id" => (isset($gameId) ? $gameId : null),
            "modified_at" => date('Y-m-d H:i:s')
        );

        //verify user has actually played quiz
        $verify_user = $this->db->from('user_quiz_answer UQA')->join('tbl_user_review as t5', 't5.user_id = UQA.user_id and t5.game_id = UQA.game_id', 'left')->where(array("UQA.user_id" => $userId, "UQA.game_id" => $gameId))->get()->num_rows();
        if (!$verify_user) {
            return ['status' => 'failed', 'msg' => 'unable to select user'];
        } else {
            //verify count of selected users is not already equal to total winner count
            $winner_count = $this->db->select("winner_count")->where('id', $gameId)->where('user_id', $this->session->userdata("user_id"))->get('game')->row()->winner_count;
            $userReviews = $this->db->where(array('game_id' => $gameId, "reselected" => 0))->get('tbl_user_review')->result();

            $userSelected = array_values(array_filter($userReviews, function($user) use($userId) {
                return isset($user->final_rank);
            }));
            $selectedCount = count($userSelected);

            $exists = array_values(array_filter($userReviews, function($user) use($userId) {
                return $user->user_id == $userId;
            }));

            if (!empty($exists)) { //check if row is already present and max winners have not been selected
                if ($name == "final_rank" && (!isset($exists[0]->final_rank) && $selectedCount == $winner_count)) {
                    return ['status' => 'failed', 'msg' => 'max users already selected'];
                } else {
                    $this->db->where('id', $exists[0]->id);
                    return ['status' => 'update', 'data' => $this->db->update('tbl_user_review', $data)];
                }
            } else {
                if ($name == "final_rank" && $selectedCount == $winner_count) {
                    return ['status' => 'failed', 'msg' => 'max users already selected'];
                } else {
                    return ['status' => 'insert', 'data' => $this->db->insert('tbl_user_review', $data)];
                }
            }
        }
    }

    public function update_game_status($id, $status) {
        $data = array('Publish' => $status);

        $this->db->where('id', $id);
        $result = $this->db->update('game', $data);
        return $result;
    }

    public function update_game_active($id, $status) {
        $data = array('active' => $status);

        $this->db->where('id', $id);
        $result = $this->db->update('game', $data);
        return $result;
    }

    public function delete_game($id) {
        $this->db->where('id', $id);
        $del = $this->db->delete('game');
        if ($del == '1') {
            return true;
        } else {
            return false;
        }
    }

    public function update_game_server($id, $status) {
        $data = array('isProd' => $status);
        $this->db->where('id', $id);
        $result = $this->db->update('game', $data);
        return $result;
    }
}
