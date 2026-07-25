@extends('../layout/' . $layout)



@section('subhead')

    <title>Recharge Amount</title>

@endsection



@section('subcontent')

    <div class="loader"></div>

    <h2 class="intro-y text-lg font-medium mt-10 d-inline">Recharge Amount</h2>

    <a href="javascript:;" data-tw-toggle="modal" data-tw-target="#add-skill"

        class="d-inline mt-10 btn btn-primary shadow-md mr-2 addbtn">Add

        Amount</a>

    <div class="grid grid-cols-12 gap-6">

        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">

        </div>

    </div>

    <!-- BEGIN: Data List -->

    @if (count($rechargeAmount) > 0)

        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible withoutsearch">

            <table class="table table-report" aria-label="skill">

                <thead class="sticky-top">

                    <tr>

                        <th class="whitespace-nowrap">#</th>

                        <th class="whitespace-nowrap">Amount</th>

                        <th class="text-center whitespace-nowrap">ACTIONS</th>

                    </tr>

                </thead>

                <tbody>

                    @php $no = 0; @endphp

                    @php

                        $currency = DB::table('systemflag')

                            ->where('name', 'Currency')

                            ->select('value')

                            ->first();

                    @endphp

                    @foreach ($rechargeAmount as $item)

                        <tr class="intro-x">

                            <td>{{ ($page - 1) * 15 + ++$no }}</td>

                            <td>



                                <div class="font-medium whitespace-nowrap">{{$currency->value}}{{ $item->amount }}

                                </div>

                            </td>

                            <td class="table-report__action w-56">

                                <div class="flex justify-center items-center">

                                    <a id="editbtn" href="javascript:;" onclick="delbtn({{ $item->id }})"

                                        value="{{ $item->id }}" class="flex items-center text-danger"

                                        data-tw-target="#delete-confirmation-modal" data-tw-toggle="modal"><i

                                            data-lucide="trash-2" class="editbtn w-4 h-4 mr-1"></i>Delete</a>

                                </div>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    @else

        <div class="intro-y" style="height:100%">

            <div style="display:flex;align-items:center;height:100%;">

                <div style="margin:auto">

                    <img src="/build/assets/images/nodata.png" style="height:290px" alt="noData">

                    <h3 class="text-center">No Data Available</h3>

                </div>

            </div>

        </div>

    @endif

    <!-- END: Data List -->

    <div id="add-skill" class="modal" tabindex="-1" aria-hidden="true">

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header">

                    <h2 class="font-medium text-base mr-auto">Add Recharge Amount </h2>
                    <button type="button" class="btn btn-danger" data-tw-dismiss="modal">Close</button>

                </div>



                <div id="form-validation" class="p-5">

                    <div class="preview">

                        <form action="{{ route('addRechargeAmount') }}" method="POST" enctype="multipart/form-data"

                            id="add-form">

                            @csrf

                            <div class="input-form">

                                <label for="name" class="form-label w-full flex flex-col sm:flex-row">

                                    Amount

                                </label>

                                <input type="text" name="amount" id="amount" class="form-control"

                                    placeholder="Enter Amount" required onkeypress="return (event.charCode !=8 && event.charCode ==0 || (event.charCode >= 48 && event.charCode <= 57))">

                                <div class="text-danger print-amount-error-msg mb-2" style="display:none">

                                    <ul></ul>

                                </div>

                            </div>

                            <div class="mt-5"><button type="submit"

                                    class="btn btn-primary shadow-md mr-2 validate-form btn-recharge-submit">Add

                                    Recharge Amount</button>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div><!-- BEGIN: Pagination -->

    @if (count($rechargeAmount) > 0)

        @if ($totalRecords > 0)

            <div>

                <div class="d-inline text-slate-500 pagecount">Showing {{ $start }} to {{ $end }} of

                    {{ $totalRecords }} entries</div>

        @endif

        <div class="d-inline addbtn intro-y col-span-12">

            <nav class="w-full sm:w-auto sm:mr-auto">

                <ul class="pagination">

                    <li class="page-item {{ $page == 1 ? 'disabled' : '' }}">

                        <a class="page-link" href="{{ route('rechargeAmount', ['page' => $page - 1]) }}">

                            <i class="w-4 h-4" data-lucide="chevron-left"></i>

                        </a>

                    </li>

                    @for ($i = 0; $i < $totalPages; $i++)

                        <li class="page-item {{ $page == $i + 1 ? 'active' : '' }} ">

                            <a class="page-link"

                                href="{{ route('rechargeAmount', ['page' => $i + 1]) }}">{{ $i + 1 }}</a>

                        </li>

                    @endfor

                    <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">

                        <a class="page-link" href="{{ route('rechargeAmount', ['page' => $page + 1]) }}">

                            <i class="w-4 h-4" data-lucide="chevron-right"></i>

                        </a>

                    </li>

                </ul>

            </nav>

        </div>

    @endif

    <div id="delete-confirmation-modal" class="modal" tabindex="-1" aria-hidden="true">

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-body p-0">

                    <div class="p-5 text-center">

                        <i data-lucide="x-circle" class="w-16 h-16 text-danger mx-auto mt-3"></i>



                        <div class="text-3xl mt-5">Are you sure?</div>

                        <div class="text-slate-500 mt-2">Do you really want to delete these records? <br>This process

                            cannot be undone.</div>

                    </div>



                    <form action="{{ route('deleteRechargeAmount') }} " method="POST">

                        @csrf

                        @method('DELETE')

                        <input type="hidden" id="del_id" name="del_id">

                        <div class="px-5 pb-8 text-center">

                            <button type="button" data-tw-dismiss="modal"

                                class="btn btn-outline-secondary w-24 mr-1">Cancel</button>

                            <button class="btn btn-danger w-24">@method('DELETE')Delete</button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

@endsection



@section('script')

    <script type="text/javascript">

        function delbtn($id) {

            $('#del_id').val($id);

        }



        function numbersOnly(event) {

            var regex = new RegExp("^[0-9]");
            var key = String.fromCharCode(event.charCode ? event.which : event.charCode);
            if (regex.test(key)) {
                event.preventDefault();
                return false;
            }

        }



        function printErrorMsg(msg) {

            jQuery(".print-amount-error-msg").find("ul").html('');

            jQuery.each(msg, function(key, value) {

                if (key == 'amount') {

                    jQuery(".print-amount-error-msg").css('display', 'block');

                    jQuery(".print-amount-error-msg").find("ul").append('<li>' + value + '</li>');

                }

            });

        }

    </script>

    <script>

        $(window).on('load', function() {

            $('.loader').hide();

        })

    </script>

@endsection

