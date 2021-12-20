<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Job_opening extends Model
{
    protected $table="job_openings";
    
    public function consultants() {
        return $this->hasMany('App\Job_opening_consultant');
    }
    
}
