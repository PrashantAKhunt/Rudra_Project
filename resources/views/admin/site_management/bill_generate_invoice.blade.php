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
                {{-- <li><a href="{{ route($module_link) }}">{{ $module_title }}</a></li> --}}
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
                        <form action="{{ route('admin.get_bill_invoice') }}" id="boq_bill_create" method="post">
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
                                        <label>Bill Number <span class="error">*</span> </label>
                                        <select class="form-control bill_number" id="bill_number" name="bill_number">
                                            <option value="">Select Bill Number</option>
                                            
                                        </select>
                                        <input type="hidden" name="bill_number_unique" id="bill_number_unique">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-success">Generate</button>
                                    <button type="button" onclick="window.location.href ='{{ route('admin.site_report') }}'" class="btn btn-default">Cancel</button>
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
$('#boq_bill_create').validate({
    rules: {
        company_id: {
            required: true
        },
        project_id: {
            required: true
        },
        bill_number: {
            required: true
        }
    }
})
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
            $('#project_id').html(data);
        }
    });
}

$("#project_id").on('change',function(){
    var project_id = $(this).val();
    var company_id = $("#company_id").val();
    $.ajax({
        type : "POST",
        url : "{{url('get_boq_bill_number')}}",
        data : {
            company_id : company_id,
            project_id : project_id,
            "_token" : "{{csrf_token()}}",
        },
        dataType : "json",
        success : function(data){
            console.log(data);
            if(data.length){
                $("#bill_number").html('');
                $("#bill_number").prepend('<option value="">Select Bill Number</option>');
                $.each(data,function(key,value){
                    $("#bill_number").append('<option value="'+value.unique_bill_number+'">'+value.unique_bill_number+'</option>');    
                });
            }else{
                $("#bill_number").append('<option value="">Not Found Bill Number</option>');
            }
        }
    });
});

$("#bill_number").on('change',function(){
    $("#bill_number_unique").val($("#bill_number option:selected").html());
});
</script>
@endsection
