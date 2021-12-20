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
                            <form action="{{ route('admin.update_stationery_items') }}" id="add_stationery" method="post" enctype="multipart/form-data">
                            @csrf

                            <input type="hidden" name="Stationery_id" value="{{$stationery_data['id']}}">
                       
                            <div class="row">
<div class="col-md-6"> 
                             <div class="form-group "> 
                                <label>Stationery Item Name</label> 
                                <input type="text" class="form-control" name="item_name" id="item_name" value="{{ $stationery_data['item_name'] }}"/>
                            </div>
                            </div>
                    </div>  
                    <div class="row">
<div class="col-md-6"> 
                            <div class="form-group "> 
                                <label>Stationery Item Detail</label> 
                                <textarea class="form-control" rows="3" name="item_detail" id="item_detail" >{{ $stationery_data['item_detail'] }}</textarea>
                            </div>
                            </div>
                    </div>   
                         
                        
                    <div class="row">
<div class="col-md-6"> 
                             <div class="form-group "> 
                                <label>Stationery Item Price</label> 
                                <input type="text" class="form-control" name="item_price" id="item_price" value="{{ $stationery_data['item_price'] }}"/>
                            </div>
                            </div>
                    </div>      
                          
                        
                    <div class="row">
<div class="col-md-6"> 
                            <div class="form-group ">
                                <label>Stationery Item Image</label>
                                <input type="file"  class="form-control" accept="image/png,image/x-png, image/jpg, image/jpeg" name="item_image" id="item_image"/>
                            </div>
                            </div>
                    </div>      
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group "> 
                                    <label>First Approval User</label>
                                    <select class="form-control" id="first_approval_id" name="first_approval_id" >
                                        <option value="">Please select</option>
                                        @foreach($users_data as $key => $value)
                                        
                                        <option value="{{$value->id}}" {{ $stationery_data['first_approval_id'] == $value->id ? 'selected' : '' }}>{{$value->name}}</option>
                                        @endforeach
                                    </select> 
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group "> 
                                    <label>Second Approval User</label>
                                    <select class="form-control" id="second_approval_id" name="second_approval_id" >
                                       <option value="">Please select</option>
                                        @foreach($users_data as $key => $value)
                                        
                                        <option value="{{$value->id}}" {{ $stationery_data['second_approval_id'] == $value->id ? 'selected' : '' }}>{{$value->name}}</option>
                                        @endforeach
                                    </select> 
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
       $('#first_approval_id').select2();
       $('#second_approval_id').select2();
       removeTextAreaWhiteSpace();
    });
    jQuery("#add_stationery").validate({
        ignore: [],
        rules: {
            item_name: {
                required: true,
            },
            item_detail: {
                required: true,
            },
            item_price: {
                required: true,
            },
            first_approval_id: {
                required: true,
            },
            second_approval_id: {
                required: true,
            }
        }
    });
     function removeTextAreaWhiteSpace() {
        var myTxtArea = document.getElementById('item_detail');
        myTxtArea.value = myTxtArea.value.replace(/^\s*|\s*$/g,'');
    }
</script>
@endsection
