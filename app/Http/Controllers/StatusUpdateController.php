<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;
use App\User;
use DateTime;
use App\Login_log;
use App\Leaves;
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
use App\CashApproval;
use App\BankPaymentApproval;
use App\OnlinePaymentApproval;
use App\Banks;
use App\Companies;
use App\Employee_expense;
use App\Driver_expense;
use App\Asset;

class StatusUpdateController extends Controller {

    private $common_task;
    private $notification_task;

    public function __construct() {
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();
    }

    public function cron_pending_leave_notify(Request $request) {
        $current_date = date('Y-m-d');
        //DB::enableQueryLog();
        //Send mail to HR
        $hr_pending_leave_list = Leaves::where('first_approval_status', 'Pending')
        ->whereDate('created_at','>=',date('Y-m-d',strtotime('2020-05-15')))
        ->get();
        if ($hr_pending_leave_list->count() > 0) {
            $table = "<table border='1px'>
                            <tr>
                                <th>User Name</th>
                                <th>Leave Subject</th>
                                <th>Leave Start Date</th>
                                <th>Leave End Date</th>
                                <th>Applied Date</th>
                            </tr>";
            foreach ($hr_pending_leave_list as $leave) {
                $leave_user = User::where('id', $leave->user_id)->get();
                $table .= "<tr>
                            <td>" . $leave_user[0]->name . "</td>
                            <td>" . $leave->subject . "</td>
                            <td>" . date('d-m-Y', strtotime($leave->start_date)) . "</td>
                            <td>" . date('d-m-Y', strtotime($leave->end_date)) . "</td>
                            <td>" . date('d-m-Y', strtotime($leave->created_at)) . "</td>
                            </tr>";
            }
            $table .= "</table>";
            $emailList = DB::table('users')->where('status', 'Enabled')->where('role', '=', config('constants.REAL_HR'))->pluck('email')->toArray();
            $mail_data = [
                'body' => $table,
                'email' => $emailList
            ];

            $this->common_task->hrpendingjobLeaveEmail($mail_data);
        }

        //Send mail to super admin
        $admin_pending_leave_list = Leaves::where('third_approval_status', 'Pending')->where('first_approval_status', 'Approved')->get();
        if ($admin_pending_leave_list->count() > 0) {
            $table = "<table border='1px'>
                            <tr>
                                <th>User Name</th>
                                <th>Leave Subject</th>
                                <th>Leave Start Date</th>
                                <th>Leave End Date</th>
                                <th>Applied Date</th>
                            </tr>";
            foreach ($admin_pending_leave_list as $leave) {
                $leave_user = User::where('id', $leave->user_id)->get();
                $table .= "<tr>
                            <td>" . $leave_user[0]->name . "</td>
                            <td>" . $leave->subject . "</td>
                            <td>" . date('d-m-Y', strtotime($leave->start_date)) . "</td>
                            <td>" . date('d-m-Y', strtotime($leave->end_date)) . "</td>
                            <td>" . date('d-m-Y', strtotime($leave->created_at)) . "</td>
                            </tr>";
            }
            $table .= "</table>";
            $emailList = DB::table('users')->where('status', 'Enabled')->where('role', '=', config('constants.SuperUser'))->pluck('email')->toArray();
            $mail_data = [
                'body' => $table,
                'email' => $emailList
            ];

            $this->common_task->adminpendingjobLeaveEmail($mail_data);
        }
    }

    public function cron_pending_relieving_leave_notify(Request $request) {
        $current_date = date('Y-m-d');
        //DB::enableQueryLog();

        $userList = User::where('status', 'Enabled')->get();

        if ($userList->count() > 0) {
            foreach ($userList as $userKey => $userValue) {
                $relieving_pending_list = Leaves::where('assign_work_status', 'Pending')->where('start_date','>',date('2019-12-31'))->where('assign_work_user_id', $userValue->id)->get();

                if ($relieving_pending_list->count() > 0) {
                    $table = "<table border='1px'>
                            <tr>
                                <th>User Name</th>
                                <th>Leave Subject</th>
                                <th>Leave Start Date</th>
                                <th>Leave End Date</th>
                                <th>Applied Date</th>
                            </tr>";
                    foreach ($relieving_pending_list as $leave) {
                        $leave_user = User::where('id', $leave->user_id)->get();
                        $table .= "<tr>
                                    <td>" . $leave_user[0]->name . "</td>
                                    <td>" . $leave->subject . "</td>
                                    <td>" . date('d-m-Y', strtotime($leave->start_date)) . "</td>
                                    <td>" . date('d-m-Y', strtotime($leave->end_date)) . "</td>
                                    <td>" . date('d-m-Y', strtotime($leave->created_at)) . "</td>
                                    </tr>";
                    }

                    $table .= "</table>";
                    $mail_data = [
                        'body' => $table,
                        'email' => $leave_user[0]->email
                    ];

                    $this->common_task->relievingpendingjobLeaveEmail($mail_data);
                }
            }
        }
    }

