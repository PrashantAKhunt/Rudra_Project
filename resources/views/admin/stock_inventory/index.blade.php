@extends('layouts.admin_app')

@section('content')
<style>
/* .modal-dialog{
  width:50%;
  margin: auto;
} */
</style>
<?php
use Illuminate\Support\Facades\Config; ?>
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
              
                <a href="{{ route('admin.add_inventory_stock_request') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Inventory Request</a>
               
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="row">
                    <div class="table-responsive">
                    <table id="emp_table" class="table table-striped">
                        <thead>
                        <th> Item Name</th>
                        <th >Details</th>
                        <th>Item Quantity</th>
                        <th>Employee name</th>
                        <th>Inventory Manager Approval</th>
                        <th>HR Approval</th>
                        <th>Admin Approval</th>
                        <th>Purchase Departent Approval</th>
                        <th>Created Date</th>
                        <th>Action</th>
                        </thead>
                    </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- purchase complete proof -->
        <div id="purchaseCompleteModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <form action="{{ route('admin.purchase_completion') }}" method="POST" id="purchase_complete_form" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                <h3 class="panel-title">Purchase Complete Form</h3>
                            </div>
                            <div class="modal-body" id="userTable">
                            <input type="hidden" name="inventory_stock_request_id" id="inventory_stock_request_id" value=""  />
                                <div class="form-group ">
                                    <label>Item Quantity</label>
                                    <input type="number"  class="form-control" name="item_quantity" id="item_quantity" value=""  />

                                </div>

                                <div class="form-group ">
                                    <label>Price</label>
                                    <input type="number" class="form-control" name="price" id="price" value="" />

                                </div>

                                <div class="form-group ">
                                    <label>Attach proof Image</label>
                                    <input type="file" class="form-control" name="proof" id="proof" value="" />

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
        <!-- purchase complete proof -->

        <!-- purchase  proof details -->
        <div id="purchaseProofModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">

                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h3 class="panel-title">Purchase Completion details</h3>
                        </div>
                        <div class="modal-body">

                            <div class="row">
                                <div class="col-sm-6">
                                <input type="text" class="hidden" name="proof_inventory_stock_request_id" id="proof_inventory_stock_request_id" value="">
                                    <div class="form-group ">
                                        <label>Item Quantity</label>
                                        <p id="proof_item_quantity"></p>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="form-group ">
                                        <label>Total Price</label>
                                        <p id="proof_item_price"></p>
                                </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group ">
                                        <label>Proof Image</label>
                                        <p id="proof_item_image"></p>
                                    </div>
                                </div>
                            </div>
                          
                        </div>

                        <div class="col-md-12 pull-left" id="purchse_confirm_button">
                                <div class="clearfix"></div>
                                <br>
                                <button type="button" onclick="confirmPurchase()"  class="btn btn-danger">Confirm and add item to inventory</button>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-info waves-effect" data-dismiss="modal">Close</button>
                        </div>
                    </div>

                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
        <!-- purchase  proof details  -->
    </div>

    <!-- sample modal content -->
    <!--  -->

</div>
@endsection


@section('script')
<script>



