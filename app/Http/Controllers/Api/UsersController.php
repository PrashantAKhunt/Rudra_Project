<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Email_format;
use App\Mail\Mails;
use App\Friends;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Lib\CommonTask;
use App\Lib\Push_notification;
use PDF;
use App\Payroll;
use App\DeviceAllow;
use DateTime;
use DateInterval;

class UsersController extends Controller {

    private $page_limit = 20;
    public $common_task;
    private $module_id = 3;

    public function __construct() {
        $this->common_task = new CommonTask();
    }

    //Get user list
    public function get_user_list(Request $request) {

        $validator = Validator::make($request->all(), [
                    'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $userData = User::select('id', 'name', 'profile_image', 'role')->where('id', '!=', $request->input('user_id'))->where(['status' => 'Enabled'])->where('role', '!=', 1)->with(['role' => function($query) {
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

    /*
     * * Change password of user
     */

    public function change_password(Request $request) {

        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'password' => 'required',
                    'old_password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $user_id = $request->input('user_id');
        $old_password = $request->input('old_password');
        $password = Hash::make($request->input('password'));

        $user_detail = User::where('id', $user_id)->get(['password']);
        if ($user_detail->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        if (!Hash::check($old_password, $user_detail[0]->password)) {
            return response()->json(['status' => false, 'msg' => "Old password does not match", 'data' => [], 'error' => config('errors.general_error.code')]);
        }

        $update_user_data = [
            'password' => $password,
            'modified_by' => $user_id,
            'updated_ip' => $request->ip(),
        ];

        if (User::where('id', $user_id)->update($update_user_data)) {
            return response()->json(['status' => true, 'msg' => 'Password Changed Successfully', 'data' => []]);
        }

        return response()->json(['status' => false, 'msg' => config('errors.sql_operation.msg'), 'data' => [], 'error' => config('errors.sql_operation.code')]);
    }

    //Get user profile
    public function get_profile(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $user_id = $request->input('user_id');
        $userData = User::select('id', 'name', 'email', 'role', 'profile_image')->where('id', '=', $user_id)->with(['role' => function($query) {
                        $query->select('id', 'role_name');
                    }])->get();
        if ($userData->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        $userData[0]['profile_image'] = asset('storage/' . str_replace('public/', '', $userData[0]->profile_image));

        return response()->json(['status' => true, 'msg' => 'Get user details!', 'data' => $userData]);
    }

    //Update user profile
    public function edit_profile(Request $request) {

        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'name' => 'required',
                    'profile_image' => 'image'
        ]);

        if ($validator->fails()) {

            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $user_id = $request->input('user_id');

        $userData = User::select('id', 'name', 'profile_image')->find($user_id);

        $userData->name = $request->input('name');

        /* $username_check = User::where('username', $request->input('username'))->where('id', '!=', $user_id)->get()->count();
          if ($username_check) {
          return response()->json(['status' => false, 'msg' => 'Username already in use. Please use another username.', 'data' => [], 'error' => config('errors.general_error.code')]);
          }
          $userData->username = $request->input('username');
          if ($request->input('profession') != "") {
          $userData->profession = $request->input('profession');
          } */

        $userData->created_ip = $request->ip();
        $userData->updated_ip = $request->ip();
        $userData->modified_by = $user_id;

        //upload user profile image
        $profile_image_file = '';
        if ($request->hasFile('profile_image')) {
            $profile_image = $request->file('profile_image');
            $file_path = $profile_image->store('public/profile_image');
            if ($file_path) {
                $profile_image_file = $file_path;
            }
        }
        if (!empty($profile_image_file)) {
            $userData->profile_image = $profile_image_file;
        }

        //check user profile update or not
        if ($userData->save()) {
            if ($userData->login_type == 'Register' && $userData->profile_image) {
                $userData->profile_image = asset('storage/' . str_replace('public/', '', $userData->profile_image));
            }

            return response()->json(['status' => true, 'msg' => 'Edit user details successfully!', 'data' => $userData]);
        } else {
            return response()->json(['status' => false, 'msg' => __('global.api_erro_msg') . " edit profile. Try again!", 'data' => [], 'error' => config('errors.sql_operation.code')]);
        }
    }

    public function get_user_lists(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                        //'page_number' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $offset = ($request->input('page_number') - 1) * $this->page_limit;
        $search_query = $request->input('search_query');
        $user_list = User::where('users.status', 'Enabled')
                ->where('users.id', '!=', $request->input('user_id'))
                ->where('users.role', '1')
                ->where(function($query) use ($search_query) {
            if ($search_query != '') {
                $query->where('users.name', 'like', '%' . $search_query . '%');
                $query->orWhere('users.email', 'like', '%' . $search_query . '%');
            }
        });

        $count = $user_list->count();
        $total_pages = ceil($count / $this->page_limit);

        $user_list = $user_list->offset($offset)->limit($this->page_limit)->get(['users.id', 'users.name', 'users.email', 'users.profile_image'])->toArray();

        if (!empty($user_list)) {
            foreach ($user_list as $key => $user) {
                $user_list[$key]['profile_image'] = asset('storage/' . str_replace('public/', '', $user[$key]['profile_image']));
            }
            return response()->json(['status' => true, 'msg' => "User list available", 'data' => array_values($user_list), 'total_pages' => $total_pages]);
        } else {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }
    }

    public function delete_user(Request $request) {
        User::where('email', $request->input('email'))->delete();
        return response()->json(['status' => true, 'msg' => 'user deleted']);
    }

    public function test() {
        $notification_obj = new Push_notification();
        //Send verificationcode to user for email
        $registrationIds = array(
            "dNHuBsWeQEw:APA91bGfVwXUaKPUJorGqVTJ-8jlK86Nfb7UEBJz6NaASKeuCvbzn6DD1b_T4Y2NyHTg-cgvMicRk3dTW9wVeQSEtrGkC05w0ZCNe_C4YkBXGSTn8NccKvtsMOs1m2Qs8JPkQFofXfgP"
        );
        /* $message_data = array(
          'body' => "message Test",
          'title' => "message title",
          'vibrate' => 1,
          'sound' => 1,
          ); */

        $notification_obj->send_ios_notification($registrationIds, $message_data);
        /* $email = "jd@jd.com";
          $name = "jayram desai";
          $verification_code = "4253";
          $site_name = "Ion App";
          $emailData = Email_format::find(1)->toArray();
          $subject = $emailData['subject'];
          $mailformat = $emailData['emailformat'];
          $mail_body = str_replace("%name%", $name, str_replace("%verification_code%", $verification_code, str_replace("%site_name%", $site_name, stripslashes($mailformat))));
          echo $mail_body; */
    }

    public function reset_virtual_money(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
        ]);

        if ($validator->fails()) {

            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();

        if (User::where('id', $request_data['user_id'])->update(['virtual_bal' => config('constants.virtual_balance'), 'total_bet_won' => 0, 'total_bet_won_amount' => 0])) {

            $user_detail = User::where('id', $request_data['user_id'])->get(['virtual_bal', 'ad_time_limit']);
            \App\User_bet::where('user_id', $request_data['user_id'])->delete();

            //$win_matches=\App\User_bet::where('user_id',$request_data['user_id'])->where('is_win','Yes')->get()->count();
            //$loss_matches=\App\User_bet::where('user_id',$request_data['user_id'])->where('is_win','No')->get()->count();

            $user_detail[0]->win_matches = 0;
            $user_detail[0]->loss_matches = 0;
            return response()->json(['status' => true, 'msg' => "Virtual balance successfully reset.", 'data' => $user_detail]);
        } else {
            return response()->json(['status' => false, 'msg' => config('errors.sql_operation.msg'), 'data' => [], 'error' => config('errors.sql_operation.code')]);
        }
    }

    public function get_reset_page_data(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
        ]);

        if ($validator->fails()) {

            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $user_detail = User::SELECT('id', 'name', 'username', 'profile_image', 'login_type', 'virtual_bal', 'real_bal', 'ad_time_limit',
                        DB::raw(" (SELECT FIND_IN_SET( `virtual_bal`, ( SELECT GROUP_CONCAT( `virtual_bal` ORDER BY `virtual_bal` DESC ) FROM users ) ) AS rank FROM users WHERE `id`= '" . $request_data['user_id'] . "' ) as user_rank")
                )
                ->where('id', $request_data['user_id'])
                ->get();

        //   follower_count, following_count, 
        $win_matches = \App\User_bet::where('user_id', $request_data['user_id'])->where('is_win', 'Yes')->get()->count();
        $loss_matches = \App\User_bet::where('user_id', $request_data['user_id'])->where('is_win', 'No')->get()->count();
        $total_matches = \App\User_bet::where('user_id', $request_data['user_id'])->get()->count();
        //  $total_matches = \App\Ad_view_detail::where('user_id', $request_data['user_id'])->get()->count();

        $following_count = Friends::where('friends.from_user_id', $request_data['user_id'])
                        ->join('users', 'users.id', '=', 'friends.to_user_id')->get()->count();

        $follower_count = Friends::where('friends.to_user_id', $request_data['user_id'])
                        ->join('users', 'users.id', '=', 'friends.from_user_id')->get()->count();

        $total_view = \App\User_bet::join('ad_view_detail', 'ad_view_detail.bet_id', '=', 'user_bet.id')
                        ->where('user_bet.user_id', $request_data['user_id'])->get()->count();

        $user_detail[0]->win_matches = $win_matches;
        $user_detail[0]->loss_matches = $loss_matches;
        $user_detail[0]->total_matches = $total_matches;
        $user_detail[0]->following_count = $following_count;
        $user_detail[0]->follower_count = $follower_count;
        $user_detail[0]->total_view = $total_view;

        foreach ($user_detail as $key => $user) {
            if ($user['login_type'] == 'Register') {
                if ($user['profile_image']) {
                    $user_detail[$key]['profile_image'] = asset('storage/' . str_replace('public/', '', $user['profile_image']));
                } else {
                    $user_detail[$key]['profile_image'] = "";
                }
            }
        }
        return response()->json(['status' => true, 'msg' => "Record found.", 'data' => $user_detail]);
    }

    public function get_user_permissions(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'module_id' => 'required',
                    'role_id' => 'required'
        ]);

        if ($validator->fails()) {

            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();

        $permission_detail = \App\Role_module::where(['module_id' => $request_data['module_id'], 'role_id' => $request_data['role_id']])->get(['access_level']);

        if ($permission_detail->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        $permissions = explode(',', $permission_detail[0]->access_level);

        return response()->json(['status' => true, 'msg' => "Permissions are available", 'data' => ['permissions' => $permissions]]);
    }

    public function get_my_reportees(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
        ]);

        if ($validator->fails()) {

            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $user_details = User::where('id', $request_data['user_id'])->get(['role']);

        //get permission
        $permission = $this->common_task->getPermissionArr($user_details[0]->role, $this->module_id);

        if (in_array(5, $permission)) {
            $user_select_field = ['users.id', 'users.name', 'employee.designation', 'employee.emp_code', 'users.profile_image'];
            $user_list = User::join('employee', 'employee.user_id', '=', 'users.id')
                    ->where('users.status','Enabled')
                    ->where('users.is_user_relieved',0)
                    ->get($user_select_field);
        } elseif (in_array(6, $permission)) {
            $user_select_field = ['users.id', 'users.name', 'employee.designation', 'employee.emp_code', 'users.profile_image'];
            $user_list = User::join('employee', 'employee.user_id', '=', 'users.id')
                    ->where('reporting_user_id', $request_data['user_id'])
                    ->where('users.status','Enabled')
                    ->where('users.is_user_relieved',0)
                    ->get($user_select_field);
        } else {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        foreach ($user_list as $key => $user) {
            if ($user->profile_image) {
                $user_list[$key]->profile_image = asset('storage/' . str_replace('public/', '', $user->profile_image));
            } else {
                $user_list[$key]->profile_image = "";
            }
        }
        $response_data['my_reportees'] = $user_list;
        return response()->json(['status' => true, 'msg' => 'Record found', 'data' => $response_data]);
    }

    public function get_my_team(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
        ]);

        if ($validator->fails()) {

            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $user_details = User::leftJoin('employee', 'employee.user_id', '=', 'users.id')->where('users.id', $request_data['user_id'])->get(['users.role', 'employee.department_id']);

        //get permission
        $permission = $this->common_task->getPermissionArr($user_details[0]->role, $this->module_id);

        if (in_array(5, $permission)) {
            $user_select_field = ['users.id', 'users.name', 'employee.designation', 'employee.emp_code', 'users.profile_image'];
            $user_list = User::join('employee', 'employee.user_id', '=', 'users.id')
                    ->where('users.role', '!=', 1)
                    ->where('users.status','Enabled')
                    ->where('users.is_user_relieved',0)
                    ->get($user_select_field);
        } elseif (in_array(6, $permission)) {
            $user_select_field = ['users.id', 'users.name', 'employee.designation', 'employee.emp_code', 'users.profile_image'];
            $user_list = User::join('employee', 'employee.user_id', '=', 'users.id')
                    ->where('department_id', $user_details[0]->department_id)
                    ->where('users.role', '!=', 1)
                    ->where('users.status','Enabled')
                    ->where('users.is_user_relieved',0)
                    ->get($user_select_field);
        } else {
            $user_select_field = ['users.id', 'users.name', 'employee.designation', 'employee.emp_code', 'users.profile_image'];
            $user_list = User::join('employee', 'employee.user_id', '=', 'users.id')
                    ->where('department_id', $user_details[0]->department_id)
                    ->where('users.role', '!=', 1)
                    ->where('users.status','Enabled')
                    ->where('users.is_user_relieved',0)
                    ->get($user_select_field);
        }

        foreach ($user_list as $key => $user) {
            if ($user->profile_image) {
                $user_list[$key]->profile_image = asset('storage/' . str_replace('public/', '', $user->profile_image));
            } else {
                $user_list[$key]->profile_image = "";
            }
        }
        $response_data['my_reportees'] = $user_list;
        return response()->json(['status' => true, 'msg' => 'Record found', 'data' => $response_data]);
    }

    public function get_emp_details(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'emp_id' => 'required'
        ]);

