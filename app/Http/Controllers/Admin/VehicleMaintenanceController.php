<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Asset;
use App\User;
use App\Common_query;
use App\Vehicle_image;
use App\Vehicle_Insurance;
use App\Vehicle_Maintenance;
use App\AssetAccess;
use Illuminate\Support\Facades\Validator;
use App\Employees;
use App\Companies;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Lib\Permissions;
use App\Role_module;
use App\Lib\CommonTask;
use App\Lib\NotificationTask;
use App\Lib\UserActionLogs;

class VehicleMaintenanceController extends Controller {

    public $data;
    public $notification_task;
    public $common_task;
    private $super_admin;
    public $user_action_logs;
    private $module_id = 46;

    public function __construct() {
        $this->data['module_title'] = "Vehicle Maintenance";
        $this->data['module_link'] = "admin.vehicle_maintenance";
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();
        $this->user_action_logs = new UserActionLogs();
        $this->super_admin = \App\User::where('role', 1)->first();
    }

    public function vehicle_maintenance() {
        $vehicle_maintanence_my_view_permission = Permissions::checkPermission(46, 1);
        $vehicle_maintanence_full_view_permission = Permissions::checkPermission(46, 5);
        $this->data['page_title'] = "Vehicle Maintenance";

        $this->data['maintenance_data'] = $maintenance_data = Vehicle_Maintenance::select('asset.name', 'asset.asset_1', 'asset.id', 'vehicle_maintenance.*', 'vehicle_image.id AS v_id ', 'vehicle_image.vehicle_maintenance_id', 'vehicle_image.image','users.name as user_name')
                ->join('asset', 'vehicle_maintenance.asset_id', '=', 'asset.id')
                ->join('vehicle_image', 'vehicle_maintenance.id', '=', 'vehicle_image.vehicle_maintenance_id')
                ->join('users','users.id','=','vehicle_maintenance.user_id')
                ->where('asset.asset_type', '=', 'Vehicle Asset')
                ->where('asset.status', '=', 'Enabled')
                ->where(function($query) use($vehicle_maintanence_full_view_permission,$vehicle_maintanence_my_view_permission){
                    if(!$vehicle_maintanence_full_view_permission && $vehicle_maintanence_my_view_permission){
                        $query->where('vehicle_maintenance.user_id', Auth::user()->id);
                    }

                })
                ->groupBy('vehicle_image.vehicle_maintenance_id')
                ->orderBy('vehicle_image.id', 'asc')
                ->get();


        foreach ($maintenance_data as $key => $value) {

            $vehicle_images = Vehicle_image::select('id', 'vehicle_maintenance_id', 'image')->where('image', '!=', $value->image)->get(); //->toArray();
        }

        $this->data['vehicle_images'] = !empty($vehicle_images) ? $vehicle_images : NULL;

        return view('admin.vehicle_maintenance.index', $this->data);
    }

    public function add_vehicle_maintenance() {
        $this->data['page_title'] = 'Add Maintenance';
        $this->data['module_title'] = "Vehicle Maintenance";

        $company_id = Employees::where('user_id', Auth::user()->id)->value('company_id');

        $this->data['asset_data'] = Asset::where('asset_type', 'Vehicle Asset')
                ->where('status', 'Enabled')
                ->where('company_id', $company_id)->orderBy('name')
                ->get(['id', 'asset_1', 'name']);


        $this->data['company_list'] = \App\Companies::orderBy('company_name')->where('status', 'Enabled')->get();
        // dd($this->data['company_list']);
        // $this->data['company_name'] = $name = Companies::where('id', $company_id)->value('company_name');


        return view('admin.vehicle_maintenance.add_maintenance', $this->data);
    }

    public function insert_vehicle_maintenance(Request $request) {


        $validator_normal = Validator::make($request->all(), [
                    'asset_id' => 'required',
                    'maintenance_type' => 'required',
                    'description' => 'required',
                    'start_meter_reading' => 'required',
                    'service_center_name' => 'required',
                    'service_center_address' => 'required',
                    'amount' => 'required',
                    'maintenance_date' => 'required',
                    'received_date' => 'required',
        ]);

        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_vehicle_maintenance')->with('error', 'Please follow validation rules.');
        }
        $company_id = Employees::where('user_id', Auth::user()->id)->value('company_id');

