<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Lib\CommonTask;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;
use Exception;
use App\Email_format;
use App\User;
use App\Vendors;
use App\Companies;
use App\Projects;
use App\Site_manage_boq;
use App\ProjectManager;
use App\Clients;
use App\ClientDetail;
use App\Vendors_bank;
use App\Project_sites;
use App\Banks;
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


class ModuleEntryApprovalController extends Controller
{

    public $data;
    public $common_task;

    public function __construct()
    {
        $this->super_admin = \App\User::where('role', config('constants.SuperUser'))->first();
        $this->common_task = new CommonTask();
    }

    public function api_entry_approval_count(Request $request)    
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
        //------------------- Vendor
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $vendor_entry_count = Vendors::where('status', 'Disabled')->where('is_approved',0)->get()->count();
        } else {
            $vendor_entry_count = 0;
        }
        $response_data['vendor_entry_count'] = $vendor_entry_count;
        //-------------------Company
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $company_entry_count = Companies::where('status', 'Disabled')->where('is_approved',0)->get()->count();
        } else {
            $company_entry_count = 0;
        }
        $response_data['company_entry_count'] = $company_entry_count;
        //-------------------Client
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $client_entry_count = Clients::where('status', 'Disabled')->where('is_approved',0)->get()->count();
        } else {
            $client_entry_count = 0;
        }
        $response_data['client_entry_count'] = $client_entry_count;
        //-------------------Project
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $project_entry_count = Projects::where('status', 'Disabled')->where('is_approved',0)->get()->count();
        } else {
            $project_entry_count = 0;
        }
        $response_data['project_entry_count'] = $project_entry_count;
         //-------------------Vendor bank
         if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $vendor_bank_entry_count = Vendors_bank::where('status', 'Disabled')->where('is_approved',0)->get()->count();
        } else {
            $vendor_bank_entry_count = 0;
        }
        $response_data['vendor_bank_entry_count'] = $vendor_bank_entry_count;
         //-------------------Project site
         if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $project_site_entry_count = Project_sites::where('status', 'Disabled')->where('is_approved',0)->get()->count();
        } else {
            $project_site_entry_count = 0;
        }
        $response_data['project_site_entry_count'] = $project_site_entry_count;
        //-------------------Bank
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $bank_entry_count = Banks::where('status', 'Disabled')->where('is_approved',0)->get()->count();
        } else {
            $bank_entry_count = 0;
        }
        $response_data['bank_entry_count'] = $bank_entry_count;
        //-------------------Bank Charge category
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $bank_charge_category_entry_count = Bank_charge_category::where('is_approved',0)->get()->count();
        } else {
            $bank_charge_category_entry_count = 0;
        }
        $response_data['bank_charge_category_entry_count'] = $bank_charge_category_entry_count;
        //-------------------Bank charge sub category
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $bank_charge_sub_category_entry_count = Bank_charge_sub_category::where('is_approved',0)->get()->count();
        } else {
            $bank_charge_sub_category_entry_count = 0;
        }
        $response_data['bank_charge_sub_category_entry_count'] = $bank_charge_sub_category_entry_count;
        //-------------------Payment Card
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $payment_card_entry_count = PaymentCard::where('status', 'Disabled')->where('is_approved',0)->get()->count();
        } else {
            $payment_card_entry_count = 0;
        }
        $response_data['payment_card_entry_count'] = $payment_card_entry_count;
        //-------------------Company Document
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $company_document_entry_count = CompanyDocumentManagement::where('status', 'Disabled')->where('is_approved',0)->get()->count();
        } else {
            $company_document_entry_count = 0;
        }
        $response_data['company_document_entry_count'] = $company_document_entry_count;
        //-------------------Tender Category
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $tender_category_entry_count = TenderCategory::where('status', 'Disabled')->where('is_approved',0)->get()->count();
        } else {
            $tender_category_entry_count = 0;
        }
        $response_data['tender_category_entry_count'] = $tender_category_entry_count;
        //-------------------Tender Pattern
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $tender_pattern_entry_count = TenderPattern::where('status', 'Disabled')->where('is_approved',0)->get()->count();
        } else {
            $tender_pattern_entry_count = 0;
        }
        $response_data['tender_pattern_entry_count'] = $tender_pattern_entry_count;
        //-------------------Tender Physical
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $tender_physical_submission_entry_count = Tender_physical_submission::where('status', 'Disabled')->where('is_approved',0)->get()->count();
        } else {
            $tender_physical_submission_entry_count = 0;
        }
        $response_data['tender_physical_submission_entry_count'] = $tender_physical_submission_entry_count;
        //-------------------Registry category
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $registry_category_entry_count = Inward_outward_doc_category::where('status', 'Disabled')->where('is_approved',0)->get()->count();
        } else {
            $registry_category_entry_count = 0;
        }
        $response_data['registry_category_entry_count'] = $registry_category_entry_count;
        //-------------------Registry sub category
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $registry_sub_category_entry_count = Inward_outward_doc_sub_category::where('status', 'Disabled')->where('is_approved',0)->get()->count();
        } else {
            $registry_sub_category_entry_count = 0;
        }
        $response_data['registry_sub_category_entry_count'] = $registry_sub_category_entry_count;
        //------------------- Delivery mode
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $delivery_mode_entry_count = Inward_outward_delivery_mode::where('status', 'Disabled')->where('is_approved',0)->get()->count();
        } else {
            $delivery_mode_entry_count = 0;
        }
        $response_data['delivery_mode_entry_count'] = $delivery_mode_entry_count;
        //-------------------Sender category
        if ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
            $sender_category_entry_count = Sender::where('status', 'Disabled')->where('is_approved',0)->get()->count();
        } else {
            $sender_category_entry_count = 0;
        }
        $response_data['sender_category_entry_count'] = $sender_category_entry_count;

        return response()->json(['status' => true, 'msg' => 'Record found', 'data' => $response_data]);

    }

    #-------------------- Vendor
    public function api_vendor_entry_approval_list(Request $request)
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
        $select_fields = ['vendor.*','company.company_name'];
        $vendor_entry = Vendors::leftjoin('company','vendor.company_id','=','company.id')
                                  ->where('vendor.status', 'Disabled')
                                  ->where('vendor.is_approved',0)
                                  ->get($select_fields);
        if ($vendor_entry->count() == 0) {
            return response()->json(['status' => false,'msg' => "No Record found.",'data' => []]);
        }
        $response_data['vendor_entry_list'] = $vendor_entry;
        return response()->json(['status' => true,'msg' => "Record found.",'data' => $response_data]);

    }

    public function api_accept_vendor_entry(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'vendor_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $id = $request_data['vendor_id'];
        $response_data = [];

        if (Vendors::where('id', $id)->update(['status' => 'Enabled','is_approved' => 1])) {
        
            return response()->json(['status' => true,'msg' => "Vendor entry successfully Approved",'data' => []]);  
        }
        return response()->json(['status' => false,'msg' => "Oops, Something went wrong!",'data' => []]); 
        
    }

    public function api_reject_vendor_entry(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'vendor_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $id = $request_data['vendor_id'];
        $response_data = [];
        $entry = Vendors::where('id',$id)->first();
        if ($entry['user_id']) {
            $mail_list = User::where('id',$entry['user_id'])->pluck('email')->toArray();
        }
        if (Vendors::where('id', $id)->delete()) {
            if ($entry['user_id']) {
                $mail_data = [
                    'vendor_name' => $entry['vendor_name'],
                    'email_list' => $mail_list
                ];
                $this->common_task->apiVendorEntryRejected($mail_data);
            }
            return response()->json(['status' => true,'msg' => "Vendor Entry successfully Rejected",'data' => []]);  
        }
        return response()->json(['status' => false,'msg' => "Oops, Something went wrong!",'data' => []]); 
        
    }

    #-------------------- Company
    public function api_company_entry_approval_list(Request $request)
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
        $select_fields = ['company.*'];
        $company_entry = Companies::where('status', 'Disabled')
                                  ->where('is_approved',0)
                                  ->get($select_fields);
        if ($company_entry->count() == 0) {
            return response()->json(['status' => false,'msg' => "No Record found.",'data' => []]);
        }
        foreach ($company_entry as $key => $company) {
            if ($company->moa_image) {
                $company_entry[$key]->moa_image = asset('storage/' . str_replace('public/', '', $company->moa_image));
            } else {
                $company_entry[$key]->moa_image = "";
            }
            //----------------------
            if ($company->gst_image) {
                $company_entry[$key]->gst_image = asset('storage/' . str_replace('public/', '', $company->gst_image));
            } else {
                $company_entry[$key]->gst_image = "";
            }
            //-------------------------
            if ($company->pan_image) {
                $company_entry[$key]->pan_image = asset('storage/' . str_replace('public/', '', $company->pan_image));
            } else {
                $company_entry[$key]->pan_image = "";
            }
            //------------------------
            if ($company->tan_image) {
                $company_entry[$key]->tan_image = asset('storage/' . str_replace('public/', '', $company->tan_image));
            } else {
                $company_entry[$key]->tan_image = "";
            }
        }
        $response_data['company_entry_list'] = $company_entry;
        return response()->json(['status' => true,'msg' => "Record found.",'data' => $response_data]);

    }

    public function api_accept_company_entry(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'company_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $id = $request_data['company_id'];
        $response_data = [];

        if (Companies::where('id', $id)->update(['status' => 'Enabled','is_approved' => 1])) {
        
            return response()->json(['status' => true,'msg' => "Company entry successfully Approved",'data' => []]);  
        }
        return response()->json(['status' => false,'msg' => "Oops, Something went wrong!",'data' => []]); 
        
    }

    public function api_reject_company_entry(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'company_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $id = $request_data['company_id'];
        $response_data = [];
        $entry = Companies::where('id',$id)->first();
        if ($entry['user_id']) {
            $mail_list = User::where('id',$entry['user_id'])->pluck('email')->toArray();
        }
        if (Companies::where('id', $id)->delete()) {
            if ($entry['user_id']) {
                $mail_data = [
                    'company_name' => $entry['company_name'],
                    'email_list' => $mail_list
                ];
                $this->common_task->apiCompanyEntryRejected($mail_data);
            }
            return response()->json(['status' => true,'msg' => "Company Entry successfully Rejected",'data' => []]);  
        }
        return response()->json(['status' => false,'msg' => "Oops, Something went wrong!",'data' => []]); 
        
    }

    #-------------------- Client
    public function api_client_entry_approval_list(Request $request)
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
        $select_fields = ['clients.*','company.company_name','tender.tender_sr_no'];
        $client_entry = Clients::with('clients_details')
                                ->leftjoin('company','clients.company_id','=','company.id')
                                ->leftjoin('tender','clients.tender_id','=','tender.id')
                                ->where('clients.status', 'Disabled')
                                ->where('clients.is_approved',0)
                                ->get($select_fields);
        if ($client_entry->count() == 0) {
            return response()->json(['status' => false,'msg' => "No Record found.",'data' => []]);
        }
        $response_data['client_entry_list'] = $client_entry;
        return response()->json(['status' => true,'msg' => "Record found.",'data' => $response_data]);

    }

    public function api_accept_client_entry(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'client_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $id = $request_data['client_id'];
        $response_data = [];

        if (Clients::where('id', $id)->update(['status' => 'Enabled','is_approved' => 1])) {
        
            return response()->json(['status' => true,'msg' => "Client entry successfully Approved",'data' => []]);  
        }
        return response()->json(['status' => false,'msg' => "Oops, Something went wrong!",'data' => []]); 
        
    }

    public function api_reject_client_entry(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'client_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $id = $request_data['client_id'];
        $response_data = [];
        $entry = Clients::where('id',$id)->first();
        if ($entry['user_id']) {
            $mail_list = User::where('id',$entry['user_id'])->pluck('email')->toArray();
        }
        if (Clients::where('id', $id)->delete()) {
            if (ClientDetail::where('client_id',$id)->get()->count() > 0) {
                ClientDetail::where('client_id',$id)->delete();
            }
            if ($entry['user_id']) {
                $mail_data = [
                    'client_name' => $entry['client_name'],
                    'email_list' => $mail_list
                ];
                $this->common_task->apiClientEntryRejected($mail_data);
            }
            return response()->json(['status' => true,'msg' => "Client Entry successfully Rejected",'data' => []]);  
        }
        return response()->json(['status' => false,'msg' => "Oops, Something went wrong!",'data' => []]); 
        
    }
    #-------------------- Project
    public function api_project_entry_approval_list(Request $request)
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
        $select_fields = ['project.*','company.company_name','clients.client_name'];
        $project_entry = Projects:://with('project_emp')
        with(['project_emp' => function($query){
            $query->leftjoin('users', 'users.id','=','project_manager.user_id')
                ->select(['project_id','user_id','users.name','is_manager']);
            }])
                    ->leftjoin('company','project.company_id','=','company.id')
                    ->leftjoin('clients','project.client_id','=','clients.id')
                    ->where('project.status', 'Disabled')
                    ->where('project.is_approved',0)
                    ->get($select_fields);
        if ($project_entry->count() == 0) {
            return response()->json(['status' => false,'msg' => "No Record found.",'data' => []]);
        }
        
        $response_data['project_entry_list'] = $project_entry;
        return response()->json(['status' => true,'msg' => "Record found.",'data' => $response_data]);

    }

    public function api_accept_project_entry(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'project_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $id = $request_data['project_id'];
        $response_data = [];

        if (Projects::where('id', $id)->update(['status' => 'Enabled','is_approved' => 1])) {
        
            return response()->json(['status' => true,'msg' => "Project entry successfully Approved",'data' => []]);  
        }
        return response()->json(['status' => false,'msg' => "Oops, Something went wrong!",'data' => []]); 
        
    }

    public function api_reject_project_entry(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'project_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $id = $request_data['project_id'];
        $response_data = [];
        $entry = Projects::where('id',$id)->first();
        if ($entry['user_id']) {
            $mail_list = User::where('id',$entry['user_id'])->pluck('email')->toArray();
        }
        if (Projects::where('id', $id)->delete()) {
            if (Site_manage_boq::where('project_id', $id)->get()->count() > 0) {
                Site_manage_boq::where('project_id', $id)->delete();
            }
            if (ProjectManager::where('project_id', $id)->get()->count() > 0) {
                ProjectManager::where('project_id', $id)->delete();
            }
            
            if ($entry['user_id']) {
                $mail_data = [
                    'project_name' => $entry['project_name'],
                    'email_list' => $mail_list
                ];
                $this->common_task->apiProjectEntryRejected($mail_data);
            }
            return response()->json(['status' => true,'msg' => "Project Entry successfully Rejected",'data' => []]);  
        }
        return response()->json(['status' => false,'msg' => "Oops, Something went wrong!",'data' => []]); 
        
    }
    #-----------------------Vendor Bank
    public function api_vendor_bank_entry_approval_list(Request $request)
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
        $select_fields = ['vendors_bank.*','company.company_name','vendor.vendor_name'];
        $vendor_bank_site_entry = Vendors_bank::leftjoin('company','vendors_bank.company_id','=','company.id')
                                  ->leftjoin('vendor','vendors_bank.vendor_id','=','vendor.id')
                                  ->where('vendors_bank.status', 'Disabled')
                                  ->where('vendors_bank.is_approved',0)
                                  ->get($select_fields);
        if ($vendor_bank_site_entry->count() == 0) {
            return response()->json(['status' => false,'msg' => "No Record found.",'data' => []]);
        }
        $response_data['vendor_bank_site_entry_list'] = $vendor_bank_site_entry;
        return response()->json(['status' => true,'msg' => "Record found.",'data' => $response_data]);

    }

    public function api_accept_vendor_bank_entry(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'vendor_bank_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $id = $request_data['vendor_bank_id'];
        $response_data = [];

        if (Vendors_bank::where('id', $id)->update(['status' => 'Enabled','is_approved' => 1])) {
        
            return response()->json(['status' => true,'msg' => "Vendor Bank entry successfully Approved",'data' => []]);  
        }
        return response()->json(['status' => false,'msg' => "Oops, Something went wrong!",'data' => []]); 
        
    }

    public function api_reject_vendor_bank_entry(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'vendor_bank_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $id = $request_data['vendor_bank_id'];
        $response_data = [];
        $entry = Vendors_bank::where('id',$id)->first();
        if ($entry['user_id']) {
            $mail_list = User::where('id',$entry['user_id'])->pluck('email')->toArray();
        }
        if (Vendors_bank::where('id', $id)->delete()) {
            if ($entry['user_id']) {
                $mail_data = [
                    'vendor_bank_name' => $entry['bank_name'],
                    'account_number' => $entry['ac_number'],
                    'email_list' => $mail_list
                ];
                $this->common_task->apiVendorBankEntryRejected($mail_data);
            }
            return response()->json(['status' => true,'msg' => "Vendor Bank Entry successfully Rejected",'data' => []]);  
        }
        return response()->json(['status' => false,'msg' => "Oops, Something went wrong!",'data' => []]); 
        
    }
    #---------------------- Project site
    public function api_project_site_entry_approval_list(Request $request)
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
        $select_fields = ['project_sites.*','company.company_name','clients.client_name','clients.location', 'project.project_name'];
        $project_site_entry = Project_sites::leftjoin('company','project_sites.company_id','=','company.id')
                                ->leftjoin('clients','project_sites.client_id','=','clients.id')
                                ->leftjoin('project','project_sites.project_id','=','project.id')
                                  ->where('project_sites.status', 'Disabled')
                                  ->where('project_sites.is_approved',0)
                                  ->get($select_fields);
        if ($project_site_entry->count() == 0) {
            return response()->json(['status' => false,'msg' => "No Record found.",'data' => []]);
        }
        $response_data['project_site_entry_list'] = $project_site_entry;
        return response()->json(['status' => true,'msg' => "Record found.",'data' => $response_data]);

    }

    public function api_accept_project_site_entry(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'project_site_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $id = $request_data['project_site_id'];
        $response_data = [];

        if (Project_sites::where('id', $id)->update(['status' => 'Enabled','is_approved' => 1])) {
        
            return response()->json(['status' => true,'msg' => "Project site entry successfully Approved",'data' => []]);  
        }
        return response()->json(['status' => false,'msg' => "Oops, Something went wrong!",'data' => []]); 
        
    }

    public function api_reject_project_site_entry(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'project_site_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $id = $request_data['project_site_id'];
        $response_data = [];
        $entry = Project_sites::where('id',$id)->first();
        if ($entry['user_id']) {
            $mail_list = User::where('id',$entry['user_id'])->pluck('email')->toArray();
        }
        if (Project_sites::where('id', $id)->delete()) {
            if ($entry['user_id']) {
                $mail_data = [
                    'project_site_name' => $entry['site_name'],
                    'project_site_address' => $entry['site_address'],
                    'email_list' => $mail_list
                ];
                $this->common_task->apiProjectSiteEntryRejected($mail_data);
            }
            return response()->json(['status' => true,'msg' => "Project site Entry successfully Rejected",'data' => []]);  
        }
        return response()->json(['status' => false,'msg' => "Oops, Something went wrong!",'data' => []]); 
        
    }
    #------------------------ Bank
    public function api_bank_entry_approval_list(Request $request)
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
        $select_fields = ['bank.*','company.company_name'];
        $bank_entry = Banks::leftjoin('company','bank.company_id','=','company.id')
                                  ->where('bank.status', 'Disabled')
                                  ->where('bank.is_approved',0)
                                  ->get($select_fields);
        if ($bank_entry->count() == 0) {
            return response()->json(['status' => false,'msg' => "No Record found.",'data' => []]);
        }
        $response_data['bank_entry_list'] = $bank_entry;
        return response()->json(['status' => true,'msg' => "Record found.",'data' => $response_data]);

    }

    public function api_accept_bank_entry(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'bank_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $id = $request_data['bank_id'];
        $response_data = [];

        if (Banks::where('id', $id)->update(['status' => 'Enabled','is_approved' => 1])) {
        
            return response()->json(['status' => true,'msg' => "Bank entry successfully Approved",'data' => []]);  
        }
        return response()->json(['status' => false,'msg' => "Oops, Something went wrong!",'data' => []]); 
        
    }

    public function api_reject_bank_entry(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'bank_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $id = $request_data['bank_id'];
        $response_data = [];
        $entry = Banks::where('id',$id)->first();
        if ($entry['user_id']) {
            $mail_list = User::where('id',$entry['user_id'])->pluck('email')->toArray();
        }
        if (Banks::where('id', $id)->delete()) {
            if ($entry['user_id']) {
                $mail_data = [
                    'bank_name' => $entry['bank_name'],
                    'account_number' => $entry['ac_number'],
                    'branch' => $entry['branch'],
                    'email_list' => $mail_list
                ];
                $this->common_task->apiBankEntryRejected($mail_data);
            }
            return response()->json(['status' => true,'msg' => "Bank Entry successfully Rejected",'data' => []]);  
        }
        return response()->json(['status' => false,'msg' => "Oops, Something went wrong!",'data' => []]); 
        
    }
    #---------------------- Bank Charge Category
    public function api_bank_charge_category_entry_approval_list(Request $request)
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
         $select_fields = ['bank_charge_category.*'];
         $bank_charge_category_entry = Bank_charge_category::where('is_approved',0)
                                   ->get($select_fields);
         if ($bank_charge_category_entry->count() == 0) {
             return response()->json(['status' => false,'msg' => "No Record found.",'data' => []]);
         }
         $response_data['bank_charge_category_entry_list'] = $bank_charge_category_entry;
         return response()->json(['status' => true,'msg' => "Record found.",'data' => $response_data]);
 
    }
 
    public function api_accept_bank_charge_category_entry(Request $request) {
         $validator = Validator::make($request->all(), [
                     'user_id' => 'required',
                     'bank_charge_category_id' => 'required'
         ]);
         if ($validator->fails()) {
             return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
         }
         $request_data = $request->all();
         $id = $request_data['bank_charge_category_id'];
         $response_data = [];
 
         if (Bank_charge_category::where('id', $id)->update(['is_approved' => 1])) {
         
             return response()->json(['status' => true,'msg' => "Bank charge category entry successfully Approved",'data' => []]);  
         }
         return response()->json(['status' => false,'msg' => "Oops, Something went wrong!",'data' => []]); 
         
    }
 
    public function api_reject_bank_charge_category_entry(Request $request) {
         $validator = Validator::make($request->all(), [
                     'user_id' => 'required',
                     'bank_charge_category_id' => 'required'
         ]);
         if ($validator->fails()) {
             return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
         }
         $request_data = $request->all();
         $id = $request_data['bank_charge_category_id'];
         $response_data = [];
         $entry = Bank_charge_category::where('id',$id)->first();
         if ($entry['user_id']) {
             $mail_list = User::where('id',$entry['user_id'])->pluck('email')->toArray();
         }
         if (Bank_charge_category::where('id', $id)->delete()) {
             if ($entry['user_id']) {
                 $mail_data = [
                    'title' => $entry['title'],
                    'email_list' => $mail_list
                 ];
                 $this->common_task->apiBankChargeCategoryEntryRejected($mail_data);
             }
             return response()->json(['status' => true,'msg' => "Bank charge category Entry successfully Rejected",'data' => []]);  
         }
         return response()->json(['status' => false,'msg' => "Oops, Something went wrong!",'data' => []]); 
         
    }
    #--------------------- Bank Charge sub-category
    public function api_bank_charge_sub_category_entry_approval_list(Request $request)
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
         $select_fields = ['bank_charge_sub_category.*','bank_charge_category.title as bank_charge_category_title'];
         $bank_charge_category_entry = Bank_charge_sub_category::leftjoin('bank_charge_category','bank_charge_sub_category.bank_charge_category_id','=','bank_charge_category.id')
                                   ->where('bank_charge_sub_category.is_approved',0)
                                   ->get($select_fields);
         if ($bank_charge_category_entry->count() == 0) {
             return response()->json(['status' => false,'msg' => "No Record found.",'data' => []]);
         }
         $response_data['bank_charge_category_list'] = $bank_charge_category_entry;
         return response()->json(['status' => true,'msg' => "Record found.",'data' => $response_data]);
 
    }
 
    public function api_accept_bank_charge_sub_category_entry(Request $request) {
         $validator = Validator::make($request->all(), [
                     'user_id' => 'required',
                     'bank_charge_sub_category_id' => 'required'
         ]);
         if ($validator->fails()) {
             return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
         }
         $request_data = $request->all();
         $id = $request_data['bank_charge_sub_category_id'];
         $response_data = [];
 
         if (Bank_charge_sub_category::where('id', $id)->update(['is_approved' => 1])) {
         
             return response()->json(['status' => true,'msg' => "Bank charge sub category entry successfully Approved",'data' => []]);  
         }
         return response()->json(['status' => false,'msg' => "Oops, Something went wrong!",'data' => []]); 
         
    }
 
    public function api_reject_bank_charge_sub_category_entry(Request $request) {
         $validator = Validator::make($request->all(), [
                     'user_id' => 'required',
                     'bank_charge_sub_category_id' => 'required'
         ]);
         if ($validator->fails()) {
             return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
         }
         $request_data = $request->all();
         $id = $request_data['bank_charge_sub_category_id'];
         $response_data = [];
         $entry = Bank_charge_sub_category::where('id',$id)->first();
         if ($entry['user_id']) {
             $mail_list = User::where('id',$entry['user_id'])->pluck('email')->toArray();
         }
         if (Bank_charge_sub_category::where('id', $id)->delete()) {
             if ($entry['user_id']) {
                 $mail_data = [
                    'title' => $entry['title'],
                    'email_list' => $mail_list
                 ];
                 $this->common_task->apiBankChargeSubCategoryEntryRejected($mail_data);
             }
             return response()->json(['status' => true,'msg' => "Bank charge sub category Entry successfully Rejected",'data' => []]);  
         }
         return response()->json(['status' => false,'msg' => "Oops, Something went wrong!",'data' => []]); 
         
    }
     #--------------------- Bank Charge sub-category
    public function api_payment_card_entry_approval_list(Request $request)
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
         $select_fields = ['payment_card.*','company.company_name', 'bank.bank_name', 'bank.ac_number'];
         $payment_card_entry = PaymentCard::leftjoin('company','payment_card.company_id','=','company.id')
                                    ->leftjoin('bank','payment_card.bank_id','=','bank.id')
                                    ->where('payment_card.status', 'Disabled')
                                    ->where('payment_card.is_approved',0)
                                    ->get($select_fields);
         if ($payment_card_entry->count() == 0) {
             return response()->json(['status' => false,'msg' => "No Record found.",'data' => []]);
         }
         $response_data['payment_card_entry_list'] = $payment_card_entry;
         return response()->json(['status' => true,'msg' => "Record found.",'data' => $response_data]);
 
    }
 
    public function api_accept_payment_card_entry(Request $request) {
         $validator = Validator::make($request->all(), [
                     'user_id' => 'required',
                     'payment_card_id' => 'required'
         ]);
        if ($validator->fails()) {
             return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
         $request_data = $request->all();
         $id = $request_data['payment_card_id'];
         $response_data = [];
 
        if (PaymentCard::where('id', $id)->update(['status' => 'Enabled', 'is_approved' => 1])) {
         
             return response()->json(['status' => true,'msg' => "Payment card entry successfully Approved",'data' => []]);  
        }
        return response()->json(['status' => false,'msg' => "Oops, Something went wrong!",'data' => []]); 
         
    }
 
    public function api_reject_payment_card_entry(Request $request) {
         $validator = Validator::make($request->all(), [
                     'user_id' => 'required',
                     'payment_card_id' => 'required'
         ]);
         if ($validator->fails()) {
             return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
         }
         $request_data = $request->all();
         $id = $request_data['payment_card_id'];
         $response_data = [];
         $entry = PaymentCard::where('id',$id)->first();
         if ($entry['user_id']) {
             $mail_list = User::where('id',$entry['user_id'])->pluck('email')->toArray();
         }
         if (PaymentCard::where('id', $id)->delete()) {
             if ($entry['user_id']) {
                 $mail_data = [
                    'card_number' => $entry['card_number'],
                    'name_on_card' => $entry['name_on_card'],
                    'email_list' => $mail_list
                 ];
                 $this->common_task->apiPaymentCardEntryRejected($mail_data);
             }
             return response()->json(['status' => true,'msg' => "Payment card Entry successfully Rejected",'data' => []]);  
         }
         return response()->json(['status' => false,'msg' => "Oops, Something went wrong!",'data' => []]); 
         
    }
    #--------------------- Company Document 
    public function api_company_document_entry_approval_list(Request $request)
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
         $select_fields = ['company_document_management.*','company.company_name', 'users.name as custodian_user'];
         $company_document_entry = CompanyDocumentManagement::leftjoin('company', 'company.id', '=', 'company_document_management.company_id')
                                    ->leftjoin('users', 'users.id', '=', 'company_document_management.custodian_id')
                                    ->where('company_document_management.status', 'Disabled')
                                    ->where('company_document_management.is_approved',0)
                                    ->get($select_fields);
        if ($company_document_entry->count() == 0) {
            return response()->json(['status' => false,'msg' => "No Record found.",'data' => []]);
        }
        foreach ($company_document_entry as $key => $value) {
            if ($value->file) {
                $company_document_entry[$key]->file = asset('storage/' . str_replace('public/', '', $value->file));
            } else {
                $company_document_entry[$key]->file = "";
            }
        }
        $response_data['company_document_entry_list'] = $company_document_entry;
        return response()->json(['status' => true,'msg' => "Record found.",'data' => $response_data]);
 
    }
 
    public function api_accept_company_document_entry(Request $request) {
         $validator = Validator::make($request->all(), [
                     'user_id' => 'required',
                     'company_document_id' => 'required'
         ]);
        if ($validator->fails()) {
             return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
         $request_data = $request->all();
         $id = $request_data['company_document_id'];
         $response_data = [];
 
        if (CompanyDocumentManagement::where('id', $id)->update(['status' => 'Enabled', 'is_approved' => 1])) {
         
             return response()->json(['status' => true,'msg' => "Company Document entry successfully Approved",'data' => []]);  
        }
        return response()->json(['status' => false,'msg' => "Oops, Something went wrong!",'data' => []]); 
         
    }
 
    public function api_reject_company_document_entry(Request $request) {
         $validator = Validator::make($request->all(), [
                     'user_id' => 'required',
                     'company_document_id' => 'required'
         ]);
         if ($validator->fails()) {
             return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
         }
         $request_data = $request->all();
         $id = $request_data['company_document_id'];
         $response_data = [];
         $entry = CompanyDocumentManagement::where('id',$id)->first();
         if ($entry['user_id']) {
             $mail_list = User::where('id',$entry['user_id'])->pluck('email')->toArray();
         }
         if (CompanyDocumentManagement::where('id', $id)->delete()) {
             if ($entry['user_id']) {
                 $mail_data = [
                    'title' => $entry['title'],
                    'email_list' => $mail_list
                 ];
                 $this->common_task->apiCompanyDocumentEntryRejected($mail_data);
             }
             return response()->json(['status' => true,'msg' => "Company Document Entry successfully Rejected",'data' => []]);  
         }
         return response()->json(['status' => false,'msg' => "Oops, Something went wrong!",'data' => []]); 
         
    }
    #------------------------ Tender category
    public function api_tender_category_entry_approval_list(Request $request)
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
        $select_fields = ['tender_category.*'];
        $tender_category_entry = TenderCategory::where('status', 'Disabled')
                                ->where('is_approved',0)
                                ->get($select_fields);
        if ($tender_category_entry->count() == 0) {
            return response()->json(['status' => false,'msg' => "No Record found.",'data' => []]);
        }

        $response_data['tender_category_entry_list'] = $tender_category_entry;
        return response()->json(['status' => true,'msg' => "Record found.",'data' => $response_data]);
 
    }
 
    public function api_accept_tender_category_entry(Request $request) {
         $validator = Validator::make($request->all(), [
                     'user_id' => 'required',
                     'tender_category_id' => 'required'
         ]);
        if ($validator->fails()) {
             return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
         $request_data = $request->all();
         $id = $request_data['tender_category_id'];
         $response_data = [];
 
        if (TenderCategory::where('id', $id)->update(['status' => 'Enabled', 'is_approved' => 1])) {
         
             return response()->json(['status' => true,'msg' => "Tender category entry successfully Approved",'data' => []]);  
        }
        return response()->json(['status' => false,'msg' => "Oops, Something went wrong!",'data' => []]); 
         
    }
 
    public function api_reject_tender_category_entry(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'tender_category_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $id = $request_data['tender_category_id'];
        $response_data = [];
        $entry = TenderCategory::where('id',$id)->first();
        if ($entry['user_id']) {
            $mail_list = User::where('id',$entry['user_id'])->pluck('email')->toArray();
        }
        if (TenderCategory::where('id', $id)->delete()) {
            if ($entry['user_id']) {
                $mail_data = [
                'title' => $entry['tender_category'],
                'email_list' => $mail_list
                ];
                $this->common_task->apiTenderCategoryEntryRejected($mail_data);
            }
            return response()->json(['status' => true,'msg' => "Tender category Entry successfully Rejected",'data' => []]);  
        }
        return response()->json(['status' => false,'msg' => "Oops, Something went wrong!",'data' => []]); 
         
    }
     #------------------------ Tender Pattern
    public function api_tender_pattern_entry_approval_list(Request $request)
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
        $select_fields = ['tender_pattern.*'];
        $tender_pattern_entry = TenderPattern::where('status', 'Disabled')
                                ->where('is_approved',0)
                                ->get($select_fields);
        if ($tender_pattern_entry->count() == 0) {
            return response()->json(['status' => false,'msg' => "No Record found.",'data' => []]);
        }

        $response_data['tender_pattern_entry_list'] = $tender_pattern_entry;
        return response()->json(['status' => true,'msg' => "Record found.",'data' => $response_data]);

    }
  
    public function api_accept_tender_pattern_entry(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'tender_pattern_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $id = $request_data['tender_pattern_id'];
        $response_data = [];

        if (TenderPattern::where('id', $id)->update(['status' => 'Enabled', 'is_approved' => 1])) {
        
            return response()->json(['status' => true,'msg' => "Tender pattern entry successfully Approved",'data' => []]);  
        }
        return response()->json(['status' => false,'msg' => "Oops, Something went wrong!",'data' => []]); 
        
    }
  
    public function api_reject_tender_pattern_entry(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'tender_pattern_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $id = $request_data['tender_pattern_id'];
        $response_data = [];
        $entry = TenderPattern::where('id',$id)->first();
        if ($entry['user_id']) {
            $mail_list = User::where('id',$entry['user_id'])->pluck('email')->toArray();
        }
        if (TenderPattern::where('id', $id)->delete()) {
            if ($entry['user_id']) {
                $mail_data = [
                'title' => $entry['tender_pattern_name'],
                'email_list' => $mail_list
                ];
                $this->common_task->apiTenderPatternEntryRejected($mail_data);
            }
            return response()->json(['status' => true,'msg' => "Tender pattern Entry successfully Rejected",'data' => []]);  
        }
        return response()->json(['status' => false,'msg' => "Oops, Something went wrong!",'data' => []]); 
          
    }
      #------------------------ Tender category
    public function api_tender_physical_submission_entry_approval_list(Request $request)
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
        $select_fields = ['tender_physical_submission.*'];
        $tender_physical_submission_entry = Tender_physical_submission::where('status', 'Disabled')
                                ->where('is_approved',0)
                                ->get($select_fields);
        if ($tender_physical_submission_entry->count() == 0) {
            return response()->json(['status' => false,'msg' => "No Record found.",'data' => []]);
        }

        $response_data['tender_physical_submission_entry_list'] = $tender_physical_submission_entry;
        return response()->json(['status' => true,'msg' => "Record found.",'data' => $response_data]);
 
    }
 
    public function api_accept_tender_physical_submission_entry(Request $request) {
        $validator = Validator::make($request->all(), [
                     'user_id' => 'required',
                     'tender_physical_submission_id' => 'required'
        ]);
        if ($validator->fails()) {
             return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
         $request_data = $request->all();
         $id = $request_data['tender_physical_submission_id'];
         $response_data = [];
 
        if (Tender_physical_submission::where('id', $id)->update(['status' => 'Enabled', 'is_approved' => 1])) {
         
             return response()->json(['status' => true,'msg' => "Tender physical submission entry successfully Approved",'data' => []]);  
        }
        return response()->json(['status' => false,'msg' => "Oops, Something went wrong!",'data' => []]); 
         
    }
 
    public function api_reject_tender_physical_submission_entry(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'tender_physical_submission_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $id = $request_data['tender_physical_submission_id'];
        $response_data = [];
        $entry = Tender_physical_submission::where('id',$id)->first();
        if ($entry['user_id']) {
            $mail_list = User::where('id',$entry['user_id'])->pluck('email')->toArray();
        }
        if (Tender_physical_submission::where('id', $id)->delete()) {
            if ($entry['user_id']) {
                $mail_data = [
                'title' => $entry['mode_name'],
                'email_list' => $mail_list
                ];
                $this->common_task->apiTenderPhysicalEntryRejected($mail_data);
            }
            return response()->json(['status' => true,'msg' => "Tender physical submission Entry successfully Rejected",'data' => []]);  
        }
        return response()->json(['status' => false,'msg' => "Oops, Something went wrong!",'data' => []]); 
         
    }
    #-------------------- Registry Category
    public function api_regitry_category_entry_approval_list(Request $request)
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
        $select_fields = ['inward_outward_doc_category.*'];
        $registry_category_entry = Inward_outward_doc_category::where('status', 'Disabled')
                                ->where('is_approved',0)
                                ->get($select_fields);
        if ($registry_category_entry->count() == 0) {
            return response()->json(['status' => false,'msg' => "No Record found.",'data' => []]);
        }

        $response_data['registry_category_entry_list'] = $registry_category_entry;
        return response()->json(['status' => true,'msg' => "Record found.",'data' => $response_data]);
 
    }
 
    public function api_accept_regitry_category_entry(Request $request) {
        $validator = Validator::make($request->all(), [
                     'user_id' => 'required',
                     'registry_category_id' => 'required'
        ]);
        if ($validator->fails()) {
             return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
         $request_data = $request->all();
         $id = $request_data['registry_category_id'];
         $response_data = [];
 
        if (Inward_outward_doc_category::where('id', $id)->update(['status' => 'Enabled', 'is_approved' => 1])) {
         
             return response()->json(['status' => true,'msg' => "Registry Category entry successfully Approved",'data' => []]);  
        }
        return response()->json(['status' => false,'msg' => "Oops, Something went wrong!",'data' => []]); 
         
    }
 
    public function api_reject_regitry_category_entry(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'registry_category_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $id = $request_data['registry_category_id'];
        $response_data = [];
        $entry = Inward_outward_doc_category::where('id',$id)->first();
        if ($entry['user_id']) {
            $mail_list = User::where('id',$entry['user_id'])->pluck('email')->toArray();
        }
        if (Inward_outward_doc_category::where('id', $id)->delete()) {
            if ($entry['user_id']) {
                $mail_data = [
                'title' => $entry['category_name'],
                'email_list' => $mail_list
                ];
                $this->common_task->apiRegistryCategoryEntryRejected($mail_data);
            }
            return response()->json(['status' => true,'msg' => "Registry Category Entry successfully Rejected",'data' => []]);  
        }
        return response()->json(['status' => false,'msg' => "Oops, Something went wrong!",'data' => []]); 
         
    }
    #------------------ Registry sub-category
    public function api_regitry_subcategory_entry_approval_list(Request $request)
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
        $select_fields = ['inward_outward_doc_sub_category.*','inward_outward_doc_category.category_name'];
        $registry_sub_category_entry = Inward_outward_doc_sub_category::join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outward_doc_sub_category.category_id')
                                ->where('inward_outward_doc_sub_category.status', 'Disabled')
                                ->where('inward_outward_doc_sub_category.is_approved',0)
                                ->get($select_fields);
        if ($registry_sub_category_entry->count() == 0) {
            return response()->json(['status' => false,'msg' => "No Record found.",'data' => []]);
        }

        $response_data['registry_sub_category_entry_list'] = $registry_sub_category_entry;
        return response()->json(['status' => true,'msg' => "Record found.",'data' => $response_data]);
 
    }
 
    public function api_accept_regitry_subcategory_entry(Request $request) {
        $validator = Validator::make($request->all(), [
                     'user_id' => 'required',
                     'registry_sub_category_id' => 'required'
        ]);
        if ($validator->fails()) {
             return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $id = $request_data['registry_sub_category_id'];
        $response_data = [];
 
        if (Inward_outward_doc_sub_category::where('id', $id)->update(['status' => 'Enabled', 'is_approved' => 1])) {
         
             return response()->json(['status' => true,'msg' => "Registry Sub category entry successfully Approved",'data' => []]);  
        }
        return response()->json(['status' => false,'msg' => "Oops, Something went wrong!",'data' => []]); 
         
    }
 
    public function api_reject_regitry_subcategory_entry(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'registry_sub_category_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $id = $request_data['registry_sub_category_id'];
        $response_data = [];
        $entry = Inward_outward_doc_sub_category::where('id',$id)->first();
        if ($entry['user_id']) {
            $mail_list = User::where('id',$entry['user_id'])->pluck('email')->toArray();
        }
        if (Inward_outward_doc_sub_category::where('id', $id)->delete()) {
            if ($entry['user_id']) {
                $mail_data = [
                'title' => $entry['sub_category_name'],
                'email_list' => $mail_list
                ];
                $this->common_task->apiRegistrySubCategoryEntryRejected($mail_data);
            }
            return response()->json(['status' => true,'msg' => "Registry sub category Entry successfully Rejected",'data' => []]);  
        }
        return response()->json(['status' => false,'msg' => "Oops, Something went wrong!",'data' => []]); 
         
    }
    #---------------------- Delivery Mode category
    public function api_delivery_mode_entry_approval_list(Request $request)
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
        $select_fields = ['inward_outward_delivery_mode.*'];
        $delivery_mode_entry = Inward_outward_delivery_mode::where('status', 'Disabled')
                                ->where('is_approved',0)
                                ->get($select_fields);
        if ($delivery_mode_entry->count() == 0) {
            return response()->json(['status' => false,'msg' => "No Record found.",'data' => []]);
        }

        $response_data['delivery_mode_entry_list'] = $delivery_mode_entry;
        return response()->json(['status' => true,'msg' => "Record found.",'data' => $response_data]);
 
    }
 
    public function api_accept_delivery_mode_entry(Request $request) {
        $validator = Validator::make($request->all(), [
                     'user_id' => 'required',
                     'delivery_mode_id' => 'required'
        ]);
        if ($validator->fails()) {
             return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
         $request_data = $request->all();
         $id = $request_data['delivery_mode_id'];
         $response_data = [];
 
        if (Inward_outward_delivery_mode::where('id', $id)->update(['status' => 'Enabled', 'is_approved' => 1])) {
         
             return response()->json(['status' => true,'msg' => "Delivery Mode entry successfully Approved",'data' => []]);  
        }
        return response()->json(['status' => false,'msg' => "Oops, Something went wrong!",'data' => []]); 
         
    }
 
    public function api_reject_delivery_mode_entry(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'delivery_mode_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $id = $request_data['delivery_mode_id'];
        $response_data = [];
        $entry = Inward_outward_delivery_mode::where('id',$id)->first();
        if ($entry['user_id']) {
            $mail_list = User::where('id',$entry['user_id'])->pluck('email')->toArray();
        }
        if (Inward_outward_delivery_mode::where('id', $id)->delete()) {
            if ($entry['user_id']) {
                $mail_data = [
                'title' => $entry['name'],
                'email_list' => $mail_list
                ];
                $this->common_task->apiDeliveryModeEntryRejected($mail_data);
            }
            return response()->json(['status' => true,'msg' => "Delivery Mode Entry successfully Rejected",'data' => []]);  
        }
        return response()->json(['status' => false,'msg' => "Oops, Something went wrong!",'data' => []]); 
         
    }
    #------------------Sender category
    public function api_sender_category_entry_approval_list(Request $request)
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
        $select_fields = ['sender.*'];
        $sender_entry = Sender::where('status', 'Disabled')
                                ->where('is_approved',0)
                                ->get($select_fields);
        if ($sender_entry->count() == 0) {
            return response()->json(['status' => false,'msg' => "No Record found.",'data' => []]);
        }

        $response_data['sender_entry_list'] = $sender_entry;
        return response()->json(['status' => true,'msg' => "Record found.",'data' => $response_data]);
 
    }
 
    public function api_accept_sender_category_entry(Request $request) {
        $validator = Validator::make($request->all(), [
                     'user_id' => 'required',
                     'sender_id' => 'required'
        ]);
        if ($validator->fails()) {
             return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
         $request_data = $request->all();
         $id = $request_data['sender_id'];
         $response_data = [];
 
        if (Sender::where('id', $id)->update(['status' => 'Enabled', 'is_approved' => 1])) {
         
            return response()->json(['status' => true,'msg' => "Sender entry successfully Approved",'data' => []]);  
        }
        return response()->json(['status' => false,'msg' => "Oops, Something went wrong!",'data' => []]); 
         
    }
 
    public function api_reject_sender_category_entry(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'sender_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $id = $request_data['sender_id'];
        $response_data = [];
        $entry = Sender::where('id',$id)->first();
        if ($entry['user_id']) {
            $mail_list = User::where('id',$entry['user_id'])->pluck('email')->toArray();
        }
        if (Sender::where('id', $id)->delete()) {
            if ($entry['user_id']) {
                $mail_data = [
                'title' => $entry['name'],
                'email_list' => $mail_list
                ];
                $this->common_task->apiSenderEntryRejected($mail_data);
            }
            return response()->json(['status' => true,'msg' => "Sender Entry successfully Rejected",'data' => []]);  
        }
        return response()->json(['status' => false,'msg' => "Oops, Something went wrong!",'data' => []]); 
         
    }

   
}
