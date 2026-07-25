@extends('vendor.auth.app')

@push('css_or_js')
<title>Advisor Login</title>
<style>
    .select2-container {
        display: block !important;
    }
</style>
@endpush

@section('content')
    <div class="loader"></div>
    <div class="customContainer">
    <div
                class="loginCard" style="padding-bottom: 0;">
                    <img alt="AstroGuru image" class="loginLogo" src="{{ asset('/assets/img/mainLogo.png') }}" >
                    <h2 class="intro-x font-bold text-2xl xl:text-3xl text-left xl:text-left">Sign In</h2>
                    <div class="intro-x mt-2 xl:hidden text-center"></div>
                    <br>
                    <div class="intro-x">
                        <form action="{{ route('advisor.post-signup') }}" method="post" id="advisor-signup">
                            {{ csrf_field() }}
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link tabbingBtn active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Basic Info</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link tabbingBtn" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Skill Details</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link tabbingBtn" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab" aria-controls="contact" aria-selected="false">Other Details</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link tabbingBtn" id="time-tab" data-bs-toggle="tab" data-bs-target="#time" type="button" role="tab" aria-controls="time" aria-selected="false">Time Availability</button>
                                </li>
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                    <div class="row">
                                        <div class="col-lg-12 mt-2">
                                            <div class="form-group">
                                                <label for="name">{{ __('Name') }}<span class="text-danger">*</span></label>
                                                <input id="name" type="text" class="form-control" placeholder="Enter Name" name="name" />
                                            </div>
                                        </div>
                                        <div class="col-lg-12 mt-2">
                                            <div class="form-group">
                                                <label for="email">{{ __('E-mail') }}<span class="text-danger">*</span></label>
                                                <input id="email" type="text" class="form-control" placeholder="Enter Email" name="email" />
                                            </div>
                                        </div>
                                        <div class="col-lg-12 mt-2 text-center">
                                            <div class="form-group">
                                                <label style="display:flex;" for="mobile">{{ __('Mobile') }}<span class="text-danger">*</span></label>
                                                <input id="mobile" type="text" class="form-control" placeholder="Enter Mobile" name="mobile"  minlength="10" maxlength="10" onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))"/>
                                                <button type="button" class="send-otp btn btn-warning btn-sm mt-2">Send OTP</button>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 mt-2 otp-field d-none">
                                            <div class="form-group">
                                                <label for="otp">{{ __('OTP') }}<span class="text-danger">*</span></label>
                                                <input id="otp" type="text" class="form-control" placeholder="Enter OTP" name="otp" />
                                            </div>
                                        </div>
                                        <div class="col-lg-12 mt-2 otp-field d-none">
                                            <button type="button" class="verify-otp mt-2 btn btn-primary btn-submit btn-sm py3 w-full xl:w-32 align-top">Verify OTP</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <label for="">Skill Detail</label>
                                        </div>
                                        <div class="col-lg-12 mt-2">
                                            <div class="form-group">
                                                <label for="gender">Gender<span class="text-danger">*</span></label>
                                                <select name="gender" id="gender" class="form-control" data-error="Please select gender" required>
                                                    <option value="male">Male</option>
                                                    <option value="female">Female</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 mt-2">
                                            <div class="form-group">
                                                <label for="dob">DOB<span class="text-danger">*</span></label>
                                                <input type="date" name="birthDate" data-error="Please select DOB" id="dob" class="form-control" max="{{ date('Y-m-d', strtotime('-18 year')) }}" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 mt-2">
                                            <label for="astrologerCategoryId">Advisor Category<span class="text-danger">*</span></label>
                                            <select name="astrologerCategoryId[]" id="astrologerCategoryId" required data-error="Please select Advisor Category" class="form-control" multiple>
                                                @foreach($astrologerCategoryIds as $astrologerCategoryId)
                                                    <option value="{{ $astrologerCategoryId->id }}">{{ $astrologerCategoryId->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-12 mt-2">
                                            <label for="primarySkill">Primary skills<span class="text-danger">*</span></label>
                                            <select name="primarySkill[]" id="primarySkill" required class="form-control" data-error="Please select Primary skills" multiple>
                                                @foreach($primarySkills as $primarySkill)
                                                    <option value="{{ $primarySkill->id }}">{{ $primarySkill->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-12 mt-2">
                                            <label for="languageKnown">Languages Known<span class="text-danger">*</span></label>
                                            <select name="languageKnown[]" id="languageKnown" required class="form-control" data-error="Please select Languages Known" multiple>
                                                @foreach($languageKnowns as $languageKnown)
                                                    <option value="{{ $languageKnown->id }}">{{ $languageKnown->languageName }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-12 mt-2">
                                            <div class="form-group">
                                                <label for="experience">Experience<span class="text-danger">*</span></label>
                                                <input type="number" name="experienceInYears" required id="experience" data-error="Please select Experience" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 mt-2 text-center">
                                            <button type="button" class="mt-2 btn btn-primary btn-submit btn-sm py3 w-full xl:w-32 align-top" onclick="validate(1)">Save & Next</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                                    <div class="row">
                                        <div class="col-lg-12 mt-2">
                                            <label for="">Other Detail</label>
                                        </div>
                                        <div class="col-lg-12 mt-2">
                                            <div class="form-group">
                                                <label for="city">{{ __('Which city do you live?') }}<span class="text-danger">*</span></label>
                                                <input id="city" type="text" class="form-control" placeholder="Enter city" name="city" data-error="Please select city" required/>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 mt-2">
                                            <div class="form-group">
                                                <label for="highestQualification">{{ __('Select your highest qualification') }}<span class="text-danger">*</span></label>
                                                <select id="highestQualification" class="form-control" placeholder="Enter Highest Qualification" name="highestQualification"  data-error="Please select your highest qualification" required>
                                                    <option value="Diploma">Diploma</option>
                                                    <option value="10th">10th</option>
                                                    <option value="12th">12th</option>
                                                    <option value="Graduate">Graduate</option>
                                                    <option value="Post Graduate">Post Graduate</option>
                                                    <option value="PhD">PhD</option>
                                                    <option value="Others">Others</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 mt-2">
                                            <div class="form-group">
                                                <label for="degree">{{ __('Degree/Diploma') }}<span class="text-danger">*</span></label>
                                                <select id="degree" class="form-control" placeholder="Enter degree" name="degree"  data-error="Please select Degree/Diploma" required>
                                                    <option value="B. Tech">B. Tech</option>
                                                    <option value="B.Sc">B.Sc</option>
                                                    <option value="B.A">B.A</option>
                                                    <option value="B.Com">B.Com</option>
                                                    <option value="B.Pharma">B. Pharma</option>
                                                    <option value="M.Tech">M. Tech</option>
                                                    <option value="M.A">M.A.</option>
                                                    <option value="M.Sc">M.Sc</option>
                                                    <option value="MBBS">MBBS</option>
                                                    <option value="Others">Others</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 mt-2">
                                            <div class="form-group">
                                                <label for="college">{{ __('College/School/University') }}<span class="text-danger">*</span></label>
                                                <input id="college" type="text" class="form-control" placeholder="Enter College/School/University" name="college" data-error="Please select College/School/University" required/>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 mt-2">
                                            <div class="form-group">
                                                <label for="bio">{{ __('Bio') }}<span class="text-danger">*</span></label>
                                                <textarea id="bio" class="form-control" placeholder="Describe Bio" name="bio" data-error="Please Enter Describe Bio" required></textarea>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 text-center">
                                            <button type="button" class="mt-2 btn btn-primary btn-submit btn-sm py3 w-full xl:w-32 align-top" onclick="validate(2)">Save & Next</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="time" role="tabpanel" aria-labelledby="time-tab">
                                    @php($weekdays=['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])
                                    <div class="row">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Day</th>
                                                    <th>From Time</th>
                                                    <th>To Time</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($weekdays as $day)
                                                    <tr>
                                                        <td>
                                                            {{ ucwords($day) }}
                                                            <input type="hidden" name="day[]" value="{{ $day }}">
                                                        </td>
                                                        <td>
                                                            <input type="time" class="form-control" name="from_time[{{$day}}][]">
                                                        </td>
                                                        <td>
                                                            <input type="time" class="form-control" name="to_time[{{$day}}][]">
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <div class="col-lg-12 text-center">
                                            <button type="button" class="mt-2 btn btn-primary btn-submit btn-sm py3 w-full xl:w-32 align-top" onclick="finalSaveSubmit()">Save & Next</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <input type="hidden" name="fcm_token">
                            <div class="col-lg-12 mt-2">
                                <hr>
                                <div class="accountText">Already have an account. <a href="{{ route('advisor.login') }}" class="">Sign In</a></div>
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
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>

    const appVersion = navigator.appVersion;

    // Detect common operating systems based on appVersion string
    let osName = "Unknown OS";
    if (appVersion.includes("Win")) {
        osName = "Windows";
    } else if (appVersion.includes("Mac")) {
        osName = "macOS";
    } else if (appVersion.includes("X11") || appVersion.includes("Linux")) {
        osName = "UNIX/Linux";
    }

    // Get user agent string
    const userAgent = navigator.userAgent;
    var spinner = $('.loader')

    setTimeout(() => {
        $('.loader').hide()
    }, 2000);

    $(document).on('submit', '#advisor-login', function (e) {
        e.preventDefault()

        let formData = new FormData(this)
        formData.append('appVersion', appVersion)
        formData.append('osName', osName)
        formData.append('userAgent', userAgent)
        formData.append('appId', 3)

        $.ajax({
            type: $(this).attr('method'),
            url: $(this).attr('action'),
            data: formData,
            dataType: "json",
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.status) {
                    toastr.success(response.message)
                    setTimeout(() => {
                        window.location.href = "{{ route('advisor.dashboard') }}"
                    }, 1000)
                } else {
                    if (response.error.length > 0) {
                        $.each(response.error, function (ind, elm) {
                            toastr.error(elm[0])
                        })
                    } else {
                        toastr.error(response.message)
                    }
                }
            },
            error: function (jqxhr, err, errthrown) {

            }
        });

    })

    let signupOTP = ''
    $(document).on('click', '.send-otp', function() {
        let name = $('[name=name]').val()
        let mobile = $('[name=mobile]').val()
        let email = $('[name=email]').val()
        $('[name=mobile]').attr('readonly')
        $.ajax({
            type: "POST",
            url: "{{ route('advisor.signup-otp') }}",
            data: {
                "_token":$('input[name=_token]').val(),
                name:name,
                mobile:mobile,
                email:email
            },
            dataType: "json",
            success: function (response) {
                if(response.status) {
                    signupOTP = response.otp
                    $('.send-otp').text('Resend OTP')
                    $('.otp-field').removeClass('d-none')

                    toastr.success(response.message)
                } else {
                    toastr.error(response.message)
                }
            },
            error: function(jqhr, errorText, throwError) {
                toastr.error(jqhr.responseJSON.message)
            }
        })
    })

    $(document).on('click', '.verify-otp', function() {
        let mobile = $('[name=mobile]').val()
        let email = $('[name=email]').val()
        let otp = $('[name=otp]').val()

        if(signupOTP == otp) {
            toastr.success('OTP verified success')
            next()
        } else {
            toastr.error('Invalid OTP')
        }

        console.log('mobile, email, otp --------- ', mobile, email, otp)
    })

    $(document).on('click', '.nav-link', function() {

        $('.nav-link').removeClass('active')

        let tab = $(this).attr('aria-controls')

        $('.tab-pane.fade').removeClass('show active')

        $(`#${tab}`).addClass('show active')

        $(this).addClass('active')
    })

    function next() {
        $('.nav-link').each(function(ind, elm) {    
            if($(elm).hasClass('active')) {
                $(elm).removeClass('active')
                $('.tab-pane.fade').eq(ind).removeClass('show active')
                $('.nav-link').eq(ind+1).addClass('active')
                $('.tab-pane.fade').eq(ind+1).addClass('show active')
                return false;
            }
        })
    }

    function finalSaveSubmit() {
        let form = $('#advisor-signup')
        let formdata = $('#advisor-signup').serialize()
        $.ajax({
            type: "POST",
            url: "{{ route('advisor.post-signup') }}",
            data: formdata,
            dataType: "json",
            success: function (response) {
                if(response.status) {
                    toastr.success(response.message)
                    setTimeout(() => {
                        window.location.href = "{{ route('advisor.login') }}"
                    }, 2000);
                }
            },
            error: function(jqhr, errText, errThrow) {
                console.log(jqhr, errText, errThrow)
                toastr.error(jqhr.responseJSON.message)
            }
        });
    }

    function validate(index) {
        console.log('--------- ', $('.tab-pane.fade').eq(index), $('.tab-pane.fade').eq(index).find('input,select'))
        let startNext = true;
        $('.tab-pane.fade').eq(index).find('input,select').each(function(ind, elm) {
            if($(elm).val().length <= 0) {
                toastr.error(`${$(elm).attr('data-error')}`)
                startNext = false
            }
        })

        if(startNext) {
            next()
        }

    }

    $('select').select2()
</script>

<script type="module">
    // Import the functions you need from the SDKs you need
    import { initializeApp } from "https://www.gstatic.com/firebasejs/12.4.0/firebase-app.js";
    // import { getAnalytics } from "https://www.gstatic.com/firebasejs/12.4.0/firebase-analytics.js";
    import { getMessaging, getToken, onMessage } from "https://www.gstatic.com/firebasejs/12.4.0/firebase-messaging.js";

    // TODO: Add SDKs for Firebase products that you want to use
    // https://firebase.google.com/docs/web/setup#available-libraries

    // Your web app's Firebase configuration
    // For Firebase JS SDK v7.20.0 and later, measurementId is optional
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

    // Initialize Firebase
    const app = initializeApp(firebaseConfig);
    const messaging = getMessaging(app)

    // Request permission for notifications
    async function requestPermission() {
        const permission = await Notification.requestPermission()
        if (permission === "granted") {
            const token = await getToken(messaging, {
                vapidKey: "BCzv6CBSKQZ7v9YjUuzJj_brefX2mmEB1g_ZAZ9Z4urRJ5SB2Kjj6Ah05SeWg-vZEZnaAe-LfuaaMmNz87iYGFY" // from Firebase Cloud Messaging settings
            })

            $('[name=fcm_token]').val(token)

            console.log('token 0000000000000000000', token)

            // Send this token to your backend server to send notifications later
        } else {
            console.log("Permission denied")
        }
    }

    requestPermission()

    // Handle foreground messages
    onMessage(messaging, (payload) => {
        console.log("Message received in foreground:", payload)
        new Notification(payload.notification.title, {
            body: payload.notification.body,
            icon: payload.data.image
        });

        // playAudio()
    });



</script>

@endpush