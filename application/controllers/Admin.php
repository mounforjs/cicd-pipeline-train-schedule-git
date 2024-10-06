<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once __DIR__ . '/Email.php';

class Admin extends CI_Controller {

	public function __construct () 	{
		parent::__construct();
		
		check_login();
		$this->load->library('user_agent');
		$this->load->model('user_model');
		$this->load->model('about_model');
		$this->load->model('quote_model');
		$this->load->model('flags_model');
		$this->load->model('manage_games_model');
		$this->load->model('emails_model');
		$this->load->model('faq_model');
		$this->load->model('news_model');
		$this->load->model('blog_model');
		$this->load->model('coupon_model');
		$this->load->model('feedback_model');
		$this->load->model('transaction_model');
		$this->load->model('buy_credit_model');
		$this->load->model('short_model');
		$this->load->model('referral_model');
		$this->load->model('maps_model');

		$this->load->library('template');
		$this->load->library('session');

		$this->user_id = $this->session->userdata("user_id");
		$this->template->set_breadcrumb('Home', asset_url());

		if(isRegularUser()) {
			redirect(asset_url());
		}

		// Get the method name from the URL
		$method_name = $this->router->fetch_method();

		// Define the role-based arrays for allowed methods
		$showForSystemAdmin = 'paymentkeys';
		$hideForSupporterAndCreator = array('users', 'games', 'coupons', 'referral');

		// condition for System Admin
		if($showForSystemAdmin === $method_name && !isAdmin()) {
			redirect(base_url('admin'));
		}

		// condition for Supporter and Creator
		if(in_array($method_name, $hideForSupporterAndCreator) && (checkSupporterLogin() || checkContentCreatorLogin())) { 
			redirect(base_url('admin'));
		}
	}

	 public function index() {
		$data='';
		$this->template->set_breadcrumb('Admin', asset_url('admin'));
		$this->template->set_layout(DEFAULT_LAYOUT)->build('admin/index', $data);
	}

	public function users() {
		$total = $this->user_model->getTotalUsers();
		$data['users_list'] = $this->user_model->get_all_users(null, null, 10, 0);

		// Iterate over the users_list to add a new parameter for each user
		foreach ($data['users_list'] as &$user) {
			// Assuming $user is an associative array. If it's an object, use $user->permissions = 'value';
			$user->role = $this->user_model->get_user_permission_string($user->usertype)->role; // Add your logic to fetch or set the permissions
		}		

		$data['coupon_list'] = $this->coupon_model->get_coupons();
		$data['user_roles'] = $this->user_model->get_admin_roles();
		
		$data['deferLoading'] = array("filtered" => $total, "total" => $total);

		$this->template->set_breadcrumb('Admin', asset_url('admin'));
		$this->template->set_breadcrumb('Users', asset_url('admin/users'));
		$this->template->set_layout(DEFAULT_LAYOUT)->build('admin/users', $data);
	}

	public function getUsers() {
		$draw = sanitizeInput($this->input->get("draw"), FILTER_VALIDATE_INT);

        $limit = sanitizeInput($this->input->get("length"), FILTER_VALIDATE_INT);
		$offset = sanitizeInput($this->input->get("start"), FILTER_VALIDATE_INT);
		$search = sanitizeInput($this->input->get("search[value]", true), FILTER_SANITIZE_STRING);

		$by = sanitizeInput($this->input->get("order[0][column]"), FILTER_VALIDATE_INT);
		$order = array("by" => sanitizeInput($this->input->get("columns[" . $by . "][data]"), FILTER_SANITIZE_STRING), "arrange" => sanitizeInput($this->input->get("order[0][dir]"), FILTER_SANITIZE_STRING));
		
		$total = $this->user_model->getTotalUsers();
		$users = $this->user_model->get_all_users($search, $order); 

		$user_roles = $this->user_model->get_admin_roles();
		foreach ($users as $key => $user) {
			$user->user_roles = $user_roles;
		}
		
		$coupons = $this->coupon_model->get_coupons(); 

		foreach ($users as $key => $user) {
			$user->coupons = $coupons;
		}
		
		$data = array(
			"draw" => $draw,
			"recordsTotal" => $total,
			"recordsFiltered" => count($users),
			"data" => array_slice($users, $offset, $limit)
		);

		echo json_encode($data);
	}

	public function admin_create_new_user() {
 	 	$username = sanitizeInput($this->input->post('username', true), FILTER_SANITIZE_STRING);
 	 	$email = sanitizeInput(trim($this->input->post('email', true)), FILTER_SANITIZE_STRING);
 	 	$password = password_hash($this->input->post('password', true), PASSWORD_BCRYPT);
		$role = $this->input->post('role');

 		$data = $this->user_model->create_new_user($username, $email, $password, $role);
 		echo json_encode(array("done"=>$data));
	}

