<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeaveCategory extends Model
{
    protected $table="leave_category";
    protected $fillable=['name','frequency','quantity','status','created_at','updated_at','created_ip','updated_ip','updated_by'];	
}
