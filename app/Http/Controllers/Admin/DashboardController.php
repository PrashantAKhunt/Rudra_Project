<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\User;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use App\Matches;
use App\Employees;
use App\Employee_education;
use App\Employee_experience;
use App\Employee_reference;
USE App\IdentityDocument;
use App\Department;
use App\Holiday;
use App\Lib\CommonTask;
use App\Employee_expense;
use App\AttendanceDetail;
use App\Common_query;
use App\Companies;
use App\Leaves;
use App\Lib\NotificationTask;
use App\ProSignLetter;
use App\SoftcopyDocumentCategory;
use App\SoftcopyRequest;
use Google\Http\REST;

use function GuzzleHttp\Promise\all;

class DashboardController extends Controller {

    public $data;
    private $module_id = 4;
    private $common_task;
    private $notification_task;

    public function __construct() {

        $this->data['module_title'] = "Dashboard";
        $this->data['module_link'] = "admin.dashboard";
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();
    }



    public function index() {
        $notification_list = $this->notification_task->getUnreadNotificationByUser(Auth::user()->id);
        $all_notify_list = [];
        if (!empty($notification_list)) {
            foreach ($notification_list as $key => $notification) {
                $notification_data = $notification->data;
                $type_arr = explode(',', $notification_data['type']);
                $this->notification_task->markReadNotification($notification);
                if (isset($type_arr[1]) && $type_arr[1] == 'Dashboard') {
                    if (isset($notification_data['message'])) {
                        $all_notify_list[$key]['message'] = $notification_data['message'];
                    } else {
                        $all_notify_list[$key]['message'] = "Alert";
                    }
                    if (isset($notification_data['title'])) {
                        $all_notify_list[$key]['title'] = $notification_data['title'];
                    } else {
                        $all_notify_list[$key]['title'] = "Alert";
                    }
                    if (isset($notification_data['tag']) && $notification_data['tag']!="") {
                        $all_notify_list[$key]['tag'] = $notification_data['tag'];
                    } else {
                        $all_notify_list[$key]['tag'] = "";
                    }
                }
            }
        }

        $this->data['all_notify_list'] = $all_notify_list;
        $user_id = Auth::user()->id;
        $this->data['user_count'] = User::get()->count();

        ///////////////////////////////// Graph Details ///////////////////////

        $this->data['graphDetails'] = AttendanceDetail::join('attendance_master', 'attendance_master.id', '=', 'attendance_detail.attendance_master_id')
                        ->leftJoin('users', 'users.id', '=', 'attendance_master.user_id')
                        ->where('attendance_detail.device_type', 'MOBILE')
                        ->whereDate('time', '=', date('Y-m-d'))
                        ->get()->toArray();


        $this->data['leave_viewall_permission'] = \App\Lib\Permissions::checkPermission(4, 5);
        $this->data['attendance_approval_viewall_permission'] = \App\Lib\Permissions::checkPermission(20, 5);
        $loggedin_user_id = Auth::user()->id;
        //get all announcements
        $this->data['announcement_list'] = \App\Announcements::whereDate('start_date', '<=', date('Y-m-d'))
                        ->whereDate('end_date', '>=', date('Y-m-d'))
                        ->whereRaw("FIND_IN_SET($loggedin_user_id,user_id)")
                        ->where('show_announcement', 1)->get();

        $letter_head_delivery_full_view_permission = \App\Lib\Permissions::checkPermission(29, 5);
        $letter_head_delivery_edit_permission = \App\Lib\Permissions::checkPermission(29, 2);
        if(($letter_head_delivery_full_view_permission || $letter_head_delivery_edit_permission) && Auth::user()->role!=config('constants.SuperUser')){
            $this->data['pre_letter_head_delivery_list']= \App\PreSignLetter::join('users','users.id','=','pre_sign_letter.user_id')
                    ->join('company','company.id','=','pre_sign_letter.company_id')
                    ->where(['pre_sign_letter.status'=>'Approved','is_deliver_status'=>'In-Process'])
                    ->where('assign_letter_user_id', Auth::user()->id)->get(['pre_sign_letter.*','users.name'])->toArray();
            $this->data['letter_head_delivery_list']= \App\ProSignLetter::join('users','users.id','=','pro_sign_letter.user_id')
                    ->join('company','company.id','=','pro_sign_letter.company_id')
                    ->where(['pro_sign_letter.status'=>'Approved','is_deliver_status'=>'In-Process'])
                    ->where('assign_letter_user_id', Auth::user()->id)->get(['pro_sign_letter.*','users.name'])->toArray();
        }
        else{
            $this->data['pre_letter_head_delivery_list']=[];
            $this->data['letter_head_delivery_list']=[];
        }

        //---------------------------------------------------------- Bank payment
        $get_fields = array(
               'bank_payment_approval.id',
            'bank_payment_approval.payment_options',
            'budget_sheet_approval.budhet_sheet_no',
                'bank_payment_approval.entry_code',
                'users.name',
                'company.company_name',
                'clients.client_name',
                'project.project_name',
                    'bank_payment_approval.other_project_detail',
                    'project_sites.site_name',
                'bank_payment_approval.total_amount',
                    'bank_payment_approval.amount',
                    'bank_payment_approval.created_at'
        );
        if (Auth::user()->role == config('constants.ACCOUNT_ROLE')) {
            $this->data['bank_payment_list'] = \App\BankPaymentApproval::join('users', 'bank_payment_approval.user_id', '=', 'users.id')
                ->join('company', 'company.id', '=', 'bank_payment_approval.company_id')
                ->join('project', 'project.id', '=', 'bank_payment_approval.project_id')
                ->leftJoin('budget_sheet_approval', 'budget_sheet_approval.id', '=', 'bank_payment_approval.budget_sheet_id')
                ->leftJoin('clients', 'clients.id', '=', 'bank_payment_approval.client_id')
                ->leftJoin('project_sites', 'project_sites.id', '=', 'bank_payment_approval.project_site_id')
                ->where('bank_payment_approval.first_approval_status', 'Pending')
                ->orderBy('bank_payment_approval.created_at', 'DESC')
                ->get($get_fields)->toArray();

        } elseif (Auth::user()->role == config('constants.Admin')) {
            $this->data['bank_payment_list'] = \App\BankPaymentApproval::join('users', 'bank_payment_approval.user_id', '=', 'users.id')
                ->join('company', 'company.id', '=', 'bank_payment_approval.company_id')
                ->join('project', 'project.id', '=', 'bank_payment_approval.project_id')
                ->leftJoin('budget_sheet_approval', 'budget_sheet_approval.id', '=', 'bank_payment_approval.budget_sheet_id')
                ->leftJoin('clients', 'clients.id', '=', 'bank_payment_approval.client_id')
                ->leftJoin('project_sites', 'project_sites.id', '=', 'bank_payment_approval.project_site_id')
                ->where('bank_payment_approval.first_approval_status', 'Approved')
                ->where('bank_payment_approval.second_approval_status', 'Pending')
                ->orderBy('bank_payment_approval.created_at', 'DESC')
                ->get($get_fields)->toArray();

        } elseif (Auth::user()->role == config('constants.SuperUser')) {
            $this->data['bank_payment_list'] = \App\BankPaymentApproval::join('users', 'bank_payment_approval.user_id', '=', 'users.id')
                ->join('company', 'company.id', '=', 'bank_payment_approval.company_id')
                ->join('project', 'project.id', '=', 'bank_payment_approval.project_id')
                ->leftJoin('budget_sheet_approval', 'budget_sheet_approval.id', '=', 'bank_payment_approval.budget_sheet_id')
                ->leftJoin('clients', 'clients.id', '=', 'bank_payment_approval.client_id')
                ->leftJoin('project_sites', 'project_sites.id', '=', 'bank_payment_approval.project_site_id')
                ->where('bank_payment_approval.first_approval_status', 'Approved')
                ->where('bank_payment_approval.second_approval_status', 'Approved')
                ->where('bank_payment_approval.third_approval_status', 'Pending')
                ->where('bank_payment_approval.status', 'Pending')
                ->orderBy('bank_payment_approval.created_at', 'DESC')
                ->get($get_fields)->toArray();

        } else {
            $this->data['bank_payment_list'] = [];
        }

        //------------------------------------------------------------- Cash payment
        $get_fields = array(
               'cash_approval.id',
            'cash_approval.payment_options',
            'budget_sheet_approval.budhet_sheet_no',
                'users.name',
                'company.company_name',
                'clients.client_name',
                'project.project_name',
                'cash_approval.other_cash_detail',
                'project_sites.site_name',
                'cash_approval.amount',
                'cash_approval.created_at'
        );
        if (Auth::user()->role == config('constants.Admin')) {
            $this->data['cash_payment_list'] = \App\CashApproval::join('users', 'cash_approval.user_id', '=', 'users.id')
                ->join('company', 'company.id', '=', 'cash_approval.company_id')
                ->join('project', 'project.id', '=', 'cash_approval.project_id')
                ->leftJoin('budget_sheet_approval', 'budget_sheet_approval.id', '=', 'cash_approval.budget_sheet_id')
                ->leftJoin('clients', 'clients.id', '=', 'cash_approval.client_id')
                ->leftJoin('project_sites', 'project_sites.id', '=', 'cash_approval.project_site_id')
                ->where('cash_approval.first_approval_status', 'Pending')
                ->orderBy('cash_approval.created_at', 'DESC')
                ->get($get_fields)->toArray();

        } elseif (Auth::user()->role == config('constants.ACCOUNT_ROLE')) {
            $this->data['cash_payment_list'] = \App\CashApproval::join('users', 'cash_approval.user_id', '=', 'users.id')
                ->join('company', 'company.id', '=', 'cash_approval.company_id')
                ->join('project', 'project.id', '=', 'cash_approval.project_id')
                ->leftJoin('budget_sheet_approval', 'budget_sheet_approval.id', '=', 'cash_approval.budget_sheet_id')
                ->leftJoin('clients', 'clients.id', '=', 'cash_approval.client_id')
                ->leftJoin('project_sites', 'project_sites.id', '=', 'cash_approval.project_site_id')
                ->where('cash_approval.first_approval_status', 'Approved')
                ->where('cash_approval.second_approval_status', 'Pending')
                ->orderBy('cash_approval.created_at', 'DESC')
                ->get($get_fields)->toArray();

        } elseif (Auth::user()->role == config('constants.SuperUser')) {
            $this->data['cash_payment_list'] = \App\CashApproval::join('users', 'cash_approval.user_id', '=', 'users.id')
                ->join('company', 'company.id', '=', 'cash_approval.company_id')
                ->join('project', 'project.id', '=', 'cash_approval.project_id')
                ->leftJoin('budget_sheet_approval', 'budget_sheet_approval.id', '=', 'cash_approval.budget_sheet_id')
                ->leftJoin('clients', 'clients.id', '=', 'cash_approval.client_id')
                ->leftJoin('project_sites', 'project_sites.id', '=', 'cash_approval.project_site_id')
                ->where('cash_approval.first_approval_status', 'Approved')
                ->where('cash_approval.second_approval_status', 'Approved')
                ->where('cash_approval.third_approval_status', 'Pending')
                ->where('cash_approval.status', 'Pending')
                ->orderBy('cash_approval.created_at', 'DESC')
                ->get($get_fields)->toArray();

        } else {

            $this->data['cash_payment_list'] = [];
        }

        //---------------------------------------------------------- Online payment
        $get_fields = array(
               'online_payment_approval.id',
            'online_payment_approval.payment_options',
            'budget_sheet_approval.budhet_sheet_no',
                'users.name',
                'company.company_name',
                'clients.client_name',
                'project.project_name',
                'online_payment_approval.other_project_detail',
                'project_sites.site_name',
                'online_payment_approval.amount',
                'online_payment_approval.created_at'
        );
        if (Auth::user()->role == config('constants.ACCOUNT_ROLE')) {
            $this->data['online_payment_list'] = \App\OnlinePaymentApproval::join('users', 'online_payment_approval.user_id', '=', 'users.id')
                ->join('company', 'company.id', '=', 'online_payment_approval.company_id')
                ->join('project', 'project.id', '=', 'online_payment_approval.project_id')
                ->leftJoin('budget_sheet_approval', 'budget_sheet_approval.id', '=', 'online_payment_approval.budget_sheet_id')
                ->leftJoin('clients', 'clients.id', '=', 'online_payment_approval.client_id')
                ->leftJoin('project_sites', 'project_sites.id', '=', 'online_payment_approval.project_site_id')
                ->where('online_payment_approval.first_approval_status', 'Pending')
                ->orderBy('online_payment_approval.created_at', 'DESC')
                ->get($get_fields)->toArray();

        } elseif (Auth::user()->role == config('constants.Admin')) {
            $this->data['online_payment_list'] = \App\OnlinePaymentApproval::join('users', 'online_payment_approval.user_id', '=', 'users.id')
                ->join('company', 'company.id', '=', 'online_payment_approval.company_id')
                ->join('project', 'project.id', '=', 'online_payment_approval.project_id')
                ->leftJoin('budget_sheet_approval', 'budget_sheet_approval.id', '=', 'online_payment_approval.budget_sheet_id')
                ->leftJoin('clients', 'clients.id', '=', 'online_payment_approval.client_id')
                ->leftJoin('project_sites', 'project_sites.id', '=', 'online_payment_approval.project_site_id')
                ->where('online_payment_approval.first_approval_status', 'Approved')
                ->where('online_payment_approval.second_approval_status', 'Pending')
                ->orderBy('online_payment_approval.created_at', 'DESC')
                ->get($get_fields)->toArray();

        } elseif (Auth::user()->role == config('constants.SuperUser')) {
            $this->data['online_payment_list'] = \App\OnlinePaymentApproval::join('users', 'online_payment_approval.user_id', '=', 'users.id')
                ->join('company', 'company.id', '=', 'online_payment_approval.company_id')
                ->join('project', 'project.id', '=', 'online_payment_approval.project_id')
                ->leftJoin('budget_sheet_approval', 'budget_sheet_approval.id', '=', 'online_payment_approval.budget_sheet_id')
                ->leftJoin('clients', 'clients.id', '=', 'online_payment_approval.client_id')
                ->leftJoin('project_sites', 'project_sites.id', '=', 'online_payment_approval.project_site_id')
                ->where('online_payment_approval.first_approval_status', 'Approved')
                ->where('online_payment_approval.second_approval_status', 'Approved')
                ->where('online_payment_approval.third_approval_status', 'Pending')
                ->where('online_payment_approval.status', 'Pending')
                ->orderBy('online_payment_approval.created_at', 'DESC')
                ->get($get_fields)->toArray();

        } else {
            $this->data['online_payment_list'] = [];
        }
        //-------------------------- Budget sheet
        $get_budget_sheet_fields = ['budget_sheet_approval.*',
                'company.company_short_name',
                'clients.client_name',
                'clients.location',
                'department.dept_name',
                'project.project_name'];

        if (Auth::user()->role == config('constants.Admin')) {

            $this->data['budget_sheet_list'] = \App\BudgetSheetApproval::join('users','users.id','=','budget_sheet_approval.user_id')
                ->join('company','company.id','=','budget_sheet_approval.company_id')
                ->join('department','department.id','=','budget_sheet_approval.department_id')
                ->join('project','project.id','=','budget_sheet_approval.project_id')
                ->leftjoin('clients','clients.id','=','budget_sheet_approval.client_id')
                ->where('budget_sheet_approval.status','Pending')
                ->where('budget_sheet_approval.first_approval_status','Pending')
                ->get($get_budget_sheet_fields)->toArray();

    
        } elseif (Auth::user()->role == config('constants.SuperUser')) {

            $this->data['budget_sheet_list'] = \App\BudgetSheetApproval::join('users','users.id','=','budget_sheet_approval.user_id')
                ->join('company','company.id','=','budget_sheet_approval.company_id')
                ->join('department','department.id','=','budget_sheet_approval.department_id')
                ->join('project','project.id','=','budget_sheet_approval.project_id')
                ->leftjoin('clients','clients.id','=','budget_sheet_approval.client_id')
                ->where('budget_sheet_approval.status','Pending')
                ->where('budget_sheet_approval.first_approval_status','Approved')
                ->where('budget_sheet_approval.second_approval_status','Pending')
                ->get($get_budget_sheet_fields)->toArray();
        
        } else {

            $this->data['budget_sheet_list'] = [];
        }

        $this->data['asset_assign_requests'] = \App\AssetAccess::join('users','users.id','=','asset_access.assigner_user_id')
                    ->join('users as B','B.id','=','asset_access.asset_access_user_id')
                    ->join('asset','asset.id','=','asset_access.asset_id')
                    ->where('asset_access.giver_status','Confirmed')
                    ->where('asset_access.hr_status','Confirmed')
                    ->where('asset_access.receiver_status','Pending')
                    ->where('asset_access.asset_access_user_id',Auth::user()->id)
                    ->get(['users.name as giver_name','B.name as reciever_name','asset.name as asset_name','asset_access.asset_access_date','asset_access.asset_return_date','asset_access.is_allocate'])
                    ->toArray();

        // dd($asset_assign_requests);
        //vendor search
        $this->data['vendor_list'] = \App\Vendors::groupBy('vendor_name')->get(['id','vendor_name']);
     
        //dd($this->data['online_payment_list']);
       
        return view('admin.dashboard.index', $this->data);
    }

