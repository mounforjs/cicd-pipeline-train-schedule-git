<?php if ((isAdmin() == true || checkSysAdminLogin() == true) OR ($game->Publish=="No" && $game->Status =="Running" && getprofile()->user_id == $game->user_id)) {
    $gameDraftedData['slug'] = $game->slug;
    $gameDraftedData['id'] = $game->id;
    $this->load->view('layouts/gameditnav', $gameDraftedData);
    } ?>
<content class="content gameediting">
    <section class="item-imgs-prize">
        <div class="container-lg">
            <div class="row p-2">
                <div class="col-md-7">
                    <?php if($game->game_status === 'Published') { ?>
                    <div class="countdownribbon">
                        <div class="ribbon-content">
                            <p>LIVE IN <span id="countdown"></span></p>
                        </div>
                    </div>
                    <?php } ?>
                    <div class="item-imgs-prize-slider">
                        <div id="slider" class="flexslider">
                            <ul class="slides">
                                <?php if (!$game->prize_image_data) { ?>
                                <li>
                                    <img src="<?= $game->GameImage["image"]; ?>" onerror="imgError(this, '<?= $game->GameImage['fallback'] ?>')" alt="<?php echo $game->name; ?>"/>
                                </li>
                                <?php }
                                    else{
                                    foreach($game->prize_image_data as $prize_image) { ?>
                                <li>
                                    <img src="<?= $prize_image->prize_image["image"]; ?>" onerror="imgError(this, '<?= $prize_image->prize_image['fallback'] ?>')" alt="<?php echo $game->name; ?>"/>
                                </li>
                                <?php } }?>
                            </ul>
                        </div>
                        <?php if (count($game->prize_image_data)!=1) {?>
                        <div id="carousel" class="flexslider flexslider-btm">
                            <ul class="slides">
                                <?php foreach($game->prize_image_data as $prize_image) { ?>
                                <li>
                                    <img src="<?= $prize_image->prize_image["image"]; ?>" onerror="imgError(this, '<?= $prize_image->prize_image['fallback'] ?>')" style="height:70px" alt="<?php echo $game->name; ?>"/>
                                </li>
                                <?php }?>
                            </ul>
                        </div>
                        <?php }?>
                    </div>
                </div>
                <div class="col-md-5 detailtitle pt-2">
                <div id="countdown" class="badge badge-pill bg-success text-white large-pill"></div>
                <script>startCountdown('<?php echo $game->Publish_Date;?>', 'countdown');</script>
                    <h1><?php echo $game->name; ?></h1>
					<div class="pull-right">
                                    <div class="backrating">
                                        <?php for ($i = 0; $i <= 4; $i++) { ?>
                                        <i class="fas fa-star"></i>
                                        <?php } ?>
                                        <div class="rating" style='width: <?php echo ((($game->rating)/5)*100) . "%"; ?>;'>
                                            <?php for ($i = 0; $i <= 4; $i++) { ?>
                                            <i class="fas fa-star rateorange"></i>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
					<h4 class="mt-0"><?php echo ucwords(str_replace("_", " ", $game->game_type[0]->name)); ?> Game</h4>
                    <div class="item-imgs-prize-detail p-1">
                        <?php if ($game->credit_type !== 'free') { ?>
                        <span class="pt-2 pb-1">
                            <div class="valueicon"><i class="fa fa-trophy" aria-hidden="true"></i> <strong><?php echo ' $'.round_to_2dc($game->value_of_the_game); ?></strong> winner's <?php echo ($game->credit_type=='prize') ? 'prize' : 'cash';?> value</div>
                        </span>
                        <div class="progressdetail pb-2">
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped active" id="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                </div>
                            </div>
                            <span style="float:left;display:inline-block;">
                                <div class="progamtdisplay">$0</div>
                            </span>
                            <span>
                                &nbsp;of <?php echo ' $'.round_to_2dc($game->minAmountNeededForGoal);?> 
                                <div class="mytooltip"><small><i data-toggle="tooltip" data-placement="right" title="" aria-hidden="true">Goal</i></small><span class="tooltiptext">Game Ends When <?php echo $game->game_end_rule; ?></span></div>
                            </span>
                        </div>
                        <!-- End of Game Info --> 
                        <?php } ?>
                        <div class="row mt-2">
                            <div class="col-6 col-md-7 col-lg-6 p-1">
                                <?php if($game->game_status === 'Drafted') { ?>
                                <div class="game-status-btn">
                                    Draft Game 
                                    <small>
                                        <div class="mytooltip">
                                            <i class="fa fa-question-circle new-tip" data-toggle="tooltip" data-placement="right" title=""></i>
                                            <span class="tooltiptext">Current game status</span>
                                        </div>
                                    </small>
                                </div>
                                <?php } ?>
                                <?php if($game->game_status === 'Published') { ?>
                                <div class="game-status-btn">
                                    Published
                                </div>
                                <?php } ?>
                                <?php if ($game->Publish=="Live" && getprofile()->user_id != $game->user_id) { ?>
                                <a id="play-game-btn" class="play-game-btn btn lastGame" data-toggle="modal" game-name="<?php echo isset($_SESSION['game_slug_from_buycredit']) ? $_SESSION['game_slug_from_buycredit'] : "";?>" game-amount="<?php echo isset($_SESSION['game_amount_from_buycredit']) ? $_SESSION['game_amount_from_buycredit'] : "";?>"><i class="fa fa-gamepad" aria-hidden="true"></i> Play Game <span>|</span> <?php echo ' $'.round_to_2dc($game->credit_cost); ?></a>
                                <!-- Play Button Start -->
                                <?php
                                    $modelData['credit_cost'] = $game->credit_cost;
                                    $modelData['slug'] = $game->slug;
                                    $modelData['tile'] = $game->game_tile_goal;
                                    $modelData['type'] = $game->game_type[0]->name;
                                    $modelData['img'] = $game->gameTypeImg;
                                    $modelData['desc'] = $game->gameTypeDesc;
                                    $modelData['instrImg'] = $game->gameTypeInstrImage;
                                    $modelData['instructions'] = $game->gameTypeInstructions;
                                    $modelData['account_link'] = $account_link;
                                    $modelData['creditType'] = $game->credit_type;
                                    $this->load->view('games/gamePlayModal', $modelData);
                                    } else if (isset($confirmPrize)) { ?>
                                <a class="play-game-btn btn green" id="claimPrize" data-toggle="modal" data-target="#prizeModal"><i class="fa-hand-rock-o" aria-hidden="true"></i> Claim Prize</a>
                                <?php } ?>
                                <!-- Play Button End -->
                            </div>
                            <div class="col-6 col-md-5 col-lg-6">
                                <div class="details-right share-buttons">
                                    <?php
                                        if ($this->session->userdata('user_id')) {
                                        if ($game->game_wishlist_status!=1) { ?>
                                    <a class="btn btn-product btn-squre wishlist-add-btn-detail wishcard-2-no" data-popup="popover" data-trigger="hover" data-placement="bottom" title="Click to add to wishlist" data-id = "<?php echo $game->id; ?>"><i class="fa fa-heart-o"></i></a>
                                    <?php } else { ?>
                                    <a class="btn btn-product btn-squre wishlist-remove-btn-detail wishcard-2-yes" data-popup="popover" data-trigger="hover" data-placement="bottom" title="Click to remove from wishlist" data-id = "<?php echo $game->id; ?>"><i class="fa fa-heart"></i></a>
                                    <?php  } }?>
                                    <button class="share-btn btn-share">
                                        <i class="fas fa-share-alt"></i>
                                    </button>
                                    <div class="share-options">
                                        <h3 class="whitetext pl-2">Share</h3>
                                        <div class="social-media">
											<button class="social-media-btn"><i class="fab fa-facebook-f"></i></button>
											<button class="social-media-btn"><i class="fab fa-twitter"></i></button>
											<button class="social-media-btn"><i class="fab fa-linkedin-in"></i></button>
											<button class="social-media-btn"><i class="fab fa-whatsapp"></i></button>
											<button class="social-media-btn"><i class="fa fa-google"></i></button>
											<button class="social-media-btn"><i class="fa fa-envelope"></i></button>
											<button class="social-media-btn copy-btn">Copy Link</button>
										</div>
                                            <span class="link d-none"><?= base_url() . "games/show/play/" . $game->slug; ?></span>
                                    </div>
                                    <?php
                                        if ($this->session->userdata('user_id')) { ?>
                                    <button class="btn btn-product pub-cls" id="fg-btn"  data-toggle="modal" data-target="#flagModal">
                                    <i class="fa fa-flag-o"></i></button>
                                    <?php } ?>
                                    <div class="modal" tabindex="-1" role="dialog" id="flagModal">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h6 class="modal-title" style="text-transform: none">Flagged games and users are reviewed by WinWinLabs staff 24 hours a day, seven days a week to determine whether they violate our policies. We take the legitimacy and quality of our service very seriously. Egregious or repeated violations can lead to account termination.
                                                        Please provide the reason for flagging the game:
                                                    </h6>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <textarea id="flag_reason" required="required" rows="4" cols="50" ></textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-primary flag-btn" >Submit</button>
                                                    <input type="hidden" value = "<?php echo $this->session->userdata('user_id'); ?>" id="uid" data-id = "<?php echo $game->id; ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if ($game->credit_type !== 'free') { ?>
                        <!-- START creators Beneficiary info -->
                        <div class="row justify-content-start bengamedetail">
                            <div class="col-auto">
                                <div class="card-img">
                                    <a href="<?= base_url("/fundraisers/show/all/{$game->selected_fundraiser->slug}"); ?>" target="_blank"><img src="<?= $game->selected_fundraiser->Image["image"]; ?>" onerror="<?= $game->selected_fundraiser->Image["fallback"]; ?>" alt="<?php echo $game->fundraise_name; ?>" /></a>
                                </div>
                            </div>
                            <div class="col-auto">
                                <p><strong><?php echo $game->selected_fundraiser->name; ?></strong><br>Creators Beneficiary</p>
                            </div>
                        </div>
                        <!-- END creators Beneficiary info -->
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    </strong>
    <section class="detail-about-prize">
        <div class="container-lg">
            <div class="row">
                <div class="col">
                    <div class="detail-about-prize-wrap mb-4">
                        <ul id="aboutgame" class="nav nav-tabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" href="#detail-info" role="tab" data-toggle="tab">
									<!--<img src="<?php echo $game->game_type_image; ?>" width="20" class="align-baseline" alt="<?php echo ucfirst($game->game_type[0]->name); ?>"> -->
									DETAILS</a>
                            </li>
                            <?php  if ($game->credit_type=='prize') { ?>
                            <li class="nav-item">
                                <a class="nav-link" href="#prize-info" role="tab" data-toggle="tab">PRIZE</a>
                            </li>
                            <?php } ?>
                            <?php if ($game->credit_type !== 'free') { ?>
                            <li class="nav-item">
                                <a class="nav-link" href="#terms" role="tab" data-toggle="tab">TERMS</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#finances" role="tab" data-toggle="tab">FINANCES</a>
                            </li>
                            <?php } ?>
                        </ul>
                        <!-- Tab panes -->
                        <div class="tab-content" id="gamedetailtabs">
                            <div role="tabpanel" class="tab-pane fade in active show" id="detail-info">
								<p><?php echo $game->game_desc; ?></p>
                            </div>
                            <?php  if ($game->credit_type=='prize') { ?>
                            <div role="tabpanel" class="tab-pane fade" id="prize-info">
                                <h3>Prize Description</h3>
                                <div class="prize-description descr">
                                    <?php echo $game->prize_description; ?>
                                </div>

                                <h3>How will this prize be awarded?</h3>
                                <div class="prize-description descr">
                                    <?php echo $game->prize_specification; ?>
                                </div>
                            </div>
                            <?php } ?>
                            <div role="tabpanel" class="tab-pane fade" id="terms">
                                <h3>Terms and Conditions</h3>
                                <p><?php $this->load->view('home/donation_terms.php'); ?></p>
                            </div>
                            <div role="tabpanel" class="tab-pane fade" id="finances">
                                <h3>Financial Breakdown</h3>
                                <table class="table table-striped border gamedetailtable">
                                    <tbody>
                                        <tr>
                                            <th scope="row">
                                                Winner(s)
                                                <div class="mytooltip"><i class="fa fa-question-circle new-tip" data-toggle="tooltip" data-placement="right" title=""></i><span class="tooltiptext">Reward value per winner</span></div>
                                                <small>(<?= ($game->credit_type == "prize") ? ucwords($game->credit_type) : ""; ?>)</small>
                                            </th>
                                            <td>
                                                <strong>$<?= round_to_2dc(isset($game->calculated_finances->winner) ? $game->calculated_finances->winner : $game->calculated_finances->prize_value); ?></strong>
                                                <small>(x<?php echo $game->calculated_finances->winner_count; ?>)</small>
                                            </td>
                                        </tr>
                                        <?php if (isset($game->calculated_finances->winner_beneficiary)) { ?>
                                        <tr>
                                            <th scope="row">
                                                Winner's Beneficiary
                                                <div class="mytooltip"><i class="fa fa-question-circle new-tip" data-toggle="tooltip" data-placement="right" title=""></i><span class="tooltiptext">Money that goes to the top winner's beneficiary</span></div>
                                            </th>
                                            <td>
                                                <strong>$<?= round_to_2dc($game->calculated_finances->winner_beneficiary); ?></strong>
                                            </td>
                                        </tr>
                                        <?php } ?>

                                        <?php if (isset($game->calculated_finances->creator_beneficiary)) {?>
                                        <tr>
                                            <th scope="row">
                                                Creator's Beneficiary
                                                <div class="mytooltip"><i class="fa fa-question-circle new-tip" data-toggle="tooltip" data-placement="right" title=""></i><span class="tooltiptext">Money that goes to the creator's beneficiary</span></div>
                                                <small>(<?= $game->selected_fundraiser->name; ?>)</small>
                                            </th>
                                            <td>
                                                <strong>$<?= round_to_2dc($game->calculated_finances->creator_beneficiary); ?></strong>
                                            </td>
                                        </tr>
                                        <?php } ?>

                                        <tr>
                                            <th scope="row">
                                                Creator 
                                                <div class="mytooltip"><i class="fa fa-question-circle new-tip" data-toggle="tooltip" data-placement="right" title=""></i><span class="tooltiptext">Money that goes to the game creator</span></div>
                                                <small>(<?= $game->username; ?>)</small>
                                            </th>
                                            <td>
                                                <strong>$<?= round_to_2dc($game->calculated_finances->creator); ?></strong>
                                            </td>
                                        </tr>
                                        
                                        <?php if (isset($game->calculated_finances->wwl)) { ?>
                                        <tr>
                                            <th scope="row">
                                                WinWinLabs
                                                <div class="mytooltip"><i class="fa fa-question-circle new-tip" data-toggle="tooltip" data-placement="right" title=""></i><span class="tooltiptext">Money that goes to WinWinLabs</span></div>
                                            </th>
                                            <td>
                                                <strong>$<?= round_to_2dc($game->calculated_finances->wwl); ?></strong>
                                            </td>
                                        </tr>
                                        <?php } ?>

                                        <tr>
                                            <th class="text-right" scope="row">
                                                Total
                                            </th>
                                            <td>
                                                <strong>$<?= round_to_2dc($game->minAmountNeededForGoal); ?></strong>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php if (isset($confirmPrize)) { 
            $this->load->view('prizes/confirm');
            } ?>
    </section>
    <?php  if ($this->uri->segment(3)=='play' AND $this->uri->segment(4) !='') {
        $this->load->view('games/moreGamesList');
        } ?>
    <?php if ($_SESSION['game_slug_from_buycredit']) { ?>
    <script>
        $(window).load(function() {
           $('.lastGame').trigger('click');
           $('#custom_amount').val('<?php echo $_SESSION['game_amount_from_buycredit']; ?>')
        });
    </script>
    <?php } 
        $this->session->unset_userdata('game_slug_from_buycredit'); 
        $this->session->unset_userdata('game_amount_from_buycredit');
        ?>
</content>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
<script>
    var value = 0;
    var gameEarnings = <?php echo $game->goalRaised ?> ;
    var gameGoal = <?php echo $game->fundraise_value ?> ;
    value += (gameEarnings/gameGoal).toFixed(2)*100;
    $("#progress-bar")
    .css("width", value + "%")
    .attr("aria-valuenow", value);
    $(".progamtdisplay")
    .text('$'+gameEarnings);
</script>