    public function update_created_user() {
        $id = sanitizeInput($this->input->post('id'), FILTER_VALIDATE_INT);
        $content = sanitizeInput($this->input->post('content', true), FILTER_SANITIZE_STRING);
        $name = sanitizeInput($this->input->post('name', true), FILTER_SANITIZE_STRING);

        $data = $this->user_model->update_admin_created_user($id, $content, $name);
        echo json_encode(array("done"=>$data));
	}

	public function accessSenstiveData(){
		if(!has_permission('access_payment_method_credentials')){
			show_error('You do not have permission to access this page.', 403);
		}
	}

	public function sendRefund(){
		if (!has_permission('send_refunds')){
			show_error('You do not have permission to access this page.', 403);
		}
	}

	public function createReferral() {
		if(!has_permission('create_referrals')){
			show_error('You do not have permission to access this page.', 403);
		}
	}

	public function updateContent(){
		if(!has_permission('update_content')){
			show_error('You do not have permission to access this page.', 403);
		}
	}


    public function user_status() {
        $id = sanitizeInput($this->input->post('id'), FILTER_VALIDATE_INT);
        $status = sanitizeInput($this->input->post('status'), FILTER_VALIDATE_BOOLEAN) ? "Yes" : "No";

        $data = $this->user_model->update_user_status($id,$status);
        echo json_encode(array("done"=>$data));
    }

    public function update_password() {
        $id = sanitizeInput($this->input->post('id'), FILTER_VALIDATE_INT);
        $pwd = password_hash($this->input->post('password', true), PASSWORD_BCRYPT);

        $data = $this->user_model->update_user_password($id,$pwd);
        echo json_encode(array("done"=>$data));
    }

    public function give_coupon() {
        $user_id = sanitizeInput($this->input->post('id'), FILTER_VALIDATE_INT);
        $coupon_id = sanitizeInput($this->input->post('coupon'), FILTER_VALIDATE_INT);

        $user = getprofile($user_id);

		$awarded = $this->coupon_model->give_user_coupon($user->user_id, $coupon_id);
		if ($awarded) {
			$coupon = $this->coupon_model->get_coupon($coupon_id);

			$emailData = array (
				"username" => $user->username,
				"coupon" => $coupon->description,
				"amount" => $coupon->amount
			);

			$notes = "You've been awarded {$coupon->amount} credits!";
			$subject = "You've been awarded credits!";
			$view = $this->load->view('emails/coupon-email', $emailData, true);
	
			sendNotificationAndEmail("credits_awarded", $user->user_id, $this->user_id, $notes, "credits", "receive", null, null, $subject, $view);
		}

		echo json_encode(array("done"=>$awarded));
	}

    public function tester_status() {
        $id = sanitizeInput($this->input->post('id'), FILTER_VALIDATE_INT);
        $status = sanitizeInput($this->input->post('status'), FILTER_VALIDATE_BOOLEAN) ? "Yes" : "No";

        $data = $this->user_model->update_tester_status($id,$status);
        echo json_encode(array("done"=>$data));

    }

    public function creator_status() {
        $id = sanitizeInput($this->input->post('id'), FILTER_VALIDATE_INT);
        $status = sanitizeInput($this->input->post('status'), FILTER_VALIDATE_BOOLEAN) ? "Yes" : "No";

        $data = $this->user_model->update_creator_status($id,$status);
        echo json_encode(array("done"=>$data));
    }

    public function credit_withdraw_status() {
		$id = sanitizeInput($this->input->post('id'), FILTER_VALIDATE_INT);
		$status = sanitizeInput($this->input->post('status'), FILTER_VALIDATE_BOOLEAN) ? "Yes" : "No";

		$data = $this->user_model->update_credit_withdraw_status($id,$status);
		echo json_encode(array("done"=>$data));
	}

    public function delete_users() {
        foreach ($this->input->post('rData') as $_id) {
            $id = sanitizeInput($_id, FILTER_VALIDATE_INT);
            $data = $this->user_model->delete_user($id);
        }

        echo json_encode(array("done"=>$data));
    }

    public function about() {
        $data['page_description'] = $this->about_model->get_content();
        $this->template->set_breadcrumb('Admin', asset_url('admin'));
        $this->template->set_breadcrumb('About', asset_url('admin/about'));
        $this->template->set_layout(DEFAULT_LAYOUT)->build('admin/about', $data);
    }

	public function quotes() {
		$total = $this->quote_model->getTotalQuotes();
		$data['quote_list'] = $this->quote_model->get_all_quotes(null, null, 10, 0);

		$data['deferLoading'] = array("filtered" => $total, "total" => $total);

		$this->template->set_breadcrumb('Admin', asset_url('admin'));
		$this->template->set_breadcrumb('Quotes', asset_url('admin/quotes'));
		$this->template->set_layout(DEFAULT_LAYOUT)->build('admin/quotes', $data);
	}

