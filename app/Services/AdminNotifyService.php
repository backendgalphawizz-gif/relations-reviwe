<?php

namespace App\Services;

use App\services\FCMService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminNotifyService
{
    /**
     * Push (and inbox) notify all admins when a new advisor registration is submitted.
     */
    public static function notifyNewAdvisorRequest(string $advisorName, ?int $astrologerId = null, ?int $advisorUserId = null): void
    {
        try {
            $adminIds = DB::table('user_roles')
                ->where('roleId', 1)
                ->pluck('userId')
                ->unique()
                ->filter()
                ->values()
                ->all();

            if ($adminIds === []) {
                return;
            }

            $title = 'New Advisor Request';
            $description = trim($advisorName) !== ''
                ? ($advisorName . ' has submitted an advisor registration request.')
                : 'A new advisor has submitted a registration request.';

            $link = $astrologerId
                ? url('/admin/advisors/' . $astrologerId)
                : url('/admin/dashboard');

            $now = now();
            $inboxRows = [];
            foreach ($adminIds as $adminId) {
                $inboxRows[] = [
                    'userId' => $adminId,
                    'title' => $title,
                    'description' => $description,
                    'image' => null,
                    'notificationId' => null,
                    'createdBy' => $advisorUserId,
                    'modifiedBy' => $advisorUserId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            foreach (array_chunk($inboxRows, 500) as $chunk) {
                DB::table('user_notifications')->insert($chunk);
            }

            $tokens = self::collectAdminFcmTokens($adminIds);
            if ($tokens === []) {
                Log::info('AdminNotifyService: no FCM tokens for admins on new advisor request');
                return;
            }

            FCMService::sendToTokens($tokens, [
                'title' => $title,
                'body' => [
                    'description' => $description,
                    'notificationType' => 21,
                    'type' => 'new_advisor_request',
                    'astrologerId' => $astrologerId ? (string) $astrologerId : '',
                    'advisorUserId' => $advisorUserId ? (string) $advisorUserId : '',
                    'advisorName' => $advisorName,
                    'link' => $link,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::warning('AdminNotifyService::notifyNewAdvisorRequest failed: ' . $e->getMessage());
        }
    }

    /**
     * @param  array<int>  $adminIds
     * @return array<int, string>
     */
    public static function collectAdminFcmTokens(array $adminIds): array
    {
        $tokens = [];

        $users = DB::table('users')
            ->whereIn('id', $adminIds)
            ->select('fcm_token', 'desktop_token')
            ->get();

        foreach ($users as $user) {
            if (!empty($user->fcm_token)) {
                $tokens[] = $user->fcm_token;
            }
            if (!empty($user->desktop_token)) {
                $tokens[] = $user->desktop_token;
            }
        }

        $deviceTokens = DB::table('user_device_details')
            ->whereIn('userId', $adminIds)
            ->whereNotNull('fcmToken')
            ->where('fcmToken', '!=', '')
            ->pluck('fcmToken')
            ->all();

        return array_values(array_unique(array_filter(array_merge($tokens, $deviceTokens))));
    }
}
