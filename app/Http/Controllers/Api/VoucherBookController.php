<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\VoucherNumberRegister;
use App\AssignedVoucher;
use App\User;
use App\Companies;
use Illuminate\Support\Facades\Auth;
use App\Lib\CommonTask;
use Illuminate\Support\Facades\Validator;
use App\Lib\NotificationTask;
use DB;
use phpDocumentor\Reflection\Types\Null_;

class VoucherBookController extends Controller
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

    public function failed_voucher_list(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'page_number' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $logged_in_userdata = User::where('id', $request_data['user_id'])->first();
        // dd($logged_in_userdata);
        if($logged_in_userdata['role'] == config('constants.SuperUser')){
            $voucher = VoucherNumberRegister::select('voucher_number_register.id', 'voucher_number_register.voucher_ref_no', 'company.company_name', 'voucher_number_register.failed_reason', 'voucher_number_register.failed_document', 'voucher_number_register.failed_unique' ,DB::raw('group_concat(voucher_no) as voucher_numbers'),
            'voucher_number_register.accountant_approval_datetime','voucher_number_register.superadmin_approval_datetime','voucher_number_register.failed_request_date', 'voucher_number_register.created_at')
                        ->join('company', 'voucher_number_register.company_id', '=', 'company.id')
                        ->where('voucher_number_register.failed_request_status', "Processing")
                        ->where('voucher_number_register.accountant_status', 'Approved')
                        ->WhereNull('voucher_number_register.superadmin_status')
                        ->groupBy('failed_unique')
                        ->get()->toArray();

        }elseif ($logged_in_userdata['role'] == config('constants.Admin')) {
            $voucher = VoucherNumberRegister::select('voucher_number_register.id', 'voucher_number_register.voucher_ref_no','company.company_name', 'voucher_number_register.failed_reason','voucher_number_register.failed_document', 'voucher_number_register.failed_unique',DB::raw('group_concat(voucher_no) as voucher_numbers'),
            'voucher_number_register.accountant_approval_datetime','voucher_number_register.superadmin_approval_datetime','voucher_number_register.failed_request_date', 'voucher_number_register.created_at')
                    ->join('company', 'voucher_number_register.company_id', '=', 'company.id')
                    ->where('voucher_number_register.failed_request_status', "Processing")
                    ->WhereNull('voucher_number_register.accountant_status')
                    ->groupBy('failed_unique')
                    ->get()->toArray();

        }else{
            /* $voucher = VoucherNumberRegister::select('voucher_number_register.id', 'voucher_number_register.voucher_ref_no', 'company.company_name', 'voucher_number_register.failed_reason', 'voucher_number_register.failed_document', DB::raw('group_concat(voucher_no) as voucher_numbers'))
                    ->join('company', 'voucher_number_register.company_id', '=', 'company.id')
                    ->where('voucher_number_register.is_failed', 1)
                    ->groupBy('failed_unique')
                    ->get()->toArray(); */
            $voucher = [];
        }

        foreach ($voucher as $key => $value) {
            $voucher[$key]['failed_document'] = asset('storage/' . str_replace('public/', '', $value['failed_document']));
        }

        if($voucher){
            return response()->json(['status' => true, 'msg' => "Record Found", 'data' => $voucher]);
        }else{
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }
    }

    public function add_failed_voucher(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'voucher_ref_no' => 'required',
            'voucher_no' => 'required',
            'failed_reason' => 'required',
            'failed_document' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        
        $request_data = $request->all();

        $document_file = Null;
        if ($request->file('failed_document')) {
            $document_file = $request->file('failed_document');
            $original_file_name = explode('.', $document_file->getClientOriginalName());
            $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);

            $file_path = $document_file->storeAs('public/voucher_failed_docs', $new_file_name);
            if ($file_path) {
                $document_file = $file_path;
            }
        }

        $voucher_no = explode(',', $request_data['voucher_no']);
        
        $uniqueId = time().mt_rand(100,999);
        
        $update_array = [
            'failed_request_status' => "Processing",
            'failed_reason' => $request_data['failed_reason'],
            'failed_document' => $document_file,
            'failed_unique' => $uniqueId,
            'updated_ip' => $request->ip(),
            'failed_request_by' => $request_data['user_id'],
            'failed_request_date' => date('Y-m-d H:i:'),
            'accountant_status' => NULL,
            'accountant_approval_id' => NULL,
            'accountant_approval_datetime' => NULL,
            'accountant_reject_reason' => NULL,
            'superadmin_status' => NULL,
            'superadmin_approval_id' => NULL,
            'superadmin_approval_datetime' => NULL,
            'superadmin_reject_reason' => NULL,
        ];
        
        foreach ($voucher_no as $key => $value) {
            VoucherNumberRegister::where('voucher_ref_no',$request_data['voucher_ref_no'])->where('voucher_no',$value)->update($update_array);
        }

        //Notification
        $user_ids = User::where('status', 'Enabled')->where('role', config('constants.Admin'))->pluck('id')->toArray();
        $messages = "Voucher failed request by user";
        $tags = "voucherFailed";
        $this->notification_task->voucherFailedNotify($user_ids, 'Voucher Failed Request', $messages, $tags);

        return response()->json(['status' => true, 'msg' => "Voucher number failing", 'data' => []]);
    }

    public function accept_failed_voucher(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'failed_unique_number' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        
        $request_data = $request->all();
        
        $logged_in_userdata = User::where('id', $request_data['user_id'])->first();

        if ($logged_in_userdata['role'] == config('constants.SuperUser')) {
            $update_array = [
                'superadmin_status' => "Approved",
                'superadmin_approval_id' => $request_data['user_id'],
                'superadmin_approval_datetime' => date('Y-m-d H:i:s'),
                'failed_request_status' => 'Approved',
                'is_failed' => "1"
            ];
        } elseif ($logged_in_userdata['role'] == config('constants.Admin')) {
            $update_array = [
                'accountant_status' => "Approved",
                'accountant_approval_id' => $request_data['user_id'],
                'accountant_approval_datetime' => date('Y-m-d H:i:s'),
            ];
        }
        
        if(VoucherNumberRegister::where('failed_unique',$request_data['failed_unique_number'])->update($update_array)){
            if ($logged_in_userdata['role'] == config('constants.SuperUser')) {
                //Notification
                $users_detail = VoucherNumberRegister::where('failed_unique', $request_data['failed_unique_number'])->first('failed_request_by');
                $messages = "Voucher failed request accepted";
                $tags = "voucherFailed";
                $this->notification_task->voucherFailedNotify([$users_detail['failed_request_by']], 'Voucher Failed Request', $messages, $tags);

            } elseif ($logged_in_userdata['role'] == config('constants.Admin')) {
                //Notification
                $user_ids = User::where('status', 'Enabled')->where('role', config('constants.SuperUser'))->pluck('id')->toArray();
                $messages = "Voucher failed request by admin";
                $tags = "voucherFailed";
                $this->notification_task->voucherFailedNotify($user_ids, 'Voucher Failed Request', $messages, $tags);
            }
            return response()->json(['status' => true, 'msg' => "Voucher number accepted successfully", 'data' => []]);
        }else{
            return response()->json(['status' => false, 'msg' => 'Voucher number not accept try again', 'data' => [], 'error' => config('errors.no_record.code')]);
        }
    }

    public function reject_failed_voucher(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'failed_unique_number' => 'required',
            'reject_reason' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $logged_in_userdata = User::where('id', $request_data['user_id'])->first();

        if ($logged_in_userdata['role'] == config('constants.SuperUser')) {
            $update_array = [
                'superadmin_status' => "Rejected",
                'superadmin_approval_id' => $request_data['user_id'],
                'superadmin_approval_datetime' => date('Y-m-d H:i:s'),
                'superadmin_reject_reason' => $request_data['reject_reason'],
            ];
        } elseif ($logged_in_userdata['role'] == config('constants.Admin')) {
            $update_array = [
                'accountant_status' => "Rejected",
                'accountant_approval_id' => $request_data['user_id'],
                'accountant_approval_datetime' => date('Y-m-d H:i:s'),
                'accountant_reject_reason' => $request_data['reject_reason'],
            ];
        }

        if (VoucherNumberRegister::where('failed_unique', $request_data['failed_unique_number'])->update($update_array)) {
            if ($logged_in_userdata['role'] == config('constants.SuperUser')) {
                //Notification
                $users_detail = VoucherNumberRegister::where('failed_unique', $request_data['failed_unique_number'])->first('failed_request_by');
                $messages = "Voucher failed request rejected by super admin";
                $tags = "voucherFailed";
                $this->notification_task->voucherFailedNotify([$users_detail['failed_request_by']], 'Voucher Failed Request', $messages, $tags);
            } elseif ($logged_in_userdata['role'] == config('constants.Admin')) {
                //Notification
                $users_detail = VoucherNumberRegister::where('failed_unique', $request_data['failed_unique_number'])->first('failed_request_by');
                $messages = "Voucher failed request rejected by admin";
                $tags = "voucherFailed";
                $this->notification_task->voucherFailedNotify([$users_detail['failed_request_by']], 'Voucher Failed Request', $messages, $tags);
            }
            return response()->json(['status' => true, 'msg' => "Voucher number rejected successfully", 'data' => []]);
        } else {
            return response()->json(['status' => false, 'msg' => 'Voucher number not reject try again', 'data' => [], 'error' => config('errors.no_record.code')]);
        }
    }

    public function get_voucher_ref_number(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'company_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();

        $vouchers = AssignedVoucher::where('to_user_id', $request_data['user_id'])->where('status', 'accepted')->pluck('voucher_ref_no');

        /*if($vouchers){
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }*/

        $all_voucher = VoucherNumberRegister::select('*')
            ->where('company_id', $request_data['company_id'])
            ->where('is_failed', 0)
            ->where('is_used', 'not_used')
            ->whereIn('voucher_ref_no', $vouchers)
            ->groupBy('voucher_ref_no')
            ->get()->toArray();

        if (count($all_voucher) == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        return response()->json(['status' => true, 'msg' => "Record Found", 'data' => $all_voucher]);
    }

    public function get_users_list_voucher(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $users = User::select('id', 'name')->where('status', 'Enabled')->whereNotIn('id',[$request->get('user_id')])->get()->toArray();
        if(count($users) == 0){
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        return response()->json(['status' => true, 'msg' => "Record Found", 'data' => $users]);

    }

    public function add_voucher_book(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'company_id' => 'required',
            /* 'client_id' => 'required',
            'project_id' => 'required',
            'project_site_id' => 'required', */
            'assign_user_id' => 'required',
            'voucher_start_number' => 'required',
            'voucher_end_number' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $amCompanyData = Companies::select('company_short_name')->where(['id' => $request->input('company_id')])->get()->toArray();
        $voucher_entry = VoucherNumberRegister::where('company_id', $request->input('company_id'))
        ->whereDate('created_at', date('Y-m-d'))
            ->distinct()
            ->groupBy('voucher_ref_no')->get()->count();

        if ($voucher_entry == 0) {
            $append_no = 1;
        } else {
            $append_no = $voucher_entry + 1;
        }

        $voucher_ref_no = $amCompanyData[0]['company_short_name'] . "/" . date('Y-m-d') . "/" . $append_no;
        for ($i = $request->input('voucher_start_number'); $i <= $request->input('voucher_end_number'); $i++) {
            $voucherModel = new VoucherNumberRegister();
            $voucherModel->company_id = $request->input('company_id');
            /* $voucherModel->client_id = $request->input('client_id');
            $voucherModel->project_id = $request->input('project_id');
            $voucherModel->project_site_id = $request->input('project_site_id'); */
            $voucherModel->voucher_ref_no = $voucher_ref_no;
            $voucherModel->voucher_no      = $i; // increment i ++;
            $voucherModel->created_at = date('Y-m-d h:i:s');
            $voucherModel->created_ip = $request->ip();
            $voucherModel->updated_at = date('Y-m-d h:i:s');
            $voucherModel->save();
        }

        if (!empty($voucherModel)) {

            $assign_arr = [
                'parent_voucher' => 0,
                'voucher_ref_no' => $voucher_ref_no,
                'from_user_id' => 0,
                'to_user_id' => $request->input('user_id'),
                'status' => 'submitted',
                'created_ip' => $request->ip(),
                'updated_ip' => $request->ip(),
            ];

            $last_id = AssignedVoucher::insertGetId($assign_arr);

            $assign_arr2 = [
                'parent_voucher' => $last_id,
                'voucher_ref_no' => $voucher_ref_no,
                'from_user_id' => $request->input('user_id'),
                'to_user_id' => $request->input('assign_user_id'),
                'status' => 'assigned',
                'created_ip' => $request->ip(),
                'updated_ip' => $request->ip(),
            ];

            AssignedVoucher::insert($assign_arr2);

            //Notification
            $messages = "Voucher assigned. So please Login to your account for more details";
            $tags = "voucherAssign";
            $this->notification_task->voucherAssignNotify([$request->input('user_id')], 'Voucher Assigned', $messages, $tags);
            //Email
            $users_email = User::whereIn('id', [$request->input('user_id')])->pluck('email')->toArray();
            $mail_data = [];
            $mail_data['name'] = "";
            $mail_data['email_list'] = $users_email;
            $this->common_task->voucher_assigned($mail_data);

            return response()->json(['status' => true, 'msg' => "New voucher number added successfully.", 'data' => []]);
        } else {
            return response()->json(['status' => false, 'msg' => "Error occurre in insert. Try Again!", 'data' => [], 'error' => config('errors.no_record.code')]);
        }
    }

    public function assign_voucher_number_list(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $assign_voucher = AssignedVoucher::select('assigned_voucher.*', 'users.name as assigned_by', 'company.id as company_id','company.company_name', 'clients.id as client_id' ,'clients.client_name', 'project.id as project_id' ,'project.project_name', 'project_sites.id as project_site_id' ,'project_sites.site_name')
                        ->leftJoin('users', 'assigned_voucher.from_user_id', 'users.id')
                        ->leftJoin('voucher_number_register', 'assigned_voucher.voucher_ref_no', 'voucher_number_register.voucher_ref_no')
                        ->join('company', 'voucher_number_register.company_id', '=', 'company.id')
                        ->leftJoin('clients', 'voucher_number_register.client_id', '=', 'clients.id')
                        ->leftJoin('project', 'voucher_number_register.project_id', '=', 'project.id')
                        ->leftJoin('project_sites', 'voucher_number_register.project_site_id', '=', 'project_sites.id')
                        ->where('to_user_id', $request->input('user_id'))
                        ->where('assigned_voucher.status', 'assigned')
                        // ->whereNotIn('from_user_id', [0])
                        ->whereRaw('parent_voucher IN (select MAX(parent_voucher) FROM assigned_voucher GROUP BY voucher_ref_no)')
                        ->groupBy('voucher_ref_no')
                        ->orderBy('created_at','DESC')
                        ->get()->toArray();

        foreach ($assign_voucher as $key => $value) {
            $assign_voucher[$key]['voucher_numbers'] = $this->get_voucher_numbers($value['voucher_ref_no']);
        }
        if($assign_voucher){
            return response()->json(['status' => true, 'msg' => "Record Found", 'data' => $assign_voucher]);
        }else{
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }
    }

    public function my_voucher_book_list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $assign_voucher = AssignedVoucher::select('assigned_voucher.*', 'users.name as assigned_by', 'company.id as company_id', 'company.company_name', 'clients.id as client_id', 'clients.client_name', 'project.id as project_id', 'project.project_name', 'project_sites.id as project_site_id', 'project_sites.site_name')
            ->leftJoin('users', 'assigned_voucher.from_user_id', 'users.id')
            ->leftJoin('voucher_number_register', 'assigned_voucher.voucher_ref_no', 'voucher_number_register.voucher_ref_no')
            ->join('company', 'voucher_number_register.company_id', '=', 'company.id')
            ->leftJoin('clients', 'voucher_number_register.client_id', '=', 'clients.id')
            ->leftJoin('project', 'voucher_number_register.project_id', '=', 'project.id')
            ->leftJoin('project_sites', 'voucher_number_register.project_site_id', '=', 'project_sites.id')
            ->where('to_user_id', $request->input('user_id'))
            ->where('assigned_voucher.status', 'accepted')
            ->whereNotIn('from_user_id', [0])
            ->groupBy('voucher_ref_no')
            ->orderBy('id', 'DESC')
            ->get()->toArray();

        foreach ($assign_voucher as $key => $value) {
            $assign_voucher[$key]['voucher_numbers'] = $this->get_voucher_numbers($value['voucher_ref_no']);
        }
        if ($assign_voucher) {
            return response()->json(['status' => true, 'msg' => "Record Found", 'data' => $assign_voucher]);
        } else {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }
    }

    public function get_voucher_numbers($ref)
    {
        $new_arr = VoucherNumberRegister::where('voucher_ref_no', $ref)->where('is_used', 'not_used')->where('is_failed', 0)->orderBy('voucher_no', 'asc')->pluck('voucher_no')->toArray();
        if ($new_arr) {
            return implode(',', $new_arr) ;
        } else {
            return "";
        }
    }

    public function assign_voucher_touser(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'assign_user_id' => 'required',
            'assigned_voucher_id' => 'required',
            /* 'client_id' => 'required',
            'project_id' => 'required',
            'project_site_id' => 'required', */
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        
        $assigned_voucher = AssignedVoucher::whereId($request->get('assigned_voucher_id'))->first();
        
        if($assigned_voucher){

            /* if(!empty($request->get('client_id')) &&  !empty($request->get('project_id')) && !empty($request->get('project_id'))){
                $edit_array = [
                    'client_id' => $request->get('client_id'),
                    'project_id' => $request->get('project_id'),
                    'project_site_id' => $request->get('project_site_id'),
                ];
                VoucherNumberRegister::where('voucher_ref_no', $assigned_voucher['voucher_ref_no'])->where('is_used', 'not_used')->where('is_failed', 0)->update($edit_array);
            } */
            $old_voucher = [
                'status' => "submitted",
                'updated_ip' => $request->ip(),
            ];

            if (AssignedVoucher::whereId($request->get('assigned_voucher_id'))->update($old_voucher)) {
                $new_voucher = [
                    'parent_voucher' => $request->get('assigned_voucher_id'),
                    'voucher_ref_no' => $assigned_voucher['voucher_ref_no'],
                    'from_user_id' => $assigned_voucher['to_user_id'],
                    'to_user_id' => $request->get('assign_user_id'),
                    'status' => "assigned",
                    'created_ip' => $request->ip(),
                    'updated_ip' => $request->ip(),
                ];

                AssignedVoucher::insert($new_voucher);

                //Notification
                $messages = "Voucher assigned. So please Login to your account for more details";
                $tags = "voucherAssign";
                $this->notification_task->voucherAssignNotify([$request->input('to_user_id')], 'Voucher Assigned', $messages, $tags);

                //Email
                $users_email = User::whereIn('id', [$request->input('to_user_id')])->pluck('email')->toArray();
                $mail_data = [];
                $mail_data['name'] = "";
                $mail_data['email_list'] = $users_email;
                $this->common_task->voucher_assigned($mail_data);

                return response()->json(['status' => true, 'msg' => "Assigned voucher successfully.", 'data' => []]);
            } else {
                return response()->json(['status' => false, 'msg' => "Error occurre in insert. Try Again!", 'data' => [], 'error' => config('errors.no_record.code')]);
            }

        }else{
            return response()->json(['status' => false, 'msg' => "Error occurre in insert. Try Again!", 'data' => [], 'error' => config('errors.no_record.code')]);
        }

    }

    public function accept_voucher_user(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'accepted_voucher_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $assigned_voucher = AssignedVoucher::whereId($request->get('accepted_voucher_id'))->first();
        if($assigned_voucher){

            $update_arr = [
                'status' => 'accepted',
                'created_ip' => \Request::ip(),
                'updated_ip' => \Request::ip(),
            ];
            if (AssignedVoucher::whereId($request->get('accepted_voucher_id'))->update($update_arr)) {

                //Notification
                $messages = "Voucher accepted by user";
                $tags = "voucherAccepted";
                $this->notification_task->voucherAssignNotify([$assigned_voucher['from_user_id']], 'Voucher Accepted', $messages, $tags);

                //Email
                $users_email = User::whereIn('id', [$assigned_voucher['from_user_id']])->pluck('email')->toArray();
                $users_name = User::whereIn('id', [$assigned_voucher['from_user_id']])->first();
                $mail_data = [];
                $mail_data['name'] = $users_name['name'];
                $mail_data['email_list'] = $users_email;
                $this->common_task->voucher_accepted($mail_data);

                return response()->json(['status' => true, 'msg' => "Voucher accepted successfully.", 'data' => []]);
            } else {
                return response()->json(['status' => false, 'msg' => "Error occurre in insert. Try Again!", 'data' => [], 'error' => config('errors.no_record.code')]);
            }
        }else{
            return response()->json(['status' => false, 'msg' => "Error occurre in insert. Try Again!", 'data' => [], 'error' => config('errors.no_record.code')]);
        }
    }

    public function reject_voucher_user(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'accepted_voucher_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $assigned_voucher = AssignedVoucher::whereId($request->get('accepted_voucher_id'))->first();
        if($assigned_voucher){
            $update_arr = [
                'status' => 'rejected',
                'created_ip' => \Request::ip(),
                'updated_ip' => \Request::ip(),
            ];

            if (AssignedVoucher::whereId($request->get('accepted_voucher_id'))->update($update_arr)) {

                $old_update_arr = [
                    'status' => 'accepted',
                    'created_ip' => \Request::ip(),
                    'updated_ip' => \Request::ip(),
                ];

                // AssignedVoucher::where('voucher_ref_no',$assigned_voucher['voucher_ref_no'])->where('to_user_id',$assigned_voucher['from_user_id'])->where('status','submitted')->update($old_update_arr);
                AssignedVoucher::where('id', $assigned_voucher['parent_voucher'])->update($old_update_arr);

                //Notification
                $messages = "Voucher rejected by user";
                $tags = "voucherRejected";
                $this->notification_task->voucherAssignNotify([$assigned_voucher['from_user_id']], 'Voucher Rejected', $messages, $tags);

                //Email
                $users_email = User::whereIn('id', [$assigned_voucher['from_user_id']])->pluck('email')->toArray();
                $users_name = User::whereIn('id', [$assigned_voucher['from_user_id']])->first();
                $mail_data = [];
                $mail_data['name'] = $users_name['name'];
                $mail_data['email_list'] = $users_email;
                $this->common_task->voucher_rejected($mail_data);

                return response()->json(['status' => true, 'msg' => "Voucher rejected successfully.", 'data' => []]);
            } else {
                return response()->json(['status' => false, 'msg' => "Error occurre in insert. Try Again!", 'data' => [], 'error' => config('errors.no_record.code')]);
            }

        }else{
            return response()->json(['status' => false, 'msg' => "Error occurre in insert. Try Again!", 'data' => [], 'error' => config('errors.no_record.code')]);
        }
    }

}
