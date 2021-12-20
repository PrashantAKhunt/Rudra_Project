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
                <li><a href="{{ route($module_link) }}">{{ $module_title }}</a></li>
                <li><a href="#">{{ $page_title }}</a></li>
            </ol>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-md-12">
            <div class="white-box">
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
                        <form class="travelForm" action="{{ route('admin.update_travel_option') }}" id="travel_option" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="option_id" value="{{ $travel_option[0]->id }}" />
                            <input type="hidden" name="id" value="{{ $travel_option[0]->travel_id }}" />


                            <div class="row">
                                <div class="col-md-3">

                                    <label>File</label>
                                    <a title="view File" href="{{ asset('storage/'.str_replace('public/','',!empty($travel_option[0]->travel_image) ? $travel_option[0]->travel_image : 'public/no_image')) }}" download>View File</a>

                                    <input type="file" name="travel_image" class="form-control travel_image" />
                                </div>
                                <div class="col-md-3">
                                    <label>Amount</label>
                                    <input type="number" class="form-control amount" name="amount" value="{{ $travel_option[0]->amount }}" required />
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group ">
                                        <label>Travel Via</label>
                                        <select class="form-control select2" class="travel_via" name="travel_via" id="travel_via">
                                            @foreach($travel_via as $key => $value)
                                            <option <?php if ($key == $travel_option[0]->travel_via) { ?> selected <?php } ?> value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3" id="flights_list" style='display:none;'>
                                    <div class="form-group ">
                                        <label>Flights</label>
                                        <select class="form-control" name="flight_trip" id="flight_trip">
                                            <option value="0">--select--</option>
                                            @foreach($flight_trip as $key => $value)
                                            <option <?php if ($key == $travel_option[0]->flight_trip) { ?> selected <?php } ?> value="{{ $key }}">{{ $value }}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                </div>


                            </div>
                            @foreach($flight_info as $index => $info)
                            <div class="row travel_info div_count" id="child_div{{$index}}">
                                <div class="col-sm-3">
                                    <div class="form-group ">
                                        <label>From Location</label>
                                        <input type="text" name="from[{{ $index }}]" maxlength="255" value="{{ $info->from }}" class="form-control" required />
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group ">
                                        <label>To Location</label>
                                        <input type="text" name="to[{{ $index }}]" value="{{ $info->to }}" class="form-control" required/>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group ">
                                        <label>Departure</label>
                                        <input type="text" name="departure_datetime[{{ $index }}]" value="{{ date('d-m-Y H:i:s', strtotime($info->departure_datetime)) }}" class="form-control departure_datetime" required/>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group ">
                                        <label>Arrival</label>
                                        <input type="text" name="arrival_datetime[{{ $index }}]" value="{{ date('d-m-Y H:i:s', strtotime($info->arrival_datetime)) }}" class="form-control arrival_datetime" required/>
                                    </div>
                                </div>



                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group ">
                                            <label>Work Details</label>
                                            <textarea class="form-control" rows="3" name="details[{{ $index }}]" required>{{ $info->details }}</textarea>
                                        </div>
                                    </div>

                                </div>
                                <br>
                                <!-- <hr class="m-t-0 m-b-40"> -->
                            </div>
                            @endforeach
                            <div class="row" id="dynamic_div">


                            </div>

                            <button type="button" id="remove_btn" style="display: none;" title="Remove" class="btn btn-danger" onclick="remove_div();"><i class="fa fa-trash"> REMOVE</i></button>
                            <button type="button" id="add_div" style="display: none;" title="Add" class="btn btn-primary" onclick="add_new();"><i class="fa fa-plus"></i> ADD ANOTHER CITY</button>
                            <div class="clearfix"></div>
                            <br>



                            <div class="clearfix"></div>
                            <br>
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.travel_requests') }}'" class="btn btn-default">Cancel</button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<link href="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/build/css/bootstrap-datetimepicker.css" rel="stylesheet">
<script src="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/src/js/bootstrap-datetimepicker.js"></script>
<script>
    $('.departure_datetime').datetimepicker({
        format: 'DD-MM-YYYY HH:mm:ss'
    });
    $('.arrival_datetime').datetimepicker({
        format: 'DD-MM-YYYY HH:mm:ss'
    });


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

<style type="text/css">
    .padded {
        padding-top: 22px;
    }
</style>

