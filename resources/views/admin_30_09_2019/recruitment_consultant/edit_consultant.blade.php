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
                        <form action="{{ route('admin.update_consultant') }}" id="add_consultant" method="post">
                            @csrf
                            <input type="hidden" name="id" id="id" value="{{ $consultant_detail[0]->id }}" />
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Name<span class="error">*</span></label>
                                        <input type="text" class="form-control" name="name" id="name" value="{{ $consultant_detail[0]->name }}" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Email<span class="error">*</span></label>
                                        <input type="text" class="form-control" name="email" id="email" value="{{ $consultant_detail[0]->email }}" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Address<span class="error">*</span></label>
                                        <textarea class="form-control" name="address" id="address">{{ $consultant_detail[0]->address }}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Specialty<span class="error">*</span></label>
                                        <input type="text" class="form-control" name="specialty" id="specialty" value="{{ $consultant_detail[0]->specialty }}" />
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.recruitment_consultant') }}'" class="btn btn-default">Cancel</button>
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
    jQuery("#add_consultant").validate({
        rules: {
            
            name: {
                required: true
            },
            email: {
                required: true
            },
            address: {
                required: true
            },
            specialty: {
                required: true
            },
            
        },
        
    });
    
</script>
@endsection
