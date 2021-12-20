<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Site_manage_item_block extends Model
{
    protected $table="site_manage_item_block";

    public function get_boq_item(){
    	return $this->belongsTo('App\Site_manage_boq', 'boq_id', 'id');
    }
}
