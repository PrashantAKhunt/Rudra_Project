<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Input;
use App\Common_query;
use App\Companies;
use App\Banks;
use App\Projects;
use App\Vendors;
use App\ChequeRegister;
use Illuminate\Support\Facades\Validator;
use Auth;
use App\User;

class GeneralRegisterController extends Controller
{
    public $data;

    public function __construct() {
        $this->data['module_title'] = "General Cheque Register";
        $this->data['module_link'] = "admin.companies";
    }

    public function index() {
        $this->data['page_title'] = "General Cheque Register";
        return view('admin.general_register.index', $this->data);
    }


    public function get_general_register_list() {
        $datatable_fields = array('cheque_register.id','company.company_name','bank.bank_name','ch_no','check_ref_no','cheque_register.use_type');
        $request = Input::all();
        $conditions_array = ['cheque_register.use_type'=>'General'];

        $getfiled =array('cheque_register.id','company.company_name','bank.bank_name','ch_no','check_ref_no','cheque_register.use_type');
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

    public function add_general_register() {
        $this->data['page_title'] = 'Add General Cheque Register';
        $this->data['companies']  = Companies::pluck('company_name','id');
        $this->data['banks']      = Banks::pluck('bank_name','id');
        $this->data['users']      = User::select('name','id')->get()->toArray();
        return view('admin.general_register.add_general_register', $this->data);
    }

    public function insert_general_register(Request $request) {
        $validator_normal = Validator::make($request->all(), [
            'company_id' => 'required',
            'bank_id' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_cheque_register')->with('error', 'Please follow validation rules.');
        }
        
        $cheq_arr = [
            'issue_date'   => date('Y-m-d h:i:s',strtotime($request->input('issue_date'))),
            'company_id'   => $request->input('company_id'),
            'party_detail' => $request->input('user_id'),
            'work_detail'  => $request->input('work_detail'),
            'amount'       => $request->input('amount'),
            'remark'       => $request->input('remark'),
            'is_used'      => 'used',
            'use_type'     => 'General',
            'created_at'   => date('Y-m-d h:i:s'),
            'created_ip'   => $request->ip(),
            'updated_at'   => date('Y-m-d h:i:s'),
            'updated_by'   =>Auth::user()->id,
        ];
        
        $chequeModel= ChequeRegister::where('id', $request->input('cheque_id'))->update($cheq_arr);
        if (!empty($chequeModel)) {
            return redirect()->route('admin.general_register')->with('success', 'Cheque details edit successfully.');
        } else {
            return redirect()->route('admin.add_general_register')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function delete_general_register($id) {
        if (ChequeRegister::where('id', $id)->delete()) {
            return redirect()->route('admin.rtgs_neft_register')->with('success', 'Delete successfully updated.');
        }
    return redirect()->route('admin.rtgs_neft_register')->with('error', 'Error during operation. Try again!');
    }

    public function get_bank_general_list()
    {
        if(!empty($_GET['company_id'])) {
           $company_id = $_GET['company_id']; 
           $bank_data = Banks::select('bank_name','id')->where(['company_id' => $company_id])->get()->toArray();
           $html = "<select name='bank_id' class='form-control'>
                    <option>Select bank</option>";
           foreach ($bank_data as $key => $bank_data_value) {
                $html.= "<option value=".$bank_data_value['id'].">".$bank_data_value['bank_name']."</option>";
           }
           $html.="</select>";
           echo  $html;
           die();
        }
    }

    public function delete_general(Request $request)
    {
        if(!empty($request->input('del_cheque_ids')))
        {
            if (ChequeRegister::whereIn('id', explode(',',$request->input('del_cheque_ids')))->delete()) {
                return redirect()->route('admin.rtgs_neft_register')->with('success', 'Delete successfully updated.');
            }    
        }

        return redirect()->route('admin.rtgs_neft_register')->with('error', 'Error during operation. Try again!');
    }

    public function get_general_project_list()
    {
        if(!empty($_GET['company_id'])) {
           $company_id = $_GET['company_id']; 
           $project_data = Projects::select('project_name','id')->where(['company_id' => $company_id])->get()->toArray();
           $html = "<select id='project_id' name='project_id' class='form-control'>
                    <option>Select project</option>";
           foreach ($project_data as $key => $project_data_value) {
                $html.= "<option value=".$project_data_value['id'].">".$project_data_value['project_name']."</option>";
           }

           $html.="</select>";
           echo  $html;
           die();
        }
    }

    public function get_general_vendor_list()
    {
        if(!empty($_GET['company_id'])) {
           $company_id = $_GET['company_id']; 
           $vendor_data = Vendors::select('vendor_name','id')->where(['company_id' => $company_id])->get()->toArray();
           $html = "<select id='vendor_id' name='vendor_id' class='form-control'>
                    <option>Select vendor</option>";
           foreach ($vendor_data as $key => $project_data_value) {
                $html.= "<option value=".$project_data_value['id'].">".$project_data_value['vendor_name']."</option>";
           }

           $html.="</select>";
           echo  $html;
           die();
        }
    }

    public function get_general_cheque_list()
    {
        if(!empty($_GET['company_id'])) {
           $company_id = $_GET['company_id'];
           //$bank_id    = $_GET['bank_id'];
           
           $cheque_data = ChequeRegister::select('ch_no','id')->where(['company_id' => $company_id])->where(['is_used' =>'not_used'])->get()->toArray();
           $html = "<select id='cheque_id' name='cheque_id' class='form-control'>
                    <option>Select cheque</option>";
           foreach ($cheque_data as $key => $cheque_data_value) {
                $html.= "<option value=".$cheque_data_value['id'].">".$cheque_data_value['ch_no']."</option>";
           }

           $html.="</select>";
           echo  $html;
           die();
        }
    }
}
