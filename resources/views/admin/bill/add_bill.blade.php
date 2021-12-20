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
                        <form action="{{ route('admin.insert_bill') }}" id="add_bill" method="post">
                            @csrf
                            <div class="form-group "> 
                                <label>Bill Date</label> 
                                <input type="text" class="form-control" name="bill_date" id="bill_date" value="" /> 
                            </div>
                            <div class="form-group "> 
                                <label>Vendor</label> 
                                @if(!empty($vendors))
                                    <select name="vendor_id" class="form-control" >
                                    <option value="">Select vendor</option>
                                        @foreach($vendors as $key => $value)
                                            <option value="{{$key}}">{{$value}}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            <div class="form-group "> 
                                <label>Request by</label> 
                                <input type="text" class="form-control" name="request_by" id="request_by" value="" /> 
                            </div>
                            <div class="form-group "> 
                                <label>Bank</label> 
                                @if(!empty($banks))
                                    <select name="account_transfer_detail" class="form-control" >
                                        <option value="">Select bank</option>
                                        @foreach($banks as $key => $value)
                                            <option value="{{$key}}">{{$value}}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            <div class="form-group "> 
                                <label>Company</label> 
                                @if(!empty($companies))
                                    <select name="company_id" class="form-control" >
                                    <option value="">Select company</option>
                                        @foreach($companies as $key => $value)
                                            <option value="{{$key}}">{{$value}}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            <div class="form-group "> 
                                <label>Mode of payment</label> 
                                <input type="text" class="form-control" name="mode_of_payment" id="mode_of_payment" value="" /> 
                            </div>
                            <div class="form-group "> 
                                <label>Heads</label> 
                                @if(!empty($heads))
                                    <select name="head_id" class="form-control" >
                                    <option value="">Select Head</option>
                                        @foreach($heads as $key => $value)
                                            <option value="{{$key}}">{{$value}}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            <div class="form-group "> 
                                <label>Account number</label> 
                                <input type="text" class="form-control" name="account_number" id="account_number" value="" /> 
                            </div>
                           
                            <div class="form-group ">
                                <label>Deduction details</label>
                                <textarea class="form-control" rows="10" name="deduction_details" id="deduction_details" ></textarea>
                            </div> 
                            <div class="form-group "> 
                                <label>Pending amount</label> 
                                <input type="text" class="form-control" name="pending_amount" id="pending_amount" value="" /> 
                            </div>
                            <div class="form-group "> 
                                <label>Amount released</label> 
                                <input type="text" class="form-control" name="amount_released" id="amount_released" value="" /> 
                            </div>
                            <div class="form-group ">
                                <label>Notes</label>
                                <textarea class="form-control" rows="10" name="notes" id="notes" ></textarea>
                            </div> 
                            <div class="form-group "> 
                                <label>Budget sheet no</label> 
                                <input type="text" class="form-control" name="budget_sheet_no" id="budget_sheet_no" value="" /> 
                            </div>
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.bills') }}'" class="btn btn-default">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>
@endsection


@section('script')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">


<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
   $( function() {
    $( "#bill_date" ).datepicker({ dateFormat: 'yy-mm-dd' });
  } );
    jQuery("#add_bill").validate({
        ignore: [],
        rules: {

            bill_date:{
                required:true
            },
            vendor_id:{
                required:true
            },
            request_by:{
                required:true
            },
            account_transfer_detail:{
                required:true
            },
            company_id:{
                required:true
            },
            mode_of_payment:{
                required:true
            },
            head_id:{
                required:true
            },
            account_number:{
                required:true
            },
            account_number:{
                required:true
            },
            deduction_details:{
                required:true
            },
            pending_amount:{
                required:true
            },
            amount_released:{
                required:true
            },
            notes:{
                required:true
            },
            budget_sheet_no:{
                required:true
            }
            
        }
    });
      
</script>
@endsection
