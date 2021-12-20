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
                        <form action="{{ route('admin.update_prime_process') }}" id="edit_process" method="post" >
                            @csrf
                            
                            <input type="hidden" name="id" value="{{ $edit_data[0]->id }}">
                            
                            <h4 class="page-title">->  Assume the Time for Complete the work</h4>
                            <br>
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="form-group">
                                    <label>Assume Time</label>
                                    <select class="select2 form-control" onclick="checkHour();" name="assume_work_type" id="assume_work_type" required>
                                        <option value="" disabled selected>Please Select</option>
                                        @foreach($work_mode as $mode)
                                        <option value="{{ $mode }}" <?php echo ($edit_data[0]->assume_work_type == $mode) ? "selected='selected'" : '' ?>>{{ $mode }}</option>
                                        @endforeach
                                    </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-9">
                                    <div class="form-group "> 
                                    <label>Enter Number of Hour/Days/Weeks</label> 
                                    <input type="number" class="form-control" name="assume_work_time" id="assume_work_time" value="{{ $edit_data[0]->assume_work_time }}" /> 
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-9">
                                    <div class="form-group" id="work_hour" style="display:none"> 
                                    <label>Enter Work hour per day</label> 
                                    <input type="number" class="form-control" name="assume_work_hour" id="assume_work_hour" value="{{ $edit_data[0]->assume_work_hour }}" /> 
                                    </div>
                                </div>
                                <input type="hidden" name="assume_total_hour" id="assume_total_hour" value="">
                            </div>

                            <div class="row">
                                <div class="col-md-9">
                                    <div class="form-group "> 
                                    <label>Work Details</label> 
                                    <textarea class="form-control" rows="3" name="work_details" id="work_details" >
                                    {{ $edit_data[0]->work_details }}
                                    </textarea>
                                    </div>
                                </div>
                            </div>

                            
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="form-group" id="departList">
                                        <label>Support Required from Department </label>
                                        <select class="select2 m-b-10 select2-multiple" name="support_departments[]" multiple="multiple" id="support_departments">
                                            
                                        
                                                @foreach($department_category as $Department)
                                                <option <?php if (in_array($Department->id, $edit_data[0]->support_department_id)) { ?> selected <?php } ?> value="{{ $Department->id }}">{{ $Department->dept_name }}</option>
                                                @endforeach
                                         
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="form-group" id="userList">
                                        <label>Support Required from Employee</label>
                                        <select class="select2 m-b-10 select2-multiple" name="support_employees[]" multiple="multiple" id="support_employees" >
                                            
                                            @if(!empty($edit_data[0]->support_employee_id))
                                                @foreach($user as $key => $value)
                                                <option <?php if (in_array($key, $edit_data[0]->support_employee_id)) { ?> selected <?php } ?> value="{{ $key }}">{{ $value }}</option>
                                                @endforeach	
                                            @endif

                                        </select>
                                    </div>
                                </div>
                            </div>

                            <h4 class="page-title">->  Distrubution of Task to Employees ( % Distribution of Task )</h4>
                            
                            <br>
                            <div class="row">
                                <div class="col-md-11">
                                <h4 class="page-title">Note: Please select employee and then check button</h4>
                                </div>
                                <div class="col-md-1">
                                <button type="button" id="check_btn" onclick="showTable();" class="btn btn-primary btn-rounded btn-sm"><i class="fa fa-check"></i></button>
                                </div>
                            </div>
                            <br>
                            
                            @if( !empty($edit_data[0]->task_percentage) )
                            <div class="row" id="loop_div" >
                                <div class="col-md-12">
                                    <table border=“2px” >
                                        @foreach($edit_data[0]->task_percentage as $key => $list)
                                        <tr><td width="200px" height="50px"> {{ $list['user_name'] }} </td><td> <input type="number" placeholder="30 %" required class="form-control" id="find_percentage_{{$key}}" onchange="set_hour(`{{$key}}`)" name="task_percentage[{{ $key }}]"  value="{{ $list['task_percentage'] }}" > </td><td> <input type="number" onchange="set_percentage(`{{$key}}`)" placeholder="5 hour" id="find_hour_{{$key}}" class="form-control" required name="work_hour[{{ $key }}]" value="{{ $list['task_hour'] }}" > </td></tr>
                                        @endforeach
                                    </table> 
                                </div>
                            </div>
                            @endif
                           
                            <div class="row" id="dynamic_div" style="display:none">
                                <div class="col-md-12">
                                    <table border=“2px” id="dynamic_table">
                                            
                                
                                    </table> 
                                </div>
                            </div>
                            <br>
                            <button type="submit" id="btn-submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.prime_action_list') }}'" class="btn btn-default">Cancel</button>
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
$(document).ready(function(){   
    checkHour();
    if ($('#support_employees').val() != null ) {
        $('#check_btn').hide();
    }
    //showTable();
    $('#support_departments').select2();
    $('#support_employees').select2();
    removeTextAreaWhiteSpace();
        jQuery("#edit_process").validate({
        ignore: [],
        rules: {
            // 'support_departments[]': {
            //     required: true,
            // },
            'support_employees[]': {
                required: function(element){
                    return $("#support_departments").val()!= null;
                },
            },
            'task_percentage[]': {
                required: true,
            },
            assume_work_type: {
                required: true,
            },
            assume_work_time: {
                required: true,
            },
            work_details: {
                required: true
            }
        }
    });
    function removeTextAreaWhiteSpace() {
        var myTxtArea = document.getElementById('work_details');
        myTxtArea.value = myTxtArea.value.replace(/^\s*|\s*$/g,'');
    }
    //==========================================
        
});
</script>

