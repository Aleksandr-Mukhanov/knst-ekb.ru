jQuery(function ($) {

    const scrollbarWidth = window.innerWidth - $(document).width();

    $('[data-svg]').not('loaded').each(function () {
        var $i = $(this).addClass('loaded');

        $.get($i.data('svg'), function (data) {
            var $svg = $(data).find('svg');

            $svg.attr('class', $i.attr('class'));
            $i.replaceWith($svg);
        }, 'xml');
    });

    $('input[type="tel"],.phone').mask("+7 (999) 999-99-99");

    $(document).on('change', 'input:file', function () {
        var input = $(this);

        input.next('span').text(input.val().split(/(\\|\/)/g).pop());
    });

//    $('.product__title').matchHeight();
    $('.purchase-var__right .purchase-var__block').matchHeight();

    $('.price-about__slider-js1').slick({
        arrows: true,
        dots: false,
        slidesToShow: 1,
        slidesToScroll: 1,
        swipeToSlide: true,
        infinite: false,
        //        adaptiveHeight: true,
        fade: true,
        prevArrow: '.price-about__prev1',
        nextArrow: '.price-about__next1',
    });

    $('.price-about__slider-js2').slick({
        arrows: true,
        dots: false,
        slidesToShow: 1,
        slidesToScroll: 1,
        swipeToSlide: true,
        infinite: false,
        //        adaptiveHeight: true,
        fade: true,
        prevArrow: '.price-about__prev2',
        nextArrow: '.price-about__next2',
    });

    $('.price-about__slider-js3').slick({
        arrows: true,
        dots: false,
        slidesToShow: 1,
        slidesToScroll: 1,
        swipeToSlide: true,
        infinite: false,
        //        adaptiveHeight: true,
        fade: true,
        prevArrow: '.price-about__prev3',
        nextArrow: '.price-about__next3',
    });

    $('.letters-slider__js').slick({
        arrows: true,
        dots: false,
        slidesToShow: 4,
        slidesToScroll: 1,
        fade: false,
        swipeToSlide: true,
        infinite: false,
        adaptiveHeight: true,
        prevArrow: '.letters-slider__prev',
        nextArrow: '.letters-slider__next',
        responsive: [
            {
                breakpoint: 1023,
                settings: {
                    slidesToShow: 3,
                }
            },
            {
                breakpoint: 767,
                settings: {
                    slidesToShow: 2
                }
            }
          ]
    });


    $('.cities span').on('click', function (e) {
        e.preventDefault();
        $('.cities-modal').addClass('active');
        $("body").addClass('shadow-overlay1');
        if ($('body').hasClass('shadow-overlay1')) {

            $('body').css({
                'margin-right': scrollbarWidth + 'px'
            });
        } else {
            $('body').css('margin-right', '');
        };
    })


    $('.cities-modal__close').on('click', function (e) {
        $('.cities-modal').removeClass('active');
        $("body").removeClass('shadow-overlay1');
        if ($('body').hasClass('shadow-overlay1')) {

            $('body').css({
                'margin-right': scrollbarWidth + 'px'
            });

            $('.header').css({
                'padding-right': scrollbarWidth + 'px'
            });
        } else {
            $('body').css('margin-right', '');
            $('.header').css('padding-right', '');
        };
    })

    $('.header__cat-btn').on('click', function (e) {
        e.preventDefault();
        $(this).toggleClass('active');
        $('.cats-modal').toggleClass('active');
        $("body").toggleClass('shadow-overlay1');
        if ($('body').hasClass('shadow-overlay1')) {

            $('body').css({
                'margin-right': scrollbarWidth + 'px'
            });
        } else {
            $('body').css('margin-right', '');
        };
    })

    $(document).mouseup(function (e) { // событие клика по веб-документу
        var div = $(".cats-modal__block, .header__cat-btn, .cities-modal"); // тут указываем ID элемента
        if (!div.is(e.target) // если клик был не по нашему блоку
            &&
            div.has(e.target).length === 0) { // и не по его дочерним элементам
            $('.cats-modal').removeClass('active');
            $('.header__cat-btn').removeClass('active');
            $("body").removeClass('shadow-overlay1');
        }
        if ($('body').hasClass('shadow-overlay1')) {

            $('body').css({
                'margin-right': scrollbarWidth + 'px'
            });
        } else {
            $('body').css('margin-right', '');
        };
    });

    $('.header__hamburger').on('click', function (e) {
        e.preventDefault();
        $('.menu-modal').addClass('active');
        $("body").addClass('shadow-overlay1');
    })

    $('.menu-modal__close').on('click', function (e) {
        e.preventDefault();
        $('.menu-modal').removeClass('active');
        $("body").removeClass('shadow-overlay1');
    })

    $('.header__serch-btn').on('click', function (e) {
        e.preventDefault();
        $('.header .search').addClass('active');
        $("body").addClass('shadow-overlay');
    })

    $(document).mouseup(function (e) { // событие клика по веб-документу
        var div = $(".header__nav, .header .search"); // тут указываем ID элемента
        if (!div.is(e.target) // если клик был не по нашему блоку
            &&
            div.has(e.target).length === 0) { // и не по его дочерним элементам
            $('.header__nav, .header .search').removeClass('active');
            $("body").removeClass('shadow-overlay');
        }
        if ($('body').hasClass('shadow-overlay')) {

            $('body').css({
                'margin-right': scrollbarWidth + 'px'
            });
        } else {
            $('body').css('margin-right', '');
        };
    });

    $('.cats-modal-cat__inner').on('click', function (e) {
        e.preventDefault();
        $(this).parent().find('.cats-modal-cat__list').addClass('active');
    })

    $('.cats-modal-cat__lvl2 > .have-child > a').on('click', function (e) {
        e.preventDefault();
        $(this).siblings('dl').addClass('active');
    })

    $('.cats-modal-cat .arrow-back').on('click', function (e) {
        e.preventDefault();
        $(this).addClass('active');
        $(this).parent().parent().parent().removeClass('active');
        $(this).parent().parent().removeClass('active');
    })

    $('.cats-modal__close').on('click', function (e) {
        e.preventDefault();
        $('.cats-modal').removeClass('active');
        $(this).parent().parent().parent().removeClass('active');
        $(this).parent().parent().removeClass('active');
    })
});