	public function getQuotes() {
		$draw = sanitizeInput($this->input->get("draw"), FILTER_VALIDATE_INT);

        $limit = sanitizeInput($this->input->get("length"), FILTER_VALIDATE_INT);
		$offset = sanitizeInput($this->input->get("start"), FILTER_VALIDATE_INT);
		$search = sanitizeInput($this->input->get("search[value]", true), FILTER_SANITIZE_STRING);

		$by = sanitizeInput($this->input->get("order[0][column]"), FILTER_VALIDATE_INT);
		$order = array("by" => sanitizeInput($this->input->get("columns[" . $by . "][data]"), FILTER_SANITIZE_STRING), "arrange" => sanitizeInput($this->input->get("order[0][dir]"), FILTER_SANITIZE_STRING));
		
		$total = $this->quote_model->getTotalQuotes();
		$quotes = $this->quote_model->get_all_quotes($search, $order); 
		
		$data = array(
			"draw" => $draw,
			"recordsTotal" => $total,
			"recordsFiltered" => count($quotes),
			"data" => array_slice($quotes, $offset, $limit)
		);

		echo json_encode($data);
	}

	public function add_quote() {
        $data = array(
    		'category' => sanitizeInput($this->input->post('quote_category', true), FILTER_SANITIZE_STRING),
            'description' => sanitizeInput($this->input->post('quote_description', true), FILTER_SANITIZE_STRING),
            'source' => sanitizeInput($this->input->post('quote_source', true), FILTER_SANITIZE_STRING),
            'order_no' => sanitizeInput($this->input->post('quote_order', true), FILTER_SANITIZE_STRING),            
    	);

		$data = $this->quote_model->insert_quotes($data);
		echo $data;
	}

	public function update_quote() {
        $id = sanitizeInput($this->input->post('update_id', true), FILTER_SANITIZE_STRING);

		$data = array(
    		'category' => sanitizeInput($this->input->post('quote_category', true), FILTER_SANITIZE_STRING),
            'description' => sanitizeInput($this->input->post('quote_description', true), FILTER_SANITIZE_STRING),
            'source' => sanitizeInput($this->input->post('quote_source', true), FILTER_SANITIZE_STRING),
            'order_no' => sanitizeInput($this->input->post('quote_order', true), FILTER_SANITIZE_STRING),            
    	);

		$data = $this->quote_model->update_quotes($id, $data);
		echo $data; exit;
	}

	public function deletequote() {
		$id = sanitizeInput($this->input->post('rData'), FILTER_VALIDATE_INT);

		$data = $this->quote_model->delete_quote($id);
		echo json_encode(array("result"=>$data));
	}

	public function getquote() {
		$id = sanitizeInput($this->input->post('id'), FILTER_VALIDATE_INT);
		if($id) {
			$result = $this->quote_model->get_quote_detail($id);
			echo json_encode($result);
		}
	}

	public function make_featured_quote() {
		$id = sanitizeInput($this->input->post('id'), FILTER_VALIDATE_INT);
		$status = sanitizeInput($this->input->post('status'), FILTER_VALIDATE_BOOLEAN) ? "Yes" : "No";

		$data = $this->quote_model->update_featured_quote($id,$status);
		echo json_encode(array("done"=>$data));
	}

	public function faq() {
		$data['categories'] = $this->faq_model->getCategories();
		$data['subcategories'] =  $this->faq_model->getSubCategories();
		$data['questions'] =  $this->faq_model->getQuestions();

		$this->template->setSeo(2);
		$this->template->set_layout(DEFAULT_LAYOUT)->build('admin/faq', $data);
	}

	public function news() {
		$data['articles'] = $this->news_model->getEntries(false, -1);
		$this->template->set_layout(DEFAULT_LAYOUT)->build('admin/news', $data);
	}

	public function blog() {
		$data['posts'] = $this->blog_model->getEntries(false, -1);
		$this->template->set_layout(DEFAULT_LAYOUT)->build('admin/blog', $data);
	}

	public function games($user_id = '') {
		if (!empty($user_id)) {
			$this->template->set_breadcrumb('Admin', asset_url('admin'));
			$this->template->set_breadcrumb('Games', asset_url('admin/games'));

			$total = $this->manage_games_model->getTotalGames($user_id);
			$data['games_list'] = $this->manage_games_model->get_user_games($user_id, null, null, 10, 0);
			$data['user_id'] = $user_id;
		} else {
			$total = $this->manage_games_model->getTotalGames();
			$data['games_list'] = $this->manage_games_model->get_all_games(null, null, 10, 0);
		}

		$data['deferLoading'] = array("filtered" => $total, "total" => $total);

		$this->template->set_layout(DEFAULT_LAYOUT)->build('admin/games', $data);
    }

