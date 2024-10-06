$(window).load(function () {
    $("#header").load("header.html");
    $("#footer").load("footer.html");
});


//MAIN Menu On Hover
(function($) { "use strict";
$('body').on('mouseenter mouseleave','.nav-item',function(e){
    if ($(window).width() > 990) {
        var _d=$(e.target).closest('.nav-item');_d.addClass('show');
        setTimeout(function(){
        _d[_d.is(':hover')?'addClass':'removeClass']('show');
        },1);
    }
    });
})(jQuery);
