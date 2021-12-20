<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Common_query;
use App\CompanyRules;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Imports\BankTransactionImport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Config;
use App\User;
use App\Policy;
use App\Lib\Permissions;
use App\Email_format;
use App\Mail\Mails;
use App\Role_module;
use App\RevisePolicy;
use App\UserRevisePolicy;
use Illuminate\Support\Facades\Mail;
use App\Lib\CommonTask;
use Yajra\DataTables\Facades\DataTables;
use App\Lib\NotificationTask;

class PolicyController extends Controller {

    public $data;

    public function __construct() {
        $this->data['module_title'] = "Policy";
        $this->data['module_link'] = "admin.policy";
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();
    }

    public function index() {
        $this->data['page_title'] = "Policy";
        $access_level = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => 21])->get()->first();
        $check_resultF = Permissions::checkPermission(21, 5);
        if (!$check_resultF) {
            $check_resultP = Permissions::checkPermission(21, 6); // Partial View
            if (!$check_resultP) {
                $check_resultM = Permissions::checkPermission(21, 1); // Only My View
                if (!$check_resultM) {
                    return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
                }
            }
        }
        if (!empty($access_level)) {
            $this->data['access_rule'] = $access_level->access_level;
        }

        return view('admin.policy.index', $this->data);
    }

    public function get_policy_list() {

        $datatable_fields = array('id', 'title', 'name','implementation_date','amendment_date','addendum_date','effective_from');
        $request = Input::all();
        $conditions_array = [];

        $join_str = [];

        $getfiled = array('id', 'title', 'name','implementation_date','amendment_date','addendum_date','effective_from');
        $table = "policy";

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);

        die();
    }

    public function add_policy() {
        $this->data['page_title'] = 'Add Policy';
        return view('admin.policy.add_policy', $this->data);
    }

    public function insert_policy(Request $request) {
        $validator_normal = Validator::make($request->all(), [
                    'title' => 'required',
                    'name' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_policy')->with('error', 'Please follow validation rules.');
        }

       
        /* if ($request->hasFile('name')) {
            $policy_pdf = $request->file('name');
            $file_path = $policy_pdf->store('public/policy');
            if ($file_path) {
                $policy_path = $file_path;
            }
        } */

        
        //21-02-2020
         
        //upload policy pdf
         $policy_path = "";
        if ($request->file('name')) {

            $policy_pdf = $request->file('name');
         
            $original_file_name = explode('.', $policy_pdf->getClientOriginalName());
    
                        $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);
    
    
                        $file_path = $policy_pdf->storeAs('public/policy', $new_file_name); 
                        if ($file_path) {
                            $policy_path = $file_path;
                        }                   
                       
            }

        $policyModel = new Policy();
        $policyModel->title = $request->input('title');
        $policyModel->name = $policy_path;
        $policyModel->created_at = date('Y-m-d h:i:s');
        $policyModel->created_ip = $request->ip();
        $policyModel->updated_at = date('Y-m-d h:i:s');
        $policyModel->updated_ip = $request->ip();
        // 23-06-2021
        $policyModel->implementation_date = date('Y-m-d', strtotime($request->input('implementation_date')));
        $policyModel->amendment_date = date('Y-m-d', strtotime($request->input('amendment_date')));
        $policyModel->addendum_date = date('Y-m-d', strtotime($request->input('addendum_date')));
        $policyModel->effective_from = date('Y-m-d', strtotime($request->input('effective_from_date')));

        if ($policyModel->save()) {
            return redirect()->route('admin.policy')->with('success', 'New policy added successfully.');
        } else {
            return redirect()->route('admin.add_policy')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function edit_policy($id) {

        $this->data['page_title'] = "Edit Policy";
        $this->data['policy_detail'] = Policy::where('id', $id)->get();
        // dd($this->data['policy_detail']);
        // $this->data['policy_detail'] = Policy::where('id', $id['implementation_date'])->get();
        // dd($this->data['policy_detail']);
        // $this->data['implementation_date'] = date('Y-m-d', strtotime($id('implementation_date')));
        // dd($this->data['implementation_date']);
        $check_result = Permissions::checkPermission(21, 2);
        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }

        if ($this->data['policy_detail']->count() == 0) {
            return redirect()->route('admin.policy')->with('error', 'Error Occurred. Try Again!');
        }

        return view('admin.policy.edit_policy', $this->data);
    }

    public function update_policy(Request $request) {

        $validator_normal = Validator::make($request->all(), [
                    'title' => 'required',
                    'name' => 'required'
        ]);

        if ($validator_normal->fails()) {
            return redirect()->route('admin.policy')->with('error', 'Please follow validation rules.');
        }

         //upload policy pdf
         $policy_path = "";
        if ($request->file('name')) {

            $policy_pdf = $request->file('name');
         
            $original_file_name = explode('.', $policy_pdf->getClientOriginalName());
    
                        $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);
    
    
                        $file_path = $policy_pdf->storeAs('public/policy', $new_file_name); 
                        if ($file_path) {
                            $policy_path = $file_path;
                        }   

            $policy_arr = [
                'name' => $policy_path
            ];
            Policy::where('id', $request->input('id'))->update($policy_arr);
        }

        //

        $policyModel = [
            'title' => $request->input('title'),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
            'implementation_date' => date('Y-m-d', strtotime($request->input('implementation_date'))),
            'amendment_date' => date('Y-m-d',strtotime($request->input('amendment_date'))),
            'addendum_date' => date('Y-m-d',strtotime($request->input('addendum_date'))),
            'effective_from' => date('Y-m-d',strtotime($request->input('effective_from_date'))),
        ];

        // dd($policyModel);
        Policy::where('id', $request->input('id'))->update($policyModel);

        return redirect()->route('admin.policy')->with('success', 'Policy successfully updated.');
    }

    // public function delete_policy($id) {
    //     if ($policyModel = Policy::findOrFail($id)) {
    //         $policyModel->delete();
    //         return redirect()->route('admin.policy')->with('success', 'Policy successfully delete.');
    //     }
    //     return redirect()->route('admin.policy')->with('error', 'Error during operation. Try again!');
    // }

    public function revise_policy($id) {

        $this->data['page_title'] = "Revise Policy";
        $this->data['policy_detail'] = Policy::where('id', $id)->get();
        $check_result = Permissions::checkPermission(22, 2);
        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }

        if ($this->data['policy_detail']->count() == 0) {
            return redirect()->route('admin.policy')->with('error', 'Error Occurred. Try Again!');
        }

        return view('admin.policy.revise_policy', $this->data);
    }

    public function update_revise_policy(Request $request) {
        $validator_normal = Validator::make($request->all(), [
                    'revise_number' => 'required',
                    'revise_note' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.policy')->with('error', 'Please follow validation rules.');
        }

        //upload user profile image
       /*  $asset_image = '';
        $policImages = '';

        if ($request->hasFile('revise_policy_image')) {
            $revise_policy_image = $request->file('revise_policy_image');

            $file_path = $revise_policy_image->store('public/policy_image');
            if ($file_path) {
                $policImages=$file_path;
                
            }
        } */

        //upload user profile image
         $policImages = "";
        if ($request->file('revise_policy_image')) {

            $revise_policy_image = $request->file('revise_policy_image');
         
            $original_file_name = explode('.', $revise_policy_image->getClientOriginalName());
    
                        $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);
    
    
                        $file_path = $revise_policy_image->storeAs('public/policy_image', $new_file_name); 
                        if ($file_path) {
                            $policImages=$file_path;
                        }                   
                       
            }

        $revise_number = new RevisePolicy();
        $revise_number->policy_id = $request->input('id');
        $revise_number->revise_number = $request->input('revise_number');
        $revise_number->revise_note = $request->input('revise_note');
        $revise_number->revise_policy_image = $policImages;
        $revise_number->created_at = date('Y-m-d h:i:s');
        $revise_number->updated_at = date('Y-m-d h:i:s');
        if ($revise_number->save()) {
            $policyData = Policy::select('title')->where('id', $request->input('id'))->get()->toArray();
            $data = [
                'policy' => $policyData[0]['title'],
            ];
            $this->common_task->sendRevisePolicyEmail($data);

            return redirect()->route('admin.policy')->with('success', 'Policy added successfully.');
        } else {
            return redirect()->route('admin.policy')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function revise_policy_list() {
        $this->data['page_title'] = "Revised Policy List";
        $access_level = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => 22])->get()->first();
        $check_resultF = Permissions::checkPermission(22, 5);
        if (!$check_resultF) {
            $check_resultP = Permissions::checkPermission(22, 6); // Partial View
            if (!$check_resultP) {
                $check_resultM = Permissions::checkPermission(22, 1); // Only My View
                if (!$check_resultM) {
                    return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
                }
            }
        }
        if (!empty($access_level)) {
            $this->data['access_rule'] = $access_level->access_level;
        }
        return view('admin.policy.revise_policy_list', $this->data);
    }

    //List of revise policy 
    public function get_revise_policy_list() {

        $datatable_fields = array('revise_policy.id', 'revise_policy.revise_number', 'revise_policy.revise_note', 'policy.title', 'revise_policy.status');
        $request = Input::all();
        $conditions_array = [];

        $getfiled = array('revise_policy.id', 'revise_policy.revise_number', 'revise_policy.revise_note', 'policy.title', 'revise_policy.status');
        $table = "revise_policy";

        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'policy';
        $join_str[0]['join_table_id'] = 'revise_policy.policy_id';
        $join_str[0]['from_table_id'] = 'policy.id';

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);

        die();
    }

    // This approve and reject policy by HR
    public function approve_revise_policy($id) {
        $check_result = Permissions::checkPermission(22, 2);

        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }
        if (RevisePolicy::where('id', $id)->update(['status' => 'Approved', 'approved_by' => Auth::user()->id])) {

            $policyData = RevisePolicy::select('policy.title')
                            ->join('policy', 'revise_policy.policy_id', '=', 'policy.id')
                            ->where('revise_policy.id', $id)->get()->toArray();
            $data = [
                'policy' => $policyData[0]['title'],
            ];

            $this->common_task->approveRevisePolicyEmail($data);

            return redirect()->route('admin.revise_policy_list')->with('success', 'Status successfully updated.');
        }
        return redirect()->route('admin.revise_policy_list')->with('error', 'Error during operation. Try again!');
    }

    // This approve and reject policy by HR
    public function reject_revise_policy($id) {
        $check_result = Permissions::checkPermission(22, 2);

        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }
        if (RevisePolicy::where('id', $id)->update(['status' => 'Rejected', 'approved_by' => Auth::user()->id])) {
            return redirect()->route('admin.revise_policy_list')->with('success', 'Status successfully updated.');
        }
        return redirect()->route('admin.revise_policy_list')->with('error', 'Error during operation. Try again!');
    }

    // This is function use for show revise policy images
    public function revise_policy_user_list($id) {
        $this->data['page_title'] = "Revised Policy Images";

        $reviseData = RevisePolicy::select('revise_policy_image', 'id', 'policy_id')->where('id', $id)->get()->toArray();
        /*if (!empty($reviseData)) {
            $imagesData = explode(',', $reviseData[0]['revise_policy_image']);
            $this->data['policyImageList'] = $imagesData;
        } else {
            $this->data['policyImageList'] = [];
        }*/
        $this->data['policyImageList']=$reviseData[0]['revise_policy_image'];
        $this->data['policy_revise_list'] = $reviseData;
        
        $this->data['check_result']= UserRevisePolicy::where(['user_id'=>Auth::user()->id,'policy_id'=>$reviseData[0]['policy_id'],'revise_policy_id'=>$id])
                                        ->get()->count();
        
        return view('admin.policy.revise_policy_user_list', $this->data);
    }

    // approve and reject policy by user
    public function confirm_user_policy(Request $request) {
        $check_result = Permissions::checkPermission(22, 2);
        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }

        $user_revise_number = new UserRevisePolicy();
        $user_revise_number->revise_policy_id = $request->input('id');
        $user_revise_number->policy_id = $request->input('policy_id');
        $user_revise_number->user_id = Auth::user()->id;
        $user_revise_number->status = $request->input('status');
        $user_revise_number->created_at = date('Y-m-d h:i:s');
        $user_revise_number->updated_at = date('Y-m-d h:i:s');

        if ($user_revise_number->save()) {
            return redirect()->route('admin.revise_policy_list')->with('success', 'Revise Policy updated successfully.');
        } else {
            return redirect()->route('admin.revise_policy_list')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function get_policy_user_list($id) {
        $user_select = ['user_revise_policy.status', 'users.name', 'user_revise_policy.policy_id', 'user_revise_policy.revise_policy_id'];
        $this->data['user_list'] = UserRevisePolicy::join('users', 'users.id', '=', 'user_revise_policy.user_id')
                ->where(['user_revise_policy.revise_policy_id' => $id])
                ->get($user_select);
        echo json_encode($this->data['user_list']);
    }
    
    public function company_rules(){
        // dd("Innn Contrtroller");
        $this->data['page_title'] = "Company Rules";
        
        return view('admin.policy.company_rules', $this->data);
    }
    public function add_rule(){
        $this->data['page_title'] = 'Add Rule';
        return view('admin.policy.add_rule', $this->data);
    }
    public function insert_rule(Request $request) {
        $validator_normal = Validator::make($request->all(), [
                    'rule_name' => 'required',
                    'rule_document' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_rule')->with('error', 'Please follow validation rules.');
        }
        
        $policy_path = "";
        if ($request->file('rule_document')) {

            $policy_pdf = $request->file('rule_document');
         
            $original_file_name = explode('.', $policy_pdf->getClientOriginalName());
    
                        $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);
    
    
                        $file_path = $policy_pdf->storeAs('public/rule', $new_file_name); 
                        if ($file_path) {
                            $policy_path = $file_path;
                        }                   
                       
            }

        $policyModel = new CompanyRules();
        $policyModel->rule_name = $request->input('rule_name');
        $policyModel->rule_document = $policy_path;
        $policyModel->created_at = date('Y-m-d h:i:s');
        $policyModel->created_ip = $request->ip();
        $policyModel->updated_at = date('Y-m-d h:i:s');
        $policyModel->updated_ip = $request->ip();

        if(Auth::user()->role == config('constants.REAL_HR'))
        {
            $policyModel->first_approval_status = "Approve";
            $policyModel->first_approval_datetime = date('Y-m-d h:i:s');
        }else if (Auth::user()->role == 1) {
            $policyModel->status == "Approve";
            $policyModel->first_approval_status = "Approve";
            $policyModel->first_approval_datetime = date('Y-m-d h:i:s');
            $policyModel->second_approval_status = "Approve";
            $policyModel->second_approval_datetime = date('Y-m-d h:i:s');
        }

        if ($policyModel->save()) {
            return redirect()->route('admin.company_rules')->with('success', 'New rule added successfully.');
        } else {
            return redirect()->route('admin.add_rule')->with('error', 'Error occurred in insert. Try Again!');
        }
    }
    public function get_companyrule_list(Request $request){
        $list_query = CompanyRules::select('company_rules.*')->get();

        if($request->ajax()){
            // $notify_user_id = User::whereNotIn('role', config('constants.REAL_HR') || 'role', config('constants.SuperUser'))->get(['id'])->pluck('id')->toArray();
            return DataTables::of($list_query)->make(true);
        }

        die();

    }
    public function edit_rules($id) {

        $this->data['page_title'] = "Edit Rules";
        $this->data['policy_detail'] = CompanyRules::where('id', $id)->get();

        if ($this->data['policy_detail']->count() == 0) {
            return redirect()->route('admin.company_rules')->with('error', 'Error Occurred. Try Again!');
        }

        return view('admin.policy.edit_rules', $this->data);
    }

    public function update_rules(Request $request) {

        $validator_normal = Validator::make($request->all(), [
                    'rule_name' => 'required',
                    'rule_document' => 'required'
        ]);

        if ($validator_normal->fails()) {
            return redirect()->route('admin.company_rules')->with('error', 'Please follow validation rules.');
        }

         //upload policy pdf
         $policy_path = "";
        if ($request->file('rule_document')) {

            $policy_pdf = $request->file('rule_document');
         
            $original_file_name = explode('.', $policy_pdf->getClientOriginalName());
    
                        $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);
    
    
                        $file_path = $policy_pdf->storeAs('public/rule', $new_file_name); 
                        if ($file_path) {
                            $policy_path = $file_path;
                        }   

            $policy_arr = [
                'rule_document' => $policy_path
            ];
            CompanyRules::where('id', $request->input('id'))->update($policy_arr);
        }

        //

        $policyModel = [
            'rule_name' => $request->input('rule_name'),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
        ];

        CompanyRules::where('id', $request->input('id'))->update($policyModel);

        return redirect()->route('admin.company_rules')->with('success', 'Rule successfully updated.');
    }
    
    public function delete_rule($id) {
        if ($policyModel = CompanyRules::findOrFail($id)) {
            $policyModel->delete();
            return redirect()->route('admin.company_rules')->with('success', 'Rule successfully delete.');
        }
        return redirect()->route('admin.company_rules')->with('error', 'Error during operation. Try again!');
    }

    public function approve_rule($id) {
        $application = CompanyRules::where('id', '=', e($id))->first();
        $notify_user_data = User::select('name')->get();
        
        // $user_ids = explode(',', $show_rule->user_id);
        if(Auth::user()->role == config('constants.REAL_HR'))
        {
            $application->first_approval_status = "Approve";
            $application->first_approval_datetime = date('Y-m-d h:i:s');
            $application->save();
            // return redirect()->route('admin.company_rules')->with('success', 'Rule Successfully Approved.');
        }else if (Auth::user()->role == 1) {
            
            $application->status == NUll;
            $application->first_approval_status = "Approve";
            $application->second_approval_status = "Approve";
            $application->second_approval_datetime = date('Y-m-d h:i:s');
            $application->status = "Approve";
        }
        if($application->save()){
            if (Auth::user()->role == 1){
                $notify_user_id = User::whereNotIn('role', [config('constants.REAL_HR'),config('constants.SuperUser')])->where('status','Enabled')->get(['id'])->pluck('id')->toArray();
                // Send notification 
                $this->notification_task->companyRuleNotify($notify_user_id);
            }
            return redirect()->route('admin.company_rules')->with('success', 'Rule Successfully Approved.');
        }
    }
    
    public function reject_rule($id) {
        $application = CompanyRules::where('id', '=', e($id))->first();
        if($application && Auth::user()->role == config('constants.REAL_HR'))
        {
            $application->first_approval_status = "Reject";
            $application->first_approval_datetime = date('Y-m-d h:i:s');
            $application->save();
            // return redirect()->route('admin.company_rules')->with('success', 'Rule Successfully Approved.');
        }else if ($application && Auth::user()->role == 1) {
            $application->status == NUll;
            $application->second_approval_status = "Reject";
            $application->second_approval_datetime = date('Y-m-d h:i:s');
            $application->status = "Reject";
            //return redirect()->route('admin.company_rules')->with('error', 'Error during operation. Try again.!');
        }
        if($application->save()){
            return redirect()->route('admin.company_rules')->with('error', 'Rule Successfully Rejected.');
        }
    }

}
