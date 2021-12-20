<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    protected $table="interview";

    public function interview_result() {
        return $this->hasMany('App\InterviewResult', 'interview_id', 'id')->orderBy('created_at', 'ASC');
    }

}
