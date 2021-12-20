@extends('layouts.admin_app')

@section('content')
<?php

use Illuminate\Support\Facades\Config; ?>
<style>
    .scrollme {
        overflow-x: auto;
    }
</style>
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
    </div>
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
                <b class="error">Approval Flow: {{implode(' -> ',config('constants.BANK_PAYMENT_APPROVAL'))}}</b>
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="table-responsive">
                    <table id="policy_table" class="table table-striped">
                        <thead>
                            <tr>
                            <th>Payment Option</th>
                            <th>Budget Sheet Number</th>
                                <th>Entry Code</th>
                                <th>User Name</th>
                                <th>Company Name</th>
                                <th>Client Name</th>
                                <th>Project Name</th>
                                <th>Other Project Detail</th>
                                <th>Site Name</th>
                                <th>Vendor Name</th>
                                <th>Work Detail</th>
                                <th>Vendor/Party Bank Details</th>
                                <th>Bank Name</th>
                                <th>Cheque Number</th>
                                <th>Total Amount</th>
                                <th>Amount</th>
                                <th>Entry Completed</th>
                                <th>Accountant Status</th>
                                <th>Admin Status</th>
                                <th>Super Admin Status</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <!--            <div id="rejectPaymentModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        </div>
                        <div class="modal-body" id="userTable">
                            <div class="form-group ">
                                <label>Reject Note</label>
                                <textarea class="form-control valid" rows="6" name="note" id="note" spellcheck="false"></textarea>
                                <input type="hidden" name="reject_url" id="reject_url">
                            </div>
                        </div>
                        <div class="col-md-12 pull-left">
                            <div class="clearfix"></div>
                            <br>
                            <button type="button" onclick="RejectedPayment('Rejected')" data-dismiss="modal" class="btn btn-danger">Reject</button>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                    
                </div>
               
            </div>          -->
        </div>
        <div class="col-md-12 col-lg-12 col-sm-12">


            <div id="rejectPaymentModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form action="{{ route('admin.reject_bank_payment') }}" method="POST" id="reject_note_frm">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            </div>
                            <div class="modal-body" id="userTable">
                                <div class="form-group ">
                                    <label>Reject Note</label>
                                    <input type="hidden" name="paymentid" id="paymentid" value="" />
                                    <textarea class="form-control valid" rows="6" name="note" id="note" spellcheck="false"></textarea>

                                </div>
                            </div>
                            <div class="col-md-12 pull-left">
                                <div class="clearfix"></div>
                                <br>
                                <button type="submit" class="btn btn-danger">Reject</button>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </form>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>

            <div id="approvePaymentModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form action="{{ route('admin.approve_bank_payment') }}" method="POST" id="approve_note_frm">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            </div>
                            <div class="modal-body" id="userTable">
                                <div class="form-group ">
                                    <label>Approve Note</label>
                                    <input type="hidden" name="approve_paymentid" id="approve_paymentid" value="" />
                                    <textarea class="form-control valid" rows="6" name="approve_note" id="approve_note" spellcheck="false"></textarea>

                                </div>
                            </div>
                            <div class="col-md-12 pull-left">
                                <div class="clearfix"></div>
                                <br>
                                <button type="submit" class="btn btn-danger">Approve</button>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </form>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>

            <div id="approval_note_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">

                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group ">
                                <label>First Approval Note</label>
                                <p id="approval_note1"></p>
                            </div>
                            <div class="form-group ">
                                <label>Second Approval Note</label>
                                <p id="approval_note2"></p>
                            </div>
                            <div class="form-group ">
                                <label>Third Approval Note</label>
                                <p id="approval_note3"></p>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                        </div>
                    </div>

                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>

            <div class="white-box">
                <div class="row bg-title">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <h4 class="page-title">All Bank Payment Approval History</h4>
                    </div>
                    <!-- start -->
                    <!-- jayram desai 193-225 -->
                    <div class="col-md-12 col-lg-12 col-sm-12">
                        <div class="panel panel-info">
                            <div class="panel-wrapper collapse in" aria-expanded="true">
                                <div class="panel-body">
                                    <form action="{{ route('admin.payment_list') }}" id="payment_list_frm" method="post" class="form-material" accept-charset="utf-8">
                                        @csrf
                                        <div class="form-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="col-sm-3 control-label">Date<label class="serror"></label> </label>
                                                        <div class="col-sm-9">
                                                            <input type="text" class="form-control timeseconds shawCalRanges" name="date" id="date" value="<?php echo !empty($date) ? $date : "" ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-actions">
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="col-md-offset-3 col-md-9">
                                                            <button type="submit" class="btn btn-success"> <i class="fa fa-check"></i> Search</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End -->
                </div>
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="table-responsive">
                    <table id="all_policy_table" class="table table-striped">
                        <thead>
                            <tr>
                            <th>Payment Option</th>
                            <th>Budget Sheet Number</th>
                                <th>Entry Code</th>
                                <th>User Name</th>
                                <th>Company Name</th>
                                <th>Client Name</th>
                                <th>Project Name</th>
                                <th>Other Project Detail</th>
                                <th>Site Name</th>
                                <th>Vendor Name</th>
                                <th>Work Detail</th>
                                <th>Vendor/Party Bank Details</th>
                                <th>Bank Name</th>
                                <th>Cheque Number</th>
                                <th>RTGS Number</th>
                                <th>Voucher Number</th>
                                <th>Transaction Detail</th>
                                <th>Total Amount</th>
                                <th>Amount</th>
                                <th>Entry Completed</th>
                                <th>Accountant Status</th>
                                <th>Admin Status</th>
                                <th>Super Admin Status</th>
                                <th>Status</th>
                                <th>Payment File</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($bank_payment_approval_history)) { ?>
                                <?php foreach ($bank_payment_approval_history as $key => $aprovals) { ?>
                                    <tr>
                                    <td>{{ $aprovals->payment_options }}</td>
                                        <td>@if($aprovals->budhet_sheet_no)
                                        {{ $aprovals->budhet_sheet_no }}
                                        @endif
                                        </td>
                                        <td>{{ $aprovals->entry_code }}</td>
                                        <td>{{ $aprovals->user_name }}</td>
                                        <td>{{ $aprovals->company_name }}</td>
                                        <td>
                                        @if($aprovals->client_name)
                                        @if($aprovals->client_name == 'Other Client')
                                        {{ $aprovals->client_name }}
                                            @else
                                            {{ $aprovals->client_name." (".$aprovals->location.")" }}
                                            @endif

                                            @else
                                            N/A
                                            @endif
                                        </td>
                                        <td>{{ $aprovals->project_name }}</td>
                                        <td>{{ $aprovals->other_project_detail }}</td>
                                        <td>{{ $aprovals->site_name}}</td>
                                        <td>{{ $aprovals->vendor_name }}</td>
                                        <td>{{ $aprovals->note }}</td>
                                        <td>
                                            @if($aprovals->vendor_bank_name)
                                            {{ $aprovals->vendor_bank_name." (".$aprovals->ac_number.")" }}
                                            @else
                                            N/A
                                            @endif
                                        </td>
                                        <td>{{ $aprovals->bank_name }}</td>
                                        <td>{{ $aprovals->ch_no }}</td>

                                        <td>{{ $aprovals->rtgs_no }}</td>
                                        <td>{{ $aprovals->voucher_no }}</td>
                                        <td>{{ $aprovals->transaction_note }}</td>

                                        <td>{{ $aprovals->total_amount }}</td>
                                        <td>{{ $aprovals->amount }}</td>
                                        <td>
                                            @if($aprovals->main_entry)
                                            {{ $aprovals->entry_completed }}

                                            @endif
                                        </td>
                                        <td @if($aprovals->first_approval_status=="Approved") class="text-success" @elseif($aprovals->first_approval_status=="Pending") class="text-warning" @else class="text-danger" @endif>
                                            {{ $aprovals->first_approval_status }}
                                            <br>
                                            @if($aprovals->first_approval_datetime)
                                            {{ date('d-m-Y h:i:s A',strtotime($aprovals->first_approval_datetime)) }}
                                            @endif
                                        </td>
                                        <td @if($aprovals->second_approval_status=="Approved") class="text-success" @elseif($aprovals->second_approval_status=="Pending") class="text-warning" @else class="text-danger" @endif>
                                            {{ $aprovals->second_approval_status }}
                                            <br>
                                            @if($aprovals->second_approval_datetime)
                                            {{ date('d-m-Y h:i:s A',strtotime($aprovals->second_approval_datetime)) }}
                                            @endif
                                        </td>
                                        <td @if($aprovals->third_approval_status=="Approved") class="text-success" @elseif($aprovals->third_approval_status=="Pending") class="text-warning" @else class="text-danger" @endif>
                                            {{ $aprovals->third_approval_status }}
                                            <br>
                                            @if($aprovals->third_approval_datetime)
                                            {{ date('d-m-Y h:i:s A',strtotime($aprovals->third_approval_datetime)) }}
                                            @endif
                                        </td>
                                        <td>@if($aprovals->status=="Pending")
                                            <b class="text-warning">{{ $aprovals->status }}</b>
                                            @elseif($aprovals->status=="Approved")
                                            <b class="text-success">{{ $aprovals->status }}</b>
                                            @elseif($aprovals->status=="Rejected")
                                            <input type="hidden" name="reject_note_{{$aprovals->id}}" id="reject_note_{{$aprovals->id}}" value="{{ $aprovals->reject_note }}" />
                                            <a class="btn btn-danger" href="#" onclick="show_reject_note({{$aprovals->id}})" data-toggle="modal" data-target="#reject_note_modal">{{ $aprovals->status }}</a>
                                            @endif

                                        </td>
                                        <td>
                                            <button title="Approval Notes" type="button" onclick="get_approval_note({{ $aprovals->id }})" data-toggle="modal" data-target="#approval_note_modal" class="btn btn-primary btn-rounded"><i class="fa fa-file-text"></i></button>
                                            @if($aprovals->payment_file)
                                            <a download="" title="Download Bank Payment File" href="{{asset('storage/'.str_replace('public/','',$aprovals->payment_file))}}" class="btn btn-rounded btn-primary"><i class="fa fa-download"></i></a>
                                            @endif
                                            @if($aprovals->invoice_file)
                                            <a download="" title="Download Invoice File" href="{{asset('storage/'.str_replace('public/','',$aprovals->invoice_file))}}" class="btn btn-rounded btn-primary"><i class="fa fa-download"></i></a>
                                            @endif
                                            &nbsp;
                                            <a href="#" onclick="get_bank_payment_files({{ $aprovals->id }});" title="View Files" id="showFiles" data-target="#bankPaymentFilesModel" data-toggle="modal" class="btn btn-primary btn-rounded"><i class="fa fa-eye"></i></a>

                                        </td>
                                        <td>
                                        @if(config::get('constants.ACCOUNT_ROLE') == Auth::user()->role && $aprovals->status=="Approved")
                                            <a href="#Approvemodel" data-toggle="modal" onclick="approve_confirmByAccountant('<?php echo $aprovals->id  ?>');" class="btn btn-success btn-rounded"><i class="fa fa-check"></i></a>
                                            @endif
                                        </td>

                                    </tr>
                            <?php
                                }
                            }
                            ?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>

