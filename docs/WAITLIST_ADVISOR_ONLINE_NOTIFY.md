# Advisor Online → Next Waitlist User Notification

**Date:** 2026-07-27  
**Feature:** When an advisor switches status to **Online**, the system automatically picks the **oldest waiting user** (FIFO) for that advisor, sends an FCM push, and marks the waitlist row as `Notified`.

---

## Behavior

1. User joins waitlist via `POST /api/waitlist/add` (existing).
2. Advisor was Offline / Wait Time / Busy.
3. Advisor becomes **Online** via any of these status APIs (see below).
4. Backend:
   - Finds first `waitlist` row for that `astrologerId` with status `Pending` / `pending` / `Waiting` / `waiting` / `NULL`
   - Ordered by `id ASC` (FIFO)
   - Sends FCM to `userFcmToken` (fallback: latest `user_device_details.fcmToken` for `userId`)
   - Updates waitlist `status` → `Notified`
5. Mobile app should handle push payload `notificationType: 13` / `type: advisor_available` and prompt the user to join.

**Note:** Only **one** next user is notified per Online transition. After that session ends / user drops, call Online again or use `waitlist/notifyNext` for the following user.

---

## New file

### `/home/logistack/alphawizz/relations-reviwe/relationship-revive_live/app/Services/WaitListService.php`

**Created** shared service with:

```php
WaitListService::notifyNextWaitingUser($astrologerId, $requestType = null)
```

| Step | What it does |
|------|----------------|
| Query | `waitlist` where `astrologerId` + waiting statuses, optional `requestType` filter |
| Order | `id ASC` (first in queue) |
| Token | `waitlist.userFcmToken`, else `user_device_details.fcmToken` |
| Push | Firebase multicast: title `Advisor Available`, data includes `notificationType: 13` |
| Update | Sets waitlist `status = Notified`, `updated_at = now()` |
| Return | Array of waitlist row + `notificationStatus`, or `null` if empty queue |

FCM is sent via `firebase.messaging` directly (not `FCMService`) so a push failure does **not** `dd()` and break status updates.

---

## Modified files

### 1. `/home/logistack/alphawizz/relations-reviwe/relationship-revive_live/app/Http/Controllers/API/User/CallRequestController.php`

**Change in:** `addCallStatus()`

- Import: `use App\Services\WaitListService;`
- After updating `astrologers.callStatus`, if `$req->status === 'Online'` (case-insensitive):
  - call `WaitListService::notifyNextWaitingUser($req->astrologerId)`
- Response adds: `waitlistNotified` (user row or `null`)

**API:** `POST /api/addCallStatus`  
**Body example:** `{ "astrologerId": 49, "status": "Online" }`

---

### 2. `/home/logistack/alphawizz/relations-reviwe/relationship-revive_live/app/Http/Controllers/API/User/ChatRequestController.php`

**Change in:** `addChatStatus()`

- Import: `use App\Services\WaitListService;`
- After updating `astrologers.chatStatus`, if status is Online:
  - call `WaitListService::notifyNextWaitingUser($req->astrologerId)`
- Response adds: `waitlistNotified`

**API:** `POST /api/addStatus`  
**Body example:** `{ "astrologerId": 49, "status": "Online" }`

---

### 3. `/home/logistack/alphawizz/relations-reviwe/relationship-revive_live/app/Http/Controllers/Advisor/DashboardController.php`

**Change in:** `updateCallStatus()`

- Import: `use App\Services\WaitListService;`
- After saving advisor `callStatus`, if Online:
  - call `WaitListService::notifyNextWaitingUser($id)`
- Response adds: `waitlistNotified`

**Route:** `POST` vendor `update-call-status`  
(`routes/vendor.php` → `DashboardController@updateCallStatus`)

---

### 4. `/home/logistack/alphawizz/relations-reviwe/relationship-revive_live/app/Http/Controllers/API/User/WaitListController.php`

**Changes:**

- Import: `use App\Services\WaitListService;`
- **New method:** `notifyNext(Request $req)`
  - Requires `astrologerId`
  - Optional `requestType` (`Chat` | `Audio` | `Video`)
  - Calls `WaitListService::notifyNextWaitingUser(...)`

Use this when you want to notify the next user **without** changing advisor Online status (e.g. after previous waitlist user finished / declined).

---

### 5. `/home/logistack/alphawizz/relations-reviwe/relationship-revive_live/routes/api.php`

**Added route:**

```php
Route::post('waitlist/notifyNext', [WaitListController::class, 'notifyNext']);
```

**Full path:** `POST /api/waitlist/notifyNext`

**Body:**

```json
{
  "astrologerId": 49,
  "requestType": "Video"
}
```

`requestType` is optional.

---

## Unchanged related files (context only)

| Path | Role |
|------|------|
| `/home/logistack/alphawizz/relations-reviwe/relationship-revive_live/app/Models/UserModel/WaitList.php` | Model for `waitlist` table (no code change) |
| `/home/logistack/alphawizz/relations-reviwe/relationship-revive_live/app/services/FCMService.php` | Existing multicast helper (not used by this feature to avoid `dd()` on failure) |
| DB table `waitlist` | Columns used: `id`, `astrologerId`, `userId`, `userFcmToken`, `status`, `requestType`, `channelName`, `userName`, `updated_at` |

---

## FCM payload (user app)

| Field | Value |
|-------|--------|
| title | `Advisor Available` |
| body.description | `Advisor is available now. Please join.` |
| notificationType | `13` |
| type | `advisor_available` |
| astrologerId | advisor id |
| waitListId | waitlist row id |
| requestType | Chat / Audio / Video |
| userId | waiting user id |
| channelName | channel from waitlist |

---

## How to test

1. Ensure user has FCM token stored on waitlist (`userFcmToken`) or in `user_device_details`.
2. Add waitlist entry: `POST /api/waitlist/add` with `astrologerId`, `userId`, `userFcmToken`, `status: Pending`, `requestType`.
3. Set advisor Online: `POST /api/addCallStatus` with `status: Online`.
4. Confirm:
   - Push received on user device
   - Waitlist row `status` = `Notified`
   - API response includes `waitlistNotified`
5. Optional: `POST /api/waitlist/notifyNext` with same `astrologerId` for the next person in queue.

---

## Status flow (summary)

```
User waits  →  waitlist.status = Pending
Advisor → Online  →  notify oldest Pending  →  status = Notified
User joins / app handles notificationType 13
(Next Online or notifyNext)  →  next Pending user
```
