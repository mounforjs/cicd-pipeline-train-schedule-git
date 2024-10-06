<?php


public function paymoney(){

		
		
$mycredits=$this->transaction_model->get_sum();



$ppappid="APP-80W284485P519543T";
$ppuserid="deepersnow-facilitator_api1.gmail.com";
$pppass="LMRH7GLWUPVXTW6R";
$ppsig="AFcWxV21C7fd0v3bYYYRCpSSRl31AHOauf9UuRYZ0khwPhiD0fs5J8j";

// $amountval=1/100*$this->input->post('withdraw');
$amount=$this->input->post('amountWithdrawn');
$mail=$this->input->post('email');
$fname=$this->input->post('fname');
$lname=$this->input->post('lname');


$url = trim("https://svcs.sandbox.paypal.com/AdaptiveAccounts/GetVerifiedStatus");  

$bodyparams = array (   "requestEnvelope.errorLanguage" => "en_US",
    "emailAddress" =>$mail,
    "firstName" =>$fname,
    "lastName" =>$lname,
    "matchCriteria" => "NAME"
);


$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER,  array(
    'X-PAYPAL-REQUEST-DATA-FORMAT: NV',
    'X-PAYPAL-RESPONSE-DATA-FORMAT: NV',
    'X-PAYPAL-SECURITY-USERID: '. $ppuserid,
    'X-PAYPAL-SECURITY-PASSWORD: '. $pppass,
    'X-PAYPAL-SECURITY-SIGNATURE: '. $ppsig,
    'X-PAYPAL-APPLICATION-ID: '. $ppappid
));  
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($bodyparams));//
$response = curl_exec($ch);
$response = json_decode($response, 1);



//print_r($response);
//exit;
if($response['responseEnvelope']["ack"]<>"Failure")
{

if(($mycredits>=$amount) and $amount>0)
{
$payLoad=array();

$transaction=time().uniqid();
$receiverList=array();
$counter=0;
$ppapicall="sandbox";
$receiverList["receiver"][$counter]["amount"]=$amount;
$receiverList["receiver"][$counter]["email"]=$mail;
$receiverList["receiver"][$counter]["paymentType"]="PERSONAL";
$receiverList["receiver"][$counter]["invoiceId"]=$transaction;
$payLoad["actionType"]="PAY";
$payLoad["cancelUrl"]=asset_url('account/transactions');
$payLoad["returnUrl"]=asset_url('account/transactions');
$payLoad["currencyCode"]="USD";
$payLoad["receiverList"]=$receiverList;

//$payLoad["fundingConstraint"]=array("allowedFundingType"=>array("fundingTypeInfo"=>array("fundingType"=>"BALANCE")));
$payLoad["sender"]["email"]="deepersnow-facilitator@gmail.com";


$API_Endpoint = "https://svcs.$ppapicall.paypal.com/AdaptivePayments/Pay";
$payLoad["requestEnvelope"]=array("errorLanguage"=>urlencode("en_US"),"detailLevel"=>urlencode("ReturnAll"));
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER,  array(
    'X-PAYPAL-REQUEST-DATA-FORMAT: JSON',
    'X-PAYPAL-RESPONSE-DATA-FORMAT: JSON',
    'X-PAYPAL-SECURITY-USERID: '. $ppuserid,
    'X-PAYPAL-SECURITY-PASSWORD: '. $pppass,
    'X-PAYPAL-SECURITY-SIGNATURE: '. $ppsig,
    'X-PAYPAL-APPLICATION-ID: '. $ppappid
));  
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payLoad));//
$response = curl_exec($ch);
$response = json_decode($response, 1);
// print_r($response);
// exit;
$payKey = $response["payKey"];
$paymentExecStatus=$response["paymentExecStatus"];
$correlationId=$response["responseEnvelope"]["correlationId"];
$paymentInfoList = isset($response["paymentInfoList"]) ? $response["paymentInfoList"] : null;

if ($paymentExecStatus<>"ERROR") {

if(isset($paymentInfoList["paymentInfo"]))
{
foreach($paymentInfoList["paymentInfo"] as $paymentInfo) 
{//they will only be in this array if they had a paypal account
$receiverEmail = $paymentInfo["receiver"]["email"];
$receiverAmount = $paymentInfo["receiver"]["amount"];
$withdrawalID = $paymentInfo["receiver"]["invoiceId"];
$transactionId = $paymentInfo["transactionId"];
$senderTransactionId = $paymentInfo["senderTransactionId"];
$senderTransactionStatus = $paymentInfo["senderTransactionStatus"];
$pendingReason = isset($paymentInfo["pendingReason"]) ? $paymentInfo["pendingReason"] : null;
}
}

$status="Credits withdrawn to Paypal Account ".$mail;
if($transactionId=="")
{
$transactionId=$transaction;
}
$date = date('Y-m-d H:i:s');
$totalcredits=$mycredits-$amount;
$ref_no = $this->db->select('ref_num')->where('ref_num IS NOT NULL')->order_by('payment_id','desc')->get('payments')->row();
if(isset($ref_no->ref_num) and $ref_no->ref_num > 0){
    $ref_no = $ref_no->ref_num + 1;
}else{
    $ref_no = 10001;
}

$data = array(
'Credits' => $amount,
'User_ID' => $this->session->userdata('user_id'),
'Date' => $date,
'Notes' => $status,
'Status' => '2',
'game_id' => '',
'item_number' => '',
'txn_id' => $transactionId,
'ref_num' => $ref_no,
'payment_gross' => $amount,
'currency_code' => 'usd',
'payment_status' => 'Completed',
'total_credits' =>$totalcredits,
);
$insert = $this->transaction_model->insert_in_db($data);

echo "2";



}else{
$paymentInfoList = isset($response["payErrorList"]) ? $response["payErrorList"] : null;
foreach($paymentInfoList["payError"] as $paymentInfo) {
echo  $error="1 : Paypal Error - ".$paymentInfo["error"]["message"];

}
}
}
else
{
echo "1";
}
}
else
{
foreach($response["error"] as $paymentInfo) 
{
$error= $paymentInfo['message'];
}
echo "1 : Paypal Error - ".$error;
}


}

  




