@extends('../layout/' . $layout)

@section('head')
<title>Login</title>
<style>
    .password-wrapper {
        position: relative;
        width: 100%;
    }
    .password-wrapper .login__input {
        width: 100%;
        padding-right: 3rem !important;
        position: relative;
        z-index: 1 !important;
    }
    .password-toggle-btn {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        z-index: 50 !important;
        border: none;
        background: transparent;
        color: #64748b !important;
        cursor: pointer;
        padding: 4px;
        line-height: 1;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        font-size: 18px;
        user-select: none;
    }
    .password-toggle-btn:hover {
        color: #334155 !important;
    }
    .password-toggle-btn .fa {
        display: inline-block !important;
        font-size: 18px !important;
        line-height: 1 !important;
        color: #64748b !important;
    }
    .password-toggle-btn:hover .fa {
        color: #334155 !important;
    }
    .password-toggle-btn .icon-eye-off {
        display: none !important;
    }
    .password-toggle-btn.show-password .icon-eye {
        display: none !important;
    }
    .password-toggle-btn.show-password .icon-eye-off {
        display: inline-block !important;
    }
</style>
@endsection
@section('content')
<div class="loader"></div>
<div class="customContainer">
<div class="loginCard">
                   
                        <img src="{{ asset('/assets/img/mainLogo.png') }}" alt="" class="loginLogo">
                    <h2 class="intro-x font-bold text-2xl xl:text-3xl text-center xl:text-left">Sign In</h2>
                    <div class="intro-x mt-2 xl:hidden text-center"></div>
            
                    <div class="intro-x mt-5">
                        <form>
                            <div class="alert alert-danger print-error-msg mb-2" style="display:none">
                                <ul></ul>
                            </div>
                            <input id="email" type="text" class="intro-x login__input form-control py-3 px-4 block"
                                placeholder="Email" name="email">
                            <div class="text-danger print-email-error-msg mb-2" style="display:none">
                                <ul></ul>
                            </div>
                            <div class="password-wrapper mt-4" style="position:relative;width:100%;">
                                <input id="password" type="password" name="password" class="login__input form-control py-3 px-4 block" placeholder="Password">
                                <span class="password-toggle-btn" id="toggle-password" role="button" tabindex="0" aria-label="Show password" style="position:absolute;right:14px;top:50%;transform:translateY(-50%);z-index:50;display:inline-flex!important;cursor:pointer;">
                                    <i class="fa fa-eye icon-eye" aria-hidden="true"></i>
                                    <i class="fa fa-eye-slash icon-eye-off" aria-hidden="true" style="display:none;"></i>
                                </span>
                            </div>
                            <div class="text-danger print-password-error-msg mb-2" >
                                <ul></ul>
                            </div>
                            <button class="mt-4 btn btn-primary btn-submit py-3 px-4 w-full xl:w-32 xl:mr-3 align-top">Login</button>
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

@section('script')
<style>
    .password-wrapper .login__input {
        z-index: 1 !important;
    }
    .password-toggle-btn {
        z-index: 50 !important;
    }
</style>
<script type="module">
    var spinner = $('.loader');
    jQuery.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    })

    jQuery("#toggle-password").on("click keydown", function(e) {
        if (e.type === "keydown" && e.key !== "Enter" && e.key !== " ") {
            return;
        }
        e.preventDefault();

        var passwordInput = jQuery("#password");
        var toggleBtn = jQuery(this);
        var eyeIcon = toggleBtn.find(".icon-eye");
        var eyeOffIcon = toggleBtn.find(".icon-eye-off");

        if (passwordInput.attr("type") === "password") {
            passwordInput.attr("type", "text");
            toggleBtn.addClass("show-password").attr("aria-label", "Hide password");
            eyeIcon.hide();
            eyeOffIcon.show();
        } else {
            passwordInput.attr("type", "password");
            toggleBtn.removeClass("show-password").attr("aria-label", "Show password");
            eyeIcon.show();
            eyeOffIcon.hide();
        }
    });

    jQuery(".btn-submit").click(function(e) {

        e.preventDefault();

        var email = $("#email").val();
        var password = $("#password").val();

        jQuery.ajax({

            type: 'POST',
            url: "{{ route('loginApi') }}",
            data: {
                email: email,
                password: password
            },
            success: function(data) {
                if (jQuery.isEmptyObject(data.error)) {
                    location.href = data.first;
                } else {
                    printErrorMsg(data.error);
                }
            }
        });

    });


    function printErrorMsg(msg) {
        jQuery(".print-error-msg").find("ul").html('');
        jQuery(".print-email-error-msg").find("ul").html('');
        jQuery(".print-password-error-msg").find("ul").html('');
        jQuery.each(msg, function(key, value) {
            if (key == 'email') {
                jQuery(".print-email-error-msg").css('display', 'block');
                jQuery(".print-email-error-msg").find("ul").append('<li>' + value + '</li>');
            }
            if (key == 'password') {
                jQuery(".print-password-error-msg").css('display', 'block');
                jQuery(".print-password-error-msg").find("ul").append('<li>' + value + '</li>');
            }
            if (!key) {
                jQuery(".print-error-msg").css('display', 'block');
                jQuery(".print-error-msg").find("ul").append('<li>' + value + '</li>');
            }
        });
    }
</script>
<script>
    $(window).on('load', function() {
        $('.loader').hide();
    })
</script>
@endsection