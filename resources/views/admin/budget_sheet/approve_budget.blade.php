@extends('layouts.admin_app')

@section('content')
<?php

use App\Lib\CommonTask;

$common_task = new CommonTask();
?>
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">Add Budget Sheet</h4>
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
                    <div class="col-sm-12 col-xs-12">
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
                        <form action="{{ route('admin.approve_budget_sheet_entry') }}" id="approve_budget_sheet" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" id="id" value="{{ $budget_data[0]->id }}" />
                            <div class="row">
                                <div class="col-md-3 col-xs-6 b-r"> <strong>Meeting Number</strong> <br>
                                    <p class="text-muted">{{ $budget_data[0]->meeting_number }}</p>
                                </div>
                                <div class="col-md-3 col-xs-6 b-r"> <strong>Meeting Date</strong> <br>
                                    <p class="text-muted">{{ date('d-m-Y',strtotime($budget_data[0]->meeting_date)) }}</p>
                                </div>
                                <div class="col-md-3 col-xs-6 b-r"> <strong>Company</strong> <br>
                                    <p class="text-muted">{{ $budget_data[0]->company_name }}</p>
                                </div>
                                <div class="col-md-3 col-xs-6"> <strong>Department</strong> <br>
                                    <p class="text-muted">{{ $budget_data[0]->dept_name }}</p>
                                </div>

                            </div>
                            <br>
                            <hr class="m-t-0">

                            <div class="row">
                                <div class="col-md-3 col-xs-6 b-r"> <strong>Vendor</strong> <br>
                                    <p class="text-muted">{{ $budget_data[0]->vendor_name }}</p>
                                </div>
                                <div class="col-md-3 col-xs-6 b-r"> <strong>Description</strong> <br>
                                    <p class="text-muted">{{ $budget_data[0]->description }}</p>
                                </div>
                                <div class="col-md-3 col-xs-6 b-r"> <strong>Remark By Employee</strong> <br>
                                    <p class="text-muted">{{ $budget_data[0]->remark_by_user }}</p>
                                </div>
                                <div class="col-md-3 col-xs-6"> <strong>Request Amount</strong> <br>
                                    <p class="text-danger"><b>{{ $budget_data[0]->request_amount }}</b></p>
                                </div>

                            </div>
                            <br>
                            <hr class="m-t-0">

                            <div class="row">
                                <div class="col-md-3 col-xs-6 b-r"> <strong>Client</strong> <br>
                                    <p class="text-muted">
                                    @if($budget_data[0]->client_name)
                                    @if($budget_data[0]->client_name == 'Other Client')
                                        {{ $budget_data[0]->client_name }}
                                            @else
                                            {{ $budget_data[0]->client_name." (".$budget_data[0]->location.")" }}
                                            @endif


                                    @endif
                                            </p>
                                </div>
                                <div class="col-md-3 col-xs-6 b-r"> <strong>Project</strong> <br>
                                    <p class="text-muted">{{ $budget_data[0]->project_name }}</p>
                                </div>
                                <div class="col-md-3 col-xs-6 b-r"> <strong>Site Name</strong> <br>
                                    <p class="text-muted">{{ $budget_data[0]->site_name }}</p>
                                </div>

                                <div class="col-md-3 col-xs-6"> <strong>Total Amount</strong> <br>
                                    <p class="text-danger"><b>{{ $budget_data[0]->total_amount }}</b></p>
                                </div>

                            </div>
                            <br>
                            <hr class="m-t-0">
                            <div class="row">
                            <div class="col-md-3 col-xs-6 b-r"> <strong>Schedule Date</strong> <br>
                                    <p class="text-muted">{{ date('d-m-Y',strtotime($budget_data[0]->schedule_date_from)).' to '.date('d-m-Y',strtotime($budget_data[0]->schedule_date_to)) }}</p>
                                </div>
                                <div class="col-md-3 col-xs-6"> <strong>Mode Of Payment</strong> <br>
                                    <p class="text-muted">{{ $budget_data[0]->mode_of_payment }}</p>
                                </div>
                            </div>
                            <br>
                            <hr class="m-t-0">
                            <h3 class="page-title">Approval Data</h3>
                            <hr class="m-t-0">
                            <div class="col-md-12">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">Approve Amount</label>
                                        {{-- <input class="form-control" onchange="set_current_hold_amt()" onkeyup="set_current_hold_amt()" type="text" id="approved_amount" name="approved_amount" value="{{ $budget_data[0]->total_amount-$budget_data[0]->hold_amount }}"/> --}}
                                        <input class="form-control" onchange="set_current_hold_amt()" onkeyup="set_current_hold_amt()" type="text" id="approved_amount" name="approved_amount" value="{{ $budget_data[0]->request_amount }}" max="{{$budget_data[0]->total_amount}}"/>
                                    </div>
                                </div>



                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label">Hold Amount In Current Request</label>
                                        <input class="form-control" type="text" readonly="" id="hold_amount" name="hold_amount" value=""/>
                                    </div>
                                </div>

