<?php

namespace App\Http\Controllers\API\Astrologer;

use App\Http\Controllers\Controller;
use App\Models\AstrologerModel\AstrologerCategory;
use Illuminate\Http\Request;

class AstrologerCategoryController extends Controller
{

    //Get all the data of the astrologer category
    public function getAstrologerCategory(Request $req)
    {
        try {

            $astrologerCategory = AstrologerCategory::query();
            if ($s = $req->input(key:'s')) {
                $astrologerCategory->whereRaw("name LIKE ?", ["%" . $s . "%"]);
            }
            $categoryCount = $astrologerCategory->count();
            $astrologerCategory->orderBy('id', 'DESC');
            if ($req->startIndex >= 0 && $req->fetchRecord) {
                $astrologerCategory->skip($req->startIndex);
                $astrologerCategory->take($req->fetchRecord);
            }
            return response()->json([
                'recordList' => $astrologerCategory->get(),
                'status' => 200,
                'totalRecords' => $categoryCount,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    //Show only active astrologer category
    public function activeAstrologerCategory()
    {
        try {
            $astrologerCategory = AstrologerCategory::query()->where('isActive', '=', '1');
            return response()->json([
                'recordList' => $astrologerCategory->get(),
                'status' => 200,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }
}