	public function getGames() {
		$user_id = sanitizeInput($this->input->get("user_id"), FILTER_VALIDATE_INT);

        $draw = sanitizeInput($this->input->get("draw"), FILTER_VALIDATE_INT);

        $limit = sanitizeInput($this->input->get("length"), FILTER_VALIDATE_INT);
		$offset = sanitizeInput($this->input->get("start"), FILTER_VALIDATE_INT);
		$search = sanitizeInput($this->input->get("search[value]", true), FILTER_SANITIZE_STRING);

		$by = sanitizeInput($this->input->get("order[0][column]"), FILTER_VALIDATE_INT);
		$order = ($by == 0) ? null : array("by" => sanitizeInput($this->input->get("columns[" . $by . "][data]"), FILTER_SANITIZE_STRING), "arrange" => sanitizeInput($this->input->get("order[0][dir]"), FILTER_SANITIZE_STRING));
		
		if (isset($user_id) && $user_id != "") {
			$total = $this->manage_games_model->getTotalGames($user_id);
			$games = $this->manage_games_model->get_user_games($user_id, $search, $order);
		} else {
			$total = $this->manage_games_model->getTotalGames();
			$games = $this->manage_games_model->get_all_games($search, $order);
		}

		foreach ($games as $key => $value) {
            $image = getImagePathSize($value->Game_Image, 'admin_games');
			$games[$key]->Game_Image = $image;
		}
		
		$data = array(
			"draw" => $draw,
			"recordsTotal" => $total,
			"recordsFiltered" => count($games),
			"data" => (($limit < 0) ? $games : array_slice($games, $offset, $limit))
		);
		
		echo json_encode($data);
	}

    public function game_server() {
		$id = sanitizeInput($this->input->post('id'), FILTER_VALIDATE_INT);
		$status = sanitizeInput($this->input->post('status'), FILTER_SANITIZE_STRING);
        
		$data = $this->manage_games_model->update_game_server($id,$status);
		echo json_encode(array("done"=>$data));
	}

	 public function game_status() {
		$id = sanitizeInput($this->input->post('id'), FILTER_VALIDATE_INT);
		$status = sanitizeInput($this->input->post('status'), FILTER_SANITIZE_STRING);

		$data = $this->manage_games_model->update_game_status($id,$status);
		echo json_encode(array("done"=>$data));
	}

	public function game_active() {
		$id = sanitizeInput($this->input->post('id'), FILTER_VALIDATE_INT);
		$status = sanitizeInput($this->input->post('status'), FILTER_SANITIZE_STRING);

		$data = $this->manage_games_model->update_game_active($id,$status);
		echo json_encode(array("done"=>$data));
	}

	public function game_remove() {
		foreach ($this->input->post('rData') as $_id) {
            $id = sanitizeInput($_id, FILTER_VALIDATE_INT);
		    $data = $this->manage_games_model->delete_game($id);
	    }

		echo json_encode(array("done"=>$data));
	}

	public function subscriptions() {
		$data['subscribe_list'] = $this->user_model->get_subscription_list();

		$this->template->set_breadcrumb('Subscriptions', asset_url('admin/subscriptions'));
		$this->template->set_layout(DEFAULT_LAYOUT)->build('admin/subscriptions', $data);
    }

	public function coupons() {
		$total = $this->coupon_model->getTotalCoupons();
		$data['coupons'] = $this->coupon_model->get_all_coupons(null, null, 10, 0);

		$data['deferLoading'] = array("filtered" => $total, "total" => $total);

		$this->template->set_breadcrumb('Coupons', asset_url('admin/coupons'));
		$this->template->set_layout(DEFAULT_LAYOUT)->build('admin/coupons', $data);
	}

	public function getCoupons() {
		$draw = sanitizeInput($this->input->get("draw"), FILTER_VALIDATE_INT);

        $limit = sanitizeInput($this->input->get("length"), FILTER_VALIDATE_INT);
		$offset = sanitizeInput($this->input->get("start"), FILTER_VALIDATE_INT);
		$search = sanitizeInput($this->input->get("search[value]", true), FILTER_SANITIZE_STRING);

		$by = sanitizeInput($this->input->get("order[0][column]"), FILTER_VALIDATE_INT);
		$order = array("by" => sanitizeInput($this->input->get("columns[" . $by . "][data]"), FILTER_SANITIZE_STRING), "arrange" => sanitizeInput($this->input->get("order[0][dir]"), FILTER_SANITIZE_STRING));
		
		$total = $this->coupon_model->getTotalCoupons();
		$coupons = $this->coupon_model->get_all_coupons($search, $order);
		
		$data = array(
			"draw" => $draw,
			"recordsTotal" => $total,
			"recordsFiltered" => count($coupons),
			"data" => array_slice($coupons, $offset, $limit)
		);

		echo json_encode($data);
	}

	public function create_new_coupon() {
		$text = sanitizeInput($this->input->post('description', true), FILTER_SANITIZE_STRING);
		$type = sanitizeInput($this->input->post('amount'), FILTER_VALIDATE_INT);

		$data = $this->coupon_model->add_coupon($text,$type);
		echo json_encode(array("done"=>$data));
	}

