@extends('../layout/' . $layout)

@section('head')
    <title>Error Page - Midone - Tailwind HTML Admin Template</title>
@endsection

@section('content')
    <style>
        /* Keep original look; ensure card/button stay clickable above bg */
        .error-page {
            z-index: 1 !important;
        }
        .error-card {
            position: relative;
            z-index: 2;
            pointer-events: auto;
        }
    </style>
    <div class="container">
        @php
            $logo = DB::table('systemflag')
                ->where('name', 'AdminLogo')
                ->select('value')
                ->first();

            if (empty($homeUrl)) {
                $homeUrl = session('error_home_url');
            }

            if (empty($homeUrl)) {
                if (Auth::guard('advisor')->check()) {
                    $homeUrl = url('/advisor/dashboard');
                } elseif (Auth::guard('web')->check() || Auth::check()) {
                    $homeUrl = url('/admin/dashboard');
                } else {
                    $fromAdvisor = str_contains((string) url()->previous(), '/advisor')
                        || str_contains((string) request()->headers->get('referer'), '/advisor');
                    $homeUrl = $fromAdvisor ? url('/advisor/login') : url('/admin/login');
                }
            }
        @endphp

        <!-- BEGIN: Error Page -->
        <div class="error-page flex flex-col items-center justify-center h-screen text-center lg:text-left">
            <div class="error-card overlay">
                <div class="-intro-x">
                    <img alt="Midone - HTML Admin Template" style="width: 100px;" class="h-48 lg:h-auto"
                        src="/{{ $logo->value }}">
                </div>
                <div class="mt-10 lg:mt-0">
                    <div class="intro-x errorCodeText font-medium">404</div>
                    <div class="intro-x text-xl lg:text-3xl font-medium mt-8">Oops. This page has gone missing.</div>
                    <div class="intro-x text-lg mt-3">You may have mistyped the address or the page may have moved.</div>
                    <a id="error-home-btn"
                        class="intro-x btn-primary btn py-3 px-4 text-white border-white dark:border-darkmode-400 dark:text-slate-200 mt-5"
                        href="{{ $homeUrl }}">Back to Home</a>
                </div>
            </div>
        </div>
        <!-- END: Error Page -->
    </div>
@endsection

@section('script')
    <script>
        (function() {
            var homeUrl = @json($homeUrl);
            var btn = document.getElementById('error-home-btn');
            if (btn && homeUrl) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    window.location.replace(homeUrl);
                });
            }

            window.addEventListener('pageshow', function(event) {
                if (window.location.pathname !== '/admin/404' || !homeUrl) {
                    return;
                }
                var nav = performance.getEntriesByType('navigation')[0];
                var isBack = event.persisted || (nav && nav.type === 'back_forward');
                if (isBack) {
                    window.location.replace(homeUrl);
                }
            });
        })();
    </script>
@endsection
