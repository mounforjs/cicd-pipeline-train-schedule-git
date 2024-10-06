<div class="tab-pane <?php echo ($tab == 1) ? "show active" : ""; ?>" id="player" role="tabpanel" aria-labelledby="player-tab">

    <ul class="nav nav-tabs" id="playerChartTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="playerFunds-tab" data-toggle="tab" href="#playerFunds" role="tab" aria-controls="playerFunds" aria-selected="true">Funds</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="playerGames-tab" data-toggle="tab" href="#playerGames" role="tab" aria-controls="playerGames" aria-selected="false">Games</a>
        </li>
        <!-- <li class="nav-item">
            <a class="nav-link" id="playerScores-tab" data-toggle="tab" href="#playerScores" role="tab" aria-controls="playerScores" aria-selected="false">Scores</a>
        </li> -->
    </ul>
    <div class="tab-content" id="playerChartContent">
        <div class="tab-pane fade show active" id="playerFunds" role="tabpanel" aria-labelledby="playerFunds-tab">

            <div class="widgetstyle">

                <?php include 'tab-filter.php'; ?>

                <div class="row">
                <!-- chart -->
                <div class="col-lg-4 chartSection">
                    <h4>Total Donations Given</h4>
                    <h3>Total: $<?php echo number_format($totalDonations, 2); ?> </h3>

                    <div class="col chartfilters">
                        <select class="form-control chart13-dropdown" name="chartType">
                                <option selected="selected" value="0">PieChart</option>
                                <option value="1">BarChart</option>
                        </select>
                    </div>

                    <div id="chart13" class="chart" data-dataType="funds" data-filter='overall'></div>
                    
                    <div class="row">
                        <button class="nav-link btn red breakdown">Breakdown</button>
                    </div>
                </div>
                <!-- chart -->

                <!-- chart -->
                <div class="col-lg-4 chartSection">
                    <h4>Donations Given to Other Fundraisers/Creators</h4>
                    <h3>Total: $<?php echo number_format($totalOtherDonations, 2); ?></h3>

                    <div class="col chartfilters">
                        <select class="form-control chart14-dropdown" name="chartType">
                                <option selected="selected" value="0">PieChart</option>
                                <option value="1">BarChart</option>
                        </select>
                    </div>

                    <div id="chart14" class="chart" data-dataType="funds" data-filter='other'></div>

                    <div class="row">
                        <button class="nav-link btn red breakdown">Breakdown</button>
                    </div>
                </div>
                <!-- chart -->

                <!-- chart -->
                <div class="col-lg-4 chartSection">
                    <h4>Total Money Won</h4>
                    <h3>Total: $<?php echo number_format($totalWon, 2); ?></h3>

                    <div class="col chartfilters">
                        <select class="form-control chart15-dropdown" name="chartType">
                                <option selected="selected" value="0">PieChart</option>
                                <option value="1">BarChart</option>
                        </select>
                    </div>

                    <div id="chart15" class="chart" data-dataType="funds" data-filter='won'></div>

                    <div class="row">
                        <button class="nav-link btn red breakdown">Breakdown</button>
                    </div>
                </div>
                <!-- chart -->

                </div>
                
                <div class="col dashboardHide" >
                <br>
                <hr>
                <br>
                <h3>Breakdown: </h3>
                <div class="row dashboard">
                    <div class="col">
                        <div class="filter_div"></div>
                        <div class="chart_div"></div>
                    </div>
                    
                    <div class="col table">
                        <div class="table_div"></div>
                    </div>
                    
                </div>
                </div>

            </div>

        </div>

        <div class="tab-pane fade" id="playerGames" role="tabpanel" aria-labelledby="playerGames-tab">

            <div class="widgetstyle">

                <?php include 'tab-filter.php'; ?>

                <div class="row">
                <!-- chart -->
                <div class="col-lg-4 chartSection">
                    <h4># of Games Played</h4>
                    <h3>Total: <?php echo $totalGamesPlayed; ?> games </h3>

                    <div class="col chartfilters">
                        <select class="form-control chart16-dropdown" name="chartType">
                                <option selected="selected" value="0">PieChart</option>
                                <option value="1">BarChart</option>
                        </select>
                    </div>

                    <div id="chart16" class="chart" data-dataType="playerGames" data-filter='played'></div>
                    
                    <div class="row">
                        <button class="nav-link btn red breakdown">Breakdown</button>
                    </div>
                </div>
                <!-- chart -->

                <!-- chart -->
                <div class="col-lg-4 chartSection">
                    <h4># of Fundraisers Supported</h4>
                    <h3>Total: <?php echo $totalFundraisersSupported; ?> fundraisers</h3>

                    <div class="col chartfilters">
                        <select class="form-control chart17-dropdown" name="chartType">
                                <option selected="selected" value="0">PieChart</option>
                                <option value="1">BarChart</option>
                        </select>
                    </div>

                    <div id="chart17" class="chart" data-dataType="playerGames" data-filter='fundraiser'></div>

                    <div class="row">
                        <button class="nav-link btn red breakdown">Breakdown</button>
                    </div>
                </div>
                <!-- chart -->

                <!-- chart -->
                <div class="col-lg-4 chartSection">
                    <h4>Type of Games Played</h4>
                    <h3>Total: <?php echo $totalGametypesPlayed; ?> types</h3>

                    <div class="col chartfilters">
                        <select class="form-control chart18-dropdown" name="chartType">
                                <option selected="selected" value="0">PieChart</option>
                                <option value="1">BarChart</option>
                        </select>
                    </div>

                    <div id="chart18" class="chart" data-dataType="playerGames" data-filter='gametype'></div>

                    <div class="row">
                        <button class="nav-link btn red breakdown">Breakdown</button>
                    </div>

                </div>
                <!-- chart -->

                </div>
                
                <div class="col dashboardHide" >
                <br>
                <hr>
                <br>
                <h3>Breakdown: </h3>
                <div class="row dashboard">
                    <div class="col">
                        <div class="filter_div"></div>
                        <div class="chart_div"></div>
                    </div>
                    
                    <div class="col table">
                        <div class="table_div"></div>
                    </div>
                    
                </div>
                </div>

            </div>

        </div>

        <div class="tab-pane fade" id="playerScores" role="tabpanel" aria-labelledby="playerScores-tab">

            <div class="widgetstyle">

                <?php include 'tab-filter.php'; ?>

                <div class="row">
                <!-- chart -->
                <div class="col-lg-4 chartSection">
                    <h4>Highest Scores</h4>
                    <h3>Total: <?php echo $totalHighestScores; ?> scores </h3>

                    <div class="col chartfilters">
                        <select class="form-control chart1-dropdown" name="chartType">
                                <option selected="selected" value="0">PieChart</option>
                                <option value="1">BarChart</option>
                        </select>
                    </div>

                    <div id="chart19" class="chart" data-dataType="score" data-filter='highest'></div>
                    
                    <div class="row">
                        <button class="nav-link btn red breakdown">Breakdown</button>
                    </div>
                </div>
                <!-- chart -->

                <!-- chart -->
                <div class="col-lg-4 chartSection">
                    <h4>Lowest Scores</h4>
                    <h3>Total: <?php echo $totalLowestScores; ?> scores</h3>

                    <div class="col chartfilters">
                        <select class="form-control chart2-dropdown" name="chartType">
                                <option selected="selected" value="0">PieChart</option>
                                <option value="1">BarChart</option>
                        </select>
                    </div>

                    <div id="chart20" class="chart" data-dataType="score" data-filter='lowest'></div>

                    <div class="row">
                        <button class="nav-link btn red breakdown">Breakdown</button>
                    </div>
                </div>
                <!-- chart -->

                <!-- chart -->
                <div class="col-lg-4 chartSection">
                    <h4>Winning Submissions</h4>
                    <h3>Total: <?php echo $totalWinningScores; ?> scores</h3>

                    <div class="col chartfilters">
                        <select class="form-control chart3-dropdown" name="chartType">
                                <option selected="selected" value="0">PieChart</option>
                                <option value="1">BarChart</option>
                        </select>
                    </div>

                    <div id="chart21" class="chart" data-dataType="score" data-filter='winning'></div>

                    <div class="row">
                        <button class="nav-link btn red breakdown">Breakdown</button>
                    </div>

                </div>
                <!-- chart -->

                </div>
                
                <div class="col dashboardHide" >
                <br>
                <hr>
                <br>
                <h3>Breakdown: </h3>
                <div class="row dashboard">
                    <div class="col">
                        <div class="filter_div"></div>
                        <div class="chart_div"></div>
                    </div>
                    
                    <div class="col table">
                        <div class="table_div"></div>
                    </div>
                    
                </div>
                </div>

            </div>

        </div>

    </div>

</div>