        if ($validator->fails()) {

            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];
        $user_select_field = [
            'users.id', 'users.name', 'users.email', 'users.profile_image', 'users.status', 'users.role', 'department.dept_name',
            'employee.emp_code', 'employee.designation', 'employee.joining_date', 'employee.skype', 'employee.contact_number',
            'employee.emg_contact_number', 'employee.residential_address', 'employee.permanent_address', 'company.company_name',
            'employee.gender', 'employee.birth_date', 'employee.marital_status', 'employee.marriage_date', 'employee.blood_group',
            'employee.physically_handicapped', 'employee.handicap_note', 'u.name as senior_name', 'e.designation as senior_designation'
        ];
        $user_detail = User::leftJoin('employee', 'employee.user_id', '=', 'users.id')
                        ->with('employee_education')
                        ->join('department', 'department.id', '=', 'employee.department_id')
                        ->join('company', 'company.id', '=', 'employee.company_id')
                        ->leftJoin('users as u', 'u.id', '=', 'employee.reporting_user_id')
                        ->leftJoin('employee as e', 'e.user_id', '=', 'u.id')
                        ->where('users.id', $request_data['emp_id'])->get($user_select_field);

        if ($user_detail->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }
        //set profile image link
        if ($user_detail[0]->profile_image) {
            $user_detail[0]->profile_image = asset('storage/' . str_replace('public/', "", $user_detail[0]->profile_image));
        } else {
            $user_detail[0]->profile_image = "";
        }

