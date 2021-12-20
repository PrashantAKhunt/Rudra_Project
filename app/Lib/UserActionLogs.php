<?php

namespace App\Lib;

use Illuminate\Support\Facades\Storage;
use App\UserActionLog;

class UserActionLogs
{
    public function action($data)
    {
        return UserActionLog::insert($data);
    }
}
