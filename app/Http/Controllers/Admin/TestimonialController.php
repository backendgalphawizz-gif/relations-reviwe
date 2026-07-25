<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use App\Models\AdEnquiry;
use App\Models\UserModel\AppReview;
use App\Models\UserModel\User;
use Illuminate\Http\Request;
use App\Models\AstrologerModel\AstrologerEnquiry;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
define('LOGINPATH', '/admin/login');
class TestimonialController extends Controller
{
    public $path;
    public $limit = 15;
    public $paginationStart;
    public function addTestimonial()
    {
        return view('pages.testimonials.testimonial-list');
    }
    public function getAds(Request $request)
{
    $page  = $request->page ?? 1;
    $limit = $this->limit;
    $offset = ($page - 1) * $limit;

    $query = AdEnquiry::query()->orderBy('id', 'DESC');

    if ($request->filled('name')) {
        $query->where(function ($q) use ($request) {
            $q->where('business_name', 'LIKE', '%' . $request->name . '%')
              ->orWhere('contact_person_name', 'LIKE', '%' . $request->name . '%');
        });
    }

    if ($request->filled('mobile')) {
        $query->where('mobile', 'LIKE', '%' . $request->mobile . '%');
    }

    if ($request->filled('from_date')) {
        $query->whereDate('created_at', '>=', $request->from_date);
    }

    if ($request->filled('to_date')) {
        $query->whereDate('created_at', '<=', $request->to_date);
    }

    $totalRecords = $query->count();
    $totalPages   = ceil($totalRecords / $limit);

    $testimonials = $query
        ->skip($offset)
        ->take($limit)
        ->get();

    $start = $totalRecords > 0 ? ($offset + 1) : 0;
    $end   = min($offset + $limit, $totalRecords);

    return view('pages.testimonials.ads-list', compact(
        'testimonials',
        'totalPages',
        'totalRecords',
        'start',
        'end',
        'page'
    ));
}
public function getAstrologerEnquiries(Request $request)
{
    $page  = $request->page ?? 1;
    $limit = $this->limit;
    $offset = ($page - 1) * $limit;

    $query = AstrologerEnquiry::orderBy('id', 'DESC');

    if ($request->filled('name')) {
        $query->where(function ($q) use ($request) {
            $q->where('name', 'LIKE', '%' . $request->name . '%')
              ->orWhereHas('user', function ($uq) use ($request) {
                  $uq->where('name', 'LIKE', '%' . $request->name . '%');
              });
        });
    }

    if ($request->filled('mobile')) {
        $query->where('mobile', 'LIKE', '%' . $request->mobile . '%');
    }

    if ($request->filled('from_date')) {
        $query->whereDate('created_at', '>=', $request->from_date);
    }

    if ($request->filled('to_date')) {
        $query->whereDate('created_at', '<=', $request->to_date);
    }

    $totalRecords = $query->count();
    $totalPages   = ceil($totalRecords / $limit);

    $enquiries = $query->skip($offset)->take($limit)->get();

    $start = $totalRecords ? ($offset + 1) : 0;
    $end   = min($offset + $limit, $totalRecords);

    return view('pages.testimonials.astrologer-enquiry-list', compact(
        'enquiries',
        'totalPages',
        'totalRecords',
        'start',
        'end',
        'page'
    ));
}

public function viewAstrologerEnquiry(Request $request, $id)
{
    $enquiry = AstrologerEnquiry::findOrFail($id);

    return view('pages.testimonials.astrologer-enquiry-view', compact('enquiry'));
}



