<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Resignation extends Model
{
    protected $table="resignation";

    public function user() {
        return $this->hasOne('App\User', 'id', 'user_id');
    }
}
