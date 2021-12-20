<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Config;
use App\Common_query;
use App\Role_module;
use Illuminate\Support\Facades\Validator;
use App\Imports\EmployeeSalaryImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use App\CompanyDocumentManagement;
use App\CompanyDocumentRequest;
use App\Companies;
use App\User;
use DB;
use App\Lib\Permissions;
use App\Lib\CommonTask;
use App\Lib\NotificationTask;
use App\Lib\UserActionLogs;
use Yajra\DataTables\Facades\DataTables;

class CompanyDocumentRequestController extends Controller {

    public $data;
    public $common_task;
    public $notification_task;
    public $user_action_logs;
    private $module_id;

    public function __construct() {
        $this->data['module_title'] = "Company Document Request";
        $this->data['module_link'] = "admin.company_document_request";
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();
        $this->user_action_logs = new UserActionLogs();
        $this->module_id = 61;
    }

    public function index() {

        $this->data['page_title'] = "Company Document Request";
        $this->data['access_rule'] = '';
        $access_level = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => $this->module_id])->get()->first();

        $this->data['request_list'] = CompanyDocumentRequest::select()->get();

        return view('admin.company_document_request.company_document_request', $this->data);
    }

    public function get_company_document_request(Request $request) {

        $check_resultF = Permissions::checkPermission($this->module_id, 5); // Full View

        $conditions_array = [];

        $or_where = [];

        if(Auth::user()->role != config('constants.SuperUser')){
            $or_where = [
                'company_document_request.request_user_id' => Auth::user()->id,
                'company_document_request.custodian_id' => Auth::user()->id,
            ];
        }

        // $datatable_fields = array('company_document_management.title as document_title', 'users.name as user_name', 'company.company_name', 'company_document_request.require_date', 'company_document_request.return_date', 'company_document_request.work_detail', 'company_document_request.request_datetime', 'company_document_request.confirm_submitted_date', 'company_document_request.actual_return_date', 'company_document_request.return_received_datetime', 'company_document_request.superadmin_status', 'company_document_request.superadmin_approval_datetime', 'company_document_request.custodian_approval_status', 'company_document_request.custodian_approval_datetime', 'company_document_request.status');
        // $request = Input::all();

        // $getfiled = array('company_document_management.title as document_title', 'users.name as user_name', 'company.company_name as company_name', 'company_document_request.id', 'company_document_request.request_user_id', 'company_document_request.require_date', 'company_document_request.return_date','company_document_request.work_detail','company_document_request.request_status', 'company_document_request.request_datetime', 'company_document_request.confirm_submitted_date', 'company_document_request.actual_return_date', 'company_document_request.return_received_datetime', 'company_document_request.superadmin_status', 'company_document_request.superadmin_approval_datetime', 'company_document_request.custodian_approval_status', 'company_document_request.custodian_approval_datetime', 'company_document_request.return_received_datetime', 'company_document_request.document_id', 'company_document_request.request_user_id', 'company_document_request.company_id', 'company_document_request.custodian_id', 'company_document_request.status');
        // $table = "company_document_request";

        // $join_str[0]['join_type'] = '';
        // $join_str[0]['table'] = 'company_document_management';
        // $join_str[0]['join_table_id'] = 'company_document_management.id';
        // $join_str[0]['from_table_id'] = 'company_document_request.document_id';

        // $join_str[1]['join_type'] = '';
        // $join_str[1]['table'] = 'users';
        // $join_str[1]['join_table_id'] = 'users.id';
        // $join_str[1]['from_table_id'] = 'company_document_request.request_user_id';

        // $join_str[2]['join_type'] = '';
        // $join_str[2]['table'] = 'company';
        // $join_str[2]['join_table_id'] = 'company.id';
        // $join_str[2]['from_table_id'] = 'company_document_request.company_id';

        // echo CompanyDocumentRequest::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str, [], $or_where);

        // die();
       
        if($request->ajax()){
            $getfiled = array('company_document_management.title as document_title', 'users.name as user_name', 'company.company_name', 'company_document_request.id', 'company_document_request.request_user_id', 'company_document_request.require_date', 'company_document_request.return_date','company_document_request.work_detail','company_document_request.request_status', 'company_document_request.request_datetime', 'company_document_request.confirm_submitted_date', 'company_document_request.actual_return_date', 'company_document_request.return_received_datetime', 'company_document_request.superadmin_status', 'company_document_request.superadmin_approval_datetime', 'company_document_request.custodian_approval_status', 'company_document_request.custodian_approval_datetime', 'company_document_request.return_received_datetime', 'company_document_request.document_id', 'company_document_request.request_user_id', 'company_document_request.company_id', 'company_document_request.custodian_id', 'company_document_request.status');
            $list_query = CompanyDocumentRequest::join('company_document_management', 'company_document_management.id', '=', 'company_document_request.document_id')
            ->join('users', 'users.id', '=', 'company_document_request.request_user_id')
            ->join('company', 'company.id', '=', 'company_document_request.company_id');
            if(Auth::user()->role != config('constants.SuperUser')){
                $list_query->Orwhere([
                    'company_document_request.request_user_id' => Auth::user()->id,
                    'company_document_request.custodian_id' => Auth::user()->id,
                ]);
            }
            $list_ajax = $list_query->get($getfiled);
            return DataTables::of($list_ajax)->make(true);
            
            // return $this->belongsToMany(DataTables::class,($list_ajax)->make(true));
            // return $this->belongsToMany(Beers::class,
        }
    }

    public function get_company_document_management() {
        if (!empty($_GET['company_id'])) {
            $company_id = $_GET['company_id'];

            $documentModal = CompanyDocumentManagement::where(['company_id' => $company_id, 'status' => 'Enabled'])->pluck('title', 'id');

            $html = "<option value=''>Select Document</option>";
            foreach ($documentModal as $key => $value) {
                $html .= "<option value=" . $key . ">" . $value . "</option>";
            }

            echo $html;
            die();
        }
    }

    public function add_company_document_request() {

        $this->data['page_title'] = "Add Company Document Request";

        $this->data['Companies'] = Companies::orderBy('company_name')->select('id', 'company_name')->get();

        return view('admin.company_document_request.add_company_document_request', $this->data);
    }

    public function insert_company_document_request(Request $request) {

        $validator_normal = Validator::make($request->all(), [
                    'company_id' => 'required',
                    'document_id' => 'required',
                    'require_date' => 'required',
                    'return_date' => 'required',
                    'work_detail' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.company_document_request')->with('error', 'Please follow validation rules.');
        }


        $documentModal = CompanyDocumentManagement::where(['id' => $request->input('document_id')])->get('custodian_id')->first();

        $documentRequest = new CompanyDocumentRequest();
        $documentRequest->company_id = $request->input('company_id');
        $documentRequest->document_id = $request->input('document_id');
        $documentRequest->require_date = date('Y-m-d', strtotime($request->input('require_date')));
        $documentRequest->return_date = date('Y-m-d', strtotime($request->input('return_date')));
        $documentRequest->work_detail = $request->input('work_detail');
        $documentRequest->request_user_id = \Illuminate\Support\Facades\Auth::user()->id;
        $documentRequest->request_datetime = date('Y-m-d H:i:s');
        $documentRequest->created_at = date('Y-m-d h:i:s');
        $documentRequest->updated_at = date('Y-m-d h:i:s');
        $documentRequest->updated_ip = $request->ip();
        $documentRequest->created_ip = $request->ip();

        if ($documentRequest->save()) {

            $notifyList = User::where('role', config('constants.SuperUser'))->get(['id'])->pluck('id')->toArray();

            $this->notification_task->documentRequestNewNotfy($notifyList, Auth::user()->name, $documentModal->title);

            $adminEmail = user::where('status', 'Enabled')->where('role', config('constants.SuperUser'))->pluck('email')->toArray();

            $data = [
                'document_title' => $documentModal->title,
                'request_by' => Auth::user()->name,
                'email_list' => $adminEmail
            ];
            $this->common_task->newDocumentRequestEmail($data);

            // User Action Log
            $company_name = Companies::whereId($request->get('company_id'))->value('company_name');
            $document_name = CompanyDocumentManagement::orderBy('title')->whereId($request->get('document_id'))->value('title');
            $add_string = "<br> Company Name: ".$company_name."<br> Document Name: ".$document_name."<br> Require Date: ".$request->input('require_date')."<br> Return Date: ".$request->input('return_date');
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => "Add company document request".$add_string,
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            return redirect()->route('admin.company_document_request')->with('success', 'Document Request added successfully.');
        } else {
            return redirect()->route('admin.company_document_request')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function edit_company_document_request($id) {

        $this->data['page_title'] = "Edit Company Document Request";

        $this->data['request_detail'] = CompanyDocumentRequest::where('id', $id)->get()->first();

        $this->data['Companies'] = Companies::select('id', 'company_name')->get();

        if ($this->data['request_detail']->count() == 0) {
            return redirect()->route('admin.company_document_request')->with('error', 'Error Occurred. Try Again!');
        }

        $this->data['request_detail']->require_date = date('d-m-Y', strtotime($this->data['request_detail']->require_date));
        $this->data['request_detail']->return_date = date('d-m-Y', strtotime($this->data['request_detail']->return_date));

        return view('admin.company_document_request.edit_company_document_request', $this->data);
    }

    public function update_company_document_request(Request $request) {

        $documentModal = CompanyDocumentManagement::where(['id' => $request->input('document_id')])->get('custodian_id')->first();

        $request_arr = [
            'company_id' => $request->input('company_id'),
            'document_id' => $request->input('document_id'),
            'require_date' => date('Y-m-d', strtotime($request->input('require_date'))),
            'return_date' => date('Y-m-d', strtotime($request->input('return_date'))),
            'work_detail' => $request->input('work_detail'),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
        ];

        CompanyDocumentRequest::where('id', $request->input('id'))->update($request_arr);

        // User Action Log
        $company_name = Companies::whereId($request->get('company_id'))->value('company_name');
        $document_name = CompanyDocumentManagement::whereId($request->get('document_id'))->value('title');
        $add_string = "<br> Company Name: ".$company_name."<br> Document Name: ".$document_name."<br> Require Date: ".$request->input('require_date')."<br> Return Date: ".$request->input('return_date');
        $action_data = [
            'user_id' => Auth::user()->id,
            'task_body' => "Edit company document request".$add_string,
            'created_ip' => $request->ip(),
        ];
        $this->user_action_logs->action($action_data);

        return redirect()->route('admin.company_document_request')->with('success', 'Document Request details updated successfully.');
    }

    public function approve_company_document_request_by_admin(Request $request) {

        $validator_normal = Validator::make($request->all(), [
                    'approve_id' => 'required'
        ]);


        if ($validator_normal->fails()) {
            return redirect()->route('admin.company_document_request')->with('error', 'Please follow validation rules.');
        }

        $approveId = $request->input('approve_id');
        $returnDate = $request->input('return_date');

        $check_result = Permissions::checkPermission($this->module_id, 4);
        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }

        $documentRequestModel = CompanyDocumentRequest::where('id', $approveId)->get('document_id')->first();

        $documentModal = CompanyDocumentManagement::where(['id' => $documentRequestModel->document_id])->get('custodian_id')->first();

        $approve_arr = [
            'request_status' => 'Approved',
            'superadmin_status' => 'Approved',
            'superadmin_approval_id' => Auth::user()->id,
            'superadmin_approval_datetime' => date('Y-m-d H:i:s'),
            'custodian_id' => $documentModal->custodian_id,
            'status' => 'Approved',
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
        ];

        if(!empty($returnDate)){
            $approve_arr['return_date'] = date('Y-m-d', strtotime($returnDate));
        }

        if (CompanyDocumentRequest::where('id', $approveId)->update($approve_arr)) {

            $documentRequest = CompanyDocumentRequest::select('company_document_request.*', 'users.name', 'users.email')
                            ->join('users', 'users.id', '=', 'company_document_request.request_user_id')
                            ->where('company_document_request.id', $approveId)->get()->first()->toArray();

            $this->notification_task->documentRequestApprovedNotfy(array_merge([$documentRequest['custodian_id']],[$documentRequest['request_user_id']]), 'Admin');

            $documentModal = CompanyDocumentManagement::where(['id' => $documentRequest['document_id']])->get(['title'])->first();

            $custodianEmail = user::where('id', $documentRequest['custodian_id'])->pluck('email')->toArray();

            $data = [
                'document_title' => $documentModal->title,
                'to_user_name' => $documentRequest['name'],
                'email_list' => [$documentRequest['email']],
                'cc_email_list' => $custodianEmail,
                'return_date' => date('d-m-Y', strtotime($documentRequest['return_date'])),
                'approved_by' => Auth::user()->name.", Admin"
            ];
            $this->common_task->approvedDocumentRequestEmailByAdmin($data);

            // User Action Log
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => "Company document request approved by admin <br>Document Name: ".$documentModal->title,
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            return redirect()->route('admin.company_document_request')->with('success', 'Document Request Successfully Approved.');
        }

        return redirect()->route('admin.company_document_request')->with('error', 'Error during operation. Try again!');
    }

    public function approve_company_document_request_by_custodian($id, Request $request) {

        $approve_arr = [
            'request_status' => 'Approved',
            'custodian_approval_status' => 'Approved',
            'custodian_approval_datetime' => date('Y-m-d H:i:s'),
            'status' => 'Approved',
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
        ];

        if (CompanyDocumentRequest::where('id', $id)->update($approve_arr)) {

            $documentRequest = CompanyDocumentRequest::select('company_document_request.*', 'users.name', 'users.email')
                            ->join('users', 'users.id', '=', 'company_document_request.request_user_id')
                            ->where('company_document_request.id', $id)->get()->first()->toArray();

            CompanyDocumentManagement::where(['id' => $documentRequest['document_id']])->update(['status'=>'Submitted']);

            $notifyList = User::where('role', config('constants.SuperUser'))->get(['id'])->pluck('id')->toArray();

            $this->notification_task->documentRequestApprovedNotfy(array_merge($notifyList,[$documentRequest['request_user_id']]), 'Custodian');

            $documentModal = CompanyDocumentManagement::where(['id' => $documentRequest['document_id']])->get(['title'])->first();

            $data = [
                'document_title' => $documentModal->title,
                'to_user_name' => $documentRequest['name'],
                'email_list' => [$documentRequest['email']],
                'approved_by' => Auth::user()->name.", Custodian"
            ];
            $this->common_task->approvedDocumentRequestEmailByCustodian($data);

            // User Action Log
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => "Company document request approved by custodian <br>Document Name: ".$documentModal->title,
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);
            return redirect()->route('admin.company_document_request')->with('success', 'Document Request Successfully Approved.');
        }

        return redirect()->route('admin.company_document_request')->with('error', 'Error during operation. Try again!');
    }

    public function received_company_document_by_requester($id, Request $request) {

        $received_arr = [
            'confirm_submitted_date' => date('Y-m-d H:i:s'),
            'request_status' => 'Submitted',
            'status' => 'Submitted',
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
        ];

        if (CompanyDocumentRequest::where('id', $id)->update($received_arr)) {

            // User Action Log
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => "Company document request received by requester",
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);
            return redirect()->route('admin.company_document_request')->with('success', 'Document Successfully Received.');
        }

        return redirect()->route('admin.company_document_request')->with('error', 'Error during operation. Try again!');
    }

    public function returned_company_document_by_requester($id, Request $request) {

        $received_arr = [
            'request_status' => 'Returned',
            'actual_return_date' => date('Y-m-d H:i:s'),
            'status' => 'Returned',
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
        ];

        if (CompanyDocumentRequest::where('id', $id)->update($received_arr)) {

            $documentRequest = CompanyDocumentRequest::select('company_document_request.*')
                            ->where('company_document_request.id', $id)->get()->first()->toArray();

            $documentModal = CompanyDocumentManagement::where(['id' => $documentRequest['document_id']])->get(['title'])->first();

            $notifyList = User::where('role', config('constants.SuperUser'))->get(['id'])->pluck('id')->toArray();

            $this->notification_task->documentRequestReturnNotfy(array_merge($notifyList,[$documentRequest['custodian_id']]), Auth::user()->name, $documentModal->title);

            $custodianEmail = user::where('id', $documentRequest['custodian_id'])->pluck('email')->toArray();

            $data = [
                'document_title' => $documentModal->title,
                'return_by' => Auth::user()->name,
                'email_list' => $custodianEmail
            ];
            $this->common_task->returnDocumentRequestEmail($data);

            // User Action Log
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => "Company document request returned by requester <br>Document Name: ".$documentModal->title,
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            return redirect()->route('admin.company_document_request')->with('success', 'Document Successfully Returned.');
        }

        return redirect()->route('admin.company_document_request')->with('error', 'Error during operation. Try again!');
    }

    public function received_company_document_by_custodian($id, Request $request) {

        $received_arr = [
            'custodian_approval_status' => 'Received',
            'return_received_datetime' => date('Y-m-d H:i:s'),
            'status' => 'Received',
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
        ];

        if (CompanyDocumentRequest::where('id', $id)->update($received_arr)) {

            $documentRequest = CompanyDocumentRequest::where('id', $id)->get('document_id')->first();

            CompanyDocumentManagement::where(['id' => $documentRequest->document_id])->update(['status'=>'Enabled']);

            // User Action Log
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => "Company document request received by custodian",
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            return redirect()->route('admin.company_document_request')->with('success', 'Document Successfully Received.');
        }

        return redirect()->route('admin.company_document_request')->with('error', 'Error during operation. Try again!');
    }

    public function reject_company_document_request(Request $request) {

        $validator_normal = Validator::make($request->all(), [
                    'reject_id' => 'required',
                    'reject_reason' => 'required',
                    'reject_by' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.company_document_request')->with('error', 'Please follow validation rules.');
        }

        $rejectId = $request->input('reject_id');
        $rejectBy = $request->input('reject_by');

            $reject_arr = [
                'request_status' => 'Rejected',
                'reason' => $request->input('reject_reason'),
                'status' => 'Rejected',
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
            ];

            if($rejectBy == 'admin'){
                $reject_arr['superadmin_status'] = 'Rejected';
                $reject_arr['superadmin_approval_id'] = Auth::user()->id;
                $reject_arr['superadmin_approval_datetime'] = date('Y-m-d H:i:s');

            }else if($rejectBy == 'custodian'){
                $reject_arr['custodian_approval_status'] = 'Rejected';
                $reject_arr['custodian_approval_datetime'] = date('Y-m-d H:i:s');
            }

        if (CompanyDocumentRequest::where('id', $rejectId)->update($reject_arr)) {

            $documentRequest = CompanyDocumentRequest::select('company_document_request.*', 'users.name', 'users.email')
                            ->join('users', 'users.id', '=', 'company_document_request.request_user_id')
                            ->where('company_document_request.id', $rejectId)->get()->first()->toArray();

            if($rejectBy == 'admin'){
                $this->notification_task->documentRequestRejectNotfy([$documentRequest['request_user_id']]);

            }else if($rejectBy == 'custodian'){

                $notifyList = User::where('role', config('constants.SuperUser'))->get(['id'])->pluck('id')->toArray();

                $this->notification_task->documentRequestRejectNotfy(array_merge($notifyList,[$documentRequest['request_user_id']]));
            }

            $documentModal = CompanyDocumentManagement::where(['id' => $documentRequest['document_id']])->get(['title'])->first();

            $data = [
                'document_title' => $documentModal->title,
                'to_user_name' => $documentRequest['name'],
                'email_list' => [$documentRequest['email']],
                'reject_by' => Auth::user()->name,
                'reason' => $request->input('reject_reason'),
                'rejected_user' => $rejectBy,
            ];
            $this->common_task->rejectDocumentRequestEmail($data);

            if ($rejectBy == 'admin') {
                $task_body = "Company document request reject by admin";
            } else if ($rejectBy == 'custodian') {
                $task_body = "Company document request reject by custodian";
            }

            // User Action Log
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => $task_body."<br>Document Name: ".$documentModal->title,
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            return redirect()->route('admin.company_document_request')->with('success', 'Document request successfully rejected.');
        }

        return redirect()->route('admin.company_document_request')->with('error', 'Error during operation. Try again!');
    }

    public function delete_company_document_request($id) {

        if (CompanyDocumentRequest::where('id', $id)->delete()) {
            // User Action Log
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => "Company document delete",
                'created_ip' => request()->ip(),
            ];
            $this->user_action_logs->action($action_data);
            return redirect()->route('admin.company_document_request')->with('success', 'Document Request Successfully Delete.');
        }
        return redirect()->route('admin.company_document_request')->with('error', 'Error during operation. Try again!');
    }

}