    public function cron_cash_payment_status_notify() {
        //Send mail to Account
        $account_cash_pending_payment_list = CashApproval::where('first_approval_status', 'Pending')->get();
        if ($account_cash_pending_payment_list->count() > 0) {
            $table = "<table border='1px'>
                            <tr>
                                <th>User Name</th>
                                <th>Amount</th>
                                <th>Company</th>
                                <th>Created At</th>
                            </tr>";
            foreach ($account_cash_pending_payment_list as $payment) {
                $leave_user = User::where('id', $payment->user_id)->get();
                $first_approval_company = Companies::where('id', $payment->company_id)->get();
                $table .= "<tr>
                            <td>" . $leave_user[0]->name . "</td>
                            <td>" . $payment->amount . "</td>
                            <td>" . $first_approval_company[0]->company_name . "</td>
                            <td>" . $payment->created_at . "</td>
                            </tr>";
            }
            $table .= "</table>";
            $emailList = DB::table('users')->where('status', 'Enabled')->where('role', '=', config('constants.ACCOUNT_ROLE'))->pluck('email')->toArray();
            $mail_data = [
                'body' => $table,
                'email' => $emailList
            ];
            
            //echo $table;
            $this->common_task->cashPaymentPendingJobEmail($mail_data);
        }

        //Send mail to Account
        $account_second_cash_pending_leave_list = CashApproval::where('second_approval_status', 'Pending')
                ->where('first_approval_status', 'Approved')
                ->get();
        if ($account_second_cash_pending_leave_list->count() > 0) {
            $table = "<table border='1px'>
                            <tr>
                                <th>User Name</th>
                                <th>Amount</th>
                                <th>Company</th>
                                <th>Created At</th>
                            </tr>";
            foreach ($account_second_cash_pending_leave_list as $second_payment) {
                $second_approval_user = User::where('id', $second_payment->user_id)->get();
                $second_approval_company = Companies::where('id', $second_payment->company_id)->get();
                $table .= "<tr>
                            <td>" . $second_approval_user[0]->name . "</td>
                            <td>" . $second_payment->amount . "</td>
                            <td>" . $second_approval_company[0]->company_name . "</td>
                            <td>" . $second_payment->created_at . "</td>
                            </tr>";
            }
            $table .= "</table>";
            $emailList = DB::table('users')->where('status', 'Enabled')->where('role', '=', config('constants.Admin'))->pluck('email')->toArray();
            $mail_data = [
                'body' => $table,
                'email' => $emailList
            ];
            
            //echo $table;
            $this->common_task->cashPaymentPendingJobEmail($mail_data);
        }

        //Send mail to Super Admin
        $account_third_cash_pending_leave_list = CashApproval::where('first_approval_status', 'Approved')
                ->where('second_approval_status', 'Approved')
                ->where('third_approval_status', 'Pending')->get();
        if ($account_third_cash_pending_leave_list->count() > 0) {
            $table = "<table border='1px'>
                            <tr>
                                <th>User Name</th>
                                <th>Amount</th>
                                <th>Company</th>
                                <th>Created At</th>
                            </tr>";
            foreach ($account_third_cash_pending_leave_list as $third_payment) {
                $third_approval_user = User::where('id', $third_payment->user_id)->get();
                $third_approval_company = Companies::where('id', $third_payment->company_id)->get();
                $table .= "<tr>
                            <td>" . $third_approval_user[0]->name . "</td>
                            <td>" . $third_payment->amount . "</td>
                            <td>" . $third_approval_company[0]->company_name . "</td>
                            <td>" . $third_payment->created_at . "</td>
                            </tr>";
            }
            $table .= "</table>";
            $emailList = DB::table('users')->where('status', 'Enabled')->where('role', '=', config('constants.SuperUser'))->pluck('email')->toArray();
            $mail_data = [
                'body' => $table,
                'email' => $emailList
            ];
            
            //echo $table;
            $this->common_task->cashPaymentPendingJobEmail($mail_data);
        }
    }

