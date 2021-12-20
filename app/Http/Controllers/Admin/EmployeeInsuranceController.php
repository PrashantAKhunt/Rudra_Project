<?php

namespace App\Http\Controllers\Admin;

use App\Asset;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Employee_Insurance;
use App\Employee_insurance_types;
use App\Employee_insurance_reminder_dates;
use Illuminate\Support\Facades\Validator;
use App\Employees;
use App\Companies;
use App\Compliance_reminders;
use App\Insurance_Upload_Policy;    /* here u need to look 4 model is it correct ya offcurse..!!! */

use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Lib\Permissions;

use App\Http\Controllers\Admin\GoogleCalenderController;

class EmployeeInsuranceController extends Controller
{

    public $data;
    public $googleCalender;
    private $module_id = 55;

    public function __construct()
    {

        $this->data['module_title'] = "Interview Process";
        $this->data['module_link'] = "admin.interview";
        $this->googleCalender = new GoogleCalenderController();
    }


    public function index()
    {

        $employee_insurance_full_view_permission = Permissions::checkPermission(55, 5);

        if (!$employee_insurance_full_view_permission) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You do not have access to this module.');
        }

        $this->data['page_title'] = "Employee Insurance";
        /*session()->forget('set_dates');
        session()->forget('expired_date');
        session()->forget('employee_name');*/

        $this->data['emp_insurances'] = $emp_insurances = Employee_Insurance::select('users.name as user_name', 'company.company_name as user_company', 'employee_insurance_types.title', 'employee_insurance.*')
            ->join('employee_insurance_types', 'employee_insurance.type_id', '=', 'employee_insurance_types.id')
            ->join('users', 'employee_insurance.employee_id', '=', 'users.id')
            ->join('company', 'employee_insurance.company_id', '=', 'company.id')
            ->where('employee_insurance.status', '=', 'Live')
            ->with(['get_insurance_policy'])->get();
        // dd($this->data['emp_insurances']);
        foreach ($emp_insurances as $key => $emp) {
            if ($emp->renew_date) {

                $currDate = strtotime(date('Y-m-d'));

                $renwDate = strtotime($emp->renew_date);

                if ($renwDate > $currDate) {

                    $datDiff = $renwDate - $currDate;
                    $left_days = round($datDiff / (60 * 60 * 24));

                    $emp_insurances[$key]->left_day = $left_days;
                    if ($left_days <= 30.0 && $left_days >= 11.0) {
                        $emp_insurances[$key]->color_class = 'text-warning';
                    } elseif ($left_days <= 10.0) {
                        $emp_insurances[$key]->color_class = 'text-danger';
                    } else {
                        $emp_insurances[$key]->color_class = 'text-success';
                    }
                }
            }
        }


