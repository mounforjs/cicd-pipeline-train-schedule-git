$(document).ready(function() {

function calculateTransactionTotalWithFee(amount) {
	// Check if a form with the id "myForm" exists on the page
	var formId = document.querySelector('form').id;

	if (formId == "payform") {
		return parseFloat((amount + 0.49)/(1- 0.0349)).toFixed(2);
	} 
	
	if (formId == "ach-form" || formId == "payment-form") {
		return parseFloat((amount + 0.30)/(1- 0.029)).toFixed(2);
	}
}

$('.buycred').on('click', function () {
	$(this).addClass('active').siblings().removeClass('active');

	var value = $(this).text().replace(/[^0-9]/gi, '');
	var amount = Number(parseFloat(value).toFixed(2));
	var total_amount = calculateTransactionTotalWithFee(amount);	

	$('#total_value').val(total_amount);
	$('#total_value_ach').val(total_amount);
	$('#total_value_paypal').val(total_amount);
	$('#amount12').val(total_amount);
	$('#customAmount').val('');
});

$('#cancelPayment').on('click', function () {
	$('.credit-form , .buycred').removeClass('active');
	$('#customAmount').val('');
	$('#total_value').val('');
	$('#amount12').val('');
});


$('#customAmount').on('keyup change', function () {
	var amount = Number(parseFloat($(this).val()).toFixed(2));
	var total_amount = calculateTransactionTotalWithFee(amount);

	$('#total_value').val(parseFloat(total_amount).toFixed(2));
	$('#total_value_ach').val(parseFloat(total_amount).toFixed(2));
	$('#total_value_paypal').val(parseFloat(total_amount).toFixed(2));
	$('#amount12').val(parseFloat(total_amount).toFixed(2));
});

/* Paypal Payment Method Start*/
$('#paypal-buy').click(function () {
	event.preventDefault();
	if ($('input[name="amount-paypal"]').val() == "") {
		var errorAlert = '<div class="alert alert-danger" role="alert">Please choose credits to buy</div>';
		$('#payform').prepend(errorAlert);
		$('.alert-danger').delay(2000).fadeOut(500);
		return false;
	}

	var amountPaypal = $('#total_value_paypal').val();
	swal({
		title: 'Are you sure?',
		text: "You will be charged $" + amountPaypal + " on your PayPal account.",
		icon: 'warning',
		buttons: true,
		buttons: ["Cancel", "Yes, process donation!"],
	}).then((isConfirm) => {
		if (isConfirm) {
			$('#divLoading').addClass('show');
			setTimeout(function () {
				$('#divLoading').removeClass('show');
			}, 6000);
			document.getElementById('payform').submit();
			
			// buy credit success tracking
			window.dataLayer = window.dataLayer || [];
			window.dataLayer.push({
			'virtual_currency_name': 'Credits',
			'value': amountPaypal, //Replace this with the number of currency they earned
			'event': 'earn_virtual_currency'
			});
		}
	});

});
/* Paypal Payment Method End */


/* Credit Card Set default */
$('input.make-default-payment[type="checkbox"]').on('change', function () {
	$('input.make-default-payment[type="checkbox"]').not(this).prop('checked', false);

	
	swal({
		title: 'Default Payment',
		text: "Are you sure you want to make this default payment method?",
		icon: 'warning',
		buttons: true,
		buttons: ["Cancel", "Yes, make default!"],
	}).then((isConfirm) => {
		if (isConfirm) 
		{
		var id = $(this).val();
		var ptype = $(this).attr('id');

		$.ajax({
			url: window.location.origin + '/buycredits/default_card_update',
			type: 'POST',
			data: {
				rData: id,
				ptype: ptype
			},
			dataType: "JSON",
			beforeSend: function(){
              $('#divLoading').addClass('show');
              },
            complete: function(){
              $('#divLoading').removeClass('show');
              },
			success: function (e) {
				if (e.done == 1) {
					location.reload();
				} else {
					location.reload();
				}
			}

		});


		} else {
			$(this).prop('checked', false)
			return false;
		}
	});
});
/* Credit Card Set default */


/* Credit Card Delete */
$('.btn_delete').click(function () {

	swal({
		title: 'Delete Payment Method',
		text: "Are you sure you want to delete this payment method?",
		icon: 'warning',
		buttons: true,
		buttons: ["Cancel", "Yes, delete!"],
	}).then((isConfirm) => {
		if (isConfirm) 
		{
		var id = $(this).val();
		$.ajax({
			url: window.location.origin + '/buycredits/delete_cards',
			type: 'POST',
			data: {
				rData: id
			},
			dataType: "JSON",
			beforeSend: function(){
              $('#divLoading').addClass('show');
              },
            complete: function(){
              $('#divLoading').removeClass('show');
              },
			success: function (e) {
				if (e.done == 1) {
					location.reload();
				} else {
					location.reload();
				}
			}

		});

		} else {
			return false;
		}
	});
});
/* Credit Card Delete */


/* Credit Card Count to open credit card form */

var cardcount = $('#numCards').val();
if (cardcount != 0) {
	$('#newcrd').addClass('d-none');

	$('#new-crd').on('click', function (e) {
		$('#newcrd').removeClass('d-none');
		$("#data-target-id").toggle();
	    $("input[type=radio][name='payment-card']").prop('checked', false);
	});

} else {

	$('#new-crd').hide();
	$('#newcrd').removeClass('d-none');

}
/* Credit Card Count to open credit card form */

/* Bank Account Count to open credit card form */
	var bankcount = $('#numBanks').val();

	if (bankcount!=0){
		$('#newbnk').addClass('d-none');

    	$('#add-bank-btn').on('click', function(e){
    	$('#newbnk').removeClass('d-none');
      	$("input[type=radio][name='payment-bank']").prop('checked', false);
    	});

	}
	else{

		$('#add-bank-btn').hide();
		$('#newbnk').removeClass('d-none');

	}

/* Bank Account Count to open credit card form */


/* Stripe Credit Card Payment Method */


var stripePublishableKey;

$.getJSON(window.location.origin + '/buycredits/jsonStripePaymentPublishKey', function(data) { 
	stripePublishableKey = data;
	Stripe.setPublishableKey(stripePublishableKey);
});

$('.card-type').unbind('keyup change input paste').bind('keyup change input paste', function (e) {	
	if(Stripe.card.cardType($(this).val())){
		$('#card-type-error').text('');
	}else{
		$('#card-type-error').text('Card type is invalid.');
	}
	
});

$('#card-number').on('keypress change', function () {
	$(this).val(function (index, value) {
		return value.replace(/\W/gi, '').replace(/(.{4})/g, '$1 ');
	});
});


$('#card-number').unbind('keyup change input paste').bind('keyup change input paste', function (e) {
	var $this = $(this);
	var val = $this.val();
	var valLength = val.length;
	var maxCount = 19;
	if (valLength > maxCount) {
		$this.val($this.val().substring(0, maxCount));
	}
	if(Stripe.card.validateCardNumber($(this).val())){
		$('#card-num-error').text('');
	}else{
		$('#card-num-error').text('Your card is invalid.');
	}
	
});



$('#expDate').on('keypress change', function () {
	$(this).val(function (index, v) {
		 v=v.replace(/\D/g,""); //Remove what is not a digit
    // v=v.replace(/(\d{2})(\d)/,"$1/$2");       
    v=v.replace(/(\d{2})(\d)/,"$1/$2");       

    v=v.replace(/(\d{2})(\d{2})$/,"$1$2");
    return v;
	});
});


$('#expDate').unbind('keyup change input paste').bind('keyup change input paste', function (e) {

	date = $(this).val().split('/') ; 
	
	if(Stripe.card.validateExpiry( parseInt(date[0]), parseInt(date[1]) )){
		$('#card-date-error').text('');
	}else{
		$('#card-date-error').text('Card expiry date is invalid.');
	}
	
});

$('#cvv').unbind('keyup change input paste').bind('keyup change input paste', function (e) {
	
	if(Stripe.card.validateCVC($(this).val())){
		$('#card-cvc-error').text('');
	}else{
		$('#card-cvc-error').text('CVC code is invalid.');
	}
	
});

$('#zipcode').on('keypress change', function () {
	$(this).val(function (index, v) {
		v=v.replace(/\D/g,"");


		regex = /^\d{1,5}$/;
		console.log( regex.test(v) );
		if(regex.test(v))
		{
			$('#card-zipcode-error').text('');
		}else{
			$('#card-zipcode-error').text('Not a valid zipcode');
		}
		return v;
	});
	
});

function stripeResponseHandler(status, response) {
	if (response.error) {
		// re-enable the submit button
		$('.submit-button').removeAttr("disabled");
		// show the errors on the form
		$(".payment-errors").html(response.error.message);
	} else {
		var form$ = $("#payment-form");
		// token contains id, last4, and card type
		var token = response['id'];
		// insert the token into the form so it gets submitted to the server
		form$.append("<input type='hidden' name='stripeToken' value='" + token + "' />");
		// and submit
		form$.get(0).submit();
	}
}

	$("#payment-form").submit(function (event) {
		
		$(".payment-errors").html('');

		$('.submit-button').attr("disabled", "disabled");
		// createToken returns immediately - the supplied callback submits the form if there are no errors
		var amountoncard = $('#total_value').val();

		var isChecked = $("input[type=radio][name=payment-card]").is(':checked'); 

		if (isChecked == false) {

			if ($('input[name="card-number"]').val() == "") {
				showError($('input[name="card-number"]'), formId);
				return false;
			} else if ($('input[name="card-cvc"]').val() == "") {
				showError($('input[name="card-cvc"]'), formId);
				return false;
			} else if ($('input[name="card-expiry-month"]').val() == "") {
				showError($('input[name="card-expiry-month"]'), formId);
				return false;
			} else if ($('input[name="amountstripe"]').val() == "") {
				showError($('input[name="amountstripe"]'), formId);
				return false;
			} else {

				event.preventDefault();

				swal({
					title: 'Are you sure?',
					text: "You will be charged $" + amountoncard + " on your card.",
					icon: 'warning',
					buttons: true,
					buttons: ["Cancel", "Yes, process donation!"],
				}).then((isConfirm) => {				
					if (isConfirm) {
						$('#divLoading').addClass('show');

						var card = $('.card-expiry-year').val();
						var month = card.split('/')[0];
						var year = card.split('/')[1];

						Stripe.card.createToken({
							number: $('.card-number1').val(),
							cvc: $('.card-cvc').val(),
							exp_month: month,
							exp_year: year
						}, stripeResponseHandler);
						return false;
					}
				})
			}
		} else {

			event.preventDefault();
			showSweetAlertWithBtnAndFormSubmit("You will be charged $" + amountoncard + " on your card.", 'Are you sure?', 
			'warning', ["Cancel", "Yes, process donation!"], 'payment-form');

		}
	});
/* Stripe Credit Card Payment Method */


/* Stripe bank ACH Payment Method */

$('#account_number').unbind('keyup change input paste').bind('keyup change input paste', function (e) {
	
	if(Stripe.bankAccount.validateAccountNumber($(this).val(),'US')){
		$('#bank-account-error').text('');
	}else{
		$('#bank-account-error').text('Account number is invalid.');
	}
	
});

$('#routing_number').unbind('keyup change input paste').bind('keyup change input paste', function (e) {
	
	if(Stripe.bankAccount.validateRoutingNumber($(this).val(),'US')){
		$('#bank-routing-error').text('');
	}else{
		$('#bank-routing-error').text('Routing number is invalid.');
	}
	
});



$("#ach-form").submit(function (event) {
	let formId = '#' + this.id;
	$(".payment-errors").html('');
	$('.submit-button').attr("disabled", "disabled");
	// createToken returns immediately - the supplied callback submits the form if there are no errors
	var isChecked = $("input[type=radio][name=payment-bank]").is(':checked'); 
	if ($('input[name="account_number"]').is(':visible')) {
		if ($('input[name="account_number"]').val() == "") {
			showError($('input[name="account_number"]'), formId);
			return false;
		}
		else if ($('input[name="routing_number"]').val() == "") {
			showError($('input[name="routing_number"]'), formId);
			return false;
		}
	
		event.preventDefault();
	
		swal({
			title: 'Are you sure?',
			text: "This bank account will be saved for future payments.",
			icon: 'warning',
			buttons: true,
			buttons: ["Cancel", "Confirm"],
		}).then((isConfirm) => {				
			if (isConfirm) {
				$('#divLoading').addClass('show');

				routing_no = $('#routing_number').val();
				account_no = $('#account_number').val();
				account_hold_name = "winwin-user"; 

				var stripe = Stripe(stripePublishableKey);
				
				stripe.createToken('bank_account', {
					country: 'US',
					currency: 'usd',
					routing_number: routing_no,
					account_number: account_no,
					account_holder_name: account_hold_name,
					account_holder_type: 'individual',
				}).then(function (result) {
					var form$ = $("#ach-form");
					form$.append("<input type='hidden' name='token_id' value='" + result.token.id + "' />");
					
					form$.get(0).submit();
					form$[0].reset();
					
					// Handle result.error or result.token
				});

			}
		});
	}
	else {
		if (isChecked == false && !$('input[name="account_number"]').is(':visible')) {
			var errorAlert = '<div class="alert alert-danger" role="alert">Please choose a payment option or click on "Add New Bank</div>';
			$('#ach-form').prepend(errorAlert);
			$('.alert-danger').delay(2000).fadeOut(500);
			return false;
		}

		var verification_code = $('.status-code').val();

		if (verification_code == 0) {
			 showSweetAlert('Your bank account must be verified in order to create an ACH payment','','warning');
			 return false;
		} else {
			event.preventDefault();
			var amountoncard = $('#total_value_ach').val();
			if (amountoncard == "") {
				var errorAlert = '<div class="alert alert-danger" role="alert">Please choose credits to buy</div>';
				$('#ach-form').prepend(errorAlert);
				$('.alert-danger').delay(2000).fadeOut(500);
				return false;
			}
			swal({
				title: 'Are you sure?',
				text: "You will be charged $" + amountoncard + " on your bank account.",
				icon: 'warning',
				buttons: true,
				buttons: ["Cancel", "Yes, process donation!"],
				
			}).then((isConfirm) => {				
				if (isConfirm) {
					var form$ = $("#ach-form");
					$('#divLoading').addClass('show');
					form$.get(0).submit();
					form$[0].reset();
				}
			})
		}
	}
});
/* Stripe bank ACH Payment Method */

function showError(fieldName, formName) {
	var msg;
	var fieldName;
	var formName;

	switch (fieldName.attr('name')) {
		case 'account_number':
		  msg = "Please fill in account number";
		  break;
		case 'routing_number':
		  msg = "Please fill the routing number";
		  break;
		case 'card-number':
		  msg = "Please fill the card number";
		  break;
		case 'card-cvc':
		  msg = "Please fill the cvc";
		  break;
		case 'card-expiry-month':
		  msg = "Please fill the card expiry month";
		  break;
		case 'amountstripe':
		  msg = "Please choose credits to buy";
	  }
	 
	var errorAlert = '<div class="alert alert-danger" role="alert">' + msg + '</div>';
	$(formName).prepend(errorAlert);
	$('.alert-danger').delay(2000).fadeOut(500);
	$('.submit-button').removeAttr("disabled");
}

/* Bank verification with micro deposits */
$('.btn-verify').click(function () {

	var bank_id = $('.verify').val();

	var id = $('.verify').attr('id');

	var amount1 = $('#amount1').val();

	var amount2 = $('#amount2').val();

	if (amount1 == '' || amount2 == "") {
		var errorAlert = '<div class="alert alert-danger" role="alert">Please fill in both the micro deposit amounts</div>';
		$('#ach-form').prepend(errorAlert);
		$('.alert-danger').delay(2000).fadeOut(500);
		return false;
	} else {

		$.ajax({
			url: window.location.origin + '/buycredits/stripe_bank_verification',
			type: 'POST',
			data: {
				bank: bank_id,
				id: id,
				amtone: amount1,
				amtwo: amount2
			},
			dataType: "JSON",
			beforeSend: function() {
				$('#divLoading').addClass('show');
			},
			success: function (e) { 
				if (e.done == 1) {
					$('#divLoading').removeClass('show');
					showSweetAlert('Your bank account has been verified successfully. You can now make donations with it.','','success');
					window.setTimeout(function(){location.reload()},3000);	
				} else {
					showSweetAlert('Please try again.','','warning');
				}
			}

		});

	}


});
/* Bank verification with micro deposits */

/* Set Default Payment Method */
$('input[type=radio][name=defaultPaymentMethodRadio]').change(function() {
	var pMethodVal = $(this).attr('id');

	$.ajax({
		type: "POST",
		url: window.origin + '/buycredits/userDefaultPaymentMethod',
		data:{pType: pMethodVal},
		cache: false, 
		success: function(data){  
			if(data == '1'){
				showSweetAlert('Your default payment method has been updated','Great');
				window.location.href = window.origin + '/buycredits/' + pMethodVal.replace('Radio','');
			}
			else {
				showSweetAlert('Your default payment method could not be updated','error','Oops!');
			}
		}
	});
});
/* Set Default Payment Method */
});
