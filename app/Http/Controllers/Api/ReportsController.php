<?php

namespace App\Http\Controllers\Api;

use App\Asset;
use App\AssetAccess;
use App\Driver_expense;
use App\AttendanceMaster;
use App\BankPaymentApproval;
use App\CashApproval;
use App\ChequeRegister;
use App\RtgsRegister;
use App\Companies;
use App\Employee_expense;
use App\Expense_category;
use App\Projects;
use App\Vehicle_trip;
use App\BudgetSheetApproval;
use App\Employees;
use App\HotelBooking;
use App\LoanTransaction;
use App\OnlinePaymentApproval;
use App\LetterHeadRegister;
use App\Travel;
use App\Payroll;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Lib\CommonTask;
use Illuminate\Support\Facades\Validator;
use App\Lib\NotificationTask;
use Illuminate\Support\Facades\Config;
use App\User;
use Illuminate\Support\Facades\App;
use App\Vehicle_Maintenance;
use App\VoucherNumberRegister;

class ReportsController extends Controller {

    private $page_limit = 20;
    public $common_task;
    public $notification_task;

    public function __construct() {
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();
    }

    public function leave_report(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'page_number' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $response_data = [];
        $offset = ($request_data['page_number'] - 1) * $this->page_limit;
        $logged_in_userdata = User::where('id', $request_data['user_id'])->get();
        $partial_query = \App\Leaves::where(function ($query) use ($request_data) {

                    if (isset($request_data['start_date']) && isset($request_data['end_date'])) {
                        if (strtotime($request_data['start_date']) != strtotime($request_data['end_date'])) {
                            $query->where([['start_date', '>=', $request_data['start_date']], ['start_date', '<=', $request_data['end_date']]]);
                            $query->orWhere([['end_date', '>=', $request_data['start_date']], ['end_date', '<=', $request_data['end_date']]]);
                        } else {
                            $query->where([['start_date', '<=', $request_data['start_date']], ['end_date', '>=', $request_data['start_date']]]);
                        }
                    }
                });
        if (isset($request_data['leave_status'])) {
            $partial_query->where('leave_status', $request_data['leave_status']);
        }

        //for super user show all and to other show only his data
        if ($logged_in_userdata[0]->role != config('constants.SuperUser')) {
            $partial_query->where('leaves.user_id', $request_data['user_id']);
        }

        $leave_result = $partial_query->join('users', 'users.id', '=', 'leaves.user_id')
                ->join('users as wr_user', 'leaves.assign_work_user_id', '=', 'wr_user.id')
                ->join('leave_category', 'leave_category.id', '=', 'leaves.leave_category_id')
                ->orderBy('id', 'DESC')
                ->offset($offset)
                ->limit($this->page_limit)
                ->get([
            'leaves.*', 'users.name', 'users.profile_image', 'leaves.first_approval_status as hr_status',
            'leaves.third_approval_status as admin_status', 'wr_user.name as work_assigned_user_name', 'leave_category.name as category_name'
        ]);
        if ($leave_result->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }
        foreach ($leave_result as $key => $leave) {
            if ($leave->profile_image) {
                $leave_result[$key]->profile_image = asset('storage/' . str_replace('public/', '', $leave->profile_image));
            } else {
                $leave_result[$key]->profile_image = "";
            }
            $approval_count = 0;
            if ($leave->hr_status == "Approved") {
                $approval_count = $approval_count + 1;
            }
            if ($leave->admin_status == "Approved") {
                $approval_count = $approval_count + 1;
            }
            $leave->approval_percent = ($approval_count * 100) / 2;
        }
        return response()->json(['status' => true, 'msg' => "Record Found", 'data' => $leave_result]);
    }

    public function attendance_report(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'page_number' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];


        $offset = ($request_data['page_number'] - 1) * $this->page_limit;

        $logged_in_userdata = User::where('id', $request_data['user_id'])->get();

        $attendace_records = AttendanceMaster::with(['attendance', 'user'])
                ->join('attendance_detail', 'attendance_detail.attendance_master_id', '=', 'attendance_master.id')
                ->where(function ($query) use ($request_data, $logged_in_userdata) {
                    if (isset($request_data['start_date']) && isset($request_data['end_date'])) {
                        $query->whereDate('date', '>=', $request_data['start_date'])
                        ->whereDate('date', '<=', $request_data['end_date']);
                    }
                    //for super user show all and to other show only his data
                    if ($logged_in_userdata[0]->role != config('constants.SuperUser')) {
                        $query->where('user_id', $request_data['user_id']);
                    }
                })
                ->offset($offset)
                ->limit($this->page_limit)
                ->orderBy('first_in', 'DESC')
                ->groupBy('attendance_detail.attendance_master_id')
                ->get(['attendance_master.*']);

        if ($attendace_records->count() == 0) {
            return response()->json([
                        'status' => false,
                        'msg' => config('errors.no_record.msg'),
                        'data' => [],
                        'error' => config('errors.no_record.code')
            ]);
        }


        $attendace_records = $attendace_records->toArray();

        foreach ($attendace_records as $key => $attendance) {

            if ($attendance['user']['profile_image']) {
                $attendace_records[$key]['user']['profile_image'] = asset('storage/' . str_replace('public/', '', $attendance['user']['profile_image']));
            } else {
                $attendace_records[$key]['user']['profile_image'] = "";
            }
        }

