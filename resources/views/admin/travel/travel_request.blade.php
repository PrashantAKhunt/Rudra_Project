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



                <!-- <b class="error">Approval Flow: {{implode(' -> ',config('constants.TRAVEL_APPROVAL'))}}</b> -->
                <p class="text-muted m-b-30"></p>
                <br>

                <div class="table-responsive">
                    <table id="travel_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Request No</th>
                                <th>Booked By</th>
                                <th>Request Travel Via</th>

                                <th>Request Departure</th>
                                <th>Request Arrival</th>
                                <th>Request From</th>
                                <th>Request To</th>
                                <th>Request Status</th>
                                <th width="150px" , data-orderable="false">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($travel_list as $travel)
                            <tr>
                                <td>{{ $travel->request_no }}</td>
                                <td>{{ $travel->name }}</td>
                                <td>
                                    {{ config::get('constants.TRAVEL_VIA')[$travel->travel_via] }}
                                    @if($travel->travel_via == 4)

                                    @if($travel->flight_trip=="one_way")
                                    <p>One way</p>
                                    @elseif($travel->flight_trip=="round_trip")
                                    <p>Round Trip</p>
                                    @else
                                    <p>Multi City</p>

                                    @endif
                                    @endif
                                </td>

                                <td>{{ date('d-m-Y h:i:s',strtotime($travel->departure_datetime)) }}</td>
                                <td>{{ date('d-m-Y h:i:s',strtotime($travel->arrival_datetime)) }}</td>
                                <td>{{ $travel->from }}</td>
                                <td>{{ $travel->to }}</td>
                                <td>


                                    @if($travel->status=="Pending")
                                    <span class="label label-rouded label-warning">{{ $travel->status }}</span>
                                    @elseif($travel->status=="Processing")
                                    <span class="label label-rouded label-success">{{ $travel->status }}</span>
                                    @else
                                    <span class="label label-rouded label-info">{{ $travel->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="#" onclick="get_travel_detail({{$travel->id}});" title="Travel Request Detail" data-target="#travel_view" data-toggle="modal" class="btn btn-primary btn-rounded"><i class="fa fa-eye"></i></a>

                                    @if(config::get('constants.ASSISTANT') == Auth::user()->role && ($travel->status == 'Pending' || $travel->status == 'Processing'))
                                    <a href="{{ route('admin.add_travel_option',['id'=>$travel->id]) }}" title="Add Option" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>

                                    <a data-toggle="modal" title="Reject Travel Request" data-target="#Rejectmodel" onclick="set_travel_id('<?php echo $travel->id; ?>')" class="btn btn-danger btn-rounded"><i class="fa fa-times"></i></a>
                                    @endif

                                    @if(config::get('constants.SuperUser') == Auth::user()->role && $travel->status == "Processing")
                                    <a href="{{ route('admin.get_travel_options',['id'=>$travel->id]) }}" title="Travel Options" class="btn btn-primary btn-rounded"><i class="fa fa-pencil"></i></a>
                                    @endif

                                    @if(config::get('constants.ASSISTANT') == Auth::user()->role && $travel->status == "Approved")
                                    <a href="{{ route('admin.travel_booking',['id'=>$travel->id]) }}" title="Add Travel Booking" class="btn btn-primary btn-rounded"><i class="fa fa-check"></i></a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!--row -->
        </div>

        <!-- Reject Model -->
        <div id="Rejectmodel" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content" id="model_data">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h3 class="panel-title">Note: Please mention rejected reason for travel expense.</h3>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="{{ route('admin.reject_travel_request') }}" id="travel_request_form">
                            @csrf
                            <div class="row">
                                <div class="col-xs-12">
                                    <input type="hidden" name="travel_id" id="travel_id" value="">
                                    <label for="travel_request">Reject Note</label>

                                    <textarea name="reject_note" id="travel_request" value="" class="form-control" required></textarea>

                                    <label id="travel_request-error" class="error" for="travel_request"></label>
                                </div>
                                <div class="col-xs-2">

                                    <button type="submit" name="submit" class="btn btn-success btn-block">Submit</button>
                                </div>
                            </div>

                        </form>
                    </div>


                </div>
            </div>
        </div>
        <!-- End Reject Model -->
        <div class="modal fade" id="travel_view" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Travel Request Detail</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group ">
                                    <label>Travel User</label>
                                    <p class="traveler"></p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group ">
                                    <label>Travel Via</label>
                                    <p class="travel_via"></p>
                                </div>
                            </div>
                        </div>
                        <h4 class="modal-title">Travel Schedule</h4>
                        <br>

                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>FROM</th>
                                    <th>TO</th>
                                    <th>Departure Time</th>
                                    <th>Arrival Time</th>
                                    <th>Details</th>

                                </tr>
                            </thead>
                            <tbody id="tr_flights_info">

                            </tbody>
                        </table>
                  

                    </div>
                </div>
            </div>
        </div>

        <!-- New Table -->
        <div class="col-md-12 col-lg-12 col-sm-12">

            <div class="white-box">
                <div class="row bg-title">
                    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                        <h4 class="page-title">Travel Expenses History</h4>
                    </div>

                </div>
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="table-responsive">
                    <table id="all_travel_list" class="table table-striped">
                        <thead>
                            <tr>

                                <th>Request No</th>
                                <th>Company</th>
                                <th>Project</th>
                                <th>Other Project Detail</th>
                                <th>Booked By</th>
                                <th>Travelers</th>
                                <th>Travel Via</th>
                                <th>Work Details</th>
                                <th>Status</th>
                                <th data-orderable="false">View</th>

                            </tr>
                        </thead>
                        <tbody>
                            @if($all_travel_list->count()>0)
                            @foreach($all_travel_list as $travel_expense)
                            <tr>
                                <td>{{ $travel_expense->request_no }}</td>
                                <td>{{ $travel_expense->company_name }}</td>
                                <td>{{ $travel_expense->project_name }}</td>
                                <td>{{ $travel_expense->other_project_details }}</td>
                                <td>{{ $travel_expense->user_name }}</td>
                                <td>{{ $travel_expense->traveler_ids }}</td>
                                <td>
                                    @if($travel_expense->travel_via)
                                    {{ config::get('constants.TRAVEL_VIA')[$travel_expense->travel_via] }}
                                    @if($travel_expense->travel_via == 4)

                                    @if($travel_expense->flight_trip=="one_way")
                                    <p>One way</p>
                                    @elseif($travel_expense->flight_trip=="round_trip")
                                    <p>Round Trip</p>
                                    @else
                                    <p>Multi City</p>

                                    @endif
                                    @endif
                                    @endif

                                </td>
                                <td>{{ $travel_expense->work_details }}</td>
                            

                                <td>
                                    @if($travel_expense->status=="Pending")
                                    <span class="label label-rouded label-warning">{{ $travel_expense->status }}</span>
                                    @elseif($travel_expense->status=="Approved")
                                    <span class="label label-rouded label-info">{{ $travel_expense->status }}</span>
                                    @elseif($travel_expense->status=="Rejected")
                                    <span class="label label-rouded label-danger">{{ $travel_expense->status }}</span>
                                    @elseif($travel_expense->status=="Canceled")
                                    <span class="label label-rouded label-default">{{ $travel_expense->status }}</span>
                                    @elseif($travel_expense->status=="Processing")
                                    <span class="label label-rouded label-success">{{ $travel_expense->status }}</span>
                                    @else
                                    <span class="label label-rouded label-primary">{{ $travel_expense->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($travel_expense->status == "Confirmed")
                                    <a href="#" title="Booking Detail" onclick="confirm_travel_detail({{ $travel_expense->id}});" data-target="#confirm_travel_view" data-toggle="modal" class="btn btn-success btn-rounded"><i class="fa fa-eye"></i></a>
                                    @endif
                               
                                    @if($travel_expense->status == "Rejected")
                                    <a href="#" title="Reject Detail" onclick="reject_detail( '{{ $travel_expense->reject_details}}' );" data-target="#reject_details_modal" data-toggle="modal" class="btn btn-danger btn-rounded"><i class="fa fa-eye"></i></a>
                                   
                                    @endif
                                   
                                
                                </td>
                            </tr>
                            @endforeach
                            @endif


                        </tbody>
                    </table>
                </div>
            </div>
            <div id="reject_details_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">Reject Detail</h4>
                    </div>

                    <div class="modal-body" id="detail_div">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
            <!-- Confirm travel view -->
            <div class="modal fade" id="confirm_travel_view" role="dialog">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Travel Booking Detail</h4>
                        </div>
                        <div class="modal-body">
                        <div class="row">
                        <div class="col-sm-6">
                                <div class="form-group ">
                                    <label>Travel Via</label>
                                    <p class="cn_travel_via"></p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group ">
                                    <label>Amount</label>
                                    <p class="cn_amount"></p>
                                </div>
                            </div>
                        </div>
                            <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group ">
                                    <label>Payment Type</label>
                                    <p class="cn_payment_type"></p>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Card Details</label>
                                    <p class="cn_card_details"></p>
                                </div>
                            </div>

                        </div>

                            <h4 class="modal-title">Travel Schedule</h4>
                            <br>

                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>FROM</th>
                                        <th>TO</th>
                                        <th>Departure Time</th>
                                        <th>Arrival Time</th>
                                        <th>Details</th>

                                    </tr>
                                </thead>
                                <tbody id="flights_info">

                                </tbody>
                            </table>
                            <br>
                            <h4 class="modal-title">Booking Files</h4>
                            <br>
                            <hr class="m-t-0 m-b-40">
                            <div class="row cn_files">

                            </div>
                            <br>
                            <h4 class="modal-title">Approval Note</h4>
                            <div class="row">
                            <div class="col-sm-12">
                                
                                    <p class="approve_note"></p>
                                
                            </div>

                        </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End this view -->


        </div>
        <!-- end-New-Table -->

    </div>
    @endsection
    @section('script')
    <script>
        $('#travel_table').DataTable({
            stateSave: true
        });

        $('#all_travel_list').DataTable({
            stateSave: true
        });

        function set_travel_id(id) {
            $('#travel_id').val(id);
        }

        function get_travel_detail(id) {
            trHTML = '';
            $.ajax({
                url: "{{ route('admin.get_travel_detail') }}",
                type: "post",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id: id
                },
                success: function(data) {


                    if (data.status) {
                        $('.traveler').html(data.data.traveler_list);
                        $('.travel_via').html(data.data.travel_list.travel_via);
                        //$('.reject_note').html(data.data.travel_list.reject_details);
                        var filight_arr = data.data.travel_shedule[0].travel_info;

                        if (filight_arr.length == 0) {

                            $('#tr_flights_info').empty();
                            trHTML += '<span>No Records Found !</span>';
                            $('#tr_flights_info').append(trHTML);

                        } else {

                            $('#tr_flights_info').empty();

                            $.each(filight_arr, function(index, flight_obj) {


                                trHTML += '<tr>' +
                                    '<td>' + flight_obj.from + '</td>' +
                                    '<td>' + flight_obj.to + '</td>' +
                                    '<td>' + moment(flight_obj.departure_datetime).format("DD-MM-YYYY h:mm:ss a") + '</td>' +
                                    '<td>' + moment(flight_obj.arrival_datetime).format("DD-MM-YYYY h:mm:ss a") + '</td>' +
                                    '<td>' + flight_obj.details + '</td>' +
                                    '</tr>';


                            });
                            $('#tr_flights_info').append(trHTML);

                        }


                    }
                }
            });
        }

        function reject_detail(val){
            $('#detail_div').html(val);
        }

        function confirm_travel_detail(id) {
            var trHTML = '';
            $.ajax({
                url: "{{ route('admin.get_confirm_travel_detail') }}",
                type: "post",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id: id
                },
                success: function(data) {
                    if (data.status) {

                            $('.cn_travel_via').html(data.data.booked_data[0].travel_via);
                            $('.cn_amount').html(data.data.booked_data[0].amount);
                            $('.cn_payment_type').html(data.data.booked_data[0].payment_type);
                            if (data.data.booked_data[0].payment_type == 'Debit Card' || data.data.booked_data[0].payment_type == 'Credit Card') {
                                $('.cn_card_details').html(data.data.booked_data[0].card_number+'('+data.data.booked_data[0].name_on_card+')');
                            }
                            
                            $('.approve_note').html(data.data.booked_data[0].approval_note);


                        var filight_arr = data.data.booked_data[0].travel_info;
                   
                        if (filight_arr.length == 0) {
                            
                            $('#flights_info').empty();
                            trHTML += '<span>No Records Found !</span>';
                            $('#flights_info').append(trHTML);

                        } else {

                            $('#flights_info').empty();

                            $.each(filight_arr, function(index, flight_obj) {

                                trHTML += '<tr>' +
                                    '<td>' + flight_obj.from + '</td>' +
                                    '<td>' + flight_obj.to + '</td>' +
                                    '<td>' + moment(flight_obj.departure_datetime).format("DD-MM-YYYY h:mm:ss a") + '</td>' +
                                    '<td>' + moment(flight_obj.arrival_datetime).format("DD-MM-YYYY h:mm:ss a") + '</td>' +
                                    '<td>' + flight_obj.details + '</td>' +
                                    '</tr>';


                            });

                
                            $('#flights_info').append(trHTML);

                        }

                        var files_arr = data.data.booked_data[0].booking_files;


                        if (files_arr.length == 0) {

                            $('.cn_files').empty();
                            $('.cn_files').append('<span>No Files!</span>');

                        } else {

                            $('.cn_files').empty();
                            $.each(files_arr, function(index, file_obj) {

                                let append_html2 = '<div class="col-sm-2">' +

                                    '<a title="Download File" download href="' + file_obj.file_name + '"><i class="fa fa-cloud-download fa-lg"></i></a>' +
                                    '</div>';

                                $('.cn_files').append(append_html2);

                            });

                        }



                    }
                }
            })
        }
    </script>



    @endsection