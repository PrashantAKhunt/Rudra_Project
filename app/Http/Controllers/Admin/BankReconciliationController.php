<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Common_query;
use Illuminate\Support\Facades\Validator;
use App\Companies;
use App\Banks;
use App\BankReconciliation;
use App\BankPaymentApproval;
use App\Imports\BankReconciliationImport;
use DB;
use Illuminate\Support\Facades\Storage;
use Rap2hpoutre\FastExcel\FastExcel;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\PdfToText\Pdf;


class BankReconciliationController extends Controller {

    public $data;

    public function __construct() {
        $this->data['module_title'] = "Bank Reconciliation";
        $this->data['module_link'] = "admin.bank_reconciliation";
    }
    
    public function index() {
        $this->data['page_title'] = "Bank Reconciliation";
        $this->data['companies']       = Companies::where('status', 'Enabled')->pluck('company_name','id');
        return view('admin.bank_reconciliation.index', $this->data);
    }
    
    public function get_bank_reconciliation_list(){
        $datatable_fields = array('company.company_name','bank.bank_name','txn_date','value_date','description','reff_cheque_no','branch_code','debit','credit','balance');
        $request = Input::all();
        $conditions_array = [];

        $getfiled = array('bank_reconciliation.id','company.company_name','bank.bank_name','bank.ac_number','txn_date','value_date','description','reff_cheque_no','branch_code','debit','credit','balance','match_payment');
        $table = "bank_reconciliation";
        $join_str=[];
        $join_str[0]['join_type']='';
        $join_str[0]['table'] = 'company';
        $join_str[0]['join_table_id'] ='bank_reconciliation.company_id';
        $join_str[0]['from_table_id'] = 'company.id';

        $join_str[1]['join_type']='';
        $join_str[1]['table'] = 'bank';
        $join_str[1]['join_table_id'] ='bank_reconciliation.bank_id';
        $join_str[1]['from_table_id'] = 'bank.id';

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);
        die();
    }

    public function add_bank_reconciliation(){
        $this->data['page_title'] = "Add Bank Reconciliation";
        $this->data['companies'] = Companies::where('status', 'Enabled')->orderBy('company_name')->pluck('company_name','id');
        return view('admin.bank_reconciliation.add_bank_reconciliation', $this->data);
    }

    public function get_company_bank_list(Request $request)
    {
        $company_id = $request->get('company_id');
        if(!empty($company_id)) {
            
           $bank_data = Banks::select('bank_name','id','ac_number')
           ->where(['company_id' => $company_id])
           ->get()->toArray();
           $html = "";
           foreach ($bank_data as $key => $bank_data_value) {
                $html.= "<option value=".$bank_data_value['id'].">".$bank_data_value['bank_name']." (".$bank_data_value['ac_number'].")"."</option>";
           }
           echo  $html;
           die();
        }
    }

    public function save_bank_reconciliation(Request $request){
        // dd($request->all());
        $check_data = 0;
        $statement = [];
        $request_data = $request->all();
        if($request->get('bank_type') == "State Bank Of India"){

            $extensions = "xls";
            $result = array($request->file('bank_statement')->getClientOriginalExtension());

            if ($extensions != $result[0]) {
                return redirect()->route('admin.add_bank_reconciliation')->with('error', 'Allow xls file only.');
            }

            $statement = $this->sbiStatement($request_data);
            if ($statement == "not match") {
                return redirect()->route('admin.bank_reconciliation')->with('error', 'Please select valid bank. Try Again!');
            }
            // echo "<pre>";print_r($statement);exit;
        }elseif ($request->get('bank_type') == "DENA BANK") {

            $extensions = "xls";
            $result = array($request->file('bank_statement')->getClientOriginalExtension());

            if ($extensions != $result[0]) {
                return redirect()->route('admin.add_bank_reconciliation')->with('error', 'Allow xls file only.');
            }

            $statement = $this->denaStatement($request_data);
            if($statement == "not match"){
                return redirect()->route('admin.bank_reconciliation')->with('error', 'Please select valid bank. Try Again!');
            }
            // echo "<pre>";print_r($statement);exit;
        }
        elseif ($request->get('bank_type') == "HDFC Bank") {

            $extensions = "xlsx";
            $result = array($request->file('bank_statement')->getClientOriginalExtension());

            if ($extensions != $result[0]) {
                return redirect()->route('admin.add_bank_reconciliation')->with('error', 'Allow xlsx file only.');
            }

            $statement = $this->hdfcStatementChange($request_data);
            if ($statement == "not match") {
                return redirect()->route('admin.bank_reconciliation')->with('error', 'Please select valid bank. Try Again!');
            }
            // echo "<pre>";print_r($statement);exit;
        }
        elseif ($request->get('bank_type') == "American Express") {
            // dd('American Express');
            $check_data = 0;
        }
        // echo "<pre>";print_r($statement);exit;
        
        if($statement){
            foreach ($statement as $key => $value) {
                $data_exists = BankReconciliation::where('company_id',$value['company_id'])->where('bank_id',$value['bank_id'])->where('txn_date',$value['txn_date'])->exists();
                if(!$data_exists){
                    if($value['debit']){
                        $amount = $value['debit'];
                    }else{
                        $amount = $value['credit'];
                    }
                    $bankPayment_approval = BankPaymentApproval::LeftJoin('cheque_register', 'cheque_register.id', '=' ,'bank_payment_approval.cheque_number')->where('cheque_register.ch_no',$value['cheque_number'])->where('bank_payment_approval.amount',$amount)->first();
                    if($bankPayment_approval){
                        $value['match_payment'] = 1;
                        $value['bank_payment_id'] = $bankPayment_approval['id'];
                    }
                    unset($value['cheque_number']);
                    BankReconciliation::insert($value);
                    
                    $check_data = 1;
                }else{
                    $check_data = 1;
                }
            }
        }
        /* dd($statement);
        exit; */
        if($check_data){
            return redirect()->route('admin.bank_reconciliation')->with('success', 'Bank statement inserted successfully.');
        }
        return redirect()->route('admin.bank_reconciliation')->with('error', 'Error occurre in insert. Try Again!');
    }

    public function sbiStatement($request){
        // return $request;
        $path = $request['bank_statement']->getRealPath();
        $csv_data = array_map('str_getcsv', file($path));
        /* $csv_heading = array_slice($csv_data, 0, 1);
        $csv_data = array_slice($csv_data, 1); */
        // echo "<pre>";print_r($csv_data);exit;
        // dd($data);
        $transation_arr = [];
        $bank_arr = [];
        foreach ($csv_data as $key => $value) {
            if(trim($value[0])){
                $repl_one=str_replace(',','.',$value);
                $repl=preg_replace('/[\x00-\x1F\x7F-\xFF]/','?',$repl_one);
                // echo "<pre>";print_r($repl);

                foreach($repl as $dat){
                    $transation_arr[] = explode("?",$dat);
                }
            }
        }
        // echo "<pre>";print_r($transation_arr);exit;
        $i = 0;

        $selected_bank = $this->get_bank_ac_number($request['bank_id']);
        $statement_ac_number = substr($transation_arr[0][1], 7);
        if ($statement_ac_number == $selected_bank['ac_number']) {

            foreach ($transation_arr as $key_transaction => $transaction) {
                if(count($transaction) > 4){
                    // echo "<pre>";print_r($transaction);
                    if(count($transaction) == 8){
                        // echo "<pre>";print_r($transaction[0]);
                        $bank_arr[$i]['company_id'] = $request['company_id'];
                        $bank_arr[$i]['bank_id'] = $request['bank_id'];
                        $bank_arr[$i]['txn_date'] = $this->remove_slashes($transaction[0]);
                        $bank_arr[$i]['value_date'] = $this->remove_slashes($transaction[1]);
                        $bank_arr[$i]['description'] = $transaction[2];
                        $bank_arr[$i]['reff_cheque_no'] = $transaction[3];
                        
                        $cheque_no = explode(' ', $transaction[3]);
                        if($cheque_no[1] == "/"){
                            if($cheque_no[2]){
                                $bank_arr[$i]['cheque_number'] = $cheque_no[2];    
                            }else{
                                $bank_arr[$i]['cheque_number'] = 0;    
                            }
                        }else{
                            $bank_arr[$i]['cheque_number'] = 0;
                        }


                        $bank_arr[$i]['branch_code'] = $transaction[4];
                        $bank_arr[$i]['debit'] = $transaction[5];
                        $bank_arr[$i]['credit'] = $transaction[6];
                        $bank_arr[$i]['balance'] = $transaction[7];
                        $bank_arr[$i]['created_ip'] = \Request::ip();
                        $bank_arr[$i]['updated_ip'] = \Request::ip();
                        
                        $i++;
                    }else{

                    }
                }
            }

            return $bank_arr;
        }else{
            return "not match";
        }
    }

    public function hdfcStatement($request){
        // return $request;
        $path = $request['bank_statement']->getRealPath();
        // return $collection = fastexcel()->import($path);
        $hdfc_arr = [];
        $data = (new FastExcel)->import($path, function ($line) use(&$request,&$hdfc_arr){
            // print_r(count($line));
            $new_date = $this->convertDate($line['Date']);
            $new_date1 = $this->convertDate($line['Value Dt']);
            if(preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$new_date)){
                $transation_arr = [
                    'company_id' => $request['company_id'],
                    'bank_id' => $request['bank_id'],
                    'txn_date' => $new_date,
                    'value_date' => $new_date1,
                    'description' => $line['Narration'],
                    'reff_cheque_no' => $line['Chq./Ref.No.'],
                    'cheque_number' => "0",
                    'branch_code' => "",
                    'debit' => $line['Withdrawal Amt.'],
                    'credit' => $line['Deposit Amt.'],
                    'balance' => $line['Closing Balance'],
                    'created_ip' => \Request::ip(),
                    'updated_ip' => \Request::ip(),
                ];
                // echo "<pre>";
                // print_r($transation_arr);
                // return $transation_arr;
                array_push($hdfc_arr,$transation_arr);
            }
           
        });
        // echo "<pre>";
        // print_r($hdfc_arr);
        return $hdfc_arr;
    }

    public function hdfcStatementChange($request){
        $path = $request['bank_statement']->store('excel-files');
        // $path = $request['bank_statement']->getRealPath();
        $selected_bank = $this->get_bank_ac_number($request['bank_id']);
        // dd($path);
        $import = new BankReconciliationImport;
        $data = Excel::toArray($import,$path, null, \Maatwebsite\Excel\Excel::XLSX);
        $statement_ac_number_arr = explode(' ', $data[0][14][4]);
        $statement_ac_number = str_replace(":","", $statement_ac_number_arr[2]);
        // return $statement_ac_number;
        if($statement_ac_number == $selected_bank['ac_number']){        
            $new_arr = [];
            foreach ($data[0] as $key => $value) {
                if ($key >= 22) {
                    if(preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $this->convertDate($value[0]))){
                        $new_arr[$key]['company_id'] = $request['company_id'];
                        $new_arr[$key]['bank_id'] = $request['bank_id'];

                        $new_arr[$key]['txn_date'] = $this->convertDate($value[0]);
                        $new_arr[$key]['value_date'] = $this->convertDate($value[3]);
                        $new_arr[$key]['reff_cheque_no'] = $value[2];
                        $new_arr[$key]['cheque_number'] = "0";
                        $new_arr[$key]['description'] = $value[1];
                        if (!empty($value[4])) {
                            $new_arr[$key]['debit'] = $value[4];
                            $new_arr[$key]['credit'] = "";
                        } else {
                            $new_arr[$key]['credit'] = $value[5];
                            $new_arr[$key]['debit'] = "";
                        }
                        $new_arr[$key]['balance'] = $value[6];
                    }
                }
            }
            return $new_arr;
        }else{
            return "not match";
        }
    }

    public function denaStatement($request){
        // $path = $request['bank_statement']->getClientOriginalName();
        $path = $request['bank_statement']->store('excel-files');

        $selected_bank = $this->get_bank_ac_number($request['bank_id']);
        // dd($selected_bank);
        $import = new BankReconciliationImport;
        $data = Excel::toArray($import, $path);
        // dd($data);
        if($data[0]){
            // return $data[0][3][3];
            if($data[0][3][3] == $selected_bank['ac_number']){
                $new_arr = [];
                foreach ($data[0] as $key => $value) {
                    if ($key >= 31) {
                        $new_arr[$key]['company_id'] = $request['company_id'];
                        $new_arr[$key]['bank_id'] = $request['bank_id'];
                        $new_arr[$key]['txn_date'] = $this->remove_slashes($value[1]);
                        $new_arr[$key]['value_date'] = $this->remove_slashes($value[1]);
                        $new_arr[$key]['reff_cheque_no'] = $value[2];
                        $new_arr[$key]['cheque_number'] = $value[2] ? $value[2] : "0";
                        $new_arr[$key]['description'] = $value[4] . " / " . $value[6];
                        if ($value[7] == "Dr.") {
                            $new_arr[$key]['debit'] = str_replace(',', '', $value[8]);
                            $new_arr[$key]['credit'] = "";
                        } else {
                            $new_arr[$key]['credit'] = str_replace(',', '', $value[8]);
                            $new_arr[$key]['debit'] = "";
                        }
                        $remove_balance = str_replace('-', '', $value[10]);
                        $new_arr[$key]['balance'] = str_replace(',', '', $remove_balance);
                    }
                }
                return $new_arr;
            }else{
                return "not match";
            }
            
        }else{
            return [];
        }
        /* $data = (new FastExcel)->import($path, function ($line) {
            echo "<pre>";
            print_r($line);    
        }); */

    }

    public function get_bank_ac_number($id){
        $banks = Banks::whereId($id)->first();
        return $banks;
    }

    public function remove_slashes($date){
        $new_data = str_replace("/", "-", $date);
        return date('Y-m-d',strtotime($new_data));
    }

    public function convertDate($date){
        $new_data = str_replace("/", "-", $date);
        $arr_date = explode('-',$new_data);
        if(count($arr_date) == 3){
            $new_date = $arr_date[2]."-".$arr_date[1]."-".$arr_date[0]; 
            $date2 = strtotime($new_date);
            return date('Y-m-d',$date2);
        }
    }
}
