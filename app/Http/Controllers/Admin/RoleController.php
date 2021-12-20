<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Roles;
use App\Module;
use App\Role_module;
use Session;
use App\Lib\Permissions;
class RoleController extends Controller {
    /*
     * called the index view of the role
     */

    public $data;
    protected $loggedin_user;

    public function __construct() {
         
        $this->data['module_title'] = 'Roles';
        $this->data['module_link'] = 'admin.roles';
        
    }

    public function index() {
        $check_result=Permissions::checkPermission();
        
        if(!$check_result){
            return redirect()->route('admin.dashboard')->with('error','Access Denied. You are not authorized to access that page.');
        }
        
        $this->data['page_title'] = "Roles and Permissions";
        return view('admin/roles.index', $this->data);
    }

    /*
     * Get and fetch the role details and show it into the data table
     */

    public function getRole() {
        $columns = array('id', 'role_name');
        $request = Input::all();
        $condition = [['id', '!=', 1], ['role_group', '=', 'Admin']];
        $getfiled = array("id", "role_name");
        echo Roles::getRolesRecord('role', $columns, $condition, $getfiled, $request);
        exit;
    }

    /*
     * called the addview roles
     */

    public function addroles(Request $request) {
        $module = Module::All();
        $this->data['page_title'] = "Add Roles and Permissions";
        $this->data['module'] = $module;
        return view('admin/roles.add', $this->data);
    }

    /*
     * insert role into the database 
     */

    public function insertRole() {
        $rules = array(
            'role_name' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Redirect::to('roles')
                            ->withErrors($validator)
                            ->withInput(Input::except('password'));
        } else {
            $permission_array = Input::get('permissions');      // fetch the permissions array
            if (empty($permission_array)) {
                return Redirect::to('roles')
                                ->with('error', 'Please select the Permission!');
            }
            $role_data = array(
                "role_name" => Input::get('role_name'), // set the rolename
            );
            $roles = new Roles;
            $roles->role_name = Input::get('role_name');
            $roles->role_group = 'Admin';
            $roles->save();
            $role_id = $roles->id;
            $i = 0;
            /* all the values get in permission array and set the role module data array */
            foreach ($permission_array as $key => $permission) {
                $module = new Role_module;
                $module->role_id = $role_id;
                $module->module_id = $key;
                $module->access_level = implode(',', array_keys($permission));
                $module->save();
                $i++;
            }
            return Redirect::to('roles')
                            ->with('success', 'Admin role created.');
        }
    }

    /*
     * All the details will fetch from the database and called the edit views of role in edit role page
     */

    public function editroles(Request $request) {
        $role_id = $request->id;
        $roledetails = Roles::where('id', $role_id)->get();
        $roleData = $roledetails->toArray();
        $module = Module::All();
        $rolemoduledetails = Role_module::where('role_id', $role_id)->get();
        $role_module = $rolemoduledetails->toArray();
        $this->data['page_title'] = "Edit Roles and Permissions";
        $this->data['module'] = $module;
        $this->data['role_module'] = $role_module;
        $this->data['roleData'] = $roleData;

        return view('admin/roles.edit', $this->data);
    }

    /*
     * update the particular roles into the database
     */

    public function updateroles() {
        $role_id = Input::get('role_id');
        $rules = array(
            'role_name' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return Redirect::to('roles')
                            ->withErrors($validator)
                            ->withInput(Input::except('password'));
        } else {


            $roles = Roles::find($role_id);
            $permission_array = Input::get('permissions'); // get the new permission array
            if (!empty($permission_array)) {
                $roles->role_name = Input::get('role_name');
                $roles->save();
                // delete data in rolemodule 

                $role_module = Role_module::where('role_id', $role_id)->delete();


                // echo "<pre>"; print_r($permission_array); die();	
                $role_module_data = array();
                $admin_module_data = array();
                $i = 0;



                // get the all permission array value and set to the role modules array 
                foreach ($permission_array as $key => $permission) {
                    $module = new Role_module;
                    $module->role_id = $role_id;
                    $module->module_id = $key;
                    $module->access_level = implode(',', array_keys($permission));
                    $module->save();
                    $i++;
                }
                $k = array_keys($permission_array);
                return Redirect::to('roles')
                                ->with('success', 'Role Updated successfully.');
            } else {
                return Redirect::to('roles')
                                ->with('error', 'No any Changes Occured.');
            }
        }
    }

    /*
     * delete the selected roles into the database
     */

    public function deleteroles(Request $request) {
        $role_id = $request->id;
        if ($request->id == '') {
            return Redirect::to('roles')
                            ->with('error', 'Error Occurred. Try Again!');
        }

        $role_user_details = User::where('role', $role_id)->where('status', 'Enable')->get();

        if ($role_user_details->count() > 0) {
            return Redirect::to('roles')
                            ->with('error', 'Role is already assigned. You can not delete this role. You have to assign different role to all users currently with this role.');
        }
        $role_user_name = $role_user_details->toArray();
        /*
         * delete in role tables
         */
        if (Roles::where('id', $role_id)->delete()) {
            $updatedData = array(
                "status" => 'Disable',
                "role" => '0'
            );
            // update data in role user data
            foreach ($role_user_name as $role_user) {
                User::where('id', $role_user['id'])
                        ->where('user_type', 'subadmin')->update(['status' => 'Disable', 'role' => 0]);
            }
            // delete the data in role module table.
            Role_module::where('role_id', $role_id)->delete();

            return Redirect::to('roles')
                            ->with('success', 'Role deleted successfully');
        } else {
            return Redirect::to('roles')
                            ->with('error', 'Error Occurred. Try Again!');
        }
    }

    //check role name exist or not
    public function check_uniqueRoleName(Request $request) {

        $id = $request->role_id;
        $role_name = $request->role_name;
        $rolecheck = Roles::where('role_name', '=', $role_name)->first();

        if (empty($rolecheck === null)) {
            echo 'false';
            die();
        } else {
            echo 'true';
            die();
        }
    }

}
