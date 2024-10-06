<?php if (isset($claim) && $claim) { ?>
    <div id="changeAddressData">
<?php } ?>

    <div id="addAddress"  class="col mb-4 addAddress">
        <h3><i class="fa fa-plus" aria-hidden="true"></i> Address</h3>
    </div>

    <div id="selectableAddresses" <?php if (isset($claim) && $claim) { ?> class="row"<?php } ?>>
		<div class="row">

        <?php foreach ($addresses as $key=>$address) { ?>
            <?php $this->load->view('address/address-partial', array("claim" => $claim, "key" => $key, "address" => $address)); ?>
        <?php } ?>
		</div>
	</div>

<?php if (isset($claim) && $claim) { ?>
    </div>
<?php } ?>

<?php if (isset($claim) && $claim) { ?>
    <div id="newAddress" class="d-none col">
        <?php $this->load->view('address/form'); ?>
    </div>
<?php } else { ?>
    <div class="modal" tabindex="-1" role="dialog" id="addressModal">
        <div class="modal-dialog modal-lg vc-modal" role="document">
            <div class="load d-none"><div class="imageLoader"></div></div>
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Add Address</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>

                <div class="modal-body">
                    <?php $this->load->view('address/form', array("fname" => $fname, "lname" => $lname)); ?>
                </div>

                <div class="modal-footer">
                    <button id="newAddress" type="button" class="btn btn-primary green"><i class="fa fa-plus" aria-hidden="true"></i> Address</button>
                    <button id="updateAddress" type="button" class="btn btn-primary orange d-none" data-id="">Update</button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<?php if (!isset($claim) || !$claim) { ?>
    <script src="<?php echo asset_url('assets/js/address.js'); ?>"></script>
<?php } else { ?>
    <script src="<?php echo asset_url('assets/js/claimPrize.js'); ?>"></script>
<?php } ?>
