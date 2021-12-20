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
                        <form action="{{ route('admin.insert_announcements') }}" id="add_announcements" method="post">
                            @csrf
                            <div class="form-group "> 
                                <label>Title</label> 
                                <input type="text" class="form-control" name="title" id="title" value="" />
                            </div>
							<div class="form-group ">
                                <label>Description</label>
                                <textarea class="form-control" name="description" id="description"></textarea>
                            </div>							
							<div class="form-group ">
                                <label></label>
								<div class="input-daterange input-group" id="date-range">
									<input type="text" class="form-control"  name="start_date" id="start_date" readonly="true" />
									<span class="input-group-addon bg-info b-0 text-white">to</span>
									<input type="text" class="form-control" name="end_date" id="end_date" readonly="true" />
								</div>
							</div>                            
							<div class="form-group ">                                
                                <div class="checkbox checkbox-success pull-right">
				                  	<input id="select_all" type="checkbox">
				                  	<label for="select_all">Select All</label>
				                </div>
				                <label>Users</label>
								@if(!empty($user))
									<select name="user_id[]" id="user_id" class="select2 m-b-10 select2-multiple" multiple="multiple" data-placeholder="Choose">
										@foreach($user as $key => $value)
											<option value="{{$key}}">{{$value}}</option>
										@endforeach								
									</select>									
								@endif
                            </div>
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.announcements') }}'" class="btn btn-default">Cancel</button>
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
    jQuery('#add_announcements').validate({
        ignore: [],
        rules: {
            title: {
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
			'user_id[]': {
                required: true,
            }
        }
    });
	
    $('.select2').select2();
	
	jQuery('#date-range').datepicker({
		toggleActive: true,
		format: 'yyyy-mm-dd'
	});
	$(document).ready(function(){
		$('#select_all').click(function() {
			if($(this).prop("checked") == true){
				$('#user_id').select2('destroy');	
    			$('#user_id option').prop('selected', true);
    			$('#user_id').select2();
			}else{
				$('#user_id').select2('destroy');	
    			$('#user_id option').prop('selected', false);
    			$('#user_id').select2();
			}
		});
	});
	
</script>
@endsection
