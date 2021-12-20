@extends('layouts.admin_app')

@section('content')
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">Add Bank Payment Details</h4>
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
                        <form action="{{ route('admin.insert_payment') }}" id="add_payment" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group "> 
                                <label>Bank Name</label> 
                                <input type="text" class="form-control" name="bank_name" id="bank_name" value="" />
                            </div>
							<div class="form-group ">
                                <label>Bank Details</label>
                                <input type="textarea" class="form-control" name="bank_details" id="bank_details" value="" />
                            </div>

                            <div class="form-group ">
                                <label>Cheque Number</label>
                                <input type="textarea" class="form-control" name="cheque_number" id="cheque_number" value="" />
                            </div>      							
                            <div class="form-group ">
                                <label>Amount</label>
                                <input type="textarea" class="form-control" name="amount" id="amount" value="" />
                            </div>      
                            <div class="form-group ">
                                <label>Note</label>
                                <textarea class="form-control valid" rows="6" name="note" id="note" spellcheck="false"></textarea>
                            </div>
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.policy') }}'" class="btn btn-default">Cancel</button>
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
    jQuery('#add_payment').validate({
        ignore: [],
        rules: {
            bank_name: {
                required: true,
            },
            bank_details:{
                required: true
            },
            amount:{
                required: true
            },
        }
    });
	$(document).ready(function(){
		 
	});
	
</script>
@endsection
