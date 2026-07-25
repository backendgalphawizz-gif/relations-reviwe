<!DOCTYPE html>
<html lang="en">
<head>
    @php
        $logo = DB::table('systemflag')->where('name', 'AdminLogo')->select('value')->first();

        $appName = DB::table('systemflag')->where('name', 'AppName')->select('value')->first();
    @endphp

    <title>{{ $appName->value }}</title>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="//cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="//unpkg.com/swiper/swiper-bundle.min.css" />

    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <link as="image" fetchpriority="high" href="{{ $logo->value }}" rel="preload shortcut icon">

    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" defer>

    {{-- <style>

        .blog-page .news-slid .blog-hover h4 {

            color: #ffa500;

            margin-top: 5px;

            margin-bottom: 10px

        }



        p {

            line-height: 23px;

            font-size: 16px;

            color: #586082;

            letter-spacing: 0.05rem

        }



        .navbar-brand {

            font-size: 30px !important;

            color: #000;

            font-weight: 600;



        }



        .navbar {

            background: linear-gradient(to right, rgb(247, 214, 138) 0%, rgb(255, 165, 0) 100%)

        }



        .home {

            background: linear-gradient(to right, #ffd472 0%, #ffa500 100%);

            height: 100vh;

            padding-bottom: 0;

            padding-top: 80px;

        }



        .homeleft {

            align-items: center;

            display: flex

        }



        .slide-text h1 {

            line-height: 1.4em;

            font-size: 42px;

            color: rgba(255, 255, 255, 0.85);

        }



        .slide-text h4 {

            font-size: 18px;

            color: #fff8ca;

            font-weight: 400;

        }



        .headerTitle {

            color: #fff8ca;

            display: inline-block;

            padding-right: 15px

        }



        .profile-msg {

            position: absolute;

            top: 41%;

            left: -25px;

        }



        .section-title {

            margin-bottom: 50px;

        }



        .section-title h2 {

            color: #ffa500

        }



        .about-box {

            padding-bottom: 50px;

            border-bottom: 1px solid #dddddd

        }



        .about-border {

            border-right: 1px solid #dddddd;

            text-align: center

        }



        .chat-slide {

            padding-top: 58px;

        }



        .theme-bg {

            background: linear-gradient(to right, #ffd472 0%, #ffa500 100%);

        }



        .timeline h4 {

            color: #ffffff !important;

            font-size: 20px

        }



        .timeline-right h4 {

            color: #ffffff !important;

            font-size: 20px

        }



        .timeline p {

            margin-bottom: 55px;

        }



        .timeline-right p {

            margin-bottom: 55px;

        }



        .download-bg {

            background: linear-gradient(to right, #ffd472 0%, #ffa500 100%);

            padding: 40px 0;

        }



        .download-text h3 {

            margin-top: 0;

            color: #ffffff;

            font-weight: 500;

            font-size: 22px;

            margin-bottom: 0;

        }



        .download-img ul li {

            margin-right: 7px;

            display: inline-block;

            margin-top: 0px;

        }



        li {

            list-style: none

        }



        .downloadapp {

            display: flex;

            align-items: center

        }



        .auth-form {

            padding-right: 150px;

        }



        .btn-theme {

            background: linear-gradient(to right, #ffd472 0%, #ffa500 100%);

            color: #ffffff !important;

            font-size: 14px;

            border-radius: 5px;

            padding: 10px 30px;

            font-weight: 600;

            text-transform: capitalize;

            display: inline-block;

            border: 0;

            letter-spacing: 1px

        }



        .contact-text h3 {

            line-height: 28px;

            font-size: 24px;

            font-weight: 700;

            margin-top: 20px;

            color: #586082;

        }



        .timeline:before {

            background: orange;

            background-size: cover;

            border: 3px solid hsla(0, 0%, 100%, .9);

            border-radius: 50%;

            content: "";

            float: right;

            height: 12px;

            padding: 0;

            position: relative;

            right: -21px;

            top: 15px;

            width: 12px;



        }



        .timeline-right:before {

            background: orange;

            background-size: cover;

            border: 3px solid hsla(0, 0%, 100%, .9);

            border-radius: 50%;

            content: "";

            float: left;

            height: 12px;

            padding: 0;

            position: relative;

            top: 8px;

            width: 12px;

            left: -10px;

        }



        .future-timeline:after {

            background-color: hsla(0, 0%, 100%, .3);

            background-size: cover;

            border-radius: 12px;

            content: "";

            height: 100%;

            position: absolute;

            right: 0;

            top: 0;

            width: 1px;

        }



        .future-timeline-right:after {

            background-color: hsla(0, 0%, 100%, .3);

            background-size: cover;

            border-radius: 12px;

            content: "";

            height: 100%;

            left: 0;

            position: absolute;

            top: 0;

            width: 1px;

        }



        .future-timeline {

            text-align: right

        }



        .future-box {

            padding: 60px 0;

        }



        .screenshots .col-sm-12 {

            flex: 0 0 90%;

            max-width: 90%;

            margin-left: 5%;

        }



        .swiper-slide img {

            display: block;

            width: 105px;

            height: 105px;

            border-radius: 50%;

            object-fit: cover

        }



        .swiper-pagination {

            pointer-events: all !important;

        }



        /* .swiper-slide {

            width:auto!important

        } */

        .swiper-slide h6 {

            padding-bottom: 40px;

            text-align: justify;

            line-height: 23px;

            font-size: 16px;

            color: #586082;

            letter-spacing: 0.05rem;

        }



        .swiper-slide {

            text-align: left

        }



        .mobile-slid {

            text-align: right

        }



        .slid-btn {

            margin-top: 70px;

        }



        .social-footer ul li {

            display: inline-flex;

            height: 35px;

            width: 35px;

            background: #f0b020;

            border-radius: 50%;

            align-items: center;

            justify-content: center;

            margin-left: 10px;

            transform: scale(1);

            transition: all .3s ease;

        }



        .float-right {

            float: right;

        }



        .navbar-collapse {

            margin-top: 10px;

        }



        @keyframes ripple1 {

            0% {

                transform: scale(5.5);

                opacity: 0.3;

            }



            100% {

                transform: scale(8.5);

                opacity: 0.0;

            }

        }



        @-webkit-keyframes ripple1 {

            0% {

                -ms-transform: scale(5.5);

                /* IE 9 */

                -webkit-transform: scale(5.5);

                /* Safari */

                transform: scale(5.5);

                opacity: 0.3;

            }



            100% {

                -ms-transform: scale(8.5);

                /* IE 9 */

                -webkit-transform: scale(8.5);

                /* Safari */

                transform: scale(8.5);

                opacity: 0.0;

            }

        }



        @keyframes ripple2 {

            0% {

                -ms-transform: scale(3.5);

                /* IE 9 */

                -webkit-transform: scale(3.5);

                /* Safari */

                transform: scale(3.5);

            }



            100% {

                -ms-transform: scale(5.5);

                /* IE 9 */

                -webkit-transform: scale(5.5);

                /* Safari */

                transform: scale(5.5);

            }

        }



        @-webkit-keyframes ripple2 {

            0% {

                -ms-transform: scale(3.5);

                /* IE 9 */

                -webkit-transform: scale(3.5);

                /* Safari */

                transform: scale(3.5);

            }



            100% {

                -ms-transform: scale(5.5);

                /* IE 9 */

                -webkit-transform: scale(5.5);

                /* Safari */

                transform: scale(5.5);

            }

        }



        @keyframes ripple3 {

            0% {

                -ms-transform: scale(1.5);

                /* IE 9 */

                -webkit-transform: scale(1.5);

                /* Safari */

                transform: scale(1.5);

            }



            100% {

                -ms-transform: scale(3.5);

                /* IE 9 */

                -webkit-transform: scale(3.5);

                /* Safari */

                transform: scale(3.5);

            }

        }



        @-webkit-keyframes ripple3 {

            0% {

                -ms-transform: scale(1.5);

                /* IE 9 */

                -webkit-transform: scale(1.5);

                /* Safari */

                transform: scale(1.5);

            }



            100% {

                -ms-transform: scale(3.5);

                /* IE 9 */

                -webkit-transform: scale(3.5);

                /* Safari */

                transform: scale(3.5);

            }

        }



        .animation-circle i {

            position: absolute;

            height: 100px;

            width: 100px;

            background: linear-gradient(to right, #F0DF20 0%, #f0b020 100%);

            border-radius: 100%;

            opacity: 0.5;

            transform: scale(1.3);

            animation: ripple1 3s linear infinite;

            z-index: 3

        }



        .animation-circle i:nth-child(2) {

            animation: ripple2 3s linear infinite;

        }



        .animation-circle i:nth-child(3) {

            animation: ripple3 3s linear infinite;

        }



        .nav-link:hover {

            color: #fff8ca

        }



        a:hover {

            color: unset;

        }



        .contact-box li {

            padding-left: 70px;

            position: relative;

        }



        .contact-box {

            position: relative;

        }



        .contact-circle {

            width: 50px;

            height: 50px;

            border-radius: 50%;

            border: 2px solid #ffa500;

            background: transparent;

            font-size: 20px;

            color: #ffa500;

            position: absolute;

            left: 0;

            text-align: center;

            line-height: 2.1;

            /* top: 4px; */

        }



        .darkHeader {

            padding-bottom: 0;

            padding-top: 0;

            background: linear-gradient(to right, rgb(247, 214, 138) 0%, rgb(255, 165, 0) 100%);

            box-shadow: 1px 1px 35px 0 rgba(51, 51, 51, .4);

            transition: all .3s ease;



        }



        .fixed-top {

            z-index: 4

        }



        .navbar-toggler {

            float: right;

            margin-top: 12px;

            color: #fff;

        }



        .lefthomeimg {

            height: 630px;

            border-radius: 50px;

            border: 15px solid #383838;

        }



        .feature {

            z-index: 4;

            position: relative

        }



        .rightcontent {

            padding-left: 0px;

        }



        @media screen and (max-width:991px) {

            .navbar-collapse {

                float: inherit !important;

            }



            .lefthomeimg {

                height: 460px;

            }



            .download-img {

                margin-top: 20px;

            }



            .future-mobile {

                display: none

            }



            .timeline-right:before {

                display: none;

            }



            .timeline:before {

                right: -14px;

                top: 10px;



            }



            .animation-circle i {

                bottom: 0px;

            }



            #about {

                z-index: 3;

                position: relative;

                background: #fff;

            }



            .screenshotimg img {

                height: 400px;

            }



            .downloadapp {

                display: initial;

            }



            .download {

                text-align: center

            }

        }



        @media screen and (max-width:767px) {



            .mobile-slid {

                text-align: center

            }



            .mobile-slid img {

                margin-top: 20px;

                height: 400px;

            }



            .slid-btn {

                text-align: center;

                margin-top: 30px;

            }



            .slid-btn img {

                height: 50px;

            }



            .home {

                height: 100vh;

            }



            .profileimg {

                text-align: center

            }



            .contact-box {

                display: initial !important;

            }



            .contacttitle {

                text-align: left !important;

                margin-bottom: 0px !important;

            }

        }



        @media(max-width: 576px) {

            .profileright {

                text-align: center

            }



            .lefthomeimg {

                height: 430px;

            }



            .home {

                height: 80vh;

            }



            .animation-circle {

                display: none

            }



            .mobile-slid {

                text-align: center

            }



            .mobile-slid img {

                margin-top: 20px;

                height: 400px;

            }



            .slid-btn {

                text-align: center;

                margin-top: 30px

            }



            .slid-btn img {

                height: 50px

            }



            .slide-text h1 {

                font-size: 30px

            }



            .future-timeline {

                text-align: left

            }



            .future-timeline-right {

                text-align: left

            }



            .timeline:before {

                float: left;

                padding: 4px;

                top: 7px;

                right: 7px;

            }



            .future-box {

                padding: 0px;

            }



            .timeline p {

                margin-bottom: 10px;

            }



            .timeline-right p {

                margin-bottom: 10px;

            }



            .rightcontent {

                padding-left: 2rem

            }

            .timeline-right:before {

                display: block;

                padding: 4px;

                top: 7px;

                right: 7px;

            }



        }

    </style> --}}

    @stack('css_or_js')

    @vite('resources/css/app.css')
</head>
<body class="login">

    @yield('content')

    <script src="{{ asset('build/assets/jquery.min.js') }}"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    @stack('script')
</body>
</html>
