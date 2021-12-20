<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Config;
use App\Common_query;
use App\Role_module;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use App\SoftcopyDocumentCategory;
use App\SoftcopyRequest;
use App\Companies;
use App\User;
use DB;
use App\Lib\Permissions;
use App\Lib\CommonTask;
use App\Lib\NotificationTask;

class SoftcopyRequestController extends Controller {

    public $data;
    public $common_task;
    public $notification_task;

    public function __construct() {
        $this->data['module_title'] = "Softcopy Document Request";
        $this->data['module_link'] = "admin.softcopy_request_sent";
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();
    }

    public function softcopy_request_sent() {

        $this->data['page_title'] = "Softcopy Request Sent";
        $this->data['access_rule'] = '';
        
        $this->data['request_list'] = SoftcopyRequest::select()->get();
        
        return view('admin.softcopy_request.softcopy_request_sent', $this->data);
    }

    public function sent_softcopy_request() {

        $conditions_array = [];

        if(Auth::user()->role != config('constants.SuperUser')){
            $conditions_array = [
                'softcopy_request.request_user_id' => Auth::user()->id
            ];
        }

        $datatable_fields = array('company.company_name','softcopy_document_category.name','users.name','softcopy_request.file_name','softcopy_request.comment','softcopy_request.reason','softcopy_request.request_datetime', 'softcopy_request.status');
        $request = Input::all();

        $getfiled = array('softcopy_request.*', 'company.company_name as company_name','softcopy_document_category.name','users.name as user_name','softcopy_request.file_name','softcopy_request.id', 'softcopy_request.request_user_id','softcopy_request.comment','softcopy_request.reason','softcopy_request.request_datetime', 'softcopy_request.softcopy_document_category_id', 'softcopy_request.request_user_id', 'softcopy_request.company_id', 'softcopy_request.receiver_user_id', 'softcopy_request.status');
        $table = "softcopy_request";

        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'softcopy_document_category';
        $join_str[0]['join_table_id'] = 'softcopy_document_category.id';
        $join_str[0]['from_table_id'] = 'softcopy_request.softcopy_document_category_id';

        $join_str[1]['join_type'] = '';
        $join_str[1]['table'] = 'users';
        $join_str[1]['join_table_id'] = 'users.id';
        $join_str[1]['from_table_id'] = 'softcopy_request.receiver_user_id';

        $join_str[2]['join_type'] = '';
        $join_str[2]['table'] = 'company';
        $join_str[2]['join_table_id'] = 'company.id';
        $join_str[2]['from_table_id'] = 'softcopy_request.company_id';

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str, []);

