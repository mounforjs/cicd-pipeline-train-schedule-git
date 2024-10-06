
<!-- Content Start -->
<content class="content adminpage">
   <div class="container-fluid">

      <div class="row">
         <div class="col-12 p-4">
            <h1>Manage Subscription List</h1>
          
            <div class="carddivider"></div>

            <table id="myAdvancedTable" class="table table-striped table-bordered dt-responsive nowrap" width="100%" >
            <thead>
                <tr>

                    <th>ID</th>
                    <th>Email</th>
                    <th>Date Created</th>


                </tr>
                </thead>
                    <tbody>
                    <?php
                    foreach ($subscribe_list as $key => $user_row) :
                    ?>

                    <tr class=''>

                           <td id='cat_<?php echo $allquotes_row->id?>' class="crd">
                            <?php echo $user_row->id; ?>
                        </td>


                        <td id='des_<?php echo $allquotes_row->id?>' class="crd">
                            <?php echo $user_row->email; ?>
                        </td>

                         <td id='des_<?php echo $allquotes_row->id?>' class="crd">
                            <?php echo $user_row->created_at; ?>
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
</content>
<!-- Content End -->
