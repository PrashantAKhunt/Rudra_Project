<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\TenderCategory;
use Illuminate\Support\Facades\Input;
use App\Common_query;
use App\Lib\Permissions;
use App\Lib\NotificationTask;

class TenderCategoryController extends Controller
{
    public $data;
    private $notification_task;


    public function __construct() {
        $this->notification_task = new NotificationTask();
        $this->data['module_title']="Tender Category";
        $this->data['module_link']='admin.tender_category';
        $this->module_id = 57;
    }
    
    public function index(){
        $this->data['page_title']='Tender Category';
        $this->data['view_special_permission'] = Permissions::checkSpecialPermission($this->module_id);
        return view('admin.tender_category.index', $this->data);
    }

    public function get_tender_category_list() {
        $datatable_fields = array('id','tender_category', 'tender_category_detail','created_at','status');
        $request = Input::all();
        $conditions_array = ['is_approved' => 1];

        $getfiled =array('id','tender_category', 'tender_category_detail','created_at','status');
        $table = "tender_category";

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, []);
        die();
    }
    
    public function change_tender_category_status($id, $status) {
        /*$permission= Permissions::checkPermission($this->module_id,2);
        if(!$permission){
            return redirect()->route('admin.dashboard')->with('error','Access denied. You do not have permission to access this module.');
        }*/
        if (TenderCategory::where('id', $id)->update(['status' => $status])) {
            return redirect()->route('admin.tender_category')->with('success', 'Status successfully updated.');
        }
        return redirect()->route('admin.tender_category')->with('error', 'Error during operation. Try again!');
    }

    public function delete_tender_category($id) {
        // $permission= Permissions::checkPermission($this->module_id,4);
        // if(!$permission){
        //     return redirect()->route('admin.dashboard')->with('error','Access denied. You do not have permission to access this module.');
        // }
        if (TenderCategory::where('id', $id)->delete()) {
            return redirect()->route('admin.tender_category')->with('success', 'Delete successfully updated.');
        }
        return redirect()->route('admin.tender_category')->with('error', 'Error during operation. Try again!');
    }

    public function add_tender_category(){
        $view_special_permission = Permissions::checkSpecialPermission($this->module_id);
        if(!$view_special_permission){
            return redirect()->route('admin.dashboard')->with('error','Access denied. You do not have permission to access this module.');
        }
        $this->data['page_title']='Add Tender Category';
        return view('admin.tender_category.add_category', $this->data);   
    }

    public function save_tender_category(Request $request){

        // $this->validate($request,[
        //         'tender_category' => 'required|unique:users',
        //     ],[
        //         'tender_category.required' => ' This field is required.',
        //         // 'tender_category.unique' => ' Tender category already inserted use anothor.',
        //     ]);
        // if($validator->fails()) {
        //     return Redirect::back()->withErrors($validator);
        // }        
        $category_data = [
            'user_id' => Auth::user()->id,
            // 'status' => 'Disabled',
            // 'is_approved' => 0,
            'tender_category' => $request->get('tender_category'),
            'tender_category_detail' => ($request->get('tender_category_detail'))? $request->get('tender_category_detail') : "",
        ];
        if (Auth::user()->role != config('constants.SuperUser')) {
            $category_data['is_approved'] = 0;
            $category_data['status'] = 'Disabled';
        } else {
            $category_data['is_approved'] = 1;
            $category_data['status'] = 'Enabled';
        }
        if($request->get('id')){
            unset($category_data['status']);
            unset($category_data['is_approved']);
            if(TenderCategory::whereId($request->get('id'))->update($category_data)){
                return redirect()->route('admin.tender_category')->with('success', 'Data successfully updated.');
            }
        }else{
            if(TenderCategory::insert($category_data)){
                $module = 'Tender Category';
                $this->notification_task->entryApprovalNotify($module);
                return redirect()->route('admin.tender_category')->with('success', 'Data successfully inserted.');
            }    
        }
        
        return redirect()->route('admin.tender_category')->with('error', 'Error during operation. Try again!');
    }

    public function edit_tender_category($id) {
        /*$permission= Permissions::checkPermission($this->module_id,2);
        if(!$permission){
            return redirect()->route('admin.dashboard')->with('error','Access denied. You do not have permission to access this module.');
        }*/
        $view_special_permission = Permissions::checkSpecialPermission($this->module_id);
        if(!$view_special_permission){
            return redirect()->route('admin.dashboard')->with('error','Access denied. You do not have permission to access this module.');
        }
        $this->data['page_title'] = "Edit Tender Category";
        $this->data['category_detail'] = TenderCategory::where('id', $id)->first();
        if ($this->data['category_detail']->count() == 0) {
            return redirect()->route('admin.tender_category')->with('error', 'Error Occurred. Try Again!');
        }
        return view('admin.tender_category.edit_category', $this->data);
    }

    public function check_tender_category(Request $request){

        $msg = "";

        $id = $request->get('id');
        if($id){
            $check = TenderCategory::whereNotIn('id',[$id])->where('tender_category',$request->get('tender_category'))->exists();
        }else{
        $check = TenderCategory::where('tender_category',$request->get('tender_category'))->exists();
        }
        if($check){
            $msg = false;
        }else{
            $msg = true;
        }
        echo json_encode($msg);
    }
}
