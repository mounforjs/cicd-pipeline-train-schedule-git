<!-- Content Start -->
<content class="content">
	<div class="container">
		<div class="row">
		<div class="col-12">
			<h1>Admin Panel</h1>
			<div class="row adminbtn">
				<?php if(isAdmin() || checkSysAdminLogin() || checkSupporterLogin()) { ?>
					<?php if(isAdmin() || checkSysAdminLogin()) { ?>
						<div class="col-md-3">
							<h2>MANAGE DATA</h2>
							<p><a href="<?php echo asset_url('admin/users'); ?>">Users</a></p>
							<p><a href="<?php echo asset_url('admin/games'); ?>">Games</a></p>
							<p><a href="<?php echo asset_url('challenge/question'); ?>">Questions</a></p>
							<p><a href="<?php echo asset_url('challenge/quiz'); ?>">Quiz</a></p>
							<p><a href="<?php echo asset_url('fundraisers/show'); ?>">Admin Fundraisers</a></p>
							<p><a href="<?php echo asset_url('fundraisers/show'); ?>">Approved User's Fundraisers</a></p>
						</div>
					<?php } ?>
						<div class="col-md-3">
							<h2>MANAGE FINANCIALS</h2>
							<?php if(isAdmin()) { ?>
								<p><a href="<?php echo asset_url('admin/paymentkeys'); ?>">Payment Method Credentials</a></p>
							<?php } ?>

							<p><a href="<?php echo asset_url('admin/distributions'); ?>">Distributions</a></p>
							<p><a href="<?php echo asset_url('admin/allTransactions'); ?>">Transactions</a></p>

							<?php if(isAdmin() || checkSysAdminLogin()) { ?>
								<p><a href="<?php echo asset_url('admin/coupons'); ?>">Coupons</a></p>
								<p><a href="<?php echo asset_url('admin/referral'); ?>">Referrals</a></p>
							<?php } ?>
						</div>
				<?php } ?>
				<div class="col-md-3">
					<h2>MANAGE CONTENT</h2>
					<p><a href="<?php echo asset_url('admin/quotes'); ?>">Quotes</a></p>
					<p><a href="<?php echo asset_url('admin/short_urls'); ?>">Short Urls</a></p>
					<p><a href="<?php echo asset_url('admin/content'); ?>">Content(TBD)</a></p>
					<p><a href="<?php echo asset_url('admin/about'); ?>">About Page</a></p>
					<p><a href="<?php echo asset_url('admin/faq'); ?>">FAQ</a></p>
					<p><a href="<?php echo asset_url('admin/news'); ?>">News</a></p>
					<p><a href="<?php echo asset_url('admin/blog'); ?>">Blog</a></p>
				</div>
			  <div class="col-md-3">
					<h2>CONTACT</h2>
					<p><a href="<?php echo asset_url('admin/feedback'); ?>">Feedback</a></p>
					<p><a href="<?php echo asset_url('admin/subscriptions'); ?>">Subscription List</a></p>
					<p><a href="<?php echo asset_url('admin/emails'); ?>">Emails</a></p>
			  </div>
			</div>
		</div>
		</div>
	</div>
</content>
<!-- Content End -->
