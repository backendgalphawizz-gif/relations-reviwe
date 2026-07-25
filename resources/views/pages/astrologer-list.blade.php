@extends('../layout/' . $layout)

@section('subhead')
    <title>Astrologers</title>
@endsection

@section('subcontent')
    <div class="loader"></div>
    <h2 class="intro-y text-lg font-medium mt-10 d-inline">Advisors</h2>
    @if ($totalRecords > 0)
        <a class="btn btn-primary shadow-md mr-2 mt-10 d-inline addbtn printpdf">PDF</a>
        <a class="btn btn-primary shadow-md mr-2 mt-10 d-inline addbtn downloadcsv">CSV</a>
    @endif
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center">


            <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
                <form action="{{ route('astrologers') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="w-56 relative text-slate-500" style="display:inline-block">
                        <input value="{{ $searchString }}" type="text" class="form-control w-56 box pr-10"
                            placeholder="Search..." id="searchString" name="searchString">
                        @if (!$searchString)
                            <i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0" data-lucide="search"></i>
                        @else
                            <a href="{{ route('astrologers') }}"><i class="w-4 h-4 absolute my-auto inset-y-0 mr-3 right-0"
                                    data-lucide="x"></i></a>
                        @endif
                    </div>
                    <button class="btn btn-primary shadow-md mr-2">Search</button>
                </form>
            </div>
        </div>
        <!-- BEGIN: Data List -->
    </div>
    @if (count($astrologers) > 0)
        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible list-table">
            <table class="table table-report mt-2" aria-label="astrologer-list">
                <thead class="sticky-top">
                    <tr>
                        <th class="whitespace-nowrap">#</th>
                        <th class="whitespace-nowrap">PROFILE</th>
                        <th class="whitespace-nowrap">NAME</th>
                        <th class="text-center whitespace-nowrap">CONTACT NO.</th>
                        <th class="text-center whitespace-nowrap">GENDER</th>
                        <th class="text-center whitespace-nowrap">TOTAL REQUEST</th>
                        <th class="text-center whitespace-nowrap">STATUS</th>
                        <th class="text-center whitespace-nowrap">ACTIONS</th>
                    </tr>
                </thead>
                <tbody id="todo-list">
                    @php
                        $no = 0;
                    @endphp
                    @foreach ($astrologers as $astro)
                        <tr class="intro-x">
                            <td>{{ ($page - 1) * 15 + ++$no }}</td>
                            <td>
                                <?php $profile = (!empty($astro['profileImage'])) ? $astro['profileImage'] : ''; ?>
                                <div class="flex">
                                    <div class="w-10 h-10 image-fit zoom-in">
                                        <img class="rounded-full" src="{{ url($profile) }}"
                                            onerror="this.onerror=null;this.src='/build/assets/images/person.png';"
                                            alt="Advisor image" />
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="font-medium whitespace-nowrap">{{ $astro['name'] }}</div>
                            </td>
                            <td class="text-center">{{ $astro['contactNo'] }}</td>
                            <td class="text-center">{{ $astro['gender'] }}</td>
                            <td class="w-40">
                                <div class="flex items-center justify-center">
                                    <i data-lucide="phone-call" class="w-4 h-4 mr-2"></i>
                                    {{ $astro['totalCallRequest'] }}
                                </div>
                            </td>

                            <td class="w-40">
                                <div
                                    class="flex items-center justify-center {{ $astro['isVerified'] ? 'text-success' : 'text-danger' }}">
                                    <i data-lucide="check-square" class="w-4 h-4 mr-2"></i>
                                    {{ $astro['isVerified'] ? 'Verified' : 'unverified' }}
                                </div>
                            </td>

                            <td class="table-report__action w-56">

                                <div class="flex justify-center items-center">
                                    <a onclick="editbtn({{ $astro['id'] }},{{ $astro['isVerified'] }})"
                                        data-tw-target="#verifiedAdvisor"id="editbtn" href="javascript:;"
                                        class="flex items-center mr-3 text-success" data-tw-toggle="modal">
                                        @if ($astro['isVerified'])
                                            <i style="color:brown"
                                                data-lucide="{{ $astro['isVerified'] ? 'lock' : 'unlock' }}"
                                                class="w-4 h-4 mr-1"></i>
                                        @else
                                            <i data-lucide="{{ $astro['isVerified'] ? 'lock' : 'unlock' }}"
                                                class="w-4 h-4 mr-1"></i>
                                        @endif
                                        @if ($astro['isVerified'])
                                            <span style="color:brown"> unverified</span>
                                        @else
                                            Verified
                                        @endif
                                    </a>
                                    <a class="flex items-center mr-3" href="advisors/edit/{{ $astro['id'] }}">
                                        <i data-lucide="check-square" class="w-4 h-4 mr-1"></i>Edit
                                    </a>
                                    <a class="flex items-center mr-3 text-success" href="advisors/{{ $astro['id'] }}">
                                        <i data-lucide="eye" class="w-4 h-4 mr-1"></i>View
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- END: Data List -->
        <!-- BEGIN: Pagination -->
        @if ($totalRecords > 0)
            <div class="d-inline text-slate-500 pagecount">Showing {{ $start }} to {{ $end }} of
                {{ $totalRecords }} entries</div>
        @endif
        <div class="d-inline addbtn intro-y col-span-12">
            <nav class="w-full sm:w-auto sm:mr-auto">
                <ul class="pagination">
                    <li class="page-item {{ $page == 1 ? 'disabled' : '' }}">
                        <a class="page-link"
                            href="{{ route('astrologers', ['page' => $page - 1, 'searchString' => $searchString]) }}">
                            <i class="w-4 h-4" data-lucide="chevron-left"></i>
                        </a>
                    </li>
                    @for ($i = 0; $i < $totalPages; $i++)
                        <li class="page-item {{ $page == $i + 1 ? 'active' : '' }} ">
                            <a class="page-link"
                                href="{{ route('astrologers', ['page' => $i + 1, 'searchString' => $searchString]) }}">{{ $i + 1 }}</a>
                        </li>
                    @endfor
                    <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                        <a class="page-link"
                            href="{{ route('astrologers', ['page' => $page + 1, 'searchString' => $searchString]) }}">
                            <i class="w-4 h-4" data-lucide="chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    @else
        <div class="intro-y mt-5" style="height:100%">
            <div style="display:flex;align-items:center;height:100%;">
                <div style="margin:auto">
                    <img src="/build/assets/images/nodata.png" style="height:290px" alt="noData">
                    <h3 class="text-center">No Data Available</h3>
                </div>
            </div>
        </div>
    @endif
    <!-- END: Pagination -->

    <!-- BEGIN: Modal Content -->
    <div id="verifiedAdvisor" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="p-5 text-center">
                        <div class="text-3xl mt-5">Are You Sure?</div>
                        <div class="text-slate-500 mt-2" id="verified">You want Verified!</div>
                    </div>
                    <form action="{{ route('verifiedAstrologerApi') }}" method="POST" enctype="multipart/form-data">
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
    </div> <!-- END: Modal Content -->
    <!-- END: Delete Confirmation Modal -->
