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
                        <form action="{{ route('admin.insert_employee_loan') }}" id="insert_employee_loan" method="post">
                            @csrf
                            <!-- <div class="form-group "> 
                                @if(!empty($employee))
                                    <select name="user_id" class="form-control" id="select2">
                                    <option value="">Select Employee</option>
                                        @foreach($employee as $key => $value)
                                            <option value="{{$key}}">{{$value}}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div> -->

                            <div class="form-group "> 
                                <label>Loan Amount</label> 
                                <input type="text" class="form-control" name="loan_amount" id="loan_amount"/> 
                            </div>

                            <div class="form-group "> 
                                <label>Loan Expected Month</label> 
                                <?php
                                $currentMonth = date('m');
                                ?>
                               <!--  <input type="text" class="form-control" name="loan_expected_month" id="loan_expected_month"/>  -->
                                @if(!empty($emi_start_month))
                                    <select name="loan_expected_month" class="form-control" id="select2">
                                    <option value="">Select Loan Expected Month</option>
                                        @foreach($emi_start_month as $key => $value)
                                            @if($key>$currentMonth) 
                                            <option value="{{$key}}<?php echo "-".date('Y')?>">{{$value}}<?php echo "-".date('Y')?></option>
                                            @endif
                                        @endforeach
                                    </select>
                                @endif
                            </div>

                            <div class="form-group "> 
                                <label>Loan Emi Start From</label> 
                                <!-- <input type="text" class="form-control" name="loan_emi_start_from" id="loan_emi_start_from"/>  -->
                                @if(!empty($emi_start_month))
                                    <select name="loan_emi_start_from" class="form-control" id="select2">
                                    <option value="">Select Loan Start Emi Month</option>
                                        @foreach($emi_start_month as $key => $value)
                                            @if($key>$currentMonth)
                                            <option value="{{$key}}<?php echo "-".date('Y')?>">{{$value}}<?php echo "-".date('Y')?></option>
                                            @endif
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            
                            <div class="form-group "> 
                                <label>Loan Terms</label> 
                                <input type="text" class="form-control" name="loan_terms" id="loan_terms"/>
                            </div>
                            
                            <div class="form-group "> 
                                <label>Loan Descption</label> 
                                <!-- <input type="text" class="form-control" name="loan_descption" id="loan_descption"/> -->
                                <textarea class="form-control" name="loan_descption" id="loan_descption" rows="10">
                                    
                                </textarea> 
                            </div>

                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.employee_loan') }}'" class="btn btn-default">Cancel</button>
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
        // var x = 4; //or whatever offset
        // var CurrentDate = new Date();
        // console.log("Current date:", CurrentDate);
        // CurrentDate.setMonth(CurrentDate.getMonth() + x);

        // jQuery('#loan_expected_month').datepicker({
        //     startDate: CurrentDate,
        //     autoclose: true,
        //     monthHighlight: true,
        //     viewMode: "months",
        //     minViewMode: "months",
        //     format: "mm-yyyy"
        // });
        // jQuery('#loan_emi_start_from').datepicker({
        //     startDate: CurrentDate,
        //     autoclose: true,
        //     todayHighlight: true,
        //     viewMode: "months",
        //     minViewMode: "months",
        //     format: "mm-yyyy"
        // });
    });
    jQuery("#insert_employee_loan").validate({
        ignore: [],
        rules: {
            loan_amount: {
                required: true,
            },
            loan_expected_month:{
                required: true,
            },
            loan_emi_start_from:{
                required: true,
            },
            loan_terms:{
                required: true,
            },
            loan_descption:{
                required: true,
            }
        }
    });
      
</script>
@endsection
