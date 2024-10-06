<?php
defined('BASEPATH') or exit('No direct script access allowed');
require('application/libraries/stripe/stripe/lib/Stripe.php');
require('application/libraries/stripe-php-master/init.php');
require_once __DIR__ . '/Email.php';

class Buycredits extends CI_Controller {

    public function __construct() {
        parent::__construct();

        check_login();
        $this->load->model('buy_credit_model');
        $this->load->model('charity_model');
        $this->load->model('transaction_model');
        $this->load->model('user_model');
        $this->load->model('notification_model');

        $this->load->library('template');
        $this->load->library('session');

        $this->template->set_breadcrumb('Home', asset_url());
    }

    public function getEnv() {
        return (asset_url() === 'https://winwinlabs.org/') ? 'prod' : 'dev';
    }

    public function index($form_name = '') {
        $user_id = $this->session->userdata('user_id');

        if (isset($_SESSION['playGameError'])) {
            switch ($_SESSION['playGameError']) {
                case "insufficient":
                    $this->session->set_flashdata('icon', 'warning');
                    $this->session->set_flashdata('prompt_title', 'Whoops!');
                    $this->session->set_flashdata('message', 'Insufficient credits for that game!');
                    break;
                default:
                    break;
            }
            unset($_SESSION['playGameError']);
        }

        $paypalPaymentKey = $this->buy_credit_model->getPaymentMethodList('paypal', $this->getEnv())[0];
        $data['paypalID'] = $paypalPaymentKey['method_id'];
        $data['paypalURL'] = $paypalPaymentKey['method_url'];

        $data['show_form'] = $form_name;

        $data['savedcardlist'] = $this->buy_credit_model->saved_card_list($user_id);
        $data['savedbanklist'] = $this->buy_credit_model->saved_bank_list($user_id);

        function search($array, $key, $value)
        {
            $results = array();

            if (is_array($array)) {
                if (isset($array[$key]) && $array[$key] == $value) {
                    $results[] = $array;
                }

                foreach ($array as $subarray) {
                    $results = array_merge($results, search($subarray, $key, $value));
                }
            }

            return $results;
        }

        $data['verifiedBankCount'] = count(search($data['savedbanklist'], 'bank_verification_status', 1));
       
        $data['loginuserdetails'] = $this->user_model->get_loginuser();

        $this->template->set_breadcrumb('Buy Credits', asset_url('buycredits'));
        $this->template->set_layout(DEFAULT_LAYOUT)->build('buycredits/index', $data);
    }

    public function paypal() {
        $this->index('paypal');
    }

    public function success() {
        $user_id = $this->session->userdata('user_id');
        $user = getprofile($user_id);
        
        $item_number = '';
        $date = date('Y-m-d H:i:s');

        $paypalInfo = $this->input->get();
        $txn_id = $paypalInfo['tx'];
        $payment_gross = round($paypalInfo['payment_gross'], 2);
        $currency_code = $paypalInfo['cc'];
        $payment_status = $paypalInfo['st'] == 'Completed' ? 'succeeded' : 'failed';

        //get the exact credit       
        $totalcredits = $paypalInfo['payment_gross'] -  $paypalInfo['payment_fee'];

        $total_charges = $payment_gross;
        $totalsumofcredits = $this->getTotalSumOfCredits($user_id, $totalcredits);

        $ref_no = $this->buy_credit_model->getReferenceNumber();
        $method = "Paypal";
        $notes = "Your Donation with " .$method. " for $" .$totalcredits. " - " .strtoupper($payment_status);

        $this->buy_credit_model->insertPayment($user_id, $totalcredits, $item_number, $txn_id, $payment_gross, $total_charges, $currency_code, 1, 0, $payment_status, $date, "Donation with " . $method, $totalsumofcredits, $ref_no, 2);
        if ($this->db->affected_rows() > 0) {
            $this->paymentSuccess();
            $this->sendReceipt($user, $date, $ref_no, $totalcredits, $total_charges, $method, $method);
            $this->notification_model->insertNotification($user_id, $user_id, $notes, 'Donation', 'transaction', '','');
            $this->paymentRedirect("transactions");
        } else {
            $this->paymentFailure();
        }
    }

