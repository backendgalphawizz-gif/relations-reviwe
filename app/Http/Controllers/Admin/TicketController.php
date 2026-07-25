<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\services\FCMService;
use Carbon\Carbon;
use Google\Cloud\Firestore\FirestoreClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

define('NOTIFICATIONDESC', 'Notification for customer support status update');

class TicketController extends Controller
{
    public $path;
    public $limit = 15;
    public $paginationStart;
    public function getTicket(Request $request)
    {

        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $ticket = DB::Table('tickets')
                    ->join('users', 'users.id', '=', 'tickets.userId')
                    ->join('help_supports', 'help_supports.id', '=', 'tickets.helpSupportId');
                $ticketCount = $ticket->count();
                $ticket = $ticket->select('tickets.*', 'users.name as userName', 'help_supports.name');
                $ticket = $ticket->skip($paginationStart);
                $ticket = $ticket->take($this->limit);
                $tickit = $ticket->orderBy('id', 'DESC')->get();
                if ($tickit && count($tickit) > 0) {
                    for ($i = 0; $i < count($tickit); $i++) {
                        $ticketReview = DB::table('ticketreview')->where('ticketId', '=', $tickit[$i]->id)->get();
                        $tickit[$i]->ticketReview = $ticketReview;
                    }
                }
                $totalPages = ceil($ticketCount / $this->limit);
                $totalRecords = $ticketCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.ticket', compact('tickit', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function closeTicket(Request $request)
    {

        try {
            if (Auth::guard('web')->check()) {
                $ticket = array(
                    'ticketStatus' => 'CLOSED',
                );
                DB::table('tickets')
                    ->where('id', '=', $request->ticket_id)
                    ->update($ticket);
                $userDeviceDetail = DB::table('user_device_details')
                    ->join('tickets', 'tickets.userId', '=', 'user_device_details.userId')
                    ->where('tickets.id', '=', $request->ticket_id)
                    ->select('user_device_details.*')
                    ->get();
                if ($userDeviceDetail && count($userDeviceDetail) > 0) {
                    FCMService::send(
                        $userDeviceDetail,
                        [
                            'title' => NOTIFICATIONDESC,
                            'body' => ['description' => NOTIFICATIONDESC, 'status' => 'CLOSED'],
                        ]
                    );
                }
                $ticket = DB::table('tickets')
                    ->where('id', '=', $request->ticket_id)
                    ->get();
                $db = new FirestoreClient([
                    'projectId' => 'astroguru-75d26',
                ]);
                $postData = array(
                    'message' => "We hope that your issue has been resolved from our end. In case you are still facing any concerns then you can connect with us anytime. We will always love to help you.!",
                    'createdAt' => Carbon::now(),
                    'updatedAt' => Carbon::now(),
                    'userId1' => $request->ticket_id,
                    'userId2' => $ticket[0]->userId,
                    'status' => 'CLOSED',
                );
                $secondMsg = array(
                    'message' => "Thank you for contacting Us!Have a great day ahead!",
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'userId1' => $ticket[0]->userId,
                    'userId2' => $request->ticket_id,
                    'status' => 'CLOSED',
                );
                $db->collection('supportChat')->document($ticket[0]->chatId)->collection('userschat')->document($ticket[0]->userId)->collection('messages')->add($postData);
                $db->collection('supportChat')->document($ticket[0]->chatId)->collection('userschat')->document($request->ticket_id)->collection('messages')->add($postData);
                $db->collection('supportChat')->document($ticket[0]->chatId)->collection('userschat')->document($ticket[0]->userId)->collection('messages')->add($secondMsg);
                $db->collection('supportChat')->document($ticket[0]->chatId)->collection('userschat')->document($request->ticket_id)->collection('messages')->add($secondMsg);
                return response()->json([
                    'success' => ['Send Notification Successfully'],
                ]);
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function pauseTicket(Request $request)
    {

        try {
            $ticket = array(
                'ticketStatus' => 'PAUSED',
            );
            DB::table('tickets')
                ->where('id', '=', $request->ticket_id)
                ->update($ticket);
            $userDeviceDetail = DB::table('user_device_details')
                ->join('tickets', 'tickets.userId', '=', 'user_device_details.userId')
                ->where('tickets.id', '=', $request->ticket_id)
                ->select('user_device_details.*')
                ->get();
            if ($userDeviceDetail && count($userDeviceDetail) > 0) {
                FCMService::send(
                    $userDeviceDetail,
                    [
                        'title' => NOTIFICATIONDESC,
                        'body' => ['description' => NOTIFICATIONDESC, 'status' => 'PAUSED'],
                    ]
                );
            }
            $ticket = DB::table('tickets')
                ->where('id', '=', $request->ticket_id)
                ->get();
            $db = new FirestoreClient([
                'projectId' => 'astroguru-75d26',
            ]);
            $postData = array(
                'message' => "Seems like you are not active as we have not received a response from you,don't worry you can continue your chat from where you have left by resuming the ticket.",
                'createdAt' => Carbon::now(),
                'updatedAt' => Carbon::now(),
                'userId1' => $request->ticket_id,
                'userId2' => $ticket[0]->userId,
                'status' => 'PAUSED',
            );
            $db->collection('supportChat')->document($ticket[0]->chatId)->collection('userschat')->document($ticket[0]->userId)->collection('messages')->add($postData);
            $db->collection('supportChat')->document($ticket[0]->chatId)->collection('userschat')->document($request->ticket_id)->collection('messages')->add($postData);
            return response()->json([
                'success' => ['Send Notification Successfully'],
            ]);
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }
}
