<?php foreach ($fundraisers as $key => $fundraiser) { ?>
    <div class="<?= ($play_game) ? "col-lg-auto" : "col-lg-3 col-md-6"; ?> col-12 card-group mb-3">
        <div class="card bg-light fundraisecard <?= ($play_game) ? "" : "pack"; ?>">
            <a href="<?= base_url("/fundraisers/show/all/" . $fundraiser["slug"]); ?>">
                <div class="card-header mainimg">
                    <img src="<?= $fundraiser["Image"]["image"]; ?>" onerror="imgError(this, '<?= $fundraiser['Image']['fallback']; ?>')" loading="lazy">
                </div>
            </a>
            <a class="card-header card-title" href="<?= base_url("/fundraisers/show/all/" . $fundraiser["slug"]); ?>">
                <h5><?php echo $fundraiser["name"]; ?></h5>
            </a>
            <div class="myfundcategory <?php echo $fundraiser["fundraise_type"]; ?>">
                <?php echo $fundraiser["fundraise_type"]; ?>
            </div>

            <?php if ($fundraiser["approved"] != 'Yes' && !$play_game) { ?>
                <div class="card-header pending"><i class="fas fa-user-clock"></i> PENDING APPROVAL</div>
            <?php } ?>

            <?php if ($fundraiser["approved"] == "Yes") { ?>
                <div class="donate p-1 show">
                    <div class="donationgroup">
                        <?php if (isset($fundraiser["raised"]) && (float)str_replace(",", "", $fundraiser["raised"]) > 250) { ?>
                            <div class="raised-text"><span>RAISED: </span> $<?php echo $fundraiser["raised"]; ?></div>
                        <?php } ?>
                    </div>
                    <?php if (!$play_game) { ?>
                        <div class="row p-0 m-sm-1">
                            <div class="col-6 pl-2 pr-1">
                                <a class="btn btn-block small" href="<?php echo base_url() . "games/show/play/?beneficiary=" . $fundraiser["slug"]; ?>">
                                    GAMES
                                </a>
                            </div>
                            <div class="col-6 pr-2 pl-1">
                                <button type="button" class="btn white btn-block small payDonateBtn" fundraiser-name="<?php echo $fundraiser["name"]; ?>" slug="<?php echo $fundraiser["slug"]; ?>">
                                    DONATE
                                </button>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>

            <div class="actions p-1 text-center">
                <?php if ((isset($usertype) || $play_game) && $usertype != 2) { ?>
                    <?php if ((isset($fundraiser["def"]) && $fundraiser["def"] == 0) || (!isset($fundraiser["def"]) && $fundraiser["slug"] != $default_fundraiser)) { ?>
                        <a data-slug='<?php echo $fundraiser["slug"]; ?>' class="btn blue small makeDefaultCardbtn">MAKE DEFAULT</a>
                    <?php } else { ?>
                        <a class="btn green small">DEFAULT</a>
                    <?php } ?>
                <?php } ?>

                <?php if (!$play_game) { ?>
                    <?php if ($type == "supported") { ?>
                        <a class="minussupport deleteFundraiserBtn btn blue small" method-type="removeSupportedFundraiser" data-slug="<?php echo $fundraiser["slug"]; ?>"><i class="fa fa-minus-circle" aria-hidden="true"></i> REMOVE</a>
                    <?php } else if ($usertype == 2) { ?>
                        <a class="editFundraiserBtn btn blue small" data-slug="<?php echo $fundraiser["slug"]; ?>"><i class="fa fa-pencil" aria-hidden="true"></i></a>

                        <?php if ($fundraiser["fundraiserPendingStatus"] === 0) { ?>
                            <span class="badge badge-info">EDIT Requested</span>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>

                <?php if (!$play_game) { ?>
                    <?php if ($type != "supported" && $type != "all" && (isset($usertype) && $usertype != 2)) { ?>
                        <?php if ($fundraiser["fundraiserPendingStatus"] !== 0) { ?>
                            <a class="editFundraiserBtn btn blue small" data-slug="<?php echo $fundraiser["slug"]; ?>"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                        <?php } else { ?>
                            <?php if ($usertype != 2) { ?>
                                <span class="badge badge-info">Request Pending</span>
                            <?php } else { ?>
                                <a class="editFundraiserBtn" data-slug="<?php echo $fundraiser["slug"]; ?>">EDIT Requested <i class="fas fa-chevron-circle-right"></i></a>
                            <?php } ?>
                        <?php } ?>

                        <?php if ($fundraiser["approved"] != "Yes" && $fundraiser["slug"] != $default_fundraiser) { ?>
                            <a class="minussupport deleteFundraiserBtn btn blue small" data-slug="<?php echo $fundraiser["slug"]; ?>"><i class="fa fa-trash" aria-hidden="true"></i></a>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>