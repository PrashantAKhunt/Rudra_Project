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
                <b class="error">Approval Flow: {{implode(' -> ',config('constants.VEHICLE_MAINTENANCE_APPROVAL'))}}</b>
                <p class="text-muted m-b-30"></p>
                <br>               
                <div class="table-responsive">
                    <table id="vehicle_maintenance_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>User Name</th>
                                <th>Company Name</th>
                                <th>Vehicle Name</th>
                                <th>Vehicle Number</th>
                                <th>Maintenance Type</th>
                                <th>Maintenance Details</th>
                                <th>Meter Reading Start</th>
                                <th>Meter Reading End</th>
                                <th>Service Center Name</th>
                                <th>Service Center Address</th>
                                <th>Amount</th>
                                <th>Maintenance Date</th>
                                <th>Return Date</th>
                                <th>First Approval</th>
                                <th>Second Approval</th>
                                <th>Final Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>                            
                        </tbody>
                    </table>
                </div>
            </div>  
            <!--            <div id="rejectPaymentModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                    </div>
                                    <div class="modal-body" id="userTable">
                                        <div class="form-group ">
                                            <label>Reject Note</label>
                                            <textarea class="form-control valid" rows="6" name="note" id="note" spellcheck="false"></textarea>
                                            <input type="hidden" name="reject_url" id="reject_url">
                                        </div>
                                    </div>
                                    <div class="col-md-12 pull-left">
                                        <div class="clearfix"></div>
                                        <br>
                                        <button type="button" onclick="RejectedPayment('Rejected')" data-dismiss="modal" class="btn btn-danger">Reject</button>
            
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                                
                            </div>
                           
                        </div>          -->
        </div>  
        <div class="col-md-12 col-lg-12 col-sm-12">


            <div id="rejectPaymentModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form action="{{ route('admin.reject_vehicle_maintenance') }}" method="POST" id="reject_note_frm">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            </div>
                            <div class="modal-body" id="userTable">
                                <div class="form-group ">
                                    <label>Reject Note</label>
                                    <input type="hidden" name="paymentid" id="paymentid" value="" />
                                    <textarea class="form-control valid" rows="6" name="note" id="note" spellcheck="false"></textarea>

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

            <div id="approvePaymentModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form action="{{ route('admin.approve_vehicle_maintenance') }}" method="POST" id="approve_note_frm">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            </div>
                            <div class="modal-body" id="userTable">
                                <div class="form-group ">
                                    <label>Approve Note</label>
                                    <input type="hidden" name="approve_paymentid" id="approve_paymentid" value="" />
                                    <!-- <textarea class="form-control valid" rows="6" name="approve_note" id="approve_note" spellcheck="false"></textarea> -->

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

            <div id="approval_note_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">

                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group ">
                                <label>First Approval Note</label>
                                <p id="approval_note1"></p>
                            </div>
                            <div class="form-group ">
                                <label>Second Approval Note</label>
                                <p id="approval_note2"></p>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                        </div>
                    </div>

                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>

            <div class="white-box">   
                <div class="row bg-title">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <h4 class="page-title">All Vehicle Approval History</h4>
                    </div>

                </div>		
                <p class="text-muted m-b-30"></p>
                <br>                
                <div class="table-responsive">
                    <table id="all_policy_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>User Name</th>
                                <th>Company Name</th>
                                <th>Vehicle Name</th>
                                <th>Vehicle Number</th>
                                <th>Maintenance Type</th>
                                <th>Maintenance Details</th>
                                <th>Meter Reading Start</th>
                                <th>Meter Reading End</th>
                                <th>Service Center Name</th>
                                <th>Service Center Address</th>
                                <th>Amount</th>
                                <th>Maintenance Date</th>
                                <th>Return Date</th>
                                <th>First Approval</th>
                                <th>Second Approval</th>
                                <th>Final Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($vehicle_maintenance_history)) { ?>
                                <?php foreach ($vehicle_maintenance_history as $key => $aprovals) { ?>
                                    <tr>

                                        <td>{{ $aprovals->user_name }}</td>
                                        <td>{{ $aprovals->company_name }}</td>
                                        <td>{{ $aprovals->asset_name }}</td>
                                        <td>{{ $aprovals->asset_1 }}</td>
                                        <td>{{ $aprovals->maintenance_type }}</td>
                                        <td>{{ $aprovals->description }}</td>
                                        <td>{{ $aprovals->start_meter_reading }}</td>
                                        <td>{{ $aprovals->received_meter_reading }}</td>
                                        <td>{{ $aprovals->service_center_name }}</td>
                                        <td>{{ $aprovals->service_center_address }}</td>
                                        <td>{{ $aprovals->amount }}</td>
                                        <td>{{ date('d-m-Y h:i:s',strtotime($aprovals->maintenance_date)) }}</td>
                                        <td>{{ date('d-m-Y h:i:s',strtotime($aprovals->received_date)) }}</td>
                                        <td>
                                            @if($aprovals->first_approval_status=="Pending")
                                            <b class="text-warning">{{ $aprovals->first_approval_status }}</b>
                                            @elseif($aprovals->first_approval_status=="Approved")
                                            <b class="text-success">
                                                {{ $aprovals->first_approval_status }}
                                                @if($aprovals->first_approval_date_time)
                                                {{ Carbon\Carbon::parse($aprovals->first_approval_date_time)->format('d-m-Y h:i:s A') }} 
                                                @endif
                                            </b>
                                            @elseif($aprovals->first_approval_status=="Rejected")
                                            <b class="text-danger">

                                                {{ $aprovals->first_approval_status }}

                                                @if($aprovals->first_approval_date_time)
                                                {{ Carbon\Carbon::parse($aprovals->first_approval_date_time)->format('d-m-Y h:i:s A') }} 
                                                @endif
                                            </b>
                                            @endif
                                        </td>

                                        <td>
                                            @if($aprovals->second_approval_status=="Pending")
                                            <b class="text-warning">{{ $aprovals->second_approval_status }}</b>
                                            @elseif($aprovals->second_approval_status=="Approved")
                                            <b class="text-success">
                                                {{ $aprovals->second_approval_status }}
                                                @if($aprovals->second_approval_date_time)
                                                {{ Carbon\Carbon::parse($aprovals->second_approval_date_time)->format('d-m-Y h:i:s A') }} 
                                                @endif
                                            </b>
                                            @elseif($aprovals->second_approval_status=="Rejected")
                                            <b class="text-danger">
                                                {{ $aprovals->second_approval_status }}
                                                @if($aprovals->second_approval_date_time)
                                                {{ Carbon\Carbon::parse($aprovals->second_approval_date_time)->format('d-m-Y h:i:s A') }} 
                                                @endif
                                            </b>
                                            @endif
                                        </td>

                                        <td>@if($aprovals->final_approval=="Pending")
                                            <b class="text-warning">{{ $aprovals->final_approval }}</b>
                                            @elseif($aprovals->final_approval=="Approved")
                                            <b class="text-success">{{ $aprovals->final_approval }}</b>
                                            @elseif($aprovals->final_approval=="Rejected")
                                            <input type="hidden" name="reject_note_{{$aprovals->id}}" id="reject_note_{{$aprovals->id}}" value="{{ $aprovals->reject_note }}" />
                                            <a class="btn btn-danger" href="#" onclick="show_reject_note({{$aprovals->id}})" data-toggle="modal" data-target="#reject_note_modal">{{ $aprovals->final_approval }}</a>
                                            @endif
                                        </td>
                                        <td><button class="btn btn-rounded btn-primary" onclick="get_vehicle_maintenance_files({{$aprovals->id}})" data-toggle="modal" data-target="#vehicleMaintenanaceFilesModel"><i class="fa fa-eye"></i></button></td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>

                        </tbody>
                    </table>
                </div>
            </div> 		
        </div> 

        <div id="reject_note_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">Reject Reason</h4>
                    </div>
                    <div class="modal-body" id="reject_note_div">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>

        <div id="vehicleMaintenanaceFilesModel" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">Files</h4>
                    </div>

                    <br>
                    <br>

                    <table  class="table table-striped table-bordered" >
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Maintenance(BEFORE/AFTER)</th>
                                
                            </tr>
                        </thead>
                        <tbody id="file_table">

                        </tbody>
                    </table>

                    <!-- <div class="modal-body" id="files">
                    </div> -->

                </div>
                <!-- /.modal-content -->
            </div>
        </div>
        @endsection

        @section('script')		
        <script>
            
            function get_vehicle_maintenance_files(id){
                $.ajax({
                    url:"{{ route('admin.get_vehicle_maintenanace_files') }}",
                    type:"POST",
                    dataType:"html",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:{"maintenanace_id":id},
                    success:function(data){
                        $('#file_table').html(data);
                    }
                })
            }

            $(document).ready(function(){
            $('#reject_note_frm').validate({
            rules:{
            note:{
            required:true
            }
            }
            });
            })
                    function show_reject_note(id) {
                    $('#reject_note_div').html($('#reject_note_' + id).val());
                    }
            $(document).ready(function () {


            var access_rule = '<?php echo $access_rule; ?>';
            access_rule = access_rule.split(',');
            var table = $('#vehicle_maintenance_table').DataTable({
            "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    "stateSave": true,
                    "ajax": {
                    url: "<?php echo route('admin.get_vehicle_maintenance_list_ajax'); ?>",
                            type: "GET",
                    },
                    "columns": [
                    {"taregts": 0, "searchable": true, "data": "user_name"},
                    {"taregts": 1, "searchable": true, "data": "company_name"},
                    {"taregts": 1, "searchable": true, "data": "vehicle_name"},
                    {"taregts": 1, "searchable": true, "data": "vehicle_number"},
                    {"taregts": 1, "searchable": true, "data": "maintenance_type"},
                    {"taregts": 1, "searchable": true, "data": "description"},
                    {"taregts": 1, "searchable": true, "data": "start_meter_reading"},
                    {"taregts": 1, "searchable": true, "data": "received_meter_reading"},
                    {"taregts": 1, "searchable": true, "data": "service_center_name"},
                    {"taregts": 1, "searchable": true, "data": "service_center_address"},
                    {"taregts": 1, "searchable": true, "data": "amount"},
                    {"taregts": 1, "searchable": true, "render":function(data, type, row){
                            return moment(row.maintenance_date).format('DD-MM-YYYY h:mm:ss');
                    }},
                    {"taregts": 1, "searchable": true, "render":function(data, type, row){
                            return moment(row.received_date).format('DD-MM-YYYY h:mm:ss');
                    }},
                    {"taregts": 10,
                            "render": function (data, type, row) {
                            var out = '';
                            if (row.first_approval_status == 'Pending')
                            {
                            return'<b class="text-warning">Pending</b>';
                            } else if (row.first_approval_status == 'Approved')
                            {
                            return '<b class="text-success">Approved</b>';
                            } else
                            {
                            return '<b class="text-danger">Rejected</b>';
                            }
                            }
                    },
                    {"taregts": 11,
                            "render": function (data, type, row) {
                            var out = '';
                            if (row.second_approval_status == 'Pending')
                            {
                            return'<b class="text-warning">Pending</b>';
                            } else if (row.second_approval_status == 'Approved')
                            {
                            return '<b class="text-success">Approved</b>';
                            } else
                            {
                            return '<b class="text-danger">Rejected</b>';
                            }
                            }
                    },
                    {"taregts": 13,
                            "render": function (data, type, row) {
                            var out = '';
                            if (row.final_approval == 'Pending')
                            {
                            return'<b class="text-warning">Pending</b>';
                            } else if (row.final_approval == 'Approved')
                            {
                            return '<b class="text-success">Approved</b>';
                            } else
                            {
                            return '<b class="text-danger">Rejected</b>';
                            }
                            }
                    },
                    {"taregts": 14, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                            var id = row.id;
                            var out = "";
                            var role = "<?php echo Auth::user()->role; ?>";
                            var SuperUser = "<?php echo config('constants.SuperUser'); ?>";
                            var AdminRole = "<?php echo config('constants.Admin'); ?>";
                            if (row.first_approval_status == "Pending" && role == AdminRole) {
                            out += ' <button type="button" data-toggle="modal" onclick=confirmPayment(' + row.id + ',"Approved") class="btn btn-info btn-circle"><i class="fa fa-check"></i> </button>';
                            out += ' <button type="button" data-target="#rejectPaymentModal" onclick=confirmPayment(' + row.id + ',"Reject") class="btn btn-warning btn-circle"><i class="fa fa-times"></i> </button>';
                            }

                            if (row.first_approval_status == "Approved" && row.second_approval_status == "Pending" && role == SuperUser) {
                            out += ' <button type="button" data-toggle="modal" onclick=confirmPayment(' + row.id + ',"Approved") class="btn btn-info btn-circle"><i class="fa fa-check"></i> </button>';
                            out += ' <button type="button" data-target="#rejectPaymentModal" onclick=confirmPayment(' + row.id + ',"Reject") class="btn btn-warning btn-circle"><i class="fa fa-times"></i> </button>';
                            }
                            out +='<button class="btn btn-circle btn-primary" onclick="get_vehicle_maintenance_files('+row.id+')" data-toggle="modal" data-target="#vehicleMaintenanaceFilesModel"><i class="fa fa-eye"></i></button>'
                            //out +='<button title="Approval Notes" type="button" onclick="get_approval_note('+row.id+')" data-toggle="modal" data-target="#approval_note_modal" class="btn btn-primary btn-rounded"><i class="fa fa-file-text"></i></button>';
                            return out;
                            }
                    }
                    ]
            });
            $('#all_policy_table').DataTable({
            dom: 'Bfrtip',
                    buttons: [
                            'csv', 'excel', 'pdf', 'print'
                    ]
            });
            });
            function openPolicy(pdf, id) {
            $('#tableBodyPolicy').empty();
            var iframeUrl = "<iframe src=" + pdf + "#toolbar=0 height='400' width='880'></iframe>";
            $('#tableBodyPolicy').append(iframeUrl);
            }

            function confirmPayment(url, status) {

            if (status == "Reject") {
            $("#reject_url").val(url);
            $("#paymentid").val(url);
            $("#rejectPaymentModal").modal('toggle'); //see here usage    
            } else {
            $('#approve_paymentid').val(url);
            swal({
            title: "Are you sure you want to confirm " + status + " Vehicle Maintenance ?",
                    //text: "You want to change status of admin user.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false
            }, function () {
            $("#approve_note_frm").submit();
            });
            }
            }



        </script>
        @endsection