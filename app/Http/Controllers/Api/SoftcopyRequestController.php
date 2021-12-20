<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use DateTime;
use Illuminate\Support\Facades\Mail;
use App\Email_format;
use App\Mail\Mails;
use App\Lib\CommonTask;
use App\Lib\NotificationTask;
use App\SoftcopyDocumentCategory;
use App\SoftcopyRequest;
use App\Companies;
use App\User;

class SoftcopyRequestController extends Controller {

    private $page_limit = 20;
    public $common_task;    
    public $notification_task;

    public function __construct() {
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();
    }

    public function get_softcopy_request_sent(Request $request) {

        $validator = Validator::make($request->all(), [
                    'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_detail = SoftcopyRequest::where(['request_user_id' => $request->input('user_id')])
                        ->with(['category' => function($query) {
                            $query->select('id', 'name');
                        }])
                        ->with(['company' => function($query) {
                            $query->select('id', 'company_name');
                        }])
                        ->with(['requester' => function($query) {
                            $query->select('id', 'name');
                        }])
                        ->with(['receiver'=>function($q){
                            $q->select('id','name');
                        }])
                        ->get(['id as request_id', 'company_id', 'softcopy_document_category_id', 'receiver_user_id', 'request_user_id', 'request_datetime', 'file_name', 'comment', 'reason', 'status'])->toArray();

            $request_detail = array_map(function ($request) {
                $request['file_name'] = asset('storage/' . str_replace('public/', '', $request['file_name']));
                return $request;
            }, $request_detail);

        return response()->json(['status' => true, 'msg' => 'Softcopy Request Details', 'data' => $request_detail]);
    }

    public function get_softcopy_request_received(Request $request) {

        $validator = Validator::make($request->all(), [
                    'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_detail = SoftcopyRequest::where(['receiver_user_id' => $request->input('user_id')])
                        ->with(['category' => function($query) {
                            $query->select('id', 'name');
                        }])
                        ->with(['company' => function($query) {
                            $query->select('id', 'company_name');
                        }])
                        ->with(['receiver' => function($query) {
                            $query->select('id', 'name');
                        }])
                        ->with(['requester' => function($query) {
                            $query->select('id', 'name');
                        }])
                        ->get(['id as request_id', 'company_id', 'softcopy_document_category_id', 'receiver_user_id', 'request_user_id', 'request_datetime', 'file_name', 'comment', 'reason', 'status'])->toArray();

            $request_detail = array_map(function ($request) {
                $request['file_name'] = asset('storage/' . str_replace('public/', '', $request['file_name']));
                return $request;
            }, $request_detail);

        return response()->json(['status' => true, 'msg' => 'Softcopy Request Details', 'data' => $request_detail]);
    }

    public function get_softcopy_category(Request $request) {

        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $category = SoftcopyDocumentCategory::where('status', 'Enabled')->get();
        
        if (empty($category)) {
            return response()->json(['status' => false, 'data' => [], 'msg' => config('errors.no_record.msg'), 'error' => config('errors.no_record.code')]);
        }
        return response()->json(['status' => true, 'msg' => 'Softcopy Category Details', 'data' => $category]);
    }

    public function add_softcopy_request(Request $request) {

        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'company_id' => 'required',
                    'softcopy_document_category_id' => 'required',
                    'receiver_user_id' => 'required',
                    'comment' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $documentRequest = new SoftcopyRequest();
        $documentRequest->company_id = $request->input('company_id');
        $documentRequest->softcopy_document_category_id = $request->input('softcopy_document_category_id');        
        $documentRequest->comment = $request->input('comment');
        $documentRequest->receiver_user_id = $request->input('receiver_user_id');
        $documentRequest->request_user_id = $request->input('user_id');
        $documentRequest->request_datetime = date('Y-m-d H:i:s');
        $documentRequest->created_at = date('Y-m-d h:i:s');
        $documentRequest->updated_at = date('Y-m-d h:i:s');
        $documentRequest->updated_ip = $request->ip();
        $documentRequest->created_ip = $request->ip();

        if ($documentRequest->save()) {

            $documentName = SoftcopyDocumentCategory::where('id', $documentRequest->softcopy_document_category_id)->first()->name;
            $userName = User::where('id', $request->input('user_id'))->first()->name;
            
            $notifyList = [$documentRequest->receiver_user_id];
            $this->notification_task->softcopyRequestNotfy($notifyList, $userName, $documentName, 'New added');

            $receiverEmail = user::where('id', $documentRequest->receiver_user_id)->pluck('email')->toArray();            
            $data = [
                'document_title' => $documentName,
                'request_by' => $userName,
                'email_list' => $receiverEmail,
                'request_type' => 'New added'
            ];
            $this->common_task->softcopyRequestEmail($data);
            
            return response()->json(['status' => true, 'msg' => 'Softcopy Request added successfully', 'data' => []]);
        }

        return response()->json(['status' => false, 'msg' => config('errors.sql_operation.msg'), 'data' => [], 'error' => config('errors.sql_operation.code')]);
    }

    public function edit_softcopy_request(Request $request) {
        $validator = Validator::make($request->all(), [
            'request_id' => 'required',
            'user_id' => 'required',
            'company_id' => 'required',
            'softcopy_document_category_id' => 'required',
            'receiver_user_id' => 'required',
            'comment' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $update_arr = [
            'company_id' => $request->input('company_id'),
            'softcopy_document_category_id' => $request->input('softcopy_document_category_id'),
            'receiver_user_id' => $request->input('receiver_user_id'),
            'comment' => $request->input('comment'),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
        ];

        if (SoftcopyRequest::where('id', $request->input('request_id'))->update($update_arr)) {

            $documentName = SoftcopyDocumentCategory::where('id', $request->input('softcopy_document_category_id'))->first()->name;
            $userName = User::where('id', $request->input('user_id'))->first()->name;

            $notifyList = [$request->input('receiver_user_id')];
            $this->notification_task->softcopyRequestNotfy($notifyList, $userName, $documentName, 'Updated');

            $receiverEmail = user::where('id', $request->input('receiver_user_id'))->pluck('email')->toArray();
            $data = [
                'document_title' => $documentName,
                'request_by' => $userName,
                'email_list' => $receiverEmail,
                'request_type' => 'Updated'
            ];
            $this->common_task->softcopyRequestEmail($data);
            return response()->json(['status' => true, 'msg' => 'Softcopy Request updated successfully', 'data' => []]);
        }

        return response()->json(['status' => false, 'msg' => config('errors.sql_operation.msg'), 'data' => [], 'error' => config('errors.sql_operation.code')]);
        
    }

    public function delete_softcopy_request(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'request_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $softcopyRequest = SoftcopyRequest::findOrFail($request->input('request_id'));

        if (!empty($softcopyRequest) && SoftcopyRequest::where('id',$request->input('request_id'))->delete()) {

            $documentName = SoftcopyDocumentCategory::where('id', $softcopyRequest->softcopy_document_category_id)->first()->name;
            $userName = User::where('id', $request->input('user_id'))->first()->name;

            $notifyList = [$softcopyRequest->receiver_user_id];
            $this->notification_task->softcopyRequestNotfy($notifyList, $userName, $documentName, 'Deleted');

            $receiverEmail = user::where('id', $softcopyRequest->receiver_user_id)->pluck('email')->toArray();
            $data = [
                'document_title' => $documentName,
                'request_by' => $userName,
                'email_list' => $receiverEmail,
                'request_type' => 'Deleted'
            ];
            $this->common_task->softcopyRequestEmail($data);
            
            return response()->json(['status' => true, 'msg' => "Softcopy Request deleted successfully.", 'data' => []]);
            
        } else {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }
    }

    public function send_softcopy_request(Request $request) {
        $validator = Validator::make($request->all(), [
            'request_id' => 'required',
            'user_id' => 'required',
            'file' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $send_arr = [
            'status' => 'Completed',
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
        ];

        //upload user softcopy image
        $softcopy_image_file = '';
        if ($request->hasFile('file')) {
            $softcopy_image = $request->file('file');
            $file_path = $softcopy_image->store('public/softcopy_request');
            if ($file_path) {
                $softcopy_image_file = $file_path;
            }
        }

        if (!empty($softcopy_image_file)) {
            $send_arr['file_name'] = $softcopy_image_file;
        }

        $softcopyRequest = SoftcopyRequest::findOrFail($request->input('request_id'));

        if (SoftcopyRequest::where('id', $request->input('request_id'))->update($send_arr)) {

            $documentName = SoftcopyDocumentCategory::where('id', $softcopyRequest->softcopy_document_category_id)->first()->name;
            $userName = User::where('id', $request->input('user_id'))->first()->name;

            $notifyList = [$softcopyRequest->request_user_id];
            $this->notification_task->softcopyRequestNotfy($notifyList, $userName, $documentName, 'Sent');

            $receiverEmail = user::where('id', $softcopyRequest->request_user_id)->pluck('email')->toArray();
            $data = [
                'document_title' => $documentName,
                'request_by' => $userName,
                'email_list' => $receiverEmail,
                'request_type' => 'Sent'
            ];
            $this->common_task->softcopyRequestEmail($data);
            return response()->json(['status' => true, 'msg' => 'Softcopy sent successfully', 'data' => []]);
        }

        return response()->json(['status' => false, 'msg' => config('errors.sql_operation.msg'), 'data' => [], 'error' => config('errors.sql_operation.code')]);
        
    }

    public function reject_softcopy_request(Request $request) {
        
        $validator_normal = Validator::make($request->all(), [            
            'user_id' => 'required',
            'request_id' => 'required',
            'reason' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.softcopy_request_received')->with('error', 'Please follow validation rules.');
        }

        $requestId = $request->input('request_id');

        $reject_arr = [
            'reason' => $request->input('reason'),
            'status' => 'Rejected',
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),                
        ];

        $softcopyRequest = SoftcopyRequest::findOrFail($requestId);
            
        if (SoftcopyRequest::where('id', $requestId)->update($reject_arr)) {

            $documentName = SoftcopyDocumentCategory::where('id', $softcopyRequest->softcopy_document_category_id)->first()->name;
            $userName = User::where('id', $request->input('user_id'))->first()->name;

            $notifyList = [$softcopyRequest->request_user_id];
            $this->notification_task->softcopyRequestNotfy($notifyList, $userName, $documentName, 'Rejected');

            $receiverEmail = user::where('id', $softcopyRequest->request_user_id)->pluck('email')->toArray();
            $data = [
                'document_title' => $documentName,
                'request_by' => $userName,
                'email_list' => $receiverEmail,
                'request_type' => 'Rejected'
            ];
            $this->common_task->softcopyRequestEmail($data);

            return response()->json(['status' => true, 'msg' => 'Softcopy Request rejected successfully', 'data' => []]);
        } else {
            return redirect()->route('admin.softcopy_request_received')->with('error', 'Error occurre in insert. Try Again!');
        }
        
    }

}
