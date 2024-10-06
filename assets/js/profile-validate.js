function addNewTinyMCE(id, content, version=0, maxChar) {		
  tinyMCEInitialize(id, content, maxChar, undefined, undefined, version);	
}

$(document).ready(function () {
  addNewTinyMCE("user_description", $("#user_description").text(), 1, 500)
  addNewTinyMCE("webapp_feedback", $("#webapp_feedback").text(), 1, 500)
  addNewTinyMCE("lifetime_goals", $("#lifetime_goals").text(), 1, 500)

  $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {
    var tab = $(e.target).attr('href');
    (tab == "#about-info") ? $('#update-btn').removeClass('d-none') : $('#update-btn').addClass('d-none');
    toggleContentTab(e.target);
  });

  /**** JQuery *******/
  $('body').on('click', '.btnNext', function () {
    var next = $('.nav-tabs > .active').next('li');
    if (next.length) {
      next.find('a').trigger('click');
    } else {
      $('#tabs a:first').tab('show');
    }
  });
  $('body').on('click', '.btnPrevious', function () {
    var prev = $('.nav-tabs > .active').prev('li')
    if (prev.length) {
      prev.find('a').trigger('click');
    } else {
      $('#tabs a:last').tab('show');
    }
  });


});

function toggleContentTab(target) {
  $(".tab-content:visible").not($(target).attr('href')).addClass('d-none');

  var parent = $($(target).attr('href')).parent();
  $(parent).removeClass('d-none');
}

$(document).ready(function () {
  var radioValue = $("input[name='internship']:checked").val();
  if (radioValue == "0") {
    $("#intern-times").addClass('d-none');
  }
  else {
    $("#intern-times").removeClass('d-none');
  }
  $("input[type='radio']").click(function () {
    var radioValue = $("input[name='internship']:checked").val();
    if (radioValue == "0") {
      $("#intern-times").addClass('d-none');
    }
    else {
      $("#intern-times").removeClass('d-none');
    }
  });



  function challengeDivCheck() {
    var radioValueFirst = $("input[name='is_first']:checked").val();
    if (radioValueFirst == "0") {
      $("#challenges").addClass('d-none');
      $('#teamname').tagsinput('removeAll');
      $('#teamnameftc').tagsinput('removeAll');
      $('#teamnamefll').tagsinput('removeAll');
      $('#teamnamejrfll').tagsinput('removeAll');
    }
    else {
      $("#challenges").removeClass('d-none');
    }
  }
  challengeDivCheck();
  $("input[type='radio']").click(function () {
    challengeDivCheck();

  });
});


$(document).ready(function () {
  $('input[name="pathway[]"').change(function () {
    if ($(this).is(":checked")) {
      if (this.value == "High School" || this.value == "College" || this.value == "Trade School") {
        $("#graduations").css('display', 'block');
      }
    } else if ($(this).is(":not(:checked)")) {
      var favorite = [];
      var isShow = false;
      $.each($("input[name='pathway[]']:checked"), function () {
        // alert(favorite.push($(this).val()));
        if ($(this).val() == "High School" || $(this).val() == "College" || $(this).val() == "Trade School") {
          isShow = true;
        }
      });
      if (isShow == true) {
        $("#graduations").css('display', 'block');
      } else {
        $("#graduations").css('display', 'none');
      }
    }
  });
});
// $(document).ready(function () {
//   $('input[name="internship"').change(function () {
//     if ($(this).is(":checked")) {
//       if (this.value == 1) {
//         $("#intern-times").css('display', 'block');
//       }
//     } else if ($(this).is(":not(:checked)")) {
//       var isShow = false;
//       if ($(this).val() == 1) {
//           isShow = true;
//       }
//       if (isShow == true) {
//         $("#intern-times").css('display', 'block');
//       } else {
//         $("#intern-times").css('display', 'none');
//       }
//     }
//   });
// });

// method to prevent browser default popup
$(window).on('beforeunload', function(event) {
  event.stopImmediatePropagation();
  $('#profile-form')[0].reset();
});

$(document).ready(function () {
  searchVisible = 0;
  transparent = true;
  if ($("#profile-form").length) {
    var $validator = $('#profile-form').validate({
      onkeyup: function (element, event) {
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
        country: {
          required: true
        },

        password: {
          required: false,
        },
        username: {
          required: true,
          minlength: 3,
          remote: {
            url: window.location.origin + "/user/check_username",
            type: "POST",
          }
        },
        updateEmail: {
          required: true,
          email: true,
        },
      }
    });
  }

  $("section").on('click', '#update-btn', function (events) {
    events.preventDefault();
    window.onbeforeunload = null;
    
    var form = $('#profile-form')[0];
    var $validator = $('#profile-form').valid();
    if (!$validator) {
      $validator.focusInvalid();
      return false;
    }

    var info = new FormData(form);
    $.ajax({
      url: '/user/edit_profile',
      type: 'POST',
      data: info,
      processData: false, // tell jQuery not to process the data
      contentType: false, // tell jQuery not to set contentType
      beforeSend: function () {
        $('#divLoading').addClass('show');
      },
      complete: function () {
        $('#divLoading').removeClass('show');
      },
      success: function (data) {
        data = JSON.parse(data);

        if (data.status == "success") {
          showSweetAlert('Your profile has been updated', 'Success!');
          window.setTimeout(function () {
            window.location.reload();
          }, 2000);
        } else {
          showSweetAlert(data.msg, 'Whoops!', 'error');
          window.setTimeout(function () {
            window.location.reload();
          }, 2000);
        }
      }
    });
  });

});

