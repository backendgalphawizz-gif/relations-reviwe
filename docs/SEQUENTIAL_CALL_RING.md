# Sequential Advisor Call Ringing (30 seconds)

**Date:** 2026-07-27  
**Feature:** When a user starts a call in sequential mode, the call rings **online advisors one by one**. If an advisor does not accept within **30 seconds**, or **rejects**, the call moves to the next available advisor. Routing **stops** when an advisor **accepts**, or when **no advisors remain**.

---

### User cancel (ends entire call)

If the **user** rejects/cancels (hangs up while searching):

- Call status → `Rejected`
- Sequential ringing **stops** (no next advisor)
- Current advisor gets FCM `notificationType` 99 (`call_cancelled_by_user`)
- Call is no longer active for anyone

APIs:
- `POST /api/callRequest/rejectCallRequest` with `callId` (customer cancel endpoint)
- `POST /api/callRequest/reject` when the logged-in user is the call owner (or `fromUser: true`)

Advisor reject still moves to the next advisor.

---

## Missed call (another advisor joined)

If advisors 1–3 miss the ring and advisor 4 accepts, then advisor 2 later taps the old notification:

1. Push already sent to missed advisors (`notificationType` **16**):  
   *"This is a call you missed. Another astrologer has joined."*
2. Accept / storeToken / dashboard start returns **HTTP 409** with the same message.
3. App can also pre-check:

`POST /api/callRequest/checkAvailability`

```json
{ "callId": 123, "astrologerId": 2 }
```

**409 response example:**

```json
{
  "message": "This is a call you missed. Another astrologer has joined.",
  "status": 409,
  "missed": true,
  "anotherJoined": true,
  "joinedAstrologerId": 4,
  "joinedAstrologerName": "Advisor Name",
  "callStatus": "Accepted"
}
```

---

## Flow

```
User starts sequential call
        │
        ▼
Pick first Online advisor (with advisor app device)
Send FCM (notificationType 2) → status Pending
ring_started_at = now
        │
        ├─ Accept  → callStatus = Accepted / Confirmed → STOP
        │            notify earlier advisors (type 16 missed)
        ├─ Reject  → notify previous missed → ring NEXT advisor
        └─ 30s timeout → notify previous missed → ring NEXT advisor
                │
                └─ No more advisors → callStatus = Rejected
                   FCM to user (notificationType 15)
```

---

## How the app should call it

### Start sequential call

`POST /api/callRequest/add`

**Without `astrologerId`** → sequential (one-by-one, 30s):

```json
{
  "type": "audio",
  "isFreeSession": false
}
```

**With `astrologerId`** → only that advisor (no 30s routing):

```json
{
  "astrologerId": 91,
  "type": "audio",
  "isFreeSession": false
}
```

Before creating a new call, any existing `Pending` calls for the same user are closed (prevents duplicate rings). FCM tokens are deduplicated so one advisor does not get the same request multiple times.

**Success response extras:**

| Field | Meaning |
|-------|---------|
| `data` | `callId` |
| `sequential` | `true` |
| `currentAstrologerId` | advisor currently ringing |
| `ringTimeoutSeconds` | `30` |

### Advisor accept (unchanged)

`POST /api/callRequest/accept` with `callId`  
→ Status `Accepted` → sequential routing stops.

### Advisor reject → next advisor

`POST /api/callRequest/reject` with `callId`  
→ Does **not** end the whole call in sequential mode; rings the next advisor.

### User cancels entire call (unchanged)

`POST /api/callRequest/rejectCallRequest` with `callId`  
→ Deletes / ends the call for everyone.

### Poll / force advance (optional)

`POST /api/callRequest/advanceRing`

```json
{ "callId": 123 }
```

Advances only if 30s already elapsed. Use `"force": true` to skip the timer.

---

## Timeout worker (required for auto 30s)

Laravel schedule runs every minute; the command polls every 10 seconds inside that minute (Laravel 10.0 has no `everyTenSeconds()`):

```bash
php artisan schedule:work
```

Or cron:

```cron
* * * * * cd /path/to/project && php artisan schedule:run
```

Manual single pass:

```bash
php artisan calls:advance-pending-rings --once
```

Mobile can also poll `callRequest/advanceRing` every few seconds as a backup.

---

## Files created / changed

### Created

| Absolute path | Purpose |
|---------------|---------|
| `/home/logistack/alphawizz/relations-reviwe/relationship-revive_live/app/Services/CallRingService.php` | Pick online advisors, start sequential call, advance on timeout/reject, FCM notifies |
| `/home/logistack/alphawizz/relations-reviwe/relationship-revive_live/app/Console/Commands/AdvancePendingCallRings.php` | Artisan `calls:advance-pending-rings` |
| `/home/logistack/alphawizz/relations-reviwe/relationship-revive_live/database/migrations/2026_07_27_175707_add_sequential_ring_columns_to_callrequest_table.php` | DB columns for sequential ring |
| `/home/logistack/alphawizz/relations-reviwe/relationship-revive_live/docs/SEQUENTIAL_CALL_RING.md` | This document |

### Modified

| Absolute path | Changes |
|---------------|---------|
| `/home/logistack/alphawizz/relations-reviwe/relationship-revive_live/app/Http/Controllers/API/User/CallRequestController.php` | `addCallRequest` sequential branch; `rejectCallRequest` advances to next; new `advanceRing()` |
| `/home/logistack/alphawizz/relations-reviwe/relationship-revive_live/app/Models/UserModel/CallRequest.php` | Fillable + casts for new columns |
| `/home/logistack/alphawizz/relations-reviwe/relationship-revive_live/routes/api.php` | `POST callRequest/advanceRing` |
| `/home/logistack/alphawizz/relations-reviwe/relationship-revive_live/app/Console/Kernel.php` | Schedule `calls:advance-pending-rings` every minute (inner 10s poll) |

---

## Database columns (`callrequest`)

| Column | Type | Meaning |
|--------|------|---------|
| `is_sequential` | bool | Sequential ring mode |
| `tried_astrologer_ids` | JSON | Advisors already rung this call |
| `ring_started_at` | timestamp | When current advisor started ringing |
| `ring_timeout_seconds` | int | Default `30` |

---

## Advisor eligibility

An advisor is rung only if:

- `callStatus = Online`
- `isDelete = 0` and active
- Has `user_device_details` with `appId` 2 or 3
- Not already on another `Pending` / `Accepted` / `Confirmed` call
- Not already in `tried_astrologer_ids` for this call

Order: lowest `astrologers.id` first (FIFO by id).

---

## FCM notification types (new / used)

| Type | When |
|------|------|
| `2` | Incoming call to current advisor |
| `14` | User notified: ringing next advisor |
| `15` | User notified: no advisor available |
| `16` | Advisor missed: call moved on / another advisor joined |
| `1` | Accept / token (existing) |
| `100` | Declined (single-advisor / non-sequential) |

---

## Test checklist

1. Put 2+ advisors Online with advisor app FCM tokens.
2. User: `POST /api/callRequest/add` with `sequential: true`.
3. First advisor receives call push; do nothing for 30s (with `schedule:work` running).
4. Confirm second advisor receives push; first is skipped.
5. Second advisor rejects → third (or exhausted).
6. An advisor accepts → status Accepted; no further rings.
7. Direct call with only `astrologerId` still works as before.
