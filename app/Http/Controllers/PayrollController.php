<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DayPayroll;
use App\Lib\CommonTask;
use App\Lib\NotificationTask;
class PayrollController extends Controller
{
    private $common_task;
    private $notification_task;

    public function __construct()
    {
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();
    }
    
    public function generate_daily_payroll() {
        
    }
}
