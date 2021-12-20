<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RemoteAttendancePlace extends Model
{
    protected $table="remote_attendance_place";
    protected $fillable = ['place','created_ip','updated_ip'];
}
