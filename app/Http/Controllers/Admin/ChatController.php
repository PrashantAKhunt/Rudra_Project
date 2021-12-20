<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Lib\OpenFire;
use App\User;
use Illuminate\Support\Facades\Auth;
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

    public function chat() {
        $this->data['page_title'] = "Chat";
        $this->data['s3_link'] = $this->s3_link;
        if (Auth::user()->profile_image) {
            $this->data['loggedin_profile_image'] = asset('storage/' . str_replace('public/', '', Auth::user()->profile_image));
        } else {
            $this->data['loggedin_profile_image'] = asset('admin_asset/assets/plugins/images/user_avatar.png');
        }
        return view('admin.chat.chat', $this->data);
    }

    public function like_match($pattern, $subject) {
        $pattern = str_replace('%', '.*', preg_quote($pattern, '/'));
        return (bool) preg_match("/^{$pattern}$/i", $subject);
    }

    public function get_contact_list($keyword = "") {
        $username = config('constants.CHAT_USER_ADD') + Auth::user()->id;
        $user_roster = $this->openfire_obj->retrive_user_roster($username);
        $html = "";
        $logged_in_user_jid = $username . '@' . $this->server;


        foreach ($user_roster->rosterItem as $user) {
            $jid = str_replace('@' . $this->server, "", $user->jid);
            $user_id = (int) $jid - config('constants.CHAT_USER_ADD');
            $user_detail = User::where('id', $user_id)->get();

            if ($keyword != "" && !$this->like_match('%' . $keyword . '%', $user_detail[0]->name)) {
                continue;
            }


            $stanza = 'from="' . $user->jid . '/';


            $offline_message_count = OfOffline::where('username', $username)->where('stanza', 'like', '%' . $stanza . '%')->get()->count();
            if ($offline_message_count == 0) {
                $style = "display:none;";
            } else {
                $style = "display:block;";
            }
            if ($user_detail[0]->profile_image) {
                $html .= '<li id="contact_' . $jid . '" onclick="get_message_list(&apos;' . $user->jid . '&apos;,&apos;User&apos;,1)"><a href="javascript:void(0)"><img style="width:50px; height:50px;" src="' . asset('storage/' . str_replace('public/', '', $user_detail[0]->profile_image)) . '" alt="user-img" class="img-circle"> <span>' . $user_detail[0]->name . '<span class="label label-rouded label-custom pull-right" style="' . $style . '" id="count_' . $jid . '">' . $offline_message_count . '</span> <small id="presence_' . $jid . '" class="text-danger">offline</small></span></a></li>';
            } else {
                $html .= '<li id="contact_' . $jid . '" onclick="get_message_list(&apos;' . $user->jid . '&apos;,&apos;User&apos;,1)"><a href="javascript:void(0)"><img style="width:50px; height:50px;" src="' . asset('admin_asset/assets/plugins/images/user_avatar.png') . '" alt="user-img" class="img-circle"> <span>' . $user_detail[0]->name . '<span class="label label-rouded label-custom pull-right" style="' . $style . '" id="count_' . $jid . '">' . $offline_message_count . '</span> <small class="text-danger" id="presence_' . $jid . '">offline</small></span></a></li>';
            }
        }
        echo $html;
        die();
    }

    public function get_chat_msg(Request $request) {
        $target_jid = $request->input('target_jid');
        $logged_in_username = config('constants.CHAT_USER_ADD') + Auth::user()->id;
        $self_jid = $logged_in_username . '@' . $this->server;
        $target_type = $request->input('target_type');
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

        if (Auth::user()->profile_image) {
            $loggedin_profile_image = asset('storage/' . str_replace('public/', '', Auth::user()->profile_image));
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
                    $chat_data[$key]['name'] = Auth::user()->name;
                } else {
                    $chat_data[$key]['profile_image'] = $target_profile_image;
                    $chat_data[$key]['name'] = $target_name;
                }
            }

            //$chat_data = $this->convert_from_latin1_to_utf8_recursively($chat_data);
            return response()->json(['status' => true, 'data' => $chat_data, 'target_profile_img' => $target_profile_image, 'target_user_name' => $target_name]);
        } else {
            return response()->json(['status' => false, 'data' => [], 'target_profile_img' => $target_profile_image, 'target_user_name' => $target_name]);
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

    public function chat_file_upload(Request $request) {

        if ($request->hasFile('file')) {

            $result = Upload_file::upload_chat_file($request);
            if ($result['status']) {
                $file_name = $result['storage_path'];
                return response()->json(['status' => true, 'file_name' => $file_name]);
            } else {
                return response()->json(['status' => false]);
            }
        }
    }

    public function createChatRoom() {
        
    }

}
