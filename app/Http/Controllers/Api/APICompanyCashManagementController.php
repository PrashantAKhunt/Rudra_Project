<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Lib\NotificationTask;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

use App\Company_cash_management;
use App\Employee_cash_management;
use App\User;
use App\Companies;
use App\Cash_transfer;

use Exception;
use App\Role_module;
use App\Email_format;
use Illuminate\Support\Facades\Mail;
use App\Mail\Mails;

class APICompanyCashManagementController extends Controller
{

    public $data;
    private $notification_task;

    public function __construct()
    {
        $this->super_admin = \App\User::where('role', config('constants.SuperUser'))->first();
    }

    public function api_company_to_company_cash_transfer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'company_id' => 'required',
            'to_company_id' => 'required',
            'balance' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $loggedin_user_data = User::where('id', $request_data['user_id'])->get(['users.id', 'users.name', 'users.role']);

        //Business Logic
        $cashModel = new Cash_transfer();

        $cashModel->account_id = $request_data['company_id'];
        $cashModel->account_type = "company"; 
        $company_bfr_amnt = Company_cash_management::where('company_id',$request_data['company_id'])->value('balance');
        $cashModel->txn_before_balance = $company_bfr_amnt; 
        $cashModel->txn_after_balance = $company_bfr_amnt - $request_data['balance'];

        $cashModel->balance = $request_data['balance']; 
        $cashModel->transfer_type = "debit"; 
        $cashModel->entry_type = "transfer"; 
        $cashModel->user_id = $request_data['user_id'];    
        $cashModel->created_at = date('Y-m-d h:i:s');
        $cashModel->created_ip = $request->ip();
        $cashModel->updated_at = date('Y-m-d h:i:s');
        $cashModel->updated_ip = $request->ip();
        $cashModel->updated_by = $request_data['user_id'];

        if ($cashModel->save()) {

            #-------------------------
            $own_cmp_arr = [
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => $request_data['user_id']
        ];
        $check_own_cmp_cash = Company_cash_management::where('company_id',$request_data['company_id'])->first();
        
        $own_cmp_arr['balance'] = $check_own_cmp_cash['balance'] - $request_data['balance'];
        Company_cash_management::where('company_id',$request_data['company_id'])->update($own_cmp_arr);

        Cash_transfer::where('id',$cashModel->id)->update(['parent_id'=> $cashModel->id]);
        $cashFromModel = new Cash_transfer();


            $cashFromModel->account_id = $request_data['to_company_id'];
            $cashFromModel->account_type = "company"; 
            $company_after_amnt = Company_cash_management::where('company_id',$request_data['to_company_id'])->value('balance');
           
            $company_after_amnt_final = !$company_after_amnt ? 0 : $company_after_amnt;
            $cashFromModel->txn_before_balance = $company_after_amnt_final; 
            $cashFromModel->txn_after_balance = $company_after_amnt_final + $request_data['balance']; 
        
        
            $cmp_arr = [
                'company_id' => $request_data['to_company_id'],
                'balance' => $request_data['balance'],
                'user_id' => $request_data['user_id'],
                'created_at' => date('Y-m-d h:i:s'),
                'created_ip' => $request->ip(),
                'updated_at' => date('Y-m-d h:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => $request_data['user_id']
            ];
            $check_cmp_cash = Company_cash_management::where('company_id',$request_data['to_company_id'])->first();
            if (empty($check_cmp_cash)) {
                Company_cash_management::insert($cmp_arr);
            } else {
                unset($cmp_arr['company_id'],  $cmp_arr['balance'], $cmp_arr['created_at'] , $cmp_arr['created_ip']);
                $cmp_arr['balance'] = $check_cmp_cash['balance'] + $request_data['balance'];
                Company_cash_management::where('company_id',$request_data['to_company_id'])->update($cmp_arr);
            }
            
        
        $cashFromModel->balance = $request_data['balance']; 
        $cashFromModel->transfer_type = "credit"; 
        $cashFromModel->entry_type = "transfer"; 

        $cashFromModel->user_id = $request_data['user_id']; 
        $cashFromModel->parent_id = $cashModel->id;    
        $cashFromModel->created_at = date('Y-m-d h:i:s');
        $cashFromModel->created_ip = $request->ip();
        $cashFromModel->updated_at = date('Y-m-d h:i:s');
        $cashFromModel->updated_ip = $request->ip();
        $cashFromModel->updated_by = $request_data['user_id'];

        $cashFromModel->save();
        
            return response()->json(['status' => true, 'msg' => "Cash successfully transfer to company.", 'data' => []]);
        } else {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }
    }


