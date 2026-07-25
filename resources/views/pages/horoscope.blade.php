@extends('../layout/' . $layout)

@section('subhead')
    <title>Horoscope</title>
@endsection

@section('subcontent')
    <div class="loader"></div>
    <div class="grid-cols-12 mt-10" style="width:100%">
        <h2 class=" intro-y text-lg font-medium  mr-2" style="display: inline-block">Horoscope</h2>
        <form class="addbtn " action="{{ route('horoscope') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div style="display: inline-block;margin-left: 13px" class="input horosign mt-2 sm:mt-0">
                <select class="form-control w-full" id="filterSign" name="filterSign" value="filterSign">
                    @foreach ($signs as $sign)
                        <option id="signId" @if ($sign['id'] == $selectedId) selected @endif value={{ $sign['id'] }}>
                            {{ $sign['name'] }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display: inline-block" class="input horosign mt-2 sm:mt-0">
                <button style="display:inline-flex;top: 4px; position: relative;"
                    class="form-control w-full btn btn-primary w-32 mr-2 mb-2" id="horoscopeType">
                    <i data-lucide="filter" class="w-4 h-4 mr-2"></i>Apply
                </button>
            </div>
        </form>
        <a href="horoscope/add" style="top: 4px; position: relative;"
            class="btn btn-primary shadow-md mr-2 mb-2 d-inline  addbtn  horobtn horo">Add
            Horoscope
        </a>
    </div>
    <div class="grid grid-cols-12 gap-6 horedit">
        <div class="intro-y col-span-12 flex flex-wrap sm:flex-nowrap  mt-2">
            @if (count($horoscope) > 0)
                <a href="{{ route('redirectEditHoroscope', ['horoscopeSignId' => $selectedId]) }}" id="deletebtn"
                    class="btn btn-primary w-32 mr-2 mb-2"><i data-lucide="check-square"
                        class="deletebtn w-4 h-4 mr-2"></i>Edit</a>
                <a onclick="deletebtn({{ $selectedId }})" id="deletebtn" class="btn btn-primary w-32 mr-2 mb-2"
                    data-tw-target="#deleteModal" data-tw-toggle="modal"><i data-lucide="trash-2"
                        class="deletebtn w-4 h-4 mr-2"></i>Delete</a>
            @endif
        </div>
    </div>

    <div class="grid-cols-12 mt-5 daily">
        @foreach ($horoscope as $horo)
            <div class="card border p-4 mt-5">
                <h2 style="font-size: 20px;font-weight:600;display:inline-block">
                    {{ $horo->title }}
                </h2>
                <h6 class="mt-2">{!! $horo->description !!}</h6>
            </div>
        @endforeach
    </div>
    <div id="add-dailyHoroscope" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form data-single="true" action="{{ route('addHoroscope') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div id="input" class="p-5">
                        <div class="preview">
                            <div class="mt-3">
                                <div class="sm:grid grid-cols gap-2">
                                    <div class="input mt-2 sm:mt-0">
                                        <label id="productCategoryId" class="form-label">Select Category</label>
                                        <select class="form-control w-full" id="horoscopeType" name="horoscopeType"
                                            value="horoscopeType">
                                            <option id="duration" value="Weekly">Weekly</option>
                                            <option id="duration" value="Monthly">Monthly</option>
                                            <option id="duration" value="Yearly">Yearly</option>
                                        </select>
                                    </div>
                                    <div class="input">
                                        <div>
                                            <label id="amount" class="form-label">Title</label>
                                            <input type="text" id="title" name="title" class="form-control"
                                                placeholder="Title" aria-describedby="input-group-3" required
                                                onkeypress="return Validate(event);">
                                        </div>
                                    </div>
                                    <div class="input mt-2 sm:mt-0">
                                        <label id="productCategoryId" class="form-label">Horoscope Sign</label>
                                        <select class="form-control" id="horoscopeSignId" name="horoscopeSignId"
                                            value="horoscopeSignId" required>
                                            <option disabled selected>--Select Sign--</option>
                                            @foreach ($signs as $sign)
                                                <option id="productCategoryId" value={{ $sign['id'] }}>
                                                    {{ $sign['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="input" id="classic-editor">
                                        <label for="description" class="from-label">Description</label>
                                        <textarea class="form-control ml-3" id="description" name="description" required></textarea>
                                    </div>
                                    <div class="mt-5"><button class="btn btn-primary shadow-md mr-2">Add
                                            Horoscope</button>
                                    </div>

                                </div>
                            </div>
                        </div>
                </form>
            </div>
        </div>
    </div>
    <div id="edit-dailyHoroscope" class="modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form data-single="true" action="{{ route('editHoroscope') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div id="input" class="p-5">
                        <div class="preview">
                            <div class="mt-3">
                                <div class="sm:grid grid-cols gap-2">
                                    <div class="input mt-2 sm:mt-0">
                                        <input type="hidden" id="horoscopeId" name="horoscopeId" class="form-control"
                                            placeholder="Title" aria-describedby="input-group-3">
                                        <label id="productCategoryId" class="form-label">Select Category</label>
                                        <select class="form-control w-full" id="horoscopeType" name="horoscopeType"
                                            value="horoscopeType">
                                            <option id="duration" value="Weekly">Weekly</option>
                                            <option id="duration" value="Monthly">Monthly</option>
                                            <option id="duration" value="Yearly">Yearly</option>
                                        </select>
                                    </div>
                                    <div class="input">
                                        <div>
                                            <label id="amount" class="form-label">Title</label>
                                            <input type="text" id="title" name="title" class="form-control"
                                                placeholder="Title" aria-describedby="input-group-3" required
                                                onkeypress="return Validate(event);">
                                        </div>
                                    </div>
                                    <div class="input mt-2 sm:mt-0">
                                        <label id="productCategoryId" class="form-label">Horoscope Sign</label>
                                        <select class="form-control" id="horoscopeSignId" name="horoscopeSignId"
                                            value="horoscopeSignId" required>
                                            <option disabled selected>--Select Sign--</option>
                                            @foreach ($signs as $sign)
                                                <option id="productCategoryId" value={{ $sign['id'] }}>
                                                    {{ $sign['name'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="input" id="classic-editor">
                                        <label for="description" class="from-label">Description</label>
                                        <textarea class="form-control editor ml-3" required id="editdescription" name="editdescription">Your content here</textarea>
                                    </div>
                                    <div class="mt-5"><button class="btn btn-primary shadow-md mr-2">Edit
                                            Horoscope</button>
                                    </div>

                                </div>
                            </div>
                        </div>
                </form>
            </div>
        </div>
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
                    <form action="{{ route('deleteHoro') }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <input type="hidden" id="del_id" name="del_id">
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
        function editbtn($id, $title, $signId, $description, $horoscopeType) {
            $('#horoscopeId').val($id);
            $('#title').val($title);
            $('#horoscopeSignId').val($signId);
            $('#description').val($description);
            $('#horoscopeType').val($horoscopeType);
            var editor = CKEDITOR.instances['editdescription'];
            if (editor) {
                editor.destroy(true);
            }
            CKEDITOR.replace('editdescription');
            var editor = CKEDITOR.instances['editdescription'];
            CKEDITOR.instances['editdescription'].setData($description)
        }

        function addHoroscope() {
            var editor = CKEDITOR.instances['description'];
            if (editor) {
                editor.destroy(true);
            }
            CKEDITOR.replace('description');
            var editor = CKEDITOR.instances['description'];
        }

        function Validate(event) {
            var regex = new RegExp("^[0-9-!@#$%&<>*?]");
            var key = String.fromCharCode(event.charCode ? event.which : event.charCode);
            if (regex.test(key)) {
                event.preventDefault();
                return false;
            }
        }

        function deletebtn($id) {
            $('#del_id').val($id);
        }
    </script>
    <script>
        $(window).on('load', function() {
            $('.loader').hide();
        })
    </script>
@endsection
