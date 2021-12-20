<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Companies extends Model
{
    protected $table="company";

    public static function getCompany() {
        return self::orderBy('company_name')->pluck('company_name', 'id');
    }
}
