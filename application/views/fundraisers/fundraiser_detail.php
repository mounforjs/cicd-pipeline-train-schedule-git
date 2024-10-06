<!-- Content Start -->
<content class="content">
    <section class="fundraiser-heading">
        <div class="container">
            <div class="fundraiser-heading-wrap">
                <div class="fundraiser-heading-inner">
                    <h2><?php echo $name; ?></h2>
                </div>
            </div>
        </div>
        </div>
        </div>
    </section>
    <section class="item-imgs-prize">
        <div class="container">
        <div class="row">
            <div class="col-lg-4">
                <div class="fd myfundcategory <?php echo $fundraise_type; ?>">
                    <?php if ($fundraise_type == 'charity') { ?>
                    <i class="fas fa-hand-holding-heart"></i> Charity 
                    <?php } else if ($fundraise_type == 'project') { ?>
                    <i class="fas fa-lightbulb"></i> Project 
                    <?php } else if ($fundraise_type == 'education') { ?>
                    <i class="fa fa-graduation-cap"></i> Education 
                    <?php } else { ?>
                    <i class="fa fa-globe"></i> Cause 
                    <?php } ?>
                </div>
                <div class="donationgroup">
                    <?php if (isset($totalRaised) && $totalRaised->raised != '' && (float)$totalRaised->raised > 250){ ?>
                    <span class="fd raised-text left">
                    <span>RAISED: </span> <?php echo ' $'.round_to_2dc($totalRaised->raised); ?>
                    </span>
                    <?php } ?>
                </div>
                <div class="fundraiserdetailimg">
                    <center><img src="<?= $Image["image"]; ?>" onerror="imgError(this, '<?= $Image['fallback']; ?>')" alt="<?php echo $name; ?>"></center>
                    <br>
                </div>
            </div>
            <div class="col-lg-4 mb-2">
                <button class="btn red play-game-btn payDonateBtn w-100 mb-2 mt-2" type="button" fundraiser-name="<?php echo $name; ?>" fundraiser-slug="<?php echo $slug; ?>">DONATE <i class="fas fa-chevron-right"></i></button>
                <div class="row bottombtns pb-2">
                    <div class="col-6 pr-1">
                        <a  href="<?php echo asset_url('games/show/play/?beneficiary=' . $slug); ?>" class="btn blue btn-block"><i class="fas fa-eye"></i> VIEW GAMES</a>
                    </div>
                    <div class="col-6 pl-1"><a target="_blank" href="<?php echo addScheme($charity_url); ?>" class="btn blue btn-block"><i class="fas fa-globe"></i> WEBSITE</a></div>
				</div>
			</div>
            </div>
            <div class="row detail-about-prize">
                <div class="col-12">
                    <div class="aboutfund">
                        <h2>About this Beneficiary:</h2>
                        <p><?php echo $Description; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Donation Form Start -->
        <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" id="donationModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <?php $this->load->view('forms/donation'); ?>
                </div>
            </div>
        </div>
        <!--  Donation Form End -->
    </section>
    <?php $this->load->view('games/moreGamesList'); ?>
</content>
<!-- Content End -->