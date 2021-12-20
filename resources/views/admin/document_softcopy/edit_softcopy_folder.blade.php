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
                <li><a href="{{ route($folder_module_link) }}">{{ $module_title }} File</a></li>
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
                            <form action="{{ route('admin.update_hardcopy_folder') }}" id="edit_softcopy_folder" method="post">
                            <input type="hidden" id="id" name="id" value="{{ $softcopy_folder_detail->id }}" />
                            @csrf
                            <div class="form-group">
                                <label>Company</label>
                                <select class="form-control select2" class="company_id" name="company_id" id="company_id">
                                    @foreach($company as $key => $value)
                                        <option value="{{ $key }}" <?php if($softcopy_folder_detail->company_id == $key){ ?> selected <?php } ?> > {{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Department</label>
                                <select class="form-control select2" class="department_id" name="department_id" id="department_id">
                                    <option value="">Select Department</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Cupboard</label>
                                <select class="form-control select2" class="cupboard_id" name="cupboard_id" id="cupboard_id">
                                    <option value="">Select Cupboard</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Rack</label>
                                <select class="form-control select2" class="document_softcopy_reck_id" name="document_softcopy_reck_id" id="document_softcopy_reck_id">
                                    <option value="">Select Rack</option>
                                </select>
                            </div>
                            <div class="form-group ">
                                <label>File Number</label>
                                <input type="text" class="form-control" name="file_number" id="file_number" value="{{ $softcopy_folder_detail->file_number }}" />
                            </div>
                            <div class="form-group ">
                                <label>File Name</label>
                                <input type="text" class="form-control" name="file_name" id="file_name" value="{{ $softcopy_folder_detail->file_name }}" />
                            </div>
                            <div class="form-group ">
                                <label>Description</label>
                                <textarea type="text" class="form-control" name="description" id="description">{{ $softcopy_folder_detail->description }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.hardcopy_folder') }}'" class="btn btn-default">Cancel</button>
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

    $(document).ready(function(){
        $("#company_id").trigger('change');
        $("#department_id").trigger('change');
    });

    var __dayDiff = 0;
    jQuery('#edit_softcopy_folder').validate({
        ignore: [],
        rules: {
            company_id: {
                required: true,
            },
            department_id: {
                required: true,
            },
            cupboard_id: {
                required: true,
            },
            document_softcopy_reck_id: {
                required: true,
            },
            file_name: {
                required: true,
            },
            file_number: {
                required: true,
                remote: {
                    url: '{{ route("admin.check_folder_number") }}',
                    type: "post",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        file_number: function () {
                            return $('#file_number').val();
                        },
                        document_softcopy_reck_id: function () {
                            return $('#document_softcopy_reck_id').val();
                        },
                        id: function () {
                            return $('#id').val();
                        }
                    }
                }
            }
        },
        messages: {
            file_number: {
                remote: "File number already in use."
            }
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
                $('#department_id option[value='+<?php echo $softcopy_folder_detail->department_id; ?>+']').attr('selected','selected');
                $('#department_id').change();
            },
            error: function (jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    });


    //=============================================
    $("#department_id").change(function () {
        $.ajax({
            url: "{{ route('admin.get_hardcopy_cupboard')}}",
            type: 'get',
            data: "department_id=" + $(this).val(),
            success: function (data, textStatus, jQxhr) {                
                $('#cupboard_id').html(data);
                $('#cupboard_id option[value='+<?php echo $softcopy_folder_detail->document_softcopy_cupboard_id; ?>+']').attr('selected','selected');
                $('#cupboard_id').change();
            },
            error: function (jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    });
    //=================================================
    $("#cupboard_id").change(function () {        
        $.ajax({
            url: "{{ route('admin.get_hardcopy_reck')}}",
            type: 'get',
            data: "cupboard_id=" + $(this).val(),
            success: function (data, textStatus, jQxhr) {                
                $('#document_softcopy_reck_id').html(data);                
                $('#document_softcopy_reck_id option[value='+<?php echo $softcopy_folder_detail->document_softcopy_reck_id; ?>+']').attr('selected','selected');
                $('#document_softcopy_reck_id').change();
            },
            error: function (jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    });
</script>
@endsection
