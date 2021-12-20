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
                    <a href="{{ route('admin.add_cash_payment_detail') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Cash Payment</a>
                <?php } ?>
                <p class="text-muted m-b-30"></p>
                <br>     
                <b class="error">Approval Flow: {{implode(' -> ',config('constants.CASH_PAYMENT_APPROVAL'))}}</b>
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="table-responsive">
                    <table id="policy_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Payment Title</th>
                                <th>Company Name</th>
                                <th>Client Name</th>
                                <th>Project Name</th>
                                <th>Other Project Detail</th>
                                <th>Project Site Name</th>
                                <th>Vendor Name</th>
                                <th>Amount</th>
                                <th>Payment Note</th>
                                <th>Accountant Status</th>
                                <th>Admin Status</th>
                                <th>Super Admin Status</th>
                                <th>Status</th>
                                <th>Created Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>                            
                        </tbody>
                    </table>
                </div>
            </div>        
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
            stateSave: true,
            order:[[8,'DESC']],
            "ajax": {
                url: "<?php echo route('admin.get_cash_payment_list'); ?>",
                type: "GET",
            },
            "columns": [
                {"taregts": 0, "searchable": true, "data": "title"},
                {"taregts": 1, "searchable": true, "data": "company_name"},
                {"targets": 2, "searchable": true, "render" : function(data, type, row){
                            if (row.client_name) {
                                if (row.client_name == 'Other Client') {
                                    return row.client_name ;
                                }else{
                                    return row.client_name + " (" + row.location + ")";
                                }
                                    
                                } else {
                                    return "N/A";
                                }
                                }},
                {"taregts": 3, "searchable": true, "data": "project_name"},
                {"taregts": 4, "searchable": true, "data": "other_cash_detail"},
                {"targets": 5, "searchable": true, "data": "site_name"},
                {"taregts": 6, "searchable": true, "data": "vendor_name"},
                {"taregts": 7, "searchable": true, "data": "amount"},
                {"taregts": 8, "searchable": true, "data": "note"},
                {
                            "taregts": 9,
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
                            "taregts": 10,
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
                        {
                            "taregts": 11,
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
                            return '<input type="hidden" id="reject_note_' + row.id + '" value="' + row.reject_note + '" /><a class="btn btn-danger" data-toggle="modal" data-target="#reject_note_modal" href="#" onclick="show_reject_note(' + row.id + ');">Rejected</a>';
                        }
                    }
                },
                {"taregts": 13, "searchable": true, "render": function (data, type, row) {
                        return moment(row.created_at).format("DD-MM-YYYY");
                    }
                },
                {"taregts": 14, "searchable": false, "orderable": false,
                    "render": function (data, type, row) {
                        var id = row.id;
                        var out = "";
                        if (($.inArray('2', access_rule) !== -1) && row.status != "Approved") {
                            out = '<a href="<?php echo url("edit_cash_payment_detail") ?>' + '/' + id + '" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>';
                        }
                        // if (($.inArray('2',access_rule) !== -1)) {
                        //     out+= '<a href="<?php //echo url("delete_policy")    ?>'+'/'+id+'" class="btn btn-primary btn-rounded"><i class="fa fa-times"></i></a>';
                        // }
                        if (row.payment_file) {
                            var file_path = row.payment_file.replace("public/", "");
                            var download_link = "{{ url('/storage/') }}" + '/' + file_path;
                            out += '<a href="' + download_link + '" title="Download Cash Payment File" class="btn btn-rounded btn-primary" target="_blank" download><i class="fa fa-download"></i></a>';
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