<div class="modal fade" id="createConfig" tabindex="-1" role="dialog" aria-labelledby="createConfig" aria-hidden="true">
    <div class="modal-dialog modal-xl">

        <div class="modal-content">
            <div class="modal-header p-2">
                <h5 class="modal-title" id="exampleModalLabel">Create Configurations</h5>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>

            <div class="modal-body">
                <form id="newMinecraftConfig">
                    <div class="row">
                        <div class="col-sm-3">
                            <label for="new_gamemode">
                                Gamemode
                                <div class="mytooltip">
                                    <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title="">
                                    </i>
                                    <span class="tooltiptext">
                                        Which gamemode do you want to create a configuration for?
                                    </span>
                                </div>
                            </label>
                            <select class="form-control" id="new_gamemode" name="new_gamemode" required>
                                <option value="0" selected disabled hidden>None</option>
                                <?php for ($i = 0; $i < count($gamemodes); $i++) { ?>
                                    <option value="<?php echo $gamemodes[$i]->id; ?>">
                                        <?php echo ucwords(str_replace("_", " ", $gamemodes[$i]->name)); ?>
                                    </option>
                                <?php } ?>
                            </select>
                            <div id="new_gamemodeError"></div>
                        </div>

                        <div class="col-sm-3">
                            <label for="new_gameConfig">
                                Game Config Name
                                <div class="mytooltip">
                                    <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title="">
                                    </i>
                                    <span class="tooltiptext">
                                        Choose a name for your new configuration.
                                    </span>
                                </div>
                            </label>
                            <input class="form-control" id="new_gameConfig" name="new_gameConfig" value="" placeholder="New Config Name" required/>
                            <div id="new_gameConfigError"></div>
                            <br>
                        </div>

                        <div class="col-sm-3 ml-auto mb-auto d-none" id="clearNewConfig">
                            <a class="btn red pull-right clear">Clear</a>
                        </div>
                    </div>

                    <div class="row d-none" id="new_gameModeRules">
                        <div class="col">
                            <div class="config">
                                <h3 class="fs-title">Config Info</h3>
                                <?php $this->load->view("games/partials/minecraft/info"); ?>
                            </div>

                            <div class="config">
                                <div class="row mb-2">
                                    <div class="col-sm-3">
                                        <h3 class="fs-title my-2">Associated Kits</h3>
                                    </div>
                                    <div class="col-sm-3 ml-auto mt-auto">
                                        <a class="btn small blue pull-right existingKit disabled"><i class="fa fa-plus" aria-hidden="true"></i> Existing</a>
                                        <a class="btn small blue pull-right newKit"><i class="fa fa-plus" aria-hidden="true"></i> New</a>
                                    </div>
                                </div>
                                <div id="new_kits">
                                    <?php for ($i = 1; $i <= 9; $i++) { 
                                        $newkit["kitname"] = "New Kit " . $i; 
                                        $newkit["kitnum"] = $i; 

                                        $newkit["minecraftItems"] = $minecraftItems; 
                                        $newkit["minecraftArmor"] = $minecraftArmor; 
                                        $this->load->view("games/partials/minecraft/kit", $newkit); } ?>

                                    <div class="row">
                                        <a class="btn blue small ml-auto newKit"><i class="fa fa-plus" aria-hidden="true"></i> New</a>
                                        <a class="btn blue small mr-auto existingKit disabled"><i class="fa fa-plus" aria-hidden="true"></i> Existing</a>
                                    </div>
                                </div>
                            </div>

                            <div class="config">
                                <div class="row mb-2">
                                    <div class="col-sm-3">
                                        <h3 class="fs-title my-2">Associated Rules</h3>
                                    </div>
                                    <div class="col-sm-3 ml-auto mt-auto">
                                        <a class="btn blue small pull-right existingRule disabled"><i class="fa fa-plus" aria-hidden="true"></i> Existing</a>
                                        <a class="btn blue small pull-right newRule"><i class="fa fa-plus" aria-hidden="true"></i> New</a>
                                    </div>
                                </div>
                                <div id="new_gamerules">
                                    <?php for ($i = 1; $i <= 5; $i++) { 
                                        $newrule["rulename"] = "New Rule " . $i; 
                                        $newrule["rulenum"] = $i; 

                                        $newrule["minecraftItems"] = $minecraftItems; 
                                        $newrule["minecraftArmor"] = $minecraftArmor; 
                                        $newrule["minecraftMobs"] = $minecraftMobs; 
                                        $this->load->view("games/partials/minecraft/rule", $newrule); } ?>
                                    
                                    <div class="row">
                                        <a class="btn blue small ml-auto newRule"><i class="fa fa-plus" aria-hidden="true"></i> New</a>
                                        <a class="btn blue small mr-auto existingRule disabled"><i class="fa fa-plus" aria-hidden="true"></i> Existing</a>
                                    </div>
                                </div>
                            </div>
                        
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer d-none">
                <button class="btn orange pull-left" id="createNewConfig">Create</button>
            </div>
        </div>
    </div>
</div>