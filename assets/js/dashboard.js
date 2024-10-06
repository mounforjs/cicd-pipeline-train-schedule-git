$(document).ready( function() {
    var activeAjax = new Map();
    var drawnCharts = new Map();

    var selfNotificationCount = 0;
    var supporterNotificationCount = 0;

    google.charts.load('current', {
        packages: ['bar', 'corechart', 'table', 'controls']
    });

    //get first visible charts and render
    var initTab = $("#chartTabCategory a[class*='nav-link'][class*='active']");
    if ($(initTab).attr("href") != "#prizes") {
        var target = $(initTab).attr("href");
        activeTab = $(target).find(".tab-content div[class*='tab-pane'][class*='show']");
        setupVisibleCharts();
    }


    //chart setup
    function getVisibleCharts() {
        var charts = $(activeTab).find("div[id*='chart']").toArray();

        return charts;
    }

    function setupChart(chart, element=null) {
        google.charts.setOnLoadCallback(function () {
            configureChart(chart, element);
        });
    }

    function setupVisibleCharts(resize=false) {
        var charts = getVisibleCharts();

        if (resize) {
            cancelAjax();
        } 

        forEachChart(charts);
    }

    function forEachChart(charts) {
        charts.forEach(element => {
            if ($(element).find("svg").length == 0) {
                google.charts.setOnLoadCallback(function () {
                    configureChart(element);
                });
            } else {
                if ($(element).closest(".dashboard").length > 0) {
                    var num = $(element).attr("id").slice(6, $(element).attr("id").length);
                    drawDashboard(num, "", true);
                } else {
                    redrawChart(element);
                }
            }
        });
    }

    function configureChart(chart, element=null) {
        var chartName = $(chart).attr("id");

        var postData = $(chart).data();

        var chartFilters = $(chart).parent('div').find("div[class*='chartfilters']");
        var chartType = parseInt($(chartFilters).find('select[name*="chartType"] option:selected').val());
        var fundraiserID = parseInt($(chartFilters).find('select[name*="fundraiserName"] option:selected').val());
        if (fundraiserID !== undefined) {
            postData.fundraiser = fundraiserID;
        }

        var req = $.ajax({
            type: 'GET',
            url: window.origin + "/dashboard/getDashboardCharts",
            data: postData,
            dataType: "json",
            beforeSend: function() {
                $(chart).prepend('<div class="loader"><div class="imageLoader"></div></div>');
            },
            complete: function() {
                $(chart).find(".loader").remove();
            },
            success: function(data) {
                //console.log("finished loading chart " + chartName);
                activeAjax.delete(chartName);

                if (element != null) {
                    if (data.length > 1) {
                        var keys = Object.keys(data[0]);
                        element.text(data.length-1);
                    } else {
                        element.text(0);
                    }
                }

                data = configureData(data);
                
                if (data.length == 1 && chartType === 1) {
                    chartType = 0;
                } else {
                    data = new google.visualization.arrayToDataTable(data);  
                }

                var options = configureOptions(chartType);
    
                drawChart(data, options, chartType, chartName);
            },
            error: function(req, err){ 
                //console.log('error:' + err); 
                activeAjax.delete(chartName);
            }
         });  
         
        activeAjax.set(chartName, req);
    }
    //chart setup

    function cancelAjax() {
        for (let [key, req] of activeAjax.entries()) {
            req.abort();

            //console.log("cancelled ajax");
            activeAjax.delete(key);
        }
    }

    function configureData(data) {
        var configuredData = [];

        for(var i in data) {
            var keys = Object.keys(data[i]);

            var first = data[i][keys[0]];
            var second = parseInt(data[i][keys[1]]);
            if (isNaN(second)) {
                second = keys[1];
            }

            configuredData.push([first, second]);
        }

        return configuredData;
    }

    //google chart setup
    
    function configureOptions(chartType, min, max) {
        var options;

        switch (chartType) {
            case 0: //piechart
                var options = {legend: {position: "bottom"},
                                sliceVisibilityThreshold:0,
                                pieSliceText: 'value-and-percentage',
                                chartArea:{left:0,top:10,width:"90%",height:"75%"},
                                };
                break;
            case 1: //columnchart
                var options = {legend: {position: "bottom"}, sliceVisibilityThreshold:0, 
                            };
                break;
        }

        return options;
    }

    function drawChart(data, options, chartType, chartName) {
        var googleChartType = "";

        switch (chartType) {
            case 0: //piechart
                googleChartType = "PieChart";
                break;
            case 1: //columnchart
                googleChartType = "ColumnChart";
                break;
        }

        var chart = new google.visualization.ChartWrapper({
            chartType: googleChartType,
            dataTable: data,
            options: options,
            containerId: chartName
        });

        chart.draw();

        drawnCharts.set(chartName, chart);
    }

    function prepareChartDashboard(data, options, chartType, chartName) {
        var googleChartType = "";

        switch (chartType) {
            case 0: //piechart
                googleChartType = "PieChart";
                break;
            case 1: //columnchart
                googleChartType = "ColumnChart";
                break;
        }

       var chart = new google.visualization.ChartWrapper({
            chartType: googleChartType,
            dataTable: data,
            options: options,
            containerId: chartName
        });

        return chart;
    }

    function drawDashboard(chartNum, title, redraw=false) {
        var chartName = "chart" + chartNum;
        var chartFilters = $("#" + chartName).parent('div').find("div[class*='chartfilters']");
        var chartType = parseInt($(chartFilters).find('select[name*="chartType"] option:selected').val());

        var dashboard_id = "dashboard_" + chartNum;
        var filter_id = "filter_" + chartNum;
        var chart_div = "chart_" + chartNum;
        var table_div = "table_" + chartNum;
        $(activeTab).find(".dashboard").attr("id", dashboard_id);
        $(activeTab).find(".dashboard .filter_div").attr("id", filter_id);
        $(activeTab).find(".dashboard .chart_div").attr("id", chart_div);
        $(activeTab).find(".dashboard .table_div").attr("id", table_div);

        var postData = $("#" + chartName).data();

        var req = $.ajax({
            type: 'GET',
            url: window.origin + "/dashboard/getDashboardCharts",
            data: postData,
            dataType: "json",
            beforeSend: function() {
                if (!redraw) {
                    $(".dashboard:visible").prepend('<div class="loader"><div class="imageLoader"></div></div>');
                }
            },
            complete: function() {
                if (!redraw) {
                    $(".dashboard .loader").remove();
                }
            },
            success: function(data) {
                //console.log("finished loading chart " + chartName);
                activeAjax.delete(chartName);

                data = configureData(data);

                dataTable = new google.visualization.arrayToDataTable(data);  
                var options = configureOptions(chartType);
    
                var chart = prepareChartDashboard(dataTable, options, chartType, chart_div);

                var dashboard = new google.visualization.Dashboard(document.getElementById(dashboard_id));
                var slider = new google.visualization.ControlWrapper({
                    'controlType': 'NumberRangeFilter',
                    'containerId': filter_id,
                    'options': {
                        'filterColumnLabel': data[0][1]
                    }
                });

                dashboard.bind(slider, chart);
                dashboard.draw(dataTable);

                drawTable(table_div, dataTable);
                
                if (!redraw) {
                    $(".dashboard:visible").parent().find("h3:last").text("Breakdown: " + title);
                }

                drawnCharts.set(chartName, chart);
            },
            error: function(req, err){ 
                //console.log('error:' + err); 
                activeAjax.delete(chartName);
            }
        });  

        activeAjax.set(chartName, req);
    }

    function drawTable(table_div, data) {
        var table = new google.visualization.Table(document.getElementById(table_div));

        table.draw(data, {showRowNumber: true, width: '100%', height: '100%'});
    }

    function redrawChart(currentChart) {
        var name = $(currentChart).attr("id");
        if (drawnCharts.has(name)) {
            var chart = drawnCharts.get(name);
            chart.draw();
        }
    }
    //google chart setup

    //resize
    $(window).resize(function() {
        if(this.resizeTO) clearTimeout(this.resizeTO);
        this.resizeTO = setTimeout(function() {
            $(this).trigger('resizeEnd');
        }, 250);
    });

    $(window).on('resizeEnd', function() {
        setupVisibleCharts(true);
    });
    //resize

    //refresh
    $("button[name='refresh']").on("click", function() {
        var btn = $(this);
        $(btn).addClass("disabled");
        $(btn).prop("disabled", true);

        var visible = getVisibleCharts();
        visible.forEach( element => {
            if ($(element).closest(".dashboard").length > 0) {
                var num = $(element).attr("id").slice(6, $(element).attr("id").length);
                drawDashboard(num, "");
            } else {
                google.charts.setOnLoadCallback(function () {
                    configureChart(element);
                });
            }
        });

        setTimeout(function() {
            $(btn).removeClass("disabled");
            $(btn).attr("disabled", false);
        }, 1000);
    });

    //on click tabs
    $('#chartTabCategory .nav-item a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        var content = $(this).attr("href")
        activeTab = $(content + " .tab-content div[class*='tab-pane'][class*='show']");
        setupVisibleCharts();

        var table = $(activeTab).find("table:visible*[id]");
        $(table).DataTable().columns.adjust();
    });
    
    $('#chartTabs .nav-item a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        activeTab = $("#chartContent div[class*='tab-pane'][class*='show']");
        setupVisibleCharts();
    });

    $('#playerChartTabs .nav-item a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        activeTab = $("#playerChartContent div[class*='tab-pane'][class*='show']");
        setupVisibleCharts();
    });

    $('#prizeChartTabs .nav-item a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        activeTab = $("#prizeChartContent div[class*='tab-pane'][class*='show']");
        var table = $("#prizeChartContent").find("table:visible*[id]");
        $(table).DataTable().columns.adjust();
    });


    //search for charts
    $('input[class*="searchBarInput"]').on('keyup', function (e) {
        var search = $(this).val().toLowerCase();
        var visible = getVisibleCharts();

        if (search == "") {
            showAllCharts(visible);
        } else {
            visible.forEach( element => {
                var parent = $(element).parent();
                var name = $(parent).find("h4").text().toLowerCase();

                var dashboard = $(parent).closest(".dashboard").length;

                if (name.includes(search)) {
                    $(parent).show();
                    redrawChart(element);
                } else {
                    if (dashboard == 0) {
                        $(parent).hide();
                    }
                }
            });
        }
    });

    function showAllCharts(charts) {
        charts.forEach( element => {
            $(element).parent().show();
            redrawChart(element);
        });
    }
    //search for charts

    $("a[class*='loadrecent']").on("click", function() {
        var name = $(this).attr("name");
        var appendTo = $(this).parent().find("ul");

        if (name == "self") {
            selfNotificationCount += 7;
            getMoreUserNotifications(selfNotificationCount, appendTo);
        } else {
            supporterNotificationCount += 7;
            getMoreSupporterNotifications(supporterNotificationCount, appendTo);
        }
    });

    $("select[class*='chart']").on("change", function() {
        var val = $(this).val();
        $(this).find("option[selected='selected']").removeAttr("selected");
        $(this).find("option[value="+val+"]").attr("selected", "selected");

        var chart = $(this).parent().parent().parent().find("div[id*='chart']");
        setupChart(chart);

        var chartName = $(chart).attr("id");
        drawDashboard(chartName.slice(5, chartName.length), "");
    })

    $("select[name*='fundraiserName']").on("change", function() {
        var val = $(this).val();

        $(this).find("option[selected='selected']").removeAttr("selected");

        $(this).find("option[value="+val+"]").attr("selected", "selected");
        var name = $(this).find("option[value="+val+"]").text();

        $(this).parent().parent().parent().find("span[name*='fundraiser']").text(name);
        var total = $(this).parent().parent().parent().find("span[name*='total']");

        var chart = $(this).parent().parent().parent().find("div[id*='chart']");
        closeBreakdown();
        setupChart(chart,total);
    })

    $(".breakdown").on("click", function() {
        var chart = $(this).parent().parent().find("div[id*='chart']");
        var chartName = $(chart).attr("id");
        var title = $(this).parent().parent().find("h4").text();

        if ($(chart).find("text:contains('No data')").length == 0) {
            toggleDashboard(chartName.slice(5, chartName.length), title);
        } else {
            showSweetAlert("Cannot break down empty table chart.", "Oops!", 'info')
        }
    })

    function toggleDashboard(breakDownChartName, title) {
        var dashboard = $(activeTab).find(".dashboard").parent();

        var currentBreakdown = $(activeTab).find(".dashboard").find(".chart_div").attr("id");

        if ($(dashboard).css("visibility") === "visible" && currentBreakdown.includes(breakDownChartName)) {
            $(dashboard).removeClass("dashboardShow");
            $(dashboard).addClass("dashboardHide");
        } else {
            $(dashboard).removeClass("dashboardHide");
            $(dashboard).addClass("dashboardShow");
            drawDashboard(breakDownChartName, title);
        }
    };

    function closeBreakdown() {
        var dashboard = $(activeTab).find(".dashboard").parent();
        $(dashboard).removeClass("dashboardShow");
        $(dashboard).addClass("dashboardHide");
    }
    
    //gets super convoluted down here
    function getMoreUserNotifications(count, element) {
        $.ajax({
            type: 'GET',
            url: window.origin + "/dashboard/getUserActivity",
            data: {limit: count},
            dataType: "json",
            success: function(data) {
                appendUserNotifications(data, element);
            },
            error: function(req, err){ 
                showSweetAlert("Could not load more.", 'Whoops!', 'error');
            }
         });  
    }

    function getMoreSupporterNotifications(count, element) {
        $.ajax({
            type: 'GET',
            url: window.origin + "/dashboard/getSupporterActivity",
            data: {limit: count},
            dataType: "json",
            success: function(data) {
                appendSupporterNotifications(data, element);
            },
            error: function(req, err){ 
                showSweetAlert("Could not load more.", 'Whoops!', 'error');
            }
         });  
    }

    function appendUserNotifications(data, element) {
        var url = window.origin;

        data.forEach(noti => {
            var notification = '<li class="notification"> <div class="notificationContent"> <div class="notificationIcon">';

            switch (noti.fundraise_type) {
                case "project":
                    notification += '<div class="recentproject"><center><i class="fas fa-lightbulb"></i></center> </div> </div>';
                    break;
                case "cause":
                    notification += '<div class="recentcause"><center><i class="fas fa-globe"></i></center> </div> </div>';
                    break;
                case "charity":
                    notification += '<div class="recentcharity"><center><i class="fas fa-hand-holding-heart"></i></center> </div> </div>';
                    break;
                case null:
                    notification += '<div class="recentunknown"><center><i class="fas fa-question"></i></center> </div> </div>';
                    break;
            }

            notification += '<div class="notificationIcon">';

            switch (noti.action) {
                case "play":
                    notification += '<div class="play"><center><i class="fa fa-play"></i></center> </div> </div> <div class="col">';
                    notification += "You played <a href='" + url + "games/show/play/" + noti.gameSlug + "'>" + noti.g_name + "</a>";

                    if (noti.charity_id != null) {
                        notification += " supporting <a href='" + url + "fundraisers/show/all/" + noti.charitySlug + "'>" + noti.c_name + "</a>.";
                    } else {
                        notification += ".";
                    }
                    break;
                case "create":
                    notification += '<div class="create"><center><i class="fa fa-pencil"></i></center> </div> </div> <div class="col">';
                    notification += "You created a new " + noti.gameType + " game called <a href='" + url + "games/show/play/" + noti.gameSlug + "'>" + noti.g_name + "</a>";
                                 
                    if (noti.charity_id != null) {
                        notification += " supporting <a href='" + url + "fundraisers/show/all/" + noti.charitySlug + "'>" + noti.c_name + "</a>.";
                    } else {
                        notification += ".";
                    }
                    break;
                case "add":
                    notification += '<div class="add"><center><i class="fa fa-plus"></i></center> </div> </div> <div class="col">';

                    if (noti.charity_id != null) {
                        notification += "You added <a href='" +  url + "fundraisers/show/all/" + noti.charitySlug + "'>" + noti.c_name + "</a> to your <a href='" + url + "fundraisers/show/supported" + "'>supported fundraisers list</a>.";
                    } else {
                        notification += "You added fundraiser to your <a href='" + url + "fundraisers/show/supported" + "'>supported fundraisers list</a>.";
                    }
                    break;  
                case "win":
                    notification += '<div class="win"><center><i class="fa fa-trophy"></i></center> </div> </div> <div class="col">';
                    notification += "You won <a href='" + url + "games/show/play/" + noti.gameSlug + "'>" + noti.g_name + "</a>";

                    if (noti.charity_id != null) {
                        notification += " supporting <a href='" + url + "fundraisers/show/all/" + noti.charitySlug + "'>" + noti.c_name + "</a>.";
                    } else {
                        notification += ".";
                    }
                    break;
                case "end":
                    notification += '<div class="end"><center><i class="fa fa-hourglass-end"></i></center> </div> </div> <div class="col">';
                    notification += "Your " + noti.gameType + " game called <a href='" + url + "games/show/play/" + noti.gameSlug + "'>" + noti.g_name + "</a>";
                                    
                    if (noti.charity_id != null) {
                        notification += " supporting <a href='" + url + "fundraisers/show/all/" + noti.charitySlug + "'>" + noti.c_name + "</a> has ended.";
                    } else {
                        notification += "has ended.";
                    }
                    break;
                case "publish":
                    notification += '<div class="publish"><center><i class="fa fa-book"></i></center> </div> </div> <div class="col">';
                    notification += "Your " + noti.gameType + " game called <a href='" + url + "games/show/play/" + noti.gameSlug + "'>" + noti.g_name + "</a>";
                                    
                    if (noti.charity_id != null) {
                        notification += " supporting <a href='" + url + "fundraisers/show/all/" + noti.charitySlug + "'>" + noti.c_name + "</a> has been published.";
                    } else {
                        notification += "has been published.";
                    }
                    break;
                case null:
                    notification += '<div class="question"><center><i class="fa fa-question"></i></center> </div> </div> <div class="col">';
                    notification += noti.Notes;
                    break;                 
            }

            notification += '</div> </div> </li>';

            $(element).append(notification);
        });
    }

    function appendSupporterNotifications(data, element) {
        var url = window.origin;

        data.forEach(noti => {
            var notification = "";

            switch (noti.fundraise_type) {
                case "project":
                    notification += '<div class="recentproject"><center><i class="fas fa-lightbulb"></i></center> </div> </div>';
                    break;
                case "cause":
                    notification += '<div class="recentcause"><center><i class="fas fa-globe"></i></center> </div> </div>';
                    break;
                case "charity":
                    notification += '<div class="recentcharity"><center><i class="fas fa-hand-holding-heart"></i></center> </div> </div>';
                    break;
                case null:
                    notification += '<div class="recentunknown"><center><i class="fas fa-question"></i></center> </div> </div>';
                    break;
            }

            switch (noti.action) {
                case "play":
                    notification += '<div class="play"><center><i class="fa fa-play"></i></center> </div> </div> <div class="col">';
                    notification += noti.username + " played <a href='" + url + "games/show/play/" + noti.gameSlug + "'>" + noti.g_name + "</a>";

                    if (noti.charity_id != null) {
                        notification += " supporting <a href='" + url + "fundraisers/show/all/" + noti.charitySlug + "'>" + noti.c_name + "</a>.";
                    } else {
                        notification += ".";
                    }
                    break;
                case "create":
                    notification += '<div class="create"><center><i class="fa fa-pencil"></i></center> </div> </div> <div class="col">';
                    notification += noti.username + " created a new " + noti.gameType + " game called <a href='" + url + "games/show/play/" + noti.gameSlug + "'>" + noti.g_name + "</a>";
                                 
                    if (noti.charity_id != null) {
                        notification += " supporting <a href='" + url + "fundraisers/show/all/" + noti.charitySlug + "'>" + noti.c_name + "</a>.";
                    } else {
                        notification += ".";
                    }
                    break;
                case "add":
                    notification += '<div class="add"><center><i class="fa fa-plus"></i></center> </div> </div> <div class="col">';

                    if (noti.charity_id != null) {
                        notification += noti.username + " added <a href='" +  url + "fundraisers/show/all/" + noti.charitySlug + "'>" + noti.c_name + "</a> to your <a href='" + url + "fundraisers/show/supported" + "'>supported fundraisers list</a>.";
                    } else {
                        notification += noti.username + " added fundraiser to your <a href='" + url + "fundraisers/show/supported" + "'>supported fundraisers list</a>.";
                    }
                    break;  
                case "win":
                    notification += '<div class="win"><center><i class="fa fa-trophy"></i></center> </div> </div> <div class="col">';
                    notification += noti.username + " won <a href='" + url + "games/show/play/" + noti.gameSlug + "'>" + noti.g_name + "</a>";

                    if (noti.charity_id != null) {
                        notification += " supporting <a href='" + url + "fundraisers/show/all/" + noti.charitySlug + "'>" + noti.c_name + "</a>.";
                    } else {
                        notification += ".";
                    }
                    break;
                case "end":
                    notification += '<div class="end"><center><i class="fa fa-hourglass-end"></i></center> </div> </div> <div class="col">';
                    notification += noti.username + "'s " + noti.gameType + " game called <a href='" + url + "games/show/play/" + noti.gameSlug + "'>" + noti.g_name + "</a>";
                                    
                    if (noti.charity_id != null) {
                        notification += " supporting <a href='" + url + "fundraisers/show/all/" + noti.charitySlug + "'>" + noti.c_name + "</a> has ended.";
                    } else {
                        notification += "has ended.";
                    }
                    break;
                case "publish":
                    notification += '<div class="publish"><center><i class="fa fa-book"></i></center> </div> </div> <div class="col">';
                    notification += noti.username + "'s " + noti.gameType + " game called <a href='" + url + "games/show/play/" + noti.gameSlug + "'>" + noti.g_name + "</a>";
                                    
                    if (noti.charity_id != null) {
                        notification += " supporting <a href='" + url + "fundraisers/show/all/" + noti.charitySlug + "'>" + noti.c_name + "</a> has been published.";
                    } else {
                        notification += "has been published.";
                    }
                    break;
                case null:
                    notification += '<div class="question"><center><i class="fa fa-question"></i></center> </div> </div> <div class="col">';
                    notification += noti.Notes;
                    break;                 
            }

            notification += '</div> </div> </li>';

            $(element).append(notification);
        });
    }

    $(document).on("click", ".shipinfo", function(e) {
        var id = $(e.target).data("id");

        var tableID = $(this).closest('table').attr("id");
        table = $("#"+tableID).DataTable();
        
        var tr = $(this).closest('tr');
        var row = table.row(tr);

        if ( row.child.isShown() ) {
            row.child.hide();
            tr.removeClass('shown');
        } else {
            if (row.data().address_1 != undefined) {
                row.child(formatShipInfo(id, row.data())).show();
                $(row.child()[0]).css("position", "relative");
                tr.addClass('shown');
            } else {
                $.ajax({
                    url: '/dashboard/getShippingInfo',
                    method: 'GET',
                    data: {id: id},
                    beforeSend: function () {
                        $(e.target).addClass("disabled");
                        $('.dataTables_processing', $("#"+tableID).closest('.dataTables_wrapper')).show();
                    },
                    complete: function () {
                        $(e.target).removeClass("disabled");
                        $('.dataTables_processing', $("#"+tableID).closest('.dataTables_wrapper')).hide();
                    },
                    success: function (data) {
                        data = JSON.parse(data);
        
                        if (data.status == "success") {
                            row.child(formatShipInfo(id, data.info)).show();
                            $(row.child()[0]).css("position", "relative");
                            tr.addClass('shown');
                        } else {
                            row.child.hide();
                            tr.removeClass('shown');
                        }
                    }
                });
            }
        }
    });

    $(document).on("click", ".updateTracking", function(e) {
        var selectedProof = $("select[name='processingProof']").val();

        var td = $(e.target).closest("td");
        var id = $(this).data('id');

        if (selectedProof == 0) {
            var provider = $(this).parent().find("select[name='shippingProvider']").val();
            var tracking = $(this).parent().find("input[name='trackingNum']").val();

            if (provider != null && tracking != null) {
                var text = "Are these correct?\n" + provider + " - " + tracking + "";
                var title = "Confirm Tracking?";
                showSweetConfirm(text, title, "warning", function(confirmed) {
                    if (!confirmed) {
                        e.preventDefault();
                    } else {
                        $.ajax({
                            url: '/dashboard/updateTracking',
                            method: 'POST',
                            data: {id: id, proof: selectedProof, provider: provider, num: tracking},
                            beforeSend: function () {
                                $(e.target).closest('table').prev().removeClass("d-none");
                                $(e.target).addClass('disabled');
                                $(td).find(".cancelTracking").addClass('disabled');
                                $("a.shipinfo[data-id='"+ id +"']").addClass('disabled');
                            },
                            complete: function () {
                                $(e.target).closest('table').prev().addClass("d-none");
                                $(e.target).removeClass('disabled');
                                $(td).find(".cancelTracking").removeClass('disabled');
                                $("a.shipinfo[data-id='"+ id +"']").removeClass('disabled');
                            },
                            success: function (data) {
                                data = JSON.parse(data);
                
                                if (data.status == "success") {
                                    var table = $(e.target).closest('table.dataTable').DataTable();
                                    var tr = $(e.target).closest('table').closest("tr").prev();
                                    var row = table.row(tr);

                                    row.data().shipping_provider = provider;
                                    row.data().tracking_num = tracking;

                                    var p = "<span class='provider'>" + provider + "</span>";
                                    var t = "<span class='num'>" + tracking + "</span";

                                    if (!$("a.shipinfo[data-id='"+ id +"']").hasClass("blue")) {
                                        $("a.shipinfo[data-id='"+ id +"']").text("Processed!");
                                        $("a.shipinfo[data-id='"+ id +"']").addClass("blue");
                                        $("a.shipinfo[data-id='"+ id +"']").removeClass("orange");
                                    }

                                    $(td).find(".editTracking").removeClass("d-none");
                                    $(e.target).addClass("d-none");
                                    $(td).find(".cancelTracking").addClass("d-none");

                                    $(td).find("span.tracking").html(p + " - " + t);
                                    $(td).find("span.tracking").removeClass("d-none");

                                    $(td).find("div").remove();
                                } else {
                                    showSweetAlert("Try again later!", "Whoops!", 'error')
                                }
                            }
                        });
                    }     
                }); 
            } else {
                showSweetAlert("We are missing some info!", "Whoops!", 'error');
            }
        } else if (selectedProof == 1) {
            var fileAmount = $("#imageProof .galleryth:visible").length;
            if (fileAmount > 0 && fileAmount <= 3) {
                var images = [];
                $("#imageProof .galleryth").each(function() {
                    var input =  $(this).find("input[name='proofImageHidden']");

                    if ($(this).is(":visible")) {
                        images.push($(input).val());
                    } else {
                        $(this).remove();
                    }
                });

                var text = "Are these correct?\n";
                var title = "Confirm proof of delivery?";
                showSweetConfirmWithImages(text, title, "warning", images, function(confirmed) {
                    if (!confirmed) {
                        e.preventDefault();
                    } else {
                        $.ajax({
                            url: '/dashboard/updateTracking',
                            method: 'POST',
                            data: {id: id, proof: selectedProof, images: images},
                            beforeSend: function () {
                                $(e.target).closest('table').prev().removeClass("d-none");
                                $(e.target).addClass('disabled');
                                $(td).find(".cancelTracking").addClass('disabled');
                                $("a.shipinfo[data-id='"+ id +"']").addClass('disabled');
                            },
                            complete: function () {
                                $(e.target).closest('table').prev().addClass("d-none");
                                $(e.target).removeClass('disabled');
                                $(td).find(".cancelTracking").removeClass('disabled');
                                $("a.shipinfo[data-id='"+ id +"']").removeClass('disabled');
                            },
                            success: function (data) {
                                data = JSON.parse(data);
                
                                if (data.status == "success") {
                                    var table = $(e.target).closest('table.dataTable').DataTable();
                                    var tr = $(e.target).closest('table').closest("tr").prev();
                                    var row = table.row(tr);

                                    row.data().images = images;

                                    if (!$("a.shipinfo[data-id='"+ id +"']").hasClass("blue")) {
                                        $("a.shipinfo[data-id='"+ id +"']").text("Processed!");
                                        $("a.shipinfo[data-id='"+ id +"']").addClass("blue");
                                        $("a.shipinfo[data-id='"+ id +"']").removeClass("orange");
                                    }

                                    $(td).find(".editTracking").removeClass("d-none");
                                    $(e.target).addClass("d-none");
                                    $(td).find(".cancelTracking").addClass("d-none");

                                    $("#proofSelect").remove();
                                    $("#proofUpload").remove();

                                    $("a.thremove").remove();
                                } else {
                                    showSweetAlert("Try again later!", "Whoops!", 'error')
                                }
                            }
                        });
                    }     
                }); 
            } else {
                if (fileAmount > 0) {
                    showSweetAlert("You can only select up to 3 images!", "Whoops!", 'error');
                } else {
                    showSweetAlert("You must select at least one image!", "Whoops!", 'error');
                }
            }
        }
        
    });

    $(document).on("click", ".editTracking", function(e) {
        var td = $(this).closest("td");

        $(td).find(".editTracking").addClass("d-none");
        $(td).find(".cancelTracking").removeClass("d-none");

        var content;
        var val;
        if ($(".tracking").length > 0) {
            val = 0;
            content = "<div class='col-sm-4'><select class='form-control' name='processingProof' value='' required><option selected value='0'>Tracking Number</option><option value='1'>Other</option></select></div>";
            $(td).find("span.tracking").addClass("d-none");
        } else if ($("#imageProof").length > 0) {
            val = 1;
            content = "<div class='col-sm-4'><select class='form-control' name='processingProof' value='' required><option value='0'>Tracking Number</option><option selected value='1'>Other</option></select></div>";
        } else {
            val = '';
            content = "<div class='col-sm-4'><select class='form-control' name='processingProof' value='' required><option disabled selected value>---</option><option value='0'>Tracking Number</option><option value='1'>Other</option></select></div>";
        }

        $(td).prepend("<div id='proofSelect' class='row mb-1'>" + content + "</div>");
        $("select[name='processingProof']").val(val).trigger('change');
    });

    $(document).on("change", "select[name='processingProof']", function(e) {
        var type = $(this).val();

        var td = $(this).closest("td");
        var provider = $(td).find(".provider").text();
        var num = $(td).find(".num").text();

        var options;
        var content;
        var trackingNum;

        $(td).find(".editTracking").addClass("d-none");
        $(td).find(".updateTracking").removeClass("d-none");
        $(td).find(".cancelTracking").removeClass("d-none");

        if (type == 0) {
            $("#proofUpload").remove();

            if (provider != null) {
                switch(provider) {
                    case "UPS":
                        options = "<option value='0'>USPS</option><option value='1' selected>UPS</option><option value='2'>FedEx</option>";
                        break;
                    case "FedEx":
                        options = "<option value='0'>USPS</option><option value='1'>UPS</option><option value='2' selected>FedEx</option>";
                        break;
                    default:
                        options = "<option value='0' selected>USPS</option><option value='1'>UPS</option><option value='2'>FedEx</option>";
                        break;
                }
            } else {
                options = "<option value='0' selected>USPS</option><option value='1'>UPS</option><option value='2'>FedEx</option>";
            }
    
            trackingNum = ((num != null) ? num : "");

            content = "<div class='row mb-1 proofTracking'><div class='col-sm-4'><select class='form-control' name='shippingProvider' value='' required>" + options + "</select></div>" + 
                        "<div class='col-sm-4'><input class='form-control' name='trackingNum' type='text' value='' " + ((num == null) ? "placeholder='Tracking Number' " : "") + "required/></div></div>";
            
            $(content).insertAfter($(td).find("div:first"));
        } else if (type == 1) {
            $(".proofTracking").remove();

            content = "<div id='proofUpload' class='col-sm-4'><h4>Add Proof of Processing: </h4><input " + (($("#imageProof img").length >= 3) ? "class='disabled'" : "") + " type='file' id='proofImages' name='proofImages[]' multiple preview-at='imageProof' set-hidden-value='proofImages'><div class='proofImagesError'></div><div class='proofImagesLabel'></div></div>";

            $("#imageProof .galleryth").each(function () {
                $(this).append("<a class='thremove'><i class='fa fa-times' aria-hidden='true'></i></a>");
            });

            if ($("#imageProof img").length >= 3) {
                $("a.updateTracking").prop("disabled", true);
                $("a.updateTracking").addClass("disabled");
            }

            if ($("#imageProof").length > 0) {
                $("#imageProof").prepend(content);
            } else {
                $("<div id='imageProof' class='row mb-1'>" + content + "<div id='imageIcons' class='col'></div></div>").insertAfter("#proofSelect");
            }
        }
        
        $(td).find("span.tracking").addClass("d-none");

        $(document).find("input[name='trackingNum']").val(trackingNum);
    });

    $(document).on('change', '#proofImages', function() {
        $(".updateTracking").addClass("disabled");
        $(".updateTracking").attr("disabled", true);

        imagePreview(this, $(this).closest("td"), $("#imageProof .col:nth-child(2)"), proofOfProcessingImagesCallback);
    });

    function imagePreview(input, parent, insert, callback) {
        var filesAmount = input.files.length;
        if ((filesAmount + $("#imageProof img:visible").length) <= 3) {
            addImageLoader($(parent));

            const promises = [];
            for (i = 0; i < filesAmount; i++) {
                var fileTypes = input.files[i].type;
    
                data = new FormData();
                data.append("file", input.files[i]);
    
                promises.push(imageUpload(insert, fileTypes, data));
            }
    
            Promise.all(promises).then((results) => {
                callback();
                $(parent).find(".loader").remove();
            });
        } else {
            showSweetAlert("Please limit to 3 images!", "Whoops!", 'error');
            callback();
        }
	};

    function imageUpload(insert, fileTypes, data) {
        return new Promise((resolve) => {
            $.ajax({
                url: window.location.origin + "/ajax/uploadImage",
                type: "POST",
                data: data,
                enctype: "multipart/form-data",
                processData: false, 
                contentType: false, 
            }).done(function (data) {
                if (data != "error") {
                    $(".proofImagesLabel").append("");

                    $(insert).append(
                        "<div class='galleryth'>" + 
                            "<img src='" + data + "'><a class='thremove'><i class='fa fa-times' aria-hidden='true'></i></a>" + 
                            "<input type='hidden' name='proofImageHidden' value='" + data + "'>" + 
                        "</div>"
                    );

                    if ($.inArray(fileTypes, ["image/jpeg","image/png","image/jpg","image/gif"]) == -1) {
                        alert("Not a valid image, only JPEG , PNG, or GIF allowed");
                    }
                } else {
                    console.log("Could not upload!");
                }

                resolve();
            });
        });
    }

    $(document).on("click", ".thremove", function () {
        $(this).parent("div").addClass("d-none");

        if ($("#imageProof .galleryth:visible").length < 3) {
            $("#proofImages").removeClass("disabled");

            $("a.updateTracking").prop("disabled", false);
            $("a.updateTracking").removeClass("disabled");
        }
    });

    function proofOfProcessingImagesCallback() {
        $(".updateTracking").removeClass("disabled");
        $(".updateTracking").attr("disabled", false);
    }

    $(document).on("click", ".cancelTracking", function(e) {
        var selectedProof = $("select[name='processingProof']").val();

        var td = $(this).closest("td");

        $(td).find(".editTracking").removeClass("d-none");
        $(this).addClass("d-none");
        $(td).find(".updateTracking").addClass("d-none");
        if (selectedProof == 0) {
            $(td).find("span.tracking").removeClass("d-none");
            $(td).find("div").remove();
        } else if (selectedProof == 1) {
            $("#proofSelect").remove();
            $("#proofUpload").remove();

            $("#imageIcons .galleryth:not(:visible)").removeClass("d-none");

            $("a.thremove").remove();
        }
    });

    $(document).on("click", ".updateReceived", function(e) {
        var td = $(e.target).closest("td");
        var id = $(e.target).data('id');

        var text = "Please make sure you have received your prize; you cannot undo this action.";
        var title = "Received Your Prize?";
        showSweetConfirm(text, title, "warning", function(confirmed) {
            if (!confirmed) {
                e.preventDefault();
            } else {
                $.ajax({
                    url: '/dashboard/updateReceived',
                    method: 'POST',
                    data: {id: id, received: $(e.target).data("received")},
                    beforeSend: function () {
                        $(e.target).closest('table').prev().removeClass("d-none");
                        $(td).find(".updateReceived").addClass('disabled');
                        $("a.shipinfo[data-id='"+ id +"']").addClass('disabled');
                    },
                    success: function (data) {
                        data = JSON.parse(data);
        
                        $(e.target).closest('table').prev().addClass("d-none");
                        $(td).find(".updateReceived").removeClass('disabled');

                        $("a.shipinfo[data-id='"+ id +"']").removeClass('disabled');
                        $("a.shipinfo[data-id='"+ id +"']").removeClass('blue');

                        if ($(e.target).data("received") == 1) {
                            $("a.shipinfo[data-id='"+ id +"']").addClass('green');
                            $("a.shipinfo[data-id='"+ id +"']").text('Received!');
                        } else {
                            $("a.shipinfo[data-id='"+ id +"']").addClass('red');
                            $("a.shipinfo[data-id='"+ id +"']").text('Not Received!');
                        }
                        
                        if (data.status == "success") {
                            $(td).find(".updateReceived").remove();
                        } else {
                            showSweetAlert("Try again later!", "Whoops!", 'error')
                        }
                    }
                });
            }     
        }); 
    });

    $("select[name='filter']").on("change", function() {
        $("table:visible").DataTable().clearPipeline().draw();
    });

    function formatShipInfo(id, data) {
        var add1 = (data.address_1) ? (data.address_1 + "\n") : "";
        var add2 = (data.address_2) ? (data.address_2 + ",\n") : "";
        var city = (data.city) ? (data.city + ", ") : "";
        var state = (data.state) ? (data.state + "\n") : "";
        var zip = (data.zip) ? (data.zip) : "";
        var address = add1 + add2 + city + state + zip;

        var format = '<div class="tableLoader d-none"><div class="imageLoader"></div></div><table class="col" data-id="' + id + '">'+
                    '<tr>'+
                        '<td><h4>Name:</h4></td>'+
                        '<td>'+ data.fullname +'</td>'+
                    '</tr>'+
                    '<tr>'+
                        '<td><h4>Address:</h4></td>'+
                        '<td>'+ address +'</td>'+
                    '</tr>';

       
        var proof;

        var image_proof = data.image_proof;
        var provider = data.shipping_provider;
        var num = data.tracking_num;

        if (image_proof.length > 0) {
            var images = "<div id='imageProof' class='row mb-1'><div id='imageIcons' class='col'>";
            image_proof.forEach(image => {
                images += "<div class='galleryth'><img src='" + image.image + "' data-id='" + image.id + "'/><input type='hidden' name='proofImageHidden' value='" + image.image + "'></div>";
            });

            proof = images + "</div></div>";
        } else {
            proof = "<span class='tracking'>" + ((provider == null) ? "N/A</span>" : ("<span class='provider'>" + provider + "</span> - <span class='num'>" + num + "</span></span>"));
        }

        
        var buttons = "";
        if (data.allowUpdate) {
            buttons = '<a class="btn btnSmall green updateTracking pull-right d-none" data-id="' + id + '"> Confirm</a><a class="btn btnSmall red cancelTracking pull-right d-none"> Cancel</a><a class="btn btnSmall orange editTracking pull-right"> Update</a>';
        } else if (data.allowReceived) {
            buttons = '<a class="btn btnSmall red updateReceived pull-right" data-id="' + id + '" data-received="0">Not Received!</a><a class="btn btnSmall green updateReceived pull-right" data-id="' + id + '" data-received="1">Received!</a>';
        }

        format += '<tr>' + '<td><h4>Proof of Processing:</h4></td>'+
                    '<td style="width: 75%;">' + proof + buttons + '</td></tr>'+ '</table>';

        return format;
    }
});