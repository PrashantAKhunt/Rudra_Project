<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use App\Job_opening;
use App\Common_query;
use App\Job_opening_consultant;
use App\Interview;
use App\User;
use App\Employees;
use App\InterviewResult;
use App\Department;
use App\Email_format;
use App\Mail\Mails;
use Exception;
use App\Recruitment_consultant;
use DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Lib\Permissions;
use App\Expense_category;
use App\Role_module;
use App\Employee_expense;
use App\Banks;
use App\ChequeRegister;
use App\Companies;
use App\Projects;
use App\Clients;
use App\Project_sites;
use App\AssignedVoucher;
use App\VoucherNumberRegister;
use App\Employee_cash_management;
use App\Cash_transfer;
use App\Lib\NotificationTask;
use App\Lib\UserActionLogs;
use App\Lib\CommonTask;

class EmployeeExpenseController extends Controller {

    private $notification_task;
    public $user_action_logs;
    private $common_task;

    public function __construct() {
        $this->data['module_title'] = "Employee Expense";
        $this->data['module_link'] = "admin.employee_expense";
        $this->notification_task = new NotificationTask();
        $this->common_task = new CommonTask();
        $this->user_action_logs = new UserActionLogs();
        $this->super_admin = \App\User::where('role', config('constants.SuperUser'))->first();
    }

