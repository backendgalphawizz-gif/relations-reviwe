<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveApiToken
{
    /**
     * Routes that must work even with a stale Authorization header.
     */
    protected array $except = [
        'api/login',
        'api/loginAppUser',
        'api/loginAppAstrologer',
        'api/sendOtp',
        'api/verifyOtp',
        'api/user/add',
        'api/getAstrologerById',
        'api/refresh',
        'api/password/reset',
        'api/optimize-clear',
        'api/migrate',
    ];

    /**
     * Reject JWTs that were replaced by a newer login (another device) or cleared on logout.
     */
    public function handle(Request $request, Closure $next): Response
    {
        foreach ($this->except as $path) {
            if ($request->is($path)) {
                return $next($request);
            }
        }

        $bearer = $request->bearerToken();
        if (!$bearer) {
            return $next($request);
        }

        $user = Auth::guard('api')->user();
        if (!$user) {
            try {
                $user = Auth::guard('api')->setToken($bearer)->user();
            } catch (\Throwable $e) {
                return $next($request);
            }
        }

        if (!$user) {
            return $next($request);
        }

        // No active server-side token => logged out / expired session
        if (empty($user->token)) {
            return response()->json([
                'success' => false,
                'message' => 'Session expired. Please login again.',
                'status' => 401,
            ], 401);
        }

        // Token replaced by login on another device
        if (!hash_equals((string) $user->token, (string) $bearer)) {
            return response()->json([
                'success' => false,
                'message' => 'Session expired. You have logged in on another device.',
                'status' => 401,
            ], 401);
        }

        return $next($request);
    }
}