    public function cron_bank_payment_status_notify() {
        //Send mail to Account
        $account_bank_pending_payment_list = BankPaymentApproval::where('first_approval_status', 'Pending')->get();
        if ($account_bank_pending_payment_list->count() > 0) {
            $table = "<table border='1px'>
                            <tr>
                                <th>User Name</th>
                                <th>Amount</th>
                                <th>Company</th>
                                <th>Bank Name</th>
                                <th>Cheque Number</th>
                                <th>Created At</th>
                            </tr>";
            foreach ($account_bank_pending_payment_list as $payment) {
                $leave_user = User::where('id', $payment->user_id)->get();
                $first_approval_company = Companies::where('id', $payment->company_id)->get();
                $first_approval_bank = Banks::where('id', $payment->bank_id)->get();
                $table .= "<tr>
                            <td>" . $leave_user[0]->name . "</td>
                            <td>" . $payment->amount . "</td>
                            <td>" . $first_approval_company[0]->company_name . "</td>
                            <td>" . $first_approval_bank[0]->bank_name . "</td>
                            <td>" . $payment->cheque_number . "</td>
                            <td>" . $payment->created_at . "</td>
                            </tr>";
            }
            $table .= "</table>";
            $emailList = DB::table('users')->where('status', 'Enabled')->where('role', '=', config('constants.ACCOUNT_ROLE'))->pluck('email')->toArray();
            $mail_data = [
                'body' => $table,
                'email' => $emailList
            ];
            
            //echo $table;
            $this->common_task->bankPaymentPendingJobEmail($mail_data);
        }

        //Send mail to Account
        $account_second_cash_pending_leave_list = BankPaymentApproval::where('second_approval_status', 'Pending')
                ->where('first_approval_status', 'Approved')
                ->get();
        if ($account_second_cash_pending_leave_list->count() > 0) {
            $table = "<table border='1px'>
                            <tr>
                                <th>User Name</th>
                                <th>Amount</th>
                                <th>Company</th>
                                <th>Bank Name</th>
                                <th>Cheque Number</th>
                                <th>Created At</th>
                            </tr>";
            foreach ($account_second_cash_pending_leave_list as $second_payment) {
                $second_approval_user = User::where('id', $second_payment->user_id)->get();
                $second_approval_company = Companies::where('id', $second_payment->company_id)->get();
                $second_approval_bank = Banks::where('id', $second_payment->bank_id)->get();

                $table .= "<tr>
                            <td>" . $second_approval_user[0]->name . "</td>
                            <td>" . $second_payment->amount . "</td>
                            <td>" . $second_approval_company[0]->company_name . "</td>
                            <td>" . $second_approval_bank[0]->bank_name . "</td>
                            <td>" . $second_payment->cheque_number . "</td>
                            <td>" . $second_payment->created_at . "</td>
                            </tr>";
            }
            $table .= "</table>";
            $emailList = DB::table('users')->where('status', 'Enabled')->where('role', '=', config('constants.Admin'))->pluck('email')->toArray();
            $mail_data = [
                'body' => $table,
                'email' => $emailList
            ];
            
            //echo $table;
            $this->common_task->bankPaymentPendingJobEmail($mail_data);
        }

        //Send mail to Super Admin
        $account_third_bank_pending_leave_list = BankPaymentApproval::where('third_approval_status', 'Pending')
                ->where('first_approval_status', 'Approved')
                ->where('second_approval_status', 'Approved')
                ->get();
        if ($account_third_bank_pending_leave_list->count() > 0) {
            $table = "<table border='1px'>
                            <tr>
                                <th>User Name</th>
                                <th>Amount</th>
                                <th>Company</th>
                                <th>Bank Name</th>
                                <th>Cheque Number</th>
                                <th>Created At</th>
                            </tr>";
            foreach ($account_third_bank_pending_leave_list as $third_payment) {
                $third_approval_user = User::where('id', $third_payment->user_id)->get();
                $third_approval_company = Companies::where('id', $third_payment->company_id)->get();
                $third_approval_bank = Banks::where('id', $third_payment->bank_id)->get();

                $table .= "<tr>
                            <td>" . $third_approval_user[0]->name . "</td>
                            <td>" . $third_payment->amount . "</td>
                            <td>" . $third_approval_company[0]->company_name . "</td>
                            <td>" . $third_approval_bank[0]->bank_name . "</td>
                            <td>" . $third_payment->cheque_number . "</td>
                            <td>" . $third_payment->created_at . "</td>
                            </tr>";
            }
            $table .= "</table>";
            $emailList = DB::table('users')->where('status', 'Enabled')->where('role', '=', config('constants.SuperUser'))->pluck('email')->toArray();
            $mail_data = [
                'body' => $table,
                'email' => $emailList
            ];
            
            //echo $table;
            $this->common_task->bankPaymentPendingJobEmail($mail_data);
        }
    }