        // !empty($vehicle_images) ? $vehicle_images : NULL;

        $next_periodic_date = $request->input('next_periodic_date');
        $maintenance_arr = [
            'user_id' => Auth::user()->id,
            'company_id' => $request->get('company_id'),
            'asset_id' => $request->input('asset_id'),
            'maintenance_type' => $request->input('maintenance_type'),
            'description' => $request->input('description'),
            'start_meter_reading' => $request->input('start_meter_reading'),
            'received_meter_reading' => $request->input('received_meter_reading'),
            'service_center_name' => $request->input('service_center_name'),
            'service_center_address' => $request->input('service_center_address'),
            'amount' => $request->input('amount'),
            'maintenance_date' => date('Y-m-d H:i:s', strtotime($request->input('maintenance_date'))),
            'received_date' => date('Y-m-d H:i:s', strtotime($request->input('received_date'))),
            'next_periodic_date' => !empty($next_periodic_date) ? date('Y-m-d', strtotime($next_periodic_date)) : NULL,
            'periodic_maintenance_km' => $request->input('periodic_maintenance_km'),
            'status'=>'Pending',
            'created_at' => date('Y-m-d H:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id
        ];


        $last_insert_id = Vehicle_Maintenance::insertGetId($maintenance_arr);

        // User Action Log
        $company_name = $request->get('company');
        $asset_name = Asset::whereId($request->get('asset_id'))->value('name');
        $add_string = "<br>Company Name: ".$company_name."<br> Asset Name: ".$asset_name."<br>Maintenance Type: ".$request->get('maintenance_type')."<br> Start Meter Reading: ".$request->get('start_meter_reading')."<br>Amount: ".$request->get('amount');
        $action_data = [
            'user_id' => Auth::user()->id,
            'task_body' => "Vehicle maintenance added".$add_string,
            'created_ip' => $request->ip(),
        ];
        $this->user_action_logs->action($action_data);

        //Intimate Maintenance return date to HR, SuperAdmin, Asset holder
        $aset_holder = AssetAccess::where('status','Confirmed')->where('asset_id',$request->input('asset_id'))->get(['asset_access_user_id']);
        $notify_ids = User::where('status', 'Enabled')->whereIn('role', [config('constants.REAL_HR'), config('constants.SuperUser')])->pluck('id')->toArray();
        $asset_name = Asset::where('id',$request->input('asset_id') )->value('name');
        $return_date = date('d-m-Y h:i A', strtotime($request->input('received_date')));
        //'HR, SuperAdmin, Asset holder'
        $email_list = User::where('status', 'Enabled')->whereIn('role', [config('constants.REAL_HR'), config('constants.SuperUser')])->pluck('email')->toArray();
        $mail_data = [];

        if($aset_holder->count() > 0){

            array_push($notify_ids, $aset_holder[0]->asset_access_user_id);
            $user_email = User::where('id',$aset_holder[0]->asset_access_user_id)->pluck('email')->toArray();
            $email_list = array_merge($email_list, $user_email );
        }

                    $mail_data['asset_name'] = $asset_name;
                    $mail_data['return_date'] = $return_date;
                    $mail_data['to_email'] = $email_list;

        $this->common_task->intimateAssetMaintenanceDate($mail_data);
        $this->notification_task->assetNotfy($notify_ids, $return_date ,$asset_name );

        if (Auth::user()->role == config('constants.Admin')) {

            $approvalArr = [
                'first_approval_status'=>"Approved",
                'first_approval_id'=>Auth::user()->id,
                'first_approval_date_time'=>date('Y-m-d H:i:s')
            ];

            $admin_user= User::where('role',config('constants.Admin'))->get(['id']);
            $this->notification_task->VehicleMaintenancePaymentFirstApprovalNotify([$admin_user[0]->id]);

            Vehicle_Maintenance::where('id', $last_insert_id)->update($approvalArr);
        }

        //upload Vehicle Images
        // $images = $request->file('vehicle_image');


        if ($request->file('vehicle_image')) {

            $vehicle_images_list = $request->file('vehicle_image');

            foreach ($vehicle_images_list as $vehicle_image) {

                $original_file_name = explode('.', $vehicle_image->getClientOriginalName());

                $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);


                $file_path = $vehicle_image->storeAs('public/vehicle_image', $new_file_name);

                $vehicle_image_arr = [
                    'vehicle_maintenance_id' => $last_insert_id,
                    'image' => $file_path,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                Vehicle_image::insert($vehicle_image_arr);
            }

        } else {

            return redirect()->back()->with('error', 'Invalid Image file found!');
        }

        return redirect()->route('admin.vehicle_maintenance')->with('success', 'Vehicle Maintenance successfully added.');
    }

