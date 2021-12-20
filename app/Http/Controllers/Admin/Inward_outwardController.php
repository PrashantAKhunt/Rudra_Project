<?php

namespace App\Http\Controllers\Admin;

use App\Common_query;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Facades\Input;
use App\Inward_outward_chat;
use App\Inward_outward_doc_category;
use App\Inward_outward_delivery_mode;
use App\Inward_outward_users;
use App\Inward_outwards;
use App\Inward_outward_views;
use App\Inward_outward_sender;

use App\Inward_outward_prime_action;
use App\Inward_outward_distrubuted_work;

use App\Sender;
use DB;
use Yajra\Datatables\Datatables;

use SebastianBergmann\Environment\Console;
use Illuminate\Support\Facades\Config;
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

class Inward_outwardController extends Controller
{

    public $data;
    private $common_task;
    private $notification_task;
    private $module_id = 40;

    public function __construct()
    {
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();
    }

    public function category_list()
    {
        $this->data['page_title'] = "Document Category";
        $this->data['view_special_permission'] = Permissions::checkSpecialPermission(39);
        $category_list = Inward_outward_doc_category::where('is_approved', 1)->get();


        return view('admin.user.document_category', $this->data, ['category_list' => $category_list]);
    }


    public function change_doc_status(Request $request, $id, $status)
    {
        $update_doc_arr = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip()
        ];

