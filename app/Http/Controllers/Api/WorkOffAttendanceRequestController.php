<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use App\Common_query;
use App\Holiday;
use App\User;
use App\WorkOff_AttendanceRequest;
use App\Lib\NotificationTask;
use App\Lib\CommonTask;

use Illuminate\Support\Facades\Validator;

class WorkOffAttendanceRequestController extends Controller
{
    public $data;
    private $page_limit = 20;
    public $notification_task;
    public $common_task;


    public function __construct() {

        $this->notification_task = new NotificationTask();
        $this->common_task = new CommonTask();

    }

    //get User requets
    public function get_userWorkOffAttendanceRequest(Request $request)  //change
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'page_number' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $offset = ($request_data['page_number'] - 1) * $this->page_limit;

        $work_off_results = WorkOff_AttendanceRequest::join('users', 'workOff_AttendanceRequest.user_id', '=', 'users.id')
        ->leftjoin('holiday', 'holiday.id', '=', 'workOff_AttendanceRequest.holiday_id')
        ->where('workOff_AttendanceRequest.user_id', $request_data['user_id'])
        ->orderBy('id', 'DESC')
        ->get(['workOff_AttendanceRequest.*','holiday.title', 'users.name as user_name']);
        

        if ($work_off_results->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        $response_data['requests_list'] = $work_off_results;
        return response()->json(['status' => true, 'msg' => "Record Found", 'data' => $response_data]);
    
    
    }

