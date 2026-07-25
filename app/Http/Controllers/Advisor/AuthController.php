<?php

namespace App\Http\Controllers\Advisor;

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
use App\Models\User;
use App\Models\UserRole;
use App\Models\AstrologerModel\AstrologerCategory;
use App\Models\AstrologerModel\Skill;
use App\Models\UserModel\UserDeviceDetail;
use App\Models\AdminModel\SystemFlag;
use App\Models\AdminModel\Language;
use App\Models\AstrologerModel\Astrologer;
use App\Models\AstrologerModel\AstrologerAvailability;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    /**
     * Show specified view.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(){
        return view('vendor.auth.login');
    }
    public function signup(){
        $astrologerCategoryIds = AstrologerCategory::get();
        $primarySkills = Skill::get();

        $values = explode(',', SystemFlag::where('name', 'Language')->first()->value);

        $languageKnowns = Language::whereIn('id', $values)->get();
        return view('vendor.auth.sign-up' ,compact('astrologerCategoryIds', 'primarySkills', 'languageKnowns'));
    }

    public function sendOtp(Request $request) {
        $request->validate([
            'mobile' => 'required'
        ]);

        $mobile = $request->input('mobile');
        
        $otp = rand(1111, 9999);
        $user = User::whereHas('astrologer')->where('contactNo', $mobile)->first();


        if($user) {

            if($user->astrologer->isVerified == 0) {
                return response()->json(['status' => false, 'message' => 'Account not verified. Contact to admin']);
            }

            session()->put('otp', $otp);
            return response()->json(['status' => true, 'message' => 'OTP sent success (OTP: '. $otp .')']);
        }
        return response()->json(['status' => false, 'message' => 'Invalid mobile number']);
    }
    public function signupOtp(Request $request) {
        $request->validate([
            'name' => 'required|min:3|max:25',
            'email' => 'required|email|unique:users,email|min:3|max:25',
            'mobile' => 'required|min:10|max:10|unique:users,contactNo'
        ]);

        $mobile = $request->input('mobile');
        $otp = rand(1111, 9999);

        return response()->json(['status' => true, 'message' => 'OTP sent success (OTP: '. $otp .')', 'otp' => $otp, 'mobile' => $mobile]);
    }

    public function authenticate(Request $request) {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required',
            'otp' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'error' => $validator->getMessageBag()->toArray(),
                'message' => 'Please fill required fields'
            ], 200);
        }
        $sessionOTP = session()->get('otp');
        $user = User::whereHas('astrologer')
            ->where('contactNo', $request->mobile)
            ->first();

        if ($sessionOTP == $request->otp && $user) {
            Auth::guard('web')->logout();
            Auth::guard('advisor')->login($user);

            $user->desktop_token = $request->fcm_token;
            $user->save();

            $userId = $user->id;
            $appVersion = $request->input('appVersion');
            $osName = $request->input('osName');
            $userAgent = $request->input('userAgent');
            $appId = $request->input('appId');
            $fcmToken = $request->input('fcm_token');

            UserDeviceDetail::where('fcmToken', $fcmToken)->update(['fcmToken' => '']);

            $userDevice = UserDeviceDetail::where(['userId' => $userId, 'appId' => $appId])->first();
            if(!$userDevice) {
                $userDevice = new UserDeviceDetail;
            }

            $userDevice->userId = $userId;
            $userDevice->appId = $appId;
            $userDevice->fcmToken = $fcmToken;
            $userDevice->deviceId = $userAgent;
            $userDevice->deviceManufacturer = $osName;
            $userDevice->deviceModel = $appVersion;
            $userDevice->appVersion = '1.1.0';
            $userDevice->save();

            session()->forget('otp');

            return response()->json([
                'status' => true,
                'message' => 'Advisor logged in success',
                'data' => Auth::guard('advisor')->user(),
                'is_login' => Auth::guard('advisor')->check()
            ], 200);
        }

        return response()->json([
            'status' => false,
            'error' => [],
            'message' => 'Invalid OTP'
        ], 200);


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
        Auth::guard('advisor')->logout();
        Auth::guard('web')->logout();
        session()->forget('token');
        session()->forget('otp');
        return redirect()->route('advisor.login');
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

    public function postSignup(Request $req) {
        try {
            //code...
        

            $validator = Validator::make($req->all(), [
                'name' => 'required|string',
                'email' => 'required|unique:users,email',
                'mobile' => 'required|max:10|unique:users,contactNo',
                'gender' => 'required',
                'birthDate' => 'required',
                'astrologerCategoryId' => 'required',
                'primarySkill' => 'required',
                'languageKnown' => 'required',
                'experienceInYears' => 'required',
                'highestQualification' => 'required',
            ]);
            if ($validator->fails()) {
                DB::rollback();
                return response()->json([
                    'message' => $validator->messages(),
                    'status' => false,
                ], 400);
            }

            $user = new User;
            $user->name = $req->name;
            $user->contactNo = $req->mobile;
            $user->email = $req->email;
            $user->birthDate = date('Y-m-d H:i:s',strtotime($req->birthDate));
            $user->gender = $req->gender;
            $user->location = $req->city;
            $user->countryCode = '+91';
            $user->save();

            $astrologer = Astrologer::create([
                'name' => $req->name,
                'userId' => $user->id,
                'email' => $req->email,
                'contactNo' => $req->mobile,
                'gender' => $req->gender,
                'birthDate' => $req->birthDate,
                'primarySkill' => implode(',', $req->primarySkill),
                // 'allSkill' => implode(',', $req->allSkill),
                'languageKnown' => implode(',', $req->languageKnown),
                'charge' => $req->charge,
                'experienceInYears' => $req->experienceInYears,
                'currentCity' => $req->city,
                'highestQualification' => $req->highestQualification,
                'degree' => $req->degree,
                'college' => $req->college,
                'astrologerCategoryId' => implode(',', $req->astrologerCategoryId),
                'loginBio' => $req->bio,
                'isVerified' => false,
                'country' => 'India',
                'videoCallRate' => 0,
                'reportRate' => 0
            ]);

            UserRole::create([
                'userId' => $user->id,
                'roleId' => 2,
            ]);

            if ($req->day) {
                foreach ($req->day as $day) {
                    if(isset($req->from_time[$day][0]) && isset($req->to_time[$day][0]) && $req->from_time[$day][0]!='' && $req->to_time[$day][0]!='') {
                        AstrologerAvailability::create([
                            'astrologerId' => $astrologer['id'],
                            'day' => ucwords($day),
                            'fromTime' => (!empty(strtotime($req->from_time[$day][0]))) ? date('h:i A',strtotime($req->from_time[$day][0])) : null,
                            'toTime' => (!empty(strtotime($req->to_time[$day][0]))) ? date('h:i A',strtotime($req->to_time[$day][0])) : null,
                            'createdBy' => $astrologer['id'],
                            'modifiedBy' => $astrologer['id'],
                        ]);
                    }
                }
            }

            DB::commit();
            return response()->json([
                'message' => 'Registered successfully',
                'status' => true
            ], 200);
        } catch (\Throwable $th) {
            // throw $th;
            DB::rollback();
            return response()->json([
                'message' => $th->getMessage(),
                'status' => false
            ], 403);
        }

    }

}
