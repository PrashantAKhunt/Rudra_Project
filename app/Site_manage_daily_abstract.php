<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Site_manage_daily_abstract extends Model
{
    protected $table = "site_manage_daily_abstract";
    public $timestamps = false;
    public function sub_item() {
        return $this->belongsTo('App\Site_manage_boq', 'boq_id', 'id');
    }
}