        return view('admin.employee_insurance.index', $this->data);
    }

    public function add_employee_insurance()
    {

        $employee_insurance_add_permission = Permissions::checkPermission(55, 3);

        if (!$employee_insurance_add_permission) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You do not have access to this module.');
        }

        $this->data['page_title'] = 'Add Insurance';
        $this->data['module_title'] = "Employee Insurance";


        $this->data['employee_list'] = User::orderBy('name')->select('id', 'name')
            ->where('status', 'Enabled')->where('is_user_relieved', 0)->get();

        $this->data['insurances_types'] = Employee_insurance_types::select('id', 'title')->get();

        // compliance reminder may be controller
        $this->data['users_data'] = User::select('id', 'name')->where('status', 'Enabled')->orderBy('name', 'asc')->where('is_user_relieved', 0)->get();
        $this->data['days'] = [1,2,3,4,5,6,7];

        return view('admin.employee_insurance.add_insurance', $this->data);
    }


    public function employee_insurances_types(Request $request)
    {

        $employee_id = $request->employee_id;

        $emp_types = Employee_Insurance::where('employee_id', $employee_id)->pluck('type_id')->toArray();

        $insurances_types = Employee_insurance_types::whereNotIn('id', $emp_types)->get(['id', 'title']);

        return response()->json($insurances_types);
    }

    public function employee_company(Request $request)
    {
        $validator_normal = Validator::make($request->all(), ['id' => 'required']);

        if ($validator_normal->fails()) {
            return response()->json(['status' => false]);
        }

        $employee_id = $request->id;
        $company_name =  Employees::join('company', 'employee.company_id', '=', 'company.id')
            ->where('employee.user_id', $employee_id)
            ->get(['company.id', 'company.company_name']);

        return response()->json($company_name);
    }

    public function insert_employee_insurance(Request $request)
    {


        $validator_normal = Validator::make($request->all(), [
            'type_id' => 'required',
            'company_id' => 'required',
            'employee_id' => 'required',
            'agent_name' => 'required',
            'company_name' => 'required',
            'contact_number' => 'required',
            'policy_number' => 'required',
            'amount' => 'required',
            'insurance_date' => 'required',
            'renew_date' => 'required'
        ]);

        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_employee_insurance')->with('error', 'Please follow validation rules.');
        }
        // CompanyCrtfcateImage insert code for multiple image
        $upload_policy = '';
        
        $insurance_arr = [
            'employee_id' => $request->input('employee_id'),
            'company_id' => $request->input('company_id'),
            'type_id' => $request->input('type_id'),
            'policy_number' => $request->input('policy_number'),
            'agent_name' => $request->input('agent_name'),
            'company_name' => $request->input('company_name'),
            'contact_number' => $request->input('contact_number'),
            'contact_email' => $request->input('contact_email'),
            'amount' => $request->input('amount'),
            'status' => 'Live',
            'renewal' => 'NO',
            'insurance_date' => date('Y-m-d', strtotime($request->input('insurance_date'))),
            'renew_date' => date('Y-m-d', strtotime($request->input('renew_date'))),
            // 'insurance_upload_policy' => $request->input('insurance_upload_policy');
            'created_at' => date('Y-m-d h:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id
        ];

        
        $ins_id = Employee_Insurance::insertGetId($insurance_arr);
        
        $reminder_date_arr = [];
        if($request->input('first_day_interval')){
            array_push($reminder_date_arr,[
                'employee_insurance_id' => $ins_id,
                'date' => date('Y-m-d', strtotime($request->input('renew_date'). ' - '.$request->input('first_day_interval').' days')),
                'created_at' => date('Y-m-d h:i:s'),
                'created_ip' => $request->ip(),
            ]);
        }
        if($request->input('second_day_interval')){
            array_push($reminder_date_arr,[
                'employee_insurance_id' => $ins_id,
                'date' => date('Y-m-d', strtotime($request->input('renew_date'). ' - '.$request->input('second_day_interval').' days')),
                'created_at' => date('Y-m-d h:i:s'),
                'created_ip' => $request->ip(),
            ]);
        }
        if($request->input('third_day_interval')){
            array_push($reminder_date_arr,[
                'employee_insurance_id' => $ins_id,
                'date' => date('Y-m-d', strtotime($request->input('renew_date'). ' - '.$request->input('third_day_interval').' days')),
                'created_at' => date('Y-m-d h:i:s'),
                'created_ip' => $request->ip(),
            ]);
        }
        Employee_insurance_reminder_dates::insert($reminder_date_arr);

        /* Last Employee_insurance_reminder_dates Logic 04062021 */
        // if ($request->input('reminder_date')) {
        //     $reminder_date_arr = $request->input('reminder_date');
        //     // $date_arr = [];
            
        //     foreach ($reminder_date_arr as $key => $date) {
        //         //array_push($date_arr, $date);
        //         $InsuranceDateModel = new Employee_insurance_reminder_dates();
        //         $InsuranceDateModel->employee_insurance_id    = $ins_id;
        //         $InsuranceDateModel->date = date('Y-m-d', strtotime($date));
        //         $InsuranceDateModel->created_at = date('Y-m-d h:i:s');
        //         $InsuranceDateModel->created_ip =  $request->ip();
        //         $InsuranceDateModel->save();
        //     }
            
        //     /*$employee_name = User::where('id',$request->input('employee_id'))->value('name');
        //     //set Dates to Google Calender                          
        //     session(['employee_name' => $employee_name]);                                                        
        //     session(['expired_date' => $request->input('renew_date')]);    //\Session::pull('set_dates');\Session::put('set_dates',$date_arr);$sessionData = \Session::all();
        //     session(['set_dates' => $date_arr]);
            
        //     return redirect()->route('admin.passDatesGoogle'); */
        // }
        
        // insert code for multiple upload policy
        
        // $uploadpolicy = '' name as given in input field;
        
        if ($request->hasFile('uploadpolicy')) {
            $profile_image = $request->file('uploadpolicy');
            foreach ($profile_image as $image){
                $file_path = $image->store('public/upload_policy');
                if ($file_path) {
                    $upload_policy = $file_path;
                    $Insurance_Upload_PolicyModel = new Insurance_Upload_Policy();
                    $Insurance_Upload_PolicyModel->insurance_id = $ins_id;
                    $Insurance_Upload_PolicyModel->attachment   = $upload_policy;
                    $Insurance_Upload_PolicyModel->save();
                } 
            }                               
        }

        // Compliance_reminders
        $request_data = $request->all();
        $complianceModel = new Compliance_reminders();
        $complianceModel->user_id = $request_data['employee_id'];
        $complianceModel->company_id = $request_data['company_id'];
        $complianceModel->compliance_category_id = 7;
        $complianceModel->compliance_name = $request_data['company_name'];
        $complianceModel->compliance_description = 'Company Name :'.$request_data['company_name']." Policy Number :".$request_data['policy_number']." Agent Name :".$request_data['agent_name']." Contact Number :".$request_data['contact_number']." Contact Email :".$request_data['contact_email']." Amount:".$request_data['amount'];
        $complianceModel->periodicity_type = 'Day';
        $complianceModel->start_date = date('Y-m-d', strtotime($request_data['insurance_date']));
        $complianceModel->end_date = date('Y-m-d', strtotime($request_data['renew_date']));
        $complianceModel->periodicity_time = "23:59";

        $diff = strtotime($request_data['renew_date']) - strtotime($request_data['insurance_date']);
        $complianceModel->periodic_date = abs(round($diff / 86400));

        $complianceModel->responsible_person_id = \App\User::where('id', $request_data['responsible_person_id'])->value('email');
        $complianceModel->checker_id = \App\User::where('id', $request_data['checker_id'])->value('email');
        $complianceModel->payment_responsible_person_id = \App\User::where('id', $request_data['payment_responsible_person_id'])->value('email');
        $complianceModel->super_admin_checker_id = \App\User::where('id', $request_data['super_admin_checker_id'])->value('email');

        $complianceModel->first_day_interval = $request_data['first_day_interval'];
        $complianceModel->second_day_interval = $request_data['second_day_interval'];
        $complianceModel->third_day_interval = $request_data['third_day_interval'];


        $complianceModel->created_at = date('Y-m-d H:i:s');
        $complianceModel->created_ip = $request->ip();
        $complianceModel->updated_at = date('Y-m-d H:i:s');
        $complianceModel->updated_ip = $request->ip();
        $complianceModel->save();

        return redirect()->route('admin.employees_insurances')->with('success', 'New Employee Insurance added successfully.');
    }

    // public function passDatesGoogle()
    // {
    //     $this->googleCalender->index();
    // }

    public function emp_insurance_reminder_dates(Request $request)
    {
        $validator_normal = Validator::make($request->all(), ['id' => 'required']);

        if ($validator_normal->fails()) {
            return response()->json(['status' => false]);
        }

        $reminder_dates = Employee_insurance_reminder_dates::where('employee_insurance_id', $request->get('id'))->pluck('date')->toArray();

        $this->data['reminder_dates'] = $reminder_dates;

        if ($reminder_dates) {
            return response()->json(['status' => true, 'data' => $this->data]);
        } else {
            return response()->json(['status' => false]);
        }
    }

    public function expired_emp_insurances_list()
    {
        $this->data['page_title'] = 'Expired Employee Insurances';
        $this->data['module_title'] = "Employee Insurances";

        $this->data['exp_insurance_list'] = $exp_insurance_list = Employee_Insurance::select('users.name as user_name', 'company.company_name as user_company', 'employee_insurance.*', 'employee_insurance_types.title')
            ->join('employee_insurance_types', 'employee_insurance.type_id', '=', 'employee_insurance_types.id')
            ->join('users', 'employee_insurance.employee_id', '=', 'users.id')
            ->join('company', 'employee_insurance.company_id', '=', 'company.id')
            ->where('employee_insurance.status', '=', 'Expired')
            ->where('employee_insurance.renewal', '=', 'NO')
            ->orderBy('employee_insurance.insurance_date', 'ASC')
            ->get();          //->toArray();

        return view('admin.employee_insurance.expired_insurances_list', $this->data);
    }

    public function renew_expired_employee_insurance($id)
    {
        $this->data['page_title'] = 'Renew Expired Employee Insurances';
        $this->data['module_title'] = "Employee Insurance";

        $employee_insurance_edit_permission = Permissions::checkPermission(55, 2);

        if (!$employee_insurance_edit_permission) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You do not have access to this module.');
        }

        $this->data['exp_insurance_data'] = $exp_insurance_list = Employee_Insurance::select('*')
            ->where('id', '=', $id)
            ->get();

        $this->data['employee_list'] = User::select('id', 'name')
            ->where('status', 'Enabled')->where('is_user_relieved', 0)->get();

        $this->data['insurances_types'] = Employee_insurance_types::select('id', 'title')->get();

        if ($this->data['exp_insurance_data']->count() == 0) {
            return redirect()->route('admin.vehicle_assets')->with('error', 'Error Occurred. Try Again!');
        }
        return view('admin.employee_insurance.renew_employee_insurance', $this->data);
    }

    public function renewed_employee_insurance(Request $request)
    {

        $validator_normal = Validator::make($request->all(), [
            'type_id' => 'required',
            'company_id' => 'required',
            'employee_id' => 'required',
            'company_name' => 'required',
            'policy_number' => 'required',
            'agent_name' => 'required',
            'contact_number' => 'required',
            'amount' => 'required',
            'insurance_date' => 'required',
            'renew_date' => 'required'
        ]);


        if ($validator_normal->fails()) {
            return redirect()->route('admin.employees_insurances')->with('error', 'Please follow validation rules.');
        }

        $renewal_arr = [
            'renewal' => "YES"
        ];

        $insurance_id = $request->input('id');

        Employee_Insurance::where('id', $insurance_id)->update($renewal_arr);

        $insurance_arr = [
            'employee_id' => $request->input('employee_id'),
            'company_id' => $request->input('company_id'),
            'type_id' => $request->input('type_id'),
            'policy_number' => $request->input('policy_number'),
            'company_name' => $request->input('company_name'),
            'agent_name' => $request->input('agent_name'),
            'contact_number' => $request->input('contact_number'),
            'contact_email' => $request->input('contact_email'),
            'amount' => $request->input('amount'),
            'status' => 'Live',
            'renewal' => 'NO',
            'insurance_date' => date('Y-m-d', strtotime($request->input('insurance_date'))),
            'renew_date' => date('Y-m-d', strtotime($request->input('renew_date'))),
            'created_at' => date('Y-m-d h:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
            'updated_by' => Auth::user()->id
        ];

        $ins_newid = Employee_Insurance::insertGetId($insurance_arr);

        if ($request->input('reminder_date')) {
            $reminder_date_arr = $request->input('reminder_date');


            foreach ($reminder_date_arr as $key => $date) {
                $InsuranceDateModel = new Employee_insurance_reminder_dates();
                $InsuranceDateModel->employee_insurance_id = $ins_newid;
                $InsuranceDateModel->date = date('Y-m-d', strtotime($date));
                $InsuranceDateModel->created_at = date('Y-m-d h:i:s');
                $InsuranceDateModel->created_ip =  $request->ip();
                $InsuranceDateModel->save();
            }
        }

        return redirect()->route('admin.employees_insurances')->with('success', 'Renewd Insurance successfully.');
    }

    public function employee_insurances_history($id)
    {

        $employee_insurance_full_view_permission = Permissions::checkPermission(55, 5);

        if (!$employee_insurance_full_view_permission) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You do not have access to this module.');
        }

        $this->data['page_title'] = 'Employee Insurances List';
        $this->data['module_title'] = "Employee Insurances";

        $this->data['insurance_list'] = $insurance_list = Employee_Insurance::select('users.name as user_name', 'company.company_name as user_company', 'employee_insurance.*', 'employee_insurance_types.title')
            ->join('employee_insurance_types', 'employee_insurance.type_id', '=', 'employee_insurance_types.id')
            ->join('users', 'employee_insurance.employee_id', '=', 'users.id')
            ->join('company', 'employee_insurance.company_id', '=', 'company.id')
            ->where('employee_insurance.employee_id', '=', $id)
            ->orderBy('employee_insurance.insurance_date', 'ASC')
            ->get();          //->toArray();


        foreach ($insurance_list as $key => $employee) {
            if ($employee->renew_date) {

                $currDate = strtotime(date('Y-m-d'));

                $renwDate = strtotime($employee->renew_date);

                if ($renwDate > $currDate) {

                    $datDiff = $renwDate - $currDate;
                    $left_days = round($datDiff / (60 * 60 * 24));

                    $insurance_list[$key]->left_day = $left_days;
                    if ($left_days <= 30.0 && $left_days >= 11.0) {
                        $insurance_list[$key]->color_class = 'text-warning';
                    } elseif ($left_days <= 10.0) {
                        $insurance_list[$key]->color_class = 'text-danger';
                    } else {
                        $insurance_list[$key]->color_class = 'text-success';
                    }
                } else {
                    $insurance_list[$key]->left_day = '';
                    $insurance_list[$key]->color_class = 'text-dark';
                }
            }
        }

        return view('admin.employee_insurance.employee_insurances_list', $this->data);
    }
}
