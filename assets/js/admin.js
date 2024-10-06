$(document).ready(function() {
    // toggle for game and user status
    $("input[class='game_active_switch']").bootstrapToggle();
    $("input[class='user_status_switch']").bootstrapToggle();

    $("content textarea").each(function(editor) {
        var id = $(this).attr("id");
        var content = $(this).text();

        tinyMCEInitialize(id, content);
    });

    // create user function
    $('.create_user').on('click', function() {
        var new_username = $('#new_username').val();
        var new_email = $('#new_email').val();
        var new_pass = $('#new_password').val();
        var new_role = $('#roleSelect').val();        

        var pattern = /^(?=.{5,})(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[\W])/

        var checkval = pattern.test($("#new_password").val());

        if (new_username == "") {
            showSweetAlert("Fill in the username", "Missing info.", "info");
        }

        if (new_email == "") {
            showSweetAlert("Fill in email address", "Missing info.", "info");
        } else if (!checkval) {
            showSweetAlert("Invalid password", "Whoops!", "error");
        } else {

            $.ajax({
                type: "POST",
                data: {
                    username: new_username,
                    email: new_email,
                    password: new_pass,
                    role: new_role
                },
                url: window.location.origin + '/admin/admin_create_new_user',
                dataType: "JSON",
                beforeSend: function() {
                    $('#divLoading').addClass('show');
                },
                complete: function() {
                    $('#divLoading').removeClass('show');
                },
                success: function(e) {
                    showSweetAlert('A new user has been created.', 'Great');
                    location.reload();
                }

            });
        }
    });

    $('.user_status').bootstrapToggle({
        on: 'Enabled',
        off: 'Disabled'
    });

    // update password
    $('#myAdvancedTable tbody').on('click', '.btn_update_pass', function() {
        var updated_user_password = $(this).closest('tr').find('.password_user').val();
        var user_id = $(this).closest('tr').find('.password_user').attr("data-id");

        var pattern = /^(?=.{5,})(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[\W])/

        var checkval = pattern.test($(this).closest('tr').find('.password_user').val());


        if (!checkval) {
            showSweetAlert("Invalid password", "Whoops!", "error");
        } else {
            $.ajax({
                type: "POST",
                data: {
                    id: user_id,
                    password: updated_user_password
                },
                url: window.location.origin + '/admin/update_password',
                dataType: "JSON",
                success: function(e) {
                    if (e.done == 1) {
                        location.reload();
                    } else {

                    }
                }

            });
        }
    });
    //

    //game active status
    $('#myAdvancedTable tbody').on('change', '.game_active_switch', function() {

        var game_id = $(this).val();
        var status = 'No';
        if ($(this).prop('checked') == true) {
            status = 'Yes';
        } else {
            status = 'No';
        }
        $.ajax({
            type: "POST",
            data: {
                id: game_id,
                status: status
            },
            url: window.location.origin + '/admin/game_active',
            dataType: "JSON",
            success: function(e) {
                if (e.done == 1) {
                    $("myAdvancedTable[data-type='games']").DataTable().clearPipeline(false).draw();
                } else {

                }
            }

        });
    });
    // 

    // 

    $('.game_status_switch').change(function() {
        var game_id = $(this).val();
        var status = 'No';
        if ($(this).prop('checked') == true) {
            status = 'Yes';
        } else {
            status = 'No';
        }
        // alert(game_id);
        $.ajax({
            type: "POST",
            data: {
                id: game_id,
                status: status
            },
            url: "<?php echo asset_url('account/manage_games/game_status'); ?>",
            //alert( url);
            dataType: "JSON",
            success: function(e) {
                if (e.done == 1) {
                    $("myAdvancedTable[data-type='games']").DataTable().clearPipeline(false).draw();
                } else {

                }
            }

        });
    });
    // 

    // game server switch
    $('#myAdvancedTable tbody').on('change', '.game_server_switch', function() {
        var game_id = $(this).val();
        var status = 'No';
        if ($(this).prop('checked') == true) {
            status = '1';
        } else {
            status = '0';
        }
        $.ajax({
            type: "POST",
            data: {
                id: game_id,
                status: status
            },
            url: window.location.origin + '/admin/game_server',
            dataType: "JSON",
            success: function(e) {
                if (e.done == 1) {
                    location.reload();
                } else {

                }
            }

        });
    });

    // game delete
    $('#btnGameDel').click(function() {

        showSweetConfirm("Are you sure you want to delete this?", "Attention", $icon = 'info', function(confirmed) {
            if (!confirmed) {
                return false;
            } else {
                var id = [];

                $(':checkbox.delete_game:checked').each(function(i) {
                    id[i] = $(this).val();
                });

                if (id.length === 0) //tell you if the array is empty
                {
                    showSweetAlert("Please Select atleast one checkbox.", "Whoops!", "error");
                } else {
                    $.ajax({
                        url: window.location.origin + '/admin/game_remove',
                        type: 'POST',
                        data: {
                            rData: id
                        },
                        dataType: "JSON",
                        success: function(e) {
                            console.log(e);
                            if (e.done == 1) {
                                location.reload();
                            } else {
                                location.reload();
                            }
                        }

                    });
                }
            }
        });
    });
    //      

    // account update
    $('#myAdvancedTable tbody').on('change', '.user_status_switch', function() {
        var user_id = $(this).val();
        var status = 'No';
        if ($(this).prop('checked') == true) {
            status = 'Yes';
        } else {
            status = 'No';
        }
        $.ajax({
            type: "POST",
            data: {
                id: user_id,
                status: status
            },
            url: window.location.origin + '/admin/user_status',
            dataType: "JSON",
            success: function(e) {
                if (e.done == 1) {
                    $("myAdvancedTable[data-type='games']").DataTable().clearPipeline(false).draw();
                } else {

                }
            }

        });
    });

    // tester status
    $('#myAdvancedTable tbody').on('change', '.tester_status_switch', function() {
        var user_id = $(this).val();
        var status = 'No';
        if ($(this).prop('checked') == true) {
            status = 'Yes';
        } else {
            status = 'No';
        }
        $.ajax({
            type: "POST",
            data: {
                id: user_id,
                status: status
            },
            url: window.location.origin + '/admin/tester_status',
            dataType: "JSON",
            success: function(e) {
                if (e.done == 1) {
                    location.reload();
                } else {

                }
            }

        });
    });
    //

    // creator Status
    $('#myAdvancedTable tbody').on('change', '.creator_status_switch', function() {
        var user_id = $(this).val();
        var status = 'No';
        if ($(this).prop('checked') == true) {
            status = 'Yes';
        } else {
            status = 'No';
        }
        $.ajax({
            type: "POST",
            data: {
                id: user_id,
                status: status
            },
            url: window.location.origin + '/admin/creator_status',
            dataType: "JSON",
            success: function(e) {
                if (e.done == 1) {
                    location.reload();
                } else {

                }
            }

        });
    });

     // creator Status
     $('#myAdvancedTable tbody').on('change', '.credit_withdraw_status_switch', function() {
        var user_id = $(this).val();
        var status = 'No';
        if ($(this).prop('checked') == true) {
            status = 'Yes';
        } else {
            status = 'No';
        }
        $.ajax({
            type: "POST",
            data: {
                id: user_id,
                status: status
            },
            url: window.location.origin + '/admin/credit_withdraw_status',
            dataType: "JSON",
            success: function(e) {
                if (e.done == 1) {
                    location.reload();
                } else {

                }
            }

        });
    });

    //

    //delete users
    $('#btn_delete').click(function() {

        showSweetConfirm("Are you sure you want to delete this?", "Attention", $icon = 'info', function(confirmed) {
            if (!confirmed) {
                return false;
            } else {
                var id = [];

                $(':checkbox.delete_user:checked').each(function(i) {
                    id[i] = $(this).val();
                });

                if (id.length === 0) //tell you if the array is empty
                {
                    showSweetAlert("Please Select atleast one checkbox.", "Whoops!", "error");
                } else {
                    $.ajax({
                        url: window.location.origin + '/admin/delete_users',
                        type: 'POST',
                        data: {
                            rData: id
                        },
                        dataType: "JSON",
                        success: function(e) {
                            console.log(e);
                            if (e.done == 1) {
                                location.reload();
                            } else {
                                location.reload();
                            }
                        }

                    });
                }
            }
        });
    });
    //

    // give coupon
    $('#myAdvancedTable tbody').on('change', '#couponSelect', function() {
        var user_id = $(this).closest('tr').find('.password_user').attr("data-id");
        var coupon_code = this.value;
        var coupon_content = this.options[this.selectedIndex].innerText;
        var selectedUsername = this.options[this.selectedIndex].getAttribute('user-data');
        
        showSweetConfirm('', 'Are you sure you want to give "' + coupon_content + '" coupon to ' + selectedUsername + ' ?', 
            $icon='warning', function(confirmed) {
            if (confirmed) {
                $.ajax({
                    type: "POST",
                    data: {
                        id: user_id,
                        coupon: coupon_code
                    },
                    url: window.location.origin + '/admin/give_coupon',
                    dataType: "JSON",
                    success: function(e) {
                        if (e.done == 1) {
                            showSweetAlert("Coupon given successfully!", "Success", "success");
                            location.reload();
                        } else {
                            showSweetAlert("Coupon could not be given at this time!", "Whoops!", "error");
                        }
                    }
        
                });
            }
        });
    });
    //

    //update user data inline
    $('#myAdvancedTable tbody').on('click', 'td[contenteditable=true]', function() {
        $(this).css('border', '3px rgb(161,31,26) dotted');
        $('#myAdvancedTable tbody').on('blur', 'td[contenteditable=true]', function() {
            var content_main = $(this).text().trim();
            var content_id = $(this).attr("id").trim();
            var content_name = $(this).attr("name").trim();
            $.ajax({
                type: "POST",
                data: {
                    id: content_id,
                    content: content_main,
                    name: content_name
                },
                url: window.location.origin + '/admin/update_created_user',
                dataType: "JSON",
                success: function(e) {
                    if (e.done == 1) {
                        showSweetAlert("Field has been updated", "Attention", "info");
                        location.reload();
                    } else {

                    }
                }

            });
        });
    });

    $('#addQuoteBtn').on('click', function() {
        var qtCategory = $('#category-name').val();
        var qtSource = $('#source-name').val();
        var qtOrder = $('#order-num').val();
        tinymce.activeEditor.uploadImages(function(success) {
            var qtDescription = tinymce.get('quote-description-text').getContent();
            var quote_id = $('#qIdEdit').val();
            if (quote_id === undefined) {
                qUrl = '/admin/add_quote';
            } else {
                qUrl = '/admin/update_quote';
            }
            var qdata = {
                quote_category: qtCategory,
                quote_description: qtDescription,
                quote_source: qtSource,
                quote_order: qtOrder,
                update_id: quote_id === undefined ? '' : quote_id
            }
            $.ajax({
                type: "POST",
                data: qdata,
                url: window.location.origin + qUrl,
                success: function(response) {
                    if (response == true) {
                        if (quote_id === undefined) {
                            showSweetAlert("New quote has been added", "Great!", 'info');
                        } else {
                            showSweetAlert("Quote has been updated", "Great!", 'info')
                        }
                        location.reload();
                    } else {
                        showSweetAlert("Something went wrong, please try again", "Oops!", 'error');
                    }
                }
            });
        });
    });

    $('#myAdvancedTable tbody').on('click', 'a.edit_quote', function() {
        var quote_id = $(this).attr('quote-id');
        $.ajax({
            type: "POST",
            data: {
                id: quote_id
            },
            url: window.location.origin + '/admin/getquote',
            dataType: "JSON",
            success: function(result) {
                var myModal = $('#quoteModal');
                $('#category-name', myModal).val(result.category);
                $('#source-name', myModal).val(result.source);
                (tinyMCE.activeEditor.setContent(result.description), myModal);
                $('#order-num', myModal).val(result.order_no);
                myModal.append('<input type="hidden" value = "' + result.id + '" id = "qIdEdit">');
                myModal.modal({
                    show: true
                });
            }

        });
    })

    $('#myAdvancedTable tbody').on('click', 'a.delete_quote', function() {
        var quote_id = $(this).attr('quote-id');

        showSweetConfirm("Are you sure you want to delete this?", "Attention", $icon = 'info', function(confirmed) {
            if (!confirmed) {
                e.preventDefault();
            } else {
                deletequote(quote_id);
            }
        });
    });

    function deletequote(quote_id) {
        if (quote_id.trim() != '') {
            $.ajax({
                type: "POST",
                data: {
                    rData: quote_id
                },
                url: window.location.origin + '/admin/deletequote',
                dataType: "JSON",
                success: function(e) {
                    if (e.done == 1) {
                        location.reload();
                    } else {
                        location.reload();
                    }
                }

            });
        }
    }

    $('#myAdvancedTable tbody').on('change', '.feature_quote_chk', function() {
        var quote_id = $(this).val();
        var status = 'No';
        if ($(this).prop('checked') == true) {
            status = 'Yes';
        } else {
            status = 'No';
        }
        $.ajax({
            type: "POST",
            data: {
                id: quote_id,
                status: status
            },
            url: window.location.origin + '/admin/make_featured_quote',
            dataType: "JSON",
            success: function(e) {
                if (e.done == 1) {
                    location.reload();
                } else {

                }
            }

        });
    });

    $('.delete_coupon_btn').on('click', function() {

        showSweetConfirm("Are you sure you want to delete this?", "Attention", $icon = 'info', function(confirmed) {
            if (!confirmed) {
                e.preventDefault();
            } else {
                var id = [];

                $(':checkbox.delete_coupon:checked').each(function(i) {
                    id[i] = $(this).val();
                });

                if (id.length === 0) //tell you if the array is empty
                {
                    showSweetAlert("Please Select atleast one checkbox.", "Whoops!", "error");
                } else {
                    $.ajax({
                        url: window.location.origin + '/admin/delete_coupons',
                        type: 'POST',
                        data: {
                            id: id
                        },
                        dataType: "JSON",
                        success: function(e) {
                            console.log(e);
                            if (e.done == 1) {
                                location.reload();
                            } else {
                                location.reload();
                            }
                        }

                    });
                }
            }
        });
    });

    $('.create_coupon').on('click', function() {
        var new_text = $('#new_coupon_description').val();
        var new_type = $("#new_coupon_amount").val();


        if (new_text == "") {
            showSweetAlert("Please fill in the description.", "Whoops!", "error");
            return false;
        } else if (new_type == "") {
            showSweetAlert("Please fill in the amount.", "Whoops!", "error");
            return false;
        }

        $.ajax({
            type: "POST",
            data: {
                description: new_text,
                amount: new_type
            },
            url: window.location.origin + '/admin/create_new_coupon',
            dataType: "JSON",
            beforeSend: function() {
                $('#divLoading').addClass('show');
            },
            complete: function() {
                $('#divLoading').removeClass('show');
            },
            success: function(e) {
                location.reload();
            }

        });
    });

    $('#myAdvancedTable tbody').on('change', '.coupon_active_switch', function() {
        var user_id = $(this).val();
        var status = 'No';
        if ($(this).prop('checked') == true) {
            status = 'Yes';
        } else {
            status = 'No';
        }

        $.ajax({
            type: "POST",
            data: {
                id: user_id,
                status: status
            },
            url: window.location.origin + '/admin/coupon_status',
            dataType: "JSON",
            success: function(e) {

                if (e.done == 1) {
                    location.reload();
                } else {

                }
            }

        });
    });

    //update coupon data
    $('#myAdvancedTable tbody').on('click', 'td[contenteditable=true]', function() {
        $(this).css('border', '3px rgb(161,31,26) dotted');
        $('#myAdvancedTable tbody').on('blur', 'td[contenteditable=true]', function() {
            var content_main = $(this).text().trim();
            var content_id = $(this).attr("id").trim();
            var content_name = $(this).attr("name").trim();

            $.ajax({
                type: "POST",
                data: {
                    id: content_id,
                    content: content_main,
                    name: content_name
                },
                url: window.location.origin + '/admin/update_coupon',
                dataType: "JSON",
                success: function(e) {
                    if (e.done == 1) {
                        location.reload();
                    } else {

                    }
                }

            });

        });
    });

    // email update

    $(document).ready(function() {

        $(document.body).on("click", "a[data-toggle='tab']", function(event) {
            location.hash = this.getAttribute("href");
        });

    });

    $(".btn-update").click(function(e) {
        var idClicked = e.target.id;
        switch (idClicked) {
            case 'welcome_email_update':
                var desc = tinyMCE.activeEditor.getContent();
                var subj = $.trim($("#subject_editor_welcome").val());

                var page_id = 2;
                break;

            case 'live_email_update':
                var desc = tinyMCE.activeEditor.getContent();
                var subj = $.trim($("#subject_editor_live").val());
                var page_id = 3;
                break;

            case 'winner_email_update':
                var desc = tinyMCE.activeEditor.getContent();
                var subj = $.trim($("#subject_editor_winner").val());
                var page_id = 4;
                break;

            case 'end_email_update':
                var desc = tinyMCE.activeEditor.getContent();
                var subj = $.trim($("#subject_editor_end").val());
                var page_id = 5;

                break;
            default:
                var desc = tinyMCE.activeEditor.getContent();
                var subj = $.trim($("#subject_editor_review").val());
                var page_id = 6;
        }


        if (desc != "") {
            var description = desc;
        }

        if (subj != "") {
            var substatus = subj;
        }

        var success_mess = 'Content Updated!';

        $.ajax({
            type: "POST",
            data: {
                id: page_id,
                description: description,
                subject: substatus
            },
            url: window.location.origin + '/admin/update_admin_email',
            success: function(result) {
                var response = JSON.parse(result);
                if (response.done == true) {
                    showSweetAlert('Email content has been updated.', 'Great');
                    location.reload();
                } else {

                }
            }

        });
    });

    $('#myAdvancedTable tbody').on('click', '.feedback-images', function() {
        var feedback_id = $(this).attr('data-id');
        var url = window.location.origin + '/admin/load_feedback_images/' + feedback_id;
        $('.modal-body').load(url, function() {
            $('#exampleModal').modal({
                show: true
            });
            var $gallery = $('.img-big-wrap'),
                $item = $('.item-gallery');
            $itemSrc = $('.item-gallery.active').css('background-image').replace('url(', '').replace(')', '');
            $gallery.css({
                'background-image': 'url(' + $itemSrc + ')'
            });
            $item.on('click mouseenter', function() {
                var image = $(this).css('background-image');
                imgSrc = image.replace('url(', '').replace(')', '');

                $(this).addClass('active').siblings().removeClass('active');

                $gallery.css({
                    'background-image': 'url(' + imgSrc + ')'
                })
            });
        });
    });

    $('#filter').on('change', function(e) {
        $("#filter").val($("#filter :selected").val());

        var table = $('#myAdvancedTable').DataTable();
        table.clearPipeline().draw();
    });

    $('.distribution').on('click', function(e) {
        var id = $(this).data("id");

        $.ajax({
            type: "POST",
            data: { id: id },
            url: window.location.origin + '/admin/getDistributionDetails',
            beforeSend: function() {
                $('#divLoading').addClass('show');
            },
            complete: function() {
                $('#divLoading').removeClass('show');
            },
            success: function(data) {
                data = JSON.parse(data).data;

                if (data.status == "failed") {
                    showSweetAlert('Unable to get distribution details at this time.', 'Whoops!', 'error');
                } else {
                    formatDistributionData(data);

                    $("#distributionDetailModal").modal("show");
                }
            }

        });
    });

    $('.refund').on('click', function(e) {
        var type = $(this).data("type");

        $("#refundTabs").removeClass("d-none");
        switch(type) {
            case "nullify":
                $("#nullTab").removeClass("d-none");
                $("#nullTab").find('.confirmRefund').data("type", type);

                $("#compTab").addClass("d-none");
                $("#partialTab").addClass("d-none");
                $("#completeTab").addClass("d-none");
                break;
            case "comp":
                $("#compTab").removeClass("d-none");
                $("#compTab").find('.confirmRefund').data("type", type);

                $("#nullTab").addClass("d-none");
                $("#partialTab").addClass("d-none");
                $("#completeTab").addClass("d-none");
                break;
            case "partial":
                $("#partialTab").removeClass("d-none");
                $("#partialTab").find('.confirmRefund').data("type", type);

                $("#nullTab").addClass("d-none");
                $("#compTab").addClass("d-none");
                $("#completeTab").addClass("d-none");
                break;
            case "complete":
                $("#completeTab").removeClass("d-none");
                $("#completeTab").find('.confirmRefund').data("type", type);

                $("#nullTab").addClass("d-none");
                $("#compTab").addClass("d-none");
                $("#partialTab").addClass("d-none");
                break;
        }

        $("#refundTabs").get(0).scrollIntoView();
    });

    $('.confirmRefund').on('click', function(e) {
        var id = $(this).data("id");
        var game_id = $(this).data("game-id");
        var note = $(this).closest(".col[id]").find("textarea[name='note']").val();

        switch($(this).data("type")) {
            case "nullify":
                refundNull(id, note);
                break;
            case "comp":
                refundComp(id, note);
                break;
            case "partial":
                refundPartial(id, note);
                break;
            case "complete":
                refundComplete(game_id, note);
                break;
        }
    });

    $('#distributionDetailModal .close').on('click', function(e) {
        $("#refundTabs").addClass("d-none");
    });

    function refundNull(id, note) {
        showSweetConfirm("The winner will receive no reward.", "Are you sure?", $icon = 'warning', function(confirmed) {
            if (!confirmed) {
                return false;
            } else {
                $.ajax({
                    type: "POST",
                    data: { id: id, note: note},
                    url: window.location.origin + '/cron/nullifyWinner',
                    beforeSend: function() {
                        $('#divLoading').addClass('show');
                    },
                    complete: function() {
                        $('#divLoading').removeClass('show');
                    },
                    success: function(data) {
                        data = JSON.parse(data).data;
        
                        if (data.status == "failed") {
                            showSweetAlert('Unable to issue nullify winner.', 'Whoops!', 'error');
                        } else {
                            showSweetAlert('Winner nullified for distribution: ' + id + '.', 'Success!', 'success');
        
                            setTimeout(function() {
                                $("#myAdvancedTable").DataTable().clearPipeline().draw(false);
                                $("#refundTabs").addClass("d-none");
                                $("#distributionDetailModal").modal("hide");
                             }, 300);
                        }
                    }
                });
            }
        });
    }

    function refundComp(id, note) {
        var compensateValue = ($("#compensateValue").val() != "") ? $("#compensateValue").val() : 0;

        showSweetConfirm("Are you sure you want to compensate the winner of this distribution with $" + compensateValue + "? It will be deducted from the creator's share.", "Are you sure?", $icon = 'warning', function(confirmed) {
            if (!confirmed) {
                return false;
            } else {
                $.ajax({
                    type: "POST",
                    data: { id: id, amount: compensateValue, note: note },
                    url: window.location.origin + '/cron/partialRefund',
                    beforeSend: function() {
                        $('#divLoading').addClass('show');
                    },
                    complete: function() {
                        $('#divLoading').removeClass('show');
                    },
                    success: function(data) {
                        data = JSON.parse(data).data;

                        if (data.status == "failed") {
                            showSweetAlert('Unable to issue compensate winner.', 'Whoops!', 'error');
                        } else {
                            showSweetAlert('Compensated winner for distribution: ' + id + '.', 'Success!', 'success');

                            setTimeout(function() {
                                $("#myAdvancedTable").DataTable().clearPipeline().draw(false);
                                $("#refundTabs").addClass("d-none");
                                $("#distributionDetailModal").modal("hide");
                            }, 300);
                        }
                    }
                });
            }
        });
    }

    function refundPartial(id, note) {
        showSweetConfirm("All payments given by this winner will be refunded and deducted from the total raised.", "Are you sure?", $icon = 'warning', function(confirmed) {
            if (!confirmed) {
                return false;
            } else {
                $.ajax({
                    type: "POST",
                    data: { id: id, note: note},
                    url: window.location.origin + '/cron/partialRefund',
                    beforeSend: function() {
                        $('#divLoading').addClass('show');
                    },
                    complete: function() {
                        $('#divLoading').removeClass('show');
                    },
                    success: function(data) {
                        data = JSON.parse(data).data;

                        if (data.status == "failed") {
                            showSweetAlert('Unable to issue partial refund.', 'Whoops!', 'error');
                        } else {
                            showSweetAlert('Partial refund issued for distribution: ' + id + '.', 'Success!', 'success');

                            setTimeout(function() {
                                $("#myAdvancedTable").DataTable().clearPipeline().draw(false);
                                $("#refundTabs").addClass("d-none");
                                $("#distributionDetailModal").modal("hide");
                            }, 300);
                        }
                    }
                });
            }
        });
    }

    function refundComplete(game_id, note) {
        showSweetConfirm("All payments associated with game " + game_id + " will be refunded.", "Are you sure?", $icon = 'warning', function(confirmed) {
            if (!confirmed) {
                return false;
            } else {
                $.ajax({
                    type: "POST",
                    data: { game_id: game_id, note: note},
                    url: window.location.origin + '/cron/completeRefund',
                    beforeSend: function() {
                        $('#divLoading').addClass('show');
                    },
                    complete: function() {
                        $('#divLoading').removeClass('show');
                    },
                    success: function(data) {
                        data = JSON.parse(data).data;

                        if (data.status == "failed") {
                            showSweetAlert('Unable to issue complete refund.', 'Whoops!', 'error');
                        } else {
                            showSweetAlert('Complete refund issued for game: ' + game_id + '.', 'Success!', 'success');

                            setTimeout(function() {
                                $("#myAdvancedTable").DataTable().clearPipeline().draw(false);
                                $("#refundTabs").addClass("d-none");
                                $("#distributionDetailModal").modal("hide");
                            }, 300);
                        }
                    }
                });
            }
        });
    }

    function formatDistributionData(data) {
        var distribution = data.distribution;
        var game = data.game;
        var creator = data.creator;
        var winner = data.winner;
        var shipping = data.shipping;

        appendData($("#distributionInfo thead tr"), $("#distributionInfo tbody"), distribution);
        appendData($("#gameInfo thead tr"), $("#gameInfo tbody"), game);
        appendData($("#creatorInfo thead tr"), $("#creatorInfo tbody"), creator);
        appendData($("#winnerInfo thead tr"), $("#winnerInfo tbody"), winner);
        appendData($("#shippingInfo thead tr"), $("#shippingInfo tbody"), shipping);

        configureRefundBtns(distribution.id, game.id, data.refund);
    }

    function appendData(tr, tbody, data) {
        var rowContent = "";
        var tbodyContent = "";

        for (const property in data) {
            rowContent += "<td>" + property + "</td>";
            tbodyContent += "<td>" + data[property] + "</td>";
        }

        $(tr).html(rowContent);
        $(tbody).html(tbodyContent);
    }

    function configureRefundBtns(id, game_id, refund) {
        for (const property in refund) {
            if (refund[property]) {
                $("#refundBtns a[data-type='" + property.toLowerCase() + "']").prop("disabled", false);
                $("#refundBtns a[data-type='" + property.toLowerCase() + "']").removeClass("disabled");
            } else {
                $("#refundBtns a[data-type='" + property.toLowerCase() + "']").prop("disabled", true);
                $("#refundBtns a[data-type='" + property.toLowerCase() + "']").addClass("disabled");
            }
        }

        $("#refundTabs .confirmRefund").each(function() {
            $(this).data("id", id);
            $(this).data("game-id", game_id);
        });
    }
    $('#shortURLTable').on('draw.dt', function (e) {
        var table = $("#shortURLTable").DataTable();
        
        table.rows().every(function(index, tableLoop, rowLoop) {
            var row = table.row(index);
            
            var node = row.node(); 
            $(node).removeClass("selected");
            $(node).addClass("select");
        });

        $("#selected_short").val("");
        $('#original-url').val("");
        $('#short-url').val("");

        $("#addShortUrl").attr("disabled", false);
        $("#addShortUrl").removeClass("d-none");

        $("#editShortUrl").attr("disabled", true);
        $("#editShortUrl").addClass("d-none");

        $("#deleteShortUrl").attr("disabled", true);
        $("#deleteShortUrl").addClass("d-none");
    });

    $('#shortURLTable tbody').on('click.dt', "tr", function (e) {
        var table = $("#shortURLTable").DataTable();
        var selectedRow = table.row( this );
        var data = selectedRow.data();

        if ($(this).hasClass("select")) {
            var id = $(this).data("id");;
            var url = data.url.match(/href="([^"]*)/);
            var short_url = data.short_url.match(/href="([^"]*)/);

            $("#selected_short").val((id != null) ? id : data.id);
            $('#original-url').val((url != null) ? url[1] : location.origin + "/" + data.url);
            $('#short-url').val((short_url != null) ? short_url[1] : location.origin + "/" + data.short_url);
        } else {
            $("#selected_short").val("");
            $('#original-url').val("");
            $('#short-url').val("");
        }
        
        table.rows().every(function(index, tableLoop, rowLoop) {
            var row = table.row(index);
            
            if (row.index() != selectedRow.index()) {
                var node = row.node(); 
                $(node).removeClass("selected");
                $(node).addClass("select");
            }
        });

        toggleSelectedRow($(this));
    });

    function toggleSelectedRow(row) {
        if (row.hasClass("select")) {
            $(row).addClass("selected");
            $(row).removeClass("select");

            $("#addShortUrl").attr("disabled", true);
            $("#addShortUrl").addClass("d-none");

            $("#editShortUrl").attr("disabled", false);
            $("#editShortUrl").removeClass("d-none");

            $("#deleteShortUrl").attr("disabled", false);
            $("#deleteShortUrl").removeClass("d-none");
        } else {
            $(row).addClass("select");
            $(row).removeClass("selected");

            $("#addShortUrl").attr("disabled", false);
            $("#addShortUrl").removeClass("d-none");

            $("#editShortUrl").attr("disabled", true);
            $("#editShortUrl").addClass("d-none");

            $("#deleteShortUrl").attr("disabled", true);
            $("#deleteShortUrl").addClass("d-none");
        }
    }

    $("#addShortUrl").on("click", function() {
        var original_url = $('#original-url').val();
        var short_url = $('#short-url').val();
        
        if (original_url.includes(window.location.origin)) {
            original_url = original_url.split("/").slice(3).join("/");
        }

        if (short_url.includes(window.location.origin)) {
            short_url = short_url.split("/").slice(3).join("/");
        }

        $.ajax({
            type: "POST",
            data: { url: original_url, short_url: short_url },
            url: window.location.origin + "/admin/addShortUrl",
            beforeSend: function() {
                $("#addShortUrl").attr("disabled", true);
            },
            complete: function () {
                $("#addShortUrl").attr("disabled", false);
            },
            success: function(response) {
                response = JSON.parse(response);

                if (response.status == "success") {
                    showSweetAlert("Short url created!", "Great!", 'success');
                    $("#short_url-form").trigger("reset");
                    $("#shortURLTable").DataTable().clearPipeline(false).draw();
                } else {
                    showSweetAlert("Something went wrong, please try again", "Oops!", 'error');
                }
            }
        });
    });

    $("#editShortUrl").on("click", function(e) {
        var id = $("#selected_short").val();
        var original_url = $('#original-url').val();
        var short_url = $('#short-url').val();
        
        if (original_url.includes(window.location.origin)) {
            original_url = original_url.split("/").slice(3).join("/");
        }

        if (short_url.includes(window.location.origin)) {
            short_url = short_url.split("/").slice(3).join("/");
        }

        $.ajax({
            type: "POST",
            data: { id: id, url: original_url, short_url: short_url },
            url: window.location.origin + "/admin/editShortUrl",
            beforeSend: function() {
                $("#editShortUrl").attr("disabled", true);
                $("#deleteShortUrl").attr("disabled", true);
            },
            complete: function () {
                $("#editShortUrl").attr("disabled", false);
                $("#deleteShortUrl").attr("disabled", false);
            },
            success: function(response) {
                response = JSON.parse(response);

                if (response.status == "success") {
                    showSweetAlert("Short url edited!", "Great!", 'success');
                    $("#short_url-form").trigger("reset");
                    $("#shortURLTable").DataTable().clearPipeline(false).draw();
                } else {
                    showSweetAlert("Something went wrong, please try again", "Oops!", 'error');
                }
            }
        });
    });

    $("#deleteShortUrl").on("click", function(e) {
        var short_url = $("#short-url").val();
        showSweetConfirm("Are you sure you want to delete: " + short_url +"?", "Attention", $icon = 'info', function(confirmed) {
            if (!confirmed) {
                e.preventDefault();
            } else {
                var id = $("#selected_short").val();
                $.ajax({
                    type: "POST",
                    data: { id: id },
                    url: window.location.origin + "/admin/deleteShortUrl",
                    beforeSend: function() {
                        $("#editShortUrl").attr("disabled", true);
                        $("#deleteShortUrl").attr("disabled", true);
                    },
                    complete: function () {
                        $("#editShortUrl").attr("disabled", false);
                        $("#deleteShortUrl").attr("disabled", false);
                    },
                    success: function(response) {
                        response = JSON.parse(response);
        
                        if (response.status == "success") {
                            showSweetAlert("Short url deleted!", "Great!", 'success');
                            $("#short_url-form").trigger("reset");
                            $("#shortURLTable").DataTable().clearPipeline(false).draw();
                        } else {
                            showSweetAlert("Something went wrong, please try again", "Oops!", 'error');
                        }
                    }
                });
            }
        });
    });

    // Update user permissions
    $('#myAdvancedTable tbody').on('change', '#roleSelect', function() {
        var user_id = $(this).closest('tr').find('.password_user').attr("data-id");
        var selectedUsername = this.options[this.selectedIndex].getAttribute('user-data');
        var selectedRole = this.options[this.selectedIndex].innerText;
        var selectedPermission = this.options[this.selectedIndex].value;
        
        showSweetConfirm('', 'Are you sure you want to give "' + selectedRole + '" role to ' + selectedUsername + ' ?', 
            $icon='warning', function(confirmed) {
            if (confirmed) {
                $.ajax({
                    type: "POST",
                    data: {
                        id: user_id,
                        role: selectedRole,
                        permission: selectedPermission
                    },
                    url: window.location.origin + '/admin/update_role',
                    dataType: "JSON",
                    success: function(e) {
                        if (e.done == 1) {
                            showSweetAlert("Role Updated successfully!", "Success", "success");
                            location.reload();
                        } else {
                            showSweetAlert("Role can not be changed at this time!", "Whoops!", "error");
                        }
                    }
        
                });
            }
        });
    });
});