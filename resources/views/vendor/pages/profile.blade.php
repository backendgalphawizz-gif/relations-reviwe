@extends('vendor.app')

@push('css_or_js')
    <title>Advisor Profile</title>
@endpush

@section('content')
    <div class="loader"></div>
    <div class="grid grid-cols-11 gap-x-6">

        <div class="intro-y col-span-12 2xl:col-span-12">

            <div class="intro-y box">

                <div

                    class="flex flex-col sm:flex-row items-center p-2 border-b border-slate-200/60 dark:border-darkmode-400">

                    <h2 class="font-medium text-base mr-auto">Advisor Profile</h2>

                </div>

                <form action="{{ route('advisor.update-profile') }}" method="POST" enctype="multipart/form-data" id="edit-profile">

                    @csrf

                    <div id="input" class="p-2">
                        <div class="preview">
                            <div class="mt-3">
                                <div class="sm:grid grid-cols-4 gap-2">

                                    <div class="input">

                                        <div>

                                            <input type="hidden" name="field_id" id="field_id" class="form-control" placeholder="Name" value="{{ $user['id'] }}">

                                            <label for="name" class="form-label">Name<span class="text-danger">*</span></label>

                                            <input id="name" name="name" type="text" class="form-control" placeholder="Name" value="{{ $user['name'] }}" required onkeypress="return Validate(event);">

                                        </div>

                                    </div>

                                    <div class="input">

                                        <div>

                                            <label id="email" class="form-label">Email<span class="text-danger">*</span></label>

                                            <input type="text" id="email" name="email" class="form-control" placeholder="Email" aria-describedby="input-group-3" value="{{ $user['email'] }}" required>

                                        </div>

                                    </div>
                                    <div class="input">
                                        <div>
                                            <label id="mobile" class="form-label">Mobile<span class="text-danger">*</span></label>
                                            <input type="text" id="mobile" name="mobile" class="form-control" placeholder="Mobile" aria-describedby="input-group-3" value="{{ $user['contactNo'] }}" required>
                                        </div>
                                    </div>

                                    <div class="input">
                                        <div>
                                            <label id="birthDate" class="form-label">DOB<span class="text-danger">*</span></label>
                                            <input type="date" id="birthDate" name="birthDate" class="form-control" placeholder="birthDate" aria-describedby="input-group-3" value="{{ date('Y-m-d', strtotime($astrologer->birthDate)) }}" max="{{ date('Y-m-d', strtotime('-18 year')) }}" required>
                                        </div>
                                    </div>

                                    <div class="input">
                                        <div>
                                            <label id="gender" class="form-label">Gender<span class="text-danger">*</span></label>
                                            <select name="gender" class="form-control select2" >
                                                <option value="Male" {{ strtolower($astrologer->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                                <option value="Female" {{ strtolower($astrologer->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                            </select>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="mt-3">

                                <div class="sm:grid grid-cols-4 gap-2">

                                    <div class="input">

                                        <div>

                                            <label for="productImage" class="form-label">Profile Image<span class="text-danger">*</span></label>

                                            <img id="thumb" width="150px" src="{{ asset($user['profile']) }}" alt="Profile image" onerror="this.style.display='none'"; />

                                            <input type="file" class="mt-2" name="profile" id="profile" onchange="preview()" accept="image/*">

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div class="mt-3">
                                <hr class="mb-3">
                                <div class="sm:grid grid-cols-4 gap-2">
                                    <div class="input">
                                        <div>
                                            <label id="experienceInYears" class="form-label">Experience In Years<span class="text-danger">*</span></label>
                                            <input type="text" id="experienceInYears" name="experienceInYears" class="form-control" placeholder="Mobile" aria-describedby="input-group-3" value="{{ $astrologer->experienceInYears }}" required>
                                        </div>
                                    </div>

                                    <div class="input">
                                        <div>
                                            <label id="primarySkill" class="form-label">Primary Skill<span class="text-danger">*</span></label>
                                            <select name="primarySkill[]" class="form-control select2" multiple>
                                                @foreach ($skills as $skill)
                                                    <option value="{{ $skill['id'] }}" {{ in_array($skill['id'], explode(',' ,$astrologer->primarySkill)) ? 'selected' : '' }}>{{ $skill['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="input">
                                        <div>
                                            <label id="astrologerCategoryId" class="form-label">Advisor Category<span class="text-danger">*</span></label>
                                            <select name="astrologerCategoryId[]" class="form-control select2" multiple>
                                                @foreach ($mainCategories as $main)
                                                    <option value="{{ $main['id'] }}" {{ in_array($main['id'], explode(',' ,$astrologer->astrologerCategoryId)) ? 'selected' : '' }}>{{ $main['name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="input">
                                        <div>
                                            <label id="languageKnown" class="form-label">Advisor Category<span class="text-danger">*</span></label>
                                            <select name="languageKnown[]" class="form-control select2" multiple>
                                                @foreach ($languages as $language)
                                                    <option value="{{ $language['id'] }}" {{ in_array($language['id'], explode(',' ,$astrologer->languageKnown)) ? 'selected' : '' }}>{{ $language['languageName'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3">
                                <hr class="mb-3">
                                <div class="sm:grid grid-cols-4 gap-2">
                                    <div class="input">
                                        <div>
                                            <label id="currentCity" class="form-label">Current City<span class="text-danger">*</span></label>
                                            <input type="text" id="currentCity" name="currentCity" class="form-control" placeholder="Current City" aria-describedby="input-group-3" value="{{ $astrologer->currentCity }}" required>
                                        </div>
                                    </div>
                                    <div class="input">
                                        <div>
                                            <label id="highestQualification" class="form-label">Highest Qualification<span class="text-danger">*</span></label>
                                            <input type="text" id="highestQualification" name="highestQualification" class="form-control" placeholder="Highest Qualification" aria-describedby="input-group-3" value="{{ $astrologer->highestQualification }}" required>
                                        </div>
                                    </div>
                                    <div class="input">
                                        <div>
                                            <label id="degree" class="form-label">Degree/ Diploma<span class="text-danger">*</span></label>
                                            <input type="text" id="degree" name="degree" class="form-control" placeholder="Degree/ Diploma" aria-describedby="input-group-3" value="{{ $astrologer->degree }}" required>
                                        </div>
                                    </div>
                                    <div class="input">
                                        <div>
                                            <label id="college" class="form-label">College/School/University<span class="text-danger">*</span></label>
                                            <input type="text" id="college" name="college" class="form-control" placeholder="College/School/University" aria-describedby="input-group-3" value="{{ $astrologer->college }}" required>
                                        </div>
                                    </div>
                                    <div class="input">
                                        <div>
                                            <label id="loginBio" class="form-label">Long Bio<span class="text-danger">*</span></label>
                                            <input type="text" id="loginBio" name="loginBio" class="form-control" placeholder="loginBio" aria-describedby="input-group-3" value="{{ $astrologer->loginBio }}" required>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="mt-5"><button class="btn btn-primary shadow-md mr-2">Edit Profile</button>

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
@endpush