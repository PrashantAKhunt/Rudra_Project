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
use App\Payroll;
use App\AttendanceDetail;
use App\AttendanceMaster;
use Rap2hpoutre\FastExcel\FastExcel;

class FinanceReportController extends Controller {

    public $data;

    public function __construct() {
        $this->data['module_title'] = "Finance Report";
        $this->data['module_link'] = "admin.finance_report";
    }

    public function index(Request $request) {


        $this->data['page_title'] = "Finance Report";
        $this->data['user'] = User::orderBy('name')->where("status", "Enabled")->get()->pluck('name', 'id');
        $this->data['records'] = [];
        $this->data['selectedUser'] = [];
        $this->data['date'] = "";
        $this->data['report_type'] = "";
        $this->data['csv_data'] = "javascript:void(0);";
	$this->data['xls_data']="";
        if ($request->method() == 'POST') {
            $this->data['selectedUser'] = $request->get('user_id');
            $this->data['date'] = $request->get('date');
            $this->data['report_type'] = $request->get('report_type');
            $reportType = $request->get('report_type');
            $date = $request->get('date');
            $mainDate = explode("-", $date);
            $strFirstdate = str_replace("/", "-", $mainDate[0]);
            $strLastdate = str_replace("/", "-", $mainDate[1]);
            $first_date = date('Y-m-d h:m:s', strtotime($strFirstdate));
            $second_date = date('Y-m-d h:m:s', strtotime($strLastdate));

            if ($reportType == "salaryreport") {

                if ($request->has('user_id')) {
                    $salaryreport = Payroll::whereBetween('date', [$first_date, $second_date])->whereIN('user_id', $request->get('user_id'))->with(['user' => function($query) {
                                    return $query->with('employee');
                                }])->get();
                } else {
                    $salaryreport = Payroll::whereBetween('date', [$first_date, $second_date])->with(['user' => function($query) {
                                    return $query->with('employee');
                                }])->get();
                }

                $this->data['records'] = $salaryreport;
                $previouse_1_salary_date = date('m-Y', strtotime('-1 months', strtotime($first_date)));
                $previouse_2_salary_date = date('m-Y', strtotime('-2 months', strtotime($first_date)));

                $previouse_1_salary_date_arr = explode('-', $previouse_1_salary_date);
                $previouse_2_salary_date_arr = explode('-', $previouse_2_salary_date);
                $previouse_1_salary_title = 'PREVIOUS MONTH SALARY(1) ' . $previouse_1_salary_date;
                $previouse_2_salary_title = 'PREVIOUS MONTH SALARY(1) ' . $previouse_2_salary_date;
                $columnName = array('User',
                    'Month', 'Year', $previouse_1_salary_title, $previouse_2_salary_title,
                    'GROSS CTC', 'Actual Basic Salary',
                    'Actual HRA', 'Actual Others', 'Payble Basic Salary',
                    'Payble HRA', 'Payble Others', 'Food',
                    'Working Day', 'EMP Working Day', 'Sandwich Leave', 'Unpaid Leave', 'SICK LEAVE',
                    'CASUAL LEAVE', 'EARNED LEAVE', 'COMP OFF LEAVE', 'Total Leave',
                    'Unpaid LA', 'PT', 'PF', 'PENDING LOAN BALANCE', 'Loan Installment', 'Extra Loan/Deduction Amount',
                    'Extra Loan/Deduction Detail', 'Penalty', 'Manual Penalty',
                    'MANUAL Penalty Note', 'Payable Salary', 'Gross Salary',
                    'First Approval', 'Second Approval', 'Third Approval', 'Fourth Approval', 'Final Approval',
                    'Generated Date'
                );
                
                if (!empty($salaryreport[0])) {
                    $data[] = $columnName;
                    foreach ($salaryreport as $k => $rowData) {

                        //get previous 2 month salary
                        $previous1 = Payroll::where('user_id', $rowData->user_id)->where('month', $previouse_1_salary_date_arr[0])
                                ->where('year', $previouse_1_salary_date_arr[1])
                                ->get();
                        $previous2 = Payroll::where('user_id', $rowData->user_id)->where('month', $previouse_2_salary_date_arr[0])
                                ->where('year', $previouse_2_salary_date_arr[1])
                                ->get();

                        $user_salary_detail = \App\EmployeesSalary::where('user_id', $rowData->user_id)->get();
                        $user_loan_bal = \App\EmployeesLoans::where('user_id', $rowData->user_id)
                                ->where('loan_status', 'Approved')
                                ->where('status', 'Enabled')
                                ->get();
                        $total_loan_amt = 0;
                        foreach ($user_loan_bal as $loan_amt) {
                            $total_loan_amt = $total_loan_amt + ($loan_amt->loan_amount - $loan_amt->completed_loan_amount);
                        }
                        
                        $previous1_amt=0;$previous2_amt=0;
                        if($previous1->count()>0){
                            $previous1_amt=$previous1[0]->payable_salary;
                        }
                        if($previous2->count()>0){
                            $previous2_amt=$previous2[0]->payable_salary;
                        }
				
                        $data[] = array(
                            $rowData->user->name,
                            $rowData->month,
                            $rowData->year,
                            $previous1_amt,
                            $previous2_amt,
                            $user_salary_detail[0]->gross_salary_pm_ctc,
                            $user_salary_detail[0]->basic_salary,
                            $user_salary_detail[0]->hra,
                            $user_salary_detail[0]->other_allowance,
                            $rowData->basic_salary,
                            $rowData->hra,
                            $rowData->others,
                            $rowData->food,
                            //$rowData->user->employee->emp_code,
                            $rowData->working_day,
                            $rowData->employee_working_day,
                            $rowData->total_sandwich_leave,
                            $rowData->unpaid_leave,
                            $rowData->sick_leave,
                            $rowData->cacual_leave,
                            $rowData->earned_leave,
                            $rowData->comp_off_leave,
                            $rowData->total_leave,
                            $rowData->unpaid_leave_amount,
                            $rowData->professional_tax,
                            $rowData->pf,
                            $total_loan_amt,
                            $rowData->loan_installment,
                            $rowData->extra_loan_amount,
                            $rowData->extra_loan_details,
                            $rowData->penalty,
                            $rowData->manual_penalty,
                            $rowData->penalty_note,
                            $rowData->payable_salary,
                            $rowData->salary_ctc,
                            $rowData->first_approval_status,
                            $rowData->second_approval_status,
                            $rowData->third_approval_status,
                            $rowData->fourth_approval_status,
                            $rowData->fifth_approval_status,
                            $rowData->date,
                        );
                    }
                    
                    $xls_data = date('D-M-Y h:m:s') . 'salary_list_report.xlsx';
                    Excel::store(new \App\Exports\PayrollExport($data), 'public/reports/attendance_report/' . $xls_data);

                    $csvData = $this->generateCsvFiles('salary_list_report', $columnName, $salaryreport);
                    $this->data['csv_data'] = $csvData;
                    $this->data['xls_data'] = asset('storage/' . str_replace('public/', '', 'reports/attendance_report/' . $xls_data));
                }
            }
            // Late comming list report
            if ($reportType == "expensereport") {

                if ($request->has('user_id')) {
                    $attendanceData = AttendanceMaster::whereBetween('date', [$first_date, $second_date])->whereIN('user_id', $request->get('user_id'))->where('is_late', "YES")->with(['user' => function($query) {
                                    return $query->with('employee');
                                }])->get();
                } else {
                    $attendanceData = AttendanceMaster::whereBetween('date', [$first_date, $second_date])->where('is_late', "YES")->with(['user' => function($query) {
                                    return $query->with('employee');
                                }])->get();
                }
                //dd($attendanceData);
                $this->data['records'] = $attendanceData;

                $columnName = array('Sr. No', 'Employee Name', 'Employee ID', 'Date', 'First IN', 'Last OUT', 'Total Hours', 'Availability Status', 'Is Late', 'Late Time');

                if (!empty($attendanceData[0])) {

                    $csvData = $this->generateCsvFiles('expense_list_report', $columnName, $attendanceData);
                    $this->data['csv_data'] = $csvData;
                }
            }

            // On leave list report
            if ($reportType == "onleave") {

                if ($request->has('user_id')) {
                    $attendanceData = AttendanceMaster::whereBetween('date', [$first_date, $second_date])->whereIN('user_id', $request->get('user_id'))->where('availability_status', 3)->with(['user' => function($query) {
                                    return $query->with('employee');
                                }])->get();
                } else {
                    $attendanceData = AttendanceMaster::whereBetween('date', [$first_date, $second_date])->where('availability_status', 3)->with(['user' => function($query) {
                                    return $query->with('employee');
                                }])->get();
                }
                //dd($attendanceData);
                $this->data['records'] = $attendanceData;

                $columnName = array('Sr. No', 'Employee Name', 'Employee ID', 'Date', 'First IN', 'Last OUT', 'Total Hours', 'Availability Status', 'Is Late', 'Late Time');

                if (!empty($attendanceData[0])) {
                    $csvData = $this->generateCsvFiles('onleave_list_report', $columnName, $attendanceData);
                    $this->data['csv_data'] = $csvData;
                }
            }
        }

        return view('admin.finance_report.index', $this->data);
    }

