<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Lib\NotificationTask;
use App\Stationery_items;
use App\Stationery_items_access;
use Illuminate\Support\Facades\Validator;

class StationeryController extends Controller
{
    public function __construct() {
       
        $this->notification_task = new NotificationTask();
        $this->super_admin = \App\User::where('role', config('constants.SuperUser'))->first();
    }

    public function api_stationery_items_access_requestes(Request $request)
    {
         $validator = Validator::make($request->all(), [
                    'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $get_fields = ['stationery_items_access.id',
            'stationery_items_access.stationery_items_id',
            'stationery_items.item_name',
            'stationery_items.item_image',
            'B.name as requested_by_user',
            'stationery_items_access.request_user_status',
            'stationery_items_access.request_note',
            'stationery_items_access.first_approval_status',
            'stationery_items_access.second_approval_status',
            'stationery_items_access.first_approval_id',
            'stationery_items_access.second_approval_id',
            'stationery_items_access.request_user_id',
        'stationery_items_access.created_at'];

        $stationery_requests = Stationery_items_access::join('stationery_items','stationery_items.id','=','stationery_items_access.stationery_items_id')
                ->join('users','users.id','=','stationery_items_access.first_approval_id')
                ->join('users AS C','C.id','=','stationery_items_access.second_approval_id')
                ->join('users As B','B.id','=','stationery_items_access.request_user_id')
                ->where('stationery_items_access.custodian_user_id',$request_data['user_id'])
                ->orWhere(function ($query) use ($request_data) {
                        $query->Where('stationery_items_access.first_approval_id', $request_data['user_id'] )
                            ->Where('stationery_items_access.first_approval_status','Pending');
                    })->orWhere(function ($query) use ($request_data)  {
                        $query->Where('stationery_items_access.second_approval_id',$request_data['user_id'] )
                            ->Where('stationery_items_access.first_approval_status','Approved')
                            ->Where('stationery_items_access.second_approval_status','Pending')
                            ->orWhere('stationery_items_access.request_user_status','Returned');
                    })
                ->orWhere('stationery_items_access.request_user_id',$request_data['user_id'])
                ->get($get_fields);


        if ($stationery_requests->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        } else {
            foreach ($stationery_requests as $key => $value) {
                if (!empty($value->item_image))
                    $stationery_requests[$key]->item_image = asset('storage/' . str_replace('public/', '', $value->item_image));
            }
        }

        return response()->json(['status' => true, 'msg' => 'Get records!', 'data' => $stationery_requests]);
    }

    public function api_stationery_items_accept_request(Request $request) {   //done
        
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'stationery_access_id' => 'required',
                    'user_type' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $id = $request_data['stationery_access_id'];
        $status = $request_data['user_type'];
        
        //NotificationTask
        $items_id = Stationery_items_access::where('id', $id)->get(['stationery_items_id','second_approval_id','request_user_id']);
        $itemName = Stationery_items::where('id',$items_id[0]->stationery_items_id)->value('item_name');
       
        if ($status == 'first_approval') {
            $accept_arr =[
                'first_approval_status' => 'Approved'
            ];
            $userName = \App\User::where('id',$items_id[0]->request_user_id)->value('name');
            $userIds = [$items_id[0]->second_approval_id];
            $this->notification_task->stationeryItemRequestNotify($itemName,$userName,$userIds);
        } elseif ($status == 'second_approval') {
            $accept_arr =[
                'second_approval_status' => 'Approved'
            ];
            $userName = \App\User::where('id',$request_data['user_id'])->value('name');
            $userIds = [$items_id[0]->request_user_id];
            $this->notification_task->stationeryItemAcceptNotify($itemName,$userName,$userIds);  
        } 


        if (Stationery_items_access::where('id', $id)->update($accept_arr)) {

            return response()->json(['status' => true, 'msg' => "Stationery Item Request successfully Accepted.", 'data' => []]);
           
        }
        return response()->json(['status' => false, 'msg' => config('errors.sql_operation.msg'), 'data' => [], 'error' => config('errors.sql_operation.code')]);
    }

    public function api_stationery_items_confirm_request(Request $request) {  
        
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'stationery_access_id' => 'required',
                    'user_type' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $id = $request_data['stationery_access_id'];
        $type = $request_data['user_type'];
        
        if ($type == 'request_user') {
            $confirm_arr =[
                'request_user_status' => 'Submitted'
            ];
        } elseif($type == 'second_approval_user') {
            $confirm_arr =[
                'second_approval_status' => 'Done'
            ];
        }
        
        if (Stationery_items_access::where('id', $id)->update($confirm_arr)) {
            return response()->json(['status' => true, 'msg' => "Stationery Item successfully Confirmed.", 'data' => []]);
        }

        return response()->json(['status' => false, 'msg' => config('errors.sql_operation.msg'), 'data' => [], 'error' => config('errors.sql_operation.code')]);
    }

    public function api_stationery_items_return(Request $request) {  
        
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'stationery_access_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $id = $request_data['stationery_access_id'];
        
        $check_item = Stationery_items_access::where('id', $id)->get(['stationery_items_id','second_approval_id']);

        $return_arr =[
            'request_user_status' => 'Returned'
        ];
        if (Stationery_items_access::where('id', $id)->update($return_arr)) {
            Stationery_items::where('id',$check_item[0]->stationery_items_id)->update(['is_used'=> 'No']);

        //NotificationTask
        
        $itemName = Stationery_items::where('id',$check_item[0]->stationery_items_id)->value('item_name');
        $userName = \App\User::where('id',$request_data['user_id'])->value('name');
        $userIds = [$check_item[0]->second_approval_id];
        $this->notification_task->stationeryItemReturnNotify($itemName,$userName,$userIds);
            return response()->json(['status' => true, 'msg' => "Stationery Item Request successfully Returned.", 'data' => []]);
            
        }

        return response()->json(['status' => false, 'msg' => config('errors.sql_operation.msg'), 'data' => [], 'error' => config('errors.sql_operation.code')]);
    }

    //24/09/2020
    public function api_add_stationery_item_access_request(Request $request) {   //done
        
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'stationery_items_id' => 'required',
                    'request_note'=>'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();

        $StationeryAccessModel = new Stationery_items_access();
        $StationeryAccessModel->stationery_items_id  = $request->input('stationery_items_id');
        $StationeryAccessModel->request_note      = $request->input('request_note');
        
        $StationeryAccessModel->first_approval_id      = Stationery_items::where('id',$request_data['stationery_items_id'])->value('first_approval_id');
        $StationeryAccessModel->first_approval_status      = 'Pending';
        $StationeryAccessModel->second_approval_id      = Stationery_items::where('id',$request_data['stationery_items_id'])->value('second_approval_id');
        $StationeryAccessModel->second_approval_status      = 'Pending';

        $StationeryAccessModel->request_user_id      = $request_data['user_id'];
        $StationeryAccessModel->request_user_status      = 'Requested';
        $StationeryAccessModel->created_at       = date('Y-m-d h:i:s');
        $StationeryAccessModel->updated_at       = date('Y-m-d h:i:s');
        $StationeryAccessModel->created_ip       = $request->ip();
        $StationeryAccessModel->updated_ip       = $request->ip();
        $StationeryAccessModel->updated_by       = $request_data['user_id'];

        if ($StationeryAccessModel->save()) {

            Stationery_items::where('id',$request_data['stationery_items_id'])->update(['is_used'=> 'Yes']);

            //NotificationTask
            $itemName = Stationery_items::where('id',$request_data['stationery_items_id'])->value('item_name');
            $userName = \App\User::where('id',$request_data['user_id'])->value('name');
            $userIds = [Stationery_items::where('id',$request_data['stationery_items_id'])->value('first_approval_id')];
            $this->notification_task->stationeryItemRequestNotify($itemName,$userName,$userIds);
            
            return response()->json(['status' => true, 'msg' => "Stationery Access Request added successfully.", 'data' => []]);
            
        }

        return response()->json(['status' => false, 'msg' => config('errors.sql_operation.msg'), 'data' => [], 'error' => config('errors.sql_operation.code')]);
    }

    public function api_edit_stationery_item_access_request(Request $request) {   //done
        
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'id' => 'required',
                    'stationery_items_id' => 'required',
                    'request_note'=>'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        
        $update_id = $request->input('id'); 
        $existing_item = Stationery_items_access::where('id', $update_id)->value('stationery_items_id');
        if ($request_data['stationery_items_id'] != $existing_item ) {
            Stationery_items::where('id',$existing_item)->update(['is_used'=> 'No']);
            Stationery_items::where('id',$request_data['stationery_items_id'])->update(['is_used'=> 'Yes']);
        }

        $update_arr = [
            'stationery_items_id' => $request->input('stationery_items_id'),
            'request_note' => $request->input('request_note'),
            
            'first_approval_id' => Stationery_items::where('id',$request_data['stationery_items_id'])->value('first_approval_id'),
            'first_approval_status' => 'Pending',
            'second_approval_id' => Stationery_items::where('id',$request_data['stationery_items_id'])->value('second_approval_id'),
            'second_approval_status' => 'Pending',

            'request_user_id' => $request_data['user_id'],
            'request_user_status' => 'Requested',
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => $request_data['user_id'],
        ];
        
        if (Stationery_items_access::where('id', $update_id)->update($update_arr)) {
            //NotificationTask
            $itemName = Stationery_items::where('id',$request_data['stationery_items_id'])->value('item_name');
            $userName = \App\User::where('id',$request_data['user_id'])->value('name');
            $userIds = [Stationery_items::where('id',$request_data['stationery_items_id'])->value('first_approval_id')];
            $this->notification_task->stationeryItemRequestNotify($itemName,$userName,$userIds);

            return response()->json(['status' => true, 'msg' => "Stationery Access Request updated successfully.", 'data' => []]);

        }
    
        return response()->json(['status' => false, 'msg' => config('errors.sql_operation.msg'), 'data' => [], 'error' => config('errors.sql_operation.code')]);
    }

    public function api_delete_stationery_item_access_request(Request $request) {  
        
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();

        //set item in used again
        $existing_item = Stationery_items_access::where('id', $request_data['id'])->value('stationery_items_id');
        Stationery_items::where('id',$existing_item)->update(['is_used'=> 'No']);

        if (Stationery_items_access::where('id', $request_data['id'])->delete()) {
          
            return response()->json(['status' => true, 'msg' => "Stationery Access Request deleted successfully.", 'data' => []]);
        }
        return response()->json(['status' => false, 'msg' => config('errors.sql_operation.msg'), 'data' => [], 'error' => config('errors.sql_operation.code')]);
    }

    public function api_stationery_items_list(Request $request) {  

        $validator = Validator::make($request->all(), [
                    'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();

        $stationery_items = Stationery_items::select('id', 'item_name')
            ->where('status', 'Enabled')
            ->where('is_used', 'No')
            ->orWhere(function ($query) use ($request_data) {
                if (isset($request_data['stationery_items_id'])) {
                    $query->where('id',$request_data['stationery_items_id']);
                }
            })
            ->orderBy('item_name','ASC')->get();

        if ($stationery_items->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        } 

        return response()->json(['status' => true, 'msg' => 'Get records!', 'data' => $stationery_items]);
    }
}
