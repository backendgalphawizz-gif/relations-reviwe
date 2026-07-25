<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;
use Response;
use Carbon\Carbon;

class ChatHistoryReportController extends Controller
{
    public $path;
    public $limit = 15;
    public $paginationStart;
    public function getChatHistory(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $chat = DB::table('chatrequest as chat')
                    ->join('astrologers as astro', 'astro.id', '=', 'chat.astrologerId')
                    ->join('users as ur', 'ur.id', '=', 'chat.userId')
                    ->select('ur.name as userName', 'ur.contactNo as contactNo', 'astro.name as astrologerName', 'chat.*');
                $searchString = $request->searchString ? $request->searchString : null;
                if ($searchString) {
                    $chat = $chat->where(function ($q) use ($searchString) {
                        $q->where('ur.name', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('ur.contactNo', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('astro.name', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('astro.contactNo', 'LIKE', '%' . $searchString . '%');
                    });
                }
                $chat->skip($paginationStart);
                $chat->take($this->limit);
                $chat = $chat->orderBy('chat.id', 'DESC');
                $chatHistory = $chat->get();
                $chatCount = DB::table('chatrequest')
                    ->join('astrologers', 'astrologers.id', '=', 'chatrequest.astrologerId')
                    ->join('users', 'users.id', '=', 'chatrequest.userId');
                if ($searchString) {
                    $chatCount = $chatCount->where(function ($q) use ($searchString) {
                        $q->where('users.name', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('users.contactNo', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('astrologers.name', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('astrologers.contactNo', 'LIKE', '%' . $searchString . '%');
                    });
                }
                $chatCount = $chatCount->count();
                $totalPages = ceil($chatCount / $this->limit);
                $totalRecords = $chatCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.chat-history-report', compact('chatHistory', 'searchString', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function setChatHistoryPage(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $chat = DB::table('chatrequest as chat')
                    ->join('astrologers as astr', 'astr.id', '=', 'chat.astrologerId')
                    ->join('users as ur', 'ur.id', '=', 'chat.userId')
                    ->select('ur.name as userName', 'ur.contactNo as contactNo', 'astr.name as astrologerName', 'chat.*');
                $chat->skip($paginationStart);
                $chat->take($this->limit);
                $chat = $chat->orderBy('chat.id', 'DESC');
                $chatHistory = $chat->get();
                $chatCount = DB::table('chatrequest')
                    ->join('astrologers', 'astrologers.id', '=', 'chatrequest.astrologerId')
                    ->join('users', 'users.id', '=', 'chatrequest.userId')
                    ->count();
                $totalPages = ceil($chatCount / $this->limit);
                $totalRecords = $chatCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.chat-history-report', compact('chatHistory', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function printPdf(Request $request)
    {
        try {
            $chatHistory = DB::table('chatrequest')
                ->join('astrologers', 'astrologers.id', '=', 'chatrequest.astrologerId')
                ->join('users', 'users.id', '=', 'chatrequest.userId')
                ->select('users.name as userName', 'users.contactNo as contactNo', 'astrologers.name as astrologerName', 'chatrequest.*');
            $searchString = $request->searchString ? $request->searchString : null;
            if ($searchString) {
                $chatHistory = $chatHistory->where(function ($q) use ($searchString) {
                    $q->where('users.name', 'LIKE', '%' . $searchString . '%')
                        ->orWhere('users.contactNo', 'LIKE', '%' . $searchString . '%')
                        ->orWhere('astrologers.name', 'LIKE', '%' . $searchString . '%')
                        ->orWhere('astrologers.contactNo', 'LIKE', '%' . $searchString . '%');
                });
            }
            $chatHistory = $chatHistory->orderBy('id', 'DESC')->get();
            $data = [
                'title' => 'Chat History Report',
                'date' => Carbon::now()->format('d-m-Y h:i'),
                'chatHistory' => $chatHistory,
            ];
            $pdf = PDF::loadView('pages.chatHistoryPdf', $data);
            return $pdf->download('chatHistory.pdf');
        } catch (\Exception$e) {
            return dd($e->getMessage());
        }
    }

    public function exportChatCSV(Request $request)
    {
        $chatHistory = DB::table('chatrequest')
            ->join('astrologers', 'astrologers.id', '=', 'chatrequest.astrologerId')
            ->join('users', 'users.id', '=', 'chatrequest.userId')
            ->select('users.name as userName', 'astrologers.name as astrologerName', 'chatrequest.*');
        $searchString = $request->searchString ? $request->searchString : null;
        if ($searchString) {
            $chatHistory = $chatHistory->where(function ($q) use ($searchString) {
                $q->where('users.name', 'LIKE', '%' . $searchString . '%')
                    ->orWhere('users.contactNo', 'LIKE', '%' . $searchString . '%')
                    ->orWhere('astrologers.name', 'LIKE', '%' . $searchString . '%')
                    ->orWhere('astrologers.contactNo', 'LIKE', '%' . $searchString . '%');
            });
        }
        $chatHistory = $chatHistory->orderBy('id', 'DESC')->get();
        $headers = array(
            "Content-type" => "text/csv",
        );
        $filename = public_path("chatHistory.csv");
        $handle = fopen($filename, 'w');
        fputcsv($handle, [
            "ID",
            "User",
            "Astrologer",
            "Chat Rate",
            "Chat Time",
            "Total Min",
            "Deduction",
        ]);
        for ($i = 0; $i < count($chatHistory); $i++) {
            fputcsv($handle, [
                $i + 1,
                $chatHistory[$i]->userName,
                $chatHistory[$i]->astrologerName,
                $chatHistory[$i]->chatRate,
                date('d-m-Y h:i', strtotime($chatHistory[$i]->updated_at)),
                $chatHistory[$i]->totalMin,
                $chatHistory[$i]->deduction,
            ]);
        }
        fclose($handle);
        return Response::download($filename, "chatHistory.csv", $headers);
    }

}
