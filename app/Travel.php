<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Travel extends Model
{
    protected $table="travel";

    public function company() {
        return $this->hasOne('App\Companies','id','company_id');
    }

    public function project() {
        return $this->hasOne('App\Projects','id','project_id');
    }

    public function user() {
        return $this->hasOne('App\User','id','booked_by');
    }

    public function travel_info() {
        return $this->hasMany('App\Travel_info', 'travel_id', 'id')->orderBy('id', 'ASC');
    }
}
