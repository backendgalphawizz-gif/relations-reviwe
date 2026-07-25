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
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

   <!-- Animation  -->
   <link rel="stylesheet" href="{{ asset('assets') }}/css/nice-select.css">
   <link rel="stylesheet" href="{{ asset('assets') }}/css/range-slider.css">
   <link rel="stylesheet" href="{{ asset('assets') }}/css/global.css">
   <link rel="stylesheet" href="{{ asset('assets') }}/css/style.css">
   <link rel="stylesheet" href="{{ asset('assets') }}/css/responsive.css">
</head>



<body>

   <header class="transparent-header">
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


   <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
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