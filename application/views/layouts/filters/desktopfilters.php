<div class="row justify-content-between cardfilters">
    <div class="col">
        <ul class="nav nav-pills d-sm-flex">
            <!-- Game Value Dropdown -->
            <li>
                <a class="dropdown-toggle nav-link" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Game Value</a>
                <ul class="dropdown-menu checkbox-menu allow-focus" aria-labelledby="dropdownMenu1">
                    <li>
                        <label>
                            <div class="form-row align-items-center">
                                <div class="col-5">
                                    <label class="sr-only" for="inlineFormInput">Min Value</label>
                                    <input type="number" min="" class="form-control" name="game_value_min" tabindex="2" title="Please enter a minimum value" placeholder="Min" autocomplete="off" value="<?php echo (count($filters['game_value']) == 1 && !isset($game_values[$filters['game_value'][0]])) ? explode("-", $filters['game_value'])[0] : ''; ?>">
                                </div>
                                <div style="padding-left:10px;">-</div>
                                <div class="col-5">
                                    <label class="sr-only" for="inlineFormInput">Max Value</label>
                                    <input type="number" class="form-control" name="game_value_max" tabindex="2" title="Please enter a maximum value" placeholder="Max" autocomplete="off" value="<?php echo (count($filters['game_value']) == 1 && !isset($game_values[$filters['game_value'][0]])) ? explode("-", $filters['game_value'])[1] : ''; ?>">
                                </div>
                            </div>
                        </label>
                    </li>
                    <?php foreach ($game_values as $key => $val_gr): ?>
                    <li>
                        <label>
                            <input autocomplete="off" name="game_value_<?php echo htmlspecialchars(str_replace(' ', '', $key)); ?>" value="<?php echo htmlspecialchars(str_replace(' ', '', $key)); ?>" type="checkbox">
                            <?php echo htmlspecialchars($val_gr); ?>
                        </label>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </li>

            <!-- Cost to Play Dropdown -->
            <li>
                <a class="dropdown-toggle nav-link" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Cost to Play</a>
                <ul class="dropdown-menu checkbox-menu allow-focus" aria-labelledby="dropdownMenu1">
                    <li>
                        <label>
                            <div class="form-row align-items-center">
                                <div class="col-5">
                                    <label class="sr-only" for="inlineFormInput">Min Cost</label>
                                    <input type="number" class="form-control" name="game_cost_min" autocomplete="off" tabindex="2" title="Please enter a minimum cost &#013; The current cheapest game is $<?php echo htmlspecialchars($min_credit_cost); ?>" placeholder="Min" value="<?php echo (count($filters['game_cost']) == 1 && !isset($game_costs[$filters['game_cost'][0]])) ? explode("-", $filters['game_cost'])[0] : ''; ?>">
                                </div>
                                <div style="padding-left:10px;">-</div>
                                <div class="col-5">
                                    <label class="sr-only" for="inlineFormInput">Max Cost</label>
                                    <input type="number" class="form-control" name="game_cost_max" autocomplete="off" tabindex="2" title="Please enter a maximum cost &#013; The current most expensive game is $<?php echo htmlspecialchars($max_credit_cost); ?>" placeholder="Max" value="<?php echo (count($filters['game_cost']) == 1 && !isset($game_costs[$filters['game_cost'][0]])) ? explode("-", $filters['game_cost'])[1] : ''; ?>">
                                </div>
                            </div>
                        </label>
                    </li>
                    <?php foreach ($game_costs as $key => $cost_gr): ?>
                    <li>
                        <label class="form__check">
                            <input name="game_cost_<?php echo htmlspecialchars(str_replace(' ', '', $key)); ?>" value="<?php echo htmlspecialchars(str_replace(' ', '', $key)); ?>" type="checkbox" autocomplete="off">
                            <?php echo htmlspecialchars($cost_gr); ?>
                        </label>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </li>

            <!-- Reward Type Dropdown -->
            <li>
                <a class="dropdown-toggle nav-link" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Reward Type</a>
                <ul class="dropdown-menu checkbox-menu allow-focus" aria-labelledby="dropdownMenu1">
                    <li>
                        <label>
                            <input value="prize" type="checkbox" name="credit_type_prize" <?php echo (isset($filters['credit_type']) && in_array('prize', explode(",", $filters['credit_type']))) ? 'checked' : ''; ?> autocomplete="off"> Prize
                        </label>
                    </li>
                    <li>
                        <label>
                            <input autocomplete="off" value="credit" type="checkbox" name="credit_type_credit" <?php echo (isset($filters['credit_type']) && in_array('credit', explode(",", $filters['credit_type']))) ? 'checked' : ''; ?>> Cash
                        </label>
                    </li>
                </ul>
            </li>

            <!-- Game Type Dropdown -->
            <li>
                <a name="game_type_drop" class="dropdown-toggle nav-link" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Game Type</a>
                <ul class="dropdown-menu checkbox-menu allow-focus" aria-labelledby="game_type_drop">
                    <?php foreach ($game_types as $key => $game_type): ?>
                        <?php 
                        $is_checked = isset($filters['game_type']) && in_array($game_type->name, explode(",", $filters['game_type']));
                        ?>
                        <?php if (count($game_type->child_types) > 0): ?>
                        <li class="dropdown-submenu">
                            <label for="<?php echo htmlspecialchars($game_type->name); ?>" >
                                <input class="sub-root" autocomplete="off" value="<?php echo htmlspecialchars($game_type->name); ?>" type="checkbox" name="game_type_<?php echo htmlspecialchars(str_replace(' ', '', $game_type->name)); ?>" <?php echo $is_checked ? 'checked' : ''; ?>>
                                <a class="dropdown-toggle submenu-toggle" href="javascript:void(null);" role="button"><?php echo ucwords(str_replace("_", " ", $game_type->name)); ?></a>
                                <ul class="dropdown-menu">
                                    <?php foreach ($game_type->child_types as $child_type): ?>
                                        <?php 
                                        $is_sub_checked = isset($filters['game_type']) && in_array($child_type->name, explode(",", $filters['game_type']));
                                        ?>
                                        <li>
                                            <label>
                                                <input class="sub-item" autocomplete="off" value="<?php echo htmlspecialchars($child_type->name); ?>" type="checkbox" name="game_type_<?php echo htmlspecialchars(str_replace(' ', '', $child_type->name)); ?>" <?php echo $is_sub_checked ? 'checked' : ''; ?>>
                                                <?php echo ucwords(str_replace("_", " ", $child_type->name)); ?>
                                            </label>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </label>
                        </li>
                        <?php else: ?>
                        <li>
                            <label>
                                <input autocomplete="off" value="<?php echo htmlspecialchars($game_type->name); ?>" type="checkbox" name="game_type_<?php echo htmlspecialchars(str_replace(' ', '', $game_type->name)); ?>" <?php echo $is_checked ? 'checked' : ''; ?>>
                                <?php echo ucwords(str_replace("_", " ", $game_type->name)); ?>
                            </label>
                        </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </li>

            <!-- Beneficiary Dropdown -->
            <li>
                <a class="dropdown-toggle nav-link" data-toggle="dropdown" aria-expanded="true">Beneficiary</a>
                <!-- modal -->
                <ul class="dropdown-menu px-1 py-1" role="menu">
                    <li class="fundraise-type">
                        <div class="form-check-inline">
                            <label class="form-check-label">
                                <input name="beneficiary_type" autocomplete="off" type="radio" class="form-check-input fundraise_all" value="" checked>All
                            </label>
                        </div>
                        <div class="form-check-inline">
                            <label class="form-check-label">
                                <input name="beneficiary_type" autocomplete="off" type="radio" class="form-check-input fundraise_charity" value="charity">Charity
                            </label>
                        </div>
                        <div class="form-check-inline">
                            <label class="form-check-label">
                                <input name="beneficiary_type" autocomplete="off" type="radio" class="form-check-input fundraise_project" value="project">Project
                            </label>
                        </div>
                        <div class="form-check-inline">
                            <label class="form-check-label">
                                <input name="beneficiary_type" autocomplete="off" type="radio" class="form-check-input fundraise_cause" value="cause">Cause
                            </label>
                        </div>
                        <div class="form-check-inline">
                            <label class="form-check-label">
                                <input name="beneficiary_type" autocomplete="off" type="radio" class="form-check-input fundraise_education" value="education">Education
                            </label>
                        </div>
                    </li>
                    <li>
                        <select placeholder="Search" name="game_beneficiary">
                            <option value=""></option>
                            <?php foreach ($fundraise_list as $beneficiary): ?>
                            <option value="<?= htmlspecialchars($beneficiary->slug) ?>" <?php echo (isset($filters['beneficiary']) && $filters['beneficiary']['slug'] == $beneficiary->slug) ? 'selected' : ''; ?>><?= htmlspecialchars($beneficiary->name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
    <div class="col-md-6">
        <div class="row">
            <!-- Search Bar -->
            <div class="col-6 pr-2 mb-1">
                <div class="justify-content-right pr-2">
                    <div class="filter-search">
                        <input type="text" name="game_search" autocomplete="off" class="form-control search_input searchBarInput" aria-label="" placeholder="Search" value='<?php echo isset($filters["search"]["input"]) ? htmlspecialchars($filters["search"]["input"]) : ""; ?>'/>
                        <a class="search_icon"><i class="fas fa-search"></i></a>
                    </div>
                </div>
            </div>

            <!-- Sort List -->
            <div class="col-6 justify-content-right">
                <select name="game_sort_list" class="btn-sm filersorting">
                    <option value="price_high" <?php echo isset($filters['sort_list']) && $filters['sort_list'] == 'price_high' ? 'selected' : ''; ?>>Game Value (Highest)</option>
                    <option value="price_low" <?php echo isset($filters['sort_list']) && $filters['sort_list'] == 'price_low' ? 'selected' : ''; ?>>Game Value (Lowest)</option>
                    <option value="newest" <?php echo isset($filters['sort_list']) && $filters['sort_list'] == 'newest' ? 'selected' : ''; ?>>Newest First</option>
                    <option value="oldest" <?php echo isset($filters['sort_list']) && $filters['sort_list'] == 'oldest' ? 'selected' : ''; ?>>Oldest First</option>
                    <option value="fund_high" <?php echo isset($filters['sort_list']) && $filters['sort_list'] == 'fund_high' ? 'selected' : ''; ?>>Highest Fundraise</option>
                    <option value="soon" <?php echo isset($filters['sort_list']) && $filters['sort_list'] == 'soon' ? 'selected' : ''; ?>>Ending Soon</option>
                    <option value="rate_high_low" <?php echo isset($filters['sort_list']) && $filters['sort_list'] == 'rate_high_low' ? 'selected' : ''; ?>>Rating (Highest to Lowest)</option>
                    <option value="cost_low" <?php echo isset($filters['sort_list']) && $filters['sort_list'] == 'cost_low' ? 'selected' : ''; ?>>Cost to Play (Lowest to Highest)</option>
                    <option value="cost_high" <?php echo isset($filters['sort_list']) && $filters['sort_list'] == 'cost_high' ? 'selected' : ''; ?>>Cost to Play (Highest to Lowest)</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Filter Badges -->
<div class="filter-badges">
    <?php
    $badges = [
        "game_value" => "badge-primary",
        "game_cost" => "badge-danger",
        "game_type" => "badge-warning",
        "credit_type" => "badge-light",
        "game_tags" => "badge-light"
    ];
    $clear = 0;
    foreach ($filters as $filter => $value) {
        $filter_name = ucwords(str_replace("_", " ", $filter));
        switch ($filter) {
            case "beneficiary":
                if (isset($value["slug"])) {
                    $clear++;
                    $name = htmlspecialchars($value["name"]);
                    $slug = htmlspecialchars($value["slug"]);
                    echo "<a data-type='game_beneficiary' data-val='$slug' class='badge badge-success ml-1 filterbadge' data-toggle='tooltip' data-placement='bottom' data-original-title='$filter_name'>$name <i class='fa fa-times'></i></a>";
                }
                break;

            case "search":
                $input = htmlspecialchars($value["input"]);
                $badges = array_merge_recursive($value["keywords"], $value["tags"]);
                foreach ($badges as $badge) {
                    $clear++;
                    $badge = htmlspecialchars($badge);
                    echo "<a data-type='keywords' data-val='$badge' class='badge badge-secondary ml-1 filterbadge' data-toggle='tooltip' data-placement='bottom' data-original-title='$filter_name'>$badge <i class='fa fa-times'></i></a>";
                }
                break;

            case "game_value":
            case "game_cost":
            case "game_type":
            case "credit_type":
            case "game_tags":
                $value = is_array($value) ? implode(", ", array_map('htmlspecialchars', explode(",", $value))) : htmlspecialchars($value);
                $clear++;
                echo "<a data-type='$filter' data-val='$value' class='badge {$badges[$filter]} ml-1 filterbadge' data-toggle='tooltip' data-placement='bottom' data-original-title='$filter_name'>$value <i class='fa fa-times'></i></a>";
                break;

            default:
                break;
        }
    }
    ?>
    <?php if ($clear > 0): ?>
    <a class="badge badge-default clearAll ml-1" data-toggle='tooltip' data-placement='bottom' data-original-title='Clear'>Clear All <i class="fa fa-times"></i></a>
    <?php endif; ?>
</div>