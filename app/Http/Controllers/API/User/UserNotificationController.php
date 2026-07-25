<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\UserModel\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserNotificationController extends Controller
{
    //Add user notification
    public function addUserNotification(Request $req)
    {
        try {
            //Get a user id
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }

            $data = $req->only(
                'userId',
                'title',
                'description',
                'notificationId',
            );

            //Validate the data
            $validator = Validator::make($data, [
                'userId' => 'required',
                'title' => 'required',
                'description' => 'required',
                'notificationId' => 'required',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }

            //Create a new user notification
            $userNotification = UserNotification::create([
                'userId' => $req->userId,
                'title' => $req->title,
                'description' => $req->description,
                'notificationId' => $req->notificationId,
                'createdBy' => $id,
                'modifiedBy' => $id,
            ]);

            return response()->json([
                'message' => 'User notification add sucessfully',
                'recordList' => $userNotification,
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

    //Get all the user Notifications
    public function getUserNotification(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }
            $userNotification = UserNotification::query();
            $userNotification = $userNotification->where('userId', '=', $id)->OrderBy('id', 'DESC');
            if ($s = $req->input(key:'s')) {
                $userNotification->whereRaw("title LIKE ?", ["%" . $s . "%"]);
            }
            return response()->json([
                'recordList' => $userNotification->get(),
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

    //Update user notification
    public function updateUserNotification(Request $req, $id)
    {
        try {
            $req->validate = ([
                'userId' => 'required',
                'title' => 'required',
                'description' => 'required',
                'notificationId' => 'required',
            ]);

            $userNotification = UserNotification::find($id);
            if ($userNotification) {
                $userNotification->userId = $req->userId;
                $userNotification->title = $req->title;
                $userNotification->description = $req->description;
                $userNotification->notificationId = $req->notificationId;
                $userNotification->update();
                return response()->json([
                    'message' => 'User notification update sucessfully',
                    'recordList' => $userNotification,
                    'status' => 200,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'User notification is not found',
                    'status' => 404,
                ], 404);
            }
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    //Active/InActive User notification
    public function activeUserNotification(Request $req, $id)
    {
        try {
            $userNotification = UserNotification::find($id);
            if ($userNotification) {
                $userNotification->isActive = $req->isActive;
                $userNotification->update();
                return response()->json([
                    'message' => 'User notification status change sucessfully',
                    'recordList' => $userNotification,
                    'status' => 200,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'User notification status not change.',
                    'status' => 400,
                ], 400);
            }
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }
}
