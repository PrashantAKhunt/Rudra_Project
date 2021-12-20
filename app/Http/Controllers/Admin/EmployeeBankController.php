<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Input;
use App\Common_query;
use App\Employees;
use App\EmployeesBankDetails;
use App\Companies;
use App\User;
use Illuminate\Support\Facades\Validator;
use App\Imports\EmployeeSalaryImport;
use Maatwebsite\Excel\Facades\Excel;
use DB;
use App\Role_module;
use Auth;
use App\Lib\Permissions;

class EmployeeBankController extends Controller
{
    public $data;

    public function __construct() {
        $this->data['module_title'] = "Employees";
        $this->data['module_link'] = "admin.employees";
    }

    public function index() {
        $this->data['page_title'] = "Employees";
        $access_level              = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => 11])->get()->first();
        $this->data['access_rule'] = '';
        if(!empty($access_level))
        {
            $this->data['access_rule'] = $access_level->access_level;
        }
        return view('admin.employee_bank_details.index', $this->data);
    }

    public function change_bank_status($id, $status) {
        $check_result=Permissions::checkPermission(11,2);
        
        if(!$check_result){
            return redirect()->route('admin.dashboard')->with('error','Access Denied. You are not authorized to access that page.');
        }
        if (EmployeesBankDetails::where('id', $id)->update(['status' => $status])) {
            return redirect()->route('admin.employee_bank')->with('success', 'Status successfully updated.');
        }
        return redirect()->route('admin.employee_bank')->with('error', 'Error during operation. Try again!');
    }

    public function employee_bank(){
        $check_result=Permissions::checkPermission(11,5);
        if(!$check_result){
            $check_result=Permissions::checkPermission(11,1);
            if(!$check_result){
                return redirect()->route('admin.dashboard')->with('error','Access Denied. You are not authorized to access that page.');
            }
        }
        $this->data['page_title']="Employee bank details";
         $access_level              = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => 11])->get()->first();
        $this->data['access_rule'] = '';
        if(!empty($access_level))
        {
            $this->data['access_rule'] = $access_level->access_level;
        }
        $this->data['employee_detail'] = EmployeesBankDetails::where('user_id', Auth::user()->id)->get()->toArray();
        return view('admin.employee_bank_details.employee_bank', $this->data);
    }

    public function employee_bank_list() {
        $datatable_fields = array('users.name','employee_bank_details.bank_name','employee_bank_details.account_number','employee_bank_details.ifsc_code','employee_bank_details.name_on_account','employee_bank_details.pancard_number','employee_bank_details.pf_number');
        $request = Input::all();
        
        $check_result=Permissions::checkPermission(11,5);
        if(!$check_result){
            $check_result=Permissions::checkPermission(11,1);
            if(!$check_result){
                return redirect()->route('admin.dashboard')->with('error','Access Denied. You are not authorized to access that page.');
            }
            else {
                $conditions_array = ['users.id'=>Auth::user()->id];
            }
        }
        else {
            $conditions_array = [];    
        }

        $getfiled = array('users.name','employee_bank_details.bank_name','employee_bank_details.account_number','employee_bank_details.ifsc_code','employee_bank_details.name_on_account','employee_bank_details.pancard_number','employee_bank_details.id','employee_bank_details.status','employee_bank_details.pf_number');
        $table = "employee_bank_details";
        
        $join_str[0]['join_type']='';
        $join_str[0]['table'] = 'users';
        $join_str[0]['join_table_id'] ='users.id';
        $join_str[0]['from_table_id'] = 'employee_bank_details.user_id';

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);
                                                  
        die();
    }
    public function add_employee_bank() {
        $this->data['page_title'] = 'Add bank details';
        $check_result=Permissions::checkPermission(11,1);
        
        if(!$check_result){
            return redirect()->route('admin.dashboard')->with('error','Access Denied. You are not authorized to access that page.');
        }
        //$this->data['employee']   = User::pluck('name','id');
        $articles = DB::table('users')
            ->select('users.id as users_id','users.name','employee.emp_code')
            ->join('employee', 'users.id', '=', 'employee.user_id')->orderBy('name')
            ->get();
        foreach ($articles as $key => $value) {
            $data['id'][]   = $value->users_id;
            $data['name'][] = $value->name."-".$value->emp_code;
        }
        $this->data['employee']  = array_combine($data['id'], $data['name']);
        
        $this->data['access_rule'] = '';
        $access_level              = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => 11])->get()->first();
        if(!empty($access_level))
        {
            $this->data['access_rule'] = $access_level->access_level;
        }
        return view('admin.employee_bank_details.add_employee_bank', $this->data);
    }
    public function edit_employee_bank($id) {
        $check_result=Permissions::checkPermission(11,2);
        
        if(!$check_result){
            return redirect()->route('admin.dashboard')->with('error','Access Denied. You are not authorized to access that page.');
        }
        $articles = DB::table('users')
            ->select('users.id as users_id','users.name','employee.emp_code')
            ->join('employee', 'users.id', '=', 'employee.user_id')->orderBy('name')
            ->get();
        foreach ($articles as $key => $value) {
            $data['id'][]   = $value->users_id;
            $data['name'][] = $value->name."-".$value->emp_code;
        }
        $this->data['employee']  = array_combine($data['id'], $data['name']);
        $this->data['page_title'] = "Edit employee bank details";
        $this->data['employee_detail'] = EmployeesBankDetails::where('id', $id)->get();
        if ($this->data['employee_detail']->count() == 0) {
            return redirect()->route('admin.employee_bank')->with('error', 'Error Occurred. Try Again!');
        }
        
        $this->data['access_rule'] = '';
        $access_level              = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => 11])->get()->first();
        if(!empty($access_level))
        {
            $this->data['access_rule'] = $access_level->access_level;
        }

        return view('admin.employee_bank_details.edit_employee_bank', $this->data);
    }
    public function insert_employee_bank(Request $request) {
        $validator_normal = Validator::make($request->all(), [
            'bank_name' => 'required',
            'account_number' => 'required',
            'ifsc_code'=>'required',
            'name_on_account'=>'required',
            'pancard_number'=>'required',

        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_employee_bank')->with('error', 'Please follow validation rules.');
        }
        $user_id = !empty($request->input('user_id'))?$request->input('user_id'):Auth::user()->id;
        $employeeModel = new EmployeesBankDetails();
        $employeeModel->user_id   = $user_id;
        $employeeModel->bank_name  = $request->input('bank_name');
        $employeeModel->account_number  = $request->input('account_number');
        $employeeModel->ifsc_code = $request->input('ifsc_code');
        $employeeModel->name_on_account  = $request->input('name_on_account');
        $employeeModel->pancard_number = $request->input('pancard_number');
        $employeeModel->pf_number      = $request->input('pf_number');
        $employeeModel->created_at = date('Y-m-d h:i:s');
        $employeeModel->created_ip = $request->ip();
        $employeeModel->updated_at = date('Y-m-d h:i:s');
        $employeeModel->updated_ip = $request->ip();
        
        if ($employeeModel->save()) {
            return redirect()->route('admin.employee_bank')->with('success', 'New employee bank details added successfully.');
        } else {
            return redirect()->route('admin.add_employee_bank')->with('error', 'Error occurre in insert. Try Again!');
        }
    }
    public function update_employee_bank(Request $request) {
        $validator_normal = Validator::make($request->all(), [
            'user_id'=>'required',
            'bank_name' => 'required',
            'account_number' => 'required',
            'ifsc_code'=>'required',
            'name_on_account'=>'required',
            'pancard_number'=>'required',

        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.edit_employee_bank')->with('error', 'Please follow validation rules.');
        }
        $employee_id = $request->input('id'); 
        $user_id = !empty($request->input('user_id'))?$request->input('user_id'):Auth::user()->id;
        $employee_arr = [
            'user_id' => $user_id,
            'bank_name' => $request->input('bank_name'),
            'account_number'  => $request->input('account_number'),
            'ifsc_code'       => $request->input('ifsc_code'),
            'name_on_account' => $request->input('name_on_account'),
            'pancard_number'  => $request->input('pancard_number'),
            'pf_number'       => $request->input('pf_number'),
            'created_at' => date('Y-m-d h:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
        ];
        
        EmployeesBankDetails::where('id', $employee_id)->update($employee_arr);

        return redirect()->route('admin.employee_bank')->with('success', 'Employee bank details updated successfully.');
    }
    public function delete_employee_bank($id) {
        $check_result=Permissions::checkPermission(11,4);
        
        if(!$check_result){
            return redirect()->route('admin.dashboard')->with('error','Access Denied. You are not authorized to access that page.');
        }
        if (EmployeesBankDetails::where('id', $id)->delete()) {
            return redirect()->route('admin.employee_bank')->with('success', 'Delete successfully updated.');
        }
        return redirect()->route('admin.employee_bank')->with('error', 'Error during operation. Try again!');
    }
}
