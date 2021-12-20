@extends('layouts.admin_app')

@section('content')
<style>
    .rmv-mrg-pedding{
        margin-left:0px !important;
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
                <li><a href="{{ route('admin.inward_outward') }}">Inward Outward</a></li>
                <li><a href="#">{{ $page_title }}</a></li>
            </ol>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <!-- .row -->
    <div class="row">
        <div class="col-md-12">
            <div class="white-box">


                <!-- .chat-row -->
                <div class="chat-main-box">

                    <!-- .chat-left-panel -->

                    <!-- .chat-left-panel -->

                    <!-- .chat-right-panel -->
                    <div class="chat-right-aside rmv-mrg-pedding">
                        <div class="chat-main-header">
                            <div class="p-20 b-b ">
                                <h3 class="box-title">{{ $registry_title}}</h3>
                            </div>
                        </div>

                        <div class="chat-box" >
                            <ul id="msg_body" class="chat-list slimscroll p-t-30" >

                                @foreach($messages as $key=> $message)


                                @if($message->message_type === 'Document')
                                <li @if($message->from_user_id === Auth::user()->id) class="odd" @endif>
                                     <div class="chat-image"> 
                                        @if($message->profile_image)
                                        <img src="{{ asset('storage/'.str_replace('public/','',$message->profile_image)) }}" alt="user-img" >
                                        @else
                                        <img src="{{ asset('admin_asset/assets/plugins/images/user_avatar.png') }}" alt="user-img" >
                                        @endif

                                    </div>
                                    <div class="chat-body">
                                        <div class="chat-text" @if($message->from_user_id === Auth::user()->id) style="background:#6CBDF5" @endif>
                                             <h4 >{{ $message->name }}</h4>
                                            <a title="Open File" href="{{ asset('storage/'.str_replace('public/','',!empty($message->document_name) ? $message->document_name : 'public/no_image')) }}" target="_blank">
                                                <img class="img-responsive" src="{{ asset('admin_asset/assets/plugins/images/pdf_icon.png') }}" /></a>
                                            <p><b>{{ $message->created_at->format('d-m-Y h:i A') }}</b></p>
                                        </div>
                                    </div>
                                </li>
                                @else
                                <li @if($message->from_user_id === Auth::user()->id) class="odd" @endif >
                                     <div class="chat-image"> 
                                        @if($message->profile_image)
                                        <img src="{{ asset('storage/'.str_replace('public/','',$message->profile_image)) }}" alt="user-img" >
                                        @else
                                        <img src="{{ asset('admin_asset/assets/plugins/images/user_avatar.png') }}" alt="user-img" >
                                        @endif

                                    </div>
                                    <div class="chat-body" >
                                        <div class="chat-text" @if($message->from_user_id === Auth::user()->id) style="background:#6CBDF5" @endif>
                                             <h4>{{ $message->name }}</h4>
                                            <p> {{ $message->message }} </p>
                                            <b>{{ $message->created_at->format('d-m-Y h:i A') }}</b>
                                        </div>
                                    </div>
                                </li>
                                @endif

                                @endforeach

                            </ul>

                            <div class="row send-chat-box">
                                <div class="col-sm-12">
                                    <input type="hidden" id="inward_outward_id" name="inward_outward_id" value="{{ $inward_outward_id }}" />

                                    <textarea class="form-control" autofocus  id="message" placeholder="Type your message"></textarea>

                                    <div class="custom-send " style="float:right;"> 
                                        <button class="btn btn-danger btn-rounded" id="sendMessage" type="button">Send</button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- .chat-right-panel -->
                </div>
                <!-- /.chat-row -->

            </div>
        </div>
    </div>
    <!-- /.row -->


</div>
@endsection


@section('script')
<script src="{{asset('admin_asset/assets/js/chat.js') }}"></script>


<script>
$("#msg_body").stop().animate({
    scrollTop: $("#msg_body")[0].scrollHeight
}, 1000);


</script>
<script>
    $(document).ready(function () {
        $('#sendMessage').click(function (e) {
            e.preventDefault();

            var registry_id = $('#inward_outward_id').val();
            var message = $('#message').val();


            if (message != "") {

                $.ajax({
                    url: "{{ route('admin.send_message') }}",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        message: message,
                        inward_outward_id: registry_id

                    },
                    cache: false,
                    success: function (dataResult) {
                        //console.log(dataResult);
                        var dataResult = JSON.parse(dataResult);
                        if (dataResult.statusCode == 200) {
                            $('#msg_body').append(dataResult.html_body);
                            $("#message").val('');


                            $("#msg_body").stop().animate({
                                scrollTop: $("#msg_body")[0].scrollHeight
                            }, 1000);
                        } else if (dataResult.statusCode == 201) {
                            alert("Error occured !");
                        }

                    }
                });
            } else {
                alert('Please write message.');
            }

        });
    });
</script>

@endsection