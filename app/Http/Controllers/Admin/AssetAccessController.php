<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Config;
use App\Common_query;
use App\Employees;
use App\EmployeesBankDetails;
use App\EmployeesLoans;
use App\Companies;
use App\User;
use App\TaxDeclaration;
use App\Role_module;
use App\Asset;
use Illuminate\Support\Facades\Validator;
use App\Imports\EmployeeSalaryImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use App\AssetAccess;
use DB;
// use Auth;
use App\AssetExpense;
use App\Lib\Permissions;
use App\Lib\CommonTask;
use App\Lib\NotificationTask;
use App\Lib\UserActionLogs;

class AssetAccessController extends Controller {

    public $data;
    public $common_task;
    public $notification_task;
    private $module_id;
    public $user_action_logs;

    public function __construct() {
        $this->data['module_title'] = "Asset Assign";
        $this->data['module_link'] = "admin.asset_access";
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();
        $this->user_action_logs = new UserActionLogs();
        $this->module_id = 15;
    }

    public function index() {
        $this->data['page_title'] = "Organization Asset Assign";
        $this->data['access_rule'] = '';
        $access_level = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => 15])->get()->first();
        $this->data['Asset_List'] = Asset::select('id', 'name')->get();
        $this->data['UsersName'] = User::orderBy('name')->select('id', 'name')->get();
        $check_resultF = Permissions::checkPermission(15, 5);
        if (!$check_resultF) {
            $check_resultP = Permissions::checkPermission(15, 6); // Partial View
            if (!$check_resultP) {
                $check_resultM = Permissions::checkPermission(15, 1); // Only My View
                if (!$check_resultM) {
                    return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
                }
            }
        }

        if (!empty($access_level)) {
            $this->data['access_rule'] = $access_level->access_level;
        }
        return view('admin.asset_access.index', $this->data);
    }

    public function asset_access_list() {

        $check_resultF = Permissions::checkPermission(15, 5); // Full View
        /* if (!$check_resultF) {
          $check_resultP = Permissions::checkPermission(15, 6); // Partial View
          if (!$check_resultP) {
          $check_resultM = Permissions::checkPermission(15, 1); // Only My View
          if (!$check_resultM) {
          return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
          } else {
          $conditions_array = [Auth::user()->id];
          }
          } else {
          //$conditions_array = ['asset_access.asset_access_user_id'=>Auth::user()->id];
          //$emp_user_id = Employees::select('user_id')->where(['reporting_user_id'=>7])->get()->toArray();
          $emp_user_id = Employees::select('user_id')->where(['reporting_user_id' => Auth::user()->id])->get()->toArray();
          if (!empty($emp_user_id)) {
          $arr_user = array_column($emp_user_id, 'user_id');
          $conditions_array = $arr_user;
          } else {
          $conditions_array = [Auth::user()->id];
          }
          }
          } else {

          $conditions_array = [];
          } */
        if ($check_resultF) {
            $conditions_array = [];
        } else {
            $conditions_array = [
                'asset_access.asset_access_user_id' => \Illuminate\Support\Facades\Auth::user()->id
            ];
        }

        if (Auth::user()->role == config('constants.REAL_HR')) {
            $conditions_array['asset_access.status'] = 'Confirmed';
        }

        $datatable_fields = array('users.name', 'asset.name',   'asset_access.asset_access_date', 'asset_access.asset_return_date', 'asset_access.is_allocate', 'asset_access.status');
        $request = Input::all();

        $getfiled = array('asset_access.id', 'asset_access.asset_access_user_id', 'asset.name as asset_name', 'users.name as user_name', 'asset_access.asset_access_date', 'asset_access.asset_return_date','asset_access.hr_status','asset_access.receiver_status', 'asset_access.status', 'asset_access.is_allocate', 'asset_access.asset_id','asset_access.receiver_datetime');
        $table = "asset_access";

        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'asset';
        $join_str[0]['join_table_id'] = 'asset.id';
        $join_str[0]['from_table_id'] = 'asset_access.asset_id';

        $join_str[1]['join_type'] = '';
        $join_str[1]['table'] = 'users';
        $join_str[1]['join_table_id'] = 'users.id';
        $join_str[1]['from_table_id'] = 'asset_access.asset_access_user_id';

        echo AssetAccess::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str, [], []);

        die();
    }

    public function hr_access_request() {   //this

        $this->data['page_title'] = "HR Access Request";

        if (Auth::user()->role == config('constants.REAL_HR')) {
            return view('admin.asset_access.asset_access_request', $this->data);
        }
        return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');

    }

    public function hr_access_request_list() {


        $conditions_array =  ['asset_access.giver_status' => 'Confirmed', 'asset_access.hr_status' => 'Pending' ];

        $datatable_fields = array('users.name', 'asset.name','users.name', 'asset_access.asset_access_date', 'asset_access.is_allocate', 'asset_access.status');
        $request = Input::all();

        $getfiled = array('asset_access.id', 'asset_access.asset_access_user_id', 'asset.name as asset_name', 'users.name as receiver_name','B.name AS user_name', 'asset_access.asset_access_date', 'asset_access.asset_return_date', 'asset_access.status', 'asset_access.is_allocate', 'asset_access.asset_id');
        $table = "asset_access";

        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'asset';
        $join_str[0]['join_table_id'] = 'asset.id';
        $join_str[0]['from_table_id'] = 'asset_access.asset_id';

        $join_str[1]['join_type'] = '';
        $join_str[1]['table'] = 'users';
        $join_str[1]['join_table_id'] = 'users.id';
        $join_str[1]['from_table_id'] = 'asset_access.asset_access_user_id';

        $join_str[2]['join_type'] = '';
        $join_str[2]['table'] = 'users AS B';
        $join_str[2]['join_table_id'] = 'B.id';
        $join_str[2]['from_table_id'] = 'asset_access.assigner_user_id';

        echo AssetAccess::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str, [], []);

        die();
    }

    public function get_assigned_user(Request $request) {
        $asset_detail = AssetAccess::join('users', 'users.id', '=', 'asset_access.asset_access_user_id')
                        ->where(['asset_id' => $request->input('id'), 'is_allocate' => 1])->get(['users.name']);

        if ($asset_detail->count() > 0) {
            return response()->json(['name' => $asset_detail[0]->name]);
        } else {
            return response()->json(['name' => 'NA']);
        }
    }

    public function asset_report(Request $request) {
        $check_result = Permissions::checkPermission($this->module_id, 3);
        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }
        $this->data['page_title'] = "Asset Report";
        $this->data['Asset_List'] = Asset::orderBy('name')->where('asset.status', 'Enabled')->get(['asset.*']);

        $this->data['asset_id'] = $request->input('asset_id');
        $this->data['status'] = $request->input('status');
        $this->data['start_date'] = $request->input('start_date');
        $this->data['end_date'] = $request->input('end_date');

        $condition = [];

        if(!empty($request->input('asset_id'))){
            $condition['asset_id'] = $request->input('asset_id');
        }
        if(!empty($request->input('status'))){
            $condition['status'] = $request->input('status');
        }

        if(!empty($request->input('start_date'))){
            $assets = AssetAccess::with(['asset','user'])->whereHas('asset', function($q){
                $q->where('status', 'Enabled');
            })->where($condition)->whereBetween('asset_access_date', [$request->input('start_date'), $request->input('end_date')])->orWhereBetween('asset_return_date', [$request->input('start_date'), $request->input('end_date')])->get();
        }else{
            $assets = AssetAccess::with(['asset','user'])->whereHas('asset', function($q){
                $q->where('status', 'Enabled');
            })->where($condition)->get();
        }
        $this->data['assets'] = $assets;

        return view('admin.asset_access.asset_report', $this->data);
    }

    public function get_asset_report(Request $request) {
        $this->data['page_title'] = "Asset Report";
        $this->data['access_rule'] = '';
        $access_level = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => 15])->get()->first();
        $this->data['Asset_List'] = Asset::select('id', 'name')->get();
        $this->data['UsersName'] = User::select('id', 'name')->get();
        $check_resultF = Permissions::checkPermission(15, 5);
        if (!$check_resultF) {
            $check_resultP = Permissions::checkPermission(15, 6); // Partial View
            if (!$check_resultP) {
                $check_resultM = Permissions::checkPermission(15, 1); // Only My View
                if (!$check_resultM) {
                    return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
                }
            }
        }
        if (!empty($access_level)) {
            $this->data['access_rule'] = $access_level->access_level;
        }
        return view('admin.asset_access.get_asset_report', $this->data);
    }

    public function asset_report_list(Request $request) {

        $check_resultF = Permissions::checkPermission(15, 5); // Full View

        if ($check_resultF) {
            $conditions_array = [];
        } else {
            $conditions_array = [
                'asset_access.asset_access_user_id' => \Illuminate\Support\Facades\Auth::user()->id
            ];
        }
        //$or_where=[ 'asset_access.status'=>'Confirmed' ];

        //dd($request->all());

        $datatable_fields = array('users.name', 'asset.name', 'asset_access.asset_access_date', 'asset_access.asset_return_date', 'asset_access.is_allocate', 'asset_access.status');
        $request = []; //Input::all(); //$request->all();

        $getfiled = array('asset_access.id', 'asset_access.asset_access_user_id', 'asset.name as asset_name', 'users.name as user_name', 'asset_access.asset_access_date', 'asset_access.asset_return_date', 'asset_access.status', 'asset_access.is_allocate', 'asset_access.asset_id');
        $table = "asset_access";

        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'asset';
        $join_str[0]['join_table_id'] = 'asset.id';
        $join_str[0]['from_table_id'] = 'asset_access.asset_id';

        $join_str[1]['join_type'] = '';
        $join_str[1]['table'] = 'users';
        $join_str[1]['join_table_id'] = 'users.id';
        $join_str[1]['from_table_id'] = 'asset_access.asset_access_user_id';

        echo AssetAccess::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str, [], []);

        die();
    }



    public function add_asset_access() {
        $check_result = Permissions::checkPermission($this->module_id, 3);
        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }

        $this->data['page_title'] = "Assign Asset";
        /* $assign_asset_list_id = AssetAccess::select('asset_id')->get()->toArray();
          if (!empty($assign_asset_list_id)) {
          $assign_asset_list_id_arr = array_column($assign_asset_list_id, 'asset_id');
          $this->data['Asset_List'] = Asset::select('id', 'name')->whereNotIn('id', $assign_asset_list_id_arr)->get();
          } else {
          $this->data['Asset_List'] = Asset::select('id', 'name')->get();
          } */
        $permission_arr = $this->common_task->getPermissionArr(\Illuminate\Support\Facades\Auth::user()->role, $this->module_id);
            $asset_assign_list = AssetAccess::where('status', 'Assigned')->get(['asset_id'])->pluck('asset_id')->toArray();

        if (in_array(5, $permission_arr)) {

            if (!empty($asset_assign_list)) {
                $this->data['Asset_List'] = Asset::where('asset.status', 'Enabled')->orderBy('name')->whereNotIn('id', $asset_assign_list)->get(['asset.*']);
            } else {
                $this->data['Asset_List'] = Asset::where('asset.status', 'Enabled')->get(['asset.*']);
            }
        } else {
            if (!empty($asset_assign_list)) {
                $this->data['Asset_List'] = Asset::join('asset_access', 'asset_access.asset_id', '=', 'asset.id')
                                ->whereNotIn('asset.id', $asset_assign_list)
                                ->where('asset.status', 'Enabled')->where('asset_access.is_allocate', 1)->where('asset_access.asset_access_user_id', \Illuminate\Support\Facades\Auth::user()->id)->get(['asset.*']);
            } else {
                $this->data['Asset_List'] = Asset::join('asset_access', 'asset_access.asset_id', '=', 'asset.id')->orderBy('name')
                                ->where('asset.status', 'Enabled')->where('asset_access.is_allocate', 1)->where('asset_access.asset_access_user_id', \Illuminate\Support\Facades\Auth::user()->id)->get(['asset.*']);
            }
        }
        $this->data['UsersName'] = User::orderBy('name')->select('id', 'name')->get();
        


        return view('admin.asset_access.add_asset_access', $this->data);
    }

    public function edit_asset_access($id) {
        $this->data['asset_detail'] = AssetAccess::where('id', $id)->get();
        if ($this->data['asset_detail']->count() == 0) {
            return redirect()->route('admin.asset_access')->with('error', 'Error Occurred. Try Again!');
        }
        $check_result = Permissions::checkPermission(15, 2);
        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }
        $this->data['asset_detail'] [0]->asset_access_date = date('d-m-Y', strtotime($this->data['asset_detail'][0]->asset_access_date));
        echo json_encode($this->data['asset_detail']);
    }

    public function update_asset_access(Request $request) {

        $asset_id = $request->input('id');
        $asset_arr = [
            'asset_access_user_id' => $request->input('user_id'),
            'assigner_user_id' => \Illuminate\Support\Facades\Auth::user()->id,
            //'asset_id'    => $request->input('asset_id'),
            //'asset_return_date' => date('Y-m-d h:i:s', strtotime($request->input('return_date'))),
            'asset_access_date' => date('Y-m-d h:i:s', strtotime($request->input('assign_date'))),
            'asset_access_description' => $request->input('asset_description'),
            'giver_status' => 'Confirmed',
            'giver_datetime' =>  date('Y-m-d H:i:s'),
            'giver_id' => \Illuminate\Support\Facades\Auth::user()->id,
            'created_at' => date('Y-m-d h:i:s'),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
            'is_allocate' => 0,
            'status' => 'Assigned'
        ];

        AssetAccess::where('id', $asset_id)->update($asset_arr);

        // User Action Log
        $accest_acce = AssetAccess::where('id', $asset_id)->first();
        $asset_name = Asset::whereId($accest_acce['asset_id'])->value('name');
        $user_name = User::whereId($request->get('user_id'))->value('name');
        $add_string = "<br>Asset Name: ".$asset_name."<br>Employee Name: ".$user_name."<br>Assign Date: ".$request->get('assign_date');
        $action_data = [
            'user_id' => Auth::user()->id,
            'task_body' => "Asset access updated".$add_string,
            'created_ip' => $request->ip(),
        ];
        $this->user_action_logs->action($action_data);

        return redirect()->route('admin.asset_access')->with('success', 'Asset Access details updated successfully.');
    }

    public function insert_asset_access(Request $request) {
        $validator_normal = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'asset_id' => 'required',
                    'assign_date' => 'required',
                    'asset_description' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.asset_access')->with('error', 'Please follow validation rules.');
        }
        $user_id = $request->input('user_id');

        $Asset = new AssetAccess();
        $Asset->asset_access_user_id = $user_id;
        $Asset->asset_id = $request->input('asset_id');
        $Asset->assigner_user_id = \Illuminate\Support\Facades\Auth::user()->id;
        $Asset->asset_access_date = date('Y-m-d h:i:s', strtotime($request->input('assign_date')));
        $Asset->asset_access_description = $request->input('asset_description');
        $Asset->is_allocate = 0;
        $Asset->giver_status = 'Confirmed';
        $Asset->giver_datetime = date('Y-m-d H:i:s');
        $Asset->giver_id = \Illuminate\Support\Facades\Auth::user()->id;
        $Asset->status = 'Assigned';
        $Asset->created_at = date('Y-m-d h:i:s');
        $Asset->updated_at = date('Y-m-d h:i:s');
        $Asset->updated_ip = $request->ip();
        $Asset->created_ip = $request->ip();
        $Asset->save();

        $userData = User::select('email', 'name')->where('id', $user_id)->get()->toArray();
        $assetData = Asset::select('name')->where('id', $request->input('asset_id'))->get()->toArray();
        $data = [
            'assign_person_name' => $userData[0]['name'],
            'asset_name' => $assetData[0]['name'],
            'email' => $userData[0]['email']
        ];
        $this->common_task->assignAssetEmail($data);

        $hr_list = User::where('role', config('constants.REAL_HR'))->get(['id'])->pluck('id')->toArray();

        $this->notification_task->hrAssetRequestNotify($hr_list);

        if ($Asset->save()) {

            // User Action Log
            $asset_name = Asset::whereId($request->get('asset_id'))->value('name');
            $user_name = User::whereId($request->get('user_id'))->value('name');
            $add_string = "<br>Asset Name: ".$asset_name."<br>Employee Name: ".$user_name."<br>Assign Date: ".$request->get('assign_date');
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => "Organization asset assigned".$add_string,
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            return redirect()->route('admin.asset_access')->with('success', 'Asset Access added successfully.');
        } else {
            return redirect()->route('admin.asset_access')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function delete_asset_access($id) {
        $check_result = Permissions::checkPermission(15, 4);
        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }

        if (AssetAccess::where('id', $id)->delete()) {
            return redirect()->route('admin.asset_access')->with('success', 'Delete successfully updated.');
        }
        return redirect()->route('admin.asset_access')->with('error', 'Error during operation. Try again!');
    }

    public function reject_asset_assigned(Request $request) {
        $validator_normal = Validator::make($request->all(), [
                    'reject_asset_id' => 'required',
                    'reject_reason' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.asset_access')->with('error', 'Please follow validation rules.');
        }

        $id = $request->input('reject_asset_id');
        $AssetAccess = AssetAccess::select('asset_access.*', 'users.name', 'users.email')
                            ->join('users', 'users.id', '=', 'asset_access.assigner_user_id')
                            ->where('asset_access.id', $id)->get()->toArray();
        if (Auth::user()->role == config('constants.REAL_HR')) {

            $new_handler_reject_arr = [
                'hr_status' => 'Rejected',
                'hr_datetime' => date('Y-m-d H:i:s'),
                'hr_id' => Auth::user()->id,
                'reason' => $request->input('reject_reason'),
                'is_allocate' => 0,
                'status' => 'Rejected',
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip()
            ];

            $this->notification_task->hrAssetRejectNotfy([$AssetAccess[0]['assigner_user_id']]);

        }else{

            $new_handler_reject_arr = [
                'receiver_status' => 'Rejected',
                'receiver_datetime' => date('Y-m-d H:i:s'),
                'receiver_id' => Auth::user()->id,
                'is_allocate' => 0,
                'status' => 'Rejected',
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'reason' => $request->input('reject_reason')
            ];

            $this->notification_task->userAssetRejectNotfy([$AssetAccess[0]['assigner_user_id']]);
        }

        if (AssetAccess::where('id', $id)->update($new_handler_reject_arr)) {

            $assetData = Asset::select('name')->where('id', $AssetAccess[0]['asset_id'])->get()->toArray();

            $hr_email = User::where('role', config('constants.REAL_HR'))->get(['email']);

            $data = [
                'asset_name' => $assetData[0]['name'],
                'email_list' => [$AssetAccess[0]['email'], $hr_email[0]->email],
                'asseiner_username' => $AssetAccess[0]['name'],
                'reason' => $request->input('reject_reason')
            ];
            $this->common_task->assignRejectAssetEmail($data);

            // User Action Log
            $accest_acce = AssetAccess::where('id', $id)->first();
            $asset_name = Asset::whereId($accest_acce['asset_id'])->value('name');
            $user_name = User::whereId($accest_acce['asset_access_user_id'])->value('name');
            $add_string = "<br>Asset Name: ".$asset_name."<br>Employee Name: ".$user_name."<br>Reject Note: ".$request->input('reject_reason');
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => "Asset assigned is rejected".$add_string,
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            return redirect()->route('admin.asset_access')->with('success', 'Asset acceptance successfully rejected.');
        }
        return redirect()->route('admin.asset_access')->with('error', 'Error during operation. Try again!');
    }

    public function change_asset_access($id, $status, Request $request) {

        $check_result = Permissions::checkPermission(15, 2);

        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }

        //get asset access data
        $asset_access_data = AssetAccess::where('id', $id)->get();
        if ($asset_access_data->count() == 0) {
            return redirect()->route('admin.asset_access')->with('error', 'Error Occurred. Try Again!');
        }

        $AssetAccess = AssetAccess::select('asset_access.*', 'users.name', 'users.email')
                                ->join('users', 'users.id', '=', 'asset_access.assigner_user_id')
                                ->where('asset_access.id', $id)->get()->toArray();

        if (Auth::user()->role == config('constants.REAL_HR')) {

            $hr_arr = [
                'hr_status' => 'Confirmed',
                'hr_datetime' => date('Y-m-d H:i:s'),
                'hr_id' => Auth::user()->id
            ];
            AssetAccess::where('id', $id)->update($hr_arr);

            $this->notification_task->hrAssetApprovalNotify([$AssetAccess[0]['asset_access_user_id']]);

        }else{

            //remove asset from other users account, check for currently which user have this asset
            $old_asset_handler = AssetAccess::where(['is_allocate' => 1, 'status' => 'Confirmed', 'asset_id' => $asset_access_data[0]->asset_id])->get();

            if ($old_asset_handler->count() > 0) {
                //release from this user
                $release_arr = [
                'is_allocate' => 0,
                'status' => 'Submited',
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip()
                 ];
               AssetAccess::where('id', $old_asset_handler[0]->id)->update($release_arr);
            }


            $new_handler_arr = [

                'receiver_status' => 'Confirmed',
                'receiver_datetime' => date('Y-m-d H:i:s'),
                'receiver_id' => Auth::user()->id,
                'is_allocate' => 1,
                'status' => 'Confirmed',
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip()
            ];
            if (AssetAccess::where('id', $id)->update($new_handler_arr)) {

                $assetData = Asset::select('name')->where('id', $AssetAccess[0]['asset_id'])->get()->toArray();
                $hr_email = User::where('role', config('constants.REAL_HR'))->get(['email']);
                $data = [
                    'asset_name' => $assetData[0]['name'],
                    'email' => [$hr_email[0]->email, $AssetAccess[0]['email']]
                ];
                $this->common_task->assignConfirmationAssetEmail($data);

                $this->notification_task->assignerConfirmationAssetNotify([$AssetAccess[0]['assigner_user_id']]);

            }else{
                return redirect()->route('admin.asset_access')->with('error', 'Error during operation. Try again!');
            }

        }

        // User Action Log
        $accest_acce = AssetAccess::where('id', $id)->first();
        $asset_name = Asset::whereId($accest_acce['asset_id'])->value('name');
        $user_name = User::whereId($accest_acce['asset_access_user_id'])->value('name');
        $add_string = "<br>Asset Name: ".$asset_name."<br>Employee Name: ".$user_name;
        $action_data = [
            'user_id' => Auth::user()->id,
            'task_body' => "Asset assigned is accepted".$add_string,
            'created_ip' => $request->ip(),
        ];
        $this->user_action_logs->action($action_data);

        return redirect()->route('admin.asset_access')->with('success', 'Asset successfully accepted.');

    }

    public function add_asset_expense(Request $request) {
        $validator_normal = Validator::make($request->all(), [
                    'expense_asset_id' => 'required',
                    'note' => 'required',
                    'amount' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.asset_access')->with('error', 'Please follow validation rules.');
        }

        //upload user profile image
        $asset_image = '';
        if ($request->hasFile('asset_expense_image')) {
            $profile_image = $request->file('asset_expense_image');
            $file_path = $profile_image->store('public/asset_image');
            if ($file_path) {
                $asset_image = $file_path;
            }
        }

        $AssetExpense = new AssetExpense();
        $AssetExpense->user_id = Auth::user()->id;
        $AssetExpense->asset_id = $request->input('expense_asset_id');
        $AssetExpense->amount = $request->input('amount');
        $AssetExpense->note = $request->input('note');
        $AssetExpense->image = $asset_image;
        $AssetExpense->created_at = date('Y-m-d h:i:s');
        $AssetExpense->updated_at = date('Y-m-d h:i:s');
        $AssetExpense->updated_ip = $request->ip();
        $AssetExpense->created_ip = $request->ip();
        if ($AssetExpense->save()) {
            return redirect()->route('admin.asset_access')->with('success', 'Asset Access added successfully.');
        } else {
            return redirect()->route('admin.asset_access')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function get_asset_expense_details($id) {
        $this->data['asset_detail'] = AssetExpense::where('asset_id', $id)->get();
        $check_result = Permissions::checkPermission(15, 1);
        if ($this->data['asset_detail']->count() > 0) {
            echo json_encode($this->data['asset_detail']);
        } else {
            echo json_encode(array('status' => 0));
        }
        die();
    }

}
