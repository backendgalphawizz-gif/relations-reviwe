@extends('vendor.app')

@push('css_or_js')
    <title>Advisor Status</title>
@endpush

@section('content')
    <div class="loader"></div>
    <div class="grid grid-cols-11 gap-x-6">

        <div class="intro-y col-span-12 2xl:col-span-12">

            <div class="intro-y box">

                <div

                    class="flex flex-col sm:flex-row items-center p-2 border-b border-slate-200/60 dark:border-darkmode-400">

                    <h2 class="font-medium text-base mr-auto">Advisor Status</h2>

                </div>

                <form action="{{ route('advisor.update-call-status') }}" method="POST" enctype="multipart/form-data" id="edit-profile">

                    @csrf

                    <div id="input" class="p-2">
                        <div class="preview">
                            <div class="mt-3">
                                <div class="sm:grid grid-cols-4 gap-2">
                                    <input type="hidden" name="id" value="{{ $astrologer->id }}">
                                    <div class="input">
                                        <div>
                                            <label id="callStatus" class="form-label">Call Status</label>
                                            <select name="callStatus" class="form-control select2" >
                                                <option value="Wait Time" {{ strtolower($astrologer->callStatus) == 'wait time' ? 'selected' : '' }}>Wait Time</option>
                                                <option value="Online" {{ strtolower($astrologer->callStatus) == 'online' ? 'selected' : '' }}>Online</option>
                                                <option value="Offline" {{ strtolower($astrologer->callStatus) == 'offline' ? 'selected' : '' }}>Offline</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3 wait-time {{ strtolower($astrologer->callStatus) != 'wait time' ? 'd-none' : '' }}">
                                <div class="sm:grid grid-cols-4 gap-2">
                                    <div class="input">
                                        <div>
                                            <label id="callWaitTime" class="form-label">Waiting Time</label>
                                            <input type="time" name="callWaitTime"  class="form-control" value="{{ date('H:i:s', strtotime($astrologer->callWaitTime)) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3"><button class="btn btn-primary shadow-md mr-2">Update</button>

                            </div>

                        </div>

                    </div>

                </form>

            </div>

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