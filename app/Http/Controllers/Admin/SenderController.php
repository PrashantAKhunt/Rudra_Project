<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use App\Sender;
use Illuminate\Support\Facades\Response;
use App\Lib\Permissions;
use App\Lib\NotificationTask;


class SenderController extends Controller {

    public $data;
    private $module_id = 70;
    private $notification_task;

    public function __construct() {
        $this->notification_task = new NotificationTask();
        $this->data['module_title'] = "Sender";
        $this->data['module_link'] = "admin.sender";
    }

    public function index() {

        $this->data['page_title'] = "Sender";
        $this->data['view_special_permission'] = Permissions::checkSpecialPermission($this->module_id);
        $this->data['sender_list'] = Sender::select('id','name','description','status')->where('is_approved',1)->get();

        return view('admin.sender.index', $this->data);
    }

    public function change_sender_status(Request $request, $id, $status) {
        $update_arr = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip()
        ];

        try {
            Sender::where('id', $id)->update($update_arr);
            return redirect()->route('admin.sender')->with('success', 'Status successfully updated.');
        } catch (Exception $exc) {

            return redirect()->route('admin.sender')->with('error', 'Error Occurred. Try Again!');
        }
    }

    public function add_sender(){
        $view_special_permission = Permissions::checkSpecialPermission($this->module_id);
        if(!$view_special_permission){
            return redirect()->route('admin.dashboard')->with('error','Access denied. You do not have permission to access this module.');
        }

    	$this->data['page_title']='Add Sender';
        return view('admin.sender.add_sender', $this->data);	
    }

    public function insert_sender(Request $request) {
        $rules = array(
            'name' => 'required',
            'description' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return redirect()->route('admin.sender')->with('error', 'Error during operation. Try again!');
        }

        $insert_arr = [
            'user_id' => Auth::user()->id,
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            //'status' => 'Enabled',
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
        Sender::insert($insert_arr);
        $module = 'Registry Sender Category';
        $this->notification_task->entryApprovalNotify($module);

        return redirect()->route('admin.sender')->with('success', 'New Entry inserted successfully.');
    }

    public function edit_sender($id) {
        $view_special_permission = Permissions::checkSpecialPermission($this->module_id);
        if(!$view_special_permission){
            return redirect()->route('admin.dashboard')->with('error','Access denied. You do not have permission to access this module.');
        }

        $this->data['page_title'] = "Edit Sender";
        $this->data['sender_list'] = Sender::select('id','name','description')->where('id', $id)->get();

        return view('admin.sender.edit_sender',$this->data);
    }

    public function update_sender(Request $request) {
        $rules = array(
            'name' => 'required',
            'description' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return redirect()->route('admin.sender')->with('error', 'Error during operation. Try again!');
        }

        $update_arr = [
            'user_id' => Auth::user()->id,
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip()
        ];

            if (Sender::where('id', $request->input('id'))->update($update_arr)) {
                return redirect()->route('admin.sender')->with('success', 'Record Updated Successfully!');
            } else {
                return redirect()->route('admin.sender')->with('success', 'Error during operation. Try again !');
            }
        
    }

    public function delete_sender($id) {
        $sender_id = Sender::where('id', $id)->get();

        if (!empty($sender_id[0])) {

            if (Sender::where('id', $id)->delete()) {

                return redirect()->route('admin.sender')->with('success', 'Delete Sender successfully updated.');
            }

            return redirect()->route('admin.sender')->with('error', 'Error during operation. Try again!');
        } else {

            return redirect()->route('admin.sender')->with('error', 'This sender does not exits !');
        }
    }


}
