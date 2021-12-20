<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Common_query;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Imports\BankTransactionImport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Config;
use App\User;
use App\Holiday;
use App\Email_format;
use App\Mail\Mails;
use Illuminate\Support\Facades\Mail;

use App\Lib\Permissions;

class HolidayController extends Controller
{
    public $data;

    public function __construct() {
	$this->data['module_title'] = "Holiday";
        $this->data['module_link'] = "admin.holiday";
    }

    public function index() {
        $this->data['page_title'] = "Holiday";  
        
        $this->data['add_permission']= Permissions::checkPermission(34,3);
        $this->data['edit_permission']=Permissions::checkPermission(34,2);
        $this->data['delete_permission']=Permissions::checkPermission(34,4);
        
        
        
        return view('admin.holiday.index', $this->data);
    }

    public function get_holiday_list() {     //this changes
				
        $datatable_fields = array('title', 'start_date','is_optional', 'status');
        $request = Input::all();
        $conditions_array = [['start_date','>=', date('Y')]];
		
		$join_str=[];
		
        $getfiled = array('id','title', 'start_date', 'end_date', 'year', 'status', 'is_optional');
        $table = "holiday";
		
        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);
                                                  
        die();
    }
	    
    public function add_holiday() {
        $this->data['page_title'] = 'Add Holiday';		
		$this->data['user']= User::getUser();
        $this->data['year_list']= Config::get('app.year');
        return view('admin.holiday.add_holiday', $this->data);
    }

    public function insert_holiday(Request $request) {

        $validator_normal = Validator::make($request->all(), [
            'title' => 'required',
			'start_date' => 'required',
            'end_date' => 'required',
            'year' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_holiday')->with('error', 'Please follow validation rules.');
        }
		
        $annModel = new Holiday();
        $annModel->title = $request->input('title');
        $annModel->start_date = $request->input('start_date');
        $annModel->end_date = $request->input('end_date');
		$annModel->year = $request->input('year');
        if(!empty($request->input('is_optional')))
		  $annModel->is_optional = 2; // Yes
		$annModel->created_at = date('Y-m-d h:i:s');
        $annModel->created_ip = $request->ip();
        $annModel->updated_at = date('Y-m-d h:i:s');
        $annModel->updated_ip = $request->ip();        			
		
        if ($annModel->save()) {
			return redirect()->route('admin.holiday')->with('success', 'New holiday added successfully.');
        } else {
            return redirect()->route('admin.add_holiday')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function edit_holiday($id) {
		
        $this->data['page_title'] = "Edit Holiday";
        $this->data['holiday_detail'] = Holiday::where('id', $id)->get();		
		$this->data['user']= User::getUser();
        $this->data['year_list']= Config::get('app.year');
     	 if ($this->data['holiday_detail']->count() == 0) {
            return redirect()->route('admin.holiday')->with('error', 'Error Occurred. Try Again!');
        }
        return view('admin.holiday.edit_holiday', $this->data);
    }

    public function update_holiday(Request $request) {
		
        $validator_normal = Validator::make($request->all(), [
            'title' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'year' => 'required',
        ]);    
		
        if ($validator_normal->fails()) {
            return redirect()->route('admin.holiday')->with('error', 'Please follow validation rules.');
        }
        
        $annModel = [
            'title' => $request->input('title'),
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
			'year' => $request->input('year'),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
        ];
		
        if(!empty($request->input('is_optional')))
            $annModel['is_optional'] = 2; // Yes
        else
            $annModel['is_optional'] = 1; // No

        Holiday::where('id', $request->input('id'))->update($annModel);
		
        return redirect()->route('admin.holiday')->with('success', 'Holiday successfully updated.');
    }

    public function delete_holiday($id) {		
        if ($annModel = Holiday::findOrFail($id)) {
        	$annModel->delete();
			return redirect()->route('admin.holiday')->with('success', 'Holiday successfully delete.');
        }
        return redirect()->route('admin.holiday')->with('error', 'Error during operation. Try again!');
    }

    public function change_holiday_status($id, $status) {        
        if (Holiday::where('id', $id)->update(['status' => $status])) {
            return redirect()->route('admin.holiday')->with('success', 'Status successfully updated.');
        }
        return redirect()->route('admin.holiday')->with('error', 'Error during operation. Try again!');
    }

}
