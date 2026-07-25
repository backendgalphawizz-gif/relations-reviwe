@extends('../layout/' . $layout)

@section('subhead')
@endsection

@section('subcontent')
    @php
        $currency = DB::table('systemflag')->where('name', 'Currency')->select('value')->first();
    @endphp
    <div class="loader"></div>
    <div class="intro-y flex items-center mt-8">
        <h2 class="text-lg font-medium mr-auto">Advisor Details</h2>
    </div>
    <!-- BEGIN: Profile Info -->

    @foreach ($result as $astrologerDetail)
        <div class="intro-y box  pt-5 mt-5">

            <div class="flex flex-col lg:flex-row border-b border-slate-200/60 dark:border-darkmode-400 pb-5 px-5">
                <div class="flex flex-1 px-5 items-center justify-center lg:justify-start">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 flex-none lg:w-32 lg:h-32 image-fit relative">
                        @if (Request::segment(2))
                            <img class="rounded-full" src="/{{ $astrologerDetail->profileImage }}"
                                onerror="this.onerror=null;this.src='/build/assets/images/person.png';"
                                alt="Astrologer image" />
                        @else
                            <img class="rounded-full" src="/{{ $astrologerDetail->profileImage }}"
                                onerror="this.onerror=null;this.src='/build/assets/images/person.png';"
                                alt="Astrologer image" />
                        @endif
                    </div>
                    <div class="ml-5">
                        <div class="w-24 sm:w-40 truncate sm:whitespace-normal font-medium text-lg">
                            {{ $astrologerDetail->name ? $astrologerDetail->name : '--' }}</div>
                        <div class="text-slate-500">
                            {{ $astrologerDetail->contactNo ? $astrologerDetail->contactNo : '--' }}</div>
                    </div>
                </div>
                <div
                    class="mt-6 lg:mt-0 flex-1 px-5 border-l border-r border-slate-200/60 dark:border-darkmode-400 border-t lg:border-t-0 pt-5 lg:pt-0">
                    <div class="font-medium text-center lg:text-left lg:mt-3">Contact Details</div>
                    <div class="flex flex-col justify-center items-center lg:items-start mt-4">
                        <div class="truncate sm:whitespace-normal flex items-center">
                            <i data-lucide="mail" class="w-4 h-4 mr-2"></i>
                            {{ $astrologerDetail->email ? $astrologerDetail->email : '--' }}
                        </div>
                    </div>
                </div>
                <div
                    class="mt-6 lg:mt-0 flex-1 px-5 border-t lg:border-0 border-slate-200/60 dark:border-darkmode-400 pt-5 lg:pt-0">
                    <div class="font-medium text-center lg:text-left lg:mt-3">Details</div>
                    {{-- <div class="flex items-center justify-center lg:justify-start mt-2">
                        <div class="flex">
                            Total Order: <span
                                class="ml-3 font-medium text-success">{{ $astrologerDetail->totalOrder ? $astrologerDetail->totalOrder : '--' }}
                                Order</span>
                        </div>
                    </div> --}}
                    {{-- <div class="flex items-center justify-center lg:justify-start">
                        <div class="flex mt-2">
                            Followers : <span
                                class="ml-3 font-medium text-danger">{{ $astrologerDetail->totalFollower ? $astrologerDetail->totalFollower : '--' }}
                                Follower</span>
                        </div>
                    </div> --}}
                    {{-- <div class="flex items-center justify-center lg:justify-start">
                        <div class="flex mt-2">
                            Total Chat Min: <span
                                class="ml-3 font-medium text-warning">{{ $astrologerDetail->chatMin ? $astrologerDetail->chatMin : '--' }}
                                Minutes</span>
                        </div>
                    </div> --}}
                    <div class="flex items-center justify-center lg:justify-start">
                        <div class="flex mt-2">
                            Total Call Min: <span
                                class="ml-3 font-medium text-warning">{{ $astrologerDetail->callMin ? $astrologerDetail->callMin : '--' }}
                                Minutes</span>
                        </div>
                    </div>
                </div>
            </div>
            <div id="link-tab" class="p-3">

                <ul class="nav nav-link-tabs" role="tablist">
                    <li id="example-1-tab" class="nav-item flex-1" role="presentation">
                        <button class="nav-link w-full py-2 active" data-tw-toggle="pill" data-tw-target="#example-tab-1"
                            type="button" role="tab" aria-controls="example-tab-1" aria-selected="true">
                            Basic Detail
                        </button>
                    </li>
                    {{-- <li id="example-2-tab" class="nav-item flex-1" role="presentation">
                        <button class="nav-link w-full py-2" data-tw-toggle="pill" data-tw-target="#example-tab-2"
                            type="button" role="tab" aria-controls="example-tab-2" aria-selected="false">
                            Wallet
                        </button>
                    </li> --}}
                    {{-- <li id="example-3-tab" class="nav-item flex-1" role="presentation">
                        <button class="nav-link w-full py-2" data-tw-toggle="pill" data-tw-target="#example-tab-3"
                            type="button" role="tab" aria-controls="example-tab-3" aria-selected="false">
                            Chat History
                        </button>
                    </li> --}}
                    <li id="example-4-tab" class="nav-item flex-1" role="presentation">
                        <button class="nav-link w-full py-2" data-tw-toggle="pill" data-tw-target="#example-tab-4"
                            type="button" role="tab" aria-controls="example-tab-4" aria-selected="false">
                            Call History
                        </button>
                    </li>
                    {{-- <li id="example-5-tab" class="nav-item flex-1" role="presentation">
                        <button class="nav-link w-full py-2" data-tw-toggle="pill" data-tw-target="#example-tab-5"
                            type="button" role="tab" aria-controls="example-tab-5" aria-selected="false">
                            Report
                        </button>
                    </li> --}}
                    {{-- <li id="example-6-tab" class="nav-item flex-1" role="presentation">
                        <button class="nav-link w-full py-2" data-tw-toggle="pill" data-tw-target="#example-tab-6"
                            type="button" role="tab" aria-controls="example-tab-6" aria-selected="false">
                            Followers List
                        </button>
                    </li> --}}
                    {{-- <li id="example-7-tab" class="nav-item flex-1" role="presentation">
                        <button class="nav-link w-full py-2" data-tw-toggle="pill" data-tw-target="#example-tab-7"
                            type="button" role="tab" aria-controls="example-tab-7" aria-selected="false">
                            Notification List
                        </button>
                    </li> --}}
                    {{-- <li id="example-8-tab" class="nav-item flex-1" role="presentation">
                        <button class="nav-link w-full py-2" data-tw-toggle="pill" data-tw-target="#example-tab-8"
                            type="button" role="tab" aria-controls="example-tab-8" aria-selected="false">
                            Gift List
                        </button>
                    </li> --}}
                </ul>
                <div class="tab-content astrologer-tab-content mt-5">
                    <div id="example-tab-1" class="tab-pane leading-relaxed active" role="tabpanel"
                        aria-labelledby="example-1-tab">


                        <div class="grid grid-cols-12 gap-6">
                            <div class="col-span-12 2xl:col-span-12">
                                <div class="grid grid-cols-12 gap-6">
                                    <div class="col-span-12 md:col-span-6">
                                        <div class="intro-y box p-5 mt-12 sm:mt-5" style="height:100%">
                                            <div
                                                class="flex text-slate-500 border-b border-slate-200 dark:border-darkmode-300 border-dashed pb-3 mb-3">
                                                <div class="text-success" style="font-weight: 700; font-size: 17px">
                                                    Skill Details</div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div class="flex items-center">
                                                    <div>Gender</div>
                                                    <div class="text-danger flex text-xs font-medium tooltip cursor-pointer ml-2"
                                                        title="49% Higher than last month">
                                                    </div>
                                                </div>
                                                <div class="ml-auto">{{ $astrologerDetail->gender }}</div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div class="flex items-center">
                                                    <div>Date of Birth</div>
                                                </div>
                                                <div class="ml-auto">
                                                    {{ date('d-m-Y', strtotime($astrologerDetail->birthDate)) ? date('d-m-Y', strtotime($astrologerDetail->birthDate)) : '--' }}
                                                </div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div class="flex items-center">
                                                    <div>Advisor Category</div>
                                                    <div class="text-success flex text-xs font-medium tooltip cursor-pointer ml-2"
                                                        title="49% Higher than last month">
                                                    </div>
                                                </div>

                                                <div class="ml-auto">
                                                    @foreach ($astrologerDetail->astrologerCategoryId as $astroCat)
                                                        <span> {{ $astroCat->name }},</span>
                                                    @endforeach
                                                </div>

                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div class="flex items-center">
                                                    <div>Primary Skill</div>
                                                </div>
                                                <div class="ml-auto">
                                                    @foreach ($astrologerDetail->primarySkill as $primarySkill)
                                                        <span> {{ $primarySkill->name }},</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                {{-- <div class="flex items-center">
                                                    <div>All Skill</div>
                                                    <div class="text-danger flex text-xs font-medium tooltip cursor-pointer ml-2"
                                                        title="49% Higher than last month">

                                                    </div>
                                                </div> --}}
                                                <div class="ml-auto">
                                                    @foreach ($astrologerDetail->allSkill as $allSkill)
                                                        <span> {{ $allSkill->name }},</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div class="flex items-center">
                                                    <div>Language</div>
                                                </div>
                                                <div class="ml-auto">
                                                    @foreach ($astrologerDetail->languageKnown as $language)
                                                        <span> {{ $language->languageName }},</span>
                                                    @endforeach
                                                </div>
                                            </div>
                                            {{-- <div class="flex items-center mb-5">
                                                <div class="flex items-center">
                                                    <div>Your charges(per min)</div>
                                                </div>

                                                <div class="ml-auto">
                                                    {{ $currency->value }}{{ $astrologerDetail->charge }}
                                                </div>
                                            </div> --}}
                                            {{-- <div class="flex items-center mb-5">
                                                <div class="flex items-center">
                                                    <div>Video charges</div>
                                                </div>
                                                <div class="ml-auto">
                                                    {{ $currency->value }}{{ $astrologerDetail->videoCallRate ? $astrologerDetail->videoCallRate : '0' }}
                                                </div>
                                            </div> --}}
                                            {{-- <div class="flex items-center mb-5">
                                                <div class="flex items-center">
                                                    <div>Report charges</div>
                                                </div>
                                                <div class="ml-auto">
                                                    {{ $currency->value }}{{ $astrologerDetail->reportRate ? $astrologerDetail->reportRate : '0' }}
                                                </div>
                                            </div> --}}
                                            <div class="flex items-center mb-5">
                                                <div class="flex items-center">
                                                    <div>Expirence in year</div>
                                                </div>
                                                <div class="ml-auto">
                                                    {{ $astrologerDetail->experienceInYears ? $astrologerDetail->experienceInYears : '0' }}
                                                </div>
                                            </div>
                                            {{-- <div class="flex items-center mb-5">
                                                <div class="flex items-center">
                                                    <div>How many hours you can contribute daily</div>
                                                </div>
                                                <div class="ml-auto">
                                                    {{ $astrologerDetail->dailyContribution ? $astrologerDetail->dailyContribution : '0' }}
                                                </div>
                                            </div> --}}
                                            {{-- <div class="flex items-center mb-5">
                                                <div class="flex items-center">
                                                    <div>Where did you hear about Relationship Revive</div>
                                                </div>
                                                <div class="ml-auto">
                                                    {{ $astrologerDetail->hearAboutAstroguru }}
                                                </div>
                                            </div> --}}
                                        </div>
                                    </div>
                                    <div class="col-span-12 md:col-span-6">
                                        <div class="intro-y box p-5 mt-12 sm:mt-5" style="height:100%">
                                            <div
                                                class="flex text-slate-500 border-b border-slate-200 dark:border-darkmode-300 border-dashed pb-3 mb-3">
                                                <div class="text-success" style="font-weight: 700; font-size: 17px">
                                                    Availability</div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div class="items-center">
                                                    @foreach ($astrologerDetail->astrologerAvailability as $availability)
                                                        <div class="text-x" style="font-weight: 600; font-size: 15px">
                                                            <div class="row"> {{ $availability->day }}</div>
                                                            @foreach ($availability->time as $time)
                                                                <div class="text-xs font-medium tooltip cursor-pointer mb-4"
                                                                    style="display: inline-block">
                                                                    @if ($time->fromTime != null)
                                                                        <div class="box p-2"
                                                                            style="background-color: #e0e8f1">
                                                                            {{ $time->fromTime }} -
                                                                            {{ $time->toTime }} </div>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endforeach
                                                    {{-- <div class="flex items-center mb-5">
                                                        <div class="text-x" style="font-weight: 600; font-size: 15px">
                                                            <div>Chat Availability</div>
                                                        </div>
                                                        <div class="ml-auto">
                                                            {{ $astrologerDetail->chatStatus ? $astrologerDetail->chatStatus : '--' }}
                                                        </div>
                                                    </div> --}}
                                                    <div class="flex items-center mb-5">
                                                        <div class="text-x" style="font-weight: 600; font-size: 15px">
                                                            <div>Call Availability</div>
                                                        </div>
                                                        <div class="ml-auto">
                                                            {{ $astrologerDetail->callStatus ? $astrologerDetail->callStatus : '--' }}
                                                        </div>
                                                    </div>
                                                    {{-- <div class="flex items-center mb-5">
                                                        <div class="text-x" style="font-weight: 600; font-size: 15px">
                                                            <div>Wait Time</div>
                                                        </div>
                                                        <div class="ml-auto">
                                                            {{ $astrologerDetail->chatWaitTime ? $astrologerDetail->chatWaitTime : '--' }}
                                                        </div>
                                                    </div> --}}
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    {{-- <div class="col-span-12 md:col-span-6 ">
                                        <div class="intro-y box p-5 mt-12 sm:mt-5" style="height:100%">
                                            <div
                                                class="flex text-slate-500 border-b border-slate-200 dark:border-darkmode-300 border-dashed pb-3 mb-3">
                                                <div class="text-success" style="font-weight: 700; font-size: 17px">
                                                    Other Details</div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div>Why do you think we should onboard you?</div>
                                                <div class="ml-auto">{{ $astrologerDetail->whyOnBoard }}</div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div>Suitable time for interview</div>
                                                <div class="ml-auto">{{ $astrologerDetail->interviewSuitableTime }}
                                                </div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div>Currently Live City</div>
                                                <div class="ml-auto">{{ $astrologerDetail->currentCity }}</div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div>Main source of bussiness</div>
                                                <div class="ml-auto">{{ $astrologerDetail->mainSourceOfBusiness }}</div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div>Highest Qualification</div>
                                                <div class="ml-auto">{{ $astrologerDetail->highestQualification }}</div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div>Degree/Diploma</div>
                                                <div class="ml-auto">
                                                    {{ $astrologerDetail->degree ? $astrologerDetail->degree : '--' }}
                                                </div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div>Expected Minimum Earning</div>
                                                <div class="ml-auto">{{ $astrologerDetail->minimumEarning }}</div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div>Expected Maximum Earning</div>
                                                <div class="ml-auto">{{ $astrologerDetail->maximumEarning }}</div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div>Login Bio</div>
                                                <div class="ml-auto">{{ $astrologerDetail->maximumEarning }}</div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div>Number of Foreign Country you Lived</div>
                                                <div class="ml-auto">{{ $astrologerDetail->NoofforeignCountriesTravel }}
                                                </div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div>Currently Working</div>
                                                <div class="ml-auto">
                                                    {{ $astrologerDetail->currentlyworkingfulltimejob }}
                                                </div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div>Good Quality</div>
                                                <div class="ml-auto">{{ $astrologerDetail->goodQuality }}
                                                </div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div>Biggest challenge you faced</div>
                                                <div class="ml-auto">Biggest challenge you faced
                                                </div>
                                            </div>
                                            <div class="flex items-center mb-5">
                                                <div>A customer asking same question repatedly: What will you do</div>
                                                <div class="ml-auto">
                                                    {{ $astrologerDetail->whatwillDo }}
                                                </div>
                                            </div>
                                        </div>
                                    </div> --}}
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-12 gap-6 mt-5">
                            <div class="col-span-12 2xl:col-span-12">
                                <div class="grid grid-cols-12 gap-6">

                                </div>
                            </div>
                        </div>

                    </div>
                    <div id="example-tab-2" class="tab-pane leading-relaxed" role="tabpanel"
                        aria-labelledby="example-2-tab">

                        <div class="intro-y">
                            @if (count($astrologerDetail->wallet) > 0)
                                @foreach ($astrologerDetail->wallet as $wallet)
                                    <div class="intro-y">
                                        <div class="box px-4 py-4 mb-3 flex items-center">
                                            <div class="ml-4 mr-auto">
                                                <div class="font-medium">
                                                    @if ($wallet->transactionType != 'Gift')
                                                        {{ $wallet->transactionType }} with
                                                        {{ $wallet->name }} for
                                                        {{ $wallet->totalMin }}
                                                        minutes
                                                    @else
                                                        Received Gift From {{ $wallet->name }}
                                                    @endif
                                                </div>
                                                <div class="text-slate-500 text-xs mt-0.5">
                                                    {{ date('j-F-Y H:i a', strtotime($wallet->created_at)) }}
                                                </div>
                                            </div>
                                            <div class="flex items-center">

                                                <div
                                                    class="ml-4 mr-2 {{ $wallet->amount ? 'text-success' : 'text-danger' }}">
                                                    <div class="font-medium">
                                                        {{ $currency->value }}{{ $wallet->amount }}</div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center w-30">
                                    <h5>No Wallet Transaction Found</h5>
                                </div>
                            @endif

                        </div>
                    </div>
                    <div id="example-tab-3" class="tab-pane leading-relaxed" role="tabpanel"
                        aria-labelledby="example-3-tab">
                        @if (count($astrologerDetail->chatHistory) > 0)
                            <div class="grid grid-cols-12 gap-6 mt-5">
                                @foreach ($astrologerDetail->chatHistory as $chatHistory)
                                    <div class="intro-y col-span-12 md:col-span-6 lg:col-span-4 xl:col-span-3">
                                        <div class="box">
                                            <div class="p-5">
                                                <div class="image-fit" style="height:150px;width:150px">
                                                    <img class="rounded-full" style="width: 100%; height: 100%;"
                                                        src="/{{ $chatHistory->profile }}"
                                                        onerror="this.onerror=null;this.src='/build/assets/images/person.png';"
                                                        alt="Astrologer image" />
                                                </div>
                                                <div class="font-medium text-center lg:text-left lg:mt-3">
                                                    {{ $chatHistory->name }}</div>
                                                <div class="text-slate-600 dark:text-slate-500 mt-2">
                                                    <div class="flex items-center">
                                                        {{ $chatHistory->created_at }}
                                                    </div>
                                                    <div class="flex items-center mt-2">
                                                        <i data-lucide="phone" class="w-4 h-4 mr-2"></i>
                                                        {{ $chatHistory->contactNo }}
                                                    </div>
                                                    <div
                                                        class="flex items-center mt-2 {{ $chatHistory->chatStatus == 'Pending' ? 'text-success' : 'text-danger' }}">
                                                        <i data-lucide="check-square" class="w-4 h-4 mr-2"></i>
                                                        {{ $chatHistory->chatStatus }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center w-30">
                                <h5>No Chat Request Found</h5>
                            </div>
                        @endif
                    </div>
                    <div id="example-tab-4" class="tab-pane leading-relaxed" role="tabpanel"
                        aria-labelledby="example-4-tab">
                        @if (count($astrologerDetail->callHistory) > 0)
                            <div class="grid grid-cols-12 gap-6 mt-5">

                                @foreach ($astrologerDetail->callHistory as $callHistory)
                                    <div class="intro-y col-span-12 md:col-span-6 lg:col-span-4 xl:col-span-3">
                                        <div class="box">
                                            <div class="p-5">
                                                <div class="h-20 2xl:h-56 image-fit" style="height:150px;width:150px">
                                                    <img class="rounded-full" style="width: 100%; height: 100%;"
                                                        src="/{{ $callHistory->profile }}"
                                                        onerror="this.onerror=null;this.src='/build/assets/images/person.png';"
                                                        alt="Astrologer image" />
                                                </div>
                                                <div class="font-medium text-center lg:text-left lg:mt-3">
                                                    {{ $callHistory->name }}</div>
                                                <div class="text-slate-600 dark:text-slate-500 mt-2">
                                                    <div class="flex items-center">
                                                        {{ $callHistory->created_at }}
                                                    </div>
                                                    <div class="flex items-center mt-2">
                                                        <i data-lucide="phone" class="w-4 h-4 mr-2"></i>
                                                        {{ $callHistory->contactNo }}
                                                    </div>
                                                    <div
                                                        class="flex items-center mt-2 {{ $callHistory->callStatus == 'Accepted' ? 'text-success' : 'text-danger' }}">
                                                        <i data-lucide="check-square" class="w-4 h-4 mr-2"></i>
                                                        {{ $callHistory->callStatus }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center w-30">
                                <h5>No Call Request Found</h5>
                            </div>
                        @endif

                    </div>
                    <div id="example-tab-5" class="tab-pane leading-relaxed" role="tabpanel"
                        aria-labelledby="example-5-tab">
                        <div class="col-span-12 xl:col-span-4 mt-6">
                            <div class="mt-5">
                                @if (count($astrologerDetail->report) > 0)
                                    @foreach ($astrologerDetail->report as $report)
                                        <div class="intro-y">
                                            <div class="box px-4 py-4 mb-3 flex items-center">
                                                <div class="ml-4 mr-auto">
                                                    <div class="font-medium text-success">
                                                        {{ $report->firstName }} {{ $report->lastName }}
                                                    </div>
                                                    <div class="text-slate-500 text-x mt-0.5">
                                                        {{ date('j-F-Y H:i a', strtotime($report->created_at)) }}
                                                    </div>
                                                    <div class="text-slate-900 text-x mt-0.9">
                                                        {{ $report->reportType }}
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center w-30">
                                        <h5>No Report Found</h5>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div id="example-tab-6" class="tab-pane leading-relaxed" role="tabpanel"
                        aria-labelledby="example-6-tab">
                        <div class="grid grid-cols-12 gap-6 mt-5">

                            <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
                                @if (count($astrologerDetail->follower) > 0)
                                    <table class="table table-report -mt-2" aria-label="follower">
                                        <thead>
                                            <tr>
                                                <th class="whitespace-nowrap">#</th>
                                                <th class="whitespace-nowrap">PROFILE</th>
                                                <th class="whitespace-nowrap">NAME</th>
                                                <th class="text-center whitespace-nowrap">CONTACT NO</th>
                                                <th class="text-center whitespace-nowrap">DATE</th>
                                                <th class="text-center whitespace-nowrap">ACTION</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $no = 0;
                                            @endphp

                                            @foreach ($astrologerDetail->follower as $follower)
                                                <tr class="intro-x">
                                                    <td>{{ ++$no }} </td>
                                                    <td>
                                                        <div class="flex">
                                                            <div class="w-10 h-10 image-fit zoom-in">
                                                                <img class="rounded-full" src="/{{ $follower->profile }}"
                                                                    onerror="this.onerror=null;this.src='/build/assets/images/person.png';"
                                                                    alt="Astrologer image" />
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="font-medium whitespace-nowrap">
                                                            {{ $follower->userName }}
                                                        </div>
                                                    </td>
                                                    <td class="text-center">{{ $follower->contactNo }}</td>
                                                    <td class="text-center whitespace-nowrap">
                                                        {{ date('d-m-Y', strtotime($follower->created_at)) }}
                                                    </td>
                                                    <td class="table-report__action w-56">
                                                        <div class="flex justify-center items-center">
                                                            <a class="flex items-center mr-3 text-success"
                                                                href="/admin/customers/{{ $follower->userId }}">
                                                                <i data-lucide="eye" class="w-4 h-4 mr-1"></i>View
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach

                                        </tbody>
                                    </table>
                                @else
                                    <div class="text-center w-30">
                                        <h5>No Followers Found</h5>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div id="example-tab-7" class="tab-pane leading-relaxed" role="tabpanel"
                        aria-labelledby="example-7-tab">
                        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
                            @if (count($astrologerDetail->notification) > 0)
                                <table class="table table-report mt-2" aria-label="notification">
                                    <thead>
                                        <tr>
                                            <th class="whitespace-nowrap">#</th>
                                            <th class="whitespace-nowrap" style="text-align: center">TITLE</th>
                                            <th class="whitespace-nowrap" style="text-align: center">DESCRIPTION</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $no = 0;
                                        @endphp

                                        @foreach ($astrologerDetail->notification as $notification)
                                            <tr class="intro-x">
                                                <td>{{ ++$no }} </td>
                                                <td>
                                                    <div class="font-medium" style="text-align: center">
                                                        {{ $notification->title }}
                                                    </div>
                                                </td>
                                                <td class="text-center">{{ $notification->description }}</td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            @else
                                <div class="text-center w-30">
                                    <h5>No Notification List Found</h5>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div id="example-tab-8" class="tab-pane leading-relaxed" role="tabpanel"
                        aria-labelledby="example-8-tab">
                        <div class="intro-y col-span-12 overflow-auto lg:overflow-visible">
                            @if (count($astrologerDetail->gifts) > 0)
                                <table class="table table-report mt-2" aria-label="gift">
                                    <thead>
                                        <tr>
                                            <th class="whitespace-nowrap">#</th>
                                            <th class="whitespace-nowrap" style="text-align: center">Name</th>
                                            <th class="whitespace-nowrap" style="text-align: center">GIFT NAME</th>
                                            <th class="whitespace-nowrap" style="text-align: center">AMOUNT</th>
                                            <th class="whitespace-nowrap" style="text-align: center">DATE</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $no = 0;
                                        @endphp

                                        @foreach ($astrologerDetail->gifts as $gift)
                                            <tr class="intro-x">
                                                <td>{{ ++$no }} </td>
                                                <td>
                                                    <div class="font-medium" style="text-align: center">
                                                        {{ $gift->userName }}
                                                    </div>
                                                </td>
                                                <td class="text-center">{{ $gift->giftName }}</td>
                                                <td class="text-center">
                                                    {{ $currency->value }}{{ $gift->giftAmount ? $gift->giftAmount : 0 }}
                                                </td>
                                                <td class="text-center">
                                                    {{ date('d-m-Y', strtotime($gift->created_at)) }}</td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            @else
                                <div class="text-center w-30">
                                    <h5>No Gift List Found</h5>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>

        </div>
    @endforeach
@endsection
@section('script')
    <script type="text/javascript"></script>
    <script>
        $(window).on('load', function() {
            $('.loader').hide();
        })
    </script>
@endsection