    public function update_vehicle_maintenance($id) {
        $this->data['page_title'] = 'Update Vehicle Maintenance';
        $this->data['module_title'] = "Vehicle Maintenance";

        $this->data['asset_data'] = Asset::where('asset_type', 'Vehicle Asset')
                        ->where('status', 'Enabled')->get(['id', 'asset_1', 'name']);

        $this->data['exp_insurance_data'] = $exp_insurance_list = Vehicle_Insurance::select('*')
                ->where('id', '=', $id)
                ->get();

        $this->data['vehicle_maintenance_data'] = Vehicle_Maintenance::where('id', $id)->get(['id', 'maintenance_type', 'amount']);


        return view('admin.vehicle_maintenance.update_maintenance', $this->data);
    }

    public function submit_vehicle_maintenance(Request $request) {


        $validator_normal = Validator::make($request->all(), [
                    'received_meter_reading' => 'required',
                    'amount' => 'required',
                    'received_date' => 'required',
        ]);


        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_vehicle_maintenance')->with('error', 'Please follow validation rules.');
        }

        $vehicle_arr = [
            'received_meter_reading' => $request->input('received_meter_reading'),
            'amount' => $request->input('amount'),
            'received_date' => date('Y-m-d H:i:s', strtotime($request->input('received_date'))),
            'status' => 'Completed',
            'created_at' => date('Y-m-d H:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id
        ];


        $vehicle_maintenance_id = $request->input('id');

        Vehicle_Maintenance::where('id', $vehicle_maintenance_id)->update($vehicle_arr);

        // User Action Log
        $add_string = "<br> Received Meter Reading: ".$request->get('received_meter_reading')."<br>Amount: ".$request->get('amount');
        $action_data = [
            'user_id' => Auth::user()->id,
            'task_body' => "Vehicle maintenance updated".$add_string,
            'created_ip' => $request->ip(),
        ];
        $this->user_action_logs->action($action_data);

        if ($request->file('vehicle_image')) {

            $vehicle_images_list = $request->file('vehicle_image');

            foreach ($vehicle_images_list as $vehicle_image) {
                // store image to directory.

                $original_file_name = explode('.', $vehicle_image->getClientOriginalName());

                $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);


                $file_path = $vehicle_image->storeAs('public/vehicle_image', $new_file_name);


                $vehicle_image_arr = [
                    'vehicle_maintenance_id' => $vehicle_maintenance_id,
                    'image' => $file_path,
                    'is_after_update' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                Vehicle_image::insert($vehicle_image_arr);
            }
        }

        return redirect()->route('admin.vehicle_maintenance')->with('success', 'Vehicle Maintenance successfully Updated.');
    }

