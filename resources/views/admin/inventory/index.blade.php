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
              
                <a href="{{ route('admin.add_inventory_item') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Stationary Item</a>
               
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="row">
                    <div class="table-responsive">
                    <table id="emp_table" class="table table-striped">
                        <thead>
                        <th> Item Name</th>
                        <th >Item Details</th>
                        <th>Item Quantity</th>
                        <th>Created Date</th>
                        <th>Updated Date</th>
                        <th>Action</th>
                        </thead>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- sample modal content -->
    <!--  -->

</div>
@endsection


@section('script')
<script>



$(document).ready(function(){
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
        "order": [[1, "DESC"]],
        stateSave: true,
        "ajax": {
            url: "<?php echo route('admin.inventory_items_list_ajax'); ?>",
            type: "GET",
        },
        "columns": [
            {"taregts": 0, 'data': 'item_name'
            },
            {"taregts": 1, 'data': 'item_detail'
            },
            {"taregts": 2, 'data': 'item_quantity'
            },
            {"taregts": 3, 'data': 'created_at'},
            {"taregts": 4, 'data': 'updated_at'},
            {"taregts": 4, "searchable": false, "orderable": false,
                "render": function (data, type, row) {
                    var id = row.id;
                    var out="";
                    
                    out += '<a href="<?php echo url("item_access_request_list") ?>' + '/' + id + '"  title="view access requests" class="btn btn-primary btn-circle"><i class="fa fa-check-circle"></i></a>';

                    out += '&nbsp;<a href="<?php echo url("item_return_request_list") ?>' + '/' + id + '"  title="view return requests" class="btn btn-info btn-circle"><i class="fa fa-exchange"></i></a>';

                    return out;
                }
            },
        ]

    });
});


</script>
@endsection
