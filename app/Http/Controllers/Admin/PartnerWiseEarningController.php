<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;
use Response;
use Carbon\Carbon;

class PartnerWiseEarningController extends Controller
{
    public $path;
    public $limit = 15;
    public $paginationStart;
    public function getPartnerWiseEarning(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $adminCommission = DB::table('admin_get_commissions as commi')
                    ->join('order_request', 'order_request.id', '=', 'commi.orderId')
                    ->where('order_request.astrologerId', '!=', 'NULL')
                    ->select('order_request.astrologerId')
                    ->selectRaw('sum(commi.amount) as totalEarning')
                    ->groupBy('order_request.astrologerId')
                    ->get();
                $adminCommissionCount = DB::table('admin_get_commissions')
                    ->join('order_request', 'order_request.id', '=', 'admin_get_commissions.orderId')
                    ->where('order_request.astrologerId', '!=', 'NULL')
                    ->select('order_request.astrologerId')
                    ->groupBy('order_request.astrologerId')->distinct()
                    ->get();
                //dd($adminCommission);
                if ($adminCommission && count($adminCommission) > 0) {
                    foreach ($adminCommission as $commission) {
                        $astrologerName = DB::table('astrologers')->where('id', '=', $commission->astrologerId)->select('name')->get();
                        $commission->astrologerName = $astrologerName[0]->name??"";
                        $chatCommission = DB::table('admin_get_commissions as admincommission')
                            ->join('order_request', 'order_request.id', '=', 'admincommission.orderId')
                            ->join('astrologers', 'astrologers.id', '=', 'order_request.astrologerId')
                            ->where('order_request.astrologerId', '!=', 'NULL')
                            ->where('astrologers.id', '=', $commission->astrologerId)
                            ->where('admincommission.commissionTypeId', '=', '1')
                            ->selectRaw('sum(admincommission.amount) as totalChatEarning')
                            ->get();
                        $callCommission = DB::table('admin_get_commissions as admin')
                            ->join('order_request', 'order_request.id', '=', 'admin.orderId')
                            ->join('astrologers', 'astrologers.id', '=', 'order_request.astrologerId')
                            ->where('order_request.astrologerId', '!=', 'NULL')
                            ->where('astrologers.id', '=', $commission->astrologerId)
                            ->where('admin.commissionTypeId', '=', '2')
                            ->selectRaw('sum(admin.amount) as totalCallEarning')
                            ->get();
                        $reportCommission = DB::table('admin_get_commissions as admin')
                            ->join('order_request', 'order_request.id', '=', 'admin.orderId')
                            ->join('astrologers', 'astrologers.id', '=', 'order_request.astrologerId')
                            ->where('order_request.astrologerId', '!=', 'NULL')
                            ->where('astrologers.id', '=', $commission->astrologerId)
                            ->where('admin.commissionTypeId', '=', '3')
                            ->selectRaw('sum(admin.amount) as totalReportEarning')
                            ->get();

                        $commission->chatEarning = $chatCommission && count($chatCommission) > 0 ? $chatCommission[0]->totalChatEarning : null;
                        $commission->callEarning = $callCommission && count($callCommission) > 0 ? $callCommission[0]->totalCallEarning : null;
                        $commission->reportEarning = $reportCommission && count($reportCommission) > 0 ? $reportCommission[0]->totalReportEarning : null;
                    }
                    $adminCommission = array_slice(array($adminCommission), $paginationStart, $this->limit);
                    $totalPages = ceil(count($adminCommissionCount) / $this->limit);
                    $totalRecords = count($adminCommissionCount);
                    $partnerWiseEarning = ($adminCommission && count($adminCommission) > 0) ? $adminCommission[0] : [];
                    $start = ($this->limit * ($page - 1)) + 1;
                    $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                    return view('pages.partnerwise-earning', compact('partnerWiseEarning', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
                }
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }

    }

    public function printPdf(Request $req)
    {
        try {
            $adminCommission = DB::table('admin_get_commissions')
                ->join('order_request', 'order_request.id', '=', 'admin_get_commissions.orderId')
                ->where('order_request.astrologerId', '!=', 'NULL')
                ->select('order_request.astrologerId')
                ->selectRaw('sum(admin_get_commissions.amount) as totalEarning')
                ->groupBy('order_request.astrologerId')
                ->get();
            if ($adminCommission && count($adminCommission) > 0) {
                foreach ($adminCommission as $commission) {
                    $astrologerName = DB::table('astrologers')->where('id', '=', $commission->astrologerId)->select('name')->get();
                    $commission->astrologerName = $astrologerName[0]->name;
                    $chatCommission = DB::table('admin_get_commissions')
                        ->join('order_request', 'order_request.id', '=', 'admin_get_commissions.orderId')
                        ->join('astrologers', 'astrologers.id', '=', 'order_request.astrologerId')
                        ->where('order_request.astrologerId', '!=', 'NULL')
                        ->where('astrologers.id', '=', $commission->astrologerId)
                        ->where('order_request.orderType', '=', 'chat')
                        ->selectRaw('sum(admin_get_commissions.amount) as totalChatEarning')
                        ->get();
                    $callCommission = DB::table('admin_get_commissions')
                        ->join('order_request', 'order_request.id', '=', 'admin_get_commissions.orderId')
                        ->join('astrologers', 'astrologers.id', '=', 'order_request.astrologerId')
                        ->where('order_request.astrologerId', '!=', 'NULL')
                        ->where('astrologers.id', '=', $commission->astrologerId)
                        ->where('order_request.orderType', '=', 'call')
                        ->selectRaw('sum(admin_get_commissions.amount) as totalCallEarning')
                        ->get();
                    $reportCommission = DB::table('admin_get_commissions as admin')
                        ->join('order_request', 'order_request.id', '=', 'admin.orderId')
                        ->join('astrologers', 'astrologers.id', '=', 'order_request.astrologerId')
                        ->where('order_request.astrologerId', '!=', 'NULL')
                        ->where('astrologers.id', '=', $commission->astrologerId)
                        ->where('order_request.orderType', '=', 'report')
                        ->selectRaw('sum(admin.amount) as totalReportEarning')
                        ->get();
                    $commission->chatEarning = $chatCommission && count($chatCommission) > 0 ? $chatCommission[0]->totalChatEarning : null;
                    $commission->callEarning = $callCommission && count($callCommission) > 0 ? $callCommission[0]->totalCallEarning : null;
                    $commission->reportEarning = $reportCommission && count($reportCommission) > 0 ? $reportCommission[0]->totalReportEarning : null;
                }

            }
            $partnerWiseEarning = ($adminCommission && count($adminCommission) > 0) ? $adminCommission : [];
            $data = [
                'title' => 'PartnerWise Earning',
                'date' => Carbon::now()->format('d-m-Y h:i'),
                'partnerWiseEarning' => $partnerWiseEarning,
            ];
            $pdf = PDF::loadView('pages.partnerwise-earning-report', $data);
            return $pdf->download('partnerWiseEarning.pdf');

        } catch (\Exception$e) {
            return dd($e->getMessage());
        }
    }

    public function exportPartnerWiseCSV(Request $request)
    {
        $adminCommission = DB::table('admin_get_commissions')
            ->join('order_request', 'order_request.id', '=', 'admin_get_commissions.orderId')
            ->where('order_request.astrologerId', '!=', 'NULL')
            ->select('order_request.astrologerId')
            ->selectRaw('sum(admin_get_commissions.amount) as totalEarning')
            ->groupBy('order_request.astrologerId')
            ->get();
        if ($adminCommission && count($adminCommission) > 0) {
            foreach ($adminCommission as $commission) {
                $astrologerName = DB::table('astrologers')->where('id', '=', $commission->astrologerId)->select('name')->get();
                $commission->astrologerName = $astrologerName[0]->name;
                $chatCommission = DB::table('admin_get_commissions')
                    ->join('order_request', 'order_request.id', '=', 'admin_get_commissions.orderId')
                    ->join('astrologers', 'astrologers.id', '=', 'order_request.astrologerId')
                    ->where('order_request.astrologerId', '!=', 'NULL')
                    ->where('astrologers.id', '=', $commission->astrologerId)
                    ->where('order_request.orderType', '=', 'chat')
                    ->selectRaw('sum(admin_get_commissions.amount) as totalChatEarning')
                    ->get();
                $callCommission = DB::table('admin_get_commissions')
                    ->join('order_request', 'order_request.id', '=', 'admin_get_commissions.orderId')
                    ->join('astrologers', 'astrologers.id', '=', 'order_request.astrologerId')
                    ->where('order_request.astrologerId', '!=', 'NULL')
                    ->where('astrologers.id', '=', $commission->astrologerId)
                    ->where('order_request.orderType', '=', 'call')
                    ->selectRaw('sum(admin_get_commissions.amount) as totalCallEarning')
                    ->get();
                $reportCommission = DB::table('admin_get_commissions as admin')
                    ->join('order_request', 'order_request.id', '=', 'admin.orderId')
                    ->join('astrologers', 'astrologers.id', '=', 'order_request.astrologerId')
                    ->where('order_request.astrologerId', '!=', 'NULL')
                    ->where('astrologers.id', '=', $commission->astrologerId)
                    ->where('order_request.orderType', '=', 'report')
                    ->selectRaw('sum(admin.amount) as totalReportEarning')
                    ->get();
                $commission->chatEarning = $chatCommission && county($chatCommission) > 0 ? $chatCommission[0]->totalChatEarning : null;
                $commission->callEarning = $callCommission && count($callCommission) > 0 ? $callCommission[0]->totalCallEarning : null;
                $commission->reportEarning = $reportCommission && count($reportCommission) > 0 ? $reportCommission[0]->totalReportEarning : null;
            }

        }
        $partnerWiseEarning = ($adminCommission && count($adminCommission) > 0) ? $adminCommission : [];
        $headers = array(
            "Content-type" => "text/csv",
        );
        $filename = public_path("partnerWiseEarning.csv");
        $handle = fopen($filename, 'w');
        fputcsv($handle, [
            "ID",
            "Astrologer",
            "Total Earning",
            'Chat Earning',
            'Call Earning',
            'Report Earning',
        ]);
        for ($i = 0; $i < count($partnerWiseEarning); $i++) {
            fputcsv($handle, [
                $i + 1,
                $partnerWiseEarning[$i]->astrologerName,
                $partnerWiseEarning[$i]->totalEarning,
                $partnerWiseEarning[$i]->chatEarning,
                $partnerWiseEarning[$i]->callEarning,
                $partnerWiseEarning[$i]->reportEarning,
            ]);
        }
        fclose($handle);
        return Response::download($filename, "partnerWiseEarning.csv", $headers);
    }

}