    public function get_vehicle_maintenance_list_ajax() {

        $datatable_fields = array('users.name', 'company.company_name','asset.name','asset.asset_1',
            'vehicle_maintenance.maintenance_type','vehicle_maintenance.description','vehicle_maintenance.start_meter_reading',
            'vehicle_maintenance.received_meter_reading','vehicle_maintenance.service_center_name','vehicle_maintenance.service_center_address',
            'vehicle_maintenance.amount','vehicle_maintenance.maintenance_date','vehicle_maintenance.received_date',
            'vehicle_maintenance.first_approval_status','vehicle_maintenance.second_approval_status','vehicle_maintenance.final_approval',

        );
        $request = Input::all();

        if (Auth::user()->role == config('constants.Admin')) {

            $conditions_array = ['vehicle_maintenance.first_approval_status' => 'Pending', 'vehicle_maintenance.second_approval_status' => 'Pending'];
        } elseif (Auth::user()->role == config('constants.SuperUser')) {
            $conditions_array = ['vehicle_maintenance.first_approval_status' => 'Approved',
                'vehicle_maintenance.second_approval_status' => 'Pending',
                'vehicle_maintenance.final_approval' => 'Pending'];
        }

        $join_str[1]['join_type'] = '';
        $join_str[1]['table'] = 'company';
        $join_str[1]['join_table_id'] = 'company.id';
        $join_str[1]['from_table_id'] = 'vehicle_maintenance.company_id';

        $join_str[2]['join_type'] = '';
        $join_str[2]['table'] = 'users';
        $join_str[2]['join_table_id'] = 'users.id';
        $join_str[2]['from_table_id'] = 'vehicle_maintenance.user_id';

        $join_str[3]['join_type'] = '';
        $join_str[3]['table'] = 'asset';
        $join_str[3]['join_table_id'] = 'asset.id';
        $join_str[3]['from_table_id'] = 'vehicle_maintenance.asset_id';

        $getfiled = array('vehicle_maintenance.*','users.name as user_name', 'company.company_name','asset.name as vehicle_name','asset.asset_1 as vehicle_number');
        $table    = "vehicle_maintenance";

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);

