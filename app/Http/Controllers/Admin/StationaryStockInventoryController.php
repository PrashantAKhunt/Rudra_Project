<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\Common_query;
use App\Inventory_stocks;
use App\Inventory_items;
use Illuminate\Support\Facades\Validator;
use App\Lib\CommonTask;
use App\Lib\Upload_file;
use App\Lib\NotificationTask;
use App\User;
use App\Inventory_manager;
use App\Inventory_purchase_records;
use App\Lib\Permissions;

class StationaryStockInventoryController extends Controller
{

    public $data;
    private $common_task;
    private $notification_task;
    public $upload_file;

    public function __construct() {
        $this->data['module_title'] = "Stationery Stock Inventory";
     
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();
        $this->upload_file = new Upload_file();
    }
    
    public function inventory_stock_requests()
    {

        $this->data['page_title'] = "Inventory stock request";
        $this->data['invemtory_manager'] = Inventory_manager::where('manager_type','invemtory_manager')->value('manager_id');
        $this->data['purchase_manager'] = Inventory_manager::where('manager_type','purchase_manager')->value('manager_id');

        return view('admin.stock_inventory.index', $this->data);
    }

    public function inventory_stock_requests_list_ajax() {
     
        $datatable_fields = array('inventory_items.item_name',
        'inventory_stocks.detail',
        'inventory_stocks.item_quantity',
        'users.name',
        'inventory_stocks.inventory_manager_approval',
        // 'inventory_stocks.inventory_manager_approval_datetime',
        'inventory_stocks.hr_approval',
        // 'inventory_stocks.hr_approval_datetime',
        'inventory_stocks.third_approval',
        // 'inventory_stocks.third_approval_datetime',
         'inventory_stocks.purchase_approval',
        //  'inventory_stocks.purchase_approval_datetime',
         'inventory_stocks.created_at');
        $request = Input::all();
        $conditions_array = [];

        $getfiled = array('inventory_stocks.*','inventory_items.item_name','users.name as emp_name');
        $table = "inventory_stocks";

        $join_str[0]['join_type']='';
        $join_str[0]['table'] = 'users';
        $join_str[0]['join_table_id'] ='inventory_stocks.user_id';
        $join_str[0]['from_table_id'] = 'users.id';

        $join_str[1]['join_type']='';
        $join_str[1]['table'] = 'inventory_items';
        $join_str[1]['join_table_id'] ='inventory_stocks.inventory_item_id';
        $join_str[1]['from_table_id'] = 'inventory_items.id';

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request,$join_str);
                                                  
