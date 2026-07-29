@extends('../layout/' . $layout)

@section('subhead')
    <title>Dashboard</title>
    <style>
        .admin-dash-pagination {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            width: 100%;
        }
        .admin-dash-pagination__info {
            color: #64748b;
            font-size: 0.9rem;
        }
        .admin-dash-pagination__info strong {
            color: #0f172a;
            font-weight: 700;
        }
        .admin-dash-pagination__list {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 6px;
            margin: 0;
            padding: 0;
            list-style: none;
        }
        .admin-dash-pagination__list li a,
        .admin-dash-pagination__list li span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 38px;
            height: 38px;
            padding: 0 12px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #334155;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.9rem;
            line-height: 1;
        }
        .admin-dash-pagination__list li a:hover {
            background: #f1f5f9;
            border-color: #94a3b8;
            color: #0f172a;
        }
        .admin-dash-pagination__list li.is-active span {
            background: #426f7f !important;
            border-color: #426f7f !important;
            color: #ffffff !important;
        }
        .admin-dash-pagination__list li.is-disabled span {
            opacity: 0.45;
            cursor: not-allowed;
            background: #f8fafc;
        }
    </style>
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
                    <div class="intro-y box overflow-hidden">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 px-5 pt-5 pb-2 border-b border-slate-200/60">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-800">Monthly Earning Report</h2>
                                <p class="text-slate-500 text-xs mt-0.5">Admin commission vs advisor earnings · last 12 months</p>
                            </div>
                            <div class="flex items-center gap-4 text-sm font-medium text-slate-700">
                                <span class="inline-flex items-center gap-2">
                                    <span style="display:inline-block;width:14px;height:14px;border-radius:50%;background:#426f7f;border:2px solid #fff;box-shadow:0 0 0 1px #426f7f;"></span>
                                    Admin Commission
                                </span>
                                <span class="inline-flex items-center gap-2">
                                    <span style="display:inline-block;width:14px;height:14px;border-radius:50%;background:#0d9488;border:2px solid #fff;box-shadow:0 0 0 1px #0d9488;"></span>
                                    Advisor Earning
                                </span>
                            </div>
                        </div>
                        <div class="p-4 sm:p-5">
                            <div class="report-chart relative" style="height:320px;">
                                <canvas id="myChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-span-12 lg:col-span-6 mt-8">
                    <div class="intro-y box overflow-hidden">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 px-5 pt-5 pb-2 border-b border-slate-200/60">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-800">Monthly Request</h2>
                                <p class="text-slate-500 text-xs mt-0.5">Audio vs video call volume · last 12 months</p>
                            </div>
                            <div class="flex items-center gap-4 text-sm font-medium text-slate-700">
                                <span class="inline-flex items-center gap-2">
                                    <span style="display:inline-block;width:14px;height:14px;border-radius:4px;background:#0d9488;"></span>
                                    Audio Call
                                </span>
                                <span class="inline-flex items-center gap-2">
                                    <span style="display:inline-block;width:14px;height:14px;border-radius:4px;background:#0284c7;"></span>
                                    Video Call
                                </span>
                            </div>
                        </div>
                        <div class="p-4 sm:p-5">
                            <div class="report-chart relative" style="height:320px;">
                                <canvas id="requestChart"></canvas>
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
        @if (isset($unverifiedAstrologer) && $unverifiedAstrologer->total() > 0)
            <div class="col-span-12 mt-6" id="unverified-advisors">
                <div class="intro-y block sm:flex items-center h-10">
                    <h2 class="text-lg font-medium truncate mr-5">Unverified Advisors</h2>
                    <div class="flex items-center sm:ml-auto mt-3 sm:mt-0 text-slate-500 text-sm">
                        {{ $unverifiedAstrologer->total() }} total
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
                            @foreach ($unverifiedAstrologer as $unverified)
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
                                            <a onclick="editbtn({{ $unverified->id }},{{ $unverified->isVerified ? 1 : 0 }})"
                                                href="javascript:;" data-tw-target="#verifiedAdvisor" id="editbtn"
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
                                                href="{{ route('astrologer-detail', ['id' => $unverified->id]) }}">
                                                <i data-lucide="eye" class="w-4 h-4 mr-1"></i>View
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="intro-y box p-4 mt-3">
                    {!! $unverifiedAstrologer->fragment('unverified-advisors')->onEachSide(2)->links('vendor.pagination.admin-dashboard') !!}
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
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
        var astroData = {{ Js::from($astroData) }};
        var calls = {{ Js::from($callData) }};
        var vcalls = {{ Js::from($vcallData) }};

        const chartFont = {
            family: 'Nunito, system-ui, sans-serif',
            size: 11
        };

        const gridColor = 'rgba(148, 163, 184, 0.18)';
        const tickColor = '#64748b';

        function formatInr(value) {
            const n = Number(value) || 0;
            if (n >= 100000) return '₹' + (n / 100000).toFixed(1) + 'L';
            if (n >= 1000) return '₹' + (n / 1000).toFixed(1) + 'k';
            return '₹' + n.toLocaleString('en-IN');
        }

        const earningCtx = document.getElementById('myChart');
        const myChart = new Chart(earningCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Admin Commission',
                        data: users,
                        borderColor: '#426f7f',
                        backgroundColor: 'rgba(66, 111, 127, 0.12)',
                        borderWidth: 3,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#426f7f',
                        pointBorderWidth: 2,
                        tension: 0.35,
                        fill: true,
                    },
                    {
                        label: 'Advisor Earning',
                        data: astroData,
                        borderColor: '#0d9488',
                        backgroundColor: 'rgba(13, 148, 136, 0.10)',
                        borderWidth: 3,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#0d9488',
                        pointBorderWidth: 2,
                        tension: 0.35,
                        fill: true,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 18,
                            boxWidth: 10,
                            color: '#334155',
                            font: { size: 12, weight: '600' }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { size: 13, weight: '600' },
                        bodyFont: { size: 12 },
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: true,
                        callbacks: {
                            label: function(ctx) {
                                return ' ' + ctx.dataset.label + ': ' + formatInr(ctx.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: tickColor,
                            font: chartFont,
                            maxRotation: 45,
                            minRotation: 0,
                            autoSkip: true,
                            maxTicksLimit: 8
                        },
                        border: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        grace: '8%',
                        grid: {
                            color: gridColor,
                            drawBorder: false
                        },
                        ticks: {
                            color: tickColor,
                            font: chartFont,
                            callback: function(value) {
                                return formatInr(value);
                            }
                        },
                        border: { display: false }
                    }
                }
            }
        });

        const requestCtx = document.getElementById('requestChart');
        const requestChart = new Chart(requestCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Audio Call',
                        data: calls,
                        backgroundColor: '#0d9488',
                        hoverBackgroundColor: '#0f766e',
                        borderRadius: 6,
                        borderSkipped: false,
                        maxBarThickness: 28,
                    },
                    {
                        label: 'Video Call',
                        data: vcalls,
                        backgroundColor: '#0284c7',
                        hoverBackgroundColor: '#0369a1',
                        borderRadius: 6,
                        borderSkipped: false,
                        maxBarThickness: 28,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'rectRounded',
                            padding: 18,
                            boxWidth: 12,
                            color: '#334155',
                            font: { size: 12, weight: '600' }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleFont: { size: 13, weight: '600' },
                        bodyFont: { size: 12 },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(ctx) {
                                return ' ' + ctx.dataset.label + ': ' + (ctx.parsed.y || 0);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: tickColor,
                            font: chartFont,
                            maxRotation: 45,
                            minRotation: 0,
                            autoSkip: true,
                            maxTicksLimit: 8
                        },
                        border: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        grace: '8%',
                        ticks: {
                            color: tickColor,
                            font: chartFont,
                            precision: 0,
                            stepSize: 5
                        },
                        grid: {
                            color: gridColor,
                            drawBorder: false
                        },
                        border: { display: false }
                    }
                },
                datasets: {
                    bar: {
                        categoryPercentage: 0.65,
                        barPercentage: 0.8
                    }
                }
            }
        });
    </script>
    <script>
        $(window).on('load', function() {
            $('.loader').hide();
        })
    </script>
@endsection
