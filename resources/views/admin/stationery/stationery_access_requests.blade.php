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
                 
                <a href="{{ route('admin.add_stationery_item_access_request') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Request</a>
                
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="row">
                    <div class="table-responsive">
                    <table id="emp_table" class="table table-striped">
                        <thead>
                        <th>Stationery Item Name</th>
                        <th>Requested Quantity</th>
                        <th>Approval Quantity</th>
                        <th>Reason Note</th>
                        <th>Request Status</th>
                        <th>Created Date</th>
                        <th>Action</th>
                        </thead>
                    </table>
                    </div>
                </div>
            </div>
        </div>
         <!-- approval form -->
         <div id="returnItemModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form action="{{ route('admin.add_returnItem_to_stock') }}" method="POST" id="item_return_form" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h3 class="panel-title">Return Item Form</h3>
                            </div>
                            <div class="modal-body" id="userTable">
                            <input type="hidden" name="access_request_id" id="access_request_id" value=""  />
                                <div class="form-group ">
                                    <label>Return Item Quantity</label>
                                    <input type="number"  class="form-control" name="return_quantity" id="return_quantity" value=""  />
                                </div>

                                <div class="form-group ">
                                    <label>Note</label>
                                    <textarea class="form-control" rows="3" name="return_note" id="return_note" ></textarea>
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
        <div id="details_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">Detail</h4>
                    </div>

                    <div class="modal-body" id="detail_div">

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

    <!-- sample modal content -->
    <!--  -->

</div>
@endsection


@section('script')
<script>

$(document).ready(function(){

    jQuery("#item_return_form").validate({
        ignore: [],
        rules: {
            return_quantity: {
                required: true,
            },
            return_note: {
                required: true,
            }
        }
    });

    $(document).delegate('*[data-toggle="lightbox"]:not([data-gallery="navigateTo"])', 'click', function(event) {
                    event.preventDefault();
                    return $(this).ekkoLightbox({
                        onShown: function() {
                            if (window.console) {
                                return console.log('Checking our the events huh?');
                            }
                        },
            onNavigate: function(direction, itemIndex) {
                            if (window.console) {
                                return console.log('Navigating '+direction+'. Current item: '+itemIndex);
                            }
            }
                    });
                });
         
    
    var table = $('#emp_table').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "order": [[5, "DESC"]],
        //stateSave: true,
        "ajax": {
            url: "<?php echo route('admin.stationery_access_requests_list_ajax'); ?>",
            type: "GET",
        },
        "columns": [
            {"taregts": 0, 'data': 'item_name' },
            {"taregts": 1, 'data': 'requested_quantity' },
            {"taregts": 2, 'data': 'approval_quantity' ,
                "render": function (data, type, row) {
                    if (row.approval_quantity) {
                        return row.approval_quantity;
                    } else {
                        return 'NA';
                    }
                }},
            {"taregts": 3, 'data': 'request_note' },
            {"taregts": 4, "render": function (data, type, row) {
                    if (row.request_user_status == 'Pending') {
                        return '<span class="label label-rouded label-warning">'+row.request_user_status+'</span>';
                    } else if (row.request_user_status == 'Accepted') {
                       
                        return '<input style="display:none" type="textarea" id="detail_' + row.id + '" value="' + row.approve_note + '" /><a class="label label-rouded label-success" data-toggle="modal" data-target="#details_modal" href="#" onclick="show_detail(' + row.id + ');">Approved</a>';
                    } else if (row.request_user_status == 'Rejected') {
                
                        return '<input style="display:none" type="textarea" id="detail_' + row.id + '" value="' + row.reject_note + '" /><a class="label label-rouded label-danger" data-toggle="modal" data-target="#details_modal" href="#" onclick="show_detail(' + row.id + ');">Rejected</a>';
                    }
                                
                }
            },
            {"taregts": 5, 'data': 'created_at'},
            {"taregts": 6, "searchable": false, "orderable": false,
                "render": function (data, type, row) {
                    var id = row.id;
                    var out="";

                    if (row.request_user_id == "{{Auth::user()->id}}" && row.request_user_status == "Pending") {

                        out += '&nbsp;<a onclick="delete_confirm(this);" class="btn btn-danger btn-rounded" href="#" data-href=\'<?php echo url('delete_stationery_item_access_request'); ?>/' + id + '\'\n\
                            title="Delete"><i class="fa fa-trash"></i></a>';

                    }

                    if (row.request_user_id == "{{Auth::user()->id}}" && row.request_user_status == "Accepted" && row.return_status == "Pending") {

                        out += '&nbsp;<button type="button" data-toggle="modal" data-target="#returnItemModal" title="retun item" onclick=returnConfim("' + row.id + '") class="btn btn-success btn-circle"><i class="fa fa-exchange"></i> </button>';

                    }


        
                    return out;
                }
            },
        ]

    });
});

function delete_confirm(e) {
    swal({
        title: "Are you sure you want to delete this request?",
        //text: "You want to change status of admin user.",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes",
        closeOnConfirm: false,
        showLoaderOnConfirm: true,
    }, function () {
        window.location.href = $(e).attr('data-href');
    });
}
//-----------------------------------------------
function returnConfim(id){
    $('#access_request_id').val(id);
}
//-----------------------------------------------
function show_detail(id) {
                $('#detail_div').html($('#detail_' + id).val());

            }
//-----------------------------------------------
function return_alert(e) {
    swal({
        title: "Are you sure you want to return this stationery item to custodian user ?",
        //text: "You want to change status of admin user.",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes",
        closeOnConfirm: false
    }, function () {
        window.location.href = $(e).attr('data-href');
    });
}
//------------------------------------------------
function accept_alert(e) {
    swal({
        title: "Are you sure you want to accept this request ?",
        //text: "You want to change status of admin user.",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes",
        closeOnConfirm: false
    }, function () {
        window.location.href = $(e).attr('data-href');
    });
}
//------------------------------------------------
function confirm_alert(e) {
    swal({
        title: "Are you sure you want to confirm ?",
        //text: "You want to change status of admin user.",
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
