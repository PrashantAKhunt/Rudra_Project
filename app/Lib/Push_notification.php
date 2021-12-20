<?php

namespace App\Lib;
use Exception;
class Push_notification {

    public $fcm_link;
    public $fcm_server_key;

    public function __construct() {
        $this->fcm_link = "https://fcm.googleapis.com/fcm/send";
        $this->fcm_server_key = "AAAAQyltEsQ:APA91bFeyOhQqxUDoAGMIuRWomXzCImBUz_0xVHcmR-EIVBz95apZKkOqNuGJAw7iXK-DtSjk4hb15gU6iWb0yUPoaeHcXAMX4ir0ZVCftpbJpxPViND064F_ZxAZlUqi7ndxU1va4cd";
    }

    public function send_andorid_notification($fcm_register_ids, $message_data) {
        \App\Test::insert(['test_type' => $message_data['message']]);

        
        $extraNotificationData =array(
            'title' => $message_data['title'],
            'body' => $message_data['message'],
            "topic" => $message_data['topic'],
            
        );

        $fcmNotification = [
            'registration_ids' => $fcm_register_ids, //multple token array
            //'to' => $fcm_token, //single token
            'notification' => $extraNotificationData,
            'data' => $extraNotificationData,
        ];

        $headers = [
            'Authorization: key=' . $this->fcm_server_key,
            'Content-Type: application/json'
        ];
\App\Test::insert(['test_type' => 'kishan1']);
//echo json_encode($fcmNotification); 
        try {
            $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->fcm_link);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
        $result = curl_exec($ch);
        curl_close($ch);
        } catch (Exception $exc) {
            //echo $exc->getTraceAsString();
            \App\Test::insert(['test_type' => $exc->getTraceAsString()]);
        }

        
\App\Test::insert(['test_type' => 'kishan']);
        return true;
    }

    public function send_ios_notification($fcm_register_ids, $message_data) {
        \App\Test::insert(['test_type' => 'ios Notification']);
        $extraNotificationData =array(
            'body' => $message_data['message'],
            'title' => $message_data['title'],
            'vibrate' => 1,
            'sound' => 1,
        );
//$extraNotificationData=$message_data;
        $fcmNotification = [
            'registration_ids' => $fcm_register_ids, //multple token array
            //'to' => $fcm_token, //single token
            'notification' => $extraNotificationData,
            'data' => $message_data,
        ];

        $headers = [
            'Authorization: key=' . $this->fcm_server_key,
            'Content-Type: application/json'
        ];

//echo json_encode($fcmNotification); 
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->fcm_link);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
        $result = curl_exec($ch);
        curl_close($ch);
      
        return true;
    }

}
