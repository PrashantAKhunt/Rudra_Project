<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Clients extends Model
{
    protected $table="clients";

    public function clients_details() {
        return $this->hasMany('App\ClientDetail', 'client_id', 'id')->orderBy('id', 'ASC');
    }
}
