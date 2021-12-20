<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\Common_query;
use App\Stationery_items;
use App\Inventory_items;
use App\Stationery_items_access;
use Illuminate\Support\Facades\Validator;
use App\Lib\CommonTask;
use App\Lib\NotificationTask;
use App\User;
use App\Lib\Permissions;

class StationeryController extends Controller
{
    public $data;
    private $common_task;
    private $notification_task;

    public function __construct() {
        $this->data['module_title'] = "Stationery Management";
     
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();
    }


    public function stationery_items()
    {
        $this->data['page_title'] = "Stationery List";
        $stationery_add_permission = Permissions::checkPermission(78, 3);
        $stationery_edit_permission = Permissions::checkPermission(78, 2);

        if (!Permissions::checkPermission(78, 5)) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You do not have access to this module.');
        }
        $this->data['stationery_add_permission'] = $stationery_add_permission;
        $this->data['stationery_edit_permission'] = $stationery_edit_permission;
        
        return view('admin.stationery.stationery_items', $this->data);
    }

    public function stationery_items_list_ajax() {

        $datatable_fields = array('stationery_items.item_name','stationery_items.item_detail','stationery_items.item_image','stationery_items.item_price','stationery_items.status','users.name','users.name');
        $request = Input::all();
        $conditions_array = [];

        $getfiled = array('stationery_items.*','users.name as first_approval_user','B.name as second_approval_user');
        $table = "stationery_items";

        $join_str[0]['join_type']='';
        $join_str[0]['table'] = 'users';
        $join_str[0]['join_table_id'] ='stationery_items.first_approval_id';
        $join_str[0]['from_table_id'] = 'users.id';

        $join_str[1]['join_type']='';
        $join_str[1]['table'] = 'users as B';
        $join_str[1]['join_table_id'] ='stationery_items.second_approval_id';
        $join_str[1]['from_table_id'] = 'B.id';

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request,$join_str);
                                                  
        die();
    }

    public function change_stationery_item_status(Request $request, $id, $status) {
        $update_arr = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip()
        ];

        try {
            Stationery_items::where('id', $id)->update($update_arr);
            return redirect()->route('admin.stationery_items')->with('success', 'Status successfully updated.');
        } catch (Exception $exc) {

            return redirect()->route('admin.stationery_items')->with('error', 'Error Occurred. Try Again!');
        }
    }

    public function add_stationery_items()
    {
        if (!Permissions::checkPermission(78, 3)) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You do not have access to this module.');
        }
        $this->data['page_title'] = 'Add Stationery';
        $this->data['module_link'] = "admin.stationery_items";
        $this->data['users_data'] = User::select('id', 'name')->where('status', 'Enabled')->orderBy('name')->where('is_user_relieved', 0)->get();
        return view('admin.stationery.add_stationery_items', $this->data);
    }

    public function insert_stationery_items(Request $request)
    {
        $validator_normal = Validator::make($request->all(), [
            'item_name' => 'required',
            'item_detail'=>'required',
            'item_price'=>'required',
            'first_approval_id' => 'required',
            'second_approval_id' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.stationery_items')->with('error', 'Please follow validation rules.');
        }

        $Stationery = new Stationery_items();
        $Stationery->item_name             = $request->input('item_name');
        $Stationery->item_detail      = $request->input('item_detail');
        $Stationery->item_price       = $request->input('item_price');
        $Stationery->first_approval_id       = $request->input('first_approval_id');
        $Stationery->second_approval_id       = $request->input('second_approval_id');
        $Stationery->created_at       = date('Y-m-d h:i:s');
        $Stationery->updated_at       = date('Y-m-d h:i:s');
        $Stationery->created_ip       = $request->ip();
        $Stationery->updated_ip       = $request->ip();
       
        if ($Stationery->save()) {
           
            $stationery_image = '';
            if ($request->hasFile('item_image')) {
                $stationery_image = $request->file('item_image');
            
                    $file_path = $stationery_image->store('public/stationery_items');
                    if ($file_path) {
                        $update_data = [
                            'item_image' => $file_path
                        ];
                        Stationery_items::where('id',$Stationery->id)->update($update_data);
                       /*  $stationery_image = $file_path;
                        $StationeryModel = new Stationery_items();
                        $StationeryModel->id = $Stationery->id;
                        $StationeryModel->item_image   = $stationery_image;
                        $StationeryModel->save(); */
                    
                }                               
            }

            return redirect()->route('admin.stationery_items')->with('success', 'Stationery added successfully.');
        } else {
            return redirect()->route('admin.stationery_items')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function edit_stationery_items($id)
    {
        if (!Permissions::checkPermission(78, 2)) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You do not have access to this module.');
        }

        $this->data['page_title'] = 'Edit Stationery';
        $this->data['module_link'] = "admin.stationery_items";
        $this->data['stationery_data'] = Stationery_items::where('id',$id)->first();
        $this->data['users_data'] = User::select('id', 'name')->where('status', 'Enabled')->where('is_user_relieved', 0)->get();
        return view('admin.stationery.edit_stationery_items', $this->data);
    }

    public function update_stationery_items(Request $request)
    {
        $validator_normal = Validator::make($request->all(), [
            'Stationery_id' => 'required',
            'item_name' => 'required',
            'item_detail'=>'required',
            'item_price'=>'required',
            'first_approval_id' => 'required',
            'second_approval_id' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.stationery_items')->with('error', 'Please follow validation rules.');
        }

        $Stationery_id = $request->input('Stationery_id'); 

        //upload user profile image
        $upload_image = '';
        if ($request->hasFile('item_image')) {
            $stationery_image = $request->file('item_image');
            
                $file_path = $stationery_image->store('public/stationery_items');
                if ($file_path) {
                    $upload_image = $file_path;
                    $StationeryModel = new Stationery_items();
                    $StationeryModel->id = $Stationery_id;
                    $StationeryModel->item_image   = $file_path;
                    $StationeryModel->save();
                } 
                                         
        }

        $stationery_arr = [
            'item_name' => $request->input('item_name'),
            'item_detail' => $request->input('item_detail'),
            'item_price' => $request->input('item_price'),
            'first_approval_id'  =>  $request->input('first_approval_id'),
            'second_approval_id'  =>  $request->input('second_approval_id'),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
        ];
        
        Stationery_items::where('id', $Stationery_id)->update($stationery_arr);

        return redirect()->route('admin.stationery_items')->with('success', 'Stationery updated successfully.');
    }

    public function delete_stationery_items($id)
    {
        $check_result=Permissions::checkPermission(78,4);
        if(!$check_result){
            return redirect()->route('admin.dashboard')->with('error','Access Denied. You are not authorized to access that page.');
        }

        if (Stationery_items::where('id', $id)->delete()) {
            return redirect()->route('admin.stationery_items')->with('success', 'Delete successfully updated.');
        }
        return redirect()->route('admin.stationery_items')->with('error', 'Error during operation. Try again!');
    }

    //-------- stationery-requests

    public function stationery_access_requests()
    {
        $this->data['page_title'] = "Stationery Access Requests";

        return view('admin.stationery.stationery_access_requests', $this->data);
    }

    public function stationery_access_requests_list_ajax()
    {

        $datatable_fields = array('inventory_items.item_name',
        'stationery_items_access.requested_quantity',
        'stationery_items_access.approval_quantity',
        'stationery_items_access.request_note',
        'stationery_items_access.request_user_status',
        'stationery_items_access.created_at');
        $request = Input::all();
        $conditions_array = [
            'stationery_items_access.request_user_id' => Auth::user()->id
        ];

        $getfiled = array('stationery_items_access.*','inventory_items.item_name');
        $table = "stationery_items_access";


        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'inventory_items';
        $join_str[0]['join_table_id'] = 'inventory_items.id';
        $join_str[0]['from_table_id'] = 'stationery_items_access.stationery_items_id';


        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request,$join_str);
                                                  
        die();
    }

    public function add_stationery_item_access_request()
    {
    

        $this->data['page_title'] = 'Add Stationery Access Requests';
        $this->data['module_link'] = "admin.stationery_access_requests";
        $this->data['stationery_items'] = Inventory_items::select('id', 'item_name')->where('status', 'Enabled')->orderBy('item_name','ASC')->get()->toArray();
        return view('admin.stationery.add_stationery_item_access_request', $this->data);
    }


    public function edit_stationery_item_access_request($id)  //not in use
    {

        $this->data['page_title'] = 'Edit Stationery Access Requests';
        $this->data['module_link'] = "admin.stationery_access_requests";
        $this->data['stationery_access_data'] = $stationery_access_data = Stationery_items_access::where('id',$id)->first();
        $this->data['stationery_items'] = Inventory_items::select('id', 'item_name')->where('status', 'Enabled')->orderBy('item_name','ASC')->get()->toArray();
        return view('admin.stationery.edit_stationery_item_access_request', $this->data);
    }

    public function insert_stationery_item_access_request(Request $request)
    {
        $validator_normal = Validator::make($request->all(), [
            'stationery_items_id' => 'required',
            'requested_quantity'=>'required',
            'request_note'=>'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.stationery_access_requests')->with('error', 'Please follow validation rules.');
        }
        $request_data = $request->all();

        $StationeryAccessModel = new Stationery_items_access();
        $StationeryAccessModel->stationery_items_id  = $request->input('stationery_items_id');
        $StationeryAccessModel->requested_quantity      = $request->input('requested_quantity');
        $StationeryAccessModel->request_note      = $request->input('request_note');

        $StationeryAccessModel->request_user_id      = Auth::user()->id;
        $StationeryAccessModel->request_user_status      = 'Pending';
        $StationeryAccessModel->created_at       = date('Y-m-d h:i:s');
        $StationeryAccessModel->updated_at       = date('Y-m-d h:i:s');
        $StationeryAccessModel->created_ip       = $request->ip();
        $StationeryAccessModel->updated_ip       = $request->ip();
        $StationeryAccessModel->updated_by       = Auth::user()->id;

        if ($StationeryAccessModel->save()) {

            //NotificationTask
          
            
            return redirect()->route('admin.stationery_access_requests')->with('success', 'Stationery Access Request added successfully.');
        } else {
            return redirect()->route('admin.stationery_access_requests')->with('error', 'Error occurre in insert. Try Again!');
        }
    }


    public function update_stationery_item_access_request(Request $request)  //not in use
    {
        $validator_normal = Validator::make($request->all(), [
            'id' => 'required',
            'stationery_items_id' => 'required',
            'request_note'=>'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.stationery_access_requests')->with('error', 'Please follow validation rules.');
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

            'request_user_id' => Auth::user()->id,
            'request_user_status' => 'Requested',
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id,
        ];
        
        Stationery_items_access::where('id', $update_id)->update($update_arr);
        //NotificationTask
        $itemName = Stationery_items::where('id',$request_data['stationery_items_id'])->value('item_name');
        $userName = Auth::user()->name;
        $userIds = [Stationery_items::where('id',$request_data['stationery_items_id'])->value('first_approval_id')];
        $this->notification_task->stationeryItemRequestNotify($itemName,$userName,$userIds);

        return redirect()->route('admin.stationery_access_requests')->with('success', 'Stationery Access Request updated successfully.');
    }

    public function delete_stationery_item_access_request($id)
    {

        if (Stationery_items_access::where('id', $id)->delete()) {
            return redirect()->route('admin.stationery_access_requests')->with('success', 'Stationery Access Request successfully deleted.');
        }
        return redirect()->route('admin.stationery_access_requests')->with('error', 'Error during operation. Try again!');
    }

    public function accept_stationery_item_access_request($id,$status)   //not in use
    {

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
            $userName = Auth::user()->name;
            $userIds = [$items_id[0]->request_user_id];
            $this->notification_task->stationeryItemAcceptNotify($itemName,$userName,$userIds);  
        } 
        
        if (Stationery_items_access::where('id', $id)->update($accept_arr)) {

            return redirect()->route('admin.stationery_access_requests')->with('success', 'Stationery Item Request successfully Accepted.');
        }
        return redirect()->route('admin.stationery_access_requests')->with('error', 'Error during operation. Try again!');
    }

    public function confirm_stationery_item_access_request($id,$type)   //not in use
    {
        
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
            return redirect()->route('admin.stationery_access_requests')->with('success', 'Stationery Item successfully Confirmed.');
        }
        return redirect()->route('admin.stationery_access_requests')->with('error', 'Error during operation. Try again!');
    }

    public function return_stationery_item($id)
    {
        $check_item = Stationery_items_access::where('id', $id)->get(['stationery_items_id','second_approval_id']);

        $return_arr =[
            'request_user_status' => 'Returned'
        ];
        if (Stationery_items_access::where('id', $id)->update($return_arr)) {
            Stationery_items::where('id',$check_item[0]->stationery_items_id)->update(['is_used'=> 'No']);

        //NotificationTask
        
        $itemName = Stationery_items::where('id',$check_item[0]->stationery_items_id)->value('item_name');
        $userName = Auth::user()->name;
        $userIds = [$check_item[0]->second_approval_id];
        $this->notification_task->stationeryItemReturnNotify($itemName,$userName,$userIds);

            return redirect()->route('admin.stationery_access_requests')->with('success', 'Stationery Item Request successfully Returned');
        }
        return redirect()->route('admin.stationery_access_requests')->with('error', 'Error during operation. Try again!');
    }

    public function add_returnItem_to_stock(Request $request)  //new
    {

        $validator_normal = Validator::make($request->all(), [
            'access_request_id' => 'required',
            'return_quantity' => 'required',
            'return_note'=>'required',
        ]);
        $request_data = $request->all();
       
        if ($validator_normal->fails()) {
            return redirect()->route('admin.stationery_access_requests')->with('error', 'Please follow validation rules.');
        }

        
        $StationeryAccessModel = Stationery_items_access::find($request_data['access_request_id']);
        $StationeryAccessModel->return_quantity      = $request_data['return_quantity'];
        $StationeryAccessModel->return_note      = $request_data['return_note'];
        $StationeryAccessModel->return_status      = 'Returned';
        $StationeryAccessModel->updated_at       = date('Y-m-d h:i:s');
        $StationeryAccessModel->updated_ip       = $request->ip();
        $StationeryAccessModel->updated_by       = Auth::user()->id;

        if($StationeryAccessModel->save()){
            return redirect()->route('admin.stationery_access_requests')->with('success', 'Inventory item returned successfully.');
        } 

        return redirect()->route('admin.stationery_access_requests')->with('error', 'Error occurre in insert. Try Again!');
      
    }
}
