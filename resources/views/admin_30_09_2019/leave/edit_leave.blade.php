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
                            <form action="{{ route('admin.update_leave') }}" id="edit_leave" method="post">
                            <input type="hidden" id="id" name="id" value="{{ $leave_detail[0]->id }}" />
                            @csrf
                             
                             <div class="form-group ">
								<label>Subject</label> 
                                <input type="text" class="form-control" name="subject" id="subject" value="{{ $leave_detail[0]->subject }}" />
                            </div>

							<div class="form-group ">
                                <label>Description</label>
                                <textarea class="form-control" name="description" id="description"> {{ $leave_detail[0]->description }} </textarea>
                            </div>

							<div class="row">
								<div class="col-md-6">
									<select class="form-control" class="leave_day" name="start_day" id="start_day">
									  <option <?php if($leave_detail[0]->start_day == "1" ) { ?> selected <?php } ?> value="1">Full Day</option>
									  <option <?php if($leave_detail[0]->start_day == "2" ) { ?> selected <?php } ?> value="2">First Half</option>
									  <option <?php if($leave_detail[0]->start_day == "3" ) { ?> selected <?php } ?> value="3">Second Half</option>
									</select>
								</div>
								<div class="col-md-6">									
									<select class="form-control" class="leave_day" name="end_day" id="end_day" >
									  <option <?php if($leave_detail[0]->end_day == "1" ) { ?> selected <?php } ?> value="1">Full Day</option>
									  <option <?php if($leave_detail[0]->end_day == "2" ) { ?> selected <?php } ?> value="2">First Half</option>
									  <option <?php if($leave_detail[0]->end_day == "3" ) { ?> selected <?php } ?> value="3">Second Half</option>
									</select>
								</div>
							</div>
							<div class="form-group ">
                                <label></label>
								<div class="input-daterange input-group" id="date-range">
									<input type="text" class="form-control start_leave_date"  name="start_date" id="start_date" value="{{ $leave_detail[0]->start_date }}" readonly="true" />
									<span class="input-group-addon bg-info b-0 text-white">to</span>
									<input type="text" class="form-control end_leave_date" name="end_date" id="end_date" value="{{ $leave_detail[0]->end_date }}" readonly="true" />
								</div>
							</div>
							<div class="form-group ">
                                <label>Leave Balance</label>
								@if(!empty($categories))
								<select name="leave_category_id" class="form-control" id="leave_category_id" >
								<option value="">Select Leave Balance</option>
									@foreach($categories as $key => $value)										
										<option <?php if($value['balance'] == "0" && $value['leavecategory']['id'] != 4) { ?> disabled style="color:red" <?php } if($leave_detail[0]->leave_category_id == $value['leavecategory']['id'] ) { ?> selected <?php } ?> <?php if($value['leavecategory']['id'] != 4) { ?> data-balance="{{ $value['balance'] }}" <?php } ?> value="{{ $value['leavecategory']['id'] }}"><?php if($value['leavecategory']['id'] != 4) { ?> {{ $value['leavecategory']['name'] ." - ". $value['balance'] }} <?php } else { ?> {{ $value['leavecategory']['name'] }} <?php } ?></option>
									@endforeach
								</select>
                                @endif
                            </div>
							<?php $notifyIds = explode(",", $leave_detail[0]->notify_id); ?>
                            <div class="form-group ">
                                <label>Notify</label>
								@if(!empty($user))
									<select name="notify_id[]" class="select2 m-b-10 select2-multiple" multiple="multiple" data-placeholder="Choose">
										@foreach($user as $key => $value)
											<option <?php if(in_array($key, $notifyIds) ) { ?> selected <?php } ?> value="{{ $key }}">{{ $value }}</option>
										@endforeach								
									</select>
								@endif
                            </div>
							
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.leave') }}'" class="btn btn-default">Cancel</button>
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
	$( document ).ready(function() {
		$('.end_leave_date, .start_leave_date').trigger('change');
	});
	
   var __dayDiff = 0;
    jQuery('#edit_leave').validate({
        ignore: [],
        rules: {
            subject: {
                required: true,
            },
            description:{
                required: true
            },
			start_date: {
                required: true,
            },
            end_date:{
                required: true
            },
			leave_category_id: {
                required: true,
            },
			notify_id: {
                required: true,
            }
        }
    });
	
    $('.select2').select2();
	
	jQuery('#date-range').datepicker({
		toggleActive: true,
		//format: 'dd-mm-yyyy'
		format: 'yyyy-mm-dd'
	});
	
	$('.end_leave_date, .start_leave_date').change(function(){
		var startDate = $('#start_date').val(), endDate = $('#end_date').val();
		var diff = new Date(new Date(endDate) - new Date(startDate));
		__dayDiff = (diff/1000/60/60/24) + 1;
		
		if( __dayDiff <= 1){
			$('#start_day option[value*="2"]').removeAttr('disabled');
			$('#end_day option').each(function(){
				$(this).prop('disabled',true);
			});
			$('#end_day option[value*="'+$('#start_day').val()+'"]').prop('disabled',false).prop('selected',true);
		}else{			
			$('#start_day option[value*="2"]').prop('disabled',true);
			$('#end_day option[value*="3"]').prop('disabled',true);
			$('#end_day option[value*="2"]').removeAttr('disabled');
			$('#start_day option[value*="1"]').removeAttr('disabled');
			$('#start_day option[value*="3"]').removeAttr('disabled');
		}
		
		$('#leave_category_id option').each(function(){
			if( __dayDiff > $(this).data('balance')){
				$(this).prop('disabled',true);
				$(this).css('color','red');
			}else{
				$(this).prop('disabled',false);
				$(this).css('color','gray');
			}
		});
		
	});
	$('#start_day').change(function(){
		if( __dayDiff <= 1){
			$('#end_day option[value*="'+$(this).val()+'"]').prop('disabled',false).prop('selected',true);
		}
	});
    
</script>
@endsection