	public function update_coupon($id, $content,$name) {
		$id = sanitizeInput($this->input->post('id'), FILTER_VALIDATE_INT);
		$content = sanitizeInput($this->input->post('content', true), FILTER_SANITIZE_STRING);
		$name = sanitizeInput($this->input->post('name', true), FILTER_SANITIZE_STRING);

		$data = $this->coupon_model->update_coupon_content($id, $content, $name);
		echo json_encode(array("done"=>$data));
 	}

	public function delete_coupons() {
		foreach ($this->input->post('id') as $_id) {
            $id = sanitizeInput($_id, FILTER_VALIDATE_INT);
			$data = $this->coupon_model->delete_coupon_content($id);
		}

		echo json_encode(array("done"=>$data));
	}

 	public function coupon_status() {
		$id = sanitizeInput($this->input->post('id'), FILTER_VALIDATE_INT);
		$status = sanitizeInput($this->input->post('status'), FILTER_VALIDATE_BOOLEAN) ? "Yes" : "No";

		$data = $this->coupon_model->update_coupon_status($id,$status);
		echo json_encode(array("done"=>$data));
	}

    public function emails() {
 		$data['email_description'] = $this->emails_model->get_content();

		$this->template->set_breadcrumb('Emails', asset_url('admin/emails'));
		$this->template->set_layout(DEFAULT_LAYOUT)->build('admin/emails', $data);
    }

    public function update_admin_email() {
 	 	$id = sanitizeInput($this->input->post('id'), FILTER_VALIDATE_INT);
 		$subject = sanitizeInput($this->input->post('subject', true), FILTER_SANITIZE_STRING);
 		$description = sanitizeInput($this->input->post('description', true), FILTER_SANITIZE_STRING);

 		$data = $this->emails_model->update_email_content($id,$subject,$description);
 		echo json_encode(array("done"=>$data));
    }

 	public function feedback() {
		$total = $this->feedback_model->getTotalFeedback();
		$data['feedback_list'] = $this->feedback_model->get_all_feedbacks(null, null, 10, 0);

		$data['deferLoading'] = array("filtered" => $total, "total" => $total);

		$this->template->set_breadcrumb('Feedback', asset_url('admin/feedback'));
		$this->template->set_layout(DEFAULT_LAYOUT)->build('admin/feedback', $data);
	}

	public function getFeedback() {
		$draw = sanitizeInput($this->input->get("draw"), FILTER_VALIDATE_INT);

        $limit = sanitizeInput($this->input->get("length"), FILTER_VALIDATE_INT);
		$offset = sanitizeInput($this->input->get("start"), FILTER_VALIDATE_INT);
		$search = sanitizeInput($this->input->get("search[value]", true), FILTER_SANITIZE_STRING);

		$by = sanitizeInput($this->input->get("order[0][column]"), FILTER_VALIDATE_INT);
		$order = array("by" => sanitizeInput($this->input->get("columns[" . $by . "][data]"), FILTER_SANITIZE_STRING), "arrange" => sanitizeInput($this->input->get("order[0][dir]"), FILTER_SANITIZE_STRING));
		
		$total = $this->feedback_model->getTotalFeedback();
		$feedback = $this->feedback_model->get_all_feedbacks($search, $order);
		
		$data = array(
			"draw" => $draw,
			"recordsTotal" => $total,
			"recordsFiltered" => count($feedback),
			"data" => array_slice($feedback, $offset, $limit)
		);

		echo json_encode($data);
	}

	public function load_feedback_images($id = '') {
		$id = sanitizeInput($id, FILTER_VALIDATE_INT);

		$feedback_data = $this->db->get_where('feedback_images',['feedback_id'=>$id])->result();

		$modal_content = '';
		if (!empty($feedback_data)) {
			$cnt = 0;
			foreach ($feedback_data as $feedback_info) {
				
				$feedImage = getImagePathSize($feedback_info->feedback_image, 'game_details_slideshow_main_image');

				$modal_content .= '<div class="gallery"><a target="_blank" href="'.$feedImage['image'].'"><img src="'.$feedImage['image'].'" width="600" height="400"></a></div>';
				$cnt++;
			}

		} else {
			$modal_content .= '<div class="gallery"> No Image Found</div>';
		}

		echo $modal_content;
	}

	public function distributions() {
		$total = $this->transaction_model->getTotalDistributions();
		$data['distributions'] = $this->transaction_model->getAllDistributions(null, null, 10, 0);

		$data['deferLoading'] = array("filtered" => $total, "total" => $total);

		$this->template->set_breadcrumb('Distributions', asset_url('admin/distributions'));
		$this->template->set_layout(DEFAULT_LAYOUT)->build('admin/distributions', $data);
	}

