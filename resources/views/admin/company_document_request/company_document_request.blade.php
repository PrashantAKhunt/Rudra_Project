<?php
use Illuminate\Support\Facades\Auth;
?>

@extends('layouts.admin_app')

@section('content')
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">{{ $page_title }}</h4>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Dashboards</a></li>
                <li><a href="#">{{ $page_title }}</a></li>
            </ol>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="white-box">
                <a href="{{ route('admin.add_company_document_request') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Company Document Request</a>
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="row">
                    <div class="table-responsive">
                        <table id="request_table" class="table table-striped">
                            <thead>
                                <th>Company Name</th>
                                <th>Document Name</th>
                                <th>Receiver Name</th>
                                <th>Work Detail</th>
                                <th>Require Date</th>
                                <th>Return Date</th>
                                <th>Request Date</th>
                                <th>Confirm Submitted Date</th>
                                <th>Actual Returned Date</th>
                                <th>Return Receive Date</th>
                                <th>Superadmin Status</th>
                                <th>Superadmin Approved Date</th>
                                <th>Custodian Status</th>
                                <th>Custodian Approved Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="reject_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" action="{{ route('admin.reject_company_document_request') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Reject Reason</h4>
                </div>
                <div class="modal-body" id="tableBody">
                    <input type="hidden" name="reject_id" id="reject_id" />
                    <input type="hidden" name="reject_by" id="reject_by" />
                    <textarea class="form-control" required name="reject_reason" id="reject_reason"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger" >Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="approve_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" action="{{ route('admin.approve_company_document_request_by_admin') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Return Date</h4>
                </div>
                <div class="modal-body" id="tableBody">
                    <input type="hidden" name="approve_id" id="approve_id" />
                    <input type="text" class="form-control" name="return_date" id="return_date" autocomplete="off" />
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger" >Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('script')
<script>
    function document_request_reject(id,reject_by) {
        $('#reject_id').val(id);
        $('#reject_by').val(reject_by);
    }
    jQuery('#return_date').datepicker({
        autoclose: true,
        todayHighlight: true,
        format: "dd-mm-yyyy"
    });
    function document_request_approve(id) {
        $('#approve_id').val(id);
    }

    var table = $('#request_table').DataTable({
        dom: 'lBfrtip',
        buttons: ['excel'],
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "stateSave": true,
        "order": [[3, "DESC"]],
        "ajax": {
            url: "<?php echo route('admin.get_company_document_request'); ?>",
            type: "GET",
        },
        "columns": [
            {"targets": 0, "data": 'company_name'},
            {"targets": 1, "data": 'document_title'},
            {"targets": 2, "data": 'user_name'},
            {"targets": 3, "data": 'work_detail'},
            {"targets": 4,"data": "require_date", "render": function (data, type, row) {
                    return moment(row.require_date).format("DD-MM-YYYY");
                }
            },
            {"targets": 5, "data": "return_date", "render": function (data, type, row) {
                    return moment(row.return_date).format("DD-MM-YYYY");
                }
            },
            {"targets": 6, "data": "request_datetime",  "render": function (data, type, row) {
                    return moment(row.request_datetime).format("DD-MM-YYYY");
                }
            },
            {"targets": 7, "data": "confirm_submitted_date", "render": function (data, type, row) {
                    if(row.confirm_submitted_date)
                        return moment(row.confirm_submitted_date).format("DD-MM-YYYY");
                    else
                        return "---";
                }
            },
            {"targets": 8, "data": "actual_return_date", "render": function (data, type, row) {
                    if(row.actual_return_date)
                        return moment(row.actual_return_date).format("DD-MM-YYYY");
                    else
                        return "---";
                }
            },
            {"targets": 9, "data": "return_received_datetime", "render": function (data, type, row) {
                    if(row.return_received_datetime)
                        return moment(row.return_received_datetime).format("DD-MM-YYYY");
                    else
                        return "---";
                }
            },

            {"targets": 10, "data": "superadmin_status", "render": function (data, type, row) {
                    if (row.superadmin_status == 'Pending') {
                        return '<b class="text-warning">Pending</b>';
                    } else if (row.superadmin_status == 'Approved') {
                        return '<b class="text-success">Approved</b>';
                    } else if (row.superadmin_status == 'Rejected') {
                        return '<b class="text-danger">Rejected</b>';
                    }
                }
            },
            {"targets": 11, "data": "superadmin_approval_datetime", "render": function (data, type, row) {
                    if(row.superadmin_approval_datetime)
                        return moment(row.superadmin_approval_datetime).format("DD-MM-YYYY HH:mm:ss");
                    else
                        return "---";
                }
            },
            {"targets": 12, "data": "custodian_approval_status", "render": function (data, type, row) {
                    if (row.custodian_approval_status == 'Pending') {
                        return '<b class="text-warning">Pending</b>';
                    } else if (row.custodian_approval_status == 'Approved') {
                        return '<b class="text-success">Approved</b>';
                    } else if (row.custodian_approval_status == 'Received') {
                        return '<b class="text-info">Received</b>';
                    } else {
                        return '<b class="text-danger">Rejected</b>';
                    }
                }
            },
            {"targets": 13, "data": "custodian_approval_datetime", "render": function (data, type, row) {
                    if(row.custodian_approval_datetime)
                        return moment(row.custodian_approval_datetime).format("DD-MM-YYYY HH:mm:ss");
                    else
                        return "---";
                }
            },
            {"targets": 14, "data": "status", "render": function (data, type, row) {
                    if (row.status == 'Pending') {
                        return '<b class="text-warning">Pending</b>';
                    } else if (row.status == 'Approved') {
                        return '<b class="text-success">Approved</b>';
                    } else if (row.status == 'Submitted') {
                        return '<b class="text-info">Submitted</b>';
                    } else if (row.status == 'Returned') {
                        return '<b class="text-info">Returned</b>';
                    } else if (row.status == 'Received') {
                        return '<b class="text-info">Received</b>';
                    } else {
                        return '<b class="text-danger">Rejected</b>';
                    }
                }
            },

            {"targets": 15,"searchable": false, "orderable": false,
                "render": function (data, type, row) {
                    var out = '';
                        if (row.superadmin_status == 'Pending' && {{ Auth::user()->id }} == row.request_user_id ){

                            out += '<a href="<?php echo url("edit_company_document_request") ?>' + '/' + row.id + '" class="btn btn-primary btn-rounded"><i class="fa fa-pencil"></i></a>';
                            out += '<a onclick="confirm(this);" data-text="Are you sure you want to delete document request?" data-href="<?php echo url('delete_company_document_request'); ?>/' + row.id + '" class="btn btn-danger btn-rounded" title="Delete Request"><i class="fa fa-trash"></i></a>';

                        }else if (row.request_status == 'Approved' && row.custodian_approval_status == 'Approved' && {{ Auth::user()->id }} == row.request_user_id ){

                            out += '<a onclick="confirm(this);" data-text="Are you sure you received document?" data-href="<?php echo url('received_company_document_by_requester'); ?>/' + row.id + '" class="btn btn-success btn-rounded" title="Received"><i class="fa fa-check"></i></a>';

                        }else if (row.request_status == 'Submitted' && {{ Auth::user()->id }} == row.request_user_id ){

                            out += '<a onclick="confirm(this);" data-text="Are you sure you want to return document?" data-href="<?php echo url('returned_company_document_by_requester'); ?>/' + row.id + '" class="btn btn-success btn-rounded" title="Returned"><i class="fa fa-share"></i></a>';

                        }
                        if (row.superadmin_status == 'Pending' && {{ Auth::user()->role }} == {{ config('constants.SuperUser') }} ){

                            out += ' <a onclick="document_request_approve(' + row.id + ');" data-toggle="modal" data-target="#approve_modal" class="btn btn-success btn-rounded" title="Approve"><i class="fa fa-check"></i></a>';
                            out += ' <a onclick="document_request_reject(' + row.id + ',&apos;admin&apos;);" data-toggle="modal" data-target="#reject_modal" class="btn btn-danger btn-rounded" title="Reject"><i class="fa fa-times"></i></a>';
                        }
                        if (row.superadmin_status == 'Approved' && row.custodian_approval_status == 'Pending' && {{ Auth::user()->id }} == row.custodian_id){

                            out += '<a onclick="confirm(this);" data-text="Are you sure you want to approve document?" data-href="<?php echo url('approve_company_document_request_by_custodian'); ?>/' + row.id + '" class="btn btn-success btn-rounded" title="Approve"><i class="fa fa-check"></i></a>';
                            out += ' <a onclick="document_request_reject(' + row.id + ',&apos;custodian&apos;);" data-toggle="modal" data-target="#reject_modal" class="btn btn-danger btn-rounded" title="Reject"><i class="fa fa-times"></i></a>';

                        } else if (row.request_status == 'Returned' && row.custodian_approval_status == 'Approved' && {{ Auth::user()->id }} == row.custodian_id){

                            out += '<a onclick="confirm(this);" data-text="Are you sure you received document?" data-href="<?php echo url('received_company_document_by_custodian'); ?>/' + row.id + '" class="btn btn-success btn-rounded" title="Received"><i class="fa fa-check"></i></a>';

                        }
                        return out;
                }
            }
        ]
    });

    function confirm(e) {
        swal({
            title: $(e).attr('data-text'),
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes",
            closeOnConfirm: false
        }, function () {
            window.location.href = $(e).attr('data-href');
        });
    }
</script>
@endsection
