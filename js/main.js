$(document).ready(function () {


    var menu = $('.menu');
    var label = $('.header .label');
    var menuItems = $('.menu').find('li');
    var iframeVideo = $('#iframeVideo');
    var bigVideo = $('#bigVideo');
    var modalForm = $('#modalForm');

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

    var videoCarousel = $("#video-carousel").waterwheelCarousel({
      opacityMultiplier: 0.5,
      flankingItems: 3,
      forcedImageWidth: forcedImageWidth,
      forcedImageHeight: 'auto',
      separationMultiplier: 0.5,
      separation: 100,
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
        menuItems.removeClass('active');
        $( menuItems[swiper.activeIndex - 2]).addClass('active');
        afterSlidePage();
    }

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

    $('.header-up .label').click(function(e) {
         $('.header').css('display', 'block');
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

    /**
     * modal
     */

    console.log( $('#modalForm .ground'),  $('#modalForm .form'))
    $('#modalForm').click(function(e) {
        $('#modalForm').addClass('hidden');
        e.stopPropagation();
    });

    $('#modalForm .form').click(function(e) {
        e.stopPropagation();
    });

    $('.get-call').on('click', function() {
        goToPage(7);
    });

});

function openFormModal() {
    $('#modalForm').removeClass('hidden');
    window.scrollTo(0, 0);
}

function resizeIframe(obj) {
//    debugger
//obj.style.height = obj.contentWindow.innerWidth * 0.5625 + 'px';
}


