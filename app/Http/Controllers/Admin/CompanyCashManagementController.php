<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use App\Common_query;
use App\CompanyCash;
use App\Company_cash_management;
use Illuminate\Support\Facades\DB;
use App\Employee_cash_management;
use App\User;
use App\Companies;
use App\Cash_transfer;
use App\Clients;
use App\Employees;
use App\Projects;
use Illuminate\Support\Facades\Validator;
use App\Lib\Permissions;
use App\Lib\UserActionLogs;

class CompanyCashManagementController extends Controller
{
    public $data;
    private $module_id = 73;
    public $user_action_logs;

    public function __construct()
    {

        $this->user_action_logs = new UserActionLogs();
        $this->data['module_title'] = "Company Cash Management";
        $this->data['module_link'] = "admin.company_cash_management";
    }

    /*public function insert_cash_transfer(Request $request)
    {
        $validator_normal = Validator::make($request->all(), [
            'company_id' => 'required',
            'balance' => 'required'
            
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_company_cash')->with('error', 'Please follow validation rules.');
        } 
        $request_data = $request->all();

        $cashModel = new Cash_transfer();

        if ($request_data['check_from_btn'] == 'from_company') {
                $cashModel->account_id = $request->input('company_id');
                $cashModel->account_type = "company"; 
                $company_bfr_amnt = Company_cash_management::where('company_id',$request_data['company_id'])->value('balance');
                $cashModel->txn_before_balance = $company_bfr_amnt; 
                $cashModel->txn_after_balance = $company_bfr_amnt - $request->input('balance');
                
                //-------------------------
                $own_cmp_arr = [
                    'updated_at' => date('Y-m-d h:i:s'),
                    'updated_ip' => $request->ip(),
                    'updated_by' => Auth::user()->id
                ];
                $check_own_cmp_cash = Company_cash_management::where('company_id',$request_data['company_id'])->first();
                
                $own_cmp_arr['balance'] = $check_own_cmp_cash['balance'] - $request_data['balance'];
                Company_cash_management::where('company_id',$request_data['company_id'])->update($own_cmp_arr);

        } else {
            
            $cashModel->account_id = $request->input('employee_id');
            $cashModel->account_type = "employee"; 
            $employee_bfr_amnt = Employee_cash_management::where('employee_id',$request_data['employee_id'])->value('balance');
            $cashModel->txn_before_balance = $employee_bfr_amnt; 
            $cashModel->txn_after_balance = $employee_bfr_amnt - $request->input('balance'); 

                //-----------------------
                $own_emp_arr = [
                    'updated_at' => date('Y-m-d h:i:s'),
                    'updated_ip' => $request->ip(),
                    'updated_by' => Auth::user()->id
                ];
                $check_own_emp_cash = Employee_cash_management::where('employee_id',$request_data['employee_id'])->first();
                
                $own_emp_arr['balance'] = $check_own_emp_cash['balance'] - $request_data['balance'];
                Employee_cash_management::where('employee_id',$request_data['employee_id'])->update($own_emp_arr);

        }

        

        if ($request_data['check_btn'] == 'employee_list') {
            $cashModel->project_id  = $request->input('project_id'); 
        }
        $cashModel->balance = $request->input('balance'); 
        $cashModel->transfer_type = "debit"; 
        $cashModel->entry_type = "transfer"; 
        $cashModel->user_id = Auth::user()->id;    
        $cashModel->created_at = date('Y-m-d h:i:s');
        $cashModel->created_ip = $request->ip();
        $cashModel->updated_at = date('Y-m-d h:i:s');
        $cashModel->updated_ip = $request->ip();
        $cashModel->updated_by = Auth::user()->id;

        if ($cashModel->save()) {

        Cash_transfer::where('id',$cashModel->id)->update(['parent_id'=> $cashModel->id]);
        $cashFromModel = new Cash_transfer();

        if ($request_data['check_btn'] == 'employee_list') {
            $cashFromModel->account_id = $request->input('to_employee_id');
            $cashFromModel->account_type = "employee"; 
            $cashFromModel->project_id  = $request->input('project_id'); 
            
            $emp_after_amnt = Employee_cash_management::where('employee_id',$request_data['to_employee_id'])->value('balance');
            $emp_after_amnt_final = !$emp_after_amnt ? 0 : $emp_after_amnt;
            $cashFromModel->txn_before_balance = $emp_after_amnt_final; 
            $cashFromModel->txn_after_balance = $emp_after_amnt_final + $request->input('balance');

    
            $emp_arr = [
                'employee_id' => $request_data['to_employee_id'],
                'balance' => $request_data['balance'],
                'user_id' => Auth::user()->id,
                'created_at' => date('Y-m-d h:i:s'),
                'created_ip' => $request->ip(),
                'updated_at' => date('Y-m-d h:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id
            ];

            $check_emp_cash = Employee_cash_management::where('employee_id',$request_data['to_employee_id'])->first();
            if (empty($check_emp_cash)) {
                Employee_cash_management::insert($emp_arr);
            } else {
                unset($emp_arr['employee_id'],  $emp_arr['balance'], $emp_arr['created_at'] , $emp_arr['created_ip']);
                $emp_arr['balance'] = $check_emp_cash['balance'] + $request_data['balance'];
                Employee_cash_management::where('employee_id',$request_data['to_employee_id'])->update($emp_arr);
            }
                
            

        } else {

            $cashFromModel->account_id = $request->input('to_company_id');
            $cashFromModel->account_type = "company"; 
            $company_after_amnt = Company_cash_management::where('company_id',$request_data['to_company_id'])->value('balance');
           
            $company_after_amnt_final = !$company_after_amnt ? 0 : $company_after_amnt;
            $cashFromModel->txn_before_balance = $company_after_amnt_final; 
            $cashFromModel->txn_after_balance = $company_after_amnt_final + $request->input('balance'); 
        
        
            $cmp_arr = [
                'company_id' => $request->input('to_company_id'),
                'balance' => $request_data['balance'],
                'user_id' => Auth::user()->id,
                'created_at' => date('Y-m-d h:i:s'),
                'created_ip' => $request->ip(),
                'updated_at' => date('Y-m-d h:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id
            ];
            $check_cmp_cash = Company_cash_management::where('company_id',$request_data['to_company_id'])->first();
            if (empty($check_cmp_cash)) {
                Company_cash_management::insert($cmp_arr);
            } else {
                unset($cmp_arr['company_id'],  $cmp_arr['balance'], $cmp_arr['created_at'] , $cmp_arr['created_ip']);
                $cmp_arr['balance'] = $check_cmp_cash['balance'] + $request_data['balance'];
                Company_cash_management::where('company_id',$request_data['to_company_id'])->update($cmp_arr);
            }
            
        }
        $cashFromModel->balance = $request->input('balance'); 
        $cashFromModel->transfer_type = "credit"; 
        $cashFromModel->entry_type = "transfer"; 

        $cashFromModel->user_id = Auth::user()->id; 
        $cashFromModel->parent_id = $cashModel->id;    
        $cashFromModel->created_at = date('Y-m-d h:i:s');
        $cashFromModel->created_ip = $request->ip();
        $cashFromModel->updated_at = date('Y-m-d h:i:s');
        $cashFromModel->updated_ip = $request->ip();
        $cashFromModel->updated_by = Auth::user()->id;

        $cashFromModel->save();
        
            return redirect()->route('admin.cash_transfer_list')->with('success', 'Company cash successfully added.');
        } else {
            return redirect()->route('admin.cash_transfer_list')->with('error', 'Error occurre in insert. Try Again!');
        }

    }*/

