@extends('../layout/' . $layout)

@section('subhead')
@endsection

@section('subcontent')
    <div class="loader"></div>
    <h2 class="intro-y text-lg font-medium mt-10">Privacy Policy</h2>
    <div class="grid grid-cols-12 gap-6 mt-5">
        <div class="intro-y col-span-12 mt-2">
            <div class="intro-y box">
                <div
                    class="flex flex-col sm:flex-row items-center p-5 border-b border-slate-200/60 dark:border-darkmode-400">
                    <!-- <h2 class="font-medium text-base mr-auto">Add Blog</h2> -->
                </div>
                <form data-single="true" action="{{ route('updatePages') }}" method="POST" >
                    @csrf
                    <div id="input" class="p-5">
                        <div class="preview">
                            <div class="mt-3">
                                <div class="sm:grid grid-cols gap-2">
                                    <div class="input">
                                        <div>
                                            <label for="title" class="form-label">Title</label>
                                            <input type="text" name="title" id="title" class="form-control"
                                                placeholder="Title" value="{{$data->title ?? ''}}" required>
                                            <input type="hidden" name="slug" class="form-control" value="{{$data->slug ?? ''}}" required>
                                        </div>
                                    </div>
                                    <div class="input" id="classic-editor">
                                        <label for="description" class="from-label">Description</label>
                                        <textarea class="form-control editor ml-3" id="description" name="description">{{$data->description ?? ''}}</textarea>
                                    </div>
                                    <div class="mt-5"><button class="btn btn-primary shadow-md mr-2">update</button>
                                    </div>
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
    <script type="text/javascript">
        CKEDITOR.replace('description')
        function editbtn($id, $title) {
            var id = $id;
            $fid = id;
            $('#id').val($fid);
            $('#etitle').val($title);

        }

        function addSubCategory($id) {
            var id = $id;
            $fid = id;
            $('#supportId').val($fid);
            var editor = CKEDITOR.instances['did'];
            if (editor) {
                editor.destroy(true);
            }
            CKEDITOR.replace('did');
        }

        function deleteHelpSupport($id) {
            $('#del_id').val($id);
        }

        function Validate(event) {
            var regex = new RegExp("^[0-9-!@#$%&<>*?]");
            var key = String.fromCharCode(event.charCode ? event.which : event.charCode);
            if (regex.test(key)) {
                event.preventDefault();
                return false;
            }
        }

        function clearForm() {
            var ele = document.getElementById('help-support').reset();
        }
    </script>
    <script>
        $(window).on('load', function() {
            $('.loader').hide();
        })
    </script>
@endsection
