<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportBlockController extends Controller
{

    public $path;
    public $limit = 15;
    public $paginationStart;

    public function getReportBlock(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $reviews = DB::table('user_reviews')
                    ->join('users', 'users.id', '=', 'user_reviews.userId')
                    ->join('astrologers', 'astrologers.id', '=', 'user_reviews.astrologerId')
                    ->select('user_reviews.*', 'users.name as userName', 'users.profile', 'users.contactNo', 'astrologers.name as astrologerName', 'astrologers.contactNo as astrologerContactNo')
                    ->whereNotNull('user_reviews.astrologerId');
                $reviewsCount = $reviews->count();
                $reviews->skip($paginationStart);
                $reviews->take($this->limit);
                $reportBlocks = $reviews->get();
                $totalPages = ceil($reviewsCount / $this->limit);
                $totalRecords = $reviewsCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.reportBlock', compact('reportBlocks', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function deleteReview(request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                DB::table('user_reviews')->where('id', $request->del_id)->delete();
                return redirect()->back();
            } else {
                return redirect('/admin/login');
            }

        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }
}
