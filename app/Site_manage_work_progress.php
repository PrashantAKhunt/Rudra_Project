<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Site_manage_work_progress extends Model
{
    protected $table="site_manage_work_progress";
    
    public function boq_detail() {
        return $this->belongsTo('App\Site_manage_boq','boq_id','id');
    }
    
}
