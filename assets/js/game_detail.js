$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();
    $(".time-zone-select").timezones();

    if ($.cookie("game_detail_slug")) {
        $.removeCookie('game_detail_slug', { path: '/' });

    }

    $(document).on('mouseover', function(e) {
        var parent = $(e.target).closest(".game_card")
        var cards = $("#moreUserGames").find(".game_card");
        cards.each((index, elem) => {
            if (index !== parent.index()) {
                elem.classList.remove("show");
            } else {
                if (!elem.classList.contains("show")) {
                    elem.classList.add("show");
                }
            }
        });
    });

    $("#play-game-btn").on("click", function(e) {
        e.preventDefault();

        openGamePlayModalWithCheck();
    });

    function openGamePlayModalWithCheck() {
        if (hasAdblock()) {
            showSweetAlerForAdBlocker('We detected an AdBlocker. We do not have ads, but our analytics can trigger AdBlockers. Our games may not work correctly with an AdBlocker enabled, so please whitelist our site. Thank you.', 'Warning', "error", 'I understand')
        } else {
            $('#playGameModal').modal();
        }
    }     

    $("#playGameModal .continue").on("click", function(e) {
        e.preventDefault();
        var howto = $("#how-to-play");
        var playgame = $("#play-game");
        
        if (howto.is(":visible")) {
            howto.addClass("d-none");
            playgame.removeClass("d-none");
        } else {
            playgame.addClass("d-none");
            howto.removeClass("d-none");
        }
    });

    const game_slug = window.location.pathname.split('/').filter(Boolean).pop();
    const credit_cost_min = parseFloat($("input[name=contribution]").attr("min"));
    const balance = parseFloat($("input[name=available_balance]").val());

    document.addEventListener('keydown', function(e) {
        if (e.key == "Shift") {
            incrementScale = 10;
        } else if (e.key == "Control") {
            incrementScale = 2;
        }
    });

    document.addEventListener('keyup', function(e) {
        if (e.key == "Shift" || e.key == "Control") {
            incrementScale = 1;
        }
    });

    var incrementStartTime;
    var incrementTimeout, incrementInterval;
    $("#play-add, #play-minus").on("mousedown touchstart", function(e) {
        e.preventDefault();

        incrementStartTime = Date.now();
        var target = e.currentTarget.id;
        
        incrementValue(target);
        incrementTimeout = setTimeout(function() {
            continuouslyIncrement(target);
        }, 300);
    });

    $("#play-custom").on("click", function(e) {
        e.preventDefault();

        var editable = $("#credit_cost_text").attr("contenteditable");
        if (editable) {
            $("#play-custom").removeAttr("disabled");
            $("#play-custom").removeClass("disabled");

            $("#credit_cost_text").removeAttr("contenteditable");
            $("#credit_cost_text").onclick = null;
        } else {
            $("#play-custom").attr("disabled");
            $("#play-custom").addClass("disabled");
            $("#credit_cost_text").attr("contenteditable", true);
            $("#credit_cost_text").click();
            window.getSelection().selectAllChildren($("#credit_cost_text")[0]);
        }
    });

    $(document).on("keypress", "#credit_cost_text[contenteditable='true']", function(e) {
        if (e.keyCode == "13") {
            e.preventDefault();
            $(this).blur();
        }
    });

    $(document).on("focusout", "#credit_cost_text[contenteditable='true']", function(e) {
        var new_credit_cost = Math.max(credit_cost_min, parseFloat($(this).text().trim()));
        new_credit_cost = isNaN(new_credit_cost) ? credit_cost_min : new_credit_cost;
        $("input[name=contribution]").val(new_credit_cost.toFixed(2));
        $("#credit_cost_text").text(new_credit_cost.toFixed(2));
        $("#credit_cost_text").removeAttr("contenteditable");
        $("#credit_cost_text").onclick = null;
        showIncrementError();

        $("#play-custom").removeAttr("disabled");
        $("#play-custom").removeClass("disabled");
    });

    $(document).on("mouseup touchend", function(e) {
        if (incrementTimeout || incrementInterval) {
            clearTimeout(incrementTimeout);
            clearInterval(incrementInterval);

            showIncrementError();
        }
    });

    const increment = 0.01; var incrementScale = 1;
    var current_increment = increment;
    function incrementValue(target) {
        var timePassed = Date.now() - incrementStartTime; //in ms
        current_increment = ((timePassed/7300 > 1) ? ((timePassed/7300 > 3) ? increment * 1000 : increment * 100) : 0.01) * incrementScale;

        var credit_cost = parseFloat($("input[name=contribution]").val());
        var new_credit_cost = clamp(parseFloat((target == "play-add") ? credit_cost+current_increment : credit_cost-current_increment), credit_cost_min, balance+0.01);
        $("input[name=contribution]").val(new_credit_cost.toFixed(2));
        $("#credit_cost_text").text(new_credit_cost.toFixed(2));
        if (new_credit_cost == credit_cost_min) {
            $("#play-minus").addClass("d-none");
        } else {
            $("#play-minus").removeClass("d-none");
        }
    }

    function continuouslyIncrement(target) {
        clearInterval(incrementInterval);
        incrementInterval = setTimeout(function() {
            incrementValue(target);
            showIncrementError();
            continuouslyIncrement(target);
        }, (current_increment < 1) ? 50 : 250)
    }

    function showIncrementError() {
        var new_credit_cost = parseFloat($("input[name=contribution]").val());

        if (new_credit_cost > balance) { 
            clearTimeout(incrementTimeout);
            clearInterval(incrementInterval);
            
            $('#play-add').addClass("disabled");
            $('#play-add').prop("disabled", true);
            $('#pay-to-play').addClass("disabled");
            $('#pay-to-play').prop("disabled", true);

            var buy_credits = '<u><a href="' + window.location.origin + '/games/playing/' + game_slug + '/?custom_amount=' + credit_cost_min + '" class="text-primary">buy more credits</a></u>';
            $('#amountError').html("Insufficient credits available. Please " + buy_credits + " or adjust donation amount.");
            $('#amountErrorContainer').removeClass("d-none");
        } else if (new_credit_cost <= credit_cost_min) { 
            clearTimeout(incrementTimeout);
            clearInterval(incrementInterval);
            
            $('#play-minus').addClass("d-none");
            
            if (new_credit_cost < credit_cost_min) {
                $('#amountError').text('Contribution is less than minimum cost to play.');
                $('#amountErrorContainer').removeClass("d-none");
            } else {
                resetContribution(new_credit_cost);
            }
        } else {
            resetContribution(new_credit_cost);
        }
    }

    function resetContribution(credit_cost) {
        $('#play-add').removeClass("disabled");
        $('#play-add').prop("disabled", false);
        if (credit_cost > credit_cost_min) {
            $('#play-minus').removeClass("d-none");
            $('#play-minus').removeClass("disabled");
            $('#play-minus').prop("disabled", false);
        }
        $('#pay-to-play').removeClass("disabled");
        $('#pay-to-play').prop("disabled", false);
        $('#amountErrorContainer').addClass("d-none");
        $('#amountError').html("");
    }

    $('#pay-to-play').on("click", function(e) {
        e.preventDefault();
        if (e.target != this && $(e.target).attr("contenteditable") == 'true') {
            return;
        }

        var selected_beneficiary = $("input[name=selected_beneficiary]").val();
        var credit_cost = parseFloat($("input[name=contribution]").val());
        if (credit_cost > balance) { 
            $('#play-add').addClass("disabled");
            $('#play-add').prop("disabled", true);
            $('#pay-to-play').addClass("disabled");
            $('#pay-to-play').prop("disabled", true);

            var buy_credits = '<u><a href="' + window.location.origin + '/games/playing/' + game_slug + '?custom_amount=' + credit_cost_min + '" class="text-primary">buy more credits</a></u>';
            $('#amountError').html("Insufficient credits available. Please " + buy_credits + " or adjust donation amount.");
            $('#amountErrorContainer').removeClass("d-none");

            return;
        } else if (credit_cost <= credit_cost_min) { 
            $('#play-minus').addClass("d-none");
            
            if (credit_cost < credit_cost_min) {
                $('#amountError').text('Contribution is less than minimum cost to play.');
                $('#amountErrorContainer').removeClass("d-none");
                return;
            }
        } else {
            $('#amountErrorContainer').addClass("d-none");
            $('#amountError').html("");
        }

        showSweetConfirm("Once the game is unlocked, you cannot stop or reset.", "Are you ready to proceed?", $icon = 'warning', function(confirmed) {
            if (!confirmed) {
                e.preventDefault();
            } else {
                
                window.open(window.location.origin + '/games/playing/' + game_slug + '/?selected_beneficiary=' + selected_beneficiary + '&custom_amount=' + credit_cost, "_self");
            }
        });
    });

    function clamp(num, min, max) {
		return Math.min(Math.max(num, min), max)
	}

    $('#all-approved-charity').click(function() {
        $('#user-ch').addClass('d-none');
        $('#all-ch').removeClass('d-none');
    
        $('select option:first-child').attr('selected', 'selected');
        var val = $("#all-charity").val();
        var val1 = $("#user-charity").val();

        if (val != "" && val != val1) getCharityDetail(val);
    });

    $('#user-approved-charity').click(function() {
        $('#all-ch').addClass('d-none');
        $('#user-ch').removeClass('d-none');
    
        $('select option:first-child').attr('selected', 'selected');
        var val = $("#user-charity").val();
        var val1 = $("#all-charity").val();

        if (val != "" && val != val1) getCharityDetail(val);
    });

    $('.fundraise_select').on('click', function(e) {
        // e.stopPropagation();
    });

    $('.fundraise_select').selectize({
        maxItems: 1,
        closeAfterSelect: false,
    });

    $('#all-charity, #user-charity').change(function(e) {
        e.stopPropagation();
        var val = $(this).val();
        if (val) getCharityDetail(val);
    });

    function getCharityDetail(slug) {
        $.ajax({
            type: "GET",
            url: window.location.origin + "/fundraisers/getCharityDetail",
            data: {
                slug: slug
            },
            beforeSend: function() {
                $("#fund-detail").siblings(".loader").removeClass("d-none");
            },
            complete: function() {
                $("#fund-detail").siblings(".loader").addClass("d-none");
            },
            success: function(response) {
                if (response) {
                    $("#fund-detail").html(response);
                    $("input[name=selected_beneficiary]").val(slug);
                }
            }
        });
    }

    $(document).on("mouseenter", ".wishcard-yes", function() {
        $(this).find('i').removeClass('fa-heart');
        $(this).find('i').addClass('fa-heart-o');
    });

    $(document).on("mouseleave", ".wishcard-yes", function() {
        $(this).find('i').removeClass('fa-heart-o');
        $(this).find('i').addClass('fa-heart');
    });

    $(document).on("mouseenter", ".wishcard-no", function() {
        $(this).find('i').removeClass('fa-heart-o');
        $(this).find('i').addClass('fa-heart');
    });

    $(document).on("mouseleave", ".wishcard-no", function() {
        $(this).find('i').removeClass('fa-heart');
        $(this).find('i').addClass('fa-heart-o');
    });

    $(document).on('click', '.wishcard-no', function (){
        var id = $(this).attr('data-id');
        var wishcard = $(this);
        var icon = $(this).find("i");

        $.ajax({
            method:"POST",
            data:{id:id},
            url: window.location.origin + '/games/add_wishlist',
            success: function(result){
                if(result == 'already'){
                    showSweetAlert('This game has already been added to wishlist', 'Whoops!', 'info');
                } else {
                    $(wishcard).addClass("wishcard-yes"); $(wishcard).removeClass("wishcard-no");
                    $(icon).addClass("fa-heart"); $(icon).removeClass("fa-heart-o");
                    showSweetAlert('This game has been added to wishlist', 'Great');
                }
            }
        });
    });

    $(document).on('click', '.wishcard-yes', function (){
        var id = $(this).attr('data-id');
        var wishcard = $(this);
        var icon = $(this).find("i");

        $.ajax({
            method:"POST",
            data:{id:id},
            url: window.location.origin + '/games/remove_wishlist',
            success: function(result){
                if(result == 'already'){
                    showSweetAlert('This game has already been removed from wishlist', 'Whoops!', 'info');
                } else {
                    $(wishcard).addClass("wishcard-no"); $(wishcard).removeClass("wishcard-yes");
                    $(icon).addClass("fa-heart-o"); $(icon).removeClass("fa-heart");
                    showSweetAlert('This game has been removed from wishlist', 'Great');
                }
            }
        });
    });
        
    const shareBtn = document.querySelector('.share-btn');
    const shareOptions = document.querySelector('.share-options');

    shareBtn.addEventListener('click', () => {
        shareOptions.classList.toggle('active');
    })
        
    function copyToClipboard(text) {
        if (window.clipboardData && window.clipboardData.setData) {
            // IE specific code path to prevent textarea being shown while dialog is visible.
            return clipboardData.setData("Text", text); 

        } else if (document.queryCommandSupported && document.queryCommandSupported("copy")) {
            var textarea = document.createElement("textarea");
            textarea.textContent = text;
            textarea.style.position = "fixed";  // Prevent scrolling to bottom of page in MS Edge.
            document.body.appendChild(textarea);
            textarea.select();
            try {
                return document.execCommand("copy");  // Security exception may be thrown by some browsers.
            } catch (ex) {
                console.warn("Copy to clipboard failed.", ex);
                return false;
            } finally {
                document.body.removeChild(textarea);
            }
        }
    }

    document.querySelector(".copy-btn").onclick = function() {
        var result = copyToClipboard($('.link').html());
        $(".copy-btn").html('Copied');
    };

    $(".btn-share").hover(function() {
        $(this).css('cursor','pointer').attr('title', 'Click to share');
    }, function() {
        $(this).css('cursor','auto');
    });

    $('.deleteGameBtn').click(function (){
        var slug = $(this).attr('data-id');

        var title = "Are you sure  you want to delete this game?";
        var text = "You will not be able to recover this again!";
        showSweetConfirm(text, title, "warning", function(confirmed) {
            if (!confirmed) {
                e.preventDefault();
                showSweetAlert('Your game was not deleted.', 'Cancelled', 'warning');
            } else {
                $('#divLoading').addClass('show');
                $.ajax({
                    method:"POST",
                    data:{slug:slug},
                    url: window.location.origin + '/games/deleteDraftedGame',
                    success: function(result){
                        if(result == '1'){
                        showSweetAlert('This game has been removed from drafted games', 'Great');
                        window.location.href = window.location.origin + '/games/show/drafted' ;
                        } else {
                            showSweetAlert('The game could not be deleted, please try again.', 'Oops', 'error');
                            location.reload(); 
                        }
                    }
                });
            }     
        }); 
    });

    $('.publishTimePicker').click(function(){
		$('#timePickerDiv').slideToggle();
    });

    $('#cancel_publish').click(function() {
        $('#timePickerDiv').slideUp();
    }); 

    function getLocalTime(time) {
		//returns correct time in terms of user's timezone
		time = new Date(time);

		//get user's timezone offset, selected timezone offset, and difference between
		var localOffset = new Date().getTimezoneOffset();
		var tzOffset = $("#timeZone :selected").data("offset");
		var offset = (-parseInt(tzOffset) * 60) - localOffset;

		// add difference between user's timezone and selected timezone
		var localTime = new Date(time.getTime() + (offset * 60000));

		return localTime;
	}

    function calcTime(publishTime) {
        timeZone = $("#timeZone").val();
        offset = $("#timeZone option[value='"+ timeZone +"']").data("offset")

		// create Date object for current location
		d = new Date(publishTime);
		// add local time zone offset
		utc = new Date(d.getTime() + (-parseInt(offset) * 60) * 60000);

		var year = String(utc.getFullYear()).padStart(2, '0');
		var mon = String(utc.getMonth()+1).padStart(2, '0');
		var day = String(utc.getDate()).padStart(2, '0');

		var hour = String(utc.getHours()).padStart(2, '0');
		var min = String(utc.getMinutes()).padStart(2, '0');
		var sec = String(utc.getSeconds()).padStart(2, '0');

		return year+"-"+mon+"-"+day+" "+hour+":"+min+":"+sec;
	}

    $('#publish_btn').click(function(e) {
        e.stopPropagation();

        var publish_date = new Date($('#publishdate').val());
        var current_date = new Date();

        if(publish_date.getTime() < current_date.getTime()){
            showSweetAlert("Please enter a future date.", 'Whoops!', 'error');
            return false;
        } else {
            var title = "Are you sure you want to publish this game?";
            var text = "You will not be allowed to make any additional changes!";
            showSweetConfirm(text, title, "warning", function(confirmed) {
                if (confirmed) {
                    $.ajax({
                        type: "POST",
                        url: window.location.origin + "/games/publishGame",
                        data: {
                            pTime:  getLocalTime(publish_date).toUTCString(), 
                            slug : $('.deleteGameBtn').attr('data-id')
                        },
                        success: function(response) {
                            response = JSON.parse(response);
                            if (response.status === "success") {
                                showSweetAlert(response.msg, 'Success!', "success");
                                window.location.href = window.location.origin + '/games/show/published' ;
                            } else {
                                showSweetAlert(response.msg, 'Whoops!', "error");
                            }
                        }
                    });
                } else {
                    showSweetAlert('Your game was not published.', 'Cancelled', 'warning');
                }     
            }); 
        }   
    });

    $('.makeGameLiveBtn').click(function(e) {
        e.stopPropagation();

        var slug = $('.deleteGameBtn').attr('data-id');
        
        var title = "Are you sure you want to make this game live?";
        var text = "You will not be allowed to make any additional changes!";
        showSweetConfirm(text, title, "warning", function(confirmed) {
            if (confirmed) {
                $('#divLoading').addClass('show');

                $.ajax({
                    type: "POST",
                    url: window.location.origin + "/games/liveGame",
                    data: {
                        slug : slug
                    },
                    success: function(response) {
                        response = JSON.parse(response);
                        if (response.status === "success") {
                            showSweetAlert(response.msg, 'Success!', "success");
                            window.location.href = window.location.origin + '/games/show/live/' + slug;
                        } else {
                            showSweetAlert(response.msg, 'Whoops!', "error");
                        }
                    }
                });
            } else {
                showSweetAlert('Your game was not made live.', 'Cancelled', 'warning');
            }     
        }); 
    });

    
    $('.duplicateGameBtn').click(function (){
        var id = $(this).attr('data-id');

        var title = "Are you sure you want to duplicate this game?";
        var text = "This will create a replica of this game";
        showSweetConfirm(text, title, "warning", function(confirmed) {
            if (!confirmed) {
                showSweetAlert('Your game could not be duplicated.', 'Cancelled', 'warning');
            } else {
                $('#divLoading').addClass('show');
                $.ajax({
                    method:"POST",
                    data:{id:id},
                    url: window.location.origin + '/games/duplicateGame',
                    success: function(result){
                        if(result){
                            showSweetAlert('This game has been duplicated, redirecting...', 'Great');
                            setTimeout(() => {
                                window.open(window.location.origin + '/games/show/drafted/' + result);
                            }, 3000);
                        } else {
                            showSweetAlert('The game could not be duplicated, please try again.', 'Oops', 'error');
                            location.reload(); 
                        }
                    }
                });
            }     
        }); 
    });

    // set link to share on social media
    $('.social-media-btn').on( "click" , function() {
        var gameLink = $('.link').html();
        var socialMediaPlatform = $(this).find('i').attr('class');

        if(socialMediaPlatform.includes('whatsapp')) {
            var shareUrl = "whatsapp://send?text=" + gameLink;
        }
        if(socialMediaPlatform.includes('facebook')) {
            var shareUrl = "https://www.facebook.com/sharer/sharer.php?u=" + gameLink;
        }
        if(socialMediaPlatform.includes('twitter')) {
            var shareUrl = "http://twitter.com/share?url=" + gameLink;
        }
        if(socialMediaPlatform.includes('linkedin')) {
            var shareUrl = "https://www.linkedin.com/sharing/share-offsite/?url=" + gameLink;
        }
        if(socialMediaPlatform.includes('google')) {
            var shareUrl = "https://mail.google.com/mail/u/0/?view=cm&to&su=Check this out on WinWinLabs&body=" + gameLink + "&bcc&cc&fs=1&tf=1";
        }
		if(socialMediaPlatform.includes('envelope')) {
            var shareUrl = "mailto:?subject=Check this out on WinWinLabs&body=" + gameLink;
        }

        window.open(shareUrl, '_blank')
    });
});