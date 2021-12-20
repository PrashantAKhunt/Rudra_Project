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
                <?php
                $role = explode(',', $access_rule);
                ?>
                <?php
                if (in_array(3, $role)) {
                ?>
                    <a href="{{ route('admin.add_user') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Employee</a>
                    <a href="{{ route('admin.export_users') }}" class="btn btn-primary pull-left"><i class="fa fa-plus"></i> Export Data</a>
                <?php } ?>
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="table-responsive">
                    <table id="user_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Employee Code</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Personal Email</th>
                                <th>Joining Date</th>
                                <th>Department</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Suspended</th>
                                <th>Attendance Type</th>
                                <th>Register Date</th>
                                <th>On Probation?</th>
                                <th>Relieved</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
            <div id="relieverModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form action="{{ route('admin.reliever_user') }}" method="POST" id="reliever_user_form">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h2 class="panel-title">NOTE:</h2>
                            </div>
                            <div class="modal-body" id="userTable">
                                <div class="form-group ">
                                    
                                   <label>Relieved date</label>
                                    <input type="hidden" name="id" id="user_id" value="" />
                                    <input type="text" name="relieved_date" id="relieved_date" required />

                                </div>
                            </div>
                            <div class="col-md-12 pull-left">
                                <div class="clearfix"></div>
                                <br>
                                <button type="submit" class="btn btn-danger">Submit</button>

                            </div>
                            <div class="modal-footer">

                            </div>
                        </div>
                    </form>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <div id="attendanceTypeModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form action="{{ route('admin.change_attend_type') }}" method="POST" id="attend_type_form">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h2 class="panel-title">Attendance Type :</h2>
                            </div>
                            <div class="modal-body" id="typeTable">
                                <div class="form-group">                                    
                                    <input type="hidden" name="id" id="atted_user_id" value="" />
                                    <div class="radio-list">
                                        <label class="radio-inline p-0">
                                        <div class="radio radio-info">
                                            <input type="radio" name="attendance_type" id="Remote" value="Remote">
                                            <label for="Remote">Remote</label>
                                        </div>
                                        </label>
                                        <label class="radio-inline">
                                        <div class="radio radio-info">
                                            <input type="radio" name="attendance_type" id="Biometric" value="Biometric">
                                            <label for="Biometric">Biometric</label>
                                        </div>
                                        </label>
                                        <label class="radio-inline">
                                        <div class="radio radio-info">
                                            <input type="radio" name="attendance_type" id="Trip" value="Trip">
                                            <label for="Trip">Trip</label>
                                        </div>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 pull-left">
                                <div class="clearfix"></div>
                                <br>
                                <button type="submit" class="btn btn-danger">Submit</button>
                            </div>
                            <div class="modal-footer">
                            </div>
                        </div>
                    </form>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!--row -->
        </div>
        @endsection
        @section('script')
        <script>
            $(document).ready(function() {
                jQuery('#relieved_date').datepicker({
                    autoclose: true,
                    todayHighlight: true,
                    format: "dd-mm-yyyy"
                });

                var access_rule = '<?php echo $access_rule; ?>';
                access_rule = access_rule.split(',');

                var table = $('#user_table').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    stateSave: true,
                    "order": [
                        [3, "DESC"]
                    ],
                    "ajax": {
                        url: "<?php echo route('admin.get_user_list'); ?>",
                        type: "GET",
                    },
                    "columns": [{
                            "taregts": 0,
                            'data': 'emp_code'
                        },
                        {
                            "taregts": 1,
                            'data': 'name'
                        },
                        {
                            "taregts": 2,
                            'data': 'email'
                        },
                        {
                            "taregts": 3,
                            'data': 'personal_email'
                        },
                        {
                            "taregts": 4,
                            "render": function(data, type, row) {
                                    if(row.joining_date != "1970-01-01" && moment(row.joining_date).isValid()){
                                    return moment(row.joining_date).format("DD-MM-YYYY");
                                    }else{
                                        return '';
                                    }
                            }
                        },
                        {
                            "taregts": 5,
                            'data': 'dept_name'
                        },
                        {
                            "taregts": 6,
                            'data': 'role_name'
                        },
                        {
                            "taregts": 7,
                            "render": function(data, type, row) {
                                var id = row.id;
                                var out = '';
                                if (row.status == 'Enabled') {
                                    out += '<a href="<?php echo url('change_status') ?>' + '/' + id + '/Disabled' + '" class="btn btn-success" title="Change Status">' + row.status + '</a>';
                                } else {
                                    out += '<a href="<?php echo url('change_status') ?>' + '/' + id + '/Enabled' + '" class="btn btn-danger" title="Change Status">' + row.status + '</a>';
                                }
                                return out;
                            }
                        },
                        {
                            "taregts": 8,
                            "render": function(data, type, row) {
                                var id = row.id;
                                var out = '';
                                if (row.is_suspended == 'YES') {
                                    out += '<a href="<?php echo url('change_suspend_status') ?>' + '/' + id + '/NO' + '" class="btn btn-danger" title="Change Status">' + row.is_suspended + '</a>';
                                } else {
                                    out += '<a href="<?php echo url('change_suspend_status') ?>' + '/' + id + '/YES' + '" class="btn btn-success" title="Change Status">' + row.is_suspended + '</a>';
                                }
                                return out;
                            }
                        },
                        {
                            "taregts": 9,
                            "render": function(data, type, row) {
                                var id = row.id;
                                var out = '';
                                out += '<button type="button" data-target="#attendanceTypeModal" onclick=attendanceType(' + row.id + ',"'+ row.user_attend_type +'") class="btn btn-success" title="Change Status">'+ row.user_attend_type +'</button>';
                                return out;
                            }
                        },
                        {
                            "taregts": 10,
                            "render": function(data, type, row) {
                                return moment(row.created_at).format("DD-MM-YYYY");
                            }
                        },
                        {
                            "taregts": 11,
                            'data': 'is_on_probation'
                        },
                        {
                            "taregts": 12,
                            "render": function(data, type, row) {
                                var id = row.id;
                                var out = '';
                                if (row.is_user_relieved == 0) {
                                    out += '<button type="button" data-target="#relieverModal" onclick=relieveUser(' + row.id + ',"YES") class="btn btn-success">NO</button>';
                                } else {
                                    out += '<button type="button" onclick=relieveUser(' + row.id + ',"NO") class="btn btn-danger">YES</button>';
                                    out += moment(row.relieved_date).format("DD-MM-YYYY");
                                }
                                return out;
                            }
                        },
                        {
                            "taregts": 13,
                            "searchable": false,
                            "orderable": false,
                            "render": function(data, type, row) {
                                var id = row.id;
                                var out = "";
                                if ($.inArray('2', access_rule) !== -1) {
                                    out = '<a href="<?php echo url('edit_user') ?>' + '/' + id + '" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>';
                                }
                                if ($.inArray('1', access_rule) !== -1) {
                                    out += '&nbsp;&nbsp;<a href="<?php echo url('view_user') ?>' + '/' + id + '" class="btn btn-primary btn-rounded"><i class="fa fa-eye"></i></a>';
                                }
                                if ($.inArray('4', access_rule) !== -1) {
                                    /*out += '&nbsp;<a onclick="delete_confirm(this);" class="btn btn-danger btn-rounded" href="#" data-href=\'<?php echo url('delete_user'); ?>/' + id + '\'\n\
                                        title="Delete"><i class="fa fa-trash"></i></a>';*/
                                }
                                out += '<a href="<?php echo url('loan_statement') ?>' + '/' + id + '" class="btn btn-primary btn-rounded" title="Loan statement"><i class="fa fa-money" aria-hidden="true"></i></a>';
                                return out;
                            }
                        },

                    ]
                });
            })

            function delete_confirm(e) {
                swal({
                    title: "Are you sure you want to delete user ?",
                    //text: "You want to change status of admin user.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false
                }, function() {
                    window.location.href = $(e).attr('data-href');
                });
            }
        </script>
        <script>        
            function relieveUser(id, status) {
                if (status == "YES") {
                    $("#user_id").val(id);
                    $("#relieverModal").modal('toggle'); //see here usage    
                } else {
                    swal({
                        title: "Are you sure you want to back user ?",
                        //text: "You want to change status of admin user.",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes",
                        closeOnConfirm: true
                    }, function() {
                        $.ajax({
                            url: "{{ route('admin.reliever_user') }}",
                            type: "post",
                            dataType: "json",
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            data: {
                                id: id,
                                status: status
                            },
                            success: function(data) {
                                if (data.status) {
                                    location.reload();
                                }
                            }
                        });
                        //e.preventDefault();
                    });
                }
            }
            function attendanceType(id, atteType) {
                $("#atted_user_id").val(id);
                $("#"+atteType).prop('checked', true);
                $("#attendanceTypeModal").modal('toggle');                
            }
        </script>
        @endsection