$(document).ready(function(){

    jQuery("#purchase_complete_form").validate({
        ignore: [],
        rules: {
            item_quantity: {
                required: true,
            },
            price: {
                required: true,
            },
            proof: {
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
        "order": [[8, "DESC"]],
        //stateSave: true,
        "ajax": {
            url: "<?php echo route('admin.inventory_stock_requests_list_ajax'); ?>",
            type: "GET",
        },
        "columns": [
            {"taregts": 0, 'data': 'item_name'
            },
            {"taregts": 1, 'data': 'detail'
            },
            {"taregts": 2, 'data': 'item_quantity'
            },
            {"taregts": 3, 'data': 'emp_name'
            },
            {"taregts": 4,
                            "render": function (data, type, row) {
                                var out = '';
                                if (row.inventory_manager_approval == 'Pending')
                                {
                                    out +='<b class="text-warning">Pending</b>';
                                } else if (row.inventory_manager_approval == 'Processing')
                                {
                                    out += '<b class="text-success">Approved</b>';
                                out += '<br>';
                                    if (row.inventory_manager_approval_datetime) {
                                        out += moment(row.inventory_manager_approval_datetime).format("DD-MM-YYYY h:mm:ss a");
                                    }
                                }  else if (row.inventory_manager_approval == 'Completed')  {
                                    out += '<b class="text-info">Confirmed</b>';
                                    out += '<br>';
                                    if (row.inventory_manager_approval_datetime) {
                                        out += moment(row.inventory_manager_approval_datetime).format("DD-MM-YYYY h:mm:ss a");
                                    }
                                } else
                                {
                                    out += '<b class="text-danger">Rejected</b>';
                                }

                                return out;
                            }
            },
            {"taregts": 5, 'data': 'hr_approval',
                "render": function (data, type, row) {
                                var out = '';
                                if (row.hr_approval == 'Pending')
                                {
                                    out +='<b class="text-warning">Pending</b>';
                                } else if (row.hr_approval == 'Processing')
                                {
                                    out += '<b class="text-success">Approved</b>';
                                    out += '<br>';
                                    if (row.hr_approval_datetime) {
                                        out += moment(row.hr_approval_datetime).format("DD-MM-YYYY h:mm:ss a");
                                    }
                                } else
                                {
                                    out += '<b class="text-danger">Rejected</b>';
                                }
                                return out;
                            }},
            {"taregts": 6, 'data': 'third_approval',
                "render": function (data, type, row) {
                                var out = '';
                                if (row.third_approval == 'Pending')
                                {
                                    out +='<b class="text-warning">Pending</b>';
                                } else if (row.third_approval == 'Processing')
                                {
                                    out += '<b class="text-success">Approved</b>';
                                    out += '<br>';
                                    if (row.third_approval_datetime) {
                                        out += moment(row.third_approval_datetime).format("DD-MM-YYYY h:mm:ss a");
                                    }
                                } else
                                {
                                    out += '<b class="text-danger">Rejected</b>';
                                }
                                return out;
                            }},
            {"taregts": 7, 'data': 'purchase_approval',
                "render": function (data, type, row) {
                                var out = '';
                                if (row.purchase_approval == 'Pending')
                                {
                                    out +='<b class="text-warning">Pending</b>';
                                } else if (row.purchase_approval == 'Processing')
                                {
                                    out += '<b class="text-success">Approved</b>';
                                    out += '<br>';
                                    if (row.purchase_approval_datetime) {
                                        out += moment(row.purchase_approval_datetime).format("DD-MM-YYYY h:mm:ss a");
                                    }
                                } else if (row.purchase_approval == 'Purchased')
                                {
                                    out += '<b class="text-primary">Purchased</b>';
                                    out += '<br>';
                                    if (row.purchase_approval_datetime) {
                                        out += moment(row.purchase_approval_datetime).format("DD-MM-YYYY h:mm:ss a");
                                    }
                                } else
                                {
                                    out += '<b class="text-danger">Rejected</b>';
                                }
                                return out;
                            }},
            {"taregts": 8, 'data': 'created_at'},
            {"taregts": 9, "searchable": false, "orderable": false,
                "render": function (data, type, row) {
                    var id = row.id;
                    var out="";
                    var role = "<?php echo Auth::user()->role; ?>";
                    var userId = "<?php echo Auth::user()->id; ?>";
                    var hrRole = "<?php echo config('constants.REAL_HR'); ?>";
                    var adminUser = "<?php echo config('constants.Admin'); ?>";
                    var inventoryUser = "<?php echo $invemtory_manager; ?>";
                    var purchaseUser = "<?php echo $purchase_manager; ?>";

                    
                if (role == hrRole && row.inventory_manager_approval == 'Processing' && row.hr_approval == 'Pending') {
                    out += ' <button type="button" title="approve" onclick=confirmPayment("<?php echo url("stock_request_approval") ?>' + '/' + row.id + '") class="btn btn-success btn-circle"><i class="fa fa-check"></i> </button>';
                   } else if (role == adminUser && row.hr_approval == 'Processing' && row.third_approval == 'Pending') {
                    out += ' <button type="button"  title="approve" onclick=confirmPayment("<?php echo url("stock_request_approval") ?>' + '/' + row.id + '") class="btn btn-success btn-circle"><i class="fa fa-check"></i> </button>';
                   } else if (userId == purchaseUser && row.third_approval == 'Processing' && row.purchase_approval == 'Pending') {
                    out += ' <button type="button"  title="approve" onclick=confirmPayment("<?php echo url("stock_request_approval") ?>' + '/' + row.id + '") class="btn btn-success btn-circle"><i class="fa fa-check"></i> </button>';
                   }  else if (userId == purchaseUser && row.purchase_approval == 'Processing') {
                    out += ' <button type="button"  data-toggle="modal" data-target="#purchaseCompleteModal" onclick=completePurchase(' + row.id + ') class="btn btn-info btn-circle"><i class="fa fa-check"></i> </button>';
                   }

                   if( row.purchase_approval == 'Purchased') {
                   
                    out += ' <button type="button" title="view purchase details" onclick=purchaseProofDetails("' + row.id + '","' + row.purchase_approval + '","' + row.inventory_manager_approval + '"); class="btn btn-info btn-circle"><i class="fa fa-eye"></i> </button>';
                   }
                    return out;
                }
            },
        ]

    });

   
});

    // confirm
    function confirmPayment(url) {
        swal({
                title: "Are you sure you want to confirm ?",
                //text: "You want to change status of admin user.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
            }, function() {
                location.href = url;
            });
    }
    //purchase complate
    function completePurchase(id) {
        $('#inventory_stock_request_id').val(id)
    }
    //purchase proof
    function purchaseProofDetails(id, status , type) {
        var authUser = "<?php echo Auth::user()->id; ?>";
        var inventoryUser = "<?php echo $invemtory_manager; ?>";
        $('#purchse_confirm_button').hide();

        $.ajax({
                url: "{{ route('admin.get_purchase_proof_details')}}",
                type: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: {
                    id :id,
                },
                dataType: "JSON",
                success: function(data, textStatus, jQxhr) {

                    if(data.status) {
                        var proof_details = data.data;
                        $("#proof_inventory_stock_request_id").val(id);
                        $("#proof_item_quantity").text(proof_details.item_quantity);
                        $("#proof_item_price").text(proof_details.price);
                        let downloadHtml = '<a title="Download Cover image" href="'+proof_details.proof_image+'" download=""><i class="fa fa-cloud-download  fa-lg"></i></a>';
                        $("#proof_item_image").html(downloadHtml);
                        if(status == 'Purchased' && type == 'Processing' && authUser == inventoryUser) {
                            $('#purchse_confirm_button').show();
                        }
                        $("#purchaseProofModal").modal('toggle');
                    } else {
                        alert('Oops, Something went wrong!');
                    }

                },
                error: function(jqXhr, textStatus, errorThrown) {
                    alert('Oops, Something went wrong!');
                    console.log(errorThrown);
                }
            });
    }
    //confirm purchase
    function confirmPurchase() {
        var id = $("#proof_inventory_stock_request_id").val();
        swal({
                title: "Are you sure you want to confirm and add items in inventoy ?",
                //text: "You want to change status of admin user.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
            }, function() {
                location.href = '{{ url("purchase_cofirmed_by_inventory_manager/") }}'+'/'+id;
            });
    }


</script>
@endsection