        //get employee all experience
        $emp_experience = \App\Employee_experience::where('user_id', $request_data['emp_id'])->get();

        $total_days = 0;
        
        foreach ($emp_experience as $experience) {
            if ($experience->exp_start_time_period != "1970-01-01" && $experience->exp_end_time_period != "1970-01-01") {
                $earlier = new DateTime($experience->exp_start_time_period);
                $later = new DateTime($experience->exp_end_time_period);

                $diff = $later->diff($earlier)->format("%a");
                $total_days += $diff;
            }
        }
       
        if ($user_detail[0]->joining_date && $user_detail[0]->joining_date != "1970-01-01") {
            $join_date = new DateTime($user_detail[0]->joining_date);
            $current_date = new DateTime(date('Y-m-d'));

            $final_diff = $current_date->diff($join_date)->format("%a");
            $total_days += $final_diff;
        }
        $start_date = new DateTime();
        $end_date = (new $start_date)->add(new DateInterval("P{$total_days}D"));
        $dd = date_diff($start_date, $end_date);
        //echo $dd->y . " years " . $dd->m . " months " . $dd->d . " days";

        $user_detail[0]->total_experience = $dd->y . ' Year ' . $dd->m . ' Months';
        $response_data['emp_details'] = $user_detail;

        return response()->json(['status' => true, 'msg' => "Record found.", 'data' => $response_data]);
    }

    public function get_department(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
        ]);

        if ($validator->fails()) {

            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        //get user department details
        $user_department = \App\Employees::where('user_id', $request_data['user_id'])->get(['department_id']);

        //get user list as per department
        $user_list = User::join('employee', 'employee.user_id', '=', 'users.id')
                ->where(function($query) use ($user_department) {
                    if ($user_department->count() > 0) {
                        $query->where('employee.department_id', $user_department[0]->department_id);
                    }
                })
                ->get(['users.id', 'users.name', 'employee.emp_code', 'users.profile_image']);

        if ($user_list->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        //set profile image path
        foreach ($user_list as $key => $user) {
            if ($user->profile_image) {
                $user_list[$key]->profile_image = asset('storage/' . str_replace('public/', '', $user->profile_image));
            } else {
                $user_list[$key]->profile_image = "";
            }
        }

        $response_data['department_emp_list'] = $user_list;

        return response()->json(['status' => true, 'msg' => 'Record found', 'data' => $response_data]);
    }

    public function get_salary_slip(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'month' => 'required',
                    'year' => 'required'
        ]);

        if ($validator->fails()) {

            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];
        $this->data['display_month'] = $request_data['month'];
        $this->data['display_year'] = $request_data['year'];
        $payrollData = Payroll::where('year', $request_data['year'])->where('main_approval_status', 'Approved')->where('month', $request_data['month'])->where('user_id', $request_data['user_id'])->with(['user' => function($query) {
                        return $query->with(['employee'=>function($query1){
                                return $query1->with('department');
                            }, 'employee_bank']);
                    }])->where('is_locked', 'YES')->get()->first();

        if (empty($payrollData)) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        $this->data['data'] = $payrollData;
        $date_m_y = $request->input('year')."-".$request->input('month');
        $this->data['display_month_year'] = date('M Y', strtotime($date_m_y));
        //dd($payrollData);
        //return view('admin.attendance.pdfview', $this->data);
        if (!$payrollData->salary_slip_file) {
            $pdf = PDF::loadView('admin.attendance.pdfview', $this->data)->setPaper('A3', 'landscape');
            Storage::put('public/salary_slip/' . $payrollData->user_id . '-' . $request_data['month'] . '-' . $request_data['year'] . '.pdf', $pdf->output());
            $file_path = 'public/salary_slip/' . $payrollData->user_id . '-' . $request_data['month'] . '-' . $request_data['year'] . '.pdf';

            Payroll::where('id', $payrollData->id)->update(['salary_slip_file' => $file_path]);
            $full_path = asset('storage/' . str_replace('public/', '', $file_path));
            return response()->json(['status' => true, 'msg' => 'Record found.', 'data' => ['file_path' => $full_path]]);
        } else {
            $full_path = asset('storage/' . str_replace('public/', '', $payrollData->salary_slip_file));
            return response()->json(['status' => true, 'msg' => 'Record found.', 'data' => ['file_path' => $full_path]]);
        }
    }

    public function check_imei(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'imei_number' => 'required',
        ]);

        if ($validator->fails()) {

            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $exits = DeviceAllow::where('user_id', $request_data['user_id'])->where('imei_number', $request_data['imei_number'])->get();

        if ($exits->count() > 0) {
            if ($exits[0]->status == "Approved") {
                return response()->json(['status' => true, 'msg' => 'Record found.', 'data' => []]);
            } else {
                return response()->json(['status' => false, 'msg' => "", 'data' => []]);
            }
        } else {
            $device_data = [
                'user_id' => $request_data['user_id'],
                'imei_number' => $request_data['imei_number'],
                'model_name' => ($request_data['model_name']) ? $request_data['model_name'] : NULL,
                'created_ip' => $request->ip(),
                'updated_ip' => $request->ip(),
            ];
            DeviceAllow::insert($device_data);
            return response()->json(['status' => false, 'msg' => "", 'data' => []]);
        }
    }
}
