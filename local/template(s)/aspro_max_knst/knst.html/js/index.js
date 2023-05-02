$(document).ready(function(){
  $(".header-info__btn").click(function(){
    $(".header-info__row.hidden").css("display","flex");
    $(this).css("display","none");
    $(".header-info__btn-hidden").css("display","inline-block");
  })
  $(".header-info__btn-hidden").click(function(){
    $(".header-info__row.hidden").css("display","none");
    $(this).css("display","none");
    $(".header-info__btn").css("display","inline-block");
  })

  $(".header-nav__item--blue").click(function(){
    $(".catalog-menu").css("display","flex");
  })
  $(".catalog-menu__close").click(function(){
    $(".catalog-menu").css("display","none");
  })

  $('.recomend__slider').slick({
    slidesToShow: 5,
    slidesToScroll: 1,
    centerMode: true,
    centerPadding: '60px',
    prevArrow: '<p class="arrow arrow-left"><img src="images/productleft.svg" alt=""></p>',
    nextArrow: '<p class="arrow arrow-right"><img src="images/productright.svg" alt=""></p>',
    responsive: [
      {
        breakpoint: 1919,
        settings:{
          slidesToShow: 5,
          centerPadding: '60px',
        }
      },
      {
        breakpoint: 1359,
        settings:{
          slidesToShow: 4,
          centerPadding: '60px',
        }
      },
      {
        breakpoint: 1023,
        settings:{
          slidesToShow: 3,
          centerPadding: '60px',
          arrows:false,
        }
      },
      {
        breakpoint: 767,
        settings:{
          slidesToShow: 1,
          centerPadding: '60px',
          arrows:false,
        }
      }
    ]
  });

  $('.recomend__slider.chair').slick({
    slidesToShow: 5,
    slidesToScroll: 1,
    centerMode: true,
    centerPadding: '60px',
    prevArrow: '<p class="arrow arrow-left"><img src="images/productleft.svg" alt=""></p>',
    nextArrow: '<p class="arrow arrow-right"><img src="images/productright.svg" alt=""></p>',
    responsive: [
      {
        breakpoint: 1919,
        settings:{
          slidesToShow: 5,
          centerPadding: '60px',
        }
      },
      {
        breakpoint: 1359,
        settings:{
          slidesToShow: 4,
          centerPadding: '60px',
        }
      },
      {
        breakpoint: 1023,
        settings:{
          slidesToShow: 3,
          centerPadding: '60px',
          arrows:false,
        }
      },
      {
        breakpoint: 767,
        settings:{
          slidesToShow: 1,
          centerPadding: '60px',
          arrows:false,
        }
      }
    ]
  });

  $(".news__slider").slick({
    slidesToShow: 4,
    slidesToScroll: 1,
    dots: false,
    arrows: true,
    prevArrow: '<p class="arrow-news arrow-left"><img src="images/productleft.svg" alt=""></p>',
    nextArrow: '<p class="arrow-news arrow-right"><img src="images/productright.svg" alt=""></p>',
    responsive: [
      {
        breakpoint: 1359,
        settings:{
          slidesToShow: 3,
        }
      },
      {
        breakpoint: 1023,
        settings:{
          slidesToShow: 2,
          arrows:false,
        }
      },
      {
        breakpoint: 767,
        settings:{
          slidesToShow: 1,
          arrows:false,
        }
      },
    ]
  });

});
