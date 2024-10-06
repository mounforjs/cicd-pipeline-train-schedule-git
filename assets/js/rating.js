$(document).ready(function() {
    rating = $("#rtg-btn").data("rating");
    $("#rating").on("mouseover", function(e) {
        if ($(e.target).is("i")) {
            var targetIndex = $(e.target).index();
            
            highlightStars(targetIndex);
        }
    });

    $("#rating").on("mouseleave", function(e) {
        highlightStars(rating);
    });
    
    $("#rating i").on("click", function() {
        rating = $(this).index();
        highlightStars(rating);

        $("#rtg-btn").show();
    });
    
    $("#rtg-btn").on("click", function() {
        submitRating();

        $("#rtg-btn").hide();
    });

    function highlightStars(targetIndex) {
        $("#rating i").each(function() {
            var index = $(this).index()
            if (index <= targetIndex) {
                if (!$(this).hasClass("rateorange")) {
                    $(this).addClass("rateorange");
                }
            } else {
                $(this).removeClass("rateorange");
            }
        });
    }

    function submitRating() {
        var slug = window.location.href.substring(window.location.href.lastIndexOf('/') + 1);
        $.ajax({
            method: 'POST',
            data: {slug: slug, rating: rating + 1},
            url: window.location.origin+'/games/addrating'
        });
    }
});