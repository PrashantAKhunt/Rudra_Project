<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\AttendanceMaster;
use App\Lib\CommonTask;
use App\AttendanceDetail;
use DateTime;
use App\User;
use App\Employees;
use App\Lib\NotificationTask;
use App\RemoteAttendancePlace;
use App\RemoteAttendanceRequest;

class AttendanceController extends Controller {

    private $page_limit = 20;
    public $common_task;
    public $notification_task;
    private $total_hour_per_day = 8;
    private $module_id = 20;
    private $super_admin;

    public function __construct() {
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();

        $this->super_admin = \App\User::where('role', 1)->first();
    }

    public function get_attendance_statistics(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'attend_user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];
        //get last week avg
        $week_start_date = date("Y-m-d", strtotime("last week monday"));
        $week_end_date = date("Y-m-d", strtotime("last week sunday"));
        $loop_date = $week_start_date;
        $total_hours_logged = 0;
        $total_work_hour_week = 0;
        $late_days = 0;
        $last_week_log = [];
        $i = 0;
        while (strtotime($loop_date) < strtotime($week_end_date)) {

            $attendance_detail = AttendanceMaster::where(['user_id' => $request_data['attend_user_id'], 'date' => $loop_date])->get();

            if ($attendance_detail->count() == 0) {
                $loop_date = date('Y-m-d', strtotime("+1 day", strtotime($loop_date)));
                continue;
            }
            if ($attendance_detail[0]->total_hours) {
                $hours_logged = $this->common_task->time_to_decimal($attendance_detail[0]->total_hours);
            } else {
                $hours_logged = 0;
            }
            $total_hours_logged = $total_hours_logged + $hours_logged;

            //this will allowed total work hour per week based on leaves and holidays
            $total_work_hour_week = $total_work_hour_week + $this->total_hour_per_day;

            //check late day
            if ($attendance_detail[0]->is_late == 'YES') {
                $late_days += 1;
            }

            //get log of attendance of each day
            $last_week_log[$i]['attendance_date'] = date('Y-m-d', strtotime($attendance_detail[0]->date));
            $last_week_log[$i]['total_hours'] = $attendance_detail[0]->total_hours;
            $loop_date = date('Y-m-d', strtotime("+1 day", strtotime($loop_date)));
            $i++;
        }
        //avg attendance of last week
        $response_data['avg_hour_per_day'] = $total_hours_logged / config('constants.TOTAL_WORKDAY_WEEK');

        //ontime arrival percentage in last week
        $total_days_on_time = config('constants.TOTAL_WORKDAY_WEEK') - $late_days;
        $response_data['ontime_arrival_percent'] = sprintf('%0.2f', ($total_days_on_time * 100) / config('constants.TOTAL_WORKDAY_WEEK'));
        $response_data['last_week_log'] = $last_week_log;

        //get attendance log of this current week
        $start_date = date('Y-m-d', strtotime("last monday"));
        $end_date = date('Y-m-d');
        $loop_date = $start_date;
        $i = 0;
        $current_week_log = [];
        
        while (strtotime($loop_date) <= strtotime($end_date)) {

            $attendance_detail = AttendanceMaster::where(['user_id' => $request_data['attend_user_id'], 'date' => $loop_date])->get();

            if ($attendance_detail->count() == 0) {
                $loop_date = date('Y-m-d', strtotime("+1 day", strtotime($loop_date)));
                continue;
            }
            //get log of attendance of each day
            $current_week_log[$i]['attendance_date'] = date('Y-m-d', strtotime($attendance_detail[0]->date));
            $current_week_log[$i]['total_hours'] = $attendance_detail[0]->total_hours;
            $loop_date = date('Y-m-d', strtotime("+1 day", strtotime($loop_date)));
            $i++;
        }
        $response_data['current_week_log'] = $current_week_log;
        return response()->json(['status' => true, 'msg' => "Record found.", 'data' => $response_data]);
    }

