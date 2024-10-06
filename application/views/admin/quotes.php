
<!-- Content Start -->
<content class="content adminpage">
   <div class="container-fluid">

      <div class="row">
         <div class="col-12 p-4">
            <h1>Manage Quotes</h1>
            <div class="row justify-content-between p-2">
               <div class="col-lg-8"></div>
               <div class="col-lg-4 text-right">
                  <button class="btn"  data-toggle="modal" data-target="#quoteModal">
                  <i class="fas fa-plus-circle"></i> New</button>
               </div>
            </div>
            <div class="carddivider"></div>
            <input name="deferLoad" type="hidden" data-filtered="<?php echo $deferLoading["filtered"]; ?>" data-total="<?php echo $deferLoading["total"]; ?>"/>
            <table id="myAdvancedTable" class="table table-striped table-bordered dt-responsive nowrap" width="100%" data-type="quotes">
                <thead>
                    <tr>
                        <th id = 'desc'>Category</th>
                        <th id = 'desc'>Description</th>
                        <th id = 'desc'>Source</th>
                        <th id = 'de'>Order</th>
                        <th id = 'chr'>Featured</th>
              			<th id = 'chr'>Delete</th>
              			<th id = 'chr'>Edit</th>

                    </tr>
                    </thead>
                        <tbody>
                        <?php
                        foreach ($quote_list as $key => $allquotes_row) :
                            // pr($allquotes_row);
                        ?>

                        <tr>

                            <td>
                                <?php echo $allquotes_row->category?>
                            </td>

                            <td>
                                <?php echo $allquotes_row->description?>
                            </td>
                            <td>
                                <?php echo $allquotes_row->source?>
                            </td>

                            <td>
                                <?php echo $allquotes_row->order_no?>
                            </td>

                            <td>
                            <input type="checkbox" name="quote_id" id="checkbox" class="feature_quote_chk" <?php if ($allquotes_row->featured=='Yes') {echo 'checked="checked"';}
                            ?> value="<?php echo $allquotes_row->id; ?>" />
                            </td>

    						<td><a class='delete_quote' href='javascript:void(0)' quote-id='<?php echo $allquotes_row->id; ?>'>Delete</a></td>

    						<td><a class='edit_quote' href='javascript:void(0)' quote-id='<?php echo $allquotes_row->id; ?>'>Edit</a></td>

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

<div class="modal fade" id="quoteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">New Quote</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="quote-form">
          <div class="form-group">
            <label for="category-name" class="col-form-label">Category:</label>
            <input type="text" class="form-control" id="category-name">
          </div>
          <div class="form-group">
            <label for="source-name" class="col-form-label">Source:</label>
            <input type="text" class="form-control" id="source-name">
          </div>
          <div class="form-group">
            <label for="quote-description-text" class="col-form-label">Description:</label>
            <textarea class="form-control" id="quote-description-text"></textarea>
          </div>
          <div class="form-group">
            <label for="order-num" class="col-form-label">Order:</label>
            <input type="number" class="form-control" id="order-num">
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id = 'addQuoteBtn'>Submit</button>
      </div>
    </div>
  </div>
</div>

<script>
$(document).ready( function() {
tinyMCEInitialize("quote-description-text", "", 1000 , "auto", "500px", "")
});
</script>