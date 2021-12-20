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
                        <form action="{{ route('admin.update_prelimary_process') }}" id="edit_process" method="post" >
                            @csrf
                            
                            <input type="hidden" name="id" value="{{ $action_data[0]->id }}">
                            <div class="form-group"> 
                                <label>Due Datetime</label> 
                                <input type="text" class="form-control" name="expected_ans_date" readonly id="expected_ans_date" value="{{ date('d-m-Y h:i a',strtotime($action_data[0]->expected_ans_date)) }}" /> 
                            </div>
                             
                            <div class="form-group"> 
                                <label>Querry Detail</label> 
                                <textarea class="form-control" rows="10" name="querry_details" id="querry_details">{{ $action_data[0]->querry_details }}</textarea>
                            </div>

                            <div class="form-group">
                                <label>Work Allotment to Department</label>
                                <select class="form-control" class="work_allotment_department_id" name="work_allotment_department_id" id="work_allotment_department_id">
                                <option value="" disabled selected>Please Select</option>
                                @foreach($department_category as $Department)
                                    <option value="{{ $Department->id }}" <?php echo ($action_data[0]->work_allotment_department_id == $Department->id) ? "selected='selected'" : '' ?>>{{ $Department->dept_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Main/Prime Employee</label>
                                <select class="form-control" class="prime_employee_id" name="prime_employee_id" id="prime_employee_id">
                                    <option value="">Please Select</option>
                                </select>
                            </div>

                                                       
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.prelimary_action_list') }}'" class="btn btn-default">Cancel</button>
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
    removeTextAreaWhiteSpace(); 
    $('#prime_employee_id').select2();
    $('#work_allotment_department_id').select2();
        jQuery("#edit_process").validate({
        ignore: [],
        rules: {
            querry_details: {
                required: true,
            },
            work_allotment_department_id: {
                required: true,
            },
            prime_employee_id: {
                required: true,
            }
        }
    });
    function removeTextAreaWhiteSpace() {
        var myTxtArea = document.getElementById('querry_details');
        myTxtArea.value = myTxtArea.value.replace(/^\s*|\s*$/g,'');
    }
    //==========================================
            var user_id = "<?php echo $action_data[0]->prime_employee_id; ?>";
            var department_list = "<?php echo $action_data[0]->work_allotment_department_id; ?>";
                $.ajax({
                    url: "{{ route('admin.depart_user_list') }}",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        id: department_list
                    },
                    dataType: "JSON",
                    success: function(data) {
                        
                        $("#prime_employee_id").html('');
                        $('#prime_employee_id').select2({
                            allowClear: true, 
                        });
                        $.each(data, function(index, user_list_obj) { 
                            $("#prime_employee_id").append('<option value="' + user_list_obj.id + '">' + user_list_obj.name + '</option>');
                        });
                        $('#prime_employee_id').val(user_id).trigger('change'); 

                    }
                });
});

</script>

<script>
 $("#work_allotment_department_id").change(function() {

department_list = $(this).val();

if (department_list) {
    
        $.ajax({

            url: "{{ route('admin.depart_user_list') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                id: department_list
            },
            dataType: "JSON",
            success: function(data) {
                
                $("#prime_employee_id").html('');
                $('#prime_employee_id').select2({
                    allowClear: true, 
                });
                $.each(data, function(index, user_list_obj) { 
                    $("#prime_employee_id").append('<option value="' + user_list_obj.id + '">' + user_list_obj.name + '</option>');
                });

            }
        });

} else {
    $("#prime_employee_id").empty();
}

});
</script>
@endsection
