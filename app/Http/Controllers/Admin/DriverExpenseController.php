<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Common_query;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Imports\BankTransactionImport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Config;
use App\User;
use App\Asset;
use App\Driver_expense;
use App\Email_format;
use App\Role_module;
use App\Mail\Mails;
use Illuminate\Support\Facades\Mail;
use App\Lib\CommonTask;
use App\Lib\NotificationTask;
use App\Lib\Permissions;
use App\AssetAccess;
use App\PeriodicMaintenanceKilometer;
use App\Roles;
use App\Vehicle_Maintenance;

class DriverExpenseController extends Controller {

    public $data;
    public $common_task;
    private $module_id = 4;
    public $notification_task;

    public function __construct() {
        $this->data['module_title'] = "Driver Expense";
        $this->data['module_link'] = "admin.expense";
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();
    }

    public function index() {
        
        /* if (Auth::user()->role != config('constants.DRIVER')) {
            return redirect()->route('admin.dashboard')->with('error', 'This module is only for drivers. You can not access it.');
        } */

        $driver_ids = $this->common_task->get_vehicle_asset_driver();
        if(!in_array(Auth::user()->id,$driver_ids)){
            return redirect()->route('admin.dashboard')->with('error', 'This module is only for drivers. You can not access it.');
        }

        $this->data['page_title'] = "Driver Expense";
        $access_level = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => 4])->get()->first();
        $this->data['access_rule'] = '';
        if (!empty($access_level)) {
            $this->data['access_rule'] = $access_level->access_level;
        }

        return view('admin.driver_expense.index', $this->data);
    }

    public function get_my_expense_list() {

        $userId = Auth::user()->id;
        $datatable_fields = array('driver_expense.fuel_type', 'asset.name', 'asset.asset_1', 'driver_expense.date_of_expense', 'driver_expense.time_of_expense', 'driver_expense.amount', 'driver_expense.total_fuel_quality','driver_expense.meter_reading','driver_expense.comment', 'driver_expense.reambance_type', 'driver_expense.first_approval_status', 'driver_expense.second_approval_status', 'driver_expense.third_approval_status', 'driver_expense.status');

        $request = Input::all();
        $conditions_array = ["user_id" => $userId];
        $join_str[0]['table'] = 'asset';
        $join_str[0]['join_table_id'] = 'asset.id';
        $join_str[0]['from_table_id'] = 'driver_expense.asset_id';
        $join_str[0]['join_type'] = '';

        $getfiled = array('driver_expense.id','driver_expense.fuel_price','driver_expense.total_fuel_quality','driver_expense.meter_reading', 'driver_expense.date_of_expense', 'driver_expense.time_of_expense','asset.name', 'asset.asset_1', 'driver_expense.fuel_type', 'driver_expense.vehicle_type', 'driver_expense.date_of_expense', 'driver_expense.time_of_expense', 'driver_expense.amount', 'driver_expense.comment', 'driver_expense.first_approval_status', 'driver_expense.second_approval_status', 'driver_expense.third_approval_status', 'driver_expense.status', 'driver_expense.asset_id', 'driver_expense.reambance_type','driver_expense.payment_type','driver_expense.card_number');
        $table = "driver_expense";

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);

        die();
    }

    public function add_expense() {
        /* if (Auth::user()->role != config('constants.DRIVER')) {
            return redirect()->route('admin.dashboard')->with('error', 'This module is only for drivers. You can not access it.');
        } */

        $driver_ids = $this->common_task->get_vehicle_asset_driver();
        if(!in_array(Auth::user()->id,$driver_ids)){
            return redirect()->route('admin.dashboard')->with('error', 'This module is only for drivers. You can not access it.');
        }

        $this->data['asset_list'] = \App\Asset::where('asset_type', 'Vehicle Asset')->get();

        $AssetAccess = AssetAccess::select('asset_id')->where('asset_access_user_id', Auth::user()->id)->get()->take(1)->toArray();
        $this->data['asset_id'] = 0;
        if (!empty($AssetAccess[0]['asset_id'])) {
            $this->data['asset_id'] = $AssetAccess[0]['asset_id'];
        }
        $this->data['page_title'] = 'Add Driver Expense';
        $this->data['users'] = User::select('name', 'id')->where('role', config('constants.ACCOUNT_ROLE'))
        ->where('status', "Enabled" )->get()->toArray();
        return view('admin.driver_expense.add_expense', $this->data);
    }

    public function insert_expense(Request $request) {
        $validator_normal = Validator::make($request->all(), [
                    //'fuel_type' => 'required',
                    'vehicle_type' => 'required',
                    //'date_of_expense' => 'required',
                    //'time_of_expense' => 'required',
                    'meter_reading_photo' => 'required',
                    'moniter_user_id' => 'required',
                    'bill_photo' => 'required',
                    'amount' => 'required',
                    'comment' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_expense')->with('error', 'Please follow validation rules.');
        }
        // dd($request->all());
        //upload user Meter Reading Photo
        /* $meter_reading_photo = '';
          if ($request->hasFile('meter_reading_photo')) {
          $meter_reading = $request->file('meter_reading_photo');
          $file_path = $meter_reading->store('public/driver_expense');
          if ($file_path) {
          $meter_reading_photo = $file_path;
          }
          } */


        //21-02-2020
        if ($request->file('meter_reading_photo')) {

            $meter_reading = $request->file('meter_reading_photo');

            $original_file_name = explode('.', $meter_reading->getClientOriginalName());

            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);


            $file_path = $meter_reading->storeAs('public/expense_image', $new_file_name);
            if ($file_path) {
                $meter_reading_photo = $file_path;
            }
        }

        //upload user Bill Photo
        /*  $bill_photo = '';
          if ($request->hasFile('bill_photo')) {
          $bill = $request->file('bill_photo');
          $file_path = $bill->store('public/driver_expense');
          if ($file_path) {
          $bill_photo = $file_path;
          }
          } */


        //21-02-2020
        if ($request->file('bill_photo')) {

            $bill = $request->file('bill_photo');

            $original_file_name = explode('.', $bill->getClientOriginalName());

            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);


            $file_path = $bill->storeAs('public/expense_image', $new_file_name);
            if ($file_path) {
                $bill_photo = $file_path;
            }
        }


        $asset_Data = Asset::select(['fuel_type'])->where('id', $request->input('vehicle_type'))->get()->toArray();

        $expenseModel = new Driver_expense();
        $expenseModel->user_id = Auth::user()->id;
        $expenseModel->fuel_type = !empty($asset_Data[0]) ? $asset_Data[0]['fuel_type'] : ""; //$request->input('fuel_type');
        $expenseModel->asset_id = $request->input('vehicle_type');

        $expenseModel->meter_reading = $request->input('meter_reading');
        $expenseModel->fuel_price = $request->input('fuel_price');
        $expenseModel->total_fuel_quality = intval($request->input('amount')) / intval($request->input('fuel_price'));
        $expenseModel->moniter_user_id = $request->input('moniter_user_id');
        $expenseModel->date_of_expense = date('Y-m-d'); //$request->input('date_of_expense');
        $expenseModel->time_of_expense = date('h:i:s'); //$request->input('time_of_expense');
        $expenseModel->amount = $request->input('amount');
        $expenseModel->comment = $request->input('comment');
        $expenseModel->reambance_type = $request->input('reambance_type');
        $expenseModel->meter_reading_photo = !empty($meter_reading_photo) ? $meter_reading_photo : NULL;
        $expenseModel->bill_photo = !empty($bill_photo) ? $bill_photo : NULL;
        $expenseModel->status = 1; // 1 = Pending
        $expenseModel->created_at = date('Y-m-d h:i:s');
        $expenseModel->created_ip = $request->ip();
        $expenseModel->updated_at = date('Y-m-d h:i:s');
        $expenseModel->updated_ip = $request->ip();
        // 2 fields added here pls check .!
        $expenseModel->payment_type = $request->input('payment_type');
        $expenseModel->card_number = $request->input('card_type');

        if($request->input('reambance_type') == "No"){
            $expenseModel->first_approval_status = "Approved";
            $expenseModel->first_approval_id = Auth::user()->id;
            $expenseModel->second_approval_status = "Approved";
            $expenseModel->second_approval_id = Auth::user()->id;
            $expenseModel->third_approval_status = "Approved";
            $expenseModel->third_approval_id = Auth::user()->id;
            $expenseModel->status = "Approved";
        }


        if ($expenseModel->save()) {
            // check Periodic Maintenance Kilometer
            if ($request->input('reambance_type') == "Yes") {
                $main_km = PeriodicMaintenanceKilometer::where('asset_id', $request->input('vehicle_type'))->latest('created_at')->first();
                if ($main_km) {
                    $start_reading_km = $main_km['periodic_maintenance_kilometer'];
                    $current_reading_km = $request->input('meter_reading');
                    $vehicle_main = Vehicle_Maintenance::where('asset_id', $request->input('vehicle_type'))->latest('created_at')->first();
                    // dd($vehicle_main);
                    if ($vehicle_main) {
                        $check_periodic_km = $current_reading_km - $start_reading_km;
                        // $check_periodic_km = 3100 - $start_reading_km;
                        if ($check_periodic_km > $vehicle_main['periodic_maintenance_km']) {
                            // notification fire
                            $this->notification_task->periodicMaintenanceNotify([Auth::user()->id]);
                        }
                    }
                }
            }

            // if reambance type no add periodic Maintenance Km
            if ($request->input('reambance_type') == "No") {
                PeriodicMaintenanceKilometer::insert([
                    'driver_expense_id' => $expenseModel->id,
                    'periodic_maintenance_kilometer' => $request->get('meter_reading'),
                    'asset_id' => $request->input('vehicle_type'),
                    'created_ip' => $request->ip(),
                    'updated_ip' => $request->ip(),
                ]);
            }
            return redirect()->route('admin.expense')->with('Expense successfully submitted.');
        } else {
            return redirect()->route('admin.add_expense')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function edit_expense($id) {
        /* if (Auth::user()->role != config('constants.DRIVER')) {
            return redirect()->route('admin.dashboard')->with('error', 'This module is only for drivers. You can not access it.');
        } */

        $driver_ids = $this->common_task->get_vehicle_asset_driver();
        if(!in_array(Auth::user()->id,$driver_ids)){
            return redirect()->route('admin.dashboard')->with('error', 'This module is only for drivers. You can not access it.');
        }

        $this->data['page_title'] = "Edit Driver Expense";
        $this->data['expense'] = Driver_expense::where(['id' => $id])->get()->first();

        if ($this->data['expense']->first_approval_status == "Approved" && $this->data['expense']->status == "Pending") {
            return redirect()->route('admin.expense')->with('error', 'Expense is in process so you can not edit it at this time.');
        }
        $this->data['asset_list'] = \App\Asset::where('asset_type', 'Vehicle Asset')->get();
        $this->data['users'] = User::select('name', 'id')->where('role', config('constants.ACCOUNT_ROLE'))->get()->toArray();
        return view('admin.driver_expense.edit_expense', $this->data);
    }

    public function update_expense(Request $request) {

        $validator_normal = Validator::make($request->all(), [
                    'vehicle_type' => 'required',
                    'amount' => 'required',
                    'meter_reading' => 'required',
                    'comment' => 'required',
                    'moniter_user_id' => 'required',
                    'fuel_price' => 'required',
        ]);

        if ($validator_normal->fails()) {
            return redirect()->route('admin.edit_expense', ['id' => $request->input('id')])->with('error', 'Please follow validation rules.');
        }

        //upload user Meter Reading Photo
        /* $meter_reading_photo = NULL;
          $expenseMeterPhoto = [];
          if ($request->hasFile('meter_reading_photo')) {
          $meter_reading = $request->file('meter_reading_photo');
          $file_path = $meter_reading->store('public/driver_expense');
          if ($file_path) {
          $meter_reading_photo = $file_path;
          }
          $expenseMeterPhoto = [
          'meter_reading_photo' => $meter_reading_photo,
          ];
          } */


        //upload user Bill Photo
        /*    $bill_photo = NULL;
          $expenseBillPhoto = [];
          if ($request->hasFile('bill_photo')) {
          $bill = $request->file('bill_photo');
          $file_path = $bill->store('public/driver_expense');
          if ($file_path) {
          $bill_photo = $file_path;
          }
          $expenseBillPhoto = [
          'bill_photo' => $bill_photo,
          ];
          } */

        $expenseModel = [
            'asset_id' => $request->input('vehicle_type'),
            'amount' => $request->input('amount'),
            'comment' => $request->input('comment'),
            'meter_reading' => $request->input('meter_reading'),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
            'status' => 'Pending',
            'first_approval_status' => 'Pending',
            'second_approval_status' => 'Pending',
            'third_approval_status' => 'Pending',
            'payment_type' => $request->input('payment_type'),
            'card_number' => $request->input('card_type')
        ];
        if ($request->input('reambance_type') == "No") {

            $expenseModel['first_approval_status'] = "Approved";
            $expenseModel['first_approval_id'] = Auth::user()->id;

            $expenseModel['second_approval_status'] = "Approved";
            $expenseModel['second_approval_id'] = Auth::user()->id;

            $expenseModel['third_approval_status'] = "Approved";
            $expenseModel['third_approval_id'] = Auth::user()->id;
            $expenseModel['status'] = "Approved";
        }else{
            $expenseModel['first_approval_status'] = "Pending";
            $expenseModel['second_approval_status'] = "Pending";
            $expenseModel['third_approval_status'] = "Pending";
            $expenseModel['status'] = "Pending";
        }

        //21-02-2020
        if ($request->hasFile('meter_reading_photo')) {
            $meter_reading = $request->file('meter_reading_photo');
            $original_file_name = explode('.', $meter_reading->getClientOriginalName());

            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);


            $file_path = $meter_reading->storeAs('public/expense_image', $new_file_name);
            if ($file_path) {
                $meter_reading_photo = $file_path;
            }
            $expenseModel['meter_reading_photo'] = $meter_reading_photo;
        }

        //21-02-2020
        if ($request->hasFile('bill_photo')) {

            $bill = $request->file('bill_photo');

            $original_file_name = explode('.', $bill->getClientOriginalName());

            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);


            $file_path = $bill->storeAs('public/expense_image', $new_file_name);
            if ($file_path) {
                $bill_photo = $file_path;
            }
            $expenseModel['bill_photo'] = $bill_photo;
        }

        // dd($expenseModel);
        $expense = Driver_expense::where('id', $request->input('id'))->update($expenseModel);

        if ($expense) {

            // check Periodic Maintenance Kilometer
            if ($request->input('reambance_type') == "Yes") {
                $main_km = PeriodicMaintenanceKilometer::where('asset_id', $request->input('vehicle_type'))->latest('created_at')->first();
                if ($main_km) {
                    $start_reading_km = $main_km['periodic_maintenance_kilometer'];
                    $current_reading_km = $request->input('meter_reading');
                    $vehicle_main = Vehicle_Maintenance::where('asset_id', $request->input('vehicle_type'))->latest('created_at')->first();
                    // dd($vehicle_main);
                    if ($vehicle_main) {
                        $check_periodic_km = $current_reading_km - $start_reading_km;
                        // $check_periodic_km = 3100 - $start_reading_km;
                        if ($check_periodic_km > $vehicle_main['periodic_maintenance_km']) {
                            // notification fire
                            $this->notification_task->periodicMaintenanceNotify([Auth::user()->id]);
                        }
                    }
                }
            }

            // if reambance type no add periodic Maintenance Km
            if ($request->input('reambance_type') == "No") {
                PeriodicMaintenanceKilometer::insert([
                    'driver_expense_id' => $request->input('id'),
                    'periodic_maintenance_kilometer' => $request->get('meter_reading'),
                    'asset_id' => $request->input('vehicle_type'),
                    'created_ip' => $request->ip(),
                    'updated_ip' => $request->ip(),
                ]);
            }

            return redirect()->route('admin.expense')->with('success', 'Expense successfully updated.');
        } else {
            return redirect()->route('admin.edit_expense')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function all_expense() {
        if (Auth::user()->role != config('constants.Admin') && Auth::user()->role != config('constants.ACCOUNT_ROLE') && Auth::user()->role != config('constants.SuperUser')) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You do not have permission to access this module.');
        }

        $this->data['page_title'] = "Driver Expense";

        $this->data['access_rule'] = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => 31])->get()->first();

        if (!empty($this->data['access_rule'])) {
            $this->data['access_rule'] = $this->data['access_rule']->access_level;
        } else {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied!');
        }
        $this->data['all_Expenses'] = DB::table('driver_expense')
                        ->join('asset', 'driver_expense.asset_id', '=', 'asset.id')
                        ->join('users', 'driver_expense.user_id', '=', 'users.id')
                        ->orderBy('driver_expense.id', 'DESC')
                        ->get(
                                ['driver_expense.*', 'vehicle_type', 'date_of_expense',
                                    'time_of_expense', 'amount', 'comment', 'comment',
                                    'driver_expense.fuel_price',
                                    'meter_reading_photo', 'bill_photo',
                                    'driver_expense.status as expense_status',
                                    'users.name as u_name',
                                    'asset.name as a_name', 'reject_note', 'asset.name', 'asset_1']
                        )->toArray();
        // dd($this->data['all_Expenses']);
        return view('admin.driver_expense.all_expense', $this->data);
    }

    public function get_all_expense_list() {
        if (Auth::user()->role != config('constants.ACCOUNT_ROLE')) {
            $datatable_fields = array('driver_expense.*' ,'users.name', 'driver_expense.fuel_type', 'asset.name as asset_name', 'asset.asset_1', 'date_of_expense', 'time_of_expense', 'amount','driver_expense.fuel_price','driver_expense.total_fuel_quality','driver_expense.meter_reading', 'comment', 'bill_photo', 'meter_reading_photo',
                'first_approval_status', 'second_approval_status', 'third_approval_status', 'driver_expense.first_approval_datetime','driver_expense.second_approval_datetime','driver_expense.third_approval_datetime');
        } elseif (Auth::user()->role != config('constants.Admin')) {
            $datatable_fields = array('driver_expense.*','users.name', 'driver_expense.fuel_type', 'vehicle_type', 'date_of_expense', 'time_of_expense', 'amount','driver_expense.fuel_price','driver_expense.total_fuel_quality','driver_expense.meter_reading', 'comment', 'bill_photo', 'meter_reading_photo',
                'first_approval_status', 'second_approval_status', 'third_approval_status', 'driver_expense.first_approval_datetime','driver_expense.second_approval_datetime','driver_expense.third_approval_datetime');
        } else {
            $datatable_fields = array('driver_expense.*','users.name', 'driver_expense.fuel_type', 'vehicle_type', 'date_of_expense', 'time_of_expense', 'amount','driver_expense.fuel_price','driver_expense.total_fuel_quality','driver_expense.meter_reading', 'comment', 'bill_photo', 'meter_reading_photo',
                'first_approval_status', 'second_approval_status', 'third_approval_status', 'driver_expense.first_approval_datetime','driver_expense.second_approval_datetime','driver_expense.third_approval_datetime');
        }
        $request = Input::all();

        $join_str[0]['table'] = 'users';
        $join_str[0]['join_table_id'] = 'users.id';
        $join_str[0]['from_table_id'] = 'driver_expense.user_id';
        $join_str[0]['join_type'] = '';
        $join_str[1]['table'] = 'asset';
        $join_str[1]['join_table_id'] = 'asset.id';
        $join_str[1]['from_table_id'] = 'driver_expense.asset_id';
        $join_str[1]['join_type'] = '';

        $getfiled = array('driver_expense.*','driver_expense.id','driver_expense.fuel_price','driver_expense.total_fuel_quality','driver_expense.meter_reading', 'driver_expense.bill_photo', 'driver_expense.meter_reading_photo', 'users.name', 'users.role', 'driver_expense.fuel_type', 'asset.name as asset_name', 'asset.asset_1', 'date_of_expense', 'time_of_expense', 'amount', 'comment', 'driver_expense.status', 'first_approval_status', 'second_approval_status', 'third_approval_status');
        $table = "driver_expense";

        //first approval from hr and then one more senior and then final to MD sir
        //check logged in user is hr
        if (Auth::user()->role == config('constants.ACCOUNT_ROLE')) {
            //get all the first first_approval_status pending
            $conditions_array['driver_expense.first_approval_status'] = "Pending";
        } else if (Auth::user()->role == config('constants.Admin')) {
            $conditions_array['driver_expense.first_approval_status'] = "Approved";
            $conditions_array['driver_expense.second_approval_status'] = "Pending";
        } else if (Auth::user()->role == config('constants.SuperUser')) {
            //user with access permission 5 only.
            $conditions_array['driver_expense.first_approval_status'] = "Approved";
            $conditions_array['driver_expense.second_approval_status'] = "Approved";
            $conditions_array['driver_expense.third_approval_status'] = "Pending";
        }

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);

        die();
    }

    public function approve_expense($id, Request $request) {

        $expenseModel = Driver_expense::find($id);
        $userDetail = User::where(['id' => $expenseModel->user_id])->get(['email', 'name'])->first()->toArray();
        $update_arr = [];
        $final_confirm = 0;
        //check expense approval-time, first, second or third

        if (Auth::user()->role == config('constants.ACCOUNT_ROLE')) {
            $update_arr = [
                'first_approval_status' => 'Approved',
                'first_approval_id' => Auth::user()->id,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id,
                'first_approval_datetime' => date('Y-m-d H:i:s'),
                // 'second_approval_datetime' => date('Y-m-d H:i:s'),
                // 'third_approval_datetime' => date('Y-m-d H:i:s'),
            ];

            $SuperUser_list = \App\User::where('role', config('constants.SuperUser'))->get(['id'])->pluck('id');
            $this->notification_task->driverExepenseFirstApprovalNotify([$SuperUser_list]);
        } elseif (Auth::user()->role == config('constants.Admin')) {
            $update_arr = [
                'second_approval_status' => 'Approved',
                'second_approval_id' => Auth::user()->id,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id,
                // 'first_approval_datetime' => date('Y-m-d H:i:s'),
                'second_approval_datetime' => date('Y-m-d H:i:s'),
                // 'third_approval_datetime' => date('Y-m-d H:i:s'),
            ];

            //get accountant user list
            $accountant_list = \App\User::where('role', config('constants.SuperUser'))->get(['id'])->pluck('id');
            $this->notification_task->driverExepenseSecondApprovalNotify([$accountant_list]);
        } elseif (Auth::user()->role == config('constants.SuperUser')) {
            $update_arr = [
                'third_approval_status' => 'Approved',
                'third_approval_id' => Auth::user()->id,
                'third_approval_datetime' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id,
                'status' => "Approved"
            ];

            $this->notification_task->driverExepenseThirdApprovalNotify([$expenseModel->user_id]);
        } else {
            return redirect()->route('admin.all_expense')->with('error', 'Error Occurred. Try Again!');
        }

        if (Driver_expense::where('id', $id)->update($update_arr)) {
            return redirect()->route('admin.all_expense')->with('success', 'Driver expense successfully Approved.');
        }
        return redirect()->route('admin.all_expense')->with('error', 'Error during operation. Try again!');
    }

    public function reject_expense($id) {
        // this will be access with full view access 5 only
        $this->data['page_title'] = "expense";

        $this->data['id'] = $id;
        return view('admin.driver_expense.reject_expense', $this->data);
    }

    public function reject_update_expense(Request $request) {

        $validator_normal = Validator::make($request->all(), [
                    'reject_reason' => 'required',
                    'id' => 'required'
        ]);

        if ($validator_normal->fails()) {
            return redirect()->route('admin.all_expense')->with('error', 'Please follow validation rules.');
        }

        $expenseModel = Driver_expense::find($request->input('id'));

        if (Auth::user()->role == config('constants.ACCOUNT_ROLE')) {
            $update_arr = [
                'first_approval_status' => 'Rejected',
                'first_approval_id' => Auth::user()->id,
                'status' => 'Rejected',
                'first_approval_datetime' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id,
                'reject_note' => $request->input('reject_note')
            ];
        } elseif (Auth::user()->role == config('constants.Admin')) {
            $update_arr = [
                'second_approval_status' => 'Rejected',
                'second_approval_id' => Auth::user()->id,
                'status' => 'Rejected',
                'second_approval_datetime' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id,
                'reject_note' => $request->input('reject_note')
            ];
        } elseif (Auth::user()->role == config('constants.SuperUser')) {
            $update_arr = [
                'third_approval_status' => 'Rejected',
                'third_approval_id' => Auth::user()->id,
                'status' => 'Rejected',
                'third_approval_datetime' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id,
                'reject_note' => $request->input('reject_note')
            ];
        } else {
            return redirect()->route('admin.all_expense')->with('error', 'Error Occurred. Try Again!');
        }

        Driver_expense::where('id', $request->input('id'))->update($update_arr);

        return redirect()->route('admin.all_expense')->with('success', 'Expense successfully rejected.');
    }

    public function delete_driver_expense($id) {
        $check_result = Permissions::checkPermission(31, 4);

        /* if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        } */

        $driver_ids = $this->common_task->get_vehicle_asset_driver();
        if(!in_array(Auth::user()->id,$driver_ids)){
            return redirect()->route('admin.dashboard')->with('error', 'This module is only for drivers. You can not access it.');
        }

        $expense_detail = Driver_expense::where('id', $id)->where('user_id', \Illuminate\Support\Facades\Auth::user()->id)->get();

        if ($expense_detail->count() == 0) {
            return redirect()->route('admin.expense')->with('error', 'Error occurred. Try Again!');
        }

        if ($expense_detail[0]->first_approval_status == 'Approved') {
            return redirect()->route('admin.expense')->with('error', 'Expense is in approval process. You can not delete it now.');
        }

        if ($expense_detail[0]->status == 'Rejected') {
            return redirect()->route('admin.expense')->with('error', 'Expense is already rejected. You can not delete it now.');
        }

        if (Driver_expense::where('id', $id)->delete()) {
            return redirect()->route('admin.expense')->with('success', 'Expense successfully deleted.');
        }
        return redirect()->route('admin.expense')->with('error', 'Error during operation. Try again!');
    }

    public function get_assign_asset() {
        if (!empty($_REQUEST)) {
            $expense_detail = Driver_expense::select('meter_reading')->where('asset_id', $_REQUEST['asset_id'])->orderBy('id', 'desc')->get()->take(1)->toArray();
            if (!empty($expense_detail[0]['meter_reading'])) {
                echo json_encode(['success' => 1, 'data' => $expense_detail[0]['meter_reading'], 'msg' => 'success']);
            } else {
                echo json_encode(['success' => 0, 'data' => [], 'msg' => 'success']);
            }
        }
    }

}