<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App\Lib\NotificationTask;
use App\Common_query;
use App\Companies;
use App\Clients;
use App\Projects;
use App\Vendors;
use App\Inward_outwards;

use App\Signed_cheque_list;

use App\Banks;
use App\User;
use App\ChequeRegister;
use Illuminate\Support\Facades\Validator;
use App\Lib\UserActionLogs;

class ChequeRegisterController extends Controller
{
    public $data;
    private $notification_task;
    public $user_action_logs;

    public function __construct() {

        $this->notification_task = new NotificationTask();
        $this->user_action_logs = new UserActionLogs();
        $this->data['module_title'] = "Blank Cheque list";
        $this->data['module_link'] = "admin.blank_cheque_list";
    }

    public function index() {
        $this->data['page_title'] = "Cheque Register";
        return view('admin.cheque_register.index', $this->data);
    }


    public function get_cheque_register_list() {
        $datatable_fields = array('cheque_register.id','company.company_name','bank.bank_name','ch_no','check_ref_no','is_used','issue_date','use_type','is_signed','is_failed','vendor.vendor_name','project.project_name','cl_date','amount','remark','work_detail');
        $request = Input::all();
        $conditions_array = [];

        $getfiled =array('cheque_register.id','company.company_name','bank.bank_name','ch_no','check_ref_no','is_used','issue_date','use_type','is_signed','is_failed','failed_reason','failed_document','vendor.vendor_name','project.project_name','cl_date','amount','remark','work_detail');
        $table = "cheque_register";
        $join_str=[];
        $join_str[0]['join_type']='';
        $join_str[0]['table'] = 'company';
        $join_str[0]['join_table_id'] ='cheque_register.company_id';
        $join_str[0]['from_table_id'] = 'company.id';

        $join_str[1]['join_type']='';
        $join_str[1]['table'] = 'bank';
        $join_str[1]['join_table_id'] ='cheque_register.bank_id';
        $join_str[1]['from_table_id'] = 'bank.id';

        $join_str[2]['join_type']='left';
        $join_str[2]['table'] = 'vendor';
        $join_str[2]['join_table_id'] ='cheque_register.party_detail';
        $join_str[2]['from_table_id'] = 'vendor.id';

        $join_str[3]['join_type']='left';
        $join_str[3]['table'] = 'project';
        $join_str[3]['join_table_id'] ='cheque_register.project_id';
        $join_str[3]['from_table_id'] = 'project.id';

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);
        die();
    }

    //------------ 08/06/2020 ----------------
    public function get_cheque_blank_list_query(){

        $all_cheques = ChequeRegister::select('*')
                ->where('is_failed',0)
                ->where('is_cancel',0)
                ->where('is_used' ,'not_used')
                ->where('is_signed' ,'no')
                ->groupBy('check_ref_no')
                ->get()->toArray();
                 $cheque_group = [];
        if($all_cheques){
        foreach($all_cheques as $one_cheque){
             $blank_chques = ChequeRegister::select('cheque_register.id','cheque_register.check_ref_no','ch_no','company.company_name','bank.bank_name','bank.ac_number','bank.bank_name')
                ->join('company', 'cheque_register.company_id', '=', 'company.id')
                ->join('bank', 'cheque_register.bank_id', '=', 'bank.id')
                ->where('is_failed',0)
                ->where('is_cancel',0)
                ->where('is_used' ,'not_used')
                ->where('is_signed' ,'no')
                ->orderBy('ch_no', 'ASC')
                ->where('check_ref_no',$one_cheque['check_ref_no'])
                ->get()->toArray();

        $arr_group = [];

        $start_pointer = $blank_chques[0]['ch_no'];
        foreach ($blank_chques as $key => $value) {
                if($start_pointer == $value['ch_no']){
                    $arr_group[] = $value;
                    $start_pointer++;
                }else{
                    array_push($cheque_group,$arr_group);
                    $arr_group = [];
                    $start_pointer = $value['ch_no']+1;
                    $arr_group[] = $value;
                }

        }
        if(!empty($arr_group)){
            array_push($cheque_group,$arr_group);
        }
        }
        return $cheque_group;
        }else{
            return [];
        }
    }
    public function get_cheque_signed_list_query(){

        $all_cheques = ChequeRegister::select('*')
                ->where('is_failed',0)
                ->where('is_cancel',0)
                ->where('is_used' ,'not_used')
                ->where('is_signed' ,'yes')
                ->groupBy('check_ref_no')
                ->get()->toArray();
                 $cheque_group = [];
        if($all_cheques){
        foreach($all_cheques as $one_cheque){
             $signed_chques = ChequeRegister::select('cheque_register.id','cheque_register.check_ref_no','ch_no','company.company_name','bank.bank_name','bank.ac_number')
                ->join('company', 'cheque_register.company_id', '=', 'company.id')
                ->join('bank', 'cheque_register.bank_id', '=', 'bank.id')
                ->where('is_failed',0)
                ->where('is_cancel',0)
                ->where('is_used' ,'not_used')
                ->where('is_signed' ,'yes')
                ->orderBy('ch_no', 'ASC')
                ->where('check_ref_no',$one_cheque['check_ref_no'])
                ->get()->toArray();

        $arr_group = [];

        $start_pointer = $signed_chques[0]['ch_no'];
        foreach ($signed_chques as $key => $value) {
                if($start_pointer == $value['ch_no']){
                    $arr_group[] = $value;
                    $start_pointer++;
                }else{
                    array_push($cheque_group,$arr_group);
                    $arr_group = [];
                    $start_pointer = $value['ch_no']+1;
                    $arr_group[] = $value;
                }

        }
        if(!empty($arr_group)){
            array_push($cheque_group,$arr_group);
        }
        }
        return $cheque_group;
        }else{
            return [];
        }
    }
    public function blank_cheque_list() {

        // $blank_cheques = ChequeRegister::select('cheque_register.id','cheque_register.check_ref_no','ch_no','company.company_name','bank.bank_name','bank.ac_number','bank.bank_name')
        //     ->join('company', 'cheque_register.company_id', '=', 'company.id')
        //     ->join('bank', 'cheque_register.bank_id', '=', 'bank.id')
        //         ->where('cheque_register.is_failed',0)
        //         ->where('cheque_register.is_used' ,'not_used')
        //         ->where('cheque_register.is_signed' ,'no')
        //         ->orderBy('cheque_register.id', 'DESC')
        //         ->groupBy('cheque_register.check_ref_no')
        //         ->get();

        //         foreach ($blank_cheques as $key => $value) {
        //             $blank_cheques[$key]->start = $start =ChequeRegister::where('check_ref_no', $value->check_ref_no)
        //             ->select('ch_no')->pluck('ch_no')->first();
        //             $blank_cheques[$key]->end = $end= ChequeRegister::where('check_ref_no', $value->check_ref_no)
        //                 ->orderBy('id', 'desc')->select('ch_no')->pluck('ch_no')->first();

        //                 for ($i=$start; $i <= $end ; $i++) {
        //                     if (!ChequeRegister::where('ch_no', $i)->first()) {
        //                         $blank_cheques[$key]->end = $i-1;
        //                         continue;
        //                     }

        //                 }
        //         }
        // dd($blank_cheques->toArray());
        $this->data['page_title'] = "Blank Cheque";
        $this->data['blank_list'] = $this->get_cheque_blank_list_query();
        return view('admin.cheque_register.blank_cheque', $this->data);
    }
    public function used_cheque_list() {

        $this->data['page_title'] = "Used Cheque";
        $this->data['companies']       = Companies::orderBy('company_name', 'asc')->pluck('company_name', 'id');
        $this->data['banks']       = Banks::orderBy('bank_name', 'asc')->get(['bank_name', 'id', 'ac_number']);
        $this->data['clients']       = Clients::orderBy('client_name', 'asc')->get(['client_name', 'id', 'location']);
        $this->data['projects']       = Projects::orderBy('project_name', 'asc')->pluck('project_name', 'id');
        $this->data['vendors']       = Vendors::select('vendor_name', 'id', 'company_id')->orderBy('vendor_name', 'asc')->with(['company'])->get();
        $this->data['check_ref_no']       = ChequeRegister::where('is_used', 'used')->where('is_failed', '0')->groupBy('check_ref_no')->pluck('check_ref_no');
        $this->data['ch_no']       = ChequeRegister::groupBy('ch_no')->pluck('ch_no','id');



        return view('admin.cheque_register.used_cheque', $this->data);
    }
    public function signed_cheque_list() {
        $this->data['page_title'] = "Cheque Signature Request";
        $this->data['signed_list'] = $this->get_cheque_signed_list_query();
        return view('admin.cheque_register.signed_cheque', $this->data);
    }
    public function failed_cheque_list() {
        $this->data['page_title'] = "Failed Cheque";
        return view('admin.cheque_register.failed_cheque', $this->data);
    }
    public function get_blank_cheque_list() {   //done


        $datatable_fields = array('check_ref_no','company.company_name','bank.bank_name','ch_no');
        $request = Input::all();
        $conditions_array = ['cheque_register.is_used' => 'not_used', 'cheque_register.is_signed' => 'no','cheque_register.is_failed' => 0,'cheque_register.is_cancel' => 0];

        $getfiled =array('cheque_register.id','company.company_name','bank.bank_name','bank.ac_number','ch_no','check_ref_no');
        $table = "cheque_register";
        $join_str=[];
        $join_str[0]['join_type']='';
        $join_str[0]['table'] = 'company';
        $join_str[0]['join_table_id'] ='cheque_register.company_id';
        $join_str[0]['from_table_id'] = 'company.id';

        $join_str[1]['join_type']='';
        $join_str[1]['table'] = 'bank';
        $join_str[1]['join_table_id'] ='cheque_register.bank_id';
        $join_str[1]['from_table_id'] = 'bank.id';

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);
        die();
    }
    public function get_used_cheque_list() {   //done
        $datatable_fields = array('cheque_register.check_ref_no','company.company_name','bank.bank_name','ch_no', 'clients.client_name','project.project_name','vendor.vendor_name','issue_date','cl_date', 'cheque_register.amount','remark','work_detail');
        $request = Input::all();

            //'cheque_register.is_signed' => 'no',
        $conditions_array = ['cheque_register.is_used' => 'used', 'cheque_register.is_failed' => 0,'cheque_register.is_cancel' => 0];

        $company_id = $request['company_id'];
        $bank_id = $request['bank_id'];
        $client_id = $request['client_id'];
        $project_id = $request['project_id'];
        $vendor_id = $request['vendor_id'];
        $check_ref_no = $request['check_ref_no'];
        $cheque_number = $request['cheque_number'];
        $amount = $request['amount'];
        $issue_date = $request['issue_date'];
        $cl_date = $request['cl_date'];

        if ($company_id != '') {
            $conditions_array['cheque_register.company_id'] = $company_id;
        }

         if ($bank_id != '') {
            $conditions_array['cheque_register.bank_id'] = $bank_id;
        }

        if($client_id != ''){
            $conditions_array['clients.id'] = $client_id;
        }

        if($project_id != ''){
            $conditions_array['cheque_register.project_id'] = $project_id;
        }
        if($vendor_id != ''){
            $conditions_array['cheque_register.party_detail'] = $vendor_id;
        }

        if($check_ref_no != ''){
            $conditions_array['cheque_register.check_ref_no'] = $check_ref_no;
        }

        if ($cheque_number != '') {
            $conditions_array['cheque_register.ch_no'] = $cheque_number;
        }

        if ($issue_date != '') {
            $new_issue_data = date('Y-m-d', strtotime($issue_date));
            $conditions_array['cheque_register.issue_date'] = $new_issue_data;
        }
        if ($cl_date != '') {
            $new_cl_date = date('Y-m-d', strtotime($cl_date));
            $conditions_array['cheque_register.cl_date'] = $new_cl_date;
        }

        if($amount != ''){
            $conditions_array['cheque_register.amount'] = $amount;
        }

        $getfiled =array('cheque_register.id','company.company_name','bank.bank_name','bank.ac_number','ch_no', 'cheque_register.check_ref_no','is_used','issue_date','use_type','is_signed','is_failed','failed_reason','failed_document','vendor.vendor_name','project.project_name','cl_date', 'cheque_register.amount','remark','work_detail', 'bank_payment_approval.cheque_number', 'clients.client_name');
        $table = "cheque_register";
        $join_str=[];
        $join_str[0]['join_type']='';
        $join_str[0]['table'] = 'company';
        $join_str[0]['join_table_id'] ='cheque_register.company_id';
        $join_str[0]['from_table_id'] = 'company.id';

        $join_str[1]['join_type']='';
        $join_str[1]['table'] = 'bank';
        $join_str[1]['join_table_id'] ='cheque_register.bank_id';
        $join_str[1]['from_table_id'] = 'bank.id';

        $join_str[2]['join_type']='left';
        $join_str[2]['table'] = 'vendor';
        $join_str[2]['join_table_id'] ='cheque_register.party_detail';
        $join_str[2]['from_table_id'] = 'vendor.id';

        $join_str[3]['join_type']='left';
        $join_str[3]['table'] = 'project';
        $join_str[3]['join_table_id'] ='cheque_register.project_id';
        $join_str[3]['from_table_id'] = 'project.id';

        $join_str[4]['join_type'] = 'left';
        $join_str[4]['table'] = 'bank_payment_approval';
        $join_str[4]['join_table_id'] = 'cheque_register.id';
        $join_str[4]['from_table_id'] = 'bank_payment_approval.cheque_number';

        $join_str[5]['join_type'] = 'left';
        $join_str[5]['table'] = 'clients';
        $join_str[5]['join_table_id'] = 'clients.id';
        $join_str[5]['from_table_id'] = 'bank_payment_approval.client_id';

        echo ChequeRegister::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);
        die();
    }

    public function get_used_cheque_ref_no_list(Request $request){
        $company_id = $request->get('company_id');
        $cheque_ref_no_list = ChequeRegister::where('company_id', $company_id)->where('is_used', 'used')->where('is_failed', '0')->where('is_cancel', '0')->groupBy('check_ref_no')->orderBy('check_ref_no', 'asc')->get('check_ref_no')->toArray();
        $html = "";
        foreach($cheque_ref_no_list as $key => $value){
            $html .= '<option value="'. $value['check_ref_no']. '">' . $value['check_ref_no'] . '</option>';
        }
        echo $html;exit;
    }

    public function get_used_cheque_number_list(Request $request){
        $check_ref_no = $request->get('check_ref_no');
        $cheque_no_list = ChequeRegister::select('ch_no', 'id')->where('check_ref_no', $check_ref_no)->where('is_used', 'used')->where('is_failed', '0')->where('is_cancel', '0')->get()->toArray();

        $html = "";
        foreach ($cheque_no_list as $key => $value) {
            $html .= '<option value="' . $value['ch_no'] . '">' . $value['ch_no'] . '</option>';
        }
        echo $html;
        exit;
    }

    public function get_signed_cheque_list() {  //done
        $datatable_fields = array('check_ref_no','company.company_name','bank.bank_name','ch_no');
        $request = Input::all();
        $conditions_array = ['cheque_register.is_signed' => 'yes','cheque_register.is_used' => 'not_used','cheque_register.is_failed' => 0,'cheque_register.is_cancel' => 0];

        $getfiled =array('cheque_register.id','company.company_name','bank.bank_name','bank.ac_number','ch_no','check_ref_no');
        $table = "cheque_register";
        $join_str=[];
        $join_str[0]['join_type']='';
        $join_str[0]['table'] = 'company';
        $join_str[0]['join_table_id'] ='cheque_register.company_id';
        $join_str[0]['from_table_id'] = 'company.id';

        $join_str[1]['join_type']='';
        $join_str[1]['table'] = 'bank';
        $join_str[1]['join_table_id'] ='cheque_register.bank_id';
        $join_str[1]['from_table_id'] = 'bank.id';

        $join_str[2]['join_type']='left';
        $join_str[2]['table'] = 'vendor';
        $join_str[2]['join_table_id'] ='cheque_register.party_detail';
        $join_str[2]['from_table_id'] = 'vendor.id';

        $join_str[3]['join_type']='left';
        $join_str[3]['table'] = 'project';
        $join_str[3]['join_table_id'] ='cheque_register.project_id';
        $join_str[3]['from_table_id'] = 'project.id';

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);
        die();
    }
    public function get_failed_cheque_list() {

        $datatable_fields = array('check_ref_no','company.company_name','bank.bank_name','ch_no','failed_reason','failed_document');

        $request = Input::all();
        $conditions_array = ['cheque_register.is_failed' => 1,'cheque_register.is_cancel' => 0];

        $getfiled =array('cheque_register.id','company.company_name','bank.bank_name','bank.ac_number','ch_no','check_ref_no','is_failed','failed_reason','failed_document');
        $table = "cheque_register";
        $join_str=[];
        $join_str[0]['join_type']='';
        $join_str[0]['table'] = 'company';
        $join_str[0]['join_table_id'] ='cheque_register.company_id';
        $join_str[0]['from_table_id'] = 'company.id';

        $join_str[1]['join_type']='';
        $join_str[1]['table'] = 'bank';
        $join_str[1]['join_table_id'] ='cheque_register.bank_id';
        $join_str[1]['from_table_id'] = 'bank.id';

        $join_str[2]['join_type']='left';
        $join_str[2]['table'] = 'vendor';
        $join_str[2]['join_table_id'] ='cheque_register.party_detail';
        $join_str[2]['from_table_id'] = 'vendor.id';

        $join_str[3]['join_type']='left';
        $join_str[3]['table'] = 'project';
        $join_str[3]['join_table_id'] ='cheque_register.project_id';
        $join_str[3]['from_table_id'] = 'project.id';

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);
        die();
    }

    public function add_signed_cheque() {
        $this->data['page_title']    = 'Add Signed Cheque';
        $this->data['module_title'] = "Signed Cheque";
        $this->data['module_link'] = "admin.signed_cheque_list";
        $this->data['companies']       = Companies::pluck('company_name','id');

        return view('admin.cheque_register.add_signed_cheque', $this->data);
    }

    public function add_failed_cheque() {   //10/06/2020
        $this->data['page_title']    = 'Add Failed Cheque';
        $this->data['module_title'] = "Failed Cheque";
        $this->data['module_link'] = "admin.failed_cheque_list";
        $this->data['companies']       = Companies::pluck('company_name','id');

        return view('admin.cheque_register.add_failed_cheque', $this->data);
    }

    public function get_unfailed_cheque_book(Request $request)    //10/06/2020
    {

            $bank_id = $request->bank_id;
            $company_id = $request->company_id;

            $cheque_data = ChequeRegister::select('check_ref_no', 'id')
                  ->where(['bank_id' => $bank_id])
                  ->where(['company_id' => $company_id])
                  ->where(['is_failed' => 0])
                  ->where(['is_cancel' => 0])
                  ->groupBy('check_ref_no')
                  ->get()->toArray();
            $html = "<option value=''>Select Cheque Book</option>";

            foreach ($cheque_data as $key => $data_value) {
                $html .= "<option value='" . $data_value['check_ref_no'] . "'>" . $data_value['check_ref_no'] . "</option>";
            }
            echo $html;
            die();

    }
    public function get_remaining_cheque(Request $request)     //11/06/2020
    {

            $cheque_book = $request->cheque_book;
            $ch_no = $request->ch_no;
            $cheque_array = [];
            $cheque_data = ChequeRegister::select('ch_no','check_ref_no', 'id')
                  ->where('check_ref_no' ,$cheque_book)
                  ->where('ch_no' ,'>=', $ch_no)
                  ->orderBy('ch_no', 'ASC')
                  ->get()->toArray();

            foreach ($cheque_data as $key => $value) {
                $exist = ChequeRegister::where('check_ref_no',$value['check_ref_no'])->where('ch_no',$value['ch_no'])->where(['is_signed' => 'no', 'signed_slug' => 'No','is_failed' => 0])->first();
                if (!$exist) {
                    break;   // will leave the foreach loop and also the if statement
                }
                $cheque_array[] = $value['ch_no'];
            }

            $html = "<option value=''>Please Select</option>";
            foreach ($cheque_array as $index => $ch_no) {
                $html .= "<option value='" . $ch_no . "'>" . $ch_no . "</option>";
            }
            echo $html;
            die();

    }
    public function get_unfailed_cheque_list(Request $request)    //10/06/2020 - 06/07
    {
            $cheque_book = $request->cheque_book;

            $cheque_data = ChequeRegister::select('ch_no', 'id')
                ->where(['check_ref_no' => $cheque_book])
                ->where(['is_failed' => 0])
                ->where(['is_cancel' => 0])
                ->get()->toArray();
            $html = "<option value=''>Please Select</option>";

            foreach ($cheque_data as $key => $data_value) {
                $html .= "<option value='" . $data_value['ch_no'] . "'>" . $data_value['ch_no'] . "</option>";
            }
            echo $html;
            die();

    }

    public function update_failed_cheque(Request $request)   //10/06/2020
    {
        $validator_normal = Validator::make($request->all(), [
            'company_id' => 'required',
            'bank_id' => 'required',
            'cheque_book' => 'required',
            'cheque_no' => 'required',
            'failed_reason' => 'required',
            'failed_document' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_failed_cheque')->with('error', 'Please follow validation rules.');
        }
        $data = $request->all();

        $document_file = '';
        if ($request->file('failed_document')) {

            $document_file = $request->file('failed_document');
            $original_file_name = explode('.', $document_file->getClientOriginalName());
            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);

            $file_path = $document_file->storeAs('public/cheque_failed_docs', $new_file_name);
            if ($file_path) {
                $document_file = $file_path;
            }
        }

            $chequeModel =  ChequeRegister::where('check_ref_no',$data['cheque_book'])->where('ch_no',$data['cheque_no'])->first();
            $chequeModel->failed_document = !empty($document_file) ? $document_file : NULL;
            $chequeModel->failed_reason = $data['failed_reason'];
            $chequeModel->is_failed = 1;
            $chequeModel->save();

        if (!empty($chequeModel)) {

            // User Action Log
            $company_name = Companies::whereId($request->get('company_id'))->value('company_name');
            $bank_name = Banks::whereId($request->get('bank_id'))->value('bank_name');
            $add_string = "<br>Company Name: ".$company_name."<br>Bank Name: ".$bank_name;
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => $data['cheque_book']." cheque book ref no cheque number ".$data['cheque_no']. " failed".$add_string,
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            return redirect()->route('admin.failed_cheque_list')->with('success', 'Cheque Failed successfully.');
        } else {
            return redirect()->route('admin.add_failed_cheque')->with('error', 'Error occurre in insert. Try Again!');
        }

    }

    public function get_cheque_book(Request $request)
    {

            $bank_id = $request->bank_id;
            $company_id = $request->company_id;

            $cheque_data = ChequeRegister::select('check_ref_no', 'id')
                  ->where(['bank_id' => $bank_id])
                  ->where(['company_id' => $company_id])
                  ->where(['signed_slug' => 'No'])
                  ->groupBy('check_ref_no')
                  ->get()->toArray();
            $html = "<option value=''>Select Cheque Book</option>";

            foreach ($cheque_data as $key => $data_value) {
                $html .= "<option value='" . $data_value['check_ref_no'] . "'>" . $data_value['check_ref_no'] . "</option>";
            }
            echo $html;
            die();

    }
    //17/09/2020 payroll
    public function get_all_cheque_ref_list(Request $request)
    {

            $bank_id = $request->bank_id;
            $company_id = $request->company_id;

            $cheque_data = ChequeRegister::select('check_ref_no', 'id')
                  ->where(['bank_id' => $bank_id])
                  ->where(['company_id' => $company_id])
                  ->groupBy('check_ref_no')
                  ->get()->toArray();
            $html = "<option value=''>Select Cheque Book</option>";

            foreach ($cheque_data as $key => $data_value) {
                $html .= "<option value='" . $data_value['check_ref_no'] . "'>" . $data_value['check_ref_no'] . "</option>";
            }
            echo $html;
            die();

    }
    //17/09/2020 payroll
    public function get_signedUnfailed_cheque_list(Request $request)
    {

            $cheque_book = $request->cheque_book;

            $cheque_data = ChequeRegister::select('ch_no', 'id')
                ->where(['check_ref_no' => $cheque_book])
                ->where(['is_signed' => 'yes','is_failed' => 0])
                ->orderBy('ch_no', 'ASC')
                ->get()->toArray();
            $html = "<option value=''>Select Cheque Book</option>";

            foreach ($cheque_data as $key => $data_value) {
                $html .= "<option value='" . $data_value['id'] . "'>" . $data_value['ch_no'] . "</option>";
            }
            echo $html;
            die();

    }
    public function get_unsigned_cheque_list(Request $request)   //11/06/2020
    {
            $cheque_book = $request->cheque_book;

            $cheque_data = ChequeRegister::select('ch_no', 'id')
                ->where(['check_ref_no' => $cheque_book])
                ->where(['is_signed' => 'no', 'signed_slug' => 'No','is_failed' => 0,'is_cancel' => 0])
                ->orderBy('ch_no', 'ASC')
                ->get()->toArray();
            $html = "<option value=''>Please Select</option>";

            foreach ($cheque_data as $key => $data_value) {
                $html .= "<option value='" . $data_value['ch_no'] . "'>" . $data_value['ch_no'] . "</option>";
            }
            echo $html;
            die();

    }
    public function update_signed_cheque(Request $request)
    {
        $validator_normal = Validator::make($request->all(), [
            'company_id' => 'required',
            'bank_id' => 'required',
            'cheque_book' => 'required',
            'chk_start_number' => 'required',
            'chk_end_number' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_signed_cheque')->with('error', 'Please follow validation rules.');
        }
        $data = $request->all();

        for($i=$data['chk_start_number'];$i<=$data['chk_end_number'];$i++) {
            $chequeModel =  ChequeRegister::where('check_ref_no',$data['cheque_book'])->where('ch_no',$i)->first();
            $chequeModel->signed_slug = 'Yes';
            $chequeModel->save();
        }


            $signedChequeModel = new Signed_cheque_list();
            $signedChequeModel->user_id =Auth::user()->id;
            $signedChequeModel->cheque_book_ref_no = $data['cheque_book'];
            $signedChequeModel->cheque_start_no   = $data['chk_start_number'];
            $signedChequeModel->cheque_end_no = $data['chk_end_number'];
            $signedChequeModel->status = 'Pending';
            $signedChequeModel->created_at = date('Y-m-d h:i:s');
            $signedChequeModel->created_ip = $request->ip();
            $signedChequeModel->updated_at = date('Y-m-d h:i:s');
            $signedChequeModel->updated_ip = $request->ip();
            $signedChequeModel->save();


        if (!empty($signedChequeModel)) {

            // User Action Log
            $company_name = Companies::whereId($request->get('company_id'))->value('company_name');
            $bank_name = Banks::whereId($request->get('bank_id'))->value('bank_name');
            $add_string = "<br>Company Name: ".$company_name."<br>Bank Name: ".$bank_name;
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => $data['cheque_book']." signed cheque request".$add_string,
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            $superUser = user::where('status', 'Enabled')->where('role', config('constants.SuperUser'))->pluck('id')->toArray();
            $cheque_book = $data['cheque_book'];
            $this->notification_task->signedChequeApprovalNotify($cheque_book, $superUser);

            return redirect()->route('admin.signed_cheque_list')->with('success', 'Your sign cheque request is submitted. It will display in this list once approve by Super Admin. You can check the request status from Signed Cheque Approval menu.');
        } else {
            return redirect()->route('admin.add_cheque_register')->with('error', 'Error occurre in insert. Try Again!');
        }

    }
    public function signed_approval_requests()
    {

        // if (Auth::user()->role != config('constants.SuperUser') ) {
        //       return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        // }
        $this->data['page_title'] = "Signed Cheque Approval";

        $records = Signed_cheque_list::leftjoin('users','users.id' ,'=' ,'signed_cheque_list.user_id')
        ->get(['users.name','signed_cheque_list.id','signed_cheque_list.cheque_book_ref_no','signed_cheque_list.cheque_start_no',
          'signed_cheque_list.cheque_end_no','signed_cheque_list.status','signed_cheque_list.reject_note','signed_cheque_list.status_datetime']);

        $this->data['records'] = $records;

        return view('admin.cheque_register.signed_approval_list', $this->data);
    }
    public function accept_approval_cheque_book($id ,Request $request) {

        if (Signed_cheque_list::where('id', $id)->update(['status' => 'Accepted', 'status_datetime' => date('Y-m-d h:i:s')])) {

            $signed_data = Signed_cheque_list::where('id',$id)->first();

            for($i=$signed_data['cheque_start_no'];$i<=$signed_data['cheque_end_no'];$i++) {
                    $chequeModel =  ChequeRegister::where('check_ref_no',$signed_data['cheque_book_ref_no'])->where('ch_no',$i)->first();
                    $chequeModel->is_signed = 'yes';
                    $chequeModel->updated_at = date('Y-m-d h:i:s');
                    $chequeModel->save();
                }

                // User Action Log
                $action_data = [
                    'user_id' => Auth::user()->id,
                    'task_body' => $signed_data['cheque_book_ref_no']." signed cheque request approved",
                    'created_ip' => $request->ip(),
                ];
                $this->user_action_logs->action($action_data);

                return redirect()->route('admin.signed_approval_requests')->with('success', 'Cheque Book signed request successfully Approved.');
        }
        return redirect()->route('admin.signed_approval_requests')->with('error', 'Error during operation. Try again!');

    }
    public function reject_approval_cheque_book(Request $request) {
        $validator = Validator::make($request->all(), [
                    'cheque_id' => 'required',
                    'reject_note' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $id = $request_data['cheque_id'];
        $update_arr = [

               'reject_note' => $request_data['reject_note'],
               'status' => 'Rejected',
               'status_datetime' => date('Y-m-d h:i:s')
        ];
        $signed_data = Signed_cheque_list::where('id',$id)->first();

        for($i=$signed_data['cheque_start_no'];$i<=$signed_data['cheque_end_no'];$i++) {
            $chequeModel =  ChequeRegister::where('check_ref_no',$signed_data['cheque_book_ref_no'])->where('ch_no',$i)->first();
            $chequeModel->signed_slug = 'No';
            $chequeModel->save();
        }

        if (Signed_cheque_list::where('id', $id)->update($update_arr)) {

            // User Action Log
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => $signed_data['cheque_book_ref_no']." signed cheque request rejected",
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            return redirect()->route('admin.signed_approval_requests')->with('success', 'Cheque Book signed request successfully Rejected.');
        }
        return redirect()->route('admin.signed_approval_requests')->with('error', 'Error during operation. Try again!');
    }

    //--------------------------------------------------------------------------------------------------

    public function add_cheque_register() {
        $this->data['page_title']      = 'Add Cheque Register';
        $this->data['companies']       = Companies::pluck('company_name','id');
        $this->data['banks']           = Banks::pluck('bank_name','id');
        $data = ChequeRegister::select(['ch_no'])->orderBy('ch_no','desc')->take(1)->get()->toArray();
        $this->data['last_cheque']    = "";//($data[0]['ch_no']+1);
        return view('admin.cheque_register.add_cheque_register', $this->data);
    }

    public function insert_cheque_register(Request $request) {
        $validator_normal = Validator::make($request->all(), [
            'company_id' => 'required',
            'bank_id' => 'required',
            'chk_start_number' => 'required',
            'chk_end_number' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_cheque_register')->with('error', 'Please follow validation rules.');
        }

        $data = ChequeRegister::select(['ch_no'])->orderBy('ch_no','desc')->take(1)->get()->toArray();
        $last_cheque    = ""; //$data[0]['ch_no'];
        if(!empty($last_cheque) && $request->input('chk_start_number')<intval($last_cheque))
        {
            return redirect()->route('admin.add_cheque_register')->with('error', 'Duplicate cheque number enter please try again !.');
        }

        $amCompanyData = Companies::select('company_short_name')->where(['id' => $request->input('company_id')])->get()->toArray();

        $amBankData = Banks::select('bank_short_name')->where(['id' => $request->input('bank_id')])->get()->toArray();
        if($amBankData[0]['bank_short_name']){
            $bank_short_name = "/".$amBankData[0]['bank_short_name'];
        }else{
            $bank_short_name = "";
        }

        $check_entry = ChequeRegister::where('company_id',$request->input('company_id'))
               //->where('bank_id', $request->input('bank_id'))
               ->whereDate('created_at',date('Y-m-d'))
               ->distinct()
               ->groupBy('check_ref_no')->get()->count();


        if ($check_entry == 0) {
            $append_no = 1;
        } else {
            $append_no = $check_entry + 1;
        }


        $check_ref_no = $amCompanyData[0]['company_short_name'].$bank_short_name."/".date('Y-m-d')."/".$append_no;
        // dd($check_ref_no);
        for($i=$request->input('chk_start_number');$i<=$request->input('chk_end_number');$i++) {
            $chequeModel = new ChequeRegister();
            $chequeModel->company_id = $request->input('company_id');
            $chequeModel->bank_id    = $request->input('bank_id');
            $chequeModel->check_ref_no = $check_ref_no;
            $chequeModel->ch_no      = $i; // increment i ++;
            $chequeModel->created_at = date('Y-m-d h:i:s');
            $chequeModel->created_ip = $request->ip();
            $chequeModel->updated_at = date('Y-m-d h:i:s');
            $chequeModel->save();
        }

        if (!empty($chequeModel)) {

            // User Action Log
            $company_name = Companies::whereId($request->get('company_id'))->value('company_name');
            $bank_name = Banks::whereId($request->get('bank_id'))->value('bank_name');
            $add_string = "<br>Company Name: ".$company_name."<br>Bank Name: ".$bank_name;
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => $check_ref_no." blank cheque added".$add_string,
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            return redirect()->route('admin.blank_cheque_list')->with('success', 'New cheque added successfully.');
        } else {
            return redirect()->route('admin.add_cheque_register')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function delete_cheque_register($id) {
        if (ChequeRegister::where('id', $id)->delete()) {
            return redirect()->route('admin.cheque_register')->with('success', 'Delete successfully updated.');
        }
        return redirect()->route('admin.cheque_register')->with('error', 'Error during operation. Try again!');
    }

    public function get_bank_list_cheque()
    {
        if(!empty($_GET['company_id'])) {
           $company_id = $_GET['company_id'];
           $bank_data = Banks::select('bank_name','id','ac_number')
           ->where(['company_id' => $company_id])
           ->orderBy('bank_name', 'asc')
           ->get()->toArray();
           $html = "";
           foreach ($bank_data as $key => $bank_data_value) {
                $html.= "<option value=".$bank_data_value['id'].">".$bank_data_value['bank_name']." (".$bank_data_value['ac_number'].")"."</option>";
           }
           echo  $html;
           die();
        }
    }

    public function delete_cheques(Request $request)
    {
        if(!empty($request->input('del_cheque_ids')))
        {
            if (ChequeRegister::whereIn('id', explode(',',$request->input('del_cheque_ids')))->delete()) {
                return redirect()->route('admin.cheque_register')->with('success', 'Delete successfully updated.');
            }
        }

        return redirect()->route('admin.cheque_register')->with('error', 'Error during operation. Try again!');
    }

    public function edit_cheque_register($id) {
        $this->data['page_title']      = 'Edit Cheque Register';
        $this->data['companies']       = Companies::pluck('company_name','id');
        $this->data['banks']           = Banks::pluck('bank_name','id');

        $this->data['cheque_register_data'] = ChequeRegister::select('*')->where('id',$id)->get()->toArray();
        // echo "<pre>";
        // print_r($this->data['cheque_register_data']);
        // die();
        return view('admin.cheque_register.edit_cheque_register', $this->data);
    }

    public function update_cheque_register(Request $request)
    {
        $validator_normal = Validator::make($request->all(), [
            'cl_date' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.cheque_register')->with('error', 'Please follow validation rules.');
        }

        $id = $request->input('id');

        $cl_arr = [
            'cl_date' => date('Y-m-d',strtotime($request->input('cl_date')))
        ];

        ChequeRegister::where('id', $id)->update($cl_arr);

        return redirect()->route('admin.cheque_register')->with('success', 'Cheque updated successfully.');
    }

    public function change_cheque_status($id, $status) {
        if (ChequeRegister::where('id', $id)->update(['is_signed' => $status])) {
            return redirect()->route('admin.cheque_register')->with('success', 'Status successfully updated.');
        }
        return redirect()->route('admin.cheque_register')->with('error', 'Error during operation. Try again!');
    }

    public function cheque_failed(Request $request) {
        $validator_normal = Validator::make($request->all(), [
            'cheque_id' => 'required',
            'failed_reason' => 'required'
        ]);

        if ($validator_normal->fails()) {
            return redirect()->route('admin.cheque_register')->with('error', 'Please follow validation rules.');
        }

        $cheque_id  = $request->input('cheque_id');

        $document_file = '';
        if ($request->file('failed_document')) {

            $document_file = $request->file('failed_document');

            $original_file_name = explode('.', $document_file->getClientOriginalName());

            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);

            $file_path = $document_file->storeAs('public/cheque_failed_docs', $new_file_name);
            if ($file_path) {
                $document_file = $file_path;
            }
        }

        $update_arr = [
            'is_failed' => 1 ,
            'is_used' => 'used' ,
            'failed_reason'  => $request->input('failed_reason'),
            'failed_document'  => !empty($document_file) ? $document_file : NULL
        ];

        if (ChequeRegister::where('id', $cheque_id)->update($update_arr)) {
            return redirect()->route('admin.cheque_register')->with('success', 'Cheque failed Successfully.');
        }
        return redirect()->route('admin.cheque_register')->with('error', 'Error during operation. Try again!');
    }

    public function signed_cheques(Request $request)
    {
        if(!empty($request->input('signed_cheque_ids')))
        {
            if (ChequeRegister::whereIn('id', explode(',',$request->input('signed_cheque_ids')))->update(['is_signed' =>'yes'])) {
                return redirect()->route('admin.cheque_register')->with('success', 'Cheque successfully updated.');
            }
        }

        return redirect()->route('admin.cheque_register')->with('error', 'Error during operation. Try again!');
    }

    public function cheque_use_report(Request $request)
    {
        $this->data['page_title'] = "Cheque Use Report";
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

            $chequeeData = ChequeRegister::select('*')
                            ->join('company', 'cheque_register.company_id', '=', 'company.id')
                            ->join('bank', 'cheque_register.bank_id', '=', 'bank.id')
                            ->leftJoin('vendor', 'cheque_register.party_detail', '=', 'vendor.id')
                            ->leftJoin('project', 'cheque_register.project_id', '=', 'project.id')
                            ->where('cheque_register.is_used','used')
                            ->whereBetween('cheque_register.issue_date', [$first_date, $second_date])
                            ->orderBy('cheque_register.id', 'ASC')
                            ->get();
            $this->data['records'] = $chequeeData;
            $columnName = array('Sr. No', 'Company', 'Bank', 'Check Ref No', 'Chque No', 'Project Name', 'Vendor', 'Issue Date', 'Clear Date', 'Amount','Work Detail','Remark','Is Used','Is Signed');
            if (!empty($chequeeData[0])) {
                $csvData = $this->generateCsvFiles('cheque_report', $columnName, $chequeeData);
                $this->data['csv_data'] = $csvData;
            }
        }

        return view('admin.cheque_register.cheque_use_report', $this->data);
    }

    public function cheque_stats_report(Request $request)
    {
        $this->data['page_title'] = "Cheque Stats Report";
        $this->data['company']= Companies::getCompany();
        $this->data['records'] = [];
        $this->data['date'] = "";

        if($request->method() == 'POST'){
            $date = $request->get('date');
            $company_id = $request->get('company_id');
            $bank_id = $request->get('bank_id');
            $this->data['date'] = $request->get('date');
            $mainDate = explode("-", $date);
            $strFirstdate = str_replace("/", "-", $mainDate[0]);
            $strLastdate = str_replace("/", "-", $mainDate[1]);
            $first_date = date('Y-m-d h:m:s', strtotime($strFirstdate.' -1 day'));
            $second_date = date('Y-m-d h:m:s', strtotime($strLastdate.' +1 day'));

            $chequeeData = ChequeRegister::select( 'cheque_register.id', 'cheque_register.check_ref_no','bank.ac_number','bank.bank_name')
                            ->join('bank', 'cheque_register.bank_id', '=', 'bank.id')
                            ->where('cheque_register.company_id',$company_id)
                            ->where(function ($query) use ($bank_id) {
                                if ($bank_id > 0) {
                                    $query->where('cheque_register.bank_id', $bank_id);
                                }
                            })
                            ->whereBetween('cheque_register.created_at', [$first_date, $second_date])
                            ->orderBy('cheque_register.id', 'ASC')
                            ->groupBy('check_ref_no')
                            ->get();

                foreach ($chequeeData as $key => $value) {

                    $chequeeData[$key]->from = ChequeRegister::where('check_ref_no', $value->check_ref_no)
                        ->select('ch_no')->pluck('ch_no')->first();
                    $chequeeData[$key]->to = ChequeRegister::where('check_ref_no', $value->check_ref_no)
                        ->orderBy('id', 'desc')->select('ch_no')->pluck('ch_no')->first();
                    $chequeeData[$key]->total_cheque = ChequeRegister::where('check_ref_no', $value->check_ref_no)
                        ->get()->count();
                    $chequeeData[$key]->balanced_cheque = ChequeRegister::where('check_ref_no', $value->check_ref_no)
                        ->where('is_used', 'not_used')
                        ->get()->count();

                }


            $this->data['records'] = $chequeeData;
        }

        return view('admin.cheque_stats_reports.cheque_stat', $this->data);
    }

    public function findChequeDetail($ref_no_arr)
    {

        if (!empty($ref_no_arr)) {
            foreach ($ref_no_arr as $key => $value) {

                $ref_no_arr[$key]->from = ChequeRegister::where('check_ref_no', $value->check_ref_no)
                    ->select('ch_no')->pluck('ch_no')->first();
                $ref_no_arr[$key]->to = ChequeRegister::where('check_ref_no', $value->check_ref_no)
                    ->orderBy('id', 'desc')->select('ch_no')->pluck('ch_no')->first();
                $ref_no_arr[$key]->total_cheque = ChequeRegister::where('check_ref_no', $value->check_ref_no)
                    ->get()->count();
                $ref_no_arr[$key]->balanced_cheque = ChequeRegister::where('check_ref_no', $value->check_ref_no)
                   ->where('is_used', 'not_used')
                    ->get()->count();
            }
        }
        return $ref_no_arr;
    }

    public function cheque_balanced_report($id)
    {
        $this->data['page_title'] = "Cheque Use Report";

        $ch_ref_no = ChequeRegister::where('id',$id)->value('check_ref_no');
            $issedData = ChequeRegister::select('cheque_register.id', 'cheque_register.check_ref_no','cheque_register.ch_no','bank.ac_number','bank.bank_name')
                            ->join('bank', 'cheque_register.bank_id', '=', 'bank.id')
                            ->where('cheque_register.is_used','used')
                            ->where('cheque_register.is_failed',0)
                            ->where('cheque_register.check_ref_no',$ch_ref_no)
                            ->orderBy('cheque_register.id', 'ASC')
                            ->get();

                $from = ChequeRegister::where('check_ref_no', $ch_ref_no)->select('ch_no')->pluck('ch_no')->first();
                $to = ChequeRegister::where('check_ref_no', $ch_ref_no)->orderBy('id', 'desc')->select('ch_no')->pluck('ch_no')->first();
                $total_cheque = ChequeRegister::where('check_ref_no', $ch_ref_no)->get()->count();
                $balanced_cheque = ChequeRegister::where('check_ref_no', $ch_ref_no)->where('is_used', 'not_used')->get()->count();

                        foreach ($issedData as $key => $value) {
                            $issedData[$key]->from = $from;
                            $issedData[$key]->to = $to;
                            $issedData[$key]->total_cheque = $total_cheque;
                            $issedData[$key]->balanced_cheque = $balanced_cheque;
                        }
            //$issed_arr = $this->findChequeDetail($issedData);

            $this->data['issedData'] = $issedData;


            $failedData = ChequeRegister::select('cheque_register.*','bank.ac_number','bank.bank_name')
                            ->join('bank', 'cheque_register.bank_id', '=', 'bank.id')
                            ->where('cheque_register.is_failed',1)
                            ->where('cheque_register.check_ref_no',$ch_ref_no)
                            ->orderBy('cheque_register.id', 'ASC')
                            ->get();

            //$failed_arr = $this->findChequeDetail($failedData);

            $this->data['failedData'] = $failedData;

        return view('admin.cheque_stats_reports.cheque_balanced', $this->data);
    }

    public function generateCsvFiles($filename, $columnName, $rptData) {

        $name = date('D-M-Y h:m:s') . ' ' . $filename . '.csv';

        $file = fopen(storage_path('app/public/reports/cheque_report/') . $name, 'wb');

        if ($filename == "cheque_report") {

            fputcsv($file, $columnName);
            $data = [];
            foreach ($rptData as $k => $rowData) {

                $data[] = array($k + 1,
                $rowData->company_name,
                $rowData->bank_name,
                $rowData->check_ref_no,
                $rowData->ch_no,
                $rowData->project_name,
                $rowData->vendor_name,
                $rowData->issue_date,
                $rowData->cl_date,
                $rowData->amount,
                $rowData->work_detail,
                $rowData->remark,
                $rowData->is_used,
                $rowData->is_signed
                );
            }
        }

        foreach ($data as $row) {
            fputcsv($file, $row);
        }

        return asset('storage/'.str_replace('public/','','reports/cheque_report/'.$name));
    }

    //---------------------  cancel Cheque -------------------------
    //06/07/2020
    public function cancel_cheque_list() {
        $this->data['page_title'] = "Stop Payment";
        return view('admin.cheque_register.cancel_cheque', $this->data);
    }
    //06/07/2020
    public function get_cancel_cheque_list() {

        $datatable_fields = array('check_ref_no','company.company_name','bank.bank_name','ch_no','cancel_cheque_img','cancel_letterhead_img','outward_no');

        $request = Input::all();
        $conditions_array = ['cheque_register.is_cancel' => 1];

        $getfiled =array('cheque_register.id','company.company_name','bank.bank_name','bank.ac_number','ch_no','check_ref_no','is_cancel','cancel_cheque_img','cancel_letterhead_img','outward_no');
        $table = "cheque_register";
        $join_str=[];
        $join_str[0]['join_type']='';
        $join_str[0]['table'] = 'company';
        $join_str[0]['join_table_id'] ='cheque_register.company_id';
        $join_str[0]['from_table_id'] = 'company.id';

        $join_str[1]['join_type']='';
        $join_str[1]['table'] = 'bank';
        $join_str[1]['join_table_id'] ='cheque_register.bank_id';
        $join_str[1]['from_table_id'] = 'bank.id';

        $join_str[2]['join_type']='left';
        $join_str[2]['table'] = 'vendor';
        $join_str[2]['join_table_id'] ='cheque_register.party_detail';
        $join_str[2]['from_table_id'] = 'vendor.id';

        $join_str[3]['join_type']='left';
        $join_str[3]['table'] = 'project';
        $join_str[3]['join_table_id'] ='cheque_register.project_id';
        $join_str[3]['from_table_id'] = 'project.id';

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);
        die();
    }
    //06/07/2020
    public function add_cancel_cheque() {
        $this->data['page_title']    = 'Add Cancel Cheque';
        $this->data['module_title'] = "Cancel Cheque";
        $this->data['module_link'] = "admin.cancel_cheque_list";
        $this->data['companies']   = Companies::orderBy('company_name', 'ASC')->pluck('company_name','id');
        $this->data['outward_list']  = Inward_outwards::where('type','Outwards')->get(['inward_outward_no','id']);

        return view('admin.cheque_register.add_cancel_cheque', $this->data);
    }
    //06/07/2020
    public function update_cancel_cheque(Request $request)
    {
        $validator_normal = Validator::make($request->all(), [
            'company_id' => 'required',
            'bank_id' => 'required',
            'cheque_book' => 'required',
            'cheque_no' => 'required',
            'cancel_cheque_img' => 'required',
            'cancel_letterhead_img' => 'required',
            'outward_no' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_cancel_cheque')->with('error', 'Please follow validation rules.');
        }
        $data = $request->all();

        $document_file = '';
        if ($request->file('cancel_cheque_img')) {

            $document_file = $request->file('cancel_cheque_img');
            $original_file_name = explode('.', $document_file->getClientOriginalName());
            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);

            $file_path = $document_file->storeAs('public/cheque_cancel_docs', $new_file_name);
            if ($file_path) {
                $document_file = $file_path;
            }
        }
        $letterhead_document_file = '';
        if ($request->file('cancel_letterhead_img')) {

            $letterhead_document_file = $request->file('cancel_letterhead_img');
            $original_file_name = explode('.', $letterhead_document_file->getClientOriginalName());
            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);

            $file_path = $letterhead_document_file->storeAs('public/cheque_cancel_docs', $new_file_name);
            if ($file_path) {
                $letterhead_document_file = $file_path;
            }
        }

            $chequeModel =  ChequeRegister::where('check_ref_no',$data['cheque_book'])->where('ch_no',$data['cheque_no'])->first();
            $chequeModel->cancel_cheque_img = !empty($document_file) ? $document_file : NULL;
            $chequeModel->cancel_letterhead_img = !empty($letterhead_document_file) ? $letterhead_document_file : NULL;
            $chequeModel->outward_no = $data['outward_no'];
            $chequeModel->is_cancel = 1;
            $chequeModel->save();

        if (!empty($chequeModel)) {

            // User Action Log
            $company_name = Companies::whereId($request->get('company_id'))->value('company_name');
            $bank_name = Banks::whereId($request->get('bank_id'))->value('bank_name');
            $add_string = "<br>Company Name: ".$company_name."<br>Bank Name: ".$bank_name;
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => $data['cheque_book']." cheque book ref no cheque number ".$data['cheque_no']. " cancelled".$add_string,
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            return redirect()->route('admin.cancel_cheque_list')->with('success', 'Cheque Cancel successfully.');
        } else {
            return redirect()->route('admin.add_cancel_cheque')->with('error', 'Error occurre in insert. Try Again!');
        }

    }
}
