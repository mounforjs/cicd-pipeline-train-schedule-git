<br><br>
<div class="widgetstyle">
    <h2 class="text-center dashboardHeader">RECENT ACTIVITY <i class="fas fa-history"></i></h2>
    <div class="row">
    <div class="col-lg-6">
        <h3 class="activityHeader">Your Activity</h3>
        <ul class="list-group">
            <?php for ($i = 0; $i < count($userActivity); $i++) { ?>
                <li class="notification">
                <div class="notificationContent row">
                    

                <?php if (isset($userActivity[$i]['fundraise_type'])) { 
                    if ($userActivity[$i]['fundraise_type'] == 'project') { ?>
                        <div class="notificationIcon">
                            <div class="recentproject">
                            <center><i class="fas fa-lightbulb"></i></center>
                            </div>
                        </div>
                    <?php } else if ($userActivity[$i]['fundraise_type'] == 'cause') { ?>
                        <div class="notificationIcon">
                            <div class="recentcause">
                            <center><i class="fas fa-globe"></i></center>
                            </div>
                        </div>
                    <?php } else if ($userActivity[$i]['fundraise_type'] == 'charity') { ?>
                        <div class="notificationIcon">
                            <div class="recentcharity">
                            <center><i class="fas fa-hand-holding-heart"></i></center>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="notificationIcon">
                            <div class="recentproject">
                            <center><i class="fas fa-lightbulb"></i></center>
                            </div>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <div class="notificationIcon">
                        <div class="recentproject">
                            <center><i class="fas fa-question"></i></center>
                        </div>
                    </div>
                <?php } ?>

                <?php if (isset($userActivity[$i]["action"])) {
                    $noti = "";

                    switch ($userActivity[$i]["action"]) { 
                        case "play": ?>
                            <div class="notificationIcon">
                            <div class="play">
                                <center><i class="fa fa-play"></i></center>
                            </div>
                            </div>

                        <div class="col">
                        <?php
                            $noti .= "You played <a href='" . asset_url("games/show/play/" . $userActivity[$i]["gameSlug"]) . "'>" . $userActivity[$i]["g_name"] . "</a>";

                            if (isset($userActivity[$i]["charity_id"])) {
                            $noti .= " supporting <a href='" . asset_url("fundraisers/show/all/" . $userActivity[$i]["charitySlug"]) . "'>" . $userActivity[$i]["c_name"] . "</a>.";
                            } else {
                            $noti .= ".";
                            }
                            break;
                        case "create": ?>
                            <div class="notificationIcon">
                            <div class="create">
                                <center><i class="fa fa-pencil"></i></center>
                            </div>
                            </div>

                        <div class="col">
                        <?php
                            $noti = "You created a new " . $userActivity[$i]["gameType"] . " game called <a href='" . asset_url("games/show/play/" . $userActivity[$i]["gameSlug"]) . "'>" . $userActivity[$i]["g_name"] . "</a>";
                            
                            if (isset($userActivity[$i]["charity_id"])) {
                            $noti .= " supporting <a href='" . asset_url("fundraisers/show/all/" . $userActivity[$i]["charitySlug"]) . "'>" . $userActivity[$i]["c_name"] . "</a>.";
                            } else {
                            $noti .= ".";
                            }
                            break;
                        case "add": ?>
                            <div class="notificationIcon">
                            <div class="add">
                                <center><i class="fa fa-plus"></i></center>
                            </div>
                            </div>

                        <div class="col">
                        <?php
                            if (isset($userActivity[$i]["charity_id"])) {
                            $noti .= "You added <a href='" . asset_url("fundraisers/show/all/" . $userActivity[$i]["charitySlug"]) . "'>" .  $userActivity[$i]["c_name"] . "</a> to your <a href='" . asset_url("fundraisers/show/supported") . "'>supported fundraisers list</a>.";
                            } else {
                            $noti .= "You added fundraiser to your <a href='" . asset_url("fundraisers/show/supported") . "'>supported fundraisers list</a>.";
                            }
                            break;
                        case "win": ?>
                            <div class="notificationIcon">
                            <div class="win">
                                <center><i class="fa fa-trophy"></i></center>
                            </div>
                            </div>

                        <div class="col">
                        <?php
                            if ($userActivity[$i]["gameUser"] == $userActivity[$i]["for_user"]) {
                                $noti .= "Winners have been selected for <a href='" . asset_url("games/show/play/" . $userActivity[$i]["gameSlug"]) . "'>" . $userActivity[$i]["g_name"] . "</a>";
                            } else {
                                $noti .= "You won <a href='" . asset_url("games/show/play/" . $userActivity[$i]["gameSlug"]) . "'>" . $userActivity[$i]["g_name"] . "</a>";
                            }

                            if (isset($userActivity[$i]["charity_id"])) {
                            $noti .= " supporting <a href='" . asset_url("fundraisers/show/all/" . $userActivity[$i]["charitySlug"]) . "'>" . $userActivity[$i]["c_name"] . "</a>.";
                            } else {
                            $noti .= ".";
                            }
                            break;
                        case "end": ?>
                            <div class="notificationIcon">
                            <div class="end">
                                <center><i class="fa fa-hourglass-end"></i></center>
                            </div>
                            </div>

                        <div class="col">
                        <?php
                            $noti = "Your " . $userActivity[$i]["gameType"] . " game called <a href='" . asset_url("games/show/play/" . $userActivity[$i]["gameSlug"]) . "'>" . $userActivity[$i]["g_name"] . "</a>";
                            
                            if (isset($userActivity[$i]["charity_id"])) {
                            $noti .= " supporting <a href='" . asset_url("fundraisers/show/all/" . $userActivity[$i]["charitySlug"]) . "'>" . $userActivity[$i]["c_name"] . "</a> has ended.";
                            } else {
                            $noti .= "has ended.";
                            }
                            break;
                        case "publish": ?>
                            <div class="notificationIcon">
                            <div class="publish">
                                <center><i class="fa fa-book"></i></center>
                            </div>
                            </div>

                        <div class="col">
                        <?php
                            $noti = "Your " . $userActivity[$i]["gameType"] . " game called <a href='" . asset_url("games/show/play/" . $userActivity[$i]["gameSlug"]) . "'>" . $userActivity[$i]["g_name"] . "</a>";
                            
                            if (isset($userActivity[$i]["charity_id"])) {
                            $noti .= " supporting <a href='" . asset_url("fundraisers/show/all/" . $userActivity[$i]["charitySlug"]) . "'>" . $userActivity[$i]["c_name"] . "</a> has been published.";
                            } else {
                            $noti .= "has been published.";
                            }
                            break;
                        case "confirm": ?>
                            <div class="notificationIcon">
                            <div class="confirmed">
                                <center><i class="fa fa-trophy"></i></center>
                            </div>
                            </div>

                        <div class="col">
                        <?php
                            $noti .= "You won a prize from <a href='" . asset_url("games/show/play/" . $userActivity[$i]["gameSlug"]) . "'>" . $userActivity[$i]["g_name"] . "</a>";

                            if (isset($userActivity[$i]["charity_id"])) {
                            $noti .= " supporting <a href='" . asset_url("fundraisers/show/all/" . $userActivity[$i]["charitySlug"]) . "'>" . $userActivity[$i]["c_name"] . "</a>.";
                            } else {
                            $noti .= ".";
                            }
                            break;
                    } echo $noti; ?>
                <?php } else { ?>
                    <?php echo $userActivity[$i]["Notes"]; ?>.
                <?php } ?>

                    </div>
                </div>
                </li>

            <?php } ?>
        </ul>
        <a class="loadrecent text-center pull-right" name="self">LOAD MORE <i class="fas fa-chevron-down"></i></a>
    </div>
    <div class="col-lg-6">
        <h3 class="activityHeader">Your Supporters</h3>
        <ul class="list-group">
            <?php for ($i = 0; $i < count($supporterActivity); $i++) { ?>
                <li class="notification">
                <div class="notificationContent">
                    <div class="notificationIcon">
                        
                <?php if (isset($supporterActivity[$i]['fundraise_type'])) { 
                    if ($supporterActivity[$i]['fundraise_type'] == 'project') { ?>
                        <div class="recentproject">
                            <center><i class="fas fa-lightbulb"></i></center>
                        </div>
                    </div>
                    <?php } else if ($supporterActivity[$i]['fundraise_type'] == 'cause') { ?>
                        <div class="recentcause">
                            <center><i class="fas fa-globe"></i></center>
                        </div>
                    </div>
                    <?php } else if ($supporterActivity[$i]['fundraise_type'] == 'charity') { ?>
                        <div class="recentcharity">
                            <center><i class="fas fa-hand-holding-heart"></i></center>
                        </div>
                    </div>
                    <?php } else { ?>
                        <div class="recentproject">
                            <center><i class="fas fa-lightbulb"></i></center>
                        </div>
                    </div>
                    <?php } ?>
                <?php } else { ?>
                        <div class="recentunknown">
                            <center><i class="fas fa-question"></i></center>
                        </div>
                    </div>
                <?php } ?>

                <div class="notificationIcon">

                <?php if (isset($supporterActivity[$i]["action"])) {
                    $noti = "";

                    switch ($supporterActivity[$i]["action"]) { 
                        case "play": ?>
                            <div class="play">
                            <center><i class="fa fa-play"></i></center>
                            </div>
                        </div>

                        <div class="col">
                        <?php
                            $noti .= $supporterActivity[$i]["username"] . " played your game <a href='" . asset_url("games/show/play/" . $supporterActivity[$i]["gameSlug"]) . "'>" . $supporterActivity[$i]["g_name"] . "</a>";

                            if (isset($supporterActivity[$i]["charity_id"])) {
                            $noti .= " supporting <a href='" . asset_url("fundraisers/show/all/" . $supporterActivity[$i]["charitySlug"]) . "'>" . $supporterActivity[$i]["c_name"] . "</a>.";
                            } else {
                            $noti .= ".";
                            }
                            break;
                        case "create": ?>
                            <div class="create">
                            <center><i class="fa fa-pencil"></i></center>
                            </div>
                        </div>
                        
                        <div class="col">
                        <?php
                            $noti = $supporterActivity[$i]["username"] . " created a new " . $supporterActivity[$i]["gameType"] . " game called <a href='" . asset_url("games/show/play/" . $supporterActivity[$i]["gameSlug"]) . "'>" . $supporterActivity[$i]["g_name"] . "</a>";
                            
                            if (isset($supporterActivity[$i]["charity_id"])) {
                            $noti .= " supporting <a href='" . asset_url("fundraisers/show/all/" . $supporterActivity[$i]["charitySlug"]) . "'>" . $supporterActivity[$i]["c_name"] . "</a>.";
                            } else {
                            $noti .= ".";
                            }
                            break;
                        case "add": ?>
                            <div class="add">
                            <center><i class="fa fa-plus"></i></center>
                            </div>
                        </div>
                        
                        <div class="col">
                        <?php
                            if (isset($supporterActivity[$i]["charity_id"])) {
                            $noti .= $supporterActivity[$i]["username"] . " added <a href='" . asset_url("fundraisers/show/all/" . $supporterActivity[$i]["charitySlug"]) . "'>" .  $supporterActivity[$i]["c_name"] . "</a> to your <a href='" . asset_url("fundraisers/show/supported") . "'>supported fundraisers list</a>.";
                            } else {
                            $noti .= $supporterActivity[$i]["username"] . " added fundraiser to your <a href='" . asset_url("fundraisers/show/supported") . "'>supported fundraisers list</a>.";
                            }
                            break;
                        case "win": ?>
                            <div class="win">
                            <center><i class="fa fa-trophy"></i></center>
                            </div>
                        </div>
                        
                        <div class="col">
                        <?php
                            $noti .= $supporterActivity[$i]["username"] ." won <a href='" . asset_url("games/show/play/" . $userActivity[$i]["gameSlug"]) . "'>" . $userActivity[$i]["g_name"] . "</a>";

                            if (isset($userActivity[$i]["charity_id"])) {
                            $noti .= " supporting <a href='" . asset_url("fundraisers/show/all/" . $userActivity[$i]["charitySlug"]) . "'>" . $userActivity[$i]["c_name"] . "</a>.";
                            } else {
                            $noti .= ".";
                            }
                            break;
                        case "end": ?>
                            <div class="end">
                            <center><i class="fa fa-hourglass-end"></i></center>
                            </div>
                        </div>
                        
                        <div class="col">
                        <?php
                            $noti = $supporterActivity[$i]["username"] . "'s " . $userActivity[$i]["gameType"] . " game called <a href='" . asset_url("games/show/play/" . $userActivity[$i]["gameSlug"]) . "'>" . $userActivity[$i]["g_name"] . "</a>";
                            
                            if (isset($userActivity[$i]["charity_id"])) {
                            $noti .= " supporting <a href='" . asset_url("fundraisers/show/all/" . $userActivity[$i]["charitySlug"]) . "'>" . $userActivity[$i]["c_name"] . "</a> has ended.";
                            } else {
                            $noti .= "has ended.";
                            }
                            break;
                        case "publish": ?>
                            <div class="publish">
                            <center><i class="fa fa-book"></i></center>
                            </div>
                        </div>
                        
                        <div class="col">
                        <?php
                            $noti = $supporterActivity[$i]["username"] . "'s " . $userActivity[$i]["gameType"] . " game called <a href='" . asset_url("games/show/play/" . $userActivity[$i]["gameSlug"]) . "'>" . $userActivity[$i]["g_name"] . "</a>";
                            
                            if (isset($userActivity[$i]["charity_id"])) {
                            $noti .= " supporting <a href='" . asset_url("fundraisers/show/all/" . $userActivity[$i]["charitySlug"]) . "'>" . $userActivity[$i]["c_name"] . "</a> has been published.";
                            } else {
                            $noti .= "has been published.";
                            }
                            break;
                        case "claimed": ?>
                            <div class="claimed">
                            <center><i class="fa fa-check"></i></center>
                            </div>
                        </div>
                        
                        <div class="col">
                        <?php
                            $noti = "A player that won your game, <a href='" . asset_url("games/show/play/" . $userActivity[$i]["gameSlug"]) . "'>" . $userActivity[$i]["g_name"] . "</a>";
                            $noti .= ", has claimed their prize!";
                            
                            break;
                    } echo $noti; ?>
                <?php } else { ?>
                    <?php echo $supporterActivity[$i]["Notes"]; ?>.
                <?php } ?>

                    </div>
                </div>
                </li>

            <?php } ?>
        </ul>
        <a class="loadrecent text-center pull-right" name="supporters">LOAD MORE <i class="fas fa-chevron-down"></i></a>
    </div>
    </div>
</div>

<br><br><br>