<!--                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">Previous Hold Request</label>
                                        <select class="form-control" onchange="show_hold_previous_amt();" id="previous_hold_id" name="previous_hold_id">
                                            <option value="">Select Previous Hold Request</option>
                                            @foreach($previous_hold_request as $previous_hold)
                                            <option value="{{ $previous_hold->id }}">{{ $previous_hold->meeting_number.' ('.$previous_hold->name.')' }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="control-label">Previous Hold Amount</label>
                                        <input class="form-control" type="text" id="previous_hold_amount" name="previous_hold_amount" value=""/>
                                    </div>
                                </div>-->
                            </div>
                            <div class="col-md-12">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">Approval Remark</label>
                                        <textarea name="approval_remark" id="approval_remark" class="form-control"></textarea>
                                    </div>
                                </div>

                            </div>
                            <br>
                            <hr class="m-t-0">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-success">Submit</button>
                                <button type="button" onclick="window.location.href ='{{ route('admin.budget_sheet_list') }}'" class="btn btn-default">Cancel</button>
                            </div>
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

    function set_current_hold_amt(){
        var total_req_amt={{ $budget_data[0]->total_amount }};
        var approved_amt=parseFloat($('#approved_amount').val());
        var final_hold_amount = total_req_amt-approved_amt;
        if(final_hold_amount >= 0)
        {
            $('#hold_amount').val(final_hold_amount);
        }else{
            $('#hold_amount').val("");
        }
    }

    function show_hold_previous_amt(){
        $.ajax({
            url:"{{ route('admin.get_previous_hold_amt') }}",
            type:"POST",
            dataType:"json",
            data:{id:$('#previous_hold_id').val()},
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success:function(data){
                if(data.status){
                    $('#previous_hold_amount').val(data.amount);
                }
                else{
                    $('#previous_hold_amount').val(0.00);
                }
            }
        })
    }
    jQuery('#approve_budget_sheet').validate({
        ignore: [],
        rules: {
            'approved_amount': {
                required: true,
                number: true
            },
            hold_amount:{
                number: true
            },
            previous_hold_amount:{
                number: true
            }
        },
        messages: {
            'approved_amount': {

                number: "Please enter valid amount"
            },
            hold_amount:{
                number: "Please enter valid amount"
            },

        }
    });
    $(document).ready(function () {
        $('.input-daterange-datepicker').daterangepicker({
            buttonClasses: ['btn', 'btn-sm'],
            applyClass: 'btn-danger',
            cancelClass: 'btn-inverse',
            locale: {
                format: 'DD/MM/YYYY'
            }
        });
        $('.remove_btn').hide();
        $('#previous_hold_id').select2();

        $("#approved_amount").trigger('onchange');
    });

</script>
@endsection