    public function stripe() {
        $stripe = $this->getStripePaymentKeys();
        \Stripe\Stripe::setApiKey($stripe['secret_key']);

        $user_id = $this->session->userdata('user_id');
        $user = getprofile($user_id);
        $openstripe = 0;
        $save_card = 1;

        $token = sanitizeInput($this->input->post("stripeToken", true), FILTER_SANITIZE_STRING);
        $card_zip = sanitizeInput($this->input->post("card-zip", true), FILTER_SANITIZE_STRING);
        $card_id = sanitizeInput($this->input->post("payment-card", true), FILTER_SANITIZE_STRING);
        $amount = sanitizeInput($this->input->post('amountstripe'), FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_THOUSAND);
        $default_payment = sanitizeInput($this->input->post("save-card-yes"), FILTER_VALIDATE_BOOLEAN) ? 1 : 0;


        if(!$user->customer_id) {
            $this->createNewStripeCustomer($amount, $user, $token, $default_payment, $save_card, $card_zip);
        }

        if ($user->customer_id && $card_id) {
            $card_existing = \Stripe\Customer::retrieveSource($user->customer_id, $card_id);
            $this->chargeStripeCard($amount, $user_id, $user->customer_id, $card_existing);
        }

        if ($user->customer_id) {
            $this->addStripeCard($amount, $user_id, $user->customer_id, $default_payment, $save_card, $token, $card_zip);
        }
    }

    public function createNewStripeCustomer($amount, $user, $token, $default_payment, $save_card, $card_zip){
        try {
            $customer = \Stripe\Customer::create([
                'name' => $user->firstname. ' ' .$user->lastname,
                'email' => $user->email,
                // You can include other customer information or metadata here
            ]);

            $this->buy_credit_model->setCustomerID($user->user_id, $customer->id);

            $this->addStripeCard($amount, $user->user_id, $customer->id, $default_payment, $save_card, $token, $card_zip);
            //  echo 'Customer created: ' . $customer->id;
        } catch (\Stripe\Exception\CardException $e) {
            // Card error, handle appropriately
            echo 'Card Error: ' . $e->getMessage();
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            // Invalid request, handle appropriately
            echo 'Invalid Request: ' . $e->getMessage();
        } catch (\Stripe\Exception\AuthenticationException $e) {
            // Authentication error, handle appropriately
            echo 'Authentication Error: ' . $e->getMessage();
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            // Network communication error, handle appropriately
            echo 'API Connection Error: ' . $e->getMessage();
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Generic API error, handle appropriately
            echo 'Stripe API Error: ' . $e->getMessage();
        } catch (Exception $e) {
            // Other generic exception, handle appropriately
            echo 'Error: ' . $e->getMessage();
        }
    }

    public function addStripeCard($amount, $user_id, $customer_id, $default_payment, $save_card, $token, $card_zip) {
        $new_card_data = \Stripe\Token::retrieve($token);
        $cards = \Stripe\Customer::allSources($customer_id, ['object' => 'card']);

        $new_card_fingerprint = $new_card_data['card']['fingerprint'];
        $stripe_cards_fingerprints = array_column($cards['data'], 'fingerprint');

        $key = array_search($new_card_fingerprint, $stripe_cards_fingerprints);

        if ($key !== false) {    
            $this->duplicateCard();
        } else {
            try {
                \Stripe\Customer::createSource($customer_id, ['source' => $token]);

                if ($default_payment == 1) {
                    $this->buy_credit_model->updateDefaultPaymentMethod($user_id, 'card');
                }

                $cardStatus = $this->buy_credit_model->insertStripeDetails($user_id, $new_card_data['card'], $card_zip, 
                            'card', $default_payment, $save_card);

                if ($cardStatus) {
                    $this->chargeStripeCard($amount, $user_id, $customer_id, $new_card_data['card']);
                }

            } catch (\Stripe\Exception\CardException $e) {
                // Handle card exception
                $this->failedCard();
            } catch (Exception $e) {
                // Handle other exceptions
                $this->failedCard();
            }
        }
    }

