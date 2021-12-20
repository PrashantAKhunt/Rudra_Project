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
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
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
                <p class="text-muted m-b-30"></p>
                <br>

                <div class="table-responsive">
                    <table id="project_table" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Project name</th>
                                <th>Company name</th>
                                <th>Client Name</th>
                                <th>Created date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
            <!--row -->

        </div>
        <!-- Modal -->
        <div id="view_project_managers" class="modal fade" role="dialog">
            <div class="modal-dialog">

                <!-- Modal content-->
                <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Project Manager / Employee</h4>
                </div>
                <div class="modal-body">
                    <div id="put_managers" class="table-responsive"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
                </div>

            </div>
        </div>
        @endsection
        @section('script')
        <script>
            $(document).ready(function () {
                var check_permission = <?php echo json_encode($view_special_permission); ?>;
                var table = $('#project_table').DataTable({
                    dom: 'lBfrtip',
                    buttons: ['excel'],
                    "processing": true,
                    "serverSide": true,
                    "responsive": true,
                    "stateSave": true,
                    "order": [[4, "DESC"]],
                    "ajax": {
                        url: "<?php echo route('admin.get_project_list_last'); ?>",
                        type: "GET",
                    },
                    "columns": [
                        
                        {"taregts": 0, 'data': 'project_name'
                        },
                        {"taregts": 1, 'data': 'company_name'
                        },
                        {"taregts": 2, 'data': 'client_name'
                        },
                        {"taregts": 3, "render":function(data,type,row){
                            return moment(row.created_at).format("DD-MM-YYYY");
                        }
                        },
                        {"taregts": 4, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out="";
                                if (check_permission) {
                                    out = '<a href="#" data-href="<?php echo url('approve_confirm_last') ?>'+'/'+id+'" onclick="approve_confirm(this);"  title="change status" class="btn btn-success btn-rounded"><i class="fa fa-check-circle" aria-hidden="true"></i></a>';
                                //    out = <a href="#" data-href="<?php echo url('approve_confirm_last/' . 'id') ?>" onclick="approve_confirm(this);" class="btn btn-success btn-rounded" title="Change Status"><i class="fa fa-check" aria-hidden="true"></i></a>;
                                }
                                /*out += '&nbsp;<a onclick="delete_confirm(this);" class="btn btn-danger btn-rounded" href="#" data-href=\'<?php echo url('delete_project'); ?>/' + id + '\'\n\
                                title="Delete"><i class="fa fa-trash"></i></a>';*/

                                // out += '&nbsp;<button type="button" id="'+id+'" onclick="viewProjectManager(this.id)" class="btn btn-danger btn-rounded" title="Delete"><i class="fa fa-times" aria-hidden="true"></i></button>';
                                // out += '<a href="<?php echo url('delete_project_last') ?>'+'/'+id+'" class="btn btn-danger btn-rounded" title="Reject/Delete"><i class="fa fa-times" aria-hidden="true"></i></a>';
                                out += '<a href="#" data-href="<?php echo url('delete_project_last') ?>'+'/'+id+'" onclick="delete_confirm(this);"  title="Reject/Delete" class="btn btn-danger btn-rounded"><i class="fa fa-times" aria-hidden="true"></i></a>';
                            //    uncommnet above
                                return out;
                                
                            }
                        },
                    ]

                });
            })

                // approve start
            function approve_confirm(e) {
                        swal({
                        title: "Are you sure you want to approve ?",
                                type: "warning",
                                showCancelButton: true,
                                confirmButtonColor: "#DD6B55",
                                confirmButtonText: "Yes",
                                closeOnConfirm: false
                        }, function () {
                        window.location.href = $(e).attr('data-href');
                        });
                    }

                    // end of approve
        function delete_confirm(e) {
            swal({
                //  msg =  Are you sure you want to delete/cancle\reject the project request ?
                title: "Are you sure you want to delete request ?",
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

        function viewProjectManager(id){
            $.ajax({
                type : "POST",
                url : "{{url('get_project_list_last')}}",
                data : {
                    id : id,
                    "_token" : "{{csrf_token()}}"
                },
                success : function(data){
                    console.log(data);
                    $("#put_managers").html(data);
                    $("#view_project_managers").modal('show');
                }
            });
        }
        </script>
        @endsection
