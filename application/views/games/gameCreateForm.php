<content class="content" id="creategameform">
    <h1><?php if (!isset($game->name)) { ?>
        Create a <?php echo ucfirst($type); ?> Game
        <?php } else { ?>
        Edit Game
        <?php } ?>
    </h1>

    <script>
        function show_prize(toggle) {
            if (toggle) {
                $(".show_price").removeClass("d-none");
            } else {
                $(".show_price").addClass("d-none");
            }
        }
        <?php if (!isset($game->name)) {?>
            $(document).ready(function() {
            show_prize(false);
            });
        <?php }?>

        isFundraiserExistAtEdit = false;
    </script>

    <?php if (isset($game->name)) { ?>
        <script>

            $(document).ready(function() {
                isFundraiserExistAtEdit = '<?php echo !empty($game->fundraise_slug) ? $game->fundraise_slug : false ;?>';      

                <?php $game_image = getImagePathSize($game->Game_Image,'create_a_game_upload_thumbnails_for_game_and_prize'); ?>          
                var gameImage = "<?= $game_image["image"] ?>";
                var gameImageFallback = "<?= $game_image["fallback"] ?>";

                $('#gametitle').val('<?php echo $game->name; ?>');
                $('#gameImagePreview').attr('src', gameImage);
                $('#gameInfoImage').val(gameImage);
                $('#gametags').val('<?php echo $game->game_tags . ','; ?>');
                
                <?php if ($type == '2048') { ?>
                    var setHowToWin = "<?php echo $game->time_limit == 1 ? 'timelimit' : 'highest'; ?>";
                    $("input[name=howToWin][value=" + setHowToWin + "]").prop('checked', 'checked');
                <?php } else if ($type == 'puzzle') { ?>
                    var setHowToWin = "<?php echo $game->time_limit == 1 ? 'timelimit' : 'steps'; ?>";
                    $("input[name=howToWin][value=" + setHowToWin + "]").prop('checked', 'checked');
                <?php } else if ($type == 'challenge') { ?>
                    var setHowToWin = "<?php echo $game->Quiz_rules; ?>";
                    $("input[name=optionsCheckboxes][value=" + setHowToWin + "]").prop('checked', 'checked');
                <?php } ?>
                $('#game_tile_goal').val('<?php echo $game->game_tile_goal; ?>');
                $('#winner_count').val('<?php echo $game->winner_count; ?>');
                $('#game_end').val('<?php echo (int)$game->winner_option; ?>');
                if ($('#game_end').val() == 2) {
		        	$("#duration_123").hide();
	        		$('#end_date').hide();
	        	} else if ($('#game_end').val() == 3) {
			        $("#duration_123").show();
	        		$('#end_date').hide();
		        } else if ($('#game_end').val() == 4) {
			        $('#duration_123').hide();
		        	$('#end_date').show();
        		}

                if ($('#game_end').val() == 3 || $('#game_end').val() == 4) {
                    $("#timeDisclaimer").removeClass("d-none");
                } else {
                    $("#timeDisclaimer").addClass("d-none");
                }

                $('#days').val(<?php echo (int)$game->End_Day; ?>);
                $('#hours').val('<?php echo (int)$game->End_Hour; ?>');
                $('#min').val('<?php echo (int)$game->End_Minute; ?>');

                ///////// finance data on edit game
                $('#winner_count').val(<?php echo (int)$game->winner_count; ?>);
                $('#fundraise_goal').val('<?php echo (float)$game->fundraiseGoal; ?>');
                $('#beneficiary_percent').val('<?php echo $game->charityPercentage; ?>');
                $('#cost_to_play').val('<?php echo $game->credit_cost; ?>');
                //////////

                var donationOption = <?php echo $game->donationOption; ?> ;
                $("input[name=donationOption][value='" + donationOption + "']").attr('checked', 'checked');
                
                var setGameStat = '<?php echo $game->game_status; ?>'; //Publish Game//Make Game Live
                $("input[name=publishstat][value='" + setGameStat + "']").attr('checked', 'checked');

                var credit_type = '<?php echo $game->credit_type; ?>';
                $("input[name=credit_prize][value='" + credit_type + "']").attr('checked', 'checked');
                if (credit_type === 'prize') {                    
                    show_prize(true);
                    $('#prize_value').val('<?php echo $game->value_of_the_game; ?>');
                    $("#prizeTitle").val('<?php echo $game->prize_title; ?>');

                    $("input[name=prize_type][value='<?php echo $game->prize_type; ?>']").prop('checked', true);
                    <?php if (isset($game->prize_image_data[0]) && $game->prize_image_data[0]->main_image == '1') { ?>
                        $('#prizeImage').val('<?= $game->prize_image_data[0]->prize_image["image"]; ?>');
                        $('#prizeImagePreview').attr('src', '<?= $game->prize_image_data[0]->prize_image["image"]; ?>');

                        <?php if (count($game->prize_image_data) > 1) {
                            foreach ($game->prize_image_data as $index => $pimg) {
                                if ($index > 0) {
                        ?>
                                    $('.prize_icons').append(
                                        "<div class='galleryth'><img src='<?= $pimg->prize_image["image"] ?>' onerror='imgError(this, '<?= $pimg->prize_image["fallback"] ?>')' alt='<?php echo $game->prize_title; ?>'><a class='thremove'><i class='fa fa-times' aria-hidden='true'></i></a><input type='hidden' name='prizeImagesHidden[]' value='<?= $pimg->prize_image["image"] ?>'></div>"
                                    );
                        <?php }
                            }
                        }
                        ?>
                    <?php } ?>
                    // prize_image_data
                } else if (credit_type === 'free') {
                    $('.addFundraiserBlock').addClass('d-none');
                    $('.financesBlock').addClass('d-none');
                    $('.financesBlockOpposite').removeClass('d-none');
                    show_prize(false);
                } else {
                    $('.addFundraiserBlock').removeClass('d-none');
                    show_prize(false);
                }
                <?php
                if ($type == 'challenge') {
                    $checkbox = $game->Quiz_rules;
                } else if ($type == 'puzzle') { ?>
                    <?php $puzzle_image = getImagePathSize($game->Image,'puzzle_image_create_game'); ?>    

                    // $('#puzzleImagePreview')[0].onerror = (e) => {
                    //     imgError($('#puzzleImagePreview')[0], '<?= $puzzle_image["fallback"] ?>');
                    // }
                    $('#puzzleImage').val('<?= $puzzle_image["image"]; ?>');
                    $('#puzzleImagePreview').attr('src', '<?= $puzzle_image["image"]; ?>');
                    
                    $('#gamedifficulty').val('<?php echo $game->Level; ?>');
                <?php
                    if ($game->Quiz_rules == '1') {
                        $checkbox = 'time_limit';
                    } elseif ($game->Quiz_rules == '2') {
                        $checkbox = 'steps';
                    }
                } ?>

                var Quiz_rules = '<?php echo @$checkbox; ?>';
                $("input[name=optionsCheckboxes][value='" + Quiz_rules + "']").attr('checked', 'checked');
            });
        </script>
    <?php } ?>
    <div class="col-md-12 mx-0" style="padding: 0;">
        <form id="creategameform2" class="wizard-card">
            <!-- progressbar -->
            <ul id="progressbar" <?php if ($type == 'challenge') echo 'class="plus1"'; ?>>
                <li class="active" id="information"><strong>Info</strong></li>
                <?php if ($type == 'challenge') { ?>
                    <li id="challengeqs"><strong>Quiz</strong></li>
                <?php } ?>
                <li id="rules"><strong>Rules</strong></li>
                <li id="finances"><strong>Finances</strong></li>
                <li id="publish"><strong>Create!</strong></li>
            </ul>
            <!-- fieldsets -->
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <fieldset name="information">
                            <input type="button" name="next" class="topdirectional next action-button scroll-top" value="Next Step" /><i class="fas fa-arrow-right topdirectional"></i>
                            <div class="form-card">
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="row text-center pw-form border p-1 pb-0 mx-1">
                                            <div class="col-12">
                                                <h2 class="fs-title">Game For:</h2>
                                            </div>
                                            <div class="col-4">
                                                <label class="switch">
                                                    <input id="credits" type="radio" value="credit" name="credit_prize" class="abc large-radio credit_type" checked="checked">
                                                </label>
                                                <p><strong>Cash</strong></p>
                                            </div>
                                            <div class="col-4">
                                                <label class="switch">
                                                    <input id="prize" class="abc large-radio credit_type" type="radio" value="prize" name="credit_prize">
                                                </label>
                                                <p><strong>Prize</strong></p>
                                            </div>
                                            <div class="col-4">
                                                <label class="switch">
                                                    <input id="free" class="abc large-radio credit_type" type="radio" value="free" name="credit_prize">
                                                </label>
                                                <p><strong>Free</strong></p>
                                            </div>
                                        </div>
                                        <br>
                                        <h2 class="fs-title">Game Thumbnail</h2>
                                        <div class="container">
                                            <div class="img-upload">
                                                <div class="img-edit">
                                                    <input type='file' name="dummyGameImg" id="imageUpload" accept=".png, .jpg, .jpeg" class="commonImageUpload" show-preview-on="gameImagePreview" set-hidden-value="gameInfoImage" />
                                                    <input type="hidden" name="gameInfoImage" id="gameInfoImage" class="gameInfoImage" required />
                                                    <label for="imageUpload"></label>
                                                </div>
                                                <div class="img-preview">
                                                    <img id="gameImagePreview" class="imagePreview" src="<?php $image = getImagePathSize("", "image_upload_placeholder"); echo $image["image"]; ?>" onerror="imgError(this, '<?= $image['fallback']; ?>');">
                                                </div>
                                            </div>
                                            <div id="mainImgError"></div>
                                        </div>
                                        <br>
                                    </div>
                                    <div class="col-lg-8">
                                        <h2 class="fs-title">Game Information</h2>
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>
                                                        Title your Game <small>(10 word max.)</small>
                                                        <div class="mytooltip"><i class="fa fa-question-circle new-tip" data-toggle="tooltip" data-placement="right" title="" aria-hidden="true"></i><span class="tooltiptext">The game icon and title should create mystery and/or excitement so people want to play</span></div>
                                                    </label>
                                                    <input class="form-control validate" id="gametitle" name="gametitle" type="text" value="" required>
                                                    <div id="gameTitleError"></div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>
                                                        Game Tags <small>(comma separated)</small>
                                                        <div class="mytooltip"><i class="fa fa-question-circle new-tip" data-toggle="tooltip" data-placement="right" title=""></i><span class="tooltiptext">Tags enable players to search for your games more easily. For anything location-based use your town or city.</span></div>
                                                    </label>
                                                    <input data-role="tagsinput" name="gametags" id="gametags" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>
                                                Describe your Game in Detail
                                                <div class="mytooltip"><i class="fa fa-question-circle new-tip" data-toggle="tooltip" data-placement="right" title=""></i><span class="tooltiptext">The game description should be accurate and complete, or people will feel cheated.</span></div>
                                            </label>
                                            <textarea class="form-control" rows="5" name="gamedescription" id="gamedescription" required>
                                                <?php if (isset($game->game_desc)) {
                                                    echo $game->game_desc;
                                                } ?>
                                                </textarea>
                                            <div id="gameDescriptionError"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="show_price">
                                    <div class="row" id='prizeSection'>
                                        <div class="col-lg-4">
                                            <h2 class="fs-title">Upload Prize Photo(s)</h2>
                                            <label>Prize Featured Thumbnail</label>
                                            <div class="img-upload">
                                                <div class="img-edit">
                                                    <input type='file' name="dummyPrizeImg" id="prizeImageUpload" accept=".png, .jpg, .jpeg" class="commonImageUpload" show-preview-on="prizeImagePreview" set-hidden-value="prizeImage" />
                                                    <input type="hidden" name="prizeImage" class="prizeImage" id="prizeImage" required />
                                                    <label for="prizeImageUpload"></label>
                                                </div>
                                                <div class="img-preview">
                                                    <img id="prizeImagePreview" class="imagePreview" src="<?php $image = getImagePathSize("", "image_upload_placeholder"); echo $image["image"]; ?>" onerror="imgError(this, '<?= $image['fallback']; ?>');">
                                                </div>
                                            </div>
                                            <div id="prizeImgError"></div>
                                            <br>
                                            <span style="font-weight: 700; color: #666666; font-size: 14px;">Add Prize Gallery: </span><input type="file" id="prize_images" name="prize_images[]" multiple show-preview-on="prizeImagesLable" set-hidden-value="prizeImagesLable" style="display: block;">
                                            <div class="prizeImagesError"></div>
                                            <div class="prizeImagesLable"></div>
                                            <div class="prize_icons"></div>
                                            <br>
                                        </div>
                                        <div class="col-lg-8">
                                            <h2 class="fs-title">Prize Information</h2>
                                            <div class="form-group">
                                                <label>
                                                    Title your Prize <small>(10 word max.)</small>
                                                    <div class="mytooltip"><i class="fa fa-question-circle new-tip" data-toggle="tooltip" data-placement="right" title=""></i><span class="tooltiptext">The prize image, title, and description should be accurate and complete.</span></div>
                                                </label>
                                                <input class="form-control" name="prizeTitle" type="text" value="" id="prizeTitle">
                                                <div id="prizeTitleError"></div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>
                                                            Describe your prize in detail
                                                            <div class="mytooltip"><i class="fa fa-question-circle new-tip" data-toggle="tooltip" data-placement="right" title=""></i><span class="tooltiptext">The prize image, title, and description should be accurate and complete.</span></div>
                                                        </label>
                                                        <textarea class="form-control" name="prizeDescription" id="prizeDescription" placeholder="" rows="8">
                                                                <?php if (isset($game->prize_description)) {
                                                                    echo $game->prize_description;
                                                                } ?>
                                                            </textarea>
                                                        <div id="prizeDescriptionError"></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label>
                                                            How will the prize be awarded?
                                                            <div class="mytooltip"><i class="fa fa-question-circle new-tip" data-toggle="tooltip" data-placement="right" title=""></i><span class="tooltiptext">Examples: Shipped via 2-day? Personal delivery to local area? Sent in an enclosed trailer? Pickup only?</span></div>
                                                        </label>
                                                        <textarea class="form-control" rows="8" name="prizeSpecification" id="prizeSpecification">
                                                                <?php if (isset($game->prize_specification)) {
                                                                    echo $game->prize_specification;
                                                                } ?>
                                                            </textarea>
                                                        <div id="prizeSpecificationError"></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="custom-control custom-radio custom-control-inline">
                                                        <input class="custom-control-input" name="prize_type" id="prize_type_product" type="radio" value="Product" checked="checked">
                                                        <label class="custom-control-label" for="prize_type_product">Is this a product?</label>
                                                    </div>
                                                    <div class="custom-control custom-radio custom-control-inline">
                                                        <input class="custom-control-input" name="prize_type" id="prize_type_service" type="radio" value="Service">
                                                        <label class="custom-control-label" for="prize_type_service">Or a service?
                                                            <div class="mytooltip">
                                                                <i class="fa fa-question-circle new-tip" data-toggle="tooltip" data-placement="right" title="" aria-hidden="true"></i>
                                                                <span class="tooltiptext">Products are smartphones and motorcycles. Services are lessons and experiences.</span>
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="addFundraiserBlock pt-1">
                                    <h2 class="fs-title text-center mt-1">Supporting Beneficiary</h2>
                                    <br>
                                    <div class="row justify-content-center text-center mb-2">
                                        <div class="col-md-2">
                                            <label>
                                                <h4>Player donates to:</h4>
                                            </label>
                                        
                                        </div>
                                        <div class="col-md-3">
                                            <label><div class="mytooltip"><i class="fa fa-question-circle new-tip" data-toggle="tooltip" data-placement="auto" title="Player is restricted to donate to the beneficiary Creator has chosen for the game."></i></div>
                                                Creator's Beneficiary 
                                                <input class="large-radio" type="radio" name="donationOption" id="donateToCreatorFundraiser" value="1" checked="checked">
                                            </label>
                                        </div>
                                        <div class="col-md-3">
                                            <label><div class="mytooltip"><i class="fa fa-question-circle new-tip" data-toggle="tooltip" data-placement="auto" title="Player can donate to any beneficiary of their choice."></i></div>
                                                Player's Beneficiary 
                                                <input class="large-radio" type="radio" name="donationOption" id="donateToPlayerFundraiser" value="2">
                                            </label>
                                        </div>
                                    </div>
                                    <?php $this->load->view('fundraisers/partials/defaultFundraiser', array('default_fundraiser' => isset($default_fundraiser) ? $default_fundraiser : $game->selected_fundraiser, 'select_fundraiser' => true)); ?>
                                </div>
                            </div>
                            <input type="button" name="next" class="btn blue next action-button scroll-top" value="Next Step" />
                            <?php if (isset($game->name)) { ?>
                                <input type="button" name="confBtn" class="btn blue action-button confirm scroll-top" value="Confirm" />
                            <?php } ?>
                        </fieldset>

                        <?php if ($type == 'challenge') { ?>
                            <fieldset name="challengeqs">
                                <?php if (isset($game_quiz)) { ?>
                                    <input type="button" name="confBtn" class="action-button" id="confirm" value="Confirm" />
                                <?php } ?>

                                <i class="fas fa-arrow-left topdirectional"></i> <input class="previous action-button-previous topdirectional scroll-top" name="previous" type="button" value="Previous" /> &nbsp; | &nbsp; 
                                <input class="next action-button topdirectional scroll-top" name="next" type="button" value="Next Step" /> <i class="fas fa-arrow-right topdirectional"></i>

                                <div class="form-card p-2">
                                    <div class ="text-center">
                                        <h3>
                                            <p class="d-inline">Select an existing quiz, or create your own!</p><br>
                                            <sub class="text-danger">*Recently created quizzes have to be approved before use.*</sub>
                                        </h3>
                                    </div>
                                    <br>
                                    <div id="quizError" class="row"></div>

                                    <?php $this->load->view('challenge/quiz_list'); ?>
                                </div>

                                <input class="btn previous action-button-previous scroll-top" name="previous" type="button" value="Previous" />
                                <input class="btn blue next action-button scroll-top" name="next" type="button" value="Next Step" />
                                <?php if (isset($game_quiz)) { ?>
                                    <input type="button" name="confBtn" class="action-button" id="confirm" value="Confirm" />
                                <?php } ?>
                            </fieldset>
                        <?php } ?>
                        <fieldset name="rules">
                            <i class="fas fa-arrow-left topdirectional"></i><input type="button" name="previous" class="previous action-button-previous topdirectional scroll-top" value="Previous" /> &nbsp; | &nbsp; <input type="button" name="next" class="next action-button topdirectional scroll-top" value="Next Step" /><i class="fas fa-arrow-right topdirectional"></i>
                            <div class="form-card">
                                <h2 class="fs-title text-center">Rules of your Game</h2>

                                <?php $this->load->view('games/partials/how_to_win');?>

                                <br>
                                <div class="carddivider"></div>
                                
                                <div class="col">
                                    <?php $this->load->view('games/partials/difficulty'); ?>
                                </div>

                                <div class="col">
                                    <div class="row">
                                        <div class="game_end">
                                            <label for="gamedepends">
                                                When does the game end?
                                                <div class="mytooltip"><i class="fa fa-question-circle new-tip" data-toggle="tooltip" data-placement="right" title="" aria-hidden="true"></i><span class="tooltiptext">Do you want your game to end immediately when the goal is reached, or run for a longer period?</span></div>
                                            </label>
                                            <select aria-invalid="false" aria-required="true" class="form-control valid" name="gamedepends" id="game_end">
                                                <option value="2">Fundraising Goal Met</option>
                                                <option value="3">Time Elapsed</option>
                                                <option value="4">Date Reached</option>
                                            </select>
                                            <div class="text-center d-none" id="timeDisclaimer">
                                                <small class="mt-1 text-danger">*Time dependent games still require the goal to have been met*</small>
                                            </div>
                                            <div id="duration_123" style="display: none;">
                                                <br>
                                                <label for="duration">How long will the game run?</label>
                                                <div id="gameLengthError"></div>
                                                <div class="row">
                                                    <div class="col-4">
                                                        <label>Days</label>
                                                        <input class="form-control duration" id="days" min="0" name="days" type="number" value="">
                                                    </div>
                                                    <div class="col-4">
                                                        <label>Hours</label>
                                                        <select class="form-control duration" id="hours" name="hours">
                                                            <option value='' selected=""></option>
                                                            <?php for ($i = 1; $i <= 23; $i++) { ?>
                                                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-4">
                                                        <label>Minutes</label>
                                                        <select class="form-control duration" id="min" name="min">
                                                            <option value=''></option>
                                                            <?php for ($i = 2; $i <= 59; $i++) { ?>
                                                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="end_date" style="display: none;">
                                                <br>
                                                <label for="end_date">End Date</label>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class='row' style="z-index:1;">
                                                            <div class="col pb-2">
                                                                <div class='input-group-prepend'>
                                                                    <span class="input-group-text"><span class="fa fa-calendar"></span></span>
                                                                    <input id="enddate" type='text' class="form-control date" name="enddate" placeholder="Select a date.." val="<?php echo (isset($game->End_Date) ? $game->End_Date : ""); ?>" data-date="<?php echo (isset($game->End_Date) ? $game->End_Date : ""); ?>" data-input/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="text-center">
                                                            <div id="enddateError"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                </div>
                            </div>
                            </div>
                            <input type="button" name="previous" class="btn previous action-button-previous scroll-top" value="Previous" />
                            <input type="button" name="next" class="btn blue next action-button scroll-top" value="Next Step" />
                            <?php if (isset($game->name)) { ?>
                                <input type="button" name="confBtn" class="btn blue action-button confirm scroll-top" value="Confirm" />
                            <?php } ?>
                        </fieldset>
                        <fieldset name="finances">
                            <i class="fas fa-arrow-left topdirectional"></i><input type="button" name="previous" class="previous action-button-previous topdirectional scroll-top" value="Previous" /> &nbsp; | &nbsp; <input type="button" name="next" class="next action-button topdirectional scroll-top" value="Next Step" /><i class="fas fa-arrow-right topdirectional"></i>
                            <div class="form-card">
                                <h2 class="fs-title">Finances</h2>
                                <div class="financesBlock">
                                    <br>
                                    <div id="fund-setting" class="row justify-content-center mb-4 main-input-table">
                                        <div class="col-lg-2 col-md-6 col-auto">
                                            <label>
                                                # Winner(s)
                                                <div class="mytooltip"><i class="fa fa-question-circle new-tip" data-toggle="tooltip" data-placement="right" title="" aria-hidden="true"></i><span class="tooltiptext">Cash prizes are limited to one winner.</span></div>
                                            </label>
                                            <div class="input-group-prepend">
                                                <input class="form-control" id="winner_count" min="1" name="winner_count" type="number" value="1">
                                            </div>
                                            <div class="text-center" id="winnerCountError"></div>
                                        </div>
                                        <div class="col-lg-2 col-md-6 col-auto">
                                            <label>
                                                Goal
                                                <div class="mytooltip"><i class="fa fa-question-circle new-tip" data-toggle="tooltip" data-placement="right" title="" aria-hidden="true"></i><span class="tooltiptext">The number of dollars you want to raise</span></div>
                                            </label>
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                                <input type="number" name="fundraise_goal" min="1" id="fundraise_goal" step="0.01" class="valid form-control" value="100.00" title="Must be >= 1">
                                            </div>
                                            <div class="text-center" id="fundraiseGoalError"></div>
                                        </div>
                                        <div class="col-lg-2 col-md-6 col-auto">
                                            <label>
                                                Beneficiary
                                                <div class="mytooltip"><i class="fa fa-question-circle new-tip" data-toggle="tooltip" data-placement="right" title="" aria-hidden="true"></i><span class="tooltiptext">This is the percentage to the Charities, Projects and Causes. Percentage can range from 10% to 90%</span></div>
                                            </label>
                                            <div class="input-group-append">
                                                <input type="number" max="100" min="10" name="beneficiary_percent" id="beneficiary_percent" class="num_select valid form-control" step="0.01" value="25.00">
                                                <span class="input-group-text">%</span>
                                            </div>
                                            <div class="text-center" id="beneficiaryPercentError"></div>
                                        </div>
                                        <div class="col-lg-2 col-md-6 col-auto cash-cls hidden show_price">
                                            <label>
                                                <span id="game-value-title">Prize Value </span>
                                                <div class="mytooltip"><i class="fa fa-question-circle new-tip" data-toggle="tooltip" data-placement="right" title="" aria-hidden="true"></i><span class="tooltiptext">The value of your prize</span></div>
                                            </label>
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                                <input type="number" name="prize_value" min="10" value="10.00" id="prize_value" step="0.01" aria-required="true" aria-invalid="false" class="valid form-control">
                                            </div>
                                            <div class="text-center" id="prizeValueError"></div>
                                        </div>
                                        <div class="col-lg-2 col-md-6 col-auto">
                                            <label>
                                                Cost to Play
                                                <div class="mytooltip"><i class="fa fa-question-circle new-tip" data-toggle="tooltip" data-placement="right" title="" aria-hidden="true"></i><span class="tooltiptext">The price for each play of your game</span></div>
                                            </label>
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">$</span>
                                                <input type="number" name="cost_to_play" id="cost_to_play" min="0.10" value="0.10" step="0.01" aria-required="true" class="valid form-control" title="Must be >= 0.10">
                                            </div>
                                            <div class="text-center" id="costToPlayError"></div>
                                        </div>
                                    </div>
                                    <div class="carddivider"></div>
                                    <div class="row justify-content-center">
                                        <div class="col-md-6">
                                            <h3 class="text-center">Calculated Fundraising Based on Goals</h3>
                                            <div class="row justify-content-center mb-4 financescalculated">
                                                <table class="table table-striped table-sm table-responsive-sm m-0" id="calculated-values">
                                                    <tbody>
                                                        <tr>
                                                            <td id="charity_name">
                                                                <span class="">For </span>
                                                                <span class="beneficiary-select creator side">(TBD)</span>
                                                                <div class="mytooltip">
                                                                    <i class="fa fa-question-circle new-tip" data-toggle="tooltip" data-placement="right" title=""></i>
                                                                    <span class="tooltiptext">Beneficiary Goal</span>
                                                                </div>
                                                                <span id="creator_beneficiary_span" class="pl-2"></span>
                                                            </td>
                                                            <td>
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">$</span><input type="text" id="creator_beneficiary_earning" disabled="disabled" size="10" min="0" value="2.5" class="form-control" title="Must be > 1">
                                                                </div>
                                                                <input type="hidden" readonly="" id="creator_beneficiary_percent" value="25.00" size="10" class="num_select form-control">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td id="winner_charity_name">
                                                                <span>Winner's Beneficiary </span>
                                                                <span class="beneficiary-select player side">(TBD)</span>
                                                                <div class="mytooltip"><i class="fa fa-question-circle new-tip" data-toggle="tooltip" data-placement="right" title=""></i><span class="tooltiptext">Winner's Beneficiary Goal</span></div>
                                                                <span id="winner_beneficiary_span" class="pl-2"></span>
                                                            </td>
                                                            <td>
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">$</span><input type="text" id="winner_beneficiary_earning" disabled="disabled" size="10" min="0" value="2.5" class="form-control"  title="Must be > 1">
                                                                </div>
                                                                <input type="hidden" readonly="" id="winner_beneficiary_percent" value="25.00" size="10" class="num_select form-control">
                                                            </td>
                                                        </tr>
                                                        <tr class="">
                                                            <td>
                                                                Creator
                                                                <div class="mytooltip"><i class="fa fa-question-circle new-tip" data-toggle="tooltip" data-placement="right" title=""></i><span class="tooltiptext">This is you.</span></div>
                                                                <span id="creator_span" class="pl-2"></span>
                                                            </td>
                                                            <td>
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">$</span><input type="text" id="creator_earning" disabled="disabled" size="10" min="0" value="2.5" class="form-control" title="Must be > 1">
                                                                </div>
                                                                <input type="hidden" id="creator_percent" readonly="" value="25.00" size="10" class="num_select form-control" name="creator_earnings">
                                                            </td>
                                                        </tr>
                                                        <tr id="winnnerDiv">
                                                            <td>
                                                                Winner
                                                                <span id="winner_span" class="pl-2"></span>
                                                            </td>
                                                            <td>
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">$</span><input type="text" id="winner_earning" disabled="disabled" size="10" min="0" value="2.5" class="form-control"  title="Must be > 1">
                                                                </div>
                                                                <input type="hidden" id="winner_percent" readonly="" value="25.00" size="10" class="num_select form-control" name="winner">
                                                            </td>
                                                        </tr>
                                                        <tr class="">
                                                            <td>
                                                                WinWinLabs
                                                                <span id="wwl_span" class="pl-2"></span>
                                                            </td>
                                                            <td>
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">$</span><input type="text" id="wwl_earning" disabled="disabled" size="10" min="1" value="2.5" class="form-control">
                                                                </div>
                                                                <input type="hidden" id="wwl_percent" readonly="" value="25.00" size="10" class="num_select form-control" name="winwin" title="Must be > 1">
                                                            </td>
                                                        </tr>
                                                      
                                                        <tr class="">
                                                            <td>
                                                                Minimum $ to Reach Goal
                                                                <div class="mytooltip"><i class="fa fa-question-circle new-tip" data-toggle="tooltip" data-placement="right" title=""></i><span class="tooltiptext">This is the income necessary to reach your goal.</span></div>
                                                            </td>
                                                            <td>
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">$</span><input type="text" id="min_earn" name="min_earn" readonly="readonly" size="10" min="0" value="17.5" class="form-control" title="Must be > 0">
                                                                </div>
                                                                <div id="minEarnError"></div>
                                                            </td>
                                                        </tr>
                                                        <tr class="">
                                                            <td>
                                                                Minimum # of Players ~
                                                                <div class="mytooltip"><i class="fa fa-question-circle new-tip" data-toggle="tooltip" data-placement="right" title=""></i><span class="tooltiptext">This is how many players have to play to acheive your goal.</span></div>
                                                            </td>
                                                            <td><input type="number" id="calc_player" disabled="disabled" size="10" min="0" value="18" readonly="readonly" class="form-control" title="Must be > 0"></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h3 class="text-center">Forecast with a total of &nbsp;</h3>
                                            <center>
                                                <div class="input-group mb-2 text-center mx-auto" style="max-width: 210px;">
                                                    <input type="number" name="num_of_players" id="num_of_players" class="valid form-control" width="120" style="display: inline; border-radius:5px 0px 0px 5px; max-width: 120px;">
                                                    <div class="input-group-append">
                                                        <div class="input-group-text">Players</div>
                                                    </div>
                                                </div>
                                            </center>
                                            <div class="financescalculated">
                                                <table class="table table-striped table-sm table-responsive-sm m-0" id="forcasted-variables">
                                                    <tbody>
                                                        <tr>
                                                            <td id="goal_charity_name">
                                                                <span>For </span>
                                                                <span class="beneficiary-select creator side">(TBD)</span>
                                                                <div class="mytooltip">
                                                                    <i class="fa fa-question-circle new-tip" data-toggle="tooltip" data-placement="right" title=""></i>
                                                                    <span class="tooltiptext">Beneficiary Goal</span>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">$</span><input class="form-control" type="text" id="forecasted_creator_beneficiary_earning" name="gp" disabled="disabled" size="10"></span>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td id="goal_charity_winner_name">
                                                                Winner's Beneficiary <span class="beneficiary-select player side">(TBD)</span>
                                                                <div class="mytooltip"><i class="fa fa-question-circle new-tip" data-toggle="tooltip" data-placement="right" title=""></i><span class="tooltiptext">Winner's Beneficiary Goal</span></div>
                                                            </td>
                                                            <td>
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">$</span><input class="form-control" type="text" id="forecasted_winner_beneficiary_earning" disabled="disabled" size="10"></span>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr class="">
                                                            <td>
                                                                Creator
                                                                <div class="mytooltip"><i class="fa fa-question-circle new-tip" data-toggle="tooltip" data-placement="right" title=""></i><span class="tooltiptext">This is you.</span></div>
                                                            </td>
                                                            <td>
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">$</span><input class="form-control" type="text" id="forecasted_creator_earning" disabled="disabled" size="10"></span>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr class="">
                                                            <td>
                                                                Winner
                                                            </td>
                                                            <td>
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">$</span><input class="form-control" type="text" id="forecasted_winner_earning" disabled="disabled" size="10"></span>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr class="winwinlabsFeeForecast">
                                                            <td>
                                                                WinWinLabs
                                                            </td>
                                                            <td>
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">$</span><input class="form-control" type="text" id="forecasted_wwl_earning" disabled="disabled" size="10"></span>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <!--END 6 col -->
                                        </div>
                                    </div>
                                </div>
                                <div class="financesBlockOpposite d-none">
                                    <h2 class="text-center">Not Applicable</h2>
                                </div>
                            </div>
                            <input type="button" name="previous" class="btn previous action-button-previous scroll-top" value="Previous" /> <input type="button" name="next" class="btn blue next action-button scroll-top" value="Next Step" />
                            <?php if (isset($game->name)) { ?>
                                <input type="button" name="confBtn" class="btn blue action-button confirm scroll-top" value="Confirm" />
                            <?php } ?>
                        </fieldset>
                        <fieldset name="publish">
                            <input type="hidden" name="game_id" id="game_id" value="<?php echo isset($game->name) ? $game->id : 0; ?>" />
                            <i class="fas fa-arrow-left topdirectional"></i><input type="button" name="previous" class="previous action-button-previous  topdirectional scroll-top" value="Previous" /> &nbsp; | &nbsp;
                            <input type="button" name="confBtn" class="next action-button confirm topdirectional scroll-top" id="confirm" value="Confirm" /> <i class="fas fa-arrow-right topdirectional"></i>
                            <div class="form-card publishstep" id="lastVue">
                                <div class="row justify-content-center text-center">
                                    <div class="col-md-8">
                                        <label>
                                            Provide information you would like game player to see when game ends (Optional)
                                            <div class="mytooltip"><i class="fa fa-question-circle new-tip" data-toggle="tooltip"></i>
                                                <span class="tooltiptext">You can provide any information you would like to share with game player</span>
                                            </div>
                                        </label>
                                        <textarea class="form-control" name="gameEndInfoDescription" id="gameEndInfoDescription" rows="6">
                                                <?php if (isset($game->gameEndDescription)) {
                                                    echo $game->gameEndDescription;
                                                } ?>
                                            </textarea>
                                    </div>
                                </div>
                                <div class="row justify-content-center text-center pw-form">
                                    <div class="col-md-4">
                                        <h2><i class="fas fa-save"></i></h2>
                                        <label class="switch">
                                            <input type="radio" id="draft_data" class='publishstat large-radio' name='publishstat' value='Draft Game' />
                                        </label>
                                        <h3>Save Draft Game</h3>
                                        <p>Save this created game as a draft so that you can manage the options and publish/make your game live at a later date.</p>
                                    </div>
                                    <div class="col-md-4">
                                        <h2><i class="fas fa-paper-plane"></i></h2>
                                        <label class="switch">
                                            <input type="radio" id="publish_data" class='publishstat large-radio' name='publishstat' value='Publish Game' />
                                        </label>
                                        <h3>Publish Game</h3>
                                        <p>Schedule a specific future date and time for your game to go live. Changes in game options/information cannot be changed once your game is published.</p>

                                        <div class="row datepick border p-2">
                                            <div class="col-12">
                                                <label for="publishdate"><strong>Date</strong></label>
                                                <div class='col' id='timePickerDiv' style="z-index:1;">
                                                    <div class="justify-content-center align-items-center">
                                                        <div class="row pb-2 pt-1">
                                                            <div class="col-sm-12">
                                                                <div class='input-group-prepend'>
                                                                    <span class="input-group-text"><span class="fa fa-calendar"></span></span>
                                                                    <input id="publishdate" type='text' class="form-control date" name="publishdate" placeholder="Select date.." val="<?php echo isset($game->Publish_Date) ? $game->Publish_Date : ''; ?>" data-date="<?php echo isset($game->Publish_Date) ? $game->Publish_Date : ''; ?>"/>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div id="publishdateError"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <label for="timeZone"><strong>Timezone</strong></label><br>
                                                <select aria-invalid="false" aria-required="true" class="form-control time-zone-select valid" id="timeZone" name="timeZone"> </select>
                                                <div class="zone-time"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <h2><i class="fas fa-gamepad"></i></h2>
                                        <label class="switch">
                                            <input type="radio" class='publishstat large-radio' name='publishstat' value='Live' />
                                        </label>
                                        <h3>Make Game Live</h3>
                                        <p>Make this game live immediately. Changes in game options/information cannot be changed once your game is made live.</p>
                                    </div>
                                </div>
                                <div class="row" id="publisherror"></div>
                            </div>
                            <input type="hidden" name="game_id" id="game_id" value="<?php echo isset($game->name) ? $game->id : 0; ?>" />
                            <input type="button" name="previous" class="btn previous action-button-previous scroll-top" value="Previous" />
                            <input type="button" name="confBtn" class="btn blue action-button confirm" id="confirm" value="Confirm" />
                        </fieldset>
                        <fieldset name="complete">
                            <div class="form-card text-center">
                                <h2>Thank you!</h2>
                                <p>Your game is confirmed</p>
                            </div>
                            <div class="dashicons">
                                <center><a href="<?php echo asset_url('games/create'); ?>"><img src="https://dg7ltaqbp10ai.cloudfront.net/fit-in/1593787669-DashButtoncreategames.png" alt="Create Games"></a><a href="<?php echo asset_url('games/show/play'); ?>"><img src="https://dg7ltaqbp10ai.cloudfront.net/fit-in/1593787687-DashButtonplaygames.png" alt="Play Games"></a><a href="<?php echo asset_url('games/show/drafted'); ?>"><img src="https://dg7ltaqbp10ai.cloudfront.net/fit-in/1593787738-DashButtonmanagegames.png" alt="Manage Games"></a><a href="<?php echo asset_url('fundraisers'); ?>"><img src="https://dg7ltaqbp10ai.cloudfront.net/fit-in/1596736745-DashButtonMangFund.png" alt="Manage Fundraisers"></a><a href="<?php echo asset_url('buycredits'); ?>"><img src="https://dg7ltaqbp10ai.cloudfront.net/fit-in/1593787789-DashButtonbuycredits.png" alt="Buy Credits"></a></center>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <span class="game_type" type="<?php echo $type; ?>"></span>
    <div id="divLoading"> </div>
</content>
<script>
    $(document).ready(function() {
        const finances = new Finances();

        $('#beneficiary_percent, #fundraise_goal, #cost_to_play, #prize_value, #creator_beneficiary_percent, #prize, #credits, #winner_count').on('change blur keyup', function(e) {
            finances.calculate();
        });

        $('#cost_to_play').on('change blur keyup', function(e) {
            finances.forecast();
        });

        $('#num_of_players').on('change blur', function(e) {
            var min_num_player = Math.ceil(parseFloat($('#calc_player').val()));
            var num_player = parseFloat($('#num_of_players').val());

            if (num_player <= min_num_player) {
                $('#num_of_players').val(min_num_player + 1);
            }

            finances.forecast();
        });

        winnerCountLogic()
        $('input[name="credit_prize"]').on('change', function() {
            winnerCountLogic()
        });

        function Finances() {
            var self = this;

            this.calculate = () => {
                var state = this.get_state();
                var parties = this.get_parties(state);
                
                $('#wwl_earning').closest("tr").toggle(!state.is_winwinlabs);

                // fundraising goal
                var total_fundraise_goal = +parseFloat($('#fundraise_goal').val());
                if (!total_fundraise_goal) {
                    $('#fundraise_goal').val(0);
                }

                var cost_play = +parseFloat($('#cost_to_play').val());
                if (!cost_play) {
                    cost_play = 0;
                }

                var num_players = $('#winner_count').val();
                var prize_value = (state.is_prize) ? +parseFloat($('#prize_value').val()) : 0;

                var n_beneficiaries = Object.values(parties.beneficiary).reduce((t, data) => {
                    return t + data.include;
                }, 0);

                var n_parties = Object.values(parties.other).reduce((t, data) => {
                    return t + data.include;
                }, 0);

                // round beneficiary percentage to whole number
                var beneficiary_percentage = +parseInt($('#beneficiary_percent').val());
                var remaining_percentage = 100 - beneficiary_percentage;

                $('#beneficiary_percent').val(beneficiary_percentage);

                var beneficiary_total = this.round(total_fundraise_goal * (beneficiary_percentage / 100));
                var beneficiary_earnings = this.distribute(beneficiary_total, parties.beneficiary);

                // set finances for beneficiaries
                for (const [beneficiary, data] of Object.entries(parties.beneficiary)) {
                    let percent = (data.include) ? beneficiary_percentage / n_beneficiaries : 0;
                    data.percent.value = this.round(percent, 4);
                    data.percent.approx = Math.abs(percent - data.percent.value) > 0;

                    $(`#${beneficiary}_percent`).val(data.percent.value);

                    var approx_text = (data.percent.approx) ? "~" : ""
                    var percent_text = `${approx_text}${(data.percent.value).toFixed(2)}%`;
                    $(`#${beneficiary}_span`).text(percent_text);

                    $(`#${beneficiary}_earning`).val((beneficiary_earnings[beneficiary]).toFixed(2));
                }

                // calculate even distribution of funds between parties
                var beneficiary_sum = Object.values(beneficiary_earnings).reduce((a,b) => a+b);
                var remaining_total = this.round((total_fundraise_goal - beneficiary_sum));
                var party_earnings = this.distribute(remaining_total, parties.other);

                // set finances for creator, winner, wwl
                for (const [party, data] of Object.entries(parties.other)) {
                    let percent =(data.include) ? remaining_percentage / n_parties : 0
                    data.percent.value = this.round(percent, 3);
                    data.percent.approx = Math.abs(percent - data.percent.value) > 0

                    $(`#${party}_percent`).val(data.percent.value);

                    var approx_text = (data.percent.approx) ? "~" : ""
                    var percent_text = (party == "winner" && state.is_prize) ? `# of Winners: ${num_players}` : `${approx_text}${(data.percent.value).toFixed(2)}%`;
                    $(`#${party}_span`).text(percent_text);

                    var earnings = (party == "winner" && state.is_prize) ? prize_value : party_earnings[party];
                    $(`#${party}_earning`).val((earnings).toFixed(2));
                }

                var party_sum = Object.values(party_earnings).reduce((a,b) => a+b);
                var actual_fundraise_goal = total_fundraise_goal - party_sum;

                // set min $ needed to reach goal (including cost of prizes)
                var total_cost_prizes = (num_players * prize_value);
                var min_earn = +parseFloat(total_fundraise_goal + total_cost_prizes);
                $('#min_earn').val((min_earn).toFixed(2));

                var calc_player = Math.ceil(min_earn / cost_play) || 0;
                $('#calc_player').val(calc_player);
                $('#num_of_players').val(calc_player+1);

                this.forecast();
            }

            this.forecast = () => {
                var state = this.get_state();
                var parties = this.get_parties(state);

                $('#forecasted_wwl_earning').closest("tr").toggle(!state.is_winwinlabs);

                var cost_play = +parseFloat($('#cost_to_play').val());
                var num_player = +parseFloat($('#num_of_players').val());

                var winner_count = $('#winner_count').val();
                var prize_value = $('#prize_value').val();
                var total_cost_prizes = (winner_count * prize_value);

                // min earn, minus prize value
                var total_forecasted_earning = this.round((state.is_prize) ? ((num_player * cost_play) - total_cost_prizes) : (num_player * cost_play));
                var beneficiary_percentage = Object.values(parties.beneficiary).reduce((t, data) => {
                    return t + ((data.include) ? data.percent.value : 0);
                }, 0);

                var n_beneficiaries = Object.values(parties.beneficiary).reduce((t, data) => {
                    return t + data.include;
                }, 0);

                var n_parties = Object.values(parties.other).reduce((t, data) => {
                    return t + data.include;
                }, 0);

                var forecasted_beneficiary_total = this.round(total_forecasted_earning * (beneficiary_percentage / 100));
                var forecasted_beneficiary_earnings = this.distribute(forecasted_beneficiary_total, parties.beneficiary);

                // set finances for beneficiaries
                for (const [beneficiary, data] of Object.entries(parties.beneficiary)) {
                    let forecasted_earning = (forecasted_beneficiary_earnings[beneficiary]).toFixed(2);
                    $(`#forecasted_${beneficiary}_earning`).val(forecasted_earning);
                }

                var forecasted_beneficiary_sum = Object.values(forecasted_beneficiary_earnings).reduce((a,b) => a+b);
                var forecasted_remaining_total = this.round((total_forecasted_earning - forecasted_beneficiary_sum));
                var forecasted_party_earnings = this.distribute(forecasted_remaining_total, parties.other);

                // set finances for creator, winner, wwl
                for (const [party, data] of Object.entries(parties.other)) {
                    let forecasted_earning = (party === "winner" && state.is_prize) ? `${prize_value} x${winner_count}` : (forecasted_party_earnings[party]).toFixed(2);
                    $(`#forecasted_${party}_earning`).val(forecasted_earning);
                }
            }

            this.get_state = () => {
                return {
                    "is_winwinlabs" : $("#selectedFundraiser").val() == "winwinlabs-fundraising-system",
                    "is_prize" : $('#prize').is(':checked'),
                    "donation_option" : parseInt($("input[name='donationOption']:checked").val()),
                }
            }

            this.get_parties = (state) => {
                return {
                    "beneficiary" : {
                        "creator_beneficiary" : {
                            include: true, 
                            percent: {
                                value : +parseFloat($("#creator_beneficiary_percent").val()),
                                approx : false
                            }
                        },
                        "winner_beneficiary" : {
                            include: state.donation_option === 2, 
                            percent: {
                                value : +parseFloat($("#winner_beneficiary_percent").val()),
                                approx : false
                            }
                        }
                    },
                    "other" : {
                        "creator" : {
                            include: true, 
                            percent: {
                                value : +parseFloat($("#creator_percent").val()),
                                approx : false
                            }
                        },
                        "winner" : {
                            include: !state.is_prize, 
                            percent: {
                                value : +parseFloat($("#winner_percent").val()),
                                approx : false
                            }
                        }, 
                        "wwl" : {
                            include: !state.is_winwinlabs, 
                            percent: {
                                value : +parseFloat($("#wwl_percent").val()),
                                approx : false
                            }
                        }
                    }
                };
            }

            this.round = (n, precision=2) => {
                return Math.round((n + Number.EPSILON) * (10 ** precision)) / (10 ** precision);
            }

            this.distribute = (total, parties) => {
                var divisions = Object.values(parties).reduce((t, data) => {
                    return t + (data.include ? 1 : 0);
                }, 0);

                var m = total * 100;
                var n = m % divisions;
                var v = Math.floor( m / divisions ) / 100;
                var w = Math.floor( m / divisions + 1 ) / 100;

                var index = 0; var earnings = {};
                for (const [party, data] of Object.entries(parties)) {
                    data.earning = (data.include) ? (index < n ? w: v) : 0;
                    earnings[party] = data.earning;
                    index++;
                }

                return earnings;
            }
        }

        function winnerCountLogic() {
            // make winner count disbaled on default
            if ($('input[name="credit_prize"]:checked').val() === 'prize') {
                $('#winner_count').prop("disabled", false);
            } else {
                $('#winner_count').prop("disabled", true);
                $('#winner_count').val(1);
            }
        }

        <?php if (isset($game->name)) { ?>
            $('#cost_to_play').focus().trigger("blur");
            $('#beneficiary_percent').trigger('change');
        <?php } else { ?>
            $('#fundraise_goal').trigger('change');
            $('#beneficiary_percent').trigger('change');
        <?php } ?>
    });
</script>
