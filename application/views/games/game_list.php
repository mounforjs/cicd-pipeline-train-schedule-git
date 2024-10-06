<content class="content">
   <div class="container-lg">
      <div class="row no-gutters showData">
         <?php $this->load->view("games/gameCard.php", $game_data); ?>
      </div>
      
      <div class="row justify-content-center mt-2">
         <a id="loadMoreButton" class="btn <?php echo (count($game_data) < 6) ? "d-none" : ""; ?>">SHOW MORE</a>
      </div>

      <div id="noRecordsFound" class="<?php echo (count($game_data) > 0) ? "d-none" : ""; ?>">
         <div class="fof">
            <h1>
               <?php $gStatus = $this->uri->segment(3);
                  switch ($gStatus) {
                  case "published":
                    echo "No published games found!";
                    break;
                  case "live":
                    echo "No live games found!";
                    break;
                  case "play":
                  case "played":
                    echo "No games found!";
                    break;
                  case "wishlist":
                    echo "No wishlisted games found!";
                    break;
                  case "completed":
                     echo "No completed games found!";
                     break;
                  case "review":
                    echo "No review games found!";
                    break;
                  default:
                    echo "No draft games found!";
                  }
                  ?>
               </h1>
            </div>
         </div>
      </div>
      <div id="noMoreRecords" class="row justify-content-center d-none">
         <div class="fof">
            <h2>No more games found!</h2>
         </div>
      </div>
   </div>
   <div id="divLoading"> </div>
   <br><br>
</content>
<!-- Content End -->