<!DOCTYPE html>

<!--

Template Name: Enigma - HTML Admin Dashboard Template

Author: Left4code

Website: http://www.left4code.com/

Contact: muhammadrizki@left4code.com

Purchase: https://themeforest.net/user/left4code/portfolio

Renew Support: https://themeforest.net/user/left4code/portfolio

License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.

-->

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="{{ 'default' != 'default' ? ' ' . 'default' : '' }}">

<!-- BEGIN: Head -->

@php

    $logo = DB::table('systemflag')

        ->where('name', 'AdminLogo')

        ->select('value')

        ->first();

    $appName = DB::table('systemflag')

        ->where('name', 'AppName')

        ->select('value')

        ->first();

@endphp



<head>

    <title>{{$appName->value}}</title>

    <meta charset="utf-8">

    <link as="image" fetchpriority="high" href="/{{ $logo->value }}" rel="preload shortcut icon">

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" defer/>

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" defer>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" defer>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="Enigma admin is super flexible, powerful, clean & modern responsive tailwind admin template with unlimited possibilities.">

    <meta name="keywords"

        content="admin template, Enigma Admin Template, dashboard template, flat admin template, responsive admin template, web app">

    <meta name="author" content="LEFT4CODE">

    <script src="https://www.gstatic.com/firebasejs/7.9.1/firebase-app.js"></script>

    <script src="https://www.gstatic.com/firebasejs/7.9.1/firebase-messaging.js"></script>

    <script src="https://cdn.ckeditor.com/4.10.1/standard/ckeditor.js"></script>

    <script src="{{ asset('build/assets/jquery.min.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

    <style>

        .disabled {

            pointer-events: none

        }

        /* Hide scrollbars (admin panel) — scrolling still works */
        html,
        body,
        .content,
        .side-nav,
        .scrollable,
        .list-table,
        .setting-page,
        .grid-table,
        .grid-table-without-search,
        .daily,
        .withoutsearch {
            scrollbar-width: none !important;
            -ms-overflow-style: none !important;
        }

        html::-webkit-scrollbar,
        body::-webkit-scrollbar,
        .content::-webkit-scrollbar,
        .side-nav::-webkit-scrollbar,
        .scrollable::-webkit-scrollbar,
        .list-table::-webkit-scrollbar,
        .setting-page::-webkit-scrollbar,
        .grid-table::-webkit-scrollbar,
        .grid-table-without-search::-webkit-scrollbar,
        .daily::-webkit-scrollbar,
        .withoutsearch::-webkit-scrollbar {
            width: 0 !important;
            height: 0 !important;
            display: none !important;
        }

        .simplebar-track {
            display: none !important;
        }



        .edit-modal {

            z-index: 1000 !important;

        }



        p.leading-5 {

            display: none !important;

        }



        .pagination li a:active {

            background-color: red

        }



        .sticky-top {

            position: sticky;

            top: 0;

            z-index: 1020;

            background: #fff

        }



        .list-table {

            height: calc(100vh - 282px);

            overflow-y: auto !important;

            margin-top: 10px;

            margin-bottom: 10px

        }



        .setting-page {

            height: calc(100vh - 251px);

            overflow-y: auto !important;

            margin-top: 10px;

            margin-bottom: 10px

        }



        .grid-table {

            height: calc(100vh - 292px);

            overflow-y: auto !important;

            margin-top: 10px;

            margin-bottom: 10px

        }



        .grid-table-without-search {

            height: calc(100vh - 238px);

            overflow-y: auto !important;

            margin-top: 10px;

            margin-bottom: 10px

        }



        .daily {

            height: calc(100vh - 340px);

            overflow-y: auto !important;

            margin-top: 10px;

            margin-bottom: 10px

        }



        .withoutsearch {

            height: calc(100vh - 227px);

            overflow-y: auto !important;

            margin-top: 10px;

            margin-bottom: 10px

        }



        .d-inline {

            display: inline-block

        }



        .addbtn {

            float: right

        }



        .horobtn:after {

            margin-right: 5px;

            content: "";

            background-color: #000;

            position: absolute;

            width: 2px;

            height: 45px;

            display: revert;

            left: 160px;

            margin-left: 5px;

        }



        .horo-insight:after {

            left: 207px

        }



        .horo:after {

            left: 127px

        }



        .astrologer-tab-content {

            height: calc(100vh - 440px);

            overflow-y: auto;

            overflow-x: hidden

        }



        .pagecount {

            margin-top: 10px

        }



        .text-red {

            color: red

        }



        .text-green {

            color: #78b144

        }



        .category {

            font-size: 20px;

        }



        @keyframes animName {

            0% {

                transform: rotate(0deg);

            }



            100% {

                transform: rotate(360deg);

            }

        }



        .loader {



            position: fixed;

            top: 0;

            left: 0;

            right: 0;

            bottom: 0;

            width: 100%;

            background: rgb(255, 255, 255) url(/{{ $logo->value }}) no-repeat center center;

            z-index: 10000;

            background-size: 100px;

        }



        .changeorder {

            border: 1px solid #ddd;

            text-align: center;

            padding: 5px;

            border-radius: 5px;



        }



        input[type='file'] {

            color: rgba(0, 0, 0, 0)

        }



        .mastertab {

            height: calc(100vh - 420px);

            overflow: auto;

            overflow-x: hidden

        }



        .fitbox {

            height: fit-content

        }



        .setting {

            height: calc(100vh - 260px);

            overflow: auto;

            overflow-x: hidden

        }



        .mail {

            margin: auto;

            width: 600px;

        }



        @media(max-width: 695px) {



            .dailyaddbtn {

                float: left;

            }



            .dailytitle {

                margin-top: 0px !important;

            }

        }



        @media(max-width: 920px) {

            .horobtn:after {

                display: none

            }



            .dailytitle {

                margin-top: 10px !important;

            }



            .dailyhorobtn {

                float: left;

            }

        }



        @media(max-width: 830px) {

            .horedit {

                margin-top: 20px

            }

        }



        @media(max-width: 640px) {

            .horosign {

                margin-top: 0px !important

            }

        }



        .nav-link-tabs {

            overflow-x: auto

        }



        .settingimg {

            height: 150px;

            text-align: -webkit-center;

            width: 100%;

        }



        .settingimg img {

            height: 100%;

            /* width: 100%; */

            object-fit: cover

        }



        th,

        td {

            font-size: .875rem

        }



        .select2-container {

            width: 100% !important

        }



        .editastrologertab {

            height: calc(100vh - 280px);

            overflow: auto;

            overflow-x: hidden

        }



        .systooltip {

            position: relative;

            display: inline-block;

        }



        .systooltip .tooltiptext {

            visibility: hidden;

            /* width: 120px; */

            background-color: black;

            color: #fff;

            /* text-align: center; */

            border-radius: 6px;

            padding: 5px 5px;

            font-size: 12px;

            /* Position the tooltip */

            position: initial;

            z-index: 1;

            top: -5px;

            left: 105%;

        }





        .systooltip:hover .tooltiptext {

            visibility: visible;

        }

    </style>

    <script></script>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    @yield('head')



    <!-- BEGIN: CSS Assets-->

    @vite('resources/css/app.css')

    <!-- END: CSS Assets-->

