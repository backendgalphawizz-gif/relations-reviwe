<?php

namespace App\Services;

use App\Models\UserModel\User;
use App\Models\UserModel\UserDeviceDetail;
use App\Models\UserModel\UserLoginHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UserAuthSessionService
{
    /**
     * Activate a new login session.
     * Clears previous devices' auth activity + FCM so only the new device stays active.
     */
    public static function startSession(User $user, string $token, Request $request, ?array $deviceDetails = null): UserLoginHistory
    {
        $deviceId = $deviceDetails['deviceId'] ?? $request->input('deviceId');
        $appId = $deviceDetails['appId'] ?? $request->input('appId');
        $now = Carbon::now();

        UserLoginHistory::where('userId', $user->id)
            ->where('status', 'active')
            ->update([
                'status' => 'forced_logout',
                'logout_at' => $now,
                'updated_at' => $now,
            ]);

        // Wipe every previous device (mobile + web) — token/FCM must not stay on old devices
        UserDeviceDetail::where('userId', $user->id)->update([
            'isActive' => 0,
            'fcmToken' => '',
            'updated_at' => $now,
        ]);

        if (!empty($deviceDetails)) {
            $existing = null;
            if (!empty($appId)) {
                $existing = UserDeviceDetail::where('userId', $user->id)
                    ->where('appId', $appId)
                    ->orderByDesc('id')
                    ->first();
            }
            if (!$existing && !empty($deviceId)) {
                $existing = UserDeviceDetail::where('userId', $user->id)
                    ->where('deviceId', $deviceId)
                    ->orderByDesc('id')
                    ->first();
            }

            $payload = [
                'userId' => $user->id,
                'appId' => $deviceDetails['appId'] ?? $appId ?? 1,
                'deviceId' => $deviceDetails['deviceId'] ?? $deviceId,
                'fcmToken' => $deviceDetails['fcmToken'] ?? '',
                'deviceLocation' => $deviceDetails['deviceLocation'] ?? '',
                'deviceManufacturer' => $deviceDetails['deviceManufacturer'] ?? null,
                'deviceModel' => $deviceDetails['deviceModel'] ?? null,
                'appVersion' => $deviceDetails['appVersion'] ?? null,
                'isActive' => 1,
                'updated_at' => $now,
            ];

            if ($existing) {
                $existing->fill($payload);
                $existing->save();
            } else {
                $payload['created_at'] = $now;
                UserDeviceDetail::create($payload);
            }
        } elseif (!empty($deviceId)) {
            UserDeviceDetail::where('userId', $user->id)
                ->where('deviceId', $deviceId)
                ->update([
                    'isActive' => 1,
                    'updated_at' => $now,
                ]);
        }

        $user->token = $token;
        $user->expirationDate = $now->copy()->addMonth();

        // App login replaces web push token so previous web panel stops getting calls
        if ((int) ($deviceDetails['appId'] ?? $appId ?? 0) !== 3) {
            $user->desktop_token = null;
            if (!empty($deviceDetails['fcmToken'])) {
                $user->fcm_token = $deviceDetails['fcmToken'];
            }
        }

        $user->save();

        return UserLoginHistory::create([
            'userId' => $user->id,
            'deviceId' => $deviceDetails['deviceId'] ?? $deviceId,
            'appId' => $deviceDetails['appId'] ?? $appId,
            'fcmToken' => $deviceDetails['fcmToken'] ?? null,
            'deviceManufacturer' => $deviceDetails['deviceManufacturer'] ?? null,
            'deviceModel' => $deviceDetails['deviceModel'] ?? null,
            'appVersion' => $deviceDetails['appVersion'] ?? null,
            'deviceLocation' => $deviceDetails['deviceLocation'] ?? null,
            'ipAddress' => $request->ip(),
            'userAgent' => substr((string) $request->userAgent(), 0, 500),
            'status' => 'active',
            'login_at' => $now,
        ]);
    }

    /**
     * Advisor web panel login: clear ALL previous sessions (mobile + other browsers).
     */
    public static function invalidateApiSessionForWebLogin(User $user): void
    {
        $now = Carbon::now();

        UserLoginHistory::where('userId', $user->id)
            ->where('status', 'active')
            ->update([
                'status' => 'forced_logout',
                'logout_at' => $now,
                'updated_at' => $now,
            ]);

        UserDeviceDetail::where('userId', $user->id)->update([
            'isActive' => 0,
            'fcmToken' => '',
            'updated_at' => $now,
        ]);

        $user->token = null;
        $user->expirationDate = null;
        $user->fcm_token = null;
        $user->desktop_token = null;
        $user->save();
    }

    /**
     * End current session on logout — clears auth + FCM tokens.
     *
     * @param  int|string|null  $appId
     */
    public static function endSession(User $user, ?string $deviceId = null, $appId = null): void
    {
        $now = Carbon::now();

        $query = UserLoginHistory::where('userId', $user->id)->where('status', 'active');
        if (!empty($deviceId)) {
            $query->where('deviceId', $deviceId);
        }
        if ($appId !== null && $appId !== '') {
            $query->where('appId', $appId);
        }
        $query->update([
            'status' => 'logout',
            'logout_at' => $now,
            'updated_at' => $now,
        ]);

        $deviceQuery = UserDeviceDetail::where('userId', $user->id);
        if (!empty($deviceId)) {
            $deviceQuery->where('deviceId', $deviceId);
        }
        if ($appId !== null && $appId !== '') {
            $deviceQuery->where('appId', $appId);
        }

        $deviceQuery->update([
            'isActive' => 0,
            'fcmToken' => '',
            'updated_at' => $now,
        ]);

        $isWebOnly = $appId !== null && $appId !== '' && (int) $appId === 3;

        if (!$isWebOnly) {
            $user->token = null;
            $user->expirationDate = null;
            $user->fcm_token = null;
        }

        if ($isWebOnly || $appId === null || $appId === '') {
            $user->desktop_token = null;
        }

        $user->save();
    }

    public static function endWebSession(User $user): void
    {
        self::endSession($user, null, 3);
    }
}
