<button id="mobileFilters" class="ml-auto nobuttonstyle" data-toggle="modal" data-target="#mobileFilterModal">
<img src="https://dg7ltaqbp10ai.cloudfront.net/fit-in/1591893623-filter.jpg">
</button>
<div class="legendbox">
    <button class="ml-auto legendicon" data-toggle="modal" data-target="#cardLegend">
    <i class="fas fa-info-circle"></i> LEGEND</button>
</div>
<div class="modal fade" id="cardLegend" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content nppadding">
            <div class="modal-header">
                <h4 class="whitetext"><span class="glyphicon glyphicon-lock"></span>Game Legend</h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <div class="modal-body">
                <div class="prize-key">
                    <div class="row p-0">
                        <div class="col-lg-6">
                            <div class="keyitem"> <span class="prizecosticon">
                                <i class="fa fa-gamepad" aria-hidden="true"></i>
                                </span> <strong>COST TO PLAY </strong>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="keyitem"> <span class="valueicon">
                                <i class="fa fa-trophy" aria-hidden="true"></i>
                                </span> <strong>VALUE </strong>
                            </div>
                        </div>
                    </div>
                    <div class="keyitem"><strong>TYPE OF GAME</strong><br>(2048, Puzzle, Challenge, Minecraft)<br>
                        <img src="https://dg7ltaqbp10ai.cloudfront.net/fit-in/41x41/2048-logo.png" alt="2048"> 
                        <img src="https://dg7ltaqbp10ai.cloudfront.net/fit-in/1591893582-categoryiconpuzzle.png" alt="Puzzle">
                        <img src="https://dg7ltaqbp10ai.cloudfront.net/fit-in/1591893396-categoryiconchallenge.png" alt="Challenge">
                        <img src="https://dg7ltaqbp10ai.cloudfront.net/fit-in/200x200/minecraft-logo.png" alt="Minecraft">
                    </div>
                    <div class="keyitem"><strong>REWARD</strong><br>(CASH, PRIZE, FREE)<br>
                        <i class="fas fa-money-bill"></i> <i class="fas fa-award"></i> <img src="https://dg7ltaqbp10ai.cloudfront.net/fit-in/freegamesicon.png" alt="Free Games">
                    </div>
                    <div class="keyitem"><strong>BENEFICIARY TYPE</strong><br>(Charity, Project, Cause, Education)<br>
                        <i class="fas fa-hand-holding-heart"></i> <i class="fas fa-lightbulb"></i> <i class="fa fa-globe"></i> <img src="https://dg7ltaqbp10ai.cloudfront.net/fit-in/200x200/education-icon-3-01.png" alt="Education">
                    </div>
                    <div style="clear: both;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="cardsdesktop">
    <div class="row">
        <div class="searchcardcontainer" style="<?php if (!$this->session->userdata('user_id')) echo 'padding-top: 25px'; ?>">
            <div class="filter-wrapper px-3">
                <div class="toggle-filter-wrapper">
                    <div class="row">
                        <div class="d-block d-sm-none col-12">
                            <div class="d-block d-sm-none pr-0" id="mobile-searchbar-wrapper">
                            </div>
                        </div>
                    </div>
                </div>
                <form name="desktopfilters">
                    <?php $this->load->view('layouts/filters/desktopfilters'); ?>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('layouts/filters/mobilecardfilters'); ?>