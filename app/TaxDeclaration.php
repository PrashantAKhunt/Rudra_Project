<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Auth;

class TaxDeclaration extends Model
{
    protected $table="employee_tax_declaration";

    public static function addTax($user_id)
    {
    	$arrTax = ['EPF (Deducted from Salary)','VPF (Deducted from Salary)','PPF','Senior Citizen Savings Scheme','Housing loan (Principal)','Mutual Fund','National Saving Certificate','Unit Link Insurance Plan','Life Insurance Policy','Education Tuition Fees','Schedule Bank FD','Post Office Time Deposit','Deferred Annuity','Super Annuation','NABARD notified bonds','Sukanya Samriddhi Yojna','Mutual Fund Pension','NPS Employee Contribution','Other'];

    	foreach ($arrTax as $key => $value) {
    		$time = strtotime("-1 year", time());
  			$date = date("Y", $time);
    		$emp_ref_arr = [
				'user_id' => $user_id,
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
