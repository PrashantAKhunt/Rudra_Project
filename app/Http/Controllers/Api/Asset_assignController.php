<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use App\Lib\NotificationTask;
use App\Lib\CommonTask;
use App\AssetAccess;
use App\Asset;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Lib\UserActionLogs;

class Asset_assignController extends Controller {

    private $page_limit = 20;
    public $common_task;
    public $notification_task;
    private $module_id = 15;
    private $super_admin;
    public $user_action_logs;

    public function __construct() {
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();
        $this->user_action_logs = new UserActionLogs();
        $this->super_admin = \App\User::where('role', 1)->first();
    }

    public function get_my_asset_list(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        /* $loggedin_user_data = User::leftJoin('employee', 'employee.user_id', '=', 'users.id')
          ->join('department', 'department.id', '=', 'employee.department_id')
          ->where('users.id', $request_data['user_id'])->get(['users.id', 'users.name', 'users.email', 'users.role', 'employee.company_id','employee.reporting_user_id', 'department.dept_name', 'users.user_attend_type']); */

        $asset_assign_list = AssetAccess::join('asset', 'asset.id', '=', 'asset_access.asset_id')
                ->where('asset_access.asset_access_user_id', $request_data['user_id'])
                ->where('asset_access.is_allocate', 1)->where('asset_access.status', 'Confirmed')
                ->orderBy('asset_access.id', 'DESC')
                ->get(['asset_access.*', 'asset.name as asset_name']);

        if ($asset_assign_list->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }
        $response_data['my_asset'] = $asset_assign_list;
        return response()->json(['status' => true, 'msg' => 'Record found', 'data' => $response_data]);
    }


    public function hr_asset_assign_requests(Request $request) {  //this
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $hr_assign_list = AssetAccess::join('asset', 'asset.id', '=', 'asset_access.asset_id')
        ->join('users', 'users.id', '=', 'asset_access.assigner_user_id')
        ->join('users AS B', 'B.id', '=', 'asset_access.asset_access_user_id')
        ->where('asset_access.giver_status', 'Confirmed')
                ->where('asset_access.hr_status', 'Pending')
                ->orderBy('asset_access.id', 'DESC')
                ->get(['asset_access.*', 'asset.name as asset_name','users.name as assigned_by','B.name as receiver_name']);

        if ($hr_assign_list->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }
        $response_data['hr_asset_assign_list'] = $hr_assign_list;
        return response()->json(['status' => true, 'msg' => 'Record found', 'data' => $response_data]);
    }

    public function hr_asset_assign_request_count(Request $request) {  //this
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $hr_assign_count = AssetAccess::where('asset_access.giver_status', 'Confirmed')
                ->where('asset_access.hr_status', 'Pending')
                ->get()->count();

        $response_data['hr_assign_count'] = $hr_assign_count;
        return response()->json(['status' => true, 'msg' => 'Record found', 'data' => $response_data]);
    }

    public function get_assigned_asset_list(Request $request) {  //this
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $asset_assign_list = AssetAccess::join('asset', 'asset.id', '=', 'asset_access.asset_id')
                ->join('users', 'users.id', '=', 'asset_access.assigner_user_id')
                ->where('asset_access.asset_access_user_id', $request_data['user_id'])
                ->where('asset_access.status', 'Assigned')
                ->orderBy('asset_access.id', 'DESC')
                ->get(['asset_access.*', 'asset.name as asset_name', 'users.name as assigned_by']);

        if ($asset_assign_list->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }
        $response_data['my_asset'] = $asset_assign_list;
        return response()->json(['status' => true, 'msg' => 'Record found', 'data' => $response_data]);
    }

