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
use App\BankPaymentApproval;
use App\Clients;
use Illuminate\Support\Facades\Mail;
use App\Lib\CommonTask;
use App\Companies;
use App\Project_sites;
use Carbon\Carbon;
use App\Projects;
use App\BudgetSheetApproval;
use App\TenderPaymentRequest;
use App\Vendors;
use App\Vendors_bank;
use App\TdsSectionType;
use App\Tender;
use Illuminate\Support\Facades\Storage;
use App\Lib\UserActionLogs;

class BankPaymentApprovalController extends Controller
{

    public $data;
    public $notification_task;
    public $common_task;
    private $super_admin;
    private $module_id = 24;
    public $user_action_logs;

    public function __construct()
    {
        $this->data['module_title'] = "BankPaymentApproval";
        $this->data['module_link'] = "admin.payment";
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();
        $this->user_action_logs = new UserActionLogs();
        $this->super_admin = \App\User::where('role', 1)->first();
    }

    public function index()
    {
        $bank_payment_full_view_permission = Permissions::checkPermission($this->module_id, 5);

        if (!$bank_payment_full_view_permission) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You do not have access to this module.');
        }

        $this->data['page_title'] = "Bank Payment";
        $access_level = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => $this->module_id])->get()->first();
        $this->data['access_rule'] = "";
        if (!empty($access_level)) {
            $this->data['access_rule'] = $access_level->access_level;
        }

        return view('admin.payment.index', $this->data);
    }

    public function get_bank_payment_list()
    {

        $datatable_fields = array(

            'bank_payment_approval.payment_options',
            'budget_sheet_approval.budhet_sheet_no',
                'bank_payment_approval.entry_code',
                'users.name',
                'company.company_name',
                'clients.client_name',
               // 'clients.location',
                'project.project_name',
                'bank_payment_approval.other_project_detail',
                'project_sites.site_name',
                'vendor.vendor_name',
                'bank_payment_approval.note',
                'vendors_bank.bank_name',
                'bank.bank_name',
                'cheque_register.ch_no',
                'bank_payment_approval.total_amount',
                'bank_payment_approval.amount',
                'bank_payment_approval.micr_code',
                'bank_payment_approval.swift_code',
                'bank_payment_approval.igst_amount',
                'bank_payment_approval.cgst_amount',
                'bank_payment_approval.sgst_amount',
                'tds_section_type.section_type',
                'bank_payment_approval.tds_amount',
                'bank_payment_approval.main_entry',
                'bank_payment_approval.invoice_no',
                'bank_payment_approval.invoice_file',
                'bank_payment_approval.payment_method',
                'tender.tender_sr_no',
                'bank_payment_approval.tender_type',
                'bank_payment_approval.first_approval_status',
                'bank_payment_approval.second_approval_status',
                'bank_payment_approval.third_approval_status',
                'bank_payment_approval.status',
                'bank_payment_approval.created_at',
        );
        $request = Input::all();
        $conditions_array = ['bank_payment_approval.user_id' => Auth::user()->id];

        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'project';
        $join_str[0]['join_table_id'] = 'project.id';
        $join_str[0]['from_table_id'] = 'bank_payment_approval.project_id';

        $join_str[1]['join_type'] = '';
        $join_str[1]['table'] = 'company';
        $join_str[1]['join_table_id'] = 'company.id';
        $join_str[1]['from_table_id'] = 'bank_payment_approval.company_id';

        $join_str[2]['join_type'] = '';
        $join_str[2]['table'] = 'bank';
        $join_str[2]['join_table_id'] = 'bank.id';
        $join_str[2]['from_table_id'] = 'bank_payment_approval.bank_id';

        $join_str[3]['join_type'] = 'left';
        $join_str[3]['table'] = 'cheque_register';
        $join_str[3]['join_table_id'] = 'cheque_register.id';
        $join_str[3]['from_table_id'] = 'bank_payment_approval.cheque_number';

        $join_str[4]['join_type'] = '';
        $join_str[4]['table'] = 'users';
        $join_str[4]['join_table_id'] = 'users.id';
        $join_str[4]['from_table_id'] = 'bank_payment_approval.user_id';

        $join_str[5]['join_type'] = '';
        $join_str[5]['table'] = 'vendor';
        $join_str[5]['join_table_id'] = 'vendor.id';
        $join_str[5]['from_table_id'] = 'bank_payment_approval.vendor_id';

        $join_str[6]['join_type'] = '';
        $join_str[6]['table'] = 'vendors_bank';
        $join_str[6]['join_table_id'] = 'vendors_bank.id';
        $join_str[6]['from_table_id'] = 'bank_payment_approval.bank_details';

        $join_str[7]['join_type'] = 'left';
        $join_str[7]['table'] = 'clients';
        $join_str[7]['join_table_id'] = 'clients.id';
        $join_str[7]['from_table_id'] = 'bank_payment_approval.client_id';

        $join_str[8]['join_type'] = 'left';
        $join_str[8]['table'] = 'project_sites';
        $join_str[8]['join_table_id'] = 'project_sites.id';
        $join_str[8]['from_table_id'] = 'bank_payment_approval.project_site_id';

        $join_str[9]['join_type'] = 'left';
        $join_str[9]['table'] = 'budget_sheet_approval';
        $join_str[9]['join_table_id'] = 'budget_sheet_approval.id';
        $join_str[9]['from_table_id'] = 'bank_payment_approval.budget_sheet_id';

        $join_str[10]['join_type'] = 'left';
        $join_str[10]['table'] = 'tds_section_type';
        $join_str[10]['join_table_id'] = 'tds_section_type.id';
        $join_str[10]['from_table_id'] = 'bank_payment_approval.section_type_id';

        $join_str[11]['join_type'] = 'left';
        $join_str[11]['table'] = 'tender';
        $join_str[11]['join_table_id'] = 'tender.id';
        $join_str[11]['from_table_id'] = 'bank_payment_approval.tender_id';

        $getfiled = array('bank_payment_approval.*','budget_sheet_approval.budhet_sheet_no', 'bank_details', 'bank.bank_name', 'cheque_register.ch_no', 'users.name as user_name', 'company.company_name','clients.client_name','clients.location', 'project.project_name','project_sites.site_name', 'vendor.vendor_name', 'vendors_bank.bank_name as vendor_bank_name', 'vendors_bank.ac_number', 'tds_section_type.section_type','tender.tender_sr_no');
        $table = "bank_payment_approval";

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);

        die();
    }

    public function get_bank_payment_list_ajax()
    {

        $datatable_fields = array(

            'bank_payment_approval.payment_options',
            'budget_sheet_approval.budhet_sheet_no',
                'bank_payment_approval.entry_code',
                'users.name',
                'company.company_name',
                'clients.client_name',
                //'clients.location',
                'project.project_name',
                'bank_payment_approval.other_project_detail',
                'project_sites.site_name',
                'vendor.vendor_name',
                'bank_payment_approval.note',
                'vendors_bank.bank_name',
                'bank.bank_name',
                'cheque_register.ch_no',
                'bank_payment_approval.total_amount',
                'bank_payment_approval.amount',
                'bank_payment_approval.micr_code',
                'bank_payment_approval.swift_code',
                'bank_payment_approval.igst_amount',
                'bank_payment_approval.cgst_amount',
                'bank_payment_approval.sgst_amount',
                'tds_section_type.section_type',
                'bank_payment_approval.tds_amount',
                'bank_payment_approval.entry_completed',
                'bank_payment_approval.invoice_no',
                // 'bank_payment_approval.invoice_file',
                'bank_payment_approval.payment_method',
                'tender.tender_sr_no',
                'bank_payment_approval.tender_type',
                'bank_payment_approval.first_approval_status',
                'bank_payment_approval.second_approval_status',
                'bank_payment_approval.third_approval_status',
                'bank_payment_approval.status',
                'bank_payment_approval.created_at'
        );
        $request = Input::all();

        if (Auth::user()->role == config('constants.ACCOUNT_ROLE')) {
            $conditions_array = ['bank_payment_approval.first_approval_status' => 'Pending'];
        } elseif (Auth::user()->role == config('constants.Admin')) {

            $conditions_array = ['bank_payment_approval.first_approval_status' => 'Approved', 'bank_payment_approval.second_approval_status' => 'Pending'];
        } elseif (Auth::user()->role == config('constants.SuperUser')) {
            $conditions_array = [
                'bank_payment_approval.first_approval_status' => 'Approved',
                'bank_payment_approval.second_approval_status' => 'Approved',
                'bank_payment_approval.third_approval_status' => 'Pending',
                'bank_payment_approval.status' => 'Pending'
            ];
        }


        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'project';
        $join_str[0]['join_table_id'] = 'project.id';
        $join_str[0]['from_table_id'] = 'bank_payment_approval.project_id';

        $join_str[1]['join_type'] = '';
        $join_str[1]['table'] = 'company';
        $join_str[1]['join_table_id'] = 'company.id';
        $join_str[1]['from_table_id'] = 'bank_payment_approval.company_id';

        $join_str[2]['join_type'] = '';
        $join_str[2]['table'] = 'bank';
        $join_str[2]['join_table_id'] = 'bank.id';
        $join_str[2]['from_table_id'] = 'bank_payment_approval.bank_id';

        $join_str[3]['join_type'] = 'left';
        $join_str[3]['table'] = 'cheque_register';
        $join_str[3]['join_table_id'] = 'cheque_register.id';
        $join_str[3]['from_table_id'] = 'bank_payment_approval.cheque_number';

        $join_str[4]['join_type'] = '';
        $join_str[4]['table'] = 'users';
        $join_str[4]['join_table_id'] = 'users.id';
        $join_str[4]['from_table_id'] = 'bank_payment_approval.user_id';

        $join_str[5]['join_type'] = '';
        $join_str[5]['table'] = 'vendor';
        $join_str[5]['join_table_id'] = 'vendor.id';
        $join_str[5]['from_table_id'] = 'bank_payment_approval.vendor_id';

        $join_str[6]['join_type'] = 'left';
        $join_str[6]['table'] = 'vendors_bank';
        $join_str[6]['join_table_id'] = 'vendors_bank.id';
        $join_str[6]['from_table_id'] = 'bank_payment_approval.bank_details';

        $join_str[7]['join_type'] = 'left';
        $join_str[7]['table'] = 'clients';
        $join_str[7]['join_table_id'] = 'clients.id';
        $join_str[7]['from_table_id'] = 'bank_payment_approval.client_id';

        $join_str[8]['join_type'] = 'left';
        $join_str[8]['table'] = 'project_sites';
        $join_str[8]['join_table_id'] = 'project_sites.id';
        $join_str[8]['from_table_id'] = 'bank_payment_approval.project_site_id';

        $join_str[9]['join_type'] = 'left';
        $join_str[9]['table'] = 'budget_sheet_approval';
        $join_str[9]['join_table_id'] = 'budget_sheet_approval.id';
        $join_str[9]['from_table_id'] = 'bank_payment_approval.budget_sheet_id';

        $join_str[10]['join_type'] = 'left';
        $join_str[10]['table'] = 'tds_section_type';
        $join_str[10]['join_table_id'] = 'tds_section_type.id';
        $join_str[10]['from_table_id'] = 'bank_payment_approval.section_type_id';

        $join_str[11]['join_type'] = 'left';
        $join_str[11]['table'] = 'tender';
        $join_str[11]['join_table_id'] = 'tender.id';
        $join_str[11]['from_table_id'] = 'bank_payment_approval.tender_id';

        $getfiled = array('bank_payment_approval.*','budget_sheet_approval.budhet_sheet_no', 'bank_details', 'bank.bank_name', 'cheque_register.ch_no', 'users.name as user_name', 'company.company_name','clients.client_name','clients.location',  'project.project_name','project_sites.site_name', 'vendor.vendor_name', 'vendors_bank.bank_name as vendor_bank_name', 'vendors_bank.ac_number', 'tds_section_type.section_type','tender.tender_sr_no');
        $table = "bank_payment_approval";

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);

        die();
    }

    public function add_bank_payment_detail($tender_id = null, $type = null)
    {
        $bank_payment_add_permission = Permissions::checkPermission($this->module_id, 3);

        if (!$bank_payment_add_permission) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You do not have access to this module.');
        }

        if($tender_id){
            if($type == "fee"){
                $this->data['tender_data'] = Tender::whereId($tender_id)->first(['id as tender_id','company_id','tender_fee_amount as payment_amount'])->toArray();
                $this->data['tender_data']['tender_type'] = "fee";
            }else{
                $this->data['tender_data'] = Tender::whereId($tender_id)->first(['id as tender_id','company_id','tender_emd_amount as payment_amount'])->toArray();
                $this->data['tender_data']['tender_type'] = "emd";
            }
        }else{
            $this->data['tender_data'] = [];
        }

        $this->data['page_title'] = 'Add Bank Payment Details';
        $this->data['Companies'] = Companies::select('id', 'company_name')->orderBy('company_name', 'asc')->get();
        $this->data['section_type'] = TdsSectionType::select('id','section_type')->where('status', 'Enabled')->orderBy('section_type', 'asc')->get();
        $this->data['main_entry_list'] = BankPaymentApproval::where('main_entry', 1)->where('entry_completed', 'No')
        ->get(['entry_code'])->pluck('entry_code');
        // dd($this->data);
        return view('admin.payment.add_payment', $this->data);
    }

    public function insert_payment(Request $request)  // 07/09/2020
    {
        $validator_normal = Validator::make($request->all(), [
            'bank_id' => 'required',
            'company_id' => 'required',
            'project_id' => 'required',
            'bank_details' => 'required',
            'amount' => 'required',
            'payment_options' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_bank_payment_detail')->with('error', 'Please follow validation rules.');
        }

        $BankPaymentApproval = new BankPaymentApproval();

        $BankPaymentApproval->company_id = $request->input('company_id');
        $BankPaymentApproval->payment_options = $request->input('payment_options');
        if ($request->input('payment_options') == 'Budget Sheet') {
            $BankPaymentApproval->budget_sheet_id = $request->input('budget_sheet_id');
        }

        $BankPaymentApproval->project_type = $request->input('project_type');
        $BankPaymentApproval->project_id = $request->input('project_id');

        if (!empty($request->input('vendor_id'))) {
            $BankPaymentApproval->vendor_id = $request->input('vendor_id');
        }

        if ($request->input('project_id') == config('constants.OTHER_PROJECT_ID')) {
            $BankPaymentApproval->other_project_detail = $request->input('other_project_detail');
        }

        $total_tax = $request->input('igst_amount') + $request->input('cgst_amount') + $request->input('sgst_amount');

        $BankPaymentApproval->bank_id = $request->input('bank_id');
        $BankPaymentApproval->project_site_id = $request->input('project_site_id');
        $BankPaymentApproval->client_id = $request->input('client_id');
        $BankPaymentApproval->user_id = Auth::user()->id;
        $BankPaymentApproval->bank_details = $request->input('bank_details');
        // $BankPaymentApproval->cheque_number = !empty($request->input('cheque_number')) ? $request->input('cheque_number') : "";
        $BankPaymentApproval->amount = $request->input('amount') + $total_tax - $request->input('tds_amount');
        $BankPaymentApproval->note = $request->input('note');
        $BankPaymentApproval->invoice_no = $request->input('invoice_no');
        $BankPaymentApproval->igst_amount = $request->input('igst_amount');
        $BankPaymentApproval->cgst_amount = $request->input('cgst_amount');
        $BankPaymentApproval->sgst_amount = $request->input('sgst_amount');
        $BankPaymentApproval->tds_amount = $request->input('tds_amount');
        $BankPaymentApproval->section_type_id = $request->input('section_type_id');
        $BankPaymentApproval->payment_method = $request->input('payment_method');
        if($request->input('created_at_date') && !empty($request->input('created_at_date')))
        {
            $BankPaymentApproval->created_at = Carbon::parse($request->input('created_at_date'))->format('Y-m-d H:i:s');
        }
        else
        {
            $BankPaymentApproval->created_at = date('Y-m-d H:i:s');
        }
        $BankPaymentApproval->created_ip = $request->ip();
        $BankPaymentApproval->updated_at = date('Y-m-d H:i:s');
        $BankPaymentApproval->updated_ip = $request->ip();
        $BankPaymentApproval->total_amount = $request->input('total_amount');
        $BankPaymentApproval->micr_code = $request->input('micr_code');
        $BankPaymentApproval->swift_code = $request->input('swift_code');


        if($request->input('tender_id') && $request->input('tender_type')){
            $BankPaymentApproval->tender_id = $request->input('tender_id');
            $BankPaymentApproval->tender_type = $request->input('tender_type');
        }

        if ($request->input('entry_code') && $request->input('entry_code') > 0) {
            $BankPaymentApproval->main_entry = 0;
            $BankPaymentApproval->entry_code = $request->input('entry_code');
            $old_sum = BankPaymentApproval::where('entry_code', $request->input('entry_code'))->get()->sum('amount');
            $completed_amt = $old_sum + $request->input('amount');
            $BankPaymentApproval->entry_completed = 'Yes';
            $main_entry = BankPaymentApproval::where('entry_code', $request->input('entry_code'))->where('main_entry', 1)->get();
            if ($request->input('total_amount') <= $completed_amt) {
                BankPaymentApproval::where('id', $main_entry[0]->id)->update(['entry_completed' => 'Yes']);
            }
        } else {
            $BankPaymentApproval->main_entry = 1;
            if ($BankPaymentApproval->total_amount <= $BankPaymentApproval->amount) {
                $BankPaymentApproval->entry_completed = 'Yes';
            } else {
                $BankPaymentApproval->entry_completed = 'No';
            }
        }

        if (Auth::user()->role == config('constants.ACCOUNT_ROLE')) {
            $BankPaymentApproval->first_approval_status = "Approved";
            $BankPaymentApproval->first_approval_datetime = date('Y-m-d H:i:s');
            $BankPaymentApproval->first_approval_id = Auth::user()->id;
            $admin_user = User::where('role', config('constants.Admin'))->get(['id']);
            $this->notification_task->bankPaymentFirstApprovalNotify([$admin_user[0]->id]);
        }

        if (Auth::user()->role == config('constants.SuperUser')) {

            $BankPaymentApproval->first_approval_status = "Approved";
            $BankPaymentApproval->first_approval_datetime = date('Y-m-d H:i:s');
            $BankPaymentApproval->first_approval_id = Auth::user()->id;

            $BankPaymentApproval->second_approval_status = "Approved";
            $BankPaymentApproval->second_approval_datetime = date('Y-m-d H:i:s');
            $BankPaymentApproval->second_approval_id = Auth::user()->id;

            $BankPaymentApproval->third_approval_status = "Approved";
            $BankPaymentApproval->third_approval_datetime = date('Y-m-d H:i:s');
            $BankPaymentApproval->third_approval_id = Auth::user()->id;
            $BankPaymentApproval->status = "Approved";

        }

        //21-02-2020
        /* if ($request->file('payment_file')) {

            $payment_file = $request->file('payment_file');

            $original_file_name = explode('.', $payment_file->getClientOriginalName());

            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);


            $file_path = $payment_file->storeAs('public/payment_file', $new_file_name);
            if ($file_path) {
                $BankPaymentApproval->payment_file = $file_path;
            }
        } */

        //21-02-2020
        if ($request->file('invoice_file')) {

            $invoice_file = $request->file('invoice_file');

            $original_file_name = explode('.', $invoice_file->getClientOriginalName());

            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);


            $file_path = $invoice_file->storeAs('public/invoice_file', $new_file_name);
            if ($file_path) {
                $BankPaymentApproval->invoice_file = $file_path;
            }
        }

        if ($BankPaymentApproval->save()) {


            if($request->input('tender_id') && $request->input('tender_type')){
                TenderPaymentRequest::where('tender_id',$request->input('tender_id'))->where('tender_type',$request->input('tender_type'))->update(['payment_status' => 'Success']);
            }

            if (!$request->input('entry_code') || $request->input('entry_code') == 0) {
                $update_arr = [
                    'entry_code' => 10000 + $BankPaymentApproval->id,
                ];
                BankPaymentApproval::where('id', $BankPaymentApproval->id)->update($update_arr);
            }

            if(!empty($request->input('budget_sheet_id'))){
                $budget_data = BudgetSheetApproval::whereId($request->input('budget_sheet_id'))->first();
                if($budget_data['release_hold_amount_status'] == "Approved"){
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
                        //check budget sheet file exist in budget_sheet_file folder if yes then will be copy and paste in invoice_file folder
                        if ($isExists) {
                            $budget_file_name = str_replace("public/budget_sheet_file/", "",$budget_file);
                            //check budget sheet file already exist in invoice_file folder if yes then do not need to copy paste...
                            if (!Storage::exists("public/invoice_file/{$budget_file_name}")) {
                                Storage::copy($budget_file, "public/invoice_file/{$budget_file_name}");
                                $existing_file_arr = [
                                    'bank_payment_id' => $BankPaymentApproval->id,
                                    'bank_payment_file' => "public/invoice_file/{$budget_file_name}",
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'created_ip' => $request->ip(),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'updated_ip' => $request->ip()
                                ];
                                \App\Bank_payment_file::insert($existing_file_arr);
                            }
                        }
                    }
                }
                //----------- upload invoice no & invoice file
                $exising_invoice_files = \App\Budget_sheet_invoice_files::where('budget_sheet_id',$request->input('budget_sheet_id'))->pluck('budget_sheet_invoice_file')->toArray();
                if (count($exising_invoice_files) > 0) {

                    foreach ($exising_invoice_files as $key => $invoice_file) {
                        $isInvoiceExists = Storage::exists($invoice_file);
                        //check invoice file exist in budget_sheet_invoice_files folder if yes then will be copy and paste in bank_payment_invoice_files folder
                        if ($isInvoiceExists) {
                            $invoice_file_name = str_replace("public/budget_sheet_invoice_files/", "",$invoice_file);
                            //check budget sheet file already exist in bank_payment_invoice_files folder if yes then do not need to copy paste...
                            if (!Storage::exists("public/bank_payment_invoice_files/{$invoice_file_name}")) {
                                Storage::copy($invoice_file, "public/bank_payment_invoice_files/{$invoice_file_name}");

                                BankPaymentApproval::where('id', $BankPaymentApproval->id)->update(['invoice_file'=> "public/bank_payment_invoice_files/{$invoice_file_name}"]);
                            }
                        }
                    }
                }

            }
            //------------------------------------------------------------------
            //upload multiple files
            if ($request->file('bank_payment_files')) {

                $bank_payment_files_list = $request->file('bank_payment_files');
                foreach ($bank_payment_files_list as $key => $bank_payment_files) {

                    $original_file_name = explode('.', $bank_payment_files->getClientOriginalName());

                    $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);


                    $file_path = $bank_payment_files->storeAs('public/invoice_file', $new_file_name);

                    $bank_payment_file_arr = [
                        'bank_payment_id' => $BankPaymentApproval->id,
                        'bank_payment_file' => $file_path,
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_ip' => $request->ip(),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_ip' => $request->ip()
                    ];
                    \App\Bank_payment_file::insert($bank_payment_file_arr);
                }
            }

            $update_arr = [
                // 'issue_date' => !empty($request->input('issue_date')) ? date('Y-m-d', strtotime($request->input('issue_date'))) : "",
                'party_detail' => $request->input('vendor_id'),
                'project_id' => $request->input('project_id'),
                'work_detail' => $request->input('note'),
                'amount' => $request->input('amount'),
                'is_used' => 'used',
                'use_type' => 'bank_payment_approval',
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::user()->id,
                'updated_ip' => $request->ip()
            ];
            if ($request->input('other_project_detail') == config('constants.OTHER_PROJECT_ID')) {
                $update_arr['other_project_detail'] = $request->input('other_project_detail');
            }
            \App\ChequeRegister::where('ch_no', $request->input('cheque_number'))
                ->update($update_arr);

            // User Action Log
            $company_name = Companies::whereId($request->input('company_id'))->value('company_name');
            $client_name = Clients::whereId($request->input('client_id'))->value('client_name');
            $project_name = Projects::whereId($request->input('project_id'))->value('project_name');
            $project_site = Project_sites::whereId($request->input('project_site_id'))->value('site_name');
            $vendor_name = Vendors::whereId($request->input('vendor_id'))->value('vendor_name');
            $add_string = "<br> Company Name: ".$company_name."<br> Client Name: ".$client_name."<br> Project Name: ".$project_name."<br> Site Name: ".$project_site."<br> Vender Name: ".$vendor_name."<br>Amount: ".$request->get('amount');
            $entry_code = BankPaymentApproval::where('id', $BankPaymentApproval->id)->value('entry_code');
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => "Bank Payment entry code ".$entry_code." added".$add_string,
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            return redirect()->route('admin.payment')->with('success', 'Payment Bank Details added successfully.');
        } else {
            return redirect()->route('admin.add_bank_payment_detail')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function edit_bank_payment_detail($id)
    {

        $this->data['page_title'] = "Edit Payment";
        $this->data['bank_payment_detail'] = $bank_payment_detail = BankPaymentApproval::where('id', $id)->get();
        $check_result = Permissions::checkPermission($this->module_id, 2);
        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }

        if ($this->data['bank_payment_detail']->count() == 0) {
            return redirect()->route('admin.edit_payment')->with('error', 'Error Occurred. Try Again!');
        }

        //new logic
        $entry_code = BankPaymentApproval::where('entry_code', $bank_payment_detail[0]->entry_code)
            ->where('entry_completed', 'Yes')
            ->where('main_entry', 1)->value('entry_code');

        $main_entry_list = BankPaymentApproval::where('main_entry', 1)->where('entry_completed', 'No')
            ->pluck('entry_code')->toArray();

        if ($entry_code) {
            array_push($main_entry_list, $entry_code);
        }

        $this->data['main_entry_list'] = $main_entry_list;
        $this->data['section_type'] = TdsSectionType::select('id', 'section_type')->where('status', 'Enabled')->orderBy('section_type', 'asc')->get();
        $this->data['Companies'] = Companies::select('id', 'company_name')->orderBy('company_name', 'asc')->get();
        $this->data['Projects'] = Projects::select('id', 'project_name')->orderBy('project_name', 'asc')->get();

        $this->data['cheque_number'] = \App\ChequeRegister::where('ch_no', $this->data['bank_payment_detail'][0]->cheque_number)->get(['ch_no', 'issue_date', 'id']);
        $this->data['chno'] = !empty($this->data['cheque_number'][0]->id) ? $this->data['cheque_number'][0]->id : 0;
        $this->data['ch_no'] = !empty($this->data['cheque_number'][0]->ch_no) ? $this->data['cheque_number'][0]->ch_no : 0;
        return view('admin.payment.edit_payment', $this->data);
    }

    public function update_payment(Request $request)
    {

        $validator_normal = Validator::make($request->all(), [
            'bank_id' => 'required',
            'bank_details' => 'required',
            'amount' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.payment')->with('error', 'Please follow validation rules.');
        }

        if ($request->input('project_id') == config('constants.OTHER_PROJECT_ID')) {
            $other_project_detail = $request->input('other_project_detail');
        } else {
            $other_project_detail = "";
        }
        $total_tax = $request->input('igst_amount') + $request->input('cgst_amount') + $request->input('sgst_amount');
        $paymentModel = [
            'bank_id' => $request->input('bank_id'),
            'payment_options' => $request->input('payment_options'),
            //'budget_sheet_id' => $request->input('budget_sheet_id'),
            'client_id' => $request->input('client_id'),
            'project_site_id' => $request->input('project_site_id'),
            'company_id' => $request->input('company_id'),
            'project_type' => $request->input('project_type'),
            'project_id' => $request->input('project_id'),
            'other_project_detail' => $other_project_detail,
            'vendor_id' => !empty($request->input('vendor_id')) ? $request->input('vendor_id') : "",
            'bank_details' => $request->input('bank_details'),
            'note' => $request->input('note'),
            'invoice_no' => $request->input('invoice_no'),
            'total_amount' => $request->input('total_amount'),
            'amount' => $request->input('amount') + $total_tax - $request->input('tds_amount'),
            'igst_amount' => $request->input('igst_amount'),
            'cgst_amount' => $request->input('cgst_amount'),
            'sgst_amount' => $request->input('sgst_amount'),
            'tds_amount' => $request->input('tds_amount'),
            'section_type_id' => $request->input('section_type_id'),
            'payment_method' => $request->input('payment_method'),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'micr_code' => $request->input('micr_code'),
            'swift_code' => $request->input('swift_code'),
        ];

        if ($request->input('payment_options') == 'Emergency Option') {
            $paymentModel['budget_sheet_id'] = NULL;
        } elseif ($request->input('payment_options') == 'Regular') {
            $paymentModel['budget_sheet_id'] = NULL;
        }else{
            $paymentModel['budget_sheet_id'] = $request->input('budget_sheet_id');
        }

        $old_payment_detail = BankPaymentApproval::where('id', $request->input('id'))->get();


        //if user select main entry in main entry dropdown list
        if ($request->input('entry_code')) {
            //$new_payment_detail = BankPaymentApproval::where('entry_code', $request->input('entry_code'))->get();

            $paymentModel['entry_code'] = $request->input('entry_code');
        }

        $old_already_completed_amount = BankPaymentApproval::where('entry_code', $old_payment_detail[0]->entry_code)->get()->sum('amount');

        //already added record with entry code selected in dropdown
        //$new_payment_detail = BankPaymentApproval::where('entry_code', $request->input('entry_code'))->get();
        $new_update_record_main_entry = BankPaymentApproval::where('entry_code', $request->input('entry_code'))
            ->where('main_entry', 1)->get();

        if ($new_update_record_main_entry[0]->id == $request->input('id')) {
            $paymentModel['main_entry'] = 1;
        } else {
            $paymentModel['main_entry'] = 0;
        }

        BankPaymentApproval::where('id', $request->input('id'))->update($paymentModel);

        //If total amount change in main entry then update all it's child entries...
        BankPaymentApproval::where('entry_code', $request->input('entry_code'))->update(['total_amount' => $request->input('total_amount')]);

        if ($request->input('entry_code') == $old_payment_detail[0]->entry_code) {

            //just update entry with new amount and it's parent entry based on amount is completed or not
            //BankPaymentApproval::where('id', $request->input('id'))->update($paymentModel);
            $new_update_record_main_entry = BankPaymentApproval::where('entry_code', $request->input('entry_code'))
                ->where('main_entry', 1)->get();

            $new_completed_amt = BankPaymentApproval::where('entry_code', $request->input('entry_code'))->get()->sum('amount');

            //if ($new_completed_amt >= $request->input('total_amount')) {
            if ($new_completed_amt >= $new_update_record_main_entry[0]->total_amount) {
                $update_arr = [
                    'entry_completed' => 'Yes',
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip()
                ];
                BankPaymentApproval::where('id', $new_update_record_main_entry[0]->id)->update($update_arr);
            } else {
                $update_arr = [
                    'entry_completed' => 'No',
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip()
                ];
                BankPaymentApproval::where('id', $new_update_record_main_entry[0]->id)->update($update_arr);
            }
        } else {

            $newcompleted_amount = ($old_already_completed_amount - $old_payment_detail[0]->amount);
            $old_update_record_main_entry = BankPaymentApproval::where('entry_code', $old_payment_detail[0]->entry_code)
                ->where('main_entry', 1)->get();
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
            BankPaymentApproval::where('id', $old_update_record_main_entry[0]->id)->update($update_arr);

            //BankPaymentApproval::where('id', $request->input('id'))->update($paymentModel);

            $new_already_completed_amount = BankPaymentApproval::where('entry_code', $request->input('entry_code'))->get()->sum('amount');

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

            $new_update_record_main_entry = BankPaymentApproval::where('entry_code', $request->input('entry_code'))
                ->where('main_entry', 1)->get();

            BankPaymentApproval::where('id', $new_update_record_main_entry[0]->id)->update($update_arr);
        }


        if ($old_payment_detail[0]->status != 'Approved') {
            $paymentModel['first_approval_status'] = "Pending";
            $paymentModel['second_approval_status'] = "Pending";
            $paymentModel['third_approval_status'] = "Pending";
            $paymentModel['status'] = "Pending";
        }
        if (Auth::user()->role == config('constants.ACCOUNT_ROLE')) {
            $paymentModel['first_approval_status'] = "Approved";
            //$paymentModel['first_approval_datetime'] = date('Y-m-d H:i:s');
            $paymentModel['first_approval_id'] = Auth::user()->id;
            $admin_user = User::where('role', config('constants.Admin'))->get(['id']);
            $this->notification_task->bankPaymentFirstApprovalNotify([$admin_user[0]->id]);
        }
        /* if (!empty($request->input('cheque_number'))) {
            $paymentModel['cheque_number'] = $request->input('cheque_number');
        } */


        //21-02-2020
        /* if ($request->file('payment_file')) {

            $payment_file = $request->file('payment_file');

            $original_file_name = explode('.', $payment_file->getClientOriginalName());

            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);


            $file_path = $payment_file->storeAs('public/payment_file', $new_file_name);
            if ($file_path) {
                $paymentModel['payment_file'] = $file_path;
            }
        } */

        //21-02-2020
        if ($request->file('invoice_file')) {

            $invoice_file = $request->file('invoice_file');

            $original_file_name = explode('.', $invoice_file->getClientOriginalName());

            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);


            $file_path = $invoice_file->storeAs('public/invoice_file', $new_file_name);
            if ($file_path) {
                $paymentModel['invoice_file'] = $file_path;
            }
        }


        BankPaymentApproval::where('id', $request->input('id'))->update($paymentModel);

        //upload multiple files
        if ($request->file('bank_payment_files')) {
            $bank_payment_files_list = $request->file('bank_payment_files');
            foreach ($bank_payment_files_list as $key => $bank_payment_files) {

                $original_file_name = explode('.', $bank_payment_files->getClientOriginalName());

                $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);


                $file_path = $bank_payment_files->storeAs('public/invoice_file', $new_file_name);

                $bank_payment_file_arr = [
                    'bank_payment_id' => $request->input('id'),
                    'bank_payment_file' => $file_path,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_ip' => $request->ip(),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip()
                ];
                \App\Bank_payment_file::insert($bank_payment_file_arr);
            }
        }

        $update_arr = [
            'issue_date' => !empty($request->input('issue_date')) ? date('Y-m-d', strtotime($request->input('issue_date'))) : "",
            'party_detail' => $request->input('vendor_id'),
            'project_id' => $request->input('project_id'),
            'work_detail' => $request->input('note'),
            'amount' => $request->input('amount'),
            'is_used' => 'used',
            'use_type' => 'bank_payment_approval',
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => Auth::user()->id,
            'updated_ip' => $request->ip(),
            'other_project_detail' => $request->input('other_project_detail')
        ];

        \App\ChequeRegister::where('ch_no', $request->input('cheque_number'))->update($update_arr);

        if ($request->input('cheque_number') != $request->input('old_cheque_number')) {
            $update_arr = [
                'issue_date' => "",
                'party_detail' => "",
                'project_id' => "",
                'work_detail' => "",
                'amount' => "",
                'is_used' => 'not_used',
                'use_type' => "",
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::user()->id,
                'updated_ip' => $request->ip(),
                'other_project_detail' => ''
            ];

            \App\ChequeRegister::where('ch_no', $request->input('old_cheque_number'))->update($update_arr);
        }

        // User Action Log
        $company_name = Companies::whereId($request->input('company_id'))->value('company_name');
        $client_name = Clients::whereId($request->input('client_id'))->value('client_name');
        $project_name = Projects::whereId($request->input('project_id'))->value('project_name');
        $project_site = Project_sites::whereId($request->input('project_site_id'))->value('site_name');
        $vendor_name = Vendors::whereId($request->input('vendor_id'))->value('vendor_name');
        $add_string = "<br> Company Name: ".$company_name."<br> Client Name: ".$client_name."<br> Project Name: ".$project_name."<br> Site Name: ".$project_site."<br> Vender Name: ".$vendor_name."<br>Amount: ".$request->get('amount');

        $entry_code = BankPaymentApproval::where('id', $request->input('id'))->value('entry_code');
        $action_data = [
            'user_id' => Auth::user()->id,
            'task_body' => "Bank Payment entry code ".$entry_code." updated".$add_string,
            'created_ip' => $request->ip(),
        ];
        $this->user_action_logs->action($action_data);

        return redirect()->route('admin.payment')->with('success', 'Bank Payment successfully updated.');
    }

    public function payment_list(Request $request)
    {
        $this->data['page_title'] = "Bank Payment Approval";
        $access_level = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => $this->module_id])->get()->first();
        //jayram desai 521
        $this->data['date'] = "";
        if (!empty($access_level)) {
            $this->data['access_rule'] = $access_level->access_level;
        }

        $Results = DB::table('bank_payment_approval')
            ->join('users', 'bank_payment_approval.user_id', '=', 'users.id')
            ->join('company', 'company.id', '=', 'bank_payment_approval.company_id')
            ->join('project', 'project.id', '=', 'bank_payment_approval.project_id')
            ->join('vendor', 'vendor.id', '=', 'bank_payment_approval.vendor_id')
            ->join('bank', 'bank.id', '=', 'bank_payment_approval.bank_id')
            ->leftJoin('budget_sheet_approval', 'budget_sheet_approval.id', '=', 'bank_payment_approval.budget_sheet_id')
            ->leftjoin('rtgs_register','rtgs_register.id','=','bank_payment_approval.rtgs_number')
            ->leftJoin('clients', 'clients.id', '=', 'bank_payment_approval.client_id')
            ->leftJoin('project_sites', 'project_sites.id', '=', 'bank_payment_approval.project_site_id')
            ->leftJoin('cheque_register', 'cheque_register.id', '=', 'bank_payment_approval.cheque_number')
            ->leftJoin('vendors_bank', 'vendors_bank.id', '=', 'bank_payment_approval.bank_details')
            ->leftJoin('tds_section_type', 'tds_section_type.id', '=', 'bank_payment_approval.section_type_id')
            ->leftJoin('tender', 'tender.id', '=', 'bank_payment_approval.tender_id')
            ->orderBy('bank_payment_approval.created_at', 'DESC');

        //jayram desai 534-548
        if (!empty($request->get('date'))) {
            $this->data['date'] = $request->get('date');
            $date = $request->get('date');
            $mainDate = explode("-", $date);
            $strFirstdate = str_replace("/", "-", $mainDate[0]);
            $strLastdate = str_replace("/", "-", $mainDate[1]);

            $first_date = date('Y-m-d H:i:s', strtotime($strFirstdate));
            $second_date = date('Y-m-d H:i:s', strtotime($strLastdate));

            $Results->whereBetween('bank_payment_approval.created_at', [$first_date, $second_date]);
        }
        $this->data['bank_payment_approval_history'] = $bank_payment_approval_history = $Results->get(['vendors_bank.bank_name as vendor_bank_name', 'vendors_bank.ac_number','vendors_bank.ifsc',
         'bank_payment_approval.*', 'bank_details', 'bank.bank_name', 'cheque_register.ch_no','cheque_register.issue_date','budget_sheet_approval.budhet_sheet_no',
          'users.name as user_name', 'company.company_name', 'project.project_name','rtgs_register.rtgs_no',
           'vendor.vendor_name','clients.client_name','clients.location', 'project_sites.site_name', 'tds_section_type.section_type','tender.tender_sr_no'])->toArray();

        //    dd($this->data['bank_payment_approval_history']);
        return view('admin.payment.payment_list', $this->data);
    }

    public function approve_bank_payment(Request $request)
    {
        $check_result = Permissions::checkPermission($this->module_id, 2);

        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }
        $id = $request->input('approve_paymentid');
        $approve_note = $request->input('approve_note');
        if (Auth::user()->role == config('constants.ACCOUNT_ROLE')) {
            $updateData = [
                'first_approval_status' => 'Approved',
                'first_approval_datetime' => date('Y-m-d H:i:s'),
                'first_approval_id' => Auth::user()->id,
                'first_approval_remark' => $approve_note
            ];

            $admin_user = User::where('role', config('constants.Admin'))->get(['id']);

            $this->notification_task->bankPaymentFirstApprovalNotify([$admin_user[0]->id]);
        } elseif (Auth::user()->role == config('constants.Admin')) {
            $bankApprovealData = BankPaymentApproval::select('users.name', 'bank_payment_approval.amount', 'users.email', 'users.id as user_id')
                ->join('users', 'bank_payment_approval.user_id', '=', 'users.id')
                ->where('bank_payment_approval.id', $id)->get();


            $this->notification_task->bankPaymentSecondApprovalNotify([$this->super_admin->id]);

            $updateData = [
                'second_approval_status' => 'Approved', 'second_approval_id' => Auth::user()->id,
                'second_approval_remark' => $approve_note, 'second_approval_datetime' => date('Y-m-d H:i:s'),
            ];
        } elseif (Auth::user()->role == config('constants.SuperUser')) {

            $bankApprovealData = BankPaymentApproval::select('users.name', 'bank_payment_approval.amount', 'users.email', 'users.id as user_id')
                ->join('users', 'bank_payment_approval.user_id', '=', 'users.id')
                ->where('bank_payment_approval.id', $id)->get();
            $data = [
                'username' => $bankApprovealData[0]['name'],
                'amount' => $bankApprovealData[0]['amount'],
                'email' => $bankApprovealData[0]['email'],
                'status' => 'Approved',
            ];

            $this->common_task->approveRejectPaymentEmail($data);
            //send notification to user who requested about approval
            $this->notification_task->bankPaymentThirdApprovalNotify([$bankApprovealData[0]->user_id]);

            $updateData = [
                'third_approval_status' => 'Approved', 'third_approval_id' => Auth::user()->id,
                'status' => 'Approved', 'third_approval_remark' => $approve_note, 'third_approval_datetime' => date('Y-m-d H:i:s'),
            ];
        }

        if (BankPaymentApproval::where('id', $id)->update($updateData)) {

            // User Action Log
            $entry_code = BankPaymentApproval::where('id', $id)->value('entry_code');
            $amount = BankPaymentApproval::where('id', $id)->value('amount');
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => "Bank Payment entry code ".$entry_code." approved <br>Amount: ".$amount,
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            return redirect()->route('admin.payment_list')->with('success', 'Status successfully updated.');
        }
        return redirect()->route('admin.payment_list')->with('error', 'Error during operation. Try again!');
    }

    public function get_approval_note(Request $request)
    {
        $approve_id = $request->input('approve_id');
        $approval_data = BankPaymentApproval::where('id', $approve_id)->get();
        if ($approval_data->count() == 0) {
            return response()->json(['status' => false]);
        } else {
            $approval_note1 = $approval_data[0]->first_approval_remark ? $approval_data[0]->first_approval_remark : "NA";
            $approval_note2 = $approval_data[0]->second_approval_remark ? $approval_data[0]->second_approval_remark : "NA";
            $approval_note3 = $approval_data[0]->third_approval_remark ? $approval_data[0]->third_approval_remark : "NA";

            return response()->json(['status' => true, 'approval_note1' => $approval_note1, 'approval_note2' => $approval_note2, 'approval_note3' => $approval_note3]);
        }
    }

    public function reject_bank_payment(Request $request)
    {
        $check_result = Permissions::checkPermission(24, 2);

        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }
        $id = $request->input('paymentid');
        $note = $request->input('note');
        if (Auth::user()->role == config('constants.ACCOUNT_ROLE')) {
            $bankApprovealData = BankPaymentApproval::select('users.name', 'bank_payment_approval.amount', 'users.email', 'users.id as user_id')
                ->join('users', 'bank_payment_approval.user_id', '=', 'users.id')
                ->where('bank_payment_approval.id', $id)->get()->toArray();

            $data = [
                'username' => $bankApprovealData[0]['name'],
                'amount' => $bankApprovealData[0]['amount'],
                'email' => $bankApprovealData[0]['email'],
                'status' => 'Rejected',
            ];

            $this->common_task->approveRejectPaymentEmail($data);
            //send notification about rejected
            $this->notification_task->bankPaymentRejectNotify([$bankApprovealData[0]['user_id']]);

            $updateData = ['reject_note' => $note, 'first_approval_status' => 'Rejected', 'first_approval_id' => Auth::user()->id, 'first_approval_datetime' => date('Y-m-d H:i:s'), 'status' => 'Rejected'];
        } elseif (Auth::user()->role == config('constants.Admin')) {
            $bankApprovealData = BankPaymentApproval::select('users.name', 'bank_payment_approval.amount', 'users.id as user_id', 'users.email', 'users.id as user_id')
                ->join('users', 'bank_payment_approval.user_id', '=', 'users.id')
                ->where('bank_payment_approval.id', $id)->get()->toArray();

            $data = [
                'username' => $bankApprovealData[0]['name'],
                'amount' => $bankApprovealData[0]['amount'],
                'email' => $bankApprovealData[0]['email'],
                'status' => 'Rejected',
            ];

            $this->common_task->approveRejectPaymentEmail($data);

            //send notification to user who requested about approval
            $this->notification_task->bankPaymentRejectNotify([$bankApprovealData[0]['user_id']]);

            $updateData = ['reject_note' => $note, 'second_approval_status' => 'Rejected', 'second_approval_id' => Auth::user()->id, 'second_approval_datetime' => date('Y-m-d H:i:s'), 'status' => 'Rejected'];
        } elseif (Auth::user()->role == config('constants.SuperUser')) {

            $bankApprovealData = BankPaymentApproval::select('users.name', 'bank_payment_approval.amount', 'users.id as user_id', 'users.email', 'users.id as user_id')
                ->join('users', 'bank_payment_approval.user_id', '=', 'users.id')
                ->where('bank_payment_approval.id', $id)->get()->toArray();

            $data = [
                'username' => $bankApprovealData[0]['name'],
                'amount' => $bankApprovealData[0]['amount'],
                'email' => $bankApprovealData[0]['email'],
                'status' => 'Rejected',
            ];

            $this->common_task->approveRejectPaymentEmail($data);

            //send notification to user who requested about approval
            $this->notification_task->bankPaymentRejectNotify([$bankApprovealData[0]['user_id']]);

            $updateData = ['reject_note' => $note, 'third_approval_status' => 'Rejected', 'third_approval_id' => Auth::user()->id, 'third_approval_datetime' => date('Y-m-d H:i:s'), 'status' => 'Rejected'];
        }


        if (BankPaymentApproval::where('id', $id)->update($updateData)) {

            // User Action Log
            $entry_code = BankPaymentApproval::where('id', $id)->value('entry_code');
            $amount = BankPaymentApproval::where('id', $id)->value('amount');
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => "Bank Payment entry code ".$entry_code." rejected <br>Amount: ".$amount."<br>Reject Notes: ".$note,
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);
            return redirect()->route('admin.payment_list')->with('success', 'Status successfully updated.');
        }
        return redirect()->route('admin.payment_list')->with('error', 'Error during operation. Try again!');
    }

    public function get_bank_cheque_list_edit()
    {
        if (!empty($_GET['company_id']) && !empty($_GET['id'])) {
            $company_id = $_GET['company_id'];
            $id = $_GET['id'];
            //$bank_id    = $_GET['bank_id'];
            $this->data['bank_payment_detail'] = BankPaymentApproval::where('id', $id)->get();
            $ch_no = $this->data['bank_payment_detail'][0]->cheque_number;
            $cheque_data = \App\ChequeRegister::select('ch_no', 'id')->where(['company_id' => $company_id])
            ->where(['is_used' => 'not_used'])
            ->where(['is_failed' => '0'])
            ->orWhere('ch_no', $ch_no)->get()->toArray();
            $html = "<option value=''>Select cheque</option>";
            foreach ($cheque_data as $key => $cheque_data_value) {
                $html .= "<option value=" . $cheque_data_value['ch_no'] . ">" . $cheque_data_value['ch_no'] . "</option>";
            }
            echo $html;
            die();
        }
    }

    public function get_cheque_list_bank_payment()   //cheque
    {
        if (!empty($_GET['company_id'])) {
            $company_id = $_GET['company_id'];
            $bank_id = $_GET['bank_id'];

            $cheque_data = \App\ChequeRegister::select('ch_no', 'id')->where(['bank_id' => $bank_id])
            ->where(['company_id' => $company_id])
            ->where(['is_used' => 'not_used'])
            ->where(['is_failed' => '0'])
            ->get()->toArray();
            $html = "<option value=''>Select cheque</option>";
            foreach ($cheque_data as $key => $cheque_data_value) {
                $html .= "<option value=" . $cheque_data_value['ch_no'] . ">" . $cheque_data_value['ch_no'] . "</option>";
            }
            echo $html;
            die();
        }
    }

    //20-03-2020
    public function get_company_client_list(Request $request)  //order by
    {
        $company_id = $request->company_id;

        /* if (empty($company_id)) {
            return response()->json([]);
        } */
        $clients = Clients::select('clients.*')
            ->where('clients.status', 'Enabled')
            ->where(function ($query) use ($company_id) {
                $query->where('clients.company_id', $company_id);
                $query->orWhere('clients.company_id', 0);
            })->orderBy('client_name', 'asc')
            ->get();

        return response()->json($clients);
    }

    public function get_client_project_list(Request $request)   //order by
    {

        $client_id = $request->client_id;
        $request_data = $request->all();

        $partial_query = Projects::select('project.*')
            ->where('project.status', 'Enabled')
            ->where(function ($query) use ($request_data) {
                $query->where('project.client_id', $request_data['client_id']);
                $query->orWhere('project.client_id', 1);
            });

            if (isset($request_data['project_type'])) {
                $partial_query->where('project.project_type', $request_data['project_type']);
            }

            $projects = $partial_query->orderBy('project_name', 'asc')->get();

            // dd($projects->toArray());
            // dd($projects->toSql(), $projects->getBindings());

        return response()->json($projects);
    }

    public function get_project_sites_list(Request $request)  //order by
    {
        $project_id = $request->project_id;

        $project_sites = Project_sites::select('project_sites.*')
            ->where('project_sites.status', 'Enabled')
            ->where(function ($query) use ($project_id) {
                $query->where('project_sites.project_id', $project_id)
                ->orWhere('project_sites.project_id', 1);
            })->orderBy('site_name', 'asc')
            ->get();

        return response()->json($project_sites);
    }

    //20-03
    public function get_vendor_bank_details()   //order by
    {
        if (!empty($_GET['company_id'])) {
            $company_id = $_GET['company_id'];
            $vendor_id = $_GET['vendor_id'];

            $vendor_bank_data = Vendors_bank::select('bank_name', 'id', 'ac_number')->where(['vendor_id' => $vendor_id])->where(['company_id' => $company_id])
               ->orderBy('bank_name', 'asc')->get()->toArray();

            $html = "<option value=''>Select Vendor/Party bank</option>";
            foreach ($vendor_bank_data as $key => $vendor_bank_data_value) {
                $html .= "<option value=" . $vendor_bank_data_value['id'] . ">" . $vendor_bank_data_value['bank_name'] . "(" . $vendor_bank_data_value['ac_number'] . ")" . "</option>";
            }
            echo $html;
            die();
        }
    }

    public function get_cheque_list_bank()   //cheque
    {
        if (!empty($_GET['company_id'])) {
            $company_id = $_GET['company_id'];
            //$bank_id    = $_GET['bank_id'];

            $cheque_data = \App\ChequeRegister::select('ch_no', 'id')->where(['company_id' => $company_id])
            ->where(['is_used' => 'not_used'])
            ->where(['is_failed' => '0'])
            ->get()->toArray();
            $html = "<select id='cheque_id' name='cheque_id' class='form-control'>
                    <option>Select cheque</option>";
            foreach ($cheque_data as $key => $cheque_data_value) {
                $html .= "<option value=" . $cheque_data_value['ch_no'] . ">" . $cheque_data_value['ch_no'] . "</option>";
            }

            $html .= "</select>";
            echo $html;
            die();
        }
    }

    public function get_bank_payment_files(Request $request)
    {
        $validator_normal = Validator::make($request->all(), ['id' => 'required']);

        if ($validator_normal->fails()) {
            return response()->json(['status' => false]);
        }

        $bank_payment_id = $request->id;
        $this->data['payment_status'] = $request->payment_status;
        $bank_payment_files = \App\Bank_payment_file::where('bank_payment_id', $bank_payment_id)
            ->get(['id', 'bank_payment_id', 'bank_payment_file']);

        foreach ($bank_payment_files as $key => $files) {

            $bank_payment_files[$key]->file_name = str_replace('public/invoice_file/', '', $files->bank_payment_file);
            if ($files->bank_payment_file) {

                $bank_payment_files[$key]->bank_payment_file = asset('storage/' . str_replace('public/', '', $files->bank_payment_file));
            } else {

                $bank_payment_files[$key]->bank_payment_file = "";
            }
        }

        $this->data['bank_payment_files'] = $bank_payment_files;

        if ($bank_payment_files->count() == 0) {
            return response()->json(['status' => false, 'data' => $this->data]);
        } else {

            return response()->json(['status' => true, 'data' => $this->data]);
        }
    }

    public function delete_bankpayment_file(Request $request)
    {
        $validator_normal = Validator::make($request->all(), ['id' => 'required']);

        if ($validator_normal->fails()) {
            return response()->json(['status' => false]);
        }

        $bank_payment_id = $request->id;

        \App\Bank_payment_file::where('id', $bank_payment_id)
            ->delete();

        return response()->json(['status' => true, 'data' => []]);
    }

    public function get_bank_payment_data(Request $request)
    {
        $entry_code = $request->input('entry_code');
        $this->data['main_entry'] = $main_entry = BankPaymentApproval::leftjoin('company', 'bank_payment_approval.company_id', '=', 'company.id')
            ->leftjoin('project', 'bank_payment_approval.project_id', '=', 'project.id')
            ->leftJoin('clients', 'clients.id', '=', 'bank_payment_approval.client_id')
            ->leftJoin('project_sites', 'project_sites.id', '=', 'bank_payment_approval.project_site_id')
            ->leftjoin('bank', 'bank_payment_approval.bank_id', '=', 'bank.id')
            ->leftjoin('cheque_register', 'bank_payment_approval.cheque_number', '=', 'cheque_register.id')
            ->leftjoin('vendor', 'bank_payment_approval.vendor_id', '=', 'vendor.id')
            ->leftjoin('vendors_bank', 'bank_payment_approval.bank_details', '=', 'vendors_bank.id')
            ->where('bank_payment_approval.entry_code', $entry_code)
            ->where('bank_payment_approval.main_entry', 1)
            ->get(['bank_payment_approval.*']);

        foreach ($main_entry as $key => $val) {
            $main_entry[$key]->bank_details = (int) $val->bank_details;
        }
        $this->data['total_complete_amount'] = BankPaymentApproval::where('entry_code', $entry_code)->get()->sum('amount');
        return response()->json($this->data);
    }

    public function get_previous_payments(Request $request)
    {
        $entry_code = $request->entry_code;
        $bank_payment_id = $request->id;

        $previous_payments = BankPaymentApproval::join('users', 'bank_payment_approval.user_id', '=', 'users.id')
            ->join('company', 'company.id', '=', 'bank_payment_approval.company_id')
            ->join('project', 'project.id', '=', 'bank_payment_approval.project_id')
            ->leftJoin('clients', 'clients.id', '=', 'bank_payment_approval.client_id')
            ->leftJoin('project_sites', 'project_sites.id', '=', 'bank_payment_approval.project_site_id')
            ->leftjoin('vendor', 'vendor.id', '=', 'bank_payment_approval.vendor_id')
            ->leftjoin('bank', 'bank.id', '=', 'bank_payment_approval.bank_id')
            ->leftJoin('cheque_register', 'cheque_register.id', '=', 'bank_payment_approval.cheque_number')
            ->leftJoin('vendors_bank', 'vendors_bank.id', '=', 'bank_payment_approval.bank_details')
            ->where('bank_payment_approval.entry_code', $entry_code)
            ->get(['vendors_bank.bank_name as vendor_bank_name', 'vendors_bank.ac_number', 'bank_payment_approval.*', 'bank.bank_name', 'cheque_register.ch_no', 'users.name as user_name',
             'company.company_name', 'project.project_name', 'vendor.vendor_name','clients.client_name','clients.location', 'project_sites.site_name']);


        $this->data['previous_payments'] = $previous_payments;
        if ($previous_payments->count() > 0) {
            return response()->json(['status' => true, 'data' => $this->data]);
        } else {
            return response()->json(['status' => false, 'data' => []]);
        }
    }

    public function get_bankApproval(Request $request)  //24-03-2020
    {
        $validator_normal = Validator::make($request->all(), ['id' => 'required']);

        if ($validator_normal->fails()) {
            return response()->json(['status' => false]);
        }

        $id = $request->id;
        $bank_payment_records = BankPaymentApproval::leftjoin('rtgs_register','rtgs_register.id','=','bank_payment_approval.rtgs_number')
         ->where('bank_payment_approval.id',$id)->get(['bank_payment_approval.id','rtgs_register.rtgs_no','bank_payment_approval.voucher_no','bank_payment_approval.transaction_note','bank_payment_approval.rtgs_number','bank_payment_approval.company_id',
         'bank_payment_approval.bank_id','bank_payment_approval.cheque_number','bank_payment_approval.check_ref_no',
            'bank_payment_approval.rtgs_ref_no', 'bank_payment_approval.payment_method']);

        $this->data['issue_date'] = \App\ChequeRegister::where('id',$bank_payment_records[0]->cheque_number)->value('issue_date');

        if($bank_payment_records[0]->company_id) {
            $company_id = $bank_payment_records[0]->company_id;
            $bank_data = \App\Banks::select('bank_name','id','ac_number')->where(['company_id' => $company_id])->get()->toArray();
            $html = "<option value=''>Select Bank</option>";
            foreach ($bank_data as $key => $bank_data_value) {
                 $html.= "<option value=".$bank_data_value['id'].">".$bank_data_value['bank_name']." (".$bank_data_value['ac_number'].")"."</option>";
            }
            $this->data['bank_list'] = $html;
            $this->data['company_id'] = $company_id;
         }

        // $this->data['cheque_id'] = \App\ChequeRegister::where('ch_no',$bank_payment_records[0]->cheque_number)->value('id');
        $this->data['bank_records'] = $bank_payment_records;
        if ($bank_payment_records) {
            return response()->json(['status' => true, 'data' => $this->data]);
        } else {
            return response()->json(['status' => false]);
        }

    }

    public function get_bank_cheque_reff_list(Request $request){  //06/07/2020
        // dd($request->all());
        $bank_id = $request->bank_id;
        $company_id = $request->company_id;
        $cheque_data = \App\ChequeRegister::select('check_ref_no', 'id')->where(['bank_id' => $bank_id])
            ->where(['company_id' => $company_id])
            ->where(['is_used' => 'not_used'])
            ->where(['is_failed' => '0'])
            ->where(['is_cancel' => '0'])
            ->groupBy('check_ref_no')
            ->get()->toArray();
        // dd($cheque_data);

        $rtgs_data = \App\RtgsRegister::select('rtgs_ref_no', 'id')->where(['bank_id' => $bank_id])
            ->where(['company_id' => $company_id])
            ->where(['is_used' => 'not_used'])
            ->where(['is_failed' => '0'])
            //->where(['is_cancel' => '0'])
            ->groupBy('rtgs_ref_no')
            ->get()->toArray();

        if($cheque_data){
            $html = "<option value=''>Select Cheque Ref Number</option>";
            foreach ($cheque_data as $key => $value) {
                $html .= '<option value="'.$value['check_ref_no'].'">'.$value['check_ref_no'].'</option>';
            }
            $this->data['cheque_reff_list'] = $html;
        }else{
            $this->data['cheque_reff_list'] = "<option value=''>Select Cheque Ref Number</option>";
        }

        if($rtgs_data){
            $html = "<option value=''>Select Rtgs Ref Number</option>";
            foreach ($rtgs_data as $key => $value) {
                $html .= '<option value=" '. $value['rtgs_ref_no'] .' ">' . $value['rtgs_ref_no'] . '</option>';
            }
            $this->data['rtgs_reff_list'] = $html;
        }else{
            $this->data['rtgs_reff_list'] = "<option value=''>Select Rtgs Ref Number</option>";
        }

        return response()->json(['status' => true, 'data' => $this->data]);
    }

    public function get_bank_cheque_list(Request $request)  //24-03-2020  cheque 06/07/2020
    {
            $bank_id = $request->bank_id;
            $company_id = $request->company_id;
            $check_ref_no = $request->check_ref_no;

            $cheque_data = \App\ChequeRegister::select('ch_no', 'id')
            ->where(['bank_id' => $bank_id])
            ->where(['company_id' => $company_id])
            ->where(['check_ref_no' => $check_ref_no])
            ->where(['is_used' => 'not_used'])
            ->where(['is_failed' => '0'])
            ->where(['is_cancel' => '0'])
            ->get()->toArray();
            $html = "<option value=''>Select cheque</option>";

            foreach ($cheque_data as $key => $cheque_data_value) {
                $html .= "<option value=" . $cheque_data_value['id'] . ">" . $cheque_data_value['ch_no'] . "</option>";
            }
            echo $html;
            die();

    }

    public function get_bank_rtgs_list(Request $request)  //31-03-2020
    {

            $bank_id = $request->bank_id;
            $company_id = $request->company_id;
            $rtgs_ref_no = $request->rtgs_ref_no;

            $rtgs_data = \App\RtgsRegister::select('rtgs_no', 'id')
                            ->where(['bank_id' => $bank_id])
                            ->where(['company_id' => $company_id])
                            ->where(['rtgs_ref_no' => $rtgs_ref_no])
                            ->where(['is_used' => 'not_used'])
                            ->get()->toArray();
            $html = "<option value=''>Select RTGS</option>";

            foreach ($rtgs_data as $key => $rtgs_data_value) {
                $html .= "<option value=" . $rtgs_data_value['id'] . ">" . $rtgs_data_value['rtgs_no'] . "</option>";
            }
            echo $html;
            die();

    }

    public function approve_bankPaymentByAccountant(Request $request)   //24-03-2020
    {

        $validator_normal = Validator::make($request->all(), [
            'id' => 'required',
            // 'transaction_note' => 'required'
        ]);

        if ($validator_normal->fails()) {
            return redirect()->route('admin.payment_list')->with('error', 'Please follow validation rules.');
        }
        // dd($request->all());
        $bank_id  = $request->input('id');



        $update_arr = [

            'bank_id' => $request->input('bank_id'),
            'check_ref_no' => $request->input('check_ref_no'),
            'cheque_number' => $request->input('cheque_number'),
            //'voucher_no' => $request->input('voucher_no'),
            'rtgs_ref_no' => $request->input('rtgs_ref_no'),
            'rtgs_number' => $request->input('rtgs_number'),
            'purchase_order_number' => $request->input('purchase_order_number'),
            'transaction_note' => $request->input('transaction_note'),
            'dd_number' => $request->input('dd_number'),
            // 'payment_method' => $request->input('payment_method'),
        ];
        if ($request->file('payment_file')) {

            $payment_file = $request->file('payment_file');

            $original_file_name = explode('.', $payment_file->getClientOriginalName());

            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);


            $file_path = $payment_file->storeAs('public/payment_file', $new_file_name);
            if ($file_path) {
                $update_arr['payment_file'] = $file_path;
            }
        }
        // dd($update_arr);
        $bank_detail= BankPaymentApproval::where('id', $bank_id)->get();


        if (BankPaymentApproval::where('id', $bank_id)->update($update_arr)) {

            $update_arr = [

            'issue_date' => !empty($request->input('issue_date')) ? date('Y-m-d', strtotime($request->input('issue_date'))) : "",
            'party_detail' => $bank_detail[0]->vendor_id,
            'project_id' => $bank_detail[0]->project_id,
            'work_detail' => $bank_detail[0]->note,
            'amount' => $bank_detail[0]->amount,
                'is_used' => 'used',
                'use_type' => 'bank_payment_approval',
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::user()->id,
                'updated_ip' => $request->ip()

            ];

            \App\ChequeRegister::where('id', $request->input('cheque_number'))->update($update_arr);

            if ($request->input('cheque_number') != $request->input('ch_no')) {
                $old_cheque_arr = [
                    'issue_date' => NULL,
                    'party_detail' => NULL,
                    'project_id' => NULL,
                    'work_detail' => NULL,
                    'amount' => NULL,
                    'is_used' => 'not_used',
                    'use_type' => NULL,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::user()->id,
                    'updated_ip' => $request->ip(),
                    'other_project_detail' => NULL
                ];

                \App\ChequeRegister::where('ch_no', $request->input('ch_no'))->update($old_cheque_arr);
            }

            //============= NEW RTGS

            $rtgs_arr = [


            'party_detail' => $bank_detail[0]->vendor_id,
            'project_id' => $bank_detail[0]->project_id,
            'work_detail' => $bank_detail[0]->note,
            'amount' => $bank_detail[0]->amount,
                'is_used' => 'used',
                'use_type' => 'bank_payment_approval',
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::user()->id,
                'updated_ip' => $request->ip()

            ];

            \App\RtgsRegister::where('id', $request->input('rtgs_number'))->update($rtgs_arr);


            if ($request->input('rtgs_number') != $request->input('rtgs_no')) {
                $rtgs_old_arr = [

                    'party_detail' => NULL,
                    'project_id' => NULL,
                    'work_detail' => NULL,
                    'amount' => NULL,
                    'is_used' => 'not_used',
                    'use_type' => NULL,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::user()->id,
                    'updated_ip' => $request->ip(),
                    'other_project_detail' => NULL
                ];

                \App\RtgsRegister::where('id', $request->input('rtgs_no'))->update($rtgs_old_arr);
            }

            return redirect()->route('admin.payment_list')->with('success', 'Bank Payment successfully Approved.');
        }
        return redirect()->route('admin.payment_list')->with('error', 'Error during operation. Try again!');
    }

    public function get_budget_sheet_data(Request $request)
    {
        $budget_sheet = BudgetSheetApproval::whereId($request->get('budget_sheet_id'))->first();
        if ($budget_sheet) {
            echo json_encode($budget_sheet);
            die;
        } else {
            echo json_encode([]);
            die;
        }
    }

    public function get_budget_sheet_entry_code(Request $request){
        $bank_payment = BankPaymentApproval::where('budget_sheet_id',$request->get('budget_sheet_id'))->where('main_entry',1)->first();
        if ($bank_payment) {
            echo json_encode($bank_payment);
            die;
        } else {
            echo json_encode([]);
            die;
        }
    }

    public function get_bank_tds_report(){
        $bank_payment_full_view_permission = Permissions::checkPermission($this->module_id, 5);

        if (!$bank_payment_full_view_permission) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You do not have access to this module.');
        }

        $this->data['page_title'] = "Bank TDS Report";
        $this->data['companies']  = Companies::orderBy('company_name', 'asc')->pluck('company_name', 'id');
        // dd($this->data);
        return view('admin.payment.tds_report', $this->data);
    }

    public function get_bank_payment_tds_report(){
        $datatable_fields = array(
            'company.company_name',
            'tds_section_type.section_type',
            'bank_payment_approval.tds_amount',
            'bank_payment_approval.payment_options',
            'budget_sheet_approval.budhet_sheet_no',
            'bank_payment_approval.entry_code',
            'users.name',
            'clients.client_name',
            // 'clients.location',
            'project.project_name',
            'bank_payment_approval.other_project_detail',
            'project_sites.site_name',
            'vendor.vendor_name',
            'bank_payment_approval.note',
            'vendors_bank.bank_name',
            'bank.bank_name',
            'cheque_register.ch_no',
            'bank_payment_approval.total_amount',
            'bank_payment_approval.amount',
            'bank_payment_approval.igst_amount',
            'bank_payment_approval.cgst_amount',
            'bank_payment_approval.sgst_amount',
            'bank_payment_approval.main_entry',
            'bank_payment_approval.invoice_no',
            'bank_payment_approval.created_at'
        );
        $request = Input::all();
        $conditions_array = ['bank_payment_approval.user_id' => Auth::user()->id];
        $conditions_array = ['bank_payment_approval.status' => "Approved"];
        // $conditions_array = [];

        $company_id = $request['company_id'];
        $date_range = $request['date_range'];

        if ($company_id != '') {
            $conditions_array['bank_payment_approval.company_id'] = $company_id;
        }
        $start_date = "";
        $end_date = "";
        if ($date_range != '') {
            $get_dates = explode(' - ', $date_range);
            $start_date = date('Y-m-d',strtotime($get_dates[0]));
            $end_date = date('Y-m-d', strtotime($get_dates[1]));
        }

        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'project';
        $join_str[0]['join_table_id'] = 'project.id';
        $join_str[0]['from_table_id'] = 'bank_payment_approval.project_id';

        $join_str[1]['join_type'] = '';
        $join_str[1]['table'] = 'company';
        $join_str[1]['join_table_id'] = 'company.id';
        $join_str[1]['from_table_id'] = 'bank_payment_approval.company_id';

        $join_str[2]['join_type'] = '';
        $join_str[2]['table'] = 'bank';
        $join_str[2]['join_table_id'] = 'bank.id';
        $join_str[2]['from_table_id'] = 'bank_payment_approval.bank_id';

        $join_str[3]['join_type'] = 'left';
        $join_str[3]['table'] = 'cheque_register';
        $join_str[3]['join_table_id'] = 'cheque_register.id';
        $join_str[3]['from_table_id'] = 'bank_payment_approval.cheque_number';

        $join_str[4]['join_type'] = '';
        $join_str[4]['table'] = 'users';
        $join_str[4]['join_table_id'] = 'users.id';
        $join_str[4]['from_table_id'] = 'bank_payment_approval.user_id';

        $join_str[5]['join_type'] = '';
        $join_str[5]['table'] = 'vendor';
        $join_str[5]['join_table_id'] = 'vendor.id';
        $join_str[5]['from_table_id'] = 'bank_payment_approval.vendor_id';

        $join_str[6]['join_type'] = '';
        $join_str[6]['table'] = 'vendors_bank';
        $join_str[6]['join_table_id'] = 'vendors_bank.id';
        $join_str[6]['from_table_id'] = 'bank_payment_approval.bank_details';

        $join_str[7]['join_type'] = 'left';
        $join_str[7]['table'] = 'clients';
        $join_str[7]['join_table_id'] = 'clients.id';
        $join_str[7]['from_table_id'] = 'bank_payment_approval.client_id';

        $join_str[8]['join_type'] = 'left';
        $join_str[8]['table'] = 'project_sites';
        $join_str[8]['join_table_id'] = 'project_sites.id';
        $join_str[8]['from_table_id'] = 'bank_payment_approval.project_site_id';

        $join_str[9]['join_type'] = 'left';
        $join_str[9]['table'] = 'budget_sheet_approval';
        $join_str[9]['join_table_id'] = 'budget_sheet_approval.id';
        $join_str[9]['from_table_id'] = 'bank_payment_approval.budget_sheet_id';

        $join_str[10]['join_type'] = 'left';
        $join_str[10]['table'] = 'tds_section_type';
        $join_str[10]['join_table_id'] = 'tds_section_type.id';
        $join_str[10]['from_table_id'] = 'bank_payment_approval.section_type_id';

        $getfiled = array('bank_payment_approval.*', 'budget_sheet_approval.budhet_sheet_no', 'bank_details', 'bank.bank_name', 'cheque_register.ch_no', 'users.name as user_name', 'company.company_name', 'clients.client_name', 'clients.location', 'project.project_name', 'project_sites.site_name', 'vendor.vendor_name', 'vendors_bank.bank_name as vendor_bank_name', 'vendors_bank.ac_number', 'tds_section_type.section_type');
        $table = "bank_payment_approval";

        echo Common_query::get_list_date_range($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str,$start_date,$end_date);

        die();
    }

    //18/09/2020
    public function delete_bank_payment($id) {

        if(Auth::user()->role !== config('constants.SuperUser')){
            return redirect()->route('admin.payment_list')->with('error','Access Denied. You are not authorized to access that functionality.');
        }

        if (BankPaymentApproval::where('id', $id)->delete()) {
            return redirect()->route('admin.payment_list')->with('success', 'Delete successfully updated.');
        }
        return redirect()->route('admin.payment_list')->with('error', 'Error during operation. Try again!');
    }

    public function tender_payment_request_list(){
        $this->data['page_title'] = "Tender Payment Request";
        return view('admin.payment.tender_payment_request', $this->data);
    }

    public function get_tender_payment_request_list(){
        $datatable_fields = array('tender.tender_sr_no','tender.tender_fee_amount','tender_fee_in_favour_of','tender_fee_in_form_of','tender_payment_request.tender_type','tender_payment_request.payment_status','tender_payment_request.created_at');
        $request = Input::all();
        $conditions_array = [];

        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'tender';
        $join_str[0]['join_table_id'] = 'tender.id';
        $join_str[0]['from_table_id'] = 'tender_payment_request.tender_id';

        $getfiled = array('tender_payment_request.*','tender.*','tender_payment_request.created_at as created_at_added');
        $table = "tender_payment_request";

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);

        die();
    }
}
