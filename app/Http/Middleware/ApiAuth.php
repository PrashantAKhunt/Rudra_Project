<?php

namespace App\Http\Middleware;

use Closure;
use App\Login_log;
use Illuminate\Http\Request;
class ApiAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $allowed_route=['api.get_leave_approval_list','api.get_leave_assigned_work','api.get_all_pending_leave',
            'api.get_attendance_detail','api.get_approval_attendance_list','api.get_all_expense','api.get_upcoming_holiday',
            'api.get_upcomings','api.get_driver_expense_approval_list','api.trip_approval_list',
            'api.pre_sign_letterhead_approval_list','api.letterhead_approval_list'];
        $token_check= Login_log::where(['auth_token'=>$request->header('Authorization'),'user_id'=>$request->input('user_id')])->get('id');
        if($token_check->count()==0){
            
            if(in_array($request->route()->getName(), $allowed_route)){
                return $next($request);
            }
            return response()->json(['status'=>false,'data'=>[],'msg'=> config('errors.access_denied.msg'),'error'=>config('errors.access_denied.code')]);
        }
        return $next($request);
    }
}
