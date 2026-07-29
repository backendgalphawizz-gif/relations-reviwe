<?php

namespace App\Http\Controllers\Advisor;

use App\Http\Controllers\Controller;
use App\AgoraToken\RtcTokenBuilder;
use App\Models\AstrologerModel\Astrologer;
use App\Models\AstrologerModel\AstrologerAvailability;
use App\Models\AdminModel\Language;
use App\Models\AstrologerCategory;
use App\Models\UserModel\CallRequest;
use App\Models\UserModel\ChatRequest;
use App\Models\User;
use App\Models\Skill;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\services\FCMService;
use App\Services\WaitListService;
use App\Services\CallRingService;

class DashboardController extends Controller
{
    /**
     * Show specified view.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {

        $user = Auth::guard('advisor')->user();
        if(!$user) {
            return redirect()->route('advisor.login');
        }

        $astrologer = Astrologer::where('userId', $user->id)->first();

        $calls = CallRequest::select(DB::raw('SUM(deduction) as total, SUM(totalMin) as total_minutes'))->where('astrologerId', $astrologer->id)->first();
        $callhistories = CallRequest::with(['astrologer', 'user'])->where('callStatus', 'Completed')->where('astrologerId', $astrologer->id)->orderBy('id', 'DESC')->get();
        $chatRequests = ChatRequest::with(['astrologer', 'user'])->whereIn('chatStatus', ['Pending', 'Accepted', 'Confirmed'])->where('astrologerId', $astrologer->id)->orderBy('id', 'DESC')->get();

        $filter = strtolower(trim((string) $request->query('filter', 'incoming')));
        $allowedFilters = ['incoming', 'calls', 'running', 'rejected', 'missed', 'minutes', 'earning'];
        if (!in_array($filter, $allowedFilters, true)) {
            $filter = 'incoming';
        }

        $astroId = (int) $astrologer->id;
        $listQuery = CallRequest::with(['astrologer', 'user']);

        $listTitle = 'Incoming Call Requests';
        $showActions = true;

        switch ($filter) {
            case 'calls':
                $listQuery->where('astrologerId', $astroId)
                    ->whereIn('callStatus', ['Pending', 'Accepted', 'Confirmed', 'Completed']);
                $listTitle = 'Call Requests';
                $showActions = false;
                break;
            case 'running':
                $listQuery->where('astrologerId', $astroId)
                    ->whereIn('callStatus', ['Accepted', 'Confirmed']);
                $listTitle = 'Running Calls';
                $showActions = true;
                break;
            case 'rejected':
                // Only: Reject by Me, or Customer cancel while this advisor had the call
                // Time Over → Missed Calls (not here)
                $this->scopeAdvisorRejectedCalls($listQuery, $astroId);
                $listTitle = 'Rejected Calls';
                $showActions = false;
                break;
            case 'missed':
                // Ring timeout / moved on (Time Over) — not explicit reject or customer cancel
                $this->scopeAdvisorMissedCalls($listQuery, $astroId);
                $listTitle = 'Missed Calls';
                $showActions = false;
                break;
            case 'minutes':
                $listQuery->where('astrologerId', $astroId)
                    ->where('callStatus', 'Completed');
                $listTitle = 'Completed Calls (Total Minutes)';
                $showActions = false;
                break;
            case 'earning':
                $listQuery->where('astrologerId', $astroId)
                    ->where('callStatus', 'Completed');
                $listTitle = 'Completed Calls (Total Earning)';
                $showActions = false;
                break;
            default: // incoming
                $listQuery->where('astrologerId', $astroId)
                    ->whereIn('callStatus', ['Pending', 'Accepted', 'Confirmed']);
                $listTitle = 'Incoming Call Requests';
                $showActions = true;
                break;
        }

        $callRequests = $listQuery->orderBy('id', 'DESC')->paginate(10)->withQueryString();

        // Attach who ended the call (Rejected Calls): Me | Customer | Time Over
        if ($filter === 'rejected') {
            foreach ($callRequests as $call) {
                $call->rejectedByName = $this->resolveRejectedByLabel($call, $astroId);
            }
        }

        // Missed Calls are always 30s timeout (moved to another advisor)
        if ($filter === 'missed') {
            foreach ($callRequests as $call) {
                $call->rejectedByName = 'Time Over';
            }
        }

        $missedCallCount = CallRequest::query();
        $this->scopeAdvisorMissedCalls($missedCallCount, $astroId);
        $missedCallCount = $missedCallCount->count();

        $rejectedCallCount = CallRequest::query();
        $this->scopeAdvisorRejectedCalls($rejectedCallCount, $astroId);
        $rejectedCallCount = $rejectedCallCount->count();

        $result = [
            "totalCallRequest" => CallRequest::where('astrologerId', $astroId)->whereIn('callStatus', ['Pending', 'Accepted', 'Confirmed', 'Completed'])->count(),
            "totalRejectedCallRequest" => $rejectedCallCount,
            "totalMissedCallRequest" => $missedCallCount,
            "totalRunningCallRequest" => CallRequest::where('astrologerId', $astroId)->whereIn('callStatus', ['Accepted', 'Confirmed'])->count(),
            "totalminutes" => $calls->total_minutes,
            "totalChatRequest" => ChatRequest::where('astrologerId', $astroId)->count(),
            "totalReportRequest" => 0,
            "topAstrologer" => 0,
            "totalEarning" => $calls->total,
            "totalCustomer" => 0,
            "totalAstrologer" => 0,
            "monthlyCommission" => 0,
            "monthlyCallRequest" => 0,
            "monthlyChatRequest" => 0,
            "monthlyReportRequest" => 0,
            "unverifiedAstrologer" => 0
        ];

        return view('vendor.pages.dashboard', compact(
            'result',
            'callhistories',
            'callRequests',
            'chatRequests',
            'filter',
            'listTitle',
            'showActions'
        ));
    }

    /**
     * Rejected Calls: only Me (advisor reject) or Customer (cancel while this advisor had the call).
     * Time Over belongs in Missed Calls.
     */
    protected function scopeAdvisorRejectedCalls($query, int $astroId): void
    {
        $query->where(function ($q) use ($astroId) {
            // Explicit Reject by this advisor
            $q->where(function ($explicit) use ($astroId) {
                $explicit->whereJsonContains('rejected_astrologer_ids', $astroId)
                    ->orWhereJsonContains('rejected_astrologer_ids', (string) $astroId);
            })
            // Customer cancelled while this advisor was ringing / assigned
            ->orWhere(function ($customer) use ($astroId) {
                $customer->where('astrologerId', $astroId)
                    ->where('callStatus', 'Rejected')
                    ->where(function ($by) {
                        $by->where('rejected_by', 'customer')
                            // Legacy rows: non-sequential reject without rejected_by = customer cancel
                            ->orWhere(function ($legacy) {
                                $legacy->where(function ($rb) {
                                    $rb->whereNull('rejected_by')->orWhere('rejected_by', '');
                                })->where(function ($seq) {
                                    $seq->whereNull('is_sequential')
                                        ->orWhere('is_sequential', 0)
                                        ->orWhere('is_sequential', false);
                                });
                            });
                    })
                    ->where(function ($notTimeout) {
                        $notTimeout->whereNull('rejected_by')
                            ->orWhere('rejected_by', '!=', 'timeout');
                    })
                    ->where(function ($notMe) use ($astroId) {
                        $notMe->whereNull('rejected_astrologer_ids')
                            ->orWhere(function ($nr) use ($astroId) {
                                $nr->whereJsonDoesntContain('rejected_astrologer_ids', $astroId)
                                    ->whereJsonDoesntContain('rejected_astrologer_ids', (string) $astroId);
                            });
                    });
            });
        });
    }

