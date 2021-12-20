<?php

namespace App\Http\Controllers\Admin;

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
use App\Lib\Permissions;
use App\Lib\NotificationTask;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Common_query;
use Illuminate\Support\Facades\Input;

class MeetingController extends Controller {

    private $page_limit = 20;
    public $common_task;
    private $module_id = 56;
    private $notification_task;
    private $super_admin;

    public function __construct() {
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();
        $this->super_admin = \App\User::where('role', config('constants.SuperUser'))->first();
        $this->data['module_title'] = "Meeting";
        $this->data['module_link'] = "admin.meeting";
    }
    
    public function index() {

        $check_result = Permissions::checkPermission(56, 5);
        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }
        $this->data['users'] = User::whereStatus('Enabled')->pluck('name', 'id');
        $this->data['page_title'] = "Meeting";
        return view('admin.meeting.index', $this->data);
    }

    public function get_meeting_list() {
        
        $datatable_fields =array('meeting_code','meeting_categories','meeting_subject','meeting_details','meeting_date_time','meeting_end_date_time','actual_meeting_start_datetime','actual_meeting_end_datetime','meeting.is_close','mom_asset','users.name', 'mom_image','attend_user_id','outsiders_email');
        $request = Input::all();
        $conditions_array = ['user_id'=> Auth::user()->id];
        $orconditions_array = ['mom_user_id'=> Auth::user()->id];

        $table = "meeting";

        $join_str[0]['join_type'] = 'left';
        $join_str[0]['table'] = 'users as B';
        $join_str[0]['join_table_id'] = 'meeting.mom_user_id';
        $join_str[0]['from_table_id'] = 'B.id';

        $getfiled =array('meeting.id','meeting.meeting_categories','meeting.user_id','meeting.meeting_code','meeting_subject','meeting_details','meeting_date_time','meeting_end_date_time','actual_meeting_start_datetime','actual_meeting_end_datetime','meeting.is_close','mom_asset','B.name as mom_user', 'meeting.mom_image','attend_user_id',\DB::raw("GROUP_CONCAT(users.name) as fullname"),'mom_user_id','outsiders_email');

        echo Meeting::get_list_datatable_ajax($table, $datatable_fields, $conditions_array,$orconditions_array, $getfiled, $request,$join_str,[]);
        die();
    }
    
    public function add_meeting() {


        $check_result = Permissions::checkPermission(56, 3);
        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }

        $this->data['page_title'] = 'Add Meeting';
        $this->data['users_data'] = User::orderBy('name')->select('id', 'name')->where('status', 'Enabled')->where('is_user_relieved', 0)->get();
        return view('admin.meeting.add_meeting', $this->data);
    }

    //Add meeting details
    public function insert_meeting(Request $request) {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
                    'meeting_date_time' => 'required',
                    'meeting_end_date_time' => 'required',
                    'meeting_subject' => 'required',
                    'meeting_details' => 'required',
                    'meeting_users' => 'required',
                    'mom_user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.add_meeting')->with('error', 'Please follow validation rules.');
        }

        $request_data = $request->all();
        $response_data = [];

        // pdf mode start
        $mom_image = NULL;
        if ($request->file('mom_image')) {

            $asset_file = $request->file('mom_image');

            $original_file_name = explode('.', $asset_file->getClientOriginalName());

            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);


            $file_path = $asset_file->storeAs('public/mom_image', $new_file_name);
            if ($file_path) {
                $mom_image = $file_path;
            }
        }

        // pdf mode end

      
        $insert_arr = [
            "user_id"           => Auth::user()->id,
            "mom_user_id" => $request_data['mom_user_id'],
            "meeting_date_time" => $request_data['meeting_date_time'],
            "meeting_end_date_time" => $request_data['meeting_end_date_time'],
            "meeting_subject"   => $request_data['meeting_subject'],
            "meeting_details"   => $request_data['meeting_details'],
            "created_at" => date('Y-m-d H:i:s'),
            "updated_at" => date('Y-m-d H:i:s'),
            "created_ip" => $request->ip(),
            "updated_ip" => $request->ip(),
            "updated_by" => Auth::user()->id,
            "meeting_categories" => $request_data['meeting_categories'],
            "mom_image" => $request_data = $mom_image,
            
        ];

        try {
            
            $last_insertId = Meeting::insertGetId($insert_arr);
            $meeting_code  = 1000+intval($last_insertId);
            Meeting::where('id', $last_insertId)->update(['meeting_code'=>$meeting_code]);

            if(!empty($request_data['meeting_users']))
            {
                $meeting_users = $request_data['meeting_users'];
               
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
            return redirect()->route('admin.meeting')->with('success', 'Meeting successfully added.');
        } catch (Exception $exc) {
            return redirect()->route('admin.meeting')->with('error', 'Error occurre in insert. Try Again!');
        }
    }
    
    public function edit_meeting($id) {

    
        $check_result = Permissions::checkPermission(56, 2);
        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }

        $this->data['page_title'] = "Edit Meeting";
        $this->data['meeting_detail']   = Meeting::where('id', $id)->get();
        $this->data['meeting_user_ids'] = '';
        $meeting_user_id = MeetingMOM::where('meeting_id', $id)->pluck('meeting_user_id','id')->toArray();
        if(!empty($meeting_user_id))
        {
            $this->data['meeting_user_ids'] = implode(',',$meeting_user_id);
        }
        $this->data['users_data'] = User::orderBy('name')->select('id', 'name')->where('status', 'Enabled')->where('is_user_relieved', 0)->get();
        if ($this->data['meeting_detail']->count() == 0) {
            return redirect()->route('admin.meeting')->with('error', 'Error Occurred. Try Again!');
        }
        return view('admin.meeting.edit_meeting', $this->data);
    }

    // Edit meeting details
    public function update_meeting(Request $request) {
        
        $validator = Validator::make($request->all(), [
                    'meeting_date_time' => 'required',
                    'meeting_end_date_time' => 'required',
                    'meeting_subject' => 'required',
                    'meeting_details' => 'required',
                    'meeting_users' => 'required',
                    'mom_user_id' => 'required'
        ]);
        $request_data = $request->all();
        if ($validator->fails()) {
            return redirect()->route('admin.edit_meeting',$request_data['meeting_id'])->with('error', 'Please follow validation rules.');
        }

        
        $response_data = [];

        $update_arr = [
            "user_id"           => Auth::user()->id,
            "mom_user_id" => $request_data['mom_user_id'],
            "meeting_date_time" => $request_data['meeting_date_time'],
            "meeting_end_date_time" => $request_data['meeting_end_date_time'],
            "meeting_subject"   => $request_data['meeting_subject'],
            "meeting_details"   => $request_data['meeting_details'],
            "created_at" => date('Y-m-d H:i:s'),
            "updated_at" => date('Y-m-d H:i:s'),
            "created_ip" => $request->ip(),
            "updated_ip" => $request->ip(),
            "updated_by" => Auth::user()->id
        ];

        try {

            Meeting::where('id', $request_data['meeting_id'])->update($update_arr);
            
            if(!empty($request_data['meeting_users']))
            {
                MeetingMOM::where('meeting_id',$request_data['meeting_id'])->delete();

                $meeting_users_arr = $request_data['meeting_users'];
                foreach ($meeting_users_arr as $key => $meeting_user)
                {
                     
                    $MeetingUser = new MeetingMOM();
                    $MeetingUser->meeting_id      = $request_data['meeting_id'];
                    $MeetingUser->meeting_user_id = $meeting_user;
                    $MeetingUser->status          = "Pending";
                    $MeetingUser->created_at      = date('Y-m-d H:i:s');
                    $MeetingUser->updated_at      = date('Y-m-d H:i:s');
                    $MeetingUser->save();    
                }

                $this->notification_task->meetingRequestNotify($meeting_users_arr);
            }

            return redirect()->route('admin.meeting')->with('success', 'Meeting successfully updated.');
        } catch (Exception $exc) {
            return redirect()->route('admin.meeting')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    //Add and Edit meeting details
    public function add_edit_meeting_mom(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'meeting_id' => 'required',
                    // 'meeting_mom' => 'required',
                    'meeting_mom_asset_file' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.meeting')->with('error', 'Please follow validation rules.');
        }

        $request_data  = $request->all();
        $response_data = [];

        $document_file = '';
        if ($request->file('meeting_mom_asset_file')) {
           
            $document_file = $request->file('meeting_mom_asset_file');

            $mime_type= $document_file->getMimeType();
            $original_filename= $document_file->getClientOriginalName();

            $original_file_name = explode('.', $document_file->getClientOriginalName());
            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);
            $file_path = $document_file->storeAs('public/meeting_mom_asset_file', $new_file_name);
            if ($file_path) {
                $document_file = $file_path;
            }
        }
       
        $meeting_arr = [
            // "mom_details" => $request_data['meeting_mom'],
            "mom_asset" => $document_file,
            "updated_at" => date('Y-m-d H:i:s')
        ];
        
        try {

            Meeting::where('id', $request_data['meeting_id'])->update($meeting_arr);

            if ($request->file('meeting_mom_asset_file')) {

                $attend_user_list = Meeting::where('id',$request_data['meeting_id'])->get(['meeting_code','meeting_subject','attend_user_id','outsiders_email','mom_asset']);
                $attend_user_mails = \App\user::where('status', 'Enabled')->whereIn('id', explode(",",$attend_user_list[0]->attend_user_id))->pluck('email')->toArray();
                $outsiders_mails = explode("," , $attend_user_list[0]->outsiders_email);
                
                $mail_data = [];
                $mail_data['mime_type'] = $mime_type;
                $mail_data['file_name'] = $original_filename;
                $mail_data['meeting_code'] = $attend_user_list[0]->meeting_code;
                $mail_data['meeting_subject'] = $attend_user_list[0]->meeting_subject;
                $mail_data['cc_mails'] = array_merge($attend_user_mails, $outsiders_mails);
                $mail_data['attach_file'] = asset('storage/' . str_replace('public/', '', $attend_user_list[0]->mom_asset));
                
               
                $this->common_task->meetingMOMNotify($mail_data);
            }
          

            $insert_arr = [
                // "meeting_mom_details" => $request_data['meeting_mom'],
                "meeting_mom_asset" => $document_file,
                "updated_at" => date('Y-m-d H:i:s')
            ];

            MeetingMOM::where('meeting_id', $request_data['meeting_id'])->update($insert_arr);

            return redirect()->route('admin.meeting')->with('success', 'Meeting mom successfully added!.');
        } catch (Exception $exc) {
            //return response()->json(['status' => false, 'msg' => config('errors.sql_operation.msg'), 'data' => [], 'error' => config('errors.sql_operation.code')]);
            return redirect()->route('admin.meeting')->with('error', 'Error occure in insert. Try Again!');

        }
    }

    //get meeting mom
    public function get_user_meeting_mom_list($meeting_id,Request $request) {    //mom details

        $request_data = $request->all();
        $response_data = [];

        //$meeting_mom_details = MeetingMOM::where('meeting_id',$meeting_id)->where('meeting_user_id',Auth::user()->id)->get(['meeting_mom_details']);
        $meeting_mom_details = Meeting::where('id',$meeting_id)->where('user_id',Auth::user()->id)->get(['mom_details']);

        if ($meeting_mom_details->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
            die();
        }
        //$response_data = $meeting_mom_details;

        return response()->json(['status' => true, 'msg' => "Record Found.", 'meeting_mom_details' => $meeting_mom_details[0]->mom_details]);
        die();
    }


    public function delete_meeting($id)  //
    {
        if (Meeting::where('id', $id)->delete()) 
        {
            return redirect()->route('admin.meeting')->with('success', 'Delete successfully updated.');
        }
        
        return redirect()->route('admin.meeting')->with('error', 'Error during operation. Try again!');
    }

    //10/09/2020
    public function close_meeting(Request $request)  //
    {
        $validator = Validator::make($request->all(), [
            'close_meeting_id' => 'required',
            'actual_meeting_start_datetime' => 'required',
            'actual_meeting_end_datetime' => 'required',
            'attend_user_id.*' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.meeting')->with('error', 'Please follow validation rules.');
        }
        $request_data = $request->all();
        $outsiders_email = [];
        if ($request_data['outsiders_email']) {
            $email_arr = explode(",",$request_data['outsiders_email']);
            foreach ($email_arr as $key => $email) {
                // str_replace(' ','',$email);
                $email = preg_replace('/\s+/','',$email);
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    return redirect()->route('admin.meeting')->with('error', 'Email invalid..Please provide valid Email');  
                }
                array_push($outsiders_email,$email); 

            }
        }
        $update_arr = [
            'is_close'=>'Yes',
            'attend_user_id' => implode(',',$request_data['attend_user_id']),
            'outsiders_email' => $request_data['outsiders_email'] ?  implode(',',$outsiders_email) : NULL,
            "actual_meeting_start_datetime" => $request_data['actual_meeting_start_datetime'],
            "actual_meeting_end_datetime" => $request_data['actual_meeting_end_datetime'],
            "updated_at" => date('Y-m-d H:i:s')
        ];

        if (Meeting::where('id', $request_data['close_meeting_id'])->update($update_arr)) 
        {
            return redirect()->route('admin.meeting')->with('success', 'Meeting is closed.');
        }
        
        return redirect()->route('admin.meeting')->with('error', 'Error during operation. Try again!');
    }

    public function get_mom_user_list($id)  // users list
    {  
        $this->data['user_list'] = MeetingMOM::leftjoin('users', 'users.id', '=', 'MeetingMOM.meeting_user_id')->where('meeting_id',$id)
            ->get(['MeetingMOM.id as meeting_mom_id', 'meeting_mom_details','meeting_user_id','MeetingMOM.status','users.name','meeting_id']);
       
            echo json_encode($this->data['user_list']);
    }

    //======================= JSON 
      //accept meeting
      public function accept_meeting_request(Request $request) {  //nish
        
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
            MeetingMOM::where('meeting_id', $request_data['meeting_id'])->where('meeting_user_id', Auth::user()->id)->update($insert_arr);

            return response()->json(['status' => true, 'msg' => "Meeting request accepted successfully.", 'data' => []]);
        } catch (Exception $exc) {
            return response()->json(['status' => false, 'msg' => config('errors.sql_operation.msg'), 'data' => [], 'error' => config('errors.sql_operation.code')]);
        }
    }

}

