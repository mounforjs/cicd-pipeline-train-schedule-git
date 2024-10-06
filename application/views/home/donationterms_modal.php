<p class="text-center">By submitting a donation you agree to our <a data-toggle="modal" data-target="#donationterms"  id="donation_terms"><u>Terms of Donations</u></a>.</p>
<div class="modal" id="donationterms" tabindex="-1" role="dialog" aria-labelledby="donationterms" >
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="">Donation Terms</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-left">
                <?php $this->load->view('home/donation_terms.php'); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
