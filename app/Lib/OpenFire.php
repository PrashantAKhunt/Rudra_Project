<?php

namespace App\Lib;

use Illuminate\Support\Facades\Config;
use App\Email_format;
use App\Mail\Mails;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use DateTime;
use Gnello\OpenFireRestAPI\Client;
use App\User;
use Exception;
/**
 * Description of OpenFire
 *
 * @author kishan
 */
class OpenFire
{
     private $super_admin;
     private $hrMail;
     private $client;
     private $add_value;
     private $domain;
     public function __construct()
     {
          $this->super_admin = \App\User::where('role', 1)->first();
          $this->hrMail = config::get('app.HR_EMAIL');
          $this->client = new Client([
               'client' => [
                    'secretKey' => config::get('constants.CHAT_KEY'),
                    'scheme' => 'http',
                    //'basePath' => '/plugins/restapi/v1/',
                    'host' => config::get('constants.CHAT_SERVER'),
                    'port' => config::get('constants.CHAT_REST_PORT'),
               ],

          ]);
          $this->add_value= Config::get('constants.CHAT_USER_ADD');
          $this->domain=config::get('constants.CHAT_SERVER');
     }

     public function check_create_openfire_user($user_id, $name)
     {
          try {
               
               //check if user is already exists
               $response = $this->client->getUserModel()->retrieveUser($this->add_value + $user_id);
               
               if($response->getStatusCode()==404){
                   
                    $response = $this->client->getUserModel()->createUser([
                         "username" => $this->add_value + $user_id,
                         "name" => $name,
                         "password" => $this->add_value + $user_id . '@' . $user_id,
     
                    ]);
                    if ($response->getStatusCode() == 201) {
                         $created_username=$this->add_value + $user_id;
                         //get all users
                        
                         $user_list_obj=$this->client->getUserModel()->retrieveUsers();
                         $user_list=json_decode($user_list_obj->getBody());
                         foreach($user_list->users as $user){
                              if($user->username==$created_username || $user->username=='admin'){
                                   continue;
                              }
                              //create roster
                              $this->client->getUserModel()->createUserRosterEntry($created_username,['jid'=>$user->username.'@'.$this->domain,'subscriptionType'=>3]);
                              
                              $this->client->getUserModel()->createUserRosterEntry($user->username,['jid'=>$created_username.'@'.$this->domain,'subscriptionType'=>3]);
                              
                         }
                         
                         
                         return true;
                    } else {
                         return false;
                    }
               }
               else{
                    return true;
               }
               
          } catch (\Throwable $tr) {
               return false;
          }
     }

     public function retrive_user_roster($username){
          $response=$this->client->getUserModel()->retrieveUserRoster($username);
          if($response->getStatusCode()==200){
               return json_decode($response->getBody());
          }
          else{
               return [];
          }
     }
     
     public function create_chat_room(){
         $chatRoomArr=[
             'roomName'=>"TEST2",
             'naturalName'=>"test2",
             'description'=>'testtest',
             
         ];
         $response=$this->client->getChatRoomModel()->createChatRoom($chatRoomArr);
         print_r($response); die();
         
     }
}
