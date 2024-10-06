<!-- Content Start -->
<content class="content adminpage">
   <div class="container-fluid">
      <div class="row">
         <div class="col-12 p-4">
            <h1>Manage Users</h1>
            <div class="row justify-content-between p-2">
               <div class="col-lg-8">
                  <div class="row p-0">
                     <div class="col-lg-2"><input type="text" class="form-control" id="new_username" placeholder="Username"></div>
                     <div class="col-lg-2"><input type="email" class="form-control" id="new_email" placeholder="Email"></div>
                     <div class="col-lg-3"><input type="password" class="form-control" id="new_password" placeholder="Password"></div>
                     <div class="col-lg-3">
                     <label for="roleSelect" class="form-label">User Role</label>
                        <select class="form-select" id="roleSelect" name="role" aria-label="Select User Role">
                           <!-- Default empty option -->
                           <option value="" disabled selected>Select a role</option>
                           <?php foreach ($user_roles as $role): ?>
                                 <option value="<?php echo htmlspecialchars($role['permission']); ?>">
                                    <?php echo htmlspecialchars(ucfirst($role['role'])); ?>
                                 </option>
                           <?php endforeach; ?>
                        </select>
                     </div>
                     <div class="col-md-2"><button type="button" class="btn btn-red create_user">CREATE USER</button></div>
                  </div>
               </div>
               <div class="col-lg-4 text-right">
                  <button type="button" name="btn_delete" id="btn_delete" class="btn">Delete Selected</button>
               </div>
            </div>
            <div class="carddivider"></div>
            <input name="deferLoad" type="hidden" data-filtered="<?php echo $deferLoading["filtered"]; ?>" data-total="<?php echo $deferLoading["total"]; ?>"/>
            <table id="myAdvancedTable" class="table table-striped table-bordered thead-dark table-hover table-sm" cellspacing="0" max-width="100%" data-type="users">
               <thead>
                  <tr>
                     <th>First Name</th>
                     <th>Last Name</th>
                     <th>User Name</th>
                     <th>Email</th>
                     <th>Password</th>
                     <th>Select Coupon</th>
                     <th>Country</th>
                     <th>Default Fundraise</th>
                     <th>Decision-Maker</th>
                     <th>Select Permission</th>
                     <th>Account Status</th>
                     <th>Creator</th>
                     <th>Beta-Tester</th>
                     <th>Withdraw Credit Status</th>
                     <th>Games</th>
                     <th>Transactions</th>
                     <th>Delete</th>
                     <th>Date Created</th>
                  </tr>
               </thead>
               <tbody>
                  <?php
                     foreach ($users_list as $key => $allusers_row) :
                     ?>
                  <tr>
                     <td id='<?php echo $allusers_row->user_id?>' name="firstname" contenteditable="true">
                        <?php echo $allusers_row->firstname?>
                     </td>
                     <td id='<?php echo $allusers_row->user_id?>' name="lastname" contenteditable="true">
                        <?php echo $allusers_row->lastname?>
                     </td>
                     <td id='<?php echo $allusers_row->user_id?>' name="username" contenteditable="true">
                        <?php echo $allusers_row->username?>
                     </td>
                     <td id='<?php echo $allusers_row->user_id?>' name="email" contenteditable="true">
                        <?php echo $allusers_row->email?>
                     </td>
                     <td>
                        <input type="text"  placeholder="Password.." name="password" class="password_user" data-id="<?php echo $allusers_row->user_id?>" ><button class="btn_update_pass" type="button">Update</button>
                        <div id="password-errors"></div>
                     </td>
                     <td id='ord_<?php echo $allusers_row->user_id?>' class="crd">
                        <?php
                           echo '<select id="couponSelect">
                                 <option value="0">Please Select the coupon</option>';
                           foreach($coupon_list as $selection) {
                              echo '<option user-data="'.$allusers_row->username.'" value="'.$selection->id.'">'.$selection->description.
                                       ' for $'.$selection->amount.'</option>';
                              }
                           echo '</select>';
                           ?>
                     </td>
                     <td id='ord_<?php echo $allusers_row->user_id?>' class="crd">
                        <?php echo $allusers_row->country?>
                     </td>
                     <td id='ord_<?php echo $allusers_row->user_id?>' class="crd">
                        <?php echo $allusers_row->name?>
                     </td>
                     <td id='ord_<?php echo $allusers_row->user_id?>' class="crd">
                        <?php echo $allusers_row->decision_maker ?>
                     </td>
                     <td id='ord_<?php echo $allusers_row->user_id?>' class="crd">
                        <select class="form-select" id="roleSelect" name="role" aria-label="Select User Role">
                           <!-- Default empty option -->
                           <option value="<?php echo $allusers_row->permission;?>" disabled 
                              <?php echo empty($allusers_row->role) ? 'selected' : ''; ?> user-data="<?php echo $allusers_row->username; ?>">
                              Select a role
                           </option>
                           <?php foreach ($user_roles as $role): ?>
                              <option value="<?php echo htmlspecialchars($role['permission']); ?>"
                                       <?php echo ($allusers_row->usertype == $role['permission']) ? 'selected' : ''; ?>
                                       user-data="<?php echo htmlspecialchars($allusers_row->username); ?>">
                                 <?php echo htmlspecialchars(ucfirst($role['role'])); ?>
                              </option>
                           <?php endforeach; ?>
                        </select>
                     </td>
                     <td>
                        <input type="checkbox" data-toggle="toggle" data-on="Enabled" data-off="Disabled" class='user_status_switch' <?php if ($allusers_row->user_status=='Yes') {echo 'checked="checked"';} ?> value="<?php echo $allusers_row->user_id; ?>" />
                     </td>
                     <td>
                        <input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" class='creator_status_switch' <?php if ($allusers_row->creator_status=='Yes') {echo 'checked="checked"';} ?> value="<?php echo $allusers_row->user_id; ?>" />
                     </td>
                     <td>
                        <input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" class='tester_status_switch' <?php if ($allusers_row->btester_status=='Yes') {echo 'checked="checked"';} ?> value="<?php echo $allusers_row->user_id; ?>" />
                     </td>
                     <td>
                        <input type="checkbox" data-toggle="toggle" data-on="Enabled" data-off="Disabled" class='credit_withdraw_status_switch' <?php if ($allusers_row->credit_withdraw_status=='Yes') {echo 'checked="checked"';} ?> value="<?php echo $allusers_row->user_id; ?>" />
                     </td>
                     <td><a href="<?php echo asset_url('games/show/play/?user='.$allusers_row->username);?>" class="btn btn-primary">View All</a></td>
                     <td><a href="<?php echo asset_url('admin/userTransactions/'.$allusers_row->user_id); ?>" class="btn btn-primary" value="<?php echo $allusers_row->user_id; ?>">View All</a></td>
                     <td><input type="checkbox" name="<?php echo $allusers_row->user_id; ?>" class="delete_user" value="<?php echo $allusers_row->user_id;?>" /></td>
                     <td id='ord_<?php echo $allusers_row->user_id?>' class="crd">
                        <?php echo $allusers_row->created_at ?>
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
