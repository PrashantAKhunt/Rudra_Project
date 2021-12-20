<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Lib\NotificationTask;
use App\Lib\CommonTask;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;
use Exception;
use App\Role_module;
use App\Email_format;
use App\User;
use App\Travel;
use App\Projects;
use Illuminate\Support\Facades\Mail;
use App\Mail\Mails;

class TravelController extends Controller
{

    private $page_limit = 20;
    public $data;
    public $common_task;
    private $module_id = 18;
    private $notification_task;

    public function __construct()
    {
        $this->super_admin = \App\User::where('role', config('constants.SuperUser'))->first();
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();
    }

    public function add_travel(Request $request)
    {
        $validator_normal = Validator::make($request->all(), [
            'user_id' => 'required',
            'company_id' => 'required',
            'project_id' => 'required',
            'travel_via' => 'required',
            'traveler_ids' => 'required',
            'travel_company' => 'required',
            'ticket_no' => 'required',
            'ticket_image' => 'required',
            'departure_datetime' => 'required',
            'arrival_datetime' => 'required',
            'total_amount' => 'required',
            'from' => 'required',
            'to' => 'required',
            'payment_type' => 'required'
        ]);

        if ($validator_normal->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        //upload user ticket image
        $ticket_image_file = '';
        if ($request->hasFile('ticket_image')) {
            $ticket_image = $request->file('ticket_image');
            $file_path = $ticket_image->store('public/ticket_image');
            if ($file_path) {
                $ticket_image_file = $file_path;
            }
        }

        $userDetail = User::select('id', 'name')->where('id', $request->get('user_id'))->get()->first();

        $tarvel_arr = [
            'company_id' => $request->get('company_id'),
            'project_id' => $request->get('project_id'),
            'other_project_details' => $request->input('other_project'),
            'travel_via' => $request->get('travel_via'),
            'traveler_ids' => $request->get('traveler_ids'),
            'travel_company' => $request->get('travel_company'),
            'ticket_no' => $request->get('ticket_no'),
            'ticket_image' => !empty($ticket_image_file) ? $ticket_image_file : NULL,
            'departure_datetime' => date('Y-m-d H:i:s', strtotime($request->get('departure_datetime'))),
            'arrival_datetime' => date('Y-m-d H:i:s', strtotime($request->get('arrival_datetime'))),
            'total_amount' => $request->get('total_amount'),
            'booked_by' => $request->get('user_id'),
            'from' => $request->get('from'),
            'to' => $request->get('to'),
            'work_details' => $request->get('work_details'),
            'booking_note' => $request->get('booking_note'),
            'payment_type' => $request->get('payment_type'),
            'status' => 'Pending',
            'created_at' => date('Y-m-d H:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => $request->get('user_id'),
        ];

        Travel::insert($tarvel_arr);

        $mail_data = [];
        $mail_data['name'] = $userDetail->name;
        $mail_data['text'] = "Travel Expence is added with below details, Please check";
        $mail_data['to_email'] = user::where('status', 'Enabled')->where('role', config('constants.ASSISTANT'))->pluck('email')->toArray();
        $travelers = user::whereIn('id', [$request->get('traveler_ids')])->pluck('name')->toArray();
        $mail_data['travelers'] = implode(", ", $travelers);
        $mail_data['travel_company'] = $request->get('travel_company');
        $mail_data['ticket_no'] = $request->get('ticket_no');
        $mail_data['departure_datetime'] =  date('Y-m-d H:i:s', strtotime($request->get('departure_datetime')));
        $mail_data['arrival_datetime'] = date('Y-m-d H:i:s', strtotime($request->get('arrival_datetime')));
        $mail_data['total_amount'] = $request->get('total_amount');
        $mail_data['from'] = $request->get('from');
        $mail_data['to'] = $request->get('to');

        $this->common_task->travelEmail($mail_data);

        return response()->json(['status' => true, 'msg' => "Travel successfully added.", 'data' => []]);
    }

    public function update_travel(Request $request)
    {
        $validator_normal = Validator::make($request->all(), [
            'id' => 'required',
            'company_id' => 'required',
            'project_id' => 'required',
            'user_id' => 'required',
            'travel_via' => 'required',
            'traveler_ids' => 'required',
            'travel_company' => 'required',
            'ticket_no' => 'required',
            //'ticket_image' => 'required',
            'departure_datetime' => 'required',
            'arrival_datetime' => 'required',
            'total_amount' => 'required',
            'from' => 'required',
            'to' => 'required',
            'payment_type' => 'required'
        ]);

        if ($validator_normal->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        //upload user ticket image
        $ticket_image_file = '';
        if ($request->hasFile('ticket_image')) {
            $ticket_image = $request->file('ticket_image');
            $file_path = $ticket_image->store('public/ticket_image');
            if ($file_path) {
                $ticket_image_file = $file_path;
            }
        }

        $update_arr = [
            'project_id' => $request->get('project_id'),
            'travel_via' => $request->get('travel_via'),
            'traveler_ids' => $request->get('traveler_ids'),
            'travel_company' => $request->get('travel_company'),
            'ticket_no' => $request->get('ticket_no'),
            //'ticket_image' => !empty($ticket_image_file) ? $ticket_image_file : NULL,
            'departure_datetime' => date('Y-m-d H:i:s', strtotime($request->get('departure_datetime'))),
            'arrival_datetime' => date('Y-m-d H:i:s', strtotime($request->get('arrival_datetime'))),
            'total_amount' => $request->get('total_amount'),
            'booked_by' => $request->get('user_id'),
            'from' => $request->get('from'),
            'to' => $request->get('to'),
            'work_details' => $request->get('work_details'),
            'booking_note' => $request->get('booking_note'),
            'payment_type' => $request->get('payment_type'),
            'first_approval_status' => 'Pending',
            'second_approval_status' => 'Pending',
            'third_approval_status' => 'Pending',
            'fourth_approval_status' => 'Pending',
            'status' => 'Pending',
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => $request->get('user_id')
        ];

        if (!empty($ticket_image_file))
            $update_arr['ticket_image'] = $ticket_image_file;


        Travel::where('id', $request->get('id'))->update($update_arr);

        return response()->json(['status' => true, 'msg' => "Travel successfully updated.", 'data' => []]);
    }

    public function get_travel_details(Request $request)
    {

        $validator_normal = Validator::make($request->all(), [
            'user_id' => 'required',
            'id' => 'required'
        ]);

        if ($validator_normal->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $travel = Travel::where('id', $request->get('id'))->with(['company', 'project', 'user'])->get();

        $travel_list = [];

        foreach ($travel as $value) {
            $travel_list['travel_via_original'] = config::get('constants.TRAVEL_VIA')[$value->travel_via];
            $travel_list['travel_via'] = $value->travel_via;
            $travel_list['payment_type_original'] = config::get('constants.PAYMENT_TYPE')[$value->payment_type];
            $travel_list['payment_type'] = $value->payment_type;
            $travel_list['departure_datetime'] = date('d-m-Y H:i:s', strtotime($value->departure_datetime));
            $travel_list['arrival_datetime'] = date('d-m-Y H:i:s', strtotime($value->arrival_datetime));

            $traveler_ids_arr = explode(',', $value->traveler_ids);
            $traveler_user_detail = [];
            foreach ($traveler_ids_arr as $k => $id) {

                $traveler_details = User::where('id', $id)->get(['id', 'name']);

                if ($traveler_details->count() == 0) {
                    continue;
                }

                $traveler_user_detail[$k] = $traveler_details[0];
            }

            //$traveler_list = User::whereIn('id', explode(',', $value->traveler_ids))->pluck('name')->toArray();
            $travel_list['traveler_ids'] = $traveler_user_detail;

            $travel_list['company_name'] = $value->company['company_name'];
            $travel_list['project_id'] = $value->project->id;
            if ($value->project->id == config('constants.OTHER_PROJECT_ID')) {


                $travel_list['project_name'] = $value->project->project_name;
                $travel_list['other_project_details'] = $value->other_project_details;
            } else {


                $travel_list['project_name'] = $value->project->project_name;
            }
            if (!empty($value->ticket_image))
                $travel_list['ticket_image'] = asset('storage/' . str_replace('public/', '', $value->ticket_image));

            if (!empty($value->user->profile_image))
                $travel_list['booked_by_image'] = asset('storage/' . str_replace('public/', '', $value->user->profile_image));

                $travel_list['first_approval_status'] = $value->first_approval_status;
                $travel_list['second_approval_status'] = $value->second_approval_status;
                $travel_list['third_approval_status'] = $value->third_approval_status;
                $travel_list['fourth_approval_status'] = $value->fourth_approval_status;
            $travel_list['booked_by_name'] = $value->user->name;
            $travel_list['travel_company'] = $value->travel_company;
            $travel_list['ticket_no'] = $value->ticket_no;
            $travel_list['total_amount'] = $value->total_amount;
            $travel_list['from'] = $value->from;
            $travel_list['to'] = $value->to;
            $travel_list['work_details'] = $value->work_details;
            $travel_list['booking_note'] = $value->booking_note;
            $travel_list['status'] = $value->status;
        }

        $this->data['travel_list'] = $travel_list;

        if ($travel_list) {
            return response()->json(['status' => true, 'data' => $this->data]);
        } else {
            return response()->json(['status' => false]);
        }
    }

    public function get_all_travel(Request $request)
    {

        $validator_normal = Validator::make($request->all(), [
            'user_id' => 'required'
        ]);

        if ($validator_normal->fails()) {
            return response()->json(['status' => false]);
        }

        $travel = Travel::with(['company', 'project', 'user'])->get();

        $travel_list = [];

        foreach ($travel as $key => $value) {
            $travel_list[$key]['id'] = $value->id;
            $travel_list[$key]['travel_via'] = config::get('constants.TRAVEL_VIA')[$value->travel_via];
            $travel_list[$key]['payment_type'] = config::get('constants.PAYMENT_TYPE')[$value->payment_type];
            $travel_list[$key]['departure_datetime'] = date('d-m-Y H:i:s', strtotime($value->departure_datetime));
            $travel_list[$key]['arrival_datetime'] = date('d-m-Y H:i:s', strtotime($value->arrival_datetime));
            $traveler_list = User::whereIn('id', explode(',', $value->traveler_ids))->pluck('name')->toArray();
            $travel_list[$key]['traveler_ids'] = implode(', ', $traveler_list);

            $travel_list[$key]['company_name'] = $value->company['company_name'];
            if ($value->project->id == config('constants.OTHER_PROJECT_ID'))
                $travel_list[$key]['project_name'] = $value->other_project_details;
            else
                $travel_list[$key]['project_name'] = $value->project->project_name;

            if (!empty($value->ticket_image))
                $travel_list[$key]['ticket_image'] = asset('storage/' . str_replace('public/', '', $value->ticket_image));

            if (!empty($value->user->profile_image))
                $travel_list[$key]['booked_by_image'] = asset('storage/' . str_replace('public/', '', $value->user->profile_image));

            $travel_list[$key]['booked_by_name'] = $value->user->name;
            $travel_list[$key]['travel_company'] = $value->travel_company;
            $travel_list[$key]['ticket_no'] = $value->ticket_no;
            $travel_list[$key]['total_amount'] = $value->total_amount;
            $travel_list[$key]['from'] = $value->from;
            $travel_list[$key]['to'] = $value->to;
            $travel_list[$key]['work_details'] = $value->work_details;
            $travel_list[$key]['booking_note'] = $value->booking_note;
            $travel_list[$key]['status'] = $value->status;
        }

        $this->data['travel_list'] = $travel_list;

        if ($travel_list) {
            return response()->json(['status' => true, 'data' => $this->data]);
        } else {
            return response()->json(['status' => false]);
        }
    }
}
