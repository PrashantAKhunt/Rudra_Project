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
    </div>
    <div class="row">
        <div class="col-lg-12 col-sm-12 col-xs-12">
            <div class="white-box">                
                <form action="{{ route('admin.insert_interview') }}" id="add_interview_form" method="post">
                    @csrf
                    <div class="col-md-6">
                        <label>Name <span class="error">*</span></label>
                        <input type="text" class="form-control" name="name" id="name" value="" />
                    </div>
                    <div class="col-md-6 pull-right">
                        <label>Designation <span class="error">*</span></label>
                        <select class="form-control" name="designation" id="designation">
                            <option value="">Select Designation</option>
                            @foreach($job_opening_position as $job_opening)
                                <option value="{{ $job_opening->id }}">{{ $job_opening->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="clearfix"></div>
                    <br>
                    <div class="col-md-6">
                        <label>Contact Number <span class="error">*</span></label>
                        <input type="text" class="form-control" name="contact_number" id="contact_number" value="" />
                    </div>
                    <div class="col-md-6 pull-right">
                        <label>Emgency Contact Number <span class="error">*</span></label>
                        <input type="text" class="form-control" name="emg_contact_number" id="emg_contact_number" value="" />
                    </div>
                    <div class="clearfix"></div>
                    <br>
                    <div class="col-md-6">
                        <label>Residential Address <span class="error">*</span></label>
                        <input type="text" class="form-control" name="residential_address" id="residential_address" value="" />
                    </div>
                    <div class="col-md-6 pull-right">
                        <label>Permanent Address <span class="error">*</span></label>
                        <input type="text" class="form-control" name="permanent_address" id="permanent_address" value="" />
                    </div>
                    <div class="clearfix"></div>
                    <br>
                    <div class="col-md-6">
                        <label>Gender <span class="error">*</span></label>
                        <select class="form-control" name="gender" id="gender">
                            <option value="">Select Gender Type</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                    <div class="col-md-6 pull-right">
                        <label>Birth Date <span class="error">*</span></label>
                        <input type="text" class="form-control" name="birth_date" id="birth_date" value="" />
                    </div>
                    <div class="clearfix"></div>
                    <br>
                    <div class="col-md-6">
                        <label>Marital Status</label>
                        <select class="form-control" name="marital_status" id="marital_status">
                            <option value="no">No</option>
                            <option value="yes">Yes</option>
                        </select>
                    </div>
                    <div class="col-md-6 pull-right">
                        <label>Is Physically Handicapped ?</label>
                        <select class="form-control" name="physically_handicapped" id="physically_handicapped">
                            <option value="no">No</option>
                            <option value="yes">Yes</option>
                        </select>
                    </div>
                    <div class="clearfix"></div>
                    <br>
                    <div class="col-md-6">
                        <label>Email <span class="error">*</span></label>
                        <input type="text" class="form-control" name="email" id="email" />
                    </div>
                    <div class="col-md-6 pull-right">
                        <label>Handicap Note</label>
                        <input type="text" class="form-control" name="handicap_note" id="handicap_note" value="" />
                    </div>                    
                    <div class="clearfix"></div>
                    <br>
                    <div class="col-md-6 pull-left">
                        <button type="submit" class="btn btn-success">Submit</button>
                        <button type="button" onclick="window.location.href ='{{ route('admin.interview') }}'" class="btn btn-default">Cancel</button>
                    </div>
                    <br>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<link href="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/build/css/bootstrap-datetimepicker.css" rel="stylesheet">
<script src="//cdn.rawgit.com/Eonasdan/bootstrap-datetimepicker/e8bddc60e73c1ec2475f827be36e1957af72e2ea/src/js/bootstrap-datetimepicker.js"></script>
<script>
    $(document).ready(function(){        
        jQuery('#birth_date').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: "yyyy-mm-dd"
        });
        $('#add_interview_form').validate({
            rules:{
                name:{
                    required:true
                },
                email:{
                    required:true
                },
                designation:{
                    required:true
                },
                contact_number:{
                    required:true
                },
                emg_contact_number:{
                    required:true
                },
                residential_address:{
                    required:true
                },
                permanent_address:{
                    required:true
                },
                gender:{
                    required:true
                },
                birth_date:{
                    required:true
                },
                marital_status:{
                    required:true
                },
                physically_handicapped:{
                    required:true
                }
            }
        })
    });
</script>
@endsection