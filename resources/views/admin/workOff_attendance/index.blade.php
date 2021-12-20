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
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
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
                <a href="{{ route('admin.add_attendance_request') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Attendance Request</a>
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="table-responsive">
                    <table id="work_off_attendance" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Date</th>
                                <th>Day Type</th>
                                <th>Holiday Working</th>
                                <th>Day</th>
                                <th>Reason to Come</th>
                                <th>Description of work</th>
                                <th>HR Status</th>
                                <th>HR Approval Note</th>
                                <th>SuperAdmin Status</th>
                                <th>SuperAdmin Approval Note</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <!-- Reject Model -->
                <div id="reject_model" class="modal fade">
            <div class="modal-dialog modal-lg">
                <div class="modal-content" >
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h3>Rejection Reason</h3>
                    </div>
                    <div class="modal-body" id="detail_div">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
                <!-- Model -->
            </div>
            <!--row -->
        </div>
        @endsection
        @section('script')
        <script>


            $(document).ready(function () {
                var table = $('#work_off_attendance').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
					"stateSave": true,
                    "order": [[0, "DESC"]],
                    "ajax": {
                        url: "<?php echo route('admin.get_workOff_attendance_request_list'); ?>",
                        type: "GET",
                    },
                    "columns": [
                        {"taregts": 0, 'data': 'user_name' },
                        {"taregts": 1, "render":function(data,type,row){
                            return moment(row.date).format("DD-MM-YYYY");
                        }
                        },
                        {"taregts": 2, 'data': 'day_type' },
                        {"taregts": 3, 'render': function(data, type, row){
                            var out = '';
                            if (row.holiday_id > 0) {
                                return out +='Yes'+'('+row.title+')';
                            }else{
                                return out += 'No';

                            }
                        }
                        },
                        {"taregts": 4, 'data': 'day_name' },
                        {"taregts": 5, 'data': 'reason_note' },
                        {"taregts": 6, 'data': 'description_note'},
                        {
                            "targets": 7,
                            "searchable": false,
                            "render": function(data, type, row) {
                                var out = '';
                                if (row.first_approval_status == 'Pending') {
                                    out += '<b class="text-warning">Pending</b>';
                                } else if (row.first_approval_status == 'Approved') {
                                    out += '<b class="text-success">Approved</b>';
                                    out += '<br>';
                                    if (row.first_approval_datetime) {
                                        out += moment(row.first_approval_datetime).format("DD-MM-YYYY h:mm:ss a");
                                    }
                                } else {
                                    out += '<b class="text-danger">Rejected</b>';
                                    out += '<br>';
                                    if (row.first_approval_datetime) {
                                        out += moment(row.first_approval_datetime).format("DD-MM-YYYY h:mm:ss a");
                                    }
                                }
                                return out;
                            }
                        },
                        {"taregts": 8, 'data': 'first_approval_note'},
                        {
                            "targets": 9,
                            "searchable": false,
                            "render": function(data, type, row) {
                                var out = '';
                                if (row.second_approval_status == 'Pending') {
                                    out += '<b class="text-warning">Pending</b>';
                                } else if (row.second_approval_status == 'Approved') {
                                    out += '<b class="text-success">Approved</b>';
                                    out += '<br>';
                                    if (row.second_approval_datetime) {
                                        out += moment(row.second_approval_datetime).format("DD-MM-YYYY h:mm:ss a");
                                    }
                                } else {
                                    out += '<b class="text-danger">Rejected</b>';
                                    out += '<br>';
                                    if (row.second_approval_datetime) {
                                        out += moment(row.second_approval_datetime).format("DD-MM-YYYY h:mm:ss a");
                                    }
                                }
                                return out;
                            }
                        },
                        {"taregts": 10, 'data': 'second_approval_note'},
                        {
                            "targets": 11,
                            "searchable": false,
                            "render": function(data, type, row) {
                                var out = '';
                                if (row.status == 'Pending') {
                                    return '<b class="text-warning">Pending</b>';
                                } else if (row.status == 'Approved') {
                                    return '<b class="text-success">Approved</b>';
                                } else if(row.status == 'Rejected'){
                                    return out+='<input style="display:none" type="textarea" id="detail_' + row.id + '" value="' + row.reject_note + '" /><a href="#" class="btn btn-danger" onclick="set_reject_reason('+ row.id+');" data-toggle="modal" data-target="#reject_model">Rejected</a>';
                                }else{
                                    return '<b class="text-dark">Canceled</b>';
                                }
                            }
                        },
                        {
                            "taregts": 12,
                            "searchable": false,
                            "orderable": false,
                            "render": function(data, type, row) {

                                var id = row.id;
                                var out = "";


                                    if(row.status=='Pending' || row.status=='Rejected'){
                                        out += '<a href="<?php echo url("edit_attendance_request") ?>' + '/' + id + '" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>';

                                        out += '<a href="<?php echo url("cancel_request") ?>' + '/' + id + '" class="btn btn-primary btn-rounded"><i class="fa fa-times"></i></a>';
                                    }

                                    return out;

                            }
                        }

                    ]
                });
            });

            function set_reject_reason(id){
                $('#detail_div').html($('#detail_' + id).val());
            }
        </script>
        @endsection
