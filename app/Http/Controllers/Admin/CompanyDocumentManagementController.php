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
use App\Policy;
use App\Lib\Permissions;
use App\Lib\NotificationTask;
use App\Email_format;
use App\Mail\Mails;
use App\Role_module;
use App\RevisePolicy;
use App\UserRevisePolicy;
use Illuminate\Support\Facades\Mail;
use App\Lib\CommonTask;
use App\Companies;
use App\User;
use App\CompanyDocumentManagement;
use App\Lib\UserActionLogs;

class CompanyDocumentManagementController extends Controller {

    public $data;
    public $notification_task;
    public $common_task;
    public $user_action_logs;
    private $super_admin;
    private $module_id = 60;

    public function __construct() {
        $this->data['module_title'] = "Company Document Management";
        $this->data['module_link'] = "admin.company_document_management";
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();
        $this->user_action_logs = new UserActionLogs();
        $this->super_admin = \App\User::where('role', 1)->first();
    }

    public function company_document_management(Request $request) {
        $this->data['page_title'] = "Company Documents Management";

        $company_doc_add_permission = Permissions::checkPermission($this->module_id, 3);
        $company_doc_edit_permission = Permissions::checkPermission($this->module_id, 2);

        if (!Permissions::checkPermission($this->module_id, 5)) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You do not have access to this module.');
        }
        $this->data['view_special_permission'] = Permissions::checkSpecialPermission($this->module_id);
        $this->data['company_doc_add_permission'] = $company_doc_add_permission;
        $this->data['company_doc_edit_permission'] = $company_doc_edit_permission;


        $query = DB::table('company_document_management')
                ->where('company_document_management.is_approved',1)
                ->leftjoin('company', 'company.id', '=', 'company_document_management.company_id')
                ->leftjoin('users', 'users.id', '=', 'company_document_management.custodian_id');

        $this->data['company_document_management'] = $query->get(['company_document_management.*', 'company.company_name', 'users.name'])->toArray();

        return view('admin.company_document_management.company_document_management', $this->data);
    }

    public function add_company_document_management(Request $request) {

        $company_doc_add_permission = Permissions::checkPermission($this->module_id, 3);

        if (!$company_doc_add_permission) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You do not have access to this module.');
        }
        $view_special_permission = Permissions::checkSpecialPermission($this->module_id);
        if(!$view_special_permission){
            return redirect()->route('admin.dashboard')->with('error','Access denied. You do not have permission to access this module.');
        }

        $this->data['page_title'] = 'Add Company Document Management';
        $this->data['Companies'] = Companies::orderBy('company_name')->select('id', 'company_name')->get();
        $this->data['users'] = User::orderBy('name')->select('id', 'name')->where('status', 'Enabled')->get();

        $request_data = $request->all();

        return view('admin.company_document_management.add_company_document_management', $this->data);
    }

    public function insert_company_document_management(Request $request) {
        $validator_normal = Validator::make($request->all(), [
                    'company_id' => 'required',
                    'title' => 'required',
                    'custodian_id' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_company_document_management')->with('error', 'Please follow validation rules.');
        }

        $request_data = $request->all();

        //upload document
        $company_document_file = '';
        if ($request->hasFile('file')) {

            $doc = $request->file('file');

            $original_file_name = explode('.', $doc->getClientOriginalName());

            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);
            $file_path = $doc->storeAs('public/company_document_management_file', $new_file_name);
            if ($file_path) {
                $company_document_file = $file_path;
            }
        }

        $CompanyDocument = new CompanyDocumentManagement();
        $CompanyDocument->user_id = Auth::user()->id;
        $CompanyDocument->company_id = $request->input('company_id');
        $CompanyDocument->custodian_id = $request->input('custodian_id');
        $CompanyDocument->title = $request->input('title');
        $CompanyDocument->description = $request->input('description');
        $CompanyDocument->file = $company_document_file;
        if (Auth::user()->role != config('constants.SuperUser')) {
            $CompanyDocument->status = 'Disabled';
            $CompanyDocument->is_approved = 0;
        } else {
            $CompanyDocument->status = 'Enabled';
            $CompanyDocument->is_approved = 1;
        }
        $CompanyDocument->created_at = date('Y-m-d H:i:s');
        $CompanyDocument->updated_at = date('Y-m-d H:i:s');
        $CompanyDocument->save();
        $module = 'Company Document';
        $this->notification_task->entryApprovalNotify($module);

        // User Action Log
        $company_name = Companies::whereId($request->get('company_id'))->value('company_name');
        $custodian_name = User::whereId($request->get('custodian_id'))->value('name');
        $add_string = "<br> Company Name: ".$company_name."<br> Document Title: ".$request_data['title']."<br> Custodian Name: ".$custodian_name;
        $action_data = [
            'user_id' => Auth::user()->id,
            'task_body' => "Company document added".$add_string,
            'created_ip' => $request->ip(),
        ];
        $this->user_action_logs->action($action_data);

        return redirect()->route('admin.company_document_management')->with('success', 'Company Document added successfully.');
    }

    public function edit_company_document_management($id) {

        $company_doc_edit_permission = Permissions::checkPermission($this->module_id, 2);

        if (!$company_doc_edit_permission) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You do not have access to this module.');
        }
        $view_special_permission = Permissions::checkSpecialPermission($this->module_id);
        if(!$view_special_permission){
            return redirect()->route('admin.dashboard')->with('error','Access denied. You do not have permission to access this module.');
        }
        $this->data['page_title'] = 'Add Company Document';
        $this->data['Companies'] = Companies::orderBy('company_name')->select('id', 'company_name')->get();
        $this->data['users'] = User::orderBy('name')->select('id', 'name')->where('status', 'Enabled')->get();

        $this->data['company_document_management'] = DB::table('company_document_management')->where('company_document_management.id', $id)->get()->first();

        return view('admin.company_document_management.edit_company_document_management', $this->data);
    }

    public function update_company_document_management(Request $request) {

        $validator_normal = Validator::make($request->all(), [
                    'company_id' => 'required',
                    'title' => 'required',
                    'custodian_id' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.edit_company_document_management')->with('error', 'Please follow validation rules.');
        }

        $request_data = $request->all();

        //upload document
        if ($request->hasFile('file')) {
            $doc = $request->file('file');

            $original_file_name = explode('.', $doc->getClientOriginalName());

            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);
            $file_path = $doc->storeAs('public/company_document_management_file', $new_file_name);

            if ($file_path) {
                $company_document_file = $file_path;
                $fileData = ['file' => $company_document_file];
                CompanyDocumentManagement::where('id', $request->input('id'))->update($fileData);
            }
        }
        $company_document_model = [
            'user_id' => Auth::user()->id,
            'company_id' => $request->input('company_id'),
            'custodian_id' => $request->input('custodian_id'),
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        CompanyDocumentManagement::where('id', $request->input('id'))->update($company_document_model);

        // User Action Log
        $company_name = Companies::whereId($request->get('company_id'))->value('company_name');
        $custodian_name = User::whereId($request->get('custodian_id'))->value('name');
        $add_string = "<br> Company Name: ".$company_name."<br> Document Title: ".$request_data['title']."<br> Custodian Name: ".$custodian_name;
        $action_data = [
            'user_id' => Auth::user()->id,
            'task_body' => "Company document updated".$add_string,
            'created_ip' => $request->ip(),
        ];
        $this->user_action_logs->action($action_data);

        return redirect()->route('admin.company_document_management')->with('success', 'Company Document successfully updated.');
    }

}