	public function getDistributions() {
		$where = (int)$this->input->get("filter");

		$draw = sanitizeInput($this->input->get("draw"), FILTER_VALIDATE_INT);

        $limit = sanitizeInput($this->input->get("length"), FILTER_VALIDATE_INT);
		$offset = sanitizeInput($this->input->get("start"), FILTER_VALIDATE_INT);
		$search = sanitizeInput($this->input->get("search[value]", true), FILTER_SANITIZE_STRING);

		$by = sanitizeInput($this->input->get("order[0][column]"), FILTER_VALIDATE_INT);
		$order = array("by" => sanitizeInput($this->input->get("columns[" . $by . "][data]"), FILTER_SANITIZE_STRING), "arrange" => sanitizeInput($this->input->get("order[0][dir]"), FILTER_SANITIZE_STRING));
		
		$total = $this->transaction_model->getTotalDistributions();
		$distributions = $this->transaction_model->getAllDistributions($where, $search, $order);
		
		$data = array(
			"draw" => $draw,
			"recordsTotal" => $total,
			"recordsFiltered" => count($distributions),
			"data" => array_slice($distributions, $offset, $limit)
		);

		echo json_encode($data);
	}

	public function getDistributionDetails() {
		$id = sanitizeInput($this->input->post('id'), FILTER_VALIDATE_INT);

		$distributions = $this->transaction_model->getDistributionDetails($id);
		if (!empty($distributions)) {
			echo json_encode(array("status" => "success", "data" => $distributions));
		} else {
			echo json_encode(array("status" => "failed"));
		}
	}

	public function allTransactions() {
		if (getprofile()->usertype=='2') {
			$total = $this->transaction_model->getTotalTransactions();
	 		$data['transactions'] = $this->transaction_model->get_all_transactions(null, null, null, 10, 0);
		}

		$data['deferLoading'] = array("filtered" => $total, "total" => $total);
		
		$this->template->set_breadcrumb('Transactions', asset_url('transactions'));
		$this->template->set_layout(DEFAULT_LAYOUT)->build('admin/transactions', $data);
	}

	public function userTransactions($user_id='') {
		if (($user_id!='') && (getprofile()->usertype=='2')) {
			$total = $this->transaction_model->getTotalTransactions($user_id);
			$data['user_id'] = $user_id;
			$data['sum'] = $this->transaction_model->get_sum($user_id);
			$data['transactions'] = $this->transaction_model->get_all_transactions($user_id, null, null, 10, 0);
		}

		$data['deferLoading'] = array("filtered" => $total, "total" => $total);

		$this->template->set_breadcrumb('Transactions', asset_url('admin/allTransactions'));
		$this->template->set_breadcrumb('User');
		$this->template->set_layout(DEFAULT_LAYOUT)->build('admin/userTransactions', $data);

	}

	public function getTransactions() {
		$user_id = (int)$this->input->get("user_id");

		$draw = sanitizeInput($this->input->get("draw"), FILTER_VALIDATE_INT);

        $limit = sanitizeInput($this->input->get("length"), FILTER_VALIDATE_INT);
		$offset = sanitizeInput($this->input->get("start"), FILTER_VALIDATE_INT);
		$search = sanitizeInput($this->input->get("search[value]", true), FILTER_SANITIZE_STRING);

		$by = sanitizeInput($this->input->get("order[0][column]"), FILTER_VALIDATE_INT);
		$order = array("by" => sanitizeInput($this->input->get("columns[" . $by . "][data]"), FILTER_SANITIZE_STRING), "arrange" => sanitizeInput($this->input->get("order[0][dir]"), FILTER_SANITIZE_STRING));
		
		if (isset($user_id) && $user_id != "") {
			$total = $this->transaction_model->getTotalTransactions($user_id);
			$transactions = $this->transaction_model->get_all_transactions($user_id, $search, $order);
		} else {
			$total = $this->transaction_model->getTotalTransactions();
			$transactions = $this->transaction_model->get_all_transactions(null, $search, $order);
		}
		
		$data = array(
			"draw" => $draw,
			"recordsTotal" => $total,
			"recordsFiltered" => count($transactions),
			"data" => array_slice($transactions, $offset, $limit)
		);

		echo json_encode($data);
	}

	public function getUserInfo() {
		$user_id = sanitizeInput($this->input->post('userId'), FILTER_VALIDATE_INT);

		$data = $this->user_model->get_user_profile($user_id);
		echo json_encode(array("done"=>$data));
	}

	public function flags($slug) {
		$total = $this->flags_model->getTotalFlags($slug);
		$data['flag_list'] = $this->flags_model->fetch_flag_detail($slug, null, null, 10, 0);

		$data['deferLoading'] = array("filtered" => $total, "total" => $total);

		$this->template->set_breadcrumb('Admin', asset_url('flags'));			
		$this->template->set_layout(DEFAULT_LAYOUT)->build('admin/flags', $data);
	}

