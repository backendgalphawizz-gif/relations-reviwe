@extends('vendor.app')

@push('css_or_js')
    <title>Advisor Withdrawls</title>
@endpush

@section('content')
    <div class="loader"></div>
    <div class="grid grid-cols-11 gap-x-6">
        <div class="intro-y col-span-12 2xl:col-span-12">
            <div class="intro-y box">
                <div class="flex flex-col sm:flex-row items-center p-2 border-b border-slate-200/60 dark:border-darkmode-400">
                    <h2 class="font-medium text-base mr-auto">Advisor Withdrawls (₹ {{ $wallet->amount }})</h2>
                    <a href="{{ route('advisor.create-withdrawls') }}" class="btn btn-primary btn-sm">Withdraw</a>
                </div>

            </div>
            <table class="table table-stripped">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Amount</th>
                        <th>Payment Details</th>
                        <th>Status</th>
                        <th>Created at</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($withdrawls as $withdrawl)
                        <tr>
                            <td>{{ $withdrawl->id ?? '-' }}</td>
                            <td>{{ $withdrawl->withdrawAmount ?? '-' }}</td>
                            <td>
                                {!! $withdrawl->upiId !='' ? '<p>UPI ID: '. $withdrawl->upiId.'</p>' : ''  !!}
                                {!! $withdrawl->accountHolderName !='' ? '<p>Account Holder Name: '. $withdrawl->accountHolderName.'</p>' : ''  !!}
                                {!! $withdrawl->accountNumber !='' ? '<p>Account Number: '. $withdrawl->accountNumber.'</p>' : ''  !!}
                                {!! $withdrawl->ifscCode !='' ? '<p>IFSC Code: '. $withdrawl->ifscCode.'</p>' : ''  !!}
                            </td>
                            <td>{{ $withdrawl->status }}</td>
                            <td>{{ date('d M, Y', strtotime($withdrawl->created_at)) }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="6">
                            {!! $withdrawls->links() !!}
                            {{-- @if (count($transactions) > 0)
                                @if ($totalRecords > 0)
                                    <div>
                                        <div class="d-inline text-slate-500 pagecount">Showing {{ $start }} to {{ $end }} of {{ $totalRecords }} entries</div>
                                    
                                        <div class="d-inline addbtn intro-y col-span-12">
                                            <nav class="w-full sm:w-auto sm:mr-auto">
                                                <ul class="pagination">
                                                    <li class="page-item {{ $page == 1 ? 'disabled' : '' }}">
                                                        <a class="page-link" href="{{ route('advisor.transactions', ['page' => $page - 1]) }}">
                                                            <i class="w-4 h-4" data-lucide="chevron-left"></i>
                                                        </a>
                                                    </li>
                                                    @for ($i = 0; $i < $totalPages; $i++)
                                                        <li class="page-item {{ $page == $i + 1 ? 'active' : '' }} ">
                                                            <a class="page-link"
                                                                href="{{ route('advisor.transactions', ['page' => $i + 1]) }}">{{ $i + 1 }}</a>
                                                        </li>
                                                    @endfor
                                                    <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                                                        <a class="page-link" href="{{ route('advisor.transactions', ['page' => $page + 1]) }}">
                                                            <i class="w-4 h-4" data-lucide="chevron-right"></i>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </nav>
                                        </div>
                                    </div>
                                @endif
                            @endif --}}
                        </th>
                    </tr>
                </tfoot>
            </table>

        </div>

    </div>

@endsection

@push('script')
    <link href="//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $('.select2').select2()

        $(document).on('change','[name=callStatus]', function() {
            let status = $(this).val()
            $('.wait-time').addClass('d-none')
            if(status == 'Wait Time') {
                $('.wait-time').removeClass('d-none')
            }
        })

        $(document).on('submit','#edit-profile', function(e) {
            e.preventDefault()

            let formData = new FormData(this)

            jQuery.ajax({
                type: "POST",
                url: $(this).attr('action'),
                data: formData,
                dataType: "json",
                processData: false,
                contentType: false,
                success: function (response) {
                    if(response.status) {
                        toastr.success(response.message)
                        setTimeout(() => {
                            window.location.reload()
                        }, 1000)
                    } else {
                        jQuery.each(response.error, function(ind, elm) {
                            toastr.error(elm[0])
                        })
                    }
                    console.log('response ------------ ', response)
                }
            });

        })

    </script>
@endpush