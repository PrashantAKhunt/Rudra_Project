@extends('layouts.admin_app')

@section('content')
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">{{ $page_title }}</h4>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">

            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">a</a></li>
                <li><a href="{{ route('admin.boq_design') }}">BOQ Design</a></li>
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
                        <form action="{{ route('admin.insert_boq_design') }}" id="boq_form" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                    <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Select Company <span class="error">*</span> </label>
                                        <select onchange="get_project_by_company()" class="form-control company_id" id="company_id" name="company_id">
                                            <option value="">Select Company</option>
                                            @foreach($company_list as $company)
                                            <option value="{{ $company->id }}">{{ $company->company_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                    <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Select Project <span class="error">*</span> </label>
                                        <select class="form-control project_id" id="project_id" name="project_id">
                                            <option value="">Select Project</option>
                                            
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                    <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Item No <span class="error">*</span> </label>
                                        <select class="form-control" name="item_no" id="item_no">
                                            <option value="">Select Item</option>
                                            
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div id="add_more_part">
                                <div class="row boq_block" id="boq_block_0">
                                    <div class="col-md-4">
                                        <div class="form-group ">
                                            <label>Block Title <span class="error">*</span> </label>
                                            <input type="text" class="form-control block_title" name="block_title[]" id="block_title_0" value="" />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group ">
                                            <label>Block Detail <span class="error">*</span> </label>
                                            <textarea class="form-control block_detail" name="block_detail[]" id="block_detail_0"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group ">
                                            <label>Block Drawing (Upload only image) <span class="error">*</span> </label>
                                            <input type="file" class="form-control block_drawing" name="block_drawing[]" id="block_drawing_0" value="" />
                                        </div>
                                    </div>
                                </div>    
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                </div>
                                <div class="col-md-6 text-right">
                                    <button type="button" class="btn btn-primary" onclick="addMoreBlock()">Add More Block</button>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-success">Submit</button>
                                    <button type="button" onclick="window.location.href ='{{ route('admin.boq_design') }}'" class="btn btn-default">Cancel</button>
                                </div>
                            </div>
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
var block_counter = 0;

function get_project_by_company() {
    $.ajax({
        url: "{{ route('admin.get_projectlist_by_company') }}",
        type: "POST",
        dataType: "html",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            company_id: $('#company_id').val()
        },
        success: function (data) {
            $("#item_no").val("");
            $('#project_id').html(data);
        }
    });
}

$("#project_id").on('change',function(){
    var company_id = $("#company_id").val();
    var project_id = $(this).val();
    $.ajax({
        type : "POST",
        url : "{{url('get_itemno_block')}}",
        data : {
            '_token' : "{{csrf_token()}}",
            'company_id' : company_id,
            'project_id' : project_id
        },
        success : function(data){
            // console.log(data);
            $("#item_no").html(data);
        }
    });
});

function addMoreBlock(){
    block_counter += 1;
    $("#add_more_part").append('<div class="row boq_block" id="boq_block_'+block_counter+'">'+
                                    '<div class="col-md-4">'+
                                        '<div class="form-group ">'+
                                            '<label>Block Title <span class="error">*</span> </label>'+
                                            '<input type="text" class="form-control block_title" name="block_title['+block_counter+']" id="block_title_'+block_counter+'" value="" />'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="col-md-4">'+
                                        '<div class="form-group ">'+
                                            '<label>Block Detail <span class="error">*</span> </label>'+
                                            '<textarea class="form-control block_detail" name="block_detail['+block_counter+']" id="block_detail_'+block_counter+'"></textarea>'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="col-md-3">'+
                                        '<div class="form-group ">'+
                                            '<label>Block Drawing(Upload only image) <span class="error">*</span> </label>'+
                                            '<input type="file" class="form-control block_drawing" name="block_drawing['+block_counter+']" id="block_drawing_'+block_counter+'" value="" />'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="col-md-1" style="margin-top: 30px;">'+
                                        '<div class="form-group ">'+
                                            '<button type="button" class="btn btn-danger remove_block">Remove</button>'+
                                        '</div>'+
                                    '</div>'+
                                '</div>');    
}
$('body').on('click','.remove_block',function(){
    $(this).parents('.boq_block').remove();
});

$("#boq_form").on('submit', function(event){
    $('.block_title').each(function() {
        $(this).rules('add', {
            required: true,
        });
    });
    $('.block_detail').each(function() {
        $(this).rules('add', {
            required: true,
        });
    });
    $('.block_drawing').each(function() {
        $(this).rules('add', {
            required: true,
        });
    });
});
$("#boq_form").validate({
    rules : {
        item_no : "required",
        company_id : "required",
        project_id : "required",
    }
});

</script>
@endsection
