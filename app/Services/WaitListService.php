<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\CloudMessage;

class WaitListService
{
    /**
     * When advisor becomes Online, notify the first waiting user (FIFO).
     *
     * @param  int|string  $astrologerId
     * @param  string|null $requestType  Optional filter: Chat|Audio|Video
     * @return array|null  Notified waitlist row data, or null if none
     */
    public static function notifyNextWaitingUser($astrologerId, $requestType = null): ?array
    {
        $query = DB::table('waitlist')
            ->where('astrologerId', '=', $astrologerId)
            ->where(function ($q) {
                $q->whereNull('status')
                    ->orWhereIn('status', ['Pending', 'pending', 'Waiting', 'waiting']);
            })
            ->orderBy('id', 'ASC');

        if (!empty($requestType)) {
            $query->where('requestType', $requestType);
        }

        $next = $query->first();
        if (!$next) {
            return null;
        }

        $fcmToken = $next->userFcmToken;
        if (empty($fcmToken) && !empty($next->userId)) {
            $device = DB::table('user_device_details')
                ->where('userId', $next->userId)
                ->whereNotNull('fcmToken')
                ->orderByDesc('id')
                ->first();
            $fcmToken = $device->fcmToken ?? null;
        }

        $astrologer = DB::table('astrologers')->where('id', $astrologerId)->first();

        $notificationBody = [
            'notificationType' => 13,
            'description' => 'Advisor is available now. Please join.',
            'astrologerId' => (string) $astrologerId,
            'astrologerName' => $astrologer->name ?? '',
            'waitListId' => (string) $next->id,
            'requestType' => $next->requestType ?? '',
            'userId' => (string) ($next->userId ?? ''),
            'channelName' => $next->channelName ?? '',
            'type' => 'advisor_available',
        ];

        if (!empty($fcmToken)) {
            try {
                $messaging = app('firebase.messaging');
                $message = CloudMessage::new()
                    ->withNotification([
                        'title' => 'Advisor Available',
                        'body' => $notificationBody['description'],
                    ])
                    ->withData([
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                        'body' => json_encode($notificationBody),
                    ]);
                $messaging->sendMulticast($message, [$fcmToken]);
            } catch (\Throwable $e) {
                Log::warning('Waitlist next-user FCM failed', [
                    'astrologerId' => $astrologerId,
                    'waitListId' => $next->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        DB::table('waitlist')
            ->where('id', $next->id)
            ->update([
                'status' => 'Notified',
                'updated_at' => now(),
            ]);

        return (array) $next + ['notificationStatus' => 'Notified'];
    }
}
