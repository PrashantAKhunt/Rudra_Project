@extends('layouts.admin_app')

@section('content')
<?php

use Illuminate\Support\Facades\Config; ?>
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
        <div class="col-md-12 col-lg-12 col-sm-12">
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
            <div class="white-box">
                <p class="text-muted m-b-30"></p>
                <br>
                <b class="error">Approval Flow: {{implode(' -> ',config('constants.EMP_EXPENSE_APPROVAL'))}}</b>
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="table-responsive">
                    <form method="post" action="{{route('admin.approve_employee_expence_multiple')}}" >
                        @csrf
                        <table id="expense_approval_table" class="table table-striped">
                            <thead>
                                <tr>
                                @if(config::get('constants.ACCOUNT_ROLE') != Auth::user()->role)<th><input type="checkbox" name="expense_approve_list[]" id="select-all" onClick="toggle(this)" /></th>@endif
                                    <th>Expense Code</th>
                                    <th>User Name</th>
                                    <th>Company</th>
                                    <th>Client</th>
                                    <th>Project</th>
                                    <th>Other Project Detail</th>
                                    <th>Site Name</th>
                                    <th>Expense Type</th>
                                    <th>Expense Category</th>
                                    <th>Title</th>
                                    <th>Merchant Name</th>
                                    <th>Amount</th>
                                    <th>Bill Number</th>
                                    <th>Voucher Number</th>
                                    <th>Voucher Image</th>
                                  
                                    <th>Expense Date</th>
                                    <th>Expense Note(Comment)</th>
                                    <th>Expense Image</th>

                                 
                                     <th>HR Status</th>
                                    <th>First Approval</th>
                                    <th>Assistant Status</th>
                                    <th>Secound Approval</th>
                                    <th>Admin Status</th>
                                    <th>Third Approval</th>
                                    <th>Super Admin Status</th>
                                    <th>Fourth Approval</th>
                                    <th>Accountant Status</th>
                                    <th>Fifth Approval</th>
                                    <th>Your Approval</th>
                                    <th>Status</th>
                                    <th>Approve</th>
                                    <th>Reject</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($employee_expense_list->count()>0)
                                @foreach($employee_expense_list as $expense_category)
                                <tr @if($expense_category->voucher_repeat === 1) class="error" @endif>
                                    

                                @if(config::get('constants.ACCOUNT_ROLE') != Auth::user()->role)
                                    <td>
                                        <input type="checkbox" value="{{$expense_category->id }}" name="expense_approve_list[]" />
                                    </td>
                                    @endif

                                    <td>{{$expense_category->expense_code }}</td>
                                    <td>{{$expense_category->name }}</td>
                                    <td>{{$expense_category->company_name}}</td>
                                    <td>

                                    @if($expense_category->client_name)
                                        @if($expense_category->client_name == 'Other Client')
                                        {{ $expense_category->client_name }}
                                            @else
                                            {{ $expense_category->client_name." (".$expense_category->location.")" }}
                                            @endif

                                    @else
                                    N/A
                                    @endif        
                                        </td>
                                    <td>{{$expense_category->project_name}}</td>
                                    @if($expense_category->other_project)
                                    <td>{{$expense_category->other_project }}</td>
                                    @else
                                    <td>NA</td>
                                    @endif
                                    <td>{{$expense_category->site_name}}</td>
                                    <td>{{$expense_category->expense_main_category }}</td>
                                    <td>{{$expense_category->category_name }}</td>
                                    <td>{{$expense_category->title }}</td>
                                    <td>{{$expense_category->merchant_name }}</td>
                                    <td class="text-primary">Rs{{$expense_category->amount }}</td>
                                    <td>{{$expense_category->bill_number }}</td>
                                     <td>{{$expense_category->voucher_no }}</td>

                                     <td>
                                            @if($expense_category->voucher_image)
                                            <div id="gallery-content">
                                                <div id="gallery-content-center">
                                                    <img width="100px" height="100px" src="{{asset('storage/' . str_replace('public/', '', $expense_category->voucher_image))}}" alt="gallery" onclick="set_voucher_image('{{asset('storage/' . str_replace('public/', '', $expense_category->voucher_image))}}');" data-toggle="modal" data-target="#voucher_model" class="all studio"/> 
                                                
                                                </div>
                                            </div>
                                            <br>
                                            <div class="text-center">
                                               <a href="{{asset('storage/' . str_replace('public/', '', $expense_category->voucher_image))}}" title="Download Image" download><i class="fa fa-cloud-download fa-lg" ></i></a>
                                            </div>
                                        @endif
                                        </td>

                                    <td class="text-primary">{{$expense_category->expense_date }}</td>
                                    <td>
                                        <textarea style="display: none;" name="" id="comment_{{$expense_category->id }}">{{$expense_category->comment}}</textarea>
                                        <a href="javascript:void(0)" data-toggle="modal" data-target="#work_note_modal"  class="btn btn-rounded btn-info" title="View Note" onclick="show_comment('{{$expense_category->id }}')"><i class="fa fa-eye"></i></a>
                                    </td>
                                    <td>
                                        @if($expense_category->expense_image)
                                        <div class="magnify-image">
                                        <img width="100px" height="100px" src="{{asset('storage/' . str_replace('public/', '', $expense_category->expense_image))}}" alt="gallery" onclick="set_image('{{asset('storage/' . str_replace('public/', '', $expense_category->expense_image))}}');" data-toggle="modal" data-target="#image_model" class="all studio"/> 
                                        </div>
                                        <br>
                                        <div class="text-center">
                                        <a href="{{asset('storage/' . str_replace('public/', '', $expense_category->expense_image))}}" title="Download Image" download><i class="fa fa-cloud-download fa-lg"></i></a>
                                        </div>
                                        @endif
                                    </td>

                                    <td>
                                        @if($expense_category->first_approval_status=="Pending")
                                        <b class="text-warning">{{ $expense_category->first_approval_status }}</b>
                                        @elseif($expense_category->first_approval_status=="Approved")
                                        <b class="text-success">
                                            {{ $expense_category->first_approval_status }}

                                        </b>
                                        @elseif($expense_category->first_approval_status=="Rejected")
                                        <b class="text-danger">

                                            {{ $expense_category->first_approval_status }}

                                        </b>
                                        @endif
                                    </td>
                                    <td>{{ ($expense_category->first_approval_datetime) ? date('d-m-Y H:i A', strtotime($expense_category->first_approval_datetime)) : "N/A"  }}</td> 
                                    <td>
                                        @if($expense_category->second_approval_status=="Pending")
                                        <b class="text-warning">{{ $expense_category->second_approval_status }}</b>
                                        @elseif($expense_category->second_approval_status=="Approved")
                                        <b class="text-success">
                                            {{ $expense_category->second_approval_status }}

                                        </b>
                                        @elseif($expense_category->second_approval_status=="Rejected")
                                        <b class="text-danger">
                                            {{ $expense_category->second_approval_status }}

                                            @endif
                                    </td>
                                    <td>{{ ($expense_category->secound_approval_datetime) ? date('d-m-Y H:i A', strtotime($expense_category->secound_approval_datetime)) : "N/A"  }}</td> 
                                    <td>
                                        @if($expense_category->third_approval_status=="Pending")
                                        <b class="text-warning">{{ $expense_category->third_approval_status }}</b>
                                        @elseif($expense_category->third_approval_status=="Approved")
                                        <b class="text-success">
                                            {{ $expense_category->third_approval_status }}

                                        </b>
                                        @elseif($expense_category->third_approval_status=="Rejected")
                                        <b class="text-danger">
                                            {{ $expense_category->third_approval_status }}

                                        </b>
                                        @endif
                                    </td>
                                    <td>{{ ($expense_category->third_approval_datetime) ? date('d-m-Y H:i A', strtotime($expense_category->third_approval_datetime)) : "N/A"  }}</td> 
                                    <td>
                                        @if($expense_category->forth_approval_status=="Pending")
                                        <b class="text-warning">{{ $expense_category->forth_approval_status }}</b>
                                        @elseif($expense_category->forth_approval_status=="Approved")
                                        <b class="text-success">
                                            {{ $expense_category->forth_approval_status }}

                                        </b>
                                        @elseif($expense_category->forth_approval_status=="Rejected")
                                        <b class="text-danger">
                                            {{ $expense_category->forth_approval_status }}

                                        </b>
                                        @endif
                                    </td>
                                    <td>{{ ($expense_category->fourth_approval_datetime) ? date('d-m-Y H:i A', strtotime($expense_category->fourth_approval_datetime)) : "N/A"  }}</td> 
                                    <td>
                                        @if($expense_category->fifth_approval_status=="Pending")
                                        <b class="text-warning">{{ $expense_category->fifth_approval_status }}</b>
                                        @elseif($expense_category->fifth_approval_status=="Approved")
                                        <b class="text-success">
                                            {{ $expense_category->fifth_approval_status }}

                                        </b>
                                        @elseif($expense_category->fifth_approval_status=="Rejected")
                                        <b class="text-danger">
                                            {{ $expense_category->fifth_approval_status }}

                                        </b>
                                        @endif
                                    </td>
                                    <td>{{ ($expense_category->fifth_approval_datetime) ? date('d-m-Y H:i A', strtotime($expense_category->fifth_approval_datetime)) : "N/A"  }}</td> 

                                    @if(Auth::user()->role==config('constants.REAL_HR'))
                                    <?php
                                    if ($expense_category->first_approval_status == 'Pending') {
                                        $class = "text-warning";
                                    } elseif ($expense_category->first_approval_status == 'Approved') {
                                        $class = "text-success";
                                    } else {
                                        $class = "text-danger";
                                    }
                                    ?>
                                    <td class="<?php echo $class; ?>">{{$expense_category->first_approval_status }}</td>
                                    @endif

                                    @if(Auth::user()->role==config('constants.ASSISTANT'))
                                    <?php
                                    if ($expense_category->second_approval_status == 'Pending') {
                                        $class = "text-warning";
                                    } elseif ($expense_category->second_approval_status == 'Approved') {
                                        $class = "text-success";
                                    } else {
                                        $class = "text-danger";
                                    }
                                    ?>
                                    <td class="<?php echo $class; ?>">{{$expense_category->second_approval_status }}</td>
                                    @endif

                                    @if(Auth::user()->role==config('constants.Admin'))
                                    <?php
                                    if ($expense_category->third_approval_status == 'Pending') {
                                        $class = "text-warning";
                                    } elseif ($expense_category->third_approval_status == 'Approved') {
                                        $class = "text-success";
                                    } else {
                                        $class = "text-danger";
                                    }
                                    ?>
                                    <td class="<?php echo $class; ?>">{{$expense_category->third_approval_status }}</td>
                                    @endif

                                    @if(Auth::user()->role==config('constants.SuperUser'))
                                    <?php
                                    if ($expense_category->forth_approval_status == 'Pending') {
                                        $class = "text-warning";
                                    } elseif ($expense_category->forth_approval_status == 'Approved') {
                                        $class = "text-success";
                                    } else {
                                        $class = "text-danger";
                                    }
                                    ?>
                                    <td class="<?php echo $class; ?>">{{$expense_category->forth_approval_status }}</td>
                                    @endif

                                    @if(Auth::user()->role==config('constants.ACCOUNT_ROLE'))
                                    <?php
                                    if ($expense_category->fifth_approval_status == 'Pending') {
                                        $class = "text-warning";
                                    } elseif ($expense_category->fifth_approval_status == 'Approved') {
                                        $class = "text-success";
                                    } else {
                                        $class = "text-danger";
                                    }
                                    ?>
                                    <td class="<?php echo $class; ?>">{{$expense_category->fifth_approval_status }}</td>
                                    @endif
                                    <?php
                                    if ($expense_category->status == 'Pending') {
                                        $class = "text-warning";
                                    } elseif ($expense_category->status == 'Approved') {
                                        $class = "text-success";
                                    } else {
                                        $class = "text-danger";
                                    }
                                    ?>
                                    <td class="<?php echo $class; ?>">{{$expense_category->status}}</td>
                                    <td>
                                        <?php if ((Auth::user()->role == config('constants.REAL_HR') && $expense_category->first_approval_status == "Pending")) { ?>
                                            <a href="#" data-href="<?php echo url('approve_employee_expense/' . $expense_category->id) ?>" onclick="approve_confirm(this);" class="btn btn-success btn-rounded" title="Change Status"><i class="fa fa-check"></i></a>
                                        <?php } elseif ($expense_category->first_approval_status == "Approved" && Auth::user()->role == config('constants.ASSISTANT') && $expense_category->second_approval_status == "Pending") {
                                            ?>
                                            <a href="#" data-href="<?php echo url('approve_employee_expense/' . $expense_category->id) ?>" onclick="approve_confirm(this);" class="btn btn-success btn-rounded" title="Change Status"><i class="fa fa-check"></i></a>
                                        <?php } elseif ($expense_category->second_approval_status == "Approved" && Auth::user()->role == config('constants.Admin') && $expense_category->third_approval_status == "Pending") {
                                            ?>
                                            <a href="#" data-href="<?php echo url('approve_employee_expense/' . $expense_category->id) ?>" onclick="approve_confirm(this);" class="btn btn-success btn-rounded" title="Change Status"><i class="fa fa-check"></i></a>
                                        <?php } elseif ($expense_category->third_approval_status == "Approved" && Auth::user()->role == config('constants.SuperUser') && $expense_category->forth_approval_status == "Pending") {
                                            ?>
                                            <a href="#" data-href="<?php echo url('approve_employee_expense/' . $expense_category->id) ?>" onclick="approve_confirm(this);" class="btn btn-success btn-rounded" title="Change Status"><i class="fa fa-check"></i></a>
                                            <?php
                                        } elseif ($expense_category->forth_approval_status == "Approved" && Auth::user()->role == config('constants.ACCOUNT_ROLE') && $expense_category->fifth_approval_status == "Pending") {
                                            ?>
                                           <a href="#Approvemodel" data-toggle="modal" onclick="approve_confirmByAccountant('<?php echo $expense_category->id ?>');" class="btn btn-success btn-rounded" title="Change Status"><i class="fa fa-check"></i></a>
                                        <?php } ?>

                                    </td>

                                    <td>
                                        <?php if ((Auth::user()->role == config('constants.REAL_HR') && $expense_category->first_approval_status == "Pending")) { ?>
                                            <a href="#expense_model" onclick="reject_employee_expenses('<?php echo url('reject_emp_expense/' . $expense_category->id) ?>')" data-toggle="modal" class="btn btn-danger btn-rounded" title="Reject Expense"><i class="fa fa-times"></i></a>
                                        <?php } elseif ($expense_category->first_approval_status == "Approved" && Auth::user()->role == config('constants.ASSISTANT') && $expense_category->second_approval_status == "Pending") {
                                            ?>
                                            <a href="#expense_model" onclick="reject_employee_expenses('<?php echo url('reject_emp_expense/' . $expense_category->id) ?>')" data-toggle="modal" class="btn btn-danger btn-rounded" title="Reject Expense"><i class="fa fa-times"></i></a>
                                        <?php } elseif ($expense_category->second_approval_status == "Approved" && Auth::user()->role == config('constants.Admin') && $expense_category->third_approval_status == "Pending") {
                                            ?>
                                            <a href="#expense_model" onclick="reject_employee_expenses('<?php echo url('reject_emp_expense/' . $expense_category->id) ?>')" data-toggle="modal" class="btn btn-danger btn-rounded" title="Reject Expense"><i class="fa fa-times"></i></a>
                                        <?php } elseif ($expense_category->third_approval_status == "Approved" && Auth::user()->role == config('constants.SuperUser') && $expense_category->forth_approval_status == "Pending") {
                                            ?>
                                            <a href="#expense_model" onclick="reject_employee_expenses('<?php echo url('reject_emp_expense/' . $expense_category->id) ?>')" data-toggle="modal" class="btn btn-danger btn-rounded" title="Reject Expense"><i class="fa fa-times"></i></a>
                                            <?php
                                        } elseif ($expense_category->forth_approval_status == "Approved" && Auth::user()->role == config('constants.ACCOUNT_ROLE') && $expense_category->fifth_approval_status == "Pending") {
                                            ?>
                                            <a href="#expense_model" onclick="reject_employee_expenses('<?php echo url('reject_emp_expense/' . $expense_category->id) ?>')" data-toggle="modal" class="btn btn-danger btn-rounded" title="Reject Expense"><i class="fa fa-times"></i></a>
                                        <?php } ?>

                                    </td>
                                </tr>
                                @endforeach

                                @endif
                            </tbody>
                        </table>
                        <br>
                        @if($employee_expense_list->count()>0)
                        @if(config::get('constants.ACCOUNT_ROLE') != Auth::user()->role)
                        <button type="submit" class="btn btn-success">Approve Expense</button>
                        @endif
                        @endif
                    </form>
                </div>
            </div>
            <!--row -->

        </div>
        <div class="col-md-12 col-lg-12 col-sm-12">

            <div class="white-box">                
                <div class="row bg-title">
                    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                        <h4 class="page-title">All Expenses History</h4>
                    </div>

                    <!-- /.col-lg-12 -->
                </div>
                <div class="table-responsive">
                    <table id="all_employee_expense_list_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Expense Code</th>
                                <th>User Name</th>
                                <th>Company</th>
                                <th>Client</th>
                                <th>Project</th>
                                <th>Other Project Detail</th>
                                <th>Site Name</th>
                                <th>Expense Type</th>
                                <th>Expense Category</th>
                                <th>Title</th>
                                <th>Merchant Name</th>
                                <th>Amount</th>
                                <th>Bill Number</th>
                                <th>Expense Date</th>
                                <th>Expense Note(Comment)</th>
                                <th>Expense Image</th>
                               
                                <th>Bank Details</th>
                                <th>Cheque Number </th>
                                <th>RTGS Number </th>
                                <th>Voucher Number </th>
                                <th>Voucher Image</th>
                                <th>Transaction Completed Detail</th>

                                <th>HR Status</th>
                                <th>First Approval</th>
                                <th>Assistant Status</th>
                                <th>Secound Approval</th>
                                <th>Admin Status</th>
                                <th>Third Approval</th>
                                <th>Super Admin Status</th>
                                <th>Fourth Approval</th>
                                <th>Accountant Status</th>
                                <th>Fifth Approval</th>
                                <th>Status</th>
                                <th>Approver Accountant</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
            </div>
            <!--row -->
            </div>
            <div id="expense_model" class="modal fade">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content" id="model_data">
                    </div>
                </div>
            </div> 
            <!-- Approve Model -->
            <div id="Approvemodel" class="modal fade">
                <div class="modal-dialog">
                <div class="modal-content" id="model_data">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h3 class="panel-title">Approve Expense</h3>
                    </div>
                    <div class="modal-body">
                        <form method="post" action="{{ route('admin.approve_employee_expenseByAccountant') }}" id="travel_option">
                            @csrf
                            <input type="hidden" name="id" id="expense_id" value="">
                            <input type="hidden" name="company_id" id="company_id" value="">
                            <input type="hidden" id="ch_no" value="">
                            <input type="hidden" name="rtgs_no" id="rtgs_no" value="">

                            <div class="row">
                                <div class="form-group">
                                    <label>Bank Name</label>
                                    <select class="form-control" name="bank_id" id="bank_id">

                                    </select>
                                </div>
                                <div class="form-group ">
                                    <label>Cheque Ref Number</label>
                                    <select class="form-control" name="check_ref_no" id="check_ref_no">
                                        <option value="">Select Cheque Ref Number</option>
                                    </select>
                                </div>
                                <div class="form-group ">
                                    <label>Cheque Number</label>
                                    <select class="form-control" name="cheque_number" id="cheque_number">
                                    </select>
                                </div>
                                <div class="form-group ">
                                    <label>Rtgs Ref Number</label>
                                    <select class="form-control" name="rtgs_ref_no" id="rtgs_ref_no">
                                        <option value="">Select Rtgs Ref Number</option>
                                    </select>
                                </div>
                                <div class="form-group ">
                                    <label>RTGS Number</label>
                                    <select class="form-control" name="rtgs_number" id="rtgs_number">
                                           <option value="">Select RTGS</option>

                                       </select>
                                </div>
                                {{-- <div class="form-group ">
                                    <label>Voucher Number</label>
                                    <input type="text" class="form-control" name="voucher_no" id="voucher_no" value="" />
                                </div> --}}
                                <div class="form-group ">
                                    <label>Transaction Detail</label>
                                    <textarea class="form-control" rows="3" name="transaction_note" id="transaction_note"></textarea>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-2">
                                    <button type="button" class="btn btn-success btn-block" id="travel_option_btn">Submit</button>
                                </div>
                            </div>

                        </form>
                    </div>


                </div>
            </div>
        </div>
        <!-- End Approve Model -->

        <div id="image_model" class="modal fade">
            <div class="modal-dialog modal-lg">
                <div class="modal-content" >
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h3>Expense Image</h3>
                    </div>
                    <div id="image_data" class="modal-body">

                    </div>
                </div>
            </div>
        </div>
        <!-- Voucher Image -->
        <div id="voucher_model" class="modal fade">
            <div class="modal-dialog modal-lg">
                <div class="modal-content" >
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h3>Voucher Image</h3>
                    </div>
                    <div id="voucher_data" class="modal-body">

                    </div>
                </div>
            </div>
        </div>
        <div id="work_note_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">Expense Note</h4>
                    </div>

                     <div class="modal-body" id="work_note_div">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!--  -->
        @endsection
        @section('script')
        <script>
            $("#travel_option_btn").on('click',function(){
                if($("#travel_option").valid()){
                    var check_ref_no = $("#check_ref_no").val();
                    var cheque_number = $("#cheque_number").val();
                    
                    var rtgs_ref_no = $("#rtgs_ref_no").val();
                    var rtgs_number = $("#rtgs_number").val();
                    
                    // if(check_ref_no != "" && cheque_number != "" || rtgs_ref_no != "" && rtgs_number != ""){
                            $("#travel_option").submit();
                        // }else{
                        //     $.toast({
                        //         heading: "Please select cheque or rtgs fields",
                        //         position: 'top-right',
                        //         loaderBg:'#ff6849',
                        //         icon: 'error',
                        //         hideAfter: 3500
                        //     });
                        // }
                }
            });
            $("#travel_option").validate({
                ignore: [],
            });
            $('#bank_id').select2();
            $('#cheque_number').select2();
            $('#rtgs_number').select2();

            $("#bank_id").change(function() {


                $('#cheque_number').select2('destroy');
                $('#cheque_number').empty();
                $('#cheque_number').select2();

                $('#rtgs_number').select2('destroy');
                $('#rtgs_number').empty();
                $('#rtgs_number').select2();

                bank_id = $(this).val();
                if (bank_id) {
                    $.ajax({
                        url: "{{ route('admin.banks_cheque_rtgs_reff_list') }}",
                        type: "POST",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            bank_id: bank_id,
                            company_id: $("#company_id").val(),
                        },
                        dataType: "JSON",
                        success: function(data) {
                            $("#check_ref_no").empty();
                            $("#check_ref_no").append(data.data.cheque_reff_list);
                            $("#rtgs_ref_no").empty();
                            $("#rtgs_ref_no").append(data.data.rtgs_reff_list);
                        }
                    });
                //================================== RTGS Number
                    /* rtgs_number  = $("#rtgs_no").val();
                $.ajax({
                    url: "{{ route('admin.get_bank_rtgs_list')}}",
                    type: 'POST',
                    headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                    data: {
                        bank_id :bank_id,
                        rtgs_no : rtgs_number
                    },
                    success: function(data, textStatus, jQxhr) {
                        $('#rtgs_number').empty();
                        $('#rtgs_number').append(data);
                        let rtgs_val = $('#rtgs_no').val();
                        $('#rtgs_number').val(rtgs_val);

                    },
                    error: function(jqXhr, textStatus, errorThrown) {
                        console.log(errorThrown);
                    }
                });  */

                } else {
                    $("#cheque_number").empty();
                }
            });

            $("#check_ref_no").on('change',function(){
                var check_ref_no = $(this).val();
                var bank_id = $("#bank_id").val();
                var company_id = $("#company_id").val();
                $.ajax({
                            url: "{{ route('admin.banks_cheque_list')}}",
                            type: 'POST',
                            headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                            data: {
                                bank_id :bank_id,
                                company_id :company_id,
                                check_ref_no : check_ref_no
                            },
                            success: function(data, textStatus, jQxhr) {
                                $('#cheque_number').empty();
                                $('#cheque_number').append(data);
                            },
                            error: function(jqXhr, textStatus, errorThrown) {
                                console.log(errorThrown);
                            }
                        });
            });

            $("#rtgs_ref_no").on('change',function(){
                var rtgs_ref_no = $(this).val();
                var bank_id = $("#bank_id").val();
                var company_id = $("#company_id").val();
                $.ajax({
                    url: "{{ route('admin.get_bank_rtgs_list')}}",
                    type: 'POST',
                    headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                    data: {
                        bank_id :bank_id,
                        company_id :company_id,
                        rtgs_ref_no : rtgs_ref_no
                    },
                    success: function(data, textStatus, jQxhr) {
                        $('#rtgs_number').empty();
                        $('#rtgs_number').append(data);
                        $("#rtgs_number").val($("#rtgs_number_edit").val());
                    },
                    error: function(jqXhr, textStatus, errorThrown) {
                        console.log(errorThrown);
                    }
                });
            });
            function approve_confirmByAccountant(id) {

                $("#travel_option").validate().resetForm();
                $('#bank_id').select2('destroy');
                $('#bank_id').empty();
                $('#bank_id').select2();


                $('#expense_id').val(id);
                $.ajax({
                    url: "{{ route('admin.get_expense') }}",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        id: id
                    },
                    dataType: "JSON",

                    success: function(data) {

                        //$('#rtgs_number').val(data.data.expense_records[0].rtgs_number);
                        $('#voucher_no').val(data.data.expense_records[0].voucher_no);
                        $('#transaction_note').val(data.data.expense_records[0].transaction_note);

                        $('#ch_no').val('');
                        $('#rtgs_no').val('');

                        $.each(data.data.bank_data, function(index, bank_obj) {
                            $("#bank_id").append('<option value="' + bank_obj.id + '">' + bank_obj.bank_name + '(' + bank_obj.ac_number + ')' + '</option>');
                        });
                        if (data.data.expense_records[0].bank_id) {
                            $('#ch_no').val(data.data.expense_records[0].cheque_number);
                            $('#rtgs_no').val(data.data.expense_records[0].rtgs_number);
                        }
                        $("#company_id").val(data.data.expense_records[0].company_id);
                        $('#bank_id').val(data.data.expense_records[0].bank_id); // Select the option with a value of '1'
                        $('#bank_id').trigger('change');

                    }

                });

            }

            //=====================================END Function 

            function set_image(src){
                $('#image_data').html('<img id="exp_img" src="' + src + '" class="" />');
                magnify("exp_img", 3);
            }
            function set_voucher_image(src){
                $('#voucher_data').html('<img id="exp_img" src="' + src + '" class="" />');
                magnify("exp_img", 3);
            }
            $(document).ready(function ($) {

                $('#select-all').click(function(event) {
                if (this.checked) {
                    // Iterate each checkbox
                    $(':checkbox').each(function() {
                    this.checked = true;
                    });
                    } else {
                    $(':checkbox').each(function() {
                    this.checked = false;
                    });
                }
                });
                // delegate calls to data-toggle="lightbox"
                $(document).delegate('*[data-toggle="lightbox"]:not([data-gallery="navigateTo"])', 'click', function (event) {
                    event.preventDefault();
                    return $(this).ekkoLightbox({
                    onShown: function () {
                    if (window.console) {
                    return console.log('Checking our the events huh?');
                    }
                    },
                            onNavigate: function (direction, itemIndex) {
                            if (window.console) {
                            return console.log('Navigating ' + direction + '. Current item: ' + itemIndex);
                            }
                            }
                    });
                });
                //Programatically call
                $('#open-image').click(function (e) {
                e.preventDefault();
                $(this).ekkoLightbox();
                });
                $('#open-youtube').click(function (e) {
                e.preventDefault();
                $(this).ekkoLightbox();
                });
                // navigateTo
                $(document).delegate('*[data-gallery="navigateTo"]', 'click', function (event) {
                event.preventDefault();
                var lb;
                return $(this).ekkoLightbox({
                onShown: function () {

                lb = this;
                $(lb.modal_content).on('click', '.modal-footer a', function (e) {

                e.preventDefault();
                lb.navigateTo(2);
                });
                }
                });
                });
            });
            $(document).ready(function () {
                $('#expense_approval_table').DataTable({
                        dom: 'lBfrtip',
                        buttons: ['excel'],
                        stateSave: true,
						"lengthMenu": [[10, 25, 50, 100,500,1000,2000,-1], [10, 25, 50, 100,500,1000,2000]],
                        "columnDefs": [ {
                        "targets": [0],
                                "orderable": false
                        } ],
                        order:[[9, 'DESC']]});
                $('#all_employee_expense_list_table').DataTable({
                    dom: 'lBfrtip',
                    buttons: ['excel'],
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    "stateSave": true,
                    "order": [[1, "DESC"]],
					"lengthMenu": [[10, 25, 50, 100,500,1000,2000,-1], [10, 25, 50, 100,500,1000,2000]],
                    "ajax": {
                        url: "<?php echo route('admin.all_employee_expense_list_ajax'); ?>",
                        type: "GET",
                    },
                    "columns": [
                        {"taregts": 0, 'data': 'expense_code'},
                        {"taregts": 1, 'data': 'name'},
                        {"taregts": 2, 'data': 'company_name'},
                        {"taregts": 3, "searchable": true, "render":function(data,type,row){
                                var id = row.id;
                                var out="";
                                if (row.client_name != null) {
                                    if (row.client_name == 'Other Client') {
                                        out+= row.client_name;
                                    } else {
                                        out+= row.client_name + "(" + row.location + ")";
                                    }
                                } else {
                                    out+= 'NA';
                                }
                              return out; 
                            }
                        },
                        {"taregts": 4, 'data': 'project_name'},
                        {"taregts": 5, "searchable": true, "render":function(data,type,row){
                                if (row.other_project) {
                                    return row.other_project;
                                } else {
                                    return 'NA';
                                }
                            }
                        },
                        {"taregts": 6, 'data': 'site_name'},
                        {"taregts": 7, 'data': 'expense_main_category'},
                        {"taregts": 8, 'data': 'category_name'},
                        {"taregts": 9, 'data': 'title'},
                        {"taregts": 10, 'data': 'merchant_name'},
                        {"taregts": 11, "render": function(data, type, row) {
                                if (row.amount) {
                                    return '<p class="text-primary">'+ row.amount +'</p>';
                                }
                            }
                        },
                        {"taregts": 12, 'data': 'bill_number'},
                        {"taregts": 13, "searchable": true, "render":function(data,type,row){
                                return '<p class="text-primary">'+ moment(row.expense_date).format("DD-MM-YYYY") +'</p>';
                            }
                        },
                        {"taregts": 14, "searchable": false, "orderable": false, "render":function(data,type,row){
                                return'<textarea style="display: none;" name="" id="comment_'+row.id+'">'+row.comment+'</textarea><a href="javascript:void(0)" title="View Note" data-toggle="modal" data-target="#work_note_modal"  class="btn btn-rounded btn-info" onclick="show_comment('+row.id+')"><i class="fa fa-eye"></i></a>';
                            }
                        },
                        {"taregts": 15, "searchable": false, "orderable": false,"render":function(data,type,row){
                                var out = '';
                                if (row.expense_image != null) {
                                    var img = row.expense_image;
                                    var baseURL = img.replace("public/","");
                                    var url=  "<?php echo url('/storage/');?>"+"/"+baseURL;
                                        out+= '<div class="magnify-image">'+
                                            '<img width="100px" height="100px" src="'+ url +'" alt="gallery" onclick="set_image(`'+  url +'`);" data-toggle="modal" data-target="#image_model" class="all studio"/>'+ 
                                        '</div>';
                                        out+= '&nbsp;<div class="text-center">'+
                                            '<a href="'+ url +'" title="Download Image" download><i class="fa fa-cloud-download fa-lg"></i></a>'+
                                        '</div>';
                                }
                                return out;
                            }
                        },
                        {"taregts": 16, "searchable": true, "render":function(data,type,row){
                                if (row.bank_name) {
                                    return row.bank_name + "(" + row.ac_number + ")";
                                } else {
                                    return 'NA';
                                }
                            }
                        },
                        {"taregts": 17, "searchable": true, "render":function(data,type,row){
                                if (row.ch_no) {
                                    return row.ch_no;
                                } else {
                                    return 'NA';
                                }
                            }
                        },
                        {"taregts": 18, "searchable": true, "render":function(data,type,row){
                                if (row.rtgs_no) {
                                    return row.rtgs_no;
                                } else {
                                    return 'NA';
                                }
                            }
                        },
                        {"taregts": 19, "searchable": true, "render":function(data,type,row){
                                if (row.voucher_no) {
                                    return row.voucher_no;
                                } else {
                                    return 'NA';
                                }
                            }
                        },
                        {"taregts": 20, "searchable": false, "orderable": false,"render":function(data,type,row){
                                var out = '';
                                if (row.voucher_image != null) {
                                    var img = row.voucher_image;
                                    var baseURL = img.replace("public/","");
                                    var url=  "<?php echo url('/storage/');?>"+"/"+baseURL;
                                        out+= '<div class="magnify-image">'+
                                            '<img width="100px" height="100px" src="'+ url +'" alt="gallery" onclick="set_voucher_image(`'+  url +'`);" data-toggle="modal" data-target="#voucher_model" class="all studio"/>'+ 
                                        '</div>';
                                        out+= '&nbsp;<div class="text-center">'+
                                            '<a href="'+ url +'" title="Download Image" download><i class="fa fa-cloud-download fa-lg"></i></a>'+
                                        '</div>';
                                }
                                return out;
                            }
                        },
                        {"taregts": 21, "searchable": true, "render":function(data,type,row){
                                if (row.transaction_note) {
                                    return row.transaction_note;
                                } else {
                                    return 'NA';
                                }
                            }
                        },
                        {
                            "taregts": 22,
                            "render": function(data, type, row) {
                                var out = '';
                                if (row.first_approval_status == 'Pending') {
                                    out += '<b class="text-warning">Pending</b>';
                                } else if (row.first_approval_status == 'Approved') {
                                    out += '<b class="text-success">Approved</b>';
                                } else {
                                    out += '<b class="text-danger">Rejected</b>';
                                }
                                return out;
                            }
                        },
                        {"taregts": 23, 'data': 'first_approval_datetime',
                            "render": function(data, type, row) {
                                var out = '';
                                if (row.first_approval_datetime) {
                                    out += moment(row.first_approval_datetime).format("DD-MM-YYYY h:mm A");
                                } else {
                                    out += 'N/A';
                                }
                                return out;
                            }
                        },
                        {
                            "taregts": 24,
                            "render": function(data, type, row) {
                                var out = '';
                                if (row.second_approval_status == 'Pending') {
                                    out += '<b class="text-warning">Pending</b>';
                                } else if (row.second_approval_status == 'Approved') {
                                    out += '<b class="text-success">Approved</b>';
                                } else {
                                    out += '<b class="text-danger">Rejected</b>';
                                }
                                return out;
                            }
                        },
                        {"taregts": 25, 'data': 'secound_approval_datetime',
                            "render": function(data, type, row) {
                                var out = '';
                                if (row.secound_approval_datetime) {
                                    out += moment(row.secound_approval_datetime).format("DD-MM-YYYY h:mm A");
                                } else {
                                    out += 'N/A';
                                }
                                return out;
                            }
                        },
                        {
                            "taregts": 26,
                            "render": function(data, type, row) {
                                var out = '';
                                if (row.third_approval_status == 'Pending') {
                                    out += '<b class="text-warning">Pending</b>';
                                } else if (row.third_approval_status == 'Approved') {
                                    out += '<b class="text-success">Approved</b>';
                                } else {
                                    out += '<b class="text-danger">Rejected</b>';
                                }
                                return out;
                            }
                        },
                        {"taregts": 27, 'data': 'third_approval_datetime',
                            "render": function(data, type, row) {
                                var out = '';
                                if (row.third_approval_datetime) {
                                    out += moment(row.third_approval_datetime).format("DD-MM-YYYY h:mm A");
                                } else {
                                    out += 'N/A';
                                }
                                return out;
                            }
                        },
                        {
                            "taregts": 28,
                            "render": function(data, type, row) {
                                var out = '';
                                if (row.forth_approval_status == 'Pending') {
                                    out += '<b class="text-warning">Pending</b>';
                                } else if (row.forth_approval_status == 'Approved') {
                                    out += '<b class="text-success">Approved</b>';
                                } else {
                                    out += '<b class="text-danger">Rejected</b>';
                                }
                                return out;
                            }
                        },
                        {"taregts": 29, 'data': 'fourth_approval_datetime',
                            "render": function(data, type, row) {
                                var out = '';
                                if (row.fourth_approval_datetime) {
                                    out += moment(row.fourth_approval_datetime).format("DD-MM-YYYY h:mm A");
                                } else {
                                    out += 'N/A';
                                }
                                return out;
                            }
                        },
                        {
                            "taregts": 30,
                            "render": function(data, type, row) {
                                var out = '';
                                if (row.fifth_approval_status == 'Pending') {
                                    out += '<b class="text-warning">Pending</b>';
                                } else if (row.fifth_approval_status == 'Approved') {
                                    out += '<b class="text-success">Approved</b>';
                                } else {
                                    out += '<b class="text-danger">Rejected</b>';
                                }
                                return out;
                            }
                        },
                        {"taregts": 31, 'data': 'fifth_approval_datetime',
                            "render": function(data, type, row) {
                                var out = '';
                                if (row.fifth_approval_datetime) {
                                    out += moment(row.fifth_approval_datetime).format("DD-MM-YYYY h:mm A");
                                } else {
                                    out += 'N/A';
                                }
                                return out;
                            }
                        },
                        {
                            "taregts": 32,
                            "render": function(data, type, row) {
                                var out = '';
                                if (row.status == 'Pending') {
                                    out += '<b class="text-warning">Pending</b>';
                                } else if (row.status == 'Approved') {
                                    out += '<b class="text-success">Approved</b>';
                                } else {
                                    out += '<b class="text-danger">Rejected</b>';
                                }
                                return out;
                            }
                        },
                        {"taregts": 33, "searchable": true, "render":function(data,type,row){
                                if (row.acc_user_name) {
                                    return row.acc_user_name + "(" + row.acc_email + ")";
                                } else {
                                    return 'NA';
                                }
                            }
                        }
                       
                    ],
                    "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                        if (aData.voucher_repeat == "1") {
                            $('td', nRow).addClass('error');
                        }
                    }
                });
            })


                    function delete_confirm(e) {
                        swal({
                        title: "Are you sure you want to delete employee expense ?",
                                //text: "You want to change status of admin user.",
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonColor: "#DD6B55",
                                confirmButtonText: "Yes",
                                closeOnConfirm: false
                        }, function () {
                        window.location.href = $(e).attr('data-href');
                        });
                    }

                    function paid_confirm(e) {
                        swal({
                        title: "Are you sure you want to paid employee expense ?",
                                //text: "You want to change status of admin user.",
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonColor: "#DD6B55",
                                confirmButtonText: "Yes",
                                closeOnConfirm: false
                        }, function () {
                        window.location.href = $(e).attr('data-href');
                        });
                    }

                    function approve_confirm(e) {
                        swal({
                        title: "Are you sure you want to approve ?",
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonColor: "#DD6B55",
                                confirmButtonText: "Yes",
                                closeOnConfirm: false
                        }, function () {
                        window.location.href = $(e).attr('data-href');
                        });
                    }
                    function reject_employee_expenses(route)
                    {
                        $('#model_data').html('');
                        $.ajax({
                        url: route,
                                type: "GET",
                                dataType: "html",
                                catch : false,
                                success: function (data) {
                                $('#model_data').append(data);
                                }
                        });
                    }

        function show_comment(id){
            if($('#comment_' + id).val()){
                $('#work_note_div').html($('#comment_' + id).val());
            }else{
                $('#work_note_div').html("N/A");
            }
        }
        </script>
        @endsection