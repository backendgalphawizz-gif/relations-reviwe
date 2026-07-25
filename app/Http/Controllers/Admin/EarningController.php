<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;
use Response;
use Carbon\Carbon;
define('LOGINPATH', '/admin/login');

class EarningController extends Controller
{
    public $path;
    public $limit = 15;
    public $paginationStart;
    public function getEarning(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $astrologerEarning = DB::table('order_request as order')
                    ->join('users as us', 'us.id', '=', 'order.userId')
                    ->where('astrologerId', '=', $request->id)
                    ->where('orderType', '!=', 'astromall')
                    ->select('us.name as userName', 'order.*')
                    ->orderby('id', 'DESC');

                $astrologerEarningCount = DB::table('order_request')
                    ->join('users', 'users.id', '=', 'order_request.userId')
                    ->where('astrologerId', '=', $request->id)
                    ->where('orderType', '!=', 'astromall')
                    ->count();
                $astrologerEarning = $astrologerEarning->skip($paginationStart);
                $astrologerEarning = $astrologerEarning->take($this->limit);
                $astrologerName = DB::Table('astrologers')
                    ->where('id', '=', $request->id)
                    ->select('name')
                    ->get();
                $astrologerEarning = $astrologerEarning->get();
                if ($astrologerEarning && count($astrologerEarning) > 0) {
                    foreach ($astrologerEarning as $earning) {
                        $earning->charge = $earning->totalMin > 0 ? $earning->totalPayable / $earning->totalMin : 0;
                        $earning->astrologerName = $astrologerName[0]->name??"";
                    }
                }
                $totalPages = ceil($astrologerEarningCount / $this->limit);
                $astrologerId = $request->id;
                $totalRecords = $astrologerEarningCount;
                if ($astrologerEarning && count($astrologerEarning) > 0) {
                    foreach ($astrologerEarning as $earning) {
                        if ($earning->totalMin > 0) {
                            $earning->charge = $earning->totalPayable / $earning->totalMin;
                        }

                    }
                }
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.earning', compact('astrologerEarning', 'astrologerId', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function printPdf(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                $astrologerEarning = DB::table('order_request')
                    ->join('users', 'users.id', '=', 'order_request.userId')
                    ->where('astrologerId', '=', $req->id)
                    ->where('orderType', '!=', 'astromall')
                    ->select('users.name as userName', 'order_request.*')
                    ->orderby('id', 'DESC')->get();
                if ($astrologerEarning && count($astrologerEarning) > 0) {
                    $astrologerName = DB::Table('astrologers')
                        ->where('id', '=', $req->id)
                        ->select('name')
                        ->get();
                    foreach ($astrologerEarning as $earning) {
                        $earning->charge = $earning->totalMin > 0 ? $earning->totalPayable / $earning->totalMin : 0;
                        $earning->astrologerName = $astrologerName[0]->name;
                    }
                }
                $data = [
                    'title' => 'Earning Report',
                    'date' => Carbon::now()->format('d-m-Y h:i'),
                    'astrologerEarning' => $astrologerEarning,
                    'astrologerName' => $astrologerEarning[0]->astrologerName??"",
                ];
                $pdf = PDF::loadView('pages.astrologer-earning-report', $data);
                return $pdf->download('astrologerEarning.pdf');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (\Exception$e) {
            return dd($e->getMessage());
        }
    }

    public function exportAstrologerEarningCSV(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $astrologerEarning = DB::table('order_request')
                    ->join('users', 'users.id', '=', 'order_request.userId')
                    ->where('astrologerId', '=', $request->id)
                    ->where('orderType', '!=', 'astromall')
                    ->select('users.name as userName', 'order_request.*')
                    ->orderby('id', 'DESC')->get();
                if ($astrologerEarning && count($astrologerEarning) > 0) {
                    $astrologerName = DB::Table('astrologers')
                        ->where('id', '=', $request->id)
                        ->select('name')
                        ->get();
                    foreach ($astrologerEarning as $earning) {
                        $earning->charge = $earning->totalMin > 0 ? $earning->totalPayable / $earning->totalMin : 0;
                        $earning->astrologerName = $astrologerName[0]->name??"";
                    }
                }
                $headers = array(
                    "Content-type" => "text/csv",
                );
                $filename = public_path("astrologerEarning.csv");
                $handle = fopen($filename, 'w');
                fputcsv($handle, [
                    "ID",
                    "User",
                    "OrderType",
                    "OrderAmount",
                    "TotalMin",
                    "Charge",
                    "OrderDate",
                ]);

                for ($i = 0; $i < count($astrologerEarning); $i++) {
                    fputcsv($handle, [
                        $i + 1,
                        $astrologerEarning[$i]->userName,
                        $astrologerEarning[$i]->orderType,
                        $astrologerEarning[$i]->totalPayable,
                        $astrologerEarning[$i]->totalMin,
                        date('d-m-Y h:i', strtotime($astrologerEarning[$i]->created_at)),
                    ]);
                }
                fclose($handle);
                return Response::download($filename, "astrologerEarning.csv", $headers);
            } else {
                return redirect(LOGINPATH);
            }
        } catch (\Exception$e) {
            return dd($e->getMessage());
        }
    }
   
}
