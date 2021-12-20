<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Lib\CommonTask;
use App\Job_opening;
use App\Job_opening_consultant;
use App\Interview;
use App\User;
use App\InterviewResult;
use App\Department;
use App\Email_format;
use App\Mail\Mails;
use Exception;
use App\Recruitment_consultant;
use DB;
use Illuminate\Support\Facades\Mail;
use Auth;
use App\Lib\Permissions;
use App\Expense_category;
use App\Role_module;

class ExpenseCategoryController extends Controller
{
    public $common_task;
    public function __construct() {
        $this->data['module_title']="Expense Category";
        $this->data['module_link']="admin.expense_category";
        $this->common_task = new CommonTask();
    }
    
    public function index() {
        $this->data['page_title']     = "Expense Category";
        $this->data['expense_category_list'] = DB::table('expense_category')->select('expense_category.*')->get();
        $check_result=Permissions::checkPermission(16,5);
        if(!$check_result){
            return redirect()->route('admin.dashboard')->with('error','Access Denied. You are not authorized to access that page.');
        }
        
        $access_level              = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => 16])->get()->first();
        $this->data['access_rule'] = '';
        if(!empty($access_level))
        {
            $this->data['access_rule'] = $access_level->access_level;
        }

        return view('admin.expense_category.index', $this->data);
    }
    public function add_expense_category()
    {
        $this->data['page_title']="Add ExpenseCategory Details";
        $check_result=Permissions::checkPermission(16,3);
        if(!$check_result){
            return redirect()->route('admin.dashboard')->with('error','Access Denied. You are not authorized to access that page.');
        }
        return view('admin.expense_category.add_expense_category', $this->data);
    }
    public function insert_expense_category(Request $request) {
        $validator_normal = Validator::make($request->all(), [
            'category_name' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.expense_category')->with('error', 'Please follow validation rules.');
        }

        $expense_categoryModel = new Expense_category();
        $expense_categoryModel->category_name       = $request->input('category_name');
        $expense_categoryModel->created_at          = date('Y-m-d h:i:s');
        $expense_categoryModel->created_ip          = $request->ip();
        $expense_categoryModel->updated_at          = date('Y-m-d h:i:s');
        $expense_categoryModel->updated_ip          = $request->ip();
        $expense_categoryModel->updated_by          = Auth::user()->id;
        if ($expense_categoryModel->save()) {
            return redirect()->route('admin.expense_category')->with('success', 'Expense Category details added successfully.');
        } else {
            return redirect()->route('admin.expense_category')->with('error', 'Error occurre in insert. Try Again!');
        }
    }
    public function edit_expense_category($id) {
        $this->data['page_title']="Edit ExpenseCategory Details";
        $this->data['expense_category_list'] = DB::table('expense_category')
                                                ->select('expense_category.*')
                                                ->where('expense_category.id',$id)
                                                ->get();
        $check_result=Permissions::checkPermission(16,2);
        if(!$check_result){
            return redirect()->route('admin.dashboard')->with('error','Access Denied. You are not authorized to access that page.');
        }

        if ($this->data['expense_category_list']->count() == 0) {
            return redirect()->route('admin.expense_category')->with('error', 'Error Occurred. Try Again!');
        }

       return view('admin.expense_category.edit_expense_category', $this->data);
    }
    public function update_expense_category(Request $request) {
        $validator_normal = Validator::make($request->all(), [
            'category_name' => 'required',
        ]);

        if ($validator_normal->fails()) {
            return redirect()->route('admin.expense_category')->with('error', 'Please follow validation rules.');
        } 
        $Expense_category_id  = $request->input('id'); 
        $Expense_category_arr = [
            'category_name' => $request->input('category_name'),
            'created_at'    => date('Y-m-d h:i:s'),
            'created_ip'    => $request->ip(),
            'updated_at'    => date('Y-m-d h:i:s'),
            'updated_ip'    => $request->ip(),
            'updated_by'    => Auth::user()->id,
        ];
        Expense_category::where('id', $Expense_category_id)->update($Expense_category_arr);
        return redirect()->route('admin.expense_category')->with('success', 'Expense Category details successfully updated.');
    }
    public function change_expense_category($id, $status) {
        $check_result=Permissions::checkPermission(16,2);
        
        if(!$check_result){
            return redirect()->route('admin.dashboard')->with('error','Access Denied. You are not authorized to access that page.');
        }
        if (Expense_category::where('id', $id)->update(['status' => $status])) {
            return redirect()->route('admin.expense_category')->with('success', 'Status successfully updated.');
        }
        return redirect()->route('admin.expense_category')->with('error', 'Error during operation. Try again!');
    }
}
