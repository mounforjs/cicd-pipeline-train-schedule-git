<!-- Content Start -->
<content class="content adminpage">
   <div class="container-fluid">

      <div class="row">
         <div class="col-12 p-4">
            <h1>Manage Games</h1>
            <div class="row justify-content-between p-2">
               <div class="col-lg-8"></div>
               <div class="col-lg-4 text-right">
                  <button type="button" name="btnGameDel" id="btnGameDel" class="btn">Delete Selected Games</button>
               </div>
            </div>
            <div class="carddivider"></div>
            <input name="deferLoad" type="hidden" data-filtered="<?php echo $deferLoading["filtered"]; ?>" data-total="<?php echo $deferLoading["total"]; ?>"/>
            <table id="myAdvancedTable" class="table table-striped table-bordered dt-responsive nowrap" width="100%" data-type="games">
               <thead>
                  <tr>
                     <th id='chr'>Icon</th>
                     <th id='desc'>Name</th>
                     <th id='desc'>Type</th>
                     <th id='de'>Creator Name</th>
                     <th id='chr'>Fundraise</th>
                     <th id='chr'>Value</th>
                     <th id='chr'>Cost to Play</th>
                     <th id='chr'>Game Status</th>
                     <th id='chr'>Active</th>
                     <th id='chr'>Test/Prod</th>
                     <th id='chr'>Details</th>
                     <th id='chr'>Delete</th>
                     <th id='chr'>Flags</th>
                     <th id='chr'>Date Created</th>
                  </tr>
               </thead>
               <tbody>
                  <?php
                  foreach ($games_list as $key => $allgames_row) :

                  ?>
                     <tr class=''>
                        <td id='ord_<?php echo $allgames_row->user_id ?>' class="crd">
                        <img width="50px" height="50px" src='<?php $image = getImagePathSize($allgames_row->Game_Image, 'admin_games'); echo $image["image"]; ?>' data-fallback="<?php echo $image['fallback']; ?>" alt="<?php echo $allgames_row->name ?>">
                        </td>
                        <td id='des_<?php echo $allgames_row->user_id ?>' class="crd">
                           <?php echo $allgames_row->name ?>
                        </td>
                        <td id='src_<?php echo $allgames_row->user_id ?>' class="crd">
                           <?php echo ($allgames_row->credit_type == 'prize') ? 'Prize-' : '';
                           echo $allgames_row->gametype; ?>
                        </td>
                        <td id='ord_<?php echo $allgames_row->user_id ?>' class="crd">
                           <?php echo $allgames_row->firstname . " " . $allgames_row->lastname; ?>
                        </td>
                        <td id='ord_<?php echo $allgames_row->user_id ?>' class="crd">
                           <?php echo $allgames_row->charityname ?>
                        </td>
                        <td id='ord_<?php echo $allgames_row->user_id ?>' class="crd">
                           <?php echo '$' . round_to_2dc($allgames_row->value_of_the_game) ?>
                        </td>
                        <td id='ord_<?php echo $allgames_row->user_id ?>' class="crd">
                           <?php echo '$' . round_to_2dc($allgames_row->credit_cost) ?>
                        </td>
                        <td>
                           <?php if ($allgames_row->Publish == 'Yes') {
                              echo 'Published';
                           } elseif ($allgames_row->Publish == 'Live') {
                              echo 'Live';
                           } else {
                              echo 'Draft';
                           }
                           ?>
                        </td>
                        <td>
                           <input type="checkbox" data-on="Yes" data-off="No" class='game_active_switch' <?php if ($allgames_row->active == 'Yes') {
                                                                                                                                 echo 'checked="checked"';
                                                                                                                              } ?> value="<?php echo $allgames_row->id; ?>" />
                        </td>
                        <td>
                           <input type="checkbox" data-on="Prod" data-off="Test" class='game_server_switch' <?php if ($allgames_row->isProd == 1) {
                                                                                                                                    echo 'checked="checked"';
                                                                                                                                 } ?> value="<?php echo $allgames_row->id; ?>" />
                        </td>
                        <td><a target="_blank" href="<?php if ($allgames_row->Publish == 'Yes') {
                                                         echo asset_url('games/show/published/' . $allgames_row->slug);
                                                      } elseif ($allgames_row->Publish == 'Live') {
                                                         echo asset_url('games/show/live/' . $allgames_row->slug);
                                                      } else {
                                                         echo asset_url('games/show/drafted/' . $allgames_row->slug);
                                                      }  ?>" class="btn btn-primary" value="<?php echo $allgames_row->slug; ?>">View/Edit</a></td>
                        <td><input type="checkbox" name="<?php echo $allgames_row->id; ?>" class="delete_game" value="<?php echo $allgames_row->id; ?>" /></td>
                        <td><a href="<?php echo asset_url('admin/flags/' . $allgames_row->slug); ?>" class="btn btn-primary" value="<?php echo $allgames_row->id; ?>">Manage Flags</a></td>
                        <td id='ord_<?php echo $allgames_row->user_id ?>' class="crd">
                           <?php echo $allgames_row->created_at ?>
                        </td>
                     </tr>
                  <?php endforeach; ?>
               </tbody>
            </table>

         </div>
      </div>
   </div>
   </div>
   </div>
</content>
<!-- Content End -->