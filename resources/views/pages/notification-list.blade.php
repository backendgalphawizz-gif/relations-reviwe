@extends('../layout/' . $layout)

@section('subhead')
    <title>Notifications</title>
@endsection

@section('subcontent')
    <div class="loader"></div>
    <h2 class="intro-y text-lg font-medium mt-10 d-inline">Notifications</h2>
    <a href="javascript:;" data-tw-toggle="modal" data-tw-target="#add-notification"
        class="mt-10 addbtn d-inline btn btn-primary shadow-md mr-2">Add
        Notification</a>
    <div class="grid grid-cols-12 gap-6">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap items-center mt-2">
            <div class="w-full sm:w-auto mt-3 sm:mt-0 sm:ml-auto md:ml-0">
            </div>
        </div>
    </div>
    <!-- BEGIN: Data List -->
    @if ($totalRecords > 0)
        <div class="intro-y col-span-12 overflow-auto withoutsearch">
            <table class="table table-report -mt-2" aria-label="notification">
                <thead class="sticky-top">
                    <tr>
                        <th class="whitespace-nowrap">#</th>
                        <th class="whitespace-nowrap">TITLE</th>
                        <th class="whitespace-nowrap">DESCRIPTION</th>
                        <th class="text-center whitespace-nowrap">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $no = 0;
                    @endphp
                    @foreach ($notifications as $notification)
                        <tr class="intro-x">
                            <td>{{ ($page - 1) * 15 + ++$no }}</td>
                            <td>{{ $notification['title'] }}</td>
                            <td>{!! $notification['description'] !!}</td>

                            <td class="table-report__action w-56">
                                <div class="flex justify-center items-center">
                                    <a id="editbtn" data-tw-toggle="modal" style="cursor: pointer"
                                        data-tw-target="#edit-modal"
                                        onclick="editbtn({{ $notification['id'] }},'{{ $notification['title'] }}', '{{ $notification['description'] }}')"
                                       class="flex items-center mr-3 "><i data-lucide="check-square"
                                            class="w-4 h-4"></i>Edit</a>

                                    <a id="editbtn" href="javascript:;"
                                        onclick="send({{ $notification['id'] }});document.getElementById('notification-form').reset();"
                                        value="{{ $notification['id'] }}" class="flex items-center"
                                        data-tw-target="#send-notification-modal" data-tw-toggle="modal"><i
                                            data-lucide="share-2" class="editbtn w-4 h-4 mr-1"></i>Send</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if ($totalRecords > 0)
            <div class="d-inline text-slate-500 pagecount">Showing {{ $start }} to {{ $end }} of
                {{ $totalRecords }} entries</div>
        @endif
        <div class="d-inline addbtn intro-y col-span-12">
            <nav class="w-full sm:w-auto sm:mr-auto">
                <ul class="pagination">
                    <li class="page-item {{ $page == 1 ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ route('notifications', ['page' => $page - 1]) }}">
                            <i class="w-4 h-4" data-lucide="chevron-left"></i>
                        </a>
                    </li>
                    @for ($i = 0; $i < $totalPages; $i++)
                        <li class="page-item {{ $page == $i + 1 ? 'active' : '' }} ">
                            <a class="page-link"
                                href="{{ route('notifications', ['page' => $i + 1]) }}">{{ $i + 1 }}</a>
                        </li>
                    @endfor
                    <li class="page-item {{ $page == $totalPages ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ route('notifications', ['page' => $page + 1]) }}">
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
    <!-- END: Data List -->
    <div id="add-notification" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" style="width:760px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Add Notification</h2>
                    <button type="button" class="btn btn-danger" data-tw-dismiss="modal">Close</button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('addNotificationApi') }}" method="POST" enctype="multipart/form-data" id="add-notification-form">
                        @csrf
                        <div id="input" class="p-5">
                            <div class="preview">
                                <div class="mt-3">
                                    <div class="sm:grid grid-cols gap-2">
                                        <div class="input">
                                            <div>
                                                <label for="title" class="form-label">Title</label>
                                                <input type="text" name="title" id="title" class="form-control"
                                                    placeholder="Title" required onkeypress="return Validate(event);">
                                            </div>
                                        </div>
                                        <div class="input" id="classic-editor">
                                            <label for="description" class="from-label">Description</label>
                                            <textarea onkeypress="return validateJavascript(event);" class="form-control" id="description" name="description" required></textarea>
                                        </div>
                                        <div class="mt-5"><button type="submit" class="btn btn-primary shadow-md mr-2">Add
                                                Notification</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="edit-modal" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" style="width:760px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Edit Notification</h2>
                    <button type="button" class="btn btn-danger" data-tw-dismiss="modal">Close</button>
                </div>
                <form action="{{ route('editNotificationApi') }}" method="POST" enctype="multipart/form-data" id="edit-notification-form">
                    @csrf
                    <div id="input" class="p-5">
                        <div class="preview">
                            <div class="mt-3">
                                <div class="sm:grid grid-cols gap-2">
                                    <div class="input">
                                        <div>
                                            <input type="hidden" id="filed_id" name="filed_id">
                                            <label for="title" class="form-label">Title</label>
                                            <input type="text" name="title" id="id" class="form-control"
                                                placeholder="Title" required onkeypress="return Validate(event);">
                                        </div>
                                    </div>
                                    <div class="input">
                                        <div>
                                            <label for="description" class="form-label">Description</label>
                                            <textarea onkeypress="return validateJavascript(event);" type="text" name="did" id="did" class="form-control" required></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-5 "><button class="btn btn-primary shadow-md mr-2"
                                    id="btn">Save</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- BEGIN: Delete Confirmation Modal -->
    <div id="delete-confirmation-modal" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="p-5 text-center">
                        <i data-lucide="x-circle" class="w-16 h-16  mx-auto mt-3"></i>

                        <div class="text-3xl mt-5">Are you sure?</div>
                        <div class="text-slate-500 mt-2">Do you really want to send these notification? </div>
                    </div>

                    <form action="{{ route('sendNotification') }} " method="POST">
                        @csrf
                        @method('Post')
                        <input type="hidden" id="notification" name="notification_id">
                        <div class="px-5 pb-8 text-center">
                            <button type="button" data-tw-dismiss="modal"
                                class="btn btn-outline-secondary w-24 mr-1">Cancel</button>
                            <button class="btn btn-primary w-24">@method('Post')Send</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="send-notification-modal" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" style="width:760px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Send Notification</h2>
                </div>
                <form method="POST" enctype="multipart/form-data" id="notification-form">
                    @csrf
                    <div id="input" class="p-5">
                        <div class="preview">
                            <div class="mt-3">
                                <div class="sm:grid grid-cols gap-2">
                                    <div class="preview">
                                        <input type="hidden" id="roleIndex" name="role">
                                        <label for="title" class="form-label">Select Role</label>
                                        <select class="form-control" id="role" name="role" value="role">
                                            <option disabled>--Select Role--</option>
                                            <option id="userrole"selected>All</option>
                                            <option id="userrole">User</option>
                                            <option id="userrole">Adviser</option>
                                        </select>
                                    </div>
                                    <div class="preview">
                                        <input type="hidden" id="notification_id" name="notification_id">
                                        <label for="title" class="form-label">Select User</label>
                                        <select name="userIds[]" class="form-control select2" multiple
                                            data-placeholder="Select User..." id="users">
                                            
                                        </select>
                                    </div>


                                </div>
                            </div>
                            <div class="mt-5"><button type="submit"
                                    class="btn-submit btn btn-primary shadow-md mr-2">Send
                                    Notification</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- END: Delete Confirmation Modal -->