        die();
    }

    public function softcopy_request_received() {

        $this->data['page_title'] = "Softcopy Request Received";
        $this->data['access_rule'] = '';
        
        $this->data['request_list'] = SoftcopyRequest::select()->get();
        
        return view('admin.softcopy_request.softcopy_request_received', $this->data);
    }

    public function get_softcopy_request() {

        $conditions_array = [];

        if(Auth::user()->role != config('constants.SuperUser')){            
            $conditions_array = [
                'softcopy_request.receiver_user_id' => Auth::user()->id
            ];
        }

        $datatable_fields = array('company.company_name','softcopy_document_category.name','users.name','softcopy_request.file_name','softcopy_request.comment','softcopy_request.reason','softcopy_request.request_datetime', 'softcopy_request.status');
        $request = Input::all();

        $getfiled = array('softcopy_request.*', 'company.company_name as company_name','softcopy_document_category.name','users.name as user_name','softcopy_request.file_name','softcopy_request.id', 'softcopy_request.request_user_id','softcopy_request.comment','softcopy_request.reason','softcopy_request.request_datetime', 'softcopy_request.softcopy_document_category_id', 'softcopy_request.request_user_id', 'softcopy_request.company_id', 'softcopy_request.receiver_user_id', 'softcopy_request.status');
        $table = "softcopy_request";

        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'softcopy_document_category';
        $join_str[0]['join_table_id'] = 'softcopy_document_category.id';
        $join_str[0]['from_table_id'] = 'softcopy_request.softcopy_document_category_id';

        $join_str[1]['join_type'] = '';
        $join_str[1]['table'] = 'users';
        $join_str[1]['join_table_id'] = 'users.id';
        $join_str[1]['from_table_id'] = 'softcopy_request.receiver_user_id';

        $join_str[2]['join_type'] = '';
        $join_str[2]['table'] = 'company';
        $join_str[2]['join_table_id'] = 'company.id';
        $join_str[2]['from_table_id'] = 'softcopy_request.company_id';

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str, []);

        die();
    }
 
    public function add_softcopy_request() {

        $this->data['page_title'] = "Add Softcopy Request";

        $this->data['companies'] = Companies::orderBy('company_name')->where('status', 'Enabled')->pluck('company_name', 'id');

        $this->data['users'] = User::getUser();

        $this->data['documents'] = SoftcopyDocumentCategory::orderBy('name')->where('status', 'Enabled')->pluck('name', 'id');

        return view('admin.softcopy_request.add_softcopy_request', $this->data);
    }

    public function insert_softcopy_request(Request $request) {
        
        $validator_normal = Validator::make($request->all(), [
                    'company_id' => 'required',
                    'softcopy_document_category_id' => 'required',
                    'receiver_user_id' => 'required',
                    'comment' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.softcopy_request_sent')->with('error', 'Please follow validation rules.');
        }

        $documentRequest = new SoftcopyRequest();
        $documentRequest->company_id = $request->input('company_id');
        $documentRequest->softcopy_document_category_id = $request->input('softcopy_document_category_id');        
        $documentRequest->comment = $request->input('comment');
        $documentRequest->receiver_user_id = $request->input('receiver_user_id');
        $documentRequest->request_user_id = \Illuminate\Support\Facades\Auth::user()->id;        
        $documentRequest->request_datetime = date('Y-m-d H:i:s');
        $documentRequest->created_at = date('Y-m-d h:i:s');
        $documentRequest->updated_at = date('Y-m-d h:i:s');
        $documentRequest->updated_ip = $request->ip();
        $documentRequest->created_ip = $request->ip();
        
        if ($documentRequest->save()) {

            $documentName = SoftcopyDocumentCategory::where('id', $documentRequest->softcopy_document_category_id)->first()->name;

            $notifyList = [$documentRequest->receiver_user_id];
            $this->notification_task->softcopyRequestNotfy($notifyList, Auth::user()->name, $documentName, 'New added');

            $receiverEmail = user::where('id', $documentRequest->receiver_user_id)->pluck('email')->toArray();            
            $data = [
                'document_title' => $documentName,
                'request_by' => Auth::user()->name,
                'email_list' => $receiverEmail,
                'request_type' => 'New added'
            ];
            $this->common_task->softcopyRequestEmail($data);

            return redirect()->route('admin.softcopy_request_sent')->with('success', 'Softcopy Request added successfully.');
        } else {
            return redirect()->route('admin.softcopy_request_sent')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function edit_softcopy_request($id) {

        $this->data['page_title'] = "Edit Softcopy Request";

        $this->data['request_detail'] = SoftcopyRequest::where('id', $id)->get()->first();
        
        $this->data['companies'] = Companies::where('status', 'Enabled')->pluck('company_name', 'id');

        $this->data['users'] = User::getUser();

        $this->data['documents'] = SoftcopyDocumentCategory::where('status', 'Enabled')->pluck('name', 'id');

        if ($this->data['request_detail']->count() == 0) {
            return redirect()->route('admin.softcopy_request_sent')->with('error', 'Error Occurred. Try Again!');
        }
                
        return view('admin.softcopy_request.edit_softcopy_request', $this->data);
    }

    public function update_softcopy_request(Request $request) {

        $request_arr = [
            'company_id' => $request->input('company_id'),
            'softcopy_document_category_id' => $request->input('softcopy_document_category_id'),
            'receiver_user_id' => $request->input('receiver_user_id'),
            'comment' => $request->input('comment'),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
        ];       

        if (SoftcopyRequest::where('id', $request->input('id'))->update($request_arr)) {

            $documentName = SoftcopyDocumentCategory::where('id', $request->input('softcopy_document_category_id'))->first()->name;

            $notifyList = [$request->input('receiver_user_id')];
            $this->notification_task->softcopyRequestNotfy($notifyList, Auth::user()->name, $documentName, 'Updated');

            $receiverEmail = user::where('id', $request->input('receiver_user_id'))->pluck('email')->toArray();
            $data = [
                'document_title' => $documentName,
                'request_by' => Auth::user()->name,
                'email_list' => $receiverEmail,
                'request_type' => 'Updated'
            ];
            $this->common_task->softcopyRequestEmail($data);

            return redirect()->route('admin.softcopy_request_sent')->with('success', 'Softcopy Request updated successfully.');
        } else {
            return redirect()->route('admin.softcopy_request_sent')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function delete_softcopy_request($id) {

        $softcopyRequest = SoftcopyRequest::findOrFail($id);

        if (SoftcopyRequest::where('id', $id)->delete()) {

            $documentName = SoftcopyDocumentCategory::where('id', $softcopyRequest->softcopy_document_category_id)->first()->name;

            $notifyList = [$softcopyRequest->receiver_user_id];
            $this->notification_task->softcopyRequestNotfy($notifyList, Auth::user()->name, $documentName, 'Deleted');

            $receiverEmail = user::where('id', $softcopyRequest->receiver_user_id)->pluck('email')->toArray();
            $data = [
                'document_title' => $documentName,
                'request_by' => Auth::user()->name,
                'email_list' => $receiverEmail,
                'request_type' => 'Deleted'
            ];
            $this->common_task->softcopyRequestEmail($data);

            return redirect()->route('admin.softcopy_request_sent')->with('success', 'Softcopy Request Successfully Deleted.');
        } else {
            return redirect()->route('admin.softcopy_request_sent')->with('error', 'Error occurre in insert. Try Again!');
        }        
    }

    public function send_softcopy(Request $request){
        $validator_normal = Validator::make($request->all(), [
            'id' => 'required',
            'file' => 'required',
        ]);

        if ($validator_normal->fails()) {
            return redirect()->route('admin.softcopy_request_received')->with('error', 'Please follow validation rules.');
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

        $softcopyRequest = SoftcopyRequest::findOrFail($request->input('id'));
            
        if (SoftcopyRequest::where('id', $request->input('id'))->update($send_arr)) {

            $documentName = SoftcopyDocumentCategory::where('id', $softcopyRequest->softcopy_document_category_id)->first()->name;

            $notifyList = [$softcopyRequest->request_user_id];
            $this->notification_task->softcopyRequestNotfy($notifyList, Auth::user()->name, $documentName, 'Sent');

            $receiverEmail = user::where('id', $softcopyRequest->request_user_id)->pluck('email')->toArray();
            $data = [
                'document_title' => $documentName,
                'request_by' => Auth::user()->name,
                'email_list' => $receiverEmail,
                'request_type' => 'Sent'
            ];
            $this->common_task->softcopyRequestEmail($data);

            return redirect()->route('admin.softcopy_request_received')->with('success', 'Softcopy sent successfully.');
        } else {
            return redirect()->route('admin.softcopy_request_received')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function reject_softcopy_request(Request $request) {

        $validator_normal = Validator::make($request->all(), [
                    'reject_id' => 'required',
                    'reason' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.softcopy_request_received')->with('error', 'Please follow validation rules.');
        }

        $rejectId = $request->input('reject_id');

        $reject_arr = [
            'reason' => $request->input('reason'),
            'status' => 'Rejected',
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),                
        ];

        $softcopyRequest = SoftcopyRequest::findOrFail($rejectId);
            
        if (SoftcopyRequest::where('id', $rejectId)->update($reject_arr)) {

            $documentName = SoftcopyDocumentCategory::where('id', $softcopyRequest->softcopy_document_category_id)->first()->name;

            $notifyList = [$softcopyRequest->request_user_id];
            $this->notification_task->softcopyRequestNotfy($notifyList, Auth::user()->name, $documentName, 'Rejected');

            $receiverEmail = user::where('id', $softcopyRequest->request_user_id)->pluck('email')->toArray();
            $data = [
                'document_title' => $documentName,
                'request_by' => Auth::user()->name,
                'email_list' => $receiverEmail,
                'request_type' => 'Rejected'
            ];
            $this->common_task->softcopyRequestEmail($data);

            return redirect()->route('admin.softcopy_request_received')->with('success', 'Softcopy Request rejected successfully.');
        } else {
            return redirect()->route('admin.softcopy_request_received')->with('error', 'Error occurre in insert. Try Again!');
        }
    }      

}
