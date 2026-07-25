<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AstrologerModel\AstrologyVideo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

define('LOGINPATH', '/admin/login');

class AdsVideoController extends Controller
{
    public $path;
    public $limit = 6;
    public $paginationStart;
    public function addAdsVideo()
    {
        return view('pages.adsVideo');
    }

    public function addAdsVideoApi(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                if (request('coverImage')) {
                    $image = base64_encode(file_get_contents($req->file('coverImage')));
                } else {
                    $image = null;
                }
                $adsVideo = AstrologyVideo::create([
                    'youtubeLink' => $req->youtubeLink,
                    'coverImage' => '',
                    'videoTitle' => $req->videoTitle,
                    'createdBy' => Auth()->user()->id,
                    'modifiedBy' => Auth()->user()->id,
                ]);
                if ($image) {
                    if (Str::contains($image, 'storage')) {
                        $path = $image;
                    } else {
                        $time = Carbon::now()->timestamp;
                        $destinationpath = 'storage/images/';
                        $imageName = 'coverImage_' . $adsVideo->id;
                        $path = $destinationpath . $imageName . $time . '.png';
                        File::delete($path);
                        file_put_contents(public_path($path), base64_decode($image));
                    }
                } else {
                    $path = null;
                }
                $adsVideo->coverImage = $path;
                $adsVideo->update();
                return redirect()->route('adsVideos');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    //Get

    public function getAdsVideo(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $adsVideo = AstrologyVideo::query();
                $adsVideoCount = $adsVideo->count();
                $adsVideo->orderBy('id', 'DESC');
                $adsVideo->skip($paginationStart);
                $adsVideo->take($this->limit);
                $adsVideo = $adsVideo->get();
                $totalPages = ceil($adsVideoCount / $this->limit);
                $totalRecords = $adsVideoCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ?
                ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view(
                    'pages.adsVideo',
                    compact('adsVideo', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    //Edit

    public function editAdsVideo()
    {
        return view('pages.adsVideo');
    }

    public function editAdsVideoApi(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                $adsVideo = AstrologyVideo::find($req->filed_id);
                if (request('coverImage')) {
                    $image = base64_encode(file_get_contents($req->file('coverImage')));
                } elseif ($adsVideo && $adsVideo->coverImage) {
                    $image = $adsVideo->coverImage;
                } else {
                    $image = null;
                }
                if ($adsVideo) {
                    if ($image) {
                        $time = Carbon::now()->timestamp;
                        if (Str::contains($image, 'storage')) {
                            $path = $image;
                        } else {
                            $time = Carbon::now()->timestamp;
                            $destinationpath = 'storage/images/';
                            $imageName = 'coverImage_' . $req->filed_id . $time;
                            $path = $destinationpath . $imageName . $time . '.png';
                            File::delete($adsVideo->coverImage);
                            file_put_contents(public_path($path), base64_decode($image));
                        }
                    } else {
                        $path = null;
                    }
                    $adsVideo->youtubeLink = $req->youtubeLink;
                    $adsVideo->coverImage = $path;
                    $adsVideo->videoTitle = $req->videoTitle;
                    $adsVideo->update();
                    return redirect()->route('adsVideos');
                }
            } else {
                return redirect(LOGINPATH);
            }

        } catch (Exception $e) {
            return dd($e->getMessage());
        }

    }

    public function videoStatus(Request $request)
    {
        return view('pages.adsVideo');
    }

    public function videoStatusApi(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $adsVideo = AstrologyVideo::find($request->status_id);
                if ($adsVideo) {
                    $adsVideo->isActive = !$adsVideo->isActive;
                    $adsVideo->update();
                }
                return redirect()->route('adsVideos');
            } else {
                return redirect(LOGINPATH);
            }

        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function deleteVideo(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                AstrologyVideo::find($request->del_id)->delete();
                return redirect()->route('adsVideos');
            } else {
                return redirect(LOGINPATH);
            }

        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }
}
