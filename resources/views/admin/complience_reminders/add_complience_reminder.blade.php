@extends('layouts.admin_app')

@section('content')
<?php

use Illuminate\Support\Facades\Config; ?>
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">{{ $module_title }}</h4>
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
                        <form action="{{ route('admin.insert_compliance_reminder') }}" id="insert_complience" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group "> 
                                <label> Company <span class="error">*</span></label>
                                <select class="form-control" name="company_id" id="company_id">
                                    <option value="">Select Company</option>
                                    @foreach($companies as $company_list_data)
                                    <option value="{{ $company_list_data->id }}">{{ $company_list_data->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group "> 
                                <label> Compliance Category <span class="error">*</span></label>
                                <select class="form-control" name="compliance_category_id" id="compliance_category_id">
                                    <option value="">Select Compliance Type</option>
                                    @foreach($compliance_types as $compliance)
                                    <option value="{{ $compliance->id }}">{{ $compliance->compliance_name }}</option>
                                    @endforeach
                                </select>
                            </div>    

                            <div class="form-group ">
                                <label>Name Of Compliance <span class="error">*</span></label>
                                <input type="text" class="form-control" name="compliance_name" id="compliance_name" value="" />
                            </div> 
                            <div class="form-group">
                                <label>Compliance Description <span class="error">*</span></label>
                                <textarea  class="form-control"  name="compliance_description" id="compliance_description" rows="3"></textarea>
                            </div>   

                            <div class="form-group "> 
                                <label> Periodicity <span class="error">*</span></label>
                                <select class="form-control" name="periodicity_type" id="periodicity_type">
                                    <option value="">Select Periodicity</option>
                                    @foreach($periodicity_types as $periodicity)
                                    <option value="{{ $periodicity }}">{{ $periodicity }}</option>
                                    @endforeach
                                </select>
                            </div> 
                            <div id="dynamic">
                            
                            </div>
                            <div class="form-group "> 
                                <label>Start Date <span class="error">*</span></label> 
                                <input type="text" class="form-control" name="start_date" id="start_date" value="" /> 
                            </div>
                            <div class="form-group "> 
                                <label>End Date <span class="error">*</span></label> 
                                <input type="text" class="form-control" name="end_date" id="end_date" value="" /> 
                            </div>
                            <div class="form-group "> 
                                <label>Periodic Due-Time <span class="error">*</span></label> 
                                <input type="text" class="form-control" name="periodicity_time" id="periodicity_time" value="" /> 
                            </div>
                            <div class="form-group "> 
                                <label> Responsible Person <span class="error">*</span></label>
                                <select class="form-control" name="responsible_person_id" id="responsible_person_id">
                                    <option value="">Select</option>
                                    @foreach($users_data as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div> 
                            <div class="form-group "> 
                                <label> Payment Responsible Person <span class="error">*</span></label>
                                <select class="form-control" name="payment_responsible_person_id" id="payment_responsible_person_id">
                                    <option value="">Select</option>
                                    @foreach($users_data as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                    
                                </select>
                            </div> 
                            <div class="form-group "> 
                                <label> Checker (Responsible) <span class="error">*</span></label>
                                <select class="form-control" name="checker_id" id="checker_id">
                                    <option value="">Select</option>
                                    @foreach($users_data as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div> 
                            <div class="form-group "> 
                                <label> Super Admin Verification </label>
                                <select class="form-control" name="super_admin_checker_id" id="super_admin_checker_id">
                                    <option value="">Select</option>
                                    @foreach($users_data as $user)
                                    <option value="{{ $user->id }}" <?php echo (config::get('constants.SuperUser') == $user->id) ? "selected='selected'" : '' ?> >{{ $user->name }}</option>
                                    
                                    @endforeach
                                </select>
                            </div> 
                            <h4>-> Reminder Schedule</h4>
                            <br>
                            <div class="form-group "> 
                                <label> Before Day</label>
                                <select class="form-control" name="first_day_interval" id="first_day_interval">
                                    <option value="">Select Day</option>
                                    @foreach($days as $day)
                                    <option value="{{ $day }}">{{ $day }}</option>
                                    @endforeach
                                </select>
                            </div> 
                            <div class="form-group "> 
                                <label> Before Day</label>
                                <select class="form-control" name="second_day_interval" id="second_day_interval">
                                    <option value="">Select Day</option>
                                    @foreach($days as $day)
                                    <option value="{{ $day }}">{{ $day }}</option>
                                    @endforeach
                                </select>
                            </div> 
                            <div class="form-group "> 
                                <label> Before Day</label>
                                <select class="form-control" name="third_day_interval" id="third_day_interval">
                                    <option value="">Select Day</option>
                                    @foreach($days as $day)
                                    <option value="{{ $day }}">{{ $day }}</option>
                                    @endforeach
                                </select>
                            </div> 

                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.compliance_reminders') }}'" class="btn btn-default">Cancel</button>
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
    jQuery('#insert_complience').validate({
        ignore: [],
        rules: {
            company_id: "required",
            compliance_category_id: "required",
            compliance_name: "required",
            compliance_description: "required",
            periodicity_type: "required",
            start_date: "required",
            end_date: "required",
            periodicity_time: "required",
            responsible_person_id: "required",
            payment_responsible_person_id: "required",
            checker_id: "required",
            super_admin_checker_id: "required" 
            // first_day_interval: "required" ,
            // second_day_interval: "required" ,
            // third_day_interval: "required" ,
        }
    });

</script>
<script>
$(document).ready(function () {
    $('#responsible_person_id').select2();
    $('#payment_responsible_person_id').select2();
        $('#super_admin_checker_id').select2({containerCssClass: 'mysel-con'});
        $('.mysel-con').css('pointer-events', 'none');
    $('#checker_id').select2();
    $('#periodicity_type').select2();
    $('#company_id').select2();
    $('#compliance_category_id').select2();
    $('#first_day_interval').select2();
    $('#second_day_interval').select2();
    $('#third_day_interval').select2();
    jQuery('#start_date').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: "dd-mm-yyyy"
        });
    jQuery('#end_date').datepicker({
        autoclose: true,
        todayHighlight: true,
        format: "dd-mm-yyyy"
    });
    $('#periodicity_time').datetimepicker({
        format: 'h:mm a'
    });
});

function setMonth() {
        var number = 12;
        var month = "";
        month+='<div class="form-group ">'+
                    '<label>Periodic Repeat Month</label>'+
                    '<select class="form-control" required name="periodic_month" id="periodic_month">'+
                    '<option value="" selected></option>';
        for (let i = 1; i <= number; i++) {
            month +='<option value="'+i+'">'+i+'</option>';
        }
        month+='</select>'+'</div>';
        return month;
}
function setDays() {
        var number = 30;
        var month = "";
        month+='<div class="form-group ">'+
                    '<label>Periodic Repeat Day</label>'+
                    '<select class="form-control" required name="periodic_date" id="periodic_date">'+
                    '<option value="" selected></option>';
        for (let i = 1; i <= number; i++) {
            month +='<option value="'+i+'">'+i+'</option>';
        }
        month+='</select>'+'</div>';
        return month;
}
function dynamicField(type) {
    var options = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday","Saturday","Sunday"];
    var day = '<div class="form-group ">'+
                '<label>Periodic Repeat day</label>' +
                '<input type="number" class="form-control" required name="periodic_date" id="periodic_date" value="" />'+ 
                '</div>';
    var week =  '<div class="form-group">'+
                '<label>Periodic Repeat Week-Day</label>' +
                '<select class="form-control" name="periodic_week_day" required id="periodic_week_day">'+
                    '<option value="" selected></option>'+
                    '<option value="Monday">Monday</option>'+
                    '<option value="Tuesday">Tuesday</option>'+
                    '<option value="Wednesday">Wednesday</option>'+
                    '<option value="Thursday">Thursday</option>'+
                    '<option value="Friday">Friday</option>'+
                    '<option value="Saturday">Saturday</option>'+
                    '<option value="Sunday">Sunday</option>'+
                '</select>'+
                '</div>'; 
                
            
    switch (type) {
        case 'Day':
            $('#dynamic').append(day);
            break;
        case 'Week':
            $('#dynamic').append(week);
            break;
        case 'Month':
            var month = setDays();
                $('#dynamic').append(month);
            break;
        default:
            var month = setMonth();
            var days = setDays();
            $('#dynamic').append(month);
            $('#dynamic').append(days);
            break;
    }
}
$("#periodicity_type").change(function () {
    $('#dynamic').empty();
    var type = $('#periodicity_type').val();
    dynamicField(type);
});

</script>
@endsection
