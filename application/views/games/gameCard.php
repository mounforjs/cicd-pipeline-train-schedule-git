<?php
        if (!empty($game_data)) {
            for ($i = 0; $i < count($game_data); $i++) {
            // get path of game images
            $image = $game_data[$i]->GameImage;
            $game_type_icon = $game_data[$i]->game_type_image;
            $game_credit_type_icon = $game_data[$i]->credit_type_image;
            $game_credit_type = $game_data[$i]->credit_type;
            $fundraise_image = $game_data[$i]->supported_fundraise_image;

            if (isset($game_data[$i]->flag_status)) {
                $flag_status = $game_data[$i]->flag_status;
            } ?>
            <div class="col-lg-4 col-md-6 p-2 pt-3 game_card">
            <div class="countdownribbon"><!-- adding red or yellow to the countdownribbon class will change it's color -->
                <p>
                    <span class="ribbon-content text-center" id="countdown<?php echo $i;?>"></span>           
                    <script>startCountdown('<?php echo $game_data[$i]->Publish_Date;?>', 'countdown<?php echo $i;?>');</script>  
                </p>
             </div>
                <?php
                if ($this->session->userdata('user_id') and ($this->uri->segment(3) == '' || $this->uri->segment(3) == 'play' || $this->uri->segment(3) == 'wishlist')) {
                    if ($game_data[$i]->game_wishlist_status == 1) { ?>
                        <div class="wishcard-yes" data-id='<?php echo $game_data[$i]->id; ?>' title="Remove from wishlist"><i class="fa fa-heart"></i></div>
                    <?php } else if ($game_data[$i]->game_wishlist_status == 0) { ?>
                        <div class="wishcard-no" data-id='<?php echo $game_data[$i]->id; ?>' title="Add to wishlist"><i class="fa fa-heart-o"></i></div>
                <?php }
                } ?>
            
                <article class="card">
                    <a href="<?= base_url() . "games/show/{$filters["show"]}/" . $game_data[$i]->slug; ?>">
                        <img class="thumb" src="<?= $image["image"]; ?>" onerror="imgError(this, '<?= $image['fallback']; ?>')">
                    </a>
                    <div class="card-body p-0">
                        <div class="infos">
                            <h3 class="cardquickstats">
                                <span class="prizecosticon mytooltip" data-toggle="tooltip" data-placement="top" title="Cost to Play"><i class="fa fa-gamepad" aria-hidden="true"></i> <?php echo ' $' . number_format($game_data[$i]->credit_cost); ?></span>
                                <span class="valueicon mytooltip" data-toggle="tooltip" data-placement="top" title="Winner's Reward Value"><i class="fa fa-trophy" aria-hidden="true"></i> <?php echo ' $' . number_format($game_data[$i]->value_of_the_game); ?></span>
                                <?php if ($game_data[$i]->credit_type  !== 'free') { ?>
                                    <span class="cardhover mytooltip <?php echo isset($game_data[$i]->supported_fundraise[0]->fundraise_type) ? $game_data[$i]->supported_fundraise[0]->fundraise_type : "charity" ?>" data-toggle="tooltip" data-placement="top" title="<?php echo ucfirst($game_data[$i]->supported_fundraise[0]->fundraise_type); ?>">
                                        <?php if (isset($game_data[$i]->supported_fundraise[0]->fundraise_type)) { ?>
                                            <?php if ($game_data[$i]->supported_fundraise[0]->fundraise_type == 'charity') { ?>
                                                <i class="fas fa-hand-holding-heart"></i>
                                            <?php } else if ($game_data[$i]->supported_fundraise[0]->fundraise_type == 'project') { ?>
                                                <i class="fas fa-lightbulb"></i>
                                            <?php } else if ($game_data[$i]->supported_fundraise[0]->fundraise_type == 'education') { ?>
                                                <i class="fa fa-graduation-cap"></i>
                                            <?php } else { ?>
                                                <i class="fa fa-globe"></i>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <i class="fas fa-hand-holding-heart"></i>
                                        <?php } ?>
                                    </span>
                                <?php } ?>
                                <span class="cardhover mytooltip" data-toggle="tooltip" data-placement="top" title="<?php echo ucwords(str_replace("_", " ", $game_data[$i]->game_type[0]->name)); ?>"><img src="<?php echo $game_type_icon; ?>" width="18" alt="Type of Game"></span>
                                <span class="cardhover mytooltip" data-toggle="tooltip" data-placement="top" title="<?php echo ucfirst($game_data[$i]->credit_type); ?>"><img src="<?php echo $game_credit_type_icon; ?>" width="18" alt="<?php echo $game_credit_type; ?>"></span>
                            </h3>
                            
                            <div class="row">
                                <div class="col pr-0">
                                    <h2 class="title"><?php echo $game_data[$i]->name; ?></h2>
                                </div>
                                
                                <?php if ($game_data[$i]->credit_type  !== 'free') { ?>
                                    <div class="col-sm-auto pl-0">
                                        <img class="fundlogo" src="<?= $fundraise_image["image"]; ?>" onerror="imgError(this, '<?= $fundraise_image['fallback']; ?>')">
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <p class="txt"><?php echo substrwords(strip_tags($game_data[$i]->game_desc), 100); ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="tagdetails">
                            <a class="btn details item" href="<?= base_url() . "games/show/{$filters["show"]}/" . $game_data[$i]->slug; ?>">Details</a>
                            <?php if (!empty($game_data[$i]->game_tags)) { ?>
                                <p class="tagitems">
                                    <?php
                                    $tags_arr = explode(',', $game_data[$i]->game_tags);
                                    foreach ($tags_arr as $tag) { ?>
                                        <a class="game_tag" href="javascript:void(null);"><?php echo $tag; ?></a>
                                    <?php } ?>
                                </p>
                            <?php } ?>
                        </div>
                    </div>
                </article>
            </div><?php 
        } 
    } ?>