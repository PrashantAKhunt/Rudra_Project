@extends('layouts.admin_app')

@section('content')
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">Add letter-head</h4>
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
                    <div class="col-sm-12 col-xs-12">
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
                        <form action="{{ route('admin.insert_pro_sign_letter') }}" id="insert_pro_sign_letter" method="post" enctype="multipart/form-data">
                            @csrf
                            <!--<input type="hidden" name="company_id" id="company_id" value="<?php //echo $company_id;?>">-->
                            <div class="form-group ">
                                <label>Select Company</label>
                                <select class="form-control" id="company_id" name="company_id">
                                    <option value="">Select Company</option>
                                    @foreach($company_list as $company)
                                    <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group ">
                                <label>Select Project</label>
                                <select class="form-control" id="project_id" name="project_id">
                                    <option value="">Select Project</option>
                                </select>
                            </div>
                            <div class="form-group" id="other_cash_txt" style="display:none;"> 
                                <label>Other Detail</label>
                                <input type="text" class="form-control" name="other_project_detail" id="other_project_detail"/> 
                            </div>
                            <div class="form-group ">
                                <label>Vendor Name</label>
                                <div id="vendor_response">
                                    <select class="form-control" id='vendor_id' name='vendor_id'>
                                        <option value="">Select vendor</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group "> 
                                <label>Letter Title</label> 
                                <input type="text" maxlength="250" class="form-control" name="title" id="title"/>
                            </div>
                            <div class="form-group ">
                                <label>Letter Content</label>
                                <textarea class="form-control valid" rows="10" name="note" id="note" spellcheck="false"></textarea>
                            </div>
                            <div class="form-group">
                                <label>Letter-head Content File</label>
                                <input type="file" class="form-control" name="letter_head_content_file" id="letter_head_content_file" accept="application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" />
                            </div>
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.pro_sign_letter') }}'" class="btn btn-default">Cancel</button>
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
    jQuery('#insert_pro_sign_letter').validate({
        ignore: [],
        rules: {
            title: {
                required: true,
            },
            note: {
                required: true,
            },
            project_id: {
                required: true,
            },
            letter_head_content_file:{
                required:true
            }
        },
        errorPlacement: function (error, element) {

            if (element.attr("id") == "note") {
                error.insertAfter($(element).parent());
            } else {
                error.insertAfter($(element));
            }
        },
        submitHandler: function (form) {
            // Prevent double submission
            if (!this.beenSubmitted) {
                this.beenSubmitted = true;
                form.submit();
            }
        }
    });
	$(document).ready(function(){
		$('#note').wysihtml5();
        
        $('#company_id').change(function () {
            var company_id = $("#company_id").val();
            $.ajax({
            url: "{{ route('admin.get_cash_project_list')}}",
            type: 'get',
            data: "company_id=" + company_id,
            success: function (data, textStatus, jQxhr) {
                $('#project_id').empty();
                $('#project_id').append(data);
            },
            error: function (jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
        $.ajax({
            url: "{{ route('admin.get_cash_vendor_list')}}",
            type: 'get',
            data: "company_id=" + company_id,
            success: function (data, textStatus, jQxhr) {
                $('#vendor_id').empty();
                $('#vendor_id').append(data);
            },
            error: function (jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
        });
        

        /*$.ajax({
            url: "{{ route('admin.get_bank_list_cheque')}}",
            type: 'get',
            data: "company_id=" + company_id,
            success: function (data, textStatus, jQxhr) {
                $('#bank_id').append(data);
            },
            error: function (jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });*/


        

        $("#project_id").change(function () {
            var project_id = $("#project_id").val();
            if (project_id == 1) {
                $("#other_project_detail").val("");
                $("#other_cash_txt").show();
            } else {
                $("#other_cash_txt").hide();
            }
        }); 
	});
	
</script>
@endsection
