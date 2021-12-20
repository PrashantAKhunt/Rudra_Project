<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vehicle_Maintenance extends Model
{
    protected $table="vehicle_maintenance";
    public function vehicleImage() {
        return $this->hasMany('App\Vehicle_image', 'vehicle_maintenance_id', 'id')->orderBy('id', 'ASC');
    }

    public function asset() {
        return $this->hasOne('App\Asset', 'id', 'asset_id');
    }
}
