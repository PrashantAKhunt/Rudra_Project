<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;
use App\Employees;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller {

    private $page_limit = 20;

    /*
     * * Leave Statestics
     */

    public function get_upcomings(Request $request) {

        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $whereCondition = ['user_id' => $request->input('user_id')];

        $birthdays = Employees::whereNOTIn('user_id', [$request->input('user_id')])
                        ->join('users','users.id','=','employee.user_id')
                        ->where('users.is_user_relieved',0)
                        ->whereRaw('DAYOFYEAR(curdate()) <= DAYOFYEAR(birth_date) AND DAYOFYEAR(curdate()) + 7 >=  dayofyear(birth_date)')
                        ->selectRaw('designation,emp_code,user_id, birth_date, birth_date, DATE_FORMAT(birth_date, "%m%d") as order_date, DATE_FORMAT(birth_date, "%m-%d") as monthdate, DATE_FORMAT(birth_date, "%m") as month')
                        ->with(['user' => function($query) {
                                $query->select('id', 'name', 'profile_image')->where('status','Enabled')->where('is_user_relieved',0);
                            }])->orderBy('order_date', 'asc')->get()->toArray();

        $marriage = Employees::whereNOTIn('user_id', [$request->input('user_id')])
                ->join('users','users.id','=','employee.user_id')
                ->where('users.is_user_relieved',0)
                        ->whereRaw('DAYOFYEAR(curdate()) <= DAYOFYEAR(marriage_date) AND DAYOFYEAR(curdate()) + 7 >=  dayofyear(marriage_date)')
                        ->selectRaw('designation,emp_code,user_id, marriage_date, DATE_FORMAT(marriage_date, "%m%d") as order_date, DATE_FORMAT(marriage_date, "%m-%d") as monthdate, DATE_FORMAT(marriage_date, "%m") as month')
                        ->with(['user' => function($query) {
                                $query->select('id', 'name', 'profile_image')->where('status','Enabled')->where('is_user_relieved',0);
                            }])->orderBy('order_date', 'asc')->get()->toArray();

        $work = Employees::whereNOTIn('user_id', [$request->input('user_id')])
                ->join('users','users.id','=','employee.user_id')
                ->where('users.is_user_relieved',0)
                        ->whereRaw('DAYOFYEAR(curdate()) <= DAYOFYEAR(joining_date) AND DAYOFYEAR(curdate()) + 7 >=  dayofyear(joining_date)')
                        ->selectRaw('designation,emp_code,user_id, joining_date, DATE_FORMAT(joining_date, "%m%d") as order_date, DATE_FORMAT(joining_date, "%m-%d") as monthdate, DATE_FORMAT(joining_date, "%m") as month')
                        ->where('joining_date','!=',date('Y-m-d'))
                        ->with(['user' => function($query) {
                                $query->select('id', 'name', 'profile_image')->where('status','Enabled')->where('is_user_relieved',0);
                            }])->orderBy('order_date', 'asc')->get()->toArray();

        $employee = new Employees();
        $upcoming_days = $employee->getFormetedDate(array_merge($birthdays, $marriage, $work));
        foreach ($upcoming_days as $key => $upcomming) {
            if ($upcomming['profile_image']) {
                $upcoming_days[$key]['profile_image'] = asset('storage/' . str_replace('public/', '', $upcomming['profile_image']));
            } else {
                $upcoming_days[$key]['profile_image'] = "";
            }
        }
        return response()->json(['status' => true, 'msg' => 'Birthday and Anniversarise', 'data' => $upcoming_days]);
    }

}
