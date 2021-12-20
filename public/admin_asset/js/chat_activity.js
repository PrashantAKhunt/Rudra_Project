function log(msg) {
    $('#log').append('<div></div>').append(document.createTextNode(msg));
    console.log(msg);
}
var lastScrollTop = 0;

document.getElementById("chat_body").addEventListener("wheel", load_data_on_scroll);
$('#chat_body').on('scroll', function () {

    load_data_on_scroll();
});

function load_data_on_scroll() {
    if ($(this).scrollTop() < lastScrollTop) {
        if ($(this).scrollTop() < 100) {
            if ($('#page_counter').val() > 0) {
                get_message_list($('#active_chat_jid').val(), 'User', 0);
            }
            $('#page_counter').val(parseInt($('#page_counter').val()) + 1);
        }
    }
    lastScrollTop = $(this).scrollTop();
}

function scroll_down() {
    /*var wtf = $('#chat_body');
     var height = wtf[0].scrollHeight;
     wtf.scrollTop(height);*/
    var div = document.getElementById('chat_body');
    $('#chat_body').animate({
        scrollTop: div.scrollHeight - div.clientHeight
    }, 500);
}

function get_contact_list() {

    $('#contact_list').html();
    $.ajax({
        url: get_contact_list_url,
        type: "GET",
        dataType: "HTML",
        success: function (data) {
            $('#contact_list').html(data);

            setTimeout(function () {

                $('#contact_list li:first-child').click();
            }, 1000);

        }
    })
}

function get_message_list(target_jid, target_type, new_chat) {
    if (new_chat) {
        $('#chat_body').html('');
    }
    $('#active_chat_jid').val(target_jid);
    //$('#contact_list').html();
    var target_username_arr = target_jid.split("@");
    $('#count_' + target_username_arr[0]).text(0);
    $('#count_' + target_username_arr[0]).hide();
    if ($('#page_counter').val() == 0) {
        var page = 1;
    } else {
        var page = $('#page_counter').val();
    }
    $.ajax({
        url: get_messages_url,
        data: {
            "_token": csrf_token,
            "target_jid": target_jid,
            "target_type": target_type,
            "page": page
        },
        type: "POST",
        dataType: "JSON",
        success: function (data) {
            var html_append = "";
            $('#target_profile_img').val(data.target_profile_img);
            $('#chat_title').text(data.target_user_name);
            $('#active_user_name').val(data.target_user_name);
            if (data.status) {
                $('#page_counter').val(parseInt($('#page_counter').val()) + 1);

                for (let i = 0; i < data.data.length; i++) {
                    var message_detail = data.data[i];
                    var msg = message_detail.body.replace(/(?:\r\n|\r|\n)/g, '<br>');
                    var msg_split = msg.split("/");

                    if (msg_split[0] && msg_split[0] == "chatWappnet") {
                        var extension = msg.split('.').pop();
                        if (extension.toLowerCase() == "jpg" || extension.toLowerCase() == "jpeg" || extension.toLowerCase() == "png") {
                            msg = '<img onclick="set_chat_img(&apos;' + s3_link + msg + '&apos;)" data-toggle="modal" data-target="#chat_img_modal" src="' + s3_link + msg + '" width="100px" height="100px" />';
                        } else {
                            msg = '<a href="' + s3_link + msg + '" target="_blank"><i class="fa fa-file fa-6"></i></a>';
                        }

                    }

                    if (jid == message_detail.fromJID) {
                        html_append += '<li class="odd">' +
                                '<div class="chat-image"> <img alt="" src="' + message_detail.profile_image + '"> </div>' +
                                '<div class="chat-body">' +
                                '<div class="chat-text">' +
                                '<h4>' + message_detail.name + '</h4>' +
                                '<p> ' + msg + ' </p>' +
                                '<b>' + message_detail.sentDate + '</b> </div>' +
                                ' </div>' +
                                ' </li>';
                    } else {
                        html_append += '<li>' +
                                '<div class="chat-image"> <img alt="" src="' + message_detail.profile_image + '"> </div>' +
                                '<div class="chat-body">' +
                                '<div class="chat-text">' +
                                '<h4>' + message_detail.name + '</h4>' +
                                '<p> ' + msg + ' </p>' +
                                '<b>' + message_detail.sentDate + '</b> </div>' +
                                ' </div>' +
                                ' </li>';
                    }
                }
                //var old_html=$('#chat_body').html();
                //$('#chat_body').html('');
                $('#chat_body').prepend(html_append);
                if (new_chat) {
                    scroll_down();
                }

            }
        }
    })
}