    public function get_vendor_payments(Request $request)
    { 
        $request_data = $request->all();
        $response_data = [];

        if ($request_data['vendor_name']) {
            $vendor_ids = \App\Vendors::where('vendor_name',$request_data['vendor_name'])->pluck('id')->toArray();
            $response_data['bank_payment'] = \App\BankPaymentApproval::whereIn('vendor_id',$vendor_ids)->get()->sum('amount');
            $response_data['cash_payment'] = \App\CashApproval::whereIn('vendor_id',$vendor_ids)->get()->sum('amount');
            $response_data['online_payment'] = \App\OnlinePaymentApproval::whereIn('vendor_id',$vendor_ids)->get()->sum('amount');
        }
        
        return response()->json($response_data);
    }

    public function changepassword() {
        $this->data['page_title'] = "Change Password";
        return view('admin.dashboard.changepassword', $this->data);
    }

    public function savepassword(Request $request) {

        $rules = array(
            'old_password' => 'required', // check old_password is empty or not
            'new_password' => 'required|min:8', // check new_password is empty or not
            're_password' => 'required|min:8|same:new_password' // check re_password is empty or not and new password and confirm password match
        );

        $validator = Validator::make(Input::all(), $rules);
        if ($validator->fails()) {
            return redirect()->route('admin.changepassword')->with('error', 'Please follow validation rules.');
        }

        if (!(Hash::check($request->get('old_password'), Auth::user()->password))) {
            //The passwords matches
            return redirect()->back()->with("error", "Your current password does not matches with the password you provided. Please try again.");
        }
        if (strcmp($request->get('old_password'), $request->get('new_password')) == 0) {
            //Current password and new password are same
            return redirect()->back()->with("error", "New Password cannot be same as your current password. Please choose a different password.");
        }

        $user = new User();
        $save_password = $user::where('id', Auth::User()->id)->first();
        $save_password->password = Hash::make($request->get('new_password'));
        if ($save_password->save()) {
            return redirect()->back()->with("success", "Password changed successfully !");
        } else {
            return redirect()->back()->with("error", "Password does not changed successfully !");
        }
    }

