
<content class="content adminpage">
   <div class="container-fluid">
      <div class="row">
         <div class="col-12 p-4">
               <h3 class="mr-auto">Balance: <?php  echo '<i class="fa fa-dollar"></i>'.round_to_2dc(($sum)); ?></h3>
               <?php if (isset($user_id) && !empty($user_id) ) { ?>
          			<div class="row justify-content-center">
          				<div class="col-md-6">
          					<h2 class="text-center"><span class="text-capitalize"><?php echo get_game_user($user_id)->firstname.' </span><span class="text-lowercase">('.get_game_user($user_id)->email.')'; ?> </span></h2>
          				</div>
          			</div>
          			<?php } ?>
                  <input id="deferLoad" type="hidden" data-filtered="<?php echo $deferLoading["filtered"]; ?>" data-total="<?php echo $deferLoading["total"]; ?>"/>
            <table id="myAdvancedTable" class="table table-striped table-bordered" style="width:100%" data-type="userTransactions">
               <thead>
                  <tr>
                     <th class="border-top-0">Date</th>
                     <th class="border-top-0">Type</th>
                     <th class="border-top-0">Amount</th>
                     <th class="border-top-0">Total Charges</th>
                     <th class="border-top-0">Total Balance</th>
                     <th class="border-top-0">Description</th>
                     <th class="border-top-0">Donation Status</th>
                     <th class="border-top-0">Mode</th>
                     <th class="border-top-0">Game Name</th>
                     <th class="border-top-0">Rank</th>
                     <th class="border-top-0">Fundraiser</th>
                     <th class="border-top-0">User Type</th>
                     <th class="border-top-0">Is Deductible</th>
                     <th class="border-top-0">Reference ID</th>
                  </tr>
               </thead>
               <tbody>
                  <?php foreach ($transactions as $key => $credit_row) : ?>
                  <tr>
                     <td>
                        <?php echo date("m-d-y H:i:s",strtotime($credit_row['Date']))?>
                     </td>
                     <td>
                        <?php if ($credit_row['Status']!=1) {
                              echo '<div class="status badge badge-danger badge-pill badge-sm">Debit</div>';
                              }
                              else {
                              echo '<div class="status badge badge-success badge-pill badge-sm">Credit</div>';
                              }
                           ?>
                     </td>
                     <td>
                        <i class="fa fa-dollar"></i>
                        <?php if ($credit_row['Status']!=1) {
                           $usd_debit=$credit_row['Credits'];
                           echo round_to_2dc($usd_debit);
                           }
                           else{
                           $usd_credit=$credit_row['Credits'];
                           echo round_to_2dc( $usd_credit);
                           }
                           ?>
                     </td>
                     <td>
                        <i class="fa fa-dollar"></i>
                        <?php
                           $usd_credit=$credit_row['total_charge'];
                           echo round_to_2dc($usd_credit);
                        ?>
                     </td>
                     <td>
                        <i class="fa fa-dollar"></i>
                        <?php
                           $usd_total=$credit_row['total_credits'];
                           echo round_to_2dc($usd_total);
                           ?>
                     </td>
                     <td>
                        <?php echo $credit_row['Notes']?>
                     </td>
                     <td>
                        <div class="status badge badge-success badge-pill badge-sm"><?php echo $credit_row['payment_status']; ?></div>
                     </td>
                     <td>
                        <?php echo ($credit_row['payment_mode'] == 1) ? 'Bank ACH' : (($credit_row['payment_mode'] == 2) ? 'Paypal' : (($credit_row['payment_mode'] == 3) ? 'Credit Card' : 'WinWinLabs')); ?>
                     </td>
                     <td>
                        <?php echo $credit_row['game_name']?>
                     </td>
                     <td>
                        <?php echo  $credit_row['final_rank']; ?>
                     </td>
                     <td>
                        <a data-placement="right" data-toggle="popover"  data-html="true" href="#" id="<?php echo (($credit_row['user_type'] == 1) ? $credit_row['creator_fundraise_id'] : $credit_row['winner_fundraise_id']) ;?>" class="fund_name" >
                        <?php echo (($credit_row['user_type'] == 1) ? $credit_row['creator_fundraise_name'] :
                        (($credit_row['donated_to_fundraiser_name'] != null)?$credit_row['donated_to_fundraiser_name']:$credit_row['winner_fundraise_name'])); ?>
                        </a>
                     </td>
                     <td>
                        <?php
                           $user_type = $credit_row['user_type'];

                           switch ($user_type ) {
                               case "1":
                                   $utype = "Creator";
                                   break;
                               case "2":
                                  $utype = "Creator Fundraise";
                                   break;
                               case "3":
                                  $utype = "Main Winner";
                                   break;
                               case "4":
                                  $utype = "Winner Fundraise";
                                   break;
                               case "5":
                                   $utype = "Sub Winner";
                                   break;
                               case "6":
                                   $utype = "goEDU";
                                   break;
                               default:
                                   $utype = "N/A";
                           }
                           echo  $utype;
                           ?>
                     </td>
                      <td>
                        <?php echo ($credit_row['is_deductible']==1)? 'Yes':'No'; ?>
                     </td>
                     <td>
                        <?php echo $credit_row['ref_num']; ?>
                     </td>
                  </tr>
                  <?php  endforeach; ?>
               </tbody>
            </table>
   </div>
 </div>
</div>
</content>