    public function cron_online_payment_status_notify() {
        //Send mail to Account
        $account_account_pending_payment_list = OnlinePaymentApproval::where('first_approval_status', 'Pending')->get();
        if ($account_account_pending_payment_list->count() > 0) {
            $table = "<table border='1px'>
                            <tr>
                                <th>User Name</th>
                                <th>Amount</th>
                                <th>Company</th>
                                <th>Bank Name</th>
                                <th>Created At</th>
                            </tr>";
            foreach ($account_account_pending_payment_list as $payment) {
                $leave_user = User::where('id', $payment->user_id)->get();
                $first_approval_company = Companies::where('id', $payment->company_id)->get();
                $first_approval_bank = Banks::where('id', $payment->bank_id)->get();
                $table .= "<tr>
                            <td>" . $leave_user[0]->name . "</td>
                            <td>" . $payment->amount . "</td>
                            <td>" . $first_approval_company[0]->company_name . "</td>
                            <td>" . $first_approval_bank[0]->bank_name . "</td>
                            <td>" . $payment->created_at . "</td>
                            </tr>";
            }
            $table .= "</table>";
            $emailList = DB::table('users')->where('status', 'Enabled')->where('role', '=', config('constants.ACCOUNT_ROLE'))->pluck('email')->toArray();
            $mail_data = [
                'body' => $table,
                'email' => $emailList
            ];
            
            //echo $table;
            $this->common_task->onlinePaymentPendingJobEmail($mail_data);
        }

        //Send mail to Account
        $account_second_online_pending_leave_list = OnlinePaymentApproval::where('second_approval_status', 'Pending')
                ->where('first_approval_status', 'Approved')
                ->get();
        if ($account_second_online_pending_leave_list->count() > 0) {
            $table = "<table border='1px'>
                            <tr>
                                <th>User Name</th>
                                <th>Amount</th>
                                <th>Company</th>
                                <th>Bank Name</th>
                                <th>Created At</th>
                            </tr>";
            foreach ($account_second_online_pending_leave_list as $second_payment) {
                $second_approval_user = User::where('id', $second_payment->user_id)->get();
                $second_approval_company = Companies::where('id', $second_payment->company_id)->get();
                $second_approval_bank = Banks::where('id', $second_payment->bank_id)->get();

                $table .= "<tr>
                            <td>" . $second_approval_user[0]->name . "</td>
                            <td>" . $second_payment->amount . "</td>
                            <td>" . $second_approval_company[0]->company_name . "</td>
                            <td>" . $second_approval_bank[0]->bank_name . "</td>
                            <td>" . $second_payment->created_at . "</td>
                            </tr>";
            }
            $table .= "</table>";
            $emailList = DB::table('users')->where('status', 'Enabled')->where('role', '=', config('constants.Admin'))->pluck('email')->toArray();
            $mail_data = [
                'body' => $table,
                'email' => $emailList
            ];
            
            //echo $table;
            $this->common_task->onlinePaymentPendingJobEmail($mail_data);
        }

        //Send mail to Super Admin
        $account_third_bank_pending_leave_list = OnlinePaymentApproval::where('third_approval_status', 'Pending')
                ->where('first_approval_status', 'Approved')
                ->where('second_approval_status', 'Approved')
                ->get();
        if ($account_third_bank_pending_leave_list->count() > 0) {
            $table = "<table border='1px'>
                            <tr>
                                <th>User Name</th>
                                <th>Amount</th>
                                <th>Company</th>
                                <th>Bank Name</th>
                                <th>Created At</th>
                            </tr>";
            foreach ($account_third_bank_pending_leave_list as $third_payment) {
                $third_approval_user = User::where('id', $third_payment->user_id)->get();
                $third_approval_company = Companies::where('id', $third_payment->company_id)->get();
                $third_approval_bank = Banks::where('id', $third_payment->bank_id)->get();

                $table .= "<tr>
                            <td>" . $third_approval_user[0]->name . "</td>
                            <td>" . $third_payment->amount . "</td>
                            <td>" . $third_approval_company[0]->company_name . "</td>
                            <td>" . $third_approval_bank[0]->bank_name . "</td>
                            <td>" . $third_payment->created_at . "</td>
                            </tr>";
            }
            $table .= "</table>";
            $emailList = DB::table('users')->where('status', 'Enabled')->where('role', '=', config('constants.SuperUser'))->pluck('email')->toArray();
            $mail_data = [
                'body' => $table,
                'email' => $emailList
            ];
            
            //echo $table;
            $this->common_task->onlinePaymentPendingJobEmail($mail_data);
        }
    }

