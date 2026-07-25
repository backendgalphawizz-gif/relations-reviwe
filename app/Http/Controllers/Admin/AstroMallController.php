<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserModel\AstromallProduct;
use App\Models\UserModel\ProductCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

define('DESTINATIONPATH', 'public/storage/images/');
define('LOGINPATH', '/admin/login');

class AstroMallController extends Controller
{
    public $path;
    public $limit = 8;
    public $paginationStart;
    public function addAstroMall()
    {
        return view('pages.astroMall');
    }

    public function addAstroMallApi(Request $req)
    {
        try {
            $validator = Validator::make($req->all(), [
                'name' => 'required|unique:product_categories',
                'categoryImage' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->getMessageBag()->toArray(),
                ]);
            }
            if (Auth::guard('web')->check()) {
                if (request('categoryImage')) {
                    $image = base64_encode(file_get_contents($req->file('categoryImage')));
                } else {
                    $image = null;
                }
                $productCategory = ProductCategory::create([
                    'name' => $req->name,
                    'displayOrder' => null,
                    'categoryImage' => '',
                    'createdBy' => Auth()->user()->id,
                    'modifiedBy' => Auth()->user()->id,
                ]);
                if ($image) {
                    if (Str::contains($image, 'storage')) {
                        $path = $image;
                    } else {
                        $time = Carbon::now()->timestamp;
                        $imageName = 'productCategory_' . $productCategory->id;
                        $path = DESTINATIONPATH . $imageName . $time . '.png';
                        File::delete($path);
                        file_put_contents($path, base64_decode($image));
                    }
                } else {
                    $path = null;
                }
                $productCategory->categoryImage = $path;
                $productCategory->update();
                return redirect()->route('productCategories');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    //Get AstroMall
    public function getastroMall(Request $request)
    {

        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $productCategory = ProductCategory::query();
                $searchString = $request->searchString ? $request->searchString : null;
                if ($searchString) {
                    $productCategory->whereRaw("name LIKE ?", ["%" . $request->searchString . "%"]);
                }
                $productCategoryCount = $productCategory->count();
                $productCategory = $productCategory->skip($paginationStart);
                $productCategory = $productCategory->take($this->limit);
                $totalPages = ceil($productCategoryCount / $this->limit);
                $totalRecords = $productCategoryCount;
                $productCategory->orderBy('id', 'DESC');
                $astroMall = $productCategory->get();
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.astroMall', compact('astroMall', 'searchString', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    //Get AstroMall Category

    public function getastroMallCategory(Request $request)
    {

        try {
            if (Auth::guard('web')->check()) {
                $productCategory = ProductCategory::query()->get();
                return view('pages.add-product')->with(['result' => $productCategory]);
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function getCategoryById(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
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
                $astroMallDetail = $productDetail;
                return view('pages.product-detail', compact('astroMallDetail'));
            } else {
                return redirect(LOGINPATH);
            }
           
        } catch (\Exception$e) {
            return dd($e->getMessage());
        }
    }

    //Delete AstroMall

    //Update AstroMall

    public function editAstroMall()
    {
        return view('pages.astroMall');
    }

    public function editAstroMallApi(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $astromallProduct = DB::table('product_categories')
                    ->where('id', '=', $request->filed_id)
                    ->get();
                if (request('categoryImage')) {
                    $image = base64_encode(file_get_contents($request->file('categoryImage')));
                } elseif ($astromallProduct && $astromallProduct[0]->categoryImage) {
                    $image = $astromallProduct[0]->categoryImage;
                } else {
                    $image = '';
                }
                $productCategory = ProductCategory::find($request->filed_id);
                if ($productCategory) {
                    if ($image) {
                        if (Str::contains($image, 'storage')) {
                            $path = $image;
                        } else {
                            $time = Carbon::now()->timestamp;
                            $imageName = 'productCategory_' . $request->filed_id;
                            $path = DESTINATIONPATH . $imageName . $time . '.png';
                            File::delete($astromallProduct[0]->categoryImage);
                            file_put_contents($path, base64_decode($image));
                        }
                    } else {
                        $path = null;
                    }

                    $productCategory->name = $request->name;
                    $productCategory->displayOrder = null;
                    $productCategory->categoryImage = $path;
                    $productCategory->update();

                }
                return redirect()->route('productCategories');
            } else {
                return redirect(LOGINPATH);
            }

        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }
    //Get Product

    public function getProduct(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $page = $request->page ? $request->page : 1;
                $paginationStart = ($page - 1) * $this->limit;
                $astromallProduct = AstromallProduct::query();
                $astromallProduct = $astromallProduct->join('product_categories','product_categories.id','=','astromall_products.productCategoryId')->select('astromall_products.*','product_categories.name as productCategory');
                $astromallProduct = $astromallProduct->orderBy('id', 'DESC');
                $searchString = $request->searchString ? $request->searchString : null;
                if ($searchString) {
                    $astromallProduct->whereRaw("astromall_products.name LIKE ?", ["%" . $request->searchString . "%"]);
                }
                $astromallProductCount = $astromallProduct->count();
                $astromallProduct->skip($paginationStart);
                $astromallProduct->take($this->limit);
                $astromallProduct = $astromallProduct->get();
                $totalPages = ceil($astromallProductCount / $this->limit);
                $totalRecords = $astromallProductCount;
                $start = ($this->limit * ($page - 1)) + 1;
                $end = ($this->limit * ($page - 1)) + $this->limit < $totalRecords ? ($this->limit * ($page - 1)) + $this->limit : $totalRecords;
                return view('pages.product', compact('astromallProduct', 'searchString', 'totalPages', 'totalRecords', 'start', 'end', 'page'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    //Add Product

    public function addProduct()
    {
        return view('pages.add-product');
    }

    public function addProductApi(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                if (request('productImage')) {
                    $image = base64_encode(file_get_contents($req->file('productImage')));
                } else {
                    $image = null;
                }
                $astromallProduct = AstromallProduct::create([
                    'name' => $req->name,
                    'features' => $req->features,
                    'productImage' => '',
                    'productCategoryId' => $req->productCategoryId,
                    'amount' => $req->amount,
                    'description' => $req->description,
                    'createdBy' => Auth()->user()->id,
                    'modifiedBy' => Auth()->user()->id,
                ]);
                if ($image) {
                    if (Str::contains($image, 'storage')) {
                        $path = $image;
                    } else {
                        $time = Carbon::now()->timestamp;
                        $imageName = 'astromallProduct_' . $astromallProduct->id;
                        $path = DESTINATIONPATH . $imageName . $time . '.png';
                        File::delete($path);
                        file_put_contents($path, base64_decode($image));
                    }
                } else {
                    $path = null;
                }
                $astromallProduct->productImage = $path;
                $astromallProduct->update();
                return redirect()->route('products');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function astroMallStatus(Request $request)
    {
        return view('pages.astroMall');
    }

    public function astroMallStatusApi(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $productCategory = ProductCategory::find($request->status_id);
                if ($productCategory) {
                    $productCategory->isActive = !$productCategory->isActive;
                    $productCategory->update();
                }
                return redirect()->back();
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function productStatus(Request $request)
    {
        return view('pages.product');
    }

    public function productStatusApi(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $astromallProduct = AstromallProduct::find($request->status_id);
                if ($astromallProduct) {
                    $astromallProduct->isActive = !$astromallProduct->isActive;
                    $astromallProduct->update();
                }
                return redirect()->route('products');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function editProduct(Request $req)
    {
        $product = AstromallProduct::find($req->id);
        $productCategory = ProductCategory::query()->where('isActive', '=', true)->get();
        return view('pages.edit-product')->with(['product' => $product, 'result' => $productCategory]);
    }

    public function editProductApi(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $product = AstromallProduct::find($request->field_id);
                if (request('productImage')) {
                    $image = base64_encode(file_get_contents($request->file('productImage')));
                } elseif ($product->productImage) {
                    $image = $product->productImage;
                } else {
                    $image = null;
                }
                if ($product) {
                    if ($image) {
                        if (Str::contains($image, 'storage')) {
                            $path = $image;
                        } else {
                            $time = Carbon::now()->timestamp;
                            $imageName = 'product_' . $request->field_id;
                            $path = DESTINATIONPATH . $imageName . $time . '.png';
                            File::delete($product->productImage);
                            file_put_contents($path, base64_decode($image));
                        }
                    } else {
                        $path = null;
                    }

                    $product->name = $request->name;
                    $product->features = $request->features;
                    $product->productImage = $path;
                    $product->productCategoryId = $request->productCategoryId;
                    $product->amount = $request->amount;
                    $product->description = $request->description;
                    $product->update();

                }
                return redirect()->route('products');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function addProductDetailApi(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                $productDetail = array(
                    'astromallProductId' => $req->astromallProductId,
                    'question' => $req->question,
                    'answer' => $req->answer,
                );
                DB::Table('product_details')->insert($productDetail);
                return redirect()->back();
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

}
