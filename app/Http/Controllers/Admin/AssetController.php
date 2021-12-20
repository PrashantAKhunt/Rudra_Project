<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Input;
use App\Common_query;
use App\Employees;
use App\Asset_expired_reminder_dates;
use App\EmployeesBankDetails;
use App\EmployeesLoans;
use App\Companies;
use App\User;
use App\TaxDeclaration;
use App\Role_module;
use App\Asset;
use Illuminate\Support\Facades\Validator;
use App\Imports\EmployeeSalaryImport;
use Maatwebsite\Excel\Facades\Excel;
use DB;
use Auth;
use App\AssetImage;
use App\Lib\Permissions;

class AssetController extends Controller
{
    public $data;

    public function __construct() {
        $this->data['module_title'] = "Asset";
        $this->data['module_link'] = "admin.asset";
    }

    public function index(){
        $this->data['page_title']="Organization Asset";
        $this->data['access_rule'] = '';
        $check_result=Permissions::checkPermission(14,5);
        if(!$check_result){
            return redirect()->route('admin.dashboard')->with('error','Access Denied. You are not authorized to access that page.');
        }
        $access_level = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => 14])->get()->first();
        if(!empty($access_level))
        {
            $this->data['access_rule'] = $access_level->access_level;
        }
        return view('admin.asset.index', $this->data);
    }

    public function asset_list() {
        $datatable_fields = array('asset.name','asset.id','asset.description','asset_image.image','asset.status');
        $request = Input::all();
        $conditions_array = [];
        $check_result     = Permissions::checkPermission(14,5);
        
        if(!$check_result){
            return redirect()->route('admin.dashboard')->with('error','Access Denied. You are not authorized to access that page.');
        }

        $getfiled = array('asset.name','asset.id','asset.description','asset_image.image','asset.status');
        $table = "asset";

        $join_str[0]['join_type']='';
        $join_str[0]['table'] = 'asset_image';
        $join_str[0]['join_table_id'] ='asset.id';
        $join_str[0]['from_table_id'] = 'asset_image.asset_id';

        echo AssetImage::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request,$join_str);
                                                  
        die();
    }
    public function add_asset() {
        $this->data['page_title']="Add Asset Details";
        $check_result=Permissions::checkPermission(14,3);
        if(!$check_result){
            return redirect()->route('admin.dashboard')->with('error','Access Denied. You are not authorized to access that page.');
        }
        
        $this->data['companies'] = Companies::orderBy('company_name')->select('company_name','id')->get()->toArray();

        return view('admin.asset.add_asset', $this->data);
    }
    public function edit_asset($id) {
        $this->data['employee_detail'] = Asset::where('id', $id)->get();
        if ($this->data['employee_detail']->count() == 0) {
            return redirect()->route('admin.employee_loan')->with('error', 'Error Occurred. Try Again!');
        }
        $check_result=Permissions::checkPermission(14,2);
        if(!$check_result){
            return redirect()->route('admin.dashboard')->with('error','Access Denied. You are not authorized to access that page.');
        }
        echo json_encode($this->data['employee_detail']);
    }
    public function update_asset(Request $request) {
        $asset_id = $request->input('id'); 
        
        // //upload user profile image
        // if ($request->hasFile('image')) {
        //     $profile_image = $request->file('image');                               
        //     $file_path = $profile_image->store('public/asset_image');
        //     if ($file_path) {
        //         $asset_image = $file_path;
        //     }           
        //     $asset_arr = [
        //         'image'=>$asset_image
        //     ];
        //      $AssetImageModel = new AssetImage();
        //      $AssetImageModel->asset_id = $asset_id;
        //      $AssetImageModel->image    = $asset_image;
        //      $AssetImageModel->save();

        //     //AssetImage::where('asset_id', $asset_id)->update($asset_arr);
        // }

        //upload user profile image
        $asset_image = '';
        if ($request->hasFile('image')) {
            $profile_image = $request->file('image');
            foreach ($profile_image as $image){
                $file_path = $image->store('public/asset_image');
                if ($file_path) {
                    $asset_image = $file_path;
                    $AssetImageModel = new AssetImage();
                    $AssetImageModel->asset_id = $asset_id;
                    $AssetImageModel->image    = $asset_image;
                    $AssetImageModel->save();
                } 
            }                               
        }

        $asset_arr = [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'asset_1' => $request->input('asset_1'),
            'asset_2' => $request->input('asset_2'),
            'expiration_date'  =>  $request->input('expiration_date') ? date('Y-m-d', strtotime($request->input('expiration_date'))) : NULL,
            'created_at' => date('Y-m-d h:i:s'),
            'updated_at' => date('Y-m-d h:i:s'),
        ];
        
        Asset::where('id', $asset_id)->update($asset_arr);

        return redirect()->route('admin.asset')->with('success', 'Asset updated successfully.');
    }
     public function insert_asset(Request $request) {   
        $validator_normal = Validator::make($request->all(), [
            'name' => 'required',
            'asset_type'=>'required',
            'company_id'=>'required',
            'description' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.asset')->with('error', 'Please follow validation rules.');
        }

        $Asset = new Asset();
        $Asset->name             = $request->input('name');
        $Asset->description      = $request->input('description');
        
        $Asset->company_id       = $request->input('company_id');
        $Asset->asset_type       = $request->input('asset_type');
        if($request->input('asset_type')=='Vehicle Asset') {
            $Asset->fuel_type    = $request->input('fuel_type');
        }
        $Asset->asset_1          = $request->input('asset_1');
        $Asset->asset_2          = $request->input('asset_2');
        $Asset->expiration_date      = $request->input('expiration_date') ? date('Y-m-d', strtotime($request->input('expiration_date'))) : NULL;
        $Asset->created_at       = date('Y-m-d h:i:s');
        $Asset->updated_at       = date('Y-m-d h:i:s');
        if ($Asset->save()) {
            //upload user profile image
            $asset_image = '';
            if ($request->hasFile('image')) {
                $profile_image = $request->file('image');
                foreach ($profile_image as $image){
                    $file_path = $image->store('public/asset_image');
                    if ($file_path) {
                        $asset_image = $file_path;
                        $AssetImageModel = new AssetImage();
                        $AssetImageModel->asset_id = $Asset->id;
                        $AssetImageModel->image    = $asset_image;
                        $AssetImageModel->save();
                    } 
                }                               
            }

            //save reminder dates
           
            if ($request->input('reminder_date')[0] != NULL) {
                $reminder_date_arr = $request->input('reminder_date');
    
                
                foreach ($reminder_date_arr as $key => $date) {
                        $AssetDateModel = new Asset_expired_reminder_dates();
                        $AssetDateModel->asset_id = $Asset->id;
                        $AssetDateModel->date = date('Y-m-d', strtotime($date));
                        $AssetDateModel->created_at = date('Y-m-d h:i:s');
                        $AssetDateModel->created_ip =  $request->ip();
                        $AssetDateModel->save();
                }
            }
            //
            return redirect()->route('admin.asset')->with('success', 'Asset added successfully.');
        } else {
            return redirect()->route('admin.asset')->with('error', 'Error occurre in insert. Try Again!');
        }
    }
    public function update_reminder_dates(Request $request) {

        //save reminder dates
        $id = $request->input('asset_id');
        Asset_expired_reminder_dates::where('asset_id', $id)->delete();
        if ($request->input('reminder_date')) {
            $reminder_date_arr = $request->input('reminder_date');
            
            foreach ($reminder_date_arr as $key => $date) {
                    $AssetDateModel = new Asset_expired_reminder_dates();
                    $AssetDateModel->asset_id = $id;
                    $AssetDateModel->date = date('Y-m-d', strtotime($date));
                    $AssetDateModel->created_at = date('Y-m-d h:i:s');
                    $AssetDateModel->created_ip =  $request->ip();
                    $AssetDateModel->save();
            }
        }
        return redirect()->route('admin.asset')->with('success', 'Reminder Dates updated successfully.');
    }

    public function delete_asset($id) {
        $check_result=Permissions::checkPermission(14,4);
        if(!$check_result){
            return redirect()->route('admin.dashboard')->with('error','Access Denied. You are not authorized to access that page.');
        }

        if (Asset::where('id', $id)->delete()) {
            return redirect()->route('admin.asset')->with('success', 'Delete successfully updated.');
        }
        return redirect()->route('admin.asset')->with('error', 'Error during operation. Try again!');
    }
    public function get_asset_images($id) {
        $this->data['asset_detail'] = AssetImage::where('asset_id', $id)->get();
        $check_result=Permissions::checkPermission(15,1);
        if ($this->data['asset_detail']->count() > 0) {
            echo json_encode($this->data['asset_detail']);
        }
        else {
            echo json_encode(array('status'=>0));
        }
        die();
    }

    public function aseet_expired_reminder_dates(Request $request){
        $validator_normal = Validator::make($request->all(), ['id' => 'required']);
        if ($validator_normal->fails()) {
            return response()->json(['status' => false]);
        }
        $reminder_dates = Asset_expired_reminder_dates::where('asset_id',$request->get('id'))->pluck('date')->toArray();
        $this->data['reminder_dates'] = $reminder_dates;

        if ($reminder_dates) {
            return response()->json(['status' => true, 'data' => $this->data]);
        } else {
            return response()->json(['status' => false]);
        }
    } 

    public function change_asset($id, $status) {
        // $check_result=Permissions::checkPermission(16,2);
        
        // if(!$check_result){
        //     return redirect()->route('admin.dashboard')->with('error','Access Denied. You are not authorized to access that page.');
        // }
        if (Asset::where('id', $id)->update(['status' => $status])) {
            return redirect()->route('admin.asset')->with('success', 'Status successfully updated.');
        }
        return redirect()->route('admin.asset')->with('error', 'Error during operation. Try again!');
    }
}