    public function cron_employee_expense_status_notify() {
        //Send mail to Account
        $expense_emp_pending_payment_list = Employee_expense::where('first_approval_status', 'Pending')->get();
        if ($expense_emp_pending_payment_list->count() > 0) {
            $table = "<table border='1px'>
                            <tr>
                                <th>User Name</th>
                                <th>Amount</th>
                                <th>Company</th>
                                <th>Expense Date</th>
                            </tr>";
            foreach ($expense_emp_pending_payment_list as $Expense_payment) {
                $leave_user = User::where('id', $Expense_payment->user_id)->get();
                $first_approval_company = Companies::where('id', $Expense_payment->company_id)->get();

                $table .= "<tr>
                            <td>" . $leave_user[0]->name . "</td>
                            <td>" . $Expense_payment->amount . "</td>
                            <td>" . $first_approval_company[0]->company_name . "</td>
                            <td>" . $Expense_payment->expense_date . "</td>
                            </tr>";
            }
            $table .= "</table>";

            $emailList = DB::table('users')->where('status', 'Enabled')->where('role', '=', config('constants.REAL_HR'))->pluck('email')->toArray();
            $mail_data = [
                'body' => $table,
                'email' => $emailList
            ];
            
            $this->common_task->empExpensePendingJobEmail($mail_data);
        }

        //Second approval
        $expense_emp_second_pending_payment_list = Employee_expense::where('second_approval_status', 'Pending')
                ->where('first_approval_status', 'Approved')
                ->get();
        if ($expense_emp_second_pending_payment_list->count() > 0) {
            $table = "<table border='1px'>
                            <tr>
                                <th>User Name</th>
                                <th>Amount</th>
                                <th>Company</th>
                                <th>Expense Date</th>
                            </tr>";
            foreach ($expense_emp_second_pending_payment_list as $Expense_payment) {
                $leave_user = User::where('id', $Expense_payment->user_id)->get();
                $first_approval_company = Companies::where('id', $Expense_payment->company_id)->get();

                $table .= "<tr>
                            <td>" . $leave_user[0]->name . "</td>
                            <td>" . $Expense_payment->amount . "</td>
                            <td>" . $first_approval_company[0]->company_name . "</td>
                            <td>" . $Expense_payment->expense_date . "</td>
                            </tr>";
            }
            $table .= "</table>";

            $emailList = DB::table('users')->where('status', 'Enabled')->where('role', '=', config('constants.ASSISTANT'))->pluck('email')->toArray();
            $mail_data = [
                'body' => $table,
                'email' => $emailList
            ];
            
            //echo $table;
            $this->common_task->empExpensePendingJobEmail($mail_data);
        }

        //Third approval
        $expense_emp_third_pending_payment_list = Employee_expense::where('third_approval_status', 'Pending')
                ->where('first_approval_status', 'Approved')
                ->where('second_approval_status', 'Approved')
                ->get();
        if ($expense_emp_third_pending_payment_list->count() > 0) {
            $table = "<table border='1px'>
                            <tr>
                                <th>User Name</th>
                                <th>Amount</th>
                                <th>Company</th>
                                <th>Expense Date</th>
                            </tr>";
            foreach ($expense_emp_third_pending_payment_list as $Expense_payment) {
                $leave_user = User::where('id', $Expense_payment->user_id)->get();
                $first_approval_company = Companies::where('id', $Expense_payment->company_id)->get();

                $table .= "<tr>
                            <td>" . $leave_user[0]->name . "</td>
                            <td>" . $Expense_payment->amount . "</td>
                            <td>" . $first_approval_company[0]->company_name . "</td>
                            <td>" . $Expense_payment->expense_date . "</td>
                            </tr>";
            }
            $table .= "</table>";

            $emailList = DB::table('users')->where('status', 'Enabled')->where('role', '=', config('constants.Admin'))->pluck('email')->toArray();
            $mail_data = [
                'body' => $table,
                'email' => $emailList
            ];
            
            //echo $table;
            $this->common_task->empExpensePendingJobEmail($mail_data);
        }

        //Fourth approval
        $expense_emp_fourth_pending_payment_list = Employee_expense::where('forth_approval_status', 'Pending')
                ->where('first_approval_status', 'Approved')
                ->where('second_approval_status', 'Approved')
                ->where('third_approval_status', 'Approved')
                ->get();
        if ($expense_emp_fourth_pending_payment_list->count() > 0) {
            $table = "<table border='1px'>
                            <tr>
                                <th>User Name</th>
                                <th>Amount</th>
                                <th>Company</th>
                                <th>Expense Date</th>
                            </tr>";
            foreach ($expense_emp_fourth_pending_payment_list as $Expense_payment) {
                $leave_user = User::where('id', $Expense_payment->user_id)->get();
                $first_approval_company = Companies::where('id', $Expense_payment->company_id)->get();

                $table .= "<tr>
                            <td>" . $leave_user[0]->name . "</td>
                            <td>" . $Expense_payment->amount . "</td>
                            <td>" . $first_approval_company[0]->company_name . "</td>
                            <td>" . $Expense_payment->expense_date . "</td>
                            </tr>";
            }
            $table .= "</table>";

            $emailList = DB::table('users')->where('status', 'Enabled')->where('role', '=', config('constants.SuperUser'))->pluck('email')->toArray();
            $mail_data = [
                'body' => $table,
                'email' => $emailList
            ];
            
            //echo $table;
            $this->common_task->empExpensePendingJobEmail($mail_data);
        }

        //Five approval
        $expense_emp_five_pending_payment_list = Employee_expense::where('fifth_approval_status', 'Pending')
                ->where('first_approval_status', 'Approved')
                ->where('second_approval_status', 'Approved')
                ->where('third_approval_status', 'Approved')
                ->where('forth_approval_status', 'Approved')
                ->get();
        if ($expense_emp_five_pending_payment_list->count() > 0) {
            $table = "<table border='1px'>
                            <tr>
                                <th>User Name</th>
                                <th>Amount</th>
                                <th>Company</th>
                                <th>Expense Date</th>
                            </tr>";
            foreach ($expense_emp_five_pending_payment_list as $Expense_payment) {
                $leave_user = User::where('id', $Expense_payment->user_id)->get();
                $first_approval_company = Companies::where('id', $Expense_payment->company_id)->get();

                $table .= "<tr>
                            <td>" . $leave_user[0]->name . "</td>
                            <td>" . $Expense_payment->amount . "</td>
                            <td>" . $first_approval_company[0]->company_name . "</td>
                            <td>" . $Expense_payment->expense_date . "</td>
                            </tr>";
            }
            $table .= "</table>";

            $emailList = DB::table('users')->where('status', 'Enabled')->where('role', '=', config('constants.ACCOUNT_ROLE'))->pluck('email')->toArray();
            $mail_data = [
                'body' => $table,
                'email' => $emailList
            ];
            
            //echo $table;
            $this->common_task->empExpensePendingJobEmail($mail_data);
        }
    }

