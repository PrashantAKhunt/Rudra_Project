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
                <b class="error">Approval Flow: {{implode(' -> ',config('constants.DRIVER_EXPENSE_APPROVAL'))}}</b>
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="table-responsive">
                    <table id="expense_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>NAME</th>
                                <th>FUEL TYPE</th>
                                <th>VEHICLE</th>
                                <th>VEHICLE NUMBER</th>
                                <th>EXPENSE DATE</th>
                                <th>EXPENSE TIME</th>
                                <th>AMOUNT</th>
                                <th>COMMENT</th>
                                <th>Bill Image</th>
                                <th>Meter Image</th>
                                <th>ACCOUNTANT STATUS</th>
                                <th>ADMIN STATUS</th>
                                <th>SUPER ADMIN STATUS</th>
                                <th>Your Approval</th>
                                <th>APPROVE</th>
                                <th>REJECT</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <!--row -->
        </div>

        <div class="col-md-12 col-lg-12 col-sm-12">

            <div class="white-box">
                <div class="row bg-title">
                    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                        <h4 class="page-title">All Expense History</h4>
                    </div>

                    <!-- /.col-lg-12 -->
                </div>
                <div class="table-responsive">
                    <table id="all_expense_list_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>NAME</th>
                                <th>FUEL TYPE</th>
                                <th>VEHICLE</th>
                                <th>VEHICLE NUMBER</th>
                                <th>EXPENSE DATE</th>
                                <th>EXPENSE TIME</th>
                                <th>AMOUNT</th>
                                <th>COMMENT</th>
                                <th>Bill Image</th>
                                <th>Meter Image</th>
                                <th>ACCOUNTANT STATUS</th>
                                <th>ADMIN STATUS</th>
                                <th>SUPER ADMIN STATUS</th>
                                <th>Status</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($all_Expenses)) {
                                foreach ($all_Expenses as $singleValue) {
                            ?>

                                    <tr>
                                        <td><?= $singleValue->u_name; ?></td>
                                        <td><?= $singleValue->fuel_type; ?></td>
                                        <td><?= $singleValue->name; ?></td>
                                        <td><?= $singleValue->asset_1; ?></td>
                                        <td><?= $singleValue->date_of_expense; ?></td>
                                        <td><?= $singleValue->time_of_expense; ?></td>
                                        <td><?= $singleValue->amount; ?></td>
                                        <td><?= $singleValue->comment; ?></td>
                                        <td><a href="{{ asset('storage/'.str_replace('public/','',$singleValue->bill_photo)) }}" download><img src="{{ asset('storage/'.str_replace('public/','',$singleValue->bill_photo)) }}" height="50px;" width="50px;"></img></a></td>
                                        <td><a href="{{ asset('storage/'.str_replace('public/','',$singleValue->meter_reading_photo)) }}" download><img src="{{ asset('storage/'.str_replace('public/','',$singleValue->meter_reading_photo)) }}" height="50px;" width="50px;"></img></a></td>
                                        <td>
                                            @if($singleValue->first_approval_status=="Pending")
                                            <b class="text-warning">{{ $singleValue->first_approval_status }}</b>
                                            @elseif($singleValue->first_approval_status=="Approved")
                                            <b class="text-success">
                                                {{ $singleValue->first_approval_status }}

                                            </b>
                                            @elseif($singleValue->first_approval_status=="Rejected")
                                            <b class="text-danger">

                                                {{ $singleValue->first_approval_status }}

                                            </b>
                                            @endif
                                        </td>
                                        <td>
                                            @if($singleValue->second_approval_status=="Pending")
                                            <b class="text-warning">{{ $singleValue->second_approval_status }}</b>
                                            @elseif($singleValue->second_approval_status=="Approved")
                                            <b class="text-success">
                                                {{ $singleValue->second_approval_status }}

                                            </b>
                                            @elseif($singleValue->second_approval_status=="Rejected")
                                            <b class="text-danger">
                                                {{ $singleValue->second_approval_status }}

                                                @endif
                                        </td>
                                        <td>
                                            @if($singleValue->third_approval_status=="Pending")
                                            <b class="text-warning">{{ $singleValue->third_approval_status }}</b>
                                            @elseif($singleValue->third_approval_status=="Approved")
                                            <b class="text-success">
                                                {{ $singleValue->third_approval_status }}

                                            </b>
                                            @elseif($singleValue->third_approval_status=="Rejected")
                                            <b class="text-danger">
                                                {{ $singleValue->third_approval_status }}

                                            </b>
                                            @endif
                                        </td>

                                        <td>@if($singleValue->expense_status=="Pending")
                                            <b class="text-warning">{{ $singleValue->expense_status }}</b>
                                            @elseif($singleValue->expense_status=="Approved")
                                            <b class="text-success">{{ $singleValue->expense_status }}</b>
                                            @elseif($singleValue->expense_status=="Rejected")
                                            <b class="text-danger">{{ $singleValue->expense_status }}</b>
                                            @endif

                                        </td>
                                    </tr>
                                <?php }
                            } else {
                                ?>

                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!--row -->
        </div>
        <div id="expense_model" class="modal fade">
            <div class="modal-dialog modal-lg">
                <div class="modal-content" id="model_data">
                </div>
            </div>
        </div>
        @endsection
        @section('script')
        <script>
            $(document).ready(function() {
                $('#all_expense_list_table').DataTable({
                    "order": [
                        [4, "DESC"]
                    ],
                    dom: 'Bfrtip',
                    buttons: [
                        'csv', 'excel', 'pdf', 'print'
                    ]
                });
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
                var table = $('#expense_table').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    "order": [
                        [4, "DESC"]
                    ],
                    stateSave: true,
                    "ajax": {
                        url: "<?php echo route('admin.get_all_expense_list'); ?>",
                        type: "GET",
                    },
                    "columns": [{
                            "taregts": 0,
                            "render": function(data, type, row) {
                                return row.name;
                            }
                        },
                        {
                            "taregts": 1,
                            "render": function(data, type, row) {
                                return row.fuel_type;
                            }
                        },
                        {
                            "taregts": 2,
                            "render": function(data, type, row) {
                                return row.asset_name;
                            }
                        },
                        {
                            "taregts": 3,
                            "render": function(data, type, row) {
                                return row.asset_1;
                            }
                        },
                        {
                            "taregts": 4,
                            "render": function(data, type, row) {
                                return moment(row.date_of_expense).format("DD/MM/YYYY");
                            }
                        },
                        {
                            "taregts": 5,
                            "render": function(data, type, row) {
                                return row.time_of_expense;
                            }
                        },
                        {
                            "taregts": 6,
                            "render": function(data, type, row) {
                                return row.amount;
                            }
                        },
                        {
                            "taregts": 7,
                            "render": function(data, type, row) {
                                return row.comment;
                            }
                        },
                        {
                            "taregts": 8,
                            "searchable": false,
                            "orderable": false,
                            "render": function(data, type, row) {
                                if (row.bill_photo) {
                                    var file_path = row.bill_photo.replace("public/", "");
                                    var download_link = "{{ url('/storage/') }}" + '/' + file_path;
                                    return '<a download href="' + download_link + '" target="_blank"><img src="' + download_link + '" class="img-responsive" /></a>';
                                } else {
                                    return "NA";
                                }
                            }
                        },
                        {
                            "taregts": 9,
                            "searchable": false,
                            "orderable": false,
                            "render": function(data, type, row) {
                                if (row.meter_reading_photo) {
                                    var file_path = row.meter_reading_photo.replace("public/", "");
                                    var download_link = "{{ url('/storage/') }}" + '/' + file_path;
                                    return '<a download href="' + download_link + '" target="_blank"><img src="' + download_link + '" class="img-responsive" /></a>';
                                } else {
                                    return "NA";
                                }
                            }
                        },
                        {
                            "taregts": 10,
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

                                //return leaveStatus[row.leave_status];
                            }   
                        },
                        {
                            "taregts": 11,
                            "searchable": false,
                            "render": function(data, type, row) {

                                if (row.second_approval_status == 'Pending') {
                                    return '<b class="text-warning">Pending</b>';
                                } else if (row.second_approval_status == 'Approved') {
                                    return '<b class="text-success">Approved</b>';
                                } else if (row.second_approval_status == 'Canceled') {
                                    return '<b class="text-danger">Canceled</b>';
                                } else {
                                    return '<b class="text-danger">Rejected</b>';
                                }

                                //return leaveStatus[row.leave_status];
                            }
                        },
                        {
                            "taregts": 12,
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

                                //return leaveStatus[row.leave_status];
                            }
                        },
                        {
                            "taregts": 13,
                            "searchable": false,
                            "orderable": false,
                            "render": function(data, type, row) {
                                var status = 'Pending';
                                @if(Auth::user() -> role == config('constants.ACCOUNT_ROLE'))
                                status = row.first_approval_status;
                                @elseif(Auth::user() -> role == config('constants.Admin'))
                                status = row.second_approval_status;
                                @else
                                status = row.third_approval_status;
                                @endif

                                if (status == 'Pending') {
                                    return '<b class="text-warning">' + status + '</b>';
                                } else if (status == 'Approved') {
                                    return '<b class="text-success">' + status + '</b>';
                                } else {
                                    return '<b class="text-danger">' + status + '</b>';
                                }
                            }
                        },
                        {
                            "taregts": 14,
                            "searchable": false,
                            "orderable": false,
                            "render": function(data, type, row) {
                                var id = row.id;
                                return '<a href="#" data-href="<?php echo url('approve_expense') ?>' + '/' + id + '" onclick="change_confirm(this);" class="btn btn-success btn-rounded" title="Change Status"><i class="fa fa-check"></i></a>';
                            }
                        },
                        {
                            "taregts": 14,
                            "searchable": false,
                            "orderable": false,
                            "render": function(data, type, row) {
                                var id = row.id;
                                return '<a href="#expense_model" onclick="reject_expenses(&quot;<?php echo url('reject_expense') ?>' + '/' + id + '&quot;)" data-toggle="modal" class="btn btn-danger btn-rounded" title="Reject Expense"><i class="fa fa-times"></i></a>';
                            }
                        },
                    ]

                });
            });

            function change_confirm(e) {
                swal({
                    title: "Are you sure you want to approve?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false
                }, function() {
                    window.location.href = $(e).attr('data-href');
                });
            }

            function reject_expenses(route) {
                $('#model_data').html('');
                $.ajax({
                    url: route,
                    type: "GET",
                    dataType: "html",
                    catch: false,
                    success: function(data) {
                        $('#model_data').append(data);
                    }
                });
            }
        </script>
        @endsection