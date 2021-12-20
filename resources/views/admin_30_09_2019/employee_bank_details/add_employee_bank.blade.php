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
                        <form action="{{ route('admin.insert_employee_bank') }}" id="insert_employee_bank" method="post">
                            @csrf
                            <!-- <div class="form-group "> 
                                @if(!empty($employee))
                                    <select name="user_id" class="form-control" id="select2">
                                    <option value="">Select Employee</option>
                                        @foreach($employee as $key => $value)
                                            <option value="{{$key}}">{{$value}}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div> -->


                            <div class="form-group "> 
                                <label>Bank Name</label> 
                                <input type="text" class="form-control" name="bank_name" id="bank_name" value="" /> 
                            </div>

                            <div class="form-group "> 
                                <label>Account Number</label> 
                                <input type="text" class="form-control" name="account_number" id="account_number" value="" /> 
                            </div>

                            <div class="form-group "> 
                                <label>Ifsc Code</label> 
                                <input type="text" class="form-control" name="ifsc_code" id="ifsc_code" value="" /> 
                            </div>
                            
                            <div class="form-group "> 
                                <label>Name On Account</label> 
                                <input type="text" class="form-control" name="name_on_account" id="name_on_account" value="" /> 
                            </div>
                            
                            <div class="form-group "> 
                                <label>Pancard Number</label> 
                                <input type="text" class="form-control" name="pancard_number" id="pancard_number" value="" /> 
                            </div>

                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.employee_bank') }}'" class="btn btn-default">Cancel</button>
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
    jQuery("#insert_employee_bank").validate({
        ignore: [],
        rules: {
            bank_name: {
                required: true,
            },
            account_number:{
                required: true,
            },
            ifsc_code:{
                required: true,
            },
            name_on_account:{
                required: true,
            },
            pancard_number:{
                required: true,
            }
        }
    });
      
</script>
@endsection
