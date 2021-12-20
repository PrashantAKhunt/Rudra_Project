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
                <li><a href="{{ route($module_link) }}">Location Of Document</a></li>
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
                                <div class="col-md-4">
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
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Department</label>
                                        <select class="form-control select2" class="department_id" name="department_id" id="department_id">
                                        <option value="">Select Department</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Cupboard</label>
                                        <select class="form-control select2" class="cupboard_id" name="cupboard_id" id="cupboard_id">
                                        <option value="">Select Cupboard</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Rack</label>
                                        <select class="form-control select2" class="document_softcopy_reck_id" name="document_softcopy_reck_id" id="document_softcopy_reck_id">
                                            <option value="">Select Rack</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>File</label>
                                        <select class="form-control select2" class="document_softcopy_folder_id" name="document_softcopy_folder_id" id="document_softcopy_folder_id">
                                            <option value="">Select File</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Type</label>
                                        <select class="form-control select2" class="type" name="type" id="type">
                                            <option value="General">General</option>
                                            <option value="Registry">Registry</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 in_out_ward hide">
                                    <div class="form-group">
                                        <label>Registry Inward No</label>
                                        <select class="form-control select2" class="inward_outward_id" name="inward_outward_id" id="inward_outward_id">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 file_box">
                                    <div class="form-group">
                                        <label>Upload Document</label>
                                        <input type="file" class="form-control" name="softcopy_file[]" class="softcopy_file" id="softcopy_file" accept="application/pdf" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                <div class="form-group ">
                                <label>Start Page Number TO End Page Number</label>
                                <div class="input-daterange input-group">
                                    <input type="text" class="form-control" readonly  name="start_page" id="start_page" />
                                    <label id="start_page-error" class="error errorMsq" for="start_page"></label>
                                    <span class="input-group-addon bg-info b-0 text-white">to</span>
                                    <input type="text" class="form-control" readonly name="end_page" id="end_page"/>
                                    <label id="end_page-error" class="error errorMsq" for="end_page"></label>
                                </div>
                            </div>
                            </div>
                            </div>
                            <div class="row">
                            <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Is Document Returnable</label>
                                        <select class="form-control select2" onclick="checkAns();" name="is_returnable" id="is_returnable">
                                        <option value="">-- Select --</option>
                                            <option value="Yes">Yes</option>
                                            <option value="No">No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Assign Employee</label>
                                        <select class="form-control select2" onclick="setVal();"  name="assignee_id" id="assignee_id">
                                        <option value="">Select Employee</option>
                                        @foreach($users_list as $key => $value)
                                            <option value="{{$key}}">{{$value}}</option>
                                        @endforeach

                                        </select>
                                    </div>
                                </div>
                            <div class="col-md-4">
                                    <div class="form-group" id="custodion_user">
                                        <label>Select Custodian</label>
                                        <select class="form-control select2" name="custodion_user_id" id="custodion_user_id">
                                        <option value="">Select Custodian</option>
                                        @foreach($users_list as $key => $value)
                                            <option value="{{$key}}">{{$value}}</option>
                                        @endforeach

                                        </select>
                                    </div>
                                </div>

                                </div>
                                <div class="row">

                                    <div class="col-md-3">
                                        <div class="form-group ">
                                            <label>Title</label>
                                            <input type="text" class="form-control" name="title" id="title" value="" />
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="form-group ">
                                            <label>Description</label>
                                            <textarea type="text" class="form-control" name="description" id="description"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                <div class="col-md-6">
                                <div class="form-group">
                                        <div class="errorTxt">
                                            <span id="errNm1"></span>
                                        </div>
                                        <div class="checkbox checkbox-success">
                                            <input id="checkbox33" name="is_check" type="checkbox" value="Pending">
                                            <label for="checkbox33">Mark AS Filled!</label>
                                        </div>
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
function checkAns() {
   var check =  $('#is_returnable').val();
   if (check == 'No') {
        $('#custodion_user').attr("style", "pointer-events: none;");
   }else{
        $('#custodion_user').attr("style", "pointer-events: auto ;");
   }

}

