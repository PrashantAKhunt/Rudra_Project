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
                        <form class="travelForm" action="{{ route('admin.insert_travel_option') }}" id="travel_option" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" id="id" name="id" value="{{ $travel->id }}" />
                            <div class="travel_box">
                                @foreach($travel_option as $index => $travel)

                                <div class="row travel_lists div_count" id="travel_list{{$index}}">
                                    <!-- <input type="text" value=""/>  -->
                                 
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label>Travel Via</label>
                                            <select class="form-control" name="travel_via[{{$index}}]">

                                                @foreach($travel_via as $key => $value)
                                                <option value="{{ $key }}" @if($key==$travel['travel_via']) selected @endif>{{ $value }}</option>
                                                @endforeach
                                            </select>

                                        </div>
                                        <div class="col-md-4">

                                            <label>Departure Time</label>
                                            <input type="text" class="form-control departure_datetime " value="{{$travel->departure_datetime}}" name="departure_datetime[{{$index}}]" />
                                        </div>
                                        <div class="col-md-4">

                                            <label>Arrival Time</label>
                                            <input type="text" class="form-control arrival_datetime " value="{{$travel->arrival_datetime}}" name="arrival_datetime[{{$index}}]" required />
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                    <div class="col-md-3">
                                            <label>Amount</label>
                                            <input type="number" class="form-control amount" value="{{$travel->amount}}" name="amount[{{$index}}]" required />
                                        </div>
                                        <div class="col-md-3">
                                            <label>From Location</label>
                                            <input type="text" class="form-control from" value="{{$travel->from}}" name="from[{{$index}}]" required />
                                        </div>
                                        <div class="col-md-3">

                                            <label>To Location</label>
                                            <input type="text" class="form-control to" value="{{$travel->to}}" name="to[{{$index}}]" required />
                                        </div>
                                        <div class="col-md-3">

                                            <label>Please Reupload File</label>
                                            <a title="view File" href="{{ asset('storage/'.str_replace('public/','',!empty($travel->travel_image) ? $travel->travel_image : 'public/no_image')) }}" download>View File</a>
                                            <!-- <input type="hidden" value="{{$travel->travel_image}}" name="old_travel_image[{{$index}}]" class="form-control travel_image" /> -->
                                            <input type="file" name="travel_image[{{$index}}]" class="form-control travel_image" required />
                                        </div>
                                    </div>
                                    <br>

                                    <div class="row">
                                        <div class="col-md-10">
                                            <label>Details</label>
                                            <textarea name="details[{{$index}}]" class="form-control details" required>{{$travel->details}}</textarea>
                                        </div>         
                            
                                    </div>
                                    <br>
                                    <hr class="m-t-0 m-b-40">
                                </div>
                               
                                @endforeach
                            </div>
                            <button type="button" title="Remove" class="btn btn-danger" id="remove-btn" onclick="remove_div();"><i class="fa fa-trash"></i></button>
                            
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

</script>
<style type="text/css">
    .padded {
        padding-top: 22px;
    }
</style>

<script>
    //let glob_var = true;
    var my_array = <?php echo json_encode($travel_option); ?>;

    var last_element = my_array.length - 1;


    function remove_div() {
        
        
        let div_counts = $(".div_count").length;
        let count = div_counts - 1;
   
        $('#travel_list' + count).remove();
       
        if (div_counts == 1) {

            $('#remove-btn').hide();

        }
        last_element--;

    }

    function add_new() {

        last_element++;
        var append_html = '<div class="div_count" id="travel_list' + last_element + '">' +

            ' <div class="row">' +
            '   <div class="col-md-3">' +
            '  <div class="form-group ">' +
            '     <label>Travel Via</label>' +
            '     <select class="form-control travel_via" name="travel_via[' + last_element + ']"  required>' +
            '<option value=" " disabled selected>Please select</option>' +
            '<option value="1">Company Car</option> ' +
            '<option value="2">Bus</option> ' +
            '<option value="3">Train</option> ' +
            '<option value="4">Flight</option> ' +
            '<option value="5">Local</option> ' +
            '<option value="6">Private</option> ' +
            '</select>' +
            '  </div>' +
            ' </div>' +
            '    <div class="col-md-4">' +
            ' <div class="form-group ">' +
            '  <label>Departure Time</label>' +
            '  <input type="text" required class="form-control departure_datetime" name="departure_datetime[' + last_element + ']" />' +
            '  </div>' +
            ' </div>' +
            '    <div class="col-md-4">' +
            ' <div class="form-group ">' +
            '  <label>Arrival Time</label>' +
            '  <input type="text" required class="form-control arrival_datetime" name="arrival_datetime[' + last_element + ']" />' +
            '  </div>' +
            ' </div>' +
            '  </div>' +
            '  <div class="row">' +
            ' <div class="col-md-3">' +
            '<div class="form-group ">' +
            '<label>Amount</label>' +
            '<input type="number" required name="amount[' + last_element + ']" class="form-control amount" />' +
            '  </div>' +
            '</div>' +
            ' <div class="col-md-3">' +
            '<div class="form-group ">' +
            '<label>From Location</label>' +
            '<input type="text" required name="from[' + last_element + ']" class="form-control from" />' +
            '  </div>' +
            '</div>' +
            ' <div class="col-md-3">' +
            ' <div class="form-group ">' +
            '<label>To Location</label>' +
            '<input class="form-control to" type="text" required  name="to[' + last_element + ']" value=""/>' +
            '</div>' +
            '</div>' +
            ' <div class="col-md-3">' +
            ' <div class="form-group ">' +
            '<label>File</label>' +
            '<input class="form-control trvael_image" type="file" required  name="travel_image[' + last_element + ']" value=""/>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="row">' +
            '<div class="col-md-10">' +
            '<div class="form-group ">' +
            '<label>Details</label>' +
            '<textarea  name="details[' + last_element + ']" class="form-control details" required>' +
            '</textarea>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<hr class="m-t-0 m-b-40"></div>';

        $('.travel_box').append(append_html);
    
        if (last_element == 0) {

            $('#remove-btn').show();

        }
        $('.departure_datetime').datetimepicker({
            format: 'DD-MM-YYYY HH:mm:ss'
        });
        $('.arrival_datetime').datetimepicker({
            format: 'DD-MM-YYYY HH:mm:ss'
        });

    }

 

</script>




@endsection