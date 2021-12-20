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
                    <div class="col-md-12">
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
                            <form action="{{ route('admin.update_stationery_item_access_request') }}" id="add_stationery" method="post" enctype="multipart/form-data">
                            @csrf

                            <input type="hidden" name="id" value="{{$stationery_access_data['id']}}">
                       
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group "> 
                                    <label>Stationery Item</label>
                                    <select class="form-control" id="stationery_items_id" name="stationery_items_id" >
                                    
                                        @foreach($stationery_items as $key => $value)
                                        
                                        <option value="{{$value['id']}}" {{ $stationery_access_data['stationery_items_id'] == $value['id'] ? 'selected' : '' }}>{{$value['item_name']}}</option>
                                        @endforeach
                                    </select> 
                                </div>
                            </div>
                        </div>
                       
                             
                    <div class="row">
                            <div class="col-md-6"> 
                                <div class="form-group "> 
                                    <label>Reason Note</label> 
                                    <textarea class="form-control" rows="5" name="request_note" id="request_note">{{ $stationery_access_data['request_note'] }}</textarea>
                                </div>
                            </div>
                        </div> 
                    
                    
                            <div class="clearfix"></div>
                            <br>
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.stationery_items') }}'" class="btn btn-default">Cancel</button>
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
       $('#stationery_items_id').select2();
       removeTextAreaWhiteSpace();
    });
    jQuery("#add_stationery").validate({
        ignore: [],
        rules: {
            request_note: {
                required: true,
            },
            stationery_items_id: {
                required: true,
            }
        }
    });
     function removeTextAreaWhiteSpace() {
        var myTxtArea = document.getElementById('request_note');
        myTxtArea.value = myTxtArea.value.replace(/^\s*|\s*$/g,'');
    }
</script>
@endsection
