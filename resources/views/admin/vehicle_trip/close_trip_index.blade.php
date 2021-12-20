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

        <div id="rejectModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">Vehicle Trip</h4>
                    </div>
                    <div class="modal-body" id="userTable">

                        <form action="{{ route('admin.reject_vehicle_trip') }}" id="reject_vehicle_trip" method="post">
                            @csrf
                            <input type="hidden" name="trip_id" id="trip_id">
                            <div class="form-group">
                                <label>Reject Note</label>
                                <textarea class="form-control" name="reject_note" id="reject_note" rows="3" cols="2"></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="button" onclick="RejectVehicleTrip('Rejected')" class="btn btn-danger">Reject Vehicle Trip</button>
                                <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>


        @endsection
        @section('script')
        <script>
            $(document).ready(function() {

                var table = $('#company_table').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    "order": [
                        [0, "DESC"]
                    ],
                    "ajax": {
                        url: "<?php echo route('admin.get_close_vehicle_trip_list'); ?>",
                        type: "GET",
                    },
                    "columns": [{
                            "taregts": 0,
                            'data': 'name'
                        },
                        {
                            "taregts": 1,
                            'data': 'trip_names'
                        },
                        {
                            "taregts": 2,
                            'data': 'Assetname'
                        },
                        {
                            "taregts": 3,
                            'data': 'trip_type'
                        },
                        {
                            "taregts": 4,
                            'data': 'note'
                        },
                        {
                            "taregts": 5,
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
                            "taregts": 6,
                            'data': 'opening_meter_reading'
                        },
                        {
                            "taregts": 7,
                            'data': 'closing_meter_reading'
                        },
                        {
                            "taregts": 8,
                            "render": function(data, type, row) {
                                var out = '';
                                if(row.closing_meter_reading >0 ){
                                    out = row.closing_meter_reading - row.opening_meter_reading;
                                }
                                return out;
                            }
                        },
                        {
                            "taregts": 9,
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
                            "taregts": 10,
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
                            "taregts": 11,
                            'data': 'opening_time'
                        },
                        {
                            "taregts": 12,
                            'data': 'closing_time'
                        },
                        {
                            "taregts": 13,
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
                            "taregts": 14,
                            "searchable": false,
                            "orderable": false,
                            "render": function(data, type, row) {
                                var id = row.id;
                                var out = "";
                                
                                if (row.opening_meter_reading_image) {

                                    let image = row.opening_meter_reading_image;
                                    let db_url = image.replace("public/", "");
                                    let url = "<?php echo url('/storage/'); ?>" + "/" + db_url;

                                    out += '<a target="_blank" class="btn btn-primary btn-rounded" title="Opening Meter Reading Image" href="' + url + '" ><i class="fa fa-eye"></i></a>';

                                }

                                if (row.closing_meter_reading_image) {

                                    let image = row.closing_meter_reading_image;
                                    let db_url = image.replace("public/", "");
                                    let url = "<?php echo url('/storage/'); ?>" + "/" + db_url;

                                    out += '&nbsp;<a target="_blank" class="btn btn-primary btn-rounded" title="Closing Meter Reading Image" href="' + url + '" ><i class="fa fa-eye"></i></a>';

                                }


                                if (row.status == "Pending" && row.is_closed == 'Yes') {
                                    out += '&nbsp;<a onclick="approve_vehicle_trip(this);" data-href="<?php echo url('approve_vehicle_trip') ?>' + '/' + id + '" class="btn btn-info btn-circle"><i class="fa fa-check"></i></a>';
                                    out += '&nbsp;<a data-toggle="modal" data-target="#rejectModal" onclick="reject_vehicle_trip(this);" class="btn btn-warning btn-circle" href="#" data-id=' + id + '><i class="fa fa-times"></i></a>';
                                }
                                return out;

                            }
                        },
                    ]

                });

            })

            function approve_vehicle_trip(e) {
                swal({
                    title: "Are you sure you want to Approved Vehicle trip ?",
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

            function reject_vehicle_trip(e) {
                $("#trip_id").val($(e).attr('data-id'));

            }

            function RejectVehicleTrip() {
                swal({
                    title: "Are you sure you want to Rejected Vehicle trip ?",
                    //text: "You want to change status of admin user.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: true
                }, function() {
                    $("#reject_vehicle_trip").submit();
                });
            }

            jQuery("#reject_vehicle_trip").validate({
                ignore: [],
                rules: {
                    reject_note: {
                        required: true,
                    }
                }
            });
        </script>
        @endsection