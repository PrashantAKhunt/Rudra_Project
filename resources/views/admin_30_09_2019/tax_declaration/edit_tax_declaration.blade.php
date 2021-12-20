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
                        <form action="{{ route('admin.update_employee_loan') }}" id="update_employee_loan" method="post">
                            @csrf
                            <input type="hidden" id="id" name="id" value="{{ $employee_detail[0]->id }}" /> 
                            <div class="form-group "> 
                                @if(!empty($employee))
                                    <select name="user_id" class="form-control" id="select2">
                                    <option value="">Select Employee</option>
                                        @foreach($employee as $key => $value)
                                             <option value="{{$key}}" <?php echo ($employee_detail[0]->user_id==$key)?"selected='selected'":'' ?> >{{$value}}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>


                            <div class="form-group "> 
                                <label>Loan Amount</label> 
                                <input type="text" class="form-control" name="loan_amount" id="loan_amount" value="{{ $employee_detail[0]->loan_amount }}" /> 
                            </div>

                            <div class="form-group "> 
                                <label>Loan Expected Month</label> 
                                <?php
                                $currentMonth = date('m');
                                ?>
                                @if(!empty($emi_start_month))
                                    <select name="loan_expected_month" class="form-control" id="select2">
                                    <option value="">Select Loan Expected Month</option>
                                        @foreach($emi_start_month as $key => $value)
                                            @if($key>$currentMonth)   
                                            <option <?php echo ($employee_detail[0]->loan_expected_month==$key."-".date('Y'))?"selected='selected'":'' ?> value="{{$key}}<?php echo "-".date('Y')?>">{{$value}}<?php echo "-".date('Y')?></option>
                                            @endif
                                        @endforeach
                                    </select>
                                @endif
                                
                            </div>

                            <div class="form-group "> 
                                <label>Loan Emi Start From</label>
                                @if(!empty($emi_start_month))
                                    <select name="loan_emi_start_from" class="form-control" id="select2">
                                    <option value="">Select Loan Start Emi Month</option>
                                        @foreach($emi_start_month as $key => $value)
                                            @if($key>$currentMonth) 
                                            <option <?php echo ($employee_detail[0]->loan_emi_start_from==$key."-".date('Y'))?"selected='selected'":'' ?> value="{{$key}}<?php echo "-".date('Y')?>">{{$value}}<?php echo "-".date('Y')?></option>
                                            @endif
                                        @endforeach
                                    </select>
                                @endif 
                            </div>
                            
                            <div class="form-group "> 
                                <label>Loan Terms</label> 
                                <input type="text" class="form-control" name="loan_terms" id="loan_terms" value="{{ $employee_detail[0]->loan_terms }}"/>
                            </div>
                            
                            <div class="form-group "> 
                                <label>Loan Descption</label> 
                                <!-- <input type="text" class="form-control" name="loan_descption" id="loan_descption" value="{{ $employee_detail[0]->loan_descption }}" />  -->
                                <textarea class="form-control" name="loan_descption" id="loan_descption" rows="10">
                                    <?php echo $employee_detail[0]->loan_descption; ?>
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
        // jQuery('#loan_expected_month').datepicker({
        //     autoclose: true,
        //     todayHighlight: true,
        //     viewMode: "months",
        //     minViewMode: "months",
        //     format: "mm-yyyy"
        // });
        // jQuery('#loan_emi_start_from').datepicker({
        //     autoclose: true,
        //     todayHighlight: true,
        //     viewMode: "months",
        //     minViewMode: "months",
        //     format: "mm-yyyy"
        // });
    });
    jQuery("#update_employee_loan").validate({
        ignore: [],
        rules: {
            user_id: {
                required: true,
            },
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
            },
            status:{
                required: true,
            },
            loan_status:{
                required: true,
            }
        }
    });
      
</script>
@endsection
