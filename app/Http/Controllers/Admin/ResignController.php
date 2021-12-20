<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Resignation;
use App\AssetAccess;
use App\EmployeesLoans;
use Illuminate\Support\Facades\Auth;
use App\Lib\CommonTask;
use Illuminate\Support\Facades\Validator;
use App\Lib\NotificationTask;
use Illuminate\Support\Facades\DB;
use App\Asset;
use App\Email_format;
use App\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\Mails;
use App\AttendanceMaster;

class ResignController extends Controller
{

    public $data;
    public $common_task;
    private $module_id = 18;
    private $notification_task;

    public function __construct()
    {
        $this->data['module_title'] = "Resignation";
        $this->data['module_link'] = "admin.resign";
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();

        $this->super_admin = \App\User::where('role', 1)->first();
    }

    public function index()
    {    //show detail
        $this->data['page_title'] = "Resignation";

        //fetch view permissions of users
        $this->data['permission_arr'] = $permission_arr = $this->common_task->getPermissionArr(Auth::user()->role, $this->module_id);
        $or_where = [];
        $where_condition = [];
        if (in_array(5, $permission_arr)) {
            //show all resignation request
            if (Auth::user()->role == config('constants.SuperUser')) {
                $where_condition = ['resignation.second_approval_status' => 'Approved', 'resignation.first_approval_status' => 'Approved'];
            } else if (Auth::user()->role == config('constants.REAL_HR')) {
                $where_condition = ['resignation.first_approval_status' => 'Approved'];
                $or_where = ['resignation.user_id' => Auth::user()->id];
            }
        } elseif (in_array(6, $permission_arr)) {
            //show only resignation request working under logged in user
            $where_condition = ['employee.reporting_user_id' => Auth::user()->id, 'resignation.first_approval_status' => 'Pending'];
            $or_where = ['resignation.user_id' => Auth::user()->id];
        } else {
            //show resignation request on logged in user only
            $where_condition = ['resignation.user_id' => Auth::user()->id];
        }

        $this->data['resign_list'] = Resignation::join('users', 'users.id', '=', 'resignation.user_id')
            ->leftJoin('employee', 'employee.user_id', '=', 'users.id')
            ->where($where_condition)
            ->orWhere($or_where)
            ->get(['resignation.*', 'users.name']);

        $this->data['check_own_resign'] = Resignation::where('user_id', Auth::user()->id)
            ->where('status', 'Pending')
            ->get()->count();
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
        return view('admin.resign.index', $this->data);
    }

    public function add_resign()
    {


        /* $days = [83 => 0,
            73 => 15,
            80 => 23,
            85 => 0,
            102 => 31,
            96 => 31,
            66 => 4,
            68 => 31,
            74 => 31,
            90 => 20,
            77 => 31,
            99 => 28,
            79 => 31,
            72 => 31,
            78 => 12,
            70 => 31,
            98 => 27,
            94 => 26,
            92 => 31,
            93 => 31,
            103 => 31,
            101 => 27];
        if (isset($days[Auth::user()->id])) {
            $may_days = $days[Auth::user()->id];
        } else {
            $may_days = 0;
        }

        $march_days = 10;
        if (Auth::user()->id == 83 || Auth::user()->id == 85) {
            $april_days = 0;
        } else {
            $april_days = 30;
        }

        $total_lockdown_days = $march_days + $april_days + $may_days;

        //check covid situation start
        $covid_start_date = date('Y-m-d', strtotime('2020-03-20'));
        $covid_end_date = date('Y-m-d');


        //get total attendance between this 2 dates of logged in user
        $total_attend = AttendanceMaster::whereDate('date', '>=', $covid_start_date)
                        ->whereDate('date', '<=', $covid_end_date)
                        ->where('user_id', Auth::user()->id)
                        ->where('availability_status', 1)
                        ->get()->count();

        $actual_attend_days_after_covid = $total_attend - $march_days;

        if ($actual_attend_days_after_covid < $total_lockdown_days) {
            $remain_day = $total_lockdown_days - $actual_attend_days_after_covid;
            return redirect()->route('admin.dashboard')->with('error', "You have still {$remain_day} days pending to attend due to covid19 situation, only after that you can submit a resign. Please contact HR department for more information.");
        }*/

        //check covid situation end

        $this->data['page_title'] = "Submit Resignation";
        return view('admin.resign.add_resign', $this->data);
    }

