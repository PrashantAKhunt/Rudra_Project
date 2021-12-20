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
            <div class="vtabs">
              <ul class="nav tabs-vertical">
                <li class="tab active"><a data-toggle="tab" href="#vihome3" aria-expanded="true"> <span><i class="icon-user-female fa-fw"></i>Round 1</span></a> </li>
                <li class="tab"><a data-toggle="tab" href="#viprofile3" aria-expanded="false"> <span><i class="icon-people fa-fw"></i>Round 2</span></a> </li>
                <li class="tab"><a aria-expanded="false" data-toggle="tab" href="#vimessages3"> <span><i class="ti-user fa-fw"></i>Final Round</span></a> </li>
              </ul>
              <div class="tab-content">
                <div id="vihome3" class="tab-pane active">
                    <form action="{{ route('admin.insert_interview') }}" id="add_interview_frm" method="post">
                    @csrf
                    <div class="col-md-6">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name" id="name" value="" />
                    </div>
                    <div class="col-md-5 pull-right">
                        <label>Designation</label>
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
                        <label>Contact Number</label>
                        <input type="text" class="form-control" name="contact_number" id="contact_number" value="" />
                    </div>
                    <div class="col-md-5 pull-right">
                        <label>Emgency Contact Number</label>
                        <input type="text" class="form-control" name="emg_contact_number" id="emg_contact_number" value="" />
                    </div>
                    <div class="clearfix"></div>
                    <br>
                    <div class="col-md-6">
                        <label>Residential Address</label>
                        <input type="text" class="form-control" name="residential_address" id="residential_address" value="" />
                    </div>
                    <div class="col-md-5 pull-right">
                        <label>Permanent Address</label>
                        <input type="text" class="form-control" name="permanent_address" id="permanent_address" value="" />
                    </div>
                    <div class="clearfix"></div>
                    <br>
                    <div class="col-md-6">
                        <label>Gender</label>
                        <select class="form-control" name="gender" id="gender">
                            <option value="">Select Gender Type</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                    <div class="col-md-5 pull-right">
                        <label>Birth Date</label>
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
                    <div class="col-md-5 pull-right">
                        <label>Is Physically Handicapped ?</label>
                        <select class="form-control" name="physically_handicapped" id="physically_handicapped">
                            <option value="no">No</option>
                            <option value="yes">Yes</option>
                        </select>
                    </div>
                    <div class="clearfix"></div>
                    <br>
                    <div class="col-md-6">
                        <label>Email</label>
                        <input type="text" class="form-control" name="email" id="email" />
                    </div>
                    <div class="col-md-5 pull-right">
                        <label>HandicapNote</label>
                        <input type="text" class="form-control" name="handicap_note" id="handicap_note" value="" />
                    </div>
                    <div class="col-md-6">
                        <label>Round1 Note</label>
                        <input type="text" class="form-control" name="round1_note" id="round1_note" value="" />
                    </div>
                    <div class="clearfix"></div>
                    <br>
                    <div class="col-md-6">
                        <label>Round1 Result</label>
                        <input type="text" class="form-control" name="round1_result" id="round1_result" value="" />
                    </div>
                    <div class="col-md-5 pull-right">
                        <label>Assing Next Round</label>
                        <select class="select2 m-b-10 select2-multiple" name="assign_user[]" multiple="multiple" id="assign_user">
                            <option value="">Select Interviwer</option>
                            @foreach($users_data as $consultant)
                            <option value="{{ $consultant->id }}">{{ $consultant->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5 pull-left">
                        <label>Next Round Date</label>
                        <input type="text" class="form-control" name="next_round_date" id="next_round_date" value="" />
                    </div>
                    <div class="clearfix"></div>
                    <br>
                    <div class="col-md-5 pull-left">
                        <button type="submit" class="btn btn-success">Submit</button>
                        <button type="button" onclick="window.location.href ='{{ route('admin.interview') }}'" class="btn btn-default">Cancel</button>
                    </div>
                    </form>
                </div>
                <div id="viprofile3" class="tab-pane">
                    <div class="col-md-6">
                        <label>Round2 Note</label>
                        <input type="text" class="form-control" name="round1_note" id="round1_note" value="" />
                    </div>
                    <div class="col-md-5 pull-right">
                        <label>Round2 Result</label>
                        <input type="text" class="form-control" name="round1_note" id="round1_note" value="" />
                    </div>
                    <div class="clearfix"></div>
                    <br>
                    <div class="col-md-5 pull-left">
                        <button type="submit" class="btn btn-success" disabled="">Submit</button>
                        <button type="button" onclick="window.location.href ='{{ route('admin.interview') }}'" class="btn btn-default">Cancel</button>
                    </div>
                </div>
                <div id="vimessages3" class="tab-pane">
                    <div class="col-md-6">
                        <label>Round3 Note</label>
                        <input type="text" class="form-control" name="round1_note" id="round1_note" value="" />
                    </div>
                    <div class="col-md-5 pull-right">
                        <label>Round3 Result</label>
                        <input type="text" class="form-control" name="round1_note" id="round1_note" value="" />
                    </div>
                    <div class="clearfix"></div>
                    <br>
                    <div class="col-md-5 pull-left">
                        <button type="button" onclick="Round1_Form()" class="btn btn-success" disabled="">Submit</button>
                        <button type="button" onclick="window.location.href ='{{ route('admin.interview') }}'" class="btn btn-default">Cancel</button>
                    </div>
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
        
        jQuery('#birth_date').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: "dd-mm-yyyy"
        });
        
        // jQuery('#next_round_date').datepicker({
        //     autoclose: true,
        //     todayHighlight: true,
        //     format: "dd-mm-yyyy h:mm:ss",
        //     timePicker: true,
        //     timePickerIncrement: 30,
        //     timePicker24Hour: true,
        //     timePickerSeconds: true,
        //     locale: {
        //         format: 'MM-DD-YYYY h:mm:ss'
        //     }
        // });
        $("#next_round_date").datetimepicker({format: 'yyyy-mm-dd hh:ii'});
        $('#assign_user').select2();
        
        $('#add_interview_frm').validate({
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
                assign_user:{
                    required:true
                },
                round1_note:{
                    required:true
                },
                round1_result:{
                    required:true
                }
            }
        })
        
    });
    function Round1_Form(e) {
        swal({
            title: "Are you sure you want to confirm intervieew note and result?",
            //text: "You want to change status of admin user.",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes",
            closeOnConfirm: false
        }, function () {
            $("#add_interview_frm").submit();
        });
    }
</script>
@endsection
