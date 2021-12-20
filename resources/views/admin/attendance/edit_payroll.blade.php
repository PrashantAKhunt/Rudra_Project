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
            <form action="{{ route('admin.update_payroll') }}" id="edit_payroll" method="post">
                <input type="hidden" id="id" name="id" value="{{ $payroll_detail[0]->id }}" />
                @csrf
                <div class="white-box">           
                    <div class="row">
                        <div class="col-sm-6 col-xs-6">
                            <div class="form-group ">
                                <label>User</label>
                                <input type="text" class="form-control" name="subject" id="subject" value="{{ $payroll_detail[0]->user->name }}" readOnly="true" />
                            </div>
                        </div>
                        <div class="col-sm-3 col-xs-3">
                            <div class="form-group ">
                                <label>Month</label>
                                <input type="number" class="form-control" name="month" id="month" value="{{ $payroll_detail[0]->month }}" readOnly="true" />
                            </div>
                        </div>
                        <div class="col-sm-3 col-xs-3">
                            <div class="form-group ">
                                <label>Year</label>
                                <input type="number" class="form-control" name="year" id="year" value="{{ $payroll_detail[0]->year }}" readOnly="true" />
                            </div>
                        </div>
                    </div>

                    <div class="row">                        
                        <div class="col-sm-6 col-xs-6">                        
                            <div class="form-group ">
                                <label>Basic Salary</label>
                                <input type="number" class="form-control" name="basic_salary" id="basic_salary" value="{{ $payroll_detail[0]->basic_salary }}" readOnly="true" />
                            </div>
                        </div>
                        <div class="col-sm-6 col-xs-6">
                            <div class="form-group ">
                                <label>HRA</label>
                                <input type="number" readonly="" class="form-control" name="hra" id="hra" value="{{ $payroll_detail[0]->hra }}" />
                            </div>
                        </div>
                    </div>

                    <div class="row">                        
                        <div class="col-sm-6 col-xs-6">                        
                            <div class="form-group ">
                                <label>Others</label>
                                <input type="number" readonly="" class="form-control" name="others" id="others" value="{{ $payroll_detail[0]->others }}" />
                            </div>
                        </div>
                        <div class="col-sm-6 col-xs-6">
                            <div class="form-group ">
                                <label>Food Allowance</label>
                                <input type="number" class="form-control" name="food" id="food" value="{{ $payroll_detail[0]->food }}" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6 col-xs-6">
                            <div class="form-group ">
                                <label>Working Day</label>
                                <input type="number" class="form-control" name="working_day" id="working_day" value="{{ $payroll_detail[0]->working_day }}" readOnly="true" />
                            </div>
                        </div>
                        <div class="col-sm-6 col-xs-6">                        
                            <div class="form-group ">
                                <label>Employee Working day</label>
                                <input type="number" class="form-control" name="employee_working_day" id="employee_working_day" value="{{ $payroll_detail[0]->employee_working_day }}" readOnly="true" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6 col-xs-6">
                            <div class="form-group ">
                                <label>Total Leave</label>
                                <input type="number" class="form-control" name="total_leave" id="total_leave" value="{{ $payroll_detail[0]->total_leave }}" readOnly="true" />
                            </div>
                        </div>
                        <div class="col-sm-6 col-xs-6">                        
                            <div class="form-group ">
                                <label>Unpaid Leave</label>
                                <input type="number" class="form-control" name="unpaid_leave" id="unpaid_leave" value="{{ $payroll_detail[0]->unpaid_leave }}" readOnly="true" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6 col-xs-6">
                            <div class="form-group ">
                                <label>Unpaid Leave Amount</label>
                                <input type="number" class="form-control" name="unpaid_leave_amount" id="unpaid_leave_amount" value="{{ $payroll_detail[0]->unpaid_leave_amount }}" readOnly="true" />
                            </div>
                        </div>
                        <div class="col-sm-6 col-xs-6">                        
                            <div class="form-group ">
                                <label>Professional Tax</label>
                                <input type="number" class="form-control" name="professional_tax" id="professional_tax" value="{{ $payroll_detail[0]->professional_tax }}" readOnly="true" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6 col-xs-6">
                            <div class="form-group ">
                                <label>PF</label>
                                <input type="number" class="form-control" name="pf" id="pf" value="{{ $payroll_detail[0]->pf }}" readOnly="true" />
                            </div>
                        </div>
                        <div class="col-sm-6 col-xs-6">                        
                            <div class="form-group ">
                                <label>Loan Installment</label>
                                <input type="number" class="form-control" name="loan_installment" id="loan_installment" value="{{ $payroll_detail[0]->loan_installment }}" readOnly="true" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6 col-xs-6">
                            <div class="form-group ">
                                <label>Penalty</label>
                                <input type="number" class="form-control" name="penalty" id="penalty" value="{{ $payroll_detail[0]->penalty }}" />
                            </div>
                        </div>
                        <div class="col-sm-6 col-xs-6">                        
                            <div class="form-group ">
                                <label>Manual Penalty</label>
                                <input type="number" class="form-control" name="manual_penalty" id="manual_penalty" value="{{ $payroll_detail[0]->manual_penalty }}" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6 col-xs-6">
                            <div class="form-group ">
                                <label>Penalty Note</label>
                                <input type="text" class="form-control" name="penalty_note" id="penalty_note" value="{{ $payroll_detail[0]->penalty_note }}" />
                            </div>
                        </div>
                        <div class="col-sm-6 col-xs-6">                        
                            <div class="form-group ">
                                <label>Payable Salary</label>
                                <input type="number" class="form-control" name="payable_salary" id="payable_salary" value="{{ $payroll_detail[0]->payable_salary }}" readOnly="true" />
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-sm-6 col-xs-6">
                            <div class="form-group ">
                                <label>Extra Loan/Deduction Amount</label>
                                <input type="text" class="form-control" name="extra_loan_amount" id="extra_loan_amount" value="{{ $payroll_detail[0]->extra_loan_amount }}" />
                            </div>
                        </div>
                        <div class="col-sm-6 col-xs-6">                        
                            <div class="form-group ">
                                <label>Extra Loan/Deduction Details</label>
                                <textarea class="form-control" name="extra_loan_details" id="extra_loan_details">{{ $payroll_detail[0]->extra_loan_details }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6 col-xs-6">
                            @if($payroll_detail[0]->main_approval_status=="Rejected")
                            <button type="submit" class="btn btn-success">Re-Submit</button>
                            @else
                            <button type="submit" class="btn btn-success">Submit</button>
                            @endif
                            <button type="button" onclick="window.location.href ='{{ route('admin.get_payroll') }}'" class="btn btn-default">Cancel</button>	
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
@endsection


@section('script')
<script>
    var __dayDiff = 0;
    jQuery('#edit_payroll').validate({
        ignore: [],
        rules: {
            hra: {
                required: true
            },
            others: {
                required: true
            },
            penalty: {
                required: true
            },
            manual_penalty: {
                required: true,
            }
        }
    });
</script>
@endsection
