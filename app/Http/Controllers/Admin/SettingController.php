<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use App\Settings;
use UI\Controls\Form;
/*
 * SettingController is used to the Show Edit and update the Setting
 */

class SettingController extends Controller {
    /*
     * All the Settings data fetch and show to the Sem view pages.
     */

    public function index() {
        $settings = Settings::all();
        return view('admin.setting.index', ['settings' => $settings]);
    }

    /*
     * This Funciton is getting Settings  data and view in Edit Settings Pages
     */

    public function editsetting($id) {
        $settings = Settings::where('id', $id)->first();

        $settings_array = array(
            'id' => $settings->id,
            'setting_name' => $settings->setting_name,
            'setting_value' => $settings->setting_value,
        );

        return view('admin.setting.edit', ['settings_array' => $settings_array]);
    }

    /*
     * All the records update into the database to use this function
     */

    public function update(Request $request) {
        $rules = array(
            'setting_value' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return redirect()->route('admin.setting')
                            ->withErrors($validator);
        } else {
            
            $settingupdate = Settings::where('id', Input::get('id'))->update(['setting_value' => Input::get('setting_value')]);
            if ($settingupdate) {
                return redirect()->route('admin.setting')
                                ->with('success', 'Record Updated Successfully!');
            } else {
                return redirect()->back()->with("error", "Not Change Any Values!");
            }
        }
    }

}
