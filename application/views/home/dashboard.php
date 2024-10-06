<content class="content">
   <div class="container">
      <h1>Dashboard</h1>
      <div class="dashicons">
         <center><a href="#"><img src="https://dg7ltaqbp10ai.cloudfront.net/fit-in/1593787669-DashButtoncreategames.png" alt="Create Games"></a><a href="#"><img src="https://dg7ltaqbp10ai.cloudfront.net/fit-in/1593787687-DashButtonplaygames.png" alt="Play Games"></a><a href="#"><img src="https://dg7ltaqbp10ai.cloudfront.net/fit-in/1593787738-DashButtonmanagegames.png" alt="Manage Games"></a><a href="#"><img src="https://dg7ltaqbp10ai.cloudfront.net/fit-in/1596736745-DashButtonMangFund.png" alt="Manage Fundraisers"></a><a href="#"><img src="https://dg7ltaqbp10ai.cloudfront.net/fit-in/1593787789-DashButtonbuycredits.png" alt="Buy Credits"></a></center>
      </div>
      <br><br>
      <h2 class="text-center">ANALYZING YOUR IMPACT <i class="fas fa-chart-pie"></i></h2>
      <br>
      <div class="widgetstyle">
         <div class="row">
            <div class="col-lg-4 text-center">
               <h4>Funds raised by games that you have played.</h4>
               <h3>Total: $12,345</h3>
               <a href="#" class="analymore">Play more <i class="fas fa-chevron-right"></i></a>
               <div id="piechart" class="chart"></div>
               <script type="text/javascript">
                  // Load google charts
                  google.charts.load('current', {'packages':['corechart']});
                  google.charts.setOnLoadCallback(drawChart);
                  
                  // Draw the chart and set the chart values
                  function drawChart() {
                    var data = google.visualization.arrayToDataTable([
                    ['Funds', 'Amount'],
                    ['Charities', 8],
                    ['Projects', 2],
                    ['Causes', 4]
                  ]);
                  
                    // Optional; add a title and set the width and height of the chart
                    var options = {colors: ['#eb206d', '#490f7e', '#187d53']};
                  
                    // Display the chart inside the <div> element with id="piechart"
                    var chart = new google.visualization.PieChart(document.getElementById('piechart'));
                    chart.draw(data, options);
                  }
               </script>
            </div>
            <div class="col-lg-4 text-center">
               <h4>Funds raised by fundraisers you created.</h4>
               <h3>Total: $1,325</h3>
               <a href="#" class="analymore">Manage Fundraisers <i class="fas fa-chevron-right"></i></a>
               <div id="piechart2" class="chart"></div>
               <script type="text/javascript">
                  // Load google charts
                  google.charts.load('current', {'packages':['corechart']});
                  google.charts.setOnLoadCallback(drawChart);
                  
                  // Draw the chart and set the chart values
                  function drawChart() {
                    var data = google.visualization.arrayToDataTable([
                    ['Funds', 'Amount'],
                    ['Charities', 2],
                    ['Projects', 7],
                    ['Causes', 9]
                  ]);
                  
                    // Optional; add a title and set the width and height of the chart
                    var options = {colors: ['#eb206d', '#490f7e', '#187d53']};
                  	
                  
                    // Display the chart inside the <div> element with id="piechart2"
                    var chart = new google.visualization.PieChart(document.getElementById('piechart2'));
                    chart.draw(data, options);
                  }
               </script>
            </div>
            <div class="col-lg-4 text-center">
               <h4 class="text-center">Funds raised by games you created.</h4>
               <h3>Total: $1,725</h3>
               <a href="#" class="analymore">Create more <i class="fas fa-chevron-right"></i></a>
               <div id="piechart3" class="chart"></div>
               <script type="text/javascript">
                  // Load google charts
                  google.charts.load('current', {'packages':['corechart']});
                  google.charts.setOnLoadCallback(drawChart);
                  
                  // Draw the chart and set the chart values
                  function drawChart() {
                    var data = google.visualization.arrayToDataTable([
                    ['Funds', 'Amount'],
                    ['Charities', 1],
                    ['Projects', 100],
                    ['Causes', 3]
                  ]);
                  
                    // Optional; add a title and set the width and height of the chart
                    var options = {colors: ['#eb206d', '#490f7e', '#187d53']};
                  	
                  
                    // Display the chart inside the <div> element with id="piechart2"
                    var chart = new google.visualization.PieChart(document.getElementById('piechart3'));
                    chart.draw(data, options);
                  }
               </script>
            </div>
         </div>
      </div>
      <br><br>
      <div class="widgetstyle">
         <h2 class="text-center">RECENT ACTIVITY <i class="fas fa-history"></i></h2>
         <br>
         <div class="row">
            <div class="col-lg-6">
               <h3>Your Activity</h3>
               <ul class="list-group">
                  <li class="list-group-item recentproject"><span class="badge badge-pill"><i class="fas fa-lightbulb"></i></span> Played <a href="https://dev.winwinlabs.com/games/show/play/Puzzle-button-test">Puzzle Button Test</a> supporting <a href="#">FRC Team 7737</a></li>
                  <li class="list-group-item recentcharity"><span class="badge badge-pill"><i class="fas fa-hand-holding-heart"></i></span> Added <a href="#">American Heart Association</a> to your <a href="#">supported fundraisers</a> list.</li>
                  <li class="list-group-item recentcause"><span class="badge badge-pill"><i class="fa fa-globe"></i></span> Created a new challenge game called <a href="#">Test Quiz</a> supporting <a href="#">Feed America</a></li>
                  <li class="list-group-item recentcause"><span class="badge badge-pill"><i class="fa fa-globe"></i></span>  Played <a href="#">Fun Gamer Challenge</a> supporting <a href="#">Feed America</a></li>
                  <li class="list-group-item recentproject"><span class="badge badge-pill"><i class="fas fa-hand-holding-heart"></i></span> Played <a href="#">Awesome Puzzle</a> supporting <a href="#">FRC Team 7737</a>.</li>
                  <li class="list-group-item recentcharity"><span class="badge badge-pill"><i class="fas fa-hand-holding-heart"></i></span> Played <a href="#">Bling Puzzle</a> supporting <a href="#">American Heart Association</a>.</li>
                  <li class="list-group-item recentproject"><span class="badge badge-pill"><i class="fas fa-lightbulb"></i></span> Created a new 2048 game called <a href="#">Test 2048 Game</a> supporting <a href="#">FRC Team 7737</a></li>
               </ul>
               <a href="#" class="loadrecent text-center">LOAD MORE <i class="fas fa-chevron-down"></i></a>
            </div>
            <div class="col-lg-6">
               <h3>Your Supporters</h3>
               <ul class="list-group">
                  <li class="list-group-item recentcause"><span class="badge badge-pill"><i class="fa fa-globe"></i></span> dashd121 played your game <a href="#">Geek Power Puzzle</a> supporting <a href="#">Dash Car</a></li>
                  <li class="list-group-item recentproject"><span class="badge badge-pill"><i class="fas fa-lightbulb"></i></span> Username2739 created a game called <a href="#">Challenge Test</a> supporting your project <a href="#">Working Test</a></li>
                  <li class="list-group-item recentcharity"><span class="badge badge-pill"><i class="fas fa-hand-holding-heart"></i></span> User279 created a game called <a href="#">Fun Challenge</a> supporting <a href="#">Dash's Charity</a></li>
                  <li class="list-group-item recentproject"><span class="badge badge-pill"><i class="fas fa-lightbulb"></i></span> User3492 created a game called <a href="#">Puzzle Fun</a> supporting your project <a href="#">Working Test</a></li>
                  <li class="list-group-item recentcause"><span class="badge badge-pill"><i class="fa fa-globe"></i></span> User382809 played your game <a href="#">Beautiful Puzzle</a> supporting <a href="#">Dash Car</a></li>
               </ul>
               <a href="#" class="loadrecent text-center">LOAD MORE <i class="fas fa-chevron-down"></i></a>
            </div>
         </div>
      </div>
      <br><br><br>
   </div>
</content>