function onConnect(status) {
    if (status == Strophe.Status.CONNECTING) {
        log('Strophe is connecting.');
    } else if (status == Strophe.Status.CONNFAIL) {
        log('Strophe failed to connect.');

    } else if (status == Strophe.Status.DISCONNECTING) {
        log('Strophe is disconnecting.');
    } else if (status == Strophe.Status.DISCONNECTED) {
        log('Strophe is disconnected.');

    } else if (status == Strophe.Status.CONNECTED) {
        log('Strophe is connected.');

        // set presence
        connection.send($pres());
        // set handlers
        connection.addHandler(onMessage, null, 'message', null, null, null);
        connection.addHandler(onSubscriptionRequest, null, "presence", "subscribe");
        connection.addHandler(onPresence, null, "presence");

        //connection.si_filetransfer.addFileHandler(fileHandler);
        //connection.ibb.addIBBHandler(ibbHandler);
        //getRoster();
        //listRooms();
        get_contact_list();
        //getPresence("1001@localhost");
    }
}

function onMessage(msg) {

    var to = msg.getAttribute('to');
    var from = msg.getAttribute('from');
    var type = msg.getAttribute('type');
    var elems = msg.getElementsByTagName('body');


    if (type == "chat" && elems.length > 0) {
        var body = elems[0];
        log('CHAT: I got a message from ' + from + ': ' + Strophe.getText(body));
        var from_username_arr = from.split('/');
        var from_username_arr2 = from_username_arr[0].split('@');
        var badge_count = parseInt($('#count_' + from_username_arr2[0]).text());

        if (from_username_arr[0] != $('#active_chat_jid').val()) {
            
            $('#count_' + from_username_arr2[0]).show();
            $('#count_' + from_username_arr2[0]).text(badge_count + 1);
            var contact_html=$('#contact_'+from_username_arr2[0]).clone();
            $('#contact_'+from_username_arr2[0]).remove();
            $('#contact_list').prepend(contact_html);
            return true;
        } else {
            $('#count_' + from_username_arr2[0]).text(0);
            $('#count_' + from_username_arr2[0]).hide();

        }
        var html_append = "";
        var msg = Strophe.getText(body);
        var msg_split = msg.split("/");
        if (msg_split[0] && msg_split[0] == "chatWappnet") {
            var extension = msg.split('.').pop();
            if (extension.toLowerCase() == "jpg" || extension.toLowerCase() == "jpeg" || extension.toLowerCase() == "png") {
                msg = '<img onclick="set_chat_img(&apos;' + s3_link + msg + '&apos;)" data-toggle="modal" data-target="#chat_img_modal" src="' + s3_link + msg + '" width="100px" height="100px" />';
            } else {
                msg = '<a href="' + s3_link + msg + '" target="_blank"><i class="fa fa-file fa-6"></i></a>';
            }
            html_append += '<li>' + '<div class="chat-image"> <img alt="" src="' + ($('#target_profile_img').val()) + '"> </div>' +
                    '<div class="chat-body">' +
                    '<div class="chat-text">' +
                    '<h4>' + ($('#active_user_name').val()) + '</h4>' +
                    '<p> ' + msg + ' </p>' +
                    '<b>' + (new Date().toLocaleDateString()) + ' ' + (new Date().toLocaleString('en-GB', {hour: 'numeric', minute: 'numeric', hour12: true})) + '</b> </div>' +
                    ' </div>' +
                    ' </li>';
        } else {
            html_append += '<li>' + '<div class="chat-image"> <img alt="" src="' + ($('#target_profile_img').val()) + '"> </div>' +
                    '<div class="chat-body">' +
                    '<div class="chat-text">' +
                    '<h4>' + ($('#active_user_name').val()) + '</h4>' +
                    '<p> ' + Strophe.getText(body) + ' </p>' +
                    '<b>' + (new Date().toLocaleDateString()) + ' ' + (new Date().toLocaleString('en-GB', {hour: 'numeric', minute: 'numeric', hour12: true})) + '</b> </div>' +
                    ' </div>' +
                    ' </li>';
        }
        $('#chat_body').append(html_append);
        scroll_down();
    }
    // we must return true to keep the handler alive.  
    // returning false would remove it after it finishes.
    return true;
}

