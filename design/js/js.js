$(window).scroll(function() {    
    if ($(window).scrollTop()) {
        $(".top-header").addClass("h-0");
        $(".c-navbar").addClass("h-60");
    } else {
        $(".top-header").removeClass("h-0");
        $(".c-navbar").removeClass("h-60");
    };
});

var swiper = new Swiper(".swiper-certifications", {
    effect: "coverflow",
    grabCursor: true,
    centeredSlides: true,
    slidesPerView: "3",
    spaceBetween: 35,
    autoplay: {
        delay: 2500,
        disableOnInteraction: false,
    },
    coverflowEffect: {
      rotate: 50,
      stretch: 0,
      depth: 100,
      modifier: 1,
      slideShadows: true,
    },
    pagination: {
      el: ".swiper-pagination",
    },
    breakpoints: {
        // when window width is >= 320px
        320: {
          slidesPerView: 1,
          spaceBetween: 10
        },
        // when window width is >= 640px
        640: {
          spaceBetween: 20
        },
    },
});

var x = document.getElementsByClassName("slider-pt");

for(var i = 0; i < x.length; i++) {

    var el = x[i];
  
    var swiper = el.getElementsByClassName("swiper-container")[0];
    var nx = el.getElementsByClassName("swiper-next")[0];
    var pr = el.getElementsByClassName("swiper-prev")[0];

    new Swiper(swiper, {
        slidesPerView: 3,  
        spaceBetween: 30,
        autoHeight: false,
        loop: true,
        loopFillGroupWithBlank: true,
        navigation: {
          nextEl: nx,
          prevEl: pr
        },
        autoplay: {
            delay: 2500,
            disableOnInteraction: false,
        },
        breakpoints: {
            // when window width is >= 320px
            320: {
              slidesPerView: 1,
              spaceBetween: 10
            },
            // when window width is >= 480px
            480: {
              slidesPerView: 1,
              spaceBetween: 10
            },
            // when window width is >= 640px
            640: {
              slidesPerView: 2,
              spaceBetween: 20
            },
            // when window width is >= 640px
            767: {
              slidesPerView: 3,
              spaceBetween: 20
            },
        },
    });
}
