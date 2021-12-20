<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Email_format;
use App\Mail\Mails;
use App\User;
use App\CompanyDocumentManagement;
use App\CompanyDocumentRequest;
use App\Lib\CommonTask;
use DateTime;
use App\Lib\NotificationTask;
use App\Lib\UserActionLogs;

class CompanyDocumentRequestController extends Controller {

    private $page_limit = 20;
    public $common_task;
    public $notification_task;
    public $user_action_logs;

    public function __construct() {
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();
        $this->user_action_logs = new UserActionLogs();
    }

    public function get_admin_pending_request(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $userId = $request->get('user_id');
        $response_data = [];

        $userRole = user::where('id', $userId)->get('role')->first();


            $documentRequest = CompanyDocumentRequest::select('company_document_request.*', 'users.name as requester_name','company_document_management.title as document_title','company_document_management.custodian_id as main_custodian_id')
                            ->join('users', 'users.id', '=', 'company_document_request.request_user_id')
                            ->join('company_document_management', 'company_document_management.id', '=', 'company_document_request.document_id')
                            ->where('company_document_request.superadmin_status', 'Pending')->with(['get_company_detail','get_custodian_detail'])->get()->toArray();

        if (empty($documentRequest)) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        $response_data['pending_request_list'] = $documentRequest;
        return response()->json(['status' => true, 'msg' => 'Record found.', 'data' => $response_data]);
    }

    public function get_custodian_pending_request(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $userId = $request->get('user_id');
        $response_data = [];

        $userRole = user::where('id', $userId)->get('role')->first();

        $documentRequest = CompanyDocumentRequest::select('company_document_request.*', 'users.name as requester_name','company_document_management.title as document_title','company_document_management.custodian_id as main_custodian_id')
                        ->join('users', 'users.id', '=', 'company_document_request.request_user_id')
                        ->join('company_document_management', 'company_document_management.id', '=', 'company_document_request.document_id')
                        ->where('company_document_request.custodian_id', $userId)
                        ->where('company_document_request.custodian_approval_status', 'Pending')
                        ->with(['get_company_detail','get_custodian_detail'])->orderBy('name')
                        ->get()->toArray();
                        // ->orWhere(function($q) use ($userId) {
                        //     $q->where('company_document_request.request_user_id', $userId)->where('company_document_request.request_status', 'Approved');
                        // })
                        // ->orWhere(function($q) use ($userId) {
                        //     $q->where('company_document_request.request_user_id', $userId)->where('company_document_request.request_status', 'Submitted');
                        // })
                        // ->orWhere(function($q) use ($userId) {
                        //     $q->where('company_document_request.custodian_id', $userId)->where('company_document_request.status', 'Returned');
                        // })


        if (empty($documentRequest)) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        $response_data['pending_request_list'] = $documentRequest;
        return response()->json(['status' => true, 'msg' => 'Record found.', 'data' => $response_data]);
    }

    public function get_requester_pending_received(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $userId = $request->get('user_id');
        $response_data = [];

        $userRole = user::where('id', $userId)->get('role')->first();

        $documentRequest = CompanyDocumentRequest::select('company_document_request.*', 'users.name as requester_name','company_document_management.title as document_title','company_document_management.custodian_id as main_custodian_id')
                        ->join('users', 'users.id', '=', 'company_document_request.request_user_id')
                        ->join('company_document_management', 'company_document_management.id', '=', 'company_document_request.document_id')
                        ->where('company_document_request.request_user_id', $userId)
                        ->where('company_document_request.request_status', 'Approved')
                        ->with(['get_company_detail','get_custodian_detail'])
                        ->get()->toArray();

        if (empty($documentRequest)) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        $response_data['pending_request_list'] = $documentRequest;
        return response()->json(['status' => true, 'msg' => 'Record found.', 'data' => $response_data]);
    }

