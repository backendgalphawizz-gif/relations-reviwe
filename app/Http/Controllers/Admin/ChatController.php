<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\services\FCMService;
use Carbon\Carbon;
use Google\Cloud\Firestore\FirestoreClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{

    public function getFireStoredata(Request $req)
    {
        try {
            $user = DB::Table('tickets')
                ->join('users', 'users.id', '=', 'tickets.userId')
                ->select('users.name as userName', 'users.profile', 'tickets.userId', 'tickets.ticketStatus')
                ->where('tickets.id', '=', $req->id)
                ->get();
            $db = new FirestoreClient([
                'projectId' => 'astroguru-75d26',
            ]);
            $chatId = $req->id . '_' . $user[0]->userId;
            $data = array(
                'chatId' => $chatId,
                'userName' => $user[0]->userName,
                'userProfile' => $user[0]->profile,
                'userId' => $user[0]->userId,
                "ticketId" => $req->id,
                'ticketStatus' => $user[0]->ticketStatus,
            );
            $chats = $db->collection('supportChat')->document($chatId)->collection('userschat')->document($req->id)->collection('messages');
            $chats = $chats->orderBy('createdAt')->documents();
            $messages = [];
            foreach ($chats as $document) {
                array_push($messages, $document->data());
            }
            return view('pages.chat', compact('messages', 'data'));
        } catch (\Exception$e) {
            return dd($e->getMessage());
        }
    }

    public function createChat(Request $req)
    {
        try {
            $db = new FirestoreClient([
                'projectId' => 'astroguru-75d26',
            ]);

            $postData = array(
                'message' => $req->message,
                'createdAt' => Carbon::now(),
                'updatedAt' => Carbon::now(),
                'userId1' => $req->ticketId,
                'userId2' => $req->senderId,
                'status' => 'OPEN',
            );
            if ($req->messageCount == 2 || $req->ticketStatus == 'WAITING') {
                $data = array(
                    'ticketStatus' => 'OPEN',
                );
                DB::table('tickets')->where('id', '=', $req->ticketId)->update($data);
                $userDeviceDetail = DB::table('user_device_details')
                    ->join('tickets', 'tickets.userId', '=', 'user_device_details.userId')
                    ->where('tickets.id', '=', $req->ticketId)
                    ->select('user_device_details.*')
                    ->get();
                if ($userDeviceDetail && count($userDeviceDetail) > 0) {
                    FCMService::send(
                        $userDeviceDetail,
                        [
                            'title' => 'Notification for customer support status update',
                            'body' => ['description' => 'Notification for customer support status update', 'status' => 'OPEN'],
                        ]
                    );
                }
            } else {
                $userDeviceDetail = DB::table('user_device_details')
                    ->join('tickets', 'tickets.userId', '=', 'user_device_details.userId')
                    ->where('tickets.id', '=', $req->ticketId)
                    ->select('user_device_details.*')
                    ->get();
                if ($userDeviceDetail && count($userDeviceDetail) > 0) {
                    FCMService::send(
                        $userDeviceDetail,
                        [
                            'title' => 'Receive Message',
                            'body' => ['description' => 'Receive Message'],
                        ]
                    );
                }
            }

            $db->collection('supportChat')->document($req->chatId)->collection('userschat')->document($req->senderId)->collection('messages')->add($postData);
            $db->collection('supportChat')->document($req->chatId)->collection('userschat')->document($req->ticketId)->collection('messages')->add($postData);
            return response()->json([
                'success' => ['Send Message Successfully'],
            ]);
        } catch (\Exception$e) {
            return dd($e->getMessage());
        }
    }
}