@endsection
@section('script')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"  ></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"  ></script>
    <script type="text/javascript">
        var $select = $('#users');
        var users = {{ Js::from($users) }};
        jQuery.each(users, function(index, option) {
            $select.append('<option value="' + option.id + '">' + (option.name ? option.name : '') + '-' + option
                .contactNo +
                '</option>');
        });

        var spinner = $('.loader');

        function editbtn($id, $title, $description) {
            var id = $id;
            var did = $id;
            $cid = id;

            $('#filed_id').val($cid);
            $('#id').val($title);
            $('#did').val($description);

        }

        function Validate(event) {
            var regex = new RegExp("^[0-9-!@#$%&<>*?]");
            var key = String.fromCharCode(event.charCode ? event.which : event.charCode);
            if (regex.test(key)) {
                event.preventDefault();
                return false;
            }
        }

        function validateJavascript(event) {
            var regex = new RegExp("^[<>]");
            var key = String.fromCharCode(event.charCode ? event.which : event.charCode);
            if (regex.test(key)) {
                event.preventDefault();
                return false;
            }
        }

        function editNotification($id, $name) {
            var id = $id;
            $fid = id;

            $('#status_id').val($fid);
            $('#id').val($name);
        }

        function send($id) {
            $fid = $id;
            $('#notification_id').val($fid);
            $('#notification_id').val($fid);
        }

        $('#role').on('change', function() {
            var roleUsers = [];
            var select = $('#users');
            $role = document.getElementById('role').value;
            var users = {{ Js::from($users) }};
            if ($role == 'User') {
                roleUsers = users.filter(c => c.roleId == 3)
            } else if ($role == 'Astrologer') {
                roleUsers = users.filter(c => c.roleId == 2)
            } else {
                roleUsers = users;
            }
           jQuery('#users').empty();
            jQuery.each(roleUsers, function(index, option) {
                select.append('<option value="' + option.id + '">' + (option.name ? option.name : '') +
                    '-' + option.contactNo +
                    '</option>');
            });
        });

        jQuery('#notification-form').submit(function(e) {
            e.preventDefault();
            spinner.show();
            jQuery.ajax({
                type: 'POST',
                url: "{{ route('sendNotification') }}",
                data: new FormData(this),
                dataType: 'JSON',
                processData: false,
                contentType: false,
                success: function(data) {
                    if (jQuery.isEmptyObject(data.error)) {
                        spinner.hide();
                        location.reload();
                    } else {
                        printErrorMsg(data.error)
                        spinner.hide();
                    }
                }
            });
        });
        jQuery('#add-notification-form').submit(function(e) {
            e.preventDefault();
            spinner.show();
            jQuery.ajax({
                type: 'POST',
                url: "{{ route('addNotificationApi') }}",
                data: new FormData(this),
                dataType: 'JSON',
                processData: false,
                contentType: false,
                success: function(data) {
                    if (jQuery.isEmptyObject(data.error)) {

                        toastr.success('Notification added successfully')

                        spinner.hide();
                        location.reload();
                    } else {
                        spinner.hide();
                        printErrorMsg(data.error)
                    }
                }
            });
        });
        jQuery('#edit-notification-form').submit(function(e) {
            e.preventDefault();
            spinner.show();
            jQuery.ajax({
                type: 'POST',
                url: "{{ route('editNotificationApi') }}",
                data: new FormData(this),
                dataType: 'JSON',
                processData: false,
                contentType: false,
                success: function(data) {
                    if (jQuery.isEmptyObject(data.error)) {
                        toastr.success('Notification updated successfully')
                        spinner.hide();
                        location.reload();
                    } else {
                        spinner.hide();
                        printErrorMsg(data.error)
                    }
                }
            });
        });

        function printErrorMsg(msg) {
            jQuery(".print-name-error-msg").find("ul").html('');
            jQuery.each(msg, function(key, value) {
                toastr.error(value)
                // if (key == 'name') {
                //     jQuery(".print-name-error-msg").css('display', 'block');
                //     jQuery(".print-name-error-msg").find("ul").append('<li>' + value + '</li>');
                // }
            });
        }

        function filterData() {
            user_filter = $('#user_filter').val();
            $ajax({
                url: "{{ url('filter_user') }}?filter_user=" + user_filter,
                success: function(data) {

                }
            })
        }

        function searchData() {
            $("#user_filter").select2({
                placeholder: "Select a Name",
                allowClear: true
            });
        }

        function showEditor() {

        }
    </script>
    <script>
        $(document).ready(function() {
            jQuery('.select2').select2();
        });
    </script>
    <script>
        $(window).on('load', function() {
            $('.loader').hide();
        })
    </script>
@endsection
