<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


class Leaves extends Model
{
    protected $table="leaves";
    protected $fillable=['user_id','subject','description','start_date','end_date','start_day','end_day','leave_category_id','approver_id','notify_id','leave_status','status','created_at','updated_at','created_ip','updated_ip','updated_by','assign_work_user_id','assign_work_details'];
	
	public function leavecategory()
	{
		return $this->hasOne('App\LeaveCategory','id','leave_category_id');
	}	
}
