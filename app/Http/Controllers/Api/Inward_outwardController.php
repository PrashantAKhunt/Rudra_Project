<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Inward_outward_chat;
use App\Inward_outward_doc_category;
use App\Inward_outward_message_view;
use App\Inward_outward_prime_action;
use App\Inward_outward_distrubuted_work;
use App\Inward_outward_users;
use App\Inward_outward_views;
use App\Inward_outwards;
use App\Department;
use App\Companies;
use App\Projects;
use App\User;
use App\Lib\CommonTask;
use App\Lib\NotificationTask;
use Exception;
use Illuminate\Support\Facades\Config;


class Inward_outwardController extends Controller
{

    private $page_limit = 20;
    public $common_task;
    private $module_id = 40;

    public function __construct()
    {
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();
    }

    //-----------------------------------------------------------------------------------------------------------------//
    # Registry Documents API

    public function pending_registry_documents(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();

        $registry_docs = Inward_outwards::select('inward_outwards.id','inward_outwards.other_project_details', 'inward_outward_doc_sub_category.sub_category_name', 'company.company_name', 'project.project_name', 'inward_outwards.document_file', 'inward_outwards.inward_outward_title', 'inward_outwards.inward_outward_no', 'inward_outwards.parent_inward_outward_no', 'inward_outwards.description','inward_outwards.ref_outward_number', 'inward_outwards.type', 'inward_outwards.expected_ans_date', 'inward_outwards.created_at', 'inward_outward_doc_category.category_name', 'inward_outwards.received_date')
            ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
            ->join('company', 'company.id', '=', 'inward_outwards.company_id')
            ->join('project', 'project.id', '=', 'inward_outwards.project_id')
            ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
            ->where('inward_outwards.doc_mark', 'Pending')
            ->orderBy('inward_outwards.id', 'DESC')
            ->get();

        if ($registry_docs->count() == 0) {
            return response()->json([
                'status' => false,
                'msg' => config('errors.no_record.msg'),
                'data' => [],
                'error' => config('errors.no_record.code')
            ]);
        }

        foreach ($registry_docs as $key => $value) {

            $this->data['users_data'] = $users_data = Inward_outward_users::select('inward_outward_users.user_id', 'inward_outward_users.status', 'users.name')
                ->join('users', 'users.id', '=', 'inward_outward_users.user_id')
                ->where('inward_outward_users.inward_outward_id', '=', $value->id)->get();

            $registry_docs[$key]->users_list = $users_data;

            if (!empty($value->document_file))
                $registry_docs[$key]->document_file = asset('storage/' . str_replace('public/', '', $value->document_file));
        }


        return response()->json([
            'status' => true,
            'msg' => "Record found.",
            'data' => $registry_docs
        ]);
    }

    public function approved_inwards_documents(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        $request_data = $request->all();

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $inwards_docs = Inward_outwards::select('inward_outwards.*', 'company.company_name', 'project.project_name', 'inward_outward_doc_category.category_name', 'inward_outward_doc_sub_category.sub_category_name')
            ->join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
            ->join('company', 'company.id', '=', 'inward_outwards.company_id')
            ->join('project', 'project.id', '=', 'inward_outwards.project_id')
            ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
            ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
            ->where(function ($query) use ($request_data) {
                //if (Auth::user()->role != config('constants.SuperUser')) {
                $query->where('inward_outward_users.user_id', '=', $request_data['user_id']);
                //}
            })
            ->where('inward_outwards.type', 'Inwards')
            ->where('inward_outwards.doc_mark', 'Approved')
            ->orderBy('inward_outwards.id', 'DESC')
            //->groupBy('inward_outward_users.user_id')
            ->get();


        if ($inwards_docs->count() == 0) {
            return response()->json([
                'status' => false,
                'msg' => config('errors.no_record.msg'),
                'data' => [],
                'error' => config('errors.no_record.code')
            ]);
        }

        foreach ($inwards_docs as $key => $value) {

            $this->data['users_data'] = $users_data = Inward_outward_users::select('inward_outward_users.user_id', 'inward_outward_users.status', 'users.name')
                ->join('users', 'users.id', '=', 'inward_outward_users.user_id')
                ->where('inward_outward_users.inward_outward_id', '=', $value->id)->get();

            $inwards_docs[$key]->users_list = $users_data;

            if (!empty($value->document_file))
                $inwards_docs[$key]->document_file = asset('storage/' . str_replace('public/', '', $value->document_file));
        }

        return response()->json([
            'status' => true,
            'msg' => "Record found.",
            'data' => $inwards_docs
        ]);
    }

    public function approved_outwards_documents(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();

        $outwards_docs = Inward_outwards::select('inward_outwards.*', 'company.company_name', 'project.project_name', 'inward_outward_doc_category.category_name', 'inward_outward_doc_sub_category.sub_category_name')
            ->join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
            ->join('company', 'company.id', '=', 'inward_outwards.company_id')
            ->join('project', 'project.id', '=', 'inward_outwards.project_id')
            ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
            ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
            ->where(function ($query) use ($request_data) {
                //if (Auth::user()->role != config('constants.SuperUser')) {
                $query->where('inward_outward_users.user_id', '=', $request_data['user_id']);
                //}
            })
            ->where('inward_outwards.type', 'Outwards')
            ->where('inward_outwards.doc_mark', 'Approved')
             ->orderBy('inward_outwards.id', 'DESC')
            //->groupBy('inward_outward_users.user_id')
            ->get();

        if ($outwards_docs->count() == 0) {
            return response()->json([
                'status' => false,
                'msg' => config('errors.no_record.msg'),
                'data' => [],
                'error' => config('errors.no_record.code')
            ]);
        }

        foreach ($outwards_docs as $key => $value) {

            $this->data['users_data'] = $users_data = Inward_outward_users::select('inward_outward_users.user_id', 'inward_outward_users.status', 'users.name')
                ->join('users', 'users.id', '=', 'inward_outward_users.user_id')
                ->where('inward_outward_users.inward_outward_id', '=', $value->id)->get();

            $outwards_docs[$key]->users_list = $users_data;


            if (!empty($value->document_file))
                $outwards_docs[$key]->document_file = asset('storage/' . str_replace('public/', '', $value->document_file));
        }

        return response()->json([
            'status' => true,
            'msg' => "Record found.",
            'data' => $outwards_docs
        ]);
    }

