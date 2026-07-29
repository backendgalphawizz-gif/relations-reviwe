<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserModel\UserDeviceDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FCMController extends Controller
{
    public function index(Request $req)
    {
        $token = trim((string) $req->input('fcm_token', ''));
        if ($token === '') {
            return response()->json([
                'success' => false,
                'message' => 'fcm_token is required',
            ], 422);
        }

        $user = Auth::user() ?: User::find($req->input('user_id'));
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        $user->fcm_token = $token;
        $user->save();

        UserDeviceDetail::updateOrCreate(
            [
                'userId' => $user->id,
                'appId' => (int) $req->input('appId', 4),
            ],
            [
                'fcmToken' => $token,
                'deviceId' => $req->input('deviceId', 'admin-web'),
                'deviceManufacturer' => $req->input('userAgent', $req->userAgent()),
                'deviceModel' => $req->input('osName', 'web'),
                'appVersion' => $req->input('appVersion', 'admin-web'),
                'isActive' => true,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'User Updated successfully',
        ]);
    }
}

