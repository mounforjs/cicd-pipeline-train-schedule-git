<!-- Content Start -->
<content class="content">
   <div class="container">
      <div class="row">
         <div class="col-12 p-4">
            <h1>Manage "Review" Games</h1>
            <input name="deferLoad" type="hidden" data-filtered="<?php echo $deferLoading["filtered"]; ?>" data-total="<?php echo $deferLoading["total"]; ?>" />
            <table id="myAdvancedTable" class="table table-striped table-bordered" style="width:100%" data-type="reviewGames">               
            <thead>
                  <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Fundraise</th>
                    <th>Value</th>
                    <th>Cost to Play</th>
                    <th>Total Players</th>
                    <th>Game Status</th>
                    <th>Review</th>
                  </tr>
               </thead>
               <tbody>
                  
                  <?php 
                    foreach ($games_list as $key => $game) :
                  ?>
                    <tr>
                        <td>
                           <img width="50px" height="50px" src="<?php $image = getImagePathSize($game->Game_Image, 'admin_games'); echo $image["image"]; ?>" onerror="imgError(this, '<?= $image['fallback']; ?>')">
                        </td>
                        <td>
                           <?php echo $game->name?>
                        </td>
                        <td>
                           <?php echo ($game->credit_type == 'prize') ? 'prize-'. $game->gametype : $game->gametype;?>
                        </td>
                        <td>
                           <?php echo $game->charityname?> 
                        </td>
                        <td>
                           <?php echo '$'.round_to_2dc($game->value_of_the_game)?>
                        </td>
                        <td>
                           <?php echo '$'.round_to_2dc($game->credit_cost)?>
                        </td>
                        <td>
                           <?php echo isset($game->player_count) ? $game->player_count : 0; ?>
                        </td>
                        <td>
                            <?php echo $game->Status; ?> 
                         </td> 
                        <td>
                           <a href="<?php echo asset_url('games/review/').$game->slug; ?>" class="btn btn-primary" ><?php echo ($game->processed == 1 && $game->review_status == 0) ? "Reviewed" : "Review"; ?></a>
                        </td>
                     </tr>
                  <?php  endforeach; ?>
               </tbody>
            </table>
         </div>
      </div>
   </div>
   </div>
   </div>
   <div id="divLoading"> </div>
</content>
<!-- Content End -->
