
const tableAjax = {
    "transactions": {
        method: "GET",
        url: window.location.origin + "/transactions/getTransactions",
        dataSrc: "data",
        pages: 5
    },
    "questions": {
        method: "GET",
        data: function(d) {
            d.userfilter = $("#filter").val();
        },
        url: window.location.origin + "/challenge/getQuestions",
        dataSrc: "data",
        pages: 5
    },
    "selectQuestions": {
        method: "GET",
        data: function(d) {
            d.userfilter = $("#selectFilter").val();
        },
        url: window.location.origin + "/challenge/getApprovedQuestions",
        dataSrc: "data",
        pages: 5
    },
    "quizzes": {
        method: "GET",
        data: function(d) {
            d.userfilter = $("#filter").val();
        },
        url: window.location.origin + "/challenge/getQuizzes",
        dataSrc: "data",
        pages: 5
    },
    "createGameQuizzes": {
        method: "GET",
        data: function(d) {
            d.userfilter = $("#filter").val();
        },
        url: window.location.origin + "/challenge/getApprovedQuizzes",
        dataSrc: "data",
        pages: 5
    },
    "claimedPrizes": {
        method: "GET",
        data: function(d) {
            d.statusfilter = $("select[name='filter']:visible").val();
        },
        url: window.location.origin + "/dashboard/getClaimedPrizes",
        dataSrc: "data",
        pages: 5
    },
    "claimablePrizes": {
        method: "GET",
        data: function(d) {
            d.statusfilter = $("select[name='filter']:visible").val();
        },
        url: window.location.origin + "/dashboard/getClaimablePrizes",
        dataSrc: "data",
        pages: 5
    },
    "reviewGames": {
        method: "GET",
        url: window.location.origin + "/games/getAllReviewGames",
        dataSrc: "data",
        pages: 5
    },
    "reviewSelectedUsers": {
        method: "GET",
        data: function(d) {
            var href = window.location.href.split("/");
            d.slug = href[href.length - 1];
        },
        url: window.location.origin + "/games/getReviewGameSelectedUsers",
        dataSrc: "data",
        pages: 5
    },
    "reviewUsers": {
        method: "GET",
        data: function(d) {
            var href = window.location.href.split("/");
            d.slug = href[href.length - 1];
        },
        url: window.location.origin + "/games/getReviewGameAttempts",
        dataSrc: "data",
        pages: 5
    },
    "reviewUserAttempts": {
        method: "GET",
        data: function(d) {
            d.user_id = $("#userAttemptData").data("user");
            d.game_id = $("#userAttemptData").data("game");
            d.quiz_id = $("#userAttemptData").data("quiz");
            d.attempt_num = $("#userAttemptsFilter").val();
        },
        url: window.location.origin + "/games/getReviewGameUserAttempt",
        dataSrc: "data",
        pages: 5
    },
};

