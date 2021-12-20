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
                        <form action="{{ route('admin.insert_bank') }}" id="add_bank" method="post">
                            @csrf
                            <div class="form-group"> 
                                <label>Bank Name <strong> <span class="text-danger">*</span></strong> </label> 
                                <input type="text" class="form-control" name="bank_name" id="bank_name" value="" /> 
                            </div>
                            <div class="form-group"> 
                                <label>Bank Short Name</label> 
                                <input type="text" class="form-control" name="bank_short_name" id="bank_short_name" value="" /> 
                            </div>
                            <div class="form-group"> 
                                <label>Company <strong> <span class="text-danger">*</span></strong> </label> 
                                @if(!empty($companies))
                                <select name="company_id" class="form-control" >
                                    <option value="">Select company</option>
                                    @foreach($companies as $key => $value)
                                    <option value="{{$key}}">{{$value}}</option>
                                    @endforeach
                                </select>
                                @endif
                            </div>
                            <div class="form-group">
                                <label>Account Number <strong> <span class="text-danger">*</span></strong> </label>
                                <input class="form-control"  name="account_number" id="account_number" />
                            </div>
                            <div class="form-group">
                                <label>Name on Account <strong> <span class="text-danger">*</span></strong> </label>
                                <input class="form-control"  name="beneficiary_name" id="beneficiary_name" />
                            </div> 
                            <div class="form-group">
                                <label>IFSC <strong> <span class="text-danger">*</span></strong> </label>
                                <input class="form-control"  name="ifsc" id="ifsc" /> 
                            </div> 
                            <div class="form-group">
                                <label>Bank Branch <strong> <span class="text-danger">*</span></strong> </label>
                                <input class="form-control"  name="branch" id="branch" /> 
                            </div> 
                            <div class="form-group ">
                                <label>Account Type <strong> <span class="text-danger">*</span></strong> </label>
                                <select name="account_type" class="form-control">
                                    <option value="">Select Account Type</option>
                                    <option value="CC">CC</option>
                                    <option value="Credit Card">Credit Card</option>
                                    <option value="Current">Current</option>
                                    <option value="Debit Card">Debit Card</option>
                                    <option value="FD">FD</option>
                                    <option value="OD">Over Draft</option>
                                    <option value="Saving">Savings</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Details <strong> <span class="text-danger">*</span></strong> </label>
                                <textarea class="form-control"  name="detail" id="detail" ></textarea>
                            </div> 							


                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.banks') }}'" class="btn btn-default">Cancel</button>
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

    jQuery("#add_bank").validate({
        ignore: [],
        rules: {
            bank_name: {
                required: true,
            },
            detail: {
                required: true,
            },
            company_id: {
                required: true,
            },
            beneficiary_name: {
                required: true,
            },
            ifsc: {
                required: true,
            },
            branch: {
                required: true,
            },
            account_type: {
                required: true,
            },
            account_number:{
                required:true,
                number:true
            }
        }
    });

</script>
@endsection
