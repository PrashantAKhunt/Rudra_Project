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
        <div class="col-lg-12 col-sm-12 col-xs-12">
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
                        <form action="{{ route('admin.update_department') }}" id="update_department_frm" method="post">
                            @csrf
                            <div class="form-group "> 
                                <label>Department Name</label> 
                                <input type="text" class="form-control" name="dept_name" id="dept_name" value="{{ $department_list[0]->dept_name }}" /> 
                            </div>

                            <div class="form-group "> 
                                <label>Department Descption</label>
                                <textarea class="form-control" rows="5" name="dept_description" id="dept_description">
                                  <?php echo $department_list[0]->dept_description;?>
                                </textarea>
                            </div>
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.department') }}'" class="btn btn-default">Cancel</button>
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
        $('#update_department_frm').validate({
            rules:{
                dept_name:{
                    required:true
                },
                dept_description:{
                    required:true
                }
            }
        })
    });
    function removeTextAreaWhiteSpace() {
        var myTxtArea = document.getElementById('dept_description');
        myTxtArea.value = myTxtArea.value.replace(/^\s*|\s*$/g,'');
    }
</script>
@endsection