function send_file() {

    var file_data = $('#file').prop('files')[0];

    var form_data = new FormData();
    form_data.append('file', file_data);
    //form_data.append('_token', csrf_token);

    $.ajax({
        url: file_upload_url,
        cache: false,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: "post",
        dataType: "json",
        data: form_data,
        success: function (data) {
            if (data.status) {
                $('#send_msg_body').val('chatWappnet/' + data.file_name);
                sendMessage();
                $('#file').val('');
            } else {
                $('#file').val('');
            }
        }
    })
}

function sendMessage() {

    var msg = $('#send_msg_body').val();
    
    //log('CHAT: Send a message to ' + $('#to').get(0).value + ': ' + msg);
    if (msg == "") {
        if (document.getElementById("file").files.length == 0) {
            return false;
        } else {
            send_file();
            $("#file").val(null);
        $("#clear_file_btn").hide();
            return false;
        }



    }

    var to = $('#active_chat_jid').val();
    var from = jid;
    var m = $msg({
        to: to,
        from: from,
        type: 'chat'
    }).c("body").t(msg);
    var currentdate = new Date().toLocaleTimeString('en-GB', {hour: "numeric",
        minute: "numeric"});
    connection.send(m);
    var html_append = "";
    var msg_split = msg.split("/");
    if (msg_split[0] && msg_split[0] == "chatWappnet") {
        var extension = msg.split('.').pop();
        if (extension.toLowerCase() == "jpg" || extension.toLowerCase() == "jpeg" || extension.toLowerCase() == "png") {
            msg = '<img onclick="set_chat_img(&apos;' + s3_link + msg + '&apos;)" data-toggle="modal" data-target="#chat_img_modal" src="' + s3_link + msg + '" width="100px" height="100px" />';
        } else {
            msg = '<a href="' + s3_link + msg + '" target="_blank"><i class="fa fa-file fa-6"></i></a>';
        }
        html_append += '<li class="odd">' + '<div class="chat-image"> <img alt="" src="' + loggedin_user_profile + '"> </div>' +
                '<div class="chat-body">' +
                '<div class="chat-text">' +
                '<h4>' + loggedin_username + '</h4>' +
                '<p> ' + msg + ' </p>' +
                '<b>' + (new Date().toLocaleDateString()) + ' ' + (new Date().toLocaleString('en-GB', {hour: 'numeric', minute: 'numeric', hour12: true})) + '</b> </div>' +
                ' </div>' +
                ' </li>';
    } else {
        
        html_append += '<li class="odd">' + '<div class="chat-image"> <img alt="" src="' + loggedin_user_profile + '"> </div>' +
                '<div class="chat-body">' +
                '<div class="chat-text">' +
                '<h4>' + loggedin_username + '</h4>' +
                '<p> ' + msg + ' </p>' +
                '<b>' + (new Date().toLocaleDateString()) + ' ' + (new Date().toLocaleString('en-GB', {hour: 'numeric', minute: 'numeric', hour12: true})) + '</b> </div>' +
                ' </div>' +
                ' </li>';
    }
    $('#chat_body').append(html_append);
    $('#send_msg_body').val("");
    $('.emoji-wysiwyg-editor').html("");
    scroll_down();
}

function set_chat_img(src_link) {

    $('#chat_img_src').attr('src', src_link);
}

function setStatus(s) {
    log('setStatus: ' + s);
    var status = $pres().c('show').t(s);
    connection.send(status);
}

function subscribePresence(jid) {
    log('subscribePresence: ' + jid);
    connection.send($pres({
        to: jid,
        type: "subscribe"
    }));
}

function getPresence(jid) {

    log('getPresence: ' + jid);
    var check = $pres({
        type: 'probe',
        to: jid
    });
    connection.send(check);


}

function getRoster() {

    log('getRoster');
    var iq = $iq({
        type: 'get'
    }).c('query', {
        xmlns: 'jabber:iq:roster'
    });

    connection.sendIQ(iq, rosterCallback);
}

