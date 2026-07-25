@extends('../layout/' . $layout)

@section('subhead')
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"  ></script>
    <title>Ads Enquiry</title>
@endsection

@section('subcontent')
    <div class="loader"></div>
    <h2 class="intro-y text-lg font-medium mt-10 d-inline">Ads Enquiry</h2>
    <div class="grid grid-cols-12 gap-6">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
        </div>
    </div>
    <form method="GET" action="{{ route('ads') }}" class="intro-y box p-4 mb-4">
    <div class="grid grid-cols-12 gap-4 items-end">

        <div class="col-span-12 sm:col-span-3">
            <label class="form-label">Name</label>
            <input type="text"
                   name="name"
                   value="{{ request('name') }}"
                   class="form-control"
                   placeholder="Business or Person Name">
        </div>

        <div class="col-span-12 sm:col-span-3">
            <label class="form-label">Mobile No</label>
            <input type="text"
                   name="mobile"
                   value="{{ request('mobile') }}"
                   class="form-control"
                   placeholder="Mobile Number">
        </div>

        <div class="col-span-12 sm:col-span-3">
            <label class="form-label">From Date</label>
            <input type="date"
                   name="from_date"
                   value="{{ request('from_date') }}"
                   class="form-control">
        </div>

        <div class="col-span-12 sm:col-span-3">
            <label class="form-label">To Date</label>
            <input type="date"
                   name="to_date"
                   value="{{ request('to_date') }}"
                   class="form-control">
        </div>

        <div class="col-span-12 sm:col-span-3 flex gap-2">
            <button type="submit" class="btn btn-primary w-full">
                Search
            </button>
            <a href="{{ route('ads') }}" class="btn btn-secondary w-full">
                Reset
            </a>
        </div>

    </div>
