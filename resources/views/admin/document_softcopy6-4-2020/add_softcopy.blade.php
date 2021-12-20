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
                        <form action="{{ route('admin.insert_hardcopy') }}" id="add_softcopy" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Company</label>
                                        <select class="form-control select2" class="company_id" name="company_id" id="company_id">
                                        <option value="">Select Company</option>
                                        @foreach($company as $key => $value)
                                            <option value="{{ $key }}"> {{ $value }}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Department</label>
                                        <select class="form-control select2" class="department_id" name="department_id" id="department_id">
                                        <option value="">Select Department</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">    
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Reck</label>
                                        <select class="form-control select2" class="document_softcopy_reck_id" name="document_softcopy_reck_id" id="document_softcopy_reck_id">
                                            <option value="">Select Reck</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Folder</label>
                                        <select class="form-control select2" class="document_softcopy_folder_id" name="document_softcopy_folder_id" id="document_softcopy_folder_id">
                                            <option value="">Select Folder</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Type</label>
                                        <select class="form-control select2" class="type" name="type" id="type">
                                            <option value="General">General</option>
                                            <option value="Registry">Registry</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 in_out_ward hide">
                                    <div class="form-group">
                                        <label>Inward-Outward</label>
                                        <select class="form-control select2" class="inward_outward_id" name="inward_outward_id" id="inward_outward_id">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 file_box">
                                    <div class="form-group">
                                        <label>Profile image</label>
                                        <input type="file" name="softcopy_file[]" class="softcopy_file" id="softcopy_file" multiple />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Title</label>
                                        <input type="text" class="form-control" name="title" id="title" value="" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Description</label>
                                        <textarea type="text" class="form-control" name="description" id="description"></textarea>
                                    </div>
                                </div>
                            </div>                            
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.hardcopy') }}'" class="btn btn-default">Cancel</button>
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
	var __dayDiff = 0;
    jQuery('#add_softcopy').validate({
        ignore: [],
        rules: {
            company_id: {
                required: true,
            },
            department_id: {
                required: true,
            },
            document_softcopy_reck_id: {
                required: true,
            },
            document_softcopy_folder_id: {
                required: true,
            },
            type:{
                required: true
            },
            title:{
                required: true
            },
        }
    });
    $('.select2').select2();

    $("#company_id").change(function () {
        $.ajax({
            url: "{{ route('admin.get_department')}}",
            type: 'get',
            data: "company_id=" + $(this).val(),
            success: function (data, textStatus, jQxhr) {
                $('#department_id').html(data);
            },
            error: function (jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    });
    $("#department_id").change(function () {
        $.ajax({
            url: "{{ route('admin.get_hardcopy_reck')}}",
            type: 'get',
            data: "department_id=" + $(this).val(),
            success: function (data, textStatus, jQxhr) {                
                $('#document_softcopy_reck_id').html(data);
            },
            error: function (jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    });
    $("#document_softcopy_reck_id").change(function () {
        $.ajax({
            url: "{{ route('admin.get_hardcopy_folder')}}",
            type: 'get',
            data: "document_softcopy_reck_id=" + $(this).val(),
            success: function (data, textStatus, jQxhr) {
                $('#document_softcopy_folder_id').html(data);
            },
            error: function (jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    });

    $("#type").change(function () {
        if($(this).val() == 'Registry'){
            $('.in_out_ward').removeClass('hide');
            $('#inward_outward_id').attr('required',true);            
            $('.file_box').addClass('hide');
            $('#softcopy_file').attr('required',false);
            $.ajax({
                url: "{{ route('admin.get_inward_outward')}}",
                type: 'get',
                data: "company_id=" + $('#company_id').val(),
                success: function (data, textStatus, jQxhr) {
                    $('#inward_outward_id').html(data);
                },
                error: function (jqXhr, textStatus, errorThrown) {
                    console.log(errorThrown);
                }
            });
        }else{
            $('.file_box').removeClass('hide');
            $('#softcopy_file').attr('required',true);
            $('.in_out_ward').addClass('hide');
            $('#inward_outward_id').attr('required',false);
        }
    });

</script>
@endsection