    public function addTestimonialApi(Request $req){
        try {
            $validator = Validator::make($req->all(), [
                // 'type' => 'required',
                // 'title' => 'required',
                // 'user_name' => 'required',
                // 'user_image' => 'required|image',
                'userId' => 'required',
                'description' => 'required'
            ], [
                'description.required' => 'Description is required.',
                'userId.required' => 'Please select user.'
            ]);

            $path = '';
            // if($req->hasFile('user_image')) {
            //     $path = $req->file('user_image')->store('testimonials', 'testimonials');
            // }
            // $videoPath = '';
            // if($req->has('video')) {
            //     $videoPath = $req->file('video')->store('testimonials', 'testimonials');
            // }

            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->getMessageBag()->toArray(),
                ], 403);
            }
            if (Auth::guard('web')->check()) {
                AppReview::create([
                    // 'title' => $req->title,
                    // 'user_name' => $req->user_name,
                    // 'user_image' => $path,
                    // 'video_url' => $videoPath,
                    // 'type' => $req->type,
                    'createdBy' => auth()->user()->id,
                    'modifiedBy' => auth()->user()->id,
                    'isActive' => 0,
                    'appId' => 1,
                    'userId' => $req->userId,
                    'review' => $req->description ?? ''
                ]);
                if($req->ajax()) {
                    return response()->json(['status' => true, 'message' => 'Testimonial created successffully.']);
                }
                return redirect()->route('testimonials');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }
    //Get testimonial Api

    public function getTestimonial(Request $request)
    {
        try {

            if (Auth::guard('web')->check()) {

                $users = User::get();

                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;

                $testimonials = AppReview::with('user');
                $testimonials->orderBy('id', 'DESC');
                $testimonials->skip($paginationStart);
                $testimonials->take($this->limit);
                $testimonials = $testimonials->get();
                // dd($testimonials);
                $testimonialCount = AppReview::query();
                $testimonialCount = $testimonialCount->count();
                $totalPages = ceil($testimonialCount / $this->limit);
                $totalRecords = $testimonialCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.testimonials.testimonial-list', compact('testimonials', 'users', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }
    //Status Changed Api

    public function testimonialStatus(Request $request)
    {
        return view('pages.testimonial-list');
    }

    public function testimonialStatusApi(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $testimonial = AppReview::find($request->status_id);
                if ($testimonial) {
                    $testimonial->isActive = !$testimonial->isActive;
                    $testimonial->update();
                }
                if($request->ajax()) {
                    return response()->json(['status' => true, 'message' => 'Testimonial status updated successffully.']);
                }
                return redirect()->route('testimonials');
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

    public function adsEnquiryStatusApi(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $testimonial = AdEnquiry::find($request->status_id);
                if ($testimonial) {
                    $testimonial->status = !$testimonial->status;
                    $testimonial->update();
                }
                if($request->ajax()) {
                    return response()->json(['status' => true, 'message' => 'Ads Enquiry status updated successffully.']);
                }
                return redirect()->route('ads');
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

    // Delete testimonial Api

    public function deleteTestimonial(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $testimonial = AppReview::find($request->del_id);
                if ($testimonial) {
                    $testimonial->delete();
                }
                return redirect()->route('testimonials');
            } else {
                return redirect(LOGINPATH);
            }

        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    // Edit testimonial
    public function editTestimonial(Request $request)
    {
        return view('pages.testimonials.testimonial-list');
    }

    public function editTestimonialApi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // 'title' => 'required',
            // 'user_name' => 'required',
            // 'user_image' => 'image',
            // 'type' => 'required',
            'description' => 'required',
        ], [
            'description.required' => 'Description is required.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->getMessageBag()->toArray(),
            ], 403);
        }

        try {
            if (Auth::guard('web')->check()) {
                $testimonial = AppReview::find($request->id);
                if ($testimonial) {
                    // $testimonial->title = $request->title;
                    // $testimonial->user_name = $request->user_name;
                    // $testimonial->type = $request->type;
                    $testimonial->review = $request->description ?? '';


                    // if($request->hasFile('user_image')) {
                    //     $imgPath = $request->file('user_image')->store('testimonials', 'testimonials');
                    //     $testimonial->user_image = $imgPath;
                    // }
                    // if($request->hasFile('video')) {
                    //     $videoPath = $request->file('video')->store('testimonials', 'testimonials');
                    //     $testimonial->video_url = $videoPath;
                    // }
                    
                    $testimonial->update();
                    if($request->ajax()) {
                        return response()->json(['status' => true, 'message' => 'Testimonial updated successfully.']);
                    }
                    return redirect()->route('testimonials');
                }
            } else {
                return redirect(LOGINPATH);
            }

        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }
}
