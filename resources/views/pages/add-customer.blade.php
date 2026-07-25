@extends('../layout/' . $layout)

@section('subhead')
    <title>Add Customer</title>
@endsection

@section('subcontent')
    <div class="loader"></div>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 mt-2">

            <div class="intro-y box">
                <div
                    class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                    <h2 class="font-medium text-base mr-auto">Add Customer</h2>
                </div>
                <form method="POST" enctype="multipart/form-data" id="add-form">
                    @csrf
                    <div id="input" class="p-5">
                        <div class="preview">
                            <div class="mt-3">
                                <div class="sm:grid grid-cols-3 gap-2">
                                    <div class="input">
                                        <div>
                                            <label for="regular-form-1" class="form-label">Name</label>
                                            <input id="name" name="name" type="text" class="form-control inputs"
                                                placeholder="Customer Name" onkeypress="return Validate(event);" >
                                            <div class="text-danger print-name-error-msg mb-2" style="display:none">
                                                <ul></ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="input mt-2 sm:mt-0">
                                        <div>
                                            <label for="regular-form-1" class="form-label">Contact No</label>
                                            <input id="contactNo" name="contactNo" type="text" class="form-control inputs" placeholder="Contact Number" onKeyDown="numbersOnly(event)" maxlength="10">
                                            <div class="text-danger print-number-error-msg mb-2" style="display:none">
                                                <ul></ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="input mt-2 sm:mt-0">
                                        <div>
                                            <label for="regular-form-1" class="form-label">DOB</label>
                                            <input id="birthDate" name="birthDate" type="date" class="form-control inputs" placeholder="DOB"  max="{{ date('Y-m-d', strtotime('-18 years')) }}">
                                        </div>
                                    </div>
                                    <div class="input mt-2 sm:mt-0">
                                        <div>
                                            <label for="validation-form-2"
                                                class="form-label w-full flex flex-col sm:flex-row">
                                                Email
                                            </label>
                                            <input id="email" type="email" name="email" onkeypress="return validateJavascript(event);" class="form-control inputs"
                                                placeholder="example@gmail.com" >
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- <div class="mt-3">
                                <div class="sm:grid grid-cols-3 gap-2">
                                    <div class="input">
                                        <div>
                                            <label id="input-group" class="form-label">Birth Date</label>
                                            <input type="date" id="birthDate" name="birthDate"
                                                class="form-control inputs" placeholder="Unit"
                                                aria-describedby="input-group-3" >
                                        </div>
                                    </div>

                                    <div class="input mt-2 sm:mt-0">
                                        <label id="input-group" class="form-label">Birth Time</label>
                                        <input type="time" id="birthTime" name="birthTime" class="form-control inputs"
                                            placeholder="Wholesale" aria-describedby="input-group-4" >

                                    </div>
                                    <div class="input mt-2 sm:mt-0">
                                        <label id="input-group" class="form-label">Birth Place</label>
                                        <input type="text" class="form-control inputs" id="birthPlace" name="birthPlace"
                                            placeholder="Birth Place" aria-describedby="input-group-5"
                                            onkeypress="return Validate(event);" >
                                    </div>
                                </div>
                            </div> --}}

                            <div class="mt-3">
                                <div>
                                    <div class="input">
                                        <div>
                                            <label for="validation-form-6"
                                                class="form-label w-full flex flex-col sm:flex-row">
                                                Current Address
                                            </label>
                                            <textarea id="addressLine1" class="form-control inputs" name="addressLine1" placeholder="Current Address" onkeypress="return validateJavascript(event);" minlength="10"
                                                ></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3">
                                <div class="sm:grid grid-cols-3 gap-2">
                                    <div class="input mt-2 sm:mt-0">
                                        <label id="input-group" class="form-label">City</label>
                                        <input type="text" id="addressLine2" name="location" class="form-control"
                                            placeholder="City" aria-describedby="input-group-4"
                                            onkeypress="return Validate(event);" >
                                    </div>
                                    <div class="input mt-2 sm:mt-0">
                                        <label id="input-group" class="form-label">Pin Code</label>
                                        <input type="text" id="pincode" name="pincode" class="form-control"
                                            placeholder="Pin Code" aria-describedby="input-group-5"
                                            onKeyDown="numbersOnly(event)" maxlength="6">
                                    </div>
                                    <div class="input mt-2 sm:mt-0">
                                        <label id="gender" class="form-label">Select Gender</label>
                                        <select class="form-control" id="gender" name="gender" value="gender"
                                            >
                                            <option disabled selected>--Select Gender--</option>
                                            <option id="gender">Female</option>
                                            <option id="gender">Male</option>
                                        </select>
                                        <div class="text-danger print-gender-error-msg mb-2" style="display:none">
                                            <ul></ul>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-12 gap-6">
                                        <div class="intro-y col-span-12">
                                            <div>
                                                <label for="profile" class="form-label">Profile Image</label>
                                                <img id="thumb" width="150px" alt="profileImage"
                                                    style="display:none" />
                                                <input type="file" class="mt-2" name="profile" id="profile"
                                                    onchange="preview()" accept="image/*">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-5"><button type="submit" class="btn btn-primary shadow-md mr-2">Add
                                    Customer</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>


        </div>
    </div>
