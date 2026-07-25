 @extends('../layout/' . $layout)

 @section('head')

     <head>
         <meta charset="UTF-8">
         <meta name="viewport" content="width=device-width, initial-scale=1.0">
         <meta http-equiv="X-UA-Compatible" content="ie=edge">
         <title>Login</title>
         <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" defer>
         <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css" rel="stylesheet" defer/>
         <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
         <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
         <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
     </head>
 @endsection

 @section('content')
     <div class="container sm:px-10">
         <div class="block xl:grid grid-cols-2 gap-4">
             <div class="hidden xl:flex flex-col min-h-screen">
                 @php
                     $logo = DB::table('systemflag')
                         ->where('name', 'AdminLogo')
                         ->select('value')
                         ->first();
                 @endphp
                 <div class="my-auto">
                     <img alt="AstroGuru image" class="-intro-x w-1/2 -mt-16" src="{{ url($logo->value) }}"
                         style="height: 200px;width: 200px">
                     <div class="-intro-x text-white font-medium text-4xl leading-tight mt-3">Relationship Manager</div>
                     <div class="-intro-x mt-5 text-lg text-white text-opacity-70 dark:text-slate-400">Best care
                         by experts</div>
                 </div>
             </div>
             <div class="h-screen xl:h-auto flex py-5 xl:py-0 my-10 xl:my-0">
                 <div
                     class="my-auto mx-auto xl:ml-20 bg-white dark:bg-darkmode-600 xl:bg-transparent px-5 sm:px-8 py-8 xl:p-0 rounded-md shadow-md xl:shadow-none w-full sm:w-3/4 lg:w-2/4 xl:w-auto">
                     <h2 class="intro-x font-bold text-2xl xl:text-3xl text-center xl:text-left">Sign In</h2>
                     <div class="intro-x mt-2 text-slate-400 xl:hidden text-center">A few more clicks to sign in to your
                         account. Manage all your e-commerce accounts in one place</div>
                     <div class="intro-x mt-8">
                         <form id="login-form">
                             <input id="email" type="text" class="intro-x login__input form-control py-3 px-4 block"
                                 placeholder="Email">
                             <div id="error-email" class="login__input-error text-danger mt-2"></div>
                             <input id="password" type="password"
                                 class="intro-x login__input form-control py-3 px-4 block mt-4" placeholder="Password">
                             <div id="error-password" class="login__input-error text-danger mt-2"></div>
                         </form>
                     </div>
                     <div class="intro-x mt-1 xl:mt-8 text-center xl:text-left">
                         <button id="btn-login"
                             class="btn btn-primary py-3 px-4 w-full xl:w-32 xl:mr-3 align-top">Login</button>
                     </div>

                 </div>
             </div>
         </div>
     </div>
 @endsection

 @section('script')
     <script type="module">
        (function () {
            async function login() {
                // Reset state
                $('#login-form').find('.login__input').removeClass('border-danger')
                $('#login-form').find('.login__input-error').html('')

                // Post form
                let email = $('#email').val()
                let password = $('#password').val()

                // Loading state
                $('#btn-login').html('<i data-loading-icon="spinning-circles" data-color="white" class="w-5 h-5 mx-auto"></i>')
                tailwind.svgLoader()
                await helper.delay(1500)

                axios.post('login', {
                    email: email,
                    password: password
                }).then(res => {
                    axios.post('https://af.codefuse.org/api/login', {
                    email: email,
                    password: password
                    }).then(serverRes => {
                        sessionStorage.clear();
                        sessionStorage.setItem('token',serverRes.data.token);
                    var href = '{{ route("get-session",":token") }}';
                    href = href.replace(':token',serverRes.data.token);
                                       
                    location.href = '/dashboard';

                    }).catch(err => {
                        $('#btn-login').html('Login')
                        if (err.response.data.message != 'Wrong email or password.') {
                            for (const [key, val] of Object.entries(err.response.data.errors)) {
                                $('#${key}').addClass('border-danger')
                                $('#error-${key}').html(val)
                            }
                        } else {
                            $('#password').addClass('border-danger')
                            $('#error-password').html(err.response.data.message)
                        }
                    })
                }).catch(err => {
                    $('#btn-login').html('Login')
                    if (err.response.data.message != 'Wrong email or password.') {
                        for (const [key, val] of Object.entries(err.response.data.errors)) {
                            $(`#${key}`).addClass('border-danger')
                            $(`#error-${key}`).html(val)
                        }
                    } else {
                        $(`#password`).addClass('border-danger')
                        $(`#error-password`).html(err.response.data.message)
                    }
                })
            }

            $('#login-form').on('keyup', function(e) {
                if (e.keyCode === 13) {
                    login()
                }
            })

            $('#btn-login').on('click', function() {
                login()
            })
        })()
    </script>
 @endsection
