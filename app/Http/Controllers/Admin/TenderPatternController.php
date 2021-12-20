<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\TenderPattern;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use App\Common_query;
use App\Lib\Permissions;
use App\Lib\NotificationTask;

class TenderPatternController extends Controller
{
    public $data;
    private $notification_task;


    public function __construct() {
        $this->notification_task = new NotificationTask();
        $this->data['module_title']="Tender Pattern";
        $this->data['module_link']='admin.tender_pattern';
        $this->module_id = 66;
    }
    
    public function index(){
        $this->data['page_title']='Tender Pattern';
        $this->data['view_special_permission'] = Permissions::checkSpecialPermission($this->module_id);
        return view('admin.tender_pattern.index', $this->data);
    }

    public function get_tender_pattern_list() {
        $datatable_fields = array('id','tender_pattern_name', 'tender_pattern_detail','created_at','status');
        $request = Input::all();
        $conditions_array = ['is_approved' => 1];

        $getfiled =array('id','tender_pattern_name', 'tender_pattern_detail','created_at','status');
        $table = "tender_pattern";

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, []);
        die();
    }
    
    public function change_tender_pattern_status($id, $status) {
        /*$permission= Permissions::checkPermission($this->module_id,2);
        if(!$permission){
            return redirect()->route('admin.dashboard')->with('error','Access denied. You do not have permission to access this module.');
        }*/
        if (TenderPattern::where('id', $id)->update(['status' => $status])) {
            return redirect()->route('admin.tender_pattern')->with('success', 'Status successfully updated.');
        }
        return redirect()->route('admin.tender_pattern')->with('error', 'Error during operation. Try again!');
    }

    public function delete_tender_pattern($id) {
        
        if (TenderPattern::where('id', $id)->delete()) {
            return redirect()->route('admin.tender_pattern')->with('success', 'Delete successfully updated.');
        }
        return redirect()->route('admin.tender_pattern')->with('error', 'Error during operation. Try again!');
    }

    public function add_tender_pattern(){
        $view_special_permission = Permissions::checkSpecialPermission($this->module_id);
        if(!$view_special_permission){
            return redirect()->route('admin.dashboard')->with('error','Access denied. You do not have permission to access this module.');
        }
        $this->data['page_title']='Add Tender Pattern';
        return view('admin.tender_pattern.add_pattern', $this->data);   
    }

    public function save_tender_pattern(Request $request){

        $category_data = [
            'user_id' => Auth::user()->id,
            // 'status' => 'Disabled',
            // 'is_approved' => 0,
            'tender_pattern_name' => $request->get('tender_pattern_name'),
            'tender_pattern_detail' => ($request->get('tender_pattern_detail'))? $request->get('tender_pattern_detail') : "",
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
            if(TenderPattern::whereId($request->get('id'))->update($category_data)){
                return redirect()->route('admin.tender_pattern')->with('success', 'Data successfully updated.');
            }
        }else{
            if(TenderPattern::insert($category_data)){
                $module = 'Tender Pattern Category';
                $this->notification_task->entryApprovalNotify($module);
                return redirect()->route('admin.tender_pattern')->with('success', 'Data successfully inserted.');
            }    
        }
        
        return redirect()->route('admin.tender_pattern')->with('error', 'Error during operation. Try again!');
    }

    public function edit_tender_pattern($id) {
        $view_special_permission = Permissions::checkSpecialPermission($this->module_id);
        if(!$view_special_permission){
            return redirect()->route('admin.dashboard')->with('error','Access denied. You do not have permission to access this module.');
        }
        $this->data['page_title'] = "Edit Tender Pattern";
        $this->data['tender_pattern_detail'] = TenderPattern::where('id', $id)->first();
        if ($this->data['tender_pattern_detail']->count() == 0) {
            return redirect()->route('admin.tender_pattern')->with('error', 'Error Occurred. Try Again!');
        }
        // dd($this->data);
        return view('admin.tender_pattern.edit_pattern', $this->data);
    }

    public function check_tender_pattern(Request $request){

        $msg = "";

        $id = $request->get('id');
        if($id){
            $check = TenderPattern::whereNotIn('id',[$id])->where('tender_pattern_name',$request->get('tender_pattern_name'))->exists();
        }else{
        $check = TenderPattern::where('tender_pattern_name',$request->get('tender_pattern_name'))->exists();
        }
        if($check){
            $msg = false;
        }else{
            $msg = true;
        }
        echo json_encode($msg);
    }
}
