<?php

namespace App\Http\Controllers\API\Astrologer;

use App\Http\Controllers\Controller;
use App\Models\AstrologerModel\Blog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    
    //Get all blog details
    public function getBlog(Request $req)
    {
        try {

            $blog = Blog::query();
            if ($req->searchString) {
                $blog = $blog->whereRaw("title LIKE ?", ["%" . $req->searchString . "%"]);
            }
            $blog->orderBy('id', 'DESC');
            if ($req->startIndex >= 0 && $req->fetchRecord) {
                $blog->skip($req->startIndex);
                $blog->take($req->fetchRecord);
            }
            $blogCount = Blog::count();
            return response()->json([
                'recordList' => $blog->get(),
                'status' => 200,
                'totalRecords' => $blogCount,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function getAppBlog(Request $req)
    {
        try {
            $blog = Blog::query();
            if ($req->searchString) {
                $blog = $blog->whereRaw("title LIKE ?", ["%" . $req->searchString . "%"]);
            }
            $blog = $blog->where('isActive', '=', true);
            $blog->orderBy('id', 'DESC');
            if ($req->startIndex >= 0 && $req->fetchRecord) {
                $blog->skip($req->startIndex);
                $blog->take($req->fetchRecord);
            }
            $blogCount = Blog::count();
            return response()->json([
                'recordList' => $blog->get(),
                'status' => 200,
                'totalRecords' => $blogCount,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    //Show single blog
    public function blogShow(Request $req)
    {
        try {
           
            error_log($req->id);
            $blog = Blog::find($req->id);
            if ($blog) {
                error_log($req->id);
                return response()->json([
                    'recordList' => $blog,
                    'status' => 200,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Blog is not found',
                    'status' => 404,
                ], 404);
            }
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    //Show only active blog
    public function activeBlogs(Request $req)
    {
        try {
            $blog = Blog::query()->where('isActive', '=', '1');
            $blogCount = $blog->count();
            if ($req->startIndex >= 0 && $req->fetchRecord) {
                $blog->skip($req->startIndex);
                $blog->take($req->fetchRecord);
            }
            return response()->json([
                'recordList' => $blog->get(),
                'status' => 200,
                'totalRecords' => $blogCount,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function addBlogReader(Request $req)
    {
        try {
            $data = $req->only(
                'blogId',
            );

            //Validate the data
            $validator = Validator::make($data, [
                'blogId' => 'required',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            } else {
                $blog = Blog::find($req->blogId);
                if ($blog) {
                    $blog->viewer = $blog->viewer + 1;
                    $blog->update();
                    return response()->json([
                        'message' => 'Viewer Add Successfully',
                        'recordList' => $blog,
                        'status' => 200,
                    ], 200);
                } else {
                    return response()->json([
                        'message' => 'Blog is not found',
                        'status' => 404,
                    ], 404);
                }
            }
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

}
