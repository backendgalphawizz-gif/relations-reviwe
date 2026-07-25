<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\UserModel\AstromallProduct;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AstromallProductController extends Controller
{
    
    //Get all astromall product
    public function getAstromallProduct(Request $req)
    {
        try {
            $astromallProduct = AstromallProduct::query();
            if ($req->productCategoryId) {
                $astromallProduct->where('productCategoryId', '=', $req->productCategoryId);
            }

            $astromallProduct = $astromallProduct->orderBy('id', 'DESC');
            if ($req->startIndex >= 0 && $req->fetchRecord) {
                $astromallProduct->skip($req->startIndex);
                $astromallProduct->take($req->fetchRecord);
            }

            $astromallProductCount = DB::table('astromall_products')->count();

            return response()->json([
                'recordList' => $astromallProduct->get(),
                'status' => 200,
                'totalRecords' => $astromallProductCount,
            ], 200);

        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function getAstromallProductForApp(Request $req)
    {
        try {
            $astromallProduct = AstromallProduct::query();
            if ($req->productCategoryId) {
                $astromallProduct->where('productCategoryId', '=', $req->productCategoryId);
            }
            $astromallProduct->where('isActive', '=', true);
            $astromallProduct->where('isDelete', '=', false);
            if ($s = $req->input(key:'s')) {
                $astromallProduct->whereRaw("name LIKE ?", ["%" . $s . "%"]);
            }

            if ($req->startIndex >= 0 && $req->fetchRecord) {
                $astromallProduct->skip($req->startIndex);
                $astromallProduct->take($req->fetchRecord);
            }

            $astromallProductCount = AstromallProduct::query();
            if ($req->productCategoryId) {
                $astromallProductCount->where('productCategoryId', '=', $req->productCategoryId);
            }
            $astromallProductCount->where('isActive', '=', true);
            $astromallProductCount->where('isDelete', '=', false);
            $astromallProductCount->count();

            return response()->json([
                'recordList' => $astromallProduct->get(),
                'status' => 200,
                'totalRecords' => $astromallProductCount,
            ], 200);

        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }

    public function getAstromallProductById(Request $req)
    {
        try {
            $productDetail = DB::table('astromall_products')
                ->join('product_categories', 'product_categories.id', '=', 'astromall_products.productCategoryId')
                ->where('astromall_products.id', '=', $req->id)
                ->select('astromall_products.*', 'product_categories.name as productCategory')
                ->get();
            $questionAnswer = DB::Table('product_details')
                ->where('astromallProductId', '=', $req->id)
                ->where('isActive', '=', 1)
                ->select('question', 'answer', 'id')
                ->get();
            $productDetail[0]->questionAnswer = $questionAnswer;

            $productReview = DB::table('user_reviews')
                ->join('users', 'users.id', '=', 'user_reviews.userId')
                ->where('astromallProductId', '=', $req->id)
                ->select('user_reviews.*', 'users.name as userName', 'users.profile')
                ->get();
            $productDetail[0]->productReview = $productReview;
            return response()->json([
                'recordList' => $productDetail,
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

    public function searchInProductCategory(Request $req)
    {
        try {

            $product = DB::table('astromall_products');
            if ($req->productCategoryId) {
                $product = $product->where('productCategoryId', '=', $req->productCategoryId);
                if ($req->searchString) {
                    $product = $product->whereRaw("name LIKE ?", ["%" . $req->searchString . "%"]);
                }
            }
            $product = $product->get();
            return response()->json([
                'recordList' => $product,
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
