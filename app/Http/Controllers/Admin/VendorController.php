<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Config;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use App\Common_query;
use App\Vendors;
use App\Companies;
use Illuminate\Support\Facades\Validator;
use App\Lib\Permissions;
use App\Lib\NotificationTask;

class VendorController extends Controller
{
    public $data;
    private $module_id;
    private $notification_task;

    public function __construct() {
        $this->notification_task = new NotificationTask();
        $this->data['module_title'] = "Vendors";
        $this->data['module_link'] = "admin.vendors";
        $this->module_id=35;
    }

    public function index() {
        $view_permission= Permissions::checkPermission($this->module_id,5);
        if(!$view_permission){
            return redirect()->route('admin.dashboard')->with('error','Access denied. You do not have permission to access this module.');
        }
        $this->data['view_special_permission'] = Permissions::checkSpecialPermission($this->module_id);
        $this->data['page_title'] = "Vendors";

        return view('admin.vendor.index', $this->data);
    }

    public function get_vendor_list() {

        $datatable_fields = array('vendor.vendor_name', 'vendor.email', 'vendor.contact_no' , 'vendor.address','vendor.pan_card_number', 'vendor.gst_number', 'company.company_name', 'vendor.detail', 'vendor.status', 'vendor.created_at');

        $request = Input::all();
        $conditions_array = ['vendor.is_approved' => 1];

        $getfiled =array('vendor.id','vendor.email','vendor.contact_no' , 'vendor.address','vendor.vendor_name','vendor.detail','company.company_name','vendor.status','vendor.pan_card_number','vendor.gst_number', 'vendor.created_at');
        $table = "vendor";

        $join_str[0]['join_type']='';
        $join_str[0]['table'] = 'company';
        $join_str[0]['join_table_id'] ='vendor.company_id';
        $join_str[0]['from_table_id'] = 'company.id';

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);

