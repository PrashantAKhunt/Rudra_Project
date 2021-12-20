<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Common_query;
use App\Employees;
use App\EmployeesSalary;
use App\Companies;
use App\User;
use Illuminate\Support\Facades\Validator;
use App\Imports\EmployeeSalaryImport;
use Maatwebsite\Excel\Facades\Excel;
//use DB;
use App\Lib\Permissions;
use Auth;
use Illuminate\Support\Facades\DB;
class EmployeeSalaryController extends Controller {

    public $data;

    public function __construct() {
        $this->data['module_title'] = "Employees";
        $this->data['module_link'] = "admin.employees";
    }

    public function index() {
        $this->data['page_title'] = "Employees";

        return view('admin.employee.index', $this->data);
    }

    public function get_employee_list() {
        $datatable_fields = array('employee.id', 'employee.emp_code', 'employee.designation', 'company.company_name');
        $request = Input::all();
        $conditions_array = [];

        $getfiled = array('employee.id', 'employee.emp_code', 'employee.designation', 'company.company_name');
        $table = "employee";
        $join_str = [];
        $join_str[0]['join_type'] = 'inner';
        $join_str[0]['table'] = 'company';
        $join_str[0]['join_table_id'] = 'employee.company_id';
        $join_str[0]['from_table_id'] = 'company.id';

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);

