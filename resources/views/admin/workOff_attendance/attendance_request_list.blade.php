@extends('layouts.admin_app')

@section('content')
<?php

use Illuminate\Support\Facades\Config; ?>
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
                <b class="error">Approval Flow: {{implode(' -> ',config('constants.WORK_OFF_ATTENDANCE_APPROVAL'))}}</b>
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
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
        <div class="col-md-12 col-lg-12 col-sm-12">

            <div class="white-box">
                <div class="row bg-title">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <h4 class="page-title">All Holiday Working Request History</h4>
                    </div>

                </div>
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="table-responsive">
                    <table id="work_off_attendance_history" class="table table-striped">
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
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($attendance_history)) { ?>
                                <?php foreach ($attendance_history as $key => $attendance) { ?>
                                    <tr>
                                    <td>{{ $attendance->user_name }}</td>
                                        <td>{{ Carbon\Carbon::parse($attendance->date)->format('d-m-Y') }}</td>
                                        <td>{{ $attendance->day_type }}</td>
                                        <td>
                                        @if( $attendance->holiday_id >0)
                                          Yes({{ $attendance->title }})
                                        @else
                                          No
                                        @endif

                                        </td>
                                        <td>{{ $attendance->day_name }}</td>
                                        <td>{{ $attendance->reason_note }}</td>
                                        <td>{{ $attendance->description_note }}</td>


                                        <td>
                                            @if($attendance->first_approval_status=="Pending")
                                            <b class="text-warning">{{ $attendance->first_approval_status }}</b>
                                            @elseif($attendance->first_approval_status=="Approved")
                                            <b class="text-success">
                                                {{ $attendance->first_approval_status }}
                                                @if($attendance->first_approval_datetime)
                                                <br>
                                                {{ Carbon\Carbon::parse($attendance->first_approval_datetime)->format('d-m-Y h:i:s A') }}
                                                @endif
                                            </b>
                                            @elseif($attendance->first_approval_status=="Rejected")
                                            <b class="text-danger">

                                                {{ $attendance->first_approval_status }}

                                                @if($attendance->first_approval_datetime)
                                                <br>
                                                {{ Carbon\Carbon::parse($attendance->first_approval_datetime)->format('d-m-Y h:i:s A') }}
                                                @endif
                                            </b>
                                            @endif
                                        </td>

                                        <td>{{ $attendance->first_approval_note }}</td>

                                        <td>
                                            @if($attendance->second_approval_status=="Pending")
                                            <b class="text-warning">{{ $attendance->second_approval_status }}</b>
                                            @elseif($attendance->second_approval_status=="Approved")
                                            <b class="text-success">
                                                {{ $attendance->second_approval_status }}
                                                @if($attendance->second_approval_datetime)
                                                <br>
                                                {{ Carbon\Carbon::parse($attendance->second_approval_datetime)->format('d-m-Y h:i:s A') }}
                                                @endif
                                            </b>
                                            @elseif($attendance->second_approval_status=="Rejected")
                                            <b class="text-danger">
                                                {{ $attendance->second_approval_status }}
                                                @if($attendance->second_approval_datetime)
                                                <br>
                                                {{ Carbon\Carbon::parse($attendance->second_approval_datetime)->format('d-m-Y h:i:s A') }}
                                                @endif
                                            </b>
                                            @endif
                                        </td>

                                        <td>{{ $attendance->second_approval_note }}</td>

                                        <td>@if($attendance->status=="Pending")
                                            <b class="text-warning">{{ $attendance->status }}</b>
                                            @elseif($attendance->status=="Approved")
                                            <b class="text-success">{{ $attendance->status }}</b>
                                            @elseif($attendance->status=="Rejected")
                                            <input style="display:none" type="textarea" id="detail_{{$attendance->id}}" value="{{ $attendance->reject_note  }}" />
                                            <b class="btn btn-danger" onclick="set_reject_reason({{ $attendance->id }});" data-toggle="modal" data-target="#reject_model">{{ $attendance->status }}</b>
                                            @else
                                            <b class="text-dark">{{ $attendance->status }}</b>
                                            @endif

                                        </td>


                                    </tr>
                            <?php
                                }
                            }
                            ?>

                        </tbody>
                    </table>
                </div>
                <!-- -----------------------------------------------reject ------------------------------------------->
            <div id="rejectModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form action="{{ route('admin.reject_work_off_attendance_request') }}" method="POST" id="reject_note_frm">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            </div>
                            <div class="modal-body" id="userTable">
                                <div class="form-group ">
                                    <label>Reject Note</label>
                                    <input type="hidden" name="reject_id" id="reject_id" value="" />
                                    <textarea class="form-control valid" required rows="6" name="note" id="note" spellcheck="false"></textarea>

                                </div>
                            </div>
                            <div class="col-md-12 pull-left">
                                <div class="clearfix"></div>
                                <br>
                                <button type="submit" class="btn btn-danger">Reject</button>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </form>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
                <!----------------------------------------- ---------/------------------------------ -->

                <!----------------------------------------------- Approve ------------------------------- -->
            <div id="approveModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form action="{{ route('admin.approve_work_off_attendance_request') }}" method="POST" id="approve_note_frm">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            </div>
                            <div class="modal-body" id="userTable">
                                <div class="form-group ">
                                    <label>Approve Note</label>
                                    <input type="hidden" name="work_off_attendance_id" id="approve_id" value="" />
                                    <textarea class="form-control valid" required rows="6" name="approve_note" id="approve_note" spellcheck="false"></textarea>

                                </div>
                            </div>
                            <div class="col-md-12 pull-left">
                                <div class="clearfix"></div>
                                <br>
                                <button type="submit" class="btn btn-danger">Approve</button>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </form>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
                <!--------------------------------------------------- / ------------------------------------------>

            <!--======================================= Reject Model Detail======================= -->
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
                <!--========================================== Model =====================================-->
            </div>
        </div>

        @endsection

        @section('script')
        <script>

            $(document).ready(function () {
                 $('#work_off_attendance_history').DataTable({
            "processing": true,
            "responsive": true

        });

                var table = $('#work_off_attendance').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
					"stateSave": true,
                    "order": [[0, "DESC"]],
                    "ajax": {
                        url: "<?php echo route('admin.get_workOff_attendance_request_all_list_ajax'); ?>",
                        type: "GET",
                    },
                    "columns": [
                        {"taregts": 0, 'data': 'user_name' },
                        {"taregts": 1, "render":function(data,type,row){
                            return moment(row.date).format("DD-MM-YYYY");
                        }
                        },
                        {"taregts": 2, 'data': 'day_type' },
                        {"taregts": 3, 'render': function(data,type, row){
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
                            "render": function(data, type, row) {
                                var out = '';
                                if (row.status == 'Pending') {
                                    return '<b class="text-warning">Pending</b>';
                                } else if (row.status == 'Approved') {
                                    return '<b class="text-success">Approved</b>';
                                } else if(row.status == 'Canceled'){
                                    return '<b class="text-dark">Canceled</b>';
                                }else{
                                    return '<b class="text-danger">Rejected</b>';
                                }
                            }
                        },{
                            "targets": 12,
                            "searchable": false,
                            "orderable": false,
                            "render": function(data, type, row) {
                                var id = row.id;
                                var out = "";
                                var role = "<?php echo Auth::user()->role; ?>";
                                var getRole = "<?php echo config('constants.REAL_HR'); ?>";
                                var SuperUser = "<?php echo config('constants.SuperUser'); ?>";

                               if (row.status != 'Canceled') {


                                if (row.first_approval_status == "Pending" && role == getRole) {
                                    out += ' <button type="button" data-toggle="modal" data-target="#approveModal" onclick=confirmRequest(' + row.id + ',"Approved") class="btn btn-info btn-circle"><i class="fa fa-check"></i> </button>';
                                    out += ' <button type="button" data-target="#rejectModal" onclick=confirmRequest(' + row.id + ',"Reject") class="btn btn-warning btn-circle"><i class="fa fa-times"></i> </button>';
                                }

                                if (row.first_approval_status == "Approved" && row.second_approval_status == "Pending" && role == SuperUser) {
                                    out += ' <button type="button" data-toggle="modal" data-target="#approveModal" onclick=confirmRequest(' + row.id + ',"Approved") class="btn btn-info btn-circle"><i class="fa fa-check"></i> </button>';
                                    out += ' <button type="button" data-target="#rejectModal" onclick=confirmRequest(' + row.id + ',"Reject") class="btn btn-warning btn-circle"><i class="fa fa-times"></i> </button>';
                                }
                            }
                                return out;
                            }


                        }

                    ]
                });
            })

        </script>

        <script>
        function confirmRequest(url, status) {

            if (status == "Reject") {
                // Reject
                $("#reject_id").val(url);
                $("#rejectModal").modal('toggle'); //see here usage
            } else {
                // Approve
                $('#approve_id').val(url);
            }
        }

        function set_reject_reason(id){
            $('#detail_div').html($('#detail_' + id).val());
            }
        </script>
        @endsection