    public function api_company_to_employee_cash_transfer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'company_id' => 'required',
            'client_id' => 'required',
            'project_id' => 'required',
            'to_employee_id' => 'required',
            'balance' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $loggedin_user_data = User::where('id', $request_data['user_id'])->get(['users.id', 'users.name', 'users.role']);

        //Business Logic
        $cashModel = new Cash_transfer();

        $cashModel->account_id = $request_data['company_id'];
        $cashModel->account_type = "company"; 
        $company_bfr_amnt = Company_cash_management::where('company_id',$request_data['company_id'])->value('balance');
        $cashModel->txn_before_balance = $company_bfr_amnt; 
        $cashModel->txn_after_balance = $company_bfr_amnt - $request_data['balance'];

     
        $cashModel->project_id  = $request_data['project_id']; 
        
        $cashModel->balance = $request_data['balance']; 
        $cashModel->transfer_type = "debit"; 
        $cashModel->entry_type = "transfer"; 
        $cashModel->user_id = $request_data['user_id'];    
        $cashModel->created_at = date('Y-m-d h:i:s');
        $cashModel->created_ip = $request->ip();
        $cashModel->updated_at = date('Y-m-d h:i:s');
        $cashModel->updated_ip = $request->ip();
        $cashModel->updated_by = $request_data['user_id'];

        if ($cashModel->save()) {

            #-------------------------
            $own_cmp_arr = [
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => $request_data['user_id']
        ];
        $check_own_cmp_cash = Company_cash_management::where('company_id',$request_data['company_id'])->first();
        
        $own_cmp_arr['balance'] = $check_own_cmp_cash['balance'] - $request_data['balance'];
        Company_cash_management::where('company_id',$request_data['company_id'])->update($own_cmp_arr);

        Cash_transfer::where('id',$cashModel->id)->update(['parent_id'=> $cashModel->id]);
        $cashFromModel = new Cash_transfer();

            $cashFromModel->account_id = $request_data['to_employee_id'];
            $cashFromModel->account_type = "employee"; 
            $cashFromModel->project_id  = $request_data['project_id']; 
            
            $emp_after_amnt = Employee_cash_management::where('employee_id',$request_data['to_employee_id'])->value('balance');
            $emp_after_amnt_final = !$emp_after_amnt ? 0 : $emp_after_amnt;
            $cashFromModel->txn_before_balance = $emp_after_amnt_final; 
            $cashFromModel->txn_after_balance = $emp_after_amnt_final + $request_data['balance'];

    
            $emp_arr = [
                'employee_id' => $request_data['to_employee_id'],
                'balance' => $request_data['balance'],
                'user_id' => $request_data['user_id'],
                'created_at' => date('Y-m-d h:i:s'),
                'created_ip' => $request->ip(),
                'updated_at' => date('Y-m-d h:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => $request_data['user_id']
            ];

            $check_emp_cash = Employee_cash_management::where('employee_id',$request_data['to_employee_id'])->first();
            if (empty($check_emp_cash)) {
                Employee_cash_management::insert($emp_arr);
            } else {
                unset($emp_arr['employee_id'],  $emp_arr['balance'], $emp_arr['created_at'] , $emp_arr['created_ip']);
                $emp_arr['balance'] = $check_emp_cash['balance'] + $request_data['balance'];
                Employee_cash_management::where('employee_id',$request_data['to_employee_id'])->update($emp_arr);
            }
                
            
        $cashFromModel->balance = $request_data['balance']; 
        $cashFromModel->transfer_type = "credit"; 
        $cashFromModel->entry_type = "transfer"; 

        $cashFromModel->user_id = $request_data['user_id']; 
        $cashFromModel->parent_id = $cashModel->id;    
        $cashFromModel->created_at = date('Y-m-d h:i:s');
        $cashFromModel->created_ip = $request->ip();
        $cashFromModel->updated_at = date('Y-m-d h:i:s');
        $cashFromModel->updated_ip = $request->ip();
        $cashFromModel->updated_by = $request_data['user_id'];

        $cashFromModel->save();
        
            return response()->json(['status' => true, 'msg' => "Cash successfully transfer to employee.", 'data' => []]);
        } else {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }

    }


    public function api_employee_cash_transfer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'balance' => 'required',
            'txn_note' => 'required',
            'check_btn' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $loggedin_user_data = User::where('id', $request_data['user_id'])->get(['users.id', 'users.name', 'users.role']);