        try {
            Inward_outward_doc_category::where('id', $id)->update($update_doc_arr);
            return redirect()->route('admin.document_category')->with('success', 'Status successfully updated.');
        } catch (Exception $exc) {

            return redirect()->route('admin.document_category')->with('error', 'Error Occurred. Try Again!');
        }
    }


    public function is_doc_special(Request $request, $id, $type)
    {
        $doc_arr = [
            'is_special' => $type,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip()
        ];
        try {
            Inward_outward_doc_category::where('id', $id)->update($doc_arr);
            return redirect()->route('admin.document_category')->with('success', 'Type successfully updated.');
        } catch (Exception $exc) {
            return redirect()->route('admin.document_category')->with('error', 'Error Occurred. Try Again!');
        }
    }

    public function add_document(Request $request)
    {

        $category_arr = [
            'user_id' => Auth::user()->id,
            'category_name' => $request->input('category_name'),
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

        Inward_outward_doc_category::insert($category_arr);
        $module = 'Registry Document Category';
        $this->notification_task->entryApprovalNotify($module);

        //Inward_outwards::where('id', $new_id)->update($new_inward_arr);

        return redirect()->route('admin.document_category')->with('success', 'New Category inserted successfully.');
    }

    public function edit_document($id)
    {
        $document = Inward_outward_doc_category::where('id', $id)->first();

        $document_array = array(
            'id' => $document->id,
            'category_name' => $document->category_name,
        );

        return view('admin.user.edit_category', ['document_array' => $document_array]);
    }

    public function update_document(Request $request)
    {
        $rules = array(
            'category_name' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return redirect()->route('admin.document_category')
                ->withErrors($validator);
        } else {

            $documentupdate = Inward_outward_doc_category::where('id', Input::get('id'))->update(['user_id' => Auth::user()->id, 'category_name' => Input::get('category_name')]);
            if ($documentupdate) {
                return redirect()->route('admin.document_category')
                    ->with('success', 'Record Updated Successfully!');
            } else {
                return redirect()->back()->with("error", "Not Change Any Values!");
            }
        }
    }

    public function delete_document($id)
    {
        $doc_id = Inward_outwards::where('doc_category_id', $id)->get();


        if ($doc_id->count() == 0) {

            if (Inward_outward_doc_category::where('id', $id)->delete()) {

                return redirect()->route('admin.document_category')->with('success', 'Delete Document successfully updated.');
            }

            return redirect()->route('admin.document_category')->with('error', 'Error during operation. Try again!');
        } else {

            return redirect()->route('admin.document_category')->with('error', 'This category is used by someone!');
        }
    }


    //--------------------------------------------------Registry Documents---------------------------------------------.//

    public function pending_registry_documents()
    {

        $this->data['page_title'] = "Important Registry Documents";
        $this->data['module_title'] = "Inward Outward ";
        $registry_docs = Inward_outwards::select(
            'department.dept_name',
            'users.name as requested_by',
            'sender.name as sender_type',
            'inward_outward_delivery_mode.name as delivery_mode_name',
            'inward_outward_doc_sub_category.sub_category_name',
            'company.company_name',
            'project.project_name',
            'inward_outwards.id',
            'inward_outwards.document_file',
            'inward_outwards.inward_outward_title',
            'inward_outwards.inward_outward_no',
            'inward_outwards.parent_inward_outward_no',
            'inward_outwards.description',
            'inward_outwards.ref_outward_number',
            'inward_outwards.type',
            'inward_outwards.expected_ans_date',
            'inward_outwards.created_at',
            'inward_outward_doc_category.category_name',
            'inward_outwards.received_date',
            'inward_outwards.sender_name',
            'inward_outwards.sender_invoice_date',
            'inward_outwards.pdf_page_no',
            'inward_outwards.pdf_size',
            'inward_outwards.doc_allotment_time',
            'inward_outwards.doc_delivery_mode',
            'inward_outwards.delivery_file',
            'inward_outwards.sender_comment'
        )
            ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
            ->join('company', 'company.id', '=', 'inward_outwards.company_id')
            ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
            ->leftjoin('sender', 'sender.id', '=', 'inward_outwards.sender_id')
            ->leftjoin('department', 'department.id', '=', 'inward_outwards.department_id')
            ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
            ->join('project', 'project.id', '=', 'inward_outwards.project_id')
            ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
            ->where('inward_outwards.doc_mark', 'Pending')
            ->orderBy('inward_outwards.id', 'DESC')
            ->get();

        foreach ($registry_docs as $key => $value) {
            $registry_user_list = Inward_outward_users::join('users', 'users.id', '=', 'inward_outward_users.user_id')
                ->where('inward_outward_users.inward_outward_id', '=', $value->id)
                ->pluck('users.name')->toArray();

            $registry_docs[$key]->users_list = implode(',', $registry_user_list);
        }

        $this->data['registry_docs'] = $registry_docs;

        return view('admin.registry_documents.pending_documents', $this->data);
    }

    public function approved_inwards_documents()
    {
        $this->data['page_title'] = "Approved Inwards Documents";
        $this->data['module_title'] = "Inward Outward ";
        $this->data['inwards_docs'] = $inwards_docs = Inward_outwards::select('inward_outward_doc_sub_category.sub_category_name', 'company.company_name', 'project.project_name', 'inward_outwards.id', 'inward_outwards.document_file', 'inward_outwards.inward_outward_title', 'inward_outwards.inward_outward_no', 'inward_outwards.parent_inward_outward_no', 'inward_outwards.ref_outward_number', 'inward_outwards.description', 'inward_outwards.expected_ans_date', 'inward_outwards.created_at', 'inward_outward_doc_category.category_name', 'inward_outwards.received_date')
            ->join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
            ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
            ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
            ->join('company', 'company.id', '=', 'inward_outwards.company_id')
            ->join('project', 'project.id', '=', 'inward_outwards.project_id')
            ->where(function ($query) {
                //if (Auth::user()->role != config('constants.SuperUser')) {
                $query->where('inward_outward_users.user_id', '=', Auth::user()->id);
                //}
            })
            ->where('inward_outwards.type', 'Inwards')
            ->where('inward_outwards.doc_mark', 'Approved')
            ->orderBy('inward_outwards.id', 'DESC')
            //->groupBy('inward_outward_users.user_id')
            ->get();


        return view('admin.registry_documents.inward_documents', $this->data);
    }

    public function approved_outwards_documents()
    {
        $this->data['page_title'] = "Approved Outwards Documents";
        $this->data['module_title'] = "Inward Outward ";
        $this->data['outwards_docs'] = Inward_outwards::select('inward_outward_doc_sub_category.sub_category_name', 'inward_outwards.id', 'company.company_name', 'project.project_name', 'inward_outwards.document_file', 'inward_outwards.inward_outward_title', 'inward_outwards.inward_outward_no', 'inward_outwards.parent_inward_outward_no', 'inward_outwards.description', 'inward_outwards.expected_ans_date', 'inward_outwards.created_at', 'inward_outwards.ref_outward_number', 'inward_outward_doc_category.category_name', 'inward_outwards.received_date')
            ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
            ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
            ->join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
            ->join('company', 'company.id', '=', 'inward_outwards.company_id')
            ->join('project', 'project.id', '=', 'inward_outwards.project_id')
            ->where(function ($query) {
                //if (Auth::user()->role != config('constants.SuperUser')) {
                $query->where('inward_outward_users.user_id', '=', Auth::user()->id);
                //}
            })
            ->where('inward_outwards.type', 'Outwards')
            ->where('inward_outwards.doc_mark', 'Approved')
            ->orderBy('inward_outwards.id', 'DESC')
            //->groupBy('inward_outward_users.user_id')
            ->get();

        return view('admin.registry_documents.outward_documents', $this->data);
    }

    public function mark_approve_documnet(Request $request)
    {

        $registry_id = $request->registry_id;
        $status = $request->doc_mark;
        $update_maek_arr = [
            'doc_mark' => $status,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip()
        ];


        Inward_outwards::where('id', $registry_id)->update($update_maek_arr);

        if ($status == 'Approved') {


            $user_data = Inward_outward_users::where('inward_outward_id', '=', $registry_id)
                ->pluck('user_id')->toArray();

            Inward_outward_users::where('inward_outward_id', $registry_id)->delete();

            $user_data = $this->common_task->setSuperUserId($user_data, 1);

            foreach ($user_data as $key => $user) {

                $users_arr = [
                    'inward_outward_id' => $registry_id,
                    'user_id' => $user,
                    'status' => $key == 0 ? 'Processing' : 'Pending',
                    'expected_ans_date' => NULL,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_ip' => $request->ip(),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                ];
                Inward_outward_users::insert($users_arr);
            }

            // insert in Inward_outward_views table
            Inward_outward_views::where('inward_outward_id', $registry_id)->delete();

            foreach ($user_data as $key => $user) {

                $views_arr = [
                    'user_id' => $user,
                    'inward_outward_id' => $registry_id,
                    'is_view' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_ip' => $request->ip(),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                ];

                Inward_outward_views::insert($views_arr);
            }
        }

        return redirect()->route('admin.pending_registry_documents'); //->with('success', 'Registry Documenet successfully Approved.');
    }

    //---------------------------------------------------Inward Outward---------------------------------------------//

    public function index()
    {

        $full_view_permission = Permissions::checkPermission($this->module_id, 5);
        $my_view_permission = Permissions::checkPermission($this->module_id, 1);

        if (!$full_view_permission && !$my_view_permission) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You do not have permission to access this module.');
        }

        $this->data['page_title'] = "Inward Outward";


        $inward_count_query = Inward_outwards::select('inward_outward_doc_sub_category.sub_category_name', 'inward_outwards.id', 'inward_outwards.inward_outward_title', 'inward_outwards.inward_outward_no', 'inward_outwards.parent_inward_outward_no', 'inward_outwards.description', 'inward_outwards.type', 'inward_outwards.expected_ans_date', 'inward_outwards.created_at', 'inward_outward_doc_category.category_name', 'inward_outwards.received_date')
            ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
            ->leftJoin('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
            ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
            ->where('inward_outwards.type', '=', 'Inwards')
            ->groupBy('inward_outward_users.inward_outward_id');

        $permission_arr = $this->common_task->getPermissionArr(Auth::user()->role, 40); //fetch view permissions of users
        if (in_array(5, $permission_arr)) {
            $inward_count =  $inward_count_query; //show all request
        } elseif (in_array(1, $permission_arr)) {
            $inward_count =  $inward_count_query
                ->leftJoin('inward_outward_prime_action', 'inward_outward_prime_action.inward_outward_id', '=', 'inward_outwards.id')
                ->leftJoin('inward_outward_distrubuted_work', 'inward_outward_distrubuted_work.inward_outward_prime_action_id', '=', 'inward_outward_prime_action.id')
                ->groupBy('inward_outward_distrubuted_work.inward_outward_prime_action_id')
                ->where(function ($query) {
                    $query->where('inward_outwards.inserted_by', Auth::user()->id)
                        ->orWhere('inward_outwards.requested_by', Auth::user()->id)
                        ->orWhere('inward_outward_users.user_id', Auth::user()->id)
                        ->orWhere(function ($query) {
                            $query->Where('inward_outwards.prime_employee_id', Auth::user()->id)
                                ->Where('inward_outwards.prime_user_status', 'Accepted');
                        })->orWhere(function ($query) {
                            $query->Where('inward_outward_distrubuted_work.support_employee_id', Auth::user()->id)
                                ->Where('inward_outward_distrubuted_work.emp_status', 'Accepted');
                        });
                });
        }

        $this->data['inward_count'] = $inward_count->get()->count();
        //----------- ----

        $outward_count_query =  Inward_outwards::select('inward_outward_doc_sub_category.sub_category_name', 'inward_outwards.id', 'inward_outwards.inward_outward_title', 'inward_outwards.inward_outward_no', 'inward_outwards.description', 'inward_outwards.parent_inward_outward_no', 'inward_outwards.type', 'inward_outwards.expected_ans_date', 'inward_outwards.created_at', 'inward_outward_doc_category.category_name', 'inward_outwards.received_date')
            ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
            ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
            ->join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
            ->where('inward_outwards.type', '=', 'Outwards')
            ->groupBy('inward_outward_users.inward_outward_id');

        $permission_arr = $this->common_task->getPermissionArr(Auth::user()->role, 40);  //fetch view permissions of users

        if (in_array(5, $permission_arr)) {
            $outward_count =  $outward_count_query; //show all  request
        } elseif (in_array(1, $permission_arr)) {
            $outward_count =  $outward_count_query->where(function ($query) {
                $query->where('inward_outwards.inserted_by', Auth::user()->id)
                    ->orWhere('inward_outwards.requested_by', Auth::user()->id)
                    ->orWhere('inward_outward_users.user_id', Auth::user()->id);
            });
        }

        $this->data['outward_count'] = $outward_count->get()->count();
        //---------------------------------------------------------------------
        $this->data['assignee_registry_count'] =  Inward_outwards::join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
            ->where('inward_outward_users.status', '=', 'Processing')
            ->whereDate('inward_outwards.created_at', '>=', '2020-06-02')
            ->where('inward_outward_users.user_id', '=', Auth::user()->id)->get()->count();

        $this->data['impo_registry'] = Inward_outwards::where('doc_mark', 'Pending')->get()->count();

        $this->data['process_list'] = Inward_outwards::join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
            ->where('inward_outwards.is_answered', 'No')
            ->where('inward_outwards.prime_user_status', 'Assigned')
            ->where('inward_outward_users.status', 'Completed')
            ->whereDate('inward_outwards.created_at', '>=', '2020-06-02')
            ->where('inward_outward_users.user_id', Auth::user()->id)
            ->get()->count();
        $this->data['prime_user_registry'] = Inward_outwards::where('prime_user_status', '!=', 'Rejected')
            ->where('prime_employee_id', Auth::user()->id)
            ->get()->count();

        //----------------- SUpport/prime box
        $prime_count = Inward_outwards::join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
            ->where('inward_outward_users.status', 'Completed')
            ->where('inward_outwards.is_answered', 'No')
            ->where('inward_outwards.prime_user_status', 'Assigned')
            ->whereDate('inward_outwards.created_at', '>=', '2020-06-02')  //custom
            ->where('inward_outwards.prime_employee_id', Auth::user()->id)
            ->get()->count();
        $suport_count = Inward_outward_distrubuted_work::join('inward_outward_prime_action', 'inward_outward_prime_action.id', '=', 'inward_outward_distrubuted_work.inward_outward_prime_action_id')
            ->join('inward_outwards', 'inward_outwards.id', '=', 'inward_outward_prime_action.inward_outward_id')
            ->where('inward_outward_distrubuted_work.emp_status', 'Assigned')
            ->whereDate('inward_outwards.created_at', '>=', '2020-06-02')
            ->where('inward_outward_distrubuted_work.support_employee_id', '=', Auth::user()->id)->get()->count();
        $submit_count = Inward_outward_distrubuted_work::join('inward_outward_prime_action', 'inward_outward_prime_action.id', '=', 'inward_outward_distrubuted_work.inward_outward_prime_action_id')
            ->join('inward_outwards', 'inward_outwards.id', '=', 'inward_outward_prime_action.inward_outward_id')
            ->where('inward_outward_distrubuted_work.emp_status', 'Accepted')
            ->where('inward_outward_distrubuted_work.work_status', 'Submitted')
            ->whereDate('inward_outwards.created_at', '>=', '2020-06-02')
            ->where('inward_outwards.prime_employee_id', Auth::user()->id)->get()->count();
        $acceptance_count =  Inward_outward_distrubuted_work::join('inward_outward_prime_action', 'inward_outward_prime_action.id', '=', 'inward_outward_distrubuted_work.inward_outward_prime_action_id')
            ->join('inward_outwards', 'inward_outwards.id', '=', 'inward_outward_prime_action.inward_outward_id')
            ->where('inward_outward_prime_action.final_status', 'Pending')
            ->where('inward_outward_distrubuted_work.emp_status', 'Rejected')
            ->whereNotNull('inward_outward_distrubuted_work.satisfied_reason')
            ->whereDate('inward_outwards.created_at', '>=', '2020-06-02')
            ->where('inward_outwards.prime_employee_id', Auth::user()->id)
            ->get()->count();
        $all_state_count = $prime_count + $suport_count + $submit_count + $acceptance_count;
        $this->data['all_state_count'] =   $all_state_count;
        //--------------------------
        $this->data['managment_count'] = Inward_outward_prime_action::join('inward_outwards', 'inward_outwards.id', '=', 'inward_outward_prime_action.inward_outward_id')
            ->where('inward_outwards.prime_user_status', '=', 'Accepted')
            ->where('inward_outward_prime_action.final_status', '=', 'Rejected')
            ->whereDate('inward_outwards.created_at', '>=', '2020-06-02')
            ->get()->count();

        //------------------------------
        $this->data['inwards_docs'] = Inward_outwards::join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
            ->where(function ($query) {
                //if (Auth::user()->role != config('constants.SuperUser')) {
                $query->where('inward_outward_users.user_id', '=', Auth::user()->id);
                //}
            })
            ->where('inward_outwards.type', 'Inwards')
            ->where('inward_outwards.doc_mark', 'Approved')
            //->groupBy('inward_outward_users.user_id')
            ->get()->count();

        $this->data['outwards_docs'] = Inward_outwards::join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
            ->where(function ($query) {
                //if (Auth::user()->role != config('constants.SuperUser')) {
                $query->where('inward_outward_users.user_id', '=', Auth::user()->id);
                //}
            })
            ->where('inward_outwards.type', 'Outwards')
            ->where('inward_outwards.doc_mark', 'Approved')
            //->groupBy('inward_outward_users.user_id')
            ->get()->count();



        return view('admin.user.inward_outward', $this->data);
    }

    public function inwards(Request $request)
    {

        $this->data['full_view_permission'] = Permissions::checkPermission($this->module_id, 5);
        $this->data['add_permission'] = Permissions::checkPermission($this->module_id, 3);
        $this->data['edit_permission'] = Permissions::checkPermission($this->module_id, 2);

        $this->data['page_title'] = "Inwards";
        $this->data['module_title'] = "Inward Outward";
        $this->data['range_date'] = '';
        $range_date = $request->input('range_date');
        if ($range_date != '') {
            // dd($request->all());

            $this->data['range_date'] = $request->get('range_date');
            if ($request->get('range_date') != "") {
                $date = $request->get('range_date');
                $mainDate = explode("-", $date);
                $strFirstdate = str_replace("/", "-", $mainDate[0]);
                $strLastdate = str_replace("/", "-", $mainDate[1]);
                $first_date = date('Y-m-d', strtotime($strFirstdate));
                $second_date = date('Y-m-d', strtotime($strLastdate));
            }
            $partial_query = Inward_outwards::select('inward_outward_doc_sub_category.sub_category_name', 'inward_outwards.id', 'inward_outwards.inward_outward_title', 'inward_outwards.inward_outward_no', 'inward_outwards.parent_inward_outward_no', 'inward_outwards.description', 'inward_outwards.type', 'inward_outwards.expected_ans_date', 'inward_outwards.created_at', 'inward_outward_doc_category.category_name', 'inward_outwards.received_date', 'document_file')
                ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
                ->leftJoin('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
                ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
                ->where('inward_outwards.type', '=', 'Inwards')
                ->whereDate('inward_outwards.created_at', '>=',$first_date)
                ->whereDate('inward_outwards.created_at', '<=',$second_date)
                ->groupBy('inward_outward_users.inward_outward_id');

            $permission_arr = $this->common_task->getPermissionArr(Auth::user()->role, 40); //fetch view permissions of users
            if (in_array(5, $permission_arr)) {
                $inward_list =  $partial_query; //show all request
            } elseif (in_array(1, $permission_arr)) {
                $inward_list =  $partial_query
                    ->leftJoin('inward_outward_prime_action', 'inward_outward_prime_action.inward_outward_id', '=', 'inward_outwards.id')
                    ->leftJoin('inward_outward_distrubuted_work', 'inward_outward_distrubuted_work.inward_outward_prime_action_id', '=', 'inward_outward_prime_action.id')
                    ->groupBy('inward_outward_distrubuted_work.inward_outward_prime_action_id')
                    ->where(function ($query) {
                        $query->where('inward_outwards.inserted_by', Auth::user()->id)
                            ->orWhere('inward_outwards.requested_by', Auth::user()->id)
                            ->orWhere('inward_outward_users.user_id', Auth::user()->id)
                            ->orWhere(function ($query) {
                                $query->Where('inward_outwards.prime_employee_id', Auth::user()->id)
                                    ->Where('inward_outwards.prime_user_status', 'Accepted');
                            })->orWhere(function ($query) {
                                $query->Where('inward_outward_distrubuted_work.support_employee_id', Auth::user()->id)
                                    ->Where('inward_outward_distrubuted_work.emp_status', 'Accepted');
                            });
                    });
            } else {
                return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
            }
        } else {
            $partial_query = Inward_outwards::select('inward_outward_doc_sub_category.sub_category_name', 'inward_outwards.id', 'inward_outwards.inward_outward_title', 'inward_outwards.inward_outward_no', 'inward_outwards.parent_inward_outward_no', 'inward_outwards.description', 'inward_outwards.type', 'inward_outwards.expected_ans_date', 'inward_outwards.created_at', 'inward_outward_doc_category.category_name', 'inward_outwards.received_date', 'document_file')
                ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
                ->leftJoin('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
                ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
                ->where('inward_outwards.type', '=', 'Inwards')
                ->groupBy('inward_outward_users.inward_outward_id');

            $permission_arr = $this->common_task->getPermissionArr(Auth::user()->role, 40); //fetch view permissions of users
            if (in_array(5, $permission_arr)) {
                $inward_list =  $partial_query; //show all request
            } elseif (in_array(1, $permission_arr)) {
                $inward_list =  $partial_query
                    ->leftJoin('inward_outward_prime_action', 'inward_outward_prime_action.inward_outward_id', '=', 'inward_outwards.id')
                    ->leftJoin('inward_outward_distrubuted_work', 'inward_outward_distrubuted_work.inward_outward_prime_action_id', '=', 'inward_outward_prime_action.id')
                    ->groupBy('inward_outward_distrubuted_work.inward_outward_prime_action_id')
                    ->where(function ($query) {
                        $query->where('inward_outwards.inserted_by', Auth::user()->id)
                            ->orWhere('inward_outwards.requested_by', Auth::user()->id)
                            ->orWhere('inward_outward_users.user_id', Auth::user()->id)
                            ->orWhere(function ($query) {
                                $query->Where('inward_outwards.prime_employee_id', Auth::user()->id)
                                    ->Where('inward_outwards.prime_user_status', 'Accepted');
                            })->orWhere(function ($query) {
                                $query->Where('inward_outward_distrubuted_work.support_employee_id', Auth::user()->id)
                                    ->Where('inward_outward_distrubuted_work.emp_status', 'Accepted');
                            });
                    });
            } else {
                return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
            }
        }
        //-------------------------------------------------

        $list = $inward_list->get();
        if($request->ajax()){
            return Datatables::of($list)->make(true);
        }
        // dd($list);
        return view('admin.user.inwards', $this->data, ['inward_list' => $list]);
    }

    public function outwards(Request $request)
    {
        $this->data['full_view_permission'] = Permissions::checkPermission($this->module_id, 5);
        $this->data['add_permission'] = Permissions::checkPermission($this->module_id, 3);
        $this->data['edit_permission'] = Permissions::checkPermission($this->module_id, 2);

        $this->data['page_title'] = "Outwards";
        $this->data['module_title'] = "Inward Outward ";
        $this->data['range_date'] = '';
        $range_date = $request->input('range_date');
        if ($range_date != '') {
            // dd($request->all());
            $this->data['range_date'] = $request->get('range_date');
            if ($request->get('range_date') != "") {
                $date = $request->get('range_date');
                $mainDate = explode("-", $date);
                $strFirstdate = str_replace("/", "-", $mainDate[0]);
                $strLastdate = str_replace("/", "-",$mainDate[1]);
                $first_date = date('Y-m-d',strtotime($strFirstdate));
                $second_date = date('Y-m-d',strtotime($strLastdate));
            }
            $partial_query = Inward_outwards::select('inward_outward_doc_sub_category.sub_category_name', 'inward_outwards.id', 'inward_outwards.inward_outward_title', 'inward_outwards.inward_outward_no', 'inward_outwards.description', 'inward_outwards.parent_inward_outward_no', 'inward_outwards.type', 'inward_outwards.expected_ans_date', 'inward_outwards.created_at', 'inward_outward_doc_category.category_name', 'inward_outwards.received_date', 'document_file')
                ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
                ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
                ->join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
                ->where('inward_outwards.type', '=', 'Outwards')
                ->whereDate('inward_outwards.created_at', '>=',$first_date)
                ->whereDate('inward_outwards.created_at', '<=',$second_date)
                ->groupBy('inward_outward_users.inward_outward_id');
            // dd($partial_query);

            $permission_arr = $this->common_task->getPermissionArr(Auth::user()->role, 40);  //fetch view permissions of users

            if (in_array(5, $permission_arr)) {
                $outward_list =  $partial_query; //show all  request
            } elseif (in_array(1, $permission_arr)) {
                $outward_list =  $partial_query->where(function ($query) {
                    $query->where('inward_outwards.inserted_by', Auth::user()->id)
                        ->orWhere('inward_outwards.requested_by', Auth::user()->id)
                        ->orWhere('inward_outward_users.user_id', Auth::user()->id);
                });
            } else {
                return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
            }
        } 
        else {
            $partial_query = Inward_outwards::select('inward_outward_doc_sub_category.sub_category_name', 'inward_outwards.id', 'inward_outwards.inward_outward_title', 'inward_outwards.inward_outward_no', 'inward_outwards.description', 'inward_outwards.parent_inward_outward_no', 'inward_outwards.type', 'inward_outwards.expected_ans_date', 'inward_outwards.created_at', 'inward_outward_doc_category.category_name', 'inward_outwards.received_date', 'document_file')
                ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
                ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
                ->join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
                ->where('inward_outwards.type', '=', 'Outwards')
                ->groupBy('inward_outward_users.inward_outward_id');

            $permission_arr = $this->common_task->getPermissionArr(Auth::user()->role, 40);  //fetch view permissions of users

            if (in_array(5, $permission_arr)) {
                $outward_list =  $partial_query; //show all  request
                // dd($outward_list);
            } elseif (in_array(1, $permission_arr)) {
                $outward_list =  $partial_query->where(function ($query) {
                    $query->where('inward_outwards.inserted_by', Auth::user()->id)
                        ->orWhere('inward_outwards.requested_by', Auth::user()->id)
                        ->orWhere('inward_outward_users.user_id', Auth::user()->id);
                });
            } else {
                return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
            }
        }
        $list = $outward_list->get();
        // dd($list);
        if($request->ajax()){
            return Datatables::of($list)->make(true);
        }
    
        return view('admin.user.outwards', $this->data);
    }

    public function add_inward()
    {
        $full_view_permission = Permissions::checkPermission($this->module_id, 5);
        $add_permission = Permissions::checkPermission($this->module_id, 3);

        if (!$full_view_permission || !$add_permission) {
            return redirect()->route('admin.inward_outward')->with('error', 'Access Denied. You do not have permission to access this function.');
        }
        $id = Auth::user()->id;
        $this->data['page_title'] = 'Add Inward';
        $this->data['module_title'] = "Inward Outward";

        $this->data['doc_category_list'] = Inward_outward_doc_category::select('id', 'category_name')
            ->where('status', 'Enabled')
            ->get();
        $this->data['department_category'] = Department::select('id', 'dept_name')
            ->get();
        $this->data['companies'] = Companies::where('status', 'Enabled')
            ->get();
        $this->data['sender_list'] = Sender::where('status', 'Enabled')
            ->get();
        $this->data['delivery_mode_list'] = Inward_outward_delivery_mode::where('status', 'Enabled')
            ->get();
        $this->data['users_list']  = User::where('status', 'Enabled')
            ->get(['id', 'name'])->toArray();
        $this->data['registry_list'] = Inward_outwards::select('inward_outwards.id', 'inward_outwards.inward_outward_title', 'inward_outwards.inward_outward_no')
            ->join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
            //->where('inward_outward_users.user_id', '=', $id)
            ->where('inward_outwards.is_reply', '=', 0)
            ->get();

        return view('admin.user.add_inward', $this->data);
    }

    public function add_outward()
    {
        $full_view_permission = Permissions::checkPermission($this->module_id, 5);
        $add_permission = Permissions::checkPermission($this->module_id, 3);

        if (!$full_view_permission || !$add_permission) {
            return redirect()->route('admin.inward_outward')->with('error', 'Access Denied. You do not have permission to access this function.');
        }
        $id = Auth::user()->id;
        $this->data['page_title'] = 'Add Outward';
        $this->data['module_title'] = "Inward Outward";

        $this->data['doc_category_list'] = Inward_outward_doc_category::select('id', 'category_name')
            ->where('status', 'Enabled')
            ->get();

        $this->data['department_category'] = Department::select('id', 'dept_name')
            ->get();
        $this->data['companies'] = Companies::where('status', 'Enabled')
            ->get();
        $this->data['sender_list'] = Sender::where('status', 'Enabled')
            ->get();
        $this->data['delivery_mode_list'] = Inward_outward_delivery_mode::where('status', 'Enabled')
            ->get();
        $this->data['users_list']  = User::where('status', 'Enabled')
            ->get(['id', 'name'])->toArray();

        $this->data['letter_head_usage'] = \App\LetterHeadRegister::where('is_used', 'not_used')->get(['id', 'letter_head_number']);
        $this->data['registry_list'] = Inward_outwards::select('inward_outwards.id', 'inward_outwards.inward_outward_title', 'inward_outwards.inward_outward_no')
            ->join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
            //->where('inward_outward_users.user_id', '=', $id)
            ->where('inward_outwards.is_reply', '=', 0)
            ->get();

        return view('admin.user.add_outward', $this->data);
    }


    public function edit_inward($id)
    {
        $full_view_permission = Permissions::checkPermission($this->module_id, 5);
        $edit_permission = Permissions::checkPermission($this->module_id, 2);

        if (!$full_view_permission || !$edit_permission) {
            return redirect()->route('admin.inward_outward')->with('error', 'Access Denied. You do not have permission to access this function.');
        }
        $this->data['page_title'] = 'Edit Inward';
        $this->data['module_title'] = "Inward Outward";

        $this->data['inwards_data'] = $inwards_data = Inward_outwards::join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
            ->where('inward_outwards.id', '=', $id)
            ->get();


        foreach ($inwards_data as $key => $value) {
            $inwards_data[$key]->doc_delivery_mode = explode(",", $value->doc_delivery_mode);
        }

        $this->data['company_name']  = Companies::where('id', $inwards_data[0]->company_id)->value('company_name');
        $this->data['project_name']  = Projects::where('id', $inwards_data[0]->project_id)->value('project_name');

        $this->data['doc_category_list'] = Inward_outward_doc_category::select('id', 'category_name')->where('status', 'Enabled')->get();
        $this->data['department_category'] = Department::select('id', 'dept_name')->get();
        $this->data['sender_list'] = Sender::where('status', 'Enabled')->get();
        $this->data['delivery_mode_list'] = Inward_outward_delivery_mode::where('status', 'Enabled')->get();
        $this->data['users_list']  = User::where('status', 'Enabled')->get(['id', 'name'])->toArray();
        $this->data['registry_list'] = Inward_outwards::select('inward_outwards.id', 'inward_outwards.inward_outward_title', 'inward_outwards.inward_outward_no')
            ->join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
            ->where('inward_outwards.is_reply', '=', 0)->get();

        return view('admin.user.edit_inward', $this->data);
    }

    public function edit_outward($id)
    {
        $full_view_permission = Permissions::checkPermission($this->module_id, 5);
        $edit_permission = Permissions::checkPermission($this->module_id, 2);

        if (!$full_view_permission || !$edit_permission) {
            return redirect()->route('admin.inward_outward')->with('error', 'Access Denied. You do not have permission to access this function.');
        }

        $this->data['page_title'] = 'Edit Outward';
        $this->data['module_title'] = "Inward Outward";

        $this->data['outwards_data'] = $outwards_data =  Inward_outwards::join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
            ->where('inward_outwards.id', '=', $id)->get();

        $this->data['company_name']  = Companies::where('id', $outwards_data[0]->company_id)->value('company_name');
        $this->data['project_name']  = Projects::where('id', $outwards_data[0]->project_id)->value('project_name');

        $this->data['doc_category_list'] = Inward_outward_doc_category::select('id', 'category_name')->where('status', 'Enabled')->get();
        $this->data['department_category'] = Department::select('id', 'dept_name')->get();
        $this->data['sender_list'] = Sender::where('status', 'Enabled')->get();
        $this->data['delivery_mode_list'] = Inward_outward_delivery_mode::where('status', 'Enabled')->get();
        $this->data['users_list']  = User::where('status', 'Enabled')->get(['id', 'name'])->toArray();
        $this->data['registry_list'] = Inward_outwards::select('inward_outwards.id', 'inward_outwards.inward_outward_title', 'inward_outwards.inward_outward_no')
            ->join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
            ->where('inward_outwards.is_reply', '=', 0)->get();

        return view('admin.user.edit_outward', $this->data);
    }

    public function update_inward(Request $request)
    {
        $validator_normal = Validator::make($request->all(), [
            'inward_outward_title' => 'required',
            'description' => 'required',
            'doc_category_id' => 'required',
            'doc_sub_category_id' => 'required',
            'doc_delivery_mode.*' => 'required',

            'delivery_mode' => 'required',
            'sender_id' => 'required',
            'sender_name' => 'required',
            'sender_invoice_date' => 'required',
            'requested_by' => 'required',
        ]);

        $inward_id = $request->input('id');

        if ($validator_normal->fails()) {
            return redirect()->route('admin.edit_inward', $inward_id)->with('error', 'Please follow validation rules.');
        }

        $email_user_ids = [];
        //upload user document_file
        $document_file = '';
        if ($request->file('document_file')) {

            $document_file = $request->file('document_file');
            $original_file_name = explode('.', $document_file->getClientOriginalName());
            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);
            $file_path = $document_file->storeAs('public/document_file', $new_file_name);
            if ($file_path) {
                $document_file = $file_path;
            }
        }

        //upload user delivery_file
        $delivery_file = '';
        if ($request->file('delivery_file')) {

            $delivery_file = $request->file('delivery_file');
            $original_file_name = explode('.', $delivery_file->getClientOriginalName());
            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);
            $file_path = $delivery_file->storeAs('public/delivery_file', $new_file_name);
            if ($file_path) {
                $delivery_file = $file_path;
            }
        }


        $doc_delivery_mode = [];
        foreach ($request->doc_delivery_mode as $value) {
            array_push($doc_delivery_mode, $value);
        }

        //========================= Update


        $in_update_arr = [
            'inward_outward_title' => $request->input('inward_outward_title'),
            'ref_outward_number' => $request->input('ref_outward_number'),
            'sender_comment' => $request->input('sender_comment'),

            'description' => $request->input('description'),
            'doc_mark' => !empty($request->input('is_important')) ? 'Pending' : 'None',
            'type' => 'Inwards',
            'doc_category_id' => $request->input('doc_category_id'),
            'doc_sub_category_id' => $request->input('doc_sub_category_id'),
            'department_id' => $request->input('department_id'),

            'inward_outward_delivery_mode_id' => $request->input('delivery_mode'),
            'sender_id' => $request->input('sender_id'),
            'sender_name' => $request->input('sender_name'),
            'sender_invoice_date' => date('Y-m-d', strtotime($request->input('sender_invoice_date'))),
            'requested_by' => $request->input('requested_by'),
            'doc_allotment_time' => date('Y-m-d H:i:s'),
            'doc_delivery_mode' => implode(",", $doc_delivery_mode),
            'assign_employee_id' => $request->input('inward_user_id'),

            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id
        ];

        if (!empty($request->input('registry')) && ($request->input('registry') != $inward_id)) {
            $inward_outward_data = Inward_outwards::where('id', '=', $request->input('registry'))->get();
            $in_update_arr['parent_inward_outward_no'] = $inward_outward_data[0]->parent_inward_outward_no;
            $in_update_arr['is_reply'] = 1;

            $last_outward_registry = Inward_outwards::where('parent_inward_outward_no', $inward_outward_data[0]->parent_inward_outward_no)
                ->where('type', 'Outwards')->orderBy('id', 'DESC')->first();
            //this inward is ans of outward registry selected. so we have to update parent outward registry
            if ($last_outward_registry) {

                $update_parent_outward_arr = [
                    'is_answered' => 'Yes',
                    'answered_date' => date('Y-m-d'),
                    'answered_outward_id' => $inward_id,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                    'updated_by' => Auth::user()->id
                ];
                Inward_outwards::where('id', $last_outward_registry->id)->update($update_parent_outward_arr);
            }
        }

        if (!empty($delivery_file)) {
            $in_update_arr['delivery_file'] = $delivery_file;
        }

        if (!empty($document_file)) {

            $filepath = $request->file('document_file');
            $pageCount = $this->getNumPagesPdf($filepath);
            $file_size =  $request->file('document_file')->getSize();
            $doc_file_size  =  $this->size_as_kb($file_size);

            $in_update_arr['document_file'] = $document_file;
            $in_update_arr['pdf_page_no'] = $pageCount;
            $in_update_arr['pdf_size'] = $doc_file_size;

            $chat_arr = [

                'from_user_id' => Auth::user()->id,
                'message' => 'Inwards',
                'message_type' => 'Document',
                'document_name' => $document_file,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
            ];

            Inward_outward_chat::where('inward_outward_id', $inward_id)->update($chat_arr);
        }

        if ($request->input('ans_expected') == 'Yes') {
            $in_update_arr['expected_ans_date'] = date('Y-m-d H:i:s', strtotime($request->input('expected_ans_date')));
            $in_update_arr['is_answered'] = 'No';
        } else {
            $in_update_arr['is_answered'] = 'Not Required';
        }

        if (Inward_outwards::where('id', $inward_id)->update($in_update_arr)) {

            array_push($email_user_ids, $request->input('inward_user_id'));

            $inward_users_arr = [

                'user_id' => $request->input('inward_user_id'),
                'status' => 'Processing',
                'expected_ans_date' => date('Y-m-d H:i:s', strtotime($request->input('expected_ans_date'))),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
            ];

            Inward_outward_users::where('inward_outward_id', $inward_id)->update($inward_users_arr);

            $inward_views_arr = [
                'user_id' => $request->input('inward_user_id'),
                'is_view' => 0,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
            ];
            Inward_outward_views::where('inward_outward_id', $inward_id)->update($inward_views_arr);

            $sender_exist = Inward_outward_sender::where('sender_name', $request->input('sender_name'))->get();
            if ($sender_exist->count() == 0) {
                $search_sender = [
                    'sender_name' => $request->input('sender_name'),
                    'inward_outward_id' => $inward_id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_ip' => $request->ip(),
                ];
                Inward_outward_sender::insert($search_sender);
            }

            //============ Mail and Notify -> send email and notification regarding inward to the users who are involved.

            $user_emails = User::whereIn('id', $email_user_ids)->get(['email'])->pluck('email')->toArray();
            $inward_outward_no = Inward_outwards::where('id', $inward_id)->value('inward_outward_no');
            $mail_data = [
                'inward_title' => $request->input('inward_outward_title'),
                'inward_number' => $inward_outward_no,
                'to_email_list' => $user_emails
            ];
            $this->common_task->newInwardAlertEmail($mail_data);
            $this->notification_task->inwardAddAlertNotify($email_user_ids, $inward_outward_no);  //send notification to all users whom we had send emails
        } else {
            return redirect()->route('admin.inwards')->with('error', 'Error occurre in insert. Try Again!');
        }
        return redirect()->route('admin.inwards')->with('success', 'Inward Details successfully updated.');
    }

    public function update_outward(Request $request)
    {
        $validator_normal = Validator::make($request->all(), [
            'inward_outward_title' => 'required',
            'description' => 'required',
            'doc_category_id' => 'required',
            'doc_sub_category_id' => 'required',
            'expected_ans_date' => 'required',

            'delivery_mode' => 'required',
            'requested_by' => 'required',
        ]);

        $outward_id = $request->input('id');
        if ($validator_normal->fails()) {
            return redirect()->route('admin.edit_outward', $outward_id)->with('error', 'Please follow validation rules.');
        }

        //upload user document_file
        $document_file = '';
        if ($request->file('document_file')) {

            $document_file = $request->file('document_file');
            $original_file_name = explode('.', $document_file->getClientOriginalName());
            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);
            $file_path = $document_file->storeAs('public/document_file', $new_file_name);
            if ($file_path) {
                $document_file = $file_path;
            }
        }

        //upload user delivery_file
        $delivery_file = '';
        if ($request->file('delivery_file')) {

            $delivery_file = $request->file('delivery_file');
            $original_file_name = explode('.', $delivery_file->getClientOriginalName());
            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);
            $file_path = $delivery_file->storeAs('public/delivery_file', $new_file_name);
            if ($file_path) {
                $delivery_file = $file_path;
            }
        }


        $email_user_ids = [];

        //========================= Update


        $in_update_arr = [
            'inward_outward_title' => $request->input('inward_outward_title'),

            'description' => $request->input('description'),
            'doc_mark' => !empty($request->input('is_important')) ? 'Pending' : 'None',
            'type' => 'Outwards',
            'doc_category_id' => $request->input('doc_category_id'),
            'doc_sub_category_id' => $request->input('doc_sub_category_id'),
            'department_id' => $request->input('department_id'),

            'inward_outward_delivery_mode_id' => $request->input('delivery_mode'),
            'requested_by' => $request->input('requested_by'),
            'assign_employee_id' => $request->input('inward_user_id'),

            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id
        ];

        if (!empty($request->input('registry')) && ($request->input('registry') != $outward_id)) {
            $inward_outward_data = Inward_outwards::where('id', '=', $request->input('registry'))->get();
            $in_update_arr['parent_inward_outward_no'] = $inward_outward_data[0]->parent_inward_outward_no;
            $in_update_arr['is_reply'] = 1;

            $last_outward_registry = Inward_outwards::where('parent_inward_outward_no', $inward_outward_data[0]->parent_inward_outward_no)
                ->where('type', 'Inwards')->orderBy('id', 'DESC')->first();
            //this outward is ans of inward registry selected. so we have to update parent inward registry
            if ($last_outward_registry) {

                $update_parent_outward_arr = [
                    'is_answered' => 'Yes',
                    'answered_date' => date('Y-m-d'),
                    'answered_outward_id' => $outward_id,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                    'updated_by' => Auth::user()->id
                ];
                Inward_outwards::where('id', $last_outward_registry->id)->update($update_parent_outward_arr);
            }
        }

        if (!empty($delivery_file)) {
            $in_update_arr['delivery_file'] = $delivery_file;
        }

        if (!empty($document_file)) {

            $filepath = $request->file('document_file');
            $pageCount = $this->getNumPagesPdf($filepath);
            $file_size =  $request->file('document_file')->getSize();
            $doc_file_size  =  $this->size_as_kb($file_size);

            $in_update_arr['document_file'] = $document_file;
            $in_update_arr['pdf_page_no'] = $pageCount;
            $in_update_arr['pdf_size'] = $doc_file_size;

            $chat_arr = [

                'from_user_id' => Auth::user()->id,
                'message' => 'Outwards',
                'message_type' => 'Document',
                'document_name' => $document_file,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
            ];

            Inward_outward_chat::where('inward_outward_id', $outward_id)->update($chat_arr);
        }

        if ($request->input('ans_expected') == 'Yes') {
            $in_update_arr['expected_ans_date'] = date('Y-m-d H:i:s', strtotime($request->input('expected_ans_date')));
            $in_update_arr['is_answered'] = 'No';
        } else {
            $in_update_arr['is_answered'] = 'Not Required';
        }

        if (Inward_outwards::where('id', $outward_id)->update($in_update_arr)) {

            array_push($email_user_ids, $request->input('inward_user_id'));

            $inward_users_arr = [

                'user_id' => $request->input('inward_user_id'),
                'status' => 'Processing',
                'expected_ans_date' => date('Y-m-d H:i:s', strtotime($request->input('expected_ans_date'))),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
            ];

            Inward_outward_users::where('inward_outward_id', $outward_id)->update($inward_users_arr);

            $inward_views_arr = [
                'user_id' => $request->input('inward_user_id'),
                'is_view' => 0,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
            ];
            Inward_outward_views::where('inward_outward_id', $outward_id)->update($inward_views_arr);


            //============ Mail and Notify -> send email and notification regarding inward to the users who are involved.

            $user_emails = User::whereIn('id', $email_user_ids)->get(['email'])->pluck('email')->toArray();
            $inward_outward_no = Inward_outwards::where('id', $outward_id)->value('inward_outward_no');
            $mail_data = [
                'outward_title' => $request->input('inward_outward_title'),
                'outward_number' => $inward_outward_no,
                'to_email_list' => $user_emails
            ];
            $this->common_task->newOutwardAlertEmail($mail_data);
            $this->notification_task->outwardAddAlertNotify($email_user_ids, $inward_outward_no);  //send notification to all users whom we had send emails
        } else {
            return redirect()->route('admin.outwards')->with('error', 'Error occurre in insert. Try Again!');
        }
        return redirect()->route('admin.outwards')->with('success', 'Outward Details successfully updated.');
    }

    public function department_user_with_registry(Request $request)
    {
        $department_id_arr = $request->id;
        $registry_id = $request->input('registry_id');

        //get list of users of particular registry
        $registry_user_list = Inward_outward_users::where('inward_outward_users.inward_outward_id', $registry_id)
            ->join('users', 'users.id', '=', 'inward_outward_users.user_id')
            ->get(['users.id', 'users.name']);

        if ($registry_user_list->count() > 0) {

            $user_list = User::join('employee', 'employee.user_id', '=', 'users.id')
                ->where('users.status', 'Enabled')
                ->where('users.id', '!=', Auth::user()->id)
                ->whereNotIn('users.id', $registry_user_list->pluck('id')->toArray())
                ->whereIn('employee.department_id', $department_id_arr)
                ->get(['users.id', 'users.name']);
        } else {
            $user_list = User::join('employee', 'employee.user_id', '=', 'users.id')
                ->where('users.status', 'Enabled')
                ->where('users.id', '!=', Auth::user()->id)
                ->whereIn('employee.department_id', $department_id_arr)
                ->get(['users.id', 'users.name']);
        }
        //$department_id_arr = explode(",", $department_ids);
        return response()->json($user_list);
    }

    public function get_registry_old_user_list(Request $request)
    {
        $registry_id = $request->input('registry_id');

        //get list of users of particular registry
        $registry_user_list = Inward_outward_users::where('inward_outward_users.inward_outward_id', $registry_id)
            ->join('users', 'users.id', '=', 'inward_outward_users.user_id')
            ->get(['users.id', 'users.name'])->pluck('name')->toArray();
        return response()->json(['registry_user_list' => implode(', ', $registry_user_list)]);
    }

    public function depart_user_list(Request $request)
    {  //this.....

        $department_id_arr = $request->id;

        $user_list = User::join('employee', 'employee.user_id', '=', 'users.id')
            ->where('users.status', 'Enabled')
            ->where('users.id', '!=', Auth::user()->id)
            ->whereIn('employee.department_id', [$department_id_arr])->orderBy('name')
            ->get(['users.id', 'users.name']);


        return response()->json($user_list);
    }

    public function depart_multi_user_list(Request $request)
    {

        $department_id_arr = $request->id;
        $user_list = User::join('employee', 'employee.user_id', '=', 'users.id')
            ->where('users.status', 'Enabled')
            ->where('users.id', '!=', Auth::user()->id)
            ->whereIn('employee.department_id', $department_id_arr)
            ->get(['users.id', 'users.name']);


        return response()->json($user_list);
    }

    public function inward_no(Request $request)
    {
        //logic of inward_outward_no
        $company_id = $request->company_id;

        $receive_date = date('Y-m-d');
        $rows_count = Inward_outwards::whereDate('received_date', $receive_date)->where('company_id', $company_id)->where('type', 'Inwards')->get()->count();

        $companies_data = Companies::where('id', '=', $company_id)->get();
        $short_name = $companies_data[0]->company_short_name;
        $new_row_count = $rows_count + 1;
        $inward_outward_no = $short_name . "/" . 'INW' . "/" . date('Y/M/d') . "/" . $new_row_count;

        return response()->json($inward_outward_no);
    }

    public function outward_no(Request $request)
    {
        //logic of inward_outward_no
        $company_id = $request->company_id;

        $receive_date = date('Y-m-d');
        $rows_count = Inward_outwards::whereDate('received_date', $receive_date)->where('company_id', $company_id)->where('type', 'Outwards')->get()->count();

        $companies_data = Companies::where('id', '=', $company_id)->get();
        $short_name = $companies_data[0]->company_short_name;
        $new_row_count = $rows_count + 1;
        $inward_outward_no = $short_name . "/" . 'OUT' . "/" . date('Y/M/d') . "/" . $new_row_count;

        return response()->json($inward_outward_no);
    }

    public function companies_project(Request $request)
    {


        $company_id = $request->company_id;

        $projects = Projects::select('project.*')
            ->where('project.status', 'Enabled')
            ->where(function ($query) use ($company_id) {
                $query->where('project.company_id', $company_id);
                $query->orWhere('project.company_id', 0);
            })
            ->get();

        return response()->json($projects);
    }

    public function getNumPagesPdf($filepath)
    {
        $fp = @fopen(preg_replace("/\[(.*?)\]/i", "", $filepath), "r");
        $max = 0;
        if (!$fp) {
            return "Could not open file: $filepath";
        } else {
            while (!@feof($fp)) {
                $line = @fgets($fp, 255);
                if (preg_match('/\/Count [0-9]+/', $line, $matches)) {
                    preg_match('/[0-9]+/', $matches[0], $matches2);
                    if ($max < $matches2[0]) {
                        $max = trim($matches2[0]);
                        break;
                    }
                }
            }
            @fclose($fp);
        }

        return $max;
    }

    public function size_as_kb($yoursize)
    {
        if ($yoursize < 1024) {
            return "{$yoursize} bytes";
        } elseif ($yoursize < 1048576) {
            $size_kb = round($yoursize / 1024);
            return "{$size_kb} KB";
        } else {
            $size_mb = round($yoursize / 1048576, 1);
            return "{$size_mb} MB";
        }
    }

    public function search_sender_name(Request $request)
    {
        $name = $request->sender_name;
        $data = DB::table('inward_outward_sender')
            ->where('sender_name', 'LIKE', "%{$name}%")
            ->get();

        $output = '<ul class="dropdown-menu" style="display:block; position:relative">';
        foreach ($data as $row) {
            $output .= '
                <li><a href="javascript:void(0)">' . $row->sender_name . '</a></li>
                ';
        }
        $output .= '</ul>';
        //echo $output;
        return response()->json($output);
    }

    public function insert_inward(Request $request)
    {
        $validator_normal = Validator::make($request->all(), [
            'inward_outward_title' => 'required',
            'description' => 'required',
            'document_file' => 'required',
            'doc_category_id' => 'required',
            'doc_sub_category_id' => 'required',
            'company_id' => 'required',
            'project_id' => 'required',
            'doc_delivery_mode.*' => 'required',

            'delivery_mode' => 'required',
            //'delivery_file' => 'required',
            'sender_id' => 'required',
            'sender_name' => 'required',
            'sender_invoice_date' => 'required',
            'requested_by' => 'required',
        ]);

        if ($validator_normal->fails()) {
            
            \App\Test::insert(['test_type' => json_encode($validator_normal->messages())]);

            return redirect()->route('admin.add_inward')->with('error', 'Please follow validation rules.');
        }

        $email_user_ids = [];

        //21-02-2020
        //upload user document_file
        $document_file = '';
        if ($request->file('document_file')) {

            $document_file = $request->file('document_file');
            $original_file_name = explode('.', $document_file->getClientOriginalName());
            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);
            $file_path = $document_file->storeAs('public/document_file', $new_file_name);
            if ($file_path) {
                $document_file = $file_path;
            }
        }

        //upload user delivery_file
        $delivery_file = '';
        if ($request->file('delivery_file')) {

            $delivery_file = $request->file('delivery_file');
            $original_file_name = explode('.', $delivery_file->getClientOriginalName());
            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);
            $file_path = $delivery_file->storeAs('public/delivery_file', $new_file_name);
            if ($file_path) {
                $delivery_file = $file_path;
            }
        }

        $filepath = $request->file('document_file');
        $pageCount = $this->getNumPagesPdf($filepath);
        $file_size =  $request->file('document_file')->getSize();
        $doc_file_size  =  $this->size_as_kb($file_size);
        $registry = $request->input('registry');   //value registry no..
        $company_id = $request->input('company_id');

        //logic of inward_outward_no
        $receive_date = date('Y-m-d');
        $rows_count = Inward_outwards::whereDate('received_date', $receive_date)->where('company_id', $company_id)->where('type', 'Inwards')->get()->count();

        $companies_data = Companies::where('id', '=', $company_id)->get();
        $short_name = $companies_data[0]->company_short_name;
        $new_row_count = $rows_count + 1;
        $inward_outward_no = $short_name . "/" . 'INW' . "/" . date('Y/M/d') . "/" . $new_row_count;


        $project_id = $request->input('project_id');
        $checkDocType = Inward_outward_doc_category::where('id', $request->input('doc_category_id'))->where('is_special', 'Yes')->get();

        $doc_delivery_mode = [];
        foreach ($request->doc_delivery_mode as $value) {
            array_push($doc_delivery_mode, $value);
        }



        if (!empty($request->input('registry'))) {

            $inward_outward_data = Inward_outwards::where('id', '=', $registry)->get();
            $depart_ids = $inward_outward_data[0]->department_id;
            $inward_outward_id = $inward_outward_data[0]->parent_inward_outward_no;


            $inward_arr = [
                'inward_outward_title' => $request->input('inward_outward_title'),
                'inward_outward_no' => $inward_outward_no,
                'parent_inward_outward_no' => $inward_outward_id,

                'ref_outward_number' => $request->input('ref_outward_number'),
                'sender_comment' => $request->input('sender_comment'),

                'description' => $request->input('description'),
                'document_file' => !empty($document_file) ? $document_file : NULL,
                'doc_mark' => !empty($request->input('is_important')) ? 'Pending' : 'None',
                'type' => 'Inwards',
                'doc_category_id' => $request->input('doc_category_id'),
                'doc_sub_category_id' => $request->input('doc_sub_category_id'),
                'department_id' => $request->input('department_id'),
                'company_id' => $request->input('company_id'),
                'project_id' => $project_id,
                'other_project_details' => $request->input('other_details'),

                'inward_outward_delivery_mode_id' => $request->input('delivery_mode'),
                'received_date' => date('Y-m-d H:i:s'),
                'sender_id' => $request->input('sender_id'),
                'sender_name' => $request->input('sender_name'),
                'sender_invoice_date' => date('Y-m-d', strtotime($request->input('sender_invoice_date'))),
                'requested_by' => $request->input('requested_by'),
                'pdf_page_no' => $pageCount,
                'pdf_size' => $doc_file_size,
                'doc_allotment_time' => date('Y-m-d H:i:s'),
                'doc_delivery_mode' => implode(",", $doc_delivery_mode),
                //'delivery_file' => !empty($delivery_file) ? $delivery_file : NULL,
                'assign_employee_id' => $request->input('inward_user_id'),

                'is_reply' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'created_ip' => $request->ip(),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id,
                'inserted_by' => Auth::user()->id
            ];

            if ($request->file('delivery_file')) {
                $inward_arr['delivery_file'] = $delivery_file;
            }

            if ($request->input('ans_expected') == 'Yes') {

                $inward_arr['expected_ans_date'] = date('Y-m-d H:i:s', strtotime($request->input('expected_ans_date')));
                $inward_arr['is_answered'] = 'No';
            } else {
                $inward_arr['is_answered'] = 'Not Required';
            }


            $new_id = Inward_outwards::insertGetId($inward_arr);


            $last_outward_registry = Inward_outwards::where('parent_inward_outward_no', $inward_outward_data[0]->parent_inward_outward_no)
                ->where('type', 'Outwards')
                ->orderBy('id', 'DESC')
                ->first();
            //this inward is ans of outward registry selected. so we have to update parent outward registry
            if ($last_outward_registry) {

                $update_parent_outward_arr = [
                    'is_answered' => 'Yes',
                    'answered_date' => date('Y-m-d'),
                    'answered_outward_id' => $new_id,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                    'updated_by' => Auth::user()->id
                ];
                Inward_outwards::where('id', $last_outward_registry->id)->update($update_parent_outward_arr);
            }

            /*$user_data = Inward_outward_users::where('inward_outward_id', '=', $inward_outward_id)
                ->pluck('user_id')->toArray();

            if ($request->input('inward_user_id')) {
                $user_id_arr = explode(',', $request->input('inward_user_list'));

                $user_data = array_merge($user_data, $user_id_arr);
            }

            if ($request->input('ans_expected') == 'Yes' || !$checkDocType->isEmpty()) {

                $user_data = $this->common_task->setSuperUserId($user_data, 1);
            }*/


            // insert into Inward_outward_users table
            $user_data = [$request->input('inward_user_id')];
            foreach ($user_data as $key => $user) {

                $inward_users_arr = [
                    'inward_outward_id' => $new_id,
                    'user_id' => $user,
                    'status' => 'Processing',
                    'expected_ans_date' => date('Y-m-d H:i:s', strtotime($request->input('expected_ans_date'))),
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_ip' => $request->ip(),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                ];
                Inward_outward_users::insert($inward_users_arr);
                array_push($email_user_ids, $user);
            }

            // insert in Inward_outward_views table
            foreach ($user_data as $key => $user) {

                $inward_views_arr = [
                    'user_id' => $user,
                    'inward_outward_id' => $new_id,
                    'is_view' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_ip' => $request->ip(),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                ];
                Inward_outward_views::insert($inward_views_arr);
            }

            $sender_exist = Inward_outward_sender::where('sender_name', $request->input('sender_name'))->get();
            if ($sender_exist->count() == 0) {
                $search_sender = [
                    'sender_name' => $request->input('sender_name'),
                    'inward_outward_id' => $new_id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_ip' => $request->ip(),
                ];
                Inward_outward_sender::insert($search_sender);
            }
        } else {

            $inward_arr = [
                'inward_outward_title' => $request->input('inward_outward_title'),
                'inward_outward_no' => $inward_outward_no,

                'ref_outward_number' => $request->input('ref_outward_number'),
                'sender_comment' => $request->input('sender_comment'),

                'description' => $request->input('description'),
                'document_file' => !empty($document_file) ? $document_file : NULL,
                'doc_mark' => !empty($request->input('is_important')) ? 'Pending' : 'None',
                'type' => 'Inwards',
                'doc_category_id' => $request->input('doc_category_id'),
                'doc_sub_category_id' => $request->input('doc_sub_category_id'),
                'department_id' => $request->input('department_id'),
                'company_id' => $request->input('company_id'),
                'project_id' => $project_id,
                'other_project_details' => $request->input('other_details'),


                'inward_outward_delivery_mode_id' => $request->input('delivery_mode'),
                'received_date' => date('Y-m-d H:i:s'),
                'sender_id' => $request->input('sender_id'),
                'sender_name' => $request->input('sender_name'),
                'sender_invoice_date' => date('Y-m-d', strtotime($request->input('sender_invoice_date'))),
                'requested_by' => $request->input('requested_by'),
                'pdf_page_no' => $pageCount,
                'pdf_size' => $doc_file_size,
                'doc_allotment_time' => date('Y-m-d H:i:s'),
                'doc_delivery_mode' => implode(",", $doc_delivery_mode),
                //'delivery_file' => !empty($delivery_file) ? $delivery_file : NULL,
                'assign_employee_id' => $request->input('inward_user_id'),

                'is_reply' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'created_ip' => $request->ip(),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id,
                'inserted_by' => Auth::user()->id
            ];

            if ($request->file('delivery_file')) {
                $inward_arr['delivery_file'] = $delivery_file;
            }
            if ($request->input('ans_expected') == 'Yes') {
                $inward_arr['expected_ans_date'] = date('Y-m-d H:i:s', strtotime($request->input('expected_ans_date')));
                $inward_arr['is_answered'] = 'No';
            } else {
                $inward_arr['is_answered'] = 'Not Required';
            }


            $new_id = Inward_outwards::insertGetId($inward_arr);

            $new_inward_arr = [
                'parent_inward_outward_no' => $new_id,
                'created_at' => date('Y-m-d H:i:s'),
                'created_ip' => $request->ip(),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id
            ];
            Inward_outwards::where('id', $new_id)->update($new_inward_arr);

            /*$login_user_arr = [
                'inward_outward_id' => $new_id,
                'user_id' => Auth::user()->id,
                'status' => 'Processing',
                'expected_ans_date' => date('Y-m-d', strtotime($request->input('expected_ans_date'))),
                'created_at' => date('Y-m-d H:i:s'),
                'created_ip' => $request->ip(),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
            ];
            Inward_outward_users::insert($login_user_arr);*/

            //$user_id_arr = $request->input('inward_user_id');

            // $user_id_arr = explode(',', $request->input('inward_user_list'));

            // if ($request->input('ans_expected') == 'Yes' || !$checkDocType->isEmpty()) {

            //     $company_id = $request->input('company_id');
            //     $user_id_arr = $this->common_task->setSuperUserId($user_id_arr, 0);
            // }

            $user_id_arr = [$request->input('inward_user_id')];
            foreach ($user_id_arr as $user_id) {         //give for each to arry
                $inward_user_arr = [
                    'inward_outward_id' => $new_id,
                    'user_id' => $user_id,
                    'status' => 'Processing',
                    'first_assigned' => 1,
                    'expected_ans_date' => date('Y-m-d H:i:s', strtotime($request->input('expected_ans_date'))),
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_ip' => $request->ip(),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                ];

                Inward_outward_users::insert($inward_user_arr);
                array_push($email_user_ids, $user_id);
            }

            /*$login_user_views_arr = [
                'user_id' => Auth::user()->id,
                'inward_outward_id' => $new_id,
                'is_view' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'created_ip' => $request->ip(),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
            ];
            Inward_outward_views::insert($login_user_views_arr);*/

            foreach ($user_id_arr as $user_id) {
                $inward_views_arr = [
                    'user_id' => $user_id,
                    'inward_outward_id' => $new_id,
                    'is_view' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_ip' => $request->ip(),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                ];
                Inward_outward_views::insert($inward_views_arr);
            }

            $sender_exist = Inward_outward_sender::where('sender_name', $request->input('sender_name'))->get();
            if ($sender_exist->count() == 0) {
                $search_sender = [
                    'sender_name' => $request->input('sender_name'),
                    'inward_outward_id' => $new_id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_ip' => $request->ip(),
                ];
                Inward_outward_sender::insert($search_sender);
            }
        }

        //insert details inward_outwards_chat table
        $chat_arr = [
            'inward_outward_id' => $new_id,
            'from_user_id' => Auth::user()->id,
            'message' => 'Inwards',
            'message_type' => 'Document',
            'document_name' => !empty($document_file) ? $document_file : NULL,
            'created_at' => date('Y-m-d H:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
        ];

        Inward_outward_chat::insert($chat_arr);

        //send notification to assistant for notify check mark as important registry..
        /*if ($request->input('is_important') == "Pending") {
            $assistant_ids = user::where('status', 'Enabled')->where('role', config('constants.ASSISTANT'))->pluck('id')->toArray();
            $this->notification_task->markAsImpoNotify($assistant_ids, $inward_outward_no);
        }*/
        //send email and notification regarding inward to the users who are involved.
        $user_emails = User::whereIn('id', $email_user_ids)->get(['email'])->pluck('email')->toArray();
        $mail_data = [
            'inward_title' => $request->input('inward_outward_title'),
            'inward_number' => $inward_outward_no,
            'to_email_list' => $user_emails
        ];
        $this->common_task->newInwardAlertEmail($mail_data);
        //send notification to all users whom we had send emails
        $this->notification_task->inwardAddAlertNotify($email_user_ids, $inward_outward_no);
        return redirect()->route('admin.inwards')->with('success', 'New inward inserted successfully.');
    }

    public function insert_outward(Request $request)
    {
        $validator_normal = Validator::make($request->all(), [
            'inward_outward_title' => 'required',
            'description' => 'required',
            'document_file' => 'required',
            'doc_category_id' => 'required',
            'doc_sub_category_id' => 'required',
            'company_id' => 'required',
            'project_id' => 'required',
            'expected_ans_date' => 'required',

            'delivery_mode' => 'required',
            //'delivery_file' => 'required',
            'requested_by' => 'required',
        ]);

        if ($validator_normal->fails()) {
            
            \App\Test::insert(['test_type' => json_encode($validator_normal->messages())]);

            return redirect()->route('admin.add_outward')->with('error', 'Please follow validation rules.');
        }

        //21-02-2020
        //upload user document_file
        $document_file = '';
        if ($request->file('document_file')) {

            $document_file = $request->file('document_file');
            $original_file_name = explode('.', $document_file->getClientOriginalName());
            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);
            $file_path = $document_file->storeAs('public/document_file', $new_file_name);
            if ($file_path) {
                $document_file = $file_path;
            }
        }

        //upload user delivery_file
        $delivery_file = '';
        if ($request->file('delivery_file')) {

            $delivery_file = $request->file('delivery_file');
            $original_file_name = explode('.', $delivery_file->getClientOriginalName());
            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);
            $file_path = $delivery_file->storeAs('public/delivery_file', $new_file_name);
            if ($file_path) {
                $delivery_file = $file_path;
            }
        }

        $filepath = $request->file('document_file');
        $pageCount = $this->getNumPagesPdf($filepath);
        $file_size =  $request->file('document_file')->getSize();
        $doc_file_size  =  $this->size_as_kb($file_size);


        $registry = $request->input('registry');   //value registry no..
        $email_user_ids = [];
        $company_id = $request->input('company_id');
        $receive_date = date('Y-m-d');
        $rows_count = Inward_outwards::whereDate('received_date', $receive_date)->where('company_id', $company_id)->where('type', 'Outwards')->get()->count();
        //logic of inward_outward_no

        $companies_data = Companies::where('id', '=', $company_id)->get();

        $short_name = $companies_data[0]->company_short_name;
        $new_row_count = $rows_count + 1;
        $inward_outward_no = $short_name . "/" . 'OUT' . "/" . date('Y/M/d') . "/" . $new_row_count;

        $project_id = $request->input('project_id');
        $checkDocType = Inward_outward_doc_category::where('id', $request->input('doc_category_id'))->where('is_special', 'Yes')->get();

        if (!empty($request->input('registry'))) {

            $inward_outward_data = Inward_outwards::where('id', '=', $registry)->get();

            $depart_ids = $inward_outward_data[0]->department_id;

            $inward_outward_id = $inward_outward_data[0]->parent_inward_outward_no;

            $outward_arr = [
                'inward_outward_title' => $request->input('inward_outward_title'),
                'inward_outward_no' => $inward_outward_no,
                'parent_inward_outward_no' => $inward_outward_id,
                'description' => $request->input('description'),
                'document_file' => !empty($document_file) ? $document_file : NULL,
                'doc_mark' => !empty($request->input('is_important')) ? 'Pending' : 'None',
                'type' => 'Outwards',
                'doc_category_id' => $request->input('doc_category_id'),
                'doc_sub_category_id' => $request->input('doc_sub_category_id'),
                'department_id' => $request->input('department_id'),
                'company_id' => $request->input('company_id'),
                'project_id' => $project_id,
                'other_project_details' => $request->input('other_details'),


                'inward_outward_delivery_mode_id' => $request->input('delivery_mode'),
                'received_date' => date('Y-m-d H:i:s'),
                'requested_by' => $request->input('requested_by'),
                'pdf_page_no' => $pageCount,
                'pdf_size' => $doc_file_size,
                //'delivery_file' => !empty($delivery_file) ? $delivery_file : NULL,
                'assign_employee_id' => $request->input('inward_user_id'),

                'is_reply' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'created_ip' => $request->ip(),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id,
                'inserted_by' => Auth::user()->id
            ];

            if ($request->file('delivery_file')) {
                $outward_arr['delivery_file'] = $delivery_file;
            }
            if ($request->input('ans_expected') == 'Yes') {
                $outward_arr['expected_ans_date'] = date('Y-m-d H:i:s', strtotime($request->input('expected_ans_date')));
                $outward_arr['is_answered'] = 'No';
            } else {
                $outward_arr['is_answered'] = 'Not Required';
            }
            $new_id = Inward_outwards::insertGetId($outward_arr);

            //get last inward entry from this particular registry.
            $last_inward_registry = Inward_outwards::where('parent_inward_outward_no', $inward_outward_data[0]->parent_inward_outward_no)
                ->where('type', 'Inwards')
                ->orderBy('id', 'DESC')
                ->first();
            //this outward is ans of inward registry selected. so we have to update parent inward registry
            if ($last_inward_registry) {
                $update_parent_inward_arr = [
                    'is_answered' => 'Yes',
                    'answered_date' => date('Y-m-d'),
                    'answered_outward_id' => $new_id,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                    'updated_by' => Auth::user()->id
                ];
                Inward_outwards::where('id', $last_inward_registry->id)->update($update_parent_inward_arr);
            }
            /*$user_data = Inward_outward_users::where('inward_outward_id', '=', $inward_outward_id)
                ->pluck('user_id')->toArray();

            if ($request->input('inward_user_list')) {
                $user_id_arr = explode(',', $request->input('inward_user_list'));

                $user_data = array_merge($user_data, $user_id_arr);
            }

            if ($request->input('ans_expected') == 'Yes' || !$checkDocType->isEmpty()) {

                $user_data = $this->common_task->setSuperUserId($user_data, 1);
            }*/

            // insert in Inward_outward_users table
            $user_data = [$request->input('inward_user_id')];
            foreach ($user_data as $key => $user) {

                $inward_users_arr = [
                    'inward_outward_id' => $new_id,
                    'user_id' => $user,
                    'status' => 'Processing',
                    'expected_ans_date' => date('Y-m-d H:i:s', strtotime($request->input('expected_ans_date'))),
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_ip' => $request->ip(),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                ];
                Inward_outward_users::insert($inward_users_arr);
                array_push($email_user_ids, $user);
            }

            // insert in Inward_outward_views table
            foreach ($user_data as $key => $user) {
                $inward_views_arr = [
                    'user_id' => $user,
                    'inward_outward_id' => $new_id,
                    'is_view' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_ip' => $request->ip(),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                ];
                Inward_outward_views::insert($inward_views_arr);
            }
        } else {

            $outward_arr = [
                'inward_outward_title' => $request->input('inward_outward_title'),
                'inward_outward_no' => $inward_outward_no,
                'ref_outward_number' => $request->input('ref_outward_number'),
                'description' => $request->input('description'),
                'document_file' => !empty($document_file) ? $document_file : NULL,
                'doc_mark' => !empty($request->input('is_important')) ? 'Pending' : 'None',
                'type' => 'Outwards',
                'doc_category_id' => $request->input('doc_category_id'),
                'doc_sub_category_id' => $request->input('doc_sub_category_id'),
                'department_id' => $request->input('department_id'),
                'company_id' => $request->input('company_id'),
                'project_id' => $project_id,
                'other_project_details' => $request->input('other_details'),

                'inward_outward_delivery_mode_id' => $request->input('delivery_mode'),
                'received_date' => date('Y-m-d H:i:s'),
                'requested_by' => $request->input('requested_by'),
                'pdf_page_no' => $pageCount,
                'pdf_size' => $doc_file_size,
                //'delivery_file' => !empty($delivery_file) ? $delivery_file : NULL,
                'assign_employee_id' => $request->input('inward_user_id'),

                'is_reply' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'created_ip' => $request->ip(),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id,
                'inserted_by' => Auth::user()->id
            ];
            if ($request->file('delivery_file')) {
                $outward_arr['delivery_file'] = $delivery_file;
            }
            if ($request->input('ans_expected') == 'Yes') {
                $outward_arr['expected_ans_date'] = date('Y-m-d H:i:s', strtotime($request->input('expected_ans_date')));
                $outward_arr['is_answered'] = 'No';
            } else {
                $outward_arr['is_answered'] = 'Not Required';
            }
            $new_id = Inward_outwards::insertGetId($outward_arr);

            $new_outward_arr = [
                'parent_inward_outward_no' => $new_id,
                'created_at' => date('Y-m-d H:i:s'),
                'created_ip' => $request->ip(),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id
            ];

            Inward_outwards::where('id', $new_id)->update($new_outward_arr);

            /*$login_user_arr = [
                'inward_outward_id' => $new_id,
                'user_id' => Auth::user()->id,
                'status' => 'Processing',
                'expected_ans_date' => date('Y-m-d', strtotime($request->input('expected_ans_date'))),
                'created_at' => date('Y-m-d H:i:s'),
                'created_ip' => $request->ip(),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
            ];
            Inward_outward_users::insert($login_user_arr);*/

            //$user_id_arr = $request->input('inward_user_id');
            $company_id = $request->input('company_id');
            /*$user_id_arr = explode(',', $request->input('inward_user_list'));

            if ($request->input('ans_expected') == 'Yes' || !$checkDocType->isEmpty()) {

                $user_id_arr = $this->common_task->setSuperUserId($user_id_arr, 0);
            }*/

            $user_id_arr = [$request->input('inward_user_id')];
            foreach ($user_id_arr as $user_id) {         //give for each to arry
                $outward_user_arr = [
                    'inward_outward_id' => $new_id,
                    'user_id' => $user_id,
                    'status' => 'Processing',
                    'first_assigned' => 1,
                    'expected_ans_date' => date('Y-m-d H:i:s', strtotime($request->input('expected_ans_date'))),
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_ip' => $request->ip(),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                ];

                Inward_outward_users::insert($outward_user_arr);
                array_push($email_user_ids, $user_id);
            }

            /*$login_user_views_arr = [
                'user_id' => Auth::user()->id,
                'inward_outward_id' => $new_id,
                'is_view' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'created_ip' => $request->ip(),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
            ];
            Inward_outward_views::insert($login_user_views_arr);*/

            foreach ($user_id_arr as $user_id) {

                $outward_views_arr = [
                    'user_id' => $user_id,
                    'inward_outward_id' => $new_id,
                    'is_view' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_ip' => $request->ip(),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                ];
                Inward_outward_views::insert($outward_views_arr);
            }
        }

        //insert details inward_outwards_chat table
        $chat_arr = [
            'inward_outward_id' => $new_id,
            'from_user_id' => Auth::user()->id,
            'message' => 'Outwards',
            'message_type' => 'Document',
            'document_name' => !empty($document_file) ? $document_file : NULL,
            'created_at' => date('Y-m-d H:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
        ];

        Inward_outward_chat::insert($chat_arr);

        //send notification to assistant for notify check mark as important registry..
        /*if ($request->input('is_important') == "Pending") {
            $assistant_ids = user::where('status', 'Enabled')->where('role', config('constants.ASSISTANT'))->pluck('id')->toArray();
            $this->notification_task->markAsImpoNotify($assistant_ids, $inward_outward_no);
        }*/


        //send email and notification regarding inward to the users who are involved.
        $user_emails = User::whereIn('id', $email_user_ids)->get(['email'])->pluck('email')->toArray();
        $mail_data = [
            'outward_title' => $request->input('inward_outward_title'),
            'outward_number' => $inward_outward_no,
            'to_email_list' => $user_emails
        ];
        $this->common_task->newOutwardAlertEmail($mail_data);
        //send notification to all users whom we had send emails
        $this->notification_task->outwardAddAlertNotify($email_user_ids, $inward_outward_no);

        return redirect()->route('admin.outwards')->with('success', 'New Outward inserted successfully.');
    }

    public function view_inward_to_outward($id, $type)
    {
        // dd($type);
        if($type == "inward"){
            $this->data['page_title'] = "View Inward";
            $this->data['module_title'] = "Inwards";
            $this->data['module_link'] = "admin.inwards";
        }else{
            $this->data['page_title'] = "View Outward";
            $this->data['module_title'] = "Outward";
            $this->data['module_link'] = "admin.outwards";
        }
        

        $inward_select_fields = [
            'inward_outward_delivery_mode.name as delivery_mode_name', 'sender.name as sender_type', 'inward_outwards.sender_comment',
            'inward_outwards.sender_name', 'inward_outwards.sender_invoice_date', 'inward_outwards.requested_by', 'users.name as requested_by_name',
            'inward_outwards.pdf_page_no', 'inward_outwards.pdf_size', 'inward_outwards.doc_allotment_time', 'department.dept_name',
            'inward_outwards.doc_delivery_mode', 'inward_outwards.delivery_file', 'inward_outward_doc_sub_category.sub_category_name',
            'inward_outwards.id', 'inward_outwards.doc_mark', 'inward_outwards.inward_outward_title', 'inward_outwards.is_answered', 'inward_outwards.inward_outward_no', 'inward_outwards.ref_outward_number', 'inward_outwards.description', 'inward_outwards.document_file', 'inward_outwards.other_project_details', 'inward_outwards.type', 'inward_outwards.received_date', 'inward_outwards.expected_ans_date',
            'inward_outward_doc_category.category_name', 'company.company_name', 'project.project_name', 'inward_outwards.parent_inward_outward_no', 'prime_user.name as prime_username'
        ];

        $this->data['received_dates'] = $received_dates = Inward_outwards::select(
            DB::raw("DATE(inward_outwards.received_date)  AS received_date")
        )->join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
            //->where('inward_outward_users.user_id', '=', Auth::user()->id)
            ->where('inward_outwards.parent_inward_outward_no', '=', $id)
            ->orderBy('inward_outwards.received_date', 'asc')
            ->distinct()
            ->pluck('received_date')->toArray();

        // associative array
        $all_inward_outward_data = [];

        foreach ($received_dates as $date) {

            $this->data['inwards_data'] = $inwards_data = Inward_outwards::join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
                ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
                ->join('company', 'company.id', '=', 'inward_outwards.company_id')
                ->join('project', 'project.id', '=', 'inward_outwards.project_id')
                ->leftJoin('users', 'users.id', '=', 'inward_outwards.requested_by')
                ->leftJoin('sender', 'sender.id', '=', 'inward_outwards.sender_id')
                ->leftJoin('users as prime_user', 'prime_user.id', '=', 'inward_outwards.prime_employee_id')
                ->leftJoin('department', 'department.id', '=', 'inward_outwards.department_id')
                ->leftJoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
                ->join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
                ->leftJoin('inward_outward_prime_action', 'inward_outward_prime_action.inward_outward_id', '=', 'inward_outwards.id')
                //->where('inward_outward_users.user_id', '=', Auth::user()->id)
                ->whereDate('inward_outwards.received_date', '=', $date)
                ->where('inward_outwards.parent_inward_outward_no', '=', $id)
                ->get($inward_select_fields); //->toArray();

            $all_inward_outward_data[$date] = $inwards_data;
        }
        //dd($all_inward_outward_data);

        $this->data['all_inward_outward_data'] = $all_inward_outward_data;

        $inward_outward_ids = Inward_outwards::where('parent_inward_outward_no', $id)->pluck('id')->toArray();
        $users_details = [];
        $supprt_employee_list = [];

        foreach ($inward_outward_ids as $ids) {
            $users_data = Inward_outward_users::select(array('inward_outward_users.user_id', 'inward_outward_users.status', 'users.name'))
                ->join('users', 'users.id', '=', 'inward_outward_users.user_id')
                ->where('inward_outward_users.inward_outward_id', '=', $ids)->get(); //->toArray();

            $support_emp = Inward_outward_prime_action::with(['emp_distrubution' => function ($query) {
                $query->leftjoin('users', 'users.id', '=', 'inward_outward_distrubuted_work.support_employee_id')
                    ->groupBy("inward_outward_distrubuted_work.id")
                    ->select(['inward_outward_prime_action_id', 'users.id', 'users.name', 'emp_status', 'work_status']);
            }])
                ->where('inward_outward_prime_action.inward_outward_id', $ids)
                ->get(['inward_outward_prime_action.id', 'inward_outward_prime_action.inward_outward_id'])->toArray();

            $users_details[$ids] = $users_data;
            $supprt_employee_list[$ids] = empty($support_emp) ? [] :  $support_emp[0]['emp_distrubution'];
        }

        $this->data['users_details'] = $users_details;
        $this->data['support_users_details'] = $supprt_employee_list;

        //dd($supprt_employee_list);
        //dd($users_details);
        return view('admin.user.view_inward_details', $this->data);
    }

    public function view_outward_to_inward($id)
    {
        $this->data['page_title'] = "View Outward";
        $this->data['module_title'] = "Outwards";

        // $inward_select_fields = [
        //     'inward_outward_delivery_mode.name as delivery_mode_name','inward_outwards.sender_comment',
        //     'inward_outwards.sender_name','inward_outwards.sender_invoice_date','inward_outwards.requested_by','users.name as requested_by_name',
        //     'inward_outwards.pdf_page_no','inward_outwards.pdf_size','inward_outwards.doc_allotment_time','department.dept_name',
        //     'inward_outwards.doc_delivery_mode','inward_outwards.delivery_file','inward_outward_doc_sub_category.sub_category_name',

        //     'inward_outwards.id', 'inward_outwards.doc_mark', 'inward_outwards.inward_outward_title', 'inward_outwards.is_answered', 'inward_outwards.inward_outward_no', 'inward_outwards.ref_outward_number', 'inward_outwards.description', 'inward_outwards.document_file', 'inward_outwards.other_project_details', 'inward_outwards.type', 'inward_outwards.received_date', 'inward_outwards.expected_ans_date',
        //     'inward_outward_doc_category.category_name', 'company.company_name', 'project.project_name', 'inward_outwards.parent_inward_outward_no'
        // ];

        $inward_select_fields = [
            'inward_outward_delivery_mode.name as delivery_mode_name', 'sender.name as sender_type', 'inward_outwards.sender_comment',
            'inward_outwards.sender_name', 'inward_outwards.sender_invoice_date', 'inward_outwards.requested_by', 'users.name as requested_by_name',
            'inward_outwards.pdf_page_no', 'inward_outwards.pdf_size', 'inward_outwards.doc_allotment_time', 'department.dept_name',
            'inward_outwards.doc_delivery_mode', 'inward_outwards.delivery_file', 'inward_outward_doc_sub_category.sub_category_name',
            'inward_outwards.id', 'inward_outwards.doc_mark', 'inward_outwards.inward_outward_title', 'inward_outwards.is_answered', 'inward_outwards.inward_outward_no', 'inward_outwards.ref_outward_number', 'inward_outwards.description', 'inward_outwards.document_file', 'inward_outwards.other_project_details', 'inward_outwards.type', 'inward_outwards.received_date', 'inward_outwards.expected_ans_date',
            'inward_outward_doc_category.category_name', 'company.company_name', 'project.project_name', 'inward_outwards.parent_inward_outward_no'
        ];

        $this->data['received_dates'] = $received_dates = Inward_outwards::select(DB::raw("DATE(inward_outwards.received_date)  AS received_date"))
            ->join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
            //->where('inward_outward_users.user_id', '=', Auth::user()->id)
            ->where('inward_outwards.parent_inward_outward_no', '=', $id)
            ->orderBy('inward_outwards.received_date', 'asc')
            ->distinct()
            ->pluck('inward_outwards.received_date')->toArray();

        // associative array
        $all_inward_outward_data = [];

        foreach ($received_dates as $date) {

            // $this->data['outward_data'] = $outward_data = Inward_outwards::join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
            //     ->join('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
            //     ->join('company', 'company.id', '=', 'inward_outwards.company_id')
            //     ->join('project', 'project.id', '=', 'inward_outwards.project_id')
            //     ->join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
            //     ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
            //     ->join('department', 'department.id', '=', 'inward_outwards.department_id')
            //     ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
            //     //->where('inward_outward_users.user_id', '=', Auth::user()->id)
            //     ->whereDate('inward_outwards.received_date', '=', $date)
            //     ->where('inward_outwards.parent_inward_outward_no', '=', $id)
            //     ->get($inward_select_fields); //->toArray();

            // $all_inward_outward_data[$date] = $outward_data;
            $this->data['outward_data'] = $outward_data = Inward_outwards::join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
                ->join('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
                ->join('company', 'company.id', '=', 'inward_outwards.company_id')
                ->join('project', 'project.id', '=', 'inward_outwards.project_id')
                ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
                ->leftjoin('sender', 'sender.id', '=', 'inward_outwards.sender_id')
                ->join('department', 'department.id', '=', 'inward_outwards.department_id')
                ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
                ->join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
                //->where('inward_outward_users.user_id', '=', Auth::user()->id)
                ->whereDate('inward_outwards.received_date', '=', $date)
                ->where('inward_outwards.parent_inward_outward_no', '=', $id)
                ->get($inward_select_fields); //->toArray();

            $all_inward_outward_data[$date] = $outward_data;
        }

        $this->data['all_inward_outward_data'] = $all_inward_outward_data;

        $inward_outward_ids = Inward_outwards::where('parent_inward_outward_no', $id)->pluck('id')->toArray();
        $users_details = [];

        foreach ($inward_outward_ids as $ids) {
            $this->data['users_data'] = $users_data = Inward_outward_users::select(array('inward_outward_users.user_id', 'inward_outward_users.status', 'users.name'))
                ->join('users', 'users.id', '=', 'inward_outward_users.user_id')
                ->where('inward_outward_users.inward_outward_id', '=', $ids)->get(); //->toArray();

            $users_details[$ids] = $users_data;
        }

        $this->data['users_details'] = $users_details;

        return view('admin.user.view_inward_details', $this->data);
    }

    //pass_registry

    public function pass_registry(Request $request, $parent_id, $id)
    {

        $inward_user_arr = [
            'status' => 'Completed',
            'created_at' => date('Y-m-d H:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
        ];

        $status = Inward_outward_users::where('user_id', Auth::user()->id)->where('inward_outward_id', $id)
            ->value('status');

        if ($status == 'Processing') {
            Inward_outward_users::where('user_id', Auth::user()->id)->where('inward_outward_id', $id)
                ->update($inward_user_arr);

            $first_user_list = Inward_outward_users::where('inward_outward_id', $id)
                ->where('status', 'Pending')
                ->limit(1)->get()->toArray();

            if (empty($first_user_list)) {
                return redirect()->route('admin.view_inward_to_outward', $parent_id)->with('success', 'Registry Passed Successfully!.');
            }
            $first_user_arr = [
                'status' => 'Processing',
                'created_at' => date('Y-m-d H:i:s'),
                'created_ip' => $request->ip(),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
            ];

            Inward_outward_users::where('user_id', $first_user_list[0]['user_id'])
                ->where('inward_outward_id', $id)
                ->update($first_user_arr);
        } else {

            return redirect()->route('admin.view_inward_to_outward', $parent_id)->with('error', 'Access Denied. You are not authorized to access !.');
        }

        return redirect()->route('admin.view_inward_to_outward', $parent_id)->with('success', 'Registry Passed Successfully!.');
    }

    //registry Chat..
    public function registry_chat($id)
    {
        $this->data['page_title'] = "Registry Chat";
        $this->data['module_title'] = "Inwards";

        $this->data['messages'] = $messages = Inward_outward_chat::select('inward_outward_chat.*', 'users.name', 'users.profile_image')->join('users', 'inward_outward_chat.from_user_id', '=', 'users.id')
            ->leftJoin('inward_outwards', 'inward_outwards.id', '=', 'inward_outward_chat.inward_outward_id')
            ->where('inward_outwards.parent_inward_outward_no', '=', $id)
            ->orWhere('inward_outward_chat.inward_outward_id', '=', $id)
            ->get();  //->toArray();

        $this->data['inward_outward_id'] = $id;
        $this->data['registry_title'] = $registry_title = Inward_outwards::where('id', $id)->value('inward_outward_title');
        return view('admin.user.registry_chat', $this->data);
    }

    //send_message
    public function send_message(Request $request)
    {


        $validator_normal = Validator::make($request->all(), [
            'message' => 'required'
        ]);

        if ($validator_normal->fails()) {
            return redirect()->route('admin.registry_chat')->with('error', 'Please follow validation rules.');
        }

        $message = $request->message;
        $registry_id = $request->input('inward_outward_id');

        $registry_data = Inward_outwards::where('id', $registry_id)->get();

        $chat_arr = [
            'inward_outward_id' => $registry_id,
            'from_user_id' => Auth::user()->id,
            'message' => $message,
            'message_type' => 'Text',
            'created_at' => date('Y-m-d H:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
        ];



        $message_id = Inward_outward_chat::insertGetId($chat_arr);

        $user_message_arr = [
            'inward_outward_id' => $registry_id,
            'message_id' => $message_id,
            'user_id' => Auth::user()->id,
            'is_read' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip()
        ];

        Inward_outward_message_view::insert($user_message_arr);

        $user_ids = Inward_outward_users::where('inward_outward_id', '=', $registry_id)
            ->where('user_id', '!=', Auth::user()->id)
            ->pluck('user_id')->toArray();



        foreach ($user_ids as $user_id) {


            $other_user_message_arr = [
                'inward_outward_id' => $registry_id,
                'message_id' => $message_id,
                'user_id' => $user_id,
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'created_ip' => $request->ip(),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip()
            ];
            Inward_outward_message_view::insert($other_user_message_arr);

            $this->notification_task->registryMessageNotify($user_ids, $message, $registry_data[0]->inward_outward_title);
        }
        $html_body = '<li class="odd">

        <div class="chat-body">
            <div class="chat-text" style="background:#6CBDF5">
                <h4>' . Auth::user()->name . '</h4>
                <p> ' . $message . ' </p>
                <b>' . date('d-m-Y h:i A') . '</b>
            </div>
        </div>
        </li>';
        return json_encode(array(
            "statusCode" => 200,
            "html_body" => $html_body
        ));
    }

    public function get_inward_pending_list()
    {
        $inward_list = Inward_outwards::join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
            ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
            ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
            ->where('inward_outward_users.user_id', \Illuminate\Support\Facades\Auth::user()->id)
            ->where('inward_outward_users.status', 'Processing')
            ->get([
                'inward_outwards.*', 'inward_outward_doc_category.category_name',
                'inward_outward_doc_sub_category.sub_category_name'
            ]);

        return response()->json(['status' => true, 'msg' => 'record found', 'data' => $inward_list]);
    }

    //===================================== Registry Assigned To You  ================================

    public function assignee_registry(Request $request)
    {

        $this->data['page_title'] = 'Registry Assigned To You';
        $this->data['module_title'] = "Inward Outward";

        $this->data['assignee_registry'] =  Inward_outwards::join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
            ->whereDate('inward_outwards.created_at', '>=', '2020-06-02')
            ->where('inward_outward_users.status', '=', 'Processing')
            ->where('inward_outward_users.user_id', '=', Auth::user()->id)->get(['inward_outwards.*']);

        return view('admin.user.assignee_registry', $this->data);
    }

    public function accept_registry(Request $request, $id)
    {
        $update_arr = [
            'status' => 'Completed',
            'action_date' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip()
        ];
        try {
            $cc_email_list = [];
            Inward_outward_users::where('inward_outward_id', $id)->update($update_arr);

            $inward_details = Inward_outwards::where('id', $id)->get(['inward_outward_no', 'inserted_by', 'requested_by']);
            $requested_by = User::where('id', $inward_details[0]->requested_by)->value('email');
            $superUser = User::where('status', 'Enabled')->where('role', config('constants.SuperUser'))->value('email');
            $inserted_by = User::where('id', $inward_details[0]->inserted_by)->pluck('email')->toArray();


            array_push($cc_email_list, $superUser, $requested_by);


            $mail_data = [

                'registry_no' => $inward_details[0]->inward_outward_no,
                'user_name' => Auth::user()->name,
                'email_list' => $inserted_by,
                'cc_email_list' => $cc_email_list
            ];

            $this->common_task->acceptDocumentRegistry($mail_data);

            return redirect()->route('admin.hardcopy')->with('success', 'Registry Document Accepted Successfully.');
        } catch (Exception $exc) {

            return redirect()->route('admin.assignee_registry')->with('error', 'Error Occurred. Try Again!');
        }
    }

    public function reject_registry(Request $request)
    {
        $validator_normal = Validator::make($request->all(), [
            'registry_id' => 'required',
            'reject_note' => 'required'
        ]);

        if ($validator_normal->fails()) {
            return redirect()->route('admin.assignee_registry')->with('error', 'Please follow validation rules.');
        }

        $update_arr = [
            'status' => 'Rejected',
            'reject_note' => $request->input('reject_note'),
            'action_date' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip()
        ];
        try {

            $cc_email_list = [];
            Inward_outward_users::where('inward_outward_id', $request->input('registry_id'))->update($update_arr);

            $inward_details = Inward_outwards::where('id', $request->input('registry_id'))->get(['inward_outward_no', 'inserted_by', 'requested_by']);
            $requested_by = User::where('id', $inward_details[0]->requested_by)->value('email');
            $superUser = User::where('status', 'Enabled')->where('role', config('constants.SuperUser'))->value('email');
            $inserted_by = User::where('id', $inward_details[0]->inserted_by)->pluck('email')->toArray();


            array_push($cc_email_list, $superUser, $requested_by);


            $mail_data = [

                'registry_no' => $inward_details[0]->inward_outward_no,
                'user_name' => Auth::user()->name,
                'email_list' => $inserted_by,
                'cc_email_list' => $cc_email_list
            ];

            $this->common_task->rejectDocumentRegistry($mail_data);


            return redirect()->route('admin.assignee_registry')->with('success', 'Registry Document Rejected Successfully.');
        } catch (Exception $exc) {
            return redirect()->route('admin.assignee_registry')->with('error', 'Error Occurred. Try Again!');
        }
    }


    //========================================= Action Process ==================================================
    public function prelimary_action_list()
    {
        $this->data['page_title'] = "Action Required Inwards";

        $this->data['process_list'] = Inward_outwards::leftjoin('users', 'users.id', '=', 'inward_outwards.prime_employee_id')
            ->leftjoin('department', 'department.id', '=', 'inward_outwards.work_allotment_department_id')
            ->join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
            ->where('inward_outwards.is_answered', 'No')
            ->where('inward_outward_users.status', 'Completed')
            ->where('inward_outward_users.user_id', Auth::user()->id)
            ->whereDate('inward_outwards.created_at', '>=', '2020-06-02')
            ->get(['inward_outwards.inward_outward_no', 'users.name as prime_employee_name', 'inward_outwards.prime_user_status', 'inward_outwards.reject_reason', 'inward_outwards.id', 'department.dept_name', 'inward_outwards.expected_ans_date', 'inward_outwards.querry_details', 'inward_outwards.inward_outward_title', 'inward_outwards.document_file']);
        // dd($this->data['process_list']);
        return view('admin.action_process.index', $this->data);
    }

    // not in use
    public function add_prelimary_process(Request $request)
    {

        $this->data['page_title'] = "Add Prelimary Action Details";
        $this->data['module_title'] = "Action Required Inwards";
        $this->data['module_link'] = "admin.prelimary_action_list";
        $this->data['department_category'] = Department::select('id', 'dept_name')->get();

        $this->data['action_data'] = Inward_outwards::where('id', $id)->value('expected_ans_date');

        return view('admin.action_process.add_process', $this->data);
    }

    //not in use
    public function insert_prelimary_process(Request $request)
    {
        $rules = array(
            'work_allotment_department_id' => 'required',
            'querry_details' => 'required',
            'prime_employee_id' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return redirect()->route('admin.prelimary_action_list')->with('error', 'Error during operation. Try again!');
        }

        $insert_arr = [
            'work_allotment_department_id' => $request->input('work_allotment_department_id'),
            'querry_details' => $request->input('querry_details'),
            'prime_employee_id' => $request->input('prime_employee_id'),
            'prime_user_status' => 'Assigned',
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip()
        ];

        Inward_outwards::insert($insert_arr);

        return redirect()->route('admin.prelimary_action_list')->with('success', 'Record successfully Inserted!.');
    }

    public function edit_prelimary_process($id)
    {
        $this->data['page_title'] = "Edit Prelimary Action Details";
        $this->data['module_title'] = "Action Required Inwards";
        $this->data['module_link'] = "admin.prelimary_action_list";
        $this->data['department_category'] = Department::select('id', 'dept_name')->orderBy('dept_name')->get();
        $this->data['action_data'] = Inward_outwards::where('id', $id)->get(['inward_outwards.id', 'inward_outwards.work_allotment_department_id', 'inward_outwards.prime_employee_id', 'inward_outwards.expected_ans_date', 'inward_outwards.querry_details']);

        return view('admin.action_process.edit_process', $this->data);
    }

    public function update_prelimary_process(Request $request)
    {
        $rules = array(
            'work_allotment_department_id' => 'required',
            'querry_details' => 'required',
            'prime_employee_id' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        $id = $request->input('id');
        if ($validator->fails()) {
            return redirect()->route('admin.edit_prelimary_process', $id)->with('error', 'Error during operation. Try again!');
        }

        $update_arr = [
            'work_allotment_department_id' => $request->input('work_allotment_department_id'),
            'querry_details' => $request->input('querry_details'),
            'prime_employee_id' => $request->input('prime_employee_id'),
            'prime_user_status' => 'Assigned',
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip()
        ];

        Inward_outwards::where('id', $request->input('id'))->update($update_arr);

        return redirect()->route('admin.prelimary_action_list')->with('success', 'Record successfully Updated!.');
    }

    //========================================= Prime action Process ==================================================

    public function prime_action_list()
    {
        $this->data['page_title'] = "Prime Action Inwards";
        $this->data['module_title'] = "Inward Outward";
        $this->data['module_link'] = "admin.inward_outward";

        $this->data['prime_list'] = $prime_list = Inward_outward_prime_action::rightjoin('inward_outwards', 'inward_outwards.id', '=', 'inward_outward_prime_action.inward_outward_id')
            ->with(['emp_distrubution' => function ($query) {
                $query->leftjoin('users', 'users.id', '=', 'inward_outward_distrubuted_work.support_employee_id')
                    ->leftjoin("department", \DB::raw("FIND_IN_SET(department.id,inward_outward_distrubuted_work.support_department_id)"), ">", \DB::raw("'0'"))
                    ->groupBy("inward_outward_distrubuted_work.id")
                    ->select(['inward_outward_prime_action_id', 'support_department_id', 'support_employee_id', 'users.name', 'task_percentage', 'task_hour', 'inward_outward_distrubuted_work.work_hour', 'emp_status', \DB::raw("GROUP_CONCAT(department.dept_name) as depart_name")]);
            }])
            ->where('inward_outwards.prime_user_status', '!=', 'Rejected')
            ->where('inward_outwards.prime_employee_id', Auth::user()->id)
            ->whereDate('inward_outwards.created_at', '>=', '2020-06-02')
            ->get([
                'inward_outwards.id as inward_id', 'inward_outwards.inward_outward_no', 'inward_outwards.prime_user_status',
                'inward_outward_prime_action.*', 'inward_outward_prime_action.assume_work_type',
                'inward_outward_prime_action.assume_work_time', 'inward_outward_prime_action.assume_work_hour', 'inward_outward_prime_action.final_status',
                'inward_outward_prime_action.work_details', 'inward_outwards.inward_outward_title', 'inward_outwards.document_file'
            ]);

        //dd($prime_list->toArray());

        $this->data['requests_list'] = $request_list = Inward_outward_distrubuted_work::join('inward_outward_prime_action', 'inward_outward_prime_action.id', '=', 'inward_outward_distrubuted_work.inward_outward_prime_action_id')
            ->join('inward_outwards', 'inward_outwards.id', '=', 'inward_outward_prime_action.inward_outward_id')
            ->join('users', 'users.id', '=', 'inward_outwards.prime_employee_id')
            ->where('inward_outward_distrubuted_work.support_employee_id', '=', Auth::user()->id)
            ->orWhere('inward_outward_distrubuted_work.reliever_user_id', '=', Auth::user()->id)
            ->whereDate('inward_outwards.created_at', '>=', '2020-06-02')
            ->get([
                'users.name as prime_user', 'inward_outward_distrubuted_work.id', 'inward_outwards.inward_outward_no',
                'inward_outward_distrubuted_work.work_status', 'inward_outward_distrubuted_work.working_start_datetime',
                'inward_outward_distrubuted_work.task_hour', 'inward_outward_distrubuted_work.emp_status',
                'inward_outward_distrubuted_work.task_percentage', 'inward_outward_distrubuted_work.work_day', 'inward_outward_distrubuted_work.support_employee_id',
                'inward_outward_prime_action.work_details', 'inward_outward_distrubuted_work.reliever_dates', 'inward_outward_distrubuted_work.reliever_user_id', 'inward_outwards.inward_outward_title', 'inward_outwards.document_file'
            ]);

        foreach ($request_list as $key => $value) {
            if ($value->reliever_dates) {
                $request_list[$key]->reliever_dates = json_decode($value->reliever_dates);
            }
        }
        // dd($this->data['prime_list']);
        return view('admin.action_process.prime_action', $this->data);
    }

    public function reject_requestByPrimeUser(Request $request)
    {

        $validator_normal = Validator::make($request->all(), [
            'inward_id' => 'required',
            'reject_reason' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.prime_action_list')->with('error', 'Please follow validation rules.');
        }

        $id = $request->input('inward_id');

        $reject_arr = [
            'prime_user_status' => 'Rejected',
            'reject_reason' => $request->input('reject_reason'),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip()
        ];

        if (Inward_outwards::where('id', $id)->update($reject_arr)) {


            $inward_data = Inward_outwards::where('id', $id)->get(['inward_outward_no', 'inserted_by']);
            $mail = User::where('id', $inward_data[0]->inserted_by)->pluck('email')->toArray();
            $data = [
                'user_name' => Auth::user()->name,
                'reason' => $request->input('reject_reason'),
                'registry_no' => $inward_data[0]->inward_outward_no,
                'date' => date('d-m-Y H:i a'),
                'email_list' => $mail
            ];
            $this->common_task->rejectRegistryDocumentPrimeUser($data);

            return redirect()->route('admin.prime_action_list')->with('success', 'Registry Document succesfully rejected');
        }
        return redirect()->route('admin.prime_action_list')->with('error', 'Error during operation. Try again!');
    }

    public function accept_requestByPrimeUser($id, Request $request)
    {  //nish

        $update_arr = [
            'prime_user_status' => 'Accepted',
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip()
        ];

        if (Inward_outwards::where('id', $id)->update($update_arr)) {

            $insert_arr = [
                'inward_outward_id' =>  $id,
                'created_at' => date('Y-m-d H:i:s'),
                'created_ip' => $request->ip(),
                'updated_by' => Auth::user()->id
            ];
            Inward_outward_prime_action::insert($insert_arr);

            $inward_data = Inward_outwards::where('id', $id)->get(['inward_outward_no', 'inserted_by']);
            $mail = User::where('id', $inward_data[0]->inserted_by)->pluck('email')->toArray();
            $data = [
                'user_name' => Auth::user()->name,
                'registry_no' => $inward_data[0]->inward_outward_no,
                'date' => date('d-m-Y H:i a'),
                'email_list' => $mail
            ];

            $this->common_task->acceptRegistryDocumentPrimeUser($data);

            return redirect()->route('admin.prime_action_list')->with('success', 'Registry Document succesfully accepted.');
        }
        return redirect()->route('admin.prime_action_list')->with('error', 'Error during operation. Try again!');
    }

    public function add_distrubuted_details($id)
    {


        $this->data['page_title'] = "Edit Prime Action Details";
        $this->data['module_title'] = "Prime Action Inwards";
        $this->data['module_link'] = "admin.prime_action_list";


        $edit_data = Inward_outward_prime_action::where('id', $id)->get();
        //$task_percentage_name = [];
        foreach ($edit_data as $key => $value) {
            $support_employee_id = Inward_outward_distrubuted_work::where('inward_outward_prime_action_id', '=', $value->id)
                ->pluck('support_employee_id')->toArray();
            $support_department_id = Inward_outward_distrubuted_work::where('inward_outward_prime_action_id', '=', $value->id)
                ->first();
            $task_percentage = Inward_outward_distrubuted_work::where('inward_outward_prime_action_id', '=', $value->id)
                ->get(['task_percentage', 'task_hour'])->toArray();

            foreach ($support_employee_id as $index => $userId) {
                $task_percentage[$index]['user_name'] = User::where('id', $userId)->value('name');
            }

            // if (empty($support_employee_id) || count($support_employee_id) == 1) {
            //     $support_emp = [];
            //     $support_depart = [];
            //     $support_task_percentage = [];
            // } else {
            //     $support_depart = explode(",", $support_department_id['support_department_id']);
            // }

            $edit_data[$key]->support_employee_id =  count($support_employee_id) <= 1 ? [] : $support_employee_id;
            $edit_data[$key]->support_department_id = count($support_employee_id) <= 1 ? [] : explode(",", $support_department_id['support_department_id']);
            $edit_data[$key]->task_percentage = count($support_employee_id) <= 1 ? [] : $task_percentage;
        }

        //dd($edit_data->toArray());

        $this->data['edit_data'] = $edit_data;
        $this->data['work_mode'] = ['Week', 'Days', 'Hour'];
        $this->data['user'] = User::getUser();
        $this->data['department_category'] = Department::select('id', 'dept_name')->get();
        $this->data['auth_name'] = Auth::user()->name;

        return view('admin.action_process.add_distrubution_task', $this->data);
    }

    public function update_prime_process(Request $request)
    {
        $rules = array(
            'id' => 'required',
            'assume_work_type' => 'required',
            'assume_work_time' => 'required',
            //'support_departments.*' => 'required',
            //'support_employees.*' => 'required',
            //'task_percentage.*' => 'required',
            //'work_hour.*' => 'required',
        );

        $validator = Validator::make(Input::all(), $rules);
        $id = $request->input('id');
        if ($validator->fails()) {
            return redirect()->route('admin.add_distrubuted_details', $id)->with('error', 'Please follow validation rules');
        }

        $update_arr = [
            'assume_work_type' => $request->input('assume_work_type'),
            'assume_work_time' => $request->input('assume_work_time'),
            'assume_work_hour' => $request->input('assume_work_hour'),
            'assume_total_hour' => $request->input('assume_total_hour'),
            'work_details' => $request->input('work_details'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'created_ip' => $request->ip(),
            'updated_by' => Auth::user()->id
        ];

        Inward_outward_prime_action::where('id', $id)->update($update_arr);
        $task_percentage = $request->task_percentage;
        if (!empty($task_percentage)) {
            $support_employees = $request->support_employees;
            array_unshift($support_employees, Auth::user()->id);
            $work_hour = $request->work_hour;
            for ($count = 0; $count < count($task_percentage); $count++) {

                $data = array(
                    'inward_outward_prime_action_id' => $request->input('id'),
                    'support_department_id' => implode(',', $request->input('support_departments')),
                    'support_employee_id' => $support_employees[$count],
                    'emp_status' => Auth::user()->id == $support_employees[$count] ? 'Accepted' : 'Assigned',
                    'task_percentage' => $task_percentage[$count],
                    'task_hour' => $work_hour[$count],
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_ip' => $request->ip(),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::user()->id
                );
                $update_data[] = $data;
            }
        } else {
            $emp_depart_id = Employees::where('user_id', Auth::user()->id)->value('department_id');
            $update_data = [
                'inward_outward_prime_action_id' => $request->input('id'),
                'support_department_id' => $emp_depart_id,
                'support_employee_id' => Auth::user()->id,
                'emp_status' => 'Accepted',
                'task_percentage' => 100,
                'task_hour' => $request->input('assume_total_hour'),
                'created_at' => date('Y-m-d H:i:s'),
                'created_ip' => $request->ip(),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => Auth::user()->id
            ];
        }

        $check_entry = Inward_outward_distrubuted_work::where('inward_outward_prime_action_id', $id)->get()->count();
        if ($check_entry > 0) {
            Inward_outward_distrubuted_work::where('inward_outward_prime_action_id', $id)->delete();
        }
        Inward_outward_distrubuted_work::insert($update_data);
        return redirect()->route('admin.prime_action_list')->with('success', 'Record successfully Updated!.');
    }

    //========================================= Support Empployee action Process ==================================================


    public function accept_requestBySupportEmp($id, Request $request)
    {  //nish

        $update_arr = [
            'emp_status' => 'Accepted',
            'updated_at' => date('Y-m-d H:i:s'),
            'action_date_time' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip()
        ];

        if (Inward_outward_distrubuted_work::where('id', $id)->update($update_arr)) {


            $inward_data = Inward_outward_distrubuted_work::join('inward_outward_prime_action', 'inward_outward_prime_action.id', '=', 'inward_outward_distrubuted_work.inward_outward_prime_action_id')
                ->join('inward_outwards', 'inward_outwards.id', '=', 'inward_outward_prime_action.inward_outward_id')
                ->where('inward_outward_distrubuted_work.id', $id)
                ->get(['inward_outwards.prime_employee_id', 'inward_outwards.inward_outward_no']);

            $mail = User::where('id', $inward_data[0]->prime_employee_id)->pluck('email')->toArray();
            $user_name = Auth::user()->name;
            $data = [
                'user_name' => $user_name,
                'date' => date('d-m-Y H:i a'),
                'registry_no' => $inward_data[0]->inward_outward_no,
                'email_list' => $mail
            ];

            $this->common_task->acceptDistrubutedWorkRequestSupportEmp($data);
            $notify_ids = User::where('id', $inward_data[0]->prime_employee_id)->pluck('id')->toArray();
            $this->notification_task->supportEmpAcceptNotify($notify_ids, $user_name);

            return redirect()->route('admin.prime_action_list')->with('success', 'Registry Document succesfully accepted.');
        }
        return redirect()->route('admin.prime_action_list')->with('error', 'Error during operation. Try again!');
    }

    public function reject_requestBySupportEmp(Request $request)
    {

        $validator_normal = Validator::make($request->all(), [
            'distrubuted_work_id' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.prime_action_list')->with('error', 'Please follow validation rules.');
        }

        $id = $request->input('distrubuted_work_id');
        $checkReason = $request->input('check_btn');

        $inward_data = Inward_outward_distrubuted_work::join('inward_outward_prime_action', 'inward_outward_prime_action.id', '=', 'inward_outward_distrubuted_work.inward_outward_prime_action_id')
            ->join('inward_outwards', 'inward_outwards.id', '=', 'inward_outward_prime_action.inward_outward_id')
            ->where('inward_outward_distrubuted_work.id', $id)
            ->get(['inward_outwards.prime_employee_id', 'inward_outwards.inward_outward_no']);

        $mail = User::where('id', $inward_data[0]->prime_employee_id)->pluck('email')->toArray();
        $user_name = Auth::user()->name;

        if ($checkReason == 'satisfied_reason') {
            $reject_arr = [
                'emp_status' => 'Rejected',
                'satisfied_reason' => $request->input('satisfied_reason'),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip()
            ];

            $data = [
                'user_name' => $user_name,
                'date' => date('d-m-Y H:i a'),
                'registry_no' => $inward_data[0]->inward_outward_no,
                'email_list' => $mail,
                'reason' => $request->input('satisfied_reason')
            ];

            $this->common_task->rejectDistrubutedWorkRequestSupportEmp($data);

            $notify_ids = User::where('id', $inward_data[0]->prime_employee_id)->pluck('id')->toArray();
            $this->notification_task->supportEmpRejectNotify($notify_ids, $user_name);
        } else {

            $reject_arr = [
                'emp_status' => 'Rejected',
                'general_reason' => $request->input('general_reason'),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip()
            ];

            $data = [
                'user_name' => $user_name,
                'date' => date('d-m-Y H:i a'),
                'registry_no' => $inward_data[0]->inward_outward_no,
                'email_list' => $mail,
                'reason' => $request->input('general_reason')
            ];

            $this->common_task->rejectDistrubutedWorkRequestSupportEmp($data);

            $notify_ids = User::where('id', $inward_data[0]->prime_employee_id)->pluck('id')->toArray();
            $this->notification_task->supportEmpRejectNotify($notify_ids, $user_name);
        }

        if (Inward_outward_distrubuted_work::where('id', $id)->update($reject_arr)) {

            return redirect()->route('admin.prime_action_list')->with('success', 'Registry Document succesfully rejected');
        }
        return redirect()->route('admin.prime_action_list')->with('error', 'Error during operation. Try again!');
    }

    public function acceptEmpRequest($id, Request $request)
    {  //nish   //mouse
        $percent = Inward_outward_distrubuted_work::where('id', $id)->get(['task_percentage', 'task_hour', 'satisfied_reason', 'inward_outward_prime_action_id']);
        $diff = $percent[0]->satisfied_reason - $percent[0]->task_percentage;

        $main_userId = Inward_outward_distrubuted_work::where('inward_outward_prime_action_id', $percent[0]->inward_outward_prime_action_id)->first();

        $main_final_perc = $main_userId['task_percentage'] - $diff;
        $main_final_hour = $main_userId['task_hour'] * $main_final_perc / $main_userId['task_percentage'];
        Inward_outward_distrubuted_work::where('id', $main_userId['id'])->update(['task_percentage' => $main_final_perc, 'task_hour' => $main_final_hour]);

        $task_hour = $percent[0]->task_hour * $percent[0]->satisfied_reason / $percent[0]->task_percentage;
        $update_arr = [
            'emp_status' => 'Accepted',
            'task_percentage' => $percent[0]->satisfied_reason,
            'task_hour' =>  $task_hour,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip()
        ];

        if (Inward_outward_distrubuted_work::where('id', $id)->update($update_arr)) {

            $inward_data = Inward_outward_prime_action::join('inward_outwards', 'inward_outwards.id', '=', 'inward_outward_prime_action.inward_outward_id')
                ->where('inward_outward_prime_action.id', $percent[0]->inward_outward_prime_action_id)
                ->get(['inward_outwards.prime_employee_id', 'inward_outwards.inward_outward_no']);

            $notify_ids = Inward_outward_distrubuted_work::where('id', $id)->pluck('support_employee_id')->toArray();
            $registry_no = $inward_data[0]->inward_outward_no;
            $prime_user = Auth::user()->name;
            $this->notification_task->empWorkPercentageUpdatedNotify($notify_ids, $registry_no, $prime_user);

            return redirect()->route('admin.prime_action_list')->with('success', 'Employee Request succesfully accepted.');
        }
        return redirect()->route('admin.prime_action_list')->with('error', 'Error during operation. Try again!');
    }

    public function rejectEmpRequest(Request $request)
    {    //mouse

        $validator_normal = Validator::make($request->all(), [
            'distrubuted_id' => 'required',
            'reject_reason' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.prime_action_list')->with('error', 'Please follow validation rules.');
        }
        $id = $request->input('distrubuted_id');

        $percent = Inward_outward_distrubuted_work::where('id', $id)->get(['task_percentage', 'task_hour', 'satisfied_reason', 'inward_outward_prime_action_id']);
        $diff = $percent[0]->satisfied_reason - $percent[0]->task_percentage;
        if ($diff >= 2 && $diff <= 10) {
            $number = $diff / 2;
            $final_perc = $percent[0]->task_percentage + $number;
            $final_hour = $percent[0]->task_hour * $final_perc / $percent[0]->task_percentage;
            Inward_outward_distrubuted_work::where('id', $id)->update(['task_percentage' => $final_perc, 'task_hour' => $final_hour, 'emp_status' => 'Accepted']);

            $main_userId = Inward_outward_distrubuted_work::where('inward_outward_prime_action_id', $percent[0]->inward_outward_prime_action_id)->first();
            $main_final_perc = $main_userId['task_percentage'] - $number;
            $main_final_hour = $main_userId['task_hour'] * $main_final_perc / $main_userId['task_percentage'];
            Inward_outward_distrubuted_work::where('id', $main_userId['id'])->update(['task_percentage' => $main_final_perc, 'task_hour' => $main_final_hour]);

            $reject_arr = [
                'emp_status' => 'Accepted',
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip()
            ];

            Inward_outward_distrubuted_work::where('id', $id)->update($reject_arr);

            return redirect()->route('admin.prime_action_list')->with('success', 'System has automatically calculated the percentage of work and divided the difference between the work efforts in same part between you and query employee.');
        } else {
            $update_arr = [
                'final_status' => 'Rejected',
                'reject_reason' => $request->input('reject_reason'),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip()
            ];
            Inward_outward_prime_action::where('id', $percent[0]->inward_outward_prime_action_id)->update($update_arr);
            //==============================================
            $inward_data = Inward_outward_prime_action::join('inward_outwards', 'inward_outwards.id', '=', 'inward_outward_prime_action.inward_outward_id')
                ->where('inward_outward_prime_action.id', $percent[0]->inward_outward_prime_action_id)
                ->get(['inward_outwards.prime_employee_id', 'inward_outwards.inward_outward_no']);

            $mail_data = [];
            $mail_data['email_list'] = user::where('status', 'Enabled')->where('role', config('constants.SuperUser'))->pluck('email')->toArray();
            $mail_data['registry_no'] = $inward_data[0]->inward_outward_no;
            $mail_data['date'] = date('d-m-Y H:i a');
            $mail_data['user_name'] = Auth::user()->name;
            $mail_data['reject_note'] = $request->input('reject_reason');

            $this->common_task->rejectFinalTaskPrimeUser($mail_data);

            $notify_ids = user::where('status', 'Enabled')->where('role', config('constants.SuperUser'))->pluck('id')->toArray();
            $registry_no = $inward_data[0]->inward_outward_no;
            $this->notification_task->rejectReturnTaskPrimeUser($notify_ids, $registry_no);
            //==============================================
            $reject_arr = [
                'emp_status' => 'Rejected',
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip()
            ];

            Inward_outward_distrubuted_work::where('id', $id)->update($reject_arr);

            return redirect()->route('admin.prime_action_list')->with('success', 'Your rejection is sent to management for resolution of work division. Management will take action soon.');
        }


        //return redirect()->route('admin.prime_action_list')->with('error', 'Error during operation. Try again!');
    }

    public function removeEmp($id, Request $request)
    {  //nish    //mouse

        if (Inward_outward_distrubuted_work::where('id', $id)->delete()) {
            $emp_id = Inward_outward_distrubuted_work::where('id', $id)->pluck('support_employee_id')->toArray();
            return redirect()->route('admin.prime_action_list')->with('success', 'Employee succesfully removed.');
        }
        return redirect()->route('admin.prime_action_list')->with('error', 'Error during operation. Try again!');
    }


    public function reject_distrubutionPrimeUser(Request $request)
    {   // not in use

        $validator_normal = Validator::make($request->all(), [
            'prime_table_id' => 'required',
            'reject_note' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.prime_action_list')->with('error', 'Please follow validation rules.');
        }

        $id = $request->input('prime_table_id');

        $reject_arr = [
            'final_status' => 'Rejected',
            'reject_reason' => $request->input('reject_note'),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip()
        ];

        if (Inward_outward_prime_action::where('id', $id)->update($reject_arr)) {


            $inward_data = Inward_outward_prime_action::join('inward_outwards', 'inward_outwards.id', '=', 'inward_outward_prime_action.inward_outward_id')
                ->where('inward_outward_prime_action.id', $id)
                ->get(['inward_outwards.prime_employee_id', 'inward_outwards.inward_outward_no']);

            $mail_data = [];
            $mail_data['email_list'] = user::where('status', 'Enabled')->where('role', config('constants.SuperUser'))->pluck('email')->toArray();
            $mail_data['registry_no'] = $inward_data[0]->inward_outward_no;
            $mail_data['date'] = date('d-m-Y H:i a');
            $mail_data['user_name'] = Auth::user()->name;
            $mail_data['reject_note'] = $request->input('reject_note');

            $this->common_task->rejectFinalTaskPrimeUser($mail_data);

            $notify_ids = user::where('status', 'Enabled')->where('role', config('constants.SuperUser'))->pluck('id')->toArray();
            $registry_no = $inward_data[0]->inward_outward_no;
            $this->notification_task->rejectReturnTaskPrimeUser($notify_ids, $registry_no);

            return redirect()->route('admin.prime_action_list')->with('success', 'Registry Document succesfully rejected');
        }
        return redirect()->route('admin.prime_action_list')->with('error', 'Error during operation. Try again!');
    }

    public function managment_view_list()
    {

        if (Auth::user()->role != config('constants.SuperUser')) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }
        $this->data['page_title'] = "Task Distribution";

        $this->data['prime_list'] = $prime_list = Inward_outward_prime_action::rightjoin('inward_outwards', 'inward_outwards.id', '=', 'inward_outward_prime_action.inward_outward_id')
            ->with(['emp_distrubution' => function ($query) {
                $query->leftjoin('users', 'users.id', '=', 'inward_outward_distrubuted_work.support_employee_id')
                    ->leftjoin("department", \DB::raw("FIND_IN_SET(department.id,inward_outward_distrubuted_work.support_department_id)"), ">", \DB::raw("'0'"))
                    ->groupBy("inward_outward_distrubuted_work.id")
                    ->select(['inward_outward_prime_action_id', 'support_department_id', 'support_employee_id', 'users.name', 'task_percentage', 'task_hour', 'emp_status', \DB::raw("GROUP_CONCAT(department.dept_name) as depart_name")]);
            }])
            ->where('inward_outwards.prime_user_status', '=', 'Accepted')
            ->where('inward_outward_prime_action.final_status', '=', 'Rejected')
            ->whereDate('inward_outwards.created_at', '>', '2020-06-02')
            ->get([
                'inward_outwards.id as inward_id', 'inward_outwards.inward_outward_no', 'inward_outwards.prime_user_status',
                'inward_outward_prime_action.*', 'inward_outward_prime_action.assume_work_type', 'inward_outward_prime_action.id as prime_table_id',
                'inward_outward_prime_action.assume_work_time', 'inward_outward_prime_action.assume_work_hour',
                'inward_outward_prime_action.work_details', 'inward_outwards.inward_outward_title', 'inward_outwards.document_file'
            ]);

        // dd($this->data['prime_list']);
        return view('admin.action_process.managment_list', $this->data);
    }

    public function distrubuted_llist($id)
    {
        $this->data['page_title'] = "Update Task Distribution";
        $this->data['module_link'] = "admin.managment_view_list";
        $this->data['module_title'] = "Task Distribution";
        $reject_div = [];
        $this->data['distrubuted_llist'] = $distrubuted_llist = Inward_outward_distrubuted_work::join('users', 'users.id', '=', 'inward_outward_distrubuted_work.support_employee_id')
            ->join('inward_outward_prime_action', 'inward_outward_prime_action.id', '=', 'inward_outward_distrubuted_work.inward_outward_prime_action_id')
            ->join('users as B', 'B.id', '=', 'inward_outward_distrubuted_work.support_employee_id')
            ->where('inward_outward_distrubuted_work.inward_outward_prime_action_id', $id)
            ->get([
                'users.name', 'B.name as reject_user_name', 'inward_outward_prime_action.reject_reason', 'inward_outward_prime_action.assume_total_hour', 'inward_outward_distrubuted_work.id', 'inward_outward_distrubuted_work.inward_outward_prime_action_id', 'inward_outward_distrubuted_work.support_employee_id',
                'inward_outward_distrubuted_work.task_percentage', 'inward_outward_distrubuted_work.satisfied_reason', 'inward_outward_distrubuted_work.task_hour', 'inward_outward_distrubuted_work.emp_status'
            ]);

        foreach ($distrubuted_llist as $key => $value) {
            if ($value->emp_status == "Rejected") {
                $reject_div['total_hour'] = $value->assume_total_hour;
                $reject_div['expect_percentage'] = $value->satisfied_reason;
                $reject_div['reject_user_name'] = $value->reject_user_name;
                $reject_div['reject_note'] = $value->reject_reason;
            }
        }
        $this->data['reject_div'] = $reject_div;
        //dd($distrubuted_llist->toArray());
        return view('admin.action_process.final_distrubated', $this->data);
    }

    public function update_distrubated_task(Request $request)
    {
        $rules = array(
            'distrubuted_work_id.*' => 'required',
            'task_percentage.*' => 'required',
            'task_hour.*' => 'required',
            'support_employee.*' => 'required'
        );

        $validator = Validator::make(Input::all(), $rules);
        $id = $request->input('id');
        if ($validator->fails()) {
            return redirect()->route('admin.distrubuted_llist', $id)->with('error', 'Please follow validation rules');
        }
        $data = $request->all();

        Inward_outward_prime_action::where('id', $data['prime_id'])->update(['final_status' => 'Accepted']);
        $arr = [];
        foreach ($data['task_percentage'] as $key => $value) {

            $arr['emp_status'] = 'Accepted';
            $arr['task_percentage'] = $data['task_percentage'][$key];
            $arr['task_hour'] = $data['task_hour'][$key];
            $arr['updated_ip'] = $request->ip();

            DB::table('inward_outward_distrubuted_work')->whereId($data['distrubuted_work_id'][$key])->update($arr);
        }

        $cc_mail = User::whereIn('id', $data['support_employee'])->pluck('email')->toArray();

        //$this->common_task->acceptRegistryDocumentPrimeUser($data);

        return redirect()->route('admin.managment_view_list')->with('success', 'Record successfully Updated!.');
    }


    //================================= Final Task Acceptance ====================================================


    public function get_emp_work_details($id)
    {

        $this->data['page_title'] = "Support Employee Work Status";
        $this->data['module_link'] = "admin.prime_action_list";
        $this->data['module_title'] = "Task Distribution";

        $emp_details = Inward_outward_distrubuted_work::join('inward_outward_prime_action', 'inward_outward_prime_action.id', '=', 'inward_outward_distrubuted_work.inward_outward_prime_action_id')
            ->join('users', 'users.id', '=', 'inward_outward_distrubuted_work.support_employee_id')
            ->where('inward_outward_prime_action_id', $id)
            ->get(['users.name as emp_name', 'inward_outward_distrubuted_work.*', 'inward_outward_prime_action.final_status']);
        foreach ($emp_details as $key => $value) {
            if ($value->satisfied_reason) {
                $satisfy_hour = $value->satisfied_reason * $value->task_hour / $value->task_percentage;
                $txt = '<p>-> Employee has query regarding percentage of work you had assigned to him.<p>-> Expected Percentage of work by employee:' . ' ' . $value->satisfied_reason . '%' . ' ' . '(' . $satisfy_hour . 'Hour' . ').';
                $txt .= '<br><br>-> So Please Accept or Reject his query so system can proceed accordingly.';
                $emp_details[$key]->satisfied_reason = $txt;
            }
        }
        //dd($emp_details->toArray());
        $this->data['emp_details'] = $emp_details;
        return view('admin.action_process.emp_work_details', $this->data);
    }

    public function reject_emp_work(Request $request)
    {

        $validator_normal = Validator::make($request->all(), [
            'distrubuted_work_id' => 'required',
            'reject_note' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.prime_action_list')->with('error', 'Please follow validation rules.');
        }

        $id = $request->input('distrubuted_work_id');

        $reject_arr = [
            'work_status' => 'Rejected',
            'work_rejection_note' => $request->input('reject_note'),
            'acceptance_datetime' => date('Y-m-d H:i:s'),
        ];

        if (Inward_outward_distrubuted_work::where('id', $id)->update($reject_arr)) {

            $users_data = Inward_outward_distrubuted_work::join('users', 'users.id', '=', 'inward_outward_distrubuted_work.support_employee_id')
                ->where('inward_outward_distrubuted_work.id', $id)
                ->get(['users.name as emp_name', 'inward_outward_distrubuted_work.support_employee_id']);
            $mail = User::where('id', $users_data[0]->support_employee_id)->pluck('email')->toArray();
            $data = [
                'user_name' => $users_data[0]->emp_name,
                'date' => date('d-m-Y H:i a'),
                'email_list' => $mail,
                'reject_note' => $request->input('reject_note'),
                'rejected_by' => Auth::user()->name
            ];

            $this->common_task->rejectWorkByPrimeUser($data);

            return redirect()->route('admin.prime_action_list')->with('success', 'Registry Document succesfully rejected');
        }
        return redirect()->route('admin.prime_action_list')->with('error', 'Error during operation. Try again!');
    }

    public function accept_emp_work($id, Request $request)
    {  //nish

        $update_arr = [
            'work_status' => 'Accepted',
            'acceptance_datetime' => date('Y-m-d H:i:s')
        ];

        if (Inward_outward_distrubuted_work::where('id', $id)->update($update_arr)) {

            return redirect()->route('admin.prime_action_list')->with('success', 'Registry Document succesfully accepted.');
        }
        return redirect()->route('admin.prime_action_list')->with('error', 'Error during operation. Try again!');
    }

    public function edit_emp_work($id)
    {
        $this->data['page_title'] = "Submit Your Work Details";
        $this->data['module_link'] = "admin.prime_action_list";
        $this->data['module_title'] = "Task Distribution";
        $this->data['details'] = $details = Inward_outward_distrubuted_work::where('id', $id)->get();

        return view('admin.action_process.edit_emp_work_details', $this->data);
    }

    public function update_emp_work(Request $request)
    {
        $rules = array(
            'work_details' => 'required'
        );
        $validator = Validator::make(Input::all(), $rules);
        $request_data = $request->all();
        if ($validator->fails()) {
            return redirect()->route('admin.edit_emp_work', $request_data['id'])->with('error', 'Please follow validation rules');
        }


        $update_arr = [
            'work_status' => 'Submitted',
            'work_details' => $request->input('work_details'),
            'work_datetime' => date('Y-m-d H:i:s')
        ];
        //upload document_file
        $document_file = '';
        if ($request->hasFile('work_document')) {

            $document_file = $request->file('work_document');
            $original_file_name = explode('.', $document_file->getClientOriginalName());
            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);
            $file_path = $document_file->storeAs('public/work_document_files', $new_file_name);
            if ($file_path) {
                $document_file = $file_path;
            }
            $update_arr['work_document'] = $document_file;
        }

        if (Inward_outward_distrubuted_work::where('id', $request_data['id'])->update($update_arr)) {

            return redirect()->route('admin.prime_action_list')->with('success', 'Registry Document task succesfully submitted.');
        }
        return redirect()->route('admin.prime_action_list')->with('error', 'Error during operation. Try again!');
    }

    public function get_hourByPercentage(Request $request)  //ajax call new
    {
        $validator_normal = Validator::make($request->all(), ['id' => 'required']);
        if ($validator_normal->fails()) {
            return response()->json(['status' => false]);
        }
        $respose_data = [];
        $get_data = Inward_outward_distrubuted_work::where('id', $request->id)->get(['work_day', 'work_hour', 'task_percentage', 'task_hour']);

        $set_hour = $get_data[0]->task_hour;
        $respose_data['set_hour'] = $set_hour;
        $respose_data['work_day'] = $get_data[0]->work_day;
        $respose_data['work_hour'] = $get_data[0]->work_hour;
        return response()->json($respose_data);
    }

    public function update_workInterval(Request $request)
    {

        $rules = array(
            'work_day' => 'required',
            'work_hour' => 'required',
            'work_id' => 'required',
        );
        $validator = Validator::make(Input::all(), $rules);
        $request_data = $request->all();
        if ($validator->fails()) {
            return redirect()->route('admin.prime_action_list')->with('error', 'Please follow validation rules');
        }

        $update_arr = [
            'work_day' =>  $request->input('work_day'),
            'work_hour' => $request->input('work_hour'),
            'working_start_datetime' => date("Y-m-d H:i:s", strtotime($request->input('working_start_datetime')))
        ];
        //dd($update_arr);
        if (Inward_outward_distrubuted_work::where('id', $request_data['work_id'])->update($update_arr)) {

            return redirect()->route('admin.prime_action_list')->with('success', 'Work Time limit Added successfully.');
        }
        return redirect()->route('admin.prime_action_list')->with('error', 'Error during operation. Try again!');
    }
}
