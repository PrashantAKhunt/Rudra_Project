<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee_detail extends Model
{
    protected $table="employee_detail";
    protected $fillable=['emp_id','company_id','bank_id','basic_salary','working_days','deduction_month','deduction_other'
        ,'hra','oa','pf_amount','net_salary','salary_date','created_at','updated_at'
        ,'created_ip','updated_ip','updated_by'];
}
