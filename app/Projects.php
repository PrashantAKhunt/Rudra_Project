<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Projects extends Model
{
    protected $table="project";

    public function project_emp() {
        return $this->hasMany('App\ProjectManager', 'project_id', 'id')->orderBy('project_manager.id', 'DESC');
    }

}
