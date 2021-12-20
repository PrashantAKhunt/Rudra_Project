<?php

namespace App\Http\Controllers\Api;

use App\Companies;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Lib\CommonTask;
use Illuminate\Support\Facades\Validator;
use App\Employee_expense;
use App\Expense_category;
use Exception;
use Illuminate\Support\Facades\Storage;
use App\Driver_expense;
use App\Employees;
use App\Meeting;
use App\MeetingMOM;
use App\Lib\NotificationTask;
use Illuminate\Support\Facades\Auth;
use App\User;

class MeetingController extends Controller {

    private $page_limit = 20;
    public $common_task;
    private $module_id = 19;
    private $notification_task;
    private $super_admin;

    public function __construct() {
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();
        $this->super_admin = \App\User::where('role', config('constants.SuperUser'))->first();
    }

    //Add meeting details
    public function add_meeting(Request $request) {   //
        
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'meeting_date_time' => 'required',
                    'meeting_end_date_time' => 'required',
                    'meeting_subject' => 'required',
                    'meeting_details' => 'required',
                    'meeting_users' => 'required',
                    'mom_user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $insert_arr = [
            "user_id"           => $request_data['user_id'],
            "mom_user_id" => $request_data['mom_user_id'],
            "meeting_date_time" => $request_data['meeting_date_time'],
            "meeting_end_date_time" => $request_data['meeting_end_date_time'],
            "meeting_subject"   => $request_data['meeting_subject'],
            "meeting_details"   => $request_data['meeting_details'],
            "created_at" => date('Y-m-d H:i:s'),
            "updated_at" => date('Y-m-d H:i:s'),
            "created_ip" => $request->ip(),
            "updated_ip" => $request->ip(),
            "updated_by" => $request_data['user_id']
        ];

