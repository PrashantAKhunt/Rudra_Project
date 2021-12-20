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
                        <form action="{{ route('admin.update_meeting') }}" id="update_meeting" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group "> 
                        <label>Meeting Categories <span class="error">*</span> </label> 
                        <select class="form-control" name="meeting_categories"  id="meeting_categories">
                            <option value="">Select Meeting</option>
                            <option value="External" {{($meeting_detail[0]->meeting_categories == "External") ? 'selected' : ''}}>External</option>
                            <option value="Internal" {{($meeting_detail[0]->meeting_categories == "Internal") ? 'selected' : ''}}>Internal</option>
                            <option value="Mix of Internal/External" {{($meeting_detail[0]->meeting_categories == "Mix of Internal/External") ? 'selected' : ''}}>Mix of Internal/External</option>
                        </select>
                    </div>
                            <input type="hidden" name="meeting_id" value="{{ $meeting_detail[0]->id }}">
                            <div class="form-group "> 
                                <label>Meeting Subject <span class="error">*</span> </label> 
                                <input type="text" class="form-control" name="meeting_subject" id="meeting_subject" value="{{ $meeting_detail[0]->meeting_subject }}" /> 
                            </div>
                             
                            <div class="form-group "> 
                                <label>Meeting Start Date Time <span class="error">*</span> </label> 
                                <input type="text" class="form-control" name="meeting_date_time" id="meeting_date_time" value="{{ $meeting_detail[0]->meeting_date_time }}"/> 
                            </div>

                            <div class="form-group "> 
                                <label>Meeting End Date Time <span class="error">*</span> </label> 
                                <input type="text" class="form-control" name="meeting_end_date_time" id="meeting_end_date_time" value="{{ $meeting_detail[0]->meeting_end_date_time }}"/> 
                            </div>

                            <div class="form-group "> 
                                <label>Meeting User <span class="error">*</span> </label> 
                                <select class="select2 m-b-10 select2-multiple" name="meeting_users[]" multiple="multiple" id="meeting_users">
                                    <option value="" disabled>Select Meeting User</option>
                                    @foreach($users_data as $user)
                                        <option <?php echo (in_array($user->id, explode(',',$meeting_user_ids)))?"selected='selected'":'' ?> value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group "> 
                                <label>MOM User <span class="error">*</span> </label> 
                                <select class="form-control" name="mom_user_id"  id="mom_user_id">
                                    <option value="" >Select User</option>
                                    @foreach($users_data as $user)
                                        <option @if($meeting_detail[0]->mom_user_id==$user->id) selected @endif value="{{$user->id}}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group ">
                                <label>Meeting Detail <span class="error">*</span> </label>
                                <textarea class="form-control" rows="10" name="meeting_details" id="meeting_details" value="{{ $meeting_detail[0]->meeting_details }}">{{ $meeting_detail[0]->meeting_details }}</textarea>
                            </div>
                            <div class="form-group">
                                <label>Mom Image Upload</label>
                                <input type="file" class="form-control" name="mom_image" id="mom_image" value="" accept="application/pdf"/>
                            </div>

                            
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.meeting') }}'" class="btn btn-default">Cancel</button>
                        </form>
                    </div>
                </div>
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
        $('#meeting_users').select2();   
        $('#mom_user_id').select2();  
        $("#meeting_date_time").datetimepicker({format: 'YYYY-MM-DD HH:mm:ss'});
        $("#meeting_end_date_time").datetimepicker({format: 'YYYY-MM-DD HH:mm:ss'});
    });

    jQuery("#add_meeting").validate({
        ignore: [],
        rules: {
            meeting_details: {
                required: true,
            },
            'meeting_users[]': {
                required: true,
            },
            meeting_date_time: {
                required: true,
            },
            meeting_end_date_time:{
                required:true
            },
            mom_user_id: { 
                required:true
            },
            meeting_subject: {
                required: true,
            }
        }
    });
      
</script>
@endsection
