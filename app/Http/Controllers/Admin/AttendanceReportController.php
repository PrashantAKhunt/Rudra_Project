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
use App\Exports\AttendanceExport;
use Carbon\Carbon;

class AttendanceReportController extends Controller
{

    public $data;

    public function __construct()
    {
        $this->data['module_title'] = "Attendance Report";
        $this->data['module_link'] = "admin.attendance_report";
    }

    public function index(Request $request)
    {
        $this->data['page_title'] = "Attendance Report";
        $this->data['user'] = User::orderBy('name')->where("status", "Enabled")->get()->pluck('name', 'id');
        $this->data['records'] = [];
        $this->data['selectedUser'] = [];
        $this->data['date'] = "";
        $this->data['report_type'] = "";
        $this->data['csv_data'] = "javascript:void(0);";

        if ($request->method() == 'POST') {

            $this->data['selectedUser'] = $request->get('user_id');
            $this->data['date'] = $request->get('date');
            $this->data['report_type'] = $request->get('report_type');
            $reportType = $request->get('report_type');
            $date = $request->get('date');
            $mainDate = explode("-", $date);
            $strFirstdate = str_replace("/", "-", $mainDate[0]);
            $strLastdate = str_replace("/", "-", $mainDate[1]);
            $first_date = date('Y-m-d H:i:s', strtotime($strFirstdate));
            $second_date = date('Y-m-d H:i:s', strtotime($strLastdate));

            if ($reportType == "attendance") {
                if ($request->has('user_id')) {
                    $attendanceData = AttendanceMaster::whereBetween('date', [$first_date, $second_date])->whereIN('user_id', $request->get('user_id'))->with(['user' => function ($query) {
                        return $query->with('employee');
                    }])->orderBy('date', 'DESC')->get();
                } else {
                    $attendanceData = AttendanceMaster::whereBetween('date', [$first_date, $second_date])->with(['user' => function ($query) {
                        return $query->with('employee');
                    }])->orderBy('date', 'DESC')->get();
                }



                $excelFileName = date('D-M-Y h:m:s') . ' ' . 'attendance_list_report.xlsx';
                //$excelFileName = 'A123' . 'attendance_list_report.xlsx';
                Excel::store(new AttendanceExport($request->all(), $first_date, $second_date), $excelFileName, 'real_public');
                //dd(new AttendanceExport($request->all(), $first_date, $second_date));
                //dd(Excel::store(new AttendanceExport($request->all(), $first_date, $second_date), $excelFileName, 'real_public'));
                //dd(asset('storage'));
                $this->data['excel_data'] = asset('storage/' . str_replace('public/', '', 'reports/attendance_report/' . $excelFileName));

                $this->data['records'] = $attendanceData;

                $columnName = array('Sr. No', 'Employee Name', 'Employee ID', 'Date', 'First IN', 'Last OUT', 'Total Hours', 'Availability Status', 'Is Late', 'Late Time');

                if (!empty($attendanceData[0])) {
                    $csvData = $this->generateCsvFiles('attendance_list_report', $columnName, $attendanceData);
                    $this->data['csv_data'] = $csvData;
                }
            }
            // Late comming list report
            if ($reportType == "latecomming") {

                if ($request->has('user_id')) {
                    $attendanceData = AttendanceMaster::whereBetween('date', [$first_date, $second_date])->whereIN('user_id', $request->get('user_id'))
                        ->where(function ($query_new) {
                            $query_new->where('is_late', "YES")->orWhere('is_late_more', "YES");
                        })->with(['user' => function ($query) {
                            return $query->with('employee');
                        }])->get();
                } else {
                    $attendanceData = AttendanceMaster::whereBetween('date', [$first_date, $second_date])
                        ->where(function ($query_new) {
                            $query_new->where('is_late', "YES")->orWhere('is_late_more', "YES");
                        })
                        ->with(['user' => function ($query) {
                            return $query->with('employee');
                        }])->get();
                }
                //dd($attendanceData);
                $this->data['records'] = $attendanceData;

                $columnName = array('Sr. No', 'Employee Name', 'Employee ID', 'Date', 'First IN', 'Last OUT', 'Total Hours', 'Availability Status', 'Is Late 9:30', 'Is Late 9:45', 'Late Time');

                if (!empty($attendanceData[0])) {
                    $csvData = $this->generateCsvFiles('latecomming_list_report', $columnName, $attendanceData);
                    $this->data['csv_data'] = $csvData;
                }
            }


            //leave balance report
            if ($reportType == "leavebalance") {

                if ($request->has('user_id')) {

                    $attendanceData = LeaveMaster::select('users.name', 'employee.emp_code', 'users.id', DB::raw('group_concat(leave_master.balance ORDER BY leave_master.leave_category_id ASC ) as balances'), DB::raw('SUM(leave_master.balance ) as total'))
                        ->join('leave_category', 'leave_master.leave_category_id', '=', 'leave_category.id')
                        ->join('users', 'leave_master.user_id', '=', 'users.id')
                        ->join('employee', 'employee.user_id', '=', 'users.id')
                        ->whereIn('leave_master.user_id', $request->get('user_id'))
                        ->where('users.status', 'Enabled')
                        ->groupBy('users.id')
                        ->groupBy('users.name')
                        ->orderBy('leave_master.user_id', 'ASC')
                        ->get();
                } else {

                    $attendanceData = LeaveMaster::select('users.name', 'employee.emp_code', 'users.id', DB::raw('group_concat(leave_master.balance ORDER BY leave_master.leave_category_id ASC ) as balances'), DB::raw('SUM(leave_master.balance ) as total'))
                        ->join('leave_category', 'leave_master.leave_category_id', '=', 'leave_category.id')
                        ->join('users', 'leave_master.user_id', '=', 'users.id')
                        ->join('employee', 'employee.user_id', '=', 'users.id')
                        ->where('users.status', 'Enabled')
                        ->groupBy('users.id')
                        ->groupBy('users.name')
                        ->orderBy('leave_master.user_id', 'ASC')
                        ->get();
                }


                $this->data['records'] = $attendanceData;
                $columnName = array('Sr. No', 'Employee Name', 'Employee ID', 'Sick Leave', 'Earned Leave', 'Casual Leave', 'Un-paid Leave', 'Short Leave', 'Comp. Off', 'ML', 'PL', 'Total Balance');

                if (!empty($attendanceData[0])) {
                    $csvData = $this->generateCsvFiles('leavebalance_list_report', $columnName, $attendanceData);
                    $this->data['csv_data'] = $csvData;
                }
            }

            //Manually Attendance report
            if ($reportType == "manually_attendance") {

                if ($request->has('user_id')) {

                    $attendanceData = AttendanceMaster::select( 'employee.emp_code','A.name AS employee_name','B.name AS add_by_username','attendance_master.first_in','attendance_master.last_out','attendance_master.total_hours','attendance_master.date','attendance_master.manual_add_reason')
                        ->leftjoin('users AS A', 'attendance_master.user_id', '=', 'A.id')
                        ->leftjoin('users AS B', 'attendance_master.manual_add_by', '=', 'B.id')
                        ->join('employee', 'employee.user_id', '=', 'A.id')
                        ->whereIn('attendance_master.user_id', $request->get('user_id'))
                        ->where('attendance_master.is_manually_added',1)
                        ->whereBetween('attendance_master.date', [$first_date, $second_date])
                        ->where('A.status', 'Enabled')
                        ->orderBy('attendance_master.date', 'DESC')
                        ->get();
                } else {

                    $attendanceData = AttendanceMaster::select('employee.emp_code','A.name AS employee_name','B.name AS add_by_username','attendance_master.first_in','attendance_master.last_out','attendance_master.total_hours','attendance_master.date','attendance_master.manual_add_reason')
                    ->leftjoin('users AS A', 'attendance_master.user_id', '=', 'A.id')
                    ->leftjoin('users AS B', 'attendance_master.manual_add_by', '=', 'B.id')
                    ->join('employee', 'employee.user_id', '=', 'A.id')
                    ->where('attendance_master.is_manually_added',1)
                    ->whereBetween('attendance_master.date', [$first_date, $second_date])
                    ->where('A.status', 'Enabled')
                    ->orderBy('attendance_master.date', 'DESC')
                    ->get();
                }


                $this->data['records'] = $attendanceData;
                $columnName = array('Sr. No', 'Employee Name', 'Employee ID','Date', 'First In', 'Last Out', 'Total Hours', 'Reason', 'Add By');

                if (!empty($attendanceData[0])) {
                    $csvData = $this->generateCsvFiles('manually_attendance_list_report', $columnName, $attendanceData);
                    $this->data['csv_data'] = $csvData;
                }
            }

            //late mark report
            if ($reportType == "late_mark") {

                if ($request->has('user_id')) {

                    $attendanceData = AttendanceMaster::select( 'employee.emp_code','A.name AS employee_name','B.name AS removed_by_username','attendance_master.first_in','attendance_master.last_out','attendance_master.is_late','attendance_master.is_late_more','attendance_master.total_hours','attendance_master.date','attendance_master.late_mark_removed_detail','attendance_master.late_time')
                        ->leftjoin('users AS A', 'attendance_master.user_id', '=', 'A.id')
                        ->leftjoin('users AS B', 'attendance_master.late_mark_removed_by', '=', 'B.id')
                        ->join('employee', 'employee.user_id', '=', 'A.id')
                        ->whereIn('attendance_master.user_id', $request->get('user_id'))
                        ->where('attendance_master.late_mark_removed',1)
                        ->whereBetween('attendance_master.date', [$first_date, $second_date])
                        ->where('A.status', 'Enabled')
                        ->orderBy('attendance_master.date', 'DESC')
                        ->get();
                } else {

                    $attendanceData = AttendanceMaster::select('employee.emp_code','A.name AS employee_name','B.name AS removed_by_username','attendance_master.first_in','attendance_master.last_out','attendance_master.is_late','attendance_master.is_late_more','attendance_master.total_hours','attendance_master.date','attendance_master.late_mark_removed_detail','attendance_master.late_time')
                    ->leftjoin('users AS A', 'attendance_master.user_id', '=', 'A.id')
                    ->leftjoin('users AS B', 'attendance_master.late_mark_removed_by', '=', 'B.id')
                    ->join('employee', 'employee.user_id', '=', 'A.id')
                    ->where('attendance_master.late_mark_removed',1)
                    ->whereBetween('attendance_master.date', [$first_date, $second_date])
                    ->where('A.status', 'Enabled')
                    ->orderBy('attendance_master.date', 'DESC')
                    ->get();
                }


                $this->data['records'] = $attendanceData;
                $columnName = array('Sr. No', 'Employee Name', 'Employee ID','Date', 'First In', 'Last Out', 'Total Hours','Is Late','Is Late More','Late Time', 'Reason', 'Removed By');

                if (!empty($attendanceData[0])) {
                    $csvData = $this->generateCsvFiles('late_mark_list_report', $columnName, $attendanceData);
                    $this->data['csv_data'] = $csvData;
                }
            }

            // On leave list report
            if ($reportType == "onleave") {
                // dd(date('Y-m-d', strtotime($first_date)));
                if ($request->has('user_id')) {
                    //print_r($request->get('user_id')); die();
                    //  $attendanceData = AttendanceMaster::whereBetween('date', [$first_date, $second_date])->whereIN('user_id', $request->get('user_id'))->where('availability_status',3)->with(['user' => function($query) { return $query->with('employee');}])->get();
                    $attendanceData = Leaves::join('users', 'users.id', '=', 'leaves.user_id')
                        ->join('employee', 'employee.user_id', '=', 'users.id')
                        ->join('users as wr_user', 'wr_user.id', '=', 'leaves.assign_work_user_id')
                        ->join('leave_category', 'leave_category.id', '=', 'leaves.leave_category_id')
                        //->whereBetween('start_date', [$first_date, $second_date])
                        ->where(function ($query) use ($first_date, $second_date) {
                            $first_date = date('Y-m-d', strtotime($first_date));
                            $second_date = date('Y-m-d', strtotime($second_date));
                            if (strtotime($first_date) != strtotime($second_date)) {
                                $query->where([['start_date', '>=', $first_date], ['start_date', '<=', $second_date]]);
                                $query->orWhere([['end_date', '>=', $first_date], ['end_date', '<=', $second_date]]);
                            } else {
                                $query->where([['start_date', '<=', $first_date], ['end_date', '>=', $first_date]]);
                            }

                            //$query->where([['start_date', '<=', date('Y-m-d', strtotime($first_date))], ['end_date', '>=', date('Y-m-d', strtotime($first_date))]]);
                            //$query->orWhere([['start_date', '<=', date('Y-m-d', strtotime($second_date))], ['end_date', '>=', date('Y-m-d', strtotime($second_date))]]);
                        })
                        /* ->where(function($query) use($first_date, $second_date){
                              $query->where('start_date','<=',date('Y-m-d',strtotime($first_date)));
                              $query->orWhere('end_date','>=',date('Y-m-d',strtotime($first_date)));
                              })
                              ->where(function($query) use($first_date, $second_date){
                              $query->where('start_date','<=',date('Y-m-d',strtotime($second_date)));
                              $query->orWhere('end_date','>=',date('Y-m-d',strtotime($second_date)));
                              }) */
                        ->whereIN('leaves.user_id', $request->get('user_id'))
                        ->where('third_approval_status', 'Approved')
                        ->get(['leaves.*', 'users.name', 'employee.emp_code', 'leave_category.name as category_name', 'wr_user.name as wr_username']);
                } else {
                    $attendanceData = Leaves::join('users', 'users.id', '=', 'leaves.user_id')
                        ->join('employee', 'employee.user_id', '=', 'users.id')
                        ->join('leave_category', 'leave_category.id', '=', 'leaves.leave_category_id')
                        ->join('users as wr_user', 'wr_user.id', '=', 'leaves.assign_work_user_id')
                        ->where(function ($query) use ($first_date, $second_date) {
                            $first_date = date('Y-m-d', strtotime($first_date));
                            $second_date = date('Y-m-d', strtotime($second_date));
                            if (strtotime($first_date) != strtotime($second_date)) {
                                $query->where([['start_date', '>=', $first_date], ['start_date', '<=', $second_date]]);
                                $query->orWhere([['end_date', '>=', $first_date], ['end_date', '<=', $second_date]]);
                            } else {
                                $query->where([['start_date', '<=', $first_date], ['end_date', '>=', $first_date]]);
                            }
                            //$query->where([['start_date', '<=', date('Y-m-d', strtotime($first_date))], ['end_date', '>=', date('Y-m-d', strtotime($first_date))]]);
                            //$query->orWhere([['start_date', '<=', date('Y-m-d', strtotime($second_date))], ['end_date', '>=', date('Y-m-d', strtotime($second_date))]]);
                        })
                        /* ->where(function($query) use($first_date, $second_date){
                              $query->where('start_date','<=',date('Y-m-d',strtotime($first_date)));
                              $query->orWhere('end_date','>=',date('Y-m-d',strtotime($first_date)));
                              })
                              ->where(function($query) use($first_date, $second_date){
                              $query->where('start_date','<=',date('Y-m-d',strtotime($second_date)));
                              $query->orWhere('end_date','>=',date('Y-m-d',strtotime($second_date)));
                              }) */
                        ->where('third_approval_status', 'Approved')
                        ->get(['leaves.*', 'users.name', 'employee.emp_code', 'leave_category.name as category_name', 'wr_user.name as wr_username']);
                    //Leaves::where('start_date', '2019-12-06')->with('users')->get();
                }

                foreach ($attendanceData as $key => $data) {

                    if ($data->start_day == 1) {
                        $data->startDay_leaveType = "Full Day";
                    } elseif ($data->start_day == 2) {
                        $data->startDay_leaveType = "First Half";
                    } else {
                        $data->startDay_leaveType = "Second Half";
                    }

                    if ($data->end_day == 1) {
                        $data->endDay_leaveType = "Full Day";
                    } elseif ($data->end_day == 2) {
                        $data->endDay_leaveType = "First Half";
                    } else {
                        $data->endDay_leaveType = "Second Half";
                    }

                    $end = strtotime($data->end_date);
                    $start = strtotime($data->start_date);
                    $datediff = $end - $start;
                    $total_diff = round($datediff / (60 * 60 * 24)) + 1;

                    $data->total_leaveDays = $total_diff;

                    if ($end == $start) {
                        if ($data->start_day == 2 || $data->start_day == 3) {

                            $data->total_leaveDays = $total_diff - 0.5;
                        }
                    } else {

                        if ($data->start_day == 2 || $data->start_day == 3) {

                            $total_diff = $total_diff - 0.5;
                        }
                        if ($data->end_day == 2 || $data->end_day == 3) {

                            $total_diff = $total_diff - 0.5;
                        }
                        $data->total_leaveDays = $total_diff;
                    }
                }

                //print_r($attendanceData); die();
                //dd($attendanceData->toArray());

                $this->data['records'] = $attendanceData;

                $columnName = array(
                    'Sr. No', 'Employee Name', 'Employee ID',
                    'Leave Start Date', 'Start Day Leave Type', 'Leave End Date', 'End Day Leave Type', 'Total Leave Days', 'Leave Type',
                    'Work Reliever',
                    'Status'
                );

                if (!empty($attendanceData[0])) {
                    $csvData = $this->generateCsvFiles('onleave_list_report', $columnName, $attendanceData);
                    $this->data['csv_data'] = $csvData;
                }
            }
        }

        $reportType = $request->get('report_type');
        $this->data['leavebalance'] = $reportType;

        return view('admin.attendance_report.index', $this->data);
    }

