<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use App\Common_query;
use App\Companies;
use App\Compliance_category;
use App\Compliance_reminders;
use App\Compliance_reminders_done_status;
use App\User;
use Illuminate\Support\Facades\Validator;
use App\Lib\Permissions;
use App\Lib\CommonTask;
use App\Lib\NotificationTask;

class complienceReminderController extends Controller
{
    public $data;
    private $module_id = 72;
    public $notification_task;
    public $common_task;


    public function __construct() {

        $this->data['module_title'] = "Compliance Reminders";
        $this->data['module_link'] = "admin.compliance_reminders";
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();
    }

    public function index()
    {//dd(strtotime(date('Y-m-d')));
        $compliance_add_permission = Permissions::checkPermission($this->module_id, 3);
        $compliance_edit_permission = Permissions::checkPermission($this->module_id, 2);

        if (!Permissions::checkPermission($this->module_id, 5)) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You do not have access to this module.');
        }
        $this->data['compliance_add_permission'] = $compliance_add_permission;
        $this->data['compliance_edit_permission'] = $compliance_edit_permission;
        $this->data['page_title'] = "Compliance Reminders";
        $get_fields = ['compliance_reminders.*','company.company_name','compliance_category.compliance_name as compliance_type','A.name as responsible_person',
                  'B.name as payment_responsible','C.name as super_admin_checker','D.name as checker'];

        $this->data['records'] = Compliance_reminders::join('company','company.id','=','compliance_reminders.company_id')
           ->join('compliance_category','compliance_category.id','=','compliance_reminders.compliance_category_id')
           ->join('users as A','A.email','=','compliance_reminders.responsible_person_id')
           ->join('users as B','B.email','=','compliance_reminders.payment_responsible_person_id')
           ->join('users as C','C.email','=','compliance_reminders.super_admin_checker_id')
           ->leftjoin('users as D','D.email','=','compliance_reminders.checker_id')
           ->get($get_fields);


