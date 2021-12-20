<?php
   
namespace App\Imports;
   
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
   
use App\Employees;
use App\Employee_detail;
use App\Companies;
use App\Banks;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
class EmployeeSalaryImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        
        //get employee id
        $emp_data= Employees::where('emp_code',$row['employee_code'])->get(['id']);
        //get company id
        $com_data= Companies::where('company_name',$row['company_name'])->get(['id']);
        //get bank id
        $bank_data= Banks::where('ac_number',$row['bank_ac_number'])->get(['id']);
        return new Employee_detail([
            'emp_id'     => $emp_data[0]->id,
            'company_id'    => $com_data[0]->id, 
            'salary_date' => date('Y-m-d',strtotime($row['date'])), 
            'bank_id'     => $bank_data[0]->id,
            'basic_salary'    => $row['basic_salary'], 
            'working_days' => $row['working_days'], 
            'deduction_month'     => $row['deduction'],
            'deduction_other'    => $row['other_deduction'], 
            'hra' => $row['hra'], 
            'oa'     => $row['oa'],
            'pf_amount'    => $row['pf'], 
            'pt'=>$row['pt'],
            'net_salary' => $row['basic_salary']-$row['deduction']-$row['other_deduction']+$row['hra']+$row['oa']+$row['pf']+$row['pt'], 
            'created_at'     => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'), 
            'created_ip' => Request::ip(), 
            'updated_ip'     => Request::ip(),
            'updated_by'=>Auth::user()->id
        ]);
    }
}