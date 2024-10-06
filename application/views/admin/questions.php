<!-- Content Start -->
<content class="content adminpage">
   <div class="container-fluid">
      <div class="row">
         <div class="col-12 p-4">
            <h1>Add/Edit Questions</h1>
            <div class="row justify-content-between p-2">

               <button type="button" class="btn btn-primary" id="addQuesBtn" data-toggle="modal" data-target="#addQuesModal" data-whatever="@getbootstrap">Add New Question</button>
               <div class="col-lg-4 text-right">
                  <button type="button"  class="btn delete_question">Delete Selected</button>
               </div>
            </div>
            <div class="carddivider"></div>
            <table id="myAdvancedTable" class="table table-striped table-bordered" style="width:100%">
               <thead>
                  <tr>
                     <th>Text</th>
                     <th>Type</th>
                     <th>Category</th>
                     <th>Tags</th>
                     <th>Delete</th>
                     <th>Approve</th>
                     <th>Created At</th>
                     <th>Edit</th>
                  </tr>
               </thead>
               <tbody>
                  <?php
                     foreach ($questions as $key => $question) : ?>
                  <tr>
                     <td id="<?php echo $question->ques_id; ?>" name="text" ><?php echo $question->ques_text; ?></td>
                     <td id="<?php echo $question->ques_id; ?>" name="type" ><?php echo $question->ques_type; ?></td>
                     <td id="<?php echo $question->ques_id; ?>" name="category" ><?php echo $question->ques_category; ?></td>
                     <td class="tags" id="<?php echo $question->ques_id; ?>" name="tags" ><input type="text" class="tags" data-role="tagsinput" value="<?php echo $question->ques_tags; ?>" /></td>
                     <td><input type="checkbox"  class="deleteQues" value="<?php echo $question->ques_id; ?>" /></td>
                     <td>
                        <input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" class='ques_status_switch' <?php if ($question->ques_status=='Yes') {echo 'checked="checked"';} ?> value="<?php echo $question->ques_id; ?>" />
                     </td>
                     <td id="<?php echo $question->ques_id; ?>" name="text"><?php echo $question->created_at; ?></td>
                     <td><button class="btn idEditBtn" id="<?php echo $question->ques_id; ?>">Edit</button></td>
                  </tr>
                  <?php  endforeach;  ?>
               </tbody>
            </table>
         </div>
      </div>
   </div>

   <div class="modal fade" id="addQuesModal"  role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">New Question</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="addForm">
          <div class="form-row">
            <div class="col-3">
              <select id="quesType" class="form-control">
                <option value="one">Enter One</option>
                <option value="many">Multiple Choice</option>
                <option value="Review">Review</option>
              </select>
            </div>
            <div class="col">
              <input type="text" id="quesCat" class="form-control" placeholder="Category">
            </div>
            <div class="col-7">
              <input type="text" id="quesTags" class="form-control" placeholder="Tags">
            </div>
          </div>
          <div class="form-group">
            <label for="question-text" class="col-form-label">Question Text</label>
            <textarea class="form-control" id="newQues"></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary create_question">Submit</button>
      </div>
    </div>
  </div>
</div>

   </div>
   </div>
   <div id="divLoading"> </div>
</content>
<!-- Content End -->
