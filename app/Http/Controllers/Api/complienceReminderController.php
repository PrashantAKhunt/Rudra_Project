<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Exception;
use App\Role_module;
use App\Email_format;
use App\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\Mails;
use App\Compliance_reminders;
use App\Compliance_reminders_done_status;
use App\Lib\CommonTask;
use App\Lib\NotificationTask;

class complienceReminderController extends Controller
{

    private $page_limit = 72;
    public $data;
    private $notification_task;
    public $common_task;

    public function __construct()
    {
        $this->super_admin = \App\User::where('role', config('constants.SuperUser'))->first();
        $this->notification_task = new NotificationTask();
        $this->common_task = new CommonTask();

    }

    public function api_complience_reminder_list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $userId = \App\User::where('id', $request_data['user_id'])->value('email');
        $response_data = [];
        
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
                 'compliance_category.compliance_name as compliance_type',
                 'compliance_reminders_done_status.id',
                 'compliance_reminders_done_status.remind_entry_date',
                 'compliance_reminders_done_status.remind_entry_time',
                 'compliance_reminders_done_status.responsible_person_status',
                 'compliance_reminders_done_status.payment_responsible_person_status','compliance_reminders_done_status.checker_status',
                 'compliance_reminders_done_status.super_admin_checker_status',
                 'compliance_reminders_done_status.responsible_person_id','compliance_reminders_done_status.payment_responsible_person_id',
                 'compliance_reminders_done_status.checker_id','compliance_reminders_done_status.super_admin_checker_id',
                 'A.name as responsible_person','B.name as payment_responsible',
                 'C.name as super_admin_checker','D.name as checker',
                 'compliance_reminders_done_status.created_at'
                 ];

        $partial_query = Compliance_reminders::join('compliance_reminders_done_status','compliance_reminders.id','=','compliance_reminders_done_status.compliance_reminders_id')
                ->join('company','company.id','=','compliance_reminders.company_id')
                ->join('compliance_category','compliance_category.id','=','compliance_reminders.compliance_category_id')
                ->join('users as A','A.email','=','compliance_reminders.responsible_person_id')
                ->join('users as B','B.email','=','compliance_reminders.payment_responsible_person_id')
                ->join('users as C','C.email','=','compliance_reminders.super_admin_checker_id')
                ->leftjoin('users as D','D.email','=','compliance_reminders.checker_id')
                ->where('compliance_reminders_done_status.final_status','Pending');;

                $reminder_list =  $partial_query->where(function ($query) use ($userId)  {
                        $query->where('compliance_reminders_done_status.responsible_person_id', $userId)
                                //->where('compliance_reminders_done_status.responsible_person_status', 'Pending')
                        ->orWhere(function ($query) use ($userId)  {
                            $query->Where('compliance_reminders_done_status.payment_responsible_person_id', $userId );
                            //->where('compliance_reminders_done_status.responsible_person_status', 'Completed')
                            //->Where('compliance_reminders_done_status.payment_responsible_person_status','Pending')
                        })->orWhere(function ($query) use ($userId)  {
                            $query->Where('compliance_reminders_done_status.checker_id', $userId );
                                //->where('compliance_reminders_done_status.responsible_person_status', 'Completed');
                                //->Where('compliance_reminders_done_status.payment_responsible_person_status','Completed')
                                //->Where('compliance_reminders_done_status.checker_status','Pending');
                        })->orWhere(function ($query) use ($userId)  {
                            $query->Where('compliance_reminders_done_status.super_admin_checker_id', $userId );
                                //->where('compliance_reminders_done_status.responsible_person_status', 'Completed')
                                //->Where('compliance_reminders_done_status.payment_responsible_person_status','Completed')
                                //->Where('compliance_reminders_done_status.checker_status','Completed')
                                //->Where('compliance_reminders_done_status.super_admin_checker_status','Pending');
                        });
                });

                $records = $reminder_list->get($get_fields)->toArray();
                if (empty($records)) {
                    return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
                }
        
                $response_data['comoliance_reminder_list'] = $records;
                return response()->json(['status' => true, 'msg' => 'Record found.', 'data' => $response_data]);
       
    }

    public function api_complete_compliance_reminder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'compliance_done_id' => 'required',
            'user_type' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $userId = $request_data['user_id'];
        $response_data = [];
    
        $compliance_done_id = $request_data['compliance_done_id']; 
        $type = $request_data['user_type']; 

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
                $mail_data['email_list'] = [$compliance_reminder_data[0]->payment_responsible_person_id ];

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
            
            return response()->json(['status' => true, 'msg' => 'Complience successfully mark as done.', 'data' => []]);
        } else {
            return response()->json(['status' => true, 'msg' => 'Error occurre in insert. Try Again!', 'data' => []]);
        }
    }



    
}
