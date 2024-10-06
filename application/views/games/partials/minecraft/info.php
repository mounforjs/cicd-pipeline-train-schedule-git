<div class="row info" id="new_gameinfo">
    <div class="col-sm-3">
        <label for="new_arena">Arena
            <div class="mytooltip">
                <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title=""></i>
                <span class="tooltiptext">The map the player will play on.
                </span>
            </div>
        </label>
        <select class="form-control" id="new_arena" name="new_arena">
            <?php for ($i = 0; $i < count($arenas); $i++) { ?>
                <option value="<?php echo $arenas[$i]->id; ?>" <?php echo (($i == 0) ? "selected": ""); ?> 
                data-minx="<?php echo $arenas[$i]->min_x; ?>" data-maxx="<?php echo $arenas[$i]->max_x; ?>" 
                data-miny="<?php echo $arenas[$i]->min_y; ?>" data-maxy="<?php echo $arenas[$i]->max_y; ?>" 
                data-minz="<?php echo $arenas[$i]->min_z; ?>" data-maxz="<?php echo $arenas[$i]->max_z; ?>">
                <?php echo ucwords(str_replace("_", " ", $arenas[$i]->arena_name)); ?></option>
            <?php } ?>
        </select>
    </div>

    <div class="col-sm-3">
        <label for="new_play_type">Play Type
            <div class="mytooltip">
                <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title=""></i>
                <span class="tooltiptext">Play alone or with friends.
                </span>
            </div>
        </label>
        <select class="form-control" id="new_play_type" name="new_play_type">
            <option value="solo" selected>Solo</option>
            <option value="cooperative" disabled>Cooperative</option>
            <option value="competitive" disabled>Competitive</option>
        </select>
    </div>

    <div class="col-sm-3">
        <label for="new_game_base">Game Base
            <div class="mytooltip">
                <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title=""></i>
                <span class="tooltiptext">What your score will based on.
                </span>
            </div>
        </label>
        <select class="form-control" id="new_game_base" name="new_game_base">
            <option value="point" selected>Point</option>
            <option value="time">Time</option>
            <option value="judge">Judge</option>
        </select>
    </div>

    <div class="col-sm-3">
        <label for="new_timelimit">Time Limit
            <div class="mytooltip">
                <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title=""></i>
                <span class="tooltiptext">The number of seconds the player is limited.
                </span>
            </div>
        </label>
        <input class="form-control" id="new_timelimit" min="10" max="600" name="new_timelimit" type="number" value="10"/>
    </div>

    <div class="col-sm-3 d-none">
        <label for="new_point_value">Point Value
            <div class="mytooltip">
                <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title=""></i>
                <span class="tooltiptext">Decrease points by X amount over time limit.
                </span>
            </div>
        </label>
        <input class="form-control" id="new_point_value" min="-10000" max="10000" name="new_point_value" type="number" value="0"/>
    </div>

    <div class="col-sm-3">
        <label for="new_hunger">Hunger
            <div class="mytooltip">
                <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title=""></i>
                <span class="tooltiptext">Does the player need to eat?
                </span>
            </div>
        </label>
        <select class="form-control" id="new_hunger" name="new_hunger">
            <option value="0" selected>No</option>
            <option value="1">Yes</option>
        </select>
    </div>

    <div class="col-sm-3">
        <label for="new_regen">Regen
            <div class="mytooltip">
                <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title=""></i>
                <span class="tooltiptext">Does the player regenerate health?
                </span>
            </div>
        </label>
        <select class="form-control" id="new_regen" name="new_regen">
            <option value="0" selected>No</option>
            <option value="1">Yes</option>
        </select>
    </div>

    <div class="col-sm-3">
        <label for="new_looting">Looting
            <div class="mytooltip">
                <i class="fa fa-question-circle new-tip" data-placement="right" data-toggle="tooltip" title=""></i>
                <span class="tooltiptext">Can the player loot items?
                </span>
            </div>
        </label>
        <select class="form-control" id="new_looting" name="new_looting">
            <option value="0" selected>No</option>
            <option value="1">Yes</option>
        </select>
    </div>
</div>