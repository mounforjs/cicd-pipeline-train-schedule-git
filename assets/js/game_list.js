var current_url = window.location.pathname.split('/')[3];
var game_status = current_url ? current_url : 'play';

$(document).ready(function() {
    $('#mobileFilterModal .modal-body').html($('.copyDiv').html());
    $(document).tooltip({
        selector: '[data-toggle="tooltip"]',
        delay: { show: 500 }
    });

    initSubMenus();
    
    $.ajax({
        type: "POST",
        url: window.location.origin + "/user/sessions"
    }).done(function( msg ) {
        usession = msg;
    });

    $('select[name="game_beneficiary"]').selectize({
        maxItems: 1,
        closeAfterSelect: false,
        onChange: function(value) {
            $(`select[name="game_beneficiary"]`).each((idx, elem) => { 
                if (this.$input[0] != elem) { 
                    var val = value || history.state.filters.beneficiary;
                    elem.selectize.setValue(val, true);
                } 
            });

            // value undefined if silent
            if (value) {
                loadGameCards(true);
            }
        }
    });

    if (!history.state) {
        history.replaceState({ filters: getFilters() }, "");
    } else {
        if (history.state.filters) {
            var filters = getFilters();
            var filters_length = Object.keys(filters).length
            var history_filter_length = Object.keys(history.state.filters).length;

            if (filters_length != history_filter_length || JSON.stringify(filters) !== JSON.stringify(history.state.filters)) {
                loadGameCards(true, history.state.filters);
            }
        } 
    }

    $(document).on('click', '.cardfilters li .dropdown-menu', function (e) {
        e.stopPropagation();
    });

    $('#loadMoreButton').click(function() {
        loadGameCards(false);
    });

    $(document).on("keyup input", "[name='game_search']:visible", function(event) {
        let lowercaseInput = $(this).val().toLowerCase();
        $(this).val(lowercaseInput);

        if (event.keyCode === 13) {
            loadGameCards(true);
        }
    }); 

    $(document).on('click', '.search_icon', function() {
        loadGameCards(true);
    });

    $(document).on('click', '.clearAll', function() {
        clearBadges();
        clearFilters();

        loadGameCards(true);
    });

    $(document).on('input', '.cardfilters input, .cardfilters select', function(e) {
        // synchronize desktop/mobile filters
        $(`input[name='${this.name}']`).each((idx, elem) => { 
            if (elem.type == "checkbox" || elem.type == "radio") {
                if (this != elem && this.value == elem.value) { 
                    elem.checked = this.checked; 

                    if (elem.classList.contains("sub-item")) {
                        toggleSubMenuItems(elem);
                    }
                } 
            } else {
                if (this != elem) { 
                    elem.value = this.value; 
                } 
            }
        });

        $(`select[name='${this.name}']`).each((idx, elem) => { 
            if (this != elem) { 
                elem.value = this.value;
            } 
        });
    });

    $(document).on('change', '[name^="game_value"], [name^="game_cost"], [name^="game_type"], [name^="gm_cr_type"], [name^="credit_type"], [name="game_sort_list"]', function(e) {
        loadGameCards(true);
    });

    $(document).on('click', '.game_tag', function(e) {
        var game_search = $('.cardfilters:visible:first [name="game_search"]');
        game_search_val = game_search.val();

        game_search_val += ` tag:"${this.text.trim()}"`;
        game_search.val(game_search_val);

        loadGameCards(true);
    });

    $(document).on('mouseover', function(e) {
        var parent = $(e.target).closest(".game_card")
        var cards = $(".showData").find(".game_card");
        cards.each((index, elem) => {
            if (index !== parent.index()) {
                elem.classList.remove("show");
            } else {
                if (!elem.classList.contains("show")) {
                    elem.classList.add("show");
                }
            }
        });
    });
    
    $(document).on('change', '[name="beneficiary_type"]', function(e) {
        var beneficiary_type = $('[name="beneficiary_type"]:checked').val();

        $.ajax({
            type: "GET",
            data: { sub_type: beneficiary_type},
            url: window.location.origin + '/fundraisers/getBeneficiaryList/',
            success: function(data) {
                data = JSON.parse(data);

                var optionList = [];

                $.each(data, function(idx, val) {
                    $child = {
                        text: val.name,
                        value: val.slug
                    };
                    optionList.push($child);
                });

                $("select[name='game_beneficiary']").each((idx, elem) => {
                    var selectize = $(elem)[0].selectize;
                    selectize.clear();
                    selectize.clearOptions();
                    selectize.load(function(callback) {
                        callback(optionList);
                    });
                });
            }
        });
    });

    $(document).on("click", '.filterbadge', function (e) {
        var data_val = $(this).data("val");
        var type = $(this).data("type");

        removeFilter(type, data_val);
        removeBadge(this);

        setTimeout(loadGameCards, 1000, true);
    });

    $(document).on("mouseenter", ".wishcard-yes", function() {
        $(this).find('i').removeClass('fa-heart');
        $(this).find('i').addClass('fa-heart-o');
    });

    $(document).on("mouseleave", ".wishcard-yes", function() {
        $(this).find('i').removeClass('fa-heart-o');
        $(this).find('i').addClass('fa-heart');
    });

    $(document).on("mouseenter", ".wishcard-no", function() {
        $(this).find('i').removeClass('fa-heart-o');
        $(this).find('i').addClass('fa-heart');
    });

    $(document).on("mouseleave", ".wishcard-no", function() {
        $(this).find('i').removeClass('fa-heart');
        $(this).find('i').addClass('fa-heart-o');
    });

    $(document).on('click', '.wishcard-no', function (){
        var id = $(this).attr('data-id');
        var wishcard = $(this);
        var icon = $(this).find("i");

        $.ajax({
            method:"POST",
            data:{id:id},
            url: window.location.origin + '/games/add_wishlist',
            success: function(result){
                if(result == 'already'){
                    showSweetAlert('This game has already been added to wishlist', 'Whoops!', 'info');
                } else {
                    $(wishcard).addClass("wishcard-yes"); $(wishcard).removeClass("wishcard-no");
                    $(icon).addClass("fa-heart"); $(icon).removeClass("fa-heart-o");
                    showSweetAlert('This game has been added to wishlist', 'Great');
                }
            }
        });
    });

    $(document).on('click', '.wishcard-yes', function (){
        var id = $(this).attr('data-id');
        var wishcard = $(this);
        var icon = $(this).find("i");

        $.ajax({
            method:"POST",
            data:{id:id},
            url: window.location.origin + '/games/remove_wishlist',
            success: function(result){
                if(result == 'already'){
                    showSweetAlert('This game has already been removed from wishlist', 'Whoops!', 'info');
                } else {
                    $(wishcard).addClass("wishcard-no"); $(wishcard).removeClass("wishcard-yes");
                    $(icon).addClass("fa-heart-o"); $(icon).removeClass("fa-heart");
                    showSweetAlert('This game has been removed from wishlist', 'Great');
                }
            }
        });
    });

    $(document).on('click', '.dropdown-menu a.dropdown-toggle', function(e) {
        // nested menus - submenu
        
        if (!$(this).next().hasClass('show')) {
            $(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
        }
    
        var sub_menu = $(this).next(".dropdown-menu");
        sub_menu.toggleClass('show');
    
        $(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function(e) {
            $('.dropdown-submenu .show').removeClass("show");
        });
    
        return false;
    });
    
    $(document).on("click", ".sub-root", function() {
        var checked = $(this).prop("checked");
        toggleSubMenu(this, checked);
    });
    
    $(document).on("click", ".sub-item", function(e) {
        toggleSubMenuItems(this);
    });
});

window.addEventListener('popstate', function (event) {
    if (event.state) {
        loadGameCards(true, event.state.filters);
    }
});

var offset = 0;
const loadGameCards = (reset, filter_history=null) => {
    var card_count = document.querySelectorAll(".game_card").length;
    offset = (reset) ? 0 : card_count;

    var filters = (!filter_history) ? getFilters() : filter_history;

    $.ajax({
        url: window.location.origin + '/games/showMore',
        type: 'get',
        data: filters,
        beforeSend: function() {
            $('#divLoading').addClass('show');
        },
        complete: function() {
            $('#divLoading').removeClass('show');
        },
        success: function(response) {
            data = JSON.parse(response);
            
            replaceFilters(data.filters);
            if (!filter_history) {
                let search = data.filters["search"] ? `: ${data.filters.search.input}` : "";
                let title = `${location.host} - Games ${search}`;
                document.title = title;
                history.pushState({ filters: filters }, "");
            }

            if (data.game_data.length == 0) {
                $('#loadMoreButton').addClass("d-none");
                if (reset) {
                    $(".showData").empty();
                    $('#noRecordsFound').removeClass("d-none");
                } else {
                    $('#noMoreRecords').removeClass("d-none");
                    setTimeout(() => {
                        $('#noMoreRecords').addClass("d-none");
                    }, 5000)
                }
            } else {
                if (data.game_data.length >= 6) {
                    $('#loadMoreButton').removeClass("d-none");
                } else {
                    $('#loadMoreButton').addClass("d-none");
                }
                
                if (reset) {
                    $('#noRecordsFound').addClass("d-none");
                    $('.showData').html('');
                } else {
                    $('#noMoreRecords').addClass("d-none");
                }

                var cards = appendCards(data.game_data);
                $('.showData').append(cards);

                $('[data-toggle="tooltip"]').tooltip();
            }
        }
    });
}

const game_values = $('.cardfilters:first [name^="game_value"]:checkbox').map(function() {
    return this.value;
}).get();

const game_costs = $('.cardfilters:first [name^="game_cost"]:checkbox').map(function() {
    return this.value;
}).get();

const getFilters = () => {
    var filters = {};

    try {
        var min_value = $('.cardfilters:visible:first [name="game_value_min"]:first');
        var max_value = $('.cardfilters:visible:first [name="game_value_max"]:first');
        var min_max_range_price = $('.cardfilters:visible:first [name^="game_value"]:checkbox:checked');

        var game_value_range = "";
        if (min_max_range_price.length > 0) {
            game_value_range = min_max_range_price.map(function() {
                return this.value;
            }).get().join(", ");
        } else if (min_value.val().length > 0 || max_value.val().length > 0) {
            game_value_range = [min_value.val(), max_value.val()].filter((v) => v).join("-");
        }

        var min_cost = $('.cardfilters:visible:first [name="game_cost_min"]:first');
        var max_cost = $('.cardfilters:visible:first [name="game_cost_max"]:first');
        var min_max_range_cost = $('.cardfilters:visible:first [name^="game_cost"]:checkbox:checked');

        var game_cost_range = "";
        if (min_max_range_cost.length > 0) {
            game_cost_range = min_max_range_cost.map(function() {
                return this.value;
            }).get().join(", ");
        } else if (min_cost.val().length > 0 || max_cost.val().length > 0) {
            game_cost_range = [min_cost.val(), max_cost.val()].filter((v) => v).join("-");
        }

        var game_type = $('.cardfilters:visible:first [name^="game_type"]:checkbox:checked').map(function() {
            return this.value;
        }).get().join(", ");
    
        var credit_type = $('.cardfilters:visible:first [name^="credit_type"]:checkbox:checked').map(function() {
            return this.value;
        }).get().join(", ");
    
        var beneficiary = $(".cardfilters:visible:first select[name='game_beneficiary']")[0].selectize.getValue();
    
        var sort_list = $('.cardfilters:visible:first [name="game_sort_list"]').val();
    
        var search_text = $('.cardfilters:visible:first [name="game_search"]').val();
    
        filters = {
            show: game_status,
            offset: offset,
    
            game_value: game_value_range,
            game_cost: game_cost_range,
            game_type: game_type,
            credit_type: credit_type,
            beneficiary: beneficiary,
            sort_list: sort_list,
            search: search_text
        };
    } catch (e) {
        console.log(e);
    }
    
    // remove any filters that are null/empty
    return Object.fromEntries(Object.entries(filters).filter(([k, filter]) => (filter != "" && filter !== undefined)));
}

const replaceFilters = (filters) => {
    clearBadges();
    clearFilters();

    applyFilters(filters);
    appendBadges(filters);
}

function applyFilters(filters) {
    for (const [filter, value] of Object.entries(filters)) {
        switch (filter) {
            case "beneficiary":
                $("select[name='game_beneficiary']").each((idx, elem) => {
                    elem.selectize.setValue(value.slug, true);
                });
                break;
            case "game_value":
            case "game_cost":
                var [min, max] = value.split("-");

                $(`input[name='${filter}_min']`).val(min);
                $(`input[name='${filter}_max']`).val(max);
                break;
            case "game_type":
            case "credit_type":
                value.split(",").forEach((val, idx) => {
                    var input = $(`input[name='${filter}_${val}']`)
                    if (input.length > 0) {
                        input.prop('checked', true);
                        if (input.length > 0 && input[0].classList.contains("sub-root")) {
                            toggleSubMenu(input, true);
                        } else {
                            toggleSubMenuItems(input);
                        }
                    }
                });
                break;
            case "search":
                $('[name="game_search"]').val(value.input);
                break;
            case "sort_list":
                $('select[name="game_sort_list"]').val(value);
                break;
            default:
                break;
        }
    }
}

function removeFilter(type, value) {
    if (type) {
        var val = value.toString().trim();
        if (type == "beneficiary") {
            $("select[name='game_beneficiary']").each((idx, elem) => {
                $(elem).data("filtered", "");
                $(elem)[0].selectize.clear(true);
            });
        } else if (type == "keywords" || type == "game_tag") {
            var re = new RegExp(`[\s]*(["']*(${val})["']*)[\s]*`, "gi");
            let search_val = $("[name='game_search']").val();
            search_val = search_val.replace(re, "");

            $("[name='game_search']").val(search_val.trim());
        } else if (type == "game_value" || type == "game_cost") {
            $(`input[name='${type}_min']`).val("");
            $(`input[name='${type}_max']`).val("");
        } else {
            var element = $(`[name='${type}_${value}']`);
            $(element).prop('checked', false);
        }

        initSubMenus();
    }
}

const clearFilters = () => {
    $('.cardfilters input:checkbox').removeAttr('checked');
    $('.cardfilters input[type="number"], input[type="text"]').val('');

    $("select[name='game_beneficiary']").each((idx, elem) => {
        var selectize = $(elem)[0].selectize;
        selectize.clear(true);
    });

    $('.filter-search input:text').val('');

    $('select[name="game_sort_list"]').val($('select[name="game_sort_list"] option').eq(0).val());

    clearAllSubMenuItems();
    initSubMenus();
};

const appendBadges = (filters) => {
    $showClearAll = false;

    var appended = false;
    for (const [filter, value] of Object.entries(filters)) {
        if (filter == "beneficiary") {
            appended = appendBadge("beneficiary", value);
        } else if (filter == "search") {
            value.keywords.forEach((val, idx) => {
                appended = appendBadge("keywords", val.trim());
            });
            value.tags.forEach((val, idx) => {
                appended = appendBadge("game_tag", val.trim());
            });
        } else  {
            if (typeof(value) === 'string') {
                value.split(",").forEach((val, idx) => {
                    appended = appendBadge(filter, val.trim());
                });
            }
        }

        if (!$showClearAll && appended) {
            $showClearAll = appended;
        }
    }

    if ($showClearAll) {
        appendBadge('clear');
    }
}

const appendBadge = (filter, val) => {
    var badges = $('.filter-badges');

    var badge = document.createElement("a");
    badge.dataset.type = filter;
    badge.dataset.toggle = "tooltip";
    badge.dataset.placement = "bottom";
    badge.dataset.originalTitle = filter.replace("_", " ").replace(/\b\w/g, l => l.toUpperCase());
    badge.dataset.delay = '{"show":"500"}';

    var append = true;
    switch (filter) {
        case "game_value":
            badge.dataset.val = val;
            badge.className = "badge badge-primary ml-1 filterbadge";
            badge.innerHTML = val + ' <i class="fa fa-times"></i>';
            break;
        case "game_cost":
            var _val = (parseFloat(val.split("-")[1]) > 0) ? val : "Free";
            badge.dataset.val = val;
            badge.className = "badge badge-danger ml-1 filterbadge";
            badge.innerHTML = _val + ' <i class="fa fa-times"></i>';
            break;
        case "game_type":
            var _val = val.replace("_", " ").replace(/\b\w/g, l => l.toUpperCase())
            badge.dataset.val = val;
            badge.className = "badge badge-warning ml-1 filterbadge";
            badge.innerHTML = val + ' <i class="fa fa-times"></i>';
            break;
        case "credit_type":
            var _val = val.replace("_", " ").replace(/\b\w/g, l => l.toUpperCase())
            badge.dataset.val = val;
            badge.className = "badge badge-light ml-1 filterbadge";
            badge.innerHTML = val + ' <i class="fa fa-times"></i>';
            break;
        case "game_tag":
            badge.dataset.val = val;
            badge.className = "badge badge-light ml-1 filterbadge";
            badge.innerHTML = val + ' <i class="fa fa-times"></i>';
            break;
        case "beneficiary":
            badge.dataset.val = val.slug;
            badge.className = "badge badge-success ml-1 filterbadge";
            badge.innerHTML = val.name + ' <i class="fa fa-times"></i>';
            break;
        case "keywords":
            badge.dataset.val = val;
            badge.className = "badge badge-secondary ml-1 filterbadge";
            badge.innerHTML = val + ' <i class="fa fa-times"></i>';
            break;
        case "clear":
            badge.className = "badge badge-default clearAll ml-1";
            badge.innerHTML = 'Clear All <i class="fa fa-times"></i>';
            break;
        default:
            append = false;
            break;
    }

    if (append) {
        badges.append(badge);
    }

    return append;
}

const removeBadge = (elem) => {
    var tooltip = elem.getAttribute("aria-describedby");
    if (tooltip !== null) {
        document.querySelector(`#${tooltip}`).remove();
    }

    elem.remove();
}

const clearBadges = () => {
    $('.filter-badges').html('');
    $(".tooltip").each((idx, elem) => elem.remove());
};

const appendCards = (game_data) => {
    var appendData = "";
    $.each(game_data, function(idx, val) {

        if (val.supported_fundraise.length !== 0) {
            fundraise_name = val.supported_fundraise[0].name;
            fundraise_description = val.supported_fundraise[0].description;
        } else {
            fundraise_name = '';
            fundraise_description = '';
        }

        if (val.game_wishlist_status== 1) {
            var fa_class = 'fa fa-heart';
            var wish_class = 'wishcard-yes';
            var tooltip_title = 'Remove from wishlist';
        } else {
            var fa_class = 'fa fa-heart-o';
            var wish_class = 'wishcard-no';
            var tooltip_title = 'Add to wishlist';
        }

        let usession;
        if (usession==1 && (game_status=="" || game_status=="play"  || game_status=="wishlist")){
            var wishlist = '<div class="' + wish_class + '" data-id = "' + val.id + '"><i class="' + fa_class + '" title = "' + tooltip_title + '"></i></div>' ;
        } else{
            var wishlist = "";
        }

        var game_tags = [];
        if(val.game_tags && val.game_tags != ""){
            var _game_tags =  val.game_tags.split(",");
            for (var i = 0; i < _game_tags.length; i++) {
                game_tags.push(`<a class="game_tag" href="javascript:void(null);">${_game_tags[i]}</a>`);
            }
        }
        
        var beneficiary = val.supported_fundraise[0];
        var icon = "";
        var fundLogo = "";

        if (beneficiary) {
            if(val.credit_type !== 'free'){
            icon = '<span class="cardhover ' + beneficiary.fundraise_type + '" data-toggle="tooltip" data-placement="top" title="' + (beneficiary.fundraise_type.charAt(0).toUpperCase() + beneficiary.fundraise_type.slice(1)) + '">';

            switch(beneficiary.fundraise_type) {
                case "charity":
                    icon += '<i class="fas fa-hand-holding-heart"></i>';
                    break;
                case "project":
                    icon += '<i class="fas fa-lightbulb"></i>';
                    break;
                case "education":
                    icon += '<i class="fa fa-graduation-cap"></i>';
                    break;
                case "cause":
                    icon += '<i class="fa fa-globe"></i>';
                    break;
            }
            
            icon += '</span>';
            }
        }

        if (val.credit_type !== 'free') {
            fundLogo = "<div class='col-sm-auto pl-0'>" + 
                            '<img class="fundlogo" src="' + val.supported_fundraise_image.image + '" onerror="imgError(this, \'' + val.supported_fundraise_image.fallback + '\');" alt="' + fundraise_name + '">' + 
                        "</div>";
        }
        
        var countDownIndex = $(".showData").children().length+idx;

        var game_type = val.game_type[0].name.replace("_", " ");
        var cardContainer = '<div class="col-lg-4 col-md-6 p-2 pt-3 game_card">' + wishlist +
            '<div class="countdownribbon"><p><span class="ribbon-content text-center" id="countdown'+countDownIndex+'"></span></p></div>'+
            ' <article class="card">' +
            '   <a href="' + window.location.origin + '/games/show/' + game_status + '/' + val.slug + '">' +
            '   <img class="thumb" src="' + val.GameImage.image + '" onerror="imgError(this, \'' + val.GameImage.fallback + '\');">' +
            '   </a>' +
            '   <div class="card-body p-0">' +
            '       <div class="infos">' +
            '         <h3 class="cardquickstats">' +
            '             <span class="prizecosticon mytooltip" data-toggle="tooltip" data-placement="top" title="Cost to Play"><i class="fa fa-gamepad" aria-hidden="true"></i> $' + val.credit_cost + '</span>' +
            '             <span class="valueicon mytooltip" data-toggle="tooltip" data-placement="top" title="Winner\'s Reward Value"><i class="fa fa-trophy" aria-hidden="true"></i> $' + val.value_of_the_game + '</span>' + icon +
            '             <span class="cardhover mytooltip" data-toggle="tooltip" data-placement="top" title="' + (game_type.charAt(0).toUpperCase() + game_type.slice(1)) + '"><img src="' + val.game_type_image + '" width="18" alt="Type of Game"></span>' +
            '             <span class="cardhover mytooltip" data-toggle="tooltip" data-placement="top" title="' + (val.credit_type.charAt(0).toUpperCase() + val.credit_type.slice(1)) + '"><img src="' + val.credit_type_image + '" width="18" alt="' + val.credit_type + '"></span>' +
            '         </h3>' +
            '         <div class="row">' +
            '           <div class="col pr-0">' +
            '               <h2 class="title">' + val.name + '</h2>' +
            '           </div>' +
                        fundLogo +
            '         </div>' + 
            '         <div class="row">' +
            '           <div class="col">' +
            '               <p class="txt">' + text_truncate(val.game_desc.replace(/<[^>]+>/g, ''),100) + '</p>' +
            '           </div>' +
            '         </div>' +
            '       </div>' +
            '       <div class="tagdetails">' +
            '           <a class="btn details item" href="' + window.location.origin + '/games/show/' + game_status + '/' + val.slug + '">Details</a>' +
            '           <p class="tagitems">' +  game_tags.join("") + '</p>' +
            '       </div>' +
            '    </div>' +
            ' </article>' +
            '</div>' ;

        appendData += cardContainer;
        startCountdown(val.Publish_Date, 'countdown'+countDownIndex);
    });

    return appendData;
}

const initSubMenus = () => {
    $(document).find(".dropdown-submenu").each((index, root) => {
        //set correct checked/interderminate state for each submenu on load
        $(root).find(".sub-item").each((index, item) => {
            toggleSubMenuItems(item);
            return false;
        });
    });
}

const clearAllSubMenuItems = () => {
    $(document).find('.dropdown-submenu input.sub-item').prop('checked', false);
       $(document).find('.dropdown-submenu input.sub-item').prop('indeterminate', false);
}

const toggleSubMenu = (elem, checked) => {
    //check all sub-menu items if checking root/parent sub-menu
    $(elem).siblings(".dropdown-menu").find("input").each((index, elem) => {
        $(elem).prop("checked", ((checked) ? !checked : checked));
        $(elem).prop("indeterminate", checked);
    });
}

const toggleSubMenuItems = (item) => {
    //set correct checked/indeterminate state for submenu items and root

    var checked = $(item).prop("checked");
    var indeterminate = $(item).prop("indeterminate");

    var total_checked = checked ? 1 : 0;
    var total_indeterminate = indeterminate ? 1 : 0;

    var siblings = $(item).closest("li").siblings();
    var siblings_length = siblings.length+1; //add one to include item itself
    siblings.each((index, elem) => {
        if ($(elem).find(".sub-item").prop("checked")) {
            total_checked++;
        }

        if ($(elem).find(".sub-item").prop("indeterminate")) {
            total_indeterminate++;
        }
    });

    //if all sub-items are checked, set root to checked and all children to indeterminate
    if (siblings_length == total_checked || siblings_length == total_indeterminate) {
        $(item).closest(".dropdown-submenu").find(".sub-root").prop("checked", true);
        $(item).closest(".dropdown-submenu").find(".sub-root").prop("indeterminate", false);
        
        $(item).prop("checked", false)
        $(item).prop("indeterminate", true)
        siblings.each((index, elem) => {
            $(elem).find(".sub-item").prop("checked", false);
            $(elem).find(".sub-item").prop("indeterminate", true);
        });
    } else {
        //all sub-items are not checked, set root to indeterminate
        $(item).closest(".dropdown-submenu").find(".sub-root").prop("checked", false);
        if (total_checked + total_indeterminate > 0) {
            $(item).closest(".dropdown-submenu").find(".sub-root").prop("indeterminate", true);
        } else {
            $(item).closest(".dropdown-submenu").find(".sub-root").prop("indeterminate", false);
        }
  
        // set children to correct checked state
        if (total_checked + total_indeterminate == siblings_length) {
            $(item).prop("checked", false)
            $(item).prop("indeterminate", false)

            siblings.each((index, elem) => {
                $(elem).find(".sub-item").prop("checked", true);
                $(elem).find(".sub-item").prop("indeterminate", false);
            });
        }
    }
}