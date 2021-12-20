<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\Common_query;
use App\Inventory_items;
use App\Stationery_items_access;
use Illuminate\Support\Facades\Validator;
use App\Lib\CommonTask;
use App\Lib\NotificationTask;
use App\User;
use App\Lib\Permissions;

class StationaryItemsController extends Controller
{

    public $data;
    private $common_task;
    private $notification_task;

    public function __construct() {
        $this->data['module_title'] = "Stationary Items";
     
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();
    }
    
    public function index()
    {
        $this->data['page_title'] = "Stationary Items";
        return view('admin.inventory.index', $this->data);
    }

    public function inventory_items_list_ajax() {
     
        $datatable_fields = array('inventory_items.item_name','inventory_items.item_detail','inventory_items.item_quantity','users.name',
        'inventory_items.created_at', 'inventory_items.updated_at');
        $request = Input::all();
        $conditions_array = [];

        $getfiled = array('inventory_items.*','users.name as emp_name');
        $table = "inventory_items";

        $join_str[0]['join_type']='';
        $join_str[0]['table'] = 'users';
        $join_str[0]['join_table_id'] ='inventory_items.user_id';
        $join_str[0]['from_table_id'] = 'users.id';

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request,$join_str);
                                                  
        die();
        
    }

    public function add_inventory_item()
    {
        $this->data['page_title'] = 'Add Stationary Item';
        $this->data['module_link'] = "admin.stationary_items";
        
        return view('admin.inventory.add_inventory', $this->data);
    }

    public function insert_inventory_item(Request $request)
    {
        $validator_normal = Validator::make($request->all(), [
            'item_name' => 'required',
            'item_detail'=>'required',
            'item_quantity'=>'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.stationary_items')->with('error', 'Please follow validation rules.');
        }

        $Stationery = new Inventory_items();
        $Stationery->item_name             = $request->input('item_name');
        $Stationery->item_detail      = $request->input('item_detail');
        $Stationery->item_quantity       = $request->input('item_quantity');
        $Stationery->user_id       = Auth::user()->id;
        $Stationery->status       = 'Enabled';
        $Stationery->created_at       = date('Y-m-d h:i:s');
        $Stationery->updated_at       = date('Y-m-d h:i:s');
        $Stationery->created_ip       = $request->ip();
        $Stationery->updated_ip       = $request->ip();

        if($Stationery->save()) {
             return redirect()->route('admin.stationary_items')->with('success', 'Stationery Item added successfully.');
        } else {
            return redirect()->route('admin.stationary_items')->with('error', 'Error occurre in insert. Try Again!');
        }
        
    }


    //----------------------- 13/05/2021

    public function item_access_request_list($id)
    {
        $this->data['page_title'] = "Item Access Request";
        $this->data['id'] = $id;
        return view('admin.inventory.view_access_requets', $this->data);
    }


    public function item_access_request_list_ajax(Request $request)
    {
        $datatable_fields = array('users.name',
        'stationery_items_access.requested_quantity',
        'stationery_items_access.approval_quantity',
        'stationery_items_access.request_note',
        'stationery_items_access.request_user_status',
        'stationery_items_access.created_at');
        //$request = Input::all();
        $id = $request->input('id');
        $conditions_array = [
            'stationery_items_access.stationery_items_id' => $id
        ];

        $getfiled = array('inventory_items.id','stationery_items_access.request_user_status',
        'stationery_items_access.requested_quantity',
        'stationery_items_access.approval_quantity',
            'stationery_items_access.request_note', 'stationery_items_access.id as access_request_id',
            'stationery_items_access.created_at','users.name as emp_name');
        $table = "inventory_items";

        $join_str[0]['join_type']='';
        $join_str[0]['table'] = 'stationery_items_access';
        $join_str[0]['join_table_id'] ='inventory_items.id';
        $join_str[0]['from_table_id'] = 'stationery_items_access.stationery_items_id';


        $join_str[1]['join_type'] = '';
        $join_str[1]['table'] = 'users';
        $join_str[1]['join_table_id'] = 'stationery_items_access.request_user_id';
        $join_str[1]['from_table_id'] = 'users.id';


        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request,$join_str);
                                                  
        die();
    }

    public function item_return_request_list($id)
    {
        $this->data['page_title'] = "Item Return Request list";
        $this->data['id'] = $id;
        return view('admin.inventory.view_return_requets', $this->data);
    }

    public function item_return_request_list_ajax(Request $request)
    {

        $datatable_fields = array('users.name',
        'stationery_items_access.requested_quantity',
        'stationery_items_access.approval_quantity',
        'stationery_items_access.return_quantity',
        'stationery_items_access.return_note',
        'stationery_items_access.created_at');
        $id = $request->input('id');
        $conditions_array = [
            'stationery_items_access.return_status' => 'Returned',
            'stationery_items_access.stationery_items_id' => $id
        ];

        $getfiled = array('inventory_items.id','stationery_items_access.request_user_status',
        'stationery_items_access.requested_quantity',
        'stationery_items_access.approval_quantity',
        'stationery_items_access.return_quantity',
            'stationery_items_access.return_note', 'stationery_items_access.id as access_request_id',
            'stationery_items_access.created_at','users.name as emp_name');
        $table = "inventory_items";

        $join_str[0]['join_type']='';
        $join_str[0]['table'] = 'stationery_items_access';
        $join_str[0]['join_table_id'] ='inventory_items.id';
        $join_str[0]['from_table_id'] = 'stationery_items_access.stationery_items_id';


        $join_str[1]['join_type'] = '';
        $join_str[1]['table'] = 'users';
        $join_str[1]['join_table_id'] = 'stationery_items_access.request_user_id';
        $join_str[1]['from_table_id'] = 'users.id';


        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request,$join_str);
                                                  
        die();
      
    }


    public function access_request_approval(Request $request)
    {
        $validator_normal = Validator::make($request->all(), [
            'access_request_id' => 'required',
            'inventory_id' => 'required',
            'approval_quantity'=>'required',
            'approval_note'=>'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.stationary_items')->with('error', 'Please follow validation rules.');
        }

        $request_data = $request->all();
       

        $check_stock = Inventory_items::where('id',$request_data['inventory_id'])->value('item_quantity');

        if($check_stock < $request_data['approval_quantity']) {
            return redirect()->route('admin.stationary_items')->with('error', 'Error occurre in insert. Try Again!');
        }


        $StationeryAccessModel = Stationery_items_access::find($request_data['access_request_id']);
        $StationeryAccessModel->approval_quantity      = $request_data['approval_quantity'];
        $StationeryAccessModel->approve_note      = $request_data['approval_note'];
        $StationeryAccessModel->request_user_status      = 'Accepted';
        $StationeryAccessModel->updated_at       = date('Y-m-d h:i:s');
        $StationeryAccessModel->updated_ip       = $request->ip();
        $StationeryAccessModel->updated_by       = Auth::user()->id;

        if($StationeryAccessModel->save()){

            $InventoryItem = Inventory_items::find($request_data['inventory_id']);
            $InventoryItem->decrement('item_quantity',$request_data['approval_quantity'] );
            if($InventoryItem->save()){
                return redirect()->route('admin.stationary_items')->with('success', 'Inventory item access request successfully approved.');
            } 

        } 

        return redirect()->route('admin.stationary_items')->with('error', 'Error occurre in insert. Try Again!');

    }

    public function access_request_rejection(Request $request)
    {
        $validator_normal = Validator::make($request->all(), [
            'access_request_entry_id' => 'required',
            'reject_note'=>'required',
        ]);
        $request_data = $request->all();
       
        if ($validator_normal->fails()) {
            return redirect()->route('admin.stationary_items')->with('error', 'Please follow validation rules.');
        }

        
        $StationeryAccessModel = Stationery_items_access::find($request_data['access_request_entry_id']);
        $StationeryAccessModel->reject_note      = $request_data['reject_note'];
        $StationeryAccessModel->request_user_status      = 'Rejected';
        $StationeryAccessModel->updated_at       = date('Y-m-d h:i:s');
        $StationeryAccessModel->updated_ip       = $request->ip();
        $StationeryAccessModel->updated_by       = Auth::user()->id;

        if($StationeryAccessModel->save()){
            return redirect()->route('admin.stationary_items')->with('success', 'Inventory item access request successfully rejected.');
        } 

        return redirect()->route('admin.stationary_items')->with('error', 'Error occurre in insert. Try Again!');

    }

    public function confirm_returnItem_to_stock(Request $request, $itemid, $id)
    {

        $return_quantity = Stationery_items_access::where('id',$id)->value('return_quantity');
        $InventoryItem = Inventory_items::find($itemid);
        $InventoryItem->increment('item_quantity',$return_quantity );

        if($InventoryItem->save()){

            $StationeryAccessModel = Stationery_items_access::find($id);
            $StationeryAccessModel->return_status      = 'confirmed';
            $StationeryAccessModel->updated_at       = date('Y-m-d h:i:s');
            $StationeryAccessModel->updated_ip       = $request->ip();
            $StationeryAccessModel->updated_by       = Auth::user()->id;

            if($StationeryAccessModel->save()){
                return redirect()->route('admin.stationary_items')->with('success', 'Inventory stock confirmed and updated.');
            }
        } 
        
        return redirect()->route('admin.stationary_items')->with('error', 'Error occurre in insert. Try Again!');
      
    }








    


}