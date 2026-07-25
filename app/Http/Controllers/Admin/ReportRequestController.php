<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;
use Response;
use Carbon\Carbon;

class ReportRequestController extends Controller
{
    public $path;
    public $limit = 15;
    public $paginationStart;
    public function getReportRequest(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $report = DB::table('user_reports as re')
                    ->join('astrologers as ast', 'ast.id', '=', 're.astrologerId')
                    ->join('users as ur', 'ur.id', '=', 're.userId')
                    ->join('report_types', 'report_types.id', 're.reportType')
                    ->select('ur.name as userName','ur.contactNo as userContactNo','ast.contactNo as astrologerContactNo', 'ast.name as astrologerName', 're.*', 'report_types.title')
                    ->orderBy('re.id', 'DESC');
                $searchString = $request->searchString ? $request->searchString : null;
                if ($searchString) {
                    $report = $report->where(function ($q) use ($searchString) {
                        $q->where('ur.name', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('ur.contactNo', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('ast.name', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('ast.contactNo', 'LIKE', '%' . $searchString . '%');
                    });
                }
                $report = $report->skip($paginationStart);
                $report = $report->take($this->limit);
                $reportRequest = $report->get();
                $reportCount = DB::table('user_reports as rep')
                    ->join('astrologers as astro', 'astro.id', '=', 'rep.astrologerId')
                    ->join('users as us', 'us.id', '=', 'rep.userId')
                    ->join('report_types', 'report_types.id', 'rep.reportType');
                $searchString = $request->searchString ? $request->searchString : null;
                if ($searchString) {
                    $reportCount = $reportCount->where(function ($q) use ($searchString) {
                        $q->where('us.name', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('us.contactNo', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('astro.name', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('astro.contactNo', 'LIKE', '%' . $searchString . '%');
                    });
                }
                $reportCount = $reportCount->count();
                $totalPages = ceil($reportCount / $this->limit);
                $totalRecords = $reportCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords
                ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.report-request', compact('reportRequest', 'searchString', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
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
            $reportHistory = DB::table('user_reports as report')
                ->join('astrologers as astr', 'astr.id', '=', 'report.astrologerId')
                ->join('users as u', 'u.id', '=', 'report.userId')
                ->join('report_types', 'report_types.id', 'report.reportType')
                ->select('u.name as userName','u.contactNo as userContactNo', 'astr.name as astrologerName','astr.contactNo as astrologerContactNo', 'report.*', 'report_types.title')
                ->orderBy('report.id', 'DESC');
            $searchString = $request->searchString ? $request->searchString : null;
            if ($searchString) {
                $reportHistory = $reportHistory->where(function ($q) use ($searchString) {
                    $q->where('u.name', 'LIKE', '%' . $searchString . '%')
                        ->orWhere('u.contactNo', 'LIKE', '%' . $searchString . '%')
                        ->orWhere('astr.name', 'LIKE', '%' . $searchString . '%')
                        ->orWhere('astr.contactNo', 'LIKE', '%' . $searchString . '%');
                });
            }
            $reportHistory = $reportHistory->get();
            $data = [
                'title' => 'Report History Report',
                'date' => Carbon::now()->format('d-m-Y h:i'),
                'reportHistory' => $reportHistory,
            ];
            $pdf = PDF::loadView('pages.report-history', $data);
            return $pdf->download('reportHistory.pdf');

        } catch (\Exception$e) {
            return dd($e->getMessage());
        }
    }

    public function exportCSV(Request $request)
    {
        $this->path = env('API_URL');
        $reportHistory = DB::table('user_reports as report')
            ->join('astrologers as astr', 'astr.id', '=', 'report.astrologerId')
            ->join('users as u', 'u.id', '=', 'report.userId')
            ->join('report_types', 'report_types.id', 'report.reportType')
            ->select('u.name as userName','u.contactNo as userContactNo', 'astr.name as astrologerName','astr.contactNo as astrologerContactNo', 'report.*', 'report_types.title')
            ->orderBy('report.id', 'DESC');
        $searchString = $request->searchString ? $request->searchString : null;
        if ($searchString) {
            $reportHistory = $reportHistory->where(function ($q) use ($searchString) {
                $q->where('u.name', 'LIKE', '%' . $searchString . '%')
                    ->orWhere('u.contactNo', 'LIKE', '%' . $searchString . '%')
                    ->orWhere('astr.name', 'LIKE', '%' . $searchString . '%')
                    ->orWhere('astr.contactNo', 'LIKE', '%' . $searchString . '%');
            });
        }
        $reportHistory = $reportHistory->get();
        // $callHistory =

        $headers = array(
            "Content-type" => "text/csv",
        );
        $filename = public_path("reportHistory.csv");
        $handle = fopen($filename, 'w');
        fputcsv($handle, [
            "ID",
            "User",
            "Astrologer",
            "Report Type",
            "Report Date",
            "Report Charge",
        ]);
        for ($i = 0; $i < count($reportHistory); $i++) {
            fputcsv($handle, [
                $i + 1,
                $reportHistory[$i]->userName,
                $reportHistory[$i]->astrologerName,
                $reportHistory[$i]->title,
                date('d-m-Y h:i', strtotime($reportHistory[$i]->created_at)),
                $reportHistory[$i]->reportRate,
            ]);
        }
        fclose($handle);
        return Response::download($filename, "reportHistory.csv", $headers);
    }
}
