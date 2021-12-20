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
                <a href="{{ route('admin.add_softcopy_request') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Softcopy Request</a>
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

@endsection

@section('script')
<script>

    var table = $('#request_table').DataTable({
        dom: 'lBfrtip',
        buttons: ['excel'],
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "stateSave": true,
        "order": [[3, "DESC"]],
        "ajax": {
            url: "<?php echo route('admin.sent_softcopy_request'); ?>",
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
                    if (row.status == 'Pending' && {{ Auth::user()->id }} == row.request_user_id ){

                        out += '<a href="<?php echo url("edit_softcopy_request") ?>' + '/' + row.id + '" class="btn btn-primary btn-rounded"><i class="fa fa-pencil"></i></a>';
                        out += '<a onclick="confirm(this);" data-text="Are you sure you want to delete softcopy request?" data-href="<?php echo url('delete_softcopy_request'); ?>/' + row.id + '" class="btn btn-danger btn-rounded" title="Delete Request"><i class="fa fa-trash"></i></a>';

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