    public function accept_asset(Request $request) {    //this
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'asset_access_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        //get asset access data
        $asset_access_data = AssetAccess::where('id', $request_data['asset_access_id'])->get();
        if ($asset_access_data->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        $user_role = $this->common_task->getUserRole($request_data['user_id']);
        $AssetAccess = AssetAccess::select('asset_access.*', 'users.name', 'users.email')
                            ->join('users', 'users.id', '=', 'asset_access.assigner_user_id')
                            ->where('asset_access.id', $request_data['asset_access_id'])->get()->toArray();

        if ($user_role == config('constants.REAL_HR')) {

            $hr_arr = [
                'hr_status' => 'Confirmed',
                'hr_datetime' => date('Y-m-d H:i:s'),
                'hr_id' => $request_data['user_id']
            ];
            AssetAccess::where('id', $request_data['asset_access_id'])->update($hr_arr);

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
                'receiver_id' => $request_data['user_id'],
                'is_allocate' => 1,
                'status' => 'Confirmed',
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip()
            ];
            if (AssetAccess::where('id', $request_data['asset_access_id'])->update($new_handler_arr)) {

                $assetData = Asset::select('name')->where('id', $AssetAccess[0]['asset_id'])->get()->toArray();
                $hr_email = User::where('role', config('constants.REAL_HR'))->get(['email']);
                $data = [
                    'asset_name' => $assetData[0]['name'],
                    'email' => [$hr_email[0]->email, $AssetAccess[0]['email']]
                ];
                $this->common_task->assignConfirmationAssetEmail($data);

                $this->notification_task->assignerConfirmationAssetNotify([$AssetAccess[0]['assigner_user_id']]);

            } else {
                return response()->json(['status' => false, 'msg' => config('errors.sql_operation.msg'), 'data' => [], 'error' => config('errors.sql_operation.code')]);
            }

        }

        // User Action Log
        $accest_acce = AssetAccess::where('id', $request_data['asset_access_id'])->first();
        $asset_name = Asset::whereId($accest_acce['asset_id'])->value('name');
        $user_name = User::whereId($accest_acce['asset_access_user_id'])->value('name');
        $add_string = "<br>Asset Name: ".$asset_name."<br>Employee Name: ".$user_name;
        $action_data = [
            'user_id' => $request_data['user_id'],
            'task_body' => "Asset assigned is accepted".$add_string,
            'created_ip' => $request->ip(),
        ];
        $this->user_action_logs->action($action_data);

        return response()->json(['status' => true, 'msg' => "Asset assigned is successfully accepted.", 'data' => []]);

    }

