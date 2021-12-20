<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use App\User;
use DateTime;
use App\Login_log;
use App\Leaves;
use App\Inward_outward_distrubuted_work;
use App\AttendanceMaster;
use App\AttendanceDetail;
use App\Holiday;
use App\TbltTimesheet;
use App\Employees;
use App\LeaveCategory;
use App\LeaveMaster;
use App\EmployeesSalary;
use App\EmployeesLoans;
use App\Payroll;
use App\Lib\CommonTask;
use App\Lib\NotificationTask;
use Carbon\Carbon;
use App\LoanTransaction;
use App\Vehicle_Insurance;
use App\Employee_Insurance;
use App\Employee_insurance_types;
use App\Document_softcopy_access;
use App\DocumentSoftcopy;
use App\AssetAccess;
use App\BankPaymentApproval;
use App\CashApproval;
use App\ChequeRegister;
use App\Companies;
use App\Clients;
use App\Projects;
use App\Project_sites;
use App\Vendors;
use App\Tender;
use App\EarnedLeave;
use App\Resignation;
use App\Compliance_reminders;
use App\Compliance_reminders_done_status;
use App\UserActionLog;
use App\DayPayroll;
use App\Http\Controllers\Admin\BankReconciliationController;
use App\OnlinePaymentApproval;

class SiteParseController extends Controller
{

    private $common_task;
    private $notification_task;

    public function __construct()
    {
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();
    }

    public function insertChequeNoToId()
    {

        $records = BankPaymentApproval::select('id', 'cheque_number')->get()->toArray();
        foreach ($records as $key => $value) {
            $entry_count = ChequeRegister::where('ch_no', $value['cheque_number'])->get();
            if ($entry_count->count() == 1) {
                BankPaymentApproval::where('id', $value['id'])->update(['cheque_number' => $entry_count[0]->id]);
            }
        }
    }

    public function yearlyLeaveBalance(Request $request)
    {

        $today = date('Y-m-d');
        $yearFirstDate = date('Y-01-01');

        if ($yearFirstDate == $today) {
            $leaveYearly = LeaveCategory::where('frequency', '=', 'Yearly')->get();
            $userList = User::where('status', 'Enabled')->get();

            if (!empty($leaveYearly)) {
                foreach ($leaveYearly as $leaveKey => $leaveValue) {
                    foreach ($userList as $userKey => $userValue) {
                        $leaveMaster = LeaveMaster::where('leave_category_id', '=', $leaveValue->id)->where('user_id', '=', $userValue->id)->get()->first();

                        if (!empty($leaveMaster)) {
                            $leaveUpdate = LeaveMaster::where('id', $leaveMaster->id)->update(['balance' => $leaveMaster->balance + $leaveValue->quantity]);
                        } else {
                            $leaveAdd = new LeaveMaster();
                            $leaveAdd->user_id = $userValue->id;
                            $leaveAdd->leave_category_id = $leaveValue->id;
                            $leaveAdd->balance = $leaveValue->quantity;
                            $leaveAdd->created_at = date('Y-m-d H:i:s');
                            $leaveAdd->created_ip = $request->ip();
                            $leaveAdd->updated_at = date('Y-m-d H:i:s');
                            $leaveAdd->updated_ip = $request->ip();
                            $leaveAdd->save();
                        }
                    }
                }
            }
        }
    }

    public function monthlyLeaveBalance(Request $request)
    {

        $today = date('Y-m-d');
        $monthFirstDate = date('Y-m-01');

        if ($monthFirstDate == $today) {
            $leaveMonthly = LeaveCategory::where('frequency', '=', 'Monthly')->get();
            $userList = User::where('status', 'Enabled')->get();

            if (!empty($leaveMonthly)) {
                foreach ($leaveMonthly as $leaveKey => $leaveValue) {
                    foreach ($userList as $userKey => $userValue) {
                        $leaveMaster = LeaveMaster::where('leave_category_id', '=', $leaveValue->id)->where('user_id', '=', $userValue->id)->get()->first();
                        if (!empty($leaveMaster)) {
                            $leaveUpdate = LeaveMaster::where('id', $leaveMaster->id)->update(['balance' => $leaveMaster->balance + $leaveValue->quantity]);
                        } else {
                            $leaveAdd = new LeaveMaster();
                            $leaveAdd->user_id = $userValue->id;
                            $leaveAdd->leave_category_id = $leaveValue->id;
                            $leaveAdd->balance = $leaveValue->quantity;
                            $leaveAdd->created_at = date('Y-m-d H:i:s');
                            $leaveAdd->created_ip = $request->ip();
                            $leaveAdd->updated_at = date('Y-m-d H:i:s');
                            $leaveAdd->updated_ip = $request->ip();
                            $leaveAdd->save();
                        }
                    }
                }
            }
        }
    }

    public function shortLeaveBalance(Request $request)
    {

        $today = date('Y-m-d');
        $date22nd = date('Y-m-01');

        if ($date22nd == $today) {
            $leaveShort = LeaveCategory::where('frequency', '=', 'Short')->get();
            $userList = User::where('status', 'Enabled')->get();

            if (!empty($leaveShort)) {
                foreach ($leaveShort as $leaveKey => $leaveValue) {
                    foreach ($userList as $userKey => $userValue) {
                        $leaveMaster = LeaveMaster::where('leave_category_id', '=', $leaveValue->id)->where('user_id', '=', $userValue->id)->get()->first();
                        if (!empty($leaveMaster)) {
                            $leaveUpdate = LeaveMaster::where('id', $leaveMaster->id)->update(['balance' => $leaveValue->quantity]);
                        } else {
                            $leaveAdd = new LeaveMaster();
                            $leaveAdd->user_id = $userValue->id;
                            $leaveAdd->leave_category_id = $leaveValue->id;
                            $leaveAdd->balance = $leaveValue->quantity;
                            $leaveAdd->created_at = date('Y-m-d H:i:s');
                            $leaveAdd->created_ip = $request->ip();
                            $leaveAdd->updated_at = date('Y-m-d H:i:s');
                            $leaveAdd->updated_ip = $request->ip();
                            $leaveAdd->save();
                        }
                    }
                }
            }
        }
    }

    public function leaveCron(Request $request)
    {
        $today = date('Y-m-d');

        $prevDate = Carbon::today()->subDays(14);

        while (strtotime($prevDate) <= strtotime($today)) {

            if (date('D', strtotime($prevDate)) != 'Sun') {

                $leave_data = Leaves::whereDate('start_date', '<=', $prevDate)->whereDate('end_date', '>=', $prevDate)->where('leave_status', '=', '2')->get()->toArray();
                /* if($prevDate=='2020-01-11'){ 
                  echo '<pre>';
                  print_r($leave_data);
                  echo '<br>';
                  } */
                $leaveDetails = [];
                foreach ($leave_data as $key => $value) {
                    if (array_key_exists($value['user_id'], $leaveDetails)) {
                        $leaveDetails[$value['user_id']]['id'] = implode(",", [$leaveDetails[$value['user_id']]['id'], $value['id']]);
                        $leaveDetails[$value['user_id']]['type'] = 6; // 6 => Mixed Leave
                    } else {
                        $leaveDetails[$value['user_id']]['id'] = $value['id'];
                        $leaveDetails[$value['user_id']]['type'] = 3; // 3 => Leave
                    }
                }

                if (!empty($leaveDetails)) {
                    foreach ($leaveDetails as $key => $leave) {
                        if (!AttendanceMaster::where('date', '=', $prevDate)->where('user_id', $key)->get()->count()) {
                            $attendanceMaster = new AttendanceMaster();
                            $attendanceMaster->user_id = $key;
                            $attendanceMaster->date = $prevDate;
                            $attendanceMaster->availability_status = $leave['type'];
                            $attendanceMaster->availability_id = $leave['id'];
                            $attendanceMaster->created_at = date('Y-m-d H:i:s');
                            $attendanceMaster->created_ip = $request->ip();
                            $attendanceMaster->updated_at = date('Y-m-d H:i:s');
                            $attendanceMaster->updated_ip = $request->ip();
                            $attendanceMaster->save();
                        }
                    }
                }
            }
            $prevDate->modify('+1 day');
        }
    }

    public function holidayCron(Request $request)
    {
        $today = date('Y-m-d');
        $holiday_data = Holiday::whereDate('start_date', '<=', $today)->whereDate('end_date', '>=', $today)->get('id')->first();

        if (!empty($holiday_data) && date('D') != 'Sun') {
            $user_data = User::where('status', 'Enabled')->get('id')->toArray();
            foreach ($user_data as $user) {
                $attendanceMaster = new AttendanceMaster();
                $attendanceMaster->user_id = $user['id'];
                $attendanceMaster->date = $today;
                $attendanceMaster->availability_status = 4; // 4 => Holiday
                $attendanceMaster->availability_id = $holiday_data->id;

                $attendanceMaster->created_at = date('Y-m-d H:i:s');
                $attendanceMaster->created_ip = $request->ip();
                $attendanceMaster->updated_at = date('Y-m-d H:i:s');
                $attendanceMaster->updated_ip = $request->ip();
                $attendanceMaster->save();
            }
        }
    }

    public function weekendCron(Request $request)
    {
        $today = date('Y-m-d');
        if (date('D') == 'Sun') {
            $user_data = User::where('status', 'Enabled')->get('id')->toArray();
            foreach ($user_data as $user) {
                $AttendanceExist = AttendanceMaster::where('date', '=', $today)->where('user_id', '=', $user['id'])->where('availability_status', '=', 4)->get()->first();
                if (!$AttendanceExist) {
                    $attendanceMaster = new AttendanceMaster();
                    $attendanceMaster->user_id = $user['id'];
                    $attendanceMaster->date = $today;
                    $attendanceMaster->availability_status = 5; // 5 => weekend

                    $attendanceMaster->created_at = date('Y-m-d H:i:s');
                    $attendanceMaster->created_ip = $request->ip();
                    $attendanceMaster->updated_at = date('Y-m-d H:i:s');
                    $attendanceMaster->updated_ip = $request->ip();
                    $attendanceMaster->save();
                }
            }
        }
    }

    public function attendanceCron(Request $request)
    {
        $today = date('Y-m-d');
        $lateTime = new DateTime('09:31:00');
        $actual_lateTime = new DateTime('09:30:00');
        $moreLateTime = new DateTime('09:46:00');

        $timeSheet = TbltTimesheet::where('execute', 0)->orderBy('date', 'ASC')->orderBy('time', 'ASC')->get();

        $timeSheetArr = [];
        if (!empty($timeSheet)) {
            foreach ($timeSheet as $key => $value) {
                $user_id = Employees::where('emp_code', $value->punchingcode)->get()->first();
                if (!empty($user_id) && !empty($user_id->user_id)) {
                    $user_id = $user_id->user_id;

                    $check_holiday_weekend = $this->common_task->check_holiday_weekend_attendance($user_id, date('Y-m-d', strtotime($value->date)));
                    if (!$check_holiday_weekend) {
                        continue;
                    }

                    $attendanceMaster = AttendanceMaster::where('date', '=', $value->date)->where('user_id', '=', $user_id)->get(['id', 'availability_status'])->first();
                    if (!$attendanceMaster) {
                        $attendanceMaster = new AttendanceMaster();
                        $attendanceMaster->user_id = $user_id;
                        $attendanceMaster->date = $value->date;
                        $attendanceMaster->availability_status = 1; // 1 => Present;
                        $attendanceMaster->first_in = $value->date . ' ' . $value->time;
                        $attendanceMaster->availability_id = $value->timesheetid;
                        $time = new DateTime($value->time);
                        if ($time > $lateTime && $moreLateTime >= $time) {
                            $attendanceMaster->is_late = 'YES';
                            $duration = $time->diff($actual_lateTime);
                            $attendanceMaster->late_time = $duration->format("%H:%I:%S");
                        } else if ($moreLateTime < $time) {
                            $attendanceMaster->is_late_more = 'YES';
                            //$duration = $time->diff($moreLateTime);
                            $duration = $time->diff($actual_lateTime);
                            $attendanceMaster->late_time = $duration->format("%H:%I:%S");
                        }
                        $attendanceMaster->updated_at = date('Y-m-d H:i:s');
                        $attendanceMaster->updated_ip = $request->ip();
                        $attendanceMaster->save();
                    } else if (in_array($attendanceMaster->availability_status, ['3', '4', '5', '6'])) {
                        // 3=leave, 4=holiday, 5=weekend, 6=mixedleave
                        $AttendanceExist = AttendanceDetail::where('attendance_master_id', '=', $attendanceMaster->id)->get()->count();
                        if (!$AttendanceExist) {
                            $attendanceMaster->first_in = $value->date . ' ' . $value->time;
                            $attendanceMaster->save();
                        }
                    }

                    $AttendanceDExist = AttendanceDetail::where('attendance_master_id', '=', $attendanceMaster->id)->orderBy('time', 'DESC')->get('punch_type')->first();

                    //check if attendance exists upto same second then skip that entry
                    $check_result = AttendanceDetail::where('attendance_master_id', $attendanceMaster->id)->where('time', $value->date . ' ' . $value->time)->get();
                    if ($check_result->count() > 0) {
                        continue;
                    }
                    $attendanceDetail = new AttendanceDetail();
                    $attendanceDetail->attendance_master_id = $attendanceMaster->id;
                    $attendanceDetail->time = $value->date . ' ' . $value->time;
                    $attendanceDetail->punch_type = (!empty($AttendanceDExist)) ? (($AttendanceDExist->punch_type == 'IN') ? "OUT" : "IN") : 'IN';
                    $attendanceDetail->device_type = 'BIOMETRIC';
                    if ($attendanceMaster->availability_status == 1) {  // 1 => Present;
                        $attendanceDetail->is_approved = "YES";
                    }
                    $attendanceDetail->created_at = date('Y-m-d H:i:s');
                    $attendanceDetail->created_ip = $request->ip();
                    $attendanceDetail->updated_at = date('Y-m-d H:i:s');
                    $attendanceDetail->updated_ip = $request->ip();
                    if ($attendanceDetail->save()) {
                        $timeSheetArr[] = $value->timesheetid;
                    }
                }
            }
        }
        TbltTimesheet::whereIn('timesheetid', $timeSheetArr)->update(['execute' => 1]);
    }

    public function punchHoursCron(Request $request)
    {
        $dates[0] = date('Y-m-d');
        $dates[1] = date('Y-m-d', strtotime($dates[0] . ' - 1 days'));
        $dates[2] = date('Y-m-d', strtotime($dates[0] . ' - 2 days'));
        $dates[3] = date('Y-m-d', strtotime($dates[0] . ' - 3 days'));
        $dates[4] = date('Y-m-d', strtotime($dates[0] . ' - 4 days'));
        $dates[5] = date('Y-m-d', strtotime($dates[0] . ' - 5 days'));
        $dates[6] = date('Y-m-d', strtotime($dates[0] . ' - 6 days'));
        $dates[7] = date('Y-m-d', strtotime($dates[0] . ' - 7 days'));
        $dates[8] = date('Y-m-d', strtotime($dates[0] . ' - 8 days'));
        $dates[9] = date('Y-m-d', strtotime($dates[0] . ' - 9 days'));
        $dates[10] = date('Y-m-d', strtotime($dates[0] . ' - 10 days'));
        $dates[11] = date('Y-m-d', strtotime($dates[0] . ' - 11 days'));
        $dates[13] = date('Y-m-d', strtotime($dates[0] . ' - 13 days'));
        $dates[14] = date('Y-m-d', strtotime($dates[0] . ' - 14 days'));
        $dates[15] = date('Y-m-d', strtotime($dates[0] . ' - 15 days'));
        $dates[16] = date('Y-m-d', strtotime($dates[0] . ' - 16 days'));
        $dates[17] = date('Y-m-d', strtotime($dates[0] . ' - 17 days'));
        $dates[18] = date('Y-m-d', strtotime($dates[0] . ' - 18 days'));
        $dates[19] = date('Y-m-d', strtotime($dates[0] . ' - 19 days'));
        $dates[20] = date('Y-m-d', strtotime($dates[0] . ' - 20 days'));
        $dates[21] = date('Y-m-d', strtotime($dates[0] . ' - 21 days'));
        $dates[22] = date('Y-m-d', strtotime($dates[0] . ' - 22 days'));
        $dates[23] = date('Y-m-d', strtotime($dates[0] . ' - 23 days'));
        $dates[24] = date('Y-m-d', strtotime($dates[0] . ' - 24 days'));
        $dates[25] = date('Y-m-d', strtotime($dates[0] . ' - 25 days'));
        $dates[26] = date('Y-m-d', strtotime($dates[0] . ' - 26 days'));
        $dates[27] = date('Y-m-d', strtotime($dates[0] . ' - 27 days'));
        $dates[28] = date('Y-m-d', strtotime($dates[0] . ' - 28 days'));
        $dates[29] = date('Y-m-d', strtotime($dates[0] . ' - 29 days'));
        $dates[30] = date('Y-m-d', strtotime($dates[0] . ' - 30 days'));
        $dates[31] = date('Y-m-d', strtotime($dates[0] . ' - 31 days'));
        $dates[32] = date('Y-m-d', strtotime($dates[0] . ' - 32 days'));
        $dates[33] = date('Y-m-d', strtotime($dates[0] . ' - 33 days'));
        $dates[34] = date('Y-m-d', strtotime($dates[0] . ' - 34 days'));
        $dates[35] = date('Y-m-d', strtotime($dates[0] . ' - 35 days'));
        $dates[36] = date('Y-m-d', strtotime($dates[0] . ' - 36 days'));
        $dates[37] = date('Y-m-d', strtotime($dates[0] . ' - 37 days'));
        $dates[38] = date('Y-m-d', strtotime($dates[0] . ' - 38 days'));
        $dates[39] = date('Y-m-d', strtotime($dates[0] . ' - 39 days'));
        $dates[40] = date('Y-m-d', strtotime($dates[0] . ' - 40 days'));
        $dates[41] = date('Y-m-d', strtotime($dates[0] . ' - 41 days'));
        //        $dates[42] = date('Y-m-d', strtotime($dates[0] . ' - 42 days'));
        //        $dates[43] = date('Y-m-d', strtotime($dates[0] . ' - 43 days'));
        //        $dates[44] = date('Y-m-d', strtotime($dates[0] . ' - 44 days'));
        //        $dates[45] = date('Y-m-d', strtotime($dates[0] . ' - 45 days'));
        //        $dates[46] = date('Y-m-d', strtotime($dates[0] . ' - 46 days'));
        //        $dates[47] = date('Y-m-d', strtotime($dates[0] . ' - 47 days'));
        //        $dates[48] = date('Y-m-d', strtotime($dates[0] . ' - 48 days'));
        //        $dates[49] = date('Y-m-d', strtotime($dates[0] . ' - 49 days'));
        //        $dates[50] = date('Y-m-d', strtotime($dates[0] . ' - 50 days'));





        foreach ($dates as $punchDates) {
            $AttendanceMList = AttendanceMaster::where('date', '=', $punchDates)
                ->with(['attendance' => function ($query) {
                    $query->select(['attendance_master_id', 'punch_type', 'time']);
                }])->get();
            if (!empty($AttendanceMList)) {
                foreach ($AttendanceMList as $mKey => $mValue) {
                    $lastOut = NULL;
                    $timeDifference = [];
                    foreach ($mValue->attendance as $dKey => $dValue) {
                        if (($dKey) % 2 == 0) {
                            if ($dValue->punch_type == 'IN' && !empty(($mValue->attendance[$dKey + 1])) && $mValue->attendance[$dKey + 1]->punch_type == 'OUT') {
                                $inTime = new DateTime($dValue->time);
                                $outTime = new DateTime($mValue->attendance[$dKey + 1]->time);
                                $duration = $inTime->diff($outTime);
                                $timeDifference[] = $duration->format("%H:%I:%S");
                            }
                        }
                        if ($dValue->punch_type == 'OUT')
                            $lastOut = $dValue->time;
                    }
                    AttendanceMaster::where('id', $mValue->id)->update(['last_out' => $lastOut, 'total_hours' => self::totalTime($timeDifference)]);
                }
            }
        }
    }

