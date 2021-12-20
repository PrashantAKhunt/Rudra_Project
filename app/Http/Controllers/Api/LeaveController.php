<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use App\Leaves;
use App\LeaveMaster;
use App\LeaveCategory;
use App\Holiday;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Email_format;
use App\Mail\Mails;
use App\Employees;
use App\User;
use App\Lib\CommonTask;
use App\AttendanceDetail;
use App\Employee_expense;
use App\Driver_expense;
use DateTime;
use App\RemoteAttendanceRequest;
use App\Lib\NotificationTask;

class LeaveController extends Controller {

    private $page_limit = 20;
    public $common_task;
    private $module_id = 4;
    public $notification_task;

    public function __construct() {
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();
    }

    /*
     * * Leave Statestics
     */

    public function leave_statistics(Request $request) {

        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $whereCondition = ['user_id' => $request->input('user_id')];
        if ($request->input('leave_category_id')) {
            $whereCondition = array_merge($whereCondition, ['leave_category_id' => $request->input('leave_category_id')]);
        }
        $leave_detail = LeaveMaster::where($whereCondition)->with(['leavecategory' => function($query) {
                        $query->select('id', 'name', 'quantity');
                    }])->get(['id', 'balance', 'leave_category_id', 'created_at']);

        $past_leave_detail = Leaves::whereNOTIn('leave_status', [1])->where(['user_id' => $request->input('user_id')])->with(['leavecategory' => function($query) {
                        $query->select('id', 'name');
                    }])->orderBy('start_date', 'desc')->take(3)->get(['id as leave_id', 'subject', 'start_date', 'end_date', 'leave_category_id', 'leave_status', 'reject_reason','created_at']);

        $pending_leave_detail = Leaves::where(['leave_status' => '1'])->where(['user_id' => $request->input('user_id')])->with(['leavecategory' => function($query) {
                        $query->select('id', 'name');
                    }])->orderBy('start_date', 'asc')->take(3)->get(['id as leave_id', 'subject', 'start_date', 'end_date', 'leave_category_id', 'leave_status', 'reject_reason', 'created_at']);

        foreach ($leave_detail as $key => $leave) {
            if (isset($leave->leavecategory)) {
                $leave_detail[$key]->consumed = $leave->leavecategory->quantity - $leave->balance;
            } else {
                $leave_detail[$key]->consumed = 0;
            }
        }

        $data['leave_detail'] = $leave_detail;
        $data['pending_leave_detail'] = $pending_leave_detail;
        $data['past_leave_detail'] = $past_leave_detail;

        return response()->json(['status' => true, 'msg' => 'Leave Statistics', 'data' => $data]);
    }

    /*
     * * Apply Leave
     */

    public function apply_leave(Request $request) {

        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'subject' => 'required',
                    'description' => 'required',
                    'start_date' => 'required',
                    'end_date' => 'required',
                    //'leave_details' => 'required',
                    //'notify_id' => 'required',
                    'start_day' => 'required',
                    'end_day' => 'required',
                    'leave_category_id' => 'required',
                    'assign_work_user_id' => 'required',
                    'assign_work_details' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $leave_start_exist = Leaves::whereDate('start_date', '<=', $request->input('start_date'))->whereDate('end_date', '>=', $request->input('start_date'))
                ->whereIn('leave_status', [1, 2])
                ->where('user_id', $request->input('user_id'))
                ->get()
                ->first();
        
        
        
        $leave_end_exist = Leaves::whereDate('start_date', '<=', $request->input('end_date'))
                        ->whereDate('end_date', '>=', $request->input('end_date'))
                        ->whereIn('leave_status', [1, 2])
                        ->where('user_id', $request->input('user_id'))
                        ->get()->first();

        if (!empty($leave_start_exist) || !empty($leave_end_exist)) {
            return response()->json(['status' => false, 'msg' => "Leave is already applied on selected date.", 'data' => [], 'error' => config('errors.general_error.code')]);
        }

        $update_user_data = [
            'leave_status' => 1,
            'created_ip' => $request->ip()
        ];

        //check if leave for particular leave category have balance
        if ($request->input('leave_category_id') == 5) {
            if ($request->input('start_day') != 1 || $request->input('end_day') != 1) {
                return response()->json(['status' => false, 'msg' => "You do not apply short leave for half day.", 'data' => [], 'error' => config('errors.general_error.code')]);
            }
        } else if ($request->input('leave_category_id') != 4) {
            $leave_master_data = LeaveMaster::where('user_id', $request->input('user_id'))
                            ->where('leave_category_id', $request->input('leave_category_id'))->get()->first();            

            $totalDays = ((strtotime($request->input('end_date')) - strtotime($request->input('start_date'))) / 60 / 60 / 24) + 1;
            if ($totalDays <= 1) {
                if ($request->input('start_day') != 1) {
                    $totalDays = 0.5;
                }
            } else {
                if ($request->input('start_day') != 1) {
                    $totalDays = $totalDays - 0.5;
                }
                if ($request->input('end_day') != 1) {
                    $totalDays = $totalDays - 0.5;
                }
            }

            if ($leave_master_data->balance < $totalDays) {
                return response()->json(['status' => false, 'msg' => "You do not have enough balance in this leave category.", 'data' => [], 'error' => config('errors.general_error.code')]);
            }
        }

        if (Leaves::create(array_merge($request->all(), $update_user_data))) {

            //fetch user data who apply leave
            $user_data = \App\User::where('id', $request->input('user_id'))->get(['id', 'name']);

            $notify_ids = [$user_data[0]->reporting_user_id];
            //get users with full view access to add into notify ids
            $role_list = $this->common_task->getRoleByModulePermission($this->module_id, 5);
            if ($role_list->count() > 0) {
                $user_list_by_role = User::whereIn('role', $role_list->pluck('role_id'))->get('id')->pluck('id');
                array_push($notify_ids, $user_list_by_role);
            }

            $hr_list = User::where('role', config('constants.REAL_HR'))->get(['id'])->pluck('id')->toArray();


            $mail_data = [
                'start_date' => $request->input('start_date'),
                'start_day' => $request->input('start_day'),
                'end_date' => $request->input('end_date'),
                'end_day' => $request->input('end_day'),
                'leave_subject' => $request->input('subject'),
                'description' => $request->input('description'),
                'notify_id' => implode(',', $hr_list),
            ];
            //$this->common_task->applyLeaveEmail($mail_data);
            //send email about work assign
            //get details of user assigned for work
            $assign_user = User::where('id', $request->input('assign_work_user_id'))->get(['name', 'email']);
            $assign_leave_work_mail_data = [
                'start_date' => $request->input('start_date'),
                'start_day' => $request->input('start_day'),
                'end_date' => $request->input('end_date'),
                'end_day' => $request->input('end_day'),
                'leave_subject' => $request->input('subject'),
                'description' => $request->input('description'),
                'name' => $user_data[0]->name,
                'email' => $assign_user[0]->email,
                'assign_work_details' => $request->input('assign_work_details'),
                'assign_name' => $assign_user[0]->name
            ];
            $this->common_task->assignLeaveWorkEmail($assign_leave_work_mail_data);
            $this->notification_task->leaveRelieverNotify([$request->input('assign_work_user_id')], $user_data[0]->name);
            $this->notification_task->leaveRequestNotify($hr_list, $user_data[0]->name);
            return response()->json(['status' => true, 'msg' => 'Leave applied successfully', 'data' => []]);
        }

        return response()->json(['status' => false, 'msg' => config('errors.sql_operation.msg'), 'data' => [], 'error' => config('errors.sql_operation.code')]);
    }

