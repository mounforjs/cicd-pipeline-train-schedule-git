<script>
   $(document).ready(function() {
      $('.player_name').hover(function() {
         var e = $(this);
         e.off('hover');
         var popup_data = e.data('popup');

         e.popover({
               html: true,
               animation: false,
               placement: "right",
               title: popoverContent,
            })
            .popover('show')
            .on("mouseenter", function() {
               var _this = this;
               $(this).popover("show");
               $(".popover").on("mouseleave", function() {
                  $(_this).popover('hide');
               });
            }).on("mouseleave", function() {
               var _this = this;
               setTimeout(function() {
                  if (!$(".popover:hover").length) {
                     $(_this).popover("hide");
                  }
               }, 300);

            });
      });
   });

   function popoverContent() {

      var element = $(this);
      var id = element.attr("id");

      $.ajax({
         async: false,
         type: "POST",
         data: {
            userId: id
         },
         url: "<?php echo asset_url('admin/getUserInfo'); ?>",

         dataType: "JSON",
         success: function(e) {
            // console.log(e.done);
            content = $("#popover-content").html();
            content = content.replace(/p_image/g, e.done.profile_img_path);
            content = content.replace(/p_name/g, e.done.firstname + " " + e.done.lastname);
            content = content.replace(/p_username/g, e.done.username);
            content = content.replace(/p_country/g, 'Country: ' + e.done.country);
            content = content.replace(/p_interests/g, 'Interests: ' + e.done.profile_interests);
            content = content.replace(/p_lifetime_goals/g, 'Lifetime goals: ' + e.done.lifetime_goals);
            content = content.replace(/p_graduation/g, 'Graduating year: ' + e.done.graduation);
            content = content.replace(/p_strengths/g, 'Strengths: ' + e.done.strengths);
            content = content.replace(/p_learn_areas/g, 'Learning areas: ' + e.done.learn_areas);
         }
      });

      return content;
   };
</script>

<!-- Content Start -->
<content class="content adminpage">
   <div class="container-fluid">
      <div class="row">
         <div class="col-12 p-4">
            <h1>Manage Transactions</h1>
            <input type="hidden" name="mycredits_value" id="mycredits_value" value="<?php echo isset($sum) ? $sum : ""; ?>">
            <?php if ($this->session->flashdata('message')) {
               $message = $this->session->flashdata('message');
               echo  '<div class="alert alert-success" id="success-alert" style="margin-top: 20px" > <strong>Success! </strong>' . $message['message'] . '</div>';
            } ?>

            <input name="deferLoad" type="hidden" data-filtered="<?php echo $deferLoading["filtered"]; ?>" data-total="<?php echo $deferLoading["total"]; ?>" />
            <table id="myAdvancedTable" class="table table-striped table-bordered" style="width:100%" data-type="allTransactions">
               <thead>
                  <tr>
                     <th>Date</th>
                     <th>Username</th>
                     <th>Type</th>
                     <th>Amount</th>
                     <th>Total Charges</th>
                     <th>Total Balance</th>
                     <th>Description</th>
                     <th>Donation Status</th>
                     <th>Mode</th>
                     <th>Game Name</th>
                     <th>Rank</th>
                     <th>Fundraiser</th>
                     <th>User Type</th>
                     <th>Is Deductible</th>
                     <th>Reference ID</th>
                  </tr>
               </thead>
               <tbody>
                  <?php foreach ($transactions as $key => $credit_row) : ?>
                     <tr>
                        <td>
                           <?php echo date("m-d-y H:i:s", strtotime($credit_row['Date'])) ?>
                        </td>
                        <td id="<?php echo $credit_row["user_id"]; ?>" class="player_name">
                           <a data-placement="right" data-toggle="popover" data-html="true" href="#" id="login"><?php echo $credit_row["firstname"] . ' </span><span class="text-lowercase">(' . $credit_row["email"] . ')'; ?></a>
                        </td>
                        <td>
                           <?php if ($credit_row['Status'] != 1) {
                              echo '<div class="status badge badge-danger badge-pill badge-sm">Debit</div>';
                           } else {
                              echo '<div class="status badge badge-success badge-pill badge-sm">Credit</div>';
                           }
                           ?>
                        </td>
                        <td>
                           <i class="fa fa-dollar"></i>
                           <?php if ($credit_row['Status'] != 1) {
                              $usd_debit = $credit_row['Credits'];
                              echo round_to_2dc($usd_debit);
                           } else {
                              $usd_credit = $credit_row['Credits'];
                              echo round_to_2dc($usd_credit);
                           }
                           ?>
                        </td>
                        <td>
                           <i class="fa fa-dollar"></i>
                           <?php
                              $usd_credit = $credit_row['total_charge'];
                              echo round_to_2dc($usd_credit);
                           ?>
                        </td>
                        <td>
                           <i class="fa fa-dollar"></i>
                           <?php
                           $usd_total = $credit_row['total_credits'];
                           echo round_to_2dc($usd_total);
                           ?>
                        </td>
                        <td>
                           <?php echo $credit_row['Notes'] ?>
                        </td>
                        <td>
                           <div class="status badge badge-success badge-pill badge-sm"><?php echo $credit_row['payment_status']; ?></div>
                        </td>
                        <td>
                           <?php echo ($credit_row['payment_mode'] == 1) ? 'Bank ACH' : (($credit_row['payment_mode'] == 2) ? 'Paypal' : (($credit_row['payment_mode'] == 3) ? 'Credit Card' : (($credit_row['payment_mode'] == 5) ? 'Referral' : 'WinWinLabs'))); ?>
                        </td>
                        <td>
                           <?php echo $credit_row['game_name'] ?>
                        </td>
                        <td>
                           <?php echo  $credit_row['final_rank']; ?>
                        </td>
                        <td>
                           <a data-placement="right" data-toggle="popover" data-html="true" href="#" id="<?php echo ($credit_row['user_type'] == 1) ? $credit_row['creator_fundraise_id'] : $credit_row['winner_fundraise_id']; ?>" class="fund_name">
                              <?php echo ($credit_row['user_type'] == 1) ? $credit_row['creator_fundraise_name'] : (($credit_row['donated_to_fundraiser_name'] != null) ? $credit_row['donated_to_fundraiser_name'] : $credit_row['winner_fundraise_name']); ?>
                           </a>
                        </td>
                        <td>
                           <?php
                           $user_type = $credit_row['user_type'];

                           switch ($user_type) {
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
                           <?php echo ($credit_row['is_deductible'] == 1) ? 'Yes' : 'No'; ?>
                        </td>
                        <td>
                           <?php echo $credit_row['ref_num']; ?>
                        </td>
                     </tr>
                  <?php endforeach; ?>
               </tbody>
            </table>
         </div>

         <div id="popover-content" class="d-none">
            <div class="row">
               <div class="col-xs-4">
                  <img width="88px" height="88px" src=p_image>
               </div>
               <div class="col-xs-8 pl-2">
                  <h4>
                     p_username <br />
                     <small>
                        p_name<br />
                        p_strengths<br />
                        p_interests<br />
                        p_graduation<br />
                        p_lifetime_goals<br />
                        p_learn_areas<br />
                     </small>
                  </h4>
               </div>
            </div>
         </div>
      </div>
   </div>
</content>
<!-- Content End -->