</form>

    <!-- BEGIN: Data List -->
    @if (count($testimonials) > 0)
        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible withoutsearch">
            <table class="table table-report" aria-label="testimonial">
                <thead class="sticky-top">
                    <tr>
                        <th class="whitespace-nowrap">ID</th>
                        <th class="whitespace-nowrap">Business/ Brand Name</th>
                        <th class="whitespace-nowrap">Person Name</th>
                        <th class="whitespace-nowrap">Email</th>
                        <th class="whitespace-nowrap">Mobile no.</th>
                        <th class="whitespace-nowrap">Campaign Duration</th>
                        <th class="whitespace-nowrap">Start Date</th>
                        <th class="whitespace-nowrap">URL</th>
                        <th class="whitespace-nowrap">Attachment</th>
                        <th class="text-center whitespace-nowrap">Status</th>
                        <th class="text-center whitespace-nowrap">Enquiry Date</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 0; @endphp
                    @foreach ($testimonials as $item)
                        <tr class="intro-x">
                            <td>{{ ($page - 1) * 15 + ++$no }}</td>
                            <td>
                                <div class="font-medium whitespace-nowrap">{{ $item['business_name'] }}</div>
                            </td>
                            <td>
                                <div class="font-medium whitespace-nowrap">{{ $item['contact_person_name'] }}</div>
                            </td>
                            <td>
                                <div class="font-medium whitespace-nowrap">{{ $item['email'] }}</div>
                            </td>
                            
                            <td>
                                <div class="font-medium whitespace-nowrap">{{ $item['mobile'] }}</div>
                            </td>
                            <td>
                                <div class="font-medium whitespace-nowrap">{{ $item['duration'] }}</div>
                            </td>
                            <td><div class="font-medium whitespace-nowrap">{{ $item['start_date'] }}</div></td>
                            <td><div class="font-medium whitespace-nowrap">{{ $item['link_url'] }}</div></td>
                            <td>
                                @if(!empty($item['file']))
                                    @php
                                        $ext = pathinfo($item['file'], PATHINFO_EXTENSION);
                                    @endphp

                                    @if(in_array(strtolower($ext), ['jpg','jpeg','png','gif','webp']))
                                        <a href="{{ asset($item['file']) }}" target="_blank">
                                            <img 
                                                src="{{ asset($item['file']) }}" 
                                                width="80"
                                                style="object-fit:cover;border-radius:6px;"
                                            >
                                        </a>
                                    @elseif($ext === 'pdf')
                                        <a href="{{ asset($item['file']) }}" target="_blank" class="text-blue-600 underline">
                                            View PDF
                                        </a>
                                    @else
                                        <span>Unsupported file</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">No File</span>
                                @endif
                            </td>

                            <td class="w-40">
                                <div
                                    class="form-check form-switch justify-center w-full sm:w-auto sm:ml-auto
                                 mt-3 sm:mt-0">
                                    <input class="toggle-class show-code form-check-input mr-0 ml-3" type="checkbox"
                                        href="javascript:;" data-tw-toggle="modal" data-onstyle="success"
                                        data-offstyle="danger" data-toggle="toggle" data-on="Active" data-off="InActive"
                                        {{ $item['status'] ? 'checked' : '' }}
                                        onclick="editTestimonial({{ $item['id'] }}, {{$item['status']}})"
                                        href="$item['id']" data-tw-target="#verified" id="switch">
                                </div>
                            </td>
                            <td>
                                <div class="font-medium whitespace-nowrap">{{ date('d M, Y h:i A', strtotime($item['created_at'])) }}</div>
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
                    <img src="{{ asset('/build/assets/images/nodata.png') }}" style="height:290px" alt="noData">
                    <h3 class="text-center">No Data Available</h3>
                </div>
            </div>
        </div>
    @endif
    <!-- END: Data List -->

    <!-- BEGIN: Pagination -->
    @if (count($testimonials) > 0)
        @if ($totalRecords > 0)
            <div>
                <div class="d-inline text-slate-500 pagecount">Showing {{ $start }} to {{ $end }} of
                    {{ $totalRecords }} entries</div>
        @endif
        <div class="d-inline addbtn intro-y col-span-12">
            <nav class="w-full sm:w-auto sm:mr-auto">
                <ul class="pagination">
                    <li class="page-item {{ $page == 1 ? 'disabled' : '' }}">
                        <a class="page-link"
                            href="{{ route('ads', array_merge(request()->query(), ['page' => $page - 1])) }}">

                        
                            <i class="w-4 h-4" data-lucide="chevron-left"></i>
                        </a>
                    </li>
                    @for ($i = 0; $i < $totalPages; $i++)
                        <li class="page-item {{ $page == $i + 1 ? 'active' : '' }}">
                            <a class="page-link"
                            href="{{ route('ads', array_merge(request()->query(), ['page' => $i + 1])) }}">
                                {{ $i + 1 }}
                            </a>
                        </li>
                    @endfor

                    <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                        <a class="page-link"
                            href="{{ route('ads', array_merge(request()->query(), ['page' => $page + 1])) }}">

                            <i class="w-4 h-4" data-lucide="chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        </div>
    @endif
    <!-- END: Pagination -->
    <!-- BEGIN: Delete Confirmation Modal -->
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

                    <form action="{{ route('deleteTestimonial') }} " method="POST">
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

    <div id="verified" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="p-5 text-center">
                        <div class="text-3xl mt-5">Are You Sure?</div>
                        <div class="text-slate-500 mt-2" id="active">You want Active!</div>
                    </div>
                    <form action="{{ route('adsEnquiryStatusApi') }}" method="POST" enctype="multipart/form-data" id="myForm">
                        @csrf
                        <input type="hidden" id="status" name="status">
                        <input type="hidden" id="status_id" name="status_id">
                        <div class="px-5 pb-8 text-center"><button class="btn btn-primary mr-3" id="btnActive">Yes,
                                Active it!
                            </button><a type="button" data-tw-dismiss="modal" class="btn btn-secondary w-24"
                                onclick="location.reload();">Cancel</a>
                        </div>
                </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        function editbtn($id, $title, $description, $user_name, $type, $user_image, $video_url) {
            var id = $id;
            $cid = id;

            $('#edit-modal').find('img').remove()
            $('#edit-modal').find('video').remove()

            $('#edit-modal').find('[name=id]').val($id)
            $('#edit-modal').find('[name=title]').val($title)
            $('#edit-modal').find('[name=user_name]').val($user_name)
            $('#edit-modal').find('[name=description]').val($description)
            $('#edit-modal').find('[name=type]').val($type)
            $('#edit-modal').find('[name=user_name]').parent().append(`<img  src="${$user_image}" width="100px">`)

            if($type == 0) {
                $('#edit-modal').find('.text-testimonial').show()
                $('#edit-modal').find('.video-testimonial').hide()
            } else {
                
                $('#edit-modal').find('.video-testimonial').append(`<video width="320" height="240" controls>
                        <source src="${$video_url}" type="video/mp4">
                    </video>`)

                $('#edit-modal').find('.text-testimonial').hide()
                $('#edit-modal').find('.video-testimonial').show()
            }

        }

        function resetButton() {
            var statusId = document.getElementById("status_id").value;
            var status = document.getElementById("status").value;
            if (status == false) {
                $('#switch').removeAttr("checked");
            } else {
                $('#switch').attr("checked", true);
            }
        }

        function editTestimonial($id, $isActive) {
            var id = $id;
            $fid = id;
            $('#status_id').val($fid);
            $('#status').val($isActive);
            var active = $isActive ? 'Inactive' : 'Active';
            document.getElementById('active').innerHTML = "You want to " + active;
            document.getElementById('btnActive').innerHTML = "Yes, " +
                active + " it";
        }

        function delbtn($id, $name) {
            var id = $id;
            $did = id;

            $('#del_id').val($did);
            $('#id').val($id);
        }

        function Validate(event) {
            var regex = new RegExp("^[0-9-!@#$%&<>*?]");
            var key = String.fromCharCode(event.charCode ? event.which : event.charCode);
            if (regex.test(key)) {
                event.preventDefault();
                return false;
            }
        }

        jQuery.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })

        // jQuery(".btn-submit").click(function(e) {

        //     e.preventDefault();

        //     var name = $("#name").val();

        //     jQuery.ajax({
        //         type: 'POST',
        //         url: "{{ route('addTestimonialApi') }}",
        //         data: {
        //             name: name
        //         },
        //         success: function(data) {
        //             if (jQuery.isEmptyObject(data.error)) {
        //                 toastr.options = {
        //                     "closeButton": true,
        //                     "progressBar": true
        //                 }
        //                 location.reload();
        //             } else {
        //                 printErrorMsg(data.error);
        //             }
        //         }
        //     });

        // });

        function printErrorMsg(msg) {
            jQuery(".print-name-error-msg").find("ul").html('');
            jQuery.each(msg, function(key, value) {
                if (key == 'name') {
                    jQuery(".print-name-error-msg").css('display', 'block');
                    jQuery(".print-name-error-msg").find("ul").append('<li>' + value + '</li>');
                }
            });
        }
    
        $(document).on('change', '.testimonial-type', function(e) {
            let val = $(this).val()
                console.log('val --------------- ', val)
            if(val == 0) {
                $('.text-testimonial').show()
                $('.video-testimonial').hide()
            } else {
                $('.text-testimonial').hide()
                $('.video-testimonial').show()
            }
        })

        $(document).on('submit', '#add-testimonial-form', function(e) {
            e.preventDefault()

            let formData = new FormData(this)

            jQuery.ajax({
                type: "POST",
                url: "{{ route('addTestimonialApi') }}",
                data: formData,
                dataType: "json",
                processData: false,
                contentType: false,
                success: function (response) {
                    if(response.status) {
                        toastr.success(response.message)
                        setTimeout(() => {
                            window.location.reload()
                        }, 1000);
                    }
                }
            });

        })
        $(document).on('submit', '#edit-testimonial-form', function(e) {
            e.preventDefault()
            let formData = new FormData(this)
            jQuery.ajax({
                type: "POST",
                url: "{{ route('editTestimonialApi') }}",
                data: formData,
                dataType: "json",
                processData: false,
                contentType: false,
                success: function (response) {
                    if(response.status) {
                        toastr.success(response.message)
                        setTimeout(() => {
                            window.location.reload()
                        }, 1000);
                    }
                }
            });

        })

    </script>
    <script>
        $(window).on('load', function() {
            $('.loader').hide();
        })
    </script>
@endsection
