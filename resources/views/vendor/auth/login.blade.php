@extends('vendor.auth.app')

@push('css_or_js')
<title>Advisor Login</title>
@endpush

@section('content')
<div class="loader"></div>
<div class="customContainer">
    <div
        class="loginCard" style="padding-bottom: 0;">
        <img src="{{ asset('/assets/img/mainLogo.png') }}" alt="" class="loginLogo">
        <br>
        <h2 class="intro-x font-bold text-2xl xl:text-3xl">Sign In</h2>
        <br>
        <div class="intro-x mt-2 xl:hidden text-center"></div>

        <div class="intro-x">
            <form action="{{ route('advisor.login-auth') }}" method="post" id="advisor-login">
                {{ csrf_field() }}
                <div class="row">
                    <div class="col-lg-12">
                        <input type="hidden" name="fcm_token">
                        <div class="form-group">
                            <label for="mobile">Mobile</label>
                            <input id="mobile" type="text" minlength="10" maxlength="10" onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))" class="intro-x login__input form-control py-3 px-4 block" placeholder="Enter Mobile" name="mobile">
                           <div style="text-align: center;"> <button type="button" class="send-otp mt-3 btn btn-primary btn-submit py-2 px-4 w-full xl:w-32 xl:mr-3 align-top">Send OTP</button></div>
                        </div>
                    </div>
                    <div class="col-lg-12 otp-field mt-3 d-none">
                        <div class="form-group">
                            <label for="otp">OTP</label>
                            <input id="otp" type="otp" name="otp" class="intro-x login__input form-control py-3 px-4 block" placeholder="Enter OTP" minlength="4" maxlength="4" onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))">
                        </div>
                    </div>
                    <div class="col-lg-12 otp-field d-none" style="display: center;">
                        <button type="submit" class="pull-right mt-2 btn-sm btn btn-primary btn-submit py-2 px4 w-full xl:w-32 align-top">Verify OTP</button>
                    </div>
                    <div class="col-lg-12 mt-2">
                        <hr>
                        <div class="accountText">Don't have an account? <a href="{{ route('advisor.signup') }}" class="">Sign Up</a></div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="superlarge-modal-size-preview" class="modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="col-span-12 lg:col-span-8 2xl:col-span-9">
                <div class="intro-y box lg:mt-5">
                    <div class="flex items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                        <h2 class="font-medium text-base mr-auto">Forgot Password</h2>
                    </div>
                    <div class="p-5">
                        <form method="POST" enctype="multipart/form-data" id="sendmailform">
                            @csrf
                            <div>
                                <label for="change-password-form-1" class="form-label">Email</label>
                                <input class="form-control" id="resendemail" name="resendemail" type="email"
                                    class="form-control" placeholder="Input text">
                                <div class="text-danger print-resendmail-error-msg mb-2" style="display:none">
                                    <ul></ul>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary mt-4">Send Mail</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    setTimeout(() => {
        $('.loader').hide()
    }, 2000);

    $(document).on('click', '.send-otp', function() {
        let mobile = $('[name=mobile]').val()
        $('[name=mobile]').attr('readonly')
        $.ajax({
            type: "POST",
            url: "{{ route('advisor.send-otp') }}",
            data: {
                "_token": $('input[name=_token]').val(),
                mobile: mobile
            },
            dataType: "json",
            success: function(response) {
                if (response.status) {
                    $('.send-otp').text('Resend OTP')
                    $('.otp-field').removeClass('d-none')
                    toastr.success(response.message)
                } else {
                    toastr.error(response.message)
                }
            },
            error: function(jqhr) {
                toastr.error(jqhr.responseJSON?.message || 'Failed to send OTP')
            }
        })
    })
</script>

<script type="module">
    import { initializeApp } from "https://www.gstatic.com/firebasejs/12.4.0/firebase-app.js";
    import { getMessaging, getToken } from "https://www.gstatic.com/firebasejs/12.4.0/firebase-messaging.js";

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

    const VAPID_KEY = "BCzv6CBSKQZ7v9YjUuzJj_brefX2mmEB1g_ZAZ9Z4urRJ5SB2Kjj6Ah05SeWg-vZEZnaAe-LfuaaMmNz87iYGFY";

    function canUseWebPush() {
        return window.isSecureContext === true
            && 'Notification' in window
            && 'serviceWorker' in navigator;
    }

    async function obtainFcmToken() {
        if (!canUseWebPush()) {
            console.warn('Web push needs HTTPS or localhost. Current origin cannot register FCM.');
            return '';
        }

        let permission = Notification.permission;
        if (permission === 'default') {
            permission = await Notification.requestPermission();
        }
        if (permission !== 'granted') {
            console.warn('Notification permission not granted');
            return '';
        }

        const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js');
        await navigator.serviceWorker.ready;

        const app = initializeApp(firebaseConfig);
        const messaging = getMessaging(app);
        const token = await getToken(messaging, {
            vapidKey: VAPID_KEY,
            serviceWorkerRegistration: registration
        });

        if (token) {
            $('[name=fcm_token]').val(token);
            console.log('Advisor web FCM token ready');
        }
        return token || '';
    }

    if (canUseWebPush() && Notification.permission === 'granted') {
        obtainFcmToken().catch((e) => console.warn('FCM prefetch failed', e));
    }

    $('#advisor-login').on('submit', async function(e) {
        e.preventDefault();

        try {
            await obtainFcmToken();
        } catch (err) {
            console.warn('FCM before login failed', err);
        }

        const formData = new FormData(this);
        const appVersion = navigator.appVersion;
        let osName = 'Unknown OS';
        if (appVersion.includes('Win')) osName = 'Windows';
        else if (appVersion.includes('Mac')) osName = 'macOS';
        else if (appVersion.includes('X11') || appVersion.includes('Linux')) osName = 'UNIX/Linux';

        formData.append('appVersion', appVersion);
        formData.append('osName', osName);
        formData.append('userAgent', navigator.userAgent);
        formData.append('appId', 3);

        $.ajax({
            type: $(this).attr('method'),
            url: $(this).attr('action'),
            data: formData,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status) {
                    toastr.success(response.message);
                    setTimeout(() => {
                        window.location.replace("{{ route('advisor.dashboard') }}");
                    }, 1000);
                } else if (response.error && response.error.length > 0) {
                    $.each(response.error, function(ind, elm) {
                        toastr.error(elm[0]);
                    });
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                toastr.error('Login failed. Please try again.');
            }
        });
    });
</script>
@endpush
