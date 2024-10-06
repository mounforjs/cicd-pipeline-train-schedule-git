<style>
div.gallery {
  margin: 5px;
  border: 1px solid #ccc;
  float: left;
  width: 180px;
}
div.gallery:hover {
  border: 1px solid #777;
}
div.gallery img {
  width: 100%;
  height: auto;
}
</style>

<div class="modal fade feedbackClass" tabindex="-1" id="feedbackModal">
   <div class="modal-dialog">
   <div class="load d-none"><div class="imageLoader"></div></div>
      <div class="modal-content">
         <div class="modal-header">
            <h2 class="modal-title">Feedback</h2>
         </div>

         <form enctype="multipart/form-data" id="feedback-form" method="POST">
            <div class="modal-body">
               <div class="form-group">
                  <label class="d-inline-block" for="from_year">Which page is the feedback for:</label>
                  <?php $all_pages = get_page_links(); ?>
                  <select name="from_page" class="form-control form-control-sm d-inline-block" style="width: auto;" id="from_page">
                     <option>Select Page</option>
                     <?php foreach($all_pages as $page) { ?>
                        <option value="<?php echo $page['url']; ?>"><?php echo $page['name']; ?></option>
                     <?php } ?>
                  </select>
               </div>

               <p>Rate your satisfaction with the Current Page on a scale of 0 - 10, 10 being highest?</p>
               <div class="table-responsive">
                  <table class="text-center select score-tbl select-off" cellspacing="1" style="margin-bottom: 15px;">
                     <tbody>
                        <tr>
                           <?php for ($i = 0; $i < 11; $i++) { ?>
                              <td name="rating_num" class="scr-td" value="<?php echo $i; ?>"><?php echo $i; ?></td>
                           <?php } ?>
                        </tr>
                     </tbody>
                  </table>
               </div>

               <p style="margin-top:14px;">Now, if you have specific feedback, please select a category below.</p>
               <div class="table-responsive">
                  <table class="text-center select-off modal-tbl" cellspacing="1">
                     <tbody>
                        <tr class="nav nav-tabs">
                           <?php $main_categories = get_feedback_category();
                           foreach($main_categories as $feedback_type) { ?>
                              <td href="#tab<?php echo $feedback_type['id'];?>" name="<?php echo str_replace(" ", "_", $feedback_type['category_name']);?>" class="nav-td feedback-category" data-id="<?php echo $feedback_type['id'];?>" data-toggle="tab"><?php echo ucwords($feedback_type['category_name']) ;?></td>
                           <?php } ?>
                        </tr>
                     </tbody>
                  </table>
               </div>

               <div id="feedback-tabs" class="tab-content tab-bg clearfix d-none">
                  <?php $main_categories = get_feedback_category();
                     foreach($main_categories as $feedback_type) { ?>
                  <div class="tab-pane" id="tab<?php echo $feedback_type['id'];?>">
                     <?php $sub_categories = get_feedback_category($feedback_type['id']);
                        if(!empty($sub_categories)){ ?>
                     <div id="sub_div<?php echo $feedback_type['id'];?>" class="sub-div">
                        <ul class="feedback-sub-div" id="feedback-sub-div-<?php echo $feedback_type['id'];?>">
                           <?php  foreach($sub_categories as $feedback_sub_type) { ?>
                              <li><a class="feedback-subcategory" name="<?php echo $feedback_sub_type['category_name'];?>" data-parent="<?php echo $feedback_type['id']; ?>" data-id="<?php echo $feedback_sub_type['id']; ?>"><?php echo $feedback_sub_type['category_name'];?></a></li>
                           <?php } ?>
                        </ul>
                     </div>
                     <?php } ?>
                  </div>
                  <?php } ?>
                  <div class="feedback-desc" style="display: none">
                     <div id="feedback-desc-header" class="row" style="display:none;">
                        <div class="col">
                           <h2 class="heading pull-left my-1"></h2>
                           <button type="button" class="fbtn back-btn" data-back="">« Back</button>
                        </div>
                     </div>
                     <div class="row">
                        <div class="col">
                           <textarea class="feedback-text" name="feedback_desc" rows="10" cols="30" id="feedback_desc"  maxlength="500"></textarea>
                           <div class="col-sm-12 text-center">
                              <div class="gIconPreview mt-2">
                                 <label class="fbtn feedBackImageBlock">Attach images:
                                 <input type="file" id="feedback_images" name="feedback_images[]" multiple form-id="feedback-form" preview-at="ul.feedback_icons" >
                                 </label>
                              </div>
                              <div class="gallery mt-2">
                                 <ul class="feedback_icons">
                                    <!-- feedback images to come here -->
                                 </ul>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>

               <p class="mt-2">Rate your satisfaction with WinWinLabs on a scale of 0 - 10, 10 being highest?</p>
               <div class="table-responsive">
                  <table class="text-center select score-tbl select-off" cellspacing="1" style="margin-bottom: 15px;">
                     <tbody>
                        <tr>
                           <?php for ($i = 0; $i < 11; $i++) { ?>
                              <td name="rating_num_winwin" class="scr-winwin-td" value="<?php echo $i; ?>"><?php echo $i; ?></td>
                           <?php } ?>
                        </tr>
                     </tbody>
                  </table>
               </div>
            </div>

            <div class="modal-footer">
               <input type="hidden" id="rating" name="rating" val="10">
               <input type="hidden" id="maincat" name="maincat">
               <input type="hidden" id="subcat" name="subcat">
               <input type="hidden" id="winwinrating" name="winwinrating" val="10">
               <input type="hidden" id="current_url" name="current_url" value="<?php echo current_url(); ?>">

               <input type="button" class="fbtn cancel-btn btn red mr-auto" data-dismiss="modal" value='Cancel'/>
               <input type='button' id="feedback-submit" class='fbtn btn orange disabled' name='Submit' value='Send Feedback' disabled/>
            </div>
         </form>

      </div>
   </div>
</div>
