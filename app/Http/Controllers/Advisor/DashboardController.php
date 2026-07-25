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

class DashboardController extends Controller
{
    /**
     * Show specified view.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $user = Auth::guard('advisor')->user();
        if(!$user) {
            return redirect()->route('advisor.login');
        }

        $astrologer = Astrologer::where('userId', $user->id)->first();

        $calls = CallRequest::select(DB::raw('SUM(deduction) as total, SUM(totalMin) as total_minutes'))->where('astrologerId', $astrologer->id)->first();
        $callhistories = CallRequest::with(['astrologer', 'user'])->where('callStatus', 'Completed')->where('astrologerId', $astrologer->id)->orderBy('id', 'DESC')->get();
        $callRequests = CallRequest::with(['astrologer', 'user'])->whereIn('callStatus', ['Pending', 'Accepted', 'Confirmed'])->where('astrologerId', $astrologer->id)->orderBy('id', 'DESC')->get();
        $chatRequests = ChatRequest::with(['astrologer', 'user'])->whereIn('chatStatus', ['Pending', 'Accepted', 'Confirmed'])->where('astrologerId', $astrologer->id)->orderBy('id', 'DESC')->get();
        // dd($callRequests, $astrologer->id);
        // $callRequests = [];

        $result = [
            "totalCallRequest" => CallRequest::where('astrologerId', $astrologer->id)->whereIn('callStatus', ['Pending', 'Accepted', 'Confirmed', 'Completed'])->count(),
            "totalRejectedCallRequest" => CallRequest::where('astrologerId', $astrologer->id)->whereIn('callStatus', ['Rejected'])->count(),
            "totalRunningCallRequest" => CallRequest::where('astrologerId', $astrologer->id)->whereIn('callStatus', ['Accepted', 'Confirmed'])->count(),
            "totalminutes" => $calls->total_minutes,
            "totalChatRequest" => ChatRequest::where('astrologerId', $astrologer->id)->count(),
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

        return view('vendor.pages.dashboard', compact('result', 'callhistories', 'callRequests', 'chatRequests'));
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
        return response()->json([
            'status' => true,
            'error' => '',
            'message' => 'Profile Status updated'
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
        // dd($callRequest);
        if($request->type == 'reject') {
            $callRequest->callStatus = 'Rejected';
            $callRequest->save();
            return redirect()->route('advisor.dashboard');
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