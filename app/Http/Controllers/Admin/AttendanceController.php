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
use App\Leaves;
use App\AttendanceDetail;
use App\AttendanceMaster;
use App\LeaveMaster;
use App\EmployeesSalary;
use App\EmployeesLoans;
use App\Email_format;
use App\Payroll;
use App\LoanTransaction;
use App\Mail\Mails;
use DateTime;

use App\Attendance_approvals;

use Illuminate\Support\Facades\Mail;
use PDF;
use App\Lib\CommonTask;
use App\Lib\Permissions;
use App\Lib\NotificationTask;

class AttendanceController extends Controller {

    public $data;
    private $common_task;
    private $notification_task;

    public function __construct() {
        $this->data['module_title'] = "Attendance";
        $this->data['module_link'] = "admin.attendance";
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();
    }

    public function index() {
        $this->data['page_title'] = "Attendance";
        $this->data['add_permission'] = Permissions::checkPermission(20, 3);
        return view('admin.attendance.index', $this->data);
    }

    public function get_attendance_list() {

        $datatable_fields = array('users.name', 'attendance_master.date', 'attendance_master.availability_status', 'attendance_master.first_in', 'attendance_master.last_out', 'attendance_master.total_hours', 'attendance_master.is_late', 'attendance_master.late_time');
        $request = Input::all();
        $conditions_array = [];
        $or_conditions_array = [];
        $join_str[0]['table'] = 'users';
        $join_str[0]['join_table_id'] = 'users.id';
        $join_str[0]['from_table_id'] = 'attendance_master.user_id';
        $join_str[0]['join_type'] = '';


        $getfiled = array('attendance_master.id', 'users.name', 'attendance_master.user_id', 'attendance_master.first_in', 'attendance_master.last_out',
            'attendance_master.total_hours', 'attendance_master.date', 'attendance_master.availability_status', 'attendance_master.is_late', 'attendance_master.is_late_more', 'attendance_master.late_time');

        $table = "attendance_master";
        $attendance_permission_full_view = Permissions::checkPermission(20, 5);
        //$attendance_permission_my_view = Permissions::checkPermission(20, 1);
        $attendance_permission_partial_view = Permissions::checkPermission(20, 6);
        if ($attendance_permission_full_view) {
            
        } elseif ($attendance_permission_partial_view) {
            $join_str[1]['table'] = 'employee';
            $join_str[1]['join_table_id'] = 'employee.user_id';
            $join_str[1]['from_table_id'] = 'users.id';
            $join_str[1]['join_type'] = '';
            $conditions_array['attendance_master.user_id'] = Auth::user()->id;
            $or_conditions_array['employee.reporting_user_id'] = Auth::user()->id;
        } else {
            $conditions_array['attendance_master.user_id'] = Auth::user()->id;
        }

        echo AttendanceMaster::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $or_conditions_array, $getfiled, $request, $join_str);

