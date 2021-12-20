<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Lib\CommonTask;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;
use App\Lib\NotificationTask;
use DB;
use App\Role_module;
use App\Email_format;
use App\User;
use App\HotelBooking;
use App\Projects;
use Illuminate\Support\Facades\Mail;
use App\Mail\Mails;
use App\Companies;

class HotelController extends Controller {

    public $data;
    public $common_task;
    private $module_id = 42;
    private $notification_task;

    public function __construct() {
        $this->data['module_title'] = "Hotel";
        $this->data['module_link'] = "admin.hotel";
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();

        $this->super_admin = \App\User::where('role', 1)->first();
    }

    public function index() {
        $this->data['page_title'] = "Hotel";

        $access_level = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => 42])->get()->first();
        $this->data['role'] = !empty($access_level) ? explode(',', $access_level->access_level) : array();
                
        $this->data['hotel_list'] = HotelBooking::whereRaw("FIND_IN_SET(".Auth::user()->id.",stay_user_ids)")
                                    ->join('users','users.id','=','hotel_booking.booked_by')
                                    ->orWhere('booked_by', Auth::user()->id)
                                    ->orderBy('id', 'desc')->get(['users.name','hotel_booking.*']);
        
        return view('admin.hotel.index', $this->data);
    }

    public function all_hotel() {

        $this->data['page_title'] = "Approve Hotel Expense";
        $access_level = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => 41])->get()->first();
        $this->data['access_rule'] = '';
        if (!empty($access_level)) {
            $this->data['access_rule'] = $access_level->access_level;
        }

        $Result = DB::table('hotel_booking')
                ->select('hotel_booking.*','company.company_name','project.project_name','users.name')
                ->where('hotel_booking.status','Pending')
                ->join('users','users.id','=','hotel_booking.booked_by')
                ->join('company','company.id','=','hotel_booking.company_id')
                ->join('project','project.id','=','hotel_booking.project_id');
        if (Auth::user()->role == config('constants.ASSISTANT')) {
            $Result->where('hotel_booking.first_approval_status', 'Pending');
        } elseif (Auth::user()->role == config('constants.Admin')) {
            $Result->where('hotel_booking.first_approval_status', 'Approved')
                    ->where('hotel_booking.second_approval_status', 'Pending');
        } elseif (Auth::user()->role == config('constants.SuperUser')) {
            $Result->where('hotel_booking.first_approval_status', 'Approved')
                    ->where('hotel_booking.second_approval_status', 'Approved')
                    ->where('hotel_booking.third_approval_status', 'Pending');
        } elseif (Auth::user()->role == config('constants.ACCOUNT_ROLE')) {
            $Result->where('hotel_booking.first_approval_status', 'Approved')
                    ->where('hotel_booking.second_approval_status', 'Approved')
                    ->where('hotel_booking.third_approval_status', 'Approved')
                    ->where('hotel_booking.fourth_approval_status', 'Pending');
        } else {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You can not access this module.');
        }

        $this->data['hotel_list'] = $Result->get();
        $Results = DB::table('hotel_booking')
                
                ->select('hotel_booking.*','company.company_name','project.project_name','users.name')
                ->join('users','users.id','=','hotel_booking.booked_by')
                ->join('company','company.id','=','hotel_booking.company_id')
                ->join('project','project.id','=','hotel_booking.project_id');

        $all_hotel_expense_list = $Results->get();
        
        foreach ($all_hotel_expense_list as $key => $value) {
            $stayedUser = user::whereIn('id',explode(',',$value->stay_user_ids))->pluck('name')->toArray();
            $value->stay_user_ids = implode(", ", $stayedUser);            
        }

        $this->data['all_hotel_expense_list'] = $all_hotel_expense_list;

        return view('admin.hotel.all_hotel', $this->data);
    }

    public function add_hotel() {
        $this->data['page_title'] = "Hotel detail";        
        $this->data['payment_type'] = config::get('constants.PAYMENT_TYPE');
        $this->data['companies'] = Companies::select('id', 'company_name')->get();
        $this->data['stay_user_ids'] = User::where('status', 'Enabled')->with(['employee'=> function(){
            return $query->where('company_id',$userDetail->employee->company_id);
        }])->pluck('name', 'id');

        return view('admin.hotel.add_hotel', $this->data);
    }

    public function save_hotel(Request $request) {
        $validator_normal = Validator::make($request->all(), [
                    'company_id' => 'required',
                    'project_id' => 'required',
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
            return redirect()->route('admin.add_hotel')->with('error', 'Please follow validation rules.');
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

        $tarvel_arr = [
            'company_id' => $request->get('company_id'),
            'project_id' => $request->get('project_id'),
            'other_project_details' => $request->get('other_project_details'),
            'stay_user_ids' => implode(",", $request->get('stay_user_ids')),
            'hotel_name' => $request->get('hotel_name'),
            'booking_no' => $request->get('booking_no'),
            'booking_image' => !empty($booking_image_file) ? $booking_image_file : NULL,
            'check_in_datetime' => date('Y-m-d H:i:s', strtotime($request->get('check_in_datetime'))),
            'check_out_datetime' => date('Y-m-d H:i:s', strtotime($request->get('check_out_datetime'))),
            'total_amount' => $request->get('total_amount'),
            'booked_by' => Auth::user()->id,
            'place' => $request->get('place'),
            'work_details' => $request->get('work_details'),
            'booking_note' => $request->get('booking_note'),
            'payment_type' => $request->get('payment_type'),
            'status' => 'Pending',
            'created_at' => date('Y-m-d H:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id,
        ];
        HotelBooking::insert($tarvel_arr);

        $mail_data = [];
        $mail_data['name'] = Auth::user()->name;
        $mail_data['text'] = "Hotel Expence is added with below details, Please check";
        $mail_data['to_email'] = user::where('status', 'Enabled')->where('role', config('constants.ASSISTANT'))->pluck('email')->toArray();
        $stayedUser = user::whereIn('id',[$request->get('stay_user_ids')])->pluck('name')->toArray();
        $mail_data['stayed_user'] = implode(", ", $stayedUser);
        $mail_data['hotel_name'] = $request->get('hotel_name');
        $mail_data['booking_no'] = $request->get('booking_no');
        $mail_data['check_in_datetime'] =  date('Y-m-d H:i:s', strtotime($request->get('check_in_datetime')));
        $mail_data['check_out_datetime'] = date('Y-m-d H:i:s', strtotime($request->get('check_out_datetime')));
        $mail_data['total_amount'] = $request->get('total_amount');
        $mail_data['place'] = $request->get('place');

        $this->common_task->hotelEmail($mail_data);
        $email_user_ids = user::where('status', 'Enabled')->where('role', config('constants.ASSISTANT'))->pluck('id')->toArray();
        $message = "Hotel expense request is added. Please check in your account for more details.";
        $this->notification_task->travelhotelExpenceNotify($email_user_ids, 'Hotel', $message);

        return redirect()->route('admin.hotel')->with('success', 'Hotel expence detail successfully submitted.');
    }
    
    public function edit_hotel($id) {
        $this->data['page_title']="Edit Hotel Details";
        
        //get hotel data
        $this->data['hotel'] = HotelBooking::where('id',$id)->get()->first();
        $this->data['payment_type'] = config::get('constants.PAYMENT_TYPE');
        $this->data['companies'] = Companies::select('id', 'company_name')->get();
        $this->data['stayed_ids'] = User::where('status', 'Enabled')->with(['employee'=> function(){
            return $query->where('company_id',$userDetail->employee->company_id);
        }])->pluck('name', 'id');

        return view('admin.hotel.edit_hotel', $this->data);
    }
    
    public function update_hotel(Request $request) {
        $validator_normal = Validator::make($request->all(), [
                    'company_id' => 'required',
                    'project_id' => 'required',
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
            return redirect()->route('admin.edit_hotel')->with('error', 'Please follow validation rules.');
        }

        //upload user booking image
        
        if ($request->hasFile('booking_image')) {
            $booking_image = $request->file('booking_image');
            $file_path = $booking_image->store('public/booking_image');
            if ($file_path) {
                $booking_image_file = $file_path;
            }
        }
        
        $update_arr=[
            'company_id' => $request->get('company_id'),
            'project_id' => $request->get('project_id'),
            'other_project_details' => $request->get('other_project_details'),
            'stay_user_ids' => implode(",", $request->get('stay_user_ids')),
            'hotel_name' => $request->get('hotel_name'),
            'booking_no' => $request->get('booking_no'),            
            'check_in_datetime' => date('Y-m-d H:i:s', strtotime($request->get('check_in_datetime'))),
            'check_out_datetime' => date('Y-m-d H:i:s', strtotime($request->get('check_out_datetime'))),
            'total_amount' => $request->get('total_amount'),
            'booked_by' => Auth::user()->id,
            'place' => $request->get('place'),
            'work_details' => $request->get('work_details'),
            'booking_note' => $request->get('booking_note'),
            'payment_type' => $request->get('payment_type'),            
            'updated_at'=>date('Y-m-d H:i:s'),
            'updated_ip'=>$request->ip(),
            'updated_by'=> Auth::user()->id
        ];

        if(!empty($booking_image_file)){
            $update_arr['booking_image'] = $booking_image_file;
        }
        HotelBooking::where('id',$request->get('id'))->update($update_arr);

        return redirect()->route('admin.hotel')->with('success','Hotel expence details updated successfully.');
    }

    public function get_hotel_detail(Request $request) {
        
        $validator_normal = Validator::make($request->all(), ['id' => 'required']);

        if ($validator_normal->fails()) {
            return response()->json(['status'=>false]);
        }

        $hotel_list = HotelBooking::where('id',$request->get('id'))->get()->first();
        
        $hotel_list->payment_type = config::get('constants.PAYMENT_TYPE')[$hotel_list->payment_type];
        $hotel_list->check_in_datetime = date('d-m-Y H:i:s',strtotime($hotel_list->check_in_datetime));
        $hotel_list->check_out_datetime = date('d-m-Y H:i:s',strtotime($hotel_list->check_out_datetime));

        $stay_user_list = User::whereIn('id',explode(',',$hotel_list->stay_user_ids))->pluck('name')->toArray();

        $this->data['stay_user_list'] = implode(', ', $stay_user_list);
        $this->data['hotel_list'] = $hotel_list;

        if($hotel_list){
            return response()->json(['status'=>true,'data'=> $this->data]);
        }else{
            return response()->json(['status'=>false]);
        }
    }


    public function approve_hotel($id, $status, Request $request) {
        $hotel = HotelBooking::where('id',$id)->get()->first();            
        $mail_data = [];
        $message = "Hotel expense request is added. Please check in your account for more details.";

        if(Auth::user()->role==config('constants.ASSISTANT')){
            $update_arr=[
                'first_approval_status'=>$status,
                'first_approval_id'=>Auth::user()->id,
                'updated_at'=>date('Y-m-d H:i:s'),
                'updated_ip'=>$request->ip(),
                'updated_by'=>Auth::user()->id
            ];
            $toEmail = user::where('status', 'Enabled')->where('role', config('constants.Admin'))->pluck('email')->toArray();
            $mail_data['text'] = "Hotel Expence is added with below details, Please check";
            $email_user_ids = user::where('status', 'Enabled')->where('role', config('constants.Admin'))->pluck('id')->toArray();

        }elseif(Auth::user()->role==config('constants.Admin')){
            $update_arr=[
                'second_approval_status'=>$status,
                'second_approval_id'=>Auth::user()->id,
                'updated_at'=>date('Y-m-d H:i:s'),
                'updated_ip'=>$request->ip(),
                'updated_by'=>Auth::user()->id
            ];
            $toEmail = user::where('status', 'Enabled')->where('role', config('constants.SuperUser'))->pluck('email')->toArray();
            $mail_data['text'] = "Hotel Expence is added with below details, Please check";
            $email_user_ids = user::where('status', 'Enabled')->where('role', config('constants.SuperUser'))->pluck('id')->toArray();

        }elseif(Auth::user()->role==config('constants.SuperUser')){
            $update_arr=[
                'third_approval_status'=>$status,
                'third_approval_id'=>Auth::user()->id,
                'updated_at'=>date('Y-m-d H:i:s'),
                'updated_ip'=>$request->ip(),
                'updated_by'=>Auth::user()->id
            ];
            $toEmail = user::where('status', 'Enabled')->where('role', config('constants.ACCOUNT_ROLE'))->pluck('email')->toArray();
            $mail_data['text'] = "Hotel Expence is added with below details, Please check";
            $email_user_ids = user::where('status', 'Enabled')->where('role', config('constants.ACCOUNT_ROLE'))->pluck('id')->toArray();

        }elseif(Auth::user()->role==config('constants.ACCOUNT_ROLE')){
            $update_arr=[
                'fourth_approval_status'=>$status,
                'fourth_approval_id'=>Auth::user()->id,
                'updated_at'=>date('Y-m-d H:i:s'),
                'updated_ip'=>$request->ip(),
                'updated_by'=>Auth::user()->id,
                'status'=>$status
            ];
            $toEmail = user::whereIn('id',[$hotel->stay_user_ids])->pluck('email')->toArray();
            $mail_data['text'] = "Your hotel Expence request is approved with below details";
            $email_user_ids = explode(',',$hotel->traveler_ids);
            $message = "Your hotel expense request is approved.";
        } else {
            return redirect()->route('admin.all_hotel')->with('error', 'Access denied. You are not allowed to access this module.');
        }

        HotelBooking::where('id',$id)->update($update_arr);
        
        $mail_data['name'] = Auth::user()->name;
        $mail_data['to_email'] = $toEmail;
        $stayedUser = user::whereIn('id',[$hotel->stay_user_ids])->pluck('name')->toArray();
        $mail_data['stayed_user'] = implode(", ", $stayedUser);
        $mail_data['hotel_name'] = $hotel->hotel_name;
        $mail_data['booking_no'] = $hotel->booking_no;
        $mail_data['check_in_datetime'] =  date('Y-m-d H:i:s', strtotime($hotel->check_in_datetime));
        $mail_data['check_out_datetime'] = date('Y-m-d H:i:s', strtotime($hotel->check_out_datetime));
        $mail_data['total_amount'] = $hotel->total_amount;
        $mail_data['place'] = $hotel->place;

        $this->common_task->hotelEmail($mail_data);        
        $this->notification_task->travelhotelExpenceNotify($email_user_ids, 'Hotel', $message);

        return redirect()->route('admin.all_hotel')->with('success', 'Hotel expence successfully approved.');
    }
    
    public function reject_hotel_expence(Request $request) {
        $status = "Rejected";
        if(Auth::user()->role==config('constants.ASSISTANT')){
            $update_arr=[
                'first_approval_status'=>$status,
                'first_approval_id'=>Auth::user()->id,
                'reject_details' => $request->get('reject_details'),
                'updated_at'=>date('Y-m-d H:i:s'),
                'updated_ip'=>$request->ip(),
                'updated_by'=>Auth::user()->id,
                'status'=>$status
            ];
        }elseif(Auth::user()->role==config('constants.Admin')){
            $update_arr=[
                'second_approval_status'=>$status,
                'second_approval_id'=>Auth::user()->id,
                'reject_details' => $request->get('reject_details'),
                'updated_at'=>date('Y-m-d H:i:s'),
                'updated_ip'=>$request->ip(),
                'updated_by'=>Auth::user()->id,
                'status'=>$status
            ];
        }elseif(Auth::user()->role==config('constants.SuperUser')){
            $update_arr=[
                'third_approval_status'=>$status,
                'third_approval_id'=>Auth::user()->id,
                'reject_details' => $request->get('reject_details'),
                'updated_at'=>date('Y-m-d H:i:s'),
                'updated_ip'=>$request->ip(),
                'updated_by'=>Auth::user()->id,
                'status'=>$status
            ];
        }elseif(Auth::user()->role==config('constants.ACCOUNT_ROLE')){
            $update_arr=[
                'fourth_approval_status'=>$status,
                'fourth_approval_id'=>Auth::user()->id,
                'reject_details' => $request->get('reject_details'),
                'updated_at'=>date('Y-m-d H:i:s'),
                'updated_ip'=>$request->ip(),
                'updated_by'=>Auth::user()->id,
                'status'=>$status
            ];            
        } else {
            return redirect()->route('admin.all_hotel')->with('error', 'Access denied. You are not allowed to access this module.');
        }

        HotelBooking::where('id',$request->get('hotel_id'))->update($update_arr);

        $hotel = HotelBooking::where('id',$request->get('hotel_id'))->get()->first();
        $mail_data = [];            
        $mail_data['text'] = "Your hotel Expence request is rejected with below details";
        $mail_data['name'] = Auth::user()->name;
        $toEmail = user::whereIn('id',[$hotel->stay_user_ids])->pluck('email')->toArray();
        $mail_data['to_email'] = $toEmail;
        $stayedUser = user::whereIn('id',[$hotel->stay_user_ids])->pluck('name')->toArray();
        $mail_data['stayed_user'] = implode(", ", $stayedUser);
        $mail_data['hotel_name'] = $hotel->hotel_name;
        $mail_data['booking_no'] = $hotel->booking_no;
        $mail_data['check_in_datetime'] =  date('Y-m-d H:i:s', strtotime($hotel->check_in_datetime));
        $mail_data['check_out_datetime'] = date('Y-m-d H:i:s', strtotime($hotel->check_out_datetime));
        $mail_data['total_amount'] = $hotel->total_amount;
        $mail_data['place'] = $hotel->place;

        $this->common_task->hotelEmail($mail_data);
        
        $email_user_ids = explode(',', $hotel->stay_user_ids);
        $message = "Hotel expense request is rejected. Please check in your account for more details.";
        $this->notification_task->travelhotelExpenceNotify($email_user_ids, 'Hotel', $message);

        return redirect()->route('admin.all_hotel')->with('success', 'Hotel expence successfully rejected.');
    }

    public function cancel_hotel($id, Request $request) {
        
        $update_arr=[
            'status'=>'Canceled',
            'updated_at'=>date('Y-m-d H:i:s'),
            'updated_ip'=>$request->ip(),
            'updated_by'=>Auth::user()->id
        ];
        
        HotelBooking::where('id',$id)->update($update_arr);

        return redirect()->route('admin.hotel')->with('success', 'Hotel expence successfully canceled.');
    }

}
