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

    <?php $totalAverage = 0; if(!empty($interview_result[0]->id)){ ?>

    <?php if(  Auth::user()->role == config('constants.SuperUser') || Auth::user()->role == config('constants.REAL_HR') ){ ?>
        <div class="row">
           
           @if( Auth::user()->role == config('constants.REAL_HR')  )
            @if( $interview_details->emp_status == 'completed' &&  $interview_details->hr_status == 'Pending' )
            
            <div class="col-sm-1">
                <a onclick="save_confirm(this);" data-href="{{ route('admin.interview_action',['id'=>$interview_details->id, 'status' => 'selected']) }}" class="btn btn-success">Select</a>
            </div>
            <div class="col-sm-1">
                <a onclick="save_confirm(this);" data-href="{{ route('admin.interview_action',['id'=>$interview_details->id, 'status' => 'rejected']) }}" class="btn btn-primary">Reject</a>
            </div>
            @endif

                @if( $interview_details->hr_status != 'Selected' && $interview_details->emp_status != 'rejected'  )
                @if( $interview_details->emp_status == 'hold' )
                  
                <div class="col-sm-1">
                    <a onclick="save_confirm(this);" data-href="{{ route('admin.interviewIsOnHold',['id'=>$interview_details->id, 'status' => 'ContinueBack']) }}" class="btn btn-info">Release Hold</a>
                </div>
                
                @else
                   
                <div class="col-sm-1">
                    <a onclick="save_confirm(this);" data-href="{{ route('admin.interview_action',['id'=>$interview_details->id, 'status' => 'hold']) }}" class="btn btn-custom">Hold</a>
                </div>
                @endif  
                @endif     
           
           @endif

           @if( Auth::user()->role == config('constants.SuperUser') )
           @if( $interview_details->emp_status == 'completed' &&  $interview_details->hr_status == 'Selected' &&  $interview_details->superUser_status == 'Pending')
            <div class="col-sm-1">
                <a onclick="save_confirm(this);" data-href="{{ route('admin.interview_action',['id'=>$interview_details->id, 'status' => 'selected']) }}" class="btn btn-success">Select</a>
            </div>
            <div class="col-sm-1">
                <a onclick="save_confirm(this);" data-href="{{ route('admin.interview_action',['id'=>$interview_details->id, 'status' => 'rejected']) }}" class="btn btn-primary">Reject</a>
            </div>
            <div class="col-sm-1">
                <a onclick="save_confirm(this);" data-href="{{ route('admin.interview_action',['id'=>$interview_details->id, 'status' => 'hold']) }}" class="btn btn-custom">Hold</a>
            </div>
            @endif

               
                    @if( $interview_details->superUser_status == 'Hold' )
                    <div class="col-sm-1">
                        <a onclick="save_confirm(this);" data-href="{{ route('admin.interviewIsOnHold',['id'=>$interview_details->id, 'status' => 'ContinueBack']) }}" class="btn btn-info">Release Hold</a>
                    </div>
                    @endif
        
           @endif

        </div>
        </br>
    <?php } ?>


            <!-- @if(Auth::user()->role == config('constants.REAL_HR') && $interview_details->emp_status != 'completed')
            <div class="row">

                 @if( $interview_details->emp_status == 'hold' ) 
                <div class="col-sm-1">
                    <a onclick="save_confirm(this);" data-href="{{ route('admin.interviewIsOnHold',['id'=>$interview_details->id, 'status' => 'ContinueBack'] ) }}" class="btn btn-info">Continue Back</a>
                </div>
                @else
                <div class="col-sm-1">
                    <a onclick="save_confirm(this);" data-href="{{ route('admin.interviewIsOnHold',['id'=>$interview_details->id, 'status' => 'hold'] ) }}" class="btn btn-custom">Hold</a>
                </div>
                @endif
            </div>
            @endif -->


    <!-- <?php if((Auth::user()->role == config('constants.REAL_HR')) && ($interview_details->emp_status == 'pending' || $interview_details->emp_status == 'completed') ){ ?>
        <div class="row">
            <div class="col-sm-1">
                <a onclick="save_confirm(this);" data-href="{{ route('admin.interview_action',['id'=>$interview_details->id, 'status' => 'hold']) }}" class="btn btn-custom">Hold</a>
            </div>            
        </div>
    <?php } elseif((Auth::user()->role == config('constants.REAL_HR')) && $interview_details->emp_status == 'hold'){ ?>
        <div class="row">
            <div class="col-sm-1">
                <a onclick="save_confirm(this);" data-href="{{ route('admin.interview_action',['id'=>$interview_details->id, 'status' => 'remove_hold']) }}" class="btn btn-custom">Release Hold</a>
            </div>            
        </div>
    <?php } ?> -->

        <div class="row">
            <div class="col-sm-9"></div>
            <div class="col-sm-3 inbox-panel">
                <span class="total_average btn btn-primary btn-block waves-effect waves-light"></span>
            </div>
        </div>
        <hr>
    <?php     
        $counter = $totalMarks = 0; 

        foreach ($interview_result as $key => $interview) { 
            $counter++; 
            $marks = ($interview->experience + $interview->knowledge + $interview->communication + $interview->personality + $interview->interpersonal_skill + $interview->decision_making + $interview->self_confidence + $interview->acceptability + $interview->commute + $interview->suitability)/10;

        $totalMarks += $marks;
        ?>
        <div class="row">
            <div class="col-sm-3 inbox-panel">
                <a href="#" class="btn btn-custom btn-block waves-effect waves-light">Interview Round No: {{ $key+1 }} </a>
            </div>
            <div class="col-sm-6"></div>
            <div class="col-sm-3 inbox-panel">
                <a href="#" class="btn btn-success btn-block waves-effect waves-light">Average Resut: {{ round($marks,2) }}% </a>
            </div>
        </div>
        </br>
        <div class="row">
            <div class="col-lg-12 col-sm-12 col-xs-12">
                <div class="white-box">
                    <div class="col-md-6">
                        <label>Inetrview Date : </label> {{ $interview->interview_date }}
                    </div>
                    <div class="col-md-6">
                        <label>interviwer : </label> {{ $interview->interviewer_ids }}
                    </div>
                    <div class="clearfix"></div>
                    <br>
                    <div class="col-md-6">
                        <label>experience : </label> {{ $interview->experience }}
                    </div>
                    <div class="col-md-6">
                        <label>Knowledge : </label> {{ $interview->knowledge }}
                    </div>
                    <div class="clearfix"></div>
                    <br>
                    <div class="col-md-6">
                        <label>Communication : </label> {{ $interview->communication }}
                    </div>
                    <div class="col-md-6">
                        <label>Personality : </label> {{ $interview->personality }}
                    </div>
                    <div class="clearfix"></div>
                    <br>
                    <div class="col-md-6">
                        <label>Interpersonal Skill : </label> {{ $interview->interpersonal_skill }}
                    </div>
                    <div class="col-md-6">
                        <label>Decision Making : </label> {{ $interview->decision_making }}
                    </div>
                    <div class="clearfix"></div>
                    <br>
                    <div class="col-md-6">
                        <label>Self Confidence : </label> {{ $interview->self_confidence }}
                    </div>
                    <div class="col-md-6">
                        <label>Acceptability : </label> {{ $interview->acceptability }}
                    </div>
                    <div class="clearfix"></div>
                    <br>
                    <div class="col-md-6">
                        <label>Commute : </label> {{ $interview->commute }}
                    </div>
                    <div class="col-md-6">
                        <label>Suitability : </label> {{ $interview->suitability }}
                    </div>
                    <div class="clearfix"></div>
                    <br>
                    <hr>
                    <div class="col-md-12">
                        <label>Technical Skill : Comment</label>
                    </div>                    
                    <br><hr>
                    <?php if(!empty($interview->technical_skill)){
                            $technical = unserialize($interview->technical_skill);
                            foreach ($technical['skill'] as $key => $value) { ?>
                                <div class="col-md-12">
                                    <label>{{ $value }} : </label> {{ $technical['comment'][$key] }}
                                </div>
                                <div class="clearfix"></div>
                                <br>
                    <?php } } ?>
                </div>
            </div>
        </div>
    <?php }  
        $totalAverage = round(($totalMarks/$counter),2);
    } else { ?>
        <div class="row">
            <div class="col-lg-12 col-sm-12 col-xs-12">
                <div class="white-box">
                    <div class="col-md-6">
                        <label>Result Not Found !!!</label>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    </br>
    <div class="row">
        <div class="col-sm-2">
            <input type="hidden" class="average" value=" {{ $totalAverage }} ">
            <button type="button" onclick="window.location.href ='{{ route('admin.interview') }}'" class="btn btn-primary">Back</button>
        </div>
    </div>
</div>
@endsection
@section('script')
<script>
    $(document).ready(function(){
        $('.total_average').html("All Round Average : "+$('.average').val()+"%");
    });
    function save_confirm(e) {
        swal({
            title: "Are you sure you want to perform this action?",
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