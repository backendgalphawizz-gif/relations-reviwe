<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Horosign;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
define('LOGINPATH', '/admin/login');
class HororScopeSignController extends Controller
{
    //Add HororScope API
    public $limit = 15;
    public $paginationStart;
    public $path;
    public function addHororScopeSign()
    {
        return view('pages.horor-scope-sign-list');
    }

    public function addHororScopeSignApi(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:hororscope_signs',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->getMessageBag()->toArray(),
                ]);
            }
            if (Auth::guard('web')->check()) {
                if (request('image')) {
                    $image = base64_encode(file_get_contents($request->file('image')));
                } else {
                    $image = null;
                }
                $hororscopeSign = Horosign::create([
                    'name' => $request->name,
                    'displayOrder' => null,
                    'image' => '',
                    'createdBy' => Auth()->user()->id,
                    'modifiedBy' => Auth()->user()->id,
                ]);
                if ($image) {
                    if (Str::contains($image, 'storage')) {
                        $path = $image;
                    } else {
                        $time = Carbon::now()->timestamp;
                        $destinationpath = 'public/storage/images/';
                        $imageName = 'sign_' . $hororscopeSign->id;
                        $path = $destinationpath . $imageName . $time . '.png';
                        File::delete($path);
                        file_put_contents($path, base64_decode($image));
                    }
                } else {
                    $path = null;
                }
                $hororscopeSign->image = $path;
                $hororscopeSign->update();
                return redirect()->route('horoscopeSigns');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    //Get Gift

    public function getHororScopeSign(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $signs = Horosign::query();
                $hororscopeSignCount = $signs->count();
                $signs->orderBy('id', 'DESC');
                $signs->skip($paginationStart);
                $signs->take($this->limit);
                $signs = $signs->get();
                $totalPages = ceil($hororscopeSignCount / $this->limit);
                $totalRecords = $hororscopeSignCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.horor-scope-sign-list', compact('signs', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function editHororScopeSign()
    {
        return view('pages.horor-scope-sign-list');
    }

    public function editHororScopeSignApi(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                $hororScopeSign = Horosign::find($req->filed_id);
                if (request('image')) {
                    $image = base64_encode(file_get_contents($req->file('image')));
                } elseif ($hororScopeSign->image) {
                    $image = $hororScopeSign->image;
                } else {
                    $image = null;
                }
                if ($hororScopeSign) {
                    if ($image) {
                        if (Str::contains($image, 'storage')) {
                            $path = $image;
                        } else {
                            $time = Carbon::now()->timestamp;
                            $destinationpath = 'public/storage/images/';
                            $imageName = 'sign_' . $req->filed_id;
                            $path = $destinationpath . $imageName . $time . '.png';
                            File::delete($hororScopeSign->image);
                            file_put_contents($path, base64_decode($image));
                        }
                    } else {
                        $path = null;
                    }
                    $hororScopeSign->name = $req->name;
                    $hororScopeSign->displayOrder = null;
                    $hororScopeSign->image = $path;
                    $hororScopeSign->update();
                    return redirect()->route('horoscopeSigns');
                }

            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }
    public function horoScopeStatus(Request $request)
    {
        return view('pages.horor-scope-sign-list');
    }

    public function horoScopeStatusApi(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $hororscopeSign = Horosign::find($request->status_id);
                if ($hororscopeSign) {
                    $hororscopeSign->isActive = !$hororscopeSign->isActive;
                    $hororscopeSign->update();
                    return redirect()->route('horoscopeSigns');
                }
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }
}
