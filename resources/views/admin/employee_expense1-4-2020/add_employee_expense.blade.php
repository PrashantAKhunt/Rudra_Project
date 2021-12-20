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
                        <form action="{{ route('admin.insert_employee_expense') }}" id="insert_employee_expense_frm" method="post" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group "> 
                                <label>Select Company</label>
                                <select class="form-control" name="company_id" id="company_id">
                                    <option value="">Select Company</option>
                                    @foreach($Companies as $company_list_data)
                                    <option value="{{ $company_list_data->id }}">{{ $company_list_data->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group ">
                                <label>Select Client</label>
                                <select class="form-control" id="client_id" name="client_id">
                                    <option value="">Select Client</option>
                                </select>
                            </div>

                            <div class="form-group ">
                                <label>Select Project</label>
                                <select class="form-control" id="project_id" name="project_id">
                                    <option value="">Select Project</option>
                                </select>
                            </div>
                            <div class="form-group ">
                                <label>Select Project Site</label>
                                <select class="form-control" id="project_site_id" name="project_site_id">
                                    <option value="">Select Site</option>
                                </select>
                            </div>
                            <div class="form-group "> 
                                <label>Select Expense Type</label>
                                <select class="form-control" name="expense_main_category" id="expense_main_category">
                                    <option value="Office Miscellaneous Expense">Office Miscellaneous Expense</option>
                                    <option value="Site Expense">Site Expense</option>
                                </select>
                            </div>

                            <div class="form-group" id="other_project_txt" style="display:none;"> 
                                <label>Other Detail</label>
                                <input type="text" class="form-control" name="other_project" id="other_project"/> 
                            </div>

                            <div class="form-group "> 
                                <label>Select Expense Category</label>
                                <select class="form-control" name="expense_category" id="expense_category">
                                    <option value="">Select Expense Category</option>
                                    @foreach($Expense_List as $asset_list_data)
                                    <option value="{{ $asset_list_data->id }}">{{ $asset_list_data->category_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!--  <div class="form-group "> 
                                 <label>User Name</label>
                                 <select class="form-control" name="user_id" id="user_id">
                                     <option value="">Select User</option>
                                     @foreach($UsersName as $users_name_data)
                                     <option value="{{ $users_name_data->id }}">{{ $users_name_data->name }}</option>
                                     @endforeach
                                 </select>
                             </div> -->
                            <div class="form-group "> 
                                <label>Title</label> 
                                <input type="text" class="form-control" name="title" id="title" value="" /> 
                            </div>
                            <div class="form-group "> 
                                <label>Bill Number</label> 
                                <input type="text" class="form-control" name="bill_number" id="bill_number" value="" /> 
                            </div>
                            <div class="form-group "> 
                                <label>Merchant Name</label> 
                                <input type="text" class="form-control" name="merchant_name" id="merchant_name" value="" /> 
                            </div>
                            <div class="form-group "> 
                                <label>Amount</label> 
                                <input type="text" class="form-control" name="amount" id="amount" value="" /> 
                            </div>
                            <div class="form-group "> 
                                <label>Expense Date</label> 
                                <input type="text" class="form-control" name="expense_date" id="expense_date" value="" /> 
                            </div>
                            <div class="form-group "> 
                                <label>Comment</label> 
                                <input type="text" class="form-control" name="comment" id="comment" value="" /> 
                            </div>
                            <div class="form-group "> 
                                <label>Voucher Number</label> 
                                <input type="text" class="form-control" name="voucher_no" id="voucher_no" value="" /> 
                            </div>
                            <div class="form-group ">
                                <label>Expense Image</label>
                                <input type="file" accept="image/png,image/x-png, image/jpg, image/jpeg" name="image" id="image" class="dropify" />
                            </div>

                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.employee_expense') }}'" class="btn btn-default">Cancel</button>
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

        $("#company_id").change(function () {

            var company_id = $("#company_id").val();

//Clients list:
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

            // $.ajax({
            //     url: "{{ route('admin.get_expense_project_list')}}",
            //     type: 'get',
            //     data: "company_id=" + company_id,
            //     success: function (data, textStatus, jQxhr) {
            //         $('#project_id').empty();
            //         $('#project_id').append(data);
            //     },
            //     error: function (jqXhr, textStatus, errorThrown) {
            //         console.log(errorThrown);
            //     }
            // });

        });

        $('#client_id').change(() => {
            
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

        $("#project_id").change(function () {
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

            if (project_id == 1) {
                $("#other_project").val("");
                $("#other_project_txt").show();
            } else {
                $("#other_project_txt").hide();
            }
        });


        jQuery('#expense_date').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: "dd-mm-yyyy"
        });

        //$('#expense_category').select2();

        $('#insert_employee_expense_frm').validate({
            rules: {

                expense_category: {
                    required: true
                },
                title: {
                    required: true
                },
                company_id: {
                    required: true
                },
                client_id: {
                required: true
            },
            project_site_id: {
                required: true
            },
                project_id: {
                    required: true
                },
                bill_number: {
                    required: true
                },
                merchant_name: {
                    required: true
                },
                amount: {
                    required: true,
                    number: true
                },
                expense_date: {
                    required: true
                },
                comment: {
                    required: true
                },
                expense_image: {
                    required: true
                }
            },
        submitHandler: function (form) {
            // Prevent double submission
            if (!this.beenSubmitted) {
                this.beenSubmitted = true;
                form.submit();
            }
        }
        })
    });
</script>
@endsection
