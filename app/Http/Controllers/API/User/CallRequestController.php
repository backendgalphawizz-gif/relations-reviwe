<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\AstrologerModel\Astrologer;
use App\Models\UserModel\CallRequest;
use App\Models\AdminModel\SystemFlag;
use App\services\FCMService;
use App\Services\WaitListService;
use App\Services\CallRingService;
use App\Providers\FirebaseService;
use Carbon\Carbon;
use Google\Cloud\Storage\StorageClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
class CallRequestController extends Controller
{
    public function addCallRequest(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }

            $userId = Auth::guard('api')->user()->id;
            $type = $req->input('type', 'audio');
            $isFreeSession = ($req['isFreeSession'] == true || $req['isFreeSession'] == 'true');

            // Treat missing / empty / null / "null" / "undefined" / 0 as "no id" → sequential ring
            $rawAstrologerId = $req->input('astrologerId');
            if (is_string($rawAstrologerId)) {
                $rawAstrologerId = trim($rawAstrologerId);
            }
            $normalizedId = strtolower((string) ($rawAstrologerId ?? ''));
            $hasAstrologerId = $rawAstrologerId !== null
                && $rawAstrologerId !== ''
                && !in_array($normalizedId, ['null', 'undefined', 'none', '0'], true)
                && (int) $rawAstrologerId > 0;

            // With astrologerId → only that advisor (direct call, no sequential)
            if ($hasAstrologerId) {
                $result = CallRingService::startDirectCall(
                    $userId,
                    (int) $rawAstrologerId,
                    $type,
                    $isFreeSession
                );

                if (!$result['ok']) {
                    return response()->json([
                        'message' => $result['message'],
                        'status' => 400,
                        'error' => false,
                        'sequential' => false,
                    ], 400);
                }

                return response()->json([
                    'message' => $result['message'],
                    'status' => 200,
                    'data' => $result['callRequest']->id,
                    'sequential' => false,
                    'currentAstrologerId' => $result['currentAstrologerId'],
                ], 200);
            }

            // Without astrologerId → ring Online advisors one-by-one (30s each)
            $result = CallRingService::startSequentialCall($userId, $type, $isFreeSession);

            if (!$result['ok']) {
                return response()->json([
                    'message' => $result['message'] ?: 'Advisor not available right now',
                    'status' => 400,
                    'error' => false,
                    'sequential' => true,
                ], 400);
            }