    public function cron_pending_driver_expense() {
        //Send mail to Account
        $expense_emp_driver_payment_list = Driver_expense::where('first_approval_status', 'Pending')->get();
        if ($expense_emp_driver_payment_list->count() > 0) {
            $table = "<table border='1px'>
                            <tr>
                                <th>User Name</th>
                                <th>Amount</th>
                                <th>Asset Name</th>
                                <th>Vehicle Type</th>
                                <th>Expense Time</th>
                                <th>Expense Date</th>
                            </tr>";
            foreach ($expense_emp_driver_payment_list as $Expense_payment) {

                $leave_user = User::where('id', $Expense_payment->user_id)->get();
                $first_asset_approval = Asset::where('id', $Expense_payment->asset_id)->get();
                $table .= "<tr>
                            <td>" . $leave_user[0]->name . "</td>
                            <td>" . $Expense_payment->amount . "</td>
                            <td>" . $first_asset_approval[0]->name . "</td>
                            <td>" . $Expense_payment->vehicle_type . "</td>
                            <td>" . $Expense_payment->time_of_expense . "</td>
                            <td>" . $Expense_payment->date_of_expense . "</td>
                            </tr>";
            }
            $table .= "</table>";

            $emailList = DB::table('users')->where('status', 'Enabled')->where('role', '=', config('constants.ACCOUNT_ROLE'))->pluck('email')->toArray();
            $mail_data = [
                'body' => $table,
                'email' => $emailList
            ];
            
            $this->common_task->driverExpensePendingJobEmail($mail_data);
        }


        //Send Second mail to Account
        $expense_emp_driver_payment_list = Driver_expense::where('second_approval_status', 'Pending')
                ->where('first_approval_status', 'Approved')
                ->get();
        if ($expense_emp_driver_payment_list->count() > 0) {
            $table = "<table border='1px'>
                            <tr>
                                <th>User Name</th>
                                <th>Amount</th>
                                <th>Asset Name</th>
                                <th>Vehicle Type</th>
                                <th>Expense Time</th>
                                <th>Expense Date</th>
                            </tr>";
            foreach ($expense_emp_driver_payment_list as $Expense_payment) {

                $leave_user = User::where('id', $Expense_payment->user_id)->get();
                $first_asset_approval = Asset::where('id', $Expense_payment->asset_id)->get();
                $table .= "<tr>
                            <td>" . $leave_user[0]->name . "</td>
                            <td>" . $Expense_payment->amount . "</td>
                            <td>" . $first_asset_approval[0]->name . "</td>
                            <td>" . $Expense_payment->vehicle_type . "</td>
                            <td>" . $Expense_payment->time_of_expense . "</td>
                            <td>" . $Expense_payment->date_of_expense . "</td>
                            </tr>";
            }
            $table .= "</table>";

            $emailList = DB::table('users')->where('status', 'Enabled')->where('role', '=', config('constants.Admin'))->pluck('email')->toArray();
            $mail_data = [
                'body' => $table,
                'email' => $emailList
            ];
            
            $this->common_task->driverExpensePendingJobEmail($mail_data);
        }



        //Send mail to Account
        $expense_emp_driver_payment_list = Driver_expense::where('third_approval_status', 'Pending')
                ->where('first_approval_status', 'Approved')
                ->where('second_approval_status', 'Approved')
                ->get();
        if ($expense_emp_driver_payment_list->count() > 0) {
            $table = "<table border='1px'>
                            <tr>
                                <th>User Name</th>
                                <th>Amount</th>
                                <th>Asset Name</th>
                                <th>Vehicle Type</th>
                                <th>Expense Time</th>
                                <th>Expense Date</th>
                            </tr>";
            foreach ($expense_emp_driver_payment_list as $Expense_payment) {

                $leave_user = User::where('id', $Expense_payment->user_id)->get();
                $first_asset_approval = Asset::where('id', $Expense_payment->asset_id)->get();
                $table .= "<tr>
                            <td>" . $leave_user[0]->name . "</td>
                            <td>" . $Expense_payment->amount . "</td>
                            <td>" . $first_asset_approval[0]->name . "</td>
                            <td>" . $Expense_payment->vehicle_type . "</td>
                            <td>" . $Expense_payment->time_of_expense . "</td>
                            <td>" . $Expense_payment->date_of_expense . "</td>
                            </tr>";
            }
            $table .= "</table>";

            $emailList = DB::table('users')->where('status', 'Enabled')->where('role', '=', config('constants.SuperUser'))->pluck('email')->toArray();
            $mail_data = [
                'body' => $table,
                'email' => $emailList
            ];
            
            $this->common_task->driverExpensePendingJobEmail($mail_data);
        }
    }