        return view('admin.complience_reminders.index', $this->data);
    }

    public function add_compliance_reminder()
    {

        //dd(date('l', strtotime('Monday + 1 day')));
        //dd(date('Y-m-d', strtotime('Sunday next week')));

        if (!Permissions::checkPermission($this->module_id, 3)) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You do not have access to this module.');
        }

        $this->data['page_title'] = 'Add Compliance Reminders';
        $this->data['companies'] = Companies::select('company_name','id')->where('status', 'Enabled')->orderBy('company_name', 'asc')->get();
        $this->data['users_data'] = User::select('id', 'name')->where('status', 'Enabled')->orderBy('name', 'asc')->where('is_user_relieved', 0)->get();
        $this->data['compliance_types'] = Compliance_category::select('id', 'compliance_name')->orderBy('compliance_name', 'asc')->where('status', 'Enabled')->get();
        $this->data['periodicity_types'] = ['Day','Week','Month','2Months','Quater','Halfyearly','Yearly','Biyearly','3Years','5years'];
        $this->data['days'] = [1,2,3,4,5,6,7];

        //dd($this->data);
        return view('admin.complience_reminders.add_complience_reminder', $this->data);
    }

    public function insert_compliance_reminder(Request $request)
    {
        $validator_normal = Validator::make($request->all(), [
            'company_id' => 'required',
            'compliance_category_id' => 'required',
            'compliance_name' => 'required',
            'compliance_description' => 'required',
            'periodicity_type' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'periodicity_time' => 'required',
            'responsible_person_id' => 'required',
            'payment_responsible_person_id' => 'required',
            'checker_id' => 'required',
            'super_admin_checker_id' => 'required',
            // 'first_day_interval' => 'required',
            // 'second_day_interval' => 'required',
            // 'third_day_interval' => 'required'

        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_compliance_reminder')->with('error', 'Please follow validation rules.');
        }
        $request_data = $request->all();
        $users_arr = [$request_data['responsible_person_id'], $request_data['payment_responsible_person_id'],$request_data['checker_id'],$request_data['super_admin_checker_id']];


        $complianceModel = new Compliance_reminders();
        $complianceModel->user_id = Auth::user()->id;
        $complianceModel->company_id = $request_data['company_id'];
        $complianceModel->compliance_category_id = $request_data['compliance_category_id'];
        $complianceModel->compliance_name = $request_data['compliance_name'];
        $complianceModel->compliance_description = $request_data['compliance_description'];
        $complianceModel->periodicity_type = $request_data['periodicity_type'];
        $complianceModel->start_date = date('Y-m-d', strtotime($request_data['start_date']));
        $complianceModel->end_date = date('Y-m-d', strtotime($request_data['end_date']));
        $complianceModel->periodicity_time = date('H:i:s', strtotime($request_data['periodicity_time']));

        $complianceModel->responsible_person_id = \App\User::where('id', $request_data['responsible_person_id'])->value('email');
        $complianceModel->checker_id = \App\User::where('id', $request_data['checker_id'])->value('email');
        $complianceModel->payment_responsible_person_id = \App\User::where('id', $request_data['payment_responsible_person_id'])->value('email');
        $complianceModel->super_admin_checker_id = \App\User::where('id', $request_data['super_admin_checker_id'])->value('email');

        $complianceModel->first_day_interval = $request_data['first_day_interval'];
        $complianceModel->second_day_interval = $request_data['second_day_interval'];
        $complianceModel->third_day_interval = $request_data['third_day_interval'];

        if ($request_data['periodicity_type'] == "Day") {
            $complianceModel->periodic_date = $request_data['periodic_date'];
        } elseif ($request_data['periodicity_type'] == "Week") {
            $complianceModel->periodic_week_day = $request_data['periodic_week_day'];
        } elseif ($request_data['periodicity_type'] == "Month") {
            $complianceModel->periodic_date = $request_data['periodic_date'];
        } else {
            $complianceModel->periodic_month = $request_data['periodic_month'];
            $complianceModel->periodic_date = $request_data['periodic_date'];

        }

        $complianceModel->created_at = date('Y-m-d H:i:s');
        $complianceModel->created_ip = $request->ip();
        $complianceModel->updated_at = date('Y-m-d H:i:s');
        $complianceModel->updated_ip = $request->ip();

        if ($complianceModel->save()) {
            // foreach ($users_arr as $key => $user) {
            //     $complianceDoneModel = new Compliance_reminders_done_status();
            //     $complianceDoneModel->compliance_reminders_id = $complianceModel->id;
            //     $complianceDoneModel->user_id = $user;
            //     $complianceDoneModel->status = "Pending";
            //     $complianceDoneModel->created_at = date('Y-m-d h:i:s');
            //     $complianceDoneModel->created_ip = $request->ip();
            //     $complianceDoneModel->updated_at = date('Y-m-d h:i:s');
            //     $complianceDoneModel->updated_ip = $request->ip();
            //     $complianceDoneModel->save();
            // }

            #------ Mail & Notify
            $mail_data = [];
            $mail_data['compliance_category'] = $compliance_type =  Compliance_category::where('id',$request_data['compliance_category_id'])->value('compliance_name');
            $mail_data['company'] = Companies::where('id',$request_data['company_id'])->value('company_name');
            $mail_data['email_list'] = \App\User::where('id', $request_data['responsible_person_id'])->pluck('email')->toArray();
            $user_list =  \App\User::where('id', $request_data['responsible_person_id'])->pluck('email')->toArray();

            $this->common_task->complianceReminder($mail_data);
            $this->notification_task->complianceReminderNotify($compliance_type, $user_list);

            return redirect()->route('admin.compliance_reminders')->with('success', 'Complience Reminder successfully added.');
        } else {
            return redirect()->route('admin.add_compliance_reminder')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function edit_compliance_reminder($id)
    {

        if (!Permissions::checkPermission($this->module_id, 2)) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You do not have access to this module.');
        }

        $this->data['page_title'] = "Edit Compliance Reminders";
        $this->data['companies'] = Companies::select('company_name','id')->where('status', 'Enabled')->orderBy('company_name', 'asc')->get();

        $this->data['users_data'] = User::where('status', 'Enabled')->orderBy('name', 'asc')->where('is_user_relieved', 0)->pluck('name','email');

        $this->data['compliance_types'] = Compliance_category::select('id', 'compliance_name')->orderBy('compliance_name', 'asc')->where('status', 'Enabled')->get();
        $this->data['periodicity_types'] = ['Day','Week','Month','2Months','Quater','Halfyearly','Yearly','Biyearly','3Years','5years'];
        $this->data['days'] = [1,2,3,4,5,6,7];
        $this->data['week_day'] = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday","Saturday","Sunday"];

        $this->data['data'] = $data = Compliance_reminders::where('id',$id)->first();
        $total_month = [];
        $total_days = [];
        for ($i=1; $i <= 12 ; $i++) {
            $total_month[]= $i;
        }
        for ($k=1; $k <= 30 ; $k++) {
            $total_days[]= $k;
        }
        $this->data['total_month'] = $total_month;
        $this->data['total_days'] = $total_days;
        //dd($records['id']);

        return view('admin.complience_reminders.edit_complience_reminder', $this->data);
    }

    public function update_compliance_reminder(Request $request)
    {
        $validator_normal = Validator::make($request->all(), [
            'company_id' => 'required',
            'compliance_category_id' => 'required',
            'compliance_name' => 'required',
            'compliance_description' => 'required',
            'periodicity_type' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'periodicity_time' => 'required',
            'responsible_person_id' => 'required',
            'payment_responsible_person_id' => 'required',
            'checker_id' => 'required',
            'super_admin_checker_id' => 'required',
            // 'first_day_interval' => 'required',
            // 'second_day_interval' => 'required',
            // 'third_day_interval' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.edit_compliance_reminder')->with('error', 'Please follow validation rules.');
        }
        $request_data = $request->all();
        $complience_id = $request->input('id');

        $users_arr = [$request_data['responsible_person_id'], $request_data['payment_responsible_person_id'],$request_data['checker_id'],$request_data['super_admin_checker_id']];

        $update_arr = [
            'user_id' => Auth::user()->id,
            'company_id'=> $request_data['company_id'],
            'compliance_category_id'=> $request_data['compliance_category_id'],
            'compliance_name' => $request_data['compliance_name'],
            'compliance_description' => $request_data['compliance_description'],
            'periodicity_type'=> $request_data['periodicity_type'],
            'start_date'=> date('Y-m-d', strtotime($request_data['start_date'])),
            'end_date' => date('Y-m-d', strtotime($request_data['end_date'])),
            'periodicity_time' => date('H:i:s', strtotime($request_data['periodicity_time'])),

            'responsible_person_id' => $request_data['responsible_person_id'],
            'payment_responsible_person_id' => $request_data['payment_responsible_person_id'],
            'checker_id' => $request_data['checker_id'],
            'super_admin_checker_id' => $request_data['super_admin_checker_id'],

            'first_day_interval' => $request_data['first_day_interval'],
            'second_day_interval' => $request_data['second_day_interval'],
            'third_day_interval' => $request_data['third_day_interval'],
            'created_at'=> date('Y-m-d H:i:s'),
            'updated_at'=> date('Y-m-d H:i:s'),
            'created_ip' => $request->ip(),
            'updated_ip' => $request->ip()

        ];

        if ($request_data['periodicity_type'] == "Day") {
            $update_arr['periodic_date'] = $request_data['periodic_date'];
        } elseif ($request_data['periodicity_type'] == "Week") {
            $update_arr['periodic_week_day'] = $request_data['periodic_week_day'];
        } elseif ($request_data['periodicity_type'] == "Month") {
            $update_arr['periodic_date'] = $request_data['periodic_date'];
        } else {
            $update_arr['periodic_month'] = $request_data['periodic_month'];
            $update_arr['periodic_date'] = $request_data['periodic_date'];

        }

        if (Compliance_reminders::where('id',$complience_id)->update($update_arr)) {
            // if (Compliance_reminders_done_status::where('compliance_reminders_id',$complience_id)->get()->count() > 0) {
            //     Compliance_reminders_done_status::where('compliance_reminders_id',$complience_id)->delete();
            // }
            // foreach ($users_arr as $key => $user) {
            //     $complianceDoneModel = new Compliance_reminders_done_status();
            //     $complianceDoneModel->compliance_reminders_id = $complience_id;
            //     $complianceDoneModel->user_id = $user;
            //     $complianceDoneModel->status = "Pending";
            //     $complianceDoneModel->created_at = date('Y-m-d h:i:s');
            //     $complianceDoneModel->created_ip = $request->ip();
            //     $complianceDoneModel->updated_at = date('Y-m-d h:i:s');
            //     $complianceDoneModel->updated_ip = $request->ip();
            //     $complianceDoneModel->save();
            // }

            #------ Mail & Notify
            $mail_data = [];
            $mail_data['compliance_category'] = $compliance_type =  Compliance_category::where('id',$request_data['compliance_category_id'])->value('compliance_name');
            $mail_data['company'] = Companies::where('id',$request_data['company_id'])->value('company_name');
            $mail_data['email_list'] = [$request_data['responsible_person_id']];
            $user_list = [$request_data['responsible_person_id']];

            $this->common_task->complianceReminder($mail_data);
            $this->notification_task->complianceReminderNotify($compliance_type, $user_list);

        }

        return redirect()->route('admin.compliance_reminders')->with('success', 'Complience Reminder successfully updated.');
    }

    public function complience_reminder_list()
    {

        $this->data['page_title'] = "Compliance Reminders List";

        $get_fields = [
                'compliance_reminders.compliance_name',
                'compliance_reminders.compliance_description',
                'compliance_reminders.periodicity_type',
                'compliance_reminders.start_date',
                 'compliance_reminders.end_date',
                 'compliance_reminders.periodicity_time',
                 'compliance_reminders.first_day_interval',
                 'compliance_reminders.second_day_interval',
                 'compliance_reminders.third_day_interval',
                 'company.company_name',
                 'compliance_category.compliance_name as compliance_type','compliance_reminders_done_status.remind_entry_date',
                 'compliance_reminders_done_status.remind_entry_time',
                 'compliance_reminders_done_status.id','compliance_reminders_done_status.responsible_person_status',
                 'compliance_reminders_done_status.payment_responsible_person_status','compliance_reminders_done_status.checker_status',
                 'compliance_reminders_done_status.super_admin_checker_status',
                 'compliance_reminders_done_status.responsible_person_id','compliance_reminders_done_status.payment_responsible_person_id',
                 'compliance_reminders_done_status.checker_id','compliance_reminders_done_status.super_admin_checker_id',
                 'A.name as responsible_person','B.name as payment_responsible',
                 'C.name as super_admin_checker','D.name as checker',
                 'compliance_reminders_done_status.responsible_person_attachment',
                 'compliance_reminders_done_status.payment_responsible_person_attachment',
                ];

        $partial_query = Compliance_reminders::join('compliance_reminders_done_status','compliance_reminders.id','=','compliance_reminders_done_status.compliance_reminders_id')
                ->join('company','company.id','=','compliance_reminders.company_id')
                ->join('compliance_category','compliance_category.id','=','compliance_reminders.compliance_category_id')
                ->join('users as A','A.email','=','compliance_reminders.responsible_person_id')
                ->join('users as B','B.email','=','compliance_reminders.payment_responsible_person_id')
                ->join('users as C','C.email','=','compliance_reminders.super_admin_checker_id')
                ->leftjoin('users as D','D.email','=','compliance_reminders.checker_id')
                ->where('compliance_reminders_done_status.final_status','Pending');

                $reminder_list =  $partial_query->where(function ($query)  {
                        $query->where('compliance_reminders_done_status.responsible_person_id', Auth::user()->email)
                                //->where('compliance_reminders_done_status.responsible_person_status', 'Pending')
                        ->orWhere(function ($query)  {
                            $query->Where('compliance_reminders_done_status.payment_responsible_person_id', Auth::user()->email );
                            //->where('compliance_reminders_done_status.responsible_person_status', 'Completed')
                            //->Where('compliance_reminders_done_status.payment_responsible_person_status','Pending');
                        })->orWhere(function ($query)  {
                            $query->Where('compliance_reminders_done_status.checker_id', Auth::user()->email );
                                //->where('compliance_reminders_done_status.responsible_person_status', 'Completed')
                                //->Where('compliance_reminders_done_status.payment_responsible_person_status','Completed')
                                //->Where('compliance_reminders_done_status.checker_status','Pending');
                        })->orWhere(function ($query)  {
                            $query->Where('compliance_reminders_done_status.super_admin_checker_id', Auth::user()->email );
                                //->where('compliance_reminders_done_status.responsible_person_status', 'Completed')
                                //->Where('compliance_reminders_done_status.payment_responsible_person_status','Completed')
                                //->Where('compliance_reminders_done_status.checker_status','Completed')
                                //->Where('compliance_reminders_done_status.super_admin_checker_status','Pending');
                        });
                });

                $records = $reminder_list->get($get_fields);

                //dd($records->toArray());
        $this->data['records'] = $records;
        return view('admin.complience_reminders.complience_reminder_list', $this->data);
    }

    /* public function complete_compliance_reminder($id,$type)
    {
        echo $id."-".$type;
        dd("");

        $compliance_done_id = $id;
        $get_fields = ['compliance_reminders.*','company.company_name','compliance_category.compliance_name as compliance_type'];

        $compliance_data = Compliance_reminders_done_status::where('id',$compliance_done_id)->get(['compliance_reminders_id']);
        $compliance_reminder_data = Compliance_reminders::join('company','company.id','=','compliance_reminders.company_id')
                        ->join('compliance_category','compliance_category.id','=','compliance_reminders.compliance_category_id')
                        ->where('compliance_reminders.id',$compliance_data[0]->compliance_reminders_id)
                        ->get($get_fields);

                $mail_data = [];
                $mail_data['compliance_category'] = $compliance_type = $compliance_reminder_data[0]->compliance_type;
                $mail_data['company'] = $compliance_reminder_data[0]->company_name;

        //$user_ids = Compliance_reminders_done_status::where('id',$compliance_done_id)->first(['responsible_person_id','payment_responsible_person_id','checker_id','super_admin_checker_id'])->toArray();
        //$user_type = array_search(Auth::user()->id,$user_ids,true);

        if ($type == 'responsible_person_status') {
            $update_arr = [
                'responsible_person_status'=> 'Completed',
                'responsible_person_datetime' => date('Y-m-d H:i:s')
            ];
                $user_list =[$compliance_reminder_data[0]->payment_responsible_person_id];
                $mail_data['email_list'] = [$compliance_reminder_data[0]->payment_responsible_person_id];

        } elseif ($type == 'payment_responsible_person_status') {
            $update_arr = [
                'payment_responsible_person_status'=> 'Completed',
                'payment_responsible_person_datetime' => date('Y-m-d H:i:s')
            ];
                $user_list = [$compliance_reminder_data[0]->checker_id];
                $mail_data['email_list'] = [$compliance_reminder_data[0]->checker_id];

        } elseif ($type == 'checker_status') {
            $update_arr = [
                'checker_status'=> 'Completed',
                'checker_datetime' => date('Y-m-d H:i:s')
            ];
                $user_list = [$compliance_reminder_data[0]->super_admin_checker_id];
                $mail_data['email_list'] = [$compliance_reminder_data[0]->super_admin_checker_id];

        } elseif ($type == 'super_admin_checker_status') {
            $update_arr = [
                'super_admin_checker_status'=> 'Completed',
                'final_status' => 'Completed',
                'super_admin_checker_datetime' => date('Y-m-d H:i:s')
            ];
        }

        if (Compliance_reminders_done_status::where('id',$compliance_done_id)->update($update_arr)) {
            #------ Mail & Notify
            if ($type != 'super_admin_checker_status') {
                $this->common_task->complianceReminder($mail_data);
                $this->notification_task->complianceReminderNotify($compliance_type, $user_list);
            }

           return redirect()->route('admin.complience_reminder_list')->with('success', 'Complience successfully mark as done.');
        } else {
            return redirect()->route('admin.complience_reminder_list')->with('error', 'Error occurre in insert. Try Again!');
        }
    } */

    public function complete_compliance_reminder(Request $request)
    {
        // dd($request->all());

        $compliance_done_id = $request->get('id');
        $type = $request->get('type');
        $get_fields = ['compliance_reminders.*','company.company_name','compliance_category.compliance_name as compliance_type'];

        $compliance_data = Compliance_reminders_done_status::where('id',$compliance_done_id)->get(['compliance_reminders_id']);
        $compliance_reminder_data = Compliance_reminders::join('company','company.id','=','compliance_reminders.company_id')
                        ->join('compliance_category','compliance_category.id','=','compliance_reminders.compliance_category_id')
                        ->where('compliance_reminders.id',$compliance_data[0]->compliance_reminders_id)
                        ->get($get_fields);

                $mail_data = [];
                $mail_data['compliance_category'] = $compliance_type = $compliance_reminder_data[0]->compliance_type;
                $mail_data['company'] = $compliance_reminder_data[0]->company_name;

        //$user_ids = Compliance_reminders_done_status::where('id',$compliance_done_id)->first(['responsible_person_id','payment_responsible_person_id','checker_id','super_admin_checker_id'])->toArray();
        //$user_type = array_search(Auth::user()->id,$user_ids,true);
        $file_attachment_name = "";
        if($request->hasFile('file_attachment')){
            $file_attachment = $request->file('file_attachment');
            $file_attachment_name = $file_attachment->store('public/complience_reminder_attachment');
        }

        if ($type == 'responsible_person_status') {
            $update_arr = [
                'responsible_person_status'=> 'Completed',
                'responsible_person_datetime' => date('Y-m-d H:i:s'),
                'responsible_person_attachment' => $file_attachment_name,
            ];
                $user_list =[$compliance_reminder_data[0]->payment_responsible_person_id];
                $mail_data['email_list'] = [$compliance_reminder_data[0]->payment_responsible_person_id];

        } elseif ($type == 'payment_responsible_person_status') {
            $update_arr = [
                'payment_responsible_person_status'=> 'Completed',
                'payment_responsible_person_datetime' => date('Y-m-d H:i:s'),
                'payment_responsible_person_attachment' => $file_attachment_name,
            ];
                $user_list = [$compliance_reminder_data[0]->checker_id];
                $mail_data['email_list'] = [$compliance_reminder_data[0]->checker_id];

        } elseif ($type == 'checker_status') {
            $update_arr = [
                'checker_status'=> 'Completed',
                'checker_datetime' => date('Y-m-d H:i:s')
            ];
                $user_list = [$compliance_reminder_data[0]->super_admin_checker_id];
                $mail_data['email_list'] = [$compliance_reminder_data[0]->super_admin_checker_id];

        } elseif ($type == 'super_admin_checker_status') {
            $update_arr = [
                'super_admin_checker_status'=> 'Completed',
                'final_status' => 'Completed',
                'super_admin_checker_datetime' => date('Y-m-d H:i:s')
            ];
        }

        if (Compliance_reminders_done_status::where('id',$compliance_done_id)->update($update_arr)) {
            #------ Mail & Notify
            if ($type != 'super_admin_checker_status') {
                $this->common_task->complianceReminder($mail_data);
                $this->notification_task->complianceReminderNotify($compliance_type, $user_list);
            }

           return redirect()->route('admin.complience_reminder_list')->with('success', 'Complience successfully mark as done.');
        } else {
            return redirect()->route('admin.complience_reminder_list')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    //------------------------------------------------------------- CRON JOB







}
