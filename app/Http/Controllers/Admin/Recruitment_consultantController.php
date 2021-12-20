<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Recruitment_consultant;
use Illuminate\Support\Facades\Validator;
use App\Lib\Permissions;
use App\Role_module;
use Auth;

class Recruitment_consultantController extends Controller
{
    public $data;
    public function __construct() {
        $this->data['module_title']="Recruitmant Consultant";
        $this->data['module_link']="admin.recruitment_consultant";
    }
    
    public function index(){
        $this->data['page_title']="Recruitment Consultant";
        $this->data['consultant_list']= Recruitment_consultant::get();
        $this->data['access_rule'] = "";
        
        $access_level             = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => 33])->get()->first(); 
        
        if(!empty($access_level)) {
            $this->data['access_rule'] = $access_level->access_level;
        }

        return view('admin.recruitment_consultant.index',$this->data);
    }
    
    public function recruitment_change_status($id,$status){
        if(Recruitment_consultant::where('id',$id)->update(['status'=>$status])){
            return redirect()->route('admin.recruitment_consultant')->with('success','Recruitment consultant status successfully updated.');
        }
        else{
            return redirect()->route('admin.recruitment_consultant')->with('error','Error Occurred. Try Again!');
        }
    }
    
    public function add_consultant(){
        $this->data['page_title']="Add Recruitment Consultant";
        $check_result = Permissions::checkPermission(33,3);
        $this->data['company_list'] = \App\Companies::where('status', 'Enabled')->orderBy('company_name')->get();
        if(!$check_result) {
            return redirect()->route('admin.dashboard')->with('error','Access Denied. You are not authorized to access that page.');
        }

        return view('admin.recruitment_consultant.add_consultant', $this->data);
    }
    
    public function insert_consultant(Request $request) {
        
        $check_result = Permissions::checkPermission(33,3);
        if(!$check_result) {
            return redirect()->route('admin.dashboard')->with('error','Access Denied. You are not authorized to access that page.');
        }

        $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'required',
                    'address' => 'required',
                    'specialty'=>'required',
                    'company_id'=>'required'
        ]);
        if ($validator->fails()) {
            return redirect()->route('admin.add_user')->with('error', 'Please follow validation rules.');
        }
        
        $request_data=$request->all();
        
        $consultant_arr=[
            'name'=>$request_data['name'],
            'address'=>$request_data['address'],
            'email'=>$request_data['email'],
            'specialty'=>$request_data['specialty'],
            'company_id'=>$request_data['company_id'],
            'status'=>'Enable',
            'created_at'=>date('Y-m-d H:i:s'),
            'updated_at'=>date('Y-m-d H:i:s'),
            'created_ip'=>$request->ip(),
            'updated_ip'=>$request->ip(),
            'updated_by'=> \Illuminate\Support\Facades\Auth::user()->id,
            
        ];
        
        if(Recruitment_consultant::insert($consultant_arr)){
            return redirect()->route('admin.recruitment_consultant')->with('success','New recruitment consultant added successfully.');
        }
        else{
            return redirect()->route('admin.recruitment_consultant')->with('error','Error Occurred. Try Again!');
        }
        
    }
    
    public function edit_consultant($id) {
        $this->data['page_title']="Edit Recruitment Consultant";
        $this->data['company_list'] = \App\Companies::where('status', 'Enabled')->orderBy('company_name')->get();
        $check_result = Permissions::checkPermission(33,2);
        if(!$check_result) {
            return redirect()->route('admin.dashboard')->with('error','Access Denied. You are not authorized to access that page.');
        }
        $this->data['consultant_detail']= Recruitment_consultant::where('id',$id)->get();
        if($this->data['consultant_detail']->count()==0){
            return redirect()->route('admin.recruitment_consultant')->with('error','Error Occurred. Try Again!');
        }
        return view('admin.recruitment_consultant.edit_consultant', $this->data);
    }
    
    public function update_consultant(Request $request) {
        
        $check_result = Permissions::checkPermission(33,2);
        if(!$check_result) {
            return redirect()->route('admin.dashboard')->with('error','Access Denied. You are not authorized to access that page.');
        }

        $validator = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'required',
                    'address' => 'required',
                    'specialty'=>'required',
                    'company_id'=>'required'
        ]);
        if ($validator->fails()) {
            return redirect()->route('admin.recruitment_consultant')->with('error', 'Please follow validation rules.');
        }
        
        $request_data=$request->all();
        
        $consultant_arr=[
            'name'=>$request_data['name'],
            'address'=>$request_data['address'],
            'email'=>$request_data['email'],
            'specialty'=>$request_data['specialty'],
            'company_id'=>$request_data['company_id'],
            'updated_at'=>date('Y-m-d H:i:s'),            
            'updated_ip'=>$request->ip(),
            'updated_by'=> \Illuminate\Support\Facades\Auth::user()->id,
            
        ];
        
        if(Recruitment_consultant::where('id',$request_data['id'])->update($consultant_arr)){
            return redirect()->route('admin.recruitment_consultant')->with('success','New recruitment consultant added successfully.');
        }
        else{
            return redirect()->route('admin.recruitment_consultant')->with('error','Error Occurred. Try Again!');
        }
    }
    
}
