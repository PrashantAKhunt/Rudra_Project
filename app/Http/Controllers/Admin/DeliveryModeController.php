<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Inward_outward_delivery_mode;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Lib\Permissions;
use App\Imports\BankTransactionImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Lib\NotificationTask;

class DeliveryModeController extends Controller {

    public $data;
    public $notification_task;
    

    public function __construct() {
        $this->notification_task = new NotificationTask();
        $this->data['module_title'] = "Delivery Modes";
        $this->data['module_link'] = "admin.delivery_mode";
        $this->module_id = 69;
    }

    public function index()
    {
        $this->data['page_title'] = "Delivery Modes";
        $this->data['view_special_permission'] = Permissions::checkSpecialPermission($this->module_id);
        $this->data['modes'] = Inward_outward_delivery_mode::where('is_approved',1)->get();

        return view('admin.delivery_mode.delivery_mode_list', $this->data);
    }

    public function change_delivery_mode_status(Request $request, $id, $status)
    {
        $update_arr = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip()
        ];

        try {
            Inward_outward_delivery_mode::where('id', $id)->update($update_arr);
            return redirect()->route('admin.delivery_mode')->with('success', 'Status successfully updated.');
        } catch (Exception $exc) {
            return redirect()->route('admin.delivery_mode')->with('error', 'Error Occurred. Try Again!');
        }
    }

    public function add_delivery_mode(Request $request)
    {

        $insert_arr = [
            'user_id' => Auth::user()->id,
            'name' => $request->input('name'),
            'created_at' => date('Y-m-d H:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip()
        ];
        if (Auth::user()->role != config('constants.SuperUser')) {
            $insert_arr['is_approved'] = 0;
            $insert_arr['status'] = 'Disabled';
        } else {
            $insert_arr['is_approved'] = 1;
            $insert_arr['status'] = 'Enabled';
        }

        Inward_outward_delivery_mode::insert($insert_arr);
        $module = 'Registry Delivery Mode';
        $this->notification_task->entryApprovalNotify($module);

        return redirect()->route('admin.delivery_mode')->with('success', 'New Delivery Mode inserted successfully.');
    }

    public function edit_delivery_mode($id)
    {
        $this->data['delivery_mode'] = Inward_outward_delivery_mode::where('id', $id)->first();

        return view('admin.delivery_mode.edit_delivery_mode', $this->data );
    }

    public function update_delivery_mode(Request $request)
    {
        $rules = array(
            'name' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return redirect()->route('admin.delivery_mode')->withErrors($validator);
        } 

        $update_arr = [
            'user_id' => Auth::user()->id,
            'name' => $request->input('name'),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip()
        ];

            if (Inward_outward_delivery_mode::where('id', Input::get('id'))->update($update_arr)) {
                return redirect()->route('admin.delivery_mode')->with('success', 'Record Updated Successfully!');
            } else {
                return redirect()->route('admin.delivery_mode')->with("error", "Not Change Any Values!");
            }
        
    }

    public function delete_delivery_mode($id)
    {

            if (Inward_outward_delivery_mode::where('id', $id)->delete()) {

                return redirect()->route('admin.delivery_mode')->with('success', 'Delete Delivery mode successfully updated.');
            }

            return redirect()->route('admin.delivery_mode')->with('error', 'Error during operation. Try again!');
      
    }
 

}
