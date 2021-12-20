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
                @if(Auth::user()->role!=1 && in_array(3,$role))
                <a href="{{ route('admin.add_travel') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Travel Request</a>
                <p class="text-muted m-b-30"></p>
                <br>
                @endif




                <div class="table-responsive">
                    <table id="travel_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Request No</th>
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
                                <td>
                                    {{ config::get('constants.TRAVEL_VIA')[$travel->travel_via] }}

                                </td>

                                <td>{{ date('d-m-Y h:i:s',strtotime($travel->departure_datetime)) }}</td>
                                <td>{{ date('d-m-Y h:i:s',strtotime($travel->arrival_datetime)) }}</td>
                                <td>{{ $travel->from }}</td>
                                <td>{{ $travel->to }}</td>
                                <td>
                                    @if($travel->status=="Pending")
                                    <span class="label label-rouded label-warning">{{ $travel->status }}</span>
                                    @elseif($travel->status=="Approved")
                                    <span class="label label-rouded label-info">{{ $travel->status }}</span>
                                    @elseif($travel->status=="Rejected")
                                    <span class="label label-rouded label-danger">{{ $travel->status }}</span>
                                    @elseif($travel->status=="Canceled")
                                    <span class="label label-rouded label-default">{{ $travel->status }}</span>
                                    @elseif($travel->status=="Processing")
                                    <span class="label label-rouded label-success">{{ $travel->status }}</span>
                                    @else
                                    <span class="label label-rouded label-primary">{{ $travel->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="#" onclick="get_travel_detail({{$travel->id}});" title="Travel Request Detail" data-target="#travel_view" data-toggle="modal" class="btn btn-primary btn-rounded"><i class="fa fa-eye"></i></a>
                                    @if(($travel->booked_by==Auth::user()->id && $travel->first_approval_status == 'Pending' && $travel->status == 'Pending') || ($travel->status == 'Processing'))
                                    <a href="{{ route('admin.edit_travel',['id'=>$travel->id]) }}" title="Edit Travel" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>
                                    <a href="{{ route('admin.cancel_travel',['id'=>$travel->id]) }}" title="Cancel Request" class="btn btn-danger btn-rounded"><i class="fa fa-trash"></i></a>
                                    @endif

                                    @if($travel->booked_by==Auth::user()->id && $travel->status == "Confirmed")
                                    <a href="#" onclick="confirm_travel_detail({{$travel->id}});" title="Booking Details" data-target="#confirm_travel_view" data-toggle="modal" class="btn btn-success btn-rounded"><i class="fa fa-eye"></i></a>
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
                            <div class="col-sm-4">
                                <div class="form-group ">
                                    <label>Travel Via</label>
                                    <p class="cn_travel_via"></p>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group ">
                                    <label>Amount</label>
                                    <p class="cn_amount"></p>
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

                    </div>
                </div>
            </div>
        </div>
        <!-- End this view -->
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
                        <br>
                        <div class="row">
                        <h4 class="modal-title">Reject Note (In case if expense rejected)</h4>
                            <div class="col-sm-12">
                                <div class="form-group ">
                                  <!--   <label>Reject Note (In case if expense rejected)</label> -->
                                    <p class="reject_note"></p>
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
            $('#travel_table').DataTable({
                stateSave: true
            });

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
                          $('.cn_file').empty();
                            $('.cn_travel_via').html(data.data.booked_data[0].travel_via);
                            $('.cn_amount').html(data.data.booked_data[0].amount);
    
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
                            $('.reject_note').html(data.data.travel_list.reject_details);

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
        </script>
        @endsection