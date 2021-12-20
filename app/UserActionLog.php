<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserActionLog extends Model
{
    protected $table = "user_action_log";

    public function get_user_name()
    {
        return $this->hasOne('App\User','id','user_id');
    }
}
