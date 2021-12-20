<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Driver_expense extends Model
{
    protected $table="driver_expense";

    public function assets() {
        return $this->hasOne('App\Asset', 'id', 'asset_id');
    }
}
