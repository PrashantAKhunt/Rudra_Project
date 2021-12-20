<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoanTransaction extends Model
{
    protected $table="loan_transaction";

    public function employee_loan() {
        return $this->hasMany('App\EmployeesLoans', 'id', 'loan_id');
    }
    public function user() {
        return $this->hasOne('App\User', 'id', 'user_id');
    }
}
