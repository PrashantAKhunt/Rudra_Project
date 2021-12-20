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
                <div class="table-responsive">
                    <table id="tender_payment_request" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Tender Sr no</th>
                                <th>Amount</th>
                                <th>In Favour Of</th>
                                <th>In Form Of</th>
                                <th>Tender</th>
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
        @endsection

        @section('script')
        <script>

            $(document).ready(function () {

                var table = $('#tender_payment_request').DataTable({
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    //"stateSave": true,
                    "order": [[ 6, "desc" ]],
                    "ajax": {
                        url: "<?php echo route('admin.get_tender_payment_request_list'); ?>",
                        type: "GET",
                    },
                    "columns": [

                        {"targets": 0, "searchable": true, "data": "tender_sr_no"},
                        {"targets": 1, "searchable": true,
                            "render": function(data, type, row) {
                                if (row.tender_type == 'fee') {
                                    return row.tender_fee_amount;
                                } else {
                                    return row.tender_emd_amount;
                                }
                            }
                        },
                        {"targets": 2, "searchable": true,
                            "render": function(data, type, row) {
                                if (row.tender_type == 'fee') {
                                    return row.tender_fee_in_favour_of;
                                } else {
                                    return row.tender_emd_in_favour_of;
                                }
                            }
                        },
                        {"targets": 3, "searchable": true, "data": "tender_fee_in_form_of",
                            "render": function(data, type, row) {
                                if (row.tender_type == 'fee') {
                                    return row.tender_fee_in_form_of;
                                } else {
                                    return row.tender_emd_in_form_of;
                                }
                            }
                        },
                        {"targets": 4, "searchable": true, "data": "tender_type"},
                        {
                            "targets": 5,
                            "searchable": false,
                            "render": function(data, type, row) {

                                if (row.payment_status == 'Pending') {
                                    return '<b class="text-warning">Pending</b>';
                                } else {
                                    return '<b class="text-success">Success</b>';
                                }

                                //return leaveStatus[row.leave_status];
                            }
                        },
                        {
                            "targets": 6,
                            "searchable": true,
                            "render": function(data, type, row) {
                                var out = '';
                                if (row.created_at_added) {
                                    out+='<span style="display: none;">'+ row.created_at_added +'</span>';
                                    return out+= moment(row.created_at_added).format("DD-MM-YYYY");
                                }
                            }
                        },
                        {"targets": 7, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out = "";
                                if(row.payment_status == 'Pending'){
                                    out += '<a href="<?php echo url("add_bank_payment_detail")?>/'+row.tender_id+'/'+row.tender_type+'" title="Bank Payment" id="showFiles" class="btn btn-primary btn-rounded"><i class="fa fa-credit-card" aria-hidden="true"></i></a>';
                                }
                                return out;
                            }
                        }
                    ]
                });
            })
        </script>
        @endsection
