@extends('layouts.admin_app')

@section('content')
<style>
/* .modal-dialog{
  width:50%;
  margin: auto;
} */
</style>
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
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-md-12">
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
            
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="row">
                    <div class="table-responsive">
                    <table id="emp_table" class="table table-striped">
                        <thead>
                        <th>Employee Name</th>
                        <th>Requested Quantity</th>
                        <th>Approal Quantity</th>
                        <th>Request Note</th>
                        <th>Status</th>
                        <th>Created Date</th>
                        <th>Action</th>
                        </thead>
                    </table>
                    </div>
                </div>
            </div>
        </div>
         <!-- approval form -->
         <div id="approvalModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form action="{{ route('admin.access_request_approval') }}" method="POST" id="approval_form" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h3 class="panel-title">Approval Form</h3>
                            </div>
                            <div class="modal-body" id="userTable">
                            <input type="hidden" name="access_request_id" id="access_request_id" value=""  />
                            <input type="hidden" name="inventory_id" id="inventory_id" value=""  />
                                <div class="form-group ">
                                    <label>Approval Item Quantity</label>
                                    <input type="number"  class="form-control" name="approval_quantity" id="approval_quantity" value=""  />
                                </div>

                                <div class="form-group ">
                                    <label>Note</label>
                                    <textarea class="form-control" rows="3" name="approval_note" id="approval_note" ></textarea>
                                </div>
                            </div>
                            <div class="col-md-12 pull-left">
                                <div class="clearfix"></div>
                                <br>
                                <button type="submit" class="btn btn-danger">Save</button>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </form>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
        <!-- approval form -->
         <!-- rejection form -->
         <div id="rejectionModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form action="{{ route('admin.access_request_rejection') }}" method="POST" id="rejection_form" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h3 class="panel-title">Rejection Form</h3>
                            </div>
                            <div class="modal-body" id="userTable">
                            <input type="hidden" name="access_request_entry_id" id="access_request_entry_id" value=""  />

                                <div class="form-group ">
                                    <label>Reject Note</label>
                                    <textarea class="form-control" rows="3" name="reject_note" id="reject_note" ></textarea>
                                </div>
                            </div>
                            <div class="col-md-12 pull-left">
                                <div class="clearfix"></div>
                                <br>
                                <button type="submit" class="btn btn-danger">Save</button>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </form>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
        <!-- rejection form -->

    </div>

    <!-- sample modal content -->
    <!--  -->

</div>
@endsection


@section('script')
<script>



$(document).ready(function(){

    jQuery("#approval_form").validate({
        ignore: [],
        rules: {
            approval_quantity: {
                required: true,
            },
            approval_note: {
                required: true,
            }
        }
    });

    jQuery("#rejection_form").validate({
        ignore: [],
        rules: {
            reject_note: {
                required: true,
            }
        }
    });
   
    var table = $('#emp_table').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "order": [[5, "DESC"]],
       // stateSave: true,
        "ajax": {
            url: "<?php echo route('admin.item_access_request_list_ajax'); ?>",
            type: "GET",
            data: {id: {{$id}} }
        },
        "columns": [
            {"taregts": 0, 'data': 'emp_name'},
            {"taregts": 1, 'data': 'requested_quantity'},
            {"taregts": 2, 'data': 'approval_quantity',
                "render": function (data, type, row) {
                    if(row.approval_quantity){
                        return row.approval_quantity;
                    } else {
                        return 'NA';
                    }
                }
            },
            {"taregts": 3, 'data': 'request_note'},
            {"taregts": 4,
                            "render": function (data, type, row) {
                                var out = '';
                                if (row.request_user_status == 'Pending')
                                {
                                    out +='<b class="text-warning">Pending</b>';
                                } else if (row.request_user_status == 'Accepted')
                                {
                                    out += '<b class="text-success">Approved</b>';
                                }  else if (row.request_user_status == 'Rejected')  {
                                    out += '<b class="text-danger">Rejected</b>';
                                }

                                return out;
                            }
            },
            {"taregts": 5, 'data': 'created_at'},
            {"taregts": 6, "searchable": false, "orderable": false,
                "render": function (data, type, row) {
                    var id = row.id;
                    var out="";

                    if(row.request_user_status == 'Pending') {
                        out += ' <button type="button" data-toggle="modal" data-target="#approvalModal" title="approve" onclick=approveConfim("' + row.access_request_id + '","' + row.id + '") class="btn btn-success btn-circle"><i class="fa fa-check"></i> </button>';
                        out += ' <button type="button" data-toggle="modal" data-target="#rejectionModal" title="reject" onclick=rejectConfim("' + row.access_request_id + '","' + row.id + '") class="btn btn-danger btn-circle"><i class="fa fa-close"></i> </button>';
                    }
                  
                    return out;
                }
            },
        ]

    });
});

function approveConfim(id,inventory_id ) {
        $('#access_request_id').val(id);
        $('#inventory_id').val(inventory_id);
    }


    function rejectConfim(id, inventory_id) {
        $('#access_request_entry_id').val(id);
    }


</script>
@endsection
