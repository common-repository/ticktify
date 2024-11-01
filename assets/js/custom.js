jQuery(document).ready(function(){       
    jQuery('.fade').slick({
        dots: false,
        infinite: true,
        speed: 700,
        autoplay:false,
        autoplaySpeed: 2000,
        arrows:true,
        slidesToShow: 6,
        slidesToScroll: 1,
        responsive: [{
            breakpoint: 1024,
            settings: {
              slidesToShow: 6,
              slidesToScroll: 1,
              arrows: true,
              vertical: false,
            },
          }, {
            breakpoint: 600,
            settings: {
              slidesToShow: 4,
              slidesToScroll: 1,
              arrows: true,
              vertical: false,
            },
          }, {
            breakpoint: 480,
            settings: {
              slidesToShow: 1,
              slidesToScroll: 1,
              arrows: true,
              vertical: false,
            },
          }]
    });

});