    public function index()
    {
        $this->data['page_title'] = "Company Cash Management";
        $company_cash_add_permission = Permissions::checkPermission($this->module_id, 3);
        $company_cash_edit_permission = Permissions::checkPermission($this->module_id, 2);

        if (!Permissions::checkPermission($this->module_id, 5)) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You do not have access to this module.');
        }
        $this->data['company_cash_add_permission'] = $company_cash_add_permission;
        $this->data['company_cash_edit_permission'] = $company_cash_edit_permission;

        return view('admin.company_cash_management.index', $this->data);
    }

    public function get_company_cash_list()
    {
        $datatable_fields = array('company.company_name', 'company_cash_management.balance', 'company_cash_management.updated_at');
        $request = Input::all();
        $conditions_array = [];

        $getfiled = array('company_cash_management.id', 'company_cash_management.company_id', 'company_cash_management.balance', 'company_cash_management.updated_at', 'company.company_name');
        $table = "company_cash_management";

        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'company';
        $join_str[0]['join_table_id'] = 'company_cash_management.company_id';
        $join_str[0]['from_table_id'] = 'company.id';

        echo CompanyCash::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);

        die();
    }

    public function add_company_cash()
    {
        if (!Permissions::checkPermission($this->module_id, 3)) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You do not have access to this module.');
        }

        $this->data['page_title'] = 'Add Company Cash';
        $company_ids = Company_cash_management::pluck('company_id')->toArray();
        $this->data['Companies'] = Companies::select('id', 'company_name')->whereNotIn('id', $company_ids)->orderBy('company_name', 'ASC')->get();
        return view('admin.company_cash_management.add_company_cash', $this->data);
    }

    public function insert_company_cash(Request $request)
    {
        $validator_normal = Validator::make($request->all(), [
            'company_id' => 'required',
            'balance' => 'required'

        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_company_cash')->with('error', 'Please follow validation rules.');
        }
        $request_data = $request->all();
        $cashModel = new Company_cash_management();
        $cashModel->company_id = $request->input('company_id');
        $cashModel->balance = $request->input('balance');
        $cashModel->user_id = Auth::user()->id;
        $cashModel->created_at = date('Y-m-d h:i:s');
        $cashModel->created_ip = $request->ip();
        $cashModel->updated_at = date('Y-m-d h:i:s');
        $cashModel->updated_ip = $request->ip();
        $cashModel->updated_by = Auth::user()->id;
        $cashModel->updated_by = Auth::user()->id;


        if ($cashModel->save()) {

            $cashTransferModel = new Cash_transfer();
            $cashTransferModel->account_id = $request->input('company_id');
            $cashTransferModel->account_type = "company";
            $cashTransferModel->balance = $request->input('balance');
            $cashTransferModel->transfer_type = "credit";
            $company_bfr_amnt = Company_cash_management::where('company_id', $request_data['company_id'])->value('balance');
            $cashTransferModel->txn_before_balance = 0;
            $cashTransferModel->txn_after_balance = $request->input('balance');
            $cashTransferModel->entry_type = "add_new";
            $cashTransferModel->user_id = Auth::user()->id;
            $cashTransferModel->created_at = date('Y-m-d h:i:s');
            $cashTransferModel->created_ip = $request->ip();
            $cashTransferModel->updated_at = date('Y-m-d h:i:s');
            $cashTransferModel->updated_ip = $request->ip();
            $cashTransferModel->updated_by = Auth::user()->id;

            $cashTransferModel->save();

            return redirect()->route('admin.company_cash_management')->with('success', 'Company cash successfully added.');
        } else {
            return redirect()->route('admin.company_cash_management')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function edit_company_cash($id)  //not in use
    {
        if (!Permissions::checkPermission($this->module_id, 2)) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You do not have access to this module.');
        }

        $this->data['page_title'] = 'Upadate Company Cash';
        $this->data['data'] = Company_cash_management::where('id', $id)->get();
        $this->data['Companies'] = Companies::select('id', 'company_name')->orderBy('company_name', 'ASC')->get();
        return view('admin.company_cash_management.edit_company_cash', $this->data);
    }

    public function update_company_cash(Request $request)   //not in use
    {
        $validator_normal = Validator::make($request->all(), [
            'id' => 'required',
            'balance' => 'required'

        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.edit_company_cash')->with('error', 'Please follow validation rules.');
        }
        $request_data = $request->all();
        $exising_balance = Company_cash_management::where('id', $request_data['id'])->get(['company_id', 'balance']);
        $final_balance = $exising_balance[0]->balance + $request_data['balance'];
        $update_arr = [

            'balance' => $final_balance,
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id

        ];

        if (Company_cash_management::where('id', $request_data['id'])->update($update_arr)) {

            $cashTransferModel = new Cash_transfer();
            $cashTransferModel->account_id = $exising_balance[0]->company_id;
            $cashTransferModel->account_type = "company";
            $cashTransferModel->balance = $request_data['balance'];
            $cashTransferModel->transfer_type = "credit";

            $company_bfr_amnt = $exising_balance[0]->balance;
            $cashTransferModel->txn_before_balance = $company_bfr_amnt;
            $cashTransferModel->txn_after_balance = $final_balance;
            $cashTransferModel->entry_type = "add_new";
            $cashTransferModel->user_id = Auth::user()->id;
            $cashTransferModel->created_at = date('Y-m-d h:i:s');
            $cashTransferModel->created_ip = $request->ip();
            $cashTransferModel->updated_at = date('Y-m-d h:i:s');
            $cashTransferModel->updated_ip = $request->ip();
            $cashTransferModel->updated_by = Auth::user()->id;

            $cashTransferModel->save();

            return redirect()->route('admin.company_cash_management')->with('success', 'Company cash successfully updated.');
        } else {
            return redirect()->route('admin.company_cash_management')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    //-----------------------------------------------   cash transfer  -----------------------------------------
    public function add_cash_transfer_company()  //not in use
    {
        if (!Permissions::checkPermission(75, 3)) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You do not have access to this module.');
        }

        $this->data['page_title'] = 'Company to Campany Cash Transfer';

        $this->data['module_title'] = "Cash Transaction";
        $this->data['module_link'] = "admin.cash_transfer_list";

        $this->data['Companies'] = Companies::select('id', 'company_name')->orderBy('company_name', 'ASC')->get();
        $this->data['users_data'] = User::select('id', 'name')->where('status', 'Enabled')->orderBy('name', 'asc')->where('is_user_relieved', 0)->get();

        return view('admin.company_cash_management.add_company_to_company_cash_transaction', $this->data);
    }

    public function insert_cash_transfer(Request $request)  //not in use
    {
        $validator_normal = Validator::make($request->all(), [
            'company_id' => 'required',
            'balance' => 'required'

        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_company_cash')->with('error', 'Please follow validation rules.');
        }
        $request_data = $request->all();

        $cashModel = new Cash_transfer();


        $cashModel->account_id = $request->input('company_id');
        $cashModel->account_type = "company";
        $company_bfr_amnt = Company_cash_management::where('company_id', $request_data['company_id'])->value('balance');
        $cashModel->txn_before_balance = $company_bfr_amnt;
        $cashModel->txn_after_balance = $company_bfr_amnt - $request->input('balance');

        if ($request_data['check_btn'] == 'employee_list') {
            $cashModel->project_id  = $request->input('project_id');
        }
        $cashModel->balance = $request->input('balance');
        $cashModel->transfer_type = "debit";
        $cashModel->entry_type = "transfer";
        $cashModel->user_id = Auth::user()->id;
        $cashModel->created_at = date('Y-m-d h:i:s');
        $cashModel->created_ip = $request->ip();
        $cashModel->updated_at = date('Y-m-d h:i:s');
        $cashModel->updated_ip = $request->ip();
        $cashModel->updated_by = Auth::user()->id;

        if ($cashModel->save()) {

            #-------------------------
            $own_cmp_arr = [
                'updated_at' => date('Y-m-d h:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id
            ];
            $check_own_cmp_cash = Company_cash_management::where('company_id', $request_data['company_id'])->first();

            $own_cmp_arr['balance'] = $check_own_cmp_cash['balance'] - $request_data['balance'];
            Company_cash_management::where('company_id', $request_data['company_id'])->update($own_cmp_arr);

            Cash_transfer::where('id', $cashModel->id)->update(['parent_id' => $cashModel->id]);
            $cashFromModel = new Cash_transfer();

            if ($request_data['check_btn'] == 'employee_list') {
                $cashFromModel->account_id = $request->input('to_employee_id');
                $cashFromModel->account_type = "employee";
                $cashFromModel->project_id  = $request->input('project_id');

                $emp_after_amnt = Employee_cash_management::where('employee_id', $request_data['to_employee_id'])->value('balance');
                $emp_after_amnt_final = !$emp_after_amnt ? 0 : $emp_after_amnt;
                $cashFromModel->txn_before_balance = $emp_after_amnt_final;
                $cashFromModel->txn_after_balance = $emp_after_amnt_final + $request->input('balance');


                $emp_arr = [
                    'employee_id' => $request_data['to_employee_id'],
                    'balance' => $request_data['balance'],
                    'user_id' => Auth::user()->id,
                    'created_at' => date('Y-m-d h:i:s'),
                    'created_ip' => $request->ip(),
                    'updated_at' => date('Y-m-d h:i:s'),
                    'updated_ip' => $request->ip(),
                    'updated_by' => Auth::user()->id
                ];

                $check_emp_cash = Employee_cash_management::where('employee_id', $request_data['to_employee_id'])->first();
                if (empty($check_emp_cash)) {
                    Employee_cash_management::insert($emp_arr);
                } else {
                    unset($emp_arr['employee_id'],  $emp_arr['balance'], $emp_arr['created_at'], $emp_arr['created_ip']);
                    $emp_arr['balance'] = $check_emp_cash['balance'] + $request_data['balance'];
                    Employee_cash_management::where('employee_id', $request_data['to_employee_id'])->update($emp_arr);
                }
            } else {

                $cashFromModel->account_id = $request->input('to_company_id');
                $cashFromModel->account_type = "company";
                $company_after_amnt = Company_cash_management::where('company_id', $request_data['to_company_id'])->value('balance');

                $company_after_amnt_final = !$company_after_amnt ? 0 : $company_after_amnt;
                $cashFromModel->txn_before_balance = $company_after_amnt_final;
                $cashFromModel->txn_after_balance = $company_after_amnt_final + $request->input('balance');


                $cmp_arr = [
                    'company_id' => $request->input('to_company_id'),
                    'balance' => $request_data['balance'],
                    'user_id' => Auth::user()->id,
                    'created_at' => date('Y-m-d h:i:s'),
                    'created_ip' => $request->ip(),
                    'updated_at' => date('Y-m-d h:i:s'),
                    'updated_ip' => $request->ip(),
                    'updated_by' => Auth::user()->id
                ];
                $check_cmp_cash = Company_cash_management::where('company_id', $request_data['to_company_id'])->first();
                if (empty($check_cmp_cash)) {
                    Company_cash_management::insert($cmp_arr);
                } else {
                    unset($cmp_arr['company_id'],  $cmp_arr['balance'], $cmp_arr['created_at'], $cmp_arr['created_ip']);
                    $cmp_arr['balance'] = $check_cmp_cash['balance'] + $request_data['balance'];
                    Company_cash_management::where('company_id', $request_data['to_company_id'])->update($cmp_arr);
                }
            }
            $cashFromModel->balance = $request->input('balance');
            $cashFromModel->transfer_type = "credit";
            $cashFromModel->entry_type = "transfer";

            $cashFromModel->user_id = Auth::user()->id;
            $cashFromModel->parent_id = $cashModel->id;
            $cashFromModel->created_at = date('Y-m-d h:i:s');
            $cashFromModel->created_ip = $request->ip();
            $cashFromModel->updated_at = date('Y-m-d h:i:s');
            $cashFromModel->updated_ip = $request->ip();
            $cashFromModel->updated_by = Auth::user()->id;

            $cashFromModel->save();

            return redirect()->route('admin.cash_transfer_list')->with('success', 'Company cash successfully added.');
        } else {
            return redirect()->route('admin.cash_transfer_list')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    //01/09/2020 nish
    public function company_to_company_cash_transfer()
    {
        if (!Permissions::checkPermission(75, 3)) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You do not have access to this module.');
        }

        $this->data['page_title'] = 'Company to Company Cash Transfer';

        $this->data['module_title'] = "Cash Transaction";
        $this->data['module_link'] = "admin.cash_transfer_list";

        $this->data['Companies'] = Companies::select('id', 'company_name')->orderBy('company_name', 'ASC')->get();
        $this->data['users_data'] = User::select('id', 'name')->where('status', 'Enabled')->orderBy('name', 'asc')->where('is_user_relieved', 0)->get();

        return view('admin.company_cash_management.add_company_to_company_cash_transaction', $this->data);
    }

    //01/09/2020 nish
    public function company_to_employee_cash_transfer()
    {
        if (!Permissions::checkPermission(75, 3)) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You do not have access to this module.');
        }

        $this->data['page_title'] = 'Company to Employee Cash Transfer';

        $this->data['module_title'] = "Cash Transaction";
        $this->data['module_link'] = "admin.cash_transfer_list";

        $this->data['Companies'] = Companies::select('id', 'company_name')->orderBy('company_name', 'ASC')->get();
        $this->data['users_data'] = User::select('id', 'name')->where('status', 'Enabled')->orderBy('name', 'asc')->where('is_user_relieved', 0)->get();

        return view('admin.company_cash_management.add_company_to_employee_cash_transaction', $this->data);
    }

    //01/09/2020 nish
    public function insert_company_to_company_cash_transfer(Request $request)
    {
        $validator_normal = Validator::make($request->all(), [
            'company_id' => 'required',
            'to_company_id' => 'required',
            'balance' => 'required'

        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.company_to_company_cash_transfer')->with('error', 'Please follow validation rules.');
        }
        $request_data = $request->all();

        $cashModel = new Cash_transfer();

        $cashModel->account_id = $request->input('company_id');
        $cashModel->account_type = "company";
        $company_bfr_amnt = Company_cash_management::where('company_id', $request_data['company_id'])->value('balance');
        $cashModel->txn_before_balance = $company_bfr_amnt;
        $cashModel->txn_after_balance = $company_bfr_amnt - $request->input('balance');

        $cashModel->balance = $request->input('balance');
        $cashModel->transfer_type = "debit";
        $cashModel->entry_type = "transfer";
        $cashModel->user_id = Auth::user()->id;
        $cashModel->created_at = date('Y-m-d h:i:s');
        $cashModel->created_ip = $request->ip();
        $cashModel->updated_at = date('Y-m-d h:i:s');
        $cashModel->updated_ip = $request->ip();
        $cashModel->updated_by = Auth::user()->id;

        if ($cashModel->save()) {

            #-------------------------
            $own_cmp_arr = [
                'updated_at' => date('Y-m-d h:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id
            ];
            $check_own_cmp_cash = Company_cash_management::where('company_id', $request_data['company_id'])->first();

            $own_cmp_arr['balance'] = $check_own_cmp_cash['balance'] - $request_data['balance'];
            Company_cash_management::where('company_id', $request_data['company_id'])->update($own_cmp_arr);

            Cash_transfer::where('id', $cashModel->id)->update(['parent_id' => $cashModel->id]);
            $cashFromModel = new Cash_transfer();


            $cashFromModel->account_id = $request->input('to_company_id');
            $cashFromModel->account_type = "company";
            $company_after_amnt = Company_cash_management::where('company_id', $request_data['to_company_id'])->value('balance');

            $company_after_amnt_final = !$company_after_amnt ? 0 : $company_after_amnt;
            $cashFromModel->txn_before_balance = $company_after_amnt_final;
            $cashFromModel->txn_after_balance = $company_after_amnt_final + $request->input('balance');


            $cmp_arr = [
                'company_id' => $request->input('to_company_id'),
                'balance' => $request_data['balance'],
                'user_id' => Auth::user()->id,
                'created_at' => date('Y-m-d h:i:s'),
                'created_ip' => $request->ip(),
                'updated_at' => date('Y-m-d h:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id
            ];
            $check_cmp_cash = Company_cash_management::where('company_id', $request_data['to_company_id'])->first();
            if (empty($check_cmp_cash)) {
                Company_cash_management::insert($cmp_arr);
            } else {
                unset($cmp_arr['company_id'],  $cmp_arr['balance'], $cmp_arr['created_at'], $cmp_arr['created_ip']);
                $cmp_arr['balance'] = $check_cmp_cash['balance'] + $request_data['balance'];
                Company_cash_management::where('company_id', $request_data['to_company_id'])->update($cmp_arr);
            }


            $cashFromModel->balance = $request->input('balance');
            $cashFromModel->transfer_type = "credit";
            $cashFromModel->entry_type = "transfer";

            $cashFromModel->user_id = Auth::user()->id;
            $cashFromModel->parent_id = $cashModel->id;
            $cashFromModel->created_at = date('Y-m-d h:i:s');
            $cashFromModel->created_ip = $request->ip();
            $cashFromModel->updated_at = date('Y-m-d h:i:s');
            $cashFromModel->updated_ip = $request->ip();
            $cashFromModel->updated_by = Auth::user()->id;

            $cashFromModel->save();

            return redirect()->route('admin.cash_transfer_list')->with('success', 'Company cash successfully added.');
        } else {
            return redirect()->route('admin.cash_transfer_list')->with('error', 'Error occurre in insert. Try Again!');
        }
    }
    //01/09/2020 nish
    public function insert_company_to_employee_cash_transfer(Request $request)
    {
        $validator_normal = Validator::make($request->all(), [
            'company_id' => 'required',
            'client_id' => 'required',
            'project_id' => 'required',
            'to_employee_id' => 'required',
            'balance' => 'required'

        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.company_to_employee_cash_transfer')->with('error', 'Please follow validation rules.');
        }
        $request_data = $request->all();

        $cashModel = new Cash_transfer();

        $cashModel->account_id = $request->input('company_id');
        $cashModel->account_type = "company";
        $company_bfr_amnt = Company_cash_management::where('company_id', $request_data['company_id'])->value('balance');
        $cashModel->txn_before_balance = $company_bfr_amnt;
        $cashModel->txn_after_balance = $company_bfr_amnt - $request->input('balance');


        $cashModel->project_id  = $request->input('project_id');

        $cashModel->balance = $request->input('balance');
        $cashModel->transfer_type = "debit";
        $cashModel->entry_type = "transfer";
        $cashModel->user_id = Auth::user()->id;
        $cashModel->created_at = date('Y-m-d h:i:s');
        $cashModel->created_ip = $request->ip();
        $cashModel->updated_at = date('Y-m-d h:i:s');
        $cashModel->updated_ip = $request->ip();
        $cashModel->updated_by = Auth::user()->id;

        if ($cashModel->save()) {

            #-------------------------
            $own_cmp_arr = [
                'updated_at' => date('Y-m-d h:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id
            ];
            $check_own_cmp_cash = Company_cash_management::where('company_id', $request_data['company_id'])->first();

            $own_cmp_arr['balance'] = $check_own_cmp_cash['balance'] - $request_data['balance'];
            Company_cash_management::where('company_id', $request_data['company_id'])->update($own_cmp_arr);

            Cash_transfer::where('id', $cashModel->id)->update(['parent_id' => $cashModel->id]);
            $cashFromModel = new Cash_transfer();

            $cashFromModel->account_id = $request->input('to_employee_id');
            $cashFromModel->account_type = "employee";
            $cashFromModel->project_id  = $request->input('project_id');

            $emp_after_amnt = Employee_cash_management::where('employee_id', $request_data['to_employee_id'])->value('balance');
            $emp_after_amnt_final = !$emp_after_amnt ? 0 : $emp_after_amnt;
            $cashFromModel->txn_before_balance = $emp_after_amnt_final;
            $cashFromModel->txn_after_balance = $emp_after_amnt_final + $request->input('balance');


            $emp_arr = [
                'employee_id' => $request_data['to_employee_id'],
                'balance' => $request_data['balance'],
                'user_id' => Auth::user()->id,
                'created_at' => date('Y-m-d h:i:s'),
                'created_ip' => $request->ip(),
                'updated_at' => date('Y-m-d h:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id
            ];

            $check_emp_cash = Employee_cash_management::where('employee_id', $request_data['to_employee_id'])->first();
            if (empty($check_emp_cash)) {
                Employee_cash_management::insert($emp_arr);
            } else {
                unset($emp_arr['employee_id'],  $emp_arr['balance'], $emp_arr['created_at'], $emp_arr['created_ip']);
                $emp_arr['balance'] = $check_emp_cash['balance'] + $request_data['balance'];
                Employee_cash_management::where('employee_id', $request_data['to_employee_id'])->update($emp_arr);
            }


            $cashFromModel->balance = $request->input('balance');
            $cashFromModel->transfer_type = "credit";
            $cashFromModel->entry_type = "transfer";

            $cashFromModel->user_id = Auth::user()->id;
            $cashFromModel->parent_id = $cashModel->id;
            $cashFromModel->created_at = date('Y-m-d h:i:s');
            $cashFromModel->created_ip = $request->ip();
            $cashFromModel->updated_at = date('Y-m-d h:i:s');
            $cashFromModel->updated_ip = $request->ip();
            $cashFromModel->updated_by = Auth::user()->id;

            $cashFromModel->save();


            // User Action Log
            $company_name = Companies::whereId($request->input('company_id'))->value('company_name');
            $client_name = Clients::whereId($request->input('client_id'))->value('client_name');
            $project_name = Projects::whereId($request->input('project_id'))->value('project_name');
            $user_name = User::whereId($request->input('to_employee_id'))->value('name');
            $add_string = "<br> Company Name: " . $company_name . "<br> Client Name: " . $client_name . "<br> Project Name: " . $project_name . "<br> Employee Name: " . $user_name . "<br>Amount: " . $request->get('balance');
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => "Company to employee cash transfer " . $add_string,
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);



            return redirect()->route('admin.cash_transfer_list')->with('success', 'Company cash successfully added.');
        } else {
            return redirect()->route('admin.cash_transfer_list')->with('error', 'Error occurre in insert. Try Again!');
        }
    }


    public function cash_transfer_list(Request $request)
    {

        $this->data['page_title']  = 'Cash Transaction';
        $company_cash_transfer_permission = Permissions::checkPermission(75, 3);
        $employee_cash_transfer__permission = Permissions::checkPermission(76, 3);

        $this->data['company_cash_transfer_permission'] = $company_cash_transfer_permission;
        $this->data['employee_cash_transfer__permission'] = $employee_cash_transfer__permission;
        $this->data['companies']       = Companies::orderBy('company_name', 'asc')->pluck('company_name', 'id');
        $this->data['employeess'] = User::select('id', 'name')->where('status', 'Enabled')->orderBy('name', 'asc')->pluck('name', 'id');
        $this->data['project_namee'] = Projects::select('id', 'project_name')->orderBy('project_name', 'asc')->pluck('project_name', 'id');
        $this->data['transfer_typee'] = Cash_transfer::select('id', 'transfer_type')->pluck('transfer_type', 'id');

        // we are here focus here

        $this->data['company_id'] = "";
        $this->data['employee_id'] = "";
        $this->data['project_id'] = "";
        $this->data['transfer_type'] = "";
        $this->data['transfer_date'] = "";

        if ($request->method() == 'POST') {
            // dd($request->all());
            $this->data['company_id'] = $request->get('company_id');
            $this->data['employee_id'] = $request->get('employee_id');
            $this->data['project_id'] = $request->get('project_id');
            $this->data['transfer_type'] = $request->get('transfer_type');
            $this->data['transfer_date'] = $request->get('transfer_date');
            $get_fields = ['cash_transfer.*', 'project.project_name', 'users.name as user_name'];
            $transfer_query = Cash_transfer::leftjoin('project', 'cash_transfer.project_id', '=', 'project.id')
                ->join('users', 'cash_transfer.user_id', '=', 'users.id');

            if ($request->get('company_id') != "") {
                $transfer_query->orWhere('cash_transfer.account_id', $request->get('company_id'));
            }
            if ($request->get('employee_id') != "") {
                $transfer_query->orWhere('cash_transfer.account_id', $request->get('employee_id'));
            }
            if ($request->get('project_id') != "") {
                $transfer_query->where('cash_transfer.project_id', $request->get('project_id'));
            }
            if ($request->get('transfer_type') != "") {
                $transfer_query->where('transfer_type', $request->get('transfer_type'));
            }
            if ($request->get('transfer_date') != "") {
                $date = $request->get('transfer_date');
                $mainDate = explode("-", $date);
                $strFirstdate = str_replace("/", "-", $mainDate[0]);
                $strLastdate = str_replace("/", "-", $mainDate[1]);
                $first_date = date('Y-m-d', strtotime($strFirstdate));
                $second_date = date('Y-m-d', strtotime($strLastdate));
                $transfer_query->whereBetween('cash_transfer.created_at', [$first_date, $second_date]);
            }

            $transfer_query->orderBy('cash_transfer.id', 'DESC');
            $transfer_data = $transfer_query->get($get_fields)->toArray();

            foreach ($transfer_data as $key => $value) {
                if ($value['account_type'] == "company") {
                    $transfer_data[$key]['accountant_name'] = DB::table('company')->where('id', $value['account_id'])->value('company_name');
                } else {
                    $transfer_data[$key]['accountant_name'] = DB::table('users')->where('id', $value['account_id'])->value('name');
                }
            }
        } else {
            $get_fields = ['cash_transfer.*', 'project.project_name', 'users.name as user_name'];
            $transfer_data = Cash_transfer::leftjoin('project', 'cash_transfer.project_id', '=', 'project.id')
                ->join('users', 'cash_transfer.user_id', '=', 'users.id')
                ->orderBy('cash_transfer.id', 'DESC')
                ->get($get_fields)->toArray();

            foreach ($transfer_data as $key => $value) {
                if ($value['account_type'] == "company") {
                    $transfer_data[$key]['accountant_name'] = DB::table('company')->where('id', $value['account_id'])->value('company_name');
                } else {
                    $transfer_data[$key]['accountant_name'] = DB::table('users')->where('id', $value['account_id'])->value('name');
                }
            }
        }




        $this->data['records']  = $transfer_data;
        //dd($this->data['records']);

        return view('admin.company_cash_management.cash_transfer_list', $this->data);
    }

    public function add_employee_cash_transfer()
    {
        // if (!Permissions::checkPermission(76, 3)) {
        //     return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You do not have access to this module.');
        // }

        $this->data['page_title'] = 'Employee Cash Transfer';

        // $this->data['module_title'] = "Cash Transfer";
        $this->data['module_link'] = "admin.cash_transfer_list";
        $this->data['emp_balance'] = Employee_cash_management::where('employee_id', Auth::user()->id)->value('balance');
        $this->data['Companies'] = Companies::select('id', 'company_name')->orderBy('company_name', 'ASC')->get();
        $this->data['users_data'] = User::select('id', 'name')->where('status', 'Enabled')->orderBy('name', 'asc')->where('is_user_relieved', 0)->get();

        return view('admin.company_cash_management.add_employee_cash_transaction', $this->data);
    }

    public function insert_emplyee_cash_transfer(Request $request)
    {
        $validator_normal = Validator::make($request->all(), [
            'balance' => 'required',
            'txn_note' => 'required'

        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_employee_cash_transfer')->with('error', 'Please follow validation rules.');
        }
        $request_data = $request->all();



        $cashModel = new Cash_transfer();
        $cashModel->account_id = Auth::user()->id;
        $cashModel->account_type = "employee";
        $cashModel->txn_note = $request->input('txn_note');
        if ($request_data['check_btn'] == 'employee_list') {
            $cashModel->project_id  = $request->input('project_id');
        }
        $cashModel->balance = $request->input('balance');
        $cashModel->transfer_type = "debit";
        $employee_bfr_amnt = Employee_cash_management::where('employee_id', Auth::user()->id)->value('balance');
        $cashModel->txn_before_balance = $employee_bfr_amnt;
        $cashModel->txn_after_balance = $employee_bfr_amnt - $request->input('balance');
        $cashModel->entry_type = "transfer";
        $cashModel->user_id = Auth::user()->id;
        $cashModel->created_at = date('Y-m-d h:i:s');
        $cashModel->created_ip = $request->ip();
        $cashModel->updated_at = date('Y-m-d h:i:s');
        $cashModel->updated_ip = $request->ip();
        $cashModel->updated_by = Auth::user()->id;

        if ($cashModel->save()) {

            #--------------------
            $own_emp_arr = [
                'updated_at' => date('Y-m-d h:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id
            ];
            $check_own_emp_cash = Employee_cash_management::where('employee_id', Auth::user()->id)->first();

            $own_emp_arr['balance'] = $check_own_emp_cash['balance'] - $request_data['balance'];
            Employee_cash_management::where('employee_id', Auth::user()->id)->update($own_emp_arr);


            Cash_transfer::where('id', $cashModel->id)->update(['parent_id' => $cashModel->id]);
            $cashFromModel = new Cash_transfer();

            if ($request_data['check_btn'] == 'employee_list') {
                $cashFromModel->account_id = $request->input('to_employee_id');
                $cashFromModel->account_type = "employee";
                $cashFromModel->project_id  = $request->input('project_id');

                $emp_after_amnt = Employee_cash_management::where('employee_id', $request_data['to_employee_id'])->value('balance');
                $emp_after_amnt_final = !$emp_after_amnt ? 0 : $emp_after_amnt;
                $cashFromModel->txn_before_balance = $emp_after_amnt_final;
                $cashFromModel->txn_after_balance = $emp_after_amnt_final + $request->input('balance');


                $emp_arr = [
                    'employee_id' => $request_data['to_employee_id'],
                    'balance' => $request_data['balance'],
                    'user_id' => Auth::user()->id,
                    'created_at' => date('Y-m-d h:i:s'),
                    'created_ip' => $request->ip(),
                    'updated_at' => date('Y-m-d h:i:s'),
                    'updated_ip' => $request->ip(),
                    'updated_by' => Auth::user()->id
                ];

                $check_emp_cash = Employee_cash_management::where('employee_id', $request_data['to_employee_id'])->first();
                if (empty($check_emp_cash)) {
                    Employee_cash_management::insert($emp_arr);
                } else {
                    unset($emp_arr['employee_id'],  $emp_arr['balance'], $emp_arr['created_at'], $emp_arr['created_ip']);
                    $emp_arr['balance'] = $check_emp_cash['balance'] + $request_data['balance'];
                    Employee_cash_management::where('employee_id', $request_data['to_employee_id'])->update($emp_arr);
                }
            } else {

                $cashFromModel->account_id = $request->input('to_company_id');
                $cashFromModel->account_type = "company";
                $company_after_amnt = Company_cash_management::where('company_id', $request_data['to_company_id'])->value('balance');

                $company_after_amnt_final = !$company_after_amnt ? 0 : $company_after_amnt;
                $cashFromModel->txn_before_balance = $company_after_amnt_final;
                $cashFromModel->txn_after_balance = $company_after_amnt_final + $request->input('balance');


                $cmp_arr = [
                    'company_id' => $request->input('to_company_id'),
                    'balance' => $request_data['balance'],
                    'user_id' => Auth::user()->id,
                    'created_at' => date('Y-m-d h:i:s'),
                    'created_ip' => $request->ip(),
                    'updated_at' => date('Y-m-d h:i:s'),
                    'updated_ip' => $request->ip(),
                    'updated_by' => Auth::user()->id
                ];
                $check_cmp_cash = Company_cash_management::where('company_id', $request_data['to_company_id'])->first();
                if (empty($check_cmp_cash)) {
                    Company_cash_management::insert($cmp_arr);
                } else {
                    unset($cmp_arr['company_id'],  $cmp_arr['balance'], $cmp_arr['created_at'], $cmp_arr['created_ip']);
                    $cmp_arr['balance'] = $check_cmp_cash['balance'] + $request_data['balance'];
                    Company_cash_management::where('company_id', $request_data['to_company_id'])->update($cmp_arr);
                }
            }
            $cashFromModel->balance = $request->input('balance');
            $cashFromModel->transfer_type = "credit";
            $cashFromModel->entry_type = "transfer";
            $cashFromModel->txn_note = $request->input('txn_note');

            $cashFromModel->user_id = Auth::user()->id;
            $cashFromModel->parent_id = $cashModel->id;
            $cashFromModel->created_at = date('Y-m-d h:i:s');
            $cashFromModel->created_ip = $request->ip();
            $cashFromModel->updated_at = date('Y-m-d h:i:s');
            $cashFromModel->updated_ip = $request->ip();
            $cashFromModel->updated_by = Auth::user()->id;

            $cashFromModel->save();

            if ($request_data['check_btn'] == "company_list") {

                $company_name = Companies::whereId($request->input('company_id'))->value('company_name');
                $add_string = "<br> Company Name: " . $company_name . "<br>Amount: " . $request->get('balance');
                $action_data = [
                    'user_id' => Auth::user()->id,
                    'task_body' => "Employee to company cash transfer " . $add_string,
                    'created_ip' => $request->ip(),
                ];
            } else {
                // employee_list ;
                $company_name = Companies::whereId($request->input('company_id'))->value('company_name');
                $client_name = Clients::whereId($request->input('company_id'))->value('client_name');
                $project_name = Projects::whereId($request->input('project_id'))->value('project_name');
                $user_name = User::whereId($request->input('to_employee_id'))->value('name');
                $add_string = "<br> Company Name: " . $company_name . "<br> Client Name: " . $client_name . "<br> Project Name: " . $project_name . "<br> Employee Name: " . $user_name . "<br>Amount: " . $request->get('balance');
                $action_data = [
                    'user_id' => Auth::user()->id,
                    'task_body' => "Employee to employee cash transfer " . $add_string,
                    'created_ip' => $request->ip(),
                ];
            }
            $this->user_action_logs->action($action_data);

            return redirect()->route('admin.dashboard')->with('success', 'Cash Transfer successfully.');
        } else {
            return redirect()->route('admin.dashboard')->with('error', 'Error occurre in insert. Try Again!');
        }
    }
}
