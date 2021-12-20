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
                <b class="error">Approval Flow: {{implode(' -> ',config('constants.CASH_PAYMENT_APPROVAL'))}}</b>
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="table-responsive">
                    <table id="policy_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>User name</th>
                                <th>Payment Title</th>
                                <th>Company Name</th>
                                <th>Client Name</th>
                                <th>Project Name</th>
                                <th>Other Project Detail</th>
                                <th>Project Site Name</th>
                                <th>Vendor Name</th>
                                <th>Amount</th>
                                <th>Payment Note</th>
                                <th>Created Date</th>
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
            <div id="rejectPaymentModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
        </div>

        <div class="col-md-12 col-lg-12 col-sm-12">

            <div class="white-box">
                <div class="row bg-title">
                    <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
                        <h4 class="page-title">Cash Payment History</h4>
                    </div>

                    <!-- start nish -->
                    <div class="col-md-12 col-lg-12 col-sm-12">
                        <div class="panel panel-info">
                            <div class="panel-wrapper collapse in" aria-expanded="true">
                                <div class="panel-body">
                                    <form action="{{ route('admin.cash_payment_list') }}" id="cash_payment_frm" method="post" class="form-material" accept-charset="utf-8">
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

                    <!-- End  -->
                </div>
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="table-responsive">
                    <table id="cash_aaroval_list" class="table table-striped">
                        <thead>
                            <tr>
                                <th>User name</th>
                                <th>Payment Title</th>
                                <th>Company Name</th>
                                <th>Client Name</th>
                                <th>Project Name</th>
                                <th>Other Project Detail</th>
                                <th>Project Site Name</th>
                                <th>Vendor Name</th>
                                <th>Amount</th>
                                <th>Payment Note</th>
                                <th>Created Date</th>
                                <th>Accountant Status</th>
                                <th>Admin Status</th>
                                <th>Super Admin Status</th>
                                <th>Status</th>
                                <th>Payment File</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($cashApprovealData)) { ?>
                                <?php foreach ($cashApprovealData as $cashData) { ?>
                                    <tr>
                                        <td>{{ $cashData['user_name'] }}</td>
                                        <td>{{ $cashData['title'] }}</td>
                                        <td>{{ $cashData['company_name'] }}</td>
                                        <td>
                                        @if($cashData['client_name'])
                                        @if($cashData['client_name'] == 'Other Client')
                                        {{ $cashData['client_name'] }}
                                            @else
                                            {{ $cashData['client_name']." (".$cashData['location'].")" }}
                                            @endif

                                            @else
                                            N/A
                                            @endif
                                        
                                        </td>
                                        <td>{{ $cashData['project_name'] }}</td>
                                        <td>{{ $cashData['other_cash_detail'] }}</td>
                                        <td>{{ $cashData['site_name'] }}</td>
                                        <td>{{ $cashData['vendor_name'] }}</td>
                                        <td>{{ $cashData['amount'] }}</td>
                                        <td>{{ $cashData['note'] }}</td>
                                        <td><span style="display: none;">{{$cashData['created_at']}}</span>{{ date('d-m-Y',strtotime($cashData['created_at'])) }}</td>



                                        <td @if($cashData['first_approval_status']=="Approved" ) class="text-success" @elseif($cashData['first_approval_status']=="Pending" ) class="text-warning" @else class="text-danger" @endif>
                                            {{ $cashData['first_approval_status'] }}
                                            <br>
                                            @if($cashData['first_approval_datetime'])
                                            {{ Carbon\Carbon::parse($cashData['first_approval_datetime'])->format('d-m-Y h:i:s A') }}
                                            @endif
                                        </td>
                                        <td @if($cashData['second_approval_status']=="Approved" ) class="text-success" @elseif($cashData['second_approval_status']=="Pending" ) class="text-warning" @else class="text-danger" @endif>
                                            {{ $cashData['second_approval_status'] }}
                                            <br>
                                            @if($cashData['second_approval_datetime'])
                                            {{ Carbon\Carbon::parse($cashData['second_approval_datetime'])->format('d-m-Y h:i:s A') }}
                                            @endif
                                        </td>
                                        <td @if($cashData['third_approval_status']=="Approved" ) class="text-success" @elseif($cashData['third_approval_status']=="Pending" ) class="text-warning" @else class="text-danger" @endif>
                                            {{ $cashData['third_approval_status'] }}
                                            <br>
                                            @if($cashData['third_approval_datetime'])
                                            {{ Carbon\Carbon::parse($cashData['third_approval_datetime'])->format('d-m-Y h:i:s A') }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($cashData['status']=="Pending")
                                            <b class="text-warning">{{ $cashData['status'] }}</b>
                                            @elseif($cashData['status']=="Approved")
                                            <b class="text-success">{{ $cashData['status'] }}</b>

                                            @elseif($cashData['status']=="Rejected")
                                            <input type="hidden" name="reject_note_{{$cashData['id']}}" id="reject_note_{{$cashData['id']}}" value="{{ $cashData['reject_note'] }}" />

                                            <a class="btn btn-danger" href="#" onclick="show_reject_note({{$cashData['id']}})" data-toggle="modal" data-target="#reject_note_modal">{{ $cashData['status'] }}</a>
                                            @endif
                                        </td>
                                        <td>
                                            @if($cashData['payment_file'])
                                            <a download="" title="Download Cash Payment File" href="{{asset('storage/'.str_replace('public/','',$cashData['payment_file']))}}" class="btn btn-rounded btn-primary"><i class="fa fa-download"></i></a>
                                            @endif
                                        </td>



                                    </tr>
                                <?php } ?>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Approve Model -->
            <div id="Approvemodel" class="modal fade">
                <div class="modal-dialog">
                    <div class="modal-content" id="model_data">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h3 class="panel-title">Approve Cash Payment</h3>
                        </div>
                        <div class="modal-body">
                            <form method="post" action="{{ route('admin.approve_cashPaymentByAccountant') }}">
                                @csrf
                                <input type="hidden" name="id" id="cash_id" value="">
                                <div class="row">
                                    <div class="form-group ">
                                        <label>Cheque Number</label>
                                        <input type="text" class="form-control" name="cheque_number" id="cheque_number" value="" />
                                    </div>
                                    <div class="form-group ">
                                        <label>RTGS Number</label>
                                        <input type="text" class="form-control" name="rtgs_number" id="rtgs_number" value="" />
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

            <div id="rejectPaymentModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
        </div>

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
        @endsection

        @section('script')
        <script>
            function approve_confirmByAccountant(id) {
                $('#cash_id').val(id);

                $.ajax({

                    url: "{{ route('admin.get_cashApproval') }}",
                    type: "POST",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        id: id
                    },
                    dataType: "JSON",
                    //processData: false,
                    //contentType: false,
                    success: function(data) {

                        $('#cheque_number').val(data.data.cash_records[0].cheque_number);
                        $('#rtgs_number').val(data.data.cash_records[0].rtgs_number);
                        $('#voucher_no').val(data.data.cash_records[0].voucher_no);
                        $('#transaction_note').val(data.data.cash_records[0].transaction_note);
                    }


                });

            }


            function show_reject_note(id) {
                $('#reject_note_div').html($('#reject_note_' + id).val());

            }
            $(document).ready(function() {

                //jayram desai 268-285
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
                $("#cash_aaroval_list").DataTable({
                    dom: 'Bfrtip',
                    buttons: [
                        'csv', 'excel', 'print'
                    ],
                    "order": [
                        [8, "desc"]
                    ],
                });
                var table = $('#policy_table').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    "order": [
                        [8, 'desc']
                    ],
                    "ajax": {
                        url: "<?php echo route('admin.get_cash_payment_list_ajax'); ?>",
                        type: "GET",
                    },
                    "columns": [{
                            "taregts": 0,
                            "searchable": true,
                            "data": "user_name"
                        },
                        {
                            "taregts": 1,
                            "searchable": true,
                            "data": "title"
                        },
                        {
                            "taregts": 2,
                            "searchable": true,
                            "data": "company_name"
                        },
                        {
                            "taregts": 3,
                            "searchable": true,
                            "render" : function(data, type, row){
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
                            "taregts": 4,
                            "searchable": true,
                            "data": "project_name"
                        },
                        {
                            "taregts": 5,
                            "searchable": true,
                            "data": "other_cash_detail"
                        },
                        {
                            "taregts": 6,
                            "searchable": true,
                            "data" : "site_name"
                            
                        },
                        {
                            "taregts": 7,
                            "searchable": true,
                            "data": "vendor_name"
                        },
                        {
                            "taregts": 8,
                            "searchable": true,
                            "data": "amount"
                        },
                        {
                            "taregts": 9,
                            "searchable": true,
                            "data": "note"
                        },
                        {
                            "taregts": 10,
                            "searchable": true,
                            "render": function(data, type, row) {
                                return moment(row.created_at).format("DD-MM-YYYY");
                            }
                        },
                        {
                            "taregts": 11,
                            "render": function(data, type, row) {
                                var out = '';
                                if (row.first_approval_status == 'Pending') {

                                    out += '<b class="text-warning">Pending</b>';
                                    //out+= moment(row.first_approval_datetime).format("DD-MM-YYYY h:mm:ss");
                                    return out;
                                } else if (row.first_approval_status == 'Approved') {
                                    out += '<b class="text-success">Approved</b>';
                                    out += '<br>';
                                    if (row.first_approval_datetime) {
                                        out += moment(row.first_approval_datetime).format("DD-MM-YYYY h:mm:ss a");
                                    }
                                    return out;
                                } else {
                                    out += '<b class="text-danger">Rejected</b>';
                                    out += '<br>';
                                    if (row.first_approval_datetime) {
                                        out += moment(row.first_approval_datetime).format("DD-MM-YYYY h:mm:ss a");
                                    }
                                    return out;
                                }
                            }
                        },

                        {
                            "taregts": 11,
                            "render": function(data, type, row) {
                                var out = '';
                                if (row.second_approval_status == 'Pending') {
                                    out += '<b class="text-warning">Pending</b>';
                                    //out+= moment(row.second_approval_datetime).format("DD-MM-YYYY h:mm:ss");
                                    return out;
                                } else if (row.second_approval_status == 'Approved') {
                                    out += '<b class="text-success">Approved</b>';
                                    out += '<br>';
                                    if (row.second_approval_datetime) {
                                        out += moment(row.second_approval_datetime).format("DD-MM-YYYY h:mm:ss a");
                                    }
                                    return out;
                                } else {
                                    out += '<b class="text-danger">Rejected</b>';
                                    out += '<br>';
                                    if (row.second_approval_datetime) {
                                        out += moment(row.second_approval_datetime).format("DD-MM-YYYY h:mm:ss a");
                                    }
                                    return out;

                                }
                            }
                        },
                        {
                            "taregts": 12,
                            "render": function(data, type, row) {
                                var out = '';
                                if (row.third_approval_status == 'Pending') {
                                    out += '<b class="text-warning">Pending</b>';
                                    //out+= moment(row.third_approval_datetime).format("DD-MM-YYYY h:mm:ss");
                                    return out;
                                } else if (row.third_approval_status == 'Approved') {
                                    out += '<b class="text-success">Approved</b>';
                                    out += '<br>';
                                    if (row.third_approval_datetime) {
                                        out += moment(row.third_approval_datetime).format("DD-MM-YYYY h:mm:ss a");
                                    }
                                    return out;
                                } else {
                                    out += '<b class="text-danger">Rejected</b>';
                                    out += '<br>';
                                    if (row.third_approval_datetime) {
                                        out += moment(row.third_approval_datetime).format("DD-MM-YYYY h:mm:ss a");
                                    }
                                    return out;
                                }
                            }
                        },
                        {
                            "taregts": 13,
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
                            "taregts": 14,
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
                                    out += ' <button type="button" onclick=confirmPayment("<?php echo url("approve_cash_payment") ?>' + '/' + row.id + '","Approved") class="btn btn-info btn-circle"><i class="fa fa-check"></i> </button>';
                                    out += ' <button type="button" onclick=confirmPayment("<?php echo url("reject_cash_payment") ?>' + '/' + row.id + '","Reject") class="btn btn-warning btn-circle"><i class="fa fa-times"></i> </button>';
                                }

                                if (row.first_approval_status == "Approved" && row.second_approval_status == "Pending" && role == AdminRole) {
                                    out += ' <button type="button" onclick=confirmPayment("<?php echo url("approve_cash_payment") ?>' + '/' + row.id + '","Approved") class="btn btn-info btn-circle"><i class="fa fa-check"></i> </button>';
                                    out += ' <button type="button" onclick=confirmPayment("<?php echo url("reject_cash_payment") ?>' + '/' + row.id + '","Reject") class="btn btn-warning btn-circle"><i class="fa fa-times"></i> </button>';
                                }

                                if (row.first_approval_status == "Approved" && row.second_approval_status == "Approved" && row.third_approval_status == "Pending" && role == SuperUser) {
                                    out += ' <button type="button" onclick=confirmPayment("<?php echo url("approve_cash_payment") ?>' + '/' + row.id + '","Approved") class="btn btn-info btn-circle"><i class="fa fa-check"></i> </button>';
                                    out += ' <button type="button" onclick=confirmPayment("<?php echo url("reject_cash_payment") ?>' + '/' + row.id + '","Reject") class="btn btn-warning btn-circle"><i class="fa fa-times"></i> </button>';
                                }
                                if (row.payment_file) {
                                    var file_path = row.payment_file.replace("public/", "");
                                    var download_link = "{{ url('/storage/') }}" + '/' + file_path;
                                    out += '&nbsp;<a title="Download Cash Payment File" href="' + download_link + '" class="btn btn-rounded btn-primary" target="_blank" download><i class="fa fa-download"></i></a>';
                                }
                                return out;
                            }
                        }
                    ]
                });
            })

            function openPolicy(pdf, id) {
                $('#tableBodyPolicy').empty();
                var iframeUrl = "<iframe src=" + pdf + "#toolbar=0 height='400' width='880'></iframe>";
                $('#tableBodyPolicy').append(iframeUrl);
            }

            function confirmPayment(url, status) {
                if (status == "Reject") {
                    $("#reject_url").val(url);
                    $("#rejectPaymentModal").modal('toggle'); //see here usage    
                } else {
                    swal({
                        title: "Are you sure you want to confirm " + status + " Cash Payment ?",
                        //text: "You want to change status of admin user.",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes",
                        closeOnConfirm: false
                    }, function() {
                        location.href = url;
                    });
                }
            }

            function RejectedPayment(status) {
                var url = $('#reject_url').val();
                var rejectURL = url + '/' + $('#note').val();
                swal({
                    title: "Are you sure you want to confirm " + status + " Cash Payment ?",
                    //text: "You want to change status of admin user.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false
                }, function() {
                    location.href = rejectURL;
                });
            }
        </script>
        @endsection