<nav id="main-nav" class="navbar navbar-expand-lg navbar-light navbar-fixed-top">
    <div class="container">
        <div class="logowrap">
            <ul class="navbar-nav">
                <li><a class="navbar-brand" href="<?php echo asset_url(); ?>"><img src="<?php echo getLogoImage() ?>" alt="WinWinLabs"></a></li>
            </ul>
        </div>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <?php if (!$this->session->userdata('user_id')) { ?>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo asset_url('about'); ?>">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo asset_url('blog'); ?>">Blog</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo asset_url('faq'); ?>">FAQ</a>
                    </li>
                </ul>
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo asset_url('login'); ?>" tabindex="-1" aria-disabled="true">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn" href="<?php echo asset_url('register'); ?>">Register</a>
                    </li>
                </ul>
            </div>
        <?php } else { ?>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto">
                    <?php if (showAdminNav()) { ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo asset_url('admin'); ?>">Admin</a>
                        </li>
                    <?php } ?>
                    <li class="nav-item"><a class="nav-link" href="<?php echo asset_url('dashboard'); ?>">Dashboard</a>
                    </li>
                    <?php if (getprofile()->creator_status == 'Yes') { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo asset_url('games/create'); ?>">Create Games</a>
                        </li>
                    <?php } ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo asset_url('games/show/play'); ?>">Play</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn" href="<?php echo asset_url('buycredits/' . getDefaultPaymentMethodType()); ?>" tabindex="-1" aria-disabled="true">Donate</a>
                    </li>
                </ul>
            </div>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle topprofile" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <img src="<?php $image = getImagePathSize(getprofile()->profile_img_path, 'profile_image_icon');
                                    echo $image['image']; ?>" onerror="imgError(this, '<?= $image['fallback']; ?>')" alt="Profile" class="profileph">
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <?php if (getprofile()->usertype == '2') { ?>
                            <a class="dropdown-item" href="<?php echo asset_url('fundraisers/show/all'); ?>"><i class="fa fa-cog" aria-hidden="true"></i> Edit Fundraisers</a>
                        <?php } ?>
                        <a class="dropdown-item" href="<?php echo asset_url('profile'); ?>"><i class="fa fa-user" aria-hidden="true"></i> My Profile</a>
                        <a class="dropdown-item" style="pointer-events: none;"><i class="fas fa-money-bill" aria-hidden="true"></i> Balance: <?php echo (!empty(getBalanceAsFloat())) ? '$' . getBalanceAsFloat() : '$0.00'; ?></a>
                        <a class="dropdown-item" href="<?php echo asset_url('transactions'); ?>"><i class="fa fa-credit-card" aria-hidden="true"></i> Transactions</a>
                        <a class="dropdown-item" data-toggle="modal" data-target="#feedbackModal"><i class="fa fa-comments" aria-hidden="true"></i> Help Us Improve</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="<?php echo asset_url('home/logout'); ?>"><i class="fa fa-power-off" aria-hidden="true"></i> Logout</a>
                    </div>
                </li>
            </ul>
            <?php $unseen = getNotificationUnseenCount(); ?>
            <div class="notification-container">
                <i class="fa fa-bell ringBell"></i>
                <div class="-count <?= ($unseen) ? '' : 'd-none' ?>"><?= $unseen ?></span>
                </div>
            <?php } ?>
            </div>
            <script>
                function scrollToFooter() {
                    var elmnt = document.getElementById("footnote");
                    elmnt.scrollIntoView();
                }
            </script>
</nav>
<div class="col-md-4 col-lg-4 notification-panel">
    <div id="notification-panel" class="panel panel-default">
        <div class="row panel-heading">
            <div class="col m-1">
                <h4 class="panel-title">Notifications</h4>
            </div>
            <div class="col">
                <a id="clearNotification" class="pull-right">Clear</a>
            </div>
        </div>
        <div class="panel-body">
        </div>
    </div>
</div>