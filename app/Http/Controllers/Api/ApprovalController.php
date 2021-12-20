<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Lib\NotificationTask;
use App\Lib\CommonTask;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Email_format;
use App\Mail\Mails;
use App\User;
use App\AssetAccess;
use App\RemoteAttendanceRequest;
use App\Resignation;
use App\BankPaymentApproval;
use App\Inward_outwards;
use App\Inward_outward_distrubuted_work;
use App\RtgsRegister;
use App\Signed_cheque_list;
use App\Signed_letter_head_request; //new
use App\LetterHeadRegister; //new
use App\Signed_rtgs_request;
use App\CashApproval;
use App\EmployeesLoans;
use App\ChequeRegister;
use App\Interview;
use App\BudgetSheetApproval;
use App\PreSignLetter;
use App\ProSignLetter;
use App\WorkOff_AttendanceRequest;
use App\Hold_budget_sheet;
use App\OnlinePaymentApproval;
use App\OnlinePaymentFile;
use App\Vehicle_Maintenance;
use App\Companies;
use App\Banks;
use App\VoucherNumberRegister;
use App\Compliance_reminders;

use DateTime;
use App\AttendanceMaster;
use App\Attendance_approvals;
use App\AttendanceDetail;
use App\Lib\UserActionLogs;
use App\ProjectUpdateApproveRequest;
use App\ProjectManager;
use App\Projects;
use App\Vendors;
use App\Clients;
use App\Vendors_bank;
use App\Project_sites;
use App\Bank_charge_category;
use App\Bank_charge_sub_category;
use App\PaymentCard;
use App\CompanyDocumentManagement;
use App\TenderCategory;
use App\TenderPattern;
use App\Tender_physical_submission;
use App\Inward_outward_doc_category;
use App\Inward_outward_doc_sub_category;
use App\Inward_outward_delivery_mode;
use App\Sender;
use App\TdsSectionType;
use App\Employees;
use App\Holiday;
use App\CompanyDocumentRequest;
use App\Leaves;
use App\Employee_expense;
use App\Driver_expense;

class ApprovalController extends Controller
{

    private $page_limit = 20;
    public $common_task;
    public $notification_task;
    private $total_hour_per_day = 8;
    private $module_id = 20;
    private $super_admin;
    public $user_action_logs;

    public function __construct()
    {
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();
        $this->user_action_logs = new UserActionLogs();

        $this->super_admin = \App\User::where('role', config('constants.SuperUser'))->first();
    }

    public function get_bankpayment_approval_list(Request $request)   //nishit
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $loggedin_user_data = User::where('id', $request_data['user_id'])->get(['users.id', 'users.name', 'users.role']);

        //check if accountant or superadmin based on that return data
        if ($loggedin_user_data[0]->role == config('constants.ACCOUNT_ROLE')) {
            $bank_payment_approvals = BankPaymentApproval::with(['paymentFiles'])->join('bank', 'bank.id', '=', 'bank_payment_approval.bank_id')
                ->join('company', 'company.id', '=', 'bank_payment_approval.company_id')
                ->join('users', 'users.id', '=', 'bank_payment_approval.user_id')
                ->join('project', 'project.id', '=', 'bank_payment_approval.project_id')
                ->join('vendor', 'vendor.id', '=', 'bank_payment_approval.vendor_id')
                ->leftJoin('clients', 'clients.id', '=', 'bank_payment_approval.client_id')
                ->leftJoin('budget_sheet_approval', 'budget_sheet_approval.id', '=', 'bank_payment_approval.budget_sheet_id')
            ->leftJoin('project_sites', 'project_sites.id', '=', 'bank_payment_approval.project_site_id')
                ->leftjoin('rtgs_register','rtgs_register.id','=','bank_payment_approval.rtgs_number')
                ->leftJoin('cheque_register', 'cheque_register.id', '=', 'bank_payment_approval.cheque_number')
                ->leftJoin('tds_section_type', 'tds_section_type.id', '=', 'bank_payment_approval.section_type_id')
                ->leftJoin('users as acc_users', 'acc_users.id', '=', 'bank_payment_approval.first_approval_id')
                ->leftJoin('users as admin_users', 'admin_users.id', '=', 'bank_payment_approval.second_approval_id')
                ->leftJoin('users as superadmin_users', 'superadmin_users.id', '=', 'bank_payment_approval.third_approval_id')
                ->where('bank_payment_approval.first_approval_status', 'Pending')
                ->orderBy('bank_payment_approval.id', 'DESC')
                ->get(['budget_sheet_approval.budhet_sheet_no','clients.client_name','clients.location', 'project_sites.site_name','rtgs_register.rtgs_no',
                    'bank_payment_approval.*', 'bank.bank_name', 'cheque_register.ch_no as cheque_number',
                    'company.company_name', 'project.project_name', 'vendor.vendor_name',
                    'users.name', 'users.profile_image', 'bank_payment_approval.first_approval_remark as account_approval_note', 'bank_payment_approval.second_approval_remark as admin_approval_note', 'bank_payment_approval.third_approval_remark as superadmin_approval_note',
                    'acc_users.name as acc_user_name', 'admin_users.name as admin_user_name', 'superadmin_users.name as superadmin_user_name', 'tds_section_type.section_type'
                ]);
        } elseif ($loggedin_user_data[0]->role == config('constants.Admin')) {
            $bank_payment_approvals = BankPaymentApproval::with(['paymentFiles'])->join('bank', 'bank.id', '=', 'bank_payment_approval.bank_id')
                ->join('company', 'company.id', '=', 'bank_payment_approval.company_id')
                ->join('users', 'users.id', '=', 'bank_payment_approval.user_id')
                ->join('project', 'project.id', '=', 'bank_payment_approval.project_id')
                ->join('vendor', 'vendor.id', '=', 'bank_payment_approval.vendor_id')
                ->leftJoin('budget_sheet_approval', 'budget_sheet_approval.id', '=', 'bank_payment_approval.budget_sheet_id')
                ->leftJoin('clients', 'clients.id', '=', 'bank_payment_approval.client_id')
            ->leftJoin('project_sites', 'project_sites.id', '=', 'bank_payment_approval.project_site_id')
                ->leftjoin('rtgs_register','rtgs_register.id','=','bank_payment_approval.rtgs_number')
                ->leftJoin('cheque_register', 'cheque_register.id', '=', 'bank_payment_approval.cheque_number')
                ->leftJoin('tds_section_type', 'tds_section_type.id', '=', 'bank_payment_approval.section_type_id')
                ->leftJoin('users as acc_users', 'acc_users.id', '=', 'bank_payment_approval.first_approval_id')
                ->leftJoin('users as admin_users', 'admin_users.id', '=', 'bank_payment_approval.second_approval_id')
                ->leftJoin('users as superadmin_users', 'superadmin_users.id', '=', 'bank_payment_approval.third_approval_id')
                ->where('bank_payment_approval.first_approval_status', 'Approved')
                ->where('bank_payment_approval.second_approval_status', 'Pending')
                ->orderBy('bank_payment_approval.id', 'DESC')
                ->get(['budget_sheet_approval.budhet_sheet_no','clients.client_name','clients.location', 'project_sites.site_name','rtgs_register.rtgs_no',
                    'bank_payment_approval.*', 'bank.bank_name', 'cheque_register.ch_no as cheque_number',
                    'company.company_name', 'project.project_name', 'vendor.vendor_name',
                    'users.name', 'users.profile_image', 'bank_payment_approval.first_approval_remark as account_approval_note', 'bank_payment_approval.second_approval_remark as admin_approval_note', 'bank_payment_approval.third_approval_remark as superadmin_approval_note',
                    'acc_users.name as acc_user_name', 'admin_users.name as admin_user_name',
                'superadmin_users.name as superadmin_user_name', 'tds_section_type.section_type'
                ]);
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $bank_payment_approvals = BankPaymentApproval::with(['paymentFiles'])->join('bank', 'bank.id', '=', 'bank_payment_approval.bank_id')
                ->join('company', 'company.id', '=', 'bank_payment_approval.company_id')
                ->join('users', 'users.id', '=', 'bank_payment_approval.user_id')
                ->join('project', 'project.id', '=', 'bank_payment_approval.project_id')
                ->join('vendor', 'vendor.id', '=', 'bank_payment_approval.vendor_id')
                ->leftJoin('budget_sheet_approval', 'budget_sheet_approval.id', '=', 'bank_payment_approval.budget_sheet_id')
                ->leftJoin('clients', 'clients.id', '=', 'bank_payment_approval.client_id')
            ->leftJoin('project_sites', 'project_sites.id', '=', 'bank_payment_approval.project_site_id')
                ->leftjoin('rtgs_register','rtgs_register.id','=','bank_payment_approval.rtgs_number')
                ->leftJoin('cheque_register', 'cheque_register.id', '=', 'bank_payment_approval.cheque_number')
                ->leftJoin('tds_section_type', 'tds_section_type.id', '=', 'bank_payment_approval.section_type_id')
                ->leftJoin('users as acc_users', 'acc_users.id', '=', 'bank_payment_approval.first_approval_id')
                ->leftJoin('users as admin_users', 'admin_users.id', '=', 'bank_payment_approval.second_approval_id')
                ->leftJoin('users as superadmin_users', 'superadmin_users.id', '=', 'bank_payment_approval.third_approval_id')
                ->where('bank_payment_approval.first_approval_status', 'Approved')
                ->where('bank_payment_approval.second_approval_status', 'Approved')
                ->where('bank_payment_approval.third_approval_status', 'Pending')
                ->orderBy('bank_payment_approval.id', 'DESC')
                ->get(['budget_sheet_approval.budhet_sheet_no','clients.client_name','clients.location', 'project_sites.site_name','rtgs_register.rtgs_no',
                    'bank_payment_approval.*', 'bank.bank_name', 'cheque_register.ch_no as cheque_number',
                    'company.company_name', 'project.project_name', 'vendor.vendor_name', 'users.name',
                    'users.profile_image', 'bank_payment_approval.first_approval_remark as account_approval_note', 'bank_payment_approval.second_approval_remark as admin_approval_note', 'bank_payment_approval.third_approval_remark as superadmin_approval_note',
                    'acc_users.name as acc_user_name', 'admin_users.name as admin_user_name', 'superadmin_users.name as superadmin_user_name', 'tds_section_type.section_type'
                ]);
        } else {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        if ($bank_payment_approvals->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        foreach ($bank_payment_approvals as $key => $bank_payment) {

            if($bank_payment->client_name == "Other Client"){
                $bank_payment_approvals[$key]->client_name = $bank_payment->client_name;
            }else{
                $bank_payment_approvals[$key]->client_name = $bank_payment->client_name. "(" . $bank_payment->location . ")";
            }
            if ($bank_payment->payment_file) {
                $bank_payment_approvals[$key]->payment_file = asset('storage/' . str_replace('public/', '', $bank_payment->payment_file));
            } else {
                $bank_payment_approvals[$key]->payment_file = "";
            }
            if ($bank_payment->invoice_file) {
                $bank_payment_approvals[$key]->invoice_file = asset('storage/' . str_replace('public/', '', $bank_payment->invoice_file));
            } else {
                $bank_payment_approvals[$key]->invoice_file = "";
            }
            if ($bank_payment->profile_image) {
                $bank_payment_approvals[$key]->profile_image = asset('storage/' . str_replace('public/', '', $bank_payment->profile_image));
            } else {
                $bank_payment_approvals[$key]->profile_image = "";
            }

            foreach($bank_payment->paymentFiles as $key=>$mul_payment_file){

                $bank_payment->paymentFiles[$key]->bank_payment_file=asset('storage/' . str_replace('public/', '', $mul_payment_file->bank_payment_file));
            }

        }

        return response()->json(['status' => true, 'msg' => 'Record found', 'data' => $bank_payment_approvals]);
    }

    public function approve_bank_payment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'bankpayment_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $loggedin_user_data = User::where('id', $request_data['user_id'])->get(['users.id', 'users.name', 'users.role']);

        if ($loggedin_user_data[0]->role == config('constants.ACCOUNT_ROLE')) {
            //first approval
            $update_arr = [
                'first_approval_status' => 'Approved',
                'first_approval_id' => $request_data['user_id'],
                'first_approval_datetime' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
            ];
            if ($request_data['approval_note']) {
                $update_arr['first_approval_remark'] = $request_data['approval_note'];
            }
            BankPaymentApproval::where('id', $request_data['bankpayment_id'])->update($update_arr);
            $admin_user = User::where('role', config('constants.Admin'))->get(['id']);
            //send notification about first approval to second approval person
            $this->notification_task->bankPaymentFirstApprovalNotify([$admin_user[0]->id]);
        } elseif ($loggedin_user_data[0]->role == config('constants.Admin')) {
            //second and final approval
            $update_arr = [
                'second_approval_status' => 'Approved',
                'second_approval_id' => $request_data['user_id'],
                'second_approval_datetime' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
            ];
            if ($request_data['approval_note']) {
                $update_arr['second_approval_remark'] = $request_data['approval_note'];
            }
            BankPaymentApproval::where('id', $request_data['bankpayment_id'])->update($update_arr);
            $bank_payment_detail = BankPaymentApproval::where('id', $request_data['bankpayment_id'])->get();
            //send notification to user who requested about approval
            $this->notification_task->bankPaymentSecondApprovalNotify([$this->super_admin->id]);
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            //second and final approval
            $update_arr = [
                'third_approval_status' => 'Approved',
                'third_approval_id' => $request_data['user_id'],
                'third_approval_datetime' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'status' => 'Approved'
            ];
            if ($request_data['approval_note']) {
                $update_arr['third_approval_remark'] = $request_data['approval_note'];
            }
            BankPaymentApproval::where('id', $request_data['bankpayment_id'])->update($update_arr);
            $bank_payment_detail = BankPaymentApproval::where('id', $request_data['bankpayment_id'])->get();

            //send notification to user who requested about approval
            $this->notification_task->bankPaymentThirdApprovalNotify([$bank_payment_detail[0]->user_id]);
        } else {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }

        // User Action Log
        $entry_code = BankPaymentApproval::where('id', $request_data['bankpayment_id'])->value('entry_code');
        $amount = BankPaymentApproval::where('id', $request_data['bankpayment_id'])->value('amount');
        $action_data = [
            'user_id' => $request_data['user_id'],
            'task_body' => "Bank Payment entry code ".$entry_code." approved <br>Amount: ".$amount,
            'created_ip' => $request->ip(),
        ];
        $this->user_action_logs->action($action_data);

        return response()->json(['status' => true, 'msg' => "Bank payment successfully approved.", 'data' => []]);
    }

    public function reject_bank_payment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'bankpayment_id' => 'required',
            'reject_note' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $loggedin_user_data = User::where('id', $request_data['user_id'])->get(['users.id', 'users.name', 'users.role']);
        $bank_payment_detail = BankPaymentApproval::where('id', $request_data['bankpayment_id'])->get();
        if ($loggedin_user_data[0]->role == config('constants.ACCOUNT_ROLE')) {
            //first approval
            $update_arr = [
                'first_approval_status' => 'Rejected',
                'first_approval_id' => $request_data['user_id'],
                'first_approval_datetime' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'status' => 'Rejected',
                'reject_note' => $request_data['reject_note']
            ];
            BankPaymentApproval::where('id', $request_data['bankpayment_id'])->update($update_arr);

            //send notification about rejected
            $this->notification_task->bankPaymentRejectNotify([$bank_payment_detail[0]->user_id]);
        } elseif ($loggedin_user_data[0]->role == config('constants.Admin')) {
            //second and final approval
            $update_arr = [
                'second_approval_status' => 'Rejected',
                'second_approval_id' => $request_data['user_id'],
                'second_approval_datetime' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'status' => 'Rejected',
                'reject_note' => $request_data['reject_note']
            ];
            BankPaymentApproval::where('id', $request_data['bankpayment_id'])->update($update_arr);

            //send notification to user who requested about approval
            $this->notification_task->bankPaymentRejectNotify([$bank_payment_detail[0]->user_id]);
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            //second and final approval
            $update_arr = [
                'third_approval_status' => 'Rejected',
                'third_approval_id' => $request_data['user_id'],
                'third_approval_datetime' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'status' => 'Rejected',
                'reject_note' => $request_data['reject_note']
            ];
            BankPaymentApproval::where('id', $request_data['bankpayment_id'])->update($update_arr);

            //send notification to user who requested about approval
            $this->notification_task->bankPaymentRejectNotify([$bank_payment_detail[0]->user_id]);
        } else {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }

        // User Action Log
        $entry_code = BankPaymentApproval::where('id', $request_data['bankpayment_id'])->value('entry_code');
        $amount = BankPaymentApproval::where('id', $request_data['bankpayment_id'])->value('amount');
        $action_data = [
            'user_id' => $request_data['user_id'],
            'task_body' => "Bank Payment entry code ".$entry_code." rejected <br>Amount: ".$amount." <br>Reject Notes: ".$request_data['reject_note'],
            'created_ip' => $request->ip(),
        ];
        $this->user_action_logs->action($action_data);

