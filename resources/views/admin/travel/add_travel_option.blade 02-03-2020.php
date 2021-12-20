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
                        <form action="{{ route('admin.insert_travel_option') }}" id="travel_option" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" id="id" name="id" value="{{ $travel->id }}" />
                            <div class="travel_box">
                                <div class="row div_count travel_list0">
                                    <div class="row">

                                        <div class="col-md-3">

                                            <label>File</label>
                                            <input type="file" name="travel_image[0]" class="form-control travel_image" required />
                                        </div>
                                        <div class="col-md-3">
                                            <label>Amount</label>
                                            <input type="number" class="form-control amount" name="amount[0]" required />
                                        </div>
                                        <div class="col-md-3">
                                            <label>Travel Via</label>
                                            <select class="form-control" onchange="trvaelVia(this.value, 0);" name="travel_via[0]" required>
                                                <option value='' disabled selected>Please select</option>
                                                @foreach($travel_via as $key => $value)

                                                <option value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                            </select>

                                        </div>
                                        <div class="col-md-3" id="flights_list_0" style='display:none;'>
                                            <div class="form-group ">
                                                <label>Flights</label>
                                                <select class="form-control" onchange="flightTrip(this.value, 0);" name="flight_trip" id="flight_trip">
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
                                            <div class="col-md-10">
                                                <label>Details</label>
                                                <textarea name="details[0]" class="form-control details" required></textarea>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="row" id="dynamic_div0">


                                    </div>
                                    <button type="button" id="remove_InBtn0" style="display: none;" title="Remove" class="btn btn-danger" onclick="removeInDiv(0);"><i class="fa fa-trash"> REMOVE</i></button>
                                    <button type="button" id="add_InBtn0" style="display: none;" title="Add" class="btn btn-primary" onclick="addInDiv();"><i class="fa fa-plus"></i> ADD ANOTHER CITY</button>
                                    <!-- <div class="clearfix"></div> -->
                                    <br>

                                </div>

                            </div>
                            <button type="button" title="Remove" id="remove-btn" style="display: none;" class="btn btn-danger" onclick="remove_div();"><i class="fa fa-trash"></i></button>

                            <button type="button" title="Add" class="btn btn-primary" onclick="add_new();"><i class="fa fa-plus"></i></button>
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

     alert('this');
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
    function flightTrip(val, index) {
        
        
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
        var fl_id = "#flights_list_" + index;

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

    function remove_div() {

        let div_counts = $(".div_count").length;
        let count = div_counts - 1;
        alert(div_counts);
        $('#travel_list' + count).remove();
        if (div_counts == 2) {

            $('#remove-btn').hide();

        }
        n--;
    }

    function add_new() {

        n++;

       alert(n);
        var append_html = '<div class="div_count" id="travel_list' + n + '">' +

            ' <div class="row">' +
            ' <div class="col-md-3">' +
            ' <div class="form-group ">' +
            '<label>File</label>' +
            '<input class="form-control trvael_image" type="file" required  name="travel_image[' + n + ']" value=""/>' +
            '</div>' +
            '</div>' +
            ' <div class="col-md-3">' +
            '<div class="form-group ">' +
            '<label>Amount</label>' +
            '<input type="number" required name="amount[' + n + ']" class="form-control amount" />' +
            '  </div>' +
            '</div>' +
            '   <div class="col-md-3">' +
            '  <div class="form-group ">' +
            '     <label>Travel Via</label>' +
            '     <select class="form-control travel_via" onchange="trvaelVia(this.value,' + n + ');" name="travel_via[' + n + ']" required>' +
            '<option value=" " disabled selected>Please select</option>' +
            ' <option value="1">Company Car</option> ' +
            '<option value="2">Bus</option> ' +
            '<option value="3">Train</option> ' +
            '<option value="4">Flight</option> ' +
            '<option value="5">Local</option> ' +
            '<option value="6">Private</option> ' +
            '</select>' +
            '  </div>' +
            ' </div>' +
            '<div class="col-md-3" id="flights_list_' + n + '" style="display:none;>' +
            '<div class="form-group ">' +
            '<label>Flights</label>' +
            '<select class="form-control" name="flight_trip[' + n + ']" id="flight_trip">' +
            '<option value="0">--select--</option>' +
            '<option value="one_way">One Way</option>' +
            '<option value="round_trip">Round Trip</option>' +
            '<option value="multi_city">Multi City</option>' +
            '</select>' +
            '</div>' +
            '</div>' +
            /* '  </div>' + */
            '  <div class="row">' +
            '    <div class="col-md-3">' +
            ' <div class="form-group ">' +
            '  <label>Departure Time</label>' +
            '  <input type="text" required class="form-control departure_datetime" name="departure_datetime[' + n + ']" />' +
            '  </div>' +
            ' </div>' +
            '    <div class="col-md-3">' +
            ' <div class="form-group ">' +
            '  <label>Arrival Time</label>' +
            '  <input type="text" required class="form-control arrival_datetime" name="arrival_datetime[' + n + ']" />' +
            '  </div>' +
            ' </div>' +
            ' <div class="col-md-3">' +
            '<div class="form-group ">' +
            '<label>From Location</label>' +
            '<input type="text" required name="from[' + n + ']" class="form-control from" />' +
            '  </div>' +
            '</div>' +
            ' <div class="col-md-3">' +
            ' <div class="form-group ">' +
            '<label>To Location</label>' +
            '<input class="form-control to" type="text" required  name="to[' + n + ']" value=""/>' +
            '</div>' +
            '</div>' +
            '<div class="row">' +
            '<div class="col-md-10">' +
            '<div class="form-group ">' +
            '<label>Details</label>' +
            '<textarea  name="details[' + n + ']" class="form-control details" required>' +
            '</textarea>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>'+
            '<div class="row" id="dynamic_div'+ n +'">'+
            
            '</div>'+
            '<button type="button" id="remove_InBtn'+ n +'" style="display: none;" title="Remove" class="btn btn-danger" onclick="removeInDiv('+ n +');"><i class="fa fa-trash"> REMOVE</i></button>'+
            '<button type="button" id="add_InBtn'+ n +'" style="display: none;" title="Add" class="btn btn-primary" onclick="addInDiv();"><i class="fa fa-plus"></i> ADD ANOTHER CITY</button>'+
            '<br>'
            '<hr class="m-t-0 m-b-40"></div></div>';


        $('.travel_box').append(append_html);

        $('#remove-btn').show();

        $('.departure_datetime').datetimepicker({
            format: 'DD-MM-YYYY HH:mm:ss'
        });
        $('.arrival_datetime').datetimepicker({
            format: 'DD-MM-YYYY HH:mm:ss'
        });


    }
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
    function removeInDiv(rm) {

        let div_counts = $(".inner_div_count").length;
        let count = div_counts - 1;

        $('#child_div' + count).remove();
        if (div_counts == 3) {

            var rm_btnId = "#remove_InBtn"+rm;
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