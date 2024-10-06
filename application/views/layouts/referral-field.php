<div class="row main mt-3 mb-3">
    <div class="col-sm-4 col-md-9 col-lg-6">
        <div class="input-group">
            <span class="input-group-prepend">
                <div class="input-group-text bg-transparent border-right-0"><i class="fa fa-gift"></i></div>
            </span>
            <input type="text" class="form-control" placeholder="Have a referral code?" id="rcode">
            <span class="input-group-append">
                <button id="refApplyBtn">Apply</button>
            </span>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var timeout = 3000;
        var refState = <?php echo $refStatus; ?>;
        console.log(refState)
        if(refState == '2') {
            setTimeout(function () {
                    $('#badRefError').alert('close');
            }, timeout);
        }
        $( "#refApplyBtn" ).on("click", function(e) {
            e.preventDefault();
            
            var refCode =  $('#rcode').val();
            if(refCode == '') {
                $('.main').after('<div class="alert alert-danger" id="emptyRefError" role="alert" collapse fade>Please enter a valid referral code.</div>');
                setTimeout(function () {
                    $('#emptyRefError').alert('close');
                }, timeout);
            }

            setCookie('referral', refCode);

            $.ajax({
                url: window.location.origin + "/home/getReferralStatus",
                type: "POST",
                data: ({id:refCode}),
                success: function(data){
                    if(data === 'true') {
                        if ($('#badRefError').length) { 
                            $('#badRefError').alert('close');
                        }
                        
                        $('.main').after('<div class="alert alert-success" role="alert" id="goodRefError">The referral code has been applied succesfully!</div>');
                        setTimeout(function () {
                            $('#goodRefError').alert('close');
                        }, timeout);
                        $(".main").remove();
                    }
                    if(data === 'false') {
                        $('.main').after('<div class="alert alert-danger" role="alert" id="badRefError">Invalid referral code. You can still sign up or try a valid code to redeem credits!</div>');
                        setTimeout(function () {
                            $('#badRefError').alert('close');
                        }, timeout);
                        eraseCookie('referral');
                    }
                }
            });
        })
    });
</script>