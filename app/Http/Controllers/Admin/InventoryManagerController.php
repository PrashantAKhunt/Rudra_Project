<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;

use App\User;
use App\Inventory_manager;

class InventoryManagerController extends Controller {


    public $data;

    public function __construct() {

        $this->data['module_title'] = 'Inventory User Setting';
    }

    public function add_inventory_managers() {

        $this->data['page_title'] = "Inventory User Setting"  ;
        $this->data['users'] = User::where('status', 'Enabled')->orderBy('name')->pluck('name', 'id')->toArray();
        $this->data['invemtory_manager'] = Inventory_manager::where('manager_type','invemtory_manager')->value('manager_id');
        $this->data['purchase_manager'] = Inventory_manager::where('manager_type','purchase_manager')->value('manager_id');
    
      
        return view('admin.inventory_manager.index', $this->data);
    }

    public function save_manager_types(Request $request) {

        $validator_normal = Validator::make($request->all(), [
            'invemtory_manager' => 'required',
            'purchase_manager' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_special_permission')->with('error', 'Please follow validation rules.');
        }

        $request_data = $request->all();
        
        $check_entry = Inventory_manager::get()->toArray();
   
        $invemtory_manager_daa = [
            'manager_type' => 'invemtory_manager',
            'manager_id' => $request_data['invemtory_manager'],
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_by' => Auth::user()->id,
        ];

        $purchase_manager_data = [
            'manager_type' => 'purchase_manager',
            'manager_id' => $request_data['purchase_manager'],
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_by' => Auth::user()->id,
        ];

        // if (!empty($check_entry)) {
        //     Inventory_manager::truncate();
        // }

		if(Inventory_manager::where('id',1)->update($invemtory_manager_daa) && Inventory_manager::where('id',2)->update($purchase_manager_data)) {
            return redirect()->route('admin.add_inventory_managers')->with('success', 'Inventory managers update successfully.');
        }
        return redirect()->route('admin.add_inventory_managers')->with('error', 'Error occurre in insert. Try Again!');
		

    }

}
