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
                            <form action="{{ route('admin.update_company') }}" id="edit_company" method="post">
                            <input type="hidden" id="id" name="id" value="{{ $company_detail[0]->id }}" /> 
                            @csrf
                            <div class="form-group "> 
                                <label>Company Name</label> 
                                <input type="text" class="form-control" name="company_name" id="company_name" value="{{ $company_detail[0]->company_name }}" /> 
                            </div>
                            <div class="form-group "> 
                                <label>Detail</label> 
                                <textarea class="form-control" name="detail" id="detail" rows="10">{{$company_detail[0]->detail}}</textarea>
                            </div>
                           
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.companies') }}'" class="btn btn-default">Cancel</button>
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
  
   
    jQuery("#edit_company").validate({
        ignore: [],
        rules: {
            company_name: {
                required: true,
            },
            detail: {
                required: true,
            },
            
        }
    });
</script>
@endsection
