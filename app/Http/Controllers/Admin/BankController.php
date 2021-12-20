<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;
use App\Common_query;
use App\Banks;
use App\Companies;
use App\Lib\Permissions;
use App\Lib\NotificationTask;
use Illuminate\Support\Facades\Validator;
use App\Imports\BankTransactionImport;
use Maatwebsite\Excel\Facades\Excel;

class BankController extends Controller {

    public $data;
    public $notification_task;

    public function __construct() {
        $this->notification_task = new NotificationTask();
        $this->data['module_title'] = "Banks";
        $this->data['module_link'] = "admin.banks";
        $this->module_id = 9;
    }

    public function index() {
        $this->data['page_title'] = "Banks";
        $this->data['view_special_permission'] = Permissions::checkSpecialPermission($this->module_id);

        return view('admin.bank.index', $this->data);
    }

    public function get_bank_list() {

        $datatable_fields = array('bank.bank_name','bank.bank_short_name',
            'company.company_name', 'ac_number', 'beneficiary_name', 'ifsc', 'branch', 'account_type', 'bank.detail', 'bank.status', 'bank.created_at');
        $request = Input::all();
        $conditions_array = ['bank.is_approved' => 1];

        $getfiled = array('bank.*', 'company.company_name');
        $table = "bank";

        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'company';
        $join_str[0]['join_table_id'] = 'bank.company_id';
        $join_str[0]['from_table_id'] = 'company.id';

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array,
                $getfiled, $request, $join_str);

        die();
    }

    public function change_bank_status_now($id, $status) {
        if (Banks::where('id', $id)->update(['status' => $status])) {
            return redirect()->route('admin.banks')->with('success', 'Status successfully updated.');
        }
        return redirect()->route('admin.banks')->with('error', 'Error during operation. Try again!');
    }

    public function add_bank(Request $request) {
        $view_special_permission = Permissions::checkSpecialPermission($this->module_id);
        if(!$view_special_permission){
            return redirect()->route('admin.dashboard')->with('error','Access denied. You do not have permission to access this module.');
        }
        $this->data['page_title'] = 'Add bank';
        // $this->data['companies'] = Companies::pluck('company_name', 'id');
        $this->data['companies'] = Companies::orderBy('company_name', 'asc')->pluck('company_name', 'id');
        // echo "<pre>";
        // print_r($this->data['companies']);die;
        return view('admin.bank.add_bank', $this->data);
    }

    public function insert_bank(Request $request) {
        $validator_normal = Validator::make($request->all(), [
                    'bank_name' => 'required',
                    'detail' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_bank')->with('error', 'Please follow validation rules.');
        }

        $bankModel = new Banks();
        $bankModel->user_id = Auth::user()->id;
        $bankModel->bank_name = $request->input('bank_name');
        $bankModel->bank_short_name = $request->input('bank_short_name');        
        $bankModel->beneficiary_name = $request->input('beneficiary_name');
        $bankModel->ac_number = $request->input('account_number');
        $bankModel->ifsc = $request->input('ifsc');
        $bankModel->branch = $request->input('branch');
        $bankModel->account_type = $request->input('account_type');
        $bankModel->detail = $request->input('detail');
        $bankModel->company_id = $request->input('company_id');
        if (Auth::user()->role != config('constants.SuperUser')) {
            $bankModel->status = 'Disabled';
            $bankModel->is_approved = 0;
        } else {
            $bankModel->status = 'Enabled';
            $bankModel->is_approved = 1;
        }
        $bankModel->created_at = date('Y-m-d h:i:s');
        $bankModel->created_ip = $request->ip();
        $bankModel->updated_at = date('Y-m-d h:i:s');
        $bankModel->updated_ip = $request->ip();

        if ($bankModel->save()) {
            $module = 'Bank';
            $this->notification_task->entryApprovalNotify($module);
            return redirect()->route('admin.banks')->with('success', 'New bank added successfully.');
        } else {
            return redirect()->route('admin.add_bank')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function edit_bank($id) {
        $view_special_permission = Permissions::checkSpecialPermission($this->module_id);
        if(!$view_special_permission){
            return redirect()->route('admin.dashboard')->with('error','Access denied. You do not have permission to access this module.');
        }
        $this->data['page_title'] = "Edit bank";
        $this->data['bank_detail'] = Banks::where('bank.id', $id)->get();
        if ($this->data['bank_detail']->count() == 0) {
            return redirect()->route('admin.banks')->with('error', 'Error Occurred. Try Again!');
        }
        $this->data['companies'] = Companies::pluck('company_name', 'id');
        return view('admin.bank.edit_bank', $this->data);
    }

    public function update_bank(Request $request) {
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
            'user_id' =>  Auth::user()->id,
            'bank_name' => $request->input('bank_name'),
            'bank_short_name' => $request->input('bank_short_name'),            
            'detail' => $request->input('detail'),
            'company_id' => $request->input('company_id'),
            'beneficiary_name' => $request->input('beneficiary_name'),
            'ac_number' => $request->input('account_number'),
            'ifsc' => $request->input('ifsc'),
            'branch' => $request->input('branch'),
            'account_type' => $request->input('account_type'),
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

    public function uploadBankTransactions() {
        Excel::import(new BankTransactionImport, request()->file('file'));
        return back()->with('success', 'Bank transactions imported successfully.');
    }

    public function get_transactions() {
        $this->data['page_title'] = "Transactions";
        return view('admin.bank.transactions', $this->data);
    }

    public function get_transaction_list() {
        $datatable_fields = array('company_name', 'bank_name', 'tx_id', 'tx_date', 'particular', 'cheque_num', 'internal', 'voucher_type', 'project', 'head_id', 'sub_head', 'received', 'paid', 'balance', 'narration', 'remark', 'bank_transaction.created_at');
        $request = Input::all();
        $conditions_array = [];

        $getfiled = array('company_name', 'bank_name', 'tx_id', 'tx_date', 'particular', 'cheque_num', 'internal', 'voucher_type', 'project', 'head_id', 'sub_head', 'received', 'paid', 'balance', 'narration', 'remark', 'bank_transaction.created_at');
        $table = "bank_transaction";
        $join_str = [];
        $join_str[0]['join_type'] = 'inner';
        $join_str[0]['table'] = 'company';
        $join_str[0]['join_table_id'] = 'bank_transaction.company_id';
        $join_str[0]['from_table_id'] = 'company.id';
        $join_str[1]['join_type'] = 'inner';
        $join_str[1]['table'] = 'bank';
        $join_str[1]['join_table_id'] = 'bank_transaction.bank_id';
        $join_str[1]['from_table_id'] = 'bank.id';

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);

        die();
    }

    public function get_bank_by_company(Request $request) {
        $company_id = $request->input('company_id');
        $bank_list = Banks::where('company_id', $company_id)->where('status', 'Enabled')->get();
        $html = '';
        foreach ($bank_list as $bank) {
            $html .= '<option value="' . $bank['id'] . '">' . $bank['bank_name'] . ' (' . $bank['ac_number'] . ')' . '</option>';
        }
        echo $html;
        die();
    }

    public function delete_bank($id) {
        if (Banks::where('id', $id)->delete()) {
            return redirect()->route('admin.banks')->with('success', 'Delete successfully updated.');
        }
        return redirect()->route('admin.banks')->with('error', 'Error during operation. Try again!');
    }

}
