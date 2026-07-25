@extends('vendor.app')

@push('css_or_js')
    <title>Chat History</title>
@endpush

@section('content')
    <div class="loader"></div>
    <div class="grid grid-cols-12 gap-6">
        <div class="col-span-12 2xl:col-span-12">
            <div class="grid grid-cols-12 gap-6">
                <div class="col-span-12">
                    <div class="box">
                        <div class="box-header p-4">
                            <h1>Chat History</h1>
                        </div>
                        <div class="box-body">
                            <table class="table table-stripped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User Name</th>
                                        <th>Time</th>
                                        <th>Duration</th>
                                        <th>Rate</th>
                                        <th>Earning</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($chathistories as $call)
                                        <tr>
                                            <td>{{ $call['id'] }}</td>
                                            <td>{{ $call->user->name??'-' }}</td>
                                            <td>{{ date('d M, Y h:i A', strtotime($call->created_at)) }}</td>
                                            <td>{{ ($call->totalMin??'-') }} {{  $call->totalMin > 1 ? 'minutes' : 'minute' }} </td>
                                            <td>₹ {{ $call->chatRate??'-' }}</td>
                                            <td>₹ {{ $call->deduction??'-' }}</td>
                                            <td>{{ $call->chatStatus??'-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <th colspan="7">No record found</th>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="7">
                                            {!! $chathistories->links() !!}
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