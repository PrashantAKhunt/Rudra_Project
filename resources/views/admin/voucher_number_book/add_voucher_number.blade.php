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
                        <form action="{{ route('admin.insert_voucher_number') }}" id="add_voucher" method="post">
                            @csrf
                            <div class="form-group "> 
                                <label>Company<span class="error">*</span></label> 
                                @if(!empty($companies))
                                    <select name="company_id" class="form-control" id="company_id">
                                    <option value="">Select company</option>
                                        @foreach($companies as $key => $value)
                                            <option value="{{$key}}">{{$value}}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            {{-- <div class="form-group ">
                                <label>Select Client<span class="error">*</span></label>
                                <select class="form-control" id="client_id" name="client_id">
                                    <option value="">Select Client</option>
                                </select>
                            </div>

                            <div class="form-group ">
                                <label>Select Project<span class="error">*</span></label>
                                <select class="form-control" id="project_id" name="project_id">
                                    <option value="">Select Project</option>
                                </select>
                            </div>
                            <div class="form-group ">
                                <label>Select Project Site<span class="error">*</span></label>
                                <select class="form-control" id="project_site_id" name="project_site_id">
                                    <option value="">Select Site</option>
                                </select>
                            </div> --}}
                            <!-- <div class="form-group ">
                                <label>Bank Name</label>
                                <select class="form-control" id="bank_id" name="bank_id">
                                    <option value="">Select bank</option>
                                </select>
                            </div> -->
                            <div class="form-group "> 
                                <label>Assign User<span class="error">*</span></label> 
                                @if(!empty($users))
                                    <select name="user_id" class="form-control" id="user_id">
                                    <option value="">Select User</option>
                                        @foreach($users as $key => $value)
                                            <option value="{{$key}}">{{$value}}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            <div class="form-group ">
                                <label>Voucher Start Number<span class="error">*</span></label>
                                 <input type="number" class="form-control" name="voucher_start_number" id="voucher_start_number" value="" /> 
                            </div>
                            <div class="form-group ">
                                <label>Voucher End Number<span class="error">*</span></label>
                                 <input type="number" class="form-control" name="voucher_end_number" id="voucher_end_number" value="" /> 
                            </div>
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.voucher_number_book') }}'" class="btn btn-default">Cancel</button>
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
       $("#user_id").select2();
        /* $("#company_id").change(function() {

            var company_id = $("#company_id").val();
            
            htmlStr = '';
                 $.ajax({
                    url: "{{ route('admin.get_company_client_list') }}",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        company_id: company_id
                    },
                    dataType: "JSON",
                    success: function(data) {

                        $("#client_id").empty();
                        $("#client_id").append("<option value='' selected>Select Client</option>");
                        $.each(data, function(index, clients_obj) {
                            
                            if (clients_obj.id == 1) {
                                htmlStr +=  '<option value="' + clients_obj.id + '">' + clients_obj.client_name + '</option>';
                            }else{
                                htmlStr += '<option value="' + clients_obj.id + '">' + clients_obj.client_name + "-" + clients_obj.location + '</option>';
                            }
                            
                            //$("#client_id").append('<option value="' + clients_obj.id + '">' + clients_obj.client_name + "-" + clients_obj.location + '</option>');
                        });
                        $("#client_id").append(htmlStr);

                        
                    }
                });

        }); */

        /* $('#client_id').on('change',function(){
            //project list
            client_id = $("#client_id").val();

            $.ajax({
                url: "{{ route('admin.get_client_project_list') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    client_id: client_id
                },
                dataType: "JSON",
                success: function(data) {
                    $("#project_id").empty();
                    $("#project_id").append("<option value='' selected>Select Project</option>");

                    $.each(data, function(index, projects_obj) {

                        $("#project_id").append('<option value="' + projects_obj.id + '">' + projects_obj.project_name + '</option>');

                    });
                }
            });
        });

        $("#project_id").on('change',function () {
            var project_id = $("#project_id").val();
            $.ajax({
                url: "{{ route('admin.get_project_sites_list') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    project_id: project_id
                },
                dataType: "JSON",
                success: function(data) {
                    $("#project_site_id").empty();
                    $("#project_site_id").append("<option value='' selected>Select Site</option>");
                    $.each(data, function(index, project_site_obj) {
                        $("#project_site_id").append('<option value="' + project_site_obj.id + '">' + project_site_obj.site_name + '</option>');
                    })
                    
                }
            });
        }); */
   });

    jQuery("#add_voucher").validate({
        ignore: [],
        rules: {
            company_id: {
                required: true,
            },
            /* client_id:{
                required: true,
            },
            project_id:{
                required: true,
            },
            project_site_id:{
                required: true,
            }, */
            user_id : {
                required : true,
            },
            voucher_start_number:{
                required: true,
            },
            voucher_end_number:{
                required: true,
                min: function() {
                    return parseInt($('#voucher_start_number').val());
                }
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
      
</script>
@endsection
