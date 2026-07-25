@extends('../layout/' . $layout)

@section('subhead')
    <title>Dashboard</title>
@endsection

@section('subcontent')
    <div class="loader"></div>
    <div class="grid grid-cols-12 gap-6">
        <div class="col-span-12 2xl:col-span-12">
            <div class="grid grid-cols-12 gap-6">
                <!-- BEGIN: General Report -->
                <div class="col-span-12 mt-8">
                    <div class="intro-y flex items-center h-10">
                        <h2 class="text-lg font-medium truncate mr-5">Welcome to Dashboard</h2>
                        @guest
                            <h3>Guest</h3>
                        @endguest
                    </div>
                    @foreach ($result as $dash)
                        <div class="grid grid-cols-12 gap-6 mt-5">
                            <div class="col-span-12 sm:col-span-6 xl:col-span-4 intro-y">
                                <a href="{{ route('callHistory') }}">
                                    <div class="report-box zoom-in">
                                        <div class="box p-5">
                                            <div class="flex">
                                                <i data-lucide="phone-call" class="report-box__icon text-primary"></i>
                                                <div class="ml-auto">
                                                </div>
                                            </div>
                                            <div class="text-3xl font-medium leading-8 mt-6">{{ $dash['totalCallRequest'] }}
                                            </div>
                                            <div class="text-base text-slate-500 mt-1">Call Request</div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            {{-- <div class="col-span-12 sm:col-span-6 xl:col-span-4 intro-y">
                                <a href="{{ route('chatHistory') }}">
                                    <div class="report-box zoom-in">
                                        <div class="box p-5">

                                            <div class="flex">
                                                <i data-lucide="message-square" class="report-box__icon text-pending"></i>
                                                <div class="ml-auto">

                                                </div>
                                            </div>

                                            <div class="text-3xl font-medium leading-8 mt-6">{{ $dash['totalChatRequest'] }}
                                            </div>
                                            <div class="text-base text-slate-500 mt-1">Chat Request</div>
                                        </div>
                                    </div>
                                </a>
                            </div> --}}


                            {{-- <div class="col-span-12 sm:col-span-6 xl:col-span-4 intro-y">
                                <a href="{{ route('reportrequest') }}">
                                <div class="report-box zoom-in">
                                    <div class="box p-5">
                                        <div class="flex">
                                            <i data-lucide="file-text" class="report-box__icon text-warning"></i>

                                        </div>
                                        <div class="text-3xl font-medium leading-8 mt-6">
                                            {{ $dash['totalReportRequest'] }}
                                        </div>
                                        <div class="text-base text-slate-500 mt-1">Report Request</div>
                                    </div>
                                </div>
                                </a>
                            </div> --}}
                            <div class="col-span-12 sm:col-span-6 xl:col-span-4 intro-y">
                                <a href="#">
                                    <div class="report-box">
                                        <div class="box p-5">
                                            <div class="flex">
                                                <i data-lucide="inr-sign" class="report-box__icon text-success"></i>
                                            </div>
                                            <div class="text-3xl font-medium leading-8 mt-6">₹ {{ round($dash['totalEarning'],2) }}
                                            </div>
                                            <div class="text-base text-slate-500 mt-1">Total Call Earning</div>
                                        </div>
                                    </div>
                                </a>
                            </div> 
                            <div class="col-span-12 sm:col-span-6 xl:col-span-4 intro-y">
                                <a href="#">
                                    <div class="report-box">
                                        <div class="box p-5">
                                            <div class="flex">
                                                <i data-lucide="dollar-sign" class="report-box__icon text-success"></i>
                                            </div>
                                            <div class="text-3xl font-medium leading-8 mt-6">₹ {{ round($dash['adminCommission'],2) }}
                                            </div>
                                            <div class="text-base text-slate-500 mt-1">Admin Earning</div>
                                        </div>
                                    </div>
                                </a>
                            </div> 
                            <div class="col-span-12 sm:col-span-6 xl:col-span-4 intro-y">
                                <a href="{{ route('customers') }}">
                                    <div class="report-box zoom-in">
                                        <div class="box p-5">
                                            <div class="flex">
                                                <i data-lucide="user" class="report-box__icon text-success"></i>
                                                <div class="ml-auto">
                                                </div>
                                            </div>
                                            <div class="text-3xl font-medium leading-8 mt-6">{{ $dash['totalCustomer'] }}
                                            </div>
                                            <div class="text-base text-slate-500 mt-1">Total Customer</div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <div class="col-span-12 sm:col-span-6 xl:col-span-4 intro-y">
                                <a href="{{ route('astrologers') }}">
                                    <div class="report-box zoom-in">
                                        <div class="box p-5">
                                            <div class="flex">
                                                <i data-lucide="user" class="report-box__icon text-success"></i>
                                                <div class="ml-auto">
                                                </div>
                                            </div>
                                            <div class="text-3xl font-medium leading-8 mt-6">{{ $dash['totalAstrologer'] }}
                                            </div>
                                            <div class="text-base text-slate-500 mt-1">Total Advisors</div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                </div>
                <div class="col-span-12 lg:col-span-6 mt-8">
                    <div class="intro-y block sm:flex items-center h-10">
                        <h2 class="text-lg font-medium truncate mr-5">Monthly Earning Report</h2>
                       
                    </div>
                    <h6>Last 12 Months</h6>
                    <div class="intro-y box p-5 mt-12 sm:mt-5">
                        <div class="report-chart">
                            <div class="h-[275px]">
                                <canvas id="myChart" height="100px"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-span-12 lg:col-span-6 mt-8">
                    <div class="intro-y block sm:flex items-center h-10">
                        <h2 class="text-lg font-medium truncate mr-5">Monthly Request</h2>
                        
                    </div>
                    <h6>Last 12 Months</h6>
                    <div class="intro-y box p-5 mt-12 sm:mt-5">
                        <div class="report-chart">
                            <div class="h-[275px]">
                                <canvas id="requestChart" height="100px"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- BEGIN: Weekly Top Products -->
        <div class="col-span-12 mt-6">
            <div class="intro-y block sm:flex items-center h-10">
                <h2 class="text-lg font-medium truncate mr-5">Top Advisor</h2>
                <div class="flex items-center sm:ml-auto mt-3 sm:mt-0">
                </div>
            </div>
            <div class="intro-y overflow-auto lg:overflow-visible mt-8 sm:mt-0">
                <table class="table table-report sm:mt-2" aria-label="">
                    <thead>
                        <tr>
                            <th class="whitespace-nowrap">Profile</th>
                            <th class="whitespace-nowrap">Name</th>
                            <th class="text-center whitespace-nowrap">ContactNo</th>
                            <th class="text-center whitespace-nowrap">Total Request</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dash['topAstrologer'] as $top)
                            <tr class="intro-x">
                                <td class="w-40">
                                    <div class="flex">
                                        <div class="w-10 h-10 image-fit zoom-in">
                                            <img class="rounded-full" src="/{{ $top->profileImage }}"
                                                onerror="this.onerror=null;this.src='/build/assets/images/person.png';"
                                                alt="Advisor image" />
                                        </div>
                                    </div>
                                </td>
                                <td class="w-40">
                                    <a class="font-medium whitespace-nowrap">{{ $top->name }}</a>
                                    <div class="text-slate-500 text-xs whitespace-nowrap mt-0.5">
                                    </div>
                                </td>
                                <td class="text-center w-40">{{ $top->contactNo }}</td>
                                <td class="w-40">
                                    <div class="flex items-center justify-center">
                                        <i data-lucide="phone-call" class="w-4 h-4 mr-2"></i>
                                        {{ $top->totalCallRequest }}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
        @if (count($dash['unverifiedAstrologer']) > 0)
            <div class="col-span-12 mt-6">
                <div class="intro-y block sm:flex items-center h-10">
                    <h2 class="text-lg font-medium truncate mr-5">Unverified Advisors</h2>
                    <div class="flex items-center sm:ml-auto mt-3 sm:mt-0">
                    </div>
                </div>
                <div class="intro-y overflow-auto lg:overflow-visible mt-8 sm:mt-0">
                    <table class="table table-report sm:mt-2" aria-label="advisor">
                        <thead>
                            <tr>
                                <th class="whitespace-nowrap">Profile</th>
                                <th class="whitespace-nowrap">Name</th>
                                <th class="text-center whitespace-nowrap">ContactNo</th>
                                <th class="text-center whitespace-nowrap">Skills</th>
                                <th class="text-center whitespace-nowrap">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dash['unverifiedAstrologer'] as $unverified)
                                <tr class="intro-x">
                                    <td class="w-40">
                                        <div class="flex">
                                            <div class="w-10 h-10 image-fit zoom-in">
                                                <img class="tooltip rounded-full" alt="profileImage"
                                                    src="/{{ $unverified->profileImage }}"
                                                    onerror="this.onerror=null;this.src='/build/assets/images/person.png';" />
                                            </div>
                                        </div>
                                    </td>
                                    <td class="w-40">
                                        <a class="font-medium whitespace-nowrap">{{ $unverified->name }}</a>
                                        <div class="text-slate-500 text-xs whitespace-nowrap mt-0.5">
                                        </div>
                                    </td>
                                    <td class="text-center w-40">{{ $unverified->contactNo }}</td>
                                    <td class="w-40">
                                        <div class="flex items-center justify-center">
                                            <a class="font-medium whitespace-nowrap">{{ $unverified->allSkill }}</a>
                                        </div>
                                    </td>
                                    <td class="w-40 text-center">
                                        <div class="flex justify-center items-center">
                                            <a onclick="editbtn({{ $unverified->id }},{{ $unverified->isVerified }})"
                                                href="javascript:;" data-tw-target="#verifiedAdvisor"id="editbtn"
                                                class="flex items-center mr-3 text-success" data-tw-toggle="modal">
                                                @if ($unverified->isVerified)
                                                    <i style="color:brown"
                                                        data-lucide="{{ $unverified->isVerified ? 'lock' : 'unlock' }}"
                                                        class="w-4 h-4 mr-1"></i>
                                                @else
                                                    <i data-lucide="{{ $unverified->isVerified ? 'lock' : 'unlock' }}"
                                                        class="w-4 h-4 mr-1"></i>
                                                @endif
                                                @if ($unverified->isVerified)
                                                    <span style="color:brown"> UnVerified</span>
                                                @else
                                                    Verified
                                                @endif
                                            </a>
                                            <a class="flex items-center mr-3 text-success"
                                                href="astrologers/{{ $unverified->id }}">
                                                <i data-lucide="eye" class="w-4 h-4 mr-1"></i>View
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        @endif
        @endforeach
        <!-- END: Weekly Top Products -->
    </div>
    <div id="verifiedAdvisor" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="p-5 text-center">
                        <div class="text-3xl mt-5">Are You Sure?</div>
                        <div class="text-slate-500 mt-2" id="verified">You want Verified!</div>
                    </div>
                    <form action="{{ route('verifiedAstrologer') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="filed_id" name="filed_id">
                        <div class="px-5 pb-8 text-center"><button class="btn btn-primary mr-3" id="btnVerified">Yes,
                                Verified it!
                            </button><a type="button" data-tw-dismiss="modal" class="btn btn-secondary w-24"
                                onclick="location.reload();">Cancel</a>
                        </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    </div>

    </div>
