<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee_Insurance extends Model
{
    protected $table="employee_insurance";


    public function get_insurance_policy()
    {
        return $this->hasMany(Insurance_Upload_Policy::class,'insurance_id','id');
    }
}