    public function mark_approve_documnet(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'registry_id' => 'required',
            'status' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();

        $update_maek_arr = [
            'doc_mark' => $request_data['status'],
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip()
        ];

        Inward_outwards::where('id', $request_data['registry_id'])->update($update_maek_arr);

        if ($request_data['status'] == 'Approved') {

            $user_data = Inward_outward_users::where('inward_outward_id', '=', $request_data['registry_id'])
                ->pluck('user_id')->toArray();
            $user_data = $this->common_task->setSuperUserId($user_data, 1);

            Inward_outward_users::where('inward_outward_id', $request_data['registry_id'])->delete();

            foreach ($user_data as $key => $user) {

                $inward_users_arr = [
                    'inward_outward_id' => $request_data['registry_id'],
                    'user_id' => $user,
                    'status' => $key == 0 ? 'Processing' : 'Pending',
                    'expected_ans_date' => NULL,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_ip' => $request->ip(),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                ];
                Inward_outward_users::insert($inward_users_arr);
            }

            // insert in Inward_outward_views table
            Inward_outward_views::where('inward_outward_id', $request_data['registry_id'])->delete();

            foreach ($user_data as $key => $user) {

                $views_arr = [
                    'user_id' => $user,
                    'inward_outward_id' => $request_data['registry_id'],
                    'is_view' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_ip' => $request->ip(),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                ];

                Inward_outward_views::insert($views_arr);
            }
        }

        return response()->json([
            'status' => true,
            'msg' => "Document Approved Succesfully.",
            'data' => []
        ]);
    }
    //---------------------------------------------------------------------------------------------------------//
    # Get inward list

    public function get_inward_list(Request $request)  //03/06/2020
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();

        $search_date = $request->input('search_date');
        $user_id = $request_data['user_id'];
        $logged_in_userdata = User::where('id', $request_data['user_id'])->get();
        $select_fileds = ['inward_outwards.*', 'company.company_name', 'project.project_name', 'inward_outward_doc_category.category_name',
                            'inward_outward_doc_sub_category.sub_category_name','inward_outward_delivery_mode.name as delivery_mode_name','sender.name as sender_type',
                            'users.name as requested_by_name','department.dept_name'];
        if ($request->has('search_date')) {
            // $inward_data = Inward_outwards::select('inward_outwards.*', 'company.company_name', 'project.project_name', 'inward_outward_doc_category.category_name',
            //  'inward_outward_doc_sub_category.sub_category_name','inward_outward_delivery_mode.name as delivery_mode_name','sender.name as sender_type',
            //  'users.name as requested_by_name','department.dept_name')
            //     ->join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
            //     ->join('company', 'company.id', '=', 'inward_outwards.company_id')
            //     ->join('project', 'project.id', '=', 'inward_outwards.project_id')
            //     ->leftjoin('inward_outward_prime_action','inward_outward_prime_action.inward_outward_id','=','inward_outwards.id')
            //     ->leftjoin('inward_outward_distrubuted_work','inward_outward_distrubuted_work.inward_outward_prime_action_id','=','inward_outward_prime_action.id')
            //     ->join('department', 'department.id', '=', 'inward_outwards.department_id')
            //     ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
            //     ->leftjoin('sender', 'sender.id', '=', 'inward_outwards.sender_id')
            //     ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
            //     ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
            //     ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
            //     ->whereDate('inward_outwards.created_at', '=', $search_date)
            //     ->groupBy('inward_outward_distrubuted_work.inward_outward_prime_action_id')
            //     ->where(function ($query) use ($request_data, $logged_in_userdata) {
            //         if ($logged_in_userdata[0]->role != config('constants.SuperUser')) {
            //         $query->where('inward_outward_users.user_id', '=', $request_data['user_id'])
            //             ->where('inward_outwards.inserted_by', $request_data['user_id'])
            //             ->orWhere('inward_outwards.requested_by', $request_data['user_id'] )
            //             ->orWhere(function ($query) use ($request_data)  {
            //                 $query->Where('inward_outwards.prime_employee_id', $request_data['user_id'] )
            //                       ->Where('inward_outwards.prime_user_status','Accepted');
            //             })->orWhere(function ($query) use ($request_data) {
            //                 $query->Where('inward_outward_distrubuted_work.support_employee_id', $request_data['user_id'] )
            //                       ->Where('inward_outward_distrubuted_work.emp_status','Accepted');
            //             });
            //         }
            //     })
            //     //->where('inward_outwards.is_reply', '=', 0)
            //     ->where('inward_outwards.type', '=', 'Inwards')
            //     ->orderBy('inward_outwards.id', 'DESC')
            //     ->get();


            $partial_query = Inward_outwards::
                     join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
                    ->join('company', 'company.id', '=', 'inward_outwards.company_id')
                    ->join('project', 'project.id', '=', 'inward_outwards.project_id')
                    ->leftJoin('department', 'department.id', '=', 'inward_outwards.department_id')
                    ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
                    ->leftjoin('sender', 'sender.id', '=', 'inward_outwards.sender_id')
                    ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
                    ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
                    ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
                    ->where('inward_outwards.type', '=', 'Inwards')
                    ->whereDate('inward_outwards.created_at', '=', $search_date)
                    ->orderBy('inward_outwards.id', 'DESC')
                    ->groupBy('inward_outward_users.inward_outward_id');

                $permission_arr = $this->common_task->getPermissionArr($logged_in_userdata[0]->role, 40); //fetch view permissions of users
            if (in_array(5, $permission_arr)) {
                    $inward_data =  $partial_query; //show all request
            } elseif (in_array(1, $permission_arr)) {
                    $inward_data =  $partial_query
                            ->leftJoin('inward_outward_prime_action','inward_outward_prime_action.inward_outward_id','=','inward_outwards.id')
                            ->leftJoin('inward_outward_distrubuted_work','inward_outward_distrubuted_work.inward_outward_prime_action_id','=','inward_outward_prime_action.id')
                            ->groupBy('inward_outward_distrubuted_work.inward_outward_prime_action_id')
                            ->where(function ($query) use ($user_id) {
                        $query->where('inward_outwards.inserted_by', $user_id)
                                ->orWhere('inward_outwards.requested_by', $user_id )
                                ->orWhere('inward_outward_users.user_id', $user_id)
                                ->orWhere(function ($query) use ($user_id)  {
                        $query->Where('inward_outwards.prime_employee_id', $user_id )
                            ->Where('inward_outwards.prime_user_status','Accepted');
                    })->orWhere(function ($query) use ($user_id)  {
                        $query->Where('inward_outward_distrubuted_work.support_employee_id', $user_id )
                            ->Where('inward_outward_distrubuted_work.emp_status','Accepted');
                    });
                });
            } else {
                return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
            }

        } else {

            $partial_query = Inward_outwards::
                    join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
                    ->join('company', 'company.id', '=', 'inward_outwards.company_id')
                    ->join('project', 'project.id', '=', 'inward_outwards.project_id')
                    ->leftJoin('department', 'department.id', '=', 'inward_outwards.department_id')
                    ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
                    ->leftjoin('sender', 'sender.id', '=', 'inward_outwards.sender_id')
                    ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
                    ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
                    ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
                    ->where('inward_outwards.type', '=', 'Inwards')
                    ->orderBy('inward_outwards.id', 'DESC')
                    ->groupBy('inward_outward_users.inward_outward_id');

                $permission_arr = $this->common_task->getPermissionArr($logged_in_userdata[0]->role, 40); //fetch view permissions of users
            if (in_array(5, $permission_arr)) {
                    $inward_data =  $partial_query; //show all request
            } elseif (in_array(1, $permission_arr)) {
                    $inward_data =  $partial_query
                            ->leftJoin('inward_outward_prime_action','inward_outward_prime_action.inward_outward_id','=','inward_outwards.id')
                            ->leftJoin('inward_outward_distrubuted_work','inward_outward_distrubuted_work.inward_outward_prime_action_id','=','inward_outward_prime_action.id')
                            ->groupBy('inward_outward_distrubuted_work.inward_outward_prime_action_id')
                            ->where(function ($query) use ($user_id)  {
                        $query->where('inward_outwards.inserted_by', $user_id)
                                ->orWhere('inward_outwards.requested_by',$user_id )
                                ->orWhere('inward_outward_users.user_id', $user_id)
                                ->orWhere(function ($query) use ($user_id) {
                        $query->Where('inward_outwards.prime_employee_id', $user_id )
                            ->Where('inward_outwards.prime_user_status','Accepted');
                    })->orWhere(function ($query) use ($user_id)  {
                        $query->Where('inward_outward_distrubuted_work.support_employee_id', $user_id )
                            ->Where('inward_outward_distrubuted_work.emp_status','Accepted');
                    });
                });
            } else {
                return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
            }
        }

        $inward_data = $inward_data->get($select_fileds);

        if ($inward_data->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        foreach ($inward_data as $key => $value) {

            if (!empty($value->document_file))
                $inward_data[$key]->document_file = asset('storage/' . str_replace('public/', '', $value->document_file));
        
                if (!empty($value->delivery_file)) {
                    $inward_data[$key]->delivery_file = asset('storage/' . str_replace('public/', '', $value->delivery_file));
                }
        }

        $inward_list = [];

        foreach ($inward_data as $key => $main_inward) {


            if ($main_inward->is_reply == 0) {

                array_push($select_fileds,'inward_outward_views.is_view');
                $thread_inward = Inward_outwards::
                         join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
                        ->join('inward_outward_views', 'inward_outward_views.inward_outward_id', '=', 'inward_outwards.id')
                        ->join('company', 'company.id', '=', 'inward_outwards.company_id')
                        ->join('project', 'project.id', '=', 'inward_outwards.project_id')
                        ->leftJoin('department', 'department.id', '=', 'inward_outwards.department_id')
                        ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
                        ->leftjoin('sender', 'sender.id', '=', 'inward_outwards.sender_id')
                        ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
                        ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
                        ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
                        ->where('inward_outwards.parent_inward_outward_no', '=', $main_inward->parent_inward_outward_no)
                        ->get($select_fileds);   


                // $thread_inward = Inward_outwards::select('inward_outwards.*', 'inward_outward_views.is_view', 'company.company_name', 'project.project_name',
                //  'inward_outward_doc_category.category_name', 'inward_outward_doc_sub_category.sub_category_name','inward_outward_delivery_mode.name as delivery_mode_name','sender.name as sender_type',
                //  'users.name as requested_by_name','department.dept_name')
                //     ->join('inward_outward_views', 'inward_outward_views.inward_outward_id', '=', 'inward_outwards.id')
                //     ->join('company', 'company.id', '=', 'inward_outwards.company_id')
                //     ->join('department', 'department.id', '=', 'inward_outwards.department_id')
                //     ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
                //     ->leftjoin('sender', 'sender.id', '=', 'inward_outwards.sender_id')
                //     ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
                //     ->join('project', 'project.id', '=', 'inward_outwards.project_id')
                //     ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
                //     ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
                //     ->where('inward_outwards.parent_inward_outward_no', '=', $main_inward->parent_inward_outward_no)
                //     ->where('inward_outward_views.user_id', '=', $request_data['user_id'])
                //     //->orderBy('inward_outwards.id', 'DESC')
                //     ->get();   //->toArray();

               

                foreach ($thread_inward as $thread__key => $value) {

                    $this->data['users_data'] = $users_data = Inward_outward_users::select('inward_outward_users.user_id', 'inward_outward_users.status', 'users.name')
                        ->join('users', 'users.id', '=', 'inward_outward_users.user_id')
                        ->where('inward_outward_users.inward_outward_id', '=', $value->id)->get();


                    if (!empty($value->document_file)) {

                        $thread_inward[$thread__key]->document_file = asset('storage/' . str_replace('public/', '', $value->document_file));
                    }
                    if (!empty($value->delivery_file)) {

                        $thread_inward[$thread__key]->delivery_file = asset('storage/' . str_replace('public/', '', $value->delivery_file));
                    }
                    $thread_inward[$thread__key]->users_list = $users_data;
                }

                $inward_list[$key]['main_inward'] = $main_inward;
                $inward_list[$key]['thread_inward'] = $thread_inward;
            } else {

                $inward_list[$key]['main_inward'] = $main_inward;

                $this->data['users_data'] = $users_data = Inward_outward_users::select('inward_outward_users.user_id', 'inward_outward_users.status', 'users.name')
                    ->join('users', 'users.id', '=', 'inward_outward_users.user_id')
                    ->where('inward_outward_users.inward_outward_id', '=', $main_inward->id)->get();

                $inward_list[$key]['thread_inward'][] = $main_inward;
                $inward_list[$key]['thread_inward'][0]['users_list'] = $users_data;
            }
        }

        return response()->json(
            [
                'status' => true,
                'msg' => 'Get Inwards details!',
                'data' => (array) $inward_list
            ]
        );
    }

    #Get Today Inward list
    public function get_today_inward_list(Request $request)  //16/06/2020
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();

        $today_date = date('Y-m-d');
        $user_id = $request_data['user_id'];
        $logged_in_userdata = User::where('id', $request_data['user_id'])->get();
        $select_fileds = ['inward_outwards.*', 'company.company_name', 'project.project_name', 'inward_outward_doc_category.category_name',
                            'inward_outward_doc_sub_category.sub_category_name','inward_outward_delivery_mode.name as delivery_mode_name','sender.name as sender_type',
                            'users.name as requested_by_name','department.dept_name'];

            $partial_query = Inward_outwards::
                    join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
                    ->join('company', 'company.id', '=', 'inward_outwards.company_id')
                    ->join('project', 'project.id', '=', 'inward_outwards.project_id')
                    ->leftJoin('department', 'department.id', '=', 'inward_outwards.department_id')
                    ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
                    ->leftjoin('sender', 'sender.id', '=', 'inward_outwards.sender_id')
                    ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
                    ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
                    ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
                    ->whereDate('inward_outwards.created_at', '=', $today_date)
                    ->where('inward_outwards.type', '=', 'Inwards')
                    ->orderBy('inward_outwards.id', 'DESC')
                    ->groupBy('inward_outward_users.inward_outward_id');

                $permission_arr = $this->common_task->getPermissionArr($logged_in_userdata[0]->role, 40); //fetch view permissions of users
            if (in_array(5, $permission_arr)) {
                    $inward_data =  $partial_query; //show all request
            } elseif (in_array(1, $permission_arr)) {
                    $inward_data =  $partial_query
                            ->leftJoin('inward_outward_prime_action','inward_outward_prime_action.inward_outward_id','=','inward_outwards.id')
                            ->leftJoin('inward_outward_distrubuted_work','inward_outward_distrubuted_work.inward_outward_prime_action_id','=','inward_outward_prime_action.id')
                            ->groupBy('inward_outward_distrubuted_work.inward_outward_prime_action_id')
                            ->where(function ($query) use ($user_id)  {
                        $query->where('inward_outwards.inserted_by', $user_id)
                                ->orWhere('inward_outwards.requested_by',$user_id )
                                ->orWhere('inward_outward_users.user_id', $user_id)
                                ->orWhere(function ($query) use ($user_id) {
                        $query->Where('inward_outwards.prime_employee_id', $user_id )
                            ->Where('inward_outwards.prime_user_status','Accepted');
                    })->orWhere(function ($query) use ($user_id)  {
                        $query->Where('inward_outward_distrubuted_work.support_employee_id', $user_id )
                            ->Where('inward_outward_distrubuted_work.emp_status','Accepted');
                    });
                });
            } else {
                return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
            }
        

        $inward_data = $inward_data->get($select_fileds);

        if ($inward_data->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        foreach ($inward_data as $key => $value) {

            if (!empty($value->document_file))
                $inward_data[$key]->document_file = asset('storage/' . str_replace('public/', '', $value->document_file));
        
                if (!empty($value->delivery_file)) {
                    $inward_data[$key]->delivery_file = asset('storage/' . str_replace('public/', '', $value->delivery_file));
                }
        }

        $inward_list = [];

        foreach ($inward_data as $key => $main_inward) {


            if ($main_inward->is_reply == 0) {

                array_push($select_fileds,'inward_outward_views.is_view');
                $thread_inward = Inward_outwards::
                         join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
                        ->join('inward_outward_views', 'inward_outward_views.inward_outward_id', '=', 'inward_outwards.id')
                        ->join('company', 'company.id', '=', 'inward_outwards.company_id')
                        ->join('project', 'project.id', '=', 'inward_outwards.project_id')
                        ->leftJoin('department', 'department.id', '=', 'inward_outwards.department_id')
                        ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
                        ->leftjoin('sender', 'sender.id', '=', 'inward_outwards.sender_id')
                        ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
                        ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
                        ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
                        ->where('inward_outwards.parent_inward_outward_no', '=', $main_inward->parent_inward_outward_no)
                        ->get($select_fileds);  
               
                foreach ($thread_inward as $thread__key => $value) {

                    $this->data['users_data'] = $users_data = Inward_outward_users::select('inward_outward_users.user_id', 'inward_outward_users.status', 'users.name')
                        ->join('users', 'users.id', '=', 'inward_outward_users.user_id')
                        ->where('inward_outward_users.inward_outward_id', '=', $value->id)->get();


                    if (!empty($value->document_file)) {

                        $thread_inward[$thread__key]->document_file = asset('storage/' . str_replace('public/', '', $value->document_file));
                    }
                    if (!empty($value->delivery_file)) {

                        $thread_inward[$thread__key]->delivery_file = asset('storage/' . str_replace('public/', '', $value->delivery_file));
                    }
                    $thread_inward[$thread__key]->users_list = $users_data;
                }

                $inward_list[$key]['main_inward'] = $main_inward;
                $inward_list[$key]['thread_inward'] = $thread_inward;
            } else {

                $inward_list[$key]['main_inward'] = $main_inward;

                $this->data['users_data'] = $users_data = Inward_outward_users::select('inward_outward_users.user_id', 'inward_outward_users.status', 'users.name')
                    ->join('users', 'users.id', '=', 'inward_outward_users.user_id')
                    ->where('inward_outward_users.inward_outward_id', '=', $main_inward->id)->get();

                $inward_list[$key]['thread_inward'][] = $main_inward;
                $inward_list[$key]['thread_inward'][0]['users_list'] = $users_data;
            }
        }

        return response()->json(
            [
                'status' => true,
                'msg' => 'Get Inwards details!',
                'data' => (array) $inward_list
            ]
        );
    }
    //Get Company list
    public function get_Company_list(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {

            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $company_list = Companies::where('status', 'Enabled')
            ->get();


        if ($company_list->count() == 0) {
            return response()->json(
                [
                    'status' => false,
                    'msg' => config('errors.no_record.msg'),
                    'data' => [],
                    'error' => config('errors.no_record.code')
                ]
            );
        }


        return response()->json(
            [
                'status' => true,
                'msg' => 'Get Company lists!',
                'data' => $company_list
            ]
        );
    }


    //Get Companies's Project list
    public function get_company_project_list(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'company_id' => 'required'
        ]);

        if ($validator->fails()) {

            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        //$request_data = $request->all();
        $company_id = $request->company_id;


        $project_list = Projects::select('project.*')
            ->where('project.status', 'Enabled')
            ->where(function ($query) use ($company_id) {
                $query->where('project.company_id', $company_id);
                $query->orWhere('project.company_id', 0);
            })
            ->get();


        if ($project_list->count() == 0) {
            return response()->json(
                [
                    'status' => false,
                    'msg' => config('errors.no_record.msg'),
                    'data' => [],
                    'error' => config('errors.no_record.code')
                ]
            );
        }


        return response()->json(
            [
                'status' => true,
                'msg' => 'Get Project lists!',
                'data' => $project_list
            ]
        );
    }

    //Get Category list
    public function get_Category_list(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {

            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $category_list = Inward_outward_doc_category::select('id', 'category_name')
            ->where('status', 'Enabled')
            ->get();


        if ($category_list->count() == 0) {
            return response()->json(
                [
                    'status' => false,
                    'msg' => config('errors.no_record.msg'),
                    'data' => [],
                    'error' => config('errors.no_record.code')
                ]
            );
        }


        return response()->json(
            [
                'status' => true,
                'msg' => 'Get Department lists!',
                'data' => $category_list
            ]
        );
    }

    //Get registry  list
    public function get_registry_list(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {

            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();

        $registy_list = Inward_outwards::select('inward_outwards.id', 'inward_outwards.inward_outward_title', 'inward_outwards.inward_outward_no')
            ->join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
            ->where('inward_outward_users.user_id', '=', $request_data['user_id'])
            ->where('inward_outwards.is_reply', '=', 0)
            ->get();

        if ($registy_list->count() == 0) {
            return response()->json(
                [
                    'status' => false,
                    'msg' => config('errors.no_record.msg'),
                    'data' => [],
                    'error' => config('errors.no_record.code')
                ]
            );
        }

        return response()->json(
            [
                'status' => true,
                'msg' => 'Get registry lists!',
                'data' => $registy_list
            ]
        );
    }

    //Get department list
    public function get_department_list(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {

            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $depart_list = Department::select('id', 'dept_name')
            ->get();


        if ($depart_list->count() == 0) {
            return response()->json(
                [
                    'status' => false,
                    'msg' => config('errors.no_record.msg'),
                    'data' => [],
                    'error' => config('errors.no_record.code')
                ]
            );
        }


        return response()->json(
            [
                'status' => true,
                'msg' => 'Get Department lists!',
                'data' => $depart_list
            ]
        );
    }

    //Get user list as per department
    public function get_depart_user_list(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'department_id' => 'required'
        ]);

        if ($validator->fails()) {

            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $department_ids = $request->department_id;
        $department_id_arr = explode(",", $department_ids);


        $user_list = User::join('employee', 'employee.user_id', '=', 'users.id')
            ->where('users.status', 'Enabled')
            ->where('users.id', '!=', $request_data['user_id'])
            ->whereIn('employee.department_id', $department_id_arr)
            ->get(['users.id', 'users.name', 'users.profile_image']);



        if ($user_list->count() == 0) {
            return response()->json(
                [
                    'status' => false,
                    'msg' => config('errors.no_record.msg'),
                    'data' => [],
                    'error' => config('errors.no_record.code')
                ]
            );
        } else {

            foreach ($user_list as $key => $value) {

                if (!empty($value->profile_image))
                    $user_list[$key]->profile_image = asset('storage/' . str_replace('public/', '', $value->profile_image));
            }
        }



        return response()->json(
            [
                'status' => true,
                'msg' => 'Get Users lists!',
                'data' => $user_list
            ]
        );
    }

    //insert inward details
    public function add_inwards(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'inward_outward_title' => 'required',
            'doc_category_id' => 'required',
            'user_id' => 'required',
            'company_id' => 'required',
            'project_id' => 'required',
            'received_date' => 'required',
            //'expected_ans_date' => 'required',
            'description' => 'required',
            'document_file' => 'required'
        ]);

        if ($validator->fails()) {

            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $email_user_ids = [];

        /* $document_file = '';
        if ($request->hasFile('document_file')) {
            $document_file = $request->file('document_file');
            $file_path = $document_file->store('public/document_file');
            if ($file_path) {
                $document_file = $file_path;
            }
        } */

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

        $registry = $request->input('registry');
        $company_id = $request->input('company_id');
        $receive_date = date('Y-m-d', strtotime($request->input('received_date')));
        $rows_count = Inward_outwards::where('received_date', $receive_date)->where('company_id', $company_id)->where('type', 'Inwards')->get()->count();

        $companies_data = Companies::where('id', '=', $company_id)->get();

        $short_name = $companies_data[0]->company_short_name;

        $new_row_count = $rows_count + 1;
        $inward_outward_no = $short_name . "/" . 'INW' . "/" . date('Y/M/d', strtotime($request->input('received_date'))) . "/" . $new_row_count;
        $project_id = $request->input('project_id');
        $checkDocType = Inward_outward_doc_category::where('id', $request->input('doc_category_id'))->where('is_special', 'Yes')->get();

        if (!empty($request->input('registry'))) {

            $inward_outward_data = Inward_outwards::where('inward_outward_no', '=', $registry)->get();

            $depart_ids = $inward_outward_data[0]->department_id;

            $inward_outward_id = $inward_outward_data[0]->parent_inward_outward_no;

            $inward_arr = [
                'inward_outward_title' => $request->input('inward_outward_title'),
                'inward_outward_no' => $inward_outward_no,
                'parent_inward_outward_no' => $inward_outward_id,
                'ref_outward_number' => $request->input('ref_outward_number'),
                'description' => $request->input('description'),
                'document_file' => !empty($document_file) ? $document_file : NULL,
                'doc_mark' => !empty($request->input('is_important')) ? 'Pending' : 'None',
                'type' => 'Inwards',
                'doc_category_id' => $request->input('doc_category_id'),
                'department_id' => $depart_ids,
                'company_id' => $request->input('company_id'),
                'project_id' => $project_id,
                'other_project_details' => $request->input('other_project'),
                'received_date' => $request->input('received_date'),
                //'expected_ans_date' => $request->input('expected_ans_date'),
                'is_reply' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'created_ip' => $request->ip(),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => $request_data['user_id'],
                'doc_sub_category_id' => $request->input('doc_sub_category_id'),
            ];

            if ($request->input('ans_expected') == 'Yes') {
                $inward_arr['expected_ans_date'] = date('Y-m-d', strtotime($request->input('expected_ans_date')));
                $inward_arr['is_answered'] = 'No';
            } else {
                $inward_arr['is_answered'] = 'Not Required';
            }

            $new_id = Inward_outwards::insertGetId($inward_arr);  //last insert id of inward_outwards Table
            //get last outward entry from this particular registry.
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
                'updated_by' => $request_data['user_id']
            ];
            Inward_outwards::where('id', $last_outward_registry->id)->update($update_parent_outward_arr);

            }
            
            $user_data = Inward_outward_users::where('inward_outward_id', '=', $inward_outward_id)
                ->pluck('user_id')->toArray();

            if ($request->input('inward_user_list')) {
                $user_id_arr = explode(',', $request->input('inward_user_list'));

                $user_data = array_merge($user_data, $user_id_arr);
            }

            if ($request->input('ans_expected') == 'Yes' || !$checkDocType->isEmpty()) {

                $user_data = $this->common_task->setSuperUserId($user_data, 1);
            }

            // insert in Inward_outward_users table

            foreach ($user_data as $key => $user) {

                $inward_users_arr = [
                    'inward_outward_id' => $new_id,
                    'user_id' => $user,
                    'status' => $key == 0 ? 'Processing' : 'Pending',
                    'expected_ans_date' => $request->input('expected_ans_date'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_ip' => $request->ip(),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                ];
                Inward_outward_users::insert($inward_users_arr);
                array_push($email_user_ids, $user);
            }

            /* $user_id_arr = [];
            if ($request->input('inward_user_id')) {

                $company_id = $request->input('company_id');
                $user_id_arr = explode(',', $request->input('inward_user_id'));
                if ($request->input('ans_expected') == 'Yes' || !$checkDocType->isEmpty()) {

                    $user_id_arr = $this->common_task->setSuperUserId($user_id_arr, $company_id);
                }

                foreach ($user_id_arr as $user_id) {         //give for each to arry
                    $inward_user_arr = [
                        'inward_outward_id' => $new_id,
                        'user_id' => $user_id,
                        'status' => 'Pending',
                        'expected_ans_date' => date('Y-m-d', strtotime($request->input('expected_ans_date'))),
                        'created_at' => date('Y-m-d H:i:s'),
                        'created_ip' => $request->ip(),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_ip' => $request->ip(),
                    ];

                    Inward_outward_users::insert($inward_user_arr);
                    array_push($email_user_ids, $user_id);
                }
            } */

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

            /* foreach ($user_id_arr as $key => $user) {

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
            } */
        } else {

            $inward_arr = [
                'inward_outward_title' => $request->input('inward_outward_title'),
                'inward_outward_no' => $inward_outward_no,
                'ref_outward_number' => $request->input('ref_outward_number'),
                'description' => $request->input('description'),
                'document_file' => !empty($document_file) ? $document_file : NULL,
                'doc_mark' => !empty($request->input('is_important')) ? 'Pending' : 'None',
                'type' => 'Inwards',
                'doc_category_id' => $request->input('doc_category_id'),
                'department_id' => $request->input('department_id'),
                'company_id' => $request->input('company_id'),
                'project_id' => $project_id,
                'other_project_details' => $request->input('other_project'),
                'received_date' => $request->input('received_date'),
                //'expected_ans_date' => $request->input('expected_ans_date'),
                'is_reply' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'created_ip' => $request->ip(),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => $request_data['user_id'],
                'doc_sub_category_id' => $request->input('doc_sub_category_id'),
            ];

            if ($request->input('ans_expected') == 'Yes') {
                $inward_arr['expected_ans_date'] = date('Y-m-d', strtotime($request->input('expected_ans_date')));
                $inward_arr['is_answered'] = 'No';
            } else {
                $inward_arr['is_answered'] = 'Not Required';
            }

            $new_id = Inward_outwards::insertGetId($inward_arr);  //last insertId of inward_outwards table

            $new_inward_arr = [
                'parent_inward_outward_no' => $new_id,
                'created_at' => date('Y-m-d H:i:s'),
                'created_ip' => $request->ip(),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => $request_data['user_id']
            ];

            Inward_outwards::where('id', $new_id)->update($new_inward_arr);

            $login_user_arr = [
                'inward_outward_id' => $new_id,
                'user_id' => $request_data['user_id'],
                'status' => 'Processing',
                'expected_ans_date' => $request->input('expected_ans_date'),
                'created_at' => date('Y-m-d H:i:s'),
                'created_ip' => $request->ip(),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
            ];

            Inward_outward_users::insert($login_user_arr);

            $inward_user_ids = $request->input('inward_user_id');

            $company_id = $request->input('company_id');
            $user_id_arr = explode(",", $inward_user_ids);   //make array using explode() func..

            if ($request->input('ans_expected') == 'Yes' || !$checkDocType->isEmpty()) {

                $user_id_arr = $this->common_task->setSuperUserId($user_id_arr, 0);
            }

            foreach ($user_id_arr as $user_id) {         //give for each to arry
                $inward_user_arr = [
                    'inward_outward_id' => $new_id,
                    'user_id' => $user_id,
                    'status' => 'Pending',
                    'expected_ans_date' => date('Y-m-d', strtotime($request->input('expected_ans_date'))),
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_ip' => $request->ip(),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                ];

                Inward_outward_users::insert($inward_user_arr);
                array_push($email_user_ids, $user_id);
            }

            $login_user_views_arr = [
                'user_id' => $request_data['user_id'],
                'inward_outward_id' => $new_id,
                'is_view' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'created_ip' => $request->ip(),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
            ];

            Inward_outward_views::insert($login_user_views_arr);

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
        }

        //insert details inward_outwards_chat table

        $chat_arr = [
            'inward_outward_id' => $new_id,
            'from_user_id' => $request_data['user_id'],
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
        if ($request->input('is_important') == "Pending") {
            $assistant_ids = user::where('status', 'Enabled')->where('role', config('constants.ASSISTANT'))->pluck('id')->toArray();
            $this->notification_task->markAsImpoNotify($assistant_ids, $inward_outward_no);
        }
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

        return response()->json(
            ['status' => true, 'msg' => 'New inward details successfully added!', 'data' => []]
        );
    }

    //Get outward list
    public function get_outward_list(Request $request)  //03/06/2020
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }


        $request_data = $request->all();
        $user_id = $request_data['user_id'];
        $search_date = $request->input('search_date');

        $logged_in_userdata = User::where('id', $request_data['user_id'])->get();
        
        $select_fileds = ['inward_outwards.*', 'company.company_name', 'project.project_name', 'inward_outward_doc_category.category_name',
                'inward_outward_doc_sub_category.sub_category_name','inward_outward_delivery_mode.name as delivery_mode_name',
                'users.name as requested_by_name','department.dept_name'];
        if ($request->has('search_date')) {

            // $outward_data = Inward_outwards::select('inward_outwards.*', 'company.company_name', 'project.project_name', 'inward_outward_doc_category.category_name', 
            // 'inward_outward_doc_sub_category.sub_category_name','inward_outward_delivery_mode.name as delivery_mode_name','sender.name as sender_type',
            // 'users.name as requested_by_name','department.dept_name')
            //     ->join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
            //     ->join('company', 'company.id', '=', 'inward_outwards.company_id')
            //     ->join('department', 'department.id', '=', 'inward_outwards.department_id')
            //         ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
            //         ->leftjoin('sender', 'sender.id', '=', 'inward_outwards.sender_id')
            //         ->leftjoin('inward_outward_prime_action','inward_outward_prime_action.inward_outward_id','=','inward_outwards.id')
            //         ->leftjoin('inward_outward_distrubuted_work','inward_outward_distrubuted_work.inward_outward_prime_action_id','=','inward_outward_prime_action.id')
            //         ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
            //     ->join('project', 'project.id', '=', 'inward_outwards.project_id')
            //     ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
            //     ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
            //     ->whereDate('inward_outwards.created_at', '=', $search_date)
            //     ->groupBy('inward_outward_distrubuted_work.inward_outward_prime_action_id')
            //     ->where(function ($query) use ($request_data, $logged_in_userdata) {
            //         //if ($logged_in_userdata[0]->role != config('constants.SuperUser')) {
            //         $query->where('inward_outward_users.user_id', '=', $request_data['user_id'])
            //               ->where('inward_outwards.inserted_by', $request_data['user_id'])
            //               ->orWhere('inward_outwards.requested_by', $request_data['user_id'] )
            //               ->orWhere(function ($query) use ($request_data) {
            //                 $query->Where('inward_outwards.prime_employee_id', $request_data['user_id'] )
            //                       ->Where('inward_outwards.prime_user_status','Accepted');
            //             })->orWhere(function ($query) use ($request_data) {
            //                 $query->Where('inward_outward_distrubuted_work.support_employee_id', $request_data['user_id'] )
            //                       ->Where('inward_outward_distrubuted_work.emp_status','Accepted');
            //             });
            //         //}
            //     })
            //     //->where('inward_outwards.is_reply', '=', 0)
            //     ->where('inward_outwards.type', '=', 'Outwards')
            //     ->orderBy('inward_outwards.id', 'DESC')
            //     ->get();

                // /--------------------
                $partial_query = Inward_outwards::
                        join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
                        ->join('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
                        ->join('company', 'company.id', '=', 'inward_outwards.company_id')
                        ->join('project', 'project.id', '=', 'inward_outwards.project_id')
                        ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
                        ->leftJoin('department', 'department.id', '=', 'inward_outwards.department_id')
                        ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
                        ->join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
                        ->where('inward_outwards.type', '=', 'Outwards')
                        ->whereDate('inward_outwards.created_at', '=', $search_date)
                        ->orderBy('inward_outwards.id', 'DESC')
                        ->groupBy('inward_outward_users.inward_outward_id');
         
                $permission_arr = $this->common_task->getPermissionArr($logged_in_userdata[0]->role, 40);  //fetch view permissions of users
                
                if (in_array(5, $permission_arr)) {
                        $outward_data =  $partial_query; //show all  request
                } elseif (in_array(1, $permission_arr)) {
                        $outward_data =  $partial_query->where(function ($query) use ($user_id)  {
                            $query->where('inward_outwards.inserted_by', $user_id)
                                    ->orWhere('inward_outwards.requested_by', $user_id )
                                    ->orWhere('inward_outward_users.user_id', $user_id);
                    });
                } else {

                    return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
                }

        } else {

            $partial_query = Inward_outwards::
                    join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
                    ->join('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
                    ->join('company', 'company.id', '=', 'inward_outwards.company_id')
                    ->join('project', 'project.id', '=', 'inward_outwards.project_id')
                    ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
                    ->leftJoin('department', 'department.id', '=', 'inward_outwards.department_id')
                    ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
                    ->join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
                    ->where('inward_outwards.type', '=', 'Outwards')
                    ->orderBy('inward_outwards.id', 'DESC')
                    ->groupBy('inward_outward_users.inward_outward_id');

            $permission_arr = $this->common_task->getPermissionArr($logged_in_userdata[0]->role, 40);  //fetch view permissions of users
            
            if (in_array(5, $permission_arr)) {
                    $outward_data =  $partial_query; //show all  request
            } elseif (in_array(1, $permission_arr)) {
                    $outward_data =  $partial_query->where(function ($query) use ($user_id)  {
                        $query->where('inward_outwards.inserted_by', $user_id)
                                ->orWhere('inward_outwards.requested_by', $user_id )
                                ->orWhere('inward_outward_users.user_id', $user_id);
                });
            } else {
                return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
            }
        }
        
        $outward_data = $outward_data->get($select_fileds);
        if ($outward_data->count() == 0) {

            return response()->json(['status' => false,'msg' => config('errors.no_record.msg'),'data' => [],'error' => config('errors.no_record.code')]);
        }

        foreach ($outward_data as $key => $value) {

            if (!empty($value->document_file))
                $outward_data[$key]->document_file = asset('storage/' . str_replace('public/', '', $value->document_file));
        
                if (!empty($value->delivery_file)) {
                    $outward_data[$key]->delivery_file = asset('storage/' . str_replace('public/', '', $value->delivery_file));
                }
        }

        $outward_list = [];

        foreach ($outward_data as $key => $main_outward) {

            if ($main_outward->is_reply == 0) {


                array_push($select_fileds,'inward_outward_views.is_view');
                $thread_outward = Inward_outwards::
                        join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
                        ->join('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
                        ->join('company', 'company.id', '=', 'inward_outwards.company_id')
                        ->join('inward_outward_views', 'inward_outward_views.inward_outward_id', '=', 'inward_outwards.id')
                        ->join('project', 'project.id', '=', 'inward_outwards.project_id')
                        ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
                        ->leftJoin('department', 'department.id', '=', 'inward_outwards.department_id')
                        ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
                        ->join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
                        ->where('inward_outwards.parent_inward_outward_no', '=', $main_outward->parent_inward_outward_no)
                        ->get($select_fileds);

                        // $thread_outward = Inward_outwards::select('inward_outwards.*', 'inward_outward_views.is_view', 'company.company_name', 'project.project_name', 'inward_outward_doc_category.category_name',
                //  'inward_outward_doc_sub_category.sub_category_name','inward_outward_delivery_mode.name as delivery_mode_name','sender.name as sender_type',
                //  'users.name as requested_by_name','department.dept_name')
                //     ->join('inward_outward_views', 'inward_outward_views.inward_outward_id', '=', 'inward_outwards.id')
                //     ->join('company', 'company.id', '=', 'inward_outwards.company_id')
                //     ->join('department', 'department.id', '=', 'inward_outwards.department_id')
                //     ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
                //     ->leftjoin('sender', 'sender.id', '=', 'inward_outwards.sender_id')
                //     ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
                //     ->join('project', 'project.id', '=', 'inward_outwards.project_id')
                //     ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
                //     ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
                //     ->where('inward_outwards.parent_inward_outward_no', '=', $main_outward->parent_inward_outward_no)
                //     ->where('inward_outward_views.user_id', '=', $request_data['user_id'])
                //     //->orderBy('inward_outwards.id', 'DESC')
                //     ->get();


                foreach ($thread_outward as $thread__key => $value) {

                    $this->data['users_data'] = $users_data = Inward_outward_users::select('inward_outward_users.user_id', 'inward_outward_users.status', 'users.name')
                        ->join('users', 'users.id', '=', 'inward_outward_users.user_id')
                        ->where('inward_outward_users.inward_outward_id', '=', $value->id)->get();


                    if (!empty($value->document_file)) {

                        $thread_outward[$thread__key]->document_file = asset('storage/' . str_replace('public/', '', $value->document_file));
                    }
                    if (!empty($value->delivery_file)) {

                        $thread_outward[$thread__key]->delivery_file = asset('storage/' . str_replace('public/', '', $value->delivery_file));
                    }
                    $thread_outward[$thread__key]->users_list = $users_data;
                }

                $outward_list[$key]['main_outward'] = $main_outward;
                $outward_list[$key]['thread_outward'] = $thread_outward;
            } else {

                $outward_list[$key]['main_outward'] = $main_outward;

                $this->data['users_data'] = $users_data = Inward_outward_users::select('inward_outward_users.user_id', 'inward_outward_users.status', 'users.name')
                    ->join('users', 'users.id', '=', 'inward_outward_users.user_id')
                    ->where('inward_outward_users.inward_outward_id', '=', $main_outward->id)->get();

                $outward_list[$key]['thread_outward'][] = $main_outward;
                $outward_list[$key]['thread_outward'][0]['users_list'] = $users_data;
            }
        }


        return response()->json(
            [
                'status' => true,
                'msg' => 'Get Outward details!',
                'data' => $outward_list
            ]
        );
    }

    #Get Today Outward list
    public function get_today_outward_list(Request $request)  //16/06/2020
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }


        $request_data = $request->all();
        $today_date = date('Y-m-d');
        $user_id = $request_data['user_id'];
        $search_date = $request->input('search_date');

        $logged_in_userdata = User::where('id', $request_data['user_id'])->get();
        
        $select_fileds = ['inward_outwards.*', 'company.company_name', 'project.project_name', 'inward_outward_doc_category.category_name',
                'inward_outward_doc_sub_category.sub_category_name','inward_outward_delivery_mode.name as delivery_mode_name',
                'users.name as requested_by_name','department.dept_name'];

            $partial_query = Inward_outwards::
                    join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
                    ->join('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
                    ->join('company', 'company.id', '=', 'inward_outwards.company_id')
                    ->join('project', 'project.id', '=', 'inward_outwards.project_id')
                    ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
                    ->leftJoin('department', 'department.id', '=', 'inward_outwards.department_id')
                    ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
                    ->join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
                    ->where('inward_outwards.type', '=', 'Outwards')
                    ->whereDate('inward_outwards.created_at', '=', $today_date)
                    ->orderBy('inward_outwards.id', 'DESC')
                    ->groupBy('inward_outward_users.inward_outward_id');

            $permission_arr = $this->common_task->getPermissionArr($logged_in_userdata[0]->role, 40);  //fetch view permissions of users
            
            if (in_array(5, $permission_arr)) {
                    $outward_data =  $partial_query; //show all  request
            } elseif (in_array(1, $permission_arr)) {
                    $outward_data =  $partial_query->where(function ($query) use ($user_id)  {
                        $query->where('inward_outwards.inserted_by', $user_id)
                                ->orWhere('inward_outwards.requested_by', $user_id )
                                ->orWhere('inward_outward_users.user_id', $user_id);
                });
            } else {
                return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
            }
        
        $outward_data = $outward_data->get($select_fileds);
        if ($outward_data->count() == 0) {

            return response()->json(['status' => false,'msg' => config('errors.no_record.msg'),'data' => [],'error' => config('errors.no_record.code')]);
        }

        foreach ($outward_data as $key => $value) {

            if (!empty($value->document_file))
                $outward_data[$key]->document_file = asset('storage/' . str_replace('public/', '', $value->document_file));
        
                if (!empty($value->delivery_file)) {
                    $outward_data[$key]->delivery_file = asset('storage/' . str_replace('public/', '', $value->delivery_file));
                }
        }

        $outward_list = [];

        foreach ($outward_data as $key => $main_outward) {

            if ($main_outward->is_reply == 0) {


                array_push($select_fileds,'inward_outward_views.is_view');
                $thread_outward = Inward_outwards::
                        join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
                        ->join('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
                        ->join('company', 'company.id', '=', 'inward_outwards.company_id')
                        ->join('inward_outward_views', 'inward_outward_views.inward_outward_id', '=', 'inward_outwards.id')
                        ->join('project', 'project.id', '=', 'inward_outwards.project_id')
                        ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
                        ->leftJoin('department', 'department.id', '=', 'inward_outwards.department_id')
                        ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
                        ->join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
                        ->where('inward_outwards.parent_inward_outward_no', '=', $main_outward->parent_inward_outward_no)
                        ->get($select_fileds);

                foreach ($thread_outward as $thread__key => $value) {

                    $this->data['users_data'] = $users_data = Inward_outward_users::select('inward_outward_users.user_id', 'inward_outward_users.status', 'users.name')
                        ->join('users', 'users.id', '=', 'inward_outward_users.user_id')
                        ->where('inward_outward_users.inward_outward_id', '=', $value->id)->get();


                    if (!empty($value->document_file)) {

                        $thread_outward[$thread__key]->document_file = asset('storage/' . str_replace('public/', '', $value->document_file));
                    }
                    if (!empty($value->delivery_file)) {

                        $thread_outward[$thread__key]->delivery_file = asset('storage/' . str_replace('public/', '', $value->delivery_file));
                    }
                    $thread_outward[$thread__key]->users_list = $users_data;
                }

                $outward_list[$key]['main_outward'] = $main_outward;
                $outward_list[$key]['thread_outward'] = $thread_outward;
            } else {

                $outward_list[$key]['main_outward'] = $main_outward;

                $this->data['users_data'] = $users_data = Inward_outward_users::select('inward_outward_users.user_id', 'inward_outward_users.status', 'users.name')
                    ->join('users', 'users.id', '=', 'inward_outward_users.user_id')
                    ->where('inward_outward_users.inward_outward_id', '=', $main_outward->id)->get();

                $outward_list[$key]['thread_outward'][] = $main_outward;
                $outward_list[$key]['thread_outward'][0]['users_list'] = $users_data;
            }
        }


        return response()->json(
            [
                'status' => true,
                'msg' => 'Get Outward details!',
                'data' => $outward_list
            ]
        );
    }

    //============================= 03/06/2020 ================================================
    public function get_assignee_registry(Request $request)    //check
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $select_fields = ['inward_outwards.*','company.company_name', 'project.project_name',
        'inward_outward_doc_category.category_name', 'inward_outward_doc_sub_category.sub_category_name',
        'inward_outward_delivery_mode.name as delivery_mode_name','sender.name as sender_type',
        'users.name as requested_by_name','department.dept_name'];

        $assignee_registry =  Inward_outwards::join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
            ->join('company', 'company.id', '=', 'inward_outwards.company_id')
            ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
            ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
            ->join('department', 'department.id', '=', 'inward_outwards.department_id')
            ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
            ->leftjoin('sender', 'sender.id', '=', 'inward_outwards.sender_id')
            ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
            ->join('project', 'project.id', '=', 'inward_outwards.project_id')
            ->where('inward_outward_users.status', '=', 'Processing')
            ->whereDate('inward_outwards.created_at','>=','2020-06-02')
            ->where('inward_outward_users.user_id', '=', $request_data['user_id'])
            ->get($select_fields);

        if ($assignee_registry->count() == 0) {
            return response()->json(['status' => false,'msg' => config('errors.no_record.msg'),'data' => [],'error' => config('errors.no_record.code')]);
        }

        foreach ($assignee_registry as $key => $value) {
            if (!empty($value->document_file)) {
                $assignee_registry[$key]->document_file = asset('storage/' . str_replace('public/', '', $value->document_file));
            }
            if (!empty($value->delivery_file)) {
                $assignee_registry[$key]->delivery_file = asset('storage/' . str_replace('public/', '', $value->delivery_file));
            }
        }
        return response()->json(['status' => true,'msg' => 'Get assignee registy!','data' => $assignee_registry]);

    }

    public function accept_assignee_registry(Request $request)  //check  message  1
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'registry_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $logged_in_userdata = User::where('id', $request_data['user_id'])->first();
        $update_arr = [
            'status' => 'Completed',
            'action_date' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip()
        ];

            $cc_email_list = [];
            Inward_outward_users::where('inward_outward_id', $request_data['registry_id'])->update($update_arr);
            
            $inward_details = Inward_outwards::where('id', $request_data['registry_id'])->get(['inward_outward_no','inserted_by','requested_by']);
                $requested_by = User::where('id', $inward_details[0]->requested_by)->value('email');
                $superUser = User::where('status', 'Enabled')->where('role', config('constants.SuperUser'))->value('email');
                $inserted_by = User::where('id', $inward_details[0]->inserted_by)->pluck('email')->toArray();
        
            
            array_push($cc_email_list , $superUser, $requested_by);
            
            $mail_data = [
                'registry_no' => $inward_details[0]->inward_outward_no,
                'user_name' => $logged_in_userdata['name'],
                'email_list' => $inserted_by,
                'cc_email_list' => $cc_email_list
            ];

            $this->common_task->acceptDocumentRegistry($mail_data);

        return response()->json(['status' => true,'msg' => 'Assignee registy successfully accepted , Please go to Location of Document menu in Website for locate document and add Main/Prime Employee in Action Required Inwards. !','data' => []]);

    }

    public function reject_assignee_registry(Request $request)  //check     2
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'registry_id' => 'required',
            'reject_note' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $logged_in_userdata = User::where('id', $request_data['user_id'])->first();

        $update_arr = [
            'status' => 'Rejected',
            'reject_note' => $request_data['reject_note'],
            'action_date' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip()
        ];

            $cc_email_list = [];
            Inward_outward_users::where('inward_outward_id', $request_data['registry_id'])->update($update_arr);
            
            $inward_details = Inward_outwards::where('id', $request_data['registry_id'])->get(['inward_outward_no','inserted_by','requested_by']);
                $requested_by = User::where('id', $inward_details[0]->requested_by)->value('email');
                $superUser = User::where('status', 'Enabled')->where('role', config('constants.SuperUser'))->value('email');
                $inserted_by = User::where('id', $inward_details[0]->inserted_by)->pluck('email')->toArray();
            
            array_push($cc_email_list , $superUser, $requested_by);
            
            $mail_data = [
                'registry_no' => $inward_details[0]->inward_outward_no,
                'user_name' => $logged_in_userdata['name'],
                'email_list' => $inserted_by,
                'cc_email_list' => $cc_email_list
            ];

            $this->common_task->rejectDocumentRegistry($mail_data);
        
        return response()->json(['status' => true,'msg' => 'Reject assignee registy successfully!','data' => []]);

    }

    public function get_prime_user_registry(Request $request)   //check
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $select_fields = ['inward_outwards.*','B.name as assignee_user','company.company_name', 'project.project_name',
        'inward_outward_doc_category.category_name', 'inward_outward_doc_sub_category.sub_category_name',
        'inward_outward_delivery_mode.name as delivery_mode_name','sender.name as sender_type',
        'users.name as requested_by_name','department.dept_name'];

        $prime_registry =  Inward_outwards::join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
            ->join('company', 'company.id', '=', 'inward_outwards.company_id')
            ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
            ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
            ->join('department', 'department.id', '=', 'inward_outwards.department_id')
            ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
            ->leftjoin('users as B', 'B.id', '=', 'inward_outward_users.user_id')
            ->leftjoin('sender', 'sender.id', '=', 'inward_outwards.sender_id')
            ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
            ->join('project', 'project.id', '=', 'inward_outwards.project_id')
            ->where('inward_outwards.prime_user_status','=', 'Assigned')
            ->whereDate('inward_outwards.created_at','>=','2020-06-02')
            ->where('inward_outwards.prime_employee_id', $request_data['user_id'])->get($select_fields);

        if ($prime_registry->count() == 0) {
            return response()->json(['status' => false,'msg' => config('errors.no_record.msg'),'data' => [],'error' => config('errors.no_record.code')]);
        }

        foreach ($prime_registry as $key => $value) {
            if (!empty($value->document_file)) {
                $prime_registry[$key]->document_file = asset('storage/' . str_replace('public/', '', $value->document_file));
            }
            if (!empty($value->delivery_file)) {
                $prime_registry[$key]->delivery_file = asset('storage/' . str_replace('public/', '', $value->delivery_file));
            }
        }
        return response()->json(['status' => true,'msg' => 'Get prime registy!','data' => $prime_registry]);

    }

    public function accept_requestByPrimeUser(Request $request)    //check  message  3
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'registry_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $logged_in_userdata = User::where('id', $request_data['user_id'])->first();
        
        $update_arr = [
            'prime_user_status' => 'Accepted',
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip() 
        ];

        if (Inward_outwards::where('id', $request_data['registry_id'])->update($update_arr)) {

            $insert_arr = [
                'inward_outward_id' =>  $request_data['registry_id'],
                'created_at' => date('Y-m-d H:i:s'),
                'created_ip' => $request->ip() ,
                'updated_by' => $request_data['user_id']
            ];
            Inward_outward_prime_action::insert($insert_arr);
            
                $inward_data = Inward_outwards::where('id', $request_data['registry_id'])->get(['inward_outward_no','inserted_by']);
                $mail = User::where('id',$inward_data[0]->inserted_by)->pluck('email')->toArray();
                $data = [
                    'user_name' => $logged_in_userdata['name'],
                    'registry_no' => $inward_data[0]->inward_outward_no,
                    'date' => date('d-m-Y H:i a'),
                    'email_list' => $mail
                ];

                $this->common_task->acceptRegistryDocumentPrimeUser($data);
   
            return response()->json(['status' => true,'msg' => 'Prime registy request successfully accepted, add distrubuted work related task in Supporting/Prime Employee Documents of Registry Documents Menu!','data' => []]);
        }

            return response()->json(['status' => false,'msg' => 'Oops..Error occured..Try again !','data' => []]);

    }

    public function reject_requestByPrimeUser(Request $request)  //check    4
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'registry_id' => 'required',
            'reject_note' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $logged_in_userdata = User::where('id', $request_data['user_id'])->first();

        $id = $request_data['registry_id'];
        
        $reject_arr = [
            'prime_user_status' => 'Rejected',
            'reject_reason' => $request_data['reject_note'],
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip()  
        ];
    
        if (Inward_outwards::where('id', $id)->update($reject_arr)) {
            
        
            $inward_data = Inward_outwards::where('id', $id)->get(['inward_outward_no','inserted_by']);
            $mail = User::where('id',$inward_data[0]->inserted_by)->pluck('email')->toArray();
            $data = [
                'user_name' => $logged_in_userdata['name'],
                'reason' => $request->input('reject_note'),
                'registry_no' => $inward_data[0]->inward_outward_no,
                'date' => date('d-m-Y H:i a'),
                'email_list' => $mail
            ];
            $this->common_task->rejectRegistryDocumentPrimeUser($data);
           
            return response()->json(['status' => true,'msg' => 'Reject prime registy successfully!','data' => []]);
        }

        return response()->json(['status' => false,'msg' => 'Oops..Error occured..Try again !','data' => []]);

    }