    //get All Requests by approvals
    public function get_allWorkOffAttendanceRequest(Request $request)   //change
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'page_number' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];
        $offset = ($request_data['page_number'] - 1) * $this->page_limit;

        //get user's permission - user with my view only permission will not come in this function
        $user_role = $this->common_task->getUserRole($request_data['user_id']);

        $select_fileds = ['workOff_AttendanceRequest.*','holiday.title', 'users.name as user_name'];
        if ($user_role == config('constants.REAL_HR')) {
            $requests_list = WorkOff_AttendanceRequest::join('users', 'workOff_AttendanceRequest.user_id', '=', 'users.id')
            ->leftjoin('holiday', 'holiday.id', '=', 'workOff_AttendanceRequest.holiday_id')
                ->where('workOff_AttendanceRequest.first_approval_status', 'Pending')
                ->where('workOff_AttendanceRequest.status', 'Pending')
                ->offset($offset)->limit($this->page_limit)
                ->orderBy('id', 'DESC')
                ->get($select_fileds);
        } elseif ($user_role == config('constants.SuperUser')) {
            $requests_list = WorkOff_AttendanceRequest::join('users', 'workOff_AttendanceRequest.user_id', '=', 'users.id')
            ->leftjoin('holiday', 'holiday.id', '=', 'workOff_AttendanceRequest.holiday_id')
                ->where('workOff_AttendanceRequest.first_approval_status', 'Approved')
                ->where('workOff_AttendanceRequest.second_approval_status', 'Pending')
                ->where('workOff_AttendanceRequest.status', 'Pending')
                ->offset($offset)->limit($this->page_limit)
                ->orderBy('id', 'DESC')
                ->get($select_fileds);
        }  else {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }

        if ($requests_list->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        $response_data['requests_list'] = $requests_list;
        return response()->json(['status' => true, 'msg' => "Record Found", 'data' => $response_data]);

    }

    public function requests_count(Request $request){

        $validator = Validator::make($request->all(), [
            'user_id' => 'required'
            
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $user_role = $this->common_task->getUserRole($request_data['user_id']);

        if ($user_role == config('constants.REAL_HR')) {
            $requests_list_count = WorkOff_AttendanceRequest::where('workOff_AttendanceRequest.first_approval_status', 'Pending')
                ->where('workOff_AttendanceRequest.status', 'Pending')
                ->get()->count();
        } elseif ($user_role == config('constants.SuperUser')) {
            $requests_list_count = WorkOff_AttendanceRequest::where('workOff_AttendanceRequest.first_approval_status', 'Approved')
                ->where('workOff_AttendanceRequest.second_approval_status', 'Pending')
                ->where('workOff_AttendanceRequest.status', 'Pending')
                ->get()->count();
        }  else {
           $requests_list_count  = 0;
        }

        $response_data['requests_list_count'] = $requests_list_count;
        return response()->json(['status' => true, 'msg' => "Record Found", 'data' => $response_data]);

    }


    public function add_attendance_request(Request $request)  //change
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'date' => 'required',
            'day_type' => 'required',
            'reason_note' => 'required',
            //'day_name' => 'required',
            'description_note' => 'required',
            'holiday_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        
        
        $day_name = date('l', strtotime($request_data['date']));

        $request_arr = [

            'user_id' => $request_data['user_id'],
            'date' => date('Y-m-d', strtotime($request_data['date'])),
            'day_type' => $request_data['day_type'],
            'day_name' => $day_name,
            'reason_note' => $request_data['reason_note'],
            'description_note' => $request_data['description_note'],
            'status' => 'Pending',
            'created_at' => date('Y-m-d H:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => $request_data['user_id']
        ];

        if ($request_data['holiday_id'] > 0) {
            $request_arr['is_holiday'] = 'Yes';
            $request_arr['holiday_id'] = $request_data['holiday_id'];
        }else{
            $request_arr['is_holiday'] = 'No';
        }

        WorkOff_AttendanceRequest::insert($request_arr);
        //Send Noti. to HR
        $notify_user_id = User::where('role', config('constants.REAL_HR'))->get(['id'])->pluck('id')->toArray();
        $this->notification_task->workOffHrApprovalNotify($notify_user_id);

        return response()->json(['status' => true, 'msg' => "Attendance request successfully submitted for approval.", 'data' => []]);

    }


    public function cancel_request(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'work_off_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        //check if user has created this request?
        $workOff_user_check = WorkOff_AttendanceRequest::where(['user_id' => $request_data['user_id'], 'id' => $request_data['work_off_id']])->get();
        
        if ($workOff_user_check->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }

        if ($workOff_user_check[0]->status == "Approved") {
            return response()->json(['status' => false, 'msg' => "Request is already {$workOff_user_check[0]->status}. You can not delete this record now from system.", 'data' => [], 'error' => config('errors.general_error.code')]);
        }

        $update_arr = [
            'status' => 'Canceled',
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => $request_data['user_id']
        ];

        WorkOff_AttendanceRequest::where('id', $request_data['work_off_id'])->update($update_arr);
        
        return response()->json(['status' => true, 'msg' => "Request Canceled successfully.", 'data' => []]);
    }
    

    public function edit_attendance_request(Request $request)  //change
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'work_off_id' => 'required',
            'date' => 'required',
            'day_type' => 'required',
            //'day_name' => 'required',
            'reason_note' => 'required',
            'description_note' => 'required',
            'holiday_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $workOff_entry_check = WorkOff_AttendanceRequest::where('id', $request_data['work_off_id'])->get();
        
        
        if ($workOff_entry_check->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.general_error.msg'), 'data' => [], 'error' => config('errors.general_error.code')]);
        }

        if ($workOff_entry_check[0]->status == "Approved") {
            return response()->json(['status' => false, 'msg' => "Request is already approved. You can not update it.", 'data' => [], 'error' => config('errors.general_error.code')]);
        }

        $day_name = date('l', strtotime($request_data['date']));
        $update_arr = [

           
            'user_id' => $request_data['user_id'],
            'date' => date('Y-m-d', strtotime($request_data['date'])),
            'day_type' => $request_data['day_type'],
            'day_name' => $day_name,
            'reason_note' => $request_data['reason_note'],
            'description_note' => $request_data['description_note'],
            'first_approval_status' => 'Pending',
            'second_approval_status' => 'Pending',
            
            'status' => 'Pending',
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => $request_data['user_id']
        ];

        if ($request_data['holiday_id'] > 0) {
            $update_arr['is_holiday'] = 'Yes';
            $update_arr['holiday_id'] = $request_data['holiday_id'];
        }else{
            $update_arr['holiday_id'] = NULL;
            $update_arr['is_holiday'] = 'No';
        }


        WorkOff_AttendanceRequest::where('id', $request_data['work_off_id'])->update($update_arr);

        return response()->json(['status' => true, 'msg' => "Request updated successfuly and submitted.", 'data' => []]);

    }

    public function approve_reject_request(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'work_off_id' => 'required',
            'status' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        //check permission for approval
        $user_role = $this->common_task->getUserRole($request_data['user_id']);
      

        //get request detail
        $workOff_entry_check = WorkOff_AttendanceRequest::where('id', $request_data['work_off_id'])->get();

        if ($workOff_entry_check->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.general_error.msg'), 'data' => [], 'error' => config('errors.general_error.code')]);
        }


        if ($request_data['status'] == 'Approved') {
            if ($user_role == config('constants.REAL_HR')) {

                $updateData = ['first_approval_status' => 'Approved', 
                'first_approval_id' => $request_data['user_id'],
                'first_approval_datetime' => date('Y-m-d H:i:s')
                ];

                if (isset($request_data['approve_note'])) {
                    $updateData['first_approval_note'] = $request_data['approve_note'];
                }
            
                //superUser
                $notify_user_id = User::where('role', config('constants.SuperUser'))->get(['id'])->pluck('id')->toArray();
                $this->notification_task->workOffSuperUserApprovalNotify($notify_user_id);

                 
            } elseif ($user_role == config('constants.SuperUser')) {

                $updateData = ['second_approval_status' => 'Approved',
                 'second_approval_id' => $request_data['user_id'],
                'status' => 'Approved','second_approval_datetime' => date('Y-m-d H:i:s')];
        
                if (isset($request_data['approve_note'])) {
                    $updateData['second_approval_note'] = $request_data['approve_note'];
                }

                //request user
                $request_user_id = WorkOff_AttendanceRequest::where('id',$request_data['work_off_id'])->get(['user_id'])->pluck('user_id')->toArray();
                $this->notification_task->workOffSecondApprovalNotify($request_user_id);

                
            } else {

                return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
            }
        } else {
            if ($user_role == config('constants.REAL_HR')) {


                $updateData = [
                'first_approval_datetime' => date('Y-m-d H:i:s'),
                 'first_approval_status' => 'Rejected',
                  'first_approval_id' => $request_data['user_id'], 'status' => 'Rejected'];

                  if (isset($request_data['reject_note'])) {
                    $updateData['reject_note'] = $request_data['reject_note'];
                }
                
            } elseif ($user_role == config('constants.SuperUser')) {

                $updateData = [
                 'second_approval_status' => 'Rejected',
                  'second_approval_id' => $request_data['user_id'],
                   'status' => 'Rejected','second_approval_datetime' => date('Y-m-d H:i:s')];

                   if (isset($request_data['reject_note'])) {
                    $updateData['reject_note'] = $request_data['reject_note'];
                   }
                
            }  else {

                return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
            }
            //request user
            $request_user_id = WorkOff_AttendanceRequest::where('id',$request_data['work_off_id'])->get(['user_id'])->pluck('user_id')->toArray();
            $this->notification_task->workOffRejectNotify($request_user_id);
        }

        WorkOff_AttendanceRequest::where('id', $request_data['work_off_id'])->update($updateData);
        
        return response()->json(['status' => true, 'msg' => 'Attendance Request successfully ' . $request_data['status'], 'data' => []]);
        
    }


    public function check_holiday(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'date' => 'required'
            
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $checkDate = date('Y-m-d',strtotime($request_data['date']));
        if (date('D', strtotime($checkDate)) == 'Sun') {
            $response_data['holiday_details'] = "Sunday";
            return response()->json(['status' => true, 'msg' => "Record Found", 'data' => $response_data]);
        }
        $holiday_name = Holiday::where([['start_date', '<=', $checkDate], ['end_date', '>=', $checkDate]])->where('status','Enabled')->get();

        if ($holiday_name->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        $response_data['holiday_details'] = $holiday_name;
        return response()->json(['status' => true, 'msg' => "Record Found", 'data' => $response_data]);
    
    }
}

