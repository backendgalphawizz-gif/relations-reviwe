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
use App\Models\UserModel\User as ApiUser;
use App\Models\AdminModel\SystemFlag;
use App\Models\AdminModel\Language;
use App\Models\AstrologerModel\Astrologer;
use App\Models\AstrologerModel\AstrologerAvailability;
use App\Services\AdminNotifyService;
use App\Services\SmsService;
use App\Services\UserAuthSessionService;
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
        if (Auth::guard('advisor')->check()) {
            return redirect()->route('advisor.dashboard');
        }

        return response()
            ->view('vendor.auth.login')
            ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }
    public function signup(){
        if (Auth::guard('advisor')->check()) {
            return redirect()->route('advisor.dashboard');
        }

        $astrologerCategoryIds = AstrologerCategory::get();
        $primarySkills = Skill::get();

        $values = explode(',', SystemFlag::where('name', 'Language')->first()->value);

        $languageKnowns = Language::whereIn('id', $values)->get();
        return response()
            ->view('vendor.auth.sign-up', compact('astrologerCategoryIds', 'primarySkills', 'languageKnowns'))
            ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }

    public function sendOtp(Request $request) {
        $request->validate([
            'mobile' => 'required'
        ]);

        $mobile = $request->input('mobile');
        
        $otp = (string) rand(1111, 9999);
        $user = User::whereHas('astrologer')->where('contactNo', $mobile)->first();


        if($user) {

            if($user->astrologer->isVerified == 0) {
                return response()->json(['status' => false, 'message' => 'Account not verified. Contact to admin']);
            }

            session()->put('otp', $otp);
            session()->put('otp_mobile', $mobile);
            session()->put('otp_expires_at', now()->addMinutes(10)->timestamp);

            $sms = SmsService::sendLoginOtp((string) $mobile, $otp);

            if (!$sms['ok']) {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed to send OTP SMS. Please try again.',
                    'smsResponse' => $sms['response'],
                ]);
            }

            // Do not expose OTP in production message; still return for local debug if needed
            $payload = [
                'status' => true,
                'message' => 'OTP sent successfully to your mobile number',
            ];
            if (config('app.debug')) {
                $payload['otp'] = $otp;
            }

            return response()->json($payload);
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
        $otp = (string) rand(1111, 9999);

        session()->put('otp', $otp);
        session()->put('otp_mobile', $mobile);
        session()->put('otp_expires_at', now()->addMinutes(10)->timestamp);

        $sms = SmsService::sendLoginOtp((string) $mobile, $otp);
        if (!$sms['ok']) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to send OTP SMS. Please try again.',
                'smsResponse' => $sms['response'],
                'mobile' => $mobile,
            ]);
        }

        $payload = [
            'status' => true,
            'message' => 'OTP sent successfully to your mobile number',
            'mobile' => $mobile,
        ];
        if (config('app.debug')) {
            $payload['otp'] = $otp;
        }

        return response()->json($payload);
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
        $otpExpiresAt = session()->get('otp_expires_at');
        $otpMobile = session()->get('otp_mobile');
        $user = User::whereHas('astrologer')
            ->where('contactNo', $request->mobile)
            ->first();

        if ($otpExpiresAt && now()->timestamp > (int) $otpExpiresAt) {
            return response()->json([
                'status' => false,
                'message' => 'OTP expired. Please request a new OTP.',
            ], 200);
        }

        if ($otpMobile && (string) $otpMobile !== (string) $request->mobile) {
            return response()->json([
                'status' => false,
                'message' => 'Mobile number does not match the OTP request.',
            ], 200);
        }

        if ($sessionOTP == $request->otp && $user) {
            Auth::guard('web')->logout();
            Auth::guard('advisor')->login($user);

            // One device at a time: clear previous mobile + web tokens/FCM
            $apiUser = ApiUser::find($user->id);
            if ($apiUser) {
                UserAuthSessionService::invalidateApiSessionForWebLogin($apiUser);
            }

            $userId = $user->id;
            $appVersion = $request->input('appVersion');
            $osName = $request->input('osName');
            $userAgent = $request->input('userAgent');
            $appId = $request->input('appId') ?: 3;
            $fcmToken = trim((string) $request->input('fcm_token', ''));

            if ($fcmToken !== '') {
                // Ensure this FCM is not attached to any other user/device
                UserDeviceDetail::where('fcmToken', $fcmToken)->update(['fcmToken' => '']);
            }

            // After wipe, create/refresh ONLY this web device
            $userDevice = UserDeviceDetail::where(['userId' => $userId, 'appId' => $appId])->first();
            if (!$userDevice) {
                $userDevice = new UserDeviceDetail;
            }

            $userDevice->userId = $userId;
            $userDevice->appId = $appId;
            $userDevice->fcmToken = $fcmToken !== '' ? $fcmToken : '';
            $userDevice->deviceId = $userAgent;
            $userDevice->deviceManufacturer = $osName;
            $userDevice->deviceModel = $appVersion;
            $userDevice->appVersion = '1.1.0';
            $userDevice->isActive = 1;
            $userDevice->save();

            $freshUser = ApiUser::find($userId);
            if ($freshUser) {
                $freshUser->desktop_token = $fcmToken !== '' ? $fcmToken : null;
                $freshUser->token = null;
                $freshUser->fcm_token = null;
                $freshUser->save();
            }

            session()->forget('otp');
            session()->forget('otp_mobile');
            session()->forget('otp_expires_at');

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
        $user = Auth::guard('advisor')->user();
        if ($user) {
            $apiUser = ApiUser::find($user->id);
            if ($apiUser) {
                UserAuthSessionService::endWebSession($apiUser);
            } else {
                // Fallback: clear web device + desktop token directly
                DB::table('user_device_details')
                    ->where('userId', $user->id)
                    ->where('appId', 3)
                    ->update([
                        'isActive' => 0,
                        'fcmToken' => '',
                        'updated_at' => now(),
                    ]);
                DB::table('users')->where('id', $user->id)->update([
                    'desktop_token' => null,
                    'updated_at' => now(),
                ]);
            }
        }

        Auth::guard('advisor')->logout();
        Auth::guard('web')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
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

            AdminNotifyService::notifyNewAdvisorRequest(
                (string) $req->name,
                (int) $astrologer->id,
                (int) $user->id
            );

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
