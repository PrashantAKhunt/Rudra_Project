<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use App\Common_query;
use App\Projects;
use App\Companies;
use App\Clients;
use App\Tender_boq_bidder;
use App\Site_manage_boq;
use App\User;
use App\ProjectManager;
use App\ProjectUpdateApproveRequest;
use App\Lib\NotificationTask;
use Illuminate\Support\Facades\Validator;
use App\Lib\OpenFire;
use App\Lib\Permissions;

class ProjectController extends Controller {

    public $data;
    private $module_id;
    private $notification_task;

    //private $openfire_obj;
    public function __construct() {
        $this->notification_task = new NotificationTask();
        $this->data['module_title'] = "Projects";
        $this->data['module_link'] = "admin.projects";
        $this->module_id = 36;
        //$this->openfire_obj=new OpenFire();
    }

    public function index() {   //chnage
        $this->data['page_title'] = "Projects";
        $permission = Permissions::checkPermission($this->module_id, 5);
        if (!$permission) {
            return redirect()->route('admin.dashboard')->with('error', 'Access denied. You do not have permission to access this module.');
        }
        $this->data['view_special_permission'] = Permissions::checkSpecialPermission($this->module_id);
        return view('admin.project.index', $this->data);
    }

    public function project_update_request() {   //chnage
        $this->data['page_title'] = "Project update request";
        $permission = Permissions::checkPermission($this->module_id, 5);
        if (!$permission) {
            return redirect()->route('admin.dashboard')->with('error', 'Access denied. You do not have permission to access this module.');
        }
        $this->data['view_special_permission'] = Permissions::checkSpecialPermission($this->module_id);
        return view('admin.project.project_update_request', $this->data);
    }

    public function get_project_list() {   //chnage
        $datatable_fields = array('project.project_name', 'company.company_name', 'clients.client_name', 'project.project_type', 'project.status', 'project.created_at');
        $request = Input::all();
        $conditions_array = ['project.is_approved' => 1];

        $getfiled = array('project.id', 'project.project_name', 'project.details', 'company.company_name', 'project.project_type', 'project.status', 'clients.client_name', 'project.created_at');
        $table = "project";
        $join_str = [];
        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'company';
        $join_str[0]['join_table_id'] = 'project.company_id';
        $join_str[0]['from_table_id'] = 'company.id';

        $join_str[1]['join_type'] = 'left';
        $join_str[1]['table'] = 'clients';
        $join_str[1]['join_table_id'] = 'clients.id';
        $join_str[1]['from_table_id'] = 'project.client_id';

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);

