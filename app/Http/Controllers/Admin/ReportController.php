<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserModel\ReportType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

define('LOGINPATH', '/admin/login');

class ReportController extends Controller
{
    //Add Gift API
    public $path;
    public $limit = 8;
    public $paginationStart;

    public function addReport()
    {
        return view('pages.report');
    }

    public function addReportApi(Request $req)
    {
        try {
            $validator = Validator::make($req->all(), [
                'title' => 'required|unique:report_types',
                'reportImage' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->getMessageBag()->toArray(),
                ]);
            }
            if (Auth::guard('web')->check()) {
                if (request('reportImage')) {
                    $image = base64_encode(file_get_contents($req->file('reportImage')));
                } else {
                    $image = null;
                }
                $reportType = ReportType::create([
                    'title' => $req->title,
                    'reportImage' => '',
                    'description' => $req->description,
                ]);
                if ($image) {
                    if (Str::contains($image, 'storage')) {
                        $path = $image;
                    } else {
                        $time = Carbon::now()->timestamp;
                        $destinationpath = 'public/storage/images/';
                        $imageName = 'reportType_' . $reportType->id;
                        $path = $destinationpath . $imageName . $time . '.png';
                        File::delete($path);
                        file_put_contents($path, base64_decode($image));
                    }
                } else {
                    $path = null;
                }
                $reportType->reportImage = $path;
                $reportType->update();
                return redirect()->route('reportTypes');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function getReport(Request $request)
    {try {
        if (Auth::guard('web')->check()) {
            $page = $request->page ? $request->page : 1;
            $paginationStart = ($page - 1) * $this->limit;
            $reportType = ReportType::query();
            $searchString = $request->searchString ? $request->searchString : null;
            if ($searchString) {
                $reportType->whereRaw("title LIKE ?", ["%" . $request->searchString . "%"]);
            }
            $reportType = $reportType->orderBy('id','DESC');
            $reportTypeCount = $reportType->count();
            $reportType->skip($paginationStart);
            $reportType->take($this->limit);
            $reports = $reportType->get();
            $totalPages = (int) ceil($reportTypeCount / $this->limit);
            $totalRecords = $reportTypeCount;
            $start = ($this->limit * ($page - 1)) + 1;
            $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
            return view('pages.report', compact('reports', 'searchString', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
        } else {
            return redirect(LOGINPATH);
        }

    } catch (Exception $e) {
        return dd($e->getMessage());
    }
    }

    //Delete Gift API

    // Edit Skill API
    public function editGift()
    {
        return view('pages.gift-list');
    }

    public function editReportApi(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                $reportType = ReportType::find($req->editId);
                if (request('reportImage')) {
                    $image = base64_encode(file_get_contents($req->file('reportImage')));
                } elseif ($reportType->reportImage) {
                    $image = $reportType->reportImage;
                } else {
                    $image = '';
                }
                if ($image) {
                    if (Str::contains($image, 'storage')) {
                        $path = $image;
                    } else {
                        $time = Carbon::now()->timestamp;
                        $destinationpath = 'public/storage/images/';
                        $imageName = 'reportType_' . $req->editId . $time;
                        $path = $destinationpath . $imageName . '.png';
                        file_put_contents($path, base64_decode($image));
                    }
                } else {
                    $path = null;
                }

                if ($reportType) {
                    $reportType->title = $req->title;
                    $reportType->description = $req->editdescription;
                    $reportType->reportImage = $path;
                    $reportType->update();
                }
                return redirect()->route('reportTypes');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function giftStatus(Request $request)
    {
        return view('pages.gift-list');
    }

    public function reportTypeStatusApi(Request $request)
    {try {
        if (Auth::guard('web')->check()) {
            $reportType = ReportType::find($request->status_id);
            if ($reportType) {
                $reportType->isActive = !$reportType->isActive;
                $reportType->update();
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