	public function getFlags() {
		$slug = sanitizeInput($this->input->get("slug", true), FILTER_VALIDATE_URL);

		$draw = sanitizeInput($this->input->get("draw"), FILTER_VALIDATE_INT);

        $limit = sanitizeInput($this->input->get("length"), FILTER_VALIDATE_INT);
		$offset = sanitizeInput($this->input->get("start"), FILTER_VALIDATE_INT);
		$search = sanitizeInput($this->input->get("search[value]", true), FILTER_SANITIZE_STRING);

		$by = sanitizeInput($this->input->get("order[0][column]"), FILTER_VALIDATE_INT);
		$order = array("by" => sanitizeInput($this->input->get("columns[" . $by . "][data]"), FILTER_SANITIZE_STRING), "arrange" => sanitizeInput($this->input->get("order[0][dir]"), FILTER_SANITIZE_STRING));
		
		$total = $this->flags_model->getTotalFlags($slug);
		$flags = $this->flags_model->fetch_flag_detail($slug, $search, $order);
		
		$data = array(
			"draw" => $draw,
			"recordsTotal" => $total,
			"recordsFiltered" => count($flags),
			"data" => array_slice($flags, $offset, $limit)
		);

		echo json_encode($data);
	}

	public function short_urls() {
		$total = $this->short_model->getTotalShortUrls();
		$data['short_urls'] = $this->short_model->getShortUrls();

		$data['deferLoading'] = array("filtered" => $total, "total" => $total);

		$this->template->set_breadcrumb('Admin', asset_url('short_urls'));			
		$this->template->set_layout(DEFAULT_LAYOUT)->build('admin/short_urls', $data);
	}

	public function getShortUrls() {
		$draw = sanitizeInput($this->input->get("draw"), FILTER_VALIDATE_INT);

        $limit = sanitizeInput($this->input->get("length"), FILTER_VALIDATE_INT);
		$offset = sanitizeInput($this->input->get("start"), FILTER_VALIDATE_INT);
		$search = sanitizeInput($this->input->get("search[value]", true), FILTER_SANITIZE_STRING);

		$by = sanitizeInput($this->input->get("order[0][column]"), FILTER_VALIDATE_INT);
		$order = array("by" => sanitizeInput($this->input->get("columns[" . $by . "][data]"), FILTER_SANITIZE_STRING), "arrange" => sanitizeInput($this->input->get("order[0][dir]"), FILTER_SANITIZE_STRING));
		
		$total = $this->short_model->getTotalShortUrls();
		$short_urls = $this->short_model->getShortUrls($search, $order);	
		
		$data = array(
			"draw" => $draw,
			"recordsTotal" => $total,
			"recordsFiltered" => count($short_urls),
			"data" => array_slice($short_urls, $offset, $limit)
		);

		echo json_encode($data);
	}

	public function addShortUrl() {
		$url_slug = sanitizeInput($this->input->post("url", true), FILTER_VALIDATE_URL);
		$short_url_slug = sanitizeInput($this->input->post("short_url", true), FILTER_SANITIZE_STRING);

		$created = $this->short_model->createShortURL($this->user_id, $url_slug, $short_url_slug);
		if ($created) {
			echo json_encode(array("status" => "success"));
		} else {
			echo json_encode(array("status" => "failed"));
		}
	}

	public function editShortUrl() {
		$id = sanitizeInput($this->input->post("id"), FILTER_VALIDATE_INT);
		$url_slug = sanitizeInput($this->input->post("url", true), FILTER_VALIDATE_URL);
		$short_url_slug = sanitizeInput($this->input->post("short_url", true), FILTER_SANITIZE_STRING);

		$edited = $this->short_model->editShortURL($this->user_id, $id, $url_slug, $short_url_slug);
		if ($edited) {
			echo json_encode(array("status" => "success"));
		} else {
			echo json_encode(array("status" => "failed"));
		}
	}

	public function deleteShortUrl() {
		$id = sanitizeInput($this->input->post("id"), FILTER_VALIDATE_INT);

		$deleted = $this->short_model->deleteShortURL($id);
		if ($deleted) {
			echo json_encode(array("status" => "success"));
		} else {
			echo json_encode(array("status" => "failed"));
		}
	}

	public function paymentkeys() {
		$data['paypalDevKeys'] = $this->buy_credit_model->getAllPaymentMethods('paypal', 'dev');
		$data['stripeDevKeys'] = $this->buy_credit_model->getAllPaymentMethods('stripe', 'dev');
		$data['paypalProdKeys'] = $this->buy_credit_model->getAllPaymentMethods('paypal', 'prod');
		$data['stripeProdKeys'] = $this->buy_credit_model->getAllPaymentMethods('stripe', 'prod');
		$data['server'] =  (asset_url() === 'https://winwinlabs.org/') ? 'prod' : 'dev';

		$this->template->set_layout(DEFAULT_LAYOUT)->build('admin/paymentMethodKey', $data);
	}

