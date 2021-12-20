@extends('layouts.admin_app')

@section('content')
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">Add Company Document Management</h4>
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
                        <form action="{{ route('admin.insert_company_document_management') }}" id="insert_company_document_management" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group "> 
                                <label>Select Company <span class="error">*</span> </label>
                                <select class="form-control" name="company_id" id="company_id">
                                    <option value="">Select Company</option>
                                    @foreach($Companies as $company_list_data)
                                    <option value="{{ $company_list_data->id }}">{{ $company_list_data->company_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group "> 
                                <label>Select Custodian</label>
                                <select class="form-control" name="custodian_id" id="custodian_id">
                                    <option value="">Select Custodian</option>
                                    @foreach($users as $user_list_data)
                                    <option value="{{ $user_list_data->id }}">{{ $user_list_data->name }}</option>
                                    @endforeach
                                </select>
                            </div>                            
                            <div class="form-group ">
                                <label>Title <span class="error">*</span> </label>
                                <input type="text" class="form-control" name="title" id="title" value="" />
                            </div>      
                            <div class="form-group ">
                                <label>Description</label>
                                <textarea class="form-control valid" rows="6" name="description" id="description" spellcheck="false"></textarea>
                            </div>
                            <div class="form-group ">
                                <label>Document File <span class="error">*</span> </label>                                 
                                <input type="file" class="form-control" name="file" id="file"/>                                
                            </div>                              
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.company_document_management') }}'" class="btn btn-default">Cancel</button>
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
    jQuery('#insert_company_document_management').validate({
        ignore: [],
        rules: {
            company_id: {
                required: true,
            },
            title: {
                required: true
            },            
            file: {
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

</script>
@endsection
