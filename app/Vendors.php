<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vendors extends Model
{
    protected $table="vendor";

    public function company()
    {
        return $this->hasOne('App\Companies', 'id', 'company_id');
    }
}
