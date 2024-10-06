<?php if ($type == '2048') { ?>
    <div class="col-sm-3">
        <label for="game_tile_goal">
            Tile Number Goal
            <div class="mytooltip">
                <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title="">
                </i>
                <span class="tooltiptext">
                    This is the biggest variable to how long the game lasts. Set between 32 and 2048.
                </span>
            </div>
        </label>
        <select aria-invalid="false" aria-required="true" class="form-control valid" id="game_tile_goal" name="game_tile_goal" required="">
            <option value="32">
                32
            </option>
            <option value="64">
                64
            </option>
            <option value="128">
                128
            </option>
            <option selected="" value="256">
                256
            </option>
            <option value="512">
                512
            </option>
            <option value="1024">
                1024
            </option>
            <option value="2048">
                2048
            </option>
        </select>
    </div>

    <br>
    <div class="carddivider"></div>
<?php } else if ($type == 'puzzle') { ?>
    <div class="col-sm-3">
        <!-- START Unique Element for Puzzle -->
        <label for="game_tile_goal">
            Puzzle Difficulty
            <div class="mytooltip">
                <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title="">
                </i>
                <span class="tooltiptext">
                    You should preview your game at different grid sizes, to choose the difficulty you desire.
                </span>
            </div>
        </label>
        <select autocomplete="off" class="form-control" id="gamedifficulty" name="gamedifficulty" required>
            <option value="">
            </option>
            <option value="4">
                4x4 grid
            </option>
            <option value="5">
                5x5 grid
            </option>
            <option value="6">
                6x6 grid
            </option>
            <option value="7">
                7x7 grid
            </option>
            <option value="8">
                8x8 grid
            </option>
            <option value="9">
                9x9 grid
            </option>
            <option value="10">
                10x10 grid
            </option>
        </select>
        <div id="gameDifficultyError"></div>
        <!-- END Unique Element for Puzzle -->
    </div>
    
    <br>
    <div class="carddivider"></div>
<?php } else if ($type == 'challenge') { ?>

<?php } else if ($type == 'minecraft') { ?>
    <div id="minecraftSection">
        <div class="row" id="gameModes">
            <div class="col-sm-3">
                <label for="gamemode">
                    Gamemode
                    <div class="mytooltip">
                        <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title="">
                        </i>
                        <span class="tooltiptext">
                            You should preview your game to find the difficulty you are happy with.
                        </span>
                    </div>
                </label>
                <select class="form-control" id="gamemode" name="gamemode" required>
                    <?php for ($i = 0; $i < count($gamemodes); $i++) { ?>
                        <option value="<?php echo $gamemodes[$i]->id; ?>" <?php echo (($gamemodes[$i]->id == $selectedGamemode) ? "selected" : ""); ?>>
                            <?php echo ucwords(str_replace("_", " ", $gamemodes[$i]->name)); ?>
                        </option>
                    <?php } ?>
                </select>
                <div id="gamemodeError"></div>
            </div>

            <div class="col-sm-3">
                <label for="gameConfig">
                    Game Configs
                    <div class="mytooltip">
                        <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title="">
                        </i>
                        <span class="tooltiptext">
                            Choose a configuration for the gamemode.
                        </span>
                    </div>
                </label>
                <select class="form-control" id="gameConfig" name="gameConfig" required>
                    <?php for ($i = 0; $i < count($gameconfigs); $i++) { ?>
                        <option value="<?php echo $gameconfigs[$i]->id; ?>" <?php echo (($gameconfigs[$i]->id == $selectedConfig) ? "selected" : ""); ?>>
                            <?php echo ucwords(str_replace("_", " ", $gameconfigs[$i]->name)); ?>
                        </option>
                    <?php } ?>
                </select>
                <div id="gameConfigError"></div>

            </div>
            
            <div class="col d-flex">
                <button class="btn cfgbtn p-1" id="refreshConfigs" type="button"><i class="fa fa-refresh" aria-hidden="true"></i> </button>
                <button class="btn cfgbtn p-1 ml-auto disabled" id="newConfig" type="button" data-toggle="modal" data-target="#createConfig" disabled><i class="fa fa-plus" aria-hidden="true"></i> New</button>
            </div>
        </div>

        <div class="row" id ="gameModeRules">
            <div class="col">
                <div class="config">
                    <h3 class="fs-title">Config Info</h3>
                    <div class="row">
                        <?php foreach($gameconfigs[$selectedConfigIdx] as $key => $info) { ?>
                            <?php if (strpos($key, "id") !== false || strpos($key, "name") !== false || strpos($key, "gametype") !== false || strpos($key, "approved") !== false || strpos($key, "trial") !== false || strpos($key, "min") !== false || strpos($key, "max") !== false || gettype($info) == "array") { continue; } ?>
                            <div class="col-sm-3">
                                <label for="<?php echo $key; ?>"><?php echo ucwords(str_replace("_", " ", $key)); ?>
                                    <div class="mytooltip">
                                        <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title=""></i>
                                        <span class="tooltiptext">
                                            <?php switch ($key) {
                                                case "play_type":
                                                    echo "Play alone or with friends.";
                                                    break;
                                                case "game_base":
                                                    echo "What your score will based on.";
                                                    break;
                                                case "arena":
                                                    echo "The map the player will play on.";
                                                    break;
                                                case "timelimit":
                                                    echo "The number of seconds the player is limited.";
                                                    break;
                                                case "point_value":
                                                    echo "Decrease points by X amount over time limit.";
                                                    break;
                                                case "regen":
                                                    echo "Does the player regenerate health?";
                                                    break;
                                                case "hunger":
                                                    echo "Does the player need to eat?";
                                                    break;
                                                case "looting":
                                                    echo "Can the player loot items?";
                                                    break;                                                                                                                                                                                                                                                                                                                                                                                                                         
                                            } ?>
                                        </span>
                                    </div>
                                </label>
                                <input class="form-control" name="<?php echo $key; ?>" value="<?php if ($key == "regen" || $key == "hunger" || $key == "looting") { echo ($info == 1) ? "True" : "False"; } else if ($key == "timelimit") { echo $info . " seconds"; } else if ($key == "point_value") { echo isset($info) ? $info : "0"; } else { echo ucwords(str_replace("_", " ", $info)); }; ?>" disabled/>
                            </div>
                        <?php } ?>

                    </div>
                </div>

                <div class="config">
                    <h3 class="fs-title">Associated Kits</h3>
                    <div class="row">
                        <div class="col-sm-3">
                            <select class="form-control" id="kits" name="kits">
                                <?php for ($i = 0; $i < count($gameconfigs[$selectedConfigIdx]->kits); $i++) { ?>
                                    <option value="<?php echo $gameconfigs[$selectedConfigIdx]->kits[$i]->id; ?>">
                                        <?php echo "Kit " . ($i+1) .  " - " . ucwords(str_replace("_", " ", $gameconfigs[$selectedConfigIdx]->kits[$i]->name)); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                
                    <div class="row">
                        <?php foreach($gameconfigs[$selectedConfigIdx]->kits[$selectedConfigIdx] as $key => $kit) { ?>
                            <?php if (strpos($key, "id") !== false || strpos($key, "name") !== false || strpos($key, "key") !== false) { continue; } ?>
                            <div class="col-sm-3 <?php echo ((!isset($kit) || $kit == "") ? "d-none" : "") ?>">
                                <label for="<?php echo $key; ?>"><?php echo ucwords(str_replace("_", " ", $key)); ?>
                                    <div class="mytooltip">
                                        <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title=""></i>
                                        <span class="tooltiptext">
                                            <?php switch ($key) {
                                                case "name":
                                                    echo "Name of associated kit.";
                                                    break;
                                                case "offhand":
                                                    echo "Items available for the offhand.";
                                                    break;
                                                case "hotbar_1":
                                                    echo "Items available for the first hotbar slot.";
                                                    break;
                                                case "hotbar_2":
                                                    echo "Items available for the second hotbar slot.";
                                                    break;
                                                case "hotbar_3":
                                                    echo "Items available for the third hotbar slot.";
                                                    break;
                                                case "hotbar_4":
                                                    echo "Items available for the fourth hotbar slot.";
                                                    break;
                                                case "hotbar_5":
                                                    echo "Items available for the fifth hotbar slot.";
                                                    break;
                                                case "hotbar_6":
                                                    echo "Items available for the sixth hotbar slot.";
                                                    break;
                                                case "hotbar_7 ":
                                                    echo "Items available for the seventh hotbar slot.";
                                                    break;
                                                case "hotbar_8":
                                                    echo "Items available for the eighth hotbar slot.";
                                                    break;
                                                case "hotbar_9":
                                                    echo "Items available for the ninth hotbar slot.";
                                                    break;
                                                case "armor_head":
                                                    echo "Armor available for the head slot.";
                                                    break;
                                                case "armor_chest":
                                                    echo "Armor available for the chest slot.";
                                                    break;
                                                case "armor_pants":
                                                    echo "Armor available for the pant slot.";
                                                    break;
                                                case "armor_boots":
                                                    echo "Armor available for the boot slot.";
                                                    break;                                                                                                                                                                                                                                                                                                                                                                                                                               
                                            } ?>
                                        </span>
                                    </div>
                                </label>
                                <div class="kit_item">
                                    <input class="form-control" name="<?php echo $key; ?>" value="<?php echo (isset($kit)) ? ucwords(str_replace("_", " ", $kit->name)) : ""; ?>" disabled/>
                                    <div class="itemtooltip">
                                        <p class="my-0"><?php echo ((isset($kit)) ? ($kit->name . " - x" . $kit->amount . " - " . (($kit->custom == 1) ? "Custom" : "Normal")) : ""); ?></p>
                                        <ul>
                                            <li>Item Type: <?php echo ((isset($kit)) ? $kit->item_name : ""); ?></li>
                                            <li>Name Color: <?php echo ((isset($kit)) ? $kit->name_color : ""); ?></li>
                                            <li>Lore: <?php echo ((isset($kit)) ? $kit->name : ""); ?></li>
                                            <li>Lore Color: <?php echo ((isset($kit)) ? $kit->lore_color : ""); ?></li>
                                            <li>Show Enchants: <?php echo ((isset($kit)) ? (($kit->show_enchants == 1) ? "Yes" : "No") : ""); ?></li>
                                            <li>Unbreakable: <?php echo ((isset($kit)) ? (($kit->unbreakable == 1) ? "Yes" : "No") : ""); ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <div class="config">
                    <h3 class="fs-title mt-2">Associated Rules</h3>
                    <div class="row">
                        <div class="col-sm-3">
                            <select class="form-control" id="gameRules" name="gameRules">
                                <?php for ($i = 0; $i < count($gameconfigs[$selectedConfigIdx]->rules); $i++) { ?>
                                    <option value="<?php echo $gameconfigs[$selectedConfigIdx]->rules[$i]->rule_id; ?>">
                                        <?php echo ucwords(str_replace("_", " ", $gameconfigs[$selectedConfigIdx]->rules[$i]->rule_type)); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                
                    <div class="row">
                        <?php foreach($gameconfigs[$selectedConfigIdx]->rules[$selectedConfigIdx] as $key => $rule) { ?>
                            <?php if (strpos($key, "id") !== false || (strpos($key, "_type") !== false && strpos($key, "judge") === false)) { continue; } ?>
                            <div class="<?php echo ((strpos($key, "description") !== false) ? "col-lg-12" : "col-sm-3") ?> <?php echo (!isset($rule) ? "d-none" : "") ?>">
                                <label for="<?php echo $key; ?>"><?php echo ucwords(str_replace("_", " ", $key)); ?>
                                    <div class="mytooltip">
                                        <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title=""></i>
                                        <span class="tooltiptext">
                                            <?php switch ($key) {
                                                case "rule_name":
                                                    echo "Name of the associated rule.";
                                                    break;
                                                case "value":
                                                    echo "Points awarded for each action.";
                                                    break;
                                                case "wave_D":
                                                    echo "Number of mobs spawned per round.";
                                                    break;
                                                case "wave_Int":
                                                    echo "Multiplier for mobs spawned per round.";
                                                    break;
                                                case "location_x":
                                                    echo "X coordinate of end objective.";
                                                    break;
                                                case "location_y":
                                                    echo "Y coordinate of end objective.";
                                                    break;
                                                case "location_z":
                                                    echo "Z coordinate of end objective.";
                                                    break;
                                                case "checkpoint":
                                                    echo "Does the gamemode have checkpoints?";
                                                    break;
                                                case "item_type":
                                                    echo "The type of item the player needs to obtain.";
                                                    break;
                                                case "item_name":
                                                    echo "Items the player needs to obtain with custom names.";
                                                    break;
                                                case "mob_name":
                                                    echo "The type of mob the rule tracks.";
                                                    break;
                                                case "judge_description":
                                                    echo "Description of the action the players will be performing/creating.";
                                                    break;
                                                case "judge_player":
                                                    echo "The username of the player whom will conduct the judging.";
                                                    break;
                                                case "perimeter":
                                                    echo "Limits the size of submissions to be judged.";
                                                    break;
                                                case "starting":
                                                    echo "The number of mobs that will spawn when testing submissions.";
                                                    break;
                                                case "judge_type":
                                                    echo "The type of objective that will be judged.";
                                                    break;                                                                                                                                                                                                                                                                                                                                                                                                                                 
                                            } ?>
                                        </span>
                                    </div>
                                </label>
                                <input class="form-control" name="<?php echo $key; ?>" value="<?php if ($key == "checkpoint") { echo (($rule == 1) ? "True" : "False"); } else { echo ucwords(str_replace("_", " ", $rule)); } ?>" disabled/>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- ?php $this->load->view("games/partials/minecraft/createconfig"); ? -->
    </div>

    <script src="<?php echo asset_url('assets/js/minecraft.js'); ?>"></script>

    <br>
    <div class="carddivider"></div>
<?php } else { ?>
    <div class="col-sm-3">
        <label for="numberOfLives">
            Lives
            <div class="mytooltip">
                <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title="">
                </i>
                <span class="tooltiptext">
                    You should preview your game to find the difficulty you are happy with.
                </span>
            </div>
        </label>
        <select autocomplete="off" class="form-control" id="numberOfLives" name="numberOfLives" required>
            <?php for ($i = 1; $i < 10; $i++) { ?>
                <option value="<?php echo $i; ?>">
                    <?php echo $i . ($i > 1 ? " Lives" : " Life"); ?>
                </option>
            <?php } ?>
        </select>
        <div id="numberOfLivesError"></div>
    </div>
<?php } ?>