    /* public function cron_pending_attendance()
      {
      //Send mail to Account
      $pending_emp_pending_attendance_list = AttendanceDetail::where('is_approved', 'Pending')->get();
      if ($pending_emp_pending_attendance_list->count() > 0) {
      $table = "<table border='1px'>
      <tr>
      <th>User Name</th>
      <th>Amount</th>
      <th>Company</th>
      <th>Expense Date</th>
      </tr>";
      foreach ($pending_emp_pending_attendance_list as $attendance) {
      $leave_user = User::where('id', $attendance->user_id)->get();
      $first_approval_company = Companies::where('id', $attendance->company_id)->get();

      $table.= "<tr>
      <td>".$leave_user[0]->name."</td>
      <td>".$attendance->amount."</td>
      <td>".$first_approval_company[0]->company_name."</td>
      <td>".$attendance->expense_date."</td>
      </tr>";
      }
      $table.="</table>";

      $emailList = DB::table('users')->where('status', 'Enabled')->where('role', '=', config('constants.REAL_HR'))->pluck('email')->toArray();
      $mail_data = [
      'body'=>$table,
      'email'=>$emailList
      ];
      echo $table;
      die();
      //$this->common_task->empExpensePendingJobEmail($mail_data);
      }
      } */

    public function cron_pending_attendance() {
        //Send mail to Account
        $pending_emp_pending_attendance_list = AttendanceDetail::where('is_approved', 'Pending')->get();
        if ($pending_emp_pending_attendance_list->count() > 0) {
            $table = "<table border='1px'>
                            <tr>
                                <th>User Name</th>
                                <th>Punch Type</th>
                                <th>Device Type</th>
                                <th>Time</th>
                            </tr>";
            foreach ($pending_emp_pending_attendance_list as $attendance) {
                $leave_user = AttendanceMaster::where('id', $attendance->attendance_master_id)->get();
                $leave_user_data = User::where('id', $leave_user[0]->user_id)->get();

                $table .= "<tr>
                            <td>" . $leave_user_data[0]->name . "</td>
                            <td>" . $attendance->punch_type . "</td>
                            <td>" . $attendance->device_type . "</td>
                            <td>" . $attendance->time . "</td>
                            </tr>";
            }
            $table .= "</table>";

            $emailList = DB::table('users')->where('status', 'Enabled')->where('role', '=', config('constants.REAL_HR'))->pluck('email')->toArray();
            $hr_detail=DB::table('users')->where('status', 'Enabled')->where('role', '=', config('constants.REAL_HR'))->get();
            $mail_data = [
                'body' => $table,
                'email' => $emailList,
                'username'=>$hr_detail[0]->name
            ];
            /* print_r($table);
              die(); */
            $this->common_task->empAttendancePendingEmail($mail_data);
        }
    }
    