    /**
     * Missed Calls (Time Over): ring timed out / moved to another advisor.
     */
    protected function scopeAdvisorMissedCalls($query, int $astroId): void
    {
        $query->where(function ($q) use ($astroId) {
            // Timed out and call moved on to another advisor
            $q->where(function ($moved) use ($astroId) {
                $moved->where(function ($tried) use ($astroId) {
                    $tried->whereJsonContains('tried_astrologer_ids', $astroId)
                        ->orWhereJsonContains('tried_astrologer_ids', (string) $astroId);
                })
                    ->where('astrologerId', '!=', $astroId)
                    ->where(function ($notRejected) use ($astroId) {
                        $notRejected->whereNull('rejected_astrologer_ids')
                            ->orWhere(function ($nr) use ($astroId) {
                                $nr->whereJsonDoesntContain('rejected_astrologer_ids', $astroId)
                                    ->whereJsonDoesntContain('rejected_astrologer_ids', (string) $astroId);
                            });
                    });
            })
            // Time Over exhausted while still assigned to this advisor
            ->orWhere(function ($timeoutMine) use ($astroId) {
                $timeoutMine->where('astrologerId', $astroId)
                    ->where('callStatus', 'Rejected')
                    ->where(function ($by) {
                        $by->where('rejected_by', 'timeout')
                            // Legacy sequential Rejected without rejected_by = timeout exhaust
                            ->orWhere(function ($legacy) {
                                $legacy->where(function ($rb) {
                                    $rb->whereNull('rejected_by')->orWhere('rejected_by', '');
                                })->where(function ($seq) {
                                    $seq->where('is_sequential', 1)
                                        ->orWhere('is_sequential', true);
                                });
                            });
                    })
                    ->where(function ($notRejected) use ($astroId) {
                        $notRejected->whereNull('rejected_astrologer_ids')
                            ->orWhere(function ($nr) use ($astroId) {
                                $nr->whereJsonDoesntContain('rejected_astrologer_ids', $astroId)
                                    ->whereJsonDoesntContain('rejected_astrologer_ids', (string) $astroId);
                            });
                    });
            });
        });
    }

    /**
     * Rejected By column for current advisor:
     * - Me → this advisor pressed Reject
     * - Time Over → ring missed / timed out (any advisor miss)
     * - Customer → customer cancelled while this advisor had the call
     */
    protected function resolveRejectedByLabel(CallRequest $call, int $astroId): string
    {
        $ids = is_array($call->rejected_astrologer_ids) ? $call->rejected_astrologer_ids : [];
        $ids = array_map('intval', $ids);
        $tried = is_array($call->tried_astrologer_ids) ? $call->tried_astrologer_ids : [];
        $tried = array_map('intval', $tried);

        $iRejected = in_array($astroId, $ids, true);
        $iTried = in_array($astroId, $tried, true);
        $reason = strtolower(trim((string) ($call->rejected_by ?? '')));

        // Explicit Reject by this advisor always wins
        if ($iRejected) {
            return 'Me';
        }
        if ($reason === 'advisor' && (int) $call->astrologerId === $astroId) {
            return 'Me';
        }

        // Customer cancelled while this advisor had the call
        if ($reason === 'customer' && (int) $call->astrologerId === $astroId) {
            return 'Customer';
        }

        // Timeout / miss
        if ($reason === 'timeout') {
            return 'Time Over';
        }

        // Legacy inference
        if ($iTried && (bool) $call->is_sequential) {
            return 'Time Over';
        }
        if ((int) $call->astrologerId === $astroId && $call->callStatus === 'Rejected') {
            return 'Customer';
        }

        return $iTried ? 'Time Over' : 'Customer';
    }