@endsection
@section('script')
<script type="text/javascript">
    function editbtn($id, $isVerified) {
        var id = $id;
        $cid = id;

        $('#filed_id').val($cid);
        var verified = $isVerified ? 'unverified' : 'Verified';
        document.getElementById('verified').innerHTML = "You want to " + verified;
        document.getElementById('btnVerified').innerHTML = "Yes, " +
            verified + " it";
    }
</script>
<script type="text/javascript">
    var spinner = $('.loader');
    jQuery(function() {
        jQuery('.printpdf').click(function(e) {
            e.preventDefault();
            spinner.show();
            var searchString = $("#searchString").val();
            jQuery.ajax({
                type: 'GET',
                url: "{{ route('printastrologerlist') }}",
                data: {
                    "searchString": searchString,
                },
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(data) {
                    if (jQuery.isEmptyObject(data.error)) {
                        var blob = new Blob([data]);
                        var link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.download = "advisorList.pdf";
                        link.click();
                        spinner.hide();
                    } else {
                        spinner.hide();
                    }
                }
            });
        });
        jQuery('.downloadcsv').click(function(e) {
            e.preventDefault();
            spinner.show();
            var searchString = $("#searchString").val();
            jQuery.ajax({
                type: 'GET',
                url: "{{ route('exportAstrologerCSV') }}",
                data: {
                    "searchString": searchString,
                },
                xhrFields: {
                    responseType: 'blob'
                },
                success: function(data) {
                    if (jQuery.isEmptyObject(data.error)) {
                        var blob = new Blob([data]);
                        var link = document.createElement('a');
                        link.href = window.URL.createObjectURL(blob);
                        link.download = "advisorList.csv";
                        link.click();
                        spinner.hide();
                    } else {
                        spinner.hide();
                    }
                }
            });
        });
    });
</script>

<script>
    $(window).on('load', function() {
        $('.loader').hide();
    })
</script>
@endsection
