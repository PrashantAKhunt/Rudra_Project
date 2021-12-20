<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Common_query;
use App\Employees;
use App\EmployeesBankDetails;
use App\EmployeesLoans;
use App\EmployeesSalary;
use App\Companies;
use App\User;
use Illuminate\Support\Facades\Validator;
use App\Imports\EmployeeSalaryImport;
use App\Role_module;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use DB;
use App\Lib\Permissions;
use App\Lib\CommonTask;
use App\Lib\NotificationTask;

class EmployeeLoanController extends Controller {

    public $data;
    public $notification_task;
    public $common_task;

    public function __construct() {
        $this->data['module_title'] = "Employees";
        $this->data['module_link'] = "admin.employees";
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();
    }

    public function change_loan_status($id, $status) {
        $check_result = Permissions::checkPermission(10, 2);

        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }
        if (EmployeesLoans::where('id', $id)->update(['status' => $status])) {
            return redirect()->route('admin.employee_loan')->with('success', 'Status successfully updated.');
        }
        return redirect()->route('admin.employee_loan')->with('error', 'Error during operation. Try again!');
    }

    public function approve_emp_loan($id, Request $request) {
        $empData = EmployeesLoans::where('id', $id)->get(['*'])->toArray();
        if (Auth::user()->role == config('constants.REAL_HR')) {
            $update_arr = [
                'first_approval_status' => "Approved",
                'first_approval_id' => Auth::user()->id,
                'first_approval_datetime' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip()
            ];

            $first_approval_user= User::where('role',config('constants.SuperUser'))->get(['id'])->pluck('id')->toArray();
            $this->notification_task->LoanApprovalNotify($first_approval_user);
            
        } elseif (Auth::user()->role == config('constants.SuperUser')) {
            $update_arr = [
                'second_approval_status' => "Approved",
                'second_approval_id' => Auth::user()->id,
                'second_approval_datetime' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip()
            ];
            $second_approval_user= User::where('role',config('constants.ACCOUNT_ROLE'))->get(['id'])->pluck('id')->toArray();
            $this->notification_task->LoanApprovalNotify($second_approval_user);
        } elseif (Auth::user()->role == config('constants.ACCOUNT_ROLE')) {
            $update_arr = [
                'third_approval_status' => "Approved",
                'third_approval_id' => Auth::user()->id,
                'third_approval_datetime' => date('Y-m-d H:i:s'),
                'loan_status' => 'Approved',
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip()
            ];
            $userData = \App\User::where('id', $empData[0]['user_id'])->get()->toArray();

            $mail_data = [
                'user_name' => $userData[0]['name'],
                'loan_amount' => $empData[0]['loan_amount'],
                'loan_term' => $empData[0]['loan_terms'],
                'email' => $userData[0]['email'],
            ];

            $this->common_task->approveLoanEmail($mail_data);
            $User_list = \App\User::where('id', $empData[0]['user_id'])->get(['id'])->pluck('id')->toArray();
            $this->notification_task->approveLoanNotify($User_list);
        } else {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You do not have this permission.');
        }

        EmployeesLoans::where('id', $id)->update($update_arr);

        return redirect()->route('admin.employee_loan')->with('success', 'Loan successfully approved.');
    }

    public function reject_emp_loan(Request $request) {
        $id = $request->input('id');
        $reject_note = $request->input('note');

        $empData = EmployeesLoans::where('id', $id)->get(['*'])->toArray();

        $User_list = \App\User::where('id', $empData[0]['user_id'])->get(['id'])->pluck('id')->toArray();
        $this->notification_task->rejectLoanNotify($User_list);
        $userData = \App\User::where('id', $empData[0]['user_id'])->get()->toArray();
        $mail_data = [
            'user_name' => $userData[0]['name'],
            'loan_amount' => $empData[0]['loan_amount'],
            'loan_term' => $empData[0]['loan_terms'],
            'reject_note' => $reject_note,
            'email' => $userData[0]['email'],
        ];

        $this->common_task->rejectLoanEmail($mail_data);

        if (Auth::user()->role == config('constants.REAL_HR')) {
            $update_arr = [
                'first_approval_status' => "Rejected",
                'first_approval_id' => Auth::user()->id,
                'first_approval_datetime' => date('Y-m-d H:i:s'),
                'loan_status' => 'Rejected',
                'reject_note' => $reject_note,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip()
            ];
        } elseif (Auth::user()->role == config('constants.SuperUser')) {
            $update_arr = [
                'second_approval_status' => "Rejected",
                'second_approval_id' => Auth::user()->id,
                'second_approval_datetime' => date('Y-m-d H:i:s'),
                'loan_status' => 'Rejected',
                'reject_note' => $reject_note,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip()
            ];
        } elseif (Auth::user()->role == config('constants.ACCOUNT_ROLE')) {
            $update_arr = [
                'third_approval_status' => "Rejected",
                'third_approval_id' => Auth::user()->id,
                'third_approval_datetime' => date('Y-m-d H:i:s'),
                'loan_status' => 'Rejected',
                'reject_note' => $reject_note,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip()
            ];
        } else {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You do not have this permission.');
        }

        EmployeesLoans::where('id', $id)->update($update_arr);

        return redirect()->route('admin.employee_loan')->with('success', 'Loan successfully rejected.');
    }

    public function delete_employee_loan($id) {
        $check_result = Permissions::checkPermission(10, 4);

        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }

        $adminData = \App\User::where('role', config('constants.SuperUser'))->get(['*'])->toArray();
        $loanData = EmployeesLoans::where('id', $id)->get()->toArray();
        $mail_data = [
            'admin_user' => $adminData[0]['name'],
            'user_name' => Auth::user()->name,
            'loan_amount' => $loanData[0]['loan_amount'],
            'loan_term' => $loanData[0]['loan_terms'],
            'email' => $adminData[0]['email']
        ];

        $this->common_task->cancelLoanEmail($mail_data);

        $SuperUser_list = \App\User::where('role', config('constants.SuperUser'))->get(['id'])->pluck('id')->toArray();
        $this->notification_task->deleteLoanNotify([$SuperUser_list], Auth::user()->name);
        if (EmployeesLoans::where('id', $id)->delete()) {
            return redirect()->route('admin.employee_loan')->with('success', 'Delete successfully updated.');
        }
        return redirect()->route('admin.employee_loan')->with('error', 'Error during operation. Try again!');
    }

    public function employee_loan() {
        $check_result = Permissions::checkPermission(10, 5);
        if (!$check_result) {
            $check_result = Permissions::checkPermission(10, 1);
            if (!$check_result) {
                return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
            }
        }

        $this->data['page_title'] = "Employee loan details";
        $access_level = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => 10])->get()->first();
        $this->data['access_rule'] = '';
        if (!empty($access_level)) {
            $this->data['access_rule'] = $access_level->access_level;
        }
        return view('admin.employee_loan.employee_loan', $this->data);
    }

    public function employee_loan_list() {
        $datatable_fields = array('employee_loan.cheque_no','users.name', 'employee_loan.loan_type', 'employee_loan.loan_amount',
            'employee_loan.loan_expected_month', 'employee_loan.loan_emi_start_from',
            'employee_loan.loan_terms', 'employee_loan.loan_descption', 'employee_loan.first_approval_status',
            'employee_loan.second_approval_status', 'employee_loan.third_approval_status', 'employee_loan.loan_status');
        $request = Input::all();
        $check_result = Permissions::checkPermission(10, 5);
        /* if(!$check_result){
          $check_result=Permissions::checkPermission(10,1);
          if(!$check_result){
          return redirect()->route('admin.dashboard')->with('error','Access Denied. You are not authorized to access that page.');
          }
          else {
          $conditions_array = ['users.id'=>Auth::user()->id];
          }
          }
          else {
          $conditions_array = [];
          } */
        $conditions_array = [];

        if (Auth::user()->role == config('constants.SuperUser') || Auth::user()->role == config('constants.REAL_HR') || Auth::user()->role == config('constants.ACCOUNT_ROLE')) {
            $conditions_array = [];
        } else {
            $conditions_array = ['users.id' => Auth::user()->id];
        }

        $getfiled = array('employee_loan.cheque_no','users.name', 'employee_loan.user_id','employee_loan.loan_type', 'employee_loan.first_approval_datetime', 'employee_loan.second_approval_datetime', 'employee_loan.third_approval_datetime', 'employee_loan.first_approval_id', 'employee_loan.second_approval_id', 'employee_loan.third_approval_id', 'employee_loan.first_approval_status',
            'employee_loan.second_approval_status', 'employee_loan.reject_note','employee_loan.third_approval_status', 'employee_loan.loan_amount', 'employee_loan.loan_expected_month', 'employee_loan.loan_emi_start_from', 'employee_loan.loan_terms', 'employee_loan.loan_descption', 'employee_loan.status', 'employee_loan.loan_status', 'employee_loan.id');
        $table = "employee_loan";

        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'users';
        $join_str[0]['join_table_id'] = 'users.id';
        $join_str[0]['from_table_id'] = 'employee_loan.user_id';

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);

        die();
    }

    public function add_employee_loan() {
        $check_result = Permissions::checkPermission(10, 3);
        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }
        $this->data['page_title'] = 'Add loan details';
        $this->data['employeeSalary']=$employeeSalary = EmployeesSalary::where('user_id', Auth::user()->id)->get(['total_month_salary', 'PF_amount', 'professional_tax'])->first();

        if (empty(($employeeSalary))) {
            return redirect()->route('admin.employee_loan')->with('error', 'Salary structure is not added. Please contact HR department to add your salary structure.');
        }
        
        //check if already any loan is applied or approved
        $loan_check=EmployeesLoans::where('user_id', Auth::user()->id)->where(function($query){
            $query->where('loan_status','Approved');
            $query->orWhere('loan_status','Pending');
        })->get();
        
        if($loan_check->count()>0){
            if($loan_check[0]->loan_status=='Approved'){
                if($loan_check[0]->completed_loan_terms!=$loan_check[0]->loan_terms){
                    return redirect()->route('admin.employee_loan')->with('error','Your one loan is already running so you can not apply another until last one will be complete.');
                }
            }
            else{
                return redirect()->route('admin.employee_loan')->with('error','Your one loan is pending for approval. You can not apply new one until it is approved and completed or rejected.');
            }
        }

        $this->data['normal_loan_amount'] = ((($employeeSalary->total_month_salary - ($employeeSalary->PF_amount + $employeeSalary->professional_tax)) * 12) / 10);
        $this->data['advance_loan_amount'] = (EmployeesLoans::getSalaryAmount() > 0) ? (EmployeesLoans::getSalaryAmount() * 0.30) : 0;
        $this->data['terms'] = config('app.hours');
        $articles = DB::table('users')
                ->select('users.id as users_id', 'users.name', 'employee.emp_code')
                ->join('employee', 'users.id', '=', 'employee.user_id')
                ->get();
        foreach ($articles as $key => $value) {
            $data['id'][] = $value->users_id;
            $data['name'][] = $value->name . "-" . $value->emp_code;
        }
        $this->data['employee'] = array_combine($data['id'], $data['name']);
        $this->data['emi_start_month'] = ['1' => 'Jan', '2' => 'Feb', '3' => 'March', '4' => 'April', '5' => 'May', '6' => 'Jun', '7' => 'Jul', '8' => 'Aug', '9' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Des'];
        return view('admin.employee_loan.add_employee_loan', $this->data);
    }
    // public function advance_salary(){
    //     dd("Inn For Advance Salary");

    // }

    public function edit_employee_loan($id) {
        $check_result = Permissions::checkPermission(10, 2);
        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }
        $this->data['employeeSalary']=$employeeSalary = EmployeesSalary::where('user_id', Auth::user()->id)->get(['total_month_salary', 'PF_amount', 'professional_tax'])->first();
        $this->data['normal_loan_amount'] = ((($employeeSalary->total_month_salary - ($employeeSalary->PF_amount + $employeeSalary->professional_tax)) * 12) / 10);
        $this->data['advance_loan_amount'] = (EmployeesLoans::getSalaryAmount() > 0) ? (EmployeesLoans::getSalaryAmount() * 0.30) : 0;
        $this->data['terms'] = config('app.hours');
        $articles = DB::table('users')
                ->select('users.id as users_id', 'users.name', 'employee.emp_code')
                ->join('employee', 'users.id', '=', 'employee.user_id')
                ->get();
        foreach ($articles as $key => $value) {
            $data['id'][] = $value->users_id;
            $data['name'][] = $value->name . "-" . $value->emp_code;
        }
        $this->data['employee'] = array_combine($data['id'], $data['name']);
        $this->data['page_title'] = "Edit employee loan details";
        $this->data['employee_detail'] = EmployeesLoans::where('id', $id)->get();
        $this->data['emi_start_month'] = ['1' => 'Jan', '2' => 'Feb', '3' => 'March', '4' => 'April', '5' => 'May', '6' => 'Jun', '7' => 'Jul', '8' => 'Aug', '9' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Desc'];
        if ($this->data['employee_detail']->count() == 0) {
            return redirect()->route('admin.employee_loan')->with('error', 'Error Occurred. Try Again!');
        }
        return view('admin.employee_loan.edit_employee_loan', $this->data);
    }

    public function insert_employee_loan(Request $request) {
        $validator_normal = Validator::make($request->all(), [
                    'loan_amount' => 'required',
                    'loan_expected_month' => 'required',
                    'loan_terms' => 'required',
                    'loan_descption' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_employee_loan')->with('error', 'Please follow validation rules.');
        }
        $employeeLoanModel = new EmployeesLoans();
        $employeeLoanModel->user_id = Auth::user()->id;
        $employeeLoanModel->loan_amount = $request->input('loan_amount');
        $employeeLoanModel->loan_type = $request->input('loan_type');
        $employeeLoanModel->loan_expected_month = $request->input('loan_expected_month');
        $employeeLoanModel->loan_emi_start_from = $request->input('loan_expected_month');
        $employeeLoanModel->loan_terms = $request->input('loan_terms');
        $employeeLoanModel->loan_descption = $request->input('loan_descption');
        $employeeLoanModel->created_at = date('Y-m-d h:i:s');
        $employeeLoanModel->created_ip = $request->ip();
        $employeeLoanModel->updated_at = date('Y-m-d h:i:s');
        $employeeLoanModel->updated_ip = $request->ip();
        if ($employeeLoanModel->save()) {
            //$adminData = \App\User::where('role', config('constants.SuperUser'))->get(['*'])->toArray();
            $hrData = User::where('role', config('constants.REAL_HR'))->get(['*'])->toArray();
            $mail_data = [
                'admin_user' => $hrData[0]['name'],
                'user_name' => Auth::user()->name,
                'loan_amount' => $request->input('loan_amount'),
                'loan_term' => $request->input('loan_terms'),
                'email' => $hrData[0]['email']
            ];
            $this->common_task->applyLoanEmail($mail_data);
            $hr_list = \App\User::where('role', config('constants.REAL_HR'))->get(['id'])->pluck('id')->toArray();
            $this->notification_task->addLoanNotify($hr_list, Auth::user()->name);
            return redirect()->route('admin.employee_loan')->with('success', 'Loan applied successfully.');
        } else {
            return redirect()->route('admin.add_employee_loan')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function advance_salary(Request $request){

        $validator_normal = Validator::make($request->all(), [
            // 'loan_amount' => 'required',
            // 'loan_expected_month' => 'required',
            // 'loan_terms' => 'required',
            'loan_description' => 'required',
            ]);
            if ($validator_normal->fails()) {
                    return redirect()->route('admin.employee_loan')->with('error', 'Please follow validation rules.');
                }
                $advance_salary = new EmployeesLoans();
                $advance_salary->user_id = Auth::user()->id;
                $advance_salary->loan_amount = 0;
                // $advance_salary->loan_amount = $request->input('loan_amount');
                // $advance_salary->loan_type = $request->input('loan_type');
                $advance_salary->loan_type = 1;
                $advance_salary->loan_expected_month = date('m-Y');
                $advance_salary->loan_emi_start_from = date('m-Y');
                $advance_salary->loan_terms = 01;
                $advance_salary->loan_descption = $request->input('loan_description');
                $advance_salary->created_at = date('Y-m-d h:i:s');
                $advance_salary->created_ip = $request->ip();
                $advance_salary->updated_at = date('Y-m-d h:i:s');
                $advance_salary->updated_ip = $request->ip();
                if ($advance_salary->save()) {
                    //$adminData = \App\User::where('role', config('constants.SuperUser'))->get(['*'])->toArray();
                    $hrData = User::where('role', config('constants.REAL_HR'))->get(['*'])->toArray();
                    $mail_data = [
                        'admin_user' => $hrData[0]['name'],
                        'user_name' => Auth::user()->name,
                        'loan_amount' => 0,
                        'loan_term' => 1,
                        'email' => $hrData[0]['email']
                    ];
                    $this->common_task->applyLoanEmail($mail_data);
                    $hr_list = \App\User::where('role', config('constants.REAL_HR'))->get(['id'])->pluck('id')->toArray();
                    $this->notification_task->addLoanNotify($hr_list, Auth::user()->name);
                    return redirect()->route('admin.employee_loan')->with('success', 'Advance Salary applied successfully.');
                } else {
                    return redirect()->route('admin.employee_loan')->with('error', 'Error occurre in insert. Try Again!');
                }

    }

    public function update_employee_loan(Request $request) {
        $validator_normal = Validator::make($request->all(), [
                    'loan_amount' => 'required',
                    'loan_expected_month' => 'required',
                    'loan_terms' => 'required',
                    'loan_descption' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.employee_loan')->with('error', 'Please follow validation rules.');
        }

        $employee_id = $request->input('id');
        $employee_arr = [
            'user_id' => Auth::user()->id,
            'loan_amount' => $request->input('loan_amount'),
            'loan_expected_month' => $request->input('loan_expected_month'),
            'loan_emi_start_from' => $request->input('loan_expected_month'),
            'loan_terms' => $request->input('loan_terms'),
            'loan_descption' => $request->input('loan_descption'),
            'created_at' => date('Y-m-d h:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
            'first_approval_status' => 'Pending',
            'second_approval_status' => 'Pending',
            'third_approval_status' => 'Pending',
            'loan_status' => 'Pending'
        ];

        EmployeesLoans::where('id', $employee_id)->update($employee_arr);
        $hrData = \App\User::where('role', config('constants.REAL_HR'))->get(['*'])->toArray();
        $loanData = EmployeesLoans::where('id', $employee_id)->get()->toArray();
        $mail_data = [
            'admin_user' => $hrData[0]['name'],
            'user_name' => Auth::user()->name,
            'loan_amount' => $loanData[0]['loan_amount'],
            'loan_term' => $loanData[0]['loan_terms'],
            'email' => $hrData[0]['email']
        ];

        $this->common_task->editLoanEmail($mail_data);

        $hr_list = \App\User::where('role', config('constants.REAL_HR'))->get(['id'])->pluck('id')->toArray();
        $this->notification_task->editLoanNotify($hr_list, Auth::user()->name);

        return redirect()->route('admin.employee_loan')->with('success', 'Employee loan details updated successfully.');
    }

    //17/09/2020
    public function get_loan_payment_details(Request $request)  
    {
        $validator_normal = Validator::make($request->all(), ['id' => 'required']);

        if ($validator_normal->fails()) {
            return response()->json(['status' => false]);
        }

        $id = $request->id;
        $companies = \App\Companies::select('id', 'company_name')->orderBy('company_name', 'asc')->get()->toArray();
        $html = "<option value=''>Select Company</option>";
            foreach ($companies as $key => $company) {
                 $html.= "<option value=".$company['id'].">".$company['company_name']."</option>";
            }
        $this->data['company_list'] = $html;
        $loan_details = EmployeesLoans::leftjoin('cheque_register','cheque_register.id','=','employee_loan.cheque_no')
                ->where('employee_loan.id',$id)
                ->get(['employee_loan.id','employee_loan.cheque_no','cheque_register.ch_no','employee_loan.payment_details'])->first();
        

        $this->data['loan_details'] = $loan_details;
        return response()->json(['status' => true, 'data' => $this->data]);
        

    }
    //17/09/2020
    public function submit_loan_payments_details(Request $request) {
        $validator_normal = Validator::make($request->all(), [
            'loan_ids' => 'required',
            'cheque_number' => 'required',
            'payment_details' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.employee_loan')->with('error', 'Please follow validation rules.');
        }
        $request_data = $request->all();
        $update_data = [
            'cheque_no' => $request_data['cheque_number'],
            'payment_details' => $request_data['payment_details'],
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            // 'updated_by' => Auth::user()->id
        ];
        
        if (EmployeesLoans::whereIn('id', explode(",",$request_data['loan_ids']))->update($update_data)) {
            return redirect()->route('admin.employee_loan')->with('success', 'Payment details successfully submitted');
        }

        return redirect()->route('admin.employee_loan')->with('error', 'Error Occurred. Try Again!');
        
    }

}
