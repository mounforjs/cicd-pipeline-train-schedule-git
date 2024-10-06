<div class="kit <?php echo (($kitnum == 1) ? "" : "d-none"); ?>">
    <div class="container">
        <div class="col">
            <div class="row">
                <div class="col-sm-3 pl-0">
                    <label for="new_kit_name_<?php echo $kitnum; ?>">Name 
                        <div class="mytooltip">
                            <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip"
                                title="" aria-hidden="true"></i>
                            <span class="tooltiptext">Name of the associated kit.</span>
                        </div>
                    </label>
                    <input class="form-control" id="new_kit_name_<?php echo $kitnum; ?>" name="new_kit_name_<?php echo $kitnum; ?>" value="" placeholder="<?php echo $kitname; ?>" required/>
                </div>
                <div class="col-sm-3">
                    <label for="new_kit_name_color_<?php echo $kitnum; ?>">Name Color
                        <div class="mytooltip">
                            <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip"
                                title="" aria-hidden="true"></i>
                            <span class="tooltiptext">Color of the associated name.</span>
                        </div>
                    </label>
                    <select id="new_new_kit_name_color_key_<?php echo $kitnum; ?>" name="new_kit_name_color_<?php echo $kitnum; ?>">
                        <option value="0" selected>None</option>
                        <?php for ($i = 0; $i < count($minecraftColors); $i++) { ?>
                            <option value="<?php echo $minecraftColors[$i]->id; ?>"><?php echo ucwords($minecraftColors[$i]->color); ?></option>
                        <?php } ?>
                    </select>
                </div>
                <a class="btn small mb-auto ml-auto minimize"><i class="fa fa-minus" aria-hidden="true"></i></a>
                <?php if ($kitnum != 1) { ?><a class="btn red small mb-auto remove"><i class="fa fa-close" aria-hidden="true"></i></a> <?php } ?>
            </div>
            <br>
        </div>
    </div>
    <div class="container minimizeSection">
        <div class="row">
            <div class="col-sm-3">
                <label for="new_kit_key_<?php echo $kitnum; ?>">Key Item
                    <div class="mytooltip">
                        <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title=""
                            aria-hidden="true"></i>
                        <span class="tooltiptext">
                            Items available for the first hotbar slot. </span>
                    </div>
                </label>

                <select id="new_kit_key_<?php echo $kitnum; ?>" name="new_kit_key_<?php echo $kitnum; ?>">
                    <option value="0" selected>None</option>
                    <?php for ($i = 0; $i < count($minecraftItems); $i++) { ?>
                        <option value="<?php echo $minecraftItems[$i]->id; ?>"><?php echo ucwords($minecraftItems[$i]->name); ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-sm-3">
                <label for="new_hotbar_1_<?php echo $kitnum; ?>">Hotbar 1 
                    <div class="mytooltip">
                        <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title=""
                            aria-hidden="true"></i>
                        <span class="tooltiptext">
                            Items available for the first hotbar slot. </span>
                    </div>
                </label>

                <select id="new_hotbar_1_<?php echo $kitnum; ?>" name="new_hotbar_1_<?php echo $kitnum; ?>">
                    <option value="0" selected>None</option>
                    <?php for ($i = 0; $i < count($minecraftItems); $i++) { ?>
                        <option value="<?php echo $minecraftItems[$i]->id; ?>"><?php echo ucwords($minecraftItems[$i]->name); ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-sm-3">
                <label for="new_hotbar_2_<?php echo $kitnum; ?>">Hotbar 2 
                    <div class="mytooltip">
                        <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title=""
                            aria-hidden="true"></i>
                        <span class="tooltiptext">
                            Items available for the second hotbar slot. </span>
                    </div>
                </label>

                <select id="new_hotbar_2_<?php echo $kitnum; ?>" name="new_hotbar_2_<?php echo $kitnum; ?>">
                    <option value="0" selected>None</option>
                    <?php for ($i = 0; $i < count($minecraftItems); $i++) { ?>
                        <option value="<?php echo $minecraftItems[$i]->id; ?>"><?php echo ucwords($minecraftItems[$i]->name); ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-sm-3">
                <label for="new_hotbar_3_<?php echo $kitnum; ?>">Hotbar 3 
                    <div class="mytooltip">
                        <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title=""
                            aria-hidden="true"></i>
                        <span class="tooltiptext">
                            Items available for the third hotbar slot. </span>
                    </div>
                </label>

                <select id="new_hotbar_3_<?php echo $kitnum; ?>" name="new_hotbar_3_<?php echo $kitnum; ?>">
                    <option value="0" selected>None</option>
                    <?php for ($i = 0; $i < count($minecraftItems); $i++) { ?>
                        <option value="<?php echo $minecraftItems[$i]->id; ?>"><?php echo ucwords($minecraftItems[$i]->name); ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-sm-3">
                <label for="new_hotbar_4_<?php echo $kitnum; ?>">Hotbar 4 
                    <div class="mytooltip">
                        <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title=""
                            aria-hidden="true"></i>
                        <span class="tooltiptext">
                            Items available for the fourth hotbar slot. </span>
                    </div>
                </label>

                <select id="new_hotbar_4_<?php echo $kitnum; ?>" name="new_hotbar_4_<?php echo $kitnum; ?>">
                    <option value="0" selected>None</option>
                    <?php for ($i = 0; $i < count($minecraftItems); $i++) { ?>
                        <option value="<?php echo $minecraftItems[$i]->id; ?>"><?php echo ucwords($minecraftItems[$i]->name); ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-sm-3">
                <label for="new_hotbar_5_<?php echo $kitnum; ?>">Hotbar 5 
                    <div class="mytooltip">
                        <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title=""
                            aria-hidden="true"></i>
                        <span class="tooltiptext">
                            Items available for the fifth hotbar slot. </span>
                    </div>
                </label>

                <select id="new_hotbar_5_<?php echo $kitnum; ?>" name="new_hotbar_5_<?php echo $kitnum; ?>">
                    <option value="0" selected>None</option>
                    <?php for ($i = 0; $i < count($minecraftItems); $i++) { ?>
                        <option value="<?php echo $minecraftItems[$i]->id; ?>"><?php echo ucwords($minecraftItems[$i]->name); ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-sm-3">
                <label for="new_hotbar_6_<?php echo $kitnum; ?>">Hotbar 6 
                    <div class="mytooltip">
                        <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title=""
                            aria-hidden="true"></i>
                        <span class="tooltiptext">
                            Items available for the sixth hotbar slot. </span>
                    </div>
                </label>

                <select id="new_hotbar_6_<?php echo $kitnum; ?>" name="new_hotbar_6_<?php echo $kitnum; ?>">
                    <option value="0" selected>None</option>
                    <?php for ($i = 0; $i < count($minecraftItems); $i++) { ?>
                        <option value="<?php echo $minecraftItems[$i]->id; ?>"><?php echo ucwords($minecraftItems[$i]->name); ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-sm-3">
                <label for="new_hotbar_7_<?php echo $kitnum; ?>">Hotbar 7 
                    <div class="mytooltip">
                        <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title=""
                            aria-hidden="true"></i>
                        <span class="tooltiptext">
                        </span>
                    </div>
                </label>

                <select id="new_hotbar_7_<?php echo $kitnum; ?>" name="new_hotbar_7_<?php echo $kitnum; ?>">
                    <option value="0" selected>None</option>
                    <?php for ($i = 0; $i < count($minecraftItems); $i++) { ?>
                        <option value="<?php echo $minecraftItems[$i]->id; ?>"><?php echo ucwords($minecraftItems[$i]->name); ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-sm-3">
                <label for="new_hotbar_8_<?php echo $kitnum; ?>">Hotbar 8 
                    <div class="mytooltip">
                        <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title=""
                            aria-hidden="true"></i>
                        <span class="tooltiptext">
                            Items available for the eighth hotbar slot. </span>
                    </div>
                </label>

                <select id="new_hotbar_8_<?php echo $kitnum; ?>" name="new_hotbar_8_<?php echo $kitnum; ?>">
                    <option value="0" selected>None</option>
                    <?php for ($i = 0; $i < count($minecraftItems); $i++) { ?>
                        <option value="<?php echo $minecraftItems[$i]->id; ?>"><?php echo ucwords($minecraftItems[$i]->name); ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-sm-3">
                <label for="new_hotbar_9_<?php echo $kitnum; ?>">Hotbar 9 
                    <div class="mytooltip">
                        <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title=""
                            aria-hidden="true"></i>
                        <span class="tooltiptext">
                            Items available for the ninth hotbar slot. </span>
                    </div>
                </label>

                <select id="new_hotbar_9_<?php echo $kitnum; ?>" name="new_hotbar_9_<?php echo $kitnum; ?>">
                    <option value="0" selected>None</option>
                    <?php for ($i = 0; $i < count($minecraftItems); $i++) { ?>
                        <option value="<?php echo $minecraftItems[$i]->id; ?>"><?php echo ucwords($minecraftItems[$i]->name); ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-sm-3">
                <label for="new_offhand_<?php echo $kitnum; ?>">Offhand 
                    <div class="mytooltip">
                        <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title=""
                            aria-hidden="true"></i>
                        <span class="tooltiptext">
                            Items available for the offhand. </span>
                    </div>
                </label>

                <select id="new_offhand_<?php echo $kitnum; ?>" name="new_offhand_<?php echo $kitnum; ?>">
                    <option value="0" selected>None</option>
                    <?php for ($i = 0; $i < count($minecraftItems); $i++) { ?>
                        <option value="<?php echo $minecraftItems[$i]->id; ?>"><?php echo ucwords($minecraftItems[$i]->name); ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-sm-3">
                <label for="new_armor_head_<?php echo $kitnum; ?>">Armor Head 
                    <div class="mytooltip">
                        <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title=""
                            aria-hidden="true"></i>
                        <span class="tooltiptext">
                            Armor available for the head slot. </span>
                    </div>
                </label>

                <select id="new_armor_head_<?php echo $kitnum; ?>" name="new_armor_head_<?php echo $kitnum; ?>">
                    <option value="0" selected>None</option>
                    <?php for ($i = 0; $i < count($minecraftArmor); $i++) { ?>
                        <option value="<?php echo $minecraftArmor[$i]->id; ?>"><?php echo ucwords($minecraftArmor[$i]->name); ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-sm-3">
                <label for="new_armor_chest_<?php echo $kitnum; ?>">Armor Chest 
                    <div class="mytooltip">
                        <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title=""
                            aria-hidden="true"></i>
                        <span class="tooltiptext">
                            Armor available for the chest slot. </span>
                    </div>
                </label>

                <select id="new_armor_chest_<?php echo $kitnum; ?>" name="new_armor_chest_<?php echo $kitnum; ?>">
                    <option value="0" selected>None</option>
                    <?php for ($i = 0; $i < count($minecraftArmor); $i++) { ?>
                        <option value="<?php echo $minecraftArmor[$i]->id; ?>"><?php echo ucwords($minecraftArmor[$i]->name); ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-sm-3">
                <label for="new_armor_pants_<?php echo $kitnum; ?>">Armor Pants 
                    <div class="mytooltip">
                        <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title=""
                            aria-hidden="true"></i>
                        <span class="tooltiptext">
                            Armor available for the pant slot. </span>
                    </div>
                </label>

                <select id="new_armor_pants_<?php echo $kitnum; ?>" name="new_armor_pants_<?php echo $kitnum; ?>">
                <option value="0" selected>None</option>
                    <?php for ($i = 0; $i < count($minecraftArmor); $i++) { ?>
                        <option value="<?php echo $minecraftArmor[$i]->id; ?>"><?php echo ucwords($minecraftArmor[$i]->name); ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="col-sm-3">
                <label for="new_armor_boots_<?php echo $kitnum; ?>">Armor Boots 
                    <div class="mytooltip">
                        <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title=""
                            aria-hidden="true"></i>
                        <span class="tooltiptext">
                            Armor available for the boot slot. </span>
                    </div>
                </label>

                <select id="new_armor_boots_<?php echo $kitnum; ?>" name="new_armor_boots_<?php echo $kitnum; ?>">
                    <option value="0" selected>None</option>
                    <?php for ($i = 0; $i < count($minecraftArmor); $i++) { ?>
                        <option value="<?php echo $minecraftArmor[$i]->id; ?>"><?php echo ucwords($minecraftArmor[$i]->name); ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
    </div>
</div>