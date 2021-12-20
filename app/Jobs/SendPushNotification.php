<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Lib\Push_notification;

class SendPushNotification implements ShouldQueue {

    use Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels;

    private $details;
    private $user_ids;
    public $tries = 2;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($details, $user_ids) {
        $this->details = $details;
        $this->user_ids = $user_ids;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        //get user's device from login log
        $android_notify_ids=[];$ios_notify_ids=[];
        foreach ($this->user_ids as $user_id) {
            $user_logs = \App\Login_log::where('user_id',$user_id)->get(['device_id','device_type']);
            if($user_logs->count()==0){
                continue;
            }
            foreach($user_logs as $user){
                if($user->device_type=='Android'){
                    $android_notify_ids[]=$user->device_id;
                }
                else{
                    $ios_notify_ids[]=$user->device_id;
                }
            }
        }
        $android_notify_data=[
            'message'=>$this->details['message'],
            'topic'=> $this->details['tag'],
            'title'=> $this->details['title']
        ];
        
        $ios_notify_data=[
            'message'=>$this->details['message'],
            'topic'=> $this->details['tag'],
            'title'=> $this->details['title']
        ];
        $pushnotificationObj=new Push_notification();
        $pushnotificationObj->send_andorid_notification($android_notify_ids, $android_notify_data);
        $pushnotificationObj->send_ios_notification($ios_notify_ids, $ios_notify_data);
        
    }

}
