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
                <a href="{{ route('admin.add_leave') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Leave</a>
                <p class="text-muted m-b-30"></p>
                <br>
                <b class="error">Approval Flow: {{implode(' -> ',config('constants.LEAVE_APPROVAL'))}}</b>
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="table-responsive">
                    <table id="leave_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>LEAVE SUBJECT</th>
                                <th>LEAVE DATES</th>
                                <th>DETAIL</th>
                                <th>LEAVE TYPE</th>
                                <th>Assigned Work Status</th>
                                <th>HR STATUS</th>
                                <th>SUPER ADMIN STATUS</th>
                               
                                <th>STATUS</th>
                                <th>ACTION TAKEN ON</th>
                                <th>Edit</th>
                                <th>Cancel</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
            <!--row -->
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
        
        <div id="reject_assignwork_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">Reject Note</h4>
                    </div>

                    <div class="modal-body" id="reject_assignwork_note">

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
            
            function reject_assign_work_reason(id){
                $('#reject_assignwork_note').html($('#assignwork_reject_note_detail_'+id).text());
            }

            $(document).ready(function() {
                var leaveStatus = {
                    1: 'Pending',
                    2: 'Approved',
                    3: 'Rejected',
                    4: 'Canceled'
                };

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
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    "order": [
                        [1, "DESC"]
                    ],
                    stateSave: true,
                    "ajax": {
                        url: "<?php echo route('admin.get_my_leave_list'); ?>",
                        type: "GET",
                    },
                    "columns": [{
                            "targts": 0,
                            "data": "subject"
                        },
                        {
                            "taregts": 1,
                            "render": function(data, type, row) {
                                var diff = new Date(new Date(row.end_date) - new Date(row.start_date));
                                var days = (diff / 1000 / 60 / 60 / 24) + 1;
                                var date = month_name(row.start_date) + moment(row.start_date).format(" DD, YYYY");
                                return date + '</br> No. of days - ' + days;
                            }
                        },
                        {
                            "taregts": 2,
                            "render": function(data, type, row) {
                                if (row.description == null) {  
                                    row.description = 'No Found';
                                }
                                return '<input style="display:none" type="textarea" id="detail_' + row.id + '" value="' + row.description + '" /><a class="btn btn-warning" data-toggle="modal" data-target="#details_modal" href="#" onclick="show_detail(' + row.id + ');">View</a>';
                            }
                            
                        },
                        {
                            "taregts": 3,
                            "render": function(data, type, row) {
                                var leaveType = row.name;
                                var date = month_name(row.created_at) + moment(row.created_at).format(" DD, YYYY");
                                return leaveType + '</br> Requested on - ' + date;
                            }

                        },
                        {
                            "taregts": 4,
                            "searchable": false,
                            "render": function(data, type, row) {

                                if (row.assign_work_status == 'Pending') {
                                    return '<b class="text-warning">Pending</b>';
                                } else if (row.assign_work_status == 'Accepted') {
                                    return '<b class="text-success">Accepted</b>';
                                }  else {
                                    var html='<textarea style="display:none;" id="assignwork_reject_note_detail_'+row.id+'">'+row.assign_work_reject_note+'</textarea>';
                                    return html+'<button class="btn btn-danger" data-toggle="modal" data-target="#reject_assignwork_modal" onclick="reject_assign_work_reason('+row.id+')">Rejected</button>';
                                }
                                
                            }   
                        },
                        {
                            "taregts": 5,
                            "searchable": false,
                            "render": function(data, type, row) {

                                if (row.first_approval_status == 'Pending') {
                                    return '<b class="text-warning">Pending</b>';
                                } else if (row.first_approval_status == 'Approved') {
                                    return '<b class="text-success">Approved</b>';
                                } else if (row.first_approval_status == 'Canceled') {
                                    return '<b class="text-danger">Canceled</b>';
                                } else {
                                    return '<b class="text-danger">Rejected</b>';
                                }
                                
                            }   
                        },
                        {
                            "taregts": 6,
                            "searchable": false,
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
                        {
                            "taregts": 7,
                            "searchable": false,
                            "orderable":false,
                            "render": function(data, type, row) {

                                if (leaveStatus[row.leave_status] == 'Pending') {
                                    return '<b class="text-warning">Pending</b>';
                                } else if (leaveStatus[row.leave_status] == 'Approved') {
                                    return '<b class="text-success">Approved</b>';
                                } else if (leaveStatus[row.leave_status] == 'Canceled') {
                                    return '<b class="text-danger">Canceled</b>';
                                } else {
                                    return '<b class="text-danger">Rejected</b>';
                                }

                               
                            }
                        },
                        {
                            "taregts": 8,
                            "render": function(data, type, row) {
                                return month_name(row.updated_at) + moment(row.updated_at).format(" DD, YYYY");
                            }
                        },
                        {
                            "taregts": 9,
                            "searchable": false,
                            "orderable": false,
                            "render": function(data, type, row) {

                                var id = row.id;
                                if (row.leave_status == 1 && ($.inArray('2', access_rule) !== -1))
                                    return '<a href="<?php echo url("edit_leave") ?>' + '/' + id + '" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>';
                                else
                                    return "";
                            }
                        },
                        {
                            "taregts": 10,
                            "searchable": false,
                            "orderable": false,
                            "render": function(data, type, row) {

                                var id = row.id;
                                if (row.leave_status == 1 && ($.inArray('2', access_rule) !== -1))
                                    return '<a href="<?php echo url("cancel_leave") ?>' + '/' + id + '" class="btn btn-primary btn-rounded"><i class="fa fa-times"></i></a>';
                                else
                                    return "";
                            }
                        },
                    ]

                });
            })
        </script>
        @endsection