    public function index() {
        $this->data['page_title'] = "Employee Expense";
        $access_level = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => 19])->get()->first();
        $this->data['access_rule'] = '';
        if (!empty($access_level)) {
            $this->data['access_rule'] = $access_level->access_level;
        }

        $conditions_array = [Auth::user()->id];

        $Result = DB::table('employee_expense')
                ->select('employee_expense.*', 'clients.client_name', 'clients.location', 'project_sites.site_name', 'acc_users.name as acc_user_name', 'acc_users.email as acc_email', 'expense_category.category_name', 'users.name', 'company.company_name', 'project.project_name')
                ->join('users', 'employee_expense.user_id', '=', 'users.id')
                ->leftJoin('users as acc_users', 'acc_users.id', '=', 'employee_expense.fifth_approval_id')
                ->join('expense_category', 'employee_expense.expense_category', '=', 'expense_category.id')
                ->join('company', 'company.id', '=', 'employee_expense.company_id')
                ->leftJoin('clients', 'clients.id', '=', 'employee_expense.client_id')
                ->leftJoin('project_sites', 'project_sites.id', '=', 'employee_expense.project_site_id')
                ->join('project', 'project.id', '=', 'employee_expense.project_id');
        if (!empty($conditions_array)) {
            $Result->whereIn('employee_expense.user_id', $conditions_array);
        }


        $this->data['employee_expense_list'] = $Result->get();


        return view('admin.employee_expense.index', $this->data);
    }

    public function add_employee_expense() {
        $this->data['page_title'] = "Add Expense Details";
        $check_result = Permissions::checkPermission(19, 3);
        $this->data['Companies'] = Companies::orderBy('company_name')->select('id', 'company_name')->get();
        $this->data['Projects'] = Projects::select('id', 'project_name')->get();
        $this->data['Expense_List'] = Expense_category::orderBy('category_name')->select('id', 'category_name')->get();
        $this->data['UsersName'] = User::select('id', 'name')->get();
        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }
        return view('admin.employee_expense.add_employee_expense', $this->data);
    }

    public function insert_employee_expense(Request $request) {
        $validator_normal = Validator::make($request->all(), [
                    'expense_category' => 'required',
                    'project_type' => 'required',
                    'bill_number' => 'required',
                    'merchant_name' => 'required',
                    'amount' => 'required',
                    'expense_date' => 'required',
                    'comment' => 'required',
                    'client_id' => 'required',
                    'project_site_id' => 'required',
                    //'voucher_no' => 'required',
                    'company_id' => 'required',
                    'project_id' => 'required',
                    //'user_id' => 'required',
                    'title' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.employee_expense')->with('error', 'Please follow validation rules.');
        }
        // dd($request->all());
        //upload user profile image
        //upload user profile image
        /* $asset_image = '';
          if ($request->file('image')) {
          $profile_image = $request->file('image');
          $file_path = $profile_image->store('public/expense_image');
          if ($file_path) {
          $asset_image = $file_path;
          }
          } */

        //21-02-2020
        
        //restrict for past date entry
        if(strtotime($request->input('expense_date'))<strtotime(date('Y-m-d'))){
            return redirect()->route('admin.employee_expense')->with('error', 'You can not add the past date entry.');
        }
        
        $asset_image = '';
        if ($request->file('image')) {

            $asset_file = $request->file('image');

            $original_file_name = explode('.', $asset_file->getClientOriginalName());

            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);


            $file_path = $asset_file->storeAs('public/expense_image', $new_file_name);
            if ($file_path) {
                $asset_image = $file_path;
            }
        }
        $voucher_image = NULL;
        if ($request->file('voucher_image')) {

            $asset_file = $request->file('voucher_image');

            $original_file_name = explode('.', $asset_file->getClientOriginalName());

            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);


            $file_path = $asset_file->storeAs('public/voucher_image', $new_file_name);
            if ($file_path) {
                $voucher_image = $file_path;
            }
        }

        $expense_employeeModel = new Employee_expense();

        $expense_employeeModel->expense_category = $request->input('expense_category');
        $expense_employeeModel->project_type = $request->input('project_type');

        $expense_employeeModel->project_id = $request->input('project_id');
        $expense_employeeModel->company_id = $request->input('company_id');
        $expense_employeeModel->other_project = !empty($request->input('other_project')) ? $request->input('other_project') : "";

        $expense_employeeModel->client_id = $request->input('client_id');
        $expense_employeeModel->project_site_id = $request->input('project_site_id');

        $expense_employeeModel->bill_number = $request->input('bill_number');
        $expense_employeeModel->merchant_name = $request->input('merchant_name');
        $expense_employeeModel->amount = $request->input('amount');
        $expense_employeeModel->expense_date = date('Y-m-d h:i:s', strtotime($request->input('expense_date')));
        $expense_employeeModel->comment = $request->input('comment');


        $expense_employeeModel->voucher_ref_no = $request->input('voucher_ref_no');
        $expense_employeeModel->voucher_id = $request->input('voucher_no');
        $expense_employeeModel->voucher_image = $voucher_image;

        if (!empty($request->input('voucher_no'))) {
            $voucher_data = VoucherNumberRegister::whereId($request->input('voucher_no'))->first();
            if ($voucher_data) {
                $expense_employeeModel->voucher_no = $voucher_data['voucher_no'];
            }
        }
        /* if (!empty($request->input('voucher_no'))) {
          $voucher_no = $request->input('voucher_no');
          $find_records = Employee_expense::where('voucher_no', $voucher_no)->get();
          if ($find_records->count() > 0) {
          Employee_expense::where('voucher_no', $voucher_no)->update(['voucher_repeat' => 1]);
          $expense_employeeModel->voucher_repeat = 1;
          } else {
          $expense_employeeModel->voucher_repeat = 0;
          } */
        // $expense_employeeModel->voucher_no = $request->input('voucher_no');
        //}


        $expense_employeeModel->user_id = Auth::user()->id; //$request->input('user_id');
        $expense_employeeModel->expense_image = $asset_image;
        $expense_employeeModel->title = $request->input('title');
        $expense_employeeModel->created_at = date('Y-m-d H:i:s');
        $expense_employeeModel->created_ip = $request->ip();
        $expense_employeeModel->updated_at = date('Y-m-d h:i:s');
        $expense_employeeModel->updated_ip = $request->ip();
        $expense_employeeModel->updated_by = Auth::user()->id;

        $expense_employeeModel->expense_main_category = $request->input('expense_main_category');
        // dd($expense_employeeModel);
        if ($request->input('expense_main_category') == 'Site Expense' && $request->input('amount') <= 500) {

            $admin_role_user = User::where('role', config('constants.Admin'))->get();
            $expense_employeeModel->first_approval_status = 'Approved';
            $expense_employeeModel->second_approval_status = 'Approved';
            $expense_employeeModel->first_approval_id = $admin_role_user[0]->id;
            $expense_employeeModel->second_approval_id = $admin_role_user[0]->id;
            $expense_employeeModel->first_approval_datetime = date('Y-m-d h:i:s');
            $expense_employeeModel->secound_approval_datetime = date('Y-m-d h:i:s');
        }

        if ($expense_employeeModel->save()) {

            $cmpy_shrt = Companies::where('id', $request->input('company_id'))->value('company_short_name');
            $employee_code = Employees::where('user_id', Auth::user()->id)->value('emp_code');

            $expense_code = $cmpy_shrt . '/' . $employee_code . '/' . date('Y-m-d') . '/' . $expense_employeeModel->id;

            $expense_arr = [
                'expense_code' => $expense_code
            ];

            Employee_expense::where('id', $expense_employeeModel->id)->update($expense_arr);

            // Send Email to CMD
            if ($request->input('expense_main_category') == 'Site Expense' && $request->input('amount') <= 500) {
                $user_emails = User::where('role', config('constants.SuperUser'))->get(['email'])->pluck('email');
                
                $mail_data['to_email'] = $user_emails;
                $mail_data['from_name'] = User::whereId(Auth::user()->id)->value('name');
                $mail_data['amount'] = $request->get('amount');
                $mail_data['title'] = $request->get('title');
                $mail_data['bill_number'] = $request->get('bill_number');
                $mail_data['merchant_name'] = $request->get('merchant_name');
                $mail_data['expense_date'] = $request->get('expense_date');
                $this->common_task->expensesCMDEmail($mail_data);
            }

            //Voucher
            if (!empty($request->input('voucher_no'))) {
                $voucher_arr = [
                    'expense_id' => $expense_employeeModel->id,
                    'project_id' => $request->input('project_id'),
                    'client_id' => $request->input('client_id'),
                    'project_site_id' => $request->input('project_site_id'),
                    'issue_date' => date('Y-m-d H:i:s', strtotime($request->input('expense_date'))),
                    'user_id' => Auth::user()->id,
                    'is_used' => 'used',
                    'created_ip' => $request->ip(),
                    'updated_ip' => $request->ip(),
                ];
                VoucherNumberRegister::whereId($request->input('voucher_no'))->update($voucher_arr);
            }

            // User Action Log
            $company_name = Companies::whereId($request->input('company_id'))->value('company_name');
            $client_name = Clients::whereId($request->input('client_id'))->value('client_name');
            $project_name = Projects::whereId($request->input('project_id'))->value('project_name');
            $project_site = Project_sites::whereId($request->input('project_site_id'))->value('site_name');
            $expense_category = Expense_category::whereId($request->get('expense_category'))->value('category_name');
            $add_string = "<br> Company Name: " . $company_name . "<br> Client Name: " . $client_name . "<br> Project Name: " . $project_name . "<br> Site Name: " . $project_site . "<br> Expense Category: " . $expense_category . "<br> Title: " . $request->get('title') . "<br> Amount: " . $request->get('amount');
            $expense_code = Employee_expense::where('id', $expense_employeeModel->id)->value('expense_code');
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => "Expense code " . $expense_code . " added" . $add_string,
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            return redirect()->route('admin.employee_expense')->with('success', 'Expense Employee details added successfully.');
        } else {
            return redirect()->route('admin.employee_expense')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function edit_employee_expense($id) {
        $this->data['page_title'] = "Edit Employee Expense Details";
        $this->data['Expense_List'] = Expense_category::select('id', 'category_name')->orderBy('category_name')->get();
        $this->data['UsersName'] = User::select('id', 'name')->get();
        $this->data['Companies'] = Companies::orderBy('company_name')->select('id', 'company_name')->get();
        $this->data['Projects'] = Projects::select('id', 'project_name')->get();
        $this->data['expense_category_list'] = DB::table('employee_expense')
                ->select('employee_expense.*')
                ->where('employee_expense.id', $id)
                ->get();
        $check_result = Permissions::checkPermission(19, 2);
        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }

        if ($this->data['expense_category_list']->count() == 0) {
            return redirect()->route('admin.employee_expense')->with('error', 'Error Occurred. Try Again!');
        }

        if ($this->data['expense_category_list'][0]->first_approval_status == 'Approved' && $this->data['expense_category_list'][0]->status == 'Pending') {
            return redirect()->route('admin.employee_expense')->with('error', 'Expense already in process. You can not edit it now.');
        }

        return view('admin.employee_expense.edit_employee_expense', $this->data);
    }

    public function update_employee_expense(Request $request) {
        $validator_normal = Validator::make($request->all(), [
                    'expense_category' => 'required',
                    'project_type' => 'required',
                    'bill_number' => 'required',
                    'merchant_name' => 'required',
                    'amount' => 'required',
                    'expense_date' => 'required',
                    'comment' => 'required',
                    'project_id' => 'required',
                    'company_id' => 'required',
                    'client_id' => 'required',
                    'project_site_id' => 'required',
                    //'user_id' => 'required',
                    'title' => 'required',
        ]);

        if ($validator_normal->fails()) {
            return redirect()->route('admin.employee_expense')->with('error', 'Please follow validation rules.');
        }

        $Expense_expanse_id = $request->input('id');

        if ($request->input('project_id') == 1) {
            $other_project = $request->input('other_project');
        } else {
            $other_project = "";
        }
        // dd($request->all());

        $voucher_image = $request->get('voucher_image_old');
        if ($request->file('voucher_image')) {

            $asset_file = $request->file('voucher_image');

            $original_file_name = explode('.', $asset_file->getClientOriginalName());

            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);


            $file_path = $asset_file->storeAs('public/voucher_image', $new_file_name);
            if ($file_path) {
                $voucher_image = $file_path;
            }
        }
        $voucher_no = NULL;
        if (!empty($request->input('voucher_no'))) {
            $voucher_data = VoucherNumberRegister::whereId($request->input('voucher_no'))->first();
            if ($voucher_data) {
                $voucher_no = $voucher_data['voucher_no'];
            } else {
                $voucher_no = NULL;
            }
        }

        $Expense_category_arr = [
            //'user_id'          => $request->input('user_id'),
            'title' => $request->input('title'),
            'expense_category' => $request->input('expense_category'),
            'project_type' => $request->input('project_type'),
            'company_id' => $request->input('company_id'),
            'project_id' => $request->input('project_id'),
            'client_id' => $request->input('client_id'),
            'project_site_id' => $request->input('project_site_id'),
            'other_project' => $other_project,
            'bill_number' => $request->input('bill_number'),
            'merchant_name' => $request->input('merchant_name'),
            'amount' => $request->input('amount'),
            'expense_date' => date('Y-m-d H:i:s', strtotime($request->input('expense_date'))),
            'comment' => $request->input('comment'),
            'status' => 'Pending',
            'first_approval_status' => 'Pending',
            'second_approval_status' => 'Pending',
            'third_approval_status' => 'Pending',
            'forth_approval_status' => 'Pending',
            'fifth_approval_status' => 'Pending',
            'created_at' => date('Y-m-d h:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id,
            'expense_main_category' => $request->input('expense_main_category'),
            'repeat_execute' => 0,
            'voucher_ref_no' => $request->get('voucher_ref_no'),
            'voucher_id' => $request->get('voucher_no'),
            'voucher_image' => $voucher_image,
            'voucher_no' => $voucher_no,
            'voucher_repeat' => 0,
            'repeat_execute' => 0
        ];

        if ($request->input('expense_main_category') == 'Site Expense' && $request->input('amount') <= 500) {

            $admin_role_user = User::where('role', config('constants.Admin'))->get();
            $Expense_category_arr['first_approval_status'] = 'Approved';
            $Expense_category_arr['second_approval_status'] = 'Approved';
            $Expense_category_arr['first_approval_id'] = $admin_role_user[0]->id;
            $Expense_category_arr['second_approval_id'] = $admin_role_user[0]->id;
        }

        //start logic
        /* $old_voucher =  Employee_expense::where('id',$Expense_expanse_id)->value('voucher_no');
          $old_records = Employee_expense::where('voucher_no', $old_voucher)->get('id');
          if (!empty($request->input('voucher_no'))) {


          if ($old_voucher != $request->input('voucher_no')) {


          if ($old_records->count() <= 2) {
          foreach ($old_records as $key => $row) {
          Employee_expense::where('id',$row->id)->update(['voucher_repeat'=>0]);
          }
          }
          $find_records = Employee_expense::where('voucher_no', $request->input('voucher_no'))
          ->get();
          if ($find_records->count() > 0) {
          Employee_expense::where('voucher_no', $request->input('voucher_no'))->update(['voucher_repeat' => 1]);
          $Expense_category_arr['voucher_repeat'] = 1;
          } else {
          $Expense_category_arr['voucher_repeat'] = 0;
          }
          }
          $Expense_category_arr['voucher_no'] = $request->input('voucher_no');
          }else{

          if ($old_records->count() <= 2) {
          foreach ($old_records as $key => $row) {
          Employee_expense::where('id',$row->id)->update(['voucher_repeat'=>0]);
          }
          }else{
          $Expense_category_arr['voucher_repeat'] = 0;
          }
          $Expense_category_arr['voucher_no'] = NULL;
          } */
        //end logic


        /* if ($request->hasFile('image')) {
          $profile_image = $request->file('image');
          $file_path = $profile_image->store('public/expense_image');
          if ($file_path) {
          $Expense_category_arr['expense_image'] = $file_path;
          }
          } */

        //21-02-2020
        if ($request->file('image')) {

            $asset_file = $request->file('image');

            $original_file_name = explode('.', $asset_file->getClientOriginalName());

            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);


            $file_path = $asset_file->storeAs('public/expense_image', $new_file_name);
            if ($file_path) {
                $Expense_category_arr['expense_image'] = $file_path;
            }
        }

        Employee_expense::where('id', $Expense_expanse_id)->update($Expense_category_arr);


        //Voucher
        if (!empty($request->input('voucher_no'))) {
            $voucher_arr = [
                'expense_id' => $Expense_expanse_id,
                'project_id' => $request->input('project_id'),
                'client_id' => $request->input('client_id'),
                'project_site_id' => $request->input('project_site_id'),
                'issue_date' => date('Y-m-d H:i:s', strtotime($request->input('expense_date'))),
                'is_used' => 'used',
                'user_id' => Auth::user()->id,
                'created_ip' => $request->ip(),
                'updated_ip' => $request->ip(),
            ];
            VoucherNumberRegister::whereId($request->input('voucher_no'))->update($voucher_arr);
        }
        if (!empty($request->get('voucher_id_old'))) {
            $voucher_arr = [
                'expense_id' => NULL,
                'project_id' => NULL,
                'client_id' => NULL,
                'project_site_id' => NULL,
                'issue_date' => NULL,
                'user_id' => NULL,
                'is_used' => 'not_used',
                'created_ip' => $request->ip(),
                'updated_ip' => $request->ip(),
            ];
            VoucherNumberRegister::whereId($request->input('voucher_id_old'))->update($voucher_arr);
        }

        // User Action Log
        $company_name = Companies::whereId($request->input('company_id'))->value('company_name');
        $client_name = Clients::whereId($request->input('client_id'))->value('client_name');
        $project_name = Projects::whereId($request->input('project_id'))->value('project_name');
        $project_site = Project_sites::whereId($request->input('project_site_id'))->value('site_name');
        $expense_category = Expense_category::whereId($request->get('expense_category'))->value('category_name');
        $add_string = "<br> Company Name: " . $company_name . "<br> Client Name: " . $client_name . "<br> Project Name: " . $project_name . "<br> Site Name: " . $project_site . "<br> Expense Category: " . $expense_category . "<br> Title: " . $request->get('title') . "<br> Amount: " . $request->get('amount');
        $expense_code = Employee_expense::where('id', $Expense_expanse_id)->value('expense_code');
        $action_data = [
            'user_id' => Auth::user()->id,
            'task_body' => "Expense code " . $expense_code . " updated" . $add_string,
            'created_ip' => $request->ip(),
        ];
        $this->user_action_logs->action($action_data);

        return redirect()->route('admin.employee_expense')->with('success', 'Expense employee details successfully updated.');
    }

    public function change_employee_expense($id, $status) {
        $check_result = Permissions::checkPermission(19, 2);

        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }
        if (Employee_expense::where('id', $id)->update(['status' => $status, 'approved_by' => Auth::user()->id])) {
            return redirect()->route('admin.employee_expense')->with('success', 'Status successfully updated.');
        }
        return redirect()->route('admin.employee_expense')->with('error', 'Error during operation. Try again!');
    }

    public function delete_employee_expense($id) {

        $check_result = Permissions::checkPermission(19, 4);

        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }

        $expense_detail = Employee_expense::where('id', $id)->where('user_id', \Illuminate\Support\Facades\Auth::user()->id)->get();

        if ($expense_detail->count() == 0) {
            return redirect()->route('admin.employee_expense')->with('error', 'Error occurred. Try Again!');
        }

        if ($expense_detail[0]->first_approval_status == 'Approved') {
            return redirect()->route('admin.employee_expense')->with('error', 'Expense is in approval process. You can not delete it now.');
        }

        if ($expense_detail[0]->status == 'Rejected') {
            return redirect()->route('admin.employee_expense')->with('error', 'Expense is already rejected. You can not delete it now.');
        }

        $emp_exp = Employee_expense::where('id', $id)->first();
        if (!empty($emp_exp['voucher_ref_no']) && !empty($emp_exp['voucher_id'])) {
            $voucher_arr = [
                'expense_id' => NULL,
                'project_id' => NULL,
                'client_id' => NULL,
                'project_site_id' => NULL,
                'issue_date' => NULL,
                'user_id' => NULL,
                'is_used' => 'not_used'
            ];
            VoucherNumberRegister::whereId($emp_exp['voucher_id'])->update($voucher_arr);
        }

        // User Action Log
        $expense_code = Employee_expense::where('id', $id)->value('expense_code');
        $action_data = [
            'user_id' => Auth::user()->id,
            'task_body' => "Expense code " . $expense_code . " deleted",
            'created_ip' => \Request::ip(),
        ];
        $this->user_action_logs->action($action_data);

        if (Employee_expense::where('id', $id)->delete()) {
            return redirect()->route('admin.employee_expense')->with('success', 'Expense successfully deleted.');
        }
        return redirect()->route('admin.employee_expense')->with('error', 'Error during operation. Try again!');
    }

    public function paid_employee_expense($id) {
        $check_result = Permissions::checkPermission(19, 5);

        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }

        $employeeExpense = [
            'payment_status' => 'Yes',
        ];
        if (Employee_expense::where('id', $id)->update($employeeExpense)) {
            return redirect()->route('admin.employee_expense')->with('success', 'Paid successfully updated.');
        }
        return redirect()->route('admin.employee_expense')->with('error', 'Error during operation. Try again!');
    }

    public function employee_expense_list() {   //this
        $this->data['page_title'] = "Approve Employee Expense";
        $access_level = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => 19])->get()->first();
        $this->data['access_rule'] = '';
        if (!empty($access_level)) {
            $this->data['access_rule'] = $access_level->access_level;
        }

        $Result = DB::table('employee_expense')
                ->select('employee_expense.*', 'clients.client_name', 'clients.location', 'project_sites.site_name', 'expense_category.category_name', 'acc_users.name as acc_user_name', 'acc_users.email as acc_email', 'users.name', 'company.company_name', 'project.project_name')
                ->join('users', 'employee_expense.user_id', '=', 'users.id')
                ->leftJoin('users as acc_users', 'acc_users.id', '=', 'employee_expense.fifth_approval_id')
                ->join('expense_category', 'employee_expense.expense_category', '=', 'expense_category.id')
                ->join('company', 'company.id', '=', 'employee_expense.company_id')
                ->leftJoin('clients', 'clients.id', '=', 'employee_expense.client_id')
                ->leftJoin('project_sites', 'project_sites.id', '=', 'employee_expense.project_site_id')
                ->join('project', 'project.id', '=', 'employee_expense.project_id');
        if (Auth::user()->role == config('constants.REAL_HR')) {
            $Result->where('employee_expense.first_approval_status', 'Pending');
        } elseif (Auth::user()->role == config('constants.ASSISTANT')) {
            $Result->where('employee_expense.first_approval_status', 'Approved')
                    ->where('employee_expense.second_approval_status', 'Pending');
            //$Result->where('employee_expense.first_approval_status','Approved')->where('employee_expense.second_approval_status','Pending');
        } elseif (Auth::user()->role == config('constants.Admin')) {
            $Result->where('employee_expense.first_approval_status', 'Approved')
                    ->where('employee_expense.second_approval_status', 'Approved')
                    ->where('employee_expense.third_approval_status', 'Pending');
            //$Result->where('employee_expense.first_approval_status','Approved')->where('employee_expense.second_approval_status','Pending');
        } elseif (Auth::user()->role == config('constants.SuperUser')) {
            $Result->where('employee_expense.first_approval_status', 'Approved')
                    ->where('employee_expense.second_approval_status', 'Approved')
                    ->where('employee_expense.third_approval_status', 'Approved')
                    ->where('employee_expense.forth_approval_status', 'Pending');
            //$Result->where('employee_expense.first_approval_status','Approved')->where('employee_expense.second_approval_status','Pending');
        } elseif (Auth::user()->role == config('constants.ACCOUNT_ROLE')) {
            $Result->where('employee_expense.first_approval_status', 'Approved')
                    ->where('employee_expense.second_approval_status', 'Approved')
                    ->where('employee_expense.third_approval_status', 'Approved')
                    ->where('employee_expense.forth_approval_status', 'Approved')
                    ->where('employee_expense.fifth_approval_status', 'Pending');
            //$Result->where('employee_expense.first_approval_status','Approved')->where('employee_expense.second_approval_status','Approved')->where('employee_expense.third_approval_status','Pending');;
        } else {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You can not access this module.');
        }

        // dd($Result->get()->toArray());

        $this->data['employee_expense_list'] = $Result->get();

        /* $Results = DB::table('employee_expense')
          ->select('employee_expense.*','rtgs_register.rtgs_no','clients.client_name','clients.location', 'project_sites.site_name','cheque_register.ch_no', 'bank.bank_name', 'bank.ac_number', 'expense_category.category_name', 'acc_users.name as acc_user_name', 'acc_users.email as acc_email', 'users.name', 'company.company_name', 'project.project_name')
          ->join('users', 'employee_expense.user_id', '=', 'users.id') //
          ->leftJoin('users as acc_users', 'acc_users.id', '=', 'employee_expense.fifth_approval_id')//
          ->join('expense_category', 'employee_expense.expense_category', '=', 'expense_category.id')//
          ->join('company', 'company.id', '=', 'employee_expense.company_id')//
          ->leftJoin('cheque_register', 'employee_expense.cheque_number', '=', 'cheque_register.id')//
          ->leftJoin('bank', 'bank.id', '=', 'employee_expense.bank_id')//
          ->leftJoin('clients', 'clients.id', '=', 'employee_expense.client_id')//
          ->leftjoin('rtgs_register','rtgs_register.id','=','employee_expense.rtgs_number')//
          ->leftJoin('project_sites', 'project_sites.id', '=', 'employee_expense.project_site_id')//
          ->join('project', 'project.id', '=', 'employee_expense.project_id');//

          $this->data['all_employee_expense_list'] = $Results->get(); */


        return view('admin.employee_expense.employee_expense_list', $this->data);
    }

    //=================================================================================================


    public function all_employee_expense_list_ajax() {   //this
        $datatable_fields = array('employee_expense.expense_code',
            'users.name',
            'company.company_name', 'clients.client_name',
            'project.project_name', 'employee_expense.other_project',
            'project_sites.site_name',
            'employee_expense.expense_main_category',
            'expense_category.category_name',
            'employee_expense.title', 'employee_expense.merchant_name',
            'employee_expense.amount', 'employee_expense.bill_number',
            'employee_expense.expense_date', 'employee_expense.comment', 'employee_expense.expense_image',
            'bank.bank_name', 'cheque_register.ch_no', 'rtgs_register.rtgs_no',
            'employee_expense.voucher_no', 'employee_expense.voucher_image', 'employee_expense.transaction_note',
            'employee_expense.first_approval_status', 'employee_expense.first_approval_datetime', 'employee_expense.second_approval_status', 'employee_expense.secound_approval_datetime',
            'employee_expense.third_approval_status', 'employee_expense.third_approval_datetime', 'employee_expense.forth_approval_status', 'employee_expense.fourth_approval_datetime',
            'employee_expense.fifth_approval_status', 'employee_expense.fifth_approval_datetime', 'employee_expense.status',
            'acc_users.name');

        $request = Input::all();

        //$request = [];
        $conditions_array = [];

        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'users';
        $join_str[0]['join_table_id'] = 'users.id';
        $join_str[0]['from_table_id'] = 'employee_expense.user_id';

        $join_str[1]['join_type'] = 'left';
        $join_str[1]['table'] = 'users as acc_users';
        $join_str[1]['join_table_id'] = 'acc_users.id';
        $join_str[1]['from_table_id'] = 'employee_expense.fifth_approval_id';

        $join_str[2]['join_type'] = '';
        $join_str[2]['table'] = 'expense_category';
        $join_str[2]['join_table_id'] = 'expense_category.id';
        $join_str[2]['from_table_id'] = 'employee_expense.expense_category';


        $join_str[3]['join_type'] = '';
        $join_str[3]['table'] = 'company';
        $join_str[3]['join_table_id'] = 'company.id';
        $join_str[3]['from_table_id'] = 'employee_expense.company_id';

        $join_str[4]['join_type'] = 'left';
        $join_str[4]['table'] = 'cheque_register';
        $join_str[4]['join_table_id'] = 'cheque_register.id';
        $join_str[4]['from_table_id'] = 'employee_expense.cheque_number';

        $join_str[5]['join_type'] = 'left';
        $join_str[5]['table'] = 'bank';
        $join_str[5]['join_table_id'] = 'bank.id';
        $join_str[5]['from_table_id'] = 'employee_expense.bank_id';

        $join_str[6]['join_type'] = 'left';
        $join_str[6]['table'] = 'clients';
        $join_str[6]['join_table_id'] = 'clients.id';
        $join_str[6]['from_table_id'] = 'employee_expense.client_id';

        $join_str[7]['join_type'] = 'left';
        $join_str[7]['table'] = 'rtgs_register';
        $join_str[7]['join_table_id'] = 'rtgs_register.id';
        $join_str[7]['from_table_id'] = 'employee_expense.rtgs_number';

        $join_str[8]['join_type'] = 'left';
        $join_str[8]['table'] = 'project_sites';
        $join_str[8]['join_table_id'] = 'project_sites.id';
        $join_str[8]['from_table_id'] = 'employee_expense.project_site_id';

        $join_str[9]['join_type'] = '';
        $join_str[9]['table'] = 'project';
        $join_str[9]['join_table_id'] = 'project.id';
        $join_str[9]['from_table_id'] = 'employee_expense.project_id';


        $getfiled = array('employee_expense.*', 'rtgs_register.rtgs_no', 'clients.client_name', 'clients.location', 'project_sites.site_name', 'cheque_register.ch_no', 'bank.bank_name', 'bank.ac_number', 'expense_category.category_name', 'acc_users.name as acc_user_name', 'acc_users.email as acc_email', 'users.name', 'company.company_name', 'project.project_name');
        $table = "employee_expense";

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);

        die();
    }

    //====================================================================================================

    public function approve_employee_expense($id, Request $request) {

        $expenseModel = Employee_expense::find($id);
        $userDetail = User::where(['id' => $expenseModel->user_id])->get(['email', 'name'])->first()->toArray();
        $update_arr = [];
        $final_confirm = 0;
        //check expense approval-time, first, second or third

        if (Auth::user()->role == config('constants.REAL_HR')) {
            //if amount is upto 500 then directly approve by hr for all and directly send to account department
            if ($expenseModel->amount <= 500) {
                $update_arr = [
                    'first_approval_status' => 'Approved',
                    'first_approval_id' => Auth::user()->id,
                    'second_approval_status' => 'Approved',
                    'second_approval_id' => Auth::user()->id,
                    'third_approval_status' => 'Approved',
                    'third_approval_id' => Auth::user()->id,
                    'forth_approval_status' => 'Approved',
                    'forth_approval_id' => Auth::user()->id,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                    'updated_by' => Auth::user()->id,
                    'first_approval_datetime' => date('Y-m-d H:i:s'),
                    'secound_approval_datetime' => date('Y-m-d H:i:s'),
                    'third_approval_datetime' => date('Y-m-d H:i:s'),
                    'fourth_approval_datetime' => date('Y-m-d H:i:s'),
                    // This above 5 field are recently added start from here
                ];
                ///get accountant user list
                $accountant_list = \App\User::where('role', config('constants.ACCOUNT_ROLE'))->get(['id'])->pluck('id')->toArray();
                $this->notification_task->employeeExepenseForthApprovalNotify($accountant_list);
            } else {
                $update_arr = [
                    'first_approval_status' => 'Approved',
                    'first_approval_id' => Auth::user()->id,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                    'updated_by' => Auth::user()->id,
                    'first_approval_datetime' => date('Y-m-d H:i:s'),
                ];
                $assistant_detail = \App\User::where('role', config('constants.ASSISTANT'))->get(['id', 'email']);
                if ($assistant_detail->count() > 0) {
                    $this->notification_task->employeeExepenseFirstApprovalNotify($assistant_detail->pluck('id')->toArray());
                }
            }
        } elseif (Auth::user()->role == config('constants.ASSISTANT')) {
            $update_arr = [
                'second_approval_status' => 'Approved',
                'second_approval_id' => Auth::user()->id,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id,
                'secound_approval_datetime' => date('Y-m-d H:i:s'),
                
            ];

            $admin_user = \App\User::where('role', config('constants.Admin'))->get(['id'])->pluck('id')->toArray();
            $this->notification_task->employeeExepenseSecondApprovalNotify($admin_user);
        } elseif (Auth::user()->role == config('constants.Admin')) {
            if ($expenseModel->amount <= 500) {
                $update_arr = [
                    'third_approval_status' => 'Approved',
                    'third_approval_id' => Auth::user()->id,
                    'forth_approval_status' => 'Approved',
                    'forth_approval_id' => Auth::user()->id,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                    'updated_by' => Auth::user()->id,
                    'third_approval_datetime' => date('Y-m-d H:i:s'),
                    'fourth_approval_datetime' => date('Y-m-d H:i:s'),
                ];
                $accountant_list = \App\User::where('role', config('constants.ACCOUNT_ROLE'))->get(['id'])->pluck('id')->toArray();
                $this->notification_task->employeeExepenseForthApprovalNotify($accountant_list);
            } else {
                $update_arr = [
                    'third_approval_status' => 'Approved',
                    'third_approval_id' => Auth::user()->id,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                    'updated_by' => Auth::user()->id,
                    'third_approval_datetime' => date('Y-m-d H:i:s'),
                ];
                $this->notification_task->employeeExepenseThirdApprovalNotify([$this->super_admin->id]);
            }
        } elseif (Auth::user()->role == config('constants.SuperUser')) {
            $update_arr = [
                'forth_approval_status' => 'Approved',
                'forth_approval_id' => Auth::user()->id,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id,
                'fourth_approval_datetime' => date('Y-m-d H:i:s'),
            ];

            ///get accountant user list
            $accountant_list = \App\User::where('role', config('constants.ACCOUNT_ROLE'))->get(['id'])->pluck('id')->toArray();
            $this->notification_task->employeeExepenseForthApprovalNotify($accountant_list);
        } elseif (Auth::user()->role == config('constants.ACCOUNT_ROLE')) {
            $update_arr = [
                'fifth_approval_status' => 'Approved',
                'fifth_approval_id' => Auth::user()->id,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id,
                'status' => "Approved",
                'fifth_approval_datetime' => date('Y-m-d H:i:s'),
            ];

            if($expenseModel->expense_main_category == 'Site Expense'){
                $employee_cach = Employee_cash_management::where('employee_id',$expenseModel->user_id)->first();
                if($employee_cach != null){
                    $employee_cach->decrement('balance',$expenseModel->amount);
                }
            }
            
            $this->notification_task->employeeExepenseApprovedNotify([$expenseModel->user_id]);
        } else {
            return redirect()->route('admin.employee_expense_list')->with('error', 'Error Occurred. Try Again!');
        }

        if (Employee_expense::where('id', $id)->update($update_arr)) {

            // User Action Log
            $expense_code = Employee_expense::where('id', $id)->value('expense_code');
            $expense_amount = Employee_expense::where('id', $id)->value('amount');
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => "Expense code " . $expense_code . " approved <br> Amount: " . $expense_amount,
                'created_ip' => \Request::ip(),
            ];
            $this->user_action_logs->action($action_data);

            return redirect()->route('admin.employee_expense_list')->with('success', 'Expense successfully Approved.');
        }
        return redirect()->route('admin.employee_expense_list')->with('error', 'Error during operation. Try again!');
    }

    public function approve_employee_expence_multiple(Request $request) {
        $expense_list = $request->input('expense_approve_list');

        if (empty($expense_list)) {
            return redirect()->route('admin.employee_expense_list')->with('error', 'Please select expenses you want to approve.');
        }

        foreach ($expense_list as $id) {
            $expenseModel = Employee_expense::find($id);
            if (empty($expenseModel)) {
                continue;
            }
            $userDetail = User::where(['id' => $expenseModel->user_id])->get(['email', 'name'])->first()->toArray();
            $update_arr = [];
            $final_confirm = 0;
            //check expense approval-time, first, second or third

            if (Auth::user()->role == config('constants.REAL_HR')) {
                //if amount is upto 500 then directly approve by hr for all and directly send to account department
                if ($expenseModel->amount <= 500) {
                    $update_arr = [
                        'first_approval_status' => 'Approved',
                        'first_approval_id' => Auth::user()->id,
                        'second_approval_status' => 'Approved',
                        'second_approval_id' => Auth::user()->id,
                        'third_approval_status' => 'Approved',
                        'third_approval_id' => Auth::user()->id,
                        'forth_approval_status' => 'Approved',
                        'forth_approval_id' => Auth::user()->id,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_ip' => $request->ip(),
                        'updated_by' => Auth::user()->id,
                        'first_approval_datetime' => date('Y-m-d H:i:s'),
                        'secound_approval_datetime' => date('Y-m-d H:i:s'),
                        'third_approval_datetime' => date('Y-m-d H:i:s'),
                        'fourth_approval_datetime' => date('Y-m-d H:i:s'),
                    ];
                    ///get accountant user list
                    $accountant_list = \App\User::where('role', config('constants.ACCOUNT_ROLE'))->get(['id'])->pluck('id')->toArray();
                    $this->notification_task->employeeExepenseForthApprovalNotify($accountant_list);
                } else {
                    $update_arr = [
                        'first_approval_status' => 'Approved',
                        'first_approval_id' => Auth::user()->id,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_ip' => $request->ip(),
                        'updated_by' => Auth::user()->id,
                        'first_approval_datetime' => date('Y-m-d H:i:s'),
                    ];
                    $assistant_detail = \App\User::where('role', config('constants.ASSISTANT'))->get(['id', 'email']);
                    if ($assistant_detail->count() > 0) {
                        $this->notification_task->employeeExepenseFirstApprovalNotify($assistant_detail->pluck('id')->toArray());
                    }
                }
            } elseif (Auth::user()->role == config('constants.ASSISTANT')) {
                $update_arr = [
                    'second_approval_status' => 'Approved',
                    'second_approval_id' => Auth::user()->id,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                    'updated_by' => Auth::user()->id,
                    'secound_approval_datetime' => date('Y-m-d H:i:s'),
                ];

                $admin_user = \App\User::where('role', config('constants.Admin'))->get(['id'])->pluck('id')->toArray();
                $this->notification_task->employeeExepenseSecondApprovalNotify($admin_user);
            } elseif (Auth::user()->role == config('constants.Admin')) {
                if ($expenseModel->amount <= 500) {
                    $update_arr = [
                        'third_approval_status' => 'Approved',
                        'third_approval_id' => Auth::user()->id,
                        'forth_approval_status' => 'Approved',
                        'forth_approval_id' => Auth::user()->id,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_ip' => $request->ip(),
                        'updated_by' => Auth::user()->id,
                        'third_approval_datetime' => date('Y-m-d H:i:s'),
                        'fourth_approval_datetime' => date('Y-m-d H:i:s'),
                    ];
                    $accountant_list = \App\User::where('role', config('constants.ACCOUNT_ROLE'))->get(['id'])->pluck('id')->toArray();
                    $this->notification_task->employeeExepenseForthApprovalNotify($accountant_list);
                } else {
                    $update_arr = [
                        'third_approval_status' => 'Approved',
                        'third_approval_id' => Auth::user()->id,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_ip' => $request->ip(),
                        'updated_by' => Auth::user()->id,
                        'third_approval_datetime' => date('Y-m-d H:i:s'),
                    ];
                    $this->notification_task->employeeExepenseThirdApprovalNotify([$this->super_admin->id]);
                }
            } elseif (Auth::user()->role == config('constants.SuperUser')) {
                $update_arr = [
                    'forth_approval_status' => 'Approved',
                    'forth_approval_id' => Auth::user()->id,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                    'updated_by' => Auth::user()->id,
                    'fourth_approval_datetime' => date('Y-m-d H:i:s'),
                ];

                ///get accountant user list
                $accountant_list = \App\User::where('role', config('constants.ACCOUNT_ROLE'))->get(['id'])->pluck('id')->toArray();
                $this->notification_task->employeeExepenseForthApprovalNotify($accountant_list);
            } elseif (Auth::user()->role == config('constants.ACCOUNT_ROLE')) {
                $update_arr = [
                    'fifth_approval_status' => 'Approved',
                    'fifth_approval_id' => Auth::user()->id,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                    'updated_by' => Auth::user()->id,
                    'status' => "Approved",
                    'fifth_approval_datetime' => date('Y-m-d H:i:s'),
                ];

                $this->notification_task->employeeExepenseApprovedNotify([$expenseModel->user_id]);
            } else {
                continue;
            }

            Employee_expense::where('id', $id)->update($update_arr);

            // User Action Log
            $expense_code = Employee_expense::where('id', $id)->value('expense_code');
            $expense_amount = Employee_expense::where('id', $id)->value('amount');
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => "Expense code " . $expense_code . " approved <br> Amount: " . $expense_amount,
                'created_ip' => \Request::ip(),
            ];
            $this->user_action_logs->action($action_data);
        }
        return redirect()->route('admin.employee_expense_list')->with('success', 'Employee expenses successfully approved.');
    }

    public function reject_emp_expense($id) {
        // this will be access with full view access 5 only
        $this->data['page_title'] = "Expense";

        $this->data['id'] = $id;
        return view('admin.employee_expense.reject_expense', $this->data);
    }

    public function reject_employee_expense(Request $request) {

        $validator_normal = Validator::make($request->all(), [
                    'reject_reason' => 'required',
                    'id' => 'required'
        ]);

        if ($validator_normal->fails()) {
            return redirect()->route('admin.employee_expense_list')->with('error', 'Please follow validation rules.');
        }

        $expenseModel = Employee_expense::find($request->input('id'));

        if (Auth::user()->role == config('constants.REAL_HR')) {
            $update_arr = [
                'first_approval_status' => 'Rejected',
                'first_approval_id' => Auth::user()->id,
                'status' => 'Rejected',
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id,
                'reject_reason' => $request->input('reject_reason')
            ];
        } elseif (Auth::user()->role == config('constants.ASSISTANT')) {
            $update_arr = [
                'second_approval_status' => 'Rejected',
                'second_approval_id' => Auth::user()->id,
                'status' => 'Rejected',
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id,
                'reject_reason' => $request->input('reject_reason')
            ];
        } elseif (Auth::user()->role == config('constants.Admin')) {
            $update_arr = [
                'third_approval_status' => 'Rejected',
                'third_approval_id' => Auth::user()->id,
                'status' => 'Rejected',
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id,
                'reject_reason' => $request->input('reject_reason')
            ];
        } elseif (Auth::user()->role == config('constants.SuperUser')) {
            $update_arr = [
                'forth_approval_status' => 'Rejected',
                'forth_approval_id' => Auth::user()->id,
                'status' => 'Rejected',
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id,
                'reject_reason' => $request->input('reject_reason')
            ];
        } elseif (Auth::user()->role == config('constants.ACCOUNT_ROLE')) {
            $update_arr = [
                'fifth_approval_status' => 'Rejected',
                'fifth_approval_id' => Auth::user()->id,
                'status' => 'Rejected',
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id,
                'reject_reason' => $request->input('reject_reason')
            ];
        } else {
            return redirect()->route('admin.employee_expense_list')->with('error', 'Error Occurred. Try Again!');
        }

        Employee_expense::where('id', $request->input('id'))->update($update_arr);

        // User Action Log
        $expense_code = Employee_expense::where('id', $request->input('id'))->value('expense_code');
        $expense_amount = Employee_expense::where('id', $request->input('id'))->value('amount');
        $action_data = [
            'user_id' => Auth::user()->id,
            'task_body' => "Expense code " . $expense_code . " rejected <br> Amount: " . $expense_amount,
            'created_ip' => \Request::ip(),
        ];
        $this->user_action_logs->action($action_data);

        $this->notification_task->employeeExepenseRejectNotify([$expenseModel->user_id]);
        return redirect()->route('admin.employee_expense_list')->with('success', 'Expense successfully rejected.');
    }

    public function get_expense_project_list() {
        if (!empty($_GET['company_id'])) {
            $company_id = $_GET['company_id'];
            $project_data = Projects::select('project_name', 'id')->where(['company_id' => $company_id])->orWhere(['company_id' => 0])->get()->toArray();
            $html = "<option value='0'>Select Project</option>";
            foreach ($project_data as $key => $project_data_value) {
                $html .= "<option value=" . $project_data_value['id'] . ">" . $project_data_value['project_name'] . "</option>";
            }
            echo $html;
            die();
        }
    }

    public function get_expense(Request $request) {  //12-03-2020
        $validator_normal = Validator::make($request->all(), ['id' => 'required']);

        if ($validator_normal->fails()) {
            return response()->json(['status' => false]);
        }

        $expense_id = $request->id;
        $expense_records = Employee_expense::leftjoin('rtgs_register', 'rtgs_register.id', '=', 'employee_expense.rtgs_number')
                        ->where('employee_expense.id', $expense_id)->get(['rtgs_register.rtgs_no', 'employee_expense.cheque_number', 'employee_expense.rtgs_number', 'employee_expense.voucher_no', 'employee_expense.transaction_note', 'employee_expense.company_id', 'employee_expense.bank_id']);

        $bank_data = Banks::select('bank_name', 'ac_number', 'id')
                ->where('company_id', '=', $expense_records[0]->company_id)
                ->where('status', '=', 'Enabled')
                ->get();
        $this->data['bank_data'] = $bank_data;

        $this->data['expense_records'] = $expense_records;

        if ($expense_records) {
            return response()->json(['status' => true, 'data' => $this->data]);
        } else {
            return response()->json(['status' => false]);
        }
    }

    public function banks_cheque_rtgs_reff_list(Request $request) {
        $bank_id = $request->bank_id;
        $company_id = $request->company_id;
        $cheque_data = \App\ChequeRegister::select('check_ref_no', 'id')->where(['bank_id' => $bank_id])
                        ->where(['company_id' => $company_id])
                        ->where(['is_used' => 'not_used'])
                        ->where(['is_failed' => '0'])
                        ->groupBy('check_ref_no')
                        ->get()->toArray();
        // dd($cheque_data);

        $rtgs_data = \App\RtgsRegister::select('rtgs_ref_no', 'id')->where(['bank_id' => $bank_id])
                        ->where(['company_id' => $company_id])
                        ->where(['is_used' => 'not_used'])
                        ->where(['is_failed' => '0'])
                        ->groupBy('rtgs_ref_no')
                        ->get()->toArray();

        if ($cheque_data) {
            $html = "<option value=''>Select Cheque Reff Number</option>";
            foreach ($cheque_data as $key => $value) {
                $html .= '<option value="' . $value['check_ref_no'] . '">' . $value['check_ref_no'] . '</option>';
            }
            $this->data['cheque_reff_list'] = $html;
        } else {
            $this->data['cheque_reff_list'] = "<option value=''>Select Cheque Reff Number</option>";
        }

        if ($rtgs_data) {
            $html = "<option value=''>Select Rtgs Reff Number</option>";
            foreach ($rtgs_data as $key => $value) {
                $html .= '<option value=" ' . $value['rtgs_ref_no'] . ' ">' . $value['rtgs_ref_no'] . '</option>';
            }
            $this->data['rtgs_reff_list'] = $html;
        } else {
            $this->data['rtgs_reff_list'] = "<option value=''>Select Rtgs Reff Number</option>";
        }

        return response()->json(['status' => true, 'data' => $this->data]);
    }

    public function banks_cheque_list(Request $request) {

        /* $bank_id = $request->bank_id;
          $company_id = $request->company_id;

          $cheque_list =  ChequeRegister::select('ch_no', 'id')->where('bank_id', '=', $bank_id)
          ->get();

          return response()->json($cheque_list); */

        $bank_id = $request->bank_id;
        $company_id = $request->company_id;
        $check_ref_no = $request->check_ref_no;

        $cheque_data = \App\ChequeRegister::select('ch_no', 'id')
                        ->where(['bank_id' => $bank_id])
                        ->where(['company_id' => $company_id])
                        ->where(['check_ref_no' => $check_ref_no])
                        ->where(['is_used' => 'not_used'])
                        ->where(['is_failed' => '0'])
                        ->get()->toArray();
        $html = "<option value=''>Select cheque</option>";

        foreach ($cheque_data as $key => $cheque_data_value) {
            $html .= "<option value=" . $cheque_data_value['id'] . ">" . $cheque_data_value['ch_no'] . "</option>";
        }
        echo $html;
        die();
    }

    public function approve_employee_expenseByAccountant(Request $request) {   //12-03-2020

        $validator_normal = Validator::make($request->all(), [
                    'id' => 'required'
                        // 'transaction_note' => 'required'
        ]);

        if ($validator_normal->fails()) {
            return redirect()->route('admin.employee_expense_list')->with('error', 'Please follow validation rules.');
        }

        $expense_id = $request->input('id');
        $this->employee_cash_transfer($expense_id);
        // dd($request->all());
        $update_arr = [
            'bank_id' => $request->input('bank_id'),
            'cheque_number' => $request->input('cheque_number'),
            'check_ref_no' => $request->input('check_ref_no'),
            'rtgs_ref_no' => $request->input('rtgs_ref_no'),
            'rtgs_number' => $request->input('rtgs_number'),
            // 'voucher_no' => $request->input('voucher_no'),
            'transaction_note' => $request->input('transaction_note'),
            'fifth_approval_status' => 'Approved',
            'fifth_approval_id' => Auth::user()->id,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id,
            'status' => "Approved",
            'fifth_approval_datetime' => date('Y-m-d H:i:s'),
        ];

        if (Employee_expense::where('id', $expense_id)->update($update_arr)) {

            $expenseModel = Employee_expense::find($expense_id);

            $this->notification_task->employeeExepenseApprovedNotify([$expenseModel->user_id]);

            $user_ids = User::where('status', 'Enabled')->whereIn('role', [config('constants.HR_ROLE'), config('constants.ASSISTANT'), config('constants.Admin'), config('constants.SuperUser')])->pluck('id')->toArray();

            $this->notification_task->exepensApprovedNotifyFlow($user_ids, $expenseModel->expense_code);

            return redirect()->route('admin.employee_expense_list')->with('success', 'Expense successfully Approved.');
        }
        return redirect()->route('admin.employee_expense_list')->with('error', 'Error during operation. Try again!');
    }

    public function employee_cash_transfer($expense_id) {
        $expense = Employee_expense::whereId($expense_id)->first();

        $emp_cash = Employee_cash_management::where('employee_id', $expense['user_id'])->first();
        if ($emp_cash) {
            if ($emp_cash['balance'] >= $expense['amount']) {
                $before_amount = $emp_cash['balance'];
                $after_amount = $emp_cash['balance'] - $expense['amount'];
                // dd($expense);
                $emp_cash_transfer_arr = [
                    'account_id' => $expense['user_id'],
                    'account_type' => 'employee',
                    'project_id' => $expense['project_id'],
                    'balance' => $expense['amount'],
                    'transfer_type' => 'debit',
                    'entry_type' => 'expense',
                    'txn_before_balance' => $before_amount,
                    'txn_after_balance' => $after_amount,
                    'user_id' => auth()->user()->id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'created_ip' => \Request::ip(),
                    'updated_ip' => \Request::ip(),
                    'updated_by' => auth()->user()->id,
                ];
                Cash_transfer::insert($emp_cash_transfer_arr);

                $emp_cash->balance = $after_amount;
                $emp_cash->save();
                // dd($emp_cash_transfer_arr);
            }
        }
    }

    public function get_voucher_ref_number(Request $request) {

        $login_id = Auth::user()->id;
        $vouchers = AssignedVoucher::where('to_user_id', $login_id)->where('status', 'accepted')->pluck('voucher_ref_no');

        $html = '<option value="">Select Voucher Ref Number</option>';
        if ($vouchers) {
            $request_data = $request->all();
            if(isset($request_data['voucher_id'])){
                $all_voucher = VoucherNumberRegister::select('*')
                            ->where('company_id', $request->get('company_id'))
                            // ->where('client_id',$request->get('client_id'))
                            // ->where('project_id',$request->get('project_id'))
                            // ->where('project_site_id',$request->get('project_site_id'))
                            ->where('is_failed', 0)
                            ->where('is_used', 'not_used')
                            ->orWhere('id', $request->get('voucher_id'))
                            ->whereIn('voucher_ref_no', $vouchers)
                            ->groupBy('voucher_ref_no')
                            ->get()->toArray();
            }else{
                $all_voucher = VoucherNumberRegister::select('*')
                            ->where('company_id', $request->get('company_id'))
                            // ->where('client_id',$request->get('client_id'))
                            // ->where('project_id',$request->get('project_id'))
                            // ->where('project_site_id',$request->get('project_site_id'))
                            ->where('is_failed', 0)
                            ->where('is_used', 'not_used')
                            ->whereIn('voucher_ref_no', $vouchers)
                            ->groupBy('voucher_ref_no')
                            ->get()->toArray();
            }

            

            if ($all_voucher) {
                foreach ($all_voucher as $key => $value) {
                    $html .= '<option value="' . $value['voucher_ref_no'] . '">' . $value['voucher_ref_no'] . '</option>';
                }
            }
        }
        echo $html;
        die;
    }

    public function get_voucher_number(Request $request) {
        // dd($request->all());
        if ($request->get('voucher_id_old') && $request->get('voucher_ref_no') == $request->get('voucher_ref_no_old')) {
            $all_voucher = VoucherNumberRegister::select('*')
                            // ->whereNotIn('id',[$request->get('voucher_id_old')])
                            ->where('voucher_ref_no', $request->get('voucher_ref_no'))
                            ->where('is_used', 'not_used')
                            ->where('is_failed', 0)
                            ->orWhere('id', $request->get('voucher_id_old'))
                            ->orderBy('voucher_no', 'asc')
                            ->get()->toArray();
        } else {
            $all_voucher = VoucherNumberRegister::select('*')
                            ->where('voucher_ref_no', $request->get('voucher_ref_no'))
                            ->where('is_failed', 0)
                            ->where('is_used', 'not_used')
                            ->orderBy('voucher_no', 'asc')
                            ->get()->toArray();
        }


        $html = '<option value="">Select Voucher Number</option>';
        if ($all_voucher) {
            foreach ($all_voucher as $key => $value) {
                $html .= '<option value="' . $value['id'] . '">' . $value['voucher_no'] . '</option>';
            }
        }
        echo $html;
        die;
    }

    //08/09/2020
    public function get_loginuser_project_list(Request $request) {
        $request_data = $request->all();
        $client_id = $request->client_id;
        $project_type = $request->project_type == NULL ? 'Current' : $request->project_type;
        if ($client_id) {
            $partial_query = Projects::select(['project.id', 'project.project_name'])
                    ->leftjoin('project_manager', 'project_manager.project_id', '=', 'project.id')
                    ->where(function ($query) use ($client_id) {
                $query->where('project.client_id', $client_id);
                $query->orWhere('project.client_id', 1);
            });

            if (isset($request_data['expense_type']) && $request_data['expense_type'] == 'Site Expense') {

                $partial_query->where('project_manager.user_id', Auth::user()->id);
            }

            $projects = $partial_query->where('project.status', 'Enabled')
                    ->where('project.project_type', $project_type)
                    ->groupBy('project.id')
                    ->orderBy('project_name', 'asc')
                    ->get();
            //  dd($projects->toArray());
            return response()->json($projects);
        } else {
            return response()->json([]);
        }
    }

}