    public function totalTime($times)
    {
        $minutes = 0;
        foreach ($times as $time) {
            list($hour, $minute) = explode(':', $time);
            $minutes += $hour * 60;
            $minutes += $minute;
        }
        $hours = floor($minutes / 60);
        $minutes -= $hours * 60;
        return sprintf('%02d:%02d:%02d', $hours, $minutes, '00');
    }

    public function check_update_probation_period(Request $request)
    {
        $user_list = User::join('employee', 'employee.user_id', '=', 'users.id')->where('users.is_on_probation', 'Yes')
            ->whereRaw("employee.joining_date + INTERVAL 6 MONTH < NOW()")
            ->get(['users.*', 'employee.joining_date']);

        if ($user_list->count() > 0) {
            foreach ($user_list as $user) {
                $update_arr = [
                    'is_on_probation' => 'No',
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip(),
                ];

                User::where('id', $user->id)->update($update_arr);
            }
        }
    }

    public function long_leave_notify(Request $request)
    {
        $current_date = date('Y-m-d');
        DB::enableQueryLog();
        $start_leave_list = Leaves::whereRaw("DATEDIFF(start_date,'{$current_date}') = 7")
            ->where('leave_status', 2)
            ->where('end_date', '>', date('Y-m-d'))
            ->get();

        if ($start_leave_list->count() > 0) {
            foreach ($start_leave_list as $leave) {
                $leave_user = User::where('id', $leave->user_id)->get();
                $mail_data = [
                    'leave_user' => $leave_user[0]->name,
                    'start_end' => 'start',
                    'leave_subject' => $leave->subject,
                    'start_date' => date('d-m-Y', strtotime($leave->start_date)),
                    'end_date' => date('d-m-Y', strtotime($leave->end_date)),
                    'email' => $leave_user[0]->email,
                ];
                $this->common_task->lognLeaveEmail($mail_data);
                $this->notification_task->longLeaveStartNotify([$leave_user[0]->user_id], date('d-m-Y', strtotime($leave->start_date)));
            }
        }

        $end_leave_list = Leaves::whereRaw("DATEDIFF(end_date,{$current_date}) = 7")
            ->where('leave_status', 2)
            ->where('end_date', '>', date('Y-m-d'))
            ->get();
        if ($end_leave_list->count() > 0) {
            foreach ($end_leave_list as $leave) {
                $leave_user = User::where('id', $leave->user_id)->get();
                $mail_data = [
                    'leave_user' => $leave_user[0]->name,
                    'start_end' => 'end',
                    'leave_subject' => $leave->subject,
                    'start_date' => date('d-m-Y', strtotime($leave->start_date)),
                    'end_date' => date('d-m-Y', strtotime($leave->end_date)),
                    'email' => $leave_user[0]->email,
                ];
                $this->common_task->lognLeaveEmail($mail_data);
                $this->notification_task->longLeaveEndNotify([$leave_user[0]->user_id], date('d-m-Y', strtotime($leave->end_date)));
            }
        }
    }

    public function spacial_day_notify(Request $request)
    {
        //DB::enableQueryLog();
        $user_list = User::where('status', 'Enabled')->where('is_user_relieved', 0)->get(['id', 'email']);
        $all_users = $user_list->pluck('id')->toArray();
        $all_users_email = $user_list->pluck('email')->toArray();
        $birthday_users = User::join('employee', 'employee.user_id', '=', 'users.id')
            ->where('users.status', 'Enabled')
            ->where('users.is_user_relieved', 0)
            ->whereRaw("DATE_FORMAT(employee.birth_date,'%m-%d') = DATE_FORMAT(NOW(),'%m-%d')")
            ->get(['users.name', 'users.email', 'users.id']);
        //dd(DB::getQueryLog());
        //$birthday_user_id_arr=$birthday_users->pluck('id')->toArray();

        foreach ($birthday_users as $birthday) {
            //notify all about birthday user
            $new_all_users = array_diff($all_users, [$birthday->id]);
            $new_all_users_email = array_diff($all_users_email, [$birthday->email]);

            $this->notification_task->birthdayNotify($new_all_users, $birthday->name);
            $this->notification_task->birthdayWishNotify([$birthday->id], $birthday->name);

            $alert_mail_data = [
                'birthday_user_name' => $birthday->name,
                'to_email_list' => $new_all_users_email
            ];
            $this->common_task->birthDayAlertEmail($alert_mail_data);

            $wish_mail_data = [
                'anniversary_user_name' => $birthday->name,
                'to_email_list' => [$birthday->email],
                'birthday_user_name' => $birthday->name
            ];
            $this->common_task->birthDayWishEmail($wish_mail_data);
        }

        $marriage_aniversary_users = User::join('employee', 'employee.user_id', '=', 'users.id')
            ->where('users.status', 'Enabled')
            ->where('users.is_user_relieved', 0)
            ->where('employee.marital_status', 'Married')
            ->whereRaw("DATE_FORMAT(employee.marriage_date,'%m-%d') = DATE_FORMAT(NOW(),'%m-%d')")
            ->where('users.created_at','<',date('Y-m-d'))
            ->get(['users.name', 'users.email', 'users.id']);
        //echo '<pre>'; print_r($marriage_aniversary_users); die();
        foreach ($marriage_aniversary_users as $marriage) {
            $new_all_users = array_diff($all_users, [$marriage->id]);
            $new_all_users_email = array_diff($all_users_email, [$marriage->email]);
            //notify all about marraiage anniversary user
            $this->notification_task->marriageAniversaryNotify($new_all_users, $marriage->name);
            $this->notification_task->marriageAniversaryWishNotify([$marriage->id], $marriage->name);

            $alert_mail_data = [
                'anniversary_user_name' => $marriage->name,
                'to_email_list' => $new_all_users_email
            ];
            $this->common_task->marraigeAnniversaryAlertEmail($alert_mail_data);

            $wish_mail_data = [
                'anniversary_user_name' => $marriage->name,
                'to_email_list' => [$marriage->email]
            ];
            $this->common_task->marraigeAnniversaryWishEmail($wish_mail_data);
        }

        $joining_aniversary_users = User::join('employee', 'employee.user_id', '=', 'users.id')
            ->where('users.status', 'Enabled')
            ->where('users.is_user_relieved', 0)
            ->whereRaw("DATE_FORMAT(employee.joining_date,'%m-%d') = DATE_FORMAT(NOW(),'%m-%d')")
            ->where('users.created_at','<',date('Y-m-d'))
            ->get(['users.name', 'users.email', 'users.id']);
        
        foreach ($joining_aniversary_users as $joining_user) {
            $new_all_users = array_diff($all_users, [$joining_user->id]);
            $new_all_users_email = array_diff($all_users_email, [$joining_user->email]);
            //notify all about joining anniversary user
            $this->notification_task->joiningAniversaryNotify($new_all_users, $joining_user->name);
            $this->notification_task->joiningAniversaryWishNotify([$joining_user->id], $joining_user->name);

            $alert_mail_data = [
                'anniversary_user_name' => $joining_user->name,
                'to_email_list' => $new_all_users_email
            ];
            $this->common_task->workAnniversaryAlertEmail($alert_mail_data);

            $wish_mail_data = [
                'anniversary_user_name' => $joining_user->name,
                'to_email_list' => [$joining_user->email]
            ];
            $this->common_task->workAnniversaryWishEmail($wish_mail_data);
        }
    }

    public function suspendUser(Request $request)
    {
        $todayDate = Carbon::today();
        $date = Carbon::today()->subDays(14);

        $countSunday = 0;
        while ($date <= $todayDate) {
            if ($date->format('w') == 0) {
                $countSunday++;
            }
            $date->modify('+1 day');
        }

        $prevDate = Carbon::today()->subDays(14 + $countSunday);

        if ($prevDate->format('w') == 0) {
            $countSunday++;
            $prevDate = Carbon::today()->subDays(14 + $countSunday);
        }

        $holidays = Holiday::whereBetween('start_date', [$prevDate, $todayDate])->orWhereBetween('end_date', [$prevDate, $todayDate])->get();

        $totalHoliday = 0;
        foreach ($holidays as $key => $value) {
            $diff = abs(strtotime($value->end_date) - strtotime($value->start_date));
            if ($diff > 0) {
                $years = floor($diff / (365 * 60 * 60 * 24));
                $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
                $diff = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
            }
            $totalHoliday += ($diff + 1);
        }
        $totalLeverageDays = 14 + $countSunday + $totalHoliday;
        $newPrevDate = Carbon::today()->subDays($totalLeverageDays);
        $AttendanceMList = AttendanceMaster::select('user_id', DB::raw('count(*) as total'))->whereBetween('date', [$newPrevDate, $todayDate])->groupBy('user_id')->get();

        foreach ($AttendanceMList as $key => $value) {
            if ($value->total >= $totalLeverageDays + 2) {
                User::where('id', $value->user_id)->where('role_id', '!=', config('constants.SuperUser'))->update(['status' => 'Disabled', 'is_suspended' => 'YES']);
            }
        }
    }

    public function payroll_deduction(Request $request)
    {

        $day_precent_arr = [
            83 => ['25' => 0, '50' => 15, '100' => 15], //amit mishra
            73 => ['25' => 13, '50' => 0, '100' => 17], //amit mehta
            80 => ['25' => 0, '50' => 0, '100' => 30], //Anil rajput
            85 => ['25' => 0, '50' => 11, '100' => 19], //ashvini
            //102=>['25' => 31, '50' => 0, '100' => 0],
            96 => ['25' => 30, '50' => 0, '100' => 0], //chetan
            66 => ['25' => 1, '50' => 0, '100' => 29], //Dhaval
            68 => ['25' => 30, '50' => 0, '100' => 0], //javed
            74 => ['25' => 1, '50' => 0, '100' => 29], //jigar
            90 => ['25' => 3, '50' => 0, '100' => 27], //jignesh chavda
            77 => ['25' => 2, '50' => 0, '100' => 4], //jignesh punchal
            99 => ['25' => 28, '50' => 0, '100' => 2], //mahesh parmar
            79 => ['25' => 1, '50' => 0, '100' => 28], //narendra
            72 => ['25' => 19, '50' => 0, '100' => 11], //praharsh
            78 => ['25' => 0, '50' => 0, '100' => 30], //Pranav rawal
            70 => ['25' => 3, '50' => 0, '100' => 27], //purvin
            98 => ['25' => 23, '50' => 0, '100' => 2], //radhika sukheja
            94 => ['25' => 12, '50' => 0, '100' => 6], //rahul prajapati
            92 => ['25' => 0, '50' => 0, '100' => 30], //rajpal sinh
            93 => ['25' => 0, '50' => 0, '100' => 30], //ziluzi thakor
            103 => ['25' => 30, '50' => 0, '100' => 0], // ganesh pardhi
            101 => ['25' => 8, '50' => 0, '100' => 22], //parikshit
            106 => ['25' => 0, '50' => 0, '100' => 15], //Akil - no salary structure added
            104 => ['25' => 3, '50' => 0, '100' => 24], //Dittal - no salary structure added
            105 => ['25' => 0, '50' => 0, '100' => 26], //varsha - no salary structure added
            102 => ['25' => 19, '50' => 0, '100' => 11], //baljeet - no salary structure added
        ];

        $percent_arr = ['25', '50', '100'];
        $monthDays = date('t');
        $year = !empty($request->has('year')) ? $request->input('year') : (date('Y', strtotime('-20 day', strtotime(date('Y-m-d')))));
        $month = !empty($request->has('month')) ? $request->input('month') : (date('m', strtotime('-20 day', strtotime(date('Y-m-d')))));


        $salary = [];
        //$sundays = AttendanceController::countSundays($monthDays, $year, $month);
        $user = User::where('status', 'Enabled')->with(['employee' => function ($query) {
            return $query->select('company_id', 'user_id');
        }])->get(['id', 'is_user_relieved', 'relieved_date'])->toArray();

        if ($user) {
            foreach ($user as $userKey => $userValue) {
                if (!array_key_exists($userValue['id'], $day_precent_arr)) {
                    continue;
                }


                $sandwich_leave_count = 0;
                $attendanceMaster = AttendanceMaster::where('date', 'LIKE', $year . '-' . $month . '%')->where('user_id', $userValue['id'])->get();


                //$empSalary = EmployeesSalary::where('user_id', $userValue['id'])->where('salary_month', '<=', $month)->where('salary_year', '<=', $year)->get()->first();
                $empSalary = EmployeesSalary::where('user_id', $userValue['id'])->orderBy('id', 'DESC')->get()->first();

                if (!empty($empSalary)) {


                    $salary[$userValue['id']]['payable_salary'] = 0;
                    $salary[$userValue['id']]['newpf'] = 0;
                    $salary[$userValue['id']]['basic_salary'] = 0;
                    $salary[$userValue['id']]['hra'] = 0;
                    $salary[$userValue['id']]['others'] = 0;
                    $new_employer_pf = 0;
                    $new_original_ctc = 0;
                    $new_pt = 0;
                    $total_paid_days = 0;
                    foreach ($percent_arr as $percent) {
                        $empSalary = EmployeesSalary::where('user_id', $userValue['id'])->orderBy('id', 'DESC')->get()->first();
                        $daysOfMonth = 30;
                        $paid_days = $day_precent_arr[$userValue['id']][$percent];
                        $total_paid_days = $total_paid_days + $paid_days;
                        $percent_salary = (int) $percent;

                        $empSalary->basic_salary = ($percent_salary * $empSalary->basic_salary) / 100;
                        $empSalary->hra = ($percent_salary * $empSalary->hra) / 100;
                        $empSalary->other_allowance = ($percent_salary * $empSalary->other_allowance) / 100;
                        $empSalary->total_month_salary = ($percent_salary * $empSalary->total_month_salary) / 100;
                        $empSalary->professional_tax = ($percent_salary * $empSalary->professional_tax) / 100;
                        $empSalary->gross_salary_pm_ctc = ($percent_salary * $empSalary->gross_salary_pm_ctc) / 100;

                        if ($empSalary->PF_amount > 0) {

                            $salary[$userValue['id']]['pf'] = ((($empSalary->basic_salary / $daysOfMonth) * ($paid_days)) * 12) / 100;
                            $employer_pf = ((($empSalary->basic_salary / $daysOfMonth) * ($paid_days)) * 13) / 100;
                        } else {
                            $salary[$userValue['id']]['pf'] = 0;
                            $employer_pf = 0;
                        }
                        $new_employer_pf = $new_employer_pf + $employer_pf;
                        $salary[$userValue['id']]['newpf'] = $salary[$userValue['id']]['newpf'] + $salary[$userValue['id']]['pf'];
                        $salary[$userValue['id']]['basic_salary'] = $salary[$userValue['id']]['basic_salary'] + (($empSalary->basic_salary / $daysOfMonth) * ($paid_days));
                        $salary[$userValue['id']]['hra'] = $salary[$userValue['id']]['hra'] + (($empSalary->hra / $daysOfMonth) * ($paid_days));
                        $salary[$userValue['id']]['others'] = $salary[$userValue['id']]['others'] + (($empSalary->other_allowance / $daysOfMonth) * ($paid_days));

                        if ($empSalary->salaray_category == 2) {
                            $original_ctc = (($empSalary->gross_salary_pm_ctc / $daysOfMonth) * $paid_days);
                            $empSalary->gross_salary_pm_ctc = (($empSalary->gross_salary_pm_ctc / $daysOfMonth) * $paid_days) - $employer_pf;
                        } else {

                            $original_ctc = $empSalary->gross_salary_pm_ctc = (($empSalary->total_month_salary / $daysOfMonth) * $paid_days) + $employer_pf;
                        }


                        //kishan chnage to calculate net salary
                        $new_original_ctc = $new_original_ctc + $original_ctc;


                        $salary[$userValue['id']]['payable_salary'] = $salary[$userValue['id']]['payable_salary'] + (($original_ctc)) - ($employer_pf + $salary[$userValue['id']]['pf']);
                        $pt = ($empSalary->professional_tax / $daysOfMonth) * $paid_days;
                        $salary[$userValue['id']]['payable_salary'] = $salary[$userValue['id']]['payable_salary'];
                        $new_pt = $new_pt + $pt;
                        //echo $salary[$userValue['id']]['payable_salary'].'-'.$paid_days.'<br>';
                    }

                    if ($salary[$userValue['id']]['payable_salary'] >= 0 && 5999 >= $salary[$userValue['id']]['payable_salary']) {
                        $professionalTax = 0;
                    } else if ($salary[$userValue['id']]['payable_salary'] >= 6000 && 8999 >= $salary[$userValue['id']]['payable_salary']) {
                        $professionalTax = 80;
                    } else if ($salary[$userValue['id']]['payable_salary'] >= 9000 && 11999 >= $salary[$userValue['id']]['payable_salary']) {
                        $professionalTax = 150;
                    } else if ($salary[$userValue['id']]['payable_salary'] >= 12000) {
                        $professionalTax = 200;
                    } else {
                        $professionalTax = 0;
                    }

                    $salary[$userValue['id']]['professional_tax'] = $professionalTax;
                    $salary[$userValue['id']]['payable_salary'] -= $salary[$userValue['id']]['professional_tax'];

                    //kishan change
                    //$salary[$userValue['id']]['payable_salary'] -=$employer_pf;
                }

                if ($userValue['id'] == 83) {
                    $salary[$userValue['id']]['payable_salary'] = $salary[$userValue['id']]['payable_salary'] + 0;
                } elseif ($userValue['id'] == 85) {
                    $salary[$userValue['id']]['payable_salary'] = $salary[$userValue['id']]['payable_salary'] + 0;
                } elseif ($userValue['id'] == 80) {
                    $salary[$userValue['id']]['payable_salary'] = $salary[$userValue['id']]['payable_salary'] + 7500;
                } elseif ($userValue['id'] == 77) {
                    $salary[$userValue['id']]['payable_salary'] = $salary[$userValue['id']]['payable_salary'] + 0;
                } else {
                    $salary[$userValue['id']]['payable_salary'] = $salary[$userValue['id']]['payable_salary'] + 0;
                }
                $penalty = 0;
                $perday_basic = $empSalary->basic_salary / 30;
                $perday_hra = $empSalary->hra / 30;
                $perday_other_allowance = $empSalary->other_allowance / 30;
                $total_amt = $perday_basic + $perday_hra + $perday_other_allowance;
                if ($userValue['id'] == 105) {

                    $penalty = $total_amt * 3.5;
                } elseif ($userValue['id'] == 106) {
                    $penalty = $total_amt * 0.5;
                }
                $salary[$userValue['id']]['payable_salary'] = $salary[$userValue['id']]['payable_salary'] - $penalty;

                if (!empty($salary) && !empty($salary[$userValue['id']])) {

                    $payrollModel = new Payroll();

                    $payrollModel->user_id = $userValue['id'];
                    $payrollModel->company_id = $userValue['employee']['company_id'];
                    $payrollModel->month = $month;
                    $payrollModel->year = $year;
                    $payrollModel->date = date("Y-m-d");
                    $payrollModel->basic_salary = $salary[$userValue['id']]['basic_salary'];
                    $payrollModel->hra = $salary[$userValue['id']]['hra'];
                    $payrollModel->others = $salary[$userValue['id']]['others'];
                    $payrollModel->working_day = 30;
                    $payrollModel->employee_working_day = $total_paid_days;
                    $payrollModel->total_leave = 0;
                    $payrollModel->unpaid_leave = 0;
                    $payrollModel->unpaid_leave_amount = 0;
                    $payrollModel->professional_tax = $professionalTax;
                    $payrollModel->pf = $salary[$userValue['id']]['newpf'];
                    $payrollModel->loan_installment = 0;
                    $payrollModel->penalty = $penalty;
                    $payrollModel->payable_salary = $salary[$userValue['id']]['payable_salary'];
                    $payrollModel->status = 'Enabled';
                    $payrollModel->created_at = date('Y-m-d H:i:s');
                    $payrollModel->created_ip = $request->ip();
                    $payrollModel->updated_at = date('Y-m-d H:i:s');
                    $payrollModel->updated_ip = $request->ip();
                    $payrollModel->total_month_days = $daysOfMonth;
                    $payrollModel->total_paid_days = $daysOfMonth;
                    $payrollModel->employer_pf = $new_employer_pf;
                    $payrollModel->total_sandwich_leave = 0;
                    $payrollModel->salary_ctc = $new_original_ctc;
                    $payrollModel->save();
                }
            }
        }
    }