    public function get_requester_pending_returned(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $userId = $request->get('user_id');
        $response_data = [];

        $userRole = user::where('id', $userId)->get('role')->first();

        $documentRequest = CompanyDocumentRequest::select('company_document_request.*', 'users.name as requester_name','company_document_management.title as document_title','company_document_management.custodian_id as main_custodian_id')
                        ->join('users', 'users.id', '=', 'company_document_request.request_user_id')
                        ->join('company_document_management', 'company_document_management.id', '=', 'company_document_request.document_id')
                        ->where('company_document_request.request_user_id', $userId)
                        ->where('company_document_request.request_status', 'Submitted')
                        ->with(['get_company_detail','get_custodian_detail'])
                        ->get()->toArray();

        if (empty($documentRequest)) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        $response_data['pending_request_list'] = $documentRequest;
        return response()->json(['status' => true, 'msg' => 'Record found.', 'data' => $response_data]);
    }

    public function get_custodian_pending_received(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $userId = $request->get('user_id');
        $response_data = [];

        $userRole = user::where('id', $userId)->get('role')->first();

        $documentRequest = CompanyDocumentRequest::select('company_document_request.*', 'users.name as requester_name','company_document_management.title as document_title','company_document_management.custodian_id as main_custodian_id')
                        ->join('users', 'users.id', '=', 'company_document_request.request_user_id')
                        ->join('company_document_management', 'company_document_management.id', '=', 'company_document_request.document_id')
                        ->where('company_document_request.custodian_id', $userId)
                        ->where('company_document_request.request_status', 'Returned')
                        ->where('company_document_request.custodian_approval_status', 'Approved')
                        ->with(['get_company_detail','get_custodian_detail'])
                        ->get()->toArray();

        if (empty($documentRequest)) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        $response_data['pending_request_list'] = $documentRequest;
        return response()->json(['status' => true, 'msg' => 'Record found.', 'data' => $response_data]);
    }

