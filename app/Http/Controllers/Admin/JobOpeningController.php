<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Job_opening;
use App\Job_opening_consultant;
use App\Email_format;
use App\Mail\Mails;
use App\Role_module;
use Exception;
use App\Recruitment_consultant;
use DB;
use Auth;
use Illuminate\Support\Facades\Mail;
use App\Lib\Permissions;

class JobOpeningController extends Controller
{
    public function __construct() {
        $this->data['module_title']="Job Openings";
        $this->data['module_link']="admin.job_opening";
    }
    
    public function index() {
        $check_resultF = Permissions::checkPermission(7,5);
        if(!$check_resultF){
            $check_result = Permissions::checkPermission(7,1);
            if(!$check_result)
            {
                return redirect()->route('admin.dashboard')->with('error','Access Denied. You are not authorized to access that page.');    
            }
        }
        $this->data['page_title']      = "Job Openings";
        $access_level                  = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => 7])->get()->first();
        if(!empty($access_level))
        {
            $this->data['access_rule'] = $access_level->access_level;
        }

        $this->data['job_opening_list']=$job_opening_list= Job_opening::get();
        //dd($this->data['job_opening_list'][0]->consultants[0]->consultant->name);
        foreach($job_opening_list as $key=>$job_opening){
            $consultant_arr=[];
            foreach($job_opening->consultants as $consultant){
                $consultant_arr[]=$consultant->consultant->name;
            }
            $job_opening_list[$key]->consultant_list= implode(',', $consultant_arr);
        }
        
        return view('admin.job_opening.index', $this->data);
    }
    
    public function opening_change_status(Request $request,$id,$status){
        $check_result=Permissions::checkPermission(7,2);
        if(!$check_result){
            return redirect()->route('admin.dashboard')->with('error','Access Denied. You are not authorized to access that page.');
        }
        $update_arr=[
            'status'=>$status,
            'updated_at'=>date('Y-m-d H:i:s'),
            'updated_ip'=>$request->ip(),
            'updated_by'=> \Illuminate\Support\Facades\Auth::user()->id,
        ];
        
        try {
            Job_opening::where('id',$id)->update($update_arr);
            return redirect()->route('admin.job_opening')->with('success','Job opening status successfully updated.');
        } catch (Exception $exc) {
            
            return redirect()->route('admin.job_opening')->with('error','Error Occurred. Try Again!');
        }
            
    }
    
    public function close_opening($id, $status) {
        $check_result=Permissions::checkPermission(5);
        if(!$check_result){
            return redirect()->route('admin.dashboard')->with('error','Access Denied. You are not authorized to access that page.');
        }
        if (Job_opening::where('id', $id)->update(['close' => $status])) {
            return redirect()->route('admin.job_opening')->with('success', 'Status successfully updated.');
        }
        return redirect()->route('admin.job_opening')->with('error', 'Error during operation. Try again!');
    }

    public function add_opening() {
        $this->data['page_title']="Add New Opening";
        $this->data['recruitment_consultant']= Recruitment_consultant::where('status','Enable')->orderBy('name')->get();
        $this->data['company_list'] = \App\Companies::where('status', 'Enabled')->orderBy('company_name')->get();

        $check_result=Permissions::checkPermission(7,3);
        if(!$check_result){
            return redirect()->route('admin.dashboard')->with('error','Access Denied. You are not authorized to access that page.');
        }
        return view('admin.job_opening.add_opening', $this->data);
    }

    public function edit_opening($id) {
        $check_result=Permissions::checkPermission(7,2);
        if(!$check_result){
            return redirect()->route('admin.dashboard')->with('error','Access Denied. You are not authorized to access that page.');
        }
        $this->data['page_title']="Edit Job Opening";
        $this->data['recruitment_consultant']= Recruitment_consultant::where('status','Enable')->orderBy('name')->get();
        $this->data['company_list'] = \App\Companies::where('status', 'Enabled')->orderBy('company_name')->get();
        //$this->data['job_opening'] = Job_opening::where('id', $id)->get();
        $this->data['job_opening'] = DB::table('job_openings')
                        ->select('job_openings.*','job_opening_consultant.consultant_id')
                        ->join('job_opening_consultant', 'job_openings.id', '=', 'job_opening_consultant.job_opening_id')
                        ->where('job_openings.id',$id)
                        ->get();
        if ($this->data['job_opening']->count() == 0) {
            return redirect()->route('admin.job_opening')->with('error', 'Error Occurred. Try Again!');
        }
       return view('admin.job_opening.edit_opening', $this->data);
    }
    public function insert_job_opening(Request $request) {
        $validator_normal = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'role' => 'required',
            'location' => 'required',
            'package' => 'required',
            'experience_level' => 'required',
            'posting_date' => 'required',
            'recruitment_consultancy' => 'required',
            'type' => 'required',
            'company_id' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.job_opening')->with('error', 'Please follow validation rules.');
        }
      
        $jobOpeningModel = new Job_opening();
        $jobOpeningModel->title = $request->input('title');
        $jobOpeningModel->description = $request->input('description');
        $jobOpeningModel->role = $request->input('role');
        $jobOpeningModel->location = $request->input('location');
        $jobOpeningModel->package = $request->input('package');
        $jobOpeningModel->experience_level = $request->input('experience_level');
        $jobOpeningModel->posting_date = date('Y-m-d h:i:s',strtotime($request->input('posting_date')));
        $jobOpeningModel->type = $request->input('type');
        $jobOpeningModel->company_id = $request->input('company_id');
        $jobOpeningModel->created_at = date('Y-m-d h:i:s');
        $jobOpeningModel->created_ip = $request->ip();
        $jobOpeningModel->updated_at = date('Y-m-d h:i:s');
        $jobOpeningModel->updated_ip = $request->ip();
        // 2 new fields are added
        $jobOpeningModel->urgency_requirement = $request->input('urgency_requirement');
        $jobOpeningModel->qualification = $request->input('qualification');
                    
                    
        if ($jobOpeningModel->save()) {
            $jobOpeningModel->qualification = $request->input('qualification');

            //generate Job Id Save Into Opening Table
            $job_id_arr = [
                'job_id' => 10000+$jobOpeningModel->id,
            ];
            Job_opening::where('id',$jobOpeningModel->id)->update($job_id_arr);

            $recruitment_consultancy = $request->input('recruitment_consultancy');
            foreach ($recruitment_consultancy as $key => $value) {
                $jobOpeningConstalt = new Job_opening_consultant();
                $jobOpeningConstalt->job_opening_id = $jobOpeningModel->id;
                $jobOpeningConstalt->consultant_id  = $value;
                $jobOpeningConstalt->save();

                //Send Job Opening Mail to consultant
                $emailData = Email_format::find(11)->toArray(); // 9 = Cancel Applied Leave
                $data = Recruitment_consultant::where('id',$value)->get();

                $subject = $emailData['subject'];
                $mailformat = $emailData['emailformat'];
                $mailformat = str_replace("%consult_name%", $data[0]['name'], $mailformat);
                $mailformat = str_replace("%title%", $request->input('title'), $mailformat);
                $mailformat = str_replace("%job_id%", 10000+$jobOpeningModel->id, $mailformat);
                $mailformat = str_replace("%job_role%",$request->input('role'), $mailformat);
                $mailformat = str_replace("%job_package%",$request->input('package'), $mailformat);
                $mailformat = str_replace("%job_location%",$request->input('location'), $mailformat);
                $mailformat = str_replace("%job_description%", $request->input('location'), $mailformat);

                Mail::to($data[0]['email'])->send(new Mails($subject, $mailformat));
            }
            return redirect()->route('admin.job_opening')->with('success', 'New job opening added successfully.');
        } else {
            return redirect()->route('admin.job_opening')->with('error', 'Error occurre in insert. Try Again!');
        }
    }
    public function update_job_opening(Request $request) {
        $validator_normal = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
            'role' => 'required',
            'location' => 'required',
            'package' => 'required',
            'experience_level' => 'required',
            'posting_date' => 'required',
            'recruitment_consultancy' => 'required',
            'type' => 'required',
            'company_id' => 'required'
        ]);

        if ($validator_normal->fails()) {
            return redirect()->route('admin.job_opening')->with('error', 'Please follow validation rules.');
        } 
        $job_id = $request->input('id'); 
        $job_arr = [
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'role' => $request->input('role'),
            'location' => $request->input('location'),
            'package' => $request->input('package'),
            'experience_level' => $request->input('experience_level'),
            'posting_date' => $request->input('posting_date'),
            'type' => $request->input('type'),
            'company_id' => $request->input('company_id'),
            'created_at' => date('Y-m-d h:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
            'urgency_requirement' => $request->input('urgency_requirement'),
            'qualification' => $request->input('qualification'),
            // $input = $request->all(),
    	    // $qualification = implode(",", $request->qualification),
            // $jobOpeningModel=implode(",",);
            
        ];
        
        Job_opening::where('id', $job_id)->update($job_arr);
        $recruitment_consultancy = $request->input('recruitment_consultancy');
        foreach ($recruitment_consultancy as $key => $value) {
            $Existsdata = Job_opening_consultant::where('job_opening_id',$job_id)->where('consultant_id',$value)->get();
            if(empty($Existsdata[0]) && $Existsdata->count()==0) {
                $jobOpeningConstalt = new Job_opening_consultant();
                $jobOpeningConstalt->job_opening_id = $job_id;
                $jobOpeningConstalt->consultant_id  = $value;
                $jobOpeningConstalt->save();
            }
            //Send Job Opening Mail to consultant
            $emailData = Email_format::find(12)->toArray(); // 9 = Cancel Applied Leave
            $data = Recruitment_consultant::where('id',$value)->get();

            $subject = $emailData['subject'];
            $mailformat = $emailData['emailformat'];
            $mailformat = str_replace("%consult_name%", $data[0]['name'], $mailformat);
            $mailformat = str_replace("%title%", $request->input('title'), $mailformat);
            $mailformat = str_replace("%job_id%", 10000+$job_id, $mailformat);
            $mailformat = str_replace("%job_role%",$request->input('role'), $mailformat);
            $mailformat = str_replace("%job_package%",$request->input('package'), $mailformat);
            $mailformat = str_replace("%job_location%",$request->input('location'), $mailformat);
            $mailformat = str_replace("%job_description%", $request->input('location'), $mailformat);

            Mail::to($data[0]['email'])->send(new Mails($subject, $mailformat));
        }
        return redirect()->route('admin.job_opening')->with('success', 'Job successfully updated.');
    }
}
