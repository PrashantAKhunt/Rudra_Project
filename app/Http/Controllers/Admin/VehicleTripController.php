<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use App\Common_query;
use App\Companies;
use App\User;
use App\Banks;
use App\Asset;
use App\Vehicle_trip;
use Illuminate\Support\Facades\Validator;

use App\Lib\NotificationTask;
use App\Lib\CommonTask;
use App\Role_module;
use App\Lib\Permissions;

class VehicleTripController extends Controller
{
    public $data;
    private $module_id;

    public function __construct()
    {
        $this->data['module_title'] = "Vehicle Trip";
        $this->data['module_link'] = "admin.vehicle_trip";
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();
        $this->super_admin = \App\User::where('role', config('constants.SuperUser'))->first();
        $this->module_id = 37;
    }

    public function index()
    {
        $driver_ids = $this->common_task->get_trip_management();
        
        if(!in_array(Auth::user()->id,$driver_ids)){
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }
        $this->data['page_title']  = "Vehicle Trip";
        // $this->data['access_rule'] = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => 37])->get()->first();
        // 37
        $this->data['access_rule'] = '';
        $access_level = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => $this->module_id])->get()->first();

        /* $trip_my_view_permission      = Permissions::checkPermission($this->module_id, 1);
        if (!$trip_my_view_permission) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        } */

        $vehicle_trip_data  = Vehicle_trip::select('user_id', 'status')->where('is_closed', 'No')->where('user_id', Auth::user()->id)->get()->toArray();
        if (!empty($vehicle_trip_data[0])) {
            $this->data['is_any_trip_open'] = 'yes';
        } else {
            $this->data['is_any_trip_open'] = 'no';
        }

        if (!empty($access_level)) {
            $this->data['access_rule'] = $access_level->access_level;
        }

        return view('admin.vehicle_trip.index', $this->data);
    }


    public function get_vehicle_trip_list()
    {
        $datatable_fields = array('vehicle_trip.id', 'vehicle_trip.from_location', 'trip_users.name', 'vehicle_trip.to_location', 'users.name', 'asset.name', 'trip_type', 'opening_meter_reading', 'opening_time', 'is_closed', 'vehicle_trip.status', 'note', 'closing_meter_reading', 'closing_time');
        $request = Input::all();
        $conditions_array = ['user_id' => Auth::user()->id];

        $getfiled = array('vehicle_trip.id', 'vehicle_trip.from_location', 'trip_users.name  as trip_names', 'vehicle_trip.to_location', 'users.name', 'asset.name as Assetname', 'trip_type', 'opening_meter_reading', 'opening_time', 'is_closed', 'vehicle_trip.status', 'note', 'closing_meter_reading', 'closing_time', 'opening_meter_reading_image', 'closing_meter_reading_image');
        $table = "vehicle_trip";
        $join_str = [];
        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'users';
        $join_str[0]['join_table_id'] = 'vehicle_trip.user_id';
        $join_str[0]['from_table_id'] = 'users.id';

        $join_str[1]['join_type'] = '';
        $join_str[1]['table'] = 'asset';
        $join_str[1]['join_table_id'] = 'vehicle_trip.asset_id';
        $join_str[1]['from_table_id'] = 'asset.id';

        $join_str[2]['join_type'] = '';
        $join_str[2]['table'] = 'users AS trip_users';
        $join_str[2]['join_table_id'] = 'vehicle_trip.trip_user_id';
        $join_str[2]['from_table_id'] = 'trip_users.id';

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);
        die();
    }

    public function add_vehicle_trip()
    {
        $this->data['page_title'] = 'Add Vehicle Trip';
        $this->data['asset']      = Asset::where('asset_type', 'Vehicle Asset')->where('status', 'Enabled')->pluck('name', 'id');
        $this->data['user']       = User::where('id', '!=', Auth::user()->id)->where('status', 'Enabled')->pluck('name', 'id');

        $driver_ids = $this->common_task->get_trip_management();
        
        if(!in_array(Auth::user()->id,$driver_ids)){
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }

        /* $trip_add_view_permission = Permissions::checkPermission($this->module_id, 3);
        if (!$trip_add_view_permission) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        } */
        return view('admin.vehicle_trip.add_vehicle_trip', $this->data);
    }

    public function insert_vehicle_trip(Request $request)
    {
        $validator_normal = Validator::make($request->all(), [
            'asset_id' => 'required',
            'user_id' => 'required',
            'note' => 'required',
            'opening_meter_reading' => 'required',

        ]);

        $driver_ids = $this->common_task->get_trip_management();
        
        if(!in_array(Auth::user()->id,$driver_ids)){
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }
        /* $trip_add_view_permission = Permissions::checkPermission($this->module_id, 3);
        if (!$trip_add_view_permission) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        } */

        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_vehicle_trip')->with('error', 'Please follow validation rules.');
        }

        
        /* $reading_image = '';
        if ($request->hasFile('reading_image')) {
            
            $meter_reading = $request->file('reading_image');
            $file_path = $meter_reading->store('public/trip_images');
            if ($file_path) {
                $reading_image = $file_path;
            }
        } */

           //21-02-2020
           //upload user Meter Reading Photo
           $reading_image = '';
           if ($request->file('reading_image')) {

            $meter_reading = $request->file('reading_image');
         
            $original_file_name = explode('.', $meter_reading->getClientOriginalName());
    
                        $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);
    
    
                        $file_path = $meter_reading->storeAs('public/trip_images', $new_file_name); 
                        if ($file_path) {
                            $reading_image = $file_path;
                        }                   
                       
            }

        $vehicleTripModel = new Vehicle_trip();
        $vehicleTripModel->asset_id = $request->input('asset_id');
        $vehicleTripModel->user_id  = Auth::user()->id;


        if ($request->input('user_id') == 0 || $request->input('user_id') == '0') {
            $vehicleTripModel->trip_user_id = Auth::user()->id;
            $vehicleTripModel->trip_type    = 'Individual';
        } else {
            $vehicleTripModel->trip_user_id = $request->input('user_id');
            $vehicleTripModel->trip_type    = 'User';
        }

        $vehicleTripModel->note      = $request->input('note');
        $vehicleTripModel->status    = 'Pending';
        $vehicleTripModel->opening_meter_reading = $request->input('opening_meter_reading');
        $vehicleTripModel->opening_meter_reading_image = !empty($reading_image) ? $reading_image : NULL;
        $vehicleTripModel->opening_time = date('Y-m-d h:i:s');
        $vehicleTripModel->is_closed    = 'No';
        
        $vehicleTripModel->created_at = date('Y-m-d h:i:s');
        $vehicleTripModel->created_ip = $request->ip();
        $vehicleTripModel->updated_ip = $request->ip();
        $vehicleTripModel->updated_at = date('Y-m-d h:i:s');
        $vehicleTripModel->from_location = $request->input('from_location');
        $vehicleTripModel->to_location = $request->input('to_location');
        $vehicleTripModel->save();

        if (!empty($vehicleTripModel)) {

            //trip user detail
            $trip_user_data = \App\User::where('id', $vehicleTripModel->trip_user_id)->get(['name', 'email', 'id']);

            if (intval($trip_user_data[0]->id) == intval(Auth::user()->id)) {
                $trip_user_name = 'Individual';
            } else {
                $trip_user_name = $trip_user_data[0]->name;
            }

            $mail_data = [
                'driver_name' => Auth::user()->name,
                'trip_user_name' => $trip_user_name,
                'to_email_list' => [$trip_user_data[0]->email, $this->super_admin->email],
            ];

            $this->common_task->tripOpeningAlertEmail($mail_data);
            $notify_user_ids = [$this->super_admin->id, $trip_user_data[0]->id];

            $this->notification_task->tripOpenAlertNotify($notify_user_ids, Auth::user()->name, $trip_user_name);

            return redirect()->route('admin.vehicle_trip')->with('success', 'Vehicle trip add successfully.');
        } else {
            return redirect()->route('admin.add_vehicle_trip')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function delete_vehicle_trip($id)
    {
        if (Vehicle_trip::where('id', $id)->delete()) {
            return redirect()->route('admin.vehicle_trip')->with('success', 'Delete successfully updated.');
        }
        return redirect()->route('admin.vehicle_trip')->with('error', 'Error during operation. Try again!');
    }

    public function edit_vehicle_trip($id)
    {
        $this->data['page_title'] = 'Edit Vehicle Trip';
        $this->data['id']  = $id;

        $trip_edit_view_permission = Permissions::checkPermission($this->module_id, 2);
        if (!$trip_edit_view_permission) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }

        return view('admin.vehicle_trip.edit_vehicle_trip', $this->data);
    }

    public function update_vehicle_trip(Request $request)
    {
        $validator_normal = Validator::make($request->all(), [
            'closing_meter_reading' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.vehicle_trip')->with('error', 'Please follow validation rules.');
        }
        $driver_ids = $this->common_task->get_trip_management();
        
        if(!in_array(Auth::user()->id,$driver_ids)){
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }
/*         $trip_edit_view_permission = Permissions::checkPermission($this->module_id, 2);
        if (!$trip_edit_view_permission) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        } */

        $id = $request->input('id');
        
        
        /* $reading_image = '';
        if ($request->hasFile('reading_image')) {
            $meter_reading = $request->file('reading_image');
            $file_path = $meter_reading->store('public/trip_images');
            if ($file_path) {
                $reading_image = $file_path;
            }
        } */

        //21-02-2020
           //upload user Closing Meter Reading Photo
           $reading_image = '';
           if ($request->file('reading_image')) {

            $meter_reading = $request->file('reading_image');
         
            $original_file_name = explode('.', $meter_reading->getClientOriginalName());
    
                        $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);
    
    
                        $file_path = $meter_reading->storeAs('public/trip_images', $new_file_name); 
                        if ($file_path) {
                            $reading_image = $file_path;
                        }                   
                       
            }

        $trip_data = Vehicle_trip::select('trip_user_id')->where('id', $id)->get()->toArray();

        $trip_arr = [
            'closing_meter_reading' => date('Y-m-d', strtotime($request->input('closing_meter_reading'))),
            'closing_meter_reading_image' => !empty($reading_image) ? $reading_image : NULL,
            'closing_time' => date('Y-m-d h:i:s'),
            'is_closed' => 'Yes'
        ];

        Vehicle_trip::where('id', $id)->update($trip_arr);

        //trip user detail
        $trip_user_data = \App\User::where('id', $trip_data[0]['trip_user_id'])->get(['name', 'email', 'id']);

        if (intval($trip_user_data[0]->id) == intval(Auth::user()->id)) {
            $trip_user_name = 'Individual';
        } else {
            $trip_user_name = $trip_user_data[0]->name;
        }


        $mail_data = [
            'driver_name' => Auth::user()->name,
            'trip_user_name' => $trip_user_name,
            'to_email_list' => [$trip_user_data[0]->email, $this->super_admin->email],
        ];
        $this->common_task->tripCloseAlertEmail($mail_data);
        $notify_user_ids = [$this->super_admin->id, $trip_user_data[0]->id];

        $this->notification_task->tripCloseAlertNotify($notify_user_ids, Auth::user()->name, $trip_user_name);

        return redirect()->route('admin.vehicle_trip')->with('success', 'Vehicle Trip updated successfully.');
    }

    public function get_close_vehicle_trip_list()
    {
        $datatable_fields = array('vehicle_trip.id', 'users.name','trip_users.name', 'vehicle_trip.from_location', 'vehicle_trip.to_location', 'asset.name', 'trip_type', 'opening_meter_reading', 'opening_time', 'is_closed', 'vehicle_trip.status', 'note', 'closing_meter_reading', 'closing_time');
        $request = Input::all();
        $conditions_array1 = [];
        if (Auth::user()->role == config('constants.SuperUser')) {
            $conditions_array = ['trip_type' => 'Individual'];
        } else {
            $conditions_array = ['trip_user_id' => Auth::user()->id];
            $conditions_array1 = ['trip_type' => 'User'];
        }


        $getfiled = array('vehicle_trip.id', 'users.name', 'vehicle_trip.from_location', 'vehicle_trip.to_location', 'trip_users.name as trip_names', 'asset.name as Assetname', 'trip_type', 'opening_meter_reading', 'opening_time', 'is_closed', 'vehicle_trip.status', 'note', 'closing_meter_reading', 'closing_time', 'opening_meter_reading_image', 'closing_meter_reading_image');
        $table = "vehicle_trip";
        $join_str = [];
        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'users';
        $join_str[0]['join_table_id'] = 'vehicle_trip.user_id';
        $join_str[0]['from_table_id'] = 'users.id';

        $join_str[1]['join_type'] = '';
        $join_str[1]['table'] = 'asset';
        $join_str[1]['join_table_id'] = 'vehicle_trip.asset_id';
        $join_str[1]['from_table_id'] = 'asset.id';

        $join_str[2]['join_type'] = '';
        $join_str[2]['table'] = 'users AS trip_users';
        $join_str[2]['join_table_id'] = 'vehicle_trip.trip_user_id';
        $join_str[2]['from_table_id'] = 'trip_users.id';
       
        echo Vehicle_trip::get_close_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str, $conditions_array1);
        die();
    }

    public function close_trip_index()
    {
       
        $this->data['page_title'] = "Vehicle Close Trip List";
        $vehicle_trip_data  = Vehicle_trip::select('user_id', 'status')->where('is_closed', 'No')->where('trip_user_id', Auth::user()->id)->get()->toArray();
        return view('admin.vehicle_trip.close_trip_index', $this->data);
    }

    public function approve_vehicle_trip($id)
    {
        $trip_arr = [
            'status' => 'Approved',
        ];

        Vehicle_trip::where('id', $id)->update($trip_arr);

        $trip_data = Vehicle_trip::select('user_id', 'trip_user_id', 'trip_type')->where('id', $id)->get()->toArray();
        //trip user detail
        $trip_user_data = \App\User::where('id', $trip_data[0]['user_id'])->get(['name', 'email', 'id']);

        if (intval($trip_data[0]['user_id']) == intval($trip_data[0]['trip_user_id'])) {
            $trip_user_name = "Individual";
        } else {
            $trip_user_name = Auth::user()->name;
        }

        $mail_data = [
            'driver_name' => $trip_user_data[0]->name,
            'trip_user_name' => $trip_user_name,
            'to_email_list' => [$trip_user_data[0]->email],
        ];
        $this->common_task->tripApproveAlertEmail($mail_data);
        $notify_user_ids = [$this->super_admin->id, $trip_user_data[0]->id];
        $this->notification_task->tripApproveAlertNotify($notify_user_ids, $trip_user_data[0]->name, $trip_user_name);

        return redirect()->route('admin.close_trip_index')->with('success', 'Vehicle Trip approved successfully.');
    }

    public function reject_vehicle_trip(Request $request)
    {
        $validator_normal = Validator::make($request->all(), [
            'reject_note' => 'required',
        ]);

        if ($validator_normal->fails()) {
            return redirect()->route('admin.close_trip_index')->with('error', 'Please follow validation rules.');
        }

        $id = $request->input('trip_id');

        $trip_arr = [
            'reject_note' => !empty($request->input('reject_note')) ? $request->input('reject_note') : "",
            'status' => 'Rejected'
        ];

        Vehicle_trip::where('id', $id)->update($trip_arr);

        $trip_data = Vehicle_trip::select('user_id', 'reject_note', 'trip_user_id')->where('id', $id)->get()->toArray();
        //trip user detail
        $trip_user_data = \App\User::where('id', $trip_data[0]['user_id'])->get(['name', 'email', 'id']);

        if (intval($trip_data[0]['user_id']) == intval($trip_data[0]['trip_user_id'])) {
            $trip_user_name = "Individual";
        } else {
            $trip_user_name = Auth::user()->name;
        }

        $mail_data = [
            'driver_name' => $trip_user_data[0]->name,
            'trip_user_name' => $trip_user_name,
            'to_email_list' => [$trip_user_data[0]->email],
            'reject_note' => $request->input('reject_note'),
        ];
        $this->common_task->tripRejectAlertEmail($mail_data);
        $notify_user_ids = [$this->super_admin->id, $trip_user_data[0]->id];
        $this->notification_task->tripRejectAlertNotify($notify_user_ids, $trip_user_data[0]->name, $trip_user_name, $request->input('reject_note'));

        return redirect()->route('admin.close_trip_index')->with('success', 'Vehicle Trip updated successfully.');
    }

    public function vehicle_trip_list_report(Request $request)
    {
        $this->data['page_title'] = "Vehicle Trip Report";
        $this->data['user'] =  User::where("status", "Enabled")->where('role', config('constants.DRIVER'))->get()->pluck('name', 'id');
        $this->data['records'] = [];
        $this->data['selectedUser'] = [];
        $this->data['date'] = "";
        $this->data['report_type'] = "";
        $this->data['csv_data'] = "javascript:void(0);";

        $trip_full_view_permission = Permissions::checkPermission($this->module_id, 5);
        if (!$trip_full_view_permission) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }
        $from = date('Y-m-d H:i:s');
        $date = strtotime($from);
        $first_date = strtotime("-7 day", $date);
        $second_date   = date('Y-m-d H:i:s', $date);

        if ($request->method() == 'POST') {
            $this->data['selectedUser'] = $request->get('user_id');
            $user_ids = $request->get('user_id');
            //dd($this->data['selectedUser']);
            $this->data['date'] = $request->get('date');
            $this->data['report_type'] = $request->get('report_type');
            $reportType = $request->get('report_type');
            $date = $request->get('date');
            $mainDate = explode("-", $date);
            $strFirstdate = str_replace("/", "-", $mainDate[0]);
            $strLastdate = str_replace("/", "-", $mainDate[1]);
            $first_date = date('Y-m-d h:m:s', strtotime($strFirstdate));
            $second_date = date('Y-m-d h:m:s', strtotime($strLastdate));
            $tripType = "Individual";
            $trip_user_id = $request->get('trip_user_id');

            if ($reportType == "individual") {
                $tripType = "Individual";
            }

            if ($reportType == "user") {
                $tripType = "User";
            }

            $query = Vehicle_trip::select('vehicle_trip.*', 'users.name', 'trip_users.name as trip_names', 'asset.name as asset_name')
                ->leftJoin('users', 'users.id', '=', 'vehicle_trip.user_id')
                ->leftJoin('users as trip_users', 'trip_users.id', '=', 'vehicle_trip.trip_user_id')
                ->leftJoin('asset', 'asset.id', '=', 'vehicle_trip.asset_id')
                ->where(function ($query) use ($user_ids) {
                    if (!empty($user_ids)) {
                        $query->orwhereIn('vehicle_trip.user_id', $user_ids);
                    }
                })
                ->where('vehicle_trip.is_closed', '=', 'Yes')
                ->whereBetween('vehicle_trip.created_at', [$first_date, $second_date]);
            // ->where('vehicle_trip.created_at','>=',$first_date)
            // ->where('vehicle_trip.created_at','<=',$second_date)
            if ($reportType == "all") {
                $this->data['records'] = $query->orderBy('vehicle_trip.id','DESC')->get();
            } else {
                $this->data['records'] = $query->where('vehicle_trip.trip_type', '=', $tripType)->orderBy('vehicle_trip.id','DESC')->get();
            }
        }
        return view('admin.vehicle_trip.vehicle_report', $this->data);
    }
}
