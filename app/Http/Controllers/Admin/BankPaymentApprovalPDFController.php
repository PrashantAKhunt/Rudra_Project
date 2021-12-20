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
use App\Projects;
use App\BudgetSheetApproval;
use App\TenderPaymentRequest;
use App\Vendors;
use App\Vendors_bank;
use App\TdsSectionType;
use App\Tender;
use Illuminate\Support\Facades\Storage;
use App\Lib\UserActionLogs;
use PDF;
use Carbon\Carbon;
use App\ApprovedPaymentCounter;

class BankPaymentApprovalPDFController extends Controller
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

    public function index($id, $type, $date)
    {
        # code...
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
            ->orderBy('bank_payment_approval.created_at', 'DESC')
            ->where('bank_payment_approval.id', $id);

        $this->data['bank_payment_approval_history'] = $bank_payment_approval_history = $Results->get(['vendors_bank.bank_name as vendor_bank_name', 'vendors_bank.ac_number', 'vendors_bank.account_type','vendors_bank.branch as vender_bank_branch','vendors_bank.ifsc',
         'bank_payment_approval.*', 'bank_details', 'bank.bank_name', 'bank.branch', 'bank.ac_number as user_ac_number', 'cheque_register.ch_no','cheque_register.issue_date','budget_sheet_approval.budhet_sheet_no',
          'users.name as user_name', 'company.company_name','company.pan_no', 'project.project_name','rtgs_register.rtgs_no',
           'vendor.vendor_name','clients.client_name','clients.location', 'project_sites.site_name', 'tds_section_type.section_type','tender.tender_sr_no'])->toArray();
        $this->data['year'] = Carbon::now()->format('Y')."-".Carbon::now()->addYear()->format('y');
        $this->data['type'] = $type;

        $customPaper = array(0,0,1020,1020);

        $counter = ApprovedPaymentCounter::count();
        $page_counter = 1;
        if($counter == 0)
        {
            $nameInsert = ApprovedPaymentCounter::create([
                'bank_payment_id' => $id,
                'counter' => 1
            ]);
        }
        else
        {
            $nameInsert2 = ApprovedPaymentCounter::where('bank_payment_id', $id)->first();
            if(empty($nameInsert2))
            {
                $latecounter = ApprovedPaymentCounter::latest()->first();
                $latecounter = $latecounter->counter;
                $nameInsert = ApprovedPaymentCounter::create([
                    'bank_payment_id' => $id,
                    'counter' => $latecounter+1
                ]);

                $page_counter = $nameInsert->counter;
            }
            else
            {
                $page_counter = $nameInsert2->counter;
            }
        }

        $this->data['page_counter'] = $page_counter;
        $this->data['date'] = Carbon::parse($date)->format('d-m-Y');

        $data = $this->data;
        if(count($data['bank_payment_approval_history']) > 0)
        {
            //return view('admin.payment.pdf', array('data' => $data));

            $pdf = PDF::loadView('admin.payment.pdf', array('data' => $data));//->set_paper('letter', 'landscape')
            //$pdf->setPaper($customPaper);
            $pdf->setPaper('A4', 'Landscape');
            return $pdf->download('approve_bank_payment.pdf');
        }
        return false;
    }

    public function sbi_index($id, $type, $date)
    {
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
            ->orderBy('bank_payment_approval.created_at', 'DESC')
            ->where('bank_payment_approval.id', $id);

        $this->data['bank_payment_approval_history'] = $bank_payment_approval_history = $Results->get(['vendors_bank.bank_name as vendor_bank_name', 'vendors_bank.ac_number', 'vendors_bank.account_type','vendors_bank.branch as vender_bank_branch','vendors_bank.ifsc',
         'bank_payment_approval.*', 'bank_details', 'bank.bank_name', 'bank.branch', 'bank.ac_number as user_ac_number', 'cheque_register.ch_no','cheque_register.issue_date','budget_sheet_approval.budhet_sheet_no',
          'users.name as user_name', 'company.company_name', 'company.detail','company.pan_no', 'project.project_name','rtgs_register.rtgs_no',
           'vendor.vendor_name','clients.client_name','clients.location', 'project_sites.site_name', 'tds_section_type.section_type','tender.tender_sr_no'])->toArray();
        $this->data['year'] = Carbon::now()->format('Y')."-".Carbon::now()->addYear()->format('y');
        $this->data['type'] = $type;

        $customPaper = array(0,0,1020,1020);

        $counter = ApprovedPaymentCounter::count();
        $page_counter = 1;
        if($counter == 0)
        {
            $nameInsert = ApprovedPaymentCounter::create([
                'bank_payment_id' => $id,
                'counter' => 1
            ]);
        }
        else
        {
            $nameInsert2 = ApprovedPaymentCounter::where('bank_payment_id', $id)->first();
            if(empty($nameInsert2))
            {
                $latecounter = ApprovedPaymentCounter::latest()->first();
                $latecounter = $latecounter->counter;
                $nameInsert = ApprovedPaymentCounter::create([
                    'bank_payment_id' => $id,
                    'counter' => $latecounter+1
                ]);

                $page_counter = $nameInsert->counter;
            }
            else
            {
                $page_counter = $nameInsert2->counter;
            }
        }

        $this->data['page_counter'] = $page_counter;
        $this->data['date'] = Carbon::parse($date)->format('d-m-Y');

        $data = $this->data;
        if(count($data['bank_payment_approval_history']) > 0)
        {
            //return view('admin.payment.sbi_pdf', array('data' => $data));
            $pdf = PDF::loadView('admin.payment.sbi_pdf', array('data' => $data));//->set_paper('letter', 'landscape')
            $pdf->setPaper($customPaper);
            return $pdf->download('approve_bank_payment.pdf');
        }

        return false;
    }
}