    public function chargeStripeCard($amount, $user_id, $customer_id, $card) {
        try {
            $charge = \Stripe\Charge::create(['amount' => $amount * 100, 'currency' => 'usd', 'customer' => $customer_id, 
            'source' => $card->id, "description" => "Thank you for the donation!", ]);

            $txn_id = $charge->balance_transaction;
            $amount = number_format(($charge->amount / 100), 2);
            $payment_gross = $amount - $this->getStripeChargeFee($txn_id);

            $user = getprofile($user_id);
            $currency_code = "usd";
    
            $totalcredits = $payment_gross;
            $total_charges = round($amount, 2);
            $totalsumofcredits = $this->getTotalSumOfCredits($user_id, $totalcredits);
    
            $ref_no = $this->buy_credit_model->getReferenceNumber();
            $item_number = '';
            $date = date('Y-m-d H:i:s');

            $payment_status = $charge->status;
            $method = "Credit Card ending in " . $card->last4;
            $notes = "Your Donation with " .$method. " for $" .$payment_gross. " - " .strtoupper($payment_status);

            $this->buy_credit_model->insertPayment($user_id, $totalcredits, $item_number, $txn_id, $payment_gross, 
                    $total_charges, $currency_code, 1, 0, $payment_status, $date, "Donation with " . $method, 
                    $totalsumofcredits, $ref_no, 3);
            if ($this->db->affected_rows() > 0) {
                $this->sendReceipt($user, $date, $ref_no, $payment_gross, $total_charges, $method, 'Stripe');
                $this->notification_model->insertNotification($user_id, $user_id, $notes, 'Donation', 'transaction', '','');

                $this->successPayment();
            } else {
                $this->failedPayment();
            }
        } catch (Exception $e) {
            $this->failedPayment();
        }
    }

    public function duplicateCard() {
        $data = array(
            'name'   => 'bStatus',
            'value'  => "1", // duplicate card
            'expire' => '10', // Cookie expiration time 20 in seconds
        );
        $this->input->set_cookie($data);
        $this->paymentRedirect('buycredits/credit');
    }

    public function failedCard() {
        $data = array(
            'name'   => 'bStatus',
            'value'  => "2", // failed card
            'expire' => '10', // Cookie expiration time 20 in seconds
        );
        $this->input->set_cookie($data);
        $this->paymentRedirect('buycredits/credit');
    }

    public function failedPayment() {
        $data = array(
            'name'   => 'bStatus',
            'value'  => "3", // failed payment
            'expire' => '10', // Cookie expiration time 20 in seconds
        );
        $this->input->set_cookie($data);
        $this->paymentRedirect('buycredits/credit');
    }

    public function successPayment() {
        $data = array(
            'name'   => 'bStatus',
            'value'  => "4", // success payment
            'expire' => '10', // Cookie expiration time 20 in seconds
        );
        $this->input->set_cookie($data);
        $this->paymentRedirect('transactions');
    }

    public function addCustomerViaBankSuccess() {
        $data = array(
            'name'   => 'bStatus',
            'value'  => "5", // success customer
            'expire' => '10', // Cookie expiration time 20 in seconds
        );
        $this->input->set_cookie($data);
        $this->paymentRedirect('transactions');
    }

    public function successPaymentBank() {
        $data = array(
            'name'   => 'bStatus',
            'value'  => "6", // success payment
            'expire' => '20', // Cookie expiration time 20 in seconds
        );
        $this->input->set_cookie($data);
        $this->paymentRedirect('transactions');
    }

