<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Config;
use App\Companies;
use App\Clients;
use App\Project_sites;
use App\Projects;
use App\Lib\Permissions;
use App\Common_query;
use App\Lib\NotificationTask;

class ProjectSitesController extends Controller {

    public $data;
    private $notification_task;

    public function __construct() {
        $this->notification_task = new NotificationTask();
        $this->data['module_title'] = "Project Sites";
        $this->data['module_link'] = "admin.project_site";
        $this->module_id = 53;
    }

    public function index() {
        $this->data['page_title'] = "Project Sites";
        $this->data['view_special_permission'] = Permissions::checkSpecialPermission($this->module_id);
        return view('admin.project_site.index', $this->data);
    }

    public function add_sites() {
        $view_special_permission = Permissions::checkSpecialPermission($this->module_id);
        if (!$view_special_permission) {
            return redirect()->route('admin.dashboard')->with('error', 'Access denied. You do not have permission to access this module.');
        }
        $this->data['page_title'] = 'Add Project Site';

        $this->data['companies'] = Companies::orderBy('company_name')->get(['id', 'company_name']);
        $this->data['clients'] = Clients::where('status', 'Enabled')->get(['client_name', 'id']);
        $this->data['projects'] = Projects::where('status', 'Enabled')->get(['project_name', 'id']);

//           $this->data['companies'] = Companies::pluck('company_name','id');
        return view('admin.project_site.add_sites', $this->data);
    }

