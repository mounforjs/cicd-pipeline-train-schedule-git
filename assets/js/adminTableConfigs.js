
const tableAjax = {
    "games": {
        method: "GET",
        data: {"user_id": location.pathname.split('/')[3]},
        url: window.location.origin + "/admin/getGames",
        dataSrc: "data",
        pages: 5
    },
    "users": {
        method: "GET",
        url: window.location.origin + "/admin/getUsers",
        dataSrc: "data",
        pages: 5
    },
    "allTransactions": {
        method: "GET",
        url: window.location.origin + "/admin/getTransactions",
        dataSrc: "data",
        pages: 5
    },
    "userTransactions": {
        method: "GET",
        data: {"user_id": location.pathname.split('/')[3]},
        url: window.location.origin + "/admin/getTransactions",
        dataSrc: "data",
        pages: 5
    },
    "coupons": {
        method: "GET",
        url: window.location.origin + "/admin/getCoupons",
        dataSrc: "data",
        pages: 5
    },
    "quotes": {
        method: "GET",
        url: window.location.origin + "/admin/getQuotes",
        dataSrc: "data",
        pages: 5
    },
    "feedback": {
        method: "GET",
        url: window.location.origin + "/admin/getFeedback",
        dataSrc: "data",
        pages: 5
    },
    "flags": {
        method: "GET",
        data: {"slug": location.pathname.split('/')[3]},
        url: window.location.origin + "/admin/getFlags",
        dataSrc: "data",
        pages: 5
    },
    "distributions": {
        method: "GET",
        data: function() {
            return {"filter": $("#filter :selected").val()};
        },
        url: window.location.origin + "/admin/getDistributions",
        dataSrc: "data",
        pages: 5
    },
    "short_urls": {
        method: "GET",
        url: window.location.origin + "/admin/getShortUrls",
        dataSrc: "data",
        pages: 5
    },
};

