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
                            <form action="{{ route('admin.insert_inventory_stock_request') }}" id="add_stationery" method="post" enctype="multipart/form-data">
                            @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group "> 
                                    <label>Stationery Item <span class="error">*</span> </label>
                                    <select class="form-control" id="inventory_item_id" name="inventory_item_id" >
                                    <option value=""  selected disabled>Select Stationery Item</option>
                                        @foreach($stationery_items as $key => $value)
                                        <option value="{{$value['id']}}">{{$value['item_name']}}</option>
                                        @endforeach
                                    </select> 
                                </div>
                            </div>
                        </div>
                       
                             
                        <div class="row">
                            <div class="col-md-6"> 
                                <div class="form-group "> 
                                    <label>Details <span class="error">*</span> </label> 
                                    <textarea class="form-control" rows="5" name="detail" id="detail">
                                    </textarea>
                                </div>
                            </div>
                        </div>   

                             
                        <div class="row">
                            <div class="col-md-6"> 
                                <div class="form-group "> 
                                    <label>Item Quantity <span class="error">*</span> </label> 
                                    <input type="number" class="form-control" name="item_quantity" id="item_quantity"/>
                                </div>
                            </div>
                        </div>  
                             
                        
                            <div class="clearfix"></div>
                            <br>
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.inventory_stock_requests') }}'" class="btn btn-default">Cancel</button>
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
        $('#inventory_item_id').select2();
       removeTextAreaWhiteSpace();
    });
    jQuery("#add_stationery").validate({
        ignore: [],
        rules: {
            inventory_item_id: {
                required: true,
            },
            detail: {
                required: true,
            },
            item_quantity: {
                required: true,
            }
        }
    });
     function removeTextAreaWhiteSpace() {
        var myTxtArea = document.getElementById('detail');
        myTxtArea.value = myTxtArea.value.replace(/^\s*|\s*$/g,'');
    }
</script>
@endsection
