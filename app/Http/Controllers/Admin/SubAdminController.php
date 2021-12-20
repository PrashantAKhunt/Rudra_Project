<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Common_query;
use Illuminate\Support\Facades\Input;
use App\User;
use App\Role;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class SubAdminController extends Controller
{
    public $data;
    public function __construct() {
        $this->data['module_title'] = "Sub Admins";
        $this->data['module_link'] = "admin.subadmins";
    }

    public function index() {
        $this->data['page_title'] = "Sub Admins";
        return view('admin.subadmin.index', $this->data);
    }

    public function get_subadmin_list() {
        $datatable_fields = array('users.name', 'users.email', 'users.mobile', 'users.status');
        $request = Input::all();
        $conditions_array = [['role_type' ,'=', 'Admin'],['role','!=',1]];

        $getfiled = array('users.id', 'users.name', 'users.email', 'users.mobile', 'users.status');
        //$getfiled=DB::raw('sms.id, sms.sms_body, CONCAT(authenticated_numbers.imei_number,"/",authenticated_numbers.number,"/",authenticated_numbers.device_detail) as imei_number ,sms.status, sms.created_at');
        $table = "users";
        
        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, []);
        die();
    }

    public function add_subadmin() {
        $this->data['page_title'] = 'Add Sub-admin';
        $this->data['role_list'] = Role::where('role_group', 'Admin')->get();
        
        return view('admin.subadmin.add_subadmin', $this->data);
    }

    public function insert_subadmin(Request $request) {
        $validator_normal = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'required',
                    'role' => 'required',
                    'password' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_subadmin')->with('error', 'Please follow validation rules.');
        }
        
        $user_arr = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'mobile' => $request->input('mobile'),
            'role' => $request->input('role'),
            'login_type' => 'Register',
            'role_type' => 'Admin',
            'is_verified' => 'Yes',
            'created_at' => date('Y-m-d h:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d h:i:s'),
            'modified_ip' => $request->ip()
        ];
        if (User::insert($user_arr)) {
            return redirect()->route('admin.subadmins')->with('success', 'New sub-admin inserted successfully.');
        } else {
            return redirect()->route('admin.add_subadmin')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function edit_subadmin($id) {
        $this->data['page_title'] = "Edit Sub-admin";
        $select_list = ['users.id', 'users.name', 'users.email', 'users.mobile', 'users.role'];
        $this->data['user_detail'] = User::where('users.id', $id)->get($select_list);
        if ($this->data['user_detail']->count() == 0) {
            return redirect()->route('admin.subadmin')->with('error', 'Error Occurred. Try Again!');
        }
       
        $this->data['role_list'] = Role::where('role_group', 'User')->get();
        
        return view('admin.subadmin.edit_subadmin', $this->data);
    }

    public function update_subadmin(Request $request) {
        $validator_normal = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'required',
                    'role' => 'required',
                    'id' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_user')->with('error', 'Please follow validation rules.');
        }
        
        $user_id= $request->input('id');
        
        $user_arr = [
            'name' => $request->input('name'),
            'mobile' => $request->input('mobile'),
            'role' => $request->input('role'),
            'updated_at' => date('Y-m-d h:i:s'),
            'modified_ip' => $request->ip()
        ];
        
        User::where('id',$user_id)->update($user_arr);
        
        return redirect()->route('admin.subadmins')->with('success','Sub-admin successfully updated.');
    }
    
    public function check_user_exists(Request $request){
        $validator= Validator::make($request->all(),[
            'email'=>'required'
        ]);
        if($validator->fails()){
            echo 'false'; die();
        }
        
        $user_exists= User::where('email',$request->input('email'))->get('id');
        if($user_exists->count()>0){
            echo 'false'; die();
        }
        else{
            echo 'true'; die();
        }
    }
}