<!-- Approve Model -->
<div id="Approvemodel" class="modal fade">
                <div class="modal-dialog">
                    <div class="modal-content" id="model_data">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h3 class="panel-title">Approve Bank Payment</h3>
                        </div>
                        <div class="modal-body">
                            <form method="post" action="{{ route('admin.approve_bankPaymentByAccountant') }}">
                                @csrf
                                <input type="hidden" name="id" id="bank_app_id" value="">
                                <input type="hidden" name="ch_no" id="ch_no" value="">
                                <input type="hidden" name="rtgs_no" id="rtgs_no" value="">
                                <div class="row">
                                <div class="form-group ">
                                <label>Bank Name</label>
                                
                                    <select class="form-control" id="bank_id" name="bank_id">
                                        <option value="">Select bank</option>
                                    </select>
                                
                            </div>
                                    <div class="form-group ">
                                        <label>Cheque Number</label>
                                        <select class="form-control" name="cheque_number" id="cheque_number">
                                    <option value="">Select cheque</option>
                                </select>
                                    </div>
                                    <div class="form-group ">
                                <label>Cheque Issue Date</label>
                                <input type="text" class="form-control" required name="issue_date" id="issue_date" value="" />
                            </div>

                                    <div class="form-group ">
                                        <label>RTGS Number</label>
                                       <select class="form-control" name="rtgs_number" id="rtgs_number">
                                           <option value="">Select RTGS</option>

                                       </select>
                                    </div>
                                    <div class="form-group ">
                                        <label>Voucher Number</label>
                                        <input type="text" class="form-control" name="voucher_no" id="voucher_no" value="" />
                                    </div>
                                    <div class="form-group ">
                                        <label>Transaction Detail</label>
                                        <textarea class="form-control" required rows="3" name="transaction_note" id="transaction_note"></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-2">
                                        <button type="submit" name="submit" class="btn btn-success btn-block">Submit</button>
                                    </div>
                                </div>

                            </form>
                        </div>


                    </div>
                </div>
            </div>
            <!-- End Approve Model -->
        <div id="reject_note_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">Reject Reason</h4>
                    </div>
                    <div class="modal-body" id="reject_note_div">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>

        <div id="bankPaymentFilesModel" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">Files</h4>
                    </div>

                    <br>
                    <br>

                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Download</th>
                                <th>Filename</th>
                                <th id="del_action">Action</th>

                            </tr>
                        </thead>
                        <tbody id="file_table">

                        </tbody>
                    </table>

                    <!-- <div class="modal-body" id="files">
                    </div> -->

                </div>
                <!-- /.modal-content -->
            </div>
        </div>

        <div class="modal fade" id="payment_view" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Bank Payment Approval Detail</h4>
                    </div>
                    <div class="modal-body">
                        <div class="scrollme">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Entry Code</th>
                                        <th>Username</th>
                                        <th>Company</th>
                                        <th>Client Name</th>
                                        <th>Project</th>
                                        <th>Other Project Detail</th>
                                        <th>Site Name</th>
                                        <th>Vendor</th>
                                        <th>Work Detail</th>
                                        <th>Vendor/Party Bank Detail</th>
                                        <th>Bank</th>
                                        <th>Cheque Number</th>
                                        <th>Accountant Status</th>
                                        <th>Admin Status</th>
                                        <th>Super Admin Status</th>
                                        <th>Status</th>
                                        <th>Total Amount</th>
                                        <th>Paid Amount</th>
                                        <th>Remaining Amount</th>
                                        <th>Entry Completed</th>

                                    </tr>
                                </thead>
                                <tbody id="payment_list">

                                </tbody>
                            </table>
                        </div>


                    </div>
                </div>
            </div>
        </div>
        @endsection

        @section('script')
        <script>

