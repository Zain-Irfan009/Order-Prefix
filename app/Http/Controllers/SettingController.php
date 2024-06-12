<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class SettingController extends Controller
{
    public function Settings(){

        $setting=Setting::first();
        $companies=Company::all();
        return view('settings.index',compact('setting','companies'));
    }

    public function SettingsSave(Request $request){

        $settings=Setting::first();
        if($settings==null){
            $settings=new Setting();
        }
        $ids=null;
        if(isset($request->company_ids_excluded)){
            $ids=implode(',',$request->company_ids_excluded);
        }
        $settings->update_prefix=isset($request->update_prefix)?$request->update_prefix:0;
        $settings->company_ids_excluded=$ids;
        $settings->save();
        return Redirect::tokenRedirect('settings', ['notice' => 'Settings Save Successfully']);
    }
}
