<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminModel\DegreeOrDiploma;
use App\Models\AdminModel\FulltimeJob;
use App\Models\AdminModel\HighestQualification;
use App\Models\AdminModel\Language;
use App\Models\AdminModel\MainSourceOfBusiness;
use App\Models\AdminModel\TravelCountry;
use App\Models\Astrologer;
use App\Models\AstrologerModel\AstrologerAvailability;
use App\Models\AstrologerModel\AstrologerCategory;
use App\Models\AstrologerModel\Skill;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PDF;
use Response;

define('LOGINPATH', '/admin/login');

class AstrologerController extends Controller
{
    //Get Customer
    public $user;
    public $limit = 15;
    public $paginationStart;
    public $path;

    public function getAstrologer(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $req->page ? $req->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $astrologers = Astrologer::query();
                $astrologers = $astrologers->where('isDelete', '=', false);
                $searchString = $req->searchString ? $req->searchString : null;
                if ($req->searchString) {
                    $astrologers = $astrologers->where(function ($q) use ($searchString) {
                        $q->where('astrologers.name', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('astrologers.contactNo', 'LIKE', '%' . $searchString . '%');
                    });
                }
                $astrologerCount = $astrologers->count();
                $astrologers = $astrologers->orderBy('id', 'DESC');
                $astrologers->skip($paginationStart);
                $astrologers->take($this->limit);

                $astrologers = $astrologers->get();

                if ($astrologers && count($astrologers) > 0) {
                    foreach ($astrologers as $astro) {
                        $review = DB::table('user_reviews')
                            ->where('astrologerId', '=', $astro->id)
                            ->get();
                        if ($review && count($review) > 0) {
                            $avgRating = 0;
                            foreach ($review as $re) {
                                $avgRating += $re->rating;
                            }
                            $avgRating = $avgRating / count($review);
                            $astro['rating'] = $avgRating;
                        }
                        $totalCall = DB::table('callrequest')
                            ->where('astrologerId', '=', $astro['id'])
                            ->count();
                        $astro['totalCallRequest'] = $totalCall;
                        $totalChat = DB::table('chatrequest')
                            ->where('astrologerId', '=', $astro['id'])
                            ->count();
                        $astro['totalChatRequest'] = $totalChat;
                    }
                }
                $totalPages = ceil($astrologerCount / $this->limit);
                $totalRecords = $astrologerCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords
                    ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view(
                    'pages.astrologer-list',
                    compact('astrologers', 'searchString', 'totalPages', 'totalRecords', 'start', 'end', 'page')
                );
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function deleteUser($id)
    {
        try {
            $this->path = env('API_URL');
            $response = Http::post($this->path . '/user/delete/' . $id);
            $response->getStatusCode();
            $response = $response->getBody();
            $responseData = json_decode($response, true);
            return dd($responseData);
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function verifiedAstrologerApi(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $eid = $request->filed_id;
                $astrologer = Astrologer::find($eid);
                $astrologer->isVerified = !$astrologer->isVerified;
                $astrologer->update();
                return redirect()->route('astrologers');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }
    public function astrologerDetail()
    {
        return view('pages.astrologer-detail');
    }

    public function astrologerDetailApi(Request $req, $id)
    {
        try {
            if (Auth::guard('web')->check()) {
                $astrologer = DB::table('astrologers')
                    ->where('id', '=', $req->id)
                    ->get();
                if ($astrologer) {
                    $astrologer[0]->allSkill = array_map('intval', explode(',', $astrologer[0]->allSkill));
                    $astrologer[0]->primarySkill = array_map('intval', explode(',', $astrologer[0]->primarySkill));
                    $astrologer[0]->languageKnown = array_map('intval', explode(',', $astrologer[0]->languageKnown));
                    $astrologer[0]->astrologerCategoryId = array_map('intval', explode(',', $astrologer[0]->astrologerCategoryId));
                    $allSkill = DB::table('skills')
                        ->whereIn('id', $astrologer[0]->allSkill)
                        ->select('name', 'id')
                        ->get();
                    $primarySkill = DB::table('skills')
                        ->whereIn('id', $astrologer[0]->primarySkill)
                        ->select('name', 'id')
                        ->get();
                    $languageKnown = DB::table('languages')
                        ->whereIn('id', $astrologer[0]->languageKnown)
                        ->select('languageName', 'id')
                        ->get();
                    $category = DB::table('astrologer_categories')
                        ->whereIn('id', $astrologer[0]->astrologerCategoryId)
                        ->select('name', 'id')
                        ->get();
                    $astrologer[0]->allSkill = $allSkill;
                    $astrologer[0]->primarySkill = $primarySkill;
                    $astrologer[0]->languageKnown = $languageKnown;
                    $astrologer[0]->astrologerCategoryId = $category;
                    $astrologerAvailability = DB::table('astrologer_availabilities')
                        ->where('astrologerId', '=', $req->id)
                        ->get();
                    if ($astrologerAvailability && count($astrologerAvailability) > 0) {
                        $day = [];
                        $working = [];
                        $day = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                        foreach ($day as $days) {
                            $day = array(
                                'day' => $days,
                            );
                            $currentday = $days;
                            $result = array_filter(json_decode($astrologerAvailability), function ($event) use ($currentday) {
                                return $event->day === $currentday;
                            });
                            $ti = [];

                            foreach ($result as $available) {
                                $time = array(
                                    'fromTime' => $available->fromTime,
                                    'toTime' => $available->toTime,
                                );
                                array_push($ti, $time);
                            }
                            $weekDay = array(
                                'day' => $days,
                                'time' => $ti,
                            );
                            array_push($working, $weekDay);
                        }
                        $astrologer[0]->astrologerAvailability = $working;
                    } else {
                        $astrologer[0]->astrologerAvailability = [];
                    }

                    $chatHistory = DB::Table('chatrequest')
                        ->join('users', 'users.id', '=', 'chatrequest.userId')
                        ->join('astrologers', 'astrologers.id', '=', 'chatrequest.astrologerId')
                        ->select('chatrequest.*', 'users.name', 'users.contactNo', 'users.profile', 'astrologers.name as astrologerName', 'astrologers.charge')
                        ->where('chatrequest.astrologerId', '=', $req->id)
                        ->orderBy('chatrequest.id', 'DESC')
                        ->get();
                    $callHistory = DB::Table('callrequest')
                        ->join('users', 'users.id', '=', 'callrequest.userId')
                        ->join('astrologers', 'astrologers.id', '=', 'callrequest.astrologerId')
                        ->select('callrequest.*', 'users.name', 'users.contactNo', 'users.profile', 'astrologers.name as astrologerName', 'astrologers.charge')
                        ->where('callrequest.astrologerId', '=', $req->id)
                        ->orderBy('callrequest.id', 'DESC')
                        ->get();

                    $wallet = DB::table('wallettransaction')
                        ->leftjoin('order_request', 'order_request.id', '=', 'wallettransaction.orderId')
                        ->leftjoin('users', 'users.id', '=', 'order_request.userId')
                        ->select('wallettransaction.*', 'users.name', 'order_request.totalMin')
                        ->where('wallettransaction.userId', '=', $astrologer[0]->userId)
                        ->orderBy('wallettransaction.id', 'DESC')
                        ->get();

                    $review = DB::table('user_reviews')
                        ->join('users as u', 'u.id', '=', 'user_reviews.userId')
                        ->where('astrologerId', '=', $req->id)
                        ->select('user_reviews.*', 'u.name as userName', 'u.profile')
                        ->orderBy('user_reviews.id', 'DESC')
                        ->get();

                    $reports = DB::table('user_reports')
                        ->join('users as u', 'u.id', '=', 'user_reports.userId')
                        ->join('report_types', 'report_types.id', '=', 'user_reports.reportType')
                        ->where('astrologerId', '=', $req->id)
                        ->select('user_reports.*', 'u.name as userName', 'u.profile', 'u.contactNo', 'report_types.reportImage', 'report_types.title as reportType')
                        ->orderBy('user_reports.id', 'DESC')
                        ->get();

                    $callMin = DB::table('callrequest')
                        ->where('astrologerId', '=', $req->id)
                        ->sum('totalMin');

                    $chatMin = DB::table('chatrequest')
                        ->where('astrologerId', '=', $req->id)
                        ->sum('totalMin');
                    $follower = DB::table('astrologer_followers')
                        ->join('users', 'users.id', '=', 'astrologer_followers.userId')
                        ->where('astrologerId', '=', $req->id)
                        ->select('astrologer_followers.*', 'users.name as userName', 'users.contactNo', 'users.profile', 'users.id as userId')
                        ->get();

                    $notification = DB::table('user_notifications')
                        ->join('astrologers', 'astrologers.userId', 'user_notifications.userId')
                        ->where('astrologers.id', '=', $req->id)
                        ->select('user_notifications.*')
                        ->orderBy('user_notifications.id', 'DESC')
                        ->get();

                    $gift = DB::Table('astrologer_gifts')
                        ->join('users', 'users.id', '=', 'astrologer_gifts.userId')
                        ->join('gifts', 'gifts.id', '=', 'astrologer_gifts.giftId')
                        ->where('astrologer_gifts.astrologerId', '=', $req->id)
                        ->select(
                            'astrologer_gifts.*',
                            'users.name as userName',
                            'users.profile',
                            'users.contactNo',
                            'gifts.name as giftName',
                            'gifts.image as giftImage',
                            'gifts.amount as giftAmount'
                        )
                        ->get();
                    $fiveStarRating = 0;
                    $fourStarRating = 0;
                    $threeStarRating = 0;
                    $twoStarRating = 0;
                    $oneStarRating = 0;
                    if ($review && count($review) > 0) {
                        for ($i = 0; $i < count($review); $i++) {

                            if ($review[$i]->rating == 1) {
                                $oneStarRating += 1;
                            }
                            if ($review[$i]->rating == 2) {
                                $twoStarRating += 1;
                            }
                            if ($review[$i]->rating == 3) {
                                $threeStarRating += 1;
                            }
                            if ($review[$i]->rating == 4) {
                                $fourStarRating += 1;
                            }
                            if ($review[$i]->rating == 5) {
                                $fiveStarRating += 1;
                            }
                        }
                    }
                    $starRating = $oneStarRating + $twoStarRating
                        + $threeStarRating + $fourStarRating + $fiveStarRating;
                    error_log(empty($review));
                    $avgRating = $review && count($review) > 0 ? $starRating / count($review) : 0;
                    $rating = array(
                        'oneStarRating' => $oneStarRating > 0 ? $oneStarRating * 100 / count($review) : 0,
                        'twoStarRating' => $twoStarRating > 0 ? $twoStarRating * 100 / count($review) : 0,
                        'threeStarRating' => $threeStarRating > 0 ? $threeStarRating * 100 / count($review) : 0,
                        'fourStarRating' => $fourStarRating > 0 ? $fourStarRating * 100 / count($review) : 0,
                        'fiveStarRating' => $fiveStarRating > 0 ? $fiveStarRating * 100 / count($review) : 0,
                    );
                    $totalFollower = DB::Table('user_followings')
                        ->where('astrologerId', '=', $req->id)
                        ->count();
                    $astrologer[0]->chatHistory = $chatHistory;
                    $astrologer[0]->callHistory = $callHistory;
                    $astrologer[0]->wallet = $wallet;
                    $astrologer[0]->review = $review;
                    $astrologer[0]->report = $reports;
                    $astrologer[0]->chatMin = $chatMin;
                    $astrologer[0]->callMin = $callMin;
                    $astrologer[0]->totalFollower = $totalFollower;
                    $astrologer[0]->astrologerRating = $rating;
                    $astrologer[0]->rating = $avgRating;
                    $astrologer[0]->follower = $follower;
                    $astrologer[0]->notification = $notification;
                    $astrologer[0]->gifts = $gift;
                    $result = json_decode($astrologer);
                    return view('pages.astrologer-detail')->with(['result' => $result]);
                }
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function printAstrologer(Request $req)
    {
        try {
            $astrologers = Astrologer::query();
            $astrologers = $astrologers->where('isDelete', '=', false);
            $searchString = $req->searchString ? $req->searchString : null;
            if ($req->searchString) {
                $astrologers = $astrologers->where(function ($q) use ($searchString) {
                    $q->where('astrologers.name', 'LIKE', '%' . $searchString . '%')
                        ->orWhere('astrologers.contactNo', 'LIKE', '%' . $searchString . '%');
                });
            }
            $astrologers = $astrologers->orderBy('id', 'DESC');
            $astrologers = $astrologers->get();
            if ($astrologers && count($astrologers) > 0) {
                foreach ($astrologers as $astro) {
                    $review = DB::table('user_reviews')
                        ->where('astrologerId', '=', $astro->id)
                        ->get();
                    if ($review && count($review) > 0) {
                        $avgRating = 0;
                        foreach ($review as $re) {
                            $avgRating += $re->rating;
                        }
                        $avgRating = $avgRating / count($review);
                        $astro['rating'] = $avgRating;
                    }
                    $totalCall = DB::table('callrequest')
                        ->where('astrologerId', '=', $astro['id'])
                        ->count();
                }
            }
            DB::table('systemflag')
                ->where('name', 'AdminLogo')
                ->select('value')
                ->first();
            $data = [
                'title' => 'Advisors',
                'date' => Carbon::now()->format('d-m-Y h:i'),
                'astrologers' => $astrologers,
            ];
            $pdf = PDF::loadView('pages.astrologerList', $data);
            return $pdf->download('advisors.pdf');
        } catch (\Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function exportAstrologer(Request $request)
    {
        $this->path = env('API_URL');
        $astrologers = Astrologer::query();
        $astrologers = $astrologers->where('isDelete', '=', false);
        $searchString = $request->searchString ? $request->searchString : null;
        if ($request->searchString) {
            $astrologers = $astrologers->where(function ($q) use ($searchString) {
                $q->where('astrologers.name', 'LIKE', '%' . $searchString . '%')
                    ->orWhere('astrologers.contactNo', 'LIKE', '%' . $searchString . '%');
            });
        }
        $astrologers = $astrologers->orderBy('id', 'DESC');
        $astrologers = $astrologers->get();
        if ($astrologers && count($astrologers) > 0) {
            foreach ($astrologers as $astro) {
                $review = DB::table('user_reviews')
                    ->where('astrologerId', '=', $astro->id)
                    ->get();
                if ($review && count($review) > 0) {
                    $avgRating = 0;
                    foreach ($review as $re) {
                        $avgRating += $re->rating;
                    }
                    $avgRating = $avgRating / count($review);
                    $astro['rating'] = $avgRating;
                }
                $totalCall = DB::table('callrequest')
                    ->where('astrologerId', '=', $astro['id'])
                    ->count();
                $astro['totalCallRequest'] = $totalCall;
            }
        }
        $headers = array(
            "Content-type" => "text/csv",
        );
        $filename = public_path("advisors.csv");
        $handle = fopen($filename, 'w');
        fputcsv($handle, [
            "ID",
            "Name",
            "ContactNo",
            "Gender",
            "TotalCallRequest",
            "status",
        ]);
        for ($i = 0; $i < count($astrologers); $i++) {
            fputcsv($handle, [
                $i + 1,
                $astrologers[$i]->name,
                $astrologers[$i]->contactNo,
                $astrologers[$i]->gender,
                $astrologers[$i]->totalCallRequest,
                $astrologers[$i]->isVerified ? 'Verified' : 'UnVerified',
            ]);
        }
        fclose($handle);
        return Response::download($filename, "advisors.csv", $headers);
    }

    public function editAstrologer(Request $req)
    {
        $astrologer = Astrologer::find($req->id);
        $astrologerCategory = AstrologerCategory::query()->where('isActive', true)->where('isDelete', false)->get();
        $skills = Skill::query()->where('isActive', true)->where('isDelete', false)->get();
        $language = Language::query()->get();
        $mainSourceBusiness = MainSourceOfBusiness::query()->get();
        $highestQualification = HighestQualification::query()->get();
        $qualifications = DegreeOrDiploma::query()->get();
        $jobs = FulltimeJob::query()->get();
        $countryTravel = TravelCountry::query()->get();
        $astrologerAvailability = DB::table('astrologer_availabilities')->where('astrologerId', $req->id)->get();
        $day = [];
        $working = [];
        $day = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        if ($astrologerAvailability && count($astrologerAvailability) > 0) {

            foreach ($day as $days) {
                $day = array(
                    'day' => $days,
                );
                $currentday = $days;
                $result =
                    array_filter(
                        json_decode($astrologerAvailability),
                        function ($event) use ($currentday) {
                            return $event->day === $currentday;
                        }
                    );
                $ti = [];
                foreach ($result as $available) {
                    if ($available->fromTime) {
                        $available->fromTime = Carbon::createFromFormat('h:i A', $available->fromTime);
                        $available->fromTime = $available->fromTime->format('H:i');
                    }
                    if ($available->toTime) {
                        $available->toTime = Carbon::createFromFormat('h:i A', $available->toTime);
                        $available->toTime = $available->toTime->format('H:i');
                    }
                    $time = array(

                        'fromTime' => $available->fromTime,
                        'toTime' => $available->toTime,
                    );
                    array_push($ti, $time);
                }
                if (count($ti) == 0) {
                    $time = array(

                        'fromTime' => null,
                        'toTime' => null,
                    );
                    array_push($ti, $time);
                }
                $weekDay = array(
                    'day' => $days,
                    'time' => $ti,
                );
                array_push($working, $weekDay);
            }
            $astrologer->astrologerAvailability = $working;
        } else {

            foreach ($day as $days) {
                $ti = [];
                $time = array(
                    'fromTime' => null,
                    'toTime' => null,
                );
                array_push($ti, $time);
                $weekDay = array(
                    'day' => $days,
                    'time' => $ti,
                );
                array_push($working, $weekDay);
            }

            $astrologer->astrologerAvailability = $working;
        }

        return view('pages.edit-astrologer')->with(['astrologer' => $astrologer, 'astrologerCategory' => $astrologerCategory, 'skills' => $skills, 'language' => $language, 'mainSourceBusiness' => $mainSourceBusiness, 'highestQualification' => $highestQualification, 'qualifications' => $qualifications, 'jobs' => $jobs, 'countryTravel' => $countryTravel]);
    }

    public function editAstrologerApi(Request $req)
    {
        try {
            $data = $req->only(
                'id',
                'name',
                'email',
                'contactNo',
                'gender',
                'birthDate',
                'primarySkill',
                'allSkill',
                'languageKnown',
                'profileImage',
                'charge',
                'experienceInYears',
                'dailyContribution',
                'isWorkingOnAnotherPlatform',
                'whyOnBoard',
                'interviewSuitableTime',
                'mainSourceOfBusiness',
                'highestQualification',
                'degree',
                'college',
                'learnAstrology',
                'astrologerCategoryId',
                'instaProfileLink',
                'facebookProfileLink',
                'linkedInProfileLink',
                'youtubeChannelLink',
                'websiteProfileLink',
                'isAnyBodyRefer',
                'minimumEarning',
                'maximumEarning',
                'loginBio',
                'NoofforeignCountriesTravel',
                'currentlyworkingfulltimejob',
                'goodQuality',
                'biggestChallenge',
                'whatwillDo',
                'isVerified',
            );
            $validator = Validator::make($data, [
                'id' => 'required',
                'astrologerCategoryId' => 'required',
                'name' => 'required|string',
                'contactNo' => 'required',
                'email' => 'required',
                'gender' => 'required',
                // 'birthDate' => 'required',
                // 'dailyContribution' => 'required',
                'languageKnown' => 'required',
                'primarySkill' => 'required',
                // 'allSkill' => 'required',
                // 'interviewSuitableTime' => 'required',
                // 'mainSourceOfBusiness' => 'required',
                // 'minimumEarning' => 'required',
                // 'maximumEarning' => 'required',
                // 'NoofforeignCountriesTravel' => 'required',
                // 'currentlyworkingfulltimejob' => 'required',
                // 'goodQuality' => 'required',
                // 'biggestChallenge' => 'required',
                // 'whatwillDo' => 'required',
                // 'charge' => 'required',
                // 'whyOnBoard' => 'required',
                // 'highestQualification' => 'required',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->getMessageBag()->toArray(),
                ]);
            }
            $astrologer = Astrologer::find($req->id);
            if (request('profileImage')) {
                $profileImage = base64_encode(file_get_contents($req->file('profileImage')));
            } elseif ($astrologer->profileImage) {
                $profileImage = $astrologer->profileImage;
            } else {
                $profileImage = null;
            }
            if ($profileImage) {
                if (Str::contains($profileImage, 'storage')) {
                    $path = $profileImage;
                } else {
                    $time = Carbon::now()->timestamp;
                    $destinationpath = 'storage/images/';
                    $imageName = 'astrologer_' . $req->id . $time;
                    $path = $destinationpath . $imageName . '.png';
                    $isFile = explode('.', $path);
                    if (!(file_exists($path) && count($isFile) > 1)) {
                        file_put_contents(public_path($path), base64_decode($profileImage));
                    }
                }
            } else {
                $path = null;
            }

            $user = User::find($astrologer->userId);
            if ($user) {
                $user->name = $req->name;
                $user->contactNo = $req->contactNo;
                $user->email = $req->email;
                $user->birthDate = $req->birthDate;
                $user->profile = $path;
                $user->gender = $req->gender;
                $user->location = $req->currentCity;
                $user->countryCode = $req->countryCode;
                $user->update();
            }

            if ($astrologer) {
                $astrologer->name = $req->name;
                $astrologer->email = $req->email;
                $astrologer->contactNo = $req->contactNo;
                $astrologer->gender = $req->gender;
                $astrologer->birthDate = $req->birthDate;
                $astrologer->primarySkill = implode(',', $req->primarySkill);
                $astrologer->allSkill = null;
                $astrologer->languageKnown = implode(',', $req->languageKnown);
                $astrologer->profileImage = $path;
                $astrologer->charge = $req->charge;
                $astrologer->experienceInYears = $req->experienceInYears;
                $astrologer->dailyContribution = $req->dailyContribution;
                $astrologer->hearAboutAstroguru = $req->hearAboutAstroguru;
                $astrologer->isWorkingOnAnotherPlatform = $req->isWorkingOnAnotherPlatform;
                $astrologer->whyOnBoard = $req->whyOnBoard;
                $astrologer->interviewSuitableTime = $req->interviewSuitableTime;
                $astrologer->currentCity = $req->currentCity;
                $astrologer->mainSourceOfBusiness = $req->mainSourceOfBusiness;
                $astrologer->highestQualification = $req->highestQualification;
                $astrologer->degree = $req->degree;
                $astrologer->college = $req->college;
                $astrologer->learnAstrology = $req->learnAstrology;
                $astrologer->astrologerCategoryId = implode(',', $req->astrologerCategoryId);
                $astrologer->instaProfileLink = $req->instaProfileLink;
                $astrologer->linkedInProfileLink = $req->linkedInProfileLink;
                $astrologer->facebookProfileLink = $req->facebookProfileLink;
                $astrologer->websiteProfileLink = $req->websiteProfileLink;
                $astrologer->youtubeChannelLink = $req->youtubeChannelLink;
                $astrologer->isAnyBodyRefer = $req->isAnyBodyRefer;
                $astrologer->minimumEarning = $req->minimumEarning;
                $astrologer->maximumEarning = $req->maximumEarning;
                $astrologer->loginBio = $req->loginBio;
                $astrologer->NoofforeignCountriesTravel = $req->NoofforeignCountriesTravel;
                $astrologer->currentlyworkingfulltimejob = $req->currentlyworkingfulltimejob;
                $astrologer->goodQuality = $req->goodQuality;
                $astrologer->biggestChallenge = $req->biggestChallenge;
                $astrologer->whatwillDo = $req->whatwillDo;
                $astrologer->videoCallRate = $req->videoCallRate;
                $astrologer->reportRate = $req->reportRate;
                $astrologer->nameofplateform = $req->nameofplateform;
                $astrologer->monthlyEarning = $req->monthlyEarning;
                $astrologer->referedPerson = $req->referedPerson;
                $astrologer->update();
                if ($req->astrologerAvailability) {
                    $availability = DB::Table('astrologer_availabilities')->where('astrologerId', '=', $req->id)->delete();
                    foreach ($req->astrologerAvailability as $astrologeravailable) {
                        if (array_key_exists('time', $astrologeravailable)) {
                            foreach ($astrologeravailable['time'] as $availability) {
                                if ($availability['fromTime']) {
                                    $availability['fromTime'] = Carbon::createFromFormat('H:i', $availability['fromTime'])->format('h:i A');
                                }
                                if ($availability['toTime']) {
                                    $availability['toTime'] = Carbon::createFromFormat('H:i', $availability['toTime'])->format('h:i A');
                                }
                                AstrologerAvailability::create([
                                    'astrologerId' => $req->id,
                                    'day' => $astrologeravailable['day'],
                                    'fromTime' => $availability['fromTime'],
                                    'toTime' => $availability['toTime'],
                                    'createdBy' => $req->id,
                                    'modifiedBy' => $req->id,
                                ]);
                            }
                        }
                    }
                }
                $astrologer->astrologerAvailability = $req->astrologerAvailability;
                return response()->json([
                    'message' => 'Astrologer update sucessfully',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }
}