	public function keyValUpdate() {
		$updatedKeyVal = sanitizeInput($this->input->post('keyval'), FILTER_SANITIZE_STRING);
		$keyId = sanitizeInput($this->input->post('keyId'), FILTER_SANITIZE_STRING);

		$response = $this->buy_credit_model->updatePaymentMethodKey($updatedKeyVal, $keyId);
		echo json_encode(array("done"=>$response));
	}

	public function keyValEnvSwitch() {
		$server =  (asset_url() === 'https://winwinlabs.org/') ? 'prod' : 'dev';
		$keyValSwitch = $this->input->post('switchVal');
		$method = $this->input->post('methodname');
		$response = $this->buy_credit_model->switchPaymentMethodKey($keyValSwitch, $server, $method);

		echo json_encode(array("done"=>$response));
	}

	public function referral() {
		$data['referralData'] = $this->referral_model->get_referrals();
		foreach($data['referralData'] as $key => &$val){
			$val['referralDateTimeRange'] = $val['start_date'] . ' to ' . $val['end_date'];
		}

		$data['users'] = $this->get_user_list('p');

		$this->template->set_breadcrumb('Referral', asset_url('admin/referral'));
		$this->template->set_layout(DEFAULT_LAYOUT)->build('admin/referral', $data);
	}

	public function get_user_list($p='') {
		$userList = $this->user_model->get_user_id_name();
		if ($p) {
			return $userList;
		} else { 
			echo json_encode($userList);
		}
	}

	public function referrral_actions() {
		$action = sanitizeInput(isset($_GET['action']) ? $_GET['action'] : '', FILTER_SANITIZE_STRING);
		switch ($action){
			case('save'):
				echo $this->referral_model->save_referral();
				break;
			case('delete'):
				echo $this->referral_model->delete_referral();
				break;
			default:
				echo json_encode(array('status'=>'failed','error'=>'unknown action'));
				break;
		}
	}

	public function isReferralNameDuplicate() {
		$referral  = $this->input->post('referral');
	
		echo json_encode($this->referral_model->checkReferralNameDuplicate($referral));
	}

	public function mapFeatures() {
		$data='';
		$this->template->set_breadcrumb('Map Features', asset_url('admin/mapFeatures'));
		$this->template->set_layout(DEFAULT_LAYOUT)->build('admin/maps/mapFeatureForm', $data);
	}

	public function insert_map() {
		$data = array(
			'MapName' => $this->input->post('MapName'),
			'Zoom' => $this->input->post('Zoom'),
			'CenterLat' => $this->input->post('CenterLat'),
			'CenterLon' => $this->input->post('CenterLon'),
			'MapType' => $this->input->post('MapType'),
			'StreetViewControl' => $this->input->post('StreetViewControl') ? 1 : 0,
			'ZoomControl' => $this->input->post('ZoomControl') ? 1 : 0,
			'MapTypeControl' => $this->input->post('MapTypeControl') ? 1 : 0,
			'FullscreenControl' => $this->input->post('FullscreenControl') ? 1 : 0,
			'Scrollwheel' => $this->input->post('Scrollwheel') ? 1 : 0,
			'ScaleControl' => $this->input->post('ScaleControl') ? 1 : 0,
			'RotateControl' => $this->input->post('RotateControl') ? 1 : 0,
			'GestureHandling' => $this->input->post('GestureHandling'),
			'StreetViewControlPosition' => $this->input->post('StreetViewControlPosition'),
			'ZoomControlPosition' => $this->input->post('ZoomControlPosition'),
			'MapTypeControlPosition' => $this->input->post('MapTypeControlPosition'),
			'FullscreenControlPosition' => $this->input->post('FullscreenControlPosition')
		);
	
		if ($this->maps_model->insert_map_data($data)) {
			var_dump('sucess'); exit;
		// Redirect to a success page or show a success message
			// Redirect example:
			//redirect('admin/mapFeatures');
		} else {
			var_dump('failed'); exit;
		}
	}

	public function update_role() {
        $user_id = sanitizeInput($this->input->post('id'), FILTER_VALIDATE_INT);
		$role = sanitizeInput($this->input->post('role'), FILTER_VALIDATE_INT);
        $permission = sanitizeInput($this->input->post('permission'), FILTER_VALIDATE_INT);
        $user = getprofile($user_id);

		$hasRoleUpdated = $this->user_model->update_user_role($user->user_id, $permission);
		// if ($hasRoleUpdated) {
		// 	$emailData = array (
		// 		"username" => $user->username,
		// 		"role"=> $role,
		// 	);

		// 	$notes = "You've been ben given " + $emailData['role'] + " role";
		// 	$subject = "User Permission Updated";
		// 	$view = $this->load->view('emails/coupon-email', $emailData, true);
	
		// 	//sendNotificationAndEmail("credits_awarded", $user->user_id, $this->user_id, $notes, "credits", "receive", null, null, $subject, $view);
		// }

		echo json_encode(array("done"=>$hasRoleUpdated));
	}
}
