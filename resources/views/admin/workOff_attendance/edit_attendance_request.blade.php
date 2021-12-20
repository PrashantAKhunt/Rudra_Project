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
                    <div class="col-sm-6 col-xs-6">
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
                        <form action="{{ route('admin.update_attendance_request') }}" id="edit_request" method="post">
                            @csrf


                          <input type="hidden"  name="id" id="id" value="{{ $request_details[0]->id }}">
                          <div class="row">

                         <div class="col-md-9">
                            <div class="form-group">
                                <label>Date</label>
                                <input type="text" class="form-control" required onchange="checkHoliday();" name="date" id="date" value="<?php echo date('d-m-Y', strtotime($request_details[0]->date)); ?>"/>
                                <input type="hidden" name="holiday_id" id="holiday_id" value="0">
                                <input type="hidden" id="old_date" value="<?php echo date('d-m-Y', strtotime($request_details[0]->date)); ?>">
                                <input type="hidden" name="day_name" id="week_name">

                            </div>
                            </div>

                                </div>

                                <!--  -->
                                <div class="row">
                                <div class="col-md-9">

                            <p class="text-dark" id="day_name">

                            </p>

                                </div>
                                </div>

                                <!--  -->
                                <div class="row">
                                <div class="col-md-9">
                            <p class="text-dark" id="holiday_name">

                            </p>
                                </div>
                                </div>


                                <!--  -->
                                <div class="row">

<div class="col-md-9">
                            <div class="form-group">
                            <label>Day Type</label>
                                    <select class="form-control" name="day_type" id="day_type">
                                    <option <?php if ($request_details[0]->day_type == "Full Day") { ?> selected <?php } ?> value="Full Day">Full Day</option>
                                        <option <?php if ($request_details[0]->day_type == "First Half") { ?> selected <?php } ?> value="First Half">First Half</option>
                                        <option <?php if ($request_details[0]->day_type == "Second Half") { ?> selected <?php } ?> value="Second Half">Second Half</option>
                                    </select>
                                </div>
                                </div>
                            </div>
                            <div class="row">

<div class="col-md-9">
                                <div class="form-group ">
                                <label>Reason to Come</label>
                                <textarea class="form-control" name="reason_note" id="reason_note">{{ $request_details[0]->reason_note }}</textarea>
                            </div>
                            </div>
                                </div>

                                <div class="row">

<div class="col-md-9">
                            <div class="form-group ">
                                <label>Description of Work</label>
                                <textarea class="form-control" name="description_note" id="description_note">{{ $request_details[0]->description_note }}</textarea>
                            </div>
                            </div>
                                </div>
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.holiday_work_attendance') }}'" class="btn btn-default">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('script')
<script>


jQuery('#edit_request').validate({
        ignore: [],
        rules: {
            date: {
                required: true,
            },
            day_type: {
                required: true
            },
            reason_note: {
                required: true,
            },
            description_note: {
                required: true
            },
        }
});

 $(document).ready(function() {
        jQuery('#date').datepicker({
            startDate: "+0d",
            autoclose: true,
            todayHighlight: true,
            format: "dd-mm-yyyy"
        });

    php_date = $('#old_date').val();
    day_name = moment(moment(php_date, 'DD-MM-YYYY')).format('YYYY-MM-DD');
    let weekday = moment(day_name).format('dddd');
    $('#week_name').val(weekday);

    $('#day_name').html('<b>Day: </b>' + weekday);



$.ajax({
                url: "{{ route('admin.check_holiday') }}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    date: php_date
                },
                success: function(data) {

                        if (data.holiday) {

                            $('#holiday_id').val(data.holiday[0].id);
                            $('#holiday_name').html('<b>Is Holiday: </b>Yes' +'('+ data.holiday[0].title + ')');


                        }else{
                            $('#holiday_id').val('0');

                            $('#holiday_name').html('<b>Is Holiday: </b>No');


                        }
                }

 });
 });
    /* ---------------------------------------New Functions-------------------------------------------------- */


    function checkHoliday() {

        //$('#week_name').val('');
        selected_date = $('#date').val();

    day_name = moment(moment(selected_date, 'DD-MM-YYYY')).format('YYYY-MM-DD');
    let weekday = moment(day_name).format('dddd');

    $('#week_name').val(weekday);
    $('#day_name').html('<b>Day: </b>' + weekday);

 $.ajax({
                url: "{{ route('admin.check_holiday') }}",
                type: "POST",
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    date: selected_date
                },
                success: function(data) {

                        if (data.holiday) {

                            $('#holiday_id').val(data.holiday[0].id);
                            $('#holiday_name').html('<b>Is Holiday: </b>Yes' +'('+ data.holiday[0].title + ')');


                        }else{

                                $('#holiday_name').html('<b>Is Holiday: </b>No');

                            $('#holiday_id').val('0');
                        }
                }

 });

}

</script>
@endsection
