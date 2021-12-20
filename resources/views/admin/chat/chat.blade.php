@extends('layouts.admin_app')

@section('content')

<style>
    #chat_body{
        overflow: auto !important;
    }
    .emoji-menu{
        position: unset !important;
        float:right;
        margin-bottom: 58px;
    margin-top: -55px;
    }
    .emoji-menu-tabs .icon-grid{
        color: transparent !important;
    }
    .emoji-menu-tabs .icon-bell{
        color: transparent !important;
    }
    .chat-list .chat-text h4{
        font-size:10px !important;
    }
    .chat-list .chat-text p{
        font-size: 20px !important;
    }
    i.fa.fa-file.fa-6{
        font-size: 100px;
    }
    .chat-box > .slimScrollDiv{
        overflow-y: hidden !important;
    }
</style>
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">{{ $page_title }}</h4>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">

            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route($module_link) }}">{{ $module_title }}</a></li>
                <li><a href="#">{{ $page_title }}</a></li>
            </ol>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-md-12">
            <div class="white-box">

                <div class="row">
                    <div class="col-md-12">
                        @if (session('error'))
                        <div class="alert alert-danger alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            {{ session('error') }}
                        </div>
                        @endif
                        @if (session('success'))
                        <div class="alert alert-success alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            {{ session('success') }}
                        </div>
                        @endif
                        <!--Chat design start-->
                        <div class="chat-main-box">

                            <!-- .chat-left-panel -->
                            <div class="chat-left-aside">
                                <div class="open-panel"><i class="ti-angle-right"></i></div>
                                <div class="chat-left-inner">

                                    <div class="form-material"><input class="form-control p-20" id="search_contact" type="text" placeholder="Search Contact"></div>
                                    <ul class="chatonline style-none " id="contact_list">
                                        
                                    </ul>
                                </div>  
                            </div>  
                            <!-- .chat-left-panel -->
                            <!-- .chat-right-panel -->
                            <div class="chat-right-aside">
                                <div class="chat-main-header">
                                    <div class="p-20 b-b">
                                        <h3 class="box-title" id="chat_title">Chat Message</h3>
                                    </div>
                                </div> 
                                <div id="meet">
                                    
                                </div>
                                <div class="chat-box">
                                    <ul id="chat_body" class="chat-list slimscroll p-t-30 chat_body">
                                        

                                    </ul>
                                    <div class="row send-chat-box">
                                        <div class="col-sm-12">
                                            <textarea class="form-control" data-emoji-input="unicode" data-emojiable="true" id="send_msg_body" placeholder="Type your message"></textarea>
                                            <div class="custom-send">
                                                <button style="display:none;" id="clear_file_btn" class="btn btn-danger">Clear File</button>
                                                <button onclick="file_browse();" class="cst-icon" data-toggle="tooltip" title="File Attachment"><i class="fa fa-paperclip"></i></button> 
                                                <button onclick="sendMessage()" class="btn btn-danger btn-rounded" type="button">Send</button></div>

                                        </div>
                                    </div>
                                </div>    
                            </div>
                            <!-- .chat-right-panel -->
                        </div> 
                        <!--Chat design end-->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="file" style="display: none;" id="file" name="file" />
</div>
<input type="hidden" id="active_chat_jid" value="" />
<input type="hidden" id="active_user_name" value="" />
<input type="hidden" id="target_profile_img" value="" />
<input type="hidden" id="page_counter" value="0" />

<div class="modal" id="chat_img_modal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Image</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
          <p><img id="chat_img_src" src="" class="img-responsive" /></p>
      </div>
      <div class="modal-footer">
        
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endsection


@section('script')
 <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet">
  <link href="{{asset('admin_asset/assets/emoji/lib/css/emoji.css') }}" rel="stylesheet">

  
<script src="{{asset('admin_asset/assets/js/chat.js') }}"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/strophe.js/1.2.16/strophe.js"></script>
<script src="https://cdn.jsdelivr.net/npm/strophejs-plugin-muc@1.1.0/lib/strophe.muc.min.js"></script>

<!--  <script src='https://cdn.jsdelivr.net/npm/strophe-plugins@0.1.3/si-filetransfer.js'></script>
  <script src='https://cdn.jsdelivr.net/npm/strophe-plugins@0.1.3/ibb.js'></script>-->

 <script src="{{asset('admin_asset/assets/emoji/lib/js/config.js') }}"></script>
  <script src="{{asset('admin_asset/assets/emoji/lib/js/util.js') }}"></script>
  <script src="{{asset('admin_asset/assets/emoji/lib/js/jquery.emojiarea.js') }}"></script>
  <script src="{{asset('admin_asset/assets/emoji/lib/js/emoji-picker.js') }}"></script>
  <script>
      
      $(function() {
        // Initializes and creates emoji set from sprite sheet
        window.emojiPicker = new EmojiPicker({
          emojiable_selector: '[data-emojiable=true]',
          assetsPath: '{{asset("admin_asset/assets/emoji/lib/img/") }}',
          popupButtonClasses: 'fa fa-smile-o'
        });
        // Finds all elements with `emojiable_selector` and converts them to rich emoji input fields
        // You may want to delay this step if you have dynamically created input fields that appear later in the loading process
        // It can be called as many times as necessary; previously converted input fields will not be converted again
        window.emojiPicker.discover();
        
        
      });
    </script>
<script>
var s3_link="{{ $s3_link }}";
var file_upload_url="{{ route('admin.chat_file_upload') }}"
var add_value = {{config('constants.CHAT_USER_ADD')}};
var get_contact_list_url = "{{ route('admin.get_contact_list') }}";
var get_messages_url = "{{ route('admin.get_chat_msg') }}";
var csrf_token = "{{ csrf_token() }}";
var page_counter = 1;
var server = "{{config('constants.CHAT_SERVER')}}";
var port="{{config('constants.CHAT_PORT')}}";
var BOSH_SERVICE = 'http://'+server+':'+port+'/http-bind/';
var username = {{config('constants.CHAT_USER_ADD') + Auth::user()-> id}};
var jid = username + "@" + server;
var password = username + "@" + {{Auth::user()-> id}}
var connection = null;
var loggedin_user_profile="{{ $loggedin_profile_image }}";
var loggedin_username="{{ Auth::user()->name }}";
</script>
<script src="{{asset('admin_asset/js/chat_activity.js') }}"></script>
<!--<script src="https://meet.jit.si/libs/lib-jitsi-meet.min.js"></script>-->
<!--<script src='https://meet.jit.si/external_api.js'></script>-->

<script>

var url = BOSH_SERVICE;
connection = new Strophe.Connection(url);
connection.rawInput = rawInput;
connection.rawOutput = rawOutput;
connection.connect(jid, password, onConnect);

</script>

@endsection