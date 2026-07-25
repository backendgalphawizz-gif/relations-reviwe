<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Request\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
define('LOGINPATH', '/admin/login');

class AuthController extends Controller
{
    /**
     * Show specified view.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function loginView()
    {
        return view('pages/login', [
            'layout' => 'login',
        ]);
    }
    public function deleteView()
    {
        return view('pages/delete-account', [
            'layout' => 'login',
        ]);
    }
    public function privacy_policy()
    {
        $data = DB::table('static_pages')->where('slug','privacy_policy')->first();

        return view('privacypolicy',compact('data'));
    }
    public function termsCondition()
    {
        $data = DB::table('static_pages')->where('slug','terms_condition')->first();

        return view('terms1',compact('data'));
    }

    /**
     * Authenticate login user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function post_delete_account(Request $request) {
        if(DB::table('users')->where('contactNo', $request->mobile)->delete()) {
            return response()->json(['status' => true]);
        }
        return response()->json(['status' => false]);

    }
    public function login(LoginRequest $request)
    {
        if (!\Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
        ])) {
            return dd('error');
        }
    }

    /**
     * Logout user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        \Auth::logout();
        session()->forget('token');
        return redirect(LOGINPATH);
    }

    public function editProfile()
    {
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            return view('pages.edit-profile', compact('user'));
        } else {
            return redirect(LOGINPATH);
        }
    }

    public function changePassword(Request $request)
    {
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            if ($user && !password_verify($request->old, $user->password)) {
                // return response()->json([
                //     'error' => ["Password doesn't match with old password"],
                // ]);
                return back()->with('error', "Password doesn't match with old password");
            } else {
                $user->password = Hash::make($request->new);
                $user->update();
                // return response()->json([
                //     'success' => ['Update Password'],
                // ]);
                return back()->with('success', "Password updated success");
            }
        } else {
            return redirect(LOGINPATH);
        }
    }

    public function editProfileApi(Request $req)
    {
        try {
            $validator = Validator::make($req->all(), [
                'email' => 'required',
                'name' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->getMessageBag()->toArray(),
                ]);
            }
            if (Auth::guard('web')->check()) {
                $user = Auth::guard('web')->user();
                if (request('profile')) {
                    $image = base64_encode(file_get_contents($req->file('profile')));
                } elseif ($user->profile) {
                    $image = $user->profile;
                } else {
                    $image = null;
                }
                if ($image) {
                    if (Str::contains($image, 'storage')) {
                        $path = $image;
                    } else {
                        $time = Carbon::now()->timestamp;
                        $destinationpath = 'storage/images/';
                        $imageName = 'profile_' . $user->id;
                        $path = $destinationpath . $imageName . $time . '.png';
                        File::delete($user->profile);
                        file_put_contents(public_path($path), base64_decode($image));
                    }
                } else {
                    $path = null;
                }
                $user->name = $req->name;
                $user->email = $req->email;
                $user->profile = $path;
                $user->update();
            } else {
                return redirect('/admin/login');
            }

        } catch (\Exception$e) {
            return dd($e->getMessage());
        }
    }
}
