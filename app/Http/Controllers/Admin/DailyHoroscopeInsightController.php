<?php
namespace app\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserModel\HororscopeSign;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
define('LOGINPATH', '/admin/login');
class DailyHoroScopeInsightController extends Controller
{
    public function getDailyHoroscopeInsight(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $dailyHoroscopeInsight = DB::table('dailyhoroscopeinsight')
                    ->join('hororscope_signs', 'hororscope_signs.id', '=', 'dailyhoroscopeinsight.horoscopeSignId');
                $request->filterSign = $request->filterSign ? $request->filterSign : null;
                $request->filterDate = $request->filterDate ? $request->filterDate : null;
                if ($request->filterDate) {
                    $filterDate = Carbon::parse($request->filterDate)->format('Y-m-d');
                    $dailyHoroscopeInsight = $dailyHoroscopeInsight->where(DB::raw("(DATE_FORMAT(horoscopeDate,'%Y-%m-%d'))"), $filterDate);
                } else {
                    $filterDate = Carbon::now()->format('Y-m-d');
                    $dailyHoroscopeInsight = $dailyHoroscopeInsight->where(DB::raw("(DATE_FORMAT(horoscopeDate,'%Y-%m-%d'))"), $filterDate);
                }
                if ($request->filterSign) {
                    $dailyHoroscopeInsight = $dailyHoroscopeInsight->where("horoscopeSignId", '=', $request->filterSign);
                } else {
                    $dailyHoroscopeInsight = $dailyHoroscopeInsight->where("horoscopeSignId", '=', 1);
                }
                $dailyHoroscopeInsight = $dailyHoroscopeInsight->select('dailyhoroscopeinsight.*', 'hororscope_signs.name as signName')->orderBy('dailyhoroscopeinsight.horoscopeDate', 'DESC')->get();
                $hororscopeSign = HororscopeSign::query();
                $signs = $hororscopeSign->get();
                if ($request->filterSign) {
                    $selectedId = $request->filterSign;
                } else {
                    $selectedId = $signs[0]->id;
                }
                $filterDate = $request->filterDate ? Carbon::parse($request->filterDate)->format('Y-m-d') : Carbon::Now()->format('Y-m-d');
                return view('pages.daily-horoscope-insight', compact('dailyHoroscopeInsight', 'signs', 'selectedId', 'filterDate'));
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function addDailyHoroscopeInsight(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                $insight = array(
                    'name' => $req->name,
                    'title' => $req->title,
                    'description' => $req->description,
                    'horoscopeSignId' => $req->horoscopeSignId,
                    'horoscopeDate' => $req->horoscopeDate,
                    'link' => $req->link,
                );
                DB::table('dailyhoroscopeinsight')
                    ->insert($insight);
                $id = DB::getPdo()->lastInsertId();
                if (request('coverImage')) {
                    $image = base64_encode(file_get_contents($req->file('coverImage')));
                } else {
                    $image = null;
                }
                if ($image) {
                    $time = Carbon::now()->timestamp;
                    if (Str::contains($image, 'storage')) {
                        $path = $image;
                    } else {
                        $destinationpath = 'public/storage/images/';
                        $imageName = 'dailyhoroscope_' . $id . $time;
                        $path = $destinationpath . $imageName . '.png';
                        File::delete($path);
                        file_put_contents($path, base64_decode($image));
                    }
                } else {
                    $path = null;
                }
                $insights = array(
                    'coverImage' => $path,
                );
                DB::table('dailyhoroscopeinsight')->where('id', '=', $id)->update($insights);
                return redirect()->route('dailyHoroscopeInsight');
            } else {
                return redirect(LOGINPATH);
            }

        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function editDailyHoroscopeInsight(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                $insight = DB::table('dailyhoroscopeinsight')->where('id', '=', $req->id)->get();
                if (request('coverImage')) {
                    $image = base64_encode(file_get_contents($req->file('coverImage')));
                } elseif ($insight[0]->coverImage) {
                    $image = $insight[0]->coverImage;
                } else {
                    $image = null;
                }
                if ($image) {
                    $time = Carbon::now()->timestamp;
                    if (Str::contains($image, 'storage')) {
                        $path = $image;
                    } else {
                        $destinationpath = 'public/storage/images/';
                        $imageName = 'dailyhoroscope_' . $req->id . $time;
                        $path = $destinationpath . $imageName . '.png';
                        File::delete($insight[0]->coverImage);
                        file_put_contents($path, base64_decode($image));
                    }
                } else {
                    $path = null;
                }

                $insight = array(
                    'name' => $req->name,
                    'title' => $req->title,
                    'description' => $req->editdescription,
                    'horoscopeSignId' => $req->horoscopeSignId,
                    'horoscopeDate' => $req->horoscopeDate,
                    'link' => $req->link,
                    'coverImage' => $path,
                );
                DB::table('dailyhoroscopeinsight')
                    ->where('id', '=', $req->id)
                    ->update($insight);
                return redirect()->route('dailyHoroscopeInsight');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function deleteHoroscopeInsight(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                DB::table('dailyhoroscopeinsight')->where('id', '=', $request->del_id)->delete();
                return redirect()->route('dailyHoroscopeInsight');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (\Exception$e) {
            return dd($e->getMessage());
        }
    }

}
