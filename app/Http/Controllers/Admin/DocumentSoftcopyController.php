<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use App\Common_query;
use App\Companies;
use App\Department;
use App\User;
use App\Lib\Permissions;
use App\Document_softcopy_access;
use App\DocumentSoftcopyReck;
use App\DocumentSoftcopyFolder; 
use App\DocumentSoftcopy;
use App\DocumentSoftcopyFiles;
use App\DocumentSoftcopyCupboard;
use App\Inward_outwards;
use App\Lib\NotificationTask;

class DocumentSoftcopyController extends Controller
{
    public $data;
    private $module_id = 50;
    public $notification_task;

    public function __construct() {
        $this->data['module_title'] = "Document Hardcopy";
        $this->data['reck_module_link'] = "admin.hardcopy_reck";
        $this->data['folder_module_link'] = "admin.hardcopy_folder";
        $this->data['module_link'] = "admin.hardcopy";
        $this->notification_task = new NotificationTask();
    }

    //---------------------------------------------------------------- Cupboard -------------------------------------------//


    public function hardcopy_cupboard() {

        $check_result = Permissions::checkPermission(50, 5);
        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }

        $this->data['page_title'] = "Document Hard Copy Cupboard";
        $this->data['add_permission'] = true;
        $this->data['edit_permission'] = true;
        $this->data['delete_permission'] = true;
        return view('admin.document_softcopy.softcopy_cupboard', $this->data);
    }

    public function get_hardcopy_cupboard_list() {     //this changes
				
        $datatable_fields = array('company.company_name','department.dept_name', 'cupboard_number', 'description', 'document_softcopy_cupboard.status');
        $request = Input::all();
        $conditions_array = [];
        
        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'company';
        $join_str[0]['join_table_id'] = 'company.id';
        $join_str[0]['from_table_id'] = 'document_softcopy_cupboard.company_id';

        $join_str[1]['join_type'] = '';
        $join_str[1]['table'] = 'department';
        $join_str[1]['join_table_id'] = 'department.id';
        $join_str[1]['from_table_id'] = 'document_softcopy_cupboard.department_id';

        $getfiled = array('document_softcopy_cupboard.id','company.company_name','department.dept_name', 'cupboard_number', 'description', 'document_softcopy_cupboard.status');
        $table = "document_softcopy_cupboard";
		
        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);
        die();
    }
	    
    public function add_hardcopy_cupboard() {

        $check_result = Permissions::checkPermission(50, 3);
        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }
        $this->data['page_title'] = 'Add Document Hard Copy Cupboard';
        $this->data['company']= Companies::getCompany();
        return view('admin.document_softcopy.add_softcopy_cupboard', $this->data);
    }

    public function insert_hardcopy_cupboard(Request $request) {

        $validator_normal = Validator::make($request->all(), [
            'company_id' => 'required',
            'department_id' => 'required',
			'cupboard_number' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_softcopy_reck')->with('error', 'Please follow validation rules.');
        }
        
        /*$this->validate($request,[
            'reck_number' => 'required|unique:document_softcopy_reck,reck_number,:id',
        ]);*/

        $model = new DocumentSoftcopyCupboard();
        $model->company_id = $request->input('company_id');
        $model->department_id = $request->input('department_id');
        $model->cupboard_number = $request->input('cupboard_number');
        $model->description = $request->input('description');
		$model->created_at = date('Y-m-d h:i:s');
        $model->created_ip = $request->ip();
        $model->updated_at = date('Y-m-d h:i:s');
        $model->updated_ip = $request->ip();
        if ($model->save()) {
			return redirect()->route('admin.hardcopy_cupboard')->with('success', 'New Hard Copy Cupboard added successfully.');
        } else {
            return redirect()->route('admin.add_hardcopy_cupboard')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function edit_hardcopy_cupboard($id) {

        $check_result = Permissions::checkPermission(50, 2);
        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }
        $this->data['page_title'] = "Edit Document Hard Copy Cupboard";
        $this->data['softcopy_cupboard_detail'] = DocumentSoftcopyCupboard::where('id', $id)->first();        
        $this->data['company']= Companies::getCompany();
     	 if (empty($this->data['softcopy_cupboard_detail'])) {
            return redirect()->route('admin.softcopy_cupboard')->with('error', 'Error Occurred. Try Again!');
        }
        return view('admin.document_softcopy.edit_softcopy_cupboard', $this->data);
    }

    public function update_hardcopy_cupboard(Request $request) {
		
        $validator_normal = Validator::make($request->all(), [
            'company_id' => 'required',
            'department_id' => 'required',
			'cupboard_number' => 'required',
        ]);    
		
        if ($validator_normal->fails()) {
            return redirect()->route('admin.softcopy_cupboard')->with('error', 'Please follow validation rules.');
        }
        
        $model = [
            'company_id' => $request->input('company_id'),
            'department_id' => $request->input('department_id'),
            'cupboard_number' => $request->input('cupboard_number'),
            'description' => $request->input('description'),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
        ];
		
        DocumentSoftcopyCupboard::where('id', $request->input('id'))->update($model);
		
        return redirect()->route('admin.hardcopy_cupboard')->with('success', 'Hard Copy Cupboard successfully updated.');
    }

    public function change_hardcopy_cupboard_status($id, $status) {        
        if (DocumentSoftcopyCupboard::where('id', $id)->update(['status' => $status])) {
            return redirect()->route('admin.hardcopy_cupboard')->with('success', 'Status successfully updated.');
        }
        return redirect()->route('admin.hardcopy_cupboard')->with('error', 'Error during operation. Try again!');
    }

    public function check_cupboard_number(Request $request) {
        $validator = Validator::make($request->all(), [
                    'cupboard_number' => 'required'
        ]);
        if ($validator->fails()) {
            echo 'false';
            die();
        }

        if(empty($request->input('id')))
            $cupboard_number = DocumentSoftcopyCupboard::where('cupboard_number', $request->input('cupboard_number'))->where('department_id', $request->input('department_id'))->get('id')->count();
        else
            $cupboard_number = DocumentSoftcopyCupboard::where('id','!=',$request->input('id'))->where('cupboard_number', $request->input('cupboard_number'))->where('department_id', $request->input('department_id'))->get('id')->count();            

        if ($cupboard_number > 0) {
            echo 'false';
            die();
        } else {
            echo 'true';
            die();
        }
    }

    //---------------------------------------------------------------- Reck---------------------------------------//

    public function hardcopy_reck() {

        $check_result = Permissions::checkPermission(50, 5);
        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }

        $this->data['page_title'] = "Document Hard Copy Rack";
        $this->data['add_permission'] = true;
        $this->data['edit_permission'] = true;
        $this->data['delete_permission'] = true;
        return view('admin.document_softcopy.softcopy_reck', $this->data);
    }

    public function get_hardcopy_reck_list() {     //this changes
				
        $datatable_fields = array('company.company_name','department.dept_name','document_softcopy_cupboard.cupboard_number', 'reck_number', 'document_softcopy_reck.description', 'document_softcopy_reck.status');
        $request = Input::all();
        $conditions_array = [];
        
        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'company';
        $join_str[0]['join_table_id'] = 'company.id';
        $join_str[0]['from_table_id'] = 'document_softcopy_reck.company_id';

        $join_str[1]['join_type'] = '';
        $join_str[1]['table'] = 'department';
        $join_str[1]['join_table_id'] = 'department.id';
        $join_str[1]['from_table_id'] = 'document_softcopy_reck.department_id';

        $join_str[2]['join_type'] = '';
        $join_str[2]['table'] = 'document_softcopy_cupboard';
        $join_str[2]['join_table_id'] = 'document_softcopy_cupboard.id';
        $join_str[2]['from_table_id'] = 'document_softcopy_reck.document_softcopy_cupboard_id';

        $getfiled = array('document_softcopy_reck.id','company.company_name','department.dept_name','document_softcopy_cupboard.cupboard_number', 'reck_number', 'document_softcopy_reck.description', 'document_softcopy_reck.status');
        $table = "document_softcopy_reck";
		
        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);
        die();
    }
	    
    public function add_hardcopy_reck() {

        $check_result = Permissions::checkPermission(50, 3);
        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }
        $this->data['page_title'] = 'Add Document Hard Copy Rack';
        $this->data['company']= Companies::getCompany();
        return view('admin.document_softcopy.add_softcopy_reck', $this->data);
    }

    public function insert_hardcopy_reck(Request $request) {

        $validator_normal = Validator::make($request->all(), [
            'company_id' => 'required',
            'department_id' => 'required',
            'cupboard_id' => 'required',
			'reck_number' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_softcopy_reck')->with('error', 'Please follow validation rules.');
        }
        
        /*$this->validate($request,[
            'reck_number' => 'required|unique:document_softcopy_reck,reck_number,:id',
        ]);*/

        $model = new DocumentSoftcopyReck();
        $model->company_id = $request->input('company_id');
        $model->department_id = $request->input('department_id');
        $model->document_softcopy_cupboard_id = $request->input('cupboard_id');
        $model->reck_number = $request->input('reck_number');
        $model->description = $request->input('description');
		$model->created_at = date('Y-m-d h:i:s');
        $model->created_ip = $request->ip();
        $model->updated_at = date('Y-m-d h:i:s');
        $model->updated_ip = $request->ip();
        if ($model->save()) {
			return redirect()->route('admin.hardcopy_reck')->with('success', 'New Hard Copy Rack added successfully.');
        } else {
            return redirect()->route('admin.add_hardcopy_reck')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function edit_hardcopy_reck($id) {

        $check_result = Permissions::checkPermission(50, 2);
        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }
        $this->data['page_title'] = "Edit Document Hard Copy Rack";
        $this->data['softcopy_reck_detail'] = DocumentSoftcopyReck::where('id', $id)->first();        
        $this->data['company']= Companies::getCompany();
     	 if (empty($this->data['softcopy_reck_detail'])) {
            return redirect()->route('admin.softcopy_reck')->with('error', 'Error Occurred. Try Again!');
        }
        return view('admin.document_softcopy.edit_softcopy_reck', $this->data);
    }

    public function update_hardcopy_reck(Request $request) {
		
        $validator_normal = Validator::make($request->all(), [
            'company_id' => 'required',
            'department_id' => 'required',
            'reck_number' => 'required',
            'cupboard_id' => 'required'
        ]);    
		
        if ($validator_normal->fails()) {
            return redirect()->route('admin.softcopy_reck')->with('error', 'Please follow validation rules.');
        }
        
        $model = [
            'company_id' => $request->input('company_id'),
            'department_id' => $request->input('department_id'),
            'reck_number' => $request->input('reck_number'),
            'document_softcopy_cupboard_id	' => $request->input('cupboard_id'),
            'description' => $request->input('description'),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
        ];
		
        DocumentSoftcopyReck::where('id', $request->input('id'))->update($model);
		
        return redirect()->route('admin.hardcopy_reck')->with('success', 'Hard Copy Rack successfully updated.');
    }

    public function delete_hardcopy_reck($id) {
        if ($model = DocumentSoftcopyReck::findOrFail($id)) {
        	$model->delete();
			return redirect()->route('admin.hardcopy_reck')->with('success', 'Hard Copy Rack successfully delete.');
        }
        return redirect()->route('admin.hardcopy_reck')->with('error', 'Error during operation. Try again!');
    }

    public function change_hardcopy_reck_status($id, $status) {        
        if (DocumentSoftcopyReck::where('id', $id)->update(['status' => $status])) {
            return redirect()->route('admin.hardcopy_reck')->with('success', 'Status successfully updated.');
        }
        return redirect()->route('admin.hardcopy_reck')->with('error', 'Error during operation. Try again!');
    }

    public function check_reck_number(Request $request) {
        $validator = Validator::make($request->all(), [
                    'reck_number' => 'required'
        ]);
        if ($validator->fails()) {
            echo 'false';
            die();
        }

        if(empty($request->input('id')))
            $reck_number = DocumentSoftcopyReck::where('reck_number', $request->input('reck_number'))->where('document_softcopy_cupboard_id', $request->input('cupboard_id'))->get('id')->count();
        else
            $reck_number = DocumentSoftcopyReck::where('id','!=',$request->input('id'))->where('reck_number', $request->input('reck_number'))->where('document_softcopy_cupboard_id', $request->input('cupboard_id'))->get('id')->count();            

        if ($reck_number > 0) {
            echo 'false';
            die();
        } else {
            echo 'true';
            die();
        }
    }

    //-------------------------------------------------------- Folder-------------------------------------------//

    public function hardcopy_folder() {
        $check_result = Permissions::checkPermission(51, 5);
        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }
        $this->data['page_title'] = "Document Hard Copy File";
        //$this->data['add_permission']= Permissions::checkPermission(34,3);
        //$this->data['edit_permission']=Permissions::checkPermission(34,2);
        //$this->data['delete_permission']=Permissions::checkPermission(34,4);
        $this->data['add_permission'] = true;
        $this->data['edit_permission'] = true;
        $this->data['delete_permission'] = true;
        return view('admin.document_softcopy.softcopy_folder', $this->data);
    }

    public function get_hardcopy_folder_list() {     //this changes
				
        $datatable_fields = array('company.company_name','department.dept_name','document_softcopy_cupboard.cupboard_number', 'document_softcopy_reck.reck_number', 'file_number','file_name', 'document_softcopy_folder.description', 'document_softcopy_folder.status');
        $request = Input::all();
        $conditions_array = [];
        
        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'company';
        $join_str[0]['join_table_id'] = 'company.id';
        $join_str[0]['from_table_id'] = 'document_softcopy_folder.company_id';

        $join_str[1]['join_type'] = '';
        $join_str[1]['table'] = 'department';
        $join_str[1]['join_table_id'] = 'department.id';
        $join_str[1]['from_table_id'] = 'document_softcopy_folder.department_id';

        $join_str[2]['join_type'] = '';
        $join_str[2]['table'] = 'document_softcopy_reck';
        $join_str[2]['join_table_id'] = 'document_softcopy_reck.id';
        $join_str[2]['from_table_id'] = 'document_softcopy_folder.document_softcopy_reck_id';

        $join_str[3]['join_type'] = '';
        $join_str[3]['table'] = 'document_softcopy_cupboard';
        $join_str[3]['join_table_id'] = 'document_softcopy_cupboard.id';
        $join_str[3]['from_table_id'] = 'document_softcopy_folder.document_softcopy_cupboard_id';

        $getfiled = array('document_softcopy_folder.id','company.company_name','department.dept_name','document_softcopy_cupboard.cupboard_number', 'document_softcopy_reck.reck_number', 'file_number','file_name', 'document_softcopy_folder.description', 'document_softcopy_folder.status');
        $table = "document_softcopy_folder";
		
        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);
        die();
    }
	    
    public function add_hardcopy_folder() {
        $check_result = Permissions::checkPermission(51, 3);
        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }
        $this->data['page_title'] = 'Add Document Hard Copy File';        
        $this->data['company']= Companies::getCompany();
        return view('admin.document_softcopy.add_softcopy_folder', $this->data);
    }

    public function insert_hardcopy_folder(Request $request) {

        $validator_normal = Validator::make($request->all(), [
            'company_id' => 'required',
            'department_id' => 'required',
            'cupboard_id' => 'required',
            'document_softcopy_reck_id' => 'required',
            'file_number' => 'required',
            'file_name' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_softcopy_folder')->with('error', 'Please follow validation rules.');
        }
		
        $model = new DocumentSoftcopyFolder();
        $model->company_id = $request->input('company_id');
        $model->department_id = $request->input('department_id');
        $model->document_softcopy_cupboard_id = $request->input('cupboard_id');
        $model->document_softcopy_reck_id = $request->input('document_softcopy_reck_id');
        $model->file_number = $request->input('file_number');
        $model->file_name = $request->input('file_name');
        $model->description = $request->input('description');
		$model->created_at = date('Y-m-d h:i:s');
        $model->created_ip = $request->ip();
        $model->updated_at = date('Y-m-d h:i:s');
        $model->updated_ip = $request->ip();
        if ($model->save()) {
			return redirect()->route('admin.hardcopy_folder')->with('success', 'New Hard Copy File added successfully.');
        } else {
            return redirect()->route('admin.add_hardcopy_folder')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function edit_hardcopy_folder($id) {
        $check_result = Permissions::checkPermission(51, 2);
        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }
        $this->data['page_title'] = "Edit Docment Hard Copy File";
        $this->data['softcopy_folder_detail'] = DocumentSoftcopyfolder::where('id', $id)->first();        
        $this->data['company']= Companies::getCompany();
     	 if (empty($this->data['softcopy_folder_detail'])) {
            return redirect()->route('admin.softcopy_folder')->with('error', 'Error Occurred. Try Again!');
        }
        return view('admin.document_softcopy.edit_softcopy_folder', $this->data);
    }

    public function update_hardcopy_folder(Request $request) {
		
        $validator_normal = Validator::make($request->all(), [
            'company_id' => 'required',
            'department_id' => 'required',
            'cupboard_id' => 'required',
            'document_softcopy_reck_id' => 'required',
            'file_number' => 'required',
            'file_name' => 'required',
        ]);    
		
        if ($validator_normal->fails()) {
            return redirect()->route('admin.softcopy_folder')->with('error', 'Please follow validation rules.');
        }
        
        $model = [
            'company_id' => $request->input('company_id'),
            'department_id' => $request->input('department_id'),
            'document_softcopy_cupboard_id' => $request->input('cupboard_id'),
            'document_softcopy_reck_id' => $request->input('document_softcopy_reck_id'),
            'file_number' => $request->input('file_number'),
            'file_name' => $request->input('file_name'),
            'description' => $request->input('description'),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
        ];
		
        DocumentSoftcopyFolder::where('id', $request->input('id'))->update($model);
		
        return redirect()->route('admin.hardcopy_folder')->with('success', 'Hard Copy File successfully updated.');
    }

    public function delete_hardcopy_folder($id) {
        if ($model = DocumentSoftcopyFolder::findOrFail($id)) {
        	$model->delete();
			return redirect()->route('admin.hardcopy_folder')->with('success', 'Hard Copy File successfully delete.');
        }
        return redirect()->route('admin.hardcopy_folder')->with('error', 'Error during operation. Try again!');
    }

    public function change_hardcopy_folder_status($id, $status) {        
        if (DocumentSoftcopyFolder::where('id', $id)->update(['status' => $status])) {
            return redirect()->route('admin.hardcopy_folder')->with('success', 'Status successfully updated.');
        }
        return redirect()->route('admin.hardcopy_folder')->with('error', 'Error during operation. Try again!');
    }

    public function check_folder_number(Request $request) {
        $validator = Validator::make($request->all(), [
                    'file_number' => 'required'
        ]);
        if ($validator->fails()) {
            echo 'false';
            die();
        }

        if(empty($request->input('id')))
            $file_number = DocumentSoftcopyFolder::where('file_number', $request->input('file_number'))->where('document_softcopy_reck_id', $request->input('document_softcopy_reck_id'))->get('id')->count();
        else
            $file_number = DocumentSoftcopyFolder::where('id','!=',$request->input('id'))->where('file_number', $request->input('file_number'))->where('document_softcopy_reck_id', $request->input('document_softcopy_reck_id'))->get('id')->count();

        if ($file_number > 0) {
            echo 'false';
            die();
        } else {
            echo 'true';
            die();
        }
    }

    //-------------------------------------------------------- Softcopy Files---------------------------------------//

    public function hardcopy() {
        $check_result = Permissions::checkPermission(52, 5);
        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }
        $this->data['page_title'] = "Location Of Document";
        //$this->data['add_permission']= Permissions::checkPermission(34,3);
        //$this->data['edit_permission']=Permissions::checkPermission(34,2);
        //$this->data['delete_permission']=Permissions::checkPermission(34,4);        
        $this->data['add_permission'] = true;
        $this->data['edit_permission'] = true;
        $this->data['delete_permission'] = true;
        $this->data['file_permission'] = true;
        return view('admin.document_softcopy.softcopy', $this->data);
    }

    public function assignee_requests() {
        
        $this->data['page_title'] = "Assignee Requests";
        
        return view('admin.document_softcopy.assignee_requests', $this->data);
    }
    public function get_assignee_requests() {     //this changes
				
        $datatable_fields = array( 'company.company_name','department.dept_name','custodion.name' ,'assignee.name' ,
                 'document_softcopy_reck.reck_number', 'document_softcopy_folder.file_number',
                 'document_softcopy.start_page','document_softcopy_access.created_at','document_softcopy_access.assignee_status',
                 'document_softcopy_access.assignee_returnDate','document_softcopy_access.assignee_actual_returnDate',
                 'document_softcopy.type', 'document_softcopy.title', 'document_softcopy.status');
        $request = Input::all();
        $conditions_array = ['document_softcopy_access.assignee_id' => Auth::user()->id, 'document_softcopy_access.is_returnable' => 'Yes'];
        
        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'document_softcopy';
        $join_str[0]['join_table_id'] = 'document_softcopy.id';
        $join_str[0]['from_table_id'] = 'document_softcopy_access.document_softcopy_id';

        $join_str[1]['join_type'] = '';
        $join_str[1]['table'] = 'department';
        $join_str[1]['join_table_id'] = 'department.id';
        $join_str[1]['from_table_id'] = 'document_softcopy.department_id';

        $join_str[2]['join_type'] = '';
        $join_str[2]['table'] = 'document_softcopy_reck';
        $join_str[2]['join_table_id'] = 'document_softcopy_reck.id';
        $join_str[2]['from_table_id'] = 'document_softcopy.document_softcopy_reck_id';

        $join_str[3]['join_type'] = '';
        $join_str[3]['table'] = 'document_softcopy_folder';
        $join_str[3]['join_table_id'] = 'document_softcopy_folder.id';
        $join_str[3]['from_table_id'] = 'document_softcopy.document_softcopy_folder_id';

        $join_str[4]['join_type'] = '';
        $join_str[4]['table'] = 'users';
        $join_str[4]['join_table_id'] = 'users.id';
        $join_str[4]['from_table_id'] = 'document_softcopy_access.user_id';

        $join_str[5]['join_type'] = 'left';
        $join_str[5]['table'] = 'users AS custodion';
        $join_str[5]['join_table_id'] = 'custodion.id';
        $join_str[5]['from_table_id'] = 'document_softcopy.custodion_user_id';

        $join_str[6]['join_type'] = 'left';
        $join_str[6]['table'] = 'users AS assignee';
        $join_str[6]['join_table_id'] = 'assignee.id';
        $join_str[6]['from_table_id'] = 'document_softcopy_access.assignee_id';

        $join_str[7]['join_type'] = '';
        $join_str[7]['table'] = 'company';
        $join_str[7]['join_table_id'] = 'company.id';
        $join_str[7]['from_table_id'] = 'document_softcopy.company_id';


        $getfiled = array('document_softcopy_access.*','document_softcopy.start_page','document_softcopy.end_page', 'document_softcopy.type',
        'document_softcopy.title','users.name','company.company_name','department.dept_name','custodion.name as custodion_name' ,'assignee.name as assignee_name', 'document_softcopy_reck.reck_number', 'document_softcopy_folder.file_number');
        $table = "document_softcopy_access";
		
        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);
        die();
    }

    public function assignee_rejected($id, Request $request) {    //set 

        $update_arr = [
            'assignee_status'  => "Rejected",
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id
        ];
        Document_softcopy_access::where('id', $id)->update($update_arr);

        return redirect()->route('admin.assignee_requests')->with('success', 'Document Process successfully Rejected !.');
    }

    public function assignee_return_date(Request $request) {    //set  date
        $validator_normal = Validator::make($request->all(), [
                    'assignee_returnDate' => 'required',
                    'hardcopy_id' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.assignee_requests')->with('error', 'Please follow validation rules.');
        }
        $request_data = $request->all();

        $update_arr = [
            'assignee_status'  => "Accepted",
            'assignee_returnDate' => $request_data['assignee_returnDate'],
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id
        ];
        Document_softcopy_access::where('id', $request_data['hardcopy_id'])->update($update_arr);

        return redirect()->route('admin.assignee_requests')->with('success', 'Return date set successfully.');
    }

    public function assignee_completed($id, Request $request) {    //set 

        $update_arr = [
            'assignee_actual_returnDate' => date('Y-m-d H:i:s'),
            'assignee_status'  => 'Completed',
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id
        ];
        Document_softcopy_access::where('id', $id)->update($update_arr);

        return redirect()->route('admin.assignee_requests')->with('success', 'Document Process successfully Completed !.');
    }

    public function get_hardcopy_list() {     //this changes
				
        $datatable_fields = array('users.name', 'company.company_name','department.dept_name','document_softcopy_cupboard.cupboard_number',
        'custodion.name' ,'assignee.name','document_softcopy_reck.reck_number', 
        'document_softcopy_folder.file_number','document_softcopy.start_page', 'document_softcopy.type',
         'document_softcopy.title', 'document_softcopy.status','document_softcopy_access.assignee_status');
        
         $request = Input::all();
        $conditions_array = [];


        // $join_str[0]['join_type'] = '';
        // $join_str[0]['table'] = 'document_softcopy_access';
        // $join_str[0]['join_table_id'] = 'document_softcopy_access.document_softcopy_id';
        // $join_str[0]['from_table_id'] = 'document_softcopy.id';

        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'company';
        $join_str[0]['join_table_id'] = 'company.id';
        $join_str[0]['from_table_id'] = 'document_softcopy.company_id';

        $join_str[2]['join_type'] = '';
        $join_str[1]['table'] = 'department';
        $join_str[1]['join_table_id'] = 'department.id';
        $join_str[1]['from_table_id'] = 'document_softcopy.department_id';

        $join_str[2]['join_type'] = '';
        $join_str[2]['table'] = 'document_softcopy_reck';
        $join_str[2]['join_table_id'] = 'document_softcopy_reck.id';
        $join_str[2]['from_table_id'] = 'document_softcopy.document_softcopy_reck_id';

        $join_str[3]['join_type'] = '';
        $join_str[3]['table'] = 'document_softcopy_folder';
        $join_str[3]['join_table_id'] = 'document_softcopy_folder.id';
        $join_str[3]['from_table_id'] = 'document_softcopy.document_softcopy_folder_id';

        $join_str[4]['join_type'] = '';
        $join_str[4]['table'] = 'users';
        $join_str[4]['join_table_id'] = 'users.id';
        $join_str[4]['from_table_id'] = 'document_softcopy.user_id';

        $join_str[5]['join_type'] = 'left';
        $join_str[5]['table'] = 'users AS custodion';
        $join_str[5]['join_table_id'] = 'custodion.id';
        $join_str[5]['from_table_id'] = 'document_softcopy.custodion_user_id';

        $join_str[6]['join_type'] = 'left';
        $join_str[6]['table'] = 'users AS assignee';
        $join_str[6]['join_table_id'] = 'assignee.id';
        $join_str[6]['from_table_id'] = 'document_softcopy_access.assignee_id';

        $join_str[7]['join_type'] = '';
        $join_str[7]['table'] = 'document_softcopy_cupboard';
        $join_str[7]['join_table_id'] = 'document_softcopy_cupboard.id';
        $join_str[7]['from_table_id'] = 'document_softcopy.document_softcopy_cupboard_id';


        $getfiled = array('document_softcopy.id','users.name','company.company_name','department.dept_name','document_softcopy_cupboard.cupboard_number',
        'custodion.name as custodion_name'  ,'assignee.name as assignee_name', 
        'document_softcopy_reck.reck_number', 'document_softcopy_folder.file_number','document_softcopy_folder.file_name',
        'document_softcopy.start_page','document_softcopy.end_page', 'document_softcopy.type',
         'document_softcopy.title', 'document_softcopy.status','document_softcopy_access.assignee_status');
        $table = "document_softcopy";
		
        echo DocumentSoftcopy::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);
        die();
    }
	    
    public function add_hardcopy() {
        $check_result = Permissions::checkPermission(52, 3);
        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }
        $this->data['page_title'] = 'Add Document Hard Copy';
        $this->data['company']= Companies::getCompany();
        $this->data['users_list']= User::orderBy('name')->where("status", "Enabled")->get()->pluck('name', 'id');
        $this->data['registry_list'] = Inward_outwards::where(['assign_employee_id' => Auth::user()->id])->get(['inward_outward_title','inward_outward_no', 'id']);
        return view('admin.document_softcopy.add_softcopy', $this->data);
    }

    public function insert_hardcopy(Request $request) {

        $validator_normal = Validator::make($request->all(), [
            'company_id' => 'required',
            'department_id' => 'required',
            'document_softcopy_reck_id' => 'required',
            'document_softcopy_folder_id' => 'required',
            'type' => 'required',
            'start_page' => 'required',
            'end_page' => 'required',
            'custodion_user_id' => 'required',
            'cupboard_id' => 'required',
            
            'assignee_id' => 'required',
            'is_returnable' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_hardcopy')->with('error', 'Please follow validation rules.');
        }
        
        $model = new DocumentSoftcopy();
        $model->company_id = $request->input('company_id');
        $model->start_page = $request->input('start_page');
        $model->end_page = $request->input('end_page');
        $model->document_softcopy_cupboard_id = $request->input('cupboard_id');



        $model->custodion_user_id = $request->input('custodion_user_id');
        $model->department_id = $request->input('department_id');   //this
        $model->document_softcopy_reck_id = $request->input('document_softcopy_reck_id');
        $model->document_softcopy_folder_id = $request->input('document_softcopy_folder_id');
        $model->type = $request->input('type');        
        $model->title = $request->input('title');
        $model->description = $request->input('description');
        if($model->type === 'Registry')
            $model->inward_outward_id = $request->input('inward_outward_id');
        $model->user_id	= Auth::user()->id;
		$model->created_at = date('Y-m-d h:i:s');
        $model->created_ip = $request->ip();
        $model->updated_at = date('Y-m-d h:i:s');
        $model->updated_ip = $request->ip();
        if ($model->save()) {
            if ($request->input('type')) {
                Inward_outwards::where('id',$request->input('inward_outward_id'))->update(['is_document_linked'=> 'Yes']);
            }
            if($model->type === 'General'){
                $softcopyFile = $request->file('softcopy_file');                
                if($softcopyFile) {
                    foreach ($softcopyFile as $file) {
                        $original_file_name = explode('.', $file->getClientOriginalName());
                        $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);
                        $file_path = $file->storeAs('public/softcopy_file', $new_file_name);
                        if(!empty($file_path)){
                            $fileModel = new DocumentSoftcopyFiles();
                            $fileModel->document_softcopy_id = $model->id;
                            $fileModel->file = $file_path;
                            $fileModel->created_at = date('Y-m-d h:i:s');
                            $fileModel->created_ip = $request->ip();
                            $fileModel->updated_at = date('Y-m-d h:i:s');
                            $fileModel->updated_ip = $request->ip();
                            $fileModel->save();
                        }
                    }                    
                }
            }  

            //Access Table
            $access = new Document_softcopy_access();
            $access->document_softcopy_id = $model->id;
            $access->user_id = Auth::user()->id;
            $access->assignee_id = $request->input('assignee_id');
            $access->is_returnable = $request->input('is_returnable');
            if ($request->input('is_returnable') == "Yes") {
                $access->assignee_status = 'Assigned';
            }else{
                $access->assignee_status = 'Accepted';
            }
            $access->created_at = date('Y-m-d h:i:s');
            $access->created_ip = $request->ip();
            $access->updated_at = date('Y-m-d h:i:s');
            $access->updated_ip = $request->ip();
            $access->save();
    
            $this->notification_task->hardCopyAssigneeNotify([$request->input('assignee_id')]);

			return redirect()->route('admin.hardcopy')->with('success', 'New Hard Copy added successfully.');
        } else {
            return redirect()->route('admin.add_hardcopy')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function edit_hardcopy($id) {
        $check_result = Permissions::checkPermission(52, 2);
        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }
        $this->data['page_title'] = "Edit Document Hard Copy";
        
        $this->data['softcopy_detail'] = DocumentSoftcopy::select('document_softcopy.*','document_softcopy_access.id as access_id','document_softcopy_access.assignee_id','document_softcopy_access.is_returnable','document_softcopy_access.assignee_status')
           ->where('document_softcopy.id', $id)
            //->join('document_softcopy_access', 'document_softcopy.id', '=' ,'document_softcopy_access.document_softcopy_id')
           // ->orderBy('document_softcopy_access.document_softcopy_id', 'DESC')
           ->leftJoin('document_softcopy_access', function($query) 
           {
              $query->on('document_softcopy.id','=','document_softcopy_access.document_softcopy_id')
              ->whereRaw('document_softcopy_access.id IN (select MAX(a2.id) from document_softcopy_access as a2 join document_softcopy as u2 on u2.id = a2.document_softcopy_id group by u2.id)');
           })
           ->first();   
            
        
        $this->data['company']= Companies::getCompany();
        $this->data['users_list']= User::where("status", "Enabled")->get()->pluck('name', 'id');
        $this->data['registry_list'] = Inward_outwards::where(['assign_employee_id' => Auth::user()->id])->get(['inward_outward_title','inward_outward_no', 'id']);
     	 if (empty($this->data['softcopy_detail'])) {
            return redirect()->route('admin.softcopy')->with('error', 'Error Occurred. Try Again!');
        }
        return view('admin.document_softcopy.edit_softcopy', $this->data);
    }

    public function update_hardcopy(Request $request) {
		
        $validator_normal = Validator::make($request->all(), [
            'company_id' => 'required',
            'department_id' => 'required',
            'document_softcopy_reck_id' => 'required',
            'document_softcopy_folder_id' => 'required',
            'type' => 'required',
            'title' => 'required',
            'start_page' => 'required',
            'end_page' => 'required',
            'custodion_user_id' => 'required',
            'assignee_id' => 'required',
            'is_returnable' => 'required',
            'cupboard_id' => 'required'
        ]);    
		
        if ($validator_normal->fails()) {
            return redirect()->route('admin.hardcopy')->with('error', 'Please follow validation rules.');
        }

        $model = [
            'company_id' => $request->input('company_id'),
            'start_page' => $request->input('start_page'),
            'end_page' => $request->input('end_page'),

            'document_softcopy_cupboard_id' => $request->input('cupboard_id'),
            'custodion_user_id' => $request->input('custodion_user_id'),
            'department_id' => $request->input('department_id'),
            'document_softcopy_reck_id' => $request->input('document_softcopy_reck_id'),
            'document_softcopy_folder_id' => $request->input('document_softcopy_folder_id'),
            'type' => $request->input('type'),
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'user_id' => Auth::user()->id,
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
        ];
        //   if ($request->input('is_returnable') == "Yes") {
        //     $model['assignee_status'] = 'Pending'; 
        // }else{
        //     $model['assignee_status'] = NULL;
        // }

        if($request->input('type') === 'Registry') {
            $model['inward_outward_id'] = $request->input('inward_outward_id');
            Inward_outwards::where('id',$request->input('inward_outward_id'))->update(['is_document_linked'=> 'Yes']);   
        } else {
            Inward_outwards::where('id',$request->input('inward_outward_id'))->update(['is_document_linked'=> 'No']); 
        }            
		
        DocumentSoftcopy::where('id', $request->input('id'))->update($model);

        $this->notification_task->hardCopyAssigneeNotify([$request->input('assignee_id')]);
        
        DocumentSoftcopy::deleteRelatedFiles($request->input('id'));

        if($request->input('type') === 'General'){
            $softcopyFile = $request->file('softcopy_file');                
            if($softcopyFile) {
                foreach ($softcopyFile as $file) {
                    $original_file_name = explode('.', $file->getClientOriginalName());
                    $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);
                    $file_path = $file->storeAs('public/softcopy_file', $new_file_name);
                    if(!empty($file_path)){
                        $fileModel = new DocumentSoftcopyFiles();
                        $fileModel->document_softcopy_id = $request->input('id');
                        $fileModel->file = $file_path;
                        $fileModel->created_at = date('Y-m-d h:i:s');
                        $fileModel->created_ip = $request->ip();
                        $fileModel->updated_at = date('Y-m-d h:i:s');
                        $fileModel->updated_ip = $request->ip();
                        $fileModel->save();
                    }
                }                    
            }
        }
        
        $checkVal = Document_softcopy_access::where('id',$request->input('access_id'))->get(['is_returnable','assignee_status']);

        if ($checkVal[0]->is_returnable == 'Yes'  &&  $checkVal[0]->assignee_status == 'Accepted') {
           
            return redirect()->route('admin.hardcopy')->with('success', 'Hard Copy  successfully updated.');
        
        }  else {

                if ($checkVal[0]->assignee_status != 'Rejected' && $checkVal[0]->assignee_status != 'Completed') {
                        Document_softcopy_access::where('id',$request->input('access_id'))->delete();
                }
                    //Access Table
                    $access = new Document_softcopy_access();
                    $access->document_softcopy_id = $request->input('id');
                    $access->user_id = Auth::user()->id;
                    $access->assignee_id = $request->input('assignee_id');
                    $access->is_returnable = $request->input('is_returnable');
                    if ($request->input('is_returnable') == "Yes") {
                    $access->assignee_status = 'Assigned';
                    }else{
                    $access->assignee_status = 'Accepted';
                    }
                    $access->created_at = date('Y-m-d h:i:s');
                    $access->created_ip = $request->ip();
                    $access->updated_at = date('Y-m-d h:i:s');
                    $access->updated_ip = $request->ip();
                    $access->save();                 
        }
		
        return redirect()->route('admin.hardcopy')->with('success', 'Hard Copy  successfully updated.');
    }

    public function delete_hardcopy($id) {
        if ($model = DocumentSoftcopy::findOrFail($id)) {
            $model->delete();
            DocumentSoftcopy::deleteRelatedFiles($id);
			return redirect()->route('admin.hardcopy')->with('success', 'Hard Copy successfully delete.');
        }
        return redirect()->route('admin.hardcopy')->with('error', 'Error during operation. Try again!');
    }

    public function change_hardcopy_status($id, $status) {        
        if (DocumentSoftcopy::where('id', $id)->update(['status' => $status])) {
            return redirect()->route('admin.hardcopy')->with('success', 'Status successfully updated.');
        }
        return redirect()->route('admin.hardcopy')->with('error', 'Error during operation. Try again!');
    }

    public function get_hardcopy_file($id) {
        $softcopyDetails = DocumentSoftcopy::select(['id','inward_outward_id'])->where('id', '=', $id)
        ->with(['files' => function($query){ return $query->select(['id','document_softcopy_id','file']); },'ward'])
        //->with(['ward' => function($query){ return $query->select(['id','inward_outward_id','document_file']); }])
        ->first()->toArray();

        $softcopyFiles = [];
        if(!empty($softcopyDetails['files'])){
            foreach ($softcopyDetails['files'] as $key => $value) {
                $file_name = explode('/', $value['file']);                
                $softcopyFiles[$key]['file_name'] = end($file_name);
                $softcopyFiles[$key]['file_path'] = $value['file'];
            }
        }else if(!empty($softcopyDetails['ward'])){
            foreach ($softcopyDetails['ward'] as $key => $value) {
                $file_name = explode('/', $value['document_file']);
                $softcopyFiles[$key]['file_name'] = end($file_name);
                $softcopyFiles[$key]['file_path'] = $value['document_file'];
            }
        }
       
        return view('admin.document_softcopy.view_softcopy', ['softcopyDetails' => $softcopyFiles]);
    }

    public function get_department() {
        if (!empty($_GET['company_id'])) {
            $company_id = $_GET['company_id'];

            $departmentModal = Department::orderBy('dept_name')->where(['company_id' => $company_id])->pluck('dept_name', 'id');
            
            $html = "<option value=''>Select Department</option>";
            foreach ($departmentModal as $key => $value) {
                $html .= "<option value=" . $key . ">" . $value . "</option>";
            }
            echo $html;
            die();
        }
    }

    public function get_hardcopy_cupboard() {
        if (!empty($_GET['department_id'])) {
            $department_id = $_GET['department_id'];

            $cupboardModal = DocumentSoftcopyCupboard::where(['department_id' => $department_id])->where(['status' => 'Enabled'])->pluck('cupboard_number', 'id');
            
            $html = "<option value=''>Select Cupboard</option>";
            foreach ($cupboardModal as $key => $value) {
                $html .= "<option value=" . $key . ">" . $value . "</option>";
            }
            echo $html;
            die();
        }
    }

    public function get_hardcopy_reck() {
        if (!empty($_GET['cupboard_id'])) {
            $cupboard_id = $_GET['cupboard_id'];

            $reckModal = DocumentSoftcopyReck::orderBy('reck_number')->where(['document_softcopy_cupboard_id' => $cupboard_id])->where(['status' => 'Enabled'])->pluck('reck_number', 'id');
            
            $html = "<option value=''>Select Reck</option>";
            foreach ($reckModal as $key => $value) {
                $html .= "<option value=" . $key . ">" . $value . "</option>";
            }
            echo $html;
            die();
        }
    }

    public function get_hardcopy_folder() {
        if (!empty($_GET['document_softcopy_reck_id'])) {
            $document_softcopy_reck_id = $_GET['document_softcopy_reck_id'];

            $folderModal = DocumentSoftcopyFolder::orderBy('file_name')->where(['document_softcopy_reck_id' => $document_softcopy_reck_id])->where(['status' => 'Enabled'])->get(['file_number','file_name', 'id']);
            
            $html = "<option value=''>Select Folder</option>";
            foreach ($folderModal as $key => $value) {
                $html .= "<option value=" . $value->id . ">" . $value->file_name . '(' . $value->file_number . ')' . "</option>";
            }
            echo $html;
            die();
        }
    }

    public function get_inward_outward() {    //09/05/2020
        //if (!empty($_GET['company_id'])) {
            $company_id = $_GET['company_id'];

            $wardModal = Inward_outwards::where(['assign_employee_id' => Auth::user()->id])->where('is_document_linked','No')->where('type','Inwards')->get(['inward_outward_title','inward_outward_no', 'id']);
            
            $html = "<option value=''>Select Inward-Outward</option>";
            foreach ($wardModal as $key => $value) {
                $html .= "<option value=" . $value->id . ">" . $value->inward_outward_title . ' - ' . $value->inward_outward_no . "</option>";
            }
            echo $html;
            die();
        //}
    }

    public function get_inward_outward_edit(Request $request) {    //09/05/2020

            $id = $request->id;

            $wardModal = Inward_outwards::where(['assign_employee_id' => Auth::user()->id])
            ->where('is_document_linked','No')
            ->where('type','Inwards')
            ->orWhere('id', $id)
            ->get(['inward_outward_title','inward_outward_no', 'id']);
            
            $html = "<option value=''>Select Inward-Outward</option>";
            foreach ($wardModal as $key => $value) {
                $html .= "<option value=" . $value->id . ">" . $value->inward_outward_title . ' - ' . $value->inward_outward_no . "</option>";
            }
            echo $html;
            die();
    }

    public function get_pdf_page_no(Request $request) {
        $validator_normal = Validator::make($request->all(), ['registry_id' => 'required']);

        if ($validator_normal->fails()) {
            return response()->json(['status' => false]);
        }
                
                $request_data = $request->all();
                $id = $request->id;
                $registry_id = $request->registry_id;
                $company_id = $request->company_id;
                $department_id = $request->department_id;
                $document_softcopy_cupboard_id = $request->cupboard_id;
                $document_softcopy_reck_id = $request->document_softcopy_reck_id;
                $document_softcopy_folder_id = $request->document_softcopy_folder_id;

            $get_last_pageNO = DocumentSoftcopy::where('company_id',$company_id)
                 ->where('department_id',$department_id)
                 ->where('document_softcopy_cupboard_id',$document_softcopy_cupboard_id)
                 ->where('document_softcopy_reck_id',$document_softcopy_reck_id)
                 ->where('document_softcopy_folder_id',$document_softcopy_folder_id)
                 ->where(function ($query) use ($request_data) {
                    if (isset($request_data['id'])) {
                        $query->where('id','!=',$request_data['id']);
                    }
                })
                 //->where('type','Registry')
                 ->orderBy('id', 'desc')
                 ->value('end_page');


            $page_count = Inward_outwards::where(['id' => $registry_id])->value('pdf_page_no');
            
            $q_startNO = !empty($get_last_pageNO) ? $get_last_pageNO : 0;
            $html = [];
            $html['start'] = $start_page =  $q_startNO + 1;
            $html['end'] = $start_page + $page_count - 1;

            return response()->json($html);
        
    }

    public function get_last_page_no(Request $request) {
           

                $request_data = $request->all();
                $id = $request->id;
                $company_id = $request->company_id;
                $department_id = $request->department_id;
                $document_softcopy_cupboard_id = $request->cupboard_id;
                $document_softcopy_reck_id = $request->document_softcopy_reck_id;
                $document_softcopy_folder_id = $request->document_softcopy_folder_id;

            $get_last_pageNO = DocumentSoftcopy::where('company_id',$company_id)
                 ->where('department_id',$department_id)->orderBy('dept_name')
                 ->where('document_softcopy_cupboard_id',$document_softcopy_cupboard_id)
                 ->where('document_softcopy_reck_id',$document_softcopy_reck_id)
                 ->where('document_softcopy_folder_id',$document_softcopy_folder_id)
                 ->where(function ($query) use ($request_data) {
                    if (isset($request_data['id'])) {
                        $query->where('id','!=',$request_data['id']);
                    }
                })
                 //->where('type','General')
                 ->orderBy('id', 'desc')
                 ->value('end_page');
            $q_lastNO = !empty($get_last_pageNO) ? $get_last_pageNO : 0;
            $html = [];
            $html['last_page_no'] = $q_lastNO;
            return response()->json($html);
        
    }

    public function get_inward_details(Request $request)  //ajax call new
    {
        $validator_normal = Validator::make($request->all(), ['registry_id' => 'required']);
        if ($validator_normal->fails()) {
            return response()->json(['status' => false]);
        }
        $respose_data = [];
        $get_data = Inward_outwards::where('id',$request->registry_id)->get(['inward_outward_title','description']);
        return response()->json($get_data);

    }

}
