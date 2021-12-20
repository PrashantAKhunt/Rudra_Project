<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Bank_cc;
use App\Common_query;
use Illuminate\Support\Facades\Input;
use App\Companies;
use App\Banks;

class BankccController extends Controller
{
    public $data;
    public function __construct() {
        $this->data['module_title']="Bank CC";
        $this->data['module_link']='admin.bankcc';
    }
    
    public function index(){
        $this->data['page_title']='Bank CC';
        return view('admin.bankcc.index', $this->data);
    }
    
    public function get_bankcc_list(){
        $datatable_fields = array('company.company_name','bank.bank_name','bank.ac_number','bank_cc.amount','bank_cc.bank_charges','bank_cc.start_date','bank_cc.end_date');
        $request = Input::all();
        $conditions_array = [];

        $getfiled =array('bank_cc.id','company.company_name','bank.bank_name','bank.ac_number','bank_cc.amount','bank_cc.bank_charges','bank_cc.start_date','bank_cc.end_date');
        $table = "bank_cc";
        $join_str=[];
        $join_str[0]['join_type']='';
        $join_str[0]['table'] = 'company';
        $join_str[0]['join_table_id'] ='bank_cc.company_id';
        $join_str[0]['from_table_id'] = 'company.id';
        
        $join_str[1]['join_type']='';
        $join_str[1]['table'] = 'bank';
        $join_str[1]['join_table_id'] ='bank_cc.bank_id';
        $join_str[1]['from_table_id'] = 'bank.id';
        
        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);
                                                  
        die();
    }
    
    public function add_bank_cc(){
        $this->data['page_title']="Add Bank CC";
        $this->data['company_list']= Companies::where('status','Enabled')->get(['company_name','id']);
        
        return view('admin.bankcc.add_bank_cc', $this->data);
    }
    
}
