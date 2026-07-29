<!-- BEGIN: Top Bar -->

@if(auth('advisor')->user())

<?php

define('EXPRESSION', '/(?=[A-Z])/');

?>

<div

    class="top-bar-boxed {{ isset($class) ? $class : '' }} h-[70px] md:h-[65px] z-[51] border-b border-white/[0.08] mt-12 md:mt-0 -mx-3 sm:-mx-8 md:-mx-0 px-3 md:border-b-0 relative md:fixed md:inset-x-0 md:top-0 sm:px-8 md:px-10 md:pt-10 md:bg-gradient-to-b md:from-slate-100 md:to-transparent dark:md:from-darkmode-700">

    <div class="h-full flex items-center">

        @php

            $teamMember = DB::table('teammember')

                ->where('userId', auth('advisor')->user()->id)

                ->first();

            if ($teamMember) {

                $dashboardPage = DB::table('rolepages')

                    ->where('adminPageId', 1)

                    ->where('teamRoleId', $teamMember->teamRoleId)

                    ->first();

            }

            else{

                $dashboardPage = true;

            }

        @endphp

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

        @if($dashboardPage)

        <a href="{{ route('advisor.dashboard') }}" class="logo -intro-x hidden md:flex xl:w-[180px] block">

            <img alt="AstroGuru image" class="logo__image w-6" src="{{ asset($logo->value) }}"

                style="height: 50px;width: 100%; max-width: 50px;border-radius:50%">

            <span class="logo__text text-white text-lg ml-5 mt-2.5" style="vertical-align: center; display:none">

                {{ $appName->value }}

            </span>

        </a>

        @else

        <a class="logo -intro-x hidden md:flex xl:w-[180px] block">

            <img alt="AstroGuru image" class="logo__image w-6" src="{{ asset($logo->value) }}"

                style="height: 50px;width: 100%; max-width: 50px;border-radius:50%">

            <span class="logo__text text-white text-lg ml-5 mt-2.5" style="vertical-align: center; display:none">

                {{ $appName->value }}

            </span>

        </a>

        @endif

        <!-- END: Logo -->

        <!-- BEGIN: Breadcrumb -->

        <nav aria-label="breadcrumb" class="-intro-x h-[45px] mr-auto">

            <ol class="breadcrumb breadcrumb-light">



                <li class="breadcrumb-item">

                    @if ($dashboardPage)

                        <a href="{{ route('advisor.dashboard') }}">Home</a>

                    @else

                        <a>Home</a>

                    @endif

                </li>

                {{-- <li class="breadcrumb-item active" aria-current="page" style="text-transform: capitalize;">

                    <a href="{{ route(Request::segment(2)) }}">

                        @if (preg_match('/[A-Z]/', Request::segment(1)))

                            <?php

                            $capital = preg_split(EXPRESSION, Request::segment(1));

                            $header = implode(' - ', $capital);

                            ?>

                            {{ $header }}

                        @else

                        @endif

                    </a>

                </li> --}}

                @if (Request::segment(2))

                    <li class="breadcrumb-item active" aria-current="page" style="text-transform: capitalize;">

                        @if (Request::segment(3))

                            @if (preg_match('/[A-Z]/', Request::segment(2)))

                                <?php

                                $capital = preg_split(EXPRESSION, Request::segment(2));

                                $header = implode(' - ', $capital);

                                ?>

                                <a href="{{ route('advisor.dashboard') }}"> {{ $header }}</a>

                            @else

                                <a href="{{ route('advisor.' . Request::segment(2)) }}"> {{ Request::segment(2) }}</a>

                            @endif

                    <li class="breadcrumb-item active" aria-current="page" style="text-transform: capitalize;">

                        @if (preg_match('/[A-Z]/', Request::segment(3)))

                            <?php

                            $capital = preg_split(EXPRESSION, Request::segment(3));

                            $header = implode(' - ', $capital);

                            ?>

                            {{ $header }}

                        @else

                            {{ Request::segment(3) }}

                        @endif

                    </li>

                @else

                    @if (preg_match('/[A-Z]/', Request::segment(2)))

                        <?php

                        $capital = preg_split(EXPRESSION, Request::segment(2));

                        $header = implode(' - ', $capital);

                        ?>

                        {{ $header }}

                    @else

                        {{ Request::segment(2) }}

                    @endif

                @endif

                </li>

                @endif

            </ol>

        </nav>

        <!-- END: Notifications -->

        @php
            $topBarAstrologer = \App\Models\AstrologerModel\Astrologer::where('userId', auth('advisor')->id())->first();
            $topBarCallStatus = $topBarAstrologer->callStatus ?? 'Offline';
        @endphp

        @if($topBarAstrologer)
        <div class="intro-x mr-4 flex items-center gap-2" style="min-width:160px;">
            <span class="hidden sm:inline text-xs font-medium text-slate-600 dark:text-slate-300 whitespace-nowrap">Status</span>
            <select id="advisor-topbar-call-status"
                class="form-select form-select-sm"
                style="min-width:120px;height:36px;border-radius:8px;font-weight:600;"
                data-astrologer-id="{{ $topBarAstrologer->id }}">
                <option value="Online" {{ strtolower($topBarCallStatus) === 'online' ? 'selected' : '' }}>Online</option>
                <option value="Offline" {{ strtolower($topBarCallStatus) === 'offline' ? 'selected' : '' }}>Offline</option>
                <option value="Wait Time" {{ strtolower($topBarCallStatus) === 'wait time' ? 'selected' : '' }}>Wait Time</option>
            </select>
        </div>
        @endif

        <!-- BEGIN: Account Menu -->

        <div class="intro-x dropdown w-8 h-8">

            <div class="dropdown-toggle w-8 h-8 rounded-full overflow-hidden shadow-lg image-fit zoom-in scale-110"

                role="button" aria-expanded="false" data-tw-toggle="dropdown">

                @if (auth('advisor')->user())

                    <img class="rounded-full" src="{{ asset(auth('advisor')->user()->profile) }}"

                        onerror="this.onerror=null;this.src='/build/assets/images/person.png';" alt="Admin Profile" />

                @else

                    <img class="rounded-full" src='{{ asset('build/assets/images/person.png') }}' alt="Advisor Profile" />

                @endif

            </div>

            <div class="dropdown-menu w-56">

                <ul class="dropdown-content bg-primary/80 before:block before:absolute before:bg-black before:inset-0 before:rounded-md before:z-[-1] text-white">
                    <li class="p-2">
                        @if (auth('advisor')->user())
                            <div class="font-medium">{{ auth('advisor')->user()->name }}</div>
                        @endif
                    </li>

                    <li>
                        <hr class="dropdown-divider border-white/[0.08]">
                    </li>

                    <li>
                        <a href="{{ route('advisor.profile') }}" class="dropdown-item hover:bg-white/5">
                            <i data-lucide="toggle-right" class="w-4 h-4 mr-2"></i> Edit Profile
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('advisor.logout') }}" class="dropdown-item hover:bg-white/5">
                            <i data-lucide="toggle-right" class="w-4 h-4 mr-2"></i> Logout
                        </a>
                    </li>

                </ul>

            </div>

        </div>
        <!-- END: Account Menu -->
    </div>

