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

            @if(Auth::user()->role!=config('constants.Admin'))
            <div class="white-box">
                <!-- <b class="error">Approval Flow: {{implode(' -> ',config('constants.LEAVE_APPROVAL'))}}</b> -->
                <b class="error">Approval Flow: <strong>Reliever User -> HR -> SuperAdmin</strong></b>
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="table-responsive">
                    <table id="leave_table" class="table table-striped">
                        <thead>
                            <tr>

                                <th>User Name</th>
                                <th>Reliever User Name</th>
                                <th>Leave Dates</th>
                                <th>Detail</th>
                                <th>Leave Type</th>
                                <th>HR STATUS</th>
                                <th>SUPER ADMIN STATUS</th>
                                <th>Status</th>
                                <th>Action Taken On</th>
                                <th>Approve</th>
                                <th>Reject</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
            <!--row -->

            <div class="white-box">
                <h3 class="title">Leave History</h3>
                <div class="table-responsive">
                    <table id="all_leave_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>User Name</th>
                                <th>Reliever User Name</th>
                                <th>Leave Start Date</th>
                                <th>Leave End Date</th>
                                <th>Leave Type</th>
                                <th>HR STATUS</th>
                                <th>SUPER ADMIN STATUS</th>
                                <th>Status</th>
                                <th>Subject</th>
                                <th>Details</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
        <div id="details_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">Detail</h4>
                    </div>

                    <div class="modal-body" id="detail_div">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        @endsection
        @section('script')
        <script>

            function show_detail(id) {
                $('#detail_div').html($('#detail_' + id).val());

            }


            $(document).ready(function () {

                var leaveStatus = {1: 'Pending', 2: 'Approved', 3: 'Rejected', 4: 'Canceled'};
                var access_rule = '<?php echo $access_rule; ?>';
                access_rule = access_rule.split(',');

                function month_name(dt) {
                    const objDate = new Date(dt);
                    const locale = "en-us";
                    const month = objDate.toLocaleString(locale, {
                        month: "short"
                    });
                    return month;
                }
                var table = $('#leave_table').DataTable({
                    dom: 'lBfrtip',
                    buttons: ['excel'],
					"processing": true,
                    "serverSide": true,
                    "responsive": true,
                    "order": [[2, "DESC"]],
                    "ajax": {
                        url: "<?php echo route('admin.get_all_leave_list'); ?>",
                        type: "GET",
                    },
                    "columns": [
                        {"targets": 0, "data" : "user_name", "render": function (data, type, row) {
                                return '<b class="text-info">'+row.user_name+'</b>';
                            }
                        },
                        {"targets": 1,"data" : "reliever_name", "render": function (data, type, row) {
                                return '<b class="text-success">'+ row.reliever_name +'</b>';

                            }
                        },
                        {"targets": 2, "data" : "start_date","searchable": true,
                            "render": function (data, type, row) {
                                var diff = new Date(new Date(row.end_date) - new Date(row.start_date));
                                var days = (diff / 1000 / 60 / 60 / 24) + 1;
                                var date = month_name(row.start_date) + moment(row.start_date).format(" DD, YYYY");
                                return date + '</br> (No. of days ' + days + ')';
                            }
                        },
                        {
                            "targets": 3,"searchable": true,
                            "render": function(data, type, row) {
                                if (row.description == null) {
                                    row.description = 'No Found';
                                }
                                return '<input style="display:none" type="textarea" id="detail_' + row.id + '" value="' + row.description + '" /><a class="btn btn-warning" data-toggle="modal" data-target="#details_modal" href="#" onclick="show_detail(' + row.id + ');">View</a>';
                            }

                        },
                        {"targets": 4, "data": "category_name" ,"searchable": true,
                             "render": function (data, type, row) {
                                var leaveType = row.category_name;
                                var date = month_name(row.created_at) + moment(row.created_at).format(" DD, YYYY");
                                return leaveType + '</br> Requested on - ' + date;
                            }
                        },
                        {
                            "targets": 5,
                            'data': 'first_approval_status',
                            "render": function(data, type, row) {

                                var out = '';
                                // if (row.first_approval_datetime) {
                                //     out += moment(row.first_approval_datetime).format("DD-MM-YYYY h:mm:ss A");
                                // }
                                
                                // moment(row.first_approval_datetime).format("DD-MM-YYYY h:mm:ss A");

                                if (row.first_approval_status == 'Pending') {
                                    return '<b class="text-warning">Pending</b>';
                                } else if (row.first_approval_status == 'Approved') {
                                    if(moment(row.first_approval_datetime).isValid()){
                                        return '<b class="text-success">Approved <br>'  + moment(row.first_approval_datetime).format("DD-MM-YYYY h:mm A")+'</b>';
                                    }else{
                                        return '<b class="text-success">Approved <br>';
                                    }
                                } else if (row.first_approval_status == 'Canceled') {
                                    return '<b class="text-danger">Canceled</b>';
                                } else {
                                    return '<b class="text-danger">Rejected</b>';
                                }
                                
                                
                            }
                        },
                        {
                            "targets": 6,
                            'data': 'third_approval_status',
                            "render": function(data, type, row) {

                                if (row.third_approval_status == 'Pending') {
                                    return '<b class="text-warning">Pending</b>';
                                } else if (row.third_approval_status == 'Approved') {
                                    return '<b class="text-success">Approved</b>';
                                } else if (row.third_approval_status == 'Canceled') {
                                    return '<b class="text-danger">Canceled</b>';
                                } else {
                                    return '<b class="text-danger">Rejected</b>';
                                }


                            }
                        },

                        {"targets": 7, "data": "leave_status", "render": function (data, type, row) {
                                if(row.leave_status==1){
                                    return '<span class="text-warning">'+ leaveStatus[row.leave_status]+'</span>';
                                }
                                else if(row.leave_status==2){
                                    return '<span class="text-success">'+leaveStatus[row.leave_status]+'</span>';
                                }
                                else{
                                    return '<span class="text-danger">'+leaveStatus[row.leave_status]+'</span>';
                                }
                            }
                        },
                        {"targets": 8, "data": "updated_at",
                             "render": function (data, type, row) {
                                return month_name(row.updated_at) + moment(row.updated_at).format(" DD, YYYY");
                            }
                        },
                        {"targets": 9, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;

                                if(row.leave_status == 1 && ($.inArray('2', access_rule) !== -1)){
                                    return '<a href="<?php echo url("approve_leave") ?>' + '/' + id + '" class="btn btn-primary btn-rounded"><i class="fa fa-check"></i></a>';
                                }else 
                                    return "";
                                }
                        },
                        {"targets": 10, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                if (row.leave_status == 1 && ($.inArray('2', access_rule) !== -1)){
                                    return '<a href="<?php echo url("reject_leave") ?>' + '/' + id + '" class="btn btn-primary btn-rounded"><i class="fa fa-times"></i></a>';
                                }else if(row.leave_cancellation_status == "Pending"){
                                    return '<a href="<?php echo url("reverse_reject_leave") ?>' + '/' + id + '" class="btn btn-danger btn-rounded" title="Reject Leave Cancel Request"><i class="fa fa-times"></i></a>';
                                }else{
                                    return "";
                                }
                            }
                        },
                    ]
                });

                var table = $('#all_leave_table').DataTable({

				dom: 'Bfrtip',
				buttons: [
					'copy', 'csv', 'excel', 'pdf', 'print'
				],


                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
					"pageLength": 500,
                    "order": [[1, "DESC"]],
                    "ajax": {
                        url: "<?php echo route('admin.get_leave_list_for_all'); ?>",
                        type: "GET",
                    },
                    "columns": [
                        {"targets": 0, "render": function (data, type, row) {
                                return '<b class="text-info">'+row.user_name+'</b>';
                            }
                        },
                        {"targets": 1, "render": function (data, type, row) {
                                return '<b class="text-success">'+ row.reliever_name +'</b>';

                            }
                        },
                        {"targets": 2, "render": function (data, type, row) {
                                return row.start_date;
                                //var date = moment(row.start_date).format(" DD-MM-YYYY");

                            }
                        },
                        {"targets": 3, "render": function (data, type, row) {
                                return row.end_date;
                            }
                        },
                        {"targets": 4, "data": "category_name" ,"searchable": true,
                             "render": function (data, type, row) {
                                return row.category_name;
                            }
                        },
                        {
                            "targets": 5,
                            'data': 'first_approval_datetime',
                            "searchable": true,
                            "render": function(data, type, row) {

                                var out = '';
                                // if (row.first_approval_datetime) {
                                //     out += moment(row.first_approval_datetime).format("DD-MM-YYYY h:mm:ss A");
                                // }
                                
                                // moment(row.first_approval_datetime).format("DD-MM-YYYY h:mm:ss A");

                                if (row.first_approval_status == 'Pending') {
                                    return '<b class="text-warning">Pending</b>';
                                } else if (row.first_approval_status == 'Approved') {
                                    if(moment(row.first_approval_datetime).isValid()){
                                        return '<b class="text-success">Approved <br>'  + moment(row.first_approval_datetime).format("DD-MM-YYYY h:mm A")+'</b>';
                                    }else{
                                        return '<b class="text-success">Approved <br>';
                                    }
                                } else if (row.first_approval_status == 'Canceled') {
                                    if(moment(row.first_approval_datetime).isValid()){
                                        return '<b class="text-danger">Canceled</b>' + moment(row.first_approval_datetime).format("DD-MM-YYYY h:mm A")+'</b>';
                                    }else{
                                        return '<b class="text-danger">Rejected <br>';
                                    }
                                } else {
                                    return '<b class="text-danger">Rejected <br>'  ;
                                }
                                
                                
                            }
                        },
                        {
                            "targets": 6,
                            'data': 'second_approval_datetime',
                            "searchable": true,
                            "render": function(data, type, row) {
                                
                                
                                if (row.third_approval_status == 'Pending') {
                                    return '<b class="text-warning">Pending</b>'
                                } else if (row.third_approval_status == 'Approved') {
                                    if(moment(row.third_approval_datetime).isValid()){
                                        return '<b class="text-success">Approved <br>'  + moment(row.third_approval_datetime).format("DD-MM-YYYY h:mm A")+'</b>';
                                    }else{
                                        return '<b class="text-success">Approved <br>';
                                    }
                                } else if (row.third_approval_status == 'Canceled') {
                                    return '<b class="text-danger">Canceled</b>' 
                                } else {
                                    return '<b class="text-danger">Rejected</b>' 
                                }


                            }
                        },

                        {   "targets": 7, 
                            "searchable": true, 
                            'data':'third_approval_datetime',
                            "render": function (data, type, row) {

                                if(row.leave_status==1){
                                    return '<span class="text-warning">'+ leaveStatus[row.leave_status]+'</span>';
                                }
                                else if(row.leave_status==2){
                                    if(moment(row.third_approval_datetime).isValid()){
                                        return '<b class="text-success">Approved <br>';
                                    }else{
                                        return '<b class="text-success">Approved <br>';
                                    }
                                }
                                else{
                                    return '<span class="text-danger">'+leaveStatus[row.leave_status]+'</span>';
                                }
                            }
                        },

                        {"targets": 8, "data": "subject", "searchable": true,
                             "render": function (data, type, row) {
                                return row.subject;
                            }
                        },
                        {"targets": 9,"data": "description" , "searchable": true, "orderable": false,
                            "render": function (data, type, row) {
                               return row.description;
                            }
                        },

                    ]

                });
            })
        </script>
        @endsection
