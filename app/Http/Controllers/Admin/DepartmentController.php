<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Job_opening;
use App\Job_opening_consultant;
use App\Interview;
use App\User;
use App\InterviewResult;
use App\Department;
use App\Email_format;
use App\Mail\Mails;
use Exception;
use App\Recruitment_consultant;
use DB;
use Illuminate\Support\Facades\Mail;
use Auth;
use App\Lib\Permissions;
use App\Role_module;

class DepartmentController extends Controller {

    public function __construct() {
        $this->data['module_title'] = "Department";
        $this->data['module_link'] = "admin.department";
    }

    public function index() {
        $this->data['page_title'] = "Department";
        $this->data['department_list'] = DB::table('department')->select('department.*')->get();
        $check_result = Permissions::checkPermission(6, 5);
        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }
        $access_level = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => 6])->get()->first();
        $this->data['access_rule'] = '';
        if (!empty($access_level)) {
            $this->data['access_rule'] = $access_level->access_level;
        }
        return view('admin.department.index', $this->data);
    }

    public function add_department() {
        $this->data['page_title'] = "Add Department Details";
        $this->data['job_opening_position'] = Department::select('id', 'dept_name')->get();
        $check_result = Permissions::checkPermission(6, 3);
        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }
        return view('admin.department.add_department', $this->data);
    }

    public function get_departmentlist_by_company(Request $request) {
        $company_id=$request->input('company_id');
        
        $department_list= Department::where('company_id',$company_id)->get();
        
        $html='<option value="">Select Department</option>';
        
        if($department_list->count()>0){
            foreach ($department_list as $department){
                $html .='<option value="'.$department->id.'">'.$department->dept_name.'</option>';
            }
        }
        echo $html; die();
    }

    public function insert_department(Request $request) {
        $validator_normal = Validator::make($request->all(), [
                    'dept_name' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.department')->with('error', 'Please follow validation rules.');
        }


        $departmentModel = new Department();
        $departmentModel->dept_name = $request->input('dept_name');
        $departmentModel->dept_description = $request->input('dept_description');
        $departmentModel->created_at = date('Y-m-d h:i:s');
        $departmentModel->created_ip = $request->ip();
        $departmentModel->updated_at = date('Y-m-d h:i:s');
        $departmentModel->updated_ip = $request->ip();
        if ($departmentModel->save()) {
            return redirect()->route('admin.department')->with('success', 'Department details added successfully.');
        } else {
            return redirect()->route('admin.department')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function edit_department($id) {
        $this->data['page_title'] = "Edit Department Details";
        $this->data['department_list'] = DB::table('department')
                ->select('department.*')
                ->where('department.id', $id)
                ->get();
        $check_result = Permissions::checkPermission(6, 2);
        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }

        if ($this->data['department_list']->count() == 0) {
            return redirect()->route('admin.department')->with('error', 'Error Occurred. Try Again!');
        }

        return view('admin.department.edit_department', $this->data);
    }

    public function update_department(Request $request) {
        $validator_normal = Validator::make($request->all(), [
                    'dept_name' => 'required',
        ]);

        if ($validator_normal->fails()) {
            return redirect()->route('admin.department')->with('error', 'Please follow validation rules.');
        }
        $department_id = $request->input('id');
        $department_arr = [
            'dept_name' => $request->input('dept_name'),
            'dept_description' => $request->input('dept_description'),
            'created_at' => date('Y-m-d h:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
        ];
        Department::where('id', $department_id)->update($department_arr);
        return redirect()->route('admin.department')->with('success', 'Department details successfully updated.');
    }

}
