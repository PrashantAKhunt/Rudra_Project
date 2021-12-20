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
use App\RevisePolicy;
use App\UserRevisePolicy;
use App\CashApproval;
use Illuminate\Support\Facades\Mail;
use App\Lib\CommonTask;
use App\Lib\NotificationTask;
use App\Companies;
use App\Projects;
use App\Vendors;
use App\Clients;
use App\Project_sites;
use App\AssignedVoucher;
use App\VoucherNumberRegister;
use App\Company_cash_management;
use App\Cash_transfer;
use App\Lib\UserActionLogs;

class CashApprovalController extends Controller
{

    public $data;
    public $notification_task;
    public $common_task;
    private $super_admin;
    public $user_action_logs;
    private $module_id = 25;

    public function __construct()
    {
        $this->data['module_title'] = "Cash Payment";
        $this->data['module_link'] = "admin.cash_payment";
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();
        $this->user_action_logs = new UserActionLogs();
        $this->super_admin = \App\User::where('role', 1)->first();
    }

    public function index()
    {
        $this->data['page_title'] = "Cash Payment";
        $access_level = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => $this->module_id])->get()->first();
        $this->data['access_rule'] = "";
        if (!empty($access_level)) {
            $this->data['access_rule'] = $access_level->access_level;
        }

        return view('admin.cash_payment.index', $this->data);
    }

    public function get_cash_payment_list()  //this
    {

        $datatable_fields = array(

            'cash_approval.payment_options',
            'budget_sheet_approval.budhet_sheet_no',
            'cash_approval.entry_code',
            'cash_approval.title',
            'company.company_name',
            'clients.client_name',
            //'clients.location',
            'project.project_name',
            'other_cash_detail',
            'project_sites.site_name',
            'vendor.vendor_name',
            'request_user.name',
            'expence_done_user.name',
            'amount',
            'cash_approval.igst_amount',
            'cash_approval.cgst_amount',
            'cash_approval.sgst_amount',
            'note',
            'vender_invoice_no',
            'cash_approval.first_approval_status',
            'cash_approval.second_approval_status',
            'cash_approval.third_approval_status',
            'cash_approval.status',
            'cash_approval.created_at');
        $request = Input::all();
        $conditions_array = ['cash_approval.user_id' => Auth::user()->id];

        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'users';
        $join_str[0]['join_table_id'] = 'users.id';
        $join_str[0]['from_table_id'] = 'cash_approval.user_id';

        $join_str[1]['join_type'] = '';
        $join_str[1]['table'] = 'company';
        $join_str[1]['join_table_id'] = 'company.id';
        $join_str[1]['from_table_id'] = 'cash_approval.company_id';

        $join_str[2]['join_type'] = '';
        $join_str[2]['table'] = 'project';
        $join_str[2]['join_table_id'] = 'project.id';
        $join_str[2]['from_table_id'] = 'cash_approval.project_id';

        $join_str[3]['join_type'] = '';
        $join_str[3]['table'] = 'vendor';
        $join_str[3]['join_table_id'] = 'vendor.id';
        $join_str[3]['from_table_id'] = 'cash_approval.vendor_id';

        $join_str[4]['join_type'] = 'left';
        $join_str[4]['table'] = 'clients';
        $join_str[4]['join_table_id'] = 'clients.id';
        $join_str[4]['from_table_id'] = 'cash_approval.client_id';

        $join_str[5]['join_type'] = 'left';
        $join_str[5]['table'] = 'project_sites';
        $join_str[5]['join_table_id'] = 'project_sites.id';
        $join_str[5]['from_table_id'] = 'cash_approval.project_site_id';

        $join_str[6]['join_type'] = 'left';
        $join_str[6]['table'] = 'budget_sheet_approval';
        $join_str[6]['join_table_id'] = 'budget_sheet_approval.id';
        $join_str[6]['from_table_id'] = 'cash_approval.budget_sheet_id';

        $join_str[7]['join_type'] = 'left';
        $join_str[7]['table'] = 'users as request_user';
        $join_str[7]['join_table_id'] = 'request_user.id';
        $join_str[7]['from_table_id'] = 'cash_approval.requested_by';

        $join_str[8]['join_type'] = 'left';
        $join_str[8]['table'] = 'users as expence_done_user';
        $join_str[8]['join_table_id'] = 'expence_done_user.id';
        $join_str[8]['from_table_id'] = 'cash_approval.expence_done_by';

        $getfiled = array('cash_approval.*','budget_sheet_approval.budhet_sheet_no',  'company.company_name','clients.client_name','clients.location', 'project.project_name','project_sites.site_name', 'vendor.vendor_name', 'request_user.name as requested_by_name', 'expence_done_user.name as expence_done_name');
        $table = "cash_approval";

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);

        die();
    }

    public function get_cash_payment_list_ajax()
    {

        $datatable_fields = array(

                'cash_approval.payment_options',
                'budget_sheet_approval.budhet_sheet_no',
                'cash_approval.entry_code',
                'users.name',

                'cash_approval.title',
                'company.company_name',
                'clients.client_name',
                //'clients.location',
                'project.project_name',
                'other_cash_detail',
                'project_sites.site_name',
                'vendor.vendor_name',
                'request_user.name',
                'expence_done_user.name',
                'amount',
                'cash_approval.igst_amount',
                'cash_approval.cgst_amount',
                'cash_approval.sgst_amount',
                'note',
                'vender_invoice_no',
                'cash_approval.created_at',
                'cash_approval.first_approval_status',
                'cash_approval.second_approval_status',
                'cash_approval.third_approval_status',
                'cash_approval.status');
        $request = Input::all();

        if (Auth::user()->role == config('constants.Admin')) {
            $conditions_array = ['cash_approval.first_approval_status' => 'Pending'];
        } elseif (Auth::user()->role == config('constants.ACCOUNT_ROLE')) {
            $conditions_array = ['cash_approval.second_approval_status' => 'Pending', 'cash_approval.first_approval_status' => 'Approved'];
        } elseif (Auth::user()->role == config('constants.SuperUser')) {
            $conditions_array = ['cash_approval.second_approval_status' => 'Approved', 'cash_approval.first_approval_status' => 'Approved', 'cash_approval.status' => 'Pending'];
        } else {
            return [];
        }



        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'users';
        $join_str[0]['join_table_id'] = 'users.id';
        $join_str[0]['from_table_id'] = 'cash_approval.user_id';

        $join_str[1]['join_type'] = '';
        $join_str[1]['table'] = 'company';
        $join_str[1]['join_table_id'] = 'company.id';
        $join_str[1]['from_table_id'] = 'cash_approval.company_id';

        $join_str[2]['join_type'] = '';
        $join_str[2]['table'] = 'project';
        $join_str[2]['join_table_id'] = 'project.id';
        $join_str[2]['from_table_id'] = 'cash_approval.project_id';

        $join_str[3]['join_type'] = '';
        $join_str[3]['table'] = 'vendor';
        $join_str[3]['join_table_id'] = 'vendor.id';
        $join_str[3]['from_table_id'] = 'cash_approval.vendor_id';


        $join_str[4]['join_type'] = 'left';
        $join_str[4]['table'] = 'clients';
        $join_str[4]['join_table_id'] = 'clients.id';
        $join_str[4]['from_table_id'] = 'cash_approval.client_id';

        $join_str[5]['join_type'] = 'left';
        $join_str[5]['table'] = 'project_sites';
        $join_str[5]['join_table_id'] = 'project_sites.id';
        $join_str[5]['from_table_id'] = 'cash_approval.project_site_id';

        $join_str[6]['join_type'] = 'left';
        $join_str[6]['table'] = 'budget_sheet_approval';
        $join_str[6]['join_table_id'] = 'budget_sheet_approval.id';
        $join_str[6]['from_table_id'] = 'cash_approval.budget_sheet_id';

        $join_str[7]['join_type'] = 'left';
        $join_str[7]['table'] = 'users as request_user';
        $join_str[7]['join_table_id'] = 'request_user.id';
        $join_str[7]['from_table_id'] = 'cash_approval.requested_by';

        $join_str[8]['join_type'] = 'left';
        $join_str[8]['table'] = 'users as expence_done_user';
        $join_str[8]['join_table_id'] = 'expence_done_user.id';
        $join_str[8]['from_table_id'] = 'cash_approval.expence_done_by';

        $getfiled = array('cash_approval.*','budget_sheet_approval.budhet_sheet_no',  'users.name as user_name', 'company.company_name','clients.client_name','clients.location', 'project.project_name','project_sites.site_name','vendor.vendor_name', 'request_user.name as requested_by_name', 'expence_done_user.name as expence_done_name');
        $table = "cash_approval";

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);

        die();
    }

    public function add_cash_payment_detail()
    {
        $this->data['page_title'] = 'Add Cash Details';
        $this->data['Companies'] = Companies::select('id', 'company_name')->orderBy('company_name', 'asc')->get();
        $this->data['Projects'] = Projects::select('id', 'project_name')->orderBy('project_name', 'asc')->get();
        $this->data['users'] = User::whereStatus('Enabled')->orderBy('name')->pluck('name', 'id');
        return view('admin.cash_payment.add_payment', $this->data);
    }

    public function insert_cash_payment(Request $request)
    {

        $validator_normal = Validator::make($request->all(), [
            'title' => 'required',
            'company_id' => 'required',
            'project_id' => 'required',
            'amount' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_bank_payment_detail')->with('error', 'Please follow validation rules.');
        }
        $CashApproval = new CashApproval();
        $CashApproval->title = $request->input('title');

        $CashApproval->company_id = $request->input('company_id');
        $CashApproval->client_id = $request->input('client_id');

        $CashApproval->payment_options = $request->input('payment_options');
        if ($request->input('payment_options') == 'Budget Sheet') {
            $CashApproval->budget_sheet_id = $request->input('budget_sheet_id');
        }

        $CashApproval->project_type = $request->input('project_type');
        $CashApproval->project_id = $request->input('project_id');
        $CashApproval->project_site_id = $request->input('project_site_id');

        if (!empty($request->input('vendor_id'))) {
            $CashApproval->vendor_id = $request->input('vendor_id');
        }
        $CashApproval->requested_by = $request->input('requested_by');
        $CashApproval->expence_done_by = $request->input('expence_done_by');

        if ($request->input('project_id') == config('constants.OTHER_PROJECT_ID')) {
            $CashApproval->other_cash_detail = $request->input('other_cash_detail');
        }

        $total_tax = $request->input('igst_amount') + $request->input('cgst_amount') + $request->input('sgst_amount');

        $CashApproval->user_id = Auth::user()->id;
        $CashApproval->amount = $request->input('amount') + $total_tax;
        $CashApproval->created_at = date('Y-m-d H:i:s');
        $CashApproval->note = $request->input('note');
        $CashApproval->vender_invoice_no = $request->input('vender_invoice_no');
        $CashApproval->igst_amount = $request->input('igst_amount');
        $CashApproval->cgst_amount = $request->input('cgst_amount');
        $CashApproval->sgst_amount = $request->input('sgst_amount');
        $CashApproval->created_ip = $request->ip();
        $CashApproval->updated_at = date('Y-m-d H:i:s');
        $CashApproval->updated_ip = $request->ip();

        //ACCOUNT_ROLE
        if (Auth::user()->role == config('constants.Admin')) {
            $CashApproval->first_approval_status = "Approved";
            $CashApproval->first_approval_datetime =  date('Y-m-d H:i:s');  //change
            $CashApproval->first_approval_id = Auth::user()->id;
            $admin_user = User::where('role', config('constants.Admin'))->get(['id']);
            $this->notification_task->cashRequestFirstApprovalNotify([$admin_user[0]->id]);
        }

        if (Auth::user()->role == config('constants.SuperUser')) {

            $CashApproval->first_approval_status = "Approved";
            $CashApproval->first_approval_datetime = date('Y-m-d H:i:s');
            $CashApproval->first_approval_id = Auth::user()->id;
            $CashApproval->second_approval_status = "Approved";
            $CashApproval->second_approval_datetime = date('Y-m-d H:i:s');
            $CashApproval->second_approval_id = Auth::user()->id;
            $CashApproval->third_approval_status = "Approved";
            $CashApproval->third_approval_datetime = date('Y-m-d H:i:s');
            $CashApproval->third_approval_id = Auth::user()->id;
            $CashApproval->status = "Approved";

        }
        // dd($CashApproval);

            /* if ($request->hasFile('payment_file')) {
                $payment_file = $request->file('payment_file');
                $file_path = $payment_file->store('public/payment_file');
                if ($file_path) {
                    $CashApproval->payment_file = $file_path;
                }
            }
            */
        //21-02-2020
        if ($request->file('payment_file')) {

            /* $payment_file = $request->file('payment_file');
            $original_file_name = explode('.', $payment_file->getClientOriginalName());
            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);
            $file_path = $payment_file->storeAs('public/payment_file', $new_file_name);
            if ($file_path) {
                $CashApproval->payment_file = $file_path;
            } */

            // 04-08-2020
            $payment_file = $request->file('payment_file');
            $payment_file_arr = [];
            foreach ($payment_file as $key => $value) {

                $original_file_name = explode('.', $value->getClientOriginalName());
                $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);
                $file_path = $value->storeAs('public/payment_file', $new_file_name);
                $payment_file_arr[$key] = $file_path;
            }
            $CashApproval->payment_file = implode(',', $payment_file_arr);
        }

        if ($CashApproval->save()) {
            $update_arr = [
                'entry_code' => 10000 + $CashApproval->id,
            ];
            CashApproval::where('id', $CashApproval->id)->update($update_arr);


            // User Action Log
            $company_name = Companies::whereId($request->input('company_id'))->value('company_name');
            $client_name = Clients::whereId($request->input('client_id'))->value('client_name');
            $project_name = Projects::whereId($request->input('project_id'))->value('project_name');
            $project_site = Project_sites::whereId($request->input('project_site_id'))->value('site_name');
            $vendor_name = Vendors::whereId($request->input('vendor_id'))->value('vendor_name');
            $add_string = "<br> Company Name: ".$company_name."<br> Client Name: ".$client_name."<br> Project Name: ".$project_name."<br> Site Name: ".$project_site."<br> Vender Name: ".$vendor_name."<br>Amount: ".$request->get('amount');
            $entry_code = CashApproval::where('id', $CashApproval->id)->value('entry_code');
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => "Cash Payment entry code ".$entry_code." added".$add_string,
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            return redirect()->route('admin.cash_payment')->with('success', 'Cash Payment Details added successfully.');
        } else {
            return redirect()->route('admin.add_cash_payment_detail')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function edit_cash_payment_detail($id)
    {

        $this->data['page_title'] = "Edit Cash Payment";
        $this->data['cash_payment_detail'] = CashApproval::where('id', $id)->get();
        $this->data['users'] = User::whereStatus('Enabled')->pluck('name', 'id');
        $check_result = Permissions::checkPermission($this->module_id, 2);
        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }

        if ($this->data['cash_payment_detail']->count() == 0) {
            return redirect()->route('admin.edit_payment')->with('error', 'Error Occurred. Try Again!');
        }
        $this->data['Companies'] = Companies::select('id', 'company_name')->orderBy('company_name', 'asc')->get();
        $this->data['Projects'] = Projects::select('id', 'project_name')->orderBy('project_name', 'asc')->get();
        return view('admin.cash_payment.edit_payment', $this->data);
    }

    public function update_cash_payment(Request $request)
    {
        $validator_normal = Validator::make($request->all(), [
            'title' => 'required',
            'amount' => 'required',
        ]);

        if ($validator_normal->fails()) {
            return redirect()->route('admin.cash_payment')->with('error', 'Please follow validation rules.');
        }

        if ($request->input('project_id') == 1) {
            $other_cash_detail = $request->input('other_cash_detail');
        } else {
            $other_cash_detail = "";
        }
        $total_tax =  $request->input('igst_amount') + $request->input('cgst_amount') + $request->input('sgst_amount');

        $paymentModel = [
            'title' => $request->input('title'),
            'company_id' => $request->input('company_id'),

            'payment_options' => $request->input('payment_options'),
            //'budget_sheet_id' => $request->input('budget_sheet_id'),

            'client_id' => $request->input('client_id'),
            'project_type' => $request->input('project_type'),
            'project_id' => $request->input('project_id'),
            'project_site_id' => $request->input('project_site_id'),
            'other_cash_detail' => $other_cash_detail,
            'vendor_id' => !empty($request->input('vendor_id')) ? $request->input('vendor_id') : "",
            'amount' => $request->input('amount') + $total_tax,
            'note' => $request->input('note'),
            'requested_by' => $request->input('requested_by'),
            'expence_done_by' => $request->input('expence_done_by'),
            'vender_invoice_no' => $request->input('vender_invoice_no'),
            'igst_amount' => $request->input('igst_amount'),
            'cgst_amount' => $request->input('cgst_amount'),
            'sgst_amount' => $request->input('sgst_amount'),
            // 'first_approval_status' => 'Pending',
            // 'second_approval_status' => 'Pending',
            // 'third_approval_status' => 'Pending',
            //'status' => 'Pending',
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
        ];

        if ($request->input('payment_options') == 'Emergency Option') {
            $paymentModel['budget_sheet_id'] = NULL;
        }else{
            $paymentModel['budget_sheet_id'] = $request->input('budget_sheet_id');
        }
        // ACCOUNT_ROLE
        if (Auth::user()->role == config('constants.Admin')) {
            $paymentModel['first_approval_status'] = "Approved";
            $paymentModel['first_approval_id'] = Auth::user()->id;
             $paymentModel['first_approval_datetime'] = date('Y-m-d H:i:s');
            $this->notification_task->cashRequestFirstApprovalNotify([$this->super_admin->id]);
        }
        if (Auth::user()->role == config('constants.SuperUser')) {

            $paymentModel['first_approval_status'] = "Approved";
            $paymentModel['first_approval_datetime'] = date('Y-m-d H:i:s');
            $paymentModel['first_approval_id'] = Auth::user()->id;

            $paymentModel['second_approval_status'] = "Approved";
            $paymentModel['second_approval_datetime'] = date('Y-m-d H:i:s');
            $paymentModel['second_approval_id'] = Auth::user()->id;

            $paymentModel['third_approval_status'] = "Approved";
            $paymentModel['third_approval_datetime'] = date('Y-m-d H:i:s');
            $paymentModel['third_approval_id'] = Auth::user()->id;
            $paymentModel['status'] = "Approved";

        }
        /* if ($request->hasFile('payment_file')) {
            $payment_file = $request->file('payment_file');
            $file_path = $payment_file->store('public/payment_file');
            if ($file_path) {
                $paymentModel['payment_file'] = $file_path;
            }
        } */
        //21-02-2020
        if ($request->file('payment_file')) {
            /* $payment_file = $request->file('payment_file');
            $original_file_name = explode('.', $payment_file->getClientOriginalName());
            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);
            $file_path = $payment_file->storeAs('public/payment_file', $new_file_name);
            if ($file_path) {
                $paymentModel['payment_file'] = $file_path;
            } */

            // 04-08-2020
            $payment_file = $request->file('payment_file');
            $payment_file_arr = [];
            foreach ($payment_file as $key => $value) {

                $original_file_name = explode('.', $value->getClientOriginalName());
                $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);
                $file_path = $value->storeAs('public/payment_file', $new_file_name);
                $payment_file_arr[$key] = $file_path;
            }
            $paymentModel['payment_file'] = implode(',', $payment_file_arr);
        }

        CashApproval::where('id', $request->input('id'))->update($paymentModel);

        // User Action Log
        $company_name = Companies::whereId($request->input('company_id'))->value('company_name');
        $client_name = Clients::whereId($request->input('client_id'))->value('client_name');
        $project_name = Projects::whereId($request->input('project_id'))->value('project_name');
        $project_site = Project_sites::whereId($request->input('project_site_id'))->value('site_name');
        $vendor_name = Vendors::whereId($request->input('vendor_id'))->value('vendor_name');
        $add_string = "<br> Company Name: ".$company_name."<br> Client Name: ".$client_name."<br> Project Name: ".$project_name."<br> Site Name: ".$project_site."<br> Vender Name: ".$vendor_name."<br>Amount: ".$request->get('amount');
        $entry_code = CashApproval::where('id', $request->input('id'))->value('entry_code');
        $action_data = [
            'user_id' => Auth::user()->id,
            'task_body' => "Cash Payment entry code ".$entry_code." updated".$add_string,
            'created_ip' => $request->ip(),
        ];
        $this->user_action_logs->action($action_data);

        return redirect()->route('admin.cash_payment')->with('success', 'Cash Approval successfully updated.');
    }

    public function cash_payment_list(Request $request)        //jayram
    {
        $this->data['page_title'] = "Cash Payment";
        $access_level = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => 25])->get()->first();
        //jayram desai
        $this->data['date'] = "";
        if (!empty($access_level)) {
            $this->data['access_rule'] = $access_level->access_level;
        }


            $Results = CashApproval::select('cash_approval.*', 'cash_approval.voucher_no as voucher_number','voucher_number_register.voucher_ref_no','voucher_number_register.voucher_no','budget_sheet_approval.budhet_sheet_no', 'users.name as user_name', 'company.company_name','clients.client_name','clients.location', 'project_sites.site_name', 'project.project_name', 'vendor.vendor_name', 'request_user.name as requested_by_name', 'expence_done.name as expence_done_name')
            ->join('users', 'cash_approval.user_id', '=', 'users.id')
            ->join('company', 'company.id', '=', 'cash_approval.company_id')
            ->join('project', 'project.id', '=', 'cash_approval.project_id')
            ->join('vendor', 'vendor.id', '=', 'cash_approval.vendor_id')
            ->leftJoin('budget_sheet_approval', 'budget_sheet_approval.id', '=', 'cash_approval.budget_sheet_id')
            ->leftJoin('clients', 'clients.id', '=', 'cash_approval.client_id')
            ->leftJoin('project_sites', 'project_sites.id', '=', 'cash_approval.project_site_id')
            ->leftJoin('voucher_number_register', 'voucher_number_register.id', '=', 'cash_approval.voucher_no')
            ->leftJoin('users as request_user', 'request_user.id', '=', 'cash_approval.requested_by')
            ->leftJoin('users as expence_done', 'expence_done.id', '=', 'cash_approval.expence_done_by');

            //jayram desai
            if(!empty($request->get('date')))
            {
                $this->data['date'] = $request->get('date');
                $date = $request->get('date');
                $mainDate = explode("-", $date);
                $strFirstdate = str_replace("/", "-", $mainDate[0]);
                $strLastdate  = str_replace("/", "-", $mainDate[1]);

                $first_date  = date('Y-m-d H:i:s', strtotime($strFirstdate));
                $second_date = date('Y-m-d H:i:s', strtotime($strLastdate));

                $Results->whereBetween('cash_approval.created_at', [$first_date, $second_date]);
            }
            $approvealData = $Results->get()->toArray();
            foreach($approvealData as $key => $value){
                $approvealData[$key]['voucher_numbers'] =  $this->get_voucher_number_data($value['voucher_number']);
            }
            $this->data['cashApprovealData'] = $approvealData;
            // dd($this->data['cashApprovealData']);
            return view('admin.cash_payment.payment_list', $this->data);
    }

    public function get_voucher_number_data($id){
        $ids = explode(',',$id);
        $voucher = VoucherNumberRegister::whereIn('id', $ids)->pluck('voucher_no')->toArray();
        if($voucher){
            return implode(',', $voucher);
        }else{
            return "";
        }
    }

    public function approve_cash_payment($id)
    {              //this
        $check_result = Permissions::checkPermission(25, 2);

        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }

        if (Auth::user()->role == config('constants.Admin')) {
            $updateData = [
                'first_approval_status' => 'Approved',
                'first_approval_id' => Auth::user()->id,
                'first_approval_datetime' => date('Y-m-d H:i:s')
            ];
            $admin_user = User::where('role', config('constants.ACCOUNT_ROLE'))->get(['id']);
            //send notification about rejected
            $this->notification_task->cashRequestFirstApprovalNotify([$admin_user[0]->id]);
        } elseif (Auth::user()->role == config('constants.ACCOUNT_ROLE')) {
            $bankApprovealData = CashApproval::select('users.name', 'cash_approval.amount', 'users.email', 'users.id as user_id')
                ->join('users', 'cash_approval.user_id', '=', 'users.id')
                ->where('cash_approval.id', $id)->get();

            //send notification to user who requested about approval
            $this->notification_task->cashRequestSecondApprovalNotify([$this->super_admin->id]);

            $updateData = [
                'second_approval_status' => 'Approved',
                'second_approval_id' => Auth::user()->id,
                'second_approval_datetime' => date('Y-m-d H:i:s')
            ];
        } elseif (Auth::user()->role == config('constants.SuperUser')) {

            $bankApprovealData = CashApproval::select('users.name', 'cash_approval.amount', 'users.email', 'users.id as user_id')
                ->join('users', 'cash_approval.user_id', '=', 'users.id')
                ->where('cash_approval.id', $id)->get();
            $data = [
                'username' => $bankApprovealData[0]['name'],
                'amount' => $bankApprovealData[0]['amount'],
                'email' => $bankApprovealData[0]['email'],
                'status' => 'Approved',
            ];

            $this->common_task->approveRejectPaymentEmail($data);
            //send notification to user who requested about approval
            $this->notification_task->cashRequestThirdApprovalNotify([$bankApprovealData[0]->user_id]);

            $updateData = [
                'third_approval_status' => 'Approved',
                'third_approval_id' => Auth::user()->id,
                'third_approval_datetime' => date('Y-m-d H:i:s'),
                'status' => 'Approved'
            ];
        }

        if (CashApproval::where('id', $id)->update($updateData)) {

            // User Action Log
            $entry_code = CashApproval::where('id', $id)->value('entry_code');
            $amount = CashApproval::where('id', $id)->value('amount');
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => "Cash Payment entry code ".$entry_code." approved <br> Amount: ".$amount,
                'created_ip' => \Request::ip(),
            ];
            $this->user_action_logs->action($action_data);

            return redirect()->route('admin.cash_payment_list')->with('success', 'Status successfully updated.');
        }
        return redirect()->route('admin.cash_payment_list')->with('error', 'Error during operation. Try again!');
    }

    public function reject_cash_payment($id, $note)
    {
        $check_result = Permissions::checkPermission(25, 2);

        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }

        if (Auth::user()->role == config('constants.Admin')) {
            $cashApprovealData = CashApproval::select('users.name', 'cash_approval.amount', 'users.email')
                ->join('users', 'cash_approval.user_id', '=', 'users.id')
                ->where('cash_approval.id', $id)->get();
            $data = [
                'username' => $cashApprovealData[0]['name'],
                'amount' => $cashApprovealData[0]['amount'],
                'email' => $cashApprovealData[0]['email'],
                'status' => 'Rejected',
            ];

            $updateData = [
                'reject_note' => $note,
                'first_approval_status' => 'Rejected',
                'first_approval_id' => Auth::user()->id,
                'first_approval_datetime' => date('Y-m-d H:i:s'),
                'status' => 'Rejected'
            ];

            $this->common_task->approveRejectPaymentEmail($data);

            //send notification about rejected
            $this->notification_task->bankPaymentRejectNotify([$this->super_admin->id]);
        } elseif (Auth::user()->role == config('constants.ACCOUNT_ROLE')) {
            $cashApprovealData = CashApproval::select('users.name', 'cash_approval.amount', 'users.id as user_id', 'users.email')
                ->join('users', 'cash_approval.user_id', '=', 'users.id')
                ->where('cash_approval.id', $id)->get();
            $data = [
                'username' => $cashApprovealData[0]['name'],
                'amount' => $cashApprovealData[0]['amount'],
                'email' => $cashApprovealData[0]['email'],
                'status' => 'Rejected',
            ];

            $this->common_task->approveRejectPaymentEmail($data);

            //send notification to user who requested about approval
            $this->notification_task->bankPaymentRejectNotify([$cashApprovealData[0]->user_id]);
            $updateData = [
                'reject_note' => $note,
                'second_approval_status' => 'Rejected',
                'second_approval_id' => Auth::user()->id,
                'second_approval_datetime' => date('Y-m-d H:i:s'),
                'status' => 'Rejected'
            ];
        } elseif (Auth::user()->role == config('constants.SuperUser')) {

            $cashApprovealData = CashApproval::select('users.name', 'cash_approval.amount', 'users.id as user_id', 'users.email')
                ->join('users', 'cash_approval.user_id', '=', 'users.id')
                ->where('cash_approval.id', $id)->get();
            $data = [
                'username' => $cashApprovealData[0]['name'],
                'amount' => $cashApprovealData[0]['amount'],
                'email' => $cashApprovealData[0]['email'],
                'status' => 'Rejected',
            ];

            $this->common_task->approveRejectPaymentEmail($data);

            //send notification to user who requested about approval
            $this->notification_task->bankPaymentRejectNotify([$cashApprovealData[0]->user_id]);
            $updateData = [
                'reject_note' => $note,
                'third_approval_status' => 'Rejected',
                'third_approval_id' => Auth::user()->id,
                'third_approval_datetime' => date('Y-m-d H:i:s'),
                'status' => 'Rejected'
            ];
        }


        if (CashApproval::where('id', $id)->update($updateData)) {

            // User Action Log
            $entry_code = CashApproval::where('id', $id)->value('entry_code');
            $amount = CashApproval::where('id', $id)->value('amount');
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => "Cash Payment entry code ".$entry_code." rejected <br>Amount : ".$amount." <br>Reject Note: ".$note,
                'created_ip' => \Request::ip(),
            ];
            $this->user_action_logs->action($action_data);
            return redirect()->route('admin.cash_payment_list')->with('success', 'Status successfully updated.');
        }
        return redirect()->route('admin.cash_payment_list')->with('error', 'Error during operation. Try again!');
    }

    public function get_cash_project_list()
    {
        if (!empty($_GET['company_id'])) {
            $company_id = $_GET['company_id'];
            $project_data = Projects::select('project_name', 'id')->where(['company_id' => $company_id])->orWhere(['company_id' => 0])->get()->toArray();
            $html = "<option value=''>Select Project</option>";
            foreach ($project_data as $key => $project_data_value) {
                $html .= "<option value=" . $project_data_value['id'] . ">" . $project_data_value['project_name'] . "</option>";
            }
            echo $html;
            die();
        }
    }

    public function get_cash_vendor_list()  //order by
    {
        if (!empty($_GET['company_id'])) {
            $company_id = $_GET['company_id'];
            $vendor_data = Vendors::orderBy('vendor_name')->select('vendor_name', 'id')->where(['company_id' => $company_id])->orderBy('vendor_name', 'asc')->get()->toArray();
            $html = "<option value=''>Select vendor</option>";
            foreach ($vendor_data as $key => $project_data_value) {
                if($project_data_value['vendor_name'] != "Others" && $project_data_value['vendor_name'] != "Other"){
                    $html .= "<option value=" . $project_data_value['id'] . ">" . $project_data_value['vendor_name'] . "</option>";
                }
            }

            //$html .= "</select>";
            echo $html;
            die();
        }
    }

    public function get_cashApproval(Request $request)  //12-03-2020
    {
        $validator_normal = Validator::make($request->all(), ['id' => 'required']);

        if ($validator_normal->fails()) {
            return response()->json(['status' => false]);
        }

        $cash_id = $request->id;
        $cash_records = CashApproval::where('id',$cash_id)->get(['cheque_number','rtgs_number','voucher_no','transaction_note']);

        $this->data['cash_records'] = $cash_records;
        if ($cash_records) {
            return response()->json(['status' => true, 'data' => $this->data]);
        } else {
            return response()->json(['status' => false]);
        }

    }
    public function approve_cashPaymentByAccountant(Request $request)   //12-03-2020
    {

        $validator_normal = Validator::make($request->all(), [
            'id' => 'required',
            'transaction_note' => 'required'
        ]);

        if ($validator_normal->fails()) {
            return redirect()->route('admin.cash_payment_list')->with('error', 'Please follow validation rules.');
        }

        $cash_id  = $request->input('id');
        $update_arr = [

            'cheque_number' => $request->input('cheque_number'),
            'rtgs_number' => $request->input('rtgs_number'),
            'voucher_no' => $request->input('voucher_no'),
            'transaction_note' => $request->input('transaction_note'),

        ];

        if (CashApproval::where('id', $cash_id)->update($update_arr)) {

            return redirect()->route('admin.cash_payment_list')->with('success', 'Cash Payment successfully Approved.');
        }
        return redirect()->route('admin.cash_payment_list')->with('error', 'Error during operation. Try again!');
    }
    //------------ 02/07/2020
    public function get_cashNewApproval(Request $request)  //12-03-2020
    {
        $validator_normal = Validator::make($request->all(), ['id' => 'required']);

        if ($validator_normal->fails()) {
            return response()->json(['status' => false]);
        }

        $cash_id = $request->id;
        $cash_records = CashApproval::where('id',$cash_id)->get(['company_id','voucher_no','transaction_note']);
        if($cash_records[0]->company_id) {

            $login_id = Auth::user()->id;
            $vouchers = AssignedVoucher::where('to_user_id', $login_id)->where('status', 'accepted')->pluck('voucher_ref_no');

            $company_id = $cash_records[0]->company_id;
            $voucher_data = \App\VoucherNumberRegister::select('voucher_ref_no','id')
                            ->where(['company_id' => $company_id])
                            ->whereIn('voucher_ref_no', $vouchers)
                            ->groupBy('voucher_ref_no')
                            ->get()->toArray();
            $html = "<option value=''>Select Voucher Ref Number</option>";
            foreach ($voucher_data as $key => $voucher_data_value) {
                $html .= '<option value="'. $voucher_data_value['voucher_ref_no'].'">'. $voucher_data_value['voucher_ref_no'].'</option>';
            }
            $this->data['voucher_ref_list'] = $html;
        }
        $this->data['cash_records'] = $cash_records;
        if ($cash_records) {
            return response()->json(['status' => true, 'data' => $this->data]);
        } else {
            return response()->json(['status' => false]);
        }

    }
     //------------ 02/07/2020
    public function get_unfailed_voucher(Request $request)
    {
            $voucher_ref_no = $request->voucher_ref_no;
            $voucher_data = \App\VoucherNumberRegister::select('voucher_no', 'id')
                            ->where(['voucher_ref_no' => $voucher_ref_no])
                            ->where(['is_failed' => 0])
                            ->where(['is_used' => "not_used"])
                            ->get()->toArray();
            $html = "<option value=''>Select Voucher</option>";

            foreach ($voucher_data as $key => $voucher_data_value) {
                $html .= "<option value=" . $voucher_data_value['id'] . ">" . $voucher_data_value['voucher_no'] . "</option>";
            }
            echo $html;
            die();

    }
    public function approve_cashNewPaymentByAccountant(Request $request)
    {
        // dd($request->all());
        $validator_normal = Validator::make($request->all(), [
            'id' => 'required',
            'transaction_note' => 'required',
            'voucher_no' => 'required'
        ]);

        if ($validator_normal->fails()) {
            return redirect()->route('admin.cash_payment_list')->with('error', 'Please follow validation rules.');
        }

        $cash_id  = $request->input('id');
        $cash_detail = CashApproval::whereId($cash_id)->first();

        // dd($request->all());
        $update_arr = [
            'voucher_no' => implode(',', $request->input('voucher_no')),
            'transaction_note' => $request->input('transaction_note'),
            'purchase_order_number' => $request->input('purchase_order_number'),
        ];


        if ($request->hasFile('voucher_image')) {
                $voucher_image = $request->file('voucher_image');
                $file_path = $voucher_image->store('public/voucher_image');
                if ($file_path) {
                    $update_arr['voucher_image'] = $file_path;
                }
            }
        // dd($update_arr);
        $this->company_cash_transfer($cash_detail);
        if (CashApproval::where('id', $cash_id)->update($update_arr)) {

            //voucher used
            foreach($request->get('voucher_no') as $key => $value){
                $voucher_arr = [
                    'cash_approval_id' => $cash_id,
                    'project_id' => $cash_detail['project_id'],
                    'client_id' => $cash_detail['client_id'],
                    'project_site_id' => $cash_detail['project_site_id'],
                    'issue_date' => date('Y-m-d H:i:s'),
                    'user_id' => Auth::user()->id,
                    'is_used' => 'used',
                    'created_ip' => $request->ip(),
                    'updated_ip' => $request->ip(),
                ];
                VoucherNumberRegister::whereId($value)->update($voucher_arr);
            }


            return redirect()->route('admin.cash_payment_list')->with('success', 'Cash Payment successfully Approved.');
        }
        return redirect()->route('admin.cash_payment_list')->with('error', 'Error during operation. Try again!');
    }

    public function company_cash_transfer($cash_detail){
        $com_cash = Company_cash_management::where('company_id', $cash_detail['company_id'])->first();
        // dd($cash_detail);
        if($com_cash){
            if ($com_cash['balance'] >=  $cash_detail['amount']) {
                $before_amount = $com_cash['balance'];
                $after_amount = $com_cash['balance'] - $cash_detail['amount'];
                // dd($after_amount);
                $cash_transfer_arr = [
                    'account_id' => $cash_detail['company_id'],
                    'account_type' => 'company',
                    'project_id' => $cash_detail['project_id'],
                    'balance' => $cash_detail['amount'],
                    'transfer_type' => 'debit',
                    'entry_type' => 'cash_payment',
                    'txn_before_balance' => $before_amount,
                    'txn_after_balance' => $after_amount,
                    'cash_entry_code' => $cash_detail['entry_code'],
                    'user_id' => auth()->user()->id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'created_ip' => \Request::ip(),
                    'updated_ip' => \Request::ip(),
                    'updated_by' => auth()->user()->id,
                ];
                // dd($cash_transfer_arr);
                Cash_transfer::insert($cash_transfer_arr);
                $com_cash->balance = $after_amount;
                $com_cash->save();
            }
        }
    }

    //18/09/2020
    public function delete_cash_payment($id) {

        if(Auth::user()->role !== config('constants.SuperUser')){
            return redirect()->route('admin.cash_payment_list')->with('error','Access Denied. You are not authorized to access that functionality.');
        }

        if (CashApproval::where('id', $id)->delete()) {
            return redirect()->route('admin.cash_payment_list')->with('success', 'Delete successfully updated.');
        }
        return redirect()->route('admin.cash_payment_list')->with('error', 'Error during operation. Try again!');
    }

}
