<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectManager extends Model {
    protected $table = 'project_manager';

    public function get_user_data(){
        return $this->belongsTo('App\User','user_id','id');
    }
}
