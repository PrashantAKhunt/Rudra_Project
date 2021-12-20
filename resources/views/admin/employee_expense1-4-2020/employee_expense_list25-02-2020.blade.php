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
                                    <th><input type="checkbox"  name="expense_approve_list[]" id="select-all" onClick="toggle(this)" /></th>
                                    <th>Expense Code</th>
                                    <th>User Name</th>
                                    <th>Company</th>
                                    <th>Project</th>
                                    <th>Other Project Detail</th>
                                    <th>Expense Type</th>
                                    <th>Expense Category</th>
                                    <th>Title</th>
                                    <th>Merchant Name</th>
                                    <th>Amount</th>
                                    <th>Bill Number</th>
                                    <th>Voucher Number</th>
                                    <th>Expense Date</th>
                                    <th>Expense Image</th>
                                    <th>Your Approval</th>
                                    <th>Status</th>
                                    <th>Approve</th>
                                    <th>Reject</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($employee_expense_list->count()>0)
                                @foreach($employee_expense_list as $expense_category)
                                <tr>
                                    <td>
                                        <input type="checkbox" value="{{$expense_category->id }}" name="expense_approve_list[]" />
                                    </td>
                                    <td>{{$expense_category->expense_code }}</td>
                                    <td>{{$expense_category->name }}</td>
                                    <td>{{$expense_category->company_name}}</td>
                                    <td>{{$expense_category->project_name}}</td>
                                    @if($expense_category->other_project)
                                    <td>{{$expense_category->other_project }}</td>
                                    @else
                                    <td>NA</td>
                                    @endif
                                    <td>{{$expense_category->expense_main_category }}</td>
                                    <td>{{$expense_category->category_name }}</td>
                                    <td>{{$expense_category->title }}</td>
                                    <td>{{$expense_category->merchant_name }}</td>
                                    <td>Rs{{$expense_category->amount }}</td>
                                    <td>{{$expense_category->bill_number }}</td>
                                     <td>{{$expense_category->voucher_no }}</td>
                                    <td>{{$expense_category->expense_date }}</td>
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
                                            <a href="#" data-href="<?php echo url('approve_employee_expense/' . $expense_category->id) ?>" onclick="approve_confirm(this);" class="btn btn-success btn-rounded" title="Change Status"><i class="fa fa-check"></i></a>
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
                        <button type="submit" class="btn btn-success">Approve Expense</button>
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
                                <th>Project</th>
                                <th>Other Project Detail</th>
                                <th>Expense Type</th>
                                <th>Expense Category</th>
                                <th>Title</th>
                                <th>Merchant Name</th>
                                <th>Amount</th>
                                <th>Bill Number</th>
                                <th>Voucher Number</th>
                                <th>Expense Date</th>
                                <th>Expense Image</th>
                                <th>Status</th>
                                <th>Approver Accountant</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($all_employee_expense_list->count()>0)
                            @foreach($all_employee_expense_list as $expense_category)
                            <tr>
                                <td>{{$expense_category->expense_code }}</td>
                                <td>{{$expense_category->name }}</td>
                                <td>{{$expense_category->company_name}}</td>
                                <td>{{$expense_category->project_name}}</td>
                                @if($expense_category->other_project)
                                <td>{{$expense_category->other_project }}</td>
                                @else
                                <td>NA</td>
                                @endif
                                <td>{{$expense_category->expense_main_category }}</td>
                                <td>{{$expense_category->category_name }}</td>
                                <td>{{$expense_category->title }}</td>
                                <td>{{$expense_category->merchant_name }}</td>
                                <td>Rs{{$expense_category->amount }}</td>
                                <td>{{$expense_category->bill_number }}</td>
                                <td>{{$expense_category->voucher_no }}</td>
                                <td>{{$expense_category->expense_date }}</td>
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
                                <td>{{$expense_category->status }}</td>
                                <td>
                                    @if($expense_category->acc_user_name)
                                    {{ $expense_category->acc_user_name.' ('.$expense_category->acc_email.')' }}
                                    @else
                                    NA
                                    @endif
                                </td>
                            </tr>
                            @endforeach

                            @endif
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
        @endsection
        @section('script')
        <script>
            function set_image(src){
            $('#image_data').html('<img id="exp_img" src="' + src + '" class="" />');
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
            $('#expense_approval_table').DataTable({stateSave: true,
                    "columnDefs": [ {
                    "targets": [0],
                            "orderable": false
                    } ],
                    order:[[9, 'DESC']]});
            $('#all_employee_expense_list_table').DataTable({
            dom: 'Bfrtip',
                    buttons: [
                            'csv', 'excel', 'pdf', 'print'
                    ], stateSave: true, order:[[9, 'DESC']]
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
        </script>
        @endsection