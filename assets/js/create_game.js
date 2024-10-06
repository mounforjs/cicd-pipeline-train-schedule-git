
function addNewTinyMCE(id, content) {	
	oldContent = content;	
	tinyMCEInitialize(id, content);	
}

var $validator;
var ignorePrize = [':hidden', ':not(#gametitle):not(#gameInfoImage):not(#gamedescription):not(#puzzleImage):not(#gamedifficulty):not(#selectedQuiz):not(#min):not(#enddate):not(#publishdate)'];
var showPrize = [':hidden', ':not(#gametitle):not(#gameInfoImage):not(#prizeImage):not(#prizeTitle):not(#prizeDescription):not(#prizeSpecification):not(#gamedescription):not(#puzzleImage):not(#gamedifficulty):not(#selectedQuiz):not(#min):not(#selectedFundraiser):not(#publishdate):not(#enddate)'];

$(document).ready(function () {
	var gameDesc = $("#gamedescription").text();
	var prizeDesc = $("#prizeDescription").text();
	var prizeSpec = $("#prizeSpecification").text();
	var gameEndInfoDesc = $("#gameEndInfoDescription").text();

	addNewTinyMCE("gamedescription", gameDesc);	
    addNewTinyMCE("prizeDescription", prizeDesc);	
    addNewTinyMCE("prizeSpecification", prizeSpec);
	addNewTinyMCE("gameEndInfoDescription", gameEndInfoDesc);
	
	isApproved();
	setDonationOptionBeneficiary();

	$(".confirm").click(function () {
		window.onbeforeunload = null;

		if (validateForm() == false){
			elementFieldset = $("fieldset label[class='error']:eq(0)").closest("fieldset").attr("name");
			$("#"+elementFieldset).click();

			window.onbeforeunload = areFormsDirty;
			return false;
		}
		
		var gameType = $(".game_type").attr("type");
		var gameTitle = $("#gametitle").val();
		var gameTags = $("#gametags").val();
		var selectedFundraiser = $("#selectedFundraiser").val();
		if($(".game_type").attr("type") == 'challenge'){ 
			var howToWin = $('input[name="optionsCheckboxes"]:checked').val();
		}else{
			var howToWin = $('input[name=howToWin]:checked').val();
		}
		var gameGoalNumber = $("#game_tile_goal").val();
		var gameWinnerCount = $("#winner_count").val();
		var gameEnd = $("#game_end").val();
		var gameDays = $("#days").val();
		var gameHours = $("#hours").val();
		var gameMIn = $("#min").val();
		var endDate = new Date($("#enddate").val()).toUTCString();
		
		var publishstat = $('input[name="publishstat"]:checked').val();
		var quiz = $('#selectedQuiz').val();
		var gameTimeZone = $("#timeZone").val();

		var publish_date = $("#publishdate").val();
		var utcpublishdate = (publishstat == "Publish Game") ? getLocalTime(publish_date).toUTCString() : null;
		var credit_prize = $('input[name="credit_prize"]:checked').val();

		var prizeTitle = $("#prizeTitle").val();
		var prizeType = $('input[name="prize_type"]:checked').val();

		var gameDonationOption = $('input[name="donationOption"]:checked').val();

		// ---------------- finance related variables -------------------
		var gameFundraiserGoal =  $('#fundraise_goal').val();
		var gameFundraiserPercent = $('#beneficiary_percent').val();

		var prize_value = (credit_prize == 'prize') ? $("#prize_value").val() : $('#winner_earn').val();
		var gameCostToPlay = $("#cost_to_play").val();
		// ---------------- finance related variables -------------------

		var gameInfoImage = $("#gameInfoImage").val();
		var gamePrizeImage = $("#prizeImage").val();
		var gamePrizeOtherImages = $("input[name='prizeImagesHidden[]']")
			.map(function () {
				return $(this).val();
			})
			.get();

		var gamedifficulty = $("#gamedifficulty").val();
		var puzzleImage = $("#puzzleImage").val();

		var gamemode = $("#gamemode").val();
		var gameconfig = $("#gameConfig").val();
		var attempts = $("#numberOfLives").val();

		var QuestionText = $("input[name='question_text[]']")
			.map(function () {
				return $(this).val();
			})
			.get();
		var QuestionType = $(".question_type option:selected")
			.map(function () {
				return $(this).val();
			})
			.get();

		tinymce.activeEditor.uploadImages(function (success) {
			var gameDescription = tinymce.get("gamedescription").getContent();
			var prizeDescription = tinymce.get("prizeDescription").getContent();
			var prizeSpecification = tinymce.get("prizeSpecification").getContent();
			var gameEndInfoDescription = tinymce.get("gameEndInfoDescription").getContent();

			var data = {
				// game info
				game_id: $("#game_id").val(),
				type: gameType,
				gametitle: gameTitle,
				gametags: gameTags,
				gamedescription: gameDescription,
				gameInfoImage: gameInfoImage,
				
				//reward type
				credit_prize: credit_prize,

				//prize info
				prizetitle: prizeTitle,
				prizedescription: prizeDescription,
				prize_specification: prizeSpecification,
				prize_type: prizeType,
				gamePrizeImage: gamePrizeImage,
				gamePrizeOtherImages: gamePrizeOtherImages,

				// beneficiary options
				gameDonationOption: gameDonationOption,
				selected_fundraiser: selectedFundraiser,
				
				// game rules 
				optionsCheckboxes: howToWin,
				game_tile_goal: gameGoalNumber,
				gamedifficulty: gamedifficulty,
				puzzleImage: puzzleImage,
				gamemode: gamemode,
				gameconfig: gameconfig,
				attempt_count: attempts,
				quiz: quiz,
				question_text: QuestionText,
				question_type: QuestionType,
				
				// finance variables
				winner_count: gameWinnerCount,
				gameFundraiserGoal: gameFundraiserGoal,
				gameFundraiserPercent: gameFundraiserPercent,
				prize_value: prize_value,
				costtoplay: gameCostToPlay,
				
				// game end variables
				gamedepends: gameEnd,
				days: gameDays,
				hours: gameHours,
				min: gameMIn,
				endDate: endDate,
				gameTimeZone: gameTimeZone,
				
				gamestage: publishstat,
				utc_publish_date: utcpublishdate,
				
				gameEndInfoDescription: gameEndInfoDescription,
			};

			$.ajax({
				method: "POST",
				data: data,
				url: window.location.origin + "/games/add_game",
				beforeSend: function () {
					$('.confirm').addClass('disabled');
					$('.confirm').prop('disabled', true);
					$('#divLoading').addClass('show');
				},
				complete: function () {
					$('#divLoading').removeClass('show');
				},
				success: function (result) {
					result = JSON.parse(result);
					if (result.status == "success") {
						if (window.location.href.indexOf("/games/edit") > -1) {
							showSweetAlert("Your game has been updated!", "Great");
						} else {
							showSweetAlert("Your game has been created!", "Great");
						}

						window.location =
							window.location.origin +
							"/games/show/" +
							result.gamestage +
							"/" +
							result.slug;
					} else {
						resetConfirm(result.msg);
					}
				},
				error: function (request, status, error) {
					resetConfirm("We ran into an error!");
				}
			});
		});
	});

	$(".date").on("change", function(e) {
		validateForm();
		if (!(this.name in $("#creategameform2").validate().invalid)) {
			$("#" + this.name + "Error label[class='error']").remove();
		}
	});

	$("#game_end").on("change", function (e) {
		if (parseInt($(this).val()) != 4) {
			$("#enddateError label[class='error']").remove()
			$("#publishdateError label[class='error']").remove()
		}
	});

	$('input[name="publishstat"]').on("click", function () {
		if ($('input[name="publishstat"]:checked').val() != "Publish Game") {
			$("#enddateError label[class='error']").remove()
			$("#publishdateError label[class='error']").remove()
		}
	});

	function getLocalTime(t) {
		//returns correct time in terms of user's timezone
		time = new Date(t);

		//get user's timezone offset, selected timezone offset, and difference between
		var localOffset = new Date().getTimezoneOffset();
		var tzOffset = $("#timeZone :selected").data("offset") || localOffset;
		var offset = (-parseInt(tzOffset) * 60) - localOffset;

		// add difference between user's timezone and selected timezone
		var localTime = new Date(time.getTime() + (offset * 60000));

		return localTime;
	}

	function resetConfirm(msg) {
		$('.confirm').removeClass('disabled');
		$('.confirm').prop('disabled', false);
		showSweetAlert(msg, "Whoops!", "error");
		window.onbeforeunload = areFormsDirty;
	}

	$(document).on("change", ".commonImageUpload", function (e) {
		if (this.files.length === 0) {
			return;
		}

		var showPreviewOn = $(this).attr("show-preview-on");
		var setHiddenValue = $(this).attr("set-hidden-value");

		if ($("#" + showPreviewOn).find(".loader").length == 0) {
			addImageLoader($("#" + showPreviewOn));
		}

		var fileTypes = this.files[0].type;
		var image = this.files[0];
		data = new FormData();
		data.append("file", this.files[0]);
		$.ajax({
			url: window.location.origin + "/ajax/uploadImage",
			type: "POST",
			data: data,
			enctype: "multipart/form-data",
			processData: false, // tell jQuery not to process the data
			contentType: false, // tell jQuery not to set contentType
			beforeSend: function () {
				$("#" + showPreviewOn).parent().find(".loader").show();
			},
			complete: function () {
				$("#" + showPreviewOn).parent().find(".loader").hide();
			}
		}).done(function (data) {
			if (data != "error") {
				if (
					$.inArray(fileTypes, [
						"image/jpeg",
						"image/png",
						"image/jpg",
						"image/gif",
					]) == -1
				) {
					alert("Not a valid image, only JPEG , PNG, or GIF allowed");
				} else {
					$("." + setHiddenValue).val(data);

					$("#" + showPreviewOn).attr("src", window.URL.createObjectURL(image));
					$(e.target).closest("div[class*='col']").find("label[class='error']:visible:first").remove();

					getOrientation(image, function (orientation) {
						rotate_div("iconimage", orientation);
					});
				}
			} else {
				console.log("Could not upload!");
			}
		});
	});

	/** Prize Images **/

	var imagesPreview = function (input, placeToInsertImagePreview) {
		if (input.files) {
			var id = $(input).attr("id");

			var prizeIcons = $("#" + id).siblings(".prize_icons");
			if ($(prizeIcons).find(".loader").length == 0) {
				$(prizeIcons).append('<div class="loader"><div class="imageLoader"></div></div>');
			} else {
				$(prizeIcons).find(".loader").css("display", "");
			}

			var filesAmount = input.files.length;
			var flag = 1;

			var filesProcessed = 0;

			$(".prizeImagesLable").html("");
			for (i = 0; i < filesAmount; i++) {
				var reader = new FileReader();
				fileTypes = input.files[i].type;
				var image = input.files[i];
				data = new FormData();
				data.append("file", input.files[i]);
				$.ajax({
					url: window.location.origin + "/ajax/uploadImage",
					type: "POST",
					data: data,
					enctype: "multipart/form-data",
					processData: false, // tell jQuery not to process the data
					contentType: false, // tell jQuery not to set contentType
					beforeSend: function () {
						$(prizeIcons).find(".loader").show();
					}
				}).done(function (data) {
					filesProcessed++;

					if (data != "error") {
						if (
							$.inArray(fileTypes, [
								"image/jpeg",
								"image/png",
								"image/jpg",
								"image/gif",
							]) == -1
						) {
							alert("Not a valid image, only JPEG , PNG, or GIF allowed");
						} else {
							$(".prizeImagesLable").append("");

							$(placeToInsertImagePreview).append(
								"<div class='galleryth'><img src='" +
									data +
									"'><a class='thremove'><i class='fa fa-times' aria-hidden='true'></i></a><input type='hidden' name='prizeImagesHidden[]' value='" +
									data +
									"'></div>"
							);

							getOrientation(image, function (orientation) {
								rotate_div("iconimage", orientation);
							});
						}
					} else {
						console.log("Could not upload!");
					}

					if (filesProcessed == filesAmount) {
						$(input).parent().find(".prize_icons").find(".loader").hide();
					}

					$(".thremove").on("click", function () {
						$(this).parent("div").remove();
					});

					if (flag == 0) {
						alert("Not a valid image, only JPEG or PNG allowed");
					}
				});
			}
		}
	};

	if (window.location.href.indexOf("/games/edit") > -1) {
		$(".thremove").on("click", function () {
			$(this).parent("div").remove();
		});
	}

	$("#prize_images").on("change", function () {
		imagesPreview(this, ".prize_icons");
	});
	/** Prize Images **/

	function getOrientation(file, callback) {
		var reader = new FileReader();
		reader.onload = function (event) {
			var view = new DataView(event.target.result);
			if (view.getUint16(0, false) != 0xffd8) return callback(-2);
			var length = view.byteLength,
				offset = 2;
			while (offset < length) {
				var marker = view.getUint16(offset, false);
				offset += 2;
				if (marker == 0xffe1) {
					if (view.getUint32((offset += 2), false) != 0x45786966) {
						return callback(-1);
					}
					var little = view.getUint16((offset += 6), false) == 0x4949;
					offset += view.getUint32(offset + 4, little);
					var tags = view.getUint16(offset, little);
					offset += 2;
					for (var i = 0; i < tags; i++)
						if (view.getUint16(offset + i * 12, little) == 0x0112)
							return callback(view.getUint16(offset + i * 12 + 8, little));
				} else if ((marker & 0xff00) != 0xff00) break;
				else offset += view.getUint16(offset, false);
			}
			return callback(-1);
		};
		reader.readAsArrayBuffer(file.slice(0, 64 * 1024));
	}

	function rotate_div(div_id, orientation) {
		// alert(orientation);
		switch (orientation) {
			case 2:
				$("#" + div_id).addClass("flip");
				break;
			case 3:
				$("#" + div_id).addClass("rotate-180");
				break;
			case 4:
				$("#" + div_id).addClass("flip-and-rotate-180");
				break;
			case 5:
				$("#" + div_id).addClass("flip-and-rotate-270");
				break;
			case 6:
				$("#" + div_id).addClass("rotate-90");
				break;
			case 7:
				$("#" + div_id).addClass("flip-and-rotate-90");
				break;
			case 8:
				$("#" + div_id).addClass("rotate-270");
				break;
			default:
				$("#" + div_id).removeClass();
				$("#" + div_id).addClass("gIconPreview-img mb-2");
				break;
		}
	}

	$("#game_end").change(function () {
		if ($(this).val() == 2) {
			$("#duration_123").hide();
			$('#end_date').hide();
		} else if ($(this).val() == 3) {
			$("#duration_123").show();
			$('#end_date').hide();
		} else if($(this).val() == 4) {
			$('#duration_123').hide();
			$('#end_date').show();
		}

		if ($(this).val() == 3 || $(this).val() == 4) {
			$("#timeDisclaimer").removeClass("d-none");
		} else {
			$("#timeDisclaimer").addClass("d-none");
		}
	});

	if ($('input[name="publishstat"]:checked').val() != "Publish Game") {
		$(".datepick").addClass("d-none");
	}
	$(".time-zone-select").timezones();

	$('input[name="publishstat"]').on("click", function (e) {
		if (this.disabled) {
			e.stopPropagation();
			return;
		}

		$pStat = this.value;
		if ($("#selectedQuiz").length > 0) {
			if ($pStat == "Publish Game") {
				$(".datepick").removeClass("d-none");
				$(".time-zone-select").timezones();
			} else {
				$("fieldset[name='publish'] .publishstat:checked").prop("checked", false);
				$(this).prop("checked", true);

				$("#publishdate").val("");
				$(".time-zone-select").val("");
				$(".datepick").addClass("d-none");
			}
		} else {
			if ($pStat == "Publish Game") {
				$(".datepick").removeClass("d-none");
				$(".time-zone-select").timezones();
			} else {
				$("#publishdate").val("");
				$(".time-zone-select").val("");
				$(".datepick").addClass("d-none");
			}
		}
		
	});
	
	var current_fs, next_fs, previous_fs; //fieldsets
	var opacity;

	$(".next[name='next']").click(function (e) {
		valid = validateForm() ;
		if (inprogress) {
			e.preventDefault()
			return;
		} else if (!valid) {
			var error = $("fieldset label[class='error']:eq(0)");
			current_fs = $(this).parent();
			error_fs = $(error).closest("fieldset");

			if ($(current_fs).index() == $(error_fs).index()) {
				$(error).get(0).scrollIntoView({behavior: "smooth", block: "end"});
				e.preventDefault()
				return;
			} else if ($(current_fs).index() > $(error_fs).index()) {
				$("#"+$(error_fs).attr("name")).click();
				$(error).get(0).scrollIntoView({behavior: "smooth", block: "end"});
				e.preventDefault()
				return;
			}
		}

		current_fs = $(this).parent();
		next_fs = $(this).parent().next();
		
		inprogress = true;
		$("#progressbar li").eq($("fieldset").index(next_fs)).addClass("active");

		next_fs.show();

		if ($.fn.DataTable.isDataTable('#quizTable')) {
			$('#quizTable').DataTable().columns.adjust();
		}

		current_fs.animate(
			{ opacity: 0 },
			{
				step: function (now) {
					opacity = 1 - now;

					$(current_fs).css({
						display: "none",
						position: "relative",
					});
					$(next_fs).css({ opacity: opacity });
				},
				duration: 400,
				done: function() {
					inprogress = false;
				}
			}
		);
	});

	const costToPlayMin = 0.10;
	function validateForm(ignore=false) {
		tinymce.triggerSave();

		$.validator.addMethod('beforeEndDate', function (value, element, param) {
			var selectedDate = new Date(element.value);
			var endDate = new Date($("#enddate").val());

			return (selectedDate >= endDate) ? false : true;
		}, 'Publish Date must be before End Date!');

		$.validator.addMethod('pastDate', function (value, element, param) {
			var currentDate = new Date();
			var selectedDate = new Date(element.value);

			return (selectedDate < currentDate) ? false : true;
		}, 'Date is in the past!');

		var ignoreList = (ignore) ? "" : (($("#prizeSection").is(":visible")) ? showPrize : ignorePrize);
		$validator = $("#creategameform2").validate({
			ignore: ignoreList, //ignore fields
			rules: {
				gametitle: {
					required: true,
					minlength: 10,
				},
				gamedescription: {
					required: true,
					minlength: 10,
				},
			
				gameInfoImage: {
					required: {
						depends: function(element) {
							if (window.location.href.indexOf("/games/edit") > -1) {
								return false;					
							} else {
								return true;
							}
							
						}
					}
				},
				prizeImage: {
					required: {
						depends: function(element) {
							if($("#prizeSection").is(":visible")) {
								if (window.location.href.indexOf("/games/edit") > -1) {
									return ($(':hidden#prizeImage').val()=="");		
								} else {
									return true;
								}					
							} else {
								return false;
							}
						}
					}
				},
				prizeImagesHidden: {
					required: false
				},
				"prize_images[]": {
					required: false
				},
				prizeTitle: {
					required: {
						depends: function(element) {
							if($("#prizeSection").is(":visible")) {
								return true;				
							} else {
								return false;
							}
						}
					},
					minlength: 10,
				},
				prizeDescription:{
					required: 
					{
						depends: function(element) {
							if($("#prizeSection").is(":visible")) {
								return true;				
							} else {
								return false;
							}
						}
					}
				},
				prizeSpecification:{
					required: {
						depends: function(element) {
							if($("#prizeSection").is(":visible")) {
								return true;				
							} else {
								return false;
							}
						}
					}	
				},
				prize_type:{
					required: false
				},
				selectedFundraiser: {
					required: {
						depends: function(element) {
							if($(".addFundraiserBlock").is(":visible")){
								return ($("#selectedFundraiser").val() == '') ? true : false;				
							}
						}
					}
				},
				puzzleImage: {
					required: {
						depends: function(element) {
							if($("fieldset[name='rules']").is(":visible")){
								if (window.location.href.indexOf("/games/edit") > -1) {
									return false;						
								} else {
									return true;
								}					
							}
						}
					}
				},	
				gamedifficulty: {
					required: {
						depends: function(element) {
							return $("fieldset[name='rules']").is(":visible");
						}	
					}	
				},
				winner_count: {
					required: {
						depends : function() {
							return !($('input[name="credit_prize"]:checked').val() == 'free');
						}
					},
					min: {
						param : 1,
						depends : function() {
							return !($('input[name="credit_prize"]:checked').val() == 'free');
						}
					},
				},
				gamedepends: {
					required: true,
				},
				selectedQuiz: {
					required: true
				},
				min: {
					required:  {
						depends: function(element) {
							if ($("#duration_123").is(":visible")) {
								var days = parseInt($("#hours").val());
								var hours = parseInt($("#days").val());
								if (days || hours || parseInt($("#game_end").val()) == 2) {
									return false;
								}
								return true;
							}
						}
					}
				},
				min_earn: {
					required: false
				},
				beneficiary_percent: {
					required: !($('input[name="credit_prize"]:checked').val() == 'free'),
					min: {
						param : 10,
						depends : function() {
							return !($('input[name="credit_prize"]:checked').val() == 'free');
						}
					},
					max: {
						param : 100,
						depends : function() {
							return !($('input[name="credit_prize"]:checked').val() == 'free');
						}
					}
				},
				prize_value: {
					required: !($('input[name="credit_prize"]:checked').val() == 'free'),
					min: {
						param : 10,
						depends : function() {
							return $('input[name="credit_prize"]:checked').val() == 'prize';
						}
					}
				},
				fundraise_goal: {
					required: !($('input[name="credit_prize"]:checked').val() == 'free'),
					min: {
						param : 1,
						depends : function() {
							return !($('input[name="credit_prize"]:checked').val() == 'free');
						}
					}
				},
				cost_to_play: {
					required: !($('input[name="credit_prize"]:checked').val() == 'free'),
					min : {
						param : costToPlayMin,
						depends : function() {
							return !($('input[name="credit_prize"]:checked').val() == 'free');
						}
					}
				},
				enddate: {
					required: {
						depends: function(element) {
							if (parseInt($("#game_end").val()) == 4) {
								return true;
							} else {
								return false;
							}
						}
					},
					pastDate : {
						depends: function(element) {
							if (parseInt($("#game_end").val()) == 4) {
								return true;					
							} else {
								return false;
							}
						}
					}
				},
				publishdate: {
					required: {
						depends: function(element) {
							if ($("#publish_data").is(":checked")) {
								return true;
							} else {
								return false;
							}
						}
					},
					beforeEndDate : {
						depends: function(element) {
							if (parseInt($("#game_end").val()) == 4 && $("#publish_data").is(":checked")) {
								return true;				
							} else {
								return false;
							}
						}
					},
					pastDate : {
						depends: function(element) {
							if ($("#publish_data").is(":checked")) {
								return true;				
							} else {
								return false;
							}
						}
					}
				},
				publishstat: {
					required: {
						depends: function(element) {
							if ($("fieldset[name='publish']").is(":visible")) {
								return $("input[name='publishstat']:checked").length == 0;
							} else {
								return false;
							}
						}
					}
				}
			},
			messages: {
				winner_count: {
					min: $.validator.format("Must be >= {0}.")
				},
				fundraise_goal: {
					min: $.validator.format("Must be >= {0}.")
				},
				beneficiary_percent: {
					min : $.validator.format("Must be > 10 and <= 100."),
					max : $.validator.format("Must be > 10 and <= 100."),
				},
				prize_value: {
					min: $.validator.format("Must be >= {0}.")
				},
				cost_to_play: {
					min: "Must be >= " + costToPlayMin.toFixed(2) + "."
				}
			},
			errorPlacement: function (error, element) {
				switch (element.attr("name")) {
					case "gameInfoImage":
						$("#mainImgError").append(error);
						break;
					case "puzzleImage":
						$("#puzzleImageError").append(error);
						break;
					case "prizeImageUpload":
					case "prizeImage":
						$("#prizeImgError").append(error);
						break;
					case "prize_images[]":
					case "prize_images":
						$("#prizeImagesError").append(error);
						break;
					case "prizeTitle":
						$("#prizeTitleError").append(error);
						break;
					case "prizeDescription":
						$("#prizeDescriptionError").append(error);
						break;
					case "prizeSpecification":
						$("#prizeSpecificationError").append(error);
						break;
					case "publishstat":
						error.css('white-space','nowrap')
						$("#publisherror").append(error);
						break;
					case "gamedescription":
						$("#gameDescriptionError").append(error);
						break;
					case "gametitle":
						$("#gameTitleError").append(error);
						break;
					case "gamedifficulty":
						$("#gameDifficultyError").append(error);
						break;
					case "min":
						$("#gameLengthError").append(error);
						break;
					case "selectedQuiz":
						$("#quizError").append(error);
						break;
					case "enddate":
						$("#enddateError").append(error);
						break;
					case "publishdate":
						$("#publishdateError").append(error);
						break;
					case "selectedFundraiser":
						$("#selectedFundraiserError").append(error);
						break;
					case "winner_count":
						$("#winnerCountError").append("<label id='winner-count-error' class='error' for='winner_count'>Must be >= 1.</label>");
						break;
					case "beneficiary_percent":
						$("#beneficiaryPercentError").append("<label id='fundraise-goal-error' class='error' for='beneficiary_percent'>Must be > 10 and <= 100.</label>");
						break;
					case "fundraise_goal":
						$("#fundraiseGoalError").append("<label id='fundraise-goal-error' class='error' for='fundraise_goal'>Must be >= 1.</label>");
						break;
					case "prize_value":
						$("#prizeValueError").append("<label id='prize_value-error' class='error' for='prize_value'>Must be >= 10.</label>");
						break;
					case "cost_to_play":
						$("#costToPlayError").append("<label id='cost_to_play-error' class='error' for='cost_to_play'>Must be >= " + costToPlayMin.toFixed(2) + ".</label>");
						break;
					default:
						error.insertAfter(element);
						break;
				}
			},
		});

		return $('#creategameform2').valid();
	};

	$("#prizeTitle").on("change", function() {
		if ($(this).val().replace(/(<([^>]+)>)/gi, "").length >= 10) {
			$("#prizeTitleError label[class='error']").remove()
		}
	});

	$("#gametitle").on("change", function() {
		if ($(this).val().replace(/(<([^>]+)>)/gi, "").length >= 10) {
			$("#gameTitleError label[class='error']").remove()
		}
	});

	$("#gamedifficulty").on("change", function() {
		if ($(this).val() != "") {
			$("#gameDifficultyError label[class='error']").remove()
		}
	});

	$("#days").on("change", function() {
		if ($("#days").val() != "" || $("#hours").val() != "" || $("#min").val() != "") {
			$("#gameLengthError label[class='error']").remove()
		}
	});

	$("#hours").on("change", function() {
		if ($("#days").val() != "" || $("#hours").val() != "" || $("#min").val() != "") {
			$("#gameLengthError label[class='error']").remove()
		}
	});

	$("#min").on("change", function() {
		if ($("#days").val() != "" || $("#hours").val() != "" || $("#min").val() != "") {
			$("#gameLengthError label[class='error']").remove()
		}
	});

	$("#selectedFundraiser").on("change", function(e) {
		if ($(this).val() != "") {
			$("#selectedFundraiserError label[class='error']").remove();
		}
	});

	$("#isApproved").on("change", function(e) {
		isApproved();
	});

	function isApproved() {
		if ($("#isApproved").val() !== "Yes") {
			$(".addFundraiserBlock")[0].scrollIntoView(false);
			showSweetUserConfirm("You can still create a game with this beneficiary, but you will only be able to create a draft until the beneficiary is approved." ,"This beneficiary is not approved!");
			$("input[name='publishstat'][value='Draft Game']").click();
			$("input[name='publishstat'][value!='Draft Game']").prop('disabled', true);
			$("input[name='publishstat'][value!='Draft Game']").closest(".col-md-4").addClass("disabled");
		} else {
			$("input[name='publishstat']").prop('disabled', false);
			$("input[name='publishstat'][value!='Draft Game']").closest(".col-md-4").removeClass("disabled");
		}

		setDonationOptionBeneficiary();
	}

	$("input[name='donationOption']").on("change", function(e) {
		setDonationOptionBeneficiary();
	});

	function setDonationOptionBeneficiary() {
		var donation_option = parseInt($("input[name='donationOption']:checked").val());
		var beneficiary_name = $('#selectedFundraiserDetails #defaultName').text();
		if (donation_option !== 2) {
			$(".beneficiary-select").text("(" + beneficiary_name +")");
		} else {
			$(".beneficiary-select.creator").text("(" + beneficiary_name +")");
			$(".beneficiary-select.player").text("(TBD)");
		}

		if (donation_option !== 2) {
			$('#winner_charity_name').parent().addClass("d-none");
			$('#goal_charity_winner_name').parent().addClass("d-none");
		} else {
			$('#winner_charity_name').parent().removeClass("d-none");
			$('#goal_charity_winner_name').parent().removeClass("d-none");
		}

		$('#beneficiary_percent').change();
	}

	$(document).on("click", "#quizTable input[name='quiz'], #quizTable td", function() {
		if ($("#selectedQuiz").val() != "") {
			$("#quizError label[class='error']").remove()
		}
	});

	tinymce.get("gamedescription").on("keyup paste", function() {
		if (tinymce.get("gamedescription").getContent().replace(/(<([^>]+)>)/gi, "").length > 10) {
			$("#gameDescriptionError label[class='error']").remove()
		}
	});

	tinymce.get("prizeDescription").on("keyup paste", function() {
		if (tinymce.get("prizeDescription").getContent().replace(/(<([^>]+)>)/gi, "").length > 10) {
			$("#prizeDescriptionError label[class='error']").remove()
		}
	});

	tinymce.get("prizeSpecification").on("keyup paste", function() {
		if (tinymce.get("prizeSpecification").getContent().replace(/(<([^>]+)>)/gi, "").length > 10) {
			$("#prizeSpecificationError label[class='error']").remove()
		}
	});

	var inprogress = false;
	$("#progressbar li").on("click", function(e) {
		if (inprogress) {
			e.preventDefault()
			return;
		}

		var current = $("fieldset:visible");
		var currentIndex = current.index() ;
	
		var targetName = $(this).attr("id");
		var targetIndex = $(this).index();

		prev = false;
		if (currentIndex > targetIndex) {
			prev = true;
		} else {
			valid = validateForm(true);
			if (!valid) {
				var error = $("fieldset label[class='error']:eq(0)");
				var error_fs = $(error).closest("fieldset");

				var errorName = $(error_fs).attr("name");
				var errorIndex = $(error_fs).index();
				if (errorIndex == currentIndex) {
					$(error).get(0).scrollIntoView({behavior: "smooth", block: "end"});
					e.preventDefault()
					return;
				} else {
					if (errorIndex < targetIndex && errorIndex >= currentIndex) {
						targetName = errorName;
						targetIndex = errorIndex;
					}
				}
			}
		}
		
		$("#progressbar li").each(function(index) {
			if (currentIndex < 0) {
				var target = $("fieldset[name='" + targetName + "']");
				$(target).show();
				return;
			}
			
			if (index < targetIndex && !$(this).hasClass("active")) {
				$(this).addClass("active");
			} else {
				if (index > targetIndex && $(this).hasClass("active")) {
					$(this).removeClass("active");
				} else {
					if ((index == targetIndex && currentIndex != targetIndex) || prev) {					
						$(this).addClass("active");
						
						var target = $("fieldset[name='" + targetName + "']");
						$(target).show();

						if ($.fn.DataTable.isDataTable('#quizTable')) {
							$('#quizTable').DataTable().columns.adjust();
						}
	
						$(current).animate(
							{ opacity: 0 },
							{
								step: function (now) {
									inprogress = true;
									opacity = 1 - now;
	
									$(current).css({
										display: "none",
										position: "relative",
									});
									$(target).css({ opacity: opacity });
								},
								duration: 400,
								done: function() {
									inprogress = false;
								}
							}
						);

						prev=false;
					}
					
				}
			}
		});
	});

	$(".previous[name='previous']").click(function (e) {
		if (inprogress) {
			e.preventDefault()
			return;
		}

		current_fs = $(this).parent();
		previous_fs = $(this).parent().prev();

		$("#progressbar li").eq($("fieldset").index(current_fs)).removeClass("active");

		//show the previous fieldset
		previous_fs.show();

		if ($.fn.DataTable.isDataTable('#quizTable')) {
			$('#quizTable').DataTable().columns.adjust();
		}

		//hide the current fieldset with style
		current_fs.animate(
		{ opacity: 0 },
		{
			step: function (now) {
				inprogress = true;
				opacity = 1 - now;

				$(current_fs).css({
					display: "none",
					position: "relative",
				});
				$(previous_fs).css({ opacity: opacity });
			},
			duration: 400,
			done: function() {
				inprogress = false;
			}
		}
		);
	});

	$(".radio-group .radio").click(function () {
		$(this).parent().find(".radio").removeClass("selected");
		$(this).addClass("selected");
	});

	$(".submit").click(function () {
		return false;
	});

	$("#fundraiserbox btn[type='button']").click(function () {
		$("#fundraiserbox").hide();
	});

	$("fieldset[name='challengeqs']").on("click", "input[name='quiz']", function(e) {
		$("input[name='quiz']").closest("tr.selected").removeClass("selected");

		$(this).prop("checked", true);
		$(this).closest("tr").addClass("selected");

		
		$("#selectedQuiz").val($("input[name='quiz']:checked").val());
	});

	$("fieldset[name='challengeqs']").on("click", "tr", function(e) {
		if ($(e.target).is("a") || $(e.target).is("i")) { return; }
		$(this).parent().find("tr.selected").removeClass("selected");

		$(this).find("input[name='quiz']").prop("checked", true);
		$(this).addClass("selected");

		
		$("#selectedQuiz").val($("input[name='quiz']:checked").val());
	});

	$('input[name="credit_prize"]').on('change', function() {
		if ($(this).val() == "free") {
			$("#game_end").val(4);
			$("#game_end option[value!='4']").hide();
		} else {
			$("#game_end option[value!='4']").show();
		}

		if ($(this).val() != "prize") {
			show_prize(false);
			$("#prizeSection label[class='error']").remove();
		} else {
			show_prize(true);
		}

		$("#game_end").trigger("change");

		if ($(this).val() == "free") {
			$('.addFundraiserBlock').addClass('d-none');
			$('.financesBlock').addClass('d-none');
			$('.financesBlockOpposite').removeClass('d-none');
		} else {
			$('.addFundraiserBlock').removeClass('d-none');
			$('.financesBlock').removeClass('d-none');
			$('.financesBlockOpposite').addClass('d-none');
		}
	});
});