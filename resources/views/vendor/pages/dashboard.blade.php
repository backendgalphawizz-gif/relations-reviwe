@extends('vendor.app')

@push('css_or_js')
    <title>Advisor Dashboard</title>
    <style>
        .report-box-active {
            outline: 2px solid #426f7f;
            border-radius: 0.5rem;
        }
        .report-box a,
        a .report-box {
            cursor: pointer;
        }
    </style>
@endpush

@section('content')
    <div class="loader"></div>
    @if(session('error'))
        <div class="alert alert-danger-soft show flex items-center mb-4" role="alert">
            <i data-lucide="alert-circle" class="w-6 h-6 mr-2"></i>
            {{ session('error') }}
        </div>
    @endif
    @php $activeFilter = $filter ?? 'incoming'; @endphp
    <div class="grid grid-cols-12 gap-6">
        <div class="col-span-12 2xl:col-span-12">
            <div class="grid grid-cols-12 gap-6">
                <!-- BEGIN: General Report -->
                <div class="col-span-12">
                    <div class="grid grid-cols-12 gap-6">
                        <div class="col-span-12 sm:col-span-6 xl:col-span-2 intro-y">
                            <a href="{{ route('advisor.dashboard', ['filter' => 'calls']) }}">
                                <div class="report-box zoom-in {{ $activeFilter === 'calls' ? 'report-box-active' : '' }}">
                                    <div class="box p-5">
                                        <div class="flex">
                                            <i data-lucide="phone-call" class="report-box__icon text-primary"></i>
                                            <div class="ml-auto">
                                            </div>
                                        </div>
                                        <div class="text-3xl font-medium leading-8 mt-6">{{ $result['totalCallRequest']??0 }}
                                        </div>
                                        <div class="text-base text-slate-500 mt-1">Call Request</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-span-12 sm:col-span-6 xl:col-span-2 intro-y">
                            <a href="{{ route('advisor.dashboard', ['filter' => 'running']) }}">
                                <div class="report-box zoom-in {{ $activeFilter === 'running' ? 'report-box-active' : '' }}">
                                    <div class="box p-5">
                                        <div class="flex">
                                            <i data-lucide="phone-outgoing" class="report-box__icon text-primary"></i>
                                            <div class="ml-auto">
                                            </div>
                                        </div>
                                        <div class="text-3xl font-medium leading-8 mt-6">{{ $result['totalRunningCallRequest']??0 }}
                                        </div>
                                        <div class="text-base text-slate-500 mt-1">Running Calls</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-span-12 sm:col-span-6 xl:col-span-2 intro-y">
                            <a href="{{ route('advisor.dashboard', ['filter' => 'rejected']) }}">
                                <div class="report-box zoom-in {{ $activeFilter === 'rejected' ? 'report-box-active' : '' }}">
                                    <div class="box p-5">
                                        <div class="flex">
                                            <i data-lucide="phone-off" class="report-box__icon text-primary"></i>
                                            <div class="ml-auto">
                                            </div>
                                        </div>
                                        <div class="text-3xl font-medium leading-8 mt-6">{{ $result['totalRejectedCallRequest']??0 }}
                                        </div>
                                        <div class="text-base text-slate-500 mt-1">Rejected Call</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-span-12 sm:col-span-6 xl:col-span-2 intro-y">
                            <a href="{{ route('advisor.dashboard', ['filter' => 'missed']) }}">
                                <div class="report-box zoom-in {{ $activeFilter === 'missed' ? 'report-box-active' : '' }}">
                                    <div class="box p-5">
                                        <div class="flex">
                                            <i data-lucide="phone-missed" class="report-box__icon text-danger"></i>
                                            <div class="ml-auto">
                                            </div>
                                        </div>
                                        <div class="text-3xl font-medium leading-8 mt-6">{{ $result['totalMissedCallRequest']??0 }}
                                        </div>
                                        <div class="text-base text-slate-500 mt-1">Missed Call</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-span-12 sm:col-span-6 xl:col-span-2 intro-y d-none">
                            <a href="#">
                                <div class="report-box zoom-in">
                                    <div class="box p-5">

                                        <div class="flex">
                                            <i data-lucide="message-square" class="report-box__icon text-pending"></i>
                                            <div class="ml-auto">

                                            </div>
                                        </div>

                                        <div class="text-3xl font-medium leading-8 mt-6">{{ $result['totalChatRequest']??0 }}
                                        </div>
                                        <div class="text-base text-slate-500 mt-1">Chat Request</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-span-12 sm:col-span-6 xl:col-span-2 intro-y">
                            <a href="{{ route('advisor.dashboard', ['filter' => 'minutes']) }}">
                            <div class="report-box zoom-in {{ $activeFilter === 'minutes' ? 'report-box-active' : '' }}">
                                <div class="box p-5">
                                    <div class="flex">
                                        <i data-lucide="file-text" class="report-box__icon text-warning"></i>

                                    </div>
                                    <div class="text-3xl font-medium leading-8 mt-6">
                                        {{ $result['totalminutes']??0 }}
                                    </div>
                                    <div class="text-base text-slate-500 mt-1">Total Minutes</div>
                                </div>
                            </div>
                            </a>
                        </div>
                        <div class="col-span-12 sm:col-span-6 xl:col-span-2 intro-y">
                            <a href="{{ route('advisor.dashboard', ['filter' => 'earning']) }}">
                            <div class="report-box zoom-in {{ $activeFilter === 'earning' ? 'report-box-active' : '' }}">
                                <div class="box p-5">
                                    <div class="flex">
                                        <i data-lucide="file-text" class="report-box__icon text-warning"></i>

                                    </div>
                                    <div class="text-3xl font-medium leading-8 mt-6">
                                        ₹ {{ round($result['totalEarning']??0, 2) }}
                                    </div>
                                    <div class="text-base text-slate-500 mt-1">Total Earning</div>
                                </div>
                            </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-span-12" id="dashboard-list">
                    <div class="box">
                        <div class="box-header p-4 flex items-center justify-between">
                            <h1>{{ $listTitle ?? 'Incoming Call Requests' }}</h1>
                            @if(($activeFilter ?? 'incoming') !== 'incoming')
                                <a href="{{ route('advisor.dashboard') }}" class="btn btn-outline-secondary btn-sm">Show Incoming</a>
                            @endif
                        </div>
                        <div class="box-body">
                            <table class="table table-stripped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User Name</th>
                                        <th>Call Time</th>
                                        <th>Call Type</th>
                                        @if(in_array($activeFilter, ['minutes', 'earning', 'calls', 'rejected', 'missed'], true))
                                            <th>Duration</th>
                                            <th>Earning</th>
                                        @endif
                                        <th>Status</th>
                                        @if($activeFilter === 'missed')
                                            <th>Handled By</th>
                                            <th>Missed By</th>
                                        @endif
                                        @if($activeFilter === 'rejected')
                                            <th>Rejected By</th>
                                        @endif
                                        @if($showActions ?? true)
                                            <th>Action</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($callRequests as $call)
                                        <tr>
                                            <td>{{ $call['id'] }}</td>
                                            <td>{{ $call->user->name??'-' }}</td>
                                            <td>{{ $call->created_at ? date('d M, Y h:i A', strtotime($call->created_at)) : '-' }}</td>
                                            <td>{{ $call->type??'-' }}</td>
                                            @if(in_array($activeFilter, ['minutes', 'earning', 'calls', 'rejected', 'missed'], true))
                                                <td>{{ $call->totalMin ?? 0 }} {{ ($call->totalMin ?? 0) > 1 ? 'minutes' : 'minute' }}</td>
                                                <td>₹ {{ round($call->deductionFromAstrologer ?? $call->deduction ?? 0, 2) }}</td>
                                            @endif
                                            <td>
                                                @if($activeFilter === 'missed')
                                                    <span class="badge bg-danger">Missed</span>
                                                @elseif($activeFilter === 'rejected')
                                                    <span class="badge bg-danger">Rejected</span>
                                                @else
                                                    <span class="badge bg-warning">{{ $call->callStatus??'-' }}</span>
                                                @endif
                                            </td>
                                            @if($activeFilter === 'missed')
                                                <td>{{ $call->astrologer->name ?? ('Advisor #'.$call->astrologerId) }}</td>
                                                <td>{{ $call->rejectedByName ?? 'Time Over' }}</td>
                                            @endif
                                            @if($activeFilter === 'rejected')
                                                <td>{{ $call->rejectedByName ?? '-' }}</td>
                                            @endif
                                            @if($showActions ?? true)
                                            <td>
                                                @if(in_array($call->callStatus, ['Pending', 'Accepted', 'Confirmed'], true))
                                                    <a href="{{ route('advisor.start-call', ['chatId' => $call['id'], 'type' => 'accept']) }}" class="btn btn-primary btn-sm">
                                                        @if($call->callStatus=='Confirmed')
                                                            Join here too
                                                        @else
                                                            Call
                                                        @endif
                                                    </a>
                                                    @if($call->callStatus=='Pending')
                                                        <a href="{{ route('advisor.start-call', ['chatId' => $call['id'], 'type' => 'reject']) }}" class="btn btn-danger btn-sm">Reject</a>
                                                    @endif
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            @endif
                                        </tr>
                                    @empty
                                        <tr>
                                            <th colspan="8">
                                                No record found
                                            </th>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="8">
                                            {!! $callRequests->links('vendor.pagination.advisor') !!}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-span-12 d-none">
                    <div class="box">
                        <div class="box-header p-4">
                            <h1>Incoming Chat Requests</h1>
                        </div>
                        <div class="box-body">
                            <table class="table table-stripped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User Name</th>
                                        <th>Call Time</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($chatRequests as $call)
                                        <tr>
                                            <td>{{ $call['id'] }}</td>
                                            <td>{{ $call->user->name??'-' }}</td>
                                            <td>{{ $call->created_at??'-' }}</td>
                                            <td><span class="badge bg-warning">{{ $call->chatStatus??'-' }}</span></td>
                                            <td>
                                                <a href="{{ route('advisor.start-chat', ['chatId' => $call['id'], 'type' => 'accept']) }}" class="btn btn-primary btn-sm">Accept</a>
                                                <a href="{{ route('advisor.start-chat', ['chatId' => $call['id'], 'type' => 'reject']) }}" class="btn btn-danger btn-sm">Reject</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <th colspan="4">
                                                No New Chat Found
                                            </th>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script>
        @if(request()->has('filter'))
            document.getElementById('dashboard-list')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        @endif
    </script>
@endpush
