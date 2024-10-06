<content class="content">

<div class="image-container set-full-height loginreg">


    <div class="container">
        <div class="row">

            <div class="col-sm-8 mx-auto">

                <!--      Wizard container        -->
                <div class="wizard-container">

                    <div class="card wizard-card" id="wizardProfile">
                        <?php $this->load->view('home/quotes_modal.php'); ?>

                            <form action="<?php echo asset_url('home/login_redirect');?>" id="logIn-form" method="post" >

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
                                                <div class="form-group">
                                                     <label>Email <small>(required)</small></label>
                                                    <div class="icon-addon addon-lg">
                                                    <input id="login-email" name="email" type="email" class="form-control" placeholder="Your Email.." value=''>
                                                    <label for="email" class="fa fa-envelope" rel="tooltip" title="email"></label>
                                                </div>
                                                </div>
                                            </div>
                                            
                                          

                                            <div class="col-sm-8 col-sm-offset-3 mx-auto">
                                                <div class="form-group">
                                                    <label>Password <small>(required)</small></label>
                                                    <div class="icon-addon addon-lg">
                                                    <input type="password" class="form-control" placeholder="Password.." id="login-password" name="password" autocomplete="off" value=''>
                                                    <label for="password" class="fa fa-key" rel="tooltip" title="password"></label>
                                                    <label for="password-toggle" class="fa fa-fw fa-eye-slash toggle-password" toggle="#login-password"></label>
                                                    </div>
                                                </div>
                                               <p id="error-msg" class='text-danger'></p>
                                            </div>


                                             <div class="col-sm-8 col-sm-offset-3 mx-auto">
                                                 <label><a href="<?php echo asset_url('reset'); ?>" class="createAccount" id="res_pass_link"><u>Forgot Password?</u></a></label>
                                             </div>



                                     

                                </div>
                                <div class="wizard-footer height-wizard">
                                        <input type='submit' class='btn btn-finish mx-auto d-block' name="loginbtn" value='Sign In' />
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