    public function generateCsvFiles($filename, $columnName, $rptData)
    {

        $name = date('D-M-Y h:m:s') . ' ' . $filename . '.csv';

        $file = fopen(storage_path('app/public/reports/attendance_report/') . $name, 'wb');

        if (
            $filename == "attendance_list_report" ||
            $filename == "latecomming_list_report"
            //$filename == "onleave_list_report"
        ) {

            fputcsv($file, $columnName);
            $data = [];
            foreach ($rptData as $k => $rowData) {

                $data[] = array(
                    $k + 1,
                    $rowData->user->name,
                    $rowData->user->employee->emp_code,
                    $rowData->date,
                    $rowData->first_in,
                    $rowData->last_out,
                    $rowData->total_hours,
                    config::get('constants.AVAILABILITY_STATUS')[$rowData->availability_status],
                    $rowData->is_late,
                    $rowData->late_time
                );
            }
        }

        if ($filename == "onleave_list_report") {


            fputcsv($file, $columnName);
            $data = [];

            foreach ($rptData as $k => $rowData) {

                $data[] = array(
                    $k + 1,
                    $rowData->name,
                    $rowData->emp_code,
                    $rowData->start_date,
                    $rowData->startDay_leaveType,
                    $rowData->end_date,
                    $rowData->endDay_leaveType,
                    $rowData->total_leaveDays,
                    $rowData->category_name,
                    $rowData->wr_username,
                    $rowData->third_approval_status
                );
            }
        }

        if ($filename == "manually_attendance_list_report") {


            fputcsv($file, $columnName);
            $data = [];
            foreach ($rptData as $k => $rowData) {

                $data[] = array(
                    $k + 1,
                    $rowData->employee_name,
                    $rowData->emp_code,
                    $rowData->date,
                    $rowData->first_in,
                    $rowData->last_out,
                    $rowData->total_hours,
                    $rowData->manual_add_reason,
                    $rowData->add_by_username
                );
            }

        }

        if ($filename == "late_mark_list_report") {


            fputcsv($file, $columnName);
            $data = [];
            foreach ($rptData as $k => $rowData) {

                $data[] = array(
                    $k + 1,
                    $rowData->employee_name,
                    $rowData->emp_code,
                    $rowData->date,
                    $rowData->first_in,
                    $rowData->last_out,
                    $rowData->total_hours,
                    $rowData->is_late,
                    $rowData->is_late_more,
                    $rowData->late_time,
                    $rowData->late_mark_removed_detail,
                    $rowData->removed_by_username
                );
            }

        }

        if ($filename == "leavebalance_list_report") {


            fputcsv($file, $columnName);
            $data = [];
            foreach ($rptData as $k => $rowData) {

                $leave_balance = $rowData->balances;

                $balance_value_arr = explode(",", $leave_balance);

                $data[$k] = array(
                    $k + 1,
                    $rowData->name,
                    $rowData->emp_code,
                );
                $data[$k] = array_merge($data[$k], $balance_value_arr);  //merge balace_val array in existing arr..

                array_push($data[$k], $rowData->total);   //add total_balance value in last position in array..
            }
        }



        foreach ($data as $row) {
            fputcsv($file, $row);
        }
        return asset('storage/' . str_replace('public/', '', 'reports/attendance_report/' . $name));
    }

    public function generateExcelFiles()
    {
        // $days = [];
        // $date = \Carbon\Carbon::now();
        // $date2 = \Carbon\Carbon::now();
        // $firstMonth =  $date->subMonth()->startOfMonth();
        // $lastMonth =  $date2->subMonth()->endOfMonth();

        // for($i = 0; $i <= $date2->diffInDays($date); $i++){
        //     $fdate = Carbon::parse($firstMonth)->addDays($i)->format('D');
        //     $days[$i]['day'] = $fdate;
        //     $days[$i]['date'] = Carbon::parse($firstMonth)->addDays($i)->format('d');
        // }

        // $firstMonth = $firstMonth->format('d.m.Y');
        // $lastMonth = $lastMonth->format('d.m.Y');

        // return view('admin.attendance_report.attendance_excel', compact('days','firstMonth','lastMonth'));
        return Excel::download(new AttendanceExport, 'invoices.xlsx');
        dd("hi");
    }
}
