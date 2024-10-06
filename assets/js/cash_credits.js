searchVisible = 0;
transparent = true;


$(document).ready(function () {
   var formid = $('form').attr('id');
   // Code for the Validator
   if (formid == 'stripe-cashout-form') {
      var maxCashVal = $('#total_credits').val();
      var $validator = $('.wizard-card form').validate({
         onkeyup: function (element, event) {
            if (event.which === 9 && this.elementValue(element) === "") {
               return;
            } else {
               this.element(element);
            }
         },
         rules: {
            stripeEmail: {
               required: true,
               minlength: 3,
               email: true,
            },
            stripeCashoutAmount: {
               required: true,
               number: true,
               max: function () {
                  return parseInt(maxCashVal);
               },
               min: 5
            },

         },
         submitHandler: function (form) {
            var stripeCash = $("input[name='stripeCashoutAmount']").val();

            $(document).ajaxStart(function () {
               $("div#divLoading").addClass('show');
            });

            $.ajax({
               type: "POST",
               url: window.location.origin + '/cashout/stripe_cashout',
               data: {
                  amountWithdrawn: stripeCash
               },
               success: function (data) {
                  document.getElementById("stripe-cashout-form").reset();
                  data = $.parseJSON(data);

                  if (data.status == true) {
                     let successMsg = '<p>You have successfully withdrawn the credits!</p>';
                     var elem = document.createElement("div");
                     elem.innerHTML = successMsg;

                     showSweetUserConfirm(elem, 'Awesome', $icon = 'success', function (confirmed) {
                        if (confirmed) {
                           window.location.replace('/transactions');
                        }
                     });
                  } else if (data.status == false) {
                     $("#divLoading").hide();
                     showSweetAlert('Sorry! The transaction could not be processed.', 'Oops', 'error');
                     return false;
                  }
               }
            })
         }

      });
   } else if (formid == 'paypal-cashout-form') {
      var maxCashVal = $('#withdrawable_credits').val();
      var $validator = $('.wizard-card form').validate({
         onkeyup: function (element, event) {
            if (event.which === 9 && this.elementValue(element) === "") {
               return;
            } else {
               this.element(element);
            }
         },
         rules: {
            paypalCashoutAmount: {
               required: true,
               number: true,
               max: function () {
                  return parseInt(maxCashVal);
               },
               min: 5
            },
            paypalEmail: {
               required: true,
               minlength: 3,
               email: true,
            },

         },

         submitHandler: function (form) {
            var email = $("input[name='paypalEmail']").val();
            var paypalCash = $("input[name='paypalCashoutAmount']").val();

            $(document).ajaxStart(function () {
               $("div#divLoading").addClass('show');

            });

            $.ajax({
               type: "POST",
               url: window.location.origin + '/cashout/paypal_cashout',
               data: {
                  email: email,
                  amountWithdrawn: paypalCash
               },
               success: function (data) {
                  data = $.parseJSON(data)
                  if (data.status == true) {
                     $("#divLoading").hide();

                     $.ajax({
                        type: "POST",
                        url: window.location.origin + '/cashout/getBatchIdStatus',
                        data: {
                           batchId: data.id,
                        },
                        success: function (data) {
                           data = $.parseJSON(data);

                           if (data.status == true) {
                              let successMsg = '<p>You have successfully withdrawn the credits!</p>';

                              if (data.errorMsg) {
                                 let errorMsg = '<p class="paypalCashoutErrorMsg">' + data.errorMsg + '</p>';
                                 successMsg = successMsg + errorMsg;
                              }

                              var elem = document.createElement("div");
                              elem.innerHTML = successMsg;

                              showSweetUserConfirm(elem, 'Awesome', $icon = 'success', function (confirmed) {
                                 if (confirmed) {
                                    window.location.replace('/transactions');
                                 }
                              });
                           }
                        }
                     });
                  } else {
                     $("#divLoading").hide();
                     showSweetAlert('Sorry! The transaction could not be processed.', 'Oops', 'error');

                     document.getElementById("paypal-cashout-form").reset();
                     return false;
                  }

                  document.getElementById("paypal-cashout-form").reset();
               }
            });

         }

      });
   }

});