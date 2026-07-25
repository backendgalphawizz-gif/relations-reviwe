<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminModel\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
define('LOGINPATH', '/admin/login');
class LanguageController extends Controller {
    public $path;
    public $limit = 15;
    public $paginationStart;
    public function addLanguage()
    {
        return view('pages.languages.language-list');
    }

    public function addLanguageApi(Request $req){
        try {
            $validator = Validator::make($req->all(), [
                'language_name' => 'required',
                'language_code' => 'required',
                'language_sign' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->getMessageBag()->toArray(),
                ]);
            }
            if (Auth::guard('web')->check()) {
                Language::create([
                    'languageName' => $req->language_name,
                    'languageCode' => $req->language_code,
                    'language_sign' => $req->language_sign
                ]);
                if($req->ajax()) {
                    return response()->json(['status' => true, 'message' => 'Language created successffully.']);
                }
                return redirect()->route('languages');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }
    //Get language Api

    public function getLanguage(Request $request)
    {
        try {

            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;

                $testimonials = Language::query();
                $testimonials->orderBy('id', 'DESC');
                $testimonials->skip($paginationStart);
                $testimonials->take($this->limit);
                $testimonials = $testimonials->get();
                $testimonialCount = Language::query();
                $testimonialCount = $testimonialCount->count();
                $totalPages = ceil($testimonialCount / $this->limit);
                $totalRecords = $testimonialCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.languages.language-list', compact('testimonials', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }
    //Status Changed Api

    public function languageStatus(Request $request)
    {
        return view('pages.language-list');
    }

    public function languageStatusApi(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $language = Language::find($request->status_id);
                if ($language) {
                    $language->status = !$language->status;
                    $language->update();
                }
                if($request->ajax()) {
                    return response()->json(['status' => true, 'message' => 'Language status updated successffully.']);
                }
                return redirect()->route('languages');
            } else {
                if($request->ajax()) {
                    return response()->json(['status' => false, 'message' => 'Please Login']);
                }
                return redirect(LOGINPATH);
            }

        } catch (Exception $e) {
            if($request->ajax()) {
                return response()->json(['status' => false, 'message' => $e->getMessage()]);
            }
            return dd($e->getMessage());
        }
    }

    // Delete language Api
    public function deleteLanguage(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $language = Language::find($request->del_id);
                if ($language) {
                    $language->delete();
                }
                return redirect()->route('languages');
            } else {
                return redirect(LOGINPATH);
            }

        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    // Edit language
    public function editLanguage(Request $request)
    {
        return view('pages.languages.language-list');
    }

    public function editLanguageApi(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $language = Language::find($request->id);
                if ($language) {
                    $language->languageName = $request->language_name;
                    $language->languageCode = $request->language_code;
                    $language->language_sign = $request->language_sign;
                    $language->update();
                    if($request->ajax()) {
                        return response()->json(['status' => true, 'message' => 'Language updated successfully.']);
                    }
                    return redirect()->route('languages');
                }
            } else {
                return redirect(LOGINPATH);
            }

        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }
}
