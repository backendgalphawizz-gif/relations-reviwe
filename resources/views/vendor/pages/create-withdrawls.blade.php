@extends('vendor.app')

@push('css_or_js')
    <title>Create Withdrawl Request</title>
@endpush

@section('content')
    <div class="loader"></div>
    <div class="grid grid-cols-11 gap-x-6">
        <div class="intro-y col-span-12 2xl:col-span-12">
            <div class="intro-y box">
                <div class="flex flex-col sm:flex-row items-center p-2 border-b border-slate-200/60 dark:border-darkmode-400">
                    <h2 class="font-medium text-base mr-auto">Create Withdrawl Request</h2>
                </div>
                <div>
                    <div id="form-validation" class="p-5">
                        <div class="preview">
                            <form id="add-testimonial-form" action="{{ route('advisor.submit-withdrawl-request') }}" method="post">
                                {{ csrf_field() }}
                                <input type="hidden" name="astrologer_id" value="{{ $astrologer->id }}">
                                <div class="input-form text-testimonial">
                                    <label for="desctipion" class="form-label w-full flex flex-col sm:flex-row">Amount<span class="text-danger">*</span></label>
                                    <input type="number" name="amount" class="form-control">
                                </div>
                                <div class="input-form">
                                    <label for="upi_id" class="form-label w-full flex flex-col sm:flex-row">Select Payment Method<span class="text-danger">*</span></label>
                                    <select name="payment_method" class="form-control">
                                        <option value="bank">Bank Account</option>
                                        <option value="upi">UPI ID</option>
                                    </select>
                                </div>
                                <div class="input-form upi d-none">
                                    <label for="upi_id" class="form-label w-full flex flex-col sm:flex-row">UPI ID<span class="text-danger">*</span></label>
                                    <input type="text" name="upi_id" id="upi_id" class="form-control">
                                </div>
                                <div class="input-form bank">
                                    <label for="account_number" class="form-label w-full flex flex-col sm:flex-row">Account Number<span class="text-danger">*</span></label>
                                    <input type="number" name="account_number" id="account_number" class="form-control">
                                </div>
                                <div class="input-form bank">
                                    <label for="ifsc_code" class="form-label w-full flex flex-col sm:flex-row">IFSC CODE<span class="text-danger">*</span></label>
                                    <input type="text" name="ifsc_code" id="ifsc_code" class="form-control">
                                </div>
                                <div class="input-form bank">
                                    <label for="account_holder_name" class="form-label w-full flex flex-col sm:flex-row">Account Holder Name<span class="text-danger">*</span></label>
                                    <input type="text" name="account_holder_name" id="account_holder_name" class="form-control">
                                </div>
                                <div class="mt-5"><button type="submit" class="btn btn-primary shadow-md mr-2 validate-form btn-submit">Submit</button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection

@push('script')
    <link href="//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $('.select2').select2()

        $(document).on('change', '[name=payment_method]', function() {
            let value = $(this).val()

            if(value == 'upi') {
                $('.upi').removeClass('d-none')
                $('.bank').addClass('d-none')
            } else {
                $('.upi').addClass('d-none')
                $('.bank').removeClass('d-none')
            }

        })

        $(document).on('submit','#add-testimonial-form', function(e) {
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
                            window.location.href = "{{ route('advisor.withdrawls') }}"
                        }, 1000)
                    } else {
                        toastr.error(response.message)

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