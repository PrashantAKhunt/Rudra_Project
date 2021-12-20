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
                        <form action="{{ route('admin.insert_employee_expense') }}" id="insert_employee_expense_frm" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group "> 
                            <label>Select Expense Category</label>
                            <select class="form-control" name="expense_category" id="expense_category">
                                    <option value="">Select Expense Category</option>
                                    @foreach($Expense_List as $asset_list_data)
                                    <option value="{{ $asset_list_data->id }}">{{ $asset_list_data->category_name }}</option>
                                    @endforeach
                            </select>
                            </div>

                            <div class="form-group "> 
                                <label>User Name</label>
                                <select class="form-control" name="user_id" id="user_id">
                                    <option value="">Select User</option>
                                    @foreach($UsersName as $users_name_data)
                                    <option value="{{ $users_name_data->id }}">{{ $users_name_data->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group "> 
                                <label>Title</label> 
                                <input type="text" class="form-control" name="title" id="title" value="" /> 
                            </div>
                            <div class="form-group "> 
                                <label>Bill Number</label> 
                                <input type="text" class="form-control" name="bill_number" id="bill_number" value="" /> 
                            </div>
                            <div class="form-group "> 
                                <label>Merchant Name</label> 
                                <input type="text" class="form-control" name="merchant_name" id="merchant_name" value="" /> 
                            </div>
                            <div class="form-group "> 
                                <label>Amount</label> 
                                <input type="text" class="form-control" name="amount" id="amount" value="" /> 
                            </div>
                            <div class="form-group "> 
                                <label>Expense Date</label> 
                                <input type="text" class="form-control" name="expense_date" id="expense_date" value="" /> 
                            </div>
                            <div class="form-group "> 
                                <label>Comment</label> 
                                <input type="text" class="form-control" name="comment" id="comment" value="" /> 
                            </div>
                            <div class="form-group ">
                                <label>Expense Image</label>
                                <input type="file" accept="image/png,image/x-png, image/jpg, image/jpeg" name="image" id="image" class="dropify" />
                            </div>

                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" onclick="window.location.href ='{{ route('admin.employee_expense') }}'" class="btn btn-default">Cancel</button>
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

        jQuery('#expense_date').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: "dd-mm-yyyy"
        });
        
        $('#user_id').select2();

        $('#insert_employee_expense_frm').validate({
            rules:{
                // user_id:{
                //     required:true
                // },
                // expense_category:{
                //     required:true
                // },
                // title:{
                //     required:true
                // },
                // bill_number:{
                //     required:true
                // },
                // merchant_name:{
                //     required:true
                // },
                // amount:{
                //     required:true
                // },
                // expense_date:{
                //     required:true
                // },
                // comment:{
                //     required:true
                // },
                // expense_image:{
                //     required:true
                // }
            }
        })
    });
</script>
@endsection
