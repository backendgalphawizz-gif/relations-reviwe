<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\UserModel\KundaliMatching;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class KundaliMatchingController extends Controller
{
    //Add a kundali boy and girls
    public function addKundaliMatching(Request $req)
    {
        try {
            //Get a id of user
            if (!Auth::guard('api')->user()) {
                return response()->json(['error' => 'Unauthorized', 'status' => 401], 401);
            } else {
                $id = Auth::guard('api')->user()->id;
            }

            $data = $req->only(
                'boyName',
                'boyBirthDate',
                'boyBirthTime',
                'boyBirthPlace',
                'girlName',
                'girlBirthDate',
                'girlBirthTime',
                'girlBirthPlace',
            );

            //Validate the data
            $validator = Validator::make($data, [
                'boyName' => 'required',
                'boyBirthDate' => 'required',
                'boyBirthTime' => 'required',
                'boyBirthPlace' => 'required',
                'girlName' => 'required',
                'girlBirthDate' => 'required',
                'girlBirthTime' => 'required',
                'girlBirthPlace' => 'required',
            ]);

            //Send failed response if request is not valid
            if ($validator->fails()) {
                return response()->json(['error' => $validator->messages(), 'status' => 400], 400);
            }

            //Create kundali
            $kundaliMatching = KundaliMatching::create([
                'boyName' => $req->boyName,
                'boyBirthDate' => $req->boyBirthDate,
                'boyBirthTime' => $req->boyBirthTime,
                'boyBirthPlace' => $req->boyBirthPlace,
                'girlName' => $req->girlName,
                'girlBirthDate' => $req->girlBirthDate,
                'girlBirthTime' => $req->girlBirthTime,
                'girlBirthPlace' => $req->girlBirthPlace,
                'createdBy' => $id,
                'modifiedBy' => $id,
            ]);

            return response()->json([
                'message' => 'Boys and girls details add sucessfully',
                'recordList' => $kundaliMatching,
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
