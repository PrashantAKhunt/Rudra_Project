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
                <?php if ($add_permission) { ?>
                    <a href="{{ route('admin.add_hardcopy') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Hardcopy</a>
                <?php } ?>
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="table-responsive">
                    <table id="softcopy_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Upload By</th>
                                <th>Company</th>
                                <th>Department</th>
                                <th>Cupboard</th>
                                <th>Custodian </th>
                                <th>Assignee</th>
                                <th>Rack Number</th>
                                <th>File Name(Number)</th>
                                <th>Pages Number</th>
                                <th>Type</th>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Document Status</th>
                                <th>View</th>
                                <th>Edit</th>
                               <!--  <th>Delete</th> -->
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div id="softcopy_model" class="modal fade">
            <div class="modal-dialog modal-lg">
                <div class="modal-content" id="softcopy_data">
                </div>
            </div>
        </div>
        @endsection
        @section('script')		
        <script>
            $(document).ready(function () {
                var table = $('#softcopy_table').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    //stateSave: true,
                    order:[[1,'DESC']],
                    "ajax": {
                        url: "<?php echo route('admin.get_hardcopy_list'); ?>",
                        type: "GET",
                    },
                    "columns": [
                        {"taregts": 0, "searchable": true, "render": function (data, type, row) {
                                return row.name;
                            }
                        },
                        {"taregts": 1, "searchable": true, "render": function (data, type, row) {
                                return row.company_name;
                            }
                        },
                        {"taregts": 2, "searchable": true, "render": function (data, type, row) {
                                return row.dept_name;
                            }
                        },
                        {"taregts": 3, "searchable": true, "render": function (data, type, row) {
                                return row.cupboard_number;
                            }
                        },
                        {"taregts": 4, "searchable": true, "render": function (data, type, row) {
                                return row.custodion_name;
                            }
                        },
                        {"taregts": 5, "searchable": true, "render": function (data, type, row) {
                                return row.assignee_name;
                            }
                        },
                        {"taregts": 6, "searchable": true, "render": function (data, type, row) {
                                return row.reck_number;
                            }
                        },
                        {"taregts": 7, "searchable": true, "render": function (data, type, row) {
                                return row.file_name + '('+ row.file_number + ')';
                            }
                        },
                        {"taregts": 8, "searchable": true, "render": function (data, type, row) {
                            var out = '';
                                if (row.start_page != null && row.start_page >0) {
                                      out += row.start_page + ' ' +  'to'  + ' ' + row.end_page;
                                }
                                return out;
                            }
                        },
                        {"taregts": 9, "searchable": true, "data": "type"},
                        {"taregts": 10, "searchable": true, "data": "title"},
                        {"taregts": 11,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out = '';
                                <?php if ($edit_permission) { ?>
                                    if (row.status == 'Enabled') {
                                        out += '<a href="<?php echo url('change_hardcopy_status') ?>' + '/' + id + '/Disabled' + '" class="btn btn-success" title="Change Status">' + row.status + '</a>';
                                    } else {
                                        out += '<a href="<?php echo url('change_hardcopy_status') ?>' + '/' + id + '/Enabled' + '" class="btn btn-danger" title="Change Status">' + row.status + '</a>';
                                    }
                                <?php } else { ?>
                                    if (row.status == 'Enabled') {
                                        out += '<b class="text-success">' + row.status + '</b>';
                                    } else {
                                        out += '<b class="text-danger">' + row.status + '</b>';
                                    }
                                <?php } ?>
                                return out;
                            }
                        },
                        {"taregts": 12, "searchable": true, "render": function (data, type, row) {
                            
                            if (row.assignee_status == 'Assigned') {
                                    return '<span class="label label-rouded label-warning">Assigned</span>';
                                } else if(row.assignee_status == 'Accepted'){
                                    return '<span class="label label-rouded label-success">Accepted</span>';
                                } else if(row.assignee_status == 'Rejected'){
                                    return '<span class="label label-rouded label-danger">Rejected</span>';
                                }  else {
                                    return '<span class="label label-rouded label-info">Return</span>';
                                }
                            }
                        },
                        {"taregts": 13, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                <?php if ($file_permission) { ?>
                                    return '<a href="#softcopy_model" onclick="view_softcopy(&quot;<?php echo url('get_hardcopy_file') ?>' + '/' + id + '&quot;)" data-toggle="modal" class="btn btn-danger btn-rounded" title="View Hardcopy"><i class="fa fa-eye"></i></a>&nbsp;';
                                <?php } else { ?>
                                    return '';
                                <?php } ?>
                            }
                        },
                        {"taregts": 14, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                <?php if ($edit_permission) { ?>
                                    return '<a href="<?php echo url("edit_hardcopy") ?>' + '/' + id + '" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>';
                                <?php } else { ?>
                                    return '';
                                <?php } ?>
                            }
                        }
                        /* {"taregts": 9, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                <?php if ($delete_permission) { ?>
                                    return '<a href="' + '/' + id + '" class="btn btn-primary btn-rounded"><i class="fa fa-times"></i></a>';
                                <?php } else { ?>
                                    return '';
                                <?php } ?>
                            }
                        }, */
                    ]
                });
            })

            function view_softcopy(route)
            {
                $.ajax({
                    url: route,
                    type: "GET",
                    dataType: "html",
                    catch : false,
                    success: function (data) {

                        $('#softcopy_data').empty();
                        $('#softcopy_data').append(data);
                    }
                });
            }

        </script>
        @endsection