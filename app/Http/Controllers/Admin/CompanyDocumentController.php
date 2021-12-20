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
use App\CompanyDocument;
use Illuminate\Support\Facades\Mail;
use App\Lib\CommonTask;
use App\Companies;
use App\Projects;
use App\Vendors;
use App\Vendors_bank;
use App\PaymentCard;

class CompanyDocumentController extends Controller {

    public $data;
    public $notification_task;
    public $common_task;
    private $super_admin;
    private $module_id = 49;

    public function __construct() {
        $this->data['module_title'] = "Company Project Document";
        $this->data['module_link'] = "admin.company_document_list";
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();
        $this->super_admin = \App\User::where('role', 1)->first();
    }

    public function company_document_list(Request $request) {
        $this->data['page_title'] = "Company Project Documents";
        $access_level = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => $this->module_id])->get()->first();
        $this->data['access_rule'] = '';
        if (!empty($access_level)) {
            $this->data['access_rule'] = $access_level->access_level;
        }
        $request_data = !empty($request->all()) ? $request->all() : [];
        if (empty($request_data)) {
            $document_type = 'Tender';
            $company_id = 0;
            $project_id = 0;
        } else {
            $document_type = $request_data['document_type'];
            $company_id = !empty($request_data['company_id']) ? $request_data['company_id'] : 0;
            $project_id = !empty($request_data['project_id']) ? $request_data['project_id'] : 0;
        }

        $this->data['document_type'] = $document_type;

        $this->data['company_id'] = $company_id;
        $this->data['project_id'] = $project_id;

        $this->data['company_document_form_data'] = $request_data;
        $query = DB::table('company_document')
                ->leftjoin('company', 'company.id', '=', 'company_document.company_id')
                ->leftjoin('project', 'project.id', '=', 'company_document.project_id')
                ->where('document_type', $document_type);

        if ($company_id != 0) {
            $query = $query->where('company_document.company_id', $company_id);
        }

        if ($project_id != 0) {
            $query = $query->where('company_document.project_id', $project_id);
        }

        $this->data['company_document_list'] = $query->get(['company_document.*', 'company.company_name', 'project.project_name'])->toArray();
        /* echo "<pre>";
          print_r($this->data['company_document_list']);
          die(); */
        $this->data['Companies'] = Companies::select('id', 'company_name')->get();
        $this->data['Projects'] = ($company_id != 0 && $company_id != '0') ? Projects::select('id', 'project_name')->where('company_id', $company_id)->get() : [];
        return view('admin.company_document.company_document_list', $this->data);
    }

    public function add_company_document_list(Request $request) {

        $bank_payment_add_permission = Permissions::checkPermission($this->module_id, 3);

        if (!$bank_payment_add_permission) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You do not have access to this module.');
        }

        $this->data['page_title'] = 'Add Company Document';
        $this->data['Companies'] = Companies::select('id', 'company_name')->get();
        $this->data['Projects'] = Projects::select('id', 'project_name')->get();
        $request_data = $request->all();

        $this->data['document_type'] = $request_data['document_type'];

        return view('admin.company_document.add_company_document', $this->data);
    }

    public function insert_company_document_list(Request $request) {

        $validator_normal = Validator::make($request->all(), [
                    'project_id' => 'required',
                    'company_id' => 'required',
                    'document_type' => 'required',
                    'document_detail' => 'required',
                    'document_title' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_company_document_list')->with('error', 'Please follow validation rules.');
        }

        $request_data = $request->all();

        //upload document
        $company_document_file = '';
        if ($request->hasFile('company_document_file')) {
            $doc = $request->file('company_document_file');

            $original_file_name = explode('.', $doc->getClientOriginalName());

            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);
            $file_path = $doc->storeAs('public/company_document_file', $new_file_name);
            //$file_path = $doc->store('public/company_document_file');
            if ($file_path) {
                $company_document_file = $file_path;
            }
        }

        $CompanyDocument = new CompanyDocument();
        $CompanyDocument->company_id = $request->input('company_id');
        $CompanyDocument->project_id = $request->input('project_id');
        $CompanyDocument->document_type = $request->input('document_type');
        $CompanyDocument->doc_detail = $request->input('document_detail');
        $CompanyDocument->doc_title = $request->input('document_title');
        $CompanyDocument->document_file = $company_document_file;
        $CompanyDocument->created_at = date('Y-m-d H:i:s');
        $CompanyDocument->updated_at = date('Y-m-d H:i:s');
        $CompanyDocument->save();

        return redirect()->route('admin.company_document_list', ['document_type' => $request->input('document_type')])->with('success', 'Payment Online Details added successfully.');
    }

    public function edit_company_document($id) {

        $bank_payment_add_permission = Permissions::checkPermission($this->module_id, 3);

        if (!$bank_payment_add_permission) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You do not have access to this module.');
        }

        $this->data['page_title'] = 'Add Company Document';
        $this->data['Companies'] = Companies::select('id', 'company_name')->get();
        $this->data['Projects'] = Projects::select('id', 'project_name')->get();
        $this->data['company_document_list'] = DB::table('company_document')
                        ->leftjoin('company', 'company.id', '=', 'company_document.company_id')
                        ->leftjoin('project', 'project.id', '=', 'company_document.project_id')
                        ->where('company_document.id', $id)
                        ->get(['company_document.*', 'company.company_name', 'project.project_name'])->toArray();

        return view('admin.company_document.edit_company_document', $this->data);
    }

    public function update_company_document(Request $request) {

        $validator_normal = Validator::make($request->all(), [
                    'project_id' => 'required',
                    'company_id' => 'required',
                    'document_detail' => 'required',
                    'document_title' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.edit_company_document')->with('error', 'Please follow validation rules.');
        }

        $request_data = $request->all();

        //upload document
        if ($request->hasFile('company_document_file')) {
            $doc = $request->file('company_document_file');
            
            $original_file_name = explode('.', $doc->getClientOriginalName());

            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);
            $file_path = $doc->storeAs('public/company_document_file', $new_file_name);
            
            //$file_path = $doc->store('public/company_document_file');
            if ($file_path) {
                $company_document_file = $file_path;
                $fileData = ['document_file' => $company_document_file];
                CompanyDocument::where('id', $request->input('id'))->update($fileData);
            }
        }

        $company_document_model = [
            'company_id' => $request->input('company_id'),
            'project_id' => $request->input('project_id'),
            'doc_detail' => $request->input('document_detail'),
            'doc_title' => $request->input('document_title'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        CompanyDocument::where('id', $request->input('id'))->update($company_document_model);

        return redirect()->route('admin.company_document_list', ['document_type' => $request->input('document_type')])->with('success', 'Online Payment successfully updated.');
    }

}