    public function generateCsvFiles($filename, $columnName, $rptData) {

        $name = date('D-M-Y h:m:s') . ' ' . $filename . '.csv';

        $file = fopen(storage_path('app/public/reports/attendance_report/') . $name, 'wb');




        if ($filename == "salary_list_report" ||
                $filename == "latecomming_list_report" ||
                $filename == "onleave_list_report"
        ) {
            fputcsv($file, $columnName);
            $data = [];
            foreach ($rptData as $k => $rowData) {

                $data[] = array($k + 1,
                    $rowData->user->name,
                    $rowData->user->employee->emp_code,
                    $rowData->date,
                    $rowData->month,
                    $rowData->year,
                    $rowData->basic_salary,
                    $rowData->hra,
                    $rowData->others,
                    $rowData->working_day,
                    $rowData->employee_working_day,
                    $rowData->total_leave,
                    $rowData->unpaid_leave,
                    $rowData->unpaid_leave_amount,
                    $rowData->professional_tax,
                    $rowData->pf,
                    $rowData->loan_installment,
                    $rowData->penalty,
                    $rowData->manual_penalty,
                    $rowData->penalty_note,
                    $rowData->payable_salary
                );
            }
        }



        foreach ($data as $row) {
            fputcsv($file, $row);
        }
        return asset('storage/' . str_replace('public/', '', 'reports/attendance_report/' . $name));
    }

    public function payroll_excel_download() {
        // dd($request->all());
        $users = User::all();

        $result = (new FastExcel($users))->download('file.xlsx');
    }

}