    public function get_attendance_detail(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'attend_user_id' => 'required',
                    'attendance_date' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $attend_master_detail = AttendanceMaster::where(['user_id' => $request_data['attend_user_id'], 'date' => $request_data['attendance_date']])
                ->get();
        if ($attend_master_detail->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        $response_data['arrival_time'] = $attend_master_detail[0]->first_in;
        $response_data['leave_time'] = $attend_master_detail[0]->last_out;
        $response_data['total_hours'] = $attend_master_detail[0]->total_hours;
        if ($attend_master_detail[0]->is_late == 'YES') {
            $response_data['late_time'] = $attend_master_detail[0]->late_time;
        } else {
            $response_data['late_time'] = "00:00:00";
        }

        $attend_log_select = ['time', 'punch_type', 'device_type', 'location', 'attend_latitude', 'attend_longitude', 'is_approved'];
        $response_data['attendance_log'] = AttendanceDetail::where(['attendance_master_id' => $attend_master_detail[0]->id])->get($attend_log_select);

        return response()->json(['status' => true, 'msg' => "Record found.", 'data' => $response_data]);
    }

    public function remote_attendance_punch(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'remote_punch_reason' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];
        //get data of logged in user
        $loggedin_user_data = User::leftJoin('employee', 'employee.user_id', '=', 'users.id')
                        ->join('department', 'department.id', '=', 'employee.department_id')
                        ->where('users.id', $request_data['user_id'])->get(['users.id', 'users.name', 'users.email', 'users.role', 'employee.reporting_user_id', 'department.dept_name']);
        $punch_type = "";
        //check attendance in master table
        $attendance_master_data = AttendanceMaster::where(['user_id' => $request_data['user_id'], 'date' => date('Y-m-d')])->get();
        $attend_time = date('Y-m-d H:i:s');
        
        $lateTime = new DateTime('09:30:00');
        $moreLateTime = new DateTime('09:45:00');

        if ($attendance_master_data->count() > 0) {
            
            //check attendance log to find last attendance event
            $attendance_detail = AttendanceDetail::where(['attendance_master_id' => $attendance_master_data[0]->id])
                    ->orderBy('time', 'desc')
                    ->first();

            if (empty($attendance_detail)) {
                return response()->json(['status' => false, 'msg' => "Error during operation. Contact your site admin for more details.", 'data' => [], 'error' => config('errors.general_error.code')]);
            }
           
            if ($attendance_detail->punch_type == 'IN') {
                //make punch out attendance
                $punchin_arr = [
                    'attendance_master_id' => $attendance_master_data[0]->id,
                    'time' => $attend_time,
                    'punch_type' => "OUT",
                    'device_type' => "MOBILE",
                    'is_approved' => 'Pending',
                    'remote_punch_reason' => $request_data['remote_punch_reason'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_ip' => $request->ip(),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                    'updated_by' => $request_data['user_id']
                ];
                $punch_type = 'Out';
            } else {
                //make punch in attendance
                $punchin_arr = [
                    'attendance_master_id' => $attendance_master_data[0]->id,
                    'time' => $attend_time,
                    'punch_type' => "IN",
                    'device_type' => "MOBILE",
                    'is_approved' => 'Pending',
                    'remote_punch_reason' => $request_data['remote_punch_reason'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_ip' => $request->ip(),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                    'updated_by' => $request_data['user_id']
                ];
                $punch_type = 'In';
            }
            AttendanceDetail::insert($punchin_arr);
            if ($attendance_detail->punch_type == 'OUT') {
                $attendance_master_arr = [
                    'availability_status'=>2,
                    'last_out' => $attend_time,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                    'updated_by' => $request_data['user_id']
                ];
                AttendanceMaster::where('id', $attendance_master_data[0]->id)->update($attendance_master_arr);
            }
        } else {

            //code for new attendance
            $master_attendance_arr = [
                'user_id' => $request_data['user_id'],
                'first_in' => $attend_time,
                'date' => date('Y-m-d'),
                'is_late' => 'NO',
                'created_at' => date('Y-m-d H:i:s'),
                'created_ip' => $request->ip(),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => $request_data['user_id'],
                'availability_status' => 2
            ];

            $time = new DateTime($attend_time);
            if ($time > $lateTime && $moreLateTime >= $time) {
                $master_attendance_arr['is_late'] = 'YES';
                $duration = $time->diff($lateTime);
                $master_attendance_arr['late_time'] = $duration->format("%H:%I:%S");
            }else if($moreLateTime < $time) {
                $master_attendance_arr['is_late_more'] = 'YES';
                $duration = $time->diff($moreLateTime);
                $master_attendance_arr['late_time'] = $duration->format("%H:%I:%S");
            }

            $master_id = AttendanceMaster::insertGetId($master_attendance_arr);
            //insert record in attendance detail table as punch in
            $punchin_arr = [
                'attendance_master_id' => $master_id,
                'time' => $attend_time,
                'punch_type' => "IN",
                'device_type' => "MOBILE",
                'is_approved' => 'Pending',
                'remote_punch_reason' => $request_data['remote_punch_reason'],
                'created_at' => date('Y-m-d H:i:s'),
                'created_ip' => $request->ip(),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => $request_data['user_id']
            ];
            AttendanceDetail::insert($punchin_arr);
            $punch_type = 'In';
        }

        //create list of email and user id to notify
        $cc_email_notify = [];
        $notify_user_id = [];
        //get user's senior emp email
        $senior_emp = User::where('id', $loggedin_user_data[0]->reporting_user_id)->get(['email', 'id']);

        if ($senior_emp->count() > 0) {
            $to_email = $senior_emp[0]->email;
            array_push($notify_user_id, $senior_emp[0]->id);
        } else {
            $to_email = $this->super_admin->email;
        }

        //get list of roles with full view access
        $full_view_roles = $this->common_task->getRoleByModulePermission($this->module_id, 5);

        if ($full_view_roles->count() > 0) {
            //get list of users with full view role
            $full_view_users = User::whereIn('role', $full_view_roles->pluck('role_id'))->get(['id', 'email']);
            $cc_email_notify = $full_view_users->pluck('email')->toArray();

            $notify_user_id = array_merge($notify_user_id, $full_view_users->pluck('id')->toArray());
        }
        //send email for remote attendance punch
        $mail_data = [
            'to_email' => $to_email,
            'attend_name' => $loggedin_user_data[0]->name,
            'department' => $loggedin_user_data[0]->dept_name,
            'cc_email_arr' => $cc_email_notify,
        ];
        $this->common_task->remoteAttendanceEmail($mail_data);

        //notify users
        $this->notification_task->remoteAttendanceNotify($notify_user_id, $loggedin_user_data[0]->name);

        return response()->json(['status' => true, 'msg' => "Punched {$punch_type} successfully", 'data' => []]);
    }

    public function get_last_attendance_activity(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $attendance_check = AttendanceMaster::where(['user_id' => $request_data['user_id'], 'date' => date('Y-m-d')])->get(['id']);
        if ($attendance_check->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_attend_error.msg'), 'data' => [], 'error' => config('errors.no_attend_error.code')]);
        }

        $attendance_detial = AttendanceDetail::select(['id', 'attendance_master_id', 'time', 'punch_type', 'device_type'])->where(['attendance_master_id' => $attendance_check[0]->id])
                ->orderBy('time', 'DESC')
                ->first();

        if (empty($attendance_detial)) {
            return response()->json(['status' => false, 'msg' => config('errors.general_error.msg'), 'data' => [], 'error' => config('errors.general_error.code')]);
        }
        $response_data = ['last_attendance_log' => $attendance_detial];
        return response()->json(['status' => true, 'msg' => "Attendance log found.", 'data' => $response_data]);
    }

    public function get_approval_attendance_list(Request $request) {
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

        //get user's permission arr
        $permission_arr = $this->common_task->getPermissionArr($loggedin_user_data[0]->role, $this->module_id);
        
        if(empty($permission_arr)){
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }
        
        if (in_array(1, $permission_arr) && !in_array(5, $permission_arr) && !in_array(6, $permission_arr)) {

            //not allowed to access this api for only my view users
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }

        $attendance_list = AttendanceDetail::join('attendance_master', 'attendance_master.id', '=', 'attendance_detail.attendance_master_id')
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
                ->get(['users.name', 'users.profile_image', 'employee.designation', 'attendance_detail.*']);

        if ($attendance_list->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        //set profile image
        foreach ($attendance_list as $key => $attendance) {
            if ($attendance->profile_image) {
                $attendance_list[$key]->profile_image = asset('storage/' . str_replace('public/', '', $attendance->profile_image));
            } else {
                $attendance_list[$key]->profile_image = "";
            }
        }

        $response_data['attendance_approval_list'] = $attendance_list;
        return response()->json(['status' => true, 'msg' => "Record found.", 'data' => $response_data]);
    }

    public function approve_remote_attendance(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'attendance_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];
        //get data of logged in user
        $loggedin_user_data = User::where('id', $request_data['user_id'])->get(['id', 'name', 'email', 'role']);

        //get user's permission arr
        $permission_arr = $this->common_task->getPermissionArr($loggedin_user_data[0]->role, $this->module_id);
        if(empty($permission_arr)){
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }
        //check permission of approval
        if ((in_array(5, $permission_arr) || in_array(6, $permission_arr)) && in_array(2, $permission_arr)) {
            
            
            $this->attendance_approval_function('YES','',$request_data['attendance_id']);
            
            //get attendance_detail
            $attendance_detail = AttendanceDetail::join('attendance_master', 'attendance_master.id', '=', 'attendance_detail.attendance_master_id')
                            ->join('users', 'users.id', '=', 'attendance_master.user_id')
                            ->where('attendance_detail.id', $request_data['attendance_id'])->get(['users.name', 'users.email']);

            /*$update_arr = [
                'is_approved' => 'YES',
                'approved_by' => $request_data['user_id'],
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => $request_data['user_id']
            ];
            AttendanceDetail::where('id', $request_data['attendance_id'])->update($update_arr);*/

            //send email for approval
            $mail_data = [
                'name' => $attendance_detail[0]->name,
                'approver_name' => $loggedin_user_data[0]['name'],
                'to_email' => $attendance_detail[0]->email
            ];
            $this->common_task->remoteAttendanceApprovedEmail($mail_data);

            //send notification
            $this->notification_task->remoteAttendanceAcceptedNotify([$attendance_detail[0]->id]);

            return response()->json(['status' => true, 'msg' => "Attendance successfully approved.", 'data' => []]);
        } else {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }
    }
    
    public function attendance_approval_function($isApproved, $rejectReason, $attendanceId){
        $attendanceModel['is_approved'] = $isApproved;
        $attendanceModel['reject_reason'] = !empty($rejectReason) ? $rejectReason : '';
        
        $attendance = AttendanceDetail::where('id', $attendanceId)->update($attendanceModel);
        $attendanceDetails = AttendanceDetail::where('id', $attendanceId)->first();
        $attendanceMaster = AttendanceMaster::where('id', $attendanceDetails->attendance_master_id)->first();    

        $attendanceExist = AttendanceDetail::
                    where(function ($query) use ($attendanceMaster) {
                        $query->where('attendance_master_id', '=', $attendanceMaster->id);
                    })->where(function ($query) {
                        $query->where('is_approved', '=', 'Pending')->orWhere('is_approved', '=', 'NO');
                    })->first();
                    
        if($attendanceMaster->availability_status == 2 && $attendanceDetails->device_type == 'MOBILE'){
            if(empty($attendanceExist)){
                $masterUpdate = AttendanceMaster::where('id', $attendanceDetails->attendance_master_id)->update(['availability_status' => 1]);
            }

        }else if($attendanceMaster->availability_status == 3){

            if(empty($attendanceExist)){

                $leaves = Leaves::find($attendanceMaster->availability_id);            

                if(!empty($leaves)){
                    $diff = abs(strtotime($leaves->end_date) - strtotime($leaves->start_date));
                    $years = floor($diff / (365*60*60*24));
                    $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
                    $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

                    if($days >= 2){
                        if($leaves->start_date == $attendanceMaster->date || $leaves->end_date == $attendanceMaster->date){
                            $leaveUpdate = Leaves::where('id',$attendanceDetails->availability_id)->update(['working_on_leave' => 'working on '.$attendanceMaster->date]);
                            if($leaves->start_day == 1){ // 1 = Full day
                                $balance = 1;
                            }else{
                                $balance = 0.5;
                            }
                        }else{
                            $balance = 1;
                        }
                    }else{
                        $leaveUpdate = Leaves::where('id',$attendanceMaster->availability_id)->update(['leave_status' => 4]);
                        if($leaves->start_day == 1){
                            $balance = 1;
                        }else{
                            $balance = 0.5;
                        }
                        $leaveMaster = LeaveMaster::where('user_id', $leaves->user_id)->where('leave_category_id', $leaves->leave_category_id)->first();
                    }

                    $leaveMasterUpdate = LeaveMaster::where('id', $leaveMaster->id)->update(['balance' => $leaveMaster->balance + $balance]);

                    $masterUpdate = AttendanceMaster::where('id', $attendanceDetails->attendance_master_id)->update(['availability_status' => 1, 'availability_id' => NULL]);
                }
            }

        }else if($attendanceMaster->availability_status == 6){

            $leavesDetail = Leaves::whereIn('id', explode(",",$attendanceMaster->availability_id))->get();
            
            foreach ($leavesDetail as $key => $value) {
                if($attendanceMaster->total_hours >= strtotime(config('app.FULL_WORKING_HOURS'))){
                    $balance = 0.5;
                    $leaveUpdate = Leaves::where('id',$value->id)->update(['leave_status' => 4]);

                    $leaveMaster = LeaveMaster::where('user_id', $value->user_id)->where('leave_category_id', $value->leave_category_id)->first();

                    $leaveMasterUpdate = LeaveMaster::where('id', $leaveMaster->id)->update(['balance' => $leaveMaster->balance + $balance]);

                }else if(($attendanceMaster->total_hours  >= strtotime(config('app.HALF_WORKING_HOURS')))  && ($value->start_day == 2)){
                    $balance = 0.5;
                    $leaveUpdate = Leaves::where('id',$value->id)->update(['leave_status' => 4]);

                    $leaveMaster = LeaveMaster::where('user_id', $value->user_id)->where('leave_category_id', $value->leave_category_id)->first();

                    $leaveMasterUpdate = LeaveMaster::where('id', $leaveMaster->id)->update(['balance' => $leaveMaster->balance + $balance]);
                }
            }

        }else if($attendanceMaster->availability_status == 4 || $attendanceMaster->availability_status == 5){
            
            if(empty($attendanceExist)){
                
                if(strtotime($attendanceMaster->total_hours) >= strtotime(config('app.FULL_WORKING_HOURS'))){
                    $leaveType = 'FULL';
                    $balance = 1;
                }else if(strtotime($attendanceMaster->total_hours) >= strtotime(config('app.HALF_WORKING_HOURS'))){
                    $leaveType = 'HALF';
                    $balance = 0.5;
                }
                $compoffLeave = [$leaveType, date('Y-m-d', strtotime($attendanceMaster->date. ' + 90 days'))];
                
                $leaveDetails = LeaveMaster::where('leave_category_id',6)->where('user_id', '=', $attendanceMaster->user_id)->first();

                if(!empty($leaveDetails)){
                    $serializeLeave = unserialize($leaveDetails->expiry_date);
                    $serializeLeave[] = $compoffLeave;                    
                    $leaveUpdate = LeaveMaster::where('leave_category_id',6)->where('user_id', '=', $attendanceMaster->user_id)->update(['balance' =>$balance ,'expiry_date' => serialize($serializeLeave)]);                    

                }else{
                    $leaveAdd = new LeaveMaster();
                    $leaveAdd->user_id = $attendanceMaster->user_id;
                    $leaveAdd->leave_category_id = 6;
                    $leaveAdd->balance = $balance;
                    $leaveAdd->expiry_date = serialize($compoffLeave);
                    $leaveAdd->created_at = date('Y-m-d h:i:s');
                    $leaveAdd->created_ip = $request->ip();
                    $leaveAdd->updated_at = date('Y-m-d h:i:s');
                    $leaveAdd->updated_ip = $request->ip();
                    $leaveAdd->save();
                }
            }
        }

        return true;
    }

    public function reject_remote_attendance(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'attendance_id' => 'required',
                    'reject_reason' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];
        //get data of logged in user
        $loggedin_user_data = User::where('id', $request_data['user_id'])->get(['id', 'name', 'email', 'role']);

        //get user's permission arr
        $permission_arr = $this->common_task->getPermissionArr($loggedin_user_data[0]->role, $this->module_id);
        
        if(empty($permission_arr)){
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }
        
        //check permission of approval
        if ((in_array(5, $permission_arr) || in_array(6, $permission_arr)) && in_array(2, $permission_arr)) {

            //get attendance_detail
            $attendance_detail = AttendanceDetail::join('attendance_master', 'attendance_master.id', '=', 'attendance_detail.attendance_master_id')
                            ->join('users', 'users.id', '=', 'attendance_master.user_id')
                            ->where('attendance_detail.id', $request_data['attendance_id'])->get(['users.name', 'users.email', 'users.id']);

            $update_arr = [
                'is_approved' => 'NO',
                'reject_reason' => $request_data['reject_reason'],
                'approved_by' => $request_data['user_id'],
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => $request_data['user_id']
            ];
            AttendanceDetail::where('id', $request_data['attendance_id'])->update($update_arr);

            //send email for approval
            $mail_data = [
                'name' => $attendance_detail[0]->name,
                'approver_name' => $loggedin_user_data[0]['name'],
                'to_email' => $attendance_detail[0]->email,
                'reject_reason' => $request_data['reject_reason']
            ];
            $this->common_task->remoteAttendanceRejectedEmail($mail_data);

            //send notification about rejection
            $notify_user_ids = [$attendance_detail[0]->id];
            $this->notification_task->remoteAttendanceRejectedNotify($notify_user_ids);

            return response()->json(['status' => true, 'msg' => "Attendance successfully rejected.", 'data' => []]);
        } else {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }
    }

    public function onDutyAttendanceRequest(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'attendance_date' => 'required',
                    'start_time' => 'required',
                    'end_time' => 'required',
                    'remote_punch_reason' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $lateTime = new DateTime('09:30:00');
        $moreLateTime = new DateTime('09:45:00');

        $request_data = $request->all();
        $response_data = [];
        //get data of logged in user
        $loggedin_user_data = User::leftJoin('employee', 'employee.user_id', '=', 'users.id')
                        ->join('department', 'department.id', '=', 'employee.department_id')
                        ->where('users.id', $request_data['user_id'])->get(['users.id', 'users.name', 'users.email', 'users.role', 'employee.reporting_user_id', 'department.dept_name']);

        //check for attendance available for given date
        $attendance_master = AttendanceMaster::where(['date' => $request_data['attendance_date'], 'user_id' => $request_data['user_id']])->get();

        if ($attendance_master->count() > 0) {
            //create entry in attendance_detail table as remote attendance
            $attendance_start_detail_arr = [
                'attendance_master_id' => $attendance_master[0]->id,
                'time' => $request_data['attendance_date'].' '.$request_data['start_time'],
                'punch_type' => 'IN',
                'device_type' => 'MOBILE',
                'is_on_duty_attend_request' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'created_ip' => $request->ip(),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => $request_data['user_id'],
                'remote_punch_reason' => $request_data['remote_punch_reason']
            ];

            $attendance_end_detail_arr = [
                'attendance_master_id' => $attendance_master[0]->id,
                'time' => $request_data['attendance_date'].' '.$request_data['end_time'],
                'punch_type' => 'OUT',
                'device_type' => 'MOBILE',
                'is_on_duty_attend_request' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'created_ip' => $request->ip(),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => $request_data['user_id'],
                'remote_punch_reason' => $request_data['remote_punch_reason']
            ];
            AttendanceDetail::insert($attendance_start_detail_arr);
            AttendanceDetail::insert($attendance_end_detail_arr);
        } else {
            //insert first in master table
            $attendance_master_arr = [
                'user_id' => $request_data['user_id'],
                'first_in' => $request_data['attendance_date'].' '.$request_data['start_time'],
                'last_out' => $request_data['attendance_date'].' '.$request_data['end_time'],
                'date' => $request_data['attendance_date'],
                'availability_status' => 1,
                'is_late' => 'NO',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'created_ip' => $request->ip(),
                'updated_ip' => $request->ip(),
                'updated_by' => $request_data['user_id'],
            ];

            $time = new DateTime($request_data['start_time']);
            if ($time > $lateTime && $moreLateTime >= $time) {
                $master_attendance_arr['is_late'] = 'YES';
                $duration = $time->diff($lateTime);
                $master_attendance_arr['late_time'] = $duration->format("%H:%I:%S");
            }else if($moreLateTime < $time) {
                $master_attendance_arr['is_late_more'] = 'YES';
                $duration = $time->diff($moreLateTime);
                $master_attendance_arr['late_time'] = $duration->format("%H:%I:%S");
            }
           
            $master_id = AttendanceMaster::insertGetId($attendance_master_arr);

            //create entry in attendance_detail table as remote attendance
            $attendance_start_detail_arr = [
                'attendance_master_id' => $master_id,
                'time' => $request_data['attendance_date'].' '.$request_data['start_time'],
                'punch_type' => 'IN',
                'device_type' => 'MOBILE',
                'is_on_duty_attend_request' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'created_ip' => $request->ip(),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => $request_data['user_id'],
                'remote_punch_reason' => $request_data['remote_punch_reason']
            ];

            $attendance_end_detail_arr = [
                'attendance_master_id' => $master_id,
                'time' => $request_data['attendance_date'].' '.$request_data['end_time'],
                'punch_type' => 'OUT',
                'device_type' => 'MOBILE',
                'is_on_duty_attend_request' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'created_ip' => $request->ip(),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => $request_data['user_id'],
                'remote_punch_reason' => $request_data['remote_punch_reason']
            ];

            AttendanceDetail::insert($attendance_start_detail_arr);
            AttendanceDetail::insert($attendance_end_detail_arr);
        }
        
        //create list of email and user id to notify
        $cc_email_notify = [];
        $notify_user_id = [];
        //get user's senior emp email
        $senior_emp = User::where('id', $loggedin_user_data[0]->reporting_user_id)->get(['email', 'id']);

        if ($senior_emp->count() > 0) {
            $to_email = $senior_emp[0]->email;
            array_push($notify_user_id, $senior_emp[0]->id);
        } else {
            $to_email = $this->super_admin->email;
        }

        //get list of roles with full view access
        $full_view_roles = $this->common_task->getRoleByModulePermission($this->module_id, 5);

        if ($full_view_roles->count() > 0) {
            //get list of users with full view role
            $full_view_users = User::whereIn('role', $full_view_roles->pluck('role_id'))->get(['id', 'email']);
            $cc_email_notify = $full_view_users->pluck('email')->toArray();

            $notify_user_id = array_merge($notify_user_id, $full_view_users->pluck('id')->toArray());
        }
        //send email for remote attendance punch
        $mail_data = [
            'attend_request_username' => $loggedin_user_data[0]->name,
            'date' =>date('d-m-Y',strtotime($request_data['attendance_date'])),
            'start_time' => $request_data['start_time'],
            'end_time' => $request_data['end_time'],
            'remote_punch_reason'=>$request_data['remote_punch_reason'],
            'cc_email_arr'=>$cc_email_notify,
            'to_email'=>$to_email
        ];
        $this->common_task->onDutyAttendanceEmail($mail_data);

        //notify users
        $this->notification_task->onDutyAttendanceNotify($notify_user_id, $loggedin_user_data[0]->name);
        
        return response()->json(['status' => true, 'msg' => "Attendance request for on duty is sent. You will be updated soon.", 'data' => []]);
    }

    public function get_place_list(Request $request) {

        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $responce_data = [];
        if(!empty($request->get('place'))){
            $responce_data['places'] = RemoteAttendancePlace::where('place', 'like', '%' . $request->get('place') . '%')->get(['id','place'])->toArray();            
        }else{
            $responce_data['places'] = RemoteAttendancePlace::get(['id','place'])->toArray();
        }
        
        return response()->json(['status' => true, 'msg' => 'Record found', 'data' => $responce_data]);
    }

    public function attendance_request(Request $request) {

        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'place' => 'required',
                    'reason' => 'required',
                    'date' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        
        $places = $request->input('place');
        $reason = $request->input('reason');
        $detail = '';
        foreach ($places as $key => $value) {
            $placeModel = RemoteAttendancePlace::where('id',$value)->get()->first();            
            if(empty($placeModel)){
                $place_data = [
                    'place' => $value,
                    'created_ip' => $request->ip(),
                ];                
                RemoteAttendancePlace::create($place_data);
                $place = RemoteAttendancePlace::where('place',$value)->get('id')->first();
                $placeName = $value;
                $value = $place->id;                
            }else{
                $placeName = $placeModel->place;
            }

            $request_data = [
                'user_id' => $request->input('user_id'),
                'place_id' => $value,
                'reason' => $reason[$key],
                'date' => $request->input('date'),
                'created_ip' => $request->ip(),
            ];            
            $requestModel = RemoteAttendanceRequest::create($request_data);

            $detail .= '<p>Place :'.$placeName.'</p>';
            $detail .= '<p>Reason :'.$reason[$key].'</p>';
        }
        
        if ($requestModel) {

            $userData = user::where('id', $request->input('user_id'))->get()->first();

            $hrEmail = user::where('status', 'Enabled')->where('role', config('constants.REAL_HR'))->pluck('email')->toArray();
            $hrIds = user::where('status', 'Enabled')->where('role', config('constants.REAL_HR'))->pluck('id')->toArray();
            
            $mail_data = [
                'to_email' => $hrEmail,
                'user_name' => $userData->name,
                'date' => $request->input('date'),
                'detail' => $detail,
            ];

            $this->common_task->applyAttendaceRequestEmail($mail_data);

            $this->notification_task->remoteAttendanceRequestNotify($hrIds, $userData->name);
            
            return response()->json(['status' => true, 'msg' => 'Request added successfully', 'data' => []]);
        }
        return response()->json(['status' => false, 'msg' => config('errors.sql_operation.msg'), 'data' => [], 'error' => config('errors.sql_operation.code')]);
    }


    public function get_request_list(Request $request) {

        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $responce_data = [];
        
        $responce_data['request'] = RemoteAttendanceRequest::with('places')->get(['id','user_id','place_id','reason','date','main_approval_status','first_approval_status','first_approval_id','reject_reason'])->toArray();
        
        return response()->json(['status' => true, 'msg' => 'Record found', 'data' => $responce_data]);
    }

    public function get_request_approval(Request $request) {

        $validator = Validator::make($request->all(), [
                    'id' => 'required',    
                    'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $requestModel = RemoteAttendanceRequest::where('id', $request->input('id'))->update(['first_approval_status' => 'Approved', 'main_approval_status' => 'Approved', 'first_approval_id' => $request->input('user_id'), 'updated_ip' => $request->ip(), 'updated_by' => $request->input('user_id')]);

        if ($requestModel) {
            
            $requestData = RemoteAttendanceRequest::where('id', $request->input('id'))->with('places')->get()->first();
            $userData = user::where('id', $requestData->user_id)->get()->first();
            $userEmail = user::where('id', $requestData->user_id)->pluck('email')->toArray();            
            $mail_data = [
                'to_email' => $userEmail,
                'user_name' => $userData->name,
                'date' => $requestData->date,
                'place' => $requestData->places->place,
                'reason' => $requestData->reason,
            ];

            $this->common_task->approveAttendaceRequestEmail($mail_data);
            $this->notification_task->approveRemoteAttendanceNotify([$requestData->user_id]);

            return response()->json(['status' => true, 'msg' => 'Request approved successfully', 'data' => []]);
        }
        return response()->json(['status' => false, 'msg' => config('errors.sql_operation.msg'), 'data' => [], 'error' => config('errors.sql_operation.code')]);
    }

    public function get_request_reject(Request $request) {

        $validator = Validator::make($request->all(), [
                    'id' => 'required',    
                    'user_id' => 'required',
                    'reject_reason' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $requestModel = RemoteAttendanceRequest::where('id', $request->input('id'))->update(['first_approval_status' => 'Rejected', 'main_approval_status' => 'Rejected', 'first_approval_id' => $request->input('user_id'), 'reject_reason' => $request->input('reject_reason'),'updated_ip' => $request->ip(), 'updated_by' => $request->input('user_id')]);  

        if ($requestModel) {

            $requestData = RemoteAttendanceRequest::where('id', $request->input('id'))->with('places')->get()->first();
            $userData = user::where('id', $requestData->user_id)->get()->first();
            $userEmail = user::where('id', $requestData->user_id)->pluck('email')->toArray();
            
            $mail_data = [
                'to_email' => $userEmail,
                'user_name' => $userData->name,
                'date' => $requestData->date,
                'place' => $requestData->places->place,
                'reason' => $requestData->reason,
                'reject_reason' => $requestData->reject_reason,
            ];

            $this->common_task->rejectAttendaceRequestEmail($mail_data);
            $this->notification_task->rejectRemoteAttendanceNotify([$requestData->user_id]);

            return response()->json(['status' => true, 'msg' => 'Request rejected successfully', 'data' => []]);
        }

        return response()->json(['status' => false, 'msg' => config('errors.sql_operation.msg'), 'data' => [], 'error' => config('errors.sql_operation.code')]);
    }

    public function get_request_cancel(Request $request) {

        $validator = Validator::make($request->all(), [
                    'id' => 'required',    
                    'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $requestData = RemoteAttendanceRequest::where('id', $request->input('id'))->with('places')->get()->first();

        if(!empty($requestData) && $requestData->main_approval_status == 'Pending'){
            
            $requestModel = RemoteAttendanceRequest::where('id', $request->input('id'))->update(['main_approval_status' => 'Cancel', 'updated_ip' => $request->ip(), 'updated_by' => $request->input('user_id')]);  

            if ($requestModel) {            
                $userData = user::where('id', $request->input('user_id'))->get()->first();
                $hrEmail = user::where('status', 'Enabled')->where('role', config('constants.REAL_HR'))->pluck('email')->toArray();
                $hrIds = user::where('status', 'Enabled')->where('role', config('constants.REAL_HR'))->pluck('id')->toArray();
                $mail_data = [
                    'to_email' => $hrEmail,
                    'user_name' => $userData->name,
                    'date' => $requestData->date,
                    'place' => $requestData->places->place,
                    'reason' => $requestData->reason,
                ];

                $this->common_task->cancelAttendaceRequestEmail($mail_data);

                $this->notification_task->cancelRemoteAttendanceNotify($hrIds, $userData->name);

                return response()->json(['status' => true, 'msg' => 'Request cancel successfully', 'data' => []]);
            }
        }

        return response()->json(['status' => false, 'msg' => config('errors.sql_operation.msg'), 'data' => [], 'error' => config('errors.sql_operation.code')]);
    }
}
