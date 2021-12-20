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
                <li><a href="{{ route($reck_module_link) }}">{{ $module_title }} Reck</a></li>
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
                            <form action="{{ route('admin.update_hardcopy_reck') }}" id="edit_softcopy_reck" method="post">
                            <input type="hidden" id="id" name="id" value="{{ $softcopy_reck_detail->id }}" />
                            @csrf
                            <div class="form-group">
                                <label>Company</label>
                                <select class="form-control select2" class="company_id" name="company_id" id="company_id">
                                    @foreach($company as $key => $value)
                                        <option value="{{ $key }}" <?php if($softcopy_reck_detail->company_id == $key){ ?> selected <?php } ?> > {{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Department <span class="error">*</span> </label>
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
                            <div class="form-group ">
                                <label>Rack Number <span class="error">*</span> </label>
                                <input type="text" class="form-control" name="reck_number" id="reck_number" value="{{ $softcopy_reck_detail->reck_number }}" />
                            </div>
                            <div class="form-group ">
                                <label>Description</label>
                                <textarea type="text" class="form-control" name="description" id="description">{{ $softcopy_reck_detail->description }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.hardcopy_reck') }}'" class="btn btn-default">Cancel</button>
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
        //$("#department_id").trigger('change');
    });

    var __dayDiff = 0;
    jQuery('#edit_softcopy_reck').validate({
        ignore: [],
        rules: {
            company_id: {
                required: true,
            },
            department_id: {
                required: true,
            },
            reck_number: {
                required: true,
                remote: {
                    url: '{{ route("admin.check_reck_number") }}',
                    type: "post",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        reck_number: function () {
                            return $('#reck_number').val();
                        },
                        cupboard_id: function () {
                            return $('#cupboard_id').val();
                        },
                        id: function () {
                            return $('#id').val();
                        }
                    }
                }
            }
        },
        messages: {
            reck_number: {
                remote: "Reck number already in use."
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
                $('#department_id option[value='+<?php echo $softcopy_reck_detail->department_id; ?>+']').attr('selected','selected');
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
                $('#cupboard_id option[value='+<?php echo $softcopy_reck_detail->document_softcopy_cupboard_id; ?>+']').attr('selected','selected');
                $('#cupboard_id').change();
            },
            error: function (jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    });

</script>
@endsection