const tableColumnDefs = {
    "transactions": [
        {
            targets: 0,
            data: "Date",
            width: "200px"
        },
        {
            targets: 1,
            data: "Status",
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    if (parseInt(row.Status) != 1) {
                        data = '<div class="status badge badge-danger badge-pill badge-sm transaction-status">Debit</div>';
                    } else {
                        data = '<div class="status badge badge-success badge-pill badge-sm transaction-status">Credit</div>';
                    }
                }

                return data;
            }
        },
        {
            targets: 2,
            data: "Credits",
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = "<i class='fa fa-dollar'></i>" + addCommaSeperators(parseFloat(row.Credits).toFixed(2));
                }

                return data;
            }
        },
        {
            targets: 3,
            data: "total_charge",
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                        data = "<i class='fa fa-dollar'></i>" + addCommaSeperators(parseFloat(row.total_charge).toFixed(2));
                }

                return data;
            }
        },
        {
            targets: 4,
            data: "total_credits",
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = "<i class='fa fa-dollar'></i>" + addCommaSeperators(parseFloat(row.total_credits).toFixed(2));
                }

                return data;
            }
        },
        {
            targets: 5,
            data: "Notes"
        },
        {
            targets: 6,
            data: "payment_status",
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = '<div class="status transaction-status badge badge-success badge-pill badge-sm ' + row.badge_color + '">' + (row.payment_status[0].toUpperCase() + row.payment_status.slice(1)) + '</div>';
                }
            
                return data;
            }
        },
        {
            targets: 7,
            data: "payment_mode",
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    switch (parseInt(row.payment_mode)) {
                        case 1:
                            data = "Bank ACH";
                            break;
                        case 2:
                            data = "Paypal";
                            break;
                        case 3:
                            data = "Credit Card";
                            break;
                        case 4:
                            data = "WinWinLabs";
                            break;
                    }
                }

                return data;
            }
        },
        {
            targets: 8,
            data: "game_name"
        },
        {
            targets: 9,
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = (row.final_rank != null) ? row.final_rank : "";
                }

                return data;
            }
        },
        {
            targets: 10,
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    if (row.creator_fundraise_id != null ) {
                        data  = '<a data-placement="right" data-toggle="popover"  data-html="true" href="#" id="' + ((parseInt(row.user_type) == 1) ? row.creator_fundraise_id : row.winner_fundraise_id) + '" class="fund_name" >' +
                                ((parseInt(row.user_type) == 1) ? row.creator_fundraise_name : ((row.donated_to_fundraiser_name != null) ? row.donated_to_fundraiser_name : row.winner_fundraise_name)) + '</a>';
                    } else {
                        data = "";
                    }
                }

                return data;
            }
        },
        {
            targets: 11,
            data: "user_type",
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    switch (parseInt(row.user_type)) {
                        case 1:
                            data = "Creator";
                            break;
                        case 2:
                            data = "Creator Fundraise";
                            break;
                        case 3:
                            data = "Main Winner";
                            break;
                        case 4:
                            data = "Winner Fundraise";
                            break;
                        case 5:
                            data = "Sub Winner";
                            break;
                        case 6:
                            data = "WinWinLabs";
                            break;
                        default:
                            data = "N/A";
                            break;
                    }
                }

                return data;
            }
        },
        // {
        //     targets: 12,
        //     data: "is_deductible",
        //     render: function(data, type, row, meta) {
        //         if (meta.settings.iDraw != 0) {
        //             data = (parseInt(row.is_deductible) == 1) ? "Yes" : "No";
        //         }

        //         return data;
        //     }
        // },
        {
            targets: 12,
            data: "ref_num"
        }
    ],
    "questions": [
        {
            targets: 0,
            data: "id"
        },
        {
            targets: 1,
            data: "category_name"
        },
        {
            targets: 2,
            data: "type"
        },
        {
            targets: 3,
            data: "difficulty"
        },
        {
            targets: 4,
            data: "question"
        },
        {
            targets: 5,
            data: "status",
            render (data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    if (row.editable) {
                        data = "<a class='editQues' data-toggle='modal' data-target='#questionModal' data-id='" + row.id + "'>Edit</a> |" + 
                        "<span> " + ((row.status == 0) ? "Approval Requested" : ((row.status == 1) ? "Approved" : "Declined")) + "</span>";
                    } else {
                        data = "<span> " + ((row.status == 0) ? "Approval Requested" : ((row.status == 1) ? "Approved" : "Declined")) + "</span>";
                    }
                }
                
                return data;
            }
        }
    ],
    "selectQuestions": [
        {
            targets: 0,
            data: "id",
            'createdCell': function(td, cellData, rowData, row, col) {
                $(td).parent().attr('id', 'ques' + cellData);
            }
        },
        {
            targets: 1,
            data: "category_name"
        },
        {
            targets: 2,
            data: "type"
        },
        {
            targets: 3,
            data: "difficulty"
        },
        {
            targets: 4,
            data: "question"
        },
    ],
    "quizzes": [
        {
            targets: 0,
            data: "id"
        },
        {
            targets: 1,
            data: "category_name"
        },
        {
            targets: 2,
            data: "name"
        },
        {
            targets: 3,
            data: "difficulty"
        },
        {
            targets: 4,
            data: "questions",
            render (data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = row.questions.replace(/[^0-9a-zA-Z., ]/g,'').split(",").length;
                }
                
                return data;
            }
        },
        {
            targets: 5,
            render (data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = ((row.is_publish == 0) ? "Draft" : ((row.status == 0) ? "Approval Requested" : ((row.status == 1) ? "Approved" : "Declined")));
                }
                
                return data;
            }
        },
        {
            targets: 6,
            data: "status",
            render (data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = "";
                    if (row.editable) {
                        data = "<a class='editQuiz' data-toggle='modal' data-target='#quizModal' data-id='" + row.id + "'>Edit</a>";
                    }

                    data += "<a class='btn small pull-right viewQues' data-id='" + row.id + "' data-toggle='modal' data-target='#myQuizQuestionModal'><i class='fa fa-eye' aria-hidden='true'></i></a>";
                }
                
                return data;
            }
        },
    ],
    "createGameQuizzes": [
        {
            targets: 0,
            data: "id",
            'createdCell': function(td, cellData, rowData, row, col) {
                $(td).parent().attr('class', 'select');
                if ($("#selectedQuiz").val() == cellData) {
                    $(td).parent().attr('class', 'selected');
                }
            }
        },
        {
            targets: 1,
            data: "category_name"
        },
        {
            targets: 2,
            data: "name"
        },
        {
            targets: 3,
            data: "difficulty"
        },
        {
            targets: 4,
            data: "questions",
            render (data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = row.questions.replace(/[^0-9a-zA-Z., ]/g,'').split(",").length;
                }
                
                return data;
            }
        },
        {
            targets: 5,
            data: "status",
            render (data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = "<input type='radio' name='quiz' value='" + row.id + "' " + (($("#selectedQuiz").val() == row.id) ? "checked='checked'" : "") + "/>"
                    data += "<a class='btn small pull-right viewQues' data-id='" + row.id + "' data-toggle='modal' data-target='#myQuizQuestionModal'><i class='fa fa-eye' aria-hidden='true'></i></a>";
                }

                return data;
            }
        }
    ],
    "claimedPrizes": [
        {
            targets: 0,
            data: "name",
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = "<h3>" + row.name + "</h3>";
                }

                return data;
            }
        },
        {
            targets: 1,
            data: "prize_title",
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = "<h3>" + row.prize_title + "</h3>";
                }

                return data;
            }
        },
        {
            targets: 2,
            data: "confirmed",
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    if (row.confirmed == 1) {
                        if (row.processed == 0) {
                            data = '<a class="shipinfo escrow-status orange" data-id="' + row.id + '">Claimed!</a>';
                        } else if (row.processed == 1) {
                            if (row.received == 1) {
                                data = '<a class="shipinfo escrow-status green" data-id="' + row.id + '">Received!</a>';
                            } else if (row.received != null && row.received == 0) {
                                data = '<a class="shipinfo escrow-status red" data-id="' + row.id + '">Not Received!</a>';
                            } else {
                                data = '<a class="shipinfo escrow-status blue" data-id="' + row.id + '">Processed!</a>';
                            }
                        }
                    } else {
                        if (row.review != 1) {
                            data = '<a class="escrow-status idle red" href="' + location.origin + "/games/show/completed/" + row.slug + '">Pending..</a>';
                        } else {
                            if (row.appoved == 1) {
                                data = '<a class="escrow-status green">Approved</a>';
                            } else if (row.approved != null && row.appoved == 0) {
                                data = '<a class="escrow-status idle red">Disapproved</a>';
                            } else {
                                data = '<a class="escrow-status idle red">Under Review</a>';
                            }
                        }
                    }

                }

                return data;
            }
        },
    ],
    "claimablePrizes": [
        {
            targets: 0,
            data: "name",
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = "<h3>" + row.name + "</h3>";
                }

                return data;
            }
        },
        {
            targets: 1,
            data: "prize_title",
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = "<h3>" + row.prize_title + "</h3>";
                }

                return data;
            }
        },
        {
            targets: 2,
            data: "confirmed",
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    if (row.confirmed == 1) {
                        if (row.processed == 0) {
                            data = '<a class="escrow-status idle red">Pending..</a>';
                        } else if (row.processed == 1) {
                            if (row.received == 1) {
                                data = '<a class="shipinfo escrow-status green" data-id="' + row.id + '">Received!</a>';
                            } else if (row.received != null && row.received == 0) {
                                data = '<a class="shipinfo escrow-status red" data-id="' + row.id + '">Not Received!</a>';
                            } else {
                                data = '<a class="shipinfo escrow-status blue" data-id="' + row.id + '">Processed!</a>';
                            }
                        }
                    } else {
                        data = '<a class="escrow-status green" href="' + location.origin + "/games/show/completed/" + row.slug + '">Claim Prize!</a>';
                    }
                }

                return data;
            }
        },
    ],
    "reviewGames": [
        {
            targets: 0,
            orderable: false,
            data: "Game_Image",
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                   data = '<img width="50px" height="50px" src="' + row.Game_Image['image'] + '" onerror="imgError(this, \''+ row.Game_Image['fallback'] +'\')">'
                }

                return data;
            }
        },
        {
            targets: 1,
            data: "name",
        },
        {
            targets: 2,
            data: "gametype",
        },
        {
            targets: 3,
            data: "charityname",
        },
        {
            targets: 4,
            data: "value_of_the_game",
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = "<i class='fa fa-dollar'></i>" + addCommaSeperators(parseFloat(row.value_of_the_game).toFixed(2));
                }

                return data;
            }
        },
        {
            targets: 5,
            data: "credit_cost",
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = "<i class='fa fa-dollar'></i>" + addCommaSeperators(parseFloat(row.credit_cost).toFixed(2));
                }

                return data;
            }
        },
        {
            targets: 6,
            data: "player_count",
            render: function(data, type, row, meta) {
                if (data == null) {
                    data = 0;
                }

                return data;
            }
        },
        {
            targets: 7,
            data: "Status",
        },
        {
            targets: 8,
            orderable: false,
            data: "slug",
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = '<a href="' + window.location.origin + '/games/review/' + row.slug + '" class="btn btn-primary" >' + ((row.processed == 1 && row.review_status == 0) ? "Reviewed" : "Review") + '</a>';
                }

                return data;
            }
        },
    ],
    "reviewSelectedUsers": [
        {
            targets: 0,
            orderable: false,
            data: "final_rank"
        },
        {
            targets: 1,
            orderable: false,
            data: "username"
        },
        {
            targets: 2,
            data: "grade",
            orderable: false
        },
        {
            targets: 3,
            orderable: false,
            data: "notes"
        }
    ],
    "reviewUsers": [
        {
            targets: 0,
            data: "final_rank",
            'createdCell': function(td, cellData, rowData, row, col) {
                $(td).attr('id', "final_rank-" + row);
                $(td).attr('name', "final_rank");

                var tr = $(td).parent();
                if (!parseInt(rowData.reselected) && !tr.hasClass("reselected")) {
                    if (rowData.editable) {
                        $(td).attr('contenteditable', "true");
                        $(td).attr('class', "edit");
                    }

                    if (parseInt(rowData.final_rank)) {
                        tr.attr('class', 'selected');
                    }
                } else {
                    tr.attr('class', 'reselected');
                }
            },
            render: function(data, type, row, meta) {
                if (parseInt(row.reselected)) {
                    data = "RESELECTED";
                }

                return data;
            }
        },
        {
            targets: 1,
            data: "",
            render: function(data, type, row, meta) {
                return meta.row + 1;
            }
        },
        {
            targets: 2,
            data: "username",
            'createdCell': function(td, cellData, rowData, row, col) {
                $(td).attr('id', "username-" + row);
                $(td).data('user_id', rowData.user_id);
                $(td).attr('class', "player_name");
            }
        },
        {
            targets: 3,
            data: "grade",
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    if (parseInt(row.reselected)) {
                        data = row.grade;
                    } else {
                        if (row.editable) {
                            data = '<select class="grade_value" id="' + "grade-" + meta.row + '" name="grade">' + 
                            ('<option ' + ((row.grade && row.grade == "A") ? 'selected ' : '') + 'value="A">A</option>') +
                            ('<option ' + ((row.grade && row.grade == "B") ? 'selected ' : '') + 'value="B">B</option>') +
                            ('<option ' + ((row.grade && row.grade == "C") ? 'selected ' : '') + 'value="C">C</option>') +
                            ('<option ' + ((row.grade && row.grade == "D") ? 'selected ' : '') + 'value="D">D</option>') +
                            ('<option ' + ((row.grade && row.grade == "E") ? 'selected ' : '') + 'value="E">E</option></select>');
                        }
                    }
                } else {
                    data = (row.grade != "") ? row.grade : "N/A";
                }

                return data;
            }
        },
        {
            targets: 4,
            orderable: false,
            data: "notes",
            'createdCell': function(td, cellData, rowData, row, col) {
                $(td).attr('id', "notes-" + row);
                $(td).attr('name', "notes");
                
                var tr = $(td).parent();
                if (!parseInt(rowData.reselected) && !tr.hasClass("reselected")) {
                    if (rowData.editable) {
                        $(td).attr('contenteditable', "true");
                        $(td).attr('class', "edit");
                    }
                }
            }
        },
        {
            targets: 5,
            orderable: false,
            data: "userTotalAttempts",
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = '<button class="btn btn-sm showReviewBtn" type="button" data-toggle="modal" data-target="#userAttemptModal"' +
                    'data-user="' + row.user_id + '" data-game="' + row.game_id + '" data-attempts="' + data + '">View ' + data + ' Attempts</button>';
                }

                return data;
            }
        },
    ],
    "reviewUserAttempts": [
        {
            targets: 0,
            orderable: false,
            data: "id"
        },
        {
            targets: 1,
            orderable: false,
            data: "question"
        },
        {
            targets: 2,
            orderable: false,
            data: "answer"
        },
        {
            targets: 3,
            orderable: false,
            data: "time"
        },
    ],
};

const tableDrawCallbacks = {
    "transactions": function( settings ) {
        
    },
    "questions" : function( settings ) {
        
    },
    "selectQuestions" : function( settings ) {
        
    },
    "quizzes" : function( settings ) {
        
    },
    "createGameQuizzes" : function( settings ) {
        
    },
    "reviewGame": function( settings ) {
        
    },
    "reviewSelectedUsers": function( settings ) {
        var table = new $.fn.dataTable.Api( settings );
        $("#selectedUsersCount").val(table.page.info().recordTotal);
        $("#selectedUsers").text(table.page.info().recordTotal);
    },
    "reviewUsers": function( settings ) {
        
    },
    "reviewUserAttempts": function( settings ) {
        $("#userAttemptsLoader").hide();
    },
};

const tableLengthMenu = {
    "reviewSelectedUsers": [
        [3, 6, 10],
        [3, 6, 10]
    ]
};

const tableOrderBy = {
    "transactions": [[0, "desc"]],
};

function addCommaSeperators(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}