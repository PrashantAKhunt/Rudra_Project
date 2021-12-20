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
use App\HotelBooking;
use App\Projects;
use Illuminate\Support\Facades\Mail;
use App\Mail\Mails;

class HotelController extends Controller
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

    public function add_hotel(Request $request)
    {
        $validator_normal = Validator::make($request->all(), [
            'user_id' => 'required',
            'stay_user_ids' => 'required',
            'hotel_name' => 'required',
            'booking_no' => 'required',
            'booking_image' => 'required',
            'check_in_datetime' => 'required',
            'check_out_datetime' => 'required',
            'total_amount' => 'required',
            'place' => 'required',
            'payment_type' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        //upload user booking image
        $booking_image_file = '';
        if ($request->hasFile('booking_image')) {
            $booking_image = $request->file('booking_image');
            $file_path = $booking_image->store('public/booking_image');
            if ($file_path) {
                $booking_image_file = $file_path;
            }
        }

        $userDetail = User::select('id', 'name')->where('id', $request->get('user_id'))->with('employee')->get()->first();
        $companyId = $userDetail->employee->company_id;

        $tarvel_arr = [
            'company_id' => $companyId,
            'project_id' => $request->get('project_id'),
            'other_project_details' => $request->input('other_project'),
            'stay_user_ids' => $request->get('stay_user_ids'),
            'hotel_name' => $request->get('hotel_name'),
            'booking_no' => $request->get('booking_no'),
            'booking_image' => !empty($booking_image_file) ? $booking_image_file : NULL,
            'check_in_datetime' => date('Y-m-d H:i:s', strtotime($request->get('check_in_datetime'))),
            'check_out_datetime' => date('Y-m-d H:i:s', strtotime($request->get('check_out_datetime'))),
            'total_amount' => $request->get('total_amount'),
            'booked_by' => $request->get('user_id'),
            'place' => $request->get('place'),
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
        HotelBooking::insert($tarvel_arr);

        $mail_data = [];
        $mail_data['name'] = $userDetail->name;
        $mail_data['text'] = "Hotel Expence is added with below details, Please check";
        $mail_data['to_email'] = user::where('status', 'Enabled')->where('role', config('constants.ASSISTANT'))->pluck('email')->toArray();
        $stayedUser = user::whereIn('id', [$request->get('stay_user_ids')])->pluck('name')->toArray();
        $mail_data['stayed_user'] = implode(", ", $stayedUser);
        $mail_data['hotel_name'] = $request->get('hotel_name');
        $mail_data['booking_no'] = $request->get('booking_no');
        $mail_data['check_in_datetime'] =  date('Y-m-d H:i:s', strtotime($request->get('check_in_datetime')));
        $mail_data['check_out_datetime'] = date('Y-m-d H:i:s', strtotime($request->get('check_out_datetime')));
        $mail_data['total_amount'] = $request->get('total_amount');
        $mail_data['place'] = $request->get('place');

        $this->common_task->hotelEmail($mail_data);

        return response()->json(['status' => true, 'msg' => "Hotel booking successfully added.", 'data' => []]);
    }

    public function update_hotel(Request $request)
    {
        $validator_normal = Validator::make($request->all(), [
            'id' => 'required',
            'user_id' => 'required',
            'stay_user_ids' => 'required',
            'hotel_name' => 'required',
            'booking_no' => 'required',
            'check_in_datetime' => 'required',
            'check_out_datetime' => 'required',
            'total_amount' => 'required',
            'place' => 'required',
            'payment_type' => 'required'
        ]);

        if ($validator_normal->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        //upload user booking image
        $booking_image_file = '';
        if ($request->hasFile('booking_image')) {
            $booking_image = $request->file('booking_image');
            $file_path = $booking_image->store('public/booking_image');
            if ($file_path) {
                $booking_image_file = $file_path;
            }
        }

        $update_arr = [
            'project_id' => $request->get('project_id'),
            'stay_user_ids' => $request->get('stay_user_ids'),
            'hotel_name' => $request->get('hotel_name'),
            'booking_no' => $request->get('booking_no'),
            //  'booking_image' => !empty($booking_image_file) ? $booking_image_file : NULL,
            'check_in_datetime' => date('Y-m-d H:i:s', strtotime($request->get('check_in_datetime'))),
            'check_out_datetime' => date('Y-m-d H:i:s', strtotime($request->get('check_out_datetime'))),
            'total_amount' => $request->get('total_amount'),
            'booked_by' => $request->get('user_id'),
            'place' => $request->get('place'),
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

        if (!empty($booking_image_file))
            $update_arr['booking_image'] = $booking_image_file;


        HotelBooking::where('id', $request->get('id'))->update($update_arr);

        return response()->json(['status' => true, 'msg' => "Hotel booking successfully updated.", 'data' => []]);
    }

    public function get_hotel_details(Request $request)
    {

        $validator_normal = Validator::make($request->all(), ['user_id' => 'required', 'id' => 'required']);

        if ($validator_normal->fails()) {
            return response()->json(['status' => false]);
        }

        $hotel = HotelBooking::where('id', $request->get('id'))->with(['company', 'project', 'user'])->get();

        $hotel_list = [];

        foreach ($hotel as $value) {
            $hotel_list['payment_type'] = $value->payment_type;
            $hotel_list['payment_type_original'] = config::get('constants.PAYMENT_TYPE')[$value->payment_type];
            $hotel_list['check_in_datetime'] = date('d-m-Y H:i:s', strtotime($value->check_in_datetime));
            $hotel_list['check_out_datetime'] = date('d-m-Y H:i:s', strtotime($value->check_out_datetime));


            $stay_user_ids_arr = explode(',', $value->stay_user_ids);
            $stay_user_list = [];
            foreach ($stay_user_ids_arr as $k => $id) {

                $stay_user_details = User::where('id', $id)->get(['id', 'name']);

                if ($stay_user_details->count() == 0) {
                    continue;
                }

                $stay_user_list[$k] = $stay_user_details[0];
            }

            $hotel_list['stay_user_list'] = $stay_user_list;

            // $stayed_list = User::whereIn('id',explode(',',$value->stay_user_ids))->pluck('name')->toArray();
            // $hotel_list['stay_user_ids'] = implode(', ', $stayed_list);

            $hotel_list['company_name'] = $value->company['company_name'];
            $hotel_list['project_id'] = $value->project->id;
            if ($value->project->id == config('constants.OTHER_PROJECT_ID')) {


                $hotel_list['project_name'] = $value->project->project_name;
                $hotel_list['other_project_details'] = $value->other_project_details;
            } else {


                $hotel_list['project_name'] = $value->project->project_name;
            }
            if (!empty($value->booking_image))
                $hotel_list['booking_image'] = asset('storage/' . str_replace('public/', '', $value->booking_image));

            if (!empty($value->user->profile_image))
                $hotel_list['booked_by_image'] = asset('storage/' . str_replace('public/', '', $value->user->profile_image));

            $hotel_list['first_approval_status'] = $value->first_approval_status;
            $hotel_list['second_approval_status'] = $value->second_approval_status;
            $hotel_list['third_approval_status'] = $value->third_approval_status;
            $hotel_list['fourth_approval_status'] = $value->fourth_approval_status;
            $hotel_list['booked_by_name'] = $value->user->name;
            $hotel_list['hotel_name'] = $value->hotel_name;
            $hotel_list['booking_no'] = $value->booking_no;
            $hotel_list['total_amount'] = $value->total_amount;
            $hotel_list['place'] = $value->place;
            $hotel_list['work_details'] = $value->work_details;
            $hotel_list['booking_note'] = $value->booking_note;
            $hotel_list['status'] = $value->status;
        }

        $this->data['hotel_list'] = $hotel_list;

        if ($hotel_list) {
            return response()->json(['status' => true, 'data' => $this->data]);
        } else {
            return response()->json(['status' => false]);
        }
    }

    public function get_all_hotel(Request $request)
    {

        $validator_normal = Validator::make($request->all(), ['user_id' => 'required']);

        if ($validator_normal->fails()) {
            return response()->json(['status' => false]);
        }

        $hotel = HotelBooking::with(['company', 'project', 'user'])->get();

        $hotel_list = [];

        foreach ($hotel as $key => $value) {
            $hotel_list[$key]['id'] = $value->id;
            $hotel_list[$key]['payment_type'] = config::get('constants.PAYMENT_TYPE')[$value->payment_type];
            $hotel_list[$key]['check_in_datetime'] = date('d-m-Y H:i:s', strtotime($value->check_in_datetime));
            $hotel_list[$key]['check_out_datetime'] = date('d-m-Y H:i:s', strtotime($value->check_out_datetime));

            $stayed_list = User::whereIn('id', explode(',', $value->stay_user_ids))->pluck('name')->toArray();
            $hotel_list[$key]['stay_user_ids'] = implode(', ', $stayed_list);

            $hotel_list[$key]['company_name'] = $value->company['company_name'];
            if ($value->project->id == config('constants.OTHER_PROJECT_ID'))
                $hotel_list[$key]['project_name'] = $value->other_project_details;
            else
                $hotel_list[$key]['project_name'] = $value->project->project_name;

            if (!empty($value->booking_image))
                $hotel_list[$key]['booking_image'] = asset('storage/' . str_replace('public/', '', $value->booking_image));

            if (!empty($value->user->profile_image))
                $hotel_list[$key]['booked_by_image'] = asset('storage/' . str_replace('public/', '', $value->user->profile_image));

            $hotel_list[$key]['booked_by_name'] = $value->user->name;
            $hotel_list[$key]['hotel_name'] = $value->hotel_name;
            $hotel_list[$key]['booking_no'] = $value->booking_no;
            $hotel_list[$key]['total_amount'] = $value->total_amount;
            $hotel_list[$key]['place'] = $value->place;
            $hotel_list[$key]['work_details'] = $value->work_details;
            $hotel_list[$key]['booking_note'] = $value->booking_note;
            $hotel_list[$key]['status'] = $value->status;
        }

        $this->data['hotel_list'] = $hotel_list;

        if ($hotel_list) {
            return response()->json(['status' => true, 'data' => $this->data]);
        } else {
            return response()->json(['status' => false]);
        }
    }
}
