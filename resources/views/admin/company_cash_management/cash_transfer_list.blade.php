<?php

use Illuminate\Support\Facades\Auth;
?>

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
    </div>
    <!-- new filter added start -->
    <div class="row">
        <div class="col-md-12 col-lg-12 col-sm-12">
            <div class="panel panel-info">
                <div class="panel-wrapper collapse in" aria-expanded="true">
                    <div class="panel-body">
                        <form action="{{ route('admin.cash_transfer_list') }}" id="used_cheque" method="POST">
                            @csrf
                            <div class="row">
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="inputEmail4">Company</label>
                                        <select class="form-control" name="company_id" id="company_id">
                                            <option value="">Select Company</option>
                                            @if($companies)
                                            @foreach($companies as $key => $value)
                                                <option value="{{$key}}" {{($company_id != "" && $company_id == $key) ? "selected": ""}}>{{$value}}</option>
                                            @endforeach
                                            @endif
                                           
                                        </select>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label for="inputEmail4">Employee</label>
                                        <select class="form-control" name="employee_id" id="employee_id">
                                            <option value="">Select Employee</option>
                                            @if($employeess)
                                            @foreach($employeess as $key => $value)
                                                <option value="{{$key}}" {{($employee_id != "" && $employee_id == $key) ? "selected": ""}}>{{$value}}</option>
                                            @endforeach
                                            @endif
                                            

                                        </select>
                                    </div>


                                    <div class="form-group col-md-4">
                                        <label for="inputEmail4">Project Name</label>
                                        <select class="form-control" name="project_id" id="project_id">
                                            <option value="">Select Project Name</option>

                                            @if($project_namee)
                                            @foreach($project_namee as $key => $value)
                                                <option value="{{$key}}" {{($project_id != "" && $project_id == $key) ? "selected": ""}}>{{$value}}</option>
                                            @endforeach
                                            @endif

                                        </select>
                                    </div>

 
                                    <div class="form-group col-md-4">
                                        <label for="inputEmail4">Transfer Type</label>
                                        <select class="form-control" name="transfer_type" id="transfer_type">
                                            <option value="">Select Transfer Type</option>
                                            <option value="credit" {{($transfer_type != "" && $transfer_type == 'credit') ? "selected": ""}}>Credit</option>
                                            <option value="debit" {{($transfer_type != "" && $transfer_type == 'debit') ? "selected": ""}}>Debit</option>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-4">
                                        <label>Transaction Date<label class="error"></label>
                                        <input type="text" class="form-control timeseconds shawCalRanges" value="{{($transfer_date != "") ? $transfer_date : ""}}" required name="transfer_date" id="transfer_date" value=" ">
                                    </div>

                                    <div class="form-group col-md-2" style="padding: 20px 0;">
                                        <button type="submit" class="form-control btn btn-success"><i class="fa fa-check"></i>Search</button>
                                    </div>
                                    

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- new filter added Ends heret -->
    <div class="row">
        <div class="col-md-12">
            <div class="white-box">

                @if($company_cash_transfer_permission)
                <a href="{{ route('admin.company_to_company_cash_transfer') }}" class="btn btn-primary pull-left"><i class="fa fa-plus"></i> To Company Transfer</a>

                <a href="{{ route('admin.company_to_employee_cash_transfer') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> To Employee Transfer</a>
                @endif
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="row">
                    <div class="table-responsive">
                        <table id="request_table" class="table table-striped">
                            <thead>
                                <th>Account (Company/Employee)</th>
                                <th>Account Type</th>
                                <th>Project Name</th>
                                <th>Balance</th>
                                <th>Transfer Note</th>
                                <th>Transfer Type</th>
                                <th>Entry Type</th>
                                <th>Transaction Before Balance</th>
                                <th>Transaction After Balance</th>
                                <th>Created Date</th>
                            </thead>
                            <tbody>
                                @if(!empty($records))
                                @foreach($records as $list_data)
                                <tr>
                                    <td>{{$list_data['accountant_name']}}</td>
                                    <td>{{$list_data['account_type']}}</td>
                                    <td>

                                        @if($list_data['project_name'])
                                        {{$list_data['project_name']}}
                                        @endif

                                    </td>
                                    <td>{{ number_format($list_data['balance'], 2, '.', ',') }}</td>
                                    <td>

                                        @if( $list_data['txn_note'] )
                                        {{ $list_data['txn_note'] }}
                                        @else
                                        NA
                                        @endif
                                    </td>

                                    <td>
                                        @if($list_data['transfer_type'] == 'credit')
                                        Credited
                                        @elseif($list_data['transfer_type'] == 'debit')
                                        Debited
                                        @endif
                                    </td>

                                    <td>
                                        @if($list_data['entry_type'] == 'add_new')
                                        Add New
                                        @elseif($list_data['entry_type'] == 'transfer')
                                        Transfer
                                        @elseif($list_data['entry_type'] == 'expense')
                                        Expense
                                        @elseif($list_data['entry_type'] == 'cash_payment')
                                        Cash Payment
                                        @if ($list_data['cash_entry_code'])
                                        (Entry Code - {{$list_data['cash_entry_code']}})
                                        @endif
                                        @endif
                                    </td>

                                    <td>{{ number_format($list_data['txn_before_balance'], 2, '.', ',') }}</td>
                                    <td>{{ number_format($list_data['txn_after_balance'], 2, '.', ',') }}</td>

                                    <td>
                                        {{ date('d-m-Y h:i A',strtotime( $list_data['created_at'] )) }}
                                    </td>

                                </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    $('#request_table').DataTable({
        dom: 'lBfrtip',
        buttons: ['excel'],
        stateSave: true,
    });
</script>

<script type="text/javascript">
$(document).ready(function() {

$('.shawCalRanges').daterangepicker({
                showDropdowns: false,
                // timePicker: false,
                // timePickerIncrement: 1,
                // timePicker24Hour: true,
                locale: {
                    format: 'D/M/YYYY'
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                },
                alwaysShowCalendars: true,
            });
});
</script>


@endsection