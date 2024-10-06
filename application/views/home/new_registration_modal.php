<div class="modal fade" id="thankyouregistr">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title text-left">YOUR REGISTRATION WAS SUCCESSFUL<br>Thank you and have fun!</h2>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col pt-3 text-left">
                        <h2>I WANT TO...</h2>
                        <div class="row justify-content-center">
                            <div class="col-md-3 col-6">
                                <a href="<?php echo asset_url('buycredits/'.getDefaultPaymentMethodType()); ?>" target="_self"><img src="https://dg7ltaqbp10ai.cloudfront.net/regwel_buycredits.png" alt="Buy Credits" class="w-100"></a>
                            </div>
                            <div class="col-md-3 col-6">
                                <a href="<?php echo asset_url('games/show/play'); ?>" target="_self"><img src="https://dg7ltaqbp10ai.cloudfront.net/regwel_playgames.png" alt="Play Games" class="w-100"></a>
                            </div>
                            <div class="col-md-3 col-6">
                                <a href="<?php echo asset_url('fundraisers/show'); ?>" target="_self"><img src="https://dg7ltaqbp10ai.cloudfront.net/regwel_dofundraising.png" alt="Do Frundraising" class="w-100"></a>
                            </div>
                            <div class="col-md-3 col-6">
                                <a href="<?php echo asset_url('games/show/play'); ?>" target="_self"><img src="https://dg7ltaqbp10ai.cloudfront.net/regwel_winprizes.png" alt="Win Prizes" class="w-100"></a>
                            </div>
                            <!-- Saving this option for a tour
                                <div class="col-sm-4 col-6">
                                    <a href="<?php echo asset_url('#'); ?>"><img src="https://dg7ltaqbp10ai.cloudfront.net/regwel_exploreoptions.png" alt="Explore all Options" class="w-100"></a>
                                </div> -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="button" class="btn small orange mr-auto" data-dismiss="modal" value="Close">
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(window).on('load', function() {
        $('#thankyouregistr').modal('show');

        var refVC = getCookie("rval");
        var regC = getCookie("s");
        if (regC) {
            var msg = (refVC) ? "You have been awarded " + refVC : "You successfully registered, however your referral code is not valid.";
            var title = (refVC) ? "Thank you!": "Whoops!";
            var icon = (refVC) ? "success": "error";

            showSweetAlert(msg, title, icon).then(() => {
                // delete cookies after alert message is shown on referral signup flow
                eraseCookie('referral');
                eraseCookie('rval');
                eraseCookie('s');
            });
        }
    });
</script>