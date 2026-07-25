<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\UserModel\WaitList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\services\FCMService;

class WaitListController extends Controller
{
    public function addWaitList(Request $req)
    {
        try {

            $waitList = WaitList::create([
                'userName' => $req->userName,
                'profile' => $req->profile,
                'time' => $req->time,
                'channelName' => $req->channelName,
                'userId' => $req->userId,
                'requestType' => $req->requestType,
                'userFcmToken' => $req->userFcmToken,
                'status' => $req->status,
                'astrologerId' => $req->astrologerId,
            ]);
            $userDeviceDetail = DB::table('user_device_details')
                ->JOIN('astrologers', 'astrologers.userId', '=', 'user_device_details.userId')
                ->WHERE('astrologers.id', '=', $req->astrologerId)
                ->SELECT('user_device_details.*')
                ->get();

            if ($req->requestType == 'Chat') {

                if ($userDeviceDetail && count($userDeviceDetail) > 0) {
                    FCMService::send(
                        $userDeviceDetail,
                        [
                            'title' => 'Get Chat Request',
                            'body' => [
                                "notificationType" => 10,
                                'description' => '',
                            ],
                        ]
                    );
                }
            }
            if ($req->requestType == 'Audio') {
                if ($userDeviceDetail && count($userDeviceDetail) > 0) {
                    FCMService::send(
                        $userDeviceDetail,
                        [
                            'title' => 'Get Call Request',
                            'body' => [
                                "notificationType" => 11,
                                'description' => '',
                            ],
                        ]
                    );
                }
            }
            if ($req->requestType == 'Video') {
                if ($userDeviceDetail && count($userDeviceDetail) > 0) {
                    FCMService::send(
                        $userDeviceDetail,
                        [
                            'title' => 'Get VideoCall Request',
                            'body' => [
                                "notificationType" => 12,
                                'description' => '',
                            ],
                        ]
                    );
                }
            }
            return response()->json([
                'message' => 'Add to waitlist successfully',
                'recordList' => $waitList,
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

    public function getWaitList(Request $req)
    {
        try {

            $waitList = DB::table('waitlist')
                ->where('channelName', '=', $req->channelName)->get();
            return response()->json([
                'message' => 'Get waitlist successfully',
                'recordList' => $waitList,
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

    public function deleteFromWaitList(Request $req)
    {
        try {

            $waitList = WaitList::find($req->id);
            $waitList->delete();
            return response()->json([
                'message' => 'Delete waitlist successfully',
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

    public function editWaitList(Request $req)
    {
        try {
            $waitList = WaitList::find($req->id);
            if ($waitList) {
                $waitList->status = $req->status;
                $waitList->update();
            }
            return response()->json([
                'message' => 'Get waitlist successfully',
                'recordList' => $waitList,
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
