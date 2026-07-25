<?php
namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\AdminModel\Language;
use App\Models\AdminModel\SystemFlag;

class LanguageController extends Controller {

    // Get a language
    public function getLanguages()    {
        try {
            $languages = explode(',', (SystemFlag::where('name', 'Language')->first()->value??""));
            $language = Language::whereIn('id', $languages)->get();
            return response()->json([
                'recordList' => $language,
                'status' => 200
            ],200);
        } catch (\Exception$e) {
            return Response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500
            ],500);
        }
    }
}