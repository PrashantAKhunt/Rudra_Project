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
                        <div class="form-group" style="color:red;">
                            <div class="col-sm-12">
                                <label>Loan Amount Note : </label> You can apply maximum loan is : {{ $normal_loan_amount }}
                            </div>
                        </div>
<!--                        <div class="form-group" style="color:red;">
                            <div class="col-sm-12">
                                <label>Loan Installment Note : </label> Maximum installment should be : {{ round(($normal_loan_amount/12),2) }}
                            </div>                            
                        </div>-->
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
                            <div class="col-sm-6">
                                <label>Loan Type</label> 
                                <select name="loan_type" class="form-control" id="loan_type" readonly>
                                    <option value="2">Normal Loan</option>
                                    <!-- <option value="1">Advance Salary</option> -->
                                </select>
                            </div>
                            <div class="col-sm-6" id="loan_amount">
                                <label>Loan Amount <span class="error">*</span></label> 
                                <input type="number" class="form-control" name="loan_amount" id="loan_amount" max="{{ $normal_loan_amount }}" />
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="col-sm-6" style="display:none;" id="loan_terms">
                                <label>Loan Terms (Number of Month)</label>
                                <select name="loan_terms" class="form-control" id="loan_terms">
                                    @foreach($terms as $value)
                                        <option value="{{ $value }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6" id="loan_expected_month">
                                <label>Loan Expected Month</label>                                 
                                <select name="loan_expected_month" class="form-control" id="loan_expected_month">                                    
                                    <?php $currentMonth = (int)date('m'); for ($x = $currentMonth; $x < $currentMonth + 12; $x++) { ?>
                                        <option value="<?php echo date('m-Y', mktime(0, 0, 0, $x, 1)); ?>"><?php echo date('m-Y', mktime(0, 0, 0, $x, 1)); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>                            
                        </div>
                        <div class="form-group ">                            
                            <div class="col-sm-12">
                                <label>Loan Descption <span class="error">*</span></label>
                                <textarea class="form-control" name="loan_descption" id="loan_descption" rows="4"></textarea> 
                            </div>
                        </div>
                        <div class="form-group ">
                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-success submit-form">Submit</button>
                                <button type="button" onclick="window.location.href ='{{ route('admin.employee_loan') }}'" class="btn btn-default">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
   $(document).ready(function () {        
        $("#loan_type").change(function(){
            if($(this).val() == 1){
                var loanAmount = '<?php echo round($advance_loan_amount,2);  ?>';
                $('#loan_expected_month option').attr('disabled',true);
                $('#loan_expected_month option:first').attr('disabled',false);
                $('#loan_expected_month option:first').attr('selected',true);
                $('#loan_terms option').attr('disabled',true);
                $('#loan_terms option:first').attr('disabled',false);
                $('#loan_terms option:first').attr('selected',true);
            }else{
                var loanAmount = '<?php echo round($normal_loan_amount,2);  ?>';
                $('#loan_expected_month option').attr('disabled',false);
                $('#loan_terms option').attr('disabled',false);
            }
            $('#loan_amount').attr('max',loanAmount);
        });

        $("#loan_terms").change(function(){
            //var maxInstAmount = '<?php //echo round(($normal_loan_amount/12),2);  ?>';
            var maxInstAmount = '<?php echo round($employeeSalary->total_month_salary,2);  ?>';
            if($(this).val() != '' && $("#loan_amount").val() != ''){
                if($("#loan_type").val() == 1){
                    var instAmount = $("#loan_amount").attr('max');
                }else{
                    var instAmount = ($("#loan_amount").val()/$(this).val()).toFixed(2);
                }
                if((parseInt(maxInstAmount)).toFixed(2) < parseInt(instAmount)){
                    swal({
                        title: "Your installment "+instAmount+" is grater than your gross salary "+maxInstAmount,
                        type: "warning",
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Ok",
                        closeOnConfirm: true
                    });
                    $('.submit-form').addClass('hide');
                }else{
                    $('.submit-form').removeClass('hide');
                }
            }
        });
        $("#loan_amount").change(function(){
            //var maxInstAmount = '<?php //echo $normal_loan_amount/12;  ?>';
            var maxInstAmount = '<?php echo round($employeeSalary->total_month_salary,2);  ?>';
            if($(this).val() != '' && $("#loan_terms").val() != ''){
                if($("#loan_type").val() == 1){
                    var instAmount = $("#loan_amount").attr('max');
                }else{
                    var instAmount = ($("#loan_amount").val()/$('#loan_terms').val()).toFixed(2);
                }
                if((parseInt(maxInstAmount)).toFixed(2) < parseInt(instAmount)){                    
                    swal({
                        title: "Your installment "+instAmount+" is grater than your gross salary "+maxInstAmount,
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Ok",
                        closeOnConfirm: true
                    });
                    $('.submit-form').addClass('hide');
                }else{
                    $('.submit-form').removeClass('hide');
                }
            }
        });
    });
    jQuery("#insert_employee_loan").validate({
        ignore: [],
        rules: {
            loan_type: {
                required: true,
            },
            loan_amount: {
                required: true,
            },
            loan_expected_month:{
                required: true,
            },
            loan_terms:{
                required: true,
            },
            loan_descption:{
                required: true,
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

<!-- hide show for advance salary  -->
<script>
$(document).ready(function(){
    $('#loan_type').trigger('change');
});
$('#loan_type').on('change', function() {
        if ( this.value == '2')
        {
            $("#loan_terms").show();
            $("#loan_amount").val("");
            $("#loan_amount").show();
            $("#loan_expected_month").show();
        }
        else
        {
            $("#loan_terms").hide();
            $("#loan_amount").val(0);
            $("#loan_amount").hide();
            $("#loan_expected_month").hide();
        }
    });
</script>
<!-- hide show for advance salary  -->
<!-- hide show for advance salary  -->
<script>
</script>
<!-- hide show for advance salary changge value to 0  -->
@endsection
