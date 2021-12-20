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
                    <div class="col-sm-6 col-xs-6">
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
                        <form action="{{ route('admin.insert_company') }}" id="add_company" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label>Company Name <strong> <span class="error">*</span></strong> </label>
                                <input type="text" class="form-control" name="company_name" id="company_name" value="" />
                            </div>
                            <div class="form-group">
                                <label>Company Short Name</label>
                                <input type="text" class="form-control" name="company_short_name" id="company_short_name" value="" />
                            </div>
                            <div class="form-group">
                                <label>MOA Document Upload (Only PDF)</label>
                                <input type="file" class="form-control" name="cmp_moa_image" id="cmp_moa_image" value="" accept="application/pdf" />
                            </div>
                            <div class="form-group">
                                <label>GST No <strong> <span class="error">*</span></strong> </label>
                                <input type="text" class="form-control" name="cmp_gst_no" id="cmp_gst_no" value="" />
                            </div>
                            <div class="form-group">
                                <label>GST Document (Only PDF)</label>
                                <input type="file" class="form-control" name="cmp_gst_image" id="cmp_gst_image" value="" accept="application/pdf" />
                            </div>
                            <div class="form-group">
                                <label>CIN No <strong> <span class="error">*</span></strong> </label>
                                <input type="text" class="form-control" name="cmp_cin_no" id="cmp_cin_no" value="" />
                            </div>
                            <div class="form-group">
                                <label>PAN No <strong> <span class="error">*</span></strong> </label>
                                <input type="text" class="form-control" name="cmp_pan_no" id="cmp_pan_no" value="" />
                            </div>
                            <div class="form-group">
                                <label>PAN Card document (Only PDF)</label>
                                <input type="file" class="form-control" name="cmp_pan_image" id="cmp_pan_image" value="" accept="application/pdf" />
                            </div>
                            <div class="form-group">
                                <label>TAN No <strong> <span class="error">*</span></strong> </label>
                                <input type="text" class="form-control" name="cmp_tan_no" id="cmp_tan_no" value="" />
                            </div>
                            <div class="form-group">
                                <label>TAN Card document (Only PDF)</label>
                                <input type="file" class="form-control" name="cmp_tan_image" id="cmp_tan_image" value="" accept="application/pdf" />
                            </div>
                            <div class="form-group">
                                <label>Certificate of Incorporation (PDF)</label>
                                <input type="file" class="form-control" name="coi_crtfcte_image[]" multiple id="coi_crtfcte_image" accept="application/pdf" value="" />
                            </div>
                            <div class="form-group">
                                <label>Company Detail <strong> <span class="error">*</span></strong> </label>
                                <textarea class="form-control" rows="10" name="detail" id="detail"></textarea>
                            </div>


                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.companies') }}'" class="btn btn-default">Cancel</button>
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
    jQuery("#add_company").validate({
        ignore: [],
        rules: {
            company_name: {
                required: true,
            },
            cmp_gst_no: {
                required: true,
            },
            cmp_tan_no: {
                required: true,
            },
            cmp_pan_no: {
                required: true,
            },
            cmp_cin_no: {
                required: true,
            },
            detail: {
                required: true,
            }
        }
    });
</script>
@endsection