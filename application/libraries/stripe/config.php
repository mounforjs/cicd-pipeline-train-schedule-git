<?php
  require_once('../stripe/lib/Stripe.php');
  $stripe = array(
    'secret_key'      => 'sk_test_xe9qDpDFjndxkcAzP8FFPfcH ',
    'publishable_key' => 'pk_test_ZBv96Mb5oCF9qzcTxWejn6lV '
    );
  Stripe::setApiKey($stripe['secret_key']);
?>