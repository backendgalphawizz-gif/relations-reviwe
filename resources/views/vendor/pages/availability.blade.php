@extends('vendor.app')

@push('css_or_js')
    <title>Advisor Profile</title>
@endpush

@section('content')
    <div class="loader"></div>
    <div class="grid grid-cols-11 gap-x-6">

        <div class="intro-y col-span-12 2xl:col-span-12">

            <div class="intro-y box">

                <div class="flex flex-col sm:flex-row items-center p-2 border-b border-slate-200/60 dark:border-darkmode-400">

                    <h2 class="font-medium text-base mr-auto">Update Availability</h2>

                </div>

                <form action="{{ route('advisor.update-availability') }}" method="POST" enctype="multipart/form-data" id="edit-profile">

                    @csrf

                    <input type="hidden" name="id" value="{{ $astrologer->id }}">

                    <div id="input" class="p-2">
                        <div class="preview">
                            @foreach ($astrologerAvailability as $availability)
                                <div class="input mt-2 sm:mt-0">

                                    <h4 class="font-medium text-lg mt-3 d-inline">{{ $availability['day'] }}</h4>

                                    <button style="padding: 3px 6px;"class="btn btn-sm btn-primary add-field d-inline"
                                        type="button" onclick="addField('{{ strtolower($availability['day']) }}')">+</button>

                                    <div class="" id="astrologerfield"> 
                                        {{-- intro-y grid grid-cols-12 gap-6 --}}

                                        @forelse ($availability['time'] as $timeIndex => $time)
                                        <div class="{{ strtolower($availability['day']) }}-time-slot intro-y grid grid-cols-12 gap-6 mt-3">
                                            <div class="{{ strtolower($availability['day']) }}_fromTime{{ $timeIndex }} intro-y col-span-6 md:col-span-6">

                                                <label id="input-group" class="astrologerAvailability_{{ strtolower($availability['day']) }}_time{{ $timeIndex }}_fromTime form-label">FromTime
                                                <button style="padding: 2px 8px;border-radius: 50%" class="btn btn-sm btn-primary add-field d-inline" type="button" onclick="removeField('{{ strtolower($availability['day']) }}','{{ $timeIndex }}')">-</button></label>

                                                <input type="hidden"
                                                    class="form-control"
                                                    id="astrologerAvailability[{{ strtolower($availability['day']) }}_{{ $timeIndex }}][day]"
                                                    placeholder="FromTime"
                                                    name="astrologerAvailability[{{ strtolower($availability['day']) }}_{{ $timeIndex }}][day]"
                                                    aria-describedby="input-group-4" value="{{ strtolower($availability['day']) }}">

                                                <input type="time" class="form-control" placeholder="FromTime"
                                                    name="astrologerAvailability[{{ strtolower($availability['day']) }}_{{ $timeIndex }}][time][{{ $timeIndex }}][fromTime]"
                                                    id="astrologerAvailability_{{ strtolower($availability['day']) }}_time{{ $timeIndex }}_fromTime"
                                                    aria-describedby="input-group-4" value="{{ date('H:i:s', strtotime($time['fromTime'])) }}">

                                            </div>

                                            <div class="{{ strtolower($availability['day']) }}_toTime{{ $loop->index }} intro-y col-span-6 md:col-span-6">
                                                <label id="input-group"
                                                    class="astrologerAvailability_{{ strtolower($availability['day']) }}_time{{ $loop->index }}_toTime form-label">ToTime</label>

                                                <input type="time" class="form-control" placeholder="FromTime"
                                                    name="astrologerAvailability[{{ strtolower($availability['day']) }}_{{ $timeIndex }}][time][{{ $loop->index }}][toTime]"
                                                    id="astrologerAvailability_{{ strtolower($availability['day']) }}_time{{ $loop->index }}_toTime"
                                                    aria-describedby="input-group-4" value="{{ date('H:i:s', strtotime($time['toTime'])) }}">

                                            </div>
                                        </div>
                                        @empty
                                            <div class="{{ strtolower($availability['day']) }}-time-slot intro-y grid grid-cols-12 gap-6 mt-3"></div>
                                        @endforelse

                                    </div>

                                </div>
                            @endforeach

                            <hr>

                            <div class="mt-5"><button class="btn btn-primary shadow-md mr-2">Update</button>

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

    {{-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}

    <script type="text/javascript"></script>

    <script>
        var spinner = $('.loader');

        $(window).on('load', function() {

            $('.loader').hide();

        });

    </script>

    <script>
        $(document).ready(function() {

            jQuery('.select2').select2({

                allowClear: true,

                tags: true,

                tokenSeparators: [',', ' ']

            });

        });


        function printErrorMsg(msg) {

        }



        function preview() {

            document.getElementById("thumb").style.display = "block";

            thumb.src = URL.createObjectURL(event.target.files[0]);

        }

        var times = {{ Js::from($astrologerAvailability) }};

        var dayTime = [];





        function addField($day) {

            if (times && times.length > 0) {

                console.log('times ----------- ', times, $day)

                dayTime = times.find(c => c.day.toLowerCase() == $day)['time']

                console.log('dayTime ------------------ ', dayTime)

                dayTime.push({

                    fromTime: '',

                    toTime: ''

                })

                console.log('dayTime ------------------ ', dayTime)

            }

            html = '';

            htmlto = `<div class="${$day}_toTime${(dayTime.length - 1)} intro-y col-span-6 md:col-span-6">
                        <label id="input-group" class="form-label astrologerAvailability_${$day}_time${(dayTime.length - 1)}_toTime">To Time</label>
                        <input type="time" class="form-control"  placeholder="ToTime" name="astrologerAvailability[${$day}_${(dayTime.length - 1)}][time][${(dayTime.length - 1)}][toTime]" id="astrologerAvailability_${$day}_time${(dayTime.length - 1)}_toTime">
                    </div>`;

            html += `<div class="${$day}_fromTime${(dayTime.length - 1)} intro-y col-span-6 md:col-span-6">
                    <label id="input-group" class="astrologerAvailability_${$day}_time${(dayTime.length - 1)}_fromTime form-label">From Time
                        <button style="padding: 2px 8px;border-radius: 50%" class="btn btn-sm btn-primary add-field d-inline" type="button" onclick=removeField('${$day}','${(dayTime.length - 1)}')>-</button>
                    </label>
                    <input id="astrologerAvailability[${$day}_${(dayTime.length - 1)}][day]" type="hidden" class="form-control" placeholder="FromTime" name="astrologerAvailability[${$day}_${(dayTime.length - 1)}][day]" aria-describedby="input-group-4" value="${$day}">
                    <input type="time" class="form-control" placeholder="FromTime" id="astrologerAvailability_${$day}_time${(dayTime.length - 1)}_fromTime" name="astrologerAvailability[${$day}_${(dayTime.length - 1)}][time][${(dayTime.length - 1)}][fromTime]" aria-describedby="input-group-4">
                </div>
                `;

            $('.' + $day + '-time-slot').append(`${html}`);
            $('.' + $day + '-time-slot').append(`${htmlto}`);

            // $('.' + $day + '-time-slot').append();

        }



        function removeField($day, $index) {

            if (dayTime.length == 0)

                dayTime = times.filter(c => c.day.toLowerCase() == $day)[0]['time'];

            dayTime.splice($index, 1);


            console.log('#astrologerAvailability_' + $day + '_time' + $index + '_fromTime', $('#astrologerAvailability_' + $day + '_time' + $index + '_fromTime'))

            $('#astrologerAvailability_' + $day + '_time' + $index + '_fromTime').remove();

            $('#astrologerAvailability_' + $day + '_time' + $index + '_toTime').remove();



            $('.astrologerAvailability_' + $day + '_time' + $index + '_fromTime').remove();

            $('.astrologerAvailability_' + $day + '_time' + $index + '_toTime').remove();

            $('#astrologerAvailability[' + $day + '_' + $index + '][day]').remove();

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
    </script>

@endpush