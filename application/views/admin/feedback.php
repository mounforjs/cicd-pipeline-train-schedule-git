
<!-- Content Start -->
<content class="content adminpage">
<script>
function convertUTCDateToLocalDate(dateTime) {

var dateTimeParts= dateTime.split(/[- :]/); // regular expression split that creates array with: year, month, day, hour, minutes, seconds values
dateTimeParts[1]--; // monthIndex begins with 0 for January and ends with 11 for December so we need to decrement by one

var date = new Date(...dateTimeParts); // our Date object

    var newDate = new Date(date.getTime()+date.getTimezoneOffset()*60*1000);

    var offset = date.getTimezoneOffset() / 60;
    var hours = date.getHours();

    newDate.setHours(hours - offset);

    return newDate;   
}
</script>
   <div class="container-fluid">

      <div class="row">
         <div class="col-12 p-4">
            <h1>Manage Feedback</h1>

			<input name="deferLoad" type="hidden" data-filtered="<?php echo $deferLoading["filtered"]; ?>" data-total="<?php echo $deferLoading["total"]; ?>"/>
            <table id="myAdvancedTable" class="table table-striped table-bordered dt-responsive nowrap" width="100%" data-type="feedback">
  			<thead>
  				<tr>
  					<th>Date Created</th>
  					<th>User Name</th>
  					<th>User Email</th>
  					<th>Page URL</th>
  					<th>Page Rating</th>
  					<th>WinWinLabs Rating</th>
  					<th>Type</th>
  					<th>Description</th>
  					<th>Images</th>
  				</tr>
  				</thead>
  					<tbody>
  					<?php
  					foreach ($feedback_list as $key => $allcharitydetails_row) :
  					?>
  					<tr>
  						<td id='divLocal<?php echo $key;?>'>
						  
						</td>
						<script>
							var dateTime = '<?php echo $allcharitydetails_row->date_created?>';
							var date = convertUTCDateToLocalDate(dateTime);
							$('#divLocal<?php echo $key;?>').text(date.toLocaleString());
						</script>
						  
  						<td id='de' class="crd">

  							<?php
  							if ($allcharitydetails_row->user_id)	{
  								echo $allcharitydetails_row->firstname.' '.$allcharitydetails_row->lastname;
  							}
  							else {
  								echo 'Guest User';
  							}

  							?>
  						</td>

  						<td id='de' class="crd">
  							<?php
  							if ($allcharitydetails_row->user_id)	{
  							echo $allcharitydetails_row->email;
  							}
  							else {
  								echo 'N/A';
  							}

  							?>

  						</td>
  						<td id='de' class="crd">
  							<?php echo $allcharitydetails_row->page_url?>
  						</td>
  						<td id='de' class="crd">
  							<?php echo $allcharitydetails_row->rating?>
  						</td>
  						<td id='de' class="crd">
  							<?php echo $allcharitydetails_row->winwinrating?>
  						</td>
  						<td id='de' class="crd">
  							<?php echo $allcharitydetails_row->category_name?>
  						</td>
  						<td id='de' class="crd">
  							<?php echo $allcharitydetails_row->feedback_description?>
  						</td>
  						<td id='de' class="crd">
  							<input type="button" class="btn btn-primary feedback-images" data-id ="<?php echo $allcharitydetails_row->id?>" value="View" />
  						</td>
  					</tr>
  				<?php  endforeach; ?>

  			</tbody>
  		</table>

         </div>
      </div>
   </div>

   <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Feedback Images</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          No Image
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
</content>
<!-- Content End -->


