<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Lib\NotificationTask;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Exception;
use App\Role_module;
use App\Email_format;
use App\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\Mails;

class APINotificationController extends Controller
{

    private $page_limit = 20;
    public $data;
    private $notification_task;

    public function __construct()
    {
        $this->super_admin = \App\User::where('role', config('constants.SuperUser'))->first();
        $this->notification_task = new NotificationTask();

    }

    public function apiUnraedNotificationByUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();

        $response_data = $this->notification_task->getUnreadNotificationByUser($request_data['user_id']);

        return response()->json(['status' => true, 'msg' => "Got Notifications Array", 'data' => $response_data]);

    }

    public function apimarkraedNotificationByUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'notification_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        DB::table('notifications')->where('id',$request_data['notification_id'])->update(['read_at'=> date('Y-m-d H:i:s')]);
        // $notification_array = json_decode($request_data['user_object'],true);
        // $notification_object  = (object) $notification_array;
        // //dd($notification_object);
        // $this->notification_task->markReadNotification($notification_object);

        return response()->json(['status' => true, 'msg' => "Notification mark as read", 'data' => []]);

    }

    public function apimarkraedNotificationByUserId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'topic' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data = $request->all();
        $query = DB::table('notifications')->where('notifiable_id',$request_data['user_id'])->whereNull('read_at');
        $query->where(function($query) use ($request_data) {
           
               $query->where('data', 'like', '%'.$request_data['topic'].'%')
                     ->where('data', 'like', '%'.$request_data['message'].'%');
            
        })
        ->update(['read_at'=> date('Y-m-d H:i:s')]);

        return response()->json(['status' => true, 'msg' => "Notification mark as read", 'data' => []]);

    }

    
}
