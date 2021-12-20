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
                        <form action="{{ route('admin.insert_education_document') }}" enctype="multipart/form-data" id="education_document" method="post">
                        <input type="hidden" name="id" id="id" value="{{ $id }}" />
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label>Degree<span class="error">*</span></label>
                                    <input type="text" name="degree" id="degree" class="form-control degree" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label>Specialization<span class="error">*</span></label>
                                    <input type="text" class="form-control" id="specialization" name="specialization" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label>University/Collage<span class="error">*</span></label>
                                    <input type="text" name="institute" id="institute" class="form-control" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label>Time Period<span class="error">*</span></label>
                                    <input class="form-control degree_time_period" readonly="" type="text" name="degree_time_period" id="degree_time_period" value=""/>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label>CGPA/Percentage<span class="error">*</span></label>
                                    <input type="text" name="percentage" id="percentage" class="form-control" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label>Upload Certificate(Only JPG, JPEG and PNG)</label>
                                    <input type="file" accept="image/x-png, image/jpg, image/jpeg" name="degree_certificate" id="degree_certificate" class="dropify" />
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
    jQuery("#education_document").validate({
        rules: {            
            degree: {
                required: true
            },
            specialization: {
                required: true
            },
            institute: {
                required: true
            },
            degree_time_period: {
                required: true
            },
            percentage: {
                required: true
            },
            degree_certificate: {
                accept: "jpg,png,jpeg"
            },            
        },
        messages: {            
        }
    });
    $('.degree_time_period').daterangepicker({
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