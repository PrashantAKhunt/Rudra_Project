<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class RemoteAttendanceRequest extends Model
{
    protected $table="remote_attendance_request";

    protected $fillable = ['user_id','place_id','reason','date','created_ip','updated_ip'];

    public function places() {
        return $this->hasOne('App\RemoteAttendancePlace','id','place_id');
    }
}