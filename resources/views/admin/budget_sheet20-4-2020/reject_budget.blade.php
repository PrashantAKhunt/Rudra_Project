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
                        <form action="{{ route('admin.reject_budget_sheet_entry') }}" id="reject_budget_sheet" method="post" enctype="multipart/form-data">
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
                          
                            <div class="col-md-12">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">Reject Note</label>
                                        <input type="hidden" name="id" id="id" value="{{ $budget_data[0]->id }}" />
                                        <textarea name="reject_note" id="reject_note" class="form-control"></textarea>
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
    
    
    jQuery('#reject_budget_sheet').validate({
        ignore: [],
        rules: {
            reject_note: {
                required: true,
                
            },
            
        },
        
    });
    

</script>
@endsection
