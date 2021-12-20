<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Lib\NotificationTask;
use App\Lib\CommonTask;
use Illuminate\Support\Facades\Input;
use App\Common_query;
use App\Companies;
use App\Banks;
use App\User;
use App\VoucherNumberRegister;
use App\AssignedVoucher;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Lib\Permissions;
use DB;

class VoucherNumberRegisterController extends Controller
{
    public $data;
    public $common_task;
    private $notification_task;

    public function __construct() {
        $this->data['module_title'] = "Voucher Book";
        $this->data['module_link'] = "admin.voucher_number_book";
        $this->notification_task = new NotificationTask();
        $this->common_task = new CommonTask();
    }

    public function index() {
        $this->data['page_title'] = "Blank Voucher Book";
        $this->data['blank_list'] = $this->get_blank_voucher_number_query();
        // dd($this->data['blank_list']);
        $this->data['add_permission'] = Permissions::checkPermission(62, 3);
        return view('admin.voucher_number_book.index', $this->data);
    }

    public function get_blank_voucher_number_list(){
        $datatable_fields = array('voucher_ref_no','voucher_no','company.company_name','bank.bank_name');
        $request = Input::all();
        $conditions_array = ['voucher_number_register.is_used' => 'not_used', 'voucher_number_register.is_failed' => 0];

        $getfiled = array('voucher_number_register.id','company.company_name','bank.bank_name','bank.ac_number','voucher_no','voucher_ref_no','is_used','project.project_name','amount','work_detail','voucher_number_register.created_at');
        $table = "voucher_number_register";
        $join_str=[];
        $join_str[0]['join_type']='';
        $join_str[0]['table'] = 'company';
        $join_str[0]['join_table_id'] ='voucher_number_register.company_id';
        $join_str[0]['from_table_id'] = 'company.id';

        $join_str[1]['join_type']='';
        $join_str[1]['table'] = 'bank';
        $join_str[1]['join_table_id'] ='voucher_number_register.bank_id';
        $join_str[1]['from_table_id'] = 'bank.id';


        $join_str[3]['join_type']='left';
        $join_str[3]['table'] = 'project';
        $join_str[3]['join_table_id'] ='voucher_number_register.project_id';
        $join_str[3]['from_table_id'] = 'project.id';

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);
        die();
    }

    public function get_blank_voucher_number_query(){
        $all_rtgs = VoucherNumberRegister::select('*')
                ->where('is_failed',0)
                ->where('is_used' ,'not_used')
                ->groupBy('voucher_ref_no')
                ->get()->toArray();
                 $rtgs_group = [];
        if($all_rtgs){
        foreach($all_rtgs as $one_rtgs){
             $blank_rtgs = VoucherNumberRegister::select('voucher_number_register.id','voucher_number_register.voucher_ref_no','voucher_number_register.voucher_no','company.company_name', 'clients.client_name', 'project.project_name', 'project_sites.site_name')
                ->join('company', 'voucher_number_register.company_id', '=', 'company.id')
                ->leftJoin('clients', 'voucher_number_register.client_id', '=', 'clients.id')
                ->leftJoin('project', 'voucher_number_register.project_id', '=', 'project.id')
                ->leftJoin('project_sites', 'voucher_number_register.project_site_id', '=', 'project_sites.id')
                ->where('is_failed',0)
                ->where('is_used' ,'not_used')
                ->orderBy('voucher_no', 'ASC')
                ->where('voucher_ref_no',$one_rtgs['voucher_ref_no'])
                ->get()->toArray();

        $arr_group = [];

        $start_pointer = $blank_rtgs[0]['voucher_no'];
        foreach ($blank_rtgs as $key => $value) {
                if($start_pointer == $value['voucher_no']){
                    $arr_group[] = $value;
                    $start_pointer++;
                }else{
                    array_push($rtgs_group,$arr_group);
                    $arr_group = [];
                    $start_pointer = $value['voucher_no']+1;
                    $arr_group[] = $value;
                }

        }
        if(!empty($arr_group)){
            array_push($rtgs_group,$arr_group);
        }
        }
        return $rtgs_group;
        }else{
            return [];
        }
    }

    public function add_voucher_number(){
        $this->data['module_title'] = "Blank Voucher Book";
        $this->data['page_title']      = 'Add Voucher Book';
        $this->data['companies']       = Companies::orderBy('company_name', 'asc')->pluck('company_name','id');
        $this->data['banks']           = Banks::pluck('bank_name','id');
        $this->data['users']= User::whereStatus("Enabled")->orderBy('name')->pluck('name','id');
        return view('admin.voucher_number_book.add_voucher_number', $this->data);
    }

    public function insert_voucher_number(Request $request){
        // dd($request->all());
        $validator_normal = Validator::make($request->all(), [
            'company_id' => 'required',
            'voucher_start_number' => 'required',
            'voucher_end_number' => 'required',
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.voucher_number_book')->with('error', 'Please follow validation rules.');
        }

        $amCompanyData = Companies::select('company_short_name')->where(['id' => $request->input('company_id')])->get()->toArray();

        $voucher_entry = VoucherNumberRegister::where('company_id',$request->input('company_id'))
               ->whereDate('created_at',date('Y-m-d'))
               ->distinct()
               ->groupBy('voucher_ref_no')->get()->count();

        if ($voucher_entry == 0) {
            $append_no = 1;
        } else {
            $append_no = $voucher_entry + 1;
        }

        $voucher_ref_no = $amCompanyData[0]['company_short_name']."/".date('Y-m-d')."/".$append_no;
        // dd($voucher_ref_no);
        // dd($request->all());

        for($i=$request->input('voucher_start_number');$i<=$request->input('voucher_end_number');$i++) {
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
                'to_user_id' => Auth::user()->id,
                'status' => 'submitted',
                'created_ip' => $request->ip(),
                'updated_ip' => $request->ip(),
            ];

            $last_id = AssignedVoucher::insertGetId($assign_arr);

            $assign_arr2 = [
                'parent_voucher' => $last_id,
                'voucher_ref_no' => $voucher_ref_no,
                'from_user_id' => Auth::user()->id,
                'to_user_id' => $request->input('user_id'),
                'status' => 'assigned',
                'created_ip' => $request->ip(),
                'updated_ip' => $request->ip(),
            ];

            AssignedVoucher::insert($assign_arr2);

            //Notification
            $messages = "Voucher assigned. So please Login to your account for more details";
            $tags = "voucherAssign";
            $this->notification_task->voucherAssignNotify([$request->input('user_id')], 'Voucher Assigned', $messages,$tags);
            //Email
            $users_email = User::whereIn('id', [$request->input('user_id')])->pluck('email')->toArray();
            $mail_data = [];
            $mail_data['name'] = "";
            $mail_data['email_list'] = $users_email;
            $this->common_task->voucher_assigned($mail_data);

            return redirect()->route('admin.voucher_number_book')->with('success', 'New voucher number added successfully.');
        } else {
            return redirect()->route('admin.add_voucher_number')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function used_voucher_number(){
        $this->data['page_title'] = "Used Voucher Number";
        return view('admin.voucher_number_book.used_voucher_number', $this->data);
    }

    public function get_used_voucher_number_list(){
        $datatable_fields = array('voucher_number_register.voucher_ref_no','voucher_number_register.voucher_no','company.company_name','employee_expense.expense_code');
        $request = Input::all();
        $conditions_array = ['voucher_number_register.is_used' => 'used', 'voucher_number_register.is_failed' => 0];

        $getfiled = array('voucher_number_register.id','company.company_name','voucher_number_register.voucher_no','voucher_number_register.voucher_ref_no','project.project_name','employee_expense.expense_code', 'cash_approval.entry_code');
        $table = "voucher_number_register";
        $join_str=[];
        $join_str[0]['join_type']='';
        $join_str[0]['table'] = 'company';
        $join_str[0]['join_table_id'] ='voucher_number_register.company_id';
        $join_str[0]['from_table_id'] = 'company.id';

        $join_str[1]['join_type']='';
        $join_str[1]['table'] = 'project';
        $join_str[1]['join_table_id'] ='voucher_number_register.project_id';
        $join_str[1]['from_table_id'] = 'project.id';

        $join_str[2]['join_type']= 'left';
        $join_str[2]['table'] = 'employee_expense';
        $join_str[2]['join_table_id'] ='voucher_number_register.expense_id';
        $join_str[2]['from_table_id'] = 'employee_expense.id';

        $join_str[3]['join_type'] = 'left';
        $join_str[3]['table'] = 'cash_approval';
        $join_str[3]['join_table_id'] = 'voucher_number_register.cash_approval_id';
        $join_str[3]['from_table_id'] = 'cash_approval.id';

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);
        die();
    }

    public function failed_voucher_number(){
        $this->data['page_title'] = "Failed Voucher Number";
        $this->data['failed_voucher_number'] = $this->get_failed_voucher_number_list();
        // dd($this->data['failed_voucher_number']);
        return view('admin.voucher_number_book.failed_voucher_number', $this->data);
    }

    public function get_failed_voucher_number_list(){
        $voucher = VoucherNumberRegister::select('voucher_number_register.id', 'users.name' ,'voucher_number_register.voucher_ref_no', 'company.company_name', 'voucher_number_register.failed_reason', 'voucher_number_register.failed_document', 'voucher_number_register.accountant_status', 'voucher_number_register.superadmin_status', 'voucher_number_register.failed_request_status', 'voucher_number_register.failed_request_date',DB::raw('group_concat(voucher_no) as voucher_numbers'))
                ->join('company', 'voucher_number_register.company_id', '=', 'company.id')
                ->leftJoin('users', 'voucher_number_register.failed_request_by', '=', 'users.id')
                ->where('voucher_number_register.is_failed', 1)
                ->groupBy('failed_unique')
                ->get()->toArray();
        return $voucher;
    }

    public function assign_voucher_number(){
        $this->data['page_title'] = "Assigned Voucher Book";
        $this->data['users']= User::whereStatus("Enabled")->whereNotIn('id',[Auth::user()->id])->pluck('name','id');
        $this->data['assign_voucher'] = $this->assign_voucher_number_list();
        // dd($this->data['assign_voucher']);
        return view('admin.voucher_number_book.assign_voucher_number', $this->data);
    }

    public function assign_voucher_number_list(){
        $assign_voucher = AssignedVoucher::select('assigned_voucher.*', 'users.name as assigned_by', 'company.id as company_id', 'company.company_name', 'clients.id as client_id', 'clients.client_name', 'project.id as project_id', 'project.project_name', 'project_sites.id as project_site_id', 'project_sites.site_name')
                            ->leftJoin('users', 'assigned_voucher.from_user_id', 'users.id')
                            ->leftJoin('voucher_number_register', 'assigned_voucher.voucher_ref_no', 'voucher_number_register.voucher_ref_no')
                            ->join('company', 'voucher_number_register.company_id', '=', 'company.id')
                            ->leftJoin('clients', 'voucher_number_register.client_id', '=', 'clients.id')
                            ->leftJoin('project', 'voucher_number_register.project_id', '=', 'project.id')
                            ->leftJoin('project_sites', 'voucher_number_register.project_site_id', '=', 'project_sites.id')
                            ->where('to_user_id',Auth::user()->id)
                            // ->whereNotIn('from_user_id', [0])
                            ->whereRaw('parent_voucher IN (select MAX(parent_voucher) FROM assigned_voucher GROUP BY voucher_ref_no)')
                            ->groupBy('voucher_ref_no')
                            ->get()->toArray();

        foreach ($assign_voucher as $key => $value) {
            $assign_voucher[$key]['voucher_numbers'] = $this->get_voucher_numbers($value['voucher_ref_no']);
        }
        return $assign_voucher;
    }

    public function get_voucher_numbers($ref){
        $new_arr = VoucherNumberRegister::where('voucher_ref_no',$ref)->where('is_used','not_used')->where('is_failed',0)->orderBy('voucher_no','asc')->pluck('voucher_no')->toArray();
        if($new_arr){
            return $new_arr;
        }else{
            return [];
        }
    }

    public function assign_voucher_touser(Request $request){
        // dd($request->all());
        $assigned_voucher = AssignedVoucher::whereId($request->get('assigned_voucher_id'))->first();

        /* if (!empty($request->get('client_id')) &&  !empty($request->get('project_id')) && !empty($request->get('project_id'))) {
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
        if(AssignedVoucher::whereId($request->get('assigned_voucher_id'))->update($old_voucher))
        {
            $new_voucher = [
                'parent_voucher' => $request->get('assigned_voucher_id'),
                'voucher_ref_no' => $assigned_voucher['voucher_ref_no'],
                'from_user_id' => $assigned_voucher['to_user_id'],
                'to_user_id' => $request->get('to_user_id'),
                'status' => "assigned",
                'created_ip' => $request->ip(),
                'updated_ip' => $request->ip(),
            ];

            AssignedVoucher::insert($new_voucher);

            //Notification
            $messages = "Voucher assigned. So please Login to your account for more details";
            $tags = "voucherAssign";
            $this->notification_task->voucherAssignNotify([$request->input('to_user_id')], 'Voucher Assigned', $messages,$tags);

            //Email
            $users_email = User::whereIn('id', [$request->input('to_user_id')])->pluck('email')->toArray();
            $mail_data = [];
            $mail_data['name'] = "";
            $mail_data['email_list'] = $users_email;
            $this->common_task->voucher_assigned($mail_data);

            return redirect()->route('admin.assign_voucher_number')->with('success', 'Assigned voucher successfully.');
        }else{
            return redirect()->route('admin.assign_voucher_number')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function accept_voucher_user($id){
        $assigned_voucher = AssignedVoucher::whereId($id)->first();
        if($assigned_voucher){
            // dd($assigned_voucher);

            $update_arr = [
                'status' => 'accepted',
                'created_ip' => \Request::ip(),
                'updated_ip' => \Request::ip(),
            ];
            if(AssignedVoucher::whereId($id)->update($update_arr)){

                //Notification
                $messages = "Voucher accepted by user";
                $tags = "voucherAccepted";
                $this->notification_task->voucherAssignNotify([$assigned_voucher['from_user_id']], 'Voucher Accepted', $messages,$tags);

                //Email
                $users_email = User::whereIn('id', [$assigned_voucher['from_user_id']])->pluck('email')->toArray();
                $users_name = User::whereIn('id', [$assigned_voucher['from_user_id']])->first();
                $mail_data = [];
                $mail_data['name'] = $users_name['name'];
                $mail_data['email_list'] = $users_email;
                $this->common_task->voucher_accepted($mail_data);

                return redirect()->route('admin.assign_voucher_number')->with('success', 'Voucher accepted successfully.');
            }else{
                return redirect()->route('admin.assign_voucher_number')->with('error', 'Error occurre in insert. Try Again!');
            }
        }else{
            return redirect()->route('admin.assign_voucher_number')->with('error', 'Error occurre in insert. Try Again!');
        }
    }

    public function reject_voucher_user($id){
        // dd($id);
        $assigned_voucher = AssignedVoucher::whereId($id)->first();
        if($assigned_voucher){
            // dd($assigned_voucher);

            $update_arr = [
                'status' => 'rejected',
                'created_ip' => \Request::ip(),
                'updated_ip' => \Request::ip(),
            ];
            if(AssignedVoucher::whereId($id)->update($update_arr)){

                $old_update_arr = [
                    'status' => 'accepted',
                    'created_ip' => \Request::ip(),
                    'updated_ip' => \Request::ip(),
                ];

                // AssignedVoucher::where('voucher_ref_no',$assigned_voucher['voucher_ref_no'])->where('to_user_id',$assigned_voucher['from_user_id'])->where('status','submitted')->update($old_update_arr);
                AssignedVoucher::where('id',$assigned_voucher['parent_voucher'])->update($old_update_arr);

                //Notification
                $messages = "Voucher rejected by user";
                $tags = "voucherRejected";
                $this->notification_task->voucherAssignNotify([$assigned_voucher['from_user_id']], 'Voucher Rejected', $messages,$tags);

                //Email
                $users_email = User::whereIn('id', [$assigned_voucher['from_user_id']])->pluck('email')->toArray();
                $users_name = User::whereIn('id', [$assigned_voucher['from_user_id']])->first();
                $mail_data = [];
                $mail_data['name'] = $users_name['name'];
                $mail_data['email_list'] = $users_email;
                $this->common_task->voucher_rejected($mail_data);

                return redirect()->route('admin.assign_voucher_number')->with('success', 'Voucher rejected successfully.');
            }else{
                return redirect()->route('admin.assign_voucher_number')->with('error', 'Error occurre in insert. Try Again!');
            }
        }else{
            return redirect()->route('admin.assign_voucher_number')->with('error', 'Error occurre in insert. Try Again!');
        }
    }
}
