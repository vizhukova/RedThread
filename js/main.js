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

    setCarouselSliders();

    SetMinHeightSlide();

    $(window).on('resize', function(e) {
        SetMinHeightSlide();
        setCarouselSliders();

        if(window.innerWidth <= 800 && mySwiper.activeIndex === 1) {
            $('.header-up').addClass('hidden');
        }
    });

    function SetMinHeightSlide() {
        var height = window.innerHeight;

        if(window.innerWidth <= 800) {
            $('.swiper-container').css('min-height', height - 55 + 'px');
        } else {
            $('.swiper-container').css('min-height', height - 66 + 'px');
        }
    }

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

    function setCarouselSliders() {

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
    }

    /**
     * swiper functions
     */
    function onSlideChangeStart(swiper) {
        
        var index =  swiper.activeIndex;

        index = index === 0 ? 7 : index;
        index = index === 8 ? 1 : index;

        console.log(index)
        if(index == 1) { $('#hand').removeClass('hidden');}
        else { $('#hand').addClass('hidden');}

        menuItems.removeClass('active');
        $( menuItems[index - 2]).addClass('active');

        backgroundNode.children().removeClass('active');
        $( backgroundNode.children()[index - 1]).addClass('active');

        if(index == 1 || index == 8) {

            $('#hand').removeClass('hidden');

        } else {
            $('#hand').addClass('hidden');
        }

         if(window.innerWidth <= 800) {
            if(index == 1 || index == 8) {
                $('.header-up').addClass('hidden');
            } else {
                $('.header-up').removeClass('hidden');
            }
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
       if(e.target.dataset.num) {
           goToPage(e.target.dataset.num);
       }
    });

    function goToPage(num) {
         mySwiper.slideTo(num);
         afterSlidePage();

        if(window.innerWidth <= 800) {
            if(num == 1 || num == 8) {
                $('.header-up').addClass('hidden');
            } else {
                $('.header-up').removeClass('hidden');
            }
        }
    }
    /**
     * header functions
     */

    function goToSlide1Mobile(e) {
         $('.header-up').addClass('hidden');
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