</div>

<div id="change-modal" class="modal" tabindex="-1" aria-hidden="true">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <h2 class="font-medium text-base mr-auto">Change Password</h2>

            </div>

            <form action="{{ route('changePassword') }}" method="POST" enctype="multipart/form-data" id="change-password">

                @csrf

                <div id="input" class="p-5">

                    <div class="input">

                        <div>

                            <label for="name" class="form-label">Old Password</label>

                            <input id="old" name="old" type="text" class="form-control"

                                placeholder="Old Password" required>

                            <div class="text-danger print-oldPassword-error-msg mb-2" style="display:none">

                                <ul></ul>

                            </div>

                        </div>

                    </div>

                    <div class="input mt-3">

                        <div>

                            <label for="name" class="form-label">New Password</label>

                            <input id="new" name="new" type="password" class="form-control"

                                placeholder="New Password" required>

                            <div class="text-danger print-newpassword-error-msg mb-2" style="display:none">

                                <ul></ul>

                            </div>

                        </div>



                    </div>

                    <div class="mt-5"><button type="submit" class="btn btn-primary shadow-md mr-2">Change

                            Password</button>

                    </div>

                </div>

            </form>

        </div>

    </div>

</div>

@section('script')

    <script type="text/javascript">

        jQuery.ajaxSetup({

            headers: {

                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

            }

        })


        jQuery("#change-password").submit(function(e) {

            e.preventDefault();

            console.log("#change-password", "#change-password")

            jQuery.ajax({

                type: 'POST',

                url: "{{ route('changePassword') }}",

                data: new FormData(this),

                processData: false,

                contentType: false,

                success: function(data) {

                    if (jQuery.isEmptyObject(data.error)) {

                        toastr.options = {

                            "closeButton": true,

                            "progressBar": true

                        }

                        location.href = "/admin/login"

                    } else {

                        printErrorMsg(data.error);

                    }

                }

            });



        });



        function printErrorMsg(msg) {

            jQuery(".print-oldPassword-error-msg").find("ul").html('');

            jQuery.each(msg, function(key, value) {

                if (!key) {

                    jQuery(".print-oldPassword-error-msg").css('display', 'block');

                    jQuery(".print-oldPassword-error-msg").find("ul").append('<li>' + value + '</li>');

                }

            });

        }

    </script>

    <script>
        (function() {
            const select = document.getElementById('advisor-topbar-call-status');
            if (!select) return;

            select.addEventListener('change', function() {
                const status = this.value;
                const id = this.getAttribute('data-astrologer-id');
                const formData = new FormData();
                formData.append('id', id);
                formData.append('callStatus', status);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '');

                fetch(@json(route('advisor.update-call-status')), {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: formData
                })
                .then(r => r.json())
                .then(function(response) {
                    if (response.status) {
                        if (typeof toastr !== 'undefined') {
                            toastr.success(response.message || ('Status set to ' + status));
                        }
                    } else if (typeof toastr !== 'undefined') {
                        toastr.error('Failed to update status');
                    }
                })
                .catch(function() {
                    if (typeof toastr !== 'undefined') toastr.error('Failed to update status');
                });
            });
        })();
    </script>

@endsection

@endif

<!-- END: Top Bar -->

