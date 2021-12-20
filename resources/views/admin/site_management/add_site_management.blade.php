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
                        <form action="#" id="site_management_form" method="post">
                            @csrf
                            <div class="form-group ">
                                <label>Item No</label>
                                 <input type="text" class="form-control" name="item_no" id="item_no" value="" />
                            </div>
                            <div class="form-group ">
                                <label>Item Description</label>
                                <textarea class="form-control" name="item_description" id="item_description"></textarea>
                            </div>
                            <div class="form-group ">
                                <label>UOM</label>
                                <input type="text" class="form-control" name="item_uom" id="item_uom" value="" />
                            </div>
                            <div class="form-group ">
                                <label>Qauntity</label>
                                <input type="number" class="form-control" name="item_qty" id="item_qty" value="" />
                            </div>
                            <div class="form-group ">
                                <label>Rate</label>
                                <input type="text" class="form-control" name="item_rate" id="item_rate" value="" />
                            </div>
                            <div class="form-group ">
                                <label>Amout</label>
                                <input type="text" class="form-control" name="item_amount" id="item_amount" value="" />
                            </div>
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.site_management') }}'" class="btn btn-default">Cancel</button>
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
    jQuery("#site_management_form").validate({
        rules: {
            item_description: {
                required: true,
            },
            item_uom: {
                required: true,
            },
            item_amount: {
                required: true,
            },
            item_qty: {
                required: true,
            },
            item_rate: {
                required: true,
            },
            item_no: {
                required: true,
            },
        },
        submitHandler: function (form) {
            // Prevent double submission
            if (!this.beenSubmitted) {
                this.beenSubmitted = true;
                form.submit();
            }
        }
    });
      
</script>
@endsection
