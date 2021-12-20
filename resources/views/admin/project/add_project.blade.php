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
                        <form action="{{ route('admin.insert_project') }}" id="add_project" method="post">
                            @csrf
                            

                            <div class="form-group "> 
                                <label>Company <strong> <span class="text-danger">*</span></strong> </label> 
                                @if(!empty($companies))
                                    <select name="company_id" class="form-control" id="company_id">
                                    <option value="" disabled selected="">Select company</option>
                                        @foreach($companies as $key => $value)
                                            <option value="{{$key}}">{{$value}}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            
                            <div class="form-group "> 
                                <label>Client <strong> <span class="text-danger">*</span></strong> </label> 
                                    <select id="client_id" name="client_id" class="form-control" >
                                        <option value="">Select client</option>
                                    </select>
                            </div>
                            
                            <div class="form-group "> 
                                <label>Project Name <strong> <span class="text-danger">*</span></strong> </label> 
                                <input type="text" class="form-control" name="project_name" id="project_name" value="" /> 
                            </div>
                            
                            <div class="form-group "> 
                                <label>Project Manager <strong> <span class="text-danger">*</span></strong> </label>
                                <select class="form-control project_manager_id" id="project_manager_id" name="project_manager_id"  data-tabindex="1">
                                   <option value="">Select Project Manager</option>
                                    @foreach($users as $key => $value)
                                    <option value="{{$key}}">{{$value}}</option>
                                    @endforeach
                                </select> 
                            </div>
                            <div class="form-group "> 
                                <label>Expense Manager <strong> <span class="text-danger">*</span></strong> </label>
                                <select class="form-control expense_manager_id" id="expense_manager_id" name="expense_manager_id"  data-tabindex="1">
                                   <option value="">Select Expense Manager</option>
                                    @foreach($users as $key => $value)
                                    <option value="{{$key}}">{{$value}}</option>
                                    @endforeach
                                </select> 
                            </div>
                            <div class="form-group "> 
                                <label>Project Support Employee <strong> <span class="text-danger">*</span></strong> </label>
                                <select class="select2 m-b-10 select2-multiple project_employee_id" multiple="multiple" data-placeholder="Select Project Support Employee" id="project_employee_id" name="project_employee_id[]"  data-tabindex="1">
                                    @foreach($users as $key => $value)
                                    <option value="{{$key}}">{{$value}}</option>
                                    @endforeach
                                </select> 
                            </div>
                            <div class="form-group "> 
                                <label>Project Location <strong> <span class="text-danger">*</span></strong> </label> 
                                <input type="text" class="form-control" name="project_location" id="project_location" value="" /> 
                            </div>
                            <div class="form-group ">
                                <label>Project Details <strong> <span class="text-danger">*</span></strong> </label>
                                <textarea class="form-control" rows="10" name="details" id="details" ></textarea>
                            </div>

                            
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.projects') }}'" class="btn btn-default">Cancel</button>
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
    
    $(document).ready(function () {
        $("#project_manager_id,#project_employee_id,#expense_manager_id").select2({
            allowClear: false,
            minimumResultsForSearch: 0,
            theme: 'bootstrap',
            width: '100%'
        });
        $("#company_id").change(function () {
            
            var company_id = $("#company_id").val();

            $.ajax({
                url: "{{ route('admin.get_clientlist_by_company')}}",
                type: 'get',
                data: "company_id=" + company_id,
                success: function (data, textStatus, jQxhr) {
                    $('#client_id').empty();
                    $('#client_id').append(data);
                },
                error: function (jqXhr, textStatus, errorThrown) {
                    console.log(errorThrown);
                }
            });
        });
    });
    $('form#add_project').on('submit', function(event) {
        $('.project_manager_id').each(function() {
            $(this).rules('add', {
                required: true,
            });
        });
        $('.expense_manager_id').each(function() {
            $(this).rules('add', {
                required: true,
            });
        });
        $('.project_employee_id').each(function() {
            $(this).rules('add', {
                required: true,
            });
        });
    });
    jQuery("#add_project").validate({
        ignore: [],
        rules: {
            project_name: {
                required: true,
                remote:{
                    url:"{{ route('admin.checkProjectName') }}",
                    type:"post",
                    data:{
                        "_token":"{{ csrf_token() }}",
                        "name":function(){
                            return $('#project_name').val()
                        },
                        "client_id":function(){
                            return $('#client_id').val();
                        }
                    }
                },
            },
            client_id: {
                required: true,
            },
            details:{
                required: true,
            },
            company_id:{
                required: true,
            },
            project_location:{
                required: true,
            },
        },
        messages:{
            project_name:{
                remote:"Project name already exists for selected client."
            }
        }
    });
      
</script>
@endsection
