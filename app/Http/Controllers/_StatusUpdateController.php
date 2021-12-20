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
        $hr_pending_leave_list = Leaves::where('first_approval_status', 'Pending')->get();
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
                $table.= "<tr>
                            <td>".$leave_user[0]->name."</td>
                            <td>".$leave->subject."</td>
                            <td>".date('d-m-Y', strtotime($leave->start_date))."</td>
                            <td>".date('d-m-Y', strtotime($leave->end_date))."</td>
                            <td>".date('d-m-Y', strtotime($leave->created_at))."</td>
                            </tr>";
            }
            $table.="</table>";
            $emailList = DB::table('users')->where('status', 'Enabled')->where('role', '=', config('constants.REAL_HR'))->pluck('email')->toArray();
            $mail_data = [
                    'body'=>$table,
                    'email'=>$emailList
                ];
            $this->common_task->hrpendingjobLeaveEmail($mail_data);
        }

        //Send mail to admin
        $admin_pending_leave_list = Leaves::where('second_approval_status', 'Pending')->get();
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
                $table.= "<tr>
                            <td>".$leave_user[0]->name."</td>
                            <td>".$leave->subject."</td>
                            <td>".date('d-m-Y', strtotime($leave->start_date))."</td>
                            <td>".date('d-m-Y', strtotime($leave->end_date))."</td>
                            <td>".date('d-m-Y', strtotime($leave->created_at))."</td>
                            </tr>";
            }
            $table.="</table>";
            $emailList = DB::table('users')->where('status', 'Enabled')->where('role', '=', config('constants.Admin'))->pluck('email')->toArray();
            $mail_data = [
                    'body'=>$table,
                    'email'=>$emailList
                ];
            $this->common_task->adminpendingjobLeaveEmail($mail_data);
        }
    }

    public function cron_pending_relieving_leave_notify(Request $request) {
        $current_date = date('Y-m-d');
        //DB::enableQueryLog();
        
        $userList = User::where('status', 'Enabled')->get();
        
        if ($userList->count() > 0) {
            foreach ($userList as $userKey => $userValue)
            {
                $relieving_pending_list = Leaves::where('assign_work_status', 'Pending')->where('assign_work_user_id',$userValue->id)->get();
                
                if ($relieving_pending_list->count() > 0) 
                {
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
                        $table.= "<tr>
                                    <td>".$leave_user[0]->name."</td>
                                    <td>".$leave->subject."</td>
                                    <td>".date('d-m-Y', strtotime($leave->start_date))."</td>
                                    <td>".date('d-m-Y', strtotime($leave->end_date))."</td>
                                    <td>".date('d-m-Y', strtotime($leave->created_at))."</td>
                                    </tr>";
                    }

                    $table.="</table>";
                    $mail_data = [
                            'body'=>$table,
                            'email'=>$leave_user[0]->email
                    ];
                    $this->common_task->relievingpendingjobLeaveEmail($mail_data);
                }
            }
        }
    }

    public function cron_cash_payment_status_notify()
    {
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
                $table.= "<tr>
                            <td>".$leave_user[0]->name."</td>
                            <td>".$payment->amount."</td>
                            <td>".$first_approval_company[0]->company_name."</td>
                            <td>".$payment->created_at."</td>
                            </tr>";
            }
            $table.="</table>";
            $emailList = DB::table('users')->where('status', 'Enabled')->where('role', '=', config('constants.ACCOUNT_ROLE'))->pluck('email')->toArray();
            $mail_data = [
                    'body'=>$table,
                    'email'=>$emailList
                ];
            //echo $table;
            $this->common_task->cashPaymentPendingJobEmail($mail_data);
        }

        //Send mail to Account
        $account_second_cash_pending_leave_list = CashApproval::where('second_approval_status', 'Pending')->get();
        if ($account_second_cash_pending_leave_list->count() > 0) {
            $table = "<table border='1px'>
                            <tr>
                                <th>User Name</th>
                                <th>Amount</th>
                                <th>Company</th>
                                <th>Created At</th>
                            </tr>";
            foreach ($account_second_cash_pending_leave_list as $second_payment) {
                $second_approval_user    = User::where('id', $second_payment->user_id)->get();
                $second_approval_company = Companies::where('id', $second_payment->company_id)->get();
                $table.= "<tr>
                            <td>".$second_approval_user[0]->name."</td>
                            <td>".$second_payment->amount."</td>
                            <td>".$second_approval_company[0]->company_name."</td>
                            <td>".$second_payment->created_at."</td>
                            </tr>";
            }
            $table.="</table>";
            $emailList = DB::table('users')->where('status', 'Enabled')->where('role', '=', config('constants.Admin'))->pluck('email')->toArray();
            $mail_data = [
                    'body'=>$table,
                    'email'=>$emailList
                ];
            //echo $table;
            $this->common_task->cashPaymentPendingJobEmail($mail_data);
        }

        //Send mail to Super Admin
        $account_third_cash_pending_leave_list = CashApproval::where('third_approval_status', 'Pending')->get();
        if ($account_third_cash_pending_leave_list->count() > 0) {
            $table = "<table border='1px'>
                            <tr>
                                <th>User Name</th>
                                <th>Amount</th>
                                <th>Company</th>
                                <th>Created At</th>
                            </tr>";
            foreach ($account_third_cash_pending_leave_list as $third_payment) {
                $third_approval_user    = User::where('id', $third_payment->user_id)->get();
                $third_approval_company = Companies::where('id', $third_payment->company_id)->get();
                $table.= "<tr>
                            <td>".$third_approval_user[0]->name."</td>
                            <td>".$third_payment->amount."</td>
                            <td>".$third_approval_company[0]->company_name."</td>
                            <td>".$third_payment->created_at."</td>
                            </tr>";
            }
            $table.="</table>";
            $emailList = DB::table('users')->where('status', 'Enabled')->where('role', '=', config('constants.SuperUser'))->pluck('email')->toArray();
            $mail_data = [
                    'body'=>$table,
                    'email'=>$emailList
                ];
            //echo $table;
            $this->common_task->cashPaymentPendingJobEmail($mail_data);
        }
    }

    public function cron_bank_payment_status_notify()
    {
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
                $table.= "<tr>
                            <td>".$leave_user[0]->name."</td>
                            <td>".$payment->amount."</td>
                            <td>".$first_approval_company[0]->company_name."</td>
                            <td>".$first_approval_bank[0]->bank_name."</td>
                            <td>".$payment->cheque_number."</td>
                            <td>".$payment->created_at."</td>
                            </tr>";
            }
            $table.="</table>";
            $emailList = DB::table('users')->where('status', 'Enabled')->where('role', '=', config('constants.ACCOUNT_ROLE'))->pluck('email')->toArray();
            $mail_data = [
                    'body'=>$table,
                    'email'=>$emailList
                ];
            //echo $table;
            $this->common_task->bankPaymentPendingJobEmail($mail_data);
        }

        //Send mail to Account
        $account_second_cash_pending_leave_list = BankPaymentApproval::where('second_approval_status', 'Pending')->get();
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
                $second_approval_user    = User::where('id', $second_payment->user_id)->get();
                $second_approval_company = Companies::where('id', $second_payment->company_id)->get();
                $second_approval_bank    = Banks::where('id', $second_payment->bank_id)->get();

                $table.= "<tr>
                            <td>".$second_approval_user[0]->name."</td>
                            <td>".$second_payment->amount."</td>
                            <td>".$second_approval_company[0]->company_name."</td>
                            <td>".$second_approval_bank[0]->bank_name."</td>
                            <td>".$second_payment->cheque_number."</td>
                            <td>".$second_payment->created_at."</td>
                            </tr>";
            }
            $table.="</table>";
            $emailList = DB::table('users')->where('status', 'Enabled')->where('role', '=', config('constants.Admin'))->pluck('email')->toArray();
            $mail_data = [
                    'body'=>$table,
                    'email'=>$emailList
                ];
            //echo $table;
            $this->common_task->bankPaymentPendingJobEmail($mail_data);
        }

        //Send mail to Super Admin
        $account_third_bank_pending_leave_list = BankPaymentApproval::where('third_approval_status', 'Pending')->get();
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
                $third_approval_user    = User::where('id', $third_payment->user_id)->get();
                $third_approval_company = Companies::where('id', $third_payment->company_id)->get();
                $third_approval_bank    = Banks::where('id', $third_payment->bank_id)->get();

                $table.= "<tr>
                            <td>".$third_approval_user[0]->name."</td>
                            <td>".$third_payment->amount."</td>
                            <td>".$third_approval_company[0]->company_name."</td>
                            <td>".$third_approval_bank[0]->bank_name."</td>
                            <td>".$third_payment->cheque_number."</td>
                            <td>".$third_payment->created_at."</td>
                            </tr>";
            }
            $table.="</table>";
            $emailList = DB::table('users')->where('status', 'Enabled')->where('role', '=', config('constants.SuperUser'))->pluck('email')->toArray();
            $mail_data = [
                    'body'=>$table,
                    'email'=>$emailList
                ];
            //echo $table;
            $this->common_task->bankPaymentPendingJobEmail($mail_data);
        }
    }

    public function cron_online_payment_status_notify()
    {
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
                $table.= "<tr>
                            <td>".$leave_user[0]->name."</td>
                            <td>".$payment->amount."</td>
                            <td>".$first_approval_company[0]->company_name."</td>
                            <td>".$first_approval_bank[0]->bank_name."</td>
                            <td>".$payment->created_at."</td>
                            </tr>";
            }
            $table.="</table>";
            $emailList = DB::table('users')->where('status', 'Enabled')->where('role', '=', config('constants.ACCOUNT_ROLE'))->pluck('email')->toArray();
            $mail_data = [
                    'body'=>$table,
                    'email'=>$emailList
                ];
            //echo $table;
            $this->common_task->onlinePaymentPendingJobEmail($mail_data);
        }

        //Send mail to Account
        $account_second_online_pending_leave_list = OnlinePaymentApproval::where('second_approval_status', 'Pending')->get();
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
                $second_approval_user    = User::where('id', $second_payment->user_id)->get();
                $second_approval_company = Companies::where('id', $second_payment->company_id)->get();
                $second_approval_bank    = Banks::where('id', $second_payment->bank_id)->get();

                $table.= "<tr>
                            <td>".$second_approval_user[0]->name."</td>
                            <td>".$second_payment->amount."</td>
                            <td>".$second_approval_company[0]->company_name."</td>
                            <td>".$second_approval_bank[0]->bank_name."</td>
                            <td>".$second_payment->created_at."</td>
                            </tr>";
            }
            $table.="</table>";
            $emailList = DB::table('users')->where('status', 'Enabled')->where('role', '=', config('constants.Admin'))->pluck('email')->toArray();
            $mail_data = [
                    'body'=>$table,
                    'email'=>$emailList
                ];
            //echo $table;
            $this->common_task->onlinePaymentPendingJobEmail($mail_data);
        }

        //Send mail to Super Admin
        $account_third_bank_pending_leave_list = OnlinePaymentApproval::where('third_approval_status', 'Pending')->get();
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
                $third_approval_user    = User::where('id', $third_payment->user_id)->get();
                $third_approval_company = Companies::where('id', $third_payment->company_id)->get();
                $third_approval_bank    = Banks::where('id', $third_payment->bank_id)->get();

                $table.= "<tr>
                            <td>".$third_approval_user[0]->name."</td>
                            <td>".$third_payment->amount."</td>
                            <td>".$third_approval_company[0]->company_name."</td>
                            <td>".$third_approval_bank[0]->bank_name."</td>
                            <td>".$third_payment->created_at."</td>
                            </tr>";
            }
            $table.="</table>";
            $emailList = DB::table('users')->where('status', 'Enabled')->where('role', '=', config('constants.SuperUser'))->pluck('email')->toArray();
            $mail_data = [
                    'body'=>$table,
                    'email'=>$emailList
                ];
            //echo $table;
            $this->common_task->onlinePaymentPendingJobEmail($mail_data);
        }
    }
}
