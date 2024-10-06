<nav class="navbar navbar-expand-lg navbar-dark bg-light" id="adminbar">
   <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
   <span class="navbar-toggler-icon"></span>
   </button>
   <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav mr-auto">
         <?php if(isAdmin() || checkSysAdminLogin() || checkSupporterLogin()) { ?>
         <?php if(isAdmin() || checkSysAdminLogin()) { ?>
            <li class="nav-item dropdown">
               <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">DATA
               </a>
               <div class="dropdown-menu" aria-labelledby="navbarDropdown">
               <p><a href="<?php echo asset_url('admin/users'); ?>">Users</a></p>
               <p><a href="<?php echo asset_url('admin/games'); ?>">Games</a></p>
               <p><a href="<?php echo asset_url('Question/list'); ?>">Questions</a></p>
               <p><a href="<?php echo asset_url('Quiz/list'); ?>">Quiz</a></p>
               <p><a href="<?php echo asset_url('fundraisers/show'); ?>">Admin Fundraisers</a></p>
               <p><a href="<?php echo asset_url('fundraisers/show'); ?>">Approved User's Fundraisers</a></p>
               </div>
            </li>
         <?php } ?>
         <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">FINANCIALS
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
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
         </li>
         <?php } ?>
         <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">CONTENT
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
              <p><a href="<?php echo asset_url('admin/quotes'); ?>">Quotes</a></p>
              <p><a href="<?php echo asset_url('admin/content'); ?>">Content(TBD)</a></p>
              <p><a href="<?php echo asset_url('admin/about'); ?>">About Page</a></p>
              <p><a href="<?php echo asset_url('admin/faq'); ?>">FAQ</a></p>
              <p><a href="<?php echo asset_url('admin/news'); ?>">News</a></p>
              <p><a href="<?php echo asset_url('admin/blog'); ?>">Blog</a></p>
            </div>
         </li>
         <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">CONTACT
            </a>
            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
              <p><a href="<?php echo asset_url('admin/feedback'); ?>">Feedback</a></p>
              <p><a href="<?php echo asset_url('admin/subscription'); ?>">Subscription List</a></p>
              <p><a href="<?php echo asset_url('admin/emails'); ?>">Emails</a></p>
            </div>
         </li>
      </ul>
   </div>
</nav>
