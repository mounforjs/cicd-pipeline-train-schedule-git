<section>
   <?php $uriSegments = explode("/", parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)); ?>
   <div class="container">
      <hr>
      <div class="row">
         <div class="col-sm-12">
            <a class="viewmorecards" href="<?php echo (base_url() . ("games/show/play/" . (($uriSegments[1] == 'games') ? ("?user=" . $game->username) : ("?beneficiary=" . $slug)))); ?>">VIEW MORE <i class="fa fa-chevron-right" aria-hidden="true"></i></a>
            <h2>
               <?php
               $attribute = ($uriSegments[1] == 'games') ? 'More from this Creator' : 'Games from this Beneficiary';
               echo $attribute;
               ?>
            </h2>
         </div>
      </div>
      <div id="moreUserGames">
         <div class="row">
            <?php $this->load->view("games/gameCard.php", $game_list); ?>
         </div>
      </div>
      <br><br>
   </div>
</section>