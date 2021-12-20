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
                <li><a href="{{ route($folder_module_link) }}">{{ $module_title }} Folder</a></li>
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
                        <form action="{{ route('admin.insert_hardcopy_folder') }}" id="add_softcopy_folder" method="post">
                            @csrf
                            <div class="form-group">
                                <label>Company</label>
                                <select class="form-control select2" class="company_id" name="company_id" id="company_id">
                                <option value="">Select Company</option>
                                @foreach($company as $key => $value)
                                    <option value="{{ $key }}"> {{ $value }}</option>
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
                                <label>Reck</label>
                                <select class="form-control select2" class="document_softcopy_reck_id" name="document_softcopy_reck_id" id="document_softcopy_reck_id">
                                    <option value="">Select Reck</option>
                                </select>
                            </div>
                            <div class="form-group ">
                                <label>File Number</label>
                                <input type="text" class="form-control" name="file_number" id="file_number" value="" />
                            </div>
                            <div class="form-group ">
                                <label>Description</label>
                                <textarea type="text" class="form-control" name="description" id="description"></textarea>
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
	var __dayDiff = 0;
    jQuery('#add_softcopy_folder').validate({
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
</script>
@endsection