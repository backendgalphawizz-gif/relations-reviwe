<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\UserModel\ReportType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ReportTypeController extends Controller
{

    //Get all report type data
    public function getReportTypes(Request $req)
    {
        try {
            $reportType = ReportType::query();
            if ($req->searchString) {
                $reportType->whereRaw("title LIKE ?", ["%" . $req->searchString . "%"]);
            }
            $reportType->where('isActive', '=', true);
            $reportTypeCount = $reportType->count();
            if ($req->startIndex >= 0 && $req->fetchRecord) {
                $reportType = $reportType->skip($req->startIndex);
                $reportType = $reportType->take($req->fetchRecord);
            }
            return response()->json([
                'recordList' => $reportType->get(),
                'status' => 200,
                'totalRecords' => $reportTypeCount,
            ]);
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ]);
        }
    }

}