@endsection
@section('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script type="text/javascript">
        function editbtn($id, $isVerified) {
            var id = $id;
            $cid = id;

            $('#filed_id').val($cid);
            var verified = $isVerified ? 'UnVerified' : 'Verified';
            document.getElementById('verified').innerHTML = "You want to " + verified;
            document.getElementById('btnVerified').innerHTML = "Yes, " +
                verified + " it";
        }
        var labels = {{ Js::from($labels) }};
        var users = {{ Js::from($data) }};
        
        var astroLabels = {{ Js::from($astroLabels) }};
        var astroData = {{ Js::from($astroData) }};

        var calls = {{ Js::from($callData) }};
        var vcalls = {{ Js::from($vcallData) }};
        var chats = {{ Js::from($chatData) }};
        var reports = {{ Js::from($reportData) }};

        const data = {
            labels: labels,
            datasets: [
                {
                    label: 'Admin Commission Earning',
                    backgroundColor: '#426f80',
                    borderColor: '#426f80',
                    data: users,
                },
                {
                    label: 'Advisor Earning',
                    backgroundColor: '#1b9c6d',
                    borderColor: '#1b9c6d',
                    data: astroData
                }
            ]
        };

        const config = {
            type: 'line',
            data: data,
            options: {}
        };

        const myChart = new Chart(
            document.getElementById('myChart'),
            config
        );
        const barChartData = {
            labels: labels,
            datasets: [
                {
                    label: 'Audio Call',
                    backgroundColor: '#1b9c6d',
                    borderColor: '#1b9c6d',
                    data: calls,
                },
                {
                    label: 'Video Call',
                    backgroundColor: '#0fa3c8',
                    borderColor: '#0fa3c8',
                    data: vcalls
                }
            ]
        };

        const requestConfig = {
            type: 'bar',
            data: barChartData,
            options: {}
        };

        const requestChart = new Chart(
            document.getElementById('requestChart'),
            requestConfig
        );
    </script>
    <script>
        $(window).on('load', function() {
            $('.loader').hide();
        })
    </script>
@endsection
