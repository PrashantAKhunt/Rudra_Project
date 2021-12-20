<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Site_manage_bill extends Model
{
    protected $table="site_manage_bill"; 

    public function sub_item() {
        return $this->belongsTo('App\Site_manage_boq', 'boq_id', 'id');
    }

    public function get_boq_detail(){
    	return $this->belongsTo('App\Site_manage_boq', 'boq_id', 'id');	
    }
}