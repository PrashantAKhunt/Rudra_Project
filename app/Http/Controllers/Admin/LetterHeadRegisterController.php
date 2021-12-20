<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Lib\NotificationTask;
use Illuminate\Support\Facades\Input;
use App\Common_query;
use App\Companies;
use App\Banks;
use App\LetterHeadChequeRegister;
use App\LetterHeadRegister;
use App\Signed_letter_head_request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\User;
use DB;
use App\Lib\UserActionLogs;

class LetterHeadRegisterController extends Controller
{
    public $data;
    public $notification_task;
    public $user_action_logs;

    public function __construct() {
        $this->data['module_title'] = "Letter Head Register";
        $this->data['module_link'] = "admin.letter_head_register";
        $this->notification_task = new NotificationTask();
        $this->user_action_logs = new UserActionLogs();
    }

    public function index() {
        $this->data['page_title'] = "Letter Head Register";
        return view('admin.letter_head_register.index', $this->data);
    }


    public function get_letter_head_register_list() {
        $datatable_fields = array('letter_head_register.id','company.company_name','letter_head_number','letter_head_ref_no','is_signed','is_used','work_detail','issue_date','letter_head_register.letter_head_content','letter_head_register.title','vendor.vendor_name','project.project_name','other_project_detail','is_used');
        $request = Input::all();
        //$conditions_array = ['letter_head_register.is_used'=>'not_used'];
        $conditions_array = [];

        $getfiled =array('letter_head_register.id','company.company_name','letter_head_number','letter_head_ref_no','is_signed','is_used','work_detail','issue_date','letter_head_register.letter_head_content','letter_head_register.title','vendor.vendor_name','project.project_name','other_project_detail','is_used');
        $table = "letter_head_register";
        $join_str=[];
        $join_str[0]['join_type']='';
        $join_str[0]['table'] = 'company';
        $join_str[0]['join_table_id'] ='letter_head_register.company_id';
        $join_str[0]['from_table_id'] = 'company.id';

        $join_str[1]['join_type']='left';
        $join_str[1]['table'] = 'vendor';
        $join_str[1]['join_table_id'] ='letter_head_register.party_detail';
        $join_str[1]['from_table_id'] = 'vendor.id';

        $join_str[2]['join_type']='left';
        $join_str[2]['table'] = 'project';
        $join_str[2]['join_table_id'] ='letter_head_register.project_id';
        $join_str[2]['from_table_id'] = 'project.id';

        // $join_str[1]['join_type']='';
        // $join_str[1]['table'] = 'bank';
        // $join_str[1]['join_table_id'] ='letter_head_cheque_register.bank_id';
        // $join_str[1]['from_table_id'] = 'bank.id';

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);
        die();
    }

    public function add_letter_head_register() {
        $this->data['page_title'] = 'Add Letter Head Register';
        $this->data['companies']  = Companies::pluck('company_name','id');
        $this->data['banks']      = Banks::pluck('bank_name','id');

        $amLetterData = LetterHeadRegister::select('letter_head_number')->orderBy('id','DESC')->get()->toArray();
        if(!empty($amLetterData[0])) {
            $this->data['last_letter_head_number'] = $amLetterData[0]['letter_head_number']+1;
        }
        else {
            $this->data['last_letter_head_number'] = 1;
        }

        return view('admin.letter_head_register.add_letter_head_register', $this->data);
    }

    public function insert_letter_head_register(Request $request) {
        $validator_normal = Validator::make($request->all(), [
            'company_id' => 'required',
            'letter_head_start_number' => 'required',
            'letter_head_end_number' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_letter_head_register')->with('error', 'Please follow validation rules.');
        }

        $amCompanyData = Companies::select('company_short_name')->where(['id' => $request->input('company_id')])->get()->toArray();
        // $letter_ref_no = $amCompanyData[0]['company_short_name']."/".date('d-m-Y');

        $letter_entry = LetterHeadRegister::where('company_id',$request->input('company_id'))
               ->whereDate('created_at',date('Y-m-d'))
               ->distinct()
               ->groupBy('letter_head_ref_no')->get()->count();

        if ($letter_entry == 0) {
            $append_no = 1;
        } else {
            $append_no = $letter_entry + 1;
        }

        $letter_ref_no = $amCompanyData[0]['company_short_name']."/".date('Y-m-d')."/".$append_no;
        // dd($letter_ref_no);
        for($i=$request->input('letter_head_start_number');$i<=$request->input('letter_head_end_number');$i++) {
            $letterHeadModel = new LetterHeadRegister();
            $letterHeadModel->company_id = $request->input('company_id');
            $letterHeadModel->letter_head_ref_no = $letter_ref_no;
            $letterHeadModel->letter_head_number = $i; // increment i ++;
            $letterHeadModel->created_at = date('Y-m-d h:i:s');
            $letterHeadModel->created_ip = $request->ip();
            $letterHeadModel->updated_at = date('Y-m-d h:i:s');
            $letterHeadModel->save();
        }

        if (!empty($letterHeadModel)) {

            // User Action Log
            $company_name = Companies::whereId($request->get('company_id'))->value('company_name');
            $add_string = "<br>Company Name: ".$company_name;
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => $letter_ref_no." letter head added".$add_string,
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            return redirect()->route('admin.blank_letter_head_list')->with('success', 'New letter head added successfully.');
        } else {
            return redirect()->route('admin.add_letter_head_register')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function delete_letter_head_register($id) {
        if (LetterHeadRegister::where('id', $id)->delete()) {
            return redirect()->route('admin.letter_head_register')->with('success', 'Delete successfully updated.');
        }
    return redirect()->route('admin.letter_head_register')->with('error', 'Error during operation. Try again!');
    }

    public function get_letter_head_bank_list()
    {
        if(!empty($_GET['company_id'])) {
           $company_id = $_GET['company_id'];
           $bank_data = Banks::select('bank_name','id')->where(['company_id' => $company_id])->get()->toArray();
           $html = "";
           foreach ($bank_data as $key => $bank_data_value) {
                $html.= "<option value=".$bank_data_value['id'].">".$bank_data_value['bank_name']."</option>";
           }
           echo  $html;
           die();
        }
    }

    public function delete_letter_head_cheques(Request $request)
    {
        if(!empty($request->input('del_cheque_ids')))
        {
            if (LetterHeadChequeRegister::whereIn('id', explode(',',$request->input('del_cheque_ids')))->delete()) {
                return redirect()->route('admin.letter_head_register')->with('success', 'Delete successfully updated.');
            }
        }

        return redirect()->route('admin.letter_head_register')->with('error', 'Error during operation. Try again!');
    }

    public function change_letter_head_status($id, $status)
    {
        if (LetterHeadRegister::where('id', $id)->update(['is_signed' => $status])) {
            return redirect()->route('admin.letter_head_register')->with('success', 'Status successfully updated.');
        }
        return redirect()->route('admin.letter_head_register')->with('error', 'Error during operation. Try again!');
    }

    public function signed_letter_head(Request $request)
    {
        if(!empty($request->input('signed_letter_head_ids')))
        {
            if (LetterHeadRegister::whereIn('id', explode(',',$request->input('signed_letter_head_ids')))->update(['is_signed' =>'yes'])) {
                return redirect()->route('admin.letter_head_register')->with('success', 'Letter Head successfully updated.');
            }
        }

        return redirect()->route('admin.letter_head_register')->with('error', 'Error during operation. Try again!');
    }

    public function blank_letter_head_list(){
        $this->data['page_title'] = "Blank Letter Head";
        $this->data['blank_list'] = $this->get_blank_letter_head_list_query();
        // dd($this->data['blank_list']);
        return view('admin.letter_head_register.blank_letter_head_list', $this->data);
    }

    public function get_blank_letter_head_list_query(){
        $all_letter_head = LetterHeadRegister::select('*')
                ->where('is_failed',0)
                ->where('is_used' ,'not_used')
                ->where('is_signed' ,'no')
                ->groupBy('letter_head_ref_no')
                ->get()->toArray();
        $letter_group = [];
        if($all_letter_head){
            foreach($all_letter_head as $one_rtgs){
             $blank_letter = LetterHeadRegister::select('letter_head_register.id','letter_head_register.letter_head_ref_no','letter_head_number','company.company_name')
                ->join('company', 'letter_head_register.company_id', '=', 'company.id')
                ->where('is_failed',0)
                ->where('is_used' ,'not_used')
                ->where('is_signed' ,'no')
                ->orderBy('letter_head_number', 'ASC')
                ->where('letter_head_ref_no',$one_rtgs['letter_head_ref_no'])
                ->get()->toArray();

                $arr_group = [];
                $start_pointer = $blank_letter[0]['letter_head_number'];
                foreach ($blank_letter as $key => $value) {
                        if($start_pointer == $value['letter_head_number']){
                            $arr_group[] = $value;
                            $start_pointer++;
                        }else{
                            array_push($letter_group,$arr_group);
                            $arr_group = [];
                            $start_pointer = $value['letter_head_number']+1;
                            $arr_group[] = $value;
                        }

                }
                    if(!empty($arr_group)){
                        array_push($letter_group,$arr_group);
                    }
                }
                return $letter_group;
        }else{
             return [];
        }
    }

    public function get_blank_letter_head_list(){
        $datatable_fields = array('letter_head_ref_no','company.company_name','letter_head_number');
        $request = Input::all();
        $conditions_array = ['letter_head_register.is_used' => 'not_used', 'letter_head_register.is_signed' => 'no','letter_head_register.is_failed' => 0];

        $getfiled =array('letter_head_register.id','company.company_name','letter_head_number','letter_head_ref_no','is_signed','is_used','work_detail','issue_date','letter_head_register.letter_head_content','letter_head_register.title','vendor.vendor_name','project.project_name','other_project_detail','is_used');
        $table = "letter_head_register";
        $join_str=[];
        $join_str[0]['join_type']='';
        $join_str[0]['table'] = 'company';
        $join_str[0]['join_table_id'] ='letter_head_register.company_id';
        $join_str[0]['from_table_id'] = 'company.id';

        $join_str[1]['join_type']='left';
        $join_str[1]['table'] = 'vendor';
        $join_str[1]['join_table_id'] ='letter_head_register.party_detail';
        $join_str[1]['from_table_id'] = 'vendor.id';

        $join_str[2]['join_type']='left';
        $join_str[2]['table'] = 'project';
        $join_str[2]['join_table_id'] ='letter_head_register.project_id';
        $join_str[2]['from_table_id'] = 'project.id';

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);
        die();
    }

    public function signed_letter_head_list(){
        $this->data['page_title'] = "Signed Letter Head";
        $this->data['signed_list'] = $this->get_signed_letter_head_list_query();
        // dd($this->data['signed_list']);
        return view('admin.letter_head_register.signed_letter_head_list', $this->data);
    }

    public function get_signed_letter_head_list_query(){
        $all_letter_head = LetterHeadRegister::select('*')
                ->where('is_signed' ,'yes')
                ->where('is_used' ,'not_used')
                ->where('is_failed',0)
                ->groupBy('letter_head_ref_no')
                ->get()->toArray();
                 $letter_group = [];
        $letter_group = [];
        if($all_letter_head){
            foreach($all_letter_head as $one_rtgs){
             $blank_letter = LetterHeadRegister::select('letter_head_register.id','letter_head_register.letter_head_ref_no','letter_head_number','company.company_name')
                ->join('company', 'letter_head_register.company_id', '=', 'company.id')
                ->where('is_signed' ,'yes')
                ->where('is_used' ,'not_used')
                ->where('is_failed',0)
                ->orderBy('letter_head_number', 'ASC')
                ->where('letter_head_ref_no',$one_rtgs['letter_head_ref_no'])
                ->get()->toArray();

                $arr_group = [];
                $start_pointer = $blank_letter[0]['letter_head_number'];
                foreach ($blank_letter as $key => $value) {
                        if($start_pointer == $value['letter_head_number']){
                            $arr_group[] = $value;
                            $start_pointer++;
                        }else{
                            array_push($letter_group,$arr_group);
                            $arr_group = [];
                            $start_pointer = $value['letter_head_number']+1;
                            $arr_group[] = $value;
                        }

                }
                    if(!empty($arr_group)){
                        array_push($letter_group,$arr_group);
                    }
                }
                return $letter_group;
        }else{
             return [];
        }
    }

    public function get_signed_letter_head_list(){
        $datatable_fields = array('letter_head_ref_no','company.company_name','letter_head_number');
        $request = Input::all();
        $conditions_array = ['letter_head_register.is_signed' => 'yes','letter_head_register.is_used' => 'not_used','letter_head_register.is_failed' => 0];

        $getfiled =array('letter_head_register.id','company.company_name','letter_head_number','letter_head_ref_no','is_signed','is_used','work_detail','issue_date','letter_head_register.letter_head_content','letter_head_register.title','vendor.vendor_name','project.project_name','other_project_detail','is_used');
        $table = "letter_head_register";
        $join_str=[];
        $join_str[0]['join_type']='';
        $join_str[0]['table'] = 'company';
        $join_str[0]['join_table_id'] ='letter_head_register.company_id';
        $join_str[0]['from_table_id'] = 'company.id';

        $join_str[1]['join_type']='left';
        $join_str[1]['table'] = 'vendor';
        $join_str[1]['join_table_id'] ='letter_head_register.party_detail';
        $join_str[1]['from_table_id'] = 'vendor.id';

        $join_str[2]['join_type']='left';
        $join_str[2]['table'] = 'project';
        $join_str[2]['join_table_id'] ='letter_head_register.project_id';
        $join_str[2]['from_table_id'] = 'project.id';

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);
        die();
    }

    public function add_signed_letter_head(){
        $this->data['page_title'] = "Add Signed Letter Head";
        $this->data['companies']  = Companies::pluck('company_name','id');
        $this->data['banks']      = Banks::pluck('bank_name','id');
        return view('admin.letter_head_register.add_signed_letter_head', $this->data);
    }

    public function get_letter_head_ref_no(Request $request){
        $company_id = $request->company_id;

        $cheque_data = LetterHeadRegister::select('letter_head_ref_no', 'id')
                  ->where(['company_id' => $company_id])
                  ->groupBy('letter_head_ref_no')
                  ->get()->toArray();
            echo "<option value=''>Select Letter Head Ref No</option>";

            if($cheque_data){
                foreach ($cheque_data as $key => $data_value) {
                    ?>
                    <option value="<?php echo $data_value['letter_head_ref_no'];?>"><?php echo $data_value['letter_head_ref_no'];?></option>
                    <?php
                }
            }
    }

    public function get_unsigned_letter_head_list(Request $request){
        // dd($request->all());
            $letter_head_ref_no = $request->letter_head_ref_no;

            $letter_data = LetterHeadRegister::select('letter_head_number', 'id')
                ->where(['letter_head_ref_no' => $letter_head_ref_no])
                ->where(['is_signed' => 'no', 'signed_slug' => 'No','is_failed' => 0])
                ->orderBy('letter_head_number', 'ASC')
                ->get()->toArray();
            $html = '<option value="">Select Letter Head Start Number</option>';
            foreach ($letter_data as $key => $data_value) {
                $html .= "<option value=" . $data_value['letter_head_number'] . ">" . $data_value['letter_head_number'] . "</option>";
            }
            echo $html;
            die();
    }

    public function get_unfailed_letter_head_list(Request $request){
        // dd($request->all());
            $letter_head_ref_no = $request->letter_head_ref_no;

            $letter_data = LetterHeadRegister::select('letter_head_number', 'id')
                ->where(['letter_head_ref_no' => $letter_head_ref_no])
                ->where(['is_failed' => 0])
                ->get()->toArray();
            $html = "";

            foreach ($letter_data as $key => $data_value) {
                $html .= "<option value='" . $data_value['letter_head_number'] . "'>" . $data_value['letter_head_number'] . "</option>";
            }
            echo $html;
            die();
    }

    public function get_remaining_letter_head_list(Request $request){
        $letter_head_ref_no = $request->letter_head_ref_no;
        $letter_head_start_number = $request->letter_head_start_number;
        $rtgs_array = [];
        $rtgs_data = LetterHeadRegister::select('letter_head_number','letter_head_ref_no', 'id')
                ->where('letter_head_ref_no' ,$letter_head_ref_no)
                ->where('letter_head_number' ,'>=', $letter_head_start_number)
                ->orderBy('letter_head_number', 'ASC')
                ->get()->toArray();
        foreach ($rtgs_data as $key => $value) {
            $exist = LetterHeadRegister::where('letter_head_ref_no',$value['letter_head_ref_no'])->where('letter_head_number',$value['letter_head_number'])->where(['is_signed' => 'no', 'signed_slug' => 'No','is_failed' => 0])->first();
            if (!$exist) {
                break;   // will leave the foreach loop and also the if statement
            }
            $rtgs_array[] = $value['letter_head_number'];
        }
        $html = "<option value=''>Select Letter Head End Number</option>";
        foreach ($rtgs_array as $index => $letter_head_number) {
            $html .= "<option value='" . $letter_head_number . "'>" . $letter_head_number . "</option>";
        }
        echo $html;
        die();
    }

    public function letter_head_register_request(Request $request){
        $validator_normal = Validator::make($request->all(), [
            'company_id' => 'required',
            'letter_head_ref_no' => 'required',
            'letter_head_start_number' => 'required',
            'letter_head_end_number' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_signed_letter_head')->with('error', 'Please follow validation rules.');
        }
        $data = $request->all();
        for($i=$data['letter_head_start_number'];$i<=$data['letter_head_end_number'];$i++) {
            $letterModel =  LetterHeadRegister::where('letter_head_ref_no',$data['letter_head_ref_no'])->where('letter_head_number',$i)->first();
            $letterModel->signed_slug = 'Yes';
            $letterModel->updated_ip = $request->ip();
            $letterModel->save();
        }

        $signedLetterModel = new Signed_letter_head_request();
        $signedLetterModel->user_id =Auth::user()->id;
        $signedLetterModel->letter_head_ref_no = $data['letter_head_ref_no'];
        $signedLetterModel->letter_head_start_number   = $data['letter_head_start_number'];
        $signedLetterModel->letter_head_end_number = $data['letter_head_end_number'];
        $signedLetterModel->status = 'Pending';
        $signedLetterModel->created_at = date('Y-m-d h:i:s');
        $signedLetterModel->created_ip = $request->ip();
        $signedLetterModel->updated_at = date('Y-m-d h:i:s');
        $signedLetterModel->updated_ip = $request->ip();
        $signedLetterModel->save();
        if (!empty($signedLetterModel)) {

            // User Action Log
            $company_name = Companies::whereId($request->get('company_id'))->value('company_name');
            $add_string = "<br>Company Name: ".$company_name;
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => $data['letter_head_ref_no']." signed letter head request".$add_string,
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            $superUser = user::where('status', 'Enabled')->where('role', config('constants.SuperUser'))->pluck('id')->toArray();
            $letter_head_ref_no = $data['letter_head_ref_no'];
            $this->notification_task->signedLetterHeadApprovalNotify($letter_head_ref_no, $superUser);

            return redirect()->route('admin.signed_letter_head_list')->with('success', 'Your sign latter head request is submitted. It will display in this list once approve by Super Admin. You can check the request status from Signed Letter Head Approval menu.');
        } else {
            return redirect()->route('admin.add_signed_letter_head')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function signed_letter_head_approval(){
        $this->data['page_title'] = "Signed Letter Head Approval";
        $records = Signed_letter_head_request::leftjoin('users','users.id' ,'=' ,'signed_letter_head_request.user_id')
        ->get(['users.name','signed_letter_head_request.id','signed_letter_head_request.letter_head_ref_no','signed_letter_head_request.letter_head_start_number',
          'signed_letter_head_request.letter_head_end_number','signed_letter_head_request.status','signed_letter_head_request.reject_note']);

        $this->data['records'] = $records;
        return view('admin.letter_head_register.signed_letter_head_approval', $this->data);
    }

    public function accept_approval_letter_head_ref($id ,Request $request){

        if (Signed_letter_head_request::where('id', $id)->update(['status' => 'Accepted'])) {

            $signed_data = Signed_letter_head_request::where('id',$id)->first();

            for($i=$signed_data['letter_head_start_number'];$i<=$signed_data['letter_head_end_number'];$i++) {
                    $rtgsModel =  LetterHeadRegister::where('letter_head_ref_no',$signed_data['letter_head_ref_no'])->where('letter_head_number',$i)->first();
                    $rtgsModel->is_signed = 'yes';
                    $rtgsModel->updated_at = date('Y-m-d h:i:s');
                    $rtgsModel->save();
                }

                // User Action Log
                $action_data = [
                    'user_id' => Auth::user()->id,
                    'task_body' => $signed_data['letter_head_ref_no']." signed letter head request approved",
                    'created_ip' => $request->ip(),
                ];
                $this->user_action_logs->action($action_data);

                return redirect()->route('admin.signed_letter_head_approval')->with('success', 'Letter head signed request successfully Approved.');
        }
        return redirect()->route('admin.signed_letter_head_approval')->with('error', 'Error during operation. Try again!');
    }

    public function reject_approval_letter_head_ref(Request $request){
        $validator = Validator::make($request->all(), [
                    'letter_head_id' => 'required',
                    'reject_note' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $id = $request_data['letter_head_id'];
        $update_arr = [
            'reject_note' => $request_data['reject_note'],
            'status' => 'Rejected'
        ];

        if (Signed_letter_head_request::where('id', $id)->update($update_arr)) {
            $signed_data = Signed_letter_head_request::where('id',$id)->first();
            for($i=$signed_data['letter_head_start_number'];$i<=$signed_data['letter_head_end_number'];$i++) {
                $rtgsModel =  LetterHeadRegister::where('letter_head_ref_no',$signed_data['letter_head_ref_no'])->where('letter_head_number',$i)->first();
                $rtgsModel->signed_slug = 'No';
                $rtgsModel->updated_at = date('Y-m-d h:i:s');
                $rtgsModel->updated_ip = $request->ip();
                $rtgsModel->save();
            }

            // User Action Log
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => $signed_data['letter_head_ref_no']." signed letter head request rejected",
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            return redirect()->route('admin.signed_letter_head_approval')->with('success', 'Letter head signed request successfully Rejected.');
        }
        return redirect()->route('admin.signed_letter_head_approval')->with('error', 'Error during operation. Try again!');
    }

    public function used_letter_head_list(){
        $this->data['page_title'] = "Used Letter Head";
        return view('admin.letter_head_register.used_letter_head_list', $this->data);
    }
    public function get_used_letter_head_list(){
        // $datatable_fields = array('letter_head_ref_no','company.company_name','letter_head_number');
        $datatable_fields = array('company.company_name','project.project_name','other_project_detail','letter_head_register.title','letter_head_register.letter_head_content','vendor.vendor_name','letter_head_number','letter_head_ref_no','work_detail');
        $request = Input::all();
        $conditions_array = ['letter_head_register.is_used' => 'used','letter_head_register.is_failed' => 0];;

        $getfiled =array('letter_head_register.id','company.company_name','letter_head_number','letter_head_ref_no','is_signed','is_used','work_detail','issue_date','letter_head_register.letter_head_content','letter_head_register.title','vendor.vendor_name','project.project_name','other_project_detail','is_used');
        $table = "letter_head_register";
        $join_str=[];
        $join_str[0]['join_type']='';
        $join_str[0]['table'] = 'company';
        $join_str[0]['join_table_id'] ='letter_head_register.company_id';
        $join_str[0]['from_table_id'] = 'company.id';

        $join_str[1]['join_type']='left';
        $join_str[1]['table'] = 'vendor';
        $join_str[1]['join_table_id'] ='letter_head_register.party_detail';
        $join_str[1]['from_table_id'] = 'vendor.id';

        $join_str[2]['join_type']='left';
        $join_str[2]['table'] = 'project';
        $join_str[2]['join_table_id'] ='letter_head_register.project_id';
        $join_str[2]['from_table_id'] = 'project.id';

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);
        die();
    }

    public function failed_letter_head_list(){
        $this->data['page_title'] = "Failed Letter Head";
        $this->data['failed_list'] = $this->get_failed_letter_head_list_query();
        // dd($this->data['failed_list']);
        return view('admin.letter_head_register.failed_letter_head_list', $this->data);
    }

    public function get_failed_letter_head_list_query(){
        $all_letter_head = LetterHeadRegister::select('letter_head_register.*','letter_head_register.letter_head_ref_no','letter_head_number','company.company_name', DB::raw('group_concat(letter_head_number) as letter_head_numbers'))
                ->join('company', 'letter_head_register.company_id', '=', 'company.id')
                // ->groupBy('letter_head_ref_no')
                ->where('is_failed',1)
                ->groupBy('failed_unique')
                ->get()->toArray();
        $letter_group = [];
        if($all_letter_head){
            return $all_letter_head;
            /* foreach($all_letter_head as $one_rtgs){
             $blank_letter = LetterHeadRegister::select('letter_head_register.id','letter_head_register.letter_head_ref_no','letter_head_number','company.company_name','failed_reason','failed_document','failed_unique')
                ->join('company', 'letter_head_register.company_id', '=', 'company.id')
                ->where('is_failed',1)
                ->orderBy('letter_head_number', 'ASC')
                ->where('letter_head_ref_no',$one_rtgs['letter_head_ref_no'])
                // ->groupBy('failed_unique')
                ->get()->toArray();

                $arr_group = [];
                $start_pointer = $blank_letter[0]['letter_head_number'];
                $failed_unique = $blank_letter[0]['failed_unique'];
                foreach ($blank_letter as $key => $value) {
                        if($start_pointer == $value['letter_head_number']){

                            if($failed_unique == $value['failed_unique']){
                                $arr_group[] = $value;
                                $start_pointer++;
                            }else{
                                array_push($letter_group,$arr_group);
                                $arr_group = [];
                                $start_pointer = $value['letter_head_number']+1;
                                $arr_group[] = $value;
                            }
                        }else{
                            array_push($letter_group,$arr_group);
                            $arr_group = [];
                            $start_pointer = $value['letter_head_number']+1;
                            $arr_group[] = $value;
                        }

                }
                    if(!empty($arr_group)){
                        array_push($letter_group,$arr_group);
                    }
                }
                return $letter_group; */
        }else{
             return [];
        }
    }

    public function get_failed_letter_head_list(){
        $datatable_fields = array('letter_head_ref_no','company.company_name','letter_head_number','failed_reason','failed_document');
        $request = Input::all();
        $conditions_array = ['letter_head_register.is_failed' => 1];
        $getfiled =array('letter_head_register.id','company.company_name','letter_head_number','letter_head_ref_no','is_signed','is_used','work_detail','issue_date','letter_head_register.letter_head_content','letter_head_register.title','vendor.vendor_name','project.project_name','other_project_detail','failed_reason','failed_document');
        $table = "letter_head_register";
        $join_str=[];
        $join_str[0]['join_type']='';
        $join_str[0]['table'] = 'company';
        $join_str[0]['join_table_id'] ='letter_head_register.company_id';
        $join_str[0]['from_table_id'] = 'company.id';

        $join_str[1]['join_type']='left';
        $join_str[1]['table'] = 'vendor';
        $join_str[1]['join_table_id'] ='letter_head_register.party_detail';
        $join_str[1]['from_table_id'] = 'vendor.id';

        $join_str[2]['join_type']='left';
        $join_str[2]['table'] = 'project';
        $join_str[2]['join_table_id'] ='letter_head_register.project_id';
        $join_str[2]['from_table_id'] = 'project.id';

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);
        die();
    }

    public function add_failed_letter_head(){
        $this->data['page_title']    = 'Add Failed Letter Head';
        $this->data['module_title'] = "Failed Letter Head";
        $this->data['module_link'] = "admin.failed_letter_head_list";
        $this->data['companies']       = Companies::pluck('company_name','id');

        return view('admin.letter_head_register.add_failed_letter_head', $this->data);
    }

    public function get_unfailed_letter_head_ref_no(Request $request){
        $company_id = $request->company_id;

            $letter_data = LetterHeadRegister::select('letter_head_ref_no', 'id')
                  ->where(['company_id' => $company_id])
                  ->where(['is_failed' => 0])
                  ->groupBy('letter_head_ref_no')
                  ->get()->toArray();
            $html = "<option value=''>Select Letter Head Ref No</option>";

            foreach ($letter_data as $key => $data_value) {
                $html .= "<option value='" . $data_value['letter_head_ref_no'] . "'>" . $data_value['letter_head_ref_no'] . "</option>";
            }
            echo $html;
            die();
    }

    public function update_failed_letter_head(Request $request){

        $validator_normal = Validator::make($request->all(), [
            'company_id' => 'required',
            'letter_head_ref_no' => 'required',
            'letter_head_number' => 'required',
            'failed_reason' => 'required',
            'failed_document' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_failed_letter_head')->with('error', 'Please follow validation rules.');
        }
        $data = $request->all();

        if ($request->file('failed_document')) {

            $document_file = $request->file('failed_document');
            $original_file_name = explode('.', $document_file->getClientOriginalName());
            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);

            $file_path = $document_file->storeAs('public/letter_head_failed_docs', $new_file_name);
            if ($file_path) {
                $document_file = $file_path;
            }
        }

            /* $letterModel =  LetterHeadRegister::where('letter_head_ref_no',$data['letter_head_ref_no'])->where('letter_head_number',$data['letter_head_number'])->first();
            $letterModel->failed_document = !empty($document_file) ? $document_file : NULL;
            $letterModel->failed_reason = $data['failed_reason'];
            $letterModel->is_failed = 1;
            $letterModel->updated_ip = $request->ip();
            $letterModel->save(); */
            $uniqueId= time();
            $update_arr = [
                'failed_document' => !empty($document_file) ? $document_file : NULL,
                'failed_reason' => $data['failed_reason'],
                'is_failed' => 1,
                'failed_unique' => $uniqueId,
                'updated_ip' => $request->ip(),
            ];
            // dd($update_arr);
            $check_data = 0;
            foreach ($data['letter_head_number'] as $key => $value) {
                LetterHeadRegister::where('letter_head_ref_no',$data['letter_head_ref_no'])->where('letter_head_number',$value)->update($update_arr);
                $check_data = 1;
            }

        if (!empty($check_data)) {

            // User Action Log
            $company_name = Companies::whereId($request->get('company_id'))->value('company_name');
            $add_string = "<br>Company Name: ".$company_name;
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => $data['letter_head_ref_no']." letter head number ".implode(',',$data['letter_head_number'])." failed".$add_string,
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            return redirect()->route('admin.failed_letter_head_list')->with('success', 'Letter head Failed successfully.');
        } else {
            return redirect()->route('admin.add_failed_letter_head')->with('error', 'Error occurre in insert. Try Again!');
        }
    }
}