        die();
    }

    public function change_employee_status($id, $status) {
        if (Employees::where('id', $id)->update(['status' => $status])) {
            return redirect()->route('admin.employees')->with('success', 'Status successfully updated.');
        }
        return redirect()->route('admin.employees')->with('error', 'Error during operation. Try again!');
    }

    public function add_employee() {
        $this->data['page_title'] = 'Add employee';
        $this->data['companies'] = Companies::pluck('company_name', 'id');
        // echo "<pre>";
        // print_r($this->data['companies']);die;
        return view('admin.employee.add_employee', $this->data);
    }

    public function insert_employee(Request $request) {
        $validator_normal = Validator::make($request->all(), [
                    'emp_code' => 'required',
                    'emp_name' => 'required',
                    'designation' => 'required',
                    'company_id' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_employee')->with('error', 'Please follow validation rules.');
        }

        $employeeModel = new Employees();
        $employeeModel->emp_code = $request->input('emp_code');
        $employeeModel->emp_name = $request->input('emp_name');
        $employeeModel->designation = $request->input('designation');
        $employeeModel->company_id = $request->input('company_id');
        $employeeModel->created_at = date('Y-m-d h:i:s');
        $employeeModel->created_ip = $request->ip();
        $employeeModel->updated_at = date('Y-m-d h:i:s');
        $employeeModel->updated_ip = $request->ip();

        if ($employeeModel->save()) {
            return redirect()->route('admin.employees')->with('success', 'New employee added successfully.');
        } else {
            return redirect()->route('admin.add_employee')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function edit_employee($id) {
        $this->data['page_title'] = "Edit employee";
        $this->data['employee_detail'] = Employees::where('employee.id', $id)->get();
        if ($this->data['employee_detail']->count() == 0) {
            return redirect()->route('admin.employees')->with('error', 'Error Occurred. Try Again!');
        }
        $this->data['companies'] = Companies::pluck('company_name', 'id');
        return view('admin.employee.edit_employee', $this->data);
    }

    public function update_employee(Request $request) {
        $validator_normal = Validator::make($request->all(), [
                    'emp_code' => 'required',
                    'emp_name' => 'required',
                    'designation' => 'required',
                    'company_id' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.employees')->with('error', 'Please follow validation rules.');
        }
        $employee_id = $request->input('id');
        $employee_arr = [
            'emp_code' => $request->input('emp_code'),
            'emp_name' => $request->input('emp_name'),
            'designation' => $request->input('designation'),
            'company_id' => $request->input('company_id'),
            'created_at' => date('Y-m-d h:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
        ];

        Employees::where('id', $employee_id)->update($employee_arr);

        return redirect()->route('admin.employees')->with('success', 'Employee successfully updated.');
    }

    public function employee_salary() {
        $this->data['page_title'] = "Employee salary structure";
        $check_result = Permissions::checkPermission(12, 5);
        if (!$check_result) {
            $check_result = Permissions::checkPermission(12, 1);
            if (!$check_result) {
                return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
            }
        }
        return view('admin.employee.employee_salary', $this->data);
    }

    public function employee_salary_upload(Request $request) {
        Excel::import(new EmployeeSalaryImport, request()->file('emp_salary_file'));
        return back()->with('success', 'Bank transactions imported successfully.');
    }

    public function employee_salary_format() {
        $headers = ["Content-type" => 'application/csv'];
        $file = storage_path('app/public/emp_salary/emp_salary_format.csv');
        return response()->download($file, 'emp_salary_format.csv', $headers);
    }

    public function employee_salary_list() {
        $check_result = Permissions::checkPermission(12, 5);
        if (!$check_result) {
            $check_result = Permissions::checkPermission(12, 1);
            if (!$check_result) {
                return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
            } else {
                $conditions_array = ['users.id' => Auth::user()->id];
            }
        } else {
            $conditions_array = [];
        }

        $datatable_fields = array('users.name', 'employee_salary.basic_salary',
            'employee_salary.hra', 'employee_salary.other_allowance','employee_salary.professional_tax','employee_salary.PF_amount','employee_salary.employer_pf_amount',
            'employee_salary.total_month_salary','employee_salary.gross_salary_pm_ctc',DB::raw("(employee_salary.total_month_salary-employee_salary.PF_amount-employee_salary.professional_tax) as payslip_amount"),
            'employee_salary.salary_month', 'employee_salary.salary_year',
            
            'employee_salary.id');
        $request = Input::all();


        $getfiled = array('users.name', DB::raw("(employee_salary.total_month_salary-employee_salary.PF_amount-employee_salary.professional_tax) as payslip_amount") ,'employee_salary.basic_salary', 'employee_salary.hra', 'employee_salary.other_allowance','employee_salary.professional_tax','employee_salary.PF_amount','employee_salary.employer_pf_amount', 'employee_salary.gross_salary_pm_ctc','employee_salary.salary_month', 'employee_salary.salary_year', 'employee_salary.total_month_salary', 'employee_salary.id');
        $table = "employee_salary";

        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'users';
        $join_str[0]['join_table_id'] = 'users.id';
        $join_str[0]['from_table_id'] = 'employee_salary.user_id';

        // $join_str[1]['join_type']='';
        // $join_str[1]['table'] = 'company';
        // $join_str[1]['join_table_id'] ='company.id';
        // $join_str[1]['from_table_id'] = 'employee_detail.company_id';
        // $join_str[2]['join_type']='';
        // $join_str[2]['table'] = 'bank';
        // $join_str[2]['join_table_id'] ='bank.id';
        // $join_str[2]['from_table_id'] = 'employee_detail.bank_id';


        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);

        die();
    }

    public function add_employee_salary() {
        $this->data['page_title'] = 'Add employee structure';
        $check_result = Permissions::checkPermission(12, 3);

        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }
        //$this->data['employee']   = User::pluck('name','id');
        $articles = DB::table('users')
                ->select('users.id as users_id', 'users.name', 'employee.emp_code')
                ->join('employee', 'users.id', '=', 'employee.user_id')->orderBy('name')
                ->get();
        foreach ($articles as $key => $value) {
            $data['id'][] = $value->users_id;
            $data['name'][] = $value->name . "-" . $value->emp_code;
        }
        $this->data['employee'] = array_combine($data['id'], $data['name']);

        return view('admin.employee.add_employee_salary', $this->data);
    }

    public function edit_employee_salary($id) {
        $check_result = Permissions::checkPermission(12, 2);

        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }
        $articles = DB::table('users')
                ->select('users.id as users_id', 'users.name', 'employee.emp_code')
                ->join('employee', 'users.id', '=', 'employee.user_id')->orderBy('name')
                ->get();
        foreach ($articles as $key => $value) {
            $data['id'][] = $value->users_id;
            $data['name'][] = $value->name . "-" . $value->emp_code;
        }
        $this->data['employee'] = array_combine($data['id'], $data['name']);
        $this->data['page_title'] = "Edit employee structure";
        $this->data['employee_detail'] = EmployeesSalary::where('id', $id)->get();

        if ($this->data['employee_detail']->count() == 0) {
            return redirect()->route('admin.employee_salary')->with('error', 'Error Occurred. Try Again!');
        }
        return view('admin.employee.edit_employee_salary', $this->data);
    }

    public function insert_employee_salary(Request $request) {
        $validator_normal = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'basic_salary' => 'required',
                    'other_allowance' => 'required',
                    'salary_month' => 'required',
                    'salary_year' => 'required',
                    'hra' => 'required',
                    'salaray_category' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_employee_salary')->with('error', 'Please follow validation rules.');
        }

        $employeeModel = new EmployeesSalary();
        $employeeModel->user_id = $request->input('user_id');
        $employeeModel->basic_salary = $request->input('basic_salary');
        $employeeModel->other_allowance = $request->input('other_allowance');
        $employeeModel->salary_month = $request->input('salary_month');
        $employeeModel->salary_year = $request->input('salary_year');
        $employeeModel->hra = $request->input('hra');
        //$employeeModel->total_month_salary = $request->input('basic_salary') + $request->input('hra') + $request->input('other_allowance');
        
        $employeeModel->total_month_salary = $request->input('total_salary');
        $employeeModel->salaray_category = $request->input('salaray_category');
        $employeeModel->professional_tax = $request->input('professional_tax');
        $employeeModel->PF_amount = $request->input('PF_amount');
        $employeeModel->employer_pf_amount = $request->input('employer_pf_amount');
        //$employeeModel->gross_salary_pm_ctc=$request->input('basic_salary') + $request->input('hra') + $request->input('other_allowance')+$request->input('employer_pf_amount');
        $employeeModel->gross_salary_pm_ctc=$request->input('total_salary')+$request->input('employer_pf_amount');
        $employeeModel->created_at = date('Y-m-d h:i:s');
        $employeeModel->created_ip = $request->ip();
        $employeeModel->updated_at = date('Y-m-d h:i:s');
        $employeeModel->updated_ip = $request->ip();

        if ($employeeModel->save()) {
            return redirect()->route('admin.employee_salary')->with('success', 'New employee structure added successfully.');
        } else {
            return redirect()->route('admin.add_employee_salary')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function update_employee_salary(Request $request) {
        $validator_normal = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'basic_salary' => 'required',
                    'other_allowance' => 'required',
                    'salary_month' => 'required',
                    'salary_year' => 'required',
                    'hra' => 'required',
                    'salaray_category' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.edit_employee_salary')->with('error', 'Please follow validation rules.');
        }
        $employee_id = $request->input('id');
        $employee_arr = [
            'user_id' => $request->input('user_id'),
            'basic_salary' => $request->input('basic_salary'),
            'other_allowance' => $request->input('other_allowance'),
            'salary_month' => $request->input('salary_month'),
            'salary_year' => $request->input('salary_year'),
            //'total_month_salary' => $request->input('basic_salary') + $request->input('hra') + $request->input('other_allowance'),
            'total_month_salary'=> $request->input('total_salary'),
            'hra' => $request->input('hra'),
            'salaray_category' => $request->input('salaray_category'),
            "professional_tax" => $request->input('professional_tax'),
            "PF_amount" => $request->input('PF_amount'),
            "employer_pf_amount" => $request->input('employer_pf_amount'),
            //"gross_salary_pm_ctc"=>$request->input('basic_salary') + $request->input('hra') + $request->input('other_allowance')+$request->input('employer_pf_amount'),
            "gross_salary_pm_ctc"=>$request->input('total_salary')+$request->input('employer_pf_amount'),
            'created_at' => date('Y-m-d h:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
        ];

        EmployeesSalary::where('id', $employee_id)->update($employee_arr);

        return redirect()->route('admin.employee_salary')->with('success', 'Employee structure updated successfully.');
    }

    public function delete_employee_salary($id) {
        $check_result = Permissions::checkPermission(12, 4);

        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }
        if (EmployeesSalary::where('id', $id)->delete()) {
            return redirect()->route('admin.employee_salary')->with('success', 'Delete successfully updated.');
        }
        return redirect()->route('admin.employee_salary')->with('error', 'Error during operation. Try again!');
    }

    public function delete_employee($id) {
        if (Employees::where('id', $id)->delete()) {
            return redirect()->route('admin.employees')->with('success', 'Delete successfully updated.');
        }
        return redirect()->route('admin.employees')->with('error', 'Error during operation. Try again!');
    }

    public function salary_slip() {
        $this->data['page_title'] = "Salary Slip";
        return view('admin.employee.emp_salary_slip', $this->data);
    }

}
