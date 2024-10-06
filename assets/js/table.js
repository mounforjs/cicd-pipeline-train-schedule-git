$(document).ready(function() {
    $.fn.dataTable.ext.errMode = 'none';

    $.fn.dataTable.pipeline = function ( opts ) {
        var conf = $.extend( {
            pages: 5,
            url: '',
            data: null,
            method: 'GET'
        }, opts );
     
        var cacheLower = -1;
        var cacheUpper = null;
        var cacheLastRequest = null;
        var cacheLastJson = null;
     
        return function ( request, drawCallback, settings ) {
            var ajax          = false;
            var requestStart  = request.start;
            var drawStart     = request.start;
            var requestLength = request.length;
            var requestEnd    = requestStart + requestLength;
             
            if ( settings.clearCache ) {
                ajax = true;
                settings.clearCache = false;
            }
            else if ( cacheLower < 0 || requestStart < cacheLower || requestEnd > cacheUpper ) {
                ajax = true;
            }
            else if ( JSON.stringify( request.order )   !== JSON.stringify( cacheLastRequest.order ) ||
                      JSON.stringify( request.columns ) !== JSON.stringify( cacheLastRequest.columns ) ||
                      JSON.stringify( request.search )  !== JSON.stringify( cacheLastRequest.search )
            ) {
                ajax = true;
            }
             
            cacheLastRequest = $.extend( true, {}, request );
     
            if ( ajax ) {
                if ( requestStart < cacheLower ) {
                    requestStart = requestStart - (requestLength*(conf.pages-1));
     
                    if ( requestStart < 0 ) {
                        requestStart = 0;
                    }
                }
                 
                cacheLower = requestStart;
                cacheUpper = requestStart + (requestLength * conf.pages);
     
                request.start = requestStart;
                request.length = requestLength*conf.pages;
     
                if ( typeof conf.data === 'function' ) {
                    var d = conf.data( request );
                    if ( d ) {
                        $.extend( request, d );
                    }
                }
                else if ( $.isPlainObject( conf.data ) ) {
                    $.extend( request, conf.data );
                }
     
                return $.ajax( {
                    "type":     conf.method,
                    "url":      conf.url,
                    "data":     request,
                    "dataType": "json",
                    "cache":    false,
                    "success":  function ( json ) {
                        cacheLastJson = $.extend(true, {}, json);
     
                        if ( cacheLower != drawStart ) {
                            json.data.splice( 0, drawStart-cacheLower );
                        }
                        if ( requestLength > -1 ) {
                            json.data.splice( requestLength, json.data.length );
                        }
                         
                        drawCallback(json);
                    }
                } );
            }
            else {
                json = $.extend( true, {}, cacheLastJson );
                json.draw = request.draw;
                json.data.splice( 0, requestStart-cacheLower );
                json.data.splice( requestLength, json.data.length );
     
                drawCallback(json);
            }
        }
    };
     
    $.fn.dataTable.Api.register( 'clearPipeline()', function () {
        return this.iterator( 'table', function ( settings ) {
            settings.clearCache = true;
        } );
    } );

    $.fn.dataTable.ext.buttons.reload = {
        className: "p-1",
        text: '<i class="fa fa-refresh" aria-hidden="true"></i>',
        action: function ( e, dt, node, config ) {
            dt.clearPipeline().draw(false);
        }
    };

    function initializeDataTables() {
        $(".table").each(function( index ) {
            var id = $(this).attr("id");
            var type = $(this).data("type");
            var propagate = $(this).data("propagate");

            //prepopulate table with data from php, deferring the loading of ajax sourced data - see above for more detail
            var defer = ($(this).parent().find("input[name='deferLoad']").length > 0) ? [ $(this).prev().data("filtered"), $(this).prev().data("total") ] : false;

            //each table element MUST have a type, e.g. data-type='games'
            if (type !== undefined) { 
                var table = $("#" + id).DataTable({
                    dom: '<"row"<"col-sm-auto"l><"col-sm-auto ml-auto"<"row"<"col pr-1"f><"p-0"B>>>>tipr',
                    autoWidth: false,
                    scrollX: true,
                    lengthMenu: ((tableLengthMenu[type] != undefined) ? tableLengthMenu[type] : [
                        [10, 25, 50],
                        [10, 25, 50]
                    ]),
                    order: ((tableOrderBy[type] != undefined) ? tableOrderBy[type] : []),
                    buttons: [
                        'reload'
                    ],
                    responsive: true,
                    processing: true,
                    serverSide: true,
                    deferLoading: defer,
                    ajax: $.fn.dataTable.pipeline(tableAjax[type]), 
                    columnDefs: tableColumnDefs[type], 
                    drawCallback: tableDrawCallbacks[type] 
                });

                table.columns.adjust(); //adjust columns to fit to width
                if (!defer) { //if data has not been prepopulated, get data
                    table.ajax.reload();
                }

                if (propagate) {
                    table.on("preXhr.dt", function(e) {
                        $("#" + propagate).DataTable().clearPipeline().draw(false);
                    })
                }
            }

            $(this).find(".dataTables_filter input")
                .unbind()
                .bind("keyup", function(e) {
                    if (e.keyCode == 13 && (this.value.length >= 3 || this.value.length == 0)) {
                        $($.fn.dataTable.tables(true)).DataTable().search(this.value).draw();
                    } else if (e.keyCode == 8 && this.value.length == 0) {
                        $($.fn.dataTable.tables(true)).DataTable().search(this.value).draw();
                    }

                    return;
            });
        });
    }

    $(window).resize(function() {
        $(".table[data-type]").each(function( index ) {
            var table = $("#" + $(this).attr("id")).DataTable();

            table.columns.adjust();
        });
    });

    initializeDataTables();
});

function ImgError(source) {
    source.src = "https://dg7ltaqbp10ai.cloudfront.net/fit-in/200x200/game.jpeg";
    source.onerror = "";
    return true;
}