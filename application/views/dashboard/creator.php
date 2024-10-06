<div class="tab-pane fade <?php echo ($tab == 2) ? "show active" : ""; ?>" id="creator" role="tabpanel" aria-labelledby="creator-tab">

    <ul class="nav nav-tabs" id="chartTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="funds-tab" data-toggle="tab" href="#funds" role="tab" aria-controls="funds" aria-selected="true">Funds</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="supporters-tab" data-toggle="tab" href="#supporters" role="tab" aria-controls="supporters" aria-selected="false">Supporters</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="traffic-tab" data-toggle="tab" href="#traffic" role="tab" aria-controls="traffic" aria-selected="false">Traffic</a>
        </li>
    </ul>
    <div class="tab-content" id="chartContent">
        <div class="tab-pane fade show active" id="funds" role="tabpanel" aria-labelledby="funds-tab">

            <div class="widgetstyle">

                <?php include 'tab-filter.php'; ?>

                <div class="row">
                <!-- chart -->
                <div class="col-lg-4 chartSection">
                    <h4>Funds Raised: Overall</h4>
                    <h3>Total: $<?php echo number_format($overall, 2); ?> </h3>

                    <div class="row chartfilters">
                        <div class="col">
                            <select class="form-control chart2-dropdown" name="chartType">
                                    <option selected="selected" value="0">PieChart</option>
                                    <option value="1">BarChart</option>
                            </select>
                        </div>
                    </div>

                    <div id="chart1" class="chart" data-dataType="raised" data-filter='overall'></div>
                    
                    <div class="row">
                        <button class="nav-link btn red breakdown">Breakdown</button>
                    </div>
                </div>
                <!-- chart -->

                <!-- chart -->
                <div class="col-lg-4 chartSection">
                    <h4>Funds Raised: Fundraisers You Created</h4>
                    <h3>Total: $<?php echo number_format($fundraiser, 2); ?></h3>

                    <div class="row chartfilters">
                        <div class="col">
                            <select class="form-control chart2-dropdown" name="chartType">
                                    <option selected="selected" value="0">PieChart</option>
                                    <option value="1">BarChart</option>
                            </select>
                        </div>
                    </div>

                    <div id="chart2" class="chart" data-dataType="raised" data-filter='fundraiser'></div>

                    <div class="row">
                        <button class="nav-link btn red breakdown">Breakdown</button>
                    </div>
                </div>
                <!-- chart -->

                <!-- chart -->
                <div class="col-lg-4 chartSection">
                    <h4>Funds Raised: Games You Created</h4>
                    <h3>Total: $<?php echo number_format($game, 2); ?></h3>

                    <div class="row chartfilters">
                        <div class="col">
                            <select class="form-control chart2-dropdown" name="chartType">
                                    <option selected="selected" value="0">PieChart</option>
                                    <option value="1">BarChart</option>
                            </select>
                        </div>
                    </div>

                    <div id="chart3" class="chart" data-dataType="raised" data-filter='game'></div>

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

        <div class="tab-pane fade" id="supporters" role="tabpanel" aria-labelledby="supporters-tab">

            <div class="widgetstyle">

                <?php include 'tab-filter.php'; ?>
                
                <div class="row">
                <!-- chart -->
                <div class="col-lg-4 chartSection">
                    <h4># of Games Supporting Your Fundraisers</h4>
                    <h3>Total: <?php echo $allSupportingGames; ?> games </h3>

                    <div class="row chartfilters">
                        <div class="col">
                            <select class="form-control chart4-dropdown" name="chartType">
                                    <option selected="selected" value="0">PieChart</option>
                                    <option value="1">BarChart</option>
                            </select>
                        </div>
                    </div>

                    <div id="chart4" class="chart" data-dataType="supporters" data-filter='all'></div>
                    
                    <div class="row">
                        <button class="nav-link btn red breakdown">Breakdown</button>
                    </div>
                </div>
                <!-- chart -->

                <!-- chart -->
                <div class="col-lg-4 chartSection">
                    <h4># of Active Games Supporting Your Fundraisers</h4>
                    <h3>Total: <?php echo $allActiveSupportingGames; ?> games</h3>

                    <div class="row chartfilters">
                        <div class="col">
                            <select class="form-control chart5-dropdown" name="chartType">
                                    <option selected="selected" value="0">PieChart</option>
                                    <option value="1">BarChart</option>
                            </select>
                        </div>
                    </div>

                    <div id="chart5" class="chart" data-dataType="supporters" data-filter='active'></div>

                    <div class="row">
                        <button class="nav-link btn red breakdown">Breakdown</button>
                    </div>
                </div>
                <!-- chart -->

                <!-- chart -->
                <div class="col-lg-4 chartSection">
                    <h4># of Games Supporting <span name="fundraiser"><?php echo $userFundraisers[0]['name']; ?></span></h4>
                    <h3>Total: <span name="total"><?php echo $allOtherSupportingGames; ?></span> games</h3>

                    <div class="row chartfilters">
                        <div class="col">
                            <select class="form-control pull-left" name="fundraiserName">
                                <?php for ($i = 0; $i < count($userFundraisers); $i++) { ?>
                                    <?php if ($i == 0) { ?>
                                        <option selected="selected" value="<?php echo $userFundraisers[$i]['id']; ?>"><?php echo $userFundraisers[$i]['name'];  ?></option>
                                    <?php } else { ?>
                                        <option value="<?php echo $userFundraisers[$i]['id'];  ?>"><?php echo $userFundraisers[$i]['name'];  ?></option> 
                                    <?php } ?>
                                <?php } ?>
                            </select>

                            <select class="form-control chart6-dropdown" name="chartType">
                                <option selected="selected" value="0">PieChart</option>
                                <option value="1">BarChart</option>
                            </select>
                        </div>
                    </div>

                    <div id="chart6" class="chart" data-dataType="supporters" data-filter='other'></div>

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

        <div class="tab-pane fade" id="traffic" role="tabpanel" aria-labelledby="traffic-tab">

            <div class="widgetstyle">

                <?php include 'tab-filter.php'; ?>

                <div class="row">
                    <div class="col-lg-4 chartSection">
                    <h4>Traffic: Your Games and Fundraisers</h4>
                    <h3>Past 7 Days: <?php echo $totalActivity[0]['traffic']; ?> users</h3>
                    <div class="row chartfilters">
                        <div class="col">
                            <select class="form-control chart7-dropdown" name="chartType">
                                    <option selected="selected" value="1">BarChart</option>
                                    <option value="0">PieChart</option>
                            </select>
                        </div>
                    </div>
                    
                    <div id="chart7" class="chart" data-dataType="engagement" data-filter='game' data-game_id='all' data-charity_id='all'></div>

                    <div class="row">
                        <button class="nav-link btn red breakdown">Breakdown</button>
                    </div>

                    </div>
                    <div class="col-lg-4 chartSection">
                    <h4>Traffic to Games with Your Fundraisers</h4>
                    <h3>Past 7 Days: <?php echo $totalActivity[0]['traffic']; ?> users</h3>
                    <div class="row chartfilters">
                        <div class="col">
                            <select class="form-control chart8-dropdown" name="chartType">
                                    <option selected="selected" value="1">BarChart</option>
                                    <option value="0">PieChart</option>
                            </select>
                        </div>
                    </div>

                    <div id="chart8" class="chart" data-dataType="engagement" data-filter='fundraiser' data-game_id='none' data-charity_id='all'></div>

                    <div class="row">
                        <button class="nav-link btn red breakdown">Breakdown</button>
                    </div>

                    </div>
                    <div class="col-lg-4 chartSection">
                    <h4>Traffic to Your Games</h4>
                    <h3>Past 7 Days: <?php echo $totalActivity[0]['traffic']; ?> users</h3>

                    <div class="row chartfilters">
                        <div class="col">
                            <select class="form-control chart9-dropdown" name="chartType">
                                    <option selected="selected" value="1">BarChart</option>
                                    <option value="0">PieChart</option>
                            </select>
                        </div>
                    </div>

                    <div id="chart9" class="chart" data-dataType="engagement" data-filter='fundraiser' data-game_id='all' data-charity_id='none'></div>

                    <div class="row">
                        <button class="nav-link btn red breakdown">Breakdown</button>
                    </div>

                    </div>
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