        die();
    }

    public function get_vehicle_maintenanace_files(Request $request){
        $id=$request->input('maintenanace_id');
        $vehicle_image= Vehicle_image::where('vehicle_maintenance_id',$id)->get();
        $html="";
        if($vehicle_image->count()==0){
            $html .='<tr><td colspan="2">No record found</td></tr>';
        }
        else{
            foreach($vehicle_image as $key=>$img){

                $file_path=asset('storage/' . str_replace('public/', '', $img->image));
                $num=$key+1;

                $html .='<tr><td>'.$num.'</td>
                <td><a href="'.$file_path.'" download ><img width="250px" height="250px" src="'.$file_path.'" class="" /></a></td>';
                if ($img->is_after_update == 0) {
                    $html .='<td><b>BEFORE</b></td>';
                }else{
                    $html .='<td><b>AFTER<b></td>';
                }
                $html .='</tr>';
            }
        }
        echo $html; die();
    }

    public function vehicle_maintenance_list()
    {
        $this->data['page_title'] = "Vehicle Maintenance Approval";
        $access_level = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => $this->module_id])->get()->first();
        $this->data['access_rule'] = '';
        if (!empty($access_level)) {
            $this->data['access_rule'] = $access_level->access_level;
        }

        $this->data['vehicle_maintenance_history'] = DB::table('vehicle_maintenance')
                        ->join('users', 'vehicle_maintenance.user_id', '=', 'users.id')
                        ->join('company', 'company.id', '=', 'vehicle_maintenance.company_id')
                        ->join('asset', 'asset.id', '=', 'vehicle_maintenance.asset_id')
                        ->get(['vehicle_maintenance.*','users.name as user_name', 'company.company_name','asset.name as asset_name','asset.asset_1'])->toArray();

        return view('admin.vehicle_maintenance.vehicle_maintenance_list', $this->data);
    }

    public function approve_vehicle_maintenance(Request $request) {

        /*$check_result = Permissions::checkPermission($this->module_id, 2);

        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }*/
        $id=$request->input('approve_paymentid');
        if (Auth::user()->role == config('constants.Admin')) {
            $maintenanceApprovealData = Vehicle_Maintenance::select('users.name', 'vehicle_maintenance.amount', 'users.email', 'users.id as user_id')
                            ->join('users', 'vehicle_maintenance.user_id', '=', 'users.id')
                            ->where('vehicle_maintenance.id', $id)->get();


            $this->notification_task->VehicleMaintenancePaymentFirstApprovalNotify([$this->super_admin->id]);

            $updateData = ['first_approval_status' => 'Approved', 'first_approval_id' => Auth::user()->id,'first_approval_date_time' => date('Y-m-d H:i:s')];
        } elseif (Auth::user()->role == config('constants.SuperUser')) {

            $maintenanceApprovealData = Vehicle_Maintenance::select('users.name', 'vehicle_maintenance.amount', 'users.email', 'users.id as user_id')
                            ->join('users', 'vehicle_maintenance.user_id', '=', 'users.id')
                            ->where('vehicle_maintenance.id', $id)->get();
            $data = [
                'username' => $maintenanceApprovealData[0]['name'],
                'amount' => $maintenanceApprovealData[0]['amount'],
                'email' => $maintenanceApprovealData[0]['email'],
                'status' => 'Approved',
            ];

            $this->common_task->approveRejectVehicleMaintenancePaymentEmail($data);
            //send notification to user who requested about approval
            $this->notification_task->VehicleMaintenancePaymentSecondApprovalNotify([$maintenanceApprovealData[0]->user_id]);

            $updateData = ['second_approval_status' => 'Approved', 'second_approval_id' => Auth::user()->id,
                'final_approval' => 'Approved','second_approval_date_time' => date('Y-m-d H:i:s')];
        }

        if (Vehicle_Maintenance::where('id', $id)->update($updateData)) {

            // User Action Log
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => "Vehicle maintenance approved",
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);

            return redirect()->route('admin.vehicle_maintenance_list')->with('success', 'Status successfully updated.');
        }
        return redirect()->route('admin.vehicle_maintenance_list')->with('error', 'Error during operation. Try again!');
    }

    public function reject_vehicle_maintenance(Request $request) {
       /* $check_result = Permissions::checkPermission(24, 2);

        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }*/
        $id=$request->input('paymentid');
        $note=$request->input('note');

        if (Auth::user()->role == config('constants.Admin')) {
            $bankApprovealData = Vehicle_Maintenance::select('users.name', 'vehicle_maintenance.amount', 'users.id as user_id', 'users.email','users.id as user_id')
                            ->join('users', 'vehicle_maintenance.user_id', '=', 'users.id')
                            ->where('vehicle_maintenance.id', $id)->get()->toArray();

            $data = [
                'username' => $bankApprovealData[0]['name'],
                'amount' => $bankApprovealData[0]['amount'],
                'email' => $bankApprovealData[0]['email'],
                'status' => 'Rejected',
            ];

            $this->common_task->approveRejectVehicleMaintenancePaymentEmail($data);

            //send notification to user who requested about approval
            $this->notification_task->VehicleMaintenancePaymentRejectNotify([$bankApprovealData[0]['user_id']]);

            $updateData = ['reject_note' => $note, 'first_approval_status' => 'Rejected', 'first_approval_id' => Auth::user()->id, 'final_approval' => 'Rejected','first_approval_date_time' => date('Y-m-d H:i:s')];
        }
        elseif (Auth::user()->role == config('constants.SuperUser')) {

            $bankApprovealData = Vehicle_Maintenance::select('users.name', 'vehicle_maintenance.amount', 'users.id as user_id', 'users.email','users.id as user_id')
                            ->join('users', 'vehicle_maintenance.user_id', '=', 'users.id')
                            ->where('vehicle_maintenance.id', $id)->get()->toArray();

            $data = [
                'username' => $bankApprovealData[0]['name'],
                'amount' => $bankApprovealData[0]['amount'],
                'email' => $bankApprovealData[0]['email'],
                'status' => 'Rejected',
            ];

            $this->common_task->approveRejectVehicleMaintenancePaymentEmail($data);

            //send notification to user who requested about approval
            $this->notification_task->VehicleMaintenancePaymentRejectNotify([$bankApprovealData[0]['user_id']]);

            $updateData = ['reject_note' => $note, 'second_approval_status' => 'Rejected', 'second_approval_id' => Auth::user()->id, 'final_approval' => 'Rejected','second_approval_date_time' => date('Y-m-d H:i:s')];
        }


        if (Vehicle_Maintenance::where('id', $id)->update($updateData)) {

            // User Action Log
            $action_data = [
                'user_id' => Auth::user()->id,
                'task_body' => "Vehicle maintenance rejected",
                'created_ip' => $request->ip(),
            ];
            $this->user_action_logs->action($action_data);
            return redirect()->route('admin.online_payment_list')->with('success', 'Status successfully updated.');
        }
        return redirect()->route('admin.vehicle_maintenance_list')->with('error', 'Error during operation. Try again!');
    }
}
