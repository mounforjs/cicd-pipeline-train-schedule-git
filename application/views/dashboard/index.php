<content class="content">
	<div class="container-fluid creatordashboard mb-2 mt-0">
	<div class="row justify-content-center p-2">
        <div class="col-lg-3 col-6 p-2">
            <a class="btn blue w-100" href="<?php echo asset_url('games/show/played'); ?>"><i class="fa fa-history" aria-hidden="true"></i> Played Games <span class="badge badge-light"><?php echo getTotalPlayed();?></span></a>
        </div>
		<div class="col-lg-3 col-6 p-2">
            <a class="btn blue w-100" href="<?php echo asset_url('games/show/wishlist'); ?>"><i class="fa fa-heart" aria-hidden="true"></i> Wishlist <span class="badge badge-light"><?php echo getTotalWishlisted();?></span></a>
        </div>
		<?php if (getprofile()->creator_status == 'Yes') { ?>
		<div class="col-lg-3 col-6 p-2">
            <a class="btn blue w-100" href="<?php echo asset_url('games/show/drafted'); ?>"><i class="fa fa-save" aria-hidden="true"></i> Draft Games <span class="badge badge-light"><?php echo getTotalDrafted();?></span></a>
        </div>
        <div class="col-lg-3 col-6 p-1">
            <a class="btn blue w-100" href="<?php echo asset_url('games/show/published'); ?>"><i class="fa fa-paper-plane" aria-hidden="true"></i> Published Games <span class="badge badge-light"><?php echo getTotalPublished();?></span></a>
        </div>
        <div class="col-lg-3 col-6 p-1">
            <a class="btn blue  w-100" href="<?php echo asset_url('games/show/live'); ?>"><i class="fa fa-gamepad" aria-hidden="true"></i> Live Games <span class="badge badge-light"><?php echo getTotalLive();?></span></a>
        </div>
        <div class="col-lg-3 col-6 p-1">
            <a class="btn blue  w-100" href="<?php echo asset_url('games/show/completed'); ?>"><i class="fa fa-check" aria-hidden="true"></i> Completed Games <span class="badge badge-light"><?php echo getTotalCompleted();?></span></a>
        </div>
        <div class="col-lg-3 p-1">
            <a class="btn blue  w-100" href="<?php echo asset_url('games/review'); ?>"><i class="fa fa-eye" aria-hidden="true"></i> Review Games <span class="badge badge-light"><?php echo getTotalReview();?></span></a>
        </div>
		<?php } ?>
    </div>
</div>

<content class="content">
    <div class="container dashanalyze pt-2">
        <h2 class="text-center dashboardHeader">ANALYZING YOUR IMPACT <i class="fas fa-chart-pie"></i></h2>
        <ul class="nav nav-tabs chartTabs" id="chartTabCategory" role="tablist">
            <li class="nav-item">
                <a class="nav-link<?php echo ($tab == 1) ? " active" : ""; ?>" id="player-tab" data-toggle="tab" href="#player" role="tab" aria-controls="player" aria-selected="<?php echo ($tab == 1) ? "true" : "false"; ?>">Player Stats</a>
            </li>
			<?php if (getprofile()->creator_status == 'Yes') { ?>
			<li class="nav-item">
                <a class="nav-link<?php echo ($tab == 2) ? " active" : ""; ?>" id="creator-tab" data-toggle="tab" href="#creator" role="tab" aria-controls="creator" aria-selected="<?php echo ($tab == 2) ? "true" : "false"; ?>">Creator Stats</a>
            </li>
			<?php } ?>
			<li class="nav-item">
                <a class="nav-link<?php echo ($tab == 0) ? " active" : ""; ?>" id="prizes-tab" data-toggle="tab" href="#prizes" role="tab" aria-controls="prizes" aria-selected="<?php echo ($tab == 0) ? "true" : "false"; ?>">Prizes</a>
            </li>
			<li class="nav-item">
                <a class="nav-link<?php echo ($tab == 3) ? " active" : ""; ?>" id="beneficiary-tab" data-toggle="tab" href="#beneficiary" role="tab" aria-controls="beneficiary" aria-selected="<?php echo ($tab == 3) ? "true" : "false"; ?>">Beneficiaries</a>
            </li>
        </ul>
        <div class="tab-content col m-0 p-2" id="activeTabs">
            <?php $this->load->view('dashboard/prizes'); ?>
            <?php if (getprofile()->creator_status == 'Yes') { ?>
				<?php $this->load->view('dashboard/creator'); ?>
			<?php } ?>
            <?php $this->load->view('dashboard/player'); ?>
			<?php $this->load->view('dashboard/beneficiary'); ?>
        </div>
        <?php $this->load->view('dashboard/notifications'); ?>
    </div>
</content>