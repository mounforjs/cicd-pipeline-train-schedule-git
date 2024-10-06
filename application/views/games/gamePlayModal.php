<div class="modal fade" id="playGameModal" role="dialog">
   <div class="modal-dialog howto">
      <!-- Modal content-->
      <div class="modal-content">
         <div class="modal-header">
            <br>
            <h4 class="whitetext">
               <?= 'PLAY - ' . strtoupper(str_replace("_", " ", $type)); ?>
            </h4>
            <button type="button" class="close" data-dismiss="modal">×</button>
         </div>
         <div class="modal-body">
            <div id="how-to-play" class="row">
               <div class="col-lg-auto mb-3">
                  <img src="https://dg7ltaqbp10ai.cloudfront.net/fit-in/shadediv.png" class="shaddiv w-100">
                  <center>
                     <p><?= $instructions; echo ($type == '2048') ? " " . $tile . " tile!" : ""; ?></p>
                     <img src="<?= $instrImg ?>" class="w-50">
                  </center>

                  <img src="https://dg7ltaqbp10ai.cloudfront.net/fit-in/shadedivbottom.png" class="shaddiv w-100">
               </div>

               <div class="col">
                  <?php if (!$this->session->userdata('user_id')) { ?>
                     <a href="<?= asset_url('login') ?>" class="btn red pull-right" id='notLoggedInPlayBtn'><span>Login/Register to Play</span></a>
                  <?php } else { ?>
                     <?php if ($account_link["required"] && !$account_link["linked"]) { ?>
                        <div class="row h-100">
                           <div class="col my-auto">
                              <p class="d-inline">You must link the appropriate account to play this game! Link them on your profile!</p>
                              <a class="btn orange link px-4" target="_blank" href="<?= asset_url("profile?tab=accounts"); ?>" style="min-height: 0;">Link</a>
                           </div>
                        </div>
                     <?php } else { ?>
                        <?php if ($creditType != "free") { ?>
                           <a class="btn orange continue pull-right">Continue</a>
                        <?php } else { ?>
                           <a href="<?= asset_url('games/playing') . "/" . $slug; ?>" class="btn red pull-right">PLAY FOR FREE </span></a>
                        <?php } ?>
                     <?php }  ?>
                  <?php } ?>
               </div>
            </div>
            
            <?php if ((!$account_link["required"] || ($account_link["required"] && $account_link["linked"])) && $this->session->userdata('user_id') && $game->credit_type !== 'free') { ?>
            <div id="play-game" class="row d-none">
               <div class="col-lg-auto">
                  <div class="row">
                     <div class="col-lg-auto col-sm-3 m-3 p-0">
                        <div class="loader d-none"><div class="imageLoader"></div></div>
                        <div id="fund-detail" class="row">
                           <?php $this->load->view('fundraisers/partials/fundraiserCard', array("play_game" => true, "fundraisers" => array((array)$game->selected_fundraiser))); ?>
                        </div>
                     </div>
                     <div class="col mt-3">
                        <div class="row">
                           <div class="col">
                              <?php if ($game->donationOption != 1) { ?>
                                 <h5 class="d-inline">Select your Beneficiary:</h5>
                                 <div class="mytooltip ml-2"><i class="fa fa-question-circle new-tip" data-toggle="tooltip" data-placement="auto" title="The selected beneficiary is bound to each attempt you make."></i></div>
                                 <div class="radio">
                                    <strong>
                                       <label class="radio-inline"><input id="all-approved-charity" type="radio" value="all-approved-charity" name="user_charity" class="user_charity_gdp" checked> All Beneficiaries</label>
                                       <label class="radio-inline"><input id="user-approved-charity" type="radio" value="user-approved-charity" name="user_charity" class="user_charity_gdp" > My Beneficiaries</label>
                                    </strong>
                                 </div>
                                 <ul class="game-desc d-block">
                                    <li id="all-ch">
                                       <select class="gametags-all player-fundraise fundraise_select" name="game_charity" id="all-charity" style="width:100%;" size="2">
                                          <?php
                                             if (!empty($all_approved_charity_list)) {
                                                foreach($all_approved_charity_list as $approved_charity_list) {
                                                   if ($approved_charity_list->slug == $game->selected_fundraiser->slug) {
                                                      // $user_approved_charity_list[] = $approved_charity_list;
                                                      $selected = 'selected';
                                                      $row_bg_class = 'bg-success';
                                                   } else {
                                                      $selected = '';
                                                      $row_bg_class = '';
                                                   } ?>
                                                <option <?= $selected; ?> class='<?= $row_bg_class; ?>' value="<?= $approved_charity_list->slug?>"><?= $approved_charity_list->name; ?></option>
                                          <?php } 
                                             } ?>
                                       </select>
                                    </li>
                                    <li id="user-ch" class="d-none">
                                       <select class="gametags-user player-fundraise fundraise_select" name="game_charity" id="user-charity" style="width:100%;" size="2" <?= (empty($user_approved_charity_list))  ? 'placeholder="No beneficiaries found." disabled' : ""; ?>>
                                          <?php
                                             if (!empty($user_approved_charity_list)) {
                                                foreach($user_approved_charity_list as $index => $user_charity) { ?>
                                                <option class='' value="<?= $user_charity->slug?>" ><?= $user_charity->name?></option>
                                          <?php } } ?>
                                       </select>
                                    </li>
                                 </ul>
                                 <hr style="width: 100%">
                              <?php } ?>
                           </div>
                        </div>
                        <div class="row">
                           <div class="col">
                              <div class="row">
                                 <div class="col">
                                    <h5 class="d-inline">Select the amount you'd like to contribute! Feeling generous? Add more to your contribution!</h5> 
                                    <div class="mytooltip ml-2"><i class="fa fa-question-circle new-tip" data-toggle="tooltip" data-placement="auto" title="Should you win, any amount over the default is divided up as the creator specified: 
                                       <?php if ($game->donationOption != 1) { ?>
                                          Creator Beneficiary: <?= round_to_2dc($game->beneficiary_percentage*0.5); ?>% - 
                                          Winner Beneficiary: <?= round_to_2dc($game->beneficiary_percentage*0.5); ?>% - 
                                       <?php } else { ?>
                                          Beneficiary: <?= round_to_2dc($game->beneficiary_percentage); ?>% - 
                                       <?php } ?>
                                          Creator: <?= round_to_2dc($game->creator_percentage); ?>% -
                                          Winner: <?= round_to_2dc($game->winner_percentage); ?>% - 
                                          WinWinLabs: <?= round_to_2dc($game->wwl_percentage); ?>%"></i>
                                    </div>
                                 </div>
                              </div>
                              
                              <?php $allow_add = ($game->credit_cost <= getBalanceAsFloat()); ?>

                              <div class="row">
                                 <div class="col">
                                    <div id="play-default" class="row px-2 mt-3 pull-right">
                                       <?php if ($creditType != "free") { ?>
                                          <a id="play-custom" class="btn red my-auto p-1">CUSTOM</a>
                                          <a id="play-minus" class="btn red my-auto p-1 d-none"><i class="fa fa-minus" aria-hidden="true"></i></a>
                                          <a id="play-add" class="btn red my-auto p-1 <?= ($allow_add) ? "" : "d-none"; ?>"><i class="fa fa-plus" aria-hidden="true"></i></a>
                                       <?php } ?>

                                       <a id="pay-to-play" class="btn red <?= ($allow_add) ? "" : "disabled"; ?>" <?= ($allow_add) ? "" : "disabled"; ?>>
                                          PLAY FOR $<span id="credit_cost_text"><?= round_to_2dc($credit_cost); ?></span>
                                       </a>
                                    </div>
                                 </div>
                              </div>

                              <div class="row d-none">
                                 <input type="hidden" name="selected_beneficiary" value="<?= $game->selected_fundraiser->slug; ?>" />
                                 <input type="hidden" name="contribution" min="<?= $game->credit_cost; ?>" value="<?= $game->credit_cost; ?>" />
                                 <input type="hidden" name="available_balance" value="<?= getBalanceAsFloat(); ?>" />
                              </div>
                              <div id="amountErrorContainer" class="row mt-2 <?= ($allow_add) ? "d-none" : ""; ?>">
                                 <div class="col">
                                    <div class="row pull-right">
                                       <div class="col d-flex">
                                          <i class="fas fa-exclamation-triangle my-auto mr-2" style="color: #f0ff00;" aria-hidden="true"></i>
                                          <div id="amountError" class="font-weight-bold">
                                             <?php if (!$allow_add) { ?>
                                                Insufficient credits available. Please <u><a href="<?= base_url() . "/games/playing/" . $game->slug . "/?custom_amount=" . $game->credit_cost; ?>" class="text-primary">buy more credits</a></u> or adjust donation amount.
                                             <?php } ?>
                                          </div>
                                       </div>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>

               <a class="btn orange continue mt-3 ml-3">Back</a>
            </div>
            <?php } ?>
         </div>
      </div>
   </div>
</div>

<!-- spend credit tracking -->
<script>
   $('.gTagPlayBtn').on("click", function() {
      window.dataLayer = window.dataLayer || [];
      window.dataLayer.push({
         'virtual_currency_name': 'Credits',
         'value': '<?= $credit_cost; ?>', //Replace this with the number of currency they spent
         'event': 'spend_virtual_currency'
      });
   });
</script>