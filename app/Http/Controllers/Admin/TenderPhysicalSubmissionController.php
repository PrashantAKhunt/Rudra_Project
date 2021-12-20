<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Tender_physical_submission;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\Common_query;
use App\Lib\Permissions;
use App\Lib\NotificationTask;

class TenderPhysicalSubmissionController extends Controller
{
    public $data;
    private $notification_task;


    public function __construct() {
        $this->notification_task = new NotificationTask();
        $this->data['module_title']="Tender Physical Submission";
        $this->data['module_link']='admin.tender_physical_submission';
        $this->module_id = 67;
    }
    
    public function index(){
        $this->data['page_title']='Tender Physical Submission';
        $this->data['view_special_permission'] = Permissions::checkSpecialPermission($this->module_id);
        return view('admin.tender_physical_submission.index', $this->data);
    }

    public function get_tender_physical_sub_list() {
        $datatable_fields = array('id','mode_name', 'mode_detail','created_at','status');
        $request = Input::all();
        $conditions_array = ['is_approved' => 1];

        $getfiled =array('id','mode_name', 'mode_detail','created_at','status');
        $table = "tender_physical_submission";

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, []);
        die();
    }
    
    public function change_tender_physical_sub_status($id, $status) {
        
        if (Tender_physical_submission::where('id', $id)->update(['status' => $status])) {
            return redirect()->route('admin.tender_physical_submission')->with('success', 'Status successfully updated.');
        }
        return redirect()->route('admin.tender_physical_submission')->with('error', 'Error during operation. Try again!');
    }

    public function delete_tender_physical_sub($id) {
        
        if (Tender_physical_submission::where('id', $id)->delete()) {
            return redirect()->route('admin.tender_physical_submission')->with('success', 'Delete successfully updated.');
        }
        return redirect()->route('admin.tender_physical_submission')->with('error', 'Error during operation. Try again!');
    }

    public function add_tender_physical_sub(){
        $view_special_permission = Permissions::checkSpecialPermission($this->module_id);
        if(!$view_special_permission){
            return redirect()->route('admin.dashboard')->with('error','Access denied. You do not have permission to access this module.');
        }
        $this->data['page_title']='Add Tender Physical Submission';
        return view('admin.tender_physical_submission.add_physical_sub', $this->data);   
    }

    public function save_tender_physical_mode(Request $request){

        $mode_data = [
            'user_id' => Auth::user()->id,
            // 'status' => 'Disabled',
            // 'is_approved' => 0,
            'mode_name' => $request->get('mode_name'),
            'mode_detail' => ($request->get('mode_detail'))? $request->get('mode_detail') : "",
        ];
        if (Auth::user()->role != config('constants.SuperUser')) {
            $mode_data['is_approved'] = 0;
            $mode_data['status'] = 'Disabled';
        } else {
            $mode_data['is_approved'] = 1;
            $mode_data['status'] = 'Enabled';
        }
        if($request->get('id')){
            unset($mode_data['status']);
            unset($mode_data['is_approved']);
            if(Tender_physical_submission::whereId($request->get('id'))->update($mode_data)){
                return redirect()->route('admin.tender_physical_submission')->with('success', 'Data successfully updated.');
            }
        }else{
            if(Tender_physical_submission::insert($mode_data)){
                $module = 'Tender Physical Submission Category';
                $this->notification_task->entryApprovalNotify($module);
                return redirect()->route('admin.tender_physical_submission')->with('success', 'Data successfully inserted.');
            }    
        }
        
        return redirect()->route('admin.tender_physical_submission')->with('error', 'Error during operation. Try again!');
    }

    public function edit_tender_physical_mode($id) {
        $view_special_permission = Permissions::checkSpecialPermission($this->module_id);
        if(!$view_special_permission){
            return redirect()->route('admin.dashboard')->with('error','Access denied. You do not have permission to access this module.');
        }
        $this->data['page_title'] = "Edit Tender Physical Submission";
        $this->data['tender_physical_mode'] = Tender_physical_submission::where('id', $id)->first();
        if ($this->data['tender_physical_mode']->count() == 0) {
            return redirect()->route('admin.tender_physical_submission')->with('error', 'Error Occurred. Try Again!');
        }
        // dd($this->data);
        return view('admin.tender_physical_submission.edit_physical_sub', $this->data);
    }

    public function check_tender_physical_mode(Request $request){

        $msg = "";

        $id = $request->get('id');
        if($id){
            $check = Tender_physical_submission::whereNotIn('id',[$id])->where('mode_name',$request->get('mode_name'))->exists();
        }else{
        $check = Tender_physical_submission::where('mode_name',$request->get('mode_name'))->exists();
        }
        if($check){
            $msg = false;
        }else{
            $msg = true;
        }
        echo json_encode($msg);
    }
}
