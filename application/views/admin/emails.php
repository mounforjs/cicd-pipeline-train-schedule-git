<style>
   .nav-item .nav-link{
   color: #581818;
   }
</style>
<!-- Content Start -->
<content class="content adminpage">
   <div class="container-fluid">
      <div class="row">
         <div class="col-12 p-4">
            <h1>Manage Emails</h1>
            <ul class="nav nav-tabs" id="myTab" style="color: black">
               <li class="nav-item">
                  <a href="#welcome" class="nav-link active" data-toggle="tab">Welcome Email</a>
               </li>
               <li class="nav-item">
                  <a href="#live" class="nav-link" data-toggle="tab">Game Live Email</a>
               </li>
               <li class="nav-item">
                  <a href="#winner" class="nav-link" data-toggle="tab">Game Winner Email</a>
               </li>
               <li class="nav-item">
                  <a href="#end" class="nav-link" data-toggle="tab">Game End Email</a>
               </li>
               <li class="nav-item">
                  <a href="#review" class="nav-link" data-toggle="tab">Game Review Email</a>
               </li>
            </ul>
            <br>
            <div class="tab-content">
               <div class="tab-pane active" id="welcome">
                  <h4 class="mt-2">Email Subject<input type="button" class="btn btn-update ml-4" id ="welcome_email_update"  value="Update" ></input></h4>
                  <input  type="text"  class="form-control " id="subject_editor_welcome" value="<?php  echo $email_description[1]->email_subject ; ?>">
                  <h4 class="mt-2">Email Body</h4>
                  <textarea  class="form-control" id="status_editor_welcome">


               <?php  echo $email_description[1]->description ; ?>


            </textarea>
               </div>
               <div class="tab-pane fade" id="live">
                  <h4 class="mt-2">Email Subject<input type="button" class="btn btn-update ml-4" id ="live_email_update"  value="Update" ></input></h4>
                  <input type="text"  class="form-control" id="subject_editor_live" value="<?php  echo $email_description[2]->email_subject ; ?>">
                  <h4 class="mt-2">Email Body</h4>
                  <textarea  class="form-control" id="status_editor_live">


               <?php  echo ($email_description[2]->description); ?>



            </textarea>
               </div>
               <div class="tab-pane fade" id="winner">
                  <h4 class="mt-2">Email Subject<input type="button" class="btn btn-update ml-4" id ="winner_email_update"  value="Update" ></input></h4>
                  <input type="text"  class="form-control" id="subject_editor_winner" value="  <?php  echo $email_description[3]->email_subject ; ?>">
                  <h4 class="mt-2">Email Body</h4>
                  <textarea  class="form-control" id="status_editor_winner">


                <?php echo ($email_description[3]->description); ?>



            </textarea>
               </div>
               <div class="tab-pane fade" id="end">
                  <h4 class="mt-2">Email Subject<input type="button" class="btn btn-update ml-4" id ="end_email_update"  value="Update" ></input></h4>
                  <input type="text" class="form-control" id="subject_editor_end" value="<?php  echo $email_description[4]->email_subject ; ?>">
                  <h4 class="mt-2">Email Body</h4>
                  <textarea  class="form-control" id="status_editor_end">


                <?php echo ($email_description[4]->description); ?>



            </textarea>
               </div>
               <div class="tab-pane fade" id="review">
                  <h4 class="mt-2">Email Subject<input type="button" class="btn btn-update ml-4" id ="review_email_update"  value="Update" ></input></h4>
                  <input type="text" class="form-control" id="subject_editor_review" value="<?php  echo $email_description[5]->email_subject ; ?>">
                  <h4 class="mt-2">Email Body</h4>
                  <textarea  class="form-control" id="status_editor_review">


                <?php echo ($email_description[5]->description); ?>



            </textarea>
               </div>
            </div>
         </div>
      </div>
   </div>
   </div>
   </div>
</content>
<!-- Content End -->
