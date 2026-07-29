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
                        <th class="whitespace-nowrap">IMAGE</th>
                        <th class="whitespace-nowrap">TITLE</th>
                        <th class="whitespace-nowrap">DESCRIPTION</th>
                        <th class="whitespace-nowrap">SEND TO</th>
                        <th class="text-center whitespace-nowrap">ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @php $no = 0; @endphp
                    @foreach ($notifications as $notification)
                        <tr class="intro-x">
                            <td>{{ ($page - 1) * 15 + ++$no }}</td>
                            <td>
                                @if (!empty($notification->image))
                                    <img src="{{ asset($notification->image) }}" alt="notification"
                                        style="width:48px;height:48px;object-fit:cover;border-radius:6px;">
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                            <td>{{ $notification->title }}</td>
                            <td>{!! $notification->description !!}</td>
                            <td>
                                <div class="font-medium whitespace-nowrap">{{ $notification->sendToLabel() }}</div>
                                @if (in_array($notification->send_to, ['single_customer', 'single_advisor'], true))
                                    @php
                                        $ids = $notification->sendToUserIds();
                                        $names = collect($ids)->map(fn ($id) => $userNamesById[$id] ?? ('#'.$id))->filter()->implode(', ');
                                    @endphp
                                    @if ($names)
                                        <div class="text-slate-500 text-xs mt-1">{{ $names }}</div>
                                    @endif
                                @endif
                            </td>
                            <td class="table-report__action w-56">
                                <div class="flex justify-center items-center">
                                    <a href="javascript:;" data-tw-toggle="modal" style="cursor: pointer"
                                        data-tw-target="#edit-modal"
                                        onclick="editbtn({{ $notification->id }}, @js($notification->title), @js($notification->description), @js($notification->image))"
                                        class="flex items-center mr-3"><i data-lucide="check-square"
                                            class="w-4 h-4"></i>Edit</a>
                                    <a href="javascript:;"
                                        onclick="send({{ $notification->id }});"
                                        class="flex items-center"
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
                    <form action="{{ route('addNotificationApi') }}" method="POST" enctype="multipart/form-data"
                        id="add-notification-form">
                        @csrf
                        <div class="p-5">
                            <div class="mt-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" name="title" id="title" class="form-control"
                                    placeholder="Title" required>
                            </div>
                            <div class="mt-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                            </div>
                            <div class="mt-3">
                                <label for="image" class="form-label">Image (optional)</label>
                                <input type="file" name="image" id="image" class="form-control"
                                    accept="image/*">
                            </div>

                            <hr class="my-4">
                            <h3 class="font-medium mb-2">Who should receive this?</h3>

                            <div class="mt-3">
                                <label class="form-label">Send To</label>
                                <select class="form-control" id="add_send_to" name="send_to" onchange="toggleAddSendTo()">
                                    <option value="all" selected>All (Customers + Advisors)</option>
                                    <option value="all_customers">All Customers</option>
                                    <option value="single_customer">Single / Select Customer</option>
                                    <option value="all_advisors">All Advisors</option>
                                    <option value="single_advisor">Single / Select Advisor</option>
                                </select>
                            </div>

                            <div class="mt-3" id="add-customer-wrap" style="display:none;">
                                <label class="form-label">Select Customer</label>
                                <select name="userIds[]" class="form-control" id="add_customers">
                                    <option value="">-- Select Customer --</option>
                                </select>
                            </div>

                            <div class="mt-3" id="add-advisor-wrap" style="display:none;">
                                <label class="form-label">Select Advisor</label>
                                <select name="userIds[]" class="form-control" id="add_advisors">
                                    <option value="">-- Select Advisor --</option>
                                </select>
                            </div>

                            <div class="mt-3 form-check">
                                <input type="checkbox" class="form-check-input" id="send_now" name="send_now" value="1" checked>
                                <label class="form-check-label" for="send_now">
                                    Send now after saving (uncheck to only save template, then use Send later)
                                </label>
                            </div>

                            <div class="mt-5">
                                <button type="submit" class="btn btn-primary shadow-md mr-2">Save &amp; Send</button>
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
                <form action="{{ route('editNotificationApi') }}" method="POST" enctype="multipart/form-data"
                    id="edit-notification-form">
                    @csrf
                    <div class="p-5">
                        <input type="hidden" id="filed_id" name="filed_id">
                        <div class="mt-3">
                            <label for="id" class="form-label">Title</label>
                            <input type="text" name="title" id="id" class="form-control"
                                placeholder="Title" required>
                        </div>
                        <div class="mt-3">
                            <label for="did" class="form-label">Description</label>
                            <textarea name="did" id="did" class="form-control" rows="4" required></textarea>
                        </div>
                        <div class="mt-3">
                            <label class="form-label">Current Image</label>
                            <div id="edit-image-preview" class="mb-2"></div>
                            <label for="edit_image" class="form-label">Change Image (optional)</label>
                            <input type="file" name="image" id="edit_image" class="form-control"
                                accept="image/*">
                        </div>
                        <div class="mt-5">
                            <button class="btn btn-primary shadow-md mr-2" id="btn">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="send-notification-modal" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" style="width:760px">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="font-medium text-base mr-auto">Send Notification</h2>
                    <button type="button" class="btn btn-danger" data-tw-dismiss="modal">Close</button>
                </div>
                <form method="POST" enctype="multipart/form-data" id="notification-form">
                    @csrf
                    <div class="p-5">
                        <input type="hidden" id="notification_id" name="notification_id">

                        <div class="mt-3">
                            <label class="form-label">Send To</label>
                            <select class="form-control" id="send_to" name="send_to" onchange="toggleSendTo()">
                                <option value="all" selected>All (Customers + Advisors)</option>
                                <option value="all_customers">All Customers</option>
                                <option value="single_customer">Single / Select Customer</option>
                                <option value="all_advisors">All Advisors</option>
                                <option value="single_advisor">Single / Select Advisor</option>
                            </select>
                        </div>

                        <div class="mt-3" id="send-customer-wrap" style="display:none;">
                            <label class="form-label">Select Customer</label>
                            <select name="userIds[]" class="form-control" id="send_customers">
                                <option value="">-- Select Customer --</option>
                            </select>
                        </div>

                        <div class="mt-3" id="send-advisor-wrap" style="display:none;">
                            <label class="form-label">Select Advisor</label>
                            <select name="userIds[]" class="form-control" id="send_advisors">
                                <option value="">-- Select Advisor --</option>
                            </select>
                        </div>

                        <div class="mt-5">
                            <button type="submit" class="btn-submit btn btn-primary shadow-md mr-2">Send
                                Notification</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        var allUsers = {{ Js::from($users ?? []) }};
        if (!Array.isArray(allUsers)) {
            allUsers = Object.keys(allUsers || {}).map(function(k) { return allUsers[k]; });
        }
        var spinner = jQuery('.loader');

        function fillRoleSelect(selectEl, roleId, placeholder) {
            var $select = jQuery(selectEl);
            $select.empty();
            $select.append('<option value="">' + placeholder + '</option>');
            (allUsers || []).forEach(function(option) {
                if (String(option.roleId) !== String(roleId)) return;
                var label = (option.name ? option.name : 'User') + ' - ' + (option.contactNo || '');
                $select.append('<option value="' + option.id + '">' + label + '</option>');
            });
        }

        function toggleAddSendTo() {
            var mode = document.getElementById('add_send_to').value;
            var customerWrap = document.getElementById('add-customer-wrap');
            var advisorWrap = document.getElementById('add-advisor-wrap');
            customerWrap.style.display = 'none';
            advisorWrap.style.display = 'none';
            document.getElementById('add_customers').value = '';
            document.getElementById('add_advisors').value = '';

            // Disable unused select so empty value is not submitted
            document.getElementById('add_customers').disabled = true;
            document.getElementById('add_advisors').disabled = true;

            if (mode === 'single_customer') {
                fillRoleSelect('#add_customers', 3, '-- Select Customer --');
                document.getElementById('add_customers').disabled = false;
                customerWrap.style.display = 'block';
            } else if (mode === 'single_advisor') {
                fillRoleSelect('#add_advisors', 2, '-- Select Advisor --');
                document.getElementById('add_advisors').disabled = false;
                advisorWrap.style.display = 'block';
            }
        }

        function toggleSendTo() {
            var mode = document.getElementById('send_to').value;
            var customerWrap = document.getElementById('send-customer-wrap');
            var advisorWrap = document.getElementById('send-advisor-wrap');
            customerWrap.style.display = 'none';
            advisorWrap.style.display = 'none';
            document.getElementById('send_customers').value = '';
            document.getElementById('send_advisors').value = '';
            document.getElementById('send_customers').disabled = true;
            document.getElementById('send_advisors').disabled = true;

            if (mode === 'single_customer') {
                fillRoleSelect('#send_customers', 3, '-- Select Customer --');
                document.getElementById('send_customers').disabled = false;
                customerWrap.style.display = 'block';
            } else if (mode === 'single_advisor') {
                fillRoleSelect('#send_advisors', 2, '-- Select Advisor --');
                document.getElementById('send_advisors').disabled = false;
                advisorWrap.style.display = 'block';
            }
        }

        function editbtn(id, title, description, image) {
            jQuery('#filed_id').val(id);
            jQuery('#id').val(title);
            jQuery('#did').val(description);
            if (image) {
                jQuery('#edit-image-preview').html('<img src="/' + image.replace(/^\/+/, '') +
                    '" style="width:80px;height:80px;object-fit:cover;border-radius:6px;">');
            } else {
                jQuery('#edit-image-preview').html('<span class="text-slate-400">No image</span>');
            }
        }

        function send(id) {
            jQuery('#notification_id').val(id);
            jQuery('#send_to').val('all');
            toggleSendTo();
        }

        jQuery(function() {
            toggleAddSendTo();
            toggleSendTo();

            jQuery('#notification-form').submit(function(e) {
                e.preventDefault();

                var mode = jQuery('#send_to').val();
                if (mode === 'single_customer' && !jQuery('#send_customers').val()) {
                    toastr.error('Please select a customer');
                    return;
                }
                if (mode === 'single_advisor' && !jQuery('#send_advisors').val()) {
                    toastr.error('Please select an advisor');
                    return;
                }

                spinner.show();
                var formData = new FormData(this);
                if (mode === 'all' || mode === 'all_customers' || mode === 'all_advisors') {
                    formData.delete('userIds[]');
                }

                jQuery.ajax({
                    type: 'POST',
                    url: "{{ route('sendNotification') }}",
                    data: formData,
                    dataType: 'JSON',
                    processData: false,
                    contentType: false,
                    timeout: 120000,
                    success: function(data) {
                        if (jQuery.isEmptyObject(data.error)) {
                            toastr.success((data.success && data.success[0]) ? data.success[0] :
                                'Notification sent successfully');
                            setTimeout(function() {
                                location.reload();
                            }, 500);
                        } else {
                            spinner.hide();
                            printErrorMsg(data.error);
                        }
                    },
                    error: function(xhr, status) {
                        spinner.hide();
                        toastr.error(status === 'timeout'
                            ? 'Sending is taking too long. Please try again or send to a smaller group.'
                            : 'Failed to send notification');
                    },
                    complete: function() {
                        // keep loader only while reloading after success
                    }
                });
            });

            jQuery('#add-notification-form').submit(function(e) {
                e.preventDefault();

                var mode = jQuery('#add_send_to').val();
                if (mode === 'single_customer' && !jQuery('#add_customers').val()) {
                    toastr.error('Please select a customer');
                    return;
                }
                if (mode === 'single_advisor' && !jQuery('#add_advisors').val()) {
                    toastr.error('Please select an advisor');
                    return;
                }

                spinner.show();
                var formData = new FormData(this);
                if (mode === 'all' || mode === 'all_customers' || mode === 'all_advisors') {
                    formData.delete('userIds[]');
                }
                if (!jQuery('#send_now').is(':checked')) {
                    formData.delete('send_now');
                }

                jQuery.ajax({
                    type: 'POST',
                    url: "{{ route('addNotificationApi') }}",
                    data: formData,
                    dataType: 'JSON',
                    processData: false,
                    contentType: false,
                    timeout: 120000,
                    success: function(data) {
                        if (jQuery.isEmptyObject(data.error) || data.status) {
                            toastr.success(data.message || 'Notification added successfully');
                            if (data.error) {
                                spinner.hide();
                                printErrorMsg(data.error);
                                return;
                            }
                            setTimeout(function() {
                                location.reload();
                            }, 500);
                        } else {
                            spinner.hide();
                            printErrorMsg(data.error);
                        }
                    },
                    error: function(xhr, status) {
                        spinner.hide();
                        toastr.error(status === 'timeout'
                            ? 'Sending is taking too long. Please try again or send to a smaller group.'
                            : 'Failed to add notification');
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
                        spinner.hide();
                        if (jQuery.isEmptyObject(data.error)) {
                            toastr.success('Notification updated successfully');
                            location.reload();
                        } else {
                            printErrorMsg(data.error);
                        }
                    },
                    error: function() {
                        spinner.hide();
                        toastr.error('Failed to update notification');
                    }
                });
            });
        });

        function printErrorMsg(msg) {
            jQuery.each(msg, function(key, value) {
                if (Array.isArray(value)) {
                    toastr.error(value[0]);
                } else {
                    toastr.error(value);
                }
            });
        }

        jQuery(window).on('load', function() {
            jQuery('.loader').hide();
        });
    </script>
@endsection
