<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Models\AdminModel\Banner;
use App\Models\AstrologerModel\AstrologyVideo;
use App\Models\AstrologerModel\Blog;
use App\Models\UserModel\AstrotalkInNews;
use App\Models\UserModel\ProductCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerHomeController extends Controller
{
    public function getCustomerHome()
    {
        try {

            $banner = Banner::query()->join('banner_types','banner_types.id','banners.bannerTypeId')->where('banners.isActive', '=', '1')->whereDate('fromDate', '<=', Carbon::today())->whereDate('ToDate', '>=', Carbon::today())
                ->limit(10)->select('banners.*','banner_types.name as bannerType')->orderBy('banners.id', 'DESC')->get();
            $blog = Blog::query()->where('isActive', '=', '1')->orderBy('id', 'DESC')->limit(10)->get();
            $productCategory = ProductCategory::query()->where('isActive', '=', '1')->orderBy('id', 'DESC')->limit(10)->get();
            $astrotalkInNews = AstrotalkInNews::query()->where('isActive', '=', '1')->orderBy('id', 'DESC')->limit(10)->get();
            $astrologyVideo = AstrologyVideo::query()->where('isActive', '=', '1')->orderBy('id', 'DESC')->limit(10)->get();
            if (!Auth::guard('api')->user()) {
                return response()->json([
                    'banner' => $banner,
                    'blog' => $blog,
                    'productCategory' => $productCategory,
                    'astrotalkInNews' => $astrotalkInNews,
                    'astrologyVideo' => $astrologyVideo,
                    'status' => 200,
                ], 200);
            } else {
                $status = array(
                    'chat',
                    'call',
                );
                $id = Auth::guard('api')->user()->id;
                $topOrders = DB::table('order_request')
                    ->join('astrologers', 'astrologers.id', '=', 'order_request.astrologerId')
                    ->where('order_request.userId', '=', $id)
                    ->whereIn('order_request.orderType', $status)
                    ->select('order_request.*', 'astrologers.name as astrologerName', 'astrologers.profileImage')
                    ->orderBy('order_request.id', 'DESC')->limit(3)->get();
                if ($topOrders && count($topOrders) > 0) {
                    foreach ($topOrders as $top) {
                        if ($top->chatId != null) {
                            $chatId = DB::Table('chatrequest')->where('id', '=', $top->chatId)->select('chatId')->get();
                            $top->firebaseChatId = $chatId[0]->chatId;
                        } elseif ($top->callId != null) {
                            $callChatId = DB::Table('callrequest')->where('id', '=', $top->callId)->select('chatId')->get();
                            if ($callChatId && count($callChatId) > 0) {
                                $top->firebaseChatId = $callChatId[0]->chatId;
                            }
                        }
                    }
                }
                return response()->json([
                    'banner' => $banner,
                    'blog' => $blog,
                    'productCategory' => $productCategory,
                    'astrotalkInNews' => $astrotalkInNews,
                    'astrologyVideo' => $astrologyVideo,
                    'topOrders' => $topOrders,
                    'status' => 200,
                ], 200);
            }

        } catch (\Exception$e) {
            return response()->json([
                'error' => false,
                'message' => $e->getMessage(),
                'status' => 500,
            ], 500);
        }
    }
}
