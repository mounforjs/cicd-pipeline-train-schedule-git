<content class="content">
  <div class="container">
    <div class="row">
      <div class="col-sm-12 beneficiaryfilter">
      <?php if ($this->session->userdata('user_id')) { ?>
        <fieldset class="border p-2 text-center">
            <div class="form-check-inline text-center">
              <label class="form-check-label">
                <input type="radio" class="form-check-input fundraiseTypeRadio" name="type" value="created" <?php if ($this->uri->segment(3) == 'created' || $type == 'created') echo 'checked'; ?>>Created
              </label>
            </div>
            <div class="form-check-inline">
              <label class="form-check-label">
                <input type="radio" class="form-check-input fundraiseTypeRadio" name="type" value="supported" <?php if ($this->uri->segment(3) == 'supported' || $type == 'supported') echo 'checked'; ?>>Supported
              </label>
            </div>
            <div class="form-check-inline">
              <label class="form-check-label">
                <input type="radio" class="form-check-input fundraiseTypeRadio" name="type" value="all" <?php if ($this->uri->segment(3) == 'all' || $type == 'all') echo 'checked'; ?>>All
              </label>
            </div>
        </fieldset>
      <?php } ?>

        <div class="row justify-content-sm-center">
          	<div class="col-12">
				<?php $this->load->view('fundraisers/partials/fundraiserfilter'); ?>

				<div class="divider"></div><br>
				<div class="row" id="repeatFundraise">
					<?php $this->load->view('fundraisers/partials/fundraiserCard', array("fundraisers" => $fundraisers)); ?>
				</div>

				<div class="row justify-content-center mt-2">
					<a id="loadMoreButton" class="btn orange <?php echo (count($fundraisers) < 6) ? "d-none" : ""; ?>">SHOW MORE</a><br><br>
				</div>

				<div id="noRecordsFound" class="<?php echo (count($fundraisers) > 0) ? "d-none" : ""; ?>">
					<div class="fof">
						<h1>No fundraisers found!</h1>
					</div>
				</div>

				<div id="noMoreRecords" class="row justify-content-center d-none">
					<div class="fof">
						<h2>No more fundraisers found!</h2>
					</div>
				</div>
			</div>
    </div>
    <br>
  </div>
  <div class="modal fade" tabindex="-1" role="dialog" aria-hidden="true" id="editFundraiserModal">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
					<div class="modal-header">
						<h3 class="modal-title">Edit Beneficiary</h3>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">×</span>
						</button>
					</div>
					<div class="modal-body">
            <?php $this->load->view('fundraisers/partials/addFundraiser'); ?>
					</div>
        </div>
      </div>
    </div>

    <!-- Donation Form Start -->
    <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" id="donationModal">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <?php $this->load->view('forms/donation'); ?>
        </div>
      </div>
    </div>
    <!--  Donation Form End -->
  </div>
  </div>
  <span id="openFundRaiserForEdit" data-slug="<?php echo @$openFundRaiser; ?>"></span>
  <div id="divLoading"></div>
  
<?php if (isset($_SESSION['fundraiser_id_from_buycredit']) && $_SESSION['fundraiser_id_from_buycredit'] != "") { ?>
	<button style="display:none" type="button" class="btn red w-100 rounded-0 m-0 p-1 payDonateBtn lastFundraise" 
		fundraiser-name="<?php echo $_SESSION['fundraiser_name_from_buycredit'];?>" 
		fundraiser-id="<?php echo $_SESSION['fundraiser_id_from_buycredit'];?>">
		<i class="fas fa-donate" aria-hidden="true"></i> DONATE
	</button>
	
	<script>
		$(window).load(function() {
		$('.lastFundraise').trigger('click');
		});
	</script>
<?php 
	$this->session->unset_userdata('fundraiser_name_from_buycredit');
	$this->session->unset_userdata('fundraiser_id_from_buycredit');
} ?>

</content>