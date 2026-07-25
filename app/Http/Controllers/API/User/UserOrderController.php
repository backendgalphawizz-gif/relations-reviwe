<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\UserModel\UserOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserOrderController extends Controller
{
    public function addUserOrder(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }
            $data = $req->only(
                'productCategoryId',
                'productId',
                'orderAddressId',
                'payableAmount',
                'gstPercent',
                'paymentMethod',
                'totalPayable'
            );

            $validator = Validator::make($data, [
                'productCategoryId' => 'required',
                'productId' => 'required',
                'orderAddressId' => 'required',
                'payableAmount' => 'required',
                'paymentMethod' => 'required',
            ]);

            if ($validator->fails()) {
                DB::rollback();
                return response()->json([
                    'error' => $validator->messages(),
                    'status' => 400,
                ], 400);
            }
            $order = UserOrder::create([
                'userId' => $id,
                'productCategoryId' => $req->productCategoryId,
                'productId' => $req->productId,
                'orderAddressId' => $req->orderAddressId,
                'payableAmount' => $req->payableAmount,
                'orderType' => 'astromall',
                'gstPercent' => $req->gstPercent,
                'totalPayable' => $req->totalPayable,
                'payamentMethod' => $req->payamentMethod,
                'orderStatus' => 'Pending',
            ]);

            $wallet = DB::table('user_wallets')
                ->where('userId', '=', $id)
                ->get();
            $wallets = array(
                'amount' => $wallet[0]->amount - $req->totalPayable,
            );
            DB::table('user_wallets')
                ->where('id', $wallet[0]->id)
                ->update($wallets);

            $transaction = array(
                'userId' => $id,
                'amount' => $req->totalPayable,
                'isCredit' => false,
                "transactionType" => 'astromallOrder',
            );
            DB::table('wallettransaction')->insert($transaction);
            return response()->json([
                'message' => 'User Order add sucessfully',
                'recordList' => $order,
                'status' => 200,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }

    }

    public function cancelOrder(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }
            $order = DB::table('order_request')->where('id', '=', $req->id)->get();
            $data = array(
                'orderStatus' => 'Cancelled',
            );
            DB::table('order_request')->where('id', '=', $req->id)->update($data);
            $wallet = DB::table('user_wallets')
                ->where('userId', '=', $id)
                ->get();
            $wallets = array(
                'amount' => $wallet[0]->amount + $order[0]->totalPayable,
            );
            DB::table('user_wallets')
                ->where('id', $wallet[0]->id)
                ->update($wallets);

            $transaction = array(
                'userId' => $id,
                'amount' => $order[0]->totalPayable,
                'isCredit' => true,
                "transactionType" => 'astromallOrder',
            );
            $res = array('totalPayable' => $order[0]->totalPayable);
            DB::table('wallettransaction')->insert($transaction);
            return response()->json([
                'message' => 'User Order Cancel sucessfully',
                'recordList' => [$res],
                'status' => 200,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }
}
