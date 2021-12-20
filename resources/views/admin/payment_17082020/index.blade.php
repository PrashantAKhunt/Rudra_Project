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
                <?php
                $role = explode(',', $access_rule);
                if (in_array(3, $role)) {
                    ?>
                    <a href="{{ route('admin.add_bank_payment_detail') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Bank Payment Detail</a>
                <?php } ?>
                <p class="text-muted m-b-30"></p>
                <br>            
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
                                <th>Part Payment Amount</th>
                                <th>Entry Completed</th>
                                <th>Accountant Status</th>
                                <th>Admin Status</th>
                                <th>Super Admin Status</th>
                                <th>Status</th>
                                <th>Created Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
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
        
        <div id="bankPaymentFilesModel" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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

        @endsection

        @section('script')		
        <script>
            
            function get_bank_payment_files(id,payment_status) {
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
                        payment_status:bank_payment_status
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
                                    trHTML += '<tr id='+'del_'+no+'>' +
                                        '<td>' + no + '</td>' +
                                        '<td><a title="Download File" download href="' + files_obj.bank_payment_file + '"><i class="fa fa-cloud-download fa-lg"></i></a></td>' +
                                        '<td>' + files_obj.file_name + '</td>' +                                    
                                        '<td><a href="#" onclick="delete_file('+no+',' + files_obj.id + ', '+ files_obj.bank_payment_id +');" id="deleteFile" class="btn btn-danger btn-rounded delete_files"><i class="fa fa-trash"></i></a></td>' +
                                        '</tr>';
                                });

                                $('#file_table').append(trHTML);
                                if(payment_status=='Approved') { 
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
        
        function delete_file(del_id,id, i_id) {
                swal({

                    title: "Are you sure you want to delete this file?",
                    //text: "You want to change status of admin user.",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes",
                    closeOnConfirm: true
                }, function() {
                    
                    $('#del_'+del_id).remove();

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

            function show_reject_note(id) {
                $('#reject_note_div').html($('#reject_note_' + id).val());

            }

            $(document).ready(function () {
                var access_rule = '<?php echo $access_rule; ?>';
                access_rule = access_rule.split(',');

                var table = $('#policy_table').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    //"stateSave": true,
                    "order": [[ 21, "desc" ]],
                    "ajax": {
                        url: "<?php echo route('admin.get_bank_payment_list'); ?>",
                        type: "GET",
                    },
                    "columns": [
                        {"targets": 0, "searchable": true, "data": "payment_options"},
                        {"targets": 1, "searchable": true, "render": function (data, type, row) {
                            if (row.budhet_sheet_no) {
                                return row.budhet_sheet_no;
                            } else {
                                return "N/A";
                            }
                        }},
                        {"targets": 2, "searchable": true, "data": "entry_code"},
                      
                        {"targets": 3, "searchable": true, "data": "user_name"},
                        {"targets": 4, "searchable": true, "data": "company_name"},
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
                        {"targets": 6, "searchable": true, "data": "project_name"},
                        {"targets": 7, "searchable": true, "data": "other_project_detail"},
                        {"targets": 8, "searchable": true, "data": "site_name"},
                        {"targets": 9, "searchable": true, "data": "vendor_name"},
                        {"targets": 10, "searchable": true, "data": "note"},
                        {"targets": 11,
                            "render": function (data, type, row) {
                                if (row.vendor_bank_name)
                                {
                                    return row.vendor_bank_name + " (" + row.ac_number + ")";
                                } else
                                {
                                    return "N/A";
                                }
                            }
                        },
                        {"targets": 12, "searchable": true, "data": "bank_name"},
                        {"targets": 13, "searchable": true, "data": "ch_no"},
                        {"targets": 14, "searchable": true, 
                            "render": function (data, type, row) {
                                if (row.total_amount) {
                                    return  Number(parseFloat(row.total_amount).toFixed(2)).toLocaleString('en', {
                                            minimumFractionDigits: 2
                                        });
                                }
                                
                            }
                        },
                        {"targets": 15, "searchable": true, 
                            "render": function (data, type, row) {
                                if (row.amount) {
                                    return  Number(parseFloat(row.amount).toFixed(2)).toLocaleString('en', {
                                            minimumFractionDigits: 2
                                        });
                                }
                                
                            }
                        },
                        {"targets": 16, "searchable": true, "render":function(data,type,row){
                                if(row.main_entry){
                                    return row.entry_completed;
                                }
                                else{
                                    return "";
                                }
                        }},
                        {
                            "targets": 17,
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

                                //return leaveStatus[row.leave_status];
                            }   
                        },
                        {
                            "targets": 18,
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

                                //return leaveStatus[row.leave_status];
                            }
                        },
                        {
                            "targets": 19,
                            "searchable": false,
                            "render": function(data, type, row) {

                                if (row.third_approval_status == 'Pending') {
                                    return '<b class="text-warning">Pending</b>';
                                } else if (row.third_approval_status == 'Approved') {
                                    return '<b class="text-success">Approved</b>';
                                } else if (row.third_approval_status == 'Canceled') {
                                    return '<b class="text-danger">Canceled</b>';
                                } else {
                                    return '<b class="text-danger">Rejected</b>';
                                }

                                //return leaveStatus[row.leave_status];
                            }
                        },
                        {"targets": 20,
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
                                    return '<input type="hidden" id="reject_note_' + row.id + '" value="' + row.reject_note + '" /><a class="btn btn-danger" data-toggle="modal" data-target="#reject_note_modal" href="#" onclick="show_reject_note(' + row.id + ');">Rejected</a>';
                                }
                            }
                        },
                        {
                            "targets": 21,
                            "searchable": true,
                            "render": function(data, type, row) {
                                var out = '';
                                if (row.created_at) {
                                    out+='<span style="display: none;">'+ row.created_at +'</span>';
                                    return out+= moment(row.created_at).format("DD-MM-YYYY");
                                } 
                            }
                        },
                        {"targets": 22, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out = "";

                                if (row.second_approval_status == 'Pending') {
                                    if (($.inArray('2', access_rule) !== -1)) {
                                        out = '<a href="<?php echo url("edit_bank_payment_detail") ?>' + '/' + id + '" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>';
                                    }
                                }
                                // if (($.inArray('2',access_rule) !== -1)) {
                                //     out+= '<a href="<?php echo url("delete_policy") ?>'+'/'+id+'" class="btn btn-primary btn-rounded"><i class="fa fa-times"></i></a>';
                                // }
                                if (row.payment_file) {
                                    var file_path = row.payment_file.replace("public/", "");
                                    var download_link = "{{ url('/storage/') }}" + '/' + file_path;
                                    out += '<a href="' + download_link + '" title="Download Bank Payment File" class="btn btn-rounded btn-primary" target="_blank" download><i class="fa fa-download"></i></a>';
                                }
                                if (row.invoice_file) {
                                    var file_path = row.invoice_file.replace("public/", "");
                                    var download_link = "{{ url('/storage/') }}" + '/' + file_path;
                                    out += '<a href="' + download_link + '" title="Download Invoice File" class="btn btn-rounded btn-primary" target="_blank" download><i class="fa fa-download"></i></a>';
                                }
                                
                                out += '<a href="#" onclick="get_bank_payment_files(' + row.id +','+"'"+row.status+"'"+ ');" title="View Files" id="showFiles" data-target="#bankPaymentFilesModel" data-toggle="modal" class="btn btn-primary btn-rounded"><i class="fa fa-eye"></i></a>';
                                
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
        </script>
        @endsection