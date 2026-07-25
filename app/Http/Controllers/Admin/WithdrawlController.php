<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\services\FCMService;

define('LOGINPATH', '/admin/login');

class WithdrawlController extends Controller
{
    public $path;
    public $limit = 15;
    public $paginationStart;

    public function setWithdrawlPage(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $withdrawRequest = DB::table('withdrawrequest')
                    ->join('astrologers', 'astrologers.id', '=', 'withdrawrequest.astrologerId');
                $withdrawRequest = $withdrawRequest->select('withdrawrequest.*', 'astrologers.name', 'astrologers.contactNo', 'astrologers.profileImage', 'astrologers.userId');

                $withdrawRequest = $withdrawRequest->orderBy('id', 'DESC');
                $withdrawRequest->skip($paginationStart);
                $withdrawRequest->take($this->limit);
                $withdrawlRequest = $withdrawRequest->get();

                $withdrawRequestCount = DB::table('withdrawrequest')
                    ->join('astrologers', 'astrologers.id', '=', 'withdrawrequest.astrologerId');
                $withdrawRequestCount = $withdrawRequestCount->count();
                $totalPages = ceil($withdrawRequestCount / $this->limit);
                $totalRecords = $withdrawRequestCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.withdrawl', compact('withdrawlRequest', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect(LOGINPATH);
            }

        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function getWithDrawlRequest(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $withdrawRequest = DB::table('withdrawrequest')
                    ->join('astrologers', 'astrologers.id', '=', 'withdrawrequest.astrologerId');
                $withdrawRequest = $withdrawRequest->select('withdrawrequest.*', 'astrologers.name', 'astrologers.contactNo', 'astrologers.profileImage', 'astrologers.userId');

                $withdrawRequest = $withdrawRequest->orderBy('id', 'DESC');
                $searchString = $request->searchString ? $request->searchString : null;
                if ($searchString) {
                    $withdrawRequest = $withdrawRequest->where(function ($q) use ($searchString) {
                        $q->where('astrologers.name', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('astrologers.contactNo', 'LIKE', '%' . $searchString . '%');
                    });
                }
                $withdrawRequest->skip($paginationStart);
                $withdrawRequest->take($this->limit);
                $withdrawlRequest = $withdrawRequest->get();

                $withdrawRequestCount = DB::table('withdrawrequest')
                    ->join('astrologers', 'astrologers.id', '=', 'withdrawrequest.astrologerId');
                if ($searchString) {
                    $withdrawRequestCount = $withdrawRequestCount->where(function ($q) use ($searchString) {
                        $q->where('astrologers.name', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('astrologers.contactNo', 'LIKE', '%' . $searchString . '%');
                    });
                }
                $withdrawRequestCount = $withdrawRequestCount->count();

                $totalPages = ceil($withdrawRequestCount / $this->limit);
                $totalRecords = $withdrawRequestCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.withdrawl', compact('withdrawlRequest', 'searchString', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function releaseAmount(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $withdrawRequest = array('status' => 'Released',
                );
                DB::table('withdrawrequest')
                    ->where('id', $request->del_id)
                    ->update($withdrawRequest);

                $userDeviceDetail = DB::table('withdrawrequest')
                    ->join('astrologers', 'astrologers.id', 'withdrawrequest.astrologerId')
                    ->join('user_device_details', 'user_device_details.userId', 'astrologers.userId')
                    ->where('withdrawrequest.id', '=', $request->del_id)
                    ->select('user_device_details.*','withdrawrequest.withdrawAmount')
                    ->get();
                if ($userDeviceDetail && count($userDeviceDetail) > 0) {
                    FCMService::send(
                        $userDeviceDetail,
                        [
                            'title' => $userDeviceDetail[0]->withdrawAmount.' Receive from astroguru admin',
                            'body' => ['description' => 'Payment release from admin successfully','notificationType'=>7],
                        ]
                    );
                }
                return redirect()->route('withdrawalRequests');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }
}
