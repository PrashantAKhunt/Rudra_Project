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
                    <div class="col-md-12">
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
                        <form action="{{ route('admin.update_user') }}" enctype="multipart/form-data" id="add_user" method="post">
                            @csrf
                            <input type="hidden" name="id" id="id" value="{{ $user_detail[0]->id }}" />
                            <h3 class="box-title">Basic Details</h3>
                            <hr class="m-t-0 m-b-40">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group ">
                                        <label>Employee Code<span class="error">*</span></label>
                                        <input type="text" class="form-control" readonly="" name="emp_code" id="emp_code" value="{{ $user_detail[0]->emp_code }}" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group ">
                                        <label>Name<span class="error">*</span></label>
                                        <input type="text" class="form-control" name="name" id="name" value="{{ $user_detail[0]->name}}" />
                                    </div>
                                </div>
                                @if(!empty($users))
                                <div class="col-md-6">
                                    <div class="form-group "> 
                                    <label>Reporting User</label> 
                                       
                                    <select name="reporting_user_id" id="reporting_user_id" class="form-control" >
                                            <option value="">Select Reporting User</option>
                                                @foreach($users as $key => $value)
                                                    <option @if($value['id']==$user_detail[0]->reporting_user_id) selected @endif value="{{$value['id']}}">{{$value['name']}} - ({{$value['dept_name']}})</option>
                                                @endforeach
                                            </select>
                                        
                                    </div>
                                </div>
                                @endif
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Email<span class="error">*</span></label>
                                        <input type="text" @if(\Request::route()->getName()=='admin.edit_profile') readonly="" @endif class="form-control" name="email" id="email" value="{{ $user_detail[0]->email}}" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Personal Email</label>
                                        <input type="text" class="form-control" name="personal_email" id="personal_email" value="{{ $user_detail[0]->personal_email}}" />
                                    </div>
                                </div>                                
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Department<span class="error">*</span></label>
                                        <select class="form-control" name="department" id="department">
                                            <option value="">Select Department</option>
                                            @foreach($department_list as $department)
                                            <option @if($department->id==$user_detail[0]->department_id) selected @endif value="{{$department->id}}">{{ $department->dept_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Profile image <span class="text-muted">Allowed file extensions are png, jpg, jpeg</span></label>
                                        <div> 
                                            <input type="file" name="profile_image" class="form-control" id="profile_image"  value="{{ $user_detail[0]->profile_image}}" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    @if($user_detail[0]->role!=1)
                                    <div class="form-group ">
                                        <label>Role<span class="error">*</span></label>
                                        <select class="form-control" name="role" id="role">
                                            <option value="">Select Role</option>
                                            @foreach($role_list as $role)
                                            <option @if($role->id==$user_detail[0]->role) selected @endif value="{{$role->id}}">{{ $role->role_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Company<span class="error">*</span></label>
                                        <select class="form-control" name="company" id="company">
                                            <option value="">Select Company</option>
                                            @foreach($company_list as $company)
                                            <option @if($company->id==$user_detail[0]->company_id) selected @endif value="{{$company->id}}">{{ $company->company_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Designation<span class="error">*</span></label>
                                        <input type="text" class="form-control" name="designation" id="designation" value="{{ $user_detail[0]->designation }}" />
                                    </div>                                    
                                </div>
								<div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Joining Date</label>
                                        <input type="text" readonly="" class="form-control" name="joining_date" id="joining_date" value="@if($user_detail[0]->joining_date && $user_detail[0]->joining_date != '1970-01-01') {{ date('d-m-Y',strtotime($user_detail[0]->joining_date)) }} @endif" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Birth Date<span class="error">*</span></label>
                                        <input type="text" class="form-control" readonly="" name="birth_date" id="birth_date" value="{{ date('d-m-Y',strtotime($user_detail[0]->birth_date)) }}" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group ">
                                        <label>Marital Status <span class="error">*</span></label>
                                        <div class="radio-list">
                                            <label class="radio-inline p-0">
                                                <div class="radio radio-info">
                                                    <input type="radio" name="marital_status" @if($user_detail[0]->marital_status=='Unmarried') checked @endif id="unmarried" value="Unmarried">
                                                    <label for="male">Unmarried </label>
                                                </div>
                                            </label>
                                            <label class="radio-inline">
                                                <div class="radio radio-info">
                                                    <input type="radio" name="marital_status" id="married" @if($user_detail[0]->marital_status=='Married') checked @endif value="Married">
                                                    <label for="married">Married</label>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
								<div class="col-md-3">
                                        <div class="form-group">
                                            <label class="control-label">Gender<span class="error">*</span></label>
                                            <div class="radio-list">
                                                <label class="radio-inline p-0">
                                                    <div class="radio radio-info">
                                                        <input type="radio" name="gender" @if($user_detail[0]->gender=='Male') checked="" @endif  id="male" value="Male">
                                                        <label for="male">Male</label>
                                                    </div>
                                                </label>
                                                <label class="radio-inline">
                                                    <div class="radio radio-info">
                                                        <input type="radio" name="gender" @if($user_detail[0]->gender=='Female') checked="" @endif id="female" value="Female">
                                                        <label for="female">Female</label>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Marriage Date</label>
										@if($user_detail[0]->marriage_date && date('d-m-Y',strtotime($user_detail[0]->marriage_date))!="01-01-1970")
                                        <input type="text" readonly="" class="form-control" name="marriage_date" id="marriage_date" value="{{ date('d-m-Y',strtotime($user_detail[0]->marriage_date)) }}" />
										@else
										<input type="text" readonly="" class="form-control" name="marriage_date" id="marriage_date" value="" />
										@endif
									</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Blood Group<span class="error">*</span></label>
                                        <select class="form-control" name="blood_group" id="blood_group">
                                            <option value="">Select Blood Group</option>
                                            <option @if($user_detail[0]->blood_group=='A+') selected @endif value="A+">A+</option>
                                            <option @if($user_detail[0]->blood_group=='A-') selected @endif value="A-">A-</option>
                                            <option @if($user_detail[0]->blood_group=='B+') selected @endif value="B+">B+</option>
                                            <option @if($user_detail[0]->blood_group=='B-') selected @endif value="B-">B-</option>
                                            <option @if($user_detail[0]->blood_group=='AB+') selected @endif value="AB+">AB+</option>
                                            <option @if($user_detail[0]->blood_group=='AB-') selected @endif value="AB-">AB-</option>
                                            <option @if($user_detail[0]->blood_group=='O+') selected @endif value="O+">O+</option>
                                            <option @if($user_detail[0]->blood_group=='O-') selected @endif value="O-">O-</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group ">
                                        <label>Physically Handicapped<span class="error">*</span></label>
                                        <div class="radio-list">
                                            <label class="radio-inline p-0">
                                                <div class="radio radio-info">
                                                    <input type="radio" name="physically_handicapped" @if($user_detail[0]->physically_handicapped=='Yes') checked="" @endif  id="hadicap_yes" value="Yes">
                                                    <label for="hadicap_yes">Yes</label>
                                                </div>
                                            </label>
                                            <label class="radio-inline">
                                                <div class="radio radio-info">
                                                    <input type="radio" name="physically_handicapped"  @if($user_detail[0]->physically_handicapped=='No') checked="" @endif  id="handicap_no" value="No">
                                                    <label for="handicap_no">No</label>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Digital Signature <span class="text-muted">Allowed file extensions are jpg, jpeg</span></label>
                                        <div> 
                                            <input type="file" name="digital_signature" class="form-control" id="digital_signature"  value="{{ $user_detail[0]->digital_signature}}" accept="image/x-png,image/png, image/jpg, image/jpeg" data-accept="jpg,png,jpeg"/>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group ">
                                        <label>Note if physically handicapped</label>
                                        <textarea name="handicap_note" class="form-control" id="handicap_note">{{ $user_detail[0]->handicap_notes }}</textarea>
                                    </div>
                                </div>
                                <!--  -->
                                <!--  -->
                                <div class="col-md-6">
                                <div class="form-group ">
                                <label>Add PF Number<span class="error">*</span></label>
                                        <select class="form-control" required name="pf_number" id="pf_number" onchange="optionCheck()" class="form-control select-yes-info" style="display:block" style="display:none">
                                            <option value="">Select Type</option>
                                            <option value="Yes" {{ ($user_detail[0]->pf_number != "") ? "selected" : "" }}>Yes</option>
                                            <option value="No" {{ ($user_detail[0]->pf_number == "") ? "selected" : "" }}>No</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- div id = up = yes-info -->
                                <div class="col-md-6">
                                    <div id="yes-info" style="display: none;">
                                        <div class="form-group">
                                            <label>PF Number</label>
                                            <input class="form-control" type="text" name="pf_num" id="pf_num" value="{{$user_detail[0]->pf_number}}" placeholder="PF Number 9876543210" style="display: block;">
                                        </div>
                                    </div>
                                </div>
                                <!--  -->
                                <!--  -->
                            </div>



                            <h3 class="box-title">Contact Details</h3>
                            <hr class="m-t-0 m-b-40">
							
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Contact Number<span class="error">*</span></label>
                                        <input type="text" class="form-control" name="contact_number" id="contact_number" value="{{ $user_detail[0]->contact_number }}" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Emergency Contact Number (Contact number of any relative or friend.)<span class="error">*</span></label>
                                        <input type="text" class="form-control" name="emg_contact_number" id="emg_contact_number" value="{{ $user_detail[0]->emg_contact_number }}" />
                                    </div>
                                </div>
                            </div>

							<div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Residential Address<span class="error">*</span></label>
                                        <textarea name="residential_address" id="residential_address" class="form-control">{{ $user_detail[0]->residential_address }}</textarea>
                                    </div>
                                </div>
								<div class="col-md-6">
									<div class="form-group ">
										<label></label>
										<div class="checkbox">
											<input id="is_same" type="checkbox">
											<label for="checkbox0"> Is same as Residential Address </label>
										</div>
									</div>
                                </div>                                
                            </div>

							<div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Permanent Address<span class="error">*</span></label>
                                        <textarea name="permanent_address" id="permanent_address" class="form-control">{{ $user_detail[0]->permanent_address }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Skype Id</label>
                                        <input type="text" class="form-control" id="skype" name="skype" value="{{ $user_detail[0]->skype }}" />
                                    </div>
                                </div>
                            </div>
						
                            <h3 class="box-title">Reference Details</h3>
                            <hr class="m-t-0 m-b-40">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Reference 1 Name<span class="error">*</span></label>
                                        <input type="text" name="ref_name1" id="ref_name1" class="form-control" value="{{ $user_detail[0]->ref_name1 }}" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Reference 1 Contact Number<span class="error">*</span></label>
                                        <input type="text" class="form-control" id="ref_contact1" name="ref_contact1" value="{{ $user_detail[0]->ref_contact1 }}" />
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Reference 2 Name<span class="error">*</span></label>
                                        <input type="text" name="ref_name2" id="ref_name2" class="form-control" value="{{ $user_detail[0]->ref_name2 }}" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Reference 2 Contact Number<span class="error">*</span></label>
                                        <input type="text" class="form-control" id="ref_contact2" name="ref_contact2" value="{{ $user_detail[0]->ref_contact2 }}" />
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="route_name" id="route_name" value="{{ \Route::currentRouteName() }}" />
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.users') }}'" class="btn btn-default">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" name="education_div_count" id="education_div_count" value="0" />
    <input type="hidden" name="experience_div_count" id="experience_div_count" value="0" />
</div>
@endsection


@section('script')
<script>
    jQuery("#add_user").validate({

        rules: {
            emp_code: {
                required: true,
                /*remote: {
                    url: '{{ route("admin.check_emp_code") }}',
                    type: "post",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        emp_code: function () {
                            return $('#emp_code').val();
                        }
                    }
                }*/
            },
            name: {
                required: true,

            },
            email: {
                required: true,
                email: true,
                remote: {
                    url: '{{ route("admin.edit_check_email") }}',
                    type: "post",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        email: function () {
                            return $('#email').val();
                        },
                        user_id:function(){
                            return $('#id').val();
                        }
                    }
                }
            },
           @if($user_detail[0]->role!=1)
            role: {
                required: true
            },
        @endif
            company: {
                required: true
            },
            designation: {
                required: true
            },
            birth_date: {
                required: true
            },
            blood_group: {
                required: true
            },
            residential_address: {
                required: true
            },
			permanent_address: {
                required: true
            },
            contact_number: {
                required: true
            },
            emg_contact_number: {
                required: true
            },            
            ref_name1: {
                required: true
            },
            ref_contact1: {
                required: true
            },
            ref_name2: {
                required: true
            },
            ref_contact2: {
                required: true
            },
            reporting_user_id:{
                required:true
            }
        },
        messages:{
            email:{
                remote:"Email already used. Please try with other email."
            }
        }
    });
    $("input[name=marital_status]:radio").click(function () {
        if ($('input[name=marital_status]:checked').val() == "Unmarried") {
            $("#marriage_date").val('');
        }
    });
    $(document).ready(function () {   
        optionCheck();    
        jQuery('#birth_date').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: "dd-mm-yyyy"
        });
        jQuery('#marriage_date').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: "dd-mm-yyyy"
        });
		jQuery('#joining_date').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: "dd-mm-yyyy"
        });
		$('#is_same').change(function (){
			if($(this).prop("checked") == true){
				$('#permanent_address').val($('#residential_address').val());
			}else{
				$('#permanent_address').val('');
			}
		});
    });

    function optionCheck(){
        var option = document.getElementById("pf_number").value;
        if(option == "Yes"){
            document.getElementById("yes-info").style.display ="block";
        }else{
            $("#pf_num").val('');
            document.getElementById("yes-info").style.display ="none";
        }
    }
</script>
@endsection
