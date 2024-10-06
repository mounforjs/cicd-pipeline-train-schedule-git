/*!

 =========================================================
 * Bootstrap Wizard - v1.1.1
 =========================================================
 
 * Product Page: https://www.creative-tim.com/product/bootstrap-wizard
 * Copyright 2017 Creative Tim (http://www.creative-tim.com)
 * Licensed under MIT (https://github.com/creativetimofficial/bootstrap-wizard/blob/master/LICENSE.md)
 
 =========================================================
 
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 */

// Get Shit Done Kit Bootstrap Wizard Functions

searchVisible = 0;
transparent = true;


$(document).ready(function(){
    var formid = $('form').attr('id');

    /*  Activate the tooltips      */
    $('[rel="tooltip"]').tooltip();

    // Code for the Validator
    if (formid == 'logIn-form') {
        var $validator = $('.wizard-card form').validate({
            onkeyup: function(element, event) {
                if (event.which === 9 && this.elementValue(element) === "") {
                    return;
                } else {
                    this.element(element);
                }
            },
            rules: {
                email: {
                    required: true,
                    minlength: 3,
                },
                password: {
                    required: true,
                    minlength: 3,
                }
                    
            },
            submitHandler: function(form) {
                var password = document.getElementById("login-password").value;
                var email = document.getElementById("login-email").value;

                $.ajax({
                    type: "POST",
                    url: 'user/login_check',
                    data: {
                        email: email,
                        password: password
                    },
                    beforeSend: function() {
                        $('#logIn-form').find('input[type=submit]').attr('disabled', true);
                        $('#divLoading').addClass('show');
                    },
                    complete: function() {
                        $('#divLoading').removeClass('show');
                    },
                    success: function(data) {
                        data = JSON.parse(data);

                        if (data.status == "success") {
                            document.getElementById('logIn-form').submit();
                            document.getElementById("login-password").value = "";
                            document.getElementById("login-email").value = "";
                            localStorage.clear();
                            
                            //login tracking
                            window.dataLayer = window.dataLayer || [];
                            window.dataLayer.push({
                                'event' : 'login',
                                'method' : 'Email', //this can be any method, but WinWin only has email
                            });
                        } else {
                            $('#logIn-form').find('input[type=submit]').attr('disabled', false);

                            $('#error-msg').text(data.msg);
                            $("#error-msg").show().delay(3000).fadeOut(2000);
                        }
                    }
                })
            }
        });
    } else if(formid == 'signUp-form'){
         var $validator = $('.wizard-card form').validate({
            onkeyup: function(element, event) {
                if (event.which === 9 && this.elementValue(element) === "") {
                    return;
                } else {
                    this.element(element);
                }
            },
            rules: {
                firstname: {
                    required: true,
                    minlength: 3
                },
                lastname: {
                    required: true,
                    minlength: 3
                },
                email: {
                    required: true,
                    minlength: 3,
                    email: true,
                },
                password: {
                    required: true,
                },
                username: {
                    required: true,
                    minlength: 3,
                    remote: {
                        url: window.location.origin + "/user/check_username",
                        type: "POST",
                    }
                },
                unEmail: {
                    required: true,
                    minlength: 3,
                    email: true
                },
            },
            submitHandler: function(form) {
                var first_name = $("input[name='firstname']").val();
                var last_name = $("input[name='lastname']").val();
                var user_name = $("input[name='username']").val();
                var country_name = $("#user_country").val();
                var email = $("input[name='unEmail']").val();
                var password = $("input[name='password']").val();

                $.ajax({
                    type: "POST",
                    url: 'user/register_insert_check',
                    data: {
                        firstname: first_name,
                        lastname: last_name,
                        unEmail: email,
                        password: password,
                        country: country_name,
                        username: user_name
                    },
                    beforeSend: function() {
                        $('#signUp-form').find('input[type=submit]').attr('disabled', true);
                        $('#divLoading').addClass('show');
                    },
                    complete: function() {
                        $('#divLoading').removeClass('show');
                    },
                    success: function(data) {
                        data = JSON.parse(data);

                        if (data.status == 'success') {
                            if(data.isReferred == true && data.referralRedeemed == true) {
                                setCookie('s', data.status);
                                setCookie('rval', data.referralValue);
                            }

                            if(data.isReferred == false) {
                                setCookie('s', data.status);
                                setCookie('rval', '');
                            }
                            // registration tracking
                            window.dataLayer = window.dataLayer || [];
                            window.dataLayer.push({
                                'event' : 'signup',
                                'method' : 'Email', //this can be any method, but WinWinLabs only has email
                            });

                            if (data.redirect) {
                                window.location.replace(window.location.origin + data.redirect);
                            } else {
                                window.location.replace(window.location.origin);
                            }
                            
                        } else {
                            showSweetAlert(data.msg,'Whoops!', "error");

                            window.setTimeout(function () {
                                $('#signUp-form').find('input[type=submit]').attr('disabled', false);
                            }, 2000); 
                        }
                    }
                });
            }
        });
    } else if(formid=='resPass-form'){

        var $validator = $('.wizard-card form').validate({
            onkeyup: function(element, event) {
                if (event.which === 9 && this.elementValue(element) === "") {
                    return;
                } else {
                    this.element(element);
                }
            },
            rules: {
                resetEmail: {
                    required: true,
                    minlength: 3,
                    email: true
                },
            },
            submitHandler: function(form) {
                var email = document.getElementById("rest_pass_email").value; 
                    
                $.ajax({
                    type:"POST",
                    url:"user/reset_password",
                    data: { email:email },
                    beforeSend: function() {
                        $('#divLoading').addClass('show');
                    },
                    complete: function() {
                        $('#divLoading').removeClass('show');
                    },
                    success: function() {
                        showSweetAlert('If this email exists in our database, we\'ll send you an email with instructions.','Success!');

                        document.getElementById("resPass-form").reset();

                        window.setTimeout(function () {
                            window.location.replace("login");
                        }, 2000); 
                    }
                });
            }
        });
 
    } else {

        var $validator = $('.wizard-card form').validate({
            onkeyup: function(element, event) {
                if (event.which === 9 && this.elementValue(element) === "") {
                    return;
                } else {
                    this.element(element);
                }
            },
            rules: {
                pswd: {
                    required: true,
                },
                pswrdVerify: {
                    required: true,
                    equalTo: '[name="pswd"]'
                },
            },
            submitHandler: function(form) {
                var pData = $("#verify-form").serialize();	
    
                $.ajax({
                    type: "POST",
                    url: window.location.origin + "/user/reset_api",
                    data: { pDa:pData },
                    beforeSend: function() {
                        $('#divLoading').addClass('show');
                    },
                    complete: function() {
                        $('#divLoading').removeClass('show');
                    },
                    success: function(data) {
                        setTimeout(function(){ 
                            if (data == 1) {
                                showSweetAlert( 'Success!','Password changed.');
                            } else {
                                if (data == 2) {
                                    showSweetAlert( 'Whoops!','Unable to reset password at this time.','error');
                                } else if (data == 3) {
                                    showSweetAlert( 'Whoops!','Password do not match.','error');
                                } else if (data == 4) {
                                    showSweetAlert( 'Whoops!','Link expired.','error');
                                }

                                document.getElementById("verify-form").reset();
                                return false;
                            }
        
                            document.getElementById("verify-form").reset();
                            window.setTimeout(function () {
                                window.location.replace("login");
                            }, 2000);
                        }, 1000);
                    }
                });
            }
        });
    }

    // Wizard Initialization
    $('.wizard-card').bootstrapWizard({
        'tabClass': 'nav nav-pills',
        'nextSelector': '.btn-next',
        'previousSelector': '.btn-previous',

        onNext: function(tab, navigation, index) {
            var $valid = $('.wizard-card form').valid();
            if(!$valid) {
                $validator.focusInvalid();
                return false;
            }
        },

        onInit : function(tab, navigation, index){

          //check number of tabs and fill the entire row
          var $total = navigation.find('li').length;
          $width = 100/$total;
          var $wizard = navigation.closest('.wizard-card');

          $display_width = $(document).width();

          if($display_width < 600 && $total > 3){
              $width = 50;
          }

           navigation.find('li').css('width',$width + '%');
           $first_li = navigation.find('li:first-child a').html();
           $moving_div = $('<div class="moving-tab">' + $first_li + '</div>');
           $('.wizard-card .wizard-navigation').append($moving_div);
           refreshAnimation($wizard, index);
           $('.moving-tab').css('transition','transform 0s');
       },

        onTabClick : function(tab, navigation, index){

            var $valid = $('.wizard-card form').valid();

            if(!$valid){
                return false;
            } else {
                return true;
            }
        },

        onTabShow: function(tab, navigation, index) {
            var $total = navigation.find('li').length;
            var $current = index+1;

            var $wizard = navigation.closest('.wizard-card');

            // If it's the last tab then hide the last button and show the finish instead
            if($current >= $total) {
                $($wizard).find('.btn-next').hide();
                $($wizard).find('.btn-finish').show();
            } else {
                $($wizard).find('.btn-next').show();
                $($wizard).find('.btn-finish').hide();
            }

            button_text = navigation.find('li:nth-child(' + $current + ') a').html();

            setTimeout(function(){
                $('.moving-tab').text(button_text);
            }, 150);

            var checkbox = $('.footer-checkbox');

            if( !index == 0 ){
                $(checkbox).css({
                    'opacity':'0',
                    'visibility':'hidden',
                    'position':'absolute'
                });
            } else {
                $(checkbox).css({
                    'opacity':'1',
                    'visibility':'visible'
                });
            }

            refreshAnimation($wizard, index);
        }
    });


    // Prepare the preview for profile picture
    $("#wizard-picture").change(function(){
        readURL(this);
    });

    $('[data-toggle="wizard-radio"]').click(function(){
        wizard = $(this).closest('.wizard-card');
        wizard.find('[data-toggle="wizard-radio"]').removeClass('active');
        $(this).addClass('active');
        $(wizard).find('[type="radio"]').removeAttr('checked');
        $(this).find('[type="radio"]').attr('checked','true');
    });

    $('[data-toggle="wizard-checkbox"]').click(function(){
        if( $(this).hasClass('active')){
            $(this).removeClass('active');
            $(this).find('[type="checkbox"]').removeAttr('checked');
        } else {
            $(this).addClass('active');
            $(this).find('[type="checkbox"]').attr('checked','true');
        }
    });

    $('.set-full-height').css('height', 'auto');

});



 //Function to show image before upload

function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            $('#wizardPicturePreview').attr('src', e.target.result).fadeIn('slow');
        }
        reader.readAsDataURL(input.files[0]);
    }
}

$(window).resize(function(){
    $('.wizard-card').each(function(){
        $wizard = $(this);
        index = $wizard.bootstrapWizard('currentIndex');
        refreshAnimation($wizard, index);

        $('.moving-tab').css({
            'transition': 'transform 0s'
        });
    });
});

function refreshAnimation($wizard, index){
    total_steps = $wizard.find('li').length;
    move_distance = $wizard.width() / total_steps;
    step_width = move_distance;
    move_distance *= index;

    $wizard.find('.moving-tab').css('width', step_width);
    $('.moving-tab').css({
        'transform':'translate3d(' + move_distance + 'px, 0, 0)',
        'transition': 'all 0.3s ease-out'

    });
}

function debounce(func, wait, immediate) {
    var timeout;
    return function() {
        var context = this, args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(function() {
            timeout = null;
            if (!immediate) func.apply(context, args);
        }, wait);
        if (immediate && !timeout) func.apply(context, args);
    };
};
