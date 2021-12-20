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
                            <form action="{{ route('admin.insert_asset') }}" id="add_asset" method="post" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" id="id" name="id"/>                            
                             <div class="form-group "> 
                                <label>Asset Name</label> 
                                <input type="text" class="form-control" name="name" id="name"/>
                            </div>
                            <div class="form-group "> 
                                <label>Department Descption</label> 
                                <textarea class="form-control" rows="5" name="description" id="description">
                                </textarea>
                            </div>
                            <div class="form-group "> 
                                <label>Asset Number (Mobile/Vehicle)</label> 
                                <input type="text" class="form-control" name="asset_1" id="asset_1"/>
                            </div>
                            <div class="form-group "> 
                                <label>Asset Number (Imie/Chassis)</label> 
                                <input type="text" class="form-control" name="asset_2" id="asset_2"/>
                            </div>
                            <div class="form-group ">
                                <label>Asset Image</label>
                                <input type="file" accept="image/png,image/x-png, image/jpg, image/jpeg" name="image[]" id="image" class="dropify" multiple/>
                            </div>
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.asset') }}'" class="btn btn-default">Cancel</button>
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
   $(document).ready(function () {
        removeTextAreaWhiteSpace();
    });
    jQuery("#add_asset").validate({
        ignore: [],
        rules: {
            name: {
                required: true,
            },
            description: {
                required: true,
            }
        }
    });
     function removeTextAreaWhiteSpace() {
        var myTxtArea = document.getElementById('description');
        myTxtArea.value = myTxtArea.value.replace(/^\s*|\s*$/g,'');
    }
</script>
@endsection
