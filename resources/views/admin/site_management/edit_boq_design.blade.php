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
                <li><a href="{{ route('admin.boq_design') }}">BOQ Design</a></li>
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
                        <form action="{{ route('admin.edit_boq_design') }}" id="boq_form" method="post" enctype="multipart/form-data">
                            @csrf
                            
                            {{-- <div class="row">
                                    <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Item No</label>
                                        <select class="form-control" name="item_no" id="item_no">
                                            <option value="">Select Item</option>
                                            <option value="1">Item 1</option>
                                            <option value="2">Item 2</option>
                                        </select>
                                    </div>
                                </div>
                            </div> --}}

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Block Title <span class="error">*</span></label>
                                        <input type="text" class="form-control block_title" name="block_title" id="block_title" value="{{$boq_block['block_title']}}" />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Block Detail <span class="error">*</span> </label>
                                        <textarea class="form-control block_detail" name="block_detail" id="block_detail">{{$boq_block['block_detail']}}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label>Block Drawing (Upload only image)</label>
                                        <input type="file" class="form-control block_drawing" name="block_drawing" id="block_drawing" value="" />
                                        <input type="hidden" name="block_drawing_hidden" value="{{$boq_block['block_drawing']}}">
                                        <input type="hidden" name="id" value="{{$boq_block['id']}}">
                                    </div>
                                </div>
                            </div> 
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-success">Submit</button>
                                    <button type="button" onclick="window.location.href ='{{ route('admin.boq_design') }}'" class="btn btn-default">Cancel</button>
                                </div>
                            </div>
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
$("#boq_form").validate({
    rules : {
        item_no : "required",
        block_title : "required",
        block_detail : "required",
        // block_drawing : "required",
    }
});

</script>
@endsection
