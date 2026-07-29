<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\services\FCMService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

define('LOGINPATH', '/admin/login');

class NotificationController extends Controller
{
    public $limit = 15;
    public $paginationStart;
    public $path;

    public function addNotification()
    {
        return view('pages.notification-list');
    }

    public function addNotificationApi(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
            'send_to' => 'nullable|in:all,all_customers,single_customer,all_advisors,single_advisor',
            'userIds' => 'nullable|array',
            'userIds.*' => 'integer',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->getMessageBag()->toArray(),
            ]);
        }
        try {
            if (!Auth::guard('web')->check()) {
                return response()->json(['error' => ['error' => 'Unauthorized']], 401);
            }

            $imagePath = $this->storeNotificationImage($req);
            $sendTo = $this->normalizeSendTo($req);
            $selectedIds = $this->parseUserIds($req->input('userIds', []));

            $notification = Notification::create([
                'title' => $req->title,
                'description' => $req->description,
                'image' => $imagePath,
                'send_to' => $sendTo,
                'send_to_user_ids' => in_array($sendTo, ['single_customer', 'single_advisor'], true)
                    ? json_encode($selectedIds)
                    : null,
                'createdBy' => Auth()->user()->id,
                'modifiedBy' => Auth()->user()->id,
            ]);

            $sendNow = $req->boolean('send_now') || $req->input('send_now') === '1' || $req->input('send_now') === 'on';
            $sendResult = null;

            if ($sendNow) {
                $sendReq = new Request([
                    'notification_id' => $notification->id,
                    'send_to' => $sendTo,
                    'userIds' => $selectedIds,
                ]);
                $sendResponse = $this->sendNotification($sendReq);
                $sendResult = method_exists($sendResponse, 'getData')
                    ? $sendResponse->getData(true)
                    : null;

                if (is_array($sendResult) && !empty($sendResult['error'])) {
                    return response()->json([
                        'status' => true,
                        'saved' => true,
                        'sent' => false,
                        'error' => $sendResult['error'],
                        'message' => 'Notification saved but send failed',
                    ]);
                }
            }

            return response()->json([
                'status' => true,
                'saved' => true,
                'sent' => (bool) $sendNow,
                'message' => $sendNow ? 'Notification saved and sent successfully' : 'Notification saved successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => ['error' => $e->getMessage()],
            ]);
        }
    }

    public function getNotification(Request $request)
    {
        try {
            if (!Auth::guard('web')->check()) {
                return redirect(LOGINPATH);
            }

            $page = $request->page ? $request->page : 1;
            $paginationStart = ($page - 1) * $this->limit;
            $notifications = Notification::query();
            $notifications->orderBy('id', 'DESC');
            $notificationCount = $notifications->count();
            $notifications->skip($paginationStart);
            $notifications->take($this->limit);
            $notifications = $notifications->get();
            $totalPages = ceil($notificationCount / $this->limit);
            $totalRecords = $notificationCount;
            $start = ($this->limit * ($page - 1)) + 1;
            $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords
                ? ($this->limit * ($page - 1)) + $this->limit
                : $totalRecords;

            $users = DB::table('users')
                ->join('user_roles', 'user_roles.userId', '=', 'users.id')
                ->where('users.isDelete', '=', false)
                ->where(function ($q) {
                    $q->whereNull('users.isActive')->orWhere('users.isActive', true);
                })
                ->whereIn('user_roles.roleId', [2, 3])
                ->select('users.id', 'users.name', 'users.contactNo', 'user_roles.roleId')
                ->orderBy('users.name')
                ->get();

            $userNamesById = $users->pluck('name', 'id');

            return view('pages.notification-list', compact(
                'notifications',
                'users',
                'userNamesById',
                'totalPages',
                'totalRecords',
                'start',
                'end',
                'page'
            ));
        } catch (\Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function editNotification()
    {
        return view('pages.notification-list');
    }

    public function editNotificationApi(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'filed_id' => 'required',
            'title' => 'required|string|max:255',
            'did' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:4096',
        ], [
            'did.required' => 'Description is required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->getMessageBag()->toArray(),
            ]);
        }
        try {
            if (!Auth::guard('web')->check()) {
                return response()->json(['error' => ['error' => 'Unauthorized']], 401);
            }

            $notification = Notification::find($req->filed_id);
            if (!$notification) {
                return response()->json(['error' => ['error' => 'Notification not found']], 404);
            }

            $notification->title = $req->title;
            $notification->description = $req->did;
            $notification->modifiedBy = Auth()->user()->id;

            $imagePath = $this->storeNotificationImage($req);
            if ($imagePath) {
                $notification->image = $imagePath;
            }

            $notification->update();

            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => ['error' => $e->getMessage()],
            ]);
        }
    }

    public function notifcationStatus(Request $request)
    {
        return view('pages.notification-list');
    }

    public function notifcationStatusApi(Request $request)
    {
        try {
            if (!Auth::guard('web')->check()) {
                return redirect(LOGINPATH);
            }

            $notification = Notification::find($request->status_id);
            if ($notification) {
                $notification->isActive = !$notification->isActive;
                $notification->update();
            }

            return redirect()->route('notifications');
        } catch (\Exception $e) {
            return dd($e->getMessage());
        }
    }

    /**
     * Send notification to:
     * - send_to: all | all_customers | single_customer | all_advisors | single_advisor
     * - userIds[] required for single_customer / single_advisor
     */
    public function sendNotification(Request $req)
    {
        try {
            if (!Auth::guard('web')->check()) {
                return response()->json(['error' => ['error' => 'Unauthorized']], 401);
            }

            $notificationMaster = Notification::find($req->notification_id);
            if (!$notificationMaster) {
                return response()->json([
                    'error' => ['error' => 'Notification not found'],
                ], 404);
            }

            $title = $notificationMaster->title;
            $description = $notificationMaster->description;
            $image = $notificationMaster->image;
            $imageUrl = $image ? url($image) : null;
            $adminId = Auth()->user()->id ?? 0;

            $sendTo = $this->normalizeSendTo($req);
            $selectedIds = $this->parseUserIds($req->input('userIds', []));

            if (in_array($sendTo, ['single_customer', 'single_advisor'], true) && empty($selectedIds)) {
                return response()->json([
                    'error' => [
                        'error' => $sendTo === 'single_customer'
                            ? 'Please select at least one customer'
                            : 'Please select at least one advisor',
                    ],
                ]);
            }

            $targetUserIds = $this->resolveTargetUserIds($sendTo, $selectedIds);
            if (empty($targetUserIds)) {
                return response()->json([
                    'error' => ['error' => 'No recipients found for selected audience'],
                ]);
            }

            $notificationMaster->send_to = $sendTo;
            $notificationMaster->send_to_user_ids = in_array($sendTo, ['single_customer', 'single_advisor'], true)
                ? json_encode($selectedIds)
                : null;
            $notificationMaster->modifiedBy = $adminId;
            $notificationMaster->save();

            @set_time_limit(120);

            $now = now();
            $inboxRows = [];
            foreach ($targetUserIds as $userId) {
                $inboxRows[] = [
                    'userId' => $userId,
                    'title' => $title,
                    'description' => $description,
                    'image' => $image,
                    'notificationId' => $notificationMaster->id,
                    'createdBy' => $adminId,
                    'modifiedBy' => $adminId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            foreach (array_chunk($inboxRows, 500) as $chunk) {
                DB::table('user_notifications')->insert($chunk);
            }

            $tokens = DB::table('user_device_details')
                ->whereIn('userId', $targetUserIds)
                ->whereNotNull('fcmToken')
                ->where('fcmToken', '!=', '')
                ->pluck('fcmToken')
                ->unique()
                ->values()
                ->all();

            $fcmPayload = [
                'title' => $title,
                'body' => [
                    'description' => $description,
                    'notificationType' => 20,
                    'type' => 'admin_notification',
                    'image' => $imageUrl,
                    'notificationId' => (string) $notificationMaster->id,
                ],
                'image' => $imageUrl,
            ];

            FCMService::sendToTokens($tokens, $fcmPayload);

            return response()->json([
                'success' => ['Send Notification Successfully'],
                'sentToDevices' => count($tokens),
                'savedForUsers' => count($targetUserIds),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => ['error' => $e->getMessage()],
            ]);
        }
    }

    /**
     * @return array<int>
     */
    protected function parseUserIds(mixed $selectedIds): array
    {
        if (is_string($selectedIds)) {
            $decoded = json_decode($selectedIds, true);
            $selectedIds = is_array($decoded) ? $decoded : array_filter(explode(',', $selectedIds));
        }
        if (!is_array($selectedIds)) {
            $selectedIds = [];
        }

        return array_values(array_unique(array_filter(array_map('intval', $selectedIds))));
    }

    /**
     * Normalize send_to from request (supports legacy audience values).
     */
    protected function normalizeSendTo(Request $req): string
    {
        $sendTo = strtolower(trim((string) $req->input('send_to', '')));

        if ($sendTo !== '') {
            return match ($sendTo) {
                'all', 'both' => 'all',
                'all_customers', 'customer', 'customers', 'user', 'users' => 'all_customers',
                'single_customer' => 'single_customer',
                'all_advisors', 'advisor', 'advisors', 'astrologer', 'astrologers', 'adviser' => 'all_advisors',
                'single_advisor' => 'single_advisor',
                default => 'all',
            };
        }

        // Legacy: audience + selection_mode / userIds
        $audience = strtolower(trim((string) $req->input('audience', $req->input('role', 'both'))));
        $selectionMode = strtolower(trim((string) $req->input('selection_mode', 'all')));
        $hasSelected = !empty($req->input('userIds'));

        if (in_array($audience, ['user', 'users', 'customer', 'customers'], true)) {
            return ($selectionMode === 'selected' || $hasSelected) ? 'single_customer' : 'all_customers';
        }
        if (in_array($audience, ['astrologer', 'astrologers', 'adviser', 'advisor', 'advisors'], true)) {
            return ($selectionMode === 'selected' || $hasSelected) ? 'single_advisor' : 'all_advisors';
        }

        return 'all';
    }

    /**
     * @param  array<int>  $selectedIds
     * @return array<int>
     */
    protected function resolveTargetUserIds(string $sendTo, array $selectedIds): array
    {
        if ($sendTo === 'all') {
            $customerIds = $this->resolveTargetUserIds('all_customers', []);
            $advisorIds = $this->resolveTargetUserIds('all_advisors', []);

            return array_values(array_unique(array_merge($customerIds, $advisorIds)));
        }

        $roleIds = match ($sendTo) {
            'all_customers', 'single_customer' => [3],
            'all_advisors', 'single_advisor' => [2],
            default => [2, 3],
        };

        $query = DB::table('users')
            ->join('user_roles', 'user_roles.userId', '=', 'users.id')
            ->whereIn('user_roles.roleId', $roleIds)
            ->where('users.isDelete', '=', false)
            ->where(function ($q) {
                $q->whereNull('users.isActive')->orWhere('users.isActive', true);
            });

        if (in_array($sendTo, ['all_advisors', 'single_advisor'], true)) {
            $query->join('astrologers', 'astrologers.userId', '=', 'users.id')
                ->where('astrologers.isDelete', 0)
                ->where('astrologers.isVerified', 1);
        }

        if (in_array($sendTo, ['single_customer', 'single_advisor'], true)) {
            if (empty($selectedIds)) {
                return [];
            }
            $query->whereIn('users.id', $selectedIds);
        }

        return $query->distinct()->pluck('users.id')->map(fn ($id) => (int) $id)->all();
    }

    protected function storeNotificationImage(Request $req): ?string
    {
        if (!$req->hasFile('image')) {
            return null;
        }

        $file = $req->file('image');
        $ext = strtolower($file->getClientOriginalExtension() ?: 'png');
        $imageName = 'notification_' . Carbon::now()->timestamp . '_' . Str::random(6) . '.' . $ext;
        $relativePath = 'storage/images/' . $imageName;
        $absolutePath = public_path($relativePath);

        if (!is_dir(dirname($absolutePath))) {
            mkdir(dirname($absolutePath), 0755, true);
        }

        $file->move(dirname($absolutePath), $imageName);

        return $relativePath;
    }
}
