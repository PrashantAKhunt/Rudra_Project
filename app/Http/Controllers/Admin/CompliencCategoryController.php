<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use App\Common_query;
use App\Compliance_category;
use App\User;
use Illuminate\Support\Facades\Validator;
use App\Lib\Permissions;

class CompliencCategoryController extends Controller
{
    public $data;
    private $module_id = 71;

    public function __construct() {

        $this->data['module_title'] = "Compliance Category";
        $this->data['module_link'] = "admin.compliance_category";
    }

    public function index()
    {
        $this->data['page_title'] = "Compliance Category";
        $compliance_add_permission = Permissions::checkPermission($this->module_id, 3);
        $compliance_edit_permission = Permissions::checkPermission($this->module_id, 2);

        if (!Permissions::checkPermission($this->module_id, 5)) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You do not have access to this module.');
        }
        $this->data['compliance_add_permission'] = $compliance_add_permission;
        $this->data['compliance_edit_permission'] = $compliance_edit_permission;
        $this->data['records'] = Compliance_category::get(['id','compliance_name','compliance_detail','status','created_at']);

        return view('admin.complience_category.index', $this->data);
    }

    public function change_compliance_status($id, $status) {
        if (Compliance_category::where('id', $id)->update(['status' => $status])) {
            return redirect()->route('admin.compliance_category')->with('success', 'Status successfully updated.');
        }
        return redirect()->route('admin.compliance_category')->with('error', 'Error during operation. Try again!');
    }

    public function add_compliance_category()
    {
        if (!Permissions::checkPermission($this->module_id, 3)) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You do not have access to this module.');
        }

        $this->data['page_title'] = 'Add Compliance Category';
        return view('admin.complience_category.add_complience', $this->data);
    }

    public function insert_compliance_category(Request $request)
    {
        $validator_normal = Validator::make($request->all(), [
            'compliance_name' => 'required',
            'compliance_detail' => 'required'
            
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_complience_category')->with('error', 'Please follow validation rules.');
        } 

        $complianceModel = new Compliance_category();
        $complianceModel->compliance_name = $request->input('compliance_name'); 
        $complianceModel->compliance_detail = $request->input('compliance_detail'); 
        $complianceModel->status = 'Enabled';    
        $complianceModel->created_at = date('Y-m-d h:i:s');
        $complianceModel->created_ip = $request->ip();
        $complianceModel->updated_at = date('Y-m-d h:i:s');
        $complianceModel->updated_ip = $request->ip();

        if ($complianceModel->save()) {
            return redirect()->route('admin.compliance_category')->with('success', 'Complience category successfully added.');
        } else {
            return redirect()->route('admin.add_compliance_category')->with('error', 'Error occurre in insert. Try Again!');
        }

    }

    public function edit_compliance_category($id)
    {
        if (!Permissions::checkPermission($this->module_id, 2)) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You do not have access to this module.');
        }
        $this->data['page_title'] = "Edit Complience Category";
        $this->data['records'] = Compliance_category::where('id',$id)->first();
        return view('admin.complience_category.edit_complience', $this->data);
    }

    public function update_compliance_category(Request $request)
    {
        $validator_normal = Validator::make($request->all(), [
            'compliance_name' => 'required',
            'compliance_detail' => 'required'
        ]);
        $complience_id = $request->input('id'); 
        if ($validator_normal->fails()) {
            return redirect()->route('admin.edit_compliance_category',$complience_id)->with('error', 'Please follow validation rules.');
        } 
        $update_arr = [
            'compliance_name' =>  $request->input('compliance_name'),
            'compliance_detail' => $request->input('compliance_detail'),            
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
        ];

        Compliance_category::where('id', $complience_id)->update($update_arr);

        return redirect()->route('admin.compliance_category')->with('success', 'Complience Category successfully updated.');
    }

    
}
