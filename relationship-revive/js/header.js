// Hide Header on on scroll down
var didScroll;
var lastScrollTop = 0;
var delta = 5;
var navbarHeight = $('header').outerHeight();

$(window).scroll(function (event) {
   didScroll = true;
});

setInterval(function () {
   if (didScroll) {
      hasScrolled();
      didScroll = false;
   }
}, 250);

function hasScrolled() {
   var st = $(this).scrollTop();

   if (Math.abs(lastScrollTop - st) <= delta)
      return;

   if (st > lastScrollTop && st > navbarHeight) {

      $('header').removeClass('show-nav').addClass('hide-nav');

   } else {

      if (st + $(window).height() < $(document).height()) {
         $('header').removeClass('hide-nav').addClass('show-nav');
      }
   }

   lastScrollTop = st;
}

// toggale 

(function ($) {

   $(document).ready(function () {
      $('.toggle-nav').on('click', function () {
         toggleNavigation($(this), $(''));
         changeLetters($(this));
      });

      function toggleNavigation(btn) {
         btn.toggleClass('open');
      }
   });

})(jQuery);


$(window).scroll(function () {
   if ($(window).scrollTop() >= 150) {
      $('header').addClass('sticky-header');
   }
   else {
      $('header').removeClass('sticky-header');
   }
});