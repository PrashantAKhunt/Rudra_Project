<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Input;
use App\Common_query;
use App\Employees;
use App\EmployeesBankDetails;
use App\EmployeesLoans;
use App\Companies;
use App\User;
use App\TaxDeclaration;
use App\EmployeeForm16;
use App\Role_module;
use Illuminate\Support\Facades\Validator;
use App\Imports\EmployeeSalaryImport;
use Maatwebsite\Excel\Facades\Excel;
use DB;
use Auth;
use App\Lib\Permissions;

class TaxDeclarationController extends Controller
{
    public $data;

    public function __construct() {
        $this->data['module_title'] = "Employees";
        $this->data['module_link'] = "admin.employees";
    }

    public function index(Request $request){
        $this->data['access_rule']  = '';
        $this->data['page_title']   = "Tax Declaration";
        $this->data['user']         =  User::where("status","Enabled")->where('role','!=',1)->get()->pluck('name', 'id');
        $this->data['records']      = [];
        $this->data['selectedUser'] = [];
        $this->data['selectedYear'] = "";
        $this->data['report_type']  = "";
        $this->data['form_data']     = "";
        $access_level               = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => 8])->get()->first();
        if(!empty($access_level))
        {
            $this->data['access_rule'] = $access_level->access_level;
        }

        $user_id = $request->input('user_id');
        
        $this->data['selectedYear'] = $request->get('year');
        $year_tax = $request->get('year');
        
        if(!empty($year_tax)) {
            
            $Year = explode("-", $year_tax);
            $strFirstyear = str_replace("/", "-", $Year[0]);
            $strLastyear  = str_replace("/", "-", $Year[1]);        
        }else{
            
            $year_tax = date('Y', strtotime("-1 year")) . "-" . date('Y');
            $strFirstyear = date('Y', strtotime("-1 year"));
            $strLastyear  = date('Y'); 

        }

       
        $this->data['user_id'] = $user_id;
        if(!empty($user_id)) {
           
            $this->data['tax_data'] = TaxDeclaration::select('users.name','employee_tax_declaration.section_name','employee_tax_declaration.deduction_name','employee_tax_declaration.declaration','employee_tax_declaration.proofs','employee_tax_declaration.status','employee_tax_declaration.financial_start_year','employee_tax_declaration.financial_end_year','employee_tax_declaration.id')
                                               ->leftJoin('users', 'users.id', '=', 'employee_tax_declaration.user_id')
                                               ->where('employee_tax_declaration.user_id',$user_id)
                                               ->where('employee_tax_declaration.financial_start_year',$strFirstyear)
                                               ->where('employee_tax_declaration.financial_end_year',$strLastyear)
                                               ->get();

            $name = EmployeeForm16::select('form_16')->where('user_id',$user_id)->where('year',$year_tax)->get();
             
            if(!empty($name[0])) {
                $this->data['form_data'] = asset('storage/'.str_replace('public/','',''.$name[0]->form_16));   
            }

            
            return view('admin.tax_declaration.index', $this->data);
        }
        else
        {

            
            $name = EmployeeForm16::select('form_16')->where('user_id',Auth::user()->id)->get();
             
            if(!empty($name[0])) {
                $this->data['form_data'] = asset('storage/'.str_replace('public/','',''.$name[0]->form_16));   
            }

            $this->data['tax_data'] = TaxDeclaration::select('users.name','employee_tax_declaration.section_name','employee_tax_declaration.deduction_name','employee_tax_declaration.declaration','employee_tax_declaration.proofs','employee_tax_declaration.status','employee_tax_declaration.financial_start_year','employee_tax_declaration.financial_end_year','employee_tax_declaration.id')
                                                ->leftJoin('users', 'users.id', '=', 'employee_tax_declaration.user_id')
                                                ->where('employee_tax_declaration.user_id',Auth::user()->id)
                                                ->get();
        }
        return view('admin.tax_declaration.index', $this->data);
    }

    public function employee_tax_declaration_list() {
        $datatable_fields = array('users.name','employee_tax_declaration.section_name','employee_tax_declaration.deduction_name','employee_tax_declaration.declaration','employee_tax_declaration.proofs','employee_tax_declaration.status','employee_tax_declaration.financial_start_year','employee_tax_declaration.financial_end_year','employee_tax_declaration.id');
        $request = Input::all();
        $conditions_array = ['user_id'=>Auth::user()->id];

        $getfiled = array('users.name','employee_tax_declaration.section_name','employee_tax_declaration.deduction_name','employee_tax_declaration.declaration','employee_tax_declaration.proofs','employee_tax_declaration.status','employee_tax_declaration.financial_start_year','employee_tax_declaration.financial_end_year','employee_tax_declaration.id');
        $table = "employee_tax_declaration";
        
        $join_str[0]['join_type']='';
        $join_str[0]['table'] = 'users';
        $join_str[0]['join_table_id'] ='users.id';
        $join_str[0]['from_table_id'] = 'employee_tax_declaration.user_id';

        echo Common_query::get_list_datatable_ajax($table, $datatable_fields, $conditions_array, $getfiled, $request, $join_str);
                                                  
        die();
    }
     
    public function edit_tax_declaration($id) {
        $this->data['tax_detail'] = TaxDeclaration::where('id', $id)->get();
        echo json_encode($this->data['tax_detail']);
    }
    public function update(Request $request) {
        $tax_id = $request->input('id'); 
        
        //upload user profile image
        if ($request->hasFile('proofs')) {
            $profile_image = $request->file('proofs');                               
            //$file_path = $profile_image->store('public/tax_doc');
            $original_file_name = explode('.', $profile_image->getClientOriginalName());
    
                $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);
    
                $file_path = $profile_image->storeAs('public/tax_doc', $new_file_name); 
            if ($file_path) {
                $profile_image_file = $file_path;
            }

            $tax_arr = [
                'proofs'     => $profile_image_file
             ];
            TaxDeclaration::where('id', $tax_id)->update($tax_arr);     
        }
        $tax_arr = [
            'declaration' => $request->input('declaration'),
            'created_at' => date('Y-m-d h:i:s'),
            'created_ip' => $request->ip(),
            'updated_at' => date('Y-m-d h:i:s'),
            'updated_ip' => $request->ip(),
        ];
        TaxDeclaration::where('id', $tax_id)->update($tax_arr);
        $userTaxData = TaxDeclaration::where('id', $tax_id)->get();

        return redirect('/tax_declaration?user_id='.$userTaxData[0]->user_id)->with('success', 'Employee tax declaration updated successfully.');
    }
    public function generate_form_16()
    {
        $this->data['page_title']="Employee Form 16";
        $this->data['access_rule'] = '';
        $access_level                  = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => 8])->get()->first();
        if(!empty($access_level))
        {
            $this->data['access_rule'] = $access_level->access_level;
        }

        $articles = DB::table('users')
            ->select('users.id as users_id','users.name','employee.emp_code')
            ->join('employee', 'users.id', '=', 'employee.user_id')
            ->get();
        foreach ($articles as $key => $value) {
            $data['id'][]   = $value->users_id;
            $data['name'][] = $value->name."-".$value->emp_code;
        }
        
        $this->data['employee']  = array_combine($data['id'], $data['name']);

        return view('admin.tax_declaration.form_16', $this->data);
    }

    public function get_user_form(Request $request)
    {
         
        $user_id = $request->input('user_id'); 
        $this->data['employee_tax_declaration'] = DB::table('employee_tax_declaration')
            ->select('*')
            ->where('user_id',$user_id)
            ->get();
            //->toArray();
        //echo json_encode(['data'=>$employee_tax_declaration]);
        echo json_encode($this->data['employee_tax_declaration']);
        die();
    }

    public function upload_user_form_16(Request $request)
    {
        //upload user profile image
        $this->data['access_rule']  = '';
        $this->data['page_title']   = "Tax Declaration";
        $this->data['user']         =  User::where("status","Enabled")->where('role','!=',1)->get()->pluck('name', 'id');
        $this->data['records']      = [];
        $this->data['selectedUser'] = [];
        $this->data['date']         = "";
        $this->data['report_type']  = "";
        $this->data['form_data']     = "";
        
        $access_level = Role_module::where(['role_id' => Auth::user()->role, 'module_id' => 8])->get()->first();
        
        if(!empty($access_level))
        {
            $this->data['access_rule'] = $access_level->access_level;
        }

        if ($request->hasFile('form_16') && !empty($request->input('form16_user_id'))) {
            $profile_pdf = $request->file('form_16');                               
            //$file_path   = $profile_pdf->store('public/reports/cheque_report');
            $original_file_name = explode('.', $profile_pdf->getClientOriginalName());
    
                $new_file_name = str_replace(' ', '_', $original_file_name[0]) . time() . '.' . end($original_file_name);
    
                $file_path = $profile_pdf->storeAs('public/reports/cheque_report', $new_file_name); 
            
            if ($file_path) {
                $form_16_pdf = $file_path;
            }

            $user_id                   = $request->input('form16_user_id');
            $year                      = $request->input('form16_year');
            $name = EmployeeForm16::where('user_id',$user_id)->where('year',$year)->get();
            if(!empty($name[0]))
            {
                $emp_form_arr = [
                    'form_16'     => $form_16_pdf
                ];
                EmployeeForm16::where('user_id', $user_id)->where('year',$year)->update($emp_form_arr);    
            }
            else
            {
                $employeeformModel = new EmployeeForm16();
                $employeeformModel->form_16    = $form_16_pdf;
                $employeeformModel->user_id    = $user_id;
                $employeeformModel->year       = $year;
                $employeeformModel->created_at = date('Y-m-d h:i:s');
                $employeeformModel->created_ip = $request->ip();
                $employeeformModel->updated_at = date('Y-m-d h:i:s');
                $employeeformModel->updated_ip = $request->ip();
                $employeeformModel->save();
            }
            $this->data['user_id'] = $user_id; 
            return redirect('/tax_declaration?user_id='.$user_id.'&year='.$year)->with('success', 'Upload Employee Form16 successfully.');  
        }
        else
        {
            return redirect()->route('admin.tax_declaration')->with('error', 'Error Occurred in Upload. Try Again!');
        }
    }

    public function save_form_16_data()
    {

        $userdata = User::get();
        $arrTax = ['EPF (Deducted from Salary)','VPF (Deducted from Salary)','PPF','Senior Citizen Savings Scheme','Housing loan (Principal)','Mutual Fund','National Saving Certificate','Unit Link Insurance Plan','Life Insurance Policy','Education Tuition Fees','Schedule Bank FD','Post Office Time Deposit','Deferred Annuity','Super Annuation','NABARD notified bonds','Sukanya Samriddhi Yojna','Mutual Fund Pension','NPS Employee Contribution','Other'];
        foreach ($userdata as $key => $uservalue) {
            foreach ($arrTax as $key => $value) {
                $time = strtotime("-1 year", time());
                $date = date("Y", $time);
                $emp_ref_arr = [
                    'user_id' => $uservalue->id,
                    'section_name' => '80C',
                    'deduction_name' => $value,
                    'financial_start_year' =>$date,
                    'financial_end_year' =>date('Y'),                           
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                TaxDeclaration::insert($emp_ref_arr);
            }
        } 
    }
}
