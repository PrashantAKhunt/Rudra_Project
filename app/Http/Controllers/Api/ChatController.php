<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Lib\OpenFire;
use App\User;
use App\OfMessageArchive;
use App\OfOffline;
use Illuminate\Support\Facades\Validator;
use App\Lib\Upload_file;

class ChatController extends Controller {

    public $data;
    private $openfire_obj;
    private $server;
    private $s3_link = "https://rtplhrms.sfo2.digitaloceanspaces.com/";
    private $limit = 20;

    public function __construct() {
        $this->data['module_link'] = 'admin.chat';
        $this->data['module_title'] = "Chat";
        $this->openfire_obj = new OpenFire;
        $this->server = config('constants.CHAT_SERVER');
    }

    public function get_contact_list(Request $request) {

        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();

        $username = config('constants.CHAT_USER_ADD') + $request_data['user_id'];
        $user_roster = $this->openfire_obj->retrive_user_roster($username);
        $html = "";
        $logged_in_user_jid = $username . '@' . $this->server;

        $contact_list = [];
        foreach ($user_roster->rosterItem as $key => $user) {
            $contact_list[$key]['jid'] = $user->jid;

            $jid = str_replace('@' . $this->server, "", $user->jid);
            $user_id = (int) $jid - config('constants.CHAT_USER_ADD');
            $user_detail = User::where('id', $user_id)->get();
            $stanza = 'from="' . $user->jid . '/';

            $offline_message_count = OfOffline::where('username', $username)->where('stanza', 'like', '%' . $stanza . '%')->get()->count();
            $contact_list[$key]['msg_count'] = $offline_message_count;
            $contact_list[$key]['name'] = $user_detail[0]->name;

            if ($user_detail[0]->profile_image) {
                $contact_list[$key]['profile_image'] = asset('storage/' . str_replace('public/', '', $user_detail[0]->profile_image));
            } else {
                $contact_list[$key]['profile_image'] = asset('admin_asset/assets/plugins/images/user_avatar.png');
            }
        }

        if (count($contact_list) > 0) {
            return response()->json(['status' => true, 'msg' => 'Record found', 'data' => $contact_list]);
        }
        return response()->json(['status' => false, 'msg' => config('errors.no_record.msg'), 'data' => [], 'error' => config('errors.no_record.code')]);
    }

    public function get_chat_msg(Request $request) {


        $user_details = User::where('id', $request->input('user_id'))->get();

        $target_jid = $request->input('target_jid');
        $logged_in_username = config('constants.CHAT_USER_ADD') + $request->input('user_id');
        $self_jid = $logged_in_username . '@' . $this->server;

        $page = $request->input('page');

        $jid = str_replace('@' . $this->server, "", $target_jid);
        $user_id = (int) $jid - config('constants.CHAT_USER_ADD');
        $user_detail = User::where('id', $user_id)->get();
        $target_name = $user_detail[0]->name;
        if ($user_detail[0]->profile_image) {
            $target_profile_image = asset('storage/' . str_replace('public/', '', $user_detail[0]->profile_image));
        } else {
            $target_profile_image = asset('admin_asset/assets/plugins/images/user_avatar.png');
        }

        if ($user_details[0]->profile_image) {
            $loggedin_profile_image = asset('storage/' . str_replace('public/', '', $user_details[0]->profile_image));
        } else {
            $loggedin_profile_image = asset('admin_asset/assets/plugins/images/user_avatar.png');
        }
        $offset = ($page - 1) * $this->limit;

        $chat_data = OfMessageArchive::where(function($query) use($self_jid, $target_jid) {
                            $query->where('fromJID', $self_jid)->where('toJID', $target_jid);
                        })
                        ->orWhere(function($query) use($self_jid, $target_jid) {
                            $query->where('fromJID', $target_jid)->where('toJID', $self_jid);
                        })
                        ->limit($this->limit)
                        ->offset($offset)
                        ->orderBy('sentDate', 'DESC')
                        ->get()->toArray();



        if (count($chat_data) > 0) {

            usort($chat_data, function($a, $b) {
                return $a['sentDate'] - $b['sentDate'];
            });

            foreach ($chat_data as $key => $chat) {
                $chat_data[$key]['sentDate'] = date('d/m/Y h:i a', ($chat['sentDate'] / 1000));
                if ($chat['fromJID'] == $self_jid) {
                    $chat_data[$key]['profile_image'] = $loggedin_profile_image;
                    $chat_data[$key]['name'] = $user_details[0]->name;
                } else {
                    $chat_data[$key]['profile_image'] = $target_profile_image;
                    $chat_data[$key]['name'] = $target_name;
                }
            }
            //$chat_data=$this->convert_from_latin1_to_utf8_recursively($chat_data);
            $data['chat_data'] = $chat_data;
            $data['logged_in_user_details']['target_profile_img'] = $target_profile_image;
            $data['logged_in_user_details']['target_user_name'] = $target_name;
            return response()->json(['status' => true, 'data' => $data, 'msg' => 'Record found']);
        } else {
            return response()->json(['status' => false, 'data' => [], 'msg' => 'Record found']);
        }
    }

    public function getUnreadChatCount(Request $request) {
        $validator = Validator::make($request->all(), [
                    'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'msg' => config('errors.validation.msg'), 'data' => [], 'error' => config('errors.validation.code')]);
        }

        $request_data = $request->all();

        $username = config('constants.CHAT_USER_ADD') + $request_data['user_id'];
        
        $offline_message_count= OfOffline::where('username',$username)->get()->count();
        return response()->json(['status' => true, 'msg' => "record found", 'data' => ['offline_message_count' => $offline_message_count]]);
    }

    public function chat_file_upload(Request $request) {

        if ($request->hasFile('file')) {

            $result = Upload_file::upload_chat_file($request);
            if ($result['status']) {
                $file_name = $result['storage_path'];
                return response()->json(['status' => true, 'msg' => "File Uploaded", 'data' => ['file_name' => $file_name]]);
            } else {
                return response()->json(['status' => false, 'msg' => "Error in file upload"]);
            }
        }
    }

    public function convert_from_latin1_to_utf8_recursively($dat) {
        if (is_string($dat)) {
            return utf8_encode($dat);
        } elseif (is_array($dat)) {
            $ret = [];
            foreach ($dat as $i => $d)
                $ret[$i] = self::convert_from_latin1_to_utf8_recursively($d);

            return $ret;
        } elseif (is_object($dat)) {
            foreach ($dat as $i => $d)
                $dat->$i = self::convert_from_latin1_to_utf8_recursively($d);

            return $dat;
        } else {
            return $dat;
        }
    }

}