$("#bank_id").change(function(e,objectData) {
            
            var bank_id = $("#bank_id").val();
            ch_no = $("#ch_no").val();

            $.ajax({
                url: "{{ route('admin.get_bank_cheque_list')}}",
                type: 'POST',
                headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                data: {
                    bank_id :bank_id,
                    ch_no : ch_no
                },
                success: function(data, textStatus, jQxhr) {
                    $('#cheque_number').empty();
                    $('#cheque_number').append(data);
                    if (objectData) {
                        $('#cheque_number').val(objectData.ch_no);  
                    }
                },
                error: function(jqXhr, textStatus, errorThrown) {
                    console.log(errorThrown);
                }
            });

    //=============================== RTGS NUMBER    
    rtgs_number  = $("#rtgs_no").val();
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
                    if (objectData) {
                        $('#rtgs_number').val(objectData.rtgs_no);  
                    }
                },
                error: function(jqXhr, textStatus, errorThrown) {
                    console.log(errorThrown);
                }
            });    

});

function approve_confirmByAccountant(id) {
                $('#bank_app_id').val(id);
                $('#ch_no').val('');
                $('#rtgs_no').val('');
                $.ajax({

                    url: "{{ route('admin.get_bankApproval') }}",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        id: id
                    },
                    dataType: "JSON",
                    success: function(data) {

                        $('#bank_id').empty();
                       
                        $('#bank_id').append(data.data.bank_list);
                        $('#bank_id').val(data.data.bank_records[0].bank_id);
                        $('#ch_no').val(data.data.bank_records[0].cheque_number);
                        $('#rtgs_no').val(data.data.bank_records[0].rtgs_number);
                        if (data.data.issue_date) {
                            let issue_date = data.data.issue_date ? moment(data.data.issue_date).format("DD-MM-YYYY") : '';
                            $('#issue_date').val(issue_date);
                        }
                        

                            if (data.data.cheque_id) {
                                $('#bank_id').trigger('change', [{
                                    'ch_id': data.data.cheque_id,
                                    'ch_no' : data.data.bank_records[0].cheque_number,
                                    'rtgs_no' : data.data.bank_records[0].rtgs_number
                                }]);
                            }

                        $('#voucher_no').val(data.data.bank_records[0].voucher_no);
                        $('#transaction_note').val(data.data.bank_records[0].transaction_note);

                }

            });

}
        
            function get_approval_note(id) {
                $('#approval_note1').text("NA");
                $('#approval_note2').text("NA");
                $('#approval_note3').text("NA");
                $.ajax({
                    url: "{{ route('admin.get_approval_note') }}",
                    type: "post",
                    dataType: "json",
                    data: {
                        approve_id: id
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        if (data.status) {
                            $('#approval_note1').text(data.approval_note1);
                            $('#approval_note2').text(data.approval_note2);
                            $('#approval_note3').text(data.approval_note3);
                        } else {
                            $('#approval_note1').text("NA");
                            $('#approval_note2').text("NA");
                            $('#approval_note3').text("NA");
                        }
                    }
                });
            }

            $(document).ready(function() {
                jQuery('#issue_date').datepicker({
            autoclose: true,
            todayHighlight: true,
            format: "dd-mm-yyyy"
        });

                $('#reject_note_frm').validate({
                    rules: {
                        note: {
                            required: true
                        }
                    }
                });
            })

            function show_reject_note(id) {
                $('#reject_note_div').html($('#reject_note_' + id).val());
            }
            $(document).ready(function() {
                $('.shawCalRanges').daterangepicker({
                    showDropdowns: false,
                    timePicker: true,
                    timePickerIncrement: 1,
                    timePicker24Hour: true,
                    locale: {
                        format: 'D/M/YYYY H:mm'
                    },
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    },
                    alwaysShowCalendars: true,
                });
                $("#date").val('');

                var access_rule = '<?php echo $access_rule; ?>';
                access_rule = access_rule.split(',');
                var table = $('#policy_table').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    "stateSave": true,
                    "ajax": {
                        url: "<?php echo route('admin.get_bank_payment_list_ajax'); ?>",
                        type: "GET",
                    },
                    "columns": [
                        {"targets": 0, "searchable": true, "data": "payment_options"},
                        {"targets": 1, "searchable": true, "render": function (data, type, row) {
                            if (row.budhet_sheet_no) {
                                return row.budhet_sheet_no;
                            } else{
                                return "N/A";
                            }
                        }},
                        {
                            "targets": 2,
                            "searchable": true,
                            "data": "entry_code"
                        },
                        {
                            "targets": 3,
                            "searchable": true,
                            "data": "user_name"
                        },
                        {
                            "targets": 4,
                            "searchable": true,
                            "data": "company_name"
                        },
                        {"targets": 5, "searchable": true, "render" : function(data, type, row){
                            if (row.client_name) {
                                if (row.client_name == 'Other Client') {
                                    return row.client_name ;
                                }else{
                                    return row.client_name + " (" + row.location + ")";
                                }
                                    
                                } else {
                                    return "N/A";
                                }
                                }
                                },  
                        {
                            "targets": 6,
                            "searchable": true,
                            "data": "project_name"
                        },
                        {
                            "targets": 7,
                            "searchable": true,
                            "data": "other_project_detail"
                        },
                        {"targets": 8, "searchable": true, "data": "site_name"},
                        {
                            "targets": 9,
                            "searchable": true,
                            "data": "vendor_name"
                        },
                        {
                            "targets": 10,
                            "searchable": true,
                            "data": "note"
                        },
                        {
                            "targets": 11,
                            "searchable": true,
                            "render": function(data, type, row) {
                                if (row.vendor_bank_name) {
                                    return row.vendor_bank_name + " (" + row.ac_number + ")";
                                } else {
                                    return "N/A";
                                }

                            }
                        },
                        {
                            "targets": 12,
                            "searchable": true,
                            "data": "bank_name"
                        },
                        {
                            "targets": 13,
                            "searchable": true,
                            "data": "ch_no"
                        },
                        {
                            "targets": 14,
                            "searchable": true,
                            "data": "total_amount"
                        },
                        {
                            "targets": 15,
                            "searchable": true,
                            "data": "amount"
                        },
                        {
                            "targets": 16,
                            "searchable": true,
                            "render": function(data, type, row) {
                                if (row.main_entry) {
                                    return row.entry_completed;
                                } else {
                                    return "";
                                }
                            }
                        },
                        {
                            "targets": 17,
                            "render": function(data, type, row) {
                                var out = '';
                                if (row.first_approval_status == 'Pending') {
                                    out += '<b class="text-warning">Pending</b>';
                                } else if (row.first_approval_status == 'Approved') {
                                    out += '<b class="text-success">Approved</b>';
                                    out += '<br>';
                                    if (row.first_approval_datetime) {
                                        out += moment(row.first_approval_datetime).format("DD-MM-YYYY h:mm:ss a");
                                    }
                                } else {
                                    out += '<b class="text-danger">Rejected</b>';
                                    out += '<br>';
                                    if (row.first_approval_datetime) {
                                        out += moment(row.first_approval_datetime).format("DD-MM-YYYY h:mm:ss a");
                                    }
                                }
                                return out;
                            }
                        },
                        {
                            "targets": 18,
                            "render": function(data, type, row) {
                                var out = '';
                                if (row.second_approval_status == 'Pending') {
                                    out += '<b class="text-warning">Pending</b>';
                                } else if (row.second_approval_status == 'Approved') {
                                    out += '<b class="text-success">Approved</b>';
                                    out += '<br>';
                                    if (row.second_approval_datetime) {
                                        out += moment(row.second_approval_datetime).format("DD-MM-YYYY h:mm:ss a");
                                    }
                                } else {
                                    out += '<b class="text-danger">Rejected</b>';
                                    out += '<br>';
                                    if (row.second_approval_datetime) {
                                        out += moment(row.second_approval_datetime).format("DD-MM-YYYY h:mm:ss a");
                                    }
                                }
                                return out;
                            }
                        },
                        {
                            "targets": 19,
                            "render": function(data, type, row) {
                                var out = '';
                                if (row.third_approval_status == 'Pending') {
                                    out += '<b class="text-warning">Pending</b>';
                                } else if (row.third_approval_status == 'Approved') {
                                    out += '<b class="text-success">Approved</b>';
                                    out += '<br>';
                                    if (row.third_approval_datetime) {
                                        out += moment(row.third_approval_datetime).format("DD-MM-YYYY h:mm:ss a");
                                    }
                                } else {
                                    out += '<b class="text-danger">Rejected</b>';
                                    out += '<br>';
                                    if (row.third_approval_datetime) {
                                        out += moment(row.third_approval_datetime).format("DD-MM-YYYY h:mm:ss a");
                                    }
                                }
                                return out;
                            }
                        },
                        {
                            "targets": 20,
                            "render": function(data, type, row) {
                                var out = '';
                                if (row.status == 'Pending') {
                                    return '<b class="text-warning">Pending</b>';
                                } else if (row.status == 'Approved') {
                                    return '<b class="text-success">Approved</b>';
                                } else {
                                    return '<b class="text-danger">Rejected</b>';
                                }
                            }
                        },
                        {
                            "targets": 21,
                            "searchable": false,
                            "orderable": false,
                            "render": function(data, type, row) {
                                var id = row.id;
                                var out = "";
                                var role = "<?php echo Auth::user()->role; ?>";
                                var getRole = "<?php echo config('constants.ACCOUNT_ROLE'); ?>";
                                var SuperUser = "<?php echo config('constants.SuperUser'); ?>";
                                var AdminRole = "<?php echo config('constants.Admin'); ?>";
                                if (row.first_approval_status == "Pending" && role == getRole) {
                                    out += ' <button type="button" data-toggle="modal" data-target="#approvePaymentModal" onclick=confirmPayment(' + row.id + ',"Approved") class="btn btn-info btn-circle"><i class="fa fa-check"></i> </button>';
                                    out += ' <button type="button" data-target="#rejectPaymentModal" onclick=confirmPayment(' + row.id + ',"Reject") class="btn btn-warning btn-circle"><i class="fa fa-times"></i> </button>';
                                }

                                if (row.first_approval_status == "Approved" && row.second_approval_status == "Pending" && role == AdminRole) {
                                    out += ' <button type="button" data-toggle="modal" data-target="#approvePaymentModal" onclick=confirmPayment(' + row.id + ',"Approved") class="btn btn-info btn-circle"><i class="fa fa-check"></i> </button>';
                                    out += ' <button type="button" data-target="#rejectPaymentModal" onclick=confirmPayment(' + row.id + ',"Reject") class="btn btn-warning btn-circle"><i class="fa fa-times"></i> </button>';
                                }

                                if (row.first_approval_status == "Approved" && row.second_approval_status == "Approved" && row.third_approval_status == "Pending" && role == SuperUser) {
                                    out += ' <button type="button" data-toggle="modal" data-target="#approvePaymentModal" onclick=confirmPayment(' + row.id + ',"Approved") class="btn btn-info btn-circle"><i class="fa fa-check"></i> </button>';
                                    out += ' <button type="button" data-target="#rejectPaymentModal" onclick=confirmPayment(' + row.id + ',"Reject") class="btn btn-warning btn-circle"><i class="fa fa-times"></i> </button>';
                                }
                                out += '<button title="Approval Notes" type="button" onclick="get_approval_note(' + row.id + ')" data-toggle="modal" data-target="#approval_note_modal" class="btn btn-primary btn-rounded"><i class="fa fa-file-text"></i></button>';
                                if (row.payment_file) {
                                    var file_path = row.payment_file.replace("public/", "");
                                    var download_link = "{{ url('/storage/') }}" + '/' + file_path;
                                    out += '&nbsp;<a title="Download Bank Payment File" href="' + download_link + '" class="btn btn-rounded btn-primary" target="_blank" download><i class="fa fa-download"></i></a>';
                                }
                                if (row.invoice_file) {
                                    var file_path = row.invoice_file.replace("public/", "");
                                    var download_link = "{{ url('/storage/') }}" + '/' + file_path;
                                    out += '&nbsp;<a title="Download Invoice File" href="' + download_link + '" class="btn btn-rounded btn-primary" target="_blank" download><i class="fa fa-download"></i></a>';
                                }

                                out += '&nbsp;<a href="#" onclick="get_bank_payment_files(' + row.id + ',' + "'" + row.status + "'" + ');" title="View Files" id="showFiles" data-target="#bankPaymentFilesModel" data-toggle="modal" class="btn btn-primary btn-rounded"><i class="fa fa-eye"></i></a>';
                                out += '&nbsp; <a href="#" onclick="bank_payment_detail(' + row.entry_code + ');" title="View Payment Details" data-target="#payment_view" data-toggle="modal" class="btn btn-success btn-rounded"><i class="fa fa-eye"></i></a>';
                                return out;
                            }
                        }
                    ]
                });
                $('#all_policy_table').DataTable({

                    dom: 'Bfrtip',
                    buttons: [
                        'csv', 'excel', 'pdf', 'print'
                    ]
                });
            });

            function openPolicy(pdf, id) {
                $('#tableBodyPolicy').empty();
                var iframeUrl = "<iframe src=" + pdf + "#toolbar=0 height='400' width='880'></iframe>";
                $('#tableBodyPolicy').append(iframeUrl);
            }

            function confirmPayment(url, status) {

                if (status == "Reject") {
                    $("#reject_url").val(url);
                    $("#paymentid").val(url);
                    $("#rejectPaymentModal").modal('toggle'); //see here usage    
                } else {
                    $('#approve_paymentid').val(url);
                    /*swal({
                    title: "Are you sure you want to confirm " + status + " Bank Payment ?",
                            //text: "You want to change status of admin user.",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#DD6B55",
                            confirmButtonText: "Yes",
                            closeOnConfirm: false
                    }, function () {
                    location.href = url;
                    });*/
                }
            }

            function delete_file(del_id, id, i_id) {
                swal({

                    title: "Are you sure you want to delete this file?",
                    //text: "You want to change status of admin user.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: true
                }, function() {

                    $('#del_' + del_id).remove();

                    $.ajax({
                        url: "{{ route('admin.delete_bankpayment_file') }}",
                        type: "post",
                        dataType: "json",
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            id: id
                        },
                        success: function(data) {
                            if (data.status) {
                                // location.reload(true);
                                // $('#showFiles').click();

                                console.log("success");

                            }

                        }
                    });
                    //e.preventDefault();
                });


            }

            function get_bank_payment_files(id, payment_status) {
                var bank_payment_status = payment_status;
                $.ajax({
                    url: "{{ route('admin.get_bank_payment_files') }}",
                    type: "post",
                    dataType: "json",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        id: id,
                        payment_status: bank_payment_status
                    },
                    success: function(data) {
                        var trHTML = '';
                        if (data.status) {

                            let bank_payment_files_arr = data.data.bank_payment_files;
                            let payment_status = data.data.payment_status;
                            if (bank_payment_files_arr.length == 0) {

                                $('#file_table').empty();
                                trHTML += '<span>No Records Found !</span>';
                                $('#file_table').append(trHTML);

                            } else {

                                $('#file_table').empty();
                                $.each(bank_payment_files_arr, function(index, files_obj) {
                                    no = index + 1;
                                    trHTML += '<tr id=' + 'del_' + no + '>' +
                                        '<td>' + no + '</td>' +
                                        '<td><a title="Download File" download href="' + files_obj.bank_payment_file + '"><i class="fa fa-cloud-download fa-lg"></i></a></td>' +
                                        '<td>' + files_obj.file_name + '</td>' +
                                        '<td><a href="#" onclick="delete_file(' + no + ',' + files_obj.id + ', ' + files_obj.bank_payment_id + ');" id="deleteFile" class="btn btn-danger btn-rounded delete_files"><i class="fa fa-trash"></i></a></td>' +
                                        '</tr>';
                                });

                                $('#file_table').append(trHTML);
                                if (payment_status == 'Approved') {
                                    $('.delete_files').hide();
                                    //$('#del_action').remove();
                                }

                            }

                        } else {

                            $('#file_table').empty();
                            trHTML += '<span>No Records Found !</span>';
                            $('#file_table').append(trHTML);
                        }

                    }
                });
            }

            function bank_payment_detail(entry_code) {

                $('#payment_list').empty();
                $.ajax({
                    url: "{{ route('admin.get_previous_payments') }}",
                    type: "post",
                    dataType: "json",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        entry_code: entry_code,
                    },
                    success: function(data) {
                        var trHTML = '';
                        if (data.status) {
                            let payment_list_arr = data.data.previous_payments;
                            if (payment_list_arr.length == 0) {

                                trHTML += '<span>No Records Found !</span>';
                                $('#payment_list').append(trHTML);

                            } else {

                                var partial_amount;
                                $.each(payment_list_arr, function(index, bank_pay) {
                                    no = index + 1;

                                    if (index == 0) {
                                        partial_amount = bank_pay.total_amount - bank_pay.amount;
                                    } else {
                                        partial_amount = partial_amount - bank_pay.amount;
                                    }
                                    var vendor_bank_detail = bank_pay.vendor_bank_name ? bank_pay.vendor_bank_name + '(' + bank_pay.ac_number + ')' : 'N/A';
                                    var entry_status = bank_pay.main_entry ? bank_pay.entry_completed : 'N/A';
                                    var first_approval = bank_pay.first_approval_status == 'Approved' || bank_pay.first_approval_status == 'Rejected' ? bank_pay.first_approval_status + '<br>' + moment(bank_pay.first_approval_datetime).format("DD-MM-YYYY h:mm:ss a") : bank_pay.first_approval_status;
                                    var second_approval = bank_pay.second_approval_status == 'Approved' || bank_pay.second_approval_status == 'Rejected' ? bank_pay.first_approval_status + '<br>' + moment(bank_pay.second_approval_datetime).format("DD-MM-YYYY h:mm:ss a") : bank_pay.second_approval_status;
                                    var third_approval = bank_pay.third_approval_status == 'Approved' || bank_pay.third_approval_status == 'Rejected' ? bank_pay.third_approval_status + '<br>' + moment(bank_pay.third_approval_datetime).format("DD-MM-YYYY h:mm:ss a") : bank_pay.third_approval_status;
                                    var other_project_detail = bank_pay.other_project_detail ? bank_pay.other_project_detail : '';
                                    var client_name = bank_pay.client_name=="Other Client" ? bank_pay.client_name : bank_pay.client_name + "(" + bank_pay.location + ")";
                                    
                                    trHTML += '<tr>' +
                                        '<td>' + bank_pay.entry_code + '</td>' +
                                        '<td>' + bank_pay.user_name + '</td>' +
                                        '<td>' + bank_pay.company_name + '</td>' +
                                        '<td>' + client_name + '</td>' +
                                        '<td>' + bank_pay.project_name + '</td>' +
                                        '<td>' + other_project_detail + '</td>' +
                                        '<td>' + bank_pay.site_name + '</td>' +
                                        '<td>' + bank_pay.vendor_name + '</td>' +
                                        '<td>' + bank_pay.note + '</td>' +
                                        '<td>' + vendor_bank_detail + '</td>' +
                                        '<td>' + bank_pay.bank_name + '</td>' +
                                        '<td>' + bank_pay.ch_no + '</td>' +
                                        '<td>' + first_approval + '</td>' +
                                        '<td>' + second_approval + '</td>' +
                                        '<td>' + third_approval + '</td>' +
                                        '<td>' + bank_pay.status + '</td>' +
                                        '<td>' + bank_pay.total_amount + '</td>' +
                                        '<td>' + bank_pay.amount + '</td>' +
                                        '<td>' + partial_amount.toFixed(2) + '</td>' +
                                        '<td>' + entry_status + '</td>' +
                                        '</tr>';
                                });

                                $('#payment_list').append(trHTML);
                            }

                        } else {
                            trHTML += '<span>No Records Found !</span>';
                            $('#payment_list').append(trHTML);
                        }
                    }
                })
            }
        </script>
        @endsection