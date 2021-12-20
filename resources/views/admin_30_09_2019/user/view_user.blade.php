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
                <li><a href="#">{{ $page_title }}</a></li>
            </ol>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-md-12 col-lg-12 col-sm-12">
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
            <div class="row">
                <div class="col-sm-10"></div>
                <div class="col-sm-2">
                    <?php if($is_profile){ ?> 
                        <a href="{{ route('admin.edit_profile') }}" class="btn btn-primary pull-right"><i class="fa fa-edit"></i> Edit Profile</a>
                    <?php }else{ ?>
                        <a href="{{ route('admin.edit_user', ['id' => $id]) }}" class="btn btn-primary pull-right"><i class="fa fa-edit"></i> Edit Employee</a>
                    <?php } ?>
                </div>
            </div>
            </br>
            <div class="white-box">
                <ul class="nav customtab nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#basic_detail" aria-controls="basic_detail" role="tab" data-toggle="tab" aria-expanded="true"><span class="visible-xs"><i class="ti-user"></i></span><span class="hidden-xs"> BASIC DETAILS</span></a></li>
                    <li role="presentation" class=""><a href="#contact_detail" aria-controls="contact_detail" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs"><i class="ti-email"></i></span> <span class="hidden-xs">CONTACT DETAILS</span></a></li>
                    <li role="presentation" class=""><a href="#reference_detail" aria-controls="reference_detail" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs"><i class="ti-receipt"></i></span> <span class="hidden-xs">REFERENCE DETAILS</span></a></li>
                    <li role="presentation" class=""><a href="#edu_detail" aria-controls="edu_detail" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs"><i class="ti-book"></i></span> <span class="hidden-xs">EDUCATION DETAILS</span></a></li>
                    <li role="presentation" class=""><a href="#exp_detail" aria-controls="exp_detail" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs"><i class="ti-briefcase"></i></span> <span class="hidden-xs">EXPERIENCE DETAILS</span></a></li>
                    <li role="presentation" class=""><a href="#identity_detail" aria-controls="identity_detail" role="tab" data-toggle="tab" aria-expanded="false"><span class="visible-xs"><i class="ti-id-badge"></i></span> <span class="hidden-xs">IDENTITY DOCUMENT</span></a></li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane fade active in" id="basic_detail">
                        <div class="row">
                            <div class="col-md-3 col-xs-6 b-r"> <strong>Employee Code</strong> <br>
                                <p class="text-muted">{{ $user_detail[0]->emp_code }}</p>
                            </div>
                            <div class="col-md-3 col-xs-6 b-r"> <strong>Full Name</strong> <br>
                                <p class="text-muted">{{ $user_detail[0]->name }}</p>
                            </div>
                            <div class="col-md-3 col-xs-6 b-r"> <strong>Email</strong> <br>
                                <p class="text-muted">{{ $user_detail[0]->email }}</p>
                            </div>
                            <div class="col-md-3 col-xs-6"> <strong>Company</strong> <br>
                                <p class="text-muted">{{ $user_detail[0]->company_name }}</p>
                            </div>
                            
                        </div>
                        <br>
                        <hr class="m-t-0">
                        <div class="row">
                            <div class="col-md-3 col-xs-6"> <strong>Department</strong> <br>
                                <p class="text-muted">{{ $user_detail[0]->dept_name }}</p>
                            </div>
                            <div class="col-md-3 col-xs-6 b-r"> <strong>Designation</strong> <br>
                                <p class="text-muted">{{ $user_detail[0]->designation }}</p>
                            </div>
                            
                            <div class="col-md-3 col-xs-6 b-r"> <strong>Gender</strong> <br>
                                <p class="text-muted">{{ $user_detail[0]->gender }}</p>
                            </div>
                            
                            <div class="col-md-3 col-xs-6 b-r"> <strong>Birth Date</strong> <br>
                                <p class="text-muted">{{ date('d-m-Y',strtotime($user_detail[0]->birth_date)) }}</p>
                            </div>
                        </div>
                        <br>
                        <hr class="m-t-0">
                        <div class="row">
                            <div class="col-md-3 col-xs-6"> <strong>Marital Status</strong> <br>
                                <p class="text-muted">{{ $user_detail[0]->marital_status }}</p>
                            </div>
                            <div class="col-md-3 col-xs-6 b-r"> <strong>Marriage Date</strong> <br>
                                <p class="text-muted">{{ date('d-m-Y',strtotime($user_detail[0]->marriage_date)) }}</p>
                            </div>
                            
                            <div class="col-md-3 col-xs-6 b-r"> <strong>Blood Group</strong> <br>
                                <p class="text-muted">{{ $user_detail[0]->blood_group }}</p>
                            </div>
                            <div class="col-md-3 col-xs-6"> <strong>Note if physically handicapped</strong> <br>
                                <p class="text-muted">
                                    @if($user_detail[0]->handicap_note)
                                    {{ $user_detail[0]->handicap_note }}
                                    @else
                                    NA
                                    @endif
                                </p>
                            </div>                            
                        </div>
						<hr class="m-t-0">
						<div class="row">
                            <div class="col-md-3 col-xs-6 b-r"> <strong>Physically Handicapped</strong> <br>
                                <p class="text-muted">{{ $user_detail[0]->physically_handicapped }}</p>
                            </div>
                            <div class="col-md-3 col-xs-6 b-r"> <strong>Joining Date</strong> <br>
                                <p class="text-muted">{{ date('d-m-Y',strtotime($user_detail[0]->joining_date)) }}</p>
                            </div>
                            <div class="col-md-3 col-xs-6 b-r"> <strong>Reporting User</strong> <br>
                                <p class="text-muted">{{ !empty($user_detail[0]->report_user_name)?$user_detail[0]->report_user_name:"--" }}</p>
                            </div>
                        </div>
                    </div>
										
                    <div role="tabpanel" class="tab-pane fade" id="contact_detail">
                        <div class="row">
                            <div class="col-md-4 col-xs-4 b-r"> <strong>Contact Number</strong> <br>
                                <p class="text-muted">{{ $user_detail[0]->contact_number }}</p>
                            </div>                            
                            <div class="col-md-4 col-xs-4 b-r"> <strong>Emergency Contact Number</strong> <br>
                                <p class="text-muted">{{ $user_detail[0]->emg_contact_number }}</p>
                            </div>                            
                            <div class="col-md-4 col-xs-4 b-r"> <strong>Skype</strong> <br>
                                <p class="text-muted">{{ $user_detail[0]->skype }}</p>
                            </div>                            
                        </div>
                        <br>
						<hr class="m-t-0">
						<div class="row">
                            <div class="col-md-4 col-xs-4 b-r"> <strong>Residential Address</strong> <br>
                                <p class="text-muted">{{ $user_detail[0]->residential_address }}</p>
                            </div>                            
                            <div class="col-md-4 col-xs-4 b-r"> <strong>Permanent Address</strong> <br>
                                <p class="text-muted">{{ $user_detail[0]->permanent_address }}</p>
                            </div>
                        </div>
                        <br>
                    </div>
										
                    <div role="tabpanel" class="tab-pane fade" id="reference_detail">
                        <div class="row">
                            <div class="col-md-6 col-xs-6 b-r"> <strong>Reference Name</strong> <br>
                                <p class="text-muted">{{ $user_detail[0]->ref_name1 }}</p>
                            </div>
                            <div class="col-md-6 col-xs-6 b-r"> <strong>Reference Contact Number</strong> <br>
                                <p class="text-muted">{{ $user_detail[0]->ref_contact1 }}</p>
                            </div>
                        </div>
                        <br>
						<hr class="m-t-0">
						<div class="row">
                            <div class="col-md-6 col-xs-6 b-r"> <strong>Reference Name</strong> <br>
                                <p class="text-muted">{{ $user_detail[0]->ref_name2 }}</p>
                            </div>
                            <div class="col-md-6 col-xs-6 b-r"> <strong>Reference Contact Number</strong> <br>
                                <p class="text-muted">{{ $user_detail[0]->ref_contact2 }}</p>
                            </div>
                        </div>
                        <br>
                    </div>
                    
                    <div role="tabpanel" class="tab-pane fade" id="edu_detail">
                        <div class="row">
                            <div class="col-sm-10"></div>
                            <div class="col-sm-2">
                                <div class="white-box">
                                    <?php if($is_profile){ ?> 
                                        <a href="{{ route('admin.education_document') }}" class="btn btn-primary pull-right"><i class="fa fa-cloud-upload"></i> Upload document</a>
                                    <?php }else{ ?>
                                        <a href="{{ route('admin.upload_education', ['id' => $id]) }}" class="btn btn-primary pull-right"><i class="fa fa-cloud-upload"></i> Upload document</a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>                        
                        <?php foreach($education_detail as $key => $value ) { ?>
                            <hr class="m-t-0">
                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="white-box">
                                        <div id="image-popups" class="row">
                                            <div class="col-sm-12">
                                                <a class="image-popup-no-margins" href="{{ asset('storage/'.str_replace('public/','',!empty($value->degree_certificate) ? $value->degree_certificate : 'public/no_image')) }}" data-effect="mfp-zoom-in"><img src="{{ asset('storage/'.str_replace('public/','',!empty($value->degree_certificate) ? $value->degree_certificate : 'public/no_image')) }}" class="img-responsive" /></a>
                                            </div>                                            
                                        </div>
                                        </br>
                                        <div class="text-center"><a title="Download image" href="{{ asset('storage/'.str_replace('public/','',!empty($value->degree_certificate) ? $value->degree_certificate : 'public/no_image')) }}" download ><i class="fa fa-cloud-download  fa-lg"></i></a></div>
                                    </div>                                    
                                </div>
                                <div class="col-sm-2">
                                    <div class="white-box">
                                        <h3 class="box-title m-b-0">Degree</h3>
                                        <small>{{ $value->degree }}</small>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="white-box">
                                        <h3 class="box-title m-b-0">Specialization</h3>
                                        <small>{{ $value->specialization }}</small>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="white-box">
                                        <h3 class="box-title m-b-0">Institute</h3>
                                        <small>{{ $value->institute }}</small>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="white-box">
                                        <h3 class="box-title m-b-0">CGPA/Percentage</h3>
                                        <small>{{ $value->percentage }}</small>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="white-box">
                                        <h3 class="box-title m-b-0">Acedemic</h3>
                                        <small>{{ date('d-m-Y',strtotime($value->degree_start_time_period)) }} </br> To </br> {{ date('d-m-Y',strtotime($value->degree_end_time_period)) }}</small>
                                    </div>
                                </div>              
                            </div>                            
                        <?php } ?>
                    </div>

                    <div role="tabpanel" class="tab-pane fade" id="exp_detail">
                        <div class="row">
                            <div class="col-sm-10"></div>
                            <div class="col-sm-2">
                                <div class="white-box">
                                    <?php if($is_profile){ ?> 
                                        <a href="{{ route('admin.experience_document') }}" class="btn btn-primary pull-right"><i class="fa fa-cloud-upload"></i> Upload document</a>
                                    <?php }else{ ?>
                                        <a href="{{ route('admin.upload_experience', ['id' => $id]) }}" class="btn btn-primary pull-right"><i class="fa fa-cloud-upload"></i> Upload document</a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>                        
                        <?php foreach($experience_detail as $key => $value ) { ?>
                            <hr class="m-t-0">
                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="white-box">
                                        <div id="image-popups" class="row">
                                            <div class="col-sm-12">
                                                <a class="image-popup-no-margins" href="{{ asset('storage/'.str_replace('public/','',!empty($value->exp_document) ? $value->exp_document : 'public/no_image')) }}" data-effect="mfp-zoom-in"><img src="{{ asset('storage/'.str_replace('public/','',!empty($value->exp_document) ? $value->exp_document : 'public/no_image')) }}" class="img-responsive" /></a>
                                            </div>
                                        </div>
                                        </br>
                                        <div class="text-center"><a title="Download image" href="{{ asset('storage/'.str_replace('public/','',!empty($value->exp_document) ? $value->exp_document : 'public/no_image')) }}" download ><i class="fa fa-cloud-download  fa-lg"></i></a></div>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="white-box">
                                        <h3 class="box-title m-b-0">Company</h3>
                                        <small>{{ $value->exp_company_name }}</small>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="white-box">
                                        <h3 class="box-title m-b-0">Job Title</h3>
                                        <small>{{ $value->exp_job_title }}</small>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="white-box">
                                        <h3 class="box-title m-b-0">Location</h3>
                                        <small>{{ $value->exp_location }}</small>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="white-box">
                                        <h3 class="box-title m-b-0">Description</h3>
                                        <small>{{ $value->exp_description }}</small>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="white-box">
                                        <h3 class="box-title m-b-0">Duration</h3>
                                        <small>{{ date('d-m-Y',strtotime($value->exp_start_time_period)) }} </br> To </br> {{ date('d-m-Y',strtotime($value->exp_end_time_period)) }}</small>
                                    </div>
                                </div>              
                            </div>                            
                        <?php } ?>
                    </div>

                    <div role="tabpanel" class="tab-pane fade" id="identity_detail">
                        <div class="row">
                            <div class="col-sm-10"></div>
                            <div class="col-sm-2">
                                <div class="white-box">
                                    <?php if($is_profile){ ?> 
                                        <a href="{{ route('admin.identity_document') }}" class="btn btn-primary pull-right"><i class="fa fa-cloud-upload"></i> Upload document</a>
                                    <?php }else{ ?>
                                        <a href="{{ route('admin.upload_identity', ['id' => $id]) }}" class="btn btn-primary pull-right"><i class="fa fa-cloud-upload"></i> Upload document</a>
                                    <?php } ?>                                    
                                </div>
                            </div>
                        </div>                        
                        <?php foreach($user_document as $key => $value ) { ?>
                            <hr class="m-t-0">
                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="white-box">
                                        <div id="image-popups" class="row">
                                            <div class="col-sm-12">
                                                <a class="image-popup-no-margins" href="{{ asset('storage/'.str_replace('public/','',!empty($value->identity_document) ? $value->identity_document : 'public/no_image')) }}" data-effect="mfp-zoom-in"><img src="{{ asset('storage/'.str_replace('public/','',!empty($value->identity_document) ? $value->identity_document : 'public/no_image')) }}" class="img-responsive" /></a>
                                            </div>
                                        </div>
                                        </br>
                                        <div class="text-center"><a title="Download image" href="{{ asset('storage/'.str_replace('public/','',!empty($value->identity_document) ? $value->identity_document : 'public/no_image')) }}" download ><i class="fa fa-cloud-download  fa-lg"></i></a></div>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="white-box">
                                        <h3 class="box-title m-b-0">Document Type</h3>
                                        <small>{{ $value->document_type }}</small>
                                    </div>
                                </div>                                            
                            </div>                            
                        <?php } ?>
                    </div>
                </div>
            </div>            

        </div>
@endsection
@section('script')
@endsection