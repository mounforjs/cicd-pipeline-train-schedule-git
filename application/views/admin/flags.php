<!-- Content Start -->
<content class="content adminpage">
   <div class="container-fluid">

      <div class="row">
         <div class="col-12 p-4">
            <h1>Manage Flags</h1>
            <input id="deferLoad" type="hidden" data-filtered="<?php echo $deferLoading["filtered"]; ?>" data-total="<?php echo $deferLoading["total"]; ?>"/>
            <table id="myAdvancedTable" class="table table-striped table-bordered dt-responsive nowrap" width="100%" data-type="flags">
            <thead>
                <tr>
                    <th id = 'chr'>Date Submitted</th>
                    <th id = 'desc'>Flagger Name</th>
                    <th id = 'chr'>Description</th>
                </tr>
                </thead>
                    <tbody>
                    <?php 
                    foreach ($flag_list as $key => $allflags_row) :
                    ?>
                    <tr>
                        <td>
                            <?php echo $allflags_row->created_at; ?>
                        </td>
                        <td>
                            <?php echo $allflags_row->firstname . " " . $allflags_row->lastname; ?>
                        </td>
                        <td>
                            <?php echo $allflags_row->flag_description; ?>
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
