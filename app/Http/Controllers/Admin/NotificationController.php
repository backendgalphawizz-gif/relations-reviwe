<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\services\FCMService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

define('LOGINPATH', '/admin/login');

class NotificationController extends Controller
{
    public $limit = 15;
    public $paginationStart;
    public $path;
    public function addNotification()
    {
        return view('pages.notification-list');
    }

    public function addNotificationApi(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'title' => 'required|trim',
            'description' => 'required|trim',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->getMessageBag()->toArray(),
            ]);
        }
        try {
            if (Auth::guard('web')->check()) {
                Notification::create([
                    'title' => $req->title,
                    'description' => $req->description,
                    'createdBy' => Auth()->user()->id,
                    'modifiedBy' => Auth()->user()->id,
                ]);
                return response()->json([
                    'status' => true
                ]);
                // return redirect()->route('notifications');
            } else {
                return response()->json([
                    'status' => true
                ]);
                // return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return response()->json([
                'error' => ['error' => $e->getMessage()]
            ]);
            // return dd($e->getMessage());
        }
    }

    //Get Skill Api

    public function getNotification(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $notifications = Notification::query();
                $notifications->orderBy("id", "DESC");
                $notificationCount = $notifications->count();
                $notifications->skip($paginationStart);
                $notifications->take($this->limit);
                $notifications = $notifications->get();
                $totalPages = ceil($notificationCount / $this->limit);
                $totalRecords = $notificationCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                $users = DB::Table('users')
                    ->join('user_roles', 'user_roles.userId', '=', 'users.id')
                    ->where('isDelete', '=', false)
                    ->where('isActive', '=', true)
                    // ->where('user_roles.roleId', '=', 3)
                    ->select('users.*','user_roles.roleId')
                    ->get();

                return view('pages.notification-list', compact('notifications', 'users', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function editNotification()
    {
        return view('pages.notification-list');
    }

    public function editNotificationApi(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'title' => 'required',
            'did' => 'required',
        ],[
            'did.required' => 'Description is required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->getMessageBag()->toArray(),
            ]);
        }
        try {
            if (Auth::guard('web')->check()) {
                $notification = Notification::find($req->filed_id);
                if ($notification) {
                    $notification->title = $req->title;
                    $notification->description = $req->did;
                    $notification->update();
                }
                // return redirect()->route('notifications');
                return response()->json([
                    'status' => true
                ]);
            } else {
                return response()->json([
                    'status' => true
                ]);
                // return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function notifcationStatus(Request $request)
    {
        return view('pages.notification-list');
    }

    public function notifcationStatusApi(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {

                $notification = Notification::find($request->status_id);
                if ($notification) {
                    $notification->isActive = !$notification->isActive;
                    $notification->update();
                }
                return redirect()->route('notifications');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function sendNotification(Request $req)
    {
        try {
            $notification = Notification::find($req->notification_id);
            if ($req->userIds && count(json_decode(json_encode($req->userIds))) > 0) {
                foreach (json_decode(json_encode($req->userIds)) as $user) {
                    $userDeviceDetail = DB::table('user_device_details')->where('userId', '=', $user)->get();

                    if ($userDeviceDetail && count($userDeviceDetail) > 0) {
                        $response = FCMService::send(
                            $userDeviceDetail,
                            [
                                'title' => $notification['title'],
                                'body' => ['description' => $notification['description']],
                            ]
                        );
                        $response = collect(array(json_decode($response)));
                        if ($response[0]->success == 1) {

                            $notification = array(
                                'userId' => $user,
                                'title' => $notification['title'],
                                'description' => $notification['description'],
                                'notificationId' => $req->notification_id,
                                'createdBy' => Auth()->user()->id,
                                'modifiedBy' => Auth()->user()->id,
                            );
                            DB::table('user_notifications')->insert($notification);
                        }
                    }
                }
            } elseif ($req->role && $req->role == 'User') {
                $userDeviceDetail = DB::table('user_device_details')
                    ->join('user_roles', 'user_roles.userId', '=', 'user_device_details.userId')
                    ->where('user_roles.roleId', '=', 3)
                    ->where('isActive', 1)
                    ->where('isDelete', 0)
                    ->select('user_device_details.*')
                    ->get();
                if ($userDeviceDetail && count($userDeviceDetail) > 0) {
                    foreach ($userDeviceDetail as $detail) {
                        $details = array($detail);
                        $response = FCMService::send(
                            collect($details),
                            [
                                'title' => $notification['title'],
                                'body' => ['description' => $notification['description']],
                            ]
                        );
                        $response = collect(array(json_decode($response)));
                        if ($response[0]->success == 1) {
                            $notification = array(
                                'userId' => $detail->userId,
                                'title' => $notification['title'],
                                'description' => $notification['description'],
                                'notificationId' => $req->notification_id,
                                'createdBy' => Auth()->user()->id,
                                'modifiedBy' => Auth()->user()->id,
                            );
                            DB::table('user_notifications')->insert($notification);
                        }
                    }
                }
            } elseif ($req->role && $req->role == 'Astrologer') {
                $userDeviceDetail = DB::table('user_device_details')
                    ->join('user_roles', 'user_roles.userId', '=' . 'user_device_details.userId')
                    ->where('user_roles.roleId', '=', 2)
                    ->where('isActive', 1)
                    ->where('isDelete', 0)
                    ->where('isVerified',1)
                    ->select('user_device_details.*')
                    ->get();
                if ($userDeviceDetail && count($userDeviceDetail) > 0) {
                    foreach ($userDeviceDetail as $detail) {
                        $details = array($detail);
                        $response = FCMService::send(
                            collect($details),
                            [
                                'title' => $notification['title'],
                                'body' => ['description' => $notification['description']],
                            ]
                        );
                        $response = collect(array(json_decode($response)));
                        if ($response[0]->success == 1) {
                            $notification = array(
                                'userId' => $detail->userId,
                                'title' => $notification['title'],
                                'description' => $notification['description'],
                                'notificationId' => $req->notification_id,
                                'createdBy' => Auth()->user()->id,
                                'modifiedBy' => Auth()->user()->id,
                            );
                            DB::table('user_notifications')->insert($notification);
                        }
                    }
                }
            } else {
                $userDeviceDetails = DB::table('user_device_details')
                    ->get();
                if ($userDeviceDetails && count($userDeviceDetails) > 0) {
                    foreach ($userDeviceDetails as $detail) {
                        $details = array($detail);
                        $response = FCMService::send(
                            collect($details),
                            [
                                'title' => $notification['title'],
                                'body' => ['description' => $notification['description']],
                            ]
                        );
                        $response = collect(array(json_decode($response)));
                        if ($response[0]->success == 1) {
                            $notifications = array(
                                'userId' => $detail->userId,
                                'title' => $notification['title'],
                                'description' => $notification['description'],
                                'notificationId' => $req->notification_id,
                                'createdBy' => Auth()->user()->id,
                                'modifiedBy' => Auth()->user()->id,
                            );
                            DB::table('user_notifications')->insert($notifications);
                        }

                    }
                }
            }
            return response()->json([
                'success' => ['Send Notification Successfullt'],
            ]);
        } catch (\Exception$e) {
            return response()->json([
                'error' => ['error' => $e->getMessage()]
            ]);
            // return dd($e->getMessage());
        }
    }

}
