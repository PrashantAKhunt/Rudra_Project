<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Input;
use App\Common_query;
use App\Banks;
use App\BillTransaction;
use App\Companies;
use App\Vendors;
use App\Heads;
use Illuminate\Support\Facades\Validator;
use App\Imports\BankTransactionImport;
use Maatwebsite\Excel\Facades\Excel;

class BillTransactionController extends Controller
{
    public $data;

    public function __construct() {
        $this->data['module_title'] = "bills";
        $this->data['module_link'] = "admin.bills";
    }

    public function index() {
        $this->data['page_title'] = "Bills"; 
        return view('admin.bill.index', $this->data);
    }


    public function get_bill_list() {
        $datatable_fields = array('bill_date','vendor.vendor_name','request_by','verify_by','account_transfer_detail','bank.bank_name','company.company_name'  ,'mode_of_payment' ,'head.head_name','account_number','deduction_details' ,'pending_amount','amount_released','notes' ,'budget_sheet_no','bill_transactions.status','bill_transactions.created_at');
        $request = Input::all();
        $conditions_array = [];

        $getfiled =array('bill_transactions.id','bill_date','vendor.vendor_name','request_by','verify_by','account_transfer_detail','bank.bank_name','company.company_name'  ,'mode_of_payment' ,'head.head_name','account_number','deduction_details' ,'pending_amount','amount_released','notes' ,'budget_sheet_no','bill_transactions.status','bill_transactions.created_at');
        $table = "bill_transactions";
        $join_str=[];
        $join_str[0]['join_type']='inner';
        $join_str[0]['table'] = 'company';
        $join_str[0]['join_table_id'] ='bill_transactions.company_id';
        $join_str[0]['from_table_id'] = 'company.id';
        $join_str[1]['join_type']='inner';
        $join_str[1]['table'] = 'vendor';
        $join_str[1]['join_table_id'] ='bill_transactions.vendor_id';
        $join_str[1]['from_table_id'] = 'vendor.id';
        $join_str[2]['join_type']='inner';
        $join_str[2]['table'] = 'head';
        $join_str[2]['join_table_id'] ='bill_transactions.head_id';
        $join_str[2]['from_table_id'] = 'head.id';
        $join_str[3]['join_type']='inner';
        $join_str[3]['table'] = 'bank';
        $join_str[3]['join_table_id'] ='bill_transactions.account_transfer_detail';
        $join_str[3]['from_table_id'] = 'bank.id';
        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);                                     
        die();
    }
    public function change_bill_status($id, $status) {
        if (Banks::where('id', $id)->update(['status' => $status])) {
            return redirect()->route('admin.banks')->with('success', 'Status successfully updated.');
        }
        return redirect()->route('admin.banks')->with('error', 'Error during operation. Try again!');
    }

    public function add_bill() {
        $this->data['page_title'] = 'Add bill';
        $this->data['companies'] = Companies::pluck('company_name','id');
        $this->data['vendors'] = Vendors::pluck('vendor_name','id');
        $this->data['banks'] = Banks::where('status','Enabled')->pluck('bank_name','id');
        $this->data['heads'] = Heads::where('status','Enabled')->pluck('head_name','id');
        
        return view('admin.bill.add_bill', $this->data);
    }

    public function insert_bill(Request $request) {
        $validator_normal = Validator::make($request->all(), [
            'bill_date' => 'required',
            'vendor_id' => 'required',
            'request_by' => 'required',
            'account_transfer_detail' => 'required',
            'company_id' => 'required',
            'mode_of_payment' => 'required',
            'head_id' => 'required',
            'account_number' => 'required',
            'account_number' => 'required',
            'deduction_details' => 'required',
            'pending_amount' => 'required',
            'amount_released' => 'required',
            'notes' => 'required',
            'budget_sheet_no' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_bill')->with('error', 'Please follow validation rules.');
        }
      
        $billModel = new BillTransaction();
        $billModel->bill_date = $request->input('bill_date');
        $billModel->vendor_id = $request->input('vendor_id');
        $billModel->request_by = $request->input('request_by');
        $billModel->account_transfer_detail = $request->input('account_transfer_detail');
        $billModel->company_id = $request->input('company_id');
        $billModel->mode_of_payment = $request->input('mode_of_payment');
        $billModel->head_id = $request->input('head_id');
        $billModel->account_number = $request->input('account_number');
        $billModel->account_number = $request->input('account_number');
        $billModel->deduction_details = $request->input('deduction_details');
        $billModel->pending_amount = $request->input('pending_amount');
        $billModel->amount_released = $request->input('amount_released');
        $billModel->notes = $request->input('notes');
        $billModel->budget_sheet_no = $request->input('budget_sheet_no');
       
        $billModel->created_at = date('Y-m-d h:i:s');
        $billModel->created_ip = $request->ip();
        $billModel->updated_at = date('Y-m-d h:i:s');
        $billModel->updated_ip = $request->ip();
        
        if ($billModel->save()) {
            return redirect()->route('admin.bills')->with('success', 'New bill added successfully.');
        } else {
            return redirect()->route('admin.add_bill')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function edit_bill($id) {
       
        $this->data['page_title'] = "Edit Bill";
        $this->data['bill_detail']= BillTransaction::where('id',$id)->get();
        $this->data['companies'] = Companies::pluck('company_name','id');
        $this->data['vendors'] = Vendors::pluck('vendor_name','id');
        $this->data['banks'] = Banks::where('status','Enabled')->pluck('bank_name','id');
        $this->data['heads'] = Heads::where('status','Enabled')->pluck('head_name','id');
        return view('admin.bill.edit_bill', $this->data);
    }

    public function update_bill(Request $request) {
        $validator_normal = Validator::make($request->all(), [
            'bank_name' => 'required',
            'detail' => 'required',
            'company_id' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.banks')->with('error', 'Please follow validation rules.');
        } 
        $bank_id = $request->input('id'); 
        $bank_arr = [
            'bank_name' => $request->input('bank_name'),
            'detail' => $request->input('detail'),
            'company_id' => $request->input('company_id'),
            'created_at' => date('Y-m-d h:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
        ];
        
        Banks::where('id', $bank_id)->update($bank_arr);

        return redirect()->route('admin.banks')->with('success', 'Bank successfully updated.');
    }


    public function import_csv() {
        $this->data['page_title'] = "Import CSV";
        return view('admin.bank.import_csv', $this->data);
    }

    public function uploadBankTransactions(){
        Excel::import(new BankTransactionImport,request()->file('file'));
        return back()->with('success', 'Bank transactions imported successfully.');
    }

    public function get_transactions() {
        $this->data['page_title'] = "Transactions"; 
        return view('admin.bank.transactions', $this->data);
    }


    public function get_transaction_list() {
        $datatable_fields = array('company_name', 'bank_name', 'tx_id', 'tx_date','particular', 'cheque_num', 'internal', 'voucher_type' ,'project','head_id','sub_head' ,'received','paid','balance' ,'narration','remark','bank_transaction.created_at');
        $request = Input::all();
        $conditions_array = [];

        $getfiled =array('company_name', 'bank_name', 'tx_id', 'tx_date', 'particular', 'cheque_num', 'internal', 'voucher_type', 'project', 'head_id', 'sub_head', 'received', 'paid', 'balance', 'narration', 'remark', 'bank_transaction.created_at');
        $table = "bank_transaction";
        $join_str=[];
        $join_str[0]['join_type']='inner';
        $join_str[0]['table'] = 'company';
        $join_str[0]['join_table_id'] ='bank_transaction.company_id';
        $join_str[0]['from_table_id'] = 'company.id';
        $join_str[1]['join_type']='inner';
        $join_str[1]['table'] = 'bank';
        $join_str[1]['join_table_id'] ='bank_transaction.bank_id';
        $join_str[1]['from_table_id'] = 'bank.id';

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);
                                                  
        die();
    }
}
