
<div class="rule <?php echo (($rulenum == 1) ? "" : "d-none"); ?>">
    <div class="container">
        <div class="col">
            <div class="row">
                <div class="col-sm-3 nopadding">
                    <label for="new_rule_name_<?php echo $rulenum; ?>">Rule Name
                        <div class="mytooltip">
                            <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title=""></i>
                            <span class="tooltiptext">Name of the associated rule.</span>
                        </div>
                    </label>
                    <input class="form-control" id="new_rule_name_<?php echo $rulenum; ?>" name="new_rule_name_<?php echo $rulenum; ?>" value="" placeholder="<?php echo $rulename; ?>" required/>
                </div>
                <a class="btn small mb-auto ml-auto minimize"><i class="fa fa-minus" aria-hidden="true"></i></a>
                <?php if ($rulenum != 1) { ?><a class="btn red small mb-auto remove"><i class="fa fa-close"></i></a><?php } ?>
            </div>
            <br>
        </div>
    </div>

    <div class="container minimizeSection">
        <div class="row">
            <div class="col-sm-3">
                <label for="new_rule_type_<?php echo $rulenum; ?>">Rule Type
                    <div class="mytooltip">
                        <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title="" aria-hidden="true"></i>
                        <span class="tooltiptext">Type of the new rule.</span>
                    </div>
                </label>
                <select class="form-control" id="new_rule_type_<?php echo $rulenum; ?>" name="new_rule_type_<?php echo $rulenum; ?>" required>
                    <option value="0" selected disabled hidden>None</option>
                    <?php for ($i = 0; $i < count($ruletypes); $i++) { ?>
                        <option value="<?php echo $ruletypes[$i]->id; ?>"><?php echo ucwords(str_replace("_", " ", $ruletypes[$i]->rule)); ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-sm-3 d-none">
                <label for="new_value_<?php echo $rulenum; ?>">Value                                                                
                    <div class="mytooltip">
                        <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title="" aria-hidden="true"></i>
                        <span class="tooltiptext">
                            Points awarded for each action.</span>
                    </div>
                </label>
                <input class="form-control" id="new_value_<?php echo $rulenum; ?>" min="-10000" max="10000" step="1" name="new_value_<?php echo $rulenum; ?>" type="number" value="1">                                                                                       
            </div>

            <div class="col-sm-3 d-none">
                <label for="new_wave_D_<?php echo $rulenum; ?>">Wave D                                                                
                    <div class="mytooltip">
                        <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title="" aria-hidden="true"></i>
                        <span class="tooltiptext">
                            Number of mobs spawned per round.</span>
                    </div>
                </label>
                <input class="form-control " id="new_wave_D_<?php echo $rulenum; ?>" min="1" max="25" step="1" name="new_wave_D_<?php echo $rulenum; ?>" type="number" value="1">                                                                                                       
            </div>

            <div class="col-sm-3 d-none">
                <label for="new_wave_Int_<?php echo $rulenum; ?>">Wave Int                                                                
                    <div class="mytooltip">
                        <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title="" aria-hidden="true"></i>
                        <span class="tooltiptext">
                            Multiplier for mobs spawned per round.</span>
                    </div>
                </label>
                <input class="form-control " id="new_wave_Int_<?php echo $rulenum; ?>" min="1" max="10" step="1" name="new_wave_Int_<?php echo $rulenum; ?>" type="number" value="1">                                                                                                        
            </div>

            <div class="col-sm-3 d-none">
                <label for="new_location_x_<?php echo $rulenum; ?>">Location X
                    <div class="mytooltip">
                        <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title="" aria-hidden="true"></i>
                        <span class="tooltiptext">Points awarded for each action.</span>
                    </div>
                </label>
                <input class="form-control" id="new_location_x_<?php echo $rulenum; ?>" min="<?php echo $arenas[0]->min_x; ?>" max="<?php echo $arenas[0]->max_x; ?>" step="1" name="new_location_x_<?php echo $rulenum; ?>" type="number" value="0">
            </div>

            <div class="col-sm-3 d-none">
                <label for="new_location_y_<?php echo $rulenum; ?>">Location Y
                    <div class="mytooltip">
                        <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title="" aria-hidden="true"></i>
                        <span class="tooltiptext">Points awarded for each action.</span>
                    </div>
                </label>
                <input class="form-control" id="new_location_y_<?php echo $rulenum; ?>" min="<?php echo $arenas[0]->min_y; ?>" max="<?php echo $arenas[0]->max_y; ?>" step="1" name="new_location_y_<?php echo $rulenum; ?>" type="number" value="0">
            </div>
            
            <div class="col-sm-3 d-none">
                <label for="new_location_z_<?php echo $rulenum; ?>">Location Z
                    <div class="mytooltip">
                        <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title="" aria-hidden="true"></i>
                        <span class="tooltiptext">Points awarded for each action.</span>
                    </div>
                </label>
                <input class="form-control" id="new_location_z_<?php echo $rulenum; ?>" min="<?php echo $arenas[0]->min_z; ?>" max="<?php echo $arenas[0]->max_z; ?>" step="1" name="new_location_z_<?php echo $rulenum; ?>" type="number" value="0">
            </div>

            <div class="col-sm-3 d-none">
                <label for="new_checkpoint_<?php echo $rulenum; ?>">Checkpoint
                    <div class="mytooltip">
                        <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title="" aria-hidden="true"></i>
                        <span class="tooltiptext">Does the gamemode have checkpoints?</span>
                    </div>
                </label>
                <select id="new_checkpoint_<?php echo $rulenum; ?>" name="new_checkpoint_<?php echo $rulenum; ?>">
                    <option value="1" selected="">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>

            <div class="col-sm-3 d-none">
                <label for="new_judge_type_<?php echo $rulenum; ?>">Judge Type
                    <div class="mytooltip">
                        <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title="" aria-hidden="true"></i>
                        <span class="tooltiptext">The username of the player whom will conduct the judging.</span>
                    </div>
                </label>
                <select class="form-control" id="new_judge_type_<?php echo $rulenum; ?>" name="new_judge_type_<?php echo $rulenum; ?>">
                    <option value="manual" selected>Manual</option>
                    <option value="mob_farm">Mob Farm</option>
                </select>
            </div>

            <div class="col-sm-3 d-none">
                <label for="new_judge_player_<?php echo $rulenum; ?>">Judge Player
                    <div class="mytooltip">
                        <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title="" aria-hidden="true"></i>
                        <span class="tooltiptext">The username of the player whom will conduct the judging.</span>
                    </div>
                </label>
                <input class="form-control" id="new_judge_player_<?php echo $rulenum; ?>" name="new_judge_player_<?php echo $rulenum; ?>" value="<?php echo $minecraftPlayer; ?>" disabled>
            </div>

            <div class="col-lg-12 d-none">
                <label for="new_judge_descr_<?php echo $rulenum; ?>">Judge Description
                    <div class="mytooltip">
                        <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title="" aria-hidden="true"></i>
                        <span class="tooltiptext">Description of the action the players will be performing/creating.</span>
                    </div>
                </label>
                <input class="form-control" id="new_judge_descr_<?php echo $rulenum; ?>" name="new_judge_descr_<?php echo $rulenum; ?>" value="" maxlength="200">
            </div>

            <div class="col-sm-3 d-none">
                <label for="new_mob_type_<?php echo $rulenum; ?>">Mob Type                                                                
                    <div class="mytooltip">
                        <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title="" aria-hidden="true"></i>
                        <span class="tooltiptext">
                            The type of mob the gamemode employs.</span>
                    </div>
                </label>
                <select id="new_mob_type_<?php echo $rulenum; ?>" name="new_mob_type_<?php echo $rulenum; ?>">     
                    <option value="0" selected disabled hidden>None</option>                                                                                                                       <option value="51">Bat</option>
                    <?php for ($i = 0; $i < count($minecraftMobs); $i++) { ?>
                        <option value="<?php echo $minecraftMobs[$i]->id; ?>"><?php echo ucwords(str_replace("_", " ", $minecraftMobs[$i]->name)); ?></option>
                    <?php } ?>
                </select>                                                                                               
            </div>

            <div class="col-sm-3 d-none">
                <label for="new_item_type_<?php echo $rulenum; ?>">Item Type
                    <div class="mytooltip">
                        <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title="" aria-hidden="true"></i>
                        <span class="tooltiptext">The type of item the player needs to obtain.</span>
                    </div>
                </label>
                <select id="new_item_type_<?php echo $rulenum; ?>" name="new_item_type_<?php echo $rulenum; ?>">
                    <option value="0" selected disabled hidden>None</option>
                    <?php for ($i = 0; $i < count($minecraftItems); $i++) { ?>
                        <option value="<?php echo $minecraftItems[$i]->id; ?>"><?php echo ucwords(str_replace("_", " ", $minecraftItems[$i]->name)); ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-sm-3 d-none">
                <label for="new_starting_<?php echo $rulenum; ?>">Starting
                    <div class="mytooltip">
                        <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title="" aria-hidden="true"></i>
                        <span class="tooltiptext">The number of mobs that will spawn when testing submissions.</span>
                    </div>
                </label>
                <input class="form-control" id="new_starting_<?php echo $rulenum; ?>" min="0" max="16" step="1" name="new_starting_<?php echo $rulenum; ?>" type="number" value="0">
            </div>

            <div class="col-sm-3 d-none">
                <label for="new_perimeter_<?php echo $rulenum; ?>">Perimeter
                    <div class="mytooltip">
                        <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title="" aria-hidden="true"></i>
                        <span class="tooltiptext">Limits the size of submissions to be judged.</span>
                    </div>
                </label>
                <input class="form-control" id="new_perimeter_<?php echo $rulenum; ?>" min="1" max="32" step="1" name="new_perimeter_<?php echo $rulenum; ?>" type="number" value="1">
            </div>   
        </div>
    </div>
</div>