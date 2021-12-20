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
                        <form action="{{ route('admin.insert_user') }}" enctype="multipart/form-data" id="add_user" method="post">
                            @csrf
                            <h3 class="box-title">Basic Details</h3>
                            <hr class="m-t-0 m-b-40">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group ">
                                        <label>Employee Code<span class="error">*</span></label>
                                        <input type="text" class="form-control" name="emp_code" id="emp_code" value="" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group ">
                                        <label>Name<span class="error">*</span></label>
                                        <input type="text" class="form-control" name="name" id="name" value="" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Department<span class="error">*</span></label>
                                        <select class="form-control" name="department" id="department">
                                            <option value="">Select Department</option>
                                            @foreach($department_list as $department)
                                            <option value="{{$department->id}}">{{ $department->dept_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">                                
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Email<span class="error">*</span></label>
                                        <input type="text" class="form-control" name="email" id="email" value="" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Personal Email</label>
                                        <input type="text" class="form-control" name="personal_email" id="personal_email" value="" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">                                
                                <div class="col-md-6">
                                    <div class="form-group "> 
                                        <label>Reporting User</label> 
                                        @if(!empty($users))
                                        <select name="reporting_user_id" id="reporting_user_id" class="form-control" >
                                            <option value="">Select Reporting User</option>
                                            @foreach($users as $key => $value)
                                            <option value="{{$value['id']}}">{{$value['name']}} - ({{$value['dept_name']}})</option>
                                            @endforeach
                                        </select>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Profile image <span class="text-muted">Allowed file extensions are png, jpg, jpeg</span></label>
                                        <div> 
                                            <input type="file" name="profile_image" class="form-control" id="profile_image"/>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Password<span class="error">*</span></label>
                                        <input type="password" class="form-control" name="password" id="password" value="" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group ">

                                        <label>Confirm Password<span class="error">*</span></label>
                                        <input type="password" class="form-control" name="conf_password" id="conf_password" value="" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">

                                        <label>Role<span class="error">*</span></label>
                                        <select class="form-control" name="role" id="role">
                                            <option value="">Select Role</option>
                                            @foreach($role_list as $role)
                                            <option value="{{$role->id}}">{{ $role->role_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group ">

                                        <label>Company<span class="error">*</span></label>
                                        <select class="form-control" name="company" id="company">
                                            <option value="">Select Company</option>
                                            @foreach($company_list as $company)
                                            <option value="{{$company->id}}">{{ $company->company_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Designation<span class="error">*</span></label>
                                        <input type="text" class="form-control" name="designation" id="designation" value="" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Joining Date<span class="error">*</span></label>
                                        <input type="text" class="form-control" readonly="" name="joining_date" id="joining_date" value="" />
                                    </div>
                                </div>								                               
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Birth Date<span class="error">*</span></label>
                                        <input type="text" class="form-control" readonly="" name="birth_date" id="birth_date" value="" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group ">
                                        <label>Marital Status<span class="error">*</span></label>
                                        <div class="radio-list">
                                            <label class="radio-inline p-0">
                                                <div class="radio radio-info">
                                                    <input type="radio" name="marital_status" checked="" id="unmarried" value="Unmarried">
                                                    <label for="male">Unmarried</label>
                                                </div>
                                            </label>
                                            <label class="radio-inline">
                                                <div class="radio radio-info">
                                                    <input type="radio" name="marital_status" id="married" value="Married">
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
                                                    <input type="radio" name="gender" checked="" id="male" value="Male">
                                                    <label for="male">Male</label>
                                                </div>
                                            </label>
                                            <label class="radio-inline">
                                                <div class="radio radio-info">
                                                    <input type="radio" name="gender" id="female" value="Female">
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
                                        <input type="text" readonly="" class="form-control" name="marriage_date" id="marriage_date" value="" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group ">

                                        <label>Blood Group<span class="error">*</span></label>
                                        <select class="form-control" name="blood_group" id="blood_group">
                                            <option value="">Select Blood Group</option>
                                            <option value="A+">A+</option>
                                            <option value="A-">A-</option>
                                            <option value="B+">B+</option>
                                            <option value="B-">B-</option>
                                            <option value="AB+">AB+</option>
                                            <option value="AB-">AB-</option>
                                            <option value="O+">O+</option>
                                            <option value="O-">O-</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Physically Handicapped<span class="error">*</span></label>
                                        <div class="radio-list">
                                            <label class="radio-inline p-0">
                                                <div class="radio radio-info">
                                                    <input type="radio" name="physically_handicapped"  id="hadicap_yes" value="Yes">
                                                    <label for="hadicap_yes">Yes</label>
                                                </div>
                                            </label>
                                            <label class="radio-inline">
                                                <div class="radio radio-info">
                                                    <input type="radio" name="physically_handicapped" checked="" id="handicap_no" value="No">
                                                    <label for="handicap_no">No</label>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Note if physically handicapped</label>
                                        <textarea class="form-control" name="handicap_note" id="handicap_note"></textarea>
                                    </div>
                                </div>
                            </div>

                            <h3 class="box-title">Contact Details</h3>
                            <hr class="m-t-0 m-b-40">

                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Contact Number<span class="error">*</span></label>
                                        <input type="text" class="form-control" name="contact_number" id="contact_number" value="" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Emergency Contact Number (Contact number of any relative or friend.)<span class="error">*</span></label>
                                        <input type="text" class="form-control" name="emg_contact_number" id="emg_contact_number" value="" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Residential Address<span class="error">*</span></label>
                                        <textarea name="residential_address" id="residential_address" class="form-control"></textarea>
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
                                        <textarea name="permanent_address" id="permanent_address" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Skype Id</label>
                                        <input type="text" class="form-control" id="skype" name="skype" />
                                    </div>
                                </div>
                            </div>

                            <h3 class="box-title">Reference Details</h3>
                            <hr class="m-t-0 m-b-40">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Reference 1 Name<span class="error">*</span></label>
                                        <input type="text" name="ref_name1" id="ref_name1" class="form-control" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Reference 1 Contact Number<span class="error">*</span></label>
                                        <input type="text" class="form-control" id="ref_contact1" name="ref_contact1" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Reference 2 Name<span class="error">*</span></label>
                                        <input type="text" name="ref_name2" id="ref_name2" class="form-control" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Reference 2 Contact Number<span class="error">*</span></label>
                                        <input type="text" class="form-control" id="ref_contact2" name="ref_contact2" />
                                    </div>
                                </div>
                            </div>

                            <h3 class="box-title">Leave Details</h3>
                            <hr class="m-t-0 m-b-40">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Sick<span class="error">*</span></label>
                                        <input type="text" name="leave[1]" id="sick" value="0" class="form-control" />
                                        <input type="hidden" name="leave[2]" id="earned" value="0" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Casual<span class="error">*</span></label>
                                        <input type="text" class="form-control" id="casual" value="0" name="leave[3]" />
                                        <input type="hidden" name="leave[4]" id="unpaid" value="0" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Short<span class="error">*</span></label>
                                        <input type="text" class="form-control" id="short" value="0" name="leave[5]" />                                        
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Comp off<span class="error">*</span></label>
                                        <input type="text" name="leave[6]" id="compoff" value="0" class="form-control" />
                                    </div>
                                </div>
                            </div>

                            <h3 class="box-title">Education Details</h3>
                            <hr class="m-t-0 m-b-40">
                            <div id="all_education_div">
                                <div id="education_div0">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group ">
                                                <label>Degree<span class="error">*</span></label>
                                                <input type="text" name="degree[]" id="degree" class="form-control degree" />
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group ">
                                                <label>Specialization<span class="error">*</span></label>
                                                <input type="text" class="form-control" id="specialization" name="specialization[]" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group ">
                                                <label>University/Collage<span class="error">*</span></label>
                                                <input type="text" name="institute[]" id="institute" class="form-control" />
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group ">
                                                <label>Time Period<span class="error">*</span></label>
                                                <input class="form-control degree_time_period" readonly="" type="text" name="degree_time_period[]" id="degree_time_period" value=""/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group ">
                                                <label>CGPA/Percentage<span class="error">*</span></label>
                                                <input type="text" name="percentage[]" id="percentage" class="form-control" />
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group ">
                                                <label>Upload Certificate(Only JPG, JPEG and PNG)</label>
                                                <input type="file" accept="image/x-png, image/jpg, image/jpeg" name="degree_certificate[]" id="degree_certificate0" class="dropify" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr class="m-t-0 m-b-40">
                            </div>
                            <button type="button" class="btn btn-primary" onclick="add_new_education();"><i class="fa fa-plus"></i> Add New Education</button>
                            <br><br>


                            <h3 class="box-title">Experience Details</h3>
                            <hr class="m-t-0 m-b-40">
                            <div id="all_experience_div">
                                <div id="experience_div0">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group ">
                                                <label>Company Name</label>
                                                <input type="text" name="exp_company_name[]" class="form-control" />
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group ">
                                                <label>Job Title</label>
                                                <input type="text" class="form-control" name="exp_job_title[]" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group ">
                                                <label>Location</label>
                                                <input type="text" name="exp_location[]" class="form-control" />
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group ">
                                                <label>Time Period</label>
                                                <input class="form-control exp_time_period" readonly="" type="text" name="exp_time_period[]" value=""/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group ">
                                                <label>Description</label>
                                                <textarea name="exp_description[]" class="form-control"></textarea>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group ">
                                                <label>Upload Document(Only PDF, JPG, JPEG and PNG)</label>
                                                <input type="file"  id="input-file-now" accept="image/x-png, image/jpg, image/jpeg,application/pdf" name="exp_document[]" class="form-control" />

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <hr class="m-t-0 m-b-40">
                            </div>
                            <button type="button" class="btn btn-primary" onclick="add_new_experience();"><i class="fa fa-plus"></i> Add New Experience</button>
                            <br><br>

                            <h3 class="box-title">Identity Document</h3>
                            <hr class="m-t-0 m-b-40">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Document Type<span class="error">*</span></label>
                                        <select class="form-control" required name="document_type" id="document_type">
                                            <option value="">Select Document Type</option>
                                            <option value="driving_license">Driver's License</option>
                                            <option value="pan_card">Pan Card</option>
                                            <option value="passport">Passport</option>
                                            <option value="aadhar">Aadhar Card</option>
                                            <option value="voter_id">Voter Id</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Upload Document(Only JPG, JPEG and PNG)<span class="error">*</span></label>
                                        <input type="file" id="identity_document" accept="image/x-png, image/jpg, image/jpeg" name="identity_document" required class="dropify" />
                                    </div>
                                </div>
                            </div>

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
                remote: {
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
                }
            },
            name: {
                required: true,
            },
            department: {
                required: true,
            },
            email: {
                required: true,
                email: true,
                remote: {
                    url: '{{ route("admin.check_email") }}',
                    type: "post",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        email: function () {
                            return $('#email').val();
                        }
                    }
                }
            },
            password: {
                required: true,
                minlength: 8,
                pwcheck: true
            },
            conf_password: {
                required: true,
            },
            role: {
                required: true
            },
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
            'degree[]': {
                required: true
            },
            'specialization[]': {
                required: true
            },
            'institute[]': {
                required: true
            },
            'degree_time_period[]': {
                required: true
            },
            'percentage[]': {
                required: true
            },
            'degree_certificate[]': {
                accept: "jpg,png,jpeg"
            },
            document_type: {
                required: true
            },
            identity_document: {
                required: true,
                accept: "jpg,png,jpeg"
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
            'leave[1]': {
                required: true,
                number: true
            },
            'leave[3]': {
                required: true,
                number: true
            },
            'leave[5]': {
                required: true,
                number: true
            },
            reporting_user_id: {
                required: true
            }
        },
        messages: {
            emp_code: {
                remote: "Employee code already in use."
            },
            email: {
                remote: "Email already in use."
            }
        }
    });
    $.validator.addMethod("pwcheck",
            function (value, element) {
                if (value != "") {
                    return /^(?=.*[0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{8,16}$/.test(value);
                } else {
                    return true;
                }
            }, 'Password must contain minimum 8 character, atleast one number and one special character.');

    function add_new_education() {
        var total_count = parseInt($('#education_div_count').val());
        var new_total_count = total_count + 1;

        var append_html = '<div id="education_div' + new_total_count + '">' +
                '  <button onclick="remove_education(' + new_total_count + ')" class="btn btn-danger pull-left " type="button"><i class="fa fa-trash"></i> Remove</button><br><br><br>' +
                ' <div class="row">' +
                '   <div class="col-md-6">' +
                '  <div class="form-group ">' +
                '     <label>Degree<span class="error">*</span></label>' +
                '     <input type="text" required name="degree[]" class="form-control degree" />' +
                '  </div>' +
                ' </div>' +
                '    <div class="col-md-6">' +
                ' <div class="form-group ">' +
                '  <label>Specialization<span class="error">*</span></label>' +
                '  <input type="text" required class="form-control" name="specialization[]" />' +
                '  </div>' +
                ' </div>' +
                '  </div>' +
                '  <div class="row">' +
                ' <div class="col-md-6">' +
                '<div class="form-group ">' +
                '<label>University/Collage<span class="error">*</span></label>' +
                '<input type="text" required name="institute[]" class="form-control" />' +
                '  </div>' +
                '</div>' +
                ' <div class="col-md-6">' +
                ' <div class="form-group ">' +
                '<label>Time Period<span class="error">*</span></label>' +
                '<input class="form-control degree_time_period" type="text" required readonly name="degree_time_period[]" value=""/>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '<div class="row">' +
                '<div class="col-md-6">' +
                '<div class="form-group ">' +
                '<label>CGPA/Percentage<span class="error">*</span></label>' +
                '<input type="text" required name="percentage[]" class="form-control" />' +
                '</div>' +
                '</div>' +
                '<div class="col-md-6">' +
                '<div class="form-group ">' +
                '<label>Upload Certificate(Only JPG, JPEG and PNG)</label>' +
                '<input type="file" accept="image/x-png, image/jpg, image/jpeg" data-accept="jpg,png,jpeg" name="degree_certificate[]" id="degree_certificate' + new_total_count + '" class="dropify" />' +
                '</div>' +
                '</div>' +
                '</div>' +
                '<hr class="m-t-0 m-b-40"></div>';
        $('#all_education_div').append(append_html);
        $('.dropify').dropify();

        $('.degree_time_period').daterangepicker({
            buttonClasses: ['btn', 'btn-sm'],
            applyClass: 'btn-danger',
            cancelClass: 'btn-inverse',
            format: "dd-mm-yyyy"
        });
        $('.exp_time_period').daterangepicker({
            buttonClasses: ['btn', 'btn-sm'],
            applyClass: 'btn-danger',
            cancelClass: 'btn-inverse',
            format: "dd-mm-yyyy"
        });

        setTimeout(function () {
            $('input[name="degree[]"]').each(function () {
                $(this).rules("add",
                        {
                            required: true,
                        });
            });
            $('input[name="specialization[]"]').each(function () {
                $(this).rules("add",
                        {
                            required: true,
                        });
            });
            $('input[name="institute[]"]').each(function () {
                $(this).rules("add",
                        {
                            required: true,
                        });
            });
            $('input[name="degree_time_period[]"]').each(function () {
                $(this).rules("add",
                        {
                            required: true,
                        });
            });
            $('input[name="percentage[]"]').each(function () {
                $(this).rules("add",
                        {
                            required: true,
                        });
            });
            $('input[name="degree_certificate[]"]').each(function () {
                $(this).rules("add",
                        {
                            accept: "jpg,png,jpeg"
                        });
            })
            jQuery("#add_user").validate().form();
        }, 2000);
    }

    function add_new_experience() {
        var total_count = parseInt($('#experience_div_count').val());
        var new_total_count = total_count + 1;

        var append_html = '<div id="experience_div' + new_total_count + '">' +
                '  <button onclick="remove_experience(' + new_total_count + ')" class="btn btn-danger pull-left" type="button"><i class="fa fa-trash"></i> Remove</button><br><br><br>' +
                ' <div class="row">' +
                '   <div class="col-md-6">' +
                '  <div class="form-group ">' +
                '     <label>Company Name</label>' +
                '     <input type="text" required name="exp_company_name[]" class="form-control" />' +
                '  </div>' +
                ' </div>' +
                '    <div class="col-md-6">' +
                ' <div class="form-group ">' +
                '  <label>Job Title</label>' +
                '  <input type="text" class="form-control" name="exp_job_title[]" />' +
                '  </div>' +
                ' </div>' +
                '  </div>' +
                '  <div class="row">' +
                ' <div class="col-md-6">' +
                '<div class="form-group ">' +
                '<label>Location</label>' +
                '<input type="text" name="exp_location[]" class="form-control" />' +
                '  </div>' +
                '</div>' +
                ' <div class="col-md-6">' +
                ' <div class="form-group ">' +
                '<label>Time Period</label>' +
                '<input class="form-control exp_time_period" readonly="" type="text" name="exp_time_period[]" value=""/>' +
                '</div>' +
                '</div>' +
                '</div>' +
                '<div class="row">' +
                '<div class="col-md-6">' +
                '<div class="form-group ">' +
                '<label>Description</label>' +
                '<textarea name="exp_description[]" class="form-control"></textarea>' +
                '</div>' +
                '</div>' +
                '<div class="col-md-6">' +
                '<div class="form-group ">' +
                '<label>Upload Document(Only JPG, JPEG and PNG)</label>' +
                '<input type="file" accept="image/x-png, image/jpg, image/jpeg, application/pdf" name="exp_document[]" id="exp_document' + new_total_count + '" class="dropify" />' +
                '</div>' +
                '</div>' +
                '</div>' +
                '<hr class="m-t-0 m-b-40"></div>';
        $('#all_experience_div').append(append_html);
        $('.dropify').dropify();

        $('.degree_time_period').daterangepicker({
            buttonClasses: ['btn', 'btn-sm'],
            applyClass: 'btn-danger',
            cancelClass: 'btn-inverse',
            format: "dd-mm-yyyy"
        });
        $('.exp_time_period').daterangepicker({
            buttonClasses: ['btn', 'btn-sm'],
            applyClass: 'btn-danger',
            cancelClass: 'btn-inverse',
            format: "dd-mm-yyyy"
        });
        setTimeout(function () {
            $('input[name="exp_document[]"]').each(function () {
                $(this).rules("add",
                        {
                            required: true,
                            accept: "jpg,png,jpeg,pdf"
                        });
            })
            jQuery("#add_user").validate().form();
        }, 2000);
    }
    function remove_education(id) {
        $('#education_div' + id).remove();
    }
    function remove_experience(id) {
        $('#experience_div' + id).remove();
    }
    $(document).ready(function () {
        $('.dropify').dropify();
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
        $('.degree_time_period').daterangepicker({
            buttonClasses: ['btn', 'btn-sm'],
            applyClass: 'btn-danger',
            cancelClass: 'btn-inverse',
            locale: {
                format: 'DD/MM/YYYY'
            }
        });
        $('.exp_time_period').daterangepicker({
            buttonClasses: ['btn', 'btn-sm'],
            applyClass: 'btn-danger',
            cancelClass: 'btn-inverse',
            locale: {
                format: 'DD/MM/YYYY'
            }
        });
        $('#is_same').change(function () {
            if ($(this).prop("checked") == true) {
                $('#permanent_address').val($('#residential_address').val());
            } else {
                $('#permanent_address').val('');
            }
        });
    });
</script>
@endsection