function rosterCallback(iq) {
    log('rosterCallback:');
    var contact_list = "";
    $('#contact_list').html('');

    $(iq).find('item').each(function () {

        var jid = $(this).attr('jid'); // The jabber_id of your contact

        // You can probably put them in a unordered list and and use their jids as ids.
        log('	>' + jid + 'ok');


        //contact_list ='<li><a href="javascript:void(0)"><img src="{{asset(&quot;admin_asset/assets/plugins/images/users/varun.jpg&quot;) }}" alt="user-img" class="img-circle"> <span>'+jid+' <small class="text-success">online</small></span></a></li>';
        //$('#contact_list').append(contact_list);
    });
}

function onSubscriptionRequest(stanza) {
    if (stanza.getAttribute("type") == "subscribe") {
        var from = $(stanza).attr('from');
        log('onSubscriptionRequest: from=' + from);
        // Send a 'subscribed' notification back to accept the incoming
        // subscription request
        connection.send($pres({
            to: from,
            type: "subscribed"
        }));
    }
    return true;
}

function onPresence(presence) {

    log('onPresence:');
    var presence_type = $(presence).attr('type'); // unavailable, subscribed, etc...
    var from = $(presence).attr('from'); // the jabber_id of the contact
    
    var jid_arr = from.split('/');
    var username = jid_arr[0].split('@');
    if (!presence_type)
        presence_type = "online";
    $('#presence_' + username).text(presence_type);
    $('#presence_' + username).removeClass('text-danger');
    $('#presence_' + username).addClass('text-success');
    log('	>' + from + ' --> ' + presence_type);
    if (presence_type != 'error') {
        if (presence_type === 'unavailable') {
            // Mark contact as offline
            $('#presence_' + username).text('offline');
            $('#presence_' + username).removeClass('text-success');
            $('#presence_' + username).addClass('text-danger');
        } else {
            var show = $(presence).find("show").text(); // this is what gives away, dnd, etc.
            if (show === 'chat' || show === '') {
                // Mark contact as online
                $('#presence_' + username).text('online');
                $('#presence_' + username).removeClass('text-danger');
                $('#presence_' + username).addClass('text-success');
            } else {
                // etc...
                $('#presence_' + username).text(show);
                $('#presence_' + username).removeClass('text-success');
                $('#presence_' + username).addClass('text-danger');

            }
        }
    }
    return true;
}

function listRooms() {
    connection.muc.listRooms(mydomain, function (msg) {
        log("listRooms - success: ");
        $(msg).find('item').each(function () {
            var jid = $(this).attr('jid'),
                    name = $(this).attr('name');
            log('	>room: ' + name + ' (' + jid + ')');
        });
    }, function (err) {
        log("listRooms - error: " + err);
    });
}

function enterRoom(room) {
    log("enterRoom: " + room);
    connection.muc.init(connection);
    connection.muc.join(room, $('#jid').get(0).value, room_msg_handler, room_pres_handler);
    //connection.muc.setStatus(room, $('#jid').get(0).value, 'subscribed', 'chat');
}

function room_msg_handler(a, b, c) {
    log('MUC: room_msg_handler');
    return true;
}

function room_pres_handler(a, b, c) {
    log('MUC: room_pres_handler');
    return true;
}

function exitRoom(room) {
    log("exitRoom: " + room);
    //TBD
}

function rawInput(data) {
    console.log('RECV: ' + data);
}

function rawOutput(data) {
    console.log('SENT: ' + data);
}


//code for file transfer
// file
var sid = null;
var chunksize;
var data;
var file = null;
var aFileParts, mimeFile, fileName;

function handleFileSelect(evt) {
    $('#clear_file_btn').show();
    var files = evt.target.files; // FileList object
    file = files[0];
}

function sendFileClick() {
    sendFile(file);

    readAll(file, function (data) {
        log("handleFileSelect:");
        log("	>data=" + data);
        log("	>data.len=" + data.length);

    });
}

function sendFile(file) {
    sid = connection._proto.sid;
    var to = $('#active_chat_jid').val() + '/' + sid;
    var filename = file.name;

    var filesize = file.size;
    var mime = file.type;
    chunksize = filesize;

    log('sendFile: to=' + to);

    // send a stream initiation
    connection.si_filetransfer.send(to, sid, filename, filesize, mime, function (err) {

        alert(err.message);
        fileTransferHandler(file, err);
    });
}

function fileHandler(from, sid, filename, size, mime) {
    // received a stream initiation
    // save to data and be prepared to receive the file.
    log("fileHandler: from=" + from + ", file=" + filename + ", size=" + size + ", mime=" + mime);
    mimeFile = mime;
    fileName = filename;
    return true;
}

