<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\AstrologerModel\Astrologer;
use App\Models\UserModel\CallRequest;
use App\Models\UserModel\ChatRequest;
use App\services\FCMService;
use App\Services\WaitListService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ChatRequestController extends Controller
{
    public function addChatRequest(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }
            $data = $req->only(
                'astrologerId',
            );
            $validator = Validator::make($data, [
                'astrologerId' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }
            ChatRequest::create([
                'astrologerId' => $req->astrologerId,
                'userId' => $id,
                'chatStatus' => 'Pending',
                'senderId' => '',
                'isFreeSession' => $req->isFreeSession,
            ]);

            $userDeviceDetail = DB::table('user_device_details as device')
                ->JOIN('astrologers', 'astrologers.userId', '=', 'device.userId')
                ->WHERE('astrologers.id', '=', $req->astrologerId)
                ->SELECT('device.*', 'astrologers.userId as astrologerUserId')
                ->get();

            $user = DB::table('users')->where('id', '=', $id)->select('name')->get();
            error_log($userDeviceDetail);
            if ($userDeviceDetail && count($userDeviceDetail) > 0) {
                FCMService::send(
                    $userDeviceDetail,
                    [
                        'title' => 'Receive Chat Request From ' . $user[0]->name,
                        'body' => ['description' => '', 'notificationType' => 8],
                    ]
                );
                $notification = array(
                    'userId' => $userDeviceDetail[0]->astrologerUserId,
                    'title' => 'Receive Chat Request From ' . $user[0]->name,
                    'description' => '',
                    'notificationId' => null,
                    'createdBy' => $userDeviceDetail[0]->astrologerUserId,
                    'modifiedBy' => $userDeviceDetail[0]->astrologerUserId,
                );
                DB::table('user_notifications')->insert($notification);
            }
            return response()->json([
                'message' => 'Chat Request add successfully',
                'status' => 200,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500,
                'error' => false,
            ], 500);
        }
    }

    public function getChatRequest(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }
            $data = $req->only(
                'astrologerId',
            );
            $validator = Validator::make($data, [
                'astrologerId' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }
            $chatRequest = DB::table('chatrequest')
                ->join('users', 'users.id', '=', 'chatrequest.userId')
                ->leftjoin('user_device_details', 'user_device_details.userId', 'users.id')
                ->where('astrologerId', '=', $req->astrologerId)
                ->where('chatStatus', '=', 'Pending')
                ->select('users.*', 'chatrequest.id as chatId', 'chatrequest.senderId', 'user_device_details.fcmToken');

            if ($req->startIndex >= 0 && $req->fetchRecord) {
                $chatRequest->skip($req->startIndex);
                $chatRequest->take($req->fetchRecord);
            }

            return response()->json([
                'messge' => 'getChatRequest Successfully',
                'status' => 200,
                'recordList' => $chatRequest->get(),
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500,
                'error' => false,
            ], 500);
        }
    }

    public function rejectChatRequest(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }
            $data = $req->only(
                'chatId',
            );
            $validator = Validator::make($data, [
                'chatId' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }
            $chatRequest = ChatRequest::find($req->chatId);
            $currenttimestamp = Carbon::now()->timestamp;
            if ($chatRequest) {
                $chatRequest->chatStatus = 'Rejected';
                $chatRequest->updated_at = $currenttimestamp;
                $chatRequest->update();
                $userDeviceDetail = DB::table('user_device_details as usd')
                    ->WHERE('usd.userId', '=', $chatRequest->userId)
                    ->SELECT('usd.*')
                    ->get();
                $astrologer = DB::table('astrologers')
                    ->where('id', '=', $chatRequest->astrologerId)
                    ->select('astrologers.name')
                    ->get();
                if ($userDeviceDetail && count($userDeviceDetail) > 0) {
                    FCMService::send(
                        $userDeviceDetail,
                        [
                            'title' => 'Chat missed with ' . $astrologer[0]->name,
                            'body' => [
                                'description' => 'It seems like you have missed/rejected your chat from ' . $astrologer[0]->name . ' .You may initiate it again from the app.',
                            ],
                        ]
                    );
                    $notification = array(
                        'userId' => $chatRequest->userId,
                        'title' => 'Chat missed with ' . $astrologer[0]->name,
                        'description' => 'It seems like you have missed/rejected your chat from ' . $astrologer[0]->name . ' .You may initiate it again from the app.',
                        'notificationId' => null,
                        'createdBy' => $chatRequest->userId,
                        'modifiedBy' => $chatRequest->userId,
                    );
                    DB::table('user_notifications')->insert($notification);
                }
                return response()->json([
                    'messge' => 'Reject Chat Request Successfully',
                    'status' => 200,
                ], 200);
            }
        } catch (\Exception$e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500,
                'error' => false,
            ], 500);
        }
    }

    public function removeFromWaitlist(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }
            $data = $req->only(
                'chatId',
            );
            $validator = Validator::make($data, [
                'chatId' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }
            $chatRequest = ChatRequest::find($req->chatId);
            $chatRequest->Delete();
            return response()->json([
                'messge' => 'Remove Chat Request Successfully',
                'status' => 200,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500,
                'error' => false,
            ], 500);
        }
    }

    public function acceptChatRequest(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }
            $data = $req->only(
                'chatId',
            );
            $validator = Validator::make($data, [
                'chatId' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }
            $chatRequest = ChatRequest::find($req->chatId);
            $currenttimestamp = Carbon::now()->timestamp;
            if ($chatRequest) {
                $chatRequest->chatStatus = 'Accepted';
                $chatRequest->updated_at = $currenttimestamp;
                $chatRequest->receiverId = '';
                $chatRequest->update();
                $userDeviceDetail = DB::table('user_device_details as us')
                    ->WHERE('us.userId', '=', $chatRequest->userId)
                    ->SELECT('us.*')
                    ->get();

                $astrologer = DB::Table('astrologers')
                    ->leftjoin('user_device_details', 'user_device_details.userId', 'astrologers.userId')
                    ->where('astrologers.id', '=', $chatRequest->astrologerId)
                    ->select('name', 'profileImage', 'user_device_details.fcmToken')
                    ->get();
                if ($userDeviceDetail && count($userDeviceDetail) > 0) {
                    $response = FCMService::send(
                        $userDeviceDetail,
                        [
                            'title' => 'Accept Chat Request From ' . $astrologer[0]->name,
                            'body' => [
                                "astrologerId" => $chatRequest->astrologerId,
                                "astrologerName" => $astrologer[0]->name,
                                "notificationType" => 3,
                                "profile" => $astrologer[0]->profileImage,
                                "token" => $chatRequest->token,
                                "channelName" => $chatRequest->channelName,
                                "receiverId" => $chatRequest->receiverId,
                                "senderId" => $chatRequest->senderId,
                                'description' => '',
                                "firebaseChatId" => $chatRequest->chatId,
                                'chatId' => $req->chatId,
                                'fcmToken' => $astrologer[0]->fcmToken,
                            ],
                        ]
                    );
                    $notification = array(
                        'userId' => $chatRequest->userId,
                        'title' => 'Accept Chat Request From ' . $astrologer[0]->name,
                        'description' => '',
                        'notificationId' => null,
                        'createdBy' => $userDeviceDetail[0]->id,
                        'modifiedBy' => $userDeviceDetail[0]->id,
                    );
                    DB::table('user_notifications')->insert($notification);
                }
                return response()->json([
                    'messge' => $response,
                    'status' => 200,
                    'firebaseChatId' => $chatRequest->chatId,
                ], 200);
            }
        } catch (\Exception$e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500,
                'error' => false,
            ], 500);
        }
    }

    public function storeToken(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }
            $data = $req->only(
                'chatId',
                'token',
                'channelName'
            );
            $validator = Validator::make($data, [
                'chatId' => 'required',
                'token' => 'required',
                'channelName' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }
            $chatRequest = ChatRequest::find($req->chatId);
            $currenttimestamp = Carbon::now()->timestamp;
            if ($chatRequest) {
                $chatRequest->chatStatus = 'Accepted';
                $chatRequest->updated_at = $currenttimestamp;
                $chatRequest->token = $req->token;
                $chatRequest->channelName = $req->channelName;
                $chatRequest->update();

            }
            $userDeviceDetail = DB::table('user_device_details')
                ->WHERE('user_device_details.userId', '=', $chatRequest->userId)
                ->SELECT('user_device_details.*')
                ->get();

            $astrologer = DB::Table('astrologers')
                ->where('id', '=', $chatRequest->astrologerId)
                ->select('name', 'profileImage')
                ->get();
            if ($userDeviceDetail && count($userDeviceDetail) > 0) {
                $response = FCMService::send(
                    $userDeviceDetail,
                    [
                        'title' => 'Receive Call Request',
                        'body' => [
                            "astrologerId" => $chatRequest->astrologerId,
                            "astrologerName" => $astrologer[0]->name,
                            "notificationType" => 6,
                            "profile" => $astrologer[0]->profileImage,
                            "token" => $chatRequest->token,
                            "channelName" => $chatRequest->channelName,
                            "receiverId" => $chatRequest->receiverId,
                            "senderId" => $chatRequest->senderId,
                            'description' => '',
                        ],
                    ]
                );
            }
            return response()->json([
                'messge' => $response,
                'status' => 200,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500,
                'error' => false,
            ], 500);
        }
    }

    public function insertChatRequest(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }
            $data = $req->only(
                'userId',
                'partnerId',
            );
            $validator = Validator::make($data, [
                'userId' => 'required',
                'partnerId' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }
            $firebaseChatData = ChatRequest::find($req->chatId);
            $chatData = DB::table('chatrequest')
                ->where('senderId', '=', $req->userId)
                ->where('receiverId', '=', $req->partnerId)
                ->get();
            if (!($chatData && count($chatData) > 0)) {
                $partnerChatData = DB::table('chatrequest')
                    ->where('senderId', '=', $req->partnerId)
                    ->where('receiverId', '=', $req->userId)
                    ->get();
                if (!($partnerChatData && count($partnerChatData) > 0)) {
                    $chatId = $req->userId . '_' . $req->partnerId;

                } else {
                    $chatId = $partnerChatData[0]->chatId;
                }
            } else {
                $chatId = $chatData[0]->chatId;
            }
            if ($firebaseChatData) {
                $firebaseChatData->senderId = $req->userId;
                $firebaseChatData->receiverId = $req->partnerId;
                $firebaseChatData->chatId = $chatId;
                $firebaseChatData->update();
            }

            $astrologer = DB::Table('astrologers')
                ->where('id', '=', $firebaseChatData->astrologerId)
                ->select('name', 'profileImage', 'userId')
                ->get();
                
            $userDeviceDetail = DB::table('user_device_details')
                ->WHERE('user_device_details.userId', '=', $astrologer[0]->userId)
                ->SELECT('user_device_details.*')
                ->get();

            if ($userDeviceDetail && count($userDeviceDetail) > 0) {
                FCMService::send(
                    $userDeviceDetail,
                    [
                        'title' => 'Receive Chat Request',
                        'body' => [
                            "astrologerId" => $firebaseChatData->astrologerId,
                            "astrologerName" => $astrologer[0]->name,
                            "notificationType" => 5,
                            "profile" => $astrologer[0]->profileImage,
                            "firebaseChatId" => $chatId,
                            "chatId" => $req->chatId,
                            'description' => '',
                        ],
                    ]
                );
            }
            return response()->json([
                "status" => 200,
                "recordList" => $chatId,
                "chatId" => $req->chatId,
            ]);
        } catch (\Exception$e) {
            return response()->json([
                'message' => $chatId,
                'status' => 500,
                'error' => false,
                'error_messages' => $e->getMessage()
            ], 500);
        }
    }

    public function endChatRequest(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }
            $data = $req->only(
                'chatId',
                'totalMin'
            );
            $validator = Validator::make($data, [
                'chatId' => 'required',
                'totalMin' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }
            $chatData = DB::table('chatrequest')
                ->join('astrologers', 'astrologers.id', '=', 'chatrequest.astrologerId')
                ->join('users', 'users.id', '=', 'chatrequest.userId')
                ->where('chatrequest.id', '=', $req->chatId)
                ->select('chatrequest.*', 'users.name', 'astrologers.name as astrologerName', 'astrologers.userId as astrologerUserId')
                ->get();
            $totalMin = $req->totalMin / 60;
            $totalMin = round($totalMin);
            $astrologerCommission = 0;
            $deduction = 0;
            $charge = Astrologer::query()
                ->where('id', '=', $chatData[0]->astrologerId)
                ->get();
            if (!$chatData[0]->isFreeSession) {
                $deduction = $totalMin * $charge[0]->charge;

                $commission = DB::table('commissions')
                    ->where('commissionTypeId', '=', '1')
                    ->where('astrologerId', '=', $chatData[0]->astrologerId)
                    ->get();
                if ($commission && count($commission) > 0) {
                    $adminCommission = ($commission[0]->commission * $deduction) / 100;
                } else {
                    $syscommission = DB::table('systemflag')->where('name', 'ChatCommission')->select('value')->get();
                    $adminCommission = ($syscommission[0]->value * $deduction) / 100;
                }
                $astrologerCommission = $deduction - $adminCommission;
            }
            $charges = array(
                'totalOrder' => $charge[0]->totalOrder + 1,
            );
            DB::table('astrologers')
                ->where('id', $charge[0]->id)
                ->update($charges);
            $chatDatas = array(
                'totalMin' => $totalMin,
                'chatStatus' => 'Completed',
                'deduction' => $deduction,
                'chatRate' => !$chatData[0]->isFreeSession ? $charge[0]->charge : 0,
                'deductionFromAstrologer' => $astrologerCommission,
            );
            DB::Table('chatrequest')
                ->where('id', '=', $req->chatId)
                ->update($chatDatas);

            $charge[0]->totalOrder = $charge[0]->totalOrder ? $charge[0]->totalOrder + 1 : 1;
            if ($charge[0]->charge > 0) {
                $wallet = DB::table('user_wallets')
                    ->where('userId', '=', $chatData[0]->userId)
                    ->get();
                $wallets = array(
                    'userId' => $chatData[0]->userId,
                    'amount' => (!$chatData[0]->isFreeSession) ? ($wallet[0]->amount - $deduction) : (($wallet && count($wallet) > 0) ? $wallet[0]->amount : 0),
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
                    ->where('userId', $chatData[0]->astrologerUserId)
                    ->get();
                $astrologerWall = array(
                    'userId' => $chatData[0]->astrologerUserId,
                    'amount' => $astrologerWallet && count($astrologerWallet) > 0 ? $astrologerWallet[0]->amount + $astrologerCommission : $astrologerCommission,
                    'createdBy' => $id,
                    'modifiedBy' => $id,
                );
                if ($astrologerWallet && count($astrologerWallet) > 0) {
                    DB::table('user_wallets')
                        ->where('id', $chatData[0]->astrologerUserId)
                        ->update($astrologerWall);
                } else {
                    DB::Table('user_wallets')->insert($astrologerWall);
                }
            }
            $orderRequest = array(
                'userId' => $chatData[0]->userId,
                'astrologerId' => $chatData[0]->astrologerId,
                'orderType' => 'chat',
                'totalPayable' => $deduction,
                'orderStatus' => 'Complete',
                'totalMin' => $totalMin,
                'chatId' => $req->chatId,

            );
            DB::Table('order_request')->insert($orderRequest);
            $id = DB::getPdo()->lastInsertId();
            $transaction = array(
                'userId' => $chatData[0]->userId,
                'amount' => $deduction,
                'isCredit' => false,
                "transactionType" => 'Chat',
                "orderId" => $id,
                "astrologerId" => $chatData[0]->astrologerId,
            );
            $astrologerTransaction = array(
                'userId' => $chatData[0]->astrologerUserId,
                'amount' => $astrologerCommission,
                'isCredit' => true,
                "transactionType" => 'Chat',
                "orderId" => $id,
                "astrologerId" => $chatData[0]->astrologerId,
            );
            if (!$chatData[0]->isFreeSession) {
                if ($commission && count($commission) > 0) {
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
                'message' => 'User Chat Request End Successfully',
                'status' => 200,
                'recordList' => $deduction,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500,
                'error' => false,
            ], 500);
        }
    }

    public function rejectChatRequestFromCustomer(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }
            $data = $req->only(
                'chatId',
            );
            $validator = Validator::make($data, [
                'chatId' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }
            $chatData = ChatRequest::find($req->chatId);
            if ($chatData) {
                $chatData->delete();
            }
            return response()->json([
                'message' => 'Chat Request Rejected Successfully',
                'status' => 200,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500,
                'error' => false,
            ], 500);
        }
    }

    public function acceptChatRequestFromCustomer(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }
            $data = $req->only(
                'chatId',
            );
            $validator = Validator::make($data, [
                'chatId' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }
            $chatData = ChatRequest::find($req->chatId);
            $currenttimestamp = Carbon::now()->timestamp;
            if ($chatData) {
                $chatData->chatStatus = 'Confirmed';
                $chatData->deduction = 0;
                $chatData->updated_at = $currenttimestamp;
                $chatData->totalMin = 0;
                $chatData->update();
            }
            return response()->json([
                'message' => 'Chat Request Accepted Successfully',
                'status' => 200,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500,
                'error' => false,
            ], 500);
        }
    }

    public function endLiveChatrequest(Request $req)
    {
        try {
            DB::beginTransaction();
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }
            $data = $req->only(
                'userId',
                'astrologerId',
                'totalMin'
            );
            $validator = Validator::make($data, [
                'userId' => 'required',
                'astrologerId' => 'required',
                'totalMin' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }
            $sId = DB::Table('callrequest')
                ->where('sId', '=', $req->sId)
                ->get();
            if (!($sId && count($sId) > 0)) {
                $totalMin = $req->totalMin / 60;
                $totalMin = round($totalMin);
                $charge = Astrologer::query()
                    ->where('id', '=', $req->astrologerId)
                    ->get();
                $deduction = $totalMin * $charge[0]->charge;
                $commission = DB::table('commissions')
                    ->where('commissionTypeId', '=', '1')
                    ->where('astrologerId', '=', $req->astrologerId)
                    ->get();
                if ($commission && count($commission) > 0) {
                    $adminCommission = ($commission[0]->commission * $deduction) / 100;
                } else {
                    $syscommission = DB::table('systemflag')->where('name', 'ChatCommission')->select('value')->get();
                    $adminCommission = ($syscommission[0]->value * $deduction) / 100;
                }
                $astrologerCommission = $deduction - $adminCommission;
                $chatData = CallRequest::create([
                    'astrologerId' => $req->astrologerId,
                    'userId' => $req->userId,
                    'totalMin' => $totalMin,
                    'callStatus' => 'Completed',
                    'callRate' => $charge[0]->charge,
                    'deductionFromAstrologer' => $astrologerCommission,
                    'deduction' => $deduction,
                    'chatId' => $req->chatId,
                    'sId' => $req->sId,
                    'sId1' => $req->sId1,
                    'channelName' => $req->channelName,
                ]);

                $charges = array(
                    'totalOrder' => $charge[0]->totalOrder + 1,
                );
                DB::table('astrologers')
                    ->where('id', $charge[0]->id)
                    ->update($charges);
                $charge[0]->totalOrder = $charge[0]->totalOrder ? $charge[0]->totalOrder + 1 : 1;
                $astrologerUserId = DB::table('astrologers')
                    ->where('id', '=', $req->astrologerId)
                    ->get();
                if ($charge[0]->charge) {
                    $wallet = DB::table('user_wallets')
                        ->where('userId', '=', $req->userId)
                        ->get();
                    $wallets = array(
                        'amount' => $wallet[0]->amount - $deduction,
                    );

                    DB::table('user_wallets')
                        ->where('id', $wallet[0]->id)
                        ->update($wallets);

                    $astrologerWallet = DB::table('user_wallets')
                        ->join('astrologers', 'astrologers.userId', '=', 'user_wallets.userId')
                        ->where('astrologers.id', $req->astrologerId)
                        ->select('user_wallets.*')
                        ->get();
                    $astrologerWall = array(
                        'userId' => $astrologerUserId[0]->userId,
                        'amount' => $astrologerWallet && count($astrologerWallet) > 0 ? $astrologerWallet[0]->amount + $astrologerCommission : $astrologerCommission,
                        'createdBy' => $id,
                        'modifiedBy' => $id,
                    );
                    if ($astrologerWallet && count($astrologerWallet) > 0) {
                        DB::table('user_wallets')
                            ->where('id', $astrologerWallet[0]->id)
                            ->update($astrologerWall);
                    } else {
                        DB::Table('user_wallets')->insert($astrologerWall);
                    }
                }
                $orderRequest = array(
                    'userId' => $req->userId,
                    'astrologerId' => $req->astrologerId,
                    'orderType' => 'chat',
                    'totalPayable' => $deduction,
                    'orderStatus' => 'Complete',
                    'totalMin' => $totalMin,
                    'callId' => $chatData->id,

                );
                DB::Table('order_request')->insert($orderRequest);
                $id = DB::getPdo()->lastInsertId();
                $transaction = array(
                    'userId' => $req->userId,
                    'amount' => $deduction,
                    'isCredit' => false,
                    "transactionType" => $req->transactionType,
                    "orderId" => $id,
                    "astrologerId" => $req->astrologerId,
                );
                $astrologerTransaction = array(
                    'userId' => $astrologerUserId[0]->userId,
                    'amount' => $astrologerCommission,
                    'isCredit' => true,
                    "transactionType" => $req->transactionType,
                    "orderId" => $id,
                    "astrologerId" => $req->astrologerId,
                );
                if ($adminCommission > 0) {
                    $adminGetCommission = array(
                        'commissionTypeId' => 1,
                        "amount" => $adminCommission,
                        "commissionId" => $commission && count($commission) > 0 ? $commission[0]->id : null,
                        "orderId" => $id,
                        // "description"=>"Commission for chat between ".$charge[0]->name ." and ".$chatData[0]->name ." for ".$totalMin . " Minutes",
                        "createdBy" => $charge[0]->userId,
                        "modifiedBy" => $charge[0]->userId,
                    );
                    DB::table('admin_get_commissions')->insert($adminGetCommission);
                }

                DB::table('wallettransaction')->insert($transaction);
                DB::table('wallettransaction')->insert($astrologerTransaction);
                DB::commit();
                $data = array(
                    'deduction' => $deduction,
                    'callId' => $chatData->id,
                );

                return response()->json([
                    'message' => 'Chat Request End Successfully',
                    'status' => 200,
                    'recordList' => $data,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Chat Request End Successfully',
                    'status' => 200,
                    'recordList' => [],
                ], 200);
            }
        } catch (\Exception$e) {
            DB::rollback();
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500,
                'error' => false,
            ], 500);
        }
    }

    public function intakeForm(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }
            $data = $req->only(
                'name',
                'phoneNumber'
            );
            $validator = Validator::make($data, [
                'name' => 'required',
                'phoneNumber' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }
            $intakeForm = array(
                'name' => $req->name,
                'phoneNumber' => $req->phoneNumber,
                'countryCode' => $req->countryCode,
                'gender' => $req->gender,
                'birthDate' => $req->birthDate,
                'birthTime' => $req->birthTime,
                'birthPlace' => $req->birthPlace,
                'maritalStatus' => $req->maritalStatus,
                'occupation' => $req->occupation,
                'topicOfConcern' => $req->topicOfConcern,
                'partnerName' => $req->partnerName,
                'partnerBirthDate' => $req->partnerBirthDate,
                'partnerBirthTime' => $req->partnerBirthTime,
                'partnerBirthPlace' => $req->partnerBirthPlace,
                'country' => $req->country,
                'city' => $req->city,
                'email' => $req->email,
                'userId' => $id,
            );
            $intake = DB::table('intakeform')->where('userId', '=', $id)->get();
            if ($intake && count($intake) > 0) {
                $intakeForm = DB::table('intakeform')->update($intakeForm);
            } else {
                DB::table('intakeform')->insert($intakeForm);
            }
            return response()->json([
                'message' => 'Chat Intake Form Add Successfully',
                'status' => 200,
                'recordList' => $intakeForm,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500,
                'error' => false,
            ], 500);
        }
    }

    public function getUserIntakForm(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }
            $id = $req->userId ? $req->userId : $id;
            $intakeData = DB::table('intakeform')
                ->where('userId', '=', $id)
                ->get();
            return response()->json([
                'message' => 'Chat Intake Form Get Successfully',
                'status' => 200,
                'recordList' => $intakeData,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500,
                'error' => false,
            ], 500);
        }
    }

    public function saveToken(Request $req)
    {
        try {
            $data = array(
                'fcm_token' => $req->token,
            );
            DB::table('users')
                ->where('id', '=', $req->id)
                ->update($data);
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function checkChatSessionTaken(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }
            $session = DB::table('chatrequest')
                ->where('userId', '=', $id)
                ->where('astrologerId', '=', $req->astrologerId)
                ->where('chatStatus', '=', 'Pending')
                ->get();
            $isAvailable = false;
            if ($session && count($session) > 0) {
                $isAvailable = true;
            }
            return response()->json([
                'status' => 200,
                'recordList' => $isAvailable,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function checkCallSessionTaken(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }
            $session = DB::table('callrequest')
                ->where('userId', '=', $id)
                ->where('astrologerId', '=', $req->astrologerId)
                ->where('callStatus', '=', 'Pending')
                ->get();
            $isAvailable = false;
            if ($session && count($session) > 0) {
                $isAvailable = true;
            }
            return response()->json([
                'status' => 200,
                'recordList' => $isAvailable,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function addChatStatus(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                Auth::guard('api')->user()->id;
            }

            $status = array(
                'chatStatus' => $req->status,
                'chatWaitTime' => ($req->status == 'Offline' || $req->status == 'Online') ? null : $req->waitTime,
            );
            DB::table('astrologers')->where('id', '=', $req->astrologerId)
                ->update($status);

            $notifiedUser = null;
            if (strcasecmp((string) $req->status, 'Online') === 0) {
                $notifiedUser = WaitListService::notifyNextWaitingUser($req->astrologerId);
            }

            return response()->json([
                "message" => "Update Astrologer",
                'status' => 200,
                'waitlistNotified' => $notifiedUser,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function checkFreeSessionAvailable(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }
            $isAddNewRequest = true;
            // $isChatRequest = DB::table('chatrequest')->where('userId', $id)->where('chatStatus', '=', 'Pending')->first();
            $isCallRequest = DB::table('callrequest')->where('userId', $id)->where('callStatus', '=', 'Pending')->first();
            if (/*$isChatRequest || */$isCallRequest) {
                $isAddNewRequest = false;
            }
            return response()->json([
                "isAddNewRequest" => $isAddNewRequest,
                "message" => "Update Astrologer",
                'status' => 200,
            ], 200);

        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }
}
