@extends('layouts.admin_app')

@section('content')
<style>
hr {
  border-top: 1px solid ;
}
</style>
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
        <div class="col-lg-12 col-sm-12 col-xs-12">
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
                        <form action="{{ route('admin.insert_cash_transfer') }}" id="insert_sender_frm" method="post">
                            @csrf
                            <h4>From Transfer:</h4>
                            <div class="form-group">
                                    <label class="radio-inline">
                                        <input type="radio" name="check_from_btn" id="radio3" checked value="from_company">Company
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="check_from_btn" id="radio4" value="from_employee">Employee
                                    </label>
                            </div> 

                            <div class="row asc" id="from_company">
                                <div class="form-group"> 
                                    <label>Company</label> 
                                    <select class="form-control" name="company_id" id="company_id">
                                        <option value="">Select Company</option>
                                        @foreach($Companies as $company_list_data)
                                        <option value="{{ $company_list_data->id }}">{{ $company_list_data->company_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row asc" style="display:none" id="from_employee">
                                <div class="form-group"> 
                                <label>Employee</label> 
                                    <select class="form-control" name="employee_id" id="employee_id">
                                        <option value="">Select Employee</option>
                                        @foreach($users_data as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <h4>To Transfer:</h4>
                            <div class="form-group">
                                    <label class="radio-inline">
                                        <input type="radio" name="check_btn" id="radio1" checked value="company_list">Company
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="check_btn" id="radio2" value="employee_list">Employee
                                    </label>
                            </div>  
    
                            <div class="row desc" id="company_list">
                                <div class="form-group"> 
                                    <label>Company</label> 
                                    <select class="form-control account_id"  name="to_company_id" id="to_company_id">
                                        <option value="">Select Company</option>
                                        @foreach($Companies as $company_list_data)
                                        <option value="{{ $company_list_data->id }}">{{ $company_list_data->company_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row desc" style="display:none" id="employee_list">
                                    <div class="form-group"> 
                                        <label>Company</label> 
                                        <select class="form-control" name="emp_company_id"  id="emp_company_id">
                                        <option value="">Select Company</option>
                                            @foreach($Companies as $company_list_data)
                                            <option value="{{ $company_list_data->id }}">{{ $company_list_data->company_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    
                                    <div class="form-group"> 
                                        <label>Client</label> 
                                        <select class="form-control" name="client_id"  id="client_id">
                                            
    
                                        </select>
                                    </div>

                                    <div class="form-group"> 
                                        <label>Project</label> 
                                        <select class="form-control" name="project_id"  id="project_id">
                                            <option value="">Select Project</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group"> 
                                        <label>Employee</label> 
                                        <select class="form-control account_id"  name="to_employee_id" id="to_employee_id">
                                            <option value="">Select Employee</option>
                                            @foreach($users_data as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                            </div>
                            
                            <div class="row">
                                <div class="form-group "> 
                                    <label>Amount</label> 
                                    <input type="number" class="form-control" name="balance" id="balance" value="" /> 
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success" id="submit_form">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.cash_transfer_list') }}'" class="btn btn-default">Cancel</button>
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
    $("input[name='check_from_btn']").click(function() {
            var test = $(this).val();


            $("div.asc").hide();
            $("#" + test).show();

        });
    $("input[name='check_btn']").click(function() {
            var test = $(this).val();


            $("div.desc").hide();
            $("#" + test).show();

        });

    $(document).ready(function(){
        $('#employee_id').select2();
        $('#company_id').select2();
        $('.account_id').select2();
        $('#client_id').select2();
        $('#project_id').select2();
        $('#emp_company_id').select2();
       
        jQuery("#insert_sender_frm").validate({
                ignore:  [],
                rules: {
                    company_id: {
                        required: function(element){
                            return $("#radio3").prop("checked");
                        }
                    },
                    employee_id: {
                        required: function(element){
                            return $("#radio4").prop("checked");
                        },
                    },
                    balance: {
                        required: true
                    },
                    to_company_id: {
                        required: function(element){
                            return $("#radio1").prop("checked");
                        },
                    },
                    emp_company_id: {
                        required: function(element){
                            return $("#radio4").prop("checked");
                        },
                    },
                    client_id: {
                        required: function(element){
                            return $("#radio2").prop("checked");
                        },
                    },
                    project_id: {
                        required: function(element){
                            return $("#radio2").prop("checked");
                        },
                    },
                    to_employee_id: {
                        required: function(element){
                            return $("#radio2").prop("checked");
                        },
                    }
                }
        });
    });

    //------------------------------------ AJAX CALL
    function client_list_api(companyId) {

        $.ajax({
            url: "{{ route('admin.get_company_client_list') }}",
            type: "POST",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: {company_id: companyId},
            dataType: "JSON",
            success: function(data) {

                $('#client_id').select2('destroy').empty().select2();
                $("#client_id").append("<option value='' selected>Select Client</option>");
                var client_html = "";
                $.each(data, function(index, clients_obj) {
                    if (clients_obj.id == 1) {
                        client_html +=  '<option value="' + clients_obj.id + '">' + clients_obj.client_name + '</option>';
                    }else{
                        client_html += '<option value="' + clients_obj.id + '">' + clients_obj.client_name + "-" + clients_obj.location + '</option>';
                    }
                });
                $("#client_id").append(client_html);
            }
        });
    }
    //------------------------------------Clients list
    
    $('#radio2, #company_id').click(function(){
        $('#project_id').select2('destroy').empty().select2();
        $('#client_id').select2('destroy').empty().select2();
        if ($("#radio2").prop("checked")) {
            var company_id = $("#company_id").val();
            if (company_id >= 1) {
                client_list_api(company_id);
            } 
        }
                    
    });
    //--------------------------------- project list
    $('#client_id').change((e) => {
        
        client_id = $("#client_id").val();
        if (client_id >= 1) {
            $.ajax({
                url: "{{ route('admin.get_client_project_list') }}",
                type: "POST",
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {client_id: client_id},
                dataType: "JSON",
                success: function(data) {

                    $('#project_id').select2('destroy').empty().select2();
                    $("#project_id").append("<option value='' selected>Select Project</option>");
                    $.each(data, function(index, projects_obj) {
                        $("#project_id").append('<option value="' + projects_obj.id + '">' + projects_obj.project_name + '</option>');
                    });
                }
            });
        }
    });

    
</script>
@endsection
