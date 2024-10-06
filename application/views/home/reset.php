<content class="content">
    <div class="image-container set-full-height loginreg">
        <div class="container">
            <div class="row">
                <div class="col-sm-8 mx-auto">
                    <!--      Wizard container        -->
                    <div class="wizard-container">
                        <div class="card wizard-card" id="wizardProfile">
                            <?php $this->load->view('home/quotes_modal.php'); ?>
                            <form action="javascript:void(0);" id="resPass-form" method="post" >
                                <div class="wizard-navigation">
                                    <ul>
                                        <li>
                                            <a href="#about" data-toggle="tab"></a>
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
                                                        <input id="rest_pass_email" name="resetEmail" type="email" class="form-control" placeholder="your@youremail.com">
                                                        <label for="email" class="fa fa-envelope" rel="tooltip" title="email"></label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="wizard-footer height-wizard">
                                    <input type='submit' class='btn btn-finish mx-auto d-block' name="resetbtn" value='Reset Password' />
                                    <div class="clearfix"></div>
                                    <div class="clearfix pt-3"></div>
                                    <center id="terms_privacy">
                                        <label><a data-toggle="modal" data-target="#tcModal" id="terms_of_use"><u>Terms of Use</u></a></label>
                                        <label id="spacer">&</label>
                                        <label><a data-toggle="modal" data-target="#ppModal" id="privacy_policy"><u>Privacy Policy</u></a></label>
                                    </center>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- wizard container -->
                </div>
            </div>
            <div id="divLoading"> </div>
        </div>
    </div>
</content>