const tableColumnDefs = {
    "games": [
        {
            targets: [0, 3, 4, 5, 6, 13],
            createdCell:  function (td, cellData, rowData, row, col) {
                $(td).attr('id', 'ord_' + rowData.user_id); 
            }
        },
        {
            targets: "_all",
            createdCell:  function (td, cellData, rowData, row, col) {
                $(td).attr('class', 'crd'); 
            }
        },
        {
            targets: 0,
            data: "Game_Image",
            orderable: false,
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    var r = '<img width="50px" height="50px" src="' + row.Game_Image["image"] + '" onerror="imgError(this, "' + row.Game_Image["fallback"] + '");">';
                    
                    return r;
                }

                return data;
            }
        },
        {
            targets: 1,
            data: "name",
            createdCell:  function (td, cellData, rowData, row, col) {
                $(td).attr('id', 'des_' + rowData.user_id); 
            }
        },
        {
            targets: 2,
            data: "gametype",
            createdCell:  function (td, cellData, rowData, row, col) {
                $(td).attr('id', 'src_' + rowData.user_id); 
            },
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    if (row.credit_type == "prize") {
                        data = row.credit_type.charAt(0).toUpperCase() + row.credit_type.slice(1) + "-" + data;
                    }
                }

                return data;
            }
        },
        {
            targets: 3,
            data: "user_id",
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = row.firstname + " " + row.lastname;
                }

                return data;
            }
        },
        {
            targets: 4,
            data: "charityname"
        },
        {
            targets: 5,
            data: "value_of_the_game",
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = "$" + addCommaSeperators(parseFloat(row.value_of_the_game).toFixed(2));
                }

                return data;
            }
        },
        {
            targets: 6,
            data: "credit_cost",
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = "$" + parseFloat(row.credit_cost).toFixed(2);
                }

                return data;
            }
        },
        {
            targets: 7,
            data: "Publish",
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    switch (data) {
                        case "Yes":
                            data = "Published";
                            break;
                        case "Live":
                            data = "Live";
                            break;
                        default:
                            data = "Draft";
                            break;
                    }
                }
                
                return data;
            }
        },
        {
            targets: 8,
            data: "active",
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = "<input type='checkbox' data-toggle='toggle' data-on='Yes' data-off='No' class='game_active_switch'" +
                            ((data == 'Yes') ? ' checked="checked"' : '') + " value='" + row.id + "' />";
                }

                return data;
            }
        },
        {
            targets: 9,
            data: "isProd",
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = "<input type='checkbox' data-toggle='toggle' data-on='Prod' data-off='Test' class='game_server_switch'" +
                            ((row.isProd == 1) ? ' checked="checked"' : '') + " value='" + row.id + "' />";
                }

                return data;
            }
        },
        {
            targets: 10,
            orderable: false,
            render: function(data, type, row, meta) {
                var url = location.origin;

                if (meta.settings.iDraw != 0) {
                    switch (row.Publish) {
                        case "Yes":
                            url += "/games/show/published/";
                            break;
                        case "Live":
                            url += "/games/show/live/";
                            break;
                        default:
                            url += "/games/show/drafted/";
                            break;
                    }

                    data = "<a target='_blank' href='" + url + row.slug + "' class='btn btn-primary' value='" + row.slug + "'>View/Edit</a>";
                }

                return data;
            }
        },
        {
            targets: 11,
            data: "id",
            orderable: false,
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = "<input type='checkbox' name='" + row.id + "' class='delete_game' value='" + row.id + "' />";
                }

                return data;
            }
        },
        {
            targets: 12,
            data: "slug",
            orderable: false,
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = "<a href='" + location.origin + "/admin/flags/" + row.slug + "'" +
                            "class='btn btn-primary' value='" + row.id + "'>Manage Flags</a>";
                }
                
                return data;
            }

        },
        {
            targets: 13,
            data: "created_at"
        },
    ],
    "users": [
        {
            targets: [6, 7, 8, 10, 16],
            createdCell:  function (td, cellData, rowData, row, col) {
                $(td).attr('id', 'ord_' + rowData.user_id); 
                $(td).attr('class', 'crd'); 
            }
        },
        {
            targets: 0,
            data: "firstname",
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = row.firstname.charAt(0).toUpperCase() + row.firstname.slice(1);
                }

                return data;
            }
        },
        {
            targets: 1,
            data: "lastname",
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = row.lastname.charAt(0).toUpperCase() + row.lastname.slice(1);
                }

                return data;
            }
        },
        {
            targets: 2,
            data: "username"
        },
        {
            targets: 3,
            data: "email"
        },
        {
            targets: 4,
            data: "password",
            orderable: false,
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = '<input type="text"  placeholder="Password.." name="password" class="password_user" ' +
                            'data-id="' + row.user_id + '" ><button class="btn_update_pass" type="button">Update</button>'
                            '<div id="password-errors"></div>';
                }

                return data;
            }
        },
        {
            targets: 5,
            data: "coupon",
            orderable: false,
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    if (row.coupons === undefined || row.coupons === null) { return; }

                    data = '<select id="couponSelect"><option value="0">Please Select the coupon</option>';
                    for (var i = 0; i < row.coupons.length; i ++) {
                        data += '<option user-data="' + row.username + '" value="' + row.coupons[i].id + '">' + row.coupons[i].description + ' for $' + row.coupons[i].amount + '</option>';
                    }

                    data += '</select>';
                }

                return data;
            }
        },
        {
            targets: 6,
            data: "country"
        },
        {
            targets: 7,
            data: "name"
        },
        {
            targets: 8,
            data: "decision_maker"
        },
        {
            targets: 9,
            data: "user_roles",
            orderable: false,
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    var data = '<select id="roleSelect">';
                    // Add the default empty option
                    data += '<option value="" disabled ' + (!row.role ? 'selected' : '') + '>Select a role</option>';

                    // Loop through user_roles to add options
                    for (var i = 0; i < row.user_roles.length; i++) {
                        var permission = row.user_roles[i].permission;
                        var role = row.user_roles[i].role;

                        data += '<option user-data="' + row.username + '" value="' + permission + '" ' +
                            (row.usertype === permission ? 'selected' : '') + '>' +
                                role.charAt(0).toUpperCase() + role.slice(1) +
                            '</option>';
                    }

                    // Close the select element
                    data += '</select>';
                }

                return data;
            }
        },
        {
            targets: 10,
            data: "user_status",
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = "<input type='checkbox' data-toggle='toggle' data-on='Enabled' data-off='Disabled' class='user_status_switch' " + ((row.user_status == 'Yes') ? 'checked="checked"' : "") + " value='" + row.user_id + "' />";
                }

                return data;
            }
        },
        {
            targets: 11,
            data: "creator_status",
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = '<input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" class="creator_status_switch" ' + ((row.creator_status == 'Yes') ? 'checked="checked"' : "") + ' value="' + row.user_id + '" />';
                }

                return data;
            }
        },
        {
            targets: 12,
            data: "btester_status",
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = '<input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" class="tester_status_switch" ' + ((row.btester_status == 'Yes') ? 'checked="checked"' : "") + ' value="' + row.user_id + '" />';
                }

                return data;
            }
        },
        {
            targets: 13,
            data: "credit_withdraw_status",
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = '<input type="checkbox" data-toggle="toggle" data-on="Enabled" data-off="Disabled" class="credit_withdraw_status_switch" ' + ((row.credit_withdraw_status == 'Yes') ? 'checked="checked"' : "") + ' value="' + row.user_id + '" />';
                }

                return data;
            }
        },
        {
            targets: 14,
            orderable: false,
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = '<a href="' + location.origin + '/games/show/play/?user=' + row.username + '" class="btn btn-primary">View All</a>';
                }
                
                return data;
            }

        },
        {
            targets: 15,
            orderable: false,
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = '<a href="' + location.origin + '/admin/userTransactions/' + row.user_id + '" class="btn btn-primary" value="' + row.user_id + '">View All</a>';
                }
                
                return data;
            }

        },
        {
            targets: 16,
            orderable: false,
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = '<input type="checkbox" name="' + row.user_id + '" class="delete_user" value="' + row.user_id + '" />';
                }
                
                return data;
            }

        },
        {
            targets: 17,
            data: "created_at"
        }
    ],
    "allTransactions": [
        {
            targets: 0,
            data: "Date"
        },
        {
            targets: 1,
            data: "user_id",
            createdCell:  function (td, cellData, rowData, row, col) {
                $(td).attr('id', rowData.user_id); 
                $(td).attr('class', "player_name"); 
            },
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = "<a data-placement='right' data-toggle='popover'  data-html='true' href='#' id='login'><span class='text-lowercase'>" + row.firstname + " (" + row.email + ")</span></a>";
                }

                return data;
            }
        },
        {
            targets: 2,
            data: "Status",
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    if (parseInt(row.Status) != 1) {
                        data = '<div class="status badge badge-danger badge-pill badge-sm">Debit</div>';
                    } else {
                        data = '<div class="status badge badge-success badge-pill badge-sm">Credit</div>';
                    }
                }

                return data;
            }
        },
        {
            targets: 3,
            data: "Credits",
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = "<i class='fa fa-dollar'></i>" + addCommaSeperators(parseFloat(row.Credits).toFixed(2));
                }

                return data;
            }
        },
        {
            targets: 4,
            data: "total_charge",
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                        data = "<i class='fa fa-dollar'></i>" + addCommaSeperators(parseFloat(row.total_charge).toFixed(2));
                }

                return data;
            }
        },
        {
            targets: 5,
            data: "total_credits",
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = "<i class='fa fa-dollar'></i>" + addCommaSeperators(parseFloat(row.total_credits).toFixed(2));
                }

                return data;
            }
        },
        {
            targets: 6,
            data: "Notes"
        },
        {
            targets: 7,
            data: "payment_status",
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = '<div class="status badge badge-success badge-pill badge-sm">' + row.payment_status + '</div>';
                }
            
                return data;
            }
        },
        {
            targets: 8,
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
            targets: 9,
            data: "game_name"
        },
        {
            targets: 10,
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = (row.final_rank != null) ? row.final_rank : "";
                }

                return data;
            }
        },
        {
            targets: 11,
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    if ((row.creator_fundraise_id != null || row.winner_fundraise_id != null) && parseInt(row.user_type) != 1) {
                        var fundraiser_id;
                        var fundraiser_name;

                        if (parseInt(row.user_type) == 2) {
                            fundraiser_id = row.creator_fundraise_id;
                            fundraiser_name = row.creator_fundraise_name;
                        } else if (parseInt(row.user_type) == 4) {
                            fundraiser_id = row.winner_fundraise_id;
                            fundraiser_name = row.winner_fundraise_name;
                        }

                        data  = '<a data-placement="right" data-toggle="popover"  data-html="true" href="#" id="' + fundraiser_id + '" class="fund_name" >' + fundraiser_name + '</a>';
                    } else {
                        data = "";
                    }
                }

                return data;
            }
        },
        {
            targets: 12,
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
        {
            targets: 13,
            data: "is_deductible",
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = (parseInt(row.is_deductible) == 1) ? "Yes" : "No";
                }

                return data;
            }
        },
        {
            targets: 14,
            data: "ref_num"
        }
    ],
    "userTransactions": [
        {
            targets: 0,
            data: "Date"
        },
        {
            targets: 1,
            data: "Status",
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    if (parseInt(row.Status) != 1) {
                        data = '<div class="status badge badge-danger badge-pill badge-sm">Debit</div>';
                    } else {
                        data = '<div class="status badge badge-success badge-pill badge-sm">Credit</div>';
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
                    data = '<div class="status badge badge-success badge-pill badge-sm">' + row.payment_status + '</div>';
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
        {
            targets: 12,
            data: "is_deductible",
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = (parseInt(row.is_deductible) == 1) ? "Yes" : "No";
                }

                return data;
            }
        },
        {
            targets: 13,
            data: "ref_num"
        }
    ],
    "coupons": [
        {
            targets: 0,
            data: "description",
            createdCell:  function (td, cellData, rowData, row, col) {
                $(td).attr('id', rowData.id); 
                $(td).attr('name', "description"); 
                $(td).attr('contenteditable', "true"); 
            }
        },
        {
            targets: 1,
            data: "amount",
            createdCell:  function (td, cellData, rowData, row, col) {
                $(td).attr('id', rowData.id); 
                $(td).attr('name', "amount"); 
                $(td).attr('contenteditable', "true"); 
            }
        },
        {
            targets: 2,
            data: "active",
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = '<input type="checkbox" data-toggle="toggle" data-on="Yes" data-off="No" class="coupon_active_switch"' + ((row.active == "Yes") ? "checked='checked'" : "") + ' value="' + row.id + '" />';
                }

                return data;
            }
        },
        {
            targets: 3,
            orderable: false,
            render: function(data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = '<input type="checkbox"  class="delete_coupon" value="' + row.id + '" />';
                }

                return data;
            }
        }
    ],
    "quotes": [
        {
            targets: 0,
            data: "category",
            render (data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = row.category.charAt(0).toUpperCase() + row.category.slice(1);
                }
                
                return data;
            }
        },
        {
            targets: 1,
            data: "description"
        },
        {
            targets: 2,
            data: "source"
        },
        {
            targets: 3,
            data: "order_no"
        },
        {
            targets: 4,
            data: "featured",
            render (data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = '<input type="checkbox" name="quote_id" id="checkbox" class="feature_quote_chk"' + ((row.featured == "Yes") ? "checked='checked'" : "") +
                            'value="' + row.id + '" />';
                }
                
                return data;
            }
        },
        {
            targets: 5,
            orderable: false,
            render (data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = "<a class='delete_quote' href='javascript:void(0)' quote-id='" + row.id + "'>Delete</a>";
                }
                
                return data;
            }
        },
        {
            targets: 6,
            orderable: false,
            render (data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = "<a class='edit_quote' href='javascript:void(0)' quote-id='" + row.id + "'>Edit</a>";
                }
                
                return data;
            }
        }
    ],
    "feedback": [
        {
            targets: [1, 2, 3, 4, 5, 6, 7, 8],
            createdCell:  function (td, cellData, rowData, row, col) {
                $(td).attr('id', 'de' + rowData.user_id); 
                $(td).attr('class', 'crd'); 
            }
        },
        {
            targets: 0,
            data: "date_created"
            ,
            render(data,type,row,meta){
                var convertedDate = window.convertUTCDateToLocalDate(row.date_created).toLocaleString();
                if(convertedDate == 'Invalid Date'){
                    return row.date_created;
                }else{
                    
                    return window.convertUTCDateToLocalDate(row.date_created).toLocaleString()
                }
            }
        },
        {
            targets: 1,
            data: "user_id",
            render (data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    if (row.user_id != null) {
                        data = row.firstname + " " + row.lastname;
                    } else {
                        data = "Guest User";
                    }
                }
                
                return data;
            }
        },
        {
            targets: 2,
            data: "email",
            render (data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    if (row.user_id != null) {
                        data = row.email;
                    } else {
                        data = "N/A";
                    }
                }
                
                return data;
            }
        },
        {
            targets: 3,
            data: "page_url"
        },
        {
            targets: 4,
            data: "rating"
        },
        {
            targets: 5,
            data: "winwinrating"
        },
        {
            targets: 6,
            data: "category_name"
        },
        {
            targets: 7,
            data: "feedback_description"
        },
        {
            targets: 8,
            orderable: false,
            render (data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = '<input type="button" class="btn btn-primary feedback-images" data-id ="' + row.id + '" value="View" />';
                }
                
                return data;
            }
        }
    ],
    "flags": [
        {
            targets: 0,
            data: "created_at"
        },
        {
            targets: 1,
            data: "creator_id",
            render (data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    if (row.firstname != null) {
                        data = row.firstname + " " + row.lastname;
                    } else {
                        data = "N/A"
                    }
                }
                
                return data;
            }
        },
        {
            targets: 2,
            data: "flag_description"
        },
    ],
    "distributions": [
        {
            targets: 0,
            data: "ref_number",
            createdCell:  function (td, cellData, rowData, row, col) {
                $(td).closest('tr').addClass("distribution"); 
                $(td).closest('tr').data("id", rowData.id); 
            }
        },
        {
            targets: 1,
            data: "status",
            render (data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    if (row.status == 1) {
                        data = '<a class="btn btnSmall red">Pending</a>';
                    } else {
                        data = '<a class="btn btnSmall green">Processed</a>';
                    }
                }
                
                return data;
            }
        },
        {
            targets: 2,
            data: "review",
            render (data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    if (row.review == 1) {
                        if (row.approved != null) {
                            data = '<a class="btn btnSmall green">Reviewed</a>';
                        } else {
                            data = '<a class="btn btnSmall red">Reviewable</a>';
                        }
                    } else {
                        data = '<a class="btn btnSmall">N/A</a>';
                    }
                }
                
                return data;
            }
        },
        {
            targets: 3,
            data: "approved",
            render (data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    if (row.approved == null) {
                        data = '<a class="btn btnSmall">N/A</a>';
                    } else {
                        if (row.approved == 1) {
                            data = '<a class="btn btnSmall green">Approved</a>';
                        } else {
                            data = '<a class="btn btnSmall red">Not Approved</a>';
                        }
                        
                    }
                }
                
                return data;
            }
        },
        {
            targets: 4,
            data: "game_id",
            render (data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = row.game_id + " - " + row.game_name;
                }
                
                return data;
            }
        },
        {
            targets: 5,
            data: "creator_id",
            render (data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = row.creator_id + " - " + row.creator_name;
                }
                
                return data;
            }
        },
        {
            targets: 6,
            data: "creator_fundraise_id",
            render (data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = row.creator_fundraise_id + " - " + row.creator_charity_name;
                }
                
                return data;
            }
        },
        {
            targets: 7,
            data: "winner__id",
            render (data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = row.winner_id + " - " + row.winner_name;
                }
                
                return data;
            }
        },
        {
            targets: 8,
            data: "winner_fundraise_id",
            render (data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = row.winner_fundraise_id + " - " + row.winner_charity_name;
                }
                
                return data;
            }
        }
    ],
    "short_urls": [
        {
            targets: 0,
            data: "user_id",
            'createdCell': function(td, cellData, rowData, row, col) {
                $(td).parent().attr('class', 'select');
            },
            render (data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = row.user_id + " - " + row.username;
                }
                
                return data;
            }
        },
        {
            targets: 1,
            data: "url",
            render (data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = '<a href="' + window.location.origin + "/" + data + '" target="_blank">' + data + '</a>';
                }
                
                return data;
            }
        },
        {
            targets: 2,
            data: "short_url",
            render (data, type, row, meta) {
                if (meta.settings.iDraw != 0) {
                    data = '<a href="' + window.location.origin + "/" + data + '" target="_blank">' + data + '</a>';
                }
                
                return data;
            }
        },
        {
            targets: 3,
            data: "created_at"
        }
    ],
};

