<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Models\AdminModel\Banner;
use Carbon\Carbon;

define('LOGINPATH', '/admin/login');

class BannerController extends Controller
{
    public $path;
    public $limit = 15;
    public $paginationStart;

    public function addBanner()
    {
        return view('pages.banner-list');
    }

    public function addBannerApi(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                if (request('bannerImage')) {
                    $image = base64_encode(file_get_contents($req->file('bannerImage')));
                } else {
                    $image = null;
                }
                $banner = Banner::create([
                    'bannerImage' => '',
                    'fromDate' => $req->fromDate,
                    'toDate' => $req->toDate,
                    'link' => $req->link ?? '',
                    'bannerTypeId' => $req->bannerTypeId,
                    'createdBy' => Auth()->user()->id,
                    'modifiedBy' => Auth()->user()->id
                ]);
                if ($image) {
                    if (Str::contains($image, 'storage')) {
                        $path = $image;
                    } else {
                        $time = Carbon::now()->timestamp;
                        $destinationpath = 'storage/images/';
                        $imageName = 'banner_' . $banner->id;
                        $path = $destinationpath . $imageName . $time . '.png';
                        File::delete($path);
                            file_put_contents(public_path($path), base64_decode($image));
                    }
                } else {
                    $path = null;
                }
                $banner->bannerImage = $path;
                $banner->update();
                return redirect()->route('banners');
            } else {
                return redirect(LOGINPATH);
            }
        
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    //Get Banner

    public function getBanner(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $banners = DB::table('banners')
                    ->leftjoin('banner_types', 'banners.bannerTypeId', '=', 'banner_types.id')
                    ->select('banner_types.name as bannerType', 'banner_types.appId', 'banners.*');
                $banners->orderBy('id', 'DESC');
                $banners->skip($paginationStart);
                $banners->limit($this->limit);
                $banners = $banners->get();
                $bannerCount = DB::table('banners')
                    ->leftjoin('banner_types', 'banners.bannerTypeId', '=', 'banner_types.id')
                    ->count();
                $totalPages = ceil($bannerCount / $this->limit);
                $totalRecords = $bannerCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                $bannerType = DB::table('banner_types')->where('isActive', '=', 1)->get();
                return view('pages.banner-list', compact('banners', 'bannerType', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function editBanner()
    {
        return view('pages.banner-list');
    }

    public function editBannerApi(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {

                $banner = Banner::find($req->filed_id);
                if (request('bannerImage')) {
                    $image = base64_encode(file_get_contents($req->file('bannerImage')));
                } elseif ($banner->bannerImage) {
                    $image = $banner->bannerImage;
                } else {
                    $image = null;
                }
                if ($banner) {
                    if ($image) {
                        if (Str::contains($image, 'storage')) {
                            $path = $image;
                        } else {
                            $time = Carbon::now()->timestamp;
                            $destinationpath = 'storage/images/';
                            $imageName = 'banner_' . $req->filed_id;
                            $path = $destinationpath . $imageName . $time . '.png';
                            File::delete($banner->bannerImage);
                                file_put_contents(public_path($path), base64_decode($image));
                        }
                    } else {
                        $path = null;
                    }
                    $banner->bannerImage = $path;
                    $banner->fromDate = $req->fromDate;
                    $banner->toDate = $req->toDate;
                    $banner->link = $req->link ?? '';
                    $banner->bannerTypeId = $req->bannerTypeId;
                    $banner->update();
                    return redirect()->route('banners');
                }
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function bannerStatus(Request $request)
    {
        return view('pages.banner-list');
    }

    public function bannerStatusApi(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $banner = Banner::find($request->status_id);
                if ($banner) {
                    $banner->isActive = !$banner->isActive;
                    $banner->update();
                }
                return redirect()->route('banners');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }
}
