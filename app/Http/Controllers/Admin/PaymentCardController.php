<?php

namespace App\Http\Controllers\Admin;

use App\Banks;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Config;
use App\Common_query;
use App\Companies;
use App\PaymentCard;
use App\Lib\Permissions;
use App\Lib\NotificationTask;

class PaymentCardController extends Controller {

    public $data;
    private $notification_task;

    public function __construct() {
        $this->notification_task = new NotificationTask();
        $this->data['module_title'] = "Payment Cards";
        $this->data['module_link'] = "admin.payment_card";
        $this->module_id = 47;
    }

    public function index() {
        $this->data['page_title'] = "Payment Cards";
        $payment_card_full_view_permission = Permissions::checkPermission(47, 5);
        $this->data['payment_card_add_permission'] = Permissions::checkPermission(47, 3);
        $this->data['payment_card_edit_permission'] = Permissions::checkPermission(47, 2);
        
        if (!$payment_card_full_view_permission) {
            return redirect()->route('admin.dashboard')->with('error','Access Denied.');
        }
        $this->data['view_special_permission'] = Permissions::checkSpecialPermission($this->module_id);
        return view('admin.payment_card.index', $this->data);
    }

    public function get_payment_card_list() {

        $datatable_fields = array(
            'company.company_name', 'bank.bank_name', 'bank.ac_number', 'payment_card.name_on_card', 'payment_card.card_number', 'payment_card.card_type', 'payment_card.status','users.name'
        );
        $request = Input::all();
        //$conditions_array = ['user_id' => Auth::user()->id];

        $getfiled = array('payment_card.*', 'payment_card.id', 'company.company_name', 'bank.bank_name', 'bank.ac_number','users.name as assigncard_user');
        $table = "payment_card";
        $conditions_array = ['payment_card.is_approved' => 1];

        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'company';
        $join_str[0]['join_table_id'] = 'company.id';
        $join_str[0]['from_table_id'] = 'payment_card.company_id';

        $join_str[1]['join_type'] = '';
        $join_str[1]['table'] = 'bank';
        $join_str[1]['join_table_id'] = 'bank.id';
        $join_str[1]['from_table_id'] = 'payment_card.bank_id';
        
        $join_str[2]['join_type'] = 'left';
        $join_str[2]['table'] = 'users';
        $join_str[2]['join_table_id'] = 'users.id';
        $join_str[2]['from_table_id'] = 'payment_card.assigncard_user_id';


        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);

