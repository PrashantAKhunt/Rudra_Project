<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Lib\CommonTask;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use App\Lib\NotificationTask;
//use DB;
use App\Role_module;
use App\Email_format;
use App\User;
use App\Travel;
use App\Projects;
use Illuminate\Support\Facades\Mail;
use App\Mail\Mails;
use App\Travel_booking_files;
use App\Companies;
use App\PaymentCard;
use App\Travel_info;
use App\Travel_option;


class TravelController extends Controller
{

    public $data;
    public $common_task;
    private $module_id = 41;
    private $notification_task;

    public function __construct()
    {
        $this->data['module_title'] = "My Travel";
        $this->data['module_link'] = "admin.travel_requests";
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();

        $this->super_admin = \App\User::where('role', 1)->first();
    }

    public function index()  //travel page
    {
        $this->data['page_title'] = "Travel";

        $access_level = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => 41])->get()->first();

        $this->data['role'] = !empty($access_level) ? explode(',', $access_level->access_level) : array();

        $this->data['travel_list'] = Travel::whereRaw("FIND_IN_SET(" . Auth::user()->id . ",traveler_ids)")->orWhere('booked_by', Auth::user()->id)->orderBy('id', 'desc')->get();

        return view('admin.travel.index', $this->data);
    }

    public function travel_requests()   //travel requests page
    {

        if (Auth::user()->role == config('constants.ASSISTANT')  || Auth::user()->role == config('constants.SuperUser')) {

            $this->data['page_title'] = "Travel Request";

            $this->data['travel_list'] = Travel::join('users', 'travel.booked_by', '=', 'users.id')
                ->whereNotIn('travel.status', ['Confirmed', 'Rejected', 'Canceled'])->orderBy('travel.id', 'desc')
                ->get(['travel.*', 'users.name']);


            $this->data['all_travel_list'] = $all_travel_list = Travel::join('users', 'travel.booked_by', '=', 'users.id')
                ->leftjoin('company', 'company.id', '=', 'travel.company_id')
                ->leftjoin('project', 'project.id', '=', 'travel.project_id')
                ->get([
                    'travel.request_no','travel.travel_via','travel.flight_trip','travel.reject_details',
                    'travel.id', 'travel.traveler_ids', 'travel.other_project_details', 'travel.work_details', 'travel.status',
                    'users.name as user_name', 'company.company_name',
                    'project.project_name'
                ]);

            foreach ($all_travel_list as $key => $value) {

                if ($value->card_number) {

                    $value->card_number = 'XXXXXXXXXXXX' . substr($value->card_number, -4);
                }

                if ($value->travel_image) {

                    $value->travel_image = asset('storage/' . str_replace('public/', '', $value->travel_image));
                } else {

                    $value->travel_image = "";
                }


                $travelers = user::whereIn('id', explode(',', $value->traveler_ids))->pluck('name')->toArray();
                $value->traveler_ids = implode(", ", $travelers);
            }



            return view('admin.travel.travel_request', $this->data);
        }



        return redirect()->route('admin.dashboard')->with('error', 'Access Denied');
    }

    public function add_travel()   //add travel
    {
        $this->data['page_title'] = "Travel detail";
        $this->data['travel_via'] = config::get('constants.TRAVEL_VIA');

        $this->data['companies'] = Companies::select('id', 'company_name')->get();
        $this->data['traveler_ids'] = $traveler_ids = User::where('status', 'Enabled')->with(['employee' => function () {
            return $query->where('company_id', $userDetail->employee->company_id);
        }])->pluck('name', 'id');

        return view('admin.travel.add_travel', $this->data);
    }

    public function save_travel(Request $request)   //save travel option
    {
        $validator_normal = Validator::make($request->all(), [
            'company_id' => 'required',
            'project_id' => 'required',
            'travel_via' => 'required',
            'traveler_ids' => 'required',
            'departure_datetime' => 'required',
            'arrival_datetime' => 'required',
            'from' => 'required',
            'to' => 'required',
        ]);

        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_travel')->with('error', 'Please follow validation rules.');
        }

        $from = $request->from;
        $departure_datetime = $request->departure_datetime;
        $arrival_datetime = $request->arrival_datetime;
        $from = $request->from;
        $to = $request->to;
        $details = $request->details;


        $tarvel_arr = [
            'company_id' => $request->get('company_id'),
            'project_id' => $request->get('project_id'),
            'other_project_details' => $request->get('other_project_details'),
            'travel_via' => $request->get('travel_via'),
            'flight_trip' => $request->get('flight_trip'),
            'traveler_ids' => implode(",", $request->get('traveler_ids')),
            'departure_datetime' => date('Y-m-d H:i:s', strtotime($departure_datetime[0])),
            'arrival_datetime' => date('Y-m-d H:i:s', strtotime($arrival_datetime[0])),
            'booked_by' => Auth::user()->id,
            'from' => $from[0],
            'to' => $to[0],
            'work_details' => $details[0],
            'status' => 'Pending',
            'created_at' => date('Y-m-d H:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id
        ];

        $last_increId = Travel::insertGetId($tarvel_arr);

        $request_no = 1000 + $last_increId;
        $travel_arr = [
            'request_no' => $request_no,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id
        ];
        Travel::where('id', $last_increId)->update($travel_arr);

        /* Multi Insert for flights Options */

        for ($count = 0; $count < count($from); $count++) {

            $data = array(
                'travel_id' => $last_increId,
                'departure_datetime' => date('Y-m-d H:i:s', strtotime($departure_datetime[$count])),
                'arrival_datetime' => date('Y-m-d H:i:s', strtotime($arrival_datetime[$count])),
                'from' => $from[$count],
                'to' => $to[$count],
                'details' => $details[$count],
                'is_travel' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'created_ip' => $request->ip(),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id
            );

            $insert_data[] = $data;
        }

        Travel_info::insert($insert_data);

        /* --END-- */

        $travelers = user::whereIn('id', $request->get('traveler_ids'))->pluck('name')->toArray();

        $mail_data = [];
        $mail_data['name'] = Auth::user()->name;
        $mail_data['to_email'] = user::where('status', 'Enabled')->where('role', config('constants.ASSISTANT'))->pluck('email')->toArray();
        $mail_data['text'] = "Travel Expense requested, Please login to your account and add travel options for that.Request No:$request_no";

        $this->common_task->travel_request($mail_data);
        $email_user_ids = user::where('status', 'Enabled')->where('role', config('constants.ASSISTANT'))->pluck('id')->toArray();

        $this->notification_task->travelExpenceNotify($email_user_ids, 'Travel Expense Request', $request_no);

        return redirect()->route('admin.travel')->with('success', 'Travel expence detail successfully submitted.');
    }

    public function edit_travel($id)   //edit travel option
    {
        $this->data['page_title'] = "Edit Travel Details";

        $this->data['travel'] = $travel = Travel::where('id', $id)->get()->first();

        if ($travel) {

            $this->data['travel_info'] = $travel_info = Travel_info::where('travel_id', $id)->where('is_travel', 1)->get();
        }
        $this->data['travel_via'] = config::get('constants.TRAVEL_VIA');

        
        $this->data['companies'] = Companies::select('id', 'company_name')->get();
        $this->data['traveler_ids'] = User::where('status', 'Enabled')->with(['employee' => function () {
            return $query->where('company_id', $userDetail->employee->company_id);
        }])->pluck('name', 'id');


        $this->data['flight_trip'] = ['one_way' => 'One Way', 'round_trip' => 'Round Trip', 'multi_city' => 'Multi City'];

        return view('admin.travel.edit_travel', $this->data);
    }

    public function update_travel(Request $request)  //update travel request
    {
        $validator_normal = Validator::make($request->all(), [
            'company_id' => 'required',
            'project_id' => 'required',
            'travel_via' => 'required',
            'traveler_ids' => 'required',
            'departure_datetime' => 'required',
            'arrival_datetime' => 'required',
            'from' => 'required',
            'to' => 'required',
        ]);

        if ($validator_normal->fails()) {
            return redirect()->route('admin.edit_travel')->with('error', 'Please follow validation rules.');
        }

        $from = $request->from;

        $departure_datetime = $request->departure_datetime;
        $arrival_datetime = $request->arrival_datetime;
        $from = $request->from;
        $to = $request->to;
        $details = $request->details;

        $update_arr = [
            'company_id' => $request->get('company_id'),
            'project_id' => $request->get('project_id'),
            'other_project_details' => $request->get('other_project_details'),
            'travel_via' => $request->get('travel_via'),
            'flight_trip' => $request->get('flight_trip'),
            'traveler_ids' => implode(",", $request->get('traveler_ids')),
            'departure_datetime' => date('Y-m-d H:i:s', strtotime($departure_datetime[0])),
            'arrival_datetime' => date('Y-m-d H:i:s', strtotime($arrival_datetime[0])),
            'booked_by' => Auth::user()->id,
            'from' => $from[0],
            'to' => $to[0],
            'work_details' => $details[0],
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id
        ];

        Travel::where('id', $request->input('id'))->update($update_arr);

        /* Multi Insert for flights Options */

        /* First delete entire records then insert */

        if ($request->input('id')) {

            Travel_info::where('travel_id', $request->input('id'))->delete();
        }

        for ($count = 0; $count < count($from); $count++) {

            $data = array(
                'travel_id' => $request->get('id'),
                'departure_datetime' => date('Y-m-d H:i:s', strtotime($departure_datetime[$count])),
                'arrival_datetime' => date('Y-m-d H:i:s', strtotime($arrival_datetime[$count])),
                'from' => $from[$count],
                'to' => $to[$count],
                'details' => $details[$count],
                'is_travel' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'created_ip' => $request->ip(),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id
            );

            $insert_data[] = $data;
        }

        Travel_info::insert($insert_data);

        /* --END-- */

        return redirect()->route('admin.travel')->with('success', 'Travel expence details updated successfully.');
    }


    public function add_travel_option($id)  //add travel option
    {

        $this->data['module_title'] = "Travel Requests";

        $this->data['travel'] = Travel::where('id', $id)->get()->first();

        $this->data['travel_via'] = config::get('constants.TRAVEL_VIA');

        $this->data['flight_trip'] = ['one_way' => 'One Way', 'round_trip' => 'Round Trip', 'multi_city' => 'Multi City'];

        $this->data['travel_option'] = Travel_option::where('travel_id', $id)->where('status', 'Pending')->get();

        $this->data['page_title'] = "Add Travel Options";

        return view('admin.travel.add_travel_option', $this->data);
    }

    public function edit_travel_option($id)   //edit travel option
    {

        $this->data['page_title'] = "Edit Travel options ";

        $this->data['travel_option'] = $travel_option = Travel_option::join('travel_info', 'travel_info.travel_option_id', '=', 'travel_option.id')
            ->where('travel_option.id', '=', $id)
            ->where('travel_option.status', 'Pending')
            ->get(['travel_option.*', 'travel_info.flight_trip']);

        if (!empty($travel_option)) {

            $this->data['flight_info'] = $flight_info = Travel_info::where('travel_option_id', $id)->where('is_travel', 0)->get();
        }

        $this->data['travel_via'] = config::get('constants.TRAVEL_VIA');
        $this->data['flight_trip'] = ['one_way' => 'One Way', 'round_trip' => 'Round Trip', 'multi_city' => 'Multi City'];


        $this->data['flight_info'] = $flight_info = Travel_info::where('travel_option_id', $id)->where('is_travel', 0)->get();


        return view('admin.travel.edit_travel_option', $this->data);
    }


    public function insert_travel_option(Request $request)   //insert travel option
    {
        $validator_normal = Validator::make($request->all(), [

            'travel_via' => 'required',
            'departure_datetime' => 'required',
            'arrival_datetime' => 'required',
            'amount' => 'required',
            'from' => 'required',
            'to' => 'required',
            'details' => 'required'
        ]);

        $request_data = $request->all();

        $travel_id = $request->input('id');
        $travel_option_id = $request->input('option_id');

        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_travel_option', $travel_id)->with('error', 'Please follow validation rules.');
        }


        //upload user travel image
        $travel_image_arr = '';
        if ($request->file('travel_image')) {
            $travel_image =  $request->file('travel_image');
            // store image to directory
            
            $original_file_name = explode('.', $travel_image->getClientOriginalName());
     
            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);

            $file_path = $travel_image->storeAs('public/travel_image', $new_file_name);
            if ($file_path) {
                $travel_image_arr = $file_path;
            }
            
        }

        $departure_datetime = $request->departure_datetime;
        $arrival_datetime = $request->arrival_datetime;
        $from = $request->from;
        $to = $request->to;
        $details = $request->details;

        $insert_data = [
            'travel_id' => $travel_id,
            'travel_via' => $request->input('travel_via'),
            'flight_trip' => $request->get('flight_trip'),
            'departure_datetime' => date('Y-m-d H:i:s', strtotime($departure_datetime[0])),
            'arrival_datetime' => date('Y-m-d H:i:s', strtotime($arrival_datetime[0])),
            'from' => $from[0],
            'to' => $to[0],
            'details' => $details[0],
            'amount' => $request->input('amount'),
            'created_at' => date('Y-m-d H:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
        ];


        if ($travel_image_arr) {
            $insert_data['travel_image'] = $travel_image_arr;
        }

        $option_id = Travel_option::insertGetId($insert_data);


        $travel_arr = [
            'status' => 'Processing',
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id
        ];

        Travel::where('id', $travel_id)->update($travel_arr);

        for ($count = 0; $count < count($from); $count++) {

            $data = array(
                'travel_id' => $travel_id,
                'travel_option_id' => $option_id,
                'flight_trip' => $request->get('flight_trip'),
                'departure_datetime' => date('Y-m-d H:i:s', strtotime($departure_datetime[$count])),
                'arrival_datetime' => date('Y-m-d H:i:s', strtotime($arrival_datetime[$count])),
                'from' => $from[$count],
                'to' => $to[$count],
                'details' => $details[$count],
                'is_travel' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'created_ip' => $request->ip(),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id
            );


            $insert_info_data[] = $data;
        }

        Travel_info::insert($insert_info_data);

        /* --END-- */


        $requested_user_name = User::join('travel', 'travel.booked_by', '=', 'users.id')
            ->where('travel.id', '=', $travel_id)
            ->get('users.name');


        $mail_data = [];
        $mail_data['name'] = $requested_user_name[0]->name;
        $mail_data['to_email'] = user::where('status', 'Enabled')->where('role', config('constants.SuperUser'))->pluck('email')->toArray();
        $mail_data['text'] = "Travel Options are Successfully added.Please Check details via login in your account.";
        $mail_data['superUser_name'] = User::where('status', 'Enabled')->where('role', config('constants.SuperUser'))->value('name');

        $this->common_task->travel_options($mail_data);

        $user_id = user::where('status', 'Enabled')->where('role', config('constants.SuperUser'))->pluck('id')->toArray();

        $user_name = $requested_user_name[0]->name;

        $this->notification_task->travelOptions($user_id, $user_name);


        return redirect()->route('admin.add_travel_option', $travel_id)->with('success', 'Travel option details  successfully added.');
    }

    public function update_travel_option(Request $request)   //update travel option
    {
        $validator_normal = Validator::make($request->all(), [

            'travel_via' => 'required',
            'departure_datetime' => 'required',
            'arrival_datetime' => 'required',
            'amount' => 'required',
            'from' => 'required',
            'to' => 'required',
            'details' => 'required'
        ]);

        $request_data = $request->all();

        $travel_id = $request->input('id');
        $travel_option_id = $request->input('option_id');

        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_travel_option', $travel_id)->with('error', 'Please follow validation rules.');
        }

        //upload user travel image
        $travel_image_arr = '';
        if ($request->file('travel_image')) {
            $travel_image =  $request->file('travel_image');
            // store image to directory.
            $original_file_name = explode('.', $travel_image->getClientOriginalName());
     
            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);

            $file_path = $travel_image->storeAs('public/travel_image', $new_file_name);
            if ($file_path) {
                $travel_image_arr = $file_path;
            }
           
        }

        $departure_datetime = $request->departure_datetime;
        $arrival_datetime = $request->arrival_datetime;
        $from = $request->from;
        $to = $request->to;
        $details = $request->details;



        $option_arr = [
            'travel_via' => $request->input('travel_via'),
            'flight_trip' => $request->get('flight_trip'),
            'departure_datetime' => date('Y-m-d H:i:s', strtotime($departure_datetime[0])),
            'arrival_datetime' => date('Y-m-d H:i:s', strtotime($arrival_datetime[0])),
            'from' => $from[0],
            'to' => $to[0],
            'details' => $details[0],
            'amount' => $request->input('amount'),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
        ];

        if ($travel_image_arr) {
            $option_arr['travel_image'] = $travel_image_arr;
        }

        Travel_option::where('id', $travel_option_id)->update($option_arr);

        $travel_arr = [
            'status' => 'Processing',
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id
        ];

        Travel::where('id', $travel_id)->update($travel_arr);

        if ($travel_option_id) {

            $check_entry_exit = Travel_info::where('travel_option_id', $travel_option_id)->get();
            if ($check_entry_exit->count() > 0) {

                Travel_info::where('travel_option_id', $travel_option_id)->delete();
            }
        }

        for ($count = 0; $count < count($from); $count++) {

            $data = array(
                'travel_id' => $travel_id,
                'flight_trip' => $request->get('flight_trip'),
                'departure_datetime' => date('Y-m-d H:i:s', strtotime($departure_datetime[$count])),
                'arrival_datetime' => date('Y-m-d H:i:s', strtotime($arrival_datetime[$count])),
                'from' => $from[$count],
                'to' => $to[$count],
                'details' => $details[$count],
                'is_travel' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'created_ip' => $request->ip(),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id
            );

            if ($travel_option_id) {
                $data['travel_option_id'] = $travel_option_id;
            }
            $insert_info_data[] = $data;
        }

        Travel_info::insert($insert_info_data);

        /* --END-- */


        $requested_user_name = User::join('travel', 'travel.booked_by', '=', 'users.id')
            ->where('travel.id', '=', $travel_id)
            ->get('users.name');


        $mail_data = [];
        $mail_data['name'] = $requested_user_name[0]->name;
        $mail_data['to_email'] = user::where('status', 'Enabled')->where('role', config('constants.SuperUser'))->pluck('email')->toArray();
        $mail_data['text'] = "Travel Options are Successfully added.Please Check details via login in your account.";
        $mail_data['superUser_name'] = User::where('status', 'Enabled')->where('role', config('constants.SuperUser'))->value('name');

        $this->common_task->travel_options($mail_data);

        $user_id = user::where('status', 'Enabled')->where('role', config('constants.SuperUser'))->pluck('id')->toArray();

        $user_name = $requested_user_name[0]->name;

        $this->notification_task->travelOptions($user_id, $user_name);


        return redirect()->route('admin.add_travel_option', $travel_id)->with('success', 'Travel option details  successfully updated.');
    }

    public function get_travel_options(Request $request, $id)   //travel options and sort By
    {
        $sort_by = '';
        $sort_by = $request->input('column_name');

        $this->data['module_title']  = "Travel Requests";

        $this->data['page_title']  = "Travel Options";

        $partial_query = Travel_option::with(['travel_info' => function ($q) {
            $q->where('is_travel', 0);
        }])
            ->where('travel_option.travel_id', $id)
            ->where('travel_option.status', 'Pending');

        if ($sort_by) {
            $partial_query->orderBy('travel_option' . '.' . $sort_by, 'ASC');
            $this->data['selected_option'] = $sort_by;
        } else {
            $partial_query->orderBy('travel_option.amount', 'ASC');
            $this->data['selected_option'] = 'amount';
        }
        $this->data['travel_option'] = $data = $partial_query->get(['travel_option.id', 'travel_option.travel_id', 'travel_option.travel_via', 'travel_option.amount', 'travel_option.travel_image', 'travel_option.flight_trip']);


        $this->data['sort_by_options'] = ['amount' => 'Amount', 'departure_datetime' => 'Time'];

        return view('admin.travel.travel_options', $this->data);
    }


    public function travel_booking($id)   //approve options
    {

        $this->data['module_title'] = "Travel Requests";

        $this->data['page_title'] = "Travel Booking";
        $this->data['travel_via'] = config::get('constants.TRAVEL_VIA');
        $this->data['payment_type'] = config::get('constants.PAYMENT_TYPE');
        $this->data['flight_trip'] = ['one_way' => 'One Way', 'round_trip' => 'Round Trip', 'multi_city' => 'Multi City'];

        $this->data['booking_data'] = Travel_option::with(['travel_info' => function ($q) {
            $q->where('is_travel', 0);
        }])
            ->where('travel_option.travel_id', $id)
            ->where('travel_option.status', 'Approved')
            ->get([
                'travel_option.id', 'travel_option.travel_id', 'travel_option.travel_via',
                'travel_option.amount',
                'travel_option.travel_image', 'travel_option.flight_trip'
            ]);


        return view('admin.travel.travel_booking', $this->data);
    }

    public function get_company_payment_cards(Request $request)   // ajax call
    {

        $travel_id = $request->travel_id;
        $card_type = config::get('constants.PAYMENT_TYPE')[$request->payment_type];


        $payment_cards = PaymentCard::join('travel', 'travel.company_id', 'payment_card.company_id')
            ->where('payment_card.status', 'Enabled')
            ->where('payment_card.card_type', $card_type)
            ->where('travel.id', '=', $travel_id)
            ->get(['payment_card.id', 'payment_card.card_number']);

        return response()->json($payment_cards);
    }

    public function save_travel_booking(Request $request)   //approve option travel booking
    {

        $validator_normal = Validator::make($request->all(), [

            'travel_via' => 'required',
            'departure_datetime' => 'required',
            'arrival_datetime' => 'required',
            'from' => 'required',
            'to' => 'required',
            'payment_type' => 'required',
            'amount' => 'required'

        ]);

        $travel_option_id = $request->input('id');
        $travel_id = $request->input('travel_id');

        if ($validator_normal->fails()) {
            return redirect()->route('admin.travel_booking', $travel_id)->with('error', 'Please follow validation rules.');
        }

        $from = $request->from;
        $departure_datetime = $request->departure_datetime;
        $arrival_datetime = $request->arrival_datetime;
        $from = $request->from;
        $to = $request->to;
        $details = $request->details;


        $booking_arrr = [

            'travel_via' => $request->input('travel_via'),
            'flight_trip' => $request->get('flight_trip'),
            'departure_datetime' => date('Y-m-d H:i:s', strtotime($departure_datetime[0])),
            'arrival_datetime' => date('Y-m-d H:i:s', strtotime($arrival_datetime[0])),
            'from' => $from[0],
            'to' => $to[0],
            'details' => $details[0],
            'payment_type' => $request->input('payment_type'),
            'amount' => $request->input('amount'),
            'card_number' => $request->input('card_number'),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id
        ];

        Travel_option::where('id', $travel_option_id)->update($booking_arrr);

        /* Multi Insert for flights Options */

        if ($travel_option_id) {

            $check_entry_exit = Travel_info::where('travel_option_id', $travel_option_id)->get();
            if ($check_entry_exit->count() > 0) {

                Travel_info::where('travel_option_id', $travel_option_id)->delete();
            }
        }

        for ($count = 0; $count < count($from); $count++) {

            $data = array(
                'travel_id' => $travel_id,
                'travel_option_id' => $travel_option_id,
                'departure_datetime' => date('Y-m-d H:i:s', strtotime($departure_datetime[$count])),
                'arrival_datetime' => date('Y-m-d H:i:s', strtotime($arrival_datetime[$count])),
                'from' => $from[$count],
                'to' => $to[$count],
                'details' => $details[$count],
                'is_travel' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'created_ip' => $request->ip(),
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id
            );

            $insert_data[] = $data;
        }

        Travel_info::insert($insert_data);

        if ($request->hasFile('file_name')) {
            $mime_type_arr = [];
            foreach ($request->file_name as $file_name) {
                // store image to directory.
                $mime_type_arr[] = $file_name->getMimeType();

                    $original_file_name = explode('.', $file_name->getClientOriginalName());

                    $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);

                    $path = $file_name->storeAs('public/travel_image', $new_file_name);


                $booking_files_arr = [

                    'travel_option_id' => $travel_option_id,
                    'file_name' => $path,
                    'created_at' => date('Y-m-d h:i:s'),
                    'created_ip' => $request->ip(),
                    'updated_by' => Auth::user()->id,
                ];

                Travel_booking_files::insert($booking_files_arr);
            }
        }

        $travel_arr = [
            'status' => 'Confirmed',
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id
        ];

        Travel::where('id', $travel_id)->update($travel_arr);

        $requested_user_name = User::join('travel', 'travel.booked_by', '=', 'users.id')
            ->join('travel_option', 'travel_option.travel_id', '=', 'travel.id')
            ->where('travel_option.id', '=', $travel_option_id)
            ->get(['users.name', 'users.role', 'users.email']);


        $html = '';
        for ($count = 0; $count < count($from); $count++) {
            $html .= '<table class="table table-striped table-bordered">' .
                '<thead>' .
                '<tr>' .
                '<th>FROM</th>' .
                '<th>TO</th>' .
                '<th>Departure</th>' .
                '<th>Arrival</th>' .
                '<th>Details</th>' .
                '</tr>' .
                '</thead>' .
                '<tbody id="flights_info">' .

                '<tr>' .
                '<td>' . $from[$count] . '</td>' .
                '<td>' . $to[$count] . '</td>' .
                '<td>' . $departure_datetime[$count] . '</td>' .
                '<td>' . $arrival_datetime[$count] . '</td>' .
                '<td>' . $details[$count] . '</td>' .
                '</tr>' .

                '</tbody>' .
                '</table>';
        }

        $mail_data = [];

        $mail_data['mimeType'] = $mime_type_arr;

        $mail_data['shedule'] = $html;

        $mail_data['assistent_name'] = $requested_user_name[0]->name;

        $mail_data['email_list'] = User::where('status', 'Enabled')->whereIn('role', [config('constants.ASSISTANT'), config('constants.SuperUser')])->pluck('email')->toArray();

        $mail_data['to_email'] = $requested_user_name[0]->email;

        $mail_data['travel_via'] = config::get('constants.TRAVEL_VIA')[$request->input('travel_via')];
        $mail_data['payment_type'] = config::get('constants.PAYMENT_TYPE')[$request->input('payment_type')];
        $mail_data['amount'] = $request->input('amount');

        $this->common_task->travel_booking_confirmed($mail_data, $travel_option_id);

        return redirect()->route('admin.travel_requests')->with('success', 'Travel Successfully Booked.');
    }

    public function approve_travel_option(Request $request)   //approve one option
    {

        $validator_normal = Validator::make($request->all(), [
            'approval_note' => 'required',
            'id' => 'required'
        ]);

        $travel_id  = $request->input('travel_id');
        if ($validator_normal->fails()) {
            return redirect()->route('admin.get_travel_options', $travel_id)->with('error', 'Please follow validation rules.');
        }

        $id  = $request->input('id');
        $travel_option_arrr = [

            'status' => 'Approved',
            'approval_note' => $request->input('approval_note'),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
        ];
        if (Travel_option::where('id', $id)->update($travel_option_arrr)) {

            $reject_option_arrr = [

                'status' => 'Rejected',
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
            ];
            Travel_option::where('travel_id', $travel_id)
                ->where('id', '!=', $id)->update($reject_option_arrr);

            $travel_arr = [
                'status' => 'Approved',
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id
            ];

            Travel::where('id', $travel_id)->update($travel_arr);

            $approve_option = Travel_option::where('id', $id)->where('status', 'Approved')->get();

            $requested_user_name = User::join('travel', 'travel.booked_by', '=', 'users.id')
                ->join('travel_option', 'travel_option.travel_id', '=', 'travel.id')
                ->where('travel_option.id', '=', $id)
                ->get('users.name');

            $request_no = Travel::where('id', $travel_id)->value('request_no');
            $mail_data = [];
            $mail_data['assistent_name'] = $requested_user_name[0]->name;
            $mail_data['to_email'] = user::where('status', 'Enabled')->where('role', config('constants.ASSISTANT'))->pluck('email')->toArray();
            $mail_data['approved_by'] = Auth::user()->name;
            $mail_data['request_no'] = $request_no;
            $mail_data['approval_note'] = $request->input('approval_note');

            $this->common_task->approve_travel_option($mail_data);

            $user_id = user::where('status', 'Enabled')->where('role', config('constants.ASSISTANT'))->pluck('id')->toArray();

            $user_name = Auth::user()->name;

            $this->notification_task->ApprovetravelOption($user_id, $user_name);

            return redirect()->route('admin.travel_requests')->with('success', 'Travel option Approved.');
        }
    }


    public function reject_all_travel_option(Request $request)   //reject all options
    {

        $validator_normal = Validator::make($request->all(), [
            'reject_note' => 'required',
            'travel_id2' => 'required'
        ]);
        $travel_id  = $request->input('travel_id2');

        if ($validator_normal->fails()) {
            return redirect()->route('admin.get_travel_options', $travel_id)->with('error', 'Please follow validation rules.');
        }
        $travel_option_arrr = [

            'status' => 'Rejected',
            'reject_note' => $request->input('reject_note'),
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
        ];

        if (Travel_option::where('travel_id', $travel_id)->update($travel_option_arrr)) {

            $travel_arr = [
                'status' => 'Pending',
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_ip' => $request->ip(),
                'updated_by' => Auth::user()->id
            ];
            Travel::where('id', $travel_id)->update($travel_arr);
            $request_no = Travel::where('id', $travel_id)->value('request_no');

            $mail_data = [];
            $mail_data['to_email'] = user::where('status', 'Enabled')->where('role', config('constants.ASSISTANT'))->pluck('email')->toArray();
            $mail_data['unique_no'] = $request_no;
            $mail_data['reject_note'] = $request->input('reject_note');

            $this->common_task->reject_all_travel_option($mail_data);

            $user_id = user::where('status', 'Enabled')->where('role', config('constants.ASSISTANT'))->pluck('id')->toArray();

            $user_name = Auth::user()->name;

            $this->notification_task->RejectTravelOptions($user_id, $user_name, $request_no);

            return redirect()->route('admin.travel_requests')->with('success', 'All travel options rejected.');
        }

        return redirect()->route('admin.get_travel_options', $travel_id)->with('error', 'Error during operation. Try again!');
    }

    public function reject_travel_request(Request $request)  //new
    {
        $validator_normal = Validator::make($request->all(), [
            'reject_note' => 'required',
            'travel_id' => 'required'
        ]);
        $travel_id  = $request->input('travel_id');
        $reject_note  = $request->input('reject_note');

        if ($validator_normal->fails()) {
            return redirect()->route('admin.travel_requests')->with('error', 'Please follow validation rules.');
        }

        $travel_arr = [
            'status' => 'Rejected',
            'reject_details' => $reject_note,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id
        ];
        Travel::where('id', $travel_id)->update($travel_arr);
        $request_no = Travel::where('id', $travel_id)->value('request_no');
        $requested_user = User::join('travel', 'travel.booked_by', '=', 'users.id')
            ->where('travel.id', '=', $travel_id)
            ->get(['users.id', 'users.name', 'users.email']);
        $rejected_user_name = Auth::user()->name;

        $mail_data = [];
        $mail_data['to_email'] = $requested_user[0]->email;
        $mail_data['request_no'] = $request_no;
        $mail_data['reject_note'] = $reject_note;
        $mail_data['user_name'] = $requested_user[0]->name;
        $mail_data['rejected_user_name'] = Auth::user()->name;

        $user_id = [$requested_user[0]->id];
        $this->common_task->reject_travel_expense($mail_data);

        $this->notification_task->RejectTravelRequest($user_id, $rejected_user_name, $request_no);

        return redirect()->route('admin.travel_requests')->with('success', 'Travel Reqest Successfully Rejected.');
    }

    public function get_travel_files(Request $request)  //ajax call
    {

        $validator_normal = Validator::make($request->all(), ['id' => 'required']);

        if ($validator_normal->fails()) {
            return response()->json(['status' => false]);
        }

        $travel_option_id = $request->id;

        $travel_files = Travel_booking_files::where('travel_option_id', $travel_option_id)
            ->get(['id', 'travel_option_id', 'file_name']);


        foreach ($travel_files as $key => $files) {

            if ($files->file_name) {

                $travel_files[$key]->file_name = asset('storage/' . str_replace('public/', '', $files->file_name));
            } else {

                $travel_files[$key]->file_name = "";
            }
        }

        $this->data['travel_files'] = $travel_files;


        if ($travel_files->count() == 0) {
            return response()->json(['status' => false, 'data' => $this->data]);
        } else {

            return response()->json(['status' => true, 'data' => $this->data]);
        }
    }

    public function get_confirm_travel_detail(Request $request)  //ajax call new
    {

        $validator_normal = Validator::make($request->all(), ['id' => 'required']);

        if ($validator_normal->fails()) {
            return response()->json(['status' => false]);
        }
        $travel_option_id = $request->id;
   
        $flight_trip_types = ['one_way' => 'One Way', 'round_trip' => 'Round Trip', 'multi_city' => 'Multi City'];

        $booked_data = Travel_option::with(['booking_files', 'travel_info' => function ($q) {
            $q->where('is_travel', 0);
        }])
        ->leftjoin('payment_card', 'payment_card.id', '=', 'travel_option.card_number')
            ->where('travel_option.travel_id', $travel_option_id)
            ->where('travel_option.status', 'Approved')
            ->get(['travel_option.id',
                'travel_option.travel_via',
                'travel_option.amount','travel_option.payment_type','travel_option.approval_note',
                'travel_option.travel_image', 'travel_option.flight_trip','payment_card.card_number','payment_card.name_on_card'
            ]);


            foreach ($booked_data as $key => $booking) {
                
                if ($booking->travel_image) {
                    $booked_data[$key]->travel_image = asset('storage/' . str_replace('public/', '', $booking->travel_image));
                } else {
                    $booked_data[$key]->travel_image = "";
                }
                if ($booking->flight_trip) {
                    $booking->travel_via = config::get('constants.TRAVEL_VIA')[$booking->travel_via]. '(' .$flight_trip_types[$booking->flight_trip] .')';
                }else {
                    $booking->travel_via = config::get('constants.TRAVEL_VIA')[$booking->travel_via];
                }
                if ($booking->card_number) {

                    $booking->card_number = 'XXXXXXXXXXXX' . substr($booking->card_number, -4);
                }

                $booking->payment_type = config::get('constants.PAYMENT_TYPE')[$booking->payment_type];
               
                //$booking->flight_trip = $flight_trip_types[$booking->flight_trip];
                foreach ($booking->booking_files as $k => $files) {

                    if ($files->file_name) {
        
                        $booked_data[$key]->booking_files[$k]->file_name = asset('storage/' . str_replace('public/', '', $files->file_name));
                    } else {
        
                        $booked_data[$key]->booking_files[$k]->file_name = "";
                    }
                }
            
            }
            $this->data['booked_data'] = $booked_data;

        if ($booked_data->count() == 0) {
            return response()->json(['status' => false, 'data' => $this->data]);
        } else {
            return response()->json(['status' => true, 'data' => $this->data]);
        }
    }


    public function cancel_travel($id, Request $request)
    {

        $update_arr = [
            'status' => 'Canceled',
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id
        ];

        Travel::where('id', $id)->update($update_arr);

        return redirect()->route('admin.travel')->with('success', 'Travel expence successfully canceled.');
    }

    public function get_travel_detail(Request $request)  //ajax call
    {

        $validator_normal = Validator::make($request->all(), ['id' => 'required']);

        if ($validator_normal->fails()) {
            return response()->json(['status' => false]);
        }
        $flight_trip_types = ['one_way' => 'One Way', 'round_trip' => 'Round Trip', 'multi_city' => 'Multi City'];
       
        $travel_list = Travel::where('id', $request->get('id'))->get()->first();

        if ($travel_list->flight_trip) {
            $travel_list->travel_via = config::get('constants.TRAVEL_VIA')[$travel_list->travel_via]. '(' .$flight_trip_types[$travel_list->flight_trip] .')';
        }else {
            $travel_list->travel_via = config::get('constants.TRAVEL_VIA')[$travel_list->travel_via];
        }

        $traveler_list = User::whereIn('id', explode(',', $travel_list->traveler_ids))->pluck('name')->toArray();
        $this->data['traveler_list'] = implode(', ', $traveler_list);
        
     
        $this->data['travel_shedule'] = Travel::with(['travel_info' => function ($q) use ($request) {
            $q->where('is_travel', 1)
            ->where('travel_id',$request->get('id'));
        
        }])
        ->where('travel.id', $request->get('id'))
            ->get(['travel.id']);
            
            
        $this->data['travel_list'] = $travel_list;

        if ($travel_list) {
            return response()->json(['status' => true, 'data' => $this->data]);
        } else {
            return response()->json(['status' => false]);
        }
    }
}
