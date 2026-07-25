<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

define('DATEFORMAT', "(DATE_FORMAT(horoscopeDate,'%Y-%m-%d'))");

class DailyHoroscopeController extends Controller
{
    public function getDailyHoroscope(Request $req)
    {
        try {
            $dt = Carbon::now()->format('Y-m-d');
            $yesterday = Carbon::yesterday()->format('Y-m-d');
            $tomorrow = Carbon::tomorrow()->format('Y-m-d');
            $todayHoroscope = DB::table('dailyhoroscope')
                ->where('horoscopeSignId', '=', $req->horoscopeSignId)
                ->where(DB::raw(DATEFORMAT), $dt)
                ->get();
            $yeasterDayHoroscope = DB::table('dailyhoroscope')
                ->where('horoscopeSignId', '=', $req->horoscopeSignId)
                ->where(DB::raw(DATEFORMAT), $yesterday)
                ->get();
            $tomorrowHoroscope = DB::table('dailyhoroscope')
                ->where('horoscopeSignId', '=', $req->horoscopeSignId)
                ->where(DB::raw(DATEFORMAT), $tomorrow)
                ->get();
            $todayinsight = DB::table('dailyhoroscopeinsight')
                ->where('horoscopeSignId', '=', $req->horoscopeSignId)
                ->where(DB::raw(DATEFORMAT), $dt)
                ->get();
            $yeasterdayInsight = DB::table('dailyhoroscopeinsight')
                ->where('horoscopeSignId', '=', $req->horoscopeSignId)
                ->where(DB::raw(DATEFORMAT), $yesterday)
                ->get();
            $tomorrowInsight = DB::table('dailyhoroscopeinsight')
                ->where('horoscopeSignId', '=', $req->horoscopeSignId)
                ->where(DB::raw(DATEFORMAT), $tomorrow)
                ->get();
            $weeklyHoroScope = DB::table('horoscope')
                ->where('horoscopeSignId', '=', $req->horoscopeSignId)
                ->where('horoscopeType', '=', 'Weekly')
                ->get();
            $monthlyHoroScope = DB::table('horoscope')
                ->where('horoscopeSignId', '=', $req->horoscopeSignId)
                ->where('horoscopeType', '=', 'Monthly')
                ->get();
            $yearlyHoroScope = DB::table('horoscope')
                ->where('horoscopeSignId', '=', $req->horoscopeSignId)
                ->where('horoscopeType', '=', 'Yearly')
                ->get();
            $todayHoroscopeStatics = DB::table('dailyhoroscopestatics')
                ->where('horoscopeSignId', '=', $req->horoscopeSignId)
                ->where(DB::raw(DATEFORMAT), $dt)
                ->get();
            $yeasterHoroscopeStatics = DB::table('dailyhoroscopestatics')
                ->where('horoscopeSignId', '=', $req->horoscopeSignId)
                ->where(DB::raw(DATEFORMAT), $yesterday)
                ->get();
            $tomorrowHoroscopeStatics = DB::table('dailyhoroscopestatics')
                ->where('horoscopeSignId', '=', $req->horoscopeSignId)
                ->where(DB::raw(DATEFORMAT), $tomorrow)
                ->get();
            $horo = array(
                'todayHoroscope' => $todayHoroscope,
                'yeasterDayHoroscope' => $yeasterDayHoroscope,
                'tomorrowHoroscope' => $tomorrowHoroscope,
                'todayInsight' => $todayinsight,
                'yeasterdayInsight' => $yeasterdayInsight,
                'tomorrowInsight' => $tomorrowInsight,
                'weeklyHoroScope' => $weeklyHoroScope,
                'monthlyHoroScope' => $monthlyHoroScope,
                'yearlyHoroScope' => $yearlyHoroScope,
                'todayHoroscopeStatics' => $todayHoroscopeStatics,
                'yeasterdayHoroscopeStatics' => $yeasterHoroscopeStatics,
                'tomorrowHoroscopeStatics' => $tomorrowHoroscopeStatics,
            );
            return response()->json([
                "message" => "get daily Horoscope",
                "recordList" => $horo,
                'status' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500,
                'error' => false,
            ], 500);
        }
    }

    public function getHoroscope(Request $req)
    {
        try {

            $horoscope = DB::Table('horoscope')
                ->join('hororscope_signs', 'hororscope_signs.id', '=', 'horoscope.horoscopeSignId');
            if ($req->filterSign) {
                $horoscope = $horoscope->where('horoscope.horoscopeSignId', '=', $req->filterSign);
            } else {
                $horoscope = $horoscope->where("horoscopeSignId", '=', 1);
            }
            if ($req->horoscopeType) {
                error_log($req->horoscopeType);
                $horoscope = $horoscope->where('horoscope.horoscopeType', '=', $req->horoscopeType);
            } else {
                $horoscope = $horoscope->where('horoscope.horoscopeType', '=', 'Weekly');
            }
            error_log($req->filterSign);

            return response()->json([
                "message" => "Get Daily Horoscope Insight Successfully",
                'status' => 200,
                "recordList" => $horoscope->select('horoscope.*')->get(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500,
                'error' => false,
            ], 500);
        }
    }

    public function addHoroscopeFeedback(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }
            $data = array(
                'userId' => $id,
                'feedback' => $req->feedback,
                'feedbacktype' => $req->feedbacktype,
            );
            DB::table('horoscopefeedback')->insert($data);
            return response()->json([
                "message" => "Add Feedback Successfully",
                'status' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 500,
                'error' => false,
            ], 500);
        }
    }
}
