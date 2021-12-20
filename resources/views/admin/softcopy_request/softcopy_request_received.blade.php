<?php
use Illuminate\Support\Facades\Auth;
?>

@extends('layouts.admin_app')

@section('content')
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">{{ $page_title }}</h4>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li><a href="#">{{ $page_title }}</a></li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="white-box">                
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="row">
                    <div class="table-responsive">
                        <table id="request_table" class="table table-striped">
                            <thead>
                                <th>Company Name</th>
                                <th>Document Name</th>
                                <th>Receiver Name</th>
                                <th>Softcopy</th>
                                <th>Comment</th>
                                <th>reason</th>
                                <th>Request Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="reject_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" action="{{ route('admin.reject_softcopy_request') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Reject Reason</h4>
                </div>
                <div class="modal-body" id="tableBody">
                    <input type="hidden" name="reject_id" id="reject_id" />
                    <textarea class="form-control" required name="reason" id="reason"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger" >Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="send_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" action="{{ route('admin.send_softcopy') }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Upload Softcopy</h4>
                </div>
                <div class="modal-body" id="tableBody">
                    <input type="hidden" name="id" id="softcopy_request_id" />
                    <input type="file" class="form-control" name="file" id="file"/>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger" >Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('script')
<script>
    function softcopy_request_reject(id) {
        $('#reject_id').val(id);
    }
    function send_softcopy(id) {
        $('#softcopy_request_id').val(id);
    }

    var table = $('#request_table').DataTable({
        dom: 'lBfrtip',
        buttons: ['excel'],
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "stateSave": true,
        "order": [[3, "DESC"]],
        "ajax": {
            url: "<?php echo route('admin.get_softcopy_request'); ?>",
            type: "GET",
        },
        "columns": [
            {"taregts": 0, 'data': 'company_name' },
            {"taregts": 1, 'data': 'name' },
            {"taregts": 2, 'data': 'user_name'},
            {"taregts": 3, "render": function (data, type, row) {

                if(row.file_name){
                    var img = row.file_name;
                    var baseURL = img.replace("public/","");
                    var url=  "<?php echo url('/storage/');?>"+"/"+baseURL;
                    return '<a title="Download" href="'+url+'" download><i class="fa fa-cloud-download  fa-lg"></i></a>';
                }else{
                    return '';
                }                 
                }
            },
            {"taregts": 4, 'data': 'comment'},
            {"taregts": 5, 'data': 'reason'},
            {"taregts": 6, "render": function (data, type, row) {
                    return moment(row.request_datetime).format("DD-MM-YYYY");
                }
            },
            {"taregts": 7, "render": function (data, type, row) {
                // 'data': ',',
                    if (row.status == 'Pending') {
                        return '<b class="text-warning">Pending</b>';
                    } else if (row.status == 'Completed') {
                        return '<b class="text-success">Approved<br>'+ moment(row.updated_at).format("DD-MM-YYYY h:mm A")+'</b>';
                    } else if (row.status == 'Rejected') {
                        return '<b class="text-danger">Rejected</b>';
                    }
                }
            },
            {"taregts": 8,
                "render": function (data, type, row) {
                    var out = '';
                    if (row.status == 'Pending' && {{ Auth::user()->id }} == row.receiver_user_id ){

                        out += ' <a onclick="send_softcopy(' + row.id + ');" data-toggle="modal" data-target="#send_modal" class="btn btn-success btn-rounded" title="Send"><i class="fa fa-check"></i></a>';
                        out += ' <a onclick="softcopy_request_reject(' + row.id + ',&apos;admin&apos;);" data-toggle="modal" data-target="#reject_modal" class="btn btn-danger btn-rounded" title="Reject"><i class="fa fa-times"></i></a>';
                    }
                    return out;
                }
            }
        ]
    });

    function confirm(e) {
        swal({
            title: $(e).attr('data-text'),
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes",
            closeOnConfirm: false
        }, function () {
            window.location.href = $(e).attr('data-href');
        });
    }
</script>
@endsection
