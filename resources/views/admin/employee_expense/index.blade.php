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
                <?php
                $role = explode(',', $access_rule);
                if (in_array(3, $role)) {
                    ?>
                    <a href="{{ route('admin.add_employee_expense') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Expense</a>
                    <?php
                }
                ?>
                <p class="text-muted m-b-30"></p>
                <br>
                <b class="error">Approval Flow: {{implode(' -> ',config('constants.EMP_EXPENSE_APPROVAL'))}}</b>
                <p class="text-muted m-b-30"></p>
                <br>

                <div class="table-responsive">
                    <table id="expense_table" class="table table-striped">
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
                                <th>Voucher Number</th>
                                <th>Voucher Image</th>
                                <th>Expense Date</th>
                                <th>Expense Note(Comment)</th>
                                <th>Expense Image</th>
                                <th>Approver Accountant</th>
                                <th>HR Status</th>
                                <th>First Approval</th>
                                <!-- recently date and time added fields  -->
                                <th>Assistant Status</th>
                                <th>Secound Approval</th>
                                <th>Admin Status</th>
                                <th>Third Approval</th>
                                <th>Super Admin Status</th>
                                <th>Fourth Approval</th>
                                <th>Accountant Status</th>
                                <th>Fifth Approval</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(count($employee_expense_list)>0)
                                    @foreach($employee_expense_list as $expense_category)
                                    <tr @if($expense_category->voucher_repeat === 1) class="error" @endif>
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
                                        NA
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
                                            <div id="gallery-content">
                                                <div id="gallery-content-center">
                                                    <img width="100px" height="100px" src="{{asset('storage/' . str_replace('public/', '', $expense_category->expense_image))}}" alt="gallery" onclick="set_image('{{asset('storage/' . str_replace('public/', '', $expense_category->expense_image))}}');" data-toggle="modal" data-target="#image_model" class="all studio"/> 
                                                
                                                </div>
                                            </div>
                                            <br>
                                            <div class="text-center">
                                               <a href="{{asset('storage/' . str_replace('public/', '', $expense_category->expense_image))}}" title="Download Image" download><i class="fa fa-cloud-download fa-lg" ></i></a>
                                            </div>
                                        @endif
                                        </td>
                                        <td>
                                            @if($expense_category->acc_user_name)
                                            {{ $expense_category->acc_user_name.' ('.$expense_category->acc_email.')' }}
                                            @else
                                            NA
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
                                                <!-- edited aproval date -->
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
                                                <!-- edited aproval date -->
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
                                                <!-- edited aproval date -->
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
                                                <!-- edited aproval date -->
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
                                                <!-- edited aproval date -->
                                        @if($expense_category->status=="Pending")
                                        <td class="text-warning">{{$expense_category->status }}</td>
                                        @elseif($expense_category->status=="Approved")
                                        <td class="text-success">{{$expense_category->status }}</td>
                                        @elseif($expense_category->status=="Rejected")
                                        <td >
                                            <a href="#" class="btn btn-danger" onclick="set_reject_reason('{{ $expense_category->reject_reason }}')" data-toggle="modal" data-target="#reject_model">{{$expense_category->status }}</a>
                                        </td>
                                        @endif

                                        <td>
                                            <a href="{{ route('admin.edit_employee_expense',['id'=>$expense_category->id]) }}" class="btn btn-rounded btn-primary"><i class="fa fa-edit"></i></a>
                                            <a onclick="delete_confirm(this);" class="btn btn-danger btn-rounded" href="#" data-href="{{ route('admin.delete_employee_expense',['id'=>$expense_category->id]) }}" title="Delete"><i class="fa fa-trash"></i></a>
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





        <div id="reject_model" class="modal fade">
            <div class="modal-dialog modal-lg">
                <div class="modal-content" >
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h3>Rejection Reason</h3>
                    </div>
                    <div  class="modal-body ">
                        <div id="model_data" class="img-magnifier-container">

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="image_model" class="modal fade">
            <div class="modal-dialog  modal-lg">
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
            function set_image(src){
            $('#image_data').html('<img id="exp_img" src="' + src + '" class="" />');
            magnify("exp_img", 3);
            }
            // 
            function set_voucher_image(src){
                $('#voucher_data').html('<img id="exp_img" src="' + src + '" class="" />');
                magnify("exp_img", 3);
            }
            $(document).ready(function ($) {

            $('#image_model').on('hidden.bs.modal', function () {
            $('#image_data').html('');
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
            function set_reject_reason(reason){
            $('#model_data').html('');
            $('#model_data').html(reason);
            }

            $('#expense_table').DataTable({
                dom: 'lBfrtip',
                buttons: ['excel'],
                stateSave: true, order:[[9, 'DESC']]
                });
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
        function show_comment(id){
            if($('#comment_' + id).val()){
                $('#work_note_div').html($('#comment_' + id).val());
            }else{
                $('#work_note_div').html("N/A");
            }
        }
        </script>
        @endsection