    public function achStripe() {
        $user_id = $this->session->userdata('user_id');
        $user = getprofile($user_id);

        $item_number = '';
        $save_card = 1;
        $openstripe = 0;

        $amount = sanitizeInput($this->input->post('amountstripe_ach'), FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_THOUSAND);
        $bank_token = $this->input->post('token_id');
        $account_number = sanitizeInput($this->input->post('account_number', true), FILTER_SANITIZE_STRING);
        $bank_id = !empty($account_number) ? "" : sanitizeInput($this->input->post('payment-bank', true), FILTER_SANITIZE_STRING);
        $default_payment = sanitizeInput($this->input->post("save-bank-yes"), FILTER_VALIDATE_BOOLEAN) ? 1 : 0;

        if (!isset($bank_token)) {
            if ($user->customer_id && $bank_id) {
                $stripe = $this->getStripePaymentKeys();
                \Stripe\Stripe::setApiKey($stripe['secret_key']);

                $card_existing = \Stripe\Customer::retrieveSource($user->customer_id, $bank_id);

                $this->chargeStripeBank($amount, $user->customer_id, $card_existing, $user, $user_id);
                exit();
            } else {
                $this->paymentFailure();
            }
        } else if (isset($bank_token)) {
            try {
                if(!$user->customer_id) {
                    $this->addStripeBankCustomer($user, $user_id, $bank_token, $default_payment, $save_card);
                    exit();
                }

                if ($user->customer_id) {
                    $this->addStripeBank($user_id, $user, $bank_token, $default_payment, $save_card);
                    exit();
                }
            } catch(Exception $e) {
                $openstripe = 1;
            }
        } 
    }

    public function addStripeBankCustomer($user, $user_id, $bank_token, $default_payment, $save_card) {
        $stripe = $this->getStripePaymentKeys();
        \Stripe\Stripe::setApiKey($stripe['secret_key']);

        $customer = \Stripe\Customer::create([
            'name' => $user->firstname. ' ' .$user->lastname,
            'email' => $user->email,
            // You can include other customer information or metadata here
        ]);

        $this->buy_credit_model->setCustomerID($user_id, $customer->id);

        $bank_account = \Stripe\Customer::createSource($customer->id, ['source' => $bank_token, ]);
        $this->buy_credit_model->insertStripeDetails($user_id, $bank_account, null, 'bank', $default_payment, $save_card);

        if ($default_payment == 1) {
            $this->buy_credit_model->updateDefaultPaymentMethod($user_id, 'card');
        }

        $this->addCustomerViaBankSuccess();
    }

    public function addStripeBank($user_id, $user, $bank_token, $default_payment, $save_card) {
        $stripe = $this->getStripePaymentKeys();
        \Stripe\Stripe::setApiKey($stripe['secret_key']);

        $new_bank_data = \Stripe\Token::retrieve($bank_token);
        $new_bank_fingerprint = $new_bank_data['bank_account']['fingerprint'];

        $bank_accounts = \Stripe\Customer::allSources($user->customer_id, ['object' => 'bank_account', ]);
        $stripe_banks_fingerprints = array_column($bank_accounts['data'], 'fingerprint');

        $key = array_search($new_bank_fingerprint, $stripe_banks_fingerprints);
        if ($key !== false) {
            $this->session->set_flashdata('prompt_title', 'Whoops!');
            $this->session->set_flashdata('message', 'This account already exists, try another bank account.');
            $this->session->set_flashdata('icon', 'error');

            redirect('buycredits/bank');
            exit;
        } else {
            $bank_account = \Stripe\Customer::createSource($user->customer_id, ['source' => $bank_token, ]);
            if ($default_payment == 1) {
                $this->buy_credit_model->updateDefaultPaymentMethod($user_id, 'bank');
            }

            $this->buy_credit_model->insertStripeDetails($user_id, $bank_account, null, 'bank', $default_payment, $save_card);

            $this->session->set_flashdata('prompt_title', 'Great!');
            $this->session->set_flashdata('message', 'You have successfully added your bank account.');
            $this->session->set_flashdata('icon', 'success');
            redirect('buycredits/bank');
            exit;
        }
    }
    