$('#fundraise-form').addClass('d-none');
$('#fundraiserbox').addClass('d-none');

$(document).ready(function () {

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
    debug: true,
    rules: {

      name: {
        required: true,
        remote: {
          url: 'account/admincharity/admincharityname/?fund_id=' + $('#fundraise_detail_id').attr('value'),
          type: "post",
          data: {
            charity_name: function () {
              return $('#form_charity_name').val();
            }
          }
        }
      },
      // address: {
      //   required: true,
      // },
      // contact: {
      //   required: true,
      // },
      // phone: {
      //   required: true,
      //   phoneUS: true
      // },
      // charity_url: {
      //   required: true,
      //   validUrl: true
      // },
      description: {
        required: true,
      },
      tax_id: {
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
      name: {
        required: "Enter Beneficiary name!",
        remote: "Beneficiary already exists."
      },

    },


    submitHandler: function (form) {

      var form = $('#fund-form')[0];

      var info = new FormData(form);
      if (window.location.href.split('/')[3] == 'getcharity') {
        var url = '/account/admincharity/update_admin_charity';
        var success_mess = 'Fundraise Updated!';
      } else if (window.location.href.split('/')[3] == 'profile') {
        var url = '/fundraisers/add_edit_fundraiser';
        var success_mess = 'Fundraise Added!';
      } else {
        var url = '/account/admincharity/addadmin_charity';
        var success_mess = 'Fundraise Added!';
      }

      console.log('In submit 325');
      $.ajax({
        url: window.location.origin + url,
        type: 'POST',
        data: info,
        processData: false,  // tell jQuery not to process the data
        contentType: false,   // tell jQuery not to set contentType
        beforeSend: function () {
          $('#divLoading').addClass('show');
        },
        complete: function () {
          $('#divLoading').removeClass('show');
        },
        success: function (dataa) {
          if (dataa == 2) {

            showSweetAlert('Oops...', 'Fundraise Name already Exists!', 'error');

          } else if (dataa == 3) {

            showSweetAlert('Oops...', 'Could not Update your default Fundraise!', 'error');

          } else if (dataa == 4) {

            showSweetAlert('Oops...', 'Could not add new Fundraise!', 'error');

          } else {
            showSweetAlert(success_mess, 'Great');
          }
          $('#fundraiserbox').addClass('d-none');
          $("#listofcharities optgroup").remove();
          $("#listofcharities option").remove();
          $("#listofcharities").append(dataa);
          window.setTimeout(function () {
            window.location.reload();
          }, 2000);

          return false;
        }
      });


    }

  });

  $.validator.addMethod("validUrl", function (value, element) {
    if (value.substr(0, 7) != 'http://') {
      value = 'http://' + value;
    }
    if (value.substr(value.length - 1, 1) != '/') {
      value = value + '/';
    }
    return this.optional(element) || /^(http|https|ftp):\/\/[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/i.test(value);
  }, "Please enter a valid URL.");

  jQuery.validator.addMethod('phoneUS', function (phone_number, element) {
    phone_number = phone_number.replace(/\s+/g, '');
    return this.optional(element) || phone_number.length > 9 &&
      phone_number.match(/^(1-?)?(\([2-9]\d{2}\)|[2-9]\d{2})-?[2-9]\d{2}-?\d{4}$/);
  }, 'Please enter a valid phone number.');


});


$(document).ready(function () {
  $('#charity-search').click(function () {
    $("#authorize-charity").removeClass('d-none');
    $('input[name="is_non"]').click();

  });

  $('input[name="is_non"]').click(function () {
    if (this.value == "0") {
      $("#authorize").addClass('d-none');
      $("#authorize-no").removeClass('d-none');
      $("#fundraise-form").addClass('d-none');
      $("#authorize-no").text("Only decision makers can add charities to our system. Please share this with a decision maker or you can fund a project or a cause!");
    } else {
      $("#authorize").removeClass('d-none');
      $("#authorize-no").addClass('d-none');
      $("#fundraise-form").removeClass('d-none');
      $("#charity-parameters").removeClass('d-none');
    }
  });

  $('#project-search, #cause-search, #education-search').click(function () {
    $("#authorize-charity").addClass('d-none');
    $("#fundraise-form").removeClass('d-none');
    $("#charity-parameters").addClass('d-none');
  });


})



