<?php

namespace App\Http\Controllers\Api;

use App\Companies;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Lib\CommonTask;
use Illuminate\Support\Facades\Validator;
use App\Employee_expense;
use App\Expense_category;
use Exception;
use Illuminate\Support\Facades\Storage;
use App\Driver_expense;
use App\Employees;
use App\Banks;
use App\User;
use App\Clients;
use App\Project_sites;
use App\Projects;
use App\ChequeRegister;
use App\Lib\NotificationTask;
use App\RtgsRegister;
use App\AssignedVoucher;
use App\VoucherNumberRegister;
use App\Employee_cash_management;
use App\Cash_transfer;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{

    private $page_limit = 20;
    public $common_task;
    private $module_id = 19;
    private $notification_task;
    private $super_admin;

    public function __construct()
    {
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();
        $this->super_admin = \App\User::where('role', config('constants.SuperUser'))->first();
    }

    public function get_expense_category(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $expense_category = Expense_category::where('status', 'Enabled')->get(['id', 'category_name']);

        if ($expense_category->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }
        $response_data = $expense_category;
        return response()->json(['status' => true, 'msg' => "Record Found.", 'data' => $response_data]);
    }


    public function company_bank_list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'company_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $bank_data = Banks::select('bank_name', 'ac_number', 'id')
            ->where('company_id', '=', $request_data['company_id'])
            ->where('status', '=', 'Enabled')
            ->get();

        if ($bank_data->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        $response_data = $bank_data;

        return response()->json(['status' => true, 'msg' => "Record Found.", 'data' => $response_data]);
    }

    public function bank_cheques_list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'bank_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $cheque_list =  ChequeRegister::select('ch_no', 'id')->where('bank_id', '=', $request_data['bank_id'])
            ->get();

        if ($cheque_list->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }
        $response_data = $cheque_list;
        return response()->json(['status' => true, 'msg' => "Record Found.", 'data' => $response_data]);
    }

    public function bank_rtgs_list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'bank_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $rtgs_list =  RtgsRegister::select('rtgs_no', 'id')->where('bank_id', '=', $request_data['bank_id'])
        ->where(['is_used' => 'not_used'])
            ->get();

        if ($rtgs_list->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }
        $response_data = $rtgs_list;
        return response()->json(['status' => true, 'msg' => "Record Found.", 'data' => $response_data]);
    }


    public function get_my_expense(Request $request)  //change
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'page_number' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $offset = ($request_data['page_number'] - 1) * $this->page_limit;

        $expense_select = ['clients.client_name','clients.location', 'project_sites.site_name','rtgs_register.rtgs_no',
            'employee_expense.id', 'employee_expense.expense_code', 'employee_expense.user_id', 'employee_expense.expense_category as expense_category_id', 'expense_category.category_name', 'employee_expense.title',
            'employee_expense.bill_number', 'employee_expense.merchant_name', 'employee_expense.amount',
            'employee_expense.expense_date', 'employee_expense.comment', 'employee_expense.voucher_no', 'employee_expense.voucher_repeat',
            'employee_expense.expense_image', 'employee_expense.status', 'users.name', 'employee_expense.reject_reason','employee_expense.client_id','employee_expense.project_site_id',
            'project.project_name', 'company.company_name', 'other_project', 'employee_expense.project_id', 'employee_expense.company_id', 'employee_expense.expense_main_category',
            'employee_expense.cheque_number','employee_expense.project_type','employee_expense.bank_id', 'employee_expense.rtgs_number', 'employee_expense.transaction_note','cheque_register.ch_no','bank.bank_name','bank.ac_number', 'employee_expense.voucher_id', 'employee_expense.voucher_ref_no', 'employee_expense.voucher_image',
        ];
        $expense_list = Employee_expense::join('expense_category', 'expense_category.id', '=', 'employee_expense.expense_category')
            ->leftJoin('users', 'users.id', '=', 'employee_expense.approved_by')
            ->leftJoin('bank', 'bank.id', '=', 'employee_expense.bank_id')
            ->leftJoin('clients', 'clients.id', '=', 'employee_expense.client_id')
            ->leftjoin('rtgs_register','rtgs_register.id','=','employee_expense.rtgs_number')
            ->leftJoin('project_sites', 'project_sites.id', '=', 'employee_expense.project_site_id')
            ->leftJoin('cheque_register', 'cheque_register.id', '=', 'employee_expense.cheque_number')
            ->join('company', 'company.id', '=', 'employee_expense.company_id')
            ->join('project', 'project.id', '=', 'employee_expense.project_id')
            ->where(['employee_expense.user_id' => $request_data['user_id']])
            ->offset($offset)->limit($this->page_limit)
            ->orderBy('employee_expense.id', 'DESC')
            ->get($expense_select);

        if ($expense_list->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        foreach ($expense_list as $key => $expense) {


            if($expense->client_name == "Other Client"){
                $expense_list[$key]->client_name = $expense->client_name;
            }else{
                $expense_list[$key]->client_name = $expense->client_name. "(" . $expense->location . ")";
            }


            if ($expense['expense_image']) {
                $expense_list[$key]['expense_image'] = asset('storage/' . str_replace('public/', '', $expense['expense_image']));
            } else {
                $expense_list[$key]['expense_image'] = "";
            }

            if ($expense['voucher_image']) {
                $expense_list[$key]['voucher_image'] = asset('storage/' . str_replace('public/', '', $expense['voucher_image']));
            } else {
                $expense_list[$key]['voucher_image'] = "";
            }
        }

        $response_data['expense_list'] = $expense_list;
        return response()->json(['status' => true, 'msg' => "Record Found", 'data' => $response_data]);
    }

    public function add_expense(Request $request)  //change
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'category_id' => 'required',
            'title' => 'required',
            //'bill_number'=>'required',
            //'merchant_name'=>'required',
            'amount' => 'required',
            'expense_date' => 'required',
            'comment' => 'required',
            'company_id' => 'required',
            'project_id' => 'required',
            'client_id' => 'required',
            'project_site_id' => 'required',
            'expense_main_category' => 'required',
            'project_type' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];
        // dd($request_data);
        //21-02-2020
        $expense_image_file = "";
        if ($request->file('expense_image')) {

            $asset_file = $request->file('expense_image');

            $original_file_name = explode('.', $asset_file->getClientOriginalName());

            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);


            $file_path = $asset_file->storeAs('public/expense_image', $new_file_name);
            if ($file_path) {
                $expense_image_file = $file_path;
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
        $voucher_no = NULL;
        $voucher_ref_no = NULL;
        if(!empty($request_data['voucher_no']) && $request_data['expense_main_category'] == "Site Expense"){
            $voucher_data = VoucherNumberRegister::whereId($request->input('voucher_no'))->first();
            if($voucher_data){
                $voucher_no = $voucher_data['voucher_no'];
            }

            $voucher_ref_no = $request_data['voucher_ref_no'];
        }

        $insert_arr = [
            "user_id" => $request_data['user_id'],
            "expense_category" => $request_data['category_id'],
            "title" => $request_data['title'],
            "bill_number" => $request_data['bill_number'],
            "merchant_name" => $request_data['merchant_name'],
            "amount" => $request_data['amount'],
            "expense_date" => $request_data['expense_date'],
            "comment" => $request_data['comment'],
            "voucher_no" => $voucher_no,
            "expense_image" => $expense_image_file,
            "created_at" => date('Y-m-d H:i:s'),
            "updated_at" => date('Y-m-d H:i:s'),
            "created_ip" => $request->ip(),
            "updated_ip" => $request->ip(),
            "updated_by" => $request_data['user_id'],
            "company_id" => $request_data['company_id'],
            "project_id" => $request_data['project_id'],
            'client_id' => $request_data['client_id'],
            'project_site_id' => $request_data['project_site_id'],
            'expense_main_category' => $request_data['expense_main_category'],
            'project_type' => $request_data['project_type'],
            'voucher_ref_no' => $voucher_ref_no,
            'voucher_id' => $request_data['voucher_no'],
            'voucher_image' => $voucher_image,
        ];

        // dd($insert_arr);

        if ($request_data['project_id'] == config('constants.OTHER_PROJECT_ID')) {
            $insert_arr['other_project'] = $request_data['other_project'];
        }

        if ($request_data['expense_main_category'] == 'Site Expense' && $request_data['amount'] <= 500) {

            $admin_role_user = \App\User::where('role', config('constants.Admin'))->get();
            $insert_arr['first_approval_status'] = 'Approved';
            $insert_arr['second_approval_status'] = 'Approved';
            $insert_arr['first_approval_id'] = $admin_role_user[0]->id;
            $insert_arr['second_approval_id'] = $admin_role_user[0]->id;
        }

        /*  if (!empty($request->input('voucher_no'))) {
            $voucher_no = $request->input('voucher_no');
            $find_records = Employee_expense::where('voucher_no', $voucher_no)->get();
            if ($find_records->count() > 0) {
                Employee_expense::where('voucher_no', $voucher_no)->update(['voucher_repeat' => 1]);
                $insert_arr['voucher_repeat'] = 1;
            } else {
                $insert_arr['voucher_repeat'] = 0;
            }
            $insert_arr['voucher_no'] = $request->input('voucher_no');
        } */


        try {
            $last_insertId = Employee_expense::insertGetId($insert_arr);

            $cmpy_shrt = Companies::where('id', $request_data['company_id'])->value('company_short_name');
            $employee_code = Employees::where('user_id', $request_data['user_id'])->value('emp_code');

            $expense_code = $cmpy_shrt . '/' . $employee_code . '/' . date('Y-m-d') . '/' . $last_insertId;

            $expense_arr = [
                'expense_code' => $expense_code
            ];

            Employee_expense::where('id', $last_insertId)->update($expense_arr);


            //Voucher
            if(!empty($request_data['voucher_no']) && $request_data['expense_main_category'] == "Site Expense"){
                $voucher_arr = [
                    'expense_id' => $last_insertId,
                    'project_id' => $request_data['project_id'],
                    'client_id' => $request_data['client_id'],
                    'project_site_id' => $request_data['project_site_id'],
                    'issue_date' => $request_data['expense_date'],
                    'user_id' => $request_data['user_id'],
                    'is_used' => 'used',
                    'created_ip' => $request->ip(),
                    'updated_ip' => $request->ip(),
                ];
                VoucherNumberRegister::whereId($request_data['voucher_no'])->update($voucher_arr);
            }

            return response()->json(['status' => true, 'msg' => "Expense successfully submitted for approval.", 'data' => []]);
        } catch (Exception $exc) {
            return response()->json(['status' => false, 'msg' => config('errors.sql_operation.msg'), 'data' => [], 'error' => config('errors.sql_operation.code')]);
        }
    }

    public function get_all_expense(Request $request)   //change
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'page_number' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];
        $offset = ($request_data['page_number'] - 1) * $this->page_limit;

        //get user's permission - user with my view only permission will not come in this function
        $user_role = $this->common_task->getUserRole($request_data['user_id']);

        $role_permission = $this->common_task->getPermissionArr($user_role, $this->module_id);

        $expense_select = ['clients.client_name','clients.location', 'project_sites.site_name','rtgs_register.rtgs_no',
            'employee_expense.id', 'employee_expense.expense_code', 'employee_expense.user_id', 'expense_category.category_name', 'employee_expense.expense_category as expense_category_id', 'employee_expense.title',
            'employee_expense.bill_number', 'employee_expense.merchant_name', 'employee_expense.amount',
            'employee_expense.expense_date', 'employee_expense.comment', 'employee_expense.voucher_no', 'employee_expense.voucher_repeat',
            'employee_expense.expense_image', 'employee_expense.status', 'users.name', 'users.profile_image',
            'employee_expense.other_project', 'project.project_name', 'company.company_name', 'employee_expense.expense_main_category','employee_expense.voucher_image',
            'employee_expense.cheque_number','employee_expense.bank_id', 'employee_expense.rtgs_number', 'employee_expense.transaction_note','cheque_register.ch_no','bank.bank_name','bank.ac_number'
        ];

        if ($user_role == config('constants.REAL_HR')) {
            $expense_list = Employee_expense::join('expense_category', 'expense_category.id', '=', 'employee_expense.expense_category')
                ->leftJoin('users', 'users.id', '=', 'employee_expense.user_id')
                ->leftJoin('employee', 'employee.user_id', '=', 'employee_expense.user_id')
                ->leftJoin('bank', 'bank.id', '=', 'employee_expense.bank_id')
                ->leftjoin('rtgs_register','rtgs_register.id','=','employee_expense.rtgs_number')
                ->leftJoin('cheque_register', 'cheque_register.id', '=', 'employee_expense.cheque_number')
                ->join('company', 'company.id', '=', 'employee_expense.company_id')
                ->leftJoin('clients', 'clients.id', '=', 'employee_expense.client_id')
                ->leftJoin('project_sites', 'project_sites.id', '=', 'employee_expense.project_site_id')
                ->join('project', 'project.id', '=', 'employee_expense.project_id')
                ->where('first_approval_status', 'Pending')
                ->offset($offset)->limit($this->page_limit)
                ->orderBy('id', 'DESC')
                ->get($expense_select);
        } elseif ($user_role == config('constants.ASSISTANT')) {
            $expense_list = Employee_expense::join('expense_category', 'expense_category.id', '=', 'employee_expense.expense_category')
                ->leftJoin('users', 'users.id', '=', 'employee_expense.user_id')
                ->leftJoin('employee', 'employee.user_id', '=', 'employee_expense.user_id')
                ->leftJoin('bank', 'bank.id', '=', 'employee_expense.bank_id')
                ->leftjoin('rtgs_register','rtgs_register.id','=','employee_expense.rtgs_number')
                ->leftJoin('cheque_register', 'cheque_register.id', '=', 'employee_expense.cheque_number')
                ->join('company', 'company.id', '=', 'employee_expense.company_id')
                ->join('project', 'project.id', '=', 'employee_expense.project_id')
                ->leftJoin('clients', 'clients.id', '=', 'employee_expense.client_id')
                ->leftJoin('project_sites', 'project_sites.id', '=', 'employee_expense.project_site_id')
                ->where('first_approval_status', 'Approved')
                ->where('second_approval_status', 'Pending')
                ->offset($offset)->limit($this->page_limit)
                ->orderBy('id', 'DESC')
                ->get($expense_select);
        } elseif ($user_role == config('constants.Admin')) {
            $expense_list = Employee_expense::join('expense_category', 'expense_category.id', '=', 'employee_expense.expense_category')
                ->leftJoin('users', 'users.id', '=', 'employee_expense.user_id')
                ->leftJoin('employee', 'employee.user_id', '=', 'employee_expense.user_id')
                ->leftJoin('bank', 'bank.id', '=', 'employee_expense.bank_id')
                ->leftjoin('rtgs_register','rtgs_register.id','=','employee_expense.rtgs_number')
                ->leftJoin('cheque_register', 'cheque_register.id', '=', 'employee_expense.cheque_number')
                ->join('company', 'company.id', '=', 'employee_expense.company_id')
                ->join('project', 'project.id', '=', 'employee_expense.project_id')
                ->leftJoin('clients', 'clients.id', '=', 'employee_expense.client_id')
                ->leftJoin('project_sites', 'project_sites.id', '=', 'employee_expense.project_site_id')
                ->where('first_approval_status', 'Approved')
                ->where('second_approval_status', 'Approved')
                ->where('third_approval_status', 'Pending')
                ->offset($offset)->limit($this->page_limit)
                ->orderBy('id', 'DESC')
                ->get($expense_select);
        } elseif ($user_role == config('constants.SuperUser')) {
            $expense_list = Employee_expense::join('expense_category', 'expense_category.id', '=', 'employee_expense.expense_category')
                ->leftJoin('users', 'users.id', '=', 'employee_expense.user_id')
                ->leftJoin('employee', 'employee.user_id', '=', 'employee_expense.user_id')
                ->leftJoin('bank', 'bank.id', '=', 'employee_expense.bank_id')
                ->leftjoin('rtgs_register','rtgs_register.id','=','employee_expense.rtgs_number')
                ->leftJoin('cheque_register', 'cheque_register.id', '=', 'employee_expense.cheque_number')
                ->join('company', 'company.id', '=', 'employee_expense.company_id')
                ->join('project', 'project.id', '=', 'employee_expense.project_id')
                ->leftJoin('clients', 'clients.id', '=', 'employee_expense.client_id')
                ->leftJoin('project_sites', 'project_sites.id', '=', 'employee_expense.project_site_id')
                ->where('first_approval_status', 'Approved')
                ->where('second_approval_status', 'Approved')
                ->where('third_approval_status', 'Approved')
                ->where('forth_approval_status', 'Pending')
                ->offset($offset)->limit($this->page_limit)
                ->orderBy('id', 'DESC')
                ->get($expense_select);
        } elseif ($user_role == config('constants.ACCOUNT_ROLE')) {
            $expense_list = Employee_expense::join('expense_category', 'expense_category.id', '=', 'employee_expense.expense_category')
                ->leftJoin('users', 'users.id', '=', 'employee_expense.user_id')
                ->leftJoin('employee', 'employee.user_id', '=', 'employee_expense.user_id')
                ->leftJoin('bank', 'bank.id', '=', 'employee_expense.bank_id')
                ->leftjoin('rtgs_register','rtgs_register.id','=','employee_expense.rtgs_number')
                ->leftJoin('cheque_register', 'cheque_register.id', '=', 'employee_expense.cheque_number')
                ->join('company', 'company.id', '=', 'employee_expense.company_id')
                ->join('project', 'project.id', '=', 'employee_expense.project_id')
                ->leftJoin('clients', 'clients.id', '=', 'employee_expense.client_id')
                ->leftJoin('project_sites', 'project_sites.id', '=', 'employee_expense.project_site_id')
                ->where('first_approval_status', 'Approved')
                ->where('second_approval_status', 'Approved')
                ->where('third_approval_status', 'Approved')
                ->where('forth_approval_status', 'Approved')
                ->where('fifth_approval_status', 'Pending')
                ->offset($offset)->limit($this->page_limit)
                ->orderBy('id', 'DESC')
                ->get($expense_select);
        } else {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }

        if ($expense_list->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        foreach ($expense_list as $key => $expense) {
            //$expense_list[$key]['expense_image'] = asset('storage/' . str_replace('public/', '', $expense['expense_image']));


            if($expense->client_name == "Other Client"){
                $expense_list[$key]->client_name = $expense->client_name;
            }else{
                $expense_list[$key]->client_name = $expense->client_name. "(" . $expense->location . ")";
            }


            if ($expense['expense_image']) {
                $expense_list[$key]['expense_image'] = asset('storage/' . str_replace('public/', '', $expense['expense_image']));
            } else {
                $expense_list[$key]['expense_image'] = "";
            }

            if ($expense->profile_image) {
                $expense_list[$key]->profile_image = asset('storage/' . str_replace('public/', '', $expense->profile_image));
            } else {
                $expense_list[$key]->profile_image = "";
            }

            if ($expense->voucher_image) {
                $expense_list[$key]->voucher_image = asset('storage/' . str_replace('public/', '', $expense->voucher_image));
            } else {
                $expense_list[$key]->voucher_image = "";
            }
        }

        $response_data['expense_list'] = $expense_list;
        return response()->json(['status' => true, 'msg' => "Record Found", 'data' => $response_data]);
    }

    /*
     * Do not call this api if expense status is Approved or Rejected
     * Right now only owner user can delete the expense
     */

    public function delete_expense(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'expense_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        //check if user has created this expense?
        $expense_user_check = Employee_expense::where(['user_id' => $request_data['user_id'], 'id' => $request_data['expense_id']])->get();;
        if ($expense_user_check->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }

        if ($expense_user_check[0]->status == "Approved" || $expense_user_check[0]->status == "Rejected") {
            return response()->json(['status' => false, 'msg' => "Expense is already {$expense_user_check[0]->status}. You can not delete this record now from system.", 'data' => [], 'error' => config('errors.general_error.code')]);
        }

        if (!empty($expense_user_check[0]->voucher_id)) {
            $voucher_arr = [
                'expense_id' => NULL,
                'project_id' => NULL,
                'client_id' => NULL,
                'project_site_id' => NULL,
                'issue_date' => NULL,
                'user_id' => NULL,
                'is_used' => 'not_used'
            ];
            VoucherNumberRegister::whereId($expense_user_check[0]->voucher_id)->update($voucher_arr);
        }

        Employee_expense::where(['id' => $request_data['expense_id']])->delete();
        return response()->json(['status' => true, 'msg' => "Expense deleted successfully.", 'data' => []]);
    }

    /*
     * Do not call this api if expense status is Approved
     * Right now only owner user can delete the expense
     * System will automatically set status resubmit if it is rejected first time
     */

    public function edit_expense(Request $request)  //change
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'expense_id' => 'required',
            'expense_category' => 'required',
            'title' => 'required',
            'bill_number' => 'required',
            'merchant_name' => 'required',
            'amount' => 'required',
            'expense_date' => 'required',
            'comment' => 'required',
            'is_resubmit' => 'required',
            'company_id' => 'required',
            'project_id' => 'required',
            'client_id' => 'required',
            'project_site_id' => 'required',
            'expense_main_category' => 'required',
            'project_type' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];
        // dd($request_data);
        //check if expense is not approved
        $expense_check = Employee_expense::where('id', $request_data['expense_id'])
            ->get();

        if ($expense_check->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.general_error.msg'), 'data' => [], 'error' => config('errors.general_error.code')]);
        }

        if ($expense_check[0]->first_approval_status == 'Approved' && $expense_check[0]->status == 'Pending') {
            return response()->json(['status' => false, 'msg' => "Expense already in process. You can not edit it now.", 'data' => [], 'error' => config('errors.general_error.code')]);
        }

        if ($expense_check[0]->status == "Approved") {
            return response()->json(['status' => false, 'msg' => "Expense is already approved. You can not update it.", 'data' => [], 'error' => config('errors.general_error.code')]);
        }

        $voucher_no = NULL;
        $voucher_ref_no = NULL;
        if(!empty($request_data['voucher_no']) && $request_data['expense_main_category'] == "Site Expense"){
            $voucher_data = VoucherNumberRegister::whereId($request->input('voucher_no'))->first();
            if($voucher_data){
                $voucher_no = $voucher_data['voucher_no'];
            }

            $voucher_ref_no = $request_data['voucher_ref_no'];
        }

        $expense_arr = [
            'expense_category' => $request_data['expense_category'],
            'title' => $request_data['title'],
            'bill_number' => $request_data['bill_number'],
            'merchant_name' => $request_data['merchant_name'],
            'amount' => $request_data['amount'],
            'expense_date' => $request_data['expense_date'],
            'comment' => $request_data['comment'],
            'voucher_no' => $voucher_no,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => $request_data['user_id'],
            "company_id" => $request_data['company_id'],
            "project_id" => $request_data['project_id'],
            'client_id' => $request_data['client_id'],
            'project_site_id' => $request_data['project_site_id'],
            'expense_main_category' => $request_data['expense_main_category'],
            'project_type' => $request_data['project_type'],
            'repeat_execute' => 0,
            'voucher_ref_no' => $voucher_ref_no,
            'voucher_id' => $request_data['voucher_no'],
        ];

        if ($request_data['project_id'] == config('constants.OTHER_PROJECT_ID')) {
            $expense_arr['other_project'] = $request_data['other_project'];
        }

        //logic for voucher column
        /*   $old_voucher =  Employee_expense::where('id',$request_data['expense_id'])->value('voucher_no');
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
                        $expense_arr['voucher_repeat'] = 1;
                    } else {
                        $expense_arr['voucher_repeat'] = 0;
                    }
            }
            $expense_arr['voucher_no'] = $request->input('voucher_no');

        }else{

            if ($old_records->count() <= 2) {
                foreach ($old_records as $key => $row) {
                    Employee_expense::where('id',$row->id)->update(['voucher_repeat'=>0]);
                    $expense_arr['voucher_repeat'] = 0;
                }
            }else{
                $expense_arr['voucher_repeat'] = 0;
            }
            $expense_arr['voucher_no'] = NULL;
        } */
        //End logic



        //set status
        if ($request_data['is_resubmit'] == "Yes") {
            $expense_arr['status'] = "Pending";
            $expense_arr['second_approval_status'] = "Pending";
            $expense_arr['first_approval_status'] = "Pending";
            $expense_arr['third_approval_status'] = "Pending";
            $expense_arr['forth_approval_status'] = "Pending";
            $expense_arr['fifth_approval_status'] = "Pending";
        } else {
            $expense_arr['status'] = "Pending";
        }

        if ($request_data['expense_main_category'] == 'Site Expense' && $request_data['amount'] <= 500) {

            $admin_role_user = \App\User::where('role', config('constants.Admin'))->get();
            $expense_arr['first_approval_status'] = 'Approved';
            $expense_arr['second_approval_status'] = 'Approved';
            $expense_arr['first_approval_id'] = $admin_role_user[0]->id;
            $expense_arr['second_approval_id'] = $admin_role_user[0]->id;
        }

        /*     $image_path = "";
          if ($request->hasFile('expense_image')) {
          $expense_image = $request->file('expense_image');
          $file_path = $expense_image->store('public/expense_image');

          if ($file_path) {
          $expense_arr['expense_image'] = $file_path;
          //delete old file
          if ($expense_check[0]->expense_image != "") {
          Storage::delete($expense_check[0]->expense_image);
          }
          $image_path = $file_path;
          }
          } else {
          $image_path = $expense_check[0]->expense_image;
          } */


        //21-02-2020
        if ($request->file('expense_image')) {

            $asset_file = $request->file('expense_image');

            $original_file_name = explode('.', $asset_file->getClientOriginalName());

            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);


            $file_path = $asset_file->storeAs('public/expense_image', $new_file_name);

            if ($file_path) {
                $expense_arr['expense_image'] = $file_path;
                //delete old file
                if ($expense_check[0]->expense_image != "") {
                    Storage::delete($expense_check[0]->expense_image);
                }
                $image_path = $file_path;
            }
        } else {
            $image_path = $expense_check[0]->expense_image;
        }

        $voucher_id_old = $expense_check[0]->voucher_id;

        $expense_arr['voucher_image'] = $expense_check[0]->voucher_image;
        if ($request->file('voucher_image')) {

            $asset_file = $request->file('voucher_image');

            $original_file_name = explode('.', $asset_file->getClientOriginalName());

            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);


            $file_path = $asset_file->storeAs('public/voucher_image', $new_file_name);
            if ($file_path) {
                $expense_arr['voucher_image'] = $file_path;
                }
        }
        // dd($expense_arr);
        Employee_expense::where('id', $request_data['expense_id'])->update($expense_arr);
        $expense_arr['expense_image'] = asset('storage/' . str_replace('public/', '', $image_path));
        $expense_arr['voucher_image'] = asset('storage/' . str_replace('public/', '', $expense_arr['voucher_image']));
        $response_data['expense_updated_data'] = $expense_arr;

        //Voucher
        if(!empty($request_data['voucher_no']) && $request_data['expense_main_category'] == "Site Expense"){
            $voucher_arr = [
                'expense_id' => $request_data['expense_id'],
                'project_id' => $request_data['project_id'],
                'client_id' => $request_data['client_id'],
                'project_site_id' => $request_data['project_site_id'],
                'issue_date' => $request_data['expense_date'],
                'user_id' => $request_data['user_id'],
                'is_used' => 'used',
                'created_ip' => $request->ip(),
                'updated_ip' => $request->ip(),
            ];
            VoucherNumberRegister::whereId($request_data['voucher_no'])->update($voucher_arr);
        }
        //remove voucher
        if($voucher_id_old != $request_data['voucher_no']){
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
            VoucherNumberRegister::whereId($voucher_id_old)->update($voucher_arr);
        }

        return response()->json(['status' => true, 'msg' => "Expense updated successfuly and submitted.", 'data' => $response_data]);
    }

    public function approve_reject_expense(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'expense_id' => 'required',
            'status' => 'required',
            //'reject_reason' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        //check permission for approval
        $user_role = $this->common_task->getUserRole($request_data['user_id']);
        $role_permission = $this->common_task->getPermissionArr($user_role, $this->module_id);

        //get expense detail
        $expense_detail = Employee_expense::where('id', $request_data['expense_id'])->get();

        if ($expense_detail->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.general_error.msg'), 'data' => [], 'error' => config('errors.general_error.code')]);
        }

        //user can not approve his own expense condition
        /* if (($request_data['user_id'] == $expense_detail[0]->user_id) && $user_role != 1) {
          return response()->json(['status' => false, 'msg' => config('errors.general_error.msg'), 'data' => [], 'error' => config('errors.general_error.code')]);
          } */

        if ($request_data['status'] == 'Approved') {
            if ($user_role == config('constants.REAL_HR')) {
                //if amount is upto 500 then directly approve by hr for all and directly send to account department
                if ($expense_detail[0]->amount <= 500) {
                    $update_arr = [
                        'first_approval_status' => 'Approved',
                        'first_approval_id' => $request_data['user_id'],
                        'second_approval_status' => 'Approved',
                        'second_approval_id' => $request_data['user_id'],
                        'third_approval_status' => 'Approved',
                        'third_approval_id' => $request_data['user_id'],
                        'forth_approval_status' => 'Approved',
                        'forth_approval_id' => $request_data['user_id'],
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_ip' => $request->ip(),
                        'updated_by' => $request_data['user_id']
                    ];

                    $account_user = \App\User::where('role', config('constants.ACCOUNT_ROLE'))->get(['id'])->pluck('id')->toArray();
                    $this->notification_task->employeeExepenseForthApprovalNotify($account_user);
                } else {
                    $update_arr = [
                        'first_approval_status' => 'Approved',
                        'first_approval_id' => $request_data['user_id'],
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_ip' => $request->ip(),
                        'updated_by' => $request_data['user_id']
                    ];

                    //get details of assistant user to send notification
                    $assistant_detail = \App\User::where('role', config('constants.ASSISTANT'))->get(['id', 'email']);
                    if ($assistant_detail->count() > 0) {
                        $this->notification_task->employeeExepenseFirstApprovalNotify($assistant_detail->pluck('id')->toArray());
                    }
                }
            } elseif ($user_role == config('constants.ASSISTANT')) {
                $update_arr = [
                    'second_approval_status' => 'Approved',
                    'second_approval_id' => $request_data['user_id'],
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                    'updated_by' => $request_data['user_id']
                ];
                $admin_user = \App\User::where('role', config('constants.Admin'))->get(['id'])->pluck('id')->toArray();
                $this->notification_task->employeeExepenseSecondApprovalNotify($admin_user);
            } elseif ($user_role == config('constants.Admin')) {
                if ($expense_detail[0]->amount <= 500) {
                    $update_arr = [
                        'third_approval_status' => 'Approved',
                        'third_approval_id' => $request_data['user_id'],
                        'forth_approval_status' => 'Approved',
                        'forth_approval_id' => $request_data['user_id'],
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_ip' => $request->ip(),
                        'updated_by' => $request_data['user_id'],
                    ];
                    $account_user = \App\User::where('role', config('constants.ACCOUNT_ROLE'))->get(['id'])->pluck('id')->toArray();
                    $this->notification_task->employeeExepenseForthApprovalNotify($account_user);
                } else {
                    $update_arr = [
                        'third_approval_status' => 'Approved',
                        'third_approval_id' => $request_data['user_id'],
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_ip' => $request->ip(),
                        'updated_by' => $request_data['user_id'],
                    ];
                    $this->notification_task->employeeExepenseThirdApprovalNotify([$this->super_admin->id]);
                }
            } elseif ($user_role == config('constants.SuperUser')) {
                $update_arr = [
                    'forth_approval_status' => 'Approved',
                    'forth_approval_id' => $request_data['user_id'],
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                    'updated_by' => $request_data['user_id']
                ];
                $account_user = \App\User::where('role', config('constants.ACCOUNT_ROLE'))->get(['id'])->pluck('id')->toArray();
                $this->notification_task->employeeExepenseForthApprovalNotify($account_user);
            } elseif ($user_role == config('constants.ACCOUNT_ROLE')) {
                $this->employee_cash_transfer($request_data['expense_id'], $request_data['user_id']);
                // dd($request_data);
                $update_arr = [

                    'bank_id' => isset($request_data['bank_id']) ? $request_data['bank_id'] : 0,
                    'cheque_number' => isset($request_data['cheque_number']) ? $request_data['cheque_number'] : 0,
                    'rtgs_number' => isset($request_data['rtgs_number']) ? $request_data['rtgs_number'] : NULL,
                    'voucher_no' => isset($request_data['voucher_no']) ? $request_data['voucher_no'] : NULL,
                    'check_ref_no' => isset($request_data['check_ref_no']) ? $request_data['check_ref_no'] : NULL,
                    'rtgs_ref_no' => isset($request_data['rtgs_ref_no']) ? $request_data['rtgs_ref_no'] : NULL,
                    'transaction_note' => isset($request_data['transaction_note']) ? $request_data['transaction_note'] : NULL,

                    'fifth_approval_status' => 'Approved',
                    'fifth_approval_id' => $request_data['user_id'],
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                    'updated_by' => $request_data['user_id'],
                    'status' => 'Approved',
                    'approved_by' => $request_data['user_id']
                ];

                $this->notification_task->employeeExepenseApprovedNotify([$expense_detail[0]->user_id]);

                $user_ids =  User::where('status', 'Enabled')->whereIn('role', [config('constants.HR_ROLE'), config('constants.ASSISTANT'),config('constants.Admin'),  config('constants.SuperUser')])->pluck('id')->toArray();

                $this->notification_task->exepensApprovedNotifyFlow($user_ids, $expense_detail[0]->expense_code );

            } else {
                return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
            }
        } else {
            if ($user_role == config('constants.REAL_HR')) {
                $update_arr = [
                    'first_approval_status' => 'Rejected',
                    'first_approval_id' => $request_data['user_id'],
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                    'updated_by' => $request_data['user_id'],
                    'status' => 'Rejected',
                    'reject_reason' => $request_data['reject_reason'],
                    'approved_by' => $request_data['user_id']
                ];
            } elseif ($user_role == config('constants.ASSISTANT')) {
                $update_arr = [
                    'second_approval_status' => 'Rejected',
                    'second_approval_id' => $request_data['user_id'],
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                    'updated_by' => $request_data['user_id'],
                    'status' => 'Rejected',
                    'reject_reason' => $request_data['reject_reason'],
                    'approved_by' => $request_data['user_id']
                ];
            } elseif ($user_role == config('constants.Admin')) {
                $update_arr = [
                    'third_approval_status' => 'Rejected',
                    'third_approval_id' => $request_data['user_id'],
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                    'updated_by' => $request_data['user_id'],
                    'status' => 'Rejected',
                    'reject_reason' => $request_data['reject_reason'],
                    'approved_by' => $request_data['user_id']
                ];
            } elseif ($user_role == config('constants.SuperUser')) {
                $update_arr = [
                    'forth_approval_status' => 'Rejected',
                    'forth_approval_id' => $request_data['user_id'],
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                    'updated_by' => $request_data['user_id'],
                    'status' => 'Rejected',
                    'reject_reason' => $request_data['reject_reason'],
                    'approved_by' => $request_data['user_id']
                ];
            } elseif ($user_role == config('constants.ACCOUNT_ROLE')) {
                $update_arr = [
                    'fifth_approval_status' => 'Rejected',
                    'fifth_approval_id' => $request_data['user_id'],
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                    'updated_by' => $request_data['user_id'],
                    'status' => 'Rejected',
                    'reject_reason' => $request_data['reject_reason'],
                    'approved_by' => $request_data['user_id']
                ];
            } else {
                return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
            }
            $this->notification_task->employeeExepenseRejectNotify([$expense_detail[0]->user_id]);
        }

        Employee_expense::where('id', $request_data['expense_id'])->update($update_arr);
        return response()->json(['status' => true, 'msg' => 'Expense successfully ' . $request_data['status'], 'data' => []]);
        /* //check if logged in user is senior of expense user and have rights
          $senior_check = \App\Employees::where('user_id', $expense_detail[0]->user_id)
          ->where('reporting_user_id', $request_data['user_id'])
          ->get();

          //check permission
          if ((in_array(5, $role_permission) && in_array(2, $role_permission)) || (in_array(6, $role_permission) && in_array(2, $role_permission) && $senior_check->count() > 0)) {
          //user has permission / change status of expense
          $update_arr = [
          'status' => $request_data['status'],
          'reject_reason' => $request_data['reject_reason'],
          'approved_by' => $request_data['user_id'],
          'updated_at' => date('Y-m-d H:i:s'),
          'updated_ip' => $request->ip(),
          'updated_by' => $request_data['user_id'],
          ];
          Employee_expense::where('id', $request_data['expense_id'])->update($update_arr);
          return response()->json(['status' => true, 'msg' => "Expense successfully " . strtolower($request_data['status']), 'data' => []]);
          } else {
          return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
          } */
    }

    public function employee_cash_transfer($expense_id, $user_id)
    {
        $expense = Employee_expense::whereId($expense_id)->first();

        $emp_cash = Employee_cash_management::where('employee_id', $expense['user_id'])->first();
        if ($emp_cash) {
            if ($emp_cash['balance'] >=  $expense['amount']) {
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
                    'user_id' => $user_id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'created_ip' => \Request::ip(),
                    'updated_ip' => \Request::ip(),
                    'updated_by' => $user_id,
                ];
                // dd($emp_cash_transfer_arr);
                Cash_transfer::insert($emp_cash_transfer_arr);

                $emp_cash->balance = $after_amount;
                $emp_cash->save();
            }
        }
    }

    public function get_voucher_ref_number(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'company_id' => 'required',
            /* 'client_id' => 'required',
            'project_id' => 'required',
            'project_site_id' => 'required', */
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();

        $vouchers = AssignedVoucher::where('to_user_id',$request_data['user_id'])->where('status','accepted')->pluck('voucher_ref_no');

        /*if($vouchers){
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }*/

        $all_voucher = VoucherNumberRegister::select('voucher_ref_no','id')
                ->where('company_id',$request_data['company_id'])
                /* ->where('client_id',$request_data['client_id'])
                ->where('project_id',$request_data['project_id'])
                ->where('project_site_id',$request_data['project_site_id']) */
                ->where('is_failed',0)
                ->where('is_used' ,'not_used')
                ->whereIn('voucher_ref_no' ,$vouchers)
                ->groupBy('voucher_ref_no')
                ->get()->toArray();

        if(count($all_voucher) == 0){
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        return response()->json(['status' => true, 'msg' => "Record Found", 'data' => $all_voucher]);

    }

    public function get_voucher_number(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'voucher_ref_no' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $all_voucher = VoucherNumberRegister::select('id','voucher_no')
                ->where('voucher_ref_no',$request_data['voucher_ref_no'])
                ->where('is_failed',0)
                ->where('is_used' ,'not_used')
                ->orderBy('voucher_no','asc')
                ->get()->toArray();

        if(count($all_voucher) == 0){
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        return response()->json(['status' => true, 'msg' => "Record Found", 'data' => $all_voucher]);

    }

    public function get_bank_cheque_rtgs_reff_list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'bank_id' => 'required',
            'company_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
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

        if (count($cheque_data) && count($rtgs_data)) {
            return response()->json(['status' => true, 'msg' => "Record Found", 'data' => ['cheque_reff_list' => $cheque_data, 'rtgs_reff_list' => $rtgs_data]]);
        } else {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }
    }

    public function get_cheque_number(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'bank_id' => 'required',
            'company_id' => 'required',
            'check_ref_no' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

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
        if (count($cheque_data)) {
            return response()->json(['status' => true, 'msg' => "Record Found", 'data' => $cheque_data]);
        } else {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }
    }

    public function get_rtgs_number(Request $request){

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'bank_id' => 'required',
            'company_id' => 'required',
            'rtgs_ref_no' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $bank_id = $request->bank_id;
        $company_id = $request->company_id;
        $rtgs_ref_no = $request->rtgs_ref_no;

        $rtgs_data = \App\RtgsRegister::select('rtgs_no', 'id')
        ->where(['bank_id' => $bank_id])
            ->where(['company_id' => $company_id])
            ->where(['rtgs_ref_no' => $rtgs_ref_no])
            ->where(['is_used' => 'not_used'])
            ->get()->toArray();

        if (count($rtgs_data)) {
            return response()->json(['status' => true, 'msg' => "Record Found", 'data' => $rtgs_data]);
        } else {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }
    }

    //08/09/2020
    public function get_loginuser_project_list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'client_id' => 'required',
            'expense_type' => 'required',
            'project_type' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $response_data = [];

        $partial_query = Projects::select(['project.id','project.project_name'])
            ->leftjoin('project_manager','project_manager.project_id','=','project.id')
            ->where(function ($query) use ($request_data) {
                $query->where('project.client_id', $request_data['client_id']);
                $query->orWhere('project.client_id', 1);
            });

            if (isset($request_data['expense_type']) && $request_data['expense_type'] == 'Site Expense') {
                  
                $partial_query->where('project_manager.user_id',$request_data['user_id']);
            }
            
            $projects_list = $partial_query->where('project.status', 'Enabled')
            ->where('project.project_type', $request_data['project_type'])
            ->groupBy('project.id')
            ->orderBy('project_name', 'asc')
            ->get();

        if ($projects_list->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }
        $response_data = $projects_list;
        return response()->json(['status' => true, 'msg' => "Record Found.", 'data' => $response_data]);
    }

    /* ----------------------------------- Driver Expense-------------------------------------------- */

    public function get_driver_expense_approvel_user(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $response_data['user_list'] = \App\User::where('role', config('constants.ACCOUNT_ROLE'))->where('status', 'Enabled')->get(['name', 'id']);
        return response()->json(['status' => true, 'msg' => 'record found', 'data' => $response_data]);
    }

    public function add_driver_expense(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            //'fuel_type' => 'required',
            'vehicle_type' => 'required',
            //'date_of_expense' => 'required',
            //'time_of_expense' => 'required',
            'meter_reading' => 'required',
            'moniter_user_id' => 'required',
            'amount' => 'required',
            'comment' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        //check role of user is of driver
        $logged_in_user = \App\User::where('id', $request_data['user_id'])->get();

        if ($logged_in_user[0]->role != config('constants.DRIVER')) {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }

        //upload images
        /* $meter_reading_photo = $request->file('meter_reading_photo');
          $meter_reading_file_path = $meter_reading_photo->store('public/driver_expense'); */

        /*  $bill_photo = $request->file('bill_photo');
          $bill_photo_file_path = $bill_photo->store('public/driver_expense');
         */
        //21-02-2020
        $meter_reading_file_path = "";
        if ($request->file('meter_reading_photo')) {

            $meter_reading_file = $request->file('meter_reading_photo');

            $original_file_name = explode('.', $meter_reading_file->getClientOriginalName());

            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);


            $file_path = $meter_reading_file->storeAs('public/driver_expense', $new_file_name);
            if ($file_path) {
                $meter_reading_file_path = $file_path;
            }
        }

        //21-02-2020
        if ($request->file('bill_photo')) {

            $bill_file = $request->file('bill_photo');

            $original_file_name = explode('.', $bill_file->getClientOriginalName());

            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);


            $file_path = $bill_file->storeAs('public/driver_expense', $new_file_name);
            if ($file_path) {
                $bill_photo_file_path = $file_path;
            }
        }


        $asset_detail = \App\Asset::where('id', $request_data['vehicle_type'])->get(['fuel_type']);

        $driver_expense_arr = [
            'user_id' => $request_data['user_id'],
            'fuel_type' => $asset_detail[0]->fuel_type,
            'asset_id' => $request_data['vehicle_type'],
            'date_of_expense' => date('Y-m-d'),
            'time_of_expense' => date('H:i:s'),
            'amount' => $request_data['amount'],
            'comment' => $request_data['comment'],
            'meter_reading_photo' => $meter_reading_file_path,
            'bill_photo' => $bill_photo_file_path,
            'fuel_price' => $request_data['fuel_price'],
            'total_fuel_quality' => $request_data['amount'] / $request_data['fuel_price'],
            'moniter_user_id' => $request_data['moniter_user_id'],
            'meter_reading' => $request_data['meter_reading'],
            'created_at' => date('Y-m-d H:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => $request_data['user_id']
        ];

        Driver_expense::insert($driver_expense_arr);
        return response()->json(['status' => true, 'msg' => "Expense successfully submitted.", 'data' => []]);
    }

    public function get_expense_list_by_driver(Request $request)
    {   //this
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        //check role of user is of driver
        $logged_in_user = \App\User::where('id', $request_data['user_id'])->get();

        if ($logged_in_user[0]->role != config('constants.DRIVER')) {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }

        $expense_list = Driver_expense::join('asset', 'asset.id', '=', 'driver_expense.asset_id')
            ->join('users', 'users.id', '=', 'driver_expense.moniter_user_id')
            ->leftjoin('users AS Accountant', 'driver_expense.first_approval_id', '=', 'Accountant.id')
            ->leftjoin('users AS Admin', 'driver_expense.second_approval_id', '=', 'Admin.id')
            ->leftjoin('users AS Superuser', 'driver_expense.third_approval_id', '=', 'Superuser.id')
            ->where('driver_expense.user_id', $request_data['user_id'])
            ->orderBy('created_at', 'DESC')
            ->get([
                'driver_expense.*', 'asset.name as vehicle_name', 'asset.asset_1 as vehicle_number', 'users.name as monitor_user_name', 'users.profile_image', 'driver_expense.first_approval_status AS Accountant_approval_status', 'driver_expense.second_approval_status AS Admin_approval_status',
                'driver_expense.third_approval_status AS superUser_approval_status', 'Accountant.name AS first_approval_user', 'Admin.name AS second_approval_user', 'Superuser.name AS third_approval_user'
            ]);

        if ($expense_list->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }
        foreach ($expense_list as $key => $expense) {

            //$expense_list[$key]->profile_image = asset('storage/' . str_replace('public/', '', $expense->profile_image));
            $expense_list[$key]->meter_reading_photo = asset('storage/' . str_replace('public/', '', $expense->meter_reading_photo));
            $expense_list[$key]->bill_photo = asset('storage/' . str_replace('public/', '', $expense->bill_photo));

            if ($expense->profile_image) {
                $expense_list[$key]->profile_image = asset('storage/' . str_replace('public/', '', $expense->profile_image));
            } else {
                $expense_list[$key]->profile_image = "";
            }
        }
        $response_data['expense_data'] = $expense_list;
        return response()->json(['status' => true, 'msg' => 'Record Found.', 'data' => $response_data]);
    }

    public function get_previous_meter_reading(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'asset_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $last_meter_reading = Driver_expense::where(['asset_id' => $request_data['asset_id']])->orderBy('id', 'DESC')->limit(1)->get();

        if ($last_meter_reading->count() == 0) {
            $response_data['last_reading'] = 0;
        } else {
            if ($last_meter_reading[0]->meter_reading) {
                $response_data['last_reading'] = $last_meter_reading[0]->meter_reading;
            } else {
                $response_data['last_reading'] = 0;
            }
        }
        return response()->json(['status' => true, 'msg' => 'Record found', 'data' => $response_data]);
    }

    public function edit_driver_expense(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            //'fuel_type' => 'required',
            'vehicle_type' => 'required',
            //'date_of_expense' => 'required',
            //'time_of_expense' => 'required',
            'amount' => 'required',
            'comment' => 'required',
            'driver_expense_id' => 'required',
            'meter_reading' => 'required',
            'moniter_user_id' => 'required',
            'is_resubmit' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        //check role of user is of driver
        $logged_in_user = \App\User::where('id', $request_data['user_id'])->get();

        if ($logged_in_user[0]->role != config('constants.DRIVER')) {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }

        $expense_detail = Driver_expense::where('id', $request_data['driver_expense_id'])->get();

        if ($expense_detail[0]->first_approval_status == 'Approved' && $expense_detail[0]->status == 'Pending') {
            return response()->json(['status' => false, 'msg' => "Expense already in process, you can not edit it.", 'data' => [], 'error' => config('errors.permission_error.code')]);
        }

        $asset_detail = \App\Asset::where('id', $request_data['vehicle_type'])->get(['fuel_type']);
        $driver_expense_arr = [
            'user_id' => $request_data['user_id'],
            'fuel_type' => $asset_detail[0]->fuel_type,
            'asset_id' => $request_data['vehicle_type'],
            'date_of_expense' => date('Y-m-d'),
            'time_of_expense' => date('H:i:s'),
            'amount' => $request_data['amount'],
            'comment' => $request_data['comment'],
            'fuel_price' => $request_data['fuel_price'],
            //'total_fuel_quality' => $request_data['amount'] / $request_data['fuel_price'],
            'moniter_user_id' => $request_data['moniter_user_id'],
            'meter_reading' => $request_data['meter_reading'],
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => $request_data['user_id']
        ];
        if ($request_data['fuel_price'] > 0) {

            $driver_expense_arr['total_fuel_quality'] = $request_data['amount'] / $request_data['fuel_price'];
        } else {

            $driver_expense_arr['total_fuel_quality'] = 0;
        }



        /*  if ($request->hasFile('meter_reading_photo')) {
          //upload images
          $meter_reading_photo = $request->file('meter_reading_photo');
          $meter_reading_file_path = $meter_reading_photo->store('public/driver_expense');

          Storage::delete($expense_detail[0]->meter_reading_photo);

          $driver_expense_arr['meter_reading_photo'] = $meter_reading_file_path;
          } */

        if ($request->file('meter_reading_photo')) {

            $meter_reading_file = $request->file('meter_reading_photo');

            $original_file_name = explode('.', $meter_reading_file->getClientOriginalName());

            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);

            $file_path = $meter_reading_file->storeAs('public/driver_expense', $new_file_name);
            Storage::delete($expense_detail[0]->meter_reading_photo);
            if ($file_path) {
                $driver_expense_arr['meter_reading_photo'] = $file_path;
            }
        }

        /*  if ($request->hasFile('bill_photo')) {
          $bill_photo = $request->file('bill_photo');
          $bill_photo_file_path = $bill_photo->store('public/driver_expense');

          Storage::delete($expense_detail[0]->bill_photo);

          $driver_expense_arr['bill_photo'] = $bill_photo_file_path;
          } */

        if ($request->file('bill_photo')) {

            $bill_file = $request->file('bill_photo');

            $original_file_name = explode('.', $bill_file->getClientOriginalName());

            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);

            $file_path = $bill_file->storeAs('public/driver_expense', $new_file_name);
            Storage::delete($expense_detail[0]->bill_photo);
            if ($file_path) {
                $driver_expense_arr['bill_photo'] = $file_path;
            }
        }

        if ($request_data['is_resubmit'] == "Yes") {
            $driver_expense_arr['status'] = "Pending";
            $driver_expense_arr['second_approval_status'] = "Pending";
            $driver_expense_arr['first_approval_status'] = "Pending";
            $driver_expense_arr['third_approval_status'] = "Pending";
        } else {
            $driver_expense_arr['status'] = "Pending";
            $driver_expense_arr['second_approval_status'] = "Pending";
            $driver_expense_arr['first_approval_status'] = "Pending";
            $driver_expense_arr['third_approval_status'] = "Pending";
        }


        Driver_expense::where('id', $request_data['driver_expense_id'])->update($driver_expense_arr);

        return response()->json(['status' => true, 'msg' => "Expense successfully updated.", 'data' => []]);
    }

    public function delete_driver_expense(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'driver_expense_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        //check role of user is of driver
        $logged_in_user = \App\User::where('id', $request_data['user_id'])->get();

        if ($logged_in_user[0]->role != config('constants.DRIVER')) {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }

        $expense_detail = Driver_expense::where('id', $request_data['driver_expense_id'])->get();

        if ($expense_detail[0]->first_approval_status == 'Approved') {
            return response()->json(['status' => false, 'msg' => "Expense already in process now, so you can not delete it.", 'data' => [], 'error' => config('errors.general_error.code')]);
        } elseif ($expense_detail[0]->first_approval_status == 'Rejected') {
            return response()->json(['status' => false, 'msg' => "Expense already in rejected.", 'data' => [], 'error' => config('errors.general_error.code')]);
        }

        Storage::delete($expense_detail[0]->bill_photo);
        Storage::delete($expense_detail[0]->meter_reading_photo);

        Driver_expense::where('id', $request_data['driver_expense_id'])->delete();
        return response()->json(['status' => true, 'msg' => "Expense successfully deleted.", 'data' => []]);
    }

    public function reject_driver_expense(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'driver_expense_id' => 'required',
            'reject_note' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];


        $logged_in_user = \App\User::where('id', $request_data['user_id'])->get();

        if ($logged_in_user[0]->role == config('constants.ACCOUNT_ROLE')) {
            $update_arr = [
                'first_approval_status' => 'Rejected',
                'first_approval_id' => $request_data['user_id'],
                'status' => 'Rejected',
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => $request_data['user_id'],
                'reject_note' => $request_data['reject_note']
            ];
        } elseif ($logged_in_user[0]->role == config('constants.Admin')) {
            $update_arr = [
                'second_approval_status' => 'Rejected',
                'second_approval_id' => $request_data['user_id'],
                'status' => 'Rejected',
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => $request_data['user_id'],
                'reject_note' => $request_data['reject_note']
            ];
        } elseif ($logged_in_user[0]->role == config('constants.SuperUser')) {
            $update_arr = [
                'third_approval_status' => 'Rejected',
                'third_approval_id' => $request_data['user_id'],
                'status' => 'Rejected',
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => $request_data['user_id'],
                'reject_note' => $request_data['reject_note']
            ];
        } else {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }

        Driver_expense::where('id', $request_data['driver_expense_id'])->update($update_arr);

        $expense_detail = Driver_expense::where('id', $request_data['driver_expense_id'])->get(['user_id']);
        $this->notification_task->driverExepenseRejectNotify([$expense_detail[0]->user_id]);

        return response()->json(['status' => true, 'msg' => 'Driver expense is rejected.', 'data' => []]);
    }

    public function get_driver_expense_approval_list(Request $request)
    {   //this
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];


        $logged_in_user = \App\User::where('id', $request_data['user_id'])->get();
        $driver_expense_list = [];
        if ($logged_in_user[0]->role == config('constants.ACCOUNT_ROLE')) {
            $driver_expense_list = Driver_expense::join('users', 'users.id', '=', 'driver_expense.user_id')
                ->leftjoin('users AS Accountant', 'driver_expense.first_approval_id', '=', 'Accountant.id')
                ->leftjoin('users AS Admin', 'driver_expense.second_approval_id', '=', 'Admin.id')
                ->leftjoin('users AS Superuser', 'driver_expense.third_approval_id', '=', 'Superuser.id')
                ->join('asset', 'asset.id', '=', 'driver_expense.asset_id')
                ->where('driver_expense.first_approval_status', 'Pending')
                ->where('driver_expense.moniter_user_id', $request_data['user_id'])
                ->orderBy('driver_expense.id', 'DESC')
                ->get([
                    'driver_expense.*', 'users.name as driver_name', 'users.profile_image', 'asset.name as vehicle_name', 'asset.asset_1 as vehicle_number', 'driver_expense.first_approval_status AS Accountant_approval_status', 'driver_expense.second_approval_status AS Admin_approval_status',
                    'driver_expense.third_approval_status AS superUser_approval_status', 'Accountant.name AS first_approval_user', 'Admin.name AS second_approval_user', 'Superuser.name AS third_approval_user'
                ]);
        } elseif ($logged_in_user[0]->role == config('constants.Admin')) {
            $driver_expense_list = Driver_expense::join('users', 'users.id', '=', 'driver_expense.user_id')
                ->leftjoin('users AS Accountant', 'driver_expense.first_approval_id', '=', 'Accountant.id')
                ->leftjoin('users AS Admin', 'driver_expense.second_approval_id', '=', 'Admin.id')
                ->leftjoin('users AS Superuser', 'driver_expense.third_approval_id', '=', 'Superuser.id')
                ->join('asset', 'asset.id', '=', 'driver_expense.asset_id')
                ->where('driver_expense.first_approval_status', 'Approved')
                ->where('driver_expense.second_approval_status', 'Pending')
                ->orderBy('driver_expense.id', 'DESC')
                ->get([
                    'driver_expense.*', 'users.name as driver_name', 'users.profile_image', 'asset.name as vehicle_name', 'asset.asset_1 as vehicle_number', 'driver_expense.first_approval_status AS Accountant_approval_status', 'driver_expense.second_approval_status AS Admin_approval_status',
                    'driver_expense.third_approval_status AS superUser_approval_status', 'Accountant.name AS first_approval_user', 'Admin.name AS second_approval_user', 'Superuser.name AS third_approval_user'
                ]);
        } elseif ($logged_in_user[0]->role == config('constants.SuperUser')) {
            $driver_expense_list = Driver_expense::join('users', 'users.id', '=', 'driver_expense.user_id')
                ->leftjoin('users AS Accountant', 'driver_expense.first_approval_id', '=', 'Accountant.id')
                ->leftjoin('users AS Admin', 'driver_expense.second_approval_id', '=', 'Admin.id')
                ->leftjoin('users AS Superuser', 'driver_expense.third_approval_id', '=', 'Superuser.id')
                ->join('asset', 'asset.id', '=', 'driver_expense.asset_id')
                ->where('driver_expense.first_approval_status', 'Approved')
                ->where('driver_expense.second_approval_status', 'Approved')
                ->where('driver_expense.third_approval_status', 'Pending')
                ->orderBy('driver_expense.id', 'DESC')
                ->get([
                    'driver_expense.*', 'users.name as driver_name', 'users.profile_image', 'asset.name as vehicle_name', 'asset.asset_1 as vehicle_number', 'driver_expense.first_approval_status AS Accountant_approval_status', 'driver_expense.second_approval_status AS Admin_approval_status',
                    'driver_expense.third_approval_status AS superUser_approval_status', 'Accountant.name AS first_approval_user', 'Admin.name AS second_approval_user', 'Superuser.name AS third_approval_user'
                ]);
        } else {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }

        if ($driver_expense_list->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        foreach ($driver_expense_list as $key => $expense) {
            $driver_expense_list[$key]['meter_reading_photo'] = asset('storage/' . str_replace('public/', '', $expense->meter_reading_photo));
            $driver_expense_list[$key]['bill_photo'] = asset('storage/' . str_replace('public/', '', $expense->bill_photo));

            if ($expense->profile_image) {
                $driver_expense_list[$key]->profile_image = asset('storage/' . str_replace('public/', '', $expense->profile_image));
            } else {
                $driver_expense_list[$key]->profile_image = "";
            }
        }

        return response()->json(['status' => true, 'msg' => 'Record Found', 'data' => $driver_expense_list]);
    }

    public function approve_driver_expense(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'driver_expense_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $logged_in_user = \App\User::where('id', $request_data['user_id'])->get();
        $driver_expense_detail = Driver_expense::where('id', $request_data['driver_expense_id'])->get();
        if ($logged_in_user[0]->role == config('constants.ACCOUNT_ROLE')) {
            $update_arr = [
                'first_approval_status' => 'Approved',
                'first_approval_id' => $request_data['user_id'],
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => $request_data['user_id'],
            ];
            //get admin user list
            $admin_list = \App\User::where('role', config('constants.Admin'))->get(['id'])->pluck('id');
            $this->notification_task->driverExepenseFirstApprovalNotify([$admin_list]);
        } elseif ($logged_in_user[0]->role == config('constants.Admin')) {
            $update_arr = [
                'second_approval_status' => 'Approved',
                'second_approval_id' => $request_data['user_id'],
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => $request_data['user_id'],
            ];


            //$accountant_list= \App\User::where('role',config('constants.ACCOUNT_ROLE'))->get(['id'])->pluck('id');
            //$this->notification_task->driverExepenseSecondApprovalNotify([$accountant_list]);
            $this->notification_task->driverExepenseSecondApprovalNotify([$this->super_admin->id]);
        } elseif ($logged_in_user[0]->role == config('constants.SuperUser')) {
            $update_arr = [
                'third_approval_status' => 'Approved',
                'third_approval_id' => $request_data['user_id'],
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => $request_data['user_id'],
                'status' => "Approved"
            ];

            $this->notification_task->driverExepenseThirdApprovalNotify([$driver_expense_detail[0]->user_id]);
        } else {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }

        Driver_expense::where('id', $request_data['driver_expense_id'])->update($update_arr);
        return response()->json(['status' => true, 'msg' => 'Expense successfully approved.', 'data' => []]);
    }

    public function get_vehicle_list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $asset_list = \App\Asset::where('asset_type', 'Vehicle Asset')->where('status', 'Enabled')->get();
        if ($asset_list->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }
        //check which vehicle is assigned to this driver
        $assign_vehicle = \App\AssetAccess::join('asset', 'asset.id', '=', 'asset_access.asset_id')
            ->where('is_allocate', 1)
            ->where('asset_access.asset_access_user_id', $request_data['user_id'])
            ->get(['asset.id']);
        $assign_id = 0;
        if ($assign_vehicle->count() > 0) {
            $assign_id = $assign_vehicle[0]->id;
        }


        foreach ($asset_list as $key => $asset) {
            if ($assign_id != 0 && $asset->id == $assign_id) {
                $asset_list[$key]->already_assigned = true;
            } else {
                $asset_list[$key]->already_assigned = false;
            }
        }

        return response()->json(['status' => true, 'msg' => "Record found", 'data' => $asset_list]);
    }



    // --------------------------------------------------------------------------------

    public function get_company_client_list(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'company_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $clients = Clients::select('clients.*')
            ->where('clients.status', 'Enabled')
            ->where(function ($query) use ($request_data) {
                $query->where('clients.company_id', $request_data['company_id']);
                $query->orWhere('clients.company_id', 0);
            })
            ->get();


            if ($clients->count() == 0) {
                return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
            }

            return response()->json(['status' => true, 'msg' => "Record found", 'data' => $clients]);

    }

    public function get_client_project_list(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'client_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();

        $projects = Projects::select('project.*')
            ->where('project.status', 'Enabled')
            ->where(function ($query) use ($request_data) {
                $query->where('project.client_id', $request_data['client_id']);
                $query->orWhere('project.client_id', 1);
            })
            ->get();

            if ($projects->count() == 0) {
                return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
            }

            return response()->json(['status' => true, 'msg' => "Record found", 'data' => $projects]);


    }

    public function get_project_sites_list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'project_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();


        $project_sites = Project_sites::select('project_sites.*')
            ->where('project_sites.status', 'Enabled')
            ->where(function ($query) use ($request_data) {
                $query->where('project_sites.project_id', $request_data['project_id'])->orWhere('project_sites.project_id', 1);
            })
            ->get();

            if ($project_sites->count() == 0) {
                return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
            }

            return response()->json(['status' => true, 'msg' => "Record found", 'data' => $project_sites]);
    }

    // ----------------------------------------
}
