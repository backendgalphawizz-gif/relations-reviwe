@extends('../layout/' . $layout)

@section('subhead')
    <title>Daily Horoscope</title>
@endsection

@section('subcontent')
    <div class="loader"></div>
    <div class="grid-cols-12 mt-10" style="width:100%">
        <h2 class="d-inline intro-y text-lg font-medium  mr-2 dailytitle">Daily Horoscope</h2>
        <form action="{{ route('dailyHoroscope') }}" method="POST" enctype="multipart/form-data" class="addbtn">
            @csrf
            <div class="relative w-56 mx-auto horodate" style="display: inline-block; margin-left: 13px;
        ">
                <div
                    class="absolute rounded-l w-10 h-full flex items-center justify-center bg-slate-100 border text-slate-500 dark:bg-darkmode-700 dark:border-darkmode-800 dark:text-slate-400">
                    <i data-lucide="calendar" class="w-4 h-4"></i>
                </div>
                <input type="text" id="filterDate" name="filterDate" class="datepicker form-control pl-12"
                    data-single-mode="true" value={{ $filterDate }}>
            </div>
            <div style="display: inline-block" class="input mt-2 sm:mt-0">
                <select class="form-control w-full" id="filterSign" name="filterSign" value="filterSign">
                    @foreach ($signs as $sign)
                        <option id="signId" @if ($sign['id'] == $selectedId) selected @endif
                            value="{{ $sign['id'] }}">
                            {{ $sign['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <button style="display:inline-flex;top: 4px; position: relative;" id="deletebtn"
                class="btn btn-primary mr-2 mb-2"><i data-lucide="filter"
                    class="deletebtn w-4 h-4 mr-2"></i>Apply</button>
        </form>
        <a href="dailyHoroscope/add" style="top: 4px; position: relative;"
            class="btn btn-primary shadow-md mr-2 mb-2 d-inline addbtn horobtn">Add
            Daily Horoscope
        </a>
    </div>
    <div class="grid grid-cols-12 gap-6" style="width:100%">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap  mt-2">
            @if (count($dailyHoroscope) > 0 || count($dailyHoroscopeStatics) > 0)
                <a href="{{ route('redirectEditDailyHoroscope', ['horoscopeSignId' => $selectedId, 'horoscopeDate' => $filterDate]) }}"
                    id="deletebtn" class="btn btn-primary w-32 mr-2 mb-2"><i data-lucide="check-square"
                        class="deletebtn w-4 h-4 mr-2"></i>Edit</a>
                <a onclick="deletebtn({{ $selectedId }},'{{ $filterDate }}')" id="deletebtn"
                    class="btn btn-primary w-32 mr-2 mb-2" data-tw-target="#deleteModal" data-tw-toggle="modal"><i
                        data-lucide="trash-2" class="deletebtn w-4 h-4 mr-2"></i>Delete</a>
            @endif

        </div>
    </div>
    @if (count($dailyHoroscopeStatics) > 0)
        <div class="grid-cols-12 mt-5">
            <div class="card border p-5 mt-5">
                <div class="d-inline mr-3">
                    <h4><b> Lucky Colour</b></h4>
                    <h6
                        style="background-color:{{ $dailyHoroscopeStatics[0]->luckyColor }};color:{{ $dailyHoroscopeStatics[0]->luckyColor }}
                        ">
                        {{ $dailyHoroscopeStatics[0]->luckyColor }}
                    </h6>
                </div>
                <div class="d-inline
                        mr-3">
                    <h4><b> Lucky Time</b></h4>
                    <h6>{{ $dailyHoroscopeStatics[0]->luckyTime }}</h6>
                </div>
                <div class="d-inline mr-3">
                    <h4><b> Lucky Number</b></h4>
                    <h6>{{ $dailyHoroscopeStatics[0]->luckyNumber }}</h6>
                </div>
                <div class="d-inline mr-3">
                    <h4><b> Mood Of The Day</b></h4>
                    <h6>{{ $dailyHoroscopeStatics[0]->moodday }}</h6>
                </div>
            </div>
        </div>
    @endif
    <div class="grid-cols-12 mt-5 daily" style="width:100%">
        @foreach ($dailyHoroscope as $horoscope)
            <div class="card border p-5 mt-5">
                <h2 style="font-size: 20px;font-weight:600;display:inline-block">
                    {{ $horoscope->category }}({{ $horoscope->percentage }}%)
                </h2>
                <h6 class="mt-2">{!! $horoscope->description !!}</h6>
            </div>
        @endforeach
    </div>
    <div id="deleteModal" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="p-5 text-center">
                        <i data-lucide="x-circle" class="w-16 h-16 text-danger mx-auto mt-3"></i>
                        <div class="text-3xl mt-5">Are you sure?</div>
                        <div class="text-slate-500 mt-2">Do you really want to delete these records? <br>This process
                            cannot be undone.</div>
                    </div>
                    <form action="{{ route('deleteHoroscope') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" id="del_id" name="del_id">
                        <input type="hidden" id="horoscope_date" name="horoscope_date">
                        <div class="px-5 pb-8 text-center">
                            <button type="button" data-tw-dismiss="modal"
                                class="btn btn-outline-secondary w-24 mr-1">Cancel</button>
                            <button class="btn btn-danger w-24">Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <script type="text/javascript">
        $(document).ready(function() {
            CKEDITOR.config.removeButtons = 'Image';
        });

        function getDate() {
            $(".datepicker").on("change", function() {
                let pickedDate = $("input").val();
            });
        }

        function editbtn($id, $category, $signId, $description, $horoscopeDate, $percentage) {
            $('#horoscopeId').val($id);
            $('#category').val($category);
            $('#horoscopeSignId').val($signId);
            $('#description').val($description);
            $('#percentage').val($percentage);
            var newdate = $horoscopeDate.split("-");
            var date = newdate[2].split(" ");
            date = newdate[0] + '-' + newdate[1] + '-' + date[0]
            $('#horoscopeDate').val(date);
            var editor = CKEDITOR.instances['editdescription'];
            if (editor) {
                editor.destroy(true);
            }
            CKEDITOR.replace('editdescription');
            var editor = CKEDITOR.instances['editdescription'];
            CKEDITOR.instances['editdescription'].setData($description)
        }

        function deletebtn($id, $horoscopeDate) {
            $('#del_id').val($id);
            $('#horoscope_date').val($horoscopeDate);
        }

        function showEditor() {
            var editor = CKEDITOR.instances['description'];
            if (editor) {
                editor.destroy(true);
            }
            CKEDITOR.replace('description', {
                toolbar: 'simple'
            });
            var editor = CKEDITOR.instances['description'];
            CKEDITOR.config.removeButtons = 'Image';
        }

        function numbersOnly(e) {
            var keycode = e.keyCode;
            if ((keycode < 48 || keycode > 57) && keycode != 9 && keycode != 8) {
                e.preventDefault();
            }
        }
    </script>
    <script>
        $(window).on('load', function() {
            $('.loader').hide();
        })
    </script>
@endsection
