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
                        <form action="{{ route('admin.insert_policy') }}" id="add_policy" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group "> 
                                <label>Title <span class="error">*</span> </label> 
                                <input type="text" class="form-control" name="title" id="title" value="" />
                            </div>
							<div class="form-group ">
                                <label>Uploads Document <span class="error">*</span> </label>
                                <input type="file" name="name" id="name" class="dropify"/>
                            </div>

                            <!-- 23-06-2021  -->
                            <div class="form-group ">
                                <label>Implementation Date <span class="error">*</span> </label>
                                <input type="text" class="form-control" required readonly="" name="implementation_date" id="implementation_date" value="" />
                            </div>
                            <div class="form-group ">
                                <label>Amendment Date <span class="error">*</span> </label>
                                <input type="text" class="form-control" required readonly="" name="amendment_date" id="amendment_date" value="" />
                            </div>
                            <div class="form-group ">
                                <label>Addendum Date <span class="error">*</span> </label>
                                <input type="text" class="form-control" required readonly="" name="addendum_date" id="addendum_date" value="" />
                            </div>
                            <div class="form-group ">
                                <label>Effective From <span class="error">*</span> </label>
                                <input type="text" class="form-control" required readonly="" name="effective_from_date" id="effective_from_date" value="" />
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
    jQuery('#add_policy').validate({
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
        },
        submitHandler: function (form) {
            // Prevent double submission
            if (!this.beenSubmitted) {
                this.beenSubmitted = true;
                form.submit();
            }
        }
    });
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