        return response()->json([
                    'status' => true,
                    'msg' => "Record found.",
                    'data' => $attendace_records
        ]);
    }

    public function driver_expense_report(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'page_number' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $response_data = [];

        $offset = ($request_data['page_number'] - 1) * $this->page_limit;

        $logged_in_userdata = User::where('id', $request_data['user_id'])->get();

        $partial_query = Driver_expense::with('assets')
                ->where(function ($query) use ($request_data) {
            if (isset($request_data['start_date']) && isset($request_data['end_date'])) {
                $query->whereDate('date_of_expense', '>=', $request_data['start_date'])
                ->whereDate('date_of_expense', '<=', $request_data['end_date']);
            }
        });

        if (isset($request_data['status'])) {
            $partial_query->where('driver_expense.status', $request_data['status']);
        }

        //for super user show all and to other show only his data
        if ($logged_in_userdata[0]->role != config('constants.SuperUser')) {
            $partial_query->where('driver_expense.user_id', $request_data['user_id']);
        }

        $driver_expense_result = $partial_query->join('users AS A', 'A.id', '=', 'driver_expense.user_id')
                ->join('users AS B', 'B.id', '=', 'driver_expense.moniter_user_id')
                ->leftjoin('users AS Accountant', 'driver_expense.first_approval_id', '=', 'Accountant.id')
                ->leftjoin('users AS Admin', 'driver_expense.second_approval_id', '=', 'Admin.id')
                ->leftjoin('users AS Superuser', 'driver_expense.third_approval_id', '=', 'Superuser.id')
                ->offset($offset)
                ->limit($this->page_limit)
                ->get([
            'driver_expense.*', 'A.name AS user_name', 'A.profile_image', 'B.name AS moniter_user_name',
            'driver_expense.first_approval_status AS accountant_approval', 'driver_expense.second_approval_status AS admin_approval', 'driver_expense.third_approval_status AS super_admin_approval'
            ,'Accountant.name AS first_approval_user','Admin.name AS second_approval_user', 'Superuser.name AS third_approval_user'
        ]);

        if ($driver_expense_result->count() == 0) {
            return response()->json([
                        'status' => false,
                        'msg' => config('errors.no_record.msg'),
                        'data' => [],
                        'error' => config('errors.no_record.code')
            ]);
        }

        foreach ($driver_expense_result as $key => $result) {
            if ($result->profile_image) {

                $driver_expense_result[$key]->profile_image = asset('storage/' . str_replace('public/', '', $result->profile_image));
            } else {

                $driver_expense_result[$key]->profile_image = "";
            }
            if ($result->meter_reading_photo) {

                $driver_expense_result[$key]->meter_reading_photo = asset('storage/' . str_replace('public/', '', $result->meter_reading_photo));
            } else {
                $driver_expense_result[$key]->meter_reading_photo = "";
            }

            if ($result->bill_photo) {

                $driver_expense_result[$key]->bill_photo = asset('storage/' . str_replace('public/', '', $result->bill_photo));
            } else {
                $driver_expense_result[$key]->bill_photo = "";
            }

            $approval_count = 0;
            if ($result->accountant_approval == "Approved") {
                $approval_count = $approval_count + 1;
            }
            if ($result->admin_approval == "Approved") {
                $approval_count = $approval_count + 1;
            }
            if ($result->super_admin_approval == "Approved") {
                $approval_count = $approval_count + 1;
            }
            $result->approval_percent = ($approval_count * 100) / 3;
        }


        return response()->json([
                    'status' => true,
                    'msg' => "Record found.",
                    'data' => $driver_expense_result
        ]);
    }

    public function regular_expense_report(Request $request) {  //change
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'page_number' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $response_data = [];

        $offset = ($request_data['page_number'] - 1) * $this->page_limit;

        $logged_in_userdata = User::where('id', $request_data['user_id'])->get();


        $partial_query = Employee_expense::where(function ($query) use ($request_data) {
                    if (isset($request_data['start_date']) && isset($request_data['end_date'])) {
                        $query->whereDate('expense_date', '>=', $request_data['start_date'])
                                ->whereDate('expense_date', '<=', $request_data['end_date']);
                    }
                });
        if (isset($request_data['status'])) {
            $partial_query->where('employee_expense.status', $request_data['status']);
        }

        //for super user show all and to other show only his data
        if ($logged_in_userdata[0]->role != config('constants.SuperUser')) {
            $partial_query->where('employee_expense.user_id', $request_data['user_id']);
        }

        $regular_expense_result = $partial_query->join('users', 'employee_expense.user_id', '=', 'users.id')
                ->join('expense_category', 'employee_expense.expense_category', '=', 'expense_category.id')
                ->leftjoin('company', 'employee_expense.company_id', '=', 'company.id')
                ->leftjoin('project', 'employee_expense.project_id', '=', 'project.id')
                ->leftJoin('clients', 'clients.id', '=', 'employee_expense.client_id')
                ->leftjoin('rtgs_register','rtgs_register.id','=','employee_expense.rtgs_number')
                ->leftJoin('project_sites', 'project_sites.id', '=', 'employee_expense.project_site_id')
                ->leftJoin('bank', 'bank.id', '=', 'employee_expense.bank_id')
                ->leftJoin('cheque_register', 'cheque_register.id', '=', 'employee_expense.cheque_number')
                ->orderBy('employee_expense.id', 'DESC')
                ->offset($offset)
                ->limit($this->page_limit)
                ->get(['clients.client_name','clients.location', 'project_sites.site_name','rtgs_register.rtgs_no',
            'employee_expense.*', 'users.name', 'users.profile_image', 'expense_category.category_name', 'company.company_name', 'project.project_name',
            'employee_expense.first_approval_status AS hr_status', 'employee_expense.second_approval_status AS assistent_status', 'employee_expense.third_approval_status AS admin_status', 'employee_expense.forth_approval_status AS super_admin_status', 'employee_expense.fifth_approval_status AS accountant_status',
            'cheque_register.ch_no','bank.bank_name','bank.ac_number'
        ]);


        if ($regular_expense_result->count() == 0) {
            return response()->json([
                        'status' => false,
                        'msg' => config('errors.no_record.msg'),
                        'data' => [],
                        'error' => config('errors.no_record.code')
            ]);
        }

        foreach ($regular_expense_result as $key => $result) {


            if($result->client_name){


                if($result->client_name == "Other Client"){
                    $regular_expense_result[$key]->client_name = $result->client_name;
                }else{
                    $regular_expense_result[$key]->client_name = $result->client_name. "(" . $result->location . ")";
                }

            }

            if ($result->profile_image) {

                $regular_expense_result[$key]->profile_image = asset('storage/' . str_replace('public/', '', $result->profile_image));
            } else {

                $regular_expense_result[$key]->profile_image = "";
            }

            if ($result->expense_image) {

                $regular_expense_result[$key]->expense_image = asset('storage/' . str_replace('public/', '', $result->expense_image));
            } else {
                $regular_expense_result[$key]->expense_image = "";
            }


            if ($result->voucher_image) {

                $regular_expense_result[$key]->voucher_image = asset('storage/' . str_replace('public/', '', $result->voucher_image));
            } else {
                $regular_expense_result[$key]->voucher_image = "";
            }


            $approval_count = 0;
            if ($result->hr_status == "Approved") {
                $approval_count = $approval_count + 1;
            }
            if ($result->assistent_status == "Approved") {
                $approval_count = $approval_count + 1;
            }
            if ($result->admin_status == "Approved") {
                $approval_count = $approval_count + 1;
            }
            if ($result->super_admin_status == "Approved") {
                $approval_count = $approval_count + 1;
            }
            if ($result->accountant_status == "Approved") {
                $approval_count = $approval_count + 1;
            }
            $result->approval_percent = ($approval_count * 100) / 5;
        }
        return response()->json([
                    'status' => true,
                    'msg' => "Record found.",
                    'data' => $regular_expense_result
        ]);
    }

    public function driver_trip_report(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'page_number' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $response_data = [];

        $offset = ($request_data['page_number'] - 1) * $this->page_limit;

        $logged_in_userdata = User::where('id', $request_data['user_id'])->get();

        $trip_records = Vehicle_trip::with('assets')
                ->join('users AS A', 'A.id', '=', 'vehicle_trip.user_id')
                ->join('users AS B', 'B.id', '=', 'vehicle_trip.trip_user_id')
                ->where(function ($query) use ($request_data, $logged_in_userdata) {
                    if (isset($request_data['start_date']) && isset($request_data['end_date'])) {
                        // $query->whereDate('opening_time', '>=', $request_data['start_date'])
                        //     ->whereDate('opening_time', '<=', $request_data['end_date']);
                        // $query->orWhere('closing_time', '>=', $request_data['start_date']);
                        $query->where([['opening_time', '>=', $request_data['start_date']], ['opening_time', '<=', $request_data['end_date'] . ' 23:59:00']]);
                        $query->orWhere([['closing_time', '>=', $request_data['start_date']], ['closing_time', '<=', $request_data['end_date'] . ' 23:59:00']]);
                    }
                    //for super user show all and to other show only his data
                    if ($logged_in_userdata[0]->role != config('constants.SuperUser')) {
                        $query->where('vehicle_trip.user_id', $request_data['user_id']);
                    }
                })
                ->offset($offset)
                ->limit($this->page_limit)
                ->orderBy('vehicle_trip.id', 'DESC')
                ->get(['vehicle_trip.*', 'A.name AS user_name', 'A.profile_image', 'B.name AS moniter_user_name']);


        if ($trip_records->count() == 0) {
            return response()->json([
                        'status' => false,
                        'msg' => config('errors.no_record.msg'),
                        'data' => [],
                        'error' => config('errors.no_record.code')
            ]);
        }

        foreach ($trip_records as $key => $result) {
            if ($result->profile_image) {


                $trip_records[$key]->profile_image = asset('storage/' . str_replace('public/', '', $result->profile_image));
            } else {

                $trip_records[$key]->profile_image = "";
            }

            if ($result->opening_meter_reading_image) {

                $trip_records[$key]->opening_meter_reading_image = asset('storage/' . str_replace('public/', '', $result->opening_meter_reading_image));
            } else {

                $trip_records[$key]->opening_meter_reading_image = "";
            }

            if ($result->closing_meter_reading_image) {

                $trip_records[$key]->closing_meter_reading_image = asset('storage/' . str_replace('public/', '', $result->closing_meter_reading_image));
            } else {

                $trip_records[$key]->closing_meter_reading_image = "";
            }
        }


        return response()->json([
                    'status' => true,
                    'msg' => "Record found.",
                    'data' => $trip_records
        ]);
    }

    public function assets_report(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'page_number' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $response_data = [];

        $offset = ($request_data['page_number'] - 1) * $this->page_limit;

        $logged_in_userdata = User::where('id', $request_data['user_id'])->get();

        $assets_result = AssetAccess::join('asset', 'asset_access.asset_id', '=', 'asset.id')
                ->join('users AS A', 'A.id', '=', 'asset_access.asset_access_user_id')
                ->join('users AS B', 'B.id', '=', 'asset_access.assigner_user_id')
                ->join('company', 'company.id', '=', 'asset.company_id')
                ->where('asset_access.is_allocate', '=', 1)
                ->where(function ($query) use ($logged_in_userdata, $request_data) {
                    //for super user show all and to other show only his data
                    if ($logged_in_userdata[0]->role != config('constants.SuperUser')) {
                        $query->where('asset_access.asset_access_user_id', $request_data['user_id']);
                    }
                })
                ->offset($offset)
                ->limit($this->page_limit)
                ->get(['asset.name As asset_name', 'asset.asset_type', 'asset.fuel_type', 'company.company_name', 'asset.asset_1', 'asset.asset_2', 'asset_access.*', 'A.name AS access_user_name', 'A.profile_image', 'B.name As assigner_name']);


        if ($assets_result->count() == 0) {
            return response()->json([
                        'status' => false,
                        'msg' => "No Record found.",
                        'data' => []
            ]);
        }

        foreach ($assets_result as $key => $result) {
            if ($result->profile_image) {


                $assets_result[$key]->profile_image = asset('storage/' . str_replace('public/', '', $result->profile_image));
            } else {

                $assets_result[$key]->profile_image = "";
            }
        }

        return response()->json([
                    'status' => true,
                    'msg' => "Record found.",
                    'data' => $assets_result
        ]);
    }

    public function cash_approval_report(Request $request) {      //change
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'page_number' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $response_data = [];

        $offset = ($request_data['page_number'] - 1) * $this->page_limit;

        $logged_in_userdata = User::where('id', $request_data['user_id'])->get();

        $partial_query = CashApproval::where(function ($query) use ($request_data) {
                    if (isset($request_data['start_date']) && isset($request_data['end_date'])) {
                        $query->whereDate('cash_approval.created_at', '>=', $request_data['start_date'])
                                ->whereDate('cash_approval.created_at', '<=', $request_data['end_date']);
                    }
                });
        if (isset($request_data['status'])) {
            $partial_query->where('cash_approval.status', $request_data['status']);
        }

        //for super user show all and to other show only his data
        if ($logged_in_userdata[0]->role != config('constants.SuperUser')) {
            $partial_query->where('cash_approval.user_id', $request_data['user_id']);
        }

        $cash_approval_result = $partial_query->join('users', 'users.id', '=', 'cash_approval.user_id')
                ->join('company', 'company.id', '=', 'cash_approval.company_id')
                ->join('project', 'project.id', '=', 'cash_approval.project_id')
                ->join('vendor', 'vendor.id', '=', 'cash_approval.vendor_id')
                ->leftJoin('voucher_number_register', 'voucher_number_register.id', '=', 'cash_approval.voucher_no')
                ->leftJoin('clients', 'clients.id', '=', 'cash_approval.client_id')
                ->leftJoin('project_sites', 'project_sites.id', '=', 'cash_approval.project_site_id')
                ->leftJoin('users as request_user', 'request_user.id', '=', 'cash_approval.requested_by')
                ->leftJoin('users as expence_done', 'expence_done.id', '=', 'cash_approval.expence_done_by')
                ->offset($offset)
                ->limit($this->page_limit)
                ->get(['clients.client_name','voucher_number_register.voucher_ref_no','clients.location', 'project_sites.site_name',
            'cash_approval.*', 'company.company_name', 'project.project_name', 'users.name AS user_name', 'users.profile_image', 'cash_approval.first_approval_status as accountant_approval_status',
            'cash_approval.second_approval_status as super_user_approval_status', 'vendor.vendor_name', 'vendor.gst_number',
            'vendor.pan_card_number', 'request_user.name as requested_by_name', 'expence_done.name as expence_done_name'
        ]);

        if ($cash_approval_result->count() == 0) {
            return response()->json([
                        'status' => false,
                        'msg' => config('errors.no_record.msg'),
                        'data' => [],
                        'error' => config('errors.no_record.code')
            ]);
        }

        foreach ($cash_approval_result as $key => $cash_approval) {
            $payment_file_images = [];

            $cash_approval_result[$key]->voucher_number =  $this->get_voucher_number_data($cash_approval->voucher_no);

            if($cash_approval->client_name == "Other Client"){
                $cash_approval_result[$key]->client_name = $cash_approval->client_name;
            }else{
                $cash_approval_result[$key]->client_name = $cash_approval->client_name. "(" . $cash_approval->location . ")";
            }

            if ($cash_approval->profile_image) {

                $cash_approval_result[$key]->profile_image = asset('storage/' . str_replace('public/', '', $cash_approval->profile_image));
            } else {

                $cash_approval_result[$key]->profile_image = "";
            }

            if ($cash_approval->payment_file) {

                // $cash_approval_result[$key]->payment_file = asset('storage/' . str_replace('public/', '', $cash_approval->payment_file));
                $images_arr = explode(',', $cash_approval->payment_file);
                foreach($images_arr as $key1 => $value1){
                    $payment_file_images[$key1] = asset('storage/' . str_replace('public/', '', $value1));
                }
                $cash_approval_result[$key]->payment_file = $payment_file_images;
            } else {
                $cash_approval_result[$key]->payment_file = "";
            }


            $approval_count = 0;
            if ($cash_approval->accountant_approval_status == "Approved") {
                $approval_count = $approval_count + 1;
            }
            if ($cash_approval->second_approval_status == "Approved") {
                $approval_count = $approval_count + 1;
            }
            if ($cash_approval->super_user_approval_status == "Approved") {
                $approval_count = $approval_count + 1;
            }

            $cash_approval->approval_percent = ($approval_count * 100) / 3;
        }

        return response()->json([
                    'status' => true,
                    'msg' => "Record found.",
                    'data' => $cash_approval_result
        ]);
    }

    public function get_voucher_number_data($id)
    {
        $ids = explode(',', $id);
        $voucher = VoucherNumberRegister::whereIn('id', $ids)->pluck('voucher_no')->toArray();
        if ($voucher) {
            return implode(',', $voucher);
        } else {
            return "";
        }
    }

    public function bank_payment_approval_report(Request $request) {   //change
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'page_number' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $response_data = [];

        $offset = ($request_data['page_number'] - 1) * $this->page_limit;

        $logged_in_userdata = User::where('id', $request_data['user_id'])->get();

        $partial_query = BankPaymentApproval::where(function ($query) use ($request_data) {
                    if (isset($request_data['start_date']) && isset($request_data['end_date'])) {

                        $query->whereDate('bank_payment_approval.created_at', '>=', $request_data['start_date'])
                                ->whereDate('bank_payment_approval.created_at', '<=', $request_data['end_date']);
                    }
                });
        if (isset($request_data['status'])) {
            $partial_query->where('bank_payment_approval.status', $request_data['status']);
        }
        //for super user show all and to other show only his data
        if ($logged_in_userdata[0]->role != config('constants.SuperUser')) {
            $partial_query->where('bank_payment_approval.user_id', $request_data['user_id']);
        }
        $bank_payment_approval_result = $partial_query->with(['paymentFiles'])->join('users', 'users.id', '=', 'bank_payment_approval.user_id')
                ->join('company', 'company.id', '=', 'bank_payment_approval.company_id')
                ->join('project', 'project.id', '=', 'bank_payment_approval.project_id')
                ->join('vendor', 'vendor.id', '=', 'bank_payment_approval.vendor_id')
                ->join('bank', 'bank.id', '=', 'bank_payment_approval.bank_id')
                ->leftJoin('clients', 'clients.id', '=', 'bank_payment_approval.client_id')
                ->leftjoin('rtgs_register','rtgs_register.id','=','bank_payment_approval.rtgs_number')
                ->leftJoin('project_sites', 'project_sites.id', '=', 'bank_payment_approval.project_site_id')
                ->leftJoin('cheque_register', 'cheque_register.id', '=', 'bank_payment_approval.cheque_number')
                ->leftJoin('users as acc_users', 'acc_users.id', '=', 'bank_payment_approval.first_approval_id')
                ->leftJoin('users as admin_users', 'admin_users.id', '=', 'bank_payment_approval.second_approval_id')
                ->leftJoin('users as superadmin_users', 'superadmin_users.id', '=', 'bank_payment_approval.third_approval_id')
                ->offset($offset)
                ->limit($this->page_limit)
                ->get(['clients.client_name','clients.location', 'project_sites.site_name','rtgs_register.rtgs_no',
            'bank_payment_approval.*', 'company.company_name', 'project.project_name', 'users.name AS user_name', 'users.profile_image', 'bank.bank_name', 'bank.ac_number', 'bank_payment_approval.first_approval_status as accountant_approval_status',
            'bank_payment_approval.third_approval_status as super_user_approval_status', 'vendor.vendor_name', 'vendor.gst_number', 'vendor.pan_card_number', 'cheque_register.check_ref_no', 'cheque_register.issue_date', 'cheque_register.ch_no', 'cheque_register.amount',
                    'acc_users.name as acc_user_name','admin_users.name as admin_user_name','superadmin_users.name as superadmin_user_name','bank_payment_approval.first_approval_remark as account_approval_note'
                        ,'bank_payment_approval.second_approval_remark as admin_approval_note'
                        ,'bank_payment_approval.third_approval_remark as superadmin_approval_note'
        ]);

        if ($bank_payment_approval_result->count() == 0) {
            return response()->json([
                        'status' => false,
                        'msg' => config('errors.no_record.msg'),
                        'data' => [],
                        'error' => config('errors.no_record.code')
            ]);
        }

        foreach ($bank_payment_approval_result as $key => $bank_payment) {

            if($bank_payment->client_name == "Other Client"){
                $bank_payment_approval_result[$key]->client_name = $bank_payment->client_name;
            }else{
                $bank_payment_approval_result[$key]->client_name = $bank_payment->client_name. "(" . $bank_payment->location . ")";
            }

            if ($bank_payment->profile_image) {

                $bank_payment_approval_result[$key]->profile_image = asset('storage/' . str_replace('public/', '', $bank_payment->profile_image));
            } else {

                $bank_payment_approval_result[$key]->profile_image = "";
            }

            if ($bank_payment->payment_file) {

                $bank_payment_approval_result[$key]->payment_file = asset('storage/' . str_replace('public/', '', $bank_payment->payment_file));
            } else {

                $bank_payment_approval_result[$key]->payment_file = "";
            }
            if ($bank_payment->invoice_file) {

                $bank_payment_approval_result[$key]->invoice_file = asset('storage/' . str_replace('public/', '', $bank_payment->invoice_file));
            } else {

                $bank_payment_approval_result[$key]->invoice_file = "";
            }


            $approval_count = 0;
            if ($bank_payment->accountant_approval_status == "Approved") {
                $approval_count = $approval_count + 1;
            }
            if ($bank_payment->second_approval_status == "Approved") {
                $approval_count = $approval_count + 1;
            }
            if ($bank_payment->super_user_approval_status == "Approved") {
                $approval_count = $approval_count + 1;
            }

            $bank_payment->approval_percent = ($approval_count * 100) / 3;

            foreach($bank_payment->paymentFiles as $key=>$mul_payment_file){

                $bank_payment->paymentFiles[$key]->bank_payment_file=asset('storage/' . str_replace('public/', '', $mul_payment_file->bank_payment_file));
            }

        }

        return response()->json([
                    'status' => true,
                    'msg' => "Record found.",
                    'data' => $bank_payment_approval_result
        ]);
    }

    public function budget_sheet_weeks_arr(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'year' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();

        $response_data = [];

        for ($i = 1; $i <= 52; $i++) {

            $date_wise_weeks = 'Week- ' . $i . ' (' . $this->common_task->getWeekStartAndEndDate($i, $request_data['year']) . ')';

            $response_data[]['week_range'] = $date_wise_weeks;
            $response_data[$i - 1]['week_no'] = $i;
        }

        if (empty($response_data)) {
            return response()->json([
                        'status' => false,
                        'msg' => config('errors.no_record.msg'),
                        'data' => [],
                        'error' => config('errors.no_record.code')
            ]);
        }

        return response()->json([
                    'status' => true,
                    'msg' => "Weeks array.",
                    'data' => $response_data
        ]);
    }

    public function budget_sheet_approvals_report(Request $request) {  //change
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'year' => 'required',
                    'week' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $response_data = [];

        $logged_in_userdata = User::where('id', $request_data['user_id'])->get();

        $budget_sheet_approval_result = BudgetSheetApproval::join('users', 'users.id', '=', 'budget_sheet_approval.user_id')
                ->leftjoin('company', 'company.id', '=', 'budget_sheet_approval.company_id')
                ->join('project', 'project.id', '=', 'budget_sheet_approval.project_id')
                ->join('vendor', 'vendor.id', '=', 'budget_sheet_approval.vendor_id')
                ->leftJoin('clients', 'clients.id', '=', 'budget_sheet_approval.client_id')
                ->leftJoin('project_sites', 'project_sites.id', '=', 'budget_sheet_approval.project_site_id')
                ->join('department', 'department.id', '=', 'budget_sheet_approval.department_id')
                ->where('budget_sheet_approval.budget_sheet_year', '=', $request_data['year'])
                ->where('budget_sheet_approval.budget_sheet_week', '=', $request_data['week'])
                ->where(function ($query) use ($request_data, $logged_in_userdata) {
                    //for super user show all and to other show only his data
                    if ($logged_in_userdata[0]->role != config('constants.SuperUser')) {
                        $query->where('budget_sheet_approval.user_id', $request_data['user_id']);
                    }
                })
                ->get(['clients.client_name','clients.location', 'project_sites.site_name',
            'budget_sheet_approval.*', 'company.company_name', 'project.project_name', 'users.name AS user_name', 'users.profile_image', 'budget_sheet_approval.first_approval_status as assistant_approval_status',
            'budget_sheet_approval.second_approval_status as super_user_approval_status', 'vendor.vendor_name', 'vendor.gst_number', 'vendor.pan_card_number'
        ]);

        if ($budget_sheet_approval_result->count() == 0) {
            return response()->json([
                        'status' => false,
                        'msg' => config('errors.no_record.msg'),
                        'data' => [],
                        'error' => config('errors.no_record.code')
            ]);
        }

        foreach ($budget_sheet_approval_result as $key => $budget_sheet) {


            if($budget_sheet->client_name){


                if($budget_sheet->client_name == "Other Client"){
                    $budget_sheet_approval_result[$key]->client_name = $budget_sheet->client_name;
                }else{
                    $budget_sheet_approval_result[$key]->client_name = $budget_sheet->client_name. "(" . $budget_sheet->location . ")";
                }

            }

            if ($budget_sheet->profile_image) {

                $budget_sheet_approval_result[$key]->profile_image = asset('storage/' . str_replace('public/', '', $budget_sheet->profile_image));
            } else {

                $budget_sheet_approval_result[$key]->profile_image = "";
            }


            $approval_count = 0;
            if ($budget_sheet->assistant_approval_status == "Approved") {
                $approval_count = $approval_count + 1;
            }
            if ($budget_sheet->super_user_approval_status == "Approved") {
                $approval_count = $approval_count + 1;
            }

            $budget_sheet->approval_percent = ($approval_count * 100) / 2;
        }

        return response()->json([
                    'status' => true,
                    'msg' => "Record found.",
                    'data' => $budget_sheet_approval_result
        ]);
    }

    public function salary_report(Request $request) {   //17/09/2020
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'year' => 'required',
                    'month' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();

        $logged_in_userdata = User::where('id', $request_data['user_id'])->get();
        $company_id = Employees::where('user_id', $request_data['user_id'])->value('company_id');

        $salary_result = Payroll::join('users', 'users.id', '=', 'payroll.user_id')
                ->leftjoin('cheque_register','cheque_register.id','=','payroll.cheque_no')
                ->join('company', 'company.id', '=', 'payroll.company_id')
                ->where('payroll.company_id', '=', $company_id)
                ->where('payroll.year', '=', $request_data['year'])
                ->where('payroll.month', '=', $request_data['month'])
                ->where(function ($query) use ($logged_in_userdata, $request_data) {
                    //for super user show all and to other show only his data
                    if ($logged_in_userdata[0]->role != config('constants.SuperUser')) {
                        $query->where('payroll.user_id', $request_data['user_id']);
                    }
                })
                ->get([
            'payroll.*','cheque_register.ch_no', 'company.company_name', 'users.name AS user_name', 'users.profile_image', 'payroll.first_approval_status as hr_approval_status',
            'payroll.second_approval_status as assistant_approval_status', 'payroll.third_approval_status as admin_approval_status', 'payroll.fourth_approval_status as super_user_approval_status', 'payroll.fifth_approval_status as account_approval_status'
        ]);

        if ($salary_result->count() == 0) {
            return response()->json([
                        'status' => false,
                        'msg' => config('errors.no_record.msg'),
                        'data' => [],
                        'error' => config('errors.no_record.code')
            ]);
        }

        foreach ($salary_result as $key => $salary) {

            if ($salary->profile_image) {

                $salary_result[$key]->profile_image = asset('storage/' . str_replace('public/', '', $salary->profile_image));
            } else {

                $salary_result[$key]->profile_image = "";
            }

            if ($salary->salary_slip_file) {

                $salary_result[$key]->salary_slip_file = asset('storage/' . str_replace('public/', '', $salary->salary_slip_file));
            } else {

                $salary_result[$key]->salary_slip_file = "";
            }


            $approval_count = 0;
            if ($salary->hr_approval_status == "Approved") {
                $approval_count = $approval_count + 1;
            }
            if ($salary->assistant_approval_status == "Approved") {
                $approval_count = $approval_count + 1;
            }
            if ($salary->admin_approval_status == "Approved") {
                $approval_count = $approval_count + 1;
            }
            if ($salary->super_user_approval_status == "Approved") {
                $approval_count = $approval_count + 1;
            }
            if ($salary->account_approval_status == "Approved") {
                $approval_count = $approval_count + 1;
            }
            /*if ($salary->main_approval_status == "Approved") {
                $approval_count = $approval_count + 1;
            }*/

            $salary->approval_percent = ($approval_count * 100) / 5;
        }

        return response()->json([
                    'status' => true,
                    'msg' => "Record found.",
                    'data' => $salary_result
        ]);
    }

    public function travel_expense_report(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'page_number' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();

        $offset = ($request_data['page_number'] - 1) * $this->page_limit;
        $logged_in_userdata = User::where('id', $request_data['user_id'])->get();

        $partial_query = Travel::where(function ($query) use ($request_data) {
                    if (isset($request_data['start_date']) && isset($request_data['end_date'])) {
                        $query->whereDate('travel.created_at', '>=', $request_data['start_date'])
                                ->whereDate('travel.created_at', '<=', $request_data['end_date']);
                    }
                });
        if (isset($request_data['status'])) {
            $partial_query->where('travel.status', $request_data['status']);
        }

        //for super user show all and to other show only his data

        if ($logged_in_userdata[0]->role != config('constants.SuperUser')) {
            $partial_query->where('travel.booked_by', $request_data['user_id']);
        }

        $travel_result = $partial_query->join('users', 'users.id', '=', 'travel.booked_by')
                ->join('company', 'company.id', '=', 'travel.company_id')
                ->join('project', 'project.id', '=', 'travel.project_id')
                ->offset($offset)
                ->limit($this->page_limit)
                ->get([
            'travel.*',
            'company.company_name',
            'project.project_name',
            'users.name AS user_name',
            'users.profile_image',
            'travel.first_approval_status as assistant_approval_status',
            'travel.second_approval_status as admin_approval_status',
            'travel.third_approval_status as super_user_approval_status',
            'travel.fourth_approval_status as accountant_approval_status'
        ]);

        if ($travel_result->count() == 0) {
            return response()->json([
                        'status' => false,
                        'msg' => config('errors.no_record.msg'),
                        'data' => [],
                        'error' => config('errors.no_record.code')
            ]);
        }

        foreach ($travel_result as $key => $travel) {



            $travel->travel_via = config::get('constants.TRAVEL_VIA')[$travel->travel_via];
            $travel->payment_type = config::get('constants.PAYMENT_TYPE')[$travel->payment_type];

            if ($travel->ticket_image) {

                $travel_result[$key]->ticket_image = asset('storage/' . str_replace('public/', '', $travel->ticket_image));
            } else {

                $travel_result[$key]->ticket_image = "";
            }
            if ($travel->profile_image) {

                $travel_result[$key]->profile_image = asset('storage/' . str_replace('public/', '', $travel->profile_image));
            } else {

                $travel_result[$key]->profile_image = "";
            }

            $traveler_ids_arr = explode(',', $travel->traveler_ids);
            $travel_result[$key]->travel_user_list = [];
            $traveler_user_detail = [];
            foreach ($traveler_ids_arr as $k => $id) {

                $traveler_details = User::where('id', $id)->get(['id', 'name', 'profile_image']);

                if ($traveler_details->count() == 0) {
                    continue;
                }

                if ($traveler_details[0]['profile_image']) {

                    $traveler_details[0]['profile_image'] = asset('storage/' . str_replace('public/', '', $traveler_details[0]['profile_image']));
                } else {

                    $traveler_details[0]['profile_image'] = "";
                }

                $traveler_user_detail[$k] = $traveler_details[0];
            }
            $travel_result[$key]->travel_user_list = $traveler_user_detail;

            $approval_count = 0;
            if ($travel->assistant_approval_status == "Approved") {
                $approval_count = $approval_count + 1;
            }
            if ($travel->admin_approval_status == "Approved") {
                $approval_count = $approval_count + 1;
            }
            if ($travel->super_user_approval_status == "Approved") {
                $approval_count = $approval_count + 1;
            }
            if ($travel->accountant_approval_status == "Approved") {
                $approval_count = $approval_count + 1;
            }

            $travel->approval_percent = ($approval_count * 100) / 4;
        }
        return response()->json([
                    'status' => true,
                    'msg' => "Record found.",
                    'data' => $travel_result
        ]);
    }

    public function hotel_expense_report(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'page_number' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();

        $offset = ($request_data['page_number'] - 1) * $this->page_limit;

        $logged_in_userdata = User::where('id', $request_data['user_id'])->get();

        $partial_query = HotelBooking::where(function ($query) use ($request_data) {
                    if (isset($request_data['start_date']) && isset($request_data['end_date'])) {

                        $query->whereDate('hotel_booking.created_at', '>=', $request_data['start_date'])
                                ->whereDate('hotel_booking.created_at', '<=', $request_data['end_date']);
                    }
                });
        if (isset($request_data['status'])) {
            $partial_query->where('hotel_booking.status', $request_data['status']);
        }


        //for super user show all and to other show only his data
        if ($logged_in_userdata[0]->role != config('constants.SuperUser')) {
            $partial_query->where('hotel_booking.booked_by', $request_data['user_id']);
        }

        $hotel_result = $partial_query->join('users', 'users.id', '=', 'hotel_booking.booked_by')
                ->join('company', 'company.id', '=', 'hotel_booking.company_id')
                ->join('project', 'project.id', '=', 'hotel_booking.project_id')
                ->offset($offset)
                ->limit($this->page_limit)
                ->get([
            'hotel_booking.*',
            'company.company_name',
            'project.project_name',
            'users.name AS user_name',
            'users.profile_image',
            'hotel_booking.first_approval_status as assistant_approval_status',
            'hotel_booking.second_approval_status as admin_approval_status',
            'hotel_booking.third_approval_status as super_user_approval_status',
            'hotel_booking.fourth_approval_status as accountant_approval_status'
        ]);

        if ($hotel_result->count() == 0) {
            return response()->json([
                        'status' => false,
                        'msg' => config('errors.no_record.msg'),
                        'data' => [],
                        'error' => config('errors.no_record.code')
            ]);
        }

        foreach ($hotel_result as $key => $hotel) {

            $hotel->payment_type = config::get('constants.PAYMENT_TYPE')[$hotel->payment_type];

            if ($hotel->booking_image) {

                $hotel_result[$key]->booking_image = asset('storage/' . str_replace('public/', '', $hotel->booking_image));
            } else {

                $hotel_result[$key]->booking_image = "";
            }
            if ($hotel->profile_image) {

                $hotel_result[$key]->profile_image = asset('storage/' . str_replace('public/', '', $hotel->profile_image));
            } else {

                $hotel_result[$key]->profile_image = "";
            }

            $stay_user_ids_arr = explode(',', $hotel->stay_user_ids);
            //$hotel_result[$key]->stay_user_list = [];
            $stay_user_detail = [];
            foreach ($stay_user_ids_arr as $k => $id) {

                $stay_user_details = User::where('id', $id)->get(['id', 'name', 'profile_image']);

                if ($stay_user_details->count() == 0) {
                    continue;
                }

                if ($stay_user_details[0]['profile_image']) {

                    $traveler_details[0]['profile_image'] = asset('storage/' . str_replace('public/', '', $stay_user_details[0]['profile_image']));
                } else {

                    $traveler_details[0]['profile_image'] = "";
                }

                $stay_user_detail[$k] = $stay_user_details[0];
            }
            $hotel_result[$key]->stay_user_list = $stay_user_detail;

            $approval_count = 0;
            if ($hotel->assistant_approval_status == "Approved") {
                $approval_count = $approval_count + 1;
            }
            if ($hotel->admin_approval_status == "Approved") {
                $approval_count = $approval_count + 1;
            }
            if ($hotel->super_user_approval_status == "Approved") {
                $approval_count = $approval_count + 1;
            }
            if ($hotel->accountant_approval_status == "Approved") {
                $approval_count = $approval_count + 1;
            }

            $hotel->approval_percent = ($approval_count * 100) / 4;
        }
        return response()->json([
                    'status' => true,
                    'msg' => "Record found.",
                    'data' => $hotel_result
        ]);
    }

    public function employee_loan_report(Request $request) {   //17/09/2020
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'page_number' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];


        $offset = ($request_data['page_number'] - 1) * $this->page_limit;

        $logged_in_userdata = User::where('id', $request_data['user_id'])->get();

        $loan_transaction_records = LoanTransaction::with(['employee_loan', 'user'])
                ->join('employee_loan', 'employee_loan.id', '=', 'loan_transaction.id')
                ->leftJoin('users as first_user','first_user.id','=','employee_loan.first_approval_id')
                ->leftJoin('users as second_user','second_user.id','=','employee_loan.second_approval_id')
                ->leftJoin('users as third_user','third_user.id','=','employee_loan.third_approval_id')
                ->leftjoin('cheque_register','cheque_register.id','=','employee_loan.cheque_no')
                ->where(function ($query) use ($request_data, $logged_in_userdata) {
                    if (isset($request_data['month']) && isset($request_data['year'])) {
                        $query->where('month', '=', $request_data['month'])
                        ->where('year', '=', $request_data['year']);
                    }
                    //for super user show all and to other show only his data
                    if ($logged_in_userdata[0]->role != config('constants.SuperUser')) {
                        $query->where('loan_transaction.user_id', $request_data['user_id']);
                    }
                })
                ->offset($offset)
                ->limit($this->page_limit)
                ->orderBy('loan_transaction.id', 'DESC')
                //->groupBy('attendance_detail.attendance_master_id')
                ->get(['loan_transaction.*','cheque_register.ch_no','employee_loan.payment_details','first_user.name as first_username','second_user.name as second_username','third_user.name as third_username']);

        if ($loan_transaction_records->count() == 0) {
            return response()->json([
                        'status' => false,
                        'msg' => config('errors.no_record.msg'),
                        'data' => [],
                        'error' => config('errors.no_record.code')
            ]);
        }


        $loan_records = $loan_transaction_records->toArray();

        foreach ($loan_records as $key => $loan) {

            if ($loan['user']['profile_image']) {
                $loan_records[$key]['user']['profile_image'] = asset('storage/' . str_replace('public/', '', $loan['user']['profile_image']));
            } else {
                $loan_records[$key]['user']['profile_image'] = "";
            }
        }

        return response()->json([
                    'status' => true,
                    'msg' => "Record found.",
                    'data' => $loan_records
        ]);
    }

    public function cheque_register_report(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'page_number' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];


        $offset = ($request_data['page_number'] - 1) * $this->page_limit;

        $logged_in_userdata = User::where('id', $request_data['user_id'])->get();


        if (
                $logged_in_userdata[0]->role == config('constants.SuperUser') ||
                $logged_in_userdata[0]->role == config('constants.ASSISTANT') ||
                $logged_in_userdata[0]->role == config('constants.Admin') ||
                $logged_in_userdata[0]->role == config('constants.ACCOUNT_ROLE')
        ) {



            $loan_transaction_records = ChequeRegister::join('company', 'company.id', '=', 'cheque_register.company_id')
                    ->join('bank', 'bank.id', '=', 'cheque_register.bank_id')
                    ->leftjoin('vendor', 'vendor.id', '=', 'cheque_register.party_detail')
                    ->leftjoin('project', 'project.id', '=', 'cheque_register.project_id')
                    //->where('cheque_register.is_used', '=', 'used')
                    ->where(function ($query) use ($request_data, $logged_in_userdata) {
                        if (isset($request_data['start_date']) && isset($request_data['end_date'])) {
                            $query->whereDate('cheque_register.created_at', '>=', $request_data['start_date'])
                            ->whereDate('cheque_register.created_at', '<=', $request_data['end_date']);
                        }
                        if (isset($request_data['company_id']) && $request_data['company_id']) {
                            $query->where('cheque_register.company_id','=',$request_data['company_id']);
                        }
                        if (isset($request_data['bank_id']) && $request_data['bank_id']) {
                            $query->where('cheque_register.bank_id','=',$request_data['bank_id']);
                        }
                        if (isset($request_data['cheque_no']) && $request_data['cheque_no']) {
                            $query->where('cheque_register.ch_no','=',$request_data['cheque_no']);
                        }
                        // for super user show all and to other show only his data
                        // if ($logged_in_userdata[0]->role != config('constants.SuperUser')) {
                        //     $query->where('cheque_register.user_id', $request_data['user_id']);
                        // }
                    })
                    ->offset($offset)
                    ->limit($this->page_limit)
                    ->orderBy('cheque_register.id', 'DESC')
                    //->groupBy('attendance_detail.attendance_master_id')
                    ->get(['cheque_register.*', 'company.company_name', 'bank.bank_name','bank.ac_number', 'project.project_name', 'vendor.vendor_name']);



            if ($loan_transaction_records->count() == 0) {
                return response()->json([
                            'status' => false,
                            'msg' => config('errors.no_record.msg'),
                            'data' => [],
                            'error' => config('errors.no_record.code')
                ]);
            }


            $loan_records = $loan_transaction_records->toArray();

            // foreach ($loan_records as $key => $loan) {
            //     if ($loan['user']['profile_image']) {
            //         $loan_records[$key]['user']['profile_image'] = asset('storage/' . str_replace('public/', '', $loan['user']['profile_image']));
            //     } else {
            //         $loan_records[$key]['user']['profile_image'] = "";
            //     }
            // }

            return response()->json([
                        'status' => true,
                        'msg' => "Record found.",
                        'data' => $loan_records
            ]);
        } else {

            return response()->json([
                        'status' => false,
                        'msg' => config('errors.permission_error.msg'),
                        'data' => [],
                        'error' => config('errors.permission_error.code')
            ]);
        }
    }
    //16/06/2020
    public function rtgs_register_report(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'page_number' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];


        $offset = ($request_data['page_number'] - 1) * $this->page_limit;

        $logged_in_userdata = User::where('id', $request_data['user_id'])->get();


        if (
                $logged_in_userdata[0]->role == config('constants.SuperUser') ||
                $logged_in_userdata[0]->role == config('constants.ASSISTANT') ||
                $logged_in_userdata[0]->role == config('constants.Admin') ||
                $logged_in_userdata[0]->role == config('constants.ACCOUNT_ROLE')
        ) {



            $rtgs_records = RtgsRegister::join('company', 'company.id', '=', 'rtgs_register.company_id')
                    ->join('bank', 'bank.id', '=', 'rtgs_register.bank_id')
                    ->leftjoin('vendor', 'vendor.id', '=', 'rtgs_register.party_detail')
                    ->leftjoin('project', 'project.id', '=', 'rtgs_register.project_id')
                    //->where('rtgs_register.is_used', '=', 'used')
                    ->where(function ($query) use ($request_data, $logged_in_userdata) {
                        if (isset($request_data['start_date']) && isset($request_data['end_date'])) {
                            $query->whereDate('rtgs_register.created_at', '>=', $request_data['start_date'])
                            ->whereDate('rtgs_register.created_at', '<=', $request_data['end_date']);
                        }
                        if (isset($request_data['company_id']) && $request_data['company_id']) {
                            $query->where('rtgs_register.company_id','=',$request_data['company_id']);
                        }
                        if (isset($request_data['bank_id']) && $request_data['bank_id']) {
                            $query->where('rtgs_register.bank_id','=',$request_data['bank_id']);
                        }
                        if (isset($request_data['rtgs_no']) && $request_data['rtgs_no']) {
                            $query->where('rtgs_register.rtgs_no','=',$request_data['rtgs_no']);
                        }
                    })
                    ->offset($offset)
                    ->limit($this->page_limit)
                    ->orderBy('rtgs_register.id', 'DESC')
                    ->get(['rtgs_register.*', 'company.company_name', 'bank.bank_name','bank.ac_number', 'project.project_name', 'vendor.vendor_name']);



            if ($rtgs_records->count() == 0) {
                return response()->json([
                            'status' => false,
                            'msg' => config('errors.no_record.msg'),
                            'data' => [],
                            'error' => config('errors.no_record.code')
                ]);
            }


            $rtgs_records = $rtgs_records->toArray();



            return response()->json([
                        'status' => true,
                        'msg' => "Record found.",
                        'data' => $rtgs_records
            ]);
        } else {

            return response()->json([
                        'status' => false,
                        'msg' => config('errors.permission_error.msg'),
                        'data' => [],
                        'error' => config('errors.permission_error.code')
            ]);
        }
    }

    public function letter_head_register_report(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'page_number' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];


        $offset = ($request_data['page_number'] - 1) * $this->page_limit;

        $logged_in_userdata = User::where('id', $request_data['user_id'])->get();


        if (
                $logged_in_userdata[0]->role == config('constants.SuperUser') ||
                $logged_in_userdata[0]->role == config('constants.ASSISTANT') ||
                $logged_in_userdata[0]->role == config('constants.Admin') ||
                $logged_in_userdata[0]->role == config('constants.ADMIN_EXECUTIVE')
        ) {

            $letter_head_records = LetterHeadRegister::join('users', 'users.id', '=', 'letter_head_register.user_id')
                    ->join('company', 'company.id', '=', 'letter_head_register.company_id')
                    //->join('bank', 'bank.id', '=', 'letter_head_register.bank_id')
                    ->join('vendor', 'vendor.id', '=', 'letter_head_register.party_detail')
                    ->join('project', 'project.id', '=', 'letter_head_register.project_id')
                    //->where('letter_head_register.is_used', '=', 'used')
                    ->where(function ($query) use ($request_data, $logged_in_userdata) {
                        if (isset($request_data['start_date']) && isset($request_data['end_date'])) {
                            $query->whereDate('letter_head_register.created_at', '>=', $request_data['start_date'])
                            ->whereDate('letter_head_register.created_at', '<=', $request_data['end_date']);
                        }
                        if (isset($request_data['company_id']) && $request_data['company_id']) {
                            $query->where('letter_head_register.company_id','=',$request_data['company_id']);
                        }

                        if (isset($request_data['letter_head_no']) && $request_data['letter_head_no']) {
                            $query->where('letter_head_register.letter_head_number','=',$request_data['letter_head_no']);
                        }
                    })
                    ->offset($offset)
                    ->limit($this->page_limit)
                    ->get(['letter_head_register.*', 'users.name', 'users.profile_image', 'company.company_name', 'project.project_name', 'vendor.vendor_name']);

            if ($letter_head_records->count() == 0) {
                return response()->json([
                            'status' => false,
                            'msg' => config('errors.no_record.msg'),
                            'data' => [],
                            'error' => config('errors.no_record.code')
                ]);
            }

            foreach ($letter_head_records as $key => $letter) {

                if ($letter->profile_image) {

                    $letter_head_records[$key]->profile_image = asset('storage/' . str_replace('public/', '', $letter->profile_image));
                } else {

                    $letter_head_records[$key]->profile_image = "";
                }
            }

            return response()->json([
                        'status' => true,
                        'msg' => "Record found.",
                        'data' => $letter_head_records
            ]);
        } else {

            return response()->json([
                        'status' => false,
                        'msg' => config('errors.permission_error.msg'),
                        'data' => [],
                        'error' => config('errors.permission_error.code')
            ]);
        }
    }

    //nishit 04-08-2020
    public function online_payment_approvals_report(Request $request)   //change
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'page_number' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $response_data = [];
        $offset = ($request_data['page_number'] - 1) * $this->page_limit;
        $logged_in_userdata = User::where('id', $request_data['user_id'])->get();
        $partial_query = OnlinePaymentApproval::with('paymentFiles')
        ->where(function ($query) use ($request_data) {
            if (isset($request_data['start_date']) && isset($request_data['end_date'])) {
                $query->whereDate('online_payment_approval.created_at', '>=', $request_data['start_date'])
                ->whereDate('online_payment_approval.created_at', '<=', $request_data['end_date']);
            }
        });
        if (isset($request_data['status'])) {
            $partial_query->where('online_payment_approval.status', $request_data['status']);
        }
        //for super user show all and to other show only his data
        if ($logged_in_userdata[0]->role != config('constants.SuperUser')) {
            $partial_query->where('online_payment_approval.user_id', $request_data['user_id']);
        }
        $online_payment_approval_result = $partial_query->join('users', 'online_payment_approval.user_id', '=', 'users.id')
        ->leftJoin('users as first_user', 'first_user.id', '=', 'online_payment_approval.first_approval_id')
        ->leftJoin('users as second_user', 'second_user.id', '=', 'online_payment_approval.second_approval_id')
        ->leftJoin('users as third_user', 'third_user.id', '=', 'online_payment_approval.third_approval_id')
        ->join('company', 'company.id', '=', 'online_payment_approval.company_id')
        ->join('project',
            'project.id',
            '=',
            'online_payment_approval.project_id'
        )
        ->join('vendor', 'vendor.id', '=', 'online_payment_approval.vendor_id')
        ->leftJoin('bank', 'bank.id', '=', 'online_payment_approval.bank_id')
        ->leftJoin('clients', 'clients.id', '=', 'online_payment_approval.client_id')
        ->leftjoin('rtgs_register', 'rtgs_register.id', '=', 'online_payment_approval.rtgs_number')
        ->leftJoin('project_sites', 'project_sites.id', '=', 'online_payment_approval.project_site_id')
        ->leftJoin('vendors_bank', 'vendors_bank.id', '=', 'online_payment_approval.bank_details')
        ->leftJoin('payment_card', 'payment_card.id', '=', 'online_payment_approval.transaction_id')
        ->leftjoin('tds_section_type', 'tds_section_type.id', '=', 'online_payment_approval.section_type_id')
        ->offset($offset)
            ->limit($this->page_limit)
            ->orderBy('online_payment_approval.id', 'DESC')
            ->get([
                'clients.client_name', 'clients.location', 'project_sites.site_name', 'rtgs_register.rtgs_no',
                'online_payment_approval.*', 'online_payment_approval.first_approval_status AS accountant_approval_status',
                'online_payment_approval.second_approval_status AS admin_approval_status', 'online_payment_approval.third_approval_status as super_user_approval_status',
                'payment_card.card_type', 'payment_card.name_on_card', 'payment_card.card_number',
                'vendor.vendor_name', 'bank.bank_name', 'vendors_bank.bank_name AS vendor_bank_name',
                'company.company_name', 'project.project_name', 'users.name AS user_name',
                'users.profile_image', 'first_user.name as first_user_name', 'second_user.name as second_user_name',
            'third_user.name as third_user_name', 'tds_section_type.section_type'
            ]);
        if ($online_payment_approval_result->count() == 0) {
            return response()->json([
                'status' => false,
                'msg' => config('errors.no_record.msg'),
                'data' => [],
                'error' => config('errors.no_record.code')
            ]);
        }
        foreach ($online_payment_approval_result as $key => $online) {
            if ($online->client_name == "Other Client") {
                $online_payment_approval_result[$key]->client_name = $online->client_name;
            } else {
                $online_payment_approval_result[$key]->client_name = $online->client_name . "(" . $online->location . ")";
            }
            if ($online->profile_image) {
                $online_payment_approval_result[$key]->profile_image = asset('storage/' . str_replace('public/', '', $online->profile_image));
            } else {
                $online_payment_approval_result[$key]->profile_image = "";
            }

            if ($online->invoice_file) {
                $online_payment_approval_result[$key]->invoice_file = asset('storage/' . str_replace('public/', '', $online->invoice_file));
            } else {
                $online_payment_approval_result[$key]->invoice_file = "";
            }

            foreach ($online->paymentFiles as $k => $file) {
                if ($file->online_payment_file) {
                    $online_payment_approval_result[$key]->paymentFiles[$k]->online_payment_file = asset('storage/' . str_replace('public/', '', $file->online_payment_file));
                } else {
                    $online_payment_approval_result[$key]->paymentFiles[$k]->online_payment_file = "";
                }
            }
            $approval_count = 0;
            if ($online->accountant_approval_status == "Approved") {
                $approval_count = $approval_count + 1;
            }
            if ($online->admin_approval_status == "Approved") {
                $approval_count = $approval_count + 1;
            }
            if ($online->super_user_approval_status == "Approved") {
                $approval_count = $approval_count + 1;
            }
            $online->approval_percent = ($approval_count * 100) / 3;
        }
        return response()->json([
            'status' => true,
            'msg' => "Record found.",
            'data' => $online_payment_approval_result
        ]);
    }


    public function vehicle_maintenance_approvals_report(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'page_number' => 'required'

        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();

        $response_data = [];

        $offset = ($request_data['page_number'] - 1) * $this->page_limit;

        $logged_in_userdata = User::where('id', $request_data['user_id'])->get();

        $partial_query = Vehicle_Maintenance::with(['vehicleImage', 'asset'])
            ->where(function ($query) use ($request_data) {
                if (isset($request_data['start_date']) && isset($request_data['end_date'])) {

                    $query->whereDate('vehicle_maintenance.maintenance_date', '>=', $request_data['start_date'])
                        ->whereDate('vehicle_maintenance.maintenance_date', '<=', $request_data['end_date']);
                }
            });
        if (isset($request_data['status'])) {
            $partial_query->where('vehicle_maintenance.final_approval', $request_data['status']);
        }
        //for super user show all and to other show only his data
        if ($logged_in_userdata[0]->role != config('constants.SuperUser')) {
            $partial_query->where('vehicle_maintenance.user_id', $request_data['user_id']);
        }

        $vehicle_maintenance_approval_result = $partial_query->join('users', 'vehicle_maintenance.user_id', '=', 'users.id')
            ->leftjoin('users AS Admin', 'vehicle_maintenance.first_approval_id', '=', 'Admin.id')
            ->leftjoin('users AS Superuser', 'vehicle_maintenance.second_approval_id', '=', 'Superuser.id')
            ->join('company', 'vehicle_maintenance.company_id', '=', 'company.id')
            ->offset($offset)
            ->limit($this->page_limit)
            ->get([
                'vehicle_maintenance.*',
                'company.company_name', 'users.name AS user_name',
                'users.profile_image', 'vehicle_maintenance.first_approval_status AS admin_approval_status',
                'vehicle_maintenance.second_approval_status AS superUser_approval_status',
                'Admin.name AS first_user_name', 'Superuser.name AS second_user_name'
            ]);


            if ($vehicle_maintenance_approval_result->count() == 0) {
                return response()->json([
                    'status' => false,
                    'msg' => config('errors.no_record.msg'),
                    'data' => [],
                    'error' => config('errors.no_record.code')
                ]);
            }

            foreach ($vehicle_maintenance_approval_result as $key => $vehicle) {


                if ($vehicle->profile_image) {
                    $vehicle_maintenance_approval_result[$key]->profile_image = asset('storage/' . str_replace('public/', '', $vehicle->profile_image));
                } else {
                    $vehicle_maintenance_approval_result[$key]->profile_image = "";
                }


                foreach ($vehicle->vehicleImage as $k => $file) {

                    if ($file->image) {
                        $vehicle_maintenance_approval_result[$key]->vehicleImage[$k]->image = asset('storage/' . str_replace('public/', '', $file->image));
                    } else {
                        $vehicle_maintenance_approval_result[$key]->vehicleImage[$k]->image = "";
                    }
                }

                $approval_count = 0;
                if ($vehicle->admin_approval_status == "Approved") {
                    $approval_count = $approval_count + 1;
                }
                if ($vehicle->superUser_approval_status == "Approved") {
                    $approval_count = $approval_count + 1;
                }

                $vehicle->approval_percent = ($approval_count * 100) / 2;
            }

            return response()->json([
                'status' => true,
                'msg' => "Record found.",
                'data' => $vehicle_maintenance_approval_result
            ]);

    }

}