    public function submit_resign(Request $request)
    {    //change

        $validator_normal = Validator::make($request->all(), [
            'reason' => 'required',
            'resign_details' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_resign')->with('error', 'Please follow validation rules.');
        }
        $request_data = $request->all();

        //get data of senior
        $sr_resignee_person_detail = \App\Employees::join('users', 'users.id', '=', 'employee.reporting_user_id')
            ->leftJoin('users as u', 'u.id', '=', 'employee.user_id')
            ->where('employee.user_id', Auth::user()->id)
            ->get(['users.name', 'users.email', 'u.name as user_name', 'users.id', 'employee.reporting_user_id']);

        if ($sr_resignee_person_detail->count() == 0) {
            //if there is no senior available then resign is not possible
            return redirect()->route('admin.resign')->with('error', 'Error Occurred. Try Again!');
        }

        $resign_arr = [
            'user_id' => Auth::user()->id,
            'resign_details' => $request_data['resign_details'],
            'reason' => $request_data['reason'],
            'expected_relieving_date' => $request_data['expected_relieving_date'],
            'status' => 'Pending',
            'created_at' => date('Y-m-d H:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id,
        ];

        $check_reporting_user_role = User::where('id', $sr_resignee_person_detail[0]->reporting_user_id)->get();

        $hr_detail = user::where('status', 'Enabled')->where('role', config('constants.REAL_HR'))->get();

        if ($check_reporting_user_role[0]->role == config('constants.SuperUser')) {
            $resign_arr['first_approval_status'] = "Approved";
            $resign_arr['first_approve_user_id'] = $check_reporting_user_role[0]->id;

            $mail_data = [];
            $mail_data['sr_name'] = $hr_detail[0]->name;
            $mail_data['resignee_name'] = $sr_resignee_person_detail[0]->user_name;
            $mail_data['to_email'] = user::where('status', 'Enabled')->where('role', config('constants.REAL_HR'))->pluck('email')->toArray();
            //send email to hr of this user for resign approved.

            $hr_ids = user::where('status', 'Enabled')->where('role', config('constants.REAL_HR'))->pluck('id')->toArray();
            $this->notification_task->resignNotify($hr_ids, $sr_resignee_person_detail[0]->user_name);

            $this->common_task->resignationEmail($mail_data);
        } else if ($check_reporting_user_role[0]->role == config('constants.REAL_HR')) {
            $resign_arr['first_approval_status'] = "Approved";
            $resign_arr['first_approve_user_id'] = $check_reporting_user_role[0]->id;

            $mail_data = [];
            $mail_data['sr_name'] = $hr_detail[0]->name;
            $mail_data['resignee_name'] = $sr_resignee_person_detail[0]->user_name;
            $mail_data['to_email'] = user::where('status', 'Enabled')->where('role', config('constants.REAL_HR'))->pluck('email')->toArray();
            //send email to hr of this user for resign approved.

            $this->notification_task->resignNotify([$check_reporting_user_role[0]->id], $sr_resignee_person_detail[0]->user_name);
            $this->common_task->resignationEmail($mail_data);
        } else {
            $mail_data = [];
            $mail_data['sr_name'] = $sr_resignee_person_detail[0]->name;
            $mail_data['to_email'] = $sr_resignee_person_detail[0]->email;
            $mail_data['resignee_name'] = $sr_resignee_person_detail[0]->user_name;

            //send email and notification to senior of this user to take action on this.
            $this->notification_task->resignNotify([$check_reporting_user_role[0]->id], $sr_resignee_person_detail[0]->user_name);
            $this->common_task->resignationEmail($mail_data);
        }
        //$this->notification_task->resignNotify([$check_reporting_user_role[0]->id], $sr_resignee_person_detail[0]->user_name);
        Resignation::insert($resign_arr);


        return redirect()->route('admin.resign')->with('success', 'Resign successfully submitted.');
    }

    public function edit_resign($id)
    {
        /*
        $days = [83 => 0,
            73 => 15,
            80 => 23,
            85 => 0,
            102 => 31,
            96 => 31,
            66 => 4,
            68 => 31,
            74 => 31,
            90 => 20,
            77 => 31,
            99 => 28,
            79 => 31,
            72 => 31,
            78 => 12,
            70 => 31,
            98 => 27,
            94 => 26,
            92 => 31,
            93 => 31,
            103 => 31,
            101 => 27];
        if (isset($days[Auth::user()->id])) {
            $may_days = $days[Auth::user()->id];
        } else {
            $may_days = 0;
        }

        $march_days = 10;
        if (Auth::user()->id == 83 || Auth::user()->id == 85) {
            $april_days = 0;
        } else {
            $april_days = 30;
        }

        $total_lockdown_days = $march_days + $april_days + $may_days;

        //check covid situation start
        $covid_start_date = date('Y-m-d', strtotime('2020-03-20'));
        $covid_end_date = date('Y-m-d');


        //get total attendance between this 2 dates of logged in user
        $total_attend = AttendanceMaster::whereDate('date', '>=', $covid_start_date)
                        ->whereDate('date', '<=', $covid_end_date)
                        ->where('user_id', Auth::user()->id)
                        ->where('availability_status', 1)
                        ->get()->count();

        $actual_attend_days_after_covid = $total_attend - $march_days;

        if ($actual_attend_days_after_covid < $total_lockdown_days) {
            $remain_day = $total_lockdown_days - $actual_attend_days_after_covid;
            return redirect()->route('admin.dashboard')->with('error', "You have still {$remain_day} days pending to attend due to covid19 situation, only after that you can submit a resign. Please contact HR department for more information.");
        }
*/
        //check covid situation end

        $this->data['page_title'] = "Edit Resignation";

        //get resignation data
        $resignation_data = Resignation::where('id', $id)->where('user_id', Auth::user()->id)->get();

        if ($resignation_data->count() == 0) {
            return redirect()->route('admin.resign')->with('error', 'Error Occurred. Try Again!');
        }

        if ($resignation_data[0]->first_approval_status != 'Pending' || $resignation_data[0]->second_approval_status != 'Pending' || $resignation_data[0]->final_approval_status != 'Pending') {
            return redirect()->route('admin.resign')->with('error', 'Process on your resignation is started so you can not now edit it. Please contact your administration if any concern.');
        }
        $this->data['resignation_data'] = $resignation_data;

        return view('admin.resign.edit_resign', $this->data);
    }

    public function update_resign(Request $request)
    {
        $validator_normal = Validator::make($request->all(), [
            'reason' => 'required',
            'resign_details' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_resign')->with('error', 'Please follow validation rules.');
        }
        $request_data = $request->all();

        $resignation_data = Resignation::where('id', $request_data['id'])->where('user_id', Auth::user()->id)->get();

        if ($resignation_data->count() == 0) {
            return redirect()->route('admin.resign')->with('error', 'Error Occurred. Try Again!');
        }

        if ($resignation_data[0]->first_approval_status != 'Pending' || $resignation_data[0]->second_approval_status != 'Pending' || $resignation_data[0]->final_approval_status != 'Pending') {
            return redirect()->route('admin.resign')->with('error', 'Process on your resignation is started so you can not now edit it. Please contact your administration if any concern.');
        }

        $update_arr = [
            'resign_details' => $request_data['resign_details'],
            'reason' => $request_data['reason'],
            'expected_relieving_date' => $request_data['expected_relieving_date'],
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id
        ];
        Resignation::where('id', $request_data['id'])->update($update_arr);
        return redirect()->route('admin.resign')->with('success', 'Resignation details updated successfully.');
    }

    public function get_resign_detail(Request $request)
    {   //check asset , loan , resign detail
        $validator_normal = Validator::make($request->all(), ['id' => 'required']);

        if ($validator_normal->fails()) {
            return response()->json(['status' => false]);
        }

        $resign_list = Resignation::join('users', 'users.id', '=', 'resignation.user_id')
            ->leftJoin('employee', 'employee.user_id', '=', 'users.id')
            ->where('resignation.id', $request->get('id'))
            ->get(['resignation.*', 'users.name'])->first();

        $assetAccess = AssetAccess::where('asset_access_user_id', $resign_list->user_id)->where('is_allocate', 1)->with('asset')->get()->toArray();

        $asset = [];
        foreach ($assetAccess as $key => $value) {
            $asset['name'] = $value['asset']['name'];
            $asset['date'] = $value['asset_access_date'];
        }

        $empLoan = EmployeesLoans::where('user_id', $resign_list->user_id)->where('loan_status', 'Approved')->where('status', 'Enabled')->get('loan_amount', 'completed_loan_amount', 'loan_terms', 'completed_loan_terms', 'user_id');
        $loan = [];
        foreach ($empLoan as $key => $value) {
            if ($value->loan_amount > $value->completed_loan_amount && $value->loan_terms > $value->completed_loan_terms) {
                $loan[$key]['amount'] = $value->loan_amount - $value->completed_loan_amount;
                $loan[$key]['term'] = $value->loan_terms - $value->completed_loan_terms;
            }
        }

        $data['resign_list'] = $resign_list;
        $data['loan'] = $loan;
        $data['asset'] = $asset;

        if ($resign_list) {
            return response()->json(['status' => true, 'data' => $data]);
        } else {
            return response()->json(['status' => false]);
        }
    }

    public function relieving_date(Request $request)
    {    //set hr date
        $validator_normal = Validator::make($request->all(), [
            'relieve_date' => 'required',
            'resign_id' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.resign')->with('error', 'Please follow validation rules.');
        }
        $request_data = $request->all();

        $update_arr = [
            'actual_relieving_date' => $request_data['relieve_date'],
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id
        ];
        Resignation::where('id', $request_data['resign_id'])->update($update_arr);

        $resignationModel = Resignation::where('id', $request_data['resign_id'])->get()->first();
        $userDetail = User::select('email', 'name')->where('id', $resignationModel->user_id)->get()->first();

        $mail_data = [];
        $mail_data['name'] = Auth::user()->name;
        $mail_data['to_email'] = $userDetail->email;
        $mail_data['resignee_name'] = $userDetail->name;
        $mail_data['date'] = $resignationModel->actual_relieving_date;

        //send email for relieving date.
        $this->common_task->relievingEmail($mail_data);

        return redirect()->route('admin.resign')->with('success', 'Relieving date set successfully.');
    }

    public function relieving_letter(Request $request)
    {   //set hr letter
        $validator_normal = Validator::make($request->all(), [
            'relieve_letter' => 'required',
            'resign_id' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.resign')->with('error', 'Please follow validation rules.');
        }
        $request_data = $request->all();

        //upload user relieve letter
        $relieve_letter_file = '';
        if ($request->hasFile('relieve_letter')) {
            $relieve_letter = $request->file('relieve_letter');
            //$file_path = $relieve_letter->store('public/relieve_letter');
            $original_file_name = explode('.', $relieve_letter->getClientOriginalName());

            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);


            $file_path = $relieve_letter->storeAs('public/relieve_letter', $new_file_name);

            if ($file_path) {
                $relieve_letter_file = $file_path;
            }
        }

        if (!empty($relieve_letter_file)) {
            $update_arr = [
                'relieving_letter' => $relieve_letter_file,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id
            ];

            Resignation::where('id', $request_data['resign_id'])->update($update_arr);

            return redirect()->route('admin.resign')->with('success', 'Relieving letter uploaded successfully.');
        } else {
            return redirect()->route('admin.resign')->with('error', 'Error Occurred. Try Again!');
        }
    }

    public function retain_resign(Request $request)
    {    //retain flow
        $validator_normal = Validator::make($request->all(), [
            'retain_details' => 'required',
            'resign_id' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.resign')->with('error', 'Please follow validation rules.');
        }
        $request_data = $request->all();

        $update_arr = [
            'final_note' => $request_data['retain_details'],
            'status' => 'Retain',
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id
        ];
        Resignation::where('id', $request_data['resign_id'])->update($update_arr);

        $resignationModel = Resignation::where('id', $request_data['resign_id'])->get()->first();
        $userDetail = User::select('email', 'name')->where('id', $resignationModel->user_id)->get()->first();

        $mail_data = [];
        $mail_data['name'] = Auth::user()->name;
        $mail_data['to_email'] = $userDetail->email;
        $mail_data['resignee_name'] = $userDetail->name;

        //send email for retain employee.
        $this->common_task->retainEmail($mail_data);

        return redirect()->route('admin.resign')->with('success', 'Resignation Retain successfully.');
    }

    public function revoked_resign(Request $request)
    {   //user revoke
        $validator_normal = Validator::make($request->all(), [
            'revoked_details' => 'required',
            'revoke_resign_id' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.resign')->with('error', 'Please follow validation rules.');
        }
        $request_data = $request->all();

        $resignation_data = Resignation::where('id', $request_data['revoke_resign_id'])->where('first_approval_status', 'Pending')->where('user_id', Auth::user()->id)->get()->first();

        if (empty($resignation_data)) {
            return redirect()->route('admin.resign')->with('error', 'Error Occurred. Try Again!');
        }

        $update_arr = [
            'revoked_details' => $request_data['revoked_details'],
            'status' => 'Revoked',
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id
        ];
        Resignation::where('id', $request_data['revoke_resign_id'])->update($update_arr);

        //get data of senior
        $sr_detail = \App\Employees::join('users', 'users.id', '=', 'employee.reporting_user_id')
            ->leftJoin('users as u', 'u.id', '=', 'employee.user_id')
            ->where('employee.user_id', Auth::user()->id)
            ->get(['users.name', 'users.email', 'u.name as user_name', 'users.id'])->first();

        $mail_data = [];
        $mail_data['sr_name'] = $sr_detail->name;
        $mail_data['to_email'] = $sr_detail->email;
        $mail_data['resignee_name'] = $sr_detail->user_name;

        //send email and notification to senior of this user.
        $this->common_task->revokeResignationEmail($mail_data);
        $this->notification_task->revokeNotify([$sr_detail->id], $sr_detail->user_name);

        return redirect()->route('admin.resign')->with('success', 'Resignation Revoked successfully.');
    }

    public function approve_resign($id, Request $request)
    {

        $fAp = "No";
        if (Auth::user()->role == 1) {
            $update_arr['final_approval_status'] = 'Approved';
            $update_arr['final_approve_user_id'] = Auth::user()->id;
            $update_arr['status'] = 'Approved';
            $update_arr['notice_period_days'] = 60;
        } else if (Auth::user()->role == config('constants.REAL_HR')) {
            $update_arr['second_approval_status'] = 'Approved';
            $update_arr['second_approve_user_id'] = Auth::user()->id;
        } else {
            $update_arr['first_approval_status'] = 'Approved';
            $update_arr['first_approve_user_id'] = Auth::user()->id;
            $fAp = "Yes";
        }
        $hr_detail = user::where('status', 'Enabled')->where('role', config('constants.REAL_HR'))->get();
        $update_arr['updated_at'] = date('Y-m-d H:i:s');
        $update_arr['updated_ip'] = $request->ip();
        $update_arr['updated_by'] = Auth::user()->id;

        if (Resignation::where('id', $id)->update($update_arr)) {

            $resignModel = Resignation::where('id', $id)->with('user')->get()->first();

            $mail_data = [];
            $mail_data['sr_name'] = $hr_detail[0]->name;
            $mail_data['resignee_name'] = $resignModel->user->name;
            $mail_data['to_email'] = user::where('status', 'Enabled')->where('role', config('constants.REAL_HR'))->pluck('email')->toArray();
            //send email to hr of this user for resign approved.
            //jayram desai
            if ($fAp == 'Yes') {
                $this->common_task->resignationEmail($mail_data);
            }

            if (Auth::user()->role == 1) {
                $handoverUser = User::select('email', 'name')->where('id', $resignModel->hand_over_user_id)->get()->first();
                $mail_data['handover_name'] = $handoverUser->name;
                //send email to Handover of this user.
                $mail_data['to_email'] = $handoverUser->email;
                $this->common_task->handoverResignationEmail($mail_data);
                //send email to this user.
                $mail_data['to_email'] = User::select('email')->where('id', $resignModel->user_id)->get()->first()->toArray();
                $this->common_task->intimateResignationEmail($mail_data);
                //send email to hr
                $mail_data['to_email'] = User::where('status', 'Enabled')->where('role', config('constants.REAL_HR'))->pluck('email')->toArray();
                $this->common_task->intimateResignationEmailHR($mail_data);
            }

            return redirect()->route('admin.resign')->with('success', 'Resignation Approved successfully.');
        } else {
            return redirect()->route('admin.resign')->with('error', 'Error Occurred. Try Again!');
        }
    }

    public function superadmin_confirm_resign(Request $request)
    {  //final approval for SuperUser
        $validator_normal = Validator::make($request->all(), [
            'super_user_resign_id' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return response()->json(['status' => false]);
        }

        $request_data = $request->all();

        $resignation_data = Resignation::where('id', $request_data['super_user_resign_id'])->get();
        $resignModel = Resignation::where('id', $request_data['super_user_resign_id'])->with('user')->get()->first();
        $update_arr['final_approval_status'] = 'Approved';
        $update_arr['final_approve_user_id'] = Auth::user()->id;
        $update_arr['status'] = 'Approved';
        $update_arr['notice_period_days'] = 60;
        $update_arr['final_note'] = $request->input('super_admin_note');
        Resignation::where('id', $request_data['super_user_resign_id'])->update($update_arr);
        $handoverUser = User::select('email', 'name')->where('id', $resignation_data[0]->hand_over_user_id)->get()->first();
        $hr_detail = user::where('status', 'Enabled')->where('role', config('constants.REAL_HR'))->get();
        $mail_data = [];
        $mail_data['sr_name'] = $hr_detail[0]->name;
        $mail_data['resignee_name'] = $resignModel->user->name;

        $mail_data['handover_name'] = $handoverUser->name;
        //send email to Handover of this user.
        $mail_data['to_email'] = $handoverUser->email;
        $this->common_task->handoverResignationEmail($mail_data);
        //send email to this user.
        $mail_data['to_email'] = User::select('email')->where('id', $resignation_data[0]->user_id)->get()->first()->toArray();
        $this->common_task->intimateResignationEmail($mail_data);
        //send email to hr
        $mail_data['to_email'] = User::where('status', 'Enabled')->where('role', config('constants.REAL_HR'))->pluck('email')->toArray();
        //$this->common_task->intimateResignationEmailHR($mail_data);

        return redirect()->route('admin.resign')->with('success', 'Resignation Approved successfully.');
    }

    /*
     * * accept and resign of user
     */

    public function confirm_resign(Request $request)
    {        //change
        $validator_normal = Validator::make($request->all(), [
            'resign_id' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return response()->json(['status' => false]);
        }

        $request_data = $request->all();

        $resignation_data = Resignation::where('id', $request_data['resign_id'])->get();

        if ($resignation_data->count() == 0) {
            return response()->json(['status' => false]);
        }

        $update_arr = [
            'second_approval_status' => 'Approved',
            'second_approve_user_id' => Auth::user()->id,
            'second_note' => $request_data['note'],
            'hand_over_user_id' => !empty($request_data['hand_over_user_id']) ? $request_data['hand_over_user_id'] : NULL,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id,
            'salary' => $request_data['salary'],
            'salary_note' => $request_data['salary_note'],
            'leave' => $request_data['leave'],
            'leave_note' => $request_data['leave_note'],
            'advance' => $request_data['advance'],
            'advance_note' => $request_data['advance_note'],
            'list_assets' => $request_data['list_assets'],
            'list_assets_note' => $request_data['list_assets_note'],
            'list_files' => $request_data['list_files'],
            'list_files_note' => $request_data['list_files_note'],
            'eligibility_policy' => $request_data['eligibility_policy'],
            'eligibility_policy_note' => $request_data['eligibility_policy_note'],
        ];

        if (Resignation::where('id', $request_data['resign_id'])->update($update_arr)) {

            $resign_user_data = DB::table('resignation')
                ->select('users.name as hand_over_user', 'gg.name as resign_user')
                ->join('users', 'resignation.hand_over_user_id', '=', 'users.id')
                ->join('users as gg', 'resignation.user_id', '=', 'gg.id')
                ->where(['resignation.id' => $request_data['resign_id']])
                ->get();
            $HandOverUserName = $resign_user_data[0]->resign_user;
            $name = $resign_user_data[0]->hand_over_user;

            $assign_user_email = User::select('email')->where('id', $request_data['hand_over_user_id'])->get()->first()->toArray();

            $handOver_mail = [];
            $handOver_mail['username'] = $name;
            $handOver_mail['resign_user_name'] = $HandOverUserName;
            $handOver_mail['to_email'] = $assign_user_email;

            $this->common_task->handoverUserEmail($handOver_mail);

            //$hrMail = user::where('status', 'Enabled')->where('role', config('constants.REAL_HR'))->pluck('email')->toArray();
            //Send mail to all user

            $super_user_detail = user::where('status', 'Enabled')->where('role', config('constants.SuperUser'))->get();
            $mail_data = [];
            $mail_data['sr_name'] = $super_user_detail[0]->name;
            $mail_data['resignee_name'] = $resign_user_data[0]->resign_user;
            $mail_data['to_email'] = user::where('status', 'Enabled')->where('role', config('constants.SuperUser'))->pluck('email')->toArray();

            $this->common_task->resignationEmail($mail_data);

            return redirect()->route('admin.resign')->with('success', 'Resignation Approved successfully.');
        }
        return redirect()->route('admin.resign')->with('error', 'Error Occurred. Try Again!');
    }

    public function exit_interview_sheet($id)
    {
        $this->data['page_title'] = "Exit Interview Sheet";

        $resign_list = Resignation::join('users', 'users.id', '=', 'resignation.user_id')
            ->leftJoin('employee', 'employee.user_id', '=', 'users.id')
            ->where('resignation.id', $id)
            ->where('resignation.status', 'Approved')
            ->get(['resignation.user_id', 'users.name'])->first();

        $this->data['resign_id'] = $id;

        $assetAccess = AssetAccess::where('asset_access_user_id', $resign_list->user_id)
            ->join('users', 'users.id', '=', 'asset_access.asset_access_user_id')
            ->leftjoin('users as A', 'A.id', '=', 'asset_access.hr_access_user_id')
            ->leftjoin('users as B', 'B.id', '=', 'asset_access.hr_user_id')
            ->leftjoin('asset', 'asset.id', '=', 'asset_access.asset_id')
            ->where('is_allocate', 1)->get([
                'asset.name', 'asset.id as asset_id', 'asset.asset_1', 'asset_access.id', 'hr_access_user_id',
                'hr_user_id', 'users.name as user_name', 'A.name as taker_name', 'B.name as hr_name'
            ]);

        $this->data['asset_list'] = $assetAccess;
        $this->data['users_list'] = User::select('id', 'name')
            ->where('status', 'Enabled')->where('is_user_relieved', 0)
            ->get();

        return view('admin.resign.exit_interview_sheet', $this->data);
    }

    public function asset_takerByHr(Request $request)
    {    //set hr date
        $validator_normal = Validator::make($request->all(), [
            'user_id' => 'required',
            'resign_id' => 'required',
            'asset_id' => 'required'
        ]);

        $route_id = $request->input('resign_id');

        if ($validator_normal->fails()) {
            return redirect()->route('admin.exit_interview_sheet', $route_id)->with('error', 'Please follow validation rules.');
        }

        $request_data = $request->all();

        $asset_access_id = $request->input('asset_access_id');

        $update_arr = [
            'hr_access_user_id' => $request_data['user_id'],
            'hr_user_id' => \Illuminate\Support\Facades\Auth::user()->id
        ];

        //process of asset assign module...

        if (AssetAccess::where('id', $asset_access_id)->update($update_arr)) {
            $Asset = new AssetAccess();
            $Asset->asset_access_user_id = $request_data['user_id'];
            $Asset->asset_id = $request_data['asset_id'];
            $Asset->assigner_user_id = \Illuminate\Support\Facades\Auth::user()->id;
            $Asset->asset_access_date = date('Y-m-d h:i:s');
            $Asset->asset_access_description = NULL;
            $Asset->is_allocate = 0;
            $Asset->giver_status = 'Confirmed';
            $Asset->giver_datetime = date('Y-m-d H:i:s');
            $Asset->giver_id = \Illuminate\Support\Facades\Auth::user()->id;
            $Asset->status = 'Assigned';
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

            return redirect()->route('admin.exit_interview_sheet', $route_id)->with('success', 'Asset assign successfully.');
        } else {

            return redirect()->route('admin.exit_interview_sheet', $route_id)->with('error', 'Error while doing operation.');
        }
    }

    public function upload_exitInterviewSheet(Request $request)
    {   //set hr letter
        $validator_normal = Validator::make($request->all(), [
            'exit_sheet' => 'required',
            'resign_id' => 'required',
        ]);
        $route_id = $request->input('resign_id');

        if ($validator_normal->fails()) {
            return redirect()->route('admin.exit_interview_sheet', $route_id)->with('error', 'Please follow validation rules.');
        }
        $request_data = $request->all();

        //upload exit_sheet
        $exit_sheet_path = '';
        if ($request->hasFile('exit_sheet')) {
            $sheet_file = $request->file('exit_sheet');

            $original_file_name = explode('.', $sheet_file->getClientOriginalName());

            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);

            $file_path = $sheet_file->storeAs('public/exit_interview_sheets', $new_file_name);

            if ($file_path) {
                $exit_sheet_path = $file_path;
            }
        }

        if (!empty($exit_sheet_path)) {

            $update_arr = [
                'hr_status' => 'Confirmed',
                'exit_sheet' => $exit_sheet_path,
                'exit_sheet_uploaded' => 'Yes',
                'exit_sheet_uploadDate' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id
            ];

            Resignation::where('id', $request_data['resign_id'])->update($update_arr);

            return redirect()->route('admin.exit_interview_sheet', $route_id)->with('success', 'Exit Interview Sheet uploaded successfully.');
        } else {
            return redirect()->route('admin.exit_interview_sheet', $route_id)->with('error', 'Error Occurred. Try Again!');
        }
    }
}
