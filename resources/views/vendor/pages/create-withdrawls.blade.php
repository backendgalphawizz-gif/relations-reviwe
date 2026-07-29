@extends('vendor.app')

@push('css_or_js')
    <title>Create Withdrawal Request</title>
    <style>
        .withdraw-form-wrap {
            width: 100%;
            max-width: 100%;
        }
        .withdraw-form-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.08), 0 8px 24px rgba(15, 23, 42, 0.04);
            overflow: hidden;
        }
        .withdraw-form-card__header {
            background: linear-gradient(135deg, #426f7f 0%, #2f5563 100%);
            color: #fff;
            padding: 1.1rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }
        .withdraw-form-card__header h2 {
            margin: 0;
            font-size: 1.125rem;
            font-weight: 600;
        }
        .withdraw-form-card__body {
            padding: 1.5rem;
        }
        .withdraw-form-card .form-label {
            color: #334155;
            font-weight: 500;
            margin-bottom: 0.4rem;
            font-size: 0.875rem;
        }
        .withdraw-form-card .form-control {
            border-radius: 8px;
            border-color: #cbd5e1;
            min-height: 42px;
        }
        .withdraw-form-card .form-control:focus {
            border-color: #426f7f;
            box-shadow: 0 0 0 3px rgba(66, 111, 127, 0.15);
        }
        .withdraw-form-card .field-group {
            margin-bottom: 1.1rem;
        }
        .withdraw-form-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-top: 0.5rem;
            padding-top: 0.5rem;
        }
        .withdraw-form-actions .btn-primary {
            background-color: #426f7f !important;
            border-color: #426f7f !important;
            min-width: 120px;
        }
        .withdraw-form-actions .btn-primary:hover {
            background-color: #2f5563 !important;
            border-color: #2f5563 !important;
        }
        .withdraw-hint {
            color: #64748b;
            font-size: 0.8rem;
            margin-top: 0.35rem;
        }
    </style>
@endpush

@section('content')
    <div class="loader"></div>
    <div class="withdraw-form-wrap intro-y">
        <div class="withdraw-form-card">
            <div class="withdraw-form-card__header">
                <h2>Create Withdrawal Request</h2>
                <a href="{{ route('advisor.withdrawls') }}" class="btn btn-sm btn-outline-secondary"
                    style="background:#fff;color:#426f7f;border:none;">Back</a>
            </div>
            <div class="withdraw-form-card__body">
                <form id="add-testimonial-form" action="{{ route('advisor.submit-withdrawl-request') }}" method="post">
                    {{ csrf_field() }}
                    <input type="hidden" name="astrologer_id" value="{{ $astrologer->id }}">

                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-12 md:col-span-6 field-group">
                            <label for="amount" class="form-label">Amount <span class="text-danger">*</span></label>
                            <input type="number" name="amount" id="amount" class="form-control"
                                placeholder="Enter amount" min="1" step="0.01" required>
                            <div class="withdraw-hint">Enter the amount you want to withdraw</div>
                        </div>

                        <div class="col-span-12 md:col-span-6 field-group">
                            <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                            <select name="payment_method" id="payment_method" class="form-control" required>
                                <option value="bank">Bank Account</option>
                                <option value="upi">UPI ID</option>
                            </select>
                        </div>

                        <div class="col-span-12 field-group upi d-none">
                            <label for="upi_id" class="form-label">UPI ID <span class="text-danger">*</span></label>
                            <input type="text" name="upi_id" id="upi_id" class="form-control"
                                placeholder="example@upi">
                        </div>

                        <div class="col-span-12 md:col-span-6 field-group bank">
                            <label for="account_number" class="form-label">Account Number <span class="text-danger">*</span></label>
                            <input type="text" name="account_number" id="account_number" class="form-control"
                                placeholder="Enter account number" inputmode="numeric">
                        </div>

                        <div class="col-span-12 md:col-span-6 field-group bank">
                            <label for="ifsc_code" class="form-label">IFSC Code <span class="text-danger">*</span></label>
                            <input type="text" name="ifsc_code" id="ifsc_code" class="form-control"
                                placeholder="Enter IFSC code" style="text-transform: uppercase;">
                        </div>

                        <div class="col-span-12 field-group bank">
                            <label for="account_holder_name" class="form-label">Account Holder Name <span class="text-danger">*</span></label>
                            <input type="text" name="account_holder_name" id="account_holder_name" class="form-control"
                                placeholder="Enter account holder name">
                        </div>
                    </div>

                    <div class="withdraw-form-actions">
                        <button type="submit" class="btn btn-primary shadow-md validate-form btn-submit">Submit</button>
                        <a href="{{ route('advisor.withdrawls') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).on('change', '[name=payment_method]', function() {
            let value = $(this).val();

            if (value == 'upi') {
                $('.upi').removeClass('d-none');
                $('.bank').addClass('d-none');
            } else {
                $('.upi').addClass('d-none');
                $('.bank').removeClass('d-none');
            }
        });

        $(document).on('submit', '#add-testimonial-form', function(e) {
            e.preventDefault();

            let formData = new FormData(this);
            let $btn = $(this).find('.btn-submit');
            $btn.prop('disabled', true).text('Submitting...');

            jQuery.ajax({
                type: "POST",
                url: $(this).attr('action'),
                data: formData,
                dataType: "json",
                processData: false,
                contentType: false,
                success: function(response) {
                    $btn.prop('disabled', false).text('Submit');
                    if (response.status) {
                        toastr.success(response.message);
                        setTimeout(() => {
                            window.location.href = "{{ route('advisor.withdrawls') }}";
                        }, 1000);
                    } else {
                        toastr.error(response.message);
                        jQuery.each(response.error || {}, function(ind, elm) {
                            toastr.error(Array.isArray(elm) ? elm[0] : elm);
                        });
                    }
                },
                error: function() {
                    $btn.prop('disabled', false).text('Submit');
                    toastr.error('Something went wrong. Please try again.');
                }
            });
        });
    </script>
@endpush