    public function edit_profile() {
        $id = Auth::user()->id;
        $this->data['page_title'] = "Edit Profile";

        $select_arr = ['users.digital_signature', 'users.name', 'users.id', 'users.profile_image', 'users.email', 'users.status', 'users.role', 'employee.emp_code'
            , 'employee.designation', 'employee.joining_date', 'employee.skype', 'employee.contact_number', 'employee.emg_contact_number', 'employee.emg_contact_number', 'employee.residential_address', 'employee.permanent_address'
            , 'employee.gender', 'employee.birth_date', 'employee.marital_status', 'employee.marriage_date', 'employee.blood_group', 'employee.physically_handicapped', 'employee.handicap_note', 'employee.company_id'
            , 'employee_reference.ref_name1', 'employee_reference.ref_contact1', 'employee_reference.ref_contact2', 'employee_reference.ref_name2'
            , 'employee.department_id'];
        $this->data['user_detail'] = User::where('users.id', $id)
                ->join('employee', 'employee.user_id', '=', 'users.id')
                ->join('employee_reference', 'employee_reference.user_id', '=', 'users.id')
                ->get($select_arr);
        if ($this->data['user_detail']->count() == 0) {
            return redirect()->route('admin.users')->with('error', 'Error Occurred. Try Again!');
        }
        $this->data['role_list'] = \App\Roles::where('status', 'Enable')->where('id', '!=', 1)->get();
        $this->data['company_list'] = \App\Companies::where('status', 'Enabled')->get();
        $this->data['department_list'] = Department::get();
        return view('admin.user.edit_user', $this->data);
    }