    public function chargeStripeBank($amount, $customerId, $bankId,  $user, $user_id) {
        $stripe = $this->getStripePaymentKeys();
        \Stripe\Stripe::setApiKey($stripe['secret_key']);

        try {
            // Create a charge
            $charge = \Stripe\Charge::create([
                'amount' => $amount*100,
                'currency' => 'usd',
                'source' => $bankId,
                'customer' => $customerId,
                'description' => 'Thank you for the donation!'
            ]);
        
            // Handle successful charge
        
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            // Handle invalid request errors
            echo 'Invalid request error: ' . $e->getMessage();
            // Display user-friendly message
            echo 'Invalid request. Please check your bank account details.';
        
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Handle Stripe errors
            $error_info = $e->getError();
            echo 'Stripe error: ' . $error_info->message;
        
        } catch (Exception $e) {
            // Handle unexpected errors
            echo 'Unexpected error: ' . $e->getMessage();
        }

        $date = date('Y-m-d H:i:s');
        $ref_no = $this->buy_credit_model->getReferenceNumber();
        $txn_id = $charge->balance_transaction;
        $payment_status = $charge->status;

        $amount = number_format(($charge->amount / 100), 2);

        if ($payment_status == 'pending') {
            $payment_gross = ($amount * (1 - 0.029)) - 0.30;
        }

        if ($payment_status == 'succeeded') {
            $payment_gross = round($amount - $this->getStripeChargeFee($txn_id),2);
        }

        $currency_code = "usd";
        $totalcredits = $payment_gross;
        $total_charges = round($amount, 2);
       
        if($payment_status == 'pending' || $payment_status == 'failed'){
            $creditToAdd = 0;
        } else {
            $creditToAdd = $totalcredits; 
        }

        $totalsumofcredits = $this->getTotalSumOfCredits($user_id, $creditToAdd);
        $method = "ACH ending in " . $charge['source']->last4;
        $notes = "Your Donation with " .$method. " for $" .$payment_gross. " - " .strtoupper($payment_status);

        $this->buy_credit_model->insertPayment($user_id, $totalcredits, '', $txn_id, $payment_gross, 
        $total_charges, $currency_code, 1, 0, $payment_status, $date, "Donation with " . $method, 
        $totalsumofcredits, $ref_no, 3);

        if ($this->db->affected_rows() > 0) {
            $this->sendReceipt($user, $date, $ref_no, $payment_gross, $total_charges, $method, 'Stripe', $payment_status); 
            $this->notification_model->insertNotification($user_id, $user_id, $notes, 'donation', 'transaction', '','');

            $this->successPaymentBank();
        } else {
            $this->paymentFailure();
        }
    }

    public function getStripeChargeFee($chargeId) {
        $stripe = $this->getStripePaymentKeys();
        \Stripe\Stripe::setApiKey($stripe['secret_key']);

        try {
            // Replace 'txn_123456789' with the ID of the specific balance transaction you want to retrieve
            $balanceTransaction = \Stripe\BalanceTransaction::retrieve($chargeId);
        
            // Check if the retrieved transaction is a charge
            if ($balanceTransaction->type === 'charge') {
                // Calculate total fees
                $totalFees = $balanceTransaction->fee / 100; // Convert fee amount to currency unit (e.g., dollars)
                
                return number_format($totalFees, 2); // Output total fees
            } else {
                echo 'This transaction is not a charge.';
            }
        } catch (\Stripe\Exception\ApiErrorException $e) {
            echo 'Error retrieving Stripe fees: ' . $e->getMessage();
        }
    }

    public function delete_cards() {
        $user_id = $this->session->userdata('user_id');
        $user = getprofile($user_id);

        $id = sanitizeInput($this->input->post("rData"), FILTER_VALIDATE_INT);

        $user_stripe_id = $this->buy_credit_model->getStripeDetails($id);

        $stripe = $this->getStripePaymentKeys();
        \Stripe\Stripe::setApiKey($stripe['secret_key']);

        $existing_customer = \Stripe\Customer::retrieve($user->customer_id);
        \Stripe\Customer::deleteSource($existing_customer->id, $user_stripe_id->payment_method_id);

        $data = $this->buy_credit_model->delete_card($id);

        echo json_encode(array("done" => $data));
    }

