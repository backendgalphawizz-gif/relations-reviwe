// home 

var swiper = new Swiper(".twoProduct", {
    slidesPerView: 2,
    loop: true,
    pagination: {
        el: ".swiper-pagination",
        clickable: true,
    },
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },

    breakpoints: {
        320: {
            slidesPerView: 1,
        },

        480: {
            slidesPerView: 2,
        },

        768: {
            slidesPerView: 2,
        }
    }
});

var swiper = new Swiper(".threeProduct", {
    slidesPerView: 3,
    loop: true,
    pagination: {
        el: ".swiper-pagination",
        clickable: true,
    },

    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },
    breakpoints: {

        320: {
            slidesPerView: 1,
        },

        480: {
            slidesPerView: 2,
        },

        768: {
            slidesPerView: 3,
        }
    }
});