<script>
    var my_array = <?php echo json_encode($flight_info); ?>;

    var loop_count = my_array.length - 1;


    function set_dynamiDiv(num) { //init div

        for (let i = 0; i < num; i++) {
            var html = '<div class="div_count travel_info set_div" id="info_div' + i + '"><hr class="m-t-0 m-b-40">' +
                '<div class="col-sm-3">' +
                '<div class="form-group ">' +
                '<label>From Location</label>' +
                '<input type="text" required name="from[' + i + ']" maxlength="255"  class="form-control" />' +
                '</div>' +
                '</div>' +
                '<div class="col-sm-3">' +
                ' <div class="form-group ">' +
                '<label>To Location</label>' +
                '<input type="text" required name="to[' + i + ']"  class="form-control" />' +
                '</div>' +
                '</div>' +
                '<div class="col-sm-3">' +
                '<div class="form-group ">' +
                '<label>Departure</label>' +
                '<input type="text" required name="departure_datetime[' + i + ']"class="form-control departure_datetime" />' +
                '</div>' +
                '</div>' +
                '<div class="col-sm-3">' +
                '<div class="form-group ">' +
                '<label>Arrival</label>' +
                '<input type="text" required name="arrival_datetime[' + i + ']" class="form-control arrival_datetime" />' +
                '</div>' +
                '</div>' +
                '<div class="row">' +
                '<div class="col-sm-6">' +
                '<div class="form-group ">' +
                '<label>Work Details</label>' +
                '<textarea class="form-control" required rows="3" name="details[' + i + ']"></textarea>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '</div>';

            $('#dynamic_div').append(html);
        };


        $('.departure_datetime').datetimepicker({
            format: 'DD-MM-YYYY HH:mm:ss'
        });
        $('.arrival_datetime').datetimepicker({
            format: 'DD-MM-YYYY HH:mm:ss'
        });


    }

    $('#travel_via').on('change', function() {

        document.getElementById('flight_trip').selectedIndex = 0;
        if (this.value == '4') {
            $("#flights_list").show();
        } else {
            $("div.travel_info").remove();
            set_dynamiDiv(1);
            loop_count = 1;
            //$('.set_div').remove();
            $('#remove_btn').hide();
            $("#add_div").hide();
            $("#flights_list").hide();
        }
    });

    /* New func */
    $('#flight_trip').on('change', function() {
        let type = $(this).val();
        loop_count = 1;
        $("div.travel_info").remove(); //all loop through div remove
        //$('.rm_div').remove();
        $('#add_div').hide();
        $('#remove_btn').hide();
        switch (type) {
            case 'round_trip':

                set_dynamiDiv(2);
                break;
            case 'multi_city':

                set_dynamiDiv(2);
                $("#add_div").show();
                break;
            default:

                set_dynamiDiv(1);
                break;
        }
    });
    /* --end-- */

    function remove_div() {

        let div_counts = $(".div_count").length;
        let count = div_counts - 1;

        $('#child_div' + count).remove();
        if (div_counts == 3) {

            $('#remove_btn').hide();

        }

        loop_count--;
    }


    function add_new() {

        loop_count++;

        var append_html = '<div class="div_count travel_info" id="child_div' + loop_count + '"><hr class="m-t-0 m-b-40">' +
            '<div class="col-sm-3">' +
            '<div class="form-group ">' +
            '<label>From Location</label>' +
            '<input type="text" required name="from[' + loop_count + ']" maxlength="255"  class="form-control" />' +
            '</div>' +
            '</div>' +
            '<div class="col-sm-3">' +
            ' <div class="form-group ">' +
            '<label>To Location</label>' +
            '<input type="text" required name="to[' + loop_count + ']"  class="form-control" />' +
            '</div>' +
            '</div>' +
            '<div class="col-sm-3">' +
            '<div class="form-group ">' +
            '<label>Departure</label>' +
            '<input type="text" required name="departure_datetime[' + loop_count + ']"class="form-control departure_datetime" />' +
            '</div>' +
            '</div>' +
            '<div class="col-sm-3">' +
            '<div class="form-group ">' +
            '<label>Arrival</label>' +
            '<input type="text" required name="arrival_datetime[' + loop_count + ']" class="form-control arrival_datetime" />' +
            '</div>' +
            '</div>' +
            '<div class="row">' +
            '<div class="col-sm-6">' +
            '<div class="form-group ">' +
            '<label>Work Details</label>' +
            '<textarea class="form-control" required rows="3" name="details[' + loop_count + ']"></textarea>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>';


        $('#dynamic_div').append(append_html);
        $('#remove_btn').show();

        $('.departure_datetime').datetimepicker({
            format: 'DD-MM-YYYY HH:mm:ss'
        });
        $('.arrival_datetime').datetimepicker({
            format: 'DD-MM-YYYY HH:mm:ss'
        });


    }
    var my_array = <?php echo json_encode($flight_info); ?>;

    var arr_length = my_array.length;



    $(document).ready(function() {

        if ($('#travel_via').val() == 4) {
            $("#flights_list").show();


            if ($('#flight_trip').val() == 'multi_city') {
                $('#add_div').show();
                if (arr_length > 2) {
                    $('#remove_btn').show();
                }

            }

        }


        $("#travel_via").change(function() {
            if ($(this).val() == 4) {
                $("#flights_list").show();
            } else {
                document.getElementById('flight_trip').selectedIndex = 0;
                $("#flights_list").hide();
            }
        });

    });
</script>




@endsection