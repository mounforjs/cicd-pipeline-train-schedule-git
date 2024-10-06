<content class="content">
    <section>
        <div class="container">
            <div class="row paymentbar p-1 mt-3">
                <div class="col-lg-3 align-self-center">
                    <h3 class="text-center">Method of Donation</h3>
                </div>
                <div class="col-lg-9">
                    <!-- payment tabs -->
                    <ul id="buycredTab" class="nav nav-tabs justify-content-center nav-pills" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link <?php if ($show_form == 'paypal') echo 'active';?>" id="ppal-tab" href="<?php echo asset_url();?>buycredits/paypal" role="tab" aria-controls="ppal"
                                aria-selected="false"><img src="https://dg7ltaqbp10ai.cloudfront.net/paypal.png" alt="Paypal"></a>
                            <div class="form-check ml-4">
                                <input class="form-check-input defaultPaymentMethodRadio" type="radio" name="defaultPaymentMethodRadio" id="paypalRadio"
                                    <?php if (getprofile()->default_payment_method == '3') { echo 'checked';}?>>
                                <label class="form-check-label pmethod">
                                    Set as Default Donation Method
                                </label>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <?php if ($show_form == 'credit' || $show_form == 'paypal' || ($show_form == 'bank' && $verifiedBankCount > 0)){ ?>
                    <br>
                    <h2 class="text-center">Select an Amount</h2>
                    <div class="row justify-content-center">
                        <div class="col-auto mb-3 text-center">
                            <button class="btn white buycred">$5</button>
                            <button class="btn white buycred">$10</button>
                            <button class="btn white buycred">$20</button>
                            <button class="btn white buycred">$50</button>
                            <button class="btn white buycred">$100</button>
                        </div>
                        <div class="col-auto text-center">
                            <h1 class="custom-amount-field">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">$</span>
                                    </div>
                                    <input type="number" id="customAmount" class="form-control" placeholder="Custom Amount" min="5">
                                </div>
                            </h1>
                        </div>
                    </div>
                    <?php }?>
                    <div class="tab-content" id="myTabContent">
                        <?php if ($show_form == 'bank') { ?>
                        <div  id="bank" role="tabpanel" aria-labelledby="bank-tab">
                            <!-- BANK CONTENT -->
                            <form role="form" id="ach-form" class="ach-form" method="POST" action="<?php echo asset_url('buycredits/achStripe') ?>" novalidate>
                                <input type='hidden' value= '<?php echo count($savedbanklist); ?>' id='numBanks'>
                                <?php foreach ($savedbanklist as $key => $value) {?>
                                <h2>
                                    <button class="banklabel" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    <input class="form-check-input" type="radio" name="payment-bank" id="payment-bank-<?php echo $value['payment_method_id']; ?>" value="<?php echo $value['payment_method_id']; ?>" <?php if ((count($savedbanklist) == 1) ||isset($value['default_payment_method']) && $value['default_payment_method'] == 1) {echo 'checked="checked"';} else {echo '';}?> >
                                    <label class="form-check-label" for="exampleRadios1">
                                    <?php echo (($value['payment_type'] == 'bank') ? $value['bank_brand'] : $value['card_brand']) . " ending in " . (($value['payment_type'] == 'bank') ? $value['bank_last_four'] : $value['card_last_four']); ?>
                                    </label>
                                    </button>
                                    <?php if ($value['default_payment_method'] == 0 and $value['payment_type'] = 'bank') {?>
                                    <input class="form-checkbox small-box make-default-payment" type="checkbox" id="type-bank" value="<?php echo $value['id']; ?>" name="save-card-yes">
                                    <input type="hidden" value="<?php echo $value['payment_type']; ?>">
                                    <label class="make-default-payment" for="inlineCheckbox1">Set as default bank account.</label>
                                    <?php } else {?>
                                    <label class="make-default-payment" for="inlineCheckbox1">Default bank account.</label>
                                    <?php }?>
                                    <button class="btn btn-link btn-danger btn_delete" type="button"  value="<?php echo $value['id']; ?>">Delete</button>
                                </h2>
                                <?php if ($value['bank_verification_status'] == 0) { ?>
                                <input type="hidden" value="<?php echo $value['payment_method_id']; ?>" id="<?php echo $value['id']; ?>" class="verify">
                                <input type="hidden" value="<?php echo $value['bank_verification_status']; ?>" class="status-code">
                                <label class="form-check-label text-danger"><i class="fa fa-exclamation-triangle"></i> This bank account is unverified</label><br><br>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <h4>Enter the 2 amounts we deposited into your bank account.</h4>
                                        <span style="margin-top:-10px; margin-bottom:10px">Don't see them yet? The deposits may take 1-2 business days to show on your bank statement.</span>
                                    </div>
                                    <div class="col-sm-2">
                                        <label for="amount#1"><strong>Amount #1</strong></label><br>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">$ 0<b>.</b></div>
                                            </div>
                                            <input type="text" class="form-control" id="amount1">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <label for="amount#2"><strong>Amount #2</strong></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text">$ 0<b>.</b></div>
                                            </div>
                                            <input type="text" class="form-control" id="amount2">
                                        </div>
                                    </div>
                                    <div class="col-sm-2"><br>
                                        <button type="button" class="btn small btn-verify">Verify</button>
                                    </div>
                                </div>
                                <hr>
                                <?php } }?>
                                <button class="btn btn-sm gray" type="button" id="add-bank-btn" > Add New Bank Account</button><br>
                                *Please add your account. New account will take 3-7 business days to validate before donation can be made.
                                <span class="payment-errors"></span><br><br>
                                <div class="tab-pan" id="newbnk" role="tabpanel" aria-labelledby="newbnk-tab">
                                    <div class="row">
                                        <div class="col-md-4"><label for="expDate"><strong>Account Number</strong></label><br>
                                            <input type="text" class="form-control" id="account_number" placeholder="ENTER ACCOUNT NUMBER" name="account_number" required >
                                            <span class="text-danger" id="bank-account-error"></span>
                                        </div>
                                        <div class="col-md-4"><label for="cvCode"><strong>Routing Number</strong></label><br>
                                            <input type="text" class="form-control" id="routing_number" placeholder="ENTER ROUTING NUMBER" name="routing_number" required >
                                            <span class="text-danger" id="bank-routing-error"></span>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <?php if(count($savedbanklist) == 1) { ?>
                                            <input class="form-checkbox small-box" type="checkbox" id="save-bank-yes" value="yes" name="save-bank-yes">
                                            <label for="inlineCheckbox1" >Set as default bank account.</label>
                                        <?php } else { ?>
                                            <input class="form-checkbox small-box" type="hidden" id="save-bank-yes" value="yes" name="save-bank-yes">
                                            <label class="form-check-label" for="inlineCheckbox1">
                                                <small class="font-weight-bold text-success">**This will be set as your default bank account.</small>
                                            </label>
                                        <?php } ?>
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        <button type="submit" class="btn red" id="stripepost" ><i class="fa fa-credit-card" aria-hidden="true"></i>Add Account</button>
                                    </div>
                                </div>
                                <br><br>
                                <?php if ($show_form == 'credit' || $show_form == 'paypal' || ($show_form == 'bank' && $verifiedBankCount > 0)){ ?>
                                <div class="row">
                                    <div class="col-lg-12 text-center">
                                        <label for="total_value"><strong>Total USD (select value from above)</strong></label><br>
                                        <input type="number" class="form-control total-usd" id="total_value_ach" name="amountstripe_ach" readonly="">
                                        <sub>Total donation includes a Stripe fee</sub><br><br>
                                        <button type="submit" class="btn" id="stripepost" ><i class="fa fa-credit-card" aria-hidden="true"></i> Donate Now</button>
                                        <!-- <a class="btn btn-buy" id="stripepost_ach" onclick="ach_pay();" >Donate Now </a> -->
                                        <button type="reset" class="btn white" id="cancelPayment">Cancel</button>
                                    </div>
                                </div>
                                <?php }?>
                            </form>
                            <!-- END BANK CONTENT -->
                        </div>
                        <?php } elseif ($show_form == 'credit') { ?>
                        <div  id="credit" role="tabpanel" aria-labelledby="credit-tab">
                            <!-- CREDIT CARD CONTENT -->
                            <br><br>
                            <form role="form" id="payment-form" novalidate  class="credit-form" method="POST" action="<?php echo asset_url('buycredits/stripe') ?>">
                                <input type='hidden' value= '<?php echo count($savedcardlist); ?>' id='numCards'>
                                <?php foreach ($savedcardlist as $key => $value) {?>
                                <h2>
                                    <button type="button" class="banklabel" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    <input class="form-check-input" type="radio" name="payment-card" id="payment-card" value="<?php echo $value['payment_method_id']; ?>" <?php if ((count($savedcardlist) == 1) || isset($value['default_payment_method']) && $value['default_payment_method'] == 1) {echo 'checked="checked"';} else {echo '';}?>  >
                                    <label class="form-check-label" for="exampleRadios1">
                                    <?php echo $value['card_brand'] . " ending in " . $value['card_last_four']; ?>
                                    </label>
                                    </button>
                                    <?php if ($value['default_payment_method'] == 0) {?>
                                    <input class="form-checkbox small-box make-default-payment" type="checkbox" id="card" value="<?php echo $value['id']; ?>" name="save-card-yes">
                                    <input class="form-check-input" type="hidden" value="<?php echo $value['payment_type']; ?>" id="type-card">
                                    <label class="form-check-label make-default-payment text-black-50" for="inlineCheckbox1">Set as default credit/debit card.</label>
                                    <?php } else {?>
                                    <label class="form-check-label text-black-50 make-default-payment" for="inlineCheckbox1">Default credit/debit card.</label>
                                    <?php }?>
                                    <button class="btn btn_delete" type="button" value="<?php echo $value['id']; ?>">Delete</button>
                                </h2>
                                <?php }?>
                                <br>
                                <button class="btn nav-link mb-2" type="button" id="new-crd" data-toggle="tab" href="#newcrd" role="tab" aria-controls="newcrd"> Add New Card</button>
                                <span class="payment-errors"></span>
                                <div class="tab-pan" id="newcrd" role="tabpanel" aria-labelledby="newcrd-tab">
                                    <div class="row justify-content-center">
                                        <div class="col-sm-3">
                                            <label for="card-number"><i class="fa fa-credit-card"></i> <strong>Card Number</strong></label>
                                            <input type="creditcard" class="form-control card-number1" id="card-number" name="card-number" placeholder="Valid Card Number" required value="">
                                            <span class="text-danger" id="card-num-error"></span>
                                        </div>

                                        <div class="col-sm-2">
                                            <label for="expDate"><strong>Expiration Date</strong></label>
                                            <input type="text" class="form-control card-expiry-year" id="expDate" name="card-expiry-month" placeholder="MM/YYYY" required value="">
                                            <span class="text-danger" id="card-date-error"></span>
                                        </div>

                                        <div class="col-sm-2">
                                            <label for="cvCode"><strong>CVC Code</strong></label>
                                            <input type="number" class="form-control card-cvc" id="cvv" min="1" minlength="3" maxlength="4"  name="card-cvc" placeholder="CVC" required value="">
                                            <span class="text-danger" id="card-cvc-error"></span>
                                        </div>

                                        <div class="col-sm-3">
                                            <label for="zipcode"><strong>Billing Zip</strong></label>
                                            <input type="text" style="width: 50%" class="form-control" id="zipcode" min="1" minlength="5" maxlength="5"  name="card-zip" placeholder="Zip" required value="">
                                            <span class="text-danger" id="card-zipcode-error"></span>
                                        </div>

                                        <div class="mt-2">
                                            <?php if(count($savedcardlist) == 1) { ?>
                                                <input class="form-checkbox small-box" type="checkbox" id="save-card-yes" value="yes" name="save-card-yes">
                                                <label class="form-check-label" for="inlineCheckbox1">Set as default credit/debit card.</label>
                                            <?php } else { ?>
                                                <input class="form-checkbox small-box" type="hidden" id="save-card-yes" value="yes" name="save-card-yes">
                                                <label class="form-check-label" for="inlineCheckbox1">
                                                    <small class="font-weight-bold text-success">**This will be set as your default credit/debit card.</small>
                                                </label>
                                            <?php } ?>        
                                        </div>
                                    </div>
                                    <br>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12 text-center"><label for="total_value"><strong>Total USD (select value from above)</strong></label><br>
                                        <input type="number" class="form-control total-usd" id="total_value" name="amountstripe" readonly="">
                                        <sub>Total donation includes a Stripe fee</sub><br>
										<?php $this->load->view('home/donationterms_modal.php'); ?>
										<br>
                                        <button type="submit" class="btn red" id="stripepost" ><i class="fa fa-credit-card" aria-hidden="true"></i> Donate Now</button>
                                        <button type="reset" class="btn gray" id="cancelPayment">Cancel</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- END CARD CONTENT -->
                    </div>
                    <?php } elseif ($show_form == 'paypal') { ?>
                    <div id="ppal" role="tabpanel" aria-labelledby="ppal-tab">
                        <!-- PAYPAL CONTENT -->
                        <br>
                        <br>
                        <div class="row">
                            <div class="col-lg-12 text-center">
                                <form action="<?php echo $paypalURL; ?>" id="payform" name="payform" method="post" role="form">
                                    <div class="form-group">
                                        <label for="total_value"><strong>Total USD (select value from above)</strong></label><br>
                                        <input type="number" class="form-control total-usd" id="total_value_paypal" name="amount-paypal" readonly>
                                        <sub>Total donation includes a PayPal fee</sub>
                                    </div>
                                    <input type="hidden" name="business" value="<?php echo $paypalID; ?>">
                                    <!-- Specify a Buy Now button. -->
                                    <input type="hidden" name="cmd" value="_xclick">
                                    <!-- Specify details about the item that buyers will purchase. -->
                                    <input type="hidden" name="item_name" value="<?php echo 'Thank you for the donation!'; ?>">
                                    <input type="hidden" name="item_number" value="">
                                    <input type="hidden" name="amount" id="amount12" value="">
                                    <input type="hidden" name="currency_code" value="USD">
                                    <input type='hidden' name='cancel_return' value='<?php echo asset_url('buycredits'); ?>'>
                                    <input type='hidden' name='return' value='<?php echo asset_url('buycredits/success'); ?>'>
                                    <div class="row">
                                        <div class="col-lg-12">
											<?php $this->load->view('home/donationterms_modal.php'); ?><br>
											<a class="btn mr-3" id="paypal-buy" ><i class="fa fa-credit-card" aria-hidden="true"></i> Donate Now</a>
                                            <button type="reset" class="btn gray" id="cancelPayment">Cancel</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- END PAYPAL CONTENT -->
                    </div>
                    <?php }?>
					<br><br>
                </div>
                <!-- payment tabs -->
            </div>
        </div>
        </div>
    </section>
    <div id="divLoading"> </div>
</content>

<script>
    $(document).ready(function() {
        var cardMsg = getCookie("bStatus");

        if (cardMsg == '1') {
            showSweetUserConfirm("", "This card already exists, please try a different one!", "Whoops!", "error");
        }

        if (cardMsg == '2') {
            showSweetUserConfirm("", "New card could not be added! Please try another card", "Whoops!", "error");
        }

        if (cardMsg == '3') {
            showSweetUserConfirm("", "Purchase method failed and transaction did not occur!", "Whoops!", "error");
        }

        if (cardMsg == '4') {
            showSweetUserConfirm("", "You have been successfully added to Stripe!", "Whoops!", "error");
        }
    })
</script>