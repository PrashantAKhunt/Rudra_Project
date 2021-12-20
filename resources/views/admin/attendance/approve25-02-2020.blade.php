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
        <div class="col-md-12 col-lg-12 col-sm-12">
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
            <div class="white-box">
                <div class="table-responsive">
                    <table id="attendance_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Employee Name</th>
                                <th>Availability</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Punch Type</th>
                                <th>Device Type</th>
                                <th>Location</th>
                                <th>Remote Punch Reason</th>
                                <th>Is Approved</th>
                                <th>Reject Reason</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>            
        </div>    
    </div>
</div>
<div id="approve_model" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content" id="model_data">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h3 class="panel-title">Attendance Approval</h3>
            </div>
            <div class="modal-body">
                <form method="post" action="{{ route('admin.attendance_approval') }}" id="attendance_approval">
                    @csrf
                    <div class="row">
                        <div class="col-sm-12">
                            <label for="is_approved">Approve</label>
                            <select class="form-control" name="is_approved" id="is_approved" required>
                                <option value="YES">Yes</option>
                                <option value="NO">No</option>
                            </select>
                            <input type="hidden" name="attendance_id" id="attendance_id">
                        </div>
                    </div>
                    </br>
                    <div class="row reason_block hide">
                        <div class="col-sm-12">
                            <label for="reject_reason">Reason</label>
                            <textarea name="reject_reason" id="reject_reason" class="form-control" rows="3"></textarea>
                        </div>
                    </div>                    
                    </br>
                    <div class="row">                        
                        <div class="col-sm-2">
                            <button type="submit" name="submit" class="btn btn-success btn-block">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')		
<script>
    $(document).ready(function () {
        var availability = {1: 'Present', 2: 'Pending', 3: 'Leave', 4: 'Holiday', 5: 'Weekend', 6: 'Mixed Leave'};

        var table = $('#attendance_table').DataTable({
            "processing": true,
            "serverSide": true,
            "responsive": true,
            "order": [[2, "DESC"]],
            stateSave: true,
            "ajax": {
                url: "<?php echo route('admin.approve_attendance_list'); ?>",
                type: "GET",
            },
            "columns": [
                {"taregts": 0, "searchable": true, "data": "name"},
                {"taregts": 1, "searchable": false, "render": function (data, type, row) {
                        return availability[row.availability_status];
                    }
                },
                {"taregts": 2, "searchable": true, "render": function (data, type, row) {
                        return moment(row.date).format("DD/MM/YYYY");
                    }
                },                
                {"taregts": 3, "searchable": true, "render": function (data, type, row) {
                        return moment(row.time).format("hh:mm A");
                    }
                },
                {"taregts": 4, "searchable": true, "data": "punch_type"},
                {"taregts": 5, "searchable": true, "data": "device_type"},                
                {"taregts": 6, "searchable": true, "data": "location"},
                {"taregts": 7, "searchable": true, "data": "remote_punch_reason"},
                {"taregts": 8, "searchable": true, "data": "is_approved"},
                {"taregts": 9, "searchable": true, "data": "reject_reason"},
                {"taregts": 10, "searchable": false, "orderable": false,
                    "render": function (data, type, row) {
                        var attendance_id = row.detail_id;
                        var is_approved = row.is_approved;
                        var reject_reason = row.reject_reason;
                        if(is_approved=='Pending'){
                        return '<a onclick="setData($(this))" data-attendance_id="'+attendance_id+'" data-is_approved="'+is_approved+'" data-reject_reason="'+reject_reason+'" data-toggle="modal" href="#approve_model" class="btn btn-primary btn-rounded" title="Approve attendance"><i class="fa fa-exchange"></i></a>';
                    }
                    else{
                        return "";
                    }
                    }
                },
            ]
        });
    });

    function setData(thisObject)
    {
        $('#attendance_id').val(thisObject.data('attendance_id'));
        if(thisObject.data('is_approved') != 'Pending')
            $('#is_approved').val(thisObject.data('is_approved'));
        $('#reject_reason').html(thisObject.data('reject_reason'));        
    }

    $('#is_approved').change(function(){
        if($(this).val() == 'YES'){
            $('.reason_block').addClass('hide');
            $('#reject_reason').html('');
            $('#reject_reason').attr('required', false);
        }else{
            $('.reason_block').removeClass('hide');
            $('#reject_reason').attr('required', true);
        }
    });

</script>
@endsection