    public function profile() {
        $id = Auth::user()->id;

        $user_select_fields = ['users.digital_signature', 'users.id', 'users.name', 'users.email', 'employee.emp_code', 'employee.designation', 'employee.joining_date',
            'employee.skype', 'employee.contact_number', 'employee.emg_contact_number', 'employee.residential_address', 'employee.permanent_address',
            'employee.gender', 'employee.birth_date', 'employee.marital_status', 'employee.marriage_date', 'employee.blood_group',
            'employee.physically_handicapped', 'employee.handicap_note', 'employee.company_id', 'company.company_name',
            'employee_reference.ref_name1', 'employee_reference.ref_name2', 'employee_reference.ref_contact1'];

        $this->data["user_detail"] = $user_detail = User::join('employee', 'employee.user_id', '=', 'users.id')
                        ->leftJoin('employee_reference', 'employee_reference.user_id', '=', 'users.id')
                        ->leftJoin('company', 'company.id', '=', 'employee.company_id')
                        ->where('users.id', $id)->get();

        $this->data["education_detail"] = Employee_education::where('user_id', $id)->get(['id', 'degree', 'specialization', 'institute', 'percentage', 'degree_start_time_period', 'degree_end_time_period', 'degree_certificate']);

        $this->data["experience_detail"] = Employee_experience::where('user_id', $id)->get(['id', 'exp_company_name', 'exp_job_title', 'exp_location', 'exp_description', 'exp_start_time_period', 'exp_end_time_period', 'exp_document']);

        $this->data["user_document"] = IdentityDocument::where('user_id', $id)->get(['id', 'document_type', 'identity_document']);

        if ($user_detail->count() == 0) {
            return redirect()->route('admin.dashboard')->with('error', 'Error Occurred. Try Again!.');
        }
        $this->data['page_title'] = "Profile";
        $this->data['id'] = $id;
        $this->data['is_profile'] = true;
        return view('admin.user.view_user', $this->data);
    }

