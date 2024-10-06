var gamemodeData;
var selectedConfig = 0;
var selectedKit = 0;
var selectedRule = 0;

$(document).ready(function () {
	getGamemodeData($("#gamemode").val());

	$("#gamemode").on("change", function(e) {
		getGamemodeData(this.value);
	});

	$("#refreshConfigs").on("click", function() {
		getGamemodeData($("#gamemode").val());
	});

	$("#gameConfig").on("change", function() {
		selectedConfig = this.selectedIndex;
		selectedKit = 0; selectedRule = 0;

		populateGamemodeInfo();
	});

	$("#kits").on("change", function() {
		selectedKit = this.selectedIndex;

		changeGameKit();
	});

	$("#gameRules").on("change", function() {
		selectedRule = this.selectedIndex;

		changeGameRule();
	});

	function getGamemodeData(gamemode) {
		addImageLoader("#gameModeRules");

		$.ajax({
			method: "GET",
			data: {gamemode: gamemode},
			url: window.location.origin + "/games/getGamemodeConfigs/",
			success: function (result) {
				if (gamemodeData === undefined) {
					gamemodeData = JSON.parse(result);
				} else {
					gamemodeData = JSON.parse(result);
					selectedConfig = 0; selectedKit = 0; selectedRule = 0;
					
					populateGameConfig();
					removeErrors();
				}
			},
			complete: function() {
				$("#gameModeRules").find(".loader").addClass("d-none");
			}
		});
	}

	function populateGameConfig() {
		options = "";

		$("#gameConfig").children().remove().end();

		if (gamemodeData.length > 0) {
			$("#gameModeRules:hidden").show();

			for (var i = 0; i < gamemodeData.length; i++) {
				options += '<option value="' + gamemodeData[i].id + '">' + gamemodeData[i].name + '</option>';
			}
		} else {
			clearGameRules();
			$("#gameModeRules:visible").hide();
		}
		

		$("#gameConfig").append(options);

		populateGamemodeInfo(gamemodeData);
		populateGamemodeKits(gamemodeData);
		populateGamemodeRules(gamemodeData);
	}

	function populateGamemodeInfo() {
		if (gamemodeData.length > 0) {
			info = gamemodeData[selectedConfig];

			clearGameRules();
			for (var key in info) {
				var value = info[key];
				var d = "";

				if (typeof(value) == "object" && value != null) { continue; }

				switch (key) {
					case "arena":
						d = capitalizeEachWord(value);
						break;
					case "play_type":
					case "game_base":
						d = capitalizeFirstChar(value);
						break;
					case "timelimit":
						d = value + " seconds";
						break;
					case "point_value":
						d = (value != null) ? value : 0;
						break;
					case "regen":
					case "hunger":
					case "looting":
						d = (value == 1) ? "True" : "False";
						break;
				}

				$("input[name='" + key + "'").val(d);
			}

			populateGamemodeKits();
			populateGamemodeRules();
		}
	}

	function changeGameKit() {
		var kits = gamemodeData[selectedConfig].kits;
		
		populateInputs(kits[selectedKit], true);
	}

	function populateGamemodeKits() {
		if (gamemodeData.length > 0) {
			var kits = gamemodeData[selectedConfig].kits;

			var options = "";
			for (var i = 0; i < kits.length; i++) {
				var selected = ((i == 0) ? " selected" : "");
				var val = capitalizeEachWord(kits[i].name);
				options += '<option value="' + kits[i].id + '"' + selected + '> Kit ' + (i+1) + " - " + val + '</option>';
			}

			$("#kits").empty();
			$("#kits").append(options);

			populateInputs(kits[selectedKit], true);
		}
	}

	function changeGameRule() {
		var rules = gamemodeData[selectedConfig].rules;
		
		populateInputs(rules[selectedRule]);
	}

	function populateGamemodeRules() {
		if (gamemodeData.length > 0) {
			var rules = gamemodeData[selectedConfig].rules;

			var options = "";
			for (var i = 0; i < rules.length; i++) {
				var selected = ((i == 0) ? " selected" : "");
				var val = capitalizeEachWord(rules[i].rule_type);
				options += '<option value="' + rules[i].rule_id + '"' + selected + '>' + val + '</option>';
			}

			$("#gameRules").empty();
			$("#gameRules").append(options);

			populateInputs(rules[selectedRule]);
		}
	}

	function populateInputs(data, tooltip=false) {
		for (let key in data) {
			var input = $("input[name='" + key + "']");
			var parent = input.closest("div[class*='col']");

			if (data[key] == null || data[key] == "") {
				if (!parent.hasClass("d-none")) {
					parent.addClass("d-none");
				}

				$(input).val("");
			} else {
				if (parent.hasClass("d-none")) {
					parent.removeClass("d-none");
				}

				var val = data[key];
				if (typeof(data[key]) === "object") {
					val = (data[key].custom == 1) ? data[key].name : data[key].item_name;
				}
				
				replaceInputValues(input, key, val);
				updateToolTips(input, data[key], tooltip);
			}		
		}
	}

	function updateToolTips(input, data, tooltip) {
		if (tooltip) {
			var next = $(input).next();
			var p = $(next).find("p");
			var list = $(next).find("ul");

			$(p).text(capitalizeEachWord(data.item_name) + " - x" + data.amount + " - " + ((data.custom == 1) ? "Custom" : "Basic"));
			$(list).empty();

			var newlist = "<li>Item Type: " + ((data.item_name != null) ? data.item_name : "") + "</li>" + 
						"<li>Name Color: " + ((data.name_color != null) ? data.name_color : "") + "</li>" + 
						"<li>Lore: " + ((data.lore != null) ? data.lore : "") + "</li>" + 
						"<li>Lore Color: " + ((data.lore_color != null) ? data.lore_color : "") + "</li>" + 
						"<li>Show Enchants: " + ((data.show_enchants == 1) ? "Yes" : "No") + "</li>" + 
						"<li>Unbreakable: " + ((data.unbreakable == 1) ? "Yes" : "No") + "</li>";

			$(list).append(newlist);
		}
	}

	function replaceInputValues(input, key, val) {
		switch(key) {
			case "checkpoint":
				$(input).val((val == 1) ? "Yes" : "No");
				break;
			default:
				$(input).val(capitalizeEachWord(val));
				break;
		}
		
	}

    function removeErrors() {
        $("#minecraftSection [class*='error']").each(function() {
            if ($(this).is("label")) {
                $(this).remove();
            } else if ($(this).is("select")) {
                $(this).removeClass("error");
            }
        });
    }

	function clearGameRules() {
		$(":input", "#gameModeRules").val("");
		$("#kits").children().remove().end();
		$("#gameRules").children().remove().end();
	}

	function capitalizeEachWord(words) {
		if (words === undefined || words === null) { return ""; }
		var test = words.replace(/_/g, " ").split(' ').map(capitalizeFirstChar).join(' ')
		return test;
	}

	function capitalizeFirstChar(str) {
		return str.charAt(0).toUpperCase() + str.slice(1);
	}

	//create config form
	var formdata = new FormData();
	setDefaultFormData();

	function setDefaultFormData() {
		formdata.append("visibleKits", 9-$(".kit.d-none").length);
		formdata.append("visibleRules", 5-$(".rule.d-none").length);

		$('#newMinecraftConfig').find("input").each(function() {
			var name = $(this).attr("name");
			var val = $(this).val();
			formdata.append(name, val);
		});
	
		$('#newMinecraftConfig').find("select").each(function() {
			var name = $(this).attr("name");
			var val = $(this).find("option:selected").val();

			if (!name.includes("new_gamemode")) {
				formdata.append(name, val);
			}
		});
	}
	
	function validateForm() {
		$validator = $("#newMinecraftConfig").validate({
			rules: {
				new_gamemode: {
					required: true
				},
				new_gameConfig: {
					required: true,
					minlength: 10,
				},
			
				new_arena: {
					required: {
						depends: function(element) {
							if ($("#new_gameModeRules").is(":visible")) {
								return true;		
							} else {
								return false;
							}
						}
					}
				},
				new_play_type: {
					required: {
						depends: function(element) {
							if ($("#new_gameModeRules").is(":visible")) {
								return true;		
							} else {
								return false;
							}
						}
					}
				},
				new_game_base: {
					required: {
						depends: function(element) {
							if ($("#new_gameModeRules").is(":visible")) {
								return true;		
							} else {
								return false;
							}
						}
					}
				},
				new_timelimit: {
					required: {
						depends: function(element) {
							if ($("#new_gameModeRules").is(":visible")) {
								return true;		
							} else {
								return false;
							}
						}
					}
				},
				new_point_value:{
					required: {
						depends: function(element) {
							if ($("#new_gameModeRules").is(":visible")) {
								return true;		
							} else {
								return false;
							}
						}
					}
				},
				new_hunger:{
					required: {
						depends: function(element) {
							if ($("#new_gameModeRules").is(":visible")) {
								return true;		
							} else {
								return false;
							}
						}
					}
				},
				new_regen:{
					required: {
						depends: function(element) {
							if ($("#new_gameModeRules").is(":visible")) {
								return true;		
							} else {
								return false;
							}
						}
					}
				},
				new_looting: {
					required: {
						depends: function(element) {
							if ($("#new_gameModeRules").is(":visible")) {
								return true;		
							} else {
								return false;
							}
						}
					}
				},	
				new_kit_name_1: {
					required: {
						depends: function(element) {
							if ($("#new_gameModeRules").is(":visible")) {
								return true;		
							} else {
								return false;
							}
						}
					}
				},
				new_kit_name_2: {
					required: {
						depends: function(element) {
							if ($("#new_gameModeRules").is(":visible")) {
								if ($(element).closest(".kit").is(":visible")) {
									return true;		
								} else {
									return false;
								}
							} else {
								return false;
							}
						}
					}	
				},
				new_kit_name_3: {
					required: {
						depends: function(element) {
							if ($("#new_gameModeRules").is(":visible")) {
								if ($(element).closest(".kit").is(":visible")) {
									return true;		
								} else {
									return false;
								}
							} else {
								return false;
							}
						}
					}	
				},
				new_kit_name_4: {
					required: {
						depends: function(element) {
							if ($("#new_gameModeRules").is(":visible")) {
								if ($(element).closest(".kit").is(":visible")) {
									return true;		
								} else {
									return false;
								}
							} else {
								return false;
							}
						}
					}
				},
				new_kit_name_5: {
					required: {
						depends: function(element) {
							if ($("#new_gameModeRules").is(":visible")) {
								if ($(element).closest(".kit").is(":visible")) {
									return true;		
								} else {
									return false;
								}
							} else {
								return false;
							}
						}
					}
				},
				new_kit_name_6: {
					required:  {
						depends: function(element) {
							if ($("#new_gameModeRules").is(":visible")) {
								if ($(element).closest(".kit").is(":visible")) {
									return true;		
								} else {
									return false;
								}
							} else {
								return false;
							}
						}
					}
				},
				new_kit_name_7: {
					required: {
						depends: function(element) {
							if ($("#new_gameModeRules").is(":visible")) {
								if ($(element).closest(".kit").is(":visible")) {
									return true;		
								} else {
									return false;
								}
							} else {
								return false;
							}
						}
					}
				},
				new_kit_name_8: {
					required: {
						depends: function(element) {
							if ($("#new_gameModeRules").is(":visible")) {
								if ($(element).closest(".kit").is(":visible")) {
									return true;		
								} else {
									return false;
								}
							} else {
								return false;
							}
						}
					}
				},
				new_kit_name_9: {
					required: {
						depends: function(element) {
							if ($("#new_gameModeRules").is(":visible")) {
								if ($(element).closest(".kit").is(":visible")) {
									return true;		
								} else {
									return false;
								}
							} else {
								return false;
							}
						}
					}
				},
				new_rule_name_1: {
					required: {
						depends: function(element) {
							if ($("#new_gameModeRules").is(":visible")) {
								if ($(element).closest(".rule").is(":visible")) {
									return true;		
								} else {
									return false;
								}
							} else {
								return false;
							}
						}
					}
				},
				new_rule_name_2: {
					required: {
						depends: function(element) {
							if ($("#new_gameModeRules").is(":visible")) {
								if ($(element).closest(".rule").is(":visible")) {
									return true;		
								} else {
									return false;
								}
							} else {
								return false;
							}
						}
					}
				},
				new_rule_name_3: {
					required: {
						depends: function(element) {
							if ($("#new_gameModeRules").is(":visible")) {
								if ($(element).closest(".rule").is(":visible")) {
									return true;		
								} else {
									return false;
								}
							} else {
								return false;
							}
						}
					}
				},
				new_rule_name_4: {
					required: {
						depends: function(element) {
							if ($("#new_gameModeRules").is(":visible")) {
								if ($(element).closest(".rule").is(":visible")) {
									return true;		
								} else {
									return false;
								}
							} else {
								return false;
							}
						}
					}
				},
				new_rule_name_5: {
					required: {
						depends: function(element) {
							if ($("#new_gameModeRules").is(":visible")) {
								if ($(element).closest(".rule").is(":visible")) {
									return true;		
								} else {
									return false;
								}
							} else {
								return false;
							}
						}
					}
				}
			}
		});

		return $('#newMinecraftConfig').valid();
	}

	$("#new_kits select").each(function() {
		$(this).selectize({
			sortField: "data-value",
		});
	});

	$("#new_gamerules select").each(function() {
		var name = $(this).attr("name");

		if (name.includes("new_mob_type") || name.includes("new_item_type")) {
			$(this).selectize({
				sortField: "data-value",
			});
		}
	});

	$("#new_gamemode").on("change", function() {
		$("#new_gameModeRules").removeClass("d-none");
		$("#createNewConfig").parent().removeClass("d-none");
	});

	$("#new_arena").on("change", function(e) {
		var selected = $(this).find("option:selected");
		var minx = $(selected).data("minx");
		var maxx = $(selected).data("maxx");
		var miny = $(selected).data("miny");
		var maxy = $(selected).data("maxy");
		var minz = $(selected).data("minz");
		var maxz = $(selected).data("maxz");

		$("#new_gamerules .rule input[name*='new_location']").each(function() {
			var name = $(this).attr("name");
			if (name.includes("x")) {
				$(this).attr("min", minx);
				$(this).attr("max", maxx);
				$(this).val(clamp($(this).val(), minx, maxx));
			} else if (name.includes("y")) {
				$(this).attr("min", miny);
				$(this).attr("max", maxy);
				$(this).val(clamp($(this).val(), miny, maxy));
			} else {
				$(this).attr("min", minz);
				$(this).attr("max", maxz);
				$(this).val(clamp($(this).val(), minz, maxz));
			}
		})
	});

	$("#new_game_base").on("change", function(e) {
		var selected = $(this).find("option:selected");
		switch (selected.val()) {
			case "point":
				$("#new_point_value:visible").parent().addClass("d-none");
				$("#new_timelimit:hidden").parent().removeClass("d-none");

				$("#new_timelimit").attr("min", 10);
				$("#new_timelimit").attr("max", 600);
				$("#new_timelimit").val(clamp($("#new_timelimit").val(), 10, 600));
				break;
			case "time":
				$("#new_point_value:hidden").parent().removeClass("d-none");
				$("#new_timelimit:hidden").parent().removeClass("d-none");

				$("#new_timelimit").attr("min", 1);
				$("#new_timelimit").attr("max", 10000);
				$("#new_timelimit").val(clamp($("#new_timelimit").val(), 1, 10000));
				break;
			case "judge":
				$("#new_point_value:hidden").parent().removeClass("d-none");
				$("#new_timelimit:visible").parent().addClass("d-none");
				break;
		}
	});

	$("#new_gamerules select[name*='new_rule_type']").on("change", function(e) {
		var parent = $(this).parent().parent();

		switch (parseInt(this.value)) {
			case 0:
				$(parent).find("label").each(function() {
					if ($(this).attr("for").includes("new_rule_type")) { 
						return; 
					} else {
						$(this).parent().addClass("d-none");
					}
				});
				break;
			case 1:
				$(parent).find("input").each(function() {
					if ($(this).attr("name") === undefined) { return; }

					var name = $(this).attr("name");
					if (name.includes("new_wave_D") || name.includes("new_wave_Int")) {
						$(this).parent().removeClass("d-none");
					} else {
						$(this).parent().addClass("d-none");
					}
				});

				$(parent).find("select").each(function() {
					var name = $(this).attr("name");
					if (name.includes("new_rule_type") || name.includes("new_mob_type")) {
						$(this).parent().removeClass("d-none");
					} else {
						$(this).parent().addClass("d-none");
					}
				});
				break;
			case 2:
			case 3:
				$(parent).find("input").each(function() {
					if ($(this).attr("name") === undefined) { return; }

					var name = $(this).attr("name");
					if (name.includes("new_value")) {
						$(this).parent().removeClass("d-none");
					} else {
						$(this).parent().addClass("d-none");
					}
				});

				$(parent).find("select").each(function() {
					var name = $(this).attr("name");
					if (!name.includes("new_rule_type")) {
						$(this).parent().addClass("d-none");
					}
				});
				break;
			case 4:
				$(parent).find("input").each(function() {
					if ($(this).attr("name") === undefined) { return; }

					var name = $(this).attr("name");
					if (name.includes("new_value")) {
						$(this).parent().removeClass("d-none");
					} else {
						$(this).parent().addClass("d-none");
					}
				});

				$(parent).find("select").each(function() {
					var name = $(this).attr("name");
					if (name.includes("new_rule_type") || name.includes("new_item_type")) {
						$(this).parent().removeClass("d-none");
					} else {
						$(this).parent().addClass("d-none");
					}
				});
				break;
			case 5:
				$(parent).find("input").each(function() {
					if ($(this).attr("name") === undefined) { return; }
					
					var name = $(this).attr("name");
					if (name.includes("new_location") || name.includes("new_checkpoint")) {
						$(this).parent().removeClass("d-none");
					} else {
						$(this).parent().addClass("d-none");
					}
				});

				$(parent).find("select").each(function() {
					var name = $(this).attr("name");
					if (name.includes("new_rule_type")) {
						$(this).parent().removeClass("d-none");
					} else {
						$(this).parent().addClass("d-none");
					}
				});
				break;
			case 6:
				$(parent).find("input").each(function() {
					if ($(this).attr("name") === undefined) { return; }

					var name = $(this).attr("name");
					if (name.includes("new_starting") || name.includes("new_perimeter") || name.includes("new_judge")) {
						$(this).parent().removeClass("d-none");
					} else {
						$(this).parent().addClass("d-none");
					}
				});

				$(parent).find("select").each(function() {
					var name = $(this).attr("name");
					if (name.includes("new_rule_type") || name.includes("new_item_type") || name.includes("new_mob_type") || name.includes("new_judge")) {
						$(this).parent().removeClass("d-none");
					} else {
						$(this).parent().addClass("d-none");
					}
				});
				break;
		}

		$(parent).find("input[name*='new_value']").parent().removeClass("d-none");

		$("#new_gameModeRules").removeClass("d-none");
	});

	$("#newMinecraftConfig").on("change", function(e) {
		var name = $(e.target).attr("name");
		var val = $(e.target).val();

		if (name.includes("new_gamemode")) { return; }

		if (formdata.get(name) != val) {
			$("#clearNewConfig").removeClass("d-none");
		} else {
			$("#clearNewConfig").addClass("d-none")
		}
	});

	$("#newMinecraftConfig .clear").on("click", function() {
		showSweetConfirm("Are you sure you want to clear all fields?", "Attention", $icon='warning', function(confirmed) {
            if (!confirmed) {
                e.preventDefault();
            } else {
                resetFields();
				$(this).parent().addClass("d-none");
            }
        });	
	});

	$(".newKit").on("click", function(e) {
		var num = $("#new_kits .kit:visible").length;
		if (num >= 9) { 
			e.preventDefault();
		} else {
			var kit = $("#new_kits .kit:hidden:first");
			
			kit.removeClass("d-none");
			scrollIntoView(kit);
			
			if (num == 8) {
				$(".newKit").parent().addClass("d-none");
			}
		}
	});

	$("#new_kits .minimize").on("click", function() {
		var minimizeSection = $(this).closest(".kit").find(".minimizeSection");

		minimize(minimizeSection);
	});

	$("#new_kits .remove").on("click", function(e) {
		$(this).closest(".kit").addClass("d-none");

		removeKits($("#new_kits .kit:visible").length);
		resetFields($(this).closest(".kit"));
	});

	$(".newRule").on("click", function(e) {
		var num = $("#new_gamerules .rule:visible").length;
		if (num >= 5) { 
			e.preventDefault();
		} else {
			var rule = $("#new_gamerules .rule:hidden:first");
			
			rule.removeClass("d-none");
			scrollIntoView(rule);
			
			if (num == 4) {
				$(".newRule").parent().addClass("d-none");
			}
		}
	});

	$("#new_gamerules .minimize").on("click", function() {
		var minimizeSection = $(this).closest(".rule").find(".minimizeSection");

		minimize(minimizeSection);
	});

	$("#new_gamerules .remove").on("click", function(e) {
		$(this).closest(".rule").addClass("d-none");

		removeRules($("#new_gamerules .rule:visible").length);
		resetFields($(this).closest(".rule"));
	});

	$("#createNewConfig").on("click", function() {
		var valid = validateForm();
		if (valid) {
			showSweetConfirm("Are you sure you want create a new config? You'll have to get it approved before you can create a fundraiser with it.", "Attention", $icon='warning', function(confirmed) {
				if (!confirmed) {
					e.preventDefault();
				} else {
					var data = {};

					var infodata = {};
					$('#newMinecraftConfig').find(".info:visible").each(function(index) {
						if (index > 1) { return false; }
						infodata = getFormData($(this), {gamemode: $("#new_gamemode").val(), new_gameconfig: $("#new_gameConfig").val()});
					});

					var kitdata = {};
					$('#newMinecraftConfig').find(".kit:visible").each(function(index) {
						if (index > 9) { return false; }
						kitdata["kit_" + (index+1)] = getFormData($(this));
					});

					var ruledata = {};
					$('#newMinecraftConfig').find(".rule:visible").each(function(index) {
						if (index > 5) { return false; }
						ruledata["rule_" + (index+1)] = getFormData($(this));
					});

					data.info = infodata;
					data.kits = kitdata;
					data.rules = ruledata;

					$.ajax({
						method: "POST",
						data: data,
						url: window.location.origin + "/games/addNewGamemodeConfig/",
						success: function (result) {
							result = JSON.parse(result);

							if (result.status == "success") {
								$('#createConfig').modal('toggle');
								showSweetAlert("Config created! Make sure to get it approved!", 'Success!', 'success');

								selectedConfig = 0; selectedKit = 0; selectedRule = 0;
								resetFields();
							} else {
								showSweetAlert("Your config wasn't able to be created. Make sure you don't have any errors!", 'Whoops!', 'error');
							}
						}
					});
				}
			});	
		}
	});

	function getFormData(formSection, subdata={}) {
		$(formSection).find("label:visible").each(function() {
			var element = $(this).next();

			var name = $(element).attr("name");
			var val = $(element).val();
			subdata[name] = val;			
		});
		
		return subdata;
	}

	function resetFields(field=null) {
		if (field === null) {
			for(var pair of formdata.entries()) {
				var element = $("[name='" + pair[0] + "']");
				var elementClass = $(element).attr("class");
	
				if ((elementClass !== undefined && elementClass != null) && elementClass.includes("selectize")) {
					$(element).selectize()[0].selectize.setValue(pair[1]);
				} else {
					$(element).val(pair[1]);
					$(element).trigger("change");
				}
			}

			var kitLimit = formdata.get("visibleKits");
			$(".kit").each(function(index) {
				if ((index+1) > kitLimit) {
					$(this).addClass("d-none");
				}
			});

			var ruleLimit = formdata.get("visibleRules");
			$(".rule").each(function(index) {
				if ((index+1) > ruleLimit) {
					$(this).addClass("d-none");
				}
			});
		} else {
			$(field).find("label").each(function() {
				var next = $(this).next();
				var nextClass = $(next).attr("class");

				var name = next.attr("name");
				var val = formdata.get(name);

				if ((nextClass !== undefined && nextClass != null) && nextClass.includes("selectize")) {
					$(next).selectize()[0].selectize.setValue(val);
				} else {
					$(next).val(val);
					$(next).trigger("change");
				}
			});
		}
		
	}

	function removeRules(num) {
		if (num == 1) { return; }

		if (num < 5) {
			$(".newRule").parent().removeClass("d-none");
		} else {
			$("#new_rules .kit:visible").each(function(index) {
				if (index > 5) {
					$(this).remove();
				}
			})
		}
	}

	function removeKits(num) {
		if (num == 1) { return; }

		if (num < 9) {
			$(".newKit").parent().removeClass("d-none");
		} else {
			$("#new_kits .kit:visible").each(function(index) {
				if (index > 9) {
					$(this).remove();
				}
			})
		}
	}

	function addImageLoader(imagePreview) {
		if ($(imagePreview).find(".loader").length > 0) {
			$(imagePreview).find(".loader").removeClass("d-none");
		} else {
			$(imagePreview).append('<div class="loader"><div class="imageLoader"></div></div>');
		}
	}

	function minimize(minimizeSection) {
		if ($(minimizeSection).is(":visible")) {
			$(this).find("i").attr("class", "fa fa-plus");
		} else {
			$(this).find("i").attr("class", "fa fa-minus");
		}

		$(minimizeSection).slideToggle();
	}

	function scrollIntoView(scrollTo) {
		var container = $("#createConfig .modal-content");

		$('#createConfig .modal-content').animate({
			scrollTop: (scrollTo.offset().top - container.offset().top + container.scrollTop() - (scrollTo.height() / 2)),
		});
	}

	function clamp(num, min, max) {
		return Math.min(Math.max(num, min), max)
	}	
});