    public function approve_company_document_request_by_admin(Request $request) {
        $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'request_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $userId = $request->get('user_id');
        $requestId = $request->get('request_id');
        $returnDate = $request->get('return_date');
        $response_data = [];

        $documentRequestModel = CompanyDocumentRequest::where('id', $requestId)->get('document_id')->first();

        $documentModal = CompanyDocumentManagement::where(['id' => $documentRequestModel->document_id])->get('custodian_id')->first();

        $approve_arr = [
            'request_status' => 'Approved',
            'superadmin_status' => 'Approved',
            'superadmin_approval_id' => $userId,
            'superadmin_approval_datetime' => date('Y-m-d H:i:s'),
            'custodian_id' => $documentModal->custodian_id,
            'status' => 'Approved',
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
        ];

        if(!empty($returnDate)){
            $approve_arr['return_date'] = date('Y-m-d', strtotime($returnDate));
        }

        if (CompanyDocumentRequest::where('id', $requestId)->update($approve_arr)) {

            $documentRequest = CompanyDocumentRequest::select('company_document_request.*', 'users.name', 'users.email')
                            ->join('users', 'users.id', '=', 'company_document_request.request_user_id')
                            ->where('company_document_request.id', $requestId)->get()->first()->toArray();

            $this->notification_task->documentRequestApprovedNotfy(array_merge([$documentRequest['custodian_id']],[$documentRequest['request_user_id']]), 'Admin');

            $documentModal = CompanyDocumentManagement::where(['id' => $documentRequest['document_id']])->get(['title'])->first();

            $custodianEmail = user::where('id', $documentRequest['custodian_id'])->pluck('email')->toArray();

            $userName = user::where('id', $userId)->get('name')->first()->toArray();

            $data = [
                'document_title' => $documentModal->title,
                'to_user_name' => $documentRequest['name'],
                'email_list' => [$documentRequest['email']],
                'cc_email_list' => $custodianEmail,
                'return_date' => date('d-m-Y', strtotime($documentRequest['return_date'])),
                'approved_by' => $userName['name'].", Admin"
            ];
            $this->common_task->approvedDocumentRequestEmailByAdmin($data);

            // User Action Log
            $action_data = [
                'user_id' => $userId,
                'task_body' => "Company document request approved by admin <br>Document Name: ".$documentModal->title,
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            return response()->json(['status' => true, 'msg' => "Company document request successfully approved.", 'data' => []]);

        } else {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }
    }

    public function approve_company_document_request_by_custodian(Request $request) {
        $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'request_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $userId = $request->get('user_id');
        $requestId = $request->get('request_id');
        $response_data = [];

        $approve_arr = [
            'request_status' => 'Approved',
            'custodian_approval_status' => 'Approved',
            'custodian_approval_datetime' => date('Y-m-d H:i:s'),
            'status' => 'Approved',
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
        ];

        if (CompanyDocumentRequest::where('id', $requestId)->update($approve_arr)) {

            $documentRequest = CompanyDocumentRequest::select('company_document_request.*', 'users.name', 'users.email')
                            ->join('users', 'users.id', '=', 'company_document_request.request_user_id')
                            ->where('company_document_request.id', $requestId)->get()->first()->toArray();

            CompanyDocumentManagement::where(['id' => $documentRequest['document_id']])->update(['status'=>'Submitted']);

            $notifyList = User::where('role', config('constants.SuperUser'))->get(['id'])->pluck('id')->toArray();

            $this->notification_task->documentRequestApprovedNotfy(array_merge($notifyList,[$documentRequest['request_user_id']]), 'Custodian');

            $documentModal = CompanyDocumentManagement::where(['id' => $documentRequest['document_id']])->get(['title'])->first();

            $userName = user::where('id', $userId)->get('name')->first();

            $data = [
                'document_title' => $documentModal->title,
                'to_user_name' => $documentRequest['name'],
                'email_list' => [$documentRequest['email']],
                'approved_by' => $userName->name.", Custodian"
            ];
            $this->common_task->approvedDocumentRequestEmailByCustodian($data);

            // User Action Log
            $action_data = [
                'user_id' => $userId,
                'task_body' => "Company document request approved by custodian <br>Document Name: ".$documentModal->title,
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            return response()->json(['status' => true, 'msg' => "Company document request successfully approved.", 'data' => []]);

        } else {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }
    }

    public function received_company_document_by_requester(Request $request) {
        $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'request_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $userId = $request->get('user_id');
        $requestId = $request->get('request_id');
        $response_data = [];

        $received_arr = [
            'confirm_submitted_date' => date('Y-m-d H:i:s'),
            'request_status' => 'Submitted',
            'status' => 'Submitted',
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
        ];

        if (CompanyDocumentRequest::where('id', $requestId)->update($received_arr)) {

            // User Action Log
            $action_data = [
                'user_id' => $userId,
                'task_body' => "Company document request received by requester",
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);
            return response()->json(['status' => true, 'msg' => "Company document successfully received.", 'data' => []]);

        } else {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }
    }

    public function returned_company_document_by_requester(Request $request) {
        $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'request_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $userId = $request->get('user_id');
        $requestId = $request->get('request_id');
        $response_data = [];

         $received_arr = [
            'request_status' => 'Returned',
            'actual_return_date' => date('Y-m-d H:i:s'),
            'status' => 'Returned',
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
        ];

        if (CompanyDocumentRequest::where('id', $requestId)->update($received_arr)) {

            $documentRequest = CompanyDocumentRequest::select('company_document_request.*')
                            ->where('company_document_request.id', $requestId)->get()->first()->toArray();

            $documentModal = CompanyDocumentManagement::where(['id' => $documentRequest['document_id']])->get(['title'])->first();

            $notifyList = User::where('role', config('constants.SuperUser'))->get(['id'])->pluck('id')->toArray();

            $userName = user::where('id', $userId)->get('name')->first();

            $this->notification_task->documentRequestReturnNotfy(array_merge($notifyList,[$documentRequest['custodian_id']]), $userName->name, $documentModal->title);

            $custodianEmail = user::where('id', $documentRequest['custodian_id'])->pluck('email')->toArray();

            $data = [
                'document_title' => $documentModal->title,
                'return_by' => $userName->name,
                'email_list' => $custodianEmail
            ];
            $this->common_task->returnDocumentRequestEmail($data);

            // User Action Log
            $action_data = [
                'user_id' => $userId,
                'task_body' => "Company document request returned by requester <br>Document Name: ".$documentModal->title,
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            return response()->json(['status' => true, 'msg' => "Company document successfully returned.", 'data' => []]);

        } else {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }
    }

    public function received_company_document_by_custodian(Request $request) {
        $validator = Validator::make($request->all(), [
                'user_id' => 'required',
                'request_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $userId = $request->get('user_id');
        $requestId = $request->get('request_id');
        $response_data = [];

        $received_arr = [
            'custodian_approval_status' => 'Received',
            'return_received_datetime' => date('Y-m-d H:i:s'),
            'status' => 'Received',
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
        ];

        if (CompanyDocumentRequest::where('id', $requestId)->update($received_arr)) {

            $documentRequest = CompanyDocumentRequest::where('id', $requestId)->get('document_id')->first();

            CompanyDocumentManagement::where(['id' => $documentRequest->document_id])->update(['status'=>'Enabled']);

            // User Action Log
            $action_data = [
                'user_id' => $userId,
                'task_body' => "Company document request received by custodian",
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            return response()->json(['status' => true, 'msg' => "Company document successfully received.", 'data' => []]);

        } else {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }
    }

    public function reject_company_document_request(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'request_id' => 'required',
                    'reject_reason' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $userId = $request->get('user_id');
        $requestId = $request->get('request_id');
        $response_data = [];

        $reject_arr = [
            'request_status' => 'Rejected',
            'reason' => $request->input('reject_reason'),
            'status' => 'Rejected',
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
        ];

        $userRole = user::where('id', $userId)->get('role')->first();

        if($userRole->role == config('constants.SuperUser')){
            $reject_arr['superadmin_status'] = 'Rejected';
            $reject_arr['superadmin_approval_id'] = $userId;
            $reject_arr['superadmin_approval_datetime'] = date('Y-m-d H:i:s');
            $rejectBy = "admin";
        }else{
            $reject_arr['custodian_approval_status'] = 'Rejected';
            $reject_arr['custodian_approval_datetime'] = date('Y-m-d H:i:s');
            $rejectBy = "custodian";
        }

        if (CompanyDocumentRequest::where('id', $requestId)->update($reject_arr)) {

            $documentRequest = CompanyDocumentRequest::select('company_document_request.*', 'users.name', 'users.email')
                            ->join('users', 'users.id', '=', 'company_document_request.request_user_id')
                            ->where('company_document_request.id', $requestId)->get()->first()->toArray();

            if($userRole->role == config('constants.SuperUser')){
                $this->notification_task->documentRequestRejectNotfy([$documentRequest['request_user_id']]);
            } else {
                $notifyList = User::where('role', config('constants.SuperUser'))->get(['id'])->pluck('id')->toArray();
                $this->notification_task->documentRequestRejectNotfy(array_merge($notifyList,[$documentRequest['request_user_id']]));
            }

            $documentModal = CompanyDocumentManagement::where(['id' => $documentRequest['document_id']])->get(['title'])->first();

            $userName = user::where('id', $userId)->get('name')->first();

            $data = [
                'document_title' => $documentModal->title,
                'to_user_name' => $documentRequest['name'],
                'email_list' => [$documentRequest['email']],
                'reject_by' => $userName->name,
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
                'user_id' => $userId,
                'task_body' => $task_body."<br>Document Name: ".$documentModal->title,
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            return response()->json(['status' => true, 'msg' => "Company document request application rejected.", 'data' => []]);

        } else {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }
    }

}
