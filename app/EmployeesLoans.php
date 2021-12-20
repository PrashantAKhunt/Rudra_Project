<?php

namespace App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class EmployeesLoans extends Model
{
    protected $table="employee_loan";


    public static function getSalaryAmount(){
        $year = date('Y');
        $month = date('m');
        $salary = [];
       
        $attendanceMaster = AttendanceMaster::where('date', 'LIKE', $year . '-' . $month . '%')->where('user_id',Auth::user()->id)->get();
        $empSalary = EmployeesSalary::where('user_id', Auth::user()->id)->orderBy('id','DESC')->get()->first();                
        //print_r($empSalary); die();
        if (!empty($empSalary)) {
            $empLoan = EmployeesLoans::where('user_id', Auth::user()->id)->where('loan_status', 'Approved')->where('status', 'Enabled')->get()->first();
            if (!empty($empLoan)) {
                $emiStart = explode('-', $empLoan->loan_emi_start_from);

                $emiStartDate = $emiStart[1]."-".$emiStart[0]."-01";
                $salaryDate = $year."-".$month."-01";

                if ($emiStartDate <= $salaryDate) {
                    $salary['installment'] = round(($empLoan->loan_amount / $empLoan->loan_terms), 2);
                }
            } else {
                $salary['installment'] = 0;
            }

            $employeeWorkingDay = $unpaid_leave = $total_leave = $nonWorkingDay = 0;
            $lateDate = $moreLateDate = [];
            foreach ($attendanceMaster as $key => $value) {
                if ($value->availability_status == 1) { //present
                    $employeeWorkingDay++;
                } else if ($value->availability_status == 3) { //leave
                    $leaves = Leaves::find($value->availability_id);

                    if (!empty($leaves)) {
                        $total = 0;
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
                            $total += $days;
                        } else {
                            if ($leaves->start_day == 1) {
                                $total += 1;
                            } else if ($leaves->start_day == 2 || $leaves->start_day == 3) {
                                $total += 0.5;
                                $employeeWorkingDay += 0.5;
                            }
                        }                                
                        if ($leaves->leave_category_id == 4) { //un-paid leave
                            $unpaid_leave = $total;
                        }

                        $total_leave += $total;
                    }
                } else if ($value->availability_status == 6) { //mixed leave
                    $leavesDetail = Leaves::whereIn('id', explode(",", $value->availability_id))->get();
                    foreach ($leavesDetail as $key => $value) {
                        $total_leave += 0.5;
                    }
                } else if ($value->availability_status == 4 || $value->availability_status == 5) { //Holiday and Weekend
                    $nonWorkingDay++;
                }

                if ($value->is_late == 'YES') {
                    array_push($lateDate, $value->date);
                }
                if ($value->is_late_more == 'YES') {
                    array_push($moreLateDate, $value->date);
                }
            }
            
            $daysOfMonth = date('t');
            $salary['working_day'] = $daysOfMonth - $nonWorkingDay;
            $salary['employeeWorkingDay'] = $employeeWorkingDay;
            $salary['unpaid_leave'] = $unpaid_leave;
            $salary['total_leave'] = $total_leave;


            $perDayBasic = !empty($empSalary->basic_salary) ? round(($empSalary->basic_salary / $salary['working_day']), 2): 0;
            $salary['basic_salary'] = round(($perDayBasic * $salary['employeeWorkingDay']), 2);

            $perDayHra = !empty($empSalary->hra) ? round(($empSalary->hra / $salary['working_day']), 2): 0;
            $salary['hra'] = round(($perDayHra * $salary['employeeWorkingDay']), 2);

            $perDayOther = !empty($empSalary->other_allowance) ? round(($empSalary->other_allowance / $salary['working_day']), 2): 0;
            $salary['others'] = round(($perDayOther * $salary['employeeWorkingDay']), 2);
        
            $salary['pf'] = !empty($empSalary->PF_amount) ? round((($empSalary->PF_amount / $salary['working_day'])*$salary['employeeWorkingDay']), 2): 0;

            $salary['professional_tax'] = $empSalary->professional_tax;

            $perDaySalary = $perDayBasic + $perDayHra + $perDayOther;

            $salary['unpaid_leave_amount'] = round(($perDaySalary * $unpaid_leave), 2);

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
                    if(isset($weeks[date('W', strtotime($dateValue))]['more_late'])){
                        $weeks[date('W', strtotime($dateValue))]['more_late'] = $weeks[date('W', strtotime($dateValue))]['more_late'] + 1;
                    }else{
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

            $salary['penalty'] = round(($perDaySalary * $penaltyday), 2);
            
            $workingDyaSalary = $salary['basic_salary'] + $salary['hra'] + $salary['others'];

            $salary['payable_salary'] = ($workingDyaSalary - ($salary['installment'] + $salary['unpaid_leave_amount'] + $salary['penalty'] + $salary['professional_tax'] + $salary['pf']));
        }
        
        if (!empty($salary) && !empty($salary)) {
        	return $salary['payable_salary'];
        }else{
        	return NULL;
        }
    }
}
