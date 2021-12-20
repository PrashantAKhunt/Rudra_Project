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
                            <form action="{{ route('admin.update_policy') }}" id="edit_policy" method="post" enctype="multipart/form-data">
                            <input type="hidden" id="id" name="id" value="{{ $policy_detail[0]->id }}" />
                            @csrf
                             
                             <div class="form-group ">
								<label>Title <span class="error">*</span> </label>
                                <input type="text" class="form-control" name="title" id="title" value="{{ $policy_detail[0]->title }}" />
                            </div>

							<div class="form-group ">
                                <label>Uploads Document <span class="error">*</span> </label>
                                <input type="file" name="name" id="name" class="dropify"/>
                            </div>
                            <!-- 23-06-2021  -->
                            <div class="form-group ">
                                <label>Implementation Date <span class="error">*</span> </label>
                                <input type="text" class="form-control" required readonly="" name="implementation_date" id="implementation_date" value="<?php echo!empty($policy_detail[0]->implementation_date) ? date('d-m-Y',strtotime($policy_detail[0]->implementation_date)) : "N/A"?>" />
                                <!-- date('d-m-Y',strtotime($policy_detail[0]->implementation_date)) }} -->
                                <!-- $policy_detail[0]->implementation_date }} -->
                                <!-- $policy_detail['implementation_date'] ? date('d-m-Y H:i a',strtotime($policy_detail['implementation_date])) : "" -->
                            </div>
                            <div class="form-group ">
                                <label>Amendment Date <span class="error">*</span> </label>
                                <input type="text" class="form-control" required readonly="" name="amendment_date" id="amendment_date" value="<?php echo!empty($policy_detail[0]->amendment_date) ? date('d-m-Y',strtotime($policy_detail[0]->amendment_date)) : "N/A"?>" />
                            </div>
                            <div class="form-group ">
                                <label>Addendum Date <span class="error">*</span> </label>
                                <input type="text" class="form-control" required readonly="" name="addendum_date" id="addendum_date" value="<?php echo!empty($policy_detail[0]->addendum_date) ? date('d-m-Y',strtotime($policy_detail[0]->addendum_date)) : "N/A"?>" />
                            </div>
                            <div class="form-group ">
                                <label>Effective From <span class="error">*</span> </label>
                                <input type="text" class="form-control" required readonly="" name="effective_from_date" id="effective_from_date" value="<?php echo!empty($policy_detail[0]->effective_from) ? date('d-m-Y',strtotime($policy_detail[0]->effective_from)) : "N/A"?>" />
                            </div>		
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.policy') }}'" class="btn btn-default">Cancel</button>
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
   var __dayDiff = 0;
    jQuery('#edit_policy').validate({
        ignore: [],
        rules: {
            title: {
                required: true,
            },
            name:{
                required: true
            },
            implementation_date:{
                required: true
            },
            amendment_date:{
                required: true
            },
            addendum_date:{
                required: true
            },
            effective_from_date:{
                required: true
            },
        }
    });

    $('.select2').select2();

	$(document).ready(function(){

	});

// reminder_date 23-06-2021
jQuery('#implementation_date').datepicker({
        autoclose: true,
        todayHighlight: true,
        format: "dd-mm-yyyy"
    });
    jQuery('#amendment_date').datepicker({
        autoclose: true,
        todayHighlight: true,
        format: "dd-mm-yyyy"
    });
    jQuery('#addendum_date').datepicker({
        autoclose: true,
        todayHighlight: true,
        format: "dd-mm-yyyy"
    });
    jQuery('#effective_from_date').datepicker({
        autoclose: true,
        todayHighlight: true,
        format: "dd-mm-yyyy"
    });
</script>
@endsection
