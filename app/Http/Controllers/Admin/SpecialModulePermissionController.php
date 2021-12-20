<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\Special_module_permission;

class SpecialModulePermissionController extends Controller {


    public $data;
    protected $loggedin_user;

    public function __construct() {

        $this->data['module_title'] = 'Special Module Permission';
    }

    public function add_special_permission() {

        $this->data['page_title'] = "Special Module Permission";
            $all_modules =  ['bank_users'=> 9,
                            'bank_category_users'=> 64,
                            'bank_sub_category_users'=> 65,
                            'client_users'=> 63,
                            'company_users'=> 17,
                            'company_document_users'=> 60,
                            'project_users'=> 36,
                            'project_site_users'=>53,
                            'payment_card_users'=> 47,
                            'registry_category_users'=>39,
                            'registry_sub_category_users'=>68,
                            'delivery_mode_users'=> 69,
                            'sender_category_users'=> 70,
                            'tender_category_users'=>57,
                            'tender_pattern_users'=> 66,
                            'tender_submission_users' => 67,
                            'vendor_users'=> 35,
                            'vendor_bank_users'=> 45,
                            'tds_section_type_users' => 77
                        ];
        $this->data['users'] = User::where('status', 'Enabled')->orderBy('name')->pluck('name', 'id')->toArray();
        foreach ($all_modules as $key => $value) {

            $this->data[$key] = Special_module_permission::where('module_id',$value)->pluck('user_id')->toArray();
        }
        return view('admin.special_module_permission.index', $this->data);
    }


    public function save_special_permission(Request $request) {

        $validator_normal = Validator::make($request->all(), [
            'company.*' => 'required',
            'vendor.*' => 'required',
            'project.*' => 'required',
            'client.*' => 'required',
            'vendor_bank.*' => 'required',
            'project_site.*' => 'required',
            'bank.*' => 'required',
            'bank_category.*' => 'required',
            'bank_sub_category.*' => 'required',
            'payment_card.*' => 'required',
            'company_document.*' => 'required',
            'tender_category.*' => 'required',
            'tender_pattern.*' => 'required',
            'tender_submission.*' => 'required',
            'registry_category.*' => 'required',
            'registry_sub_category.*' => 'required',
            'delivery_mode.*' => 'required',
            'sender_category.*' => 'required',
            'tds_section_type.*' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_special_permission')->with('error', 'Please follow validation rules.');
        }

        $check_entry = Special_module_permission::get()->toArray();
        $all_modules =  ['bank'=> 9, 'bank_category'=> 64, 'bank_sub_category'=> 65,'client'=> 63,'company'=> 17,'company_document'=> 60,
                         'project'=> 36,'project_site'=>53,'payment_card'=> 47,'registry_category'=>39,'registry_sub_category'=>68,
                         'delivery_mode'=> 69,'sender_category'=> 70,'tender_category'=>57,'tender_pattern'=> 66, 'tender_submission' => 67,'vendor'=> 35,
                         'vendor_bank'=> 45, 'tds_section_type'=>77
                        ];

        foreach ($all_modules as $moduleName => $moduleId) {

            $module = $request->$moduleName;
            for ($count = 0; $count < count($module); $count++) {
                $users_arr = [
                    'user_id' => $module[$count],
                    'module_id' => $all_modules[$moduleName],
                    'created_at' => date('Y-m-d h:i:s'),
                    'created_ip' => $request->ip(),
                    'updated_at' => date('Y-m-d h:i:s'),
                    'updated_ip' => $request->ip()
                ];
                $insert_data[] = $users_arr;
            }
        }

        if (!empty($check_entry)) {
            Special_module_permission::truncate();
        }

		Special_module_permission::insert($insert_data);

		return redirect()->route('admin.add_special_permission')->with('success', 'Employees added for special module permission.');

    }

}
