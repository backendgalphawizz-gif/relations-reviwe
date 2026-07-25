@extends('vendor.app')

@push('css_or_js')
    <title>Call History</title>
@endpush

@section('content')
    <div class="loader"></div>
    <div class="grid grid-cols-12 gap-6">
        <div class="col-span-12 2xl:col-span-12">
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box">
                        <div class="box-header p-4">
                            <h1>Call History</h1>
                        </div>
                        <div class="box-body">
                            <table class="table table-stripped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User Name</th>
                                        <th>Call Type</th>
                                        <th>Call Time</th>
                                        <th>Call Duration</th>
                                        <th>Call Rate</th>
                                        <th>Call Charge</th>
                                        <th>Earning</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($callhistories as $call)
                                        <tr>
                                            <td>{{ $call['id'] }}</td>
                                            <td>{{ $call->user->name??'-' }}</td>
                                            <td><span class="badge bg-{{ $call->type == 'audio' ? 'warning' : 'success'}}">{{ ucwords($call->type??'-') }}</span></td>
                                            <td>{{ date('d M, Y h:i A', strtotime($call->created_at)) }}</td>
                                            <td>{{ ($call->totalMin??'-') }} {{  $call->totalMin > 1 ? 'minutes' : 'minute' }} </td>
                                            <td>₹ {{ $call->callRate??'-' }}</td>
                                            <td>₹ {{ $call->deduction??'-' }}</td>
                                            <td>₹ {{ $call->deductionFromAstrologer??'-' }}</td>
                                            <td><span class="badge bg-success">{{ $call->callStatus??'-' }}</span></td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <th colspan="7">
                                                No record Found
                                            </th>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="7">
                                            {!! $callhistories->links() !!}
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')

@endpush