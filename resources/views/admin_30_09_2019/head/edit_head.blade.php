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
                            <form action="{{ route('admin.update_head') }}" id="edit_head" method="post">
                            <input type="hidden" id="id" name="id" value="{{ $head_detail[0]->id }}" /> 
                            @csrf
                            <div class="form-group "> 
                                <label>Head Name</label> 
                                <input type="text" class="form-control" name="head_name" id="head_name" value="{{ $head_detail[0]->head_name }}" /> 
                            </div>
                           
                            <div class="form-group "> 
                                <label>Head Detail</label> 
                                <textarea class="form-control" name="head_detail" id="head_detail" rows="10">{{$head_detail[0]->head_detail}}</textarea>
                            </div>
                           
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.heads') }}'" class="btn btn-default">Cancel</button>
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
  
   
    jQuery("#edit_head").validate({
        ignore: [],
        rules: {
            head_name: {
                required: true,
            },
            head_detail: {
                required: true,
            },
            
        }
    });
</script>
@endsection
