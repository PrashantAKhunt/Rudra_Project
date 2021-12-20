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
                        <form action="{{ route('admin.insert_employee_salary') }}" id="insert_employee_salary" method="post">
                            @csrf
                            <div class="form-group "> 
                            <label>Select Employee <span class="error">*</span> </label>
                                @if(!empty($employee))
                                <select name="user_id" class="form-control" id="select2">
                                    <option value="">Select Employee</option>
                                    @foreach($employee as $key => $value)
                                    <option value="{{$key}}">{{$value}}</option>
                                    @endforeach
                                </select>
                                @endif
                            </div>

                            <div class="form-group "> 

                                <select name="salaray_category" class="form-control" id="salary_category">

                                    <option value="1">Category 1</option> 
                                    <option value="2">Category 2</option> 									
                                </select>

                            </div>

                            <div class="form-group "> 
                                <label>Total Salary (Per Month) <span class="error">*</span> </label> 
                                <input type="text" class="form-control" name="total_salary" id="total_salary" value="" /> 
                            </div>
                            <div class="form-group "> 
                                <label>Basic Salary <span class="error">*</span> </label> 
                                <input type="text" class="form-control" name="basic_salary" id="basic_salary" value="" /> 
                            </div>

                            <div class="form-group "> 
                                <label>HRA <span class="error">*</span> </label> 
                                <input type="text" class="form-control" name="hra" id="hra" value="" /> 
                            </div>

                            <div class="form-group "> 
                                <label>Other Allowance <span class="error">*</span> </label> 
                                <input type="text" class="form-control" name="other_allowance" id="other_allowance" value="" /> 
                            </div>

                            <div class="form-group "> 
                                <label>Salary Month <span class="error">*</span> </label> 
                                <input type="text" class="form-control" name="salary_month" id="salary_month" value="" /> 
                            </div>

                            <div class="form-group "> 
                                <label>Salary Year <span class="error">*</span> </label> 
                                <input type="text" class="form-control" name="salary_year" id="salary_year" value="" /> 
                            </div>

                            <div class="form-group "> 
                                <label>Professional Tax</label> 
                                <input type="text" class="form-control" name="professional_tax" id="professional_tax"/> 
                            </div>

                            <div class="form-group "> 
                                <label>Employee PF Deduction</label> 
                                <input type="text" class="form-control" name="PF_amount" value="0.00" id="PF_amount"/> 
                            </div>
                            
                            <div class="form-group "> 
                                <label>Employer PF Deduction</label> 
                                <input type="text" class="form-control" name="employer_pf_amount" value="0.00" id="employer_pf_amount"/> 
                            </div>

                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.employee_salary') }}'" class="btn btn-default">Cancel</button>
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
        jQuery('#salary_year').datepicker({
            autoclose: true,
            todayHighlight: true,
            viewMode: "years",
            minViewMode: "years",
            format: "yyyy"
        });
        jQuery('#salary_month').datepicker({
            autoclose: true,
            todayHighlight: true,
            viewMode: "months",
            minViewMode: "months",
            format: "mm"
        });
    });
    jQuery("#insert_employee_salary").validate({
        ignore: [],
        rules: {
            user_id: {
                required: true,
            },
            total_salary: {
                required: true,
                number: true
            },
            basic_salary: {
                required: true,
            },
            hra: {
                required: true,
            },
            other_allowance: {
                required: true,
            },
            salary_month: {
                required: true,
            },
            salary_year: {
                required: true,
            }
        }
    });


    $("#total_salary").keyup(function (event) {
        var d = new Date();
        var n = d.getMonth();
        var y = new Date().getFullYear();

        var category = $("#salary_category").val();
        var $tatalsalaray = $("#total_salary").val();
        if (category == 1) {
            $("#basic_salary").val((($tatalsalaray * 60) / 100).toFixed(2));
            $("#hra").val((($tatalsalaray * 24) / 100).toFixed(2));
            $("#other_allowance").val((($tatalsalaray * 16) / 100).toFixed(2));
            $("#salary_month").val(n);
            $("#salary_year").val(y);
            $("#professional_tax").val(200);
            $("#PF_amount").val(0);



        } else if (category == 2) {
            $("#basic_salary").val((($tatalsalaray * 31) / 100).toFixed(2));
            $("#hra").val((($tatalsalaray * 53) / 100).toFixed(2));
            $("#other_allowance").val((($tatalsalaray * 16) / 100).toFixed(2));
            $("#salary_month").val(n);
            $("#salary_year").val(y);
            $("#professional_tax").val(200);
            $("#PF_amount").val(0);


        }
    });

    $("#salary_category").change(function () {

        $('#total_salary').trigger('keyup');
    });



</script>
@endsection