    public function education_document() {
        $id = Auth::user()->id;
        if (!$id) {
            return redirect()->route('admin.profile')->with('error', 'Error Occurred. Try Again!');
        }
        $this->data['page_title'] = "Upload education Document";
        $this->data['id'] = $id;
        return view('admin.user.upload_education', $this->data);
    }

    public function experience_document() {
        $id = Auth::user()->id;
        if (!$id) {
            return redirect()->route('admin.profile')->with('error', 'Error Occurred. Try Again!');
        }
        $this->data['page_title'] = "Upload experience Document";
        $this->data['id'] = $id;
        return view('admin.user.upload_experience', $this->data);
    }

    public function identity_document() {
        $id = Auth::user()->id;
        if (!$id) {
            return redirect()->route('admin.profile')->with('error', 'Error Occurred. Try Again!');
        }
        $this->data['page_title'] = "Upload identity Document";
        $this->data['id'] = $id;
        return view('admin.user.upload_identity', $this->data);
    }

    public function organization_chart() {
        $this->data['page_title'] = "Organizational Structure";
        $employee_list = Employees::join('users', 'users.id', '=', 'employee.user_id')
                        ->where('users.status', 'Enabled')->get(['employee.*', 'users.name', 'users.profile_image']);
        $this->data['selected_row'] = 0;
        foreach ($employee_list as $key => $emp) {
            if ($emp->profile_image) {
                $employee_list[$key]->profile_image = asset('storage/' . str_replace('public/', '', $emp->profile_image));
            } else {
                $employee_list[$key]->profile_image = asset('admin_asset/assets/plugins/images/user_avatar.png');
            }

            if ($emp->user_id == Auth::user()->id) {
                $this->data['selected_row'] = $key;
            }
        }
        $this->data['employee_list'] = $employee_list;

        return view('admin.dashboard.organization_chart', $this->data);
    }

