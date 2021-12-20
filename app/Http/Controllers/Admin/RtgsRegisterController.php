<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Lib\NotificationTask;
use Illuminate\Support\Facades\Input;
use App\Common_query;
use App\Companies;
use App\Banks;
use App\User;
use App\Clients;
use App\Vendors;
use App\Projects;
use App\RtgsRegister;
use App\Signed_rtgs_request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Lib\UserActionLogs;

class RtgsRegisterController extends Controller
{
    public $data;
    private $notification_task;
    public $user_action_logs;

    public function __construct() {
        $this->data['module_title'] = "RTGS Register";
        $this->data['module_link'] = "admin.rtgs_register";
        $this->notification_task = new NotificationTask();
        $this->user_action_logs = new UserActionLogs();
    }

    public function index() {
        $this->data['page_title'] = "RTGS Register";
        return view('admin.rtgs_register.index', $this->data);
    }

    public function get_rtgs_register_list() {
        $datatable_fields = array('rtgs_register.id','company.company_name','bank.bank_name','rtgs_no','rtgs_ref_no','is_used','use_type','is_signed','vendor.vendor_name','project.project_name','amount','remark','work_detail','created_at');
        $request = Input::all();
        $conditions_array = [];

        $getfiled = array('rtgs_register.id','company.company_name','bank.bank_name','rtgs_no','rtgs_ref_no','is_used','use_type','is_signed','vendor.vendor_name','project.project_name','amount','remark','work_detail','rtgs_register.created_at');
        $table = "rtgs_register";
        $join_str=[];
        $join_str[0]['join_type']='';
        $join_str[0]['table'] = 'company';
        $join_str[0]['join_table_id'] ='rtgs_register.company_id';
        $join_str[0]['from_table_id'] = 'company.id';

        $join_str[1]['join_type']='';
        $join_str[1]['table'] = 'bank';
        $join_str[1]['join_table_id'] ='rtgs_register.bank_id';
        $join_str[1]['from_table_id'] = 'bank.id';

        $join_str[2]['join_type']='left';
        $join_str[2]['table'] = 'vendor';
        $join_str[2]['join_table_id'] ='rtgs_register.party_detail';
        $join_str[2]['from_table_id'] = 'vendor.id';

        $join_str[3]['join_type']='left';
        $join_str[3]['table'] = 'project';
        $join_str[3]['join_table_id'] ='rtgs_register.project_id';
        $join_str[3]['from_table_id'] = 'project.id';

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);
        die();
    }

    public function blank_rtgs_list(){
        $this->data['page_title'] = "Blank RTGS";
        $this->data['blank_list'] = $this->get_rtgs_blank_list_query();
        // dd($this->data['blank_list']);
        return view('admin.rtgs_register.blank_rtgs_list', $this->data);
    }

    public function get_rtgs_blank_list_query(){

        $all_rtgs = RtgsRegister::select('*')
                ->where('is_failed',0)
                ->where('is_used' ,'not_used')
                ->where('is_signed' ,'no')
                ->groupBy('rtgs_ref_no')
                ->get()->toArray();
                 $rtgs_group = [];
        if($all_rtgs){
        foreach($all_rtgs as $one_rtgs){
             $blank_rtgs = RtgsRegister::select('rtgs_register.id','rtgs_register.rtgs_ref_no','rtgs_no','company.company_name','bank.bank_name','bank.ac_number','bank.bank_name')
                ->join('company', 'rtgs_register.company_id', '=', 'company.id')
                ->join('bank', 'rtgs_register.bank_id', '=', 'bank.id')
                ->where('is_failed',0)
                ->where('is_used' ,'not_used')
                ->where('is_signed' ,'no')
                ->orderBy('rtgs_no', 'ASC')
                ->where('rtgs_ref_no',$one_rtgs['rtgs_ref_no'])
                ->get()->toArray();

        $arr_group = [];

        $start_pointer = $blank_rtgs[0]['rtgs_no'];
        foreach ($blank_rtgs as $key => $value) {
                if($start_pointer == $value['rtgs_no']){
                    $arr_group[] = $value;
                    $start_pointer++;
                }else{
                    array_push($rtgs_group,$arr_group);
                    $arr_group = [];
                    $start_pointer = $value['rtgs_no']+1;
                    $arr_group[] = $value;
                }

        }
        if(!empty($arr_group)){
            array_push($rtgs_group,$arr_group);
        }
        }
        return $rtgs_group;
        }else{
            return [];
        }
    }

    public function get_rtgs_blank_list(){
        $datatable_fields = array('rtgs_ref_no','company.company_name','bank.bank_name','rtgs_no');
        $request = Input::all();
        $conditions_array = ['rtgs_register.is_used' => 'not_used', 'rtgs_register.is_signed' => 'no','rtgs_register.is_failed' => 0];

        $getfiled = array('rtgs_register.id','company.company_name','bank.bank_name','bank.ac_number','rtgs_no','rtgs_ref_no','is_used','use_type','is_signed','vendor.vendor_name','project.project_name','amount','remark','work_detail','rtgs_register.created_at');
        $table = "rtgs_register";
        $join_str=[];
        $join_str[0]['join_type']='';
        $join_str[0]['table'] = 'company';
        $join_str[0]['join_table_id'] ='rtgs_register.company_id';
        $join_str[0]['from_table_id'] = 'company.id';

        $join_str[1]['join_type']='';
        $join_str[1]['table'] = 'bank';
        $join_str[1]['join_table_id'] ='rtgs_register.bank_id';
        $join_str[1]['from_table_id'] = 'bank.id';

        $join_str[2]['join_type']='left';
        $join_str[2]['table'] = 'vendor';
        $join_str[2]['join_table_id'] ='rtgs_register.party_detail';
        $join_str[2]['from_table_id'] = 'vendor.id';

        $join_str[3]['join_type']='left';
        $join_str[3]['table'] = 'project';
        $join_str[3]['join_table_id'] ='rtgs_register.project_id';
        $join_str[3]['from_table_id'] = 'project.id';

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);
        die();
    }

    public function used_rtgs_list(){
        $this->data['page_title'] = "Used RTGS";
         $this->data['companies']       = Companies::orderBy('company_name', 'asc')->pluck('company_name', 'id');
        $this->data['banks']       = Banks::orderBy('bank_name', 'asc')->get(['bank_name', 'id', 'ac_number']);
        $this->data['clients']       = Clients::orderBy('client_name', 'asc')->get(['client_name', 'id', 'location']);
        $this->data['projects']       = Projects::orderBy('project_name', 'asc')->pluck('project_name', 'id');
        $this->data['vendors']       = Vendors::select('vendor_name', 'id', 'company_id')->orderBy('vendor_name', 'asc')->with(['company'])->get();
        $this->data['rtgs_ref_no']       = RtgsRegister::where('is_used', 'used')->where('is_failed', '0')->groupBy('rtgs_ref_no')->pluck('rtgs_ref_no');
        $this->data['rtgs_no']       = RtgsRegister::groupBy('rtgs_no')->pluck('rtgs_no', 'id');

        return view('admin.rtgs_register.used_rtgs_list', $this->data);
    }

    public function get_used_rtgs_ref_no_list(Request $request){
        $company_id = $request->get('company_id');
        $rtgs_ref_no_list = RtgsRegister::where('company_id', $company_id)->where('is_used', 'used')->where('is_failed', '0')->groupBy('rtgs_ref_no')->orderBy('rtgs_ref_no', 'asc')->get('rtgs_ref_no')->toArray();
        $html = "";
        foreach ($rtgs_ref_no_list as $key => $value) {
            $html .= '<option value="' . $value['rtgs_ref_no'] . '">' . $value['rtgs_ref_no'] . '</option>';
        }
        echo $html;
        exit;
    }

    public function get_used_rtgs_number_list(Request $request){
        $rtgs_ref_no = $request->get('rtgs_ref_no');
        $cheque_no_list = RtgsRegister::select('rtgs_no', 'id')->where('rtgs_ref_no', $rtgs_ref_no)->where('is_used', 'used')->where('is_failed', '0')->get()->toArray();

        $html = "";
        foreach ($cheque_no_list as $key => $value) {
            $html .= '<option value="' . $value['rtgs_no'] . '">' . $value['rtgs_no'] . '</option>';
        }
        echo $html;
        exit;
    }

    public function get_used_rtgs_list(){
        $datatable_fields = array('rtgs_ref_no','company.company_name','bank.bank_name','rtgs_no', 'clients.client_name','project.project_name', 'vendor.vendor_name', 'issue_date', 'cl_date', 'amount', 'work_detail', 'remark');
        $request = Input::all();
        //'rtgs_register.is_signed' => 'no',
        $conditions_array = ['rtgs_register.is_used' => 'used', 'rtgs_register.is_failed' => 0];
        $company_id = $request['company_id'];
        $bank_id = $request['bank_id'];
        $client_id = $request['client_id'];
        $project_id = $request['project_id'];
        $vendor_id = $request['vendor_id'];
        $rtgs_ref_no = $request['rtgs_ref_no'];
        $rtgs_no = $request['rtgs_no'];
        $amount = $request['amount'];
        $issue_date = $request['issue_date'];
        $cl_date = $request['cl_date'];

        if ($company_id != '') {
            $conditions_array['rtgs_register.company_id'] = $company_id;
        }

        if ($bank_id != '') {
            $conditions_array['rtgs_register.bank_id'] = $bank_id;
        }

        if ($client_id != '') {
            $conditions_array['clients.id'] = $client_id;
        }

        if ($project_id != '') {
            $conditions_array['rtgs_register.project_id'] = $project_id;
        }
        if ($vendor_id != '') {
            $conditions_array['rtgs_register.party_detail'] = $vendor_id;
        }

        if ($rtgs_ref_no != '') {
            $conditions_array['rtgs_register.rtgs_ref_no'] = $rtgs_ref_no;
        }

        if ($rtgs_no != '') {
            $conditions_array['rtgs_register.rtgs_no'] = $rtgs_no;
        }

        if ($issue_date != '') {
            $new_issue_data = date('Y-m-d', strtotime($issue_date));
            $conditions_array['rtgs_register.issue_date'] = $new_issue_data;
        }
        if ($cl_date != '') {
            $new_cl_date = date('Y-m-d', strtotime($cl_date));
            $conditions_array['rtgs_register.cl_date'] = $new_cl_date;
        }

        if ($amount != '') {
            $conditions_array['rtgs_register.amount'] = $amount;
        }


        $getfiled = array('rtgs_register.id','company.company_name','bank.bank_name','bank.ac_number', 'rtgs_register.rtgs_no', 'rtgs_register.rtgs_ref_no', 'rtgs_register.is_used', 'rtgs_register.use_type', 'rtgs_register.is_signed','vendor.vendor_name','project.project_name', 'rtgs_register.amount', 'rtgs_register.remark', 'rtgs_register.work_detail', 'rtgs_register.issue_date', 'rtgs_register.cl_date' ,'clients.client_name');
        $table = "rtgs_register";
        $join_str=[];
        $join_str[0]['join_type']='';
        $join_str[0]['table'] = 'company';
        $join_str[0]['join_table_id'] ='rtgs_register.company_id';
        $join_str[0]['from_table_id'] = 'company.id';

        $join_str[1]['join_type']='';
        $join_str[1]['table'] = 'bank';
        $join_str[1]['join_table_id'] ='rtgs_register.bank_id';
        $join_str[1]['from_table_id'] = 'bank.id';

        $join_str[2]['join_type']='left';
        $join_str[2]['table'] = 'vendor';
        $join_str[2]['join_table_id'] ='rtgs_register.party_detail';
        $join_str[2]['from_table_id'] = 'vendor.id';

        $join_str[3]['join_type']='left';
        $join_str[3]['table'] = 'project';
        $join_str[3]['join_table_id'] ='rtgs_register.project_id';
        $join_str[3]['from_table_id'] = 'project.id';

        $join_str[4]['join_type'] = 'left';
        $join_str[4]['table'] = 'bank_payment_approval';
        $join_str[4]['join_table_id'] = 'rtgs_register.id';
        $join_str[4]['from_table_id'] = 'bank_payment_approval.rtgs_number';

        $join_str[5]['join_type'] = 'left';
        $join_str[5]['table'] = 'clients';
        $join_str[5]['join_table_id'] = 'clients.id';
        $join_str[5]['from_table_id'] = 'bank_payment_approval.client_id';

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);
        die();
    }

    public function signed_rtgs_list(){
        $this->data['page_title'] = "Signed RTGS";
        $this->data['signed_list'] = $this->get_signed_rtgs_list_query();
        // dd($this->data['signed_list']);
        return view('admin.rtgs_register.signed_rtgs_list', $this->data);
    }

    public function get_signed_rtgs_list_query(){
        $all_rtgs = RtgsRegister::select('*')
                ->where('is_signed' ,'yes')
                ->where('is_used' ,'not_used')
                ->where('is_failed',0)
                ->groupBy('rtgs_ref_no')
                ->get()->toArray();
                 $rtgs_group = [];
        if($all_rtgs){
        foreach($all_rtgs as $one_rtgs){
             $blank_rtgs = RtgsRegister::select('rtgs_register.id','rtgs_register.rtgs_ref_no','rtgs_no','company.company_name','bank.bank_name','bank.ac_number','bank.bank_name')
                ->join('company', 'rtgs_register.company_id', '=', 'company.id')
                ->join('bank', 'rtgs_register.bank_id', '=', 'bank.id')
                ->where('is_signed' ,'yes')
                ->where('is_used' ,'not_used')
                ->where('is_failed',0)
                ->orderBy('rtgs_no', 'ASC')
                ->where('rtgs_ref_no',$one_rtgs['rtgs_ref_no'])
                ->get()->toArray();

        $arr_group = [];

        $start_pointer = $blank_rtgs[0]['rtgs_no'];
        foreach ($blank_rtgs as $key => $value) {
                if($start_pointer == $value['rtgs_no']){
                    $arr_group[] = $value;
                    $start_pointer++;
                }else{
                    array_push($rtgs_group,$arr_group);
                    $arr_group = [];
                    $start_pointer = $value['rtgs_no']+1;
                    $arr_group[] = $value;
                }

        }
        if(!empty($arr_group)){
            array_push($rtgs_group,$arr_group);
        }
        }
        return $rtgs_group;
        }else{
            return [];
        }
    }

    public function get_signed_rtgs_list(){
        $datatable_fields = array('rtgs_ref_no','company.company_name','bank.bank_name','rtgs_no');
        $request = Input::all();
        $conditions_array = ['rtgs_register.is_signed' => 'yes','rtgs_register.is_used' => 'not_used','rtgs_register.is_failed' => 0];
        $getfiled = array('rtgs_register.id','company.company_name','bank.bank_name','bank.ac_number','rtgs_no','rtgs_ref_no','is_used','use_type','is_signed','vendor.vendor_name','project.project_name','amount','remark','work_detail','issue_date');
        $table = "rtgs_register";
        $join_str=[];
        $join_str[0]['join_type']='';
        $join_str[0]['table'] = 'company';
        $join_str[0]['join_table_id'] ='rtgs_register.company_id';
        $join_str[0]['from_table_id'] = 'company.id';

        $join_str[1]['join_type']='';
        $join_str[1]['table'] = 'bank';
        $join_str[1]['join_table_id'] ='rtgs_register.bank_id';
        $join_str[1]['from_table_id'] = 'bank.id';

        $join_str[2]['join_type']='left';
        $join_str[2]['table'] = 'vendor';
        $join_str[2]['join_table_id'] ='rtgs_register.party_detail';
        $join_str[2]['from_table_id'] = 'vendor.id';

        $join_str[3]['join_type']='left';
        $join_str[3]['table'] = 'project';
        $join_str[3]['join_table_id'] ='rtgs_register.project_id';
        $join_str[3]['from_table_id'] = 'project.id';

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);
        die();
    }

    public function add_signed_rtgs(){
        $this->data['page_title']    = 'Add Signed RTGS Request';
        $this->data['module_title'] = "Signed RTGS";
        $this->data['module_link'] = "admin.signed_rtgs_list";
        $this->data['companies']       = Companies::pluck('company_name','id');

        return view('admin.rtgs_register.add_signed_rtgs', $this->data);
    }

    public function get_rtgs_ref(Request $request){
        $bank_id = $request->bank_id;
            $company_id = $request->company_id;

            $cheque_data = RtgsRegister::select('rtgs_ref_no', 'id')
                  ->where(['bank_id' => $bank_id])
                  ->where(['company_id' => $company_id])
                  ->groupBy('rtgs_ref_no')
                  ->get()->toArray();
            echo "<option value=''>Select RTGS No</option>";

            if($cheque_data){
                foreach ($cheque_data as $key => $data_value) {
                    ?>
                    <option value="<?php echo $data_value['rtgs_ref_no'];?>"><?php echo $data_value['rtgs_ref_no'];?></option>
                    <?php
                }
            }
    }

    public function get_unsigned_rtgs_list(Request $request){
        $rtgs_ref_no = $request->rtgs_ref_no;

            $cheque_data = RtgsRegister::select('rtgs_no', 'id')
                ->where(['rtgs_ref_no' => $rtgs_ref_no])
                ->where(['is_signed' => 'no', 'signed_slug' => 'No','is_failed' => 0])
                ->orderBy('rtgs_no', 'ASC')
                ->get()->toArray();
            $html = "";
            foreach ($cheque_data as $key => $data_value) {
                $html .= "<option value=" . $data_value['rtgs_no'] . ">" . $data_value['rtgs_no'] . "</option>";
            }
            echo $html;
            die();
    }

    public function get_remaining_rtgs(Request $request)
    {
            $rtgs_ref_no = $request->rtgs_ref_no;
            $rtgs_no = $request->rtgs_no;
            $rtgs_array = [];
            $rtgs_data = RtgsRegister::select('rtgs_no','rtgs_ref_no', 'id')
                  ->where('rtgs_ref_no' ,$rtgs_ref_no)
                  ->where('rtgs_no' ,'>=', $rtgs_no)
                  ->orderBy('rtgs_no', 'ASC')
                  ->get()->toArray();
            foreach ($rtgs_data as $key => $value) {
                $exist = RtgsRegister::where('rtgs_ref_no',$value['rtgs_ref_no'])->where('rtgs_no',$value['rtgs_no'])->where(['is_signed' => 'no', 'signed_slug' => 'No','is_failed' => 0])->first();
                if (!$exist) {
                    break;   // will leave the foreach loop and also the if statement
                }
                $rtgs_array[] = $value['rtgs_no'];
            }
            $html = "<option value=''>Select End Number</option>";
            foreach ($rtgs_array as $index => $rtgs_no) {
                $html .= "<option value='" . $rtgs_no . "'>" . $rtgs_no . "</option>";
            }
            echo $html;
            die();
    }

    public function signed_rtgs_request(Request $request){

        $validator_normal = Validator::make($request->all(), [
            'company_id' => 'required',
            'bank_id' => 'required',
            'rtgs_ref_no' => 'required',
            'rtgs_start_number' => 'required',
            'rtgs_end_number' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_signed_rtgs')->with('error', 'Please follow validation rules.');
        }
        $data = $request->all();
        // dd($data);

        for($i=$data['rtgs_start_number'];$i<=$data['rtgs_end_number'];$i++) {
            $rtgsModel =  RtgsRegister::where('rtgs_ref_no',$data['rtgs_ref_no'])->where('rtgs_no',$i)->first();
            $rtgsModel->signed_slug = 'Yes';
            $rtgsModel->updated_ip = $request->ip();
            $rtgsModel->save();
        }


        $signedRtgsModel = new Signed_rtgs_request();
        $signedRtgsModel->user_id =Auth::user()->id;
        $signedRtgsModel->rtgs_ref_no = $data['rtgs_ref_no'];
        $signedRtgsModel->rtgs_start_no   = $data['rtgs_start_number'];
        $signedRtgsModel->rtgs_end_no = $data['rtgs_end_number'];
        $signedRtgsModel->status = 'Pending';
        $signedRtgsModel->created_at = date('Y-m-d h:i:s');
        $signedRtgsModel->created_ip = $request->ip();
        $signedRtgsModel->updated_at = date('Y-m-d h:i:s');
        $signedRtgsModel->updated_ip = $request->ip();
        $signedRtgsModel->save();


        if (!empty($signedRtgsModel)) {

            // User Action Log
            $company_name = Companies::whereId($request->get('company_id'))->value('company_name');
            $bank_name = Banks::whereId($request->get('bank_id'))->value('bank_name');
            $add_string = "<br>Company Name: ".$company_name."<br>Bank Name: ".$bank_name;
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => $data['rtgs_ref_no']." RTGS signed request".$add_string,
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            $superUser = user::where('status', 'Enabled')->where('role', config('constants.SuperUser'))->pluck('id')->toArray();
            $rtgs_ref_no = $data['rtgs_ref_no'];
            $this->notification_task->signedRtgsApprovalNotify($rtgs_ref_no, $superUser);

            return redirect()->route('admin.signed_rtgs_list')->with('success', 'Your sign rtgs request is submitted. It will display in this list once approve by Super Admin. You can check the request status from Signed Rtgs Approval menu.');
        } else {
            return redirect()->route('admin.add_signed_rtgs')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function signed_rtgs_approval_requests(){
        $this->data['page_title'] = "Signed RTGS Approval";
        $records = Signed_rtgs_request::leftjoin('users','users.id' ,'=' ,'signed_rtgs_request.user_id')
        ->get(['users.name','signed_rtgs_request.id','signed_rtgs_request.rtgs_ref_no','signed_rtgs_request.rtgs_start_no',
          'signed_rtgs_request.rtgs_end_no','signed_rtgs_request.status','signed_rtgs_request.reject_note']);

        $this->data['records'] = $records;
        return view('admin.rtgs_register.signed_approval_list', $this->data);
    }

    public function accept_approval_rtgs_ref($id ,Request $request){

        if (Signed_rtgs_request::where('id', $id)->update(['status' => 'Accepted'])) {

            $signed_data = Signed_rtgs_request::where('id',$id)->first();

            for($i=$signed_data['rtgs_start_no'];$i<=$signed_data['rtgs_end_no'];$i++) {
                    $rtgsModel =  RtgsRegister::where('rtgs_ref_no',$signed_data['rtgs_ref_no'])->where('rtgs_no',$i)->first();
                    $rtgsModel->is_signed = 'yes';
                    $rtgsModel->updated_at = date('Y-m-d h:i:s');
                    $rtgsModel->save();
                }

                // User Action Log
                $action_data = [
                    'user_id' => Auth::user()->id,
                    'task_body' => $signed_data['rtgs_ref_no']." RTGS signed request approved",
                    'created_ip' => $request->ip(),
                ];
                $this->user_action_logs->action($action_data);

                return redirect()->route('admin.signed_rtgs_approval_requests')->with('success', 'Rtgs signed request successfully Approved.');
        }
        return redirect()->route('admin.signed_rtgs_approval_requests')->with('error', 'Error during operation. Try again!');
    }

    public function reject_approval_rtgs_ref(Request $request){
        // dd($request->all());
        $validator = Validator::make($request->all(), [
                    'rtgs_id' => 'required',
                    'reject_note' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $id = $request_data['rtgs_id'];
        $update_arr = [
               'reject_note' => $request_data['reject_note'],
               'status' => 'Rejected'
        ];

        if (Signed_rtgs_request::where('id', $id)->update($update_arr)) {
            $signed_data = Signed_rtgs_request::where('id',$id)->first();
            for($i=$signed_data['rtgs_start_no'];$i<=$signed_data['rtgs_end_no'];$i++) {
                $rtgsModel =  RtgsRegister::where('rtgs_ref_no',$signed_data['rtgs_ref_no'])->where('rtgs_no',$i)->first();
                $rtgsModel->signed_slug = 'No';
                $rtgsModel->updated_at = date('Y-m-d h:i:s');
                $rtgsModel->updated_ip = $request->ip();
                $rtgsModel->save();
            }

            // User Action Log
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => $signed_data['rtgs_ref_no']." RTGS signed request rejected",
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            return redirect()->route('admin.signed_rtgs_approval_requests')->with('success', 'Rtgs signed request successfully Rejected.');
        }
        return redirect()->route('admin.signed_rtgs_approval_requests')->with('error', 'Error during operation. Try again!');
    }

    public function failed_rtgs_list(){
        $this->data['page_title'] = "Failed RTGS";
        return view('admin.rtgs_register.failed_rtgs_list', $this->data);
    }

    public function get_failed_rtgs_list(){
        $datatable_fields = array('rtgs_ref_no','company.company_name','bank.bank_name','rtgs_no','failed_reason','failed_document');
        $request = Input::all();
        $conditions_array = ['rtgs_register.is_failed' => 1];
        $getfiled = array('rtgs_register.id','company.company_name','bank.bank_name','bank.ac_number','rtgs_no','rtgs_ref_no','is_used','use_type','is_signed','vendor.vendor_name','project.project_name','amount','remark','work_detail','issue_date','failed_reason','failed_document');
        $table = "rtgs_register";
        $join_str=[];
        $join_str[0]['join_type']='';
        $join_str[0]['table'] = 'company';
        $join_str[0]['join_table_id'] ='rtgs_register.company_id';
        $join_str[0]['from_table_id'] = 'company.id';

        $join_str[1]['join_type']='';
        $join_str[1]['table'] = 'bank';
        $join_str[1]['join_table_id'] ='rtgs_register.bank_id';
        $join_str[1]['from_table_id'] = 'bank.id';

        $join_str[2]['join_type']='left';
        $join_str[2]['table'] = 'vendor';
        $join_str[2]['join_table_id'] ='rtgs_register.party_detail';
        $join_str[2]['from_table_id'] = 'vendor.id';

        $join_str[3]['join_type']='left';
        $join_str[3]['table'] = 'project';
        $join_str[3]['join_table_id'] ='rtgs_register.project_id';
        $join_str[3]['from_table_id'] = 'project.id';

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);
        die();
    }

    public function add_failed_rtgs(){
        $this->data['page_title']    = 'Add Failed Rtgs';
        $this->data['module_title'] = "Failed Rtgs";
        $this->data['module_link'] = "admin.failed_rtgs_list";
        $this->data['companies']       = Companies::pluck('company_name','id');

        return view('admin.rtgs_register.add_failed_rtgs', $this->data);
    }

    public function get_unfailed_rtgs(Request $request)
    {

            $bank_id = $request->bank_id;
            $company_id = $request->company_id;

            $cheque_data = RtgsRegister::select('rtgs_ref_no', 'id')
                  ->where(['bank_id' => $bank_id])
                  ->where(['company_id' => $company_id])
                  ->where(['is_failed' => 0])
                  ->groupBy('rtgs_ref_no')
                  ->get()->toArray();
            $html = "<option value=''>Select Rtgs Ref No</option>";

            foreach ($cheque_data as $key => $data_value) {
                $html .= "<option value='" . $data_value['rtgs_ref_no'] . "'>" . $data_value['rtgs_ref_no'] . "</option>";
            }
            echo $html;
            die();

    }

    public function get_unfailed_rtgs_list(Request $request)    //10/06/2020
    {
            $rtgs_ref_no = $request->rtgs_ref_no;

            $cheque_data = RtgsRegister::select('rtgs_no', 'id')
                ->where(['rtgs_ref_no' => $rtgs_ref_no])
                ->where(['is_failed' => 0])
                ->get()->toArray();
            $html = "<option value=''>Select Rtgs Number</option>";

            foreach ($cheque_data as $key => $data_value) {
                $html .= "<option value='" . $data_value['rtgs_no'] . "'>" . $data_value['rtgs_no'] . "</option>";
            }
            echo $html;
            die();

    }

    public function update_failed_rtgs(Request $request){

        $validator_normal = Validator::make($request->all(), [
            'company_id' => 'required',
            'bank_id' => 'required',
            'rtgs_ref_no' => 'required',
            'rtgs_no' => 'required',
            'failed_reason' => 'required',
            'failed_document' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_failed_rtgs')->with('error', 'Please follow validation rules.');
        }
        $data = $request->all();

        if ($request->file('failed_document')) {

            $document_file = $request->file('failed_document');
            $original_file_name = explode('.', $document_file->getClientOriginalName());
            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);

            $file_path = $document_file->storeAs('public/rtgs_failed_docs', $new_file_name);
            if ($file_path) {
                $document_file = $file_path;
            }
        }

            $rtgsModel =  RtgsRegister::where('rtgs_ref_no',$data['rtgs_ref_no'])->where('rtgs_no',$data['rtgs_no'])->first();
            $rtgsModel->failed_document = !empty($document_file) ? $document_file : NULL;
            $rtgsModel->failed_reason = $data['failed_reason'];
            $rtgsModel->is_failed = 1;
            $rtgsModel->updated_ip = $request->ip();
            $rtgsModel->save();

        if (!empty($rtgsModel)) {

            // User Action Log
            $company_name = Companies::whereId($request->get('company_id'))->value('company_name');
            $bank_name = Banks::whereId($request->get('bank_id'))->value('bank_name');
            $add_string = "<br>Company Name: ".$company_name."<br>Bank Name: ".$bank_name;
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => $data['rtgs_ref_no']." RTGS ref no RTGS number ".$data['rtgs_no']." failed".$add_string,
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            return redirect()->route('admin.failed_rtgs_list')->with('success', 'Rtgs Failed successfully.');
        } else {
            return redirect()->route('admin.add_failed_rtgs')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function add_rtgs_register() {
        $this->data['page_title']      = 'Add RTGS Register';
        $this->data['companies']       = Companies::pluck('company_name','id');
        $this->data['banks']           = Banks::pluck('bank_name','id');
        $data = RtgsRegister::select(['rtgs_no'])->orderBy('rtgs_no','desc')->take(1)->get()->toArray();
        $this->data['last_rtgs']    = "";//($data[0]['ch_no']+1);

        return view('admin.rtgs_register.add_rtgs_register', $this->data);
    }

    public function insert_rtgs_register(Request $request) {
        $validator_normal = Validator::make($request->all(), [
            'company_id' => 'required',
            'bank_id' => 'required',

            'rtgs_start_number' => 'required',
            'rtgs_end_number' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_rtgs_register')->with('error', 'Please follow validation rules.');
        }

        $data = RtgsRegister::select(['rtgs_no'])->orderBy('rtgs_no','desc')->take(1)->get()->toArray();
        $last_rtgs    = "";//$data[0]['ch_no'];
        if(!empty($last_rtgs) && $request->input('rtgs_start_number')<intval($last_rtgs))
        {
            return redirect()->route('admin.add_rtgs_register')->with('error', 'Duplicate RTGS number enter please try again !.');
        }

        $amCompanyData = Companies::select('company_short_name')->where(['id' => $request->input('company_id')])->get()->toArray();
        // $rtgs_ref_no = $amCompanyData[0]['company_short_name']."/"."rtgs"."/".date('d-m-Y');

        $amBankData = Banks::select('bank_short_name')->where(['id' => $request->input('bank_id')])->get()->toArray();
        if($amBankData[0]['bank_short_name']){
            $bank_short_name = "/".$amBankData[0]['bank_short_name'];
        }else{
            $bank_short_name = "";
        }

        $rtgs_entry = RtgsRegister::where('company_id',$request->input('company_id'))
            //    ->where('bank_id', $request->input('bank_id'))
               ->whereDate('created_at',date('Y-m-d'))
               ->distinct()
               ->groupBy('rtgs_ref_no')->get()->count();


        if ($rtgs_entry == 0) {
            $append_no = 1;
        } else {
            $append_no = $rtgs_entry + 1;
        }


        $rtgs_ref_no = $amCompanyData[0]['company_short_name'].$bank_short_name."/".date('Y-m-d')."/".$append_no;
        // dd($rtgs_ref_no);
        for($i=$request->input('rtgs_start_number');$i<=$request->input('rtgs_end_number');$i++) {
            $rtgsModel = new RtgsRegister();
            $rtgsModel->company_id = $request->input('company_id');
            $rtgsModel->bank_id    = $request->input('bank_id');
            $rtgsModel->rtgs_ref_no = $rtgs_ref_no;
            $rtgsModel->rtgs_no      = $i; // increment i ++;
            $rtgsModel->created_at = date('Y-m-d h:i:s');
            $rtgsModel->created_ip = $request->ip();
            $rtgsModel->updated_at = date('Y-m-d h:i:s');
            $rtgsModel->save();
        }

        if (!empty($rtgsModel)) {

            // User Action Log
            $company_name = Companies::whereId($request->get('company_id'))->value('company_name');
            $bank_name = Banks::whereId($request->get('bank_id'))->value('bank_name');
            $add_string = "<br>Company Name: ".$company_name."<br>Bank Name: ".$bank_name;
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => $rtgs_ref_no." RTGS register added".$add_string,
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            return redirect()->route('admin.blank_rtgs_list')->with('success', 'New RTGS added successfully.');
        } else {
            return redirect()->route('admin.add_rtgs_register')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function delete_rtgs(Request $request)
    {
        if(!empty($request->input('del_rtgs_ids')))   //this
        {


            if (RtgsRegister::whereIn('id', explode(',',$request->input('del_rtgs_ids')))->delete()) {
                return redirect()->route('admin.rtgs_register')->with('success', 'Delete successfully updated.');
            }
        }

        return redirect()->route('admin.rtgs_register')->with('error', 'Error during operation. Try again!');
    }

    public function change_rtgs_status($id, $status) {
        if (RtgsRegister::where('id', $id)->update(['is_signed' => $status])) {
            return redirect()->route('admin.rtgs_register')->with('success', 'Status successfully updated.');
        }
        return redirect()->route('admin.rtgs_register')->with('error', 'Error during operation. Try again!');
    }

    public function signed_rtgs(Request $request)
    {
        if(!empty($request->input('signed_rtgs_ids')))   //this
        {
            if (RtgsRegister::whereIn('id', explode(',',$request->input('signed_rtgs_ids')))->update(['is_signed' =>'yes'])) {
                return redirect()->route('admin.rtgs_register')->with('success', 'RTGS successfully updated.');
            }
        }

        return redirect()->route('admin.rtgs_register')->with('error', 'Error during operation. Try again!');
    }


    //===========================================NEXT

    public function rtgs_use_report(Request $request)
    {
        $this->data['page_title'] = "RTGS Use Report";
        $this->data['user']=  User::where("status","Enabled")->get()->pluck('name', 'id');
        $this->data['records'] = [];
        $this->data['selectedUser'] = [];
        $this->data['date'] = "";
        $this->data['report_type'] = "";
        $this->data['csv_data'] = "javascript:void(0);";
        $reportType = $request->get('report_type');

        if($request->method() == 'POST'){

            $date = $request->get('date');
            $this->data['date'] = $request->get('date');
            $mainDate = explode("-", $date);
            $strFirstdate = str_replace("/", "-", $mainDate[0]);
            $strLastdate = str_replace("/", "-", $mainDate[1]);
            $first_date = date('Y-m-d h:m:s', strtotime($strFirstdate.' -1 day'));
            $second_date = date('Y-m-d h:m:s', strtotime($strLastdate.' +1 day'));

            $rtgsData = RtgsRegister::join('company', 'rtgs_register.company_id', '=', 'company.id')
                            ->join('bank', 'rtgs_register.bank_id', '=', 'bank.id')
                            ->leftJoin('vendor', 'rtgs_register.party_detail', '=', 'vendor.id')
                            ->leftJoin('project', 'rtgs_register.project_id', '=', 'project.id')
                            ->where('rtgs_register.is_used','used')
                            ->whereBetween('rtgs_register.created_at', [$first_date, $second_date])
                            ->orderBy('rtgs_register.id', 'ASC')
                            ->get(['rtgs_register.*','company.company_name','bank.bank_name','vendor.vendor_name','project.project_name']);

            $this->data['records'] = $rtgsData;
            $columnName = array('Sr. No', 'Company', 'Bank', 'RTGS Ref No', 'RTGS No', 'Project Name', 'Vendor', 'Amount','Work Detail','Remark','created_at','Is Used','Is Signed');
            if (!empty($rtgsData[0])) {
                $csvData = $this->generateCsvFiles('rtgs_report', $columnName, $rtgsData);  //func outPut.
                $this->data['csv_data'] = $csvData;
            }
        }

        return view('admin.rtgs_register.rtgs_use_report', $this->data);
    }

    public function generateCsvFiles($filename, $columnName, $rptData) {

       $name = date('D-M-Y h:m:s') . ' ' . $filename . '.csv';

        $file = fopen(storage_path('app/public/reports/rtgs_report/') . $name, 'wb');

        if ($filename == "rtgs_report") {

            fputcsv($file, $columnName);
            $data = [];
            foreach ($rptData as $k => $rowData) {

                $data[] = array($k + 1,
                $rowData->company_name,
                $rowData->bank_name,
                $rowData->rtgs_ref_no,
                $rowData->rtgs_no,
                $rowData->project_name,
                $rowData->vendor_name,
                $rowData->amount,
                $rowData->work_detail,
                $rowData->remark,
                $rowData->created_at,
                $rowData->is_used,
                $rowData->is_signed
                );
            }
        }

        foreach ($data as $row) {
            fputcsv($file, $row);
        }

        return asset('storage/'.str_replace('public/','','reports/rtgs_report/'.$name));
    }
}