    /*
     * * get Leave Details
     */

    public function get_leave_detail(Request $request) {

        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'leave_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $whereCondition = ['leaves.id' => $request->input('leave_id')];

        $leave_detail = Leaves::where($whereCondition)->with(['leavecategory' => function($query) {
                        $query->select('id', 'name');
                    }])
                ->leftJoin('users', 'users.id', '=', 'leaves.approver_id')
                ->get(['leaves.id as leave_id', 'users.name as approve_reject_user_name', 'leaves.assign_work_reject_note', 'leaves.assign_work_status', 'leaves.user_id', 'leaves.subject', 'leaves.description', 'leaves.start_date', 'leaves.start_day', 'leaves.end_date', 'leaves.end_day', 'leaves.leave_category_id', 'leaves.approver_id', 'leaves.notify_id', 'leaves.leave_status', 'leaves.reject_reason', 'leaves.created_at']);
        $days = $this->common_task->calculate_leave_days($leave_detail[0]->start_date, $leave_detail[0]->end_date, $leave_detail[0]->start_day, $leave_detail[0]->end_day);
        $leave_detail[0]->total_days = $days;
        return response()->json(['status' => true, 'msg' => 'Leave Details', 'data' => $leave_detail]);
    }

    /*
     * * get Leaves List
     */