    public function update_letterhead_numbers(){
        /*$ref_no="RT I LAB SOLUTIONS LLP/05-02-2020";
        $company_id=12;
        
        $letterhead_list= \App\LetterHeadRegister::where('company_id','!=',$company_id)
                ->where('letter_head_ref_no',$ref_no)
                ->where('is_used','used')
                ->get();
        
        foreach($letterhead_list as $letterhead){
            $original_company=$letterhead->company_id;
            $original_letter_number=$letterhead->letter_head_number;
            $update_array_original_company=[
                'user_id'=>$letterhead->user_id,
                'ref_id'=>$letterhead->ref_id,
                'issue_date'=>$letterhead->issue_date,
                'party_detail'=>$letterhead->party_detail,
                'project_id'=>$letterhead->project_id,
                'other_project_detail'=>$letterhead->other_project_detail,
                'title'=>$letterhead->title,
                'letter_head_content'=>$letterhead->letter_head_content,
                'work_detail'=>$letterhead->work_detail,
                'is_used'=>'used',
                'use_type'=>$letterhead->use_type,
                'updated_at'=>$letterhead->updated_at,
                
                
            ];
            
            //update wrong entry
            \App\LetterHeadRegister::where('id',$letterhead->id)->update(['company_id'=>$company_id,'is_used'=>'not_used']);
            
            //update original company
            $original_update_data=\App\LetterHeadRegister::where('company_id',$original_company)
                    ->where('letter_head_number',$original_letter_number)
                    ->get();
            \App\LetterHeadRegister::where('id',$original_update_data[0]->id)
                    ->update($update_array_original_company);
            if($letterhead->use_type=='pro_sign_letter'){
                $get_use_table_update= \App\ProSignLetter::where('id',$letterhead->ref_id)->get();
                if($get_use_table_update->count()>0){
                    $letter_head_number_arr= explode(',', $get_use_table_update[0]->letter_head_number);
                    foreach($letter_head_number_arr as $key=>$num){
                        if($num==$letterhead->id){
                            $letter_head_number_arr[$key]=$original_update_data[0]->id;
                        }
                    }
                    $num_str= implode(',', $letter_head_number_arr);
                    \App\ProSignLetter::where('id',$letterhead->ref_id)->update(['letter_head_number'=>$num_str]);
                }
            }
            else{
                $get_use_table_update= \App\PreSignLetter::where('id',$letterhead->ref_id)->get();
                if($get_use_table_update->count()>0){
                    $letter_head_number_arr= explode(',', $get_use_table_update[0]->letter_head_number);
                    foreach($letter_head_number_arr as $key=>$num){
                        if($num==$letterhead->id){
                            $letter_head_number_arr[$key]=$original_update_data[0]->id;
                        }
                    }
                    $num_str= implode(',', $letter_head_number_arr);
                    \App\PreSignLetter::where('id',$letterhead->ref_id)->update(['letter_head_number'=>$num_str]);
                }
            }
            
            
        }*/
        
        
    }

}
