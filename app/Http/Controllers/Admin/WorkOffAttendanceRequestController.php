<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use App\Common_query;
use App\Holiday;
use App\User;
use App\WorkOff_AttendanceRequest;
use App\Lib\NotificationTask;

use Illuminate\Support\Facades\Validator;

class WorkOffAttendanceRequestController extends Controller
{
    public $data;
    public $notification_task;

    public function __construct() {
        $this->data['module_title'] = "Holiday Working Request";
        $this->data['module_link'] = "admin.holiday_work_attendance";

        $this->notification_task = new NotificationTask();
    }

    public function index() {
        $this->data['page_title'] = "Holiday Working Request";
        return view('admin.workOff_attendance.index', $this->data);
    }


    public function get_workOff_attendance_request_list() {   //this

        $datatable_fields = array('users.name','workOff_AttendanceRequest.date','workOff_AttendanceRequest.day_type','workOff_AttendanceRequest.holiday_id',
        'holiday.title','workOff_AttendanceRequest.day_name','workOff_AttendanceRequest.reason_note','workOff_AttendanceRequest.description_note',
        'workOff_AttendanceRequest.first_approval_status','workOff_AttendanceRequest.first_approval_datetime',
        'workOff_AttendanceRequest.second_approval_status','workOff_AttendanceRequest.second_approval_datetime', 'workOff_AttendanceRequest.status');

        $request = Input::all();
        $conditions_array = ['user_id' => Auth::user()->id];

        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'users';
        $join_str[0]['join_table_id'] = 'users.id';
        $join_str[0]['from_table_id'] = 'workOff_AttendanceRequest.user_id';

        $join_str[1]['join_type'] = 'left';
        $join_str[1]['table'] = 'holiday';
        $join_str[1]['join_table_id'] = 'holiday.id';
        $join_str[1]['from_table_id'] = 'workOff_AttendanceRequest.holiday_id';


        $getfiled = array('workOff_AttendanceRequest.*', 'users.name as user_name','holiday.title');
        $table = "workOff_AttendanceRequest";

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);

