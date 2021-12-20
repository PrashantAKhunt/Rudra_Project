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
                @if($stationery_add_permission)
                <a href="{{ route('admin.add_stationery_items') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Stationery</a>
                @endif
                <p class="text-muted m-b-30"></p>
                <br>
                <div class="row">
                    <div class="table-responsive">
                    <table id="emp_table" class="table table-striped">
                        <thead>
                        <th>Name</th>
                        <th >Details</th>
                        <th>Image</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>First Approval User</th>
                        <th>Second Approval User</th>
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
    
    var check_permission = <?php echo json_encode($stationery_edit_permission); ?>;

    var table = $('#emp_table').DataTable({
        "processing": true,
        "serverSide": true,
        "responsive": true,
        "order": [[1, "DESC"]],
        stateSave: true,
        "ajax": {
            url: "<?php echo route('admin.stationery_items_list_ajax'); ?>",
            type: "GET",
        },
        "columns": [
            {"taregts": 0, 'data': 'item_name'
            },
            {"taregts": 1, 'data': 'item_detail'
            },
            {"taregts": 2,
                    "render": function (data, type, row) {
                        if(row.item_image==null)
                        {
                            return "No Image";
                        }
                        else
                        {
                            var img = row.item_image;
                            var baseURL = img.replace("public/","");
                            var url=  "<?php echo url('/storage/');?>"+"/"+baseURL;

                           return "<a href="+ url +" data-toggle='lightbox'><img height='100px' width='100px' src="+ url +"></a>";
                        }
                    }
            },
            {"taregts": 3, 'data': 'item_price'
            },
            {"taregts": 4,
                    "render": function (data, type, row) {
                        var id = row.id;
                        var out = '';
                        if(row.status=='Enabled'){
                        out += '<a href="<?php echo url('change_stationery_item_status') ?>'+'/'+id+'/Disabled'+'" class="btn btn-success" title="Change Status">'+row.status+'</a>';
                        }
                        else{
                        out += '<a href="<?php echo url('change_stationery_item_status') ?>'+'/'+id+'/Enabled'+'" class="btn btn-danger" title="Change Status">'+row.status+'</a>';    
                        }
                        return out;
                    }
            },
            {"taregts": 5, 'data': 'first_approval_user'},
            {"taregts": 6, 'data': 'second_approval_user'},
            {"taregts": 7, "searchable": false, "orderable": false,
                "render": function (data, type, row) {
                    var id = row.id;
                    var out="";
                    
                    if (check_permission) {
                        out = '<a href="<?php echo url('edit_stationery_items') ?>'+'/'+id+'" class="btn btn-primary btn-rounded" title="Edit"><i class="fa fa-edit"></i></a>';
        
                        out += '&nbsp;<a onclick="delete_confirm(this);" class="btn btn-danger btn-rounded" href="#" data-href=\'<?php echo url('delete_stationery_items'); ?>/' + id + '\'\n\
                        title="Delete"><i class="fa fa-trash"></i></a>';
                    }    
                    
                
                    return out;
                }
            },
        ]

    });
});

function delete_confirm(e) {
    swal({
        title: "Are you sure you want to delete Stationery ?",
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
