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

            @if(Auth::user()->role == config('constants.SuperUser'))
                <a href="{{ route('admin.project_update_request') }}" class="btn btn-primary pull-left"><i class="fa fa-plus"></i> Project Update Request</a>
            @endif

            @if($view_special_permission)
                <a href="{{ route('admin.add_project') }}" class="btn btn-primary pull-right"><i class="fa fa-plus"></i> Add Project</a>
            @endif
                <p class="text-muted m-b-30"></p>
                <br>

                <div class="table-responsive">
                    <table id="project_table" class="table table-striped">
                        <thead>
                            <tr>

                                <th>Project name</th>
                                <th>Company name</th>
                                <th>Client Name</th>
                                <th>Project Status</th>
                                <th>Status</th>
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
                        url: "<?php echo route('admin.get_project_list_all'); ?>",
                        type: "GET",
                    },
                    "columns": [

                        {"taregts": 0, 'data': 'project_name'
                        },
                        {"taregts": 1, 'data': 'company_name'
                        },
                        {"taregts": 2, 'data': 'client_name'
                        },
                        {"taregts": 3,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out = '';
                                if (check_permission) {
                                    if(row.project_type=='Current'){
                                    out += '<a href="<?php echo url('change_project_type') ?>'+'/'+id+'/Completed'+'" class="btn btn-success" title="Change Status">'+row.project_type+'</a>';
                                    }
                                    else{
                                    out += '<a href="<?php echo url('change_project_type') ?>'+'/'+id+'/Current'+'" class="btn btn-danger" title="Change Status">'+row.project_type+'</a>';
                                    }
                                } else {
                                    if(row.project_type=='Current'){
                                    out += '<a href="#" class="btn btn-success" title="Change Status">'+row.project_type+'</a>';
                                    }
                                    else{
                                    out += '<a href="#" class="btn btn-danger" title="Change Status">'+row.project_type+'</a>';
                                    }
                                }
                                return out;
                            }
                        },
                        {"taregts": 4,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out = '';
                                if (check_permission) {
                                    if(row.status=='Enabled'){
                                    out += '<a href="<?php echo url('change_project_status') ?>'+'/'+id+'/Disabled'+'" class="btn btn-success" title="Change Status">'+row.status+'</a>';
                                    }
                                    else{
                                    out += '<a href="<?php echo url('change_project_status') ?>'+'/'+id+'/Enabled'+'" class="btn btn-danger" title="Change Status">'+row.status+'</a>';
                                    }
                                } else {
                                    if(row.status=='Enabled'){
                                    out += '<a href="#" class="btn btn-success" title="Change Status">'+row.status+'</a>';
                                    }
                                    else{
                                    out += '<a href="#" class="btn btn-danger" title="Change Status">'+row.status+'</a>';
                                    }
                                }
                                return out;
                            }
                        },
                        {"taregts": 5, "render":function(data,type,row){
                            return moment(row.created_at).format("DD-MM-YYYY");
                        }
                        },

                        {"taregts": 6, "searchable": false, "orderable": false,
                            "render": function (data, type, row) {
                                var id = row.id;
                                var out="";
                                if (check_permission) {
                                    out = '<a href="<?php echo url('edit_project') ?>'+'/'+id+'" title="Edit Project" class="btn btn-primary btn-rounded"><i class="fa fa-edit"></i></a>';
                                }
                                /*out += '&nbsp;<a onclick="delete_confirm(this);" class="btn btn-danger btn-rounded" href="#" data-href=\'<?php echo url('delete_project'); ?>/' + id + '\'\n\
                                title="Delete"><i class="fa fa-trash"></i></a>';*/

                                out += '&nbsp;<button type="button" id="'+id+'" onclick="viewProjectManager(this.id)" class="btn btn-primary btn-rounded" title="View Project Manager/Employee"><i class="fa fa-eye"></i></button>';
                                return out;
                            }
                        },
                    ]

                });
            })
        function delete_confirm(e) {
            swal({
                title: "Are you sure you want to delete project ?",
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
                url : "{{url('get_project_managers')}}",
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