</head>

@yield('body')

<script>

    $(window).on('load', function() {

        $('#loading').hide();

    })

</script>

@auth
<script type="module">
    import { initializeApp } from "https://www.gstatic.com/firebasejs/12.4.0/firebase-app.js";
    import { getMessaging, getToken, onMessage } from "https://www.gstatic.com/firebasejs/12.4.0/firebase-messaging.js";

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

    const canUseWebPush = () =>
        window.isSecureContext &&
        'Notification' in window &&
        'serviceWorker' in navigator &&
        'PushManager' in window;

    async function registerAdminFcmToken() {
        try {
            if (!canUseWebPush()) return;

            const permission = Notification.permission === 'granted'
                ? 'granted'
                : await Notification.requestPermission();
            if (permission !== 'granted') return;

            const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js');
            await navigator.serviceWorker.ready;

            const app = initializeApp(firebaseConfig);
            const messaging = getMessaging(app);
            const token = await getToken(messaging, {
                vapidKey: "BCzv6CBSKQZ7v9YjUuzJj_brefX2mmEB1g_ZAZ9Z4urRJ5SB2Kjj6Ah05SeWg-vZEZnaAe-LfuaaMmNz87iYGFY",
                serviceWorkerRegistration: registration
            });
            if (!token) return;

            const res = await fetch("{{ route('save-token') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'Accept': 'application/json'
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    fcm_token: token,
                    user_id: {{ auth()->id() ?? 'null' }},
                    appId: 4,
                    userAgent: navigator.userAgent,
                    osName: navigator.platform || 'web',
                    appVersion: navigator.appVersion || 'admin-web'
                })
            });

            if (!res.ok) {
                console.warn('Failed to save admin FCM token', await res.text());
                return;
            }

            onMessage(messaging, (payload) => {
                const title = payload?.notification?.title || 'Notification';
                const body = payload?.notification?.body || '';
                if (window.toastr) {
                    toastr.info(body, title);
                }
            });
        } catch (err) {
            console.error('Admin FCM register failed', err);
        }
    }

    if (canUseWebPush()) {
        registerAdminFcmToken();
    }
</script>
@endauth

</html>

