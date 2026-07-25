<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gift;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

define('LOGINPATH', '/admin/login');
class GiftController extends Controller
{
    //Add Gift API
    public $limit = 15;
    public $paginationStart;
    public $path;

    public function addGift()
    {
        return view('pages.gift-list');
    }

    public function addGiftApi(Request $req)
    {
        try {
            $validator = Validator::make($req->all(), [
                'name' => 'required|unique:gifts',
                'amount' => 'required',
                'image' => 'required',
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
                $gift = Gift::create([
                    'name' => $req->name,
                    'amount' => $req->amount,
                    'displayOrder' => null,
                    'createdBy' => Auth()->user()->id,
                    'modifiedBy' => Auth()->user()->id,
                ]);
                if ($image) {
                    if (Str::contains($image, 'storage')) {
                        $path = $image;
                    } else {
                        $time = Carbon::now()->timestamp;
                        $destinationpath = 'public/storage/images/';
                        $imageName = 'gift_' . $gift->id;
                        $path = $destinationpath . $imageName . $time . '.png';
                        File::delete($path);
                        file_put_contents($path, base64_decode($image));
                    }
                } else {
                    $path = null;
                }
                $gift->image = $path;
                $gift->update();

                return redirect()->route('gifts');
            } else {
                return redirect(LOGINPATH);
            }

        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    //Get Gift API
    public function setGiftPage(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $req->page ? $req->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $gifts = Gift::query();
                $giftCount = $gifts->count();
                $gifts->orderBy('id', 'DESC');
                $gifts->skip($paginationStart);
                $gifts->take($this->limit);
                $gifts = $gifts->get();
                $totalPages = (int) ceil($giftCount / $this->limit);
                $totalRecords = $giftCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.gift-list', compact('gifts', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect(LOGINPATH);
            }

        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function getGift(Request $req)
    {
        try {
            $page = $req->page ? $req->page : 1;
            $paginationStart = ($page - 1) * $this->limit;
            $gifts = Gift::query();
            $giftCount = $gifts->count();
            $gifts->orderBy('id', 'DESC');
            $gifts->skip($paginationStart);
            $gifts->take($this->limit);
            $gifts = $gifts->get();
            $totalPages = (int) ceil($giftCount / $this->limit);
            $totalRecords = $giftCount;
            $start = ($this->limit * ($page - 1)) + 1;
            $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
            return view('pages.gift-list', compact('gifts', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    //Delete Gift API

    public function deleteGift(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $gift = Gift::find($request->del_id);
                if ($gift) {
                    $gift->delete();
                }
                return redirect()->route('gifts');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    // Edit Skill API
    public function editGift()
    {
        return view('pages.gift-list');
    }

    public function editGiftApi(Request $req)
    {
        try {
            // if (Auth::guard('web')->check()) {
            $gift = Gift::find($req->filed_id);
            if (request('image')) {
                $image = base64_encode(file_get_contents($req->file('image')));
            } elseif ($gift->image) {
                $image = $gift->image;
            } else {
                $image = null;
            }
            if ($gift) {
                if ($image) {
                    if (Str::contains($image, 'storage')) {
                        $path = $image;
                    } else {
                        $time = Carbon::now()->timestamp;
                        $destinationpath = 'public/storage/images/';
                        $imageName = 'gift_' . $req->filed_id;
                        $path = $destinationpath . $imageName . $time . '.png';
                        File::delete($gift->image);

                        file_put_contents($path, base64_decode($image));
                    }
                } else {
                    $path = null;
                }
                $gift->name = $req->name;
                $gift->image = $path;
                $gift->amount = $req->amount;
                $gift->displayOrder = null;
                $gift->update();
                return redirect()->route('gifts');
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function giftStatus(Request $request)
    {
        return view('pages.gift-list');
    }

    public function giftStatusApi(Request $request)
    {try {
        if (Auth::guard('web')->check()) {
            $gift = Gift::find($request->status_id);
            if ($gift) {
                $gift->isActive = !$gift->isActive;
                $gift->update();
                return redirect()->route('gifts');
            }
        } else {
            return redirect(LOGINPATH);
        }
    } catch (Exception $e) {
        return dd($e->getMessage());
    }
    }

}
