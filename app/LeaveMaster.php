<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeaveMaster extends Model
{
    protected $table="leave_master";
    protected $fillable=['user_id','leave_category_id','balance','status','created_at','updated_at','created_ip','updated_ip','updated_by'];

	public function leavecategory()
	{
		return $this->hasOne('App\LeaveCategory','id','leave_category_id');
	}
}