<script>
function checkHour() {
    type = $('#assume_work_type').val();
    
    if (type != null && type != 'Hour') {
        $('#work_hour').show();
    } else {
        $('#work_hour').hide();
    }
}
function showTable() {
    
    auth_name = '<?php echo $auth_name ?>';
    var hTML = '<tr class="copy"><td width="200px" height="50px"> '+ auth_name +' </td><td> <input type="number" id="find_percentage_0" placeholder="30 %" onchange="set_hour(0)" class="form-control" required name="task_percentage[0]" > </td><td> <input type="number" placeholder="5 hour" id="find_hour_0" onchange="set_percentage(0)" class="form-control" required name="work_hour[0]" > </td></tr>';
    emp_list = $('#support_employees').val();
    if (emp_list != null ) {
        
        var emp_data = $('#support_employees').select2('data');
        
        for (let index = 0; index < emp_list.length; index++) {
            var count = index+1;
            hTML+= '<tr class="copy"><td width="200px" height="50px"> '+ emp_data[index].text +' </td><td> <input type="number" placeholder="30 %" id="find_percentage_'+ count +'" onchange="set_hour('+ count +')" class="form-control" required name="task_percentage['+ count +']" > </td><td> <input type="number" placeholder="5 hour" id="find_hour_'+ count +'" onchange="set_percentage('+ count +')" class="form-control" required name="work_hour['+ count +']" > </td></tr>'; 
        }
        
        $('#dynamic_table').append(hTML);
        $('#dynamic_div').show();
        $('#check_btn').hide();

    } 
        
    
}
//================================
function get_hour() {
    var total_hour = 0;
    if ( $("#assume_work_type").val() == 'Hour') {
        total_hour = Number($("#assume_work_time").val());
    } else if( $("#assume_work_type").val() == 'Week') {
        total_days =  Number($("#assume_work_time").val())*6;
        total_hour = total_days*Number($("#assume_work_hour").val());
    } else if( $("#assume_work_type").val() == 'Days'){
        total_hour =  Number($("#assume_work_time").val())*Number($("#assume_work_hour").val());
    }
    $("#assume_total_hour").val(total_hour);
    return total_hour;
}
$(document).on('click', '#btn-submit', function(e) {
        if ($("#edit_process").valid()) {
            e.preventDefault();
                total_hour = get_hour();
                if (total_hour) {
                    $('#edit_process').submit();
                }       
        }
        
});
function set_hour(id){
    
    total_hour = get_hour();
    var check_val = $("#find_percentage_"+id).val();
    var set_val = total_hour*Number(check_val) / 100 ;
    $("#find_hour_"+id).val(set_val);

}
//===================================
function set_percentage(id){
    
    total_hour = get_hour();
    
    var check_val = $("#find_hour_"+id).val();
    var set_val = 100*Number(check_val) / total_hour ;
    $("#find_percentage_"+id).val(set_val);

}
</script>

<script>
$("#support_departments").change(function () {
    $(".copy").remove(); 
    $("#dynamic_div").hide();

    $("#loop_div").remove(); 
    $('#check_btn').show();

    department_list = $(this).val();

    if (department_list) {

        $.ajax({

            url: "{{ route('admin.depart_multi_user_list') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                id: department_list
            },
            dataType: "JSON",
            success: function (data) {
            
                $("#support_employees").html('');
                $('#support_employees').val(null).trigger('change');

                $.each(data, function (index, user_list_obj) {
                    $("#support_employees").append('<option value="' + user_list_obj.id + '">' + user_list_obj.name + '</option>');
                })

            }
        });

    } else {

        $("#support_employees").html('');
    }

});

function change_stat() {
    $(".copy").remove(); 
    $("#dynamic_div").hide();
    $("#loop_div").remove();
    $('#check_btn').show();
}

$("#support_employees").change(function () {
    $(".copy").remove(); 
    $("#dynamic_div").hide();
    $("#loop_div").remove();
    $('#check_btn').show();
    
});
$("#assume_work_type, #assume_work_time , #assume_work_hour").change(function () {
    change_stat();
});

</script>
@endsection
