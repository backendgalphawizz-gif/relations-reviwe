<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\UserModel\Payment;
use App\Models\UserModel\UserWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function addPayment(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }
            $data = $req->only(
                'amount',
                'paymentMode',
            );
            $validator = Validator::make($data, [
                'amount' => 'required',
                'paymentMode' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->messages(),
                    'status' => 400,
                ], 400);
            }
            $payment = Payment::Create([
                'paymentMode' => $req->paymentMode,
                'paymentReference' => $req->paymentReference,
                'amount' => $req->amount,
                'userId' => $id,
                'paymentStatus' => $req->paymentStatus,
                'createdBy' => $id,
                'modifiedBy' => $id,
                'signature' => $req->signature,
                'orderId' => $req->orderId,
            ]);
            if ($req->paymentStatus == 'Success') {
                $userWallet = UserWallet::query()
                    ->where('userId', '=', $id)
                    ->get();
                if ($userWallet && count($userWallet) > 0) {
                    $userWallet[0]->amount = $userWallet[0]->amount + $req->amount;
                    $userWallet[0]->update();
                } else {
                    $wallet = UserWallet::create([
                        'userId' => $id,
                        'amount' => $req->amount,
                        'createdBy' => $id,
                        'modifiedBy' => $id,
                    ]);
                }

            }
            $payment->totalWalletAmount = ($userWallet && count($userWallet) > 0) ? $userWallet[0]->amount : $wallet->amount;
            return response()->json([
                'status' => 200,
                'message' => 'Payment Add Successfully',
                'recordList' => $payment,
            ], 200);
        } catch (\Exception$e) {
            error_log($e->getMessage());
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function getRechargeAmount()
    {
        try {
            $rechargeAmount = DB::table('rechargeamount')->get();
            return response()->json([
                'status' => 200,
                'message' => 'RechargeAmount Get Successfully',
                'recordList' => $rechargeAmount,
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
