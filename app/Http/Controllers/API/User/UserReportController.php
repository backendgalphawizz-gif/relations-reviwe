<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\UserModel\UserReport;
use App\services\FCMService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserReportController extends Controller
{
    //Add a report
    public function addReport(Request $req)
    {
        try {
            //Get a user id
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }

            $data = $req->only(
                'userId',
                'firstName',
                'lastName',
                'contactNo',
                'gender',
                'birthDate',
                'birthTime',
                'birthPlace',
                'occupation',
                'maritalStatus',
                'answerLanguage',
                'comments',
                'reportFile',
            );

            //Validate the data
            $validator = Validator::make($data, [
                'userId' => 'required',
                'firstName' => 'required',
                'contactNo' => 'required',
                'gender' => 'required',
                'birthDate' => 'required',
                'birthTime' => 'required',
                'birthPlace' => 'required',
                'comments' => 'required',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }
            $reportCharge = DB::table('astrologers')->where('id', '=', $req->astrologerId)->select('reportRate')->get();
            //Create a user report
            $userReport = UserReport::create([
                'userId' => $req->userId,
                'firstName' => $req->firstName,
                'lastName' => $req->lastName,
                'contactNo' => $req->contactNo,
                'gender' => $req->gender,
                'birthDate' => $req->birthDate,
                'birthTime' => $req->birthTime,
                'birthPlace' => $req->birthPlace,
                'occupation' => $req->occupation,
                'maritalStatus' => $req->maritalStatus,
                'answerLanguage' => $req->answerLanguage,
                'partnerName' => $req->partnerName,
                'partnerBirthDate' => $req->partnerBirthDate,
                'partnerBirthTime' => $req->partnerBirthTime,
                'partnerBirthPlace' => $req->partnerBirthPlace,
                'comments' => $req->comments,
                'createdBy' => $id,
                'modifiedBy' => $id,
                'reportType' => $req->reportType,
                'astrologerId' => $req->astrologerId,
                'countryCode' => $req->countryCode,
                'reportRate' => $reportCharge[0]->reportRate,
            ]);

            if ($reportCharge && $reportCharge[0]->reportRate > 0) {
                $commission = DB::table('commissions')
                    ->where('commissionTypeId', '=', '3')
                    ->where('astrologerId', '=', $req->astrologerId)
                    ->get();
                if ($commission && count($commission) > 0) {
                    $adminCommission = ($commission[0]->commission * $reportCharge[0]->reportRate) / 100;
                } else {
                    $syscommission = DB::table('systemflag')->where('name', 'ReportCommission')->select('value')->get();
                    $adminCommission = ($syscommission[0]->value * $reportCharge[0]->reportRate) / 100;
                }
                $astrologerCommission = $reportCharge[0]->reportRate - $adminCommission;

                $userwallet = Db::Table('user_wallets')->where('userId', '=', $req->userId)->select('amount')->get();
                $data = array(
                    'amount' => $userwallet[0]->amount - $reportCharge[0]->reportRate,
                );
                DB::table('user_wallets')->where('userId', '=', $req->userId)->update($data);
                $transaction = array(
                    'userId' => $req->userId,
                    'amount' => $reportCharge[0]->reportRate,
                    'isCredit' => false,
                    "transactionType" => 'Report',
                    "orderId" => null,
                    "astrologerId" => $req->astrologerId,
                );
                DB::table('wallettransaction')->insert($transaction);
                $astrologerUser = DB::Table('astrologers')->where('id', $req->astrologerId)->select('userId')->get();
                $astrologerWallet = DB::Table('user_wallets')->where('userId', '=', $astrologerUser[0]->userId)->select('amount')->get();
                $astrologerWall = array(
                    'userId' => $astrologerUser[0]->userId,
                    'amount' => $astrologerWallet && count($astrologerWallet) > 0 ? $astrologerWallet[0]->amount + $astrologerCommission : $astrologerCommission,
                    'createdBy' => $id,
                    'modifiedBy' => $id,
                );
                if ($astrologerWallet && count($astrologerWallet) > 0) {
                    DB::table('user_wallets')
                        ->where('id', $astrologerUser[0]->userId)
                        ->update($astrologerWall);
                } else {
                    DB::Table('user_wallets')->insert($astrologerWall);
                }
                $astrologerTransaction = array(
                    'userId' => $astrologerUser[0]->userId,
                    'amount' => $astrologerCommission,
                    'isCredit' => true,
                    "transactionType" => 'Report',
                    "orderId" => null,
                    "astrologerId" => $req->astrologerId,
                );
                DB::table('wallettransaction')->insert($astrologerTransaction);
                $orderRequest = array(
                    'userId' => $req->userId,
                    'astrologerId' => $req->astrologerId,
                    'orderType' => 'report',
                    'totalPayable' => $reportCharge[0]->reportRate,
                    'orderStatus' => 'Complete',
                    'totalMin' => null,
                    'callId' => null,

                );
                DB::Table('order_request')->insert($orderRequest);
                $id = DB::getPdo()->lastInsertId();
                if ($adminCommission && $adminCommission > 0) {
                    $adminGetCommission = array(
                        'commissionTypeId' => 3,
                        "amount" => $adminCommission,
                        "commissionId" => $commission && count($commission) > 0 ? $commission[0]->id : null,
                        "orderId" => $id,
                        "createdBy" => $astrologerUser[0]->userId,
                        "modifiedBy" => $astrologerUser[0]->userId,
                    );
                    DB::table('admin_get_commissions')->insert($adminGetCommission);
                }
                $userDeviceDetail = DB::table('user_device_details')
                    ->JOIN('astrologers', 'astrologers.userId', '=', 'user_device_details.userId')
                    ->WHERE('astrologers.id', '=', $req->astrologerId)
                    ->SELECT('user_device_details.*')
                    ->get();

                if ($userDeviceDetail && count($userDeviceDetail) > 0) {
                    FCMService::send(
                        $userDeviceDetail,
                        [
                            'title' => 'Get Report Request',
                            'body' => [
                                "notificationType" => 9,
                                'description' => '',
                            ],
                        ]
                    );
                }
            }
            return response()->json([
                'message' => 'User report add sucessfully',
                'recordList' => $userReport,
                'status' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    //Get all report data
    public function getUserReportRequest(Request $req)
    {
        try {
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }
            $data = $req->only(
                'astrologerId'
            );
            $validator = Validator::make($data, [
                'astrologerId' => 'required',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }
            $userReport = DB::table('user_reports')
                ->join('users', 'users.id', '=', 'user_reports.userId')
                ->join('report_types', 'report_types.id', '=', 'user_reports.reportType')
                ->where('astrologerId', '=', $req->astrologerId)
                ->whereNull('reportFile')
                ->select('user_reports.*', 'users.name as userName', 'users.profile', 'users.contactNo', 'report_types.reportImage', 'report_types.title as reportType')
                ->orderBy('user_reports.id', 'DESC')
                ->get();

            return response()->json([
                'recordList' => $userReport,
                'status' => 200,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    //Update user report
    public function updateUserReport(Request $req, $id)
    {
        try {
            //Get a user id
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            }

            $req->validate = ([
                'userId',
                'firstName',
                'lastName',
                'contactNo',
                'gender',
                'birthDate',
                'birthTime',
                'birthPlace',
                'occupation',
                'maritalStatus',
                'answerLanguage',
                'partnerName',
                'partnerBirthDate',
                'partnerBirthTime',
                'partnerBirthPlace',
                'comments',
                'reportFile',
            ]);

            $userReport = UserReport::find($id);
            if ($userReport) {
                $userReport->userId = $req->userId;
                $userReport->firstName = $req->firstName;
                $userReport->lastName = $req->lastName;
                $userReport->contactNo = $req->contactNo;
                $userReport->gender = $req->gender;
                $userReport->birthDate = $req->birthDate;
                $userReport->birthTime = $req->birthTime;
                $userReport->birthPlace = $req->birthPlace;
                $userReport->occupation = $req->occupation;
                $userReport->maritalStatus = $req->maritalStatus;
                $userReport->answerLanguage = $req->answerLanguage;
                $userReport->partnerName = $req->partnerName;
                $userReport->partnerBirthDate = $req->partnerBirthDate;
                $userReport->partnerBirthTime = $req->partnerBirthTime;
                $userReport->partnerBirthPlace = $req->partnerBirthPlace;
                $userReport->comments = $req->comments;
                $userReport->update();
                return response()->json([
                    'message' => 'User report update sucessfully',
                    'recordList' => $userReport,
                    'status' => 200,
                ], 200);
            } else {
                return response()->json([
                    'message' => 'User report is not found',
                    'status' => 404,
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function addUserReportFile(Request $req)
    {
        try {
            $data = $req->only(
                'id',
                'reportFile'
            );
            //Validate the data
            $validator = Validator::make($data, [
                'id' => 'required',
                'reportFile' => 'required',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->messages(),
                    'status' => 400,
                ], 400);
            }
            $reportRequest = UserReport::find($req->id);
            if ($reportRequest) {
                if ($req->reportFile) {
                    if (Str::contains($req->reportFile, 'storage')) {
                        $path = $req->reportFile;
                    } else {
                        $destinationpath = 'public/storage/reports/';
                        $time = Carbon::now()->timestamp;
                        $imageName = 'report_' . $req->id;
                        $path = $destinationpath . $imageName . $time . '.pdf';
                        File::delete($path);
                        file_put_contents($path, base64_decode($req->reportFile));
                    }
                } else {
                    $path = null;
                }
                $reportRequest->reportFile = $path;
                $reportRequest->updated_at = Carbon::now()->timestamp;
                $reportRequest->update();
                return response()->json([
                    'message' => 'User report update sucessfully',
                    'recordList' => $reportRequest,
                    'status' => 200,
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }
}