    public function stripe_bank_verification() {
        $user_id = $this->session->userdata('user_id');
        $user = getprofile($user_id);

        $id = sanitizeInput($this->input->post('id'), FILTER_VALIDATE_INT);
        $bank_id = sanitizeInput($this->input->post('bank'), FILTER_SANITIZE_STRING);

        $d1 = sanitizeInput($this->input->post('amtone'), FILTER_VALIDATE_FLOAT);
        $d2 = sanitizeInput($this->input->post('amtwo'), FILTER_VALIDATE_FLOAT);

        $stripe = $this->getStripePaymentKeys();
        \Stripe\Stripe::setApiKey($stripe['secret_key']);

        $bank_account = \Stripe\Customer::retrieveSource($user->customer_id, $bank_id);

        try {
            $verify_status = $bank_account->verify(['amounts' => [$d1, $d2]]);

            if ($verify_status['status'] == 'verified') {
                $verify = $this->buy_credit_model->stripeDetailsBankVerification($id, 1);

                echo json_encode(array("done" => $verify));
            }
        } catch(\Stripe\Error\Card $e) {
            $body = $e->getJsonBody();
            $err = $body['error'];

            echo json_encode(array("done" => $err['message']));
        }
    }

    public function cashout($form_name = '') {
        $data['show_form'] = $form_name;
        $data['total_credits'] = $this->transaction_model->get_sum($this->session->userdata('user_id'));
        $data['withdrawable_credits'] = $this->transaction_model->get_withdrawable_sum($this->session->userdata('user_id'));
        $data['stripe_payout_status'] = $this->user_model->get_stripe_payout_id_status();

        $this->template->set_breadcrumb('Withdraw Credits', asset_url('buycredits/cashout'));
        $this->template->set_layout(DEFAULT_LAYOUT)->build('buycredits/cashout', $data);
    }

    public function stripe_account() {
        $this->cashout('stripe');
    }

    public function paypal_account() {
        $this->cashout('paypal');
    }

    public function donationSuccess() {
        $user_id = $this->session->userdata('user_id');
        $user = getprofile($user_id);

        $item_number = '';
        $date = date('Y-m-d H:i:s');

        $paypalInfo = $this->input->get();
        $txn_id = $paypalInfo['tx'];
        $payment_gross = round($paypalInfo['amt'], 2);
        $currency_code = $paypalInfo['cc'];
        $payment_status = $paypalInfo['st'];
        $donated_fundraiser_name = $paypalInfo['item_name'];

        //get the exact credit
        $totalcredits = round(($payment_gross - 0.3) - ($payment_gross * 0.029), 2);
        $total_charges = $payment_gross;
        $totalsumofcredits = $this->getTotalSumOfCredits($user_id, $totalcredits);

        $ref_no = $this->buy_credit_model->getReferenceNumber();
        $method = "Paypal";

        $this->buy_credit_model->insertPayment($user_id, $totalcredits, $item_number, $txn_id, $payment_gross, $total_charges, $currency_code, 1, 0, $payment_status, $date, "Donation via " . $method, $totalsumofcredits, $ref_no, 2, 1, $donated_fundraiser_name);
        if ($this->db->affected_rows() > 0) {
            $this->session->set_flashdata('icon', 'success');
            $this->session->set_flashdata('prompt_title', 'Thank you!');
            $this->session->set_flashdata('message', 'Your donation was successful!');

            $this->sendReceipt($user, $date, $ref_no, $payment_gross, $total_charges, $method, $method, $payment_status);
        } else {
            $this->session->set_flashdata('icon', 'error');
            $this->session->set_flashdata('prompt_title', 'Whoops!');
            $this->session->set_flashdata('message', 'Your donation has failed.');
        }

        $this->paymentRedirect("fundraisers");
    }

    public function fundraiser($id = '') {
        $charity = $this->db->where('id', $id)->get('charity')->row();
        $this->session->set_userdata('fundraiser_id_from_buycredit', $id);
        $this->session->set_userdata('fundraiser_name_from_buycredit', $charity->name);
        redirect( base_url('buycredits') );
    }

