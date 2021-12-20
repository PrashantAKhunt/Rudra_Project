<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Travel_option extends Model
{
    protected $table="travel_option";

    public function travel_info() {
        return $this->hasMany('App\Travel_info', 'travel_option_id', 'id');
    }

    public function booking_files() {
        return $this->hasMany('App\Travel_booking_files', 'travel_option_id', 'id');
    }
}
