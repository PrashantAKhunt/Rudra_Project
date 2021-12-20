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
                        <form action="{{ route('admin.update_emp_work') }}" id="add_process" method="post" enctype="multipart/form-data">
                            @csrf

                            <input type="hidden" id="id" name="id" value="{{ $details[0]->id }}" > 
                            

                            <h4>Already Uploadeded Document
                                <a title="Download File" href="{{ asset('storage/'.str_replace('public/','',!empty($details[0]->work_document) ? $details[0]->work_document : 'public/no_image')) }}" download><i class="fa fa-cloud-download fa-lg"></i></a></a>
                            </h4>  
                            <div class="form-group ">
                                <label>Upload Document</label>
                                <input type="file" name="work_document" @if(empty($details[0]->work_document)) required @endif id="work_document"/>
                            </div>

                            <div class="form-group "> 
                                <label>Work Deatils</label> 
                                <textarea class="form-control" rows="5" name="work_details" id="work_details">{{ $details[0]->work_details }}</textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.prime_action_list') }}'" class="btn btn-default">Cancel</button>
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
    $(document).ready(function(){ 
        removeTextAreaWhiteSpace();
        jQuery("#add_process").validate({
        ignore: [],
        rules: {
            work_details: {
                required: true,
            }
        }
    });
     function removeTextAreaWhiteSpace() {
        var myTxtArea = document.getElementById('work_details');
        myTxtArea.value = myTxtArea.value.replace(/^\s*|\s*$/g,'');
    }
    });
 
</script>

<script>


</script>
@endsection