        return response()->json(['status' => true, 'msg' => "Bank payment successfully rejected.", 'data' => []]);
    }

    public function get_cash_approval_list(Request $request)   //nishit
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $loggedin_user_data = User::where('id', $request_data['user_id'])->get(['users.id', 'users.name', 'users.role']);

        //check if accountant or superadmin based on that return data
        if ($loggedin_user_data[0]->role == config('constants.Admin')) {
            $cash_approval_list = CashApproval::join('users', 'users.id', '=', 'cash_approval.user_id')
                ->join('company', 'company.id', '=', 'cash_approval.company_id')
                ->join('project', 'project.id', '=', 'cash_approval.project_id')
                ->join('vendor', 'vendor.id', '=', 'cash_approval.vendor_id')
                ->leftJoin('budget_sheet_approval', 'budget_sheet_approval.id', '=', 'cash_approval.budget_sheet_id')
                ->leftJoin('clients', 'clients.id', '=', 'cash_approval.client_id')
                ->leftJoin('project_sites', 'project_sites.id', '=', 'cash_approval.project_site_id')
                ->leftJoin('users as request_user', 'request_user.id', '=', 'cash_approval.requested_by')
                ->leftJoin('users as expence_done', 'expence_done.id', '=', 'cash_approval.expence_done_by')
                ->where('cash_approval.first_approval_status', 'Pending')
                ->orderBy('cash_approval.id', 'DESC')
                ->get(['cash_approval.*','budget_sheet_approval.budhet_sheet_no', 'users.name as username', 'users.profile_image', 'company.company_name', 'project.project_name', 'vendor.vendor_name','clients.client_name','clients.location','project_sites.site_name', 'request_user.name as requested_by_name', 'expence_done.name as expence_done_name']);
        } elseif ($loggedin_user_data[0]->role == config('constants.ACCOUNT_ROLE')) {
            $cash_approval_list = CashApproval::join('users', 'users.id', '=', 'cash_approval.user_id')
                ->join('company', 'company.id', '=', 'cash_approval.company_id')
                ->join('project', 'project.id', '=', 'cash_approval.project_id')
                ->join('vendor', 'vendor.id', '=', 'cash_approval.vendor_id')
                ->leftJoin('budget_sheet_approval', 'budget_sheet_approval.id', '=', 'cash_approval.budget_sheet_id')
                ->leftJoin('clients', 'clients.id', '=', 'cash_approval.client_id')
                ->leftJoin('project_sites', 'project_sites.id', '=', 'cash_approval.project_site_id')
                ->leftJoin('users as request_user', 'request_user.id', '=', 'cash_approval.requested_by')
                ->leftJoin('users as expence_done', 'expence_done.id', '=', 'cash_approval.expence_done_by')
                ->where('cash_approval.first_approval_status', 'Approved')
                ->where('cash_approval.second_approval_status', 'Pending')
                ->orderBy('cash_approval.id', 'DESC')
                ->get(['cash_approval.*','budget_sheet_approval.budhet_sheet_no', 'users.name as username', 'users.profile_image', 'company.company_name', 'project.project_name', 'vendor.vendor_name','clients.client_name','clients.location','project_sites.site_name', 'request_user.name as requested_by_name', 'expence_done.name as expence_done_name']);
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $cash_approval_list = CashApproval::join('users', 'users.id', '=', 'cash_approval.user_id')
                ->join('company', 'company.id', '=', 'cash_approval.company_id')
                ->join('project', 'project.id', '=', 'cash_approval.project_id')
                ->join('vendor', 'vendor.id', '=', 'cash_approval.vendor_id')
                ->leftJoin('budget_sheet_approval', 'budget_sheet_approval.id', '=', 'cash_approval.budget_sheet_id')
                ->leftJoin('clients', 'clients.id', '=', 'cash_approval.client_id')
                ->leftJoin('project_sites', 'project_sites.id', '=', 'cash_approval.project_site_id')
                ->leftJoin('users as request_user', 'request_user.id', '=', 'cash_approval.requested_by')
                ->leftJoin('users as expence_done', 'expence_done.id', '=', 'cash_approval.expence_done_by')
                ->where('cash_approval.first_approval_status', 'Approved')
                ->where('cash_approval.second_approval_status', 'Approved')
                ->where('cash_approval.third_approval_status', 'Pending')
                ->orderBy('cash_approval.id', 'DESC')
                ->get(['cash_approval.*','budget_sheet_approval.budhet_sheet_no', 'users.name as username', 'users.profile_image', 'company.company_name', 'project.project_name', 'vendor.vendor_name','clients.client_name','clients.location','project_sites.site_name', 'request_user.name as requested_by_name', 'expence_done.name as expence_done_name']);
        } else {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        if ($cash_approval_list->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }
        $payment_file_images = [];
        foreach ($cash_approval_list as $key => $cash_payment) {

            if($cash_payment->client_name == "Other Client"){
                $cash_approval_list[$key]->client_name = $cash_payment->client_name;
            }else{
                $cash_approval_list[$key]->client_name = $cash_payment->client_name. "(" . $cash_payment->location . ")";
            }

            if ($cash_payment->payment_file) {
                // $cash_approval_list[$key]->payment_file = asset('storage/' . str_replace('public/', '', $cash_payment->payment_file));
                $images_arr = explode(',', $cash_approval_list[$key]->payment_file);
                foreach ($images_arr as $key1 => $value1) {
                    $payment_file_images[$key1] = asset('storage/' . str_replace('public/', '', $value1));
                }
                $cash_approval_list[$key]->payment_file = $payment_file_images;
            } else {
                $cash_approval_list[$key]->payment_file = "";
            }

            if ($cash_payment->profile_image) {
                $cash_approval_list[$key]->profile_image = asset('storage/' . str_replace('public/', '', $cash_payment->profile_image));
            } else {
                $cash_approval_list[$key]->profile_image = "";
            }
        }

        return response()->json(['status' => true, 'msg' => 'Record found', 'data' => $cash_approval_list]);
    }

    public function approve_cash(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'cash_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $loggedin_user_data = User::where('id', $request_data['user_id'])->get(['users.id', 'users.name', 'users.role']);

        if ($loggedin_user_data[0]->role == config('constants.Admin')) {
            //first approval
            $update_arr = [
                'first_approval_status' => 'Approved',
                'first_approval_id' => $request_data['user_id'],
                'first_approval_datetime' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
            ];
            CashApproval::where('id', $request_data['cash_id'])->update($update_arr);
            $admin_user = User::where('role', config('constants.ACCOUNT_ROLE'))->get(['id']);
            //send notification about first approval to second approval person
            $this->notification_task->cashRequestFirstApprovalNotify([$admin_user[0]->id]);
        } elseif ($loggedin_user_data[0]->role == config('constants.ACCOUNT_ROLE')) {
            //second and final approval
            $update_arr = [
                'second_approval_status' => 'Approved',
                'second_approval_id' => $request_data['user_id'],
                'second_approval_datetime' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
            ];
            CashApproval::where('id', $request_data['cash_id'])->update($update_arr);
            $bank_payment_detail = CashApproval::where('id', $request_data['cash_id'])->get();
            //send notification to user who requested about approval
            $this->notification_task->cashRequestSecondApprovalNotify([$this->super_admin->id]);
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            //second and final approval
            $update_arr = [
                'third_approval_status' => 'Approved',
                'third_approval_id' => $request_data['user_id'],
                'third_approval_datetime' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'status' => 'Approved'
            ];
            CashApproval::where('id', $request_data['cash_id'])->update($update_arr);
            $bank_payment_detail = CashApproval::where('id', $request_data['cash_id'])->get();
            //send notification to user who requested about approval
            $this->notification_task->cashRequestThirdApprovalNotify([$bank_payment_detail[0]->user_id]);
        } else {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }

        // User Action Log
        $entry_code = CashApproval::where('id', $request_data['cash_id'])->value('entry_code');
        $amount = CashApproval::where('id', $request_data['cash_id'])->value('amount');
        $action_data = [
            'user_id' => $request_data['user_id'],
            'task_body' => "Cash Payment entry code ".$entry_code." approved <br>Amount: ".$amount,
            'created_ip' => $request->ip(),
        ];
        $this->user_action_logs->action($action_data);

        return response()->json(['status' => true, 'msg' => "Cash approval successfully approved.", 'data' => []]);
    }

    public function reject_cash_approval(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'cash_id' => 'required',
            'reject_note' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $loggedin_user_data = User::where('id', $request_data['user_id'])->get(['users.id', 'users.name', 'users.role']);
        $bank_payment_detail = CashApproval::where('id', $request_data['cash_id'])->get();
        if ($loggedin_user_data[0]->role == config('constants.Admin')) {
            //first approval
            $update_arr = [
                'first_approval_status' => 'Rejected',
                'first_approval_id' => $request_data['user_id'],
                'first_approval_datetime' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'status' => 'Rejected',
                'reject_note' => $request_data['reject_note']
            ];
            CashApproval::where('id', $request_data['cash_id'])->update($update_arr);

            //send notification about rejected
            $this->notification_task->cashRequestRejectNotify([$bank_payment_detail[0]->user_id]);
        } elseif ($loggedin_user_data[0]->role == config('constants.ACCOUNT_ROLE')) {
            //second and final approval
            $update_arr = [
                'second_approval_status' => 'Rejected',
                'second_approval_id' => $request_data['user_id'],
                'second_approval_datetime' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'status' => 'Rejected',
                'reject_note' => $request_data['reject_note']
            ];
            CashApproval::where('id', $request_data['cash_id'])->update($update_arr);

            //send notification to user who requested about approval
            $this->notification_task->bankPaymentRejectNotify([$bank_payment_detail[0]->user_id]);
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            //second and final approval
            $update_arr = [
                'third_approval_status' => 'Rejected',
                'third_approval_id' => $request_data['user_id'],
                'third_approval_datetime' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'status' => 'Rejected',
                'reject_note' => $request_data['reject_note']
            ];
            CashApproval::where('id', $request_data['cash_id'])->update($update_arr);

            //send notification to user who requested about approval
            $this->notification_task->bankPaymentRejectNotify([$bank_payment_detail[0]->user_id]);
        } else {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }

        // User Action Log
        $entry_code = CashApproval::where('id', $request_data['cash_id'])->value('entry_code');
        $amount = CashApproval::where('id', $request_data['cash_id'])->value('amount');
        $action_data = [
            'user_id' => $request_data['user_id'],
            'task_body' => "Cash Payment entry code ".$entry_code." rejected <br>Amount: ".$amount."<br>Reject Note: ".$request_data['reject_note'],
            'created_ip' => $request->ip(),
        ];
        $this->user_action_logs->action($action_data);
        return response()->json(['status' => true, 'msg' => "Cash approval successfully rejected.", 'data' => []]);
    }

    public function get_budgetsheet_approval_list(Request $request)  //change
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $loggedin_user_data = User::where('id', $request_data['user_id'])->get(['users.id', 'users.name', 'users.role']);


        if ($loggedin_user_data[0]->role == config('constants.Admin')) {
            $budget_sheet_approvals = BudgetSheetApproval::join('users', 'users.id', '=', 'budget_sheet_approval.user_id')
                ->join('company', 'company.id', '=', 'budget_sheet_approval.company_id')
                ->join('project', 'project.id', '=', 'budget_sheet_approval.project_id')
                ->join('vendor', 'vendor.id', '=', 'budget_sheet_approval.vendor_id')
                ->join('department', 'department.id', '=', 'budget_sheet_approval.department_id')

                ->leftJoin('clients', 'clients.id', '=', 'budget_sheet_approval.client_id')
                ->leftJoin('project_sites', 'project_sites.id', '=', 'budget_sheet_approval.project_site_id')
                ->with('budgetsheet_file')
                ->where('first_approval_status', 'Pending')
                ->get(['clients.client_name','clients.location', 'project_sites.site_name',
                    'budget_sheet_approval.*', 'users.name', 'users.profile_image', 'company.company_name', 'project.project_name', 'vendor.vendor_name', 'department.dept_name'
                ]);
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $budget_sheet_approvals = BudgetSheetApproval::join('users', 'users.id', '=', 'budget_sheet_approval.user_id')
                ->join('company', 'company.id', '=', 'budget_sheet_approval.company_id')
                ->join('project', 'project.id', '=', 'budget_sheet_approval.project_id')
                ->join('vendor', 'vendor.id', '=', 'budget_sheet_approval.vendor_id')
                ->join('department', 'department.id', '=', 'budget_sheet_approval.department_id')

                ->leftJoin('clients', 'clients.id', '=', 'budget_sheet_approval.client_id')
                ->leftJoin('project_sites', 'project_sites.id', '=', 'budget_sheet_approval.project_site_id')
                ->where('first_approval_status', 'Approved')
                ->where('second_approval_status', 'Pending')
                ->with('budgetsheet_file')
                ->get(['clients.client_name','clients.location', 'project_sites.site_name',
                    'budget_sheet_approval.*', 'users.name', 'users.profile_image', 'company.company_name', 'project.project_name', 'vendor.vendor_name', 'department.dept_name'
                ]);
        } else {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }
        if ($budget_sheet_approvals->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        foreach ($budget_sheet_approvals as $key => $sheet) {
            /* if (1$sheet->budget_sheet_file) {
              $budget_sheet_approvals[$key]['budget_sheet_file'] = asset('storage/' . str_replace('public/', '', $sheet->budget_sheet_file));
              } else {
              $budget_sheet_approvals[$key]['budget_sheet_file'] = "";
              } */


              if($sheet->client_name){


            if($sheet->client_name == "Other Client"){
                $budget_sheet_approvals[$key]->client_name = $sheet->client_name;
            }else{
                $budget_sheet_approvals[$key]->client_name = $sheet->client_name. "(" . $sheet->location . ")";
            }

        }




            if ($sheet->profile_image) {
                $budget_sheet_approvals[$key]['profile_image'] = asset('storage/' . str_replace('public/', '', $sheet->profile_image));
            } else {
                $budget_sheet_approvals[$key]['profile_image'] = "";
            }

            if ($sheet->invoice_file) {
                $budget_sheet_approvals[$key]['invoice_file'] = asset('storage/' . str_replace('public/', '', $sheet->invoice_file));
            } else {
                $budget_sheet_approvals[$key]['invoice_file'] = "";
            }

            foreach ($sheet->budgetsheet_file as $key1 => $files) {
                if ($files->budget_sheet_file) {
                    $sheet->budgetsheet_file[$key1]['budget_sheet_file'] = asset('storage/' . str_replace('public/', '', $files->budget_sheet_file));
                } else {
                    $sheet->budgetsheet_file[$key1]['budget_sheet_file'] = "";
                }
            }
        }

        return response()->json(['status' => true, 'msg' => 'Record found', 'data' => $budget_sheet_approvals]);
    }

    public function approve_budget_sheet(Request $request)
    {
        $validator_normal = Validator::make($request->all(), [
            'user_id' => 'required',
            'id' => 'required',
            'approval_remark' => 'required',
            'approved_amount' => 'required',
            'hold_amount' => 'required',
        ]);

        if ($validator_normal->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $loggedin_user_data = User::where('id', $request_data['user_id'])->get(['users.id', 'users.name', 'users.role']);
        $budget_sheet_detail = BudgetSheetApproval::join('users', 'users.id', '=', 'budget_sheet_approval.user_id')->where('budget_sheet_approval.id', $request_data['id'])->get(['budget_sheet_approval.*', 'users.name', 'users.email']);

        $update_arr = [
            'approved_amount' => $request_data['approved_amount'],
            'hold_amount' => $request_data['hold_amount'],
            //'previous_hold_id' => $request_data['previous_hold_id'],
            //'previous_hold_amount' => $request_data['previous_hold_amount'],
            'approval_remark' => $request_data['approval_remark'],
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => $request_data['user_id'],
            'final_approved_amount' => $request_data['approved_amount'],
            'remain_hold_amount' => $request_data['hold_amount'],
            'approval_remark' => $request_data['approval_remark']
        ];

        if ($loggedin_user_data[0]->role == config('constants.Admin')) {
            $update_arr['first_approval_status'] = "Approved";
            $update_arr['first_approval_id'] = $request_data['user_id'];
            $update_arr['first_approval_datetime'] = date('Y-m-d H:i:s');

            $mail_data = [
                'to_user_name' => $this->super_admin->name,
                'budget_sheet_number' => $budget_sheet_detail[0]->meeting_number,
                'to_email' => $this->super_admin->email,
                'request_user_name' => $budget_sheet_detail[0]->name
            ];
            $this->common_task->budgetSheetRequestEmail($mail_data);
            $this->notification_task->budgetSheetRequestNotify([$this->super_admin->id], $budget_sheet_detail[0]->name, $budget_sheet_detail[0]->meeting_number);
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $update_arr['second_approval_status'] = "Approved";
            $update_arr['status'] = "Approved";
            $update_arr['second_approval_id'] = $loggedin_user_data[0]->id;
            $update_arr['second_approval_datetime'] = date('Y-m-d H:i:s');

            $this->notification_task->approveBudgetSheetNotify([$budget_sheet_detail[0]->user_id], $budget_sheet_detail[0]->meeting_number);

            $mail_data = [
                'username' => $budget_sheet_detail[0]->name,
                'budget_meeting_number' => $budget_sheet_detail[0]->meeting_number,
                'status' => $budget_sheet_detail[0]->status,
                'email' => $budget_sheet_detail[0]->email
            ];
            $this->common_task->approveRejectBudgetEmail($mail_data);
        } else {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }

        if ($request_data['hold_amount'] > 0) {
            $update_arr['is_hold'] = "Yes";
        }

        BudgetSheetApproval::where('id', $request_data['id'])->update($update_arr);

        // User Action Log
        $budhet_sheet_no = BudgetSheetApproval::where('id', $request_data['id'])->value('budhet_sheet_no');
        $action_data = [
            'user_id' => $request_data['user_id'],
            'task_body' => $budhet_sheet_no . " budget sheet number approved",
            'created_ip' => $request->ip(),
        ];
        $this->user_action_logs->action($action_data);
        return response()->json(['status' => true, 'msg' => "Budget sheet is successfully approved.", 'data' => []]);
    }

    /* public function approve_budget_sheet(Request $request) {
      $validator = Validator::make($request->all(), [
      'user_id' => 'required',
      'budget_sheet_id' => 'required'
      ]);

      if ($validator->fails()) {
      return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
      }

      $request_data = $request->all();
      $response_data = [];

      $loggedin_user_data = User::where('id', $request_data['user_id'])->get(['users.id', 'users.name', 'users.role']);

      if ($loggedin_user_data[0]->role == config('constants.ACCOUNT_ROLE')) {
      //first approval
      $update_arr = [
      'first_approval_status' => 'Approved',
      'first_approval_id' => $request_data['user_id'],
      'updated_at' => date('Y-m-d H:i:s'),
      'updated_ip' => $request->ip(),
      ];
      BudgetSheetApproval::where('id', $request_data['budget_sheet_id'])->update($update_arr);

      //send notification about first approval to second approval person
      $this->notification_task->budgetSheetFirstApprovalNotify([$this->super_admin->id]);
      } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
      //second and final approval
      $update_arr = [
      'first_approval_status' => 'Approved',
      'first_approval_id' => $request_data['user_id'],
      'second_approval_status' => 'Approved',
      'second_approval_id' => $request_data['user_id'],
      'updated_at' => date('Y-m-d H:i:s'),
      'updated_ip' => $request->ip(),
      'status' => 'Approved',
      'approval_comment' => $request_data['approval_comment']
      ];
      BudgetSheetApproval::where('id', $request_data['budget_sheet_id'])->update($update_arr);
      $budget_sheet_detail = BudgetSheetApproval::where('id', $request_data['budget_sheet_id'])->get();
      //send notification to user who requested about approval
      $this->notification_task->budgetSheetSecondApprovalNotify([$budget_sheet_detail[0]->user_id]);
      } else {
      return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
      }
      return response()->json(['status' => true, 'msg' => "Budget sheet is successfully approved.", 'data' => []]);
      } */

    public function reject_budget_sheet(Request $request)
    {
        $validator_normal = Validator::make($request->all(), [
            'reject_note' => 'required',
            'id' => 'required',
            'user_id' => 'required',
        ]);

        if ($validator_normal->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $loggedin_user_data = User::where('id', $request_data['user_id'])->get(['users.id', 'users.name', 'users.role']);
        $update_arr = [
            'reject_note' => $request_data['reject_note'],
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => $request_data['user_id'],
        ];

        if ($loggedin_user_data[0]->role == config('constants.Admin')) {
            $update_arr['first_approval_status'] = "Rejected";
            $update_arr['first_approval_id'] = $request_data['user_id'];
            $update_arr['status'] = "Rejected";
            $update_arr['first_approval_datetime'] = date('Y-m-d H:i:s');

        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $update_arr['second_approval_status'] = "Rejected";
            $update_arr['second_approval_id'] = $request_data['user_id'];
            $update_arr['status'] = "Rejected";
            $update_arr['second_approval_datetime'] = date('Y-m-d H:i:s');
        } else {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
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
        $this->common_task->approveRejectBudgetEmail($mail_data);

        // User Action Log
        $budhet_sheet_no = BudgetSheetApproval::where('id', $request_data['id'])->value('budhet_sheet_no');
        $action_data = [
            'user_id' => $request_data['user_id'],
            'task_body' => $budhet_sheet_no . " budget sheet number rejected",
            'created_ip' => $request->ip(),
        ];
        $this->user_action_logs->action($action_data);

        return response()->json(['status' => true, 'msg' => "Budget sheet is rejected.", 'data' => []]);
    }

    /* public function reject_budget_sheet(Request $request) {
      $validator = Validator::make($request->all(), [
      'user_id' => 'required',
      'budget_sheet_id' => 'required',
      'reject_note' => 'required'
      ]);

      if ($validator->fails()) {
      return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
      }

      $request_data = $request->all();
      $response_data = [];

      $loggedin_user_data = User::where('id', $request_data['user_id'])->get(['users.id', 'users.name', 'users.role']);
      $budget_sheet_detail = BudgetSheetApproval::where('id', $request_data['budget_sheet_id'])->get();
      if ($loggedin_user_data[0]->role == config('constants.ACCOUNT_ROLE')) {
      //first approval
      $update_arr = [
      'first_approval_status' => 'Rejected',
      'first_approval_id' => $request_data['user_id'],
      'updated_at' => date('Y-m-d H:i:s'),
      'updated_ip' => $request->ip(),
      'status' => 'Rejected',
      'reject_note' => $request_data['reject_note']
      ];
      BudgetSheetApproval::where('id', $request_data['budget_sheet_id'])->update($update_arr);

      //send notification about rejected
      $this->notification_task->budgetSheetRejectNotify([$budget_sheet_detail[0]->user_id]);
      } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
      //second and final approval
      $update_arr = [
      'second_approval_status' => 'Rejected',
      'second_approval_id' => $request_data['user_id'],
      'updated_at' => date('Y-m-d H:i:s'),
      'updated_ip' => $request->ip(),
      'status' => 'Rejected',
      'reject_note' => $request_data['reject_note']
      ];
      BudgetSheetApproval::where('id', $request_data['budget_sheet_id'])->update($update_arr);

      //send notification to user who requested about approval
      $this->notification_task->budgetSheetRejectNotify([$budget_sheet_detail[0]->user_id]);
      } else {
      return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
      }
      return response()->json(['status' => true, 'msg' => "Budget sheet is rejected.", 'data' => []]);
      } */

    public function pre_sign_letterhead_approval_list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $loggedin_user_data = User::where('id', $request_data['user_id'])->get(['users.id', 'users.name', 'users.role']);

        //check if hr or superadmin based on that return data
        if ($loggedin_user_data[0]->role == config('constants.ASSISTANT')) {
            $pre_sign_approvals = PreSignLetter::where('first_approval_status', 'Pending')
                ->join('users', 'users.id', '=', 'pre_sign_letter.user_id')
                ->join('company', 'company.id', '=', 'pre_sign_letter.company_id')
                ->join('project', 'project.id', '=', 'pre_sign_letter.project_id')
                ->leftJoin('clients', 'clients.id', '=', 'pre_sign_letter.client_id')
                ->get(['pre_sign_letter.*', 'company.company_name', 'project.project_name', 'users.name as user_name', 'users.profile_image', 'clients.client_name']);
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $pre_sign_approvals = PreSignLetter::where('first_approval_status', 'Approved')
                ->join('users', 'users.id', '=', 'pre_sign_letter.user_id')
                ->join('company', 'company.id', '=', 'pre_sign_letter.company_id')
                ->join('project', 'project.id', '=', 'pre_sign_letter.project_id')
                ->leftJoin('clients', 'clients.id', '=', 'pre_sign_letter.client_id')
                ->where('second_approval_status', 'Pending')
                ->get(['pre_sign_letter.*', 'company.company_name', 'project.project_name', 'users.name as user_name','users.profile_image', 'clients.client_name']);
        } else {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        if ($pre_sign_approvals->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        foreach ($pre_sign_approvals as $key => $letter) {
            if ($letter->profile_image) {
                $pre_sign_approvals[$key]->profile_image = asset('storage/' . str_replace('public/', '', $letter->profile_image));
            } else {
                $pre_sign_approvals[$key]->profile_image = "";
            }
            if ($letter->letter_head_content_file) {
                $pre_sign_approvals[$key]->letter_head_content_file = asset('storage/' . str_replace('public/', '', $letter->letter_head_content_file));
            } else {
                $pre_sign_approvals[$key]->letter_head_content_file = "";
            }
        }
        return response()->json(['status' => true, 'msg' => 'Record found', 'data' => $pre_sign_approvals]);
    }

    public function approve_pre_sign_letter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'presign_letter_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $loggedin_user_data = User::where('id', $request_data['user_id'])->get(['users.id', 'users.name', 'users.role']);

        if ($loggedin_user_data[0]->role == config('constants.LETTERHEAD_APPROVE')) {
            //first approval
            $update_arr = [
                'first_approval_status' => 'Approved',
                'first_approval_id' => $request_data['user_id'],
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'first_approval_datetime' => date('Y-m-d H:i:s'),
            ];
            PreSignLetter::where('id', $request_data['presign_letter_id'])->update($update_arr);

            //send notification about first approval to second approval person
            $this->notification_task->preSignRequestFirstApprovalNotify([$this->super_admin->id]);
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            //second and final approval
            $update_arr = [
                'second_approval_status' => 'Approved',
                'second_approval_id' => $request_data['user_id'],
                'updated_at' => date('Y-m-d H:i:s'),
                'second_approval_datetime' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'status' => 'Approved',
                'is_deliver_user_id' => $request_data['is_deliver_user_id'],
                'assign_letter_user_id' => $request_data['is_deliver_user_id'],
            ];
            PreSignLetter::where('id', $request_data['presign_letter_id'])->update($update_arr);
            $pre_sign_detail = PreSignLetter::where('id', $request_data['presign_letter_id'])->get();
            //send notification to user who requested about approval
            $this->notification_task->preSignRequestSecondApprovalNotify([$pre_sign_detail[0]->user_id]);
        } else {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }
        return response()->json(['status' => true, 'msg' => "Pre-sign Letterhead is successfully approved.", 'data' => []]);
    }

    public function reject_pre_sign_letter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'presign_letter_id' => 'required',
            'reject_note' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $loggedin_user_data = User::where('id', $request_data['user_id'])->get(['users.id', 'users.name', 'users.role']);
        $pre_signletter_detail = PreSignLetter::where('id', $request_data['presign_letter_id'])->get();
        if ($loggedin_user_data[0]->role == config('constants.LETTERHEAD_APPROVE')) {
            //first approval
            $update_arr = [
                'first_approval_status' => 'Rejected',
                'first_approval_id' => $request_data['user_id'],
                'updated_at' => date('Y-m-d H:i:s'),
                'first_approval_datetime' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'status' => 'Rejected',
                'reject_note' => $request_data['reject_note']
            ];
            PreSignLetter::where('id', $request_data['presign_letter_id'])->update($update_arr);

            //send notification about rejected
            $this->notification_task->preSignRequestRejectNotify([$pre_signletter_detail[0]->user_id]);
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            //second and final approval
            $update_arr = [
                'second_approval_status' => 'Rejected',
                'second_approval_id' => $request_data['user_id'],
                'updated_at' => date('Y-m-d H:i:s'),
                'second_approval_datetime' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'status' => 'Rejected',
                'reject_note' => $request_data['reject_note']
            ];
            PreSignLetter::where('id', $request_data['presign_letter_id'])->update($update_arr);

            //send notification to user who requested about approval
            $this->notification_task->preSignRequestRejectNotify([$pre_signletter_detail[0]->user_id]);
        } else {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }
        return response()->json(['status' => true, 'msg' => "Pre-signed letter-head is rejected.", 'data' => []]);
    }

    public function letterhead_approval_list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $loggedin_user_data = User::where('id', $request_data['user_id'])->get(['users.id', 'users.name', 'users.role']);

        //check if hr or superadmin based on that return data
        if ($loggedin_user_data[0]->role == config('constants.LETTERHEAD_APPROVE')) {
            $pre_sign_approvals = ProSignLetter::where('first_approval_status', 'Pending')
                ->join('users', 'users.id', '=', 'pro_sign_letter.user_id')
                ->join('company', 'company.id', '=', 'pro_sign_letter.company_id')
                ->join('project', 'project.id', '=', 'pro_sign_letter.project_id')
                ->leftJoin('clients', 'clients.id', '=', 'pro_sign_letter.client_id')
                ->get(['pro_sign_letter.*', 'company.company_name', 'project.project_name', 'users.name as user_name', 'users.profile_image', 'clients.client_name']);
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
            /* $pre_sign_approvals = PreSignLetter::where('first_approval_status', 'Approved')
              ->where('second_approval_status', 'Pending')
              ->get(); */
        } else {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        if ($pre_sign_approvals->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        foreach ($pre_sign_approvals as $key => $letter) {
            if ($letter->profile_image) {
                $pre_sign_approvals[$key]->profile_image = asset('storage/' . str_replace('public/', '', $letter->profile_image));
            } else {
                $pre_sign_approvals[$key]->profile_image = "";
            }
            if ($letter->letter_head_content_file) {
                $pre_sign_approvals[$key]->letter_head_content_file = asset('storage/' . str_replace('public/', '', $letter->letter_head_content_file));
            } else {
                $pre_sign_approvals[$key]->letter_head_content_file = "";
            }
        }

        return response()->json(['status' => true, 'msg' => 'Record found', 'data' => $pre_sign_approvals]);
    }

    public function approve_letter_head(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'letter_head_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $loggedin_user_data = User::where('id', $request_data['user_id'])->get(['users.id', 'users.name', 'users.role']);

        if ($loggedin_user_data[0]->role == config('constants.LETTERHEAD_APPROVE')) {
            //first approval
            $update_arr = [
                'first_approval_status' => 'Approved',
                'first_approval_id' => $request_data['user_id'],
                'updated_at' => date('Y-m-d H:i:s'),
                'first_approval_datetime' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'status' => 'Approved',
                'is_deliver_user_id' => $request_data['is_deliver_user_id'],
                'assign_letter_user_id' => $request_data['is_deliver_user_id']
            ];
            ProSignLetter::where('id', $request_data['letter_head_id'])->update($update_arr);
            $pro_sign_detail = ProSignLetter::where('id', $request_data['letter_head_id'])->get();
            //send notification about first approval
            $this->notification_task->proSignRequestFirstApprovalNotify([$pro_sign_detail[0]->user_id]);
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {

            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);

            /* //second and final approval
              $update_arr = [
              'second_approval_status' => 'Approved',
              'second_approval_id' => $request_data['user_id'],
              'second_approval_datetime' => date('Y-m-d H:i:s'),
              'updated_at' => date('Y-m-d H:i:s'),
              'updated_ip' => $request->ip(),
              'status' => 'Approved'
              ];
              ProSignLetter::where('id', $request_data['letter_head_id'])->update($update_arr);
              $pre_sign_detail = PreSignLetter::where('id', $request_data['letter_head_id'])->get();
              //send notification to user who requested about approval
              $this->notification_task->preSignRequestSecondApprovalNotify([$pre_sign_detail[0]->user_id]); */
        } else {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }
        return response()->json(['status' => true, 'msg' => "Letterhead request is successfully approved.", 'data' => []]);
    }

    public function reject_letter_head(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'letter_head_id' => 'required',
            'reject_note' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $loggedin_user_data = User::where('id', $request_data['user_id'])->get(['users.id', 'users.name', 'users.role']);
        $pre_signletter_detail = ProSignLetter::where('id', $request_data['letter_head_id'])->get();
        if ($loggedin_user_data[0]->role == config('constants.LETTERHEAD_APPROVE')) {
            //first approval
            $update_arr = [
                'first_approval_status' => 'Rejected',
                'first_approval_id' => $request_data['user_id'],
                'updated_at' => date('Y-m-d H:i:s'),
                'first_approval_datetime' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'status' => 'Rejected',
                'reject_note' => $request_data['reject_note']
            ];
            ProSignLetter::where('id', $request_data['letter_head_id'])->update($update_arr);

            //send notification about rejected
            $this->notification_task->proSignRequestRejectNotify([$pre_signletter_detail[0]->user_id]);
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {

            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);

            /* //second and final approval
              $update_arr = [
              'second_approval_status' => 'Rejected',
              'second_approval_id' => $request_data['user_id'],
              'second_approval_datetime' => date('Y-m-d H:i:s'),
              'updated_at' => date('Y-m-d H:i:s'),
              'updated_ip' => $request->ip(),
              'status' => 'Rejected',
              'reject_note'=>$request_data['reject_note']
              ];
              PreSignLetter::where('id', $request_data['presign_letter_id'])->update($update_arr);

              //send notification to user who requested about approval
              $this->notification_task->preSignRequestRejectNotify([$pre_signletter_detail[0]->user_id]); */
        } else {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }
        return response()->json(['status' => true, 'msg' => "Letter-head request is rejected.", 'data' => []]);
    }

    public function get_approved_letter_head_request(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        //check for permission
        $loggedin_user_data = User::where('id', $request_data['user_id'])->get(['users.id', 'users.name', 'users.role']);

        $permission_arr = $this->common_task->getPermissionArr($loggedin_user_data[0]->role, 29);

        if (!in_array(5, $permission_arr) || !in_array(2, $permission_arr)) {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }

        $pre_signed_letter_request = PreSignLetter::where('status', 'Approved')->where('is_deliver_status', 'In-Process')->where('assign_letter_user_id', $request_data['user_id'])->get();
        $letter_head_request = ProSignLetter::where('status', 'Approved')->where('is_deliver_status', 'In-Process')->where('assign_letter_user_id', $request_data['user_id'])->get();

        foreach ($pre_signed_letter_request as $key => $presigned_letter) {
            $pre_signed_letter_request[$key]->letter_head_image = asset('storage/' . str_replace('public/', '', $presigned_letter->letter_head_image));
        }

        foreach ($letter_head_request as $key => $letter) {
            $letter_head_request[$key]->letter_head_image = asset('storage/' . str_replace('public/', '', $letter->letter_head_image));
        }

        $response_data['pre_signed_letter_request'] = $pre_signed_letter_request;
        $response_data['letter_head_request'] = $letter_head_request;

        return response()->json(['status' => true, 'msg' => 'Record found', 'data' => $response_data]);
    }

    public function issuing_presigned_letter_head(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'presign_letter_head_id' => 'required',
            'letter_head_number' => 'required',
            'letter_head_image' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $letter_head_image = $request->file('letter_head_image');
        //$file_path = $letter_head_image->store('public/letter_head');
        $original_file_name = explode('.', $letter_head_image->getClientOriginalName());

            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);

            $file_path = $letter_head_image->storeAs('public/letter_head', $new_file_name);

        $update_arr = [
            'letter_head_number' => $request_data['letter_head_number'],
            'letter_head_image' => $file_path,
            'is_deliver_status' => 'Delivered',
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => $request_data['user_id'],
            'is_deliver_user_id' => $request_data['user_id']
        ];
        PreSignLetter::where('id', $request_data['presign_letter_head_id'])->update($update_arr);

        $letter_detail = PreSignLetter::where('id', $request_data['presign_letter_head_id'])->get();

        $request_user_detail = User::where('id', $letter_detail[0]->user_id)->get(['name', 'email']);
        $deliver_user_name = User::where('id', $letter_detail[0]->is_deliver_user_id)->get(['name', 'email']);
        $first_approval = User::where('id', $letter_detail[0]->first_approval_id)->get(['name', 'email']);

        $mail_data['request_user_name'] = $request_user_detail[0]->name;
        $mail_data['deliver_user_name'] = $deliver_user_name[0]->name;
        $mail_data['letter_head_number'] = $letter_detail[0]->letter_head_number;
        $mail_data['email_list'] = [$request_user_detail[0]->email, $deliver_user_name[0]->email, $first_approval[0]->email];
        $this->common_task->preSignedLetterHeadDeliveryEmail($mail_data);

        $user_ids = [$letter_detail[0]->user_id, $letter_detail[0]->is_deliver_user_id, $letter_detail[0]->first_approval_id, $letter_detail[0]->second_approval_id];
        $letter_head_number = $letter_detail[0]->letter_head_number;
        $received_user = $request_user_detail[0]->name;
        $this->notification_task->preSignLetterheadDeliveryNotify($user_ids, $letter_head_number, $received_user);

        return response()->json(['status' => true, 'msg' => "Pre-signed letter-head details successfully submitted.", 'data' => []]);
    }

    public function issuing_letter_head(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'letter_head_id' => 'required',
            'letter_head_number' => 'required',
            'letter_head_image' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $letter_head_image = $request->file('letter_head_image');
        //$file_path = $letter_head_image->store('public/letter_head');
        $original_file_name = explode('.', $letter_head_image->getClientOriginalName());

        $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);

        $file_path = $letter_head_image->storeAs('public/letter_head', $new_file_name);


        $update_arr = [
            'letter_head_number' => $request_data['letter_head_number'],
            'letter_head_image' => $file_path,
            'is_deliver_status' => 'Delivered',
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => $request_data['user_id'],
            'is_deliver_user_id' => $request_data['user_id']
        ];
        ProSignLetter::where('id', $request_data['letter_head_id'])->update($update_arr);

        $letter_detail = ProSignLetter::where('id', $request_data['letter_head_id'])->get();

        $request_user_detail = User::where('id', $letter_detail[0]->user_id)->get(['name', 'email']);
        $deliver_user_name = User::where('id', $letter_detail[0]->is_deliver_user_id)->get(['name', 'email']);
        $first_approval = User::where('id', $letter_detail[0]->first_approval_id)->get(['name', 'email']);

        $mail_data['request_user_name'] = $request_user_detail[0]->name;
        $mail_data['deliver_user_name'] = $deliver_user_name[0]->name;
        $mail_data['letter_head_number'] = $letter_detail[0]->letter_head_number;
        $mail_data['email_list'] = [$request_user_detail[0]->email, $deliver_user_name[0]->email, $first_approval[0]->email];
        $this->common_task->LetterHeadDeliveryEmail($mail_data);

        $user_ids = [$letter_detail[0]->user_id, $letter_detail[0]->is_deliver_user_id, $letter_detail[0]->first_approval_id, $this->super_admin->id];
        $letter_head_number = $letter_detail[0]->letter_head_number;
        $received_user = $request_user_detail[0]->name;
        $this->notification_task->LetterheadDeliveryNotify($user_ids, $letter_head_number, $received_user);

        return response()->json(['status' => true, 'msg' => "Letter-head details successfully submitted.", 'data' => []]);
    }

    public function approval_count(Request $request)      //this  11 .. 16..17
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $loggedin_user_data = \App\User::where('id', $request_data['user_id'])->get();

        //comliance reminders
        $partial_query = Compliance_reminders::join('compliance_reminders_done_status','compliance_reminders.id','=','compliance_reminders_done_status.compliance_reminders_id')
                ->join('company','company.id','=','compliance_reminders.company_id')
                ->join('compliance_category','compliance_category.id','=','compliance_reminders.compliance_category_id')
                ->join('users as A','A.id','=','compliance_reminders.responsible_person_id')
                ->join('users as B','B.id','=','compliance_reminders.payment_responsible_person_id')
                ->join('users as C','C.id','=','compliance_reminders.super_admin_checker_id')
                ->leftjoin('users as D','D.id','=','compliance_reminders.checker_id')
                ->where('compliance_reminders_done_status.final_status','Pending');

                $reminder_list =  $partial_query->where(function ($query) use ($request_data)   {
                        $query->where('compliance_reminders_done_status.responsible_person_id', $request_data['user_id'])

                        ->orWhere(function ($query) use ($request_data)  {
                            $query->Where('compliance_reminders_done_status.payment_responsible_person_id', $request_data['user_id'] );

                        })->orWhere(function ($query) use ($request_data)  {
                            $query->Where('compliance_reminders_done_status.checker_id', $request_data['user_id'] );

                        })->orWhere(function ($query) use ($request_data)  {
                            $query->Where('compliance_reminders_done_status.super_admin_checker_id', $request_data['user_id'] );

                        });
                });

        $compliance_reminder_count = $reminder_list->get()->count();
        $response_data['compliance_reminder_count'] = $compliance_reminder_count;

        if ($loggedin_user_data[0]->role == config('constants.ACCOUNT_ROLE')) {
            $bank_payment_approvals_count = BankPaymentApproval::where('first_approval_status', 'Pending')->get()->count();
        } elseif ($loggedin_user_data[0]->role == config('constants.Admin')) {
            $bank_payment_approvals_count = BankPaymentApproval::where('first_approval_status', 'Approved')
                ->where('second_approval_status', 'Pending')
                ->get()->count();
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $bank_payment_approvals_count = BankPaymentApproval::where('first_approval_status', 'Approved')
                ->where('second_approval_status', 'Approved')
                ->where('third_approval_status', 'Pending')
                ->get()->count();
        } else {
            $bank_payment_approvals_count = 0;
        }
        $response_data['bank_payment_approval_count'] = $bank_payment_approvals_count;


        //workOffattendanceRequests...
        if ($loggedin_user_data[0]->role == config('constants.REAL_HR')) {
            $requests_list_count = WorkOff_AttendanceRequest::where('workOff_AttendanceRequest.first_approval_status', 'Pending')
                ->where('workOff_AttendanceRequest.status', 'Pending')
                ->get()->count();
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $requests_list_count = WorkOff_AttendanceRequest::where('workOff_AttendanceRequest.first_approval_status', 'Approved')
                ->where('workOff_AttendanceRequest.second_approval_status', 'Pending')
                ->where('workOff_AttendanceRequest.status', 'Pending')
                ->get()->count();
        }  else {
           $requests_list_count  = 0;
        }

        $response_data['requests_list_count'] = $requests_list_count;

        //hr asset assign count
        if ($loggedin_user_data[0]->role == config('constants.REAL_HR')) {

            $hr_assign_count = AssetAccess::where('asset_access.giver_status', 'Confirmed')
                ->where('asset_access.hr_status', 'Pending')
                ->get()->count();
        } else {

           $hr_assign_count  = 0;
        }
        $response_data['hr_assign_count'] = $hr_assign_count;

        //asset access request
        $asset_assign_requests = \App\AssetAccess::join('users','users.id','=','asset_access.assigner_user_id')
                    ->join('users as B','B.id','=','asset_access.asset_access_user_id')
                    ->join('asset','asset.id','=','asset_access.asset_id')
                    ->where('asset_access.giver_status','Confirmed')
                    ->where('asset_access.hr_status','Confirmed')
                    ->where('asset_access.receiver_status','Pending')
                    ->where('asset_access.asset_access_user_id',$request_data['user_id'])
                    ->get(['users.name as giver_name','B.name as reciever_name','asset.name as asset_name','asset_access.asset_access_date','asset_access.asset_return_date','asset_access.is_allocate'])
                    ->count();

        $response_data['asset_assign_requests'] = $asset_assign_requests;

        // super user manual attendace approval request 03/09/2020
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {

            $manual_attendace_approval_request = \App\Attendance_approvals::where('attendance_approvals.status', 'Pending')
                     ->get()->count();
        } else {

           $manual_attendace_approval_request  = 0;
        }

        $response_data['manual_attendace_approval_request_count'] = $manual_attendace_approval_request;


        //-------------------------------------- Cheque / RTGS / Letter Head ------------------------------------------
            #-------CHEQUE
            if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {

                $signed_cheque_approval_requests_count = Signed_cheque_list::where('status', 'Pending')
                        ->get()->count();
            } else {

                $signed_cheque_approval_requests_count  = 0;
            }

            $response_data['signed_cheque_approval_requests_count'] = $signed_cheque_approval_requests_count;

            #-------RTGS
            if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {

                $signed_rtgs_approval_request_count = Signed_rtgs_request::where('status', 'Pending')
                        ->get()->count();
            } else {

                $signed_rtgs_approval_request_count  = 0;
            }

            $response_data['signed_rtgs_approval_request_count'] = $signed_rtgs_approval_request_count;

            #-------Letter Head
            if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {

                $signed_letterhead_approval_request_count = Signed_letter_head_request::where('status', 'Pending')
                        ->get()->count();
            } else {

                $signed_letterhead_approval_request_count  = 0;
            }

            $response_data['signed_letterhead_approval_request_count'] = $signed_letterhead_approval_request_count;

        //--------------------------------------------------------------------------------------------------
        //Inward Outward
        $user_id = $request_data['user_id'];
        $today_date = date('Y-m-d');
        $partial_query = Inward_outwards::
                join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
                ->join('company', 'company.id', '=', 'inward_outwards.company_id')
                ->join('project', 'project.id', '=', 'inward_outwards.project_id')
                ->leftJoin('department', 'department.id', '=', 'inward_outwards.department_id')
                ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
                ->leftjoin('sender', 'sender.id', '=', 'inward_outwards.sender_id')
                ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
                ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
                ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
                ->where('inward_outwards.type', '=', 'Inwards')
                ->orderBy('inward_outwards.id', 'DESC')
                ->groupBy('inward_outward_users.inward_outward_id');

            $permission_arr = $this->common_task->getPermissionArr($loggedin_user_data[0]->role, 40); //fetch view permissions of users
                if (in_array(5, $permission_arr)) {
                        $inward_data =  $partial_query->get(); //show all request
                } elseif (in_array(1, $permission_arr)) {
                        $inward_data =  $partial_query
                                ->leftJoin('inward_outward_prime_action','inward_outward_prime_action.inward_outward_id','=','inward_outwards.id')
                                ->leftJoin('inward_outward_distrubuted_work','inward_outward_distrubuted_work.inward_outward_prime_action_id','=','inward_outward_prime_action.id')
                                ->groupBy('inward_outward_distrubuted_work.inward_outward_prime_action_id')
                                ->where(function ($query) use ($user_id) {
                            $query->where('inward_outwards.inserted_by', $user_id)
                                    ->orWhere('inward_outwards.requested_by',$user_id )
                                    ->orWhere('inward_outward_users.user_id', $user_id)
                                    ->orWhere(function ($query) use ($user_id) {
                            $query->Where('inward_outwards.prime_employee_id', $user_id )
                                ->Where('inward_outwards.prime_user_status','Accepted');
                        })->orWhere(function ($query) use ($user_id) {
                            $query->Where('inward_outward_distrubuted_work.support_employee_id', $user_id )
                                ->Where('inward_outward_distrubuted_work.emp_status','Accepted');
                        });
                    })->get();
                } else {
                    $inward_data = [];
                }
            $inward_count = count($inward_data);

        #----------------------
        $outward_partial_query = Inward_outwards::
                    join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
                    ->join('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
                    ->join('company', 'company.id', '=', 'inward_outwards.company_id')
                    ->join('project', 'project.id', '=', 'inward_outwards.project_id')
                    ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
                    ->leftJoin('department', 'department.id', '=', 'inward_outwards.department_id')
                    ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
                    ->join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
                    ->where('inward_outwards.type', '=', 'Outwards')
                    ->orderBy('inward_outwards.id', 'DESC')
                    ->groupBy('inward_outward_users.inward_outward_id');

            $permission_arr = $this->common_task->getPermissionArr($loggedin_user_data[0]->role, 40);  //fetch view permissions of users

            if (in_array(5, $permission_arr)) {
                    $outward_data =  $outward_partial_query->get(); //show all  request
            } elseif (in_array(1, $permission_arr)) {
                    $outward_data =  $outward_partial_query->where(function ($query) use ($user_id)  {
                        $query->where('inward_outwards.inserted_by', $user_id)
                                ->orWhere('inward_outwards.requested_by', $user_id )
                                ->orWhere('inward_outward_users.user_id', $user_id);
                        })->get();
            } else {
                    $outward_data = [];
            }

            $outward_count = count($outward_data);
        #----------------------
        $today_partial_query = Inward_outwards::
                join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
                ->join('company', 'company.id', '=', 'inward_outwards.company_id')
                ->join('project', 'project.id', '=', 'inward_outwards.project_id')
                ->leftJoin('department', 'department.id', '=', 'inward_outwards.department_id')
                ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
                ->leftjoin('sender', 'sender.id', '=', 'inward_outwards.sender_id')
                ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
                ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
                ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
                ->where('inward_outwards.type', '=', 'Inwards')
                ->whereDate('inward_outwards.created_at', '=', $today_date)
                ->orderBy('inward_outwards.id', 'DESC')
                ->groupBy('inward_outward_users.inward_outward_id');

            $permission_arr = $this->common_task->getPermissionArr($loggedin_user_data[0]->role, 40); //fetch view permissions of users
                if (in_array(5, $permission_arr)) {
                        $today_inward_data =  $today_partial_query->get(); //show all request
                } elseif (in_array(1, $permission_arr)) {
                        $today_inward_data =  $today_partial_query
                                ->leftJoin('inward_outward_prime_action','inward_outward_prime_action.inward_outward_id','=','inward_outwards.id')
                                ->leftJoin('inward_outward_distrubuted_work','inward_outward_distrubuted_work.inward_outward_prime_action_id','=','inward_outward_prime_action.id')
                                ->groupBy('inward_outward_distrubuted_work.inward_outward_prime_action_id')
                                ->where(function ($query) use ($user_id) {
                            $query->where('inward_outwards.inserted_by', $user_id)
                                    ->orWhere('inward_outwards.requested_by',$user_id )
                                    ->orWhere('inward_outward_users.user_id', $user_id)
                                    ->orWhere(function ($query) use ($user_id) {
                            $query->Where('inward_outwards.prime_employee_id', $user_id )
                                ->Where('inward_outwards.prime_user_status','Accepted');
                        })->orWhere(function ($query) use ($user_id) {
                            $query->Where('inward_outward_distrubuted_work.support_employee_id', $user_id )
                                ->Where('inward_outward_distrubuted_work.emp_status','Accepted');
                        });
                    })->get();
                } else {
                    $today_inward_data = [];
                }
            $today_inward_count = count($today_inward_data);

        #----------------------
        $today_outward_partial_query = Inward_outwards::
                    join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
                    ->join('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
                    ->join('company', 'company.id', '=', 'inward_outwards.company_id')
                    ->join('project', 'project.id', '=', 'inward_outwards.project_id')
                    ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
                    ->leftJoin('department', 'department.id', '=', 'inward_outwards.department_id')
                    ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
                    ->join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
                    ->where('inward_outwards.type', '=', 'Outwards')
                    ->whereDate('inward_outwards.created_at', '=', $today_date)
                    ->orderBy('inward_outwards.id', 'DESC')
                    ->groupBy('inward_outward_users.inward_outward_id');

            $permission_arr = $this->common_task->getPermissionArr($loggedin_user_data[0]->role, 40);  //fetch view permissions of users

            if (in_array(5, $permission_arr)) {
                    $today_outward_data =  $today_outward_partial_query->get(); //show all  request
            } elseif (in_array(1, $permission_arr)) {
                    $today_outward_data =  $today_outward_partial_query->where(function ($query) use ($user_id)  {
                        $query->where('inward_outwards.inserted_by', $user_id)
                                ->orWhere('inward_outwards.requested_by', $user_id )
                                ->orWhere('inward_outward_users.user_id', $user_id);
                        })->get();
            } else {
                    $today_outward_data = [];
            }

            $today_outward_count = count($today_outward_data);
        #----------------------
        $assignee_registry_count =  Inward_outwards::join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
            ->join('company', 'company.id', '=', 'inward_outwards.company_id')
            ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
            ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
            ->join('department', 'department.id', '=', 'inward_outwards.department_id')
            ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
            ->leftjoin('sender', 'sender.id', '=', 'inward_outwards.sender_id')
            ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
            ->join('project', 'project.id', '=', 'inward_outwards.project_id')
            ->where('inward_outward_users.status', '=', 'Processing')
            ->whereDate('inward_outwards.created_at','>=','2020-06-02')
            ->where('inward_outward_users.user_id', '=', $request_data['user_id'])
            ->get()->count();
        #----------------------
        $prime_registry_count =  Inward_outwards::join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
            ->join('company', 'company.id', '=', 'inward_outwards.company_id')
            ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
            ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
            ->join('department', 'department.id', '=', 'inward_outwards.department_id')
            ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
            ->leftjoin('users as B', 'B.id', '=', 'inward_outward_users.user_id')
            ->leftjoin('sender', 'sender.id', '=', 'inward_outwards.sender_id')
            ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
            ->join('project', 'project.id', '=', 'inward_outwards.project_id')
            ->where('inward_outwards.prime_user_status','=', 'Assigned')
            ->whereDate('inward_outwards.created_at','>=','2020-06-02')
            ->where('inward_outwards.prime_employee_id', $request_data['user_id'])->get()->count();
        #----------------------
        $support_user_registry_count = Inward_outward_distrubuted_work::join('inward_outward_prime_action','inward_outward_prime_action.id','=','inward_outward_distrubuted_work.inward_outward_prime_action_id')
            ->join('inward_outwards', 'inward_outwards.id','=','inward_outward_prime_action.inward_outward_id')
            ->join('users','users.id','=','inward_outwards.prime_employee_id')
            ->where('inward_outward_distrubuted_work.emp_status','Assigned')
            ->whereDate('inward_outwards.created_at','>=','2020-06-02')
            ->where('inward_outward_distrubuted_work.support_employee_id','=',$request_data['user_id'])
            ->get()->count();

        $support_prime_user_all_count  = $prime_registry_count+$support_user_registry_count;
        #----------------------
        $rejected_support_emp_request_count = Inward_outward_distrubuted_work::join('inward_outward_prime_action','inward_outward_prime_action.id','=','inward_outward_distrubuted_work.inward_outward_prime_action_id')
                        ->join('inward_outwards', 'inward_outwards.id','=','inward_outward_prime_action.inward_outward_id')
                        ->join('users','users.id','=','inward_outward_distrubuted_work.support_employee_id')
                        ->where('inward_outward_prime_action.final_status','Pending')
                        ->where('inward_outward_distrubuted_work.emp_status','Rejected')
                        ->whereDate('inward_outwards.created_at','>=','2020-06-02')
                        ->where('inward_outwards.prime_employee_id',$request_data['user_id'])
                        ->get()->count();
        #---------------------------

        $response_data['inward_registry_count'] = $inward_count;
        $response_data['outward_registry_count'] = $outward_count;
        $response_data['today_inward_registry_count'] = $today_inward_count;
        $response_data['today_outward_registry_count'] = $today_outward_count;
        $response_data['assignee_registry_count'] = $assignee_registry_count;
        $response_data['support_prime_user_combine_count'] = $support_prime_user_all_count;
        $response_data['support_prime_user'] = ['prime_registry_count' => $prime_registry_count,'support_user_registry_count' => $support_user_registry_count ];
        $response_data['rejected_support_emp_request_count'] = $rejected_support_emp_request_count;

        //---------------------------------------------------------------------------------------------------------
        //Cash Approval
        if ($loggedin_user_data[0]->role == config('constants.Admin')) {
            $cash_approval_count = CashApproval::where('first_approval_status', 'Pending')->get()->count();
        } elseif ($loggedin_user_data[0]->role == config('constants.ACCOUNT_ROLE')) {
            $cash_approval_count = CashApproval::where('first_approval_status', 'Approved')
                ->where('second_approval_status', 'Pending')
                ->get()->count();
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $cash_approval_count = CashApproval::where('first_approval_status', 'Approved')
                ->where('second_approval_status', 'Approved')
                ->where('third_approval_status', 'Pending')
                ->get()->count();
        } else {
            $cash_approval_count = 0;
        }
        $response_data['cash_approval_count'] = $cash_approval_count;



        // if ($loggedin_user_data[0]->role == config('constants.ACCOUNT_ROLE')) {
        //     $online_approval_count = OnlinePaymentApproval::where('first_approval_status', 'Pending')->get()->count();
        // } elseif ($loggedin_user_data[0]->role == config('constants.Admin')) {
        //     $online_approval_count = OnlinePaymentApproval::where('first_approval_status', 'Approved')
        //                     ->where('second_approval_status', 'Pending')
        //                     ->get()->count();
        // } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
        //     $online_approval_count = OnlinePaymentApproval::where('first_approval_status', 'Approved')
        //                     ->where('second_approval_status', 'Approved')
        //                     ->where('third_approval_status', 'Pending')
        //                     ->get()->count();
        // } else {
        //     $online_approval_count = 0;
        // }
        // $response_data['online_approval_count'] = $online_approval_count;
        //Nishit 11/02/2020
        if ($loggedin_user_data[0]->role == config('constants.Admin')) {

            $vehicle_maintenance_approval_count = Vehicle_Maintenance::where('first_approval_status', 'Pending')->get()->count();
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {

            $vehicle_maintenance_approval_count = Vehicle_Maintenance::where('first_approval_status', 'Approved')
                ->where('second_approval_status', 'Pending')
                ->get()->count();
        } else {
            $vehicle_maintenance_approval_count = 0;
        }
        $response_data['vehicle_maintenance_approval_count'] = $vehicle_maintenance_approval_count;


        //Nishit 10/02/2020
        if ($loggedin_user_data[0]->role == config('constants.ACCOUNT_ROLE')) {

            $online_payment_approvals_count = OnlinePaymentApproval::where('first_approval_status', 'Pending')->get()->count();
        } elseif ($loggedin_user_data[0]->role == config('constants.Admin')) {

            $online_payment_approvals_count = OnlinePaymentApproval::where('first_approval_status', 'Approved')
                ->where('second_approval_status', 'Pending')
                ->get()->count();
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {

            $online_payment_approvals_count = OnlinePaymentApproval::where('first_approval_status', 'Approved')
                ->where('second_approval_status', 'Approved')
                ->where('third_approval_status', 'Pending')
                ->get()->count();
        } else {

            $online_payment_approvals_count = 0;
        }

        $response_data['online_payment_approvals_count'] = $online_payment_approvals_count;


        /* if ($loggedin_user_data[0]->role == config('constants.ACCOUNT_ROLE')) {
          $budget_sheet_approvals_count = BudgetSheetApproval::where('first_approval_status', 'Pending')->get()->count();
          } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
          $budget_sheet_approvals_count = BudgetSheetApproval::where('first_approval_status', 'Approved')
          ->where('second_approval_status', 'Pending')
          ->get()->count();
          } else {
          $budget_sheet_approvals_count = 0;
          } */
        if ($loggedin_user_data[0]->role == config('constants.Admin')) {
            $budget_sheet_approvals_count = BudgetSheetApproval::where('first_approval_status', 'Pending')->get()->count();
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $budget_sheet_approvals_count = BudgetSheetApproval::where('first_approval_status', 'Approved')->where('second_approval_status', 'Pending')->get()->count();
            //$budget_sheet_approvals_count = 0;
        } else {
            $budget_sheet_approvals_count = 0;
        }
        $response_data['budget_sheet_approvals_count'] = $budget_sheet_approvals_count;

        //hold budget sheet count start
        $hold_budget_count = 0;
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $hold_budget_count = BudgetSheetApproval::where('is_hold', 'Yes')
                            ->where('budget_sheet_approval.status', 'Approved')
                            ->where('budget_sheet_approval.release_hold_amount_status', 'Pending')
                            ->where('budget_sheet_approval.release_amount_first_approval_status', 'Approved')
                            ->where('budget_sheet_approval.release_amount_second_approval_status', 'Pending')
                            ->get()->count();
        }elseif($loggedin_user_data[0]->role == config('constants.Admin')){
            $hold_budget_count = BudgetSheetApproval::where('is_hold', 'Yes')
                            ->where('budget_sheet_approval.status', 'Approved')
                            ->where('budget_sheet_approval.release_hold_amount_status', 'Pending')
                            ->where('budget_sheet_approval.release_amount_first_approval_status', 'Pending')
                            ->get()->count();
        }else{
            $hold_budget_count = 0;
        }
        $response_data['hold_budget_count'] = $hold_budget_count;
        //hold budget sheet count end

        if ($loggedin_user_data[0]->role == config('constants.LETTERHEAD_APPROVE')) {
            $letterhead_approvals_count = ProSignLetter::where('first_approval_status', 'Pending')->get()->count();
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $letterhead_approvals_count = 0;
        } else {
            $letterhead_approvals_count = 0;
        }
        $response_data['letterhead_approvals_count'] = $letterhead_approvals_count;

        if ($loggedin_user_data[0]->role == config('constants.LETTERHEAD_APPROVE')) {
            $pre_sign_approvals_count = PreSignLetter::where('first_approval_status', 'Pending')->get()->count();
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $pre_sign_approvals_count = PreSignLetter::where('first_approval_status', 'Approved')
                ->where('second_approval_status', 'Pending')
                ->get()->count();
        } else {
            $pre_sign_approvals_count = 0;
        }
        $response_data['pre_sign_approvals_count'] = $pre_sign_approvals_count;


        if ($loggedin_user_data[0]->role == config('constants.REAL_HR')) {
            $salary_count = \App\Payroll::where('first_approval_status', 'Pending')
                //->where('month', date('m'))
                //->where('year', date('Y'))
                ->get()->count();
        } elseif ($loggedin_user_data[0]->role == config('constants.ASSISTANT')) {
            $salary_count = \App\Payroll::where('first_approval_status', 'Approved')
                ->where('second_approval_status', 'Pending')
                //->where('month', date('m'))
                //->where('year', date('Y'))
                ->get()->count();
        } elseif ($loggedin_user_data[0]->role == config('constants.Admin')) {
            $salary_count = \App\Payroll::where('first_approval_status', 'Approved')
                ->where('second_approval_status', 'Approved')
                ->where('third_approval_status', 'Pending')
                //->where('month', date('m'))
                //->where('year', date('Y'))
                ->get()->count();
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $salary_count = \App\Payroll::where('first_approval_status', 'Approved')
                ->where('second_approval_status', 'Approved')
                ->where('third_approval_status', 'Approved')
                ->where('fourth_approval_status', 'Pending')
                //->where('month', date('m'))
                //->where('year', date('Y'))
                ->get()->count();
        } elseif ($loggedin_user_data[0]->role == config('constants.ACCOUNT_ROLE')) {
            $salary_count = \App\Payroll::where('first_approval_status', 'Approved')
                ->where('second_approval_status', 'Approved')
                ->where('third_approval_status', 'Approved')
                ->where('fourth_approval_status', 'Approved')
                ->where('fifth_approval_status', 'Pending')
                //->where('month', date('m'))
                //->where('year', date('Y'))
                ->get()->count();
        } else {
            $salary_count = 0;
        }
        $response_data['salary_approval_count'] = $salary_count;

        //==============================  Resignation

        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {

            $resign_list = Resignation::join('users', 'users.id', '=', 'resignation.user_id')
                ->leftJoin('employee', 'employee.user_id', '=', 'users.id')
                ->where(['resignation.first_approval_status' => 'Approved','resignation.second_approval_status' => 'Approved','resignation.status' => 'Pending','resignation.final_approval_status'=>'Pending'])
                ->get('resignation.id')->count();

        } elseif ($loggedin_user_data[0]->role == config('constants.REAL_HR')) {

            $resign_list = Resignation::join('users', 'users.id', '=', 'resignation.user_id')
                ->leftJoin('employee', 'employee.user_id', '=', 'users.id')
                ->where(['resignation.first_approval_status' => 'Approved', 'resignation.second_approval_status' => 'Pending','resignation.status' => 'Pending'])
                ->get('resignation.id')->count();

        } else {

            $resign_list = Resignation::join('users', 'users.id', '=', 'resignation.user_id')
                ->leftJoin('employee', 'employee.user_id', '=', 'users.id')
                ->where(['employee.reporting_user_id' => $request_data['user_id'], 'resignation.first_approval_status' => 'Pending','resignation.status' => 'Pending'])
                ->get('resignation.id')->count();
        }

        $response_data['resignation_count'] = $resign_list;

        //==============================

        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $trip_approval_count = \App\Vehicle_trip::where('trip_type', '=', 'Individual')
                ->where(['status' => 'Pending', 'is_closed' => 'Yes'])
                ->get(['id'])->count();
        } else {
            $trip_approval_count = \App\Vehicle_trip::where('trip_user_id', $request_data['user_id'])
                ->where('trip_type', '=', 'User')
                ->where(['status' => 'Pending', 'is_closed' => 'Yes'])
                ->where('user_id', '!=', $request_data['user_id'])
                ->get(['id'])->count();
        }
        $response_data['trip_approval_count'] = $trip_approval_count;

        //remote user attendance data
        $response_data['remote_attendance_count'] = \App\AttendanceMaster::join('users', 'users.id', '=', 'attendance_master.user_id')
            ->join('attendance_detail', 'attendance_detail.attendance_master_id', '=', 'attendance_master.id')
            ->where('attendance_master.date', date('Y-m-d'))
            ->where('users.user_attend_type', 'Remote')
            ->groupBy('attendance_detail.attendance_master_id')
            ->get()->count();
        //biometric user attendance data
        $response_data['biometric_attendance_count'] = \App\AttendanceMaster::join('users', 'users.id', '=', 'attendance_master.user_id')
            ->join('attendance_detail', 'attendance_detail.attendance_master_id', '=', 'attendance_master.id')
            ->where('attendance_master.date', date('Y-m-d'))
            ->where('users.user_attend_type', 'Biometric')
            ->groupBy('attendance_detail.attendance_master_id')
            ->get()->count();
        //biometric user attendance data
        $response_data['map_count'] = \App\AttendanceDetail::join('attendance_master', 'attendance_master.id', '=', 'attendance_detail.attendance_master_id')
            ->leftJoin('users', 'users.id', '=', 'attendance_master.user_id')
            ->where('attendance_detail.device_type', 'MOBILE')
            ->whereDate('time', '=', date('Y-m-d'))
            ->orderBy('time', 'DESC')
            ->get()->count();

        //today's leave data
        $response_data['today_leave_count'] = \App\Leaves::where(function ($query) {
            $query->where([['start_date', '<=', date('Y-m-d')], ['end_date', '>=', date('Y-m-d')]]);
        })->where('leave_status', 2)->get()->count();

        //loan count
        if ($loggedin_user_data[0]->role == config('constants.REAL_HR')) {
            $response_data['loan_count'] = EmployeesLoans::where('first_approval_status', '=', 'Pending')
                ->get()
                ->count();
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {

            $response_data['loan_count'] = EmployeesLoans::where('first_approval_status', '=', 'Approved')
                ->where('second_approval_status', '=', 'Pending')
                ->get()
                ->count();
        } elseif ($loggedin_user_data[0]->role == config('constants.ACCOUNT_ROLE')) {
            $response_data['loan_count'] = EmployeesLoans::where('first_approval_status', '=', 'Approved')
                ->where('second_approval_status', '=', 'Approved')
                ->where('third_approval_status', '=', 'Pending')
                ->get()
                ->count();
        } else {
            $response_data['loan_count'] = 0;
        }


        // interview_approval_count      interview_change
        if ($loggedin_user_data[0]->role == config('constants.REAL_HR')) {

            $response_data['interview_approval_count'] = Interview::where('emp_status', '=', 'completed')
            ->where('hr_status', '=', 'Pending')
            ->get()->count();

        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $response_data['interview_approval_count'] = Interview::where('emp_status', '=', 'completed')
            ->where('hr_status', '=', 'Selected')
            ->where('superUser_status', '=', 'Pending')
            ->get()->count();

        } else {
            $response_data['interview_approval_count'] = 0;
        }

        //hold Interview count
        if ($loggedin_user_data[0]->role == config('constants.REAL_HR')) {

            $response_data['hold_interview_count'] = Interview::where('emp_status', '=', 'hold')
            ->where('hr_status', '=', 'Hold')
            ->get()->count();

        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $response_data['hold_interview_count'] = Interview::where('emp_status', '=', 'hold')
            ->where('hr_status', '=', 'Selected')
            ->where('superUser_status', '=', 'Hold')
            ->get()->count();

        } else {
            $response_data['hold_interview_count'] = 0;
        }
        // failed voucher count
        if ($loggedin_user_data[0]->role == config('constants.Admin')
        ) {
            $response_data['failed_voucher_count'] =
            VoucherNumberRegister::select('voucher_number_register.id', 'voucher_number_register.voucher_ref_no', 'company.company_name', 'voucher_number_register.failed_reason', 'voucher_number_register.failed_document', 'voucher_number_register.failed_unique', DB::raw('group_concat(voucher_no) as voucher_numbers'))
                                ->join('company', 'voucher_number_register.company_id', '=', 'company.id')
                                ->where('voucher_number_register.failed_request_status', "Processing")
                                ->WhereNull('voucher_number_register.accountant_status')
                                ->groupBy('failed_unique')
                                ->get()->count();
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $response_data['failed_voucher_count'] = VoucherNumberRegister::select('voucher_number_register.id', 'voucher_number_register.voucher_ref_no', 'company.company_name', 'voucher_number_register.failed_reason', 'voucher_number_register.failed_document', 'voucher_number_register.failed_unique', DB::raw('group_concat(voucher_no) as voucher_numbers'))
                                ->join('company', 'voucher_number_register.company_id', '=', 'company.id')
                                ->where('voucher_number_register.failed_request_status', "Processing")
                                ->where('voucher_number_register.accountant_status', 'Approved')
                                ->WhereNull('voucher_number_register.superadmin_status')
                                ->groupBy('failed_unique')
                                ->get()->count();
        } else {
            $response_data['failed_voucher_count'] = 0;
        }

        // apiUnraedNotificationByUser
        $response_data['unread_notification_count'] = $this->notification_task->getUnreadNotificationByUser($request_data['user_id'])->count();

        // api_entry_approval_count
        $entry_response_data = [];

        $loggedin_user_data = \App\User::where('id', $request_data['user_id'])->get();
        //------------------- Vendor
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $vendor_entry_count = Vendors::where('status', 'Disabled')->where('is_approved',0)->get()->count();
        } else {
            $vendor_entry_count = 0;
        }
        $entry_response_data['vendor_entry_count'] = $vendor_entry_count;
        //-------------------Company
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $company_entry_count = Companies::where('status', 'Disabled')->where('is_approved',0)->get()->count();
        } else {
            $company_entry_count = 0;
        }
        $entry_response_data['company_entry_count'] = $company_entry_count;
        //-------------------Client
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $client_entry_count = Clients::where('status', 'Disabled')->where('is_approved',0)->get()->count();
        } else {
            $client_entry_count = 0;
        }
        $entry_response_data['client_entry_count'] = $client_entry_count;
        //-------------------Project
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $project_entry_count = Projects::where('status', 'Disabled')->where('is_approved',0)->get()->count();
        } else {
            $project_entry_count = 0;
        }
        $entry_response_data['project_entry_count'] = $project_entry_count;
         //-------------------Vendor bank
         if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $vendor_bank_entry_count = Vendors_bank::where('status', 'Disabled')->where('is_approved',0)->get()->count();
        } else {
            $vendor_bank_entry_count = 0;
        }
        $entry_response_data['vendor_bank_entry_count'] = $vendor_bank_entry_count;
         //-------------------Project site
         if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $project_site_entry_count = Project_sites::where('status', 'Disabled')->where('is_approved',0)->get()->count();
        } else {
            $project_site_entry_count = 0;
        }
        $entry_response_data['project_site_entry_count'] = $project_site_entry_count;
        //-------------------Bank
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $bank_entry_count = Banks::where('status', 'Disabled')->where('is_approved',0)->get()->count();
        } else {
            $bank_entry_count = 0;
        }
        $entry_response_data['bank_entry_count'] = $bank_entry_count;
        //-------------------Bank Charge category
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $bank_charge_category_entry_count = Bank_charge_category::where('is_approved',0)->get()->count();
        } else {
            $bank_charge_category_entry_count = 0;
        }
        $entry_response_data['bank_charge_category_entry_count'] = $bank_charge_category_entry_count;
        //-------------------Bank charge sub category
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $bank_charge_sub_category_entry_count = Bank_charge_sub_category::where('is_approved',0)->get()->count();
        } else {
            $bank_charge_sub_category_entry_count = 0;
        }
        $entry_response_data['bank_charge_sub_category_entry_count'] = $bank_charge_sub_category_entry_count;
        //-------------------Payment Card
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $payment_card_entry_count = PaymentCard::where('status', 'Disabled')->where('is_approved',0)->get()->count();
        } else {
            $payment_card_entry_count = 0;
        }
        $entry_response_data['payment_card_entry_count'] = $payment_card_entry_count;
        //-------------------Company Document
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $company_document_entry_count = CompanyDocumentManagement::where('status', 'Disabled')->where('is_approved',0)->get()->count();
        } else {
            $company_document_entry_count = 0;
        }
        $entry_response_data['company_document_entry_count'] = $company_document_entry_count;
        //-------------------Tender Category
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $tender_category_entry_count = TenderCategory::where('status', 'Disabled')->where('is_approved',0)->get()->count();
        } else {
            $tender_category_entry_count = 0;
        }
        $entry_response_data['tender_category_entry_count'] = $tender_category_entry_count;
        //-------------------Tender Pattern
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $tender_pattern_entry_count = TenderPattern::where('status', 'Disabled')->where('is_approved',0)->get()->count();
        } else {
            $tender_pattern_entry_count = 0;
        }
        $entry_response_data['tender_pattern_entry_count'] = $tender_pattern_entry_count;
        //-------------------Tender Physical
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $tender_physical_submission_entry_count = Tender_physical_submission::where('status', 'Disabled')->where('is_approved',0)->get()->count();
        } else {
            $tender_physical_submission_entry_count = 0;
        }
        $entry_response_data['tender_physical_submission_entry_count'] = $tender_physical_submission_entry_count;
        //-------------------Registry category
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $registry_category_entry_count = Inward_outward_doc_category::where('status', 'Disabled')->where('is_approved',0)->get()->count();
        } else {
            $registry_category_entry_count = 0;
        }
        $entry_response_data['registry_category_entry_count'] = $registry_category_entry_count;
        //-------------------Registry sub category
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $registry_sub_category_entry_count = Inward_outward_doc_sub_category::where('status', 'Disabled')->where('is_approved',0)->get()->count();
        } else {
            $registry_sub_category_entry_count = 0;
        }
        $entry_response_data['registry_sub_category_entry_count'] = $registry_sub_category_entry_count;
        //------------------- Delivery mode
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $delivery_mode_entry_count = Inward_outward_delivery_mode::where('status', 'Disabled')->where('is_approved',0)->get()->count();
        } else {
            $delivery_mode_entry_count = 0;
        }
        $entry_response_data['delivery_mode_entry_count'] = $delivery_mode_entry_count;
        //-------------------Sender category
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $sender_category_entry_count = Sender::where('status', 'Disabled')->where('is_approved',0)->get()->count();
        } else {
            $sender_category_entry_count = 0;
        }
        $entry_response_data['sender_category_entry_count'] = $sender_category_entry_count;

        //-------------------TDS Section
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $sender_tds_section_entry_count = TdsSectionType::where('status', 'Disabled')->where('is_approved', 0)->get()->count();
        } else {
            $sender_tds_section_entry_count = 0;
        }
        $entry_response_data['sender_tds_section_entry_count'] = $sender_tds_section_entry_count;

        $response_data['entry_approval_count'] = $entry_response_data;

        // get_upcomings
        $whereCondition = ['user_id' => $request->input('user_id')];

        $birthdays = Employees::whereNOTIn('user_id', [$request->input('user_id')])
                        ->join('users','users.id','=','employee.user_id')
                        ->where('users.is_user_relieved',0)
                        ->whereRaw('DAYOFYEAR(curdate()) <= DAYOFYEAR(birth_date) AND DAYOFYEAR(curdate()) + 7 >=  dayofyear(birth_date)')
                        ->selectRaw('designation,emp_code,user_id, birth_date, birth_date, DATE_FORMAT(birth_date, "%m%d") as order_date, DATE_FORMAT(birth_date, "%m-%d") as monthdate, DATE_FORMAT(birth_date, "%m") as month')
                        ->with(['user' => function($query) {
                                $query->select('id', 'name', 'profile_image')->where('status','Enabled')->where('is_user_relieved',0);
                            }])->orderBy('order_date', 'asc')->get()->toArray();

        $marriage = Employees::whereNOTIn('user_id', [$request->input('user_id')])
                ->join('users','users.id','=','employee.user_id')
                ->where('users.is_user_relieved',0)
                        ->whereRaw('DAYOFYEAR(curdate()) <= DAYOFYEAR(marriage_date) AND DAYOFYEAR(curdate()) + 7 >=  dayofyear(marriage_date)')
                        ->selectRaw('designation,emp_code,user_id, marriage_date, DATE_FORMAT(marriage_date, "%m%d") as order_date, DATE_FORMAT(marriage_date, "%m-%d") as monthdate, DATE_FORMAT(marriage_date, "%m") as month')
                        ->with(['user' => function($query) {
                                $query->select('id', 'name', 'profile_image')->where('status','Enabled')->where('is_user_relieved',0);
                            }])->orderBy('order_date', 'asc')->get()->toArray();

        $work = Employees::whereNOTIn('user_id', [$request->input('user_id')])
                ->join('users','users.id','=','employee.user_id')
                ->where('users.is_user_relieved',0)
                        ->whereRaw('DAYOFYEAR(curdate()) <= DAYOFYEAR(joining_date) AND DAYOFYEAR(curdate()) + 7 >=  dayofyear(joining_date)')
                        ->selectRaw('designation,emp_code,user_id, joining_date, DATE_FORMAT(joining_date, "%m%d") as order_date, DATE_FORMAT(joining_date, "%m-%d") as monthdate, DATE_FORMAT(joining_date, "%m") as month')
                        ->where('joining_date','!=',date('Y-m-d'))
                        ->with(['user' => function($query) {
                                $query->select('id', 'name', 'profile_image')->where('status','Enabled')->where('is_user_relieved',0);
                            }])->orderBy('order_date', 'asc')->get()->toArray();

        $employee = new Employees();
        $upcoming_days = $employee->getFormetedDate(array_merge($birthdays, $marriage, $work));
            
        $response_data['get_upcomings_count'] = count($upcoming_days);

        // get_upcoming_holiday
        $holiday = Holiday::where('start_date', '>=', date('Y-m-d'))->where('start_date', '<=', date('Y-m-d', strtotime("+7 day")))
                ->orderBy('start_date', 'asc')
                ->get(); 
        $response_data['upcoming_holiday_count'] = count($holiday); 



        // get_custodian_pending_request
        $userId = $request->get('user_id');

        $userRole = User::where('id', $userId)->get('role')->first();

        $documentRequest = CompanyDocumentRequest::select('company_document_request.*', 'users.name as requester_name','company_document_management.title as document_title','company_document_management.custodian_id as main_custodian_id')
                        ->join('users', 'users.id', '=', 'company_document_request.request_user_id')
                        ->join('company_document_management', 'company_document_management.id', '=', 'company_document_request.document_id')
                        ->where('company_document_request.custodian_id', $userId)
                        ->where('company_document_request.custodian_approval_status', 'Pending')
                        ->with(['get_company_detail','get_custodian_detail'])->orderBy('name')
                        ->get()->toArray();

        $response_data['custodian_pending_request_count'] = count($documentRequest);


        // get_requester_pending_received
        $documentRequestReceived = CompanyDocumentRequest::select('company_document_request.*', 'users.name as requester_name','company_document_management.title as document_title','company_document_management.custodian_id as main_custodian_id')
                        ->join('users', 'users.id', '=', 'company_document_request.request_user_id')
                        ->join('company_document_management', 'company_document_management.id', '=', 'company_document_request.document_id')
                        ->where('company_document_request.request_user_id', $userId)
                        ->where('company_document_request.request_status', 'Approved')
                        ->with(['get_company_detail','get_custodian_detail'])
                        ->get()->toArray();

        $response_data['requester_pending_received_count'] = count($documentRequestReceived);

        // get_requester_pending_returned
        $documentRequestReturned = CompanyDocumentRequest::select('company_document_request.*', 'users.name as requester_name','company_document_management.title as document_title','company_document_management.custodian_id as main_custodian_id')
                        ->join('users', 'users.id', '=', 'company_document_request.request_user_id')
                        ->join('company_document_management', 'company_document_management.id', '=', 'company_document_request.document_id')
                        ->where('company_document_request.request_user_id', $userId)
                        ->where('company_document_request.request_status', 'Submitted')
                        ->with(['get_company_detail','get_custodian_detail'])
                        ->get()->toArray();
        $response_data['requester_pending_returned_count'] = count($documentRequestReturned);

        // get_custodian_pending_received
        $documentRequestReceived = CompanyDocumentRequest::select('company_document_request.*', 'users.name as requester_name','company_document_management.title as document_title','company_document_management.custodian_id as main_custodian_id')
                        ->join('users', 'users.id', '=', 'company_document_request.request_user_id')
                        ->join('company_document_management', 'company_document_management.id', '=', 'company_document_request.document_id')
                        ->where('company_document_request.custodian_id', $userId)
                        ->where('company_document_request.request_status', 'Returned')
                        ->where('company_document_request.custodian_approval_status', 'Approved')
                        ->with(['get_company_detail','get_custodian_detail'])
                        ->get()->toArray();
        $response_data['custodian_pending_received_count'] = count($documentRequestReceived);

        // get_admin_pending_request
        $documentRequestAdmin = CompanyDocumentRequest::select('company_document_request.*', 'users.name as requester_name','company_document_management.title as document_title','company_document_management.custodian_id as main_custodian_id')
                            ->join('users', 'users.id', '=', 'company_document_request.request_user_id')
                            ->join('company_document_management', 'company_document_management.id', '=', 'company_document_request.document_id')
                            ->where('company_document_request.superadmin_status', 'Pending')->get()->toArray();

        $response_data['admin_pending_request_count'] = count($documentRequestAdmin);



        // get_category_app_display
        $loggedin_user_data = User::where('id', $request_data['user_id'])->get(['id', 'name', 'email', 'role']);
        $category_list = \App\App_display_category::where('status', 'Enabled')->get();

        foreach ($category_list as $key => $category) {
            switch ($category->id) {
                case 1:
                    //get user's permission arr
                    $permission_arr = $this->common_task->getPermissionArr($loggedin_user_data[0]->role, 20);
                    if (empty($permission_arr)) {
                        $category_list[$key]->total_count = 0;
                        continue;
                    }
                    if (in_array(1, $permission_arr) && !in_array(5, $permission_arr) && !in_array(6, $permission_arr)) {
                        $category_list[$key]->total_count = 0;
                        continue;
                    }
                    /*if($loggedin_user_data[0]->role==config('constants.REAL_HR')){
                        $category_list[$key]->total_count = 0;
                        continue;
                    }*/

                    $attendance_list_count = AttendanceDetail::join('attendance_master', 'attendance_master.id', '=', 'attendance_detail.attendance_master_id')
                                    ->join('users', 'users.id', '=', 'attendance_master.user_id')
                                    ->leftJoin('employee', 'employee.user_id', '=', 'users.id')
                                    ->where('attendance_detail.is_approved', 'Pending')
                                    ->where(function($query) use ($permission_arr, $request_data) {
                                        if (in_array(6, $permission_arr) && !in_array(5, $permission_arr)) {

                                            $jr_user_list = \App\Employees::where('reporting_user_id', $request_data['user_id'])->get(['user_id']);
                                            if ($jr_user_list->count() == 0) {
                                                return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
                                            }

                                            //fetch all pending attendance list
                                            $query->whereIn('attendance_master.user_id', $jr_user_list->pluck('user_id'));
                                        }
                                    })
                                    ->get(['users.name', 'users.profile_image', 'employee.designation'])->count();
                    
                    if($loggedin_user_data[0]->role!=config('constants.REAL_HR')){
                        $attendance_list_count=0;
                    }
                                    
                    $category_list[$key]->total_count = $attendance_list_count;
                    break;
                case 2:
                    //get permission arr
                    $permission_arr = $this->common_task->getPermissionArr($loggedin_user_data[0]->role, $this->module_id);
                    if (empty($permission_arr)) {
                        $category_list[$key]->total_count = 0;
                        continue;
                    }
                    /* if (in_array(5, $permission_arr) && in_array(2, $permission_arr)) {
                      //have to allow the approval for all user leaves
                      $leave_list_count = Leaves::join('users', 'users.id', '=', 'leaves.user_id')
                      ->leftJoin('employee', 'employee.user_id', '=', 'users.id')
                      ->where(['leave_status' => 1, 'assign_work_status' => 'Accepted'])
                      ->get(['leaves.*', 'users.name', 'users.profile_image', 'employee.designation'])->count();
                      } elseif (in_array(6, $permission_arr) && in_array(2, $permission_arr)) {
                      //have to allow the approval for the leaves in which his name is as notify id
                      $leave_list_count = Leaves::join('users', 'users.id', '=', 'leaves.user_id')
                      ->leftJoin('employee', 'employee.user_id', '=', 'users.id')
                      ->where(['leave_status' => 1, 'assign_work_status' => 'Accepted'])
                      ->whereRaw('FIND_IN_SET(' . $request_data['user_id'] . ',notify_id)')
                      ->get(['leaves.*', 'users.name', 'users.profile_image', 'employee.designation'])->count();
                      } else {
                      $leave_list_count = 0;
                      } */
                    if (in_array(5, $permission_arr) && in_array(2, $permission_arr)) {
                        //have to allow the approval for all user leaves
                        if ($loggedin_user_data[0]->role == config('constants.REAL_HR')) {
                            $leave_list_count = Leaves::join('users', 'users.id', '=', 'leaves.user_id')
                                            ->leftJoin('employee', 'employee.user_id', '=', 'users.id')
                                            ->where(['leave_status' => 1, 'assign_work_status' => 'Accepted'])
                                            ->where('leaves.first_approval_status', 'Pending')
                                            ->get(['leaves.*', 'users.name', 'users.profile_image', 'employee.designation'])->count();
                        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
                            //$leave_list_count=0;
                           $leave_list_count = Leaves::join('users', 'users.id', '=', 'leaves.user_id')
                                            ->leftJoin('employee', 'employee.user_id', '=', 'users.id')
                                            ->where(['leave_status' => 1, 'assign_work_status' => 'Accepted'])
                                            ->where('leaves.first_approval_status', 'Approved')
                                            ->where('leaves.second_approval_status', 'Approved')
                                            ->where('leaves.third_approval_status', 'Pending')
                                            ->get(['leaves.*', 'users.name', 'users.profile_image', 'employee.designation'])->count();
                        } else {
                            $leave_list_count = Leaves::join('users', 'users.id', '=', 'leaves.user_id')
                                            ->leftJoin('employee', 'employee.user_id', '=', 'users.id')
                                            ->where(['leave_status' => 1, 'assign_work_status' => 'Accepted'])
                                            ->where('leaves.first_approval_status', 'Approved')
                                            ->where('leaves.second_approval_status', 'Pending')
                                            ->get(['leaves.*', 'users.name', 'users.profile_image', 'employee.designation'])->count();
                        }
                    } else {
                        $leave_list_count = 0;
                    }

                    $category_list[$key]->total_count = $leave_list_count;
                    break;
                case 3:
                    $leave_work_count = Leaves::join('users', 'users.id', '=', 'leaves.user_id')
                                    ->where(['assign_work_user_id' => $request_data['user_id'], 'assign_work_status' => 'Pending'])
                                    ->orderBy('leaves.created_at', 'DESC')
                                    ->get(['leaves.*', 'users.name', 'users.profile_image', 'users.email'])->count();
                    $category_list[$key]->total_count = $leave_work_count;
                    break;

                case 5:

                    $role_permission = $this->common_task->getPermissionArr($loggedin_user_data[0]->role, $this->module_id);

                    $expense_select = ['employee_expense.id', 'employee_expense.user_id', 'expense_category.category_name', 'employee_expense.expense_category as expense_category_id', 'employee_expense.title',
                        'employee_expense.bill_number', 'employee_expense.merchant_name', 'employee_expense.amount',
                        'employee_expense.expense_date', 'employee_expense.comment',
                        'employee_expense.expense_image', 'employee_expense.status', 'users.name'
                    ];


                    if ($loggedin_user_data[0]->role == config('constants.REAL_HR')) {
                        $expense_list_count = Employee_expense::join('expense_category', 'expense_category.id', '=', 'employee_expense.expense_category')
                                        ->leftJoin('users', 'users.id', '=', 'employee_expense.approved_by')
                                        ->leftJoin('employee', 'employee.user_id', '=', 'employee_expense.user_id')
                                        ->join('company', 'company.id', '=', 'employee_expense.company_id')
                                        ->join('project', 'project.id', '=', 'employee_expense.project_id')
                                        ->where('first_approval_status', 'Pending')
                                        ->get($expense_select)->count();
                    } 
                    elseif ($loggedin_user_data[0]->role == config('constants.ASSISTANT')) {
                        $expense_list_count = Employee_expense::join('expense_category', 'expense_category.id', '=', 'employee_expense.expense_category')
                                        ->leftJoin('users', 'users.id', '=', 'employee_expense.approved_by')
                                        ->leftJoin('employee', 'employee.user_id', '=', 'employee_expense.user_id')
                                        ->join('company', 'company.id', '=', 'employee_expense.company_id')
                                        ->join('project', 'project.id', '=', 'employee_expense.project_id')
                                        ->where('first_approval_status', 'Approved')
                                        ->where('second_approval_status', 'Pending')
                                        ->get($expense_select)->count();
                    }
                    elseif ($loggedin_user_data[0]->role == config('constants.Admin')) {
                        $expense_list_count = Employee_expense::join('expense_category', 'expense_category.id', '=', 'employee_expense.expense_category')
                                        ->leftJoin('users', 'users.id', '=', 'employee_expense.approved_by')
                                        ->leftJoin('employee', 'employee.user_id', '=', 'employee_expense.user_id')
                                        ->join('company', 'company.id', '=', 'employee_expense.company_id')
                                        ->join('project', 'project.id', '=', 'employee_expense.project_id')
                                        ->where('first_approval_status', 'Approved')
                                        ->where('second_approval_status', 'Approved')
                                        ->where('third_approval_status', 'Pending')
                                        ->get($expense_select)->count();
                    }
                    elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
                        $expense_list_count = Employee_expense::join('expense_category', 'expense_category.id', '=', 'employee_expense.expense_category')
                                        ->leftJoin('users', 'users.id', '=', 'employee_expense.approved_by')
                                        ->leftJoin('employee', 'employee.user_id', '=', 'employee_expense.user_id')
                                        ->join('company', 'company.id', '=', 'employee_expense.company_id')
                                        ->join('project', 'project.id', '=', 'employee_expense.project_id')
                                        ->where('first_approval_status', 'Approved')
                                        ->where('second_approval_status', 'Approved')
                                        ->where('third_approval_status', 'Approved')
                                        ->where('forth_approval_status', 'Pending')
                                        ->get($expense_select)->count();
                    } elseif ($loggedin_user_data[0]->role == config('constants.ACCOUNT_ROLE')) {
                        $expense_list_count = Employee_expense::join('expense_category', 'expense_category.id', '=', 'employee_expense.expense_category')
                                        ->leftJoin('users', 'users.id', '=', 'employee_expense.approved_by')
                                        ->leftJoin('employee', 'employee.user_id', '=', 'employee_expense.user_id')
                                        ->join('company', 'company.id', '=', 'employee_expense.company_id')
                                        ->join('project', 'project.id', '=', 'employee_expense.project_id')
                                        ->where('first_approval_status', 'Approved')
                                        ->where('second_approval_status', 'Approved')
                                        ->where('third_approval_status', 'Approved')
                                        ->where('forth_approval_status', 'Approved')
                                        ->where('fifth_approval_status', 'Pending')
                                        ->get($expense_select)->count();
                    } else {
                        $expense_list_count = 0;
                    }

                    $category_list[$key]->total_count = $expense_list_count;
                    break;

                case 6:

                    if ($loggedin_user_data[0]->role == config('constants.ACCOUNT_ROLE')) {
                        $driver_expense_count = Driver_expense::join('users', 'users.id', '=', 'driver_expense.user_id')
                                        ->where('driver_expense.first_approval_status', 'Pending')
                                        ->where('driver_expense.moniter_user_id', $request_data['user_id'])
                                        ->get(['driver_expense.*', 'users.name as driver_name'])->count();
                    } elseif ($loggedin_user_data[0]->role == config('constants.Admin')) {
                        $driver_expense_count = Driver_expense::join('users', 'users.id', '=', 'driver_expense.user_id')
                                        ->where('driver_expense.first_approval_status', 'Approved')
                                        ->where('driver_expense.second_approval_status', 'Pending')
                                        ->get(['driver_expense.*', 'users.name as driver_name'])->count();
                    } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
                        $driver_expense_count = Driver_expense::join('users', 'users.id', '=', 'driver_expense.user_id')
                                        ->where('driver_expense.first_approval_status', 'Approved')
                                        ->where('driver_expense.second_approval_status', 'Approved')
                                        ->where('driver_expense.third_approval_status', 'Pending')
                                        ->get(['driver_expense.*', 'users.name as driver_name'])->count();
                    } else {
                        $driver_expense_count = 0;
                    }

                    $category_list[$key]->total_count = $driver_expense_count;
                    break;

                case 7:
                    if ($loggedin_user_data[0]->role == config('constants.REAL_HR')) {
                        $remote_attend_request_count = RemoteAttendanceRequest::with('places')
                                        ->where('main_approval_status', 'Pending')
                                        ->get(['id', 'user_id', 'place_id', 'reason', 'date', 'main_approval_status', 'first_approval_status', 'first_approval_id', 'reject_reason'])->count();
                    } else {
                        $remote_attend_request_count = 0;
                    }
                    $category_list[$key]->total_count = $remote_attend_request_count;
                    break;
                //--------
                case 8:
               
                $meeting_request_count = \App\Meeting::join('MeetingMOM', 'meeting.id','=','MeetingMOM.meeting_id')
                        ->where('MeetingMOM.meeting_user_id','=',$request_data['user_id'])
                        ->where('MeetingMOM.status','Pending')
                        ->orderBy('meeting.id', 'DESC')
                        ->get()->count();
               
                $category_list[$key]->total_count = $meeting_request_count;
                break;   

                default:
                    break;
            }
        }
        $response_data['category_app_display_count'] = count($category_list);              

        // registry_module_count
        $user_id = $request_data['user_id'];
        $logged_in_userdata = User::where('id', $request_data['user_id'])->get();
        
        $partial_query = Inward_outwards::
                join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
                ->join('company', 'company.id', '=', 'inward_outwards.company_id')
                ->join('project', 'project.id', '=', 'inward_outwards.project_id')
                ->leftJoin('department', 'department.id', '=', 'inward_outwards.department_id')
                ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
                ->leftjoin('sender', 'sender.id', '=', 'inward_outwards.sender_id')
                ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
                ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
                ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
                ->where('inward_outwards.type', '=', 'Inwards')
                ->orderBy('inward_outwards.id', 'DESC')
                ->groupBy('inward_outward_users.inward_outward_id');

            $permission_arr = $this->common_task->getPermissionArr($logged_in_userdata[0]->role, 40); //fetch view permissions of users
                if (in_array(5, $permission_arr)) {
                        $inward_data =  $partial_query->get(); //show all request
                } elseif (in_array(1, $permission_arr)) {
                        $inward_data =  $partial_query
                                ->leftJoin('inward_outward_prime_action','inward_outward_prime_action.inward_outward_id','=','inward_outwards.id')
                                ->leftJoin('inward_outward_distrubuted_work','inward_outward_distrubuted_work.inward_outward_prime_action_id','=','inward_outward_prime_action.id')
                                ->groupBy('inward_outward_distrubuted_work.inward_outward_prime_action_id')
                                ->where(function ($query) use ($user_id) {
                            $query->where('inward_outwards.inserted_by', $user_id)
                                    ->orWhere('inward_outwards.requested_by',$user_id )
                                    ->orWhere('inward_outward_users.user_id', $user_id)
                                    ->orWhere(function ($query) use ($user_id) {
                            $query->Where('inward_outwards.prime_employee_id', $user_id )
                                ->Where('inward_outwards.prime_user_status','Accepted');
                        })->orWhere(function ($query) use ($user_id) {
                            $query->Where('inward_outward_distrubuted_work.support_employee_id', $user_id )
                                ->Where('inward_outward_distrubuted_work.emp_status','Accepted');
                        });
                    })->get();
                } else {
                    $inward_data = [];
                }
            $inward_count = count($inward_data);
          
        #----------------------
        $outward_partial_query = Inward_outwards::
                    join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
                    ->join('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
                    ->join('company', 'company.id', '=', 'inward_outwards.company_id')
                    ->join('project', 'project.id', '=', 'inward_outwards.project_id')
                    ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
                    ->leftJoin('department', 'department.id', '=', 'inward_outwards.department_id')
                    ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
                    ->join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
                    ->where('inward_outwards.type', '=', 'Outwards')
                    ->orderBy('inward_outwards.id', 'DESC')
                    ->groupBy('inward_outward_users.inward_outward_id');

            $permission_arr = $this->common_task->getPermissionArr($logged_in_userdata[0]->role, 40);  //fetch view permissions of users
            
            if (in_array(5, $permission_arr)) {
                    $outward_data =  $outward_partial_query->get(); //show all  request
            } elseif (in_array(1, $permission_arr)) {
                    $outward_data =  $outward_partial_query->where(function ($query) use ($user_id)  {
                        $query->where('inward_outwards.inserted_by', $user_id)
                                ->orWhere('inward_outwards.requested_by', $user_id )
                                ->orWhere('inward_outward_users.user_id', $user_id);
                        })->get();
            } else {
                    $outward_data = [];
            } 

            $outward_count = count($outward_data); 
        $today_date = date('Y-m-d');
        #----------------------
        $today_partial_query = Inward_outwards::
                join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
                ->join('company', 'company.id', '=', 'inward_outwards.company_id')
                ->join('project', 'project.id', '=', 'inward_outwards.project_id')
                ->leftJoin('department', 'department.id', '=', 'inward_outwards.department_id')
                ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
                ->leftjoin('sender', 'sender.id', '=', 'inward_outwards.sender_id')
                ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
                ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
                ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
                ->where('inward_outwards.type', '=', 'Inwards')
                ->whereDate('inward_outwards.created_at', '=', $today_date)
                ->orderBy('inward_outwards.id', 'DESC')
                ->groupBy('inward_outward_users.inward_outward_id');

            $permission_arr = $this->common_task->getPermissionArr($logged_in_userdata[0]->role, 40); //fetch view permissions of users
                if (in_array(5, $permission_arr)) {
                        $today_inward_data =  $today_partial_query->get(); //show all request
                } elseif (in_array(1, $permission_arr)) {
                        $today_inward_data =  $today_partial_query
                                ->leftJoin('inward_outward_prime_action','inward_outward_prime_action.inward_outward_id','=','inward_outwards.id')
                                ->leftJoin('inward_outward_distrubuted_work','inward_outward_distrubuted_work.inward_outward_prime_action_id','=','inward_outward_prime_action.id')
                                ->groupBy('inward_outward_distrubuted_work.inward_outward_prime_action_id')
                                ->where(function ($query) use ($user_id) {
                            $query->where('inward_outwards.inserted_by', $user_id)
                                    ->orWhere('inward_outwards.requested_by',$user_id )
                                    ->orWhere('inward_outward_users.user_id', $user_id)
                                    ->orWhere(function ($query) use ($user_id) {
                            $query->Where('inward_outwards.prime_employee_id', $user_id )
                                ->Where('inward_outwards.prime_user_status','Accepted');
                        })->orWhere(function ($query) use ($user_id) {
                            $query->Where('inward_outward_distrubuted_work.support_employee_id', $user_id )
                                ->Where('inward_outward_distrubuted_work.emp_status','Accepted');
                        });
                    })->get();
                } else {
                    $today_inward_data = [];
                }
            $today_inward_count = count($today_inward_data);
          
        #----------------------
        $today_outward_partial_query = Inward_outwards::
                    join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
                    ->join('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
                    ->join('company', 'company.id', '=', 'inward_outwards.company_id')
                    ->join('project', 'project.id', '=', 'inward_outwards.project_id')
                    ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
                    ->leftJoin('department', 'department.id', '=', 'inward_outwards.department_id')
                    ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
                    ->join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
                    ->where('inward_outwards.type', '=', 'Outwards')
                    ->whereDate('inward_outwards.created_at', '=', $today_date)
                    ->orderBy('inward_outwards.id', 'DESC')
                    ->groupBy('inward_outward_users.inward_outward_id');

            $permission_arr = $this->common_task->getPermissionArr($logged_in_userdata[0]->role, 40);  //fetch view permissions of users
            
            if (in_array(5, $permission_arr)) {
                    $today_outward_data =  $today_outward_partial_query->get(); //show all  request
            } elseif (in_array(1, $permission_arr)) {
                    $today_outward_data =  $today_outward_partial_query->where(function ($query) use ($user_id)  {
                        $query->where('inward_outwards.inserted_by', $user_id)
                                ->orWhere('inward_outwards.requested_by', $user_id )
                                ->orWhere('inward_outward_users.user_id', $user_id);
                        })->get();
            } else {
                    $today_outward_data = [];
            } 

            $today_outward_count = count($today_outward_data); 
        #----------------------
        $assignee_registry_count =  Inward_outwards::join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
            ->join('company', 'company.id', '=', 'inward_outwards.company_id')
            ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
            ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
            ->join('department', 'department.id', '=', 'inward_outwards.department_id')
            ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
            ->leftjoin('sender', 'sender.id', '=', 'inward_outwards.sender_id')
            ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
            ->join('project', 'project.id', '=', 'inward_outwards.project_id')
            ->where('inward_outward_users.status', '=', 'Processing')
            ->whereDate('inward_outwards.created_at','>=','2020-06-02')
            ->where('inward_outward_users.user_id', '=', $request_data['user_id'])
            ->get()->count();
        #---------------------- 
        $prime_registry_count =  Inward_outwards::join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
            ->join('company', 'company.id', '=', 'inward_outwards.company_id')
            ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
            ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
            ->join('department', 'department.id', '=', 'inward_outwards.department_id')
            ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
            ->leftjoin('users as B', 'B.id', '=', 'inward_outward_users.user_id')
            ->leftjoin('sender', 'sender.id', '=', 'inward_outwards.sender_id')
            ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
            ->join('project', 'project.id', '=', 'inward_outwards.project_id')
            ->where('inward_outwards.prime_user_status','=', 'Assigned')
            ->whereDate('inward_outwards.created_at','>=','2020-06-02')
            ->where('inward_outwards.prime_employee_id', $request_data['user_id'])->get()->count();
        #----------------------
        $support_user_registry_count = Inward_outward_distrubuted_work::join('inward_outward_prime_action','inward_outward_prime_action.id','=','inward_outward_distrubuted_work.inward_outward_prime_action_id')
            ->join('inward_outwards', 'inward_outwards.id','=','inward_outward_prime_action.inward_outward_id') 
            ->join('users','users.id','=','inward_outwards.prime_employee_id')
            ->where('inward_outward_distrubuted_work.emp_status','Assigned')
            ->whereDate('inward_outwards.created_at','>=','2020-06-02')
            ->where('inward_outward_distrubuted_work.support_employee_id','=',$request_data['user_id'])
            ->get()->count();

        $support_prime_user_all_count  = $prime_registry_count+$support_user_registry_count;
        #----------------------
        $rejected_support_emp_request_count = Inward_outward_distrubuted_work::join('inward_outward_prime_action','inward_outward_prime_action.id','=','inward_outward_distrubuted_work.inward_outward_prime_action_id')
                        ->join('inward_outwards', 'inward_outwards.id','=','inward_outward_prime_action.inward_outward_id')               
                        ->join('users','users.id','=','inward_outward_distrubuted_work.support_employee_id')
                        ->where('inward_outward_prime_action.final_status','Pending')
                        ->where('inward_outward_distrubuted_work.emp_status','Rejected') 
                        ->whereDate('inward_outwards.created_at','>=','2020-06-02')
                        ->where('inward_outwards.prime_employee_id',$request_data['user_id'])
                        ->get()->count();
        #----------------------Not In use now
        $submit_entries_count = Inward_outward_distrubuted_work::join('inward_outward_prime_action','inward_outward_prime_action.id','=','inward_outward_distrubuted_work.inward_outward_prime_action_id')
            ->join('inward_outwards', 'inward_outwards.id','=','inward_outward_prime_action.inward_outward_id') 
            ->join('users','users.id','=','inward_outward_distrubuted_work.support_employee_id')
            ->where('inward_outward_distrubuted_work.emp_status','Accepted')
            ->where('inward_outward_distrubuted_work.work_status','Submitted')
            ->where('inward_outwards.prime_employee_id',$request_data['user_id'])
            ->get()->count();
        #---------------------------
        $response_data_count = [  
            'inward_registry_count' => $inward_count,
            'outward_registry_count' => $outward_count,
            'today_inward_registry_count' => $today_inward_count,
            'today_outward_registry_count' => $today_outward_count,
            'assignee_registry_count' => $assignee_registry_count,
            'support_prime_user_combine_count' => $support_prime_user_all_count,
            'support_prime_user' => ['prime_registry_count' => $prime_registry_count,'support_user_registry_count' => $support_user_registry_count    ],
            'rejected_support_emp_request_count' => $rejected_support_emp_request_count
            //'submit_entries_count' => $submit_entries_count
        ];
        $response_data['registry_module_count'] = $response_data_count;
        return response()->json(['status' => true, 'msg' => 'Record found', 'data' => $response_data]);
    }

    public function approval_count_new(Request $request)      //this  11 .. 16..17
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $loggedin_user_data = \App\User::where('id', $request_data['user_id'])->get();

        //comliance reminders
        $partial_query = Compliance_reminders::join('compliance_reminders_done_status','compliance_reminders.id','=','compliance_reminders_done_status.compliance_reminders_id')
                ->join('company','company.id','=','compliance_reminders.company_id')
                ->join('compliance_category','compliance_category.id','=','compliance_reminders.compliance_category_id')
                ->join('users as A','A.id','=','compliance_reminders.responsible_person_id')
                ->join('users as B','B.id','=','compliance_reminders.payment_responsible_person_id')
                ->join('users as C','C.id','=','compliance_reminders.super_admin_checker_id')
                ->leftjoin('users as D','D.id','=','compliance_reminders.checker_id')
                ->where('compliance_reminders_done_status.final_status','Pending');

                $reminder_list =  $partial_query->where(function ($query) use ($request_data)   {
                        $query->where('compliance_reminders_done_status.responsible_person_id', $request_data['user_id'])

                        ->orWhere(function ($query) use ($request_data)  {
                            $query->Where('compliance_reminders_done_status.payment_responsible_person_id', $request_data['user_id'] );

                        })->orWhere(function ($query) use ($request_data)  {
                            $query->Where('compliance_reminders_done_status.checker_id', $request_data['user_id'] );

                        })->orWhere(function ($query) use ($request_data)  {
                            $query->Where('compliance_reminders_done_status.super_admin_checker_id', $request_data['user_id'] );

                        });
                });

        $compliance_reminder_count = $reminder_list->count();
        $response_data['compliance_reminder_count'] = $compliance_reminder_count;

        if ($loggedin_user_data[0]->role == config('constants.ACCOUNT_ROLE')) {
            $bank_payment_approvals_count = BankPaymentApproval::where('first_approval_status', 'Pending')->count();
        } elseif ($loggedin_user_data[0]->role == config('constants.Admin')) {
            $bank_payment_approvals_count = BankPaymentApproval::where('first_approval_status', 'Approved')
                ->where('second_approval_status', 'Pending')
                ->count();
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $bank_payment_approvals_count = BankPaymentApproval::where('first_approval_status', 'Approved')
                ->where('second_approval_status', 'Approved')
                ->where('third_approval_status', 'Pending')
                ->count();
        } else {
            $bank_payment_approvals_count = 0;
        }
        $response_data['bank_payment_approval_count'] = $bank_payment_approvals_count;


        //workOffattendanceRequests...
        if ($loggedin_user_data[0]->role == config('constants.REAL_HR')) {
            $requests_list_count = WorkOff_AttendanceRequest::where('workOff_AttendanceRequest.first_approval_status', 'Pending')
                ->where('workOff_AttendanceRequest.status', 'Pending')
                ->count();
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $requests_list_count = WorkOff_AttendanceRequest::where('workOff_AttendanceRequest.first_approval_status', 'Approved')
                ->where('workOff_AttendanceRequest.second_approval_status', 'Pending')
                ->where('workOff_AttendanceRequest.status', 'Pending')
                ->count();
        }  else {
           $requests_list_count  = 0;
        }

        $response_data['requests_list_count'] = $requests_list_count;

        //hr asset assign count
        if ($loggedin_user_data[0]->role == config('constants.REAL_HR')) {

            $hr_assign_count = AssetAccess::where('asset_access.giver_status', 'Confirmed')
                ->where('asset_access.hr_status', 'Pending')
                ->count();
        } else {

           $hr_assign_count  = 0;
        }
        $response_data['hr_assign_count'] = $hr_assign_count;

        //asset access request
        $asset_assign_requests = \App\AssetAccess::join('users','users.id','=','asset_access.assigner_user_id')
                    ->join('users as B','B.id','=','asset_access.asset_access_user_id')
                    ->join('asset','asset.id','=','asset_access.asset_id')
                    ->where('asset_access.giver_status','Confirmed')
                    ->where('asset_access.hr_status','Confirmed')
                    ->where('asset_access.receiver_status','Pending')
                    ->where('asset_access.asset_access_user_id',$request_data['user_id'])
                    ->get(['users.id'])
                    ->count();

        $response_data['asset_assign_requests'] = $asset_assign_requests;

        // super user manual attendace approval request 03/09/2020
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {

            $manual_attendace_approval_request = \App\Attendance_approvals::where('attendance_approvals.status', 'Pending')
                     ->count();
        } else {

           $manual_attendace_approval_request  = 0;
        }

        $response_data['manual_attendace_approval_request_count'] = $manual_attendace_approval_request;


        //-------------------------------------- Cheque / RTGS / Letter Head ------------------------------------------
            #-------CHEQUE
            if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {

                $signed_cheque_approval_requests_count = Signed_cheque_list::where('status', 'Pending')
                        ->count();
            } else {

                $signed_cheque_approval_requests_count  = 0;
            }

            $response_data['signed_cheque_approval_requests_count'] = $signed_cheque_approval_requests_count;

            #-------RTGS
            if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {

                $signed_rtgs_approval_request_count = Signed_rtgs_request::where('status', 'Pending')
                        ->count();
            } else {

                $signed_rtgs_approval_request_count  = 0;
            }

            $response_data['signed_rtgs_approval_request_count'] = $signed_rtgs_approval_request_count;

            #-------Letter Head
            if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {

                $signed_letterhead_approval_request_count = Signed_letter_head_request::where('status', 'Pending')
                        ->count();
            } else {

                $signed_letterhead_approval_request_count  = 0;
            }

            $response_data['signed_letterhead_approval_request_count'] = $signed_letterhead_approval_request_count;

        //--------------------------------------------------------------------------------------------------
        //Inward Outward
        $user_id = $request_data['user_id'];
        $today_date = date('Y-m-d');
        $partial_query = Inward_outwards::
                join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
                ->join('company', 'company.id', '=', 'inward_outwards.company_id')
                ->join('project', 'project.id', '=', 'inward_outwards.project_id')
                ->leftJoin('department', 'department.id', '=', 'inward_outwards.department_id')
                ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
                ->leftjoin('sender', 'sender.id', '=', 'inward_outwards.sender_id')
                ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
                ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
                ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
                ->where('inward_outwards.type', '=', 'Inwards')
                ->orderBy('inward_outwards.id', 'DESC')
                ->groupBy('inward_outward_users.inward_outward_id');

            $permission_arr = $this->common_task->getPermissionArr($loggedin_user_data[0]->role, 40); //fetch view permissions of users
                if (in_array(5, $permission_arr)) {
                        $inward_data =  $partial_query->get(); //show all request
                } elseif (in_array(1, $permission_arr)) {
                        $inward_data =  $partial_query
                                ->leftJoin('inward_outward_prime_action','inward_outward_prime_action.inward_outward_id','=','inward_outwards.id')
                                ->leftJoin('inward_outward_distrubuted_work','inward_outward_distrubuted_work.inward_outward_prime_action_id','=','inward_outward_prime_action.id')
                                ->groupBy('inward_outward_distrubuted_work.inward_outward_prime_action_id')
                                ->where(function ($query) use ($user_id) {
                            $query->where('inward_outwards.inserted_by', $user_id)
                                    ->orWhere('inward_outwards.requested_by',$user_id )
                                    ->orWhere('inward_outward_users.user_id', $user_id)
                                    ->orWhere(function ($query) use ($user_id) {
                            $query->Where('inward_outwards.prime_employee_id', $user_id )
                                ->Where('inward_outwards.prime_user_status','Accepted');
                        })->orWhere(function ($query) use ($user_id) {
                            $query->Where('inward_outward_distrubuted_work.support_employee_id', $user_id )
                                ->Where('inward_outward_distrubuted_work.emp_status','Accepted');
                        });
                    })->get();
                } else {
                    $inward_data = [];
                }
            $inward_count = count($inward_data);

        #----------------------
        $outward_partial_query = Inward_outwards::
                    join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
                    ->join('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
                    ->join('company', 'company.id', '=', 'inward_outwards.company_id')
                    ->join('project', 'project.id', '=', 'inward_outwards.project_id')
                    ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
                    ->leftJoin('department', 'department.id', '=', 'inward_outwards.department_id')
                    ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
                    ->join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
                    ->where('inward_outwards.type', '=', 'Outwards')
                    ->orderBy('inward_outwards.id', 'DESC')
                    ->groupBy('inward_outward_users.inward_outward_id');

            $permission_arr = $this->common_task->getPermissionArr($loggedin_user_data[0]->role, 40);  //fetch view permissions of users

            if (in_array(5, $permission_arr)) {
                    $outward_data =  $outward_partial_query->count(); //show all  request
            } elseif (in_array(1, $permission_arr)) {
                    $outward_data =  $outward_partial_query->where(function ($query) use ($user_id)  {
                        $query->where('inward_outwards.inserted_by', $user_id)
                                ->orWhere('inward_outwards.requested_by', $user_id )
                                ->orWhere('inward_outward_users.user_id', $user_id);
                        })->count();
            } else {
                    $outward_data = 0;
            }

            $outward_count = $outward_data;
        #----------------------
        $today_partial_query = Inward_outwards::
                join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
                ->join('company', 'company.id', '=', 'inward_outwards.company_id')
                ->join('project', 'project.id', '=', 'inward_outwards.project_id')
                ->leftJoin('department', 'department.id', '=', 'inward_outwards.department_id')
                ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
                ->leftjoin('sender', 'sender.id', '=', 'inward_outwards.sender_id')
                ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
                ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
                ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
                ->where('inward_outwards.type', '=', 'Inwards')
                ->whereDate('inward_outwards.created_at', '=', $today_date)
                ->orderBy('inward_outwards.id', 'DESC')
                ->groupBy('inward_outward_users.inward_outward_id');

            $permission_arr = $this->common_task->getPermissionArr($loggedin_user_data[0]->role, 40); //fetch view permissions of users
                if (in_array(5, $permission_arr)) {
                        $today_inward_data =  $today_partial_query->count(); //show all request
                } elseif (in_array(1, $permission_arr)) {
                        $today_inward_data =  $today_partial_query
                                ->leftJoin('inward_outward_prime_action','inward_outward_prime_action.inward_outward_id','=','inward_outwards.id')
                                ->leftJoin('inward_outward_distrubuted_work','inward_outward_distrubuted_work.inward_outward_prime_action_id','=','inward_outward_prime_action.id')
                                ->groupBy('inward_outward_distrubuted_work.inward_outward_prime_action_id')
                                ->where(function ($query) use ($user_id) {
                            $query->where('inward_outwards.inserted_by', $user_id)
                                    ->orWhere('inward_outwards.requested_by',$user_id )
                                    ->orWhere('inward_outward_users.user_id', $user_id)
                                    ->orWhere(function ($query) use ($user_id) {
                            $query->Where('inward_outwards.prime_employee_id', $user_id )
                                ->Where('inward_outwards.prime_user_status','Accepted');
                        })->orWhere(function ($query) use ($user_id) {
                            $query->Where('inward_outward_distrubuted_work.support_employee_id', $user_id )
                                ->Where('inward_outward_distrubuted_work.emp_status','Accepted');
                        });
                    })->count();
                } else {
                    $today_inward_data = 0;
                }
            $today_inward_count = $today_inward_data;

        #----------------------
        $today_outward_partial_query = Inward_outwards::
                    join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
                    ->join('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
                    ->join('company', 'company.id', '=', 'inward_outwards.company_id')
                    ->join('project', 'project.id', '=', 'inward_outwards.project_id')
                    ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
                    ->leftJoin('department', 'department.id', '=', 'inward_outwards.department_id')
                    ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
                    ->join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
                    ->where('inward_outwards.type', '=', 'Outwards')
                    ->whereDate('inward_outwards.created_at', '=', $today_date)
                    ->orderBy('inward_outwards.id', 'DESC')
                    ->groupBy('inward_outward_users.inward_outward_id');

            $permission_arr = $this->common_task->getPermissionArr($loggedin_user_data[0]->role, 40);  //fetch view permissions of users

            if (in_array(5, $permission_arr)) {
                    $today_outward_data =  $today_outward_partial_query->count(); //show all  request
            } elseif (in_array(1, $permission_arr)) {
                    $today_outward_data =  $today_outward_partial_query->where(function ($query) use ($user_id)  {
                        $query->where('inward_outwards.inserted_by', $user_id)
                                ->orWhere('inward_outwards.requested_by', $user_id )
                                ->orWhere('inward_outward_users.user_id', $user_id);
                        })->count();
            } else {
                    $today_outward_data = 0;
            }

            $today_outward_count = $today_outward_data;
        #----------------------
        $assignee_registry_count =  Inward_outwards::join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
            ->join('company', 'company.id', '=', 'inward_outwards.company_id')
            ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
            ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
            ->join('department', 'department.id', '=', 'inward_outwards.department_id')
            ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
            ->leftjoin('sender', 'sender.id', '=', 'inward_outwards.sender_id')
            ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
            ->join('project', 'project.id', '=', 'inward_outwards.project_id')
            ->where('inward_outward_users.status', '=', 'Processing')
            ->whereDate('inward_outwards.created_at','>=','2020-06-02')
            ->where('inward_outward_users.user_id', '=', $request_data['user_id'])
            ->count();
        #----------------------
        $prime_registry_count =  Inward_outwards::join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
            ->join('company', 'company.id', '=', 'inward_outwards.company_id')
            ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
            ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
            ->join('department', 'department.id', '=', 'inward_outwards.department_id')
            ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
            ->leftjoin('users as B', 'B.id', '=', 'inward_outward_users.user_id')
            ->leftjoin('sender', 'sender.id', '=', 'inward_outwards.sender_id')
            ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
            ->join('project', 'project.id', '=', 'inward_outwards.project_id')
            ->where('inward_outwards.prime_user_status','=', 'Assigned')
            ->whereDate('inward_outwards.created_at','>=','2020-06-02')
            ->where('inward_outwards.prime_employee_id', $request_data['user_id'])->count();
        #----------------------
        $support_user_registry_count = Inward_outward_distrubuted_work::join('inward_outward_prime_action','inward_outward_prime_action.id','=','inward_outward_distrubuted_work.inward_outward_prime_action_id')
            ->join('inward_outwards', 'inward_outwards.id','=','inward_outward_prime_action.inward_outward_id')
            ->join('users','users.id','=','inward_outwards.prime_employee_id')
            ->where('inward_outward_distrubuted_work.emp_status','Assigned')
            ->whereDate('inward_outwards.created_at','>=','2020-06-02')
            ->where('inward_outward_distrubuted_work.support_employee_id','=',$request_data['user_id'])
            ->count();

        $support_prime_user_all_count  = $prime_registry_count+$support_user_registry_count;
        #----------------------
        $rejected_support_emp_request_count = Inward_outward_distrubuted_work::join('inward_outward_prime_action','inward_outward_prime_action.id','=','inward_outward_distrubuted_work.inward_outward_prime_action_id')
                        ->join('inward_outwards', 'inward_outwards.id','=','inward_outward_prime_action.inward_outward_id')
                        ->join('users','users.id','=','inward_outward_distrubuted_work.support_employee_id')
                        ->where('inward_outward_prime_action.final_status','Pending')
                        ->where('inward_outward_distrubuted_work.emp_status','Rejected')
                        ->whereDate('inward_outwards.created_at','>=','2020-06-02')
                        ->where('inward_outwards.prime_employee_id',$request_data['user_id'])
                        ->count();
        #---------------------------

        $response_data['inward_registry_count'] = $inward_count;
        $response_data['outward_registry_count'] = $outward_count;
        $response_data['today_inward_registry_count'] = $today_inward_count;
        $response_data['today_outward_registry_count'] = $today_outward_count;
        $response_data['assignee_registry_count'] = $assignee_registry_count;
        $response_data['support_prime_user_combine_count'] = $support_prime_user_all_count;
        $response_data['support_prime_user'] = ['prime_registry_count' => $prime_registry_count,'support_user_registry_count' => $support_user_registry_count ];
        $response_data['rejected_support_emp_request_count'] = $rejected_support_emp_request_count;

        //---------------------------------------------------------------------------------------------------------
        //Cash Approval
        if ($loggedin_user_data[0]->role == config('constants.Admin')) {
            $cash_approval_count = CashApproval::where('first_approval_status', 'Pending')->count();
        } elseif ($loggedin_user_data[0]->role == config('constants.ACCOUNT_ROLE')) {
            $cash_approval_count = CashApproval::where('first_approval_status', 'Approved')
                ->where('second_approval_status', 'Pending')
                ->count();
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $cash_approval_count = CashApproval::where('first_approval_status', 'Approved')
                ->where('second_approval_status', 'Approved')
                ->where('third_approval_status', 'Pending')
                ->count();
        } else {
            $cash_approval_count = 0;
        }
        $response_data['cash_approval_count'] = $cash_approval_count;



        // if ($loggedin_user_data[0]->role == config('constants.ACCOUNT_ROLE')) {
        //     $online_approval_count = OnlinePaymentApproval::where('first_approval_status', 'Pending')->count();
        // } elseif ($loggedin_user_data[0]->role == config('constants.Admin')) {
        //     $online_approval_count = OnlinePaymentApproval::where('first_approval_status', 'Approved')
        //                     ->where('second_approval_status', 'Pending')
        //                     ->count();
        // } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
        //     $online_approval_count = OnlinePaymentApproval::where('first_approval_status', 'Approved')
        //                     ->where('second_approval_status', 'Approved')
        //                     ->where('third_approval_status', 'Pending')
        //                     ->count();
        // } else {
        //     $online_approval_count = 0;
        // }
        // $response_data['online_approval_count'] = $online_approval_count;
        //Nishit 11/02/2020
        if ($loggedin_user_data[0]->role == config('constants.Admin')) {

            $vehicle_maintenance_approval_count = Vehicle_Maintenance::where('first_approval_status', 'Pending')->count();
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {

            $vehicle_maintenance_approval_count = Vehicle_Maintenance::where('first_approval_status', 'Approved')
                ->where('second_approval_status', 'Pending')
                ->count();
        } else {
            $vehicle_maintenance_approval_count = 0;
        }
        $response_data['vehicle_maintenance_approval_count'] = $vehicle_maintenance_approval_count;


        //Nishit 10/02/2020
        if ($loggedin_user_data[0]->role == config('constants.ACCOUNT_ROLE')) {

            $online_payment_approvals_count = OnlinePaymentApproval::where('first_approval_status', 'Pending')->count();
        } elseif ($loggedin_user_data[0]->role == config('constants.Admin')) {

            $online_payment_approvals_count = OnlinePaymentApproval::where('first_approval_status', 'Approved')
                ->where('second_approval_status', 'Pending')
                ->count();
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {

            $online_payment_approvals_count = OnlinePaymentApproval::where('first_approval_status', 'Approved')
                ->where('second_approval_status', 'Approved')
                ->where('third_approval_status', 'Pending')
                ->count();
        } else {

            $online_payment_approvals_count = 0;
        }

        $response_data['online_payment_approvals_count'] = $online_payment_approvals_count;


        /* if ($loggedin_user_data[0]->role == config('constants.ACCOUNT_ROLE')) {
          $budget_sheet_approvals_count = BudgetSheetApproval::where('first_approval_status', 'Pending')->count();
          } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
          $budget_sheet_approvals_count = BudgetSheetApproval::where('first_approval_status', 'Approved')
          ->where('second_approval_status', 'Pending')
          ->count();
          } else {
          $budget_sheet_approvals_count = 0;
          } */
        if ($loggedin_user_data[0]->role == config('constants.Admin')) {
            $budget_sheet_approvals_count = BudgetSheetApproval::where('first_approval_status', 'Pending')->count();
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $budget_sheet_approvals_count = BudgetSheetApproval::where('first_approval_status', 'Approved')->where('second_approval_status', 'Pending')->count();
            //$budget_sheet_approvals_count = 0;
        } else {
            $budget_sheet_approvals_count = 0;
        }
        $response_data['budget_sheet_approvals_count'] = $budget_sheet_approvals_count;

        //hold budget sheet count start
        $hold_budget_count = 0;
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $hold_budget_count = BudgetSheetApproval::where('is_hold', 'Yes')
                            ->where('budget_sheet_approval.status', 'Approved')
                            ->where('budget_sheet_approval.release_hold_amount_status', 'Pending')
                            ->where('budget_sheet_approval.release_amount_first_approval_status', 'Approved')
                            ->where('budget_sheet_approval.release_amount_second_approval_status', 'Pending')
                            ->count();
        }elseif($loggedin_user_data[0]->role == config('constants.Admin')){
            $hold_budget_count = BudgetSheetApproval::where('is_hold', 'Yes')
                            ->where('budget_sheet_approval.status', 'Approved')
                            ->where('budget_sheet_approval.release_hold_amount_status', 'Pending')
                            ->where('budget_sheet_approval.release_amount_first_approval_status', 'Pending')
                            ->count();
        }else{
            $hold_budget_count = 0;
        }
        $response_data['hold_budget_count'] = $hold_budget_count;
        //hold budget sheet count end

        if ($loggedin_user_data[0]->role == config('constants.LETTERHEAD_APPROVE')) {
            $letterhead_approvals_count = ProSignLetter::where('first_approval_status', 'Pending')->count();
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $letterhead_approvals_count = 0;
        } else {
            $letterhead_approvals_count = 0;
        }
        $response_data['letterhead_approvals_count'] = $letterhead_approvals_count;

        if ($loggedin_user_data[0]->role == config('constants.LETTERHEAD_APPROVE')) {
            $pre_sign_approvals_count = PreSignLetter::where('first_approval_status', 'Pending')->count();
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $pre_sign_approvals_count = PreSignLetter::where('first_approval_status', 'Approved')
                ->where('second_approval_status', 'Pending')
                ->count();
        } else {
            $pre_sign_approvals_count = 0;
        }
        $response_data['pre_sign_approvals_count'] = $pre_sign_approvals_count;


        if ($loggedin_user_data[0]->role == config('constants.REAL_HR')) {
            $salary_count = \App\Payroll::where('first_approval_status', 'Pending')
                //->where('month', date('m'))
                //->where('year', date('Y'))
                ->count();
        } elseif ($loggedin_user_data[0]->role == config('constants.ASSISTANT')) {
            $salary_count = \App\Payroll::where('first_approval_status', 'Approved')
                ->where('second_approval_status', 'Pending')
                //->where('month', date('m'))
                //->where('year', date('Y'))
                ->count();
        } elseif ($loggedin_user_data[0]->role == config('constants.Admin')) {
            $salary_count = \App\Payroll::where('first_approval_status', 'Approved')
                ->where('second_approval_status', 'Approved')
                ->where('third_approval_status', 'Pending')
                //->where('month', date('m'))
                //->where('year', date('Y'))
                ->count();
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $salary_count = \App\Payroll::where('first_approval_status', 'Approved')
                ->where('second_approval_status', 'Approved')
                ->where('third_approval_status', 'Approved')
                ->where('fourth_approval_status', 'Pending')
                //->where('month', date('m'))
                //->where('year', date('Y'))
                ->count();
        } elseif ($loggedin_user_data[0]->role == config('constants.ACCOUNT_ROLE')) {
            $salary_count = \App\Payroll::where('first_approval_status', 'Approved')
                ->where('second_approval_status', 'Approved')
                ->where('third_approval_status', 'Approved')
                ->where('fourth_approval_status', 'Approved')
                ->where('fifth_approval_status', 'Pending')
                //->where('month', date('m'))
                //->where('year', date('Y'))
                ->count();
        } else {
            $salary_count = 0;
        }
        $response_data['salary_approval_count'] = $salary_count;

        //==============================  Resignation

        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {

            $resign_list = Resignation::join('users', 'users.id', '=', 'resignation.user_id')
                ->leftJoin('employee', 'employee.user_id', '=', 'users.id')
                ->where(['resignation.first_approval_status' => 'Approved','resignation.second_approval_status' => 'Approved','resignation.status' => 'Pending','resignation.final_approval_status'=>'Pending'])
                ->get('resignation.id')->count();

        } elseif ($loggedin_user_data[0]->role == config('constants.REAL_HR')) {

            $resign_list = Resignation::join('users', 'users.id', '=', 'resignation.user_id')
                ->leftJoin('employee', 'employee.user_id', '=', 'users.id')
                ->where(['resignation.first_approval_status' => 'Approved', 'resignation.second_approval_status' => 'Pending','resignation.status' => 'Pending'])
                ->get('resignation.id')->count();

        } else {

            $resign_list = Resignation::join('users', 'users.id', '=', 'resignation.user_id')
                ->leftJoin('employee', 'employee.user_id', '=', 'users.id')
                ->where(['employee.reporting_user_id' => $request_data['user_id'], 'resignation.first_approval_status' => 'Pending','resignation.status' => 'Pending'])
                ->get('resignation.id')->count();
        }

        $response_data['resignation_count'] = $resign_list;

        //==============================

        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $trip_approval_count = \App\Vehicle_trip::where('trip_type', '=', 'Individual')
                ->where(['status' => 'Pending', 'is_closed' => 'Yes'])
                ->count();
        } else {
            $trip_approval_count = \App\Vehicle_trip::where('trip_user_id', $request_data['user_id'])
                ->where('trip_type', '=', 'User')
                ->where(['status' => 'Pending', 'is_closed' => 'Yes'])
                ->where('user_id', '!=', $request_data['user_id'])
                ->count();
        }
        $response_data['trip_approval_count'] = $trip_approval_count;

        //remote user attendance data
        $response_data['remote_attendance_count'] = \App\AttendanceMaster::join('users', 'users.id', '=', 'attendance_master.user_id')
            ->join('attendance_detail', 'attendance_detail.attendance_master_id', '=', 'attendance_master.id')
            ->where('attendance_master.date', date('Y-m-d'))
            ->where('users.user_attend_type', 'Remote')
            ->groupBy('attendance_detail.attendance_master_id')
            ->count();
        //biometric user attendance data
        $response_data['biometric_attendance_count'] = \App\AttendanceMaster::join('users', 'users.id', '=', 'attendance_master.user_id')
            ->join('attendance_detail', 'attendance_detail.attendance_master_id', '=', 'attendance_master.id')
            ->where('attendance_master.date', date('Y-m-d'))
            ->where('users.user_attend_type', 'Biometric')
            ->groupBy('attendance_detail.attendance_master_id')
            ->count();
        //biometric user attendance data
        $response_data['map_count'] = \App\AttendanceDetail::join('attendance_master', 'attendance_master.id', '=', 'attendance_detail.attendance_master_id')
            ->leftJoin('users', 'users.id', '=', 'attendance_master.user_id')
            ->where('attendance_detail.device_type', 'MOBILE')
            ->whereDate('time', '=', date('Y-m-d'))
            ->orderBy('time', 'DESC')
            ->count();

        //today's leave data
        $response_data['today_leave_count'] = \App\Leaves::where(function ($query) {
            $query->where([['start_date', '<=', date('Y-m-d')], ['end_date', '>=', date('Y-m-d')]]);
        })->where('leave_status', 2)->count();

        //loan count
        if ($loggedin_user_data[0]->role == config('constants.REAL_HR')) {
            $response_data['loan_count'] = EmployeesLoans::where('first_approval_status', '=', 'Pending')
                ->get()
                ->count();
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {

            $response_data['loan_count'] = EmployeesLoans::where('first_approval_status', '=', 'Approved')
                ->where('second_approval_status', '=', 'Pending')
                ->get()
                ->count();
        } elseif ($loggedin_user_data[0]->role == config('constants.ACCOUNT_ROLE')) {
            $response_data['loan_count'] = EmployeesLoans::where('first_approval_status', '=', 'Approved')
                ->where('second_approval_status', '=', 'Approved')
                ->where('third_approval_status', '=', 'Pending')
                ->get()
                ->count();
        } else {
            $response_data['loan_count'] = 0;
        }


        // interview_approval_count      interview_change
        if ($loggedin_user_data[0]->role == config('constants.REAL_HR')) {

            $response_data['interview_approval_count'] = Interview::where('emp_status', '=', 'completed')
            ->where('hr_status', '=', 'Pending')
            ->count();

        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $response_data['interview_approval_count'] = Interview::where('emp_status', '=', 'completed')
            ->where('hr_status', '=', 'Selected')
            ->where('superUser_status', '=', 'Pending')
            ->count();

        } else {
            $response_data['interview_approval_count'] = 0;
        }

        //hold Interview count
        if ($loggedin_user_data[0]->role == config('constants.REAL_HR')) {

            $response_data['hold_interview_count'] = Interview::where('emp_status', '=', 'hold')
            ->where('hr_status', '=', 'Hold')
            ->count();

        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $response_data['hold_interview_count'] = Interview::where('emp_status', '=', 'hold')
            ->where('hr_status', '=', 'Selected')
            ->where('superUser_status', '=', 'Hold')
            ->count();

        } else {
            $response_data['hold_interview_count'] = 0;
        }
        // failed voucher count
        if ($loggedin_user_data[0]->role == config('constants.Admin')
        ) {
            $response_data['failed_voucher_count'] =
            VoucherNumberRegister::select('voucher_number_register.id', 'voucher_number_register.voucher_ref_no', 'company.company_name', 'voucher_number_register.failed_reason', 'voucher_number_register.failed_document', 'voucher_number_register.failed_unique', DB::raw('group_concat(voucher_no) as voucher_numbers'))
                                ->join('company', 'voucher_number_register.company_id', '=', 'company.id')
                                ->where('voucher_number_register.failed_request_status', "Processing")
                                ->WhereNull('voucher_number_register.accountant_status')
                                ->groupBy('failed_unique')
                                ->get()->count();
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $response_data['failed_voucher_count'] = VoucherNumberRegister::select('voucher_number_register.id', 'voucher_number_register.voucher_ref_no', 'company.company_name', 'voucher_number_register.failed_reason', 'voucher_number_register.failed_document', 'voucher_number_register.failed_unique', DB::raw('group_concat(voucher_no) as voucher_numbers'))
                                ->join('company', 'voucher_number_register.company_id', '=', 'company.id')
                                ->where('voucher_number_register.failed_request_status', "Processing")
                                ->where('voucher_number_register.accountant_status', 'Approved')
                                ->WhereNull('voucher_number_register.superadmin_status')
                                ->groupBy('failed_unique')
                                ->get()->count();
        } else {
            $response_data['failed_voucher_count'] = 0;
        }

        // apiUnraedNotificationByUser
        $response_data['unread_notification_count'] = $this->notification_task->getUnreadNotificationByUser($request_data['user_id'])->count();

        // api_entry_approval_count
        $entry_response_data = [];

        $loggedin_user_data = \App\User::where('id', $request_data['user_id'])->get();
        //------------------- Vendor
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $vendor_entry_count = Vendors::where('status', 'Disabled')->where('is_approved',0)->count();
        } else {
            $vendor_entry_count = 0;
        }
        $entry_response_data['vendor_entry_count'] = $vendor_entry_count;
        //-------------------Company
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $company_entry_count = Companies::where('status', 'Disabled')->where('is_approved',0)->count();
        } else {
            $company_entry_count = 0;
        }
        $entry_response_data['company_entry_count'] = $company_entry_count;
        //-------------------Client
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $client_entry_count = Clients::where('status', 'Disabled')->where('is_approved',0)->count();
        } else {
            $client_entry_count = 0;
        }
        $entry_response_data['client_entry_count'] = $client_entry_count;
        //-------------------Project
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $project_entry_count = Projects::where('status', 'Disabled')->where('is_approved',0)->count();
        } else {
            $project_entry_count = 0;
        }
        $entry_response_data['project_entry_count'] = $project_entry_count;
         //-------------------Vendor bank
         if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $vendor_bank_entry_count = Vendors_bank::where('status', 'Disabled')->where('is_approved',0)->count();
        } else {
            $vendor_bank_entry_count = 0;
        }
        $entry_response_data['vendor_bank_entry_count'] = $vendor_bank_entry_count;
         //-------------------Project site
         if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $project_site_entry_count = Project_sites::where('status', 'Disabled')->where('is_approved',0)->count();
        } else {
            $project_site_entry_count = 0;
        }
        $entry_response_data['project_site_entry_count'] = $project_site_entry_count;
        //-------------------Bank
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $bank_entry_count = Banks::where('status', 'Disabled')->where('is_approved',0)->count();
        } else {
            $bank_entry_count = 0;
        }
        $entry_response_data['bank_entry_count'] = $bank_entry_count;
        //-------------------Bank Charge category
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $bank_charge_category_entry_count = Bank_charge_category::where('is_approved',0)->count();
        } else {
            $bank_charge_category_entry_count = 0;
        }
        $entry_response_data['bank_charge_category_entry_count'] = $bank_charge_category_entry_count;
        //-------------------Bank charge sub category
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $bank_charge_sub_category_entry_count = Bank_charge_sub_category::where('is_approved',0)->count();
        } else {
            $bank_charge_sub_category_entry_count = 0;
        }
        $entry_response_data['bank_charge_sub_category_entry_count'] = $bank_charge_sub_category_entry_count;
        //-------------------Payment Card
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $payment_card_entry_count = PaymentCard::where('status', 'Disabled')->where('is_approved',0)->count();
        } else {
            $payment_card_entry_count = 0;
        }
        $entry_response_data['payment_card_entry_count'] = $payment_card_entry_count;
        //-------------------Company Document
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $company_document_entry_count = CompanyDocumentManagement::where('status', 'Disabled')->where('is_approved',0)->count();
        } else {
            $company_document_entry_count = 0;
        }
        $entry_response_data['company_document_entry_count'] = $company_document_entry_count;
        //-------------------Tender Category
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $tender_category_entry_count = TenderCategory::where('status', 'Disabled')->where('is_approved',0)->count();
        } else {
            $tender_category_entry_count = 0;
        }
        $entry_response_data['tender_category_entry_count'] = $tender_category_entry_count;
        //-------------------Tender Pattern
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $tender_pattern_entry_count = TenderPattern::where('status', 'Disabled')->where('is_approved',0)->count();
        } else {
            $tender_pattern_entry_count = 0;
        }
        $entry_response_data['tender_pattern_entry_count'] = $tender_pattern_entry_count;
        //-------------------Tender Physical
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $tender_physical_submission_entry_count = Tender_physical_submission::where('status', 'Disabled')->where('is_approved',0)->count();
        } else {
            $tender_physical_submission_entry_count = 0;
        }
        $entry_response_data['tender_physical_submission_entry_count'] = $tender_physical_submission_entry_count;
        //-------------------Registry category
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $registry_category_entry_count = Inward_outward_doc_category::where('status', 'Disabled')->where('is_approved',0)->count();
        } else {
            $registry_category_entry_count = 0;
        }
        $entry_response_data['registry_category_entry_count'] = $registry_category_entry_count;
        //-------------------Registry sub category
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $registry_sub_category_entry_count = Inward_outward_doc_sub_category::where('status', 'Disabled')->where('is_approved',0)->count();
        } else {
            $registry_sub_category_entry_count = 0;
        }
        $entry_response_data['registry_sub_category_entry_count'] = $registry_sub_category_entry_count;
        //------------------- Delivery mode
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $delivery_mode_entry_count = Inward_outward_delivery_mode::where('status', 'Disabled')->where('is_approved',0)->count();
        } else {
            $delivery_mode_entry_count = 0;
        }
        $entry_response_data['delivery_mode_entry_count'] = $delivery_mode_entry_count;
        //-------------------Sender category
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $sender_category_entry_count = Sender::where('status', 'Disabled')->where('is_approved',0)->count();
        } else {
            $sender_category_entry_count = 0;
        }
        $entry_response_data['sender_category_entry_count'] = $sender_category_entry_count;

        //-------------------TDS Section
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $sender_tds_section_entry_count = TdsSectionType::where('status', 'Disabled')->where('is_approved', 0)->count();
        } else {
            $sender_tds_section_entry_count = 0;
        }
        $entry_response_data['sender_tds_section_entry_count'] = $sender_tds_section_entry_count;

        $response_data['entry_approval_count'] = $entry_response_data;

        // get_upcomings
        $whereCondition = ['user_id' => $request->input('user_id')];

        $birthdays = Employees::whereNOTIn('user_id', [$request->input('user_id')])
                        ->join('users','users.id','=','employee.user_id')
                        ->where('users.is_user_relieved',0)
                        ->whereRaw('DAYOFYEAR(curdate()) <= DAYOFYEAR(birth_date) AND DAYOFYEAR(curdate()) + 7 >=  dayofyear(birth_date)')
                        ->selectRaw('designation,emp_code,user_id, birth_date, birth_date, DATE_FORMAT(birth_date, "%m%d") as order_date, DATE_FORMAT(birth_date, "%m-%d") as monthdate, DATE_FORMAT(birth_date, "%m") as month')
                        ->count();
        
        $marriage = Employees::whereNOTIn('user_id', [$request->input('user_id')])
                        ->join('users','users.id','=','employee.user_id')
                        ->where('users.is_user_relieved',0)
                        ->whereRaw('DAYOFYEAR(curdate()) <= DAYOFYEAR(marriage_date) AND DAYOFYEAR(curdate()) + 7 >=  dayofyear(marriage_date)')
                        ->selectRaw('designation,emp_code,user_id, marriage_date, DATE_FORMAT(marriage_date, "%m%d") as order_date, DATE_FORMAT(marriage_date, "%m-%d") as monthdate, DATE_FORMAT(marriage_date, "%m") as month')
                        ->count();

        $work = Employees::whereNOTIn('user_id', [$request->input('user_id')])
                        ->join('users','users.id','=','employee.user_id')
                        ->where('users.is_user_relieved',0)
                        ->whereRaw('DAYOFYEAR(curdate()) <= DAYOFYEAR(joining_date) AND DAYOFYEAR(curdate()) + 7 >=  dayofyear(joining_date)')
                        ->selectRaw('designation,emp_code,user_id, joining_date, DATE_FORMAT(joining_date, "%m%d") as order_date, DATE_FORMAT(joining_date, "%m-%d") as monthdate, DATE_FORMAT(joining_date, "%m") as month')
                        ->where('joining_date','!=',date('Y-m-d'))
                        ->count();

        $employee = new Employees();
        // $upcoming_days = $employee->getFormetedDate(array_merge($birthdays, $marriage, $work));
        $upcoming_days = $birthdays + $marriage + $work ;
            
        $response_data['get_upcomings_count'] = $upcoming_days;

        // get_upcoming_holiday
        $holiday = Holiday::where('start_date', '>=', date('Y-m-d'))->where('start_date', '<=', date('Y-m-d', strtotime("+7 day")))
                ->orderBy('start_date', 'asc')
                ->count(); 
        $response_data['upcoming_holiday_count'] = $holiday;

        // get_custodian_pending_request
        $userId = $request->get('user_id');

        $userRole = User::where('id', $userId)->get('role')->first();

        $documentRequest = CompanyDocumentRequest::select('company_document_request.*', 'users.name as requester_name','company_document_management.title as document_title','company_document_management.custodian_id as main_custodian_id')
                        ->join('users', 'users.id', '=', 'company_document_request.request_user_id')
                        ->join('company_document_management', 'company_document_management.id', '=', 'company_document_request.document_id')
                        ->where('company_document_request.custodian_id', $userId)
                        ->where('company_document_request.custodian_approval_status', 'Pending')
                        ->with(['get_company_detail','get_custodian_detail'])->orderBy('name')
                        ->count();

        $response_data['custodian_pending_request_count'] = $documentRequest;


        // get_requester_pending_received
        $documentRequestReceived = CompanyDocumentRequest::select('company_document_request.*', 'users.name as requester_name','company_document_management.title as document_title','company_document_management.custodian_id as main_custodian_id')
                        ->join('users', 'users.id', '=', 'company_document_request.request_user_id')
                        ->join('company_document_management', 'company_document_management.id', '=', 'company_document_request.document_id')
                        ->where('company_document_request.request_user_id', $userId)
                        ->where('company_document_request.request_status', 'Approved')
                        ->with(['get_company_detail','get_custodian_detail'])
                        ->count();

        $response_data['requester_pending_received_count'] = $documentRequestReceived;

        // get_requester_pending_returned
        $documentRequestReturned = CompanyDocumentRequest::select('company_document_request.*', 'users.name as requester_name','company_document_management.title as document_title','company_document_management.custodian_id as main_custodian_id')
                        ->join('users', 'users.id', '=', 'company_document_request.request_user_id')
                        ->join('company_document_management', 'company_document_management.id', '=', 'company_document_request.document_id')
                        ->where('company_document_request.request_user_id', $userId)
                        ->where('company_document_request.request_status', 'Submitted')
                        ->with(['get_company_detail','get_custodian_detail'])
                        ->count();
        $response_data['requester_pending_returned_count'] = $documentRequestReturned;

        // get_custodian_pending_received
        $documentRequestReceived = CompanyDocumentRequest::select('company_document_request.*', 'users.name as requester_name','company_document_management.title as document_title','company_document_management.custodian_id as main_custodian_id')
                        ->join('users', 'users.id', '=', 'company_document_request.request_user_id')
                        ->join('company_document_management', 'company_document_management.id', '=', 'company_document_request.document_id')
                        ->where('company_document_request.custodian_id', $userId)
                        ->where('company_document_request.request_status', 'Returned')
                        ->where('company_document_request.custodian_approval_status', 'Approved')
                        ->with(['get_company_detail','get_custodian_detail'])
                        ->count();
        $response_data['custodian_pending_received_count'] = $documentRequestReceived;

        // get_admin_pending_request
        $documentRequestAdmin = CompanyDocumentRequest::select('company_document_request.*', 'users.name as requester_name','company_document_management.title as document_title','company_document_management.custodian_id as main_custodian_id')
                            ->join('users', 'users.id', '=', 'company_document_request.request_user_id')
                            ->join('company_document_management', 'company_document_management.id', '=', 'company_document_request.document_id')
                            ->where('company_document_request.superadmin_status', 'Pending')->count();

        $response_data['admin_pending_request_count'] = $documentRequestAdmin;



        // get_category_app_display
        $loggedin_user_data = User::where('id', $request_data['user_id'])->get(['id', 'name', 'email', 'role']);
        $category_list = \App\App_display_category::where('status', 'Enabled')->get();

        foreach ($category_list as $key => $category) {
            switch ($category->id) {
                case 1:
                    //get user's permission arr
                    $permission_arr = $this->common_task->getPermissionArr($loggedin_user_data[0]->role, 20);
                    if (empty($permission_arr)) {
                        $category_list[$key]->total_count = 0;
                        continue;
                    }
                    if (in_array(1, $permission_arr) && !in_array(5, $permission_arr) && !in_array(6, $permission_arr)) {
                        $category_list[$key]->total_count = 0;
                        continue;
                    }
                    /*if($loggedin_user_data[0]->role==config('constants.REAL_HR')){
                        $category_list[$key]->total_count = 0;
                        continue;
                    }*/

                    $attendance_list_count = AttendanceDetail::join('attendance_master', 'attendance_master.id', '=', 'attendance_detail.attendance_master_id')
                                    ->join('users', 'users.id', '=', 'attendance_master.user_id')
                                    ->leftJoin('employee', 'employee.user_id', '=', 'users.id')
                                    ->where('attendance_detail.is_approved', 'Pending')
                                    ->where(function($query) use ($permission_arr, $request_data) {
                                        if (in_array(6, $permission_arr) && !in_array(5, $permission_arr)) {

                                            $jr_user_list = \App\Employees::where('reporting_user_id', $request_data['user_id'])->get(['user_id']);
                                            if ($jr_user_list->count() == 0) {
                                                return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
                                            }

                                            //fetch all pending attendance list
                                            $query->whereIn('attendance_master.user_id', $jr_user_list->pluck('user_id'));
                                        }
                                    })
                                    ->get(['users.name', 'users.profile_image', 'employee.designation'])->count();
                    
                    if($loggedin_user_data[0]->role!=config('constants.REAL_HR')){
                        $attendance_list_count=0;
                    }
                                    
                    $category_list[$key]->total_count = $attendance_list_count;
                    break;
                case 2:
                    //get permission arr
                    $permission_arr = $this->common_task->getPermissionArr($loggedin_user_data[0]->role, $this->module_id);
                    if (empty($permission_arr)) {
                        $category_list[$key]->total_count = 0;
                        continue;
                    }
                    /* if (in_array(5, $permission_arr) && in_array(2, $permission_arr)) {
                      //have to allow the approval for all user leaves
                      $leave_list_count = Leaves::join('users', 'users.id', '=', 'leaves.user_id')
                      ->leftJoin('employee', 'employee.user_id', '=', 'users.id')
                      ->where(['leave_status' => 1, 'assign_work_status' => 'Accepted'])
                      ->get(['leaves.*', 'users.name', 'users.profile_image', 'employee.designation'])->count();
                      } elseif (in_array(6, $permission_arr) && in_array(2, $permission_arr)) {
                      //have to allow the approval for the leaves in which his name is as notify id
                      $leave_list_count = Leaves::join('users', 'users.id', '=', 'leaves.user_id')
                      ->leftJoin('employee', 'employee.user_id', '=', 'users.id')
                      ->where(['leave_status' => 1, 'assign_work_status' => 'Accepted'])
                      ->whereRaw('FIND_IN_SET(' . $request_data['user_id'] . ',notify_id)')
                      ->get(['leaves.*', 'users.name', 'users.profile_image', 'employee.designation'])->count();
                      } else {
                      $leave_list_count = 0;
                      } */
                    if (in_array(5, $permission_arr) && in_array(2, $permission_arr)) {
                        //have to allow the approval for all user leaves
                        if ($loggedin_user_data[0]->role == config('constants.REAL_HR')) {
                            $leave_list_count = Leaves::join('users', 'users.id', '=', 'leaves.user_id')
                                            ->leftJoin('employee', 'employee.user_id', '=', 'users.id')
                                            ->where(['leave_status' => 1, 'assign_work_status' => 'Accepted'])
                                            ->where('leaves.first_approval_status', 'Pending')
                                            ->get(['leaves.*', 'users.name', 'users.profile_image', 'employee.designation'])->count();
                        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
                            //$leave_list_count=0;
                           $leave_list_count = Leaves::join('users', 'users.id', '=', 'leaves.user_id')
                                            ->leftJoin('employee', 'employee.user_id', '=', 'users.id')
                                            ->where(['leave_status' => 1, 'assign_work_status' => 'Accepted'])
                                            ->where('leaves.first_approval_status', 'Approved')
                                            ->where('leaves.second_approval_status', 'Approved')
                                            ->where('leaves.third_approval_status', 'Pending')
                                            ->get(['leaves.*', 'users.name', 'users.profile_image', 'employee.designation'])->count();
                        } else {
                            $leave_list_count = Leaves::join('users', 'users.id', '=', 'leaves.user_id')
                                            ->leftJoin('employee', 'employee.user_id', '=', 'users.id')
                                            ->where(['leave_status' => 1, 'assign_work_status' => 'Accepted'])
                                            ->where('leaves.first_approval_status', 'Approved')
                                            ->where('leaves.second_approval_status', 'Pending')
                                            ->get(['leaves.*', 'users.name', 'users.profile_image', 'employee.designation'])->count();
                        }
                    } else {
                        $leave_list_count = 0;
                    }

                    $category_list[$key]->total_count = $leave_list_count;
                    break;
                case 3:
                    $leave_work_count = Leaves::join('users', 'users.id', '=', 'leaves.user_id')
                                    ->where(['assign_work_user_id' => $request_data['user_id'], 'assign_work_status' => 'Pending'])
                                    ->orderBy('leaves.created_at', 'DESC')
                                    ->get(['leaves.*', 'users.name', 'users.profile_image', 'users.email'])->count();
                    $category_list[$key]->total_count = $leave_work_count;
                    break;

                case 5:

                    $role_permission = $this->common_task->getPermissionArr($loggedin_user_data[0]->role, $this->module_id);

                    $expense_select = ['employee_expense.id', 'employee_expense.user_id', 'expense_category.category_name', 'employee_expense.expense_category as expense_category_id', 'employee_expense.title',
                        'employee_expense.bill_number', 'employee_expense.merchant_name', 'employee_expense.amount',
                        'employee_expense.expense_date', 'employee_expense.comment',
                        'employee_expense.expense_image', 'employee_expense.status', 'users.name'
                    ];


                    if ($loggedin_user_data[0]->role == config('constants.REAL_HR')) {
                        $expense_list_count = Employee_expense::join('expense_category', 'expense_category.id', '=', 'employee_expense.expense_category')
                                        ->leftJoin('users', 'users.id', '=', 'employee_expense.approved_by')
                                        ->leftJoin('employee', 'employee.user_id', '=', 'employee_expense.user_id')
                                        ->join('company', 'company.id', '=', 'employee_expense.company_id')
                                        ->join('project', 'project.id', '=', 'employee_expense.project_id')
                                        ->where('first_approval_status', 'Pending')
                                        ->get($expense_select)->count();
                    } 
                    elseif ($loggedin_user_data[0]->role == config('constants.ASSISTANT')) {
                        $expense_list_count = Employee_expense::join('expense_category', 'expense_category.id', '=', 'employee_expense.expense_category')
                                        ->leftJoin('users', 'users.id', '=', 'employee_expense.approved_by')
                                        ->leftJoin('employee', 'employee.user_id', '=', 'employee_expense.user_id')
                                        ->join('company', 'company.id', '=', 'employee_expense.company_id')
                                        ->join('project', 'project.id', '=', 'employee_expense.project_id')
                                        ->where('first_approval_status', 'Approved')
                                        ->where('second_approval_status', 'Pending')
                                        ->get($expense_select)->count();
                    }
                    elseif ($loggedin_user_data[0]->role == config('constants.Admin')) {
                        $expense_list_count = Employee_expense::join('expense_category', 'expense_category.id', '=', 'employee_expense.expense_category')
                                        ->leftJoin('users', 'users.id', '=', 'employee_expense.approved_by')
                                        ->leftJoin('employee', 'employee.user_id', '=', 'employee_expense.user_id')
                                        ->join('company', 'company.id', '=', 'employee_expense.company_id')
                                        ->join('project', 'project.id', '=', 'employee_expense.project_id')
                                        ->where('first_approval_status', 'Approved')
                                        ->where('second_approval_status', 'Approved')
                                        ->where('third_approval_status', 'Pending')
                                        ->get($expense_select)->count();
                    }
                    elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
                        $expense_list_count = Employee_expense::join('expense_category', 'expense_category.id', '=', 'employee_expense.expense_category')
                                        ->leftJoin('users', 'users.id', '=', 'employee_expense.approved_by')
                                        ->leftJoin('employee', 'employee.user_id', '=', 'employee_expense.user_id')
                                        ->join('company', 'company.id', '=', 'employee_expense.company_id')
                                        ->join('project', 'project.id', '=', 'employee_expense.project_id')
                                        ->where('first_approval_status', 'Approved')
                                        ->where('second_approval_status', 'Approved')
                                        ->where('third_approval_status', 'Approved')
                                        ->where('forth_approval_status', 'Pending')
                                        ->get($expense_select)->count();
                    } elseif ($loggedin_user_data[0]->role == config('constants.ACCOUNT_ROLE')) {
                        $expense_list_count = Employee_expense::join('expense_category', 'expense_category.id', '=', 'employee_expense.expense_category')
                                        ->leftJoin('users', 'users.id', '=', 'employee_expense.approved_by')
                                        ->leftJoin('employee', 'employee.user_id', '=', 'employee_expense.user_id')
                                        ->join('company', 'company.id', '=', 'employee_expense.company_id')
                                        ->join('project', 'project.id', '=', 'employee_expense.project_id')
                                        ->where('first_approval_status', 'Approved')
                                        ->where('second_approval_status', 'Approved')
                                        ->where('third_approval_status', 'Approved')
                                        ->where('forth_approval_status', 'Approved')
                                        ->where('fifth_approval_status', 'Pending')
                                        ->get($expense_select)->count();
                    } else {
                        $expense_list_count = 0;
                    }

                    $category_list[$key]->total_count = $expense_list_count;
                    break;

                case 6:

                    if ($loggedin_user_data[0]->role == config('constants.ACCOUNT_ROLE')) {
                        $driver_expense_count = Driver_expense::join('users', 'users.id', '=', 'driver_expense.user_id')
                                        ->where('driver_expense.first_approval_status', 'Pending')
                                        ->where('driver_expense.moniter_user_id', $request_data['user_id'])
                                        ->get(['driver_expense.*', 'users.name as driver_name'])->count();
                    } elseif ($loggedin_user_data[0]->role == config('constants.Admin')) {
                        $driver_expense_count = Driver_expense::join('users', 'users.id', '=', 'driver_expense.user_id')
                                        ->where('driver_expense.first_approval_status', 'Approved')
                                        ->where('driver_expense.second_approval_status', 'Pending')
                                        ->get(['driver_expense.*', 'users.name as driver_name'])->count();
                    } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
                        $driver_expense_count = Driver_expense::join('users', 'users.id', '=', 'driver_expense.user_id')
                                        ->where('driver_expense.first_approval_status', 'Approved')
                                        ->where('driver_expense.second_approval_status', 'Approved')
                                        ->where('driver_expense.third_approval_status', 'Pending')
                                        ->get(['driver_expense.*', 'users.name as driver_name'])->count();
                    } else {
                        $driver_expense_count = 0;
                    }

                    $category_list[$key]->total_count = $driver_expense_count;
                    break;

                case 7:
                    if ($loggedin_user_data[0]->role == config('constants.REAL_HR')) {
                        $remote_attend_request_count = RemoteAttendanceRequest::with('places')
                                        ->where('main_approval_status', 'Pending')
                                        ->get(['id', 'user_id', 'place_id', 'reason', 'date', 'main_approval_status', 'first_approval_status', 'first_approval_id', 'reject_reason'])->count();
                    } else {
                        $remote_attend_request_count = 0;
                    }
                    $category_list[$key]->total_count = $remote_attend_request_count;
                    break;
                //--------
                case 8:
               
                $meeting_request_count = \App\Meeting::join('MeetingMOM', 'meeting.id','=','MeetingMOM.meeting_id')
                        ->where('MeetingMOM.meeting_user_id','=',$request_data['user_id'])
                        ->where('MeetingMOM.status','Pending')
                        ->orderBy('meeting.id', 'DESC')
                        ->get()->count();
               
                $category_list[$key]->total_count = $meeting_request_count;
                break;   

                default:
                    break;
            }
        }
        $response_data['category_app_display_count'] = count($category_list);              

        // registry_module_count
        $user_id = $request_data['user_id'];
        $logged_in_userdata = User::where('id', $request_data['user_id'])->get();
        
        $partial_query = Inward_outwards::
                join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
                ->join('company', 'company.id', '=', 'inward_outwards.company_id')
                ->join('project', 'project.id', '=', 'inward_outwards.project_id')
                ->leftJoin('department', 'department.id', '=', 'inward_outwards.department_id')
                ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
                ->leftjoin('sender', 'sender.id', '=', 'inward_outwards.sender_id')
                ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
                ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
                ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
                ->where('inward_outwards.type', '=', 'Inwards')
                ->orderBy('inward_outwards.id', 'DESC')
                ->groupBy('inward_outward_users.inward_outward_id');

            $permission_arr = $this->common_task->getPermissionArr($logged_in_userdata[0]->role, 40); //fetch view permissions of users
                if (in_array(5, $permission_arr)) {
                        $inward_data =  $partial_query->count(); //show all request
                } elseif (in_array(1, $permission_arr)) {
                        $inward_data =  $partial_query
                                ->leftJoin('inward_outward_prime_action','inward_outward_prime_action.inward_outward_id','=','inward_outwards.id')
                                ->leftJoin('inward_outward_distrubuted_work','inward_outward_distrubuted_work.inward_outward_prime_action_id','=','inward_outward_prime_action.id')
                                ->groupBy('inward_outward_distrubuted_work.inward_outward_prime_action_id')
                                ->where(function ($query) use ($user_id) {
                            $query->where('inward_outwards.inserted_by', $user_id)
                                    ->orWhere('inward_outwards.requested_by',$user_id )
                                    ->orWhere('inward_outward_users.user_id', $user_id)
                                    ->orWhere(function ($query) use ($user_id) {
                            $query->Where('inward_outwards.prime_employee_id', $user_id )
                                ->Where('inward_outwards.prime_user_status','Accepted');
                        })->orWhere(function ($query) use ($user_id) {
                            $query->Where('inward_outward_distrubuted_work.support_employee_id', $user_id )
                                ->Where('inward_outward_distrubuted_work.emp_status','Accepted');
                        });
                    })->count();
                } else {
                    $inward_data = 0;
                }
            $inward_count = $inward_data;
          
        #----------------------
        $outward_partial_query = Inward_outwards::
                    join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
                    ->join('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
                    ->join('company', 'company.id', '=', 'inward_outwards.company_id')
                    ->join('project', 'project.id', '=', 'inward_outwards.project_id')
                    ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
                    ->leftJoin('department', 'department.id', '=', 'inward_outwards.department_id')
                    ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
                    ->join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
                    ->where('inward_outwards.type', '=', 'Outwards')
                    ->orderBy('inward_outwards.id', 'DESC')
                    ->groupBy('inward_outward_users.inward_outward_id');

            $permission_arr = $this->common_task->getPermissionArr($logged_in_userdata[0]->role, 40);  //fetch view permissions of users
            
            if (in_array(5, $permission_arr)) {
                    $outward_data =  $outward_partial_query->count(); //show all  request
            } elseif (in_array(1, $permission_arr)) {
                    $outward_data =  $outward_partial_query->where(function ($query) use ($user_id)  {
                        $query->where('inward_outwards.inserted_by', $user_id)
                                ->orWhere('inward_outwards.requested_by', $user_id )
                                ->orWhere('inward_outward_users.user_id', $user_id);
                        })->count();
            } else {
                    $outward_data = 0;
            } 

            $outward_count = $outward_data; 
        $today_date = date('Y-m-d');
        #----------------------
        $today_partial_query = Inward_outwards::
                join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
                ->join('company', 'company.id', '=', 'inward_outwards.company_id')
                ->join('project', 'project.id', '=', 'inward_outwards.project_id')
                ->leftJoin('department', 'department.id', '=', 'inward_outwards.department_id')
                ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
                ->leftjoin('sender', 'sender.id', '=', 'inward_outwards.sender_id')
                ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
                ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
                ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
                ->where('inward_outwards.type', '=', 'Inwards')
                ->whereDate('inward_outwards.created_at', '=', $today_date)
                ->orderBy('inward_outwards.id', 'DESC')
                ->groupBy('inward_outward_users.inward_outward_id');

            $permission_arr = $this->common_task->getPermissionArr($logged_in_userdata[0]->role, 40); //fetch view permissions of users
                if (in_array(5, $permission_arr)) {
                        $today_inward_data =  $today_partial_query->count(); //show all request
                } elseif (in_array(1, $permission_arr)) {
                        $today_inward_data =  $today_partial_query
                                ->leftJoin('inward_outward_prime_action','inward_outward_prime_action.inward_outward_id','=','inward_outwards.id')
                                ->leftJoin('inward_outward_distrubuted_work','inward_outward_distrubuted_work.inward_outward_prime_action_id','=','inward_outward_prime_action.id')
                                ->groupBy('inward_outward_distrubuted_work.inward_outward_prime_action_id')
                                ->where(function ($query) use ($user_id) {
                            $query->where('inward_outwards.inserted_by', $user_id)
                                    ->orWhere('inward_outwards.requested_by',$user_id )
                                    ->orWhere('inward_outward_users.user_id', $user_id)
                                    ->orWhere(function ($query) use ($user_id) {
                            $query->Where('inward_outwards.prime_employee_id', $user_id )
                                ->Where('inward_outwards.prime_user_status','Accepted');
                        })->orWhere(function ($query) use ($user_id) {
                            $query->Where('inward_outward_distrubuted_work.support_employee_id', $user_id )
                                ->Where('inward_outward_distrubuted_work.emp_status','Accepted');
                        });
                    })->count();
                } else {
                    $today_inward_data = 0;
                }
            $today_inward_count = $today_inward_data;
          
        #----------------------
        $today_outward_partial_query = Inward_outwards::
                    join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
                    ->join('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
                    ->join('company', 'company.id', '=', 'inward_outwards.company_id')
                    ->join('project', 'project.id', '=', 'inward_outwards.project_id')
                    ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
                    ->leftJoin('department', 'department.id', '=', 'inward_outwards.department_id')
                    ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
                    ->join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
                    ->where('inward_outwards.type', '=', 'Outwards')
                    ->whereDate('inward_outwards.created_at', '=', $today_date)
                    ->orderBy('inward_outwards.id', 'DESC')
                    ->groupBy('inward_outward_users.inward_outward_id');

            $permission_arr = $this->common_task->getPermissionArr($logged_in_userdata[0]->role, 40);  //fetch view permissions of users
            
            if (in_array(5, $permission_arr)) {
                    $today_outward_data =  $today_outward_partial_query->count(); //show all  request
            } elseif (in_array(1, $permission_arr)) {
                    $today_outward_data =  $today_outward_partial_query->where(function ($query) use ($user_id)  {
                        $query->where('inward_outwards.inserted_by', $user_id)
                                ->orWhere('inward_outwards.requested_by', $user_id )
                                ->orWhere('inward_outward_users.user_id', $user_id);
                        })->count();
            } else {
                    $today_outward_data = 0;
            } 

            $today_outward_count = $today_outward_data; 
        #----------------------
        $assignee_registry_count =  Inward_outwards::join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
            ->join('company', 'company.id', '=', 'inward_outwards.company_id')
            ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
            ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
            ->join('department', 'department.id', '=', 'inward_outwards.department_id')
            ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
            ->leftjoin('sender', 'sender.id', '=', 'inward_outwards.sender_id')
            ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
            ->join('project', 'project.id', '=', 'inward_outwards.project_id')
            ->where('inward_outward_users.status', '=', 'Processing')
            ->whereDate('inward_outwards.created_at','>=','2020-06-02')
            ->where('inward_outward_users.user_id', '=', $request_data['user_id'])
            ->count();
        #---------------------- 
        $prime_registry_count =  Inward_outwards::join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
            ->join('company', 'company.id', '=', 'inward_outwards.company_id')
            ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
            ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
            ->join('department', 'department.id', '=', 'inward_outwards.department_id')
            ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
            ->leftjoin('users as B', 'B.id', '=', 'inward_outward_users.user_id')
            ->leftjoin('sender', 'sender.id', '=', 'inward_outwards.sender_id')
            ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
            ->join('project', 'project.id', '=', 'inward_outwards.project_id')
            ->where('inward_outwards.prime_user_status','=', 'Assigned')
            ->whereDate('inward_outwards.created_at','>=','2020-06-02')
            ->where('inward_outwards.prime_employee_id', $request_data['user_id'])->get()->count();
        #----------------------
        $support_user_registry_count = Inward_outward_distrubuted_work::join('inward_outward_prime_action','inward_outward_prime_action.id','=','inward_outward_distrubuted_work.inward_outward_prime_action_id')
            ->join('inward_outwards', 'inward_outwards.id','=','inward_outward_prime_action.inward_outward_id') 
            ->join('users','users.id','=','inward_outwards.prime_employee_id')
            ->where('inward_outward_distrubuted_work.emp_status','Assigned')
            ->whereDate('inward_outwards.created_at','>=','2020-06-02')
            ->where('inward_outward_distrubuted_work.support_employee_id','=',$request_data['user_id'])
            ->count();

        $support_prime_user_all_count  = $prime_registry_count+$support_user_registry_count;
        #----------------------
        $rejected_support_emp_request_count = Inward_outward_distrubuted_work::join('inward_outward_prime_action','inward_outward_prime_action.id','=','inward_outward_distrubuted_work.inward_outward_prime_action_id')
                        ->join('inward_outwards', 'inward_outwards.id','=','inward_outward_prime_action.inward_outward_id')               
                        ->join('users','users.id','=','inward_outward_distrubuted_work.support_employee_id')
                        ->where('inward_outward_prime_action.final_status','Pending')
                        ->where('inward_outward_distrubuted_work.emp_status','Rejected') 
                        ->whereDate('inward_outwards.created_at','>=','2020-06-02')
                        ->where('inward_outwards.prime_employee_id',$request_data['user_id'])
                        ->count();
        #----------------------Not In use now
        $submit_entries_count = Inward_outward_distrubuted_work::join('inward_outward_prime_action','inward_outward_prime_action.id','=','inward_outward_distrubuted_work.inward_outward_prime_action_id')
            ->join('inward_outwards', 'inward_outwards.id','=','inward_outward_prime_action.inward_outward_id') 
            ->join('users','users.id','=','inward_outward_distrubuted_work.support_employee_id')
            ->where('inward_outward_distrubuted_work.emp_status','Accepted')
            ->where('inward_outward_distrubuted_work.work_status','Submitted')
            ->where('inward_outwards.prime_employee_id',$request_data['user_id'])
            ->count();
        #---------------------------
        $response_data_count = [  
            'inward_registry_count' => $inward_count,
            'outward_registry_count' => $outward_count,
            'today_inward_registry_count' => $today_inward_count,
            'today_outward_registry_count' => $today_outward_count,
            'assignee_registry_count' => $assignee_registry_count,
            'support_prime_user_combine_count' => $support_prime_user_all_count,
            'support_prime_user' => ['prime_registry_count' => $prime_registry_count,'support_user_registry_count' => $support_user_registry_count],
            'rejected_support_emp_request_count' => $rejected_support_emp_request_count
            //'submit_entries_count' => $submit_entries_count
        ];
        $response_data['registry_module_count'] = $response_data_count;
        return response()->json(['status' => true, 'msg' => 'Record found', 'data' => $response_data]);
    }

     ///================================  Interview ===

    public function get_interview_approval_list(Request $request)  //interview_change
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $loggedin_user_data = \App\User::where('id', $request_data['user_id'])->get();

        if ($loggedin_user_data[0]->role == config('constants.REAL_HR')) {

            $interview_approval_list = Interview::with('interview_result')
            ->join('job_openings', 'job_openings.id', '=', 'interview.job_opening_id')
            ->where('interview.emp_status', '=', 'completed')
            ->where('interview.hr_status', '=', 'Pending')
            ->get(['interview.*', 'job_openings.title AS job_title']);

        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $interview_approval_list = Interview::with('interview_result')
            ->join('job_openings', 'job_openings.id', '=', 'interview.job_opening_id')
            ->where('interview.emp_status', '=', 'completed')
            ->where('interview.hr_status', '=', 'Selected')
            ->where('interview.superUser_status', '=', 'Pending')
            ->get(['interview.*', 'job_openings.title AS job_title']);

        } else {
            $interview_approval_list = [];
        }

        if ($interview_approval_list->count() == 0) {
            return response()->json([
                'status' => false,
                'msg' => config('errors.no_record.msg'),
                'data' => [],
                'error' => config('errors.no_record.code')
            ]);
        }

        foreach ($interview_approval_list as $key => $interview) {

            $totalAverage = 0;
            $counter = $totalMarks = 0;

            foreach ($interview->interview_result as $index => $list) {

                $counter++;
                $marks = ($list->experience + $list->knowledge + $list->communication + $list->personality + $list->interpersonal_skill + $list->decision_making + $list->self_confidence + $list->acceptability + $list->commute + $list->suitability)/10;

                $totalMarks += $marks;

                $interview_approval_list[$key]->interview_result[$index]->round_average = round($marks,2).'%';

                if ($list->technical_skill) {

                    $interview_approval_list[$key]->interview_result[$index]->technical_skill = unserialize($list->technical_skill);
                }

                $interviewrs_ids_arr = explode(',', $list->interviewer_ids);

                $interviewrs_list = [];
                foreach ($interviewrs_ids_arr as $k => $id) {

                    $details = User::where('id', $id)->get(['id', 'name', 'profile_image']);

                    if ($details->count() == 0) {
                        continue;
                    }

                    if ($details[0]['profile_image']) {

                        $details[0]['profile_image'] = asset('storage/' . str_replace('public/', '', $details[0]['profile_image']));
                    } else {

                        $stay_userdetails_details[0]['profile_image'] = "";
                    }

                    $interviewrs_list[$k] = $details[0];
                }

                $interview_approval_list[$key]->interview_result[$index]->intervewrs = $interviewrs_list;

            }

            $totalAverage = round(($totalMarks/$counter),2);

            $interview_approval_list[$key]->totalAverage = $totalAverage.'%';
        }

        return response()->json([
            'status' => true,
            'msg' => 'Record found',
            'data' => $interview_approval_list
        ]);
    }

    public function get_hold_interview_list(Request $request)  //20/04/2020
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $loggedin_user_data = \App\User::where('id', $request_data['user_id'])->get();

        if ($loggedin_user_data[0]->role == config('constants.REAL_HR')) {

            $interview_approval_list = Interview::with('interview_result')
            ->join('job_openings', 'job_openings.id', '=', 'interview.job_opening_id')
            ->where('interview.emp_status', '=', 'hold')
            ->where('interview.hr_status', '=', 'Hold')
            ->get(['interview.*', 'job_openings.title AS job_title']);

        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $interview_approval_list = Interview::with('interview_result')
            ->join('job_openings', 'job_openings.id', '=', 'interview.job_opening_id')
            ->where('interview.emp_status', '=', 'hold')
            ->where('interview.hr_status', '=', 'Selected')
            ->where('interview.superUser_status', '=', 'Hold')
            ->get(['interview.*', 'job_openings.title AS job_title']);

        } else {
            $interview_approval_list = [];
        }

        if ($interview_approval_list->count() == 0) {
            return response()->json([
                'status' => false,
                'msg' => config('errors.no_record.msg'),
                'data' => [],
                'error' => config('errors.no_record.code')
            ]);
        }

        foreach ($interview_approval_list as $key => $interview) {

            $totalAverage = 0;
            $counter = $totalMarks = 0;

            foreach ($interview->interview_result as $index => $list) {

                $counter++;
                $marks = ($list->experience + $list->knowledge + $list->communication + $list->personality + $list->interpersonal_skill + $list->decision_making + $list->self_confidence + $list->acceptability + $list->commute + $list->suitability)/10;

                $totalMarks += $marks;

                $interview_approval_list[$key]->interview_result[$index]->round_average = round($marks,2).'%';

                if ($list->technical_skill) {

                    $interview_approval_list[$key]->interview_result[$index]->technical_skill = unserialize($list->technical_skill);
                }

                $interviewrs_ids_arr = explode(',', $list->interviewer_ids);

                $interviewrs_list = [];
                foreach ($interviewrs_ids_arr as $k => $id) {

                    $details = User::where('id', $id)->get(['id', 'name', 'profile_image']);

                    if ($details->count() == 0) {
                        continue;
                    }

                    if ($details[0]['profile_image']) {

                        $details[0]['profile_image'] = asset('storage/' . str_replace('public/', '', $details[0]['profile_image']));
                    } else {

                        $stay_userdetails_details[0]['profile_image'] = "";
                    }

                    $interviewrs_list[$k] = $details[0];
                }

                $interview_approval_list[$key]->interview_result[$index]->intervewrs = $interviewrs_list;

            }

            $totalAverage = round(($totalMarks/$counter),2);

            $interview_approval_list[$key]->totalAverage = $totalAverage.'%';
        }

        $response_data['hold_interviews_list']  = $interview_approval_list;

        return response()->json([
            'status' => true,
            'msg' => 'Record found',
            'data' => $response_data
        ]);
    }

    public function interviewIsOnHold(Request $request)   //20/04/2020
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'id' => 'required',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $loggedin_user_data = \App\User::where('id', $request_data['user_id'])->get();

        $before_hold_status =  Interview::where('id', $request_data['id'])->value('before_hold_status');
        $emp_status =  Interview::where('id', $request_data['id'])->value('prev_status');

        if ($request_data['status'] == 'ContinueBack') {
            $interview_arr = [

                'emp_status' => $emp_status,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip()
            ];

            if ($loggedin_user_data[0]->role  == config('constants.REAL_HR')) {
                $interview_arr['hr_status'] = $before_hold_status;
            }else{
                $interview_arr['superUser_status'] = $before_hold_status;
            }

        } else {

            return response()->json([
                'status' => false,
                'msg' => 'Please Pass Valid Parameters !',
                'data' => []
            ]);
        }

        if(Interview::where('id', $request_data['id'])->update($interview_arr)){

            return response()->json([
                'status' => true,
                'msg' => 'Interviewee details successfully updated.',
                'data' => []
            ]);
        }

        return response()->json([
            'status' => false,
            'msg' => config('errors.sql_operation.msg'),
            'data' => [],
            'error' => config('errors.sql_operation.code')
        ]);

    }

    public function interview_action(Request $request)   //interview_change
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'id' => 'required',
            'emp_status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $loggedin_user_data = \App\User::where('id', $request_data['user_id'])->get();

        //===================  NEW  =======================================
        $userInfo = DB::table('interview')
        ->select('interview.name as candidate','interview.email', 'interview.interviewee_id', 'interview.emp_status', 'job_openings.*')
        ->join('job_openings', 'interview.job_opening_id', '=', 'job_openings.id')
        ->where('interview.id', $request_data['id'])->get()->toArray();

        $mail_data = [];
        $mail_data['to_email'] = $userInfo[0]->email;
        $mail_data['desgination'] = $userInfo[0]->title;
        $mail_data['user_name'] = $userInfo[0]->candidate;


        if ($request_data['emp_status'] == 'selected') {
            if ($loggedin_user_data[0]->role == config('constants.REAL_HR')) {

                $interview_arr = [
                    'hr_status' => 'Selected',
                    'hr_datetime' => date('Y-m-d H:i:s'),
                    'hr_id' => $request_data['user_id'],
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip()
                ];

            }else{

                $interview_arr = [
                    'superUser_status' => 'Selected',
                    'superUser_datetime' => date('Y-m-d H:i:s'),
                    'superUser_id' => $request_data['user_id'],
                    'emp_status' => $request_data['emp_status'],
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip()
                ];

                $this->common_task->interSelectConfirm($mail_data);

            }
        }elseif ($request_data['emp_status'] == 'rejected') {
            if ($loggedin_user_data[0]->role== config('constants.REAL_HR')) {
                $interview_arr = [
                    'hr_status' => 'Rejected',
                    'hr_datetime' => date('Y-m-d H:i:s'),
                    'hr_id' => $request_data['user_id'],
                    'emp_status' => $request_data['emp_status'],
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip()
                ];
            }else{
                $interview_arr = [
                    'superUser_status' => 'Rejected',
                    'superUser_datetime' => date('Y-m-d H:i:s'),
                    'superUser_id' => $request_data['user_id'],
                    'emp_status' => $request_data['emp_status'],
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip()
                ];

            }

            $this->common_task->interRejectConfirm($mail_data);

        }else{
            if ($loggedin_user_data[0]->role == config('constants.REAL_HR')) {
                $interview_arr = [
                    'before_hold_status' => Interview::where('id',$request_data['id'])->value('hr_status'),
                    'prev_status' => Interview::where('id',$request_data['id'])->value('emp_status'),
                    'hr_status' => 'Hold',
                    'hr_datetime' => date('Y-m-d H:i:s'),
                    'hr_id' => $request_data['user_id'],
                    'emp_status' => $request_data['emp_status'],
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip()
                ];

            }else{

                $interview_arr = [
                    'before_hold_status' => Interview::where('id',$request_data['id'])->value('superUser_status'),
                    'prev_status' => Interview::where('id',$request_data['id'])->value('emp_status'),
                    'superUser_status' => 'Hold',
                    'superUser_datetime' => date('Y-m-d H:i:s'),
                    'superUser_id' => $request_data['user_id'],
                    'emp_status' => $request_data['emp_status'],
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip()
                ];

            }

            $this->common_task->interHoldConfirm($mail_data);

        }


        if (Interview::where('id', $request_data['id'])->update($interview_arr)) {

            $sendMailData = DB::table('interview')
                ->select('interview.name as candidate', 'interview.interviewee_id', 'interview.emp_status', 'job_openings.*')
                ->join('job_openings', 'interview.job_opening_id', '=', 'job_openings.id')
                ->where('interview.id', $request_data['id'])->get()->toArray();

            $emailData = Email_format::find(13)->toArray(); // 13 => Send mail to HR after select/reject

            $hrMail_data = [];
            $hrMail_data['emp_status'] = $sendMailData[0]->emp_status;
            $hrMail_data['name'] = $sendMailData[0]->candidate;
            $hrMail_data['candidate_id'] = $sendMailData[0]->interviewee_id;
            $hrMail_data['desgination'] = $sendMailData[0]->title;
            $hrMail_data['job_id'] =  $sendMailData[0]->job_id;
            $hrMail_data['job_role'] = $sendMailData[0]->role;
            $hrMail_data['job_location'] = $sendMailData[0]->location;
            $hrMail_data['job_description'] = $sendMailData[0]->description;

            if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {

                $this->common_task->hrMailAfterApproval($hrMail_data);
            }

           return response()->json([
                'status' => true,
                'msg' => 'Interviewee details successfully updated.',
                'data' => []
            ]);
        }

        return response()->json([
            'status' => false,
            'msg' => config('errors.sql_operation.msg'),
            'data' => [],
            'error' => config('errors.sql_operation.code')
        ]);
    }

    ///================================  Interview ===

    public function approve_emp_loan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();

        //EmployeesLoans::where('id', $request_data['id'])->update(['loan_status' => 'Approved']);
        $logged_in_user_data = User::where('id', $request_data['user_id'])->get();
        $empData = EmployeesLoans::where('id', $request_data['id'])->get(['*'])->toArray();
        if ($logged_in_user_data[0]->role == config('constants.REAL_HR')) {
            $update_arr = [
                'first_approval_status' => "Approved",
                'first_approval_id' => $request_data['user_id'],
                'first_approval_datetime' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip()
            ];

            $first_approval_user = User::where('role', config('constants.SuperUser'))->get(['id'])->pluck('id')->toArray();
            $this->notification_task->LoanApprovalNotify($first_approval_user);
        } elseif ($logged_in_user_data[0]->role == config('constants.SuperUser')) {
            $update_arr = [
                'second_approval_status' => "Approved",
                'second_approval_id' => $request_data['user_id'],
                'second_approval_datetime' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip()
            ];
            $second_approval_user = User::where('role', config('constants.ACCOUNT_ROLE'))->get(['id'])->pluck('id')->toArray();
            $this->notification_task->LoanApprovalNotify($second_approval_user);
        } elseif ($logged_in_user_data[0]->role == config('constants.ACCOUNT_ROLE')) {
            $update_arr = [
                'third_approval_status' => "Approved",
                'third_approval_id' => $request_data['user_id'],
                'third_approval_datetime' => date('Y-m-d H:i:s'),
                'loan_status' => 'Approved',
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip()
            ];
            $User_list = \App\User::where('id', $empData[0]['user_id'])->get(['id'])->pluck('id')->toArray();

            $this->notification_task->approveLoanNotify($User_list);

            $userData = \App\User::where('id', $empData[0]['user_id'])->get()->toArray();

            $mail_data = [
                'user_name' => $userData[0]['name'],
                'loan_amount' => $empData[0]['loan_amount'],
                'loan_term' => $empData[0]['loan_terms'],
                'email' => $userData[0]['email'],
            ];

            $this->common_task->approveLoanEmail($mail_data);
        }

        EmployeesLoans::where('id', $request_data['id'])->update($update_arr);

        return response()->json([
            'status' => true,
            'msg' => 'Loan successfully approved',
            'data' => []
        ]);
    }

    public function reject_emp_loan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'id' => 'required',
            'note' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $logged_in_user_data = User::where('id', $request_data['user_id'])->get();
        $empData = EmployeesLoans::where('id', $request_data['id'])->get(['*'])->toArray();

        $User_list = \App\User::where('id', $empData[0]['user_id'])->get(['id'])->pluck('id')->toArray();

        $this->notification_task->rejectLoanNotify($User_list);

        $userData = \App\User::where('id', $empData[0]['user_id'])->get()->toArray();
        $mail_data = [
            'user_name' => $userData[0]['name'],
            'loan_amount' => $empData[0]['loan_amount'],
            'loan_term' => $empData[0]['loan_terms'],
            'reject_note' => $request_data['note'],
            'email' => $userData[0]['email'],
        ];

        $this->common_task->rejectLoanEmail($mail_data);

        if ($logged_in_user_data[0]->role == config('constants.REAL_HR')) {
            $update_arr = [
                'first_approval_status' => "Rejected",
                'first_approval_id' => $request_data['user_id'],
                'first_approval_datetime' => date('Y-m-d H:i:s'),
                'loan_status' => 'Rejected',
                'reject_note' => $request_data['note'],
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip()
            ];
        } elseif ($logged_in_user_data[0]->role == config('constants.SuperUser')) {
            $update_arr = [
                'second_approval_status' => "Rejected",
                'second_approval_id' => $request_data['user_id'],
                'second_approval_datetime' => date('Y-m-d H:i:s'),
                'loan_status' => 'Rejected',
                'reject_note' => $request_data['note'],
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip()
            ];
        } elseif ($logged_in_user_data[0]->role == config('constants.ACCOUNT_ROLE')) {
            $update_arr = [
                'third_approval_status' => "Rejected",
                'third_approval_id' => $request_data['user_id'],
                'third_approval_datetime' => date('Y-m-d H:i:s'),
                'loan_status' => 'Rejected',
                'reject_note' => $request_data['note'],
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip()
            ];
        }

        EmployeesLoans::where('id', $request_data['id'])->update($update_arr);

        return response()->json([
            'status' => true,
            'msg' => 'Loan successfully rejected',
            'data' => []
        ]);
    }

    public function employee_loan_list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $loggedin_user_data = \App\User::where('id', $request_data['user_id'])->get();

        $select_fields = [
            'employee_loan.id', 'first_user.name as first_user_name', 'second_user.name as second_user_name',
            'third_user.name as third_user_name', 'employee_loan.user_id', 'employee_loan.loan_type', 'employee_loan.loan_amount', 'employee_loan.loan_expected_month', 'employee_loan.loan_terms', 'employee_loan.loan_descption', 'users.name', 'users.profile_image',
            'employee_loan.created_at'
        ];
        if ($loggedin_user_data[0]->role == config('constants.REAL_HR')) {
            $loan_result = EmployeesLoans::join('users', 'employee_loan.user_id', '=', 'users.id')
                ->leftJoin('users as first_user', 'first_user.id', '=', 'employee_loan.first_approval_id')
                ->leftJoin('users as second_user', 'second_user.id', '=', 'employee_loan.second_approval_id')
                ->leftJoin('users as third_user', 'third_user.id', '=', 'employee_loan.third_approval_id')
                ->where('employee_loan.first_approval_status', '=', 'Pending')
                ->get($select_fields);
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $loan_result = EmployeesLoans::join('users', 'employee_loan.user_id', '=', 'users.id')
                ->leftJoin('users as first_user', 'first_user.id', '=', 'employee_loan.first_approval_id')
                ->leftJoin('users as second_user', 'second_user.id', '=', 'employee_loan.second_approval_id')
                ->leftJoin('users as third_user', 'third_user.id', '=', 'employee_loan.third_approval_id')
                ->where('employee_loan.second_approval_status', '=', 'Pending')
                ->where('employee_loan.first_approval_status', '=', 'Approved')
                ->get($select_fields);
        } elseif ($loggedin_user_data[0]->role == config('constants.ACCOUNT_ROLE')) {
            $loan_result = EmployeesLoans::join('users', 'employee_loan.user_id', '=', 'users.id')
                ->leftJoin('users as first_user', 'first_user.id', '=', 'employee_loan.first_approval_id')
                ->leftJoin('users as second_user', 'second_user.id', '=', 'employee_loan.second_approval_id')
                ->leftJoin('users as third_user', 'third_user.id', '=', 'employee_loan.third_approval_id')
                ->where('employee_loan.third_approval_status', '=', 'Pending')
                ->where('employee_loan.second_approval_status', '=', 'Approved')
                ->where('employee_loan.first_approval_status', '=', 'Approved')
                ->get($select_fields);
        }

        if ($loan_result->count() == 0) {
            return response()->json([
                'status' => false,
                'msg' => config('errors.no_record.msg'),
                'data' => [],
                'error' => config('errors.no_record.code')
            ]);
        }


        foreach ($loan_result as $key => $loan) {

            $loan->loan_type = config::get('constants.LOAN_TYPE')[$loan->loan_type];

            if ($loan->profile_image) {

                $loan_result[$key]->profile_image = asset('storage/' . str_replace('public/', '', $loan->profile_image));
            } else {

                $loan_result[$key]->profile_image = "";
            }
        }


        return response()->json([
            'status' => true,
            'msg' => 'Record found',
            'data' => $loan_result
        ]);
    }

    public function trip_approval_list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $loggedin_user_data = \App\User::where('id', $request_data['user_id'])->get();

        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $trip_approval = \App\Vehicle_trip::join('users', 'users.id', '=', 'vehicle_trip.trip_user_id')
                ->join('users as driver_user', 'driver_user.id', '=', 'vehicle_trip.user_id')
                ->join('asset', 'asset.id', '=', 'vehicle_trip.asset_id')
                ->where('vehicle_trip.trip_type', '=', 'Individual')
                ->where(['vehicle_trip.status' => 'Pending', 'vehicle_trip.is_closed' => 'Yes'])
                ->get(['vehicle_trip.*', 'asset.name as vehicle_name', 'users.name as trip_user_name', 'driver_user.name as driver_name', 'driver_user.profile_image as driver_profile_image']);
        } else {
            $trip_approval = \App\Vehicle_trip::join('users', 'users.id', '=', 'vehicle_trip.trip_user_id')
                ->join('asset', 'asset.id', '=', 'vehicle_trip.asset_id')
                ->join('users as driver_user', 'driver_user.id', '=', 'vehicle_trip.user_id')
                ->where('vehicle_trip.trip_user_id', $request_data['user_id'])
                ->where('vehicle_trip.trip_type', '=', 'User')
                ->where(['vehicle_trip.status' => 'Pending', 'vehicle_trip.is_closed' => 'Yes'])
                ->get(['vehicle_trip.*', 'asset.name as vehicle_name', 'users.name as trip_user_name', 'driver_user.name as driver_name', 'driver_user.profile_image as driver_profile_image']);
        }

        foreach ($trip_approval as $key => $trip) {

            if ($trip->driver_profile_image) {
                $trip_approval[$key]->driver_profile_image = asset('storage/' . str_replace('public/', '', $trip->driver_profile_image));
            } else {
                $trip_approval[$key]->driver_profile_image = "";
            }

            if ($trip->opening_meter_reading_image) {
                $trip_approval[$key]->opening_meter_reading_image = asset('storage/' . str_replace('public/', '', $trip->opening_meter_reading_image));
            } else {
                $trip_approval[$key]->opening_meter_reading_image = "";
            }

            if ($trip->closing_meter_reading_image) {
                $trip_approval[$key]->closing_meter_reading_image = asset('storage/' . str_replace('public/', '', $trip->closing_meter_reading_image));
            } else {
                $trip_approval[$key]->closing_meter_reading_image = "";
            }
        }

        $response_data['trip_approval_list'] = $trip_approval;
        return response()->json(['status' => true, 'msg' => 'Record found', 'data' => $response_data]);
    }

    public function approve_trip(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'trip_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];
        $logged_in_user = \App\User::where('id', $request_data['user_id'])->get();
        $update_arr = [
            'status' => 'Approved',
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => $request_data['user_id']
        ];
        \App\Vehicle_trip::where('id', $request_data['trip_id'])->update($update_arr);
        $trip_data = \App\Vehicle_trip::select('user_id', 'trip_user_id', 'trip_type')->where('id', $request_data['trip_id'])->get()->toArray();
        $trip_user_data = \App\User::where('id', $trip_data[0]['user_id'])->get(['name', 'email', 'id']);

        if (intval($trip_data[0]['user_id']) == intval($trip_data[0]['trip_user_id'])) {
            $trip_user_name = "Individual";
        } else {
            $trip_user_name = $logged_in_user[0]->name;
        }

        $mail_data = [
            'driver_name' => $trip_user_data[0]->name,
            'trip_user_name' => $trip_user_name,
            'to_email_list' => [$trip_user_data[0]->email],
        ];
        $this->common_task->tripApproveAlertEmail($mail_data);
        $notify_user_ids = [$trip_user_data[0]->id];
        $this->notification_task->tripApproveAlertNotify($notify_user_ids, $trip_user_data[0]->name, $trip_user_name);

        return response()->json(['status' => true, 'msg' => 'Trip successfully approved.', 'data' => []]);
    }

    public function reject_trip(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'trip_id' => 'required',
            'reject_note' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];
        $logged_in_user = \App\User::where('id', $request_data['user_id'])->get();
        $update_arr = [
            'status' => 'Rejected',
            'reject_note' => $request_data['reject_note'],
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => $request_data['user_id']
        ];
        \App\Vehicle_trip::where('id', $request_data['trip_id'])->update($update_arr);


        $trip_data = \App\Vehicle_trip::select('user_id', 'reject_note', 'trip_user_id')->where('id', $request_data['trip_id'])->get()->toArray();
        //trip user detail
        $trip_user_data = \App\User::where('id', $trip_data[0]['user_id'])->get(['name', 'email', 'id']);

        if (intval($trip_data[0]['user_id']) == intval($trip_data[0]['trip_user_id'])) {
            $trip_user_name = "Individual";
        } else {
            $trip_user_name = $logged_in_user[0]->name;
        }

        $mail_data = [
            'driver_name' => $trip_user_data[0]->name,
            'trip_user_name' => $trip_user_name,
            'to_email_list' => [$trip_user_data[0]->email],
            'reject_note' => $request->input('reject_note'),
        ];
        $this->common_task->tripRejectAlertEmail($mail_data);
        $notify_user_ids = [$this->super_admin->id, $trip_user_data[0]->id];
        $this->notification_task->tripRejectAlertNotify($notify_user_ids, $trip_user_data[0]->name, $trip_user_name, $request->input('reject_note'));

        return response()->json(['status' => true, 'msg' => 'Trip successfully rejected.', 'data' => []]);
    }

    public function salary_approval_list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $loggedin_user_data = \App\User::where('id', $request_data['user_id'])->get();

        if ($loggedin_user_data[0]->role == config('constants.REAL_HR')) {
            $salary_list = \App\Payroll::join('users', 'users.id', '=', 'payroll.user_id')
                ->where('payroll.first_approval_status', 'Pending')
                //->where('payroll.month', date('m'))
                //->where('payroll.year', date('Y'))
                ->orderBy('payroll.id', 'DESC')
                ->get(['payroll.*', 'users.name']);
        } elseif ($loggedin_user_data[0]->role == config('constants.ASSISTANT')) {
            $salary_list = \App\Payroll::join('users', 'users.id', '=', 'payroll.user_id')
                ->where('payroll.first_approval_status', 'Approved')
                ->where('payroll.second_approval_status', 'Pending')
                //->where('payroll.month', date('m'))
                //->where('payroll.year', date('Y'))
                ->orderBy('payroll.id', 'DESC')
                ->get(['payroll.*', 'users.name']);
        } elseif ($loggedin_user_data[0]->role == config('constants.Admin')) {
            $salary_list = \App\Payroll::join('users', 'users.id', '=', 'payroll.user_id')
                ->where('payroll.first_approval_status', 'Approved')
                ->where('payroll.second_approval_status', 'Approved')
                ->where('payroll.third_approval_status', 'Pending')
                //->where('payroll.month', date('m'))
                //->where('payroll.year', date('Y'))
                ->orderBy('payroll.id', 'DESC')
                ->get(['payroll.*', 'users.name']);
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $salary_list = \App\Payroll::join('users', 'users.id', '=', 'payroll.user_id')
                ->where('payroll.first_approval_status', 'Approved')
                ->where('payroll.second_approval_status', 'Approved')
                ->where('payroll.third_approval_status', 'Approved')
                ->where('payroll.fourth_approval_status', 'Pending')
                //->where('payroll.month', date('m'))
                //->where('payroll.year', date('Y'))
                ->orderBy('payroll.id', 'DESC')
                ->get(['payroll.*', 'users.name']);
        } elseif ($loggedin_user_data[0]->role == config('constants.ACCOUNT_ROLE')) {
            $salary_list = \App\Payroll::join('users', 'users.id', '=', 'payroll.user_id')
                ->where('payroll.first_approval_status', 'Approved')
                ->where('payroll.second_approval_status', 'Approved')
                ->where('payroll.third_approval_status', 'Approved')
                ->where('payroll.fourth_approval_status', 'Approved')
                ->where('payroll.fifth_approval_status', 'Pending')
                //->where('payroll.month', date('m'))
                //->where('payroll.year', date('Y'))
                ->orderBy('payroll.id', 'DESC')
                ->get(['payroll.*', 'users.name']);
        } else {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }

        if ($salary_list->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        return response()->json(['status' => true, 'msg' => 'Record found', 'data' => $salary_list]);
    }

    public function approve_salary(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'salary_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $loggedin_user_data = \App\User::where('id', $request_data['user_id'])->get();
        $notify_user = [];
        if ($loggedin_user_data[0]->role == config('constants.REAL_HR')) {
            $update_arr = [
                'first_approval_status' => 'Approved',
                'first_approval_id' => $request_data['user_id'],
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => $request_data['user_id']
            ];
            $notify_user = User::where('role', config('constants.ASSISTANT'))->get(['id'])->pluck('id')->toArray();
        } elseif ($loggedin_user_data[0]->role == config('constants.ASSISTANT')) {
            $update_arr = [
                'second_approval_status' => 'Approved',
                'second_approval_id' => $loggedin_user_data[0]->id,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => $loggedin_user_data[0]->id
            ];
            $notify_user = User::where('role', config('constants.Admin'))->get(['id'])->pluck('id')->toArray();
        } elseif ($loggedin_user_data[0]->role == config('constants.Admin')) {
            $update_arr = [
                'third_approval_status' => 'Approved',
                'third_approval_id' => $loggedin_user_data[0]->id,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => $loggedin_user_data[0]->id
            ];
            $notify_user = User::where('role', config('constants.SuperUser'))->get(['id'])->pluck('id')->toArray();
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $update_arr = [
                'fourth_approval_status' => 'Approved',
                'fourth_approval_id' => $request_data['user_id'],
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => $request_data['user_id']
            ];
            $notify_user = User::where('role', config('constants.ACCOUNT_ROLE'))->get(['id'])->pluck('id')->toArray();
        } elseif ($loggedin_user_data[0]->role == config('constants.ACCOUNT_ROLE')) {
            $update_arr = [
                'fifth_approval_status' => 'Approved',
                'fifth_approval_id' => $request_data['user_id'],
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => $request_data['user_id'],
                'main_approval_status' => 'Approved',
                'is_locked' => 'YES'
            ];
        } else {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }

        if (!empty($notify_user)) {
            $this->notification_task->PayrollApprovalNotify($notify_user);
        }

        \App\Payroll::where('id', $request_data['salary_id'])->update($update_arr);

        return response()->json(['status' => true, 'msg' => "Salary successfully approved.", 'data' => []]);
    }

    //Nishit
    public function approve_all_salary(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'salary_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $loggedin_user_data = \App\User::where('id', $request_data['user_id'])->get();
        $notify_user = [];

        $itemselections  = $request_data['salary_id'];   //salary id array

        $notify_user = [];
        foreach ($itemselections as $id) {

            if ($loggedin_user_data[0]->role == config('constants.REAL_HR')) {
                $update_arr = [
                    'first_approval_status' => 'Approved',
                    'first_approval_id' => $request_data['user_id'],
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                    'updated_by' => $request_data['user_id']
                ];
                $notify_user = User::where('role', config('constants.ASSISTANT'))->get(['id'])->pluck('id')->toArray();

            } elseif ($loggedin_user_data[0]->role == config('constants.ASSISTANT')) {
                $update_arr = [
                    'second_approval_status' => 'Approved',
                    'second_approval_id' => $request_data['user_id'],
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                    'updated_by' => $request_data['user_id']
                ];
                $notify_user = User::where('role', config('constants.Admin'))->get(['id'])->pluck('id')->toArray();
            } elseif ($loggedin_user_data[0]->role == config('constants.Admin')) {
                $update_arr = [
                    'third_approval_status' => 'Approved',
                    'third_approval_id' => $request_data['user_id'],
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                    'updated_by' => $request_data['user_id']
                ];
                $notify_user = User::where('role', config('constants.SuperUser'))->get(['id'])->pluck('id')->toArray();
            } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
                $update_arr = [
                    'fourth_approval_status' => 'Approved',
                    'fourth_approval_id' => $request_data['user_id'],
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                    'updated_by' => $request_data['user_id']
                ];
                $notify_user = User::where('role', config('constants.ACCOUNT_ROLE'))->get(['id'])->pluck('id')->toArray();
            } elseif ($loggedin_user_data[0]->role == config('constants.ACCOUNT_ROLE')) {
                $update_arr = [
                    'fifth_approval_status' => 'Approved',
                    'fifth_approval_id' => $request_data['user_id'],
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                    'updated_by' => $request_data['user_id'],
                    'main_approval_status' => 'Approved',
                    'is_locked' => 'YES'
                ];
            }

            \App\Payroll::where('id', $id)->update($update_arr);
        }
        if (!empty($notify_user)) {

            $this->notification_task->PayrollApprovalNotify($notify_user);
        }

        return response()->json([
            'status' => true,
            'msg' => "Salary successfully approved for all selected record.",
            'data' => []
        ]);
    }

    public function reject_salary(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'salary_id' => 'required',
            'reject_note' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $loggedin_user_data = \App\User::where('id', $request_data['user_id'])->get();

        if ($loggedin_user_data[0]->role == config('constants.REAL_HR')) {
            $update_arr = [
                'first_approval_status' => 'Rejected',
                'first_approval_id' => $request_data['user_id'],
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => $request_data['user_id'],
                'main_approval_status' => 'Rejected',
                'reject_note' => $request_data['reject_note']
            ];
        } elseif ($loggedin_user_data[0]->role == config('constants.ASSISTANT')) {
            $update_arr = [
                'second_approval_status' => 'Rejected',
                'second_approval_id' => $request_data['user_id'],
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => $request_data['user_id'],
                'main_approval_status' => 'Rejected',
                'reject_note' => $request_data['reject_note']
            ];
        } elseif ($loggedin_user_data[0]->role == config('constants.Admin')) {
            $update_arr = [
                'third_approval_status' => 'Rejected',
                'third_approval_id' => $request_data['user_id'],
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => $request_data['user_id'],
                'main_approval_status' => 'Rejected',
                'reject_note' => $request_data['reject_note']
            ];
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $update_arr = [
                'fourth_approval_status' => 'Rejected',
                'fourth_approval_id' => $request_data['user_id'],
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => $request_data['user_id'],
                'main_approval_status' => 'Rejected',
                'reject_note' => $request_data['reject_note']
            ];
        } elseif ($loggedin_user_data[0]->role == config('constants.ACCOUNT_ROLE')) {
            $update_arr = [
                'fifth_approval_status' => 'Rejected',
                'fifth_approval_id' => $request_data['user_id'],
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => $request_data['user_id'],
                'main_approval_status' => 'Rejected',
                'reject_note' => $request_data['reject_note']
            ];
        } else {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }
        \App\Payroll::where('id', $request_data['salary_id'])->update($update_arr);

        return response()->json(['status' => true, 'msg' => "Salary successfully rejected.", 'data' => []]);
    }

    /* public function get_hold_budget_sheet_list(Request $request)  //change
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $loggedin_user_data = \App\User::where('id', $request_data['user_id'])->get();

        $hold_budgetsheet_list = BudgetSheetApproval::join('users', 'users.id', '=', 'budget_sheet_approval.user_id')
            ->join('company', 'company.id', '=', 'budget_sheet_approval.company_id')
            ->join('project', 'project.id', '=', 'budget_sheet_approval.project_id')
            ->join('vendor', 'vendor.id', '=', 'budget_sheet_approval.vendor_id')
            ->join('department', 'department.id', '=', 'budget_sheet_approval.department_id')
            ->leftJoin('clients', 'clients.id', '=', 'budget_sheet_approval.client_id')
                ->leftJoin('project_sites', 'project_sites.id', '=', 'budget_sheet_approval.project_site_id')
            ->with('budgetsheet_file')
            ->where('is_hold', 'Yes')
            ->where('budget_sheet_approval.status', 'Approved')
            ->get(['clients.client_name','clients.location', 'project_sites.site_name',
                'budget_sheet_approval.*', 'users.name', 'users.profile_image', 'company.company_name', 'project.project_name', 'vendor.vendor_name', 'department.dept_name'
            ]);

        if ($hold_budgetsheet_list->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        foreach ($hold_budgetsheet_list as $key => $budgetsheet) {

            if($budgetsheet->client_name){


                if($budgetsheet->client_name == "Other Client"){
                    $hold_budgetsheet_list[$key]->client_name = $budgetsheet->client_name;
                }else{
                    $hold_budgetsheet_list[$key]->client_name = $budgetsheet->client_name. "(" . $budgetsheet->location . ")";
                }

            }


            if ($budgetsheet->profile_image) {
                $hold_budgetsheet_list[$key]->profile_image = asset('storage/' . str_replace('public/', '', $budgetsheet->profile_image));
            } else {
                $hold_budgetsheet_list[$key]->profile_image = "";
            }

            if ($budgetsheet->invoice_file) {
                $hold_budgetsheet_list[$key]->invoice_file = asset('storage/' . str_replace('public/', '', $budgetsheet->invoice_file));
            } else {
                $hold_budgetsheet_list[$key]->invoice_file = "";
            }

            foreach ($budgetsheet->budgetsheet_file as $key1 => $files) {
                if ($files->budget_sheet_file) {
                    $budgetsheet->budgetsheet_file[$key1]['budget_sheet_file'] = asset('storage/' . str_replace('public/', '', $files->budget_sheet_file));
                } else {
                    $budgetsheet->budgetsheet_file[$key1]['budget_sheet_file'] = "";
                }
            }
        }
        return response()->json(['status' => true, 'msg' => "record found.", 'data' => $hold_budgetsheet_list]);
    } */

    /* public function complete_hold_amt(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'id' => 'required',
            'completed_amount' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();

        $buget_data = BudgetSheetApproval::whereId($request_data['id'])->first();
        if($buget_data['payment_status'] == "Pending"){
            $old_release_amount = $buget_data['release_hold_amount'];

            if ($request_data['completed_amount'] != $old_release_amount) {
                $buget_data = BudgetSheetApproval::whereId($request_data['id'])->first();

                $user_data = User::where('id', $buget_data['user_id'])->first();
                $user_email = User::where('id', $buget_data['user_id'])->pluck('email')->toArray();
                $mail_data = [
                    'to_email' => $user_email,
                    'budget_sheet_no' => $buget_data['budhet_sheet_no'],
                    'old_release_hold_amount' => $old_release_amount,
                    'completed_amount' => $request_data['completed_amount'],
                    'name' => $user_data['name'],
                ];
                $this->common_task->budgetSheetReleaseAmountChangeEmail($mail_data);

                $buget_data->release_hold_amount_status = 'Approved';
                $buget_data->save();
            }
        }
        $response_data = [];

        $loggedin_user_data = \App\User::where('id', $request_data['user_id'])->get();

        $hold_arr = [
            'budget_sheet_id' => $request_data['id'],
            'completed_amount' => $request_data['completed_amount'],
            'note' => $request_data['note'],
            'created_at' => date('Y-m-d H:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => $request_data['user_id']
        ];
        Hold_budget_sheet::insert($hold_arr);

        //check if value is still hold
        $completed_amt = Hold_budget_sheet::where('budget_sheet_id', $request_data['id'])->get(['completed_amount'])->sum('completed_amount');
        $hold_detail = BudgetSheetApproval::where('id', $request_data['id'])->get(['hold_amount', 'meeting_number', 'user_id', 'remain_hold_amount']);

        $update_arr = [
            'remain_hold_amount' => $hold_detail[0]->remain_hold_amount - $request_data['completed_amount'],
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => $request_data['user_id']
        ];

        if ($completed_amt >= $hold_detail[0]->hold_amount) {
            $update_arr['is_hold'] = 'No';
            BudgetSheetApproval::where('id', $request_data['id'])->update($update_arr);
        } else {
            BudgetSheetApproval::where('id', $request_data['id'])->update($update_arr);
        }
        $this->notification_task->holdAmountReleaseNotify([$hold_detail[0]->user_id], $hold_detail[0]->meeting_number);
        return response()->json(['status' => true, 'msg' => "Hold amount successfully updated.", 'data' => []]);
    } */

    //07/09/2020 alpesh
    public function complete_hold_amt(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'id' => 'required',
            'completed_amount' => 'required',
            'approval_status' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $update_arr = [];
        $loggedin_user_data = \App\User::where('id', $request_data['user_id'])->get();
        //check if value is still hold
        $completed_amt = Hold_budget_sheet::where('budget_sheet_id', $request_data['id'])->get(['completed_amount'])->sum('completed_amount');
        $hold_detail = BudgetSheetApproval::where('id', $request_data['id'])->get(['hold_amount', 'meeting_number', 'user_id', 'remain_hold_amount']);
        if ($loggedin_user_data[0]->role == config('constants.Admin')) {
            if ($request_data['approval_status'] == "Rejected") {
                $update_arr['release_amount_first_approval_status'] = $request_data['approval_status'];
                $update_arr['release_amount_first_reject_note'] = $request_data['note'];
                $update_arr['release_amount_first_approval_id'] = $loggedin_user_data[0]->id;
            } else {
                $update_arr['release_amount_first_approval_status'] = $request_data['approval_status'];
                $update_arr['release_amount_first_reject_note'] = $request_data['note'];
                $update_arr['release_amount_first_approval_id'] = $loggedin_user_data[0]->id;
                $update_arr['release_hold_amount'] = $request_data['completed_amount'];
            }
        } else if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            if ($request_data['approval_status'] == "Rejected") {
                $update_arr['release_amount_second_approval_status'] = $request_data['approval_status'];
                $update_arr['release_amount_second_reject_note'] = $request_data['note'];
                $update_arr['release_amount_second_approval_id'] = $loggedin_user_data[0]->id;
                //Reject Email
            } else {
                $update_arr['release_amount_second_approval_status'] = $request_data['approval_status'];
                $update_arr['remain_hold_amount'] = $hold_detail[0]->remain_hold_amount - $request_data['completed_amount'];
                $update_arr['release_hold_amount'] = $request_data['completed_amount'];
                $update_arr['release_hold_amount_status'] = 'Approved';
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
                    'updated_by' => $loggedin_user_data[0]->id
                ];
                Hold_budget_sheet::insert($hold_arr);
            }
        }else{
            return response()->json(['status' => false, 'msg' => "", 'data' => [], 'error' => ""]);
        }
        $update_arr['updated_at'] = date('Y-m-d H:i:s');
        $update_arr['updated_ip'] = $request->ip();
        $update_arr['updated_by'] = $loggedin_user_data[0]->id;
        // dd($update_arr);
        BudgetSheetApproval::where('id', $request_data['id'])->update($update_arr);
        $buget_data = BudgetSheetApproval::whereId($request_data['id'])->first();
        $user_data = User::where('id', $buget_data['user_id'])->first();
        $user_email = User::where('id', $buget_data['user_id'])->pluck('email')->toArray();
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            if ($request_data['approval_status'] == "Approved") {
                if ($request_data['completed_amount'] != $buget_data['release_hold_amount']) {
                    $mail_data = [
                        'to_email' => $user_email,
                        'budget_sheet_no' => $buget_data['budhet_sheet_no'],
                        'old_release_hold_amount' => $buget_data['release_hold_amount'],
                        'completed_amount' => $request_data['completed_amount'],
                        'name' => $user_data['name'],
                    ];
                    $this->common_task->budgetSheetReleaseAmountChangeEmail($mail_data);
                }
                $this->notification_task->holdAmountReleaseNotify([$hold_detail[0]->user_id], $hold_detail[0]->meeting_number);
            }
        }
        // Reject Email
        if ($request_data['approval_status'] == "Rejected") {
            if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
                $reject_user = "Super Admin";
            } else {
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

        if ($request_data['approval_status'] == "Rejected"){
            // User Action Log
            $budhet_sheet_no = BudgetSheetApproval::where('id', $request_data['id'])->value('budhet_sheet_no');
            $action_data = [
                'user_id' => $request_data['user_id'],
                'task_body' => $budhet_sheet_no . " budget sheet number hold amount request rejected",
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);
        }else{
            // User Action Log
            $budhet_sheet_no = BudgetSheetApproval::where('id', $request_data['id'])->value('budhet_sheet_no');
            $action_data = [
                'user_id' => $request_data['user_id'],
                'task_body' => $budhet_sheet_no . " budget sheet number hold amount request approved",
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);
        }
        return response()->json(['status' => true, 'msg' => "Hold amount successfully updated.", 'data' => []]);
    }

    //07/09/2020
    public function get_hold_budget_sheet_list(Request $request)  //change
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $response_data = [];
        $loggedin_user_data = \App\User::where('id', $request_data['user_id'])->get();
        if($loggedin_user_data[0]->role == config('constants.SuperUser')){
            $hold_budgetsheet_list = BudgetSheetApproval::join('users', 'users.id', '=', 'budget_sheet_approval.user_id')
            ->join('company', 'company.id', '=', 'budget_sheet_approval.company_id')
            ->join('project', 'project.id', '=', 'budget_sheet_approval.project_id')
            ->join('vendor', 'vendor.id', '=', 'budget_sheet_approval.vendor_id')
            ->join('department', 'department.id', '=', 'budget_sheet_approval.department_id')
            ->leftJoin('clients', 'clients.id', '=', 'budget_sheet_approval.client_id')
                ->leftJoin('project_sites', 'project_sites.id', '=', 'budget_sheet_approval.project_site_id')
            ->with('budgetsheet_file')
            ->where('is_hold', 'Yes')
            ->where('budget_sheet_approval.status', 'Approved')
            ->where('budget_sheet_approval.release_hold_amount_status', 'Pending')
            ->where('budget_sheet_approval.release_amount_first_approval_status', 'Approved')
            ->where('budget_sheet_approval.release_amount_second_approval_status', 'Pending')
            ->get(['clients.client_name','clients.location', 'project_sites.site_name',
                'budget_sheet_approval.*', 'users.name', 'users.profile_image', 'company.company_name', 'project.project_name', 'vendor.vendor_name', 'department.dept_name'
            ]);

        }else if($loggedin_user_data[0]->role == config('constants.Admin')){
            $hold_budgetsheet_list = BudgetSheetApproval::join('users', 'users.id', '=', 'budget_sheet_approval.user_id')
            ->join('company', 'company.id', '=', 'budget_sheet_approval.company_id')
            ->join('project', 'project.id', '=', 'budget_sheet_approval.project_id')
            ->join('vendor', 'vendor.id', '=', 'budget_sheet_approval.vendor_id')
            ->join('department', 'department.id', '=', 'budget_sheet_approval.department_id')
            ->leftJoin('clients', 'clients.id', '=', 'budget_sheet_approval.client_id')
                ->leftJoin('project_sites', 'project_sites.id', '=', 'budget_sheet_approval.project_site_id')
            ->with('budgetsheet_file')
            ->where('is_hold', 'Yes')
            ->where('budget_sheet_approval.status', 'Approved')
            ->where('budget_sheet_approval.release_amount_first_approval_status', 'Pending')
            ->get(['clients.client_name','clients.location', 'project_sites.site_name',
                'budget_sheet_approval.*', 'users.name', 'users.profile_image', 'company.company_name', 'project.project_name', 'vendor.vendor_name', 'department.dept_name'
            ]);
        }else{
            $hold_budgetsheet_list = [];
        }
        if ($hold_budgetsheet_list->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }
        foreach ($hold_budgetsheet_list as $key => $budgetsheet) {
            if($budgetsheet->client_name){
                if($budgetsheet->client_name == "Other Client"){
                    $hold_budgetsheet_list[$key]->client_name = $budgetsheet->client_name;
                }else{
                    $hold_budgetsheet_list[$key]->client_name = $budgetsheet->client_name. "(" . $budgetsheet->location . ")";
                }
            }
            if ($budgetsheet->profile_image) {
                $hold_budgetsheet_list[$key]->profile_image = asset('storage/' . str_replace('public/', '', $budgetsheet->profile_image));
            } else {
                $hold_budgetsheet_list[$key]->profile_image = "";
            }
            if ($budgetsheet->invoice_file) {
                $hold_budgetsheet_list[$key]->invoice_file = asset('storage/' . str_replace('public/', '', $budgetsheet->invoice_file));
            } else {
                $hold_budgetsheet_list[$key]->invoice_file = "";
            }
            foreach ($budgetsheet->budgetsheet_file as $key1 => $files) {
                if ($files->budget_sheet_file) {
                    $budgetsheet->budgetsheet_file[$key1]['budget_sheet_file'] = asset('storage/' . str_replace('public/', '', $files->budget_sheet_file));
                } else {
                    $budgetsheet->budgetsheet_file[$key1]['budget_sheet_file'] = "";
                }
            }
        }
        return response()->json(['status' => true, 'msg' => "record found.", 'data' => $hold_budgetsheet_list]);
    }

    //Nishit..

    public function get_Onlinepayment_approval_list(Request $request)  //nishit 04/08
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $response_data = [];
        $loggedin_user_data = User::where('id', $request_data['user_id'])->get(['users.id', 'users.name', 'users.role']);
        //check if accountant or superadmin based on that return data
        if ($loggedin_user_data[0]->role == config('constants.ACCOUNT_ROLE')) {
            $online_payment_approvals = OnlinePaymentApproval::with('paymentFiles')
                ->join('users', 'online_payment_approval.user_id', '=', 'users.id')
                ->leftJoin('users as first_user', 'first_user.id', '=', 'online_payment_approval.first_approval_id')
                ->leftJoin('users as second_user', 'second_user.id', '=', 'online_payment_approval.second_approval_id')
                ->leftJoin('users as third_user', 'third_user.id', '=', 'online_payment_approval.third_approval_id')
                ->leftJoin('budget_sheet_approval', 'budget_sheet_approval.id', '=', 'online_payment_approval.budget_sheet_id')
                ->join('company', 'company.id', '=', 'online_payment_approval.company_id')
                ->join('project', 'project.id', '=', 'online_payment_approval.project_id')
                ->join('vendor', 'vendor.id', '=', 'online_payment_approval.vendor_id')
                ->leftJoin('bank', 'bank.id', '=', 'online_payment_approval.bank_id')
                ->leftJoin('clients', 'clients.id', '=', 'online_payment_approval.client_id')
                ->leftJoin('project_sites', 'project_sites.id', '=', 'online_payment_approval.project_site_id')
                ->leftJoin('vendors_bank', 'vendors_bank.id', '=', 'online_payment_approval.bank_details')
                ->leftJoin('payment_card', 'payment_card.id', '=', 'online_payment_approval.transaction_id')
                ->leftjoin('rtgs_register','rtgs_register.id','=','online_payment_approval.rtgs_number')
                ->leftjoin('tds_section_type', 'tds_section_type.id', '=', 'online_payment_approval.section_type_id')
                ->where('online_payment_approval.first_approval_status', 'Pending')
                ->get(['clients.client_name','clients.location', 'project_sites.site_name','rtgs_register.rtgs_no',
                    'online_payment_approval.*','budget_sheet_approval.budhet_sheet_no',
                    'payment_card.card_type', 'payment_card.name_on_card', 'payment_card.card_number',
                    'vendor.vendor_name', 'bank.bank_name', 'vendors_bank.bank_name AS vendor_bank_name',
                    'company.company_name', 'project.project_name', 'users.name AS user_name',
                    'users.profile_image', 'first_user.name as first_user_name', 'second_user.name as second_user_name', 'third_user.name as third_user_name', 'tds_section_type.section_type'
                ]);
        } elseif ($loggedin_user_data[0]->role == config('constants.Admin')) {
            $online_payment_approvals = OnlinePaymentApproval::with('paymentFiles')
                ->join('users', 'online_payment_approval.user_id', '=', 'users.id')
                ->leftJoin('users as first_user', 'first_user.id', '=', 'online_payment_approval.first_approval_id')
                ->leftJoin('users as second_user', 'second_user.id', '=', 'online_payment_approval.second_approval_id')
                ->leftJoin('users as third_user', 'third_user.id', '=', 'online_payment_approval.third_approval_id')
                ->leftJoin('budget_sheet_approval', 'budget_sheet_approval.id', '=', 'online_payment_approval.budget_sheet_id')
                ->join('company', 'company.id', '=', 'online_payment_approval.company_id')
                ->join('project', 'project.id', '=', 'online_payment_approval.project_id')
                ->join('vendor', 'vendor.id', '=', 'online_payment_approval.vendor_id')
                ->leftJoin('bank', 'bank.id', '=', 'online_payment_approval.bank_id')
                 ->leftjoin('rtgs_register','rtgs_register.id','=','online_payment_approval.rtgs_number')
                ->leftJoin('clients', 'clients.id', '=', 'online_payment_approval.client_id')
                ->leftJoin('project_sites', 'project_sites.id', '=', 'online_payment_approval.project_site_id')
                ->leftJoin('vendors_bank', 'vendors_bank.id', '=', 'online_payment_approval.bank_details')
                ->leftJoin('payment_card', 'payment_card.id', '=', 'online_payment_approval.transaction_id')
                ->leftjoin('tds_section_type', 'tds_section_type.id', '=', 'online_payment_approval.section_type_id')
                ->where('online_payment_approval.first_approval_status', 'Approved')
                ->where('online_payment_approval.second_approval_status', 'Pending')
                ->get(['clients.client_name','clients.location', 'project_sites.site_name','rtgs_register.rtgs_no',
                    'online_payment_approval.*','budget_sheet_approval.budhet_sheet_no',
                    'payment_card.card_type', 'payment_card.name_on_card', 'payment_card.card_number',
                    'vendor.vendor_name', 'bank.bank_name', 'vendors_bank.bank_name AS vendor_bank_name',
                    'company.company_name', 'project.project_name', 'users.name AS user_name',
                    'users.profile_image', 'first_user.name as first_user_name', 'second_user.name as second_user_name',
                'third_user.name as third_user_name', 'tds_section_type.section_type'
                ]);
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $online_payment_approvals = OnlinePaymentApproval::with('paymentFiles')
                ->join('users', 'online_payment_approval.user_id', '=', 'users.id')
                ->leftJoin('users as first_user', 'first_user.id', '=', 'online_payment_approval.first_approval_id')
                ->leftJoin('users as second_user', 'second_user.id', '=', 'online_payment_approval.second_approval_id')
                ->leftJoin('users as third_user', 'third_user.id', '=', 'online_payment_approval.third_approval_id')
                ->leftJoin('budget_sheet_approval', 'budget_sheet_approval.id', '=', 'online_payment_approval.budget_sheet_id')
                ->join('company', 'company.id', '=', 'online_payment_approval.company_id')
                ->join('project', 'project.id', '=', 'online_payment_approval.project_id')
                ->join('vendor', 'vendor.id', '=', 'online_payment_approval.vendor_id')
                ->leftJoin('bank', 'bank.id', '=', 'online_payment_approval.bank_id')
                 ->leftjoin('rtgs_register','rtgs_register.id','=','online_payment_approval.rtgs_number')
                ->leftJoin('clients', 'clients.id', '=', 'online_payment_approval.client_id')
                ->leftJoin('project_sites', 'project_sites.id', '=', 'online_payment_approval.project_site_id')
                ->leftJoin('vendors_bank', 'vendors_bank.id', '=', 'online_payment_approval.bank_details')
                ->leftJoin('payment_card', 'payment_card.id', '=', 'online_payment_approval.transaction_id')
                ->leftjoin('tds_section_type', 'tds_section_type.id', '=', 'online_payment_approval.section_type_id')
                ->where('online_payment_approval.first_approval_status', 'Approved')
                ->where('online_payment_approval.second_approval_status', 'Approved')
                ->where('online_payment_approval.third_approval_status', 'Pending')
                ->get(['clients.client_name','clients.location', 'project_sites.site_name','rtgs_register.rtgs_no',
                    'online_payment_approval.*','budget_sheet_approval.budhet_sheet_no',
                    'payment_card.card_type', 'payment_card.name_on_card', 'payment_card.card_number',
                    'vendor.vendor_name', 'bank.bank_name', 'vendors_bank.bank_name AS vendor_bank_name',
                    'company.company_name', 'project.project_name', 'users.name AS user_name',
                    'users.profile_image', 'first_user.name as first_user_name', 'second_user.name as second_user_name',
                'third_user.name as third_user_name', 'tds_section_type.section_type'
                ]);
        } else {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }
        if ($online_payment_approvals->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }
        foreach ($online_payment_approvals as $key => $online) {
            if($online->client_name == "Other Client"){
                $online_payment_approvals[$key]->client_name = $online->client_name;
            }else{
                $online_payment_approvals[$key]->client_name = $online->client_name. "(" . $online->location . ")";
            }
            if ($online->profile_image) {
                $online_payment_approvals[$key]->profile_image = asset('storage/' . str_replace('public/', '', $online->profile_image));
            } else {
                $online_payment_approvals[$key]->profile_image = "";
            }
            if ($online->invoice_file) {
                $online_payment_approvals[$key]->invoice_file = asset('storage/' . str_replace('public/', '', $online->invoice_file));
            } else {
                $online_payment_approvals[$key]->invoice_file = "";
            }
            foreach ($online->paymentFiles as $k => $file) {
                if ($file->online_payment_file) {
                    $online_payment_approvals[$key]->paymentFiles[$k]->online_payment_file = asset('storage/' . str_replace('public/', '', $file->online_payment_file));
                } else {
                    $online_payment_approvals[$key]->paymentFiles[$k]->online_payment_file = "";
                }
            }
        }
        return response()->json(['status' => true, 'msg' => 'Records found', 'data' => $online_payment_approvals]);
    }

    //Nishit..

    public function approve_online_payment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'online_paymentid' => 'required',
            //'approve_note'=>'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $loggedin_user_data = User::where('id', $request_data['user_id'])->get(['users.id', 'users.name', 'users.role']);

        if ($loggedin_user_data[0]->role == config('constants.ACCOUNT_ROLE')) {

            //first approval
            $update_arr = [
                'first_approval_status' => 'Approved',
                'first_approval_id' => $request_data['user_id'],
                'first_approval_date_time' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
            ];

            if (isset($request_data['approve_note'])) {
                $update_arr['first_approval_remark'] = $request_data['approve_note'];
            }

            OnlinePaymentApproval::where('id', $request_data['online_paymentid'])->update($update_arr);
            $admin_user = User::where('role', config('constants.Admin'))->get(['id']);

            $this->notification_task->onlinePaymentFirstApprovalNotify([$admin_user[0]->id]);
        } elseif ($loggedin_user_data[0]->role == config('constants.Admin')) {

            //second  approval
            $update_arr = [
                'second_approval_status' => 'Approved',
                'second_approval_id' => $request_data['user_id'],
                'second_approval_date_time' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
            ];


            if (isset($request_data['approve_note'])) {
                $update_arr['second_approval_remark'] = $request_data['approve_note'];
            }

            OnlinePaymentApproval::where('id', $request_data['online_paymentid'])->update($update_arr);;

            $this->notification_task->onlinePaymentSecondApprovalNotify([$this->super_admin->id]);
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {


            $bankApprovealData = OnlinePaymentApproval::select('users.name', 'online_payment_approval.amount', 'users.email', 'users.id as user_id')
                ->join('users', 'online_payment_approval.user_id', '=', 'users.id')
                ->where('online_payment_approval.id', $request_data['online_paymentid'])->get();

            $data = [
                'username' => $bankApprovealData[0]['name'],
                'amount' => $bankApprovealData[0]['amount'],
                'email' => $bankApprovealData[0]['email'],
                'status' => 'Approved',
            ];

            $this->common_task->approveRejectOnlinePaymentEmail($data);
            //send notification to user who requested about approval
            $this->notification_task->onlinePaymentThirdApprovalNotify([$bankApprovealData[0]->user_id]);


            //final approval
            $update_arr = [
                'third_approval_status' => 'Approved',
                'third_approval_id' => $request_data['user_id'],
                'third_approval_date_time' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'status' => 'Approved'
            ];


            if (isset($request_data['approve_note'])) {
                $update_arr['third_approval_remark'] = $request_data['approve_note'];
            }

            OnlinePaymentApproval::where('id', $request_data['online_paymentid'])->update($update_arr);
        } else {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }

        // User Action Log
        $entry_code = OnlinePaymentApproval::where('id', $request_data['online_paymentid'])->value('entry_code');
        $amount = OnlinePaymentApproval::where('id', $request_data['online_paymentid'])->value('amount');
        $action_data = [
            'user_id' => $request_data['user_id'],
            'task_body' => "Online Payment entry code ".$entry_code." approved <br>Amount: ".$amount,
            'created_ip' => $request->ip(),
        ];
        $this->user_action_logs->action($action_data);

        return response()->json(['status' => true, 'msg' => "Online payment successfully approved.", 'data' => []]);
    }

    //Nishit

    public function reject_online_payment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'online_payment_id' => 'required',
            'reject_note' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $loggedin_user_data = User::where('id', $request_data['user_id'])->get(['users.id', 'users.name', 'users.role']);
        //$bank_payment_detail = BankPaymentApproval::where('id', $request_data['bankpayment_id'])->get();


        if ($loggedin_user_data[0]->role == config('constants.ACCOUNT_ROLE')) {
            //first approval
            $bankApprovealData = OnlinePaymentApproval::select('users.name', 'online_payment_approval.amount', 'users.email', 'users.id as user_id')
                ->join('users', 'online_payment_approval.user_id', '=', 'users.id')
                ->where('online_payment_approval.id', $request_data['online_payment_id'])->get()->toArray();

            $data = [
                'username' => $bankApprovealData[0]['name'],
                'amount' => $bankApprovealData[0]['amount'],
                'email' => $bankApprovealData[0]['email'],
                'status' => 'Rejected',
            ];

            $this->common_task->approveRejectOnlinePaymentEmail($data);
            //send notification about rejected
            $this->notification_task->onlinePaymentRejectNotify([$bankApprovealData[0]['user_id']]);

            $updateData = ['reject_note' => $request_data['reject_note'], 'first_approval_status' => 'Rejected', 'first_approval_id' => $request_data['user_id'], 'status' => 'Rejected', 'first_approval_date_time' => date('Y-m-d H:i:s')];  //this..

            OnlinePaymentApproval::where('id', $request_data['online_payment_id'])->update($updateData);
        } elseif ($loggedin_user_data[0]->role == config('constants.Admin')) {
            //second and  approval
            $bankApprovealData = OnlinePaymentApproval::select('users.name', 'online_payment_approval.amount', 'users.id as user_id', 'users.email', 'users.id as user_id')
                ->join('users', 'online_payment_approval.user_id', '=', 'users.id')
                ->where('online_payment_approval.id', $request_data['online_payment_id'])->get()->toArray();

            $data = [
                'username' => $bankApprovealData[0]['name'],
                'amount' => $bankApprovealData[0]['amount'],
                'email' => $bankApprovealData[0]['email'],
                'status' => 'Rejected',
            ];

            $this->common_task->approveRejectOnlinePaymentEmail($data);

            //send notification to user who requested about approval
            $this->notification_task->onlinePaymentRejectNotify([$bankApprovealData[0]['user_id']]);

            $updateData = ['reject_note' => $request_data['reject_note'], 'second_approval_status' => 'Rejected', 'second_approval_id' => $request_data['user_id'], 'status' => 'Rejected', 'second_approval_date_time' => date('Y-m-d H:i:s')];
            OnlinePaymentApproval::where('id', $request_data['online_payment_id'])->update($updateData);
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {


            // final approval

            $bankApprovealData = OnlinePaymentApproval::select('users.name', 'online_payment_approval.amount', 'users.id as user_id', 'users.email', 'users.id as user_id')
                ->join('users', 'online_payment_approval.user_id', '=', 'users.id')
                ->where('online_payment_approval.id', $request_data['online_payment_id'])->get()->toArray();

            $data = [
                'username' => $bankApprovealData[0]['name'],
                'amount' => $bankApprovealData[0]['amount'],
                'email' => $bankApprovealData[0]['email'],
                'status' => 'Rejected',
            ];

            $this->common_task->approveRejectOnlinePaymentEmail($data);

            //send notification to user who requested about approval
            $this->notification_task->onlinePaymentRejectNotify([$bankApprovealData[0]['user_id']]);

            $updateData = ['reject_note' => $request_data['reject_note'], 'third_approval_status' => 'Rejected', 'third_approval_id' => $request_data['user_id'], 'status' => 'Rejected', 'third_approval_date_time' => date('Y-m-d H:i:s')];
            OnlinePaymentApproval::where('id', $request_data['online_payment_id'])->update($updateData);
        } else {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }

        // User Action Log
        $entry_code = OnlinePaymentApproval::where('id', $request_data['online_payment_id'])->value('entry_code');
        $amount = OnlinePaymentApproval::where('id', $request_data['online_payment_id'])->value('amount');
        $action_data = [
            'user_id' => $request_data['user_id'],
            'task_body' => "Online Payment entry code ".$entry_code." rejected <br>Amount: ".$amount."<br>Reject Note: ".$request_data['reject_note'],
            'created_ip' => $request->ip(),
        ];
        $this->user_action_logs->action($action_data);

        return response()->json(['status' => true, 'msg' => "Online payment successfully rejected.", 'data' => []]);
    }


    //Nishit..
    public function get_VehicleMaintenance_approval_list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $loggedin_user_data = User::where('id', $request_data['user_id'])->get(['users.id', 'users.name', 'users.role']);

        //check if accountant or superadmin based on that return data
        if ($loggedin_user_data[0]->role == config('constants.Admin')) {

            $vehicles_approvals = Vehicle_Maintenance::with(['vehicleImage', 'asset'])
                ->join('users', 'vehicle_maintenance.user_id', '=', 'users.id')
                ->leftJoin('users as first_user', 'first_user.id', '=', 'vehicle_maintenance.first_approval_id')
                ->leftJoin('users as second_user', 'second_user.id', '=', 'vehicle_maintenance.second_approval_id')
                ->join('company', 'vehicle_maintenance.company_id', '=', 'company.id')
                ->where('vehicle_maintenance.first_approval_status', 'Pending')
                ->where('vehicle_maintenance.second_approval_status', 'Pending')
                ->get([
                    'vehicle_maintenance.*',
                    'company.company_name', 'users.name AS user_name',
                    'users.profile_image', 'first_user.name as first_user_name', 'second_user.name as second_user_name'
                ]);
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {

            $vehicles_approvals = Vehicle_Maintenance::with(['vehicleImage', 'asset'])
                ->join('users', 'vehicle_maintenance.user_id', '=', 'users.id')
                ->join('company', 'vehicle_maintenance.company_id', '=', 'company.id')
                ->leftJoin('users as first_user', 'first_user.id', '=', 'vehicle_maintenance.first_approval_id')
                ->leftJoin('users as second_user', 'second_user.id', '=', 'vehicle_maintenance.second_approval_id')
                ->where('vehicle_maintenance.first_approval_status', 'Approved')
                ->where('vehicle_maintenance.second_approval_status', 'Pending')
                ->where('vehicle_maintenance.final_approval', 'Pending')
                ->get([
                    'vehicle_maintenance.*',
                    'company.company_name', 'users.name AS user_name',
                    'users.profile_image', 'first_user.name as first_user_name', 'second_user.name as second_user_name'
                ]);
        } else {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        if ($vehicles_approvals->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        foreach ($vehicles_approvals as $key => $vehicle) {


            if ($vehicle->profile_image) {
                $vehicles_approvals[$key]->profile_image = asset('storage/' . str_replace('public/', '', $vehicle->profile_image));
            } else {
                $vehicles_approvals[$key]->profile_image = "";
            }

            foreach ($vehicle->vehicleImage as $k => $file) {

                if ($file->image) {
                    $vehicles_approvals[$key]->vehicleImage[$k]->image = asset('storage/' . str_replace('public/', '', $file->image));
                } else {
                    $vehicles_approvals[$key]->vehicleImage[$k]->image = "";
                }
            }
        }

        return response()->json(['status' => true, 'msg' => 'Records found', 'data' => $vehicles_approvals]);
    }

    //Nishit

    public function approve_vehicle_maintenance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'vehicle_maintenanceid' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $loggedin_user_data = User::where('id', $request_data['user_id'])->get(['users.id', 'users.name', 'users.role']);

        if ($loggedin_user_data[0]->role == config('constants.Admin')) {

            //first approval

            $this->notification_task->VehicleMaintenancePaymentFirstApprovalNotify([$this->super_admin->id]);

            $updateData = ['first_approval_status' => 'Approved', 'first_approval_id' => $request_data['user_id'], 'first_approval_date_time' => date('Y-m-d H:i:s')];
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {

            //second  approval

            $maintenanceApprovealData = Vehicle_Maintenance::select('users.name', 'vehicle_maintenance.amount', 'users.email', 'users.id as user_id')
                ->join('users', 'vehicle_maintenance.user_id', '=', 'users.id')
                ->where('vehicle_maintenance.id', $request_data['vehicle_maintenanceid'])->get();
            $data = [
                'username' => $maintenanceApprovealData[0]['name'],
                'amount' => $maintenanceApprovealData[0]['amount'],
                'email' => $maintenanceApprovealData[0]['email'],
                'status' => 'Approved',
            ];

            $this->common_task->approveRejectVehicleMaintenancePaymentEmail($data);

            //send notification to user who requested about approval
            $this->notification_task->VehicleMaintenancePaymentSecondApprovalNotify([$maintenanceApprovealData[0]->user_id]);

            $updateData = [
                'second_approval_status' => 'Approved', 'second_approval_id' => $request_data['user_id'],
                'final_approval' => 'Approved', 'second_approval_date_time' => date('Y-m-d H:i:s')
            ];
        } else {

            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }

        Vehicle_Maintenance::where('id', $request_data['vehicle_maintenanceid'])->update($updateData);

        // User Action Log
        $maintenance_data = Vehicle_Maintenance::where('id', $request_data['vehicle_maintenanceid'])->first();
        $add_string = "<br>Amount: ".$maintenance_data['amount'];
        $action_data = [
            'user_id' => $request_data['user_id'],
            'task_body' => "Vehicle maintenance approved".$add_string,
            'created_ip' => $request->ip(),
        ];
        $this->user_action_logs->action($action_data);

        return response()->json(['status' => true, 'msg' => "Vehicle Maintenance successfully approved.", 'data' => []]);
    }

    //Nishit

    public function reject_vehicle_maintenance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'vehicle_maintenanceid' => 'required',
            'reject_note' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $loggedin_user_data = User::where('id', $request_data['user_id'])->get(['users.id', 'users.name', 'users.role']);


        if ($loggedin_user_data[0]->role == config('constants.Admin')) {
            //first approval

            $bankApprovealData = Vehicle_Maintenance::select('users.name', 'vehicle_maintenance.amount', 'users.id as user_id', 'users.email', 'users.id as user_id')
                ->join('users', 'vehicle_maintenance.user_id', '=', 'users.id')
                ->where('vehicle_maintenance.id', $request_data['vehicle_maintenanceid'])->get()->toArray();

            $data = [
                'username' => $bankApprovealData[0]['name'],
                'amount' => $bankApprovealData[0]['amount'],
                'email' => $bankApprovealData[0]['email'],
                'status' => 'Rejected',
            ];

            $this->common_task->approveRejectVehicleMaintenancePaymentEmail($data);

            //send notification to user who requested about approval
            $this->notification_task->VehicleMaintenancePaymentRejectNotify([$bankApprovealData[0]['user_id']]);

            $updateData = ['reject_note' => $request_data['reject_note'], 'first_approval_status' => 'Rejected', 'first_approval_id' => $request_data['user_id'], 'final_approval' => 'Rejected', 'first_approval_date_time' => date('Y-m-d H:i:s')];
        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            //second and  approval


            $bankApprovealData = Vehicle_Maintenance::select('users.name', 'vehicle_maintenance.amount', 'users.id as user_id', 'users.email', 'users.id as user_id')
                ->join('users', 'vehicle_maintenance.user_id', '=', 'users.id')
                ->where('vehicle_maintenance.id', $request_data['vehicle_maintenanceid'])->get()->toArray();

            $data = [
                'username' => $bankApprovealData[0]['name'],
                'amount' => $bankApprovealData[0]['amount'],
                'email' => $bankApprovealData[0]['email'],
                'status' => 'Rejected',
            ];

            $this->common_task->approveRejectVehicleMaintenancePaymentEmail($data);

            //send notification to user who requested about approval
            $this->notification_task->VehicleMaintenancePaymentRejectNotify([$bankApprovealData[0]['user_id']]);

            $updateData = ['reject_note' => $request_data['reject_note'], 'second_approval_status' => 'Rejected', 'second_approval_id' => $request_data['user_id'], 'final_approval' => 'Rejected', 'second_approval_date_time' => date('Y-m-d H:i:s')];
        } else {

            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }

        Vehicle_Maintenance::where('id', $request_data['vehicle_maintenanceid'])->update($updateData);

        // User Action Log
        $maintenance_data = Vehicle_Maintenance::where('id', $request_data['vehicle_maintenanceid'])->first();
        $add_string = "<br>Amount: ".$maintenance_data['amount']."<br>Reject Note: ".$request_data['reject_note'];
        $action_data = [
            'user_id' => $request_data['user_id'],
            'task_body' => "Vehicle maintenance rejected".$add_string,
            'created_ip' => $request->ip(),
        ];
        $this->user_action_logs->action($action_data);

        return response()->json(['status' => true, 'msg' => "Vehicle Maintenance successfully rejected.", 'data' => []]);
    }

    //--------------------------------- 09/06/2020
    public function get_signed_cheque_approval_requests(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $response_data = [];

        $logged_in_userdata = User::where('id', $request_data['user_id'])->get();
        $get_fields = ['users.name','signed_cheque_list.id','signed_cheque_list.cheque_book_ref_no','signed_cheque_list.cheque_start_no',
                        'signed_cheque_list.cheque_end_no','signed_cheque_list.status','signed_cheque_list.reject_note', 
                    'signed_cheque_list.created_at'];

        $cheque_list = Signed_cheque_list::leftjoin('users','users.id' ,'=' ,'signed_cheque_list.user_id')
            ->where('signed_cheque_list.status', 'Pending')
            ->get($get_fields);


        if ($cheque_list->count() == 0) {
            return response()->json(['status' => false,'msg' => "No Record found.",'data' => []]);
        }

        foreach ($cheque_list as $key => $value) {
            $cheque_list[$key]['get_company_detail'] = $this->get_company_detail($value['cheque_book_ref_no'],'cheque');
            $cheque_list[$key]['get_bank_detail'] = $this->get_bank_detail($value['cheque_book_ref_no'],'cheque');
        }

        $response_data['cheque_list'] = $cheque_list;
        return response()->json(['status' => true,'msg' => "Record found.",'data' => $response_data]);
    }

    public function accept_signed_cheque_approval_request(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'signed_cheque_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $response_data = [];

        if (Signed_cheque_list::where('id', $request_data['signed_cheque_id'])->update(['status' => 'Accepted'])) {

            $signed_data = Signed_cheque_list::where('id',$request_data['signed_cheque_id'])->first();

            for($i=$signed_data['cheque_start_no'];$i<=$signed_data['cheque_end_no'];$i++) {
                    $chequeModel =  ChequeRegister::where('check_ref_no',$signed_data['cheque_book_ref_no'])->where('ch_no',$i)->first();
                    $chequeModel->is_signed = 'yes';
                    $chequeModel->updated_at = date('Y-m-d h:i:s');
                    $chequeModel->save();
            }

            // User Action Log
            $action_data = [
                'user_id' => $request_data['user_id'],
                'task_body' => $signed_data['cheque_book_ref_no']." signed cheque request approved",
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            return response()->json(['status' => true,'msg' => "Cheque signed request successfully Approved",'data' => []]);
        }
        return response()->json(['status' => false,'msg' => "Oops, Something went wrong!",'data' => []]);

    }

    public function reject_signed_cheque_approval_request(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'signed_cheque_id' => 'required',
                    'reject_note' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $response_data = [];

        $logged_in_userdata = User::where('id', $request_data['user_id'])->get();

        $id = $request_data['signed_cheque_id'];
        $update_arr = [
                'reject_note' => $request_data['reject_note'],
                'status' => 'Rejected'
        ];
        if (Signed_cheque_list::where('id', $id)->update($update_arr)) {

            $signed_data = Signed_cheque_list::where('id',$id)->first();
            for($i=$signed_data['cheque_start_no'];$i<=$signed_data['cheque_end_no'];$i++) {
                $chequeModel =  ChequeRegister::where('check_ref_no',$signed_data['cheque_book_ref_no'])->where('ch_no',$i)->first();
                $chequeModel->signed_slug = 'No';
                $chequeModel->save();
            }

            // User Action Log
            $action_data = [
                'user_id' => $request_data['user_id'],
                'task_body' => $signed_data['cheque_book_ref_no']." signed cheque request rejected",
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            return response()->json(['status' => true,'msg' => "Cheque signed request successfully Rejected.",'data' => []]);
        }
        return response()->json(['status' => false,'msg' => "Oops, Something went wrong!",'data' => []]);
    }

    public function get_signed_rtgs_approval_requests(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $response_data = [];
        $get_fields = ['users.name','signed_rtgs_request.id','signed_rtgs_request.rtgs_ref_no','signed_rtgs_request.rtgs_start_no',
                        'signed_rtgs_request.rtgs_end_no','signed_rtgs_request.status','signed_rtgs_request.reject_note',
                    'signed_rtgs_request.created_at'];

        $rtgs_list = Signed_rtgs_request::leftjoin('users','users.id' ,'=' ,'signed_rtgs_request.user_id')
                ->where('signed_rtgs_request.status', 'Pending')
                ->get($get_fields);

        if ($rtgs_list->count() == 0) {
            return response()->json(['status' => false,'msg' => "No Record found.",'data' => []]);
        }

        foreach ($rtgs_list as $key => $value) {
            $rtgs_list[$key]['get_company_detail'] = $this->get_company_detail($value['rtgs_ref_no'],'rtgs');
            $rtgs_list[$key]['get_bank_detail'] = $this->get_bank_detail($value['rtgs_ref_no'],'rtgs');
        }

        $response_data['rtgs_list'] = $rtgs_list;
        return response()->json(['status' => true,'msg' => "Record found.",'data' => $response_data]);
    }

    public function get_company_detail($ref_no,$type){

        if($type == "rtgs"){
            $rtgs = RtgsRegister::where('rtgs_ref_no',$ref_no)->first();
            if($rtgs){
                return Companies::whereId($rtgs['company_id'])->get()->toArray();
            }else{
                return [];
            }
        }elseif ($type == "cheque") {
            $rtgs = ChequeRegister::where('check_ref_no',$ref_no)->first();
            if($rtgs){
                return Companies::whereId($rtgs['company_id'])->get()->toArray();
            }else{
                return [];
            }
        }elseif ($type == "letter") {
            $rtgs = LetterHeadRegister::where('letter_head_ref_no',$ref_no)->first();
            if($rtgs){
                return Companies::whereId($rtgs['company_id'])->get()->toArray();
            }else{
                return [];
            }
        }
    }

    public function get_bank_detail($ref_no,$type){
        if($type == "rtgs"){
            $rtgs = RtgsRegister::where('rtgs_ref_no',$ref_no)->first();
            if($rtgs){
                return Banks::whereId($rtgs['bank_id'])->get()->toArray();
            }else{
                return [];
            }
        }elseif ($type == "cheque") {
            $rtgs = ChequeRegister::where('check_ref_no',$ref_no)->first();
            if($rtgs){
                return Banks::whereId($rtgs['bank_id'])->get()->toArray();
            }else{
                return [];
            }
        }
    }

    public function accept_signed_rtgs_approval_request(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'signed_rtgs_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $response_data = [];

        if (Signed_rtgs_request::where('id', $request_data['signed_rtgs_id'])->update(['status' => 'Accepted'])) {

            $signed_data = Signed_rtgs_request::where('id',$request_data['signed_rtgs_id'])->first();

            for($i=$signed_data['rtgs_start_no'];$i<=$signed_data['rtgs_end_no'];$i++) {
                    $rtgsModel =  RtgsRegister::where('rtgs_ref_no',$signed_data['rtgs_ref_no'])->where('rtgs_no',$i)->first();
                    $rtgsModel->is_signed = 'yes';
                    $rtgsModel->updated_at = date('Y-m-d h:i:s');
                    $rtgsModel->save();
                }

            // User Action Log
            $action_data = [
                'user_id' => $request_data['user_id'],
                'task_body' => $signed_data['rtgs_ref_no']." RTGS signed request approved",
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            return response()->json(['status' => true,'msg' => "Rtgs signed request successfully Approved",'data' => []]);
        }
            return response()->json(['status' => false,'msg' => "Oops, Something went wrong!",'data' => []]);
    }

    public function reject_signed_rtgs_approval_request(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'signed_rtgs_id' => 'required',
                    'reject_note' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $response_data = [];

        $id = $request_data['signed_rtgs_id'];
        $update_arr = [
                'reject_note' => $request_data['reject_note'],
                'status' => 'Rejected'
        ];
        if (Signed_rtgs_request::where('id', $id)->update($update_arr)) {

            $signed_data = Signed_rtgs_request::where('id',$id)->first();
            for($i=$signed_data['rtgs_start_no'];$i<=$signed_data['rtgs_end_no'];$i++) {
                $rtgsModel =  RtgsRegister::where('rtgs_ref_no',$signed_data['rtgs_ref_no'])->where('rtgs_no',$i)->first();
                $rtgsModel->signed_slug = 'No';
                $rtgsModel->updated_at = date('Y-m-d h:i:s');
                $rtgsModel->updated_ip = $request->ip();
                $rtgsModel->save();
            }

            // User Action Log
            $action_data = [
                'user_id' => $request_data['user_id'],
                'task_body' => $signed_data['rtgs_ref_no']." RTGS signed request rejected",
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            return response()->json(['status' => true,'msg' => "Rtgs signed request successfully Rejected",'data' => []]);
        }
        return response()->json(['status' => false,'msg' => "Oops, Something went wrong!",'data' => []]);
    }

    //--------------------------------- 16/06/2020
    public function get_signed_letterhead_approval_requests(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $response_data = [];

        $logged_in_userdata = User::where('id', $request_data['user_id'])->get();

        $letterhead_list = Signed_letter_head_request::leftjoin('users','users.id' ,'=' ,'signed_letter_head_request.user_id')
                ->where('signed_letter_head_request.status', 'Pending')
                ->get(['users.name','signed_letter_head_request.id','signed_letter_head_request.letter_head_ref_no','signed_letter_head_request.letter_head_start_number',
                        'signed_letter_head_request.letter_head_end_number','signed_letter_head_request.status','signed_letter_head_request.reject_note',
                        'signed_letter_head_request.created_at']);


        if ($letterhead_list->count() == 0) {
            return response()->json(['status' => false,'msg' => "No Record found.",'data' => []]);
        }

        foreach ($letterhead_list as $key => $value) {
            $letterhead_list[$key]['get_company_detail'] = $this->get_company_detail($value['letter_head_ref_no'],'letter');
        }

        $response_data['letterhead_list'] = $letterhead_list;
        return response()->json(['status' => true,'msg' => "Record found.",'data' => $response_data]);

    }

    public function accept_letterhead_cheque_approval_request(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'signed_letterhead_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $id = $request_data['signed_letterhead_id'];
        $response_data = [];

        if (Signed_letter_head_request::where('id', $id)->update(['status' => 'Accepted'])) {

            $signed_data = Signed_letter_head_request::where('id',$id)->first();

            for($i=$signed_data['letter_head_start_number'];$i<=$signed_data['letter_head_end_number'];$i++) {
                    $rtgsModel =  LetterHeadRegister::where('letter_head_ref_no',$signed_data['letter_head_ref_no'])->where('letter_head_number',$i)->first();
                    $rtgsModel->is_signed = 'yes';
                    $rtgsModel->updated_at = date('Y-m-d h:i:s');
                    $rtgsModel->save();
                }

                // User Action Log
                $action_data = [
                    'user_id' => $request_data['user_id'],
                    'task_body' => $signed_data['letter_head_ref_no']." signed letter head request approved",
                    'created_ip' => $request->ip(),
                ];
                $this->user_action_logs->action($action_data);

                return response()->json(['status' => true,'msg' => "Letter head signed request successfully Approved",'data' => []]);
        }
        return response()->json(['status' => false,'msg' => "Oops, Something went wrong!",'data' => []]);

    }

    public function reject_signed_letterhead_approval_request(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'signed_letterhead_id' => 'required',
                    'reject_note' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $response_data = [];

        $logged_in_userdata = User::where('id', $request_data['user_id'])->get();

        $id = $request_data['signed_letterhead_id'];
        $update_arr = [
            'reject_note' => $request_data['reject_note'],
            'status' => 'Rejected'
        ];

        if (Signed_letter_head_request::where('id', $id)->update($update_arr)) {

            $signed_data = Signed_letter_head_request::where('id',$id)->first();
            for($i=$signed_data['letter_head_start_number'];$i<=$signed_data['letter_head_end_number'];$i++) {
                $rtgsModel =  LetterHeadRegister::where('letter_head_ref_no',$signed_data['letter_head_ref_no'])->where('letter_head_number',$i)->first();
                $rtgsModel->signed_slug = 'No';
                $rtgsModel->updated_at = date('Y-m-d h:i:s');
                $rtgsModel->updated_ip = $request->ip();
                $rtgsModel->save();
            }

            // User Action Log
                $action_data = [
                    'user_id' => $request_data['user_id'],
                    'task_body' => $signed_data['letter_head_ref_no']." signed letter head request rejected",
                    'created_ip' => $request->ip(),
                ];
                $this->user_action_logs->action($action_data);

            return response()->json(['status' => true,'msg' => "Letter head signed request successfully Rejected!",'data' => []]);
        }

        return response()->json(['status' => false,'msg' => "Oops, Something went wrong!",'data' => []]);
    }

    //------------------------------- Attendace ------------------------

    //03-09-2020 nish
    public function attendace_approval_list(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $response_data = [];

        $logged_in_userdata = User::where('id', $request_data['user_id'])->get();

        $attendace_approval_list = \App\Attendance_approvals::leftjoin('users','users.id' ,'=' ,'attendance_approvals.user_id')
                ->where('attendance_approvals.status', 'Pending')
                ->orderBy('attendance_approvals.id', 'DESC')
                ->get(['users.name as user_name','attendance_approvals.*']);


        if ($attendace_approval_list->count() == 0) {
            return response()->json(['status' => false,'msg' => "No Record found.",'data' => []]);
        }

        $response_data['attendace_approval_list'] = $attendace_approval_list;
        return response()->json(['status' => true,'msg' => "Record found.",'data' => $response_data]);

    }

    public function accept_attendace_approval_request(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'attendance_approval_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $id = $request_data['attendance_approval_id'];
        $response_data = [];

        if (Attendance_approvals::where('id', $id)->update(['status' => 'Approved'])) {

            $user_attendace_data = Attendance_approvals::where('id',$id)->first();
            $attendanceMaster = AttendanceMaster::where('date', $user_attendace_data['attendace_date'] )->where('user_id', $user_attendace_data['user_id'])->get()->first();
            if (!empty($attendanceMaster)) {
                $this->manual_attendance_insert($user_attendace_data, $attendanceMaster, 1);
            } else {
                $this->manual_attendance_insert($user_attendace_data, $attendanceMaster, 0);
            }

            return response()->json(['status' => true,'msg' => "Attendace request successfully Approved",'data' => []]);
            //Already check in web side
            // $check_holiday_weekend = $this->common_task->check_holiday_weekend_attendance($user_attendace_data['user_id'], $user_attendace_data['attendace_date']);

            // 1. case one
            // if (!$check_holiday_weekend) {
            //     return response()->json(['status' => false,'msg' => "This user has not requested/approved any attendance for work on holiday/weekend on given date. Please ask user to make request from Weekend/Holiday Request menu, and once that request will be approved then you can make attendance of that date for user", 'data' => [], ]);
            // }

            //2. case two
            // $attendanceMaster = AttendanceMaster::where('date', $user_attendace_data['attendace_date'] )->where('user_id', $user_attendace_data['user_id'])->get()->first();
            // if (!empty($attendanceMaster)) {
            //     if ($attendanceMaster->availability_status == 3 || $attendanceMaster->availability_status == 4 || $attendanceMaster->availability_status == 5) {
            //         $this->manual_attendance_insert($user_attendace_data, $attendanceMaster, 1);
            //         return response()->json(['status' => true, 'msg' => "Attendace request successfully Approved", 'data' => [] ]);
            //     } else {
            //         return response()->json(['status' => false, 'msg' => "Attendance already available for this date.", 'data' => [] ]);
            //     }
            // } else {

            //     $this->manual_attendance_insert($user_attendace_data, $attendanceMaster, 0);

            //     return response()->json(['status' => true,'msg' => "Attendace request successfully Approved",'data' => []]);
            // }
        }
        return response()->json(['status' => false,'msg' => "Oops, Something went wrong!",'data' => []]);

    }

    public function manual_attendance_insert($request, $attendance_master, $HLW_attend = 0) {
        if ($HLW_attend == 1) {
            $AttendanceMaster = $attendance_master;
        } else {
            $AttendanceMaster = new AttendanceMaster();
            $AttendanceMaster->availability_status = 1;
        }
        $AttendanceMaster->user_id = $request['user_id'];
        $AttendanceMaster->first_in = $request['attendace_date'] . ' ' . $request['punch_in'];
        $AttendanceMaster->last_out = $request['attendace_date'] . ' ' . $request['punch_out'];
        $AttendanceMaster->date = $request['attendace_date'];
        $AttendanceMaster->status = 'Enabled';
        $AttendanceMaster->created_at = date('Y-m-d H:i:s');
        $AttendanceMaster->created_ip = $request['created_ip'];
        $AttendanceMaster->updated_at = date('Y-m-d H:i:s');
        $AttendanceMaster->updated_ip = $request['updated_ip'];
        $AttendanceMaster->manual_add_by = $request['manual_add_by'];
        $AttendanceMaster->manual_add_reason = $request['manual_add_reason'];
        $AttendanceMaster->is_manually_added = 1;
        //check for late time
        $lateTime = new DateTime('09:31:00');
        $moreLateTime = new DateTime('09:46:00');
        $time = new DateTime($request['punch_in']);
        $actual_lateTime = new DateTime('09:30:00');

        if ($time > $lateTime && $moreLateTime >= $time) {
            $AttendanceMaster->is_late = 'YES';
            $duration = $time->diff($actual_lateTime);
            $AttendanceMaster->late_time = $duration->format("%H:%I:%S");
        } else if ($moreLateTime < $time) {
            $AttendanceMaster->is_late_more = 'YES';
            //$duration = $time->diff($moreLateTime);
            $duration = $time->diff($actual_lateTime);
            $AttendanceMaster->late_time = $duration->format("%H:%I:%S");
        }

        if ($AttendanceMaster->save()) {

            $AttendanceDetail = new AttendanceDetail();
            $AttendanceDetail->attendance_master_id = $AttendanceMaster->id;
            $AttendanceDetail->time = $request['attendace_date'] . ' ' . $request['punch_in'];
            $AttendanceDetail->punch_type = 'IN';
            $AttendanceDetail->device_type = 'WEB';
            if ($HLW_attend == 1) {
                $AttendanceDetail->is_approved = 'Pending';
            } else {
                $AttendanceDetail->is_approved = 'YES';
            }
            $AttendanceDetail->status = 'Enabled';
            $AttendanceDetail->created_at = date('Y-m-d H:i:s');
            $AttendanceDetail->created_ip =  $request['created_ip'];
            $AttendanceDetail->updated_at = date('Y-m-d H:i:s');
            $AttendanceDetail->updated_ip = $request['updated_ip'];
            $AttendanceDetail->save();

            $AttendanceDetail = new AttendanceDetail();
            $AttendanceDetail->attendance_master_id = $AttendanceMaster->id;
            $AttendanceDetail->time = $request['attendace_date'] . ' ' . $request['punch_out'];
            $AttendanceDetail->punch_type = 'OUT';
            $AttendanceDetail->device_type = 'WEB';
            if ($HLW_attend == 1) {
                $AttendanceDetail->is_approved = 'Pending';
            } else {
                $AttendanceDetail->is_approved = 'YES';
            }
            $AttendanceDetail->status = 'Enabled';
            $AttendanceDetail->created_at = date('Y-m-d H:i:s');
            $AttendanceDetail->created_ip = $request['created_ip'];
            $AttendanceDetail->updated_at = date('Y-m-d H:i:s');
            $AttendanceDetail->updated_ip =  $request['updated_ip'];
            $AttendanceDetail->save();
            return true;
        } else {
            return false;
        }
    }

    public function reject_attendace_approval_request(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'attendance_approval_id' => 'required',
                    'reject_note' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $response_data = [];

        // $logged_in_userdata = User::where('id', $request_data['user_id'])->get();

        $id = $request_data['attendance_approval_id'];
        $update_arr = [
            'reject_note' => $request_data['reject_note'],
            'status' => 'Rejected'
        ];

        if (Attendance_approvals::where('id', $id)->update($update_arr)) {
            $user_attendace_data = Attendance_approvals::where('id',$id)->first();
            $mail_data = [
                'user_name' => User::where('id',$user_attendace_data['user_id'])->value('name'),
                'super_user' => User::where('id', $request_data['user_id'])->value('name'),
                'date' => $user_attendace_data['attendace_date'],
                'in' => $user_attendace_data['punch_in'],
                'out' => $user_attendace_data['punch_out'],
                'reject_note' => $request_data['reject_note'],
                'to_email' => User::where('id',$user_attendace_data['manual_add_by'])->pluck('email')->toArray()
            ];
            $this->common_task->attendanceApprovalRejectEmail($mail_data);
            return response()->json(['status' => true,'msg' => "Attendace request successfully Rejected!",'data' => []]);
        }

        return response()->json(['status' => false,'msg' => "Oops, Something went wrong!",'data' => []]);
    }

    //16/09/2020

    public function get_vendor_list(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $response_data = \App\Vendors::groupBy('vendor_name')->get(['id','vendor_name'])->toArray();

        return response()->json(['status' => true,'msg' => "Record found",'data' => $response_data]);

    }
    public function get_vendor_payments(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'vendor_name' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        if ($request_data['vendor_name']) {
            $vendor_ids = \App\Vendors::where('vendor_name',$request_data['vendor_name'])->pluck('id')->toArray();
            $response_data['bank_payment'] = $bank_payment_amount =  \App\BankPaymentApproval::whereIn('vendor_id',$vendor_ids)->get()->sum('amount');
            $response_data['cash_payment'] = $cash_payment_amount = \App\CashApproval::whereIn('vendor_id',$vendor_ids)->get()->sum('amount');
            $response_data['online_payment'] = $online_payment_amount = \App\OnlinePaymentApproval::whereIn('vendor_id',$vendor_ids)->get()->sum('amount');
            $response_data['total_amount'] = $bank_payment_amount + $cash_payment_amount + $online_payment_amount;
        }

        return response()->json(['status' => true,'msg' => "Record found",'data' => $response_data]);

    }
    

    public function get_project_update_request(){
        // project_update_approve_request       /* new table name */
        $aprvl_reqst = ProjectUpdateApproveRequest::get()->toArray();
        return response()->json(['status' => true,'msg' => "Record found",'data' => $aprvl_reqst]);
    }

    public function approve_reject_project_update_request(Request $request){
        $validator = Validator::make($request->all(), [
                    'request_id' => 'required',
                    'status' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        if($request_data['status'] == "Rejected"){
            ProjectUpdateApproveRequest::where('id', $request_data['request_id'])->delete();
            return response()->json(['status' => true,'msg' => "Request rejected successfully.",'data' => []]);
        }else{
            $approve_request = ProjectUpdateApproveRequest::where('id', $request_data['request_id'])->first();
            $managers = json_decode($approve_request['project_manager']);
            $approve_request_arr = ['company_id' => $approve_request->company_id,
                                    'client_id' => $approve_request->client_id,
                                    'project_name' => $approve_request->project_name,
                                    'project_location' => $approve_request->project_location,
                                    'details' => $approve_request->details
                                    ];
                Projects::where('id',$approve_request->project_id)->update($approve_request_arr);

                ProjectManager::where('project_id', $approve_request->project_id)->delete();
                foreach ($managers as $key => $value) {
                    ProjectManager::insert([
                        'project_id' => $value->project_id,
                        'user_id' => $value->user_id,
                        'is_manager' => (isset($value->is_manager)) ? $value->is_manager : "0",
                        'created_ip' => $request->ip(),
                        'updated_ip' => $request->ip(),
                    ]);
                }
                ProjectUpdateApproveRequest::where('id', $request_data['request_id'])->delete();
                return response()->json(['status' => true,'msg' => "Request approved successfully.",'data' => []]);   
        }
    }

}
