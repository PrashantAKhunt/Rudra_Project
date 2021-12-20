<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employees extends Model {

    protected $table = "employee";

    public function user() {
        return $this->hasOne('App\User', 'id', 'user_id');
    }
    
    public function company() {
        return $this->hasOne('App\Companies', 'id', 'company_id');
    }
    
    public function department(){
        return $this->hasOne('App\Department','id','department_id');
    }

    public function getFormetedDate($days) {
        $days_list = [];
        $currentMonth = date('m');
        $currentYear = date('Y');
        $nextYear = $currentYear + 1;
        $lastMonth = 12;
        $firstMonth = 1;

        foreach ($days as $key => $value) {
            $days_list[$key]['id'] = $value['user_id'];
            $days_list[$key]['designation'] = $value['designation'];
            $days_list[$key]['emp_code'] = $value['emp_code'];
            $days_list[$key]['name'] = isset($value['user']['name']) ? $value['user']['name'] : NULL;
            $days_list[$key]['profile_image'] = isset($value['user']['profile_image']) ? $value['user']['profile_image'] : NULL;
            $title = isset($value['birth_date']) ? "Birthday On " : (isset($value['marriage_date']) ? "Marriage Anniversary On " : "Work Anniversary On ");
            if ($currentMonth == $lastMonth && $value['month'] == $firstMonth)
                $days_list[$key]['date'] = $title . date('l, d F', strtotime($nextYear . '-' . $value['monthdate']));
            else {
                $days_list[$key]['date'] = $title . date('l, d F', strtotime($currentYear . '-' . $value['monthdate']));
            }
        }
        return $days_list;
    }
    
    

}
