
<content class="content">
   <div class="container">
      <div class="card">
         <div class="card-header">
            <div class="d-flex align-items-center">
               <h2 class="mr-auto mb-3 bg-success text-white btn-sm rounded">
                  Available Credits: <?php  echo '<i class="fa fa-dollar"></i>'.round_to_2dc($total_credits); ?>
               </h2>
               <h2 class="mr-auto bg-warning text-dark btn-sm rounded">
                  Donations: <?php  echo '<i class="fa fa-dollar"></i>'.round_to_2dc($donated_credits); ?>
               </h2>
               <div class="btn-group" role="group">
                  <a class="btn btn-sm" href="<?php echo asset_url('cashout/paypal'); ?>">
                     Withdraw Credits:<?php  echo '<i class="fa fa-dollar"></i>'.round_to_2dc($withdrawable_credits); ?>
                  </a>
               </div>
            </div>
         </div>
         <div class="card-body">
         	<h5 class="card-title text-center">Transaction Details</h5>
            <input name="deferLoad" type="hidden" data-filtered="<?php echo $deferLoading["filtered"]; ?>" data-total="<?php echo $deferLoading["total"]; ?>"/>
            <table id="payment-transaction" class="table table-striped table-bordered" data-type="transactions">
               <thead>
                  <tr>
                     <th class="border-top-0">Date</th>
                     <th class="border-top-0">Type</th>
                     <th class="border-top-0">Amount</th>
                     <th class="border-top-0">Total Charges</th>
                     <th class="border-top-0">Total Balance</th>
                     <th class="border-top-0">Description</th>
                     <th class="border-top-0">Transaction Status</th>
                     <th class="border-top-0">Mode</th>
                     <th class="border-top-0">Game Name</th>
                     <th class="border-top-0">Rank</th>
                     <th class="border-top-0">Beneficiary</th>
                     <th class="border-top-0">User Type</th>
                     <!-- <th class="border-top-0">Is Deductible</th> -->
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
                              echo '<div class="status badge badge-danger badge-pill badge-sm transaction-status">Debit</div>';
                              }
                              else {
                              echo '<div class="status badge badge-success badge-pill badge-sm transaction-status">Credit</div>';
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
                        <div class="status transaction-status badge badge-pill <?php echo $credit_row['badge_color']; ?>">
                           <?php echo ucfirst($credit_row['payment_status']); ?>
                        </div>
                     </td>
                     <td>
                        <?php echo ($credit_row['payment_mode'] == 1) 
                           ? 'Bank ACH' 
                           : (
                              ($credit_row['payment_mode'] == 2) 
                                    ? 'PayPal' 
                                    : (
                                       ($credit_row['payment_mode'] == 3) 
                                          ? 'Stripe' 
                                          : (
                                                ($credit_row['payment_mode'] == 4) 
                                                   ? 'Credit Card' 
                                                   : (
                                                      ($credit_row['payment_mode'] == 5) 
                                                            ? 'Referral' 
                                                            : 'WinWinLabs'
                                                   )
                                          )
                                    )
                           ); 
                        ?>
                     </td>
                     <td>
                        <?php echo $credit_row['game_name']?>
                     </td>
                     <td>
                        <?php echo  $credit_row['final_rank']; ?>
                     </td>
                     <td>
                        <a data-placement="right" data-toggle="popover"  data-html="true" href="#" id="<?php echo ($credit_row['user_type'] == 1) ? $credit_row['creator_fundraise_id'] : $credit_row['winner_fundraise_id'] ;?>" class="fund_name" >
                        <?php echo ($credit_row['user_type'] == 1) ? $credit_row['creator_fundraise_name'] :
                        ($credit_row['donated_to_fundraiser_name'] != null)?$credit_row['donated_to_fundraiser_name']:$credit_row['winner_fundraise_name'] ; ?>
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
                                  $utype = "Creator Beneficiary";
                                   break;
                               case "3":
                                  $utype = "Game Winner";
                                   break;
                               case "4":
                                  $utype = "Winner Fundraise";
                                   break;
                               case "5":
                                   $utype = "Sub Winner";
                                   break;
                               case "6":
                                   $utype = "WinWinLabs";
                                   break;
                               case "7":
                                   $utype = "Player";
                                   break;
                               default:
                                   $utype = "N/A";
                           }
                           echo  $utype;
                           ?>
                     </td>
                      <!-- <td>
                        <?php //echo ($credit_row['is_deductible']==1)? 'Yes':'No'; ?>
                     </td> -->
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

<script>
    $(document).ready(function() {
        var cardMsg = getCookie("bStatus");
        if (cardMsg == '4') {
            showSweetUserConfirm("Success!", "Thank you for your Donation!", "success", "");

        }
        if (cardMsg == '6') {
         showSweetUserConfirm("Pending!", "Thank you for your Donation! It is pending and may take a few days to process through Stripe.", "info", "");
        }
    })
</script>
