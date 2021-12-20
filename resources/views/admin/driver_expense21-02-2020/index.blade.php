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
                <a href="{{ route('admin.add_expense') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Driver Expense</a>
                <p class="text-muted m-b-30"></p>
                <br>
                <b class="error">Approval Flow: {{implode(' -> ',config('constants.DRIVER_EXPENSE_APPROVAL'))}}</b>
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="table-responsive">
                    <table id="expense_table" class="table table-striped">
                        <thead>
                            <tr>                                
                                <th>FUEL TYPE</th>
                                <th>VEHICLE </th>
                                <th>VEHICLE NUMBER </th>
                                <th>EXPENSE DATE</th>
                                <th>EXPENSE TIME</th>
                                <th>AMOUNT</th>
                                <th>COMMENT</th>
                                <th>ACCOUNTANT STATUS</th>
                                <th>ADMIN STATUS</th>
                                <th>SUPER ADMIN STATUS</th>
                                <th>STATUS</th>								
                                <th>ACTION</th>
                            </tr>
                        </thead>
                        <tbody>                            
                        </tbody>
                    </table>
                </div>
            </div>
            <!--row -->
        </div>        		
        @endsection
		
		
		
        @section('script')		
        <script>
            $(document).ready(function () {

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
                    "order": [[3, "DESC"]],
                    stateSave: true,
                    "ajax": {
                        url: "<?php echo route('admin.get_my_expense_list'); ?>",
                        type: "GET",
                    },
                    "columns": [
                        {"taregts": 0, "render": function (data, type, row) {
                                return row.fuel_type;
                            }
                        },
                        {"taregts": 1, "render": function (data, type, row) {
                                return row.name;
                            }
                        },
                        {"taregts": 2, "render": function (data, type, row) {
                                return row.asset_1;
                            }
                        },
                        {"taregts": 3, "render": function (data, type, row) {
                                return moment(row.date_of_expense).format("DD/MM/YYYY");
                            }
                        },
                        {"taregts": 4, "render": function (data, type, row) {
                                return row.time_of_expense;
                            }
                        },
                        {"taregts": 5, "render": function (data, type, row) {
                                return row.amount;
                            }
                        },
                        {"taregts": 6, "render": function (data, type, row) {
                                return row.comment;
                            }
                        },
                       
                        {
                            "taregts": 7,
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
                            "taregts": 8,
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
                            "taregts": 9,
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
                        {"taregts": 10, "searchable": false, "render": function (data, type, row) {
                                if (row.status == 'Pending') {
                                    return '<b class="text-warning">Pending</b>';
                                } else if (row.status == 'Approved') {
                                    return '<b class="text-success">Approved</b>';
                                } else {
                                    return '<b class="text-danger">Rejected</b>';
                                }
                            }
                        },
                        {"taregts": 11, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var html = '<a href="<?php echo url("edit_expense") ?>' + '/' + id + '" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a> &nbsp;';
                                html += '<a onclick="change_confirm(this);" href="#" data-href="<?php echo url("delete_driver_expense") ?>' + '/' + id + '" class="btn btn-danger btn-rounded"><i class="fa fa-trash"></i></a>';
                                return html;
                            }
                        }
                    ]

                });
            })
            function change_confirm(e) {
                swal({
                    title: "Are you sure you want to delete this record?",
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
