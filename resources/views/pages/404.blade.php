@extends('../layout/' . $layout)



@section('head')

    <title>Error Page - Midone - Tailwind HTML Admin Template</title>

@endsection



@section('content')

    <div class="container">

        @php

            $logo = DB::table('systemflag')

                ->where('name', 'AdminLogo')

                ->select('value')

                ->first();

        @endphp

        <!-- BEGIN: Error Page -->

        <div class="error-page flex flex-col  items-center justify-center h-screen text-center lg:text-left">

            <div class=" error-card overlay" >
                <div class="-intro-x">
                    <img alt="Midone - HTML Admin Template" style="width: 100px;"class="h-48 lg:h-auto" src="/{{$logo->value }}">
                </div>
                <div class=" mt-10 lg:mt-0">
                    <div class="intro-x errorCodeText font-medium">404</div>

                    <div class="intro-x text-xl lg:text-3xl font-medium mt-8">Oops. This page has gone missing.</div>

                    <div class="intro-x text-lg mt-3">You may have mistyped the address or the page may have moved.</div>

                    <a

                    class="intro-x btn-primary btn py-3 px-4 text-white border-white dark:border-darkmode-400 dark:text-slate-200 mt-5" href="/admin/login">Back

                    to Home</a>

                </div>
            </div>

        </div>

        <!-- END: Error Page -->

    </div>

@endsection

