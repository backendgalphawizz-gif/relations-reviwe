<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\AstrologerModel\AstrologerGift;
use App\Models\UserModel\Gift;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\services\FCMService;

class GiftController extends Controller
{

    //Get all the gift
    public function getGifts(Request $req)
    {
        try {

            $gift = Gift::query();
            if ($s = $req->input(key:'s')) {
                $gift->whereRaw("name LIKE ?", ["%" . $s . "%"]);
            }
            $giftCount = $gift->count();
            $gift->orderBy('id', 'DESC');
            if ($req->startIndex >= 0 && $req->fetchRecord) {
                $gift->skip($req->startIndex);
                $gift->take($req->fetchRecord);
            }
            return response()->json([
                'recordList' => $gift->get(),
                'status' => 200,
                'totalRecords' => $giftCount,
            ], 200);
        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    //Show only active blog
    public function activeGifts()
    {
        try {
            $gift = Gift::query()->where('isActive', '=', '1');
            return response()->json([
                'recordList' => $gift->get(),
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

    public function sendGifts(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }

            $data = $req->only(
                'giftId',
                'astrologerId'
            );
            $validator = Validator::make($data, [
                'giftId' => 'required',
                'astrologerId' => 'required',

            ]);
            if ($validator->fails()) {
                DB::rollback();
                return response()->json([
                    'error' => $validator->messages(),
                    'status' => 400,
                ], 400);
            }
            DB::beginTransaction();

            $gift = DB::Table('gifts')
                ->where('id', '=', $req->giftId)
                ->get();

            AstrologerGift::create([
                'giftId' => $req->giftId,
                'astrologerId' => $req->astrologerId,
                'userId' => $id,
                'createdBy' => $id,
                'modifiedBy' => $id,
                'giftAmount' => $gift[0]->amount,
            ]);

            $userWallet = DB::table('user_wallets')
                ->where('userId', '=', $id)
                ->get();

            $astrologerUserId = DB::table('astrologers')
                ->where('id', '=', $req->astrologerId)
                ->select('userId')
                ->get();
            $astrologerWallet = DB::table('user_wallets')
                ->where('userId', '=', $astrologerUserId[0]->userId)
                ->get();

            $deduction = $userWallet[0]->amount - $gift[0]->amount;
            $userWalletData = array(
                'amount' => $deduction,
            );
            $astrologerWalletData = array(
                'amount' => $astrologerWallet && count($astrologerWallet) > 0 ? $astrologerWallet[0]->amount + $gift[0]->amount : $gift[0]->amount,
                'userId' => $astrologerUserId[0]->userId,
                'createdBy' => $astrologerUserId[0]->userId,
                'modifiedBy' => $astrologerUserId[0]->userId,
            );
            DB::Table('user_wallets')
                ->where('userId', '=', $id)
                ->update($userWalletData);

            if ($astrologerWallet && count($astrologerWallet) > 0) {
                DB::Table('user_wallets')
                    ->where('userId', '=', $astrologerUserId[0]->userId)
                    ->update($astrologerWalletData);
            } else {
                DB::Table('user_wallets')->insert($astrologerWalletData);
            }
            $walletTransaction = array(
                'amount' => $gift[0]->amount,
                'userId' => $id,
                'createdBy' => $id,
                'modifiedBy' => $id,
                'isCredit' => false,
                'transactionType' => 'Gift',
                "astrologerId" => $req->astrologerId,
                "createdBy" => $id,
                "modifiedBy" => $id,
            );
            $astrologerWalletTransaction = array(
                'amount' => $gift[0]->amount,
                'userId' => $astrologerUserId[0]->userId,
                'createdBy' => $id,
                'modifiedBy' => $id,
                'isCredit' => true,
                'transactionType' => 'Gift',
                "astrologerId" => $req->astrologerId,
                "createdBy" => $id,
                "modifiedBy" => $id,
            );
            DB::table('wallettransaction')->insert($walletTransaction);
            DB::table('wallettransaction')->insert($astrologerWalletTransaction);
            DB::commit();

            $userDeviceDetail = DB::table('user_device_details')
                ->JOIN('astrologers', 'astrologers.userId', '=', 'user_device_details.userId')
                ->WHERE('astrologers.id', '=', $req->astrologerId)
                ->SELECT('user_device_details.*')
                ->get();

            if ($userDeviceDetail && count($userDeviceDetail) > 0) {
                FCMService::send(
                    $userDeviceDetail,
                    [
                        'title' => 'Receive Gift',
                        'body' => [
                            "notificationType" => 13,
                            'description' => 'Receive Gift',
                        ],
                    ]
                );
                $notification = array(
                    'userId' => $astrologerUserId[0]->userId,
                    'title' => 'Receive Gift',
                    'description' => '',
                    'notificationId' => null,
                    'createdBy' => $astrologerUserId[0]->userId,
                    'modifiedBy' => $astrologerUserId[0]->userId,
                );
                DB::table('user_notifications')->insert($notification);
            }
            return response()->json([
                'recordList' => [],
                'status' => 200,
                'message' => 'Astrologer Gift Add Successfully',
            ], 200);
        } catch (\Exception$e) {
            DB::rollback();
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }
}
