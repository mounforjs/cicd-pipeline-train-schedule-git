<div class="container">
   <?php $this->load->view("games/game_header"); ?>

   <center>
   <div class="row">
      <div class="col-12">
         <div id="game-wrapper">
            <?php $this->load->view('layouts/counter'); ?>

            <?php $this->load->view("games/play/{$type}/index"); ?>
         </div>
      </div>
   </div>

   
   <?php $this->load->view("games/game_footer"); ?>
   </center>

   <input type="hidden" name="game_session_id" value="<?= $game_session_id ?>" />
</div>

<!-- to be revisited once we have figured out a better solution -->
<!-- <script>
   // Create a global variable to store the timestamp when the page is loaded
   var pageLoadTimestamp = new Date().getTime();

   // Add event listener for beforeunload
   window.addEventListener('beforeunload', function(event) {
      // Create a new timestamp when the user tries to exit the page
      var exitTimestamp = new Date().getTime();
      
      // Calculate the difference between page load and exit timestamps
      var timeDifference = exitTimestamp - pageLoadTimestamp;

      // If the difference is greater than a certain threshold (e.g., 1 seconds)
      // show an alert to the user
      if (timeDifference > 1000) { // Adjust threshold as needed
         event.preventDefault();
         event.returnValue = ''; // For legacy browsers
         alert('Timestamps do not match! Changes may not be saved.');
      }
   });
</script> -->