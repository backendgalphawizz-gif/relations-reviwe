<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminModel\DefaultProfile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

define('DESTINATIONPATH', 'public/storage/images/');
define('LOGINPATH', '/admin/login');

class DefaultImageController extends Controller
{
    public $path;
    public $limit = 15;
    public $paginationStart;
    public function getDefaultImage(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $customerProfile = DB::table('defaultprofile')
                    ->where('isDelete', 0)
                    ->orderby('id', 'DESC');

                $defaultImageCount = DB::table('defaultprofile')
                    ->where('isDelete', 0)
                    ->count();
                $customerProfile = $customerProfile->skip($paginationStart);
                $customerProfile = $customerProfile->take($this->limit);
                $customerProfile = $customerProfile->get();
                $totalPages = ceil($defaultImageCount / $this->limit);
                $totalRecords = $defaultImageCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.default-profile', compact('customerProfile', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function addDefaultImage(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                if (request('profile')) {
                    $profileImage = base64_encode(file_get_contents($req->file('profile')));
                    $extension = $req->file('profile')->getClientOriginalExtension();
                }
                if (request('profile')) {
                    $profileImage = base64_encode(file_get_contents($req->file('profile')));
                } else {
                    $profileImage = null;
                }
                $default = DefaultProfile::create([
                    'name' => $req->name,
                    'profile' => '',
                ]);
                if ($profileImage) {
                    if (Str::contains($profileImage, 'storage')) {
                        $path = $profileImage;
                    } else {
                        $time = Carbon::now()->timestamp;

                        $imageName = 'defaultprofile_' . $default->id;
                        $path = DESTINATIONPATH . $imageName . $time . '.' . $extension;
                        File::delete($path);
                        file_put_contents($path, base64_decode($profileImage));
                    }
                } else {
                    $path = null;
                }
                $default->profile = $path;
                $default->update();
                return response()->json([
                    'success' => "DefaultImage Added",
                ]);
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function updateDefaultImage(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                $default = DefaultProfile::find($req->filed_id);
                if (request('profile')) {
                    $profileImage = base64_encode(file_get_contents($req->file('profile')));
                    $extension = $req->file('profile')->getClientOriginalExtension();
                } elseif ($default->profile) {
                    $profileImage = $default->profile;
                } else {
                    $profileImage = null;
                }

                if ($profileImage) {
                    if (Str::contains($profileImage, 'storage')) {
                        $path = $profileImage;
                    } else {
                        $time = Carbon::now()->timestamp;
                        $imageName = 'defaultprofile_' . $req->id;
                        $path = DESTINATIONPATH . $imageName . $time . '.' . $extension;
                        File::delete($default->profile);
                        file_put_contents($path, base64_decode($profileImage));
                    }
                } else {
                    $path = null;
                }

                $default->profile = $path;
                $default->name = $req->name;
                $default->update();
                return redirect()->back();
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function activeInactiveDefaultProfile(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                $default = DefaultProfile::find($req->status_id);
                $default->isActive = !$default->isActive;
                $default->update();
                return redirect()->back();
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }
}