        die();
        
    }

    public function add_inventory_stock_request()
    {
        $this->data['page_title'] = 'Add Inventory request';
        $this->data['module_link'] = "admin.inventory_stock_requests";
        $this->data['stationery_items'] = Inventory_items::select('id', 'item_name')->where('status', 'Enabled')->orderBy('item_name','ASC')->get()->toArray();
        return view('admin.stock_inventory.add_stock_request', $this->data);
    }

    public function insert_inventory_stock_request(Request $request)
    {
        $validator_normal = Validator::make($request->all(), [
            'inventory_item_id' => 'required',
            'detail'=>'required',
            'item_quantity'=>'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.inventory_stock_requests')->with('error', 'Please follow validation rules.');
        }

        $Stationery = new Inventory_stocks();
        $Stationery->inventory_item_id             = $request->input('inventory_item_id');
        $Stationery->detail      = $request->input('detail');
        $Stationery->item_quantity       = $request->input('item_quantity');
        $Stationery->inventory_manager_approval       = 'Processing';
        $Stationery->inventory_manager_id       = Auth::user()->id;
        $Stationery->inventory_manager_approval_datetime       = date('Y-m-d h:i:s');
        $Stationery->hr_approval       = 'Pending';
        $Stationery->third_approval       = 'Pending';
        $Stationery->hr_approval       = 'Pending';
        $Stationery->purchase_approval       = 'Pending';
        $Stationery->user_id       = Auth::user()->id;
        $Stationery->created_at       = date('Y-m-d h:i:s');
        $Stationery->updated_at       = date('Y-m-d h:i:s');
        $Stationery->created_ip       = $request->ip();
        $Stationery->updated_ip       = $request->ip();

        if($Stationery->save()) {
             return redirect()->route('admin.inventory_stock_requests')->with('success', 'Stationery stock request added successfully.');
        } else {
            return redirect()->route('admin.inventory_stock_requests')->with('error', 'Error occurre in insert. Try Again!');
        }
        
    }

    public function stock_request_approval(Request $request,$id)
    {

        $request_data = $request->all;

        $update_data = [];
        $userId = Auth::user()->id; 
        $userRole = Auth::user()->role; 
        $inventory_stock_id = $id;
        $invemtory_manager = Inventory_manager::where('manager_type','invemtory_manager')->value('manager_id');
        $purchase_manager = Inventory_manager::where('manager_type','purchase_manager')->value('manager_id');
        
        $get_approvals = Inventory_stocks::where('id', $inventory_stock_id)
                ->get(['inventory_manager_approval', 'hr_approval',
                'third_approval', 'purchase_approval'])->first();

        if($userRole == config('constants.REAL_HR')) {

               if($userId == $purchase_manager && $get_approvals['third_approval'] == 'Processing') {
                $update_data = [
                    'purchase_approval' => 'Processing',
                    'purchase_approval_id' => $userId,
                    'purchase_approval_datetime' => date('Y-m-d H:i:s')
                ];
               } else {
                $update_data = [
                    'hr_approval' => 'Processing',
                    'hr_approval_id' => $userId,
                    'hr_approval_datetime' => date('Y-m-d H:i:s')
                ];
               }
                
        } elseif ($userRole == config('constants.Admin')) {

            if($userId == $purchase_manager) {
                $update_data = [
                    'third_approval' => 'Processing',
                    'third_approval_id' => $userId,
                    'third_approval_datetime' => date('Y-m-d H:i:s'),
                    'purchase_approval' => 'Processing',
                    'purchase_approval_id' => $userId,
                    'purchase_approval_datetime' => date('Y-m-d H:i:s')
                ];
               } else {
                $update_data = [
                    'third_approval' => 'Processing',
                    'third_approval_id' => $userId,
                    'third_approval_datetime' => date('Y-m-d H:i:s')
                ];
               }
            
        } elseif ($userId == $purchase_manager){
            $update_data = [
                'purchase_approval' => 'Processing',
                'purchase_approval_id' => $userId,
                'purchase_approval_datetime' => date('Y-m-d H:i:s')
            ];
        }

        if(Inventory_stocks::where('id', $inventory_stock_id)->update($update_data)) {
                return redirect()->route('admin.inventory_stock_requests')->with('success', 'Stationery stock request approved successfully.');
        } else {
            return redirect()->route('admin.inventory_stock_requests')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function purchase_completion(Request $request)
    {
        $validator_normal = Validator::make($request->all(), [
            'inventory_stock_request_id' => 'required',
            'item_quantity'=>'required',
            'price'=>'required',
            'proof'=>'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.inventory_stock_requests')->with('error', 'Please follow validation rules.');
        }
    

        $proof_image = '';
        if ($request->file('proof')) {

            $proof = $request->file('proof');
            $file_path = $this->upload_file->upload_s3_file($proof, 'inventory_proof/');
            if ($file_path) {
                $proof_image = $file_path;
            } 
        }

        $Inventory = new Inventory_purchase_records();
        $Inventory->inventory_stock_request_id             = $request->input('inventory_stock_request_id');
        $Inventory->item_quantity      = $request->input('item_quantity');
        $Inventory->price       = $request->input('price');
        $Inventory->proof       = $proof_image;
        $Inventory->user_id       = Auth::user()->id;
        $Inventory->created_at       = date('Y-m-d h:i:s');
        $Inventory->created_ip       = $request->ip();
        $Inventory->updated_at       = date('Y-m-d h:i:s');
        $Inventory->updated_ip       = $request->ip();
         
        if($Inventory->save()) {
            $update_inventory_data = [
                'purchase_approval' => 'Purchased',
                'purchase_approval_datetime' => date('Y-m-d h:i:s'),
                'updated_at' => date('Y-m-d h:i:s'),
                'updated_ip' => $request->ip()
            ];
        if( Inventory_stocks::where('id', $request->input('inventory_stock_request_id'))->update($update_inventory_data)){
            return redirect()->route('admin.inventory_stock_requests')->with('success', 'Stationery item purchased successfully.');
        }
       } 
        return redirect()->route('admin.inventory_stock_requests')->with('error', 'Error occurre in insert. Try Again!');
       
    }

    public function purchase_cofirmed_by_inventory_manager(Request $request,$id)
    {
        $purchase_data = Inventory_purchase_records::where('inventory_stock_request_id', $id)->value('item_quantity');
        $inventory_item_id = Inventory_stocks::where('id',$id)->value('inventory_item_id');

        $update_inventory_request_data = [
            'inventory_manager_approval' => 'Completed',
            'inventory_manager_approval_datetime' => date('Y-m-d h:i:s'),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip()
        ];
        if(Inventory_stocks::where('id', $id)->update($update_inventory_request_data)){

            $InventoryItem = Inventory_items::find($inventory_item_id);
            $InventoryItem->increment('item_quantity',$purchase_data );
            if($InventoryItem->save()){
                return redirect()->route('admin.inventory_stock_requests')->with('success', 'Inventory stock confirmed and updated.');
            } 
        }
        return redirect()->route('admin.inventory_stock_requests')->with('error', 'Error occurre in insert. Try Again!');
        
    }

    public function get_purchase_proof_details(Request $request)  //ajax call
    {
        $validator_normal = Validator::make($request->all(), ['id' => 'required']);
        if ($validator_normal->fails()) {
            return response()->json(['status' => false]);
        }

        $request_data = $request->all();
        $id = $request_data['id'];

        $purchase_data = Inventory_purchase_records::where('inventory_stock_request_id', $id)->first()->toArray();
        if(count($purchase_data) > 0) {
            return response()->json(['status' => true, 'data' => $purchase_data ]);
        } else {
            return response()->json(['status' => false, 'data' => []]);
        }

    }

    

    

}