        die();
    }

    public function add_attendance() {
        $this->data['page_title'] = 'Add attendance';
        $this->data['user'] = User::orderBy('name')->where('status', 'Enabled')->where('role', '!=', config('constants.SuperUser'))->get()->pluck('name', 'id');
        return view('admin.attendance.add_attendance', $this->data);
    }

    //03/09/2020
    public function insert_attendance_approval(Request $request)
    {
        $validator_normal = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'date' => 'required',
                    'in' => 'required',
                    'out' => 'required'
        ]);
        $request_data = $request->all();
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_attendance')->with('error', 'Please follow validation rules.');
        }

        //1. case one
        $check_holiday_weekend = $this->common_task->check_holiday_weekend_attendance($request_data['user_id'], $request_data['date']);
        if (!$check_holiday_weekend) {
            return redirect()->route('admin.add_attendance')->with('error', 'This user has not requested/approved any attendance for work on holiday/weekend on given date. Please ask user to make request from Weekend/Holiday Request menu, and once that request will be approved then you can make attendance of that date for user');
        }

        //2. case two
        $attendanceMaster = AttendanceMaster::where('date', $request->input('date'))->where('user_id', $request->input('user_id'))->get()->first();
        if (!empty($attendanceMaster)) {
            if ($attendanceMaster->availability_status == 3 || $attendanceMaster->availability_status == 4 || $attendanceMaster->availability_status == 5) {
                $approvalModel = new Attendance_approvals();
                $approvalModel->user_id = $request_data['user_id'];
                $approvalModel->manual_add_by = Auth::user()->id;
                $approvalModel->manual_add_reason = $request_data['manual_add_reason'];
                $approvalModel->attendace_date = date('Y-m-d', strtotime($request_data['date']));        
                $approvalModel->punch_in = date("H:i:s", strtotime($request_data['in']));
                $approvalModel->punch_out = date("H:i:s", strtotime($request_data['out']));
                $approvalModel->status = 'Pending';
                $approvalModel->created_at = date('Y-m-d h:i:s');
                $approvalModel->created_ip = $request->ip();
                $approvalModel->updated_at = date('Y-m-d h:i:s');
                $approvalModel->updated_ip = $request->ip();
                $approvalModel->updated_by = Auth::user()->id;
                    if ($approvalModel->save()) {
                        return redirect()->route('admin.attendance')->with('success', 'Attendance Request added successfully. Please check pending attendance and approve this new added attendance after 10 minutes once system calculate total hours.');
                    } else {
                        return redirect()->route('admin.add_attendance')->with('error', 'Error occurre in insert. Try Again!');
                    }
            } else {
                return redirect()->route('admin.add_attendance')->with('error', 'Attendance already available for this date.');
            }
        }

        $approvalModel = new Attendance_approvals();
        $approvalModel->user_id = $request_data['user_id'];
        $approvalModel->manual_add_by = Auth::user()->id;
        $approvalModel->manual_add_reason = $request_data['manual_add_reason'];
        $approvalModel->attendace_date = date('Y-m-d', strtotime($request_data['date']));        
        $approvalModel->punch_in = date("H:i:s", strtotime($request_data['in']));
        $approvalModel->punch_out = date("H:i:s", strtotime($request_data['out']));
        $approvalModel->status = 'Pending';
        $approvalModel->created_at = date('Y-m-d h:i:s');
        $approvalModel->created_ip = $request->ip();
        $approvalModel->updated_at = date('Y-m-d h:i:s');
        $approvalModel->updated_ip = $request->ip();
        $approvalModel->updated_by = Auth::user()->id;

        if ($approvalModel->save()) {
    
            return redirect()->route('admin.attendance')->with('success', 'New Attendace request added successfully.');
        } else {
            return redirect()->route('admin.add_attendance')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    //not in use 03/09/2020
    public function insert_attendance(Request $request) {
        $validator_normal = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'date' => 'required',
                    'in' => 'required',
                    'out' => 'required'
        ]);

        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_attendance')->with('error', 'Please follow validation rules.');
        }

        $check_holiday_weekend = $this->common_task->check_holiday_weekend_attendance($request->input('user_id'), $request->input('date'));
        if (!$check_holiday_weekend) {
            return redirect()->route('admin.add_attendance')->with('error', 'This user has not requested/approved any attendance for work on holiday/weekend on given date. Please ask user to make request from Weekend/Holiday Request menu, and once that request will be approved then you can make attendance of that date for user.');
        }
        $attendanceMaster = AttendanceMaster::where('date', $request->input('date'))->where('user_id', $request->input('user_id'))->get()->first();

        if (!empty($attendanceMaster)) {
            if ($attendanceMaster->availability_status == 3 || $attendanceMaster->availability_status == 4 || $attendanceMaster->availability_status == 5) {
                $this->manual_attendance_insert($request, $attendanceMaster, 1);
                return redirect()->route('admin.attendance')->with('success', 'Attendance added successfully. Please check pending attendance and approve this new added attendance after 10 minutes once system calculate total hours.');
            } else {
                return redirect()->route('admin.add_attendance')->with('error', 'Attendance already available for this date.');
            }
        } else {
            $this->manual_attendance_insert($request, $attendanceMaster, 0);
            return redirect()->route('admin.attendance')->with('success', 'Attendance added successfully.');
        }
    }

    //not in use 03/09/2020
    public function manual_attendance_insert($request, $attendance_master, $HLW_attend = 0) {
        if ($HLW_attend == 1) {
            $AttendanceMaster = $attendance_master;
        } else {
            $AttendanceMaster = new AttendanceMaster();
            $AttendanceMaster->availability_status = 1;
        }
        $AttendanceMaster->user_id = $request->input('user_id');
        $AttendanceMaster->first_in = $request->input('date') . ' ' . $request->input('in');
        $AttendanceMaster->last_out = $request->input('date') . ' ' . $request->input('out');
        $AttendanceMaster->date = $request->input('date');
        $AttendanceMaster->status = 'Enabled';
        $AttendanceMaster->created_at = date('Y-m-d H:i:s');
        $AttendanceMaster->created_ip = $request->ip();
        $AttendanceMaster->updated_at = date('Y-m-d H:i:s');
        $AttendanceMaster->updated_ip = $request->ip();
        $AttendanceMaster->manual_add_by = Auth::user()->id;
        $AttendanceMaster->manual_add_reason = $request->input('manual_add_reason');
        $AttendanceMaster->is_manually_added = 1;
        //check for late time
        $lateTime = new DateTime('09:31:00');
        $moreLateTime = new DateTime('09:46:00');
        $time = new DateTime($request->input('in'));
        $actual_lateTime = new DateTime('09:30:00');

        if ($time > $lateTime && $moreLateTime >= $time) {
            $AttendanceMaster->is_late = 'YES';
            $duration = $time->diff($actual_lateTime);
            $AttendanceMaster->late_time = $duration->format("%H:%I:%S");
        } else if ($moreLateTime < $time) {
            $AttendanceMaster->is_late_more = 'YES';
            //$duration = $time->diff($moreLateTime);
            $duration = $time->diff($actual_lateTime);
            $AttendanceMaster->late_time = $duration->format("%H:%I:%S");
        }

        if ($AttendanceMaster->save()) {

            $AttendanceDetail = new AttendanceDetail();
            $AttendanceDetail->attendance_master_id = $AttendanceMaster->id;
            $AttendanceDetail->time = $request->input('date') . ' ' . $request->input('in');
            $AttendanceDetail->punch_type = 'IN';
            $AttendanceDetail->device_type = 'WEB';
            if ($HLW_attend == 1) {
                $AttendanceDetail->is_approved = 'Pending';
            } else {
                $AttendanceDetail->is_approved = 'YES';
            }
            $AttendanceDetail->status = 'Enabled';
            $AttendanceDetail->created_at = date('Y-m-d H:i:s');
            $AttendanceDetail->created_ip = $request->ip();
            $AttendanceDetail->updated_at = date('Y-m-d H:i:s');
            $AttendanceDetail->updated_ip = $request->ip();
            $AttendanceDetail->save();

            $AttendanceDetail = new AttendanceDetail();
            $AttendanceDetail->attendance_master_id = $AttendanceMaster->id;
            $AttendanceDetail->time = $request->input('date') . ' ' . $request->input('out');
            $AttendanceDetail->punch_type = 'OUT';
            $AttendanceDetail->device_type = 'WEB';
            if ($HLW_attend == 1) {
                $AttendanceDetail->is_approved = 'Pending';
            } else {
                $AttendanceDetail->is_approved = 'YES';
            }
            $AttendanceDetail->status = 'Enabled';
            $AttendanceDetail->created_at = date('Y-m-d H:i:s');
            $AttendanceDetail->created_ip = $request->ip();
            $AttendanceDetail->updated_at = date('Y-m-d H:i:s');
            $AttendanceDetail->updated_ip = $request->ip();
            $AttendanceDetail->save();
            return true;
        } else {
            return false;
        }
    }

    public function get_punch_data($id) {

        $attendanceDetail = AttendanceDetail::where('attendance_master_id', $id)->orderBy('time', 'asc')->get(['id', 'time', 'punch_type'])->toArray();

        $expectType = "IN";
        $punch_data = [];
        $counter = $keyValue = 1;
        $is_last_entry = 0;

        if (empty($attendanceDetail)) {
            return view('admin.attendance.punch_data', ['punch_data' => [], 'id' => $id]);
        }

        foreach ($attendanceDetail as $key => $value) {
            $timing = date('h:i A', strtotime($value['time']));
            if ($expectType == $value['punch_type']) {
                $punch_data[$counter][$expectType] = $timing;
                $punch_data[$counter]['is_last_entry'] = 0;
            } else {
                $punch_data[$counter][$expectType] = 'UNSET';
                $advanceCounter = ($expectType == 'IN') ? $counter : $counter + 1;
                $expectType = ($expectType == 'IN') ? "OUT" : "IN";
                $punch_data[$advanceCounter][$expectType] = $timing;
                $punch_data[$advanceCounter]['is_last_entry'] = 0;
                $keyValue++;
            }
            $expectType = ($expectType == 'IN') ? "OUT" : "IN";

            if (($keyValue) % 2 == 0) {
                $counter++;
            }

            $keyValue++;
        }

        if ($value['punch_type'] == 'IN' && date('Y-m-d', strtotime($value['time'])) != date('Y-m-d')) {

            if (isset($advanceCounter) && $advanceCounter > $counter) {
                $punch_data[$advanceCounter]['OUT'] = 'UNSET';
                $punch_data[$advanceCounter]['is_last_entry'] = 1;
                $is_last_entry = 1;
            } else {
                $punch_data[$counter]['OUT'] = 'UNSET';
                $punch_data[$counter]['is_last_entry'] = 1;
                $is_last_entry = 1;
            }
        }

        return view('admin.attendance.punch_data', ['punch_data' => $punch_data, 'id' => $id]);
    }

    public function set_punch_data($id, $time, $type, Request $request) {

        $attendanceDate = AttendanceMaster::find($id)->date;

        $attendanceDetail = new AttendanceDetail();
        $attendanceDetail->attendance_master_id = $id;
        $attendanceDetail->time = $attendanceDate . ' ' . date("H:i:s", strtotime($time));
        $attendanceDetail->punch_type = $type;
        $attendanceDetail->device_type = 'WEB';
        $attendanceDetail->created_at = date('Y-m-d H:i:s');
        $attendanceDetail->created_ip = $request->ip();
        $attendanceDetail->updated_at = date('Y-m-d H:i:s');
        $attendanceDetail->updated_ip = $request->ip();
        $attendanceDetail->save();
        return response()->json(['status' => true, 'msg' => 'Saved successfully']);
    }

    public function get_user_attendance($id, $month, $year) {
        $attendanceDetails = AttendanceMaster::select(['id', 'user_id', 'availability_status', 'first_in', 'last_out', 'total_hours', 'date', 'is_late', 'is_late_more', 'late_time', 'manual_add_reason', 'manual_add_by', 'late_mark_removed_detail', 'late_mark_removed_by'])
                        ->where('date', 'LIKE', $year . '-' . $month . '%')
                        ->where('user_id', '=', $id)
                        ->where(function ($query) {
                            $query->orWhere('is_adjusted', '=', 1)->orWhere('is_manually_added', '=', 1)->orWhere('late_mark_removed', '=', 1);
                        })
                        ->with(['manualBy' => function($query) {
                                return $query->select(['id', 'name']);
                            }])
                        ->with(['lateMarkedBy' => function($query) {
                                return $query->select(['id', 'name']);
                            }])
                        ->orderBy('date', 'ASC')->get()->toArray();
        return view('admin.attendance.user_attendance', ['attendanceDetails' => $attendanceDetails]);
    }

    public function late_change_status($id) {
        if (AttendanceMaster::where('id', $id)->update(['is_late' => 'NO', 'is_late_more' => 'NO'])) {
            return redirect()->route('admin.attendance')->with('success', 'Status successfully updated.');
        }
        return redirect()->route('admin.attendance')->with('error', 'Error during operation. Try again!');
    }

    public function late_mark_remove(Request $request) {
        $note = $request->input('late_remove_note');
        $attend_id = $request->input('late_remove_attendance_id');
        AttendanceMaster::where('id', $attend_id)->update(['is_late' => 'NO', 'is_late_more' => 'NO', 'late_mark_removed' => 1, 'late_mark_removed_detail' => $note, 'late_mark_removed_by' => Auth::user()->id]);
        return redirect()->route('admin.attendance')->with('success', 'Status successfully updated.');
    }

    public function approve_attendance(Request $request) {
        $this->data['page_title'] = "Approve Attendance";
        return view('admin.attendance.approve', $this->data);
    }

    public function approve_attendance_list(Request $request) {

        /* $AttendanceList = AttendanceMaster::where('availability_status', '!=', 1)
          ->with(['attendance' => function($query) { return $query->where('is_approved', '=', 'NO')->select(['attendance_master_id','punch_type','time', 'device_type', 'location']);}])
          ->with(['user' => function($query) { return $query->select(['name','id']);}])
          ->get(['id', 'user_id', 'date', 'availability_status'])->toArray();

          dd($AttendanceList); */

        $datatable_fields = array('users.name', 'attendance_master.availability_status', 'attendance_master.date', 'attendance_detail.time', 'attendance_detail.punch_type', 'attendance_detail.device_type', 'attendance_detail.remote_punch_reason', 'attendance_detail.is_approved', 'attendance_detail.reject_reason');
        $request = Input::all();
        $conditions_array = [['attendance_master.availability_status', '!=', 1]];
        //$conditions_array = [['attendance_detail.is_approved', '=', 'Pending']];
        $or_conditions_array = ['attendance_detail.is_approved' => 'Pending'];
        $join_str = [];
        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'attendance_detail';
        $join_str[0]['join_table_id'] = 'attendance_detail.attendance_master_id';
        $join_str[0]['from_table_id'] = 'attendance_master.id';
        $join_str[1]['join_type'] = '';
        $join_str[1]['table'] = 'users';
        $join_str[1]['join_table_id'] = 'users.id';
        $join_str[1]['from_table_id'] = 'attendance_master.user_id';

        $getfiled = array('attendance_detail.id as detail_id', 'attendance_master_id', 'date', 'availability_status', 'punch_type', 'time', 'device_type', 'location', 'user_id', 'name', 'remote_punch_reason', 'reject_reason', 'attendance_detail.is_approved');

        $table = "attendance_master";

        echo AttendanceMaster::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $or_conditions_array, $getfiled, $request, $join_str);

        die();
    }

    public function attendance_approval(Request $request) {

        $this->attendance_approval_function($request->input('is_approved'), $request->input('reject_reason'), $request->input('attendance_id'));

        return redirect()->route('admin.approve_attendance')->with('success', 'Action Completed successfully.');
    }

    public function attendance_approval_function($isApproved, $rejectReason, $attendanceId) {

        $attendanceModel['approved_by'] = Auth::user()->id;
        $attendanceModel['is_approved'] = $isApproved;
        $attendanceModel['reject_reason'] = !empty($rejectReason) ? $rejectReason : '';

        $attendance = AttendanceDetail::where('id', $attendanceId)->update($attendanceModel);
        if ($isApproved == 'NO') {
            return true;
        }
        $attendanceDetails = AttendanceDetail::where('id', $attendanceId)->first();
        $attendanceMaster = AttendanceMaster::where('id', $attendanceDetails->attendance_master_id)->first();

        $attendanceExist = AttendanceDetail::where('id','!=',$attendanceId)->
                where(function ($query) use ($attendanceMaster) {
                    $query->where('attendance_master_id', '=', $attendanceMaster->id);
                })->where(function ($query) {
                    $query->where('is_approved', '=', 'Pending')->orWhere('is_approved', '=', 'NO');
                })->first();

        if ($attendanceMaster->availability_status == 2 && ($attendanceDetails->device_type == 'MOBILE' || $attendanceDetails->device_type == 'WEB')) {
            if (empty($attendanceExist)) {
                $masterUpdate = AttendanceMaster::where('id', $attendanceDetails->attendance_master_id)->update(['availability_status' => 1]);
            }
        } else if ($attendanceMaster->availability_status == 3) {

            if (empty($attendanceExist)) {

                $leaves = Leaves::find($attendanceMaster->availability_id);

                if (!empty($leaves)) {
                    $diff = abs(strtotime($leaves->end_date) - strtotime($leaves->start_date));
                    $years = floor($diff / (365 * 60 * 60 * 24));
                    $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
                    $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));

                    if ($days >= 2) {
                        if ($leaves->start_date == $attendanceMaster->date || $leaves->end_date == $attendanceMaster->date) {
                            $leaveUpdate = Leaves::where('id', $attendanceDetails->availability_id)->update(['working_on_leave' => 'working on ' . $attendanceMaster->date]);
                            if ($leaves->start_day == 1) { // 1 = Full day
                                $balance = 1;
                            } else {
                                $balance = 0.5;
                            }
                        } else {
                            $balance = 1;
                        }
                    } else {
                        //$leaveUpdate = Leaves::where('id', $attendanceMaster->availability_id)->update(['leave_status' => 4]);
                        if ($leaves->start_day == 1) {
                            $balance = 1;
                        } else {
                            $balance = 0.5;
                        }
                    }
                    $leaveMaster = LeaveMaster::where('user_id', $leaves->user_id)->where('leave_category_id', $leaves->leave_category_id)->first();

                    //check if half leave and then on half leave check if present for full day
                    if ($leaves->start_date == $attendanceMaster->date || $leaves->end_date == $attendanceMaster->date) {
                        if ($leaves->start_day == 2 || $leaves->start_day == 3 || $leaves->end_day == 2 || $leaves->end_day == 3) {
                            //get total working hours of that day
                            $attendance_details = AttendanceDetail::where('attendance_master_id', $attendanceMaster->id)
                                            ->orderBy('time', 'ASC')->get();
                            $timeDifference = [];
                            foreach ($attendance_details as $dKey => $dValue) {
                                if (($dKey) % 2 == 0) {
                                    if ($dValue->punch_type == 'IN' && !empty(($attendance_details[$dKey + 1])) && $attendance_details[$dKey + 1]->punch_type == 'OUT') {
                                        $inTime = new DateTime($dValue->time);
                                        $outTime = new DateTime($attendance_details[$dKey + 1]->time);
                                        $duration = $inTime->diff($outTime);
                                        $timeDifference[] = $duration->format("%H:%I:%S");
                                    }
                                }
                            }
                            $minutes = 0;
                            foreach ($timeDifference as $time) {
                                list($hour, $minute) = explode(':', $time);
                                $minutes += $hour * 60;
                                $minutes += $minute;
                            }
                            //echo $minutes; die();
                            if ($minutes < 360) {
                                $balance = 0;
                            }
                        }
                    }


                    if ($leaves->leave_category_id != 5 && $balance != 0) {
                        $leaveMasterUpdate = LeaveMaster::where('id', $leaveMaster->id)->update(['balance' => $leaveMaster->balance + $balance]);
                    }
                    if ($balance != 0) {
                        $masterUpdate = AttendanceMaster::where('id', $attendanceDetails->attendance_master_id)->update(['availability_status' => 1, 'availability_id' => NULL]);
                    }
                }
            }
        } else if ($attendanceMaster->availability_status == 6) {

            $leavesDetail = Leaves::whereIn('id', explode(",", $attendanceMaster->availability_id))->get();

            foreach ($leavesDetail as $key => $value) {
                if ($attendanceMaster->total_hours >= strtotime(config('app.FULL_WORKING_HOURS'))) {
                    $balance = 0.5;
                    $leaveUpdate = Leaves::where('id', $value->id)->update(['leave_status' => 4]);

                    $leaveMaster = LeaveMaster::where('user_id', $value->user_id)->where('leave_category_id', $value->leave_category_id)->first();

                    $leaveMasterUpdate = LeaveMaster::where('id', $leaveMaster->id)->update(['balance' => $leaveMaster->balance + $balance]);
                } else if (($attendanceMaster->total_hours >= strtotime(config('app.HALF_WORKING_HOURS'))) && ($value->start_day == 2)) {
                    $balance = 0.5;
                    $leaveUpdate = Leaves::where('id', $value->id)->update(['leave_status' => 4]);

                    $leaveMaster = LeaveMaster::where('user_id', $value->user_id)->where('leave_category_id', $value->leave_category_id)->first();

                    $leaveMasterUpdate = LeaveMaster::where('id', $leaveMaster->id)->update(['balance' => $leaveMaster->balance + $balance]);
                }
            }
        } else if ($attendanceMaster->availability_status == 4 || $attendanceMaster->availability_status == 5) {

            if (empty($attendanceExist)) {

                if (strtotime($attendanceMaster->total_hours) > strtotime(config('app.HALF_WORKING_HOURS'))) {
                    $leaveType = 'FULL';
                    $balance = 1;

                    $approval_detail = \App\WorkOff_AttendanceRequest::where('date', date('Y-m-d', strtotime($attendanceMaster->date)))
                            ->where('user_id', $attendanceMaster->user_id)
                            ->where('status', 'Approved')
                            ->get();
                    if ($approval_detail->count() > 0) {
                        if ($approval_detail[0]->day_type != 'Full Day') {
                            $leaveType = 'HALF';
                            $balance = 0.5;
                        }
                    }
                } else {
                    $leaveType = 'HALF';
                    $balance = 0.5;
                }



                $compoffLeave = [$leaveType, date('Y-m-d', strtotime($attendanceMaster->date . ' + 90 days'))];

                $leaveDetails = LeaveMaster::where('leave_category_id', 6)->where('user_id', '=', $attendanceMaster->user_id)->first();

                if (!empty($leaveDetails)) {
                    $serializeLeave = unserialize($leaveDetails->expiry_date);
                    $serializeLeave[] = $compoffLeave;
                    $leaveUpdate = LeaveMaster::where('leave_category_id', 6)->where('user_id', '=', $attendanceMaster->user_id)->update(['balance' => $balance + $leaveDetails->balance, 'expiry_date' => serialize($serializeLeave)]);
                } else {
                    $leaveAdd = new LeaveMaster();
                    $leaveAdd->user_id = $attendanceMaster->user_id;
                    $leaveAdd->leave_category_id = 6;
                    $leaveAdd->balance = $balance;
                    $leaveAdd->expiry_date = serialize($compoffLeave);
                    $leaveAdd->created_at = date('Y-m-d H:i:s');
                    $leaveAdd->created_ip = $request->ip();
                    $leaveAdd->updated_at = date('Y-m-d H:i:s');
                    $leaveAdd->updated_ip = $request->ip();
                    $leaveAdd->save();
                }
                $userDetails = User::select('email', 'name')->where('id', $attendanceMaster->user_id)->get()->first()->toArray();

                $mail_data = [];
                $mail_data['name'] = $userDetails['name'];
                $mail_data['leave_type'] = $leaveType;
                $mail_data['date'] = $attendanceMaster->date;
                $mail_data['hr_email'] = user::where('status', 'Enabled')->where('role', config('constants.REAL_HR'))->pluck('email')->toArray();
                $mail_data['to_email'] = $userDetails['email'];
                $mail_data['admin_email'] = user::where('status', 'Enabled')->where('role', config('constants.SuperUser'))->pluck('email')->toArray();

                $this->common_task->compoffEmail($mail_data);
            }
        }

        return true;
    }

    public function countSundays($monthDays, $year, $month) {

        $sundays = 0;
        for ($i = 1; $i <= $monthDays; $i++) {
            $date = $year . '/' . $month . '/' . $i; //format date
            $get_name = date('l', strtotime($date)); //get week day
            $day_name = substr($get_name, 0, 3); // Trim day name to 3 chars
            if ($day_name == 'Sun') {
                $sundays++;
            }
        }
        return $sundays;
    }

    public function get_payroll() {
        $this->data['page_title'] = "Payroll";
        return view('admin.attendance.payroll_index', $this->data);
    }

    public function get_payroll_list() {
        $conditions_array = [];
        if (Auth::user()->role == config('constants.REAL_HR')) {
            $datatable_fields = array('users.id','payroll.cheque_no', 'users.name', 'payroll.month', 'payroll.year', 'payroll.basic_salary', 'payroll.hra', 'payroll.others', 'payroll.food', 'payroll.working_day', 'payroll.employee_working_day', 'payroll.total_leave', 'payroll.total_sandwich_leave', 'payroll.unpaid_leave', 'payroll.unpaid_leave_amount', 'payroll.professional_tax', 'payroll.pf', 'payroll.loan_installment', 'payroll.extra_loan_amount', 'payroll.extra_loan_details', 'payroll.penalty', 'payroll.manual_penalty', 'payroll.penalty_note', 'payroll.payable_salary', 'payroll.basic_salary', 'payroll.first_approval_status', 'payroll.main_approval_status', 'payroll.date');
        } elseif (Auth::user()->role == config('constants.ASSISTANT')) {
            $datatable_fields = array('users.id','payroll.cheque_no', 'users.name', 'payroll.month', 'payroll.year', 'payroll.basic_salary', 'payroll.hra', 'payroll.others', 'payroll.food', 'payroll.working_day', 'payroll.employee_working_day', 'payroll.total_leave', 'payroll.total_sandwich_leave', 'payroll.unpaid_leave', 'payroll.unpaid_leave_amount', 'payroll.professional_tax', 'payroll.pf', 'payroll.loan_installment', 'payroll.extra_loan_amount', 'payroll.extra_loan_details', 'payroll.penalty', 'payroll.manual_penalty', 'payroll.penalty_note', 'payroll.payable_salary', 'payroll.basic_salary', 'payroll.second_approval_status', 'payroll.main_approval_status', 'payroll.date');
        } elseif (Auth::user()->role == config('constants.Admin')) {
            $datatable_fields = array('users.id','payroll.cheque_no', 'users.name', 'payroll.month', 'payroll.year', 'payroll.basic_salary', 'payroll.hra', 'payroll.others', 'payroll.food', 'payroll.working_day', 'payroll.employee_working_day', 'payroll.total_leave', 'payroll.total_sandwich_leave', 'payroll.unpaid_leave', 'payroll.unpaid_leave_amount', 'payroll.professional_tax', 'payroll.pf', 'payroll.loan_installment', 'payroll.extra_loan_amount', 'payroll.extra_loan_details', 'payroll.penalty', 'payroll.manual_penalty', 'payroll.penalty_note', 'payroll.payable_salary', 'payroll.basic_salary', 'payroll.third_approval_status', 'payroll.main_approval_status', 'payroll.date');
        } elseif (Auth::user()->role == config('constants.SuperUser')) {
            $datatable_fields = array('users.id','payroll.cheque_no', 'users.name', 'payroll.month', 'payroll.year', 'payroll.basic_salary', 'payroll.hra', 'payroll.others', 'payroll.food', 'payroll.working_day', 'payroll.employee_working_day', 'payroll.total_leave', 'payroll.total_sandwich_leave', 'payroll.unpaid_leave', 'payroll.unpaid_leave_amount', 'payroll.professional_tax', 'payroll.pf', 'payroll.loan_installment', 'payroll.extra_loan_amount', 'payroll.extra_loan_details', 'payroll.penalty', 'payroll.manual_penalty', 'payroll.penalty_note', 'payroll.payable_salary', 'payroll.basic_salary', 'payroll.fourth_approval_status', 'payroll.main_approval_status', 'payroll.date');
        } else {
            $datatable_fields = array('users.id','payroll.cheque_no', 'users.name', 'payroll.month', 'payroll.year', 'payroll.basic_salary', 'payroll.hra', 'payroll.others', 'payroll.food', 'payroll.working_day', 'payroll.employee_working_day', 'payroll.total_leave', 'payroll.total_sandwich_leave', 'payroll.unpaid_leave', 'payroll.unpaid_leave_amount', 'payroll.professional_tax', 'payroll.pf', 'payroll.loan_installment', 'payroll.extra_loan_amount', 'payroll.extra_loan_details', 'payroll.penalty', 'payroll.manual_penalty', 'payroll.penalty_note', 'payroll.payable_salary', 'payroll.basic_salary', 'payroll.fifth_approval_status', 'payroll.main_approval_status', 'payroll.date');
        }
        $request = Input::all();
        
        if (Auth::user()->role == config('constants.REAL_HR')) {
            $conditions_array = [];
        } elseif (Auth::user()->role == config('constants.ASSISTANT')) {
            $conditions_array = ['payroll.first_approval_status'=>'Approved'];
        } elseif (Auth::user()->role == config('constants.Admin')) {
            $conditions_array = ['payroll.first_approval_status'=>'Approved','payroll.second_approval_status'=>'Approved'];
        } elseif (Auth::user()->role == config('constants.SuperUser')) {
            $conditions_array = ['payroll.first_approval_status'=>'Approved','payroll.second_approval_status'=>'Approved','payroll.third_approval_status'=>'Approved'];
        } elseif(Auth::user()->role == config('constants.ACCOUNT_ROLE')) {
            $conditions_array = ['payroll.first_approval_status'=>'Approved','payroll.second_approval_status'=>'Approved','payroll.third_approval_status'=>'Approved','payroll.fourth_approval_status'=>'Approved'];
        }

        $join_str[0]['table'] = 'users';
        $join_str[0]['join_table_id'] = 'users.id';
        $join_str[0]['from_table_id'] = 'payroll.user_id';
        $join_str[0]['join_type'] = '';

        $join_str[1]['table'] = 'loan_transaction';
        $join_str[1]['join_table_id'] = 'loan_transaction.payroll_id';
        $join_str[1]['from_table_id'] = 'payroll.id';
        $join_str[1]['join_type'] = 'left';

        $getfiled = array(DB::raw("(payroll.basic_salary+payroll.hra+payroll.others) as gross_salary"),'payroll.cheque_no', 'payroll.total_month_days', 'payroll.employer_pf', 'payroll.total_paid_days', 'payroll.id', 'payroll.salary_ctc', 'payroll.date', 'payroll.reject_note', 'users.name', 'payroll.user_id', 'payroll.month', 'payroll.year', 'payroll.basic_salary', 'payroll.hra', 'payroll.others', 'payroll.food', 'payroll.extra_loan_amount', 'payroll.extra_loan_details', 'payroll.working_day', 'payroll.employee_working_day', 'payroll.total_leave', 'payroll.total_sandwich_leave', 'payroll.unpaid_leave', 'payroll.unpaid_leave_amount', 'payroll.professional_tax', 'payroll.pf', 'payroll.loan_installment', 'payroll.penalty', 'payroll.manual_penalty', 'payroll.penalty_note', 'payroll.payable_salary', 'payroll.is_locked', 'payroll.fifth_approval_status', 'payroll.fourth_approval_status', 'payroll.third_approval_status', 'payroll.second_approval_status', 'payroll.first_approval_status', 'payroll.main_approval_status', 'payroll.loan_pause', 'payroll.first_approval_datetime', 'payroll.second_approval_datetime', 'payroll.third_approval_datetime', 'payroll.fourth_approval_datetime', 'payroll.fifth_approval_datetime');

        $table = "payroll";

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);

        die();
    }

    public function payroll_approve($id, Request $request) {
        $notify_user = [];
        if (Auth::user()->role == config('constants.REAL_HR')) {
            $update_arr = [
                'first_approval_status' => 'Approved',
                'first_approval_id' => Auth::user()->id,
                'first_approval_datetime' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id
            ];
            $notify_user = User::where('role', config('constants.ASSISTANT'))->get(['id'])->pluck('id')->toArray();
        } elseif (Auth::user()->role == config('constants.ASSISTANT')) {
            $update_arr = [
                'second_approval_status' => 'Approved',
                'second_approval_id' => Auth::user()->id,
                'second_approval_datetime' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id
            ];
            $notify_user = User::where('role', config('constants.Admin'))->get(['id'])->pluck('id')->toArray();
        } elseif (Auth::user()->role == config('constants.Admin')) {
            $update_arr = [
                'third_approval_status' => 'Approved',
                'third_approval_id' => Auth::user()->id,
                'third_approval_datetime' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id
            ];
            $notify_user = User::where('role', config('constants.SuperUser'))->get(['id'])->pluck('id')->toArray();
        } elseif (Auth::user()->role == config('constants.SuperUser')) {
            $update_arr = [
                'fourth_approval_status' => 'Approved',
                'fourth_approval_id' => Auth::user()->id,
                'fourth_approval_datetime' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id
            ];
            $notify_user = User::where('role', config('constants.ACCOUNT_ROLE'))->get(['id'])->pluck('id')->toArray();
        } elseif (Auth::user()->role == config('constants.ACCOUNT_ROLE')) {
            $update_arr = [
                'fifth_approval_status' => 'Approved',
                'fifth_approval_id' => Auth::user()->id,
                'fifth_approval_datetime' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id,
                'main_approval_status' => 'Approved',
                'is_locked' => 'YES'
            ];
        } else {
            return redirect()->route('admin.dashboard')->with('error', 'Access denied. You are not allowed to access this module.');
        }
        if (!empty($notify_user)) {
            $this->notification_task->PayrollApprovalNotify($notify_user);
        }
        Payroll::where('id', $id)->update($update_arr);
        return redirect()->route('admin.get_payroll')->with('success', 'Salary successfully approved.');
    }

    public function payroll_approve_all(Request $request) {

        $itemselections = explode(",",$request->get('itemselectionsAll'));
        if (!$request->get('itemselectionsAll')) {
            return redirect()->route('admin.get_payroll')->with('error', 'Please follow validation rules.');
        }
        $notify_user = [];
        foreach ($itemselections as $id) {

            if (Auth::user()->role == config('constants.REAL_HR')) {
                $update_arr = [
                    'first_approval_status' => 'Approved',
                    'first_approval_id' => Auth::user()->id,
                    'first_approval_datetime' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                    'updated_by' => Auth::user()->id
                ];
                $notify_user = User::where('role', config('constants.ASSISTANT'))->get(['id'])->pluck('id')->toArray();
            } elseif (Auth::user()->role == config('constants.ASSISTANT')) {
                $update_arr = [
                    'second_approval_status' => 'Approved',
                    'second_approval_id' => Auth::user()->id,
                    'secpnd_approval_datetime' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                    'updated_by' => Auth::user()->id
                ];
                $notify_user = User::where('role', config('constants.Admin'))->get(['id'])->pluck('id')->toArray();
            } elseif (Auth::user()->role == config('constants.Admin')) {
                $update_arr = [
                    'third_approval_status' => 'Approved',
                    'third_approval_id' => Auth::user()->id,
                    'third_approval_datetime' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                    'updated_by' => Auth::user()->id
                ];
                $notify_user = User::where('role', config('constants.SuperUser'))->get(['id'])->pluck('id')->toArray();
            } elseif (Auth::user()->role == config('constants.SuperUser')) {
                $update_arr = [
                    'fourth_approval_status' => 'Approved',
                    'fourth_approval_id' => Auth::user()->id,
                    'fourth_approval_datetime' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                    'updated_by' => Auth::user()->id
                ];
                $notify_user = User::where('role', config('constants.ACCOUNT_ROLE'))->get(['id'])->pluck('id')->toArray();
            } elseif (Auth::user()->role == config('constants.ACCOUNT_ROLE')) {
                $update_arr = [
                    'fifth_approval_status' => 'Approved',
                    'fifth_approval_id' => Auth::user()->id,
                    'fifth_approval_datetime' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                    'updated_by' => Auth::user()->id,
                    'main_approval_status' => 'Approved',
                    'is_locked' => 'YES'
                ];
            }

            Payroll::where('id', $id)->update($update_arr);
        }
        if (!empty($notify_user)) {
            $this->notification_task->PayrollApprovalNotify($notify_user);
        }
        return redirect()->route('admin.get_payroll')->with('success', 'Salary successfully approved for all selected record.');
    }

    public function payroll_reject(Request $request) {
        $request_data = $request->all();
        if (Auth::user()->role == config('constants.REAL_HR')) {
            $update_arr = [
                'first_approval_status' => 'Rejected',
                'first_approval_id' => Auth::user()->id,
                'first_approval_datetime' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id,
                'main_approval_status' => 'Rejected',
                'reject_note' => $request_data['reject_note']
            ];
        } elseif (Auth::user()->role == config('constants.ASSISTANT')) {
            $update_arr = [
                'second_approval_status' => 'Rejected',
                'second_approval_id' => Auth::user()->id,
                'second_approval_datetime' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id,
                'main_approval_status' => 'Rejected',
                'reject_note' => $request_data['reject_note']
            ];
        } elseif (Auth::user()->role == config('constants.Admin')) {
            $update_arr = [
                'third_approval_status' => 'Rejected',
                'third_approval_id' => Auth::user()->id,
                'third_approval_datetime' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id,
                'main_approval_status' => 'Rejected',
                'reject_note' => $request_data['reject_note']
            ];
        } elseif (Auth::user()->role == config('constants.SuperUser')) {
            $update_arr = [
                'fourth_approval_status' => 'Rejected',
                'fourth_approval_id' => Auth::user()->id,
                'fourth_approval_datetime' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id,
                'main_approval_status' => 'Rejected',
                'reject_note' => $request_data['reject_note']
            ];
        } elseif (Auth::user()->role == config('constants.ACCOUNT_ROLE')) {
            $update_arr = [
                'fifth_approval_status' => 'Rejected',
                'fifth_approval_id' => Auth::user()->id,
                'fifth_approval_datetime' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id,
                'main_approval_status' => 'Rejected',
                'reject_note' => $request_data['reject_note']
            ];
        } else {
            return redirect()->route('admin.dashboard')->with('error', 'Access denied. You are not allowed to access this module.');
        }
        Payroll::where('id', $request_data['payroll_id'])->update($update_arr);
        return redirect()->route('admin.get_payroll')->with('success', 'Salary is rejected and send for review.');
    }

    public function lock_payroll($id) {
        if (Payroll::where('id', $id)->update(['is_locked' => 'YES'])) {
            return redirect()->route('admin.get_payroll')->with('success', 'Successfully Locked.');
        }
        return redirect()->route('admin.get_payroll')->with('error', 'Error during operation. Try again!');
    }

    public function edit_payroll($id) {
        $this->data['page_title'] = "Edit payroll";
        $this->data['payroll_detail'] = Payroll::where('id', $id)->with('user')->get();

        if ($this->data['payroll_detail']->count() == 0) {
            return redirect()->route('admin.get_payroll')->with('error', 'Error Occurred. Try Again!');
        }
        return view('admin.attendance.edit_payroll', $this->data);
    }

    public function update_payroll(Request $request) {

        $validator_normal = Validator::make($request->all(), [
                    'hra' => 'required',
                    'others' => 'required',
                    'food' => 'required',
                    'penalty' => 'required',
                    'manual_penalty' => 'required',
        ]);

        if ($validator_normal->fails()) {
            return redirect()->route('admin.get_payroll')->with('error', 'Please follow validation rules.');
        }

        $payrollData = Payroll::find($request->input('id'));
        $empSalary = EmployeesSalary::where('user_id', $payrollData->user_id)->orderBy('id', 'DESC')->get()->first();
        if ($payrollData->month == 4 && $payrollData->year == 2020) {
            if ($payrollData->user_id == 83 || $payrollData->user_id == 85) {
                $percent_salary = 50;
            } else {
                $percent_salary = 25;
            }
            $empSalary->basic_salary = ($percent_salary * $empSalary->basic_salary) / 100;
            $empSalary->hra = ($percent_salary * $empSalary->hra) / 100;
            $empSalary->other_allowance = ($percent_salary * $empSalary->other_allowance) / 100;
            $empSalary->total_month_salary = ($percent_salary * $empSalary->total_month_salary) / 100;
            $empSalary->gross_salary_pm_ctc = ($percent_salary * $empSalary->gross_salary_pm_ctc) / 100;
        }

        if ($empSalary->salaray_category == 2) {
            $original_ctc = $empSalary->gross_salary_pm_ctc;
            $empSalary->gross_salary_pm_ctc = $empSalary->gross_salary_pm_ctc - $payrollData->employer_pf;
        } else {
            $original_ctc = $empSalary->gross_salary_pm_ctc = $empSalary->total_month_salary + $payrollData->employer_pf;
        }




        //$newPayableSalary = ($payrollData->basic_salary + $request->input('hra') + $request->input('others') + $request->input('food')) - ($payrollData->installment + $payrollData->unpaid_leave_amount + $request->input('penalty') + $request->input('manual_penalty') + $payrollData->pf);
        $newPayableSalary = ((($original_ctc / $payrollData->total_month_days) * $payrollData->total_paid_days) + $request->input('food')) - ($payrollData->employer_pf + $request->input('penalty') + $request->input('manual_penalty') + $payrollData->pf);

        if ($newPayableSalary >= 0 && 5999 >= $newPayableSalary) {
            $professionalTax = 0;
        } else if ($newPayableSalary >= 6000 && 8999 >= $newPayableSalary) {
            $professionalTax = 80;
        } else if ($newPayableSalary >= 9000 && 11999 >= $newPayableSalary) {
            $professionalTax = 150;
        } else if ($newPayableSalary >= 12000) {
            $professionalTax = 200;
        } else {
            $professionalTax = 0;
        }
        $newPayableSalary = $newPayableSalary - $professionalTax;

        $newPayableSalary = $newPayableSalary - $payrollData->loan_installment;

        if ($request->input('extra_loan_amount') && $request->input('extra_loan_amount') > 0) {
            $newPayableSalary = $newPayableSalary - $request->input('extra_loan_amount');
        }

        $payrollModel = [
            'penalty' => $request->input('penalty'),
            'manual_penalty' => $request->input('manual_penalty'),
            'payable_salary' => $newPayableSalary,
            'hra' => $request->input('hra'),
            'others' => $request->input('others'),
            'food' => $request->input('food'),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
            'first_approval_status' => 'Pending',
            'second_approval_status' => 'Pending',
            'third_approval_status' => 'Pending',
            'main_approval_status' => 'Pending',
            'is_locked' => 'No',
            'professional_tax' => $professionalTax,
            'extra_loan_amount' => $request->input('extra_loan_amount'),
            'extra_loan_details' => $request->input('extra_loan_details'),
            'salary_ctc' => $original_ctc
        ];

        Payroll::where('id', $request->input('id'))->update($payrollModel);

        return redirect()->route('admin.get_payroll')->with('success', 'Salary successfully updated.');
    }

    public function get_salary_slip() {
        $this->data['page_title'] = "Salary Slip";
        $this->data['months'] = config('app.hours');
        $this->data['year'] = config('app.year');
        $permission_arr = $this->common_task->getPermissionArr(Auth::user()->role, 30);

        $this->data['user_list'] = [];
        if (in_array(5, $permission_arr)) {
            $this->data['user_list'] = User::orderBy('name')->where('role', '!=', config('constants.SuperUser'))
                    ->get();
        }


        return view('admin.attendance.salary_slip', $this->data);
    }

    public function download_salary(Request $request) {
        $validator_normal = Validator::make($request->all(), [
                    'month' => 'required',
                    'year' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.get_salary_slip')->with('error', 'Please follow validation rules.');
        }
        if ($request->input('user_id')) {
            $payrollData = Payroll::where('year', $request->input('year'))
                            ->where('month', $request->input('month'))
                            ->where('user_id', $request->input('user_id'))->with(['user' => function($query) {
                            return $query->with(['employee' => function($query1) {
                                            return $query1->with('department');
                                        }, 'employee_bank']);
                        }])->where('is_locked', 'YES')->get()->first();
        } else {
            $payrollData = Payroll::where('year', $request->input('year'))->where('month', $request->input('month'))->where('user_id', Auth::user()->id)->with(['user' => function($query) {
                            return $query->with('employee', 'employee_bank');
                        }])->where('is_locked', 'YES')->get()->first();
        }
        if (empty($payrollData)) {
            return redirect()->route('admin.get_salary_slip')->with('error', 'No record available for given month and year. Please try again.');
        }
        $this->data['data'] = $payrollData;
        $this->data['display_month'] = date('M', strtotime($request->input('month')));
        $this->data['display_year'] = date('Y', strtotime($request->input('year')));
        $date_m_y = $request->input('year') . "-" . $request->input('month');
        $this->data['display_month_year'] = date('M Y', strtotime($date_m_y));
        // dd($this->data);
        //return view('admin.attendance.pdfview', $this->data);
        $pdf = PDF::loadView('admin.attendance.pdfview', $this->data)->setPaper('A3', 'landscape');
        return $pdf->download($request->input('month') . '/' . $request->input('year') . '.pdf');
    }

    public function pause_loan($id, Request $request) {
        $payroll_record = Payroll::where('id', $id)->get();
        if ($payroll_record->count() == 0) {
            return redirect()->route('admin.get_payroll')->with('error', 'Something went wrong. Try Again!');
        }

        //remove loan transaction entry
        LoanTransaction::where('payroll_id', $id)->delete();

        //update loan amount and completed loan terms
        $loan_details = EmployeesLoans::where('user_id', $payroll_record[0]->user_id)
                ->where('loan_terms', '!=', 'completed_loan_terms')
                ->where('loan_status', 'Approved')
                ->get();
        if ($loan_details->count() == 0) {
            return redirect()->route('admin.get_payroll')->with('error', 'Something went wrong. Try Again!');
        }
        $loan_update_arr = [
            'completed_loan_amount' => $loan_details[0]->completed_loan_amount - $payroll_record[0]->loan_installment,
            'completed_loan_terms' => $loan_details[0]->completed_loan_terms - 1,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip()
        ];
        EmployeesLoans::where('id', $loan_details[0]->id)
                ->update($loan_update_arr);

        //update payroll
        $payroll_update_data = [
            'loan_installment' => 0.00,
            'loan_pause' => 1,
            'payable_salary' => $payroll_record[0]->payable_salary + $payroll_record[0]->loan_installment,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id
        ];
        Payroll::where('id', $id)->update($payroll_update_data);

        return redirect()->route('admin.get_payroll')->with('success', 'Loan installment is paused successfully. Payroll is changed accordingly.');
    }

    public function resume_loan($id, Request $request) {
        $payroll_record = Payroll::where('id', $id)->get();
        if ($payroll_record->count() == 0) {
            return redirect()->route('admin.get_payroll')->with('error', 'Something went wrong. Try Again!');
        }
        $loan_details = EmployeesLoans::where('user_id', $payroll_record[0]->user_id)
                ->where('loan_terms', '!=', 'completed_loan_terms')
                ->where('loan_status', 'Approved')
                ->get();
        if ($loan_details->count() == 0) {
            return redirect()->route('admin.get_payroll')->with('error', 'Something went wrong. Try Again!');
        }
        $installment = ($loan_details[0]->loan_amount / $loan_details[0]->loan_terms);
        //insert loan transaction entry
        $insert_arr = [
            'user_id' => $payroll_record[0]->user_id,
            'loan_id' => $loan_details[0]->id,
            'payroll_id' => $payroll_record[0]->id,
            'month' => date('m'),
            'year' => date('Y'),
            'amount' => $installment,
            'date' => date('Y-m-d'),
            'status' => 'Enabled',
            'created_at' => date('Y-m-d H:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
        ];
        LoanTransaction::insert($insert_arr);

        //update loan amount and completed loan terms

        $loan_update_arr = [
            'completed_loan_amount' => $loan_details[0]->completed_loan_amount + $installment,
            'completed_loan_terms' => $loan_details[0]->completed_loan_terms + 1,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip()
        ];
        EmployeesLoans::where('id', $loan_details[0]->id)
                ->update($loan_update_arr);

        //update payroll
        $payroll_update_data = [
            'loan_installment' => $installment,
            'loan_pause' => 0,
            'payable_salary' => $payroll_record[0]->payable_salary - $installment,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id
        ];
        Payroll::where('id', $id)->update($payroll_update_data);

        return redirect()->route('admin.get_payroll')->with('success', 'Loan installment is deducted successfully. Payroll is changed accordingly.');
    }

    //16/09/2020

    public function get_payroll_details(Request $request)  //24-03-2020
    {
        $validator_normal = Validator::make($request->all(), ['id' => 'required']);

        if ($validator_normal->fails()) {
            return response()->json(['status' => false]);
        }

        $id = $request->id;
        $companies = \App\Companies::select('id', 'company_name')->orderBy('company_name', 'asc')->get()->toArray();
        $html = "<option value=''>Select Company</option>";
            foreach ($companies as $key => $company) {
                 $html.= "<option value=".$company['id'].">".$company['company_name']."</option>";
            }
        $this->data['company_list'] = $html;
        $payroll_details = Payroll::leftjoin('cheque_register','cheque_register.id','=','payroll.cheque_no')
                ->where('payroll.id',$id)
                ->get(['payroll.id','payroll.cheque_no','cheque_register.ch_no','payroll.payment_details'])->first();
        

        $this->data['payroll_details'] = $payroll_details;
        return response()->json(['status' => true, 'data' => $this->data]);
        // if ($bank_payment_records) {
            
        /* } else {
            return response()->json(['status' => false]);
        } */

    }
    public function submit_payments_details(Request $request) {
        $validator_normal = Validator::make($request->all(), [
            'payroll_ids' => 'required',
            'cheque_number' => 'required',
            'payment_details' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.get_payroll')->with('error', 'Please follow validation rules.');
        }
        $request_data = $request->all();
        $update_data = [
            'cheque_no' => $request_data['cheque_number'],
            'payment_details' => $request_data['payment_details'],
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id
        ];
        
        if (Payroll::whereIn('id', explode(",",$request_data['payroll_ids']))->update($update_data)) {
            return redirect()->route('admin.get_payroll')->with('success', 'Payment details successfully submitted');
        }

        return redirect()->route('admin.get_payroll')->with('error', 'Error Occurred. Try Again!');
        
    }

    public function get_company_bank_list_ajax(Request $request)
    {
        $company_id = $request->get('company_id');
        if(!empty($company_id)) {
            
           $bank_data = \App\Banks::select('bank_name','id','ac_number')
           ->where(['company_id' => $company_id])
           ->get()->toArray();
           $html = "";
           foreach ($bank_data as $key => $bank_data_value) {
                $html.= "<option value=".$bank_data_value['id'].">".$bank_data_value['bank_name']." (".$bank_data_value['ac_number'].")"."</option>";
           }
           echo  $html;
           die();
        }
    }

    public function payroll_generate_hr(){
        $year = date('Y', strtotime('-15 day', strtotime(date('Y-m-d'))));
        $month = date('m', strtotime('-15 day', strtotime(date('Y-m-d'))));
        
        $check_payroll = Payroll::where('month',$month)->where('year',$year)->count();
        
        if($check_payroll == 0){
            // $url = "wget -q -O /dev/null http://139.59.8.252/payroll";
            // dd(\URL::to('/'));
            $url = 'wget -q -O /dev/null http://139.59.8.252/payroll';
            
            exec($url, $output, $return);
            
            if (!$return) {
                return response()->json(['status' => true, 'message' => "Payroll genereted succssfully."]);
            } else {
                return response()->json(['status' => false, 'message' => "Error occurre in generate payroll. Try Again."]);
            }
        }else{
            return response()->json(['status' => false, 'message' => "Previous month payroll already genereted."]);
        }
    }
}