@endsection


@section('script')
    <script type="text/javascript">
        var spinner = $('.loader');

        function preview() {
            document.getElementById("thumb").style.display = "block";
            thumb.src = URL.createObjectURL(event.target.files[0]);
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

        function numbersOnly(e) {
            var keycode = e.keyCode;
            let contact = document.getElementById("contactNo").value;
            if ((keycode < 48 || keycode > 57) && (keycode < 96 || keycode > 105) && keycode !=
                9 && keycode != 8 && keycode != 37 && keycode != 38 && keycode != 39 && keycode != 40 && keycode != 46) {
                e.preventDefault();
            }
        }


        function checkPincode(e) {
            let pincode = document.getElementById("pincode").value;
            var keycode = e.keyCode;
            if (pincode.length >= 6 && (keycode < 48 || keycode > 57) && keycode != 9 && keycode != 8) {
                e.preventDefault();
            }
        }

        jQuery(function() {
            jQuery('#add-form').submit(function(e) {
                e.preventDefault();
                spinner.show();
                var data = new FormData(this);
                jQuery.ajax({
                    type: 'POST',
                    url: "{{ route('addUserApi') }}",
                    data: data,
                    dataType: 'JSON',
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        if (jQuery.isEmptyObject(data.error)) {
                            spinner.hide();
                            location.href = "/admin/customers";
                        } else {
                            printErrorMsg(data.error);
                            spinner.hide();
                        }
                    }
                });
            });
        });

        function printErrorMsg(msg) {
            jQuery(".print-name-error-msg").find("ul").html('');
            jQuery(".print-number-error-msg").find("ul").html('');
            jQuery(".print-gender-error-msg").find("ul").html('');
            jQuery.each(msg, function(key, value) {

                toastr.error(value)

                // if (key == 'name') {
                //     jQuery(".print-name-error-msg").css('display', 'block');
                //     jQuery(".print-name-error-msg").find("ul").append('<li>' + value + '</li>');
                // }
                // if (key == 'contactNo') {
                //     jQuery(".print-number-error-msg").css('display', 'block');
                //     jQuery(".print-number-error-msg").find("ul").append('<li>' + value + '</li>');
                // }
                // if (key == 'gender') {
                //     jQuery(".print-gender-error-msg").css('display', 'block');
                //     jQuery(".print-gender-error-msg").find("ul").append('<li>' + value + '</li>');
                // }
            });
        }
    </script>
    <script>
        $(window).on('load', function() {
            $('.loader').hide();
        })
    </script>
@endsection
