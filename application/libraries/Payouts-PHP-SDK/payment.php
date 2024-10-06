<?php
require __DIR__ . '/vendor/autoload.php';

use PaypalPayoutsSDK\Core\PayPalHttpClient;
use PaypalPayoutsSDK\Core\SandboxEnvironment;
use PaypalPayoutsSDK\Core\ProductionEnvironment;
use PaypalPayoutsSDK\Payouts\PayoutsPostRequest;
use PaypalPayoutsSDK\Payouts\PayoutsGetRequest;
// Creating an environment

if ($_SERVER['SERVER_NAME'] === "winwinlabs.org") {
    $clientId = "ARxNfIA9RW60S2-0Ki0UJ7_T4w58HvQEkjqnw92bfJOUJy-E2qxIixs252aslysSUn_igViy6iJ02qdH";
    $clientSecret = "EDzoE447p-iELt_02TDOsy0JtBxM7QxOaZSFbtWAklWH63S4QGRQJp34dKOyFbAsq8O7h3m61LAUwW-0";
    $environment = new ProductionEnvironment($clientId, $clientSecret);
} else {
    $clientId = "AfcuYHv-EeCv2Tsz-bGn4aiuYGl4SIIeGLn_nRIijbbo79_NQER9VELQvubceQbdCawYZyA8f_ASeZnM";
    $clientSecret = "EHKlvpeXyP9FDrm10m0PHtJsMneYbMXdwgydbToA_odGO7EeTe4n8cqPhhKyBdhQKY-oKVaagsibnXr7";
    $environment = new SandboxEnvironment($clientId, $clientSecret);
}

$client = new PayPalHttpClient($environment);
$rand = rand(11111, 99999);
$batchId = "Withdraw-" . $rand;

class Payment
{
    function payout($userEmail, $amount)
    {

        $body = json_decode(
            '{
            "sender_batch_header": {
                "sender_batch_id": "' . $GLOBALS['batchId'] . '",
                "email_subject": "You have a payout!",
                "email_message": "You have received a payout!"
            },
            "items": [
                {
                    "recipient_type": "EMAIL",
                    "receiver": "' . $userEmail . '",
                    "note": "Your $' . $amount . ' payout",
                    "sender_item_id": "Test_txn_12",
                    "amount": {
                        "currency": "USD",
                        "value": "' . $amount . '"
                    }
                }
            ]
        }',
            true
        );

        $request = new PayoutsPostRequest();
        $request->body = $body;
        $response = $GLOBALS['client']->execute($request);

        // Handle response
        // print "Status Code: {$response->statusCode}\n";
        // print "Status: {$response->result->batch_header->batch_status}\n";
        // print "Batch ID: {$response->result->batch_header->payout_batch_id}\n";
        // print "Links:\n";
        $error = [];
        // var_dump($response);
        // foreach ($response->result->links as $link) {
        //     $error[] =  "\t{$link->rel}: {$link->href}\tCall Type: {$link->method}\n";
        // }
        // if(count($error) > 0){
        //     return ['status' => 0, 'data' => $error];
        // }
        return $response;
    }

    function getBatchIdStatus($batchId){
        $request = new PayoutsGetRequest($batchId);
        $response = $GLOBALS['client']->execute($request);
        return $response;
    }
}
