<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\UserRole;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PDF;
use Response;

define('LOGINPATH', '/admin/login');

class CustomerController extends Controller
{
    public $limit = 15;
    public $paginationStart;
    public $path;
    public $customers = [];

    public function addUser()
    {
        return view('pages.add-customer');
    }

    public function addUserApi(Request $req)
    {
        try {
            $validator = Validator::make($req->all(), [
                'name' => 'required|min:3|max:25',
                'birthDate' => 'required',
                'addressLine1' => 'required',
                'contactNo' => 'required|unique:users|digits:10',
                'email' => 'required|unique:users|email',
                'pincode' => 'required|digits:6',
                'gender' => 'required',
                'location' => 'required'
            ], [
                'addressLine1.required' => 'Please fill current address'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->getMessageBag()->toArray(),
                ]);
            }
            if (Auth::guard('web')->check()) {
                if (request('profile')) {
                    $image = base64_encode(file_get_contents($req->file('profile')));
                } else {
                    $image = null;
                }
                $user = Customer::create([
                    'name' => $req->name,
                    'contactNo' => $req->contactNo,
                    'email' => $req->email,
                    'password' => Hash::make($req->password),
                    'birthDate' => $req->birthDate,
                    'birthTime' => $req->birthTime,
                    'birthPlace' => $req->birthPlace,
                    'addressLine1' => $req->addressLine1,
                    'location' => $req->location,
                    'pincode' => $req->pincode,
                    'gender' => $req->gender,
                    'countryCode' => $req->countryCode,
                ]);
                if ($image) {
                    if (Str::contains($image, 'storage')) {
                        $path = $image;
                    } else {
                        $time = Carbon::now()->timestamp;
                        $destinationpath = 'storage/images/';
                        $imageName = 'profile_' . $user->id;
                        $path = $destinationpath . $imageName . $time . '.png';
                        File::delete($path);
                        file_put_contents(public_path($path), base64_decode($image));
                    }
                } else {
                    $path = null;
                }
                $user->profile = $path;
                $user->update();
                UserRole::create([
                    'userId' => $user->id,
                    'roleId' => 3,
                ]);
                return response()->json([
                    'success' => "Customer Added",
                ]);
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    //Get Customer

    public function getUser(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $customers = DB::table('users as u')
                    ->join('user_roles', 'user_roles.userId', '=', 'u.id')
                    ->where('u.isDelete', '=', false)
                    ->where('user_roles.roleId', '=', 3)
                    ->select('u.*', 'user_roles.roleId')
                    ->orderBy('u.id', 'DESC');
                $searchString = $request->searchString ? $request->searchString : null;
                if ($searchString) {
                    $customers = $customers->where(function ($q) use ($searchString) {
                        $q->where('u.name', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('u.contactNo', 'LIKE', '%' . $searchString . '%');
                    });
                }
                $customers = $customers->skip($paginationStart);
                $customers = $customers->take($this->limit);
                $customers = $customers->get();
                $userCount = DB::table('users')
                    ->join('user_roles', 'user_roles.userId', '=', 'users.id')
                    ->where('users.isActive', '=', true)
                    ->where('users.isDelete', '=', false)
                    ->where('user_roles.roleId', '=', 3);
                if ($searchString) {
                    $userCount = $userCount->where(function ($q) use ($searchString) {
                        $q->where('users.name', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('users.contactNo', 'LIKE', '%' . $searchString . '%');
                    });
                }
                $userCount = $userCount->count();
                $totalPages = ceil($userCount / $this->limit);
                $totalRecords = $userCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.customer-list', compact('customers', 'searchString', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function deleteUser(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $user = Customer::find($request->del_id);
                if ($user) {
                    $user->isDelete = true;
                    $user->update();
                } else {
                    return redirect(LOGINPATH);
                }
                return redirect()->route('customers');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function editUser(Request $req)
    {
        $user = Customer::find($req->id);

        return view('pages.edit-customer')->with(['customer' => $user]);
    }

    public function editUserApi(Request $req)
    {

        $validator = Validator::make($req->all(), [
            'name' => 'required|min:3|max:25',
            'birthDate' => 'required',
            'addressLine1' => 'required',
            'contactNo' => 'required|digits:10',
            // 'email' => 'required|email',
            'pincode' => 'required|digits:6',
            'gender' => 'required',
            'location' => 'required'
        ], [
            'addressLine1.required' => 'Please fill current address'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->getMessageBag()->toArray(),
            ]);
        }

        try {
            if (Auth::guard('web')->check()) {
                $user = Customer::find($req->field_id);
                if (request('profile')) {
                    $image = base64_encode(file_get_contents($req->file('profile')));
                } elseif ($user->profile) {
                    $image = $user->profile;
                } else {
                    $image = null;
                }
                if ($user) {
                    $time = Carbon::now()->timestamp;
                    if ($image) {
                        if (Str::contains($image, 'storage')) {
                            $path = $image;
                        } else {
                            $destinationpath = 'storage/images/';
                            $imageName = 'user_' . $req->id . $time;
                            $path = $destinationpath . $imageName . '.png';
                            File::delete($user->profile);
                            file_put_contents($path, base64_decode($image));
                        }
                    } else {
                        $path = null;
                    }
                    $user->name = $req->name;
                    $user->contactNo = $req->contactNo;
                    $user->password = Hash::make($req->password);
                    $user->birthDate = $req->birthDate;
                    $user->birthTime = $req->birthTime;
                    $user->birthPlace = $req->birthPlace;
                    $user->addressLine1 = $req->addressLine1;
                    $user->location = $req->location;
                    $user->profile = $path;
                    $user->pincode = $req->pincode;
                    $user->gender = $req->gender;
                    $user->email = $req->email;
                    $user->countryCode = $req->countryCode;
                    $user->update();
                    return response()->json([
                        'success' => "Customer Updated",
                    ]);
                }
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }
    public function editUserDetail()
    {
        return view('pages.customer-detail');
    }

    public function getUserDetailApi(Request $req, $id)
    {
        try {
            if (Auth::guard('web')->check()) {
                $user = DB::table('users')
                    ->where('id', '=', $id)
                    ->get();
                if ($user) {
                    $follower = DB::table('astrologer_followers')
                        ->join('astrologers', 'astrologer_followers.astrologerId', '=', 'astrologers.id')
                        ->where('astrologer_followers.userId', '=', $id)
                        ->select('astrologers.*', 'astrologer_followers.created_at as followingDate')
                        ->orderBy('astrologer_followers.id', 'DESC')
                        ->get();
                    if ($follower && count($follower) > 0) {
                        foreach ($follower as $follow) {
                            $languages = DB::table('languages')
                                ->whereIn('id', explode(',', $follow->languageKnown))
                                ->select('languageName')
                                ->get();

                            $allSkill = DB::table('skills')
                                ->whereIn('id', explode(',', $follow->languageKnown))
                                ->get('name');
                            $follow->languageKnown = $languages;
                            $follow->allSkill = $allSkill;
                        }
                    }
                    $orderRequest = DB::table('order_request')
                        ->leftjoin('product_categories', 'product_categories.id', '=', 'order_request.productCategoryId')
                        ->leftjoin('astromall_products', 'astromall_products.id', '=', 'order_request.productId')
                        ->leftjoin('order_addresses', 'order_addresses.id', '=', 'order_request.orderAddressId')
                        ->where('order_request.userId', '=', $id)
                        ->where('order_request.orderType', '=', 'astromall');

                    $orderRequestCount = $orderRequest->count();
                    $orderRequest->select('order_request.*', 'product_categories.name as productCategory', 'astromall_products.productImage', 'astromall_products.amount as productAmount', 'astromall_products.description',
                        'order_addresses.name as orderAddressName', 'order_addresses.phoneNumber', 'order_addresses.flatNo', 'order_addresses.locality', 'order_addresses.landmark', 'order_addresses.city', 'order_addresses.state', 'order_addresses.country', 'order_addresses.pincode', 'astromall_products.name as productName'
                    );
                    $orderRequest->orderBy('order_request.id', 'DESC');
                    if ($req->startIndex >= 0 && $req->fetchRecord) {
                        $orderRequest->skip($req->startIndex);
                        $orderRequest->take($req->fetchRecord);
                    }
                    $orderRequest = $orderRequest->get();
                    $giftList = DB::table('astrologer_gifts')
                        ->join('gifts', 'gifts.id', 'astrologer_gifts.giftId')
                        ->join('astrologers as astro', 'astro.id', '=', 'astrologer_gifts.astrologerId')
                        ->where('astrologer_gifts.userId', '=', $id);

                    $giftListCount = $giftList->count();
                    $giftList->select('gifts.name as giftName', 'astrologer_gifts.*', 'astro.id as astrologerId', 'astro.name as astrolgoerName', 'astro.contactNo');

                    $giftList->orderBy('astrologer_gifts.id', 'DESC');
                    if ($req->startIndex >= 0 && $req->fetchRecord) {
                        $giftList->skip($req->startIndex);
                        $giftList->take($req->fetchRecord);
                    }
                    $giftList = $giftList->get();
                    $chatHistory = DB::Table('chatrequest')
                        ->join('astrologers as astr', 'astr.id', '=', 'chatrequest.astrologerId')
                        ->where('chatrequest.userId', '=', $id);

                    $chatHistoryCount = $chatHistory->count();
                    $chatHistory->select('chatrequest.*', 'astr.id as astrologerId', 'astr.name as astrologerName', 'astr.contactNo', 'astr.profileImage', 'astr.charge');
                    $chatHistory->orderBy('chatrequest.id', 'DESC');
                    $chatHistory = $chatHistory->get();
                    $callHistory = DB::Table('callrequest')
                        ->join('astrologers', 'astrologers.id', '=', 'callrequest.astrologerId')
                        ->where('callrequest.userId', '=', $id);
                    $callHistoryCount = $callHistory->count();
                    $callHistory->select('callrequest.*', 'astrologers.id as astrologerId', 'astrologers.name as astrologerName', 'astrologers.contactNo', 'astrologers.profileImage', 'astrologers.charge');
                    $callHistory->orderBy('callrequest.id', 'DESC');

                    if ($req->startIndex >= 0 && $req->fetchRecord) {
                        $callHistory->skip($req->startIndex);
                        $callHistory->take($req->fetchRecord);
                    }
                    $callHistory = $callHistory->get();

                    $reportHistory = DB::Table('user_reports')
                        ->join('astrologers', 'astrologers.id', '=', 'user_reports.astrologerId')
                        ->join('report_types', 'report_types.id', '=', 'user_reports.reportType')
                        ->where('user_reports.userId', '=', $id);

                    $reportHistoryCount = $reportHistory->count();

                    $reportHistory->select('user_reports.*', 'astrologers.id as astrologerId', 'astrologers.name as astrologerName', 'astrologers.contactNo', 'report_types.title', 'astrologers.profileImage', 'astrologers.reportRate');

                    $reportHistory->orderBy('user_reports.id', 'DESC');
                    if ($req->startIndex >= 0 && $req->fetchRecord) {
                        $reportHistory->skip($req->startIndex);
                        $reportHistory->take($req->fetchRecord);
                    }
                    $reportHistory = $reportHistory->get();
                    if ($reportHistory && count($reportHistory) > 0) {
                        for ($i = 0; $i < count($reportHistory); $i++) {
                            if (!$reportHistory[$i]->reportFile) {
                                $reportHistory[$i]->isFileUpload = false;
                            } else {
                                $reportHistory[$i]->isFileUpload = true;
                            }
                        }
                    }
                    $wallet = DB::table('wallettransaction')
                        ->leftjoin('order_request', 'order_request.id', '=', 'wallettransaction.orderId')
                        ->leftjoin('astrologers', 'astrologers.id', '=', 'wallettransaction.astrologerId')
                        ->where('wallettransaction.userId', '=', $id);
                    $walletCount = $wallet->count();
                    $wallet->select('wallettransaction.*', 'astrologers.name', 'order_request.totalMin');
                    $wallet->orderBy('wallettransaction.id', 'DESC');
                    if ($req->startIndex >= 0 && $req->fetchRecord) {
                        $wallet->skip($req->startIndex);
                        $wallet->take($req->fetchRecord);
                    }
                    $wallet = $wallet->get();

                    $payment = DB::table('payment')
                        ->where('userId', '=', $id)
                        ->orderBy('id', 'DESC');
                    $paymentCount = $payment->count();
                    if ($req->startIndex >= 0 && $req->fetchRecord) {
                        $payment->skip($req->startIndex);
                        $payment->take($req->fetchRecord);
                    }
                    $payment = $payment->get();

                    $notification = DB::table('user_notifications')
                        ->where('userId', '=', $id)
                        ->orderBy('id', 'DESC')
                        ->get();
                    $orderRequests = array(
                        'totalCount' => $orderRequestCount,
                        'order' => $orderRequest,
                    );
                    $giftLists = array(
                        'totalCount' => $giftListCount,
                        'gifts' => $giftList,
                    );
                    $chatHistorys = array(
                        'totalCount' => $chatHistoryCount,
                        'chatHistory' => $chatHistory,
                    );
                    $callHistorys = array(
                        'totalCount' => $callHistoryCount,
                        'callHistory' => $callHistory,
                    );
                    $reportHistorys = array(
                        'totalCount' => $reportHistoryCount,
                        'reportHistory' => $reportHistory,
                    );
                    $wallets = array(
                        'totalCount' => $walletCount,
                        'wallet' => $wallet,
                    );
                    $payments = array(
                        'totalCount' => $paymentCount,
                        'payment' => $payment,
                    );
                    $user[0]->follower = $follower;
                    $user[0]->orders = $orderRequests;
                    $user[0]->sendGifts = $giftLists;
                    $user[0]->chatRequest = $chatHistorys;
                    $user[0]->callRequest = $callHistorys;
                    $user[0]->reportRequest = $reportHistorys;
                    $user[0]->walletTransaction = $wallets;
                    $user[0]->paymentLogs = $payments;
                    $user[0]->notification = $notification;
                    $result = json_decode($user);
                    return view('pages.customer-detail')->with(['result' => $result]);

                }
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function fetch_data(Request $request)
    {
        if ($request->ajax()) {
            $customers = DB::table('users as us')
                ->join('user_roles', 'user_roles.userId', '=', 'us.id')
                ->where('us.isActive', '=', true)
                ->where('us.isDelete', '=', false)
                ->where('user_roles.roleId', '=', 3)
                ->select('us.*', 'user_roles.roleId')
                ->orderBy('us.id', 'DESC')->paginate(15);
            return view('pages.customer-list', compact('customers'))->render();
        }
    }

    public function printCustomerList(Request $request)
    {
        try {
            $customers = DB::table('users')
            ->join('user_roles', 'user_roles.userId', '=', 'users.id')
            ->where('users.isDelete', '=', false)
            ->where('user_roles.roleId', '=', 3)
            ->select('users.*', 'user_roles.roleId')
            ->orderBy('users.id', 'DESC');
            $searchString = $request->searchString ? $request->searchString : null;
            if ($searchString) {
                $customers = $customers->where(function ($q) use ($searchString) {
                    $q->where('users.name', 'LIKE', '%' . $searchString . '%')
                        ->orWhere('users.contactNo', 'LIKE', '%' . $searchString . '%');
                });
            }
            $customers = $customers->get();
            $data = [
                'title' => 'Customers',
                'date' => Carbon::now()->format('d-m-Y h:i'),
                'customers' => $customers,
            ];
            $pdf = PDF::loadView('pages.customerList', $data);
           return $pdf->download('CustomerList.pdf');

        } catch (\Exception$e) {
            return dd($e->getMessage());
        }
    }

    public function exportCustomerCSV(Request $request)
    {
        $this->path = env('API_URL');
        $customers = DB::table('users')
            ->join('user_roles', 'user_roles.userId', '=', 'users.id')
            ->where('users.isDelete', '=', false)
            ->where('user_roles.roleId', '=', 3)
            ->select('users.*', 'user_roles.roleId')
            ->orderBy('users.id', 'DESC');
        $searchString = $request->searchString ? $request->searchString : null;
        if ($searchString) {
            $customers = $customers->where(function ($q) use ($searchString) {
                $q->where('users.name', 'LIKE', '%' . $searchString . '%')
                    ->orWhere('users.contactNo', 'LIKE', '%' . $searchString . '%');
            });
        }
        $customers = $customers->get();
        // $callHistory =

        $headers = array(
            "Content-type" => "text/csv",
        );
        $filename = public_path("CustomerList.csv");
        $handle = fopen($filename, 'w');
        fputcsv($handle, [
            "ID",
            "Name",
            "ContactNo",
            "BirthDate",
        ]);
        for ($i = 0; $i < count($customers); $i++) {
            fputcsv($handle, [
                $i + 1,
                $customers[$i]->name,
                $customers[$i]->contactNo,
                date('d-m-Y', strtotime($customers[$i]->birthDate)),
            ]);
        }
        fclose($handle);
        return Response::download($filename, "customerList.csv", $headers);
    }
}
