<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\services\FCMService;

class OrderController extends Controller
{
    public $path;
    public $limit = 15;
    public $paginationStart;
    public function getOrders(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $orderRequest = DB::table('order_request as req')
                    ->join('astromall_products as astromall', 'astromall.id', '=', 'req.productId')
                    ->join('product_categories as category', 'category.id', '=', 'astromall.productCategoryId')
                    ->join('users as us', 'us.id', '=', 'req.userId')
                    ->leftjoin('order_addresses as address', 'address.id', '=', 'req.orderAddressId')
                    ->where('req.orderType', '=', 'astromall')
                    ->select('req.*', 'us.name as userName', 'us.contactNo as userContactNo', 'astromall.name as productName', 'category.name as categoryName', 'astromall.productImage',
                        'address.name as addressUserName', 'address.phoneNumber', 'address.phoneNumber2', 'address.flatNo', 'address.locality', 'address.landmark', 'address.city', 'address.state', 'address.country', 'address.pincode'
                    )
                    ->orderBy('req.id', 'DESC');
                $searchString = $request->searchString ? $request->searchString : null;
                if ($searchString) {
                    $orderRequest = $orderRequest->where(function ($q) use ($searchString) {
                        $q->where('us.name', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('us.contactNo', 'LIKE', '%' . $searchString . '%');
                    });
                }

                $orderCount = DB::table('order_request')
                    ->join('astromall_products', 'astromall_products.id', '=', 'order_request.productId')
                    ->join('product_categories', 'product_categories.id', '=', 'astromall_products.productCategoryId')
                    ->join('users', 'users.id', '=', 'order_request.userId')
                    ->leftjoin('order_addresses', 'order_addresses.id', '=', 'order_request.orderAddressId')
                    ->where('order_request.orderType', '=', 'astromall');
                if ($searchString) {
                    $orderCount = $orderCount->where(function ($q) use ($searchString) {
                        $q->where('users.name', 'LIKE', '%' . $searchString . '%')
                            ->orWhere('users.contactNo', 'LIKE', '%' . $searchString . '%');
                    });
                }
                $orderCount = $orderCount->count();
                $orderRequest->skip($paginationStart);
                $orderRequest->take($this->limit);
                $orderRequest = $orderRequest->get();
                if ($orderRequest && count($orderRequest) > 0) {
                    foreach ($orderRequest as $od) {
                        if ($od->gstPercent > 0) {
                            $od->gstAmount = $od->payableAmount / $od->gstPercent;
                            $od->gstAmount = number_format($od->gstAmount, 2, '.', ',');
                        } else {
                            $od->gstAmount = 0;
                        }
                    }
                }
                $totalPages = ceil($orderCount / $this->limit);
                $totalRecords = $orderCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.order', compact('orderRequest', 'searchString', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect('/admin/login');
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function changeOrderStatus(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $data = array(
                    'orderStatus' => $request->status,
                    'updated_at' => Carbon::now(),
                );
                DB::table('order_request')->where('id', '=', $request->id)->update($data);
                $userDeviceDetail = DB::table('user_device_details')
                    ->join('order_request', 'order_request.userId', '=', 'user_device_details.userId')
                    ->where('order_request.id', '=', $request->id)
                    ->select('user_device_details.*')
                    ->get();
                if ($userDeviceDetail && count($userDeviceDetail) > 0) {
                    $title = $request->status == 'Confirmed' ? 'Your Order has been accept from admin' : 'Your Order Delivered Successfully';
                    FCMService::send(
                        $userDeviceDetail,
                        [
                            'title' => $title,
                            'body' => ['description' => $title, 'status' => ''],
                        ]
                    );
                }
                return redirect()->back();
            } else {
                return redirect('/admin/login');
            }
        } catch (\Exception$e) {
            return dd($e->getMessage());
        }
    }
}
