@extends('vendor.app')

@push('css_or_js')
    <title>Advisor Dashboard</title>
@endpush

@section('content')
    <div class="loader"></div>
    <div class="grid grid-cols-12 gap-6">
        <div class="col-span-12 2xl:col-span-12">
            <div class="grid grid-cols-12 gap-6">
                <!-- BEGIN: General Report -->
                <div class="col-span-12">
                    <div class="grid grid-cols-12 gap-6">
                        <div class="col-span-12 sm:col-span-6 xl:col-span-2 intro-y">
                            <a href="#">
                                <div class="report-box zoom-in">
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
                            <a href="#">
                                <div class="report-box zoom-in">
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
                            <a href="#">
                                <div class="report-box zoom-in">
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
                            <a href="#">
                            <div class="report-box zoom-in">
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
                            <a href="#">
                            <div class="report-box zoom-in">
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
                <div class="col-span-12">
                    <div class="box">
                        <div class="box-header p-4">
                            <h1>Incoming Call Requests</h1>
                        </div>
                        <div class="box-body">
                            <table class="table table-stripped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User Name</th>
                                        <th>Call Time</th>
                                        <th>Call Type</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($callRequests as $call)
                                        <tr>
                                            <td>{{ $call['id'] }}</td>
                                            <td>{{ $call->user->name??'-' }}</td>
                                            <td>{{ $call->created_at??'-' }}</td>
                                            <td>{{ $call->type??'-' }}</td>
                                            <td><span class="badge bg-warning">{{ $call->callStatus??'-' }}</span></td>
                                            <td>
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
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <th colspan="6">
                                                No New Call Found
                                            </th>
                                        </tr>
                                    @endforelse
                                </tbody>
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
        
    </script>
@endpush