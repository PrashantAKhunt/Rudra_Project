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

                use Illuminate\Support\Facades\Auth;
                $common_task = new \App\Lib\CommonTask();
                $driver_ids = $common_task->get_trip_management();
                if ($is_any_trip_open == 'no') {
                ?>
                    <?php
                    $role = explode(',', $access_rule);
                    if (in_array(Auth::user()->id,$driver_ids)) {
                    ?>
                        <a href="{{ route('admin.add_vehicle_trip') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Vehicle Trip</a>
                <?php
                    }
                }
                ?>
                <p class="text-muted m-b-30"></p>
                <br>

                <div class="table-responsive">
                    <table id="company_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Driver Name</th>
                                <th>Employee Name</th>
                                <th>Asset</th>
                                <th>TripType</th>
                                <th>Note</th>
                                <th>Status</th>
                                <th>Opening Meter Reading</th>
                                <th>Closing Meter Reading</th>
                                <th>Actual Km</th>
                                <th>From Location</th>
                                <th>To Location</th>
                                <th>Opening Time</th>
                                <th>Closing Time</th>
                                <th>Is Closed</th>
                                <th>Action</th>
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
            $(document).ready(function() {
                var access_rule = '<?php echo $access_rule; ?>';
                access_rule = access_rule.split(',');
                var table = $('#company_table').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    "order": [
                        [1, "DESC"]
                    ],
                    stateSave: true,
                    "ajax": {
                        url: "<?php echo route('admin.get_vehicle_trip_list'); ?>",
                        type: "GET",
                    },
                    "columns": [{
                            "taregts": 1,
                            'data': 'name'
                        },
                        {
                            "taregts": 2,
                            'data': 'trip_names'
                        },
                        {
                            "taregts": 3,
                            'data': 'Assetname'
                        },
                        {
                            "taregts": 4,
                            'data': 'trip_type'
                        },
                        {
                            "taregts": 5,
                            'data': 'note'
                        },
                        {
                            "taregts": 6,
                            "render": function(data, type, row) {
                                var out = '';
                                if (row.status == 'Pending') {
                                    return '<b class="text-warning">Pending</b>';
                                } else if (row.status == 'Approved') {
                                    return '<b class="text-success">Approved</b>';
                                } else {
                                    return '<b class="text-danger">Rejected</b>';
                                }
                            }
                        },
                        {
                            "taregts": 7,
                            'data': 'opening_meter_reading'
                        },
                        {
                            "taregts": 8,
                            'data': 'closing_meter_reading'
                        },
                        {
                            "taregts": 9,
                            "render": function(data,type,row){
                            out = '';
                                if(row.closing_meter_reading > 0){
                                   out = row.closing_meter_reading - row.opening_meter_reading ;
                                }
                            return out;
                            }
                        },
                        {
                            "taregts": 10,
                            "render": function(data,type,row){
                                var out = '';

                                if(row.from_location){
                                        out += row.from_location;
                                    }else{
                                        out += "N/A";
                                    }
                                    return out;

                            }
                        },
                        {
                            "taregts": 11,
                            "render": function(data,type,row){
                                var out = '';

                                if(row.to_location){
                                        out += row.to_location;
                                    }else{
                                        out += "N/A";
                                    }
                                    return out;

                            }
                        },
                        {
                            "taregts": 12,
                            'data': 'opening_time'
                        },
                        {
                            "taregts": 13,
                            'data': 'closing_time'
                        },
                        {
                            "taregts": 14,
                            "render": function(data, type, row) {
                                var out = '';
                                if (row.is_closed == 'No') {
                                    return '<b class="text-warning">No</b>';
                                } else {
                                    return '<b class="text-success">Yes</b>';
                                }
                            }
                        },

                        {
                            "taregts": 15,
                            "searchable": false,
                            "orderable": false,
                            "render": function(data, type, row) {
                                var id = row.id;
                                var out = "";
                                if (row.is_closed == 'No') {
                                    if (($.inArray('2', access_rule) !== -1)) {
                                        out = '<a href="<?php echo url('edit_vehicle_trip') ?>' + '/' + id + '" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>';
                                    }
                                }
                                if (row.opening_meter_reading_image) {

                                    let image = row.opening_meter_reading_image;
                                    let db_url = image.replace("public/", "");
                                    let url = "<?php echo url('/storage/'); ?>" + "/" + db_url;

                                    out += '<a target="_blank" class="btn btn-primary btn-rounded" title="Opening Meter Reading Image" href="' + url + '" ><i class="fa fa-eye"></i></a>';

                                }
                                
                                if (row.closing_meter_reading_image ) {
                                   
                                    let image = row.closing_meter_reading_image;
                                    let db_url = image.replace("public/", "");
                                    let url = "<?php echo url('/storage/'); ?>" + "/" + db_url;

                                    out += '<a target="_blank" class="btn btn-primary btn-rounded" title="Closing Meter Reading Image " href="' + url + '" ><i class="fa fa-eye"></i></a>';

                                }
                                // out += '&nbsp;<a onclick="delete_confirm(this);" class="btn btn-danger btn-rounded" href="#" data-href=\'<?php echo url('delete_vehicle_trip'); ?>/' + id + '\'\n\
                               //         title="Delete"><i class="fa fa-trash"></i></a>';
                                return out;
                            }
                        },
                    ]

                });

            })

            function delete_confirm(e) {
                swal({
                    title: "Are you sure you want to delete Vehicle trip ?",
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
        @endsection