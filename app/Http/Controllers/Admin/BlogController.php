<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AstrologerModel\Blog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

define('DESTINATIONPATH', 'storage/images/');
define('LOGINPATH', '/admin/login');

class BlogController extends Controller
{
    public $path;
    public $limit = 6;
    public $paginationStart;

    public function addBlog()
    {
        return view('pages.blog-list');
    }

    public function addBlogApi(Request $req)
    {

        $validator = Validator::make($req->all(), [
            'title' => 'required',
            'description' => 'required',
            // 'viewer' => 'required',
            'author' => 'required',
            'postedOn' => 'required'
        ], 
        [
            'title.required' => 'Title is required',
            'description.required' => 'Description is required',
            'author.required' => 'Posted By is required',
            'postedOn.required' => 'Posted Date is required',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->getMessageBag()->toArray(),
            ], 403);
        }

        try {
            if (Auth::guard('web')->check()) {
                if (request('blogImage')) {
                    $image = base64_encode(file_get_contents($req->file('blogImage')));
                    $extension = $req->file('blogImage')->getClientOriginalExtension();
                }
                if (request('previewImage')) {
                    $previewImage = base64_encode(file_get_contents($req->file('previewImage')));
                } else {
                    $previewImage = null;
                }
                $blog = Blog::create([
                    'title' => $req->title,
                    'blogImage' => '',
                    'description' => $req->description,
                    'viewer' => $req->viewer,
                    'author' => $req->author,
                    'createdBy' => Auth()->user()->id,
                    'modifiedBy' => Auth()->user()->id,
                    'postedOn' => $req->postedOn,
                    'extension' => $extension,
                    'previewImage' => '',
                ]);
                if ($image) {
                    if (Str::contains($image, 'storage')) {
                        $path = $image;
                    } else {
                        $time = Carbon::now()->timestamp;

                        $imageName = 'blog_' . $blog->id;
                        $path = DESTINATIONPATH . $imageName . $time . '.' . $extension;
                        File::delete($path);
                        file_put_contents(public_path($path), base64_decode($image));
                    }
                } else {
                    $path = null;
                }
                if ($previewImage) {
                    if (Str::contains($previewImage, 'storage')) {
                        $previewPath = $previewImage;
                    } else {
                        $time = Carbon::now()->timestamp;

                        $imageName = 'blogpreview_' . $blog->id;
                        $previewPath = DESTINATIONPATH . $imageName . $time . '.png';
                        File::delete($previewPath);
                        file_put_contents(public_path($previewPath), base64_decode($previewImage));
                    }
                } else {
                    $previewPath = null;
                }
                $blog->previewImage = $previewPath;
                $blog->blogImage = $path;
                $blog->update();
                return response()->json([
                    'success' => "Blog Added",
                ]);
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function getBlog(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $blog = Blog::query();
                $searchString = $request->searchString ? $request->searchString : null;
                if ($searchString) {
                    $blog->whereRaw("title LIKE ?", ["%" . $request->searchString . "%"]);
                }
                $blog->orderBy('id', 'DESC');
                $blogCount = $blog->count();
                $blog->skip($paginationStart);
                $blog->take($this->limit);
                $blogs = $blog->get();
                $totalPages = ceil($blogCount / $this->limit);
                $totalRecords = $blogCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.blog-list', compact('blogs', 'totalPages', 'searchString', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect(LOGINPATH);
            }

        } catch (Exception $e) {
            return redirect()->back()->with('error', '', $e->getMessage());
        }
    }

    public function getBlogById(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $blogDetail = Blog::find($request->id);
                return view('pages.blog-detail', compact('blogDetail'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (\Exception$e) {
            return dd($e->getMessage());
        }
    }

    public function editBlog()
    {
        return view('pages.blog-list');
    }

    public function editBlogApi(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            // 'viewer' => 'required',
            'author' => 'required',
            'postedOn' => 'required'
        ], 
        [
            'title.required' => 'Title is required',
            'description.required' => 'Description is required',
            'author.required' => 'Posted By is required',
            'postedOn.required' => 'Posted Date is required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->getMessageBag()->toArray(),
            ], 403);
        }

        try {
            if (Auth::guard('web')->check()) {
                $blog = Blog::find($request->filed_id);

                if (request('eblogImage')) {
                    $image = base64_encode(file_get_contents($request->file('eblogImage')));
                    $extension = $request->file('eblogImage')->getClientOriginalExtension();
                } elseif ($blog->blogImage) {
                    $image = $blog->blogImage;
                    $extension = $blog->extension;
                } else {
                    $image = '';
                    $extension = '';
                }
                if (request('previewImages')) {
                    $previewImage = base64_encode(file_get_contents($request->file('previewImages')));
                } elseif ($blog->previewImage) {
                    $previewImage = $blog->previewImage;
                } else {
                    $previewImage = '';
                }
                if ($blog) {
                    if ($image) {
                        if (Str::contains($image, 'storage')) {
                            $path = $image;
                        } else {
                            $time = Carbon::now()->timestamp;

                            $imageName = 'blog_' . $request->filed_id;
                            $path = DESTINATIONPATH . $imageName . $time . '.' . $extension;
                            File::delete($blog->blogImage);
                            file_put_contents(public_path($path), base64_decode($image));
                        }
                    } else {
                        $path = null;
                    }
                    if ($previewImage) {
                        if (Str::contains($previewImage, 'storage')) {
                            $previewPath = $previewImage;
                        } else {
                            $time = Carbon::now()->timestamp;
                            $imageName = 'blogpreview_' . $blog->id;
                            $previewPath = DESTINATIONPATH . $imageName . $time . '.png';
                            File::delete($blog->previewImage);
                            file_put_contents(public_path($previewPath), base64_decode($previewImage));
                        }
                    } else {
                        $previewPath = null;
                    }
                    $blog->title = $request->title;
                    $blog->blogImage = $path;
                    $blog->previewImage = $previewPath;
                    $blog->description = $request->editdescription;
                    $blog->author = $request->author;
                    $blog->postedOn = $request->postedOn;
                    $blog->extension = $extension;
                    $blog->update();

                    return response()->json([
                        'success' => "Blog Update",
                    ]);
                }
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return redirect()->back()->with('error', '', $e->getMessage());
        }
    }

    public function blogStatus(Request $request)
    {
        return view('pages.blog-list');
    }

    public function blogStatusApi(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $blog = Blog::find($request->status_id);
                if ($blog) {
                    $blog->isActive = !$blog->isActive;
                    $blog->update();
                }
                return redirect()->route('blogs');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }

    }

    public function deleteBlog(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $blog = Blog::find($request->del_id);
                if ($blog) {
                    $blog->delete();
                }
                return redirect()->back();
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }
}