    public function profile() {
        $user = Auth::guard('advisor')->user();

        $astrologer = Astrologer::where('userId', $user->id)->first();
        $mainCategories = AstrologerCategory::where(['isActive' => 1, 'isDelete' => 0])->get();
        $skills = Skill::where(['isActive' => 1, 'isDelete' => 0])->get();
        $languages = Language::where(['status' => 1])->get();

        return view('vendor.pages.profile', compact('user', 'astrologer', 'mainCategories', 'skills', 'languages'));
    }

    public function availability() {
        $user = Auth::guard('advisor')->user();
        $astrologer = Astrologer::with('availability')->where('userId', $user->id)->first();
        $astrologerAvailability = $astrologer->availability;
        if ($astrologerAvailability && count($astrologerAvailability) > 0) {
            $day = [];
            $working = [];
            $day = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            foreach ($day as $days) {
                $day = array(
                    'day' => $days,
                );
                $currentday = $days;
                $result = array_filter(json_decode($astrologerAvailability), function ($event) use ($currentday) {
                    return strtolower($event->day) === strtolower($currentday);
                });
                $ti = [];

                foreach ($result as $available) {
                    $time = array(
                        'fromTime' => $available->fromTime ?? '',
                        'toTime' => $available->toTime ?? '',
                    );
                    array_push($ti, $time);
                }
                $weekDay = array(
                    'day' => $days,
                    'time' => $ti,
                );
                array_push($working, $weekDay);
            }
            $astrologerAvailability = $working;
        } else {
            $weekDay = [];
            $day = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            foreach ($day as $days) {
                $day = array(
                    'day' => $days,
                );
                
                $time = [array(
                    'fromTime' => '',
                    'toTime' => '',
                )];
                $weekDay[] = array(
                    'day' => $days,
                    'time' => $time,
                );
            }

            $astrologerAvailability = $weekDay;
        }
        // dd($astrologerAvailability);

        return view('vendor.pages.availability', compact('astrologer', 'astrologerAvailability'));
    }

