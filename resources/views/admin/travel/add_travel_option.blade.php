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
                <li><a href="{{ route($module_link) }}">{{ $module_title }}</a></li>
                <li><a href="#">{{ $page_title }}</a></li>
            </ol>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-md-12">

            <div class="row">
                <div class="col-sm-12 col-xs-12">
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
                        <h4 class="page-title">Travel Options</h4>
                        <p class="text-muted m-b-30"></p>
                        <br>
                        <div class="table-responsive">
                            <table id="travel_table" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Travel Via</th>
                                        <th>Departure Time</th>
                                        <th>Arrival Time</th>

                                        <th>From</th>
                                        <th>To</th>
                                        <th>Amount</th>
                                        <th>File</th>
                                        <th>Details</th>
                                        <th width="150px" , data-orderable="false">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($travel_option->count()>0)
                                    @foreach($travel_option as $option)
                                    <tr>
                                        <td>
                                            {{ config::get('constants.TRAVEL_VIA')[$option->travel_via] }}
                                            @if($option->travel_via == 4)

                                            @if($option->flight_trip=="one_way")
                                            <p>One way</p>
                                            @elseif($option->flight_trip=="round_trip")
                                            <p>Round Trip</p>
                                            @else
                                            <p>Multi City</p>

                                            @endif
                                            @endif
                                        </td>
                                        <td>{{ date('d-m-Y h:i:s',strtotime($option->departure_datetime)) }}</td>
                                        <td>{{ date('d-m-Y h:i:s',strtotime($option->arrival_datetime)) }}</td>
                                        <td>{{ $option->from }}</td>
                                        <td>{{ $option->to }}</td>
                                        <td>{{ $option->amount }}</td>
                                        <td>
                                            <a title="Download" download href="<?php echo asset('storage/' . str_replace('public/', '', $option->travel_image)); ?>"><i class="fa fa-cloud-download"></i></a>
                                        </td>
                                        <td>{{ $option->details }}</td>
                                        <td>
                                            <a href="{{ route('admin.edit_travel_option',['id'=>$option->id]) }}" title="Edit Option" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="white-box">
                        <h4 class="page-title">Add New Options</h4>
                        <p class="text-muted m-b-30"></p>
                        <br>
                        <form action="{{ route('admin.insert_travel_option') }}" id="travel_option" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" id="id" name="id" value="{{ $travel->id }}" />

                            <div class="row">

                                <div class="col-md-3">

                                    <label>File</label>
                                    <input type="file" name="travel_image" class="form-control travel_image" required />
                                </div>
                                <div class="col-md-3">
                                    <label>Amount</label>
                                    <input type="number" class="form-control amount" name="amount" required />
                                </div>
                                <div class="col-md-3">
                                    <label>Travel Via</label>
                                    <select class="form-control" onchange="trvaelVia(this.value);" name="travel_via" required>
                                        <option value='' disabled selected>Please select</option>
                                        @foreach($travel_via as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>

                                </div>
                                <div class="col-md-3" id="flights_list" style='display:none;'>
                                    <div class="form-group ">
                                        <label>Flights</label>
                                        <select class="form-control" onchange="flightTrip(this.value);" name="flight_trip" id="flight_trip">
                                            <option value="0">--select--</option>
                                            <option value="one_way">One Way</option>
                                            <option value="round_trip">Round Trip</option>
                                            <option value="multi_city">Multi City</option>

                                        </select>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div class="row inner_div_count">
                                <div class="col-md-3">

                                    <label>Departure Time</label>
                                    <input type="text" class="form-control departure_datetime" name="departure_datetime[0]" required />
                                </div>
                                <div class="col-md-3">

                                    <label>Arrival Time</label>
                                    <input type="text" class="form-control arrival_datetime" name="arrival_datetime[0]" required />
                                </div>

                                <div class="col-md-3">
                                    <label>From Location</label>
                                    <input type="text" class="form-control from" name="from[0]" required />
                                </div>
                                <div class="col-md-3">

                                    <label>To Location</label>
                                    <input type="text" class="form-control to" name="to[0]" required />
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <label>Details</label>
                                        <textarea name="details[0]" class="form-control details" required></textarea>
                                    </div>

                                </div>
                            </div>
                            <br>
                            <div class="row" id="dynamic_div">


                            </div>
                            <button type="button" id="remove_InBtn" style="display: none;" title="Remove" class="btn btn-danger" onclick="removeInDiv();"><i class="fa fa-trash"> REMOVE</i></button>
                            <button type="button" id="add_InBtn" style="display: none;" title="Add" class="btn btn-primary" onclick="addInDiv();"><i class="fa fa-plus"></i> ADD ANOTHER CITY</button>
                            <div class="clearfix"></div>
                            <br>
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.travel_requests') }}'" class="btn btn-default">Cancel</button>
                        </form>
                    </div>
                </div>
                <input type="hidden" name="travel_div_count" id="travel_div_count" value="0" />

            </div>
        </div>
    </div>
    @endsection
    @section('script')
    <link href="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/build/css/bootstrap-datetimepicker.css" rel="stylesheet">
    <script src="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/src/js/bootstrap-datetimepicker.js"></script>
    <script>
        $('#travel_table').DataTable({
            stateSave: true
        });
        $('.departure_datetime').datetimepicker({
            format: 'DD-MM-YYYY HH:mm:ss'
        });
        $('.arrival_datetime').datetimepicker({
            format: 'DD-MM-YYYY HH:mm:ss'
        });
    </script>


    <script type="text/javascript">

    </script>


    <style type="text/css">
        .padded {
            padding-top: 22px;
        }
    </style>


    <script>
        var n = 0; //for outer add-remove more loop
        var m = 1; //for inner add-remove loop

        function set_dynamiDiv() {

            var html = '<div class="inner_div_count rm_div set_div"><hr class="m-t-0 m-b-40">' +
                '<div class="col-sm-3">' +
                '<div class="form-group ">' +
                '<label>Departure</label>' +
                '<input type="text" required name="departure_datetime[1]"class="form-control departure_datetime" />' +
                '</div>' +
                '</div>' +
                '<div class="col-sm-3">' +
                '<div class="form-group ">' +
                '<label>Arrival</label>' +
                '<input type="text" required name="arrival_datetime[1]" class="form-control arrival_datetime" />' +
                '</div>' +
                '</div>' +
                '<div class="col-sm-3">' +
                '<div class="form-group ">' +
                '<label>From Location</label>' +
                '<input type="text" required name="from[1]" maxlength="255"  class="form-control" />' +
                '</div>' +
                '</div>' +
                '<div class="col-sm-3">' +
                ' <div class="form-group ">' +
                '<label>To Location</label>' +
                '<input type="text" required name="to[1]"  class="form-control" />' +
                '</div>' +
                '</div>' +
                '<div class="row">' +
                '<div class="col-sm-6">' +
                '<div class="form-group ">' +
                '<label>Details</label>' +
                '<textarea class="form-control" required rows="3" name="details[1]"></textarea>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>';

            $('#dynamic_div').append(html);


            $('.departure_datetime').datetimepicker({
                format: 'DD-MM-YYYY HH:mm:ss'
            });
            $('.arrival_datetime').datetimepicker({
                format: 'DD-MM-YYYY HH:mm:ss'
            });


        }

        /* New func */
        function flightTrip(val) {


            let type = val;
            m = 1;
            $("div.rm_div").remove();
            $('.set_div').remove();
            $('#remove_InBtn').hide();
            $('#add_InBtn').hide();
            switch (type) {
                case 'round_trip':

                    set_dynamiDiv();
                    break;
                case 'multi_city':

                    set_dynamiDiv();
                    $("#add_InBtn").show();
                    break;
                default:

                    console.log('NOTHING');
                    break;
            }
        };
        /* --end-- */

        /* Inner loop div */

        /* END INNER LOOP DIV */

        function trvaelVia(val, index) {


            document.getElementById('flight_trip').selectedIndex = 0;
            var fl_id = "#flights_list";

            if (val == '4') {

                $(fl_id).show();
            } else {
                $("div.rm_div").remove();
                m = 1;
                $('.set_div').remove();
                $('#remove_InBtn').hide();
                $("#add_InBtn").hide();
                $(fl_id).hide();
            }
        };
    </script>


    <script>
        $('#travel_option').validate({
            ignore: [],
            rules: {

                'travel_via[]': {
                    required: true,
                },
                'travel_image[]': {
                    required: true
                },
                'departure_datetime[]': {
                    required: true
                },
                'arrival_datetime[]': {
                    required: true
                },
                'details[]': {
                    required: true

                },
                'amount[]': {
                    required: true,
                    number: true
                },
                'from[]': {
                    required: true
                },
                'to[]': {
                    required: true
                }

            }
        });
    </script>

    <script>
        function removeInDiv() {

            let div_counts = $(".inner_div_count").length;
            let count = div_counts - 1;

            $('#child_div' + count).remove();
            if (div_counts == 3) {

                var rm_btnId = "#remove_InBtn";
                $(rm_btnId).hide();

            }

            m--;
        }


        function addInDiv() {

            m++;

            var append_html = '<div class="inner_div_count rm_div" id="child_div' + m + '"><hr class="m-t-0 m-b-40">' +
                '<div class="col-sm-3">' +
                '<div class="form-group ">' +
                '<label>Departure</label>' +
                '<input type="text" required name="departure_datetime[' + m + ']"class="form-control departure_datetime" />' +
                '</div>' +
                '</div>' +
                '<div class="col-sm-3">' +
                '<div class="form-group ">' +
                '<label>Arrival</label>' +
                '<input type="text" required name="arrival_datetime[' + m + ']" class="form-control arrival_datetime" />' +
                '</div>' +
                '</div>' +
                '<div class="col-sm-3">' +
                '<div class="form-group ">' +
                '<label>From Location</label>' +
                '<input type="text" required name="from[' + m + ']" maxlength="255"  class="form-control" />' +
                '</div>' +
                '</div>' +
                '<div class="col-sm-3">' +
                ' <div class="form-group ">' +
                '<label>To Location</label>' +
                '<input type="text" required name="to[' + m + ']"  class="form-control" />' +
                '</div>' +
                '</div>' +
                '<div class="row">' +
                '<div class="col-sm-6">' +
                '<div class="form-group ">' +
                '<label>Details</label>' +
                '<textarea class="form-control" required rows="3" name="details[' + m + ']"></textarea>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>';


            $('#dynamic_div').append(append_html);
            $('#remove_InBtn').show();

            $('.departure_datetime').datetimepicker({
                format: 'DD-MM-YYYY HH:mm:ss'
            });
            $('.arrival_datetime').datetimepicker({
                format: 'DD-MM-YYYY HH:mm:ss'
            });


        }
    </script>
    @endsection