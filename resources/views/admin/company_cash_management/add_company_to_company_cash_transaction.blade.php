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
        <div class="col-lg-12 col-sm-12 col-xs-12">
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
                        <form action="{{ route('admin.insert_company_to_company_cash_transfer') }}" id="insert_sender_frm" method="post">
                            @csrf
                            <div class="row">
                                <div class="form-group"> 
                                    <label>Company</label> 
                                    <select class="form-control" name="company_id" id="company_id">
                                        <option value="">Select Company</option>
                                        @foreach($Companies as $company_list_data)
                                        <option value="{{ $company_list_data->id }}">{{ $company_list_data->company_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row" id="company_list">
                                <div class="form-group"> 
                                    <label>To Company</label> 
                                    <select class="form-control account_id"  name="to_company_id" id="to_company_id">
                                        <option value="">Select Company</option>
                                        @foreach($Companies as $company_list_data)
                                        <option value="{{ $company_list_data->id }}">{{ $company_list_data->company_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            
                            <div class="row">
                                <div class="form-group "> 
                                    <label>Amount</label> 
                                    <input type="number" class="form-control" name="balance" id="balance" value="" /> 
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success" id="submit_form">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.cash_transfer_list') }}'" class="btn btn-default">Cancel</button>
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
    
    $(document).ready(function(){
        $('#company_id').select2();
        $('.account_id').select2();
       
        jQuery("#insert_sender_frm").validate({
                ignore:  [],
                rules: {
                    company_id: {
                        required: true,
                    },
                    balance: {
                        required: true
                    },
                    to_company_id: {
                        required: true
                    }
                    
                }
        });
    });


</script>
@endsection
