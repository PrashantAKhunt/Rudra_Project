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
                        <form action="{{ route('admin.insert_company_document') }}" id="add_company_document" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group "> 
                                <input type="hidden" class="form-control" name="company_id" id="company_id" value="{{$id}}" /> 
                                <label>Title</label> 
                                <input type="text" class="form-control" name="title" id="title" value="" /> 
                            </div>
                            <div class="form-group "> 
                                <label>Upload Document (Only PDF)</label> 
                                <input type="file" class="form-control" name="document" id="document" value="" accept="application/pdf"/> 
                            </div>
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.cmp_document_list',['id'=>$id]) }}'" class="btn btn-default">Cancel</button>
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
   
    jQuery("#add_company_document").validate({
        ignore: [],
        rules: {
            title: {
                required: true,
            },
            document: {
                required: true,
            }
        }
    });
      
</script>
@endsection
