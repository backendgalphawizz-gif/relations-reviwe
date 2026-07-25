<?php

namespace App\Http\Controllers\Admin;



use App\Http\Controllers\Controller;

use App\Models\News;

use Carbon\Carbon;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\File;

use Illuminate\Support\Str;

use Illuminate\Support\Facades\Validator;



define('LOGINPATH', '/admin/login');

class NewsController extends Controller

{

    public $path;

    public $limit = 6;

    public $paginationStart;

    public function addAdsVideo()

    {

        return view('pages.adsVideo');

    }



    public function addNewsApi(Request $req)

    {

        try {

            $validator = Validator::make($req->all(), [

                'channel' => 'required',

                'link' => 'required',

                'bannerImage' => 'required',

                'newsDate' => 'required',

            ]);

            if ($validator->fails()) {

                return response()->json([

                    'error' => $validator->getMessageBag()->toArray(),

                ]);

            }

            if (Auth::guard('web')->check()) {

                if (request('bannerImage')) {

                    $image = base64_encode(file_get_contents($req->file('bannerImage')));

                } else {

                    $image = null;

                }

                $news = News::create([

                    'channel' => $req->channel,

                    'newsDate' => $req->newsDate,

                    'link' => $req->link,

                    'bannerImage' => '',

                    'description' => $req->description,

                    'createdBy' => Auth()->user()->id,

                    'modifiedBy' => Auth()->user()->id,

                ]);

                if ($image) {

                    if (Str::contains($image, 'storage')) {

                        $path = $image;

                    } else {

                        $time = Carbon::now()->timestamp;

                        $destinationpath = 'storage/images/';

                        $imageName = 'bannerImage_' . $news->id;

                        $path = $destinationpath . $imageName . $time . '.png';

                        File::delete($path);

                        file_put_contents(public_path($path), base64_decode($image));

                    }

                } else {

                    $path = null;

                }

                $news->bannerImage = $path;

                $news->update();

                return response()->json([

                    'success' => "News Added",

                ]);

            } else {

                return redirect(LOGINPATH);

            }

        } catch (Exception $e) {

            return dd($e->getMessage());

        }

    }



    //Get



    public function getNews(Request $request)

    {

        try {

            if (Auth::guard('web')->check()) {

                $page = $request->page ? $request->page : 1;

                $paginationStart = ($page - 1) * $this->limit;

                $news = News::query();

                $newsCount = $news->count();

                $news->orderBy('id', 'DESC');

                $news->skip($paginationStart);

                $news->take($this->limit);

                $news = $news->get();

                $totalPages = ceil($newsCount / $this->limit);

                $totalRecords = $newsCount;

                $start = ($this->limit * ($page - 1)) + 1;

                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ?

                ($this->limit * ($page - 1)) + $this->limit : $totalRecords;

                return view(

                    'pages.news',

                    compact('news', 'totalPages', 'totalRecords', 'start', 'end', 'page'));

            } else {

                return redirect(LOGINPATH);

            }

        } catch (Exception $e) {

            return dd($e->getMessage());

        }

    }



    //Edit



    public function editNews(Request $req)

    {

        try {

            

            $validator = Validator::make($req->all(), [

                'channel' => 'required',

                'link' => 'required',

                'newsDate' => 'required',

            ]);

            if ($validator->fails()) {

                return response()->json([

                    'error' => $validator->getMessageBag()->toArray(),

                ]);

            }

            if (Auth::guard('web')->check()) {

                $news = News::find($req->filed_id);

                if (request('bannerImage')) {

                    $image = base64_encode(file_get_contents($req->file('bannerImage')));

                } elseif ($news && $news->bannerImage) {

                    $image = $news->bannerImage;

                } else {

                    $image = null;

                }

                if ($news) {

                    if ($image) {

                        $time = Carbon::now()->timestamp;

                        if (Str::contains($image, 'storage')) {

                            $path = $image;

                        } else {

                            $time = Carbon::now()->timestamp;

                            $destinationpath = 'storage/images/';

                            $imageName = 'bannerImage_' . $req->filed_id . $time;

                            $path = $destinationpath . $imageName . $time . '.png';

                            File::delete($news->bannerImage);

                            file_put_contents(public_path($path), base64_decode($image));

                        }

                    } else {

                        $path = null;

                    }

                    $news->link = $req->link;

                    $news->bannerImage = $path;

                    $news->channel = $req->channel;

                    $news->newsDate = $req->newsDate;

                    $news->description = $req->description;

                    $news->update();

                    return response()->json([

                        'success' => "News Updated",

                    ]);

                 

                }

            } else {

                return redirect(LOGINPATH);

            }



        } catch (Exception $e) {

            return dd($e->getMessage());

        }



    }



    public function newsStatusApi(Request $request)

    {

        try {

            if (Auth::guard('web')->check()) {

                $newsStatus = News::find($request->status_id);

                if ($newsStatus) {

                    $newsStatus->isActive = !$newsStatus->isActive;

                    $newsStatus->update();

                }

                return redirect()->route('astroguruNews');

            } else {

                return redirect(LOGINPATH);

            }



        } catch (Exception $e) {

            return dd($e->getMessage());

        }

    }



    public function deleteNews(Request $request)

    {

        try {

            if (Auth::guard('web')->check()) {

                News::find($request->del_id)->delete();

                return redirect()->route('astroguruNews');

            } else {

                return redirect(LOGINPATH);

            }



        } catch (Exception $e) {

            return dd($e->getMessage());

        }

    }

}

