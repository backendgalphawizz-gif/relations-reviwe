<!DOCTYPE html>
<html lang="en">
<head>
    @php
        $logo = DB::table('systemflag')->where('name', 'AdminLogo')->select('value')->first();

        $appName = DB::table('systemflag')->where('name', 'AppName')->select('value')->first();
    @endphp


    <title>{{ $appName->value }}</title>

    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="//cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="//unpkg.com/swiper/swiper-bundle.min.css" />

    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <link as="image" fetchpriority="high" href="{{ $logo->value }}" rel="preload shortcut icon">

    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" defer>

    <style>

        a.dropdown-item.hover\:bg-white\/5 {
            color: white;
        }

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

    </style>

    @stack('css_or_js')

    @vite('resources/css/app.css')

    {{-- After theme CSS so active page number stays visible --}}
    <style>
        /* Hide scrollbars (advisor panel) — scrolling still works */
        html,
        body,
        .content,
        .side-nav,
        .scrollable {
            scrollbar-width: none !important;
            -ms-overflow-style: none !important;
        }

        html::-webkit-scrollbar,
        body::-webkit-scrollbar,
        .content::-webkit-scrollbar,
        .side-nav::-webkit-scrollbar,
        .scrollable::-webkit-scrollbar {
            width: 0 !important;
            height: 0 !important;
            display: none !important;
        }

        .simplebar-track {
            display: none !important;
        }

        .advisor-pagination-list {
            display: flex !important;
            flex-wrap: wrap;
            align-items: center;
            gap: 6px;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .advisor-pagination-list .page-item {
            margin: 0;
        }

        .advisor-pagination-list .page-link,
        .advisor-pagination-list .page-item .page-link,
        .advisor-pagination-list .page-item span.page-link,
        .advisor-pagination-list .page-item a.page-link {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-width: 36px !important;
            width: auto !important;
            height: 36px !important;
            padding: 0 10px !important;
            margin: 0 !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 6px !important;
            background-color: #ffffff !important;
            background-image: none !important;
            color: #334155 !important;
            font-size: 14px !important;
            font-weight: 600 !important;
            line-height: 1 !important;
            text-decoration: none !important;
            box-shadow: none !important;
            box-sizing: border-box !important;
            opacity: 1 !important;
        }

        .advisor-pagination-list .page-item.active .page-link,
        .advisor-pagination-list .page-item.active span.page-link,
        .advisor-pagination-list .page-item.advisor-page-active .page-link,
        .advisor-pagination-list .page-item.advisor-page-active span.page-link {
            background-color: #1c3c6c !important;
            background-image: none !important;
            border-color: #1c3c6c !important;
            color: #ffffff !important;
            z-index: 2;
        }

        .advisor-pagination-list .page-item.disabled .page-link,
        .advisor-pagination-list .page-item.disabled span.page-link {
            background-color: #f8fafc !important;
            border-color: #e2e8f0 !important;
            color: #94a3b8 !important;
            opacity: 0.7 !important;
            pointer-events: none;
        }

        .advisor-pagination-list .page-item:not(.active):not(.disabled) .page-link:hover {
            background-color: #f1f5f9 !important;
            border-color: #cbd5e1 !important;
            color: #1c3c6c !important;
        }

        .advisor-pagination-list .page-item.active .page-link:hover {
            background-color: #1c3c6c !important;
            color: #ffffff !important;
        }

        .advisor-pagination .pagecount {
            font-size: 14px;
            color: #64748b;
        }

        tfoot .advisor-pagination {
            width: 100%;
        }
    </style>
</head>
<body class="pt-5 md:py-0">

    @include('../layout/components/advisor/mobile-menu')
    @include('../layout/components/advisor/top-bar')
    <div class="flex overflow-hidden">
        <nav class="side-nav">
            <ul>
                <li>
                    <a href="{{ route('advisor.dashboard') }}" class="side-menu {{ Request::is('advisor/dashboard') ? 'side-menu--active' : '' }}">
                        <div class="side-menu__icon"><i data-lucide="layout-dashboard"></i></div>
                        <div class="side-menu__title"> Dashboard</div>
                    </a>
                </li>
                <li>
                    <a href="{{ route('advisor.call-history') }}" class="side-menu {{ Request::is('advisor/call-history') ? 'side-menu--active' : '' }}">
                        <div class="side-menu__icon"><i data-lucide="phone-call"></i></div>
                        <div class="side-menu__title"> Call History</div>
                    </a>
                </li>
                {{-- <li>
                    <a href="{{ route('advisor.chat-history') }}" class="side-menu {{ Request::is('advisor/chat-history') ? 'side-menu--active' : '' }}">
                        <div class="side-menu__icon"><i data-lucide="message-circle"></i></div>
                        <div class="side-menu__title"> Chat History</div>
                    </a>
                </li> --}}
                <li>
                    <a href="{{ route('advisor.availability') }}" class="side-menu {{ Request::is('advisor/availability') ? 'side-menu--active' : '' }}">
                        <div class="side-menu__icon"><i data-lucide="clock-2"></i></div>
                        <div class="side-menu__title"> Availability</div>
                    </a>
                </li>
                <li>
                    <a href="{{ route('advisor.wait-time') }}" class="side-menu {{ Request::is('advisor/wait-time') ? 'side-menu--active' : '' }}">
                        <div class="side-menu__icon"><i data-lucide="clock-2"></i></div>
                        <div class="side-menu__title"> Wait Time</div>
                    </a>
                </li>
                <li>
                    <a href="{{ route('advisor.transactions') }}" class="side-menu {{ Request::is('advisor/transactions') ? 'side-menu--active' : '' }}">
                        <div class="side-menu__icon"><i data-lucide="clock-2"></i></div>
                        <div class="side-menu__title"> Transactions</div>
                    </a>
                </li>
                <li>
                    <a href="{{ route('advisor.notifications') }}" class="side-menu {{ Request::is('advisor/notifications') ? 'side-menu--active' : '' }}">
                        <div class="side-menu__icon"><i data-lucide="bell"></i></div>
                        <div class="side-menu__title"> Notifications</div>
                    </a>
                </li>
                <li>
                    <a href="{{ route('advisor.withdrawls') }}" class="side-menu {{ Request::is('advisor/withdrawls') ? 'side-menu--active' : '' }}">
                        <div class="side-menu__icon"><i data-lucide="clock-2"></i></div>
                        <div class="side-menu__title"> Withdrawls</div>
                    </a>
                </li>
                <li>
                    <a href="{{ route('advisor.privacy-policy') }}" class="side-menu {{ Request::is('advisor/privacy-policy') ? 'side-menu--active' : '' }}">
                        <div class="side-menu__icon"><i data-lucide="file"></i></div>
                        <div class="side-menu__title"> Privacy Policy</div>
                    </a>
                </li>
                <li>
                    <a href="{{ route('advisor.terms-condition') }}" class="side-menu {{ Request::is('advisor/terms-condition') ? 'side-menu--active' : '' }}">
                        <div class="side-menu__icon"><i data-lucide="file"></i></div>
                        <div class="side-menu__title"> Terms & Condition</div>
                    </a>
                </li>
                
            </ul>
        </nav>
        <div class="content">
            <div id="advisor-notify-banner" style="display:none;background:#fff7ed;border:1px solid #fdba74;color:#9a3412;"
                class="mb-3 p-3 rounded-md flex items-center justify-between gap-3">
                <div class="text-sm">
                    <strong>Call alerts:</strong>
                    <span id="advisor-notify-banner-text">Keep this tab open. New calls will pop up here.</span>
                </div>
                <button type="button" id="advisor-enable-notify-btn" class="btn btn-sm btn-primary"
                    style="background:#426f7f;border-color:#426f7f;white-space:nowrap;display:none;">
                    Enable Browser Push
                </button>
            </div>

            {{-- Incoming call modal is moved to document.body by JS so layout overflow cannot hide it --}}
            <div id="incoming-call-modal" class="advisor-incoming-call-modal" aria-hidden="true"
                style="display:none !important;position:fixed;inset:0;z-index:2147483000;background:rgba(15,23,42,.78);align-items:center;justify-content:center;">
                <div style="width:min(420px,92vw);background:#fff;border-radius:16px;padding:28px 24px;text-align:center;box-shadow:0 20px 50px rgba(0,0,0,.35);">
                    <div style="width:72px;height:72px;margin:0 auto 16px;border-radius:50%;background:#426f7f;display:flex;align-items:center;justify-content:center;color:#fff;font-size:32px;line-height:1;">
                        ☎
                    </div>
                    <h3 id="incoming-call-title" style="font-size:1.35rem;font-weight:700;color:#0f172a;margin:0 0 8px;">Incoming Call</h3>
                    <p id="incoming-call-meta" style="color:#64748b;margin:0 0 22px;">Connecting...</p>
                    <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
                        <a id="incoming-call-accept" href="#" class="btn btn-primary"
                            style="background:#16a34a;border-color:#16a34a;min-width:120px;color:#fff;">Accept</a>
                        <a id="incoming-call-reject" href="#" class="btn btn-danger"
                            style="min-width:120px;color:#fff;">Reject</a>
                    </div>
                </div>
            </div>

            @yield('content')
        </div>
    </div>
    @include('../layout/components/dark-mode-switcher')
    @include('../layout/components/main-color-switcher')

    <!-- BEGIN: JS Assets-->
    {{-- <script src="https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js"></script> --}}
    {{-- <script src="https://maps.googleapis.com/maps/api/js?key=[" your-google-map-api"]&libraries=places"></script> --}}
    @vite('resources/js/app.js')

    <script src="{{ asset('build/assets/jquery.min.js') }}"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    @stack('script')

    <script>
        (function() {
            const pendingUrl = @json(route('advisor.pending-calls'));
            const startCallBase = @json(url('/advisor/start-call'));
            const csrf = @json(csrf_token());
            const advisorUserId = @json(optional(auth('advisor')->user())->id);
            let knownCallIds = new Set();
            let primed = false;
            let audioCtx = null;
            let ringInterval = null;
            let activeCallId = null;
            let ringAdvanceTimer = null;

            window.__advisorIncoming = {
                show: showIncomingModal,
                hide: hideIncomingModal,
                advisorUserId: advisorUserId
            };

            function isSecureForNotify() {
                return window.isSecureContext === true;
            }

            function showBanner(message, showButton) {
                const banner = document.getElementById('advisor-notify-banner');
                const text = document.getElementById('advisor-notify-banner-text');
                const btn = document.getElementById('advisor-enable-notify-btn');
                if (!banner || !text) return;
                text.textContent = message;
                if (btn) btn.style.display = showButton ? '' : 'none';
                banner.style.display = 'flex';
            }

            function hideBanner() {
                const banner = document.getElementById('advisor-notify-banner');
                if (banner) banner.style.display = 'none';
            }

            function playBeep() {
                try {
                    audioCtx = audioCtx || new (window.AudioContext || window.webkitAudioContext)();
                    if (audioCtx.state === 'suspended') audioCtx.resume();
                    const osc = audioCtx.createOscillator();
                    const gain = audioCtx.createGain();
                    osc.connect(gain);
                    gain.connect(audioCtx.destination);
                    osc.frequency.value = 880;
                    gain.gain.value = 0.06;
                    osc.start();
                    setTimeout(() => { try { osc.stop(); } catch (e) {} }, 280);
                } catch (e) {}
            }

            function startRinging() {
                stopRinging();
                playBeep();
                ringInterval = setInterval(playBeep, 1400);
            }

            function stopRinging() {
                if (ringInterval) {
                    clearInterval(ringInterval);
                    ringInterval = null;
                }
            }

            function mountIncomingModal(modal) {
                if (!modal) return null;
                if (modal.parentElement !== document.body) {
                    document.body.appendChild(modal);
                }
                return modal;
            }

            function showIncomingModal(call) {
                try {
                    const callId = String(call?.callId || call?.id || '');
                    if (!callId || callId === 'undefined' || callId === 'null') {
                        console.warn('Incoming call missing id', call);
                        return;
                    }

                    const name = call.userName
                        || (call.user && call.user.name)
                        || 'Customer';
                    const type = call.type || 'audio';

                    const modal = mountIncomingModal(document.getElementById('incoming-call-modal'));
                    const title = document.getElementById('incoming-call-title');
                    const meta = document.getElementById('incoming-call-meta');
                    const accept = document.getElementById('incoming-call-accept');
                    const reject = document.getElementById('incoming-call-reject');

                    if (title) title.textContent = 'Incoming ' + String(type).toUpperCase() + ' Call';
                    if (meta) meta.textContent = name + ' is calling you';
                    if (accept) accept.href = startCallBase + '/' + callId + '?type=accept';
                    if (reject) reject.href = startCallBase + '/' + callId + '?type=reject';

                    // After ring timeout, force poll so server can advance to next (app) advisor
                    if (ringAdvanceTimer) {
                        clearTimeout(ringAdvanceTimer);
                        ringAdvanceTimer = null;
                    }
                    if (call.is_sequential) {
                        const timeoutSec = Number(call.ring_timeout_seconds || call.ringTimeoutSeconds || 30);
                        let waitMs = timeoutSec * 1000;
                        if (call.ringSecondsLeft != null && call.ringSecondsLeft !== '') {
                            waitMs = Math.max(500, Number(call.ringSecondsLeft) * 1000);
                        }
                        ringAdvanceTimer = setTimeout(function() {
                            pollPendingCalls();
                        }, waitMs + 300);
                    }

                    if (modal) {
                        modal.style.setProperty('display', 'flex', 'important');
                        modal.setAttribute('aria-hidden', 'false');
                    }

                    const isSame = activeCallId === callId;
                    activeCallId = callId;
                    knownCallIds.add(callId);

                    if (!isSame) {
                        startRinging();
                        if (typeof toastr !== 'undefined') {
                            toastr.clear();
                            toastr.warning(name + ' is calling (' + type + ')', 'Incoming Call', {
                                timeOut: 0,
                                extendedTimeOut: 0,
                                closeButton: true,
                                tapToDismiss: false
                            });
                        }
                        if ('Notification' in window && Notification.permission === 'granted') {
                            try {
                                const n = new Notification('Incoming Call', {
                                    body: name + ' is calling (' + type + ')',
                                    requireInteraction: true,
                                    tag: 'incoming-call-' + callId
                                });
                                n.onclick = function() {
                                    window.focus();
                                    n.close();
                                };
                            } catch (e) {}
                        }
                    }

                    console.log('Incoming call popup shown', callId, name);
                } catch (err) {
                    console.error('showIncomingModal failed', err);
                }
            }

            function hideIncomingModal(callId) {
                if (callId && activeCallId && String(callId) !== String(activeCallId)) return;
                activeCallId = null;
                stopRinging();
                if (ringAdvanceTimer) {
                    clearTimeout(ringAdvanceTimer);
                    ringAdvanceTimer = null;
                }
                const modal = document.getElementById('incoming-call-modal');
                if (modal) {
                    modal.style.setProperty('display', 'none', 'important');
                    modal.setAttribute('aria-hidden', 'true');
                }
                if (typeof toastr !== 'undefined') {
                    try { toastr.clear(); } catch (e) {}
                }
            }

            async function pollPendingCalls() {
                try {
                    const res = await fetch(pendingUrl, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        credentials: 'same-origin'
                    });
                    if (!res.ok) return;
                    const data = await res.json();
                    if (!data.status) return;

                    if (data.advisorOnline === false) {
                        showBanner('You are Offline. Use the Status dropdown in the top bar (or open Wait Time) and set Online to receive calls.', false);
                    }

                    const calls = (data.calls || []).filter(function(c) {
                        return String(c.callStatus || '').toLowerCase() === 'pending';
                    });

                    primed = true;

                    if (calls.length) {
                        // Always keep popup visible for current pending call
                        showIncomingModal(calls[0]);
                        // If ring already timed out server-side, next poll advances to app advisor
                        if (calls[0].ringSecondsLeft === 0 && calls[0].is_sequential) {
                            // Force another poll soon so advance propagates to app
                            setTimeout(pollPendingCalls, 500);
                        }
                    } else if (activeCallId) {
                        hideIncomingModal(activeCallId);
                    }

                    knownCallIds = new Set(calls.map(function(c) { return String(c.id); }));
                    if (activeCallId) knownCallIds.add(String(activeCallId));
                } catch (e) {
                    console.warn('pending call poll failed', e);
                }
            }

            function updateNotifyUi() {
                if (!('Notification' in window)) {
                    showBanner('Keep this advisor tab open. Incoming calls will pop up here (browser push not supported).', false);
                    return;
                }
                if (!isSecureForNotify()) {
                    showBanner('You are on HTTP — browser push cannot prompt. Keep this tab open: calls still show a full-screen Accept/Reject popup.', false);
                    return;
                }
                if (Notification.permission === 'granted') {
                    hideBanner();
                    return;
                }
                if (Notification.permission === 'denied') {
                    showBanner('Browser notifications blocked. Keep this tab open for live call popups.', false);
                    return;
                }
                showBanner('Optional: enable browser push. Call popups also work while this tab stays open.', true);
            }

            document.getElementById('advisor-enable-notify-btn')?.addEventListener('click', async function() {
                if (!('Notification' in window)) return;
                const result = await Notification.requestPermission();
                updateNotifyUi();
                if (result === 'granted') {
                    window.dispatchEvent(new Event('advisor-notify-enabled'));
                    if (typeof toastr !== 'undefined') toastr.success('Notifications enabled');
                }
            });

            // Unlock audio on first user interaction (browser autoplay policy)
            ['click', 'keydown', 'touchstart'].forEach(function(evt) {
                document.addEventListener(evt, function once() {
                    try {
                        audioCtx = audioCtx || new (window.AudioContext || window.webkitAudioContext)();
                        audioCtx.resume();
                    } catch (e) {}
                }, { once: true });
            });

            updateNotifyUi();
            mountIncomingModal(document.getElementById('incoming-call-modal'));
            pollPendingCalls();
            setInterval(pollPendingCalls, 2000);
        })();
    </script>

    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/12.4.0/firebase-app.js";
        import { getMessaging, getToken, onMessage } from "https://www.gstatic.com/firebasejs/12.4.0/firebase-messaging.js";
        import { getDatabase, ref, onChildAdded, onChildRemoved, onChildChanged } from "https://www.gstatic.com/firebasejs/12.4.0/firebase-database.js";

        const firebaseConfig = {
            apiKey: "AIzaSyAbVv-H2kbOH1REQ2ggNc7xxg0Bh9LfT28",
            authDomain: "realtionship-849b1.firebaseapp.com",
            databaseURL: "https://realtionship-849b1-default-rtdb.firebaseio.com",
            projectId: "realtionship-849b1",
            storageBucket: "realtionship-849b1.firebasestorage.app",
            messagingSenderId: "884911350693",
            appId: "1:884911350693:web:86f89b3a009d9fe8823e4c",
            measurementId: "G-2QD0G6R41N"
        };

        const advisorUserId = window.__advisorIncoming?.advisorUserId;

        let firebaseApp = null;
        function getApp() {
            if (!firebaseApp) firebaseApp = initializeApp(firebaseConfig);
            return firebaseApp;
        }

        // Realtime incoming-call path (works on HTTP LAN; does not need browser Notification permission)
        if (advisorUserId) {
            try {
                const db = getDatabase(getApp());
                const incomingRef = ref(db, 'webAdvisorIncoming/' + advisorUserId);

                onChildAdded(incomingRef, (snapshot) => {
                    const data = snapshot.val();
                    if (!data) return;
                    if (String(data.callStatus || 'Pending') !== 'Pending') return;
                    window.__advisorIncoming?.show(data);
                });

                onChildChanged(incomingRef, (snapshot) => {
                    const data = snapshot.val();
                    if (!data) return;
                    if (String(data.callStatus || '') !== 'Pending') {
                        window.__advisorIncoming?.hide(data.callId || snapshot.key);
                    }
                });

                onChildRemoved(incomingRef, (snapshot) => {
                    const data = snapshot.val() || {};
                    window.__advisorIncoming?.hide(data.callId || snapshot.key);
                });

                console.log('Advisor RTDB incoming listener ready for user', advisorUserId);
            } catch (e) {
                console.warn('RTDB incoming listener failed', e);
            }
        }

        function canUseWebPush() {
            return window.isSecureContext === true
                && 'Notification' in window
                && 'serviceWorker' in navigator;
        }

        async function registerAdvisorFcmToken(requestPermissionIfNeeded = false) {
            try {
                if (!canUseWebPush()) {
                    console.warn('Web push unavailable on this origin (need HTTPS/localhost)');
                    return false;
                }

                if (Notification.permission === 'denied') {
                    return false;
                }

                if (Notification.permission !== 'granted') {
                    if (!requestPermissionIfNeeded) {
                        return false;
                    }
                    const result = await Notification.requestPermission();
                    if (result !== 'granted') {
                        return false;
                    }
                }

                const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js');
                await navigator.serviceWorker.ready;
                const messaging = getMessaging(getApp());

                const token = await getToken(messaging, {
                    vapidKey: "BCzv6CBSKQZ7v9YjUuzJj_brefX2mmEB1g_ZAZ9Z4urRJ5SB2Kjj6Ah05SeWg-vZEZnaAe-LfuaaMmNz87iYGFY",
                    serviceWorkerRegistration: registration
                });

                if (!token) return false;

                if ($('[name=fcm_token]').length) {
                    $('[name=fcm_token]').val(token);
                }

                const res = await fetch("{{ route('advisor.update-fcm-token') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        fcm_token: token,
                        appId: 3,
                        userAgent: navigator.userAgent,
                        osName: navigator.platform || 'web',
                        appVersion: navigator.appVersion || 'browser'
                    })
                });

                if (!res.ok) {
                    console.warn('Failed to save advisor FCM token', await res.text());
                    return false;
                }

                console.log('Advisor web FCM token saved');
                return true;
            } catch (err) {
                console.error('Advisor FCM register failed', err);
                return false;
            }
        }

        // Auto-enable push after login when secure context allows it
        if (canUseWebPush()) {
            if (Notification.permission === 'granted') {
                registerAdvisorFcmToken(false);
            } else if (Notification.permission === 'default') {
                // Ask once after panel loads so web push works without hunting for the button
                setTimeout(function() {
                    registerAdvisorFcmToken(true).then(function(ok) {
                        if (ok && typeof toastr !== 'undefined') {
                            toastr.success('Web push notifications enabled');
                        }
                        window.dispatchEvent(new Event('advisor-notify-ui-refresh'));
                    });
                }, 800);
            }
        }

        window.addEventListener('advisor-notify-enabled', function() {
            registerAdvisorFcmToken(true);
        });

        if (canUseWebPush()) {
            try {
                const messaging = getMessaging(getApp());

                function parseFcmBody(payload) {
                    let body = payload?.data?.body ?? payload?.data ?? {};
                    if (typeof body === 'string') {
                        try { body = JSON.parse(body); } catch (e) { body = {}; }
                    }
                    return body || {};
                }

                onMessage(messaging, (payload) => {
                    const body = parseFcmBody(payload);
                    if (Number(body?.notificationType) === 2 || body?.type === 'call_request') {
                        // Modal + one browser notification come from showIncomingModal
                        window.__advisorIncoming?.show({
                            callId: body.callId,
                            type: body.call_type || 'audio',
                            userName: body.userName || 'Customer',
                            callStatus: 'Pending'
                        });
                        return;
                    }

                    const title = payload?.notification?.title || 'Notification';
                    const description = payload?.notification?.body
                        || body?.description
                        || 'You have a new notification';
                    try { new Notification(title, { body: description }); } catch (e) {}
                    if (typeof toastr !== 'undefined') toastr.info(description, title);
                });
            } catch (e) {
                console.warn('FCM foreground listener skipped', e);
            }
        }
    </script>

</body>
</html>