    //month may
    /* public function payroll_deduction(Request $request) {

      $day_precent_arr = [
      //83=>['25' => 0, '50' => 28, '100' => 3],
      73=>['25' => 15, '50' => 9, '100' => 7],
      //80=>['25' => 23, '50' => 0, '100' => 8],
      //85=>['25' => 0, '50' => 26, '100' => 5],
      //102=>['25' => 31, '50' => 0, '100' => 0],
      //96=>['25' => 31, '50' => 0, '100' => 0],
      66=>['25' => 4, '50' => 17, '100' => 10],
      //68=>['25' => 31, '50' => 0, '100' => 0],
      //74=>['25' => 31, '50' => 0, '100' => 0],
      //90=>['25' => 20, '50' => 1, '100' => 10],
      //77=>['25' => 31, '50' => 0, '100' => 0],
      //99=>['25' => 28, '50' => 0, '100' => 3],
      //79=>['25' => 31, '50' => 0, '100' => 0],
      //72=>['25' => 31, '50' => 0, '100' => 0],
      78=>['25' => 12, '50' => 7, '100' => 12],
      //70=>['25' => 31, '50' => 0, '100' => 0],
      98=>['25' => 27, '50' => 4, '100' => 0],
      //94=>['25' => 26, '50' => 2, '100' => 3],
      //92=>['25' => 31, '50' => 0, '100' => 0],
      //93=>['25' => 31, '50' => 0, '100' => 0],
      //103=>['25' => 31, '50' => 0, '100' => 0],
      101=>['25' => 27, '50' => 4, '100' => 0],
      ];

      $percent_arr = ['25', '50', '100'];
      $monthDays = date('t');
      $year = !empty($request->has('year')) ? $request->input('year') : (date('Y', strtotime('-15 day', strtotime(date('Y-m-d')))));
      $month = !empty($request->has('month')) ? $request->input('month') : (date('m', strtotime('-15 day', strtotime(date('Y-m-d')))));


      $salary = [];
      //$sundays = AttendanceController::countSundays($monthDays, $year, $month);
      $user = User::where('status', 'Enabled')->with(['employee' => function($query) {
      return $query->select('company_id', 'user_id');
      }])->get(['id', 'is_user_relieved', 'relieved_date'])->toArray();

      if ($user) {
      foreach ($user as $userKey => $userValue) {
      if (!array_key_exists($userValue['id'], $day_precent_arr)) {
      continue;
      }


      $sandwich_leave_count = 0;
      $attendanceMaster = AttendanceMaster::where('date', 'LIKE', $year . '-' . $month . '%')->where('user_id', $userValue['id'])->get();


      //$empSalary = EmployeesSalary::where('user_id', $userValue['id'])->where('salary_month', '<=', $month)->where('salary_year', '<=', $year)->get()->first();
      $empSalary = EmployeesSalary::where('user_id', $userValue['id'])->orderBy('id', 'DESC')->get()->first();

      if (!empty($empSalary)) {


      $salary[$userValue['id']]['payable_salary'] = 0;
      $salary[$userValue['id']]['newpf']=0;
      $salary[$userValue['id']]['basic_salary']=0;
      $salary[$userValue['id']]['hra']=0;
      $salary[$userValue['id']]['others']=0;
      $new_employer_pf=0;
      $new_original_ctc=0;
      $new_pt=0;
      foreach ($percent_arr as $percent) {
      $empSalary = EmployeesSalary::where('user_id', $userValue['id'])->orderBy('id', 'DESC')->get()->first();
      $daysOfMonth = 31;
      $paid_days = $day_precent_arr[$userValue['id']][$percent];
      $percent_salary = (int) $percent;

      $empSalary->basic_salary = ($percent_salary * $empSalary->basic_salary) / 100;
      $empSalary->hra = ($percent_salary * $empSalary->hra) / 100;
      $empSalary->other_allowance = ($percent_salary * $empSalary->other_allowance) / 100;
      $empSalary->total_month_salary = ($percent_salary * $empSalary->total_month_salary) / 100;
      $empSalary->professional_tax=($percent_salary * $empSalary->professional_tax) / 100;
      $empSalary->gross_salary_pm_ctc = ($percent_salary * $empSalary->gross_salary_pm_ctc) / 100;

      if ($empSalary->PF_amount > 0) {

      $salary[$userValue['id']]['pf'] = ((($empSalary->basic_salary / $daysOfMonth) * ($paid_days)) * 12) / 100;
      $employer_pf = ((($empSalary->basic_salary / $daysOfMonth) * ($paid_days)) * 13) / 100;
      } else {
      $salary[$userValue['id']]['pf'] = 0;
      $employer_pf = 0;
      }
      $new_employer_pf=$new_employer_pf+$employer_pf;
      $salary[$userValue['id']]['newpf']=$salary[$userValue['id']]['newpf']+$salary[$userValue['id']]['pf'];
      $salary[$userValue['id']]['basic_salary']=$salary[$userValue['id']]['basic_salary']+(($empSalary->basic_salary / $daysOfMonth) * ($paid_days));
      $salary[$userValue['id']]['hra']=$salary[$userValue['id']]['hra']+(($empSalary->hra / $daysOfMonth) * ($paid_days));
      $salary[$userValue['id']]['others']=$salary[$userValue['id']]['others']+(($empSalary->other_allowance / $daysOfMonth) * ($paid_days));

      if ($empSalary->salaray_category == 2) {
      $original_ctc = (($empSalary->gross_salary_pm_ctc/$daysOfMonth)*$paid_days);
      $empSalary->gross_salary_pm_ctc = (($empSalary->gross_salary_pm_ctc/$daysOfMonth)*$paid_days) - $employer_pf;
      } else {

      $original_ctc = $empSalary->gross_salary_pm_ctc = (($empSalary->total_month_salary/$daysOfMonth)*$paid_days) + $employer_pf;
      }


      //kishan chnage to calculate net salary
      $new_original_ctc=$new_original_ctc+$original_ctc;


      $salary[$userValue['id']]['payable_salary'] = $salary[$userValue['id']]['payable_salary'] + (($original_ctc) ) - ($employer_pf + $salary[$userValue['id']]['pf']);
      $pt=($empSalary->professional_tax/$daysOfMonth)*$paid_days;
      $salary[$userValue['id']]['payable_salary']=$salary[$userValue['id']]['payable_salary'];
      $new_pt=$new_pt+$pt;
      //echo $salary[$userValue['id']]['payable_salary'].'-'.$paid_days.'<br>';
      }

      if ($new_original_ctc >= 0 && 5999 >= $new_original_ctc) {
      $professionalTax = 0;
      } else if ($new_original_ctc >= 6000 && 8999 >= $new_original_ctc) {
      $professionalTax = 80;
      } else if ($new_original_ctc >= 9000 && 11999 >= $new_original_ctc) {
      $professionalTax = 150;
      } else if ($new_original_ctc >= 12000) {
      $professionalTax = 200;
      } else {
      $professionalTax = 0;
      }

      $salary[$userValue['id']]['professional_tax'] = $professionalTax;
      $salary[$userValue['id']]['payable_salary'] -= $salary[$userValue['id']]['professional_tax'];

      //kishan change
      //$salary[$userValue['id']]['payable_salary'] -=$employer_pf;
      }

      if($userValue['id']==83){
      $salary[$userValue['id']]['payable_salary']=$salary[$userValue['id']]['payable_salary']+1976;

      }
      elseif($userValue['id']==85){
      $salary[$userValue['id']]['payable_salary']= $salary[$userValue['id']]['payable_salary']+2032;
      }
      elseif($userValue['id']==80){
      $salary[$userValue['id']]['payable_salary']=$salary[$userValue['id']]['payable_salary']+3438;
      }
      elseif($userValue['id']==77){
      $salary[$userValue['id']]['payable_salary']=$salary[$userValue['id']]['payable_salary']+1000;
      }
      else{
      $salary[$userValue['id']]['payable_salary']= $salary[$userValue['id']]['payable_salary']+0;
      }

      if (!empty($salary) && !empty($salary[$userValue['id']])) {

      $payrollModel = new Payroll();

      $payrollModel->user_id = $userValue['id'];
      $payrollModel->company_id = $userValue['employee']['company_id'];
      $payrollModel->month = $month;
      $payrollModel->year = $year;
      $payrollModel->date = date("Y-m-d");
      $payrollModel->basic_salary = $salary[$userValue['id']]['basic_salary'];
      $payrollModel->hra = $salary[$userValue['id']]['hra'];
      $payrollModel->others = $salary[$userValue['id']]['others'];
      $payrollModel->working_day = 31;
      $payrollModel->employee_working_day = 31;
      $payrollModel->total_leave = 0;
      $payrollModel->unpaid_leave = 0;
      $payrollModel->unpaid_leave_amount = 0;
      $payrollModel->professional_tax = $professionalTax;
      $payrollModel->pf = $salary[$userValue['id']]['newpf'];
      $payrollModel->loan_installment = 0;
      $payrollModel->penalty = 0;
      $payrollModel->payable_salary = $salary[$userValue['id']]['payable_salary'];
      $payrollModel->status = 'Enabled';
      $payrollModel->created_at = date('Y-m-d H:i:s');
      $payrollModel->created_ip = $request->ip();
      $payrollModel->updated_at = date('Y-m-d H:i:s');
      $payrollModel->updated_ip = $request->ip();
      $payrollModel->total_month_days = $daysOfMonth;
      $payrollModel->total_paid_days = $daysOfMonth;
      $payrollModel->employer_pf = $new_employer_pf;
      $payrollModel->total_sandwich_leave = 0;
      $payrollModel->salary_ctc = $new_original_ctc;
      $payrollModel->save();


      }
      }
      }
      } */

    public function add_unattended_sandwich()
    {
        $year = (date('Y', strtotime('-15 day', strtotime(date('Y-m-d')))));
        $month = (date('m', strtotime('-15 day', strtotime(date('Y-m-d')))));

        $daysOfMonth = date('t', strtotime('-15 day', strtotime(date('Y-m-d'))));

        $users = User::where('status', 'Enabled')->with(['employee' => function ($query) {
            return $query->select('company_id', 'user_id');
        }])->get(['id', 'is_user_relieved', 'relieved_date'])->toArray();

        foreach ($users as $user) {

            $attendance_detail = AttendanceMaster::where('user_id', $user['id'])
                ->whereRaw('`date` LIKE "%' . $year . '-' . $month . '%"')->orderBy('date', 'ASC')->get();

            if ($attendance_detail->count() == 0) {
                continue;
            }

            $start_date = $year . '-' . $month . '-01';
            if ($daysOfMonth != $attendance_detail->count() && $attendance_detail->count() > 3) {
                $sandwhich_count = 0;
                $check_sandwhich = 0;
                foreach ($attendance_detail as $key => $attendance) {

                    if (date('D', strtotime($attendance->date)) == 'Sun') {
                        $pre_date = date('Y-m-d', strtotime('-1 days', strtotime($attendance->date)));
                        $next_date = date('Y-m-d', strtotime('+1 days', strtotime($attendance->date)));

                        if (isset($attendance_detail[$key - 1]) && $attendance_detail[$key - 1]->date != $pre_date && isset($attendance_detail[$key + 1]) && $attendance_detail[$key + 1]->date != $next_date) {
                            AttendanceMaster::where('id', $attendance->id)->delete();
                        }
                    } else {
                        $holidaycheck = Holiday::whereDate('start_date', '<=', $attendance->date)->whereDate('end_date', '>=', $attendance->date)->get();
                        if ($holidaycheck->count() > 0) {
                            $pre_date = date('Y-m-d', strtotime('-1 days', strtotime($attendance->date)));
                            $next_date = date('Y-m-d', strtotime('+1 days', strtotime($attendance->date)));

                            if (isset($attendance_detail[$key - 1]) && $attendance_detail[$key - 1]->date != $pre_date && isset($attendance_detail[$key + 1]) && $attendance_detail[$key + 1]->date != $next_date) {
                                AttendanceMaster::where('id', $attendance->id)->delete();
                            }
                        }
                    }
                }
            }
        }
    }

