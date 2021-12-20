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
                        <form action="{{ route('admin.update_resign') }}" id="apply_resign" method="post">
                            @csrf
                            <input type="hidden" name="id" id="id" value="{{ $resignation_data[0]->id }}" />
                            <div class="form-group ">
                                <label>Reason</label>
                                <input type="text" value="{{ $resignation_data[0]->reason }}" name="reason" maxlength="200" id="reason" class="form-control" />
                            </div>
                            
                            <div class="form-group ">
                                <label>Resignation Details</label>
                                <textarea class="form-control" rows="6" name="resign_details" id="resign_details">{{ $resignation_data[0]->resign_details }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.resign') }}'" class="btn btn-default">Cancel</button>
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
   
    jQuery('#apply_resign').validate({
        ignore: [],
        rules: {
            reason: {
                required: true,
            },
            resign_details: {
                required: true
            },
            
        }
    });

    

</script>
@endsection
