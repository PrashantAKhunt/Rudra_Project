<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use App\Common_query;
use App\Companies;
use App\CompanyCrtfcateImage;

use App\Company_document_list;
use App\Lib\NotificationTask;
use App\Lib\Permissions;
use Dotenv\Regex\Success;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\ContentTypes;

use function PHPSTORM_META\type;

class CompanyController extends Controller
{
    public $data;
    public $notification_task;

    public function __construct() {
        $this->notification_task = new NotificationTask();
        $this->data['module_title'] = "Companies";
        $this->data['module_link'] = "admin.companies";
        $this->module_id=17;
    }

    public function index() {   //chnage
        $this->data['page_title'] = "Companies";
        $this->data['view_special_permission'] = Permissions::checkSpecialPermission($this->module_id);
        return view('admin.company.index', $this->data);
    }


    public function get_company_list() {   //chnage
        $datatable_fields = array('company.company_name','company_short_name','gst_no','pan_no','cin_no','tan_no','created_at','status');
        $request = Input::all();
        $conditions_array = ['is_approved' => 1];

        $getfiled =array('company.id','company.company_name', 'company_short_name','company.detail','status','gst_no','pan_no','cin_no','tan_no',
            'created_at','moa_image','gst_image','pan_image','tan_image');
        $table = "company";

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, []);
        die();
    }
    public function change_company_status($id, $status) {
        if (Companies::where('id', $id)->update(['status' => $status])) {
            return redirect()->route('admin.companies')->with('success', 'Status successfully updated.');
        }
        return redirect()->route('admin.companies')->with('error', 'Error during operation. Try again!');
    }

    public function add_company() {
        $view_special_permission = Permissions::checkSpecialPermission($this->module_id);
        if(!$view_special_permission){
            return redirect()->route('admin.dashboard')->with('error','Access denied. You do not have permission to access this module.');
        }
        $this->data['page_title'] = 'Add Company';
        return view('admin.company.add_company', $this->data);
    }

    public function insert_company(Request $request) {   //chnage
        $validator_normal = Validator::make($request->all(), [
            'company_name' => 'required',
            'detail' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_company')->with('error', 'Please follow validation rules.');
        }
        
         //upload user profile image
        $cmp_moa_image_file = '';
        if ($request->hasFile('cmp_moa_image')) {
            $cmp_moa_image = $request->file('cmp_moa_image');
            //$file_path = $cmp_moa_image->store('public/cmp_moa_image');
            $original_file_name = explode('.', $cmp_moa_image->getClientOriginalName());
    
            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);

            $file_path = $cmp_moa_image->storeAs('public/cmp_moa_image', $new_file_name); 
        
            if ($file_path) {
                $cmp_moa_image_file = $file_path;
            }
        }

        //upload user profile image
        $cmp_gst_image_file = '';
        if ($request->hasFile('cmp_gst_image')) {
            $cmp_gst_image = $request->file('cmp_gst_image');
            //$file_path = $cmp_gst_image->store('public/cmp_gst_image');
            $original_file_name = explode('.', $cmp_gst_image->getClientOriginalName());
    
            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);

            $file_path = $cmp_gst_image->storeAs('public/cmp_gst_image', $new_file_name); 
        
            if ($file_path) {
                $cmp_gst_image_file = $file_path;
            }
        }

        //upload user PAN image
        $cmp_pan_image_file = '';
        if ($request->hasFile('cmp_pan_image')) {
            $cmp_pan_image = $request->file('cmp_pan_image');
           // $file_path = $cmp_pan_image->store('public/cmp_pan_image');
            $original_file_name = explode('.', $cmp_pan_image->getClientOriginalName());
    
            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);

            $file_path = $cmp_pan_image->storeAs('public/cmp_pan_image', $new_file_name); 
        
            if ($file_path) {
                $cmp_pan_image_file = $file_path;
            }
        }

        //upload user TAN image
        $cmp_tan_image_file = '';
        if ($request->hasFile('cmp_tan_image')) {
            $cmp_tan_image = $request->file('cmp_tan_image');
            $original_file_name = explode('.', $cmp_tan_image->getClientOriginalName());    
            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);
            $file_path = $cmp_tan_image->storeAs('public/cmp_tan_image', $new_file_name);         
            if ($file_path) {
                $cmp_tan_image_file = $file_path;
            }
        }

            // dd('exit');
        $companyModel = new Companies();
        $companyModel->user_id = Auth::user()->id;
        $companyModel->company_name       = $request->input('company_name');
        $companyModel->company_short_name = $request->input('company_short_name');
        $companyModel->pan_image  = $cmp_pan_image_file;
        $companyModel->tan_image  = $cmp_tan_image_file;
        $companyModel->moa_image  = $cmp_moa_image_file;
        $companyModel->gst_image  = $cmp_gst_image_file;
        $companyModel->gst_no     = $request->input('cmp_gst_no');
        $companyModel->cin_no     = $request->input('cmp_cin_no');
        $companyModel->pan_no     = $request->input('cmp_pan_no');
        $companyModel->tan_no     = $request->input('cmp_tan_no');
        $companyModel->detail = $request->input('detail');
        if (Auth::user()->role != config('constants.SuperUser')) {
            $companyModel->status = 'Disabled';
            $companyModel->is_approved = 0;
        } else {
            $companyModel->status = 'Enabled';
            $companyModel->is_approved = 1;
        }
        $companyModel->created_at = date('Y-m-d h:i:s');
        $companyModel->created_ip = $request->ip();
        $companyModel->updated_at = date('Y-m-d h:i:s');
        $companyModel->updated_ip = $request->ip();
        
        if ($companyModel->save()) {

            // CompanyCrtfcateImage insert code for multiple image
            $coi_image = '';
            if ($request->hasFile('coi_crtfcte_image')) {
                $profile_image = $request->file('coi_crtfcte_image');
                foreach ($profile_image as $image){
                    $file_path = $image->store('public/coi_image');
                    if ($file_path) {
                        $coi_image = $file_path;
                        $CompanyCrtfcateImageModel = new CompanyCrtfcateImage();
                        $CompanyCrtfcateImageModel->company_id = $companyModel->id;
                        $CompanyCrtfcateImageModel->image    = $coi_image;
                        $CompanyCrtfcateImageModel->save();
                    } 
                }                               
            }

            $module = 'Company';
            $this->notification_task->entryApprovalNotify($module);
            return redirect()->route('admin.companies')->with('success', 'New company added successfully.');
        } else {
            return redirect()->route('admin.add_company')->with('error', 'Error occurred in insert. Try Again!');
        }
    }

    public function edit_company($id) {
        $this->data['page_title'] = "Edit company";
        $view_special_permission = Permissions::checkSpecialPermission($this->module_id);
        if(!$view_special_permission){
            return redirect()->route('admin.dashboard')->with('error','Access denied. You do not have permission to access this module.');
        }
        $this->data['company_detail'] = Companies::where('company.id', $id)->get();
        if ($this->data['company_detail']->count() == 0) {
            return redirect()->route('admin.companies')->with('error', 'Error Occurred. Try Again!');
        }
        return view('admin.company.edit_company', $this->data);
    }

    public function update_company(Request $request) {    //chnage
        $validator_normal = Validator::make($request->all(), [
            'company_name' => 'required',
            'detail' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.companies')->with('error', 'Please follow validation rules.');
        } 
        $company_id = $request->input('id'); 

         //upload user profile image
        $cmp_moa_image_file = '';
        if ($request->hasFile('cmp_moa_image')) {
            $cmp_moa_image = $request->file('cmp_moa_image');
            //$file_path = $cmp_moa_image->store('public/cmp_moa_image');
            $original_file_name = explode('.', $cmp_moa_image->getClientOriginalName());
    
            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);

            $file_path = $cmp_moa_image->storeAs('public/cmp_moa_image', $new_file_name); 
        
            if ($file_path) {
                $cmp_moa_image_file = $file_path;
            }
        }

        //upload user profile image
        $cmp_gst_image_file = '';
        if ($request->hasFile('cmp_gst_image')) {
            $cmp_gst_image = $request->file('cmp_gst_image');
            $file_path = $cmp_gst_image->store('public/cmp_gst_image');
            $original_file_name = explode('.', $cmp_moa_image->getClientOriginalName());
    
            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);

            $file_path = $cmp_moa_image->storeAs('public/cmp_moa_image', $new_file_name); 
        
            if ($file_path) {
                $cmp_gst_image_file = $file_path;
            }
        }
        
        //upload user PAN image
        $cmp_pan_image_file = '';
        if ($request->hasFile('cmp_pan_image')) {
            $cmp_pan_image = $request->file('cmp_pan_image');
            //$file_path = $cmp_pan_image->store('public/cmp_pan_image');
            $original_file_name = explode('.', $cmp_pan_image->getClientOriginalName());
    
            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);

            $file_path = $cmp_pan_image->storeAs('public/cmp_pan_image', $new_file_name); 
        
            if ($file_path) {
                $cmp_pan_image_file = $file_path;
            }
        }

        //upload user TAN image
        $cmp_tan_image_file = '';
        if ($request->hasFile('cmp_tan_image')) {
            $cmp_tan_image = $request->file('cmp_tan_image');
            $original_file_name = explode('.', $cmp_tan_image->getClientOriginalName());    
            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);
            $file_path = $cmp_tan_image->storeAs('public/cmp_tan_image', $new_file_name);
            if ($file_path) {
                $cmp_tan_image_file = $file_path;
            }
        }
        $coi_image = '';
            if ($request->hasFile('coi_crtfcte_image')) {
                CompanyCrtfcateImage::where('company_id', $company_id)->delete();
                $profile_image = $request->file('coi_crtfcte_image');
                foreach ($profile_image as $image){
                    $file_path = $image->store('public/coi_image');
                    if ($file_path) {
                        $coi_image = $file_path;
                        $CompanyCrtfcateImageModel = new CompanyCrtfcateImage();
                        $CompanyCrtfcateImageModel->company_id = $company_id;
                        $CompanyCrtfcateImageModel->image    = $coi_image;
                        $CompanyCrtfcateImageModel->save();
                    } 
                }                               
            }

        $company_arr=[];$company_arr_image=[];
        if (!empty($cmp_pan_image_file)) {
            $company_arr_image['pan_image'] = $cmp_pan_image_file;
        }
        if (!empty($cmp_tan_image_file)) {
            $company_arr_image['tan_image'] = $cmp_tan_image_file;
        }
        if (!empty($coi_crtfcte_image_file)) {
            $company_arr_image['coi_crtfcte_image'] = $coi_crtfcte_image_file;
        }
        if (!empty($cmp_gst_image_file)) {
            $company_arr_image['gst_image'] = $cmp_gst_image_file;
        }
        if (!empty($cmp_moa_image_file)) {
            $company_arr_image['moa_image'] = $cmp_moa_image_file;
        }

        $company_arr = [
            'user_id' =>  Auth::user()->id,
            'company_name' => $request->input('company_name'),
            'company_short_name' => $request->input('company_short_name'),
            'gst_no' => $request->input('cmp_gst_no'),
            'cin_no' => $request->input('cmp_cin_no'),
            'pan_no' => $request->input('cmp_pan_no'),
            'tan_no' => $request->input('cmp_tan_no'),
            'detail' => $request->input('detail'),
            'created_at' => date('Y-m-d h:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
        ];
        if(!empty($company_arr_image)){
            Companies::where('id', $company_id)->update($company_arr_image);
        }
        Companies::where('id', $company_id)->update($company_arr);

        return redirect()->route('admin.companies')->with('success', 'Company successfully updated.');
    }
    public function delete_company($id) {
        if (Companies::where('id', $id)->delete()) {
            return redirect()->route('admin.companies')->with('success', 'Delete successfully updated.');
        }
    return redirect()->route('admin.companies')->with('error', 'Error during operation. Try again!');
    }

    public function cmp_document_list($id)
    {
        $this->data['page_title']   = "Companies Document List";
        $this->data['company_data'] = Company_document_list::where('company_id',$id)->get();
        $this->data['id'] = $id;
        // echo "<pre>";
        // print_r($this->data['company_data'][0]->doc_link);
        // die();
        return view('admin.company.document_list', $this->data);
    }
    
    public function add_company_document($id)
    {
        $this->data['page_title'] = 'Add Company Document';
        $this->data['id'] = $id;
        return view('admin.company.add_company_document', $this->data);
    }

    public function insert_company_document(Request $request)
    {
         $validator_normal = Validator::make($request->all(), [
            'title' => 'required',
            'document' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.cmp_document_list')->with('error', 'Please follow validation rules.');
        }

        //upload user profile image
        $document_file = '';
        if ($request->hasFile('document')) {
            $document = $request->file('document');
            //$file_path = $document->store('public/document');
            $original_file_name = explode('.', $document->getClientOriginalName());
    
            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);

            $file_path = $document->storeAs('public/document', $new_file_name); 
        
            if ($file_path) {
                $document_file = $file_path;
            }
        }

        $Company_document_list = new Company_document_list();
        $Company_document_list->title       = $request->input('title');
        $Company_document_list->company_id  = $request->input('company_id');
        $Company_document_list->doc_link    = $document_file;
        if ($Company_document_list->save()) {
            return redirect()->route('admin.cmp_document_list',['id'=>$request->input('company_id')])->with('success', 'New company document added successfully.');
        } else {
            return redirect()->route('admin.add_company_document')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function delete_document($id,$company_id) {
        if (Company_document_list::where('id', $id)->delete()) {
            return redirect()->route('admin.cmp_document_list',['id'=>$company_id])->with('success', 'Delete Document successfully updated.');
        }
        return redirect()->route('admin.cmp_document_list',['id'=>$company_id])->with('error', 'Error during operation. Try again!');
    }

    public function get_company_crt_images(Request $request){
        $images = CompanyCrtfcateImage::where('company_id',$request->get('company_id'))->get();
        return response()->json(['data' => $images]);

    }

}
