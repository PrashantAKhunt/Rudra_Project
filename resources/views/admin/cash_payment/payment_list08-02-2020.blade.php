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
                                <th>Project Name</th>
                                <th>Other Project Detail</th>
                                <th>Vendor Name</th>
                                <th>Amount</th>
                                <th>Payment Note</th>
                                <th>Created Date</th>
                                <th>First Approval</th>
                                <th>Second Approval</th>
                                <th>Third Approval</th>
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
                                <th>Project Name</th>
                                <th>Other Project Detail</th>
                                <th>Vendor Name</th>
                                <th>Amount</th>
                                <th>Payment Note</th>
                                <th>Created Date</th>
                                <th>First Approval</th>
                                <th>Second Approval</th>
                                <th>Third Approval</th>
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
                                        <td>{{ $cashData['project_name'] }}</td>
                                        <td>{{ $cashData['other_cash_detail'] }}</td>
                                        <td>{{ $cashData['vendor_name'] }}</td>
                                        <td>{{ $cashData['amount'] }}</td>
                                        <td>{{ $cashData['note'] }}</td>
                                        <td><span style="display: none;">{{$cashData['created_at']}}</span>{{ date('d-m-Y',strtotime($cashData['created_at'])) }}</td>
                                        <td>
                                            {{ $cashData['first_approval_status'] }}
                                        </td>
                                        <td>{{ $cashData['second_approval_status'] }}</td>
                                        <td>{{ $cashData['third_approval_status'] }}</td>
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
            
            function show_reject_note(id) {
        $('#reject_note_div').html($('#reject_note_' + id).val());

    }
            $(document).ready(function () {
                var access_rule = '<?php echo $access_rule; ?>';
                access_rule = access_rule.split(',');
                $("#cash_aaroval_list").DataTable({
                    dom: 'Bfrtip',
                    buttons: [
                        'csv', 'excel', 'print'
                    ],
                    "order": [[ 8, "desc" ]],
                });
                var table = $('#policy_table').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    "order":[[8,'desc']],
                    "ajax": {
                        url: "<?php echo route('admin.get_cash_payment_list_ajax'); ?>",
                        type: "GET",
                    },
                    "columns": [
                        {"taregts": 0, "searchable": true, "data": "user_name"},
                        {"taregts": 1, "searchable": true, "data": "title"},
                        {"taregts": 2, "searchable": true, "data": "company_name"},
                        {"taregts": 3, "searchable": true, "data": "project_name"},
                        {"taregts": 4, "searchable": true, "data": "other_cash_detail"},
                        {"taregts": 5, "searchable": true, "data": "vendor_name"},
                        {"taregts": 6, "searchable": true, "data": "amount"},
                        {"taregts": 7, "searchable": true, "data": "note"},
                        {"taregts": 8, "searchable": true, "render": function (data, type, row) {
                                return moment(row.created_at).format("DD-MM-YYYY");
                            }
                        },
                        {"taregts": 9,
                            "render": function (data, type, row) {
                                var out = '';
                                if (row.first_approval_status == 'Pending')
                                {
                                    return'<b class="text-warning">Pending</b>';
                                } else if (row.first_approval_status == 'Approved')
                                {
                                    return '<b class="text-success">Approved</b>';
                                } else
                                {
                                    return '<b class="text-danger">Rejected</b>';
                                }
                            }
                        },

                        {"taregts": 10,
                            "render": function (data, type, row) {
                                var out = '';
                                if (row.second_approval_status == 'Pending')
                                {
                                    return'<b class="text-warning">Pending</b>';
                                } else if (row.second_approval_status == 'Approved')
                                {
                                    return '<b class="text-success">Approved</b>';
                                } else
                                {
                                    return '<b class="text-danger">Rejected</b>';
                                }
                            }
                        },
                        {"taregts": 11,
                            "render": function (data, type, row) {
                                var out = '';
                                if (row.third_approval_status == 'Pending')
                                {
                                    return'<b class="text-warning">Pending</b>';
                                } else if (row.third_approval_status == 'Approved')
                                {
                                    return '<b class="text-success">Approved</b>';
                                } else
                                {
                                    return '<b class="text-danger">Rejected</b>';
                                }
                            }
                        },
                        {"taregts": 12,
                            "render": function (data, type, row) {
                                var out = '';
                                if (row.status == 'Pending')
                                {
                                    return'<b class="text-warning">Pending</b>';
                                } else if (row.status == 'Approved')
                                {
                                    return '<b class="text-success">Approved</b>';
                                } else
                                {
                                    return '<b class="text-danger">Rejected</b>';
                                }
                            }
                        },
                        {"taregts": 13, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
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
                    }, function () {
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
                }, function () {
                    location.href = rejectURL;
                });
            }

        </script>
        @endsection