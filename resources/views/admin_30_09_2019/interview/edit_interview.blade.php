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
        <div class="col-lg-12 col-sm-12 col-xs-12">
          <div class="white-box">
            <!-- <h3 class="box-title">Vertical Tabs with icon</h3>
            <p class="text-muted m-b-30">Use default tab with class <code>vtabs</code></p> -->
            <?php
                $role = explode(',', $access_rule);
            ?>
            <div class="vtabs">
              <ul class="nav tabs-vertical">
                @if(in_array(2,$role))
                <li class="tab active" id="round1"><a data-toggle="tab" href="#vihome3" aria-expanded="true"> <span><i class="icon-user-female fa-fw"></i>Round 1</span></a> </li>
                @endif
                @if(in_array(2,$role))
                <li class="tab"><a data-toggle="tab" href="#viprofile3" aria-expanded="false"> <span><i class="icon-people fa-fw"></i>Round 2</span></a> </li>
                @endif
                @if(in_array(5,$role) && in_array(2,$role))
                <li class="tab"><a aria-expanded="false" data-toggle="tab" href="#vimessages3"> <span><i class="ti-user fa-fw"></i>Final Round</span></a> </li>
                @endif
              </ul>
              <div class="tab-content">
                <div id="vihome3" class="tab-pane active">
                    <form action="{{ route('admin.update_interview') }}" id="edit_interview_frm" method="post">
                    @csrf
                    <input type="hidden" name="id" value="{{ $interview_list[0]->id }}">
                    <div class="col-md-6">
                        <label>Candidate Name</label>
                        <input type="text" class="form-control" name="name" id="name" value="{{ $interview_list[0]->name }}" />
                    </div>
                    <div class="col-md-5 pull-right">
                        <label>Designation</label>
                        <select class="form-control" name="designation" id="designation">
                            <option value="">Select Designation</option>
                            @foreach($job_opening_position as $job_opening)
                                <option <?php echo ($interview_list[0]->job_opening_id==$job_opening->id)?"selected='selected'":'' ?> value="{{ $job_opening->id }}">{{ $job_opening->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="clearfix"></div>
                    <br>
                    <div class="col-md-6">
                        <label>Contact Number</label>
                        <input type="text" class="form-control" name="contact_number" id="contact_number" value="{{ $interview_list[0]->contact_number }}" />
                    </div>
                    <div class="col-md-5 pull-right">
                        <label>Emgency Contact Number</label>
                        <input type="text" class="form-control" name="emg_contact_number" id="emg_contact_number" value="{{ $interview_list[0]->emg_contact_number }}" />
                    </div>
                    <div class="clearfix"></div>
                    <br>
                    <div class="col-md-6">
                        <label>Residential Address</label>
                        <input type="text" class="form-control" name="residential_address" id="residential_address" value="{{ $interview_list[0]->residential_address }}" />
                    </div>
                    <div class="col-md-5 pull-right">
                        <label>Permanent Address</label>
                        <input type="text" class="form-control" name="permanent_address" id="permanent_address" value="{{ $interview_list[0]->permanent_address }}" />
                    </div>
                    <div class="clearfix"></div>
                    <br>
                    <div class="col-md-6">
                        <label>Gender</label>
                        <select class="form-control" name="gender" id="gender">
                            <option value="">Select Gender Type</option>
                            <option <?php echo ($interview_list[0]->gender=="male" OR $interview_list[0]->gender=="Male")?"selected='selected'":'' ?> value="male">Male</option>
                            <option <?php echo ($interview_list[0]->gender=="female" OR $interview_list[0]->gender=="Female")?"selected='selected'":'' ?> value="female">Female</option>
                        </select>
                    </div>
                    <div class="col-md-5 pull-right">
                        <label>Birth Date</label>
                        <input type="text" class="form-control" name="birth_date" id="birth_date" value="{{ $interview_list[0]->birth_date }}" />
                    </div>
                    <div class="clearfix"></div>
                    <br>
                    <div class="col-md-6">
                        <label>Marital Status</label>
                        <select class="form-control" name="marital_status" id="marital_status">
                            <option <?php echo ($interview_list[0]->marital_status=="no")?"selected='selected'":'' ?> value="no">No</option>
                            <option <?php echo ($interview_list[0]->marital_status=="yes")?"selected='selected'":'' ?> value="yes">Yes</option>
                        </select>
                    </div>
                    <div class="col-md-5 pull-right">
                        <label>Is Physically Handicapped ?</label>
                        <select class="form-control" name="physically_handicapped" id="physically_handicapped">
                            <option <?php echo ($interview_list[0]->physically_handicapped=="no")?"selected='selected'":'' ?> value="no">No</option>
                            <option <?php echo ($interview_list[0]->physically_handicapped=="yes")?"selected='selected'":'' ?> value="yes">Yes</option>
                        </select>
                    </div>
                    <div class="clearfix"></div>
                    <br>
                    <div class="col-md-6">
                        <label>Email</label>
                        <input type="text" class="form-control" name="email" id="email" value="{{ $interview_list[0]->email }}" />
                    </div>
                    <div class="col-md-6">
                        <label>HandicapNote</label>
                        <input type="text" class="form-control" name="handicap_note" id="handicap_note" value="{{ $interview_list[0]->handicap_note }}" />
                    </div>
                    <div class="col-md-5 pull-right">
                        <div class="form-group">
                            <label class="col-md-12">Round1 Note</label>
                            <div class="col-md-12">
                              <textarea class="form-control" rows="5" name="round1_note" id="round1_note">
                                  <?php echo $round1_result[0]->round_note;?>
                              </textarea>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <br>
                    <div class="col-md-6">
                        <label>Round1 Result(%)</label>
                        <input type="text" class="form-control" name="round1_result" id="round1_result" value="{{ $round1_result[0]->round_result }}" />
                    </div>
                    <div class="col-md-5 pull-right">
                        <label>Assing Next Round</label>
                        <select class="select2 m-b-10 select2-multiple" name="assign_user[]" multiple="multiple" id="assign_user">
                            <option value="">Select Interviwer</option>
                            @foreach($users_data as $consultant)
                            <option <?php echo (in_array($consultant->id,$assign_user))?"selected='selected'":'' ?> value="{{ $consultant->id }}">{{ $consultant->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5 pull-left">
                        <label>Next Round Date</label>
                        <input type="text" class="form-control" name="next_round_date" id="next_round_date" value="{{ $interview_list[0]->next_round_date }}" />
                    </div>
                    <div class="clearfix"></div>
                    <br>
                    <div class="col-md-5 pull-left">
                        <button type="submit" class="btn btn-success" <?php echo !empty($round1_result[0]->round_note)?"disabled='disabled'":'' ?>>Submit</button>
                        <button type="button" onclick="window.location.href ='{{ route('admin.interview') }}'" class="btn btn-default">Cancel</button>
                    </div>
                    </form>
                </div>
                <div id="viprofile3" class="tab-pane">
                    <form action="{{ route('admin.update_round2_interview') }}" id="edit_round2_interview_frm" method="post">
                        @csrf
                        <input type="hidden" name="id" value="{{ $interview_list[0]->id }}">
                        <div class="row">
                            <div class="col-md-4 col-xs-4 b-r"> <strong>Candidate Name</strong> <br>
                                <p class="text-muted"><?= $interview_list[0]->name .'('.$interview_list[0]->interviewee_id.')' ?></p>
                            </div>                            
                            <div class="col-md-4 col-xs-4 b-r"> <strong>Contact Number</strong> <br>
                                <p class="text-muted">{{ $interview_list[0]->contact_number }}</p>
                            </div>                            
                            <div class="col-md-4 col-xs-4 b-r"> <strong>Emg Contact Number</strong> <br>
                                <p class="text-muted">{{ $interview_list[0]->emg_contact_number }}</p>
                            </div>                            
                        </div>
                        <br>
                        <hr class="m-t-0">

                        <div class="row">
                            <div class="col-md-4 col-xs-4 b-r"> <strong>Designation</strong> <br>
                                <p class="text-muted" id="txt_designation">{{ $interview_list[0]->job_opening_id }}</p>
                            </div>                            
                            <div class="col-md-4 col-xs-4 b-r"> <strong>Residential Address</strong> <br>
                                <p class="text-muted">{{ $interview_list[0]->residential_address }}</p>
                            </div>                            
                            <div class="col-md-4 col-xs-4 b-r"> <strong>Gender</strong> <br>
                                <p class="text-muted">{{ $interview_list[0]->gender }}</p>
                            </div>                            
                        </div>
                        <br>
                        <hr class="m-t-0">

                        <div class="row">
                            <div class="col-md-4 col-xs-4 b-r"> <strong>Birth Date</strong> <br>
                                <p class="text-muted">{{ $interview_list[0]->birth_date }}</p>
                            </div>                            
                            <div class="col-md-4 col-xs-4 b-r"> <strong>Marital Status</strong> <br>
                                <p class="text-muted">{{ $interview_list[0]->marital_status }}</p>
                            </div>                            
                            <div class="col-md-4 col-xs-4 b-r"> <strong>Physically Handicapped</strong> <br>
                                <p class="text-muted">{{ $interview_list[0]->physically_handicapped }}</p>
                            </div>                            
                        </div>
                        <br>
                        <hr class="m-t-0">
                        
                        <div class="row">
                            <div class="col-md-4 col-xs-4 b-r"> <strong>Handicap Note</strong> <br>
                                <p class="text-muted">{{ $interview_list[0]->handicap_note }}</p>
                            </div>                            
                            <div class="col-md-4 col-xs-4 b-r"> <strong>Round1 Note</strong> <br>
                                <p class="text-muted">{{ $round1_result[0]->round_note }}</p>
                            </div>                            
                            <div class="col-md-4 col-xs-4 b-r"> <strong>Round Result</strong> <br>
                                <p class="text-muted">{{ $round1_result[0]->round_result }}</p>
                            </div>                            
                        </div>
                        <?php 
                        foreach($interview_details as $data) {
                        ?>
                        <div class="row">
                            <div class="col-md-4 col-xs-4 b-r"> <strong>Interviewer Name</strong> <br>
                                <p class="text-muted">{{ $data['name'] }}</p>
                            </div> 
                            <div class="col-md-4 col-xs-4 b-r"> <strong>Round Note</strong> <br>
                                <p class="text-muted">{{ $data['round_note'] }}</p>
                            </div>                            
                            <div class="col-md-4 col-xs-4 b-r"> <strong>Round Result</strong> <br>
                                <p class="text-muted">{{ $data['round_result'] }}</p>
                            </div>                                                
                        </div>
                        <br>
                        <?php
                        }
                        ?>

                        <br>
                        <hr class="m-t-0">
                        <div class="col-md-6" style="<?php echo (Auth::user()->role!=6)?"display:none":'' ?>">
                            <label>Round2 Note</label>
                            <textarea class="form-control" rows="5" name="round2_note" id="round2_note">
                                  <?php echo !empty($round2_result[0]->round_note)?$round2_result[0]->round_note:"";?>
                            </textarea>
                        </div>
                        <div class="col-md-5 pull-right" style="<?php echo (Auth::user()->role!=6)?"display:none":'' ?>">
                            <label>Round2 Result(%)</label>
                            <input type="text" class="form-control" name="round2_result" id="round2_result" value="{{ (!empty($round2_result[0]->round_result))?$round2_result[0]->round_result:'' }}" />
                        </div>
                        <div class="clearfix"></div>
                        <br>
                        <div class="col-md-5 pull-left">
                            <button type="button" <?php echo (!empty($round2_result[0]->round_note)|| Auth::user()->role!=6)?"disabled='disabled'":'' ?> onclick="Round2_Form()" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.interview') }}'" class="btn btn-default">Cancel</button>
                        </div>
                    </form>
                </div>
                <div id="vimessages3" class="tab-pane">
                    <form action="{{ route('admin.update_round3_interview') }}" id="edit_round3_interview_frm" method="post">
                        @csrf
                        <input type="hidden" name="id" value="{{ $interview_list[0]->id }}">
                        <div class="row">
                            <div class="col-md-4 col-xs-4 b-r"> <strong>Candidate Name</strong> <br>
                                <p class="text-muted"><?= $interview_list[0]->name .'('.$interview_list[0]->interviewee_id.')' ?></p>
                            </div>                            
                            <div class="col-md-4 col-xs-4 b-r"> <strong>Contact Number</strong> <br>
                                <p class="text-muted">{{ $interview_list[0]->contact_number }}</p>
                            </div>                            
                            <div class="col-md-4 col-xs-4 b-r"> <strong>Emg Contact Number</strong> <br>
                                <p class="text-muted">{{ $interview_list[0]->emg_contact_number }}</p>
                            </div>                            
                        </div>
                        <br>
                        <hr class="m-t-0">

                        <div class="row">
                            <div class="col-md-4 col-xs-4 b-r"> <strong>Designation</strong> <br>
                                <p class="text-muted" id="txt_designation1">{{ $interview_list[0]->job_opening_id }}</p>
                            </div>                            
                            <div class="col-md-4 col-xs-4 b-r"> <strong>Residential Address</strong> <br>
                                <p class="text-muted">{{ $interview_list[0]->residential_address }}</p>
                            </div>                            
                            <div class="col-md-4 col-xs-4 b-r"> <strong>Gender</strong> <br>
                                <p class="text-muted">{{ $interview_list[0]->gender }}</p>
                            </div>                            
                        </div>
                        <br>
                        <hr class="m-t-0">

                        <div class="row">
                            <div class="col-md-4 col-xs-4 b-r"> <strong>Birth Date</strong> <br>
                                <p class="text-muted">{{ $interview_list[0]->birth_date }}</p>
                            </div>                            
                            <div class="col-md-4 col-xs-4 b-r"> <strong>Marital Status</strong> <br>
                                <p class="text-muted">{{ $interview_list[0]->marital_status }}</p>
                            </div>                            
                            <div class="col-md-4 col-xs-4 b-r"> <strong>Physically Handicapped</strong> <br>
                                <p class="text-muted">{{ $interview_list[0]->physically_handicapped }}</p>
                            </div>                            
                        </div>
                        <br>
                        <hr class="m-t-0">
                        
                        <div class="row">
                            <div class="col-md-12 col-xs-12 b-r"> <strong>Handicap Note</strong> <br>
                                <p class="text-muted">{{ $interview_list[0]->handicap_note }}</p>
                            </div>                                                       
                        </div>
                        <br>
                        <hr class="m-t-0">
                        <?php 
                        foreach($interview_details as $data) {
                        ?>
                        <div class="row">
                            <div class="col-md-4 col-xs-4 b-r"> <strong>Interviewer Name</strong> <br>
                                <p class="text-muted">{{ $data['name'] }}</p>
                            </div> 
                            <div class="col-md-4 col-xs-4 b-r"> <strong>Round Note</strong> <br>
                                <p class="text-muted">{{ $data['round_note'] }}</p>
                            </div>                            
                            <div class="col-md-4 col-xs-4 b-r"> <strong>Round Result</strong> <br>
                                <p class="text-muted">{{ $data['round_result'] }}</p>
                            </div>                                                
                        </div>
                        <br>
                        <hr class="m-t-0">
                        <?php
                        }
                        ?>
                        
                        
                        <div class="col-md-6">
                            <label>Final Round Note</label>
                            <textarea class="form-control" rows="5" name="round3_note" id="round3_note">
                                <?php echo !empty($interview_list[0]->round3_note)?$interview_list[0]->round3_note:"";?>
                            </textarea>
                        </div>
                        <div class="col-md-6 pull-right">
                            <div class="form-group">
                              <label class="control-label">Final Result</label>
                              <div class="radio-list">
                                <label class="radio-inline p-0">
                                <div class="radio radio-info">
                                  <input type="hidden" id="round3_result_status" name="round3_result_status" value="<?php echo !empty($interview_list[0]->emp_status)?$interview_list[0]->emp_status:"";?>">
                                  <input type="radio" name="round3_result" id="selected" value="selected">
                                  <label for="radio1">Select Person</label>
                                </div>
                                </label>
                                <label class="radio-inline">
                                <div class="radio radio-info">
                                  <input type="radio" name="round3_result" id="selected" value="rejected">
                                  <label for="radio2">Reject Person</label>
                                </div>
                                </label>
                              </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <br>
                        <div class="col-md-12 pull-left">
                            <button type="button" onclick="Round3_Form()" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.interview') }}'" class="btn btn-default">Cancel</button>
                        </div>
                    </form>
                </div>
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
        $('ul.tabs-vertical li:first').css('background-color','#00c292');
        var round2_result = $('#round2_note').val();
        var round3_result = $('#round3_note').val();
        if(round2_result.length>0)
        {
            $('ul.tabs-vertical li:eq(1)').css('background-color','#00c292');
        }
        if(round3_result.length>0)
        {
            $('ul.tabs-vertical li:eq(2)').css('background-color','#00c292');
        }
        var round3_result_status =  $("#round3_result_status").val();
        if(round3_result_status=='selected' || round3_result_status=='rejected'){
            document.getElementById($("#round3_result_status").val()).checked = true;
        }
        //$('#round1').css('background-color','#00c292');
        removeTextAreaWhiteSpace();

        jQuery('#birth_date').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: "dd-mm-yyyy"
        });
        
        // jQuery('#next_round_date').datepicker({
        //     autoclose: true,
        //     todayHighlight: true,
        //     format: "dd-mm-yyyy"
        // });
        $("#next_round_date").datetimepicker({format: 'yyyy-mm-dd hh:ii'});
        $('#assign_user').select2();
        $('#txt_designation').text($("#designation option:selected").text());
        $('#txt_designation1').text($("#designation option:selected").text());
        $('#edit_interview_frm').validate({
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
                },
                handicap_note:{
                    required:true
                },
                round1_note:{
                    required:true
                }
            }
        });

        $('#edit_round2_interview_frm').validate({
            rules:{
                round2_note:{
                    required:true
                },
                round2_result:{
                    required:true
                }
            }
        });

        $('#edit_round3_interview_frm').validate({
            rules:{
                round3_note:{
                    required:true
                }
            }
        });
        
    });

    function removeTextAreaWhiteSpace() {
        var myTxtArea = document.getElementById('round3_note');
        myTxtArea.value = myTxtArea.value.replace(/^\s*|\s*$/g,'');

        var myTxtArea1 = document.getElementById('round2_note');
        myTxtArea1.value = myTxtArea1.value.replace(/^\s*|\s*$/g,'');

        var myTxtArea2 = document.getElementById('round1_note');
        myTxtArea2.value = myTxtArea2.value.replace(/^\s*|\s*$/g,'');
    }
    function Round2_Form(e) {
        swal({
            title: "Are you sure you want to confirm intervieew note and result?",
            //text: "You want to change status of admin user.",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes",
            closeOnConfirm: false
        }, function () {
            $("#edit_round2_interview_frm").submit();
        });
    }
    function Round3_Form(e) {
        swal({
            title: "Are you sure you want to confirm intervieew note and result?",
            //text: "You want to change status of admin user.",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes",
            closeOnConfirm: false
        }, function () {
            $("#edit_round3_interview_frm").submit();
        });
    }

</script>
@endsection
