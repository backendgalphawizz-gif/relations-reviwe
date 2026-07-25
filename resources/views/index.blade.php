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

   <section class="home-banner">
      <div class="homeVideo">
         <video autoplay loop muted playsinline>
            <source src="{{ asset('assets') }}/video/sugarrushvdeo.mp4" type="video/mp4">
         </video>
      </div>
      <div class="banner-details">
         <div class="brandLogo logo">
            <img src="{{ asset('assets') }}/img/suger-rushLogo.png"
               class="animateCount animate__animated animate__jello animate__delay-1s animate__slow-2 overlay" alt=""
               id="logoAnimate">
         </div>
         <div class="banner-text">
            <h2>REVIVE, REKINDLE, RECONNECT – NURTURE STRONGER BONDS, TOGETHER</h2>
            <p>Let’s begin the journey towards a better tomorrow…
            </p>
         </div>
         <a data-bs-toggle="modal" data-bs-target="#discoverModal" class="ThemeBtn animate__animated animate__zoomInDown animate__delay-2s animate__slow-2s"
            href=""><span>Discover More</span></a>
      </div>

      <!-- Modal -->
      <div class="modal fade" id="discoverModal" tabindex="-1" aria-labelledby="discoverModalLabel" aria-hidden="true">
         <div class="modal-dialog discoverModal">
            <div class="modal-content">
               <div class="modal-header">
                  <h1 class="modal-title fs-5" id="discoverModalLabel">More of Relationship Revive</h1>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>
               <div class="modal-body">
                  <div class="discoverDiv">
                     Relationships aren’t perfect — but with the right support, they can be beautiful again.
                     At Relationship Revive, our certified coaches help you navigate emotional distance, rebuild trust, and deepen your bond. Whether you’re married, dating, or struggling solo — our mission is simple: <span>Revive, Rekindle, Reconnect.</span> Because strong, fulfilling relationships are the foundation of a happier life.

                  </div>
                  <div class="discoverLink">
                     Download the app to Reignite Love. Rekindle Trust. Reconnect Deeply (available in iOS and Google Play Store)
                     <div class="playImg">
                        <a href="https://play.google.com/store/apps/details?id=com.relationshiprevive.app&hl=en_IN"><img src="{{ asset('assets') }}/img/playIcon.webp" alt=""></a>
                        <a href="#" ><img  src="{{ asset('assets') }}/img/appStore.png" alt=""></a>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </section>


   <section class="custom-space bg-lightgray overflow-hidden designerDiamond-container">
      <div class="container">
         <div class="row align-items-center gy-5">
            <div class="col-lg-4 col-md-6 col-12">
               <ul class="DD-effect">
                  <li class="center-main">
                     <img src="{{ asset('assets') }}/img/relationMobile.png" alt="" data-aos="zoom-in" data-aos-duration="2000">
                  </li>


               </ul>
            </div>

            <div class="col-lg-8 col-md-6 col-12">
               <div class="concentrates-description" data-aos="fade-left" data-aos-duration="2000">
                  <div class="headTitle">
                     <h2 class="themeSecondColor">REIGNITE LOVE. REKINDLE TRUST. RECONNECT DEEPLY.
                     </h2>
                     <p>Stronger Bonds Start Here — Guided by Certified Relationship Coaches
                     </p>
                     <div class="aboutListClass">
                        <h4>Welcome to Relationship Revive </h4>
                        <p>Your relationship deserves a second chance — a chance to grow, heal, and thrive. Whether you're struggling with communication, emotional distance, or rebuilding trust, our expert relationship coaches are here to guide you.
                        </p>
                     </div>
                     <div class="aboutColDiv">
                        <img src="" alt="">
                        <span>Personalized Coaching | Proven Strategies | Confidential Support</span>
                     </div>
                     <div class="issueHeadDiv aboutListContainer">
                        <h6>What We Help You Achieve:</h6>
                        <div class="issuePointDiv">
                           <li>
                              <span><img src="{{ asset('assets') }}/img/issueIcon7.png" alt=""></span>
                              <div>
                                 <h6>Communicate with Love, Not Conflict</h6>
                                 <p>Learn the art of listening, expressing, and resolving — without blame or judgment.
                                 </p>

                              </div>
                           </li>
                           <li>
                              <span><img src="{{ asset('assets') }}/img/issueIcon2.png" alt=""></span>
                              <div>
                                 <h6>Heal Emotional Wounds & Betrayals</h6>
                                 <p>Professional guidance to rebuild trust and rediscover intimacy.</p>
                              </div>
                           </li>
                           <li>
                              <span><img src="{{ asset('assets') }}/img/issueIcon1.png" alt=""></span>
                              <div>
                                 <h6>Reignite Romance & Connection</h6>
                                 <p>Bring back the spark — emotionally, physically, spiritually.</p>
                              </div>
                           </li>
                           <li>
                              <span><img src="{{ asset('assets') }}/img/issueIcon3.png" alt=""></span>
                              <div>
                                 <h6>Navigate Family & External Challenges</h6>
                                 <p>Strengthen your bond, no matter the pressures around you</p>
                              </div>
                           </li>
                           <li>
                              <span>
                                 <img src="{{ asset('assets') }}/img/issueIcon4.png" alt=""></span>

                              <div>
                                 <h6>Build Emotional Safety & Security</h6>
                                 <p>Create a relationship where both partners feel truly seen, heard, and valued</p>
                              </div>
                           </li>

                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </section>

   <section class="custom-space secretCartridges-container">
      <div class="container">
         <div class="row align-items-center">
            <div class="col-lg-5 col-md-5 col-12">
               <div class="secretCartridges-description" data-aos="fade-right" data-aos-duration="2000">
                  <div class="headTitle">
                     <h2>Unlock Emotional Mastery</h2>
                     <div class="title-divider">
                        <span></span>
                        <span></span>
                        <span></span>
                     </div>
                     <p>This is Your Safe Space to Heal and Grow Together
                        Are you…
                     </p>
                     <ul class="unlockListUL">
                        <li>Constantly arguing or feeling misunderstood?
                        </li>
                        <li>Distant from your partner?</li>
                        <li> Losing trust or struggling with infidelity?
                        </li>
                        <li>Feeling unseen, unheard, or unloved?
                        </li>
                     </ul>
                     <div class="unlockList">
                        <span>You’re not alone.</span> With us, you don’t just survive — you thrive. Our expert coaches help you uncover root causes, change patterns, and fall back in love — with yourself and each other.

                     </div>
                  </div>
               </div>
            </div>
            <div class="col-lg-7 col-md-7 col-12">
               <div class="secretSauce-product">
                  <div class="swiper twoProduct">
                     <div class="swiper-wrapper">


                        <div class="swiper-slide">
                           <div class="product-box" data-aos="flip-left" data-aos-duration="2000"
                              data-aos-easing="ease-out-cubic">
                              <div class="product-thumb">
                                 <img src="{{ asset('assets') }}/img/product-img/productImg5.jpg" alt="">
                              </div>
                              <div class="product-details">
                                 <h4>Renew Your Relationships</h4>
                              </div>
                           </div>
                        </div>

                        <div class="swiper-slide">
                           <div class="product-box" data-aos="flip-left" data-aos-duration="2000"
                              data-aos-easing="ease-out-cubic">
                              <div class="product-thumb">
                                 <img src="{{ asset('assets') }}/img/product-img/productImg6.jpg" alt="">
                              </div>
                              <div class="product-details">
                                 <h4>Empower Your Communication</h4>
                              </div>
                           </div>
                        </div>

                        <div class="swiper-slide">
                           <div class="product-box" data-aos="flip-left" data-aos-duration="2000"
                              data-aos-easing="ease-out-cubic">
                              <div class="product-thumb">
                                 <img src="{{ asset('assets') }}/img/product-img/grape-soda.png" alt="">
                              </div>
                              <div class="product-details">
                                 <h4>Strengthen Family Bonds</h4>
                              </div>
                           </div>
                        </div>


                     </div>
                     <div class="swiper-button-next"></div>
                     <div class="swiper-button-prev"></div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </section>

   <section class="custom-space contact-banner">
      <div class="parallax-bg"></div>
      <div class="container">
         <div class="row">
            <div class="col-lg-12">
               <div class="contact-description">
                  <div class="goContact" data-aos="fade-up-right" data-aos-duration="2000">
                     <div class="headTitle headTitle-center">
                        <h2 class="themeFirstColor">Discover Support for Stronger Connections</h2>
                        <h5>Join us today and unlock the potential of enriched relationships.</h5>
                        <div class="title-divider">
                           <span></span>
                           <span></span>
                           <span></span>
                        </div>
                        <ul class="list-style">
                           <li style="font-size: 20px;">Download the app to Reignite Love. Rekindle Trust. Reconnect Deeply (available in iOS and Google Play Store).</li>
                        </ul>
                        <div class="playImg">
                           <a href="https://play.google.com/store/apps/details?id=com.relationshiprevive.app&hl=en_IN"><img src="{{ asset('assets') }}/img/playIcon.webp" alt=""></a>
                           <a href="#" ><img  src="{{ asset('assets') }}/img/appStore.png" alt=""></a>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </section>

   <section class="custom-space diamondInfused-container overflow-hidden">
      <div class="container">
         <div class="row align-items-center">
            <div class="col-lg-6 col-md-6 col-12">
               <div class="diamondInfused-description" data-aos="slide-up" data-aos-duration="2000">
                  <div class="headTitle">
                     <h2>Master Relationship Challenges with Assurance</h2>
                     <div class="title-divider">
                        <span></span>
                        <span></span>
                        <span></span>
                     </div>
                     <p>Relationship Revive is dedicated to helping individuals and couples strengthen their relationships and build meaningful connections. Through personalized coaching services, we guide you to revive lost connections, rekindle love and trust, reconnect emotionally, and nurture stronger bonds with your loved ones. Our mission is to empower you to overcome challenges, foster healthier communication, and create a foundation of lasting harmony and joy in your relationships. Together, we’ll work to transform your bonds into thriving, supportive partnerships that stand the test of time.</p>

                     <div class="mssionImpPara">
                        <p>
                           Relationships play a vital role in both personal and professional life, helping us grow and thrive in all aspects.
                        </p>
                        <img src="{{ asset('assets') }}/img/heart.png" alt="">
                     </div>
                  </div>
                  <!-- <a class="ThemeBtn" href="diamond-infused-details.html"><span>Learn More</span></a> -->
               </div>
            </div>
            <div class="col-lg-6 col-md-6 col-12">
               <ul class="DIG-effect d-block d-md-none">
                  <li class="center-main">
                     <img src="{{ asset('assets') }}/img/DIG-animation/diamond.png" alt="" data-aos="zoom-in" data-aos-duration="2000">
                  </li>

               </ul>

               <ul class="DIG-effect d-none d-md-block">
                  <li class="center-main masterImage">
                     <img src="{{ asset('assets') }}/img/DIG-animation/diamond.png" alt="" data-aos="zoom-in" data-aos-duration="2000">
                  </li>

               </ul>
            </div>
         </div>
      </div>
   </section>

   <section class="relationshipSection">
      <div class="container">
         <h2>Do you want a Relationship <span>Get into it !</span></h2>
         <!-- <div class="relationCardDiv"> -->
         <div class="swiper relationShipSwiper">
            <div class="swiper-wrapper">
               <div class="swiper-slide widthAuto">
                  <div class="relationCard" style="background-image: url('/assets/img/relationCard3.png');">
                     <div class="relationOverlay">
                        <h3>Propose your Girlfriend</h3>
                        <p>Proposing to your girlfriend should be heartfelt, personal, and memorable. Choose a meaningful location, express your genuine feelings, and remind her why she’s so special to you</p>
                     </div>
                  </div>
               </div>
               <div class="swiper-slide widthAuto">
                  <div class="relationCard" style="background-image: url('/assets/img/relationCard4.jpg');">
                     <div class="relationOverlay">
                        <h3>Marry your Crush</h3>
                        <p>Marrying your crush requires building a strong and meaningful relationship first. Start by getting to know them better, expressing genuine interest, and creating a deep emotional connection. Build trust, support their dreams, and make them feel valued.</p>
                     </div>
                  </div>
               </div>
               <div class="swiper-slide widthAuto">
                  <div class="relationCard" style="background-image: url('/assets/img/relationCard1.jpg');">
                     <div class="relationOverlay">
                        <h3>Get your Girlfriend back</h3>
                        <p>Getting your girlfriend back requires patience, self-improvement, and sincere effort. Start by reflecting on what went wrong and take responsibility for any mistakes. Give her space to process emotions while focusing on becoming a better version of yourself.</p>
                     </div>
                  </div>
               </div>
               <div class="swiper-slide widthAuto">
                  <div class="relationCard" style="background-image: url('/assets/img/selfLove.jpg');">
                     <div class="relationOverlay">
                        <h3>Start Loving Your-Self</h3>
                        <p>Loving yourself is the foundation of a healthy and fulfilling life. When you prioritize self-love, you build confidence, set healthy boundaries, and attract positive relationships. It allows you to respect your own needs, embrace your strengths, and grow emotionally. </p>
                     </div>
                  </div>
               </div>
               <div class="swiper-slide widthAuto">
                  <div class="relationCard" style="background-image: url('/assets/img/thirdParty.jpg');">
                     <div class="relationOverlay">
                        <h3>Third Party Involvement in your Relations.</h3>
                        <p>Third-party involvement in a relationship can create distrust and tension, so the solution lies in clear communication and setting strong boundaries.</p>
                     </div>
                  </div>
               </div>
               <div class="swiper-slide widthAuto">
                  <div class="relationCard" style="background-image: url('/assets/img/listenImg.jpg');">
                     <div class="relationOverlay">
                        <h3>Is your Partner Doesn't Listen or Understand You</h3>
                        <p>Communication is the key to any strong relationship, but if your partner is not listening or understanding you, it can lead to frustration and emotional distance. In such situations, try expressing your feelings calmly and clearly, using "I" statements instead of blame.</p>
                     </div>
                  </div>
               </div>
               <div class="swiper-slide widthAuto">
                  <div class="relationCard" style="background-image: url('/assets/img/relationCard2.jpg');">
                     <div class="relationOverlay">
                        <h3>Find your soul mate</h3>
                        <p>Finding your soulmate is about more than just luck—it requires self-awareness, patience, and an open heart. Start by understanding yourself, your values, and what truly makes you happy. Engage in activities and environments where you can meet like-minded people who share your interests and beliefs.</p>
                     </div>
                  </div>
               </div>
               <div class="swiper-slide widthAuto">
                  <div class="relationCard" style="background-image: url('/assets/img/relationCard15.jpg');">
                     <div class="relationOverlay">
                        <h3>Sort Conflict in Relationship</h3>
                        <p>Sorting conflicts in a relationship requires open communication, patience, and a willingness to understand each other. Start by calmly discussing the issue without blaming or criticizing, focusing on feelings rather than accusations.</p>
                     </div>
                  </div>
               </div>
               <div class="swiper-slide widthAuto">
                  <div class="relationCard" style="background-image: url('/assets/img/relationCard17.png');">
                     <div class="relationOverlay">
                        <h3>Fighting with your Spouse Everyday?</h3>
                        <p>Fighting with your spouse every day often signals deeper issues, such as poor communication, unmet needs, or unresolved emotions. The solution begins with taking a step back and reflecting on the root cause of the conflicts.</p>
                     </div>
                  </div>
               </div>
               <div class="swiper-slide widthAuto">
                  <div class="relationCard" style="background-image: url('/assets/img/relationCard14.jpg');">
                     <div class="relationOverlay">
                        <h3>Trouble Relationship with your Partner</h3>
                        <p>If you're facing trouble in your relationship with your partner, the solution lies in open and honest communication. Start by addressing the underlying issues without blaming or accusing, and listen to each other’s feelings and concerns. </p>
                     </div>
                  </div>
               </div>
               <div class="swiper-slide widthAuto">
                  <div class="relationCard" style="background-image: url('/assets/img/relationCard11.jpg');">
                     <div class="relationOverlay">
                        <h3>Emotionally Unavailable with Partner</h3>
                        <p>If you or your partner are emotionally unavailable, the solution starts with understanding the root causes, such as past traumas, fear of vulnerability, or unresolved issues. It's important to create a safe and supportive space for open communication.</p>
                     </div>
                  </div>
               </div>
               <div class="swiper-slide widthAuto">
                  <div class="relationCard" style="background-image: url('/assets/img/relationCard12.jpg');">
                     <div class="relationOverlay">
                        <h3>In-Laws Interference in Marriage life.</h3>
                        <p>Dealing with in-laws' interference in marriage can be challenging, but the solution lies in setting clear, respectful boundaries while maintaining a united front with your spouse. Have an open conversation with your partner about the impact of the interference and agree on how to handle situations together.</p>
                     </div>
                  </div>

               </div>
               <div class="swiper-slide widthAuto">
                  <div class="relationCard" style="background-image: url('/assets/img/relationCard14.jpg');">
                     <div class="relationOverlay">
                        <h3>Unnecessary Family Issues</h3>
                        <p>Unnecessary family issues often arise from miscommunications, differing values, or unresolved conflicts. The solution starts with addressing the root causes by encouraging open, honest conversations where everyone feels heard.</p>
                     </div>
                  </div>

               </div>
            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
         </div>
         <!-- </div> -->
      </div>
   </section>

   <section class="bioSection">
      <div class="container bioContainer" style="background-image: url('/assets/img/relationBg1.png');">
         <div class="col-md-8 bioHead">
            <h4>The Core of Who I Am</h4>
            <p>
               My name is Neelu Taneja and I am here to fulfill a purpose. My
               purpose focuses specifically on helping individuals or couples
               improve their relationships, whether you’re seeking to enhance your
               romantic relationship, resolve conflicts, address communication
               issues or require support for your overall relationship skills. I am
               here to guide and support people in saving their relationships, and
               growing & strengthening their bonds. Life is a beautiful gift and
               together we can embark on this journey of self-discovery. We will
               explore your challenges, strengths, aspirations and weaknesses, and
               learn to live life.
            </p>
            <p>
               I believe that each person and relationship is unique, but the core
               feeling of hurt, fear and anxiety is mutual in every individual who
               is struggling to keep their relationship afloat.
            </p>
            <p>
               Neelu Taneja is a first-time author and working on being a rising
               voice in the field of personal development and self-help. With a
               background in diverse areas of work and a deep passion for helping
               individuals lead more fulfilling lives, Neelu’s debut book reflects
               her commitment to empowering readers with practical tools for
               personal growth and transformation.
            </p>
            <div class="customName">
               NEELU TANEJA
               <span>Relationship Coach</span>
            </div>
         </div>
         <div class="col-md-4 bioImg">
            <img src="{{ asset('assets') }}/img/bioImg5.png" alt="" />
         </div>
      </div>
   </section>

   <section class="testimonialSection">
      <div class="container">
         <h3>Hear Their Stories: <span>Real Experiences, Real Impact!</span></h3>
         <div class="swiper reviewSlider">
            <div class="swiper-wrapper reviewSliderWrapper">
               <div class="swiper-slide widthAuto widthFull">

                  <div class="reviewCard">
                     <div class="reviewHead">
                        <img src="{{ asset('assets') }}/img/reviewImg1.png" alt="">
                        <div>
                           <h6>Sanju Pathak</h6>
                           <span>
                              <img src="{{ asset('assets') }}/img/star.png" alt="">
                              <img src="{{ asset('assets') }}/img/star.png" alt="">
                              <img src="{{ asset('assets') }}/img/star.png" alt="">
                              <img src="{{ asset('assets') }}/img/star.png" alt="">
                              <img src="{{ asset('assets') }}/img/star.png" alt="">
                           </span>
                        </div>
                        <img class="quoteImg" src="{{ asset('assets') }}/img/double-quotes.png" alt="">
                     </div>
                     <div class="reviewPara">
                        I was suffering from my relationship very badly then I attend the session with Neelu mam. I got excellent result in my life. Thank you Neelu mam. I recommend to everyone those who are suffering from relationship
                     </div>
                  </div>
               </div>
               <div class="swiper-slide widthAuto widthFull">
                  <div class="reviewCard">
                     <div class="reviewHead">
                        <img src="{{ asset('assets') }}/img/reviewImg3.png" alt="">
                        <div>
                           <h6>Shriya Sethi</h6>
                           <span>
                              <img src="{{ asset('assets') }}/img/star.png" alt="">
                              <img src="{{ asset('assets') }}/img/star.png" alt="">
                              <img src="{{ asset('assets') }}/img/star.png" alt="">
                              <img src="{{ asset('assets') }}/img/star.png" alt="">
                              <img src="{{ asset('assets') }}/img/star.png" alt="">
                           </span>
                        </div>
                        <img class="quoteImg" src="{{ asset('assets') }}/img/double-quotes.png" alt="">
                     </div>
                     <div class="reviewPara">
                        "My experience with Neelu ma'am has been wonderful. When I met her, my struggled seemed to not end only but her guidance and support really made my relationship much stronger. Thank you for taking so much pain for me."
                     </div>
                  </div>
               </div>
               <div class="swiper-slide widthAuto widthFull">
                  <div class="reviewCard">
                     <div class="reviewHead">
                        <img src="{{ asset('assets') }}/img/reviewImg2.png" alt="">
                        <div>
                           <h6>Suryakant Panda</h6>
                           <span>
                              <img src="{{ asset('assets') }}/img/star.png" alt="">
                              <img src="{{ asset('assets') }}/img/star.png" alt="">
                              <img src="{{ asset('assets') }}/img/star.png" alt="">
                              <img src="{{ asset('assets') }}/img/star.png" alt="">
                              <img src="{{ asset('assets') }}/img/star.png" alt="">
                           </span>
                        </div>
                        <img class="quoteImg" src="{{ asset('assets') }}/img/double-quotes.png" alt="">
                     </div>
                     <div class="reviewPara">
                        I have been dealing with depression for a couple of years now and it started to strain my relationship with my husband. No matter how much I tried, I couldn’t seem to make things better. I met Neelu when I was at the verge of giving up. She provided me with insightful perspectives and created a safe space for open and honest communication. I felt a shift in my behaviour and thoughts in just a few sessions. I’m really grateful for her empathetic approach and creating such a deep connection with me. Our sessions together have really improved my relationship and strengthened my bond with my husband.
                     </div>
                  </div>
               </div>

            </div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
         </div>

      </div>
   </section>

   <section class="awardSection">
      <div class="container">
         <h3>Some Collection of Awards to <span>Mrs. Neelu Taneja</span></h3>
         <div class="awardImg">
            <img src="{{ asset('assets') }}/img/Picture1.png" alt="">
            <img src="{{ asset('assets') }}/img/Picture2.png" alt="">
            <img src="{{ asset('assets') }}/img/Picture3.jpg" alt="">
         </div>
         <div class="podcastDiv">
            <h3>Podcast with <span>Mrs. Neelu Taneja</span></h3>
            <div>
               <iframe height="315" src="https://www.youtube.com/embed/z6bfx0-3Rss?si=Hb0nCntKk-ntATwO" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
               <iframe height="315" src="https://www.youtube.com/embed/IWlJjFmqFhw?si=spNYjHn5A72nBHas" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
            </div>
         </div>
      </div>
   </section>

   <section class="issueSection">
      <div class="container issueContainer">
         <div class="col-md-7 issueHeadDiv">
            <h2>threat for a healthy Relationship</h2>
            <p>Protect your beloved relation from silly missunderstanding and misconceptions, consult with us for further information.</p>
            <div class="issuePointDiv">
               <li>
                  <span><img src="{{ asset('assets') }}/img/issueIcon7.png" alt=""></span>
                  <div>
                     <h6>Third Party Involvement in your Relations.</h6>
                     <p>Third-party involvement in a relationship can create distrust and tension, so the solution lies in clear communication and setting strong boundaries.</p>

                  </div>
               </li>
               <li>
                  <span><img src="{{ asset('assets') }}/img/issueIcon2.png" alt=""></span>
                  <div>
                     <h6>In-Law's Interference in Relationship.</h6>
                     <p>In-law interference in a relationship can create tension, but the solution lies in establishing clear boundaries with respect and understanding.</p>
                  </div>
               </li>
               <li>
                  <span><img src="{{ asset('assets') }}/img/issueIcon1.png" alt=""></span>
                  <div>
                     <h6>Facing Trouble with partners in a Relationship.</h6>
                     <p>Facing trouble with your partner in a relationship requires open communication, patience, and a willingness to work through challenges together.</p>
                  </div>
               </li>
               <li>
                  <span><img src="{{ asset('assets') }}/img/issueIcon3.png" alt=""></span>
                  <div>
                     <h6>Emotionally unavailable with partner.</h6>
                     <p>If you or your partner are emotionally unavailable, the solution begins with fostering open, honest communication.</p>
                  </div>
               </li>
               <li>
                  <span>
                     <img src="{{ asset('assets') }}/img/issueIcon4.png" alt=""></span>

                  <div>
                     <h6>Commitment issues in Relationship</h6>
                     <p>Commitment issues in a relationship often stem from fear of vulnerability, past experiences, or uncertainty about the future. The solution lies in addressing these fears through open, honest communication</p>
                  </div>
               </li>
               <li>
                  <span><img src="{{ asset('assets') }}/img/issueIcon6.png" alt=""></span>
                  <div>
                     <h6>Bad Relation with Boss.</h6>
                     <p>A bad relationship with your boss can create stress and hinder your professional growth, but the solution lies in improving communication, professionalism, and understanding. </p>
                  </div>
               </li>
            </div>
            <div class="issueBottom">
               <p>If you faceing these type of Problems, Be free to cunsult with us !</p>
               <a href="" data-bs-toggle="modal" data-bs-target="#connectUs" class="ThemeBtn animate__animated animate__zoomInDown animate__delay-2s animate__slow-2s mt-3" style="cursor: pointer;"><span>Connect with us</span></a>
            </div>
         </div>
         <div class="col-md-5 issueImgDiv">
            <img src="{{ asset('assets') }}/img/issueImg.jpg" alt="">
         </div>
      </div>
      <div class="modal fade" id="connectUs" tabindex="-1" aria-labelledby="connectUsLabel" aria-hidden="true">
         <div class="modal-dialog connectUs">
            <div class="modal-content">
               <div class="modal-header">
                  <h1 class="modal-title fs-5" id="connectUsLabel">More of Relationship Revive</h1>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>
               <div class="modal-body">
                  <div class="discoverDiv">
                     Relationships aren’t perfect — but with the right support, they can be beautiful again.
                     At Relationship Revive, our certified coaches help you navigate emotional distance, rebuild trust, and deepen your bond. Whether you’re married, dating, or struggling solo — our mission is simple: <span>Revive, Rekindle, Reconnect.</span> Because strong, fulfilling relationships are the foundation of a happier life.

                  </div>
                  <div class="discoverLink">
                     Download the app to Reignite Love. Rekindle Trust. Reconnect Deeply (available in iOS and Google Play Store)
                     <div class="playImg">
                        <a href="https://play.google.com/store/apps/details?id=com.relationshiprevive.app&hl=en_IN"><img src="{{ asset('assets') }}/img/playIcon.webp" alt=""></a>
                        <a href="#" ><img  src="{{ asset('assets') }}/img/appStore.png" alt=""></a>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </section>

   <section>
      <div class="container ">
         <div class="importantRole">
            <div class="overlayImportant">
               <h2>Relationships play a crucial role in our lives, shaping our emotions, growth, and overall well-being.</h2>
               <p>They provide love, support, and companionship, helping us navigate both joys and challenges. A healthy relationship fosters trust, understanding, and emotional security, allowing individuals to feel valued and connected. Whether with family, friends, or a partner, strong relationships contribute to happiness and personal development. </p>
            </div>
         </div>
      </div>
   </section>

   <section class="deprassionSection">
      <div class="container depressionContainer">

         <div class="col-md-5 deprassionImg"><img src="{{ asset('assets') }}/img/deprassionImg.png" alt=""></div>
         <div class="col-md-7 depressionHead">
            <h2>If you have Anxiety and Depression through a <span>Relationship !</span></h2>
            <p>Anxiety and depression in a relationship can stem from unresolved conflicts, lack of communication, or emotional neglect. When one or both partners struggle with these mental health challenges, it can lead to misunderstandings, withdrawal, and feelings of loneliness.
               Constant overthinking, fear of abandonment, or past traumas may create insecurity, making it hard to trust and connect. Without proper support, these issues can strain the bond, leading to further emotional distance. However, open communication, patience, and mutual understanding can help create a safe space where both partners feel valued and supported.
            </p>
            <a data-bs-toggle="modal" href="" data-bs-target="#getRelief" class="ThemeBtn animate__animated animate__zoomInDown animate__delay-2s animate__slow-2s mt-4"><span>Get Relief</span></a>

         </div>
      </div>
      <div class="modal fade" id="getRelief" tabindex="-1" aria-labelledby="getReliefLabel" aria-hidden="true">
         <div class="modal-dialog getRelief">
            <div class="modal-content">
               <div class="modal-header">
                  <h1 class="modal-title fs-5" id="getReliefLabel">More of Relationship Revive</h1>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>
               <div class="modal-body">
                  <div class="discoverDiv">
                     Relationships aren’t perfect — but with the right support, they can be beautiful again.
                     At Relationship Revive, our certified coaches help you navigate emotional distance, rebuild trust, and deepen your bond. Whether you’re married, dating, or struggling solo — our mission is simple: <span>Revive, Rekindle, Reconnect.</span> Because strong, fulfilling relationships are the foundation of a happier life.

                  </div>
                  <div class="discoverLink">
                     Download the app to Reignite Love. Rekindle Trust. Reconnect Deeply (available in iOS and Google Play Store)
                     <div class="playImg">
                        <a href="https://play.google.com/store/apps/details?id=com.relationshiprevive.app&hl=en_IN"><img src="{{ asset('assets') }}/img/playIcon.webp" alt=""></a>
                        <a href="#" ><img  src="{{ asset('assets') }}/img/appStore.png" alt=""></a>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </section>


   <section class="missionSection">
      <div class="container missionContainer" style="background-image: url('/assets/img/relationBg4.png');">
         <h4>Purpose of Relationship Revive</h4>
         <p>
            Our mission is to inspire and empower individuals to believe in
            themselves, no matter the circumstances they face. As an NLP certified
            coach, I am dedicated to helping people overcome challenges in their
            relationships—whether it’s between partners, parents and children, or
            families—and guide them toward deeper understanding, connection, and
            healing. I believe that every person holds the strength within to rise
            above difficulties, rebuild trust, and nurture meaningful bonds.
         </p>
         <p>
            With the help of strong, healthy relationships, you can experience
            remarkable growth in both your personal and professional life. I am
            here to support you on that journey. My expertise will help you
            recognize your true potential and tap into your inner strength,
            allowing you to create lasting change, build meaningful connections,
            and live a life filled with love, confidence, and purpose.
         </p>
         <div class="mssionImpPara">
            <p>
               Together, we will turn struggles into stepping stones, unlocking the
               best version of yourself.
            </p>
            <img src="{{ asset('assets') }}/img/heart.png" alt="" />
         </div>
      </div>
   </section>

   <section class="impactSection">
      <div class="container impactContainer">
         <div class="col-md-7 ImpactHead">
            <h3 class="ImpactHeading">Impacts from a bad Relationship</h3>
            <p>
               A bad relationship can have profound impacts on both mental and
               physical health. It can lead to anxiety, depression, emotional
               exhaustion, and low self-esteem due to constant stress, conflicts,
               or emotional neglect
            </p>

            <div class="impactPointDiv">
               <li>
                  <span><img src="{{ asset('assets') }}/img/impactIcon1.png" alt="" /></span>
                  <div>
                     <h6>Mental Health Impact</h6>
                     <p>
                        Mental health can be deeply impacted in a relationship, especially when there are constant conflicts, emotional neglect, or lack of communication.
                     </p>
                  </div>
               </li>
               <li>
                  <span><img src="{{ asset('assets') }}/img/impactIcon2.png" alt="" /></span>
                  <div>
                     <h6>Emotional Impact</h6>
                     <p>
                        Emotional impact in a relationship can be profound, especially when there’s consistent emotional neglect, manipulation, or unresolved conflicts.
                     </p>
                  </div>
               </li>
               <li>
                  <span><img src="{{ asset('assets') }}/img/impactIcon3.png" alt="" /></span>
                  <div>
                     <h6>Physical Health Problems</h6>
                     <p>
                        The stress and emotional strain from a troubled relationship can significantly impact physical health. Chronic tension, anxiety, or depression can weaken the immune system, leading to frequent illnesses, headaches, fatigue, and sleep disturbances.
                     </p>
                  </div>
               </li>
               <li>
                  <span><img src="{{ asset('assets') }}/img/impactIcon4.png" alt="" /></span>
                  <div>
                     <h6>Low Self-Esteem</h6>
                     <p>
                        Low self-esteem in a relationship can develop when one partner consistently feels unloved, unappreciated, or criticized. Constant negativity, belittling comments, or lack of emotional support can cause a person to doubt their worth, leading to feelings of inadequacy and insecurity.
                     </p>
                  </div>
               </li>
               <li>
                  <span><img src="{{ asset('assets') }}/img/impactIcon5.png" alt="" /></span>
                  <div>
                     <h6>Social Isolation</h6>
                     <p>
                        Social isolation in a relationship can occur when one or both partners withdraw from family, friends, or social activities due to unhealthy dynamics or controlling behavior.
                     </p>
                  </div>
               </li>
            </div>
         </div>
         <div class="col-md-5 impactImg">
            <img src="{{ asset('assets') }}/img/impactImg1.png" alt="" />
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
   <script>
      const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
      const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
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