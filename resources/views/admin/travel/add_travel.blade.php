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
                <li><a href="{{ route('admin.travel') }}">{{ $module_title }}</a></li>
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
                        <form action="{{ route('admin.save_travel') }}" id="save_travel" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group ">
                                        <label>Select Company <span class="error">*</span></label>
                                        <select class="form-control" name="company_id" id="company_id">
                                            <option value="">Select Company</option>
                                            @foreach($companies as $company)
                                            <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-sm-3">
                                    <div class="form-group ">
                                        <label>Project <span class="error">*</span></label>
                                        <select class="form-control select2" class="project_id" name="project_id" id="project_id">
                                            <option value="">Select Project</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3 other_project hide">
                                    <div class="form-group">
                                        <label>Other Project</label>
                                        <input type="text" class="form-control" name="other_project_details" id="other_project_details" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                            <div class="col-sm-6">
                                    <div class="form-group ">
                                        <label>Travel User <span class="error">*</span></label>
                                        <select required="required" name="traveler_ids[]" id="traveler_ids" class="select2 m-b-10 select2-multiple" multiple="multiple" data-placeholder="Choose">
                                            @foreach($traveler_ids as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group ">
                                        <label>Travel Via</label>
                                        <select class="form-control select2" class="travel_via" name="travel_via" id="travel_via">
                                            @foreach($travel_via as $key => $value)
                                            <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3" id="flights_list" style='display:none;'>
                                    <div class="form-group ">
                                        <label>Flights</label>
                                        <select class="form-control" name="flight_trip" id="flight_trip">
                                            <option value="0">--select--</option>
                                            <option value="one_way">One Way</option>
                                            <option value="round_trip">Round Trip</option>
                                            <option value="multi_city">Multi City</option>

                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row div_count">

                                <div class="col-sm-3">
                                    <div class="form-group ">
                                        <label>From Location <span class="error">*</span></label>
                                        <input type="text" required name="from[0]" maxlength="255" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group ">
                                        <label>To Location <span class="error">*</span></label>
                                        <input type="text" required name="to[0]" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group ">
                                        <label>Departure <span class="error">*</span></label>
                                        <input type="text" required name="departure_datetime[0]" class="form-control departure_datetime" />
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group ">
                                        <label>Arrival <span class="error">*</span></label>
                                        <input type="text" required name="arrival_datetime[0]" class="form-control arrival_datetime" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group ">
                                            <label>Work Details <span class="error">*</span></label>
                                            <textarea class="form-control" required rows="3" name="details[0]"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="dynamic_div">


                            </div>

                            <button type="button" id="remove_btn" style="display: none;" title="Remove" class="btn btn-danger" onclick="remove_div();"><i class="fa fa-trash"> REMOVE</i></button>
                            <button type="button" id="add_div" style="display: none;" title="Add" class="btn btn-primary" onclick="add_new();"><i class="fa fa-plus"></i> ADD ANOTHER CITY</button>
                            <div class="clearfix"></div>
                            <br>
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.travel') }}'" class="btn btn-default">Cancel</button>
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
    /* new function */

    var n = 1;

    $('#travel_via').on('change', function() {

        document.getElementById('flight_trip').selectedIndex = 0;
        if (this.value == '4') {
            $("#flights_list").show();
        } else {
            $("div.rm_div").remove();
            n = 1;
            $('.set_div').remove();
            $('#remove_btn').hide();
            $("#add_div").hide();
            $("#flights_list").hide();
        }
    });

    function set_dynamiDiv() {

        var html = '<div class="div_count rm_div set_div"><hr class="m-t-0 m-b-40">' +
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
            '<div class="row">' +
            '<div class="col-sm-6">' +
            '<div class="form-group ">' +
            '<label>Work Details</label>' +
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
    $('#flight_trip').on('change', function() {
        let type = $(this).val();
        n = 1;
        $("div.rm_div").remove();
        $('.set_div').remove();
        $('#add_div').hide();
        $('#remove_btn').hide();
        switch (type) {
            case 'round_trip':

                set_dynamiDiv();
                break;
            case 'multi_city':

                set_dynamiDiv();
                $("#add_div").show();
                break;
            default:

                console.log('NOTHING');
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

        n--;
    }


    function add_new() {

        n++;

        var append_html = '<div class="div_count rm_div" id="child_div' + n + '"><hr class="m-t-0 m-b-40">' +
            '<div class="col-sm-3">' +
            '<div class="form-group ">' +
            '<label>From Location</label>' +
            '<input type="text" required name="from[' + n + ']" maxlength="255"  class="form-control" />' +
            '</div>' +
            '</div>' +
            '<div class="col-sm-3">' +
            ' <div class="form-group ">' +
            '<label>To Location</label>' +
            '<input type="text" required name="to[' + n + ']"  class="form-control" />' +
            '</div>' +
            '</div>' +
            '<div class="col-sm-3">' +
            '<div class="form-group ">' +
            '<label>Departure</label>' +
            '<input type="text" required name="departure_datetime[' + n + ']"class="form-control departure_datetime" />' +
            '</div>' +
            '</div>' +
            '<div class="col-sm-3">' +
            '<div class="form-group ">' +
            '<label>Arrival</label>' +
            '<input type="text" required name="arrival_datetime[' + n + ']" class="form-control arrival_datetime" />' +
            '</div>' +
            '</div>' +
            '<div class="row">' +
            '<div class="col-sm-6">' +
            '<div class="form-group ">' +
            '<label>Work Details</label>' +
            '<textarea class="form-control" required rows="3" name="details[' + n + ']"></textarea>' +
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
    $('.departure_datetime').datetimepicker({
        format: 'DD-MM-YYYY HH:mm:ss'
    });
    $('.arrival_datetime').datetimepicker({
        format: 'DD-MM-YYYY HH:mm:ss'
    });
    $('.select2').select2();
    jQuery('#save_travel').validate({
        ignore: [],
        rules: {
            company_id: {
                required: true,
            },
            project_id: {
                required: true,
            },
            traveler_ids: {
                required: true
            },
            'departure_datetime[]': {
                required: true
            },
            'arrival_datetime[]': {
                required: true
            },
            'from[]': {
                required: true
            },
            'to[]': {
                required: true
            }
        }
    });
    $(document).ready(function() {
        $("#company_id").change(function() {
            var company_id = $(this).val();
            if ($(this).val().length >= 1) {
                $.ajax({
                    url: "{{ route('admin.get_cash_project_list')}}",
                    type: 'get',
                    data: "company_id=" + company_id,
                    success: function(data, textStatus, jQxhr) {
                        $('#project_id').empty();
                        $('#project_id').append(data);
                    },
                    error: function(jqXhr, textStatus, errorThrown) {
                        console.log(errorThrown);
                    }
                });
            }
        });
        $("#project_id").change(function() {
            if ($(this).val() == 1) {
                $("#other_project_details").parents('.other_project').removeClass('hide');
            } else {
                $("#other_project_details").val("");
                $("#other_project_details").parents('.other_project').addClass('hide');
            }
        });
    });
</script>

<script>
    $(document).ready(function() {





    });
</script>
@endsection