<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminModel\Language;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SystemFlagController extends Controller
{
    public function getSystemFlag(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                $flagGroup = DB::table('flaggroup')->where('isActive', 1)->whereNull('parentFlagGroupId')->get();
                for ($i = 0; $i < count($flagGroup); $i++) {
                    $subGroup = DB::Table('flaggroup')->where('isActive', 1)->where('parentFlagGroupId', $flagGroup[$i]->id)->get();
                    if ($subGroup && count($subGroup) > 0) {
                        for ($j = 0; $j < count($subGroup); $j++) {
                            $systemFlag = $systemFlag = DB::table('systemflag')->where('isActive', 1)->where('flagGroupId', $subGroup[$j]->id)->get();
                            $subGroup[$j]->systemFlag = $systemFlag;

                        }
                        $flagGroup[$i]->subGroup = $subGroup;
                        $systemFlag = DB::table('systemflag')->where('isActive', 1)->where('flagGroupId', $flagGroup[$i]->id)->get();
                        $flagGroup[$i]->systemFlag = $systemFlag;
                    } else {
                        $systemFlag = DB::table('systemflag')->where('isActive', 1)->where('flagGroupId', $flagGroup[$i]->id)->get();
                        $flagGroup[$i]->systemFlag = $systemFlag;
                        $flagGroup[$i]->subGroup = [];
                    }
                }
                // dd($flagGroup);
                $language = Language::query()->get();
                return view('pages.system-flag', compact('flagGroup', 'language'));
            } else {
                return redirect('/admin/login');
            }
        } catch (\Exception$e) {
            return dd($e->getMessage());
        }
    }

    public function editSystemFlag(Request $req)
    {
        try {
            if (Auth::guard('web')->check()) {
                foreach ($req->group as $flag) {
                    if (array_key_exists('systemFlag', $flag) && count($flag['systemFlag']) > 0) {
                        foreach ($flag['systemFlag'] as $flagvalue) {
                            if (array_key_exists('value', $flagvalue)) {
                                if (array_key_exists('valueType', $flagvalue)) {
                                    if ($flagvalue['valueType'] == 'File') {
                                        $sysFile = DB::Table('systemflag')->where('name', $flagvalue['name'])->first();
                                        $time = Carbon::now()->timestamp;
                                        if($flagvalue['value']) {
                                            $flagvalue['value'] = base64_encode(file_get_contents($flagvalue['value']));
                                            $destinationpath = 'storage/images/';
                                            $imageName = $flagvalue['name'];
                                            $path = $destinationpath . $imageName . $time . '.png';
                                            File::delete($sysFile->value);
                                            file_put_contents(public_path($path), base64_decode($flagvalue['value']));
                                            $flagvalue['value'] = $path;
                                        }
                                    }
                                    if ($flagvalue['valueType'] == 'MultiSelect') {
                                        $flagvalue['value'] = implode(',', $flagvalue['value']);
                                    }
                                    if ($flagvalue['valueType'] == 'Checkbox') {
                                        $flagvalue['value'] = 1;
                                    }
                                    if ($flagvalue['valueType'] == 'Video') {
                                        $time = Carbon::now()->timestamp;
                                        $sysFile = DB::Table('systemflag')->where('name', $flagvalue['name'])->first();
                                        $flagvalue['value'] = base64_encode(file_get_contents($flagvalue['value']));
                                        $destinationpath = 'public/storage/images/';
                                        $imageName = $flagvalue['name'];
                                        $path = $destinationpath . $imageName . $time . '.mp4';
                                        File::delete($sysFile->value);
                                        file_put_contents($path, base64_decode($flagvalue['value']));
                                        $flagvalue['value'] = $path;
                                    }
                                }
                                $flagData = array(
                                    'value' => $flagvalue['value'],
                                );
                                DB::Table('systemflag')
                                    ->where('name', '=', $flagvalue['name'])
                                    ->update($flagData);
                            } else {
                                if (array_key_exists('valueType', $flagvalue)) {
                                    if ($flagvalue['valueType'] == 'Checkbox') {
                                        $flagvalue['value'] = 0;
                                        $flagData = array(
                                            'value' => $flagvalue['value'],
                                        );
                                        DB::Table('systemflag')
                                            ->where('name', '=', $flagvalue['name'])
                                            ->update($flagData);
                                    }
                                }
                            }
                        }
                    }
                    if (array_key_exists('subGroup', $flag) && count($flag['subGroup']) > 0) {
                        foreach ($flag['subGroup'] as $flagvalue) {
                            foreach ($flagvalue['systemFlag'] as $sys) {
                                if (array_key_exists('value', $sys)) {
                                    if (array_key_exists('valueType', $sys)) {
                                        if ($sys['valueType'] == 'File') {
                                            $sysFile = DB::Table('systemflag')->where('name', $sys['name'])->first();
                                            $time = Carbon::now()->timestamp;
                                            $sys['value'] = base64_encode(file_get_contents($sys['value']));
                                            $destinationpath = 'public/storage/images/';
                                            $imageName = $sys['name'];
                                            $path = $destinationpath . $imageName . $time . '.png';
                                            File::delete($sysFile->value);
                                            file_put_contents($path, base64_decode($sys['value']));
                                            $sys['value'] = $path;
                                        }
                                        if ($sys['valueType'] == 'MultiSelect') {
                                            $sys['value'] = implode(',', $sys['value']);
                                        }
                                        
                                        if ($sys['valueType'] == 'Video') {
                                            $sysFile = DB::Table('systemflag')->where('name', $sys['name'])->first();
                                            $time = Carbon::now()->timestamp;
                                            $sys['value'] = base64_encode(file_get_contents($sys['value']));
                                            $destinationpath = 'public/storage/images/';
                                            $imageName = $sys['name'];
                                            $path = $destinationpath . $imageName . $time . '.mp4';
                                            File::delete($sysFile->value);
                                            file_put_contents($path, base64_decode($sys['value']));
                                            $sys['value'] = $path;
                                        }
                                    }
                                    if($sys['name'] == 'CallCharges') {
                                        DB::Table('astrologers')
                                        ->update(['charge' => $sys['value']]);
                                    }
                                    $flagData = array(
                                        'value' => $sys['value'],
                                    );
                                    DB::Table('systemflag')
                                        ->where('name', '=', $sys['name'])
                                        ->update($flagData);
                                }
                            }
                        }
                    }
                }
                return response()->json([
                    'success' => "SystemFlag Update",
                ]);
            } else {
                return redirect('/admin/login');
            }
        } catch (\Exception$e) {
            return dd($e->getMessage());
        }
    }

    public function updateSystemFlagPublished(Request $req) {

        DB::Table('systemflag')->where('id', '=', $req['id'])->update([$req['field'] => $req['is_published']]);
        return response()->json([
            'success' => "Status updated success",
        ]);
    }

}
