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
                    <a href="{{ route('admin.add_holiday') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Holiday</a>
                <?php } ?>
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="table-responsive">
                    <table id="holiday_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Day</th>
                                <th>Date</th>
                                <th>Is Optional</th>
                                <th>Status</th>
                                <th>Edit</th>
                                <th>Delete</th>
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
</div>
</div> 
@endsection

@section('script')
<script>
    $(document).ready(function() {
        var table = $('#holiday_table').DataTable({
            "processing": true,
            "serverSide": true,
            "responsive": true,
            //stateSave: true,
            order: [
                [1, 'DESC']
            ],
            "ajax": {
                url: "<?php echo route('admin.get_holiday_list'); ?>",
                type: "GET",
            },
            "columns": [{
                    "target": 0,
                    "searchable": true,
                    "data": "title"
                },
                {
                    "target": 1,
                    "searchable": true,
                    "render": function(data, type, row) {
                        if (row.start_date == row.end_date) {
                            // return moment(row.start_date).format("DD-MM-YYYY");
                            return moment(row.start_date).format('dddd');
                        } else {
                            // return moment(row.start_date).format("DD-MM-YYYY") + " to " + moment(row.end_date).format("DD-MM-YYYY");
                            return moment(row.start_date).format('dddd') + " to " + moment(row.end_date).format('dddd');
                        }
                    }
                },
                {
                    "target": 2,
                    "searchable": true,
                    "render": function(data, type, row) {
                        if (row.start_date == row.end_date) {
                            return moment(row.start_date).format("DD-MM-YYYY");
                        } else {
                            return moment(row.start_date).format("DD-MM-YYYY") + " to " + moment(row.end_date).format("DD-MM-YYYY");
                        }
                    }
                },
                {
                    "target": 3,
                    "render": function(data, type, row) {
                        var id = row.id;
                        var out = '';
                        <?php if ($edit_permission) { ?>
                            if (row.is_optional == 2) {
                                out += '<span class="btn btn-success" title="Change Status">Yes</span>';
                            } else {
                                out += '<span class="btn btn-danger" title="Change Status">No</span>';
                            }
                        <?php } else { ?>
                            if (row.is_optional == 2) {
                                out += '<b class="text-success">Yes</b>';
                            } else {
                                out += '<b class="text-danger">No</b>';
                            }
                        <?php } ?>
                        return out;
                    }
                },
                {
                    "target": 4,
                    "render": function(data, type, row) {
                        var id = row.id;
                        var out = '';
                        <?php if ($edit_permission) { ?>
                            if (row.status == 'Enabled') {
                                out += '<a href="<?php echo url('change_holiday_status') ?>' + '/' + id + '/Disabled' + '" class="btn btn-success" title="Change Status">' + row.status + '</a>';
                            } else {
                                out += '<a href="<?php echo url('change_holiday_status') ?>' + '/' + id + '/Enabled' + '" class="btn btn-danger" title="Change Status">' + row.status + '</a>';
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
                {
                    "target": 5,
                    "searchable": false,
                    "orderable": false,
                    "render": function(data, type, row) {
                        var id = row.id;
                        <?php if ($edit_permission) { ?>
                            return '<a href="<?php echo url("edit_holiday") ?>' + '/' + id + '" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>';
                        <?php } else { ?>
                            return '';
                        <?php } ?>
                    }
                },
                {
                    "target": 6,
                    "searchable": false,
                    "orderable": false,
                    "render": function(data, type, row) {
                        var id = row.id;
                        <?php if ($delete_permission) { ?>
                            return '<a href="<?php echo url("delete_holiday") ?>' + '/' + id + '" class="btn btn-primary btn-rounded"><i class="fa fa-times"></i></a>';
                        <?php } else { ?>
                            return '';
                        <?php } ?>
                    }
                },
            ]
        });
    })
</script>
@endsection