$(document).ready(function () {
  let tags = $('.tagsTeam');
  tags.tagsinput({
    allowDuplicates: false,
    maxChar: 10,
    trimValue: true,
    confirmKeys: [13],
  });


  $('#teamname').on('beforeItemAdd', function (event) {
    var tag = event.item;
    // Do some processing here
    var team_url = 'user/checkteam';
    if (!event.options || !event.options.preventPost) {
      $.get(team_url, { team: tag }, function (response) {
        if (response == 'error') {
          $("#team_info").html("Not a valid team number");
          $("#team_info").removeClass("hidden");
          $("#team_info").show().delay(2000).fadeOut();
          // Remove the tag since there was a failure
          // "preventPost" here will stop this ajax call from running when the tag is removed
          $('#teamname').tagsinput('remove', tag, { preventPost: true });
        } else {
          var team_name = response;
          var firstname = $("#firstname").val();
          $("#team_info").html("Hi " + team_name + ", we are inspired by you -- Thanks!");
          $("#team_info").show().delay(4000).fadeOut();
          $("#team_info").removeClass("hidden");

        }
      });
    }
  });

  // tags.on('itemAdded', function(item, tag) {
  //     $('.items').html('');
  //     let tags = $('.tags').tagsinput('items');
  //     $.each(tags, function(index, tag) {
  //         $('.items').append('<span>' + tag + '</span>');
  //     });
  // });

});

function showTeam(isChecked, divId, inputId) {

  if (isChecked) {
    $('#' + divId).removeClass('d-none');
    $('#frcteam').removeClass('d-none');
  }
  else {
    $('#' + inputId).tagsinput('removeAll');
    $('#' + inputId).val('');
    $('#' + divId).addClass('d-none');
    $('#frcteam').addClass('d-none');
  }
  /* For outer div */
  showfrcteam = false;
  if ($('#frc').is(':checked')) {
    showfrcteam = true;
  } else if ($('#ftc').is(':checked')) {
    showfrcteam = true;
  } else if ($('#fll').is(':checked')) {
    showfrcteam = true;
  } else if ($('#jrfll').is(':checked')) {
    showfrcteam = true;
  }

  if (showfrcteam == true) {
    $('#frcteam').removeClass('d-none');
  }
  /* For outer div */
}

$(document).ready(function () {

  $('.roboticCheckbox').each(function (idx, textArea) {

    showTeam($(this).is(':checked'), $(this).attr('div-id'), $(this).attr('teamTextboxId'));

  });

});

$(".link").click(function() {
  var btn = $(this);
	btn.attr("disabled", true);

  var card = $(this).closest(".card-body");
  addImageLoader(card);

	$.ajax({
		url: location.origin + '/home/linkAccount/',
		type: 'POST',
		data: { "type": $(this).data("gametype") },
	}).complete(function (data) {
    $(card).find(".loader").addClass("d-none");

    data = JSON.parse(data.responseText);
    if (data.status == "success") {
      $(btn).text("Pending..");
      $(btn).attr("disabled", "disabled");
      $(btn).addClass("disabled");

      showSweetAlert('Check your email for instructions!', 'Success!', 'success');
    } else {
      switch (parseInt(data.reason)) {
        case 2:
          showSweetAlert('Something went wrong on our end', 'Uh oh!', 'error');
          break;
        case 3:
          showSweetAlert('You\'ve already linked an account!', 'Whoops!', 'error');
          break;
        case 4:
          showSweetAlert('We have already sent you an email to link your account!', 'Whoops!', 'error');
          break;
      }
    }

    btn.attr("disabled", false);
  });
});

$(".unlink").click(function() {
  var btn = $(this);
	btn.attr("disabled", true);

  var gametype = $(this).data("gametype");
	showSweetConfirm('Are you sure you want to unlink your ' + gametype + ' account?', "Warning!", "warning", function(confirmed) {
		if (!confirmed) {
      btn.attr("disabled", false);
			e.preventDefault();
		} else {
			$.ajax({
				url: location.origin + '/home/unlinkAccount/',
				type: 'POST',
				data: { "type": gametype }
			}).complete( function (data) {
        data = JSON.parse(data.responseText)
        if (data.status == "success") {
          showSweetAlert('Your account was unlinked!', 'Success!', 'success');
          window.setTimeout(function () {
            window.location = window.location.href + "?tab=accounts"
          }, 2000);
        } else {
          showSweetAlert('Unable to unlink accounts.', 'Uh oh!', 'error');
        }

        btn.attr("disabled", false);
      });
		}
	}); 
});
