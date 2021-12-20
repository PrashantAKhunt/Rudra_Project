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
                        <form action="{{ route('admin.insert_emplyee_cash_transfer') }}" id="insert_sender_frm" method="post">
                            @csrf
                            <div class="row">
                                <div class="form-group"> 
                                    <label>My Current Balance <span class="error">*</span> </label> 
                                    <input type="text" class="form-control" name="emp_balance" id="emp_balance" value="{{$emp_balance}}" readonly>
                                </div>
                            </div>
                            <h4>To Transfer:</h4>
                            <div class="form-group">
                                    <label class="radio-inline">
                                        <input type="radio" name="check_btn" id="radio1" checked value="company_list">Company
                                    </label>
                                    <label class="radio-inline">
                                        <input type="radio" name="check_btn" id="radio2" value="employee_list">Other Employee
                                    </label>
                            </div>  
    
                            <div class="row" id="company_list">
                                <div class="form-group"> 
                                    <label>Company <span class="error">*</span> </label> 
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
                                        <label>Client</label> 
                                        <select class="form-control" name="client_id"  id="client_id">
                                        <option value="">Select Client</option>
    
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
                                    <label>Amount <span class="error">*</span> </label> 
                                    <input type="number" class="form-control" name="balance" id="balance" value="" /> 
                                </div>
                            </div>

                            <div class="row">
                                    
                                <div class="form-group ">
                                    <label>Transfer Note <span class="error">*</span> </label>
                                    <textarea class="form-control" rows="3" name="txn_note" id="txn_note" ></textarea>
                                </div>
                               
                            </div>

                            <button type="submit" class="btn btn-success" id="submit_form">Submit</button>
                            <!-- <button type="button" onclick="window.location.href ='{{ route('admin.cash_transfer_list') }}'" class="btn btn-default">Cancel</button> -->
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
    $("input[name='check_btn']").click(function() {
            var test = $(this).val();

            $('#to_company_id').select2("val", null);
            $("div.desc").hide();
            $("#" + test).show();

        });

    $(document).ready(function(){
      
        $('.account_id').select2();
        $('#client_id').select2();
        $('#project_id').select2();
       
        jQuery("#insert_sender_frm").validate({
                ignore:  [],
                rules: {
                    emp_balance: {
                        required: true,
                        min:1
                    },
                    txn_note: {
                        required:true
                    },
                    balance: {
                        required: true,
                        range: [1, $("#emp_balance").val()]
                        // min:1,
                        // max:$("#emp_balance").val()
                    },
                    to_company_id: {
                        required: true
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
                },
                messages: {
                    emp_balance: "Oops.. Your Current Balance is Zero , So You can't tranfer!",
                    balance:"Amount Should be less or equal to your current balance!"
                }
        });
    });

    // ---------------------------- Balance
    // $('#balance').change((e) => {
    //     let currBalance = $("#emp_balance").val() - $("#balance").val();
    //     $("#emp_balance").val(currBalance)
    // });

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
                        // client_html +=  '<option value="' + clients_obj.id + '">' + clients_obj.client_name + '</option>';
                    }else{
                        client_html += '<option value="' + clients_obj.id + '">' + clients_obj.client_name + "-" + clients_obj.location + '</option>';
                    }
                });
                $("#client_id").append(client_html);
            }
        });
    }
    //------------------------------------Clients list
    
    $('#to_company_id').click(function(){
        $('#project_id').select2('destroy').empty().select2();
        $('#client_id').select2('destroy').empty().select2();
        if ($("#radio2").prop("checked")) {
            var company_id = $("#to_company_id").val();
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