        die();
    }

    public function add_payment_card() {
        $view_special_permission = Permissions::checkSpecialPermission($this->module_id);
        if(!$view_special_permission){
            return redirect()->route('admin.dashboard')->with('error','Access denied. You do not have permission to access this module.');
        }
        $this->data['page_title'] = 'Add Payment Card';

        $this->data['companies'] = Companies::where('status', 'Enabled')->orderBy('company_name')
                ->get();
        $this->data['users_data'] = \App\User::select('id', 'name')->where('status', 'Enabled')->orderBy('name')->where('is_user_relieved', 0)->get();

        return view('admin.payment_card.add_payment_card', $this->data);
    }

    public function companies_bank(Request $request) {


        $company_id = $request->company_id;

        $banks = Banks::select('bank.*')
                ->where('bank.status', 'Enabled')
                ->where('company_id', $company_id)->orderBy('bank_name')
                ->get();

        return response()->json($banks);
    }

    public function insert_payment_card(Request $request) {


        $validator_normal = Validator::make($request->all(), [
                    'company_id' => 'required',
                    'bank_id' => 'required',
                    'card_type' => 'required',
                    'name_on_card' => 'required',
                    'card_number' => 'required',
                    'assigncard_user_id' => 'required',
        ]);


        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_payment_card')->with('error', 'Please follow validation rules.');
        }

        $payment_card_arr = [
            'user_id' => Auth::user()->id,
            'company_id' => $request->input('company_id'),
            'bank_id' => $request->input('bank_id'),
            'card_type' => $request->input('card_type'),
            'name_on_card' => $request->input('name_on_card'),
            'card_number' => $request->input('card_number'),
            'assigncard_user_id' => $request->input('assigncard_user_id'),
            // 'status' => 'Disabled',
            // 'is_approved' => 0,
            'created_at' => date('Y-m-d h:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id
        ];
        if (Auth::user()->role != config('constants.SuperUser')) {
            $payment_card_arr['status'] = 'Disabled';
            $payment_card_arr['is_approved'] = 0;
        } else {
            $payment_card_arr['status'] = 'Enabled';
            $payment_card_arr['is_approved'] = 1;
        }
 
        PaymentCard::insert($payment_card_arr);
        $module = 'Payment Card';
        $this->notification_task->entryApprovalNotify($module);

        return redirect()->route('admin.payment_card')->with('success', 'New Payment Card successfully Added.');
    }

    public function change_payment_card_status(Request $request, $id, $status) {


        if (PaymentCard::where('id', $id)->update(['status' => $status])) {
            return redirect()->route('admin.payment_card')->with('success', 'Status successfully updated.');
        }
        return redirect()->route('admin.payment_card')->with('error', 'Error during operation. Try again!');
    }

    public function edit_payment_card($id) {
        $view_special_permission = Permissions::checkSpecialPermission($this->module_id);
        if(!$view_special_permission){
            return redirect()->route('admin.dashboard')->with('error','Access denied. You do not have permission to access this module.');
        }
        $this->data['page_title'] = "Edit Payment Card Details";
        $this->data['card_detail'] = $card_detail = PaymentCard::where('id', $id)->get();
        if ($this->data['card_detail']->count() == 0) {
            return redirect()->route('admin.payment_card')->with('error', 'Error Occurred. Try Again!');
        }
        $this->data['companies'] = Companies::where('status', 'Enabled')->orderBy('company_name')
                ->get();

        $this->data['banks'] = Banks::where('company_id', '=', $card_detail[0]->company_id)->where('status', 'Enabled')->get(['bank_name', 'id', 'ac_number']);
        $this->data['users_data'] = \App\User::select('id', 'name')->where('status', 'Enabled')->orderBy('name')->where('is_user_relieved', 0)->get();
        return view('admin.payment_card.edit_payment_card', $this->data);
    }

    public function update_payment_card(Request $request) {
        $validator_normal = Validator::make($request->all(), [
                    'company_id' => 'required',
                    'bank_id' => 'required',
                    'card_type' => 'required',
                    'name_on_card' => 'required',
                    'card_number' => 'required',
                    'assigncard_user_id' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.payment_card')->with('error', 'Please follow validation rules.');
        }
        $payment_card_id = $request->input('id');
        $card_arr = [
            'user_id' => Auth::user()->id,
            'company_id' => $request->input('company_id'),
            'bank_id' => $request->input('bank_id'),
            'card_type' => $request->input('card_type'),
            'name_on_card' => $request->input('name_on_card'),
            'card_number' => $request->input('card_number'),
            'assigncard_user_id' => $request->input('assigncard_user_id'),
            //'status' => 'Enabled',
            'created_at' => date('Y-m-d h:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id
        ];

        PaymentCard::where('id', $payment_card_id)->update($card_arr);

        return redirect()->route('admin.payment_card')->with('success', 'Payment Card successfully updated.');
    }

    public function delete_payment_card($id) {
        if (PaymentCard::where('id', $id)->delete()) {
            return redirect()->route('admin.payment_card')->with('success', 'Payment successfully deleted.');
        }
        return redirect()->route('admin.payment_card')->with('error', 'Error during operation. Try again!');
    }

    //check role name exist or not
    public function check_uniqueCardNumber(Request $request) {
        $card_number = $request->card_number;
        $payment_card_id = $request->payment_card_id;


        $cardnumberCheck = PaymentCard::select(['id'])->where('card_number', '=', $card_number)->first();

        //Check during add card number details
        if (empty($request->payment_card_id)) {
            if (!empty($cardnumberCheck)) {
                echo 'false';
                die();
            } else {
                echo 'true';
                die();
            }
        }

        //Check during edit card number details
        if (!empty($request->card_number) && !empty($request->payment_card_id) && !empty($cardnumberCheck)) {
            if ($cardnumberCheck->id == $payment_card_id) {
                echo 'true';
                die();
            } else {
                echo 'false';
                die();
            }
        } else {
            echo 'true';
            die();
        }
    }

}
