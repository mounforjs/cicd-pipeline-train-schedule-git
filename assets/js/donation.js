$(document).ready(function () {
	$("#searchbar").focus();
	$("#donationAmount").text("$25");
	$("#noteAmount").text("$25");
	$("#noteAmountFee").text("$0.25");
	$("#donationTotalCharges").text("$25.25").addClass("text-success");

	$("#donate-buttons").on("click", ".btn-blue", function (e) {
		e.preventDefault();
		$(".active").removeClass("active");
		$("#other-input").hide().siblings("#other").show();
		$(this).filter(".btn-blue").addClass("active");
		var dollar = $(".active").data("dollars");
		if (dollar) {
			$("#dAmount").val("");
			checkDonationValidationAmount(dollar);
			dollarTotal = (dollar * 1.01).toFixed(2);
			$("#donationAmount").text("$" + dollar);
			$("#noteAmount").text("$" + dollar);
			$("#noteAmountFee").text("$" + (dollar*0.01).toFixed(2));
			$("#donationTotalCharges").text("$" + dollarTotal);
		}
	});

	$("#other").on("click", function (e) {
		e.preventDefault();
		var buttons = $(this).parent("#donate-buttons");
		buttons.find(".active").removeClass("active");
		var other = $(this).hide().siblings("#other-input");
		other.show();
		other.find("input").focus();
		var pText = buttons.siblings("p");
		pText.text("Thank you!");
		var oValue = other.find("input");
		$("#dAmount").on("input", function () {
			$("#dAmount").trigger("click");
			var input = $(this).val();
			checkDonationValidationAmount(input.toString(2));
			inputTotal = (input * 1.01).toFixed(2);
			$("#donationAmount").text("$" + input);
			$("#noteAmount").text("$" + input);
			$("#noteAmountFee").text("$" + (input*0.01).toFixed(2));
			$("#donationTotalCharges").text("$" + inputTotal);
		});
	});

	$(document).ready(function () {
		$.ajax({
			type: "POST",
			url: window.location.origin + "/user/sessions",
		}).done(function (msg) {
			usession = msg;
		});
	});

	var fundraiser_slug = "";
	$(document).on("click", ".payDonateBtn", function () {
		if (usession == 1) {
			$("#donationModal").modal("show");
			$("#donationModalTitle").text(
				"Donate to" + " " + $(this).attr("fundraiser-name")
			);
			fundraiser_slug = $(this).attr("fundraiser-slug") || $(this).attr("slug");
		} else {
			showSweetAlert("Please login to donate. Redirecting...", "Great!");
			setTimeout(
				window.location.assign(window.location.origin + "/login"),
				2000
			);
		}
	});

	$(document).ready(function () {
		$("#disclaimerCheckBox").change(function () {
			if (this.checked){
				$("#disclaimerErrorCheck").hide();
			}
		});
	});

	$(".donationBtn").click(function () {
		var dAmount = Number($("#donationAmount").text().replace("$", "")); //$(".amount").val(),
		if(checkDonationValidationAmount(dAmount) == false) return;
		var IsChecked = $("#disclaimerCheckBox").is(":checked");
		if (IsChecked == true) {
			$("#errorCheck").hide();
			$.ajax({
				type: "POST",
				data: {
					amount: dAmount,
					fundraiser_slug: fundraiser_slug,
				},
				url: window.location.origin + "/donation",
				beforeSend: function () {
					$("#divLoading").addClass("show");
				},
				complete: function () {
					$("#divLoading").removeClass("show");
				},
				success: function (result) {
					if(JSON.parse(result).status == 'error'){
					$("#disclaimerErrorCheck").text(JSON.parse(result).message);
					$("#disclaimerErrorCheck").show();
					}
					else {
					showSweetAlert(
						JSON.parse(result).message,
						"Great"
					);
					setTimeout(window.location.reload(), 2000);
					}
				},
			});
		} else {
			$("#disclaimerErrorCheck").text("Please accept the disclaimer");
			$("#disclaimerErrorCheck").show();
			return;
		}
	});

	function checkDonationValidationAmount(donation) {
		var availableCredits = $("#availCredits").text().replace("$", "");
		
		var buyCreditsLink = '<u><a href="' + window.location.origin + '/buycredits/fundraiser/'+fundraiser_slug+'" class="text-primary">buy more credits</a></u>';
		if (Number(donation * 1.01) > Number(availableCredits)) {
			$("#amountErrorCheck").html(
				"Insufficient credits available. Please " +  buyCreditsLink + " or adjust donation amount."
			);
			$("#amountErrorCheck").show();
			$("#donationTotalCharges").addClass("text-danger");
			$("#donationTotalCharges").removeClass("text-success");
			return false;
		} else if (Number(donation) < 5) {
			$("#amountErrorCheck").text("Minimum donation is $5.");
			$("#amountErrorCheck").show();
			return false;
		}
		else{
			$("#amountErrorCheck").hide();
			$("#donationTotalCharges").addClass("text-success");
			$("#donationTotalCharges").removeClass("text-danger");
			return true;
		}
	}
});
