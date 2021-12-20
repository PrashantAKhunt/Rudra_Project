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
        <div class="white-box">
            <div class="col-md-12">
                <label>Note : </label> All marks are out of 100% individually.
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 col-sm-12 col-xs-12">
            <div class="white-box">
                <form action="{{ route('admin.insert_interview_marks') }}" id="interview_marks" method="post">
                    @csrf
                    <input type="hidden" class="form-control" name="interview_result_id" id="interview_result_id" value="{{ $interview_result_id }}" />
                    <div class="col-md-6">
                        <label>Experience</label>
                        <input type="number" class="form-control" name="experience" id="experience" value="{{ $interview_list->experience }}" min="0" max="100" />
                    </div>
                    <div class="col-md-6">
                        <label>Knowledge</label>
                        <input type="number" class="form-control" name="knowledge" id="knowledge" value="{{ $interview_list->knowledge }}" min="0" max="100" />
                    </div>
                    <div class="col-md-6">
                        <label>Communication</label>
                        <input type="number" class="form-control" name="communication" id="communication" value="{{ $interview_list->communication }}" min="0" max="100" />
                    </div>
                    <div class="col-md-6">
                        <label>Personality</label>
                        <input type="number" class="form-control" name="personality" id="personality" value="{{ $interview_list->personality }}" min="0" max="100" />
                    </div>
                    <div class="col-md-6">
                        <label>Interpersonal Skill</label>
                        <input type="number" class="form-control" name="interpersonal_skill" id="interpersonal_skill" value="{{ $interview_list->interpersonal_skill }}" min="0" max="100" />
                    </div>
                    <div class="col-md-6">
                        <label>Decision Making</label>
                        <input type="number" class="form-control" name="decision_making" id="decision_making" value="{{ $interview_list->decision_making }}" min="0" max="100" />
                    </div>                   
                    <div class="col-md-6">
                        <label>Self Confidence</label>
                        <input type="number" class="form-control" name="self_confidence" id="self_confidence" value="{{ $interview_list->self_confidence }}" min="0" max="100" />
                    </div>
                    <div class="col-md-6">
                        <label>Acceptability</label>
                        <input type="number" class="form-control" name="acceptability" id="acceptability" value="{{ $interview_list->acceptability }}" min="0" max="100" />
                    </div>
                    <div class="col-md-6">
                        <label>Commute</label>
                        <input type="number" class="form-control" name="commute" id="commute" value="{{ $interview_list->commute }}" min="0" max="100" />
                    </div>
                     <div class="col-md-6">
                        <label>Suitability</label>
                        <input type="number" class="form-control" name="suitability" id="suitability" value="{{ $interview_list->suitability }}" min="0" max="100" />
                    </div>
                    <div class="clearfix"></div>
                    <br>

                    <div class="col-md-3">
                        <label>Technical Skill</label>
                    </div>
                     <div class="col-md-7">
                        <label>Comment</label>
                    </div>

                    <div class="technical_box">
                        <?php if(!empty($interview_list->technical_skill)){
                            $technical = unserialize($interview_list->technical_skill);
                            foreach ($technical['skill'] as $key => $value) { ?>
                                <div class="row technical_list">
                                    <div class="col-md-3">
                                        <label></label>
                                        <input type="text" class="form-control technical" name="technical[skill][]" value="{{ $value }}" />
                                    </div>
                                    <div class="col-md-7">
                                        <label></label>
                                        <input type="text" class="form-control comment" name="technical[comment][]" value="{{ $technical['comment'][$key] }}" />
                                    </div>
                                    <div class="col-md-1 padded">
                                        <label></label>
                                        <button type="button" class="remove-skill btn btn-warning btn-circle" onClick="removeSkill($(this));"><i class="fa fa-times"></i></button>
                                    </div>
                                    </br>
                                </div>
                        <?php } } ?>
                        <div class="row technical_list">
                            <div class="col-md-3">
                                <label></label>
                                <input type="text" class="form-control technical" name="technical[skill][]" />
                            </div>
                             <div class="col-md-7">
                                <label></label>
                                <input type="text" class="form-control comment" name="technical[comment][]" />
                            </div>                            
                            <div class="col-md-1 padded">
                                <label></label>
                                <button type="button" class="hide remove-skill btn btn-warning btn-circle" onClick="removeSkill($(this));"><i class="fa fa-times"></i></button>
                            </div>
                            <div class="col-md-1 padded">
                                <label></label>
                                <button type="button" class="add-skill btn btn-info btn-circle" onClick="addSkill($(this));"><i class="fa fa-plus"></i></button>
                            </div>
                            </br>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <br>
                    <div class="col-md-6 pull-left">
                        <button type="button" onclick="save_confirm(this);" class="btn btn-success">Submit</button>
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
<script>
    $(document).ready(function(){    
        $('#interview_marks').validate({
            rules:{                
                experience:{
                    required:true
                },
                knowledge:{
                    required:true
                },
                communication:{
                    required:true
                },
                personality:{
                    required:true
                },
                interpersonal_skill:{
                    required:true
                },
                decision_making:{
                    required:true
                },
                self_confidence:{
                    required:true
                },
                acceptability:{
                    required:true
                },
                commute:{
                    required:true
                },
            }
        })
    });
    function removeSkill(thisObject){
        thisObject.parents('.technical_list').remove();
    }
    function addSkill(thisObject){
        var technicalList = thisObject.parents('.technical_list').clone();
        technicalList.find('.technical').val('');
        technicalList.find('.comment').val('');
        technicalList.find('.remove-skill').removeClass('hide');
        technicalList.find('.add-skill').parent().remove();
        $('.technical_box').append(technicalList);
    }
    function save_confirm(e) {
        swal({
            title: "Are you sure you want to save marks?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes",
            closeOnConfirm: false            
        }, function () {
            $('#interview_marks').submit();
        });        
    }
</script>
<style type="text/css">
    .padded{ padding-top: 22px; }
</style>
@endsection