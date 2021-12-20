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
        <div class="col-md-3 inbox-panel">
            <a href="#" class="btn btn-custom btn-block waves-effect waves-light">Interview Round No: {{ $interview_round }}</a>            
        </div>
        <div class="col-md-6"></div>
        <?php if((Auth::user()->role == config('constants.REAL_HR')) && $interview_round > 1 && $interview->emp_status == 'pending'){ ?>
            <div class="col-sm-3 inbox-panel">
                <a onclick="save_confirm(this);" data-href="{{ route('admin.interview_complete',['id'=>$interview->id]) }}" class="btn btn-success btn-block waves-effect waves-light">Complete Interview Process</a>
            </div>
        <?php } ?>
    </div>
    </br>
    <div class="row">
        <div class="col-lg-12 col-sm-12 col-xs-12">
            <div class="white-box">
                <form action="{{ route('admin.insert_next_interview') }}" id="add_next_interview" method="post">
                    @csrf
                    <input type="hidden" class="form-control" name="interview_id" id="interview_id" value="{{ $interview->id }}" />
                    <?php if(!isset($interview_list)){ ?>
                        <div class="col-md-6 pull-left">
                            <label>Next Round Date</label>
                            <input type="text" class="form-control" name="interview_date" id="interview_date" value="" />
                        </div>
                        <div class="col-md-6 pull-right">
                            <label>Assing Next Round</label>
                            <select class="select2 m-b-10 select2-multiple" name="interviewer_ids[]" multiple="multiple" id="interviewer_ids">
                                @foreach($users_data as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    <?php } else { ?>
                        <input type="hidden" class="form-control" name="interview_result_id" id="interview_result_id" value="{{ $interview_result_id }}" />
                        <div class="col-md-6 pull-left">
                            <label>Next Round Date</label>
                            <input type="text" class="form-control" name="interview_date" id="interview_date" value="{{ $interview_list->interview_date }}" />
                        </div>
                        <div class="col-md-6 pull-right">
                            <label>Assing Next Round</label>
                            <select class="select2 m-b-10 select2-multiple" name="interviewer_ids[]" multiple="multiple" id="interviewer_ids">
                                <option value="">Select Interviwer</option>
                                @foreach($users_data as $user)
                                    <option <?php echo (in_array($user->id, explode(',',$interview_list->interviewer_ids)))?"selected='selected'":'' ?> value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    <?php } ?>
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
        $('#interview_date').datetimepicker();
        $('#interviewer_ids').select2();        
        $('#add_next_interview').validate({
            rules:{                
                interview_date:{
                    required:true
                },
                interviewer_ids:{
                    required:true
                }
            }
        });        
    });
    function save_confirm(e) {
        swal({
            title: "Are you sure you want to complete process?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes",
            closeOnConfirm: false
        }, function () {
            window.location.href = $(e).attr('data-href');
        });
    }
</script>
@endsection