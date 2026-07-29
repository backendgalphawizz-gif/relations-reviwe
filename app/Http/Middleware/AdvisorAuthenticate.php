<?php

namespace App\Http\Middleware;

use App\Models\UserModel\User as ApiUser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdvisorAuthenticate
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('advisor')->check()) {
            return redirect()->route('advisor.login');
        }

        $user = Auth::guard('advisor')->user();

        // Mobile/API login sets users.token — that means another device took over
        if ($user) {
            $apiUser = ApiUser::find($user->id);
            if ($apiUser && !empty($apiUser->token)) {
                Auth::guard('advisor')->logout();
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Session expired. You have logged in on another device.',
                    ], 401);
                }

                return redirect()->route('advisor.login')
                    ->with('error', 'You have logged in on another device. Please login again.');
            }
        }

        // Keep web panel device alive only while this web session is the active one
        if ($user) {
            $now = now();
            $device = DB::table('user_device_details')
                ->where('userId', $user->id)
                ->where('appId', 3)
                ->first();

            if ($device) {
                DB::table('user_device_details')->where('id', $device->id)->update([
                    'isActive' => 1,
                    'updated_at' => $now,
                ]);
            } else {
                DB::table('user_device_details')->insert([
                    'userId' => $user->id,
                    'appId' => 3,
                    'isActive' => 1,
                    'fcmToken' => '',
                    'deviceId' => $request->userAgent() ?: 'web',
                    'deviceManufacturer' => 'web',
                    'deviceModel' => 'browser',
                    'appVersion' => '1.1.0',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        $response = $next($request);

        return $response->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }
}
