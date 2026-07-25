<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="theme-color" content="#05a5ca">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
   <!-- <meta name="viewport" content="width=device-width, initial-scale=1"> -->
   <title>Relationship Revive</title>
   <link rel="shortcut icon" href="img/shortlogo.png" type="image/png" />
   <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />
   <!-- Animation  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
   <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
   <!-- Animation  -->
   <link rel="stylesheet" href="{{ asset('assets') }}/css/nice-select.css">
   <link rel="stylesheet" href="{{ asset('assets') }}/css/range-slider.css">
   <link rel="stylesheet" href="{{ asset('assets') }}/css/global.css">
   <link rel="stylesheet" href="{{ asset('assets') }}/css/style.css">
   <link rel="stylesheet" href="{{ asset('assets') }}/css/responsive.css">
</head>



<body>

   <header>
      <nav class="navbar navbar-expand-lg">
         <div class="container">
            <div class="header-container">
               <a class="navbar-brand animate__animated animate__rubberBand animate__delay-1s" href="{{route('home')}}">
                  <img src="{{ asset('assets') }}/img/shortlogo.png" alt="">
               </a>
               <div class="navigation">
                  <div class="main-menu">
                     <div class="toggle-nav" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
                        <span class="togglemenu">
                           <span class="bar"></span>
                           <span class="bar"></span>
                           <span class="bar"></span>
                        </span>
                     </div>
                     <div class="collapse navbar-collapse" id="collapsibleNavbar">
                        <div class="device-brandLogo d-block d-lg-none">
                           <a class="" href="{{route('home')}}">
                              <img src="{{ asset('assets') }}/img/shortlogo.png" alt="">
                           </a>
                        </div>
                        <ul class="navbar-nav ms-auto">
                           <li class="nav-item">
                              <a class="nav-link" href="{{route('about')}}">About</a>
                           </li>
                           <li class="nav-item">
                              <a class="nav-link" href="{{route('contact')}}">Contact</a>
                           </li>
                           <li class="nav-item">
                              <a class="nav-link" href="{{route('faq')}}">FAQ</a>
                           </li>
                        </ul>
                     </div>
                     <div class="nav-overlay" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar"></div>
                  </div>
                  <div class="icon-menu">
                  <ul>
                     <li class="d-none d-md-block">
                           <a class="" href="https://www.instagram.com/relationshiprevive___/">
                              <img src="https://cdn-icons-png.flaticon.com/512/1384/1384015.png" alt="" class="img-icons">
                           </a>
                        </li>
                        <li class="d-none d-md-block">
                           <a class="" href="https://www.linkedin.com/in/relationship-revive-174626357/?trk=public-profile-join-page">
                              <img src="{{ asset('assets') }}/img/linkedin.png" alt="" class="img-icons">
                           </a>
                        </li>
                        <li class="d-none d-md-block">
                           <a class="" href="https://x.com/Relationsh30922">
                              <img src="https://cdn-icons-png.flaticon.com/512/733/733635.png" alt="" class="img-icons">
                           </a>
                        </li>
                        <li class="d-none d-md-block">
                           <a class="" href="https://www.youtube.com/@Relationship.Revive">
                              <img src="{{ asset('assets') }}/img/youtube.png" alt="" class="img-icons">
                           </a>
                        </li>
                        <li class="d-none d-md-block">
                           <a class="" href="https://www.facebook.com/profile.php?id=61572426069296">
                              <img src="https://cdn-icons-png.flaticon.com/512/1384/1384005.png" alt="" class="img-icons">
                           </a>
                        </li>
                        
                     </ul>
                  </div>
               </div>
            </div>
         </div>
      </nav>
   </header>
   <section class="fit-header"></section>

   <section class="faq custom-space grey">
      <div class="container">
         <div class="row">
            <div class="secHead text-center">
               <h2 class="themeSecondColor">Frequently Asked Questions</h2>
               <h6>The commom questions which are been asking to Relationship Revive</h6>
            </div>
         </div>
         <div class="row mt-5">
            <div class="col-12">
               <div class="accordion accordion-flush" id="accordionFlushExample">
                  <div class="accordion-item">
                     <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                        How does Relationship Revive work?
                        </button>
                     </h2>
                     <div id="flush-collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body">We connect you with certified relationship coaches who guide you through personalized sessions, helping you heal, rebuild trust, and nurture emotional intimacy.
                        </div>
                     </div>
                  </div>
                  <div class="accordion-item">
                     <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                        Is this service for couples only?

                        </button>
                     </h2>
                     <div id="flush-collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body">No! We work with individuals, couples, or even families — anyone seeking guidance to improve their relationships.
                        </div>
                     </div>
                  </div>
                  <div class="accordion-item">
                     <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseThree" aria-expanded="false" aria-controls="flush-collapseThree">
                        What issues can I get help with?

                        </button>
                     </h2>
                     <div id="flush-collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body">Communication gaps, emotional disconnect, infidelity recovery, relationship anxiety, commitment issues, family conflicts, and more.
                        </div>
                     </div>
                  </div>
                  <div class="accordion-item">
                     <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseFour" aria-expanded="false" aria-controls="flush-collapseFour">
                        Are sessions confidential?

                        </button>
                     </h2>
                     <div id="flush-collapseFour" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body">Yes. All sessions are 100% private and confidential. Your personal information is protected.
                        </div>
                     </div>
                  </div>
                  <div class="accordion-item">
                     <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseFive" aria-expanded="false" aria-controls="flush-collapseFive">
                        Do you offer online coaching?

                        </button>
                     </h2>
                     <div id="flush-collapseFive" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body">Absolutely! We offer flexible online sessions, making it convenient for you to join from anywhere.
                        </div>
                     </div>
                  </div>
                  <div class="accordion-item">
                     <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseSix" aria-expanded="false" aria-controls="flush-collapseSix">
                        How soon can I start?
                        </button>
                     </h2>
                     <div id="flush-collapseSix" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body">You can start immediately by downloading the app (provide the app link here) and recharging your wallet to connect to our relationship advisor for the consultation
                        .</div>
                     </div>
                  </div>
                  <div class="accordion-item">
                     <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseSeven" aria-expanded="false" aria-controls="flush-collapseSeven">
                        Is it possible to get consultation directly from Relationship Coach Neelu Taneja?

                        </button>
                     </h2>
                     <div id="flush-collapseSeven" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body">Yes, you need to share the basic details to the relationship advisor and they will fix an amicable time slot with Coach Neelu Taneja.
                        </div>
                     </div>
                  </div>

               </div>
            </div>
         </div>
      </div>
   </section>

   <section class="footerSection">
      <footer>
         <div class="container">
            <div class="row gy-3">
               <div class="col-lg-4 col-md-6 col-12">
                  <div class="Footer-AboutBrands">
                     <a class="navbar-brand" href="{{route('home')}}" data-aos="zoom-in" data-aos-duration="2000">
                        <img src="{{ asset('assets') }}/img/suger-rushLogo.png" alt="">
                     </a>

                  </div>
               </div>
               <div class="col-lg-2 col-md-3 col-12">
                  <div class="Footer-Col" data-aos="fade-left" data-aos-duration="2000">
                     <h5 class="">Quick Links</h5>
                     <ul>
                        <li><a href="{{route('home')}}">Home</a></li>
                        <li><a href="{{route('about')}}">About</a></li>
                        <li><a href="{{route('faq')}}">FAQ</a></li>
                        <li><a href="{{route('contact')}}">Contact Us</a></li>
                     </ul>
                  </div>
               </div>

               <div class="col-lg-4 col-md-12 col-12">
                  <div class="Footer-Col" data-aos="fade-left" data-aos-duration="2000">
                     <h5 class="">Location</h5>
                     <p>Tower-17-002, Orchid Petals, Sohna Road, Sector 49, Gurugram, Haryana 122018</p>
                  </div>
                  <div class="Footer-Col mt-4" data-aos="fade-left" data-aos-duration="2000">
                     <h5>Phone</h5>
                     <p>+91 85279 84701</p>
                  </div>

                  <div class="Footer-Col mt-4" data-aos="fade-left" data-aos-duration="2000">
                     <h5>Email</h5>
                     <p>support@relationship-revive.com</p>
                  </div>
               </div>
            </div>
         </div>
         <div class="container">
            <div class="row">
               <div class="col-lg-12">
                  <div class="SubFooter">
                     <p>Relationship Revive is under the copyright of Neelu World. Copyright reserved © 2025 </p>
                     <ul class="social-icons">
                        <li><a href="https://www.instagram.com/relationshiprevive___/"><img src="{{ asset('assets') }}/icon/instagram.svg" alt=""></a></li>
                        <li><a href="https://www.linkedin.com/in/relationship-revive-174626357/?trk=public-profile-join-page"><img src="{{ asset('assets') }}/img/linkedin.png" alt=""></a></li>
                        <li><a href="https://x.com/Relationsh30922"><img src="{{ asset('assets') }}/icon/twitter.svg" alt=""></a></li>
                        <li><a href="https://www.youtube.com/@Relationship.Revive"><img src="{{ asset('assets') }}/img/youtube.png" alt=""></a></li>
                        <li><a href="https://www.facebook.com/profile.php?id=61572426069296"><img src="{{ asset('assets') }}/icon/facebook.svg" alt=""></a></li>
                     </ul>
                  </div>
               </div>
            </div>
         </div>
      </footer>
   </section>

   <button id="scrollTotop">
      <div class="ScrollArrow">
         <i class="material-icons">keyboard_double_arrow_up</i>
      </div>
   </button>

   <!-- 10th jan Code -->

   <!-- 10th jan Code End -->

   <script>
      // Create an audio element
      let audio = new Audio("https://developmentalphawizz.com/html/romantic-orchestral-love.mp3");

      // Enable looping
      audio.loop = true;

      // Autoplay (some browsers may block autoplay)
      audio.play().catch(error => {
         console.log("Autoplay blocked by the browser:", error);
      });
   </script>

   <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.slim.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
   <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
   <script src="https://apps.elfsight.com/p/platform.js" defer></script>
   <script src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/16327/gsap-latest-beta.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.11.3/ScrollTrigger.min.js"></script>
   <script src="{{ asset('assets') }}/js/header.js"></script>
   <script src="{{ asset('assets') }}/js/slider.js"></script>
   <script src="{{ asset('assets') }}/js/scrolltop.js"></script>
   <script src="{{ asset('assets') }}/js/jquery.nice-select.min.js"></script>
   <script src="{{ asset('assets') }}/js/range-slider.js"></script>
   <script src="{{ asset('assets') }}/js/custom.js"></script>
</body>

</html>