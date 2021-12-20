<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Resignation;
use App\AssetAccess;
use Illuminate\Support\Facades\Auth;
use App\EmployeesLoans; 
use App\Lib\CommonTask;
use Illuminate\Support\Facades\Validator;
use App\Lib\NotificationTask;
use Illuminate\Support\Facades\DB;
use App\Email_format;
use App\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\Mails;

class ResignController extends Controller {

    private $page_limit = 20;
    public $data;
    public $common_task;
    private $module_id = 18;
    private $notification_task;

    public function __construct() {

        $this->super_admin = \App\User::where('role', config('constants.SuperUser'))->first();
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();
    }

    public function list_of_resign_user_list(Request $request) {
        $validator_normal = Validator::make($request->all(), [
            'user_id' => 'required',
            // 'id' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $RequestData = $request->all();
        $login_user = \App\User::where('id', $RequestData['user_id'])->first();
        //$permission_arr = $permission_arr = $this->common_task->getPermissionArr($login_user['role'], $this->module_id);
        // $or_where=[];
        // $where_condition=[];
        // if (in_array(5, $permission_arr)) {
        //     //show all resignation request
        //     if ($login_user['role'] == config('constants.SuperUser')) {
                
        //         $where_condition = [ 'resignation.first_approval_status' => 'Approved','resignation.second_approval_status' => 'Approved','resignation.status' => 'Pending','resignation.final_approval_status'=>'Pending'];
        //         // $or_where = ['resignation.status' => 'Pending','resignation.final_approval_status'=>'Pending'];
        //     } else if ($login_user['role'] == config('constants.REAL_HR')) {
        //         $where_condition = ['resignation.first_approval_status' => 'Approved', 'resignation.second_approval_status' => 'Pending','resignation.status' => 'Pending'];
        //         $or_where = ['resignation.user_id' => $RequestData['user_id']];
        //     }
        // } elseif (in_array(6, $permission_arr)) {
        //     //show only resignation request working under logged in user
        //     $where_condition = ['employee.reporting_user_id' => $RequestData['user_id'], 'resignation.first_approval_status' => 'Pending','resignation.status' => 'Pending'];
        //     $or_where = ['resignation.user_id' => $RequestData['user_id']];
        
        // } else {
        //     //show resignation request on logged in user only
        //     $where_condition = ['resignation.user_id' => $RequestData['user_id']];
        // }
        // DB::enableQueryLog();
        // $this->data['resign_list'] = Resignation::join('users', 'users.id', '=', 'resignation.user_id')
        //         ->leftJoin('employee', 'employee.user_id', '=', 'users.id')
        //         ->where($where_condition)
        //         ->orWhere($or_where)
        //         ->get(['resignation.*', 'users.name']);
        // $query = DB::getQueryLog();
        // print_r($query);exit;
        // $this->data['check_own_resign'] = Resignation::where('user_id', $RequestData['user_id'])->where('status', 'Pending')
        //                 ->get()->count();
        

        if ($login_user['role'] == config('constants.SuperUser')) {
            
            $resign_list = Resignation::join('users', 'users.id', '=', 'resignation.user_id')
                ->leftJoin('employee', 'employee.user_id', '=', 'users.id')
                ->where(['resignation.first_approval_status' => 'Approved','resignation.second_approval_status' => 'Approved','resignation.status' => 'Pending','resignation.final_approval_status'=>'Pending'])
                ->get(['resignation.*', 'users.name','users.profile_image']);

        } elseif ($login_user['role'] == config('constants.REAL_HR')) {
            
            $resign_list = Resignation::join('users', 'users.id', '=', 'resignation.user_id')
                ->leftJoin('employee', 'employee.user_id', '=', 'users.id')
                ->where(['resignation.first_approval_status' => 'Approved', 'resignation.second_approval_status' => 'Pending','resignation.status' => 'Pending'])
                ->get(['resignation.*', 'users.name','users.profile_image']);

                $articles = DB::table('users')
                ->select('users.id as users_id', 'users.name', 'employee.emp_code', 'department.dept_name')
                ->join('employee', 'users.id', '=', 'employee.user_id')
                ->join('department', 'employee.department_id', '=', 'department.id')
                ->get();

                foreach ($articles as $key => $value) {
                    $data['id'][] = $value->users_id;
                    $data['name'][] = $value->name . "-" . $value->dept_name;
                }

                $this->data['employee'] = array_combine($data['id'], $data['name']);
        
        } else {

            $resign_list = Resignation::join('users', 'users.id', '=', 'resignation.user_id')
                ->leftJoin('employee', 'employee.user_id', '=', 'users.id')
                ->where(['employee.reporting_user_id' => $RequestData['user_id'], 'resignation.first_approval_status' => 'Pending','resignation.status' => 'Pending'])
                ->get(['resignation.*', 'users.name','users.profile_image']);
        }

        foreach ($resign_list as $key => $value) {
            if ($value->profile_image) {
                $resign_list[$key]->profile_image = asset('storage/' . str_replace('public/', '', $value->profile_image));
            } else {
                $resign_list[$key]->profile_image = "";
            }

        }

        $this->data['resign_list'] = $resign_list;
        
        return response()->json(['status' => true, 'data' => $this->data]);
    }

    public function retain_resign_employee(Request $request) {    //retain 

        $validator_normal = Validator::make($request->all(), [
            'retain_details' => 'required',
            'user_id'        => 'required',
            'resign_id'      => 'required'
        ]);
        
        if ($validator_normal->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
                             
        $update_arr=[
            'final_note'=>$request_data['retain_details'],
            'status'=>'Retain',
            'updated_at'=>date('Y-m-d H:i:s'),
            'updated_ip'=>$request->ip(),
            'updated_by'=> $request_data['user_id'],
        ];
        Resignation::where('id',$request_data['resign_id'])->update($update_arr);

        $resignationModel = Resignation::where('id',$request_data['resign_id'])->get()->first();
        $userDetail       = User::select('email','name')->where('id',$resignationModel->user_id)->get()->first();
        $userDetailResign = User::select('email','name')->where('id',$request_data['user_id'])->get()->first();

        $mail_data = [];
        $mail_data['name']          = $userDetailResign->name;
        $mail_data['to_email']      = $userDetail->email;
        $mail_data['resignee_name'] = $userDetail->name;

        //send email for retain employee.
        $this->common_task->retainEmail($mail_data);

        return response()->json(['status' => true, 'msg' => "Resignation retain successfully.", 'data' => []]);
    }

    public function approve_resign_employee(Request $request) {

        $validator_normal = Validator::make($request->all(), [
            'user_id' => 'required',
            'resign_id'=>'required',
            // 'final_note'=>'required'
        ]);

        if ($validator_normal->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $login_user = \App\User::where('id', $request_data['user_id'])->first();
        
        if(isset($request_data['final_note']) && !empty($request_data['final_note'])){
            $request_data['final_note'] = $request_data['final_note'];
        }else{
            $request_data['final_note'] = "";
        }
        $fAp = "No";
        if ($login_user['role'] == 1) {
            $update_arr['final_approval_status'] = 'Approved';
            $update_arr['final_approve_user_id'] = $request_data['user_id'];
            $update_arr['status'] = 'Approved';
            $update_arr['final_note'] = $request_data['final_note'];
        } else if ($login_user['role'] == config('constants.REAL_HR')) {
            $update_arr['second_approval_status'] = 'Approved';
            $update_arr['second_approve_user_id'] = $request_data['user_id'];
            $update_arr['second_note'] = $request_data['final_note'];
            $update_arr['hand_over_user_id'] = isset($request_data['hand_over_user_id']) ? $request_data['hand_over_user_id'] : NULL;
        } else {
            $update_arr['first_approval_status'] = 'Approved';
            $update_arr['first_approve_user_id'] = $request_data['user_id'];
            

            $fAp = "Yes";
        }
        $hr_detail = user::where('status', 'Enabled')->where('role', config('constants.REAL_HR'))->get();

        $update_arr['updated_at'] = date('Y-m-d H:i:s');
        $update_arr['updated_ip'] = $request->ip();
        $update_arr['updated_by'] = $request_data['user_id'];
        
        if(Resignation::where('id', $request->get('resign_id'))->update($update_arr)) {
            
            $resignModel = Resignation::where('id', $request->get('resign_id'))->with('user')->get()->first();

            $mail_data = [];
            $mail_data['sr_name'] = $hr_detail[0]->name;
            $mail_data['resignee_name'] = $resignModel->user->name;
            $mail_data['to_email'] = user::where('status', 'Enabled')->where('role', config('constants.REAL_HR'))->pluck('email')->toArray();
            
            if ($fAp == 'Yes') {
                $this->common_task->resignationEmail($mail_data);
            }
            if($login_user['role'] == 1){
                $handoverUser = User::select('email','name')->where('id',$resignModel->hand_over_user_id)->get()->first();
                $mail_data['handover_name'] = $handoverUser->name;
                //send email to Handover of this user.
                $mail_data['to_email'] = $handoverUser->email;                
                $this->common_task->handoverResignationEmail($mail_data);
                //send email to this user.
                $mail_data['to_email'] = User::select('email')->where('id',$resignModel->user_id)->get()->first()->toArray();         
                $this->common_task->intimateResignationEmail($mail_data);
                //send email to hr
                $mail_data['to_email'] = User::where('status', 'Enabled')->where('role', config('constants.REAL_HR'))->pluck('email')->toArray();
                $this->common_task->intimateResignationEmailHR($mail_data);
            }

            
            return response()->json(['status' => true, 'msg' => "Resignation Approved successfully.", 'data' => []]);
        }else{
            return response()->json(['status' => false,'msg'=>'Error Occurred. Try Again!']);
        }
    }
}
