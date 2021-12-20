@extends('layouts.admin_app')
@section('content')
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">Edit Company Document</h4>
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
                        <form action="{{ route('admin.update_company_document') }}" id="update_company_document" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" value="{{$company_document_list[0]->id}}">
                            <div class="form-group "> 
                                <label>Select Company</label>
                                <select class="form-control" name="company_id" id="company_id">
                                    <option value="">Select Company</option>
                                    @foreach($Companies as $company_list_data)
                                    <option value="{{ $company_list_data->id }}" <?php echo ($company_document_list[0]->company_id == $company_list_data->id) ? "selected='selected'" : '' ?> >{{ $company_list_data->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group ">
                                <label>Select Project</label>
                                <select class="form-control" id="project_id" name="project_id">
                                    <option value="">Select Project</option>
                                     @foreach($Projects as $project_list_data)
                                    <option value="{{ $project_list_data->id }}" <?php echo ($company_document_list[0]->project_id == $project_list_data->id) ? "selected='selected'" : '' ?> >{{ $project_list_data->project_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group ">
                                <label>Document Title</label>
                                 <input type="hidden" class="form-control" name="document_type" id="document_type" value=<?php echo $company_document_list[0]->document_type;?> />
                                <input type="text" class="form-control" name="document_title" id="document_title" value="{{$company_document_list[0]->doc_title}}" />
                            </div>      
                            <div class="form-group ">
                                <label>Document Detail</label>
                                <textarea class="form-control valid" rows="6" name="document_detail" id="document_detail" value="{{$company_document_list[0]->doc_detail}}" spellcheck="false">{{$company_document_list[0]->doc_detail}}</textarea>
                            </div>
                            <div class="form-group ">
                                <label>Documet File</label>                                 
                                <input type="file" class="form-control" name="company_document_file" id="company_document_file"/>
                                <!-- <input type="hidden" name="file_counts[]" id="file_counts" value="0" /> -->
                            </div>                              
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.company_document_list') }}'" class="btn btn-default">Cancel</button>
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
    jQuery('#update_company_document').validate({
        ignore: [],
        rules: {
            company_id: {
                required: true,
            },        
            project_id:{
                required: true,
            },     
            document_type: {
                required: true
            },
            document_detail: {
                required: true
            },
            document_title: {
                required: true
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
    $(document).ready(function () {

        //$("#project_id").val("<?php echo $company_document_list[0]->project_id; ?>");

        $("#company_id").change(function () {

            var company_id = $("#company_id").val();
            if (company_id.length >= 1)
            {
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
            }

        });
    });

</script>
@endsection
