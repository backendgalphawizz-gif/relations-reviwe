<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Broadcast;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
define('LOGINPATH', '/admin/login');
class BroadcastController extends Controller
{
    public $path;
    public $limit = 15;
    public $paginationStart;
    public function addBroadcast()
    {
        return view('pages.broadcasts.broadcast-list');
    }

    public function addBroadcastApi(Request $req){
        try {
            $validator = Validator::make($req->all(), [
                'title' => 'required',
                'status' => 'required',
                'description' => 'required'
            ]);

            $path = '';
            $videoPath = '';
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->getMessageBag()->toArray(),
                ]);
            }
            if (Auth::guard('web')->check()) {
                Broadcast::create([
                    'title' => $req->title??'',
                    'status' => $req->status??'',
                    'description' => $req->description ?? ''
                ]);
                if($req->ajax()) {
                    return response()->json(['status' => true, 'message' => 'Broadcasts created successfully.']);
                }
                return redirect()->route('broadcasts');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }
    //Get Broadcast Api

    public function getBroadcast(Request $request)
    {
        try {

            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;

                $broadcasts = Broadcast::query();
                $broadcasts->orderBy('id', 'DESC');
                $broadcasts->skip($paginationStart);
                $broadcasts->take($this->limit);
                $broadcasts = $broadcasts->get();
                $broadcastCount = Broadcast::query()->count();
                $totalPages = ceil($broadcastCount / $this->limit);
                $totalRecords = $broadcastCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.broadcasts.broadcast-list', compact('broadcasts', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }
    //Status Changed Api

    public function broadcastStatus(Request $request)
    {
        return view('pages.broadcast-list');
    }

    public function broadcastStatusApi(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $broadcast = Broadcast::find($request->status_id);
                if ($broadcast) {
                    $broadcast->status = !$broadcast->status;
                    $broadcast->update();
                }
                if($request->ajax()) {
                    return response()->json(['status' => true, 'message' => 'Broadcast status updated successffully.']);
                }
                return redirect()->route('broadcasts');
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

    // Delete Broadcast Api

    public function deleteBroadcast(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $broadcast = Broadcast::find($request->del_id);
                if ($broadcast) {
                    $broadcast->delete();
                }
                return redirect()->route('broadcasts');
            } else {
                return redirect(LOGINPATH);
            }

        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    // Edit Broadcast
    public function editBroadcast(Request $request)
    {
        return view('pages.broadcasts.broadcast-list');
    }

    public function editBroadcastApi(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $broadcast = Broadcast::find($request->id);
                if ($broadcast) {
                    $broadcast->title = $request->title;
                    $broadcast->status = $request->status;
                    $broadcast->description = $request->description ?? '';                    
                    $broadcast->update();
                    if($request->ajax()) {
                        return response()->json(['status' => true, 'message' => 'Broadcast updated successfully.']);
                    }
                    return redirect()->route('broadcasts');
                }
            } else {
                return redirect(LOGINPATH);
            }

        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }
}
