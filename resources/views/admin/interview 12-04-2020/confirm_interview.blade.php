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
              <div class="tab-content">                
                    <form action="{{ route('admin.add_confirm_interview') }}" id="confirm_interview_frm" method="post">
                        @csrf
                        <input type="hidden" name="id" value="{{ $interview_list->id }}">
                        <div class="col-md-6">
                            <label>Candidate Name</label>
                            <input type="text" class="form-control" name="name" id="name" value="{{ $interview_list->name }}" />
                        </div>
                        <div class="col-md-6">
                            <label>Designation</label>
                            <select class="form-control" name="designation" id="designation">
                                <option value="">Select Designation</option>
                                @foreach($job_opening_position as $job_opening)
                                    <option <?php echo ($interview_list->job_opening_id==$job_opening->id)?"selected='selected'":'' ?> value="{{ $job_opening->id }}">{{ $job_opening->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="clearfix"></div>
                        <br>
                        <div class="col-md-3">
                            <label>Contact Number</label>
                            <input type="text" class="form-control" name="contact_number" id="contact_number" value="{{ $interview_list->contact_number }}" />
                        </div>
                        <div class="col-md-3">
                            <label>Emgency Contact Number</label>
                            <input type="text" class="form-control" name="emg_contact_number" id="emg_contact_number" value="{{ $interview_list->emg_contact_number }}" />
                        </div>
                        <div class="col-md-6">
                            <label>Email</label>
                            <input type="text" class="form-control" name="email" id="email" value="{{ $interview_list->email }}" />
                        </div>                        
                        <div class="clearfix"></div>
                        <br>
                        <div class="col-md-6">
                            <label>Residential Address</label>
                            <input type="text" class="form-control" name="residential_address" id="residential_address" value="{{ $interview_list->residential_address }}" />
                        </div>
                        <div class="col-md-6">
                            <label>Permanent Address</label>
                            <input type="text" class="form-control" name="permanent_address" id="permanent_address" value="{{ $interview_list->permanent_address }}" />
                        </div>
                        <div class="clearfix"></div>
                        <br>
                        <div class="col-md-3">
                            <label>Gender</label>
                            <select class="form-control" name="gender" id="gender">
                                <option value="">Select Gender Type</option>
                                <option <?php echo ($interview_list->gender=="male" OR $interview_list->gender=="Male")?"selected='selected'":'' ?> value="male">Male</option>
                                <option <?php echo ($interview_list->gender=="female" OR $interview_list->gender=="Female")?"selected='selected'":'' ?> value="female">Female</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Birth Date</label>
                            <input type="text" class="form-control" name="birth_date" id="birth_date" value="{{ $interview_list->birth_date }}" />
                        </div>                        
                        <div class="col-md-3">
                            <label>Marital Status</label>
                            <select class="form-control" name="marital_status" id="marital_status">
                                <option <?php echo ($interview_list->marital_status=="no")?"selected='selected'":'' ?> value="no">No</option>
                                <option <?php echo ($interview_list->marital_status=="yes")?"selected='selected'":'' ?> value="yes">Yes</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Is Physically Handicapped ?</label>
                            <select class="form-control" name="physically_handicapped" id="physically_handicapped">
                                <option <?php echo ($interview_list->physically_handicapped=="no")?"selected='selected'":'' ?> value="no">No</option>
                                <option <?php echo ($interview_list->physically_handicapped=="yes")?"selected='selected'":'' ?> value="yes">Yes</option>
                            </select>
                        </div>
                        <div class="clearfix"></div>
                        <br>
                        <div class="col-md-6">
                            <label>Annual Package</label>
                            <input type="text" class="form-control" name="package" id="package" value="{{ $interview_list->package }}" />
                        </div>
                        <div class="col-md-6">
                            <label>Joining Date</label>
                            <input type="text" class="form-control" name="join_date" id="join_date" value="{{ $interview_list->join_date }}" />
                        </div>
                        <div class="clearfix"></div>
                        <br>
                        <div class="col-md-6">
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.interview') }}'" class="btn btn-default">Cancel</button>
                        </div>
                        <div class="clearfix"></div>
                        <br>
                    </form>
                
              </div>            
          </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
    $(document).ready(function(){        
        jQuery('#join_date').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: "dd-mm-yyyy"
        });
        $('#confirm_interview_frm').validate({
            rules:{
                package:{
                    required:true
                },
                join_date:{
                    required:true
                }
            }
        });        
    });
</script>
@endsection
