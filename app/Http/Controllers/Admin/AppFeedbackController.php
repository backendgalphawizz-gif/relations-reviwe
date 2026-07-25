<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AppFeedbackController extends Controller
{
    public $path;
    public $limit = 15;
    public $paginationStart;
    public function getAppFeedback(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $feedback = DB::table('app_reviews')->join('users', 'users.id', '=', 'app_reviews.userId')->select('app_reviews.*', 'users.name', 'users.contactNo', 'users.profile')->orderBy('app_reviews.id', 'DESC');
                $feedbackCount = $feedback->count();
                $feedback->orderBy('id', 'DESC');
                $feedback->skip($paginationStart);
                $feedback->take($this->limit);
                $feedback = $feedback->get();
                $totalPages = ceil($feedbackCount / $this->limit);
                $totalRecords = $feedbackCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ?
                ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view(
                    'pages.app-feedback',
                    compact('feedback', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }
}
