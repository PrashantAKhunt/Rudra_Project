<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
     protected $table="payroll";
	 
	 public function user()
	{
		return $this->hasOne('App\User','id','user_id');
	}
}


