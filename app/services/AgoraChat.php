<?php

namespace App\Services;

use App\AgoraToken\RtcTokenBuilder;

class AgoraChat {

    public static function callPostApi($endPoint, $params=[], $channelName) {

        $authToken = session('api_token') ?? '';

        $appID = "";
        $appCertificate = "";
        $privilegeExpiredTs = Carbon::now()->timestamp + 600;
        $rtcTokenController = new RtcTokenBuilder;
        $rtcToken = '007eJxTYBDrTVyiuDX1WuX2jRPjN2+c8SztavGKx/usnD7cqdJ3qD2iwGBqlmJiZmBhlmJham5ilpycZGSWlppsYGBikJyYZpJqcm6DeWZDICPDocVSjIwMrAyMDEwMID4DAwAzKSAj'; // $rtcTokenController->buildTokenWithUid($appID, $appCertificate, $channelName, null, 1, $privilegeExpiredTs);

        try {
            $endUrl = env('AGORA_CHAT_BASE_URL').'/'.env('AGORA_ORG_NAME').'/'.env('AGORA_APP_NAME').'/'.$endPoint;
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $rtcToken
            ])->post($endUrl, [
               
            ]);
            // Get the API response
            return $response->json();
        } catch (\Throwable $th) {
            return ['status' => false, 'message' => $th->getMessage()];
        }
    }

    public static function checkuser($username) {

    }
}