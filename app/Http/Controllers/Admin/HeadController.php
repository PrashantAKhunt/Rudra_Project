<?php

namespace App\Http\Controllers\Admin; 
use Illuminate\Http\Request;
use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\Input;
use App\Common_query; 
use Illuminate\Support\Facades\Validator;
use App\Heads;
class HeadController extends Controller
{
    public $data;

    public function __construct() {
        $this->data['module_title'] = "Heads";
        $this->data['module_link'] = "admin.heads";
    }

    public function index() {
        $this->data['page_title'] = "Heads"; 
        return view('admin.head.index', $this->data);
    } 

    public function get_head_list() {
        $datatable_fields = array('head.id','head.head_name','head.head_detail','head.status','head.created_at');
        $request = Input::all();
        $conditions_array = []; 
        $getfiled =array('head.id','head.head_name','head.head_detail','head.status','head.created_at');
        $table = "head";
        $join_str=[]; 
        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);                                  
        die();
    }
    public function change_head_status($id, $status) {
        if (Heads::where('id', $id)->update(['status' => $status])) {
            return redirect()->route('admin.heads')->with('success', 'Status successfully updated.');
        }
        return redirect()->route('admin.heads')->with('error', 'Error during operation. Try again!');
    }

    public function add_head() {
        $this->data['page_title'] = 'Add head';
        return view('admin.head.add_head', $this->data);
    }

    public function insert_head(Request $request) {
        $validator_normal = Validator::make($request->all(), [
            'head_name' => 'required',
            'head_detail' => 'required'
        ]);

        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_head')->with('error', 'Please follow validation rules.');
        }
      
        $headModel = new Heads();
        $headModel->head_name = $request->input('head_name');
        $headModel->head_detail = $request->input('head_detail');
        $headModel->created_at = date('Y-m-d h:i:s');
        $headModel->created_ip = $request->ip();
        $headModel->updated_at = date('Y-m-d h:i:s');
        $headModel->updated_ip = $request->ip();
        
        if ($headModel->save()) {
            return redirect()->route('admin.heads')->with('success', 'New head added successfully.');
        } else {
            return redirect()->route('admin.add_head')->with('error', 'Error occurred in insert. Try Again!');
        }
    }

    public function edit_head($id) {
        $this->data['page_title'] = "Edit head";
        $this->data['head_detail'] = Heads::where('head.id', $id)->get();
        if ($this->data['head_detail']->count() == 0) {
            return redirect()->route('admin.heads')->with('error', 'Error Occurred. Try Again!');
        }
        return view('admin.head.edit_head', $this->data);
    }

    public function update_head(Request $request) {
        $validator_normal = Validator::make($request->all(), [
            'head_name' => 'required',
            'head_detail' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.heads')->with('error', 'Please follow validation rules.');
        } 
        $head_id = $request->input('id'); 
        $head_arr = [
            'head_name' => $request->input('head_name'),
            'head_detail' => $request->input('head_detail'),
            'created_at' => date('Y-m-d h:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
        ];
        
        Heads::where('id', $head_id)->update($head_arr); 
        return redirect()->route('admin.heads')->with('success', 'Head successfully updated.');
    }
}