function ibbHandler(type, from, sid, data, seq) {
    log("ibbHandler: type=" + type + ", from=" + from);

    switch (type) {
        case "open":
            // new file, only metadata
            aFileParts = [];
            break;
        case "data":
            log("	>seq=" + seq);
            log("	>data=" + data);
            aFileParts.push(data);
            // data
            break;
        case "close":
            // and we're done
            var data = "data:" + mimeFile + ";base64,";
            for (var i = 0; i < aFileParts.length; i++) {
                data += aFileParts[i].split(",")[1];
            }
            log("	>data=" + data);
            log("	>data.len=" + data.length);
            if (mimeFile.indexOf("png") > 0) {
                var span = document.createElement('span');
                span.innerHTML = ['<img class="thumb" src="', data, '" title=""/>'].join('');
            } else {
                var span = document.createElement('span');
                span.innerHTML = ['<a class="link" download="' + fileName + '" href="', data, '" title="">download</a>'].join('');
            }
            document.getElementById('list').insertBefore(span, null);
        default:
            throw new Error("shouldn't be here.")
    }
    return true;
}

function fileTransferHandler(file, err) {
    log("fileTransferHandler: err=" + err);

    if (err) {
        return console.log(err);
    }
    var to = $('#to').get(0).value;
    chunksize = file.size;

    chunksize = 20 * 1024;

    // successfully initiated the transfer, now open the band
    connection.ibb.open(to, sid, chunksize, function (err) {
        log("ibb.open: err=" + err);
        if (err) {
            return console.log(err);
        }


        readChunks(file, function (data, seq) {
            sendData(to, seq, data);
        });
    });
}

function readAll(file, cb) {
    var reader = new FileReader();

    // If we use onloadend, we need to check the readyState.
    reader.onloadend = function (evt) {
        if (evt.target.readyState == FileReader.DONE) { // DONE == 2
            cb(evt.target.result);
        }
    };

    reader.readAsDataURL(file);
}

function readChunks(file, callback) {
    var fileSize = file.size;
    var chunkSize = 20 * 1024; // bytes
    var offset = 0;
    var block = null;
    var seq = 0;

    var foo = function (evt) {
        if (evt.target.error === null) {
            offset += chunkSize; //evt.target.result.length;
            seq++;
            callback(evt.target.result, seq); // callback for handling read chunk
        } else {
            console.log("Read error: " + evt.target.error);
            return;
        }
        if (offset >= fileSize) {
            console.log("Done reading file");
            return;
        }

        block(offset, chunkSize, file);
    }

    block = function (_offset, length, _file) {
        log("_block: length=" + length + ", _offset=" + _offset);
        var r = new FileReader();
        var blob = _file.slice(_offset, length + _offset);
        r.onload = foo;
        r.readAsDataURL(blob);
    }

    block(offset, chunkSize, file);
}

function sendData(to, seq, data) {
    // stream is open, start sending chunks of data
    connection.ibb.data(to, sid, seq, data, function (err) {
        log("ibb.data: err=" + err);
        if (err) {
            return console.log(err);
        }
        // ... repeat calling data
        // keep sending until you're ready you've reached the end of the file
        connection.ibb.close(to, sid, function (err) {
            log("ibb.close: err=" + err);
            if (err) {
                return console.log(err);
            }
            // done
        });
    });
}

$('#btnSendFile').bind('click', function () {
    console.log('File clicked:');
    sendFileClick();
});

document.getElementById('file').addEventListener('change', handleFileSelect, false);

function file_browse() {
    $('#file').click();
}

setInterval(function(){
    connection.send($pres());
},20000);

window.onunload = function() {
    
    alert('Bye.');
}

$('#document').ready(function () {
    $('#search_contact').keyup(function () {
        
        $('#contact_list').html();
        $.ajax({
            url: get_contact_list_url+'/'+this.value,
            type: "GET",
            dataType: "HTML",
            success: function (data) {
                $('#contact_list').html(data);
                
                setTimeout(function () {
                    $('#page_counter').val(0);
                    $('#contact_list li:first-child').click();
                }, 1000);

            }
        })
    });
    
    $('#clear_file_btn').click(function(){
        $("#file").val(null);
        $("#clear_file_btn").hide();
    });
    
    
});