        try {
            
            $last_insertId = Meeting::insertGetId($insert_arr);
            $meeting_code  = 1000+intval($last_insertId);
            Meeting::where('id', $last_insertId)->update(['meeting_code'=>$meeting_code]);

            if(!empty($request_data['meeting_users']))
            {
                $meeting_users = explode(',', $request_data['meeting_users']);

                foreach ($meeting_users as $key => $meeting_user)
                {
                    $MeetingUser = new MeetingMOM();
                    $MeetingUser->meeting_id      = $last_insertId;
                    $MeetingUser->meeting_user_id = $meeting_user;
                    $MeetingUser->status          = "Pending";
                    $MeetingUser->created_at      = date('Y-m-d H:i:s');
                    $MeetingUser->updated_at      = date('Y-m-d H:i:s');
                    $MeetingUser->save();
                }

                $this->notification_task->meetingRequestNotify($meeting_users);
            }

            return response()->json(['status' => true, 'msg' => "Meeting successfully added.", 'data' => []]);
        } catch (Exception $exc) {
            return response()->json(['status' => false, 'msg' => config('errors.sql_operation.msg'), 'data' => [], 'error' => config('errors.sql_operation.code')]);
        }
    }

    // Edit meeting details
    public function edit_meeting(Request $request) {  //
        
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'meeting_date_time' => 'required',
                    'meeting_end_date_time' => 'required',
                    'meeting_id'        => 'required',
                    'meeting_subject'   => 'required',
                    'meeting_details'   => 'required',
                    'meeting_users'     => 'required',
                    'mom_user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $update_arr = [
            "user_id"           => $request_data['user_id'],
            "meeting_date_time" => $request_data['meeting_date_time'],
            "meeting_end_date_time" => $request_data['meeting_end_date_time'],
            "meeting_subject"   => $request_data['meeting_subject'],
            "meeting_details"   => $request_data['meeting_details'],
            "mom_user_id" => $request_data['mom_user_id'],
            "created_at" => date('Y-m-d H:i:s'),
            "updated_at" => date('Y-m-d H:i:s'),
            "created_ip" => $request->ip(),
            "updated_ip" => $request->ip(),
            "updated_by" => $request_data['user_id']
        ];

        try {

            Meeting::where('id', $request_data['meeting_id'])->update($update_arr);
            
            if(!empty($request_data['meeting_users']))
            {
                MeetingMOM::where('meeting_id',$request_data['meeting_id'])->delete();

                $meeting_users_arr = explode(',', $request_data['meeting_users']);

                foreach ($meeting_users_arr as $key => $meeting_users)
                {
                     
                    $MeetingUser = new MeetingMOM();
                    $MeetingUser->meeting_id      = $request_data['meeting_id'];
                    $MeetingUser->meeting_user_id = $meeting_users;
                    $MeetingUser->status          = "Pending";
                    $MeetingUser->created_at      = date('Y-m-d H:i:s');
                    $MeetingUser->updated_at      = date('Y-m-d H:i:s');
                    $MeetingUser->save();    
                }

                $this->notification_task->meetingRequestNotify($meeting_users_arr);
            }

            return response()->json(['status' => true, 'msg' => "Meeting successfully updated.", 'data' => []]);
        } catch (Exception $exc) {
            return response()->json(['status' => false, 'msg' => config('errors.sql_operation.msg'), 'data' => [], 'error' => config('errors.sql_operation.code')]);
        }
    }

    //Get user list
    public function get_meeting_user_list(Request $request) {  //users list for dropdown

        $validator = Validator::make($request->all(), [
                    'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $userData = User::select('id', 'name', 'profile_image', 'role')->where(['status' => 'Enabled'])->where('is_user_relieved', '=', 0)->with(['role' => function($query) {
                        $query->select('id', 'role_name');
                    }])->get();

        if ($userData->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        } else {
            foreach ($userData as $key => $value) {
                if (!empty($value->profile_image))
                    $userData[$key]->profile_image = asset('storage/' . str_replace('public/', '', $value->profile_image));
            }
        }

        return response()->json(['status' => true, 'msg' => 'Get user details!', 'data' => $userData]);
    }

    //Add and Edit meeting details
    public function add_edit_meeting_mom(Request $request) {  //add edit mom
        
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'meeting_id' => 'required',
                    // 'meeting_mom_details' => 'required',
                    'meeting_mom_asset_file' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $document_file = '';
        if ($request_data['meeting_mom_asset_file']) {
           
            $document_file = $request_data['meeting_mom_asset_file'];

            $original_file_name = explode('.', $document_file->getClientOriginalName());
            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);
            $file_path = $document_file->storeAs('public/meeting_mom_asset_file', $new_file_name);
            if ($file_path) {
                $document_file = $file_path;
            }
        }

        $meeting_arr = [
            // "mom_details" => $request_data['meeting_mom_details'],
            "mom_asset" => $document_file,
            "updated_at" => date('Y-m-d H:i:s')
        ];

        try {

            Meeting::where('id', $request_data['meeting_id'])->update($meeting_arr);
            
            if ($request_data['meeting_mom_asset_file']) {

                $attend_user_list = Meeting::where('id',$request_data['meeting_id'])->get(['meeting_code','meeting_subject','attend_user_id','outsiders_email','mom_asset']);
                $attend_user_mails = \App\user::where('status', 'Enabled')->whereIn('id', explode(",",$attend_user_list[0]->attend_user_id))->pluck('email')->toArray();
                $outsiders_mails = explode("," , $attend_user_list[0]->outsiders_email);
                
                $mail_data = [];
                $mail_data['meeting_code'] = $attend_user_list[0]->meeting_code;
                $mail_data['meeting_subject'] = $attend_user_list[0]->meeting_subject;
                $mail_data['cc_mails'] = array_merge($attend_user_mails, $outsiders_mails);
                $mail_data['attach_file'] = asset('storage/' . str_replace('public/', '', $attend_user_list[0]->mom_asset));
                
               
                $this->common_task->meetingMOMNotify($mail_data);
            }

          
            
            $insert_arr = [
                // "meeting_mom_details" => $request_data['meeting_mom_details'],
                "meeting_mom_asset" => $document_file,
                "updated_at" => date('Y-m-d H:i:s')
            ];

            MeetingMOM::where('meeting_id', $request_data['meeting_id'])->update($insert_arr);

            return response()->json(['status' => true, 'msg' => "Meeting MOM successfully added.", 'data' => []]);
        } catch (Exception $exc) {
        
            return response()->json(['status' => false, 'msg' => config('errors.sql_operation.msg'), 'data' => [], 'error' => config('errors.sql_operation.code')]);
        }
    }

    //View meeting mom   NO
    public function get_all_user_meeting_mom_details(Request $request) {  //users details in mom table
        
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'meeting_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $meeting_mom_details = MeetingMOM::leftjoin('users', 'users.id', '=', 'MeetingMOM.meeting_user_id')
                                        ->where('meeting_id',$request_data['meeting_id'])->get(['MeetingMOM.id as meeting_mom_id', 'meeting_mom_details','meeting_user_id','users.name','meeting_id']);

        if ($meeting_mom_details->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }
        $response_data = $meeting_mom_details;
        return response()->json(['status' => true, 'msg' => "Record Found.", 'data' => $response_data]);
    }

    //get specific user meeting mom
    public function get_user_meeting_mom_list(Request $request) {  //mom details
        
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'meeting_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        // $meeting_mom_details = MeetingMOM::leftjoin('users', 'users.id', '=', 'MeetingMOM.meeting_user_id')
        //                                 ->where('meeting_id',$request_data['meeting_id'])
        //                                 ->where('meeting_user_id',$request_data['user_id'])
        //                                 ->get(['MeetingMOM.id as meeting_mom_id', 'meeting_mom_details','meeting_user_id','users.name','meeting_id']);

        $meeting_mom_details = Meeting::where('id',$request_data['meeting_id'])->where('user_id',$request_data['user_id'])->get(['mom_details']);

        if ($meeting_mom_details->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }
        $response_data = $meeting_mom_details;
        return response()->json(['status' => true, 'msg' => "Record Found.", 'data' => $response_data]);
    }

     //Get meeting list
    public function get_meeting_list(Request $request) {  //listing for accept or reject  27/04/2020

        $validator = Validator::make($request->all(), [
                    'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();

        $meeting_details = Meeting::join('MeetingMOM', 'meeting.id','=','MeetingMOM.meeting_id')
            ->where('MeetingMOM.meeting_user_id','=',$request_data['user_id'])
            ->orderBy('meeting.id', 'DESC')
            ->get(['meeting.*','MeetingMOM.meeting_mom_details','MeetingMOM.status']);

        if ($meeting_details->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        } 

        foreach ($meeting_details as $key => $value) {

            if ($value->mom_asset) {
                $meeting_details[$key]->mom_asset = asset('storage/' . str_replace('public/', '', $value->mom_asset));
            } else {
                $meeting_details[$key]->mom_asset = "";
            }

        }

        return response()->json(['status' => true, 'msg' => 'Get user details!', 'data' => $meeting_details]);
    }

     //Get meeting list
    public function all_meeting_list(Request $request) {  //listing for accept  27/04/2020

        $validator = Validator::make($request->all(), [
                    'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();

        $meeting_details = Meeting::with(['meetingUsers'])
            ->leftjoin('users as B','B.id','=','meeting.mom_user_id')
            ->where('user_id',$request_data['user_id'])
            ->orWhere('mom_user_id',$request_data['user_id'])
            ->leftjoin("users",\DB::raw("FIND_IN_SET(users.id,meeting.attend_user_id)"),">",\DB::raw("0"))
            ->groupBy("meeting.id")
            ->orderBy('meeting.id', 'DESC')
            ->get(['meeting.*','B.name as mom_username',\DB::raw("GROUP_CONCAT(users.name) as attended_persons"),'mom_user_id']);

        if ($meeting_details->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        } 

        foreach ($meeting_details as $key => $value) {

            if ($value->mom_asset) {
                $meeting_details[$key]->mom_asset = asset('storage/' . str_replace('public/', '', $value->mom_asset));
            } else {
                $meeting_details[$key]->mom_asset = "";
            }

            foreach ($value->meetingUsers as $index => $list) {
                  $meeting_details[$key]->meetingUsers[$index]->name = User::where('id', $list->meeting_user_id)->value('name');
            }
        }

        return response()->json(['status' => true, 'msg' => 'Get user details!', 'data' => $meeting_details]);
    }

    //accept meeting
    public function accept_meeting_request(Request $request) {  //
        
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'meeting_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $insert_arr = [
            "status" => "Accept",
            "updated_at" => date('Y-m-d H:i:s')
        ];

        try {
            MeetingMOM::where('meeting_id', $request_data['meeting_id'])->where('meeting_user_id', $request_data['user_id'])->update($insert_arr);

            return response()->json(['status' => true, 'msg' => "Meeting request accepted successfully.", 'data' => []]);
        } catch (Exception $exc) {
            return response()->json(['status' => false, 'msg' => config('errors.sql_operation.msg'), 'data' => [], 'error' => config('errors.sql_operation.code')]);
        }
    }

    //reject meeting
    public function reject_meeting_request(Request $request) {  //
        
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'meeting_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $insert_arr = [
            "status" => "Rejected",
            "updated_at" => date('Y-m-d H:i:s')
        ];

        try {
            MeetingMOM::where('meeting_id', $request_data['meeting_id'])->where('meeting_user_id', $request_data['user_id'])->update($insert_arr);
            return response()->json(['status' => true, 'msg' => "Meeting request rejected successfully.", 'data' => []]);
        } catch (Exception $exc) {
            return response()->json(['status' => false, 'msg' => config('errors.sql_operation.msg'), 'data' => [], 'error' => config('errors.sql_operation.code')]);
        }
    }

    //10/09/2020
    public function get_meeting_approvals(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();

        $meeting_approvals = Meeting::join('MeetingMOM', 'meeting.id','=','MeetingMOM.meeting_id')
            ->where('MeetingMOM.meeting_user_id','=',$request_data['user_id'])
            ->where('MeetingMOM.status','Pending')
            ->orderBy('meeting.id', 'DESC')
            ->get(['meeting.*','MeetingMOM.meeting_mom_details','MeetingMOM.status']);

        if ($meeting_approvals->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        } 

        return response()->json(['status' => true, 'msg' => 'Get records!', 'data' => $meeting_approvals]);
    }
    //10/09/2020
    public function get_meeting_approvals_ount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();

        $meeting_approvals_count = Meeting::join('MeetingMOM', 'meeting.id','=','MeetingMOM.meeting_id')
            ->where('MeetingMOM.meeting_user_id','=',$request_data['user_id'])
            ->where('MeetingMOM.status','Pending')
            ->orderBy('meeting.id', 'DESC')
            ->get()->count();


        return response()->json(['status' => true, 'msg' => 'Get records count!', 'data' => $meeting_approvals_count]);
    }
    //11/09/2020
    public function close_meeting(Request $request)  //
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'meeting_id' => 'required',
            'actual_meeting_start_datetime' => 'required',
            'actual_meeting_end_datetime' => 'required',
            'attend_user_id.*' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $outsiders_email = [];

        if ($request_data['outsiders_email']) {
            $email_arr = explode(",",$request_data['outsiders_email']);
            foreach ($email_arr as $key => $email) {
                // str_replace(' ','',$email);
                $email = preg_replace('/\s+/','',$email);
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    return response()->json(['status' => false, 'msg' => 'Email invalid..Please provide valid Email', 'data' => [], 'error' => config('errors.validation.code')]); 
                }
                array_push($outsiders_email,$email); 

            }
        }
        $update_arr = [
            'is_close'=>'Yes',
            'attend_user_id' => $request_data['attend_user_id'],
            'outsiders_email' => $request_data['outsiders_email'] ?  implode(',',$outsiders_email) : NULL,
            "actual_meeting_start_datetime" => $request_data['actual_meeting_start_datetime'],
            "actual_meeting_end_datetime" => $request_data['actual_meeting_end_datetime'],
            "updated_at" => date('Y-m-d H:i:s')
        ];

        if (Meeting::where('id', $request_data['meeting_id'])->update($update_arr)) 
        {
            return response()->json(['status' => true, 'msg' => 'Meeting is closed', 'data' => []]);
        }
        return response()->json(['status' => false, 'msg' => 'Error during operation. Try again', 'data' => []]);
    }
}