const tableDrawCallbacks = {
    "games": function( settings ) {
        $("input[class='game_active_switch']").bootstrapToggle();
        $("input[class='user_status_switch']").bootstrapToggle();
        $("input[class='game_server_switch']").bootstrapToggle();
    },
    "users": function( settings ) {
        $("input[class='user_status_switch']").bootstrapToggle();
        $("input[class='creator_status_switch']").bootstrapToggle();
        $("input[class='tester_status_switch']").bootstrapToggle();
        $("input[class='credit_withdraw_status_switch']").bootstrapToggle();

        $("#myAdvancedTable").DataTable().columns.adjust();
    },
    "allTransactions" : function( settings ) {
        
    },
    "userTransactions" : function( settings ) {
        
    },
    "coupons" : function( settings ) {
        $("input[class='coupon_active_switch']").bootstrapToggle();
    },
    "quotes" : function( settings ) {

    },
    "feedback" : function( settings ) {
        
    },
    "flags" : function( settings ) {
        
    },
    "distributions" : function( settings ) {
        
    },
    "short_urls" : function( settings ) {
        
    },
};

const tableLengthMenu = {
    "games": [
        [10, 25, 50, 100, 200, -1],
        [10, 25, 50, 100, 200, "All"]
    ]
};

const tableOrderBy = {
    "allTransactions": [[0, "desc"]],
    "userTransactions": [[0, "desc"]],
    "feedback": [[0, "desc"]],
};

function addCommaSeperators(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}