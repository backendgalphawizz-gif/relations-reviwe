<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AstrologerCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
define('LOGINPATH', '/admin/login');
class AstrologerCategoryController extends Controller
{
    //Add Astrologer API
    public $limit = 15;
    public $paginationStart;
    public $path;
    public function addAstrolgerCategory()
    {
        return view('pages.astrologer-category-list');
    }

    public function addAstrolgerCategoryApi(Request $req)
    {
        try {
            $validator = Validator::make($req->all(), [
                'name' => 'required|unique:astrologer_categories',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->getMessageBag()->toArray(),
                ]);
            }
            if (Auth::guard('web')->check()) {
                if (request('image')) {
                    $image = base64_encode(file_get_contents($req->file('image')));
                } else {
                    $image = null;
                }
                $astrologerCategory = AstrologerCategory::create([
                    'name' => $req->name,
                    'image' => '',
                    'displayOrder' => null,
                    'createdBy' => Auth()->user()->id,
                    'modifiedBy' => Auth()->user()->id,
                ]);
                if ($image) {
                    if (Str::contains($image, 'storage')) {
                        $path = $image;
                    } else {
                        $time = Carbon::now()->timestamp;
                        $destinationpath = 'storage/images/';
                        $imageName = 'astrologerCategory_' . $astrologerCategory->id;
                        $path = $destinationpath . $imageName . $time . '.png';
                        File::delete($path);
                        file_put_contents(public_path($path), base64_decode($image));
                    }
                } else {
                    $path = null;
                }
                $astrologerCategory->image = $path;
                $astrologerCategory->update();
                return redirect()->route('astrologerCategories')->with('message', 'Data added Successfully');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function getAstrolgerCategory(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $categories = AstrologerCategory::query();
                $categoryCount = $categories->count();
                $categories->orderBy('id', 'DESC');
                $categories->skip($paginationStart);
                $categories->take($this->limit);
                $categories = $categories->get();
                $totalPages = ceil($categoryCount / $this->limit);
                $totalRecords = $categoryCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords
                ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view(
                    'pages.astrologer-category-list',
                    compact('categories', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function editAstrolgerCategory()
    {
        return view('pages.astrologer-category-list');
    }

    public function editAstrolgerCategoryApi(Request $request)
    {
        try {
            
            if (Auth::guard('web')->check()) {

                $astrologerCategory = AstrologerCategory::find($request->filed_id);
                if (request('image')) {
                    $image = base64_encode(file_get_contents($request->file('image')));
                } elseif ($astrologerCategory->image) {
                    $image = $astrologerCategory->image;
                } else {
                    $image = null;
                }

                if ($astrologerCategory) {
                    if ($image) {
                        if (Str::contains($image, 'storage')) {
                            $path = $image;
                        } else {
                            $time = Carbon::now()->timestamp;
                            $destinationpath = 'storage/images/';
                            $imageName = 'astrologerCategory_' . $request->filed_id;
                            $path = $destinationpath . $imageName . $time . '.png';
                            File::delete($astrologerCategory->image);
                            file_put_contents(public_path($path), base64_decode($image));
                        }
                    } else {
                        $path = null;
                    }
                    $astrologerCategory->name = $request->name;
                    $astrologerCategory->image = $path;
                    $astrologerCategory->displayOrder = null;
                    $astrologerCategory->update();
                    return redirect()->route('astrologerCategories');
                }
            } else {
                return redirect(LOGINPATH);
            }

        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function astrologyCategoryStatus(Request $request)
    {
        return view('pages.astrologer-category-list');
    }

    public function astrologyCategoryStatusApi(Request $request)
    {
        try {
            $astrologerCategory = AstrologerCategory::find($request->status_id);
            if (Auth::guard('web')->check()) {
                $astrologerCategory->isActive = !$astrologerCategory->isActive;
                $astrologerCategory->update();
                return redirect()->route('astrologerCategories');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }
}
