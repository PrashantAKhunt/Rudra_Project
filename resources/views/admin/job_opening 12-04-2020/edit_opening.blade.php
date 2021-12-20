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
                        <form action="{{ route('admin.update_job_opening') }}" id="job_opening_frm" method="post">
                            @csrf
                            <input type="hidden" id="id" name="id" value="{{ $job_opening[0]->id }}" />
                            <div class="form-group ">
                                <label>Company<span class="error">*</span></label>
                                <select class="form-control" name="company_id" id="company_id">
                                    <option value="">Select Company</option>
                                    @foreach($company_list as $company)
                                    <option @if($company->id==$job_opening[0]->company_id) selected @endif value="{{$company->id}}">{{ $company->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>                            
                            <div class="form-group "> 
                                <label>Title</label> 
                                <input type="text" class="form-control" name="title" id="title"  value="{{ $job_opening[0]->title }}" />
                            </div>
                            <div class="form-group "> 
                                <label>Description</label> 
                                <textarea class="form-control" name="description" id="description"> {{ $job_opening[0]->description }} </textarea>
                            </div>       
                            <div class="form-group "> 
                                <label>Job Role</label> 
                                <input type="text" class="form-control" name="role" id="role"  value="{{ $job_opening[0]->role }}" />
                            </div>
                            <div class="form-group "> 
                                <label>Job Location</label> 
                                <input type="text" class="form-control" name="location" id="location"  value="{{ $job_opening[0]->location }}" />
                            </div>
                            <div class="form-group "> 
                                <label>Package</label> 
                                <input type="text" class="form-control" name="package" id="package"  value="{{ $job_opening[0]->package }}" />
                            </div>
                            <div class="form-group "> 
                                <label>Experience Required </label> 
                                <input type="text" class="form-control" name="experience_level" id="experience_level"  value="{{ $job_opening[0]->experience_level }}" />
                            </div>
                            <div class="form-group "> 
                                <label>Job Type</label> 
                                <select class="form-control" name="type" id="type">
                                    <option <?php echo ($job_opening[0]->type==$job_opening[0]->type)?"selected='selected'":'' ?> value="">Select Job Type</option>
                                    <option <?php echo ($job_opening[0]->type=='FullTime')?"selected='selected'":'' ?> value="FullTime">Full Time</option>
                                    <option <?php echo ($job_opening[0]->type=='PartTime')?"selected='selected'":'' ?> value="PartTime">Part Time</option>
                                    <option <?php echo ($job_opening[0]->type=='Remote')?"selected='selected'":'' ?> value="Remote">Remote</option>
                                </select>
                            </div>
                            <div class="form-group "> 
                                <label>Job Post Date</label> 
                                <input type="text" class="form-control" name="posting_date" id="posting_date" value="{{ $job_opening[0]->posting_date }}" />
                            </div>
                            <div class="form-group "> 
                                <label>Recruitment Consultancy</label> 
                                <select class="select2 m-b-10 select2-multiple" name="recruitment_consultancy[]" multiple="multiple" id="recruitment_consultancy">
                                    <option value="">Select Recruitment Consultancy</option>
                                    @foreach($recruitment_consultant as $consultant)
                                    <option <?php echo ($job_opening[0]->consultant_id==$consultant->id)?"selected='selected'":'' ?> value="{{ $consultant->id }}">{{ $consultant->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.job_opening') }}'" class="btn btn-default">Cancel</button>
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
    $(document).ready(function(){
        
        jQuery('#posting_date').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: "dd-mm-yyyy"
        });
        
        $('#recruitment_consultancy').select2();
        
        $('#job_opening_frm').validate({
            rules:{
                title:{
                    required:true
                },
                description:{
                    required:true
                },
                role:{
                    required:true
                },
                location:{
                    required:true
                },
                package:{
                    required:true
                },
                experience_level:{
                    required:true
                },
                posting_date:{
                    required:true
                },
                recruitment_consultancy:{
                    required:true
                },
                type:{
                    required:true
                },
                company_id:{
                    required:true
                },
            }
        })
        
    });
</script>
@endsection
