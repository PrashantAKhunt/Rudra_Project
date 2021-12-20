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
                        <th>Return Quantity</th>
                        <th>Return Note</th>
                        <th>Created Date</th>
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

   
    var table = $('#emp_table').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "order": [[5, "DESC"]],
        //stateSave: true,
        "ajax": {
            url: "<?php echo route('admin.item_return_request_list_ajax'); ?>",
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
            {"taregts": 3, 'data': 'return_quantity',
                "render": function (data, type, row) {
                    if(row.return_quantity){
                        return row.return_quantity;
                    } else {
                        return 'NA';
                    }
                }
            },
            {"taregts": 4, 'data': 'return_note'},
            {"taregts": 5, 'data': 'created_at'},
            {"taregts": 6, "searchable": false, "orderable": false,
                "render": function (data, type, row) {
                    var id = row.id;
                    var out="";

                    out += ' <button type="button" title="confirm return items" onclick=confirmEntry("' + row.id + '","' + row.access_request_id + '"); class="btn btn-info btn-circle"><i class="fa fa-check"></i> </button>';
                  
                    return out;
                }
            },
        ]

    });
});


function confirmEntry(id, request_id) {
        
        swal({
                title: "Are you sure you want to confirm and add items in inventoy stock ?",
                //text: "You want to change status of admin user.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                closeOnConfirm: false,
                showLoaderOnConfirm: true,
            }, function() {
                location.href = '{{ url("confirm_returnItem_to_stock/") }}'+'/'+id+'/'+request_id;
            });
    }


</script>
@endsection
