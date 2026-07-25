<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserModel\HororscopeSign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
define('LOGINPATH', '/admin/login');

class HoroscopeController extends Controller
{
    public function getHoroscope(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                $horoscope = DB::Table('horoscope')
                    ->join('hororscope_signs', 'hororscope_signs.id', '=', 'horoscope.horoscopeSignId');
                if ($request->filterSign) {
                    $horoscope = $horoscope->where('horoscope.horoscopeSignId', '=', $request->filterSign);
                } else {
                    $horoscope = $horoscope->where("horoscopeSignId", '=', 1);
                }
                $horoscope = $horoscope->select('horoscope.*')->get();
                $hororscopeSign = HororscopeSign::query();
                $signs = $hororscopeSign->get();
                $selectedId = $request->filterSign ? $request->filterSign : $signs[0]['id'];
                return view('pages.horoscope', compact('horoscope', 'signs', 'selectedId'));
            } else {
                return redirect(LOGINPATH);
            }

        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }
    public function addHoroscope(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                $validator = Validator::make($req->all(), [
                    'horoscopeSignId' => 'required',
                ]);
                if ($validator->fails()) {
                    return response()->json([
                        'error' => $validator->getMessageBag()->toArray(),
                    ]);
                }
                $this->addHoro('Weekly', $req->title, $req->horoscopeSignId, $req->weeklydesc, null);
                $this->addHoro('Monthly', $req->monthlytitle, $req->horoscopeSignId, $req->monthlydesc, null);
                $this->addHoro('Yearly', $req->yearlytitle, $req->horoscopeSignId, $req->yearlydesc, null);
                return response()->json([
                    'success' => "Add Horoscope Successfully",
                ]);
            } else {
                return redirect(LOGINPATH);
            }

        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function addHoro($hroscopeType, $horoTitle, $horoscopeSignId, $description, $oldSignId)
    {
        $horoscope = DB::table('horoscope')->where('horoscopeSignId', $horoscopeSignId)->where('horoscopeType', $hroscopeType)->get();
        $data = array(
            'horoscopeType' => $hroscopeType,
            'title' => $horoTitle,
            'description' => $description,
            'horoscopeSignId' => $horoscopeSignId,
        );
        if ($horoscope && count($horoscope) > 0) {
            DB::table('horoscope')->where('id', $horoscope[0]->id)->update($data);
        } else {
            if ($oldSignId) {
                DB::table('horoscope')->where('horoscopeSignId', $oldSignId)->delete();
            }
            DB::table('horoscope')
                ->insert($data);
        }
    }

    public function editHoroscope(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                $validator = Validator::make($req->all(), [
                    'horoscopeSignId' => 'required',
                ]);
                if ($validator->fails()) {
                    return response()->json([
                        'error' => $validator->getMessageBag()->toArray(),
                    ]);
                }
                $this->addHoro('Weekly', $req->title, $req->horoscopeSignId, $req->weeklydesc, $req->oldSignId);
                $this->addHoro('Monthly', $req->monthlytitle, $req->horoscopeSignId, $req->monthlydesc, $req->oldSignId);
                $this->addHoro('Yearly', $req->yearlytitle, $req->horoscopeSignId, $req->yearlydesc, $req->oldSignId);
                return response()->json([
                    'success' => "Edit Horoscope Successfully",
                ]);
            } else {
                return redirect(LOGINPATH);
            }

        } catch (Exception $e) {
            return dd($e->getMessage());
        }
    }

    public function deleteHoroscope(Request $request)
    {
        try {
            if (Auth::guard('web')->check()) {
                DB::table('horoscope')->where('horoscopeSignId', '=', $request->del_id)->delete();
                return redirect()->route('horoscope');
            } else {
                return redirect(LOGINPATH);
            }
        } catch (\Exception$e) {
            return dd($e->getMessage());
        }
    }

    public function redirectAddHoroscope()
    {
        $hororscopeSign = HororscopeSign::query();
        $signs = $hororscopeSign->where('isActive', true)->orderBy('id', 'DESC')->get();
        return view('pages.add-horoscope', compact('signs'));
    }

    public function redirectEditHoroscope(Request $req)
    {
        $horoscopeSignId = $req->horoscopeSignId;
        $horoscope = DB::table('horoscope')->where('horoscopeSignId', $req->horoscopeSignId)->get();
        if ($horoscope && count($horoscope) > 0) {
            for ($i = 0; $i < count($horoscope); $i++) {
                if ($horoscope[$i]->horoscopeType == 'Weekly') {
                    $weeklytitle = $horoscope[$i]->title;
                    $weeklydesc = $horoscope[$i]->description;
                }

                if ($horoscope[$i]->horoscopeType == 'Monthly') {
                    $monthlytitle = $horoscope[$i]->title;
                    $monthlydesc = $horoscope[$i]->description;
                }

                if ($horoscope[$i]->horoscopeType == 'Yearly') {
                    $yearlytitle = $horoscope[$i]->title;
                    $yearlydesc = $horoscope[$i]->description;
                }
            }
        }
        $horo = array(
            'weeklytitle' => $weeklytitle,
            'weeklydesc' => $weeklydesc,
            'monthlytitle' => $monthlytitle,
            'monthlydesc' => $monthlydesc,
            'yearlytitle' => $yearlytitle,
            'yearlydesc' => $yearlydesc,
        );
        $hororscopeSign = HororscopeSign::query();
        $signs = $hororscopeSign->where('isActive', true)->orderBy('id', 'DESC')->get();
        return view('pages.edit-horoscope', compact('signs', 'horo', 'horoscopeSignId'));
    }
}
