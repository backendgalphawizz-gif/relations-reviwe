<?php



namespace app\Services;

use Kreait\Firebase\Messaging\CloudMessage;



class FCMService

{

    public static function send($userDeviceDetail, $notification)
    {
        try {
            //code...
            $messaging = app('firebase.messaging');
            $tokens = $userDeviceDetail->pluck('fcmToken')->toArray();
            $message = CloudMessage::new() // withTarget('token', $userDeviceDetail->pluck('fcmToken')->toArray())
                ->withNotification(['title' => $notification['title'], 'body' => $notification['body']['description']])
                ->withData(["click_action" => "FLUTTER_NOTIFICATION_CLICK", "body" => json_encode($notification['body'])]);
            return $messaging->sendMulticast($message, $tokens);
        } catch (\Throwable $th) {
            //throw $th;
            dd($th);
        }

    }
}
