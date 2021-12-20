<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Lib\NotificationTask;
use App\Lib\CommonTask;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Exception;
use App\Vehicle_trip;

class TripController extends Controller
{

    private $page_limit = 20;
    public $common_task;
    private $module_id = 19;
    private $notification_task;
    private $super_admin;

    public function __construct()
    {
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();
        $this->super_admin = \App\User::where('role', config('constants.SuperUser'))->first();
    }

    public function add_trip_opening(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'asset_id' => 'required',
            'trip_user_id' => 'required',
            'opening_meter_reading' => 'required',
            'reading_image' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        //check role of user is of driver
        $logged_in_user = \App\User::where('id', $request_data['user_id'])->get();

        //check if any trip is open for this logged in user
        $open_trip_check_count = Vehicle_trip::where('user_id', $request_data['user_id'])->where('is_closed', 'No')->get(['id'])->count();

        if ($open_trip_check_count > 0) {
            return response()->json(['status' => false, 'msg' => "One trip is already open. Please close it to add new one.", 'data' => [], 'error' => config('errors.general_error.code')]);
        }

       
       /*  $reading_image = '';
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

        $trip_arr = [
            'asset_id' => $request_data['asset_id'],
            'user_id' => $request_data['user_id'],
            'note' => $request_data['note'],
            'status' => 'Pending',
            'opening_meter_reading' => $request_data['opening_meter_reading'],
            'opening_meter_reading_image' => !empty($reading_image) ? $reading_image : NULL,
            'opening_time' => date('Y-m-d H:i:s'),
            'is_closed' => 'No',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'created_ip' => $request->ip(),
            'updated_ip' => $request->ip(),
            'updated_by' => $request_data['user_id'],
        ];
        if ($request_data['trip_user_id'] == 0) {
            $trip_arr['trip_user_id'] = $request_data['user_id'];
            $trip_arr['trip_type'] = 'Individual';
        } else {
            $trip_arr['trip_user_id'] = $request_data['trip_user_id'];
            $trip_arr['trip_type'] = 'User';
        }
        Vehicle_trip::insert($trip_arr);

        //trip user detail
        $trip_user_data = \App\User::where('id', $trip_arr['trip_user_id'])->get(['name', 'email', 'id']);

        $mail_data = [
            'driver_name' => $logged_in_user[0]->name,
            'trip_user_name' => $trip_user_data[0]->name,
            'to_email_list' => [$trip_user_data[0]->email, $this->super_admin->email],
        ];
        $this->common_task->tripOpeningAlertEmail($mail_data);
        $notify_user_ids = [$this->super_admin->id, $trip_user_data[0]->id];
        $this->notification_task->tripOpenAlertNotify($notify_user_ids, $logged_in_user[0]->name, $trip_user_data[0]->name);
        return response()->json(['status' => true, 'msg' => "Trip successfully opened.", 'data' => []]);
    }

    public function edit_trip(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'asset_id' => 'required',
            'trip_user_id' => 'required',
            'opening_meter_reading' => 'required',
            'trip_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        //upload user Meter Reading Photo
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

        $logged_in_user = \App\User::where('id', $request_data['user_id'])->get();

        $trip_arr = [
            'asset_id' => $request_data['asset_id'],
            'user_id' => $request_data['user_id'],
            'note' => $request_data['note'],
            'status' => 'Pending',
            'opening_meter_reading' => $request_data['opening_meter_reading'],
            'opening_time' => date('Y-m-d H:i:s'),
            'is_closed' => 'No',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'created_ip' => $request->ip(),
            'updated_ip' => $request->ip(),
            'updated_by' => $request_data['user_id'],
        ];

        if (!empty($reading_image))
            $trip_arr['opening_meter_reading_image'] = $reading_image;


        if ($request_data['trip_user_id'] == 0) {
            $trip_arr['trip_user_id'] = $request_data['user_id'];
            $trip_arr['trip_type'] = 'Individual';
        } else {
            $trip_arr['trip_user_id'] = $request_data['trip_user_id'];
            $trip_arr['trip_type'] = 'User';
        }
        Vehicle_trip::where('id', $request_data['trip_id'])->update($trip_arr);

        //trip user detail
        $trip_user_data = \App\User::where('id', $trip_arr['trip_user_id'])->get(['name', 'email', 'id']);

        $mail_data = [
            'driver_name' => $logged_in_user[0]->name,
            'trip_user_name' => $trip_user_data[0]->name,
            'to_email_list' => [$trip_user_data[0]->email, $this->super_admin->email],
        ];
        $this->common_task->tripOpeningAlertEmail($mail_data);
        $notify_user_ids = [$this->super_admin->id, $trip_user_data[0]->id];
        $this->notification_task->tripOpenAlertNotify($notify_user_ids, $logged_in_user[0]->name, $trip_user_data[0]->name);
        return response()->json(['status' => true, 'msg' => "Trip successfully updated.", 'data' => []]);
    }

    public function add_trip_closing(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'trip_id' => 'required',
            'closing_meter_reading' => 'required',
            'reading_image' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        $logged_in_user = \App\User::where('id', $request_data['user_id'])->get();

       
       /*  $reading_image = '';
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

        $trip_detail = Vehicle_trip::where('id', $request_data['trip_id'])->get();

        $trip_arr = [

            'closing_meter_reading' => $request_data['closing_meter_reading'],
            'closing_meter_reading_image' => !empty($reading_image) ? $reading_image : NULL,
            'closing_time' => date('Y-m-d H:i:s'),
            'is_closed' => 'Yes',
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => $request_data['user_id'],
        ];

        Vehicle_trip::where('id', $request_data['trip_id'])->update($trip_arr);

        //trip user detail
        $trip_user_data = \App\User::where('id', $trip_detail[0]['trip_user_id'])->get(['name', 'email', 'id']);

        $mail_data = [
            'driver_name' => $logged_in_user[0]->name,
            'trip_user_name' => $trip_user_data[0]->name,
            'to_email_list' => [$trip_user_data[0]->email, $this->super_admin->email],
        ];
        //$this->common_task->tripOpeningAlertEmail($mail_data);
        $notify_user_ids = [$this->super_admin->id, $trip_user_data[0]->id];
        //$this->notification_task->tripOpenAlertNotify($notify_user_ids, $logged_in_user[0]->name, $trip_user_data[0]->name);
        return response()->json(['status' => true, 'msg' => "Trip successfully closed.", 'data' => []]);
    }

    public function get_trip_list_by_driver(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        //check role of user is of driver
        $logged_in_user = \App\User::where('id', $request_data['user_id'])->get();

         $trip_list = Vehicle_trip::join('users', 'users.id', '=', 'vehicle_trip.trip_user_id')
            ->join('asset', 'asset.id', '=', 'vehicle_trip.asset_id')
            ->where('vehicle_trip.user_id', $request_data['user_id'])
            ->orderBy('id', 'DESC')
            ->get(['vehicle_trip.*', 'asset.name as vehicle_name', 'users.name as trip_user_name']);
        
            if ($trip_list->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        foreach ($trip_list as $key => $trip) {

            if ($trip->opening_meter_reading_image) {

                $trip_list[$key]->opening_meter_reading_image = asset('storage/' . str_replace('public/', '', $trip->opening_meter_reading_image));
            } else {

                $trip_list[$key]->opening_meter_reading_image = "";
            }

            if ($trip->closing_meter_reading_image) {

                $trip_list[$key]->closing_meter_reading_image = asset('storage/' . str_replace('public/', '', $trip->closing_meter_reading_image));
            } else {

                $trip_list[$key]->closing_meter_reading_image = "";
            }
        }

        $response_data['trip_list'] = $trip_list;

        return response()->json(['status' => true, 'msg' => "record found", 'data' => $response_data]);
    }
}
