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
                        <form action="{{ route('admin.insert_experience_document') }}" enctype="multipart/form-data" id="experience_document" method="post">
                        <input type="hidden" name="id" id="id" value="{{ $id }}" />
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label>Company Name<span class="error">*</span></label>
                                    <input type="text" name="exp_company_name" id="exp_company_name" class="form-control" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label>Job Title<span class="error">*</span></label>
                                    <input type="text" class="form-control" id="exp_job_title" name="exp_job_title" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label>Job Location<span class="error">*</span></label>
                                    <input type="text" name="exp_location" id="exp_location" class="form-control" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label>Time Period<span class="error">*</span></label>
                                    <input class="form-control exp_time_period" readonly="" type="text" name="exp_time_period" id="exp_time_period" value=""/>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label>Description</label>
                                    <textarea name="exp_description" class="form-control"></textarea>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label>Upload Document(Only JPG, JPEG and PNG)</label>
                                    <input type="file" accept="image/x-png, image/jpg, image/jpeg" name="exp_document" id="exp_document" class="dropify" />
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success">Submit</button>
                        <button type="button" onclick="window.location.href ='{{ route('admin.view_user', $id) }}'" class="btn btn-default">Cancel</button>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    jQuery("#experience_document").validate({
        rules: {            
            exp_company_name: {
                required: true
            },
            exp_job_title: {
                required: true
            },
            exp_location: {
                required: true
            },
            exp_time_period: {
                required: true
            },
            exp_description: {
                required: true
            },
            exp_document: {
                accept: "jpg,png,jpeg"
            },            
        },
        messages: {            
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
    $('.dropify').dropify();
</script>
@endsection