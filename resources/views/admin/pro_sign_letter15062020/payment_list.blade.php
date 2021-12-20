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
                <b class="error">Approval Flow: {{implode(' -> ',config('constants.LETTER_HEAD_APPROVAL'))}}</b>
                <p class="text-muted m-b-30"></p>
                <br>              
                <div class="table-responsive">
                    <table id="policy_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Title</th>
                                <th>Company</th>
                                <th>Project Name</th>
                                <th>Other Project Detail</th>
                                
                                <th>Letter Head Number</th>
                                <th>Vendor Name</th>
                                <th>Requested Content</th>
                                <th>Requested Date</th>
                                <th>First Approval</th>
                                <th>Final Approval</th>
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

            <div id="acceptPaymentModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        </div>
                        <div class="modal-body" id="userTable">
                            <div class="form-group">
                                <label>Assign letter-head delivery person</label>
                                <select class="select2 m-b-10 select2-multiple form-control" name="assign_letter_user_id" id="assign_letter_user_id">
                                    <option value="">Select Letter-head Delivery Person</option>
                                    @foreach($users_data as $consultant)
                                    <option value="{{ $consultant->id }}">{{ $consultant->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 pull-left">
                            <div class="clearfix"></div>
                            <br>
                            <button type="button" onclick="AcceptPayment('Accept')" data-dismiss="modal" class="btn btn-success">Accept</button>

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
        
        <div id="letter_content" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h4 class="modal-title" id="myModalLabel">Requested Content</h4>
                        </div>
                        <div class="modal-body" id="tableBodylatterContent">

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
            $(document).ready(function () {
                $('#assign_letter_user_id').select2();
                var table = $('#policy_table').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    stateSave: true,
                    "ajax": {
                        url: "<?php echo route('admin.get_pro_sign_letter_list_ajax'); ?>",
                        type: "GET",
                    },
                    "columns": [
                        {"taregts": 0, "searchable": true, "data": "user_name"},
                        {"taregts": 1, "searchable": true, "data": "title"},
                        {"taregts": 2, "searchable": true, "data": "company_name"},
                        {"taregts": 3, "searchable": true, "data": "project_name"}, 
                        {"taregts": 4, "searchable": true, "data": "other_project_detail"},
                        
                        {"taregts": 5, "searchable": true, "data": "letter_head_number"},
                        {"taregts": 6, "searchable": true, "data": "vendor_name"}, 
                        {"taregts": 7, "searchable": true, "render": function (data, type, row) {
                                
                                return '<input type="hidden" id="letter_content_input_'+row.id+'" value="'+row.note+'" /><a href="#" class="btn btn-info btn-rounded" onclick="open_letter_content('+row.id+')" data-toggle="modal" data-target="#letter_content" title="View Content"><i class="fa fa-eye"></i></a>';
                            }
                        },
                        {"taregts": 8, "searchable": true, "render": function (data, type, row) {

                                return moment(row.created_at).format("DD-MM-YYYY h:m a");
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
                        {"taregts": 11, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out = "";
                                var role = "<?php echo Auth::user()->role; ?>";
                                var getRole = "<?php echo config('constants.HR_ROLE'); ?>";
                                var SuperUser = "<?php echo config('constants.SuperUser'); ?>";
                                out +='<a title="Download Request Content Document file" class="btn btn-rounded btn-info" href="<?php echo url("download_normal_letter_head_content") ?>' + '/' + id + '" target="_blank"><i class="fa fa-download"></i></a> &nbsp;';
                                if (row.first_approval_status == "Pending" && role == getRole) {
                                    out += ' <button type="button" title="Approve" onclick=confirmPayment("<?php echo url("approve_pro_sign_letter") ?>' + '/' + row.id + '","Approved") class="btn btn-info btn-circle"><i class="fa fa-check"></i> </button>';
                                    out += ' <button type="button" title="Reject" onclick=confirmPayment("<?php echo url("reject_pro_sign_letter") ?>' + '/' + row.id + '","Reject") class="btn btn-warning btn-circle"><i class="fa fa-times"></i> </button>';
                                }

                                if (row.first_approval_status == "Approved" && row.status == "Pending" && role == SuperUser) {
                                    out += ' <button type="button" title="Approve" onclick=confirmPayment("<?php echo url("approve_pro_sign_letter") ?>' + '/' + row.id + '","Approved") class="btn btn-info btn-circle"><i class="fa fa-check"></i> </button>';
                                    out += ' <button type="button" title="Reject" onclick=confirmPayment("<?php echo url("reject_pro_sign_letter") ?>' + '/' + row.id + '","Reject") class="btn btn-warning btn-circle"><i class="fa fa-times"></i> </button>';
                                }
                                return out;
                            }
                        }
                    ]
                });
            })
             function open_letter_content(id) {
            
                $('#tableBodylatterContent').html($('#letter_content_input_'+id).val());
            }

            function confirmPayment(url, status) {
                var role = "<?php echo Auth::user()->role; ?>";
                var SuperUser = "<?php echo config('constants.SuperUser'); ?>";
                var getRole = "<?php echo config('constants.HR_ROLE'); ?>";

                if (status == "Reject") {
                    $("#reject_url").val(url);
                    $("#rejectPaymentModal").modal('toggle'); //see here usage    
                } else if (status == "Approved")
                {
                    $("#reject_url").val(url);
                    $("#acceptPaymentModal").modal('toggle'); //see here usage    
                }
                // else
                // {
                //     swal({
                //             title: "Are you sure you want to confirm "+status+" Pro Sign File ?",
                //             //text: "You want to change status of admin user.",
                //             type: "warning",
                //             showCancelButton: true,
                //             confirmButtonColor: "#DD6B55",
                //             confirmButtonText: "Yes",
                //             closeOnConfirm: false
                //         }, function () {
                //             location.href = url;
                //     });
                // }
            }

            function RejectedPayment(status) {
                var url = $('#reject_url').val();
                var rejectURL = url + '/' + $('#note').val();
                swal({
                    title: "Reject Confirmation",
                    text: "Are you sure you want to " + status + " letter head request?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false
                }, function () {
                    location.href = rejectURL;
                });
            }
            function AcceptPayment(status) {
                var url = $('#reject_url').val();
                var acceptURL = url + '/' + $('#assign_letter_user_id').val();
                swal({
                    title: "Approval Confirmation",
                    text: "Are you sure you want to " + status + " this letted head ? Please check the content requested carefully before approval.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: false
                }, function () {
                    location.href = acceptURL;
                });
            }
        </script>
        @endsection