    public function reject_assign_asset(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'asset_access_id' => 'required',
                    'reject_reason' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $id = $request_data['asset_access_id'];
        $user_role = $this->common_task->getUserRole($request_data['user_id']);
        $AssetAccess = AssetAccess::select('asset_access.*', 'users.name', 'users.email')
                            ->join('users', 'users.id', '=', 'asset_access.assigner_user_id')
                            ->where('asset_access.id', $id)->get()->toArray();
        if ($user_role == config('constants.REAL_HR')) {

            $new_handler_reject_arr = [
                'hr_status' => 'Rejected',
                'hr_datetime' => date('Y-m-d H:i:s'),
                'hr_id' => $request_data['user_id'],
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
                'receiver_id' => $request_data['user_id'],
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
                'reason' => $request_data['reject_reason']
            ];
            $this->common_task->assignRejectAssetEmail($data);

            // User Action Log
            $accest_acce = AssetAccess::where('id', $request_data['asset_access_id'])->first();
            $asset_name = Asset::whereId($accest_acce['asset_id'])->value('name');
            $user_name = User::whereId($accest_acce['asset_access_user_id'])->value('name');
            $add_string = "<br>Asset Name: ".$asset_name."<br>Employee Name: ".$user_name."<br>Reject Note: ".$request_data['reject_reason'];
            $action_data = [
                'user_id' => $request_data['user_id'],
                'task_body' => "Asset assigned is rejected".$add_string,
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            return response()->json(['status' => true, 'msg' => "Asset assigned is rejected to accept.", 'data' => []]);
        }
    }

    public function re_assign_asset(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'asset_access_user_id' => 'required',
                    'asset_id' => 'required',
                    'asset_access_date' => 'required',
                    'asset_access_description' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $Asset = new AssetAccess();
        $Asset->asset_access_user_id = $request_data['asset_access_user_id'];
        $Asset->asset_id = $request_data['asset_id'];
        $Asset->assigner_user_id = $request_data['user_id'];
        $Asset->asset_access_date = date('Y-m-d h:i:s', strtotime($request_data['asset_access_date']));
        $Asset->asset_access_description = $request_data['asset_access_description'];
        $Asset->is_allocate = 0;
        $Asset->status = 'Assigned';
        $Asset->giver_status = 'Confirmed';
        $Asset->giver_datetime = date('Y-m-d H:i:s');
        $Asset->giver_id = $request_data['user_id'];
        $Asset->created_at = date('Y-m-d h:i:s');
        $Asset->updated_at = date('Y-m-d h:i:s');
        $Asset->updated_ip = $request->ip();
        $Asset->created_ip = $request->ip();
        $Asset->save();

        $userData = User::select('email', 'name')->where('id', $request_data['user_id'])->get()->toArray();
        $assetData = Asset::select('name')->where('id', $request_data['asset_id'])->get()->toArray();
        $data = [
            'assign_person_name' => $userData[0]['name'],
            'asset_name' => $assetData[0]['name'],
            'email' => $userData[0]['email']
        ];
        $this->common_task->assignAssetEmail($data);

        $hr_list = User::where('role', config('constants.REAL_HR'))->get(['id'])->pluck('id')->toArray();

        $this->notification_task->hrAssetRequestNotify($hr_list);

        if ($Asset->save()) {

            $asset_name = Asset::whereId($request->get('asset_id'))->value('name');
            $user_name = User::whereId($request->get('asset_access_user_id'))->value('name');
            $add_string = "<br>Asset Name: ".$asset_name."<br>Employee Name: ".$user_name."<br>Assign Date: ".$request->get('asset_access_date');
            $action_data = [
                'user_id' => $request_data['user_id'],
                'task_body' => "Organization asset assigned".$add_string,
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            return response()->json(['status' => true, 'msg' => 'Asset assign request is successfully placed.', 'data' => []]);
        } else {
            return response()->json(['status' => false, 'msg' => config('errors.sql_operation.msg'), 'data' => [], 'error' => config('errors.sql_operation.code')]);
        }
    }

    public function get_all_asset_list(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $loggedin_user_data = User::leftJoin('employee', 'employee.user_id', '=', 'users.id')
                        ->join('department', 'department.id', '=', 'employee.department_id')
                        ->where('users.id', $request_data['user_id'])->get(['users.id', 'users.name', 'users.email', 'users.role', 'employee.company_id', 'employee.reporting_user_id', 'department.dept_name', 'users.user_attend_type']);

        $permission_arr = $this->common_task->getPermissionArr($loggedin_user_data[0]->role, $this->module_id);
        if (in_array(5, $permission_arr)) {
            $asset_assign_list = AssetAccess::where('status', 'Assigned')->get(['asset_id'])->pluck('asset_id')->toArray();

            if (!empty($asset_assign_list)) {
                $Asset_List = Asset::where('asset.status', 'Enabled')->whereNotIn('id', $asset_assign_list)->get(['asset.*']);
            } else {
                $Asset_List = Asset::where('asset.status', 'Enabled')->get(['asset.*']);
            }
        } else {
            $asset_assign_list = AssetAccess::where('status', 'Assigned')->get(['asset_id'])->pluck('asset_id')->toArray();
            if (!empty($asset_assign_list)) {
                $Asset_List = Asset::join('asset_access', 'asset_access.asset_id', '=', 'asset.id')
                                ->whereNotIn('asset.id', $asset_assign_list)
                                ->where('asset.status', 'Enabled')->where('asset_access.is_allocate', 1)->where('asset_access.asset_access_user_id', $request_data['user_id'])->get(['asset.*']);
            } else {
                $Asset_List = Asset::join('asset_access', 'asset_access.asset_id', '=', 'asset.id')
                                ->where('asset.status', 'Enabled')->where('asset_access.is_allocate', 1)->where('asset_access.asset_access_user_id', $request_data['user_id'])->get(['asset.*']);
            }
        }

        if ($Asset_List->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        return response()->json(['status' => true, 'msg' => 'Record Found.', 'data' => $Asset_List]);
    }

}