    public function get_list_datatable_ajax() {
        $table = "project_sites";
        $datatable_fields = array('company.company_name', 'clients.client_name', 'project.project_name', 'project_sites.site_name', 'project_sites.site_address', 'project_sites.site_details', 'project_sites.status');
        $conditions_array = ['project_sites.is_approved' => 1];
        $getfiled = array('project_sites.id', 'company.company_name', 'clients.client_name', 'clients.location', 'project.project_name', 'project_sites.site_name', 'project_sites.site_address', 'project_sites.site_details', 'project_sites.status');
        $request = Input::all();

        $join_str[0]['table'] = 'company';
        $join_str[0]['join_table_id'] = 'company.id';
        $join_str[0]['from_table_id'] = 'project_sites.company_id';
        $join_str[0]['join_type'] = '';

        $join_str[1]['table'] = 'clients';
        $join_str[1]['join_table_id'] = 'clients.id';
        $join_str[1]['from_table_id'] = 'project_sites.client_id';
        $join_str[1]['join_type'] = '';

        $join_str[2]['table'] = 'project';
        $join_str[2]['join_table_id'] = 'project.id';
        $join_str[2]['from_table_id'] = 'project_sites.project_id';
        $join_str[2]['join_type'] = '';

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);
        die();
    }

    public function project_site__status($id, $status) {

        if (Project_sites::where('id', $id)->update(['status' => $status])) {
            return redirect()->route('admin.project_site')->with('success', 'Status successfully updated.');
        }
        return redirect()->route('admin.project_site')->with('error', 'Error during operation. Try again!');
    }

    public function companies_clients(Request $request) {


        $company_id = $request->company_id;


        $clients = Clients::select('clients.*')
                ->where('clients.status', 'Enabled')->orderBy('client_name')
                ->where(function($query) use($company_id) {
                    $query->where('clients.company_id', $company_id);
                })
                ->get();

        return response()->json($clients);
    }

    public function clients_projects(Request $request) {


        $client_id = $request->client_id;


        $projects = Projects::select('project.*')
                ->where('project.status', 'Enabled')->orderBy('project_name')
                ->where(function($query) use($client_id) {
                    $query->where('project.client_id', $client_id);
                })
                ->get();

        return response()->json($projects);
    }

    public function insert_project_site(Request $request) {

        $validator_normal = Validator::make($request->all(), [
                    'company_id' => 'required',
                    'client_id' => 'required',
                    'project_id' => 'required',
                    'site_name' => 'required',
                    'site_address' => 'required',
                    'site_details' => 'required',
        ]);


        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_sites')->with('error', 'Please follow validation rules.');
        }


        $project_sites_arr = [
            'user_id' => Auth::user()->id,
            'company_id' => $request->input('company_id'),
            'client_id' => $request->input('client_id'),
            'project_id' => $request->input('project_id'),
            'site_name' => $request->input('site_name'),
            'site_address' => $request->input('site_address'),
            'site_details' => $request->input('site_details'),
            // 'status' => 'Disabled',
            // 'is_approved' => 0,
            'created_at' => date('Y-m-d h:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id
        ];
        if (Auth::user()->role != config('constants.SuperUser')) {
            $project_sites_arr['status'] = 'Disabled';
            $project_sites_arr['is_approved'] = 0;
        } else {
            $project_sites_arr['status'] = 'Enabled';
            $project_sites_arr['is_approved'] = 1;
        }

        Project_sites::insert($project_sites_arr);
        $module = 'Project sites';
        $this->notification_task->entryApprovalNotify($module);

        return redirect()->route('admin.project_site')->with('success', 'New Project site Added successfully.');
    }

    public function edit_project_sites($id) {
        $this->data['page_title'] = "Edit Project Sites";
        $view_special_permission = Permissions::checkSpecialPermission($this->module_id);
        if (!$view_special_permission) {
            return redirect()->route('admin.dashboard')->with('error', 'Access denied. You do not have permission to access this module.');
        }
        $this->data['project_site_list'] = $project_site = Project_sites::where('id', $id)->get();

        if ($this->data['project_site_list']->count() == 0) {
            return redirect()->route('admin.project_site')->with('error', 'Error Occurred. Try Again!');
        }

        $this->data['companies'] = $companies = Companies::orderBy('company_name')->get(['id', 'company_name']);


        $this->data['clients'] = $clients = Clients::where('company_id', '=', $project_site[0]->company_id)->where('status', 'Enabled')->orderBy('client_name')->get(['client_name', 'id', 'location']);

        $this->data['projects'] = $projects = Projects::where('client_id', '=', $project_site[0]->client_id)->where('status', 'Enabled')->get(['project_name', 'id']);



        return view('admin.project_site.edit_sites', $this->data);
    }

    public function update_project_sites(Request $request) {

        $validator_normal = Validator::make($request->all(), [
                    'company_id' => 'required',
                    'client_id' => 'required',
                    'project_id' => 'required',
                    'site_name' => 'required',
                    'site_address' => 'required',
                    'site_details' => 'required',
        ]);


        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_sites')->with('error', 'Please follow validation rules.');
        }

        $project_site_id = $request->input('id');

        $project_sites_arr = [
            'user_id' => Auth::user()->id,
            'company_id' => $request->input('company_id'),
            'client_id' => $request->input('client_id'),
            'project_id' => $request->input('project_id'),
            'site_name' => $request->input('site_name'),
            'site_address' => $request->input('site_address'),
            'site_details' => $request->input('site_details'),
            //'status' => 'Enabled',
            'created_at' => date('Y-m-d h:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id
        ];



        Project_sites::where('id', $project_site_id)->update($project_sites_arr);

        return redirect()->route('admin.project_site')->with('success', ' Project site Updated !!.');
    }

    public function checkProjectSiteName(Request $request) {
        $site_name = $request->input('site_name');
        $project_id = $request->input('project_id');

        $check_result = Project_sites::where('site_name', $site_name)->where('project_id', $project_id)->get()->count();
        if ($check_result > 0) {
            echo 'false';
        } else {
            echo 'true';
        }
        die();
    }
    
    public function checkEditProjectSiteName(Request $request) {
        $site_name = $request->input('site_name');
        $project_id = $request->input('project_id');

        $check_result = Project_sites::where('site_name', $site_name)
                ->where('project_id', $project_id)
                ->where('id','!=',$request->input('site_id'))
                ->get()->count();
        if ($check_result > 0) {
            echo 'false';
        } else {
            echo 'true';
        }
        die();
    }

}
