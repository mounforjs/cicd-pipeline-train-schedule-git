<div class="modal" tabindex="-1" role="dialog" id="prizeModal" data-id="<?php echo $game->id; ?>" data-slug="<?php echo $game->slug; ?>">
    <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document">
        <div class="load d-none"><div class="imageLoader"></div></div>
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Congratulations!</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>

            <div class="modal-body">
                <div class="row prizeclaimbox p-2">
                    <div class="col-sm-5"><img src="https://dg7ltaqbp10ai.cloudfront.net/fit-in/redbow2.png" class="prizebow">
                        <div class="item-imgs-prize-slider">
                            <div id="confirmslider" class="flexslider">
                                <ul class="slides">
                                    <?php if (!$game->prize_image_data) { ?>
                                        <li><img src="<?php echo $game->GameImage; ?>"/></li>
                                    <?php } else { ?>
                                        <?php foreach($game->prize_image_data as $prize_image) { ?>
                                            <li><img src="<?php echo $prize_image->prize_image; ?>"/></li>
                                        <?php } ?>
                                    <?php } ?>
                                </ul>
                            </div>
                            <?php if (count($game->prize_image_data) != 1) {?>
                                <div id="confirmcarousel" class="flexslider flexslider-btm">
                                    <ul class="slides">
                                        <?php foreach($game->prize_image_data as $prize_image) { ?>
                                        <li>
                                        <img src="<?php echo $prize_image->prize_image; ?>"  style="height:70px"/>
                                        </li>
                                        <?php }?>
                                    </ul>
                                </div>
                            <?php }?>
                        </div>
                    </div>

                    <div class="col align-self-center">
						<h3 class="text-center">Claim your
							<br><?php echo $game->prize_title; ?></h3>
                        <div id="accordion">
							<center><button class="btn small text-center" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">View Prize Details</button></center>
							<div class="card">
								<div id="collapseOne" class="collapse hide" aria-labelledby="headingOne" data-parent="#accordion">
									<div class="card-body">
										<div class="prize-details">
											<div class="prize-description">
												<?php echo $game->prize_description; ?>
											</div>
											<div class="prize-description">
												<?php echo $game->prize_specification; ?>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<br>
                <input class="d-none" id="selectedAddress" name="selectedAddress" type="hidden" value="<?php echo $confirmPrize["defaultAddress"]->id;?>" data-selected="<?php echo $confirmPrize["defaultAddress"]->id;?>"/>
                <div id="destinationAddress" class="row <?php echo !isset($confirmPrize["defaultAddress"]) ? 'd-none' : ''; ?>">
                    <div class="col">
                        <div class="row justify-content-start">
                            <div class="col-auto">
                                <h3>Shipping Address: </h3>
                            </div>
                            <div class="col-auto">
                                <button id="changePrizeAddress" type="button" class="btn small orange pull-right <?php echo !isset($confirmPrize["defaultAddress"]) ? 'd-none' : ''; ?>">Change</button>
                                <button id="cancelChangePrizeAddress" type="button" class="d-none btn small red pull-right <?php echo !isset($confirmPrize["defaultAddress"]) ? 'd-none' : ''; ?>">Cancel</button>
                            </div>
                        </div>
						<div class="row">
							<div class="col-md-6">
								<div class="card addresscard mb-3">
									<div class="card-header">
										<h3 class="addressName">Default Address</h3>
									</div>
									<div class="card-body">
										<div class="addressData">
											<p id="destfullname"><?php echo strtoupper($confirmPrize["defaultAddress"]->fullname);?></p>
											<p id="destaddress_1"><?php echo strtoupper($confirmPrize["defaultAddress"]->address_1);?></p>
											<p id="destaddress_2"><?php echo strtoupper($confirmPrize["defaultAddress"]->address_2);?></p>
											<p><span id="destcity"><?php echo strtoupper($confirmPrize["defaultAddress"]->city);?></span>, <span id="deststate"><?php echo strtoupper($confirmPrize["defaultAddress"]->state);?></span></p>
											<p><span id="destzip"><?php echo strtoupper($confirmPrize["defaultAddress"]->zip);?></span></p>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<br>
					<div id="update_address" class="row <?php echo isset($confirmPrize["defaultAddress"]) ? 'd-none' : ''; ?>">
                    <br>
                    <div class="col pl-4 pr-4">
                        <?php $this->load->view("address/index", array("fname" => $confirmPrize["profile"]["firstname"], "lname" => $confirmPrize["profile"]["lastname"], "addresses" => $userData->addresses, "claim" => $confirmPrize["claim"], "addresses" => $confirmPrize["addresses"])); ?>
                    </div>
                </div>
            </div>

            <div class="modal-footer <?php echo (!isset($confirmPrize["defaultAddress"]) ? 'd-none' : ''); ?>">
                <button id="cancelNewAddress" type="button" class="btn btn-primary red mr-auto d-none">Cancel</button>
                <button id="addNewAddress" type="button" class="btn btn-primary green d-none">Add</button>

                <button id="changeAddress" type="button" class="btn btn-primary orange d-none">Change</button>

                <button id="updateAddress" type="button" class="btn btn-primary orange d-none" data-id="">Update</button>
                <button id="confirmPrizeAddress" type="button" class="btn btn-primary green <?php echo (!isset($confirmPrize["defaultAddress"]) ? 'd-none' : ''); ?>">CLAIM</button>
            </div>
        </div>
    </div>
</div>