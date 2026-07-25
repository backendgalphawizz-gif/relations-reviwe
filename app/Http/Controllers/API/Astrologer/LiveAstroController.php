<?php

namespace App\Http\Controllers\API\Astrologer;

use App\Http\Controllers\Controller;
use App\Models\AstrologerModel\LiveAstro;
use App\services\FCMService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LiveAstroController extends Controller
{
    //Add LiveAstro
    public function addLiveAstrologer(Request $req)
    {
        try {
            //Get a user id
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }

            $data = $req->only(
                'astrologerId',
                'channelName',
                'token',
            );

            //Validate the data
            $validator = Validator::make($data, [
                'astrologerId' => 'required',
                'channelName' => 'required',
                'token' => 'required',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }
            LiveAstro::query()->where('astrologerId', $req->astrologerId)->delete();
            LiveAstro::create([
                'astrologerId' => $req->astrologerId,
                'channelName' => $req->channelName,
                'token' => $req->token,
                'room_id' => $req->room_id ?? '',
                'isActive' => true,
                'liveChatToken' => $req->liveChatToken,
            ]);
            $userDeviceDetail = DB::table('user_device_details')->join('user_roles', 'user_roles.userId', 'user_device_details.userId')->where('user_roles.roleId', 3)->get();
            $astrologer = DB::table('astrologers')->where('id', $req->astrologerId)->get();
            if ($userDeviceDetail && count($userDeviceDetail) > 0) {

                for ($i = 0; $i < count($userDeviceDetail); $i++) {
                    $follow = DB::table('astrologer_followers')
                        ->where('astrologer_followers.astrologerId', '=', $req->astrologerId)
                        ->where('astrologer_followers.userId', '=', $userDeviceDetail[$i]->userId)
                        ->select('astrologer_followers.*')
                        ->get();
                    $details = array($userDeviceDetail[$i]);
                    $response = FCMService::send(
                        collect($details),
                        [
                            'title' => $astrologer[0]->name . ' is Online!',
                            'body' => [
                                'astrologerId' => $req->astrologerId,
                                'room_id' => $req->room_id,
                                'notificationType' => 4,
                                'description' => 'Join before their waitlist grows!',
                                'isFollow' => ($follow && count($follow) > 0) ? 1 : 0,
                            ],
                        ]
                    );
                    if ($follow && count($follow)) {
                        $notification = array(
                            'userId' => $userDeviceDetail[$i]->userId,
                            'title' => $astrologer[0]->name . ' is Online!',
                            'description' => 'Join before their waitlist grows!',
                            'notificationId' => null,
                            'createdBy' => $userDeviceDetail[$i]->userId,
                            'modifiedBy' => $userDeviceDetail[$i]->userId,
                        );
                        DB::table('user_notifications')->insert($notification);
                    }
                }

            }

            return response()->json([
                'message' => $response,
                'status' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500,
                'error' => false,
            ], 500);
        }
    }

    public function getLiveAstrologer(Request $req)
    {
        try {
            $liveAstrologer = DB::table('liveastro')
                ->join('astrologers', 'astrologers.id', '=', 'liveastro.astrologerId')
                ->where('liveastro.isActive', '=', true)
                ->select('astrologers.name', 'astrologers.profileImage', 'liveastro.*', 'astrologers.charge', 'astrologers.videoCallRate')
                ->orderBy('id', 'DESC')
                ->get();
            if ($req->header('Authorization')) {
                if (!Auth::guard('api')->user()) {
                    return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
                } else {
                    $id = Auth::guard('api')->user()->id;
                }
                for ($i = 0; $i < count($liveAstrologer); $i++) {
                    $isFollow = DB::table('astrologer_followers')->where('userId', $id)->where('astrologerId', $liveAstrologer[$i]->astrologerId)->count();
                    $liveAstrologer[$i]->isFollow = $isFollow ? true : false;
                }
            }
            return response()->json([
                'message' => 'Get Live Astrologer Successfully',
                'status' => 200,
                'recordList' => $liveAstrologer,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function endLiveSession(Request $req)
    {
        try {
            $liveAstrologer = LiveAstro::query();
            $liveAstrologer->where('astrologerId', '=', $req->astrologerId);
            if ($liveAstrologer) {
                $liveAstrologer->delete();
                $userDeviceDetail = DB::table('user_device_details')->join('user_roles', 'user_roles.userId', 'user_device_details.userId')->where('user_roles.roleId', 3)->get();
                if ($userDeviceDetail && count($userDeviceDetail) > 0) {
                    for ($i = 0; $i < count($userDeviceDetail); $i++) {
                        $details = array($userDeviceDetail[$i]);
                        FCMService::send(
                            collect($details),
                            [
                                'title' => 'End Session',
                                'body' => [
                                    'astrologerId' => $req->astrologerId,
                                    'notificationType' => 14,
                                    'description' => '',
                                ],
                            ]
                        );
                    }

                }
            }

            return response()->json([
                'message' => 'Live Session End Successfully',
                'status' => 200,
                'recordList' => $liveAstrologer,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function addLiveChatToken(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }
            $data = $req->only(
                'astrologerId',
                'liveChatToken',
            );

            //Validate the data
            $validator = Validator::make($data, [
                'astrologerId' => 'required',
                'liveChatToken' => 'required',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }

            $chat = LiveAstro::query()
                ->where('astrologerId', '=', $req->astrologerId)
                ->get();

            if ($chat) {
                $chat[0]->liveChatToken = $req->liveChatToken;
                $chat[0]->update();
            }
            return response()->json([
                'message' => 'Live Chat Token Successfully',
                'status' => 200,
                'recordList' => $chat,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function getToken(Request $req)
    {
        try {
            $token = DB::table('liveastro')
                ->where('channelName', '=', $req->channelName)
                ->get();
            return response()->json([
                'message' => 'Get Token Successfully',
                'status' => 200,
                'recordList' => $token && count($token) > 0 ? $token[0]->token : null,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function getUpcomingAstrologer(Request $req)
    {
        try {
            $upcomingAstrologer = DB::table('astrologers')
                ->select(
                    'astrologers.*',
                )
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('liveastro')
                        ->whereRaw('astrologers.id = liveastro.astrologerId');
                })
                ->get();

            if ($upcomingAstrologer && count($upcomingAstrologer) > 0) {
                foreach ($upcomingAstrologer as $upcoming) {
                    $astrologerAvailability = DB::table('astrologer_availabilities')
                        ->where('astrologerId', '=', $upcoming->id)
                        ->get();

                    $working = [];
                    if ($astrologerAvailability && count($astrologerAvailability) > 0) {
                        $day = [];

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

                    }
                    $upcoming->availability = $working;
                }
            }
            return response()->json([
                'recordList' => $upcomingAstrologer,
                'status' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function searchLiveAstrologer(request $req)
    {
        try {
            $upcomingAstrologer = DB::table('astrologers')
                ->select(
                    'astrologers.*',
                )
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('liveastro')
                        ->whereRaw('astrologers.id = liveastro.astrologerId');
                })
                ->whereRaw("astrologers.name LIKE ?", ["%" . $req->searchString . "%"])
                ->get();

            if ($upcomingAstrologer && count($upcomingAstrologer) > 0) {
                foreach ($upcomingAstrologer as $upcoming) {
                    $astrologerAvailability = DB::table('astrologer_availabilities')
                        ->where('astrologerId', '=', $upcoming->id)
                        ->get();

                    $working = [];
                    if ($astrologerAvailability && count($astrologerAvailability) > 0) {
                        $day = [];

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

                    }
                    $upcoming->availability = $working;
                }
            }

            return response()->json([
                'recordList' => $upcomingAstrologer,
                'status' => 200,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }
}
