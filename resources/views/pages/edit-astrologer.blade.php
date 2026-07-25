@extends('../layout/' . $layout)



@section('subhead')
@endsection



@section('subcontent')
    <div class="loader"></div>

    <form id="editastrologer" method="POST" enctype="multipart/form-data">

        @csrf

        <div class="grid grid-cols-12 gap-6 mt-5">

            <div class="intro-y col-span-12 overflow-auto lg:overflow-visible ">

            </div>

        </div>

        <!-- BEGIN: Profile Info -->

        <div class="intro-y box  pt-5 mt-5">

            <div id="link-tab" class="p-3">

                <button type="submit"class="btn btn-primary shadow-md mr-2 d-inline addbtn">Save

                </button>

                <ul class="nav nav-link-tabs" role="tablist">

                    <li id="example-1-tab" class="nav-item flex-1" role="presentation">

                        <button class="nav-link w-full py-2 active" data-tw-toggle="pill" data-tw-target="#example-tab-1"
                            type="button" role="tab" aria-controls="example-tab-1" aria-selected="true">

                            Personal Detail

                        </button>

                    </li>

                    <li id="example-2-tab" class="nav-item flex-1" role="presentation">

                        <button class="nav-link w-full py-2" data-tw-toggle="pill" data-tw-target="#example-tab-2"
                            type="button" role="tab" aria-controls="example-tab-2" aria-selected="false">

                            Skill Detail

                        </button>

                    </li>

                    <li id="example-3-tab" class="nav-item flex-1" role="presentation">

                        <button class="nav-link w-full py-2" data-tw-toggle="pill" data-tw-target="#example-tab-3"

                            type="button" role="tab" aria-controls="example-tab-3" aria-selected="false">

                            Other Details

                        </button>

                    </li>

                    <li id="example-4-tab" class="nav-item flex-1" role="presentation">

                        <button class="nav-link w-full py-2" data-tw-toggle="pill" data-tw-target="#example-tab-4"
                            type="button" role="tab" aria-controls="example-tab-4" aria-selected="false">

                            Availability

                        </button>

                    </li>

                </ul>



                <div class="tab-content mt-5 editastrologertab">

                    <div id="example-tab-1" class="tab-pane grid grid-cols-12 gap-6 mt-5 leading-relaxed active" role="tabpanel"
                        aria-labelledby="example-1-tab">

                        <div class="input col-span-4">

                            <div>

                                <input type="hidden" name="id" id="id" class="form-control"
                                    placeholder="Customer Name" value="{{ $astrologer['id'] }}">

                                <label for="regular-form-1" class="form-label">Name</label>

                                <input type="text" name="name" id="name" class="form-control"
                                    placeholder="Astrologer Name" value="{{ $astrologer['name'] }}"
                                    onkeypress="return Validate(event);" required>

                            </div>

                        </div>

                        <div class="input col-span-4">

                            <div>

                                <label for="regular-form-1" class="form-label">Email</label>

                                <input type="text" name="email" id="email" class="form-control"
                                    placeholder="Astrologer Email" onkeypress="return validateJavascript(event);"
                                    value="{{ $astrologer['email'] }}" required>

                            </div>

                        </div>

                        <div class="input col-span-4">

                            <div>

                                <label for="regular-form-1" class="form-label">Mobile Number</label>

                                <input type="text" name="contactNo" id="contactNo" class="form-control"
                                    placeholder="ContactNo" value="{{ $astrologer['contactNo'] }}" required>

                            </div>

                        </div>

                        <div class="intro-y col-span-12">

                            <div>

                                <label for="profile" class="form-label">Profile Image</label>

                                <img id="thumb" width="150px" src="/{{ $astrologer['profileImage'] }}"
                                    alt="Customer image" onerror="this.style.display='none';" />

                                <input type="file" class="mt-2" name="profileImage" id="profileImage"
                                    onchange="preview()" accept="image/*">

                            </div>

                        </div>

                    </div>

                    <div id="example-tab-2" class="tab-pane leading-relaxed" role="tabpanel"
                        aria-labelledby="example-2-tab">

                        <div class="intro-y grid grid-cols-12 gap-6 mt-5">

                            <div class="intro-y col-span-6 md:col-span-6">

                                <div class="input mt-3">

                                    <div>

                                        <label for="title" class="form-label">Select Gender</label>

                                        <select data-minimum-results-for-search="Infinity" name="gender"
                                            class="form-control select2" data-placeholder="Gender">

                                            <option Value="Female"
                                                {{ $astrologer['gender'] == 'Female' ? 'selected' : '' }}>

                                                Female

                                            </option>

                                            <option Value="Male" {{ $astrologer['gender'] == 'Male' ? 'selected' : '' }}>

                                                Male

                                            </option>

                                        </select>

                                    </div>

                                </div>

                            </div>

                            <div class="intro-y col-span-6 md:col-span-6">

                                <div class="input mt-3">

                                    <div>

                                        <label id="input-group" class="form-label">Birth Date</label>

                                        <input type="date" class="form-control" placeholder="Unit"
                                            aria-describedby="input-group-3" name="birthDate"
                                            value="{{ date('Y-m-d', strtotime($astrologer['birthDate'])) }}" required>

                                    </div>

                                </div>

                            </div>

                            <div class="intro-y col-span-6 md:col-span-6">

                                <div class="preview mt-3">

                                    <label for="title" class="form-label">Advisor Category</label>

                                    <select name="astrologerCategoryId[]" class="form-control select2 category" multiple
                                        data-placeholder="Choose Your Category">

                                        @foreach ($astrologerCategory as $categroy)
                                            <option value={{ $categroy->id }}>

                                                {{ $categroy->name }}</option>
                                        @endforeach

                                    </select>

                                </div>

                            </div>

                            <div class="intro-y col-span-6 md:col-span-6">

                                <div class="preview mt-3">

                                    <label for="title" class="form-label">Primary Skills</label>

                                    <select name="primarySkill[]" class="form-control select2 primary" multiple
                                        data-placeholder="Choose Your Primary Skills">

                                        @foreach ($skills as $skill)
                                            <option value={{ $skill->id }}>

                                                {{ $skill->name }}</option>
                                        @endforeach

                                    </select>

                                </div>

                            </div>
{{-- 
                            <div class="intro-y col-span-6 md:col-span-6">

                                <div class="preview mt-3">

                                    <label for="title" class="form-label">All Skills</label>

                                    <select name="allSkill[]" class="form-control select2 all" multiple
                                        data-placeholder="Choose Your Primary Skills">

                                        @foreach ($skills as $skill)
                                            <option value={{ $skill->id }}>

                                                {{ $skill->name }}</option>
                                        @endforeach

                                    </select>

                                </div>

                            </div> --}}

                            <div class="intro-y col-span-6 md:col-span-6">

                                <div class="preview mt-3">

                                    <label for="title" class="form-label">Language</label>

                                    <select name="languageKnown[]" class="form-control select2 language" multiple
                                        data-placeholder="Choose Language">

                                        @foreach ($language as $lang)
                                            <option value={{ $lang->id }}>

                                                {{ $lang->languageName }}</option>
                                        @endforeach

                                    </select>

                                </div>

                            </div>

                            {{-- <div class="intro-y col-span-6 md:col-span-6" style="display:none;">

                                <div class="input mt-3">

                                    <div>

                                        <label for="regular-form-1" class="form-label">Add Your Charge(As per

                                            Minute)</label>

                                        <input onkeypress="return validateJavascript(event);" type="text" name="charge" id="charge" class="form-control"

                                            placeholder="Charge" value="{{ $astrologer['charge'] }}" required>

                                    </div>

                                </div>

                            </div> --}}

                            {{-- <div class="intro-y col-span-6 md:col-span-6">

                                <div class="input mt-3">

                                    <div>

                                        <label for="regular-form-1" class="form-label">Add Your video charge(As per

                                            Minute)</label>

                                        <input type="text" onkeypress="return validateJavascript(event);" name="videoCallRate" id="videoCallRate"

                                            class="form-control" placeholder="VideoCall Rate"

                                            value="{{ $astrologer['videoCallRate'] }}" required>

                                    </div>

                                </div>

                            </div> --}}

                            {{-- <div class="intro-y col-span-6 md:col-span-6">

                                <div class="input mt-3">

                                    <div>

                                        <label for="regular-form-1" class="form-label">Add Your report charge(As per

                                            Minute)</label>

                                        <input onkeypress="return validateJavascript(event);" type="text" name="reportRate" id="reportRate" class="form-control"

                                            placeholder="Reprot Rate" value="{{ $astrologer['reportRate'] }}" required>

                                    </div>

                                </div>

                            </div> --}}

                            <div class="intro-y col-span-6 md:col-span-6">

                                <div class="input mt-3">

                                    <div>

                                        <label for="regular-form-1" class="form-label">Experience In Years</label>

                                        <input onkeypress="return validateJavascript(event);" type="text"
                                            name="experienceInYears" id="experienceInYears" class="form-control"
                                            placeholder="Experience In Years"
                                            value="{{ $astrologer['experienceInYears'] }}" required>

                                    </div>

                                </div>

                            </div>

                            {{-- <div class="intro-y col-span-6 md:col-span-6" style="display:none;">

                                <div class="input mt-3">

                                    <div>

                                        <label for="regular-form-1" class="form-label">How many hours you can contribute

                                            daily?</label>

                                        <input onkeypress="return validateJavascript(event);" type="text" name="dailyContribution" id="dailyContribution"

                                            class="form-control" placeholder="Daily Contribution"

                                            value="{{ $astrologer['dailyContribution'] }}" required>

                                    </div>

                                </div>

                            </div> --}}
{{-- 
                            <div class="intro-y col-span-6 md:col-span-6">

                                <div class="input mt-3">

                                    <div>

                                        <label for="regular-form-1" class="form-label">Where did you hear about

                                            AsthaJyotish?</label>

                                        <input onkeypress="return validateJavascript(event);" type="text" name="hearAboutAstroguru" id="hearAboutAstroguru"

                                            class="form-control" placeholder="Youtube,Facebook"

                                            value="{{ $astrologer['hearAboutAstroguru'] }}">

                                    </div>

                                </div>

                            </div> --}}
                            
                            {{-- <div class="intro-y col-span-6 md:col-span-6">

                                <div class="flex flex-col sm:flex-row mt-2">

                                    <label for="regular-form-1" class="form-label">Are you working on any other

                                        platform?</label>

                                    <div class="flex flex-col sm:flex-row mt-2">

                                        <div class="form-check mr-2">

                                            <input class="form-check-input" type="radio"

                                                name="isWorkingOnAnotherPlatform" value=1

                                                {{ $astrologer['isWorkingOnAnotherPlatform'] ? 'checked' : '' }}>

                                            <label class="form-check-label" for="radio-switch-4">Yes</label>

                                        </div>

                                        <div class="form-check mr-2 mt-2 sm:mt-0">

                                            <input class="form-check-input" type="radio"

                                                name="isWorkingOnAnotherPlatform" value=0

                                                {{ !$astrologer['isWorkingOnAnotherPlatform'] ? 'checked' : '' }}>

                                            <label class="form-check-label" for="radio-switch-5">No</label>

                                        </div>

                                    </div>

                                </div>

                            </div> --}}

                        </div>

                    </div>

                    <div id="example-tab-3" class="tab-pane leading-relaxed" role="tabpanel"
                        aria-labelledby="example-3-tab">

                        <div class="intro-y grid grid-cols-12 gap-6 mt-5">

                            {{-- <div class="intro-y col-span-6 md:col-span-6">

                                <div class="input mt-3">

                                    <div>

                                        <label for="regular-form-1" class="form-label">Why do you think we should onboard

                                            you?</label>

                                        <input onkeypress="return validateJavascript(event);" type="text"
                                            name="whyOnBoard" id="whyOnBoard" class="form-control"
                                            placeholder="Why we should on board you?"
                                            value="{{ $astrologer['whyOnBoard'] }}" required>

                                    </div>

                                </div>

                            </div>

                            <div class="intro-y col-span-6 md:col-span-6" style="display:none">

                                <div class="input mt-3">

                                    <div>

                                        <label for="regular-form-1" class="form-label">What is suitable time for

                                            interview?</label>

                                        <input onkeypress="return validateJavascript(event);" type="text"
                                            name="interviewSuitableTime" id="interviewSuitableTime" class="form-control"
                                            placeholder="Enter Suitable Time For Interview"
                                            value="{{ $astrologer['interviewSuitableTime'] }}" required>

                                    </div>

                                </div>

                            </div> --}}

                            <div class="intro-y col-span-6 md:col-span-6">

                                <div class="input mt-3">

                                    <div>

                                        <label for="regular-form-1" class="form-label">Which city do you currently live

                                            in?</label>

                                        <input onkeypress="return validateJavascript(event);" type="text"
                                            name="currentCity" id="currentCity" class="form-control" placeholder="City"
                                            value="{{ $astrologer['currentCity'] }}">

                                    </div>

                                </div>

                            </div>

                            {{-- <div class="intro-y col-span-6 md:col-span-6">

                                <div class="preview mt-3">

                                    <label for="title" class="form-label">Main Source of business(Other than

                                        astrology)?</label>

                                    <select data-minimum-results-for-search="Infinity" id="mainSourceOfBusiness"
                                        name="mainSourceOfBusiness" class="form-control select2"
                                        data-placeholder="Main Source of business">

                                        @foreach ($mainSourceBusiness as $source)
                                            <option value='{{ $source->jobName }}'>

                                                {{ $source->jobName }}</option>
                                        @endforeach

                                    </select>

                                </div>

                            </div> --}}

                            <div class="intro-y col-span-6 md:col-span-6">

                                <div class="preview mt-3">

                                    <label for="title" class="form-label">Select your highest qualification</label>

                                    <select name="highestQualification" id="highestQualification"
                                        class="form-control select2" data-placeholder="Highest Qualification">

                                        @foreach ($highestQualification as $highest)
                                            <option value='{{ $highest->qualificationName }}'>

                                                {{ $highest->qualificationName }}</option>
                                        @endforeach

                                    </select>

                                </div>

                            </div>

                            <div class="intro-y col-span-6 md:col-span-6">

                                <div class="preview mt-3">

                                    <label for="title" class="form-label">Degree / Diploma</label>

                                    <select data-minimum-results-for-search="Infinity" id="degree" name="degree"
                                        class="form-control select2" data-placeholder="Degree">

                                        @foreach ($qualifications as $qua)
                                            <option value='{{ $qua->degreeName }}'>

                                                {{ $qua->degreeName }}</option>
                                        @endforeach

                                    </select>

                                </div>

                            </div>

                            <div class="intro-y col-span-6 md:col-span-6">

                                <div class="input mt-3">

                                    <div>

                                        <label for="regular-form-1" class="form-label">College/School/University</label>

                                        <input onkeypress="return validateJavascript(event);" type="text"
                                            name="college" id="college" class="form-control"
                                            placeholder="Enter your College/School/University"
                                            value="{{ $astrologer['college'] }}">

                                    </div>

                                </div>

                            </div>

                            {{-- <div class="intro-y col-span-6 md:col-span-6">

                                <div class="input mt-3">

                                    <div>

                                        <label for="regular-form-1" class="form-label">From where did you learn

                                            Astrology?</label>

                                        <input onkeypress="return validateJavascript(event);" type="text"
                                            name="learnAstrology" id="learnAstrology" class="form-control"
                                            placeholder="From where did you learn Astrology"
                                            value="{{ $astrologer['learnAstrology'] }}">

                                    </div>

                                </div>

                            </div> --}}



                            {{-- <div class="intro-y col-span-6 md:col-span-6">

                                <div class="input mt-3">

                                    <div>

                                        <label for="regular-form-1" class="form-label">Instagram profile link</label>

                                        <input onkeypress="return validateJavascript(event);" type="text"
                                            name="instaProfileLink" id="instaProfileLink" class="form-control"
                                            placeholder="Please let us know your Instagram profile"
                                            value="{{ $astrologer['instaProfileLink'] }}">

                                    </div>

                                </div>

                            </div>

                            <div class="intro-y col-span-6 md:col-span-6">

                                <div class="input mt-3">

                                    <div>

                                        <label for="regular-form-1" class="form-label">Facebook profile link</label>

                                        <input onkeypress="return validateJavascript(event);" type="text"
                                            name="instaProfileLink" id="facebookProfileLink" class="form-control"
                                            placeholder="Please let us know your Facebook profile"
                                            value="{{ $astrologer['facebookProfileLink'] }}">

                                    </div>

                                </div>

                            </div>

                            <div class="intro-y col-span-6 md:col-span-6">

                                <div class="input mt-3">

                                    <div>

                                        <label for="regular-form-1" class="form-label">LinkedIn profile link</label>

                                        <input onkeypress="return validateJavascript(event);" type="text"
                                            name="linkedInProfileLink" id="linkedInProfileLink" class="form-control"
                                            placeholder="Please let us know your LinkedIn profile"
                                            value="{{ $astrologer['linkedInProfileLink'] }}">

                                    </div>

                                </div>

                            </div>

                            <div class="intro-y col-span-6 md:col-span-6">

                                <div class="input mt-3">

                                    <div>

                                        <label for="regular-form-1" class="form-label">Youtube profile link</label>

                                        <input onkeypress="return validateJavascript(event);" type="text"
                                            name="youtubeChannelLink" id="youtubeChannelLink" class="form-control"
                                            placeholder="Please let us know your Youtube profile"
                                            value="{{ $astrologer['youtubeChannelLink'] }}">

                                    </div>

                                </div>

                            </div>

                            <div class="intro-y col-span-6 md:col-span-6">

                                <div class="input mt-3">

                                    <div>

                                        <label for="regular-form-1" class="form-label">Website profile link</label>

                                        <input onkeypress="return validateJavascript(event);" type="text"
                                            name="websiteProfileLink" id="websiteProfileLink" class="form-control"
                                            placeholder="Please let us know your Website profile"
                                            value="{{ $astrologer['websiteProfileLink'] }}">

                                    </div>

                                </div>

                            </div>

                            <div class="intro-y col-span-6 md:col-span-6">

                                <div class="flex flex-col sm:flex-row mt-2">

                                    <label for="regular-form-1" class="form-label">Did anybody refer you to

                                        AsthaJyotish?</label>

                                    <div class="form-check mr-2">

                                        <input class="form-check-input" type="radio" name="isAnyBodyRefer" value=1
                                            {{ $astrologer['isAnyBodyRefer'] ? 'checked' : '' }}>

                                        <label class="form-check-label" for="radio-switch-4">Yes</label>

                                    </div>

                                    <div class="form-check mr-2 mt-2 sm:mt-0">

                                        <input class="form-check-input" type="radio" name="isAnyBodyRefer" value=0
                                            {{ !$astrologer['isAnyBodyRefer'] ? 'checked' : '' }}>

                                        <label class="form-check-label" for="radio-switch-5">No</label>

                                    </div>

                                </div>

                            </div>

                            <div class="intro-y col-span-6 md:col-span-6">

                                @if ($astrologer['isAnyBodyRefer'])
                                    <div class="input mt-3">

                                        <div>

                                            <label for="regular-form-1" class="form-label">Name of the person who referred

                                                you?</label>

                                            <input onkeypress="return validateJavascript(event);" type="text"
                                                name="referedPerson" id="referedPerson" class="form-control"
                                                placeholder="Please let us know your Website profile"
                                                value="{{ $astrologer['referedPerson'] }}">

                                        </div>

                                    </div>
                                @endif

                            </div>

                            <div class="intro-y col-span-6 md:col-span-6">

                                <div class="input mt-3">

                                    <div>

                                        <label for="regular-form-1" class="form-label">Minimum Earning Expection from

                                            AsthaJyotish</label>

                                        <input onkeypress="return validateJavascript(event);" type="text"
                                            name="minimumEarning" id="minimumEarning" class="form-control"
                                            placeholder="Minimum Earning" value="{{ $astrologer['minimumEarning'] }}">

                                    </div>

                                </div>

                            </div>

                            <div class="intro-y col-span-6 md:col-span-6">

                                <div class="input mt-3">

                                    <div>

                                        <label for="regular-form-1" class="form-label">Maximum Earning Expection from

                                            AsthaJyotish</label>

                                        <input onkeypress="return validateJavascript(event);" type="text"
                                            name="maximumEarning" id="maximumEarning" class="form-control"
                                            placeholder="Maximum Earning" value="{{ $astrologer['maximumEarning'] }}">

                                    </div>

                                </div>

                            </div> --}}

                            <div class="intro-y col-span-6 md:col-span-6">

                                <div class="input mt-3">

                                    <div>

                                        <label for="regular-form-1" class="form-label">Long bio</label>

                                        <textarea onkeypress="return validateJavascript(event);" name="loginBio" id="loginBio" class="form-control"
                                            placeholder="Describe bio">{{ $astrologer['loginBio'] }}</textarea>

                                    </div>

                                </div>

                            </div>

                            {{-- <div class="intro-y col-span-6 md:col-span-6">

                                <div class="preview mt-3">

                                    <label for="title" class="form-label">Number of the foreign countries you

                                        lived/travelled to?</label>

                                    <select data-minimum-results-for-search="Infinity" name="NoofforeignCountriesTravel"
                                        class="form-control select2" data-placeholder="Travel Countries">

                                        @foreach ($countryTravel as $travel)
                                            <option value={{ $travel->NoOfCountriesTravell }}>

                                                {{ $travel->NoOfCountriesTravell }}</option>
                                        @endforeach

                                    </select>

                                </div>

                            </div>

                            <div class="intro-y col-span-6 md:col-span-6">

                                <div class="preview mt-3">

                                    <label for="title" class="form-label">Are you currently working a fulltime

                                        job?</label>

                                    <select data-minimum-results-for-search="Infinity" id="currentlyworkingfulltimejob"
                                        name="currentlyworkingfulltimejob" class="form-control select2"
                                        data-placeholder="Currently Working">

                                        @foreach ($jobs as $working)
                                            <option value='{{ $working->workName }}'>

                                                {{ $working->workName }}</option>
                                        @endforeach

                                    </select>

                                </div>

                            </div>

                            <div class="intro-y col-span-6 md:col-span-6">

                                <div class="input mt-3">

                                    <div>

                                        <label for="regular-form-1" class="form-label">What are some good qualities of

                                            perfect

                                            astrologer?</label>

                                        <textarea onkeypress="return validateJavascript(event);" name="goodQuality" id="goodQuality" class="form-control"
                                            placeholder="Describe Here">{{ $astrologer['goodQuality'] }}</textarea>

                                    </div>

                                </div>

                            </div>

                            <div class="intro-y col-span-6 md:col-span-6">

                                <div class="input mt-3">

                                    <div>

                                        <label for="regular-form-1" class="form-label">What was the biggest challenge you

                                            faced and how did you overcome it?</label>

                                        <textarea onkeypress="return validateJavascript(event);" name="biggestChallenge" id="biggestChallenge"
                                            class="form-control" placeholder="Describe Here">{{ $astrologer['biggestChallenge'] }}</textarea>

                                    </div>

                                </div>

                            </div>

                            <div class="intro-y col-span-6 md:col-span-6">

                                <div class="input mt-3">

                                    <div>

                                        <label for="regular-form-1" class="form-label">A customer is asking the same

                                            question

                                            repeatedly: what will you do?</label>

                                        <textarea onkeypress="return validateJavascript(event);" name="whatwillDo" id="whatwillDo" class="form-control"
                                            placeholder="Describe Here">{{ $astrologer['whatwillDo'] }}</textarea>

                                    </div>

                                </div>

                            </div> --}}

                        </div>

                    </div>

                    <div id="example-tab-4" class="tab-pane leading-relaxed" role="tabpanel"
                        aria-labelledby="example-4-tab">

                        @foreach ($astrologer['astrologerAvailability'] as $availability)
                            <div class="input mt-2 sm:mt-0">

                                <h4 class="font-medium text-lg mt-3 d-inline">{{ $availability['day'] }}</h4>

                                <button style="padding: 3px 6px;"class="btn btn-sm btn-primary add-field d-inline"
                                    type="button" onclick="addField('{{ $availability['day'] }}')">+</button>

                                <div class="intro-y grid grid-cols-12 gap-6" id="astrologerfield">

                                    @foreach ($availability['time'] as $timeIndex => $time)
                                        <div
                                            class="{{ $availability['day'] }}_fromTime{{ $timeIndex }} intro-y col-span-6 md:col-span-6">

                                            <label id="input-group"
                                                class="astrologerAvailability_{{ $availability['day'] }}_time{{ $timeIndex }}_fromTime form-label">FromTime

                                                <button
                                                    style="padding: 2px 7px;

                                                border-radius: 50%"
                                                    class="btn btn-sm btn-primary add-field d-inline" type="button"
                                                    onclick="removeField('{{ $availability['day'] }}',{{ $timeIndex }})">-</button></label>

                                            <input type="hidden" class="form-control"
                                                id="astrologerAvailability[{{ $availability['day'] }}_{{ $timeIndex }}][day]"
                                                placeholder="FromTime"
                                                name="astrologerAvailability[{{ $availability['day'] }}_{{ $timeIndex }}][day]"
                                                aria-describedby="input-group-4" value="{{ $availability['day'] }}">

                                            <input type="time" class="form-control" placeholder="FromTime"
                                                name="astrologerAvailability[{{ $availability['day'] }}_{{ $timeIndex }}][time][{{ $timeIndex }}][fromTime]"
                                                id="astrologerAvailability_{{ $availability['day'] }}_time{{ $timeIndex }}_fromTime"
                                                aria-describedby="input-group-4" value="{{ $time['fromTime'] }}">

                                        </div>

                                        <div
                                            class="{{ $availability['day'] }}_toTime{{ $loop->index }} intro-y col-span-6 md:col-span-6">

                                            <label id="input-group"
                                                class="astrologerAvailability_{{ $availability['day'] }}_time{{ $loop->index }}_toTime form-label">ToTime</label>

                                            <input type="time" class="form-control" placeholder="FromTime"
                                                name="astrologerAvailability[{{ $availability['day'] }}_{{ $timeIndex }}][time][{{ $loop->index }}][toTime]"
                                                id="astrologerAvailability_{{ $availability['day'] }}_time{{ $loop->index }}_toTime"
                                                aria-describedby="input-group-4" value="{{ $time['toTime'] }}">

                                        </div>
                                    @endforeach

                                </div>

                            </div>
                        @endforeach

                    </div>

                </div>

            </div>

        </div>



        </div>

    </form>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script type="text/javascript"></script>

    <script>
        var spinner = $('.loader');

        $(window).on('load', function() {

            $('.loader').hide();

        });

        var category = {{ Js::from($astrologer['astrologerCategoryId']) }};

        var primarySkill = {{ Js::from($astrologer['primarySkill']) }};

        var allSkill = {{ Js::from($astrologer['allSkill']) }};

        var language = {{ Js::from($astrologer['languageKnown']) }};

        var mainSourceOfBusiness = {{ Js::from($astrologer['mainSourceOfBusiness']) }};

        var degree = {{ Js::from($astrologer['degree']) }};

        var highestQualification = {{ Js::from($astrologer['highestQualification']) }};

        var currentlyworkingfulltimejob = {{ Js::from($astrologer['currentlyworkingfulltimejob']) }};

        category = category.split(',')

        primarySkill = primarySkill.split(',')

        //allSkill = allSkill.split(',')

        languageKnown = language.split(',')

        $('.category').val(category).trigger('change');

        $('.primary').val(primarySkill).trigger('change');

        $('.all').val(allSkill).trigger('change');

        $('.language').val(languageKnown).trigger('change');

        $('#mainSourceOfBusiness').val(mainSourceOfBusiness).trigger('change');

        $('#degree').val(degree).trigger('change');

        $('#highestQualification').val(highestQualification).trigger('change');

        $('#currentlyworkingfulltimejob').val(currentlyworkingfulltimejob).trigger('change');
    </script>

    <script>
        $(document).ready(function() {

            jQuery('.select2').select2({

                allowClear: true,

                tags: true,

                tokenSeparators: [',', ' ']

            });

        });

        jQuery(function() {

            jQuery('#editastrologer').submit(function(e) {

                e.preventDefault();

                spinner.show();

                var data = new FormData(this);

                jQuery.ajax({

                    type: 'POST',

                    url: "{{ route('editAstrologerApi') }}",

                    data: data,

                    dataType: 'JSON',

                    processData: false,

                    contentType: false,

                    success: function(data) {

                        if (jQuery.isEmptyObject(data.error)) {

                            spinner.hide();

                            location.href = "/admin/advisors";

                        } else {

                            printErrorMsg(data.error);

                            spinner.hide();

                        }

                    }

                });

            });

        });



        function printErrorMsg(msg) {

        }



        function preview() {

            document.getElementById("thumb").style.display = "block";

            thumb.src = URL.createObjectURL(event.target.files[0]);

        }

        var times = {{ Js::from($astrologer['astrologerAvailability']) }};

        var dayTime = [];





        function addField($day) {

            if (times && times.length > 0) {

                dayTime = times.find(c => c.day == $day)['time'];

                dayTime.push({

                    fromTime: null,

                    toTime: null

                })

            }

            html = '';

            htmlto = '';

            html +=

                " <div class=" + $day + "_fromTime" + (dayTime.length - 1) +

                " intro-y col-span-6 md:col-span-6 mt-5'> <label id='input-group' class='mt-5 astrologerAvailability_" +

                $day +

                "_time" + (dayTime.length - 1) +

                "_fromTime form-label'>FromTime<button style='padding: 2px 7px;border-radius: 50%'class='btn btn-sm btn-primary add-field d-inline' type='button' onclick=removeField('" +

                $day + "'," + (dayTime.length - 1) +

                ")>-</button></label> <input id='astrologerAvailability[" + $day + "_" + (dayTime.length - 1) +

                "][day]' type='hidden' class='form-control' placeholder='FromTime' name='astrologerAvailability[" +

                $day + "_" + (dayTime.length - 1) + "][day]' aria-describedby='input-group-4' value=" + $day +

                "><input type = 'time' class = 'form-control' placeholder = 'FromTime' id='astrologerAvailability_" +

                $day + "_time" + (dayTime.length - 1) + "_fromTime' name = 'astrologerAvailability[" +

                $day + "_" + (dayTime.length - 1) + "][time][" + (dayTime.length - 1) +

                "][fromTime]' aria-describedby = 'input-group-4'></div>";

            htmlto +=

                ' <div class=' + $day + '_toTime' + (dayTime.length - 1) +

                ' intro-y col-span-6 md:col-span-6 mt-5"><label id="input-group" class="mt-5 form-label astrologerAvailability_' +

                $day + '_time' + (dayTime.length - 1) +

                '_toTime">ToTime</label><input type = "time" class = "form-control"  placeholder = "ToTime" name = "astrologerAvailability[' +

                $day + '_' + (dayTime.length - 1) + '][time][' + (dayTime.length - 1) +

                '][toTime]" id="astrologerAvailability_' +

                $day + '_time' + (dayTime.length - 1) + '_toTime"></div>'

            $('.' + $day + '_fromTime' + (dayTime.length - 2)).append(

                html

            );

            $('.' + $day + '_toTime' + (dayTime.length - 2)).append(

                htmlto

            );

        }



        function removeField($day, $index) {

            if (dayTime.length == 0)

                dayTime = times.filter(c => c.day == $day)[0]['time'];

            dayTime.splice($index, 1);



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
@endsection
