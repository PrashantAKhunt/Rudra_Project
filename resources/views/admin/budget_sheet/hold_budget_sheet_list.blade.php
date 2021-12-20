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
            <b class="error">Approval Flow: {{implode(' -> ',config('constants.BUDGET_SHEET_APPROVAL'))}}</b><br>
            <br>
                <div class="table-responsive">
                    <table id="policy_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Meeting Number</th>
                                <th>Meeting Date</th>
                                <th>Budget Sheet Number</th>
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
                                <th>Purchase Order Number</th>
                                <th>Purchase Order Date</th>
                                <th>Bill Number</th>
                                <th>Bill Date</th>
                                <th>Approval Remark</th>
                                <th>Hold Amount</th>
                                <th>Remaining Hold Amount</th>
                                <th>Request Hold Amount</th>
                                <th>Invoice File</th>
                                <th>Status</th>
                                <th >Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
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


                var table = $('#policy_table').DataTable({
                    dom: 'lBfrtip',
                    buttons: [
                        'excel',
                        'csv',
                    ],
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    "order": [[ 1, "desc" ]],
                    stateSave: true,
                    "ajax": {
                        url: "<?php echo route('admin.get_hold_budget_sheet_list_ajax'); ?>",
                        type: "GET",
                    },
                    "columns": [
                        {"taregts": 0, "searchable": true, "data": "meeting_number"},
                        {"taregts": 1, "searchable": true, "data": "meeting_date"},
                        {"taregts": 2, "searchable": true, "data": "budhet_sheet_no"},
                        {"taregts": 3, "searchable": true, "data": "company_name"},
                        {
                            "taregts": 4,
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
                        {"taregts": 5, "searchable": true, "data": "dept_name"},
                        {"taregts": 6, "searchable": true, "data": "vendor_name"},
                        {"taregts": 7, "searchable": true, "data": "description"},
                        {"taregts": 8, "searchable": true, "data": "remark_by_user"},
                        {"taregts": 9, "searchable": true, "data": "request_amount"},
                        {"taregts": 10, "searchable": true, "data": "schedule_date_from"},
                        {"taregts": 11, "searchable": true, "data": "schedule_date_to"},
                        {"taregts": 12, "searchable": true, "data": "mode_of_payment"},

                        {"taregts": 13, "searchable": true, "data": "project_name"},
                        {"taregts": 14, "searchable": true, "data": "site_name"},
                        {"taregts": 15, "searchable": true, "data": "total_amount"},
                        {"taregts": 16, "searchable": true, "data": "approved_amount"},
                        {
                            "taregts": 17,
                            "searchable": true,
                            "data": "purchase_order_number"
                        },
                        {"taregts": 18, "searchable": true, "data": "purchase_order_date"},
                        {"taregts": 19, "searchable": true, "data": "bill_number"},
                        {"taregts": 20, "searchable": true, "data": "bill_date"},
                        {"taregts": 21, "searchable": true, "data": "approval_remark"},
                        {"taregts": 22, "searchable": true, "data": "hold_amount"},
                        {"taregts": 23, "searchable": true, "data": "remain_hold_amount"},
                        {"taregts": 24,"searchable": true, "orderable": true,
                            "render": function (data, type, row) {
                                var release_hold_amount_status = row.release_hold_amount_status;
                                if (release_hold_amount_status == "Pending") {
                                    return "Yes";
                                }else{
                                    return "No";
                                }
                            }
                        },
                        {"taregts": 25,"searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out = '';
                                var path = row.invoice_file;
                                if (path) {
                                    var baseURL = path.replace("public/","");
                                    var url=  "<?php echo url('/storage/');?>"+"/"+baseURL;
                                    // out += '<input type="hidden" value="'+row.failed_reason+'" id="failed_reason_hide'+row.id+'" name="failed_reason_hide" /><input type="hidden" value="'+row.failed_document+'" id="failed_document_hide'+row.id+'" name="failed_document_hide" /><a href="#" onclick="show_detail(' + row.id + ');" data-toggle="modal" data-target="#failed_cheque_view" class="btn btn-danger" title="View details">'+'Reason'+'</a>';
                                    out += '<a href="'+ url +'" title="Download" download><i class="fa fa-cloud-download fa-lg"></i></a>';
                                }

                                return out;
                            }
                        },
                        {"taregts": 26,
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
                        {"taregts": 27, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out = "";

                                var hold_action_url="{{ url('manage_hold_amt') }}"+"/"+id;

                                    out += ' <a title="Complete Hold Amount" href="'+hold_action_url+'" class="btn btn-success btn-circle"><i class="fa fa-check"></i> </a>';

                                    out += '<a href="#" onclick="get_budget_sheet_files(' + row.id + ');" title="View Files" id="showFiles" data-target="#budgetSheetFilesModel" data-toggle="modal" class="btn btn-primary btn-circle"><i class="fa fa-eye"></i></a>';

                                    out += '<a href="#" onclick="get_invoice_files(' + row.id + ');" title="View Invoice Files" id="showFiles" data-target="#budgetSheetFilesModel" data-toggle="modal" class="btn btn-primary btn-circle"><i class="fa fa-picture-o"></i></a>';

                                return out;
                            }
                        }
                    ]
                });
            })

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
                                             '<td><a title="Download File" target="_blank;" href="' + files_obj.budget_sheet_file + '"><i class="fa fa-cloud-download fa-lg"></i></a></td>' +
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
        //------------------------------------ Invoice files ---------------------------------  
        function get_invoice_files(id) {


                     $.ajax({
                         url: "{{ route('admin.get_invoice_files') }}",
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

                                 let invoice_files_arr = data.data.invoice_files;
                                 if (invoice_files_arr.length == 0) {

                                     $('#file_table').empty();
                                     trHTML += '<span>No Records Found !</span>';
                                     $('#file_table').append(trHTML);

                                 } else {

                                     $('#file_table').empty();

                                     $.each(invoice_files_arr, function(index, files_obj) {

                                         no = index + 1;
                                         trHTML += '<tr>' +
                                             '<td>' + no + '</td>' +
                                             '<td><a title="Download File" target="_blank;" href="' + files_obj.budget_sheet_invoice_file + '"><i class="fa fa-cloud-download fa-lg"></i></a></td>' +
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
