<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Common_query;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Imports\BankTransactionImport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Config;
use App\Leaves;
use App\AttendanceMaster;
use App\LeaveCategory;
use App\LeaveMaster;
use App\User;
use App\Email_format;
use App\Role_module;
use App\Mail\Mails;
use Illuminate\Support\Facades\Mail;
use App\Lib\CommonTask;
use App\Lib\NotificationTask;
use DateTime;
use DataTables;
use Yajra\DataTables\DataTables as DataTablesDataTables;
use Yajra\DataTables\Facades\DataTables as FacadesDataTables;

class LeaveController extends Controller {

    public $data;
    public $common_task;
    private $module_id = 4;
    public $notification_task;

    public function __construct() {
        $this->data['module_title'] = "Leave";
        $this->data['module_link'] = "admin.leave";
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();
    }

    public function index() {
        $this->data['page_title'] = "Leave";
        $access_level = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => 4])->get()->first();
        $this->data['access_rule'] = '';
        if (!empty($access_level)) {
            $this->data['access_rule'] = $access_level->access_level;
        }

        return view('admin.leave.index', $this->data);
    }

    public function get_my_leave_list(Request $request) {   //change

        $userId = Auth::user()->id;if($request->ajax()){
            $getfiled = array('leaves.description','users.name as user_name','B.name as reliever_name','leaves.id','leaves.start_date','leaves.assign_work_status','leaves.assign_work_datetime', 'leaves.subject', 'leaves.end_date', 'leave_category.name', 'leaves.leave_status','leaves.first_approval_status', 'leaves.first_approval_datetime', 'leaves.second_approval_status', 'leaves.second_approval_datetime', 'leaves.third_approval_status', 'leaves.third_approval_datetime', 'leaves.approver_id', 'leaves.created_at', 'leaves.updated_at','leaves.leave_cancellation_status'
        );
            $list_query = Leaves::leftjoin('leave_category', 'leave_category.id', '=', 'leaves.leave_category_id')
            ->leftjoin('users','users.id', '=', 'leaves.user_id')
            ->leftjoin('users AS B','B.id', '=', 'leaves.assign_work_user_id')
            ->where('leaves.user_id',$userId)
            ->get($getfiled);
            
            return FacadesDataTables::of($list_query)->make(true);
        }


        die();
    }

    // status change entry from the list first leaves, attendance_master and leave_master

    public function reversal_leave(Request $request){
        $leave = Leaves::where('id',$request->get('leave_id'))->first();
        $leave->leave_cancellation_status = "Pending";
        $leave->leave_reversal_note = $request->get('note');
       
        if($leave->save()){
            /* $attendance = AttendanceMaster::where('user_id',$leave['user_id'])->whereDate('date',date('Y-m-d'))->first();
            if($attendance != null){
                $attendance->availability_status = 1;
                $attendance->save();

                if(!in_array($leave->leave_category_id,array(2, 4, 6))){
                    $leaves_maintain = LeaveMaster::where('user_id',$leave['user_id']->whereCatergory('leave_category_id',$leave['leave_category_id']))->first();
                    $leaves_maintain->increment('balance', 1);
                }
            } */
            return redirect()->route('admin.leave')->with('success', 'Leave reversal request send successfully.');
        }
    }
   

    public function cancel_leave($id) {
        $leaveModel = Leaves::find($id);
        if($leaveModel->first_approval_status=="Approved" && $leaveModel->leave_status!=3){
            // return response()->json(['status' => false, 'msg' => "You are not allowed to cancel this leave because it is in process.", 'data' => [], 'error' => config('errors.validation.code')]);
            return redirect()->route('admin.leave')->with('error', 'You are not allowed to cancel this leave because it is in process.');
        }
        
        if (Leaves::where('id', $id)->update(['leave_status' => 4])) { // 4 = Leave Cancel
            $leaveModel = Leaves::find($id);
            //Send Cancel Applied Leave Mail
            $emailData = Email_format::find(9)->toArray(); // 9 = Cancel Applied Leave
            $subject = $emailData['subject'];
            $mailformat = $emailData['emailformat'];
            $mailformat = str_replace("%user_name%", Auth::user()->name, $mailformat);
            $mailformat = str_replace("%start_date%", $leaveModel->start_date, $mailformat);
            $mailformat = str_replace("%start_day%", config::get('constants.LEAVE_DAY.' . $leaveModel->start_day), $mailformat);
            $mailformat = str_replace("%end_date%", $leaveModel->end_date, $mailformat);
            $mailformat = str_replace("%end_day%", config::get('constants.LEAVE_DAY.' . $leaveModel->end_day), $mailformat);
            $mailformat = str_replace("%subject%", $leaveModel->subject, $mailformat);
            $mailformat = str_replace("%description%", $leaveModel->description, $mailformat);
            $mailformat = str_replace("%leave_user_name%", Auth::user()->name, $mailformat);

            //$hrMail = config::get('app.HR_EMAIL');
            //$emailList = DB::table('users')->whereIn('id', array_merge([$leaveModel->approver_id], explode(",", $leaveModel->notify_id)))->pluck('email')->toArray();
            //send email to hr and work reliver
            $work_reliver = User::where('id', $leaveModel->assign_work_user_id)->get(['email'])->pluck('email')->toArray();
            $hr_email = User::where('role', config('constants.REAL_HR'))->get(['email'])->pluck('email')->toArray();
            Mail::to($work_reliver)->cc($hr_email)->send(new Mails($subject, $mailformat));

            return redirect()->route('admin.leave')->with('success', 'Leave successfully canceled.');
        }
        return redirect()->route('admin.leave')->with('error', 'Error during operation. Try again!');
    }

    public function add_leave() {
        $this->data['page_title'] = 'Add leave';
        $this->data['categories'] = LeaveMaster::where(['user_id' => Auth::user()->id])->with(['leavecategory' => function($query) {
                        $query->select('id', 'name');
                    }])->get(['id', 'balance', 'leave_category_id'])->toArray();
        $this->data['user'] = User::getUser();

        $user_select_field = ['users.id', 'users.name', 'employee.designation', 'employee.emp_code', 'users.profile_image'];

        $loggedin_user_detail = \App\Employees::where('user_id', Auth::user()->id)->get();

        $this->data['user_list'] = User::orderBy('name')->join('employee', 'employee.user_id', '=', 'users.id')
                //->where('department_id', $loggedin_user_detail[0]->department_id)
                ->where('users.role', '!=', 1)
                ->where('users.id', '!=', Auth::user()->id)
                ->get($user_select_field);

        return view('admin.leave.add_leave', $this->data);
    }

    public function insert_leave(Request $request) {
        $validator_normal = Validator::make($request->all(), [
                    'subject' => 'required',
                    'description' => 'required',
                    'start_date' => 'required',
                    'end_date' => 'required',
                    'leave_category_id' => 'required',
                    //'notify_id' => 'required',
                    'assign_work_details' => 'required',
                    'assign_work_user_id' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_leave')->with('error', 'Please follow validation rules.');
        }

        $leave_start_exist = Leaves::whereDate('start_date', '<=', date('Y-m-d', strtotime($request->input('start_date'))))
                        ->whereDate('end_date', '>=', date('Y-m-d', strtotime($request->input('start_date'))))
                        ->whereIn('leave_status', [1, 2])
                        ->where('user_id', Auth::user()->id)
                        ->get()->first();
        $leave_end_exist = Leaves::whereDate('start_date', '<=', date('Y-m-d', strtotime($request->input('end_date'))))
                        ->whereDate('end_date', '>=', date('Y-m-d', strtotime($request->input('end_date'))))
                        ->whereIn('leave_status', [1, 2])
                        ->where('user_id', Auth::user()->id)
                        ->get()->first();

        if (!empty($leave_start_exist) || !empty($leave_end_exist)) {
            return redirect()->route('admin.add_leave')->with('error', 'Leave is already applied on selected date.');
        }

        //fetch user data who apply leave
        $user_data = \App\User::join('employee', 'employee.user_id', '=', 'users.id')->where('users.id', Auth::user()->id)->get(['users.id', 'users.name', 'employee.reporting_user_id']);

        $notify_ids = [$user_data[0]->reporting_user_id];
        $hr_list = User::where('role', config('constants.REAL_HR'))->get(['id'])->pluck('id')->toArray();
        //get users with full view access to add into notify ids
        $role_list = $this->common_task->getRoleByModulePermission($this->module_id, 5);
        if ($role_list->count() > 0) {
            $user_list_by_role = User::whereIn('role', $role_list->pluck('role_id'))->get('id')->pluck('id')->toArray();
            array_push($notify_ids, $user_list_by_role);
        }

        $leaveModel = new Leaves();
        $leaveModel->subject = $request->input('subject');
        $leaveModel->description = $request->input('description');
        $leaveModel->leave_category_id = $request->input('leave_category_id');
        $leaveModel->start_date = $request->input('start_date');
        $leaveModel->end_date = $request->input('end_date');
        $leaveModel->start_day = $request->input('start_day');
        $leaveModel->end_day = $request->input('end_day');
        //$leaveModel->notify_id = implode(",", $request->input('notify_id'));
        //$leaveModel->notify_id = implode(',', $hr_list);
        $leaveModel->user_id = Auth::user()->id;
        //$leaveModel->approver_id = 8; // user::find(Auth::user()->id)->get('approver_id');
        $leaveModel->leave_status = 1; // 1 = Pending
        $leaveModel->created_at = date('Y-m-d h:i:s');
        $leaveModel->created_ip = $request->ip();
        $leaveModel->updated_at = date('Y-m-d h:i:s');
        $leaveModel->updated_ip = $request->ip();
        $leaveModel->assign_work_user_id = $request->input('assign_work_user_id');
        $leaveModel->assign_work_details = $request->input('assign_work_details');

        if ($leaveModel->save()) {

            //notify only hr about leaves apply
            $hr_list = User::where('role', config('constants.REAL_HR'))->get(['id'])->pluck('id')->toArray();

            $mail_data = [
                'start_date' => $leaveModel->start_date,
                'start_day' => config::get('constants.LEAVE_DAY.' . $leaveModel->start_day),
                'end_date' => $leaveModel->end_date,
                'end_day' => config::get('constants.LEAVE_DAY.' . $leaveModel->end_day),
                'leave_subject' => $leaveModel->subject,
                'description' => $leaveModel->description,
                'notify_id' => implode(',', $hr_list),
            ];

            //$this->common_task->applyLeaveEmail($mail_data);
            $this->notification_task->leaveRequestNotify($hr_list, $user_data[0]->name);
            //$this->notification_task->leaveRequestNotify($notify_ids, $user_data[0]->name);
            //send email about work assign
            //get details of user assigned for work
            $assign_user = User::where('id', $request->input('assign_work_user_id'))->get(['name', 'email']);
            $assign_leave_work_mail_data = [
                'start_date' => $leaveModel->start_date,
                'start_day' => config::get('constants.LEAVE_DAY.' . $leaveModel->start_day),
                'end_date' => $leaveModel->end_date,
                'end_day' => config::get('constants.LEAVE_DAY.' . $leaveModel->end_day),
                'leave_subject' => $leaveModel->subject,
                'description' => $leaveModel->description,
                'name' => $user_data[0]->name,
                'email' => $assign_user[0]->email,
                'assign_work_details' => $leaveModel->assign_work_details,
                'assign_name' => $assign_user[0]->name
            ];
            $this->common_task->assignLeaveWorkEmail($assign_leave_work_mail_data);

            $this->notification_task->leaveRelieverNotify([$request->input('assign_work_user_id')], $user_data[0]->name);
            return redirect()->route('admin.leave')->with('Your leave request successfully submitted.');
        } else {
            return redirect()->route('admin.add_leave')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function edit_leave($id) {

        $this->data['page_title'] = "Edit leave";
        $this->data['leave_detail'] = Leaves::where('id', $id)->get();
        $this->data['categories'] = LeaveMaster::where(['user_id' => Auth::user()->id])->with(['leavecategory' => function($query) {
                        $query->select('id', 'name');
                    }])->get(['id', 'balance', 'leave_category_id'])->toArray();
        $this->data['user'] = User::getUser();

        if ($this->data['leave_detail']->count() == 0) {
            return redirect()->route('admin.leave')->with('error', 'Error Occurred. Try Again!');
        }

        if ($this->data['leave_detail'][0]->assign_work_status == "Accepted" && ($this->data['leave_detail'][0]->first_approval_status != "Rejected" || $this->data['leave_detail'][0]->second_approval_status != "Rejected" || $this->data['leave_detail'][0]->third_approval_status != "Rejected")) {
            return redirect()->route('admin.leave')->with('error', 'Your leave request already in process. You can not edit it at this time.');
        }

        $user_select_field = ['users.id', 'users.name', 'employee.designation', 'employee.emp_code', 'users.profile_image'];
        $loggedin_user_detail = \App\Employees::where('user_id', Auth::user()->id)->get();
        $this->data['user_list'] = User::join('employee', 'employee.user_id', '=', 'users.id')
                //->where('department_id', $loggedin_user_detail[0]->department_id)
                ->where('users.role', '!=', 1)
                ->where('users.id', '!=', Auth::user()->id)
                ->get($user_select_field);

        return view('admin.leave.edit_leave', $this->data);
    }

    public function update_leave(Request $request) {

        $validator_normal = Validator::make($request->all(), [
                    'subject' => 'required',
                    'description' => 'required',
                    'start_date' => 'required',
                    'end_date' => 'required',
                    'leave_category_id' => 'required',
                        //'notify_id' => 'required'
        ]);

        if ($validator_normal->fails()) {
            return redirect()->route('admin.leave')->with('error', 'Please follow validation rules.');
        }

        $leave_start_exist = Leaves::whereDate('start_date', '<=', $request->input('start_date'))->whereDate('end_date', '>=', $request->input('start_date'))->where('id', '!=', $request->input('id'))->whereIn('leave_status', [1, 2])->get()->first();
        $leave_end_exist = Leaves::whereDate('start_date', '<=', $request->input('end_date'))->whereDate('end_date', '>=', $request->input('end_date'))->where('id', '!=', $request->input('id'))->whereIn('leave_status', [1, 2])->get()->first();

        if (!empty($leave_start_exist) || !empty($leave_end_exist)) {
            return redirect()->route('admin.edit_leave', ['id' => $request->input('id')])->with('error', 'Leave is already applied on selected date.');
        }

        //fetch user data who apply leave
        $user_data = \App\User::join('employee', 'employee.user_id', '=', 'users.id')->where('users.id', Auth::user()->id)->get(['users.id', 'users.name', 'employee.reporting_user_id']);

        $notify_ids = [$user_data[0]->reporting_user_id];
        //get users with full view access to add into notify ids
        $role_list = $this->common_task->getRoleByModulePermission($this->module_id, 5);
        if ($role_list->count() > 0) {
            $user_list_by_role = User::whereIn('role', $role_list->pluck('role_id'))->get('id')->pluck('id')->toArray();
            array_merge($notify_ids, $user_list_by_role);
        }
        //print_r($notify_ids); die();
        $leaveModel = [
            'subject' => $request->input('subject'),
            'description' => $request->input('description'),
            'leave_category_id' => $request->input('leave_category_id'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
            'start_day' => $request->input('start_day'),
            'end_day' => $request->input('end_day'),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
            'assign_work_user_id' => $request->input('assign_work_user_id'),
            'assign_work_details' => $request->input('assign_work_details'),
        ];

        Leaves::where('id', $request->input('id'))->update($leaveModel);
        $leaveModel = Leaves::find($request->input('id'));

        //notify only hr about leaves apply
        $hr_list = User::where('role', config('constants.REAL_HR'))->get(['id'])->pluck('id')->toArray();

        $mail_data = [
            'start_date' => $leaveModel->start_date,
            'start_day' => config::get('constants.LEAVE_DAY.' . $leaveModel->start_day),
            'end_date' => $leaveModel->end_date,
            'end_day' => config::get('constants.LEAVE_DAY.' . $leaveModel->end_day),
            'leave_subject' => $leaveModel->subject,
            'description' => $leaveModel->description,
            'notify_id' => implode(',', $hr_list),
        ];

        //$this->common_task->applyLeaveEmail($mail_data);
        //$this->notification_task->leaveRequestNotify($notify_ids, $user_data[0]->name);
        //send email about work assign
        //get details of user assigned for work
        $assign_user = User::where('id', $request->input('assign_work_user_id'))->get(['name', 'email']);
        $assign_leave_work_mail_data = [
            'start_date' => $leaveModel->start_date,
            'start_day' => config::get('constants.LEAVE_DAY.' . $leaveModel->start_day),
            'end_date' => $leaveModel->end_date,
            'end_day' => config::get('constants.LEAVE_DAY.' . $leaveModel->end_day),
            'leave_subject' => $leaveModel->subject,
            'description' => $leaveModel->description,
            'name' => $user_data[0]->name,
            'email' => $assign_user[0]->email,
            'assign_work_details' => $leaveModel->assign_work_details,
            'assign_name' => $assign_user[0]->name
        ];
        $this->common_task->assignLeaveWorkEmail($assign_leave_work_mail_data);
        $this->notification_task->leaveRequestNotify($hr_list, $user_data[0]->name);
        return redirect()->route('admin.leave')->with('success', 'Leave successfully updated.');
    }

    public function all_leave() {
        $permission_arr = $this->common_task->getPermissionArr(Auth::user()->role, $this->module_id);
        if (empty($permission_arr) || !in_array(5, $permission_arr)) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You do not have permission to access this module.');
        }

        // this will be access with full view access 5 only
        $this->data['page_title'] = "leaves";

        $this->data['access_rule'] = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => 4])->get()->first()->access_level;
        return view('admin.leave.all_leave', $this->data);
    }

    public function get_all_leave_list(Request $request) {  //change

        $list_query = Leaves::leftjoin('leave_category','leave_category.id', '=', 'leaves.leave_category_id')
        ->leftjoin('users','users.id', '=', 'leaves.user_id')
        ->leftjoin('users AS B','B.id', '=', 'leaves.assign_work_user_id');

        $list_query->where('leaves.assign_work_status','Accepted');
        $list_query->where('leaves.leave_status','1');
        if (Auth::user()->role == config('constants.REAL_HR')) {
                //get all the first first_approval_status pending
                $list_query->where('leaves.first_approval_status','Pending');
                
          } else if (Auth::user()->role == 1) {
                $list_query->where('leaves.first_approval_status','Approved');
                $list_query->where('leaves.second_approval_status','Approved');
                $list_query->where('leaves.third_approval_status','Pending');
               
          } else {
                //user with access permission 5 only.
                $list_query->where('leaves.first_approval_status','Approved');
                $list_query->where('leaves.second_approval_status','Pending');
          }
        $list_ajax= $list_query->get(['leaves.*','users.name as user_name','B.name as reliever_name', 'leave_category.name as category_name']);

        if($request->ajax()){
            return DataTablesDataTables::of($list_ajax)->make(true);
        }
    }

    public function get_leave_list_for_all(Request $request) {   //change

        // $datatable_fields = array('users.name','users.name', 'leaves.start_date', 'leaves.end_date', 'leave_category.name','leaves.first_approval_status', 'leaves.third_approval_status', 'leaves.leave_status', 'leaves.subject', 'leaves.description');
        // $request = Input::all();
        // $conditions_array = [];

        // $join_str = [];
        // $join_str[0]['join_type'] = 'left';
        // $join_str[0]['table'] = 'leave_category';
        // $join_str[0]['join_table_id'] = 'leaves.leave_category_id';
        // $join_str[0]['from_table_id'] = 'leave_category.id';

        // $join_str[1]['join_type'] = 'left';
        // $join_str[1]['table'] = 'users';
        // $join_str[1]['join_table_id'] = 'leaves.user_id';
        // $join_str[1]['from_table_id'] = 'users.id';

        // $join_str[2]['join_type'] = 'left';
        // $join_str[2]['table'] = 'users AS B';
        // $join_str[2]['join_table_id'] = 'leaves.assign_work_user_id';
        // $join_str[2]['from_table_id'] = 'B.id';

        // $getfiled = array('users.name as user_name','B.name as reliever_name', 'leaves.id', 'leaves.start_date', 'leaves.end_date', 'leave_category.name as category_name','leaves.first_approval_status', 'leaves.second_approval_status','leaves.third_approval_status', 'leaves.leave_status', 'leaves.approver_id', 'leaves.created_at', 'leaves.updated_at', 'leaves.third_approval_status', 'leaves.subject', 'leaves.description', 'leaves.first_approval_datetime', 'leaves.second_approval_datetime', 'leaves.third_approval_datetime');
        // $table = "leaves";
        // $conditions_array = [];
        //first approval from hr and then one more senior and then final to MD sir
        //check logged in user is hr
        /* if (Auth::user()->role == config('constants.REAL_HR')) {
          //get all the first first_approval_status pending
          $conditions_array['leaves.first_approval_status'] = "Pending";
          } else if (Auth::user()->role == 1) {
          $conditions_array['leaves.first_approval_status'] = "Approved";
          $conditions_array['leaves.second_approval_status'] = "Approved";
          $conditions_array['leaves.third_approval_status'] = "Pending";
          } else {
          //user with access permission 5 only.
          $conditions_array['leaves.first_approval_status'] = "Approved";
          $conditions_array['leaves.second_approval_status'] = "Pending";
          } */
        //$conditions_array['assign_work_status'] = 'Accepted';
        // echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);

        if($request->ajax()){
            $getfiled = array('users.name as user_name','B.name as reliever_name', 'leaves.id', 'leaves.start_date', 'leaves.end_date', 'leave_category.name as category_name','leaves.first_approval_status', 'leaves.second_approval_status','leaves.third_approval_status', 'leaves.leave_status', 'leaves.approver_id', 'leaves.created_at', 'leaves.updated_at', 'leaves.third_approval_status', 'leaves.subject', 'leaves.description', 'leaves.first_approval_datetime', 'leaves.second_approval_datetime', 'leaves.third_approval_datetime');
            $list_query = Leaves::leftjoin('leave_category', 'leave_category.id', '=', 'leaves.leave_category_id')
            ->leftjoin('users','users.id', '=', 'leaves.user_id')
            ->leftjoin('users AS B','B.id', '=', 'leaves.assign_work_user_id')
            ->get($getfiled);
            
            return FacadesDataTables::of($list_query)->make(true);
        }

        die();
    }

    public function approve_leave($id, Request $request) {

        $leaveModel = Leaves::find($id);
       
        $userDetail = User::where(['id' => $leaveModel->user_id])->get(['email', 'name'])->first()->toArray();
        
        $reliever_name = User::where('id', $leaveModel->assign_work_user_id)->value('name');
    
       
        $update_arr = [];
        $final_confirm = 0;
        //check leave approval-time, first, second or third

        if ($leaveModel->first_approval_status == 'Pending') {
            $update_arr['first_approval_status'] = "Approved";
            $update_arr['first_approval_id'] = Auth::user()->id;
            $update_arr['second_approval_status'] = "Approved";
            $update_arr['second_approval_id'] = Auth::user()->id;
            $update_arr['updated_at'] = date('Y-m-d H:i:s');
            $update_arr['first_approval_datetime'] = date('Y-m-d H:i:s');
            $update_arr['second_approval_datetime'] = date('Y-m-d H:i:s');
            $update_arr['updated_ip'] = $request->ip();
            $update_arr['updated_by'] = Auth::user()->id;

            //get role list with full view permission for second approval
            /*$role_ids = $this->common_task->getRoleByModulePermission($this->module_id, 5);

            if ($role_ids->count()) {

                //get users with this roles
                $notify_users = User::whereIn('role', $role_ids->pluck('role_id')->toArray())->where('role', '!=', config('constants.SuperUser'))->where('role', '!=', config('constants.REAL_HR'))->get(['id'])->pluck('id')->toArray();

                $this->notification_task->leaveRequestActionNotify($notify_users, $userDetail['name']);
            }*/
            //send notification to super admin
            $notify_user_id = User::where('role', config('constants.SuperUser'))->get(['id'])->pluck('id')->toArray();
             $this->notification_task->leaveRequestActionNotify($notify_user_id, $userDetail['name']);
        } elseif ($leaveModel->first_approval_status == 'Approved' && $leaveModel->second_approval_status == 'Approved' && $leaveModel->third_approval_status == 'Pending') {
            $update_arr['third_approval_status'] = "Approved";
            $update_arr['third_approval_id'] = Auth::user()->id;
            $update_arr['leave_status'] = 2;
            $update_arr['updated_at'] = date('Y-m-d H:i:s');
            $update_arr['third_approval_datetime'] = date('Y-m-d H:i:s');
            $update_arr['updated_ip'] = $request->ip();
            $update_arr['updated_by'] = Auth::user()->id;
            $final_confirm = 1;
        } else {
            return redirect()->route('admin.all_leave')->with('error', 'Error Occurred. Try Again!');
        }

        if (Leaves::where('id', $id)->update($update_arr)) { // 2 = Leave Approve
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
                    $start = new DateTime($leaveModel->start_date);
                    $end = new DateTime($leaveModel->end_date);
                    $days = $start->diff($end, true)->days;

                    $sundays = intval($days / 7) + ($start->format('N') + $days % 7 >= 7);

                    //$totalDays=$totalDays-$sundays;

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
                $mailformat = str_replace("%reliever_name%", $reliever_name, $mailformat);
                $mailformat = str_replace("%description%", $leaveModel->description, $mailformat);
                $mailformat = str_replace("%approved_by%", Auth::user()->name, $mailformat);
                //$mailformat = str_replace("%approval_note%", $request_data['approval_note'], $mailformat);
                //$emailList = DB::table('users')->whereIn('id', array_merge([$leaveModel->approver_id], explode(",", $leaveModel->notify_id)))->pluck('email')->toArray();
                //notify all about leave is approved.
                
                $emailList = DB::table('users')->where('status', 'Enabled')->pluck('email')->toArray();
                Mail::to($userDetail['email'])->cc($emailList)->send(new Mails($subject, $mailformat));

                $this->notification_task->leaveApprovedNotify([$leaveModel->user_id]);
            }
            return redirect()->route('admin.all_leave')->with('success', 'Leave successfully Approved.');
        }
        return redirect()->route('admin.all_leave')->with('error', 'Error during operation. Try again!');
    }


    // for new sub module leave_reversal
    public function leave_reversal(Request $request){
        // dd("Inn_Checked_Working");
        if (Auth::user()->role != config('constants.REAL_HR') && Auth::user()->role != 1) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You do not have permission to access this module.');
        }

        // this will be access with full view access 5 only
        $this->data['page_title'] = "Reversal Leave";



        if($request->ajax()){
            $list_query = Leaves::join('leave_category','leave_category.id', '=', 'leaves.leave_category_id')
                ->leftjoin('users','users.id', '=', 'leaves.user_id')
                ->leftjoin('users AS B','B.id', '=', 'leaves.assign_work_user_id');
                if (Auth::user()->role == config('constants.REAL_HR')) {
                        $list_query->where('leaves.leave_cancellation_status','Pending');
                        $list_query->whereNull('leaves.first_reversal_approval_status');
                        
                } else if (Auth::user()->role == 1) {
                    $list_query->where('leaves.leave_cancellation_status','Pending');
                    $list_query->where('leaves.first_reversal_approval_status','Approved');
                    $list_query->whereNull('leaves.second_reversal_approval_status');
                } 
                $list_ajax= $list_query->get(['leaves.*','users.name as user_name','B.name as reliever_name', 'leaves.id', 'leaves.start_date', 'leaves.end_date', 'leave_category.name as category_name','leaves.first_approval_status', 'leaves.second_approval_status','leaves.third_approval_status', 'leaves.leave_status', 'leaves.approver_id', 'leaves.created_at', 'leaves.updated_at', 'leaves.third_approval_status', 'leaves.subject', 'leaves.description', 'leaves.first_approval_datetime', 'leaves.second_approval_datetime', 'leaves.third_approval_datetime','leaves.leave_reversal_note','leaves.first_reversal_approval_status','leaves.second_reversal_approval_status','leaves.first_reversal_approval_datetime','leaves.second_reversal_approval_datetime']);

                return FacadesDataTables::of($list_ajax)->make(true);
        }

        $this->data['access_rule'] = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => 4])->get()->first()->access_level;
        return view('admin.leave.reversal_leave', $this->data);
        
    }

    public function reverse_approve_leave($id){
        $leave = Leaves::where('id',$id)->first();
 
        $leave_user_data = User::whereId($leave->user_id)->first();
        
        if (Auth::user()->role == config('constants.REAL_HR')) {
            $leave->first_reversal_approval_status = "Approved";
            $leave->first_reversal_approval_datetime = date('Y-m-d h:i:s');
            // Send Notification
            $superadmin_list = User::where('role', 1)->get(['id'])->pluck('id')->toArray();
            $this->notification_task->leaveReversalApproveNotify($superadmin_list,$leave_user_data['name'],'HR');

        } else if (Auth::user()->role == 1) {
            $leave->second_reversal_approval_status = "Approved";
            $leave->second_reversal_approval_datetime = date('Y-m-d h:i:s');
            $leave->leave_cancellation_status = "Approved";
            $leave->leave_status = 4;
            // Send Notification
            $this->notification_task->leaveReversalApproveNotify([$leave->user_id],$leave_user_data['name'],'Super Admin');

        }
               
        if($leave->save()){
            $notify_user_id = User::where('role', config('constants.REAL_HR') || 'role', config('constants.SuperUser'))->get(['id'])->pluck('id')->toArray();
            return redirect()->route('admin.leave_reversal')->with('success', 'Leave Reversal Successfully Approved.');
        }
        
    }

    public function reject_leave($id) {

        $this->data['page_title'] = "Reject leave";
        $this->data['leave_detail'] = Leaves::where('id', $id)->get();

        if ($this->data['leave_detail']->count() == 0) {
            return redirect()->route('admin.all_leave')->with('error', 'Error Occurred. Try Again!');
        }
        return view('admin.leave.reject_leave', $this->data);
    }
    public function reverse_reject_leave($id){
        $leave = Leaves::where('id',$id)->first();

        $leave_user_data = User::whereId($leave->user_id)->first();
        if (Auth::user()->role == config('constants.REAL_HR')) {
            
            $leave->first_reversal_approval_status = "Rejected";
            $leave->leave_cancellation_status = "Rejected";
            $leave->first_reversal_approval_datetime = date('Y-m-d h:i:s');
            // Send Notification
            // $superadmin_list = User::where('role', 1)->get(['id'])->pluck('id')->toArray();
            $leave_user_data = User::whereId($leave->user_id)->first();
            $this->notification_task->leaveReversalRejectNotify([$leave->user_id],$leave_user_data['name'],'HR');
        } else if (Auth::user()->role == 1) {
            $leave->second_reversal_approval_status = "Rejected";
            $leave->leave_cancellation_status = "Rejected";
            $leave->second_reversal_approval_datetime = date('Y-m-d h:i:s');
            // Send Notification
            $this->notification_task->leaveReversalRejectNotify([$leave->user_id],$leave_user_data['name'],'Super Admin');
        }

        if($leave->save()){
            $notify_user_id = User::where('role', config('constants.REAL_HR') || 'role', config('constants.SuperUser'))->get(['id'])->pluck('id')->toArray();
            return redirect()->route('admin.leave_reversal')->with('success', 'Leave Reversal Successfully Rejected.');
        }
    }

    public function reject_update_leave(Request $request) {

        $validator_normal = Validator::make($request->all(), [
                    'reject_reason' => 'required'
        ]);

        if ($validator_normal->fails()) {
            return redirect()->route('admin.all_leave')->with('error', 'Please follow validation rules.');
        }

        $leaveModel = Leaves::find($request->input('id'));
        
        if ($leaveModel->first_approval_status == 'Pending') {

            $leaveModelUpdate = [
                'reject_reason' => $request->input('reject_reason'),
                'leave_status' => 3, // Reject Leave
                'approver_id' => Auth::user()->id,
                'updated_at' => date('Y-m-d h:i:s'),
                'updated_ip' => $request->ip(),
                'first_approval_status' => 'Rejected',
                'first_approval_datetime' => date('Y-m-d h:i:s'),
                'first_approval_id' => Auth::user()->id,
                'updated_by' => Auth::user()->id
            ];
        } else if ($leaveModel->first_approval_status == 'Approved' && $leaveModel->second_approval_status == 'Pending') {

            $leaveModelUpdate = [
                'reject_reason' => $request->input('reject_reason'),
                'leave_status' => 3, // Reject Leave
                'approver_id' => Auth::user()->id,
                'updated_at' => date('Y-m-d h:i:s'),
                'updated_ip' => $request->ip(),
                'second_approval_status' => 'Rejected',
                'second_approval_id' => Auth::user()->id,
                'second_approval_datetime' => date('Y-m-d h:i:s'),
                'updated_by' => Auth::user()->id
            ];
        } elseif ($leaveModel->first_approval_status == 'Approved' && $leaveModel->second_approval_status == 'Approved' && $leaveModel->third_approval_status == 'Pending') {

            $leaveModelUpdate = [
                'reject_reason' => $request->input('reject_reason'),
                'leave_status' => 3, // Reject Leave
                'approver_id' => Auth::user()->id,
                'updated_at' => date('Y-m-d h:i:s'),
                'updated_ip' => $request->ip(),
                'third_approval_status' => 'Rejected',
                'third_approval_id' => Auth::user()->id,
                'third_approval_datetime' => date('Y-m-d h:i:s'),
                'updated_by' => Auth::user()->id
            ];
        } else {
            return redirect()->route('admin.all_leave')->with('error', 'Error Occurred. Try Again!');
        }



        Leaves::where('id', $request->input('id'))->update($leaveModelUpdate);

        $leaveModel = Leaves::find($request->input('id'));
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
        return redirect()->route('admin.all_leave')->with('success', 'Leave successfully updated.');
    }

    public function relieving_request() {
        $this->data['page_title'] = "Reliever Leaves";
        $this->data['reliever_leave_list'] = Leaves::select('users.*', 'leaves.*', 'leaves.id as leave_id')
                ->leftJoin('users', 'users.id', '=', 'leaves.user_id')
                ->where('assign_work_user_id', Auth::user()->id)
                ->where('leaves.leave_status', '=', 1)
                ->get();
        return view('admin.leave.relieving_request', $this->data);
    }

    public function relival_change_status(Request $request, $id, $status) {
        $update_arr = [
            'assign_work_status' => $status,
            'assign_work_datetime' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => \Illuminate\Support\Facades\Auth::user()->id,
        ];

        try {

            Leaves::where('id', $id)->update($update_arr);

            $leaveModel = Leaves::find($id);
            $userDetail = User::where(['id' => $leaveModel->user_id])->get(['email', 'name'])->first()->toArray();
            $acceptUserName = User::where(['id' => $leaveModel->assign_work_user_id])->get(['email', 'name'])->first()->toArray();

            $leave_details = Leaves::join('users', 'users.id', '=', 'leaves.user_id')
                    ->where(['leaves.id' => $id, 'leaves.assign_work_user_id' => \Illuminate\Support\Facades\Auth::user()->id])
                    ->get(['leaves.id', 'users.email', 'users.name']);

            //Send Reject Applied Leave Mail
//            $emailData = Email_format::find(30)->toArray();
//            $subject = $emailData['subject'];
//            $mailformat = $emailData['emailformat'];
//            $mailformat = str_replace("%username%", $userDetail['name'], $mailformat);
//            $mailformat = str_replace("%date%", $leaveModel->start_date, $mailformat);
//            $mailformat = str_replace("%accept_user_name%", $acceptUserName['name'], $mailformat);
//            //$emailList  = DB::table('users')->whereIn('id', array_merge([$leaveModel->approver_id], explode(",", $leaveModel->notify_id)))->pluck('email')->toArray();
//            Mail::to($userDetail['email'])->send(new Mails($subject, $mailformat));

            $mail_data = [
                'to_email' => $userDetail['email'],
                'leave_person_name' => $userDetail['name'],
                'assigned_person_name' => $acceptUserName['name'],
            ];
            $this->common_task->acceptLeaveWorkEmail($mail_data);

            $hr_list = User::where('role', config('constants.REAL_HR'))->get(['id'])->pluck('id')->toArray();

            $mail_data = [
                'start_date' => $leaveModel->start_date,
                'start_day' => config::get('constants.LEAVE_DAY.' . $leaveModel->start_day),
                'end_date' => $leaveModel->end_date,
                'end_day' => config::get('constants.LEAVE_DAY.' . $leaveModel->end_day),
                'leave_subject' => $leaveModel->subject,
                'description' => $leaveModel->description,
                'notify_id' => implode(',', $hr_list),
                'user_name' => $leave_details[0]->name
            ];
            
            $this->common_task->applyLeaveEmail($mail_data);
            $this->notification_task->leaveRequestActionNotify($hr_list, $userDetail['name']);
            return redirect()->route('admin.relieving_request')->with('success', 'Relieving status successfully updated.');
        } catch (Exception $exc) {

            return redirect()->route('admin.relieving_request')->with('error', 'Error Occurred. Try Again!');
        }
    }

    // approve and reject policy by user
    public function confirm_relieving(Request $request) {
        $update_arr = [
            'assign_work_status' => 'Rejected',
            'assign_work_reject_note' => $request->input('reason_note'),
            'updated_at' => date('Y-m-d H:i:s'),
            'assign_work_datetime' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => \Illuminate\Support\Facades\Auth::user()->id,
        ];

        if (Leaves::where('id', $request->input('id'))->update($update_arr)) {

            $leaveModel = Leaves::find($request->input('id'));
            $userDetail = User::where(['id' => $leaveModel->user_id])->get(['email', 'name'])->first()->toArray();
            $acceptUserName = User::where(['id' => $leaveModel->assign_work_user_id])->get(['email', 'name'])->first()->toArray();

            $leave_details = Leaves::join('users', 'users.id', '=', 'leaves.user_id')
                    ->where(['leaves.id' => $request->input('id'), 'leaves.assign_work_user_id' => \Illuminate\Support\Facades\Auth::user()->id])
                    ->get(['leaves.*', 'users.email', 'users.name']);

            //Send Reject Applied Leave Mail
        //    $emailData = Email_format::find(30)->toArray();
        //    $subject = $emailData['subject'];
        //    $mailformat = $emailData['emailformat'];
        //    $mailformat = str_replace("%username%", $userDetail['name'], $mailformat);
        //    $mailformat = str_replace("%date%", $leaveModel->start_date, $mailformat);
        //    $mailformat = str_replace("%accept_user_name%", $acceptUserName['name'], $mailformat);
        //    //$emailList  = DB::table('users')->whereIn('id', array_merge([$leaveModel->approver_id], explode(",", $leaveModel->notify_id)))->pluck('email')->toArray();
        //    Mail::to($userDetail['email'])->send(new Mails($subject, $mailformat));
            $mail_data = [
                'to_email' => $userDetail['email'],
                'leave_person_name' => $userDetail['name'],
                'assigned_person_name' => $acceptUserName['name'],
                'assign_work_reject_note' => $request->input('reason_note')
            ];
            $this->common_task->rejectLeaveWorkEmail($mail_data);
            $this->notification_task->leaveRelieveRequestRejectedNotify([$leaveModel->user_id], $acceptUserName['name']);
            return redirect()->route('admin.relieving_request')->with('success', 'Relieving status successfully updated.');
        } else {
            return redirect()->route('admin.relieving_request')->with('success', 'Relieving status successfully updated.');
        }
    }

    public function get_today_leaves(Request $request) {
        $validator_normal = Validator::make($request->all(), [
                    'user_id' => 'required'
        ]);

        if ($validator_normal->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $today_date = date('Y-m-d');
        //$today_date = '2019-12-13';
        $today_leaves = Leaves::join('users', 'users.id', '=', 'leaves.user_id')
                ->join('users as au', 'au.id', '=', 'leaves.assign_work_user_id')
                ->where('leave_status', 2)
                ->whereDate('start_date', '<=', $today_date)
                ->whereDate('end_date', '>=', $today_date)
                ->get(['users.name', 'subject', 'au.name as assigned_username']);
        if ($today_leaves->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }
        return response()->json(['status' => true, 'msg' => 'record found', 'data' => $today_leaves]);
    }

    public function get_leave_balance() {
        $leave_balance = LeaveMaster::where(['user_id' => Auth::user()->id])->with(['leavecategory' => function($query) {
                        $query->select('id', 'name');
                    }])->get(['id', 'balance', 'leave_category_id'])->toArray();
        return response()->json(['status' => true, 'msg' => 'record found', 'data' => $leave_balance]);
    }

}