    public function get_support_user_registry(Request $request)    //check
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $select_fields = ['users.name as prime_user','inward_outward_distrubuted_work.id',
            'inward_outwards.inward_outward_no','inward_outward_distrubuted_work.work_status',
            'inward_outward_distrubuted_work.working_start_datetime',
            'inward_outward_distrubuted_work.task_hour',
            'inward_outward_distrubuted_work.emp_status',
            'inward_outward_distrubuted_work.task_percentage',
            'inward_outward_distrubuted_work.work_day','inward_outward_prime_action.work_details'];

        $support_user_registry = Inward_outward_distrubuted_work::join('inward_outward_prime_action','inward_outward_prime_action.id','=','inward_outward_distrubuted_work.inward_outward_prime_action_id')
            ->join('inward_outwards', 'inward_outwards.id','=','inward_outward_prime_action.inward_outward_id') 
            ->join('users','users.id','=','inward_outwards.prime_employee_id')
            ->where('inward_outward_distrubuted_work.emp_status','Assigned')
            ->whereDate('inward_outwards.created_at','>=','2020-06-02')
            ->where('inward_outward_distrubuted_work.support_employee_id','=',$request_data['user_id'])
            ->get($select_fields);

        if ($support_user_registry->count() == 0) {
            return response()->json(['status' => false,'msg' => config('errors.no_record.msg'),'data' => [],'error' => config('errors.no_record.code')]);
        }

