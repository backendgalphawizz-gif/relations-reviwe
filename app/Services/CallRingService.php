<?php

namespace App\Services;

use App\Models\UserModel\CallRequest;
use App\services\FCMService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CallRingService
{
    public const DEFAULT_TIMEOUT_SECONDS = 30;

    /** Accepted/Confirmed with no endCall for this long are treated as stuck and auto-closed. */
    public const STALE_LIVE_CALL_MINUTES = 60;

    /**
     * Online advisors with an advisor app device, not busy on another live call.
     *
     * @param  array<int>  $excludeIds
     * @param  int|null  $ignoreCallId  Current call id (so this call does not mark advisor busy wrongly when advancing)
     * @return \Illuminate\Support\Collection<int, object>
     */
    public static function getAvailableAdvisors(array $excludeIds = [], ?int $ignoreCallId = null)
    {
        $timeout = (int) self::DEFAULT_TIMEOUT_SECONDS;
        // Ignore stuck Accepted/Confirmed older than this (common cause: app advisor never gets next ring)
        $liveCallMaxAgeMinutes = self::STALE_LIVE_CALL_MINUTES;

        // Busy = recent live Accepted/Confirmed, or Pending still within ring timeout.
        // Stale Accepted / overdue sequential Pending must NOT block the next advisor.
        $busyQuery = DB::table('callrequest')
            ->whereNotNull('astrologerId')
            ->where(function ($q) use ($timeout, $liveCallMaxAgeMinutes) {
                $q->where(function ($live) use ($liveCallMaxAgeMinutes) {
                    $live->whereIn('callStatus', ['Accepted', 'Confirmed'])
                        ->where(function ($recent) use ($liveCallMaxAgeMinutes) {
                            $recent->where('updated_at', '>=', Carbon::now()->subMinutes($liveCallMaxAgeMinutes))
                                ->orWhere('created_at', '>=', Carbon::now()->subMinutes($liveCallMaxAgeMinutes));
                        });
                })
                    ->orWhere(function ($pending) use ($timeout) {
                        $pending->where('callStatus', 'Pending')
                            ->where(function ($liveRing) use ($timeout) {
                                // Direct / non-sequential Pending — only recent rows count as busy
                                $liveRing->where(function ($direct) {
                                    $direct->where(function ($ns) {
                                        $ns->whereNull('is_sequential')
                                            ->orWhere('is_sequential', 0)
                                            ->orWhere('is_sequential', false);
                                    })
                                    ->where(function ($recent) {
                                        $recent->where('updated_at', '>=', Carbon::now()->subMinutes(5))
                                            ->orWhere('created_at', '>=', Carbon::now()->subMinutes(5));
                                    });
                                })
                                // Sequential still ringing (within timeout window)
                                ->orWhere(function ($seq) use ($timeout) {
                                    $seq->where(function ($s) {
                                        $s->where('is_sequential', 1)
                                            ->orWhere('is_sequential', true);
                                    })
                                    ->whereNotNull('ring_started_at')
                                    ->whereRaw(
                                        'TIMESTAMPDIFF(SECOND, ring_started_at, NOW()) < COALESCE(ring_timeout_seconds, ?)',
                                        [$timeout]
                                    );
                                });
                            });
                    });
            });

        if ($ignoreCallId) {
            $busyQuery->where('id', '!=', $ignoreCallId);
        }

        $busyAstrologerIds = $busyQuery
            ->pluck('astrologerId')
            ->unique()
            ->filter()
            ->all();

        $exclude = array_values(array_unique(array_merge($excludeIds, $busyAstrologerIds)));

        $query = DB::table('astrologers')
            ->where('isDelete', 0)
            ->where(function ($q) {
                $q->whereNull('isActive')->orWhere('isActive', 1);
            })
            ->whereRaw('LOWER(TRIM(callStatus)) = ?', ['online'])
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('user_device_details')
                    ->whereColumn('user_device_details.userId', 'astrologers.userId')
                    ->where('user_device_details.isActive', 1)
                    ->where(function ($device) {
                        // App/web with FCM token
                        $device->where(function ($withToken) {
                            $withToken->whereIn('user_device_details.appId', [2, 3])
                                ->whereNotNull('user_device_details.fcmToken')
                                ->where('user_device_details.fcmToken', '!=', '');
                        })
                        // Web panel logged in (can receive via RTDB / polling even without FCM)
                        ->orWhere(function ($webOnly) {
                            $webOnly->where('user_device_details.appId', 3);
                        });
                    });
            })
            // 1) Mobile app (appId 2 + FCM), 2) any FCM, 3) web-only — web miss then advances to app
            ->orderByRaw("
                (
                    EXISTS (
                        SELECT 1 FROM user_device_details ud
                        WHERE ud.userId = astrologers.userId
                          AND ud.isActive = 1
                          AND ud.appId = 2
                          AND ud.fcmToken IS NOT NULL
                          AND ud.fcmToken != ''
                    )
                ) DESC,
                (
                    EXISTS (
                        SELECT 1 FROM user_device_details ud
                        WHERE ud.userId = astrologers.userId
                          AND ud.isActive = 1
                          AND ud.appId IN (2, 3)
                          AND ud.fcmToken IS NOT NULL
                          AND ud.fcmToken != ''
                    )
                    OR EXISTS (
                        SELECT 1 FROM users u
                        WHERE u.id = astrologers.userId
                          AND (
                            (u.fcm_token IS NOT NULL AND u.fcm_token != '')
                            OR (u.desktop_token IS NOT NULL AND u.desktop_token != '')
                          )
                    )
                ) DESC
            ")
            ->orderBy('astrologers.id', 'ASC')
            ->select('astrologers.*');

        if (!empty($exclude)) {
            $query->whereNotIn('astrologers.id', $exclude);
        }

        return $query->get();
    }

    /**
     * Close any open Pending calls for this user so they cannot create duplicate rings.
     */
    public static function cancelUserPendingCalls(int $userId, ?int $exceptCallId = null): void
    {
        $query = CallRequest::query()
            ->where('userId', $userId)
            ->where('callStatus', 'Pending');

        if ($exceptCallId) {
            $query->where('id', '!=', $exceptCallId);
        }

        $pending = $query->get();
        foreach ($pending as $call) {
            self::clearWebIncomingCall($call);
            $call->callStatus = 'Rejected';
            // Replaced by a newer request — treat as timeout/miss for Rejected By
            if (empty($call->rejected_by)) {
                $call->rejected_by = 'timeout';
            }
            $call->is_sequential = false;
            $call->ring_started_at = null;
            $call->updated_at = Carbon::now();
            $call->save();
        }
    }

    /**
     * Advisor availability for receiving call rings / call-related pushes.
     */
    public static function isAdvisorOnline(int $astrologerId): bool
    {
        $status = DB::table('astrologers')
            ->where('id', $astrologerId)
            ->where('isDelete', 0)
            ->value('callStatus');

        return strcasecmp((string) $status, 'Online') === 0;
    }

    /**
     * True when advisor has a recent ringing/live call (stale Accepted/Pending ignored).
     */
    public static function isAdvisorBusyOnLiveCall(int $astrologerId, ?int $ignoreCallId = null): bool
    {
        $now = Carbon::now();

        $live = DB::table('callrequest')
            ->where('astrologerId', $astrologerId)
            ->whereIn('callStatus', ['Accepted', 'Confirmed'])
            ->where(function ($q) use ($now) {
                $q->where('updated_at', '>=', $now->copy()->subMinutes(self::STALE_LIVE_CALL_MINUTES))
                    ->orWhere('created_at', '>=', $now->copy()->subMinutes(self::STALE_LIVE_CALL_MINUTES));
            });
        if ($ignoreCallId) {
            $live->where('id', '!=', $ignoreCallId);
        }
        if ($live->exists()) {
            return true;
        }

        $timeout = (int) self::DEFAULT_TIMEOUT_SECONDS;
        $pending = DB::table('callrequest')
            ->where('astrologerId', $astrologerId)
            ->where('callStatus', 'Pending')
            ->where(function ($q) use ($now, $timeout) {
                $q->where(function ($seq) use ($timeout) {
                    $seq->where(function ($s) {
                        $s->where('is_sequential', 1)->orWhere('is_sequential', true);
                    })
                        ->whereNotNull('ring_started_at')
                        ->whereRaw(
                            'TIMESTAMPDIFF(SECOND, ring_started_at, NOW()) < COALESCE(ring_timeout_seconds, ?)',
                            [$timeout]
                        );
                })
                    ->orWhere(function ($direct) use ($now) {
                        $direct->where(function ($ns) {
                            $ns->whereNull('is_sequential')
                                ->orWhere('is_sequential', 0)
                                ->orWhere('is_sequential', false);
                        })
                            ->where(function ($recent) use ($now) {
                                $recent->where('updated_at', '>=', $now->copy()->subMinutes(5))
                                    ->orWhere('created_at', '>=', $now->copy()->subMinutes(5));
                            });
                    });
            });
        if ($ignoreCallId) {
            $pending->where('id', '!=', $ignoreCallId);
        }

        return $pending->exists();
    }

    /**
     * Close Accepted/Confirmed calls that never received endCall (frees stuck advisors like Veeru).
     * Does not charge wallet — only clears status so they can receive new rings.
     */
    public static function releaseStaleLiveCalls(?int $astrologerId = null): int
    {
        $cutoff = Carbon::now()->subMinutes(self::STALE_LIVE_CALL_MINUTES);

        $query = CallRequest::query()
            ->whereIn('callStatus', ['Accepted', 'Confirmed'])
            ->where(function ($q) use ($cutoff) {
                $q->where('updated_at', '<', $cutoff)
                    ->where(function ($created) use ($cutoff) {
                        $created->whereNull('created_at')
                            ->orWhere('created_at', '<', $cutoff);
                    });
            })
            ->where(function ($q) {
                // Never billed / never ended properly
                $q->whereNull('totalMin')
                    ->orWhere('totalMin', '')
                    ->orWhere('totalMin', 0);
            });

        if ($astrologerId) {
            $query->where('astrologerId', $astrologerId);
        }

        $count = 0;
        foreach ($query->get() as $call) {
            try {
                self::clearWebIncomingCall($call);
                $call->callStatus = 'Completed';
                $call->totalMin = $call->totalMin ?: 0;
                $call->deduction = $call->deduction ?: 0;
                $call->deductionFromAstrologer = $call->deductionFromAstrologer ?: 0;
                $call->is_sequential = false;
                $call->ring_started_at = null;
                $call->updated_at = Carbon::now();
                $call->save();
                $count++;

                Log::info('Released stale live call (no endCall)', [
                    'callId' => $call->id,
                    'astrologerId' => $call->astrologerId,
                    'userId' => $call->userId,
                    'staleMinutes' => self::STALE_LIVE_CALL_MINUTES,
                ]);
            } catch (\Throwable $e) {
                Log::warning('Failed to release stale live call', [
                    'callId' => $call->id ?? null,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $count;
    }

    /**
     * Advisor has at least one advisor app/web device with a non-empty FCM token.
     * Also treats users.fcm_token / desktop_token as valid.
     */
    public static function advisorHasFcmToken(int $astrologerId): bool
    {
        if (self::getAdvisorDevicesUnique($astrologerId, false)->isNotEmpty()) {
            return true;
        }

        $userId = DB::table('astrologers')->where('id', $astrologerId)->value('userId');
        if (!$userId) {
            return false;
        }

        $user = DB::table('users')->where('id', $userId)->select('fcm_token', 'desktop_token')->first();
        if (!$user) {
            return false;
        }

        return (!empty($user->fcm_token) && trim((string) $user->fcm_token) !== '')
            || (!empty($user->desktop_token) && trim((string) $user->desktop_token) !== '');
    }

    /**
     * Active advisor web panel session (appId 3), used when browser FCM is unavailable.
     */
    public static function advisorHasWebSession(int $astrologerId): bool
    {
        return DB::table('user_device_details')
            ->join('astrologers', 'astrologers.userId', '=', 'user_device_details.userId')
            ->where('astrologers.id', $astrologerId)
            ->where('user_device_details.appId', 3)
            ->where('user_device_details.isActive', 1)
            ->exists();
    }

    /**
     * Unique advisor-app devices (dedupe by fcmToken) for one astrologer.
     * Only returns devices when the advisor is Online — offline advisors never get call pushes.
     */
    public static function getAdvisorDevicesUnique(int $astrologerId, bool $requireOnline = true)
    {
        if ($requireOnline && !self::isAdvisorOnline($astrologerId)) {
            return collect();
        }

        return DB::table('user_device_details')
            ->join('astrologers', 'astrologers.userId', '=', 'user_device_details.userId')
            ->where('astrologers.id', '=', $astrologerId)
            ->where(function ($q) {
                $q->where('user_device_details.appId', '=', 3)
                    ->orWhere('user_device_details.appId', '=', 2);
            })
            ->whereNotNull('user_device_details.fcmToken')
            ->where('user_device_details.fcmToken', '!=', '')
            ->select('user_device_details.*')
            ->orderByDesc('user_device_details.id')
            ->get()
            ->unique('fcmToken')
            ->values();
    }

    /**
     * When an advisor goes Offline: clear ringing UI and advance any Pending sequential ring on them.
     */
    public static function handleAdvisorWentOffline(int $astrologerId): void
    {
        try {
            $pending = CallRequest::query()
                ->where('astrologerId', $astrologerId)
                ->where('callStatus', 'Pending')
                ->get();

            foreach ($pending as $call) {
                self::clearWebIncomingCall($call);

                if ($call->is_sequential) {
                    self::advanceToNextAdvisor($call, true, 'advisor_offline');
                }
            }
        } catch (\Throwable $e) {
            Log::warning('handleAdvisorWentOffline failed', [
                'astrologerId' => $astrologerId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Direct call to one advisor only (no sequential routing to others).
     */
    public static function startDirectCall(
        int $userId,
        int $astrologerId,
        string $type = 'audio',
        bool $isFreeSession = false
    ): array {
        self::cancelUserPendingCalls($userId);

        $astrologer = DB::table('astrologers')
            ->where('id', $astrologerId)
            ->where('isDelete', 0)
            ->first();

        if (!$astrologer) {
            return ['ok' => false, 'message' => 'Astrologer not found', 'callRequest' => null];
        }

        if (!self::isAdvisorOnline($astrologerId)) {
            return ['ok' => false, 'message' => 'right now astrologer not available', 'callRequest' => null];
        }

        // Direct call: no FCM token → do not create call (friendly message, not technical FCM text)
        if (!self::advisorHasFcmToken($astrologerId)) {
            return ['ok' => false, 'message' => 'right now astrologer not available', 'callRequest' => null];
        }

        if (self::isAdvisorBusyOnLiveCall($astrologerId)) {
            return ['ok' => false, 'message' => 'Astrologer is busy on another call', 'callRequest' => null];
        }

        $callRate = self::resolveCallRate($type);

        $callRequest = CallRequest::create([
            'astrologerId' => $astrologerId,
            'type' => $type,
            'userId' => $userId,
            'callRate' => $callRate,
            'callStatus' => 'Pending',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'isFreeSession' => $isFreeSession ? 1 : 0,
            'is_sequential' => false,
            'tried_astrologer_ids' => [$astrologerId],
            'ring_started_at' => Carbon::now(),
            'ring_timeout_seconds' => self::DEFAULT_TIMEOUT_SECONDS,
        ]);

        self::notifyAdvisorIncomingCall($callRequest);

        return [
            'ok' => true,
            'message' => 'Call Request Send Successfully',
            'callRequest' => $callRequest,
            'currentAstrologerId' => $astrologerId,
            'ringTimeoutSeconds' => self::DEFAULT_TIMEOUT_SECONDS,
        ];
    }

    /**
     * Start sequential ringing: pick first available advisor and notify.
     */
    public static function startSequentialCall(int $userId, string $type = 'audio', bool $isFreeSession = false): array
    {
        $result = DB::transaction(function () use ($userId, $type, $isFreeSession) {
            self::cancelUserPendingCalls($userId);
            // Free any advisor stuck on Accepted without endCall before picking next
            self::releaseStaleLiveCalls();

            $advisors = self::getAvailableAdvisors();
            if ($advisors->isEmpty()) {
                return [
                    'ok' => false,
                    'message' => 'Advisor not available right now',
                    'callRequest' => null,
                ];
            }

            $first = $advisors->first();
            $callRate = self::resolveCallRate($type);

            $callRequest = CallRequest::create([
                'astrologerId' => $first->id,
                'type' => $type,
                'userId' => $userId,
                'callRate' => $callRate,
                'callStatus' => 'Pending',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'isFreeSession' => $isFreeSession ? 1 : 0,
                'is_sequential' => true,
                'tried_astrologer_ids' => [$first->id],
                'ring_started_at' => Carbon::now(),
                'ring_timeout_seconds' => self::DEFAULT_TIMEOUT_SECONDS,
            ]);

            return [
                'ok' => true,
                'message' => 'Call Request Send Successfully',
                'callRequest' => $callRequest,
                'currentAstrologerId' => $first->id,
                'ringTimeoutSeconds' => self::DEFAULT_TIMEOUT_SECONDS,
            ];
        });

        // Notify only AFTER commit so callRequest/get can see the row when FCM arrives
        if (!empty($result['ok']) && !empty($result['callRequest'])) {
            self::notifyAdvisorIncomingCall($result['callRequest']);
        }

        return $result;
    }

    /**
     * If still Pending after timeout (or force), move to next advisor.
     * On reject of current advisor, pass $force = true.
     */
    public static function advanceToNextAdvisor(CallRequest $callRequest, bool $force = false, string $reason = 'timeout'): array
    {
        $callRequest->refresh();

        if (!$callRequest->is_sequential) {
            return ['advanced' => false, 'message' => 'Not a sequential call', 'callRequest' => $callRequest];
        }

        if (!in_array($callRequest->callStatus, ['Pending'], true)) {
            return ['advanced' => false, 'message' => 'Call already ' . $callRequest->callStatus, 'callRequest' => $callRequest];
        }

        if (!$force) {
            $started = $callRequest->ring_started_at
                ? Carbon::parse($callRequest->ring_started_at)
                : null;
            $timeout = (int) ($callRequest->ring_timeout_seconds ?: self::DEFAULT_TIMEOUT_SECONDS);
            if ($started && $started->diffInSeconds(Carbon::now()) < $timeout) {
                return [
                    'advanced' => false,
                    'message' => 'Still within ring timeout',
                    'secondsLeft' => $timeout - $started->diffInSeconds(Carbon::now()),
                    'callRequest' => $callRequest,
                ];
            }
        }

        $previousAstrologerId = $callRequest->astrologerId ? (int) $callRequest->astrologerId : null;

        // Explicit advisor Reject → keep them in Rejected Call list
        if ($reason === 'rejected' && $previousAstrologerId) {
            self::appendRejectedAstrologer($callRequest, $previousAstrologerId);
        }

        $tried = self::normalizeTriedIds($callRequest->tried_astrologer_ids);
        if ($previousAstrologerId && !in_array($previousAstrologerId, $tried, true)) {
            $tried[] = $previousAstrologerId;
        }

        // Exclude current (just timed out / rejected) plus already tried
        $exclude = $tried;
        $nextAdvisors = self::getAvailableAdvisors($exclude, (int) $callRequest->id);

        if ($nextAdvisors->isEmpty()) {
            self::clearWebIncomingCall($callRequest);
            $callRequest->callStatus = 'Rejected';
            // Exhausted queue: advisor Reject → advisor; ring timeout / offline → timeout (Time Over)
            $callRequest->rejected_by = ($reason === 'rejected') ? 'advisor' : 'timeout';
            $callRequest->updated_at = Carbon::now();
            $callRequest->tried_astrologer_ids = $tried;
            $callRequest->save();

            self::notifyUserNoAdvisorAvailable($callRequest);

            return [
                'advanced' => false,
                'exhausted' => true,
                'message' => 'No more advisors available',
                'reason' => $reason,
                'previousAstrologerId' => $previousAstrologerId,
                'callRequest' => $callRequest,
            ];
        }

        $next = $nextAdvisors->first();
        $tried[] = (int) $next->id;

        // Clear previous advisor's web ringing UI before switching
        self::clearWebIncomingCall($callRequest);

        $callRequest->astrologerId = $next->id;
        $callRequest->tried_astrologer_ids = array_values(array_unique($tried));
        $callRequest->ring_started_at = Carbon::now();
        $callRequest->callStatus = 'Pending';
        $callRequest->is_sequential = true;
        $callRequest->ring_timeout_seconds = (int) ($callRequest->ring_timeout_seconds ?: self::DEFAULT_TIMEOUT_SECONDS);
        $callRequest->updated_at = Carbon::now();
        $callRequest->save();
        $callRequest->refresh();

        Log::info('Sequential call advanced to next advisor', [
            'callId' => $callRequest->id,
            'reason' => $reason,
            'previousAstrologerId' => $previousAstrologerId,
            'nextAstrologerId' => (int) $next->id,
            'hasFcm' => self::advisorHasFcmToken((int) $next->id),
            'hasWebSession' => self::advisorHasWebSession((int) $next->id),
        ]);

        // Previous advisor still has the ringing UI — tell them this call moved on
        // (skip "missed" FCM when they explicitly rejected or went offline)
        if ($previousAstrologerId && !in_array($reason, ['rejected', 'advisor_offline'], true)) {
            self::notifyAdvisorMissedCall(
                $callRequest,
                (int) $previousAstrologerId,
                (int) $next->id,
                false
            );
        }

        // Always notify next advisor (app FCM + web RTDB) after web miss / timeout
        self::notifyAdvisorIncomingCall($callRequest);
        self::notifyUserRingingNext($callRequest, $previousAstrologerId, $reason);

        return [
            'advanced' => true,
            'exhausted' => false,
            'message' => 'Moved to next advisor',
            'reason' => $reason,
            'previousAstrologerId' => $previousAstrologerId,
            'currentAstrologerId' => $next->id,
            'callRequest' => $callRequest,
        ];
    }

    /**
     * User cancelled the call — stop sequential ringing and clear the call.
     */
    public static function cancelByUser(CallRequest $callRequest): array
    {
        $callRequest->refresh();
        $astrologerId = $callRequest->astrologerId;
        $callId = $callRequest->id;
        $callType = $callRequest->type;
        $wasSequential = (bool) $callRequest->is_sequential;

        // Notify currently ringing advisor that user cancelled
        if ($astrologerId) {
            try {
                $devices = self::getAdvisorDevicesUnique((int) $astrologerId);

                if ($devices->isNotEmpty()) {
                    FCMService::send(
                        $devices,
                        [
                            'title' => 'Call Cancelled',
                            'body' => [
                                'notificationType' => 99,
                                'description' => 'User cancelled the call.',
                                'type' => 'call_cancelled_by_user',
                                'callId' => (string) $callId,
                                'astrologerId' => (string) $astrologerId,
                                'call_type' => $callType,
                            ],
                        ]
                    );
                }
            } catch (\Throwable $e) {
                Log::warning('User cancel advisor notify failed', [
                    'callId' => $callId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // End call so schedule will not advance to next advisor
        self::clearWebIncomingCall($callRequest);
        $callRequest->callStatus = 'Rejected';
        $callRequest->rejected_by = 'customer';
        $callRequest->is_sequential = false;
        $callRequest->ring_started_at = null;
        $callRequest->updated_at = Carbon::now();
        $callRequest->save();

        return [
            'cancelled' => true,
            'callId' => $callId,
            'wasSequential' => $wasSequential,
            'callStatus' => 'Rejected',
        ];
    }

    /**
     * After an advisor accepts, notify all earlier rung advisors they missed it.
     */
    public static function notifyMissedAdvisorsAfterJoin(CallRequest $callRequest): void
    {
        self::clearWebIncomingCall($callRequest);

        if (!$callRequest->is_sequential) {
            return;
        }

        $joinedId = (int) $callRequest->astrologerId;
        $tried = self::normalizeTriedIds($callRequest->tried_astrologer_ids);

        foreach ($tried as $advisorId) {
            if ((int) $advisorId === $joinedId) {
                continue;
            }
            self::notifyAdvisorMissedCall($callRequest, (int) $advisorId, $joinedId, true);
        }
    }

    /**
     * Can this advisor still take the call? If not, return missed-call payload.
     *
     * @return array{allowed: bool, missed: bool, message: string, joinedAstrologerId: int|null, joinedAstrologerName: string|null, callStatus: string|null}
     */
    public static function validateAdvisorCanTakeCall(CallRequest $callRequest, ?int $astrologerId): array
    {
        $joinedId = (int) $callRequest->astrologerId;
        $joinedName = DB::table('astrologers')->where('id', $joinedId)->value('name');
        $status = $callRequest->callStatus;
        $tried = self::normalizeTriedIds($callRequest->tried_astrologer_ids);

        $missedMessage = 'This is a call you missed. Another astrologer has joined.';

        // Already accepted / in progress / done by someone else
        if (in_array($status, ['Accepted', 'Confirmed', 'Completed'], true)) {
            if ($astrologerId && (int) $astrologerId === $joinedId) {
                return [
                    'allowed' => true,
                    'missed' => false,
                    'message' => 'You already joined this call',
                    'joinedAstrologerId' => $joinedId,
                    'joinedAstrologerName' => $joinedName,
                    'callStatus' => $status,
                ];
            }

            return [
                'allowed' => false,
                'missed' => true,
                'message' => $missedMessage,
                'joinedAstrologerId' => $joinedId,
                'joinedAstrologerName' => $joinedName,
                'callStatus' => $status,
            ];
        }

        if ($status === 'Rejected') {
            return [
                'allowed' => false,
                'missed' => true,
                'message' => 'This call is no longer available.',
                'joinedAstrologerId' => null,
                'joinedAstrologerName' => null,
                'callStatus' => $status,
            ];
        }

        // Pending but already moved to another advisor
        if ($callRequest->is_sequential && $astrologerId && (int) $astrologerId !== $joinedId) {
            $wasTried = in_array((int) $astrologerId, $tried, true);
            $pendingMessage = $joinedName
                ? "This is a call you missed. Another astrologer ({$joinedName}) is handling this request."
                : 'This is a call you missed. Another astrologer is handling this request.';

            return [
                'allowed' => false,
                'missed' => true,
                'message' => $wasTried ? $pendingMessage : 'This call is ringing another advisor.',
                'joinedAstrologerId' => $joinedId,
                'joinedAstrologerName' => $joinedName,
                'callStatus' => $status,
            ];
        }

        return [
            'allowed' => true,
            'missed' => false,
            'message' => 'OK',
            'joinedAstrologerId' => $joinedId,
            'joinedAstrologerName' => $joinedName,
            'callStatus' => $status,
        ];
    }

    /**
     * Push to an advisor who no longer owns this ringing call.
     */
    public static function notifyAdvisorMissedCall(
        CallRequest $callRequest,
        int $missedAstrologerId,
        ?int $joinedAstrologerId = null,
        bool $anotherJoined = false
    ): void {
        try {
            // Offline advisors must not receive any call-related pushes
            if (!self::isAdvisorOnline($missedAstrologerId)) {
                return;
            }

            $joinedName = null;
            if ($joinedAstrologerId) {
                $joinedName = DB::table('astrologers')->where('id', $joinedAstrologerId)->value('name');
            }

            $description = $anotherJoined
                ? 'This is a call you missed. Another astrologer has joined.'
                : 'This is a call you missed. The request moved to another advisor.';

            $devices = self::getAdvisorDevicesUnique((int) $missedAstrologerId);

            if ($devices->isEmpty()) {
                return;
            }

            FCMService::send(
                $devices,
                [
                    'title' => 'Call Missed',
                    'body' => [
                        'notificationType' => 16,
                        'description' => $description,
                        'type' => 'call_missed_another_joined',
                        'callId' => (string) $callRequest->id,
                        'missedAstrologerId' => (string) $missedAstrologerId,
                        'joinedAstrologerId' => $joinedAstrologerId ? (string) $joinedAstrologerId : '',
                        'joinedAstrologerName' => $joinedName ?? '',
                        'anotherJoined' => $anotherJoined,
                        'call_type' => $callRequest->type,
                        'callStatus' => $callRequest->callStatus,
                    ],
                ]
            );
        } catch (\Throwable $e) {
            Log::warning('Sequential call missed-advisor notify failed', [
                'callId' => $callRequest->id,
                'missedAstrologerId' => $missedAstrologerId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Advance all overdue sequential Pending calls (scheduler / poll).
     */
    public static function advanceOverdueCalls(): int
    {
        // Free advisors stuck on Accepted without endCall before advancing rings
        self::releaseStaleLiveCalls();

        $count = 0;
        $calls = CallRequest::query()
            ->where(function ($q) {
                $q->where('is_sequential', true)
                    ->orWhere('is_sequential', 1);
            })
            ->where('callStatus', 'Pending')
            ->whereNotNull('ring_started_at')
            ->get();

        foreach ($calls as $call) {
            $timeout = (int) ($call->ring_timeout_seconds ?: self::DEFAULT_TIMEOUT_SECONDS);
            $started = Carbon::parse($call->ring_started_at);
            if ($started->diffInSeconds(Carbon::now()) >= $timeout) {
                $result = self::advanceToNextAdvisor($call, true, 'timeout');
                if (!empty($result['advanced']) || !empty($result['exhausted'])) {
                    $count++;
                }
            }
        }

        return $count;
    }

    /**
     * Collect all FCM tokens for an advisor (device rows + users.fcm_token / desktop_token).
     *
     * @return array<int, string>
     */
    public static function collectAdvisorFcmTokens(int $astrologerId, bool $requireOnline = true): array
    {
        if ($requireOnline && !self::isAdvisorOnline($astrologerId)) {
            return [];
        }

        $tokens = self::getAdvisorDevicesUnique($astrologerId, false)
            ->pluck('fcmToken')
            ->filter(fn ($t) => is_string($t) && trim($t) !== '')
            ->values()
            ->all();

        $userId = DB::table('astrologers')->where('id', $astrologerId)->value('userId');
        if ($userId) {
            $user = DB::table('users')->where('id', $userId)->select('fcm_token', 'desktop_token')->first();
            if ($user) {
                if (!empty($user->fcm_token) && trim((string) $user->fcm_token) !== '') {
                    $tokens[] = trim((string) $user->fcm_token);
                }
                if (!empty($user->desktop_token) && trim((string) $user->desktop_token) !== '') {
                    $tokens[] = trim((string) $user->desktop_token);
                }
            }
        }

        return array_values(array_unique($tokens));
    }

    public static function notifyAdvisorIncomingCall(CallRequest $callRequest): void
    {
        try {
            $astrologerId = (int) $callRequest->astrologerId;
            if (!$astrologerId || !self::isAdvisorOnline($astrologerId)) {
                return;
            }

            // Always try web RTDB (web panel) + FCM (app) so miss on web can still reach app next
            self::publishWebIncomingCall($callRequest);

            $tokens = self::collectAdvisorFcmTokens($astrologerId, false);
            if ($tokens === []) {
                Log::info('Incoming call: no FCM tokens (web RTDB only)', [
                    'callId' => $callRequest->id,
                    'astrologerId' => $astrologerId,
                ]);
                return;
            }

            $customer = DB::table('users')->where('id', $callRequest->userId)->first();
            $customerName = $customer->name ?? 'Customer';
            $callType = $callRequest->type ?? 'audio';

            FCMService::sendToTokens(
                $tokens,
                [
                    'title' => 'Incoming Call',
                    'body' => [
                        'notificationType' => 2,
                        'description' => $customerName . ' is calling (' . $callType . ')',
                        'userName' => $customerName,
                        'type' => 'call_request',
                        'call_type' => $callType,
                        'callId' => (string) $callRequest->id,
                        'astrologerId' => (string) $callRequest->astrologerId,
                        'is_sequential' => (bool) $callRequest->is_sequential,
                        'ring_timeout_seconds' => (string) ($callRequest->ring_timeout_seconds ?: self::DEFAULT_TIMEOUT_SECONDS),
                        'link' => route('advisor.dashboard'),
                    ],
                ]
            );
        } catch (\Throwable $e) {
            Log::warning('Call advisor notify failed', [
                'callId' => $callRequest->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Firebase RTDB client with explicit database URL (Admin default URL is often wrong).
     */
    protected static function firebaseDatabase()
    {
        static $database = null;
        if ($database !== null) {
            return $database;
        }

        $credentials = base_path((string) env('FIREBASE_CREDENTIALS'));
        $databaseUrl = env('FIREBASE_DATABASE_URL')
            ?: config('services.firebase.databaseURL')
            ?: 'https://realtionship-849b1-default-rtdb.firebaseio.com';

        $factory = (new \Kreait\Firebase\Factory)->withServiceAccount($credentials);
        if (!empty($databaseUrl)) {
            $factory = $factory->withDatabaseUri($databaseUrl);
        }

        $database = $factory->createDatabase();

        return $database;
    }

    /**
     * Push incoming call to Firebase RTDB for advisor web panel realtime UI.
     */
    public static function publishWebIncomingCall(CallRequest $callRequest): void
    {
        try {
            if (!self::isAdvisorOnline((int) $callRequest->astrologerId)) {
                return;
            }

            $astrologer = DB::table('astrologers')->where('id', $callRequest->astrologerId)->first();
            if (!$astrologer || empty($astrologer->userId)) {
                return;
            }

            $customer = DB::table('users')->where('id', $callRequest->userId)->first();

            self::firebaseDatabase()
                ->getReference('webAdvisorIncoming/' . $astrologer->userId . '/' . $callRequest->id)
                ->set([
                    'callId' => (int) $callRequest->id,
                    'astrologerId' => (int) $callRequest->astrologerId,
                    'userId' => (int) $callRequest->userId,
                    'userName' => $customer->name ?? 'Customer',
                    'type' => $callRequest->type ?? 'audio',
                    'callStatus' => $callRequest->callStatus ?? 'Pending',
                    'isSequential' => (bool) $callRequest->is_sequential,
                    'ringTimeoutSeconds' => (int) ($callRequest->ring_timeout_seconds ?: self::DEFAULT_TIMEOUT_SECONDS),
                    'createdAt' => time(),
                ]);
        } catch (\Throwable $e) {
            Log::warning('Web incoming RTDB publish failed', [
                'callId' => $callRequest->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public static function clearWebIncomingCall(CallRequest $callRequest): void
    {
        try {
            $astrologer = DB::table('astrologers')->where('id', $callRequest->astrologerId)->first();
            if (!$astrologer || empty($astrologer->userId)) {
                return;
            }

            self::firebaseDatabase()
                ->getReference('webAdvisorIncoming/' . $astrologer->userId . '/' . $callRequest->id)
                ->remove();
        } catch (\Throwable $e) {
            Log::warning('Web incoming RTDB clear failed', [
                'callId' => $callRequest->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected static function notifyUserRingingNext(CallRequest $callRequest, $previousAstrologerId, string $reason): void
    {
        try {
            $devices = DB::table('user_device_details')
                ->where('userId', $callRequest->userId)
                ->get();
            if ($devices->isEmpty()) {
                return;
            }

            FCMService::send(
                $devices,
                [
                    'title' => 'Connecting next advisor',
                    'body' => [
                        'notificationType' => 14,
                        'description' => 'Stay in call astrologer will connect soon.',
                        'type' => 'call_ring_next',
                        'reason' => $reason,
                        'callId' => (string) $callRequest->id,
                        'previousAstrologerId' => (string) $previousAstrologerId,
                        'astrologerId' => (string) $callRequest->astrologerId,
                        'call_type' => $callRequest->type,
                    ],
                ]
            );
        } catch (\Throwable $e) {
            Log::warning('Sequential call user next-ring notify failed', [
                'callId' => $callRequest->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected static function notifyUserNoAdvisorAvailable(CallRequest $callRequest): void
    {
        try {
            $devices = DB::table('user_device_details')
                ->where('userId', $callRequest->userId)
                ->get();
            if ($devices->isEmpty()) {
                return;
            }

            FCMService::send(
                $devices,
                [
                    'title' => 'No Advisor Available',
                    'body' => [
                        'notificationType' => 15,
                        'description' => 'No advisor answered your call.',
                        'type' => 'call_no_advisor',
                        'callId' => (string) $callRequest->id,
                        'call_type' => $callRequest->type,
                    ],
                ]
            );
        } catch (\Throwable $e) {
            Log::warning('Sequential call exhausted notify failed', [
                'callId' => $callRequest->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected static function resolveCallRate(string $type): float|int|string
    {
        if ($type === 'video') {
            return DB::table('systemflag')->where('name', 'VcCallCharges')->value('value') ?? 0;
        }

        return DB::table('systemflag')->where('name', 'CallCharges')->value('value') ?? 0;
    }

    /**
     * @param  mixed  $value
     * @return array<int>
     */
    /**
     * Remember that this advisor explicitly rejected the call (for Rejected Call list).
     */
    public static function appendRejectedAstrologer(CallRequest $callRequest, int $astrologerId): void
    {
        $rejected = self::normalizeTriedIds($callRequest->rejected_astrologer_ids);
        if (!in_array($astrologerId, $rejected, true)) {
            $rejected[] = $astrologerId;
        }
        $callRequest->rejected_astrologer_ids = array_values(array_unique($rejected));
    }

    /**
     * Non-sequential (or final) reject by an advisor — status Rejected + track advisor.
     */
    public static function markRejectedByAdvisor(CallRequest $callRequest, ?int $astrologerId = null): CallRequest
    {
        $astroId = $astrologerId ?: (int) $callRequest->astrologerId;
        if ($astroId) {
            self::appendRejectedAstrologer($callRequest, $astroId);
        }
        self::clearWebIncomingCall($callRequest);
        $callRequest->callStatus = 'Rejected';
        $callRequest->rejected_by = 'advisor';
        $callRequest->is_sequential = false;
        $callRequest->ring_started_at = null;
        $callRequest->updated_at = Carbon::now();
        $callRequest->save();

        return $callRequest;
    }

    protected static function normalizeTriedIds($value): array
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $value = is_array($decoded) ? $decoded : [];
        }
        if (!is_array($value)) {
            return [];
        }

        return array_values(array_map('intval', $value));
    }
}
