<?php

namespace App\services;

use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\CloudMessage;

class FCMService
{
    /**
     * @param  iterable<object|array>  $userDeviceDetail  rows with fcmToken
     * @param  array<string, mixed>  $notification
     */
    public static function send($userDeviceDetail, $notification)
    {
        $tokens = collect($userDeviceDetail)
            ->pluck('fcmToken')
            ->filter()
            ->unique()
            ->values()
            ->all();

        return self::sendToTokens($tokens, $notification);
    }

    /**
     * Send the same push to many FCM tokens (chunked, max 500 per request).
     *
     * @param  array<int, string>  $tokens
     * @param  array<string, mixed>  $notification
     */
    public static function sendToTokens(array $tokens, array $notification): ?array
    {
        $tokens = array_values(array_unique(array_filter($tokens)));
        if ($tokens === []) {
            return null;
        }

        try {
            $messaging = app('firebase.messaging');

            $notifPayload = [
                'title' => $notification['title'] ?? '',
                'body' => $notification['body']['description']
                    ?? (($notification['body']['userName'] ?? '')
                        ? (($notification['body']['userName'] ?? 'Customer') . ' is calling')
                        : ''),
            ];
            $image = $notification['image'] ?? ($notification['body']['image'] ?? null);
            if (!empty($image)) {
                $notifPayload['image'] = $image;
            }

            $message = CloudMessage::new()
                ->withNotification($notifPayload)
                ->withData([
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    'body' => json_encode($notification['body'] ?? []),
                ])
                ->withWebPushConfig([
                    'headers' => [
                        'Urgency' => 'high',
                    ],
                    'fcm_options' => [
                        'link' => $notification['body']['link']
                            ?? url('/advisor/dashboard'),
                    ],
                ]);

            $results = [];
            foreach (array_chunk($tokens, 500) as $chunk) {
                $results[] = $messaging->sendMulticast($message, $chunk);
            }

            return $results;
        } catch (\Throwable $th) {
            Log::warning('FCM send failed: '.$th->getMessage());

            return null;
        }
    }
}