    public function dashboard_pro_sign_letter_list(Request $request){
        $request_data = $request->all();
        $response_data = [];

        $loggedin_user_data = User::where('id', $request_data['user_id'])->get(['users.id', 'users.name', 'users.role']);

        //check if hr or superadmin based on that return data
        if (Auth::user()->role == config('constants.ASSISTANT')) {
            $pre_sign_approvals = ProSignLetter::where('first_approval_status', 'Pending')
                ->join('users', 'users.id', '=', 'pro_sign_letter.user_id')
                ->join('company', 'company.id', '=', 'pro_sign_letter.company_id')
                ->join('project', 'project.id', '=', 'pro_sign_letter.project_id')
                ->leftJoin('clients', 'clients.id', '=', 'pro_sign_letter.client_id')
                ->get(['pro_sign_letter.*', 'company.company_name', 'project.project_name', 'users.name as user_name', 'users.profile_image', 'clients.client_name']);
        } else {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        if ($pre_sign_approvals->count() == 0) {
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }

        foreach ($pre_sign_approvals as $key => $letter) {
            if ($letter->profile_image) {
                $pre_sign_approvals[$key]->profile_image = asset('storage/' . str_replace('public/', '', $letter->profile_image));
            } else {
                $pre_sign_approvals[$key]->profile_image = "";
            }
            if ($letter->letter_head_content_file) {
                $pre_sign_approvals[$key]->letter_head_content_file = asset('storage/' . str_replace('public/', '', $letter->letter_head_content_file));
            } else {
                $pre_sign_approvals[$key]->letter_head_content_file = "";
            }
        }
        return response()->json(['status' => true, 'msg' => 'Record found', 'data' => $pre_sign_approvals]);
    }

    public function dashboard_get_softcopy_received_request(Request $request){
        $request_list = SoftcopyRequest::join('company','company.id','=','softcopy_request.company_id')
                        ->join('users','users.id','=','softcopy_request.request_user_id')
                        ->join('softcopy_document_category','softcopy_document_category.id','=','softcopy_request.softcopy_document_category_id')
                        ->where('softcopy_request.receiver_user_id',Auth::user()->id)->where('softcopy_request.status','Pending')
                        ->get(['company.company_name','users.name as request_user_name','softcopy_document_category.name as document_name']);
        return response()->json(['status' => true, 'msg' => 'Record found', 'data' => $request_list]);
    }
    public function dashboard_get_leave_reversal_request(Request $request)
    {
        $list_query = Leaves::join('leave_category','leave_category.id', '=', 'leaves.leave_category_id')
                ->leftjoin('users','users.id', '=', 'leaves.user_id')
                ->leftjoin('users AS B','B.id', '=', 'leaves.assign_work_user_id');
                if (Auth::user()->role == config('constants.REAL_HR')) {
                        $list_query->where('leaves.leave_cancellation_status','Pending');
                        $list_query->whereNull('leaves.first_reversal_approval_status');
                        
                } else if (Auth::user()->role == 1) {
                    $list_query->where('leaves.leave_cancellation_status','Pending');
                    $list_query->where('leaves.first_reversal_approval_status','Approved');
                    $list_query->whereNull('leaves.second_reversal_approval_status');
                } 
        $list_ajax= $list_query->get(['users.name as request_user_name','leaves.leave_reversal_note','leaves.start_date','leaves.end_date']);
        return response()->json(['status' => true, 'msg' => 'Record found', 'data' => $list_ajax]);
    }

    // 'leaves.assign_work_status' != 'Approved'
    // ->whereRaw("FIND_IN_SET($loggedin_user_id,user_id)")
    // $temp = $loggedin_user_id == $assign_id;

    // ->where('assign_work_user_id',Auth::user()->id );
    public function dashboard_get_work_assigned_leave(Request $request){
        $loggedin_user_id = Auth::user()->id;
        $list_ajax = Leaves::leftJoin('users', 'users.id', '=', 'leaves.user_id')
            ->where('assign_work_user_id',$loggedin_user_id)
            ->where('leaves.assign_work_status','Pending')
            ->where('leaves.leave_status', '=', 1)
            ->orderBy('leaves.start_date','DESC')
            ->get(['users.name as request_user_name','leaves.start_date','leaves.end_date','leaves.assign_work_details']);
        return response()->json(['status' => true, 'msg' => 'Record found', 'data' => $list_ajax]);
    }
}