function setVal() {
    var user_id =  $('#assignee_id').val();
    var check =  $('#is_returnable').val();
    if (check == 'No') {
        $("#custodion_user_id").val(user_id).trigger('change');
   }

}
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
            cupboard_id: {
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
            start_page:{
                required: true
            },
            end_page:{
                required: true
            },
            custodion_user_id:{
                required: true
            },
            is_returnable:{
                required: true
            },
            assignee_id:{
                required: true
            },
            title:{
                required: true
            },
            is_check:{
                required: true
            },
            description:{
                required: true
            }
        },
        messages: {
            company_id: {
                required: "This field is required!",
            },
            department_id: {
                required: "This field is required!",
            },
            cupboard_id: {
                required: "This field is required!",
            },
            document_softcopy_reck_id: {
                required: "This field is required!",
            },
            document_softcopy_folder_id: {
                required: "This field is required!",
            },
            type:{
                required: "This field is required!"
            },
            start_page:{
                required: "This field is required!"
            },
            end_page:{
                required: "This field is required!"
            },
            custodion_user_id:{
                required: "This field is required!"
            },
            is_returnable:{
                required: "This field is required!"
            },
            assignee_id:{
                required: "This field is required!"
            },
            title:{
                required: "This field is required!"
            },
            is_check:{
                required: "This field is required!"
            },
            description:{
                required: "This field is required!"
            },
        },
        errorPlacement: function(label, element) {
                label.addClass('errorMsq');
                if (element.attr("name") == "is_check" ) {
                    $("#errNm1").append(label);
                } else {
                    element.parent().append(label);
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
            url: "{{ route('admin.get_hardcopy_cupboard')}}",
            type: 'get',
            data: "department_id=" + $(this).val(),
            success: function (data, textStatus, jQxhr) {
                $('#cupboard_id').html(data);
            },
            error: function (jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    });

    $("#cupboard_id").change(function () {
        $.ajax({
            url: "{{ route('admin.get_hardcopy_reck')}}",
            type: 'get',
            data: "cupboard_id=" + $(this).val(),
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
            $('#title').val('');
            $('#description').val('');
            $('.file_box').removeClass('hide');
            $('#softcopy_file').attr('required',true);
            $('.in_out_ward').addClass('hide');
            $('#inward_outward_id').attr('required',false);
        }
    });

    $("#inward_outward_id").change(function () {
        $.ajax({
            url: "{{ route('admin.get_pdf_page_no')}}",
            type: 'post',
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            data: {
                registry_id: $(this).val(),
                company_id: $('#company_id').val(),
                department_id: $('#department_id').val(),
                cupboard_id: $('#cupboard_id').val(),
                document_softcopy_reck_id: $('#document_softcopy_reck_id').val(),
                document_softcopy_folder_id: $('#document_softcopy_folder_id').val()
                },
            success: function (data, textStatus, jQxhr) {
                $('#start_page').val(data.start);
                $('#end_page').val(data.end);
            },
            error: function (jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
       //==============================================
       $.ajax({
            url: "{{ route('admin.get_inward_details')}}",
            type: 'post',
            dataType: "json",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: {registry_id: $(this).val()},
            success: function (data, textStatus, jQxhr) {
                $('#title').val(data[0].inward_outward_title);
                $('#description').val(data[0].description);
            },
            error: function (jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
        //==============================================
    });

</script>

<script>

$(document).ready(function() {
            $('input[type="file"]').change(function() {
                if(document.getElementById("softcopy_file") && document.getElementById("softcopy_file").value)
                    {
                        $.ajax({
                            url: "{{ route('admin.get_last_page_no')}}",
                            type: 'post',
                            dataType: "json",
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                            data: {
                                company_id: $('#company_id').val(),department_id: $('#department_id').val(),
                                cupboard_id: $('#cupboard_id').val(),document_softcopy_reck_id: $('#document_softcopy_reck_id').val(),
                                document_softcopy_folder_id: $('#document_softcopy_folder_id').val()
                                },
                            success: function (data, textStatus, jQxhr) {
                                var input = document.getElementById("softcopy_file");
                                var reader = new FileReader();
                                reader.readAsBinaryString(input.files[0]);
                                reader.onloadend = function(){
                                    var count = reader.result.match(/\/Type[\s]*\/Page[^s]/g).length;
                                    let startPage = Number(data.last_page_no) + 1;
                                    let endPage = startPage + count - 1;

                                        $('#start_page').val(startPage);
                                        $('#end_page').val(endPage);
                                }
                            },
                            error: function (jqXhr, textStatus, errorThrown) {
                                console.log(errorThrown);
                            }
                        });

                    } else {
                        $('#start_page').val('');
                        $('#end_page').val('');
                    }
            });
        });


</script>
@endsection
