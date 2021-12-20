<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Job_opening;
use App\Lib\CommonTask;
use App\Job_opening_consultant;
use App\Interview;
use App\User;
use App\InterviewResult;
use App\Email_format;
use App\Mail\Mails;
use App\Role_module;
use Exception;
use App\Recruitment_consultant;
use DB;
use Illuminate\Support\Facades\Mail;
use Auth;
use App\Policy;
use App\Lib\Permissions;
use URL;

class InterviewController extends Controller {

    public $common_task;

    public function __construct() {
        $this->data['module_title'] = "Interview Process";
        $this->data['module_link'] = "admin.interview";
        $this->common_task = new CommonTask();
    }

    public function index() {
        $this->data['page_title'] = "Interview Process";
        $access_level = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => 5])->get()->first();
        $this->data['role'] = !empty($access_level) ? explode(',', $access_level->access_level) : '';

        if (in_array(5, $this->data['role'])) {
            $this->data['interview_list'] = DB::table('interview')
                    ->select('interview.*', 'job_openings.status', 'job_openings.job_id', 'job_openings.title')
                    ->join('job_openings', 'interview.job_opening_id', '=', 'job_openings.id')
                    ->orderBy('interview.updated_at', 'DESC')
                    ->get();
        } else if (in_array(6, $this->data['role'])) {
            $this->data['interview_list'] = DB::table('interview')
                    ->select('interview.*', 'job_openings.status', 'job_openings.job_id', 'job_openings.title')
                    ->join('job_openings', 'interview.job_opening_id', '=', 'job_openings.id')
                    ->whereRaw("FIND_IN_SET(" . Auth::user()->id . ",interviewers)")
                    ->orderBy('interview.updated_at', 'DESC')
                    ->get();
        } else {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }
        return view('admin.interview.index', $this->data);
    }

    public function add_interview() {
        $check_result = Permissions::checkPermission(5, 3);
        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }
        $this->data['page_title'] = "Add Interview Details";
        $this->data['job_opening_position'] = Job_opening::select('id', 'title')->get();
        $this->data['users_data'] = User::select('id', 'name')->where('role', '<>', 4)->where('role', '<>', 1)->get();
        return view('admin.interview.add_interview', $this->data);
    }

    public function insert_interview(Request $request) {

        $validator_normal = Validator::make($request->all(), [
                    'name' => 'required',
                    'designation' => 'required',
                    'email' => 'required',
                    'contact_number' => 'required',
                    'emg_contact_number' => 'required',
                    'residential_address' => 'required',
                    'permanent_address' => 'required',
                    'gender' => 'required',
                    'birth_date' => 'required',
                    'marital_status' => 'required',
                    'physically_handicapped' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.interview')->with('error', 'Please follow validation rules.');
        }
        $getJobId = Job_opening::select('job_id')->where('id', $request->input('designation'))->get()->first()->toArray();
        $getJobId = strtoupper(substr($request->input('name'), 0, 2) . $getJobId['job_id']);

        $interviewModel = new Interview();
        $interviewModel->name = $request->input('name');
        $interviewModel->job_opening_id = $request->input('designation');
        $interviewModel->interviewee_id = $getJobId;
        $interviewModel->contact_number = $request->input('contact_number');
        $interviewModel->emg_contact_number = $request->input('emg_contact_number');
        $interviewModel->residential_address = $request->input('residential_address');
        $interviewModel->permanent_address = $request->input('permanent_address');
        $interviewModel->gender = $request->input('gender');
        $interviewModel->birth_date = date('Y-m-d', strtotime($request->input('birth_date')));
        $interviewModel->marital_status = $request->input('marital_status');
        $interviewModel->physically_handicapped = $request->input('physically_handicapped');
        $interviewModel->handicap_note = $request->input('handicap_note');
        $interviewModel->email = $request->input('email');
        $interviewModel->created_at = date('Y-m-d h:i:s');
        $interviewModel->created_ip = $request->ip();
        $interviewModel->updated_at = date('Y-m-d h:i:s');
        $interviewModel->updated_ip = $request->ip();
        if ($interviewModel->save()) {
            return redirect()->route('admin.interview')->with('success', 'Interview details added successfully.');
        } else {
            return redirect()->route('admin.interview')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function edit_interview($id) {
        $check_result = Permissions::checkPermission(5, 2);
        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }
        $this->data['page_title'] = "Edit Interview Details";
        $this->data['job_opening_position'] = Job_opening::select('id', 'title')->orderBy('title')->get();
        $this->data['interview_list'] = Interview::where('id', $id)->get()->first();
        $this->data['users_data'] = User::select('id', 'name')->where('role', '<>', 4)->where('role', '<>', 1)->get();

        return view('admin.interview.edit_interview', $this->data);
    }

    public function update_interview(Request $request) {
        $validator_normal = Validator::make($request->all(), [
                    'name' => 'required',
                    'designation' => 'required',
                    'contact_number' => 'required',
                    'emg_contact_number' => 'required',
                    'residential_address' => 'required',
                    'email' => 'required',
                    'permanent_address' => 'required',
                    'gender' => 'required',
                    'birth_date' => 'required',
                    'marital_status' => 'required',
                    'physically_handicapped' => 'required'
        ]);

        if ($validator_normal->fails()) {
            return redirect()->route('admin.interview')->with('error', 'Please follow validation rules.');
        }
        $interview_id = $request->input('id');
        $interview_arr = [
            'name' => $request->input('name'),
            'job_opening_id' => $request->input('designation'),
            'contact_number' => $request->input('contact_number'),
            'emg_contact_number' => $request->input('emg_contact_number'),
            'residential_address' => $request->input('residential_address'),
            'permanent_address' => $request->input('permanent_address'),
            'email' => $request->input('email'),
            'gender' => $request->input('gender'),
            'birth_date' => date('Y-m-d', strtotime($request->input('birth_date'))),
            'marital_status' => $request->input('marital_status'),
            'physically_handicapped' => $request->input('physically_handicapped'),
            'handicap_note' => $request->input('handicap_note'),
            'created_at' => date('Y-m-d h:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
        ];
        if (Interview::where('id', $interview_id)->update($interview_arr)) {
            return redirect()->route('admin.interview')->with('success', 'Interviewee details successfully updated.');
        } else {
            return redirect()->route('admin.interview')->with('error', 'Error occurre in update. Try Again!');
        }
    }

    public function add_next_interview($id) {
        $this->data['page_title'] = "Interview Next Round Details";
        $this->data['interview_list'] = InterviewResult::where('interview_id', $id)->where('status', 'Pending')->orderBy('id', 'desc')->get()->first();

        $this->data['interview'] = Interview::select(['emp_status', 'id'])->where('id', $id)->orderBy('id', 'desc')->get()->first();

        $interview_round = InterviewResult::where('interview_id', $id)->where('status', 'Completed')->orderBy('id', 'desc')->get()->count();

        $this->data['interview_round'] = ($interview_round == 0) ? 1 : $interview_round + 1;

        if (!empty($this->data['interview_list'])) {
            $this->data['interview_result_id'] = $this->data['interview_list']->id;
        }
        $this->data['users_data'] = User::select('id', 'name')->where('status', 'Enabled')->get();
        return view('admin.interview.add_next_interview', $this->data);
    }

    public function insert_next_interview(Request $request) {   //change

        $validator_normal = Validator::make($request->all(), [
                    'interview_id' => 'required',
                    'interview_date' => 'required',
                    'interviewer_ids' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_next_interview', ['id' => $request->input('interview_id')])->with('error', 'Please follow validation rules.');
        }
        if (!empty($request->input('interview_result_id'))) {
            $interviewModel = InterviewResult::where('id', $request->input('interview_result_id'))->get()->first();
        } else {
            $interviewModel = new InterviewResult();
        }
        $interviewModel->interview_id = $request->input('interview_id');
        $interviewModel->interview_date = $request->input('interview_date');
        $interviewModel->interviewer_ids = implode(',', $request->input('interviewer_ids'));
        $interviewModel->created_at = date('Y-m-d h:i:s');
        $interviewModel->created_ip = $request->ip();
        $interviewModel->updated_at = date('Y-m-d h:i:s');
        $interviewModel->updated_ip = $request->ip();

        if ($interviewModel->save()) {
            $intModel = Interview::where('id', $interviewModel->interview_id)->get()->first();
            $interviewers = !empty($intModel->interviewers) ? explode(',', $intModel->interviewers) : [];
            $interviewerIds = !empty($interviewModel->interviewer_ids) ? explode(',', $interviewModel->interviewer_ids) : [];
            $intModel->interviewers = implode(',', array_unique(array_merge($interviewers, $interviewerIds)));
            $intModel->save();

            $sendMailData = DB::table('interview')
                            ->select('interview.name as candidate', 'interview.interviewee_id', 'interview.emp_status', 'job_openings.*')
                            ->join('job_openings', 'interview.job_opening_id', '=', 'job_openings.id')
                            ->where('interview.id', $interviewModel->interview_id)->get()->first();
                            
            $interviewerEmail = user::whereIn('id', $interviewerIds)->pluck('email')->toArray();

            $this->common_task->interviewersNotifyMail($sendMailData, $interviewModel->interview_date ,$interviewerEmail );

            return redirect()->route('admin.interview')->with('success', 'Next Interview scheduled successfully.');
        } else {
            return redirect()->route('admin.interview')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function interview_marks($id) {
        $this->data['page_title'] = "Interview Round Results";
        $this->data['interview_list'] = InterviewResult::where('interview_id', $id)->where('status', 'Pending')->orderBy('id', 'desc')->get()->first();
        if (empty($this->data['interview_list']) || !in_array(Auth::user()->id, explode(',', $this->data['interview_list']->interviewer_ids))) {
            return redirect()->route('admin.interview')->with('error', 'Access Denied. You already submited marks.');
        }
        if (!empty($this->data['interview_list'])) {
            $this->data['interview_result_id'] = $this->data['interview_list']->id;
        }
        return view('admin.interview.interview_marks', $this->data);
    }

    public function insert_interview_marks(Request $request) {  //change

        $validator_normal = Validator::make($request->all(), [
                    'experience' => 'required',
                    'knowledge' => 'required',
                    'communication' => 'required',
                    'personality' => 'required',
                    'interpersonal_skill' => 'required',
                    'decision_making' => 'required',
                    'self_confidence' => 'required',
                    'acceptability' => 'required',
                    'commute' => 'required',
                    'suitability' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.interview_marks', ['id' => $request->input('interview_id')])->with('error', 'Please follow validation rules.');
        }
        if (!empty($request->input('interview_result_id'))) {
            $interviewModel = InterviewResult::where('id', $request->input('interview_result_id'))->get()->first();
        } else {
            $interviewModel = new InterviewResult();
        }

        $interviewModel->status = 'Completed';
        $interviewModel->experience = $request->input('experience');
        $interviewModel->knowledge = $request->input('knowledge');
        $interviewModel->communication = $request->input('communication');
        $interviewModel->personality = $request->input('personality');
        $interviewModel->interpersonal_skill = $request->input('interpersonal_skill');
        $interviewModel->decision_making = $request->input('decision_making');
        $interviewModel->self_confidence = $request->input('self_confidence');
        $interviewModel->acceptability = $request->input('acceptability');
        $interviewModel->commute = $request->input('commute');
        $interviewModel->suitability = $request->input('suitability');
        $interviewModel->technical_skill = !empty($request->input('technical')) ? serialize($request->input('technical')) : NULL;

        $interviewModel->updated_at = date('Y-m-d h:i:s');
        $interviewModel->updated_ip = $request->ip();
        if ($interviewModel->save()) {

            $intModel = Interview::where('id', $interviewModel->interview_id)->get()->first();

            $this->common_task->interviewMarksUpdate($intModel, $interviewModel->interview_date);

            return redirect()->route('admin.interview')->with('success', 'Interview marks added successfully.');
        } else {
            return redirect()->route('admin.interview')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function interview_details($id) {
        $this->data['page_title'] = "Interview Details";
        $interviewe_result = InterviewResult::where('status', 'Completed')->where("interview_result.interview_id", $id)->get();
        foreach ($interviewe_result as $key => $value) {
            $interviewerIds = user::whereIn('id', explode(',', $value->interviewer_ids))->pluck('name')->toArray();
            $value->interviewer_ids = implode(',', $interviewerIds);
        }
        $this->data['interview_result'] = $interviewe_result;
        $this->data['interview_details'] = Interview::where("id", $id)->get()->first();
        return view('admin.interview.interview_details', $this->data);
    }

    public function interview_complete($id, Request $request) {

        $interview_arr = [
            'emp_status' => 'completed',
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
        ];
        if (Interview::where('id', $id)->update($interview_arr)) {

            $sendMailData = DB::table('interview')
                            ->select('interview.name as candidate', 'interview.interviewee_id', 'interview.emp_status', 'job_openings.*')
                            ->join('job_openings', 'interview.job_opening_id', '=', 'job_openings.id')
                            ->where('interview.id', $id)->get()->toArray();

            $emailData = Email_format::find(55)->toArray(); // 55 => Send mail to admin for select/reject

            $name = $sendMailData[0]->candidate;
            $candidate_id = $sendMailData[0]->interviewee_id;
            $emp_status = $sendMailData[0]->emp_status;
            $desgination = $sendMailData[0]->title;
            $description = $sendMailData[0]->description;
            $role = $sendMailData[0]->role;
            $location = $sendMailData[0]->location;
            $job_id = $sendMailData[0]->job_id;

            $subject = $emailData['subject'];
            $mailformat = $emailData['emailformat'];
            $mailformat = str_replace("%emp_status%", $emp_status, $mailformat);
            $mailformat = str_replace("%name%", $name, $mailformat);
            $mailformat = str_replace("%candidate_id%", $candidate_id, $mailformat);
            $mailformat = str_replace("%desgination%", $desgination, $mailformat);
            $mailformat = str_replace("%job_id%", $job_id, $mailformat);
            $mailformat = str_replace("%job_role%", $role, $mailformat);
            $mailformat = str_replace("%job_location%", $location, $mailformat);
            $mailformat = str_replace("%job_description%", $description, $mailformat);

            $superUserEmail = user::where('status', 'Enabled')->where('role', config('constants.SuperUser'))->pluck('email')->toArray();

            //Mail::to($superUserEmail)->send(new Mails($subject, $mailformat));

            return redirect()->route('admin.interview')->with('success', 'Interview process completed successfully updated.');
        }

        return redirect()->route('admin.interview')->with('error', 'Error during operation. Try again!');
    }

    public function interview_action($id, $status, Request $request) {
        
        $userInfo = DB::table('interview')
        ->select('interview.name as candidate','interview.email', 'interview.interviewee_id', 'interview.emp_status', 'job_openings.*')
        ->join('job_openings', 'interview.job_opening_id', '=', 'job_openings.id')
        ->where('interview.id', $id)->get()->toArray();


        $mail_data = [];
        $mail_data['to_email'] = $userInfo[0]->email;
        $mail_data['desgination'] = $userInfo[0]->title;
        $mail_data['user_name'] = $userInfo[0]->candidate;


        if ($status == 'selected') {
            if (Auth::user()->role == config('constants.REAL_HR')) {
                
                $interview_arr = [
                    'hr_status' => 'Selected',
                    'hr_datetime' => date('Y-m-d H:i:s'),
                    'hr_id' => Auth::user()->id,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip()
                ];
            
            }else{

                $interview_arr = [
                    'superUser_status' => 'Selected',
                    'superUser_datetime' => date('Y-m-d H:i:s'),
                    'superUser_id' => Auth::user()->id,
                    'emp_status' => $status,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip()
                ];

                $this->common_task->interSelectConfirm($mail_data);

            }
        }elseif ($status == 'rejected') {
            if (Auth::user()->role == config('constants.REAL_HR')) {
                $interview_arr = [
                    'hr_status' => 'Rejected',
                    'hr_datetime' => date('Y-m-d H:i:s'),
                    'hr_id' => Auth::user()->id,
                    'emp_status' => $status,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip()
                ];
            }else{
                $interview_arr = [
                    'superUser_status' => 'Rejected',
                    'superUser_datetime' => date('Y-m-d H:i:s'),
                    'superUser_id' => Auth::user()->id,
                    'emp_status' => $status,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip()
                ];

            }

            $this->common_task->interRejectConfirm($mail_data);
            
        }else{
            if (Auth::user()->role == config('constants.REAL_HR')) {
                $interview_arr = [
                    'hr_status' => 'Hold',
                    'before_hold_status' => Interview::where('id',$id)->value('hr_status'),
                    'prev_status' => Interview::where('id',$id)->value('emp_status'),
                    'hr_datetime' => date('Y-m-d H:i:s'),
                    'hr_id' => Auth::user()->id,
                    'emp_status' => $status,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip()
                ];

            }else{

                $interview_arr = [
                    'superUser_status' => 'Hold',
                    'before_hold_status' => Interview::where('id',$id)->value('superUser_status'),
                    'prev_status' => Interview::where('id',$id)->value('emp_status'),
                    'superUser_datetime' => date('Y-m-d H:i:s'),
                    'superUser_id' => Auth::user()->id,
                    'emp_status' => $status,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip()
                ];

            }

            $this->common_task->interHoldConfirm($mail_data);

        }

        if (Interview::where('id', $id)->update($interview_arr)) {

            $sendMailData = DB::table('interview')
                            ->select('interview.name as candidate','interview.email', 'interview.interviewee_id', 'interview.emp_status', 'job_openings.*')
                            ->join('job_openings', 'interview.job_opening_id', '=', 'job_openings.id')
                            ->where('interview.id', $id)->get()->toArray();

            $hrMail_data = [];
            $hrMail_data['emp_status'] = $sendMailData[0]->emp_status;
            $hrMail_data['name'] = $sendMailData[0]->candidate;
            $hrMail_data['candidate_id'] = $sendMailData[0]->interviewee_id;
            $hrMail_data['desgination'] = $sendMailData[0]->title;
            $hrMail_data['job_id'] =  $sendMailData[0]->job_id;
            $hrMail_data['job_role'] = $sendMailData[0]->role;
            $hrMail_data['job_location'] = $sendMailData[0]->location;
            $hrMail_data['job_description'] = $sendMailData[0]->description;

            if (Auth::user()->role == config('constants.SuperUser')) {
                $this->common_task->hrMailAfterApproval($hrMail_data);
            }
            

            return redirect()->route('admin.interview')->with('success', 'Interviewee details successfully updated.');
        }

        return redirect()->route('admin.interview')->with('error', 'Error during operation. Try again!');
    }

    public function confirm_interview($id) {
        $check_result = Permissions::checkPermission(5, 2);
        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }

        $this->data['page_title'] = " Candidate Details";
        $this->data['job_opening_position'] = Job_opening::select('id', 'title')->get();
        $this->data['access_rule'] = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => 5])->get()->first()->access_level;

        $this->data['users_data'] = User::select('id', 'name')->get();

        $this->data['interview_list'] = DB::table('interview')
                        ->select('interview.*', 'job_openings.status', 'job_openings.job_id', 'job_openings.title')
                        ->join('job_openings', 'interview.job_opening_id', '=', 'job_openings.id')
                        ->where('interview.id', $id)->get()->first();

        if (empty($this->data['interview_list'])) {
            return redirect()->route('admin.interview')->with('error', 'Error Occurred. Try Again!');
        }
        return view('admin.interview.confirm_interview', $this->data);
    }

    public function add_confirm_interview(Request $request) {
        $validator_normal = Validator::make($request->all(), [
                    'package' => 'required',
                    'join_date' => 'required',
        ]);

        if ($validator_normal->fails()) {
            return redirect()->route('admin.interview')->with('error', 'Please follow validation rules.');
        }
        $interview_id = $request->input('id');
        $email = $request->input('email');
        $designation = $request->input('designation');
        $name = $request->input('name');

        $interview_arr = [
            'package' => $request->input('package'),
            'join_date' => date('Y-m-d', strtotime($request->input('join_date'))),
            'password' => rand(10000, 99999),
            'created_at' => date('Y-m-d h:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
        ];

        Interview::where('id', $interview_id)->update($interview_arr);
        $interviewData = Interview::select('job_opening_id', 'password')->where('id', $interview_id)->get()->first()->toArray();

        if (!empty($interviewData)) {
            $jobOpeningData = Job_opening::select('title')->where('id', $interviewData['job_opening_id'])->get()->toArray();
        }
        $data = [
            'email' => $email,
            'name' => $name,
            'position' => $jobOpeningData[0]['title'],
            'password' => $interviewData['password'],
            'link' => URL::to('/guestLogin'),
        ];

        $this->common_task->sendConfirmationEmail($data);

        return redirect()->route('admin.interview')->with('success', 'Interviewee details successfully updated.');
    }

    public function guestLogin() {
        return view('admin.interview.guestLogin');
    }

    public function guestLoginAuth(Request $request) {
        $validator_normal = Validator::make($request->all(), [
                    'email' => 'required',
                    'password' => 'required',
        ]);

        if ($validator_normal->fails()) {
            return redirect()->route('admin.interview.guestLogin')->with('error', 'Please follow validation rules.');
        }

        $email = $request->input('email');
        $password = $request->input('password');

        $guestLoinData = Interview::select('id', 'email', 'password', 'name', 'job_opening_id')->where('email', $email)->where('password', $password)->where('policy_except', 'NO')->get()->toArray();

        if (!empty($guestLoinData)) {
            $jobOpeningData = Job_opening::select('title')->where('id', $guestLoinData[0]['job_opening_id'])->get()->toArray();
            session_start();
            $_SESSION['is_guest'] = 'yes';
            $_SESSION['guest_email'] = $guestLoinData[0]['email'];
            $_SESSION['guest_name'] = $guestLoinData[0]['name'];
            $_SESSION['guest_interview_id'] = $guestLoinData[0]['id'];
            $_SESSION['guest_job_position'] = $jobOpeningData[0]['title'];

            return redirect()->route('admin.confirm_interview_form');
        } else {
            return redirect()->route('admin.guestLogin')->with('error', 'Invalid email or password.');
        }
    }

    public function confirm_interview_form() {
        session_start();
        if (isset($_SESSION) && !empty($_SESSION)) {
            $id = $_SESSION['guest_interview_id'];
            if (!empty($_SESSION['is_guest']) && isset($_SESSION)) {
                $this->data['interview_list'] = Interview::select('*')->where('id', $id)->get()->toArray();
                $this->data['policy_list'] = Policy::select('*')->get()->toArray();
                return view('admin.interview.confirm_interview_form', $this->data);
            } else {
                return redirect()->route('admin.guestLogin')->with('error', 'You are not authorized to perform this action !.');
            }
        } else {
            return redirect()->route('admin.guestLogin')->with('error', 'You are not authorized to perform this action !.');
        }
    }

    public function emp_confirm_interview(Request $request) {
        $email = $request->input('email');
        $name = $request->input('name');
        $interviewData = Interview::select('id')->where('email', $email)->get()->first();
        Interview::where('id', $interviewData->id)->update(['policy_except' => 'YES']);
        $hrEmail = user::where('status', 'Enabled')->where('role', config('constants.REAL_HR'))->pluck('email')->toArray();
        $data = ['name' => $name, 'hr_email' => $hrEmail];
        $this->common_task->sendJoinConfirmationEmail($data);
        session_unset();
        return redirect()->route('admin.guestLogin')->with('success', 'Thanks for confirmation!');
    }

    public function interviewer_detail(Request $request)  //ajax call new 09/04
    {

        $validator_normal = Validator::make($request->all(), ['id' => 'required']);

        if ($validator_normal->fails()) {
            return response()->json(['status' => false]);
        }
            $interview_id = $request->id;
        
            $interviewer_info = Interview::where('id', $interview_id)->get()->first();

            foreach ($interviewer_info as $key => $value) {
               
                $interviewer_info['job_opening'] = Job_opening::where('id',$interviewer_info['job_opening_id'])->value('title');
                
                if ($interviewer_info['marital_status'] == 'no') {
                    $interviewer_info['marital_status'] = 'No';
                }elseif ($interviewer_info['marital_status'] == 'yes') {
                    $interviewer_info['marital_status'] = 'Yes';
                }
                //=============//
                if ($interviewer_info['physically_handicapped'] == 'no') {
                    $interviewer_info['physically_handicapped'] = 'No';
                }elseif ($interviewer_info['physically_handicapped'] == 'yes') {
                    $interviewer_info['physically_handicapped'] = 'Yes';
                }

            }
        
            $this->data['interviewer_info'] = $interviewer_info;

        if ($interviewer_info->count() == 0) {
            return response()->json(['status' => false, 'data' => $this->data]);
        } else {
            return response()->json(['status' => true, 'data' => $this->data]);
        }
    }


    public function interviewIsOnHold($id ,$status, Request $request)  //14/04/2020
    {

        $before_hold_status =  Interview::where('id',$id)->value('before_hold_status');
        $emp_status =  Interview::where('id',$id)->value('prev_status');
        if ($status == 'ContinueBack') {
            $interview_arr = [

                'emp_status' => $emp_status,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip()
            ];

            if (Auth::user()->role == config('constants.REAL_HR')) {
                $interview_arr['hr_status'] = $before_hold_status;
            }else{
                $interview_arr['superUser_status'] = $before_hold_status;
            }
    
        } else {

            $interview_arr = [

                'emp_status' => 'hold',
                'before_hold_status' => 'hold',
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip()
            ];
    
        }

        if(Interview::where('id', $id)->update($interview_arr)){

            return redirect()->route('admin.interview')->with('success', 'Interviewee details successfully updated');
        }

        return redirect()->route('admin.interview')->with('error', 'Error during operation. Try again!');

    }

    public function job_candidates($id) {   //09/04
        
        if (Auth::user()->role == config('constants.SuperUser') || Auth::user()->role == config('constants.REAL_HR')  || Auth::user()->role == config('constants.Admin')) {
            
        $this->data['page_title'] = "Job Candidates Sheet";
        $this->data['module_link'] = "admin.job_opening";
        
        $this->data['route_id'] = $id;

        $this->data['candidates_list'] = $candidates_list = Interview::with('interview_result')
            ->where('interview.job_opening_id','=',$id)
            ->whereIn('interview.emp_status',['selected','completed'])
            ->get(['interview.*']);

            $round_arr  = []; 
            foreach ($candidates_list as $key => $value) {
                $totalAverage = 0;
                $counter = $totalMarks = 0;
                $round_arr[] = count($value->interview_result);
                
                foreach ($value->interview_result as $index => $list) {

                    $counter++; 
                    $marks = ($list->experience + $list->knowledge + $list->communication + $list->personality + $list->interpersonal_skill + $list->decision_making + $list->self_confidence + $list->acceptability + $list->commute + $list->suitability)/10;

                    $totalMarks += $marks;

                    $candidates_list[$key]->interview_result[$index]->round_average = round($marks,2).'%';
                   
                }

                $totalAverage = round(($totalMarks/$counter),2);

                $candidates_list[$key]->totalAverage = $totalAverage.'%';
            }
            
            $this->data['candidates_list'] = $candidates_list;
           
            $this->data['round'] = count($round_arr) > 0 ? max($round_arr) : 1;
        return view('admin.job_opening.job_candidate_sheet', $this->data);

        }else{
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }
       
    
    }


    public function multiple_candidate_approval(Request $request)  //09/04
    {
        $candidate_list = explode(',',$request->input('candidate_ids'));
         
        $route_id = $request->input('id');
        if (empty($candidate_list)) {
            return redirect()->route('admin.job_candidates',$route_id)->with('error', 'Please select expenses you want to approve.');
        }

        $status = $request->input('emp_status');
        foreach ($candidate_list as $id) {
            $userInfo = DB::table('interview')
                ->select('interview.name as candidate','interview.email', 'interview.interviewee_id', 'interview.emp_status', 'job_openings.*')
                ->join('job_openings', 'interview.job_opening_id', '=', 'job_openings.id')
                ->where('interview.id', $id)->get()->toArray();


            $mail_data = [];
            $mail_data['to_email'] = $userInfo[0]->email;
            $mail_data['desgination'] = $userInfo[0]->title;
            $mail_data['user_name'] = $userInfo[0]->candidate;

            if ($status == 'selected') {
                if (Auth::user()->role == config('constants.REAL_HR')) {
                    
                    $interview_arr = [
                        'hr_status' => 'Selected',
                        'hr_datetime' => date('Y-m-d H:i:s'),
                        'hr_id' => Auth::user()->id,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_ip' => $request->ip()
                    ];
                
                }else{
    
                    $interview_arr = [
                        'superUser_status' => 'Selected',
                        'superUser_datetime' => date('Y-m-d H:i:s'),
                        'superUser_id' => Auth::user()->id,
                        'emp_status' => $status,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_ip' => $request->ip()
                    ];
    
                    $this->common_task->interSelectConfirm($mail_data);
    
                }
            }elseif ($status == 'rejected') {
                if (Auth::user()->role == config('constants.REAL_HR')) {
                    $interview_arr = [
                        'hr_status' => 'Rejected',
                        'hr_datetime' => date('Y-m-d H:i:s'),
                        'hr_id' => Auth::user()->id,
                        'emp_status' => $status,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_ip' => $request->ip()
                    ];
                }else{
                    $interview_arr = [
                        'superUser_status' => 'Rejected',
                        'superUser_datetime' => date('Y-m-d H:i:s'),
                        'superUser_id' => Auth::user()->id,
                        'emp_status' => $status,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_ip' => $request->ip()
                    ];
    
                }
    
                $this->common_task->interRejectConfirm($mail_data);
                
            }else{
                if (Auth::user()->role == config('constants.REAL_HR')) {
                    $interview_arr = [
                        'hr_status' => 'Hold',
                        'hr_datetime' => date('Y-m-d H:i:s'),
                        'hr_id' => Auth::user()->id,
                        'emp_status' => $status,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_ip' => $request->ip()
                    ];
    
                }else{
    
                    $interview_arr = [
                        'superUser_status' => 'Hold',
                        'superUser_datetime' => date('Y-m-d H:i:s'),
                        'superUser_id' => Auth::user()->id,
                        'emp_status' => $status,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_ip' => $request->ip()
                    ];
    
                }
    
                $this->common_task->interHoldConfirm($mail_data);
    
            }

            //then update candidate status=======
            Interview::where('id', $id)->update($interview_arr);

                $sendMailData = DB::table('interview')
                                ->select('interview.name as candidate','interview.email', 'interview.interviewee_id', 'interview.emp_status', 'job_openings.*')
                                ->join('job_openings', 'interview.job_opening_id', '=', 'job_openings.id')
                                ->where('interview.id', $id)->get()->toArray();
    
                $hrMail_data = [];
                $hrMail_data['emp_status'] = $sendMailData[0]->emp_status;
                $hrMail_data['name'] = $sendMailData[0]->candidate;
                $hrMail_data['candidate_id'] = $sendMailData[0]->interviewee_id;
                $hrMail_data['desgination'] = $sendMailData[0]->title;
                $hrMail_data['job_id'] =  $sendMailData[0]->job_id;
                $hrMail_data['job_role'] = $sendMailData[0]->role;
                $hrMail_data['job_location'] = $sendMailData[0]->location;
                $hrMail_data['job_description'] = $sendMailData[0]->description;
    
                if (Auth::user()->role == config('constants.SuperUser')) {
                    $this->common_task->hrMailAfterApproval($hrMail_data);
                }
        
        }

        return redirect()->route('admin.job_candidates',$route_id)->with('success', 'Candidate Status successfully updated.');
        
    
    }

}