    public function get_leaves(Request $request) {

        $validator = Validator::make($request->all(), [
                    'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $whereCondition = ['user_id' => $request->input('user_id')];

        if ($request->input('leave_category_id')) {
            $whereCondition = array_merge($whereCondition, ['leave_category_id' => $request->input('leave_category_id')]);
        }

        if ($request->input('approver_id')) {
            $whereCondition = array_merge($whereCondition, ['approver_id' => $request->input('approver_id')]);
        }

        if ($request->input('notify_id')) {
            $whereCondition = array_merge($whereCondition, ['notify_id' => $request->input('notify_id')]);
        }

        if ($request->input('leave_status')) {
            $whereCondition = array_merge($whereCondition, ['leave_status' => $request->input('leave_status')]);
        }

        $leave_detail = Leaves::where($whereCondition)->with(['leavecategory' => function($query) {
                        $query->select('id', 'name');
                    }])->get(['id as leave_id', 'subject', 'start_date', 'end_date', 'leave_category_id', 'leave_status']);

        return response()->json(['status' => true, 'msg' => 'Leave Details', 'data' => $leave_detail]);
    }

    /*
     * * get Leaves Category
     */

    public function get_leave_category(Request $request) {

        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $leave_category = LeaveMaster::where(['user_id' => $request->input('user_id')])->with(['leavecategory' => function($query) {
                        $query->select('id', 'name', 'quantity');
                    }])->get(['id', 'balance', 'leave_category_id']);
        $leaveCategory = [];
        foreach ($leave_category as $key => $value) {
            $leaveCategory[$key]['id'] = $value->leavecategory->id;
            $leaveCategory[$key]['name'] = $value->leavecategory->name;
            $leaveCategory[$key]['status'] = ($value->balance > 0) ? "Available" : "Not Available";
            $leaveCategory[$key]['balance']=$value->balance;
        }

        if (empty($leaveCategory)) {
            return response()->json(['status' => false, 'data' => [], 'msg' => config('errors.no_record.msg'), 'error' => config('errors.no_record.code')]);
        }
        return response()->json(['status' => true, 'msg' => 'Leave Details', 'data' => $leaveCategory]);
    }

    /*
     * * get Holidays
     */

    public function get_holiday(Request $request) {

        if ($request->input('year')) {
            $year = $request->input('year');
        } else {
            $year = date('Y');
        }

        $whereCondition = ['year' => $year];

        $holiday = Holiday::where($whereCondition)->selectRaw('title, start_date, end_date, DAYNAME(start_date) as dayname, is_optional')->orderBy('start_date', 'asc')->get();

        if (empty($holiday[0])) {
            return response()->json(['status' => false, 'data' => [], 'msg' => config('errors.no_record.msg'), 'error' => config('errors.no_record.code')]);
        }
        return response()->json(['status' => true, 'msg' => 'Holiday Details', 'data' => $holiday]);
    }

    /*
     * * get Upcoming Holidays
     */

    public function get_upcoming_holiday() {

        //$holiday = Holiday::whereRaw('curdate() <= start_date AND curdate()+7 >=  start_date')->selectRaw('title, start_date, end_date, DATE_FORMAT(start_date, "%D %M, %W") as formated, DATE_FORMAT(start_date, "%d") as day, is_optional')->orderBy('start_date', 'asc')->get();

        $holiday = Holiday::where('start_date', '>=', date('Y-m-d'))->where('start_date', '<=', date('Y-m-d', strtotime("+7 day")))
                ->orderBy('start_date', 'asc')
                ->get();

        if (empty($holiday[0])) {
            return response()->json(['status' => false, 'data' => [], 'msg' => config('errors.no_record.msg'), 'error' => config('errors.no_record.code')]);
        }
        return response()->json(['status' => true, 'msg' => 'Upcoming Holiday Details', 'data' => $holiday]);
    }

    public function team_leaves(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'leave_date' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];
        //get logged in user details
        $user_details = User::where('id', $request_data['user_id'])->get(['role']);

        //get permission
        $permission = $this->common_task->getPermissionArr($user_details[0]->role, $this->module_id);
        if (empty($permission)) {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }
        //fetch user list as per team as per permission
        if (in_array(5, $permission)) {
            $department_list = \App\Department::get(['id as department_id']);
        } elseif (in_array(6, $permission)) {
            $department_list = Employees::where('user_id', $request_data['user_id'])->get(['department_id']);
        } elseif (in_array(1, $permission)) {
            $department_list = Employees::where('user_id', $request_data['user_id'])->get(['department_id']);
        } else {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }

        if ($department_list->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        //get list of user as per department
        $team_member_list = Employees::whereIn('department_id', $department_list->pluck('department_id'))->get(['user_id']);

        if ($team_member_list->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        $leave_list = Leaves::join('users', 'users.id', '=', 'leaves.user_id')
                ->join('leave_category', 'leave_category.id', '=', 'leaves.leave_category_id')
                ->leftJoin('employee', 'employee.user_id', '=', 'users.id')
                ->whereIn('leaves.user_id', $team_member_list->pluck('user_id'))
                ->where('leaves.start_date', '<=', $request_data['leave_date'])
                ->where('leaves.end_date', '>=', $request_data['leave_date'])
                ->where('leaves.leave_status',2)
                ->get(['leaves.*', 'users.name', 'users.profile_image', 'employee.designation', 'leave_category.name as leave_category_name']);
        foreach ($leave_list as $key => $leave) {
            if ($leave->profile_image) {
                $leave_list[$key]->profile_image = asset('storage/' . str_replace('public/', '', $leave->profile_image));
            } else {
                $leave_list[$key]->profile_image = "";
            }
        }
        $response_data['team_leave'] = $leave_list;
        return response()->json(['status' => true, 'msg' => "Record found", 'data' => $response_data]);
    }

    public function get_leave_approval_list(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        //get user details
        $user_details = User::where('id', $request_data['user_id'])->get(['id', 'role']);

        //get permission arr
        $permission_arr = $this->common_task->getPermissionArr($user_details[0]->role, $this->module_id);
        if (empty($permission_arr)) {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }


        if (in_array(5, $permission_arr) && in_array(2, $permission_arr)) {
            //have to allow the approval for all user leaves
            if ($user_details[0]->role == config('constants.REAL_HR')) {
                $leave_list = Leaves::join('users', 'users.id', '=', 'leaves.user_id')
                        ->join('leave_category', 'leave_category.id', '=', 'leaves.leave_category_id')
                        ->leftJoin('employee', 'employee.user_id', '=', 'users.id')
                        ->where(['leave_status' => 1, 'assign_work_status' => 'Accepted'])
                        ->where('leaves.first_approval_status', 'Pending')
                        ->get(['leaves.*', 'users.name', 'users.profile_image', 'employee.designation', 'leave_category.name as leave_category_name']);
            } elseif ($user_details[0]->role == config('constants.SuperUser')) {
                //superuser approval process is removed if want to add again then just remove below return line
                //return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
                
                $leave_list = Leaves::join('users', 'users.id', '=', 'leaves.user_id')
                        ->leftJoin('employee', 'employee.user_id', '=', 'users.id')
                        ->join('leave_category', 'leave_category.id', '=', 'leaves.leave_category_id')
                        ->where(['leave_status' => 1, 'assign_work_status' => 'Accepted'])
                        ->where('leaves.first_approval_status', 'Approved')
                        ->where('leaves.second_approval_status', 'Approved')
                        ->where('leaves.third_approval_status', 'Pending')
                        ->get(['leaves.*', 'users.name', 'users.profile_image', 'employee.designation', 'leave_category.name as leave_category_name']);
            } else {
                $leave_list = Leaves::join('users', 'users.id', '=', 'leaves.user_id')
                        ->leftJoin('employee', 'employee.user_id', '=', 'users.id')
                        ->join('leave_category', 'leave_category.id', '=', 'leaves.leave_category_id')
                        ->where(['leave_status' => 1, 'assign_work_status' => 'Accepted'])
                        ->where('leaves.first_approval_status', 'Approved')
                        ->where('leaves.second_approval_status', 'Pending')
                        ->get(['leaves.*', 'users.name', 'users.profile_image', 'employee.designation', 'leave_category.name as leave_category_name']);
            }
        } elseif (in_array(6, $permission_arr) && in_array(2, $permission_arr)) {
            //have to allow the approval for the leaves in which his name is as notify id
            /* $leave_list = Leaves::join('users', 'users.id', '=', 'leaves.user_id')
              ->leftJoin('employee', 'employee.user_id', '=', 'users.id')
              ->where(['leave_status' => 1, 'assign_work_status' => 'Accepted'])
              ->whereRaw('FIND_IN_SET(' . $request_data['user_id'] . ',notify_id)')
              ->get(['leaves.*', 'users.name', 'users.profile_image', 'employee.designation']); */

            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        } else {
            //show error of no record
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        //set profile image path
        foreach ($leave_list as $key => $leave) {
            if ($leave->profile_image) {
                $leave_list[$key]->profile_image = asset('storage/' . str_replace('public/', '', $leave->profile_image));
            } else {
                $leave_list[$key]->profile_image = "";
            }
        }
        $response_data['leave_approval_list'] = $leave_list;
        return response()->json(['status' => true, 'msg' => "Record found", 'data' => $response_data]);
    }

    public function approve_leave(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'leave_id' => 'required',
                        //'approval_note' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        //get user details
        $login_user_details = User::where('id', $request_data['user_id'])->get(['id', 'role', 'name']);

        //get permission arr
        $permission_arr = $this->common_task->getPermissionArr($login_user_details[0]->role, $this->module_id);
        if (empty($permission_arr)) {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }
        $final_confirm = 0;
        if ((in_array(5, $permission_arr) && in_array(2, $permission_arr)) || in_array(6, $permission_arr) && in_array(2, $permission_arr)) {
            //allowed to approve leave

            $leaveModel = Leaves::find($request_data['leave_id']);
            $userDetail = User::where(['id' => $leaveModel->user_id])->get(['email', 'name'])->first()->toArray();
            $reliever_name = User::where('id', $leaveModel->assign_work_user_id)->value('name');
            if ($leaveModel->first_approval_status == 'Pending') {
                $update_arr['first_approval_status'] = "Approved";
                $update_arr['first_approval_id'] = $request_data['user_id'];
                $update_arr['second_approval_status'] = "Approved";
                $update_arr['second_approval_id'] = $request_data['user_id'];
                $update_arr['updated_at'] = date('Y-m-d H:i:s');
                $update_arr['updated_ip'] = $request->ip();
                $update_arr['updated_by'] = $request_data['user_id'];
                $update_arr['first_approval_datetime'] = date('Y-m-d H:i:s');
                $update_arr['second_approval_datetime'] = date('Y-m-d H:i:s');

                //get role list with full view permission for second approval
                /*$role_ids = $this->common_task->getRoleByModulePermission($this->module_id, 5);

                if ($role_ids->count()) {

                    //get users with this roles
                    $notify_users = User::whereIn('role', $role_ids->pluck('role_id')->toArray())->where('role', '!=', config('constants.SuperUser'))->where('role', '!=', config('constants.HR_ROLE'))->get(['id'])->pluck('id')->toArray();
                    $this->notification_task->leaveRequestActionNotify($notify_users, $userDetail['name']);
                }*/
                //send notification to super admin
                $notify_user_id = User::where('role', config('constants.SuperUser'))->get(['id'])->pluck('id')->toArray();

                $this->notification_task->leaveRequestActionNotify($notify_user_id, $userDetail['name']);
            }elseif ($leaveModel->first_approval_status == 'Approved' && $leaveModel->second_approval_status == 'Approved' && $leaveModel->third_approval_status == 'Pending') {
                // this code will not run as super admin is removed from approval process
                $update_arr['third_approval_status'] = "Approved";
                $update_arr['third_approval_id'] = $request_data['user_id'];
                $update_arr['leave_status'] = 2;
                $update_arr['updated_at'] = date('Y-m-d H:i:s');
                $update_arr['updated_ip'] = $request->ip();
                $update_arr['updated_by'] = $request_data['user_id'];
                $update_arr['approver_id'] = $request_data['user_id'];
                $final_confirm = 1;
            } else {
                return response()->json(['status' => false, 'msg' => config('errors.general_error.msg'), 'data' => [], 'error' => config('errors.general_error.code')]);
            }

            if (Leaves::where('id', $request_data['leave_id'])->update($update_arr)) { // 2 = Leave Approve
                if ($leaveModel->leave_category_id != 4) {
                    if ($final_confirm) {
                        $totalDays = ((strtotime($leaveModel->end_date) - strtotime($leaveModel->start_date)) / 60 / 60 / 24) + 1;
                        if ($totalDays <= 1) {
                            if ($leaveModel->start_day != 1) {
                                $totalDays = $totalDays - 0.5;
                            }
                        } else {
                            if ($leaveModel->start_day != 1) {
                                $totalDays = $totalDays - 0.5;
                            }
                            if ($leaveModel->end_day != 1) {
                                $totalDays = $totalDays - 0.5;
                            }
                        }

                        //do not consider sunday in total leaves
                        /* $start = new DateTime($leaveModel->start_date);
                          $end = new DateTime($leaveModel->end_date);
                          $days = $start->diff($end, true)->days;

                          $sundays = intval($days / 7) + ($start->format('N') + $days % 7 >= 7); */

                        //$totalDays = $totalDays - $sundays;

                        $leaveMaster = LeaveMaster::where(['user_id' => $leaveModel->user_id, 'leave_category_id' => $leaveModel->leave_category_id])->get(['id', 'balance'])->first();
                        LeaveMaster::where('id', $leaveMaster->id)->update(['balance' => $leaveMaster->balance - $totalDays]);
                    }
                }

                if ($final_confirm) {
                    //Send Approve Applied Leave Mail							
                    $emailData = Email_format::find(8)->toArray(); // 8 = Approve Applied Leave
                    $subject = $emailData['subject'];
                    $mailformat = $emailData['emailformat'];
                    $mailformat = str_replace("%user_name%", $userDetail['name'], $mailformat);
                    $mailformat = str_replace("%start_date%", $leaveModel->start_date, $mailformat);
                    $mailformat = str_replace("%start_day%", config::get('constants.LEAVE_DAY.' . $leaveModel->start_day), $mailformat);
                    $mailformat = str_replace("%end_date%", $leaveModel->end_date, $mailformat);
                    $mailformat = str_replace("%end_day%", config::get('constants.LEAVE_DAY.' . $leaveModel->end_day), $mailformat);
                    $mailformat = str_replace("%subject%", $leaveModel->subject, $mailformat);
                    $mailformat = str_replace("%description%", $leaveModel->description, $mailformat);
                    $mailformat = str_replace("%approved_by%", $login_user_details[0]->name, $mailformat);
                    $mailformat = str_replace("%reliever_name%", $reliever_name, $mailformat);
                    //$mailformat = str_replace("%approval_note%", $request_data['approval_note'], $mailformat);
                    //$emailList = DB::table('users')->whereIn('id', array_merge([$leaveModel->approver_id], explode(",", $leaveModel->notify_id)))->pluck('email')->toArray();
                    //notify all about leave is approved.
                    $emailList = DB::table('users')->where('status', 'Enabled')->pluck('email')->toArray();
                    Mail::to($userDetail['email'])->cc($emailList)->send(new Mails($subject, $mailformat));

                    $this->notification_task->leaveApprovedNotify([$leaveModel->user_id]);
                }
                return response()->json(['status' => true, 'msg' => "Leave successfully approved.", 'data' => []]);
            }
        } else {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }
    }

    public function reject_leave(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'leave_id' => 'required',
                    'reject_reason' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        //get user details
        $login_user_details = User::where('id', $request_data['user_id'])->get(['id', 'role']);

        //get permission arr
        $permission_arr = $this->common_task->getPermissionArr($login_user_details[0]->role, $this->module_id);
        if (empty($permission_arr)) {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }
        if ((in_array(5, $permission_arr) && in_array(2, $permission_arr)) || in_array(6, $permission_arr) && in_array(2, $permission_arr)) {

            $leaveModel = Leaves::find($request_data['leave_id']);

            if ($leaveModel->first_approval_status == 'Pending') {

                $leaveModelUpdate = [
                    'reject_reason' => $request_data['reject_reason'],
                    'leave_status' => 3, // Reject Leave
                    'approver_id' => $request_data['user_id'],
                    'updated_at' => date('Y-m-d h:i:s'),
                    'updated_ip' => $request->ip(),
                    'first_approval_status' => 'Rejected',
                    'first_approval_id' => $request_data['user_id'],
                    'updated_by' => $request_data['user_id']
                ];
            } else if ($leaveModel->first_approval_status == 'Approved' && $leaveModel->second_approval_status == 'Pending') {

                $leaveModelUpdate = [
                    'reject_reason' => $request_data['reject_reason'],
                    'leave_status' => 3, // Reject Leave
                    'approver_id' => $request_data['user_id'],
                    'updated_at' => date('Y-m-d h:i:s'),
                    'updated_ip' => $request->ip(),
                    'second_approval_status' => 'Rejected',
                    'second_approval_id' => $request_data['user_id'],
                    'updated_by' => $request_data['user_id']
                ];
            } elseif ($leaveModel->first_approval_status == 'Approved' && $leaveModel->second_approval_status == 'Approved' && $leaveModel->third_approval_status == 'Pending') {

                $leaveModelUpdate = [
                    'reject_reason' => $request_data['reject_reason'],
                    'leave_status' => 3, // Reject Leave
                    'approver_id' => $request_data['user_id'],
                    'updated_at' => date('Y-m-d h:i:s'),
                    'updated_ip' => $request->ip(),
                    'third_approval_status' => 'Rejected',
                    'third_approval_id' => $request_data['user_id'],
                    'updated_by' => $request_data['user_id']
                ];
            } else {
                return response()->json(['status' => false, 'msg' => config('errors.general_error.msg'), 'data' => [], 'error' => config('errors.general_error.code')]);
            }

            /* $leaveModel = [
              'reject_reason' => $request_data['reject_reason'],
              'leave_status' => 3, // Reject Leave
              'approver_id' => $request_data['user_id'],
              'updated_at' => date('Y-m-d h:i:s'),
              'updated_ip' => $request->ip(),
              ]; */

            Leaves::where('id', $request_data['leave_id'])->update($leaveModelUpdate);

            $leaveModel = Leaves::find($request_data['leave_id']);
            $userDetail = User::where(['id' => $leaveModel->user_id])->get(['email', 'name'])->first()->toArray();

            //Send Reject Applied Leave Mail
            $emailData = Email_format::find(7)->toArray(); // 7 = Reject Applied Leave
            $subject = $emailData['subject'];
            $mailformat = $emailData['emailformat'];
            $mailformat = str_replace("%user_name%", $userDetail['name'], $mailformat);
            $mailformat = str_replace("%start_date%", $leaveModel->start_date, $mailformat);
            $mailformat = str_replace("%start_day%", config::get('constants.LEAVE_DAY.' . $leaveModel->start_day), $mailformat);
            $mailformat = str_replace("%end_date%", $leaveModel->end_date, $mailformat);
            $mailformat = str_replace("%end_day%", config::get('constants.LEAVE_DAY.' . $leaveModel->end_day), $mailformat);
            $mailformat = str_replace("%subject%", $leaveModel->subject, $mailformat);
            $mailformat = str_replace("%description%", $leaveModel->description, $mailformat);
            $mailformat = str_replace("%reject_reason%", $leaveModel->reject_reason, $mailformat);


            $emailList = DB::table('users')->whereIn('id', array_merge([$leaveModel->approver_id], explode(",", $leaveModel->notify_id)))->pluck('email')->toArray();

            Mail::to($userDetail['email'])->cc($emailList)->send(new Mails($subject, $mailformat));

            $this->notification_task->leaveRejectedNotify([$leaveModel->user_id]);

            return response()->json(['status' => true, 'msg' => "Leave application rejected.", 'data' => []]);
        } else {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }
    }

    public function get_leave_assigned_work(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $leave_list = Leaves::join('users', 'users.id', '=', 'leaves.user_id')
                ->where(['assign_work_user_id' => $request_data['user_id'], 'assign_work_status' => 'Pending'])
                ->join('leave_category', 'leave_category.id', '=', 'leaves.leave_category_id')
                ->orderBy('leaves.created_at', 'DESC')
                ->get(['leaves.*', 'users.name', 'users.profile_image', 'users.email', 'leave_category.name as leave_category_name']);
        if ($leave_list->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }
        //set user profile image path
        foreach ($leave_list as $key => $leave) {
            if ($leave->profile_image) {
                $leave_list[$key]->profile_image = asset('storage/' . str_replace('public/', '', $leave->profile_image));
            } else {
                $leave_list[$key]->profile_image = "";
            }
        }

        $response_data['leave_work_request'] = $leave_list;
        return response()->json(['status' => true, 'msg' => "Record found", 'data' => $response_data]);
    }

    public function approve_reject_assigned_work(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'leave_id' => 'required',
                    //'assign_work_reject_note'=>'required',
                    'assign_work_status' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        //get data of logged in user
        $loggedin_user_data = User::where('id', $request_data['user_id'])->get(['id', 'name', 'email']);



        //check leave details
        $leave_details = Leaves::join('users', 'users.id', '=', 'leaves.user_id')
                ->where(['leaves.id' => $request_data['leave_id'], 'leaves.assign_work_user_id' => $request_data['user_id']])
                ->get(['leaves.*', 'users.email', 'users.name']);

        if ($leave_details->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }
        $userDetail = User::where(['id' => $leave_details[0]->user_id])->get(['email', 'name'])->first()->toArray();
        //accept|reject the work request with note
        $leave_arr = [
            'assign_work_reject_note' => $request_data['assign_work_reject_note'],
            'assign_work_status' => $request_data['assign_work_status'],
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => $request_data['user_id'],
        ];
        Leaves::where('id', $request_data['leave_id'])->update($leave_arr);

        //send email to notify related users
        if ($request_data['assign_work_status'] == "Accepted") {
            $mail_data = [
                'to_email' => $userDetail['email'],
                'leave_person_name' => $userDetail['name'],
                'assigned_person_name' => $loggedin_user_data[0]->name,
            ];
            $this->common_task->acceptLeaveWorkEmail($mail_data);

            $hr_list = User::where('role', config('constants.REAL_HR'))->get(['id'])->pluck('id')->toArray();

            $mail_data = [
                'start_date' => $leave_details[0]->start_date,
                'start_day' => config::get('constants.LEAVE_DAY.' . $leave_details[0]->start_day),
                'end_date' => $leave_details[0]->end_date,
                'end_day' => config::get('constants.LEAVE_DAY.' . $leave_details[0]->end_day),
                'leave_subject' => $leave_details[0]->subject,
                'description' => $leave_details[0]->description,
                'notify_id' => implode(',', $hr_list),
                'user_name' => $userDetail['name']
            ];

            $this->common_task->applyLeaveEmail($mail_data);
            $this->notification_task->leaveRequestActionNotify($hr_list, $userDetail['name']);
        } else {
            $mail_data = [
                'to_email' => $userDetail['email'],
                'leave_person_name' => $userDetail['name'],
                'assigned_person_name' => $loggedin_user_data[0]->name,
                'assign_work_reject_note' => $request_data['assign_work_reject_note']
            ];
            $this->common_task->rejectLeaveWorkEmail($mail_data);
            $this->notification_task->leaveRelieveRequestRejectedNotify($leave_details[0]->user_id, $loggedin_user_data[0]->name);
        }
        return response()->json(['status' => true, 'msg' => "Assigned work is successfully " . strtolower($request_data['assign_work_status']), 'data' => []]);
    }

    public function reassign_work_again(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'leave_id' => 'required',
                    'assign_work_user_id' => 'required',
                    'assign_work_details' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        //get data of logged in user
        $loggedin_user_data = User::where('id', $request_data['user_id'])->get(['id', 'name', 'email']);

        //leave details
        $leave_details = Leaves::join('users', 'users.id', '=', 'leaves.user_id')
                ->where(['leaves.id' => $request_data['leave_id'], 'leaves.user_id' => $request_data['user_id']])
                ->get(['leaves.*', 'users.email', 'users.name']);

        if ($leave_details->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }

        $update_arr = [
            'assign_work_user_id' => $request_data['assign_work_user_id'],
            'assign_work_details' => $request_data['assign_work_details'],
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => $request_data['user_id'],
        ];
        Leaves::where('id', $request_data['leave_id'])->update($update_arr);

        $assign_user = User::where('id', $request_data['assign_work_user_id'])->get(['name', 'email']);
        $assign_leave_work_mail_data = [
            'start_date' => $leave_details[0]->start_date,
            'start_day' => $leave_details[0]->start_day,
            'end_date' => $leave_details[0]->end_date,
            'end_day' => $leave_details[0]->end_day,
            'leave_subject' => $leave_details[0]->subject,
            'description' => $leave_details[0]->description,
            'name' => $loggedin_user_data[0]->name,
            'email' => $assign_user[0]->email,
            'assign_work_details' => $leave_details[0]->assign_work_details,
            'assign_name' => $assign_user[0]->name,
        ];
        $this->common_task->assignLeaveWorkEmail($assign_leave_work_mail_data);
        return response()->json(['status' => true, 'msg' => "Work assign request is send successfully. You will be notify about it soon.", 'data' => []]);
    }

    public function get_category_app_display(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];
        //get data of logged in user
        $loggedin_user_data = User::where('id', $request_data['user_id'])->get(['id', 'name', 'email', 'role']);
        $category_list = \App\App_display_category::where('status', 'Enabled')->get();

        foreach ($category_list as $key => $category) {
            switch ($category->id) {
                case 1:
                    //get user's permission arr
                    $permission_arr = $this->common_task->getPermissionArr($loggedin_user_data[0]->role, 20);
                    if (empty($permission_arr)) {
                        $category_list[$key]->total_count = 0;
                        continue;
                    }
                    if (in_array(1, $permission_arr) && !in_array(5, $permission_arr) && !in_array(6, $permission_arr)) {
                        $category_list[$key]->total_count = 0;
                        continue;
                    }
                    /*if($loggedin_user_data[0]->role==config('constants.REAL_HR')){
                        $category_list[$key]->total_count = 0;
                        continue;
                    }*/

                    $attendance_list_count = AttendanceDetail::join('attendance_master', 'attendance_master.id', '=', 'attendance_detail.attendance_master_id')
                                    ->join('users', 'users.id', '=', 'attendance_master.user_id')
                                    ->leftJoin('employee', 'employee.user_id', '=', 'users.id')
                                    ->where('attendance_detail.is_approved', 'Pending')
                                    ->where(function($query) use ($permission_arr, $request_data) {
                                        if (in_array(6, $permission_arr) && !in_array(5, $permission_arr)) {

                                            $jr_user_list = \App\Employees::where('reporting_user_id', $request_data['user_id'])->get(['user_id']);
                                            if ($jr_user_list->count() == 0) {
                                                return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
                                            }

                                            //fetch all pending attendance list
                                            $query->whereIn('attendance_master.user_id', $jr_user_list->pluck('user_id'));
                                        }
                                    })
                                    ->get(['users.name', 'users.profile_image', 'employee.designation'])->count();
                    
                    if($loggedin_user_data[0]->role!=config('constants.REAL_HR')){
                        $attendance_list_count=0;
                    }
                                    
                    $category_list[$key]->total_count = $attendance_list_count;
                    break;
                case 2:
                    //get permission arr
                    $permission_arr = $this->common_task->getPermissionArr($loggedin_user_data[0]->role, $this->module_id);
                    if (empty($permission_arr)) {
                        $category_list[$key]->total_count = 0;
                        continue;
                    }
                    /* if (in_array(5, $permission_arr) && in_array(2, $permission_arr)) {
                      //have to allow the approval for all user leaves
                      $leave_list_count = Leaves::join('users', 'users.id', '=', 'leaves.user_id')
                      ->leftJoin('employee', 'employee.user_id', '=', 'users.id')
                      ->where(['leave_status' => 1, 'assign_work_status' => 'Accepted'])
                      ->get(['leaves.*', 'users.name', 'users.profile_image', 'employee.designation'])->count();
                      } elseif (in_array(6, $permission_arr) && in_array(2, $permission_arr)) {
                      //have to allow the approval for the leaves in which his name is as notify id
                      $leave_list_count = Leaves::join('users', 'users.id', '=', 'leaves.user_id')
                      ->leftJoin('employee', 'employee.user_id', '=', 'users.id')
                      ->where(['leave_status' => 1, 'assign_work_status' => 'Accepted'])
                      ->whereRaw('FIND_IN_SET(' . $request_data['user_id'] . ',notify_id)')
                      ->get(['leaves.*', 'users.name', 'users.profile_image', 'employee.designation'])->count();
                      } else {
                      $leave_list_count = 0;
                      } */
                    if (in_array(5, $permission_arr) && in_array(2, $permission_arr)) {
                        //have to allow the approval for all user leaves
                        if ($loggedin_user_data[0]->role == config('constants.REAL_HR')) {
                            $leave_list_count = Leaves::join('users', 'users.id', '=', 'leaves.user_id')
                                            ->leftJoin('employee', 'employee.user_id', '=', 'users.id')
                                            ->where(['leave_status' => 1, 'assign_work_status' => 'Accepted'])
                                            ->where('leaves.first_approval_status', 'Pending')
                                            ->get(['leaves.*', 'users.name', 'users.profile_image', 'employee.designation'])->count();
                        } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
                            //$leave_list_count=0;
                           $leave_list_count = Leaves::join('users', 'users.id', '=', 'leaves.user_id')
                                            ->leftJoin('employee', 'employee.user_id', '=', 'users.id')
                                            ->where(['leave_status' => 1, 'assign_work_status' => 'Accepted'])
                                            ->where('leaves.first_approval_status', 'Approved')
                                            ->where('leaves.second_approval_status', 'Approved')
                                            ->where('leaves.third_approval_status', 'Pending')
                                            ->get(['leaves.*', 'users.name', 'users.profile_image', 'employee.designation'])->count();
                        } else {
                            $leave_list_count = Leaves::join('users', 'users.id', '=', 'leaves.user_id')
                                            ->leftJoin('employee', 'employee.user_id', '=', 'users.id')
                                            ->where(['leave_status' => 1, 'assign_work_status' => 'Accepted'])
                                            ->where('leaves.first_approval_status', 'Approved')
                                            ->where('leaves.second_approval_status', 'Pending')
                                            ->get(['leaves.*', 'users.name', 'users.profile_image', 'employee.designation'])->count();
                        }
                    } else {
                        $leave_list_count = 0;
                    }

                    $category_list[$key]->total_count = $leave_list_count;
                    break;
                case 3:
                    $leave_work_count = Leaves::join('users', 'users.id', '=', 'leaves.user_id')
                                    ->where(['assign_work_user_id' => $request_data['user_id'], 'assign_work_status' => 'Pending'])
                                    ->orderBy('leaves.created_at', 'DESC')
                                    ->get(['leaves.*', 'users.name', 'users.profile_image', 'users.email'])->count();
                    $category_list[$key]->total_count = $leave_work_count;
                    break;

                case 5:

                    $role_permission = $this->common_task->getPermissionArr($loggedin_user_data[0]->role, $this->module_id);

                    $expense_select = ['employee_expense.id', 'employee_expense.user_id', 'expense_category.category_name', 'employee_expense.expense_category as expense_category_id', 'employee_expense.title',
                        'employee_expense.bill_number', 'employee_expense.merchant_name', 'employee_expense.amount',
                        'employee_expense.expense_date', 'employee_expense.comment',
                        'employee_expense.expense_image', 'employee_expense.status', 'users.name'
                    ];


                    if ($loggedin_user_data[0]->role == config('constants.REAL_HR')) {
                        $expense_list_count = Employee_expense::join('expense_category', 'expense_category.id', '=', 'employee_expense.expense_category')
                                        ->leftJoin('users', 'users.id', '=', 'employee_expense.approved_by')
                                        ->leftJoin('employee', 'employee.user_id', '=', 'employee_expense.user_id')
                                        ->join('company', 'company.id', '=', 'employee_expense.company_id')
                                        ->join('project', 'project.id', '=', 'employee_expense.project_id')
                                        ->where('first_approval_status', 'Pending')
                                        ->get($expense_select)->count();
                    } 
                    elseif ($loggedin_user_data[0]->role == config('constants.ASSISTANT')) {
                        $expense_list_count = Employee_expense::join('expense_category', 'expense_category.id', '=', 'employee_expense.expense_category')
                                        ->leftJoin('users', 'users.id', '=', 'employee_expense.approved_by')
                                        ->leftJoin('employee', 'employee.user_id', '=', 'employee_expense.user_id')
                                        ->join('company', 'company.id', '=', 'employee_expense.company_id')
                                        ->join('project', 'project.id', '=', 'employee_expense.project_id')
                                        ->where('first_approval_status', 'Approved')
                                        ->where('second_approval_status', 'Pending')
                                        ->get($expense_select)->count();
                    }
                    elseif ($loggedin_user_data[0]->role == config('constants.Admin')) {
                        $expense_list_count = Employee_expense::join('expense_category', 'expense_category.id', '=', 'employee_expense.expense_category')
                                        ->leftJoin('users', 'users.id', '=', 'employee_expense.approved_by')
                                        ->leftJoin('employee', 'employee.user_id', '=', 'employee_expense.user_id')
                                        ->join('company', 'company.id', '=', 'employee_expense.company_id')
                                        ->join('project', 'project.id', '=', 'employee_expense.project_id')
                                        ->where('first_approval_status', 'Approved')
                                        ->where('second_approval_status', 'Approved')
                                        ->where('third_approval_status', 'Pending')
                                        ->get($expense_select)->count();
                    }
                    elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
                        $expense_list_count = Employee_expense::join('expense_category', 'expense_category.id', '=', 'employee_expense.expense_category')
                                        ->leftJoin('users', 'users.id', '=', 'employee_expense.approved_by')
                                        ->leftJoin('employee', 'employee.user_id', '=', 'employee_expense.user_id')
                                        ->join('company', 'company.id', '=', 'employee_expense.company_id')
                                        ->join('project', 'project.id', '=', 'employee_expense.project_id')
                                        ->where('first_approval_status', 'Approved')
                                        ->where('second_approval_status', 'Approved')
                                        ->where('third_approval_status', 'Approved')
                                        ->where('forth_approval_status', 'Pending')
                                        ->get($expense_select)->count();
                    } elseif ($loggedin_user_data[0]->role == config('constants.ACCOUNT_ROLE')) {
                        $expense_list_count = Employee_expense::join('expense_category', 'expense_category.id', '=', 'employee_expense.expense_category')
                                        ->leftJoin('users', 'users.id', '=', 'employee_expense.approved_by')
                                        ->leftJoin('employee', 'employee.user_id', '=', 'employee_expense.user_id')
                                        ->join('company', 'company.id', '=', 'employee_expense.company_id')
                                        ->join('project', 'project.id', '=', 'employee_expense.project_id')
                                        ->where('first_approval_status', 'Approved')
                                        ->where('second_approval_status', 'Approved')
                                        ->where('third_approval_status', 'Approved')
                                        ->where('forth_approval_status', 'Approved')
                                        ->where('fifth_approval_status', 'Pending')
                                        ->get($expense_select)->count();
                    } else {
                        $expense_list_count = 0;
                    }

                    $category_list[$key]->total_count = $expense_list_count;
                    break;

                case 6:

                    if ($loggedin_user_data[0]->role == config('constants.ACCOUNT_ROLE')) {
                        $driver_expense_count = Driver_expense::join('users', 'users.id', '=', 'driver_expense.user_id')
                                        ->where('driver_expense.first_approval_status', 'Pending')
                                        ->where('driver_expense.moniter_user_id', $request_data['user_id'])
                                        ->get(['driver_expense.*', 'users.name as driver_name'])->count();
                    } elseif ($loggedin_user_data[0]->role == config('constants.Admin')) {
                        $driver_expense_count = Driver_expense::join('users', 'users.id', '=', 'driver_expense.user_id')
                                        ->where('driver_expense.first_approval_status', 'Approved')
                                        ->where('driver_expense.second_approval_status', 'Pending')
                                        ->get(['driver_expense.*', 'users.name as driver_name'])->count();
                    } elseif ($loggedin_user_data[0]->role == config('constants.SuperUser')) {
                        $driver_expense_count = Driver_expense::join('users', 'users.id', '=', 'driver_expense.user_id')
                                        ->where('driver_expense.first_approval_status', 'Approved')
                                        ->where('driver_expense.second_approval_status', 'Approved')
                                        ->where('driver_expense.third_approval_status', 'Pending')
                                        ->get(['driver_expense.*', 'users.name as driver_name'])->count();
                    } else {
                        $driver_expense_count = 0;
                    }

                    $category_list[$key]->total_count = $driver_expense_count;
                    break;

                case 7:
                    if ($loggedin_user_data[0]->role == config('constants.REAL_HR')) {
                        $remote_attend_request_count = RemoteAttendanceRequest::with('places')
                                        ->where('main_approval_status', 'Pending')
                                        ->get(['id', 'user_id', 'place_id', 'reason', 'date', 'main_approval_status', 'first_approval_status', 'first_approval_id', 'reject_reason'])->count();
                    } else {
                        $remote_attend_request_count = 0;
                    }
                    $category_list[$key]->total_count = $remote_attend_request_count;
                    break;
                //--------
                case 8:
               
                $meeting_request_count = \App\Meeting::join('MeetingMOM', 'meeting.id','=','MeetingMOM.meeting_id')
                        ->where('MeetingMOM.meeting_user_id','=',$request_data['user_id'])
                        ->where('MeetingMOM.status','Pending')
                        ->orderBy('meeting.id', 'DESC')
                        ->get()->count();
               
                $category_list[$key]->total_count = $meeting_request_count;
                break;   

                default:
                    break;
            }
        }
        $response_data['category_list'] = $category_list;
        return response()->json(['status' => true, 'msg' => 'Record found.', 'data' => $response_data]);
    }

    public function cancel_leave(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'leave_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $leaveModel = Leaves::find($request->input('leave_id'));
        
        if($leaveModel->first_approval_status=="Approved" && $leaveModel->leave_status!=3){
            return response()->json(['status' => false, 'msg' => "You are not allowed to cancel this leave because it is in process.", 'data' => [], 'error' => config('errors.validation.code')]);
        }
        
        if (!empty($leaveModel) && $leaveModel->leave_status == 1) { // 1 = pending
            $leave = Leaves::where('id', $request->input('leave_id'))->update(['leave_status' => 4,'canceled_by_note'=>$request->input('user_id').'/'.date('Y-m-d H:i:s')]);
            if ($leave) {
                return response()->json(['status' => true, 'msg' => "Leave application cancelled.", 'data' => []]);
            }
        } else {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }
    }

    public function get_all_pending_leave(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $leaves_data = Leaves::join('leave_category', 'leave_category.id', '=', 'leaves.leave_category_id')
                ->where(['leaves.leave_status' => 1, 'leaves.user_id' => $request_data['user_id']])
                ->whereDate('leaves.end_date', '>=', date('Y-m-d'))
                ->get(['leaves.*', 'leave_category.name']);

        if ($leaves_data->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }
        $response_data['pending_leave_list'] = $leaves_data;
        return response()->json(['status' => true, 'msg' => 'Record found.', 'data' => $response_data]);
    }

}
