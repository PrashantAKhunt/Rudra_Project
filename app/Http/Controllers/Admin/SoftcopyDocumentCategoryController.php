<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use App\Sender;
use DB;
use SebastianBergmann\Environment\Console;
use Illuminate\Support\Facades\Config;
use App\Employees;
use App\User;
use App\Companies;
use App\SoftcopyDocumentCategory;
use Illuminate\Support\Facades\Response;
use PhpParser\Node\Expr\BinaryOp\Concat;
use App\Lib\CommonTask;
use App\Lib\NotificationTask;
use App\Lib\Permissions;

class SoftcopyDocumentCategoryController extends Controller
{

    public $data;
    private $common_task;
    private $notification_task;
    private $module_id = '999';

    public function __construct()
    {
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();
    }

    public function softcopy_document_category_list()
    {
        $this->data['page_title'] = "Softcopy Document Category";
        //$this->data['view_special_permission'] = Permissions::checkSpecialPermission(999);
        $category_list = SoftcopyDocumentCategory::get();
        return view('admin.softcopy_document_category.softcopy_document_category', $this->data, ['category_list' => $category_list]);
    }


    public function change_softcopy_document_status(Request $request, $id, $status)
    {
        $update_doc_arr = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip()
        ];

        try {
            SoftcopyDocumentCategory::where('id', $id)->update($update_doc_arr);
            return redirect()->route('admin.softcopy_document_category')->with('success', 'Status successfully updated.');
        } catch (Exception $exc) {

            return redirect()->route('admin.softcopy_document_category')->with('error', 'Error Occurred. Try Again!');
        }
    }

    public function add_softcopy_document_category(Request $request)
    {
        $category_arr = [
            'name' => $request->input('name'),
            'status' => 'Enabled',
            'created_at' => date('Y-m-d H:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip()
        ];
        SoftcopyDocumentCategory::insert($category_arr);
        return redirect()->route('admin.softcopy_document_category')->with('success', 'New Category inserted successfully.');
    }

    public function edit_softcopy_document_category($id)
    {
        $document = SoftcopyDocumentCategory::where('id', $id)->first();

        $document_array = array(
            'id' => $document->id,
            'name' => $document->name,
        );

        return view('admin.softcopy_document_category.edit_softcopy_document_category', ['document_array' => $document_array]);
    }

    public function update_softcopy_document_category(Request $request)
    {
        $rules = array(
            'name' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return redirect()->route('admin.softcopy_document_category')
                ->withErrors($validator);
        } else {

            $documentUpdate = SoftcopyDocumentCategory::where('id', Input::get('id'))->update(['name' => Input::get('name')]);
            if ($documentUpdate) {
                return redirect()->route('admin.softcopy_document_category')
                    ->with('success', 'Record Updated Successfully!');
            } else {
                return redirect()->back()->with("error", "Not Change Any Values!");
            }
        }
    }

    /*public function delete_softcopy_document_category($id)
    {
        $doc_id = SoftcopyDocumentCategory::where('doc_category_id', $id)->get();

        if ($doc_id->count() == 0) {

            if (SoftcopyDocumentCategory::where('id', $id)->delete()) {

                return redirect()->route('admin.softcopy_document_category')->with('success', 'Delete Document successfully updated.');
            }

            return redirect()->route('admin.softcopy_document_category')->with('error', 'Error during operation. Try again!');
        } else {

            return redirect()->route('admin.softcopy_document_category')->with('error', 'This category is used by someone!');
        }
    }*/
}
