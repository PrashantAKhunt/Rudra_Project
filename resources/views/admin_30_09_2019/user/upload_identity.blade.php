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
                        <form action="{{ route('admin.insert_identity_document') }}" enctype="multipart/form-data" id="identity_document" method="post">
                        <input type="hidden" name="id" id="id" value="{{ $id }}" />
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group ">
                                    <label>Document Type<span class="error">*</span></label>
                                    <select class="form-control" name="document_type" id="document_type">
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
                                    <input type="file" id="identity_document" accept="image/x-png, image/jpg, image/jpeg" name="identity_document" class="dropify" />
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
    jQuery("#identity_document").validate({
        rules: {            
            document_type: {
                required: true
            },
            identity_document: {
                required: true,
                accept: "jpg,png,jpeg"
            },            
        },
        messages: {
            
        }
    });
    $('.dropify').dropify();
</script>
@endsection