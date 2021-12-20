<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HotelBooking extends Model
{
	protected $table="hotel_booking";

	public function company() {
        return $this->hasOne('App\Companies','id','company_id');
    }

    public function project() {
        return $this->hasOne('App\Projects','id','project_id');
    }

    public function user() {
        return $this->hasOne('App\User','id','booked_by');
    }
}