        //Business Logic
        $cashModel = new Cash_transfer();
        $cashModel->account_id = $request_data['user_id'];
        $cashModel->account_type = "employee"; 
        $cashModel->txn_note = $request_data['txn_note']; 
        if ($request_data['check_btn'] == 'employee_list') {
            $cashModel->project_id  = $request_data['project_id']; 
        }
        $cashModel->balance = $request_data['balance']; 
        $cashModel->transfer_type = "debit"; 
        $employee_bfr_amnt = Employee_cash_management::where('employee_id',$request_data['user_id'])->value('balance');
        $cashModel->txn_before_balance = $employee_bfr_amnt; 
        $cashModel->txn_after_balance = $employee_bfr_amnt - $request->input('balance'); 
        $cashModel->entry_type = "transfer"; 
        $cashModel->user_id = $request_data['user_id'];    
        $cashModel->created_at = date('Y-m-d h:i:s');
        $cashModel->created_ip = $request->ip();
        $cashModel->updated_at = date('Y-m-d h:i:s');
        $cashModel->updated_ip = $request->ip();
        $cashModel->updated_by = $request_data['user_id'];

        if ($cashModel->save()) {

            #--------------------
            $own_emp_arr = [
                'updated_at' => date('Y-m-d h:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => $request_data['user_id']
            ];
            $check_own_emp_cash = Employee_cash_management::where('employee_id',$request_data['user_id'])->first();
            
            $own_emp_arr['balance'] = $check_own_emp_cash['balance'] - $request_data['balance'];
            Employee_cash_management::where('employee_id',$request_data['user_id'])->update($own_emp_arr);
            
        
        Cash_transfer::where('id',$cashModel->id)->update(['parent_id'=> $cashModel->id]);
        $cashFromModel = new Cash_transfer();

        if ($request_data['check_btn'] == 'employee_list') {
            $cashFromModel->account_id = $request_data['to_employee_id'];
            $cashFromModel->account_type = "employee"; 
            $cashFromModel->project_id  = $request_data['project_id']; 
            
            $emp_after_amnt = Employee_cash_management::where('employee_id',$request_data['to_employee_id'])->value('balance');
            $emp_after_amnt_final = !$emp_after_amnt ? 0 : $emp_after_amnt;
            $cashFromModel->txn_before_balance = $emp_after_amnt_final; 
            $cashFromModel->txn_after_balance = $emp_after_amnt_final + $request_data['balance'];

    
            $emp_arr = [
                'employee_id' => $request_data['to_employee_id'],
                'balance' => $request_data['balance'],
                'user_id' => $request_data['user_id'],
                'created_at' => date('Y-m-d h:i:s'),
                'created_ip' => $request->ip(),
                'updated_at' => date('Y-m-d h:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => $request_data['user_id']
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

            $cashFromModel->account_id = $request_data['to_company_id'];
            $cashFromModel->account_type = "company"; 
            $company_after_amnt = Company_cash_management::where('company_id',$request_data['to_company_id'])->value('balance');
           
            $company_after_amnt_final = !$company_after_amnt ? 0 : $company_after_amnt;
            $cashFromModel->txn_before_balance = $company_after_amnt_final; 
            $cashFromModel->txn_after_balance = $company_after_amnt_final + $request_data['balance']; 
        
        
            $cmp_arr = [
                'company_id' => $request_data['to_company_id'],
                'balance' => $request_data['balance'],
                'user_id' => $request_data['user_id'],
                'created_at' => date('Y-m-d h:i:s'),
                'created_ip' => $request->ip(),
                'updated_at' => date('Y-m-d h:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => $request_data['user_id']
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
        $cashFromModel->balance = $request_data['balance']; 
        $cashFromModel->transfer_type = "credit"; 
        $cashFromModel->entry_type = "transfer"; 
        $cashFromModel->txn_note = $request_data['txn_note']; 

        $cashFromModel->user_id = $request_data['user_id']; 
        $cashFromModel->parent_id = $cashModel->id;    
        $cashFromModel->created_at = date('Y-m-d h:i:s');
        $cashFromModel->created_ip = $request->ip();
        $cashFromModel->updated_at = date('Y-m-d h:i:s');
        $cashFromModel->updated_ip = $request->ip();
        $cashFromModel->updated_by = $request_data['user_id'];

        $cashFromModel->save();
        
        return response()->json(['status' => true, 'msg' => "Employee cash successfully transfer.", 'data' => []]);

        } else {
            return response()->json(['status' => false, 'msg' => config('errors.permission_error.msg'), 'data' => [], 'error' => config('errors.permission_error.code')]);
        }
    }

    public function api_employee_current_balance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $emp_balance = Employee_cash_management::where('employee_id',$request_data['user_id'])->value('balance');

        return response()->json(['status' => true, 'msg' => 'Record found', 'data' => (float)$emp_balance]);
    }

}