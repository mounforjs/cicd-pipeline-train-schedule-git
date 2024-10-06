var current_url = window.location.href.split('/')[5];
var fundraiserStatus = current_url ? current_url : 'all';

function addNewTinyMCE(id, content, version=0) {		
    tinyMCEInitialize(id, content, undefined, undefined, undefined, version);	
}

$(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();

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

    $('#listoffundraisers').selectize({
        maxItems: 1,
        allowEmptyOption: true,
        closeAfterSelect: true,
        loadingClass: 'loading',
        onChange: function (value, isOnInitialize) {
            if( window.location.pathname.split('/')[1] == 'fundraisers' &&  window.location.pathname.split('/')[2] == 'show'){
                getBeneficiaries(value);
            } else {    
                $.ajax({
                    type: "POST",
                    data: {
                        slug: value
                    },
                    url: window.location.origin + '/fundraisers/getDefaultFundraiseDetailsOnChange',
                    beforeSend: function () {
                        $(".loader").removeClass("d-none");
                    },
                    complete: function() {
                        $(".loader").addClass("d-none");
                    },
                    success: function (result) {
                        result = JSON.parse(result);
                        $('#selectedFundraiserDetails').show();
                        $('#defaultName').text(result.name);
                        $('#defautIcon').removeClass().addClass(result.icon);
                        $('#defaultCategory').removeClass('project').removeClass('cause').removeClass('education').removeClass('charity').addClass(result.fundraise_type);
                        $('#defaultType').text(result.fundraise_type);
                        $('#defaultWebsite').attr('href', result.charity_url).text(result.charity_url);
                        addtoggleDescription($("#defaultDescription"), result.Description);
                        $('#fundraiserImagePreview').attr('src', result.Image.image);
                        $('#fundraiserImagePreview')[0].onerror = function() {
                            imgError(this, result.Image.fallback);
                        };
                        $('#fundraiserIcon').removeClass().addClass(result.icon);
                        $('.fundcatdefault').removeClass('project').removeClass('cause').removeClass('education').removeClass('charity').addClass(result.fundraise_type);
                        $('#defaultType').text(result.fundraise_type);

                        $(".payDonateBtn").attr("fundraiser-slug", result.slug);
                        $(".payDonateBtn").attr("fundraiser-name", result.name);

                        $("#view_games").data("slug", result.slug);

                        var original = $("#default_fundraiser").val();
                        $('.makeDefaultCardbtn:first').attr('data-slug', result.slug);
                        if ((result.def && result.def == 1) || result.slug == original) {
                            $('.makeDefaultCardbtn:first').addClass("d-none");
                        } else {
                            $('.makeDefaultCardbtn:first').removeClass("d-none");
                        }
                        
                        $("#selectedFundraiser").val(result.slug);
                        $("#isApproved").val(result.approved);
                        $("#isApproved").trigger("change");
                        
                        if (result.raised > 250) {
                            var amtRaised = '<div class="raised-text defaultraised"><span>RAISED: </span> $' + parseFloat(result.raised).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",") + '</div>';
                            $('.donationgroup').html(amtRaised);
                        } else {
                            $('.donationgroup').html("");
                        }
                        $('#listofcharities').val(result.slug);
                        $('#defaultFundraiseCheck').val(result.slug);
                    }
                });
            }
        }
    });

    $(document).on("click", "#view_games", function(e) {
        e.stopPropagation();

        window.open(window.location.origin + '/games/show/play/?beneficiary=' + $(this).data("slug"));
    });

    $(document).on("click", ".makeDefaultCardbtn", function () {

        $.ajax({
            type: "POST",
            url: window.location.origin + "/fundraisers/makeDefaultFundraiser",
            data: {
                slug: $(this).attr('data-slug'),
            },
            beforeSend: function () {
                $('#divLoading').addClass('show');
            },
            complete: function () {
                $('#divLoading').removeClass('show');
            },
            success: function (response) {
                if (response == 1) {
                    showSweetAlert('Your default fundraiser has been updated', 'Great');
                } else {
                    showSweetAlert('Your default fundraiser could not be updated', 'Oops', 'error');
                }
                window.setTimeout(function () {
                    window.location.reload();
                }, 2000);

            }
        });
    });

    var offset = 0;

    var oldType = $('.fundraiseTypeRadio:checked').val();
    var oldSubType = $('.fundraise_type:checked').val();
    var oldSlug = '';

    function getBeneficiaries(slug = '') {
        var type = $('.fundraiseTypeRadio:checked').val();
        var subType = $('.fundraise_type:checked').val();
        if (oldType == type && oldSubType == subType && oldSlug == slug) {
            offset = offset + 8;
        } else {
            offset = 0;
        }

        if (oldType !== '' && oldType != type) {
            oldType = type;
        }

        if (oldSlug != slug) {
            oldSlug = slug;
        }
        
        if (oldSubType !== undefined && oldSubType != subType) {
            oldSubType = subType;
        } else {
            if (oldSubType === undefined && subType === undefined) {
                oldSubType = subType = 'all';
            }
        }

        $.ajax({
            type: "POST",
            data: {
                type: type,
                sub_type: subType,
                slug: slug,
                offset: offset
            },
            url: window.location.origin + '/fundraisers/getBeneficiaries',
            beforeSend: function() {
                $('#divLoading').addClass('show');
            },
            complete: function () {
                $('#divLoading').removeClass('show');
            },
            success: function (result) {
                result = JSON.parse(result);

                var appendData = "";
                $.each(result.data, function (idx, val) {
                    var image = val.Image.image;
                    var fallback = val.Image.fallback;

                    str =
                        '<div class="col-lg-3 col-md-6 col-12 card-group mb-3">' +
                        '<div class="card bg-light fundraisecard">' +
                        '<a href="' + location.origin + '/fundraisers/show/all/' + val.slug + '"><div class="card-header mainimg">' +
                        "<img src='" + image  + `' onerror="imgError(this, '${fallback}')">` + 
                        '</div></a>';


                    str += '<a class="card-header card-title" href="' + location.origin + '/fundraisers/show/all/' + val.slug + '">' +
                        '<h5>' + val.name + '</h5>' +
                        '</a>';

                    str += '<div class="myfundcategory ' + val.fundraise_type + '">' +
                        '<i class="' + val.icon + '"></i> ' + val.fundraise_type +
                        '</div>';

                    if (val.approved != 'Yes') {
                        str += '<div class="card-header pending"><i class="fas fa-user-clock"></i> PENDING APPROVAL</div>';
                    }

                    var amtRaised = '';
                    if (val.raised != null && val.raised != undefined && val.raised != 0) {
                        var raised = parseInt(val.raised.replace(/,/g, ''));

                        if (raised > 250) {
                            var amtRaised = '<div class="raised-text"><span>RAISED: </span> $' + val.raised + '</div>';
                        }
                    }

                    if (val.approved == 'Yes') {
                        str += '<div class="donate p-1 show">' +
                            '<div class="donationgroup">' + amtRaised + '</div>' +
							'<div class="row p-0 m-sm-1"><div class="col-6 pl-2 pr-1">' + 
							'<a class="btn orange btn-block small" href="' + window.location.origin + '/games/show/play/?beneficiary=' + val.slug +'"><i class="fas fa-eye"></i> GAMES</a>' + '</div><div class="col-6 pr-2 pl-1">' + 
							'<button type="button" class="btn orange btn-block small payDonateBtn" fundraiser-name="'+ val.name +'" fundraiser-slug="'+ val.slug +'"><i class="fas fa-donate"></i> ' +
                            'DONATE</button>' +
                            '</div></div></div>';
                    }

                    //when supported make default & remove
                    // created 
                    // all make default
                    str += '<div class="actions p-1 text-center">';
                    if (result.usertype != null) {
                        if (val.slug != result.default_fundraiser && result.usertype != 2) {
                            str += '<a data-slug="' + val.slug + '" class="btn blue small makeDefaultCardbtn">MAKE DEFAULT</a>';
                        } else if (result.usertype != 2) {
                            str += '<a class="btn green small">DEFAULT</a>';
                        }
                    }

                    if (type == 'supported') {
                        str += '<a class="minussupport deleteFundraiserBtn btn blue small" method-type= "removeSupportedFundraiser" data-slug = "' + val.slug + '"><i class="fa fa-minus-circle" aria-hidden="true"></i> REMOVE</a>';
                    } else if (result.usertype == '2') {
                        str += '<a class="editFundraiserBtn btn blue small" data-slug="' + val.slug + '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';

                        if (val.fundraiserPendingStatus == '0') {
                            str += ' <span class="badge badge-info">EDIT Requested</span>';
                        }
                    }

                    if (type != 'supported' && type != 'all' && result.usertype != '2' && result.usertype != null) {
                        if (val.fundraiserPendingStatus != '0') {
                            str += '<a class="editFundraiserBtn btn blue small" data-slug="' + val.slug + '"><i class="fa fa-pencil" aria-hidden="true"></i></a>';
                        } else {
                            if (result.usertype != 2) {
                                str += '<span class="badge badge-info">Request Pending</span>';
                            } else {
                                str += '<a class="editFundraiserBtn" data-slug="' + val.slug + '">EDIT Requested <i class="fas fa-chevron-circle-right"></i></a>';
                            }
                        }

                        if (val.approved != 'Yes' && val.slug != result.default_fundraiser) {
                            str += '<a class="minussupport deleteFundraiserBtn btn blue small" data-slug="' + val.slug + '"><i class="fa fa-trash" aria-hidden="true"></i></a>';
                        }
                    }

                    str += '</div></div></div></div>';

                    console.log(str);

                    appendData += str;
                });

                if (result.data.length == 0) {
                    $('#loadMoreButton').addClass("d-none");
                    if (offset == 0) {
                        $("#repeatFundraise").empty();
                        $('#noRecordsFound').removeClass("d-none");
                    } else {
                        $('#noMoreRecords').removeClass("d-none");
                        setTimeout(() => {
                            $('#noMoreRecords').addClass("d-none");
                        }, 5000)
                    }
                } else {
                    if (result.data.length == 8) {
                        $('#loadMoreButton').removeClass("d-none");
                    } else {
                        $('#loadMoreButton').addClass("d-none");
                    }

                    if (offset == 0) {
                        $('#noRecordsFound').addClass("d-none");
                        $('#repeatFundraise').empty();
                    }
                }

                $('#repeatFundraise').append(appendData);
                populateBeneficiarySearch(slug, result.search);
            }
        });
    }

    //change searchbar list of beneficiaries
    function getBeneficiaryList() {
        var subType = $('.fundraise_type:checked').val();

        $.ajax({
            type: "GET",
            data: { 'sub_type' : subType },
            url: window.location.origin + '/fundraisers/getBeneficiaryList/',
            success: function (result) {
                var data = JSON.parse(result);
                populateBeneficiarySearch(null, data);
            }
        });
    }

    const populateBeneficiarySearch = (slug, data) => {
        var optionList = [];

        $.each(data, function (idx, val) {
            $child = {
                text: val.name,
                value: val.slug
            };

            optionList.push($child);
        });

        var selectize = $("#listoffundraisers")[0].selectize;
        selectize.clear(true);
        selectize.clearOptions(true);
        selectize.load(function (callback) {
            callback(optionList);
            if (slug) {
                selectize.setValue(slug, true);
            }
        });
    }

    $("#create_beneficiary, #cancel-new-beneficiary").on("click", function() {
        var modal = $('#editFundraiserModal');
        if (modal.length > 0) {
            modal.modal("hide");
            return;
        }

        if ($('#fundraiserbox').hasClass("d-none")) {
            $('#fundraiserbox').removeClass('d-none');
            $('html, body').animate({scrollTop: '+=350px'}, 800);
        } else {
            $('#fundraiserbox').addClass('d-none');
            $('#fundraise-form').addClass('d-none');
            $("#authorize-charity").addClass('d-none');
            $('#fundraise-form').find('input:text').val('');
            $('#fundraise-form').find('input[type="tel"]').val('');
            $('#fundraise-form').find('textarea').val('');
            $('#rchars').text('500');
            $('#fundraiserbox').find('input:radio').prop("checked", false);
            $('#fundraise-form').find('input:checkbox').prop("checked", false);

            $('#addFundraiserImagePreview').attr('src', '');
        }
    });

    $('#loadMoreButton').click(function () {
        getBeneficiaries();
    });

    $('.fundraiseTypeRadio').click(function () {
        $('.fundraiseTypeRadio').attr('checked', false);
        $(this).attr('checked', true);
        $(this).prop('checked', true);

        getBeneficiaries();
    });

    $(document).on('change', '.fundraise_type', function () {
        $('.fundraise_type').prop('checked', false);
        $(this).prop('checked', true);

        if (window.location.pathname.split('/')[1] == 'fundraisers' && window.location.pathname.split('/')[2] == 'show') {
            getBeneficiaries();
        } else {
            getBeneficiaryList();
        }
    });

    $(document).ready(function () {
        var defaultDesc = $("#defaultDescription");
        if (defaultDesc.length > 0) {
            var content = defaultDesc.html();
            addtoggleDescription(defaultDesc, content);
        }
    });

    function addtoggleDescription(elem, content) {
        var showChar = 250;
        var ellipsestext = "...";

        if (content.length > showChar) {
            var c = content.substr(0, showChar);
            var h = content.substr(showChar, content.length - showChar);

            var html = c + '<span class="moreellipses">' + ellipsestext + '&nbsp;</span><span class="morecontent d-none">' + h + '</span>';

            $(elem).html(html);
            $(elem).append('<a class="morelink">Show more</a></span>');
        } else {
            $(elem).html(content);
        }
    }

    $(document).on("click", ".morelink", function () {
        var more = $(this).closest(".more");
        var ellipsis = more.find(".moreellipses");
        var content = more.find(".morecontent");
        var btn = more.find(".morelink");

        if (!content.is(":visible")) {
            ellipsis.addClass("d-none");
            content.removeClass("d-none");
            btn.text("Show less");
        } else {
            ellipsis.removeClass("d-none");
            content.addClass("d-none");
            btn.text("Show more");
        }
    });

    function openNewFundraiser(data=null, isEdit = 0) {
        $('#editFundraiserModal').modal('show');

        if (isEdit == 0) {
            $('#editFundraiserModal .modal-title').text('Add Beneficiary');
            $('#fundraise-form').addClass('d-none');
            $("#fund-form").trigger("reset");
            $('#addFundraiserImagePreview').attr('src', '');
            $('#addFundraiserImagePreview').trigger("error");
            $('#fundraiserEditReason').addClass('d-none');
            $('.avatar-edit').removeClass('d-none');
            $("#fund-form :input").removeAttr("readonly");
            $(".abc").removeAttr("isEdit");
            $("#authorize-charity").addClass('d-none');
            $('input[name="is_non"]').removeAttr('isEdit');
            $('input:checkbox[name=fundraise_default_authorize]').removeAttr('isEdit');
            $('#fundraise_detail_slug').attr('data-slug', '');
        } else {
            $('#editFundraiserModal .modal-title').text('Edit Beneficiary');
            $('#fundraise-form').removeClass('d-none');
        }

        if (data !== null) {
            $("#fund-form :input").removeAttr("readonly");
            $("#fund-form :input").removeAttr("isedit");

            var fType = data.fundraise_type;
            switch (fType) {
                case "charity":
                    $('#charity-search').click();
                    $('input:radio[name=is_non][value="1"]').click();
                    break;
                case "project":
                    $('#project-search').click();
                    break;
                case "education":
                    $('#education-search').click();
                    break;
                default:
                    $('#cause-search').click();
            }
    
            $('#form_charity_name').val(data.name);
            $('#form_charity_address').val(data.Address);
            $('#charity_url').val(data.charity_url);
            $('#form_charity_desc').val(data.Description);
            $('#form_charity_phone').val(data.Phonenumber);
            $('#form_charity_contact').val(data.Contact_personnel);
            $('#form_charity_tax').val(data.Tax_ID);
            $('.gIconPreview-img').css('background-image', 'none');
            $('#addFundraiserImagePreview').attr('src', data.Image.image.replace(/\s+/g, ''));
            $('#fundraise_detail_slug').attr('data-slug', data.slug);
            $('#fund-submit').addClass('cancel');
            $('#authorize-charity').attr("readonly", 'readonly');
            $('input:radio[name=is_non]').attr("isEdit", true);
            $('input:checkbox[name=fundraise_default_authorize]').attr('isEdit', true);
            if (data.isDefault === data.slug) {
                $('input:checkbox[name=fundraise_default_authorize]').attr('checked', 'checked');
            } else {
                $('input:checkbox[name=fundraise_default_authorize]').removeAttr('checked');
            }
    
            // not to do when admin logged in
            if (data.isAdmin == 0) {
                $(".abc").attr("isEdit", true);
                $('input').css('color', 'black');
                $("#fund-form :input").attr("readonly", 'readonly');
                $('#fundraiserEditReason').removeClass('d-none');
                $('.avatar-edit').addClass('d-none');
                $('#editReason').removeAttr("disabled");
                $('#editReason').removeAttr("readonly");
                $('#editReason').val(data.reason);
                
            }
    
            if (data.isAdmin == 1) {
                $('#editReason').val(data.reason);
            }
            // not to do when admin logged in
        }
    }

    $('.new').click(function (e) {
        openNewFundraiser();
    });

    $('#charity-search').click(function (e) {
        if ($(this).find('.abc').attr('isEdit')) {
            e.preventDefault();
            return;
        }
        $("#authorize-charity").removeClass('d-none');
        $('input[name="is_non"]').click();

    });

    $('input[name="is_non"]').click(function (e) {
        if ($(this).attr('isEdit')) {
            e.preventDefault();
            return;
        }


        if (this.value == "0") {
            $("#authorize").addClass('d-none');
            $("#authorize-no").removeClass('d-none');
            $("#fundraise-form").addClass('d-none');
            $("#authorize-no").text("Only decision makers can add charities to our system. Please share this with a decision maker or you can fund a project, cause, or support education!");
        } else {
            $("#authorize").removeClass('d-none');
            $("#authorize-no").addClass('d-none');
            $("#fundraise-form").removeClass('d-none');
            $("#charity-parameters").removeClass('d-none');
        }
    });

    $('input:checkbox[name=fundraise_default_authorize]').click(function (e) {
        if ($(this).attr('isEdit')) {
            e.preventDefault();
            return;
        }
    })

    $('#project-search, #cause-search, #education-search').click(function (e) {
        if ($(this).find('.abc').attr('isEdit')) {
            e.preventDefault();
            return;
        }
        $("#authorize-charity").addClass('d-none');
        $("#fundraise-form").removeClass('d-none');
        $("#charity-parameters").addClass('d-none');
    });

    searchVisible = 0;
    transparent = true;
    $validator_fund = $('#fund-form').validate({

        onkeyup: function (element, event) {
            if (event.which === 9 && this.elementValue(element) === "") {
                return;
            } else {
                this.element(element);
            }
        },
        rules: {

            form_charity_name: {
                required: true,
                remote: {
                    url: window.location.origin + '/account/admincharity/admincharityname/?slug=' + $('#fundraise_detail_slug').attr('value'),
                    type: "post",
                    data: {
                        charity_name: function () {
                            return $('#form_charity_name').val();
                        }
                    }
                }
            },
            description: {
                required: true,
            },
            form_charity_tax: {
                required: true,
            },
            fundraise_non_profit: {
                required: true
            },

            is_non: {
                required: {
                    depends: function (element) {
                        if ($("#fundraise-entity-id").val() != "") { return false; }
                        else { return true; }
                    }
                },
            }
        },
        messages: {
            fundraise_type: {
                required: "Select Beneficiary Type!"
            },
            form_charity_name: {
                required: "Enter Beneficiary name!",
                remote: "Beneficiary already exists."
            },

        },


        submitHandler: function (form) {
            var form = $('#fund-form')[0];
            var info = new FormData(form);
            if ($('#fundraise_detail_slug').data("slug") !== undefined) {
                info.append('slug', $('#fundraise_detail_slug').data("slug"));
            }

            if (window.location.href.split('/')[3] == 'getcharity') {
                var url = '/account/admincharity/update_admin_charity';
            }
            else {
                var url = '/fundraisers/add_edit_fundraiser';
            }
            
            $.ajax({
                url: window.location.origin + url,
                type: 'POST',
                data: info,
                processData: false,  // tell jQuery not to process the data
                contentType: false,   // tell jQuery not to set contentType
                beforeSend: function () {
                    $('#divLoading').addClass('show');
                    $('#divLoading').css('z-index', '9999');
                },
                complete: function () {
                    $('#divLoading').removeClass('show');
                },
                success: function (response) {
                    var data = JSON.parse(response);
                    if (data.status !== "success") {
                        showSweetAlert(data.msg, 'Whoops!', 'error');
                    } else {
                        $("#editFundraiserModal").modal("hide");
                        showSweetAlert(data.msg, 'Success!');
                    }
                    
                    window.setTimeout(function () {
                        window.location.reload();
                    }, 2000);

                    return false;
                }
            });
        }
    });

    // custom method for url validation with or without http://
    $.validator.addMethod("cus_url", function (value, element) {
        if (value.substr(0, 7) != 'http://') {
            value = 'http://' + value;
        }
        if (value.substr(value.length - 1, 1) != '/') {
            value = value + '/';
        }
        return this.optional(element) || /^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i.test(value);
    }, "Please enter a valid URL.");

    $.validator.addMethod('phoneUS', function (phone_number, element) {
        phone_number = phone_number.replace(/\s+/g, '');
        return this.optional(element) || phone_number.length > 9 &&
            phone_number.match(/^(1-?)?(\([2-9]\d{2}\)|[2-9]\d{2})-?[2-9]\d{2}-?\d{4}$/);
    }, 'Please enter a valid phone number.');

    $(document).on("click", ".deleteFundraiserBtn", function () {
        var slug = $(this).attr('data-slug');
        var methodType = $(this).attr('method-type');
        if (methodType == 'removeSupportedFundraiser') {
            url = window.location.origin + '/fundraisers/removeSupportedFundraiser';
        }
        else {
            url = window.location.origin + '/fundraisers/deleteCreatedFundraiser';
        }
        $.ajax({
            type: "POST",
            data: { slug: slug },
            url: url,
            beforeSend: function () {
                $('#divLoading').addClass('show');
            },
            complete: function () {
                $('#divLoading').removeClass('show');
            },
            success: function (result) {
                if (result == 1) {
                    if (methodType != 'removeSupportedFundraiser') {
                        showSweetAlert('The fundraiser has been deleted', 'Great');
                    }
                    else {
                        showSweetAlert('The fundraiser has been removed from your supported fundraisers', 'Great');
                    }
                } else {
                    showSweetAlert('The fundraiser could not be deleted', 'Whoops!', 'error');
                }
                window.setTimeout(function () {
                    window.location.reload();
                }, 2000);
            }
        });
    });

    $(document).on("click", ".editFundraiserBtn", function () {
        var slug = $(this).attr('data-slug');

        $.ajax({
            type: "POST",
            data: { slug: slug },
            url: window.location.origin + '/fundraisers/getEditedFundraiserDetails',
            beforeSend: function () {
                $('#divLoading').addClass('show');
            },
            complete: function () {
                $('#divLoading').removeClass('show');
            },
            success: function (result) {
                result = JSON.parse(result);

                if (result.status === 'failed') {
                    showSweetAlert('You already have a pending request for this fundraiser.', 'Whoops!', 'error');
                    return;
                }
                
                openNewFundraiser(result, 1);
            }
        });
    });

    if ($('#openFundRaiserForEdit').data('slug') > 0) {
        $('#openFundRaiserForEdit').html('<a class="editFundraiserBtn d-none" data-slug="' + $('#openFundRaiserForEdit').data('slug') + '"></a>');
        $('a.editFundraiserBtn[data-slug="' + $('#openFundRaiserForEdit').data('slug') + '"]').click();
        $('#openFundRaiserForEdit').remove();
    }
    
    $(document).on("click", ".paypalDonateBtnDetailPage", function () {
        $(this).val($(this).attr('fundraiser-name'));
        $("#paypalDonateForm").submit(); // Submit the form
    
    });

    $("#editFundraiserModal").on('hidden.bs.modal', function () {
        $("#editFundraiserModal").modal('hide');
        $("#fund-form").trigger('reset');
    });
});

function capitalize(string) {
    return [].map.call(string, (char, i) => i ? char : char.toUpperCase()).join('')
}

