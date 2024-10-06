<content class="content">

<div class="image-container set-full-height loginreg">

    <div class="container">
        <div class="row">

            <div class="col-sm-8 mx-auto">

                <!--      Wizard container        -->
                <div class="wizard-container">

                    <div class="card wizard-card" id="wizardProfile">
                        <?php $this->load->view('home/quotes_modal.php'); ?>

                            <form id="verify-form" method="post" novalidate="novalidate">

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
                                            <div class="col-sm-8 col-sm-offset-3 mx-auto">
                                                <input type="hidden" name="token" value="<?php echo $this->input->get('token');  ?>" />
                                                <input type="hidden" name="id" value="<?php echo $this->input->get('key');  ?>" />
                                                <input type="hidden" name="user_id" value="<?php echo $user_id  ?>" />
                                                <div class="form-group">
                                                    <label>New Password <small>(required)</small></label>
                                                    <div class="icon-addon addon-lg">
                                                        <input type="password" class="form-control" placeholder="Password.." name="pswd" id="pswd" value="" autocomplete="off">
                                                        <label for="password" class="fa fa-key" rel="tooltip" title="password"></label>
                                                        <label for="password-toggle" class="fa fa-fw fa-eye-slash toggle-password" toggle="#pswd"></label>
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
                                            
                                            <div class="col-sm-8 col-sm-offset-3 mx-auto">
                                                <div class="form-group">
                                                    <label>Re-enter New Password <small>(required)</small></label>
                                                    <div class="icon-addon addon-lg">
                                                        <input type="password" class="form-control" placeholder="Password.." name="pswrdVerify" id="pswrdVerify" value="" autocomplete="off">
                                                        <label for="password" class="fa fa-key" rel="tooltip" title="password"></label>
                                                        <label for="password-toggle" class="fa fa-fw fa-eye-slash toggle-password" toggle="#pswrdVerify"></label>
                                                    </div>
                                                </div>
                                               <p id="error-msg" class='text-danger'></p>
                                            </div>
                                        </div>

                                        <div class="wizard-footer height-wizard">
                                            <input type='submit' class='btn btn-finish mx-auto d-block' name="resetbtn" value='Reset Password' id="reset-pass-btn"/>

                                            <div class="clearfix"></div>
                                            <div class="clearfix pt-3"></div>
                                            
                                            <center id="terms_privacy">
                                                <label><a data-toggle="modal" data-target="#tcModal"  id="terms_of_use"><u>Terms of Use</u></a></label>
                                                <label id="spacer">&</label>
                                                <label><a data-toggle="modal" data-target="#ppModal"  id="privacy_policy"><u>Privacy Policy</u></a></label>
                                            </center>
                                        </div>
                                    </div>
                                </div>

                            </form>
                    </div>
                </div>
                <!-- wizard container -->
            </div>
        </div>
        <!-- end row -->

        <div id="divLoading"> </div>

    </div>

</div>

</content>