        return response()->json(['status' => true,'msg' => 'Get support user registy!','data' => $support_user_registry]);

    }

    public function accept_requestBySupportEmp(Request $request)  //check  message  5
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'distrubuted_work_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $logged_in_userdata = User::where('id', $request_data['user_id'])->first();
        
        $id = $request_data['distrubuted_work_id'];
        $update_arr = [
            'emp_status' => 'Accepted',
            'updated_at' => date('Y-m-d H:i:s'),
            'action_date_time' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip() 
        ];

        if (Inward_outward_distrubuted_work::where('id', $id)->update($update_arr)) {

            
            $inward_data = Inward_outward_distrubuted_work::join('inward_outward_prime_action','inward_outward_prime_action.id','=','inward_outward_distrubuted_work.inward_outward_prime_action_id')
                ->join('inward_outwards', 'inward_outwards.id','=','inward_outward_prime_action.inward_outward_id')
                ->where('inward_outward_distrubuted_work.id', $id)
                ->get(['inward_outwards.prime_employee_id','inward_outwards.inward_outward_no']);
            
            $mail = User::where('id',$inward_data[0]->prime_employee_id)->pluck('email')->toArray();
            $user_name = $logged_in_userdata['name'];
            $data = [
                'user_name' => $user_name,
                'date' => date('d-m-Y H:i a'),
                'registry_no' => $inward_data[0]->inward_outward_no,
                'email_list' => $mail
            ];
         
            $this->common_task->acceptDistrubutedWorkRequestSupportEmp($data);
            $notify_ids = User::where('id',$inward_data[0]->prime_employee_id)->pluck('id')->toArray();
            $this->notification_task->supportEmpAcceptNotify($notify_ids, $user_name);
            //dd($data, $notify_ids);
            return response()->json(['status' => true,'msg' => 'Registy task request successfully accepted , Please submit your distrubuted Time Taken for Complete Task in Supporting/Prime Employee Documents of Registry Documents Menu!','data' => []]);
        }

        return response()->json(['status' => false,'msg' => 'Oops..Error occured..Try again !','data' => []]);

    }

    //pendig...
    public function reject_requestBySupportEmp(Request $request)  //check    6
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'distrubuted_work_id' => 'required',
            'check_btn' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $logged_in_userdata = User::where('id', $request_data['user_id'])->first();
        
        $id = $request->get('distrubuted_work_id');
        $checkReason = $request->input('check_btn');

        $inward_data = Inward_outward_distrubuted_work::join('inward_outward_prime_action','inward_outward_prime_action.id','=','inward_outward_distrubuted_work.inward_outward_prime_action_id')
            ->join('inward_outwards', 'inward_outwards.id','=','inward_outward_prime_action.inward_outward_id')
            ->where('inward_outward_distrubuted_work.id', $id)
            ->get(['inward_outwards.prime_employee_id','inward_outwards.inward_outward_no']);
    
        $mail = User::where('id',$inward_data[0]->prime_employee_id)->pluck('email')->toArray();
        $user_name = $logged_in_userdata['name'];

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

                    $notify_ids = User::where('id',$inward_data[0]->prime_employee_id)->pluck('id')->toArray();
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

            $notify_ids = User::where('id',$inward_data[0]->prime_employee_id)->pluck('id')->toArray();
            $this->notification_task->supportEmpRejectNotify($notify_ids, $user_name);
     
        }
        //dd($data, $notify_ids);
        if (Inward_outward_distrubuted_work::where('id', $id)->update($reject_arr)) {
            return response()->json(['status' => true,'msg' => 'Reject support user registy successfully!','data' => []]);
        }

        return response()->json(['status' => false,'msg' => 'Oops..Error occured..Try again !','data' => []]);

    }

    //04/06/2020
    public function get_rejected_support_emp_entry(Request $request)   //check
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();

        $emp_details = Inward_outward_distrubuted_work::join('inward_outward_prime_action','inward_outward_prime_action.id','=','inward_outward_distrubuted_work.inward_outward_prime_action_id')
                        ->join('inward_outwards', 'inward_outwards.id','=','inward_outward_prime_action.inward_outward_id')               
                        ->join('users','users.id','=','inward_outward_distrubuted_work.support_employee_id')
                        ->where('inward_outward_prime_action.final_status','Pending')
                        ->where('inward_outward_distrubuted_work.emp_status','Rejected') 
                        ->whereDate('inward_outwards.created_at','>=','2020-06-02')
                        ->where('inward_outwards.prime_employee_id',$request_data['user_id'])
                        ->get(['users.name as emp_name','inward_outwards.inward_outward_no','inward_outward_distrubuted_work.*']);
        if ($emp_details->count() == 0) {
            return response()->json(['status' => false,'msg' => config('errors.no_record.msg'),'data' => [],'error' => config('errors.no_record.code')]);
        }
        
        foreach ($emp_details as $key => $value) {
            if ($value->satisfied_reason) {
                $satisfy_hour = $value->satisfied_reason*$value->task_hour/$value->task_percentage;
                //$txt = '<p>-> Employee has query regarding percentage of work you had assigned to him.<p>-> Expected Percentage of work by employee:'. ' ' . $value->satisfied_reason. '%' . ' ' . '(' . $satisfy_hour . 'Hour' . ').';
                //$txt.= '<br><br>-> So Please Accept or Reject his query so system can proceed accordingly.';
                $emp_details[$key]->satisfied_reason = $value->satisfied_reason. '%' . ' ' . '(' . $satisfy_hour . 'Hour' . ')';
            }
        }

        return response()->json(['status' => true,'msg' => 'Get Entries!','data' => $emp_details]);

    }

    public function acceptEmpRequest(Request $request)     //check   message  7
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'distrubuted_work_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $logged_in_userdata = User::where('id', $request_data['user_id'])->first();
        
        $id = $request_data['distrubuted_work_id'];
        
        $percent = Inward_outward_distrubuted_work::where('id',$id)->get(['task_percentage','task_hour','satisfied_reason','inward_outward_prime_action_id']);
        $diff = $percent[0]->satisfied_reason - $percent[0]->task_percentage;

            $main_userId = Inward_outward_distrubuted_work::where('inward_outward_prime_action_id',$percent[0]->inward_outward_prime_action_id)->first();

            $main_final_perc = $main_userId['task_percentage'] - $diff;
            $main_final_hour = $main_userId['task_hour'] * $main_final_perc / $main_userId['task_percentage'];
            Inward_outward_distrubuted_work::where('id',$main_userId['id'])->update(['task_percentage' => $main_final_perc, 'task_hour' => $main_final_hour]);
        
        $task_hour = $percent[0]->task_hour * $percent[0]->satisfied_reason / $percent[0]->task_percentage;
        $update_arr = [
                'emp_status' => 'Accepted',
                'task_percentage' => $percent[0]->satisfied_reason,
                'task_hour' =>  $task_hour,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip() 
        ];
    
        if (Inward_outward_distrubuted_work::where('id', $id)->update($update_arr)) {

            $inward_data = Inward_outward_prime_action::join('inward_outwards', 'inward_outwards.id','=','inward_outward_prime_action.inward_outward_id')
                ->where('inward_outward_prime_action.id', $percent[0]->inward_outward_prime_action_id)
                ->get(['inward_outwards.prime_employee_id','inward_outwards.inward_outward_no']);
                
            $notify_ids = Inward_outward_distrubuted_work::where('id',$id)->pluck('support_employee_id')->toArray();
            $registry_no = $inward_data[0]->inward_outward_no;
            $prime_user = $logged_in_userdata['name'];
            $this->notification_task->empWorkPercentageUpdatedNotify($notify_ids, $registry_no, $prime_user);
       
            return response()->json(['status' => true,'msg' => 'Support Employee rejection request successfully accepted!','data' => []]);
        }
    
        return response()->json(['status' => false,'msg' => 'Oops..Error occured..Try again !','data' => []]);

    }

    public function rejectEmpRequest(Request $request)    //check 50%    8
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'distrubuted_work_id' => 'required',
            'reject_reason' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $logged_in_userdata = User::where('id', $request_data['user_id'])->first();
        
        $id = $request_data['distrubuted_work_id'];

            $percent = Inward_outward_distrubuted_work::where('id',$id)->get(['task_percentage','task_hour','satisfied_reason','inward_outward_prime_action_id']);
            $diff = $percent[0]->satisfied_reason - $percent[0]->task_percentage;
            
            if ( $diff >= 2 && $diff <= 10) {
                $number = $diff / 2;
                $final_perc = $percent[0]->task_percentage + $number;
                $final_hour = $percent[0]->task_hour * $final_perc / $percent[0]->task_percentage;
                Inward_outward_distrubuted_work::where('id',$id)->update(['task_percentage' => $final_perc,'task_hour' => $final_hour , 'emp_status' => 'Accepted']);
                
                $main_userId = Inward_outward_distrubuted_work::where('inward_outward_prime_action_id',$percent[0]->inward_outward_prime_action_id)->first();
                $main_final_perc = $main_userId['task_percentage'] - $number;
                $main_final_hour = $main_userId['task_hour'] * $main_final_perc / $main_userId['task_percentage'];
                Inward_outward_distrubuted_work::where('id',$main_userId['id'])->update(['task_percentage' => $main_final_perc, 'task_hour' => $main_final_hour]);
            
                $reject_arr = [
                    'emp_status' => 'Accepted',
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip()  
                ];

                Inward_outward_distrubuted_work::where('id', $id)->update($reject_arr);
   
                return response()->json(['status' => true,'msg' => 'System has accept request successfully!','data' => []]);

            } else {

                    $update_arr = [
                        'final_status' => 'Rejected',
                        'reject_reason' => $request->input('reject_reason'),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_ip' => $request->ip()  
                    ];
                    Inward_outward_prime_action::where('id', $percent[0]->inward_outward_prime_action_id)->update($update_arr);
                    //==============================================
                    $inward_data = Inward_outward_prime_action::join('inward_outwards', 'inward_outwards.id','=','inward_outward_prime_action.inward_outward_id')
                        ->where('inward_outward_prime_action.id', $percent[0]->inward_outward_prime_action_id)
                        ->get(['inward_outwards.prime_employee_id','inward_outwards.inward_outward_no']);
        
                        $mail_data = [];
                        $mail_data['email_list'] = user::where('status', 'Enabled')->where('role', config('constants.SuperUser'))->pluck('email')->toArray();
                        $mail_data['registry_no'] = $inward_data[0]->inward_outward_no;
                        $mail_data['date'] = date('d-m-Y H:i a');
                        $mail_data['user_name'] = $logged_in_userdata['name'];
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
                     
                return response()->json(['status' => true,'msg' => 'Support Employee rejection request successfully rejected!','data' => []]);
            }
                return response()->json(['status' => false,'msg' => 'Oops..Error occured..Try again !','data' => []]);

    }

    // for general reason
    public function removeEmpFromRegistry(Request $request)     //check  message   9
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'distrubuted_work_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $logged_in_userdata = User::where('id', $request_data['user_id'])->first();
        
        $id = $request_data['distrubuted_work_id'];

        if (Inward_outward_distrubuted_work::where('id', $id)->delete()) {
            $emp_id = Inward_outward_distrubuted_work::where('id',$id)->pluck('support_employee_id')->toArray();
            return response()->json(['status' => true,'msg' => 'Supporting Employee succesfully removed!','data' => []]);
        }

        return response()->json(['status' => false,'msg' => 'Oops..Error occured..Try again !','data' => []]);

    }

    // not in use now ....
    public function get_submitted_support_emp_entry(Request $request)    //check 
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();

        $submit_entries = Inward_outward_distrubuted_work::join('inward_outward_prime_action','inward_outward_prime_action.id','=','inward_outward_distrubuted_work.inward_outward_prime_action_id')
            ->join('inward_outwards', 'inward_outwards.id','=','inward_outward_prime_action.inward_outward_id') 
            ->join('users','users.id','=','inward_outward_distrubuted_work.support_employee_id')
            ->where('inward_outward_distrubuted_work.emp_status','Accepted')
            ->where('inward_outward_distrubuted_work.work_status','Submitted')
            ->where('inward_outwards.prime_employee_id',$request_data['user_id'])
            ->get(['users.name as emp_name','inward_outwards.inward_outward_no','inward_outward_distrubuted_work.*']);
        
        if ($submit_entries->count() == 0) {
            return response()->json(['status' => false,'msg' => config('errors.no_record.msg'),'data' => [],'error' => config('errors.no_record.code')]);
        }

        return response()->json(['status' => true,'msg' => 'Get Entries!','data' => $submit_entries]);

    }

    public function accept_emp_work(Request $request)     //check 
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'distrubuted_work_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $logged_in_userdata = User::where('id', $request_data['user_id'])->first();
        $id = $request_data['distrubuted_work_id'];

        $update_arr = [
            'work_status' => 'Accepted',
            'acceptance_datetime' => date('Y-m-d H:i:s')
        ];

        if (Inward_outward_distrubuted_work::where('id', $id)->update($update_arr)) {

            return response()->json(['status' => true,'msg' => 'Submitted work succesfully accepted!','data' => []]);
        }

        return response()->json(['status' => false,'msg' => 'Oops..Error occured..Try again !','data' => []]);

    }

    public function reject_emp_work(Request $request)     //check 
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'distrubuted_work_id' => 'required',
            'reject_note' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $logged_in_userdata = User::where('id', $request_data['user_id'])->first();
        
        $id = $request_data['distrubuted_work_id'];

        $reject_arr = [
            'work_status' => 'Rejected',
            'work_rejection_note' => $request->input('reject_note'),
            'acceptance_datetime' => date('Y-m-d H:i:s'),
        ];
    
        if (Inward_outward_distrubuted_work::where('id', $id)->update($reject_arr)) {

            $users_data = Inward_outward_distrubuted_work::join('users','users.id','=','inward_outward_distrubuted_work.support_employee_id')
                        ->where('inward_outward_distrubuted_work.id', $id)
                        ->get(['users.name as emp_name','inward_outward_distrubuted_work.support_employee_id']);
            $mail = User::where('id',$users_data[0]->support_employee_id)->pluck('email')->toArray();
            $data = [
                'user_name' => $users_data[0]->emp_name,
                'date' => date('d-m-Y H:i a'),
                'email_list' => $mail,
                'reject_note' => $request->input('reject_note'),
                'rejected_by' => $logged_in_userdata['name']
            ];

            $this->common_task->rejectWorkByPrimeUser($data);

            return response()->json(['status' => true,'msg' => 'Submitted work succesfully rejected!','data' => []]);
        }
        
        return response()->json(['status' => false,'msg' => 'Oops..Error occured..Try again !','data' => []]);

    }

    public function registry_module_count(Request $request)     //check 
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $user_id = $request_data['user_id'];
        $logged_in_userdata = User::where('id', $request_data['user_id'])->get();
        $response_data = [];
        $partial_query = Inward_outwards::
                join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
                ->join('company', 'company.id', '=', 'inward_outwards.company_id')
                ->join('project', 'project.id', '=', 'inward_outwards.project_id')
                ->leftJoin('department', 'department.id', '=', 'inward_outwards.department_id')
                ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
                ->leftjoin('sender', 'sender.id', '=', 'inward_outwards.sender_id')
                ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
                ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
                ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
                ->where('inward_outwards.type', '=', 'Inwards')
                ->orderBy('inward_outwards.id', 'DESC')
                ->groupBy('inward_outward_users.inward_outward_id');

            $permission_arr = $this->common_task->getPermissionArr($logged_in_userdata[0]->role, 40); //fetch view permissions of users
                if (in_array(5, $permission_arr)) {
                        $inward_data =  $partial_query->get(); //show all request
                } elseif (in_array(1, $permission_arr)) {
                        $inward_data =  $partial_query
                                ->leftJoin('inward_outward_prime_action','inward_outward_prime_action.inward_outward_id','=','inward_outwards.id')
                                ->leftJoin('inward_outward_distrubuted_work','inward_outward_distrubuted_work.inward_outward_prime_action_id','=','inward_outward_prime_action.id')
                                ->groupBy('inward_outward_distrubuted_work.inward_outward_prime_action_id')
                                ->where(function ($query) use ($user_id) {
                            $query->where('inward_outwards.inserted_by', $user_id)
                                    ->orWhere('inward_outwards.requested_by',$user_id )
                                    ->orWhere('inward_outward_users.user_id', $user_id)
                                    ->orWhere(function ($query) use ($user_id) {
                            $query->Where('inward_outwards.prime_employee_id', $user_id )
                                ->Where('inward_outwards.prime_user_status','Accepted');
                        })->orWhere(function ($query) use ($user_id) {
                            $query->Where('inward_outward_distrubuted_work.support_employee_id', $user_id )
                                ->Where('inward_outward_distrubuted_work.emp_status','Accepted');
                        });
                    })->get();
                } else {
                    $inward_data = [];
                }
            $inward_count = count($inward_data);
          
        #----------------------
        $outward_partial_query = Inward_outwards::
                    join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
                    ->join('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
                    ->join('company', 'company.id', '=', 'inward_outwards.company_id')
                    ->join('project', 'project.id', '=', 'inward_outwards.project_id')
                    ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
                    ->leftJoin('department', 'department.id', '=', 'inward_outwards.department_id')
                    ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
                    ->join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
                    ->where('inward_outwards.type', '=', 'Outwards')
                    ->orderBy('inward_outwards.id', 'DESC')
                    ->groupBy('inward_outward_users.inward_outward_id');

            $permission_arr = $this->common_task->getPermissionArr($logged_in_userdata[0]->role, 40);  //fetch view permissions of users
            
            if (in_array(5, $permission_arr)) {
                    $outward_data =  $outward_partial_query->get(); //show all  request
            } elseif (in_array(1, $permission_arr)) {
                    $outward_data =  $outward_partial_query->where(function ($query) use ($user_id)  {
                        $query->where('inward_outwards.inserted_by', $user_id)
                                ->orWhere('inward_outwards.requested_by', $user_id )
                                ->orWhere('inward_outward_users.user_id', $user_id);
                        })->get();
            } else {
                    $outward_data = [];
            } 

            $outward_count = count($outward_data); 
        $today_date = date('Y-m-d');
        #----------------------
        $today_partial_query = Inward_outwards::
                join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
                ->join('company', 'company.id', '=', 'inward_outwards.company_id')
                ->join('project', 'project.id', '=', 'inward_outwards.project_id')
                ->leftJoin('department', 'department.id', '=', 'inward_outwards.department_id')
                ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
                ->leftjoin('sender', 'sender.id', '=', 'inward_outwards.sender_id')
                ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
                ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
                ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
                ->where('inward_outwards.type', '=', 'Inwards')
                ->whereDate('inward_outwards.created_at', '=', $today_date)
                ->orderBy('inward_outwards.id', 'DESC')
                ->groupBy('inward_outward_users.inward_outward_id');

            $permission_arr = $this->common_task->getPermissionArr($logged_in_userdata[0]->role, 40); //fetch view permissions of users
                if (in_array(5, $permission_arr)) {
                        $today_inward_data =  $today_partial_query->get(); //show all request
                } elseif (in_array(1, $permission_arr)) {
                        $today_inward_data =  $today_partial_query
                                ->leftJoin('inward_outward_prime_action','inward_outward_prime_action.inward_outward_id','=','inward_outwards.id')
                                ->leftJoin('inward_outward_distrubuted_work','inward_outward_distrubuted_work.inward_outward_prime_action_id','=','inward_outward_prime_action.id')
                                ->groupBy('inward_outward_distrubuted_work.inward_outward_prime_action_id')
                                ->where(function ($query) use ($user_id) {
                            $query->where('inward_outwards.inserted_by', $user_id)
                                    ->orWhere('inward_outwards.requested_by',$user_id )
                                    ->orWhere('inward_outward_users.user_id', $user_id)
                                    ->orWhere(function ($query) use ($user_id) {
                            $query->Where('inward_outwards.prime_employee_id', $user_id )
                                ->Where('inward_outwards.prime_user_status','Accepted');
                        })->orWhere(function ($query) use ($user_id) {
                            $query->Where('inward_outward_distrubuted_work.support_employee_id', $user_id )
                                ->Where('inward_outward_distrubuted_work.emp_status','Accepted');
                        });
                    })->get();
                } else {
                    $today_inward_data = [];
                }
            $today_inward_count = count($today_inward_data);
          
        #----------------------
        $today_outward_partial_query = Inward_outwards::
                    join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
                    ->join('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
                    ->join('company', 'company.id', '=', 'inward_outwards.company_id')
                    ->join('project', 'project.id', '=', 'inward_outwards.project_id')
                    ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
                    ->leftJoin('department', 'department.id', '=', 'inward_outwards.department_id')
                    ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
                    ->join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
                    ->where('inward_outwards.type', '=', 'Outwards')
                    ->whereDate('inward_outwards.created_at', '=', $today_date)
                    ->orderBy('inward_outwards.id', 'DESC')
                    ->groupBy('inward_outward_users.inward_outward_id');

            $permission_arr = $this->common_task->getPermissionArr($logged_in_userdata[0]->role, 40);  //fetch view permissions of users
            
            if (in_array(5, $permission_arr)) {
                    $today_outward_data =  $today_outward_partial_query->get(); //show all  request
            } elseif (in_array(1, $permission_arr)) {
                    $today_outward_data =  $today_outward_partial_query->where(function ($query) use ($user_id)  {
                        $query->where('inward_outwards.inserted_by', $user_id)
                                ->orWhere('inward_outwards.requested_by', $user_id )
                                ->orWhere('inward_outward_users.user_id', $user_id);
                        })->get();
            } else {
                    $today_outward_data = [];
            } 

            $today_outward_count = count($today_outward_data); 
        #----------------------
        $assignee_registry_count =  Inward_outwards::join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
            ->join('company', 'company.id', '=', 'inward_outwards.company_id')
            ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
            ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
            ->join('department', 'department.id', '=', 'inward_outwards.department_id')
            ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
            ->leftjoin('sender', 'sender.id', '=', 'inward_outwards.sender_id')
            ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
            ->join('project', 'project.id', '=', 'inward_outwards.project_id')
            ->where('inward_outward_users.status', '=', 'Processing')
            ->whereDate('inward_outwards.created_at','>=','2020-06-02')
            ->where('inward_outward_users.user_id', '=', $request_data['user_id'])
            ->get()->count();
        #---------------------- 
        $prime_registry_count =  Inward_outwards::join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
            ->join('company', 'company.id', '=', 'inward_outwards.company_id')
            ->join('inward_outward_doc_category', 'inward_outward_doc_category.id', '=', 'inward_outwards.doc_category_id')
            ->leftJoin('inward_outward_doc_sub_category', 'inward_outward_doc_sub_category.id', '=', 'inward_outwards.doc_sub_category_id')
            ->join('department', 'department.id', '=', 'inward_outwards.department_id')
            ->leftjoin('users', 'users.id', '=', 'inward_outwards.requested_by')
            ->leftjoin('users as B', 'B.id', '=', 'inward_outward_users.user_id')
            ->leftjoin('sender', 'sender.id', '=', 'inward_outwards.sender_id')
            ->leftjoin('inward_outward_delivery_mode', 'inward_outward_delivery_mode.id', '=', 'inward_outwards.inward_outward_delivery_mode_id')
            ->join('project', 'project.id', '=', 'inward_outwards.project_id')
            ->where('inward_outwards.prime_user_status','=', 'Assigned')
            ->whereDate('inward_outwards.created_at','>=','2020-06-02')
            ->where('inward_outwards.prime_employee_id', $request_data['user_id'])->get()->count();
        #----------------------
        $support_user_registry_count = Inward_outward_distrubuted_work::join('inward_outward_prime_action','inward_outward_prime_action.id','=','inward_outward_distrubuted_work.inward_outward_prime_action_id')
            ->join('inward_outwards', 'inward_outwards.id','=','inward_outward_prime_action.inward_outward_id') 
            ->join('users','users.id','=','inward_outwards.prime_employee_id')
            ->where('inward_outward_distrubuted_work.emp_status','Assigned')
            ->whereDate('inward_outwards.created_at','>=','2020-06-02')
            ->where('inward_outward_distrubuted_work.support_employee_id','=',$request_data['user_id'])
            ->get()->count();

        $support_prime_user_all_count  = $prime_registry_count+$support_user_registry_count;
        #----------------------
        $rejected_support_emp_request_count = Inward_outward_distrubuted_work::join('inward_outward_prime_action','inward_outward_prime_action.id','=','inward_outward_distrubuted_work.inward_outward_prime_action_id')
                        ->join('inward_outwards', 'inward_outwards.id','=','inward_outward_prime_action.inward_outward_id')               
                        ->join('users','users.id','=','inward_outward_distrubuted_work.support_employee_id')
                        ->where('inward_outward_prime_action.final_status','Pending')
                        ->where('inward_outward_distrubuted_work.emp_status','Rejected') 
                        ->whereDate('inward_outwards.created_at','>=','2020-06-02')
                        ->where('inward_outwards.prime_employee_id',$request_data['user_id'])
                        ->get()->count();
        #----------------------Not In use now
        $submit_entries_count = Inward_outward_distrubuted_work::join('inward_outward_prime_action','inward_outward_prime_action.id','=','inward_outward_distrubuted_work.inward_outward_prime_action_id')
            ->join('inward_outwards', 'inward_outwards.id','=','inward_outward_prime_action.inward_outward_id') 
            ->join('users','users.id','=','inward_outward_distrubuted_work.support_employee_id')
            ->where('inward_outward_distrubuted_work.emp_status','Accepted')
            ->where('inward_outward_distrubuted_work.work_status','Submitted')
            ->where('inward_outwards.prime_employee_id',$request_data['user_id'])
            ->get()->count();
        #---------------------------
        $response_data = [  
            'inward_registry_count' => $inward_count,
            'outward_registry_count' => $outward_count,
            'today_inward_registry_count' => $today_inward_count,
            'today_outward_registry_count' => $today_outward_count,
            'assignee_registry_count' => $assignee_registry_count,
            'support_prime_user_combine_count' => $support_prime_user_all_count,
            'support_prime_user' => ['prime_registry_count' => $prime_registry_count,'support_user_registry_count' => $support_user_registry_count    ],
            'rejected_support_emp_request_count' => $rejected_support_emp_request_count
            //'submit_entries_count' => $submit_entries_count
        ];
        return response()->json(['status' => true,'msg' => 'Get Registry module count!','data' => $response_data]);

    }
    //============================= 03/06/2020 =======================================================
    


    
    //insert outward details
    public function add_outwards(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'inward_outward_title' => 'required',
            'doc_category_id' => 'required',
            'company_id' => 'required',
            'project_id' => 'required',
            'user_id' => 'required',
            'received_date' => 'required',
            //'expected_ans_date' => 'required',
            'description' => 'required',
            'document_file' => 'required'
        ]);

        if ($validator->fails()) {

            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();

        /* $document_file = '';
        if ($request->hasFile('document_file')) {
            $document_file = $request->file('document_file');
            $file_path = $document_file->store('public/document_file');
            if ($file_path) {
                $document_file = $file_path;
            }
        } */

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


        $email_user_ids = [];

        $registry = $request->input('registry');
        $company_id = $request->input('company_id');
        $receive_date = date('Y-m-d', strtotime($request->input('received_date')));
        $rows_count = Inward_outwards::where('received_date', $receive_date)->where('company_id', $company_id)->where('type', 'Outwards')->get()->count();

        $companies_data = Companies::where('id', '=', $company_id)->get();

        $short_name = $companies_data[0]->company_short_name;
        $new_row_count = $rows_count + 1;
        $inward_outward_no = $short_name . "/" . 'OUT' . "/" . date('Y/M/d', strtotime($request->input('received_date'))) . "/" . $new_row_count;

        $project_id = $request->input('project_id');
        $checkDocType = Inward_outward_doc_category::where('id', $request->input('doc_category_id'))->where('is_special', 'Yes')->get();

        if (!empty($request->input('registry'))) {

            $inward_outward_data = Inward_outwards::where('inward_outward_no', '=', $registry)
                ->get();

            $depart_ids = $inward_outward_data[0]->department_id;

            $inward_outward_id = $inward_outward_data[0]->parent_inward_outward_no;

            $outward_arr = [
                'inward_outward_title' => $request->input('inward_outward_title'),
                'inward_outward_no' => $inward_outward_no,
                'parent_inward_outward_no' => $inward_outward_id,
                'ref_outward_number' => $request->input('ref_outward_number'),
                'description' => $request->input('description'),
                'document_file' => !empty($document_file) ? $document_file : NULL,
                'doc_mark' => !empty($request->input('is_important')) ? 'Pending' : 'None',
                'type' => 'Outwards',
                'doc_category_id' => $request->input('doc_category_id'),
                'doc_sub_category_id' => $request->input('doc_sub_category_id'),
                'company_id' => $request->input('company_id'),
                'project_id' => $project_id,
                'other_project_details' => $request->input('other_project'),
                'department_id' => $depart_ids,
                'received_date' => $request->input('received_date'),
                //'expected_ans_date' => $request->input('expected_ans_date'),
                'is_reply' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'created_ip' => $request->ip(),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => $request_data['user_id']
            ];
            if ($request->input('ans_expected') == 'Yes') {
                $outward_arr['expected_ans_date'] = date('Y-m-d', strtotime($request->input('expected_ans_date')));
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
                'updated_by' => $request_data['user_id']
            ];
             Inward_outwards::where('id', $last_inward_registry->id)->update($update_parent_inward_arr);
            }
            
            
            $user_data = Inward_outward_users::where('inward_outward_id', '=', $inward_outward_id)
                ->pluck('user_id')->toArray();

            if ($request->input('outward_user_id')) {
                $user_id_arr = explode(',', $request->input('outward_user_id'));

                $user_data = array_merge($user_data, $user_id_arr);
            }

            if ($request->input('ans_expected') == 'Yes' || !$checkDocType->isEmpty()) {

                $user_data = $this->common_task->setSuperUserId($user_data, 1);
            }
            foreach ($user_data as $key => $user) {

                $outward_users_arr = [
                    'inward_outward_id' => $new_id,
                    'user_id' => $user,
                    'status' => $key == 0 ? 'Processing' : 'Pending',
                    'expected_ans_date' => $request->input('expected_ans_date'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_ip' => $request->ip(),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                ];

                Inward_outward_users::insert($outward_users_arr);
                array_push($email_user_ids, $user);
            }

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
                //'ref_outward_number' => $request->input('ref_outward_number'),
                'description' => $request->input('description'),
                'document_file' => !empty($document_file) ? $document_file : NULL,
                'doc_mark' => !empty($request->input('is_important')) ? 'Pending' : 'None',
                'type' => 'Outwards',
                'doc_category_id' => $request->input('doc_category_id'),
                'doc_sub_category_id' => $request->input('doc_sub_category_id'),
                'company_id' => $request->input('company_id'),
                'project_id' => $project_id,
                'other_project_details' => $request->input('other_project'),
                'department_id' => $request->input('department_id'),
                'received_date' => $request->input('received_date'),
                //'expected_ans_date' => $request->input('expected_ans_date'),
                'is_reply' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'created_ip' => $request->ip(),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => $request_data['user_id']
            ];
            if ($request->input('ans_expected') == 'Yes') {
                $outward_arr['expected_ans_date'] = date('Y-m-d', strtotime($request->input('expected_ans_date')));
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
                'updated_by' => $request_data['user_id']
            ];

            Inward_outwards::where('id', $new_id)->update($new_outward_arr);
            $login_user_arr = [
                'inward_outward_id' => $new_id,
                'user_id' => $request_data['user_id'],
                'status' => 'Processing',
                'expected_ans_date' => $request->input('expected_ans_date'),
                'created_at' => date('Y-m-d H:i:s'),
                'created_ip' => $request->ip(),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
            ];

            Inward_outward_users::insert($login_user_arr);

            $outward_user_ids = $request->input('outward_user_id');

            $company_id = $request->input('company_id');
            $user_id_arr = explode(",", $outward_user_ids);   //make array using explode() func..

            if ($request->input('ans_expected') == 'Yes' || !$checkDocType->isEmpty()) {

                $user_id_arr = $this->common_task->setSuperUserId($user_id_arr, 0);
            }

            foreach ($user_id_arr as $user_id) {  //give for loop to arry
                $outward_user_arr = [
                    'inward_outward_id' => $new_id,
                    'user_id' => $user_id,
                    'status' => 'Pending',
                    'expected_ans_date' => date('Y-m-d', strtotime($request->input('expected_ans_date'))),
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_ip' => $request->ip(),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                ];

                Inward_outward_users::insert($outward_user_arr);
                array_push($email_user_ids, $user_id);
            }

            $login_user_views_arr = [
                'user_id' => $request_data['user_id'],
                'inward_outward_id' => $new_id,
                'is_view' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'created_ip' => $request->ip(),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
            ];

            Inward_outward_views::insert($login_user_views_arr);

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
        }

        //insert details inward_outwards_chat table

        $chat_arr = [
            'inward_outward_id' => $new_id,
            'from_user_id' => $request_data['user_id'],
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
        if ($request->input('is_important') == "Pending") {
            $assistant_ids = user::where('status', 'Enabled')->where('role', config('constants.ASSISTANT'))->pluck('id')->toArray();
            $this->notification_task->markAsImpoNotify($assistant_ids, $inward_outward_no);
        }
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

        return response()->json(['status' => true, 'msg' => 'New outward details successfully added!', 'data' => []]);
    }

    //get_registry_category
    public function get_registry_category(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $logged_in_userdata = User::where('id', $request_data['user_id'])->get();
        $inward_count = Inward_outwards::select('inward_outwards.*')
            ->join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
            ->where(function ($query) use ($request_data, $logged_in_userdata) {
                //if ($logged_in_userdata[0]->role != config('constants.SuperUser')) {
                $query->where('inward_outward_users.user_id', '=', $request_data['user_id']);
                //}
            })
            //->where('inward_outwards.is_reply', '=', 0)
            ->where('inward_outwards.type', '=', 'Inwards')
            ->get()
            ->count();

        $outward_count = Inward_outwards::select('inward_outwards.*')
            ->join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
            ->where(function ($query) use ($request_data, $logged_in_userdata) {
                //if ($logged_in_userdata[0]->role != config('constants.SuperUser')) {
                $query->where('inward_outward_users.user_id', '=', $request_data['user_id']);
                //}
            })
            //->where('inward_outwards.is_reply', '=', 0)
            ->where('inward_outwards.type', '=', 'Outwards')
            ->get()
            ->count();

        $unread_registry_count = Inward_outwards::select('inward_outwards.*')
            ->join('inward_outward_views', 'inward_outward_views.inward_outward_id', '=', 'inward_outwards.id')
            ->where('inward_outward_views.user_id', '=', $request_data['user_id'])
            ->where('inward_outward_views.is_view', 0)
            ->groupBy('inward_outwards.parent_inward_outward_no')
            ->get()
            ->count();


        $unread_message_count = Inward_outward_message_view::select('inward_outward_message_view.*')
            ->where('user_id', $request_data['user_id'])
            ->where('is_read', 0)
            ->groupBy('inward_outward_id')
            ->get()
            ->count();

        $loggedin_user_data = User::where('id', $request_data['user_id'])->get(['users.id', 'users.name', 'users.role']);

        if ($loggedin_user_data[0]->role  == Config('constants.ASSISTANT')) {
            $pending_registry_docs = Inward_outwards::where('doc_mark', 'Pending')->get()->count();
        }else{
            $pending_registry_docs = 0;
        }
        

        $approved_inwards_docs = Inward_outwards::join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
            ->where(function ($query) use ($request_data) {
                //if (Auth::user()->role != config('constants.SuperUser')) {
                $query->where('inward_outward_users.user_id', '=', $request_data['user_id']);
                //}
            })
            ->where('inward_outwards.type', 'Inwards')
            ->where('inward_outwards.doc_mark', 'Approved')
            //->groupBy('inward_outward_users.user_id')
            ->get()->count();

        $approved_outwards_docs = Inward_outwards::join('inward_outward_users', 'inward_outward_users.inward_outward_id', '=', 'inward_outwards.id')
            ->where(function ($query) use ($request_data) {
                //if (Auth::user()->role != config('constants.SuperUser')) {
                $query->where('inward_outward_users.user_id', '=', $request_data['user_id']);
                //}
            })
            ->where('inward_outwards.type', 'Outwards')
            ->where('inward_outwards.doc_mark', 'Approved')
            //->groupBy('inward_outward_users.user_id')
            ->get()->count();

        return response()->json(
            [
                'status' => true,
                'msg' => 'Get Registry category count!',
                'data' => [
                    'inward_count' => $inward_count,
                    'outward_count' => $outward_count,
                    'unread_registry_count' => $unread_registry_count,
                    'unread_message_count' => $unread_message_count,
                    'approved_inwards_docs' => $approved_inwards_docs,
                    'approved_outwards_docs' => $approved_outwards_docs,
                    'pending_registry_docs' =>  $pending_registry_docs
                ]
            ]
        );
    }

    //view_registry 
    public function view_registry(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'registry_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();

        $user_views_arr = [
            'is_view' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
        ];
        Inward_outward_views::where('user_id', $request_data['user_id'])
            ->where('inward_outward_id', $request_data['registry_id'])
            ->update($user_views_arr);

        return response()->json(
            [
                'status' => true,
                'msg' => 'User Can see details!',
                'data' => []
            ]
        );
    }

    //pass_registry 
    public function pass_registry(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'registry_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();


        $inward_user_arr = [
            'status' => 'Completed',
            'created_at' => date('Y-m-d H:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
        ];

        Inward_outward_users::where('user_id', $request_data['user_id'])
            ->where('inward_outward_id', $request_data['registry_id'])
            ->update($inward_user_arr);

        $first_user_list = Inward_outward_users::where('inward_outward_id', $request_data['registry_id'])
            ->where('status', 'Pending')
            ->limit(1)
            ->get()
            ->toArray();

        if (empty($first_user_list)) {

            return response()->json(
                [
                    'status' => true,
                    'msg' => 'Passed registry Successfully!',
                    'data' => []
                ]
            );
        }

        $first_user_arr = [
            'status' => 'Processing',
            'created_at' => date('Y-m-d H:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
        ];

        Inward_outward_users::where('user_id', $first_user_list[0]['user_id'])
            ->where('inward_outward_id', $request_data['registry_id'])
            ->update($first_user_arr);

        return response()->json(
            [
                'status' => true,
                'msg' => 'Passed registry Successfully!',
                'data' => []
            ]
        );
    }

    public function get_unread_registry(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();

        $inward_outward_data = Inward_outwards::select('inward_outwards.*')
            ->join('inward_outward_views', 'inward_outward_views.inward_outward_id', '=', 'inward_outwards.id')
            ->where('inward_outward_views.user_id', '=', $request_data['user_id'])
            ->where('inward_outward_views.is_view', 0)
            ->groupBy('inward_outwards.parent_inward_outward_no')
            ->get();

        foreach ($inward_outward_data as $key => $value) {

            if (!empty($value->document_file))
                $inward_outward_data[$key]->document_file = asset('storage/' . str_replace('public/', '', $value->document_file));
        }

        if ($inward_outward_data->count() == 0) {
            return response()->json(
                [
                    'status' => false,
                    'msg' => config('errors.no_record.msg'),
                    'data' => [],
                    'error' => config('errors.no_record.code')
                ]
            );
        }

        $inward_outward_list = [];

        foreach ($inward_outward_data as $key => $main_data) {



            $thread_data = Inward_outwards::select('inward_outwards.*', 'inward_outward_views.is_view')
                ->join('inward_outward_views', 'inward_outward_views.inward_outward_id', '=', 'inward_outwards.id')
                ->where('inward_outwards.parent_inward_outward_no', '=', $main_data->parent_inward_outward_no)
                ->where('inward_outward_views.user_id', '=', $request_data['user_id'])
                ->where('inward_outward_views.is_view', 0)
                //->groupBy('inward_outwards.parent_inward_outward_no','inward_outwards.id'); 
                //->groupBy('inward_outwards.parent_inward_outward_no',$inward->parent_inward_outward_no) 
                ->get();

            foreach ($thread_data as $thread__key => $value) {

                $users_data = Inward_outward_users::select('inward_outward_users.user_id', 'inward_outward_users.status', 'users.name')
                    ->join('users', 'users.id', '=', 'inward_outward_users.user_id')
                    ->where('inward_outward_users.inward_outward_id', '=', $value->id)->get();


                if (!empty($value->document_file)) {

                    $thread_data[$thread__key]->document_file = asset('storage/' . str_replace('public/', '', $value->document_file));
                }
                $thread_data[$thread__key]->users_list = $users_data;
            }

            $inward_outward_list[$key]['main_data'] = $main_data;
            $inward_outward_list[$key]['thread_data'] = $thread_data;
        }

        return response()->json(
            [
                'status' => true,
                'msg' => 'Get unread registry details!',
                'data' => $inward_outward_list
            ]
        );
    }

    //mark_read_registry 
    public function mark_read_registry(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'registry_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();

        //get ids which are still unread
        $inward_outward_ids = Inward_outwards::join('inward_outward_views', 'inward_outward_views.inward_outward_id', '=', 'inward_outwards.id')
            ->where('inward_outward_views.user_id', $request_data['user_id'])
            ->where('inward_outwards.id', $request_data['registry_id'])
            ->pluck('inward_outwards.id')->toArray();


        $user_read_arr = [
            'is_view' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
        ];

        Inward_outward_views::where('user_id', $request_data['user_id'])
            ->whereIn('inward_outward_id', $inward_outward_ids)
            ->update($user_read_arr);

        return response()->json(
            [
                'status' => true,
                'msg' => 'This registry marked as read!',
                'data' => []
            ]
        );
    }

    //chat_send_message 
    public function chat_send_message(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'registry_id' => 'required',
            'message_type' => 'required'
        ]);

        //dd($request->input('message'));
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();

        /*  $document_file = '';
        if ($request->hasFile('document_file')) {
            $document_file = $request->file('document_file');
            $file_path = $document_file->store('public/document_file');
            if ($file_path) {
                $document_file = $file_path;
            }
        } */

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
        $registry_data = Inward_outwards::where('id', $request_data['registry_id'])->get();
        if (!empty($request->input('message'))) {

            $chat_arr = [
                'inward_outward_id' => $request_data['registry_id'],
                'from_user_id' => $request_data['user_id'],
                'message' => $request->input('message'),
                'message_type' => $request_data['message_type'],
                'document_name' => !empty($document_file) ? $document_file : NULL,
                'created_at' => date('Y-m-d H:i:s'),
                'created_ip' => $request->ip(),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
            ];
        } else {

            $message_type = Inward_outwards::where('parent_inward_outward_no', $request_data['registry_id'])->where('is_reply', 0)->value('type');

            $chat_arr = [
                'inward_outward_id' => $request_data['registry_id'],
                'from_user_id' => $request_data['user_id'],
                'message' => $message_type,
                'message_type' => $request_data['message_type'],
                'document_name' => !empty($document_file) ? $document_file : NULL,
                'created_at' => date('Y-m-d H:i:s'),
                'created_ip' => $request->ip(),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
            ];
        }


        $message_id = Inward_outward_chat::insertGetId($chat_arr);

        $user_message_arr = [
            'inward_outward_id' => $request_data['registry_id'],
            'message_id' => $message_id,
            'user_id' => $request_data['user_id'],
            'is_read' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip()
        ];

        Inward_outward_message_view::insert($user_message_arr);

        $user_ids = Inward_outward_users::where('inward_outward_id', '=', $request_data['registry_id'])
            ->where('user_id', '!=', $request_data['user_id'])
            ->pluck('user_id')->toArray();


        foreach ($user_ids as $id) {

            $other_user_message_arr = [
                'inward_outward_id' => $request_data['registry_id'],
                'message_id' => $message_id,
                'user_id' => $id,
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'created_ip' => $request->ip(),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip()
            ];
            Inward_outward_message_view::insert($other_user_message_arr);
        }

        $this->notification_task->registryMessageNotify($user_ids, $request->input('message'), $registry_data[0]->inward_outward_title);

        return response()->json(
            ['status' => true, 'msg' => 'Message Sent!', 'data' => []]
        );
    }

    //chat_messages 
    public function chat_messages(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'registry_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();

        $messages = Inward_outward_chat::select('inward_outward_chat.*', 'users.name')
            ->join('users', 'inward_outward_chat.from_user_id', '=', 'users.id')
            ->leftJoin('inward_outwards', 'inward_outwards.id', '=', 'inward_outward_chat.inward_outward_id')
            ->where('inward_outwards.parent_inward_outward_no', '=', $request_data['registry_id'])
            ->orWhere('inward_outward_chat.inward_outward_id', '=', $request_data['registry_id'])
            ->get();

        foreach ($messages as $key => $value) {

            if (!empty($value->document_name))
                $messages[$key]->document_name = asset('storage/' . str_replace('public/', '', $value->document_name));
        }
        if ($messages->count() == 0) {
            return response()->json(
                [
                    'status' => false,
                    'msg' => config('errors.no_record.msg'),
                    'data' => [],
                    'error' => config('errors.no_record.code')
                ]
            );
        }

        return response()->json(
            [
                'status' => true,
                'msg' => 'Messages Records!',
                'data' => $messages
            ]
        );
    }

    //unread_messages 
    public function get_unread_messages(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();

        $unread_messages = inward_outward_message_view::select('inward_outwards.inward_outward_title', 'inward_outward_message_view.*')
            ->join('inward_outwards', 'inward_outward_message_view.inward_outward_id', '=', 'inward_outwards.id')
            ->where('user_id', $request_data['user_id'])
            ->where('is_read', 0)
            ->groupBy('inward_outward_message_view.inward_outward_id')
            ->get();


        if ($unread_messages->count() == 0) {
            return response()->json(
                [
                    'status' => false,
                    'msg' => config('errors.no_record.msg'),
                    'data' => [],
                    'error' => config('errors.no_record.code')
                ]
            );
        }
        return response()->json(
            [
                'status' => true,
                'msg' => 'Get unread messages !',
                'data' => $unread_messages
            ]
        );
    }

    //mark as read messages
    public function mark_read_messages(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'registry_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();

        $user_read_arr = [
            'is_read' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
        ];

        inward_outward_message_view::where('user_id', $request_data['user_id'])
            ->where('inward_outward_id', $request_data['registry_id'])
            ->update($user_read_arr);

        return response()->json(
            [
                'status' => true,
                'msg' => 'This messages marked as read!',
                'data' => []
            ]
        );
    }

    public function department_user_with_registry(Request $request)
    {
        $department_ids = $request->input('department_ids');
        $registry_id = $request->input('registry_id');
        $department_id_arr = explode(",", $department_ids);
        //get list of users of particular registry
        $registry_user_list = Inward_outward_users::where('inward_outward_users.inward_outward_id', $registry_id)
            ->join('users', 'users.id', '=', 'inward_outward_users.user_id')
            ->get(['users.id', 'users.name']);

        if ($registry_user_list->count() > 0) {

            $user_list = User::join('employee', 'employee.user_id', '=', 'users.id')
                ->where('users.status', 'Enabled')
                ->where('users.id', '!=', $request->input('user_id'))
                ->whereNotIn('users.id', $registry_user_list->pluck('id')->toArray())
                ->whereIn('employee.department_id', $department_id_arr)
                ->get(['users.id', 'users.name', 'users.profile_image']);
        } else {
            $user_list = User::join('employee', 'employee.user_id', '=', 'users.id')
                ->where('users.status', 'Enabled')
                ->where('users.id', '!=', $request->input('user_id'))
                ->whereIn('employee.department_id', $department_id_arr)
                ->get(['users.id', 'users.name', 'users.profile_image']);
        }

        foreach ($user_list as $key => $value) {

            if ($value->profile_image) {
                $user_list[$key]->profile_image = asset('storage/' . str_replace('public/', '', $value->profile_image));
            } else {
                $user_list[$key]->profile_image = "";
            }
        }

        //$department_id_arr = explode(",", $department_ids);
        return response()->json(['status' => true, 'msg' => 'record found', 'data' => $user_list]);
    }

    public function get_registry_old_user_list(Request $request)
    {
        $registry_id = $request->input('registry_id');

        //get list of users of particular registry
        $registry_user_list = Inward_outward_users::where('inward_outward_users.inward_outward_id', $registry_id)
            ->join('users', 'users.id', '=', 'inward_outward_users.user_id')
            ->get(['users.id', 'users.name'])->pluck('name')->toArray();
        return response()->json(['status' => true, 'msg' => 'record found', 'data' => implode(', ', $registry_user_list)]);
    }

    public function get_doc_sub_category(Request $request)
    {
        $document_sub_cat_data = \App\Inward_outward_doc_sub_category::select('sub_category_name', 'id')->where('status', 'Enabled')->where(['category_id' => $request->input('doc_category_id')])->get();

        if ($document_sub_cat_data->count() == 0) {
            return response()->json(['status' => false, 'msg' => 'record']);
        }
        return response()->json(['status' => true, 'msg' => 'record found', 'data' => $document_sub_cat_data]);
    }
}