    private function getStripePaymentKeys() {
        $stripePaymentKey = $this->buy_credit_model->getPaymentMethodList('stripe', $this->getEnv())[0];

        $stripe = array(
            'secret_key' => $stripePaymentKey['secret_key'],
            'publishable_key' => $stripePaymentKey['publishable_key'],
        );

        return $stripe;
    }

    public function jsonStripePaymentPublishKey() {
        $stripePaymentKey = $this->getStripePaymentKeys();

        echo json_encode($stripePaymentKey['publishable_key']);
    }

    private function getTotalSumOfCredits($user_id, $totalcredits) {
        $this->db->select('sum(Credits) as credits_cr');
        $this->db->where_not_in('payment_status', array('pending', 'failed'));
        $total_credit_cr = $this->db->get_where('payments', ['status' => 1, 'User_ID' => $user_id ])->row()->credits_cr;

        $this->db->select('sum(Credits) as credits_db');
        $this->db->where_not_in('payment_status', array('pending', 'failed'));
        $total_credit_db = $this->db->get_where('payments', ['status' => 2, 'User_ID' => $user_id ])->row()->credits_db;

        $mycredits = $total_credit_cr - $total_credit_db;

        return round($totalcredits + $mycredits, 2);
    }

    private function sendReceipt($user, $date, $ref_no, $payment_gross, $total_charges, $method, $vendor, $status='') {
        $emailData = array (
            "username" => $user->username,
            "transaction_date" => $date,
            "transaction_id" => $ref_no,
            "transaction_subtotal" => $payment_gross,
            "transaction_tax" => $total_charges - $payment_gross,
            "transaction_total" => $total_charges,
            "transaction_method" => $method,
            "transaction_vendor" => $vendor,
            "transaction_status" => $status
        );

        $subject = "Your WinWinLabs receipt";
        $view = $this->load->view('emails/receipt-email', $emailData, true);

        Email::index($user->email, $subject, $view);
    }

    private function paymentRedirect($redirect) {
        if (isset($_SESSION['redirectTo']) && $_SESSION['redirectTo'] != "") {
            $rurl = $_SESSION['redirectTo'];
            $this->session->unset_userdata('redirectTo');
            redirect($rurl);
        }  else if (isset($_SESSION['fundraiser_id_from_buycredit']) && $_SESSION['fundraiser_id_from_buycredit'] != "") {
            redirect(base_url('fundraisers/show/all'));
        } else if (isset($_SESSION['fundraiser_id_from_buycredit']) && $_SESSION['fundraiser_id_from_buycredit'] != "") {
            redirect(base_url('fundraisers/show/all'));
        } else if (isset($_SESSION['game_slug_from_buycredit']) && $_SESSION['game_slug_from_buycredit'] != "") {
            redirect(base_url('games/show/play/'.$_SESSION['game_slug_from_buycredit']));
        } else  {
            redirect($redirect);
        }
    }

    public function paymentSuccess() {
        $this->session->set_flashdata('icon', 'success');
        $this->session->set_flashdata('prompt_title', 'Thank you!');
        $this->session->set_flashdata('message', 'Thank you for your Donation!');
    }

    private function paymentFailure() {
        $this->session->set_flashdata('icon', 'error');
        $this->session->set_flashdata('prompt_title', 'Whoops!');
        $this->session->set_flashdata('message', 'Your payment has failed');
    }

    public function paymentDuplicate() {
        $this->session->set_flashdata('icon', 'error');
        $this->session->set_flashdata('prompt_title', 'Whoops!');
        $this->session->set_flashdata('message', 'This card already exists, try another card.');
    }

    public function userDefaultPaymentMethod() {
		$pType = $this->input->post('pType');

		$result = $this->buy_credit_model->updateUserDefaultPaymentMethod($this->session->userdata("user_id"), $pType);
		echo $result;
	}

    public function default_card_update()
    {
        $id = $this->input->Post('rData');
        $ptype = $this->input->Post('ptype');
        $data = $this->buy_credit_model->set_default_card($id, $ptype);

        echo json_encode(array("done" => $data));
    }
}

