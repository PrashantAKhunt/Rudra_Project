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
                    <div class="col-sm-10 col-xs-10">
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
                        <form action="{{ route('admin.save_manager_types') }}" id="add_inventory_managers" method="post">
                            @csrf

                           <!--  -->
                           <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Inventory Manager <span class="error">*</span> </label>
                                        <select class="select2 m-b-10 form-control"  name="invemtory_manager" id="invemtory_manager" >
                                            <option value=""  selected disabled>Select Inventory Manager</option>
                                                @foreach($users as $key => $value)
                                                <option <?php if ($key == $invemtory_manager) { ?> selected <?php } ?> value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!--  -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Purchase Manager<span class="error">*</span> </label>
                                        <select class="select2 m-b-10 form-control" name="purchase_manager" id="purchase_manager" >
                                        <option value=""  selected disabled>Select Inventory Manager</option>
                                                @foreach($users as $key => $value)
                                                <option <?php if ($key == $purchase_manager) { ?> selected <?php } ?> value="{{ $key }}">{{ $value }}</option>
                                                @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!--  -->
                            <!--  -->
                            <button type="submit" class="btn btn-success">Save</button>

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
    jQuery('#add_inventory_managers').validate({
        ignore: [],
        rules: {
            'invemtory_manager': {
                required: true,
            },
            'purchase_manager': {
                required: true,
            },
        }
    });

    $('.select2').select2();


</script>
@endsection
