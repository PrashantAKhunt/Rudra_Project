<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Job_opening_consultant extends Model
{
    protected $table="job_opening_consultant";
    
    public function consultant(){
        return $this->belongsTo('App\Recruitment_consultant','consultant_id');
    }
    
}
