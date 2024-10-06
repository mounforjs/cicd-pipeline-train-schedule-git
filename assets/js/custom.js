$(".navbar-toggler-icon").click(function () {
  var target = $(this).attr("data-target");
  $(target).fadeToggle();
});

jQuery("#today-prize-carousel").owlCarousel({
  autoplay: false,
  // lazyLoad: true,
  loop: true,
  margin: 27,
  navText: ["<i class='fa fa-angle-left' aria-hidden='true'></i>", "<i class='fa fa-angle-right' aria-hidden='true'></i>"],
  navClass: ['owl-prev', 'owl-next'],
  /*
 animateOut: 'fadeOut',
 animateIn: 'fadeIn',
 */
  responsiveClass: true,
  autoHeight: true,
  autoplayTimeout: 7000,
  smartSpeed: 300,
  nav: false,
  items: 5.4,
  responsive: {
    320: {
      items: 1,
    },

    450: {
      items: 2,
    },

    575: {
      items: 2.5
    },

    768: {
      items: 5,
    },

    992: {
      items: 5,

    },

    1280: {
      items: 5.5,
    }
  }
});

jQuery("#popular-competitions-slider").owlCarousel({
  autoplay: false,
  lazyLoad: true,
  loop: true,
  margin: 70,
  navText: ["<i class='fa fa-angle-left' aria-hidden='true'></i>", "<i class='fa fa-angle-right' aria-hidden='true'></i>"],
  navClass: ['owl-prev', 'owl-next'],
  /*
 animateOut: 'fadeOut',
 animateIn: 'fadeIn',
 */
  responsiveClass: true,
  autoHeight: true,
  autoplayTimeout: 7000,
  smartSpeed: 300,
  nav: true,
  responsive: {
    0: {
      items: 1,
      margin: 3
    },

    768: {
      items: 2,
      margin: 3
    },

    992: {
      items: 3,
      margin: 20
    },

    1280: {
      items: 3,
      margin: 10
    }
  }
});

$(window).load(function () {
  // The slider being synced must be initialized first
  $('#carousel').flexslider({
    animation: "slide",
    controlNav: false,
    animationLoop: true,
    slideshow: false,
    itemWidth: 112,
    itemMargin: 14,
    asNavFor: '#slider'
  });

  $('#slider').flexslider({
    animation: "slide",
    controlNav: false,
    animationLoop: true,
    slideshow: false,
    sync: "#carousel"
  });
});


$(document).ready(function() {
  $('.wishcard-2-no, .wishlist-add-btn-detail').click( function (){
            var id = $(this).attr('data-id');
            
            $.ajax({
                method:"POST",
                data:{id:id},
                url: window.location.origin + '/games/add_wishlist',
                success: function(result){
                    if(result == 'already'){
                        showSweetAlert('This game has already been added to wishlist', 'Oops', 'info');
                    
                    } else {
                        showSweetAlert('This game has been added to wishlist', 'Great');
                         location.reload(); //reload page
                        // return false; 
                    }
                }
            });
        });
        
        $('.wishcard-2-yes,.wishlist-remove-btn-detail').click( function (){
            var id = $(this).attr('data-id');
            
            $.ajax({
                method:"GET",
                data:{id:id},
                url: window.location.origin + '/games/remove_wishlist',
                success: function(result){
                    if(result == 'already'){
                        /* swal({
                            type:'info',
                            text:'Already in Wishlist!',
                        }); */
                    } else {
                         showSweetAlert('This game has been removed from wishlist', 'Great');
                         location.reload(); 
                    }
                }
            });
        });

$(".wishcard-2-yes").hover(
   function() {
      $(this).find('i').removeClass('fa-heart');
      $(this).find('i').addClass('fa-heart-o');
   },
   function() {
      $(this).find('i').removeClass('fa-heart-o');
    $(this).find('i').addClass('fa-heart');
   }
);

$(".wishcard-2-no").hover(
   function() {
      $(this).find('i').removeClass('fa-heart-o');
      $(this).find('i').addClass('fa-heart');
   },
   function() {
      $(this).find('i').removeClass('fa-heart');
    $(this).find('i').addClass('fa-heart-o');
   }
);
});



$(document).on('click', '.card-title', function(e) {

        window.location.href = window.location.origin + '/fundraisers/show/all/' + $(this).attr('slug');
});
