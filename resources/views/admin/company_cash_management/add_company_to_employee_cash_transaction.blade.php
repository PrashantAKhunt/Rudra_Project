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
                        <form action="{{ route('admin.insert_company_to_employee_cash_transfer') }}" id="insert_sender_frm" method="post">
                            @csrf
                            <div class="row">
                                <div class="form-group"> 
                                    <label>Company <span class="error">*</span> </label> 
                                    <select class="form-control" name="company_id" id="company_id">
                                        <option value="">Select Company</option>
                                        @foreach($Companies as $company_list_data)
                                        <option value="{{ $company_list_data->id }}">{{ $company_list_data->company_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            

                            <div class="row" id="employee_list">
                                    <div class="form-group"> 
                                        <label>Client <span class="error">*</span> </label> 
                                        <select class="form-control" name="client_id"  id="client_id">
                                        <option value="">Select Client</option>
    
                                        </select>
                                    </div>

                                    <div class="form-group"> 
                                        <label>Project <span class="error">*</span> </label> 
                                        <select class="form-control" name="project_id"  id="project_id">
                                            <option value="">Select Project</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group"> 
                                        <label>Employee <span class="error">*</span> </label> 
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
                                    <label>Amount <span class="error">*</span> </label> 
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

    $(document).ready(function(){
        $('#company_id').select2();
        $('.account_id').select2();
        $('#client_id').select2();
        $('#project_id').select2();
       
        jQuery("#insert_sender_frm").validate({
                ignore:  [],
                rules: {
                    company_id: {
                        required: true,
                    },
                    balance: {
                        required: true
                    },
                    client_id: {
                        required:true
                    },
                    project_id: {
                        required:true
                    },
                    to_employee_id: {
                        required:true
                    }
                }
        });
    });

    //------------------------------------ AJAX CALL

    $('#company_id').change((e) => {
        companyId = $("#company_id").val();
        $.ajax({
            url: "{{ route('admin.get_company_client_list') }}",
            type: "POST",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: {company_id: companyId},
            dataType: "JSON",
            success: function(data) {

                $('#client_id').select2('destroy').empty().select2();
                $("#client_id").append("<option value='' selected>Select Client</option>");
                $('#project_id').select2('destroy').empty().select2();
                $("#project_id").append("<option value='' selected>Select Project</option>");
                var client_html = "";
                $.each(data, function(index, clients_obj) {
                    if (clients_obj.id == 1) {
                        // client_html +=  '<option value="' + clients_obj.id + '">' + clients_obj.client_name + '</option>';
                    }else{
                        client_html += '<option value="' + clients_obj.id + '">' + clients_obj.client_name + "-" + clients_obj.location + '</option>';
                    }
                });
                $("#client_id").append(client_html);
            }
        });
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
                        if(projects_obj.project_name != "Other Project"){
                            $("#project_id").append('<option value="' + projects_obj.id + '">' + projects_obj.project_name + '</option>');
                        }
                    });
                }
            });
        }
    });

    
</script>
@endsection
