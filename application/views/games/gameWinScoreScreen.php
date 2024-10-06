<body id="winscreen">
   <?php if (!isset($preview)) { ?>
      <script src="<?php echo asset_url('assets/tinymce/tinymce.min.js'); ?>"></script>
      <link rel="stylesheet" href="<?php echo asset_url('assets/css/tinymce.css'); ?>">
      <script src="<?php echo asset_url('assets/js/tinycustom.js'); ?>"></script>
      <script type="text/javascript" src="<?php echo asset_url('assets/js/flag.js');?>"></script>
      <script type="text/javascript" src="<?php echo asset_url('assets/js/rating.js');?>"></script>
   <?php } ?>

   <div class="container-fluid">
      <center>
         <p>PRESS ESC TO EXIT FULLSCREEN</p>
         <img src="https://dg7ltaqbp10ai.cloudfront.net/fit-in/1591014973-winscreenbanner.png" class="winbanner animate__animated animate__flip" alt="Great Job!">
         <div class="animate__animated animate__bounceInDown animate__delay-1s">
            <h3 class="thankyou">THANK YOU FOR PLAYING!</h3>
            <h3 class="text-white">Your support for <strong class='orange'><?= ($game_details->donationOption == 2 && isset($game_details->selected_fundraiser)) ? "{$game_details->selected_fundraiser->name} and {$game_details->game_fundraiser->name}" : $game_details->game_fundraiser->name; ?></strong> is appreciated.</h3>
         <br>
         <div class="animate__animated animate__bounceInUp animate__delay-1s">
            <div class="btn-group" role="group" aria-label="First group">
               <button type="button" class="btn <?= (isset($preview) && $preview) ? "disabled" : ""; ?>" onClick="window.location.href='<?= base_url() . "games/playing/" . $game_details->slug . (($game_details->donationOption == 2) ? ("/?selected_beneficiary=" . $game_details->selected_fundraiser->slug . "&custom_amount=" . $game_score->custom_amount) : "/");?>'" <?= (isset($preview) && $preview) ? "disabled" : ""; ?>><i class="fa fa-gamepad" aria-hidden="true"></i> PLAY AGAIN FOR <?= ' $'.number_format($game_score->custom_amount ? $game_score->custom_amount : $game_details->credit_cost, 2);?></button>
               <div class="btn-group" role="group">
                  <button id="btnGroupDrop1" type="button" class="btn orange dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="far fa-eye"></i> BROWSE MORE GAMES</button>
                  <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                      <a class="dropdown-item" href="<?php echo asset_url('games/show/play'); ?>">All Games</a>
                     <a class="dropdown-item" href="<?php echo asset_url('games/show/play/?game_type='.$game_details->gameType);?>"><?php echo ucfirst($game_details->gameType);?> Games</a>
                     <a class="dropdown-item" href="<?php echo asset_url('games/show/play/?user='.$game_details->username);?>">Games by this Creator</a>
                     <a class="dropdown-item" href="<?php echo asset_url('games/show/play/?beneficiary='.$game_details->fundraise_slug);?>">Games for this Fundraiser</a>
                  </div>
               </div>
            </div>
            <br><br>
            <div class="winboard">
               <div class="avatar-preview pull-right">
                  <img src="<?php $image = getImagePathSize($user->profile_img_path, 'profile_image'); echo $image['image']; ?>" onerror="this.onerror=null; this.src='<?= $image['fallback']; ?>'" alt="<?php echo $user->username; ?>">
               </div>
               <h4 class="pull-right"><?php echo $user->username; ?></h4>
               <h2 style="text-align: left;">GAME STATS</h2>
               <hr>
               <br>
               <p>We will notify you about the results soon!</p>
                  <?php switch ($game_details->gameType) {
                        case 'challenge':
                           $score = 100;
                           break;
                        default:
                           $score = $game_score->score;
                           break;
                  } ?>
                  <?php if ($game_details->gameType !== 'challenge') { ?>
                     <p><strong><i class="fas fa-star"></i> 
                        YOUR <?php echo ($game_details->gameType == 'puzzle') ? 'STEPS' : 'SCORE';?>:
                     </strong> 
                     <?php echo $score; ?>
                     <br>
                  <?php } ?>

                  <?php if ($game_details->gameType == 'challenge') { ?>
                     <?php if ($game_details->credit_type != 'free') { ?>
                        <strong><i class="fas fa-clock"></i> FINISHING TIME:</strong> 
                           <?php echo formatSeconds($game_score->completed_in); ?>
                        </p>
                     <?php } ?>
                  <?php } else { ?>
                     <strong><i class="fas fa-clock"></i> FINISHING TIME:</strong> 
                     <?php echo formatSeconds($game_score->completed_in); ?>
                     </p>
                  <?php } ?>
               <?php if (isset($game_details->gameEndDescription)) { ?>
                 <p><?php echo $game_details->gameEndDescription; ?></p>
               <?php } ?>
               <span><strong>RATE THIS GAME</strong></span>
               <h5 id="rating">
                  <?php for ($i = 0; $i <= 4; $i++) { ?>
                     <?php if ($i < $rating) { ?>
                        <i class="fas fa-star rateorange"></i>
                     <?php } else { ?>
                        <i class="fas fa-star"></i>
                     <?php } ?>
                  <?php } ?>
               </h5>
               <button class="btn blue" id="rtg-btn" data-rating="<?php echo $rating - 1; ?>" style="display: none;"><span>Submit</span></button>
            </div>
            <br>
            <button class="btn blue" id="fg-btn" class="<?= (isset($preview) && $preview) ? "disabled" : ''; ?>" data-id="<?php echo $game_details->id; ?>"  data-toggle="modal" data-target="#flagModal"><i class="fa fa-flag-o"></i><span> Flag</span></button><br>
         </div>
      </center>

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
                  <textarea id="flag_reason" name="flag_reason" required="required" rows="4" cols="50" ></textarea>
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-primary flag-btn" >Submit</button>
                  <input type="hidden" value = "<?php echo $this->session->userdata('user_id'); ?>" id="uid" data-id = "<?php echo $game_details->id; ?>">
               </div>
            </div>
         </div>
      </div>
   </div>

<script>
   $(window).on("load", function() {
      //everything is fully loaded, don't use me if you can use DOMContentLoaded
      window.dataLayer = window.dataLayer || [];
      window.dataLayer.push({
         'score': <?php echo $score; ?>,
         'event': 'post_score'
      });
   });
</script>
