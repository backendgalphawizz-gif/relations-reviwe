<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Astrologer;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

define('MONTHGROUP', 'month(created_at)');

class DashboardController extends Controller
{
    public $path;

    public function getDashboard(Request $request)
    {
        try {

            if (Auth::guard('web')->check()) {
                $totalCallRequest = DB::table('callrequest')
                    ->count();
                $totalChatRequest = DB::table('chatrequest')
                    ->count();
                $totalReportRequest = DB::table('user_reports')
                    ->count();
                $totalCustomer = DB::table('users')
                    ->join('user_roles', 'user_roles.userId', '=', 'users.id')
                    ->where('user_roles.roleId', '=', '3')
                    ->where('users.isActive', '=', true)
                    ->where('users.isDelete', '=', false)
                    ->count();
                $totalAstrologer = DB::table('astrologers')
                    ->count();

                $adminCommission = DB::table('callrequest')->sum(DB::raw('deduction - deductionFromAstrologer'));
                $totalEarning = DB::table('callrequest')->sum(DB::raw('deduction'));

                $topAstrologers = DB::table('astrologers')
                    ->where('isVerified', '=', true)
                    ->orderBy('totalOrder', 'desc')
                    ->limit(10)
                    ->get();
                if ($topAstrologers && count($topAstrologers) > 0) {
                    foreach ($topAstrologers as $astrologer) {
                        $allSkill = array_map('intval', explode(',', $astrologer->allSkill));
                        $languages = array_map('intval', explode(',', $astrologer->languageKnown));
                        $allSkill = DB::table('skills')
                            ->whereIn('id', $allSkill)
                            ->select('name')
                            ->get();
                        $skill = $allSkill->pluck('name')->all();
                        $astrologer->allSkill = implode(",", $skill);
                        $languageKnown = DB::table('languages')
                            ->whereIn('id', $languages)
                            ->select('languageName')
                            ->get();
                        $languageKnown = $languageKnown->pluck('languageName')->all();
                        $astrologer->languageKnown = implode(",", $languageKnown);
                        $totalCall = DB::table('callrequest')
                            ->where('astrologerId', '=', $astrologer->id)
                            ->count();
                        $astrologer->totalCallRequest = $totalCall;
                        $totalChat = DB::table('chatrequest')
                            ->where('astrologerId', '=', $astrologer->id)
                            ->count();
                        $astrologer->totalChatRequest = $totalChat;
                    }
                }
                $currentDate = Carbon::now();
                $last12Months = [];
                $last12Months[] = $currentDate->format('Y-m');
                for ($i = 1; $i <= 11; $i++) {
                    $lastMonth = $currentDate->subMonth();
                    $last12Months[] = $lastMonth->format('Y-m');
                }
                $last12Months = array_reverse($last12Months);
                $call = [];
                $chat = [];
                $vcall = [];
                $report = [];
                $ti = [];
                $astroTi = [];
                for ($i = 0; $i < count($last12Months); $i++) {
                    $last12monthyear = array_map('intval', explode('-', $last12Months[$i]))[0];
                    $last12monthofmonth = array_map('intval', explode('-', $last12Months[$i]))[1];
                    $callRequest = DB::table('callrequest')
                        ->selectRaw('month(created_at) as callMonth')
                        ->selectRaw('count(id) as totalCall')
                        ->where('type', 'audio')
                        ->whereyear('created_at', '=', $last12monthyear)
                        ->wheremonth('created_at', '=', $last12monthofmonth)
                        ->groupBy(DB::raw(MONTHGROUP))
                        ->get();
                    $vcallRequest = DB::table('callrequest')
                        ->selectRaw('month(created_at) as callMonth')
                        ->selectRaw('count(id) as totalCall')
                        ->where('type', 'video')
                        ->whereyear('created_at', '=', $last12monthyear)
                        ->wheremonth('created_at', '=', $last12monthofmonth)
                        ->groupBy(DB::raw(MONTHGROUP))
                        ->get();
                    $chatRequest = DB::table('chatrequest')
                        ->selectRaw('month(created_at) as chatMonth')
                        ->selectRaw('count(id) as totalChat')
                        ->whereyear('created_at', '=', $last12monthyear)
                        ->wheremonth('created_at', '=', $last12monthofmonth)
                        ->groupBy(DB::raw(MONTHGROUP))
                        ->get();
                    $reportRequest = DB::table('user_reports')
                        ->selectRaw('month(created_at) as month')
                        ->selectRaw('count(id) as totalReport')
                        ->whereyear('created_at', '=', $last12monthyear)
                        ->wheremonth('created_at', '=', $last12monthofmonth)
                        ->groupBy(DB::raw(MONTHGROUP))
                        ->get();
                    $monthyCommission = DB::table('callrequest')
                        ->selectRaw('month(created_at) as month')
                        ->selectRaw('sum(deduction - deductionFromAstrologer) as totalEarning')
                        ->whereyear('created_at', '=', $last12monthyear)
                        ->wheremonth('created_at', '=', $last12monthofmonth)
                        ->groupBy(DB::raw(MONTHGROUP))
                        ->get();
                    $monthyAstroCommission = DB::table('callrequest')
                        ->selectRaw('month(created_at) as month')
                        ->selectRaw('sum(deductionFromAstrologer) as totalEarning')
                        ->whereyear('created_at', '=', $last12monthyear)
                        ->wheremonth('created_at', '=', $last12monthofmonth)
                        ->groupBy(DB::raw(MONTHGROUP))
                        ->get();
                    $dateObj = DateTime::createFromFormat('!m', $last12monthofmonth);
                    $data = array(
                        'callMonth' => $dateObj->format('M'),
                        'callYear' => $last12monthyear,
                        'totalCall' => $callRequest && count($callRequest) > 0 ? $callRequest[0]->totalCall : 0,
                    );
                    $vdata = array(
                        'callMonth' => $dateObj->format('M'),
                        'callYear' => $last12monthyear,
                        'totalCall' => $vcallRequest && count($vcallRequest) > 0 ? $vcallRequest[0]->totalCall : 0,
                    );
                    $chatData = array(
                        'chatMonth' => $dateObj->format('M'),
                        'chatYear' => $last12monthyear,
                        'totalChat' => $chatRequest && count($chatRequest) > 0 ? $chatRequest[0]->totalChat : 0,
                    );
                    $reportData = array(
                        'month' => $dateObj->format('M'),
                        'reportYear' => $last12monthyear,
                        'totalReport' => $reportRequest && count($reportRequest) > 0 ? $reportRequest[0]->totalReport : 0,
                    );
                    $monthCommission = array(
                        'month' => $dateObj->format('M'),
                        'commissionYear' => $last12monthyear,
                        'totalEarning' => $monthyCommission && count($monthyCommission) > 0 ? $monthyCommission[0]->totalEarning : 0,
                    );
                    $monthAstroCommission = array(
                        'month' => $dateObj->format('M'),
                        'commissionYear' => $last12monthyear,
                        'totalEarning' => $monthyAstroCommission && count($monthyAstroCommission) > 0 ? $monthyAstroCommission[0]->totalEarning : 0,
                    );
                    array_push($vcall, $vdata);
                    array_push($call, $data);
                    array_push($chat, $chatData);
                    array_push($report, $reportData);
                    array_push($ti, $monthCommission);
                    array_push($astroTi, $monthAstroCommission);
                }
                $unverifiedAstrologer = DB::table('astrologers')
                    ->where(function ($q) {
                        $q->where('isVerified', 0)
                            ->orWhere('isVerified', '0')
                            ->orWhere('isVerified', false)
                            ->orWhere('isVerified', 'false');
                    })
                    ->orderByDesc('id')
                    ->paginate(10, ['*'], 'unverified_page')
                    ->withQueryString();

                foreach ($unverifiedAstrologer as $astrologers) {
                    // Newer advisors store skills in primarySkill; allSkill is often empty
                    $skillSource = trim((string) ($astrologers->allSkill ?? ''));
                    if ($skillSource === '') {
                        $skillSource = trim((string) ($astrologers->primarySkill ?? ''));
                    }

                    $skillIds = array_values(array_unique(array_filter(array_map('intval', explode(',', $skillSource)))));
                    $languageIds = array_values(array_unique(array_filter(array_map(
                        'intval',
                        explode(',', (string) ($astrologers->languageKnown ?? ''))
                    ))));

                    $skillNames = [];
                    if (!empty($skillIds)) {
                        $skillNames = DB::table('skills')
                            ->whereIn('id', $skillIds)
                            ->pluck('name')
                            ->all();
                    }
                    $astrologers->allSkill = !empty($skillNames) ? implode(', ', $skillNames) : '-';

                    $languageNames = [];
                    if (!empty($languageIds)) {
                        $languageNames = DB::table('languages')
                            ->whereIn('id', $languageIds)
                            ->pluck('languageName')
                            ->all();
                    }
                    $astrologers->languageKnown = !empty($languageNames) ? implode(', ', $languageNames) : '-';
                }
                $dashboardData = ([
                    "totalCallRequest" => $totalCallRequest,
                    "totalChatRequest" => $totalChatRequest,
                    "totalReportRequest" => $totalReportRequest,
                    "topAstrologer" => $topAstrologers,
                    "totalEarning" => $totalEarning,
                    "adminCommission" => $adminCommission,
                    "totalCustomer" => $totalCustomer,
                    "totalAstrologer" => $totalAstrologer,
                    "monthlyCommission" => $ti,
                    "monthlyAstroCommission" => $astroTi,
                    "monthlyCallRequest" => $call,
                    "monthlyVCallRequest" => $vcall,
                    "monthlyChatRequest" => $chat,
                    "monthlyReportRequest" => $report,
                    "unverifiedAstrologer" => $unverifiedAstrologer,
                ]);
                $labels = [];
                $data = [];
                $astroLabels = [];
                $astroData = [];
                $vcallData = [];
                $callData = [];
                $chatData = [];
                $reportData = [];
                $dashboardData = [$dashboardData];
                foreach ($dashboardData[0]['monthlyCommission'] as $label) {
                    $la = $label['month'] . ' ' . $label['commissionYear'];
                    array_push($labels, $la);
                    array_push($data, $label['totalEarning']);
                }
                foreach ($dashboardData[0]['monthlyAstroCommission'] as $label) {
                    $la = $label['month'] . ' ' . $label['commissionYear'];
                    array_push($astroLabels, $la);
                    array_push($astroData, $label['totalEarning']);
                }

                foreach ($dashboardData[0]['monthlyVCallRequest'] as $vcall) {
                    array_push($vcallData, $vcall['totalCall']);
                }
                foreach ($dashboardData[0]['monthlyCallRequest'] as $call) {
                    array_push($callData, $call['totalCall']);
                }
                foreach ($dashboardData[0]['monthlyChatRequest'] as $chat) {
                    array_push($chatData, $chat['totalChat']);
                }
                foreach ($dashboardData[0]['monthlyReportRequest'] as $report) {
                    array_push($reportData, $report['totalReport']);
                }
                $result = $dashboardData;
                return view('pages.dashboard-overview-1', compact(
                    'result',
                    'labels',
                    'data',
                    'astroLabels',
                    'astroData',
                    'vcallData',
                    'callData',
                    'chatData',
                    'reportData',
                    'unverifiedAstrologer'
                ));
            } else {
                return redirect('admin/login');
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }

    }

    public function verifiedAstrologer(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $eid = $request->filed_id;
                $astrologer = Astrologer::find($eid);
                $astrologer->isVerified = !$astrologer->isVerified;
                $astrologer->update();
                return redirect()->route('getDashboard');
            } else {
                return redirect('admin/login');
            }

        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }
}