        die();
    }
    // start new table project_update_approve_request
    // new tbl project_update_approve_request new tbl
     public function get_project_list_last() {   //chnage
        $datatable_fields = array('id','project_update_approve_request.project_name', 'company.company_name', 'clients.client_name', 'project_update_approve_request.created_at', 'project_update_approve_request.project_id');
        $request = Input::all();
        $conditions_array = ['project_update_approve_request.is_approved' => 0];
        
        $getfiled = array('project_update_approve_request.id', 'project_update_approve_request.project_name', 'company.company_name', 'clients.client_name', 'project_update_approve_request.created_at');
        $table = "project_update_approve_request";
        $join_str = [];
        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'company';
        $join_str[0]['join_table_id'] = 'project_update_approve_request.company_id';
        $join_str[0]['from_table_id'] = 'company.id';
        
        $join_str[1]['join_type'] = 'left';
        $join_str[1]['table'] = 'clients';
        $join_str[1]['join_table_id'] = 'clients.id';
        $join_str[1]['from_table_id'] = 'project_update_approve_request.client_id';
        
        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);
           
        die();
    }
    // new tbl project_update_approve_request new tbl
    // end new table for approve project_update_approve_request

    public function change_project_status($id, $status) {
        $permission = Permissions::checkPermission($this->module_id, 2);
        if (!$permission) {
            return redirect()->route('admin.dashboard')->with('error', 'Access denied. You do not have permission to access this module.');
        }
        if (Projects::where('id', $id)->update(['status' => $status])) {
            return redirect()->route('admin.projects')->with('success', 'Status successfully updated.');
        }
        return redirect()->route('admin.projects')->with('error', 'Error during operation. Try again!');
    }

    //09/09/2020
    public function change_project_type($id, $status) {
        $permission = Permissions::checkPermission($this->module_id, 2);
        if (!$permission) {
            return redirect()->route('admin.dashboard')->with('error', 'Access denied. You do not have permission to access this module.');
        }

        if (Projects::where('id', $id)->update(['project_type' => $status])) {
            return redirect()->route('admin.projects')->with('success', 'Project Status successfully updated.');
        }
        return redirect()->route('admin.projects')->with('error', 'Error during operation. Try again!');
    }

    public function add_project() {
        $permission = Permissions::checkPermission($this->module_id, 3);
        if (!$permission) {
            return redirect()->route('admin.dashboard')->with('error', 'Access denied. You do not have permission to access this module.');
        }
        $view_special_permission = Permissions::checkSpecialPermission($this->module_id);
        if (!$view_special_permission) {
            return redirect()->route('admin.dashboard')->with('error', 'Access denied. You do not have permission to access this module.');
        }
        $this->data['page_title'] = 'Add project';
        $this->data['companies'] = Companies::orderBy('company_name', 'asc')->pluck('company_name', 'id');
        $this->data['users'] = User::whereStatus('Enabled')->orderBy('name')->pluck('name', 'id');
        // echo "<pre>";
        // print_r($this->data);die;
        return view('admin.project.add_project', $this->data);
    }

    public function insert_project(Request $request) {   //chnage
        $validator_normal = Validator::make($request->all(), [
                    'project_name' => 'required',
                    'details' => 'required',
                    'company_id' => 'required',
                    'client_id' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_project')->with('error', 'Please follow validation rules.');
        }

        // dd($request->all());
        $projectModel = new Projects();
        $projectModel->user_id = Auth::user()->id;
        $projectModel->project_name = $request->input('project_name');
        $projectModel->details = $request->input('details');
        $projectModel->company_id = $request->input('company_id');
        $projectModel->client_id = $request->input('client_id');
        $projectModel->project_location = $request->input('project_location');
        if (Auth::user()->role != config('constants.SuperUser')) {
            $projectModel->status = 'Disabled';
            $projectModel->is_approved = 0;
        } else {
            $projectModel->status = 'Enabled';
            $projectModel->is_approved = 1;
        }
        $projectModel->created_at = date('Y-m-d h:i:s');
        $projectModel->created_ip = $request->ip();
        $projectModel->updated_at = date('Y-m-d h:i:s');
        $projectModel->updated_ip = $request->ip();

        if ($projectModel->save()) {

            $module = 'Project';
            $this->notification_task->entryApprovalNotify($module);

            $client = Clients::whereId($request->input('client_id'))->first();
            if ($client->tender_id > 0) {
                $items = Tender_boq_bidder::where('tender_id', $client->tender_id)->where('bidder_id', $request->input('company_id'))->where('own_company', 1)->get()->toArray();
                if (count($items)) {
                    $item_arr = [];
                    $item_id = 0;
                    foreach ($items as $key => $value) {
                        $item_arr['company_id'] = $request->input('company_id');
                        $item_arr['project_id'] = $projectModel->id;
                        $item_arr['tender_id'] = $client->tender_id;
                        $item_arr['item_no'] = $value['item_no'];
                        $item_arr['item_description'] = $value['item_of_work'];
                        $item_arr['UOM'] = $value['unit'];
                        $item_arr['quantity'] = $value['qty'];
                        $item_arr['rate'] = $value['estimated_rates'];
                        $item_arr['amount'] = $value['total_amount'];
                        $item_arr['parent_boq'] = $item_id;

                        if ($key == 0) {
                            $item_id = Site_manage_boq::insertGetId($item_arr);
                            Site_manage_boq::whereId($item_id)->update(['parent_boq' => $item_id]);
                        } else {
                            Site_manage_boq::insert($item_arr);
                        }
                    }
                }
            }

            //Project Manager
            $manager_arr = [
                'project_id' => $projectModel->id,
                'user_id' => $request->get('project_manager_id'),
                'is_manager' => 1,
                'created_ip' => $request->ip(),
                'updated_ip' => $request->ip(),
            ];
            ProjectManager::insert($manager_arr);
            //Expense Manager
            $expense_manager_arr = [
                'project_id' => $projectModel->id,
                'user_id' => $request->get('expense_manager_id'),
                'is_manager' => 2,
                'created_ip' => $request->ip(),
                'updated_ip' => $request->ip(),
            ];
            ProjectManager::insert($expense_manager_arr);
            $emp_arr = [];

            foreach ($request->get('project_employee_id') as $key_emp => $value_emp) {
                $emp_arr[$key_emp]['project_id'] = $projectModel->id;
                $emp_arr[$key_emp]['user_id'] = $value_emp;
                $emp_arr[$key_emp]['created_ip'] = $request->ip();
                $emp_arr[$key_emp]['updated_ip'] = $request->ip();
            }
            ProjectManager::insert($emp_arr);


            return redirect()->route('admin.projects')->with('success', 'New project added successfully.');
        } else {
            return redirect()->route('admin.add_project')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function edit_project($id) {
        $permission = Permissions::checkPermission($this->module_id, 2);
        if (!$permission) {
            return redirect()->route('admin.dashboard')->with('error', 'Access denied. You do not have permission to access this module.');
        }
        $view_special_permission = Permissions::checkSpecialPermission($this->module_id);
        if (!$view_special_permission) {
            return redirect()->route('admin.dashboard')->with('error', 'Access denied. You do not have permission to access this module.');
        }
        $this->data['page_title'] = "Edit project";
        $this->data['project_detail'] = Projects::where('project.id', $id)->get();
        $this->data['users'] = User::whereStatus('Enabled')->orderBy('name')->pluck('name', 'id');
        $this->data['project_manager'] = ProjectManager::where('project_id', $id)->where('is_manager', '1')->pluck('user_id')->toArray();
        $this->data['expense_manager'] = ProjectManager::where('project_id', $id)->where('is_manager', '2')->pluck('user_id')->toArray();
        $this->data['project_employee'] = ProjectManager::where('project_id', $id)->where('is_manager', '0')->pluck('user_id')->toArray();
        if ($this->data['project_detail']->count() == 0) {
            return redirect()->route('admin.projects')->with('error', 'Error Occurred. Try Again!');
        }
        $this->data['companies'] = Companies::orderBy('company_name', 'asc')->pluck('company_name', 'id');
        // dd($this->data);
        return view('admin.project.edit_project', $this->data);
    }

    
    public function update_project_last(Request $request) {  //chnage
        $validator_normal = Validator::make($request->all(), [
                    'project_name' => 'required',
                    'details' => 'required',
                    'company_id' => 'required',
                    'client_id' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.projects')->with('error', 'Please follow validation rules.');
        }
        $project_id = $request->input('id');
        $project_arr = [
            'user_id' => Auth::user()->id,
            'project_name' => $request->input('project_name'),
            'details' => $request->input('details'),
            'company_id' => $request->input('company_id'),
            'client_id' => $request->input('client_id'),
            'project_location' => $request->input('project_location'),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
        ];

        Projects::where('id', $project_id)->update($project_arr);

        $client = Clients::whereId($request->input('client_id'))->first();
        if ($client->tender_id > 0) {
            $items = Tender_boq_bidder::where('tender_id', $client->tender_id)->where('bidder_id', $request->input('company_id'))->where('own_company', 1)->get()->toArray();
            // dd($items);
            if (count($items)) {
                Site_manage_boq::where('company_id', $request->input('company_id'))->where('project_id', $project_id)->delete();
                $item_arr = [];
                $item_id = 0;
                foreach ($items as $key => $value) {
                    $item_arr['company_id'] = $request->input('company_id');
                    $item_arr['project_id'] = $project_id;
                    $item_arr['tender_id'] = $client->tender_id;
                    $item_arr['item_no'] = $value['item_no'];
                    $item_arr['item_description'] = $value['item_of_work'];
                    $item_arr['UOM'] = $value['unit'];
                    $item_arr['quantity'] = $value['qty'];
                    $item_arr['rate'] = $value['estimated_rates'];
                    $item_arr['amount'] = $value['total_amount'];
                    $item_arr['parent_boq'] = $item_id;

                    if ($key == 0) {
                        $item_id = Site_manage_boq::insertGetId($item_arr);
                        Site_manage_boq::whereId($item_id)->update(['parent_boq' => $item_id]);
                    } else {
                        Site_manage_boq::insert($item_arr);
                    }
                }
            }
        }

        //Project Manager
        ProjectManager::where('project_id', $project_id)->delete();
        $manager_arr = [
            'project_id' => $project_id,
            'user_id' => $request->get('project_manager_id'),
            'is_manager' => 1,
            'created_ip' => $request->ip(),
            'updated_ip' => $request->ip(),
        ];
        ProjectManager::insert($manager_arr);
        //Expense Manager
        $expense_manager_arr = [
            'project_id' => $project_id,
            'user_id' => $request->get('expense_manager_id'),
            'is_manager' => 2,
            'created_ip' => $request->ip(),
            'updated_ip' => $request->ip(),
        ];
        ProjectManager::insert($expense_manager_arr);
        $emp_arr = [];
        foreach ($request->get('project_employee_id') as $key_emp => $value_emp) {
            $emp_arr[$key_emp]['project_id'] = $project_id;
            $emp_arr[$key_emp]['user_id'] = $value_emp;
            $emp_arr[$key_emp]['created_ip'] = $request->ip();
            $emp_arr[$key_emp]['updated_ip'] = $request->ip();
        }
        ProjectManager::insert($emp_arr);
        return redirect()->route('admin.projects')->with('success', 'Project successfully updated.');
    }
    // approve route function 
    public function approve_confirm_last($id, Request $request){
        if (Auth::user()->role != config('constants.SuperUser')){
            return redirect()->route('admin.dashboard')->with('error', 'Access denied. You do not have permission to access this module.');
        }
        $request->$id;
        $approve_request = ProjectUpdateApproveRequest::where('id', $id)->first();
        $managers = json_decode($approve_request['project_manager']);
        $approve_request_arr = ['company_id' => $approve_request->company_id,
                                'client_id' => $approve_request->client_id,
                                'project_name' => $approve_request->project_name,
                                'project_location' => $approve_request->project_location,
                                'details' => $approve_request->details
                                ];
            Projects::where('id',$approve_request->project_id)->update($approve_request_arr);

            ProjectManager::where('project_id', $approve_request->project_id)->delete();
            foreach ($managers as $key => $value) {
                ProjectManager::insert([
                    'project_id' => $value->project_id,
                    'user_id' => $value->user_id,
                    'is_manager' => (isset($value->is_manager)) ? $value->is_manager : "0",
                    'created_ip' => $request->ip(),
                    'updated_ip' => $request->ip(),
                ]);
            }
            ProjectUpdateApproveRequest::where('id', $id)->delete();
            return redirect()->route('admin.projects')->with('success', 'Request approved successfully.');
    }

    // update project for request

    public function update_project(Request $request) {  //chnage //update of approval request for project;
        $validator_normal = Validator::make($request->all(), [
                    'project_name' => 'required',
                    'details' => 'required',
                    'company_id' => 'required',
                    'client_id' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.projects')->with('error', 'Please follow validation rules.');
        }
        $project_id = $request->input('id');


        if (Auth::user()->role != config('constants.SuperUser')){
            $project_arr = [
                'user_id' => Auth::user()->id,
                'project_name' => $request->input('project_name'),
                'details' => $request->input('details'),
                'company_id' => $request->input('company_id'),
                'client_id' => $request->input('client_id'),
                'project_location' => $request->input('project_location'),
                'updated_at' => date('Y-m-d h:i:s'),
                'updated_ip' => $request->ip(),
                'project_id' => $project_id,
                'updated_by' => Auth::user()->id,
                'created_ip' => $request->ip(),
                'updated_ip' => $request->ip(),
                'created_at' => date('Y-m-d h:i:s'),
            ];
    
            // ProjectUpdateApproveRequest
            ProjectUpdateApproveRequest::where('project_id', $project_id)->delete();
            $manager_json = [];
            $manager_arr = [
                'project_id' => $project_id,
                'user_id' => $request->get('project_manager_id'),
                'is_manager' => 1,
                'created_ip' => $request->ip(),
                'updated_ip' => $request->ip(),
            ];
            array_push($manager_json,$manager_arr);
            // ProjectUpdateApproveRequest::insert($manager_arr);
            //Expense Manager
            $expense_manager_arr = [
                'project_id' => $project_id,
                'user_id' => $request->get('expense_manager_id'),
                'is_manager' => 2,
                'created_ip' => $request->ip(),
                'updated_ip' => $request->ip(),
            ];
            array_push($manager_json,$expense_manager_arr);
            // ProjectUpdateApproveRequest::insert($expense_manager_arr);
            $emp_arr = [];
            foreach ($request->get('project_employee_id') as $key_emp => $value_emp) {
                $emp_arr['project_id'] = $project_id;
                $emp_arr['user_id'] = $value_emp;
                $emp_arr['created_ip'] = $request->ip();
                $emp_arr['updated_ip'] = $request->ip();
    
                array_push($manager_json,$emp_arr);
            }
            // ProjectUpdateApproveRequest::insert($emp_arr);
            
            $project_arr['project_manager'] = json_encode($manager_json);
            ProjectUpdateApproveRequest::where('project_id',$project_id)->delete();
            ProjectUpdateApproveRequest::insert($project_arr);

            $success_message = 'Project update request set successfully.';
        }else{
            $project_arr = [
                'user_id' => Auth::user()->id,
                'project_name' => $request->input('project_name'),
                'details' => $request->input('details'),
                'company_id' => $request->input('company_id'),
                'client_id' => $request->input('client_id'),
                'project_location' => $request->input('project_location'),
                'updated_at' => date('Y-m-d h:i:s'),
                'updated_ip' => $request->ip(),
            ];
    
            Projects::where('id', $project_id)->update($project_arr);
    
            $client = Clients::whereId($request->input('client_id'))->first();
            if ($client->tender_id > 0) {
                $items = Tender_boq_bidder::where('tender_id', $client->tender_id)->where('bidder_id', $request->input('company_id'))->where('own_company', 1)->get()->toArray();
                // dd($items);
                if (count($items)) {
                    Site_manage_boq::where('company_id', $request->input('company_id'))->where('project_id', $project_id)->delete();
                    $item_arr = [];
                    $item_id = 0;
                    foreach ($items as $key => $value) {
                        $item_arr['company_id'] = $request->input('company_id');
                        $item_arr['project_id'] = $project_id;
                        $item_arr['tender_id'] = $client->tender_id;
                        $item_arr['item_no'] = $value['item_no'];
                        $item_arr['item_description'] = $value['item_of_work'];
                        $item_arr['UOM'] = $value['unit'];
                        $item_arr['quantity'] = $value['qty'];
                        $item_arr['rate'] = $value['estimated_rates'];
                        $item_arr['amount'] = $value['total_amount'];
                        $item_arr['parent_boq'] = $item_id;
    
                        if ($key == 0) {
                            $item_id = Site_manage_boq::insertGetId($item_arr);
                            Site_manage_boq::whereId($item_id)->update(['parent_boq' => $item_id]);
                        } else {
                            Site_manage_boq::insert($item_arr);
                        }
                    }
                }
            }
    
            //Project Manager
            ProjectManager::where('project_id', $project_id)->delete();
            $manager_arr = [
                'project_id' => $project_id,
                'user_id' => $request->get('project_manager_id'),
                'is_manager' => 1,
                'created_ip' => $request->ip(),
                'updated_ip' => $request->ip(),
            ];
            ProjectManager::insert($manager_arr);
            //Expense Manager
            $expense_manager_arr = [
                'project_id' => $project_id,
                'user_id' => $request->get('expense_manager_id'),
                'is_manager' => 2,
                'created_ip' => $request->ip(),
                'updated_ip' => $request->ip(),
            ];
            ProjectManager::insert($expense_manager_arr);
            $emp_arr = [];
            foreach ($request->get('project_employee_id') as $key_emp => $value_emp) {
                $emp_arr[$key_emp]['project_id'] = $project_id;
                $emp_arr[$key_emp]['user_id'] = $value_emp;
                $emp_arr[$key_emp]['created_ip'] = $request->ip();
                $emp_arr[$key_emp]['updated_ip'] = $request->ip();
            }
            ProjectManager::insert($emp_arr);

            $success_message = 'Project successfully updated.';
        }

        
        return redirect()->route('admin.projects')->with('success', $success_message);
    }

    public function delete_project($id) {
        $permission = Permissions::checkPermission($this->module_id, 4);
        if (!$permission) {
            return redirect()->route('admin.dashboard')->with('error', 'Access denied. You do not have permission to access this module.');
        }
        if (Projects::where('id', $id)->delete()) {
            return redirect()->route('admin.projects')->with('success', 'Delete successfully updated.');
        }
        return redirect()->route('admin.projects')->with('error', 'Error during operation. Try again!');
    }

    // edited here 4 delete _ project 4 request
    public function delete_project_last($id) {
        if (Auth::user()->role != config('constants.SuperUser')){
            return redirect()->route('admin.dashboard')->with('error', 'Access denied. You do not have permission to access this module.');
        }
        if (ProjectUpdateApproveRequest::where('id', $id)->delete()) {
            return redirect()->route('admin.project_update_request')->with('success', 'Request rejected successfully.');
        }
        return redirect()->route('admin.project_update_request')->with('error', 'Error during operation. Try again!');
    }
    // End of project dlt request

    public function get_projectlist_by_company(Request $request) {
        $company_id = $request->input('company_id');

        $project_list = Projects::where('company_id', $company_id)->orderBy('project_name')->get();

        $html = '<option value="">Select Project</option>';

        if ($project_list->count() > 0) {
            foreach ($project_list as $project) {
                $html .= '<option value="' . $project->id . '">' . $project->project_name . '</option>';
            }
        }
        echo $html;
        die();
    }

    public function get_project_managers(Request $request) {
        $project_manager = ProjectManager::where('project_id', $request->get('id'))->where('is_manager', 1)->with(['get_user_data'])->first();

        $expense_manager = ProjectManager::where('project_id', $request->get('id'))->where('is_manager', 2)->with(['get_user_data'])->first();

        $project_employee = ProjectManager::where('project_id', $request->get('id'))->where('is_manager', 0)->with(['get_user_data'])->get()->toArray();
        // dd($project_manager);
        if ($project_manager) {
            ?>

            <table class="table table-condensed">
                <tr>
                    <th colspan="2" ><span><strong>Project Manager</strong></span></th>
                </tr>
                <tr>
                    <th><b>Name</b></th>
                    <th><b>Email</b></th>
                </tr>
                <tr>
                    <td><?php echo $project_manager['get_user_data']['name'] ?></td>
                    <td><?php echo $project_manager['get_user_data']['email'] ?></td>
                </tr>
            </table>
            <table class="table table-condensed">
                <tr>
                    <th colspan="2" ><span><strong>Expense Manager</strong></span></th>
                </tr>
                <tr>
                    <th><b>Name</b></th>
                    <th><b>Email</b></th>
                </tr>
                <tr>
                    <td><?php echo $expense_manager['get_user_data']['name'] ?></td>
                    <td><?php echo $expense_manager['get_user_data']['email'] ?></td>
                </tr>
            </table>
            <table class="table table-condensed">
                <tr>
                    <th colspan="2" ><span><strong>Project Support Employee</strong></span></th>
                </tr>
                <tr>
                    <th><b>Name</b></th>
                    <th><b>Email</b></th>
                </tr>
                <?php
                foreach ($project_employee as $key => $value) {
                    ?>
                    <tr>
                        <td><?php echo $value['get_user_data']['name'] ?></td>
                        <td><?php echo $value['get_user_data']['email'] ?></td>
                    </tr>
                    <?php
                }
                ?>

            </table>
            <?php
        } else {
            echo "Project Manager, Expense Manager and support employee not add";
        }
    }

    public function checkProjectName(Request $request) {
        $name=$request->input('name');
        $client_id=$request->input('client_id');
        $check_result = Projects::where('project_name', $name)->where('client_id',$client_id)->get()->count();
        if ($check_result > 0) {
            echo 'false';
        } else {
            echo 'true';
        }
        die();
    }
    public function checkEditProjectName(Request $request) {
        $name=$request->input('name');
        $client_id=$request->input('client_id');
        $check_result = Projects::where('project_name', $name)->where('client_id',$client_id)
                ->where('id','!=',$request->input('project_id'))->get()->count();
        if ($check_result > 0) {
            echo 'false';
        } else {
            echo 'true';
        }
        die();
    }

}
