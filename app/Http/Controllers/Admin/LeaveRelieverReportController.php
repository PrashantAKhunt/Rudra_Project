<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use App\Common_query;
use App\Companies;
use App\User;
use App\Banks;
use App\Asset;
use App\Leaves;
use Illuminate\Support\Facades\Validator;

use App\Lib\NotificationTask;
use App\Lib\CommonTask;
use App\Role_module;
use App\Lib\Permissions;

class LeaveRelieverReportController extends Controller
{
    public $data;
    private $module_id;

    public function __construct()
    {
        $this->data['module_title'] = "Leave Reliever";
        $this->data['module_link'] = "admin.leaves";
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();
        $this->super_admin = \App\User::where('role', config('constants.SuperUser'))->first();
        $this->module_id = 37;
    }

    public function leave_reliever_report(Request $request)
    {
        $this->data['page_title'] = "Leave Reliever Report";
        
        $role_Array = array(config('constants.REAL_HR'),config('constants.SuperUser'),config('constants.Admin'),config('constants.ASSISTANT')) ;

        if (in_array(Auth::user()->role, $role_Array)) {

            $this->data['user'] =  User::orderBy('name')->where("status", "Enabled")->get()->pluck('name', 'id');
        }
        else {
            $this->data['user'] =  User::orderBy('name')->where("status", "Enabled")->where("id",Auth::user()->id)->get()->pluck('name', 'id');
        }

        $this->data['records'] = [];
        $this->data['selectedUser'] = [];
        $this->data['date'] = "";
        $this->data['report_type'] = "";
        $this->data['csv_data'] = "javascript:void(0);";

        $trip_full_view_permission = Permissions::checkPermission($this->module_id, 5);
        if (!$trip_full_view_permission) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }
        $from = date('Y-m-d H:i:s');
        $date = strtotime($from);
        $first_date = strtotime("-7 day", $date);
        $second_date   = date('Y-m-d H:i:s', $date);

        if ($request->method() == 'POST') {
            $this->data['selectedUser'] = $request->get('user_id');
            $user_ids = $request->get('user_id');
            //dd($this->data['selectedUser']);
            $this->data['date'] = $request->get('date');
            $reportType = $request->get('report_type');
            $date = $request->get('date');
            $mainDate = explode("-", $date);
            $strFirstdate = str_replace("/", "-", $mainDate[0]);
            $strLastdate = str_replace("/", "-", $mainDate[1]);
            $first_date = date('Y-m-d h:m:s', strtotime($strFirstdate));
            $second_date = date('Y-m-d h:m:s', strtotime($strLastdate));

            $this->data['records'] = Leaves::select('leaves.*', 'users.name','reliever_user.name as reliever_name')
                ->leftJoin('users', 'users.id', '=', 'leaves.user_id')
                ->leftJoin('users as reliever_user', 'reliever_user.id', '=', 'leaves.assign_work_user_id')
                ->where(function ($query) use ($user_ids) {
                    if (!empty($user_ids)) {
                        $query->orwhereIn('leaves.assign_work_user_id', $user_ids);
                    }
                })
                ->whereBetween('leaves.created_at', [$first_date, $second_date])
                ->get();
        }
        return view('admin.leave_reliever_report.leave_reliever', $this->data);
    }
}
