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
use App\Lib\NotificationTask;
use App\Email_format;
use App\Mail\Mails;
use App\Role_module;
use App\RevisePolicy;
use App\UserRevisePolicy;
use App\OnlinePaymentApproval;
use Illuminate\Support\Facades\Mail;
use App\Lib\CommonTask;
use App\Companies;
use App\Clients;
use App\Projects;
use App\Project_sites;
use App\Vendors;
use App\Vendors_bank;
use App\PaymentCard;
use App\TdsSectionType;
use App\BudgetSheetApproval;
use App\Lib\UserActionLogs;
use Illuminate\Support\Facades\Storage;

class OnlinePaymentApprovalController extends Controller {

    public $data;
    public $notification_task;
    public $common_task;
    private $super_admin;
    private $module_id = 48;
    public $user_action_logs;

    public function __construct() {
        $this->data['module_title'] = "OnlinePaymentApproval";
        $this->data['module_link'] = "admin.online_payment";
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();
        $this->user_action_logs = new UserActionLogs();
        $this->super_admin = \App\User::where('role', 1)->first();
    }

    public function index() {
        $bank_payment_full_view_permission = Permissions::checkPermission($this->module_id, 5);

        if (!$bank_payment_full_view_permission) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You do not have access to this module.');
        }

        $this->data['page_title'] = "Online Payment";
        $access_level = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => $this->module_id])->get()->first();
        $this->data['access_rule'] = "";
        if (!empty($access_level)) {
            $this->data['access_rule'] = $access_level->access_level;
        }

        return view('admin.online_payment.index', $this->data);
    }

    public function get_online_payment_list() {   //this

        $datatable_fields = array(
        'online_payment_approval.payment_options',
        'budget_sheet_approval.budhet_sheet_no',
        'online_payment_approval.entry_code',
        'users.name',
         'company.company_name',
         'clients.client_name',
         //'clients.location',
          'project.project_name',
           'online_payment_approval.other_project_detail',
           'project_sites.site_name',
           'vendor.vendor_name',
            'online_payment_approval.note',
            'online_payment_approval.transation_detail',
            'online_payment_approval.transaction_type',
            'payment_card.card_number',
            'vendors_bank.bank_name',
            'bank.bank_name',
            'online_payment_approval.total_amount',
            'online_payment_approval.amount',
            'online_payment_approval.igst_amount',
            'online_payment_approval.cgst_amount',
            'online_payment_approval.sgst_amount',
            'tds_section_type.section_type',
            'online_payment_approval.tds_amount',
            'online_payment_approval.entry_completed',
            'online_payment_approval.invoice_no',
            'online_payment_approval.invoice_no',
            'online_payment_approval.first_approval_status',
            'online_payment_approval.second_approval_status',
            'online_payment_approval.third_approval_status',
            'online_payment_approval.status',
            'online_payment_approval.created_at');
        $request = Input::all();
        $conditions_array = ['online_payment_approval.user_id' => Auth::user()->id];

        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'project';
        $join_str[0]['join_table_id'] = 'project.id';
        $join_str[0]['from_table_id'] = 'online_payment_approval.project_id';

        $join_str[1]['join_type'] = '';
        $join_str[1]['table'] = 'company';
        $join_str[1]['join_table_id'] = 'company.id';
        $join_str[1]['from_table_id'] = 'online_payment_approval.company_id';

        $join_str[2]['join_type'] = 'left';
        $join_str[2]['table'] = 'bank';
        $join_str[2]['join_table_id'] = 'bank.id';
        $join_str[2]['from_table_id'] = 'online_payment_approval.bank_id';


        $join_str[3]['join_type'] = '';
        $join_str[3]['table'] = 'users';
        $join_str[3]['join_table_id'] = 'users.id';
        $join_str[3]['from_table_id'] = 'online_payment_approval.user_id';

        $join_str[4]['join_type'] = '';
        $join_str[4]['table'] = 'vendor';
        $join_str[4]['join_table_id'] = 'vendor.id';
        $join_str[4]['from_table_id'] = 'online_payment_approval.vendor_id';

        $join_str[5]['join_type'] = 'left';
        $join_str[5]['table'] = 'vendors_bank';
        $join_str[5]['join_table_id'] = 'vendors_bank.id';
        $join_str[5]['from_table_id'] = 'online_payment_approval.bank_details';

        $join_str[6]['join_type'] = 'left';
        $join_str[6]['table'] = 'payment_card';
        $join_str[6]['join_table_id'] = 'payment_card.id';
        $join_str[6]['from_table_id'] = 'online_payment_approval.transaction_id';

        $join_str[7]['join_type'] = 'left';
        $join_str[7]['table'] = 'clients';
        $join_str[7]['join_table_id'] = 'clients.id';
        $join_str[7]['from_table_id'] = 'online_payment_approval.client_id';

        $join_str[8]['join_type'] = 'left';
        $join_str[8]['table'] = 'project_sites';
        $join_str[8]['join_table_id'] = 'project_sites.id';
        $join_str[8]['from_table_id'] = 'online_payment_approval.project_site_id';

        $join_str[9]['join_type'] = 'left';
        $join_str[9]['table'] = 'budget_sheet_approval';
        $join_str[9]['join_table_id'] = 'budget_sheet_approval.id';
        $join_str[9]['from_table_id'] = 'online_payment_approval.budget_sheet_id';

        $join_str[10]['join_type'] = 'left';
        $join_str[10]['table'] = 'tds_section_type';
        $join_str[10]['join_table_id'] = 'tds_section_type.id';
        $join_str[10]['from_table_id'] = 'online_payment_approval.section_type_id';

        $getfiled = array('online_payment_approval.*','budget_sheet_approval.budhet_sheet_no', 'bank_details', 'bank.bank_name', 'users.name as user_name', 'company.company_name','clients.client_name','clients.location', 'project.project_name','project_sites.site_name', 'vendor.vendor_name', 'vendors_bank.bank_name as vendor_bank_name', 'vendors_bank.ac_number','payment_card.card_number as payment_card', 'tds_section_type.section_type');
        $table = "online_payment_approval";

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);

        die();
    }

    public function get_online_payment_list_ajax() {

        $datatable_fields = array(
            'online_payment_approval.payment_options',
            'budget_sheet_approval.budhet_sheet_no',
            'online_payment_approval.entry_code',
            'users.name',
             'company.company_name',
             'clients.client_name',
             //'clients.location',
              'project.project_name',
               'online_payment_approval.other_project_detail',
               'project_sites.site_name',
               'vendor.vendor_name',
                'online_payment_approval.note',

                'online_payment_approval.transation_detail',
                'online_payment_approval.transaction_type',
                'payment_card.card_number',

                 'vendors_bank.bank_name',
                'bank.bank_name',
                'online_payment_approval.total_amount',
                'online_payment_approval.amount',
                'online_payment_approval.igst_amount',
                'online_payment_approval.cgst_amount',
                'online_payment_approval.sgst_amount',
                'tds_section_type.section_type',
                'online_payment_approval.tds_amount',
                'online_payment_approval.entry_completed',
                'online_payment_approval.invoice_no',
                'online_payment_approval.invoice_file',
                'online_payment_approval.first_approval_status',
                'online_payment_approval.second_approval_status',
                'online_payment_approval.third_approval_status',
                'online_payment_approval.status',
                'online_payment_approval.created_at');
        $request = Input::all();

        if (Auth::user()->role == config('constants.ACCOUNT_ROLE')) {
            $conditions_array = ['online_payment_approval.first_approval_status' => 'Pending'];
        } elseif (Auth::user()->role == config('constants.Admin')) {

            $conditions_array = ['online_payment_approval.first_approval_status' => 'Approved', 'online_payment_approval.second_approval_status' => 'Pending'];
        } elseif (Auth::user()->role == config('constants.SuperUser')) {
            $conditions_array = ['online_payment_approval.first_approval_status' => 'Approved',
                'online_payment_approval.second_approval_status' => 'Approved',
                'online_payment_approval.third_approval_status' => 'Pending',
                'online_payment_approval.status' => 'Pending'];
        }


        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'project';
        $join_str[0]['join_table_id'] = 'project.id';
        $join_str[0]['from_table_id'] = 'online_payment_approval.project_id';

        $join_str[1]['join_type'] = '';
        $join_str[1]['table'] = 'company';
        $join_str[1]['join_table_id'] = 'company.id';
        $join_str[1]['from_table_id'] = 'online_payment_approval.company_id';

        $join_str[2]['join_type'] = 'left';
        $join_str[2]['table'] = 'bank';
        $join_str[2]['join_table_id'] = 'bank.id';
        $join_str[2]['from_table_id'] = 'online_payment_approval.bank_id';

        $join_str[4]['join_type'] = '';
        $join_str[4]['table'] = 'users';
        $join_str[4]['join_table_id'] = 'users.id';
        $join_str[4]['from_table_id'] = 'online_payment_approval.user_id';

        $join_str[5]['join_type'] = '';
        $join_str[5]['table'] = 'vendor';
        $join_str[5]['join_table_id'] = 'vendor.id';
        $join_str[5]['from_table_id'] = 'online_payment_approval.vendor_id';

        $join_str[6]['join_type'] = 'left';
        $join_str[6]['table'] = 'vendors_bank';
        $join_str[6]['join_table_id'] = 'vendors_bank.id';
        $join_str[6]['from_table_id'] = 'online_payment_approval.bank_details';

        $join_str[7]['join_type'] = 'left';
        $join_str[7]['table'] = 'payment_card';
        $join_str[7]['join_table_id'] = 'payment_card.id';
        $join_str[7]['from_table_id'] = 'online_payment_approval.transaction_id';

        $join_str[8]['join_type'] = 'left';
        $join_str[8]['table'] = 'clients';
        $join_str[8]['join_table_id'] = 'clients.id';
        $join_str[8]['from_table_id'] = 'online_payment_approval.client_id';

        $join_str[9]['join_type'] = 'left';
        $join_str[9]['table'] = 'project_sites';
        $join_str[9]['join_table_id'] = 'project_sites.id';
        $join_str[9]['from_table_id'] = 'online_payment_approval.project_site_id';

        $join_str[10]['join_type'] = 'left';
        $join_str[10]['table'] = 'budget_sheet_approval';
        $join_str[10]['join_table_id'] = 'budget_sheet_approval.id';
        $join_str[10]['from_table_id'] = 'online_payment_approval.budget_sheet_id';

        $join_str[11]['join_type'] = 'left';
        $join_str[11]['table'] = 'tds_section_type';
        $join_str[11]['join_table_id'] = 'tds_section_type.id';
        $join_str[11]['from_table_id'] = 'online_payment_approval.section_type_id';

        $getfiled = array('online_payment_approval.*','budget_sheet_approval.budhet_sheet_no', 'bank_details', 'bank.bank_name', 'users.name as user_name', 'company.company_name','clients.client_name','clients.location', 'project.project_name','project_sites.site_name', 'vendor.vendor_name', 'vendors_bank.bank_name as vendor_bank_name', 'vendors_bank.ac_number','payment_card.card_number as payment_card', 'tds_section_type.section_type');
        $table = "online_payment_approval";

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);

        die();
    }

    public function add_online_payment_detail() {

        $bank_payment_add_permission = Permissions::checkPermission($this->module_id, 3);

        if (!$bank_payment_add_permission) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You do not have access to this module.');
        }

        $this->data['page_title'] = 'Add Bank Payment Details';
        $this->data['section_type'] = TdsSectionType::select('id', 'section_type')->where('status', 'Enabled')->orderBy('section_type', 'asc')->get();
        $this->data['Companies'] = Companies::select('id', 'company_name')->orderBy('company_name', 'asc')->get();
        //$this->data['Projects'] = Projects::select('id', 'project_name')->get();
        $this->data['main_entry_list'] = OnlinePaymentApproval::where('main_entry', 1)->where('entry_completed', 'No')->get(['entry_code'])->pluck('entry_code');
        // dd($this->data);
        return view('admin.online_payment.add_payment', $this->data);
    }

    public function  insert_online_payment (Request $request) {   // 07/09/2020

        $validator_normal = Validator::make($request->all(), [
                    // 'bank_id' => 'required',
                    'company_id' => 'required',
                    'client_id' => 'required',
                    'project_id' => 'required',
                    'project_site_id' => 'required',
                    'transaction_type'=>'required',
                    'amount' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_online_payment_detail')->with('error', 'Please follow validation rules.');
        }

        $request_data     = $request->all();
        $total_file_count = $request_data['file_counts'];

        $OnlinePaymentApproval = new OnlinePaymentApproval();

        $OnlinePaymentApproval->company_id = $request->input('company_id');
        $OnlinePaymentApproval->client_id = $request->input('client_id');

        $OnlinePaymentApproval->payment_options = $request->input('payment_options');
        if ($request->input('payment_options') == 'Budget Sheet') {
            $OnlinePaymentApproval->budget_sheet_id = $request->input('budget_sheet_id');
        }

        $OnlinePaymentApproval->project_type = $request->input('project_type');

        $OnlinePaymentApproval->project_site_id = $request->input('project_site_id');
        $OnlinePaymentApproval->project_id = $request->input('project_id');

        if (!empty($request->input('vendor_id'))) {
            $OnlinePaymentApproval->vendor_id = $request->input('vendor_id');
        }

        if ($request->input('project_id') == config('constants.OTHER_PROJECT_ID')) {
            $OnlinePaymentApproval->other_project_detail = $request->input('other_project_detail');
        }

        $OnlinePaymentApproval->user_id      = Auth::user()->id;
        $OnlinePaymentApproval->bank_details = $request->input('bank_details');

        $OnlinePaymentApproval->transaction_type  = $request->input('transaction_type');
        if ($request->input('transaction_type') == "Netbanking") {
            $OnlinePaymentApproval->bank_id      = $request->input('bank_id');
        } else {
            $OnlinePaymentApproval->transaction_id    = $request->input('transaction_id');
        }

        $total_tax = $request->input('igst_amount') + $request->input('cgst_amount') + $request->input('sgst_amount');

        $OnlinePaymentApproval->transation_detail = $request->input('transation_detail');
        $OnlinePaymentApproval->amount       = $request->input('amount') + $total_tax - $request->input('tds_amount');
        $OnlinePaymentApproval->total_amount = $request->input('total_amount');
        $OnlinePaymentApproval->igst_amount = $request->input('igst_amount');
        $OnlinePaymentApproval->cgst_amount = $request->input('cgst_amount');
        $OnlinePaymentApproval->sgst_amount = $request->input('sgst_amount');
        $OnlinePaymentApproval->tds_amount = $request->input('tds_amount');
        $OnlinePaymentApproval->invoice_no = $request->input('invoice_no');
        $OnlinePaymentApproval->section_type_id = $request->input('section_type_id');
        $OnlinePaymentApproval->note         = $request->input('note');
        $OnlinePaymentApproval->created_at = date('Y-m-d H:i:s');
        $OnlinePaymentApproval->created_ip = $request->ip();
        $OnlinePaymentApproval->updated_at = date('Y-m-d H:i:s');
        $OnlinePaymentApproval->updated_ip = $request->ip();
        if (Auth::user()->role == config('constants.ACCOUNT_ROLE')) {
            $OnlinePaymentApproval->first_approval_status = "Approved";
            $OnlinePaymentApproval->first_approval_id = Auth::user()->id;
            $OnlinePaymentApproval->first_approval_date_time = date('Y-m-d H:i:s');
            $admin_user= User::where('role',config('constants.Admin'))->get(['id']);
            $this->notification_task->onlinePaymentFirstApprovalNotify([$admin_user[0]->id]);
        }

        if (Auth::user()->role == config('constants.SuperUser')) {

            $OnlinePaymentApproval->first_approval_status = "Approved";
            $OnlinePaymentApproval->first_approval_date_time = date('Y-m-d H:i:s');
            $OnlinePaymentApproval->first_approval_id = Auth::user()->id;
            $OnlinePaymentApproval->second_approval_status = "Approved";
            $OnlinePaymentApproval->second_approval_date_time = date('Y-m-d H:i:s');
            $OnlinePaymentApproval->second_approval_id = Auth::user()->id;
            $OnlinePaymentApproval->third_approval_status = "Approved";
            $OnlinePaymentApproval->third_approval_date_time = date('Y-m-d H:i:s');
            $OnlinePaymentApproval->third_approval_id = Auth::user()->id;
            $OnlinePaymentApproval->status = "Approved";

        }

        if ($request->input('entry_code') && $request->input('entry_code') > 0) {
            $OnlinePaymentApproval->main_entry = 0;
            $OnlinePaymentApproval->entry_code = $request->input('entry_code');
            $old_sum = OnlinePaymentApproval::where('entry_code', $request->input('entry_code'))->get()->sum('amount');
            $completed_amt = $old_sum + $request->input('amount');
            $OnlinePaymentApproval->entry_completed = 'Yes';
            $main_entry = OnlinePaymentApproval::where('entry_code', $request->input('entry_code'))->where('main_entry', 1)->get();
            if ($request->input('total_amount') <= $completed_amt) {
                OnlinePaymentApproval::where('id', $main_entry[0]->id)->update(['entry_completed' => 'Yes']);
            }
        } else {
            $OnlinePaymentApproval->main_entry = 1;
            if ($OnlinePaymentApproval->total_amount <= $OnlinePaymentApproval->amount) {
                $OnlinePaymentApproval->entry_completed = 'Yes';
            } else {
                $OnlinePaymentApproval->entry_completed = 'No';
            }
        }

        //21-02-2020
        if ($request->file('invoice_file')) {

            $invoice_file = $request->file('invoice_file');

            $original_file_name = explode('.', $invoice_file->getClientOriginalName());

            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);


            $file_path = $invoice_file->storeAs('public/invoice_file', $new_file_name);
            if ($file_path) {
                $OnlinePaymentApproval->invoice_file = $file_path;
            }
        }
        // dd($OnlinePaymentApproval);
        if ($OnlinePaymentApproval->save()) {

            // entry code
            if (!$request->input('entry_code') || $request->input('entry_code') == 0) {
                $update_arr = [
                    'entry_code' => 10000 + $OnlinePaymentApproval->id,
                ];
                OnlinePaymentApproval::where('id', $OnlinePaymentApproval->id)->update($update_arr);
            }

            if (!empty($request->input('budget_sheet_id'))) {
                $budget_data = BudgetSheetApproval::whereId($request->input('budget_sheet_id'))->first();
                if ($budget_data['release_hold_amount_status'] == "Approved") {
                    $budget_data->release_hold_amount = NULL;
                    $budget_data->release_hold_amount_status = NULL;
                    $budget_data->release_amount_first_approval_status = NULL;
                    $budget_data->release_amount_first_reject_note = NULL;
                    $budget_data->release_amount_first_approval_id = NULL;
                    $budget_data->release_amount_second_approval_status = NULL;
                    $budget_data->release_amount_second_reject_note = NULL;
                    $budget_data->release_amount_second_approval_id = NULL;
                    $budget_data->save();
                }
            }
            //----------------------- upload existing budget sheet files
            if ($request->input('payment_options') == 'Budget Sheet') {

                $exising_budgetsheet_files = \App\Budget_sheet_file::where('budget_sheet_id',$request->input('budget_sheet_id'))->pluck('budget_sheet_file')->toArray();
                if (count($exising_budgetsheet_files) > 0) {

                    foreach ($exising_budgetsheet_files as $key => $budget_file) {
                        $isExists = Storage::exists($budget_file);
                        if ($isExists) {
                            $budget_file_name = str_replace("public/budget_sheet_file/", "",$budget_file);
                            if (!Storage::exists("public/online_payment_file/{$budget_file_name}")) {
                                Storage::copy($budget_file, "public/online_payment_file/{$budget_file_name}");
                                $existing_file_arr = [
                                    'online_payment_id' => $OnlinePaymentApproval->id,
                                    'online_payment_file' => "public/online_payment_file/{$budget_file_name}",
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'created_ip' => $request->ip(),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'updated_ip' => $request->ip()
                                ];
                                \App\OnlinePaymentFile::insert($existing_file_arr);
                            }
                        }
                    }
                }
                //----------- upload invoice no & invoice file
                $exising_invoice_files = \App\Budget_sheet_invoice_files::where('budget_sheet_id',$request->input('budget_sheet_id'))->pluck('budget_sheet_invoice_file')->toArray();
                if (count($exising_invoice_files) > 0) {

                    foreach ($exising_invoice_files as $key => $invoice_file) {
                        $isInvoiceExists = Storage::exists($invoice_file);
                        //check invoice file exist in budget_sheet_invoice_files folder if yes then will be copy and paste in online_payment_invoice_files folder
                        if ($isInvoiceExists) {
                            $invoice_file_name = str_replace("public/budget_sheet_invoice_files/", "",$invoice_file);
                            //check budget sheet file already exist in online_payment_invoice_files folder if yes then do not need to copy paste...
                            if (!Storage::exists("public/online_payment_invoice_files/{$invoice_file_name}")) {
                                Storage::copy($invoice_file, "public/online_payment_invoice_files/{$invoice_file_name}");

                                OnlinePaymentApproval::where('id', $OnlinePaymentApproval->id)->update(['invoice_file'=> "public/online_payment_invoice_files/{$invoice_file_name}"]);
                            }
                        }
                    }
                }

            }

            $file_array = [];
            //upload all images
            if ($request->file('online_payment_file')) {
                $online_payment_files_list = $request->file('online_payment_file');
                foreach ($online_payment_files_list as $key => $online_payment_files) {

                    $original_file_name = explode('.', $online_payment_files[0]->getClientOriginalName());

                    $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);


                    $file_path = $online_payment_files[0]->storeAs('public/online_payment_file', $new_file_name);

                    $online_payment_file_arr = [
                                'online_payment_id' => $OnlinePaymentApproval->id,
                                'online_payment_file' => $file_path,
                                'created_at' => date('Y-m-d H:i:s'),
                                'created_ip' => $request->ip(),
                                'updated_at' => date('Y-m-d H:i:s'),
                                'updated_ip' => $request->ip()
                            ];
                    \App\OnlinePaymentFile::insert($online_payment_file_arr);
                }
            }

            // User Action Log
            $company_name = Companies::whereId($request->input('company_id'))->value('company_name');
            $client_name = Clients::whereId($request->input('client_id'))->value('client_name');
            $project_name = Projects::whereId($request->input('project_id'))->value('project_name');
            $project_site = Project_sites::whereId($request->input('project_site_id'))->value('site_name');
            $vendor_name = Vendors::whereId($request->input('vendor_id'))->value('vendor_name');
            $add_string = "<br> Company Name: ".$company_name."<br> Client Name: ".$client_name."<br> Project Name: ".$project_name."<br> Site Name: ".$project_site."<br> Vender Name: ".$vendor_name."<br>Amount: ".$request->get('amount');
            $entry_code = OnlinePaymentApproval::where('id', $OnlinePaymentApproval->id)->value('entry_code');
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => "Online Payment entry code ".$entry_code." added".$add_string,
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            return redirect()->route('admin.online_payment')->with('success', 'Payment Online Details added successfully.');
        } else {
            return redirect()->route('admin.add_online_payment_detail')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function edit_online_payment_detail($id) {

        $this->data['page_title'] = "Edit Online Payment";
        $this->data['online_payment_detail'] = $online_payment_detail = OnlinePaymentApproval::where('id', $id)->get();
        $check_result = Permissions::checkPermission($this->module_id, 2);
        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }

        if ($this->data['online_payment_detail']->count() == 0) {
            return redirect()->route('admin.edit_payment')->with('error', 'Error Occurred. Try Again!');
        }
        $this->data['section_type'] = TdsSectionType::select('id', 'section_type')->where('status', 'Enabled')->orderBy('section_type', 'asc')->get();
        $this->data['Companies'] = Companies::select('id', 'company_name')->orderBy('company_name', 'asc')->get();
        $this->data['Projects'] = Projects::select('id', 'project_name')->orderBy('project_name', 'asc')->get();

        $entry_code = OnlinePaymentApproval::where('entry_code', $online_payment_detail[0]->entry_code)
            ->where('entry_completed', 'Yes')
            ->where('main_entry', 1)->value('entry_code');

        $main_entry_list = OnlinePaymentApproval::where('main_entry', 1)->where('entry_completed', 'No')
        ->pluck('entry_code')->toArray();

        if ($entry_code) {
            array_push($main_entry_list, $entry_code);
        }
        $this->data['main_entry_list'] = $main_entry_list;
        // dd($this->data['main_entry_list']);
        return view('admin.online_payment.edit_payment', $this->data);
    }

    public function update_online_payment(Request $request) {

        $validator_normal = Validator::make($request->all(), [
                    // 'bank_id' => 'required',
                    'amount' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.online_payment')->with('error', 'Please follow validation rules.');
        }

        if ($request->input('project_id') == config('constants.OTHER_PROJECT_ID')) {
            $other_project_detail = $request->input('other_project_detail');
        } else {
            $other_project_detail = "";
        }

        if($request->input('transaction_type')=='Netbanking'){
            $card_id="";
        }
        else{
            $card_id=$request->input('transaction_id');
        }
        $total_tax = $request->input('igst_amount') + $request->input('cgst_amount') + $request->input('sgst_amount');
        $onlinePaymentModel = [
            //'bank_id' => $request->input('bank_id'),
            'company_id' => $request->input('company_id'),

            'payment_options' => $request->input('payment_options'),
            //'budget_sheet_id' => $request->input('budget_sheet_id'),

            'client_id' => $request->input('client_id'),
            'project_site_id' => $request->input('project_site_id'),
            'project_type' => $request->input('project_type'),
            'project_id' => $request->input('project_id'),
            'other_project_detail' => $other_project_detail,
            'transation_detail'=> $request->input('transation_detail'),
            'transaction_type'=> $request->input('transaction_type'),
            //'transaction_id'=> $card_id,
            'vendor_id' => !empty($request->input('vendor_id')) ? $request->input('vendor_id') : "",
            'bank_details' => $request->input('bank_details'),
            'note' => $request->input('note'),
            'amount' => $request->input('amount') + $total_tax - $request->input('tds_amount'),
            'total_amount' => $request->input('total_amount'),
            'igst_amount' => $request->input('igst_amount'),
            'cgst_amount' => $request->input('cgst_amount'),
            'sgst_amount' => $request->input('sgst_amount'),
            'tds_amount' => $request->input('tds_amount'),
            'invoice_no' => $request->input('invoice_no'),
            'section_type_id' => $request->input('section_type_id'),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip()
        ];

        if ($request->input('transaction_type') == "Netbanking") {
            $onlinePaymentModel['transaction_id'] = NULL;
            $onlinePaymentModel['bank_id'] = $request->input('bank_id');
        } else {
            $onlinePaymentModel['bank_id'] = NULL;
            $onlinePaymentModel['transaction_id']= $card_id;
        }

        if ($request->input('payment_options') == 'Emergency Option') {
            $onlinePaymentModel['budget_sheet_id'] = NULL;
        }elseif ($request->input('payment_options') == 'Regular') {
            $onlinePaymentModel['budget_sheet_id'] = NULL;
        }else{
            $onlinePaymentModel['budget_sheet_id'] = $request->input('budget_sheet_id');
        }
        $payment_detail = OnlinePaymentApproval::where('id', $request->input('id'))->get();
        if ($request->input('entry_code')) {
            $onlinePaymentModel['entry_code'] = $request->input('entry_code');
        }

        $old_already_completed_amount = OnlinePaymentApproval::where('entry_code', $payment_detail[0]->entry_code)->get()->sum('amount');

        //already added record with entry code selected in dropdown
        $new_update_record_main_entry = OnlinePaymentApproval::where('entry_code', $request->input('entry_code'))
        ->where('main_entry', 1)->get();

        if ($new_update_record_main_entry[0]->id == $request->input('id')) {
            $onlinePaymentModel['main_entry'] = 1;
        } else {
            $onlinePaymentModel['main_entry'] = 0;
        }

        if($payment_detail[0]->status!='Approved'){
            $onlinePaymentModel['first_approval_status'] = "Pending";
            $onlinePaymentModel['second_approval_status'] = "Pending";
            $onlinePaymentModel['third_approval_status'] = "Pending";
            $onlinePaymentModel['status'] = "Pending";
        }
        if (Auth::user()->role == config('constants.ACCOUNT_ROLE')) {
            $onlinePaymentModel['first_approval_status'] = "Approved";
            $onlinePaymentModel['first_approval_id'] = Auth::user()->id;
            $this->notification_task->onlinePaymentFirstApprovalNotify([$this->super_admin->id]);
        }

        if (Auth::user()->role == config('constants.SuperUser')) {
            $onlinePaymentModel['first_approval_status'] = "Approved";
            $onlinePaymentModel['first_approval_date_time'] =  date('Y-m-d H:i:s');
            $onlinePaymentModel['first_approval_id'] = Auth::user()->id;

            $onlinePaymentModel['second_approval_status'] = "Approved";
            $onlinePaymentModel['second_approval_date_time'] =  date('Y-m-d H:i:s');
            $onlinePaymentModel['second_approval_id'] = Auth::user()->id;

            $onlinePaymentModel['third_approval_status'] = "Approved";
            $onlinePaymentModel['third_approval_date_time'] = date('Y-m-d H:i:s');
            $onlinePaymentModel['third_approval_id'] = Auth::user()->id;
            $onlinePaymentModel['status'] = "Approved";

        }

        //26-08-2020
        if ($request->file('invoice_file')) {

            $invoice_file = $request->file('invoice_file');

            $original_file_name = explode('.', $invoice_file->getClientOriginalName());

            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);


            $file_path = $invoice_file->storeAs('public/invoice_file', $new_file_name);
            if ($file_path) {
                $onlinePaymentModel['invoice_file'] = $file_path;
            }
        }

        OnlinePaymentApproval::where('id', $request->input('id'))->update($onlinePaymentModel);

        //If total amount change in main entry then update all it's child entries...
        OnlinePaymentApproval::where('entry_code', $request->input('entry_code'))->update(['total_amount' => $request->input('total_amount')]);

        if ($request->input('entry_code') == $payment_detail[0]->entry_code) {
            //just update entry with new amount and it's parent entry based on amount is completed or not
            $new_update_record_main_entry = OnlinePaymentApproval::where('entry_code', $request->input('entry_code'))
                ->where('main_entry', 1)->get();

            $new_completed_amt = OnlinePaymentApproval::where('entry_code', $request->input('entry_code'))->get()->sum('amount');

            if ($new_completed_amt >= $new_update_record_main_entry[0]->total_amount) {
                $update_arr = [
                    'entry_completed' => 'Yes',
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip()
                ];
                OnlinePaymentApproval::where('id', $new_update_record_main_entry[0]->id)->update($update_arr);
            } else {
                $update_arr = [
                    'entry_completed' => 'No',
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip()
                ];
                OnlinePaymentApproval::where('id', $new_update_record_main_entry[0]->id)->update($update_arr);
            }
        }else{
            $newcompleted_amount = ($old_already_completed_amount - $payment_detail[0]->amount);

            $old_update_record_main_entry = OnlinePaymentApproval::where('entry_code', $payment_detail[0]->entry_code)
                ->where('main_entry', 1)->get();

            if($old_update_record_main_entry->count() > 0){
                if ($newcompleted_amount >= $old_update_record_main_entry[0]->total_amount) {
                    $update_arr = [
                        'entry_completed' => 'Yes',
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_ip' => $request->ip()
                    ];
                } else {
                    $update_arr = [
                        'entry_completed' => 'No',
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_ip' => $request->ip()
                    ];
                }
                OnlinePaymentApproval::where('id', $old_update_record_main_entry[0]->id)->update($update_arr);
            }


            if(!empty($request->input('entry_code'))){
                $new_already_completed_amount = OnlinePaymentApproval::where('entry_code', $request->input('entry_code'))->get()->sum('amount');

                if ($new_already_completed_amount >= $request->input('total_amount')) {

                    $update_arr = [
                        'entry_completed' => 'Yes',
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_ip' => $request->ip()
                    ];
                } else {
                    $update_arr = [
                        'entry_completed' => 'No',
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_ip' => $request->ip()
                    ];
                }
                $new_update_record_main_entry = OnlinePaymentApproval::where('entry_code', $request->input('entry_code'))
                ->where('main_entry', 1)->get();

                OnlinePaymentApproval::where('id', $new_update_record_main_entry[0]->id)->update($update_arr);
            }

        }

        $file_array = [];
        //upload all images
        if ($request->file('online_payment_file')) {
            $online_payment_files_list = $request->file('online_payment_file');
            foreach ($online_payment_files_list as $key => $online_payment_files) {

                $original_file_name = explode('.', $online_payment_files[0]->getClientOriginalName());

                $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);


                $file_path = $online_payment_files[0]->storeAs('public/online_payment_file', $new_file_name);

                $online_payment_file_arr = [
                            'online_payment_id' => $request->input('id'),
                            'online_payment_file' => $file_path,
                            'created_at' => date('Y-m-d H:i:s'),
                            'created_ip' => $request->ip(),
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_ip' => $request->ip()
                        ];
                \App\OnlinePaymentFile::insert($online_payment_file_arr);
            }
        }

        // User Action Log
        $company_name = Companies::whereId($request->input('company_id'))->value('company_name');
        $client_name = Clients::whereId($request->input('client_id'))->value('client_name');
        $project_name = Projects::whereId($request->input('project_id'))->value('project_name');
        $project_site = Project_sites::whereId($request->input('project_site_id'))->value('site_name');
        $vendor_name = Vendors::whereId($request->input('vendor_id'))->value('vendor_name');
        $add_string = "<br> Company Name: ".$company_name."<br> Client Name: ".$client_name."<br> Project Name: ".$project_name."<br> Site Name: ".$project_site."<br> Vender Name: ".$vendor_name."<br>Amount: ".$request->get('amount');
        $entry_code = OnlinePaymentApproval::where('id', $request->input('id'))->value('entry_code');
        $action_data = [
            'user_id' => Auth::user()->id,
            'task_body' => "Online Payment entry code ".$entry_code." updated".$add_string,
            'created_ip' => $request->ip(),
        ];
        $this->user_action_logs->action($action_data);

        return redirect()->route('admin.online_payment')->with('success', 'Online Payment successfully updated.');
    }

    public function online_payment_list(Request $request) {
        $this->data['page_title'] = "Online Payment Approval";
        $access_level = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => $this->module_id])->get()->first();
        $this->data['access_rule'] = '';

        //jayram desai
        $this->data['date'] = "";

        if (!empty($access_level)) {
            $this->data['access_rule'] = $access_level->access_level;
        }

        $Results = DB::table('online_payment_approval')
                        ->join('users', 'online_payment_approval.user_id', '=', 'users.id')
                        ->join('company', 'company.id', '=', 'online_payment_approval.company_id')
                        ->join('project', 'project.id', '=', 'online_payment_approval.project_id')
                        ->join('vendor', 'vendor.id', '=', 'online_payment_approval.vendor_id')
                        ->leftJoin('bank', 'bank.id', '=', 'online_payment_approval.bank_id')
                        ->leftJoin('budget_sheet_approval', 'budget_sheet_approval.id', '=', 'online_payment_approval.budget_sheet_id')
                        ->leftjoin('rtgs_register','rtgs_register.id','=','online_payment_approval.rtgs_number')
                        ->leftJoin('clients', 'clients.id', '=', 'online_payment_approval.client_id')
                        ->leftJoin('project_sites', 'project_sites.id', '=', 'online_payment_approval.project_site_id')
                        ->leftJoin('vendors_bank', 'vendors_bank.id', '=', 'online_payment_approval.bank_details')
                        ->leftJoin('tds_section_type', 'tds_section_type.id', '=', 'online_payment_approval.section_type_id')
                        ->leftJoin('payment_card', 'payment_card.id', '=', 'online_payment_approval.transaction_id');

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

                $Results->whereBetween('online_payment_approval.created_at', [$first_date, $second_date]);
            }
            $this->data['online_payment_approval_history'] = $Results->get(['budget_sheet_approval.budhet_sheet_no','rtgs_register.rtgs_no','clients.client_name','clients.location', 'project_sites.site_name','vendors_bank.bank_name as vendor_bank_name','vendors_bank.ac_number','vendors_bank.ifsc' ,'online_payment_approval.*', 'bank_details', 'bank.bank_name', 'users.name as user_name', 'company.company_name', 'project.project_name', 'vendor.vendor_name','payment_card.card_number as payment_card', 'tds_section_type.section_type'])->toArray();

        return view('admin.online_payment.online_payment_list', $this->data);
    }


    public function approve_online_payment(Request $request) {
        $check_result = Permissions::checkPermission($this->module_id, 2);

        
        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }
        $id=$request->input('approve_paymentid');
        $approve_note=$request->input('approve_note');
        if (Auth::user()->role == config('constants.ACCOUNT_ROLE')) {
            $updateData = ['first_approval_status' => 'Approved',
                'first_approval_id' => Auth::user()->id,
                'first_approval_date_time' => date('Y-m-d H:i:s'),
                'first_approval_remark'=>$approve_note];

            $admin_user= User::where('role',config('constants.Admin'))->get(['id']);

            $this->notification_task->onlinePaymentFirstApprovalNotify([$admin_user[0]->id]);
        } elseif (Auth::user()->role == config('constants.Admin')) {
            $bankApprovealData = OnlinePaymentApproval::select('users.name', 'online_payment_approval.amount', 'users.email', 'users.id as user_id')
                            ->join('users', 'online_payment_approval.user_id', '=', 'users.id')
                            ->where('online_payment_approval.id', $id)->get();


            $this->notification_task->onlinePaymentSecondApprovalNotify([$this->super_admin->id]);

            $updateData = ['second_approval_status' => 'Approved', 'second_approval_id' => Auth::user()->id,
                'second_approval_remark'=>$approve_note,'second_approval_date_time' => date('Y-m-d H:i:s')];
        } elseif (Auth::user()->role == config('constants.SuperUser')) {

            $bankApprovealData = OnlinePaymentApproval::select('users.name', 'online_payment_approval.amount', 'users.email', 'users.id as user_id')
                            ->join('users', 'online_payment_approval.user_id', '=', 'users.id')
                            ->where('online_payment_approval.id', $id)->get();
            $data = [
                'username' => $bankApprovealData[0]['name'],
                'amount' => $bankApprovealData[0]['amount'],
                'email' => $bankApprovealData[0]['email'],
                'status' => 'Approved',
            ];

            $this->common_task->approveRejectOnlinePaymentEmail($data);
            //send notification to user who requested about approval
            $this->notification_task->onlinePaymentThirdApprovalNotify([$bankApprovealData[0]->user_id]);

            $updateData = ['third_approval_status' => 'Approved', 'third_approval_id' => Auth::user()->id,
                'status' => 'Approved','third_approval_remark'=>$approve_note,'third_approval_date_time' => date('Y-m-d H:i:s')];
        }

        if (OnlinePaymentApproval::where('id', $id)->update($updateData)) {

            // User Action Log
            $entry_code = OnlinePaymentApproval::where('id', $id)->value('entry_code');
            $amount = OnlinePaymentApproval::where('id', $id)->value('amount');
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => "Online Payment entry code ".$entry_code." approved <br>Amount: ".$amount,
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            return redirect()->route('admin.online_payment_list')->with('success', 'Status successfully updated.');
        }
        return redirect()->route('admin.online_payment_list')->with('error', 'Error during operation. Try again!');
    }

    public function get_online_payment_approval_note(Request $request) {
        $approve_id=$request->input('approve_id');
        $approval_data= OnlinePaymentApproval::where('id',$approve_id)->get();
        if($approval_data->count()==0){
            return response()->json(['status'=>false]);
        }
        else{
            $approval_note1=$approval_data[0]->first_approval_remark ? $approval_data[0]->first_approval_remark : "NA";
            $approval_note2=$approval_data[0]->second_approval_remark ? $approval_data[0]->second_approval_remark : "NA";
            $approval_note3=$approval_data[0]->third_approval_remark ? $approval_data[0]->third_approval_remark : "NA";

            return response()->json(['status'=>true,'approval_note1'=>$approval_note1,'approval_note2'=>$approval_note2,'approval_note3'=>$approval_note3]);
        }
    }

    public function reject_online_payment(Request $request) {
        $check_result = Permissions::checkPermission($this->module_id, 2);

        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }
        $id=$request->input('paymentid');
        $note=$request->input('note');
        if (Auth::user()->role == config('constants.ACCOUNT_ROLE')) {
            $bankApprovealData = OnlinePaymentApproval::select('users.name', 'online_payment_approval.amount', 'users.email', 'users.id as user_id')
                            ->join('users', 'online_payment_approval.user_id', '=', 'users.id')
                            ->where('online_payment_approval.id', $id)->get()->toArray();

            $data = [
                'username' => $bankApprovealData[0]['name'],
                'amount' => $bankApprovealData[0]['amount'],
                'email' => $bankApprovealData[0]['email'],
                'status' => 'Rejected',
            ];

            $this->common_task->approveRejectOnlinePaymentEmail($data);
            //send notification about rejected
            $this->notification_task->onlinePaymentRejectNotify([$bankApprovealData[0]['user_id']]);

            $updateData = ['reject_note' => $note,'first_approval_date_time' => date('Y-m-d H:i:s'), 'first_approval_status' => 'Rejected', 'first_approval_id' => Auth::user()->id, 'status' => 'Rejected'];
        }
        elseif (Auth::user()->role == config('constants.Admin')) {
            $bankApprovealData = OnlinePaymentApproval::select('users.name', 'online_payment_approval.amount', 'users.id as user_id', 'users.email','users.id as user_id')
                            ->join('users', 'online_payment_approval.user_id', '=', 'users.id')
                            ->where('online_payment_approval.id', $id)->get()->toArray();

            $data = [
                'username' => $bankApprovealData[0]['name'],
                'amount' => $bankApprovealData[0]['amount'],
                'email' => $bankApprovealData[0]['email'],
                'status' => 'Rejected',
            ];

            $this->common_task->approveRejectOnlinePaymentEmail($data);

            //send notification to user who requested about approval
            $this->notification_task->onlinePaymentRejectNotify([$bankApprovealData[0]['user_id']]);

            $updateData = ['reject_note' => $note, 'second_approval_status' => 'Rejected', 'second_approval_id' => Auth::user()->id, 'status' => 'Rejected','second_approval_date_time' => date('Y-m-d H:i:s')];
        }
        elseif (Auth::user()->role == config('constants.SuperUser')) {

            $bankApprovealData = OnlinePaymentApproval::select('users.name', 'online_payment_approval.amount', 'users.id as user_id', 'users.email','users.id as user_id')
                            ->join('users', 'online_payment_approval.user_id', '=', 'users.id')
                            ->where('online_payment_approval.id', $id)->get()->toArray();

            $data = [
                'username' => $bankApprovealData[0]['name'],
                'amount' => $bankApprovealData[0]['amount'],
                'email' => $bankApprovealData[0]['email'],
                'status' => 'Rejected',
            ];

            $this->common_task->approveRejectOnlinePaymentEmail($data);

            //send notification to user who requested about approval
            $this->notification_task->onlinePaymentRejectNotify([$bankApprovealData[0]['user_id']]);

            $updateData = ['reject_note' => $note, 'third_approval_status' => 'Rejected', 'third_approval_id' => Auth::user()->id, 'status' => 'Rejected','third_approval_date_time' => date('Y-m-d H:i:s')];
        }


        if (OnlinePaymentApproval::where('id', $id)->update($updateData)) {

            // User Action Log
            $entry_code = OnlinePaymentApproval::where('id', $id)->value('entry_code');
            $amount = OnlinePaymentApproval::where('id', $id)->value('amount');
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => "Online Payment entry code ".$entry_code." rejected <br>Amount: ".$amount."<br>Reject Note: ".$note,
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);
            return redirect()->route('admin.online_payment_list')->with('success', 'Status successfully updated.');
        }
        return redirect()->route('admin.online_payment_list')->with('error', 'Error during operation. Try again!');
    }

    public function get_vendor_online_details() {
        if (!empty($_GET['company_id'])) {
            $company_id = $_GET['company_id'];
            $vendor_id = $_GET['vendor_id'];

            $vendor_bank_data = Vendors_bank::select('bank_name', 'id', 'ac_number')->where(['vendor_id' => $vendor_id])->where(['company_id' => $company_id])->get()->toArray();
            $html = "<option value=''>Select Vendor/Party bank</option>";
            foreach ($vendor_bank_data as $key => $vendor_bank_data_value) {
                $html .= "<option value=" . $vendor_bank_data_value['id'] . ">" . $vendor_bank_data_value['bank_name'] . "(" . $vendor_bank_data_value['ac_number'] . ")" . "</option>";
            }
            echo $html;
            die();
        }
    }

    public function get_online_payment_files(Request $request) {

        $validator_normal = Validator::make($request->all(), ['id' => 'required']);

        if ($validator_normal->fails()) {
            return response()->json(['status' => false]);
        }

        $online_payment_id = $request->id;
        $this->data['payment_status'] = $request->payment_status;
        $online_payment_files = \App\OnlinePaymentFile::where('online_payment_id', $online_payment_id)
                ->get(['id', 'online_payment_id', 'online_payment_file']);

        foreach ($online_payment_files as $key => $files) {

            $online_payment_files[$key]->file_name = str_replace('public/online_payment_file/', '', $files->online_payment_file);
            if ($files->online_payment_file) {

                $online_payment_files[$key]->online_payment_file = asset('storage/' . str_replace('public/', '', $files->online_payment_file));
            } else {

                $online_payment_files[$key]->online_payment_file = "";
            }
        }

        $this->data['online_payment_files'] = $online_payment_files;

        if ($online_payment_files->count() == 0) {
            return response()->json(['status' => false, 'data' => $this->data]);
        } else {

            return response()->json(['status' => true, 'data' => $this->data]);
        }
    }

    public function delete_online_file(Request $request) {

        $validator_normal = Validator::make($request->all(), ['id' => 'required']);

        if ($validator_normal->fails()) {
            return response()->json(['status' => false]);
        }

        $online_payment_id = $request->id;

        \App\OnlinePaymentFile::where('id', $online_payment_id)
                ->delete();

        return response()->json(['status' => true, 'data' => []]);
    }

    public function get_bank_card_list()
    {
        if(!empty($_GET['transaction_type'])) {
        //    $company_id       = $_GET['company_id'];
        //    $bank_id          = $_GET['bank_id'];
           $transaction_type = $_GET['transaction_type'];

           $card_data = PaymentCard::select('payment_card.id', 'payment_card.card_number', 'bank.bank_name', 'bank.ac_number')
                        ->leftjoin('bank', 'bank.id', '=', 'payment_card.bank_id')
                        // ->where(['bank_id'=>$bank_id,'company_id' => $company_id,'card_type'=>$transaction_type])
                        ->where('payment_card.card_type', $transaction_type)
                        ->where('payment_card.status', 'Enabled')
                        ->orderBy('payment_card.name_on_card', 'asc')->get()->toArray();

           $html = "<option value=''>Select Payment Card</option>";
           foreach ($card_data as $key => $card_data_value) {
                // $html.= "<option value=".$card_data_value['id'].">".substr_replace($card_data_value['card_number'], 'XXXX', 0, 12)."</option>";

                $html .= '<option value="'. $card_data_value['id'].'">'. substr_replace($card_data_value['card_number'], 'XXXX', 0, 12).' ('. $card_data_value['bank_name'].' - '. $card_data_value['ac_number'].')</option>';
           }
           echo  $html;
           die();
        }
    }

    public function get_onlineApproval(Request $request)  //12-03-2020
    {
        $validator_normal = Validator::make($request->all(), ['id' => 'required']);

        if ($validator_normal->fails()) {
            return response()->json(['status' => false]);
        }

        $id = $request->id;
        $online_payment_records = OnlinePaymentApproval::leftjoin('rtgs_register','rtgs_register.id','=','online_payment_approval.rtgs_number')
          ->where('online_payment_approval.id',$id)->get(['online_payment_approval.company_id','online_payment_approval.bank_id','online_payment_approval.rtgs_number','online_payment_approval.voucher_no','online_payment_approval.transation_detail']);


          if($online_payment_records[0]->company_id) {

            $company_id = $online_payment_records[0]->company_id;
            $bank_data = \App\Banks::select('bank_name','id','ac_number')->where(['company_id' => $company_id])->get()->toArray();
            $html = "<option value=''>Select Bank</option>";
            foreach ($bank_data as $key => $bank_data_value) {
                 $html.= "<option value=".$bank_data_value['id'].">".$bank_data_value['bank_name']." (".$bank_data_value['ac_number'].")"."</option>";
            }
            $this->data['bank_list'] = $html;
         }


        $this->data['online_records'] = $online_payment_records;
        if ($online_payment_records) {
            return response()->json(['status' => true, 'data' => $this->data]);
        } else {
            return response()->json(['status' => false]);
        }

    }

    public function approve_onlinePaymentByAccountant(Request $request)   //12-03-2020
    {

        $validator_normal = Validator::make($request->all(), [
            'id' => 'required',
            // 'transaction_note' => 'required'
        ]);

        if ($validator_normal->fails()) {
            return redirect()->route('admin.online_payment_list')->with('error', 'Please follow validation rules.');
        }
        $online_id  = $request->input('id');
        $update_arr = [

            'rtgs_number' => $request->input('rtgs_number'),
            'rtgs_ref_no' => $request->input('rtgs_ref_no'),
            'voucher_no' => $request->input('voucher_no'),
            'transation_detail' => $request->input('transaction_note'),
            'purchase_order_number' => $request->input('purchase_order_number'),

        ];

        if (OnlinePaymentApproval::where('id', $online_id)->update($update_arr)) {

            return redirect()->route('admin.online_payment_list')->with('success', 'Online Payment successfully Approved.');
        }
        return redirect()->route('admin.online_payment_list')->with('error', 'Error during operation. Try again!');
    }

    public function get_online_tds_report()
    {
        $bank_payment_full_view_permission = Permissions::checkPermission($this->module_id, 5);

        if (!$bank_payment_full_view_permission) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You do not have access to this module.');
        }

        $this->data['page_title'] = "Online TDS Report";
        $this->data['companies']  = Companies::orderBy('company_name', 'asc')->pluck('company_name', 'id');
        // dd($this->data);
        return view('admin.online_payment.tds_report', $this->data);
    }

    public function get_online_payment_tds_report(){
        $datatable_fields = array(
            'company.company_name',
            'tds_section_type.section_type',
            'online_payment_approval.tds_amount',
            'online_payment_approval.payment_options',
            'budget_sheet_approval.budhet_sheet_no',
            'users.name',
            'clients.client_name',
            //'clients.location',
            'project.project_name',
            'online_payment_approval.other_project_detail',
            'project_sites.site_name',
            'vendor.vendor_name',
            'online_payment_approval.note',
            'online_payment_approval.transation_detail',
            'online_payment_approval.transaction_type',
            'payment_card.card_number',
            'vendors_bank.bank_name',
            'bank.bank_name',
            'online_payment_approval.amount',
            'online_payment_approval.igst_amount',
            'online_payment_approval.cgst_amount',
            'online_payment_approval.sgst_amount',
            'online_payment_approval.created_at'
        );
        $request = Input::all();
        $conditions_array = ['online_payment_approval.user_id' => Auth::user()->id];
        $conditions_array = ['online_payment_approval.status' => "Approved"];
        $company_id = $request['company_id'];
        $date_range = $request['date_range'];

        if ($company_id != '') {
            $conditions_array['online_payment_approval.company_id'] = $company_id;
        }
        $start_date = "";
        $end_date = "";
        if ($date_range != '') {
            $get_dates = explode(' - ', $date_range);
            $start_date = date('Y-m-d', strtotime($get_dates[0]));
            $end_date = date('Y-m-d', strtotime($get_dates[1]));
        }

        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'project';
        $join_str[0]['join_table_id'] = 'project.id';
        $join_str[0]['from_table_id'] = 'online_payment_approval.project_id';

        $join_str[1]['join_type'] = '';
        $join_str[1]['table'] = 'company';
        $join_str[1]['join_table_id'] = 'company.id';
        $join_str[1]['from_table_id'] = 'online_payment_approval.company_id';

        $join_str[2]['join_type'] = 'left';
        $join_str[2]['table'] = 'bank';
        $join_str[2]['join_table_id'] = 'bank.id';
        $join_str[2]['from_table_id'] = 'online_payment_approval.bank_id';


        $join_str[3]['join_type'] = '';
        $join_str[3]['table'] = 'users';
        $join_str[3]['join_table_id'] = 'users.id';
        $join_str[3]['from_table_id'] = 'online_payment_approval.user_id';

        $join_str[4]['join_type'] = '';
        $join_str[4]['table'] = 'vendor';
        $join_str[4]['join_table_id'] = 'vendor.id';
        $join_str[4]['from_table_id'] = 'online_payment_approval.vendor_id';

        $join_str[5]['join_type'] = 'left';
        $join_str[5]['table'] = 'vendors_bank';
        $join_str[5]['join_table_id'] = 'vendors_bank.id';
        $join_str[5]['from_table_id'] = 'online_payment_approval.bank_details';

        $join_str[6]['join_type'] = 'left';
        $join_str[6]['table'] = 'payment_card';
        $join_str[6]['join_table_id'] = 'payment_card.id';
        $join_str[6]['from_table_id'] = 'online_payment_approval.transaction_id';

        $join_str[7]['join_type'] = 'left';
        $join_str[7]['table'] = 'clients';
        $join_str[7]['join_table_id'] = 'clients.id';
        $join_str[7]['from_table_id'] = 'online_payment_approval.client_id';

        $join_str[8]['join_type'] = 'left';
        $join_str[8]['table'] = 'project_sites';
        $join_str[8]['join_table_id'] = 'project_sites.id';
        $join_str[8]['from_table_id'] = 'online_payment_approval.project_site_id';

        $join_str[9]['join_type'] = 'left';
        $join_str[9]['table'] = 'budget_sheet_approval';
        $join_str[9]['join_table_id'] = 'budget_sheet_approval.id';
        $join_str[9]['from_table_id'] = 'online_payment_approval.budget_sheet_id';

        $join_str[10]['join_type'] = 'left';
        $join_str[10]['table'] = 'tds_section_type';
        $join_str[10]['join_table_id'] = 'tds_section_type.id';
        $join_str[10]['from_table_id'] = 'online_payment_approval.section_type_id';

        $getfiled = array('online_payment_approval.*', 'budget_sheet_approval.budhet_sheet_no', 'bank_details', 'bank.bank_name', 'users.name as user_name', 'company.company_name', 'clients.client_name', 'clients.location', 'project.project_name', 'project_sites.site_name', 'vendor.vendor_name', 'vendors_bank.bank_name as vendor_bank_name', 'vendors_bank.ac_number', 'payment_card.card_number as payment_card', 'tds_section_type.section_type');
        $table = "online_payment_approval";

        echo Common_query::get_list_date_range($table, $datatable_fields, $conditions_array, $getfiled, $request,$join_str, $start_date, $end_date);

        die();
    }

    public function get_online_payment_data(Request $request){

        $entry_code = $request->input('entry_code');
        $this->data['main_entry'] = $main_entry = OnlinePaymentApproval::where('online_payment_approval.entry_code', $entry_code)
            ->where('online_payment_approval.main_entry', 1)
            ->get(['online_payment_approval.*']);

        // foreach ($main_entry as $key => $val) {
        //     $main_entry[$key]->bank_details = (int) $val->bank_details;
        // }
        $this->data['total_complete_amount'] = OnlinePaymentApproval::where('entry_code', $entry_code)->get()->sum('amount');
        return response()->json($this->data);
    }


    public function get_budget_sheet_online_entry_code(Request $request)
    {
        $bank_payment = OnlinePaymentApproval::where('budget_sheet_id', $request->get('budget_sheet_id'))->where('main_entry', 1)->first();
        if ($bank_payment) {
            echo json_encode($bank_payment);
            die;
        } else {
            echo json_encode([]);
            die;
        }
    }

    //18/09/2020
    public function delete_online_payment($id) {

        if(Auth::user()->role !== config('constants.SuperUser')){
            return redirect()->route('admin.online_payment_list')->with('error','Access Denied. You are not authorized to access that functionality.');
        }

        if (OnlinePaymentApproval::where('id', $id)->delete()) {
            return redirect()->route('admin.online_payment_list')->with('success', 'Delete successfully updated.');
        }
        return redirect()->route('admin.online_payment_list')->with('error', 'Error during operation. Try again!');
    }
}
