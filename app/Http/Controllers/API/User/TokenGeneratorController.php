<?php

namespace App\Http\Controllers\API\User;

use App\AgoraToken\RtmTokenBuilder;
use App\AgoraToken\RtcTokenBuilder;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;


class TokenGeneratorController extends Controller {
    public function generateToken(Request $req){
        try {
            $privilegeExpiredTs = Carbon::now()->timestamp + 600;
            $rtmTokenController = new RtmTokenBuilder;
            $rtmToken = $rtmTokenController->buildToken($req->appID, $req->appCertificate, $req->user, $privilegeExpiredTs);
           
            return response()->json([
                'rtmToken' => $rtmToken,
                'status' => 200,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function generateRtcToken(Request $req){
        try {
            $privilegeExpiredTs = Carbon::now()->timestamp + 600;
            $rtcTokenController = new RtcTokenBuilder;
            $rtcToken = $rtcTokenController->buildTokenWithUid($req->appID, $req->appCertificate, $req->channelName, $req->user, 1, $privilegeExpiredTs);
            return response()->json([
                'rtcToken' => $rtcToken,
                'status' => 200,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }
}
