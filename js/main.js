$(document).ready(function () {


    var menu = $('.menu');
    var label = $('.header .label');
    var menuItems = $('.menu').find('li');
    var iframeVideo = $('#iframeVideo');
    var bigVideo = $('#bigVideo');
    var modalForm = $('#modalForm');
    var backgroundNode = $('#backgroundNode');

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

    var separationVideo = window.innerWidth <= 320 ? 50 : 100;

    var videoCarousel = $("#video-carousel").waterwheelCarousel({
      opacityMultiplier: 0.5,
      flankingItems: 3,
      forcedImageWidth: forcedImageWidth,
      forcedImageHeight: 'auto',
      separationMultiplier: 0.5,
      separation: separationVideo,
      clickedCenter: onClickedCenter,
      movingToCenter: onMovingToCenter
    });

    $(window).on('resize', function(e) {
        var width = window.innerWidth;

        if(width <= 800) {
            $('.header').height(window.innerHeight);
        } else {
            $('.header').height(65);
        }
    });

    /**
     * carousel functions
     */
    $( "#carousel .arrow_right" ).click(function(e) {
      carousel.next();
    });

    $( "#carousel .arrow_left" ).click(function(e) {
      carousel.prev();
    });

     $( "#carousel").click(function(e) {
         e.stopPropagation();
     });

    $( "#video-carousel .arrow_right" ).click(function(e) {
        iframeVideo.attr('src', '');
        videoCarousel.next();
    });

    $( "#video-carousel .arrow_left" ).click(function(e) {
        iframeVideo.attr('src', '');
        videoCarousel.prev();
    });

     $( ".slider").click(function(e) {
         e.stopPropagation();
     });

    function onClickedCenter(items) {
        var center = $($('#video-carousel').find('.carousel-center')[0]);
        var width = center.width();
        var height = center.height();

        var url = items[0].dataset.url;

        iframeVideo.attr('src', url + '&autoplay=1');
        iframeVideo.width(width);
        iframeVideo.height(height);
        iframeVideo.removeClass('hidden');
    }

    function onMovingToCenter(item) {
        iframeVideo.addClass('hidden');
    }

    /**
     * swiper functions
     */
    function onSlideChangeStart(swiper) {
        console.log(swiper.activeIndex)
        if(swiper.activeIndex == 1) { $('#hand').removeClass('hidden'); console.log(1) }
        else { $('#hand').addClass('hidden'); console.log(2) }

        menuItems.removeClass('active');
        $( menuItems[swiper.activeIndex - 2]).addClass('active');

        backgroundNode.children().removeClass('active');
        $( backgroundNode.children()[swiper.activeIndex - 1]).addClass('active');

        if(swiper.activeIndex == 1 || swiper.activeIndex == 8) {

            $('#hand').removeClass('hidden');

            if(window.innerWidth <= 800) {
               goToSlide1Mobile();
            }

        } else {
            $('#hand').addClass('hidden');
        }

        afterSlidePage();
    }

    backgroundNode.on('click', function(e) {
        var num = e.target.dataset.num;
        goToPage(num);
    });

    function afterSlidePage() {
        iframeVideo.attr('src', '');
        bigVideo.attr('src', bigVideo.attr('src'));
    }

    label.click(function (e) {
        mySwiper.slideTo(1);
        afterSlidePage();
    });

    menu.click(function (e) {
       goToPage(e.target.dataset.num);
    });

    function goToPage(num) {
         mySwiper.slideTo(num);
         afterSlidePage();

        if(window.innerWidth <= 800) {
            $('.header').css('display', 'none');
            $('.header-up').removeClass('hidden');
            $('body').addClass('overflow-no');
        }
    }
    /**
     * header functions
     */

    function goToSlide1Mobile(e) {
         $('.header').css('display', 'block');
         $('.header-up').addClass('hidden');
         $('body').removeClass('overflow-no');

         if(mySwiper) mySwiper.slideTo(1);

         if(e) e.stopPropagation();
    }

    $('.header-up__label').click(function(e) {
       goToSlide1Mobile(e);
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

    /**
     * modal
     */

    $('.modal').click(function(e) {
        $('.modal').addClass('hidden');
        e.stopPropagation();
    });

    $('.modal .form').click(function(e) {
        e.stopPropagation();
    });

    $('.get-call').on('click', function() {
        goToPage(7);
    });

    $('.form').on('submit', function(e) {

        e.preventDefault();
        e.stopPropagation();
        var data = $( this ).serialize();
        var form = $(e.target);

         $.ajax({
            method: 'POST',
            url: './../php/index.php',
            data: data,
            success: function(response){
                form.children().removeClass('error');
                $('.modal').addClass('hidden');
            },
            error: function(response){
                var arrayOfErrors = JSON.parse(response.responseText);
                for(var i = 0; i < arrayOfErrors.length; i++) {
                    form.find('#' + arrayOfErrors[i]).addClass('error');
                }
            }

        });
    });

    $('.open-order-modal').on('click', function(e) {
         $('#modalForm').removeClass('hidden');
          window.scrollTo(0, 0);
    });

    $('.open-consultation-modal').on('click', function(e) {
         $('#modalFormConsultation').removeClass('hidden');
          window.scrollTo(0, 0);
    });

});




