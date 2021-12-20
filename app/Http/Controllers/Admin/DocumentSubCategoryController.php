<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use App\Inward_outward_chat;
use App\Inward_outward_doc_category;
use App\Inward_outward_users;
use App\Inward_outwards;
use App\Inward_outward_views;
use DB;
use SebastianBergmann\Environment\Console;
use App\Department;
use App\Employees;
use App\User;
use App\Companies;
use App\Inward_outward_message_view;
use App\Projects;
use Illuminate\Support\Facades\Response;
use PhpParser\Node\Expr\BinaryOp\Concat;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;
use App\Lib\CommonTask;
use App\Lib\NotificationTask;
use App\Lib\Permissions;
use App\Inward_outward_doc_sub_category;

class DocumentSubCategoryController extends Controller {

    public $data;
    private $common_task;
    private $notification_task;
    private $module_id = 68;

    public function __construct() {
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();
    }

    public function document_sub_category_list() {
        $this->data['page_title'] = "Document Sub Category";
        
        $this->data['category_list'] = Inward_outward_doc_category::where('status','Enabled')->orderBy('category_name')->get();
        $this->data['view_special_permission'] = Permissions::checkSpecialPermission($this->module_id);
        $sub_category_list = Inward_outward_doc_sub_category::select('inward_outward_doc_category.category_name','inward_outward_doc_sub_category.*')
                                                            //->where('inward_outward_doc_category.status','Enabled')
                                                            ->where('inward_outward_doc_sub_category.is_approved',1)
                                                            ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outward_doc_sub_category.category_id')
                                                            ->get();

        return view('admin.document_sub_category.document_sub_category_list', $this->data, ['sub_category_list' => $sub_category_list]);
    }

    public function change_doc_sub_cat_status(Request $request, $id, $status) {
        $update_doc_arr = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip()
        ];

        try {
            Inward_outward_doc_sub_category::where('id', $id)->update($update_doc_arr);
            return redirect()->route('admin.document_sub_category')->with('success', 'Status successfully updated.');
        } catch (Exception $exc) {

            return redirect()->route('admin.document_sub_category')->with('error', 'Error Occurred. Try Again!');
        }
    }

    public function add_document_sub_categoery(Request $request) {
        $rules = array(
            'sub_category_name' => 'required',
            'category_id'       => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return redirect()->route('admin.document_sub_category')
                            ->with('error', 'Error during operation. Try again!');
        }

        $category_arr = [
            'user_id' => Auth::user()->id,
            'sub_category_name' => $request->input('sub_category_name'),
            'category_id' => $request->input('category_id'),
            'created_at' => date('Y-m-d H:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip()
        ];
        if (Auth::user()->role != config('constants.SuperUser')) {
            $category_arr['is_approved'] = 0;
            $category_arr['status'] = 'Disabled';
        } else {
            $category_arr['is_approved'] = 1;
            $category_arr['status'] = 'Enabled';
        }

        Inward_outward_doc_sub_category::insert($category_arr);
        $module = 'Registry Document Sub-category';
        $this->notification_task->entryApprovalNotify($module);

        //Inward_outwards::where('id', $new_id)->update($new_inward_arr);

        return redirect()->route('admin.document_sub_category')->with('success', 'New Sub Category inserted successfully.');
    }

    public function edit_document_sub_categoery($id) {
        //$document = Inward_outward_doc_sub_category::where('id', $id)->first();

        $document = Inward_outward_doc_sub_category::select('inward_outward_doc_category.category_name','inward_outward_doc_category.id as document_category_id','inward_outward_doc_sub_category.*')
                                                            ->where('inward_outward_doc_category.status','Enabled')
                                                            ->where('inward_outward_doc_sub_category.id', $id)
                                                            ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outward_doc_sub_category.category_id')
                                                            ->first();

        $document_array = array(
            'category_name' => $document->category_name,
            'category_id' => $document->document_category_id,
            'sub_category_id' => $document->id,
            'sub_category_name' => $document->sub_category_name,
        );
        
        $this->data['category_list'] = Inward_outward_doc_category::where('status','Enabled')->orderBy('category_name')->get();

        return view('admin.document_sub_category.edit_document_sub_category',$this->data,['document_array' => $document_array]);
    }

    public function update_document_sub_categoery(Request $request) {
        $rules = array(
            'sub_category_name' => 'required',
            'category_id'       => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return redirect()->route('admin.document_sub_category')
                            ->with('error', 'Error during operation. Try again!');
        } else {

            $documentupdate = Inward_outward_doc_sub_category::where('id', Input::get('id'))->update([ 'user_id' => Auth::user()->id,'sub_category_name' => Input::get('sub_category_name'),'category_id'=>Input::get('category_id')]);
            if ($documentupdate) {
                return redirect()->route('admin.document_sub_category')
                                ->with('success', 'Record Updated Successfully!');
            } else {
                return redirect()->back()->with("error", "Not Change Any Values!");
            }
        }
    }

    public function delete_document_sub_cat($id) {
        $doc_id = Inward_outward_doc_sub_category::where('id', $id)->get();


        if (!empty($doc_id[0])) {

            if (Inward_outward_doc_sub_category::where('id', $id)->delete()) {

                return redirect()->route('admin.document_sub_category')->with('success', 'Delete Document successfully updated.');
            }

            return redirect()->route('admin.document_sub_category')->with('error', 'Error during operation. Try again!');
        } else {

            return redirect()->route('admin.document_sub_category')->with('error', 'This sub category does not exits !');
        }
    }

    public function get_doc_sub_cat()
    {
        if(!empty($_GET['doc_category_id'])) {
           $doc_category_id = $_GET['doc_category_id'];

           $document_sub_cat_data = Inward_outward_doc_sub_category::select('sub_category_name','id')->where('status','Enabled')->where(['category_id' => $doc_category_id])->get()->toArray();
           $html = "<option value='' disabled selected>Select Sub Category</option>";
           foreach ($document_sub_cat_data as $key => $document_sub_cat_data_value) {
                $html.= "<option value=".$document_sub_cat_data_value['id'].">".$document_sub_cat_data_value['sub_category_name']."</option>";
           }
           echo  $html;
           die();
        }
    }
}
