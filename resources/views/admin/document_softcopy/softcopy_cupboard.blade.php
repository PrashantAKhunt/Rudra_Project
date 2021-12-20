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
                    <a href="{{ route('admin.add_hardcopy_cupboard') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Hardcopy Cupboard</a>
                <?php } ?>
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="table-responsive">
                    <table id="softcopy_cupboard_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Company</th>
                                <th>Department</th>
                                <th>Cupboard Number</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Edit</th>
                                <!-- <th>Delete</th> -->
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endsection
        @section('script')		
        <script>
            $(document).ready(function () {
                var table = $('#softcopy_cupboard_table').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    //stateSave: true,
                    order:[[1,'DESC']],
                    "ajax": {
                        url: "<?php echo route('admin.get_hardcopy_cupboard_list'); ?>",
                        type: "GET",
                    },
                    "columns": [
                        {"taregts": 0, "searchable": true, "render": function (data, type, row) {
                                return row.company_name;
                            }
                        },
                        {"taregts": 1, "searchable": true, "render": function (data, type, row) {
                                return row.dept_name;
                            }
                        },
                        {"taregts": 2, "searchable": true, "data": "cupboard_number"},
                        {"taregts": 3, "searchable": true, "data": "description"},
                        {"taregts": 4,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out = '';
                                <?php if ($edit_permission) { ?>
                                    if (row.status == 'Enabled') {
                                        out += '<a href="<?php echo url('change_hardcopy_cupboard_status') ?>' + '/' + id + '/Disabled' + '" class="btn btn-success" title="Change Status">' + row.status + '</a>';
                                    } else {
                                        out += '<a href="<?php echo url('change_hardcopy_cupboard_status') ?>' + '/' + id + '/Enabled' + '" class="btn btn-danger" title="Change Status">' + row.status + '</a>';
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
                        {"taregts": 5, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                <?php if ($edit_permission) { ?>
                                    return '<a href="<?php echo url("edit_hardcopy_cupboard") ?>' + '/' + id + '" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>';
                                <?php } else { ?>
                                    return '';
                                <?php } ?>
                            }
                        }
                        /* {"taregts": 6, "searchable": false, "orderable": false,
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
        </script>
        @endsection