<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Common_query;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Imports\BankTransactionImport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Config;
use App\User;
use App\Policy;
use App\Lib\Permissions;
use App\Email_format;
use App\Mail\Mails;
use App\Role_module;
use App\Department;
use App\RevisePolicy;
use App\UserRevisePolicy;
use App\BudgetSheetApproval;
use Illuminate\Support\Facades\Mail;
use App\Lib\CommonTask;
use App\Lib\NotificationTask;
use App\Budget_sheet_file;
use App\Budget_sheet_invoice_files;
use DateTime;
use App\Vendors;
use App\Hold_budget_sheet;
use Illuminate\Support\Facades\Storage;
use App\Lib\UserActionLogs;
use App\Projects;
use App\Clients;
use App\Project_sites;

class BudgetSheetController extends Controller {

    public $data;
    public $notification_task;
    public $common_task;
    private $super_admin;
    public $user_action_logs;

    public function __construct() {
        $this->data['module_title'] = "Budget Sheet";
        $this->data['module_link'] = "admin.budget_sheet";
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();
        $this->user_action_logs = new UserActionLogs();
        $this->super_admin = \App\User::where('role', 1)->first();
    }

    public function index() {
        $this->data['page_title'] = "Budget Sheet";
        $access_level = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => 26])->get()->first();
        $this->data['access_rule'] = "";

        if (!empty($access_level)) {
            $this->data['access_rule'] = $access_level->access_level;
        }

        return view('admin.budget_sheet.index', $this->data);
    }

    public function getBudgetSheets(Request $request)  //17-04-2020
    {

        $budget_sheets = BudgetSheetApproval::select('id','budhet_sheet_no')->orderBy('budhet_sheet_no')->get();

        if ($budget_sheets->count() > 0) {

            $this->data['budget_sheets'] = $budget_sheets;

            return response()->json(['status' => true, 'data' => $this->data]);
        } else {
            return response()->json(['status' => false]);
        }

    }

    public function get_budget_sheet_list() {   //change

        $datatable_fields = [
            'budget_sheet_approval.meeting_number', 'budget_sheet_approval.meeting_date','budget_sheet_approval.budhet_sheet_no',
            'company.company_name','clients.client_name','clients.location', 'department.dept_name','vendor.vendor_name',
             'budget_sheet_approval.description','budget_sheet_approval.remark_by_user', 'budget_sheet_approval.request_amount',
            'budget_sheet_approval.schedule_date_from', 'budget_sheet_approval.schedule_date_to','budget_sheet_approval.mode_of_payment',
            'project.project_name','project_sites.site_name', 'budget_sheet_approval.total_amount', 'budget_sheet_approval.approved_amount',
            'budget_sheet_approval.purchase_order_number', 'budget_sheet_approval.purchase_order_date', 'budget_sheet_approval.bill_number', 'budget_sheet_approval.bill_date' ,'budget_sheet_approval.approval_remark', 'budget_sheet_approval.hold_amount', 'budget_sheet_approval.remain_hold_amount',
             'budget_sheet_approval.final_approved_amount','budget_sheet_approval.first_approval_status','budget_sheet_approval.second_approval_status',
            'budget_sheet_approval.status' , 'budget_sheet_approval.payment_status','budget_sheet_approval.payment_date',
        ];
        $request = Input::all();


        if (Auth::user()->role == config('constants.ACCOUNT_ROLE')) {
            $conditions_array = [];
        }else{
            $conditions_array = ['budget_sheet_approval.user_id' => Auth::user()->id];
        }

        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'users';
        $join_str[0]['join_table_id'] = 'users.id';
        $join_str[0]['from_table_id'] = 'budget_sheet_approval.user_id';

        $join_str[1]['join_type'] = '';
        $join_str[1]['table'] = 'company';
        $join_str[1]['join_table_id'] = 'company.id';
        $join_str[1]['from_table_id'] = 'budget_sheet_approval.company_id';

        $join_str[2]['join_type'] = '';
        $join_str[2]['table'] = 'department';
        $join_str[2]['join_table_id'] = 'department.id';
        $join_str[2]['from_table_id'] = 'budget_sheet_approval.department_id';

        $join_str[3]['join_type'] = '';
        $join_str[3]['table'] = 'vendor';
        $join_str[3]['join_table_id'] = 'vendor.id';
        $join_str[3]['from_table_id'] = 'budget_sheet_approval.vendor_id';

        $join_str[4]['join_type'] = '';
        $join_str[4]['table'] = 'project';
        $join_str[4]['join_table_id'] = 'project.id';
        $join_str[4]['from_table_id'] = 'budget_sheet_approval.project_id';

        $join_str[5]['join_type'] = 'left';
        $join_str[5]['table'] = 'clients';
        $join_str[5]['join_table_id'] = 'clients.id';
        $join_str[5]['from_table_id'] = 'budget_sheet_approval.client_id';

        $join_str[6]['join_type'] = 'left';
        $join_str[6]['table'] = 'project_sites';
        $join_str[6]['join_table_id'] = 'project_sites.id';
        $join_str[6]['from_table_id'] = 'budget_sheet_approval.project_site_id';
        $getfiled = [
            'budget_sheet_approval.*',
            'company.company_name','clients.client_name','clients.location', 'department.dept_name',
            'vendor.vendor_name',
            'project.project_name','project_sites.site_name'
        ];
        $table = "budget_sheet_approval";

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);

        die();
    }

    public function get_budget_sheet_files(Request $request) {

        $validator_normal = Validator::make($request->all(), ['id' => 'required']);

        if ($validator_normal->fails()) {
            return response()->json(['status' => false]);
        }

        $budget_sheet_id = $request->id;

        $budget_sheet_files = Budget_sheet_file::where('budget_sheet_id', $budget_sheet_id)
                ->get(['id', 'budget_sheet_id', 'budget_sheet_file']);

        $this->data['get_status'] = BudgetSheetApproval::where('id', $budget_sheet_id)->value('status');

        foreach ($budget_sheet_files as $key => $files) {

            $budget_sheet_files[$key]->file_name = str_replace('public/budget_sheet_file/', '', $files->budget_sheet_file);

            if ($files->budget_sheet_file) {

                $budget_sheet_files[$key]->budget_sheet_file = asset('storage/' . str_replace('public/', '', $files->budget_sheet_file));
            } else {

                $budget_sheet_files[$key]->budget_sheet_file = "";
            }
        }

        $this->data['budget_sheet_files'] = $budget_sheet_files;

        if ($budget_sheet_files->count() == 0) {
            return response()->json(['status' => false, 'data' => $this->data]);
        } else {

            return response()->json(['status' => true, 'data' => $this->data]);
        }
    }

    public function delete_file(Request $request) {

        $validator_normal = Validator::make($request->all(), ['id' => 'required']);

        if ($validator_normal->fails()) {
            return response()->json(['status' => false]);
        }

        $budget_sheet_file_id = $request->id;

        Budget_sheet_file::where('id', $budget_sheet_file_id)
                ->delete();

        return response()->json(['status' => true, 'data' => []]);
    }

    public function get_budget_sheet_list_ajax() {

        $datatable_fields = [
            'budget_sheet_approval.meeting_number', 'budget_sheet_approval.meeting_date','budget_sheet_approval.budhet_sheet_no',
            'company.company_name','clients.client_name', 'department.dept_name',
            'vendor.vendor_name', 'budget_sheet_approval.description',
            'budget_sheet_approval.remark_by_user', 'budget_sheet_approval.request_amount', 'budget_sheet_approval.schedule_date_from', 'budget_sheet_approval.schedule_date_to', 'budget_sheet_approval.mode_of_payment',
             'project.project_name','project_sites.site_name', 'budget_sheet_approval.total_amount', 'budget_sheet_approval.purchase_order_number', 'budget_sheet_approval.approved_amount',
            'budget_sheet_approval.purchase_order_number', 'budget_sheet_approval.purchase_order_date', 'budget_sheet_approval.bill_number', 'budget_sheet_approval.bill_date', 'budget_sheet_approval.approval_remark', 'budget_sheet_approval.hold_amount', 'budget_sheet_approval.remain_hold_amount', 'budget_sheet_approval.final_approved_amount',
            'budget_sheet_approval.first_approval_status','budget_sheet_approval.second_approval_status', 'budget_sheet_approval.status'
        ];
        $request = Input::all();

        /* if (Auth::user()->role == config('constants.ACCOUNT_ROLE')) {
          $conditions_array = ['budget_sheet_approval.status' => 'Pending'];
          }

          if (Auth::user()->role == config('constants.SuperUser')) {
          $conditions_array = [ 'budget_sheet_approval.status' => 'Pending'];
          } */
        $conditions_array = ['budget_sheet_approval.status' => 'Pending'];

        if (Auth::user()->role == config('constants.Admin')) {
            $conditions_array['budget_sheet_approval.first_approval_status'] = "Pending";
        } elseif (Auth::user()->role == config('constants.SuperUser')) {
            $conditions_array['budget_sheet_approval.first_approval_status'] = "Approved";
            $conditions_array['budget_sheet_approval.second_approval_status'] = "Pending";
        }

        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'users';
        $join_str[0]['join_table_id'] = 'users.id';
        $join_str[0]['from_table_id'] = 'budget_sheet_approval.user_id';

        $join_str[1]['join_type'] = '';
        $join_str[1]['table'] = 'company';
        $join_str[1]['join_table_id'] = 'company.id';
        $join_str[1]['from_table_id'] = 'budget_sheet_approval.company_id';

        $join_str[2]['join_type'] = '';
        $join_str[2]['table'] = 'department';
        $join_str[2]['join_table_id'] = 'department.id';
        $join_str[2]['from_table_id'] = 'budget_sheet_approval.department_id';

        $join_str[3]['join_type'] = '';
        $join_str[3]['table'] = 'vendor';
        $join_str[3]['join_table_id'] = 'vendor.id';
        $join_str[3]['from_table_id'] = 'budget_sheet_approval.vendor_id';

        $join_str[4]['join_type'] = '';
        $join_str[4]['table'] = 'project';
        $join_str[4]['join_table_id'] = 'project.id';
        $join_str[4]['from_table_id'] = 'budget_sheet_approval.project_id';

        $join_str[5]['join_type'] = 'left';
        $join_str[5]['table'] = 'clients';
        $join_str[5]['join_table_id'] = 'clients.id';
        $join_str[5]['from_table_id'] = 'budget_sheet_approval.client_id';

        $join_str[6]['join_type'] = 'left';
        $join_str[6]['table'] = 'project_sites';
        $join_str[6]['join_table_id'] = 'project_sites.id';
        $join_str[6]['from_table_id'] = 'budget_sheet_approval.project_site_id';


        $getfiled = [
            'budget_sheet_approval.*',
            'company.company_name','clients.client_name','clients.location', 'department.dept_name',
            'vendor.vendor_name',
            'project.project_name','project_sites.site_name'
        ];
        $table = "budget_sheet_approval";

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);

        die();
    }

    public function add_budget_sheet_detail() {
        $this->data['page_title'] = 'Add Budget Details';
        $this->data['department_list'] = \App\Department::orderBy('dept_name')->get();
        $this->data['vendor_list'] = Vendors::where('status', 'Enabled')->orderBy('vendor_name')->get();
        $this->data['company_list'] = \App\Companies::where('status', 'Enabled')->orderBy('company_name')->get();
        $this->data['project_list'] = \App\Projects::where('status', 'Enabled')->get();
        return view('admin.budget_sheet.add_payment', $this->data);
    }

    public function insert_budget_sheet(Request $request) {
        $validator_normal = Validator::make($request->all(), [
                    'budget_sheet_year' => 'required',
                    'budget_sheet_week' => 'required',
                    'company_id' => 'required',
                    'client_id' => 'required',
                    'department_id.*' => 'required',
                    'vendor_id.*' => 'required',
                    'description.*' => 'required',
                    'request_amount.*' => 'required',
                    'schedule_date.*' => 'required',
                    'mode_of_payment.*' => 'required',
                    'project_id.*' => 'required',
                    'project_site_id' => 'required',
                    'total_amount.*' => 'required',
        ]);

        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_budget_sheet_detail')->with('error', 'Please follow validation rules.');
        }

        $request_data = $request->all();

        $company_data = \App\Companies::where('id', $request_data['company_id'])->get();
        $month = (new DateTime())->setISODate($request_data['budget_sheet_year'], $request_data['budget_sheet_week'])->format('m');

        //echo $company_data[0]->company_short_name . '/' . date('F', strtotime($month)) . '-' . $request_data['budget_sheet_year'] . '/' . $request_data['budget_sheet_week'];
        //die();
        //        echo '<pre>';
        //        print_r($request_data['file_counts']);
        //        print_r($_FILES); die();
        $total_file_count = $request_data['file_counts'];

        $file_array = [];
        //upload all images
        if ($request->file('budget_sheet_file')) {
            $budget_files_list = $request->file('budget_sheet_file');
            foreach ($budget_files_list as $key => $budget_files) {

                $original_file_name = explode('.', $budget_files[0]->getClientOriginalName());

                $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);


                $file_path = $budget_files[0]->storeAs('public/budget_sheet_file', $new_file_name);
                array_push($file_array, $file_path);
            }
        }

        foreach ($request_data['department_id'] as $key => $department_id) {

            $schedule_date_arr = explode('-', $request_data['schedule_date'][$key]);

            $schedule_date_to = date('Y-m-d', strtotime(str_replace('/', '-', $schedule_date_arr[1])));
            $schedule_date_from = date('Y-m-d', strtotime(str_replace('/', '-', $schedule_date_arr[0])));

            $BudgetSheetApproval = new BudgetSheetApproval();
            $BudgetSheetApproval->budget_sheet_year = $request_data['budget_sheet_year'];
            $BudgetSheetApproval->budget_sheet_week = $request_data['budget_sheet_week'];
            $BudgetSheetApproval->budget_sheet_month = $month;
            $BudgetSheetApproval->meeting_number = $company_data[0]->company_short_name . '/' . date('F', strtotime($request_data['budget_sheet_year'].'-'.$month.'-01')) . '-' . $request_data['budget_sheet_year'] . '/' . $request_data['budget_sheet_week'];
            $BudgetSheetApproval->department_id = $request_data['department_id'][$key];
            $BudgetSheetApproval->vendor_id = $request_data['vendor_id'][$key];
            $BudgetSheetApproval->description = $request_data['description'][$key];
            $BudgetSheetApproval->remark_by_user = $request_data['remark_by'][$key];
            $BudgetSheetApproval->request_amount = $request_data['request_amount'][$key];
            $BudgetSheetApproval->schedule_date_to = $schedule_date_to;
            $BudgetSheetApproval->schedule_date_from = $schedule_date_from;
            $BudgetSheetApproval->mode_of_payment = $request_data['mode_of_payment'][$key];
            $BudgetSheetApproval->project_type = $request_data['project_type'][$key];
            $BudgetSheetApproval->project_id = $request_data['project_id'][$key];
            $BudgetSheetApproval->client_id = $request_data['client_id'][$key];
            $BudgetSheetApproval->project_site_id = $request_data['project_site_id'][$key];
            $BudgetSheetApproval->total_amount = $request_data['total_amount'][$key];
            $BudgetSheetApproval->purchase_order_number = $request_data['purchase_order_number'][$key];
            $BudgetSheetApproval->purchase_order_date = $request_data['purchase_order_date'][$key];
            $BudgetSheetApproval->bill_number = $request_data['bill_number'][$key];
            $BudgetSheetApproval->bill_date = $request_data['bill_date'][$key];
            $BudgetSheetApproval->created_at = date('Y-m-d H:i:s');
            $BudgetSheetApproval->created_ip = $request->ip();
            $BudgetSheetApproval->updated_at = date('Y-m-d H:i:s');
            $BudgetSheetApproval->updated_ip = $request->ip();
            $BudgetSheetApproval->updated_by = Auth::user()->id;
            $BudgetSheetApproval->meeting_date = date('Y-m-d');
            $BudgetSheetApproval->user_id = Auth::user()->id;
            $BudgetSheetApproval->company_id = $request_data['company_id'];
            $BudgetSheetApproval->release_amount_first_approval_datetime = date('Y-m-d');
            dd($BudgetSheetApproval);

            if ($request->hasFile('invoice_file')) {
                $invoice_file = $request->file('invoice_file');
                foreach ($invoice_file as $key1 => $value) {
                    if ($key == $key1) {
                        $file_path = $value->store('public/invoice_file');
                        $BudgetSheetApproval->invoice_file = $file_path;
                    }
                }
            }

            $BudgetSheetApproval->save();

            $updateArr = [
                'budhet_sheet_no' => $company_data[0]->company_short_name . '/' . date('F', strtotime($request_data['budget_sheet_year'].'-'.$month.'-01')) . '-' . $request_data['budget_sheet_year'] . '/' . $request_data['budget_sheet_week']. '/' . $BudgetSheetApproval->id
            ];
            BudgetSheetApproval::where('id',$BudgetSheetApproval->id)->update($updateArr);

            // User Action Log
            $dept_name = Department::whereId($request_data['department_id'][$key])->value('dept_name');
            $vendor_name = Vendors::whereId($request_data['vendor_id'][$key])->value('vendor_name');
            $client_name = Clients::whereId($request_data['client_id'][$key])->value('client_name');
            $project_name = Projects::whereId($request_data['project_id'][$key])->value('project_name');
            $project_site = Project_sites::whereId($request_data['project_site_id'][$key])->value('site_name');
            $add_string = "<br>Company Name: ".$company_data[0]->company_name."<br>Department Name: ".$dept_name."<br>Vendor Name: ".$vendor_name."<br>Client Name: ".$client_name."<br>Project Name: ".$project_name."<br>Project Site Name: ".$project_site."<br>Total Amount: ".$request_data['total_amount'][$key]."<br>Request Amount: ".$request_data['request_amount'][$key]."<br>Payment Mode: ".$request_data['mode_of_payment'][$key]."<br>Schedule Date: ".$request_data['schedule_date'][$key];

            $budhet_sheet_no = $company_data[0]->company_short_name . '/' . date('F', strtotime($request_data['budget_sheet_year'] . '-' . $month . '-01')) . '-' . $request_data['budget_sheet_year'] . '/' . $request_data['budget_sheet_week'] . '/' . $BudgetSheetApproval->id;
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => $budhet_sheet_no. " budget sheet number data added".$add_string,
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            if (!empty($total_file_count) && isset($total_file_count[$key])) {
                $file_count = $total_file_count[$key];
                for ($i = 0; $i < $file_count; $i++) {
                    if (isset($file_array[$i])) {
                        $budget_sheet_file_arr = [
                            'budget_sheet_id' => $BudgetSheetApproval->id,
                            'budget_sheet_file' => $file_array[$i],
                            'created_at' => date('Y-m-d H:i:s'),
                            'created_ip' => $request->ip(),
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_ip' => $request->ip()
                        ];
                        \App\Budget_sheet_file::insert($budget_sheet_file_arr);
                        unset($file_array[$i]);
                    }
                }
                $file_array = array_values($file_array);
            }
        }

        $admin_user_data = User::where('role', config('constants.Admin'))->get();

        $mail_data = [
            'to_user_name' => $admin_user_data[0]->name,
            'budget_sheet_number' => $BudgetSheetApproval->meeting_number,
            'to_email' => $admin_user_data[0]->email,
            'request_user_name' => Auth::user()->name
        ];
        $this->common_task->budgetSheetRequestEmail($mail_data);
        $this->notification_task->budgetSheetRequestNotify([$admin_user_data[0]->id], Auth::user()->name, $BudgetSheetApproval->meeting_number);
        return redirect()->route('admin.budget_sheet')->with('success', 'Budget Sheet details added successfully.');
    }

    public function edit_budget_sheet_detail($id) {

        $this->data['page_title'] = "Edit Budget Payment";
        $this->data['budget_sheet_detail'] = BudgetSheetApproval::where('id', $id)->get();
        $this->data['department_list'] = \App\Department::orderBy('dept_name')->get();
        $this->data['vendor_list'] = Vendors::orderBy('vendor_name')->where('status', 'Enabled')->where('company_id', $this->data['budget_sheet_detail'][0]->company_id)->get();
        $this->data['company_list'] = \App\Companies::where('status', 'Enabled')->orderBy('company_name')->get();
        $this->data['project_list'] = \App\Projects::where('status', 'Enabled')->where('company_id', $this->data['budget_sheet_detail'][0]->company_id)->get();

        if ($this->data['budget_sheet_detail'][0]->status == 'Approved') {
            return redirect()->route('admin.budget_sheet')->with('error', 'Budget sheet request is already approved. You can not edit it now.');
        }

        $check_result = Permissions::checkPermission(26, 2);
        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }

        if ($this->data['budget_sheet_detail']->count() == 0) {
            return redirect()->route('admin.budget_sheet.edit_payment')->with('error', 'Error Occurred. Try Again!');
        }
        return view('admin.budget_sheet.edit_payment', $this->data);
    }

    public function update_budget_sheet(Request $request) {
        $validator_normal = Validator::make($request->all(), [
                    'id' => 'required',
                    'budget_sheet_year' => 'required',
                    'budget_sheet_week' => 'required',
                    'company_id' => 'required',
                    'client_id' => 'required',
                    'department_id' => 'required',
                    'vendor_id' => 'required',
                    'description' => 'required',
                    'remark_by' => 'required',
                    'request_amount' => 'required',
                    'schedule_date' => 'required',
                    'mode_of_payment' => 'required',
                    'project_id' => 'required',
                    'project_site_id' => 'required',
                    'total_amount' => 'required'
        ]);

        if ($validator_normal->fails()) {
            return redirect()->route('admin.budget_sheet')->with('error', 'Please follow validation rules.');
        }
        $request_data = $request->all();
        $company_data = \App\Companies::where('id', $request_data['company_id'])->get();

        $schedule_date_arr = explode('-', $request_data['schedule_date']);

        $schedule_date_to = date('Y-m-d', strtotime(str_replace('/', '-', $schedule_date_arr[1])));
        $schedule_date_from = date('Y-m-d', strtotime(str_replace('/', '-', $schedule_date_arr[0])));
        $month = (new DateTime())->setISODate($request_data['budget_sheet_year'], $request_data['budget_sheet_week'])->format('m');

        $budget_sheet_arr = [
            'budget_sheet_year' => $request->input('budget_sheet_year'),
            'budget_sheet_month' => $month,
            'budget_sheet_week' => $request->input('budget_sheet_week'),
            'company_id' => $request->input('company_id'),
            'client_id' => $request->input('client_id'),
            'project_site_id' => $request->input('project_site_id'),
            'meeting_number' => $company_data[0]->company_short_name . '/' . date('F', strtotime($month)) . '-' . $request_data['budget_sheet_year'] . '/' . $request_data['budget_sheet_week'],
            //'meeting_date' => date('Y-m-d'),
            'department_id' => $request->input('department_id'),
            'vendor_id' => $request->input('vendor_id'),
            'description' => $request->input('description'),
            'remark_by_user' => $request->input('remark_by'),
            'request_amount' => $request->input('request_amount'),
            'schedule_date_to' => $schedule_date_to,
            'schedule_date_from' => $schedule_date_from,
            'mode_of_payment' => $request->input('mode_of_payment'),
            'project_type' => $request->input('project_type'),
            'project_id' => $request->input('project_id'),
            'total_amount' => $request->input('total_amount'),
            'purchase_order_number' => $request->input('purchase_order_number'),
            'purchase_order_date' => $request->input('purchase_order_date'),
            'bill_number' => $request->input('bill_number'),
            'bill_date' => $request->input('bill_date'),
            'first_approval_status' => "Pending",
            'second_approval_status' => "Pending",
            'status' => "Pending",
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id
        ];

        if ($request->hasFile('invoice_file')) {
            $invoice_file = $request->file('invoice_file');
            $file_path = $invoice_file->store('public/invoice_file');
            $budget_sheet_arr['invoice_file'] = $file_path;
        }

        BudgetSheetApproval::where('id', $request->input('id'))->update($budget_sheet_arr);

        // User Action Log
        $dept_name = Department::whereId($request->input('department_id'))->value('dept_name');
        $vendor_name = Vendors::whereId($request->input('vendor_id'))->value('vendor_name');
        $client_name = Clients::whereId($request->input('client_id'))->value('client_name');
        $project_name = Projects::whereId($request->input('project_id'))->value('project_name');
        $project_site = Project_sites::whereId($request->input('project_site_id'))->value('site_name');
        $add_string = "<br>Company Name: ".$company_data[0]->company_name."<br>Department Name: ".$dept_name."<br>Vendor Name: ".$vendor_name."<br>Client Name: ".$client_name."<br>Project Name: ".$project_name."<br>Project Site Name: ".$project_site."<br>Total Amount: ".$request->input('total_amount')."<br>Request Amount: ".$request->input('request_amount')."<br>Payment Mode: ".$request->input('mode_of_payment')."<br>Schedule Date: ".$request->input('schedule_date');
        $budhet_sheet_no = BudgetSheetApproval::where('id', $request->input('id'))->value('budhet_sheet_no');
        $action_data = [
            'user_id' => Auth::user()->id,
            'task_body' => $budhet_sheet_no . " budget sheet number data updated".$add_string,
            'created_ip' => $request->ip(),
        ];
        $this->user_action_logs->action($action_data);

        if ($request->hasFile('budget_sheet_file')) {

            foreach ($request->budget_sheet_file as $budget_sheet_file) {
                // set uniuqe name .
                $original_file_name = explode('.', $budget_sheet_file->getClientOriginalName());

                $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);
                // store image to directory.
                $path = $budget_sheet_file->storeAs('public/budget_sheet_file', $new_file_name);
                //$path = basename($path);

                $budget_sheet_file_arr = [
                    'budget_sheet_id' => $request->input('id'),
                    'budget_sheet_file' => $path,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_ip' => $request->ip(),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip()
                ];

                Budget_sheet_file::insert($budget_sheet_file_arr);
            }
        }

        $admin_user_data = User::where('role', config('constants.Admin'))->get();

        $mail_data = [
            'to_user_name' => $admin_user_data[0]->name,
            'budget_sheet_number' => $budget_sheet_arr['meeting_number'],
            'to_email' => $admin_user_data[0]->email,
            'request_user_name' => Auth::user()->name
        ];
        $this->common_task->budgetSheetRequestEmail($mail_data);
        $this->notification_task->budgetSheetRequestNotify([$admin_user_data[0]->id], Auth::user()->name, $budget_sheet_arr['meeting_number']);
        return redirect()->route('admin.budget_sheet')->with('success', 'Budget Sheet successfully updated.');
    }

    public function budget_sheet_list() {

        if (Auth::user()->role != config('constants.SuperUser') && Auth::user()->role != config('constants.Admin')) {
            return redirect()->route('admin.dashboard')->with('error', 'Access denied. You are not allowed to access this module.');
        }

        $this->data['page_title'] = "Budget Sheet Approvals";
        $access_level = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => 26])->get()->first();
        if (!empty($access_level)) {
            $this->data['access_rule'] = $access_level->access_level;
        }

        return view('admin.budget_sheet.payment_list', $this->data);
    }

    public function approve_budget($id, Request $request) {
        if (Auth::user()->role != config('constants.SuperUser') && Auth::user()->role != config('constants.Admin')) {
            return redirect()->route('admin.dashboard')->with('error', 'Access denied. You are not allowed to access this module.');
        }
        $this->data['page_title'] = "Approve Budget Sheet Entry";
        $this->data['budget_data'] = BudgetSheetApproval::where('budget_sheet_approval.id', $id)
                ->join('company', 'company.id', '=', 'budget_sheet_approval.company_id')
                ->join('department', 'department.id', '=', 'budget_sheet_approval.department_id')
                ->join('vendor', 'vendor.id', '=', 'budget_sheet_approval.vendor_id')
                ->join('project', 'project.id', '=', 'budget_sheet_approval.project_id')
                ->leftJoin('clients', 'clients.id', '=', 'budget_sheet_approval.client_id')
                ->leftJoin('project_sites', 'project_sites.id', '=', 'budget_sheet_approval.project_site_id')
                ->get([
            'budget_sheet_approval.*','clients.client_name','clients.location', 'project_sites.site_name',
            'company.company_name', 'department.dept_name',
            'vendor.vendor_name',
            'project.project_name'
        ]);
        $this->data['previous_hold_request'] = BudgetSheetApproval::join('users', 'users.id', '=', 'budget_sheet_approval.user_id')->where('is_hold', '=', 'Yes')->get(['budget_sheet_approval.*', 'users.name']);
        return view('admin.budget_sheet.approve_budget', $this->data);
    }

    public function reject_budget($id, Request $request) {
        if (Auth::user()->role != config('constants.SuperUser') && Auth::user()->role != config('constants.Admin')) {
            return redirect()->route('admin.dashboard')->with('error', 'Access denied. You are not allowed to access this module.');
        }

        $this->data['page_title'] = "Reject Budget Sheet Entry";
        $this->data['budget_data'] = BudgetSheetApproval::where('budget_sheet_approval.id', $id)
                ->join('company', 'company.id', '=', 'budget_sheet_approval.company_id')
                ->join('department', 'department.id', '=', 'budget_sheet_approval.department_id')
                ->join('vendor', 'vendor.id', '=', 'budget_sheet_approval.vendor_id')
                ->join('project', 'project.id', '=', 'budget_sheet_approval.project_id')
                ->leftJoin('clients', 'clients.id', '=', 'budget_sheet_approval.client_id')
                    ->leftJoin('project_sites', 'project_sites.id', '=', 'budget_sheet_approval.project_site_id')
                ->get([
            'budget_sheet_approval.*','clients.client_name','clients.location', 'project_sites.site_name',
            'company.company_name', 'department.dept_name',
            'vendor.vendor_name',
            'project.project_name'
        ]);

        return view('admin.budget_sheet.reject_budget', $this->data);
    }

    public function reject_budget_sheet_entry(Request $request) {
        $validator_normal = Validator::make($request->all(), [
                    'reject_note' => 'required',
        ]);

        if ($validator_normal->fails()) {
            return redirect()->route('admin.budget_sheet_list')->with('error', 'Please follow validation rules.');
        }

        $request_data = $request->all();

        $update_arr = [
            'reject_note' => $request_data['reject_note'],
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id,
        ];

        if (Auth::user()->role == config('constants.Admin')) {
            $update_arr['first_approval_status'] = "Rejected";
            $update_arr['first_approval_id'] = Auth::user()->id;
            $update_arr['status'] = "Rejected";
        } elseif (Auth::user()->role == config('constants.SuperUser')) {
            $update_arr['second_approval_status'] = "Rejected";
            $update_arr['second_approval_id'] = Auth::user()->id;
            $update_arr['status'] = "Rejected";
        } else {
            return redirect()->route('admin.budget_sheet_list')->with('error', 'Access Denied');
        }

        BudgetSheetApproval::where('id', $request_data['id'])->update($update_arr);
        $budget_sheet_detail = BudgetSheetApproval::join('users', 'users.id', '=', 'budget_sheet_approval.user_id')->where('budget_sheet_approval.id', $request_data['id'])->get(['budget_sheet_approval.*', 'users.name', 'users.email']);

        $this->notification_task->rejectBudgetSheetNotify([$budget_sheet_detail[0]->user_id], $budget_sheet_detail[0]->meeting_number);

        $mail_data = [
            'username' => $budget_sheet_detail[0]->name,
            'budget_meeting_number' => $budget_sheet_detail[0]->meeting_number,
            'status' => $budget_sheet_detail[0]->status,
            'email' => $budget_sheet_detail[0]->email
        ];

        // User Action Log
        $budhet_sheet_no = BudgetSheetApproval::where('id', $request_data['id'])->value('budhet_sheet_no');
        $action_data = [
            'user_id' => Auth::user()->id,
            'task_body' => $budhet_sheet_no . " budget sheet number rejected",
            'created_ip' => $request->ip(),
        ];
        $this->user_action_logs->action($action_data);

        $this->common_task->approveRejectBudgetEmail($mail_data);
        return redirect()->route('admin.budget_sheet_list')->with('success', 'Budget sheet entry rejected.');
    }

    // This approve and reject policy by HR
    public function approve_budget_sheet($id) {
        $check_result = Permissions::checkPermission(26, 2);

        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }

        if (Auth::user()->role == config('constants.Admin')) {
            $updateData = [
                'first_approval_status' => 'Approved', 
                'first_approval_id' => Auth::user()->id,
                'first_approval_datetime' => date('Y-m-d H:i:s'),
                'release_amount_first_approval_datetime' => date('Y-m-d H:i:s'),
            ];

            //send notification about rejected
            $this->notification_task->budgetSheetFirstApprovalNotify([$this->super_admin->id]);
        }

        if (Auth::user()->role == config('constants.SuperUser')) {

            $budgetData = BudgetSheetApproval::select('users.name', 'budget_sheet_approval.budget_sheet_file', 'users.email', 'users.id as user_id')
                            ->join('users', 'budget_sheet_approval.user_id', '=', 'users.id')
                            ->where('budget_sheet_approval.id', $id)->get();
            $data = [
                'username' => $budgetData[0]['name'],
                'email' => $budgetData[0]['email'],
                'name' => $budgetData[0]['budget_sheet_file'],
                'status' => 'Approved',
            ];

            $this->common_task->approveRejectBudgetEmail($data);

            //send notification to user who requested about approval
            $this->notification_task->budgetSheetSecondApprovalNotify([$budgetData[0]->user_id]);

            $updateData = ['first_approval_status' => 'Approved', 'first_approval_id' => Auth::user()->id,
            'first_approval_datetime' => date('Y-m-d H:i '), 
            'release_amount_first_approval_datetime' => date('Y-m-d H:i:s'),
            'second_approval_status' => 'Approved', 
            'second_approval_id' => Auth::user()->id, 
            'second_approval_datetime' => date('Y-m-d H:i:s'),
            'status' => 'Approved'];
        }

        if (BudgetSheetApproval::where('id', $id)->update($updateData)) {
            return redirect()->route('admin.budget_sheet_list')->with('success', 'Status successfully updated.');
        }
        return redirect()->route('admin.budget_sheet_list')->with('error', 'Error during operation. Try again!');
    }

    public function get_previous_hold_amt(Request $request) {
        $budget_detail = BudgetSheetApproval::where('id', $request->input('id'))->get()->first();
        $already_paid_amt = BudgetSheetApproval::where('previous_hold_id', $request->input('id'))->get()->sum('previous_hold_amount');
        $remain_amt = $budget_detail->hold_amount - $already_paid_amt;

        return response()->json(['status' => true, 'amount' => $remain_amt]);
    }

    // This approve and reject policy by HR
    public function reject_budget_sheet($id, $note) {
        $check_result = Permissions::checkPermission(26, 2);

        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }

        if (Auth::user()->role == config('constants.ACCOUNT_ROLE')) {
            $cashApprovealData = BudgetSheetApproval::select('users.name', 'budget_sheet_approval.budget_sheet_file', 'users.email')
                            ->join('users', 'budget_sheet_approval.user_id', '=', 'users.id')
                            ->where('budget_sheet_approval.id', $id)->get();
            $data = [
                'username' => $cashApprovealData[0]['name'],
                'email' => $cashApprovealData[0]['email'],
                'name' => $cashApprovealData[0]['budget_sheet_file'],
                'status' => 'Rejected',
            ];

            $updateData = ['reject_note' => $note, 'first_approval_status' => 'Rejected', 'first_approval_id' => Auth::user()->id, 'status' => 'Rejected'];

            $this->common_task->approveRejectBudgetEmail($data);

            //send notification about rejected
            $this->notification_task->budgetSheetRejectNotify([$this->super_admin->id]);
        }

        if (Auth::user()->role == config('constants.SuperUser')) {
            $cashApprovealData = BudgetSheetApproval::select('users.name', 'budget_sheet_approval.budget_sheet_file', 'users.id as user_id', 'users.email')
                            ->join('users', 'budget_sheet_approval.user_id', '=', 'users.id')
                            ->where('budget_sheet_approval.id', $id)->get();
            $data = [
                'username' => $cashApprovealData[0]['name'],
                'email' => $cashApprovealData[0]['email'],
                'name' => $cashApprovealData[0]['budget_sheet_file'],
                'status' => 'Rejected',
            ];

            $this->common_task->approveRejectBudgetEmail($data);

            //send notification to user who requested about approval
            $this->notification_task->budgetSheetRejectNotify([$cashApprovealData[0]->user_id]);
            $updateData = ['reject_note' => $note, 'second_approval_status' => 'Rejected', 'second_approval_id' => Auth::user()->id, 'status' => 'Rejected'];
        }


        if (BudgetSheetApproval::where('id', $id)->update($updateData)) {
            return redirect()->route('admin.budget_sheet_list')->with('success', 'Status successfully updated.');
        }
        return redirect()->route('admin.budget_sheet_list')->with('error', 'Error during operation. Try again!');
    }

    public function approve_budget_sheet_entry(Request $request) {
        $validator_normal = Validator::make($request->all(), [
                    'approved_amount' => 'required',
        ]);

        if ($validator_normal->fails()) {
            return redirect()->route('admin.budget_sheet_list')->with('error', 'Please follow validation rules.');
        }

        $request_data = $request->all();
        $budget_sheet_detail = BudgetSheetApproval::join('users', 'users.id', '=', 'budget_sheet_approval.user_id')->where('budget_sheet_approval.id', $request_data['id'])->get(['budget_sheet_approval.*', 'users.name', 'users.email']);

        $update_arr = [
            'approved_amount' => $request_data['approved_amount'],
            'hold_amount' => $request_data['hold_amount'],
            //'previous_hold_id' => $request_data['previous_hold_id'],
            //'previous_hold_amount' => $request_data['previous_hold_amount'],
            'approval_remark' => $request_data['approval_remark'],
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id,
            'final_approved_amount' => $request_data['approved_amount'],
            'remain_hold_amount' => $request_data['hold_amount'],
            'approval_remark' => $request_data['approval_remark'],
            // 'release_first_approve_datetime' => date('Y-m-d H:i:s'),
        ];

        if (Auth::user()->role == config('constants.Admin')) {
            $update_arr['first_approval_status'] = "Approved";
            $update_arr['first_approval_id'] = Auth::user()->id;
            $update_arr['first_approval_datetime'] = date('Y-m-d H:i:s A');
            $update_arr['release_amount_first_approval_datetime'] = date('Y-m-d H:i:s A');

            $mail_data = [
                'to_user_name' => $this->super_admin->name,
                'budget_sheet_number' => $budget_sheet_detail[0]->meeting_number,
                'to_email' => $this->super_admin->email,
                'request_user_name' => $budget_sheet_detail[0]->name
            ];
            $this->common_task->budgetSheetRequestEmail($mail_data);
            $this->notification_task->budgetSheetRequestNotify([$this->super_admin->id], $budget_sheet_detail[0]->name, $budget_sheet_detail[0]->meeting_number);
        } elseif (Auth::user()->role == config('constants.SuperUser')) {
            $update_arr['second_approval_status'] = "Approved";
            $update_arr['status'] = "Approved";
            $update_arr['second_approval_id'] = Auth::user()->id;
            $update_arr['second_approval_datetime'] = date('Y-m-d H:i:s A');




            $this->notification_task->approveBudgetSheetNotify([$budget_sheet_detail[0]->user_id], $budget_sheet_detail[0]->meeting_number);

            $mail_data = [
                'username' => $budget_sheet_detail[0]->name,
                'budget_meeting_number' => $budget_sheet_detail[0]->meeting_number,
                'status' => $budget_sheet_detail[0]->status,
                'email' => $budget_sheet_detail[0]->email
            ];
            $this->common_task->approveRejectBudgetEmail($mail_data);
        } else {
            return redirect()->route('admin.budget_sheet_list')->with('error', 'Access Denied.');
        }

        if ($request_data['hold_amount'] > 0) {
            $update_arr['is_hold'] = "Yes";
        }

        BudgetSheetApproval::where('id', $request_data['id'])->update($update_arr);
        // User Action Log
        $budhet_sheet_no = BudgetSheetApproval::where('id', $request_data['id'])->value('budhet_sheet_no');
        $action_data = [
            'user_id' => Auth::user()->id,
            'task_body' => $budhet_sheet_no . " budget sheet number approved",
            'created_ip' => $request->ip(),
        ];
        $this->user_action_logs->action($action_data);
        /* if ($request_data['previous_hold_id'] > 0) {
          $previos_hold_detail = BudgetSheetApproval::where('id', $request_data['previous_hold_id'])->get()->first();

          $paid_hold = BudgetSheetApproval::where('previous_hold_id', $request_data['previous_hold_id'])->get()->sum('previous_hold_amount');
          $remain_amount = $previos_hold_detail->hold_amount - $paid_hold;

          if ($remain_amount <= 0) {
          $hold_update_arr = [
          'is_hold' => 'No',
          'updated_at' => date('Y-m-d H:i:s'),
          'updated_ip' => $request->ip(),
          'updated_by' => Auth::user()->id
          ];
          BudgetSheetApproval::where('id', $request_data['previous_hold_id'])->update($hold_update_arr);
          }
          } */



        return redirect()->route('admin.budget_sheet_list')->with('success', 'Budget sheet entry successfully approved.');
    }

    public function hold_budget_sheet_list(Request $request) {
        $this->data['page_title'] = "Hold Budget Sheet List";
        $this->data['hold_list'] = BudgetSheetApproval::where('is_hold', 'Yes')->get();
        return view('admin.budget_sheet.hold_budget_sheet_list', $this->data);
    }

    public function get_hold_budget_sheet_list_ajax() {  //change
        $datatable_fields = [
            'budget_sheet_approval.meeting_number', 'budget_sheet_approval.meeting_date','budget_sheet_approval.budhet_sheet_no',
            'company.company_name','clients.client_name','clients.location','department.dept_name',
            'vendor.vendor_name', 'budget_sheet_approval.description',
            'budget_sheet_approval.remark_by_user', 'budget_sheet_approval.request_amount', 'budget_sheet_approval.schedule_date_from', 'budget_sheet_approval.schedule_date_to', 'budget_sheet_approval.mode_of_payment',
             'project.project_name','project_sites.site_name', 'budget_sheet_approval.total_amount', 'budget_sheet_approval.approved_amount',
            'budget_sheet_approval.purchase_order_number', 'budget_sheet_approval.purchase_order_date', 'budget_sheet_approval.bill_number', 'budget_sheet_approval.bill_date', 'budget_sheet_approval.approval_remark', 'budget_sheet_approval.hold_amount', 'budget_sheet_approval.release_hold_amount_status','budget_sheet_approval.final_approved_amount', 'budget_sheet_approval.status'
        ];
        $request = Input::all();

        /* if (Auth::user()->role == config('constants.ACCOUNT_ROLE')) {
          $conditions_array = ['budget_sheet_approval.status' => 'Pending'];
          }

          if (Auth::user()->role == config('constants.SuperUser')) {
          $conditions_array = [ 'budget_sheet_approval.status' => 'Pending'];
          } */

        if (Auth::user()->role == config('constants.SuperUser')) {
            $conditions_array = [
                'is_hold' => 'Yes',
                'budget_sheet_approval.status' => 'Approved',
                'budget_sheet_approval.release_hold_amount_status' => 'Pending',
                'budget_sheet_approval.release_amount_first_approval_status' => 'Approved',
                'budget_sheet_approval.release_amount_second_approval_status' => 'Pending',
            ];
        }elseif(Auth::user()->role == config('constants.Admin')){

            $conditions_array = [
                'is_hold' => 'Yes',
                'budget_sheet_approval.status' => 'Approved',
                'budget_sheet_approval.release_amount_first_approval_status' => 'Pending',
            ];
        }




        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'users';
        $join_str[0]['join_table_id'] = 'users.id';
        $join_str[0]['from_table_id'] = 'budget_sheet_approval.user_id';

        $join_str[1]['join_type'] = '';
        $join_str[1]['table'] = 'company';
        $join_str[1]['join_table_id'] = 'company.id';
        $join_str[1]['from_table_id'] = 'budget_sheet_approval.company_id';

        $join_str[2]['join_type'] = '';
        $join_str[2]['table'] = 'department';
        $join_str[2]['join_table_id'] = 'department.id';
        $join_str[2]['from_table_id'] = 'budget_sheet_approval.department_id';

        $join_str[3]['join_type'] = '';
        $join_str[3]['table'] = 'vendor';
        $join_str[3]['join_table_id'] = 'vendor.id';
        $join_str[3]['from_table_id'] = 'budget_sheet_approval.vendor_id';

        $join_str[4]['join_type'] = '';
        $join_str[4]['table'] = 'project';
        $join_str[4]['join_table_id'] = 'project.id';
        $join_str[4]['from_table_id'] = 'budget_sheet_approval.project_id';

        $join_str[5]['join_type'] = 'left';
        $join_str[5]['table'] = 'clients';
        $join_str[5]['join_table_id'] = 'clients.id';
        $join_str[5]['from_table_id'] = 'budget_sheet_approval.client_id';

        $join_str[6]['join_type'] = 'left';
        $join_str[6]['table'] = 'project_sites';
        $join_str[6]['join_table_id'] = 'project_sites.id';
        $join_str[6]['from_table_id'] = 'budget_sheet_approval.project_site_id';

        $getfiled = [
            'budget_sheet_approval.*',
            'company.company_name', 'department.dept_name',
            'vendor.vendor_name',
            'project.project_name','clients.client_name','clients.location', 'project_sites.site_name'
        ];
        $table = "budget_sheet_approval";

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);

        die();
    }

    public function manage_hold_amt($id) {
        if (Auth::user()->role != config('constants.SuperUser') && Auth::user()->role != config('constants.Admin')) {
            return redirect()->route('admin.hold_budget_sheet_list')->with('error', 'Error Occurred. Try Again!');
        }
        $this->data['budget_data'] = BudgetSheetApproval::where('budget_sheet_approval.id', $id)
                ->join('company', 'company.id', '=', 'budget_sheet_approval.company_id')
                ->join('department', 'department.id', '=', 'budget_sheet_approval.department_id')
                ->join('vendor', 'vendor.id', '=', 'budget_sheet_approval.vendor_id')
                ->join('project', 'project.id', '=', 'budget_sheet_approval.project_id')
                ->leftJoin('clients', 'clients.id', '=', 'budget_sheet_approval.client_id')
                ->leftJoin('project_sites', 'project_sites.id', '=', 'budget_sheet_approval.project_site_id')
                ->get([
            'budget_sheet_approval.*','clients.client_name','clients.location', 'project_sites.site_name',
            'company.company_name', 'department.dept_name',
            'vendor.vendor_name',
            'project.project_name'
        ]);
        if ($this->data['budget_data']->count() == 0) {
            return redirect()->route('admin.hold_budget_sheet_list')->with('error', 'Error Occurred. Try Again!');
        }

        $this->data['completed_hold_amount'] = Hold_budget_sheet::where('budget_sheet_id', $id)->get(['completed_amount'])->sum('completed_amount');
        $this->data['page_title'] = "Manage Hold Amount";
        return view('admin.budget_sheet.manage_hold_amt', $this->data);
    }

    public function update_hold_amount(Request $request) {
        $validator_normal = Validator::make($request->all(), [
                    'completed_amount' => 'required',
                    'id' => 'required'
        ]);

        if ($validator_normal->fails()) {
            return redirect()->back()->with('error', 'Please follow validation rules.');
        }

        $request_data = $request->all();
        $update_arr = [];

        //check if value is still hold
        $completed_amt = Hold_budget_sheet::where('budget_sheet_id', $request_data['id'])->get(['completed_amount'])->sum('completed_amount');
        $hold_detail = BudgetSheetApproval::where('id', $request_data['id'])->get(['hold_amount', 'meeting_number', 'user_id', 'remain_hold_amount']);

        if (Auth::user()->role == config('constants.Admin')) {
            if($request_data['approval_status'] == "Rejected"){
                $update_arr['release_amount_first_approval_status'] = $request_data['approval_status'];
                $update_arr['release_amount_first_reject_note'] = $request_data['note'];
                $update_arr['release_amount_first_approval_id'] = Auth::user()->id;
                $update_arr['release_amount_first_approval_datetime'] = date('Y-m-d h:i:s');

                //Reject Email

            }else{
                $update_arr['release_amount_first_approval_status'] = $request_data['approval_status'];
                $update_arr['release_amount_first_reject_note'] = $request_data['note'];
                $update_arr['release_amount_first_approval_id'] = Auth::user()->id;
                $update_arr['release_hold_amount'] = $request_data['completed_amount'];
                $update_arr['release_amount_first_approval_datetime'] = date('Y-m-d h:i:s');
            }
        }else if(Auth::user()->role == config('constants.SuperUser')){
            if ($request_data['approval_status'] == "Rejected") {
                $update_arr['release_amount_second_approval_status'] = $request_data['approval_status'];
                $update_arr['release_amount_second_reject_note'] = $request_data['note'];
                $update_arr['release_amount_second_approval_id'] = Auth::user()->id;
                $update_arr['release_amount_second_approval_datetime'] = date('Y-m-d h:i:s');

                //Reject Email

            } else {
                $update_arr['release_amount_second_approval_status'] = $request_data['approval_status'];
                $update_arr['remain_hold_amount'] = $hold_detail[0]->remain_hold_amount - $request_data['completed_amount'];
                $update_arr['release_hold_amount'] = $request_data['completed_amount'];
                $update_arr['release_hold_amount_status'] = 'Approved';
                $update_arr['release_amount_second_approval_datetime'] = date('Y-m-d h:i:s');

                if ($completed_amt >= $hold_detail[0]->hold_amount) {
                    $update_arr['is_hold'] = 'No';
                }

                $hold_arr = [
                    'budget_sheet_id' => $request_data['id'],
                    'completed_amount' => $request_data['completed_amount'],
                    'note' => $request_data['note'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_ip' => $request->ip(),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                    'updated_by' => Auth::user()->id
                ];
                Hold_budget_sheet::insert($hold_arr);

            }
        }else{
            return redirect()->back()->with('error', 'Access Denied.');
        }
        if ($request_data['approval_status'] == "Rejected"){
            // User Action Log
            $budhet_sheet_no = BudgetSheetApproval::where('id', $request_data['id'])->value('budhet_sheet_no');
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => $budhet_sheet_no . " budget sheet number hold amount request rejected",
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);
        }else{
            // User Action Log
            $budhet_sheet_no = BudgetSheetApproval::where('id', $request_data['id'])->value('budhet_sheet_no');
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => $budhet_sheet_no . " budget sheet number hold amount request approved",
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);
        }


        $update_arr['updated_at'] = date('Y-m-d H:i:s');
        $update_arr['updated_ip'] = $request->ip();
        $update_arr['updated_by'] = Auth::user()->id;
        // dd($update_arr);
        BudgetSheetApproval::where('id', $request_data['id'])->update($update_arr);

        $buget_data = BudgetSheetApproval::whereId($request_data['id'])->first();
        $user_data = User::where('id', $buget_data['user_id'])->first();
        $user_email = User::where('id', $buget_data['user_id'])->pluck('email')->toArray();

        // Reject Email
        if ($request_data['approval_status'] == "Rejected") {
            if (Auth::user()->role == config('constants.SuperUser')){
                $reject_user = "Super Admin";
            }else{
                $reject_user = "Admin";
            }
            $mail_data = [
                'to_email' => $user_email,
                'budget_sheet_no' => $buget_data['budhet_sheet_no'],
                'completed_amount' => $request_data['completed_amount'],
                'name' => $user_data['name'],
                'action' => $reject_user
            ];
            $this->common_task->budgetSheetReleaseAmountRejectEmail($mail_data);
        }

        if (Auth::user()->role == config('constants.SuperUser')) {
            if ($request_data['approval_status'] == "Approved") {
                if ($request_data['completed_amount'] != $request_data['old_release_hold_amount']) {
                    $mail_data = [
                        'to_email' => $user_email,
                        'budget_sheet_no' => $buget_data['budhet_sheet_no'],
                        'old_release_hold_amount' => $request_data['old_release_hold_amount'],
                        'completed_amount' => $request_data['completed_amount'],
                        'name' => $user_data['name'],
                    ];
                    $this->common_task->budgetSheetReleaseAmountChangeEmail($mail_data);
                }

                $this->notification_task->holdAmountReleaseNotify([$hold_detail[0]->user_id], $hold_detail[0]->meeting_number);
            }
        }

        return redirect()->route('admin.hold_budget_sheet_list')->with('Hold entry successfully updated.');
    }

    public function budget_sheet_report(Request $request) {


        if ($request->method() == 'POST') {

            $this->data['report_data'] = BudgetSheetApproval::where(['budget_sheet_year' => $request->input('budget_sheet_year'), 'budget_sheet_week' => $request->input('budget_sheet_week')])
                    ->join('company', 'company.id', '=', 'budget_sheet_approval.company_id')
                    ->join('department', 'department.id', '=', 'budget_sheet_approval.department_id')
                    ->join('vendor', 'vendor.id', '=', 'budget_sheet_approval.vendor_id')
                    ->join('project', 'project.id', '=', 'budget_sheet_approval.project_id')
                    ->leftJoin('clients', 'clients.id', '=', 'budget_sheet_approval.client_id')
                    ->leftJoin('project_sites', 'project_sites.id', '=', 'budget_sheet_approval.project_site_id')
                    ->orderBy('budget_sheet_approval.payment_date', 'desc')
                    ->get([
                'budget_sheet_approval.*','clients.client_name','clients.location', 'project_sites.site_name',
                'company.company_name', 'department.dept_name',
                'vendor.vendor_name',
                'project.project_name'
            ]);
            $this->data['selected_week'] = $request->input('budget_sheet_week');
            $this->data['selected_year'] = $request->input('budget_sheet_year');

        } else {
            $this->data['report_data'] = BudgetSheetApproval::where(['budget_sheet_year' => date('Y'), 'budget_sheet_week' => date('W')])
                    ->join('company', 'company.id', '=', 'budget_sheet_approval.company_id')
                    ->join('department', 'department.id', '=', 'budget_sheet_approval.department_id')
                    ->join('vendor', 'vendor.id', '=', 'budget_sheet_approval.vendor_id')
                    ->join('project', 'project.id', '=', 'budget_sheet_approval.project_id')
                    ->leftJoin('clients', 'clients.id', '=', 'budget_sheet_approval.client_id')
                    ->leftJoin('project_sites', 'project_sites.id', '=', 'budget_sheet_approval.project_site_id')
                    ->orderBy('budget_sheet_approval.payment_date', 'desc')
                    ->get([
                'budget_sheet_approval.*','clients.client_name','clients.location', 'project_sites.site_name',
                'company.company_name', 'department.dept_name',
                'vendor.vendor_name',
                'project.project_name'
            ]);
            $this->data['selected_week'] = date('W');
            $this->data['selected_year'] = date('Y');
        }
        $this->data['page_title'] = "Budget Sheet Report";
        $this->data['min_year'] = BudgetSheetApproval::min('budget_sheet_year');
        $this->data['max_year'] = BudgetSheetApproval::max('budget_sheet_year');
        return view('admin.budget_sheet.budget_sheet_report', $this->data);
    }

    public  function budget_sheet_reportByDate(Request $request)
    {


        $date = $request->get('meeting_date');

            $mainDate = explode("-", $date);
            $strFirstdate = str_replace("/", "-", $mainDate[0]);
            $strLastdate = str_replace("/", "-", $mainDate[1]);
            $first_date = date('Y-m-d h:m:s', strtotime($strFirstdate.' -1 day'));
            $second_date = date('Y-m-d h:m:s', strtotime($strLastdate.' +1 day'));


        $meetingDate = date('Y-m-d', strtotime($request->input('meeting_date')));
        $this->data['report_data'] = BudgetSheetApproval::join('company', 'company.id', '=', 'budget_sheet_approval.company_id')
                    ->join('department', 'department.id', '=', 'budget_sheet_approval.department_id')
                    ->join('vendor', 'vendor.id', '=', 'budget_sheet_approval.vendor_id')
                    ->join('project', 'project.id', '=', 'budget_sheet_approval.project_id')
                    ->leftJoin('clients', 'clients.id', '=', 'budget_sheet_approval.client_id')
                    ->leftJoin('project_sites', 'project_sites.id', '=', 'budget_sheet_approval.project_site_id')
                    ->whereBetween('budget_sheet_approval.meeting_date' ,[$first_date, $second_date])
                    ->orderBy('budget_sheet_approval.payment_date', 'desc')
                    ->get([
                'budget_sheet_approval.*','clients.client_name','clients.location', 'project_sites.site_name',
                'company.company_name', 'department.dept_name',
                'vendor.vendor_name',
                'project.project_name'
            ]);

            $this->data['selected_week'] = date('W');
            $this->data['selected_year'] = date('Y');

        $this->data['page_title'] = "Budget Sheet Report";
        $this->data['min_year'] = BudgetSheetApproval::min('budget_sheet_year');
        $this->data['max_year'] = BudgetSheetApproval::max('budget_sheet_year');

        return view('admin.budget_sheet.budget_sheet_report', $this->data);
    }

    public function payment_done_budgetSheet($id) {

        $update_arr = [
            'payment_status' => 'Done',
            'payment_date' => date('Y-m-d H:i:s'),
            'payment_user_id' => Auth::user()->id
        ];

        if (BudgetSheetApproval::where('id',$id)->update($update_arr)) {

            return redirect()->route('admin.budget_sheet')->with('success', 'Budget Sheet payment is successfully done !.');
        }
        return redirect()->route('admin.budget_sheet')->with('error', 'Oops! Something went wrong.');


    }

    public function check_purchase_order_number(Request $request){

        if(!empty($request->get('id'))){
            $check = BudgetSheetApproval::whereNotIn('id',[$request->get('id')])->where('purchase_order_number', $request->get('purchase_order_number'))->exists();
            if ($check) {
                $msg = false;
            } else {
                $msg = true;
            }
            echo json_encode($msg);die;
        }else{
            $purchase_number = BudgetSheetApproval::where('purchase_order_number', $request->get('purchase_order_number'))->count();
            if ($purchase_number) {
                echo "error";
                die();
            } else {
                echo "success";
                die();
            }
        }
    }

    public function check_bill_number(Request $request){
        if (!empty($request->get('id'))) {
            $check = BudgetSheetApproval::whereNotIn('id', [$request->get('id')])->where('bill_number', $request->get('bill_number'))->exists();
            if ($check) {
                $msg = false;
            } else {
                $msg = true;
            }
            echo json_encode($msg);
            die;
        }else{
            $purchase_number = BudgetSheetApproval::where('bill_number',$request->get('bill_number'))->count();
            if($purchase_number){
                echo "error";
                die();
            }else{
                echo "success";
                die();
            }
        }
    }

    public function your_hold_budget_sheet_list(){
        $this->data['page_title'] = "Your Hold Budget Sheet List";
        $this->data['hold_list'] = BudgetSheetApproval::where('is_hold', 'Yes')->get();
        return view('admin.budget_sheet.your_hold_budget_sheet_list', $this->data);
    }

    public function get_your_hold_budget_sheet_list_ajax()
    {  //change
        $datatable_fields = [
            'budget_sheet_approval.meeting_number', 'budget_sheet_approval.meeting_date', 'budget_sheet_approval.budhet_sheet_no',
            'company.company_name', 'clients.client_name', 'clients.location', 'department.dept_name',
            'vendor.vendor_name', 'budget_sheet_approval.description',
            'budget_sheet_approval.remark_by_user', 'budget_sheet_approval.request_amount', 'budget_sheet_approval.schedule_date_from', 'budget_sheet_approval.schedule_date_to', 'budget_sheet_approval.mode_of_payment',
            'project.project_name', 'project_sites.site_name', 'budget_sheet_approval.total_amount', 'budget_sheet_approval.approved_amount',
            'budget_sheet_approval.purchase_order_number', 'budget_sheet_approval.purchase_order_date', 'budget_sheet_approval.bill_number', 'budget_sheet_approval.bill_date', 'budget_sheet_approval.approval_remark', 'budget_sheet_approval.hold_amount', 'budget_sheet_approval.final_approved_amount', 'budget_sheet_approval.status', 'budget_sheet_approval.release_amount_first_approval_status', 'budget_sheet_approval.release_amount_second_approval_status'
        ];
        $request = Input::all();

        /* if (Auth::user()->role == config('constants.ACCOUNT_ROLE')) {
          $conditions_array = ['budget_sheet_approval.status' => 'Pending'];
          }

          if (Auth::user()->role == config('constants.SuperUser')) {
          $conditions_array = [ 'budget_sheet_approval.status' => 'Pending'];
          } */
        $conditions_array = [
            'budget_sheet_approval.user_id' => Auth::user()->id,
            'is_hold' => 'Yes',
            'budget_sheet_approval.status' => 'Approved',
        ];


        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'users';
        $join_str[0]['join_table_id'] = 'users.id';
        $join_str[0]['from_table_id'] = 'budget_sheet_approval.user_id';

        $join_str[1]['join_type'] = '';
        $join_str[1]['table'] = 'company';
        $join_str[1]['join_table_id'] = 'company.id';
        $join_str[1]['from_table_id'] = 'budget_sheet_approval.company_id';

        $join_str[2]['join_type'] = '';
        $join_str[2]['table'] = 'department';
        $join_str[2]['join_table_id'] = 'department.id';
        $join_str[2]['from_table_id'] = 'budget_sheet_approval.department_id';

        $join_str[3]['join_type'] = '';
        $join_str[3]['table'] = 'vendor';
        $join_str[3]['join_table_id'] = 'vendor.id';
        $join_str[3]['from_table_id'] = 'budget_sheet_approval.vendor_id';

        $join_str[4]['join_type'] = '';
        $join_str[4]['table'] = 'project';
        $join_str[4]['join_table_id'] = 'project.id';
        $join_str[4]['from_table_id'] = 'budget_sheet_approval.project_id';

        $join_str[5]['join_type'] = 'left';
        $join_str[5]['table'] = 'clients';
        $join_str[5]['join_table_id'] = 'clients.id';
        $join_str[5]['from_table_id'] = 'budget_sheet_approval.client_id';

        $join_str[6]['join_type'] = 'left';
        $join_str[6]['table'] = 'project_sites';
        $join_str[6]['join_table_id'] = 'project_sites.id';
        $join_str[6]['from_table_id'] = 'budget_sheet_approval.project_site_id';

        $getfiled = [
            'budget_sheet_approval.*',
            'company.company_name', 'department.dept_name',
            'vendor.vendor_name',
            'project.project_name', 'clients.client_name', 'clients.location', 'project_sites.site_name'
        ];
        $table = "budget_sheet_approval";

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);

        die();
    }

    public function release_hold_amount(Request $request){   //copy

        $validator_normal = Validator::make($request->all(), [
            'release_hold_amount' => 'required',
            'budget_sheet_id' => 'required',
        ]);

        if ($validator_normal->fails()) {
            return redirect()->route('admin.your_hold_budget_sheet_list')->with('error', 'Please follow validation rules.');
        }

        $id = $request->get('budget_sheet_id');

        $update = [
            'release_hold_amount_status' => "Pending",
            'release_hold_amount' => $request->get('release_hold_amount'),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id,
        ];
        if (Auth::user()->role == config('constants.Admin')) {
            $update['release_amount_first_approval_status'] = "Approved";
            $update['release_amount_first_approval_id'] = Auth::user()->id;
            $update['release_amount_first_reject_note'] = NULL;
            $update['release_amount_second_approval_status'] = "Pending";
        }else{
            $update['release_amount_first_approval_status'] = "Pending";
            $update['release_amount_second_approval_status'] = "Pending";
            $update['release_amount_second_reject_note'] = NULL;
            $update['release_amount_first_reject_note'] = NULL;
        }
        //---------------------------
        // if ($request->hasFile('invoice_file')) {
        //     $invoice_file = $request->file('invoice_file');

        //     $file_path = $invoice_file->store('public/invoice_file');
        //     $update['invoice_file'] = $file_path;
        // }

        //---------------------------
        // dd($update);
        $buget_data = BudgetSheetApproval::whereId($id)->first();
        if($buget_data){
            if(BudgetSheetApproval::whereId($id)->update($update)){

                //------------------------------------------- 07/09/2020
                if ($request->hasFile('budget_sheet_file')) {

                    foreach ($request->budget_sheet_file as $file_name) {
                        // store image to directory.

                            $original_file_name = explode('.', $file_name->getClientOriginalName());

                            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);

                            $path = $file_name->storeAs('public/budget_sheet_file', $new_file_name);


                        $budget_files_arr = [

                            'budget_sheet_id' => $id,
                            'budget_sheet_file' => $path,
                            'created_at' => date('Y-m-d H:i:s'),
                            'created_ip' => $request->ip(),
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_ip' => $request->ip()
                        ];

                        \App\Budget_sheet_file::insert($budget_files_arr);
                    }
                }
                //---------- Invoice file-------------------
                $document_file = '';
                if ($request->file('invoice_file')) {

                    $document_file = $request->file('invoice_file');
                    $original_file_name = explode('.', $document_file->getClientOriginalName());
                    $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);

                    $file_path = $document_file->storeAs('public/budget_sheet_invoice_files', $new_file_name);
                    if ($file_path) {
                        $document_file = $file_path;
                    }
                    $budget_invoice_arr = [

                        'budget_sheet_id' => $id,
                        'budget_sheet_invoice_file' => $document_file,
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_ip' => $request->ip(),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_ip' => $request->ip()
                    ];

                    \App\Budget_sheet_invoice_files::insert($budget_invoice_arr);
                }

                //--------------------------------------------
                $admin_email = User::where('role', config('constants.SuperUser'))->pluck('email')->toArray();
                $user_data = User::where('id', $buget_data['user_id'])->first();
                $mail_data = [
                    'to_email' => $admin_email,
                    'request_by' => $user_data['name'],
                    'budget_sheet_no' => $buget_data['budhet_sheet_no'],
                ];
                $this->common_task->budgetSheetReleaseAmountRequestEmail($mail_data);
                return redirect()->route('admin.your_hold_budget_sheet_list')->with('success', 'Release amount request submitted successfully.');
            }else{
                return redirect()->route('admin.your_hold_budget_sheet_list')->with('error', 'Error during operation. Try again!');
            }
        }else{
            return redirect()->route('admin.your_hold_budget_sheet_list')->with('error', 'Error during operation. Try again!');
        }

    }

     //07/09/2020
     public function get_invoice_files(Request $request) {

        $validator_normal = Validator::make($request->all(), ['id' => 'required']);

        if ($validator_normal->fails()) {
            return response()->json(['status' => false]);
        }

        $budget_sheet_id = $request->id;

        $invoice_files = Budget_sheet_invoice_files::where('budget_sheet_id', $budget_sheet_id)
                ->get(['id', 'budget_sheet_id', 'budget_sheet_invoice_file']);

        foreach ($invoice_files as $key => $files) {
            $invoice_files[$key]->file_name = str_replace('public/budget_sheet_invoice_files/', '', $files->budget_sheet_invoice_file);

            if ($files->budget_sheet_invoice_file) {

                $invoice_files[$key]->budget_sheet_invoice_file = asset('storage/' . str_replace('public/', '', $files->budget_sheet_invoice_file));
            } else {

                $invoice_files[$key]->budget_sheet_invoice_file = "";
            }

        }

        $this->data['invoice_files'] = $invoice_files;

        if ($invoice_files->count() == 0) {
            return response()->json(['status' => false, 'data' => $this->data]);
        } else {
            return response()->json(['status' => true, 'data' => $this->data]);
        }
    }

    public function manage_your_hold_amt($id){    //copy
        if (Auth::user()->role == config('constants.SuperUser') || Auth::user()->role == config('constants.Admin')) {
            return redirect()->route('admin.your_hold_budget_sheet_list')->with('error', 'Error Occurred. Try Again!');
        }
        $this->data['budget_data'] = BudgetSheetApproval::where('budget_sheet_approval.id', $id)
        ->join('company', 'company.id', '=', 'budget_sheet_approval.company_id')
        ->join('department', 'department.id', '=', 'budget_sheet_approval.department_id')
        ->join('vendor', 'vendor.id', '=', 'budget_sheet_approval.vendor_id')
        ->join('project', 'project.id', '=', 'budget_sheet_approval.project_id')
        ->leftJoin('clients', 'clients.id', '=', 'budget_sheet_approval.client_id')
        ->leftJoin('project_sites', 'project_sites.id', '=', 'budget_sheet_approval.project_site_id')
        ->get([
            'budget_sheet_approval.*', 'clients.client_name', 'clients.location', 'project_sites.site_name',
            'company.company_name', 'department.dept_name',
            'vendor.vendor_name',
            'project.project_name'
        ]);
        if ($this->data['budget_data']->count() == 0) {
            return redirect()->route('admin.your_hold_budget_sheet_list')->with('error', 'Error Occurred. Try Again!');
        }

        $this->data['completed_hold_amount'] = Hold_budget_sheet::where('budget_sheet_id', $id)->get(['completed_amount'])->sum('completed_amount');
        $this->data['page_title'] = "Manage Hold Amount";
        $this->data['budget_sheet_id'] = $id;
        return view('admin.budget_sheet.your_manage_hold_amt', $this->data);
    }
}
