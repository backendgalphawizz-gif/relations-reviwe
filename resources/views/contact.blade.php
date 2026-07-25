<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="theme-color" content="#05a5ca">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
   <!-- <meta name="viewport" content="width=device-width, initial-scale=1"> -->
   <title>Relationship Revive</title>
   <link rel="shortcut icon" href="{{ asset('assets') }}/img/shortlogo.png" type="image/png" />
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

   <section class="custom-space overflow-hidden">
      <div class="container">
         <div class="row align-items-center gy-4 flex-column-reverse flex-md-row">
            <div class="col-lg-4 col-md-5 col-12">
               <div class="imgBox shadow-lg rounded-4" data-aos="fade-right" data-aos-duration="2000">
                  <img src="{{ asset('assets') }}/img/contactImg.png" class="rounded-5" alt="">
               </div>
            </div>

            <div class="col-lg-8 col-md-7 col-12">
               <div class="OpportunityForm" data-aos="fade-left" data-aos-duration="2000">
                  <div class="headTitle headTitle-center" data-aos="fade-left" data-aos-duration="2000">
                     <h3>Be part of the <strong>Reationship Revive</strong> Family</h3>
                     <!-- <div class="title-divider">
                        <span></span>
                        <span></span>
                        <span></span>
                     </div> -->
                     <p>Join Us as a Relationship Advisor and Foster Healthier, Happier Connections
                        Make a Meaningful Impact in Society by Guiding Relationships with Care and Expertise
                     </p>
                     <div class="playImg">
                        <a href="https://play.google.com/store/apps/details?id=com.relationship_app"><img src="{{ asset('assets') }}/img/playIcon.webp" alt=""></a>
                        <a href="#"><img src="{{ asset('assets') }}/img/appStore.png" alt=""></a>
                     </div>
                  </div>

                  <div class="row gy-3 mt-4">
                     <div class="col-lg-6 col-md-6 col-12">
                        <div class="form-group">
                           <input type="text" class="form-control" id="name" placeholder="Full Name">
                        </div>
                     </div>

                     <div class="col-lg-6 col-md-6 col-12">
                        <div class="form-group">
                           <input type="number" class="form-control" id="name" placeholder="Phone Number">
                        </div>
                     </div>

                     <div class="col-lg-12">
                        <div class="form-group">
                           <input type="email" class="form-control" id="name" placeholder="Enter Email">
                        </div>
                     </div>

                     <div class="col-lg-6 col-md-6 col-12">
                        <div class="form-group">
                           <input type="url" class="form-control" id="name" placeholder="Instagram">
                        </div>
                     </div>
                     <div class="col-lg-6 col-md-6 col-12">
                        <div class="form-group">
                           <input type="text" class="form-control" id="name" placeholder="City">
                        </div>
                     </div>

                    

                     <div class="col-lg-12">
                        <div class="form-group">
                           <textarea class="form-control" id="exampleFormControlTextarea1" rows="4"
                              placeholder="Comment"></textarea>
                        </div>
                     </div>

                     <div class="col-lg-12">
                        <div class="btn-block text-center">
                           <button type="submit" class="ThemeBtn btn-secondary"><span>Submit</span></button>
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