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
                <h3 class="page-title">Pending Budget Sheet Entry</h3>
                <br>        
                <b class="error">Approval Flow: {{implode(' -> ',config('constants.BUDGET_SHEET_APPROVAL'))}}</b>
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="table-responsive">
                    <table id="policy_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Meeting Number</th>
                                <th>Meeting Date</th>
                                <th>Company</th>
                                <th>Client</th>
                                <th>Department</th>
                                <th>Vendor</th>
                                <th>Description</th>
                                <th>Remarks by employee</th>
                                <th>Request Amount</th>
                                <th>Schedule Date From</th>
                                <th>Schedule Date To</th>
                                <th>Mode Of Payment</th>
                                <th>Project</th>
                                <th>Site Name</th>
                                <th>Total Amount</th>
                                <th>Approved Amount</th>
                                <th>Approval Remark</th>
                                <th>Hold Amount</th>
                                <th>Previous Meeting Hold Amount</th>
                                <th>Total Approved Amount</th>
                                <th>Admin Status</th>
                                <th>Super Admin Status</th>

                                <th>Status</th>
                                <th >Action</th>
                            </tr>
                        </thead>
                        <tbody>                            
                        </tbody>
                    </table>
                </div>
            </div>  
            
            
<!--            <div class="white-box">                
                <h3 class="page-title">Pending Budget Sheet Entry</h3>
                <br>                
                <div class="table-responsive">
                    <table id="all_data_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Meeting Number</th>
                                <th>Meeting Date</th>
                                <th>Company</th>
                                <th>Department</th>
                                <th>Vendor</th>
                                <th>Description</th>
                                <th>Remarks by employee</th>
                                <th>Request Amount</th>
                                <th>Schedule Date From</th>
                                <th>Schedule Date To</th>
                                <th>Mode Of Payment</th>
                                <th>Project</th>
                                <th>Total Amount</th>
                                <th>Approved Amount</th>
                                <th>Approval Remark</th>
                                <th>Hold Amount</th>
                                <th>Previous Meeting Hold Amount</th>
                                <th>Total Approved Amount</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>                            
                        </tbody>
                    </table>
                </div>
            </div>-->
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
        
        <div id="rejectReasonModel" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">Rejection Note</h4>
                    </div>
                    <div class="modal-body" id="reject_content">
                        
                    </div>
                    
                </div>
                <!-- /.modal-content -->
            </div> 
        </div>
        <!-------------------------------------------------------- Files Modal ------------------------------------------>    
    <div id="budgetSheetFilesModel" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">Files</h4>
                    </div>

                    <br>
                    <br>

                    <table  class="table table-striped table-bordered" >
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Download</th>
                                <th>Filename</th>
                                

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
        @endsection

        @section('script')		
        <script>
            $(document).ready(function () {
                //var access_rule = '<?php //echo $access_rule; ?>';
               // access_rule = access_rule.split(',');

                var table = $('#policy_table').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    "order": [[ 1, "desc" ]],
                    stateSave: true,
                    "ajax": {
                        url: "<?php echo route('admin.get_budget_sheet_list_ajax'); ?>",
                        type: "GET",
                    },
                    "columns": [
                        {"taregts": 0, "searchable": true, "data": "meeting_number"},
                        {"taregts": 1, "searchable": true, "data": "meeting_date"},
                        {"taregts": 2, "searchable": true, "data": "company_name"},
                        {
                            "taregts": 3,
                            "searchable": true,
                            "render": function(data, type, row){
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
                        {"taregts": 4, "searchable": true, "data": "dept_name"},
                        {"taregts": 5, "searchable": true, "data": "vendor_name"},
                        {"taregts": 6, "searchable": true, "data": "description"},
                        {"taregts": 7, "searchable": true, "data": "remark_by_user"},
                        {"taregts": 8, "searchable": true, "data": "request_amount"},
                        {"taregts": 9, "searchable": true, "data": "schedule_date_from"},
                        {"taregts": 10, "searchable": true, "data": "schedule_date_to"},
                        {"taregts": 11, "searchable": true, "data": "mode_of_payment"},

                        {"taregts": 12, "searchable": true, "data": "project_name"},
                        {"taregts": 13, "searchable": true, "data": "site_name"},
                        {"taregts": 14, "searchable": true, "data": "total_amount"},
                        {"taregts": 15, "searchable": true, "data": "approved_amount"},
                        {"taregts": 16, "searchable": true, "data": "approval_remark"},
                        {"taregts": 17, "searchable": true, "data": "hold_amount"},
                        {"taregts": 18, "searchable": true, "data": "remain_hold_amount"},
                        {"taregts": 19, "searchable": true, "data": "final_approved_amount"},
                        {
                            "taregts": 20,
                            "searchable": false,
                            "render": function(data, type, row) {

                                if (row.first_approval_status == 'Pending') {
                                    return '<b class="text-warning">Pending</b>';
                                } else if (row.first_approval_status == 'Approved') {
                                    return '<b class="text-success">Approved</b>';
                                } else if (row.first_approval_status == 'Canceled') {
                                    return '<b class="text-danger">Canceled</b>';
                                } else {
                                    return '<b class="text-danger">Rejected</b>';
                                }

                            }   
                        },
                        {
                            "taregts": 21,
                            "searchable": false,
                            "render": function(data, type, row) {

                                if (row.second_approval_status == 'Pending') {
                                    return '<b class="text-warning">Pending</b>';
                                } else if (row.second_approval_status == 'Approved') {
                                    return '<b class="text-success">Approved</b>';
                                } else if (row.second_approval_status == 'Canceled') {
                                    return '<b class="text-danger">Canceled</b>';
                                } else {
                                    return '<b class="text-danger">Rejected</b>';
                                }

                            }
                        },

                        {"taregts": 22,
                            "searchable": false,
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
                                    return '<input type="hidden" id="reject_reason_'+row.id+'" value="'+row.reject_note+'" /><a href="#" data-toggle="modal" data-target="#rejectReasonModel" onclick="set_reject_reason('+row.id+');" class="btn btn-danger">Rejected</a>';
                                }
                            }
                        },
                        {"taregts": 23, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out = "";
                                var role = "<?php echo Auth::user()->role; ?>";
                                var getRole = "<?php echo config('constants.ACCOUNT_ROLE'); ?>";
                                var SuperUser = "<?php echo config('constants.SuperUser'); ?>";
                                var approve_link="{{ url('approve_budget') }}"+"/"+id;
                                var reject_link="{{ url('reject_budget') }}"+"/"+id;
                                if (row.status == "Pending") {
                                    out += ' <a href="'+approve_link+'" class="btn btn-success btn-circle"><i class="fa fa-check"></i> </a>';
                                    out += '&nbsp; <a href="'+reject_link+'" class="btn btn-danger btn-circle"><i class="fa fa-times"></i> </a>';
                                }
                                out += '<a href="#" onclick="get_budget_sheet_files(' + row.id + ');" title="View Files" id="showFiles" data-target="#budgetSheetFilesModel" data-toggle="modal" class="btn btn-primary btn-circle"><i class="fa fa-eye"></i></a>';
                                /*if (row.first_approval_status == "Approved" && row.status == "Pending" && role == SuperUser) {
                                    out += ' <button type="button" onclick=confirmPayment("<?php echo url("approve_budget_sheet") ?>' + '/' + row.id + '","Approved") class="btn btn-info btn-circle"><i class="fa fa-check"></i> </button>';
                                    out += ' <button type="button" onclick=confirmPayment("<?php echo url("reject_budget_sheet") ?>' + '/' + row.id + '","Reject") class="btn btn-warning btn-circle"><i class="fa fa-times"></i> </button>';
                                }*/
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
                        title: "Are you sure you want to confirm " + status + " Budget File ?",
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
                    title: "Are you sure you want to confirm " + status + " Budget File ?",
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
            function set_reject_reason(id){
                $('#reject_content').html($('#reject_reason_'+id).val());
            }

        </script>

        <script>
         function get_budget_sheet_files(id) {
                     
                    
                     $.ajax({
                         url: "{{ route('admin.get_budget_sheet_files') }}",
                         type: "post",
                         dataType: "json",
                         headers: {
                             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                         },
                         data: { 
                             id: id
                         },
                         success: function(data) {
                             var trHTML = '';
                             if (data.status) {
     
                                 let sheet_files_arr = data.data.budget_sheet_files;
                                 if (sheet_files_arr.length == 0) {
     
                                     $('#file_table').empty();
                                     trHTML += '<span>No Records Found !</span>';
                                     $('#file_table').append(trHTML);
     
                                 } else {
     
                                     $('#file_table').empty();
     
                                     $.each(sheet_files_arr, function(index, files_obj) {
     
                                         no = index + 1;
                                         trHTML += '<tr>' +
                                             '<td>' + no + '</td>' +
                                             '<td><a title="Download File" download href="' + files_obj.budget_sheet_file + '"><i class="fa fa-cloud-download fa-lg"></i></a></td>' +
                                             '<td>' + files_obj.file_name + '</td>' +
                                             '</tr>';
     
     
                                     });
                                     $('#file_table').append(trHTML);
     
                                 }
     
                             } else {
     
                                 $('#file_table').empty();
                                 trHTML += '<span>No Records Found !</span>';
                                 $('#file_table').append(trHTML);
                             }
     
                         }
                     });
                 }
        </script>
        @endsection