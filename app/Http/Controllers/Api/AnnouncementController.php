<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Lib\NotificationTask;
use App\Lib\CommonTask;
use App\Announcements;
use Illuminate\Support\Facades\Validator;

class AnnouncementController extends Controller {

    private $page_limit = 20;
    public $common_task;
    public $notification_task;
    private $total_hour_per_day = 8;
    private $module_id = 23;
    private $super_admin;

    public function __construct() {
        $this->common_task = new CommonTask();
        $this->notification_task = new NotificationTask();

        $this->super_admin = \App\User::where('role', 1)->first();
    }

    public function get_announcement_list(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'page_number' => 'required'
                    
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];
        
        $offset = ($request_data['page_number'] - 1) * $this->page_limit;

        $announcement_list= Announcements::whereRaw('FIND_IN_SET('.$request_data['user_id'].',user_id)')
                            ->offset($offset)
                            ->limit($this->page_limit)
                            ->orderBy('created_at','DESC')
                            ->get();
        
        if($announcement_list->count()==0){
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }
        
        $response_data['announcement_list']=$announcement_list;
        return response()->json(['status' => true, 'msg' => "Record Found.", 'data' => $response_data]);
    }
    
    public function get_my_announcement_list(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'page_number' => 'required'
                    
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];
        
        $offset = ($request_data['page_number'] - 1) * $this->page_limit;

        $announcement_list= Announcements::
                            offset($offset)
                            ->limit($this->page_limit)
                            ->orderBy('created_at','DESC')
                            ->get();
        
        if($announcement_list->count()==0){
            return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
        }
        
        $response_data['announcement_list']=$announcement_list;
        return response()->json(['status' => true, 'msg' => "Record Found.", 'data' => $response_data]);
    }
    
    public function add_announcement_details(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'title' => 'required',
                    'description' => 'required',
                    'start_date' => 'required',
                    'end_date' => 'required',
                    'announcement_user_id'  =>'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];
        //check role of user is of driver
        $logged_in_user = \App\User::where('id', $request_data['user_id'])->get();

        $annoucement_arr = [
            'title'       => $request_data['title'],
            'description' => $request_data['description'],
            'start_date'  => $request_data['start_date'],
            'end_date'    => $request_data['end_date'],
            'user_id'     => $request_data['announcement_user_id'],
            'status'      => 'Enabled',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'created_ip' => $request->ip(),
            'updated_ip' => $request->ip(),
            'updated_by' => $request_data['user_id'],
        ];

        Announcements::insert($annoucement_arr);

        return response()->json(['status' => true, 'msg' => "Announcements successfully added.", 'data' => []]);
    }

    public function edit_announcement_details(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'annoucement_id' => 'required',
                    'title' => 'required',
                    'description' => 'required',
                    'start_date' => 'required',
                    'end_date' => 'required',
                    'announcement_user_id'  =>'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();
        $response_data = [];

        //check role of user is of driver
        $logged_in_user = \App\User::where('id', $request_data['user_id'])->get();

        $annoucement_arr = [
            'title'       => $request_data['title'],
            'description' => $request_data['description'],
            'start_date'  => $request_data['start_date'],
            'end_date'    => $request_data['end_date'],
            'user_id'     => $request_data['announcement_user_id'],
            'status'      => 'Enabled',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'created_ip' => $request->ip(),
            'updated_ip' => $request->ip(),
            'updated_by' => $request_data['user_id'],
        ];
        if(!empty(Announcements::where('id',$request_data['annoucement_id'])->get()->toArray()))
        {
            Announcements::where('id',$request_data['annoucement_id'])->update($annoucement_arr);    
            return response()->json(['status' => true, 'msg' => "Announcements successfully updated.", 'data' => []]);
        }
        else
        {
            return response()->json(['status' => true, 'msg' => "Announcements not exit.", 'data' => []]);
        }
    }

    public function delete_announcement_details(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    'annoucement_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data  = $request->all();
        $response_data = [];
        
        if(Announcements::where(['id' => $request_data['annoucement_id']])->delete())
        {
            return response()->json(['status' => true, 'msg' => "Announcements deleted successfully.", 'data' => []]);    
        }
        else
        {
            return response()->json(['status' => true, 'msg' => "Announcements not exits.", 'data' => []]);       
        }
    }

    public function announcement_users_list(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
                    
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }
        $request_data       = $request->all();
        $response_data      = [];
        $announcement_list  = \App\User::where('status','Enabled')
                                ->orderBy('created_at','DESC')
                                ->get()
                                ->toArray();
        foreach ($announcement_list as $key => $announcement) {
            
            $announcement_records[$key]['user_id']   = $announcement['id'];
            $announcement_records[$key]['user_name'] = $announcement['name'];

            if ($announcement['profile_image']) {
                $announcement_records[$key]['user_profile_image'] = asset('storage/' . str_replace('public/', '', $announcement['profile_image']));
            } else {
                $announcement_records[$key]['user_profile_image'] = "";
            }

        }

        $response_data['announcement_user_list'] = $announcement_records;

        return response()->json(['status' => true, 'msg' => "Record Found.", 'data' => $response_data]);
    }
}