    public function payroll(Request $request)
    {

        $monthDays = date('t');
        $year = !empty($request->has('year')) ? $request->input('year') : (date('Y', strtotime('-15 day', strtotime(date('Y-m-d')))));
        $month = !empty($request->has('month')) ? $request->input('month') : (date('m', strtotime('-15 day', strtotime(date('Y-m-d')))));

        //calculate total sunday in month
        $m_start_date = date($year . '-' . $month . '-' . '01');
        $m_end_date = date("Y-m-t", strtotime($m_start_date));
        $month_sunday = 0;
        $month_hoilday = 0;
        while (strtotime($m_start_date) <= strtotime($m_end_date)) {
            if (date('D', strtotime($m_start_date)) == 'Sun') {
                $month_sunday++;
            } else {
                $holidaycheck = Holiday::whereDate('start_date', '<=', $m_start_date)->whereDate('end_date', '>=', $m_start_date)->get();
                if ($holidaycheck->count() > 0) {
                    $month_hoilday++;
                }
            }

            $m_start_date = date('Y-m-d', strtotime($m_start_date . ' + 1 days'));
        }


        $salary = [];
        //$sundays = AttendanceController::countSundays($monthDays, $year, $month);
        $user = User::where('status', 'Enabled')->with(['employee' => function ($query) {
            return $query->select('company_id', 'user_id');
        }])->get(['id', 'is_user_relieved', 'relieved_date'])->toArray();

        if ($user) {
            foreach ($user as $userKey => $userValue) {
                $salary = [];

                $sandwich_leave_count = 0;
                $attendanceMaster = AttendanceMaster::where('date', 'LIKE', $year . '-' . $month . '%')->where('user_id', $userValue['id'])->orderBy('date', 'ASC')->get();


                //$empSalary = EmployeesSalary::where('user_id', $userValue['id'])->where('salary_month', '<=', $month)->where('salary_year', '<=', $year)->get()->first();
                $empSalary = EmployeesSalary::where('user_id', $userValue['id'])->orderBy('id', 'DESC')->get()->first();

                if (!empty($empSalary)) {
                    $empLoan = EmployeesLoans::where('user_id', $userValue['id'])->where('loan_status', 'Approved')->where('status', 'Enabled')->get()->first();
                    if (!empty($empLoan) && $empLoan->completed_loan_terms != $empLoan->loan_terms) {
                        $emiStart = explode('-', $empLoan->loan_emi_start_from);

                        $emiStartDate = $emiStart[1] . "-" . $emiStart[0] . "-01";
                        $salaryDate = $year . "-" . $month . "-01";

                        if ($emiStartDate <= $salaryDate) {
                            $salary[$userValue['id']]['installment'] = round(($empLoan->loan_amount / $empLoan->loan_terms), 2);
                            $complete_load_arr = [
                                'completed_loan_amount' => $empLoan->completed_loan_amount + $salary[$userValue['id']]['installment'],
                                'completed_loan_terms' => $empLoan->completed_loan_terms + 1,
                            ];
                            EmployeesLoans::where('id', $empLoan->id)->update($complete_load_arr);
                        } else {
                            $salary[$userValue['id']]['installment'] = 0;
                        }
                    } else {
                        $salary[$userValue['id']]['installment'] = 0;
                    }

                    $late_join_nonworkingday = $employeeWorkingDay = $actual_emp_working_day = $unpaid_leave = $total_leave = $nonWorkingDay = $WrongnonWorkingDay = $total = 0;
                    $availablity_ids = [];
                    $lateDate = $moreLateDate = [];
                    //need condition to add weekends for new added employee
                    if (date('d', strtotime($attendanceMaster[0]->date)) != '01') {
                        //take weekends form start to first date
                        $s_date = date($year . '-' . $month . '-01');
                        $e_date = $attendanceMaster[0]->date;
                        while (strtotime($s_date) < strtotime($e_date)) {
                            if (date('D', strtotime($s_date)) == 'Sun') {
                                //$late_join_nonworkingday++;
                            } else {
                                $holidaycheck = Holiday::whereDate('start_date', '<=', $s_date)->whereDate('end_date', '>=', $s_date)->get();
                                if ($holidaycheck->count() > 0) {
                                    //$late_join_nonworkingday++;
                                } else {
                                    $late_join_nonworkingday++;
                                }
                            }

                            $s_date = date('Y-m-d', strtotime($s_date . ' + 1 days'));
                        }
                    }

                    foreach ($attendanceMaster as $key => $value) {
                        if ($value->availability_status == 1) { //present
                            $employeeWorkingDay++;
                            $actual_emp_working_day++;
                        } else if ($value->availability_status == 3) { //leave
                            $actual_emp_working_day++;
                            if (in_array($value->availability_id, $availablity_ids)) {
                                continue;
                            }
                            $leaves = Leaves::find($value->availability_id);
                            array_push($availablity_ids, $value->availability_id);
                            if (!empty($leaves)) {
                                $total = 0;
                                $rmv_leaves = 0;
                                $cur_date = $leaves->start_date;
                                while (strtotime($cur_date) <= strtotime($leaves->end_date)) {
                                    if (date('m', strtotime($cur_date)) != $month) {
                                        if ($cur_date == $leaves->start_date || $cur_date == $leaves->end_date) {
                                            if ($leaves->start_day == 2 || $leaves->start_day == 3 || $leaves->end_day == 2 || $leaves->end_day == 3) {
                                                $rmv_leaves = $rmv_leaves + 0.5;
                                            } else {
                                                $rmv_leaves++;
                                            }
                                        } else {
                                            $rmv_leaves++;
                                        }
                                    }
                                    $cur_date = date('Y-m-d', strtotime($cur_date . ' + 1 days'));
                                }

                                $diff = abs(strtotime($leaves->end_date) - strtotime($leaves->start_date));
                                $years = floor($diff / (365 * 60 * 60 * 24));
                                $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
                                $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));


                                if ($days > 0) {
                                    if ($leaves->start_day == 1) {
                                        $total += 1;
                                    } else if ($leaves->start_day == 2 || $leaves->start_day == 3) {
                                        $total += 0.5;
                                        $employeeWorkingDay += 0.5;
                                    }
                                    if ($leaves->end_day == 1) {
                                        $total += 1;
                                    } else if ($leaves->end_day == 2 || $leaves->end_day == 3) {
                                        $total += 0.5;
                                        $employeeWorkingDay += 0.5;
                                    }
                                    $total += ($days - 1);
                                } else {
                                    if ($leaves->start_day == 1) {
                                        $total += 1;
                                    } else if ($leaves->start_day == 2 || $leaves->start_day == 3) {
                                        $total += 0.5;
                                        $employeeWorkingDay += 0.5;
                                    }
                                }
                                $total = $total - $rmv_leaves;
                                if ($leaves->leave_category_id == 4) { //un-paid leave
                                    $unpaid_leave = $unpaid_leave + $total;
                                }

                                $total_leave += $total;
                            }
                        } else if ($value->availability_status == 6) { //mixed leave
                            $leavesDetail = Leaves::whereIn('id', explode(",", $value->availability_id))->get();
                            foreach ($leavesDetail as $key => $value) {
                                $total_leave += 0.5;
                            }
                        } else if ($value->availability_status == 4 || $value->availability_status == 5) { //Holiday and Weekend
                            if ($userValue['is_user_relieved']) {
                                if (strtotime($value->date) > strtotime($userValue['relieved_date'])) {
                                    $WrongnonWorkingDay++;
                                }
                            }
                            $nonWorkingDay++;
                        }

                        if ($value->is_late == 'YES') {
                            array_push($lateDate, $value->date);
                        }
                        if ($value->is_late_more == 'YES') {
                            array_push($moreLateDate, $value->date);
                        }
                    }
                    /* if ($userValue['is_user_relieved']) {
                      //$daysOfMonth = date('t', strtotime('-6 day', strtotime(date('Y-m-d'))));
                      $relived_date = $userValue['relieved_date'];

                      $startDate = new DateTime(date($year . '-' . $month . '-01'));
                      $endDate = new DateTime($relived_date);

                      $interval = $startDate->diff($endDate);

                      $daysOfMonth = $interval->days + 1;
                      } else {
                      $daysOfMonth = date('t', strtotime('-6 day', strtotime(date('Y-m-d'))));
                      } */
                    $daysOfMonth = date('t', strtotime('-15 day', strtotime(date('Y-m-d'))));

                    //$salary[$userValue['id']]['working_day'] = $daysOfMonth - ($nonWorkingDay + $late_join_nonworkingday);
                    $salary[$userValue['id']]['working_day'] = $daysOfMonth - ($month_sunday + $month_hoilday + $late_join_nonworkingday);
                    $salary[$userValue['id']]['employeeWorkingDay'] = $employeeWorkingDay;
                    $salary[$userValue['id']]['unpaid_leave'] = $unpaid_leave;

                    //kishan change
                    $actual_emp_working_day = $actual_emp_working_day + $nonWorkingDay;

                    $salary[$userValue['id']]['total_leave'] = $total_leave + $sandwich_leave_count;

                    //kishan change$perDayBasic = !empty($empSalary->basic_salary) ? round(($empSalary->basic_salary / $salary[$userValue['id']]['working_day']), 2) : 0;
                    $perDayBasic = !empty($empSalary->basic_salary) ? ($empSalary->basic_salary / $daysOfMonth) : 0;

                    //$salary[$userValue['id']]['basic_salary'] = round(($perDayBasic * $salary[$userValue['id']]['employeeWorkingDay']), 2);
                    //kishan change$salary[$userValue['id']]['basic_salary'] = round(($perDayBasic * $actual_emp_working_day), 2);
                    $salary[$userValue['id']]['basic_salary'] = $perDayBasic * $actual_emp_working_day;

                    //$perDayHra = !empty($empSalary->hra) ? round(($empSalary->hra / $salary[$userValue['id']]['working_day']), 2) : 0;
                    $perDayHra = !empty($empSalary->hra) ? $empSalary->hra / $daysOfMonth : 0;

                    //$salary[$userValue['id']]['hra'] = round(($perDayHra * $salary[$userValue['id']]['employeeWorkingDay']), 2);
                    //kishan change$salary[$userValue['id']]['hra'] = round(($perDayHra * $actual_emp_working_day), 2);
                    $salary[$userValue['id']]['hra'] = $perDayHra * $actual_emp_working_day;

                    //kishan change$perDayOther = !empty($empSalary->other_allowance) ? round(($empSalary->other_allowance / $salary[$userValue['id']]['working_day']), 2) : 0;
                    $perDayOther = !empty($empSalary->other_allowance) ? $empSalary->other_allowance / $daysOfMonth : 0;

                    //$salary[$userValue['id']]['others'] = round(($perDayOther * $salary[$userValue['id']]['employeeWorkingDay']), 2);
                    //kishan change$salary[$userValue['id']]['others'] = round(($perDayOther * $actual_emp_working_day), 2);
                    $salary[$userValue['id']]['others'] = $perDayOther * $actual_emp_working_day;
                    //$salary[$userValue['id']]['pf'] = !empty($empSalary->PF_amount) ? round((($empSalary->PF_amount / $salary[$userValue['id']]['working_day']) * $salary[$userValue['id']]['employeeWorkingDay']), 2) : 0;
                    //kishan change$salary[$userValue['id']]['pf'] = !empty($empSalary->PF_amount) ? round((($empSalary->PF_amount / $salary[$userValue['id']]['working_day']) * $actual_emp_working_day), 2) : 0;
                    $non_punch_days = $daysOfMonth - ($attendanceMaster->count() - $WrongnonWorkingDay);
                    if ($empSalary->PF_amount > 0) {

                        $salary[$userValue['id']]['pf'] = ((($empSalary->basic_salary / $daysOfMonth) * ($daysOfMonth - ($unpaid_leave + $non_punch_days + $sandwich_leave_count))) * 12) / 100;
                        $employer_pf = ((($empSalary->basic_salary / $daysOfMonth) * ($daysOfMonth - ($unpaid_leave + $non_punch_days + $sandwich_leave_count))) * 13) / 100;
                    } else {
                        $salary[$userValue['id']]['pf'] = 0;
                        $employer_pf = 0;
                    }



                    $salary[$userValue['id']]['professional_tax'] = $empSalary->professional_tax;

                    $perDaySalary = $perDayBasic + $perDayHra + $perDayOther;

                    //kishan change below 2 lines
                    if ($empSalary->salaray_category == 2) {
                        $original_ctc = $empSalary->gross_salary_pm_ctc;
                        $empSalary->gross_salary_pm_ctc = $empSalary->gross_salary_pm_ctc - $employer_pf;
                    } else {

                        $original_ctc = $empSalary->gross_salary_pm_ctc = $empSalary->total_month_salary + $employer_pf;
                    }


                    //kishan change$salary[$userValue['id']]['unpaid_leave_amount'] = round(($perDaySalary * $unpaid_leave), 2);
                    $salary[$userValue['id']]['unpaid_leave_amount'] = round((($empSalary->gross_salary_pm_ctc / $daysOfMonth) * $unpaid_leave), 2);
                    $weeks = [];
                    foreach ($lateDate as $dateValue) {
                        if (array_key_exists(date('W', strtotime($dateValue)), $weeks)) {
                            $weeks[date('W', strtotime($dateValue))]['late'] = $weeks[date('W', strtotime($dateValue))]['late'] + 1;
                        } else {
                            $weeks[date('W', strtotime($dateValue))]['late'] = 1;
                        }
                    }
                    foreach ($moreLateDate as $dateValue) {
                        if (array_key_exists(date('W', strtotime($dateValue)), $weeks)) {
                            if (isset($weeks[date('W', strtotime($dateValue))]['more_late'])) {
                                $weeks[date('W', strtotime($dateValue))]['more_late'] = $weeks[date('W', strtotime($dateValue))]['more_late'] + 1;
                            } else {
                                $weeks[date('W', strtotime($dateValue))]['more_late'] = 1;
                            }
                        } else {
                            $weeks[date('W', strtotime($dateValue))]['more_late'] = 1;
                        }
                    }
                    $penaltyday = 0;
                    foreach ($weeks as $weekCount) {
                        if (isset($weekCount['late']) && $weekCount['late'] >= 2 && isset($weekCount['more_late']) && $weekCount['more_late'] >= 1) {
                            $penaltyday = $penaltyday + 1.5;
                            $weekCount['late'] -= 2;
                            $weekCount['more_late'] -= 1;
                        }
                        if (isset($weekCount['late']) && $weekCount['late'] >= 3) {
                            $penaltyday = $penaltyday + 1;
                            $weekCount['late'] -= 3;
                        }
                        if (isset($weekCount['more_late']) && $weekCount['more_late'] >= 1) {
                            $penaltyday = $penaltyday + ($weekCount['more_late'] * 0.5);
                            $weekCount['more_late'] = 0;
                        }
                    }
                    //get sandwich leave data
                    $sandwich_leave_count = \App\Sandwich_leave::where('sandwich_date', 'LIKE', $year . '-' . $month . '%')
                        ->where('user_id', $userValue['id'])
                        ->get()->sum('sandwich_total_day');

                    $salary[$userValue['id']]['penalty'] = round(($perDaySalary * $penaltyday), 2);

                    $workingDyaSalary = $salary[$userValue['id']]['basic_salary'] + $salary[$userValue['id']]['hra'] + $salary[$userValue['id']]['others'];

                    $salary[$userValue['id']]['payable_salary'] = ($workingDyaSalary - ($salary[$userValue['id']]['installment'] + $salary[$userValue['id']]['unpaid_leave_amount'] + $salary[$userValue['id']]['penalty'] + $salary[$userValue['id']]['pf']));
                    //kishan chnage to calculate net salary
                    $paid_days = $daysOfMonth - ($unpaid_leave + $non_punch_days + $sandwich_leave_count);

                    $salary[$userValue['id']]['payable_salary'] = (($original_ctc / $daysOfMonth) * $paid_days) - ($employer_pf + $salary[$userValue['id']]['penalty'] + $salary[$userValue['id']]['pf']);

                    if ($original_ctc >= 0 && 5999 >= $original_ctc) {
                        $professionalTax = 0;
                    } else if ($original_ctc >= 6000 && 8999 >= $original_ctc) {
                        $professionalTax = 80;
                    } else if ($original_ctc >= 9000 && 11999 >= $original_ctc) {
                        $professionalTax = 150;
                    } else if ($original_ctc >= 12000) {
                        $professionalTax = 200;
                    } else {
                        $professionalTax = 0;
                    }

                    $salary[$userValue['id']]['professional_tax'] = $professionalTax;
                    $salary[$userValue['id']]['payable_salary'] -= $salary[$userValue['id']]['professional_tax'];
                    $salary[$userValue['id']]['payable_salary'] -= $salary[$userValue['id']]['installment'];
                    //kishan change
                    //$salary[$userValue['id']]['payable_salary'] -=$employer_pf;
                }

                if (!empty($salary) && !empty($salary[$userValue['id']])) {

                    $payrollModel = new Payroll();

                    $payrollModel->user_id = $userValue['id'];
                    $payrollModel->company_id = $userValue['employee']['company_id'];
                    $payrollModel->month = $month;
                    $payrollModel->year = $year;
                    $payrollModel->date = date("Y-m-d");
                    $payrollModel->basic_salary = $perDayBasic * ($actual_emp_working_day - $salary[$userValue['id']]['total_leave']);
                    $payrollModel->hra = $perDayHra * ($actual_emp_working_day - $salary[$userValue['id']]['total_leave']);
                    $payrollModel->others = $perDayOther * ($actual_emp_working_day - $salary[$userValue['id']]['total_leave']);
                    $payrollModel->working_day = $salary[$userValue['id']]['working_day'];
                    $payrollModel->employee_working_day = $salary[$userValue['id']]['employeeWorkingDay'];
                    $payrollModel->total_leave = $salary[$userValue['id']]['total_leave'];
                    $payrollModel->unpaid_leave = $salary[$userValue['id']]['unpaid_leave'];
                    $payrollModel->unpaid_leave_amount = $salary[$userValue['id']]['unpaid_leave_amount'];
                    $payrollModel->professional_tax = $salary[$userValue['id']]['professional_tax'];
                    $payrollModel->pf = $salary[$userValue['id']]['pf'];
                    $payrollModel->loan_installment = $salary[$userValue['id']]['installment'];
                    $payrollModel->penalty = $salary[$userValue['id']]['penalty'];
                    $payrollModel->payable_salary = $salary[$userValue['id']]['payable_salary'];
                    $payrollModel->status = 'Enabled';
                    $payrollModel->created_at = date('Y-m-d H:i:s');
                    $payrollModel->created_ip = $request->ip();
                    $payrollModel->updated_at = date('Y-m-d H:i:s');
                    $payrollModel->updated_ip = $request->ip();
                    $payrollModel->total_month_days = $daysOfMonth;
                    $payrollModel->total_paid_days = $paid_days;
                    $payrollModel->employer_pf = $employer_pf;
                    $payrollModel->total_sandwich_leave = $sandwich_leave_count;
                    $payrollModel->salary_ctc = $original_ctc;
                    $payrollModel->save();

                    if (!empty($empLoan->id) && !empty($payrollModel->loan_installment)) {
                        $installmentModel = [
                            'user_id' => $userValue['id'],
                            'loan_id' => $empLoan->id,
                            'payroll_id' => $payrollModel->id,
                            'month' => $month,
                            'year' => $year,
                            'date' => date("Y-m-d"),
                            'amount' => $payrollModel->loan_installment,
                            'status' => 'Enabled',
                            'created_at' => date('Y-m-d H:i:s'),
                            'created_ip' => $request->ip(),
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_ip' => $request->ip(),
                        ];
                        LoanTransaction::insert($installmentModel);
                    }
                }
            }
        }
    }

    public function calculate_sandwich()
    {
        $user_list = User::where('status', 'Enabled')->get();
        $year = date('Y', strtotime('-15 day', strtotime(date('Y-m-d'))));
        $month = date('m', strtotime('-15 day', strtotime(date('Y-m-d'))));

        foreach ($user_list as $user) {

            $this->sandwich_leaves_by_user($user->id, $year, $month);
        }
    }

    public function sandwich_leaves_by_user($user_id, $year, $month)
    {
        $month = date('m', strtotime('-15 day', strtotime(date('Y-m-d'))));
        //get leaves of this month where('date', 'LIKE', $year . '-' . $month . '%')
        $leaves_list = Leaves::where(function ($query) use ($year, $month) {
            $query->where('start_date', 'LIKE', $year . '-' . $month . '%');
            $query->orWhere('end_date', 'LIKE', $year . '-' . $month . '%');
        })
            ->where('user_id', $user_id)
            ->get();



        if ($leaves_list->count() == 0) {
            return 0;
        } else {
            $check_start_date = "";
            $check_end_date = "";

            foreach ($leaves_list as $key => $leave) {

                $sanwich_check_date = date('Y-m-d', strtotime($leave->end_date . ' +1 day'));

                if (date('D', strtotime($sanwich_check_date)) == 'Sun') {
                    //check if next day is leave
                    if (isset($leaves_list[$key + 1])) {
                        if ($leave->end_day != 1 || $leaves_list[$key + 1]->start_day != 1) {
                            continue;
                        }
                        if (strtotime($leaves_list[$key + 1]->start_date) == strtotime($sanwich_check_date . ' +1 day')) {
                            //this is sandwich
                            $insert_arr = [
                                'first_leave_id' => $leave->id,
                                'second_leave_id' => $leaves_list[$key + 1]->id,
                                'sandwich_reason' => 'Weekend',
                                'sandwich_date' => $sanwich_check_date,
                                'user_id' => $user_id,
                                'deduct_extra' => 1,
                                'sandwich_total_day' => 1,
                                'created_at' => date('Y-m-d H:i:s')
                            ];
                            \App\Sandwich_leave::insert($insert_arr);
                        }
                    }
                } else {
                    if (isset($leaves_list[$key + 1])) {
                        if ($leave->end_day != 1 || $leaves_list[$key + 1]->start_day != 1) {
                            continue;
                        }
                        //check for holiday
                        $check_start_date = $leave->end_date;
                        $check_end_date = $leaves_list[$key + 1]->start_date;
                        $single_holiday_sandwich_check_date = date('Y-m-d', strtotime($check_start_date . ' +1 day'));
                        $leave_diff = date_diff(date_create($check_start_date), date_create($check_end_date));
                        //get single day holiday
                        $single_day_holiday = Holiday::where('start_date', $single_holiday_sandwich_check_date)
                            ->where('end_date', $single_holiday_sandwich_check_date)
                            ->get();
                        if ($single_day_holiday->count() > 0) {
                            if (strtotime($check_end_date) == strtotime($single_day_holiday . ' +1 day')) {
                                //this is sandwich
                                $insert_arr = [
                                    'first_leave_id' => $leave->id,
                                    'second_leave_id' => $leaves_list[$key + 1]->id,
                                    'sandwich_reason' => 'Holiday',
                                    'sandwich_date' => $single_holiday_sandwich_check_date,
                                    'user_id' => $user_id,
                                    'deduct_extra' => 1,
                                    'sandwich_total_day' => 1,
                                    'created_at' => date('Y-m-d H:i:s')
                                ];
                                \App\Sandwich_leave::insert($insert_arr);
                            }
                        } else {
                            $insert_arr = [];
                            //check for multi-day holiday
                            $multi_day_holiday_list = Holiday::where('start_date', '!=', 'end_date')->get();
                            if ($multi_day_holiday_list->count() > 0) {
                                $holiday_check_date = $multi_day_holiday_list[0]->start_date;
                                $sandwich_check_date = date('Y-m-d', strtotime($check_start_date . ' +1 day'));
                                $total_holiday = 0;
                                while (strtotime($holiday_check_date) <= $multi_day_holiday_list[0]->end_date) {

                                    if (strtotime($holiday_check_date) == strtotime($sandwich_check_date) && strtotime($sandwich_check_date) <= strtotime($check_end_date)) {
                                        //this is sandwich
                                        $insert_arr[] = [
                                            'first_leave_id' => $leave->id,
                                            'second_leave_id' => $leaves_list[$key + 1]->id,
                                            'sandwich_reason' => 'Holiday',
                                            'sandwich_date' => $sandwich_check_date,
                                            'user_id' => $user_id,
                                            'deduct_extra' => 1,
                                            'sandwich_total_day' => 1,
                                            'created_at' => date('Y-m-d H:i:s')
                                        ];
                                        $sandwich_check_date = date('Y-m-d', strtotime($sandwich_check_date . ' +1 day'));
                                    }
                                    $holiday_check_date = date('Y-m-d', strtotime($holiday_check_date . ' +1 day'));
                                }

                                //check if count of leave insert array = count of difference between 2 leave then sandwich
                                if (count($insert_arr) == $leave_diff->format("%a") && !empty($insert_arr)) {
                                    \App\Sandwich_leave::insert($insert_arr);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function addEarnedLeave(Request $request)
    {
        $currentYear = date('Y');
        $startDate = date('Y-m-01');
        $endDate = date('Y-m-25');

        $userList = User::where('status', 'Enabled')->get();
        foreach ($userList as $userKey => $userValue) {
            $AttendanceMList = AttendanceMaster::select('user_id', DB::raw('count(*) as total'))->whereBetween('date', [$startDate, $endDate])->whereIn('availability_status', [3, 6])->where('user_id', $userValue->id)->groupBy('user_id')->get()->toArray();
            // 3 = Leave, 6 = Mixed Leave

            if (empty($AttendanceMList)) {
                $earnedLeave = EarnedLeave::where('user_id', $userValue->id)->where('year', $currentYear)->get()->first();
                if (!empty($earnedLeave)) {
                    $earnedLeave = EarnedLeave::where('id', $earnedLeave->id)->update(['count' => $earnedLeave->count + 1]);
                } else {
                    $earnedLeaveModel = [
                        'user_id' => $userValue->id,
                        'year' => $currentYear,
                        'count' => 1,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                    EarnedLeave::insert($earnedLeaveModel);
                }
            }
        }
    }

    // Run Every year 31-12-XXXX for add earned leave of user
    public function releaseEarnedLeave(Request $request)
    {
        $currentYear = date('Y');
        $userList = User::where('status', 'Enabled')->get();

        foreach ($userList as $userKey => $userValue) {
            $earnedLeave = EarnedLeave::where('user_id', $userValue->id)->where('year', $currentYear)->get()->first();
            if (!empty($earnedLeave)) {
                $leaveMaster = LeaveMaster::where('leave_category_id', '=', 2)->where('user_id', '=', $userValue->id)->get()->first();
                if (!empty($leaveMaster)) {
                    $leaveUpdate = LeaveMaster::where('id', $leaveMaster->id)->update(['balance' => $earnedLeave->count]);
                } else {
                    $leaveAdd = new LeaveMaster();
                    $leaveAdd->user_id = $userValue->id;
                    $leaveAdd->leave_category_id = 2; // 2 = Earned leave
                    $leaveAdd->balance = $earnedLeave->count;
                    $leaveAdd->created_at = date('Y-m-d H:i:s');
                    $leaveAdd->created_ip = $request->ip();
                    $leaveAdd->updated_at = date('Y-m-d H:i:s');
                    $leaveAdd->updated_ip = $request->ip();
                    $leaveAdd->save();
                }
            }
        }
    }

    public function inward_outward_due_remider(Request $request)
    {
        $today_date = date('Y-m-d');
        $unanswered_inward = \App\Inward_outwards::where('is_answered', 'No')->get();

        foreach ($unanswered_inward as $inward) {
            $inward_date = date('Y-m-d', strtotime($inward->expected_ans_date));
            $earlier = new DateTime($today_date);
            $later = new DateTime($inward_date);

            $diff = $later->diff($earlier)->format("%a");
            if ($diff <= 10 && $diff >= 0) {
                //get list of users involved in this registry
                $user_ids = \App\Inward_outward_users::where('inward_outward_id', $inward['id'])->get(['user_id'])->pluck('user_id')->toArray();
                $registry_number = $inward->inward_outward_no;
                //send reminders
                $this->notification_task->registryResponseReminderNotify($user_ids, $registry_number);

                $email_list = User::whereIn('id', $user_ids)->get(['email'])->pluck('email')->toArray();
                $mail_data = [
                    'registry_number' => $registry_number,
                    'expected_ans_date' => date('d-m-Y', strtotime($inward->expected_ans_date)),
                    'to_email_list' => $email_list
                ];
                $this->common_task->registryResponseReminderEmail($mail_data);
            }
        }
    }

    public function general_notification()
    {
        $user_ids = User::where('status', 'Enabled')->get(['id'])->pluck('id')->toArray();

        //$this->notification_task->generalNotify($user_ids);
    }

    public function late_leave_approval(Request $request)
    {
        $start_date = date('Y-01-01');
        $end_date = date('Y-01-31');
        //echo $start_date.'/'.$end_date; die();
        while (strtotime($start_date) <= strtotime($end_date)) {

            $leave_data = Leaves::whereDate('start_date', '<=', $start_date)->whereDate('end_date', '>=', $start_date)->where('leave_status', 2)->get();

            if ($leave_data->count() == 0) {
                $start_date = date('Y-m-d', strtotime($start_date . ' + 1 days'));
                continue;
            }



            foreach ($leave_data as $leave) {
                $attendance_data = AttendanceMaster::where('date', $start_date)->where('user_id', $leave->user_id)
                    ->where(function ($q) {
                        $q->where('availability_status', 1);
                        $q->orWhere('availability_status', 5);
                        $q->orWhere('availability_status', 4);
                    })->get();

                if ($attendance_data->count() == 0) {
                    continue;
                } else {
                    if ($attendance_data[0]->availability_status != 1 && $attendance_data[0]->availability_status != 5 && $attendance_data[0]->availability_status != 4) {
                        continue;
                    }
                }

                $update_arr = [
                    'leave_cron_note' => 'Late Approved Leave, Attendance Type 1 to 3 By Cron Job At ' . date('Y-m-d H:i:s'),
                    'availability_status' => 3,
                    'availability_id' => $leave->id,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_ip' => $request->ip()
                ];
                AttendanceMaster::where('id', $attendance_data[0]->id)->update($update_arr);
            }
            $start_date = date('Y-m-d', strtotime($start_date . ' + 1 days'));
        }
    }

    public function cron_insExpired(Request $request)
    {  //this
        $cron_dates = Vehicle_Insurance::where('status', 'Live')->get(['id', 'asset_id', 'insurance_date', 'renew_date']);

        foreach ($cron_dates as $key => $date) {

            if ($date->renew_date) {

                $currDate = strtotime(date('Y-m-d'));
                $renwDate = strtotime($date->renew_date);

                if ($currDate > $renwDate) {

                    $arr = ['status' => 'Expired'];
                    Vehicle_Insurance::where('id', $date->id)->update($arr);

                    $ins_details = Vehicle_Insurance::where('id', $date->id)->first();
                    $aset_holder = AssetAccess::where('status', 'Confirmed')->where('asset_id', $date->asset_id)->get(['asset_access_user_id']);
                    $email_list = User::where('status', 'Enabled')->whereIn('role', [config('constants.REAL_HR'), config('constants.SuperUser')])->pluck('email')->toArray();

                    $mail_data = [];
                    if ($aset_holder->count() > 0) {
                        $user_email = User::where('id', $aset_holder[0]->asset_access_user_id)->pluck('email')->toArray();
                        $email_list = array_merge($email_list, $user_email);
                    }

                    $mail_data['to_email'] = $email_list;
                    $mail_data['ins_number'] = $ins_details->insurance_number;
                    $mail_data['ins_type'] = $ins_details->type;
                    $mail_data['expiration_date'] = date('d-m-Y', strtotime($ins_details->renew_date));

                    $this->common_task->cron_expiredInsNotify($mail_data);
                }
            }
        }
    }

    public function cron_insExpiredlefdays(Request $request)
    {

        $cron_dates = Vehicle_Insurance::where('status', 'Live')->get(['id', 'asset_id', 'insurance_date', 'renew_date']);


        foreach ($cron_dates as $key => $date) {

            if ($date->renew_date) {

                $currDate = strtotime(date('Y-m-d'));

                $renwDate = strtotime($date->renew_date);

                if ($renwDate > $currDate) {

                    $datDiff = $renwDate - $currDate;
                    $left_days = round($datDiff / (60 * 60 * 24));

                    $ins_details = Vehicle_Insurance::where('id', $date->id)->first();
                    $aset_holder = AssetAccess::where('status', 'Confirmed')->where('asset_id', $date->asset_id)->get(['asset_access_user_id']);
                    $email_list = User::where('status', 'Enabled')->whereIn('role', [config('constants.REAL_HR'), config('constants.SuperUser')])->pluck('email')->toArray();

                    $mail_data = [];
                    if ($aset_holder->count() > 0) {
                        $user_email = User::where('id', $aset_holder[0]->asset_access_user_id)->pluck('email')->toArray();
                        $email_list = array_merge($email_list, $user_email);
                    }

                    $mail_data['to_email'] = $email_list;
                    $mail_data['ins_number'] = $ins_details->insurance_number;
                    $mail_data['ins_type'] = $ins_details->type;
                    $mail_data['expiration_date'] = date('d-m-Y', strtotime($ins_details->renew_date));

                    if ($left_days == 30) {
                        $this->common_task->cron_renewdInsNotify($mail_data);
                    } elseif ($left_days == 10) {
                        $this->common_task->cron_renewdInsNotify($mail_data);
                    } elseif ($left_days == 5) {
                        $this->common_task->cron_renewdInsNotify($mail_data);
                    } elseif ($left_days == 1) {
                        $this->common_task->cron_renewdInsNotify($mail_data);
                    }
                }
            }
        }
    }

    public function execute_expense(Request $request)
    {
        $expense_list = \App\Employee_expense::where('repeat_execute', 0)->get();
        if ($expense_list->count() > 0) {
            foreach ($expense_list as $key => $expense) {
                $check_repeat = \App\Employee_expense::where('project_id', $expense->project_id)
                    ->where('amount', $expense->amount)
                    ->where('voucher_no', $expense->voucher_no)
                    ->where('voucher_no', '!=', NULL)
                    ->where('bill_number', $expense->bill_number)
                    ->where('voucher_ref_no', $expense->voucher_ref_no)
                    ->get();
                if ($check_repeat->count() > 1) {
                    //dd($check_repeat);
                    foreach ($check_repeat as $repeat) {
                        $update_arr = [
                            'voucher_repeat' => 1,
                            'repeat_execute' => 1
                        ];
                        \App\Employee_expense::where('id', $repeat->id)->update($update_arr);
                    }
                }
            }
        }
    }

    public function pass_to_process_inward()
    {
        $inward_data = \App\Inward_outwards::where('pass_search_process', 0)->get();
        $curl = curl_init();
        foreach ($inward_data as $data) {

            $filename_arr = explode('/', $data->document_file);

            curl_setopt_array($curl, array(
                CURLOPT_URL => "http://139.59.8.252:3001/api/pdf_content/insert_pdf/",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => array('file_path' => end($filename_arr), 'inward_id' => $data->id),
            ));

            $response = curl_exec($curl);
            \App\Inward_outwards::where('id', $data->id)->update(['pass_search_process' => 1]);
        }
        curl_close($curl);
    }

    public function auto_punch_out(Request $request)
    {
        $today = date('Y-m-d');
        $attendanceLastRecord = AttendanceMaster::select(['id', 'user_id', 'date'])->where('date', '=', $today)
            ->with(['attendanceDetail' => function ($query) {
                $query->select(['id', 'attendance_master_id', 'punch_type']);
            }])
            ->with(['user' => function ($query) {
                $query->select(['id', 'user_attend_type'])->where('status', '=', 'Enabled');
            }])->get();

        foreach ($attendanceLastRecord as $value) {
            if (!empty($value->attendanceDetail[0]) && $value->attendanceDetail[0]->punch_type === 'IN') {
                $attendanceDetail = new AttendanceDetail();
                $attendanceDetail->attendance_master_id = $value->id;
                $attendanceDetail->auto_entry = 1;
                $attendanceDetail->time = $today . ' 23:59:00';
                $attendanceDetail->punch_type = "OUT";
                $attendanceDetail->device_type = 'WEB';
                $attendanceDetail->is_approved = "Pending";
                $attendanceDetail->created_at = date('Y-m-d H:i:s');
                $attendanceDetail->created_ip = $request->ip();
                $attendanceDetail->updated_at = date('Y-m-d H:i:s');
                $attendanceDetail->updated_ip = $request->ip();


                if ($value->user->user_attend_type === 'Trip') {
                    $attendanceDetail->save();
                    AttendanceMaster::where('id', '=', $value->id)->update(['on_trip' => 'YES']);
                }
            }
        }
    }

    public function auto_punch_in(Request $request)
    {
        $today = date('Y-m-d');
        $prevDay = date('Y-m-d', strtotime("-1 days"));

        $attendanceLastRecord = AttendanceMaster::select(['id', 'user_id', 'date', 'on_trip'])->where('date', '=', $prevDay)->where('on_trip', '=', 'YES')->get();

        foreach ($attendanceLastRecord as $value) {
            $attendanceFirstRecord = AttendanceMaster::select(['id', 'date', 'user_id'])->where('date', '=', $today)->where('user_id', '=', $value->user_id)->first();
            if (!empty($attendanceFirstRecord)) {
                $attendanceMasterId = $attendanceFirstRecord->id;
            } else {
                $attendanceMaster = new AttendanceMaster();
                $attendanceMaster->user_id = $value->user_id;
                $attendanceMaster->date = $today;
                $attendanceMaster->availability_status = 1; // 1 => Present;
                $attendanceMaster->first_in = $today . ' 00:05:00';
                // Need to ask - $attendanceMaster->availability_id = NULL;
                $attendanceMaster->is_late = 'NO';
                $attendanceMaster->late_time = '00:00:00';
                $attendanceMaster->is_late_more = 'NO';
                $attendanceMaster->created_at = date('Y-m-d H:i:s');
                $attendanceMaster->created_ip = $request->ip();
                $attendanceMaster->updated_at = date('Y-m-d H:i:s');
                $attendanceMaster->updated_ip = $request->ip();
                $attendanceMaster->save();
                $attendanceMasterId = $attendanceMaster->id;
            }
            $attendanceDetail = new AttendanceDetail();
            $attendanceDetail->attendance_master_id = $attendanceMasterId;
            $attendanceDetail->auto_entry = 1;
            $attendanceDetail->time = $today . ' 00:05:00';
            $attendanceDetail->punch_type = "IN";
            $attendanceDetail->device_type = 'WEB';
            $attendanceDetail->is_approved = "Pending";
            $attendanceDetail->created_at = date('Y-m-d H:i:s');
            $attendanceDetail->created_ip = $request->ip();
            $attendanceDetail->updated_at = date('Y-m-d H:i:s');
            $attendanceDetail->updated_ip = $request->ip();
            $attendanceDetail->save();
        }
    }

    public function expire_remote_attend_request(Request $request)
    {

        $expired_request = \App\RemoteAttendanceRequest::where('is_used', 0)
            ->whereDate('date', '<', Carbon::yesterday()->format('Y-m-d'))
            ->get();
        if ($expired_request->count() > 0) {
            foreach ($expired_request as $expired) {
                \App\RemoteAttendanceRequest::where('id', $expired->id)->update(['is_used' => 2, 'updated_at' => date('Y-m-d H:i:s'), 'updated_ip' => $request->ip()]);
            }
        }
    }

    public function auto_policy_approve(Request $request)
    {

        $pending_reviced_policy = \App\RevisePolicy::where('status', 'Pending')
            ->whereRaw("DATEDIFF('" . Carbon::now() . "',created_at)  > 10")
            ->get();

        if ($pending_reviced_policy->count() > 0) {
            $all_users = User::where('is_user_relieved', 0)
                ->where('status', 'Enabled')->pluck('id')->toArray();

            foreach ($pending_reviced_policy as $policy) {
                $approved_users = \App\UserRevisePolicy::where('revise_policy_id', $policy->id)->get(['user_id'])->pluck('user_id')->toArray();
                $remain_users = array_diff($all_users, $approved_users);
                if (!empty($remain_users)) {
                    foreach ($remain_users as $key => $user) {
                        $insert_arr[$key] = [
                            'policy_id' => $policy->policy_id,
                            'revise_policy_id' => $policy->id,
                            'user_id' => $user,
                            'status' => 'Approved',
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s')
                        ];
                    }
                    \App\UserRevisePolicy::insert($insert_arr);
                    $hr_user = User::where('role', config('constants.REAL_HR'))->get();
                    $hr_name = $hr_user[0]->name;
                    $mail_data = [
                        'to_email' => $hr_user->pluck('email')->toArray(),
                        'policy_number' => $policy->revise_number,
                        'name' => $hr_name
                    ];
                    $this->common_task->policy_auto_approve($mail_data);
                }
            }
        }
    }

    //========================================== Employee Insurance ==============
    //13/04/2020

    public function cron_employeeInsExpired(Request $request)
    {  //this
        $cron_dates = Employee_Insurance::where('status', 'Live')->get(['id', 'employee_id', 'insurance_date', 'renew_date']);

        foreach ($cron_dates as $key => $date) {

            if ($date->renew_date) {

                $currDate = strtotime(date('Y-m-d'));
                $renwDate = strtotime($date->renew_date);

                if ($currDate > $renwDate) {

                    $arr = ['status' => 'Expired'];
                    Employee_Insurance::where('id', $date->id)->update($arr);

                    $ins_details = Employee_Insurance::where('id', $date->id)->first();
                    $email_list = User::where('status', 'Enabled')->whereIn('role', [config('constants.REAL_HR'), config('constants.SuperUser')])->pluck('email')->toArray();

                    $mail_data = [];
                    if ($date->employee_id > 0) {
                        $user_email = User::where('id', $date->employee_id)->pluck('email')->toArray();
                        $email_list = array_merge($email_list, $user_email);
                    }

                    $mail_data['to_email'] = $email_list;
                    $mail_data['ins_number'] = $ins_details->policy_number;
                    $mail_data['ins_type'] = Employee_insurance_types::where('id', $ins_details->type_id)->value('title');
                    $mail_data['expiration_date'] = date('d-m-Y', strtotime($ins_details->renew_date));

                    $this->common_task->cron_expiredEmployeeInsNotify($mail_data);
                }
            }
        }
    }

    //13/04/2020
    public function cron_employeeinsExpiredlefdays(Request $request)
    {

        $cron_dates = Employee_Insurance::where('status', 'Live')->get(['id', 'employee_id', 'insurance_date', 'renew_date']);


        foreach ($cron_dates as $key => $date) {

            if ($date->renew_date) {

                $currDate = strtotime(date('Y-m-d'));

                $renwDate = strtotime($date->renew_date);

                if ($renwDate > $currDate) {

                    $datDiff = $renwDate - $currDate;
                    $left_days = round($datDiff / (60 * 60 * 24));

                    $ins_details = Employee_Insurance::where('id', $date->id)->first();
                    $email_list = User::where('status', 'Enabled')->whereIn('role', [config('constants.REAL_HR'), config('constants.SuperUser')])->pluck('email')->toArray();

                    $mail_data = [];
                    if ($date->employee_id > 0) {
                        $user_email = User::where('id', $date->employee_id)->pluck('email')->toArray();
                        $email_list = array_merge($email_list, $user_email);
                    }

                    $mail_data['to_email'] = $email_list;
                    $mail_data['ins_number'] = $ins_details->policy_number;
                    $mail_data['ins_type'] = Employee_insurance_types::where('id', $ins_details->type_id)->value('title');
                    $mail_data['expiration_date'] = date('d-m-Y', strtotime($ins_details->renew_date));

                    if ($left_days == 30) {
                        $this->common_task->cron_renewdEmployeeInsNotify($mail_data);
                    } elseif ($left_days == 10) {
                        $this->common_task->cron_renewdEmployeeInsNotify($mail_data);
                    } elseif ($left_days == 5) {
                        $this->common_task->cron_renewdEmployeeInsNotify($mail_data);
                    } elseif ($left_days == 1) {
                        $this->common_task->cron_renewdEmployeeInsNotify($mail_data);
                    }
                }
            }
        }
    }

    public function cron_assignUserHardCopy(Request $request)
    {

        $assinees_dates = Document_softcopy_access::join('document_softcopy', 'document_softcopy_access.document_softcopy_id', '=', 'document_softcopy.id')
            ->where('document_softcopy.status', 'Enable')
            ->where('document_softcopy_access.is_returnable', 'Yes')
            ->where('document_softcopy_access.assignee_status', 'Accepted')
            ->get(['document_softcopy_access.assignee_returnDate', 'document_softcopy_access.id', 'document_softcopy_access.assignee_id']);


        foreach ($assinees_dates as $key => $date) {

            if ($date->assignee_returnDate) {

                $currDate = strtotime(date('Y-m-d'));

                $returnDate = strtotime($date->assignee_returnDate);

                $email_list = User::where('id', $date->assignee_id)->pluck('email')->toArray();
                $mail_data = [];
                $mail_data['to_email'] = $email_list;
                $mail_data['assignee_name'] = User::where('id', $date->assignee_id)->value('name');
                $mail_data['return_date'] = date('d-m-Y', strtotime($date->assignee_returnDate));

                if ($returnDate > $currDate) {

                    $datDiff = $returnDate - $currDate;
                    $left_days = round($datDiff / (60 * 60 * 24));
                    if ($left_days == 2) {
                        $this->common_task->cron_remindSubmitHardCopyNotify($mail_data);
                    }
                } elseif ($returnDate == $currDate) {
                    $this->common_task->cron_submitHardCopyNotify($mail_data);
                }
            }
        }
    }

    // ================================= Tender opening email and notification ===================
    public function get_tender_details($id)
    {
        $tender_data = Tender::select('tender.id', 'tender.tender_sr_no', 'tender.tender_id_per_portal', 'tender.portal_name', 'tender.tender_no', 'tender.name_of_work', 'tender.state_name_work_execute', 'tender_client_detail.client_name', 'department.dept_name')->Leftjoin('tender_client_detail', 'tender.id', '=', 'tender_client_detail.tender_id')->Leftjoin('department', 'tender.department_id', '=', 'department.id')->where('tender.id', $id)->first();
        return $tender_data;
    }

    public function preliminary_opening_tender_notify()
    {

        $tenders = Tender::where('tender_status', 'Selected')->whereNotNull('opening_status_preliminary_datetime')->get()->toArray();

        $current_date = date('Y-m-d H:i:s');
        foreach ($tenders as $key => $value) {
            $days_ago = date('Y-m-d H:i:s', strtotime('-10 days', strtotime($value['opening_status_preliminary_datetime'])));

            if ($days_ago <= $current_date && $value['opening_status_preliminary_datetime'] >= $current_date) {

                $t_user = explode(',', $value['assign_tender']);
                $users_email = user::whereIn('id', $t_user)->pluck('email')->toArray();
                $tender_data = $this->get_tender_details($value['id']);

                $mail_data = [];
                $mail_data['name'] = "";
                $mail_data['opening_email_type'] = "Preliminary opening";
                $mail_data['opening_date'] = date('d/m/Y', strtotime($value['opening_status_preliminary_datetime']));
                $mail_data['to_email'] = $users_email;
                $mail_data['client_name'] = $tender_data['client_name'];
                $mail_data['tender_sr_no'] = $tender_data['tender_sr_no'];
                $mail_data['tender_id'] = $tender_data['tender_id_per_portal'];
                $mail_data['dept_name'] = $tender_data['dept_name'];
                $mail_data['portal_name'] = $tender_data['portal_name'];
                $mail_data['tender_no'] = $tender_data['tender_no'];
                $mail_data['state_name'] = $tender_data['state_name_work_execute'];
                $mail_data['name_of_work'] = $tender_data['name_of_work'];
                $this->common_task->opening_tender($mail_data);

                $messages = "Preliminary opening tender date " . date('d/m/Y', strtotime($value['opening_status_preliminary_datetime'])) . " is coming soon.";
                $this->notification_task->openingTenderNotify($t_user, 'Preliminary Opening Date', $messages);
            }
        }
    }

    public function technical_opening_tender_notify()
    {

        $tenders = Tender::where('tender_status', 'Selected')->whereNotNull('opening_status_technical_datetime')->get()->toArray();

        $current_date = date('Y-m-d H:i:s');
        foreach ($tenders as $key => $value) {
            $days_ago = date('Y-m-d H:i:s', strtotime('-10 days', strtotime($value['opening_status_technical_datetime'])));

            if ($days_ago <= $current_date && $value['opening_status_technical_datetime'] >= $current_date) {

                $t_user = explode(',', $value['assign_tender']);
                $users_email = user::whereIn('id', $t_user)->pluck('email')->toArray();
                $tender_data = $this->get_tender_details($value['id']);

                $mail_data = [];
                $mail_data['name'] = "";
                $mail_data['opening_email_type'] = "Technical opening";
                $mail_data['opening_date'] = date('d/m/Y', strtotime($value['opening_status_technical_datetime']));
                $mail_data['to_email'] = $users_email;
                $mail_data['client_name'] = $tender_data['client_name'];
                $mail_data['tender_sr_no'] = $tender_data['tender_sr_no'];
                $mail_data['tender_id'] = $tender_data['tender_id_per_portal'];
                $mail_data['dept_name'] = $tender_data['dept_name'];
                $mail_data['portal_name'] = $tender_data['portal_name'];
                $mail_data['tender_no'] = $tender_data['tender_no'];
                $mail_data['state_name'] = $tender_data['state_name_work_execute'];
                $mail_data['name_of_work'] = $tender_data['name_of_work'];
                $this->common_task->opening_tender($mail_data);

                $messages = "Technical opening tender date " . date('d/m/Y', strtotime($value['opening_status_technical_datetime'])) . " is coming soon.";
                $this->notification_task->openingTenderNotify($t_user, 'Technical Opening Date', $messages);
            }
        }
    }

    public function financial_opening_tender_notify()
    {

        $tenders = Tender::where('tender_status', 'Selected')->whereNotNull('opening_status_financial_datetime')->get()->toArray();

        $current_date = date('Y-m-d H:i:s');
        foreach ($tenders as $key => $value) {
            $days_ago = date('Y-m-d H:i:s', strtotime('-10 days', strtotime($value['opening_status_financial_datetime'])));

            if ($days_ago <= $current_date && $value['opening_status_financial_datetime'] >= $current_date) {

                $t_user = explode(',', $value['assign_tender']);
                $users_email = user::whereIn('id', $t_user)->pluck('email')->toArray();
                $tender_data = $this->get_tender_details($value['id']);

                $mail_data = [];
                $mail_data['name'] = "";
                $mail_data['opening_email_type'] = "Financial opening";
                $mail_data['opening_date'] = date('d/m/Y', strtotime($value['opening_status_financial_datetime']));
                $mail_data['to_email'] = $users_email;
                $mail_data['client_name'] = $tender_data['client_name'];
                $mail_data['tender_sr_no'] = $tender_data['tender_sr_no'];
                $mail_data['tender_id'] = $tender_data['tender_id_per_portal'];
                $mail_data['dept_name'] = $tender_data['dept_name'];
                $mail_data['portal_name'] = $tender_data['portal_name'];
                $mail_data['tender_no'] = $tender_data['tender_no'];
                $mail_data['state_name'] = $tender_data['state_name_work_execute'];
                $mail_data['name_of_work'] = $tender_data['name_of_work'];
                $this->common_task->opening_tender($mail_data);

                $messages = "Financial opening tender date " . date('d/m/Y', strtotime($value['opening_status_financial_datetime'])) . " is coming soon.";
                $this->notification_task->openingTenderNotify($t_user, 'Financial Opening Date', $messages);
            }
        }
    }

    public function commercial_opening_tender_notify()
    {

        $tenders = Tender::where('tender_status', 'Selected')->whereNotNull('opening_status_commercial_datetime')->get()->toArray();

        $current_date = date('Y-m-d H:i:s');
        foreach ($tenders as $key => $value) {
            $days_ago = date('Y-m-d H:i:s', strtotime('-10 days', strtotime($value['opening_status_commercial_datetime'])));

            if ($days_ago <= $current_date && $value['opening_status_commercial_datetime'] >= $current_date) {

                $t_user = explode(',', $value['assign_tender']);
                $users_email = user::whereIn('id', $t_user)->pluck('email')->toArray();
                $tender_data = $this->get_tender_details($value['id']);

                $mail_data = [];
                $mail_data['name'] = "";
                $mail_data['opening_email_type'] = "Commercial opening";
                $mail_data['opening_date'] = date('d/m/Y', strtotime($value['opening_status_commercial_datetime']));
                $mail_data['to_email'] = $users_email;
                $mail_data['client_name'] = $tender_data['client_name'];
                $mail_data['tender_sr_no'] = $tender_data['tender_sr_no'];
                $mail_data['tender_id'] = $tender_data['tender_id_per_portal'];
                $mail_data['dept_name'] = $tender_data['dept_name'];
                $mail_data['portal_name'] = $tender_data['portal_name'];
                $mail_data['tender_no'] = $tender_data['tender_no'];
                $mail_data['state_name'] = $tender_data['state_name_work_execute'];
                $mail_data['name_of_work'] = $tender_data['name_of_work'];
                $this->common_task->opening_tender($mail_data);

                $messages = "Commercial opening tender date " . date('d/m/Y', strtotime($value['opening_status_commercial_datetime'])) . " is coming soon.";
                $this->notification_task->openingTenderNotify($t_user, 'Commercial Opening Date', $messages);
            }
        }
    }

    //============================== Cheque 
    public function cron_remainingBlankCheque(Request $request)
    {
        $cheque_list = DB::table('cheque_register')
            ->select('check_ref_no', DB::raw('count(*) as total'))
            ->where('is_used', 'not_used')
            ->where('is_signed', 'no')
            ->where('is_failed', 0)
            ->where('is_mail_fire', 0)
            ->groupBy('check_ref_no')
            ->get()->toArray();

        $email = user::where('status', 'Enabled')->whereIn('role', [config('constants.SuperUser'), config('constants.ACCOUNT_ROLE')])->pluck('email')->toArray();
        $notify_ids = user::where('status', 'Enabled')->whereIn('role', [config('constants.SuperUser'), config('constants.ACCOUNT_ROLE')])->pluck('id')->toArray();
        $mail_data = [];
        $mail_data['email_list'] = $email;

        if (count($cheque_list) > 0) {
            foreach ($cheque_list as $key => $value) {
                if ($value['total'] <= 10) {

                    DB::table('cheque_register')->where('check_ref_no', $value['check_ref_no'])->update(['is_mail_fire' => 1]);
                    $mail_data['cheque_book_no'] = $value['check_ref_no'];
                    $this->common_task->cronRemainBlankCheque($mail_data);
                    $this->notification_task->emptyChequeBookNotify($value['check_ref_no'], $notify_ids);
                }
            }
        }
    }

    public function cron_remainingSignedCheque(Request $request)
    {
        $cheque_list = DB::table('cheque_register')
            ->select('check_ref_no', DB::raw('count(*) as total'))
            ->where('is_signed', 'yes')
            ->where('is_failed', 0)
            ->where('is_signed_mail_fire', 0)
            ->groupBy('check_ref_no')
            ->get()->toArray();

        $email = user::where('status', 'Enabled')->whereIn('role', [config('constants.SuperUser'), config('constants.ACCOUNT_ROLE')])->pluck('email')->toArray();
        $notify_ids = user::where('status', 'Enabled')->whereIn('role', [config('constants.SuperUser'), config('constants.ACCOUNT_ROLE')])->pluck('id')->toArray();
        $mail_data = [];
        $mail_data['email_list'] = $email;

        if (count($cheque_list) > 0) {
            foreach ($cheque_list as $key => $value) {
                if ($value['total'] <= 5) {

                    DB::table('cheque_register')->where('check_ref_no', $value['check_ref_no'])->update(['is_signed_mail_fire' => 1]);
                    $mail_data['cheque_book_no'] = $value['check_ref_no'];
                    $this->common_task->cronRemainSignedCheque($mail_data);
                    $this->notification_task->emptySignedChequeBookNotify($value['check_ref_no'], $notify_ids);
                }
            }
        }
    }

    //================================  RTGS 
    public function cron_remainingBlankRtgs(Request $request)
    {
        $rtgs_list = DB::table('rtgs_register')
            ->select('rtgs_ref_no', DB::raw('count(*) as total'))
            ->where('is_used', 'not_used')
            ->where('is_signed', 'no')
            ->where('is_failed', 0)
            ->where('is_mail_fire', 0)
            ->groupBy('rtgs_ref_no')
            ->get()->toArray();

        $email = user::where('status', 'Enabled')->whereIn('role', [config('constants.SuperUser'), config('constants.ACCOUNT_ROLE')])->pluck('email')->toArray();
        $notify_ids = user::where('status', 'Enabled')->whereIn('role', [config('constants.SuperUser'), config('constants.ACCOUNT_ROLE')])->pluck('id')->toArray();
        $mail_data = [];
        $mail_data['email_list'] = $email;

        if (count($rtgs_list) > 0) {
            foreach ($rtgs_list as $key => $value) {
                if ($value['total'] <= 10) {

                    DB::table('rtgs_register')->where('rtgs_ref_no', $value['rtgs_ref_no'])->update(['is_mail_fire' => 1]);
                    $mail_data['rtgs_book_no'] = $value['rtgs_ref_no'];
                    $this->common_task->cronRemainBlankRtgs($mail_data);
                    $this->notification_task->emptyRtgsBookNotify($value['rtgs_ref_no'], $notify_ids);
                }
            }
        }
    }

    public function cron_remainingSignedRtgs(Request $request)
    {
        $cheque_list = DB::table('rtgs_register')
            ->select('rtgs_ref_no', DB::raw('count(*) as total'))
            ->where('is_signed', 'yes')
            ->where('is_failed', 0)
            ->where('is_signed_mail_fire', 0)
            ->groupBy('rtgs_ref_no')
            ->get()->toArray();

        $email = user::where('status', 'Enabled')->whereIn('role', [config('constants.SuperUser'), config('constants.ACCOUNT_ROLE')])->pluck('email')->toArray();
        $notify_ids = user::where('status', 'Enabled')->whereIn('role', [config('constants.SuperUser'), config('constants.ACCOUNT_ROLE')])->pluck('id')->toArray();
        $mail_data = [];
        $mail_data['email_list'] = $email;

        if (count($cheque_list) > 0) {
            foreach ($cheque_list as $key => $value) {
                if ($value['total'] <= 5) {

                    DB::table('rtgs_register')->where('rtgs_ref_no', $value['rtgs_ref_no'])->update(['is_signed_mail_fire' => 1]);
                    $mail_data['rtgs_book_no'] = $value['rtgs_ref_no'];
                    $this->common_task->cronRemainSignedrtgs($mail_data);
                    $this->notification_task->emptySignedRtgsBookNotify($value['rtgs_ref_no'], $notify_ids);
                }
            }
        }
    }

    //19/06/2020
    public function cron_registry_emp_on_leave()
    {

        $all_employee = Inward_outward_distrubuted_work::where('emp_status', 'Accepted')
            ->whereDate('working_start_datetime', '>', date('Y-m-d'))
            ->get(['id', 'support_employee_id', 'working_start_datetime', 'work_day'])->toArray();

        if (!empty($all_employee)) {

            foreach ($all_employee as $key => $emp) {

                $work_start_date = date('Y-m-d', strtotime($emp['working_start_datetime']));
                $nDays = $emp['work_day'] - 1;
                $work_end_date = $emp['work_day'] == 1 ? $work_start_date : date('Y-m-d', strtotime($work_start_date . '+ ' . $nDays . 'days'));

                $check_leave = Leaves::where('user_id', $emp['support_employee_id'])
                    ->where('leave_status', 2)
                    ->where(function ($query) use ($work_start_date, $work_end_date) {

                        if ($work_start_date && $work_end_date) {
                            if (strtotime($work_start_date) != strtotime($work_end_date)) {
                                $query->where([['start_date', '>=', $work_start_date], ['start_date', '<=', $work_end_date]]);
                                $query->orWhere([['end_date', '>=', $work_start_date], ['end_date', '<=', $work_end_date]]);
                            } else {
                                $query->where([['start_date', '<=', $work_start_date], ['end_date', '>=', $work_start_date]]);
                            }
                        }
                    })->get(['id', 'assign_work_user_id', 'start_date', 'end_date']);

                if ($check_leave->count() > 0) {

                    $leave_work_days = [];

                    if ($work_start_date == $check_leave[0]->start_date) {
                        $start_leave = $work_start_date;
                    } elseif ($work_start_date == $check_leave[0]->end_date) {
                        $start_leave = $work_start_date;
                    } elseif ($work_start_date > $check_leave[0]->start_date && $work_start_date < $check_leave[0]->end_date) {
                        $start_leave = $work_start_date;
                    } elseif ($work_start_date < $check_leave[0]->start_date) {
                        $start_leave = $check_leave[0]->start_date;
                    }
                    #----------------------------------------------------------------
                    if ($work_end_date == $check_leave[0]->end_date) {
                        $end_leave = $work_end_date;
                    } elseif ($work_end_date == $check_leave[0]->end_date) {
                        $end_leave = $work_end_date;
                    } elseif ($work_end_date > $check_leave[0]->start_date && $work_end_date < $check_leave[0]->end_date) {
                        $end_leave = $work_end_date;
                    } elseif ($work_end_date > $check_leave[0]->end_date) {
                        $end_leave = $check_leave[0]->end_date;
                    } elseif ($work_end_date < $check_leave[0]->end_date) {
                        $end_leave = $work_end_date;
                    }
                    array_push($leave_work_days, $start_leave, $end_leave);
                    //dd($check_leave->toArray());
                    Inward_outward_distrubuted_work::where('id', $emp['id'])->update(['reliever_user_id' => $check_leave[0]->assign_work_user_id, 'reliever_dates' => json_encode($leave_work_days)]);
                }
            }
        }
    }

    //14/07/2020
    //------------------ Compliance Reminder -------------------
    public function insertReminder($compliance_id, $remind_date)
    {
        $user_ids = Compliance_reminders::where('id', $compliance_id)->first(['responsible_person_id', 'payment_responsible_person_id', 'checker_id', 'super_admin_checker_id', 'periodicity_time'])->toArray();
        //$users_arr = array_values($user_ids);
        //foreach ($users_arr as $key => $user) {
        $complianceDoneModel = new Compliance_reminders_done_status();
        $complianceDoneModel->compliance_reminders_id = $compliance_id;
        $complianceDoneModel->responsible_person_id = $user_ids['responsible_person_id'];
        $complianceDoneModel->responsible_person_status = "Pending";
        $complianceDoneModel->payment_responsible_person_id = $user_ids['payment_responsible_person_id'];
        $complianceDoneModel->payment_responsible_person_status = "Pending";
        $complianceDoneModel->checker_id = $user_ids['checker_id'];
        $complianceDoneModel->checker_status = "Pending";
        $complianceDoneModel->super_admin_checker_id = $user_ids['super_admin_checker_id'];
        $complianceDoneModel->super_admin_checker_status = "Pending";
        $complianceDoneModel->remind_entry_date = $remind_date;
        $complianceDoneModel->remind_entry_time = $user_ids['periodicity_time'];
        $complianceDoneModel->created_at = date('Y-m-d h:i:s');
        $complianceDoneModel->updated_at = date('Y-m-d h:i:s');
        $complianceDoneModel->save();
        //}
    }

    public function dayModify($date, $day)
    {
        $in = date_create($date);

        $out = $in->setDate($in->format('Y'), $in->format('m'), $day);
        return $out->format('Y-m-d');
    }

    public function dayMonthModify($date, $day, $month)
    {
        $in = date_create($date);

        $out = $in->setDate($in->format('Y'), $month, $day);
        return $out->format('Y-m-d');
    }

    public function monthWiseReminder($records_arr, $month)
    {
        foreach ($records_arr as $key => $remind_month) {

            $nMonth = $month;
            $first_repeat_month = date('Y-m-d', strtotime($remind_month['start_date'] . '+ ' . $nMonth . 'months'));
            $first_month_day = $this->dayModify($first_repeat_month, $remind_month['periodic_date']);

            $first_repeat_entry_month = Compliance_reminders_done_status::where('compliance_reminders_id', $remind_month['id'])
                ->where('remind_entry_date', $first_month_day)->orderBy('created_at', 'desc')->first();
            if (!$first_repeat_entry_month) {
                $this->insertReminder($remind_month['id'], $first_month_day);
            } else {
                $day_last_entry_month = Compliance_reminders_done_status::where('compliance_reminders_id', $remind_month['id'])->orderBy('created_at', 'desc')->first();
                if (strtotime(date('Y-m-d')) >= strtotime($day_last_entry_month['remind_entry_date']) && time() >= strtotime($day_last_entry_month['remind_entry_time'])) {
                    $next_repeat_month_day = date('Y-m-d', strtotime($day_last_entry_month['remind_entry_date'] . '+ ' . $nMonth . 'months'));
                    $this->insertReminder($remind_month['id'], $next_repeat_month_day);
                }
            }
        }
    }

    public function yearWiseReminder($records_arr, $year)
    {
        foreach ($records_arr as $key => $remind_year) {

            $nYear = $year;
            $first_repeat_year = date('Y-m-d', strtotime($remind_year['start_date'] . '+ ' . $nYear . 'years'));
            $first_year_day = $this->dayMonthModify($first_repeat_year, $remind_year['periodic_date'], $remind_year['periodic_month']);

            $first_repeat_entry_year = Compliance_reminders_done_status::where('compliance_reminders_id', $remind_year['id'])
                ->where('remind_entry_date', $first_year_day)->orderBy('created_at', 'desc')->first();
            if (!$first_repeat_entry_year) {
                $this->insertReminder($remind_year['id'], $first_year_day);
            } else {
                $day_last_entry_year = Compliance_reminders_done_status::where('compliance_reminders_id', $remind_year['id'])->orderBy('created_at', 'desc')->first();
                if (strtotime(date('Y-m-d')) >= strtotime($day_last_entry_year['remind_entry_date']) && time() >= strtotime($day_last_entry_year['remind_entry_time'])) {
                    $next_repeat_year_day = date('Y-m-d', strtotime($day_last_entry_year['remind_entry_date'] . '+ ' . $nYear . 'years'));
                    $this->insertReminder($remind_year['id'], $next_repeat_year_day);
                }
            }
        }
    }

    public function cron_ComplianceReminder()
    {

        $reminders_day = Compliance_reminders::where('periodicity_type', 'Day')->get()->toArray();
        if (!empty($reminders_day)) {
            foreach ($reminders_day as $key => $remind_day) {

                $nDays = $remind_day['periodic_date'];
                $first_repeat_day = date('Y-m-d', strtotime($remind_day['start_date'] . '+ ' . $nDays . 'days'));

                $first_repeat_entry = Compliance_reminders_done_status::where('compliance_reminders_id', $remind_day['id'])
                    ->where('remind_entry_date', $first_repeat_day)->orderBy('created_at', 'desc')->first();

                if (!$first_repeat_entry) {
                    $this->insertReminder($remind_day['id'], $first_repeat_day);
                } else {
                    $day_last_entry = Compliance_reminders_done_status::where('compliance_reminders_id', $remind_day['id'])->orderBy('created_at', 'desc')->first();
                    if (strtotime(date('Y-m-d')) >= strtotime($day_last_entry['remind_entry_date']) && time() >= strtotime($day_last_entry['remind_entry_time'])) {
                        $next_repeat_day = date('Y-m-d', strtotime($day_last_entry['remind_entry_date'] . '+ ' . $nDays . 'days'));
                        $this->insertReminder($remind_day['id'], $next_repeat_day);
                    }
                }
            }
        }
        #---------------- WEEK
        $reminders_weeks = Compliance_reminders::where('periodicity_type', 'Week')->get()->toArray();
        if (!empty($reminders_weeks)) {
            foreach ($reminders_weeks as $key => $remind_week) {

                $nWeeks = $remind_week['periodic_week_day'];
                $first_repeat_week_day = date('Y-m-d', strtotime($nWeeks . ' ' . 'next week'));

                // $first_repeat_entry_week = Compliance_reminders_done_status::where('compliance_reminders_id',$remind_week['id'])
                //                 ->where('remind_entry_date',$first_repeat_week_day)->orderBy('created_at', 'desc')->first();
                $init_entry = Compliance_reminders_done_status::where('compliance_reminders_id', $remind_week['id'])->get()->count();
                if ($init_entry == 0) {
                    $this->insertReminder($remind_week['id'], $first_repeat_week_day);
                } else {
                    $nWeekDay = 7;
                    $day_last_entry_week = Compliance_reminders_done_status::where('compliance_reminders_id', $remind_week['id'])->orderBy('created_at', 'desc')->first();
                    if (strtotime(date('Y-m-d')) >= strtotime($day_last_entry_week['remind_entry_date']) && time() >= strtotime($day_last_entry_week['remind_entry_time'])) {
                        $next_repeat_week_day = date('Y-m-d', strtotime($day_last_entry_week['remind_entry_date'] . '+ ' . $nWeekDay . 'days'));
                        $this->insertReminder($remind_week['id'], $next_repeat_week_day);
                    }
                }
            }
        }

        #----------------- 2Months
        $reminders_quater = Compliance_reminders::where('periodicity_type', '2Months')->get()->toArray();
        if (!empty($reminders_quater)) {
            $months = 2;
            $this->monthWiseReminder($reminders_quater, $months);
        }

        #----------------- Month
        $reminders_month = Compliance_reminders::where('periodicity_type', 'Month')->get()->toArray();
        if (!empty($reminders_month)) {
            foreach ($reminders_month as $key => $remind_month) {

                $nMonth = 1;  //$remind_week['periodic_date']
                $first_repeat_month = date('Y-m-d', strtotime($remind_month['start_date'] . '+ ' . $nMonth . 'months'));

                $first_month_day = $this->dayModify($first_repeat_month, $remind_month['periodic_date']);

                $first_repeat_entry_month = Compliance_reminders_done_status::where('compliance_reminders_id', $remind_month['id'])
                    ->where('remind_entry_date', $first_month_day)->orderBy('created_at', 'desc')->first();
                if (!$first_repeat_entry_month) {
                    $this->insertReminder($remind_month['id'], $first_month_day);
                } else {
                    $day_last_entry_month = Compliance_reminders_done_status::where('compliance_reminders_id', $remind_month['id'])->orderBy('created_at', 'desc')->first();
                    if (strtotime(date('Y-m-d')) >= strtotime($day_last_entry_month['remind_entry_date']) && time() >= strtotime($day_last_entry_month['remind_entry_time'])) {
                        $next_repeat_month_day = date('Y-m-d', strtotime($day_last_entry_month['remind_entry_date'] . '+ ' . $nMonth . 'months'));
                        $this->insertReminder($remind_month['id'], $next_repeat_month_day);
                    }
                }
            }
        }
        #------------------ Quater
        $reminders_quater = Compliance_reminders::where('periodicity_type', 'Quater')->get()->toArray();
        if (!empty($reminders_quater)) {
            $quater = 3;
            $this->monthWiseReminder($reminders_quater, $quater);
        }
        #----------------- Halfyearly
        $reminders_halfyearly = Compliance_reminders::where('periodicity_type', 'Halfyearly')->get()->toArray();
        if (!empty($reminders_halfyearly)) {
            $halfyearly = 6;
            $this->monthWiseReminder($reminders_halfyearly, $halfyearly);
        }
        #----------------- Yearly
        $reminders_years = Compliance_reminders::where('periodicity_type', 'Yearly')->get()->toArray();
        if (!empty($reminders_years)) {
            foreach ($reminders_years as $key => $remind_year) {

                $nYear = 1;
                $first_repeat_year = date('Y-m-d', strtotime($remind_year['start_date'] . '+ ' . $nYear . 'years'));
                $first_year_day = $this->dayMonthModify($first_repeat_year, $remind_year['periodic_date'], $remind_year['periodic_month']);

                $first_repeat_entry_year = Compliance_reminders_done_status::where('compliance_reminders_id', $remind_year['id'])
                    ->where('remind_entry_date', $first_year_day)->orderBy('created_at', 'desc')->first();
                if (!$first_repeat_entry_year) {
                    $this->insertReminder($remind_year['id'], $first_year_day);
                } else {
                    $day_last_entry_year = Compliance_reminders_done_status::where('compliance_reminders_id', $remind_year['id'])->orderBy('created_at', 'desc')->first();
                    if (strtotime(date('Y-m-d')) >= strtotime($day_last_entry_year['remind_entry_date']) && time() >= strtotime($day_last_entry_year['remind_entry_time'])) {
                        $next_repeat_year_day = date('Y-m-d', strtotime($day_last_entry_year['remind_entry_date'] . '+ ' . $nYear . 'years'));
                        $this->insertReminder($remind_year['id'], $next_repeat_year_day);
                    }
                }
            }
        }
        #------------------ Biyearly
        $reminders_biyearly = Compliance_reminders::where('periodicity_type', 'Biyearly')->get()->toArray();
        if (!empty($reminders_biyearly)) {
            $biyearly = 2;
            $this->yearWiseReminder($reminders_biyearly, $biyearly);
        }
        #----------------- 3Years
        $reminders_threeyearly = Compliance_reminders::where('periodicity_type', '3Years')->get()->toArray();
        if (!empty($reminders_threeyearly)) {
            $threeyearly = 3;
            $this->yearWiseReminder($reminders_threeyearly, $threeyearly);
        }
        #------------------ 5years
        $reminders_fiveyearly = Compliance_reminders::where('periodicity_type', '5years')->get()->toArray();
        if (!empty($reminders_fiveyearly)) {
            $fiveyearly = 5;
            $this->yearWiseReminder($reminders_fiveyearly, $fiveyearly);
        }
    }

    public function remindMailNotify($remind)
    {
        $mail_data = [];
        $mail_data['compliance_category'] = $compliance_type = $remind['compliance_type'];
        $mail_data['company'] = $remind['company_name'];
        $mail_data['compliance_name'] = $remind['compliance_name'];
        $mail_data['due_date'] = date('d-m-Y', strtotime($remind['remind_entry_date']));
        $mail_data['due_time'] = date('h:i A', strtotime($remind['remind_entry_time']));
        $due_date = date('d-m-Y', strtotime($remind['remind_entry_date'])) . ' ' . date('h:i A', strtotime($remind['remind_entry_time']));

        if ($remind['responsible_person_status'] == 'Pending') {

            $user_list = [$remind['responsible_person_id']];
            $mail_data['email_list'] = [$remind['responsible_person_id']];
            $this->common_task->repeatComplianceRemind($mail_data);
            $this->notification_task->remindComplianceNotify($compliance_type, $user_list, $due_date);
        } elseif ($remind['payment_responsible_person_status'] == 'Pending') {

            $user_list = [$remind['payment_responsible_person_id']];
            $mail_data['email_list'] = [$remind['payment_responsible_person_id']];
            $this->common_task->repeatComplianceRemind($mail_data);
            $this->notification_task->remindComplianceNotify($compliance_type, $user_list, $due_date);
        } elseif ($remind['checker_status'] == 'Pending') {

            $user_list = [$remind['checker_id']];
            $mail_data['email_list'] = [$remind['checker_id']];
            $this->common_task->repeatComplianceRemind($mail_data);
            $this->notification_task->remindComplianceNotify($compliance_type, $user_list, $due_date);
        } elseif ($remind['super_admin_checker_status'] == 'Pending') {

            $user_list = [$remind['super_admin_checker_id']];
            $mail_data['email_list'] = [$remind['super_admin_checker_id']];
            $this->common_task->repeatComplianceRemind($mail_data);
            $this->notification_task->remindComplianceNotify($compliance_type, $user_list, $due_date);
        }
    }

    public function cron_ComplianceReminderNotify()
    {
        $get_fields = [
            'compliance_reminders.periodicity_type',
            'compliance_reminders.compliance_name',
            'compliance_reminders.first_day_interval',
            'compliance_reminders.second_day_interval',
            'compliance_reminders.third_day_interval',
            'company.company_name',
            'compliance_category.compliance_name as compliance_type',
            'compliance_reminders_done_status.*'
        ];

        $reminders_list = Compliance_reminders::join('company', 'company.id', '=', 'compliance_reminders.company_id')
            ->join('compliance_category', 'compliance_category.id', '=', 'compliance_reminders.compliance_category_id')
            ->join('compliance_reminders_done_status', 'compliance_reminders.id', '=', 'compliance_reminders_done_status.compliance_reminders_id')
            ->whereRaw('compliance_reminders_done_status.id IN (select MAX(id) FROM compliance_reminders_done_status GROUP BY compliance_reminders_id)')
            ->get($get_fields)->toArray();

        foreach ($reminders_list as $key => $remind) {
            if ($remind['periodicity_type'] != 'Day' && $remind['first_day_interval'] && date('Y-m-d') < $remind['remind_entry_date']) {

                $currDate = strtotime(date('Y-m-d'));
                $remindDate = strtotime($remind['remind_entry_date']);
                $datDiff = $remindDate - $currDate;
                $left_days = round($datDiff / (60 * 60 * 24));

                if ($left_days == $remind['first_day_interval']) {
                    $this->remindMailNotify($remind);
                } elseif ($left_days == $remind['second_day_interval']) {
                    $this->remindMailNotify($remind);
                } elseif ($left_days == $remind['third_day_interval']) {
                    $this->remindMailNotify($remind);
                }
            }
        }
    }

    public function offline_chat_notify(Request $request)
    {
        //\App\Test::insert(['test_type'=>'ok1']);
        $message_detail = $request->all();
        $jid_arr = explode('@', $message_detail['from']);
        $from_user_id = $jid_arr[0] - config('constants.CHAT_USER_ADD');

        $user_detail = User::where('id', $from_user_id)->get(['name']);

        $to_jid_arr = explode('@', $message_detail['to']);
        $to_user_id = $to_jid_arr[0] - config('constants.CHAT_USER_ADD');
        //\App\Test::insert(['test_type'=>$user_detail[0]->name]);
        $this->notification_task->chatOfflineNotify($message_detail['body'], [$to_user_id], $user_detail[0]->name);
    }

    public function todayPunchInCron(Request $request)
    {

        $today = date('Y-m-d');
        $lateTime = date('Y-m-d 09:30:00');

        $todayPunchIn = AttendanceMaster::select(['id', 'user_id', 'date', 'first_in'])->with('user')->where('date', '=', $today)->whereIn('availability_status', [config('constants.ATTENDANCE_STATUS.Present'), config('constants.ATTENDANCE_STATUS.Pending')])->orderBy('first_in', 'ASC')->get()->toArray();

        $message = '';
        $message .= 'Hello,<br>';
        $message .= 'Punch in details on ' . date('d/m/yy', strtotime($today)) . ' as below.';
        $message .= '<table rules="all" style="border-color: black;" cellpadding="10">';
        $message .= "<tr><td><strong>Name</strong></td><td><strong>Time</strong></td></tr>";
        foreach ($todayPunchIn as $value) {
            $colorCode = '#FFFFFF';
            if ($lateTime < $value['first_in'])
                $colorCode = '#FF0000';

            $message .= "<tr style='background: " . $colorCode . ";'>" . $value['user']['name'] . "</td><td>" . date('h:i A', strtotime($value['first_in'])) . "</td></tr>";
        }
        $message .= '</table>';

        $email_list = User::where('status', 'Enabled')->whereIn('role', [config('constants.Admin'), config('constants.SuperUser')])->pluck('email')->toArray();

        $mail_data = [
            'mail_subject' => 'Punch-In details',
            'mail_format' => $message,
            'to_email' => $email_list,
        ];

        $this->common_task->sendDailyPunchInOut($mail_data);
    }

    public function todayPunchOutCron(Request $request)
    {

        $today = date('Y-m-d');

        $todayPunchIn = AttendanceMaster::select(['id', 'user_id', 'date', 'last_out'])->with('user')->where('date', '=', $today)->whereIn('availability_status', [config('constants.ATTENDANCE_STATUS.Present'), config('constants.ATTENDANCE_STATUS.Pending')])->orderBy('last_out', 'ASC')->get()->toArray();

        $message = '';
        $message .= 'Hello,<br>';
        $message .= 'Punch out details on ' . date('d/m/yy', strtotime($today)) . ' as below.';
        $message .= '<table rules="all" style="border-color: black;" cellpadding="10">';
        $message .= "<tr><td><strong>Name</strong></td><td><strong>Time</strong></td></tr>";
        foreach ($todayPunchIn as $value) {
            $message .= "<tr>" . $value['user']['name'] . "</td><td>" . date('h:i A', strtotime($value['last_out'])) . "</td></tr>";
        }
        $message .= '</table>';

        $email_list = User::where('status', 'Enabled')->whereIn('role', [config('constants.Admin'), config('constants.SuperUser')])->pluck('email')->toArray();

        $mail_data = [
            'mail_subject' => 'Punch-Out details',
            'mail_format' => $message,
            'to_email' => $email_list,
        ];

        $this->common_task->sendDailyPunchInOut($mail_data);
    }

    public function cron_user_activity_email()
    {

        // $today_date = "2020-09-27";
        $today_date = date('Y-m-d');
        $activity = UserActionLog::whereDate('created_at', $today_date)->with(['get_user_name'])->get()->toArray();

        if ($activity) {
            $activity_arr = [];
            foreach ($activity as $key => $value) {
                $activity_arr[$value['get_user_name']['name']][] = $value;
            }
            // dd($activity_arr);
            $html = '';
            foreach ($activity_arr as $key1 => $value1) {
                $html .= '<table border="1">';
                $html .= '<tr style="background-color: grey;font-weight: bold;color: white;">';
                $html .= '<th style="width: 100px;">Time</th>';
                $html .= '<td style="width: 650px;">Employee Name : ' . $key1 . '</td>';
                $html .= '</tr>';
                foreach ($value1 as $key2 => $value2) {
                    $html .= '<tr>';
                    $html .= '<td>' . date('g:i A', strtotime($value2['created_at'])) . '</td>';
                    $html .= '<td>' . $value2['task_body'] . '</td>';
                    $html .= '</tr>';
                }
                $html .= '</table><br>';
            }
            // echo $html;
            $email_list = User::where('status', 'Enabled')->whereIn('role', [config('constants.SuperUser')])->pluck('email')->toArray();
            // $email_list = ['alpesh.wappnet@gmail.com'];

            $mail_data = [
                'mail_subject' => 'Today Activity',
                'mail_format' => $html,
                'to_email' => $email_list,
            ];
            // dd($mail_data);
            $this->common_task->sendTodayActivityLog($mail_data);
        } else {
            echo "Not any entry";
        }
    }

    public function cron_resign_day_count()
    {
        $today_date = date('Y-m-d');
        // $today_date = "2020-10-04";

        $resign_data = Resignation::where('status', 'Approved')->where('notice_period_days', '>', '0')->get();

        foreach ($resign_data as $key => $value) {
            $attendance_data = AttendanceMaster::where('user_id', $value['user_id'])->where('availability_status', 1)->whereDate('date', $today_date)->first();

            if ($attendance_data) {
                Resignation::where('user_id', $value['user_id'])->decrement('notice_period_days', 1);
            }
        }
    }

    public function cron_day_payroll_generate()
    {
        // $last_date = date('Y-m-d',strtotime('-1 days'));
        // $last_date = '2021-02-07';
        $last_date = '2021-02-22';
        $get_attendance = AttendanceMaster::whereDate('date', '=', $last_date)->get();
        // dd($get_attendance);
        $payroll_arr = [];
        $total_days = date('t');
        foreach ($get_attendance as $key => $value) {
            // echo "<pre>";print_r($value);

            // sandwitch leave
            // get Day
            $check_sunday = date('D', strtotime($value['date']));
            $get_previous_date = date('Y-m-d', strtotime($value['date'] . '-1 days'));
            $get_next_date = date('Y-m-d', strtotime($value['date'] . '+1 days'));
            $previous_leave_count = Leaves::where('user_id', $value['user_id'])->where('start_date', $get_previous_date)->where('leave_status', '2')->count();
            $next_leave_count = Leaves::where('user_id', $value['user_id'])->where('start_date', $get_next_date)->where('leave_status', '2')->count();

            // check leave available
            $current_day_leave = Leaves::where('user_id', $value['user_id'])->where('start_date', $get_next_date)->where('leave_status', '2')->first();
            if ($current_day_leave != null) {
                $leave_count = LeaveMaster::where('user_id', $value['user_id'])->where('leave_category_id', $current_day_leave['leave_category_id'])->first();
                // print_r($leave_count);
            }

            /* echo $get_previous_date."p".$previous_leave_count."<br>";
            echo $get_next_date."n".$next_leave_count."<br>"; */

            if ($previous_leave_count == 0 && $next_leave_count == 0) {
            }
            $user_info = Employees::where('user_id', $value['user_id'])->first();
            $user_salary_info = EmployeesSalary::where('user_id', $value['user_id'])->first();
            $payroll_arr[$key]['user_id'] = $value['user_id'];
            $payroll_arr[$key]['employee_code'] = $user_info['emp_code'];
            $payroll_arr[$key]['basic_salary'] = (isset($user_salary_info['basic_salary'])) ? $user_salary_info['basic_salary'] / $total_days : 0;
            $payroll_arr[$key]['hra'] = (isset($user_salary_info['hra'])) ? $user_salary_info['hra'] / $total_days : 0;
            $payroll_arr[$key]['other_allowance'] = (isset($user_salary_info['other_allowance'])) ? $user_salary_info['other_allowance'] / $total_days : 0;
            $payroll_arr[$key]['pt'] = (isset($user_salary_info['professional_tax'])) ? $user_salary_info['professional_tax'] / $total_days : 0;
            $payroll_arr[$key]['employee_pf_amount'] = (isset($user_salary_info['PF_amount'])) ? $user_salary_info['PF_amount'] / $total_days : 0;
            $payroll_arr[$key]['employer_pf_amount'] = (isset($user_salary_info['employer_pf_amount'])) ? $user_salary_info['employer_pf_amount'] / $total_days : 0;
            $payroll_arr[$key]['total_month_salary'] = (isset($user_salary_info['total_month_salary'])) ? $user_salary_info['total_month_salary'] / $total_days : 0;
            $payroll_arr[$key]['gross_salary_pm_ctc'] = (isset($user_salary_info['gross_salary_pm_ctc'])) ? $user_salary_info['gross_salary_pm_ctc'] / $total_days : 0;
            $payroll_arr[$key]['created_payroll_date'] = $last_date;
        }
        // Insert Code
        // DayPayroll::insert($payroll_arr);
        dd($payroll_arr);
    }

    public function cron_daily_cash_payment_email_report(Request $request)
    {

        $last_date = date('Y-m-d');
        // $last_date = '2021-02-13';
        $cash_list_above = CashApproval::join('company', 'company.id', '=', 'cash_approval.company_id')
            ->join('project', 'project.id', '=', 'cash_approval.project_id')
            ->join('clients', 'clients.id', '=', 'cash_approval.client_id')
            ->join('project_sites', 'project_sites.id', '=', 'cash_approval.project_site_id')
            ->join('vendor', 'vendor.id', '=', 'cash_approval.vendor_id')
            ->join('users as request_user', 'request_user.id', '=', 'cash_approval.requested_by')
            ->whereDate('cash_approval.created_at', '=', $last_date)
            ->where('cash_approval.amount', '>', '500')
            ->get(['cash_approval.*', 'company.company_name', 'clients.client_name', 'project.project_name', 'project_sites.site_name', 'vendor.vendor_name', 'request_user.name as request_user_name']);
        // dd($quereys->toArray());   

        $table_html = '';
        if (count($cash_list_above)) {

            $table_html .= '<h4>Above Rs.500 <br></h4><table border="1">';
            $table_html .= '<tr>';
            $table_html .= '<th>Payment Options</th><th>Note</th>';
            $table_html .= '<th>Compnay Name</th><th>Client Name</th><th>Project Name</th>';
            $table_html .= '<th>Site Name</th><th>Vendor Name</th><th>Amount</th>';
            $table_html .= '<th>First Approval</th><th>Second Approval</th><th>Third Approval</th>';
            $table_html .= '</tr>';

            foreach ($cash_list_above as $key => $value) {
                $table_html .= '<tr>';
                $table_html .= '<td>' . $value['payment_options'] . '</td>';
                $table_html .= '<td>' . $value['note'] . '</td>';
                $table_html .= '<td>' . $value['company_name'] . '</td>';
                $table_html .= '<td>' . $value['client_name'] . '</td>';
                $table_html .= '<td>' . $value['project_name'] . '</td>';
                $table_html .= '<td>' . $value['site_name'] . '</td>';
                $table_html .= '<td>' . $value['vendor_name'] . '</td>';
                $table_html .= '<td>' . $value['amount'] . '</td>';
                $table_html .= '<td>' . $value['first_approval_status'] . '</td>';
                $table_html .= '<td>' . $value['second_approval_status'] . '</td>';
                $table_html .= '<td>' . $value['third_approval_status'] . '</td>';
                $table_html .= '</tr>';
            }

            $table_html .= '</table><br>';
            
        }

        $cash_list_below = CashApproval::join('company', 'company.id', '=', 'cash_approval.company_id')
            ->join('project', 'project.id', '=', 'cash_approval.project_id')
            ->join('clients', 'clients.id', '=', 'cash_approval.client_id')
            ->join('project_sites', 'project_sites.id', '=', 'cash_approval.project_site_id')
            ->join('vendor', 'vendor.id', '=', 'cash_approval.vendor_id')
            ->join('users as request_user', 'request_user.id', '=', 'cash_approval.requested_by')
            ->whereDate('cash_approval.created_at', '=', $last_date)
            ->where('cash_approval.amount', '<=', '500')
            ->get(['cash_approval.*', 'company.company_name', 'clients.client_name', 'project.project_name', 'project_sites.site_name', 'vendor.vendor_name', 'request_user.name as request_user_name']);
            
            if (count($cash_list_below)) {

                $table_html .= '<h4>Below Rs.500 <br></h4><table border="1">';
                $table_html .= '<tr>';
                $table_html .= '<th>Payment Options</th><th>Note</th>';
                $table_html .= '<th>Compnay Name</th><th>Client Name</th><th>Project Name</th>';
                $table_html .= '<th>Site Name</th><th>Vendor Name</th>';
                $table_html .= '<th>Amount</th>';
                $table_html .= '<th>First Approval</th><th>Second Approval</th><th>Third Approval</th>';
                $table_html .= '</tr>';
    
                foreach ($cash_list_below as $key => $value) {
                    $table_html .= '<tr>';
                    $table_html .= '<td>' . $value['payment_options'] . '</td>';
                    $table_html .= '<td>' . $value['note'] . '</td>';
                    $table_html .= '<td>' . $value['company_name'] . '</td>';
                    $table_html .= '<td>' . $value['client_name'] . '</td>';
                    $table_html .= '<td>' . $value['project_name'] . '</td>';
                    $table_html .= '<td>' . $value['site_name'] . '</td>';
                    $table_html .= '<td>' . $value['vendor_name'] . '</td>';
                    $table_html .= '<td>' . $value['amount'] . '</td>';
                    $table_html .= '<td>' . $value['first_approval_status'] . '</td>';
                    $table_html .= '<td>' . $value['second_approval_status'] . '</td>';
                    $table_html .= '<td>' . $value['third_approval_status'] . '</td>';
                    $table_html .= '</tr>';
                }
    
                $table_html .= '</table><br>';
                
            }
        if(count($cash_list_below) || count($cash_list_above)){
            $admin_user = User::where('role', config('constants.SuperUser'))->get(['name', 'email']);

            $mail_data = [
                'to_email' => $admin_user[0]->email,
                'table_data' => $table_html,
            ];
            $this->common_task->dailyCashPaymentEmailReport($mail_data);
        }
        
    }
    public function cron_daily_bank_payment_email_report(Request $request)
    {

        $last_date = date('Y-m-d');
        // $last_date = '2021-02-13';
        $bank_list = BankPaymentApproval::join('company', 'company.id', '=', 'bank_payment_approval.company_id')
            ->join('project', 'project.id', '=', 'bank_payment_approval.project_id')
            ->join('clients', 'clients.id', '=', 'bank_payment_approval.client_id')
            ->join('project_sites', 'project_sites.id', '=', 'bank_payment_approval.project_site_id')
            ->join('vendor', 'vendor.id', '=', 'bank_payment_approval.vendor_id')
            ->whereDate('bank_payment_approval.created_at', '=', $last_date)
            ->get(['bank_payment_approval.*', 'company.company_name', 'clients.client_name', 'project.project_name', 'project_sites.site_name', 'vendor.vendor_name']);
        //    dd($bank_list->toArray());

        if (count($bank_list)) {

            $table_html = '<table border="1">';
            $table_html .= '<tr>';
            $table_html .= '<th>Payment Options</th><th>Note</th>';
            $table_html .= '<th>Compnay Name</th><th>Client Name</th><th>Project Name</th>';
            $table_html .= '<th>Site Name</th><th>Vendor Name</th><th>Amount</th>';
            $table_html .= '<th>First Approval</th><th>Second Approval</th><th>Third Approval</th>';
            $table_html .= '</tr>';

            foreach ($bank_list as $key => $value) {
                $table_html .= '<tr>';
                $table_html .= '<td>' . $value['payment_options'] . '</td>';
                $table_html .= '<td>' . $value['note'] . '</td>';
                $table_html .= '<td>' . $value['company_name'] . '</td>';
                $table_html .= '<td>' . $value['client_name'] . '</td>';
                $table_html .= '<td>' . $value['project_name'] . '</td>';
                $table_html .= '<td>' . $value['site_name'] . '</td>';
                $table_html .= '<td>' . $value['vendor_name'] . '</td>';
                $table_html .= '<td>' . $value['amount'] . '</td>';
                $table_html .= '<td>' . $value['first_approval_status'] . '</td>';
                $table_html .= '<td>' . $value['second_approval_status'] . '</td>';
                $table_html .= '<td>' . $value['third_approval_status'] . '</td>';
                $table_html .= '</tr>';
            }

            $table_html .= '</table>';
            $admin_user = User::where('role', config('constants.SuperUser'))->get(['name', 'email']);

            $mail_data = [
                'to_email' => $admin_user[0]->email,
                'table_data' => $table_html,
            ];
            $this->common_task->dailyBankPaymentEmailReport($mail_data);
        }
    }
    //    online_payment_approval
    public function cron_daily_online_payment_email_report(Request $request)
    {

        // $last_date = '2021-02-19';
        $last_date = date('Y-m-d');
        $online_list = OnlinePaymentApproval::join('company', 'company.id', '=', 'online_payment_approval.company_id')
            ->join('project', 'project.id', '=', 'online_payment_approval.project_id')
            ->join('clients', 'clients.id', '=', 'online_payment_approval.client_id')
            ->join('project_sites', 'project_sites.id', '=', 'online_payment_approval.project_site_id')
            ->join('vendor', 'vendor.id', '=', 'online_payment_approval.vendor_id')
            ->whereDate('online_payment_approval.created_at', '=', $last_date)
            ->get(['online_payment_approval.*', 'company.company_name', 'clients.client_name', 'project.project_name', 'project_sites.site_name', 'vendor.vendor_name']);
        // dd($online_list->toArray());
        if (count($online_list)) {

            $table_html = '<table border="1">';
            $table_html .= '<tr>';
            $table_html .= '<th>Payment Options</th><th>Amount</th><th>Approval Note</th>';
            $table_html .= '<th>Compnay Name</th><th>Client Name</th><th>Project Name</th>';
            $table_html .= '<th>Site Name</th><th>Vendor Name</th>';
            $table_html .= '<th>First Approval</th><th>Second Approval</th><th>Third Approval</th>';
            $table_html .= '</tr>';

            foreach ($online_list as $key => $value) {
                $table_html .= '<tr>';
                $table_html .= '<td>' . $value['payment_options'] . '</td>';
                $table_html .= '<td>' . $value['amount'] . '</td>';
                $table_html .= '<td>' . $value['approval_note'] . '</td>';
                $table_html .= '<td>' . $value['company_name'] . '</td>';
                $table_html .= '<td>' . $value['client_name'] . '</td>';
                $table_html .= '<td>' . $value['project_name'] . '</td>';
                $table_html .= '<td>' . $value['site_name'] . '</td>';
                $table_html .= '<td>' . $value['vendor_name'] . '</td>';
                $table_html .= '<td>' . $value['first_approval_status'] . '</td>';
                $table_html .= '<td>' . $value['second_approval_status'] . '</td>';
                $table_html .= '<td>' . $value['third_approval_status'] . '</td>';
                $table_html .= '</tr>';
            }

            $table_html .= '</table>';
            $admin_user = User::where('role', config('constants.SuperUser'))->get(['name', 'email']);

            $mail_data = [
                'to_email' => $admin_user[0]->email,
                'table_data' => $table_html,
            ];
            $this->common_task->dailyOnlinePaymentEmailReport($mail_data);
        }
    }

    public function cron_daily_employee_leave_report(Request $request)
    {
        // $subject_name = Leaves::whereId($request->input('id'))->value('subject');    1
        $cur_date = date('Y-m-d');    
        // $cur_date = "2021-05-06";
        $leave_list = Leaves::join('users', 'users.id', '=', 'leaves.user_id')
            ->join('leave_category', 'leave_category.id', '=', 'leaves.leave_category_id')
            ->where('start_date', '<=', $cur_date)
            ->where('end_date', '>=', $cur_date)
            ->where('leave_status','2')
            ->get(['leaves.subject', 'leaves.description', 'users.name as user_name', 'leave_category.name as leave_name', 'leaves.leave_status']);

        // dd($leave_list->toArray());
        if (count($leave_list)) {
            $table_html = '<table border="1">';
            $table_html .= '<tr>';
            $table_html .= '<th>Subject</th>';
            $table_html .= '<th>Description</th><th>User Name</th><th>Leave Name</th>';
            $table_html .= '<th>Leave Status</th>';
            $table_html .= '</tr>';

            foreach ($leave_list as $key => $value) {
                $table_html .= '<tr>';
                $table_html .= '<td>' . $value['subject'] . '</td>';
                $table_html .= '<td>' . $value['description'] . '</td>';
                $table_html .= '<td>' . $value['user_name'] . '</td>';
                $table_html .= '<td>' . $value['leave_name'] . '</td>';
                if ($value['leave_status'] == 2) {
                    $leave_status = 'Approved';
                } elseif ($value['leave_status'] == 3) {
                    $leave_status = 'Rejected';
                } elseif ($value['leave_status'] == 4) {
                    $leave_status = 'Canceled';
                } else {
                    $leave_status = 'Pending';
                }
                $table_html .= '<td>' . $leave_status . '</td>';
                $table_html .= '</tr>';
            }
            $table_html .= '</table>';

            $admin_user = User::where('role', config('constants.SuperUser'))->get(['name', 'email']);

            $mail_data = [
                'to_email' => $admin_user[0]->email,
                'table_data' => $table_html,
            ];
            $this->common_task->dailyEmployeeLeaveReport($mail_data);
        }

        // endforeach

    }
}
