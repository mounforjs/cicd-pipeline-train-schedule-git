$(function () {
	// Create New Row
	$("#add_referral").click(function () {
		if ($('tr[data-id=""]').length > 0) {
			$('tr[data-id=""]').find('[name="name"]').focus();
			return false;
		}
		var tr = $("<tr>");
		$('input[name="id"]').val("");
		tr.addClass("py-1 px-2");
		tr.attr("data-id", "");
        tr.append('<td name="id"></td>');
        tr.append('<td contenteditable name="referrer_name"><select id="search" placeholder="Choose user.."autocomplete="true" class="userList"></select></td>');
        tr.append('<td contenteditable name="referrer_value"></td>');
        tr.append('<td contenteditable name="name" id="name"></td>');
		tr.append('<td contenteditable name="link" id="link"></td>');
        tr.append('<td contenteditable name="value"></td>');
		tr.append('<td contenteditable name="cap_number"></td>');
		tr.append('<td name="redemption_total"></td>');
		tr.append('<td contenteditable name="referralDateTimeRange">' +
            '<input type="text" id="dateTimeRange" name ="dateTimeRange" placeholder="Choose dates" class="form-control-sm" required/>' +
        '</td>');
		tr.append('<td contenteditable name="status">' + 
			'<input type="checkbox" name="active" checked>' + 
		'</td>');
		tr.append('<td name="created"></td>');
		tr.append('<td name="modified"></td>');
		tr.append(
			'<td class="text-center inline-block"><button class="btn btn-sm btn-primary btn-flat rounded-0 px-2 py-0">Save</button><button class="btn btn-sm btn-dark btn-flat rounded-0 px-2 py-0 mt-1" onclick="cancel_button($(this))" type="button">Cancel</button></td>'
		);
		$("#form-tbl").append(tr);
		tr.find('[name="referrer_name"]').focus();

        flatpickr('#dateTimeRange', {
            enableTime: true,
            allowInput: true,
            dateFormat: "Y-m-d H:i:s",
            minDate: "today",
            mode: "range",
			disableMobile: "true"
        });

        $(function () {
            var items = "";
            $.getJSON(window.location.origin + "/admin/get_user_list", function (data) {
                $.each(data, function (index, item) {
                        items +=
                            "<option value='" +
                                item.user_id +
                                "'>" +
                                item.fullname +
                            "</option>";
                });    
                tr.find('[name="referrer_name"]').find('#search').append(items).selectize({
                    plugins: ["remove_button"],
                    maxItems: 1,
                    mode: 'multi',
                    items: null
                });
            });
        });
	});

    $(document).on('input','#name',function(e){
        let referralName = $(this).parent().find('[name="name"]').text();

        const myUrlWithParams = new URL("https://winwinlabs.org/");
        myUrlWithParams.searchParams.append("referral", referralName);
        $(this).parent().find('[name="link"]').html(myUrlWithParams.href);
    });
    
	//setup before functions
	var typingTimer;                //timer identifier
	var doneTypingInterval = 1000;  //time in ms, 5 second for example

	//on keyup, start the countdown
	$(document).on('keyup','#name',function(e){
		clearTimeout(typingTimer);
		var self = $(this);
		let referralName = $(this).parent().find('[name="name"]').text();
		if ($('#name').val) {
			typingTimer = setTimeout(function(){			
				$.ajax({
					url: window.location.origin + "/admin/isReferralNameDuplicate",
					method: "POST",
					data: {referral:referralName},
					dataType: "json",
					error: (err) => {
						alert("An error occured while saving the data");
						console.log(err);
					},
					success: function (resp) {
						if (resp === true) {
							alert('This referral code already exits, please use a different name.');
							self.parent().find('[name="name"]').text('');
							self.parent().find('[name="link"]').text('');
							self.parent().find('[name="name"]').focus();
						} else {
							self.parent().find('[name="name"]').html(referralName);
						}
					},
				});
			}, doneTypingInterval);
		}
	});

	// Edit Row
	$(".edit_data").click(function () {
		var id = $(this).closest("tr").attr("data-id");
		$('input[name="id"]').val(id);
		var count_column = $(this).closest("tr").find("td").length;
        colArrayToReadOnly = Array(0, 4, 7, 10, 11);
		$(this)
			.closest("tr")
			.find("td")
			.each(function () {
				if ($(this).index() != count_column - 1 && !colArrayToReadOnly.includes($(this).index()))
					$(this).attr("contenteditable", true);
			});

		$(this).closest("tr").find('[name="referrer_name"]').focus();
        $(this).closest("tr").find('#search').removeAttr('disabled');
        $(this).closest("tr").find('#search').selectize({
            items: [$(this).closest("tr").find('#search').val()],
            plugins: ["remove_button"],
            maxItems: 1,
            mode: 'multi'
        });
		$(this).closest("tr").find('[name="active"]').removeAttr('disabled');

		$(this)
			.closest("tr")
			.find(".editable")
			.show("fast")
			.css("display", "inline-block");
		$(this).closest("tr").find(".noneditable").hide("fast");

        $('#name').on('input', (e) => {
            const myUrlWithParams = new URL("https://winwinlabs.org/");
            myUrlWithParams.searchParams.append("referral", $('#name').html());

            $('#link').html(myUrlWithParams.href);
        });
        
        $(this).closest("tr").find("#dateTimeRange").removeAttr('disabled');
            $(this).closest("tr").find("#dateTimeRange").click(function() {
                flatpickr($(this).closest("tr").find("#dateTimeRange"), {
                    enableTime: true,
                    allowInput: true,
                    dateFormat: "Y-m-d H:i:s",
                    minDate: "today",
                    mode: "range",
					disableMobile: "true"
                });
        });
	});

	// Delete Row
	$(".delete_data").click(function () {
		var id = $(this).closest("tr").attr("data-id");
		var name = $(this).closest("tr").find("[name='name']").text();
		var _conf = confirm('Are you sure to delete "' + name + '" from the list?');
		if (_conf == true) {
			$.ajax({
				url: window.location.origin + "/admin/referrral_actions?action=delete",
				method: "POST",
				data: { id: id },
				dataType: "json",
				error: (err) => {
					alert("An error occured while saving the data");
					console.log(err);
				},
				success: function (resp) {
					if (resp.status == "success") {
						alert(name + " is successfully deleted from the list.");
						location.reload();
					} else {
						alert(resp.msg);
						console.log(err);
					}
				},
			});
		}
	});

	$("#form-data").submit(function (e) {
		e.preventDefault();
		var id = $('input[name="id"]').val();
		var data = {};
		// check fields promise
		var check_fields = new Promise(function (resolve, reject) {
			data["id"] = id;
                $("td[contenteditable]").each(function () {
                    if($(this).attr("name")) {
                        if($(this).attr("name") =='referralDateTimeRange') {
                            data[$(this).attr("name")] = $(this).find('input[name="dateTimeRange"]').val();
                        }
                        else if ($(this).attr("name") =='referrer_name') {
                            data[$(this).attr("name")] = $(this).closest("tr").find('#search').val();
                        }
						else if ($(this).attr("name") =='status') {
							if($(this).closest("tr").find('input[name="active"]').is(':checked')){
								data[$(this).attr("name")] = 1;
							} else {
								data[$(this).attr("name")] = 0;
							}
                        }
                        else {
                            data[$(this).attr("name")] = $(this).text();
                        }
                    }
                    if ($(this).attr("name") !== "status" && data[$(this).attr("name")] == "") {
                        alert("All fields are required.");
                        resolve(false);
                        return false;
                    }
                });
			resolve(true);
		});
		// continue only if all fields are filled
		check_fields.then(function (resp) {
			if (!resp) return false;
			// validate name
			// if (!isName(data["name"])) {
			// 	alert("Invalid Name.");
			// 	$('[name="name"][contenteditable]')
			// 		.addClass("bg-danger text-light bg-opacity-50")
			// 		.focus();
			// 	return false;
			// } else {
			// 	$('[name="name"][contenteditable]').removeClass(
			// 		"bg-danger text-light bg-opacity-50"
			// 	);
			// }

			// // validate link
			// if (!isLink(data["link"])) {
			// 	alert("Invalid Link.");
			// 	$('[name="link"][contenteditable]')
			// 		.addClass("bg-danger text-light bg-opacity-50")
			// 		.focus();
			// 	return false;
			// } else {
			// 	$('[name="link"][contenteditable]').removeClass(
			// 		"bg-danger text-light bg-opacity-50"
			// 	);
			// }

			// // validate value
			// if (!Value(data["value"])) {
			// 	alert("Invalid Values.");
			// 	$('[name="value"][contenteditable]')
			// 		.addClass("bg-danger text-light bg-opacity-50")
			// 		.focus();
			// 	return false;
			// } else {
			// 	$('[name="value"][contenteditable]').removeClass(
			// 		"bg-danger text-light bg-opacity-50"
			// 	);
			// }

			// // validate cap value
			// if (!isCap(data["cap"])) {
			// 	alert("Invalid Cap Amount.");
			// 	$('[name="cap"][contenteditable]')
			// 		.addClass("bg-danger text-light bg-opacity-50")
			// 		.focus();
			// 	return false;
			// } else {
			// 	$('[name="cap"][contenteditable]').removeClass(
			// 		"bg-danger text-light bg-opacity-50"
			// 	);
			// }

			$.ajax({
				url: window.location.origin + "/admin/referrral_actions?action=save",
				method: "POST",
				data: data,
				dataType: "json",
				error: (err) => {
					alert("An error occured while saving the data");
					console.log(err);
				},
				success: function (resp) {
					if (!!resp.status && resp.status == "success") {
						alert(resp.msg);
						location.reload();
					} else {
						alert(resp.msg);
					}
				},
			});
		});
	});
});
//Email Validation Function
window.IsEmail = function (email) {
	var regex =
		/^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
	if (!regex.test(email)) {
		return false;
	} else {
		return true;
	}
};
//Contact Number Validation Function
window.isContact = function (contact) {
	return (
		$.isNumeric(contact) && contact.length == 11 && contact.substr(0, 2) == "09"
	);
};

// removing table row when cancel button triggered clicked
window.cancel_button = function (_this) {
    $("#dateTimeRange").attr('disabled','disabled');
    _this.closest("tr").find('#search').selectize()[0].selectize.disable(); 
	_this.closest("tr").find('[name="active"]').attr('disabled','disabled');

	if (_this.closest("tr").attr("data-id") == "") {
		_this.closest("tr").remove();
	} else {
		$('input[name="id"]').val("");
		_this
			.closest("tr")
			.find("td")
			.each(function () {
				$(this).removeAttr("contenteditable");
			});
		_this.closest("tr").find(".editable").hide("fast");
		_this.closest("tr").find(".noneditable").show("fast");
	}
};
