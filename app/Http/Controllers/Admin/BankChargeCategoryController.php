<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Config;
use App\Common_query;
use App\User;
use App\Bank_charge_category;
use App\Lib\NotificationTask;
use App\Lib\Permissions;
use Illuminate\Support\Facades\Validator;

class BankChargeCategoryController extends Controller {
    public $data;
    public $notification_task;

    public function __construct() {

        $this->notification_task = new NotificationTask();
       $this->data['module_title']="Bank Charge Category";
        $this->data['module_link']='admin.bank_charge_category';
        $this->module_id = 64;
    }

    public function index() {
        $this->data['page_title'] = 'Bank Charge Category';
        $this->data['view_special_permission'] = Permissions::checkSpecialPermission($this->module_id);
        return view( 'admin.bank_charge_category.index', $this->data );
    }

    public function get_bank_charge_table_list(){
        $datatable_fields = array('id','title','detail','created_at');
        $request = Input::all();
        $conditions_array = ['is_approved' => 1];

        $getfiled =array('id','title','detail','created_at');
        $table = "bank_charge_category";

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, []);
        die();
    }

    public function add_bank_charge_category(){
        $view_special_permission = Permissions::checkSpecialPermission($this->module_id);
        if(!$view_special_permission){
            return redirect()->route('admin.dashboard')->with('error','Access denied. You do not have permission to access this module.');
        }
        $this->data['page_title'] = 'Add Bank Charge Category';
        return view( 'admin.bank_charge_category.add_bank_charge_category', $this->data );
    }

    public function save_bank_charge(Request $request){

        $charge_arr = [
            'user_id' => Auth::user()->id,
            'title' => $request->get('title'),
            'detail' => $request->get('detail'),
            //'is_approved' => 0,
            'created_ip' => $request->ip(),
            'updated_ip' => $request->ip(),
        ];
        if (Auth::user()->role != config('constants.SuperUser')) {
            $charge_arr['is_approved'] = 0;
        } else {
            $charge_arr['is_approved'] = 1;
        }
        if($request->get('id')){
            unset($charge_arr['created_ip']);
            unset($charge_arr['is_approved']);
            if(Bank_charge_category::whereId($request->get('id'))->update($charge_arr)){
                return redirect()->route('admin.bank_charge_category')->with('success', 'Data successfully updated.');
            }
        }else{
            if(Bank_charge_category::insert($charge_arr)){
                $module = 'Bank Charge Category';
                $this->notification_task->entryApprovalNotify($module);
                return redirect()->route('admin.bank_charge_category')->with('success', 'Data successfully inserted.');
            }
        }
        return redirect()->route('admin.bank_charge_category')->with('error', 'Error during operation. Try again!');
    }

    public function edit_bank_charge_category($id){
        $view_special_permission = Permissions::checkSpecialPermission($this->module_id);
        if(!$view_special_permission){
            return redirect()->route('admin.dashboard')->with('error','Access denied. You do not have permission to access this module.');
        }
        $this->data['page_title'] = 'Edit Bank Charge Category';
        $this->data['bank_charge'] = Bank_charge_category::whereId($id)->first();
        return view( 'admin.bank_charge_category.edit_bank_charge_category', $this->data );
    }
}

