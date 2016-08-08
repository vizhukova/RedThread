$(document).ready(function () {


    var menu = $('.menu');
    var label = $('.header .label');
    var menuItems = $('.menu').find('li');
    //initialize swiper when document ready  
    var mySwiper = new Swiper ('.swiper-container', {
      // Optional parameters
      direction: 'horizontal',
      loop: true,
      autoHeight: true,
      mousewheelControl: true,
      onSlideChangeStart: onSlideChangeStart
    })

    var forcedImageWidth = window.innerWidth > 800 ? '15rem' : '10rem';

     var carousel = $("#carousel").waterwheelCarousel({
      opacityMultiplier: 0.5,
      flankingItems: 3,
      forcedImageWidth: forcedImageWidth,
      forcedImageHeight: 'auto',
      separationMultiplier: 0.9,
      separation: 50
    });

    $( "#carousel .arrow_right" ).click(function(e) {
      carousel.next();
    });

    $( "#carousel .arrow_left" ).click(function(e) {
      carousel.prev();
    });

     $( "#carousel").click(function(e) {
         e.stopPropagation();
     });

    function onSlideChangeStart(swiper) {
        menuItems.removeClass('active');
        $( menuItems[swiper.activeIndex - 2]).addClass('active');
    }

    label.click(function (e) {
        mySwiper.slideTo(1);
    });

    menu.click(function (e) {
        mySwiper.slideTo(e.target.dataset.num);

        if(window.innerWidth <= 800) {
            $('.header').addClass('hidden');
            $('.header-up').removeClass('hidden');
            $('body').addClass('overflow-no');
        }
    });

    $('.header-up .label').click(function(e) {
         $('.header').removeClass('hidden');
         $('.header-up').addClass('hidden');
         $('body').removeClass('overflow-no');

        mySwiper.slideTo(1);

         e.stopPropagation();
    });

    $('.header-up').click(function(e) {
        if( $('.header-up .menu').hasClass('hidden') ) {
            $('.header-up .menu').removeClass('hidden');
        } else {
            $('.header-up .menu').addClass('hidden');
        }

        e.stopPropagation();
    });

    $('body').click(function(e) {
        $('.header-up .menu').addClass('hidden');
    });


  });