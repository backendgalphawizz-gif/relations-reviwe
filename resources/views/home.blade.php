@extends('layout.app')

@section('content')
    <section id="home"class="blog-page home" style="padding-top: 160px !important;">
        <div class="animation-circle">
            <i></i>
            <i></i>
            <i></i>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-8 homeleft">
                    <div class="d-flex-1">
                        <div class="slide-text">
                            <div>
                                <h1>The best way to connect with Astrologer is with
                                    <span class="main-title">hghfhgf!</span>
                                </h1>
                                <h4>Astrology is a predictive science with its own sets of methods, claims and findings that
                                    have
                                    forever inspired and allowed people with insights into different aspects of their life.
                                </h4>
                                <div class="slid-btn">
                                    <a [routerLink]="'/home'"><img src="/build/assets/images/app1.png" alt="app1"
                                            class="img-fluid" data-tilt data-tilt-perspective="50" data-tilt-speed="350"
                                            data-tilt-max="1.8"></a>
                                    <a [routerLink]="'/home'"><img src="/build/assets/images/app2.png" alt="app2"
                                            class="img-fluid" data-tilt data-tilt-perspective="50" data-tilt-speed="350"
                                            data-tilt-max="1.8"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="home-right">
                        <div class="mobile-slid">
                            <img src="https://i.ibb.co/6nxjr29/asthahome.jpg" alt="top1" class="img-fluid lefthomeimg">
                        </div>
                                          </div>
                </div>
            </div>
        </div>
    </section>
    <!-- about section-->
    <section id="about" class="pb-0 about" style="padding-top: 100px !important;">
        <div class="about-chat">
            <div class="container ">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <div class="section-title">
                            <h2>About AsthaJyotish</h2>
                            <img src="/build/assets/images/title-line.png" alt="title-line" class="img-fluid">
                        </div>
                    </div>
                    <div class="col-md-12 about-box">
                        <div class="row">
                            <div class="col-lg-3 col-sm-6 about-border">
                                <div class="chat-box">
                                    <img src="/build/assets/images/stay-connected.png" alt="stay-connected">
                                    <h3 class="sub-title"> Stay Connected</h3>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 about-border">
                                <div class="chat-box">
                                    <img src="/build/assets/images/get-notified.png" alt="get-notified">
                                    <h3 class="sub-title"> Get Notified </h3>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 about-border">
                                <div class="chat-box">
                                    <img src="/build/assets/images/stay-updated.png" alt="stay-updated">
                                    <h3 class="sub-title">Stay Updated</h3>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6 text-center">
                                <div class="chat-box">
                                    <img src="/build/assets/images/mini-world.png" alt="mini-world">
                                    <h3 class="sub-title">Mini World</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="chat-slide text-center">
                            <h3>Connect with Astrologer all over the world , with your small device</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container-fluid text-center">
            <img src="/build/assets/images/about.jpg"
                style="z-index: -1;
                position: relative;
                bottom: -80px;
                height: 630px;
                border-radius: 50px;
                border: 15px solid #383838;"
                alt="banner.png" class="img-fluid full-banner">
        </div>
    </section>
    <!-- Feature section -->
    <section class="theme-bg feature" id="feature"
        style="padding-top: 100px !important;padding-bottom: 100px !important;">
        <div class="container">
            <div class="animation-circle-inverse">
                <i></i>
                <i></i>
                <i></i>
            </div>
            <div class="row">
                <div class="col-md-12 text-center">
                    <div class="section-title">
                        <h2 class="text-white">AsthaJyotish Feature</h2>
                        <img src="/build/assets/images/white-line.png" alt="white-line" class="img-fluid">
                    </div>
                </div>
               
                <div class="col-lg-4 col-sm-6">
                    <div class="future-box">
                        <div class="future-timeline">
                            <ul>
                                <li class="timeline">
                                    <h4 class="sub-title">Easy Installation</h4>
                                    <p>Install & Register For Free</p>
                                </li>
                                <li class="timeline">
                                    <h4 class="sub-title">Daily Horoscope</h4>
                                    <p>Read Your Daily Horoscope For Free</p>
                                </li>
                                <li class="timeline">
                                    <h4 class="sub-title">Free Kundli</h4>
                                    <p>Generate Your Free Kundli With AsthaJyotish App.</p>
                                </li>
                                <li class="timeline">
                                    <h4 class="sub-title">Kundli Matching</h4>
                                    <p>Use our Kundli Matching Tool.</p>
                                </li>
                                <li class="timeline">
                                    <h4 class="sub-title">Live Astrologers</h4>
                                    <p>Watch Popular Astrologer Live & Ask Your Question.</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 future-mobile text-center"><img src="/build/assets/images/feature.jpg"
                        style="height: 600px;
                    border-radius: 50px;
                    border: 15px solid #383838;"alt="feature-mob"
                        class="img-fluid">
                </div>
                <div class="col-lg-4 col-sm-6">
                    <div class="future-box">
                        <div class="future-timeline-right">
                            <ul class="rightcontent">
                                <li class="timeline-right">
                                    <h4>Call and Chat with Astrologer</h4>
                                    <p>Connect With Experienced Astrologer.</p>
                                </li>
                                <li class="timeline-right">
                                    <h4>Astromall</h4>
                                    <p>Buy Any Astrology Related Product From AsthaJyotish App</p>
                                </li>
                                <li class="timeline-right">
                                    <h4>Astro Blogs</h4>
                                    <p>Read News & Blogs Related To Astrology</p>
                                </li>
                                <li class="timeline-right">
                                    <h4>Daily Panchang</h4>
                                    <p>Read Daily Panchang In AsthaJyotish App.</p>
                                </li>
                                <li class="timeline-right">
                                    <h4>Connect with Counsellor</h4>
                                    <p>We have best astrology counsellor from Nepal.</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="work" id="work" style="padding-top: 100px !important;padding-bottom: 100px !important;">
        <div class="container">
            <div class="row">
                <div class="col-md-8 offset-md-2 text-center">
                    <div class="section-title">
                        <h2>How AsthaJyotish Work</h2>
                        <img src="/build/assets/images/title-line.png" alt="white-line" class="img-fluid">
                        <p>Online Astrology Counselling Is New For Nepal. Ready How We Work To Make Your Life More Easier.</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4 text-center">
                    <div class="process-box">
                        <img src="/build/assets/images/icon/1.png" alt="" />
                        <h3>Download App</h3>
                        <p>You Can Download Our App From Google Play Store For Free.</p>
                    </div>
                </div>
                <div class="col-lg-4 text-center">
                    <div class="process-box">
                        <img src="/build/assets/images/icon/2.png" alt="" />
                        <h3>Connect Astrologer</h3>
                        <p>Register Yourself And Connect With Any Astrologery You Want.</p>
                    </div>
                </div>
                <div class="col-lg-4 text-center">
                    <div class="process-box">
                        <img src="/build/assets/images/icon/3.png" alt="" />
                        <h3>Enjoy App</h3>
                        <p>Enjoy Daily Rashifal, Panchang, Kundli In Free Of Cost.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section id="screenshots" class="theme-bg screenshots" style="padding-top: 100px !important;">
    </section>

    <!-- news section-->
    <section class="team-bg" id="testimonial" style="padding-top: 100px !important;padding-bottom: 70px !important;">
        <div class="animation-circle-inverse">
            <i></i>
            <i></i>
            <i></i>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-8 offset-md-2 text-center">
                    <div class="swiper mySwiper">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <div style="display: inline-block">
                                    <img class="object-cover w-full h-96" src="/build/assets/images/profile-3.jpg"
                                        alt="image" />
                                </div>
                                <div style="display: inline-block">
                                    <h4 style="color:#ffa500;text-align:left">Pradeep Airee</h4>
                                    <h6>Excellent, Deep, Easy. This could be a AsthaJyotish app, it's very accurate and I love
                                        the
                                        new chat feature they've added, I had been hoping they'd add it ever since I joined.
                                        The best feature by far though, is being able to add your friends on your own so you
                                        can look and compare without them making an account. Of course, I ask them to
                                        anyway, but if they are the type to think this kind of stuff skeptically, it doesn't
                                        stop you from getting some insight to their personalities! Love the app, thank you
                                    </h6>
                                </div>

                            </div>
                            <div class="swiper-slide">
                                <div style="display: inline-block">
                                    <img class="object-cover w-full h-96" src="/build/assets/images/profile-3.jpg"
                                        alt="image" />
                                </div>
                                <div style="display: inline-block">
                                    <h4 style="color:#ffa500;text-align:left">Rajkumar Bc</h4>
                                    <h6>Excellent, Deep, Easy. This could be a AsthaJyotish app, it's very accurate and I love
                                        the
                                        new chat feature they've added, I had been hoping they'd add it ever since I joined.
                                        The best feature by far though, is being able to add your friends on your own so you
                                        can look and compare without them making an account. Of course, I ask them to
                                        anyway, but if they are the type to think this kind of stuff skeptically, it doesn't
                                        stop you from getting some insight to their personalities! Love the app, thank you
                                    </h6>
                                </div>
                            </div>
                            <div class="swiper-slide">
                                <div style="display: inline-block">
                                    <img class="object-cover w-full h-96" src="/build/assets/images/profile-3.jpg"
                                        alt="image" />
                                </div>
                                <div style="display: inline-block">
                                    <h4 style="color:#ffa500;text-align:left">Kopila Rai</h4>
                                    <h6>Excellent, Deep, Easy. This could be a AsthaJyotish app, it's very accurate and I love
                                        the
                                        new chat feature they've added, I had been hoping they'd add it ever since I joined.
                                        The best feature by far though, is being able to add your friends on your own so you
                                        can look and compare without them making an account. Of course, I ask them to
                                        anyway, but if they are the type to think this kind of stuff skeptically, it doesn't
                                        stop you from getting some insight to their personalities! Love the app, thank you
                                    </h6>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
                <div class="col-md-12">
                </div>
            </div>
        </div>
    </section>
    <!-- end news section-->
    <!-- download section -->
    <section class="download-bg" style="padding-top: 40px !important;padding-bottom: 40px !important;">
        <div class="container">
            <div class="row download">
                <div class="col-xl-3 display-flex">
                    <div class="footer-logo">
                        <img src="/build/assets/images/astroTalk.png" style="width:100px;height:100px" alt="footer-logo"
                            class="img-fluid">
                    </div>
                </div>
                <div class="col-xl-5 downloadapp">
                    <div class="download-text">
                        <h3>Download the AsthaJyotish app and enjoy it</h3>
                    </div>
                </div>
                <div class="col-xl-4 downloadapp">
                    <div class="download-img">
                        <ul>
                            <li><img style="width:120px" src="/build/assets/images/app1.png" alt="foot-app"
                                    class="img-fluid" data-tilt data-tilt-perspective="50" data-tilt-speed="350"
                                    data-tilt-max="1.8"></li>
                            <li><img style="width:120px" src="/build/assets/images/app2.png" alt="foot-app1"
                                    class="img-fluid" data-tilt data-tilt-perspective="50" data-tilt-speed="350"
                                    data-tilt-max="1.8"></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- contact-section -->
    <section class="contact" id="contact" style="padding-top: 100px !important;">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="section-title">
                        <h2>Contact Info</h2>
                    </div>
                    <p>If you have any queries, Feel Free To Connect With Us.</p>
                    <ul class="contact-box">
                        <li>
                            <div class="contact-circle">
                                <i class="fa fa-map-marker" aria-hidden="true"></i>
                            </div>
                            <div class="contact-text">
                                <h3>Location</h3>
                                <p>Kathmandu, Nepal</p>
                            </div>
                        </li>
                        <li>
                            <div class="contact-circle">
                                <i class="fa fa-phone" aria-hidden="true"></i>
                            </div>
                            <div class="contact-text">
                                <h3>Call US</h3>
                                <p>+977 981-0646852</p>
                            </div>
                        </li>
                        <li>
                            <div class="contact-circle">
                                <i class="fa fa-envelope" aria-hidden="true"></i>
                            </div>
                            <div class="contact-text">
                                <h3>Email Us</h3>
                                <p>info@asthajyotish.com</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!-- end contact section -->
    <!-- end download section -->
    <!-- end Feature section -->
    <!-- end about section  -->
@endsection

<!-- end slider section -->