        die();
    }


    public function add_attendance_request()
    {
        $this->data['page_title'] = 'Add Holiday Working Request';
        $this->data['module_title'] = 'Holiday Working Request';

        $this->data['holidays'] = Holiday::where('status', 'Enabled')->get(['title', 'id']);

        return view('admin.workOff_attendance.add_attendance_request', $this->data);
    }

    public function insert_attendance_request(Request $request)
    {


        $validator_normal = Validator::make($request->all(), [

            'date' => 'required',
            'day_type' => 'required',
            'reason_note' => 'required',
            'description_note' => 'required'

        ]);


        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_attendance_request')->with('error', 'Please follow validation rules.');
        }

        $request_arr = [


            'user_id' => Auth::user()->id,
            'date' => date('Y-m-d', strtotime($request->input('date'))),
            'day_type' => $request->input('day_type'),
            'day_name' => $request->input('day_name'),
            'reason_note' => $request->input('reason_note'),
            'description_note' => $request->input('description_note'),
            'status' => 'Pending',
            'created_at' => date('Y-m-d H:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id
        ];

        if ($request->input('holiday_id') > 0) {
            $request_arr['is_holiday'] = 'Yes';
            $request_arr['holiday_id'] = $request->input('holiday_id');
        }else{
            $request_arr['is_holiday'] = 'No';
        }


        WorkOff_AttendanceRequest::insert($request_arr);
        //Send Noti. to HR
        $notify_user_id = User::where('role', config('constants.REAL_HR'))->get(['id'])->pluck('id')->toArray();
        $this->notification_task->workOffHrApprovalNotify($notify_user_id);

        return redirect()->route('admin.holiday_work_attendance')->with('success', 'Attendance Request successfully Added.');
    }

    public function edit_attendance_request($id)
    {
        $this->data['page_title'] = "Edit Holiday Working Request";
        $this->data['module_title'] = 'Holiday Working Request';

        $this->data['request_details'] = WorkOff_AttendanceRequest::where('id', $id)->get();

        $this->data['holidays'] = Holiday::where('status', 'Enabled')->get(['title', 'id']);

        return view('admin.workOff_attendance.edit_attendance_request', $this->data);
    }

    public function update_attendance_request(Request $request)
    {


        $validator_normal = Validator::make($request->all(), [

            'date' => 'required',
            'day_type' => 'required',
            'reason_note' => 'required',
            'description_note' => 'required'

        ]);


        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_attendance_request')->with('error', 'Please follow validation rules.');
        }

        $work_off_id = $request->input('id');

        $update_arr = [


            'user_id' => Auth::user()->id,
            'date' => date('Y-m-d', strtotime($request->input('date'))),
            'day_type' => $request->input('day_type'),
            'day_name' => $request->input('day_name'),
            'reason_note' => $request->input('reason_note'),
            'description_note' => $request->input('description_note'),
            'first_approval_status' => 'Pending',
            'second_approval_status' => 'Pending',

            'status' => 'Pending',
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id
        ];

        if ($request->input('holiday_id') > 0) {
            $update_arr['is_holiday'] = 'Yes';
            $update_arr['holiday_id'] = $request->input('holiday_id');
        }else{
            $update_arr['holiday_id'] = NULL;
            $update_arr['is_holiday'] = 'No';
        }


        WorkOff_AttendanceRequest::where('id', $work_off_id)->update($update_arr);

        return redirect()->route('admin.holiday_work_attendance')->with('success', 'Attendance Request successfully Updated !.');
    }

    public function get_workOff_attendance_request_all_list_ajax() {

        $datatable_fields = array('users.name','workOff_AttendanceRequest.date','workOff_AttendanceRequest.day_type','workOff_AttendanceRequest.is_holiday',
        'holiday.title','workOff_AttendanceRequest.day_name','workOff_AttendanceRequest.reason_note','workOff_AttendanceRequest.description_note',
        'workOff_AttendanceRequest.first_approval_status','workOff_AttendanceRequest.first_approval_datetime',
        'workOff_AttendanceRequest.second_approval_status','workOff_AttendanceRequest.second_approval_datetime', 'workOff_AttendanceRequest.status');

        $request = Input::all();

        if (Auth::user()->role == config('constants.REAL_HR')) {

            $conditions_array = ['workOff_AttendanceRequest.first_approval_status' => 'Pending', ['workOff_AttendanceRequest.status','!=','Canceled']];

        } elseif (Auth::user()->role == config('constants.SuperUser')) {

            $conditions_array = ['workOff_AttendanceRequest.first_approval_status' => 'Approved', 'workOff_AttendanceRequest.second_approval_status' => 'Pending',
            'workOff_AttendanceRequest.status' => 'Pending',['workOff_AttendanceRequest.status','!=','Canceled']];

        }

        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'users';
        $join_str[0]['join_table_id'] = 'users.id';
        $join_str[0]['from_table_id'] = 'workOff_AttendanceRequest.user_id';

        $join_str[1]['join_type'] = 'left';
        $join_str[1]['table'] = 'holiday';
        $join_str[1]['join_table_id'] = 'holiday.id';
        $join_str[1]['from_table_id'] = 'workOff_AttendanceRequest.holiday_id';


        $getfiled = array('workOff_AttendanceRequest.*', 'users.name as user_name','holiday.title');
        $table = "workOff_AttendanceRequest";

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);

        die();
    }

    public function work_off_all_attendance_history(Request $request) {

        $this->data['page_title'] = "Holiday Working Request";

       if (Auth::user()->role == config('constants.REAL_HR')  || Auth::user()->role == config('constants.SuperUser')) {

        $Results = WorkOff_AttendanceRequest::join('users', 'workOff_AttendanceRequest.user_id', '=', 'users.id')
                        ->leftjoin('holiday', 'holiday.id', '=', 'workOff_AttendanceRequest.holiday_id');

        $this->data['attendance_history'] = $Results->get(['workOff_AttendanceRequest.*','holiday.title', 'users.name as user_name']);

        return view('admin.workOff_attendance.attendance_request_list', $this->data);

      }
        return redirect()->route('admin.dashboard')->with('error', 'Access Denied');
    }

    public function cancel_request($id, Request $request)
    {

        $update_arr = [
            'status' => 'Canceled',
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id
        ];

        WorkOff_AttendanceRequest::where('id', $id)->update($update_arr);

        return redirect()->route('admin.holiday_work_attendance')->with('success', 'Attendance Request successfully canceled.');
    }


    // -----------------------------------------------Approve-Reject---------------------------------------------//

    public function approve_work_off_attendance_request(Request $request) {

        $id=$request->input('work_off_attendance_id');

        $approve_note=$request->input('approve_note');

        if (Auth::user()->role == config('constants.REAL_HR')) {
            $updateData = ['first_approval_status' => 'Approved',
                'first_approval_id' => Auth::user()->id,
                'first_approval_datetime' => date('Y-m-d H:i:s'),
                'first_approval_note'=>$approve_note];

                //superUser
                $notify_user_id = User::where('role', config('constants.SuperUser'))->get(['id'])->pluck('id')->toArray();
                $this->notification_task->workOffSuperUserApprovalNotify($notify_user_id);

        } elseif (Auth::user()->role == config('constants.SuperUser')) {



            $updateData = ['second_approval_status' => 'Approved', 'second_approval_id' => Auth::user()->id,
                'second_approval_note'=>$approve_note,'status' => 'Approved','second_approval_datetime' => date('Y-m-d H:i:s')];

                //request user
                $request_user_id = WorkOff_AttendanceRequest::where('id',$id)->get(['user_id'])->pluck('user_id')->toArray();
                $this->notification_task->workOffSecondApprovalNotify($request_user_id);
            }

        if (WorkOff_AttendanceRequest::where('id', $id)->update($updateData)) {
            return redirect()->route('admin.work_off_all_attendance_history')->with('success', 'Request successfully Approved.');
        }
        return redirect()->route('admin.work_off_all_attendance_history')->with('error', 'Error during operation. Try again!');
    }

    public function reject_work_off_attendance_request(Request $request) {


        $id=$request->input('reject_id');

        $note=$request->input('note');

        if (Auth::user()->role == config('constants.REAL_HR')) {

            $updateData = ['reject_note' => $note,'first_approval_datetime' => date('Y-m-d H:i:s'), 'first_approval_status' => 'Rejected', 'first_approval_id' => Auth::user()->id, 'status' => 'Rejected'];
        }
        elseif (Auth::user()->role == config('constants.SuperUser')) {

            $updateData = ['reject_note' => $note, 'second_approval_status' => 'Rejected', 'second_approval_id' => Auth::user()->id, 'status' => 'Rejected','second_approval_datetime' => date('Y-m-d H:i:s')];
        }

        if (WorkOff_AttendanceRequest::where('id', $id)->update($updateData)) {

            //request user
            $request_user_id = WorkOff_AttendanceRequest::where('id',$id)->get(['user_id'])->pluck('user_id')->toArray();
            $this->notification_task->workOffRejectNotify($request_user_id);

            return redirect()->route('admin.work_off_all_attendance_history')->with('success', 'Request successfully Rejected!.');
        }
        return redirect()->route('admin.work_off_all_attendance_history')->with('error', 'Error during operation. Try again!');
    }

    public function check_holiday(Request $request){

        $date= $request->date;

        $checkDate = date('Y-m-d',strtotime($date));


        $holiday_name = Holiday::where([['start_date', '<=', $checkDate], ['end_date', '>=', $checkDate]])->where('status','Enabled')->get();

        if ($holiday_name->count() == 0) {
            return response()->json(['holiday' => '']);

        }else{
            return response()->json(['holiday' => $holiday_name]);
        }


    }



}

