<content class="content">

<div class="image-container set-full-height loginreg">

    <!--   Big container   -->
    <div class="container">
        <div class="row">
           <div class="col-sm-8 mx-auto">

                <div class="wizard-container">

                    <div class="card wizard-card" id="wizardProfile">
                        <?php $this->load->view('home/quotes_modal.php'); ?>
                            <form id="signUp-form" method="POST" enctype="multipart/form-data" onsubmit="return false;">
                                <div class="wizard-navigation">

                                    <ul>

                                        <li>
                                            <a href="#about" data-toggle="tab">
                                               
                                            </a>
                                        </li>

                                    </ul>
                                </div>
                                <div class="tab-content">
                                    <div class="tab-pane" id="about">
                                        <div class="row">

                                            <div class="col-sm-6 col-sm-offset-1">
                                                <div class="form-group">
                                                    <label>First Name <small>(* indicates required fields)</small></label>
                                                    <input name="firstname" type="text" class="form-control" placeholder="First Name">
                                                </div>
                                            </div>
                                            <div class="col-sm-6 ">
                                                <div class="form-group">
                                                    <label>Last Name <small>*</small></label>
                                                    <input name="lastname" type="text" class="form-control" placeholder="Last Name">
                                                </div>

                                            </div>

                                            <div class="col-sm-6 col-sm-offset-1">
                                                <div class="form-group">
                                                    <label>Country <small>*</small></label>
                                                    <select name="country" class="form-control form-control-lg" id="user_country">
                                                        <?php foreach (countries() as $key => $country):?>
                                                            <option value="<?php echo $key?>">
                                                                <?php echo $country?>
                                                            </option>
                                                            <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>Username <small>*</small></label>
                                                     <div class="icon-addon addon-lg">
                                                    <input name="username" type="text" class="form-control" placeholder="Must be Unique">
                                                    <label for="email" class="fa fa-user" rel="tooltip" title="email"></label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-6 col-sm-offset-1">
                                                <div class="form-group">
                                                    <label>Email <small>*</small></label>
                                                    <div class="icon-addon addon-lg">
                                                    <input name="unEmail" id="unEmail" type="email" class="form-control" placeholder="you@youremail.com">
                                                    <label for="email" class="fa fa-envelope" rel="tooltip" title="email"></label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-6">
                                                 <div class="form-group">
                                                     <label>Password <small>*</small></label>
                                                    <div class="icon-addon addon-lg">
                                                    <input type="password" class="form-control" id="password" name="password"  placeholder="Password"  autocomplete="off" value="">
                                                    <label for="password" class="fa fa-key" rel="tooltip" title="password"></label>
                                                    <label for="password-toggle" class="fa fa-fw fa-eye-slash toggle-password" toggle="#password"></label>
                                                    </div>
                                                </div>
                                                <div id="popover-password" class="d-none">
                                                    <p>Password Strength: <span id="result"> </span></p>
                                                    <div class="progress">
                                                        <div id="password-strength" class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:0%">
                                                        </div>
                                                    </div>
                                                    <ul class="list-unstyled">
                                                        <li class=""><span class="low-upper-case"><i class="fa fa-times" aria-hidden="true"></i></span>&nbsp; 1 lowercase &amp; 1 uppercase</li>
                                                        <li class=""><span class="one-number"><i class="fa fa-times" aria-hidden="true"></i></span> &nbsp;1 number (0-9)</li>
                                                        <li class=""><span class="one-special-char"><i class="fa fa-times" aria-hidden="true"></i></span> &nbsp;1 Special Character (!@#$%^&*).</li>
                                                        <li class=""><span class="eight-character"><i class="fa fa-times" aria-hidden="true"></i></span>&nbsp; Atleast 8 Character</li>
                                                    </ul>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <?php if ($refStatus == '1') { ?>
                                    <div class="alert alert-success" role="alert" id='goodRefError' collapse>
                                        Your referral credits will be applied immediately upon registration.
                                    </div>
                                    <?php } ?>
                                    <?php if ($refStatus == '2') { ?>
                                    <div class="alert alert-danger" role="alert" id='badRefError' collapse>
                                        Invalid referral code. You can still sign up or try a valid code to redeem credits!
                                    </div>
                                    <?php $this->load->view('layouts/referral-field', $refStatus); ?>
                                    <?php } ?>
                                    <?php if ($refStatus == '3') {
                                        $this->load->view('layouts/referral-field', $refStatus);
                                    } ?>
                                </div>
                                <div class="wizard-footer">

                                        <input type='submit' class='btn btn-finish mx-auto d-block' name='finish' value='Sign Up' id="finish" />

                                    <div class="clearfix pt-3"></div>
                                    <center id="terms_privacy">
                                        <label>By clicking Sign Up you agree to our</label>
                                        <label><a data-toggle="modal" data-target="#tcModal"  id="terms_of_use"><u>Terms of Use</u></a></label>
                                        <label id="spacer">&</label>
                                        <label><a data-toggle="modal" data-target="#ppModal"  id="privacy_policy"><u>Privacy Policy</u></a></label>
                                    </center>

                                </div>
                             </form>

                    </div>

                </div>

             
            </div>
        </div>
        <!-- wizard container -->

        <div id="divLoading"> </div>
    </div>


</div>
<!-- end row -->
<!--  big container -->
</content>