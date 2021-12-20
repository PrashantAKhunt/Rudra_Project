@extends('layouts.admin_app')

@section('content')
<div class="container-fluid">
    <div class="row bg-title">
        <div class="col-lg-3 col-md-4 col-sm-4 col-xs-12">
            <h4 class="page-title">{{ $page_title }}</h4>
        </div>
        <div class="col-lg-9 col-sm-8 col-md-8 col-xs-12">

            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Dashboardds</a></li>

                <li><a href="#">{{ $page_title }}</a></li>
            </ol>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-md-12">
            <div class="white-box">
        
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="row">
                    <div class="table-responsive">
                        <table id="emp_table" class="table table-striped">
                            <thead>
                            <th>Assigner Name</th>
                            <th>Asset Name</th>
                            
                            <th>Receiver Name</th>
                            <th>Assign Date</th>
                            <th>Currently Assigned</th>
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
        <form method="post" action="{{ route('admin.reject_asset_assigned') }}">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title" id="myModalLabel">Reject Reason</h4>
                </div>
                <div class="modal-body" id="tableBody">
                    <input type="hidden" name="reject_asset_id" id="reject_asset_id" />
                    <textarea class="form-control" required name="reject_reason" id="reject_reason"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger" >Submit</button>
                </div>
            </div>
        </form>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

@endsection


@section('script')
<script>
    
    
    function asset_reject(id) {
        $('#reject_asset_id').val(id);
    }

    var table = $('#emp_table').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "stateSave": true,
        "order": [[2, "DESC"]],
        "ajax": {
            url: "<?php echo route('admin.hr_access_request_list'); ?>",
            type: "GET",
        },
        "columns": [
            {"taregts": 0, 'data': 'user_name'
            },
            {"taregts": 1, 'data': 'asset_name'
            },
            {"taregts": 2, 'data': 'receiver_name'
            },
            {"taregts": 3, "render": function (data, type, row) {

                    return moment(row.asset_access_date).format("DD-MM-YYYY");
                }
            },
            {"taregts": 4, "render": function (data, type, row) {
                    var out = '';
                    if (row.is_allocate) {
                        return '<span class="text-success">Yes</span>';
                    } else {
                        return '<span class="text-danger">No</span>';
                    }
                }
            },
            {"taregts": 5,
                "render": function (data, type, row) {
                   

                        out = '<a onclick="asset_confirm(this);" data-href="<?php echo url('change_asset_access'); ?>/' + row.id + '/1" class="btn btn-success" title="Accept">Confirm</a>';
                        out += ' <a onclick="asset_reject(' + row.id + ');" data-toggle="modal" data-target="#reject_modal"  class="btn btn-danger" title="Reject">Reject</a>';
                        return out;
            
                }
            }
        
        ]

    });


    function asset_confirm(e) {
        swal({
            title: "Are you sure you want change status ?",
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
