<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Input;
use App\Common_query;
use App\Roles;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Email_format;
use Illuminate\Support\Facades\Mail;
use App\Mail\Mails;
use App\Employees;
use App\Department;
use App\Employee_education;
use App\Employee_experience;
use App\Employee_reference;
USE App\IdentityDocument;
use Exception;
use App\Role_module;
use App\DeviceAllow;
use App\EmployeesLoans;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Session;

use App\Lib\Permissions;
use App\TaxDeclaration;
use App\LeaveMaster;
use App\Lib\CommonTask;

class UserController extends Controller {

    public $data;
    public $common_task;

    public function __construct() {
        $this->data['module_title'] = "Employee";
        $this->data['module_link'] = "admin.users";
        $this->common_task = new CommonTask();
    }

    public function index() {
        $this->data['page_title'] = "Employees";
        $access_level = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => 3])->get()->first();
        $this->data['access_rule'] = '';
        if (!empty($access_level)) {
            $this->data['access_rule'] = $access_level->access_level;
        }

        //$check_resultM = Permissions::checkPermission(3, 1);
        $permission_arr = $this->common_task->getPermissionArr(Auth::user()->role, 3);
        if (!in_array(5, $permission_arr) && !in_array(6, $permission_arr)) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied.');
        }
        return view('admin.user.index', $this->data);
    }

    public function get_user_list() {
        $datatable_fields = array('employee.emp_code','users.name', 'users.email', 'users.personal_email','employee.joining_date', 'department.dept_name', 'role.role_name', 'users.status', 'users.is_suspended', 'users.user_attend_type','users.created_at', 'users.is_on_probation','users.is_user_relieved','users.relieved_date');
        $request = Input::all();

        $check_resultF = Permissions::checkPermission(3, 5); // Full View
        if (!$check_resultF) {
            $check_resultP = Permissions::checkPermission(3, 6); // Partial View
            if (!$check_resultP) {
                $check_resultM = Permissions::checkPermission(3, 1); // Only My View
                if (!$check_resultM) {
                    return response()->json([]);
                } else {
                    return response()->json([]);
                }
            } else {
                $emp_user_id = Employees::select('user_id')->where(['reporting_user_id' => Auth::user()->id])->get()->toArray();
                if (!empty($emp_user_id)) {
                    $arr_user = array_column($emp_user_id, 'user_id');
                    $conditions_array = $arr_user;
                } else {
                    $conditions_array = [Auth::user()->id];
                }
            }
        } else {
            //$conditions_array = [['role', '!=', 1]];
            $conditions_array = [];
        }

        $getfiled = array('users.id', 'employee.emp_code','users.name', 'users.email', 'users.personal_email','users.status', 'users.is_suspended','users.user_attend_type', 'users.created_at', 'employee.joining_date', 'department.dept_name', 'role.role_name', 'users.is_on_probation','users.is_user_relieved','users.relieved_date');

        $table = "users";
        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'employee';
        $join_str[0]['join_table_id'] = 'employee.user_id';
        $join_str[0]['from_table_id'] = 'users.id';

        $join_str[1]['join_type'] = '';
        $join_str[1]['table'] = 'department';
        $join_str[1]['join_table_id'] = 'department.id';
        $join_str[1]['from_table_id'] = 'employee.department_id';

        $join_str[2]['join_type'] = '';
        $join_str[2]['table'] = 'role';
        $join_str[2]['join_table_id'] = 'role.id';
        $join_str[2]['from_table_id'] = 'users.role';

        echo User::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);
        die();
    }

      public function reliever_user(Request $request)
    {

        $user_id = $request->id;
        $is_relieved = $request->status;


        if ($is_relieved) {

            $userRelieve_arr = [
                'is_user_relieved' => 0,
                'relieved_date' => NULL
            ];

            if (User::where('id', $user_id)->update($userRelieve_arr)) {

                Session::flash('success', 'User Relieved Status Updated');
                return response()->json(['status' => true, 'data' => []]);

            } else {

                return response()->json(['status' => false, 'data' => []]);
            }
        } else {
            $userRelieve_arr = [
                'is_user_relieved' => 1,
                'relieved_date' => date('Y-m-d', strtotime($request->relieved_date))
            ];
            User::where('id', $user_id)->update($userRelieve_arr);
            return redirect()->route('admin.users')->with('success', 'User Relieved Successfully.');
        }
    }

    public function add_user() {
        // dd("Inn");
        $check_result = Permissions::checkPermission(3, 3);
        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }
        $this->data['page_title'] = 'Add Employee';
        $this->data['role_list'] = Roles::orderBy('role_name')->where('status', 'Enable')->where('id', '!=', 1)->get();
        $this->data['company_list'] = \App\Companies::orderBy('company_name')->where('status', 'Enabled')->get();
        $this->data['department_list'] = Department::orderBy('dept_name')->get();
        $this->data['users'] = User::orderBy('name')->select('users.name', 'users.id', 'department.dept_name')
                ->join('employee', 'employee.user_id', '=', 'users.id')
                ->join('department', 'department.id', '=', 'employee.department_id')
                ->get()
                ->toArray();
        return view('admin.user.add_user', $this->data);
    }

    public function export_users() {
        $check_result = Permissions::checkPermission(3, 3);
        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }
        $this->data['csv_data'] = "";

        $this->data['users'] = User::select('*')
                ->join('employee', 'employee.user_id', '=', 'users.id')
                ->join('department', 'department.id', '=', 'employee.department_id')
                ->leftjoin('employee_salary', 'employee_salary.user_id', '=', 'users.id')
                ->join('company', 'company.id', '=', 'employee.company_id')
                ->get()
                ->toArray();


        $columnName = array('Sr. No', 'Employee Name',
            'Employee ID', 'Designation', 'email', 'personal_email',
            'status', 'Joining Date', 'skype', 'Contact Number',
            'Emergency contact number', 'Residential Address',
            'Permanent Address', 'Gender', 'Birth Date',
            'Marital Status', 'Marriage Date',
            'BG', 'Physically Handicapped', 'Dept Name', 'Company Name',
            'Payable Salary');


        if (!empty($this->data['users'][0])) {
            $csvData = $this->generateCsvFiles('employee_list_report', $columnName, $this->data['users']);
            $this->data['csv_data'] = $csvData;
        }

        die();
    }

    public function generateCsvFiles($filename, $columnName, $rptData) {

        $name = date('D-M-Y h:m:s') . ' ' . $filename . '.csv';

        $file = fopen(storage_path('app/public/reports/attendance_report/') . $name, 'wb');



        if ($filename == "employee_list_report") {
            fputcsv($file, $columnName);
            $data = [];
            foreach ($rptData as $k => $rowData) {

                $data[] = array($k + 1,
                    $rowData['name'],
                    $rowData['emp_code'],
                    $rowData['designation'],
                    $rowData['email'],
                    $rowData['personal_email'],
                    $rowData['status'],
                    $rowData['joining_date'],
                    $rowData['skype'],
                    $rowData['contact_number'],
                    $rowData['emg_contact_number'],
                    $rowData['residential_address'],
                    $rowData['permanent_address'],
                    $rowData['gender'],
                    $rowData['birth_date'],
                    $rowData['marital_status'],
                    $rowData['marriage_date'],
                    $rowData['blood_group'],
                    $rowData['physically_handicapped'],
                    $rowData['dept_name'],
                    $rowData['company_name'],
                    $rowData['total_month_salary'],
                );
            }
        }



        foreach ($data as $row) {
            fputcsv($file, $row);
        }

        $file_url = asset('storage/' . str_replace('public/', '', 'reports/attendance_report/' . $name));

        header("Location: $file_url");
        die();
        // return asset('storage/'.str_replace('public/','','reports/attendance_report/'.$name));
    }

    public function insert_user(Request $request) {

        $validator_normal = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'required',
                    'password' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.add_user')->with('error', 'Please follow validation rules.');
        }

        //upload user profile image            //no change
        $profile_image_file = '';
        if ($request->hasFile('profile_image')) {
            $profile_image = $request->file('profile_image');
            $file_path = $profile_image->store('public/profile_image');
            if ($file_path) {
                $profile_image_file = $file_path;
            }
        }
        // Employee_signature filled to insert signature_image
        $signature_file_emp = '';
        if ($request->hasFile('digital_signature')) {
            // dd($request);
            $signature_file = $request->file('digital_signature');
            $file_path = $signature_file->store('public/digital_signature');
            // dd($file_path);
            if ($file_path) {
                $signature_file_emp = $file_path;
                // dd($signature_file_emp);
            }
        }

        $user_arr = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'personal_email' => $request->input('personal_email'),
            'profile_image' => !empty($profile_image_file) ? $profile_image_file : NULL,
            'password' => Hash::make($request->input('password')),
            'is_verified' => 'Yes',
            'role' => $request->input('role'),
            'created_at' => date('Y-m-d h:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
            'modified_by' => Auth::user()->id,
            'digital_signature' => !empty($signature_file_emp) ? $signature_file_emp : NULL,
        ];

        $result = DB::transaction(function() use ($user_arr, $request) {
                    if ($user_id = User::insertGetId($user_arr)) {
                        //Add tax declaration
                        TaxDeclaration::addTax($user_id);

                        foreach ($request->input('leave') as $key => $value) {
                            $leaveMaster = new LeaveMaster();
                            $leaveMaster->user_id = $user_id;
                            $leaveMaster->leave_category_id = $key;
                            $leaveMaster->balance = $value;
                            $leaveMaster->created_at = date('Y-m-d h:i:s');
                            $leaveMaster->created_ip = $request->ip();
                            $leaveMaster->updated_at = date('Y-m-d h:i:s');
                            $leaveMaster->updated_ip = $request->ip();
                            $leaveMaster->updated_by = Auth::user()->id;
                            $leaveMaster->save();
                        }


                        

                        $employee_arr = [
                            'user_id' => $user_id,
                            'department_id' => $request->input('department'),
                            'emp_code' => $request->input('emp_code'),
                            'designation' => $request->input('designation'),
                            'skype' => $request->input('skype'),
                            'contact_number' => $request->input('contact_number'),
                            'emg_contact_number' => $request->input('emg_contact_number'),
                            'residential_address' => $request->input('residential_address'),
                            'permanent_address' => $request->input('permanent_address'),
                            'company_id' => $request->input('company'),
                            'reporting_user_id' => !empty($request->input('reporting_user_id')) ? $request->input('reporting_user_id') : 0,
                            'gender' => $request->input('gender'),
                            'birth_date' => date('Y-m-d', strtotime($request->input('birth_date'))),
                            'joining_date' => ($request->input('joining_date')) ? date('Y-m-d', strtotime($request->input('joining_date'))) : NULL,
                            'marital_status' => $request->input('marital_status'),
                            'marriage_date' => ($request->input('marriage_date') && $request->input('marital_status') == "Married") ? date('Y-m-d', strtotime($request->input('marriage_date'))) : NULL,
                            'blood_group' => $request->input('blood_group'),
                            'physically_handicapped' => $request->input('physically_handicapped'),
                            'handicap_note' => $request->input('handicap_note'),
                            'created_at' => date('Y-m-d h:i:s'),
                            'created_ip' => $request->ip(),
                            'updated_at' => date('Y-m-d h:i:s'),
                            'updated_ip' => $request->ip(),
                            'updated_by' => Auth::user()->id,
                            'pf_number' => !empty($request->input('pf_num'))?$request->input('pf_num'):"",
                            // need to look here
                        ];
                        // dd($employee_arr);
                        try {
                            Employees::insert($employee_arr);

                            //insert education detail
                            $degree = $request->input('degree');
                            $specialization = $request->input('specialization');
                            $institute = $request->input('institute');
                            $degree_time_period = $request->input('degree_time_period');
                            $percentage = $request->input('percentage');

                            //upload degree certificate
                            $degree_certificate_file = [];


                            if ($request->hasFile('degree_certificate')) {
                                $degree_certificate = $request->file('degree_certificate');
                                foreach ($degree_certificate as $key => $deg_cert) {

                                    //$file_path = $deg_cert->store('public/degree_certi');

                                    $original_file_name = explode('.', $deg_cert->getClientOriginalName());

                                    $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);


                                    $file_path = $deg_cert->storeAs('public/degree_certi', $new_file_name);


                                    if ($file_path) {
                                        $degree_certificate_file[$key] = $file_path;
                                    } else {
                                        DB::rollback();
                                        die('upload degree certificate');
                                        return false;
                                    }
                                }
                            }



                            //upload experience certificate
                            $exp_doc_file = [];

                            if ($request->hasFile('exp_document')) {
                                $exp_document = $request->file('exp_document');
                                foreach ($exp_document as $key => $exp_doc) {
                                    //$file_path = $exp_doc->store('public/exp_doc');

                                    $original_file_name = explode('.', $exp_doc->getClientOriginalName());

                                    $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);


                                    $file_path = $exp_doc->storeAs('public/exp_doc', $new_file_name);


                                    if ($file_path) {
                                        $exp_doc_file[$key] = $file_path;
                                    } else {
                                        DB::rollback();
                                        die('upload experience certificate');
                                        return false;
                                    }
                                }
                            }

                            foreach ($degree as $key => $deg) {
                                $degree_date = explode('-', $degree_time_period[$key]);
                                $degree_start_date = date('Y-m-d', strtotime($degree_date[0]));
                                $degree_end_date = date('Y-m-d', strtotime($degree_date[1]));
                                $edu_insert_arr = [
                                    'user_id' => $user_id,
                                    'degree' => $deg,
                                    'specialization' => $specialization[$key],
                                    'institute' => $institute[$key],
                                    'degree_start_time_period' => $degree_start_date,
                                    'degree_end_time_period' => $degree_end_date,
                                    'percentage' => $percentage[$key],
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'created_ip' => $request->ip(),
                                    'updated_at' => date('Y-m-d'),
                                    'updated_ip' => $request->ip(),
                                    'updated_by' => $user_id,
                                    'degree_certificate' => !empty($degree_certificate_file[$key]) ? $degree_certificate_file[$key] : NULL
                                ];
                                Employee_education::insert($edu_insert_arr);
                            }

                            $exp_company_name = $request->input('exp_company_name');
                            $exp_job_title = $request->input('exp_job_title');
                            $exp_location = $request->input('exp_location');
                            $exp_time_period = $request->input('exp_time_period');
                            $exp_description = $request->input('exp_description');

                            if (!empty($exp_company_name) && !empty($exp_job_title) && !empty($exp_time_period)) {
                                foreach ($exp_company_name as $key => $exp_company) {

                                    $exp_date = explode('-', $exp_time_period[$key]);
                                    $start_date = date('Y-m-d', strtotime($exp_date[0]));
                                    $end_date = date('Y-m-d', strtotime($exp_date[1]));

                                    $exp_insert_arr = [
                                        'user_id' => $user_id,
                                        'exp_company_name' => $exp_company,
                                        'exp_job_title' => $exp_job_title[$key],
                                        'exp_location' => $exp_location[$key],
                                        'exp_start_time_period' => $start_date,
                                        'exp_end_time_period' => $end_date,
                                        'exp_description' => $exp_description[$key],
                                        'exp_document' => !empty($exp_doc_file[$key]) ? $exp_doc_file[$key] : NULL,
                                        'created_at' => date('Y-m-d H:i:s'),
                                        'created_ip' => $request->ip(),
                                        'updated_at' => date('Y-m-d'),
                                        'updated_ip' => $request->ip(),
                                        'updated_by' => $user_id,
                                    ];

                                    Employee_experience::insert($exp_insert_arr);
                                }
                            }

                            //insert employee reference
                            $emp_ref_arr = [
                                'user_id' => $user_id,
                                'ref_name1' => $request->input('ref_name1'),
                                'ref_contact1' => $request->input('ref_contact1'),
                                'ref_name2' => $request->input('ref_name2'),
                                'ref_contact2' => $request->input('ref_contact2'),
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                                'created_ip' => $request->ip(),
                                'updated_ip' => $request->ip(),
                                'updated_by' => $user_id,
                            ];
                            Employee_reference::insert($emp_ref_arr);                           
                            
                            //upload user identity document                           


                            $identity_doc_file = '';

                            if ($request->hasFile('identity_document')) {

                                $identity_document = $request->file('identity_document');
                                //$file_path = $identity_document->store('public/identity_doc');

                                $original_file_name = explode('.', $identity_document->getClientOriginalName());

                                $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);


                                $file_path = $identity_document->storeAs('public/identity_doc', $new_file_name);


                                if ($file_path) {
                                    $identity_doc_file = $file_path;
                                } else {
                                    DB::rollback();
                                    die('upload user identity document');
                                    return false;
                                }
                            }

                            //insert user identity document
                            $doc_insert_arr = [
                                'user_id' => $user_id,
                                'document_type' => $request->input('document_type'),
                                'identity_document' => $identity_doc_file,
                                'created_at' => date('Y-m-d H:i:s'),
                                'created_ip' => $request->ip(),
                                'updated_at' => date('Y-m-d'),
                                'updated_ip' => $request->ip(),
                                'updated_by' => $user_id,
                            ];
                            IdentityDocument::insert($doc_insert_arr);

                            //Send Email
                            $emailData = Email_format::find(4)->toArray();
                            $site_name = config('app.name');
                            $subject = $emailData['subject'];
                            $mailformat = $emailData['emailformat'];

                            $mail_body = str_replace("%name%", $request->input('name'), str_replace("%email%", $request->input('email'), str_replace("%password%", $request->input('password'), str_replace("%site_name%", $site_name, stripslashes($mailformat)))));

                            try {
                                Mail::to($request->input('email'))->send(new Mails($subject, $mail_body));
                            } catch (\Exception $e) {

                            }
                            return true;
                        } catch (Exception $e) {
                            DB::rollback();
                            echo $e->getMessage();
                            die('All');
                            return false;
                        }
                    } else {
                        return redirect()->route('admin.add_user')->with('error', 'Error occurre in insert. Try Again!');
                    }
                });

        if ($result) {
            return redirect()->route('admin.users')->with('success', 'New employee inserted successfully.');
        } else {
            return redirect()->route('admin.users')->with('error', 'Error Occurred. Try Again!');
        }
    }

    public function edit_user($id) {
        $check_result = Permissions::checkPermission(3, 2);
        if (!$check_result) {
            return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
        }
        $this->data['page_title'] = "Edit Employee";

        $select_arr = ['users.name', 'users.id', 'users.profile_image', 'users.email', 'users.personal_email','users.status', 'users.role', 'employee.emp_code'
            , 'employee.designation', 'employee.joining_date', 'employee.skype', 'employee.contact_number', 'employee.emg_contact_number', 'employee.emg_contact_number', 'employee.residential_address', 'employee.permanent_address'
            , 'employee.gender', 'employee.birth_date', 'employee.marital_status', 'employee.marriage_date', 'employee.blood_group', 'employee.physically_handicapped', 'employee.handicap_note', 'employee.company_id', 'employee.department_id'
            , 'employee_reference.ref_name1', 'employee_reference.ref_contact1', 'employee_reference.ref_contact2', 'employee_reference.ref_name2', 'employee.reporting_user_id','employee.pf_number'
        ];
        $this->data['user_detail'] = User::where('users.id', $id)
                ->join('employee', 'employee.user_id', '=', 'users.id')
                ->join('employee_reference', 'employee_reference.user_id', '=', 'users.id')
                ->get($select_arr);
        if ($this->data['user_detail']->count() == 0) {
            return redirect()->route('admin.users')->with('error', 'Error Occurred. Try Again!');
        }
        $this->data['role_list'] = Roles::orderBy('role_name')->where('status', 'Enable')->where('id', '!=', 1)->get();
        $this->data['company_list'] = \App\Companies::orderBy('company_name')->where('status', 'Enabled')->get();
        // $this->data['company_list'] = \App\Companies::orderBy('company_name')->where('status', 'Enabled')->get();
        $this->data['department_list'] = Department::orderBy('dept_name')->get();
        
        return view('admin.user.edit_user', $this->data);
    }

    public function update_user(Request $request) {
        $validator_normal = Validator::make($request->all(), [
                    'name' => 'required',
                    'email' => 'required',
                    'emp_code' => 'required',
                    'id' => 'required'
        ]);
        if ($validator_normal->fails()) {
            return redirect()->route('admin.edit_user', ['id' => $request->input('id')])->with('error', 'Please follow validation rules.');
        }

        //upload user profile image
        $profile_image_file = '';
        if ($request->hasFile('profile_image')) {
            $profile_image = $request->file('profile_image');
            $file_path = $profile_image->store('public/profile_image');
            if ($file_path) {
                $profile_image_file = $file_path;
            }
        }

        //upload user profile image
             //21-02-2020
        $digital_signature_file = '';
        if ($request->hasFile('digital_signature')) {

            $digital_signature = $request->file('digital_signature');

            //$file_path = $digital_signature->store('public/digital_signature');

            $original_file_name = explode('.', $digital_signature->getClientOriginalName());

                        $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);


                        $file_path = $digital_signature->storeAs('public/digital_signature', $new_file_name);


            if ($file_path) {
                $digital_signature_file = $file_path;
            }
        }



        $user_arr = [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'personal_email' => $request->input('personal_email'),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
            'modified_by' => Auth::user()->id,
            
        ];
        if ($request->input('role')) {
            $user_arr['role'] = $request->input('role');
        }

        if (!empty($profile_image_file)) {
            $user_arr['profile_image'] = $profile_image_file;
        }
        if (!empty($digital_signature_file)) {
            $user_arr['digital_signature'] = $digital_signature_file;
        }
        $result = DB::transaction(function() use ($user_arr, $request) {
                    if (User::where('id', $request->input('id'))->update($user_arr)) {
                        $employee_arr = [
                            'department_id' => $request->input('department'),
                            'designation' => $request->input('designation'),
                            'skype' => $request->input('skype'),
                            'contact_number' => $request->input('contact_number'),
                            'emg_contact_number' => $request->input('emg_contact_number'),
                            'residential_address' => $request->input('residential_address'),
                            'permanent_address' => $request->input('permanent_address'),
                            'company_id' => $request->input('company'),
                            'gender' => $request->input('gender'),
                            'birth_date' => date('Y-m-d', strtotime($request->input('birth_date'))),
                            'joining_date' => date('Y-m-d', strtotime($request->input('joining_date'))),
                            'marital_status' => $request->input('marital_status'),
                            'marriage_date' => ($request->input('marriage_date')) ? date('Y-m-d', strtotime($request->input('marriage_date'))) : NULL,
                            'blood_group' => $request->input('blood_group'),
                            'physically_handicapped' => $request->input('physically_handicapped'),
                            'handicap_note' => $request->input('handicap_note'),
                            'updated_at' => date('Y-m-d h:i:s'),
                            'updated_ip' => $request->ip(),
                            'updated_by' => Auth::user()->id,
                            'pf_number' => !empty($request->input('pf_num'))?$request->input('pf_num'):"",
                            
                            //'reporting_user_id' => $request->input('reporting_user_id')
                        ];
                        if($request->input('reporting_user_id')){
                            $employee_arr['reporting_user_id']=$request->input('reporting_user_id');
                        }
                        try {
                            Employees::where('user_id', $request->input('id'))->update($employee_arr);

                            //insert employee reference
                            $emp_ref_arr = [
                                'ref_name1' => $request->input('ref_name1'),
                                'ref_name2' => $request->input('ref_name2'),
                                'ref_contact2' => $request->input('ref_contact2'),
                                'ref_contact1' => $request->input('ref_contact1'),
                                'updated_at' => date('Y-m-d H:i:s'),
                                'updated_ip' => $request->ip(),
                                'updated_by' => Auth::user()->id
                            ];
                            Employee_reference::where('user_id', $request->input('id'))->update($emp_ref_arr);


                            return true;
                        } catch (Exception $e) {
                            DB::rollback();
                            echo $e->getMessage();
                            die();
                            return false;
                        }
                        //return redirect()->route('admin.users')->with('success', 'New user inserted successfully.');
                    } else {
                        return redirect()->route('admin.edit_user', ['id' => $request->input('id')])->with('error', 'Error occurre in insert. Try Again!');
                    }
                });

        if ($result) {
            if ($request->input('route_name') && $request->input('route_name') == 'admin.edit_profile') {
                return redirect()->route('admin.profile')->with('success', 'Profile details successfully updated.');
            } else {
                return redirect()->route('admin.users')->with('success', 'Employee details successfully updated.');
            }
        } else {
            if ($request->input('route_name') && $request->input('route_name') == 'admin.edit_profile') {
                return redirect()->route('admin.profile')->with('error', 'Error Occurred. Try Again!');
            } else {
                return redirect()->route('admin.users')->with('error', 'Error Occurred. Try Again!');
            }
        }
    }

    public function upload_education($id) {
        if (!$id) {
            return redirect()->route('admin.users')->with('error', 'Error Occurred. Try Again!');
        }
        $this->data['page_title'] = "Upload education Document";
        $this->data['id'] = $id;
        return view('admin.user.upload_education', $this->data);
    }

    public function insert_education_document(Request $request) {

        $validator_normal = Validator::make($request->all(), [
                    'degree' => 'required',
                    'specialization' => 'required',
                    'institute' => 'required',
                    'degree_time_period' => 'required',
                    'percentage' => 'required',
                    'degree_certificate' => 'required',
                    'id' => 'required'
        ]);

        if ($validator_normal->fails()) {
            return redirect()->route('admin.view_user', ['id' => $request->input('id')])->with('error', 'Please follow validation rules.');
        }

        //upload user identity document
        $degree_doc_file = '';
        if ($request->file('degree_certificate')) {
            $degree_document = $request->file('degree_certificate');

            //$file_path = $degree_document->store('public/degree_certi');

            $original_file_name = explode('.', $degree_document->getClientOriginalName());

                $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);


                $file_path = $degree_document->storeAs('public/degree_certi', $new_file_name);


            if ($file_path) {
                $degree_doc_file = $file_path;
            }
        }

        $degree_date = explode('-', $request->input('degree_time_period'));
        $degree_start_date = date('Y-m-d', strtotime(str_replace('/','-',$degree_date[0])));
        $degree_end_date = date('Y-m-d', strtotime(str_replace('/','-',$degree_date[1])));
        $edu_insert_arr = [
            'user_id' => $request->input('id'),
            'degree' => $request->input('degree'),
            'specialization' => $request->input('specialization'),
            'institute' => $request->input('institute'),
            'degree_start_time_period' => $degree_start_date,
            'degree_end_time_period' => $degree_end_date,
            'percentage' => $request->input('percentage'),
            'degree_certificate' => !empty($degree_doc_file) ? $degree_doc_file : NULL,
            'created_at' => date('Y-m-d H:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d'),
            'updated_ip' => $request->ip(),
            'updated_by' => $request->input('id')
        ];

        $result = Employee_education::insert($edu_insert_arr);

        if ($result) {
            return redirect()->route('admin.view_user', ['id' => $request->input('id')])->with('success', 'Document uploaded successfully.');
        } else {
            return redirect()->route('admin.view_user', ['id' => $request->input('id')])->with('error', 'Error Occurred. Try Again!');
        }
    }

    public function upload_experience($id) {
        if (!$id) {
            return redirect()->route('admin.users')->with('error', 'Error Occurred. Try Again!');
        }
        $this->data['page_title'] = "Upload experience Document";
        $this->data['id'] = $id;
        return view('admin.user.upload_experience', $this->data);
    }

    public function insert_experience_document(Request $request) {

        $validator_normal = Validator::make($request->all(), [
                    'exp_company_name' => 'required',
                    'id' => 'required'
        ]);

        if ($validator_normal->fails()) {
            return redirect()->route('admin.view_user', ['id' => $request->input('id')])->with('error', 'Please follow validation rules.');
        }

        //upload user identity document
        $exp_doc_file = '';
        if ($request->file('exp_document')) {

            $exp_document = $request->file('exp_document');

            //$file_path = $exp_document->store('public/exp_doc');

            $original_file_name = explode('.', $exp_document->getClientOriginalName());

                $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);


                $file_path = $exp_document->storeAs('public/exp_doc', $new_file_name);

            if ($file_path) {
                $exp_doc_file = $file_path;
            }
        }

        $exp_date = explode('-', $request->input('exp_time_period'));

        $exp_start_date = date('Y-m-d', strtotime(str_replace('/', '-', $exp_date[0])));
        $exp_end_date = date('Y-m-d', strtotime(str_replace('/', '-', $exp_date[1])));
        $exp_insert_arr = [
            'user_id' => $request->input('id'),
            'exp_company_name' => $request->input('exp_company_name'),
            'exp_job_title' => $request->input('exp_job_title'),
            'exp_location' => $request->input('exp_location'),
            'exp_start_time_period' => $exp_start_date,
            'exp_end_time_period' => $exp_end_date,
            'exp_description' => $request->input('exp_description'),
            'exp_document' => !empty($exp_doc_file) ? $exp_doc_file : NULL,
            'created_at' => date('Y-m-d H:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d'),
            'updated_ip' => $request->ip(),
            'updated_by' => $request->input('id')
        ];

        $result = Employee_experience::insert($exp_insert_arr);

        if ($result) {
            return redirect()->route('admin.view_user', ['id' => $request->input('id')])->with('success', 'Document uploaded successfully.');
        } else {
            return redirect()->route('admin.view_user', ['id' => $request->input('id')])->with('error', 'Error Occurred. Try Again!');
        }
    }

    public function upload_identity($id) {
        if (!$id) {
            return redirect()->route('admin.users')->with('error', 'Error Occurred. Try Again!');
        }
        $this->data['page_title'] = "Upload identity Document";
        $this->data['id'] = $id;
        return view('admin.user.upload_identity', $this->data);
    }

    public function insert_identity_document(Request $request) {

        $validator_normal = Validator::make($request->all(), [
                    'document_type' => 'required',
                    'identity_document' => 'required',
                    'id' => 'required'
        ]);

        if ($validator_normal->fails()) {
            return redirect()->route('admin.view_user', ['id' => $request->input('id')])->with('error', 'Please follow validation rules.');
        }

        //upload user identity document
        $identity_doc_file = '';
        if ($request->file('identity_document')) {

            $identity_document = $request->file('identity_document');

            //$file_path = $identity_document->store('public/identity_doc');

            $original_file_name = explode('.', $identity_document->getClientOriginalName());

                $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);


                $file_path = $identity_document->storeAs('public/identity_doc', $new_file_name);

            if ($file_path) {
                $identity_doc_file = $file_path;
            }
        }

        $oldDocument = IdentityDocument::where(['user_id' => $request->input('id'), 'document_type' => $request->input('document_type')])->get('identity_document')->first();

        //insert user identity document
        $doc_insert_arr = [
            'user_id' => $request->input('id'),
            'document_type' => $request->input('document_type'),
            'identity_document' => $identity_doc_file,
            'created_at' => date('Y-m-d H:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d'),
            'updated_ip' => $request->ip(),
            'updated_by' => $request->input('id'),
        ];

        if ($oldDocument) {
            $result = IdentityDocument::where(['user_id' => $request->input('id'), 'document_type' => $request->input('document_type')])->update($doc_insert_arr);
            unlink(storage_path('app/' . $oldDocument->identity_document));
        } else {
            $result = IdentityDocument::insert($doc_insert_arr);
        }

        if ($result) {
            return redirect()->route('admin.view_user', ['id' => $request->input('id')])->with('success', 'Document uploaded successfully.');
        } else {
            return redirect()->route('admin.view_user', ['id' => $request->input('id')])->with('error', 'Error Occurred. Try Again!');
        }
    }

    public function view_user($id) {
        $user_select_fields = ['users.id', 'users.name','users.digital_signature' ,'users.email', 'users.personal_email','employee.emp_code', 'employee.designation', 'employee.joining_date',
            'employee.skype', 'employee.contact_number', 'employee.emg_contact_number', 'employee.residential_address', 'employee.permanent_address',
            'employee.gender', 'employee.birth_date', 'employee.marital_status', 'employee.marriage_date', 'employee.blood_group',
            'employee.physically_handicapped', 'employee.handicap_note', 'employee.company_id', 'company.company_name',
            'employee_reference.ref_name1', 'employee_reference.ref_name2', 'employee_reference.ref_contact1','employee_reference.ref_contact2', 'department.dept_name', 'report_user.name as report_user_name'];

        $this->data["user_detail"] = $user_detail = User::join('employee', 'employee.user_id', '=', 'users.id')
                        ->leftJoin('employee_reference', 'employee_reference.user_id', '=', 'users.id')
                        ->leftJoin('employee_education', 'employee_education.user_id', '=', 'users.id')
                        ->leftJoin('company', 'company.id', '=', 'employee.company_id')
                        ->leftJoin('department', 'department.id', '=', 'employee.department_id')
                        ->leftJoin('users as report_user', 'report_user.id', '=', 'employee.reporting_user_id')
                        ->where('users.id', $id)->get($user_select_fields);



        $this->data["education_detail"] = Employee_education::where('user_id', $id)->get(['id', 'degree', 'specialization', 'institute', 'percentage', 'degree_start_time_period', 'degree_end_time_period', 'degree_certificate']);

        $this->data["experience_detail"] = Employee_experience::where('user_id', $id)->get(['id', 'exp_company_name', 'exp_job_title', 'exp_location', 'exp_description', 'exp_start_time_period', 'exp_end_time_period', 'exp_document']);

        $this->data["user_document"] = IdentityDocument::where('user_id', $id)->get(['id', 'document_type', 'identity_document']);

        if ($user_detail->count() == 0) {
            return redirect()->route('admin.users')->with('error', 'Error Occurred. Try Again!.');
        }
        $this->data['page_title'] = "Employee Details";
        $this->data['id'] = $id;
        $this->data['is_profile'] = false;
        return view('admin.user.view_user', $this->data);
    }

    public function change_status($id, $status) {
        if (User::where('id', $id)->update(['status' => $status])) {
            return redirect()->route('admin.users')->with('success', 'Status successfully updated.');
        }
        return redirect()->route('admin.users')->with('error', 'Error during operation. Try again!');
    }

    public function change_suspend_status($id, $status) {
        if (User::where('id', $id)->update(['is_suspended' => $status])) {
            return redirect()->route('admin.users')->with('success', 'Suspend status successfully changed.');
        }
        return redirect()->route('admin.users')->with('error', 'Error during operation. Try again!');
    }

    public function change_attend_type(Request $request)
    {
        $user_id = $request->id;
        $attendance_type = $request->attendance_type;

        if (User::where('id', $user_id)->update(['user_attend_type' => $attendance_type])) {
            return redirect()->route('admin.users')->with('success', 'Attendance type successfully changed.');
        }
        return redirect()->route('admin.users')->with('error', 'Error during operation. Try again!');
    }

    public function check_email(Request $request) {
        $validator = Validator::make($request->all(), [
                    'email' => 'required'
        ]);
        if ($validator->fails()) {
            echo 'false';
            die();
        }
        $email = $request->input('email');
        $user_check = User::where('email', $email)->get();
        if ($user_check->count() > 0) {
            echo 'false';
            die();
        } else {
            echo 'true';
            die();
        }
    }

    public function edit_check_email(Request $request) {
        $validator = Validator::make($request->all(), [
                    'email' => 'required'
        ]);
        if ($validator->fails()) {
            echo 'false';
            die();
        }
        $email = $request->input('email');
        $user_check = User::where('email', $email)->get();


        if ($user_check->count() > 0) {
            if ($user_check[0]->id == $request->input('user_id')) {
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

    public function get_paypal_transactions() {

        $this->data['page_title'] = "Paypal Transactions";
        return view('admin.transaction.paypal-transaction', $this->data);
    }

    public function get_paypal_transaction_list() {
        $datatable_fields = array('users.name', 'transaction.tx_no', 'transaction.amount', 'transaction.type', 'transaction.status', 'transaction.created_at');
        $request = Input::all();
        $conditions_array = ['transaction.money_type' => 'Real'];
        $getfiled = array('users.name', 'transaction.tx_no', 'transaction.amount', 'transaction.type', 'transaction.status', 'transaction.created_at');

        $table = "transaction";

        $join_str[0]['table'] = 'users';
        $join_str[0]['join_type'] = 'inner';
        $join_str[0]['join_table_id'] = 'users.id';
        $join_str[0]['from_table_id'] = 'transaction.user_id';
        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);

        die();
    }

    public function get_bet_transactions() {

        $this->data['page_title'] = "Bet Transactions";
        return view('admin.transaction.bet-transaction', $this->data);
    }

    public function get_bet_transaction_list() {
        $datatable_fields = array('users.name', 'transaction.tx_no', 'transaction.amount', 'transaction.type', 'transaction.status', 'transaction.created_at');
        $request = Input::all();
        $conditions_array = ['transaction.money_type' => 'Virtual'];
        $getfiled = array('users.name', 'transaction.tx_no', 'transaction.amount', 'transaction.type', 'transaction.status', 'transaction.created_at');

        $table = "transaction";

        $join_str[0]['table'] = 'users';
        $join_str[0]['join_type'] = 'inner';
        $join_str[0]['join_table_id'] = 'users.id';
        $join_str[0]['from_table_id'] = 'transaction.user_id';
        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);

        die();
    }

    public function check_emp_code(Request $request) {
        $validator = Validator::make($request->all(), [
                    'emp_code' => 'required'
        ]);
        if ($validator->fails()) {
            echo 'false';
            die();
        }

        $emp_count = Employees::where('emp_code', $request->input('emp_code'))->get('id')->count();

        if ($emp_count > 0) {
            echo 'false';
            die();
        } else {
            echo 'true';
            die();
        }
    }

    public function delete_user($id) {
        if (User::where('id', $id)->delete()) {
            return redirect()->route('admin.users')->with('success', 'Delete employee successfully updated.');
        }
        return redirect()->route('admin.users')->with('error', 'Error during operation. Try again!');
    }

    public function delete_education($id,$user_id) {
        if (Employee_education::where('id', $id)->delete()) {
            return redirect()->route('admin.view_user',['id'=>$user_id])->with('success', 'Education record is deleted successfully.');
        }
        return redirect()->route('admin.view_user',['id'=>$user_id])->with('error', 'Error during operation. Try again!');
    }

    public function delete_experience($id,$user_id) {

        if (Employee_experience::where('id', $id)->delete()) {
            return redirect()->route('admin.view_user',['id'=>$user_id])->with('success', 'Experience record is deleted successfully.');
        }
        return redirect()->route('admin.view_user',['id'=>$user_id])->with('error', 'Error during operation. Try again!');
    }

    public function get_device_user(){
        $this->data['page_title'] = 'Device Allow';
        if (Auth::user()->role == config('constants.SuperUser') || Auth::user()->role == config('constants.Admin')) {
            return view('dashboard.allow_user', $this->data);
        } else {
            return redirect()->route('admin.dashboard')->with('error', 'Access denied. You do not have permission to access this module.');
        }
    }

    public function get_allow_user_list_all(){
        $datatable_fields = array('users.name', 'device_allow.imei_number', 'device_allow.model_name','device_allow.status', 'device_allow.created_at');
        $request = Input::all();
        $conditions_array = [];

        $join_str = [];

        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'users';
        $join_str[0]['join_table_id'] = 'users.id';
        $join_str[0]['from_table_id'] = 'device_allow.user_id';


        $getfiled = array('device_allow.id', 'users.name', 'device_allow.imei_number', 'device_allow.model_name', 'device_allow.status', 'device_allow.created_at');
        $table = "device_allow";

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);

        die();
    }

    public function approved_device_user($id){
        $update_arr = [
            'status' => "Approved",
            'updated_ip' => \Request::ip()
        ];

        if(DeviceAllow::whereId($id)->update($update_arr)){
            return redirect()->route('admin.get_device_user')->with('success', 'Status successfully updated.');
        }else{
            return redirect()->route('admin.get_device_user')->with('error', 'Error during operation. Try again!');
        }
    }

    public function delete_device_user($id){
        if (DeviceAllow::whereId($id)->delete()) {
            return redirect()->route('admin.get_device_user')->with('success', 'Device user deleted successfully.');
        } else {
            return redirect()->route('admin.get_device_user')->with('error', 'Error during operation. Try again!');
        }
    }
    public function loan_statement($id){
        $check_result = Permissions::checkPermission(10, 5);
        if (!$check_result) {
            $check_result = Permissions::checkPermission(10, 1);
            if (!$check_result) {
                return redirect()->route('admin.dashboard')->with('error', 'Access Denied. You are not authorized to access that page.');
            }
        }

        $this->data['page_title'] = "Employee loan statement";
        $this->data['user_id'] = $id;
        $access_level = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => 10])->get()->first();
        $this->data['access_rule'] = '';
        if (!empty($access_level)) {
            $this->data['access_rule'] = $access_level->access_level;
        }
        
        return view('admin.user.loan_statement', $this->data);
    }

    public function selected_employee_loan_list(Request $request) {
        $datatable_fields = array('employee_loan.cheque_no', 'employee_loan.loan_type', 'employee_loan.loan_amount',
            'employee_loan.loan_expected_month', 'employee_loan.loan_emi_start_from',
            'employee_loan.loan_terms', 'employee_loan.loan_descption', 'employee_loan.first_approval_status',
            'employee_loan.second_approval_status', 'employee_loan.third_approval_status', 'employee_loan.loan_status');
        // $request = Input::all();
        $check_result = Permissions::checkPermission(10, 5);
        /* if(!$check_result){
          $check_result=Permissions::checkPermission(10,1);
          if(!$check_result){
          return redirect()->route('admin.dashboard')->with('error','Access Denied. You are not authorized to access that page.');
          }
          else {
          $conditions_array = ['users.id'=>Auth::user()->id];
          }
          }
          else {
          $conditions_array = [];
          } */
        $conditions_array = ['users.id' => $request->input('user_id')];

        $getfiled = array('employee_loan.cheque_no','users.name', 'employee_loan.user_id','employee_loan.loan_type', 'employee_loan.first_approval_datetime', 'employee_loan.second_approval_datetime', 'employee_loan.third_approval_datetime', 'employee_loan.first_approval_id', 'employee_loan.second_approval_id', 'employee_loan.third_approval_id', 'employee_loan.first_approval_status',
            'employee_loan.second_approval_status', 'employee_loan.reject_note','employee_loan.third_approval_status', 'employee_loan.loan_amount', 'employee_loan.loan_expected_month', 'employee_loan.loan_emi_start_from', 'employee_loan.loan_terms', 'employee_loan.loan_descption', 'employee_loan.status', 'employee_loan.loan_status', 'employee_loan.id');
        $table = "employee_loan";

        $join_str[0]['join_type'] = '';
        $join_str[0]['table'] = 'users';
        $join_str[0]['join_table_id'] = 'users.id';
        $join_str[0]['from_table_id'] = 'employee_loan.user_id';

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);

        die();
    }
}
