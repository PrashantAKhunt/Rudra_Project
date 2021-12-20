<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use App\Common_query;
use App\Employee_cash_management;
use App\User;
use Illuminate\Support\Facades\Validator;
use App\Lib\Permissions;
use App\Cash_transfer;

class EmployeeCashManagementController extends Controller
{
    public $data;
    private $module_id = 74;

    public function __construct() {

        $this->data['module_title'] = "Employee Cash Management";
        $this->data['module_link'] = "admin.employee_cash_management";
    }

    public function index()
    {
        $this->data['page_title'] = "Employee Cash Management";
        $emp_cash_add_permission = Permissions::checkPermission($this->module_id, 3);
        // $emp_cash_edit_permission = Permissions::checkPermission($this->module_id, 2);

        if (!Permissions::checkPermission($this->module_id, 5)) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You do not have access to this module.');
        }
        $this->data['emp_cash_add_permission'] = $emp_cash_add_permission;

        return view('admin.employee_cash_management.index', $this->data);
    }

    public function get_employee_cash_list() {  
        $datatable_fields = array('users.name','employee_cash_management.balance','employee_cash_management.created_at','employee_cash_management.receive_status','employee_cash_management.receive_datetime');
        $request = Input::all();
        $conditions_array = [];

        $getfiled = array('employee_cash_management.id','employee_cash_management.user_id','employee_cash_management.balance','employee_cash_management.created_at','users.name','employee_cash_management.receive_status','employee_cash_management.receive_datetime','employee_cash_management.employee_id');
        $table = "employee_cash_management";
        
        $join_str[0]['join_type']='';
        $join_str[0]['table'] = 'users';
        $join_str[0]['join_table_id'] ='employee_cash_management.employee_id';
        $join_str[0]['from_table_id'] = 'users.id';
        
        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);
                                                  
        die();
    }

    public function add_employee_cash()
    {
        if (!Permissions::checkPermission($this->module_id, 3)) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You do not have access to this module.');
        }

        $this->data['page_title'] = 'Add Employee Cash';
        $this->data['users_data'] = User::select('id', 'name')->where('status', 'Enabled')->orderBy('name', 'asc')->where('is_user_relieved', 0)->get();
        return view('admin.employee_cash_management.add_employee_cash', $this->data);
    }

    public function insert_employee_cash(Request $request)
    {
        $validator_normal = Validator::make($request->all(), [
            'employee_id' => 'required',
            'balance' => 'required'
            
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_employee_cash')->with('error', 'Please follow validation rules.');
        } 

        $cashModel = new Employee_cash_management();
        $cashModel->employee_id = $request->input('employee_id'); 
        $cashModel->balance = $request->input('balance'); 
        $cashModel->user_id = Auth::user()->id;    
        $cashModel->created_at = date('Y-m-d h:i:s');
        $cashModel->created_ip = $request->ip();
        $cashModel->updated_at = date('Y-m-d h:i:s');
        $cashModel->updated_ip = $request->ip();
        $cashModel->updated_by = Auth::user()->id;

        if ($cashModel->save()) {

            return redirect()->route('admin.employee_cash_management')->with('success', 'Employee cash successfully added.');
        } else {
            return redirect()->route('admin.employee_cash_management')->with('error', 'Error occurre in insert. Try Again!');
        }

    }

    public function confirm_employee_cash($id, Request $request){
        // dd($id);
        $update_arr= [
                        'receive_status' => 'Confirmed',
                        'receive_datetime' => date('Y-m-d h:i:s')
                     ];

        if (employee_cash_management::where('id', $id)->update($update_arr)) {
            return redirect()->route('admin.employee_cash_management')->with('success', 'Employee cash successfully confirmed.');
        }    
    }
}
