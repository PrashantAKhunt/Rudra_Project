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
                        <form action="{{route('admin.save_bank_charge_sub_category')}}" id="add_charge" method="post">
                            @csrf
                            <div class="form-group "> 
                                <label>Bank Charge Category <strong> <span class="text-danger">*</span></strong> </label> 
                                <select name="bank_charge_category_id" id="bank_charge_category_id" class="form-control">
                                <option value="">Select Category</option>
                                @foreach($change_category as $key => $value)
                                <option value="{{$value['id']}}">{{$value['title']}}</option>
                                @endforeach
                                </select>
                            </div>
                            <div class="form-group "> 
                                <label>Title <strong> <span class="text-danger">*</span></strong> </label> 
                                <input type="text" class="form-control" name="title" id="title" value="" /> 
                            </div>
                            <div class="form-group ">
                                <label>Detail <strong> <span class="text-danger">*</span></strong> </label>
                                <textarea class="form-control" rows="10" name="detail" id="detail"></textarea>
                            </div>
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.bank_charge_sub_category') }}'" class="btn btn-default">Cancel</button>
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
    });
    jQuery("#add_charge").validate({
        rules: {
            title: {
                required: true,
            },
            detail:{
                required: true,
            },
            bank_charge_category_id : {
                required : true,
            }
        }
    });
      
</script>
@endsection