        die();
    }
    public function change_vendor_status($id, $status) {
        $view_permission= Permissions::checkPermission($this->module_id,2);
        if(!$view_permission){
            return redirect()->route('admin.dashboard')->with('error','Access denied. You do not have permission to access this module.');
        }
        if (Vendors::where('id', $id)->update(['status' => $status])) {
            return redirect()->route('admin.vendors')->with('success', 'Status successfully updated.');
        }
        return redirect()->route('admin.vendors')->with('error', 'Error during operation. Try again!');
    }

    public function add_vendor() {
        $view_permission= Permissions::checkPermission($this->module_id,3);
        $view_special_permission = Permissions::checkSpecialPermission($this->module_id);
        if(!$view_permission){
            return redirect()->route('admin.dashboard')->with('error','Access denied. You do not have permission to access this module.');
        }
        if(!$view_special_permission){
            return redirect()->route('admin.dashboard')->with('error','Access denied. You do not have permission to access this module.');
        }
        $this->data['page_title'] = 'Add vendor';
        $this->data['companies'] = Companies::orderBy('company_name')->pluck('company_name','id');
        // echo "<pre>";
        // print_r($this->data['companies']);die;
        return view('admin.vendor.add_vendor', $this->data);
    }

    public function insert_vendor(Request $request) {
        $validator_normal = Validator::make($request->all(), [
            'vendor_name' => 'required',
            'email' => 'required',
            //'pan_card_number' => 'required',
            'detail' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_vendor')->with('error', 'Please follow validation rules.');
        }
        $vendorModel = new Vendors();
        $vendorModel->user_id = Auth::user()->id;
        $vendorModel->vendor_name     = $request->input('vendor_name');
        $vendorModel->pan_card_number = $request->input('pan_card_number');
        $vendorModel->gst_number      = !empty($request->input('gst_number'))?$request->input('gst_number'):"";
        $vendorModel->email           = $request->input('email');
        $vendorModel->contact_no      = $request->input('contact_no');
        $vendorModel->address         = $request->input('address');
        $vendorModel->detail          = $request->input('detail');
        $vendorModel->company_id = $request->input('company_id');
        if (Auth::user()->role != config('constants.SuperUser')) {
            $vendorModel->status = 'Disabled';
            $vendorModel->is_approved = 0;
        } else {
            $vendorModel->status = 'Enabled';
            $vendorModel->is_approved = 1;
        }
        $vendorModel->created_at = date('Y-m-d h:i:s');
        $vendorModel->created_ip = $request->ip();
        $vendorModel->updated_at = date('Y-m-d h:i:s');
        $vendorModel->updated_ip = $request->ip();

        if ($vendorModel->save()) {
            $module = 'Vendor';
                $this->notification_task->entryApprovalNotify($module);
            return redirect()->route('admin.vendors')->with('success', 'New vendor added successfully.');
        } else {
            return redirect()->route('admin.add_vendor')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function edit_vendor($id) {
        $view_permission= Permissions::checkPermission($this->module_id,2);
        $view_special_permission = Permissions::checkSpecialPermission($this->module_id);
        if(!$view_special_permission){
            return redirect()->route('admin.dashboard')->with('error','Access denied. You do not have permission to access this module.');
        }
        $this->data['page_title'] = "Edit vendor";
        $this->data['vendor_detail'] = Vendors::where('vendor.id', $id)->get();
        if ($this->data['vendor_detail']->count() == 0) {
            return redirect()->route('admin.vendors')->with('error', 'Error Occurred. Try Again!');
        }
        $this->data['companies'] = Companies::orderBy('company_name')->pluck('company_name','id');
        return view('admin.vendor.edit_vendor', $this->data);
    }

    public function update_vendor(Request $request) {
        $validator_normal = Validator::make($request->all(), [
            'vendor_name' => 'required',
            'email' => 'required',
            //'pan_card_number' => 'required',
            'detail' => 'required',
            'company_id' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.vendors')->with('error', 'Please follow validation rules.');
        }
        $vendor_id = $request->input('id');
        $vendor_arr = [
            'user_id' =>  Auth::user()->id,
            'vendor_name' => $request->input('vendor_name'),
            'email' => $request->input('email'),
            'gst_number' => !empty($request->input('gst_number'))?$request->input('gst_number'):"",
            'pan_card_number' => $request->input('pan_card_number'),
            'detail' => $request->input('detail'),
            'contact_no' => $request->input('contact_no'),
            'address' => $request->input('address'),
            'company_id' => $request->input('company_id'),
            // 'status' => 'Disabled',
            // 'is_approved' => 0,
            'created_at' => date('Y-m-d h:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
        ];

        Vendors::where('id', $vendor_id)->update($vendor_arr);

        return redirect()->route('admin.vendors')->with('success', 'Vendor successfully updated.');
    }

    public function get_vendorlist_by_company(Request $request) {
        $company_id=$request->input('company_id');

        $vendor_list= Vendors::where('company_id',$company_id)->orderBy('vendor_name')->get();

        $html='<option value="">Select Vendor</option>';

        if($vendor_list->count()>0){
            foreach ($vendor_list as $vendor){
                if($vendor->vendor_name != "Others" && $vendor->vendor_name != "Other")
                    $html .='<option value="'.$vendor->id.'">'.$vendor->vendor_name.'</option>';
            }
        }
        echo $html; die();
    }

    //check role name exist or not
    public function check_uniquePancardNumber(Request $request) {
        $pan_card_number = $request->pan_card_number;
        $vendor_id       = $request->vendor_id;
		if(!$request->company_id){
        $pancardheck     = Vendors::select(['id'])->where('pan_card_number', '=', $pan_card_number)->first();
        }
		else{
			$pancardheck     = Vendors::select(['id'])->where('pan_card_number', '=', $pan_card_number)->where('company_id',$request->company_id)->first();
		}
        //Check during add pancard details
        if(empty($request->vendor_id)) {
          if (!empty($pancardheck)) {
                echo 'false';
                die();
            } else {
                echo 'true';
                die();
            }
        }

        //Check during edit pancard details
        if(!empty($request->pan_card_number) && !empty($request->vendor_id) && !empty($pancardheck)) {
            if($pancardheck->id==$vendor_id) {
                echo 'true';
                die();
            }
            else {
                echo 'false';
                die();
            }
        }
        else
        {
            echo 'true';
            die();
        }
    }

    public function check_vender_name(Request $request){
        if(!empty($request->get('id'))){
            $check = Vendors::whereNotIn('id', [$request->get('id')])->where('company_id', $request->get('company_id'))->where('vendor_name', $request->get('vendor_name'))->count();
        }else{
            $check = Vendors::where('company_id', $request->get('company_id'))->where('vendor_name', $request->get('vendor_name'))->count();
        }

        if($check){
            echo "false";
            die;
        }else{
            echo "true";
            die;
        }
    }

    public function vender_name_autosuggest(Request $request){
        $vender = Vendors::where('company_id', $request->get('company_id'))->where('vendor_name', 'like', '%' . $request->get('vendor_name') . '%')->pluck('vendor_name')->toArray();
        echo json_encode($vender);exit;
    }
}