    public function updateProfile(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'mobile' => 'required',
            'birthDate' => 'required',
            'gender' => 'required',
            'experienceInYears' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->getMessageBag()->toArray(),
                'message' => 'Please fill required fields'
            ], 200);
        }

        $user = User::find(Auth::guard('advisor')->user()->id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->contactNo = $request->mobile;
        $user->birthDate = $request->birthDate;
        $user->gender = $request->gender;
        $user->save();

        $astrologer = Astrologer::where('userId', Auth::guard('advisor')->user()->id)->first();
        $astrologer->name = $request->name;
        $astrologer->email = $request->email;
        $astrologer->contactNo = $request->mobile;
        $astrologer->birthDate = $request->birthDate;
        $astrologer->gender = $request->gender;
        $astrologer->experienceInYears = $request->experienceInYears;
        $astrologer->primarySkill = implode(',',$request->primarySkill);
        $astrologer->astrologerCategoryId = implode(',', $request->astrologerCategoryId);
        $astrologer->languageKnown = implode(',', $request->languageKnown);
        $astrologer->currentCity = $request->currentCity;
        $astrologer->highestQualification = $request->highestQualification;
        $astrologer->degree = $request->degree;
        $astrologer->college = $request->college;
        $astrologer->loginBio = $request->loginBio;

        
        if (request('profile')) {
            $image = base64_encode(file_get_contents($request->file('profile')));
        } elseif ($user->profile) {
            $image = $user->profile;
        } else {
            $image = null;
        }
        $time = Carbon::now()->timestamp;
        if ($image) {
            if (Str::contains($image, 'storage')) {
                $path = $image;
            } else {
                $destinationpath = 'storage/images/';
                $imageName = 'user_' . $request->id . $time;
                $path = $destinationpath . $imageName . '.png';
                File::delete($user->profile);
                file_put_contents($path, base64_decode($image));
            }
            $user->profile = $path;
            $user->save();
        }

        $astrologer->save();

        return response()->json([
            'status' => true,
            'error' => '',
            'message' => 'Profile Updated Success'
        ], 200);

    }

    public function callHistory(Request $request) {
        $user = Auth::guard('advisor')->user();
        $limit = 10;
        $astrologer = Astrologer::where('userId', $user->id)->first();
        $callhistories = CallRequest::with(['astrologer', 'user'])->whereIn('callStatus', ['Rejected', 'Completed'])->where('astrologerId', $astrologer->id)->orderBy('id', 'DESC')->paginate($limit);

        return view('vendor.pages.call-history', compact( 'callhistories'));
    }

    public function chatHistory(Request $request) {
        $user = Auth::guard('advisor')->user();
        $astrologer = Astrologer::where('userId', $user->id)->first();
        $limit = 10;
        $chathistories = ChatRequest::with(['astrologer', 'user'])->whereIn('chatStatus', ['Rejected', 'Completed'])->where('astrologerId', $astrologer->id)->orderBy('id', 'DESC')->paginate($limit);

        return view('vendor.pages.chat-history', compact( 'chathistories'));
    }

    public function waitTime(Request $request) {
        $user = Auth::guard('advisor')->user();
        $astrologer = Astrologer::where('userId', $user->id)->first();
        return view('vendor.pages.wait-time', compact( 'user', 'astrologer'));
    }

    public function transactions(Request $request) {
        $user = Auth::guard('advisor')->user();
        $limit = 10;
        $transactions = DB::table('wallettransaction')->where('userId', $user->id)->orderBy('id', 'DESC')->paginate($limit); // ->skip($paginationStart)->take($limit)->get();
        return view('vendor.pages.transactions', compact( 'transactions'));
    }

    public function notifications(Request $request)
    {
        $user = Auth::guard('advisor')->user();
        if (!$user) {
            return redirect()->route('advisor.login');
        }

        $notifications = DB::table('user_notifications')
            ->where('userId', $user->id)
            ->where(function ($q) {
                $q->whereNull('isDelete')->orWhere('isDelete', 0)->orWhere('isDelete', false);
            })
            ->orderBy('id', 'DESC')
            ->paginate(15);

        return view('vendor.pages.notifications', compact('notifications'));
    }

    public function updateFcmToken(Request $request)
    {
        $user = Auth::guard('advisor')->user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        $token = trim((string) $request->input('fcm_token', ''));
        if ($token === '') {
            return response()->json(['status' => false, 'message' => 'FCM token required'], 422);
        }

        $appId = (int) ($request->input('appId') ?: 3);

        // Free this token from any other user/device rows
        DB::table('user_device_details')
            ->where('fcmToken', $token)
            ->where('userId', '!=', $user->id)
            ->update(['fcmToken' => '', 'updated_at' => now()]);

        $device = DB::table('user_device_details')
            ->where('userId', $user->id)
            ->where('appId', $appId)
            ->first();

        $payload = [
            'userId' => $user->id,
            'appId' => $appId,
            'fcmToken' => $token,
            'deviceId' => $request->input('userAgent') ?: ($request->header('User-Agent') ?: 'web'),
            'deviceManufacturer' => $request->input('osName') ?: 'web',
            'deviceModel' => $request->input('appVersion') ?: 'browser',
            'appVersion' => '1.1.0',
            'isActive' => 1,
            'updated_at' => now(),
        ];

        if ($device) {
            DB::table('user_device_details')->where('id', $device->id)->update($payload);
        } else {
            $payload['created_at'] = now();
            DB::table('user_device_details')->insert($payload);
        }

        DB::table('users')->where('id', $user->id)->update([
            'desktop_token' => $token,
            'updated_at' => now(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'FCM token updated',
        ]);
    }

    public function pendingCalls(Request $request)
    {
        $user = Auth::guard('advisor')->user();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'Unauthorized'], 401);
        }

        // Move timed-out sequential rings so the next Online advisor (app/web) can receive
        try {
            CallRingService::advanceOverdueCalls();
        } catch (\Throwable $e) {
            // ignore scheduler failures for poll
        }

        $astrologer = Astrologer::where('userId', $user->id)->first();
        if (!$astrologer) {
            return response()->json(['status' => true, 'calls' => [], 'count' => 0]);
        }

        $astroId = (int) $astrologer->id;

        // If this web advisor still holds an overdue sequential ring, force advance to next (app) advisor
        try {
            $overdue = CallRequest::query()
                ->where('astrologerId', $astroId)
                ->where('callStatus', 'Pending')
                ->where(function ($q) {
                    $q->where('is_sequential', true)->orWhere('is_sequential', 1);
                })
                ->whereNotNull('ring_started_at')
                ->get();

            foreach ($overdue as $call) {
                $timeout = (int) ($call->ring_timeout_seconds ?: CallRingService::DEFAULT_TIMEOUT_SECONDS);
                $started = Carbon::parse($call->ring_started_at);
                if ($started->diffInSeconds(Carbon::now()) >= $timeout) {
                    CallRingService::advanceToNextAdvisor($call, true, 'timeout');
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }

        $isOnline = strtolower((string) $astrologer->callStatus) === 'online';
        if (!$isOnline) {
            return response()->json([
                'status' => true,
                'count' => 0,
                'calls' => [],
                'advisorOnline' => false,
                'callStatus' => $astrologer->callStatus,
                'astrologerId' => $astrologer->id,
            ]);
        }

        $calls = CallRequest::with('user')
            ->where('astrologerId', $astroId)
            ->whereIn('callStatus', ['Pending', 'Accepted', 'Confirmed'])
            ->orderBy('id', 'DESC')
            ->limit(20)
            ->get();

        return response()->json([
            'status' => true,
            'count' => $calls->count(),
            'calls' => $calls->map(function ($call) {
                $timeout = (int) ($call->ring_timeout_seconds ?: CallRingService::DEFAULT_TIMEOUT_SECONDS);
                $secondsLeft = null;
                if ($call->is_sequential && $call->ring_started_at) {
                    $elapsed = Carbon::parse($call->ring_started_at)->diffInSeconds(Carbon::now());
                    $secondsLeft = max(0, $timeout - $elapsed);
                }

                return [
                    'id' => $call->id,
                    'userId' => $call->userId,
                    'type' => $call->type,
                    'callStatus' => $call->callStatus,
                    'created_at' => $call->created_at,
                    'is_sequential' => (bool) $call->is_sequential,
                    'ring_timeout_seconds' => $timeout,
                    'ringSecondsLeft' => $secondsLeft,
                    'user' => [
                        'id' => $call->user->id ?? null,
                        'name' => $call->user->name ?? 'Customer',
                    ],
                ];
            })->values(),
            'advisorOnline' => true,
            'callStatus' => $astrologer->callStatus,
            'astrologerId' => $astrologer->id,
        ]);
    }

    public function privacyPolicy()
    {
        $user = Auth::guard('advisor')->user();
        if (!$user) {
            return redirect()->route('advisor.login');
        }

        $data = DB::table('static_pages')->where('slug', 'privacy_policy')->first();

        return view('vendor.pages.static-page', [
            'pageTitle' => $data->title ?? 'Privacy Policy',
            'content' => $data->description ?? '<p>Content not available.</p>',
        ]);
    }

    public function termsCondition()
    {
        $user = Auth::guard('advisor')->user();
        if (!$user) {
            return redirect()->route('advisor.login');
        }

        $data = DB::table('static_pages')->where('slug', 'terms_condition')->first();

        return view('vendor.pages.static-page', [
            'pageTitle' => $data->title ?? 'Terms & Condition',
            'content' => $data->description ?? '<p>Content not available.</p>',
        ]);
    }

    public function withdrawls(Request $request) {
        $user = Auth::guard('advisor')->user();
        $astrologer = Astrologer::where('userId', $user->id)->first();
        $wallet = DB::table('user_wallets')
                ->where('user_wallets.userId', '=', $user->id)
                ->select('amount', 'user_wallets.id')->first();

        $limit = 10;
        $withdrawls = DB::table('withdrawrequest')->where('astrologerId', $astrologer->id)->orderBy('id', 'DESC')->paginate($limit); // ->skip($paginationStart)->take($limit)->get();
        return view('vendor.pages.withdrawls', compact( 'withdrawls', 'wallet'));
    }
    public function createWithdrawls(Request $request) {
        $user = Auth::guard('advisor')->user();
        $astrologer = Astrologer::where('userId', $user->id)->first();

        
        $limit = 10;
        $withdrawls = DB::table('withdrawrequest')->where('astrologerId', $astrologer->id)->orderBy('id', 'DESC')->paginate($limit); // ->skip($paginationStart)->take($limit)->get();
        return view('vendor.pages.create-withdrawls', compact( 'withdrawls', 'astrologer'));
    }

    public function submitWithdrawlRequest(Request $request) {
        $validator = Validator::make($request->all(), [
            'amount' => 'required',
            'payment_method' => 'required',
            'upi_id' => 'required_if:payment_method,upi',
            'account_number' => 'required_if:payment_method,bank',
            'ifsc_code' => 'required_if:payment_method,bank',
            'account_holder_name' => 'required_if:payment_method,bank',
        ],[
            'upi_id.required_if' => 'UPI ID is required',
            'account_number.required_if' => 'Account Number is required',
            'ifsc_code.required_if' => 'IFSC Code is required',
            'account_holder_name.required_if' => 'Account Holder Name is required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->getMessageBag()->toArray(),
                'message' => 'Please fill required fields'
            ], 200);
        }

        $user = Auth::guard('advisor')->user();
        $wallet = DB::table('user_wallets')
                ->join('astrologers', 'astrologers.userId', '=', 'user_wallets.userId')
                ->where('astrologers.id', '=', $request->astrologer_id)
                ->select('amount', 'user_wallets.id')->first();

        if($wallet->amount > $request->amount) {
            if ($wallet) {
                $userWallet = array(
                    'amount' => $wallet->amount - $request->amount,
                );
                DB::table('user_wallets')->where('id', $wallet->id)->update($userWallet);
            }
            DB::table('withdrawrequest')->insert([
                'astrologerId' => $request->astrologer_id,
                'withdrawAmount' => $request->amount,
                'paymentMethod' => $request->payment_method == 'upi' ? '2' : '1',
                'upiId' => $request->upi_id ?? NULL,
                'accountNumber' => $request->account_number ?? NULL,
                'ifscCode' => $request->ifsc_code ?? NULL,
                'accountHolderName' => $request->account_holder_name ?? NULL
            ]);
            return response()->json(['status' => true, 'message' => 'Withdrawl request generated successfully']);
        }
        return response()->json(['status' => false, 'message' => 'Insufficient balance in wallet']);
    }

    public function updateCallStatus(Request $request) {
        $id = $request->id;
        $callStatus = $request->callStatus;
        $callWaitTime = $request->callWaitTime ?? NULL;

        $astrologer = Astrologer::find($id);
        $astrologer->callStatus = $callStatus;
        $astrologer->callWaitTime = $callStatus == 'Wait Time' ? $callWaitTime : NULL;
        $astrologer->save();

        $notifiedUser = null;
        if (strcasecmp((string) $callStatus, 'Online') === 0) {
            CallRingService::releaseStaleLiveCalls((int) $id);
            $advisorUser = Auth::guard('advisor')->user();
            if ($advisorUser) {
                $now = now();
                $device = DB::table('user_device_details')
                    ->where('userId', $advisorUser->id)
                    ->where('appId', 3)
                    ->first();
                if ($device) {
                    DB::table('user_device_details')->where('id', $device->id)->update([
                        'isActive' => 1,
                        'updated_at' => $now,
                    ]);
                } else {
                    DB::table('user_device_details')->insert([
                        'userId' => $advisorUser->id,
                        'appId' => 3,
                        'isActive' => 1,
                        'fcmToken' => '',
                        'deviceId' => $request->userAgent() ?: 'web',
                        'deviceManufacturer' => 'web',
                        'deviceModel' => 'browser',
                        'appVersion' => '1.1.0',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
            $notifiedUser = WaitListService::notifyNextWaitingUser($id);
        } elseif (strcasecmp((string) $callStatus, 'Offline') === 0) {
            CallRingService::handleAdvisorWentOffline((int) $id);
        }

        return response()->json([
            'status' => true,
            'error' => '',
            'message' => 'Profile Status updated',
            'waitlistNotified' => $notifiedUser,
        ], 200);
    }

    public function updateAvailability(Request $request) {
        if ($request->astrologerAvailability) {
            $availability = DB::Table('astrologer_availabilities')->where('astrologerId', '=', $request->id)->delete();
            foreach ($request->astrologerAvailability as $astrologeravailable) {
                if (array_key_exists('time', $astrologeravailable)) {
                    foreach ($astrologeravailable['time'] as $availability) {
                        if ($availability['fromTime']) {
                            $availability['fromTime'] = date('h:i A',strtotime( $availability['fromTime']));
                        }
                        if ($availability['toTime']) {
                            $availability['toTime'] = date('h:i A',strtotime( $availability['toTime']));
                        }
                        AstrologerAvailability::create([
                            'astrologerId' => $request->id,
                            'day' => ucwords($astrologeravailable['day']),
                            'fromTime' => $availability['fromTime'],
                            'toTime' => $availability['toTime'],
                            'createdBy' => $request->id,
                            'modifiedBy' => $request->id
                        ]);
                    }
                }
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Availability time updated success'
        ]);

    }

    public function startCall(Request $request, $requestId) {
        $callRequest = CallRequest::find($requestId);
        if (!$callRequest) {
            return redirect()->route('advisor.dashboard')->with('error', 'Call request not found');
        }

        if($request->type == 'reject') {
            $actingAstrologerId = null;
            $advisorUser = Auth::guard('advisor')->user();
            if ($advisorUser) {
                $actingAstrologerId = optional(Astrologer::where('userId', $advisorUser->id)->first())->id;
            }

            if ($callRequest->is_sequential && $callRequest->callStatus === 'Pending') {
                // Track reject for this advisor, then ring next
                if ($actingAstrologerId) {
                    CallRingService::appendRejectedAstrologer($callRequest, (int) $actingAstrologerId);
                    $callRequest->save();
                }
                CallRingService::clearWebIncomingCall($callRequest);
                CallRingService::advanceToNextAdvisor($callRequest, true, 'rejected');
            } else {
                CallRingService::markRejectedByAdvisor(
                    $callRequest,
                    $actingAstrologerId ? (int) $actingAstrologerId : null
                );
            }
            return redirect()->route('advisor.dashboard', ['filter' => 'rejected']);
        }

        $actingAstrologerId = null;
        $advisorUser = Auth::guard('advisor')->user();
        if ($advisorUser) {
            $actingAstrologerId = optional(Astrologer::where('userId', $advisorUser->id)->first())->id;
        }

        $gate = CallRingService::validateAdvisorCanTakeCall($callRequest, $actingAstrologerId ? (int) $actingAstrologerId : null);
        if (!$gate['allowed']) {
            return redirect()->route('advisor.dashboard')->with(
                'error',
                $gate['message'] ?: 'This is a call you missed. Another astrologer has joined.'
            );
        }

        if(in_array($callRequest->callStatus, ['Pending', 'Accepted', 'Confirmed'])) {
            $chatId = $requestId;
            // $appId = env('AGORA_APP_ID');

            // $admin_charge = DB::table('systemflag')->where('name', 'CallCharges')->first()->value;

            $appId = '68e78b7633604458a5bf8b312a5b59cc'; // env('AGORA_APP_ID');
            $certificateId = '14ab7be1bcf84ffc88113f76ac9eeaeb'; // env('AGORA_APP_ID');
            $rtcToken = '';
            $channelName = '';
            if($callRequest->channelName=='' && $callRequest->token=='') {
                $channelName = 'relationship_revive_' . $callRequest->id.'_'.$callRequest->userId.'_'.$callRequest->astrologerId;
                $privilegeExpiredTs = Carbon::now()->timestamp + 600;
                $rtcTokenController = new RtcTokenBuilder;
                $rtcToken = $rtcTokenController->buildTokenWithUid($appId, $certificateId, $channelName, null, 1, $privilegeExpiredTs);
                $callRequest->channelName = $channelName;
                $callRequest->token = $rtcToken;
                $callRequest->callStatus = 'Accepted';
                $callRequest->save();

                CallRingService::notifyMissedAdvisorsAfterJoin($callRequest);
                
                $userDeviceDetail = DB::table('user_device_details')
                    ->WHERE('user_device_details.userId', '=', $callRequest->userId)
                    ->SELECT('user_device_details.*')
                    ->get();
        
                $astrologer = DB::Table('astrologers')
                        ->leftjoin('user_device_details', 'user_device_details.userId', 'astrologers.userId')
                        ->where('astrologers.id', '=', $callRequest->astrologerId)
                        ->select('astrologers.charge', 'name', 'profileImage', 'user_device_details.fcmToken')
                        ->get();
                $admin_charge = $callRequest->callRate;
        
                if ($userDeviceDetail && count($userDeviceDetail) > 0) {
        
                    // $admin_charge = DB::table('systemflag')->where('name', 'CallCharges')->first()->value;

                    // if($callRequest->callStatus == 'Accepted') {
                    //     $response = FCMService::send(
                    //         $userDeviceDetail,
                    //         [
                    //             'title' => 'Accept Call Request',
                    //             'body' => [
                    //                 "astrologerId" => $callRequest->astrologerId,
                    //                 "astrologerName" => $astrologer[0]->name,
                    //                 "notificationType" => 1,
                    //                 "profile" => $astrologer[0]->profileImage,
                    //                 "token" => $callRequest->token,
                    //                 "channelName" => $callRequest->channelName,
                    //                 "callId" => $callRequest->id,
                    //                 "type" => $callRequest->type,
                    //                 'description' => '',
                    //                 'fcmToken' => $astrologer[0]->fcmToken,
                    //                 'charges' => strval($admin_charge),
                    //             ],
                    //         ]
                    //     );
                    // }

                }
            }
    
            return view('vendor.pages.call-screen', compact('rtcToken', 'channelName', 'appId', 'chatId', 'callRequest'));
        }
        return redirect()->route('advisor.dashboard');
    }

    public function startChat(Request $request, $requestId) {
        $callRequest = ChatRequest::find($requestId);
        $chatStatus = $request->type == 'accept' ? 'Accepted' : 'Rejected';
        $callRequest->chatStatus = $chatStatus;
        $chatId = $requestId;
        $appId = '190390938d2549b2b31f680336e1fae0'; // env('AGORA_APP_ID');
        if($request->type == 'accept') {
            $channelName = 'relationship_revive_' . $callRequest->id.'_'.$callRequest->userId.'_'.$callRequest->astrologerId;
            $privilegeExpiredTs = Carbon::now()->timestamp + 600;
            $rtcTokenController = new RtcTokenBuilder;
            $rtcToken = $rtcTokenController->buildTokenWithUid(env('AGORA_APP_ID'), env('AGORA_CERTIFICATE'), $channelName, null, 1, $privilegeExpiredTs);

            $callRequest->channelName = $channelName;
            $callRequest->token = $rtcToken;
            $callRequest->save();

            return view('vendor.pages.chat-screen', compact('rtcToken', 'channelName', 'appId', 'chatId'));
        } else {
            $callRequest->save();

            return redirect()->route('advisor.dashboard');
        }
    }

    public function endCall(Request $req) {
        $data = $req->only(
            'callId',
            'totalMin'
        );
        $id = Auth::guard('api')->user()->id;
        $validator = Validator::make($data, [
            'callId' => 'required',
            'totalMin' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
        }

        $callData = DB::table('callrequest')
            ->join('astrologers', 'astrologers.id', '=', 'callrequest.astrologerId')
            ->join('users', 'users.id', '=', 'callrequest.userId')
            ->where('callrequest.id', '=', $req->callId)
            ->select('callrequest.*', 'users.name', 'astrologers.name as astrologerName', 'astrologers.userId as astrologerUserId')
            ->get();
        $totalMin = $req->totalMin / 60;
        $totalMin = round($totalMin);
        $astrologerCommission = 0;
        $deduction = 0;
        $charge = Astrologer::query()
            ->where('id', '=', $callData[0]->astrologerId)
            ->get();

        $admin_charge = DB::table('systemflag')->where('name', 'CallCharges')->first()->value;

        if (!$callData[0]->isFreeSession) {
            $deduction = $totalMin > 0 ? ($totalMin * $admin_charge) : (1 * $admin_charge);

            // $deduction = $totalMin > 0 ? ($totalMin * $charge[0]->charge) : (1 * $charge[0]->charge);
            $commission = DB::table('commissions')
                ->where('commissionTypeId', '=', '2')
                ->where('astrologerId', '=', $callData[0]->astrologerId)
                ->get();
            if ($commission && count($commission) > 0) {
                $adminCommission = ($commission[0]->commission * $deduction) / 100;
            } else {
                $syscommission = DB::table('systemflag')->where('name', 'CallCommission')->select('value')->get();

                $adminCommission = ($syscommission[0]->value * $deduction) / 100;
            }
            $astrologerCommission = $deduction - $adminCommission;
        }

        $callDatas = array(
            'totalMin' => $totalMin,
            'callStatus' => 'Completed',
            'deduction' => $deduction,
            'callRate' => !$callData[0]->isFreeSession ? $admin_charge : 0,
            'deductionFromAstrologer' => $astrologerCommission,
            'sId' => $req->sId,
            'sId1' => $req->sId1,
        );
        DB::Table('callrequest')
            ->where('id', '=', $req->callId)
            ->update($callDatas);
        $charge[0]->totalOrder = $charge[0]->totalOrder ? $charge[0]->totalOrder + 1 : 1;
        $charges = array(
            'totalOrder' => $charge[0]->totalOrder,
        );
        DB::table('astrologers')
            ->where('id', $charge[0]->id)
            ->update($charges);
        if ($admin_charge > 0) {
            $wallet = DB::table('user_wallets')
                ->where('userId', '=', $callData[0]->userId)
                ->get();
            $wallets = array(
                'userId' => $callData[0]->userId,
                'amount' => (!$callData[0]->isFreeSession) ? ($wallet[0]->amount - $deduction) : (($wallet && count($wallet) > 0) ? $wallet[0]->amount : 0),
                'createdBy' => $id,
                'modifiedBy' => $id,
            );
            if ($wallet && count($wallet) > 0) {
                DB::table('user_wallets')
                    ->where('id', $wallet[0]->id)
                    ->update($wallets);
            } else {
                DB::table('user_wallets')->insert($wallets);
            }
            $astrologerWallet = DB::table('user_wallets')
                ->where('userId', $callData[0]->astrologerUserId)
                ->get();
            $astrologerWall = array(
                'userId' => $callData[0]->astrologerUserId,
                'amount' => $astrologerWallet && count($astrologerWallet) > 0 ? $astrologerWallet[0]->amount + $astrologerCommission : $astrologerCommission,
                'createdBy' => $id,
                'modifiedBy' => $id,
            );
            if ($astrologerWallet && count($astrologerWallet) > 0) {
                DB::table('user_wallets')
                    ->where('id', $callData[0]->astrologerUserId)
                    ->update($astrologerWall);
            } else {
                DB::Table('user_wallets')->insert($astrologerWall);
            }
        }
        $orderRequest = array(
            'userId' => $callData[0]->userId,
            'astrologerId' => $callData[0]->astrologerId,
            'orderType' => 'call',
            'totalPayable' => $deduction,
            'orderStatus' => 'Complete',
            'totalMin' => $totalMin,
            'callId' => $req->callId,

        );
        DB::Table('order_request')->insert($orderRequest);
        $id = DB::getPdo()->lastInsertId();
        $transaction = array(
            'userId' => $callData[0]->userId,
            'amount' => $deduction,
            'isCredit' => false,
            "transactionType" => 'Call',
            "orderId" => $id,
            "astrologerId" => $callData[0]->astrologerId,
        );
        $astrologerTransaction = array(
            'userId' => $callData[0]->astrologerUserId,
            'amount' => $astrologerCommission,
            'isCredit' => true,
            "transactionType" => 'Call',
            "orderId" => $id,
            "astrologerId" => $callData[0]->astrologerId,
        );
        if(!$callData[0]->isFreeSession){
            if ($commission && count($commission) > 0 ) {
                $adminGetCommission = array(
                    'commissionTypeId' => 1,
                    "amount" => $adminCommission,
                    "commissionId" => $commission && count($commission) > 0 ? $commission[0]->id : null,
                    "orderId" => $id,
                    "createdBy" => $charge[0]->userId,
                    "modifiedBy" => $charge[0]->userId,
                );
                DB::table('admin_get_commissions')->insert($adminGetCommission);
            }
        }
        DB::table('wallettransaction')->insert($transaction);
        DB::table('wallettransaction')->insert($astrologerTransaction);
        return response()->json([
            'message' => 'Call Request End Successfully',
            'status' => 200,
            'recordList' => $deduction,
        ], 200);
    }

    public function joinNotification(Request $request) {

        try {
            //code...
            
            $callRequest = CallRequest::find($request->chatId);

            $userDeviceDetail = DB::table('user_device_details')
                        ->WHERE('user_device_details.userId', '=', $callRequest->userId)
                        ->SELECT('user_device_details.*')
                        ->get();

            $astrologer = DB::Table('astrologers')
                    ->leftjoin('user_device_details', 'user_device_details.userId', 'astrologers.userId')
                    ->where('astrologers.id', '=', $callRequest->astrologerId)
                    ->select('astrologers.charge', 'name', 'profileImage', 'user_device_details.fcmToken')
                    ->get();
            $admin_charge = DB::table('systemflag')->where('name', 'CallCharges')->first()->value;

            if ($userDeviceDetail && count($userDeviceDetail) > 0) {

                $admin_charge = DB::table('systemflag')->where('name', 'CallCharges')->first()->value;

                if($callRequest->callStatus == 'Accepted') {
                    FCMService::send(
                        $userDeviceDetail,
                        [
                            'title' => 'Accept Call Request',
                            'body' => [
                                "astrologerId" => $callRequest->astrologerId,
                                "astrologerName" => $astrologer[0]->name,
                                "notificationType" => 1,
                                "profile" => $astrologer[0]->profileImage,
                                "token" => $callRequest->token,
                                "channelName" => $callRequest->channelName,
                                "callId" => $callRequest->id,
                                "type" => $callRequest->type,
                                'call_type' => $callRequest->type,
                                'description' => '',
                                'fcmToken' => $astrologer[0]->fcmToken,
                                'charges' => strval($admin_charge),
                                'isFree' => strval($callRequest->isFreeSession),
                            ],
                        ]
                    );
                }

            }

            return response()->json(['status' => true]);
        } catch (\Throwable $th) {
            dd($th);
        }

    }

}