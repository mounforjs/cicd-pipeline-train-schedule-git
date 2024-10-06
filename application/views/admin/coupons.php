<!-- Content Start -->
<content class="content adminpage">
   <div class="container-fluid">
      <div class="row">
         <div class="col-12 p-4">
            <h1>Manage Coupons</h1>
            <div class="row justify-content-between p-2">
               <div class="col-lg-8">
                  <div class="row p-0">
                     <div class="col-lg-3"><input type="text" class="form-control" id="new_coupon_description" placeholder="Description of coupon"></div>
                     <div class="col-lg-3"><input type="number" class="form-control" placeholder="Amount" id="new_coupon_amount" required></div>
                  </div>
               </div>
               <div class="col-lg-4 text-right">
                  <button type="button" class="btn delete_coupon_btn">Delete Selected</button>
                  <button type="button" class="btn create_coupon">Add New Coupon</button>
               </div>
            </div>
            <div class="carddivider"></div>
            <input name="deferLoad" type="hidden" data-filtered="<?php echo $deferLoading["filtered"]; ?>" data-total="<?php echo $deferLoading["total"]; ?>"/>
            <table id="myAdvancedTable" class="table table-striped table-bordered" style="width:100%" data-type="coupons">
               <thead>
                  <tr>
                     <th>Description</th>
                     <th>Amount in $ (USD)</th>
                     <th>Active</th>
                     <th>Delete</th>

                  </tr>
               </thead>

               <tbody>
                  <?php foreach ($coupons as $key => $coupon) : ?>
                        <tr>
                              <td id="<?php echo $coupon->id; ?>" name="description" contenteditable="true"><?php echo $coupon->description; ?></td>
                              <td id="<?php echo $coupon->id; ?>" name="amount" contenteditable="true"><?php echo $coupon->amount; ?></td>
                              <td>
                              <input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" class='coupon_active_switch'<?php if ($coupon->active=='Yes') {echo 'checked="checked"';} ?>  value="<?php echo $coupon->id; ?>" />
                              </td>
                              <td><input type="checkbox"  class="delete_coupon" value="<?php echo $coupon->id; ?>" /></td>
                        </tr>
                  <?php  endforeach;  ?>
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
