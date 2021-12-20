<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Common_query;
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
use App\PreSignLetter;
use App\ProSignLetter;
use Illuminate\Support\Facades\Mail;
use App\Lib\CommonTask;
use App\Lib\NotificationTask;
use App\LetterHeadRegister;
use App\Employees;

class ProSignedLetterController extends Controller {

    public $data;
    public $notification_task;
    public $common_task;
    private $super_admin;

    public function __construct() {
        $this->data['module_title'] = "Blank Letter-head";
        $this->data['module_link'] = "admin.pro_sign_letter";
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();
        $this->super_admin = \App\User::where('role', 1)->first();
    }

    public function index() {
        $this->data['page_title'] = "Blank Letter Head";
        $access_level = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => 28])->get()->first();
        $this->data['access_rule'] = "";
        if (!empty($access_level)) {
            $this->data['access_rule'] = $access_level->access_level;
        }

        return view('admin.pro_sign_letter.index', $this->data);
    }

    public function get_pro_sign_letter_list() {

        $datatable_fields = array('users.name','pro_sign_letter.title','company.company_name','clients.client_name','project.project_name','pro_sign_letter.other_project_detail','letter_head_register.letter_head_number','vendor.vendor_name','pro_sign_letter.created_at', 'pro_sign_letter.note','pro_sign_letter.status');
        $request = Input::all();
        $conditions_array = ['pro_sign_letter.user_id' => Auth::user()->id];

        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'users';
        $join_str[0]['join_table_id'] = 'users.id';
        $join_str[0]['from_table_id'] = 'pro_sign_letter.user_id';
        
        $join_str[1]['join_type']='';
        $join_str[1]['table'] = 'company';
        $join_str[1]['join_table_id'] ='pro_sign_letter.company_id';
        $join_str[1]['from_table_id'] = 'company.id';

        $join_str[2]['join_type']='left';
        $join_str[2]['table'] = 'vendor';
        $join_str[2]['join_table_id'] ='pro_sign_letter.vendor_id';
        $join_str[2]['from_table_id'] = 'vendor.id';

        $join_str[3]['join_type']='left';
        $join_str[3]['table'] = 'project';
        $join_str[3]['join_table_id'] ='pro_sign_letter.project_id';
        $join_str[3]['from_table_id'] = 'project.id';

        $join_str[4]['join_type']='left';
        $join_str[4]['table'] = 'clients';
        $join_str[4]['join_table_id'] ='pro_sign_letter.client_id';
        $join_str[4]['from_table_id'] = 'clients.id';

        $getfiled = array('pro_sign_letter.id', 'pro_sign_letter.letter_head_image','pro_sign_letter.user_id', 
            'pro_sign_letter.created_at', 'pro_sign_letter.note', 'pro_sign_letter.title', 'users.name as user_name', 
            'pro_sign_letter.first_approval_status', 'pro_sign_letter.status','company.company_name',
            'vendor.vendor_name','project.project_name','pro_sign_letter.other_project_detail','clients.client_name','pro_sign_letter.first_approval_datetime',
            DB::raw("GROUP_CONCAT(letter_head_register.letter_head_number) as letter_head_number"));
        $table = "pro_sign_letter";
        $letter_head_number_join="Yes";
        $group_by="pro_sign_letter.id";
        echo ProSignLetter::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str,[],$letter_head_number_join,$group_by);

        die();
    }

    public function get_pro_sign_letter_list_ajax() {

        $datatable_fields = array( 'users.name','pro_sign_letter.title','company.company_name','clients.client_name','project.project_name','pro_sign_letter.other_project_detail','letter_head_register.letter_head_number','vendor.vendor_name','pro_sign_letter.note','pro_sign_letter.created_at','first_approval_status');
        $request = Input::all();

        if (Auth::user()->role == config('constants.ASSISTANT')) {
            $conditions_array = ['pro_sign_letter.first_approval_status' => 'Pending'];
        }

        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'users';
        $join_str[0]['join_table_id'] = 'users.id';
        $join_str[0]['from_table_id'] = 'pro_sign_letter.user_id';

        $join_str[1]['join_type']='';
        $join_str[1]['table'] = 'company';
        $join_str[1]['join_table_id'] ='pro_sign_letter.company_id';
        $join_str[1]['from_table_id'] = 'company.id';

        $join_str[2]['join_type']='left';
        $join_str[2]['table'] = 'vendor';
        $join_str[2]['join_table_id'] ='pro_sign_letter.vendor_id';
        $join_str[2]['from_table_id'] = 'vendor.id';

        $join_str[3]['join_type']='left';
        $join_str[3]['table'] = 'project';
        $join_str[3]['join_table_id'] ='pro_sign_letter.project_id';
        $join_str[3]['from_table_id'] = 'project.id';

        $join_str[4]['join_type']='left';
        $join_str[4]['table'] = 'clients';
        $join_str[4]['join_table_id'] ='pro_sign_letter.client_id';
        $join_str[4]['from_table_id'] = 'clients.id';

        $getfiled = array('pro_sign_letter.id', 'pro_sign_letter.letter_head_image','pro_sign_letter.user_id', 'pro_sign_letter.title', 
            'pro_sign_letter.created_at','users.name as user_name', 'first_approval_status', 
            'pro_sign_letter.status','pro_sign_letter.note','company.company_name','vendor.vendor_name','clients.client_name',
            'project.project_name','pro_sign_letter.other_project_detail',DB::raw("GROUP_CONCAT(letter_head_register.letter_head_number) as letter_head_number"));
        $table = "pro_sign_letter";
        $letter_head_number_join="Yes";
        $group_by="pro_sign_letter.id";
        echo ProSignLetter::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str,[],$letter_head_number_join,$group_by);

        die();
    }

    public function add_pro_sign_letter_detail() {
        $this->data['page_title'] = 'Add Blank Letter Head ';
        $company_id = Employees::select(['company_id'])->where(['user_id' => Auth::user()->id])->get()->first();
        $this->data['company_id'] = $company_id->company_id;
        $this->data['company_list']= \App\Companies::orderBy('company_name')->where('status','Enabled')->get();
        return view('admin.pro_sign_letter.add_payment', $this->data);
    }

    public function insert_pro_sign_letter(Request $request) {
        $validator_normal = Validator::make($request->all(), [
                    'title' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_pro_sign_letter_detail')->with('error', 'Please follow validation rules.');
        }
        
        //upload document
        $doc_file = '';
        if ($request->hasFile('letter_head_content_file')) {
            $doc = $request->file('letter_head_content_file');
            $file_path = $doc->store('public/letter_head_content_doc');
            if ($file_path) {
                $doc_file = $file_path;
            }
        } else {
            return redirect()->route('admin.add_pro_sign_letter_detail')->with('error', 'Please upload valid word document file.');
        }
        
        $ProSignLetter = new ProSignLetter();

        $ProSignLetter->company_id = $request->input('company_id');
        $ProSignLetter->client_id = $request->input('client_id');
        $ProSignLetter->project_id = $request->input('project_id');

        if (!empty($request->input('vendor_id'))) {
            $ProSignLetter->vendor_id = $request->input('vendor_id');
        }

        if ($request->input('project_id') == config('constants.OTHER_PROJECT_ID')) {
            $ProSignLetter->other_project_detail = $request->input('other_project_detail');
        }

        $ProSignLetter->title = $request->input('title');
        $ProSignLetter->user_id = Auth::user()->id;
        $ProSignLetter->note = $request->input('note');
        $ProSignLetter->created_at = date('Y-m-d H:i:s');
        $ProSignLetter->created_ip = $request->ip();
        $ProSignLetter->updated_at = date('Y-m-d H:i:s');
        $ProSignLetter->updated_ip = $request->ip();
        $ProSignLetter->letter_head_content_file = $doc_file;

        if ($ProSignLetter->save()) {
            return redirect()->route('admin.pro_sign_letter')->with('success', 'Pro Signed Details added successfully.');
        } else {
            return redirect()->route('admin.add_pro_sign_letter_detail')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function edit_pro_sign_letter_detail($id) {

        $this->data['page_title'] = "Edit Signed Payment";
        $this->data['pro_sign_letter_detail'] = ProSignLetter::where('id', $id)->get();
        $company_id = Employees::select(['company_id'])->where(['user_id' => Auth::user()->id])->get()->first();
        $this->data['company_id'] = $company_id->company_id;
        $check_result = Permissions::checkPermission(28, 2);
        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }

        if ($this->data['pro_sign_letter_detail']->count() == 0) {
            return redirect()->route('admin.pro_sign_letter.edit_payment')->with('error', 'Error Occurred. Try Again!');
        }
        $this->data['company_list']= \App\Companies::orderBy('company_name')->where('status','Enabled')->get();
        return view('admin.pro_sign_letter.edit_payment', $this->data);
    }

    public function update_pro_sign_letter(Request $request) {
        $validator_normal = Validator::make($request->all(), [
                    'title' => 'required',
        ]);

        if ($validator_normal->fails()) {
            return redirect()->route('admin.pro_sign_letter')->with('error', 'Please follow validation rules.');
        }
         
        if ($request->input('project_id') == config('constants.OTHER_PROJECT_ID')) {
            $other_project_detail = $request->input('other_project_detail');
        } else {
            $other_project_detail = "";
        }

        $paymentModel = [
            'project_id' => $request->input('project_id'),
            'client_id' => $request->input('client_id'),
            'other_project_detail' => $other_project_detail,
            'vendor_id' => !empty($request->input('vendor_id')) ? $request->input('vendor_id') : "",
            'title' => $request->input('title'),
            'note' => $request->input('note'),
            'first_approval_status' => 'Pending',
            'status' => 'Pending',
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
        ];
        
        //upload document
        
        if ($request->hasFile('letter_head_content_file')) {
            $doc = $request->file('letter_head_content_file');
            $file_path = $doc->store('public/letter_head_content_doc');
            if ($file_path) {
                $paymentModel['letter_head_content_file']=$file_path;
            }
        }

        ProSignLetter::where('id', $request->input('id'))->update($paymentModel);

        return redirect()->route('admin.pro_sign_letter')->with('success', 'Pro Signed Letter successfully updated.');
    }

    public function pro_sign_letter_list() {
        $this->data['page_title'] = "Blank Letter Head";
        $access_level = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => 28])->get()->first();
        if (!empty($access_level)) {
            $this->data['access_rule'] = $access_level->access_level;
        }

        $this->data['users_data'] = User::select('id', 'name')->where('status','Enabled')->get();

        return view('admin.pro_sign_letter.payment_list', $this->data);
    }

    public function approve_pro_sign_letter($id, $assign_letter_user_id = "") {
        /*$check_result = Permissions::checkPermission(28, 2);

        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }*/

        if (Auth::user()->role == config('constants.ASSISTANT')) {
            $updateData = ['assign_letter_user_id' => $assign_letter_user_id, 'first_approval_status' => 'Approved', 'first_approval_id' => Auth::user()->id, 'status' => 'Approved','first_approval_datetime'=> date('Y-m-d h:i:s')];

            $cashApprovealData = ProSignLetter::select('users.name', 'users.email', 'users.id as user_id')
                            ->join('users', 'pro_sign_letter.user_id', '=', 'users.id')
                            ->where('pro_sign_letter.id', $id)->get();
            $data = [
                'username' => $cashApprovealData[0]['name'],
                'email' => $cashApprovealData[0]['email'],
                'doctype' => 'Letter-head',
                'status' => 'Approved',
            ];

            $this->common_task->approveRejectSignEmail($data);

            //send notification about rejected
            $this->notification_task->proSignRequestFirstApprovalNotify([$cashApprovealData[0]['user_id']]);
            $this->notification_task->proSignRequestDeliveryNotify([$assign_letter_user_id]);
        }

        if (ProSignLetter::where('id', $id)->update($updateData)) {
            return redirect()->route('admin.pro_sign_letter_list')->with('success', 'Status successfully updated.');
        }

        return redirect()->route('admin.pro_sign_letter_list')->with('error', 'Error during operation. Try again!');
    }

    public function reject_pro_sign_letter($id, $note) {
        /*$check_result = Permissions::checkPermission(28, 2);

        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }*/

        if (Auth::user()->role == config('constants.ASSISTANT')) {

            $cashApprovealData = ProSignLetter::select('users.name', 'users.email', 'users.id as user_id')
                            ->join('users', 'pro_sign_letter.user_id', '=', 'users.id')
                            ->where('pro_sign_letter.id', $id)->get();
            $data = [
                'username' => $cashApprovealData[0]['name'],
                'email' => $cashApprovealData[0]['email'],
                'doctype' => 'Letter-Head',
                'status' => 'Rejected',
            ];

            $updateData = ['reject_note' => $note, 'first_approval_status' => 'Rejected', 'first_approval_id' => Auth::user()->id, 'status' => 'Rejected'];

            $this->common_task->approveRejectSignEmail($data);
            //send notification about rejected
            $this->notification_task->proSignRequestRejectNotify([$cashApprovealData[0]['user_id']]);
        }

        if (ProSignLetter::where('id', $id)->update($updateData)) {
            return redirect()->route('admin.pro_sign_letter_list')->with('success', 'Status successfully updated.');
        }

        return redirect()->route('admin.pro_sign_letter_list')->with('error', 'Error during operation. Try again!');
    }

    public function letter_head_delivery_pro_list() {

        $datatable_fields = array('users.name','pro_sign_letter.title','company.company_name','clients.client_name','project.project_name','pro_sign_letter.other_project_detail','letter_head_register.letter_head_number','vendor.vendor_name','pro_sign_letter.note', 'pro_sign_letter.created_at', 'pro_sign_letter.status', 'pro_sign_letter.is_deliver_status');
        $request = Input::all();
        $conditions_array = ['assign_letter_user_id' => Auth::user()->id];

        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'users';
        $join_str[0]['join_table_id'] = 'users.id';
        $join_str[0]['from_table_id'] = 'pro_sign_letter.user_id';
        
        $join_str[1]['join_type']='';
        $join_str[1]['table'] = 'company';
        $join_str[1]['join_table_id'] ='pro_sign_letter.company_id';
        $join_str[1]['from_table_id'] = 'company.id';

        $join_str[2]['join_type']='left';
        $join_str[2]['table'] = 'vendor';
        $join_str[2]['join_table_id'] ='pro_sign_letter.vendor_id';
        $join_str[2]['from_table_id'] = 'vendor.id';

        $join_str[3]['join_type']='left';
        $join_str[3]['table'] = 'project';
        $join_str[3]['join_table_id'] ='pro_sign_letter.project_id';
        $join_str[3]['from_table_id'] = 'project.id';

        $join_str[4]['join_type']='left';
        $join_str[4]['table'] = 'clients';
        $join_str[4]['join_table_id'] ='pro_sign_letter.client_id';
        $join_str[4]['from_table_id'] = 'clients.id';

        $getfiled = array('pro_sign_letter.id','pro_sign_letter.created_at', 'letter_head_image','pro_sign_letter.user_id', 
            'pro_sign_letter.title', 'users.name as user_name', 'pro_sign_letter.first_approval_status', 'pro_sign_letter.status', 
            'pro_sign_letter.is_deliver_status', 'pro_sign_letter.letter_head_image','pro_sign_letter.note',
            'company.company_name','vendor.vendor_name','project.project_name','pro_sign_letter.other_project_detail','clients.client_name','pro_sign_letter.first_approval_datetime','pro_sign_letter.second_approval_datetime',
            DB::raw("GROUP_CONCAT(letter_head_register.letter_head_number) as letter_head_number"));
        $table = "pro_sign_letter";
        $letter_head_number_join="Yes";
        $group_by="pro_sign_letter.id";
        echo ProSignLetter::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str,[],$letter_head_number_join,$group_by);

        die();
    }

     public function letter_head_report_list() {

        $datatable_fields = array('users.name','pro_sign_letter.title','company.company_name','project.project_name','pro_sign_letter.other_project_detail', 'letter_head_register.letter_head_ref_no', 'letter_head_register.letter_head_number','vendor.vendor_name','pro_sign_letter.note', 'pro_sign_letter.created_at', 'pro_sign_letter.status', 'pro_sign_letter.is_deliver_status');
        $request = Input::all();
        $conditions_array = [ 'pro_sign_letter.status' => 'Approved'];

        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'users';
        $join_str[0]['join_table_id'] = 'users.id';
        $join_str[0]['from_table_id'] = 'pro_sign_letter.user_id';
        
        $join_str[1]['join_type']='';
        $join_str[1]['table'] = 'company';
        $join_str[1]['join_table_id'] ='pro_sign_letter.company_id';
        $join_str[1]['from_table_id'] = 'company.id';

        $join_str[2]['join_type']='left';
        $join_str[2]['table'] = 'vendor';
        $join_str[2]['join_table_id'] ='pro_sign_letter.vendor_id';
        $join_str[2]['from_table_id'] = 'vendor.id';

        $join_str[3]['join_type']='left';
        $join_str[3]['table'] = 'project';
        $join_str[3]['join_table_id'] ='pro_sign_letter.project_id';
        $join_str[3]['from_table_id'] = 'project.id';

        $getfiled = array('pro_sign_letter.id','pro_sign_letter.created_at', 'letter_head_image','pro_sign_letter.user_id', 
            'pro_sign_letter.title', 'users.name as user_name', 'pro_sign_letter.first_approval_status', 'pro_sign_letter.status', 
            'pro_sign_letter.is_deliver_status', 'pro_sign_letter.letter_head_image','pro_sign_letter.note',
            'company.company_name','vendor.vendor_name','project.project_name','pro_sign_letter.other_project_detail',
            DB::raw("GROUP_CONCAT(letter_head_register.letter_head_number) as letter_head_number"), DB::raw("SUBSTRING_INDEX(letter_head_register.letter_head_ref_no,',',1) as letter_head_ref_no"));
        $table = "pro_sign_letter";
        $letter_head_number_join="Yes";
        $group_by="pro_sign_letter.id";
        echo ProSignLetter::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str,[],$letter_head_number_join,$group_by);

        die();
    }

    
    public function download_normal_letter_head_content($id) {
        $letter_head_file= ProSignLetter::where('id',$id)->get(['letter_head_content_file']);
        if($letter_head_file->count()==0 || !$letter_head_file[0]->letter_head_content_file){
            return redirect()->route('admin.dashboard')->with('error','Requested file not available for download.');
        }
        $file_path=storage_path('app/'.$letter_head_file[0]->letter_head_content_file);
        return response()->download($file_path);

    }

    public function deliver_pro_sign_letter($id) {

        $check_result = Permissions::checkPermission(29, 5);

        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }

        $updateData = ['is_deliver_status' => 'Delivered', 'is_deliver_user_id' => Auth::user()->id];

        if (PreSignLetter::where('id', $id)->update($updateData)) {
            return redirect()->route('admin.letter_head_delivery')->with('success', 'Status successfully updated.');
        }

        return redirect()->route('admin.letter_head_delivery')->with('error', 'Error during operation. Try again!');
    }

    public function confirm_pro_request(Request $request) {
        // dd($request->all());
        $check_result = Permissions::checkPermission(29, 5);

        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }
        $letter_head_number = $request->input('letter_head_numbers');
        foreach($letter_head_number as $letter_head){
            $letter_register_check= LetterHeadRegister::where(['id'=>$letter_head,'is_used'=>'used'])->get()->count();
            if($letter_register_check>0){
                return redirect()->route('admin.letter_head_delivery')->with('error', 'You are trying to add same letter-head number which is already used.');
            }
        }

        //upload policy pdf
        $letter_path = "";
        if ($request->hasFile('letter_head_image')) {
            $letter_head_image = $request->file('letter_head_image');
            $file_path = $letter_head_image->store('public/letter_head');
            if ($file_path) {
                $letter_path = $file_path;
            }
            $letter_arr = [
                'letter_head_image' => $letter_path
            ];

            ProSignLetter::where('id', $request->input('pro_id'))->update($letter_arr);
        }
        
        $strLetterHead = implode(",",$request->input('letter_head_numbers'));

        $updateData = ['is_deliver_status' => 'Delivered', 'is_deliver_user_id' => Auth::user()->id, 'letter_head_number' => $strLetterHead,'second_approval_datetime' => date('Y-m-d h:i:s')];

        if (ProSignLetter::where('id', $request->input('pro_id'))->update($updateData)) {
            
            $ProSignLetterData = ProSignLetter::select(['user_id','company_id','project_id','vendor_id','title','note','other_project_detail','first_approval_id'])->where('id', $request->input('pro_id'))->get()->first();
            
            $vendor_id = !empty($ProSignLetterData->vendor_id)?$ProSignLetterData->vendor_id:"";
            $project_details = !empty($ProSignLetterData->other_project_detail)?$ProSignLetterData->other_project_detail:"";

            $updateLetterdata = ['user_id'=>$ProSignLetterData->user_id,'company_id'=>$ProSignLetterData->company_id,'party_detail'=>$vendor_id,'project_id'=>$ProSignLetterData->project_id,'other_project_detail'=>$project_details,'title'=>$ProSignLetterData->title,'letter_head_content'=>$ProSignLetterData->note,'work_detail'=>$ProSignLetterData->note,'use_type'=>'pro_sign_letter','is_used'=>'used','ref_id'=>$request->input('pro_id'),'issue_date'=>date('Y-m-d H:i:s')];
            
            LetterHeadRegister::whereIn('id', $request->input('letter_head_numbers'))->update($updateLetterdata);
            
            $request_user= User::where('id',$ProSignLetterData->user_id)->get();
            $notify_user_id=[$ProSignLetterData->first_approval_id,$ProSignLetterData->user_id];
            $letter_head_numbers_arr= LetterHeadRegister::whereIn('id',$request->input('letter_head_numbers'))->get(['letter_head_number'])->pluck('letter_head_number')->toArray();
            $letter_head_numbers_list= implode(',', $letter_head_numbers_arr);
            $this->notification_task->LetterheadDeliveryNotify($notify_user_id, $letter_head_numbers_list, $request_user[0]->name);
            $email_list= User::whereIn('id',$notify_user_id)->get(['email'])->pluck('email')->toArray();
            $mail_data=[
                'request_user_name'=>$request_user[0]->name,
                'deliver_user_name'=> Auth::user()->name,
                'letter_head_number'=>$letter_head_numbers_list,
                'email_list'=>$email_list
            ];
            $this->common_task->LetterHeadDeliveryEmail($mail_data);
            return redirect()->route('admin.letter_head_delivery')->with('success', 'Status successfully updated.');
        }

        return redirect()->route('admin.letter_head_delivery')->with('error', 'Error during operation. Try again!');
    }

    public function get_company_letter_head_ref_number(Request $request){
        $pro_data = ProSignLetter::whereId($request->get('id'))->first();
        $letter_data = LetterHeadRegister::select('letter_head_ref_no', 'id')->where('company_id', $pro_data['company_id'])->where(['is_signed' => 'no', 'is_used' => 'not_used', 'is_failed' => 0])->groupBy('letter_head_ref_no')->get()->toArray();
        $html = '<option value="">Select Letter Head Ref Number</option>';
        foreach($letter_data as $key => $value){
            $html .= '<option value="'. $value['letter_head_ref_no']. '">' . $value['letter_head_ref_no'] . '</option>';
        }
        echo $html;die;
    }

    public function get_company_letter_head_number(Request $request){
        $letter_data = LetterHeadRegister::select('letter_head_number', 'id')->where('letter_head_ref_no', $request->get('letter_head_ref_no'))->where(['is_signed' => 'no', 'is_used' => 'not_used', 'is_failed' => 0])->orderBy('letter_head_number','asc')->get()->toArray();
        $html = '';
        foreach ($letter_data as $key => $value) {
            $html .= '<option value="' . $value['id'] . '">' . $value['letter_head_number'] . '</option>';
        }
        echo $html;
        die;
    }
}
