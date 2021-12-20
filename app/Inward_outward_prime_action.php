<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Inward_outward_prime_action extends Model
{
    protected $table="inward_outward_prime_action";

    public function emp_distrubution() {
        return $this->hasMany('App\Inward_outward_distrubuted_work', 'inward_outward_prime_action_id', 'id');
    }
}
