<content class="content">
    <div class="container">
        <div class="row">
            <?php $this->load->view('fundraisers/partials/defaultFundraiser', array("default_fundraiser" => $default_fundraiser)); ?>
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

        <div class="divider"></div>
        <div class="row justify-content-sm-center">
            <div class="col-12">
                <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" id="editFundraiserModal">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3 class="modal-title">Edit Beneficiary</h3>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <?php $this->load->view('fundraisers/partials/addFundraiser'); ?>
                        </div>
                    </div>
                </div>
                <!-- Add New Fundraiser End -->
                <div class="divider"></div>
                <br>
                <h2>Created <a href="<?php echo base_url('fundraisers/show/created'); ?>" class="seeallfund">SEE ALL <i class="fa fa-angle-double-right"></i></a>
                    <button class="btn orange pull-right new"><i class="fas fa-plus-circle"></i> <span>NEW</span></button>
                </h2>
                <br>
                <div class="row">
                    <?php $this->load->view('fundraisers/partials/fundraiserCard', array("fundraisers" => $my_created_fundraiser_list, "usertype" => getprofile()->usertype, "default_fundraiser" => getprofile()->default_fundraise, "type" => "created")); ?>
                </div>
                <div class="divider"></div>
                <br>
                <h2>Supported <a href="<?php echo base_url('fundraisers/show/supported'); ?>" class="seeallfund">SEE ALL <i class="fa fa-angle-double-right"></i></a></h2><br>
                <div class="row">
                    <?php $this->load->view('fundraisers/partials/fundraiserCard', array("fundraisers" => $my_supported_fundraiser_list, "type" => "supported")); ?>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div id="divLoading"> </div>
</content>
<?php $this->load->view('forms/paypal_donation'); ?>
<!-- Content End -->
