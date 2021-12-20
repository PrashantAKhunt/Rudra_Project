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
                        <form action="{{ route('admin.save_travel_booking') }}" id="travel_booking" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" id="id" name="id" value="{{ $booking_data[0]->id }}" />
                            <input type="hidden" id="travel_id" name="travel_id" value="{{ $booking_data[0]->travel_id }}" />

                  
                            <div class="row">
                               <div class="col-sm-12">
                                     <h4>Approved Option File
                                     <a title="Download File" href="{{ asset('storage/'.str_replace('public/','',!empty($booking_data[0]->travel_image) ? $booking_data[0]->travel_image : 'public/no_image')) }}" download><i class="fa fa-cloud-download fa-lg"></i></a></a>
                                    </h4>  
                                </div>
                                <br>
                                <div class="col-sm-3">
                                    <div class="form-group ">
                                        <label>Upload Booking Files</label>
                                        <input type="file" name="file_name[]" class="form-control" multiple required />
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group ">
                                        <label>Amount</label>
                                        <input type="text" name="amount" id="amount" value="{{ $booking_data[0]->amount }}" class="form-control" required />
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group ">
                                        <label>Payment Type</label>
                                        <select class="form-control select2" name="payment_type" id="payment_type">
                                            <option value=" " disabled selected>Please select</option>
                                            @foreach( $payment_type as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group" id="card_number" style='display:none;'>
                                        <label>Card Number</label>
                                        <select class="form-control select2" name="card_number" id="card_number_list">

                                        </select>

                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="form-group ">
                                        <label>Travel Via</label>
                                        <select class="form-control" name="travel_via" id="travel_via">
                                            @foreach($travel_via as $key => $value)
                                            <option value="{{ $key }}" @if($key==$booking_data[0]->travel_via) selected @endif>{{ $value }}</option>
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
                                            <option <?php if ($key == $booking_data[0]->flight_trip) { ?> selected <?php } ?> value="{{ $key }}">{{ $value }}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                </div>
                            </div>
                            @foreach($booking_data[0]->travel_info as $index => $info)
                            <div class="row travel_info div_count">
                                <div class="col-sm-3">
                                    <div class="form-group ">
                                        <label>From Location</label>
                                        <input type="text" name="from[{{ $index }}]" value="{{ $info->from }}" maxlength="255" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group ">
                                        <label>To Location</label>
                                        <input type="text" name="to[{{ $index }}]" value="{{ $info->to }}" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group ">
                                        <label>Departure</label>
                                        <input type="text" name="departure_datetime[{{ $index }}]" value="{{ $info->departure_datetime }}" class="form-control departure_datetime" />
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group ">
                                        <label>Arrival</label>
                                        <input type="text" name="arrival_datetime[{{ $index }}]" value="{{ $info->arrival_datetime }}" class="form-control arrival_datetime" />
                                    </div>
                                </div>



                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group ">
                                            <label>Details</label>
                                            <textarea name="details[{{ $index }}]" class="form-control details">{{ $info->details }}</textarea>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            @endforeach
                            <div class="row" id="dynamic_div">


                            </div>

                            <button type="button" id="remove_btn" style="display: none;" title="Remove" class="btn btn-danger" onclick="remove_div();"><i class="fa fa-trash"> REMOVE</i></button>
                            <button type="button" id="add_div" style="display: none;" title="Add" class="btn btn-primary" onclick="add_new();"><i class="fa fa-plus"></i> ADD ANOTHER CITY</button>
                            <div class="clearfix"></div>
                            <br>
                           <!--  <br> -->
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


    var my_array = <?php echo json_encode($booking_data[0]->travel_info); ?>;

var loop_count = my_array.length - 1;


function set_dynamiDiv(num) { //init div

    for (let i = 0; i < num; i++) {
        var html = '<div class="div_count travel_info set_div" id="info_div' + i + '"><hr class="m-t-0 m-b-40">' +
            '<div class="col-sm-3">' +
            '<div class="form-group ">' +
            '<label>From Location</label>' +
            '<input type="text" required name="from['+ i +']" maxlength="255"  class="form-control" />' +
            '</div>' +
            '</div>' +
            '<div class="col-sm-3">' +
            ' <div class="form-group ">' +
            '<label>To Location</label>' +
            '<input type="text" required name="to['+ i +']"  class="form-control" />' +
            '</div>' +
            '</div>' +
            '<div class="col-sm-3">' +
            '<div class="form-group ">' +
            '<label>Departure</label>' +
            '<input type="text" required name="departure_datetime['+ i +']"class="form-control departure_datetime" />' +
            '</div>' +
            '</div>' +
            '<div class="col-sm-3">' +
            '<div class="form-group ">' +
            '<label>Arrival</label>' +
            '<input type="text" required name="arrival_datetime['+ i +']" class="form-control arrival_datetime" />' +
            '</div>' +
            '</div>' +
            '<div class="row">' +
            '<div class="col-sm-6">' +
            '<div class="form-group ">' +
            '<label>Work Details</label>' +
            '<textarea class="form-control" required rows="3" name="details['+ i +']"></textarea>' +
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
</script>
<script>
    $('#travel_booking').validate({
        ignore: [],
        rules: {

            'travel_via': {
                required: true,
            },
            'travel_image': {
                required: true
            },
            'departure_datetime': {
                required: true
            },
            'arrival_datetime': {
                required: true
            },
            'payment_type': {
                required: true

            },
            'amount': {
                required: true

            },
            'from': {
                required: true
            },
            'to': {
                required: true
            }

        }
    });
</script>

<script>
    $(document).ready(function() {

        $("#payment_type").change(function() {

            payment_type = $(this).val();

            if (payment_type == 'credit_card' || payment_type == 'debit_card') {

                $.ajax({
                    url: "{{ route('admin.get_company_payment_cards') }}",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        travel_id: function() {
                            return $("#travel_id").val();
                        },
                        id: function() {
                            return $("#id").val();
                        },
                        payment_type: function() {
                            return $("#payment_type").val();
                        }
                    },
                    dataType: "JSON",
                    //     //processData: false,
                    //     //contentType: false,
                    success: function(data) {

                        $("#card_number").show();

                        $("#card_number_list").empty();
                        $("#card_number_list").append("<option value='' disabled selected>Please select</option>");
                        $.each(data, function(index, card_obj) {


                            $("#card_number_list").append('<option value="' + card_obj.id + '">' + card_obj.card_number + '</option>');
                        });

                    },
                    error: function(jqXhr, textStatus, errorThrown) {
                        console.log(errorThrown);
                    }
                });

            } else {

                $("#card_number_list").empty();
                $("#card_number").hide();

            }

        });

        var my_array = <?php echo json_encode($booking_data[0]->travel_info); ?>;
        var arr_length = my_array.length;

        if ($('#travel_via').val() == 4) {
            $("#flights_list").show();
       

        if ($('#flight_trip').val() == 'multi_city') {
            $('#add_div').show();
            if (arr_length >2) {
                $('#remove_btn').show();
            }
           
        }

    }

    });
</script>
@endsection