<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Common_query;
use Illuminate\Support\Facades\Validator;
use App\Imports\BankTransactionImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Leaves;
use App\LeaveCategory;
use App\Lib\Permissions;
use App\Role_module;
use Auth;

class LeaveCategoryController extends Controller
{
    public $data;

    public function __construct() {
		
        $this->data['module_title'] = "Leave Category";
        $this->data['module_link'] = "admin.leavecategory";
    }

    public function index() {
		
        $this->data['page_title'] = "Leave Category";
        $access_level             = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => 32])->get()->first(); 
        
        if(!empty($access_level)) {
            $this->data['access_rule'] = $access_level->access_level;
        }

        return view('admin.leavecategory.index', $this->data);
    }


    public function get_leavecatogry_list() {
		
        $datatable_fields = array('name','frequency','quantity','status','created_at','id');
        $request = Input::all();
        $conditions_array = [];

        $getfiled =array('id','name','frequency','quantity','status','created_at');
        $table = "leave_category";
        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request);
                                                  
        die();
    }
    public function change_category_status($id, $status) {
		 
        if (LeaveCategory::where('id', $id)->update(['status' => $status])) {
           
		   return redirect()->route('admin.leavecategory')->with('success', 'Status successfully updated.');
        }
        return redirect()->route('admin.leavecategory')->with('error', 'Error during operation. Try again!');
    }

    public function add_leavecategory() {
        $this->data['page_title'] = 'Add Category';
        $check_result = Permissions::checkPermission(32,3);
        if(!$check_result) {
            return redirect()->route('admin.dashboard')->with('error','Access Denied. You are not authorized to access that page.');
        }
        return view('admin.leavecategory.add_leavecategory', $this->data);
    }

    public function insert_leavecategory(Request $request) {
		 
        $validator_normal = Validator::make($request->all(), [
            'name' => 'required',
			'frequency' => 'required',
            'quantity' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_leavecategory')->with('error', 'Please follow validation rules.');
        }
      
        $bankModel = new LeaveCategory();
        $bankModel->name = $request->input('name');
        $bankModel->frequency = $request->input('frequency');
        $bankModel->quantity = $request->input('quantity');
        $bankModel->created_at = date('Y-m-d h:i:s');
        $bankModel->created_ip = $request->ip();
        $bankModel->updated_at = date('Y-m-d h:i:s');
        $bankModel->updated_ip = $request->ip();
        
        if ($bankModel->save()) {
            return redirect()->route('admin.leavecategory')->with('success', 'New Category added successfully.');
        } else {
            return redirect()->route('admin.add_leavecategory')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function edit_leavecategory($id) {
		
        $this->data['page_title'] = "Edit Category";
        
        $check_result = Permissions::checkPermission(32,2);
        
        if(!$check_result) {
            return redirect()->route('admin.dashboard')->with('error','Access Denied. You are not authorized to access that page.');
        }

        $this->data['category_detail'] = leavecategory::where('id', $id)->get();
      
     	if ($this->data['category_detail']->count() == 0) {
            return redirect()->route('admin.leavecategory')->with('error', 'Error Occurred. Try Again!');
        }
        return view('admin.leavecategory.edit_leavecategory', $this->data);
    }

    public function update_leavecategory(Request $request) {
	//p($request->all());
        $validator_normal = Validator::make($request->all(), [
            'name' => 'required',
            'frequency' => 'required',
            'quantity' => 'required'
        ]);
		
		
        if ($validator_normal->fails()) {
            return redirect()->route('admin.leavecategory')->with('error', 'Please follow validation rules.');
        } 
        $category_id = $request->input('id'); 
        $category_arr = [
            'name' => $request->input('name'),
            'frequency' => $request->input('frequency'),
            'quantity' => $request->input('quantity'),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
        ];
        
        leavecategory::where('id', $category_id)->update($category_arr);

        return redirect()->route('admin.leavecategory')->with('success', 'Bank successfully updated.');
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
    
    public function get_bank_by_company(Request $request){
        $company_id=$request->input('company_id');
        $bank_list=leavecategory::where('company_id',$company_id)->where('status','Enabled')->get();
        $html='';
        foreach($bank_list as $bank){
            $html .='<option value="'.$bank['id'].'">'.$bank['bank_name'].' ('.$bank['ac_number'].')'.'</option>';
        }
        echo $html; die();
    }
}
