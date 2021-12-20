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
                                <th>User Name</th>
                                <th>Company Name</th>
                                <th>Project Name</th>
                                <th>Other Project Detail</th>
                                <th>Vendor Name</th>
                                <th>Work Detail</th>
                                <th>Vendor/Party Bank Details</th>
                                <th>Bank Name</th>
                                <th>Cheque Number</th>
                                <th>Amount</th>
                                <th>Status</th>
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

        @endsection

        @section('script')		
        <script>

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
                    "stateSave": true,
                    "ajax": {
                        url: "<?php echo route('admin.get_bank_payment_list'); ?>",
                        type: "GET",
                    },
                    "columns": [
                        {"taregts": 0, "searchable": true, "data": "user_name"},
                        {"taregts": 1, "searchable": true, "data": "company_name"},
                        {"taregts": 2, "searchable": true, "data": "project_name"},
                        {"taregts": 3, "searchable": true, "data": "other_project_detail"},
                        {"taregts": 4, "searchable": true, "data": "vendor_name"},
                        {"taregts": 5, "searchable": true, "data": "note"},
                        {"taregts": 6,
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
                        {"taregts": 7, "searchable": true, "data": "bank_name"},
                        {"taregts": 8, "searchable": true, "data": "ch_no"},
                        {"taregts": 9, "searchable": true, "data": "amount"},
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
                                    return '<input type="hidden" id="reject_note_' + row.id + '" value="' + row.reject_note + '" /><a class="btn btn-danger" data-toggle="modal" data-target="#reject_note_modal" href="#" onclick="show_reject_note(' + row.id + ');">Rejected</a>';
                                }
                            }
                        },
                        {"taregts": 11, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out = "";

                                if (($.inArray('2', access_rule) !== -1)) {
                                    out = '<a href="<?php echo url("edit_bank_payment_detail") ?>' + '/' + id + '" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>';
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