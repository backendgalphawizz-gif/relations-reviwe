<?php

namespace App\Services;

use App\Models\UserModel\User;
use App\Models\UserModel\UserDeviceDetail;
use App\Models\UserModel\UserLoginHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserAuthSessionService
{
    /**
     * Activate a new login session: expire other devices' tokens/sessions and log history.
     */
    public static function startSession(User $user, string $token, Request $request, ?array $deviceDetails = null): UserLoginHistory
    {
        $deviceId = $deviceDetails['deviceId'] ?? $request->input('deviceId');
        $appId = $deviceDetails['appId'] ?? $request->input('appId');
        $now = Carbon::now();

        DB::table('user_login_histories')
            ->where('userId', $user->id)
            ->where('status', 'active')
            ->update([
                'status' => 'forced_logout',
                'logout_at' => $now,
                'updated_at' => $now,
            ]);

        $otherDevices = UserDeviceDetail::where('userId', $user->id);
        if (!empty($deviceId)) {
            $otherDevices->where(function ($q) use ($deviceId) {
                $q->where('deviceId', '!=', $deviceId)->orWhereNull('deviceId');
            });
        }
        $otherDevices->update([
            'isActive' => 0,
            'updated_at' => $now,
        ]);

        if (!empty($deviceDetails)) {
            $existing = UserDeviceDetail::where('userId', $user->id)
                ->when(!empty($deviceId), fn ($q) => $q->where('deviceId', $deviceId))
                ->when(!empty($appId), fn ($q) => $q->where('appId', $appId))
                ->first();

            $payload = [
                'userId' => $user->id,
                'appId' => $deviceDetails['appId'] ?? $appId ?? 1,
                'deviceId' => $deviceDetails['deviceId'] ?? $deviceId,
                'fcmToken' => $deviceDetails['fcmToken'] ?? null,
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
                ->update(['isActive' => 1, 'updated_at' => $now]);
        }

        $user->token = $token;
        $user->expirationDate = $now->copy()->addMonth();
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
     * End current session on logout.
     */
    public static function endSession(User $user, ?string $deviceId = null): void
    {
        $now = Carbon::now();

        $query = UserLoginHistory::where('userId', $user->id)->where('status', 'active');
        if (!empty($deviceId)) {
            $query->where('deviceId', $deviceId);
        }
        $query->update([
            'status' => 'logout',
            'logout_at' => $now,
            'updated_at' => $now,
        ]);

        if (!empty($deviceId)) {
            UserDeviceDetail::where('userId', $user->id)
                ->where('deviceId', $deviceId)
                ->update(['isActive' => 0, 'updated_at' => $now]);
        } else {
            UserDeviceDetail::where('userId', $user->id)
                ->where('isActive', 1)
                ->update(['isActive' => 0, 'updated_at' => $now]);
        }

        $user->token = null;
        $user->expirationDate = null;
        $user->save();
    }
}
