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
                        <form action="{{ route('admin.insert_leave') }}" id="add_leave" method="post">
                            @csrf
                            <div class="form-group "> 
                                <label>Subject <span class="error">*</span> </label> 
                                <input type="text" class="form-control" name="subject" id="subject" value="" />
                            </div>
                            <div class="form-group ">
                                <label>Description <span class="error">*</span> </label>
                                <textarea class="form-control" name="description" id="description"></textarea>
                            </div>
                            <div class="form-group ">
                                <label>Select Leave dates and leave types on start and end date <span class="error">*</span> </label>
                                <div class="input-daterange input-group" id="date-range">
                                    <input type="text" class="form-control start_leave_date"  name="start_date" id="start_date" readonly="true" />
                                    <span class="input-group-addon bg-info b-0 text-white">to</span>
                                    <input type="text" class="form-control end_leave_date" name="end_date" id="end_date" readonly="true" />
                                </div>
                            </div>
                            <div class="row">                                
                                <div class="col-md-6">
                                    <select class="form-control" class="leave_day" name="start_day" id="start_day">
                                        <option value="1">Full Day</option>
                                        <option value="2">First Half</option>
                                        <option value="3">Second Half</option>
                                    </select>
                                </div>
                                <div class="col-md-6">									
                                    <select class="form-control" class="leave_day" name="end_day" id="end_day" >
                                        <option value="1">Full Day</option>
                                        <option value="2">First Half</option>
                                        <option value="3">Second Half</option>
                                    </select>
                                </div>
                            </div>                            
                            <div class="form-group ">
                                <label>Leave Balance <span class="error">*</span> </label>
                                @if(!empty($categories))
                                <select name="leave_category_id" class="form-control" id="leave_category_id" >
                                    <option value="">Select Leave Balance</option>
                                    @foreach($categories as $key => $value)
                                    <option <?php if ($value['balance'] == "0" && $value['leavecategory']['id'] != 4) { ?> disabled style="color:red" <?php } ?> <?php if ($value['leavecategory']['id'] != 4) { ?> data-balance="{{ $value['balance'] }}" <?php } ?> value="{{ $value['leavecategory']['id'] }}"><?php if ($value['leavecategory']['id'] != 4) { ?> {{ $value['leavecategory']['name'] ." - ". $value['balance'] }} <?php } else { ?> {{ $value['leavecategory']['name'] }} <?php } ?></option>
                                    @endforeach
                                </select>
                                @endif
                            </div>
                            <!--<div class="form-group ">
                                <label>Notify</label>
                                @if(!empty($user))
                                <select name="notify_id[]" class="select2 m-b-10 select2-multiple" multiple="multiple" data-placeholder="Choose">
                                    @foreach($user as $key => $value)
                                    <option value="{{$key}}">{{$value}}</option>
                                    @endforeach								
                                </select>
                                @endif
                            </div>-->
                            
                            <div class="form-group ">
                                <label>Select Work Reliever <span class="error">*</span> </label>
                                
                                <select name="assign_work_user_id" class="form-control select2 m-b-10" data-placeholder="Select Work Reliever">
                                    <option value="">Select Work Reliever</option>
                                    @foreach($user_list as $key => $team_user)
                                    <option value="{{$team_user->id}}">{{$team_user->name}}</option>
                                    @endforeach								
                                </select>
                                
                            </div>
                            
                            <div class="form-group ">
                                <label>Reliever Work Detail <span class="error">*</span> </label>
                                <textarea name="assign_work_details" id="assign_work_details" class="form-control" ></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.leave') }}'" class="btn btn-default">Cancel</button>
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
    var __dayDiff = 0;
    jQuery('#add_leave').validate({
        ignore: [],
        rules: {
            subject: {
                required: true,
            },
            description: {
                required: true
            },
            start_date: {
                required: true,
            },
            end_date: {
                required: true
            },
            leave_category_id: {
                required: true,
            },
           /* notify_id: {
                required: true,
            },*/
            assign_work_details:{
                required:true
            },
            assign_work_user_id:{
                required:true
            }
        },
        submitHandler: function (form) {
            // Prevent double submission
            if (!this.beenSubmitted) {
                this.beenSubmitted = true;
                form.submit();
            }
        }
    });

    $('.select2').select2();

    jQuery('#date-range').datepicker({
        toggleActive: true,
        //format: 'dd-mm-yyyy'
        format: 'yyyy-mm-dd',
         daysOfWeekDisabled: [0,7]
    });

    $('.end_leave_date, .start_leave_date').change(function () {
        
        $('#leave_category_id').val("");
        
        var startDate = $('#start_date').val(), endDate = $('#end_date').val();
        var diff = new Date(new Date(endDate) - new Date(startDate));
        __dayDiff = (diff / 1000 / 60 / 60 / 24) + 1;
       
        if (__dayDiff <= 1) {
            $('#start_day option[value*="2"]').removeAttr('disabled');
            $('#end_day option').each(function () {
                $(this).prop('disabled', true);
            });
            $('#end_day option[value*="' + $('#start_day').val() + '"]').prop('disabled', false).prop('selected', true);
        } else {
            //first enable all end_day options and then disable only option 3
            $('#end_day option').each(function () {
                $(this).prop('disabled', false);
            });
            
            $('#start_day option[value*="2"]').prop('disabled', true);
            $('#end_day option[value*="3"]').prop('disabled', true);
            $('#end_day option[value*="2"]').removeAttr('disabled');
            $('#start_day option[value*="1"]').removeAttr('disabled');
            $('#start_day option[value*="3"]').removeAttr('disabled');
        }

        $('#leave_category_id option').each(function () {
            if (__dayDiff > $(this).data('balance')) {
                $(this).prop('disabled', true);
                $(this).css('color', 'red');
            } else {
                $(this).prop('disabled', false);
                $(this).css('color', 'gray');
            }
        });
        
        if ($('#start_day').val() == 2 || $('#start_day').val() == 3 || $('#end_day').val() == 2 || $('#end_day').val() == 3) {
            $("#leave_category_id option[value='5']").attr('disabled','disabled');
        }else{
            $("#leave_category_id option[value='5']").removeAttr('disabled');
        }

    });
    $('#start_day').change(function () {
        if (__dayDiff <= 1) {
            $('#end_day option').each(function () {
                $(this).prop('disabled', true);
            });
            $('#end_day option[value*="' + $(this).val() + '"]').prop('disabled', false).prop('selected', true);
            
        }
    });
    
    $('#start_day, #end_day').change(function () {
        debugger;
        if ($('#start_day').val() == 2 || $('#start_day').val() == 3 || $('#end_day').val() == 2 || $('#end_day').val() == 3) {
            $("#leave_category_id option[value='5']").attr('disabled','disabled');
        }else{
            $("#leave_category_id option[value='5']").removeAttr('disabled');
        }
    });

</script>
@endsection