            return response()->json([
                'message' => $result['message'],
                'status' => 200,
                'data' => $result['callRequest']->id,
                'sequential' => true,
                'currentAstrologerId' => $result['currentAstrologerId'],
                'ringTimeoutSeconds' => $result['ringTimeoutSeconds'],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Astrologer Not Available',
                'errorDetail' => $e->getMessage(),
                'status' => 500,
                'error' => false,
            ], 500);
        }
    }

    public function getCallRequest(Request $req)
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

            $astrologerId = (int) $req->astrologerId;
            $defaultTimeout = CallRingService::DEFAULT_TIMEOUT_SECONDS;

            // Offline advisors should not receive / poll pending call requests
            if (!CallRingService::isAdvisorOnline($astrologerId)) {
                return response()->json([
                    'messge' => 'getCallRequest Successfully',
                    'recordList' => [],
                    'status' => 200,
                    'advisorOnline' => false,
                    'ringTimeoutSeconds' => $defaultTimeout,
                ], 200);
            }

            // Move timed-out sequential rings to the next Online advisor first
            try {
                CallRingService::advanceOverdueCalls();
            } catch (\Throwable $e) {
                // ignore advance failures for poll
            }

            $query = DB::table('callrequest')
                ->join('users', 'users.id', '=', 'callrequest.userId')
                ->where('callrequest.astrologerId', '=', $astrologerId)
                ->where('callrequest.callStatus', '=', 'Pending')
                ->orderByDesc('callrequest.id')
                ->select(
                    'users.name',
                    'users.contactNo',
                    'users.email',
                    'users.profile',
                    'users.birthDate',
                    'users.gender',
                    'callrequest.userId',
                    'callrequest.id as callId',
                    'callrequest.id as id',
                    'callrequest.type as call_type',
                    'callrequest.type',
                    'callrequest.is_sequential',
                    'callrequest.ring_timeout_seconds',
                    'callrequest.ring_started_at',
                    'callrequest.created_at as call_created_at'
                );

            if ($req->startIndex >= 0 && $req->fetchRecord) {
                $query->skip((int) $req->startIndex);
                $query->take((int) $req->fetchRecord);
            }

            $rows = $query->get();
            $now = Carbon::now();
            $recordList = [];

            foreach ($rows as $row) {
                $timeout = (int) ($row->ring_timeout_seconds ?: $defaultTimeout);
                $row->ringTimeoutSeconds = $timeout;
                $row->ring_timeout_seconds = $timeout;

                $isSequential = (int) $row->is_sequential === 1;
                $row->is_sequential = $isSequential;

                if ($isSequential && !empty($row->ring_started_at)) {
                    $started = Carbon::parse($row->ring_started_at);
                    $elapsed = abs($started->diffInSeconds($now));
                    $secondsLeft = max(0, $timeout - $elapsed);
                    $row->ringSecondsLeft = $secondsLeft;

                    // Already past 30s — advance and do not return to this advisor
                    if ($elapsed >= $timeout) {
                        try {
                            $model = CallRequest::find($row->callId);
                            if ($model) {
                                CallRingService::advanceToNextAdvisor($model, true, 'timeout');
                            }
                        } catch (\Throwable $e) {
                            // ignore
                        }
                        continue;
                    }
                } else {
                    // Direct call (no sequential timer) — still return immediately
                    $row->ringSecondsLeft = $timeout;
                }

                $userDeviceDetail = DB::table('user_device_details')
                    ->where('userId', $row->userId)
                    ->whereNotNull('fcmToken')
                    ->where('fcmToken', '!=', '')
                    ->orderByDesc('id')
                    ->first();
                $row->fcmToken = $userDeviceDetail->fcmToken ?? null;

                $recordList[] = $row;
            }

            return response()->json([
                'messge' => 'getCallRequest Successfully',
                'status' => 200,
                'advisorOnline' => true,
                'ringTimeoutSeconds' => $defaultTimeout,
                'recordList' => $recordList,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500,
                'error' => false,
            ], 500);
        }
    }

    public function rejectCallRequest(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }
            $data = $req->only(
                'callId',
            );
            $validator = Validator::make($data, [
                'callId' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }
            $callRequest = CallRequest::find($req->callId);
            if (!$callRequest) {
                return response()->json([
                    'message' => 'Call request not found',
                    'status' => 404,
                    'error' => true,
                ], 404);
            }

            $authUserId = Auth::guard('api')->user()->id;

            // User cancelled from app → end entire call (no more advisor ringing)
            if ((int) $callRequest->userId === (int) $authUserId || $req->boolean('fromUser')) {
                $result = CallRingService::cancelByUser($callRequest);

                return response()->json([
                    'messge' => 'Call cancelled successfully',
                    'message' => 'Call cancelled successfully',
                    'status' => 200,
                    'callStatus' => 'Rejected',
                    'cancelledByUser' => true,
                    'sequential' => (bool) $callRequest->is_sequential,
                ], 200);
            }

            // Sequential: advisor reject → ring next advisor (do not end call yet)
            if ($callRequest->is_sequential && $callRequest->callStatus === 'Pending') {
                $actingAstrologerId = (int) $callRequest->astrologerId;
                // Prefer astrologer linked to logged-in advisor user
                $authAstro = DB::table('astrologers')->where('userId', $authUserId)->value('id');
                if ($authAstro) {
                    $actingAstrologerId = (int) $authAstro;
                }
                CallRingService::appendRejectedAstrologer($callRequest, $actingAstrologerId);
                $callRequest->save();

                $result = CallRingService::advanceToNextAdvisor($callRequest, true, 'rejected');

                return response()->json([
                    'messge' => !empty($result['exhausted'])
                        ? 'All advisors exhausted'
                        : 'Advisor rejected — ringing next advisor',
                    'status' => 200,
                    'sequential' => true,
                    'advanced' => $result['advanced'] ?? false,
                    'exhausted' => $result['exhausted'] ?? false,
                    'currentAstrologerId' => $result['currentAstrologerId'] ?? null,
                    'callStatus' => $result['callRequest']->callStatus ?? null,
                ], 200);
            }

            $userDeviceDetail = DB::table('user_device_details')
            ->WHERE('user_device_details.userId', '=', $callRequest->userId)
            ->SELECT('user_device_details.*')
            ->get();

            FCMService::send(
                $userDeviceDetail,
                [
                    'title' => 'Call Declined',
                    'body' => [
                        "notificationType" => 100,
                        'description' => '',
                    ],
                ]
            );

            if ($callRequest) {
                $authAstro = DB::table('astrologers')->where('userId', $authUserId)->value('id');
                CallRingService::markRejectedByAdvisor(
                    $callRequest,
                    $authAstro ? (int) $authAstro : (int) $callRequest->astrologerId
                );
                return response()->json([
                    'messge' => 'Reject Call Request Successfully',
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

    /**
     * Advance ring if 30s elapsed (poll from app) or force next advisor.
     * Body: callId (required), force (optional bool)
     */
    public function advanceRing(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }

            $validator = Validator::make($req->all(), [
                'callId' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }

            $callRequest = CallRequest::find($req->callId);
            if (!$callRequest) {
                return response()->json([
                    'message' => 'Call request not found',
                    'status' => 404,
                ], 404);
            }

            $result = CallRingService::advanceToNextAdvisor(
                $callRequest,
                $req->boolean('force'),
                $req->boolean('force') ? 'forced' : 'timeout'
            );

            return response()->json([
                'message' => $result['message'],
                'status' => 200,
                'advanced' => $result['advanced'] ?? false,
                'exhausted' => $result['exhausted'] ?? false,
                'secondsLeft' => $result['secondsLeft'] ?? null,
                'currentAstrologerId' => $result['currentAstrologerId'] ?? $callRequest->astrologerId,
                'callStatus' => $result['callRequest']->callStatus ?? $callRequest->callStatus,
                'recordList' => $result['callRequest'] ?? $callRequest,
            ], 200);
        } catch (\Exception $e) {
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
                'callId',
            );
            $validator = Validator::make($data, [
                'callId' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }
            $callRequest = CallRequest::find($req->callId);
            $callRequest->Delete();
            return response()->json([
                'messge' => 'Remove Call Request Successfully',
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

    public function acceptCallRequest(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }
            $data = $req->only(
                'callId',
            );
            $validator = Validator::make($data, [
                'callId' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }
            $callRequest = CallRequest::find($req->callId);
            if (!$callRequest) {
                return response()->json([
                    'message' => 'Call request not found',
                    'status' => 404,
                    'missed' => true,
                ], 404);
            }

            $actingAstrologerId = self::resolveActingAstrologerId($req);
            $gate = CallRingService::validateAdvisorCanTakeCall($callRequest, $actingAstrologerId);
            if (!$gate['allowed']) {
                return response()->json([
                    'messge' => $gate['message'],
                    'message' => $gate['message'],
                    'status' => 409,
                    'missed' => true,
                    'anotherJoined' => true,
                    'joinedAstrologerId' => $gate['joinedAstrologerId'],
                    'joinedAstrologerName' => $gate['joinedAstrologerName'],
                    'callStatus' => $gate['callStatus'],
                ], 409);
            }

            $currenttimestamp = Carbon::now()->timestamp;
            $callRequest->callStatus = 'Accepted';
            $callRequest->updated_at = $currenttimestamp;
            if ($actingAstrologerId) {
                $callRequest->astrologerId = $actingAstrologerId;
            }
            $callRequest->update();

            CallRingService::notifyMissedAdvisorsAfterJoin($callRequest);

            return response()->json([
                'messge' => 'call Request Accept Successfully',
                'message' => 'call Request Accept Successfully',
                'status' => 200,
                'missed' => false,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500,
                'error' => false,
            ], 500);
        }
    }

    /**
     * Advisor opens a ringing notification: check if call is still theirs.
     * Body: callId (required), astrologerId (optional if auth maps to advisor)
     */
    public function checkCallAvailability(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }

            $validator = Validator::make($req->all(), [
                'callId' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }

            $callRequest = CallRequest::find($req->callId);
            if (!$callRequest) {
                return response()->json([
                    'message' => 'Call request not found',
                    'status' => 404,
                    'missed' => true,
                    'available' => false,
                ], 404);
            }

            $actingAstrologerId = self::resolveActingAstrologerId($req);
            $gate = CallRingService::validateAdvisorCanTakeCall($callRequest, $actingAstrologerId);

            return response()->json([
                'message' => $gate['message'],
                'status' => $gate['allowed'] ? 200 : 409,
                'available' => $gate['allowed'],
                'missed' => $gate['missed'],
                'anotherJoined' => $gate['missed'],
                'joinedAstrologerId' => $gate['joinedAstrologerId'],
                'joinedAstrologerName' => $gate['joinedAstrologerName'],
                'callStatus' => $gate['callStatus'],
                'currentAstrologerId' => $callRequest->astrologerId,
            ], $gate['allowed'] ? 200 : 409);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500,
                'error' => false,
            ], 500);
        }
    }

    /**
     * Resolve advisor id from request or logged-in user.
     */
    protected static function resolveActingAstrologerId(Request $req): ?int
    {
        if (!empty($req->astrologerId)) {
            return (int) $req->astrologerId;
        }

        $userId = Auth::guard('api')->user()->id ?? null;
        if (!$userId) {
            return null;
        }

        $id = DB::table('astrologers')->where('userId', $userId)->value('id');

        return $id ? (int) $id : null;
    }

    public function storeToken(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }
            $data = $req->only(
                'callId',
                'token',
                'channelName'
            );
            $validator = Validator::make($data, [
                'callId' => 'required',
                'token' => 'required',
                'channelName' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }
            $callRequest = CallRequest::find($req->callId);
            if (!$callRequest) {
                return response()->json([
                    'message' => 'Call request not found',
                    'status' => 404,
                    'missed' => true,
                ], 404);
            }

            $actingAstrologerId = self::resolveActingAstrologerId($req);
            $gate = CallRingService::validateAdvisorCanTakeCall($callRequest, $actingAstrologerId);
            if (!$gate['allowed']) {
                return response()->json([
                    'messge' => $gate['message'],
                    'message' => $gate['message'],
                    'status' => 409,
                    'missed' => true,
                    'anotherJoined' => true,
                    'joinedAstrologerId' => $gate['joinedAstrologerId'],
                    'joinedAstrologerName' => $gate['joinedAstrologerName'],
                    'callStatus' => $gate['callStatus'],
                ], 409);
            }

            $currenttimestamp = Carbon::now()->toDateTimeString();
            $callRequest->callStatus = 'Accepted';
            $callRequest->updated_at = $currenttimestamp;
            $callRequest->token = $req->token;
            $callRequest->channelName = $req->channelName;
            if ($actingAstrologerId) {
                $callRequest->astrologerId = $actingAstrologerId;
            }
            $callRequest->update();

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
            if ($userDeviceDetail && count($userDeviceDetail) > 0) {

if ($callRequest->type == 'video') {
    $admin_charge = DB::table('systemflag')->where('name', 'VcCallCharges')->first()->value;
} else {
    $admin_charge = DB::table('systemflag')->where('name', 'CallCharges')->first()->value;
}
                $response = FCMService::send(
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
                            'call_type' => $callRequest->type,
                            'description' => '',
                            'fcmToken' => $astrologer[0]->fcmToken,
                            'charges' => strval($admin_charge)? $admin_charge : 0,
                            'isFree' => strval($callRequest->isFreeSession)
                        ],
                    ]
                );
            }
            return response()->json([
                'messge' => $response,
                'status' => 200,
                'missed' => false,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500,
                'error' => false,
            ], 500);
        }
    }

    public function endCall(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }
            $data = $req->only(
                'callId',
                'totalMin'
            );
            $validator = Validator::make($data, [
                'callId' => 'required',
                'totalMin' => 'required',
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

            $admin_charge = $callData[0]->callRate ?? DB::table('systemflag')->where('name', 'CallCharges')->first()->value;

            if (!$callData[0]->isFreeSession) {
                // ($req->totalMin / 60)
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
                // $adminCommission = 0;
                $astrologerCommission = $deduction - $adminCommission;
            }

            $callDatas = array(
                'totalMin' => $totalMin,
                'callStatus' => 'Completed',
                'deduction' => $deduction,
                // 'callRate' => !$callData[0]->isFreeSession ? $admin_charge : 0,
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
                        ->where('id', $astrologerWallet[0]->id)
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
        } catch (\Exception$e) {
            // dd($e);
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500,
                'error' => false,
            ], 500);
        }
    }

    public function rejectCallRequestFromCustomer(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }
            $data = $req->only(
                'callId',
            );
            $validator = Validator::make($data, [
                'callId' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }
            $callData = CallRequest::find($req->callId);
            if (!$callData) {
                return response()->json([
                    'message' => 'Call request not found',
                    'status' => 404,
                ], 404);
            }

            // User cancel → end call completely (stop sequential ringing)
            CallRingService::cancelByUser($callData);

            return response()->json([
                'message' => 'Call Request Rejected Successfully',
                'status' => 200,
                'callStatus' => 'Rejected',
                'cancelledByUser' => true,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500,
                'error' => false,
            ], 500);
        }
    }

    public function acceptCallRequestFromCustomer(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }
            $data = $req->only(
                'callId',
            );
            $validator = Validator::make($data, [
                'callId' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }
            $callData = CallRequest::find($req->callId);
            $currenttimestamp = Carbon::now()->timestamp;
            if ($callData) {
                $callData->callStatus = 'Confirmed';
                $callData->deduction = 0;
                $callData->updated_at = $currenttimestamp;
                $callData->totalMin = 0;
                $callData->update();
            }
            return response()->json([
                'message' => 'Call Request Accepted Successfully',
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

    public function storeCallRecording(Request $request)
    {
        try {

            // $request->validate([
            //     'record' => 'required',
            // ]);
    
            if ($request->file('record')) {
                $image = $request->file('record'); // ->store('uploads', 'public');
                $dir = 'uploads/';
                $format = $request->file('record')->getClientOriginalExtension();
                if ($image != null) {
                    $imageName = Carbon::now()->toDateString() . "-" . uniqid() . "." . $format;
                    // if (!Storage::disk('public')->exists($dir)) {
                    //     Storage::disk('public')->makeDirectory($dir);
                    // }
                    // Storage::disk('public')->put($dir . $imageName, );

                    $time = time();
                    $destinationpath = 'storage/images/';
                    $path = $destinationpath . $imageName;
                    file_put_contents(public_path($path), file_get_contents($image));

                } else {
                    $imageName = 'def.png';
                }

                $callData = CallRequest::find($request->callId);
                $callData->record = $imageName;
                $callData->save();

                return response()->json([
                    'message' => 'Image uploaded successfully!',
                    'image'=> $imageName,
                    'status' => true,
                    'error' => false,
                ], 200);
            }

            return response()->json([
                'message' => 'Recording failed',
                'image'=> '',
                'status' => false,
                'error' => true,
            ], 500);

            $storage = new StorageClient([
                'projectId' => 'realtionship-849b1', // 'astroguru-75d26',
                'keyFilePath' => storage_path('app/firebase/realtionship-849b1-d33d422c9dd4.json') // '..\storage\app\public\file.json',
            ]);
            foreach ($storage->buckets() as $bucket) {
                $bucketName = $bucket->name();
            }
            $buckets = $storage->bucket($bucketName);
            $objects = [];
            foreach ($buckets->objects() as $object) {
                array_push($objects, $object->name());
            }
            for ($i = 0; $i < count($objects); $i++) {
                $bucket = $storage->bucket($bucketName);
                $objectss = $bucket->object($objects[$i]);
                $objectss->downloadToFile(public_path("callRecording/" . $i . "_" . $objects[$i]));
            }
            return response()->json([
                'message' => $objects,
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

    public function getCallById(Request $req)
    {
        try {
            $callData = DB::table('callrequest')
                ->join('astrologers', 'astrologers.id', '=', 'callrequest.astrologerId')
                ->select('callrequest.*', 'astrologers.name as astrologerName')
                ->where('callrequest.id', '=', $req->callId)
                ->get();
            return response()->json([
                'recordList' => $callData,
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

    public function addCallStatus(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                Auth::guard('api')->user()->id;
            }
            $status = array(
                'callStatus' => $req->status,
                'callWaitTime' => ($req->status == 'Offline' || $req->status == 'Online') ? null : $req->waitTime,
            );
            DB::table('astrologers')->where('id', '=', $req->astrologerId)
                ->update($status);

            $notifiedUser = null;
            if (strcasecmp((string) $req->status, 'Online') === 0) {
                // Free this advisor if an old Accepted call never got endCall
                CallRingService::releaseStaleLiveCalls((int) $req->astrologerId);
                $notifiedUser = WaitListService::notifyNextWaitingUser($req->astrologerId);
            } elseif (strcasecmp((string) $req->status, 'Offline') === 0) {
                CallRingService::handleAdvisorWentOffline((int) $req->astrologerId);
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

    public function getMissedCalls(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }

            $userId = Auth::guard('api')->user()->id;
            $astroId = DB::table('astrologers')->where('userId', $userId)->value('id');

            if (!$astroId) {
                return response()->json([
                    'message' => 'Advisor profile not found for this token',
                    'status' => 404,
                    'error' => false,
                ], 404);
            }

            $astroId = (int) $astroId;

            $query = CallRequest::query()
                ->with(['user:id,name,contactNo,profile', 'astrologer:id,name,profileImage'])
                ->where(function ($q) use ($astroId) {
                    $q->whereJsonContains('tried_astrologer_ids', $astroId)
                        ->orWhereJsonContains('tried_astrologer_ids', (string) $astroId);
                })
                ->where('astrologerId', '!=', $astroId)
                ->where(function ($q) use ($astroId) {
                    $q->whereNull('rejected_astrologer_ids')
                        ->orWhere(function ($notRejected) use ($astroId) {
                            $notRejected->whereJsonDoesntContain('rejected_astrologer_ids', $astroId)
                                ->whereJsonDoesntContain('rejected_astrologer_ids', (string) $astroId);
                        });
                })
                ->orderByDesc('id');

            $totalCount = (clone $query)->count();

            $startIndex = (int) ($req->input('startIndex', 0));
            $fetchRecord = (int) ($req->input('fetchRecord', 20));
            if ($startIndex < 0) {
                $startIndex = 0;
            }
            if ($fetchRecord <= 0) {
                $fetchRecord = 20;
            }

            $query->skip($startIndex)->take($fetchRecord);

            $calls = $query->get();

            $recordList = $calls->map(function ($call) {
                $duration = (int) ($call->totalMin ?? 0);
                $earning = round((float) ($call->deductionFromAstrologer ?? $call->deduction ?? 0), 2);

                return [
                    'id' => $call->id,
                    'userId' => $call->userId,
                    'userName' => $call->user->name ?? '-',
                    'userProfile' => $call->user->profile ?? null,
                    'userContactNo' => $call->user->contactNo ?? null,
                    'callTime' => $call->created_at
                        ? date('d M, Y h:i A', strtotime($call->created_at))
                        : null,
                    'created_at' => $call->created_at,
                    'callType' => $call->type ?? '-',
                    'type' => $call->type ?? '-',
                    'duration' => $duration,
                    'durationText' => $duration . ' ' . ($duration > 1 ? 'minutes' : 'minute'),
                    'earning' => $earning,
                    'earningText' => '₹ ' . $earning,
                    'status' => 'Missed',
                    'callStatus' => $call->callStatus,
                    'handledBy' => $call->astrologer->name ?? ('Advisor #' . $call->astrologerId),
                    'handledByAstrologerId' => $call->astrologerId,
                    'is_sequential' => (bool) $call->is_sequential,
                ];
            })->values();

            return response()->json([
                'message' => 'Missed calls fetched successfully',
                'status' => 200,
                'astrologerId' => $astroId,
                'startIndex' => $startIndex,
                'fetchRecord' => $fetchRecord,
                'totalCount' => $totalCount,
                'recordList' => $recordList,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500,
                'error' => false,
            ], 500);
        }
    }

    public function send_notification(Request $request) {

        $userId = $request->input('user_id');
        $fcm_token = DB::table('user_device_details')
        ->WHERE('user_device_details.userId', '=', $userId)
        ->SELECT('user_device_details.*')
        ->first()->fcmToken ?? '';
        
        
        $data = [
            'title' => 'Call Declined',
            'body' => 'Hello World'
        ];
        
        $userDeviceDetail = DB::table('user_device_details')
            ->WHERE('user_device_details.userId', '=', 484)
            ->SELECT('user_device_details.*')
            ->get();
        
        $res = FCMService::send(
            $userDeviceDetail,
            [
                'title' => 'Accept Call Request',
                'body' => [
                    "astrologerId" => 103,
                    "astrologerName" => 'ABC',
                    "notificationType" => 1,
                    "profile" => '',
                    "token" => '',
                    "channelName" => '',
                    "callId" => '',
                    'call_type' => 'audio',
                    'description' => '',
                    'fcmToken' => '',
                    'charges' => strval(10),
                ],
            ]
        );

        dd($res);

        // $firebase = new FirebaseService;
        // return $firebase->sendNotification('eZ3bVoF8QVOrQCIKY9GmWe:APA91bEDRPvTlpSaDRYbduFjBfm10bt5IqET-ywzZo0skkhPxk5myzkbwW9PQVCTRxb0-BXu4eP4r910damrGQx78FB-DcWyct-PnL_k6AdSAFyQPkMjBPg', 'Call Declined', 'Hello World', $data);
    }

}
