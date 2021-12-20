<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Site_manage_boq extends Model
{
    protected $table = "site_manage_boq";
    
    public function company_detail() {
        return $this->belongsTo('App\Companies', 'company_id', 'id');
    }
    
    public function project_detail() {
        return $this->belongsTo('App\Projects', 'project_id', 'id');
    }
    
    public function sub_item() {
        return $this->hasMany('App\Site_manage_boq','parent_boq','id');
    }
    
    public function get_item_blocks() {
        return $this->hasMany('App\Site_manage_item_block','boq_id','id');
    }
    
}
