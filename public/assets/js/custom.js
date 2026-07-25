// banner top fade

AOS.init({

   // Global settings:
   disable: false, // accepts following values: 'phone', 'tablet', 'mobile', boolean, expression or function
   startEvent: 'DOMContentLoaded', // name of the event dispatched on the document, that AOS should initialize on
   initClassName: 'aos-init', // class applied after initialization
   animatedClassName: 'aos-animate', // class applied on animation
   useClassNames: false, // if true, will add content of `data-aos` as classes on scroll
   disableMutationObserver: false, // disables automatic mutations' detections (advanced)
   debounceDelay: 50, // the delay on debounce used while resizing window (advanced)
   throttleDelay: 99, // the delay on throttle used while scrolling the page (advanced)

   // Settings that can be overridden on per-element basis, by `data-aos-*` attributes:
   offset: 120, // offset (in px) from the original trigger point
   delay: 0, // values from 0 to 3000, with step 50ms
   duration: 400, // values from 0 to 3000, with step 50ms
   easing: 'ease', // default easing for AOS animations
   once: false, // whether animation should happen only once - while scrolling down
   mirror: false, // whether elements should animate out while scrolling past them
   anchorPlacement: 'top-bottom', // defines which position of the element regarding to window should trigger the animation
});


$(document).ready(function () {
   $(window).scroll(function () {
      var scroll = $(window).scrollTop();
      var top = -scroll + 'px';
      $('.home-banner').css("opacity", 1 - scroll / 700);
      $('.banner-details').css({
         "transform": 'translate(' + 0 + ' , ' + top + ')'
      });
   })
})

// banner top fade end

// lab results 

$(document).ready(function () {
   $('.view_result').click(function () {
      let pdfname = $(this).data('pdffile')
      // let imagename = $(this).data('imagename')
      // console.log(pdfname,imagename);
      $('.view_result').attr('href', 'img/lab-results/' + pdfname)
      // $('.lbrImg').attr('src','img/lab-results/'+imagename)
   })
})

// lab results end

$('select').niceSelect();

// flover filter 

var filterActive;

function filterCategory(category) {
   if (filterActive != category) {

      // reset results list
      $('.flavorsBox').hide();

      // elements to be filtered
      $('.flavorsBox')
         .filter('[data-cat="' + category + '"]').show();
      // .addClass('active');

      // reset active filter
      filterActive = category;
   }
}

$('.flavorsBox').show();

$('.flavorsFiltering select').change(function () {
   if ($(this).val() == 'AllFlavors') {
      $('.flavorsBox').show();
      filterActive = 'AllFlavors';
   } else {
      filterCategory($(this).val());
   }
});

// flover filter 

// parallax bg 

gsap.registerPlugin(ScrollTrigger);
let getRatio = el => window.innerHeight / (window.innerHeight + el.offsetHeight);
gsap.utils.toArray("section").forEach((section, i) => {
   section.bg = section.querySelector(".parallax-bg");
   gsap.fromTo(section.bg, {
      backgroundPosition: () => i ? `50% ${-window.innerHeight * getRatio(section)}px` : "50% 0px"
   }, {
      backgroundPosition: () => `50% ${window.innerHeight * (1 - getRatio(section))}px`,
      ease: "none",
      scrollTrigger: {
         trigger: section,
         start: () => i ? "top bottom" : "top top",
         end: "bottom top",
         scrub: true,
         invalidateOnRefresh: true
      }
   });
});

// parallax bg



// Show Popup 10th jan Code

$(document).ready(function ($) {

   if (sessionStorage.getItem('advertOnce') !== 'true') {
      //sessionStorage.setItem('advertOnce','true');
      setTimeout(function () {
         $('#staticBackdrop').addClass('show').show();
         $('.modal-backdrop').addClass('fade show').show();
      }, 1000);

   } else {
      $('#staticBackdrop').addClass('show').hide();
      $('.modal-backdrop').removeClass('fade show').hide();
   };

   $('#btn-alpha').click(function () {
      sessionStorage.setItem('advertOnce', 'true');
      $('#staticBackdrop').removeClass('show').hide();
      $('.modal-backdrop').removeClass('fade show').hide();
   });

   $('#btn-beta').click(function () {
      window.location.href = 'http://google.com/';
   });
});

