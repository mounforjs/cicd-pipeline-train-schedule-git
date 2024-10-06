<div class="col p-0">
    <?php if (!isset($select_fundraiser) || !$select_fundraiser) { ?>
        <h2>My Default Beneficiary</h2>
    <?php } ?>
    <div class="bg-light mb-3 fundraisecard defaultfund">
        <div class="card-header">
            <div class="row justify-content-sm-center">
                <div class="col-12 p-1">
                    <?php $this->load->view('fundraisers/partials/fundraiserfilter'); ?>
                </div>
            </div>
            <?php if (isset($select_fundraiser) && $select_fundraiser) { ?>
                <div class="row">
                    <div class="col-md-12 text-right">
                        <div class="mytooltip" data-toggle="tooltip" data-placement="auto" title="Submit a request to be added to the list."><i class="fa fa-question-circle new-tip" data-toggle="tooltip" data-placement="right" title=""></i></div>
                        <button id="create_beneficiary" class="btn primary" type="button"><i class="fas fa-plus-circle"></i> <span>Create New Beneficiary</span></button>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="col">
            <div class="row">
                <div class="loader d-none"><div class="imageLoader"></div></div>
                <div class="card-body">
                    <div class="row" id="selectedFundraiserDetails">
                        <div class="col-sm-4">
                            <div class="row">
                                <div class="col-12 pb-2">
                                    <img id="fundraiserImagePreview" src="<?= $default_fundraiser->Image["image"]; ?>" onerror="imgError(this, '<?= $default_fundraiser->Image['fallback']; ?>')" width="100%" alt="<?php echo $default_fundraiser->name; ?>">
                                </div>
                                <div class="col-lg-6 col-sm-12 col-6 p-1">
                                    <div class="myfundcategory fundcatdefault <?php echo $default_fundraiser->fundraise_type; ?>">
                                        <?php echo '<i id="fundraiserIcon" class="' . $default_fundraiser->icon . '"></i>' . '<span id="defaultType" class="ml-1">' . $default_fundraiser->fundraise_type . '</span>' ?>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-sm-12 col-6 p-1">
                                    <div class="donationgroup">
                                        <?php if ((float)str_replace(",", "", $default_fundraiser->totalRaised) > 250) { ?>
                                            <span class="raised-text defaultraised">
                                                <span>RAISED: </span>
                                                <span id='fundRaisedTotal'><?php echo ' $' . round_to_2dc($default_fundraiser->totalRaised); ?></span>
                                            </span>
                                        <?php } ?>
                                    </div>
                                    <br>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-8">
                            <div class="row">
                                <div class="col">
                                    <h3 class="pb-2"><span id="defaultName"><?php echo $default_fundraiser->name; ?></span></h3>
                                </div>
                                <?php if (!isset($select_fundraiser) || !$select_fundraiser) { ?>
                                    <div class="col-sm-auto">
                                        <button class='btn blue small makeDefaultCardbtn <?= ((int)$default_fundraiser->def == 0) ? "" : "d-none"; ?>' data-slug='<?php echo $default_fundraiser->slug; ?>'>MAKE DEFAULT</button>
                                    </div>
                                <?php } ?>
                            </div>
                            <p class="card-text text-left"><strong>Website:</strong> <a href="<?php echo 'https://' . $default_fundraiser->charity_url; ?>" target="_blank" id="defaultWebsite"><?php echo $default_fundraiser->charity_url; ?></a>
                                <br>
                            </p>
                            <div class="comment more" id="defaultDescription">
                                <?php echo $default_fundraiser->Description; ?>
                            </div>

                            <div class="row bottombtns mt-3">
                                <?php if (!isset($select_fundraiser) || !$select_fundraiser) { ?>
                                    <div class="col-lg-4 pt-1">
                                        <button class="btn orange btn-block payDonateBtn" type="button" fundraiser-name="<?php echo $default_fundraiser->name; ?>" fundraiser-slug="<?php echo $default_fundraiser->slug; ?>"><span>DONATE</span></button>
                                    </div>
                                    <div class="col-lg-4 pt-1">
                                        <a id="view_games" class="btn blue btn-block" data-slug="<?= $default_fundraiser->slug; ?>">VIEW GAMES</a>
                                    </div>
                                    <div class="col-lg-4 pt-1">
                                        <a class="btn blue btn-block" href="<?php echo asset_url('games/create'); ?>">CREATE A GAME</a>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (!isset($select_fundraiser) || !$select_fundraiser) { ?>
                        <input type="hidden" id="default_fundraiser" value="<?php echo $default_fundraiser->slug; ?>" readonly/>
                    <?php } ?>
                </div>
                
                <?php if (isset($select_fundraiser) && $select_fundraiser) { ?>
                    <div class="text-center" id="selectedFundraiserError"></div>
                <?php } ?>
            </div>
        </div>
    </div>
    
    <?php if (isset($select_fundraiser) && $select_fundraiser) { ?>
        <input type="hidden" id="selectedFundraiser" name="selectedFundraiser" value="<?= (!is_null($game) && $game->selected_fundraiser->slug !== '') ? $game->selected_fundraiser->slug : $default_fundraiser->slug; ?>"/>
        <input type="hidden" id="isApproved" name="isApproved" value="<?= (!is_null($game) && $game->selected_fundraiser->approved !== '') ? $game->selected_fundraiser->approved : $default_fundraiser->approved; ?>"/>
    <?php } ?>
</div>

<?php if (isset($select_fundraiser) && $select_fundraiser) { ?>
    <?php $this->load->view('fundraisers/partials/addFundraiser'); ?>
<?php } ?>